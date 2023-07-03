<?php

namespace telconet\administracionBundle\Service;

use telconet\administracionBundle\Service\UtilidadesService;

/**
 * Clase JefesTecnicoService
 *
 * Clase que maneja las funcionales necesarias para la administración de los jefes del área técnica
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 22-10-2015
 */    
class JefesTecnicoService 
{
    const ESTADO_ACTIVO = 'Activo';
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    /**
     * @var \telconet\administracionBundle\Service\UtilidadesService
     */
    private $serviceUtilidades;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;
    
    
    public function setDependencies( \Symfony\Component\DependencyInjection\ContainerInterface $container )
    {
        $this->emComercial       = $container->get('doctrine.orm.telconet_entity_manager');
        $this->serviceUtilidades = $container->get('administracion.Utilidades');
        $this->emInfraestructura = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
    }
    
        
    /**
     * getListadoEmpleados
     *
     * Método que retorna el listado del personal del área comercial junto con sus metas Activas y Brutas                                   
     *      
     * @param array $arrayParametros ['usuario', 'esJefe', 'departamento', 'empresa', 'exceptoUsr', 'noAsignados', 'asignadosA',
     *                                'jefeConCargo', 'inicio', 'limite', 'sinCuadrilla', 'intIdCuadrilla', 'criterios', 'nombreArea',
     *                                'rolesNoIncluidos', 'estadoActivo', 'caracteristicaCargo', 'metaBruta', 'metaActiva', 'detalleElementoTablet']
     * 
     * @return array $arrayResultados ['total', 'usuarios']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-10-2015
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 26-10-2015 - Se modifica para saber si un operativo esta 'Disponible' o 'Prestado' a un Coordinador o un Jefe
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 17-11-2015 - Se verifica si el empleado tiene cargo de 'Lider' o 'Jefe Cuadrilla', y si fuese el caso se verifica que tablet 
     *                           tiene asignada
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 14-12-2015 - Se modifica para que retorne la cantidad de empleados asignados al Jefe de acuerdo a la ciudad del usuario en 
     *                           sessión y los cargos del personal para el área Técnica, y para ello no se envía el parámetro 'departamento' 
     *                           cuando se requiere realizar la búsqueda del personal asignado.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 29-11-2016 Se agrega en los filtros aquellos cargos que funcionarán como cargos de Jefe para que puedan ser considerados 
     *                         al prestar empleados y cambiar responsable. Además se agrega en la validación del coordinador a aquellos cargos que
     *                         funcionan como jefe, para que se pueda contabilizar de manera correcta el número de cuadrillas prestadas y 
     *                         el número de cuadrillas que son préstamo 
     */
    public function getListadoEmpleados($arrayParametros)
    {
        $arrayUsuarios   = array();
        $arrayResultados = array();
        
        $intUsuarioSession      = $arrayParametros['usuario'];
        $strSinCuadrillas       = ( isset($arrayParametros['sinCuadrilla']) ) ? $arrayParametros['sinCuadrilla'] : 'N';
        $intContadorNoAgregados = 0;//Variable que contara los registros no agregados al resultado
        $arrayTmpPersonas       = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonalByCriterios($arrayParametros);

        $arrayRegistros = $arrayTmpPersonas['registros'];
        $intTmpTotal    = $arrayTmpPersonas['total'];
        
        if( $arrayRegistros )
        {
            foreach ($arrayRegistros as $arrayDatos)
            {
                $strFuncionaComoJefe     = 'N';
                $boolEsJefe              = $arrayDatos['esJefe'];
                $boolTabletAsignada      = 'N';
                $strTabletAsignada       = 'N/A';
                $intTabletAsignada       = 0;
                $intCuadrillasPrestadas  = 0;
                $intCuadrillasActivas    = 0;
                $intCuadrillasEsPrestamo = 0;

                $idPersonaEmpresaRolEmpleado  = $arrayDatos['idPersonaEmpresaRol'];
                $objPersonaEmpresaRolEmpleado = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findOneById($idPersonaEmpresaRolEmpleado);
                $strUsuario                   = ucwords(strtolower(trim($arrayDatos['nombres']))).' '.
                                                ucwords(strtolower(trim($arrayDatos['apellidos'])));//Obtener el nombre del empleado


                //Obtener el nombre del Jefe a quien reporta el Empleado
                $strIdReportaA = $arrayDatos['reportaPersonaEmpresaRolId'];
                $strReportaA   = $this->serviceUtilidades
                                      ->getNombreReportaA( array('reportaPersonaEmpresaRolId' => $strIdReportaA) );
                //Fin Obtener el nombre del Jefe a quien reporta el Empleado


                $arrayParametrosCaracteristicas = array(
                                                            'estado'              => $arrayParametros['estadoActivo'],
                                                            'area'                => $arrayParametros['nombreArea'],
                                                            'esJefe'              => $arrayDatos['esJefe'],
                                                            'idPersonaEmpresaRol' => $idPersonaEmpresaRolEmpleado
                                                       );
                
                
                /*
                 * Obtener el Coordinador del Préstamo
                 * Se obtiene el id y el nombre de la persona a la cual se prestó el empleado, y adicional el estado del empleado
                 * prestado es decir si el Coordinador en session es el que prestó el empleado le indicará que el empleado está 
                 * prestado, si no lo es indicará que es un préstamo de otro coordinador. 
                 */
                $strEstadoEmpleado = "Disponible";
                
                $arrayParametrosCaracteristicas['caracteristica']     = $arrayParametros['prestamoEmpleado'];
                $arrayParametrosCaracteristicas['tipoCaracteristica'] = 'Default';
                $arrayParametrosCaracteristicas['retornarObjeto']     = true;

                $strNombrePersonaPrestamo = '';
                $strFechaPrestamo         = '';
                $intIdPersonaPrestamo     = 0;
                $arrayTmpResultado        = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );
                $objCaracteristica        = ( isset($arrayTmpResultado['objeto']) ) ? $arrayTmpResultado['objeto'] : '';
                
                if( $objCaracteristica )
                {
                    $intIdPersonaPrestamo = $objCaracteristica->getValor() ? $objCaracteristica->getValor() : 0;
                    $strFechaPrestamo     = $objCaracteristica->getFeCreacion() ? $objCaracteristica->getFeCreacion()->format('d M Y') : '';
                    
                    if( $intIdPersonaPrestamo != $intUsuarioSession )
                    {
                        $strEstadoEmpleado = "Prestado";
                    }
                    else
                    {
                        $strEstadoEmpleado    = "Es prestamo";
                        $intIdPersonaPrestamo = $strIdReportaA;
                    }//( $intIdPersonaPrestador != $intUsuarioSession )
                }
                else
                {  
                    $arrayParametrosCaracteristicas['caracteristica'] = $arrayParametros['prestamoCuadrilla'];

                    $arrayTmpResultado    = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );
                    $objTmpCaracteristica = ( isset($arrayTmpResultado['objeto']) ) ? $arrayTmpResultado['objeto'] : '';

                    if( $objTmpCaracteristica )
                    {
                        $strTmpValor      = $objTmpCaracteristica->getValor() ? $objTmpCaracteristica->getValor() : '';
                        $strFechaPrestamo = $objTmpCaracteristica->getFeCreacion() ? $objTmpCaracteristica->getFeCreacion()->format('d M Y') : '';
                        
                        if( $strTmpValor == "SI" )
                        {
                            $objCuadrillaAsignada = $objPersonaEmpresaRolEmpleado->getCuadrillaId();
                            
                            if( $objCuadrillaAsignada )
                            {
                                $intIdCoordinadorPrincipal = $objCuadrillaAsignada->getCoordinadorPrincipalId();
                                $intIdCoordinadorPrestamo  = $objCuadrillaAsignada->getCoordinadorPrestadoId();
                                
                                if( $intIdCoordinadorPrincipal == $intUsuarioSession )
                                {
                                    $strEstadoEmpleado = "Se presto cuadrilla";
                                    
                                    $intIdPersonaPrestamo = $intIdCoordinadorPrestamo;
                                }
                                else
                                {
                                    $strEstadoEmpleado = "Prestamo de cuadrilla";
                                    
                                    $intIdPersonaPrestamo = $intIdCoordinadorPrincipal;
                                }//( $objCuadrillaAsignada->getCoordinadorPrincipalId() )
                            }//( $objCuadrillaAsignada )
                        }//( $strTmpValor == "SI" )
                    }//( $objTmpCaracteristica )
                }//( $intIdPersonaPrestador )
                
                
                $objIperPrestamo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaPrestamo);

                if( $objIperPrestamo )
                {
                    $strNombrePersonaPrestamo = $objIperPrestamo->getPersonaId() ? $objIperPrestamo->getPersonaId()->getInformacionPersona() : '';
                }
                //Fin Obtener el Coordinador del Préstamo

                
                if( $strEstadoEmpleado == "Prestado" && $strSinCuadrillas == "S" )
                {
                    $intContadorNoAgregados++;
                }
                else
                {
                    //Obtener el nombre del cargo del Empleado
                    $strCargo = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                  ->getRolEmpleadoEmpresa( array('usuario' => $idPersonaEmpresaRolEmpleado) );
                    //Fin Obtener el nombre del cargo del Empleado
                    
                    if(isset($arrayParametros['cargosFuncionanComoJefe']) && !empty($arrayParametros['cargosFuncionanComoJefe']))
                    {
                        if(in_array($strCargo, $arrayParametros['cargosFuncionanComoJefe']))
                        {
                            $strFuncionaComoJefe = 'S';
                        }
                    }
                    
                    
                    
                    //Obtener el nombre del cargo del Empleado guardado a través del Telcos 
                    $arrayParametrosCaracteristicas['caracteristica']     = $arrayParametros['caracteristicaCargo'];
                    $arrayParametrosCaracteristicas['tipoCaracteristica'] = 'Cargo';
                    $arrayParametrosCaracteristicas['retornarObjeto']     = false;

                    $arrayTmpResultado = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );

                    $strCargoTelcos    = ( isset($arrayTmpResultado['valor']) ) ? $arrayTmpResultado['valor'] : '';
                    //Fin Obtener el nombre del cargo del Empleado guardado a través del Telcos 



                    //Saber si tiene empleados asignados
                    $arrayTmpParametros = array(
                                                    'usuario'          => $arrayParametros['usuario'],
                                                    'empresa'          => $arrayParametros['empresa'],
                                                    'asignadosA'       => $idPersonaEmpresaRolEmpleado,
                                                    'nombreArea'       => $arrayParametros['nombreArea'],
                                                    'rolesNoIncluidos' => array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.'),
                                                );

                    $arrayEmpleadosAsignados = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->findPersonalByCriterios($arrayTmpParametros);

                    $intTotalEmpleadosAsignados = $arrayEmpleadosAsignados['total'];

                    if( $intTotalEmpleadosAsignados > 0 )
                    {
                        $intPos = strpos(strtolower($strCargo), 'coord');

                        if( $intPos === 0 || $strFuncionaComoJefe=='S')
                        {
                            $strEstadoActivo = $arrayParametros['estadoActivo'];

                            //Cuadrillas Activas
                            $arrayParametrosCuadrillas = array(
                                                                  'strUsrCreacion'          => trim($arrayDatos['login']), 
                                                                  'intCoordinadorPrincipal' => $idPersonaEmpresaRolEmpleado,
                                                                  'criterios'               => array( 'estado' => $strEstadoActivo )
                                                              );

                            $arrayCuadrillasActivas = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                        ->getCuadrillasByCriterios($arrayParametrosCuadrillas);

                            $intCuadrillasActivas = $arrayCuadrillasActivas['total'];
                            //Fin Cuadrillas Activas


                            //Cuadrillas Prestadas
                            $arrayParametrosCuadrillas['criterios']['estado'] = 'Prestado';

                            $arrayCuadrillasPrestadas = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                          ->getCuadrillasByCriterios($arrayParametrosCuadrillas);

                            $intCuadrillasPrestadas = $arrayCuadrillasPrestadas['total'];
                            //Fin Cuadrillas Prestadas


                            //Cuadrillas que son Prestamos de otros coordinadores o jefaturas
                            $arrayParametrosCuadrillas['criterios']['estado'] = 'Es_Prestamo';

                            $arrayCuadrillasEsPrestamo = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                           ->getCuadrillasByCriterios($arrayParametrosCuadrillas);

                            $intCuadrillasEsPrestamo = $arrayCuadrillasEsPrestamo['total'];
                            //Fin Cuadrillas que son Prestamos de otros coordinadores o jefaturas
                        }//( $intPos === 0 )
                    }//( $intTotalEmpleadosAsignados > 0 )
                    //Fin Saber si tiene empleados asignados


                    //Se obtiene el nombre y el id de la cuadrilla a la cual está asignado el empleado
                    $strCuadrillaAsignada   = "";
                    $intIdCuadrillaAsignada = 0;

                    if( $objPersonaEmpresaRolEmpleado )
                    {
                        $objCuadrillaAsignada = $objPersonaEmpresaRolEmpleado->getCuadrillaId();

                        if( $objCuadrillaAsignada )
                        {
                            $intIdCuadrillaAsignada = $objCuadrillaAsignada->getId();
                            $strCuadrillaAsignada   = $objCuadrillaAsignada->getNombreCuadrilla();
                        }
                    }
                    //Fin Se obtiene el nombre y el id de la cuadrilla a la cual está asignado el empleado
                    
                    
                    //Se verifica si tiene tablet asignada o no el 'Líder' o 'Jefe Cuadrilla'
                    if( $strCargo == 'Jefe Cuadrilla' || $strCargoTelcos == 'Lider' )
                    {
                        $strTabletAsignada  = 'Sin asignación';
                        $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                   ->findOneBy( 
                                                                array( 
                                                                        'estado'        => self::ESTADO_ACTIVO, 
                                                                        'detalleNombre' => $arrayParametros['detalleElementoTablet'], 
                                                                        'detalleValor'  => $idPersonaEmpresaRolEmpleado,
                                                                     ) 
                                                              );

                        if( $objDetalleElemento )
                        {
                            $boolTabletAsignada = 'S';
                            $intTabletAsignada  = $objDetalleElemento->getElementoId();
                            
                            $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intTabletAsignada); 
                            
                            if( $objElemento )
                            {
                                $strTabletAsignada  = $objElemento->getNombreElemento();
                            }//if( $objElemento )
                        }//( $objDetalleElemento )
                    }//( $strCargo == 'Jefe Cuadrilla' || $strCargoTelcos == 'Lider' )
                    //Fin Se verifica si tiene tablet asignada o no el 'Líder' o 'Jefe Cuadrilla'


                    $arrayUsuarios[] = array(
                                                'intIdPersonaEmpresaRol'     => $idPersonaEmpresaRolEmpleado,
                                                'strEmpleado'                => $strUsuario,
                                                'strReportaA'                => $strReportaA,
                                                'strIdReportaA'              => $strIdReportaA,
                                                'strCargo'                   => $strCargo,
                                                'intTotalEmpleadosAsignados' => $intTotalEmpleadosAsignados,
                                                'boolEsJefe'                 => $boolEsJefe,
                                                'strFuncionaComoJefe'        => $strFuncionaComoJefe,
                                                'intCuadrillasPrestadas'     => $intCuadrillasPrestadas,
                                                'intCuadrillasActivas'       => $intCuadrillasActivas,
                                                'intCuadrillasEsPrestamo'    => $intCuadrillasEsPrestamo,
                                                'strCargoTelcos'             => $strCargoTelcos,
                                                'strCuadrillaAsignada'       => $strCuadrillaAsignada,
                                                'intIdCuadrillaAsignada'     => $intIdCuadrillaAsignada,
                                                'intIdPersonaPrestamo'       => $intIdPersonaPrestamo,
                                                'strEstadoEmpleado'          => $strEstadoEmpleado,
                                                'strNombrePersonaPrestamo'   => $strNombrePersonaPrestamo,
                                                'strFechaPrestamo'           => $strFechaPrestamo,
                                                'boolTabletAsignada'         => $boolTabletAsignada,
                                                'intTabletAsignada'          => $intTabletAsignada,
                                                'strTabletAsignada'          => $strTabletAsignada
                                            );
                }//( $strEstadoEmpleado == "Prestado" && $strSinCuadrillas == "S" )
            }//foreach ($arrayRegistros as $arrayDatos)
        }//( $arrayRegistros )
        
        $arrayResultados['usuarios'] = $arrayUsuarios;
        $arrayResultados['total']    = intval($intTmpTotal) - $intContadorNoAgregados;
        
        return $arrayResultados;
    }
}