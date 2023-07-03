<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;

/**
 * AdmiCargos controller.
 *
 */
class AdmiCargosController extends Controller implements TokenAuthenticatedController
{  
    /**
    * @Secure(roles="ROLE_390-1")
    *
    * Documentación para el método 'administrarCargoReactivacion'
    * Método que redirecciona a página de administración de parámetros de cargo por reactivación de servicio.
    * 
    * @author : Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 12-04-2017.
    */      
    public function administrarCargoReactivacionAction()
    {       
        return $this->render('financieroBundle:AdmiCargos:administrarCargoReactivacion.html.twig');
    }

    /**
     * getListadoParametrosCabAjaxAction, Obtiene los parámetros de cargo por reactivación de la estructura ADMI_PARAMETRO_CAB 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 12-07-2017
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_390-1")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'CARGO REACTIVACION SERVICIO';
        $arrayParametros['strModulo']           = 'FINANCIERO';
        $arrayParametros['strProceso']          = 'CARGO REACTIVACION SERVICIO';
        
        //Obtiene los regsitros de la entidad AdmiParametroCab
        $arrayAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findParametrosCab($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta
        if(empty($arrayAdmiParametroCab['strMensajeError']))
        {
            //Itera el array de los datos obtenidos
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
    }//getListadoParametrosCabAjaxAction  
    
    
    /**
     * getListadoParametrosDetAjaxAction, Obtiene los parametros de cargo por reactivación de la estructura ADMI_PARAMETRO_DET 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 12-07-2017
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_390-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $arrayParametros                          = array();
        $arrayParametros['strBuscaCabecera']      = "SI";
        $arrayParametros['strNombreParametroCab'] = 'CARGO REACTIVACION SERVICIO';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        
        $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametro']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }

        //Obtiene registros de la entidad AdmiParametroDet
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta
        if(empty($arrayAdmiParametroDet['strMensajeError']))
        {
            //Itera el array obtenido en la consulta
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
    }//getListadoParametrosDetAjaxAction  
    

    /**
     * activarInactivarCargoReactivacionAjaxAction, Activa e Inactiva el parámetro correspondiente a cargo por reactivación de servicio.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 12-07-2017
     * 
     * @return 
     *
     * @Secure(roles="ROLE_390-1")
     */
    public function activarInactivarCargoReactivacionAjaxAction()
    {
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        $objReturnResponse                          = new ReturnResponse();
        $emGeneral                                  = $this->getDoctrine()->getManager("telconet_general");   
        $intIdParametroCab                          = $objRequest->get('intIdParametro');
        $strEstado                                  = $objRequest->get('strEstado');   
        
        
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
                        // Se inactiva cabecera de parámetro cargo por reproceso
                        $objAdmiParametroCab->setUsrUltMod($objSession->get('user'));
                        $objAdmiParametroCab->setFeUltMod(new \DateTime('now'));
                        $objAdmiParametroCab->setEstado($strEstado);
                        $emGeneral->persist($objAdmiParametroCab);
                        $emGeneral->flush();

                        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findByParametroId($intIdParametroCab);

                        foreach($arrayAdmiParametroDet as $objAdmiParametroDet)
                        {
                            // Se inactiva detalle de parámetro cargo por reproceso
                            $objAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                            $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                            $objAdmiParametroDet->setEstado($strEstado);
                            $emGeneral->persist($objAdmiParametroDet);
                            $emGeneral->flush();                        
                        }


                        $emGeneral->getConnection()->commit();
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
            error_log("AdmiCargosController->activarInactivarCargoReactivacionAjaxAction".
                      $objReturnResponse::MSN_ERROR.' '.$ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus('Existio un error al '.$strEstado.' el proceso');
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } 
    
    /**
     * getListadoParametrosHistAjaxAction, Obtiene el historial de parámetros de cargo por reactivación de la estructura ADMI_PARAMETRO_HIST 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 18-07-2017
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_390-1")
     */
    public function getListadoParametrosHistAjaxAction()
    {
        $emGeneral                                = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                               = $this->getRequest();
        $arrayParametros                          = array();
        $arrayParametros['strNombreParametroCab'] = 'CARGO REACTIVACION SERVICIO';
        $arrayParametros['strEmpresaCod']         = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intStart']              = $objRequest->get('start');
        $arrayParametros['intLimit']              = $objRequest->get('limit');        
        
        $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametro']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }

        //Obtiene registros de la entidad AdmiParametroHist
        $arrayAdmiParametroHist = $emGeneral->getRepository('schemaBundle:AdmiParametroHist')->getParametrosHist($arrayParametros);

             
        //Valida que no tenga mensaje de error la consulta
        if(empty($arrayAdmiParametroHist['strMensajeError']))
        {
            //Itera el array obtenido en la consulta
            foreach($arrayAdmiParametroHist['arrayResultado'] as $arrayAdmiParamHist):             
                
                $arrayAdmiParametroHistResult[] = array('intIdParametroDet' => $arrayAdmiParamHist['intIdParametroDet'],
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
    }//getListadoParametrosHistAjaxAction      
    
}
