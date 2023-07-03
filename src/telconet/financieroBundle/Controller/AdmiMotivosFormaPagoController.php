<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiMotivo;
use Symfony\Component\HttpFoundation\Response;
/**
 * AdmiMotivosFormaPago Controller.
 *
 */
class AdmiMotivosFormaPagoController extends Controller implements TokenAuthenticatedController
{  
    /**
    * @Secure(roles="ROLE_389-1")
    *
    * Documentación para el método 'AdmiMotivosFormaPago'.
    * Método que redirecciona a página de administración de parámetros MOTIVOS POR CAMBIO FORMA DE PAGO.
    * 
    * @author : Madeline Haz <mhaz@telconet.ec>
    * @version 1.0 09-07-2019
    */      
    public function administrarMotivosFormaPagoAction()
    {                         
     return $this->render('financieroBundle:AdmiFacturacion:MotivosFormaPago.html.twig');     
    }

    /**
     * getListadoParametrosCabAjaxAction, Obtiene la cabecera de los parámetro 
     * MOTIVOS POR CAMBIO FORMA DE PAGO de la estructura ADMI_PARAMETRO_CAB.
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_389-1")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                            = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'MOTIVOS_CAMBIO_FORMA_PAGO';
        $arrayParametros['strModulo']           = 'FINANCIERO';
        $arrayParametros['strProceso']          = 'FACTURACION';   
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
     * getListadoParametrosDetAjaxAction, Obtiene el detalle de los parámetro 
     * MOTIVOS POR CAMBIO FORMA DE PAGO de la estructura ADMI_PARAMETRO_DET.
     * @author  Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro
     *
     * @Secure(roles="ROLE_435-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                             = $this->getRequest();
        $objResponse                            = new JsonResponse();
        $arrayParamCab                          = array();
        $arrayParamCab['strBuscaCabecera']      = "SI";
        $arrayParamCab['strNombreParametroCab'] = 'MOTIVOS_CAMBIO_FORMA_PAGO';
        $arrayParamCab['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParamCab['intStart']              = $objRequest->get('start');
        $arrayParamCab['intLimit']              = $objRequest->get('limit');
        
        $arrayParametrosCab  = array('nombreParametro' => $arrayParamCab['strNombreParametroCab']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParamCab['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }
        //Obtiene registros de la entidad AdmiParametroDet.
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findParametrosDet($arrayParamCab);
       
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
     * getListadoParametrosHistAjaxAction, obtiene el historial de cambios de los parámetros 
     * MOTIVOS POR CAMBIO FORMA DE PAGO de la estructura ADMI_PARAMETRO_HIST.
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 15-03-2019.
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro
     *
     * @Secure(roles="ROLE_435-1")
     */
    public function getListadoParametrosHistAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'MOTIVOS_CAMBIO_FORMA_PAGO';
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

        //Obtiene registros de la entidad AdmiParametroHist.
        $arrayAdmiParametroHist = $emGeneral->getRepository('schemaBundle:AdmiParametroHist')->getParametrosHist($arrayParametros);
             
        //Valida que no tenga mensaje de error la consulta.
        if(isset($arrayAdmiParametroHist['strMensajeError']) && empty($arrayAdmiParametroHist['strMensajeError']))
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
        $objResponse->setData(array('jsonAdmiParametroHistResult' => $arrayAdmiParametroHistResult,
                                    'intTotalParametros'          => $arrayAdmiParametroHist['intTotal'],
                                    'strMensajeError'             => $arrayAdmiParametroHist['strMensajeError']));        
        return $objResponse;
    }
    
    /**
     * Crea registro en la ADMI_PARAMETRO_DET y ADMI_MOTIVO
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-01-2020
     * @return json con un código de estatus y un mensaje de acción realizada
     * 
     * @Secure(roles="ROLE_435-1")
     */
    public function creaMotivoParametroDetAjaxAction()
    {
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametrosDet                      = json_decode($objRequest->get('jsonCreaParametrosDet'));
        $strEmpresaCod = $objSession->get('idEmpresa');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {         
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro' => 'MOTIVOS_CAMBIO_FORMA_PAGO', 
                                                                  'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {
                foreach($arrayParametrosDet->arrayData as $objParametrosDet):
                    
                $objAdmiParametroDetSearch = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                                      'valor1'      => trim($objParametrosDet->strValor1),
                                                                      'valor2'      => trim($objParametrosDet->strValor2),
                                                                      'empresaCod'  => $strEmpresaCod,
                                                                      'estado'      => 'Activo'));

                if(!is_object($objAdmiParametroDetSearch))
                {
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                    $objAdmiParametroDet->setDescripcion(trim($objParametrosDet->strDescripcion));
                    $objAdmiParametroDet->setValor1(trim($objParametrosDet->strValor1));
                    $objAdmiParametroDet->setValor2(trim($objParametrosDet->strValor2));
                    $objAdmiParametroDet->setEmpresaCod($strEmpresaCod);
                    $objAdmiParametroDet->setEstado("Activo");
                    $objAdmiParametroDet->setUsrCreacion($objSession->get('user'));
                    $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                    $objAdmiParametroDet->setIpCreacion($objRequest->getClientIp());                    
                    
                    $objAdmiMotivo = new AdmiMotivo();
                    $objAdmiMotivo->setNombreMotivo($objParametrosDet->strValor2);
                    $objAdmiMotivo->setRelacionSistemaId(425);
                    $objAdmiMotivo->setEstado('Activo');
                    $objAdmiMotivo->setFeCreacion(new \DateTime('now'));
                    $objAdmiMotivo->setUsrCreacion($objSession->get('user'));
                    $objAdmiMotivo->setFeUltMod(new \DateTime('now'));
                    $objAdmiMotivo->setUsrUltMod($objSession->get('user'));
                        
                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();
                    $emGeneral->persist($objAdmiMotivo);
                    $emGeneral->flush();                    
                }                       
                 
                endforeach;                
                $emGeneral->getConnection()->commit();
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se encontró la cabecera del parámetro.');
            }
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' '.$ex->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }     
}
