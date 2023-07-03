<?php

namespace telconet\comercialBundle\Service;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

/**
 * Clase InfoPersonaEmpresaRolService
 *
 * Clase que maneja las funcionales de la tabla 'INFO_PERSONA_EMPRESA_ROL' que son usadas por los demás módulos
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 12-10-2015
 */    
class InfoPersonaEmpresaRolService 
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emComercial = $container->get('doctrine.orm.telconet_entity_manager');
    }
    
        
    /**
     * getVentasVendedores
     *
     * Método que retorna las ventas de los vendedores de cada departamento dependiendo del usuario logueado                                    
     *      
     * @param array $arrayParametros ['arrayTipoVentas', 'arrayParametrosBusqueda', 'strFechaActivacionDesde', 'strFechaBusqueda', 
     *                                 'strFechaAprobacionDesde', 'strFechaAprobacionHasta', 'strFechaCreacionPuntoDesde', 
     *                                 'strFechaCreacionPuntoHasta', 'strFechaActivacionHasta', 'strCaracteristicaBruta', 'strCaracteristicaActiva']
     * 
     * @return array $arrayResultados [ 'total', 'encontrados' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-10-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 25-11-2015 - Se modifica la opcion para adaptarla al Reporte de Ventas del Vendedor, el cual no requiere que se envien el valor
     *                           de la meta, ni cumplimiento cuando se busca por tipo de venta 'rechazada' o 'cancel'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 06-07-2016 - Se agregan lo filtros de fePlanificacionDesde y fePlanificacionHasta los cuales retorna las ventas Brutas del 
     *                           vendedor consultado.
     */
    public function getVentasBrutasYActivasVendedores($arrayParametrosIniciales)
    {
        $arrayUsuarios   = array();
        $arrayResultados = array();
        $strEstadoActivo = 'Activo';
        
        $arrayParametrosVentas            = array( 'numeroVentas' => true );
        $arrayParametrosVentas['empresa'] = ( isset($arrayParametrosIniciales['empresa']) ? ( ($arrayParametrosIniciales['empresa']) 
                                              ? $arrayParametrosIniciales['empresa'] 
                                              : ( isset($arrayParametrosIniciales['arrayParametrosBusqueda']['empresa']) 
                                              ? ( ($arrayParametrosIniciales['arrayParametrosBusqueda']['empresa']) 
                                              ? $arrayParametrosIniciales['arrayParametrosBusqueda']['empresa'] : '' ) : '' ) ) : '' );
        
        $arrayTipoVentas  = $arrayParametrosIniciales['arrayTipoVentas'];
        $arrayTmpPersonas = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                 ->findPersonalByCriterios($arrayParametrosIniciales['arrayParametrosBusqueda']);

        $arrayRegistros = $arrayTmpPersonas['registros'];
        
        $arrayResultados['intTotal'] = $arrayTmpPersonas['total'];
        
        $boolFechaActivacion = $arrayParametrosIniciales['strFechaActivacionDesde'] ? true : false;

        if( $arrayRegistros )
        {
            foreach($arrayRegistros as $arrayDatos)
            {
                $arrayItem = array();
                
                $idPersonaEmpresaRolVendedor      = $arrayDatos['idPersonaEmpresaRol'];
                $objInfoPersonaEmpresaRolVendedor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->findOneById($idPersonaEmpresaRolVendedor);
                
                //Se obtiene el nombre del vendedor asignado
                $strNombreVendedor = ucwords(strtolower(trim($arrayDatos['nombres']))).' '.ucwords(strtolower(trim($arrayDatos['apellidos'])));
                //Fin Se obtiene el nombre del vendedor asignado
                
                
                //Se obtiene la cantidad de ventas realizadas por el vendedor
                $intTmpTotalVentas = 0;
                $strTmpLogin       = $arrayDatos['login'];
                
                $arrayTmpFechaInicio = array();

                if( isset($arrayParametrosIniciales['strFechaBusqueda']) )
                {
                    $strFechaBusqueda = $arrayParametrosIniciales['strFechaBusqueda'];
                    
                    if( $arrayParametrosIniciales['strFechaBusqueda'] )
                    {
                        $arrayTmpFechaInicio = explode("T", $strFechaBusqueda);
                    }
                    else
                    {
                        $dateFechaBusqueda   = new \DateTime('now');
                        $strFechaBusqueda    = $dateFechaBusqueda->format('Y-m-d H:i:s');
                        $arrayTmpFechaInicio = explode(" ", $strFechaBusqueda);
                    }

                    $arrayFechaInicio = explode("-", $arrayTmpFechaInicio[0]);
                    $timeFechaInicio  = strtotime("01-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0]);
                    $dateFechaInicio  = date("Y/m/d", $timeFechaInicio);

                    $dateFechaFinal  = strtotime(date("d-m-Y", $timeFechaInicio)." +1 month");
                    $dateFechaFinal  = date("Y/m/d", $dateFechaFinal);
                }
                else
                {
                    //Obtener las fechas de Aprobación y Activación en formato respectivo usado para la consulta en base de datos
                    $arrayTiposFechasConsulta = array('Aprobacion', 'Activacion', 'CreacionPunto', 'Planificacion');

                    foreach( $arrayTiposFechasConsulta as $strTipoFecha )
                    {
                        switch($strTipoFecha)
                        {
                            case 'Aprobacion':

                                $arrayTmpFechas = array(
                                                            'feInicio' => $arrayParametrosIniciales['strFechaAprobacionDesde'], 
                                                            'feFinal'  => $arrayParametrosIniciales['strFechaAprobacionHasta']
                                                       );

                                break;


                            case 'CreacionPunto':

                                $arrayTmpFechas = array(
                                                            'feInicio' => $arrayParametrosIniciales['strFechaCreacionPuntoDesde'], 
                                                            'feFinal'  => $arrayParametrosIniciales['strFechaCreacionPuntoHasta']
                                                       );

                                break;


                            case 'Activacion':

                                $arrayTmpFechas = array(
                                                            'feInicio' => $arrayParametrosIniciales['strFechaActivacionDesde'], 
                                                            'feFinal'  => $arrayParametrosIniciales['strFechaActivacionHasta']
                                                       );

                                break;


                            case 'Planificacion':

                                $arrayTmpFechas = array(
                                                            'feInicio' => $arrayParametrosIniciales['strFechaPlanificacionDesde'], 
                                                            'feFinal'  => $arrayParametrosIniciales['strFechaPlanificacionHasta']
                                                       );

                                break;
                        }

                        $arrayFechasResultados = $this->getFechasParaConsultaBaseDatos($arrayTmpFechas);

                        if( $arrayFechasResultados )
                        {
                            if( isset($arrayFechasResultados['feInicio']) )
                            {
                                $arrayParametrosVentas['fe'.$strTipoFecha.'Inicio'] = $arrayFechasResultados['feInicio'];
                            }

                            if( isset($arrayFechasResultados['feFinal']) )
                            {
                                $arrayParametrosVentas['fe'.$strTipoFecha.'Final'] = $arrayFechasResultados['feFinal'];
                            }
                        }
                    }//foreach( $arrayTiposFechasConsulta as $strTipoFecha )
                    //Fin Obtener las fechas de Aprobación y Activación en formato respectivo usado para la consulta en base de datos


                    $dateFechaInicio  = $arrayParametrosVentas['feCreacionPuntoInicio'] ? $arrayParametrosVentas['feCreacionPuntoInicio']
                                        : ( $arrayParametrosVentas['feActivacionInicio'] ? $arrayParametrosVentas['feActivacionInicio'] 
                                            : ( $arrayParametrosVentas['feAprobacionInicio'] ? $arrayParametrosVentas['feAprobacionInicio'] 
                                                : ( $arrayParametrosVentas['fePlanificacionInicio'] ? $arrayParametrosVentas['fePlanificacionInicio']
                                                    : '' ) ) );

                    $dateFechaFinal   = $arrayParametrosVentas['feCreacionPuntoFinal'] ? $arrayParametrosVentas['feCreacionPuntoFinal']
                                        : ( $arrayParametrosVentas['feActivacionFinal'] ? $arrayParametrosVentas['feActivacionFinal'] 
                                            : ( $arrayParametrosVentas['feAprobacionFinal'] ? $arrayParametrosVentas['feAprobacionFinal'] 
                                                : ( $arrayParametrosVentas['fePlanificacionFinal'] ? $arrayParametrosVentas['fePlanificacionFinal'] 
                                                    : '') ) );


                    /*
                     * Seteo en null dichas variables puesto que cuando elija por el usuario fechas de creación de punto será como 
                     * si estuviera consultando el informe consolidado del mes.
                     */
                    $arrayParametrosVentas['feCreacionPuntoInicio'] = null;
                    $arrayParametrosVentas['feCreacionPuntoFinal']  = null;
                    
                }//( isset($arrayParametrosIniciales['strFechaBusqueda']) )
                    
                foreach($arrayTipoVentas as $strTipoVentas)
                {
                    $arrayParametrosVentas['usuarioVendedor'] = $strTmpLogin;
                    $arrayParametrosVentas['tipoVenta']       = $strTipoVentas;
                    
                    if( $strTipoVentas == 'brutas' )
                    {
                        $arrayEstadosServiciosNoIncluidos = array( 'Inactivo', 'Rechazada', 'Cancel', 'Eliminado', 'In-Corte', 'Anulado', 'In-Temp' );

                        $arrayParametrosVentas['estadosServiciosNoIncluidos'] = $arrayEstadosServiciosNoIncluidos;
                        
                        if( !$boolFechaActivacion )
                        {
                            $arrayParametrosVentas['feActivacionInicio'] = '';
                            $arrayParametrosVentas['feActivacionFinal']  = '';
                        }
                        
                        $arrayParametrosVentas['estadosServiciosIncluidos'] = '';
                        $arrayParametrosVentas['feInicio']                  = $dateFechaInicio;
                        $arrayParametrosVentas['feFinal']                   = $dateFechaFinal;
                        
                        $strCaracteristicaBruta = $arrayParametrosIniciales['strCaracteristicaBruta'];
                        
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaBruta,
                                                                                   'estado'                    => $strEstadoActivo ) );
                    }//( $strTipoVentas == 'brutas' )
                    elseif( $strTipoVentas == 'activas' )
                    {
                        $strTmpEstadoServicio = 'Activo';

                        $arrayParametrosVentas['estadosServiciosIncluidos']   = $strTmpEstadoServicio;
                        
                        $arrayParametrosVentas['feActivacionInicio'] = $dateFechaInicio;
                        $arrayParametrosVentas['feActivacionFinal']  = $dateFechaFinal;
                        
                        $strCaracteristicaActiva = $arrayParametrosIniciales['strCaracteristicaActiva'];
                        
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaActiva,
                                                                                   'estado'                    => $strEstadoActivo ) );
                    }//( $strTipoVentas == 'activas' )
                    else
                    {
                        $arrayParametrosVentas['estadosServiciosNoIncluidos'] = '';
                        $arrayParametrosVentas['estadosServiciosIncluidos']   = '';
                        $arrayParametrosVentas['feInicio']                    = $dateFechaInicio;
                        $arrayParametrosVentas['feFinal']                     = $dateFechaFinal;
                        
                        if( !$boolFechaActivacion )
                        {
                            $arrayParametrosVentas['feActivacionInicio'] = '';
                            $arrayParametrosVentas['feActivacionFinal']  = '';
                        }//( !$boolFechaActivacion )
                        
                        if( $strTipoVentas == 'rechazada' )
                        {
                            $arrayEstadosServiciosTipoVenta = array('Rechazada', 'Anulado');
                        }
                        elseif( $strTipoVentas == 'cancel' )
                        {
                            $arrayEstadosServiciosTipoVenta = array('Cancelado', 'Cancel');
                        }
                        else
                        {
                            $arrayEstadosServiciosTipoVenta = array(ucwords(strtolower($strTipoVentas)));
                        }
                        
                        $arrayParametrosVentas['estadoServiciosTipoVenta'] = $arrayEstadosServiciosTipoVenta;
                    }
                    

                    //Se obtiene la cantidad de ventas realizadas por el vendedor
                    $arrayTmpVentas = array();

                    $arrayTmpVentas = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getVentasByCriterios($arrayParametrosVentas);

                    if( $arrayTmpVentas )
                    {
                        $intTmpTotalVentas = $arrayTmpVentas['total'] ? $arrayTmpVentas['total'] : 0;
                    }
                    //Fin Se obtiene la cantidad de ventas realizadas por el vendedor
                    
                    
                    //Se obtiene el monto total de las ventas obtenidas
                    $floatMontoTotalVentas    = 0.00;
                    $arrayTotalServicioVentas = $arrayTmpVentas['resultados'] ? $arrayTmpVentas['resultados'] : array();
                    
                    if( $arrayTotalServicioVentas )
                    {
                        foreach( $arrayTotalServicioVentas as $arrayServicio )
                        {
                            $objTmpServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($arrayServicio['id']);
                            
                            if( $objTmpServicio )
                            {
                                $floatMontoTotalVentas += floatval($objTmpServicio->getPrecioVenta() * $objTmpServicio->getCantidad());
                            }//( $objTmpServicio )
                        }//foreach( $arrayTotalServicioVentas as $arrayServicio )
                    }//( $arrayTotalServicioVentas )
                    
                    $floatMontoTotalVentas = number_format( $floatMontoTotalVentas, 2 );
                    //Fin Se obtiene el monto total de las ventas obtenidas
                    
                    
                    
                    $strMeta       = 'N/A';
                    $intTmpFaltan  = 0;
                    $intPorcentaje = 0;
                    
                    if( $strTipoVentas == 'brutas' || $strTipoVentas == 'activas' )
                    {
                        //Se obtiene el valor de la meta asignada actualmente
                        $arrayParametros = array(
                                                    'tipo'      => 'Meta',
                                                    'criterios' => array (
                                                                            'caracteristicaId'    => $objCaracteristica,
                                                                            'personaEmpresaRolId' => $objInfoPersonaEmpresaRolVendedor, 
                                                                            'estado'              => 'Activo'
                                                                         )     
                                                );

                        $strMeta = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->getCaracteristicaValor($arrayParametros);
                        //Fin Se obtiene el valor de la meta asignada actualmente


                        //Se obtiene el porcentaje de cumplimiento
                        if( $strTipoVentas == 'activas' )
                        {
                            $strCaracteristicaBruta = $arrayParametrosIniciales['strCaracteristicaBruta'];

                            $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaBruta,
                                                                                       'estado'                    => $strEstadoActivo ) );

                            $arrayParametros['criterios']['caracteristicaId'] = $objCaracteristica;

                            $strTmpMetaBruta = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                 ->getCaracteristicaValor($arrayParametros);

                            $strMeta = round( (intval($strTmpMetaBruta) * intval($strMeta))/100 );
                        }

                        if( $strMeta )
                        {
                            $intPorcentaje = number_format( ( (intval($intTmpTotalVentas) * 100 )/( intval($strMeta)) ), 2 );
                        }
                        //Fin Se obtiene el porcentaje de cumplimiento
                    
                    
                        //Obtener el número de ventas que le hacen falta a un vendedor
                        if( intval($intTmpTotalVentas) < intval($strMeta) )
                        {
                            $intTmpFaltan = intval($strMeta) - intval($intTmpTotalVentas);
                        }
                        //Fin Obtener el número de ventas que le hacen falta a un vendedor
                    }//( $strTipoVentas == 'brutas' || $strTipoVentas == 'activas' )
                    
                    $arrayItem['strMeta'.$strTipoVentas]         = $strMeta;
                    $arrayItem['intCumplimiento'.$strTipoVentas] = $intTmpTotalVentas; //intVendido
                    $arrayItem['intFalta'.$strTipoVentas]        = $intTmpFaltan;
                    $arrayItem['intPorcentaje'.$strTipoVentas]   = $intPorcentaje; //intCumplimiento
                    $arrayItem['floatMontoTotal'.$strTipoVentas] = $floatMontoTotalVentas;
                }//foreach($arrayTipoVentas as $strTipoVentas)
                
                //Para obtener el tiempo en días que tiene en la empresa el Vendedor
                $intTiempoVendedor = 0;
                
                if( $arrayDatos['feCreacion'] )
                {
                    $strFechaCreacionVendedor  = $arrayDatos['feCreacion'] ? $arrayDatos['feCreacion']->format('d/m/Y') : '';
                    $dateFechaCreacionVendedor = \DateTime::createFromFormat('d/m/Y H:i:s', $strFechaCreacionVendedor. ' 00:00:00');

                    $dateFechaActual   = new \Datetime('now');
                    $dateDiferencia    = $dateFechaCreacionVendedor->diff($dateFechaActual);
                    $intTiempoVendedor = $dateDiferencia->days;
                }
                //Fin Para obtener el tiempo en días que tiene en la empresa el Vendedor
                
                
                $arrayItem['intIdVendedor']     = $idPersonaEmpresaRolVendedor;
                $arrayItem['strNombreVendedor'] = $strNombreVendedor;
                $arrayItem['intTiempoVendedor'] = $intTiempoVendedor;
                
                $arrayUsuarios[] = $arrayItem;
                
            }//foreach($arrayRegistros as $arrayDatos)
        }//if( $arrayRegistros )
        
        $arrayResultados['encontrados'] = $arrayUsuarios;
        
        return $arrayResultados;
    }
    
    
    /**
     * Documentación para el método 'getFechasParaConsultaBaseDatos'.
     *
     * Retorna las fechas de Inicio y Fin en formato 'Y/m/d' usado para la consulta en la Base de Datos.
     *
     * @param array $arrayParametros Parámetros enviados por el usuario
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-09-2015
     */
    public function getFechasParaConsultaBaseDatos( $arrayParametros )
    {
        $arrayFechasResultado = array();
        
        if( isset($arrayParametros['feInicio']) )
        {
            if( $arrayParametros['feInicio'] )
            {
                $arrayFechaInicio = explode("-", $arrayParametros['feInicio']);
                $timeFechaInicio  = strtotime( $arrayFechaInicio[2]."-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0] );
                $dateFechaInicio  = date("Y/m/d", $timeFechaInicio);

                $arrayFechasResultado['feInicio'] = $dateFechaInicio;
            }
        }
        
        if( isset($arrayParametros['feFinal']) )
        {
            if( $arrayParametros['feFinal'] )
            {
                $arrayFechaFinal = explode("-", $arrayParametros['feFinal']);
                $timeFechaFinal  = strtotime( $arrayFechaFinal[2]."-".$arrayFechaFinal[1]."-".$arrayFechaFinal[0] );
                $timeFechaFinal  = strtotime(date("d-m-Y", $timeFechaFinal)." +1 day");
                $dateFechaFinal  = date("Y/m/d", $timeFechaFinal);
                
                $arrayFechasResultado['feFinal'] = $dateFechaFinal;
            }
        }
        
        return $arrayFechasResultado;
    }

    /**
     * Modifica el valor de la caracteristica del usuario enviado por parametro
     * @param $arrParametros
     * [
     *     intIdPersonaEmpresaRol => id de la persona empresa rol
     *     strValor               => valor de la caracteristica
     *     strUsrUltMod           => usuario de última modificación
     *     strFeUltMod            => fecha de última modificación
     *     strIpUltMod            => ip de última modificación
     *     strCaracteristica      => nombre de la característica
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-04-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function crearPersonaEmpresaRolCarac($arrayParametros)
    {
        $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
        $strValor               = $arrayParametros['strValor'];
        $strUsrUltMod           = $arrayParametros['strUsrUltMod'];
        $objFeUltMod            = $arrayParametros['dateFeUltMod'];
        $strIpUltMod            = $arrayParametros['strIpUltMod'];
        $strCaracteristica      = $arrayParametros['strCaracteristica'];
        $this->emComercial->beginTransaction();
        try
        {
            //consultamos característica de estado de conexión
            $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, 'Activo');
            if (is_object($objAdmiCaracteristica))
            {
                $objInfoPersonaEmpresaRolCarac = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                                   ->findOneBy(array('personaEmpresaRolId'=> $intIdPersonaEmpresaRol,
                                                                                     'caracteristicaId'   => $objAdmiCaracteristica->getId()
                                                                                    ));
                if (is_object($objInfoPersonaEmpresaRolCarac))
                {
                    $objInfoPersonaEmpresaRolCarac->setValor($strValor);
                    $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUsrUltMod);
                    $objInfoPersonaEmpresaRolCarac->setFeUltMod($objFeUltMod);
                    
                    $this->emComercial->persist($objInfoPersonaEmpresaRolCarac);
                    $this->emComercial->flush();
                    $strRespuesta = 'OK';
                }
                else
                {
                    $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                    $objInfoPersonaEmpresaRol      = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                       ->findOneById($intIdPersonaEmpresaRol);
                    if (is_object($objInfoPersonaEmpresaRol) && is_object($objAdmiCaracteristica))
                    {
                        $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristica);
                        $objInfoPersonaEmpresaRolCarac->setValor(substr($strValor, 0, 99));
                        $objInfoPersonaEmpresaRolCarac->setFeCreacion($objFeUltMod);
                        $objInfoPersonaEmpresaRolCarac->setUsrCreacion($strUsrUltMod);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpUltMod);
                        $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                        $this->emComercial->persist($objInfoPersonaEmpresaRolCarac);
                        $this->emComercial->flush();
                        $strRespuesta = 'OK';
                    }
                }
            }
            $this->emComercial->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($this->emComercial->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }

            $strRespuesta = " Error InfoPersonaEmpresaRolService.crearPersonaEmpresaRolCarac: " . $e . ", <br> Favor Notificar a Sistemas";
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoPersonaEmpresaRolService.crearPersonaEmpresaRolCarac',
                                             'Error InfoPersonaEmpresaRolService.crearPersonaEmpresaRolCarac:'.$e->getMessage(),
                                             $strUsrUltMod,
                                             $strIpUltMod);
            return $strRespuesta;
        }
        return $strRespuesta;
    }


    /**
     * getReporteTipoVentasVendedores
     *
     * Método que retorna las ventas de los vendedores de cada departamento dependiendo del usuario logueado                                    
     *      
     * @param array $arrayParametros [
     *                  'empresa',
     *                  'strTipoVenta',
     *                  'strFechaBusqueda',
     *                  'strCaracteristicaBruta',
     *                  'strCaracteristicaActiva'
     *                  ,arrayParametrosBusqueda
     *              ]
     * 
     * @return array $arrayResultados [ 'total', 'encontrados' ]
     * 
     * @author Jorge Veliz <jlveliz@telconet.ec>
     * @version 1.0 20-10-2021 - Trae los datos de los tipos de ventas de un vendedor
     * 
     */
    public function getReporteTipoVentasVendedores($arrayParametrosIniciales)
    {
        $arrayParametrosVentas            = array( 'numeroVentas' => true );
        $strEstadoActivo = 'Activo';
        $objRsm = new ResultSetMappingBuilder($this->emComercial);
        $objQuery = $this->emComercial->createNativeQuery(null, $objRsm);


        $arrayTmpPersonas = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
        ->findPersonalByCriterios($arrayParametrosIniciales['arrayParametrosBusqueda']);


        $arrayRegistros = $arrayTmpPersonas['registros'];
        
        $arrayResultados['intTotal'] = $arrayTmpPersonas['total'];
        
      

        if( $arrayRegistros ) 
        {
            $arrayDatos = $arrayRegistros[0];
            $strTipoVenta = $arrayParametrosIniciales['strTipoVentas'];
            $strEmpresa = $arrayParametrosIniciales['empresa'];
            $strLogin = $arrayDatos['login'];
            $intIdPersonaEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];
           

            //Se obtiene el nombre del vendedor asignado
            $strNombreVendedor = ucwords(strtolower(trim($arrayDatos['nombres']))).' '.ucwords(strtolower(trim($arrayDatos['apellidos'])));
            //Fin Se obtiene el nombre del vendedor asignado
            
            if( isset($arrayParametrosIniciales['strFechaBusqueda']) )
            {
                $strFechaBusqueda = $arrayParametrosIniciales['strFechaBusqueda'];
                
                if( $arrayParametrosIniciales['strFechaBusqueda'] )
                {
                    $arrayTmpFechaInicio = explode("T", $strFechaBusqueda);
                }
                else
                {
                    $objFechaBusqueda   = new \DateTime('now');
                    $strFechaBusqueda    = $objFechaBusqueda->format('Y-m');
                    $arrayTmpFechaInicio = explode(" ", $strFechaBusqueda);
                }

                $arrayFechaInicio = explode("-", $arrayTmpFechaInicio[0]);
                $strFechaInicio  = strtotime("01-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0]);
                $strFechaInicio  = date("Y/m/d", $strFechaInicio);

                $strFechaFinal  = strtotime(date("d-m-Y", $strFechaInicio)." +1 month");
                

                $strAnio          = $arrayFechaInicio[0];
                $strMes           = $arrayFechaInicio[1];
            }


            if( $strTipoVenta == 'brutas' )
            {
                $arrayParametrosVentas['estadosServiciosIncluidos'] = '';
                $arrayParametrosVentas['feInicio']                  = $strFechaInicio;
                $arrayParametrosVentas['feFinal']                   = $strFechaFinal;
                
                $strCaracteristicaBruta = $arrayParametrosIniciales['strCaracteristicaBruta'];
                
                $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaBruta,
                                                                            'estado'                    => $strEstadoActivo ) );
            }
            elseif( $strTipoVenta == 'activas' )
            {
                $strTmpEstadoServicio = 'Activo';

                $arrayParametrosVentas['estadosServiciosIncluidos']   = $strTmpEstadoServicio;
                
                
                $strCaracteristicaActiva = $arrayParametrosIniciales['strCaracteristicaActiva'];
                
                $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaActiva,
                                                                            'estado'                    => $strEstadoActivo ) );
            }

            $strSql = "select 
                    DB_COMERCIAL.CMKG_REPORTE_VENDEDORES_VENTAS.F_R_COUNT_TIPO_VENTAS(:tipoventa,:usuario,:empresa,:anio,:mes) as TOTAL, 
                    DB_COMERCIAL.CMKG_REPORTE_VENDEDORES_VENTAS.F_R_TOTAL_TIPO_VENTAS(:tipoventa,:usuario,:empresa,:anio,:mes) as RESULTADOS 
                from dual";
           
            $objRsm->addScalarResult('TOTAL', 'total', 'number');
            $objRsm->addScalarResult('RESULTADOS', 'resultados', 'float');
            

            $objQuery->setParameter('tipoventa', $strTipoVenta);
            $objQuery->setParameter('usuario', $strLogin);
            $objQuery->setParameter('empresa', $strEmpresa);
            $objQuery->setParameter('anio',$strAnio);
            $objQuery->setParameter('mes', $strMes);
            $objQuery->setSQL($strSql);

            $arrayTmpVentas = $objQuery->getScalarResult();
            $arrayTmpVentas = $arrayTmpVentas[0];

            if($arrayTmpVentas) 
            {
                $intTmpTotalVentas = $arrayTmpVentas['total'] ? $arrayTmpVentas['total'] : 0;
                $floatMontoTotalVentas = $arrayTmpVentas['resultados'] ? number_format($arrayTmpVentas['resultados'],2) : 0;
                $strMeta       = 'N/A';
                $intTmpFaltan  = 0;
                $intPorcentaje = 0;
                        
                if( $strTipoVenta == 'brutas' || $strTipoVenta == 'activas' )
                {
                    //Se obtiene el valor de la meta asignada actualmente
                    $arrayParametros = array(
                                                'tipo'      => 'Meta',
                                                'criterios' => array (
                                                                        'caracteristicaId'    => $objCaracteristica,
                                                                        'personaEmpresaRolId' => $intIdPersonaEmpresaRol, 
                                                                        'estado'              => 'Activo'
                                                                        )     
                                            );
    
                    $strMeta = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                    ->getCaracteristicaValor($arrayParametros);
                    //Fin Se obtiene el valor de la meta asignada actualmente
    
    
                    //Se obtiene el porcentaje de cumplimiento
                    if( $strTipoVenta == 'activas' )
                    {
                        $strCaracteristicaBruta = $arrayParametrosIniciales['strCaracteristicaBruta'];
    
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaBruta,
                                                                                    'estado'                    => $strEstadoActivo ) );
    
                        $arrayParametros['criterios']['caracteristicaId'] = $objCaracteristica;
    
                        $strTmpMetaBruta = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->getCaracteristicaValor($arrayParametros);
    
                        $strMeta = round( (intval($strTmpMetaBruta) * intval($strMeta))/100 );
                    }
    
                    if( $strMeta )
                    {
                        $intPorcentaje = number_format( ( (intval($intTmpTotalVentas) * 100 )/( intval($strMeta)) ), 2 );
                    }
                    //Fin Se obtiene el porcentaje de cumplimiento
                
                
                    //Obtener el número de ventas que le hacen falta a un vendedor
                    if( intval($intTmpTotalVentas) < intval($strMeta) )
                    {
                        $intTmpFaltan = intval($strMeta) - intval($intTmpTotalVentas);
                    }
                    //Fin Obtener el número de ventas que le hacen falta a un vendedor
                }//( $strTipoVentas == 'brutas' || $strTipoVentas == 'activas' )
                
                $arrayItem['strMeta'.$strTipoVenta]         = $strMeta;
                $arrayItem['intCumplimiento'.$strTipoVenta] = $intTmpTotalVentas; //intVendido
                $arrayItem['intFalta'.$strTipoVenta]        = $intTmpFaltan;
                $arrayItem['intPorcentaje'.$strTipoVenta]   = $intPorcentaje; //intCumplimiento
                $arrayItem['floatMontoTotal'.$strTipoVenta] = $floatMontoTotalVentas;

                //Para obtener el tiempo en días que tiene en la empresa el Vendedor
                $intTiempoVendedor = 0;
                
                if( $arrayDatos['feCreacion'] )
                {
                    $strFechaCreacionVendedor  = $arrayDatos['feCreacion'] ? $arrayDatos['feCreacion']->format('d/m/Y') : '';
                    $objFechaCreacionVendedor = \DateTime::createFromFormat('d/m/Y H:i:s', $strFechaCreacionVendedor. ' 00:00:00');

                    $objFechaActual   = new \Datetime('now');
                    $objDiferencia    = $objFechaCreacionVendedor->diff($objFechaActual);
                    $intTiempoVendedor = $objDiferencia->days;
                }
                //Fin Para obtener el tiempo en días que tiene en la empresa el Vendedor
                
                
                $arrayItem['intIdVendedor']     = $intIdPersonaEmpresaRol;
                $arrayItem['strNombreVendedor'] = $strNombreVendedor;
                $arrayItem['intTiempoVendedor'] = $intTiempoVendedor;
            }
            



            $arrayResultados['encontrados'] = $arrayItem;

            return $arrayResultados;

        }

       
    }





}
