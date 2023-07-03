<?php

namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\DependencyInjection\BaseWSController;

class ContratoDigitalService  extends BaseWSController
{
    
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emBiFinanciero;
    private $emComunicacion;    
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceCliente;
    private $serviceComercial;
    private $serviceInfoPunto;    
    private $serviceInfoServicio;
    private $servicePlanificacion;
    private $strFormContactoSitio;
    private $strTipoFactura;
    private $strTipoFacturaProporcional;
    private $serviceUtilidades;
    private $serviceLicenciasKaspersky;
    

    /**
     * setDependencies
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 07-09-2019
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        
        $this->emComercial                = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral                  = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero               = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero             = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->emComunicacion             = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->serviceUtil                = $objContainer->get('schema.Util');
        $this->serviceTecnico             = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceCliente             = $objContainer->get('comercial.Cliente');
        $this->serviceComercial           = $objContainer->get('comercial.Comercial');
        $this->serviceInfoPunto           = $objContainer->get('comercial.InfoPunto');
        $this->serviceInfoServicio        = $objContainer->get('comercial.InfoServicio');
        $this->servicePlanificacion       = $objContainer->get('planificacion.Planificar');
        $this->strFormContactoSitio       = $objContainer->getParameter('planificacion.mobile.codFormaContactoSitio');
        $this->strTipoFactura             = $objContainer->getParameter('financiero_tipo_factura');
        $this->strTipoFacturaProporcional = $objContainer->getParameter('financiero_tipo_factura_proporcional');
        $this->serviceUtilidades          = $objContainer->get('administracion.Utilidades');
        $this->serviceLicenciasKaspersky  = $objContainer->get('tecnico.LicenciasKaspersky');
    }


    /**
     * @author Edgar Pin Villavicencio  <epin@telconet.ec>
     * @version 1.0 07-10-2019  -Validaciones para los servicios de tm-comercial(Código duplicado de controller)
     *
     * @author Christian Jaramillo Espinoza  <cjaramilloe@telconet.ec>
     * @version 1.1 14-04-2020  Compatibilidad con productos adicionales parametrizados al mostrar descripción al usuario.
     */
    public function getValidacionesServicios($arrayParametros)
    {
        $strMensaje = $this->validaIpsMaximasPermitidas($arrayParametros);
        
        if (!empty($strMensaje)) 
        {
            $arrayRespuesta['response'][] = array("k" => "ips", "v" => $strMensaje);
        }
        if ($arrayParametros['intPlanId'] && $arrayParametros['intPlanId'] != "")
        {
            $strMensaje =  $this->validaFrecuenciaPlan($arrayParametros);
            
            if (!empty($strMensaje)) 
            {
                $arrayRespuesta['response'][] = array("k" => "frecuencia", "v" => $strMensaje);
            }
    
        }
        else
        {
            $arrayProdParams = array();
            
            if($arrayParametros['strEmpresaCod'] == '18')
            {
                $arrayProdParams = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                    ->get("PRODUCTOS_TM_COMERCIAL", "COMERCIAL", "", "", "", "", "", "", "", $arrayParametros['strEmpresaCod']);

                if(!empty($arrayProdParams))
                {
                    $arrayNuevoProdParams = array();

                    foreach ($arrayProdParams as $intKey => $arrayProdParam)
                    {
                        $arrayNuevoProdParams[intval($arrayProdParam['valor4'])] = $arrayProdParam;
                    }

                    $arrayProdParams = $arrayNuevoProdParams;
                }    
            }
            
            //I. PROTEGIDO MULTI PAID
            $entityProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($arrayParametros['intProductoId']);
            
            $strMensaje =  $this->validaFrecuenciaProducto($arrayParametros);
            
            if (!empty($strMensaje)) 
            {
                if(isset($arrayProdParams[$entityProducto->getId()]) && !empty($arrayProdParams[$entityProducto->getId()]['descripcion']))
                {
                    $strMensaje = str_replace($entityProducto->getDescripcionProducto(),
                        $arrayProdParams[$entityProducto->getId()]['descripcion'], $strMensaje);
                }
                
                $arrayRespuesta['response'][] = array("k" => "frecuencia", "v" => $strMensaje);
            }
            
            $strMensaje = $this->validaProductoNetlifecam($arrayParametros);

            if (!empty($strMensaje)) 
            {
                if(isset($arrayProdParams[$entityProducto->getId()]) && !empty($arrayProdParams[$entityProducto->getId()]['descripcion']))
                {
                    $strMensaje = str_replace($entityProducto->getDescripcionProducto(),
                        $arrayProdParams[$entityProducto->getId()]['descripcion'], $strMensaje);
                }
                
                $arrayRespuesta['response'][] = array("k" => "netlifecam", "v" => $strMensaje);
            }
            
            if ($entityProducto->getDescripcionProducto() == 'I. PROTEGIDO MULTI PAID')
            {
                $strMensaje = $this->validaIPMP($arrayParametros);

                if (!empty($strMensaje)) 
                {
                    if(isset($arrayProdParams[$entityProducto->getId()]) && !empty($arrayProdParams[$entityProducto->getId()]['descripcion']))
                    {
                        $strMensaje = str_replace($entityProducto->getDescripcionProducto(),
                            $arrayProdParams[$entityProducto->getId()]['descripcion'], $strMensaje);
                    }
                    
                    $arrayRespuesta['response'][] = array("k" => "ipmp", "v" => $strMensaje);
                }
    
            }

        }


        $arrayRespuesta['status']  = 200;
        $arrayRespuesta['message'] = 'OK';
        $arrayRespuesta['success'] = true;
        return $arrayRespuesta; 
    }

    /**
     * @author Edgar Pin Villavicencio  <epin@telconet.ec>
     * @version 1.0 07-10-2019  -Valida las ips maximas permitidas
     * 
     * @param $array [
     *                intPlanId                 => id del plan,
     *                intCantidadDetalle        => cantidad del detalle del servicio,
     *                intCantidadTotalIngresada => cantidad total de servicio ingresada,
     *                intProductoId             => id del producto,
     *                strTipo                   => portafolio,
     *                intPuntoId                => id del punto,
     *                strPrefijoEmpresa         => MD o TN,
     *                strEmpresaCod             =  10 o 18
     *               ]
     * 
     */
    public function validaIpsMaximasPermitidas($arrayParametros)
    {
        $intIdPlan                 = $arrayParametros['intPlanId'];   
        $intCantidadDetalle        = $arrayParametros['intCantidadDetalle'];   
        $intCantidadTotalIngresada = $arrayParametros['intCantidadTotalIngresada'];   
        $intProductoId             = $arrayParametros['intProductoId'];           
        $strTipo                   = $arrayParametros['strTipo'];           
        $intPuntoId                = $arrayParametros['intPuntoId']; 
        $strPrefijoEmpresa         = $arrayParametros['strPrefijoEmpresa'];
             
        $intNumIpsMaxPermitidas    = 0;
        $intNumIpsUtilizadas       = 0;
        $intCantidadIpsEnPlan      = 0;
        $strMensaje                = "";
        
        if($strPrefijoEmpresa === "MD")
        {
            $objProductoIP             = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->obtenerProductoIp($intIdPlan,$intProductoId, $strTipo);   
            $intNumIpsUtilizadas       = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->obtenerIpsUtilizadas($intPuntoId);
            $intNumIpsMaxPermitidas    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->obtenerIpsMaxPermitidas($intPuntoId, $intIdPlan, $strTipo);	                

            if($objProductoIP!=null && $objProductoIP->getId())
            {   
                if($strTipo=="portafolio")
                {
                    $intCantidadIpsEnPlan      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->obtenerCantidadIpsEnPlan($intIdPlan);
                    $intCantidadTotalIngresada = $intCantidadTotalIngresada + ($intCantidadDetalle*$intCantidadIpsEnPlan);
                }
                else
                {
                    $intCantidadTotalIngresada = $intCantidadTotalIngresada + $intCantidadDetalle;        
                }            
                if($intNumIpsMaxPermitidas>0)
                {                
                    if (($intNumIpsUtilizadas + $intCantidadTotalIngresada) > $intNumIpsMaxPermitidas)
                    {  
                        $strMensaje = 'No se permite el ingreso de [' .$intCantidadTotalIngresada.'] IPS adicional(es):  IPS Utilizadas['
                                      .$intNumIpsUtilizadas.'] IPS Max Permitidas[' .$intNumIpsMaxPermitidas.'] para el punto cliente';
                    }
                }else
                {
                      $strMensaje = 'No se permite el ingreso de [' .$intCantidadTotalIngresada.'] IPS adicional(es):  IPS Utilizadas['
                                    .$intNumIpsUtilizadas.'] IPS Max Permitidas[' .$intNumIpsMaxPermitidas.'] para el punto cliente';
                }
            }
        }
        return $strMensaje;
    }

    public function validaFrecuenciaPlan($arrayParametros)
    {

        $strMensaje                = "";
        $intIdPlan                 = $arrayParametros['intPlanId'];   

        
        if($intIdPlan<0 || $intIdPlan =="" || preg_match('/[^\d]/',$intIdPlan))
        { 
           $strMensaje = 'Debe escoger un Plan';
        }
        else
        {
            $intFrecuencia = $this->emComercial->getRepository('schemaBundle:InfoServicio')->obtenerFrecuencia($intIdPlan); 
        
            if(!$intFrecuencia || $intFrecuencia<0)
            {             
                $strMensaje = 'No se permite el ingreso del servicio por no poseer una Frecuencia ['
                              .$intFrecuencia.'] valida para su Facturacion';
            }
        }
        return $strMensaje;

    }

    public function validaFrecuenciaProducto($arrayParametros) 
    {
        $strUsrSesion       = $arrayParametros['user'];         
        $intIdProducto      = $arrayParametros['intProductoId'];           
        $intFrecuencia      = $arrayParametros['strFrecuencia'] == "Mensual" ? "1" : "0";  
        $strRespuesta       = "";
        try
        {  
            $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'FACTURACION_UNICA',
                                                    'strEstado'             => 'Activo');
            $strEsFacturacionUnica = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            
            $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'RENTA_MENSUAL',
                                                    'strEstado'             => 'Activo');
            $strEsRentaMensual = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            
            $objAdmiProducto       = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);

            if((!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "S" )
                && (empty($strEsRentaMensual) || (!empty($strEsRentaMensual) && $strEsRentaMensual == "N"))
              )
            {
                if($intFrecuencia != "0")
                {                                                                               
                    $strRespuesta = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto().' ya que es de '
                        . '[FACTURACION_UNICA] y la Frecuencia que debe escoger es [UNICA]';                                   
                }
            } 
            else
            {   if(!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "N"  && $intFrecuencia == "0")                                   
                {
                    $strRespuesta = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto(). ' ya que no es de '.
                                    '[FACTURACION_UNICA] no puede escoger Frecuencia [UNICA]';                    
                }
            }
        }
        catch (\Exception $e) 
        {                
            $this->serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxValidaFrecuenciaAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      '127.0.0.1'
                                     ); 
            $strRespuesta       = "Se presentaron errores en la validacion de FRECUENCIA y servicios de FACTURACION_UNICA , "
                                . "favor notificar a Sistemas.";
                            
        }
        
        return $strRespuesta;
    }
    

    public function validaProductoNetlifecam($arrayParametros)
    {      
        error_log("entro a validacion netlifecam");
        $intProductoId          = $arrayParametros['intProductoId'];           
        $intPuntoId             = $arrayParametros['intPuntoId'];           
        $boolProductoNetlifeCam = false;
        $strMensaje             = "";
        //Variable para validar si el producto pertenece a NetlifeCam
        $arrayParamProducNetCam   = $this->serviceTecnico->paramProductosNetlifeCam();
        $objProductoNetlifeCam = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);
        if($objProductoNetlifeCam && in_array($objProductoNetlifeCam->getNombreTecnico(),$arrayParamProducNetCam) )
        {
            $boolProductoNetlifeCam = true;   
        }
        if( $boolProductoNetlifeCam )
        {
            $objInternet    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->obtieneProductoInternetxPunto($intPuntoId); 
            if($objInternet!=null && $objInternet->getId())
            {  
                $strMensaje = "";
            }
        }
        return $strMensaje;
    }


    public function validaIPMP($arrayParametros)
    {
        $intIdPunto                     = $arrayParametros['intPuntoId'];
        $strCantidadDispositivosIPMP    = $arrayParametros['intCantidadDetalle'];
        $strCodEmpresa                  = $arrayParametros['strEmpresaCod'];
        $strUsrCreacion                 = $arrayParametros['user'];
        $strIpCreacion                  = '127.0.0.1';
        try
        {
            $arrayParametros            = array("intIdPunto"                    => $intIdPunto,
                                                "strCantidadDispositivosIPMP"   => $strCantidadDispositivosIPMP,
                                                "strCodEmpresa"                 => $strCodEmpresa);
            $arrayRespuestaCambioCorreo = $this->serviceLicenciasKaspersky->validaAgregarIPMP($arrayParametros);
            $strMensaje                 = $arrayRespuestaCambioCorreo['mensaje'];
        }
        catch(\Exception $e)
        {
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                      'InfoServicioController->validaAgregarIPMPAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return $strMensaje;
    }

}
