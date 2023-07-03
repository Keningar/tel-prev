<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;

/**
 * AdmiMotivosController.
 *
 */
class AdmiMotivosEliminarController extends Controller implements TokenAuthenticatedController
{  
    /**
    * @Secure(roles="ROLE_430-6")
    *
    * Documentación para el método 'administrarMotivosEliminacion'.
    * Método que redirecciona a la página de administración de parámetros de motivos de Eliminación.
    * 
    * @author : Josselhin Moreira <kjmoreira@telconet.ec>
    * @version 1.0 21-03-2019.
    */      
    public function administrarMotivosEliminacionAction()
    {
      return $this->render('financieroBundle:AdmiMotivoEliminar:show.html.twig');
    }
    
    /**
     * getListadoParametrosCabAjaxAction, Obtiene los parámetros (motivos) para eliminar la orden de Servicio de la cabecera (en la estructura ADMI_PARAMETRO_CAB).
     * @author Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 1.0 20-03-2019.
     * 
     * @return json con el total de registros y un array formado con la data obtenida.
     *
     * @Secure(roles="ROLE_430-6")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR';
        $arrayParametros['strModulo']           = 'FINANCIERO';
        $arrayParametros['strProceso']          = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR';
        $arrayParametros['intStart']            = $objRequest->get('start');
        $arrayParametros['intLimit']            = $objRequest->get('limit');
        
        //Obtiene los registros de la entidad AdmiParametroCab.
        $arrayAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findParametrosCab($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta.
        if(empty($arrayAdmiParametroCab['strMensajeError']))
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
        $objResponse = new Response(json_encode(array('jsonAdmiParametroCabResult' => $arrayAdmiParametroCabResult,
                                                      'intTotalParametros'         => $arrayAdmiParametroCab['intTotal'],
                                                      'strMensajeError'            => $arrayAdmiParametroCab['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }  

    /**
     * getListadoParametrosDetAjaxAction, Obtiene los detalles de los parámetros Motivos de Elimanción de la Orden de Servicio de la estructura ADMI_PARAMETRO_DET.
     * @author  Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 1.0 20-03-2019
     * 
     * @return json con el total de registros y un array formado con la data obtenida.
     *
     * @Secure(roles="ROLE_430-6") 
    */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $arrayParametros                          = array();
        $arrayParametros['strBuscaCabecera']      = "SI";
        $arrayParametros['strNombreParametroCab'] = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');
        
        $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametroCab']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }

        //Obtiene registros de la entidad AdmiParametroDet.
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta.
        if(empty($arrayAdmiParametroDet['strMensajeError']))
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
        $objResponse = new Response(json_encode(array('jsonAdmiParametroDetResult' => $arrayAdmiParametroDetResult,
                                                      'intTotalParametros'         => $arrayAdmiParametroDet['intTotal'],
                                                      'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }  
    

    /**
     * activarInactivarMotivosRechazoAjaxAction, Activa e Inactiva el parámetro correspondiente a Motivos de Elimanción de la Orden de Servicio.
     * @author Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 1.0 20-03-2019.
     * 
     * @return 
     *
     * @Secure(roles="ROLE_430-6")
     */
    public function activarInactivarMotivosRechazoAjaxAction()
    {
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        $strUserSession                             = $objSession->get('user');
        $strIpCreacion                              = $objRequest->getClientIp();
        $objReturnResponse                          = new ReturnResponse();
        $emGeneral                                  = $this->getDoctrine()->getManager("telconet_general");   
        $intIdParametroCab                          = $objRequest->get('intIdParametro');
        $strEstado                                  = $objRequest->get('strEstado');   
        $serviceUtil                                = $this->get('schema.Util');
        
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $intIdParametroCab haya sido enviado en el request.
            if(!empty($intIdParametroCab))
            {
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->find($intIdParametroCab);
                
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
                        $emGeneral->persist($objAdmiParametroCab);
                        $emGeneral->flush();

                        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findByParametroId($intIdParametroCab);

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
            $serviceUtil->insertError(  'Telcos+',
                                        'Administración de Motivos ELiminar',
                                        'Error al '.$strEstado.' el proceso'.$ex->getMessage(),
                                        $strUserSession,
                                        $strIpCreacion );
            
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus('Existió un error al '.$strEstado.' el proceso');
            $emGeneral        ->getConnection()->rollback();
            $emGeneral        ->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } 
    
    /**
     * getListadoParametrosHistAjaxAction, Obtiene el historial de parámetros de motivos de eliminación de la OS, estructura ADMI_PARAMETRO_HIST.
     * @author Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 1.0 20-03-2019.
     * 
     * @return json con el total de registros y un array formado con la data obtenida.
     *
     * @Secure(roles="ROLE_430-6")
     */
    public function getListadoParametrosHistAjaxAction()
    {
        error_log("Muestra el historial del parametro...");
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');        
        
        $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametroCab']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }

        //Obtiene registros de la entidad AdmiParametroHist.
        $arrayAdmiParametroHist = $emGeneral->getRepository('schemaBundle:AdmiParametroHist')->getParametrosHist($arrayParametros);

             
        //Valida que no tenga mensaje de error la consulta.
        if(empty($arrayAdmiParametroHist['strMensajeError']))
        {
            //Itera el array obtenido en la consulta.
            foreach($arrayAdmiParametroHist['arrayResultado'] as $arrayAdmiParamHist):             
                
                $arrayAdmiParametroHistResult[] = array('intIdParametroDet' => $arrayAdmiParamHist['intIdParametroHist'],
                                                       'strDescripcionDet'  => $arrayAdmiParamHist['strObservacion'],
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
        
        $objResponse = new Response(json_encode(array('jsonAdmiParametroHistResult' => $arrayAdmiParametroHistResult,
                                                      'intTotalParametros'          => $arrayAdmiParametroHist['intTotal'],
                                                      'strMensajeError'             => $arrayAdmiParametroHist['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }      
}
