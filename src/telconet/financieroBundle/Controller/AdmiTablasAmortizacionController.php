<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;
use \telconet\schemaBundle\Entity\AdmiParametroDet;
use \telconet\schemaBundle\Entity\AdmiParametroCab;
 

/**
 * AdmiTablasAmortizacion Controller.
 *
 */
class AdmiTablasAmortizacionController extends Controller implements TokenAuthenticatedController
{  
    /**    
    *
    * Documentación para el método 'admitablasamortizacionAction'.
    * Método que redirecciona a página de administración de parámetros
    * ADMINISTRACIÓN DE TABLAS DE AMORTIZACIÓN.
    * @Secure(roles="ROLE_433-6")    
    * @author : Madeline Haz <mhaz@telconet.ec>
    * @version 1.0 26-06-2019
    */      
    public function admitablasamortizacionAction()
    {            
        return $this->render('financieroBundle:AdmiFacturacion:TablasAmotizacion.html.twig');     
    }

    /**
     * getListadoParameCabInstAjaxAction, Obtiene la cabecera del parámetro 
     * 'PROM_PRECIO_INSTALACION' de la estructura ADMI_PARAMETRO_CAB.
     * 
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     * @Secure(roles="ROLE_433-6")    
     */
    public function getListadoParameCabInstAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                            = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'PROM_PRECIO_INSTALACION';
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
    }
        
    /**
     * getListadoParametrosDetAjaxAction, Obtiene el detalle de los parámetro 
     * 'PROM_PRECIO_INSTALACION' de la estructura ADMI_PARAMETRO_DET.
     * @author  Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_433-6")        
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strBuscaCabecera']      = "SI";
        $arrayParametros['strNombreParametroCab'] = 'PROM_PRECIO_INSTALACION';
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

        //Obtiene registros de la entidad AdmiParametroDet.
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findParametrosDet($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta.        
        if(isset($arrayAdmiParametroDet['strMensajeError']) && empty($arrayAdmiParametroDet['strMensajeError']))
        {
            //Itera el array obtenido en la consulta.
            foreach($arrayAdmiParametroDet['arrayResultado'] as $arrayAdmiParamDet):
                $arrayAdmiParametroDetResult[] = 
                    array(
                            'intIdParametroDet' => $arrayAdmiParamDet['intIdParametroDet'],
                            'strDescripcionDet' => $arrayAdmiParamDet['strDescripcionDet'],
                            'strValor1'         => $arrayAdmiParamDet['strValor1'],
                            'strValor2'         => $arrayAdmiParamDet['strValor2'],
                            'strValor3'         => $arrayAdmiParamDet['strValor3'],                                                       
                            'strValor4'         => $arrayAdmiParamDet['strValor4'],
                            'strValor5'         => $arrayAdmiParamDet['strValor5'],
                            'strValor6'         => $arrayAdmiParamDet['strValor6'],
                            'strEstado'         => $arrayAdmiParamDet['strEstado'],
                            'strUsrCreacion'    => $arrayAdmiParamDet['strUsrCreacion'],
                            'strFeCreacion'     => $arrayAdmiParamDet['strFeCreacion']->format('d/m/Y')
                    );
            endforeach;
        }  
        $objResponse->setData( array('jsonAdmiParametroDetResult' => $arrayAdmiParametroDetResult,
                                     'intTotalParametros'         => $arrayAdmiParametroDet['intTotal'],
                                     'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError']));        
        return $objResponse;
    }
    
    /**
     * getListadoParametrosHistAjaxAction, obtiene el historial de cambios de los parámetros 
     * 'PROM_PRECIO_INSTALACION' de la estructura ADMI_PARAMETRO_HIST.
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019.
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_433-6")        
     */
    public function getListadoParametrosHistAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'PROM_PRECIO_INSTALACION';
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
                                                       'strValor3'          => $arrayAdmiParamHist['strValor3'],
                                                       'strValor4'          => $arrayAdmiParamHist['strValor4'],
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
     * getListadoParamEquipoAjaxAction, Obtiene la cabecera de los parámetro
     * 'RETIRO_EQUIPOS_SOPORTE' de la estructura ADMI_PARAMETRO_CAB.
     * 
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     * @Secure(roles="ROLE_433-6")            
     */
    public function getListadoParamEquipoAjaxAction()
    { 
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                           = new JsonResponse();
        $objRequest                            = $this->getRequest();
        $arrayParametro                        = array();
        $arrayParametro['strNombreParametro']  = 'RETIRO_EQUIPOS_SOPORTE';
        $arrayParametro['strModulo']           = 'FINANCIERO';
        $arrayParametro['strProceso']          = 'FACTURACION_RETIRO_EQUIPOS';             
        $arrayParametro['intStart']            = $objRequest->get('start');
        $arrayParametro['intLimit']            = $objRequest->get('limit');

        //Obtiene los registros de la entidad AdmiParametroCab.
        $arrayAdmiParametroCabecera            = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                           ->findParametrosCab($arrayParametro);       
        //Valida que no tenga mensaje de error la consulta.        
        if(isset($arrayAdmiParametroCabecera['strMensajeError']) && empty($arrayAdmiParametroCabecera['strMensajeError']))
        {
            //Itera el array de los datos obtenidos.
            foreach($arrayAdmiParametroCabecera['arrayResultado'] as $arrayAdmiParamCab):
                    $arrayAdmiParametroCabResultado[] = array('intParametroId'     => $arrayAdmiParamCab['intIdParametro'],
                                                              'strParametro'       => $arrayAdmiParamCab['strNombreParametro'],
                                                              'strDescription'     => $arrayAdmiParamCab['strDescripcion'],
                                                              'strModule'          => $arrayAdmiParamCab['strModulo'],
                                                              'strProcess'         => $arrayAdmiParamCab['strProceso'],
                                                              'strState'           => $arrayAdmiParamCab['strEstado'],
                                                              'strDate'            => $arrayAdmiParamCab['strFeCreacion']->format('d/m/Y'),
                                                              'strUser'            => $arrayAdmiParamCab['strUsrCreacion']);
            endforeach;
        }        
        $objResponse->setData(array('jsonAdmiParametroCab'  => $arrayAdmiParametroCabResultado,
                                    'intTotalParam'         => $arrayAdmiParametroCabecera['intTotal'],
                                    'strMensajeError'       => $arrayAdmiParametroCabecera['strMensajeError']));        
        return $objResponse;
    }
    
    /**
     * getListParamDetEquiposAjaxAction, Obtiene el detalle de los parámetro 
     * 'RETIRO_EQUIPOS_SOPORTE' de la estructura ADMI_PARAMETRO_DET.
     * @author  Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_433-6")    
     */
    public function getListParamDetEquiposAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strBuscaCabecera']      = "SI";
        $arrayParametros['strNombreParametroCab'] = 'RETIRO_EQUIPOS_SOPORTE';
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

        //Obtiene registros de la entidad AdmiParametroDet.
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findParametrosDet($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta.        
        if(isset($arrayAdmiParametroDet['strMensajeError']) && empty($arrayAdmiParametroDet['strMensajeError']))
        {
            //Itera el array obtenido en la consulta.
            foreach($arrayAdmiParametroDet['arrayResultado'] as $arrayAdmiParamDet):
                $arrayAdmiParametroDetResult[] = 
                    array(
                          'intIdParamDet'        => $arrayAdmiParamDet['intIdParametroDet'],
                          'strDescriptionDet'    => $arrayAdmiParamDet['strDescripcionDet'],
                          'strValor1Det'         => $arrayAdmiParamDet['strValor1'],
                          'strValor2Det'         => $arrayAdmiParamDet['strValor2'],
                          'strValor3Det'         => $arrayAdmiParamDet['strValor3'],                                                       
                          'strValor4Det'         => $arrayAdmiParamDet['strValor4'],
                          'strValor5Det'         => $arrayAdmiParamDet['strValor5'],
                          'strValor6Det'         => $arrayAdmiParamDet['strValor6'],
                          'strStateDet'          => $arrayAdmiParamDet['strEstado'],
                          'strUserDet'           => $arrayAdmiParamDet['strUsrCreacion'],
                          'strDateDet'           => $arrayAdmiParamDet['strFeCreacion']->format('d/m/Y')
                    );
            endforeach;
        }                
        $objResponse->setData( array('jsonAdmiParamDetResult'     => $arrayAdmiParametroDetResult,
                                     'intTotalParam'              => $arrayAdmiParametroDet['intTotal'],
                                     'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError']));        
        return $objResponse;
    }
        
    /**
     * getListParamHistEquiposAjaxAction, obtiene el historial de cambios de los parámetros 
     * 'PROM_PRECIO_INSTALACION' de la estructura ADMI_PARAMETRO_HIST.
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019.
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_433-6")    
     */
    public function getListParamHistEquiposAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $objResponse                              = new JsonResponse();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'RETIRO_EQUIPOS_SOPORTE';
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
                
                $arrayAdmiParametroHistResult[] = array('intIdParamDet'        => $arrayAdmiParamHist['intIdParametroHist'],
                                                       'strDescripDet'         => $arrayAdmiParamHist['strObservacion'],
                                                       'strValor1Hist'         => $arrayAdmiParamHist['strValor1'],
                                                       'strValor2Hist'         => $arrayAdmiParamHist['strValor2'],
                                                       'strValor3Hist'         => $arrayAdmiParamHist['strValor3'],
                                                       'strValor4Hist'         => $arrayAdmiParamHist['strValor4'],
                                                       'strValor5Hist'         => $arrayAdmiParamHist['strValor5'],
                                                       'strValor6Hist'         => $arrayAdmiParamHist['strValor6'],
                                                       'strStateHist'          => $arrayAdmiParamHist['strEstado'],
                                                       'strUserHist'           => $arrayAdmiParamHist['strUsrCreacion'],
                                                       'strDateHist '          => $arrayAdmiParamHist['strFeCreacion']->format('d/m/Y'),
                                                       'strUsrUltModHist'      => $arrayAdmiParamHist['strUsrUltMod'],
                                                       'strFeUltModHist'       => $arrayAdmiParamHist['strFeUltMod']->format('d/m/Y H:i:s')
                    );
            endforeach;
        }                
        $objResponse->setData(array('jsonAdmiParamHistResultEquipo' => $arrayAdmiParametroHistResult,
                                    'intTotalParam'                 => $arrayAdmiParametroHist['intTotal'],
                                    'strMensajeError'               => $arrayAdmiParametroHist['strMensajeError']));        
        return $objResponse;
    }

    /**
     * getactualizaParametroDetAjaxAction, actualiza los registros en la ADMI_PARAMETRO_DET
     * para los parámetros de equipo.
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.0 26-06-2019.
     * 
     * @return json con un código de estatus y un mensaje de acción realizada.
     * @Secure(roles="ROLE_389-1")       
     */    
    public function getactualizaParametroDetAjaxAction()
    {
     
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();        
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        
        $arrayParametros['intIdParametroDet']  = $objRequest->get('intIdParametroDet');
        $arrayParametros['strDescripcion']     = $objRequest->get('strDescripcion');
        $arrayParametros['strValor1']          = $objRequest->get('strValor1');
        $arrayParametros['strValor2']          = $objRequest->get('strValor2');
        $arrayParametros['strValor3']          = $objRequest->get('strValor3');
        $arrayParametros['strValor4']          = $objRequest->get('strValor4');
        $arrayParametros['strValor5']          = $objRequest->get('strValor5');
        $arrayParametros['strValor6']          = $objRequest->get('strValor6');        
        $arrayParametros['strValor7']          = $objRequest->get('strValor7');
        $arrayParametros['strEmpresaCod']      = $objSession->get('idEmpresa');
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            if($arrayParametros)
            {
               $entityAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($arrayParametros['intIdParametroDet']);
                    //AdmiParametroDet
                if($entityAdmiParametroDet)
                { 
                        $entityAdmiParametroDet->setDescripcion($arrayParametros['strDescripcion']);                        
                        $entityAdmiParametroDet->setValor5($arrayParametros['strValor5']);
                        $entityAdmiParametroDet->setValor6($arrayParametros['strValor6']);                        
                        $entityAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                        $entityAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                        $entityAdmiParametroDet->setIpUltMod($objRequest->getClientIp());
                        $emGeneral->persist($entityAdmiParametroDet);
                        $emGeneral->flush();
                        $emGeneral->getConnection()->commit();
                        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
                }

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

    /**
     * getValorPermanenciaMinimaAction, obtiene los valores de permanencia mínima parametrizados.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 10-01-2020.
     * 
     * @return json con valores respectivos.
     * @Secure(roles="ROLE_433-6")       
     */    
    public function getValorPermanenciaMinimaAction()
    {
        $emGeneral                     = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                    = $this->getRequest();
        $objResponse                   = new JsonResponse();
        $objSession                    = $objRequest->getSession();
        $strEmpresaCod                 = $objSession->get('idEmpresa');
             
        $arrayPermMin24  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->getOne('CANCELACION VOLUNTARIA','FINANCIERO','FACTURACION',"","PERMANENCIA MINIMA 24 MESES",
                                               "","","","",$strEmpresaCod);
        
        $arrayPermMin36  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->getOne('CANCELACION VOLUNTARIA','FINANCIERO','FACTURACION',"","PERMANENCIA MINIMA 36 MESES",
                                               "","","","",$strEmpresaCod);

        if(count($arrayPermMin24) > 0 && count($arrayPermMin36) > 0)
        {
            $objResponse->setData(array('intPerMin24'  => $arrayPermMin24['valor2'],
                                        'intPerMin36'  => $arrayPermMin36['valor2'],
                                        'strMsjeError' => ''));
        }
        return $objResponse;
    }   
}
