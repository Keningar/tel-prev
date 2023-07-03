<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Form\InfoDocumentoType;
use telconet\schemaBundle\Form\InfoContratoType;
use telconet\schemaBundle\Form\InfoContratoDatoAdicionalType;
use telconet\schemaBundle\Form\InfoContratoFormaPagoType;
use telconet\schemaBundle\Form\InfoContratoFormaPagoEditType;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Promoción controller.
 *
 */
class CancelacionCambioBeneficioController extends Controller implements TokenAuthenticatedController
{
    
    /**
     * @Secure(roles="ROLE_457-1")
     * 
     * indexAction
     * 
     * Función que carga pantalla de Cancelación y Cambio de Beneficio.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-03-2021     
     * 
     * @return render - Página de Consulta de solicitudes por beneficio Adulto Mayor y Discapacidad.
     */
    public function indexAction()
    {  
        $objRequest                  = $this->getRequest();
        $objSession                  = $objRequest->getSession();
        $strCodEmpresa               = $objSession->get('idEmpresa');
        $emComercial                 = $this->get('doctrine')->getManager('telconet');
        $emGeneral                   = $this->getDoctrine()->getManager('telconet_general');	
        $arrayParametros             = array();
     
        return $this->render('comercialBundle:cancelacionCambioBeneficio:index.html.twig', $arrayParametros);
    }
    
    /**    
     * gridSolicitudesAction
     * 
     * Función que obtiene listado de solicitudes.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-03-2021
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 19-08-2021 - Se agrega código para obtener el tipo de flujo parametrizado mediante el motivo de adulto mayor.
     *                         - Se valida por el tipo de categoría plan cuando el proceso es por el flujo adulto mayor del motivo 
     *                           3era Edad Resolución 07-2021, dónde si el plan es básico debe mostrar valor descuento por
     *                           porcentaje, de lo contrario si es comercial debe mostrar descuento por valor. Para los demás 
     *                           motivos se mantiene su flujo correspondiente de valor descuento.
     * 
     * @return $objResponse - Listado de solicitudes por Beneficio 3era Edad / Adulto Mayor - Cliente con Discapacidad.
     */
    public function gridSolicitudesAction()
    {        
        $objRequest        = $this->getRequest();       
        $strIdentificacion = $objRequest->get("strIdentificacion");
        $strLogin          = $objRequest->get("strLogin");
        $strCodEmpresa     = $objRequest->getSession()->get('idEmpresa');        
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');		
        
        $arrayParametros                       = array();        
        $arrayParametros['strCodEmpresa']      = $strCodEmpresa;        
        $arrayParametros['strIdentificacion']  = $strIdentificacion;
        $arrayParametros['strLogin']           = $strLogin;

        $arrayResultado   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getSolictudesPorCriterios($arrayParametros);
        $arrayRegistros   = $arrayResultado['objRegistros'];
        $intTotal         = $arrayResultado['intCantidad'];
        $arraySolicitudes = array();
        
        //Obtengo los parámetros a validar Adulto Mayor y Discapacidad
        $arrayMotivoAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                     '', '', '', '', '', $strCodEmpresa);
        
        $arrayMotivoDiscapacidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                      'COMERCIAL','','MOTIVO_DESC_DISCAPACIDAD','',
                                                      '','','','', $strCodEmpresa);
        
        $arrayParamCategPlanBasico = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                    'COMERCIAL','','CATEGORIA_PLAN_ADULTO_MAYOR',
                                                    '','','PLAN_BASICO','','',$strCodEmpresa);

        $arrayAdultoMayor = array();
        foreach($arrayMotivoAdultoMayor as $arrayMotivo)
        {
            $arrayAdultoMayor[] = $arrayMotivo["valor1"] ;
        } 
        
        foreach($arrayRegistros as $arrayDatos)
        {
            $intIdDetalleSolicitud = $arrayDatos['idDetalleSolicitud'];
            
            $strFechaNacimiento = !empty($arrayDatos['fechaNacimiento']) ? strval(date_format($arrayDatos['fechaNacimiento'], "Y-m-d")) : "";
            
            if (in_array($arrayDatos['nombreMotivo'], $arrayAdultoMayor))  
            {
                $arrayFlujoProceso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR','COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                         $arrayDatos['nombreMotivo'], '', '', '', '', $strCodEmpresa);
                
                if($arrayFlujoProceso['valor6'] == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR') 
                {
                    $strDescuento = $arrayDatos['precioDescuento'].' $';
                    $strTotal     = round($arrayDatos['precioVenta'] - $arrayDatos['precioDescuento'],2);  
                }
                elseif($arrayFlujoProceso['valor6'] == 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                {
                    if($arrayDatos['categoriaPlan'] == $arrayParamCategPlanBasico['valor1'])
                    {
                        $strDescuento  = $arrayDatos['porcentajeDescuento'].' %';
                        $strTotal      = round($arrayDatos['precioVenta'] - ($arrayDatos['precioVenta']* $arrayDatos['porcentajeDescuento']/100),2);
                    }
                    else
                    {  
                        $strDescuento  = $arrayDatos['precioDescuento'].' $';
                        $strTotal      = round($arrayDatos['precioVenta'] - $arrayDatos['precioDescuento'],2); 
                    }    
                } 
                $strCambioBenneficio   = '';
            }
            elseif($arrayDatos['nombreMotivo'] == $arrayMotivoDiscapacidad["valor1"])
            {
                $strDescuento          = $arrayDatos['porcentajeDescuento'].' %';
                $strTotal              = round($arrayDatos['precioVenta'] - ($arrayDatos['precioVenta']* $arrayDatos['porcentajeDescuento']/100),2); 
                $strCambioBenneficio   = 'SI';
            }
            $arraySolicitudes[] = array(
                                        'intIdDetalleSolicitud' => $arrayDatos['idDetalleSolicitud'],
                                        'strIdentificacion'     => $arrayDatos['identificacion'],
                                        'strCliente'            => $arrayDatos['nombreCliente'],
                                        'strFechaNacimiento'    => $strFechaNacimiento,
                                        'strEdad'               => $arrayDatos['edad'],
                                        'strDireccionLogin'     => $arrayDatos['direccionPto'],
                                        'strLogin'              => $arrayDatos['login'],
                                        'strPlan'               => $arrayDatos['nombrePlan'],
                                        'strBeneficio'          => $arrayDatos['nombreMotivo'],
                                        'strPrecioVenta'        => $arrayDatos['precioVenta'],
                                        'strDescuento'          => $strDescuento,
                                        'strTotalPagar'         => $strTotal,              
                                        'strAcciones'           => 
                                        array('linkActFeNacimiento'      => $this->generateUrl('cancelacionCambioBeneficio_actFechaNacimiento', 
                                                                            array('intIdDetalleSolicitud' => $intIdDetalleSolicitud,
                                                                                  'strOpcion'             => 'Individual')),
                                              'strCambioBenneficio'      => $strCambioBenneficio,
                                              'intIdServicio'            => $arrayDatos['idServicio'],
                                              'intIdDetalleSolicitud'    => $arrayDatos['idDetalleSolicitud'])
                                       );
                       
        }

        if(empty($arraySolicitudes))
        {             
            $arraySolicitudes[] = array('intIdDetalleSolicitud'  => "",
                                        'strIdentificacion'      => "",
                                        'strCliente'             => "",
                                        'strFechaNacimiento'     => "",
                                        'strEdad'                => "",
                                        'strDireccionLogin'      => "",
                                        'strLogin'               => "",
                                        'strPlan'                => "",
                                        'strBeneficio'           => "",
                                        'strPrecioVenta'         => "",
                                        'strDescuento'           => "",
                                        'strTotalPagar'          => "",              
                                        'strAcciones'            => array('linkActFeNacimiento'       => '',
                                                                           'intIdServicio'            => '',
                                                                           'intIdDetalleSolicitud'    => '')
                                       );
        }

        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arraySolicitudes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
     * getMotivos, obtiene la información de las motivos de Cancelación de Beneficios 
     * (Beneficio 3era Edad / Adulto Mayor - Cliente con Discapacidad.).
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-03-2021
     *  
     *                    
     * @return Response lista de Motivos
     */
    public function getMotivosAction()
    {               
        $emComercial                           = $this->get('doctrine')->getManager('telconet');
        $arrayParametros                       = array();        
        $arrayParametros['arrayEstadoMotivos'] = array('Activo');  
        $arrayParametros['strNombreModulo']    = 'cancelacionCambioBeneficio';          
        
        // Se obtiene los motivos parametrizados en base al modulo y estados.
        $arrayMotivos     = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')->getMotivos($arrayParametros);
        $objResponse      = new Response(json_encode(array('motivos'=> $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;	
    }          
    /**    
     * cancelacionBeneficioAction
     * 
     * Función que realiza Cancelación del Beneficio 3era Edad / Adulto Mayor - Cliente con Discapacidad.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-03-2021     
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 19-08-2021 - Se modifica código para obtener los motivos de beneficios parametrizados y que se evalúen 
     *                           cuando se ejecute una cancelación de beneficio.
     * 
     * @return $objResponse
     */
    public function cancelacionBeneficioAction()
    {
        $objRequest           = $this->getRequest();
        $objSesion            = $objRequest->getSession();
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $intIdMotivo          = $objRequest->get('intIdMotivo');               
        $arrayIdsSolicitudes  = $objRequest->get('arrayIdsSolicitudes');         
        $strUsrCreacion       = $objSesion->get('user');
        $strCodEmpresa        = $objSesion->get('idEmpresa');        
        $strIpCreacion        = $objRequest->getClientIp();   
        $objResponse          = new Response();
        $arrayParametros      = array(); 
        $strEstadoCancelado   = 'Cancelado';
        $objResponse->headers->set('Content-Type', 'text/plain'); 
        $serviceUtil          = $this->get('schema.Util');
        $emComercial->getConnection()->beginTransaction();
        try
        {   
            $arrayMotivoAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PARAM_FLUJO_ADULTO_MAYOR',
                                                         'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                         '', '', '', '', '', $strCodEmpresa);
        
            $arrayMotivoDiscapacidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                          'COMERCIAL','','MOTIVO_DESC_DISCAPACIDAD','',
                                                          '','','','', $strCodEmpresa);
        
            $serviceServicioHistorial = $this->get('comercial.InfoServicioHistorial');
            
            $objAdmiMotivoCancel      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            if(!is_object($objAdmiMotivoCancel))
            {
                throw $this->createNotFoundException('No se encontro el motivo de la Cancelación de Beneficio: '.$intIdMotivo);
            }             
            //Se obtiene mensaje de cancelación de beneficio.
            $arrayMsjCancelacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                      'COMERCIAL','','','MENSAJE_CANCELACION_BENEFICIO','',
                                                      '','','',$strCodEmpresa);
                                   
            $strMensajeCancelacion = (isset($arrayMsjCancelacion["valor2"])
                                       && !empty($arrayMsjCancelacion["valor2"])) ? $arrayMsjCancelacion["valor2"]
                                       : 'Se canceló el beneficio de [strTipoBeneficio] por motivo: strMotivoCancel.';
                        
            $strMensajeCancelacion = str_replace("strMotivoCancel", $objAdmiMotivoCancel->getNombreMotivo(), $strMensajeCancelacion);
            
            //Se almacena los motivos paramterizados en un array para vaidación.
            $arrayAdultoMayor = array();
            foreach($arrayMotivoAdultoMayor as $arrayMotivo)
            {
                $arrayAdultoMayor[] = $arrayMotivo["valor1"] ;
            } 
            
            foreach ($arrayIdsSolicitudes as $intIdDetalleSolicitud)
            {       
                // Se cancela Solicitud
                $objDetalleSol  = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
                if(!is_object($objDetalleSol))
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada: ' . $intIdDetalleSolicitud);
                } 
                $objDetalleSol->setEstado($strEstadoCancelado);
                $emComercial->persist($objDetalleSol);
                $emComercial->flush();
                
                // Obtengo motivo de la solicitud
                $objAdmiMotivoSol = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                if(!is_object($objAdmiMotivoSol))
                {
                    throw $this->createNotFoundException('No se encontro el motivo de la Solicitud.');
                }                               
                $strObservCancelacion = str_replace("strTipoBeneficio", $objAdmiMotivoSol->getNombreMotivo(), $strMensajeCancelacion);
                
                // Se guarda historial de la solicitud
                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setEstado($strEstadoCancelado);
                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolHistorial->setObservacion($strObservCancelacion);
                $objDetalleSolHistorial->setMotivoId($objAdmiMotivoCancel->getId());
                $emComercial->persist($objDetalleSolHistorial);
                $emComercial->flush();
                
                // Se cancela caracteristicas de la Solicitud
                $objInfoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                       ->findBy(array("detalleSolicitudId" => $intIdDetalleSolicitud));

                foreach ($objInfoDetalleSolCaract as $objSolCaract)            
                {
                    $objSolCaract->setEstado($strEstadoCancelado);
                    $emComercial->persist($objSolCaract);
                    $emComercial->flush();
                }                    
                $objServicio = $objDetalleSol->getServicioId();
                if(!is_object($objServicio))
                {
                    throw $this->createNotFoundException('No se encontro el servicio.');    
                }
                // Se cancela descuento en el servicio
                $objServicio->setValorDescuento(null);
                $objServicio->setDescuentoUnitario(null);
                $emComercial->persist($objServicio);
                $emComercial->flush();
                                
                // Se guarda Historial de Cancelación de Beneficio en el servicio.  
                if (in_array($objAdmiMotivoSol->getNombreMotivo(), $arrayAdultoMayor))  
                {
                    $strObservacion = $strObservCancelacion . '<br> Descuento anterior: $' . $objDetalleSol->getPrecioDescuento();
                }
                elseif($objAdmiMotivoSol->getNombreMotivo() == $arrayMotivoDiscapacidad["valor1"])
                {
                    $strObservacion = $strObservCancelacion . '<br> Descuento anterior: '. $objDetalleSol->getPorcentajeDescuento() . '%';
                }
                
                $arrayParametros['objServicio']    = $objServicio;
                $arrayParametros['strIpClient']    = $strIpCreacion;
                $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                $arrayParametros['strObservacion'] = $strObservacion;                
                $arrayParametros['strAccion']      = 'cancelarBeneficio';
                $arrayParametros['intMotivo']      = $objAdmiMotivoCancel->getId();
                $objServicioHistorial              = $serviceServicioHistorial->crearHistorialServicio($arrayParametros);
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();                
            }              
            $emComercial->getConnection()->commit();
            $objResponse->setContent("Se cancelaron la(s) solicitud(es) con exito.");
        }       
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();
            $serviceUtil->insertError('Telcos+', 
                                      'CancelacionCambioBeneficioController->cancelacionBeneficioAction', 
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion
                                     );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas.");
        }
        return $objResponse;                
    }
    /**    
     * cambioBeneficioAction
     * 
     * Función que realiza cambio de beneficio de "Cliente con Discapacidad" a "Cambio de Beneficio 3era Edad / Adulto Mayor". 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-03-2021     
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 24-08-2021 - Se modifica código a service y se realiza validaciones para beneficio 3era Edad Resolución 07-2021.   
     * 
     * @return $objResponse
     */
    public function cambioBeneficioAction()
    {
        $objRequest                     = $this->getRequest();
        $objSesion                      = $objRequest->getSession();                
        $strUsrCreacion                 = $objSesion->get('user');
        $strCodEmpresa                  = $objSesion->get('idEmpresa');        
        $strIpCreacion                  = $objRequest->getClientIp();           
        $fltValorDescuento              = $objRequest->get('fltValorDescuentoAdultoMayor'); 
        $intIdDetalleSolicitud          = $objRequest->get('intIdDetalleSolicitud'); 
        $strFlujoAdultoMayor            = $objRequest->get('strFlujoAdultoMayor');
        $serviceCancelCambioBeneficio   = $this->get('comercial.CancelacionCambioBeneficio');
        $objResponse                    = new Response(); 
        $serviceUtil                    = $this->get('schema.Util');        

        try
        {   
            $arrayParametrosDatos = array();
            $arrayParametrosDatos['strUsrCreacion']         = $strUsrCreacion;
            $arrayParametrosDatos['strCodEmpresa']          = $strCodEmpresa;
            $arrayParametrosDatos['strIpCreacion']          = $strIpCreacion;
            $arrayParametrosDatos['fltValorDescuento']      = $fltValorDescuento;
            $arrayParametrosDatos['intIdDetalleSolicitud']  = $intIdDetalleSolicitud;
            $arrayParametrosDatos['strFlujoAdultoMayor']    = $strFlujoAdultoMayor;

            $arrayResultado = $serviceCancelCambioBeneficio->cambioBeneficio($arrayParametrosDatos); 
            
            if($arrayResultado['status'] == "ERROR")
            {
                $objResponse->setContent($arrayResultado['mensaje']); 
                return  $objResponse;
            } 
            
            $objResponse->setContent("Se realizó cambio de beneficio con exito.");
        } 
        catch(\Exception $e)
        { 
            $serviceUtil->insertError('Telcos+', 
                                      'CancelacionCambioBeneficioController->cambioBeneficioAction', 
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion
                                     );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas.");
        }
        return $objResponse;                            
    }
    /**    
     * actFechaNacimientoAction
     * 
     * Función que carga twig para Actualización de Fecha de Nacimiento del cliente y subida de archivos digitales.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-03-2021     
     * 
     * @return render
     */
    public function actFechaNacimientoAction($intIdDetalleSolicitud, $strOpcion)
    {
        $objRequest                  = $this->getRequest();
        $objSession                  = $objRequest->getSession();
        $arrayCliente                = $objSession->get('cliente');
        $intIdPersonaEmpresaRol      = $arrayCliente['id_persona_empresa_rol'];
        $strCodEmpresa               = $objSession->get('idEmpresa');
        $emComercial                 = $this->get('doctrine')->getManager('telconet');
        $emGeneral                   = $this->getDoctrine()->getManager('telconet_general');		
        $arrayTipoDocumentos         = array();
        $intCantDocumentosPermitidos = 0;
        
        $arrayParamCantDocumentosPermitidos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR', 'COMERCIAL', '',
                                                                 'CANTIDAD_PERMITIDA_DOCUMENTOS', '', '', '', '', '', 
                                                                  $strCodEmpresa);

        if(!empty($arrayParamCantDocumentosPermitidos) && !empty($arrayParamCantDocumentosPermitidos['valor1']))
        {
            $intCantDocumentosPermitidos = intval($arrayParamCantDocumentosPermitidos['valor1']);
        }

        $arrayParamTipoDocumentos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                      "TIPO_DOCUMENTO", "", "", "", "", "",
                                                      $strCodEmpresa);
        $arrayParamTipoDoc = array();
        
        foreach($arrayParamTipoDocumentos as $objParamTipoDoc)
        {
            $arrayParamTipoDoc[] = $objParamTipoDoc['valor1'];
        }
            
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                         ->findByCodigosTipoDocumento($arrayParamTipoDoc);
        
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();           
        }    
        
        $arrayFormDocumentos                            = $this->createForm(new InfoDocumentoType(array('validaFile'=>false,
                                                          'arrayTipoDocumentos'   => $arrayTipoDocumentos)), new InfoDocumento());
        $arrayParametros                                = array('form_documentos' => $arrayFormDocumentos->createView());
        $arrayParametros['arrayTipoDocumentos']         = $arrayTipoDocumentos;
        $arrayParametros['intCantDocumentosPermitidos'] = $intCantDocumentosPermitidos;
        
        if($strOpcion == 'Individual')
        {
            $arrayParamSol = array();
            $arrayParamSol['strCodEmpresa'] = $strCodEmpresa;
            $arrayParamSol['intIdDetalleSolicitud'] = $intIdDetalleSolicitud;

            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getSolictudesPorCriterios($arrayParamSol);
            $arraySolicitud = $arrayResultado['objRegistros'];

            $arrayParametros['arraySolicitud'] = $arraySolicitud[0];
        }
        else
        {   
            if(!empty($intIdPersonaEmpresaRol))
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                $objInfoPersona           = $emComercial->getRepository('schemaBundle:InfoPersona')->find($objInfoPersonaEmpresaRol->getPersonaId());
                
                $objInfoPersonaService      = $this->get('comercial.InfoPersona');
                $strMsjValidaTipoTributario = $objInfoPersonaService->getValidaTipoTributario(
                                                                                              array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                    'strCodEmpresa' => $strCodEmpresa));
                if(!empty($strMsjValidaTipoTributario))
                {
                    $arraySolicitud['strMsjValidaTipoTributario'] = $strMsjValidaTipoTributario;
                    $arrayParametros['arraySolicitud']            = $arraySolicitud;
                }
                else
                {                                        
                    $objAdmiTipoIdentif       = $emGeneral->getRepository('schemaBundle:AdmiTipoIdentificacion')
                                                          ->find($objInfoPersona->getTipoIdentificacion());
                
                    $arraySolicitud['idPersonaRol']       = $intIdPersonaEmpresaRol;
                    $arraySolicitud['nombreCliente']      = sprintf("%s", $objInfoPersonaEmpresaRol->getPersonaId());
                    $arraySolicitud['identificacion']     = $objInfoPersona->getIdentificacionCliente();                
                    if ($objInfoPersona->getFechaNacimiento() != null)
                    {
                        $strFechaNacimiento = strval(date_format($objInfoPersona->getFechaNacimiento(), "Y-m-d"));           
                    }
                    else
                    {
                        $strFechaNacimiento = "";
                    }
                    $arraySolicitud['fechaNacimiento']    = $strFechaNacimiento;
                    $intEdadCliente = 0;
                    $intEdadCliente                       = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                        ->getEdadPersona(array('intIdPersona' => $objInfoPersona->getId()));
                    $arraySolicitud['edad']               = $intEdadCliente;                
                    $arraySolicitud['tipoIdentificacion'] = $objAdmiTipoIdentif->getDescripcion();
                    $strTipoTributario                    = ($objInfoPersona->getTipoTributario()=='NAT') ? 'Natural' : 'Juridico';
                
                    $arraySolicitud['tipoTributario']     = $strTipoTributario;
                    $arrayParametros['arraySolicitud']    = $arraySolicitud;
                }
            }
        }
        
        $arrayParametros['strOpcion'] = $strOpcion;        
        
        return $this->render('comercialBundle:cancelacionCambioBeneficio:actFechaNacimiento.html.twig', $arrayParametros);
    }    
    /**
     * buscarClienteAction
     * 
     * Método encargado de realizar la busqueda de la información de un cliente.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23/03/2021
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 18/08/2021 - Se valida para que también se permita bsucar la información cuando sea rol Pre-cliente.
     *
     * @return JsonResponse
     */
    public function buscarClienteAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();        
        $strIdentificacion       = $objRequest->get('strIdentificacion');               
        $strUsrCreacion          = $objSession->get('user');
        $strIpCreacion           = $objRequest->getClientIp();        
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');    
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $serviceUtil             = $this->get('schema.Util');           
        $strStatus               = 'OK';   
        $arraySolicitud          = array();
        $strMsjValida            = "";
        try
        {
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->findOneBy(array('identificacionCliente' => $strIdentificacion));
            
            if(!is_object($objInfoPersona))
            {
                $strStatus    = 'ERROR';
                $strMsjValida = 'No se encontro información del Cliente.';
                throw new \Exception($strMsjValida);
            }
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                'strDescRol'    => 'Cliente',
                                                                                                'intCodEmpresa' => $strCodEmpresa));
            if(!is_object($objInfoPersonaEmpresaRol))
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                    'strDescRol'    => 'Pre-cliente',
                                                                                                    'intCodEmpresa' => $strCodEmpresa));
                if(!is_object($objInfoPersonaEmpresaRol))
                {
                    $strStatus    = 'ERROR';
                    $strMsjValida = 'Identificación no posee Rol Cliente o Rol Pre-cliente';
                    throw new \Exception($strMsjValida);
                } 
            }
            //Valida Tipo Tributario
            $objInfoPersonaService      = $this->get('comercial.InfoPersona');
            $strMsjValidaTipoTributario = $objInfoPersonaService->getValidaTipoTributario(
                                                                 array('intIdPersona'  => $objInfoPersona->getId(),
                                                                       'strCodEmpresa' => $strCodEmpresa));
            if(!empty($strMsjValidaTipoTributario))
            {
                $strStatus    = 'ERROR';
                $strMsjValida = $strMsjValidaTipoTributario;
                throw new \Exception($strMsjValida);
            }

            $objAdmiTipoIdentif = $emGeneral->getRepository('schemaBundle:AdmiTipoIdentificacion')
                                            ->find($objInfoPersona->getTipoIdentificacion());            
            
            $arraySolicitud['idPersonaRol']       = $objInfoPersonaEmpresaRol->getId();
            $arraySolicitud['nombreCliente']      = sprintf("%s", $objInfoPersona);
            $arraySolicitud['identificacion']     = $objInfoPersona->getIdentificacionCliente();            
            
            if ($objInfoPersona->getFechaNacimiento() != null)
            {
                $strFechaNacimiento = strval(date_format($objInfoPersona->getFechaNacimiento(), "Y-m-d"));           
            }
            else
            {
                $strFechaNacimiento = "";
            }
            $arraySolicitud['fechaNacimiento']    = $strFechaNacimiento;
            $intEdadCliente = 0;
            $intEdadCliente                       = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->getEdadPersona(array('intIdPersona' => $objInfoPersona->getId()));
            $arraySolicitud['edad']               = $intEdadCliente;                
            $arraySolicitud['tipoIdentificacion'] = $objAdmiTipoIdentif->getDescripcion();
            $strTipoTributario                    = ($objInfoPersona->getTipoTributario()=='NAT') ? 'Natural' : 'Juridico';              
            $arraySolicitud['tipoTributario']     = $strTipoTributario;            
                
            $strStatus   = 'OK';
            $strMensaje  = '';            
        }
        catch(\Exception $e)
        {
            $strStatus      = "ERROR";
            if(!empty($strMsjValida))
            {
                $strMensaje = $strMsjValida;
            }
            else
            {
                $strMensaje     = "No es posible obtener la información del cliente.";
            }
            $arraySolicitud = array();
            $serviceUtil->insertError('Telcos+', 'CancelacionCambioBeneficioController->buscarClienteAction',
                                       $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $arrayRespuesta = array("strStatus"       => $strStatus,
                                "strMensaje"      => $strMensaje,
                                "arraySolicitud"  => $arraySolicitud);
        
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }    
    
    /**    
     * actualizarFeNacimientoAction
     * 
     * Función que actualiza información del cliente (Fecha de Nacimiento), valida previo a la actualizacion el ingreso de los documentos digitales
     * que son requeridos para el ingreso los cuales se encuentran definidos en el parametro PARAM_FLUJO_ADULTO_MAYOR
     * Se realiza el guardado de Archivos Digitales en el microservicio NFS
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 24-03-2021     
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 18-08-2021 - Se valida para que también se permita confirmar la información cuando sea rol Pre-cliente.
     *                         - Se realiza validación mediante función getContratoPorEstado para obtener el último contrato
     *                           en los estados válidos parametrizados.
     * 
     * @return JsonResponse
     */
    public function actualizarFeNacimientoAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();        
        $strIdentificacion       = $objRequest->get('identificacion_cliente'); 
        $strFechaNacimiento      = $objRequest->get('fecha_nacimiento');      
        $strUsrCreacion          = $objSession->get('user');
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strIpCreacion           = $objRequest->getClientIp();                
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');    
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $emComunicacion          = $this->getDoctrine()->getManager('telconet_comunicacion');
        $serviceUtil             = $this->get('schema.Util');           
        $strStatus               = 'OK';   
        $strMsjValida            = "";
        $arrayDoc                = array();  
        $arrayTipoDoc            = array();      
        
        $arrayDatosFormFiles     = $objRequest->files->get('infodocumentotype');               
        $arrayDatosFormTipos     = $objRequest->get('infodocumentotype');
        $strKey                  = key($arrayDatosFormTipos);        
        $arrayTipoDocumentos     = array (); 
        
        $intPersonaEmpresaRolId  = $objRequest->get('infocontratoextratype')['personaEmpresaRolId']; 
        
        try
        {            
            //Se obtiene mensaje de actualización.
            $arrayMsjDocRequeridos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                          'COMERCIAL','','','MENSAJE_VALIDACION_DOC_REQUERIDOS','',
                                                          '','','',$strCodEmpresa);
            
            $strMsjDocRequeridos = (isset($arrayMsjDocRequeridos["valor2"])
                                   && !empty($arrayMsjDocRequeridos["valor2"])) ? $arrayMsjDocRequeridos["valor2"]
                                   : 'Se debe cargar los archivos requeridos';
            
            //Se verifica los documentos digitales que son requeridos para permitir la actualizacion de la fecha de nacimiento           
            $arrayParamTipoDocumentos = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                          "TIPO_DOCUMENTO", "", "", "S", "", "",
                                                          $strCodEmpresa);
            
            //Se obtiene los estados de contrato requerido para verificar si existe contrato
            $arrayEstadosContrato = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                          "ESTADOS_CONTRATO", "", "", "", "", "",
                                                          $strCodEmpresa);
            
            $arrayEstadosCont = array();
            foreach($arrayEstadosContrato as $arrayEstados)
            {
                $arrayEstadosCont[] = array('valor1' => $arrayEstados["valor1"]);
            } 

            if(empty($arrayEstadosCont))
            {
                $arrayEstadosCont = null;
            }
            
            $objPersonaEmpresaRol    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
            
            $arrayParamContrato = array('idPersonaEmpresaRol' => $intPersonaEmpresaRolId, 'arrayEstados' => $arrayEstadosCont );
            
            $objInfoContrato = $emComercial->getRepository("schemaBundle:InfoContrato")->getContratoPorEstado($arrayParamContrato);
         
            if(!is_object($objInfoContrato))
            {
                $strStatus    = 'ERROR';
                $strMsjValida = 'No se encontro el Contrato.';
                throw new \Exception($strMsjValida); 
            }

            foreach ($arrayDatosFormTipos as $strKey => $arrayTipos)
            {                           
                foreach ( $arrayTipos as $strKeyTipo => $strValue)
                {                     
                    $arrayTipoDocumentos[$strKeyTipo] = $strValue; 
                    $arrayTipoDoc[] = $strValue;
                }
            }
            $intIndice = 0;
            foreach($arrayDatosFormFiles as $strKeyImg => $strImagenes)
            {            
                foreach($strImagenes as $strkeyImg2 => $strValueImg)
                {              
                    $arrayDoc[] = $strValueImg; 
                    if($strValueImg)
                    {                  
                        $arrayFileBase64[$intIndice]['file']      = base64_encode(file_get_contents($strValueImg->getPathName()));                    
                        $arrayArchivo                             = explode('.', $strValueImg->getClientOriginalName());
                        $intCountArray                            = count($arrayArchivo);                    
                        $strExtArchivo                            = $arrayArchivo[$intCountArray - 1];                    
                        $arrayFileBase64[$intIndice]['extension'] = $strExtArchivo;
                        $intIndice++;
                    }
                }
            }
            
            $arrayParamTipoDoc   = array();
            $strTipoDocRequerido = " ";
            foreach($arrayParamTipoDocumentos as $objParamTipoDoc)
            {
                $arrayParamTipoDoc[] = $objParamTipoDoc['valor1'];
                $arrayParamDescTipoDoc[] = strtoupper($objParamTipoDoc['valor2']);                
            }           
            $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                             ->findByCodigosTipoDocumento($arrayParamTipoDoc);                    
            $boolTipoDoc = true;
            $strTipoDocRequerido = implode(" y ", $arrayParamDescTipoDoc);
            foreach ( $objTiposDocumentos as $objTiposDocumentos )
            {    
                $boolEncontro  =  false;
                for($intI=0; $intI<=count($arrayTipoDoc);$intI++)
                {
                    if($objTiposDocumentos->getId() == $arrayTipoDoc[$intI] && $arrayDoc[$intI] != null)
                    {
                       $boolEncontro = true;
                    }
                    if($boolEncontro)
                    {
                        break;
                    }
                }
                if(!$boolEncontro)
                {
                    $strStatus    = 'ERROR';
                    $strMsjValida = $strMsjDocRequeridos.' de '. $strTipoDocRequerido;
                    throw new \Exception($strMsjValida); 
                }   
            }  
            
            //Se obtiene mensaje de actualización.
            $arrayMsjActualizacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                             'COMERCIAL','','','MENSAJE_ACTUALIZACION_FECHA_NACIMIENTO','',
                                                             '','','',$strCodEmpresa);
            
            $strMensajeActualizacion = (isset($arrayMsjActualizacion["valor2"])
                                                   && !empty($arrayMsjActualizacion["valor2"])) ? $arrayMsjActualizacion["valor2"]
                                                   : 'Se actualizo la Fecha de Nacimiento.';
            
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->findOneBy(array('identificacionCliente' => $strIdentificacion));
            
            if(!is_object($objInfoPersona))
            {
                $strStatus    = 'ERROR';
                $strMsjValida = 'No se encontro información del Cliente.';
                throw new \Exception($strMsjValida);
            }

            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                'strDescRol'    => 'Cliente',
                                                                                                'intCodEmpresa' => $strCodEmpresa));
            if(!is_object($objInfoPersonaEmpresaRol))
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                    'strDescRol'    => 'Pre-cliente',
                                                                                                    'intCodEmpresa' => $strCodEmpresa));
                if(!is_object($objInfoPersonaEmpresaRol))
                {
                    $strStatus    = 'ERROR';
                    $strMsjValida = 'Identificación no posee Rol Cliente o Rol Pre-cliente';
                    throw new \Exception($strMsjValida);
                } 
            }
                        
            $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
            $objInfoPersonaEmpresaRolHisto->setEstado($objInfoPersonaEmpresaRol->getEstado());
            
            $strObservacion = $strMensajeActualizacion.' <br> Valor nuevo: '. $strFechaNacimiento
                              . '<br> Valor anterior: '. strval(date_format($objInfoPersona->getFechaNacimiento(), "Y-m-d"));
                                                   
            $objInfoPersonaEmpresaRolHisto->setObservacion($strObservacion);
            $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaEmpresaRolHisto->setIpCreacion($strIpCreacion);
            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objInfoPersonaEmpresaRolHisto);
                        
            $objInfoPersona->setFechaNacimiento(date_create($strFechaNacimiento));
            $emComercial->persist($objInfoPersona);
            
            //Guardo files asociados al contrato                           
            $arrayParmGuardarNFS = array('arrayDatosFormFiles' => $arrayDatosFormFiles,
                                         'arrayFileBase64'     => $arrayFileBase64,
                                         'arrayTipoDocumentos' => $arrayTipoDocumentos,
                                         'strUsuario'          => $strUsrCreacion,
                                         'prefijoEmpresa'      => $strPrefijoEmpresa,
                                         'strApp'              => 'TmComercial',
                                         'objPerEmpRol'        => $objPersonaEmpresaRol,
                                         'strNombreEtiqueta'   => 'AdultoMayor'
            );

            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');

            $arrayRespuestaNfs = $serviceInfoContrato->guardarArchivoNFS($arrayParmGuardarNFS);
            if(isset($arrayRespuestaNfs) && !empty($arrayRespuestaNfs))
            {
                foreach($arrayRespuestaNfs as $arrayValor)
                {
                    $objInfoDocumento = new InfoDocumento();
                    $objInfoDocumento->setNombreDocumento($arrayValor['strNombreArchivo']);
                    $objInfoDocumento->setUbicacionLogicaDocumento($arrayValor['strNombreArchivo']);
                    $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                    $objInfoDocumento->setUsrCreacion($strUsrCreacion);
                    $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                    $objInfoDocumento->setIpCreacion($strIpCreacion);
                    $objInfoDocumento->setEstado('Activo');
                    $objInfoDocumento->setMensaje("Archivo agregado al contrato # "
                                                  . $objInfoContrato->getNumeroContrato());
                    $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                    $objInfoDocumento->setPath($arrayValor['strUrl']);
                    $objInfoDocumento->setFile(null);
                    $objInfoDocumento->setUbicacionFisicaDocumento($arrayValor['strUrl']);

                    if(isset($arrayValor['strTipoDocGeneral']))
                    {
                        $objTipoDocumentoGeneral = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                             ->find($arrayValor['strTipoDocGeneral']);
                        if($objTipoDocumentoGeneral != null)
                        {
                            $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                        }
                    }

                    $objTipoDocumento = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                       ->findOneByExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                    if($objTipoDocumento != null)
                    {
                        $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                    }
                    else
                    {
                        $objAdmiTipoDocumento = new AdmiTipoDocumento();
                        $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                        $objAdmiTipoDocumento->setTipoMime(strtoupper($arrayValor['strTipoDocumento']));
                        $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO ' . strtoupper($arrayValor['strTipoDocumento']));
                        $objAdmiTipoDocumento->setEstado('Activo');
                        $objAdmiTipoDocumento->setUsrCreacion($strUsrCreacion);
                        $objAdmiTipoDocumento->setFeCreacion(new \DateTime('now'));
                        $emComunicacion->persist($objAdmiTipoDocumento);
                        $emComunicacion->flush();
                        $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                    }
                    $objInfoDocumento->setContratoId($objInfoContrato->getId());
                    $emComunicacion->persist($objInfoDocumento);
                    $emComunicacion->flush();

                    $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                    $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                    $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                    $objInfoDocumentoRelacion->setContratoId($objInfoContrato->getId());
                    $objInfoDocumentoRelacion->setEstado('Activo');
                    $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                    $objInfoDocumentoRelacion->setUsrCreacion($strUsrCreacion);

                    $emComunicacion->persist($objInfoDocumentoRelacion);
                    $emComunicacion->flush();
                }
            }
            
            $emComercial->flush ();
            $strStatus   = 'OK';
            $strMensaje  = 'Se actualizó con éxito la información del cliente.';            
        }
        catch(\Exception $e)
        {
            $strStatus      = "ERROR";
            if(!empty($strMsjValida))
            {
                $strMensaje = $strMsjValida;
            }
            else
            {
                $strMensaje = "No es posible actualizar la información del cliente";
            }            
            $serviceUtil->insertError('Telcos+', 'CancelacionCambioBeneficioController->actualizarFeNacimientoAction',
                                       $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }             
        $arrayRespuesta = array("strStatus"       => $strStatus,
                                "strMensaje"      => $strMensaje);
        
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;    
    }
    /**    
     * confirmarFeNacimientoAction
     * 
     * Función que se encarga de confirmar la fecha de nacimiento.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 24-03-2021     
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 19-08-2021 - Se valida para que también se permita confirmar la información cuando sea rol Pre-cliente.
     * @return JsonResponse
     */
    public function confirmarFeNacimientoAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();        
        $strIdentificacion       = $objRequest->get('strIdentificacion');               
        $strFechaNacimiento      = $objRequest->get('strFechaNacimiento');
        $strUsrCreacion          = $objSession->get('user');
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strIpCreacion           = $objRequest->getClientIp();                
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');    
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $serviceUtil             = $this->get('schema.Util');           
        $strStatus               = 'OK'; 
        $strMsjValida            = "";                
        try
        {
            //Se obtiene mensaje de confirmación.
            $arrayMsjConfirmacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                             'COMERCIAL','','','MENSAJE_CONFIRMACION_FECHA_NACIMIENTO','',
                                                             '','','',$strCodEmpresa);
            
            $strMensajeConfirmacion = (isset($arrayMsjConfirmacion["valor2"])
                                                   && !empty($arrayMsjConfirmacion["valor2"])) ? $arrayMsjConfirmacion["valor2"]
                                                   : 'Se confirma Fecha de Nacimiento.';
                
            $objInfoPersona     = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->findOneBy(array('identificacionCliente' => $strIdentificacion));
            
            if(!is_object($objInfoPersona))
            {
                $strStatus    = 'ERROR';
                $strMsjValida = 'No se encontro información del Cliente.';
                throw new \Exception($strMsjValida);
            }
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                'strDescRol'    => 'Cliente',
                                                                                                'intCodEmpresa' => $strCodEmpresa));
            if(!is_object($objInfoPersonaEmpresaRol))
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objInfoPersona->getId(),
                                                                                                    'strDescRol'    => 'Pre-cliente',
                                                                                                    'intCodEmpresa' => $strCodEmpresa));
                if(!is_object($objInfoPersonaEmpresaRol))
                {
                    $strStatus    = 'ERROR';
                    $strMsjValida = 'Identificación no posee Rol Cliente o Rol Pre-cliente';
                    throw new \Exception($strMsjValida);
                }  
            }
            
            $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
            $objInfoPersonaEmpresaRolHisto->setEstado($objInfoPersonaEmpresaRol->getEstado());            
            $objInfoPersonaEmpresaRolHisto->setObservacion($strMensajeConfirmacion);
            $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaEmpresaRolHisto->setIpCreacion($strIpCreacion);
            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objInfoPersonaEmpresaRolHisto);
                        
            $emComercial->flush ();
            $strStatus   = 'OK';
            $strMensaje  = 'Se confirmo con exito la fecha de nacimiento';            
        }
        catch(\Exception $e)
        {
            $strStatus      = "ERROR";
            if(!empty($strMsjValida))
            {
                $strMensaje = $strMsjValida;
            }
            else
            {                
                $strMensaje = "No es posible confirmar la fecha de nacimiento del cliente";           
            }                     
            $serviceUtil->insertError('Telcos+', 'CancelacionCambioBeneficioController->confirmarFeNacimientoAction',
                                       $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $arrayRespuesta = array("strStatus"       => $strStatus,
                                "strMensaje"      => $strMensaje);
        
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;                
    }
}
