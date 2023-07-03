<?php

namespace telconet\administracionBundle\Service;

/**
 * Clase UtilidadesService
 *
 * Clase que maneja funcionales necesarias para el bundle de administración
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 22-10-2015
 */    
class UtilidadesService 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    /**
     * @var \telconet\schemaBundle\Service\UtilService
     */
    private $serviceUtil ;
    
    
    /**
     * Documentación para el método 'setDependencies'
     *
     * Método que inyecta las dependencias usadas en el service 'UtilidadesService'
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-03-2017 - Se agrega la variable 'serviceUtil'
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emComercial = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral   = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil = $container->get('schema.Util');
    }
    
        
    /**
     * Documentación para el método 'getDetallesParametrizables'
     *
     * Método que retorna un array con los detalles de los parámetros dependiendo de los criterios enviados por el usuario
     * 
     * @param array ['strCodEmpresa'     => 'Código de la empresa en sessión',
     *               'strValorRetornar'  => 'Valor a retornar como resultado',
     *               'strNombreProceso'  => 'Nombre del proceso del parámetro a consultar',
     *               'strNombreModulo'   => 'Módulo del parámetro a consultar',
     *               'strNombreCabecera' => 'Nombre de la cabecera del parámetro a consultar',
     *               'strDescripcion'    => 'Descripción del parámetro a consultar',
     *               'strValor1Detalle'  => 'Valor 1 del detalle a consultar',
     *               'strValor2Detalle'  => 'Valor 2 del detalle a consultar',
     *               'strValor3Detalle'  => 'Valor 3 del detalle a consultar',
     *               'strValor4Detalle'  => 'Valor 4 del detalle a consultar',
     *               'strValor5Detalle'  => 'Valor 5 del detalle a consultar',
     *               'strUsrCreacion'    => 'Usuario en sessión',
     *               'strIpCreacion'     => 'Ip del usuario en sessión' ]
     * 
     * @return array $arrayResultado['resultado' => 'Contiene los detalles parametrizados que se obtuvieron de la consulta',
     *                               'intTotal   => 'Retorna la cantidad de registros retornados']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-03-2017
     */
    public function getDetallesParametrizables($arrayParametros)
    {
        $arrayResultados   = array('resultado' => array(), 'intTotal' => 0);
        $intTotal          = 0;
        $strCodEmpresa     = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                             ? $arrayParametros['strCodEmpresa'] : '';
        $strValorRetornar  = ( isset($arrayParametros['strValorRetornar']) && !empty($arrayParametros['strValorRetornar']) )
                             ? $arrayParametros['strValorRetornar'] : '';
        $strNombreProceso  = ( isset($arrayParametros['strNombreProceso']) && !empty($arrayParametros['strNombreProceso']) )
                             ? $arrayParametros['strNombreProceso'] : '';
        $strNombreModulo   = ( isset($arrayParametros['strNombreModulo']) && !empty($arrayParametros['strNombreModulo']) )
                             ? $arrayParametros['strNombreModulo'] : '';
        $strNombreCabecera = ( isset($arrayParametros['strNombreCabecera']) && !empty($arrayParametros['strNombreCabecera']) )
                             ? $arrayParametros['strNombreCabecera'] : '';
        $strDescripcion    = ( isset($arrayParametros['strDescripcion']) && !empty($arrayParametros['strDescripcion']) )
                             ? $arrayParametros['strDescripcion'] : '';
        $strValor1Detalle  = ( isset($arrayParametros['strValor1Detalle']) && !empty($arrayParametros['strValor1Detalle']) )
                             ? $arrayParametros['strValor1Detalle'] : '';
        $strValor2Detalle  = ( isset($arrayParametros['strValor2Detalle']) && !empty($arrayParametros['strValor2Detalle']) )
                             ? $arrayParametros['strValor2Detalle'] : '';
        $strValor3Detalle  = ( isset($arrayParametros['strValor3Detalle']) && !empty($arrayParametros['strValor3Detalle']) )
                             ? $arrayParametros['strValor3Detalle'] : '';
        $strValor4Detalle  = ( isset($arrayParametros['strValor4Detalle']) && !empty($arrayParametros['strValor4Detalle']) )
                             ? $arrayParametros['strValor4Detalle'] : '';
        $strValor5Detalle  = ( isset($arrayParametros['strValor5Detalle']) && !empty($arrayParametros['strValor5Detalle']) )
                             ? $arrayParametros['strValor5Detalle'] : '';
        $strUsrCreacion    = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                             ? $arrayParametros['strUsrCreacion'] : '';
        $strIpCreacion     = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                             ? $arrayParametros['strIpCreacion'] : '';
        
        try
        {
           if( !empty($strCodEmpresa) && !empty($strValorRetornar) )
           {
               $arrayDetalles           = array();
               $arrayResultadosDetalles = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')->get( $strNombreCabecera, 
                                                                                                                   $strNombreModulo, 
                                                                                                                   $strNombreProceso, 
                                                                                                                   $strDescripcion, 
                                                                                                                   $strValor1Detalle, 
                                                                                                                   $strValor2Detalle,
                                                                                                                   $strValor3Detalle, 
                                                                                                                   $strValor4Detalle,
                                                                                                                   $strValor5Detalle,
                                                                                                                   $strCodEmpresa );
               if( !empty($arrayResultadosDetalles) )
                {
                    foreach($arrayResultadosDetalles as $arrayDetalle)
                    {
                        if( isset($arrayDetalle[$strValorRetornar]) && !empty($arrayDetalle[$strValorRetornar]) )
                        {
                            $arrayDetalles[] = $arrayDetalle[$strValorRetornar];
                            
                            $intTotal++;
                        }//( isset($arrayDepartamento['valor1']) && !empty($arrayDepartamento['valor1']) )
                    }//foreach($arrayResultadosDepartamentos as $arrayDepartamento)
                }//( $arrayResultadosDepartamentos )

                if( !empty($arrayDetalles) )
                {
                    $arrayResultados['resultado'] = $arrayDetalles;
                    $arrayResultados['intTotal']  = $intTotal;
                }//( !empty($arrayDepartamentos) )
           }
           else
           {
               throw new \Exception('Se deben enviar los parámetros requeridos para realizar la consulta de los detalles parametrizables que son '.
                                    'código de la empresa y el valor a retornar.');
           }//( !empty($strCodEmpresa) && !empty($strValorRetornar) )
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError( 'Telcos+', 
                                             'UtilidadesService.getDetallesParametrizables', 
                                             $e->getMessage(), 
                                             $strUsrCreacion, 
                                             $strIpCreacion );
        }
        
        return $arrayResultados;
    }
    
        
    /**
      * Documentación para el método 'getOpcionesTipoFormaPago'
      *
      * Método que retorna un array con las diferentes opciones que se usan en el formulario para la creación o edición de un tipo de forma de pago
      * 
      * @return array $arrayEntityFormaPago['arrayTipoFormaPago' => 'Contiene los tipos de formas de pago']
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 28-11-2016
      */
    public function getOpcionesTipoFormaPago()
    {
        $arrayEntityFormaPago    = array();
        $arrayParametrosDetalles = array('strNombreParametroCab' => 'TIPO_FORMA_PAGO', 'strEstado' => 'Activo');
        $arrayResultado          = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")->findParametrosDet($arrayParametrosDetalles);
        
        if( isset($arrayResultado['arrayResultado']) && !empty($arrayResultado['arrayResultado']) )
        {
            foreach($arrayResultado['arrayResultado'] as $arrayDetalle)
            {
                $arrayEntityFormaPago['arrayTipoFormaPago'][$arrayDetalle['strDescripcionDet']] = $arrayDetalle['strValor1'];
            }
        }
        
        return $arrayEntityFormaPago;
    }
    
        
    /**
     * Documentación para el método 'validarCaracteristicaProducto'
     *
     * Método que retorna un string indicando si existe la relación de producto con una característica
     * 
     * @param array   $arrayParametros [ 'intIdProducto'         => 'Id del producto',
                                         'strDescCaracteristica' => 'Descripcion de la caracteristica',
                                         'strEstado'             => 'Estado del producto a consultar']
     * 
     * @return String $strExisteCaracteristica
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-02-2017
     */
    public function validarCaracteristicaProducto($arrayParametros)
    {
        $strExisteCaracteristica = "N";
        $objCaracteristica       = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findByDescripcionProductoAndCaracteristica($arrayParametros);

        if( is_object($objCaracteristica) )
        {
            $intIdCaracteristica = $objCaracteristica->getId();

            if( !empty($intIdCaracteristica) && $intIdCaracteristica > 0 )
            {
                $strExisteCaracteristica = "S";
            }//( !empty($intIdCaracteristica) && $intIdCaracteristica > 0 )
        }//( is_object($objCaracteristica) )
        
        return $strExisteCaracteristica;
    }
    
        
    /**
      * getNombreReportaA
      *
      * Método que retorna el nombre de la persona a la cual ha sido asignado el empleado                           
      *      
      * @param array $arrayParametros
      * 
      * @return string $strReportaA
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 22-10-2015
      */
    public function getNombreReportaA($arrayParametros)
    {
        $strReportaA   = '';
        $strIdReportaA = $arrayParametros['reportaPersonaEmpresaRolId'] ? $arrayParametros['reportaPersonaEmpresaRolId'] : '';

        if( $strIdReportaA )
        {
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($strIdReportaA);

            if( $objPersona )
            {
                $strNombresDeReportaA   = ucwords(strtolower(trim($objPersona->getPersonaId()->getNombres())));
                $strApellidosDeReportaA = ucwords(strtolower(trim($objPersona->getPersonaId()->getApellidos())));

                $strReportaA = $strNombresDeReportaA.' '.$strApellidosDeReportaA;
            }
        }
        
        return $strReportaA;
    }
    
    
    /**
     * getValorCaracteristica
     *
     * Método que retorna el valor de la caracteristica a consultar la cual puede ser: El cargo del empleado, la meta Bruta
     * o la meta Activa asignada               
     *      
     * @param array $arrayParametros [ 'caracteristica', 'estado', 'area', 'tipoCaracteristica', 'esJefe', 'idPersonaEmpresaRol' ]
     * 
     * @return array $arrayResultados [ 'valor', 'objeto' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-10-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-10-2015 - Se modifica para retornar ahora un array con la información requerida.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     */
    public function getValorCaracteristica($arrayParametros)
    {
        $arrayResultados          = array();
        $arrayCaracteristicaValor = array();
        $objTmpCaracteristica     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy( array( 'descripcionCaracteristica' => $arrayParametros['caracteristica'],
                                                                          'estado'                    => $arrayParametros['estado'] ) );
        $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->findOneById($arrayParametros['idPersonaEmpresaRol']);

        $arrayParametrosCaracteristicas = array(
                                                    'moduloActivo' => $arrayParametros['area'],
                                                    'tipo'         => $arrayParametros['tipoCaracteristica'],
                                                    'esJefe'       => $arrayParametros['esJefe'],
                                                    'criterios'    => array (
                                                                                'caracteristicaId'    => $objTmpCaracteristica,
                                                                                'personaEmpresaRolId' => $objInfoPersonaEmpresaRol, 
                                                                                'estado'              => $arrayParametros['estado']
                                                                            )     
                                                );

        if( isset($arrayParametros['retornarObjeto']) )
        {
            if( $arrayParametros['retornarObjeto'] )
            {
                $arrayResultados['objeto'] = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                  ->findOneBy($arrayParametrosCaracteristicas['criterios']);
            }
            else
            {
                $arrayCaracteristicaValor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->getCaracteristicaValor($arrayParametrosCaracteristicas);
            } 
        }
        else
        {
            $arrayCaracteristicaValor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                             ->getCaracteristicaValor($arrayParametrosCaracteristicas);
        }
        
        
        if( isset($arrayCaracteristicaValor['strValor']) && !empty($arrayCaracteristicaValor['strValor']) )
        {
            $arrayResultados['valor'] = $arrayCaracteristicaValor['strValor'];
        }//( isset($arrayCaracteristicaValor['strValor']) && !empty($arrayCaracteristicaValor['strValor']) )
        
        
        if( isset($arrayCaracteristicaValor['intIdValor']) && !empty($arrayCaracteristicaValor['intIdValor']) )
        {
            $arrayResultados['intIdValor'] = $arrayCaracteristicaValor['intIdValor'];
        }//( isset($arrayCaracteristicaValor['intIdValor']) && !empty($arrayCaracteristicaValor['intIdValor']) )
        
        return $arrayResultados;
    }
    
    
    /**
     * getInformacionPorCargo
     *
     * Método que retorna el cargo y el objeto del coordinador principal de una cuadrilla               
     *      
     * @param array $arrayParametros  ['cargoBusqueda', 'idPersonaEmpresaRol']
     * 
     * @return array $arrayResultados ['cargo', 'objCoordinadorPrincipal']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-10-2015
     */
    public function getInformacionPorCargo($arrayParametros)
    {
        $arrayResultados = array();
        
        $intIdPersonEmpresaRol = $arrayParametros['idPersonaEmpresaRol'];
        
        $strCargo             = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                     ->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol ) );
        $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonEmpresaRol);
            
        $intPos = strpos(strtolower($strCargo), $arrayParametros['cargoBusqueda']);
                
        if($intPos === 0)
        {
            $intIdReportaA           = $objPersonaEmpresaRol ? $objPersonaEmpresaRol->getReportaPersonaEmpresaRolId() : 0;
            $objCoordinadorPrincipal = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdReportaA);
            
            if($objCoordinadorPrincipal)
            {
                $strCargo = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                 ->getRolEmpleadoEmpresa( array('usuario' => $objCoordinadorPrincipal->getId()) );
                
                $intIdPersonEmpresaRol = $objCoordinadorPrincipal->getId();
            }
            
            $arrayResultados['objCoordinadorPrincipal'] = $objCoordinadorPrincipal;
        }
        else
        {
            $arrayResultados['objCoordinadorPrincipal'] = $objPersonaEmpresaRol;
        }
        
        $arrayResultados['cargo'] = $strCargo;
        
        return $arrayResultados;
    }
}
