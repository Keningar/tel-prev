<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;

/**
 * AdmiMotivoRechazoOS Controller.
 *
 */
class AdmiMotivoRechazoOSController extends Controller implements TokenAuthenticatedController
{  
    /**
    * @Secure(roles="ROLE_451-1")
    *
    * Documentación para el método 'administrarMotivosRechazoOS'.
    * Método que redirecciona a página de administración de parámetros de motivos de rechazo de OS.
    * 
    * @author  : Gustavo Narea <gnarea@telconet.ec> 
    * @version : 1.0
    */      
    public function administrarMotivosRechazoOSAction()
    {                         
     return $this->render('financieroBundle:AdmiMotivoRechazoOS:show.html.twig');
    }

    /**
     * getListadoParametrosCabAjaxAction, Obtiene la cabecera de los parámetro de motivos de rechazo de la OS de la estructura ADMI_PARAMETRO_CAB.
     * @author Gustavo Narea
     * @version 1.0
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_451-1")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                            = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'PARAMETROS_REINGRESO_OS_AUTOMATICA';
        $arrayParametros['strModulo']           = 'COMERCIAL';
        $arrayParametros['strProceso']          = 'REINGRESO AUTOMATICO';   
        $arrayParametros['intStart']            = $objRequest->get('start');
        $arrayParametros['intLimit']            = $objRequest->get('limit');
        
        //Obtiene los registros de la entidad AdmiParametroCab.
        $arrayAdmiParametroCab                  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findParametrosCab($arrayParametros);       
        //Valida que no tenga mensaje de error la consulta.        
        if(isset($arrayAdmiParametroCab['strMensajeError']) && empty($arrayAdmiParametroCab['strMensajeError']))
        {
            //Itera el array de los datos obtenidos.
            foreach($arrayAdmiParametroCab['arrayResultado'] as $arrayAdmiParamCab):
                $arrayAdmiParametroCabResult[] = array('intIdParametro'     => $arrayAdmiParamCab['intIdParametro'],
                                                       'strNombreParametro' => $arrayAdmiParamCab['strNombreParametro'],
                                                       'strDescripcion'     => $arrayAdmiParamCab['strDescripcion'],
                                                       'strModulo'          => $arrayAdmiParamCab['strModulo'],
                                                       'strProceso'         => $arrayAdmiParamCab['strProceso'],
                                                       'strEstado'          => $arrayAdmiParamCab['strEstado'],
                                                       'strFeCreacion'      => $arrayAdmiParamCab['strFeCreacion']->format('d/m/Y'),
                                                       'strUsrCreacion'     => $arrayAdmiParamCab['strUsrCreacion']);
            endforeach;
        }        
        $objResponse->setData(array('jsonAdmiParametroCabResult' => $arrayAdmiParametroCabResult,
                                    'intTotalParametros'         => $arrayAdmiParametroCab['intTotal'],
                                    'strMensajeError'            => $arrayAdmiParametroCab['strMensajeError']));
        return $objResponse;
    }//getListadoParametrosCabAjaxAction.


/**
 * Funcion para cambiar el estado de un motivo de rechazo de la OS
 * 
 * @author: Gustavo Narea Gomez
 * @version: 1.0
 * @Secure(roles="ROLE_451-1")
 */
public function actualizarEstadoMotivoRechazoOSAjaxAction()
{
    
    $objResponse                             = new JsonResponse();
    $objReturnResponse                       = new ReturnResponse();

    $emGeneral                               = $this->getDoctrine()->getManager("telconet_general");
    $objPeticion                             = $this->get('request');
    
    $intIdParametroDet                       = $objPeticion->get("intIdParametroDet");
    $intMotivoId                             = $objPeticion->get("intMotivoId");
    $strEstadoMotivo                         = $objPeticion->get("strEstadoMotivo");
    $arrayParametrosCab                       = array ('nombreParametro' => 'PARAMETROS_REINGRESO_OS_AUTOMATICA');
    $objAdmiParametroCab                      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                          ->findOneBy($arrayParametrosCab);
    $objSession                                 = $objPeticion->getSession();
    $strUsuario                                 = $objSession->get('user');
    
    try
    {
        $emGeneral->getConnection()->beginTransaction();
        if(!empty($objAdmiParametroCab))
        {
            $objAdmiParamDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->find($intIdParametroDet);

            if(!($objAdmiParamDet->getEstado()==$strEstadoMotivo))
            {
                $objAdmiParamDet->setEstado($strEstadoMotivo);
                $objAdmiParamDet->setUsrUltMod($strUsuario);
                $objAdmiParamDet->setFeUltMod(new \DateTime('now'));
                $emGeneral->persist($objAdmiParamDet);
                $emGeneral->flush();
                $emGeneral->getConnection()->commit();
                
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus("Estado Cambiado exitosamente");            
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus('El Estado no se ha cambiado');
            }
            
        }
        
    }
    catch(\Exception $ex)
    {
        $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        $objReturnResponse->setStrMessageStatus('Existio un error al '.$strEstado.' el proceso');
        $emGeneral->getConnection()->rollback();
        $emGeneral->getConnection()->close();
    }
    
    $objResponse->headers->set('Content-type', 'text/json');
    $objResponse->setContent(json_encode((array)$objReturnResponse));
    return $objResponse;
}

    /**
     * getListadoParametrosDetAjaxAction, Obtiene el detalle de los parámetro de motivos de rechazo de la OS de la estructura ADMI_PARAMETRO_DET.
     * @author  : Gustavo Narea <gnarea@telconet.ec>
     * @version : 1.0
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro
     * @Secure(roles="ROLE_429-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strBuscaCabecera']      = "SI";
        $arrayParametros['strNombreParametroCab'] = 'PARAMETROS_REINGRESO_OS_AUTOMATICA';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');
        
        $arrayParametrosCab  = array('nombreParametro' => $arrayParametros['strNombreParametroCab']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }
        $arrayParametros["strDescripcionDet"]='MOTIVOS_RECHAZO_ANULACION_OS_AUTOMATICA';
        //Obtiene registros de la entidad AdmiParametroDet.
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findParametrosDet($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta.        
        if(isset($arrayAdmiParametroDet['strMensajeError']) && empty($arrayAdmiParametroDet['strMensajeError']))
        {
            //Itera el array obtenido en la consulta.
            foreach($arrayAdmiParametroDet['arrayResultado'] as $arrayAdmiParamDet):
                $arrayAdmiParametroDetResult[] = array('intIdParametroDet' => $arrayAdmiParamDet['intIdParametroDet'],
                                                       'strDescripcionDet' => $arrayAdmiParamDet['strDescripcionDet'],
                                                       'strValor1'         => $arrayAdmiParamDet['strValor1'],
                                                       'strValor2'         => $arrayAdmiParamDet['strValor2'],
                                                       'strValor3'         => $arrayAdmiParamDet['strValor3'],
                                                       'strValor4'         => $arrayAdmiParamDet['strValor4'],
                                                       'strEstado'         => $arrayAdmiParamDet['strEstado'],
                                                       'strUsrCreacion'    => $arrayAdmiParamDet['strUsrCreacion'],
                                                       'strFeCreacion'     => $arrayAdmiParamDet['strFeCreacion']->format('d/m/Y'));
            endforeach;
        }
        
        $objResponse->setData( array('jsonAdmiParametroDetResult' => $arrayAdmiParametroDetResult,
                                     'intTotalParametros'         => $arrayAdmiParametroDet['intTotal'],
                                     'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError']));        
        return $objResponse;
    }
    
    /**
     * activarInactivarMotivosRechazoOSAjaxAction, Activa e Inactiva el parámetro correspondiente a motivos de rechazo PYL.
     * @author 
     * @version 
     * 
     * @return El estado del parametro
     *
     * @Secure(roles="ROLE_429-1")
     */
    public function activarInactivarMotivosRechazoAjaxAction()
    {
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        $objReturnResponse                          = new ReturnResponse();
        $emGeneral                                  = $this->getDoctrine()->getManager("telconet_general");   
        $intIdParametroCab                          = $objRequest->get('intIdParametro');
        $strEstado                                  = $objRequest->get('strEstado');   
        $serviceUtil                                = $this->get('schema.Util');
        $strUsuario                                 = $objSession->get('user');
        $strIpCreacion                              = $objRequest->getClientIp();
                
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $intIdParametroCab haya sido enviado en el request.
            if(isset ($intIdParametroCab) && !empty($intIdParametroCab))
            {
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->find($intIdParametroCab);
                
                if(is_object($objAdmiParametroCab))
                {
                    if(strcmp($strEstado,$objAdmiParametroCab->getEstado())== 0)
                    {                        
                        $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                        $objReturnResponse->setStrMessageStatus('El proceso ya se encuentra '.$strEstado);                       
                    }
                    else
                    {
                        // Se inactiva cabecera de parámetro cargo por reproceso.
                        $objAdmiParametroCab->setUsrUltMod($objSession->get('user'));
                        $objAdmiParametroCab->setFeUltMod(new \DateTime('now'));
                        $objAdmiParametroCab->setEstado($strEstado);
                        $emGeneral          ->persist($objAdmiParametroCab);
                        $emGeneral          ->flush();

                        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->findByParametroId($intIdParametroCab);

                        foreach($arrayAdmiParametroDet as $objAdmiParametroDet)
                        {
                            // Se inactiva detalle de parámetro cargo por reproceso.
                            $objAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                            $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                            $objAdmiParametroDet->setEstado($strEstado);
                            $emGeneral          ->persist($objAdmiParametroDet);
                            $emGeneral          ->flush();                        
                        }
                        
                        $emGeneral        ->getConnection()->commit();
                        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);                        
                    }
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se econtró el parámetro en la base.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No esta enviando el parámetro.');
            }
        }
        catch(\Exception $ex)
        {
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $serviceUtil->insertError( 'Telcos+', 
                            'FinancieroBundle.AdmiMotivosRechazoPYLController.activarInactivarMotivosRechazoAjaxAction',
                            'Error al cambiar estado del parámetros.'.$e->getMessage(),                            
                             $strUsuario, 
                             $strIpCreacion);                        
                $emGeneral  ->getConnection()->rollback();
              }
            $emGeneral->getConnection()->close();
        }
        $objResponse = new JsonResponse();
        $objResponse->setData((array) $objReturnResponse);        
        return $objResponse;
    } 
    
    /**
     * getListadoParametrosHistAjaxAction, obtiene el historial de cambios de los parámetros de motivos de rechazo PYL de la estructura ADMI_PARAMETRO_HIST.
     * @author 
     * @version 
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro
     *
     * @Secure(roles="ROLE_429-1")
     */
    public function getListadoParametrosHistAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'PARAMETROS_REINGRESO_OS_AUTOMATICA';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');                
        $arrayParametrosCab                       = array ('nombreParametro' => $arrayParametros['strNombreParametroCab']);
        $objAdmiParametroCab                      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                              ->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }
        $arrayParametros['strDescripcion'] = 'MOTIVOS_RECHAZO_ANULACION_OS_AUTOMATICA';
        //Obtiene registros de la entidad AdmiParametroHist.
        $arrayAdmiParametroHist = $emGeneral->getRepository('schemaBundle:AdmiParametroHist')->getParametrosHist($arrayParametros);
             
        //Valida que no tenga mensaje de error la consulta.
        if(isset($arrayAdmiParametroHist['strMensajeError']) && empty($arrayAdmiParametroHist['strMensajeError']))
        {
            //Itera el array obtenido en la consulta.
            foreach($arrayAdmiParametroHist['arrayResultado'] as $arrayAdmiParamHist):             
                
                $arrayAdmiParametroHistResult[] = array('intIdParametroDet' => $arrayAdmiParamHist['intIdParametroHist'],
                                                       'strDescripcionDet'  => $arrayAdmiParamHist['strDescripcionDet'],
                                                       'strValor1'          => $arrayAdmiParamHist['strValor1'],
                                                       'strValor2'          => $arrayAdmiParamHist['strValor2'],
                                                       'strEstado'          => $arrayAdmiParamHist['strEstado'],
                                                       'strUsrCreacion'     => $arrayAdmiParamHist['strUsrCreacion'],
                                                       'strFeCreacion'      => $arrayAdmiParamHist['strFeCreacion']->format('d/m/Y'),
                                                       'strUsrUltMod'       => $arrayAdmiParamHist['strUsrUltMod'],
                                                       'strFeUltMod'        => $arrayAdmiParamHist['strFeUltMod']->format('d/m/Y H:i:s')
                    );
            endforeach;
        }                
        $objResponse->setData(array('jsonAdmiParametroHistResult' => $arrayAdmiParametroHistResult,
                                    'intTotalParametros'          => $arrayAdmiParametroHist['intTotal'],
                                    'strMensajeError'             => $arrayAdmiParametroHist['strMensajeError']));        
        return $objResponse;
    }//getListadoParametrosHistAjaxAction. 
}
