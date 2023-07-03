<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;


class CancelacionCambioBeneficioService 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    private $emFinanciero;    
    private $emInfraestructura;
    private $emComunicacion;
    private $emGeneral;
    private $emSoporte;

    private $serviceInfoPersonaFormaContacto;
    private $serviceUtilidades;
    private $serviceSoporte;
    private $serviceUtil;
    private $serviceSecurity;
    private $serviceTecnico;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    { 
        $this->emComercial                     = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emFinanciero                    = $objContainer->get('doctrine')->getManager('telconet_financiero');
        $this->emInfraestructura               = $objContainer->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComunicacion                  = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral                       = $objContainer->get('doctrine.orm.telconet_general_entity_manager');        
        $this->emSoporte                       = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->serviceInfoPersonaFormaContacto = $objContainer->get('comercial.InfoPersonaFormaContacto'); 
        $this->serviceUtilidades               = $objContainer->get('administracion.Utilidades'); 
        $this->serviceSoporte                  = $objContainer->get('soporte.SoporteService');
        $this->serviceUtil                     = $objContainer->get('schema.Util');
        $this->serviceSecurity                 = $objContainer->get('security.context');
        $this->serviceTecnico                  = $objContainer->get('tecnico.InfoServicioTecnico');

        $this->serviceServicioHistorial        = $objContainer->get('comercial.InfoServicioHistorial');
        $this->serviceComercial                = $objContainer->get('comercial.Comercial');
        $this->serviceInfoPersona              = $objContainer->get('comercial.InfoPersona');
        
    }
        
    /** 
    * Documentación para el método 'cambioBeneficio'.
    * 
    * Función que realiza cambio de beneficio de "Cliente con Discapacidad" a "3era Edad Resolución 07-2021". 
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 24-08-2021 
    * 
    * @param  array $arrayParametros [
    *                                  "strStatus"    : Estado,
    *                                  "strMessage"   : Mensaje  
    *                                ]     
    */

    public function cambioBeneficio($arrayParametrosDatos)
    {
        $strUsrCreacion         = $arrayParametrosDatos['strUsrCreacion']; 
        $strCodEmpresa          = $arrayParametrosDatos['strCodEmpresa'];
        $strIpCreacion          = $arrayParametrosDatos['strIpCreacion'];
        $fltValorDescuento      = $arrayParametrosDatos['fltValorDescuento'];
        $intIdDetalleSolicitud  = $arrayParametrosDatos['intIdDetalleSolicitud'];
        $strFlujoAdultoMayor    = $arrayParametrosDatos['strFlujoAdultoMayor'];
        
        $arrayRespuesta         = array();
        $arrayParametros        = array(); 
        $strEstadoCancelado     = 'Cancelado';
        $strEstadoAprobado      = 'Aprobado';
        $strTipoSolicitud       = 'SOLICITUD DESCUENTO';

        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            $arrayMotivoAdultoMayor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                         'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                         '', '', '', '', '', $strCodEmpresa,'',$strFlujoAdultoMayor);
            
            //Se obtiene edad parametrizada para validar que cliente sea Adulto mayor > 65 
            $arrayEdadAdultoMayor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                       'COMERCIAL','','EDAD_ADULTO_MAYOR','',
                                                       '','','','', $strCodEmpresa);
                
            $intEdadParam  = (isset($arrayEdadAdultoMayor["valor1"])
                             && !empty($arrayEdadAdultoMayor["valor1"])) ? intval($arrayEdadAdultoMayor["valor1"]) : 65;
                
            //Se obtiene mensaje de validación si cliente no cumple ser Adulto Mayor
            $arrayValidacionAdultoMayor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                             'COMERCIAL','','','MENSAJE_VALIDACION_ADULTO_MAYOR','',
                                                             '','','',$strCodEmpresa);
                
            $strMensajeValidacionAdultoMayor = (isset($arrayValidacionAdultoMayor["valor2"])
                                               && !empty($arrayValidacionAdultoMayor["valor2"])) ? $arrayValidacionAdultoMayor["valor2"]
                                               : 'No es Adulto Mayor.';
                
            //Obtiene los Tipos de planes permitidos para otrogar beneficio Adulto mayor
            $arrayTipoPlan = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                'COMERCIAL','','TIPO_PLAN','',
                                                '','','','', $strCodEmpresa);
                
            //Se obtiene mensaje de validación si cliente no cumple con el Tipo de Plan permitido.
            $arrayValidaPlanPermitido  =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                             'COMERCIAL','','','MENSAJE_VALIDACION_PLANES_PERMITIDOS','',
                                                             '','','',$strCodEmpresa);
                                            
            $strMsjValidaPlanPermitido = (isset($arrayValidaPlanPermitido["valor2"])
                                         && !empty($arrayValidaPlanPermitido["valor2"])) ? $arrayValidaPlanPermitido["valor2"]
                                         : 'Plan no permitido para el Beneficio.';
            
            //Se obtiene mensaje de cambio de beneficio.
            $arrayMsjCambioBeneficio = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                          'COMERCIAL','','','MENSAJE_CAMBIO_BENEFICIO','',
                                                          '','','',$strCodEmpresa);
                                   
            $strMensajeCambioBeneficio = (isset($arrayMsjCambioBeneficio["valor2"])
                                         && !empty($arrayMsjCambioBeneficio["valor2"])) ? $arrayMsjCambioBeneficio["valor2"]
                                         : 'Cambio de Beneficio Discapacidad a 3era Edad.';

            $arrayAdmiMotivoSol = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')
                                         ->findBy(array("nombreMotivo" => $arrayMotivoAdultoMayor["valor1"]));                              

            if(empty($arrayAdmiMotivoSol) || !is_array($arrayAdmiMotivoSol))
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "No se encontro el motivo de la Solicitud: ".$arrayMotivoAdultoMayor["valor1"];
                return $arrayRespuesta; 
            } 
            
            $objDetalleSol = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            if(!is_object($objDetalleSol))
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "No se encontro la solicitud buscada: ". $intIdDetalleSolicitud;
                return $arrayRespuesta; 
            }
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objDetalleSol->getServicioId()->getId());
            if(!is_object($objServicio))
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "No se encontro el servicio.";
                return $arrayRespuesta; 
            }
            $objPunto  = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId()->getId());            
            $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
            
            //Validación Tipo Tributario
            $strMsjValidaTipoTributario = $this->serviceInfoPersona->getValidaTipoTributario(
                                                                array('intIdPersona'  => $objPersonaEmpresaRol->getPersonaId()->getId(),
                                                                       'strCodEmpresa' => $strCodEmpresa));
            if(!empty($strMsjValidaTipoTributario))
            {              
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = $strMsjValidaTipoTributario;
                return $arrayRespuesta; 
            }

            //Validacion Adulto Mayor
            $intEdadCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getEdadPersona(array('intIdPersona' => $objPersonaEmpresaRol->getPersonaId()->getId()));

            if($intEdadCliente < $intEdadParam)
            {              
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = $strMensajeValidacionAdultoMayor;
                return $arrayRespuesta; 
            }
                        
            //Valida que el beneficio solo se aplique a Planes Home segun parametro.
            $intExisteServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                             ->getServicioTipoPlan(array('intIdServicio' => $objServicio->getId(),
                                                                         'arrayTipoPlan' => $arrayTipoPlan));

            if($intExisteServicio === 0)
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = $strMsjValidaPlanPermitido; 
                return $arrayRespuesta; 
            }

            //SE CANCELA SOLICITUD ORIGEN POR CAMBIO DE BENEFICIO
            $objDetalleSol->setEstado($strEstadoCancelado);
            $this->emComercial->persist($objDetalleSol);
            $this->emComercial->flush();
            // Se guarda historial de la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setEstado($strEstadoCancelado);
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
            $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolHistorial->setObservacion($strMensajeCambioBeneficio);            
            $this->emComercial->persist($objDetalleSolHistorial);
            $this->emComercial->flush();
            // Se cancela caracteristicas de la Solicitud
            $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                   ->findBy(array("detalleSolicitudId" => $intIdDetalleSolicitud));
            foreach($objInfoDetalleSolCaract as $objSolCaract)
            {
                $objSolCaract->setEstado($strEstadoCancelado);
                $this->emComercial->persist($objSolCaract);
                $this->emComercial->flush();
            }            
            // Se cancela descuento en el servicio
            $fltValorDescAnterior = $objServicio->getValorDescuento();
            $objServicio->setValorDescuento(null);
            $objServicio->setDescuentoUnitario(null);
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();            
            
            //SE CREA SOLICITUD POR CAMBIO DE BENEFICIO            
            $strDescripcionCarac = 'DESCUENTO TOTALIZADO FACT';
            $objDetalleSolNueva  = new InfoDetalleSolicitud();
            $objDetalleSolNueva->setMotivoId($arrayAdmiMotivoSol[0]->getId());            
            $objDetalleSolNueva->setServicioId($objServicio);

            $arrayTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                              ->findBy(array("descripcionSolicitud" => $strTipoSolicitud));  
            if(empty($arrayTipoSolicitud) || !is_array($arrayTipoSolicitud))
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "No se encontro el Tipo de la Solicitud: ".$strTipoSolicitud;
                return $arrayRespuesta; 
            }            
            $objDetalleSolNueva->setTipoSolicitudId($arrayTipoSolicitud[0]);
            $objDetalleSolNueva->setPrecioDescuento($fltValorDescuento);
            $objDetalleSolNueva->setObservacion($strMensajeCambioBeneficio);
            $objDetalleSolNueva->setFeCreacion(new \DateTime('now'));
            $objDetalleSolNueva->setUsrCreacion($strUsrCreacion);
            $objDetalleSolNueva->setEstado($strEstadoAprobado);
            $this->emComercial->persist($objDetalleSolNueva);
            $this->emComercial->flush();

            //Busca la caracteristica asociada al descuento.
            $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCarac,
                                                                   'estado'                    => 'Activo'));

            if(!is_object($objAdmiCaracteristica))
            {                
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "No se pudo generar solicitud de descuento, no existe caracteristica asociada.";
                return $arrayRespuesta; 
            }
            //Se inserta Caracteristica en la solicitud
            $arrayRequestDetalleSolCaract = array();
            $arrayRequestDetalleSolCaract['entityAdmiCaracteristica'] = $objAdmiCaracteristica;
            $arrayRequestDetalleSolCaract['floatValor']               = round($fltValorDescuento, 2); 
            $arrayRequestDetalleSolCaract['entityDetalleSolicitud']   = $objDetalleSolNueva;
            $arrayRequestDetalleSolCaract['strEstado']                = 'Activo';
            $arrayRequestDetalleSolCaract['strUsrCreacion']           = $strUsrCreacion;
            
            $objDetalleSolCaractNueva = $this->serviceComercial->creaObjetoInfoDetalleSolCaract($arrayRequestDetalleSolCaract);                
            $this->emComercial->persist($objDetalleSolCaractNueva);
            $this->emComercial->flush();

            //Grabamos en la tabla de historial de la solicitud
            $objDetalleSolHistorialNueva = new InfoDetalleSolHist();
            $objDetalleSolHistorialNueva->setEstado($strEstadoAprobado);
            $objDetalleSolHistorialNueva->setDetalleSolicitudId($objDetalleSolNueva);
            $objDetalleSolHistorialNueva->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHistorialNueva->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorialNueva->setIpCreacion($strIpCreacion);
            $objDetalleSolHistorialNueva->setMotivoId($arrayAdmiMotivoSol[0]->getId());
            $objDetalleSolHistorialNueva->setObservacion($strMensajeCambioBeneficio);
            $this->emComercial->persist($objDetalleSolHistorialNueva);
            $this->emComercial->flush();                        
            // Se guarda descuento en el servicio
            $objServicio->setValorDescuento(round($fltValorDescuento, 2));
            $objServicio->setDescuentoUnitario(round($fltValorDescuento, 2));
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            // Se guarda Historial en el servicio.        
            $strObservacion = $strMensajeCambioBeneficio . '<br> Descuento anterior: ' . $objDetalleSol->getPorcentajeDescuento() . '%';
            $strObservacion .= '  ($'.$fltValorDescAnterior.')';
            $strObservacion .= '<br> Descuento nuevo: $' . $objDetalleSolNueva->getPrecioDescuento();
            $arrayParametros['objServicio']    = $objServicio;
            $arrayParametros['strIpClient']    = $strIpCreacion;
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['strObservacion'] = $strObservacion;
            $arrayParametros['strAccion']      = 'cambioBeneficio';           
            $objServicioHistorialNuevo         = $this->serviceServicioHistorial->crearHistorialServicio($arrayParametros);
            $this->emComercial->persist($objServicioHistorialNuevo);
            $this->emComercial->flush();

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            //setear respuesta
            $arrayRespuesta["status"]  = "OK";
            $arrayRespuesta["mensaje"] = "Se realizó cambio de beneficio con exito";
        }
        catch(\Exception $ex)
        {
            //setear respuesta
            $arrayRespuesta["status"]  = "ERROR";
            $arrayRespuesta["mensaje"] = "Ha ocurrido un problema. Por favor informe a Sistemas";
            //rollback
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'CancelacionCambioBeneficioService.cambioBeneficio',
                                            "Error: " .$ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayRespuesta;  
    }
    
}

