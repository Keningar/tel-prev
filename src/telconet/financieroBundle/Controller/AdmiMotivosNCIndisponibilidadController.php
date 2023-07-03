<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use Symfony\Component\HttpFoundation\Response;
/**
 * AdmiMotivosNCIndisponibilidad Controller.
 *
 */
class AdmiMotivosNCIndisponibilidadController extends Controller implements TokenAuthenticatedController
{  
    /**
    * @Secure(roles="ROLE_458-1")
    *
    * Documentación para el método 'AdmiMotivosNCIndisponibilidad'.
    * Método que redirecciona a página de administración de parámetros MOTIVOS POR NC POR INDISPONIBILIDAD
    * 
    * @author : Allan Suarez <arsuarez@telconet.ec>
    * @version 1.0 22-04-2021
    */      
    public function administrarMotivosNCIndisponibilidadAction()
    {             
        $emSoporte       = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        $objRequest      = $this->getRequest();
        
        $arrayObjTipoCaso= $emSoporte->getRepository('schemaBundle:AdmiTipoCaso')->findBy(array('estado'=>'Activo'));
        
        $arrayTipoCaso = array();
        $arrayPeriodos = array();
        $arrayTipoAfectacion = array();
        $arrayParametrosCab = array();
        
        foreach($arrayObjTipoCaso as $objTipoCaso)
        {
            $arrayTipoCaso[] = array('id'    => $objTipoCaso->getNombreTipoCaso(),
                                     'valor' => $objTipoCaso->getNombreTipoCaso());
        }     
        
        $arrayParamPeriodos   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                          'SOPORTE', 
                                                          '',
                                                          'PERIODOS',
                                                          '', 
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $objRequest->getSession()->get('idEmpresa'));
                
        foreach($arrayParamPeriodos as $array)
        {
            $arrayPeriodos[] = array('valor' => $array['valor1'],
                                     'id'    => $array['valor1']);
        } 
        
        $arrayParamTipoAfectacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                          'SOPORTE', 
                                                          '',
                                                          'TIPO AFECTACION',
                                                          '', 
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $objRequest->getSession()->get('idEmpresa'));

        foreach($arrayParamTipoAfectacion as $array)
        {
            $arrayTipoAfectacion[] = array('id'     => $array['valor1'],
                                           'valor'  => $array['valor1']);
                                           
        }                
        
        //Detalles parametros que me indica como consultar las hipotesis (todo activo o solo tercer nivel)
        $arrayParametrosHip   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                          'SOPORTE', 
                                                          '',
                                                          'TIPO CONSULTA HIPOTESIS',
                                                          '', 
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $objRequest->getSession()->get('idEmpresa'));

        $strTipoConsulta = 'Todos';
        
        if(!empty($arrayParametrosHip))
        {
            $strTipoConsulta = $arrayParametrosHip['valor1'];            
        }
        
        //Detalles parametros que sean multiple ingreso (mas de un motivo configurado)
        $arrayParamCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                      'SOPORTE', 
                                                                      '',
                                                                      'PARAMETROS DET MULTIPLES',
                                                                      '', 
                                                                      'S',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $objRequest->getSession()->get('idEmpresa'));
        
        foreach($arrayParamCab as $array)
        {
            if(!in_array($array,$arrayParametrosCab))
            {
                $arrayParametrosCab[] = array('id'     => $array['valor1'],
                                              'valor'  => $array['valor1']);
            }                          
        } 
        
        $arrayParametrosConsulta                 = array();
        $arrayParametrosConsulta['info']         = $strTipoConsulta;
        $arrayParametrosConsulta['estado']       = 'Activo';
        $arrayParametrosConsulta['empresaCod']   = $objRequest->getSession()->get('idEmpresa');
        
        $arrayHipotesis = $emSoporte->getRepository('schemaBundle:AdmiHipotesis')->getHipotesisParaNCIndisponiblidad($arrayParametrosConsulta);

        $array                        = array();
        $array['arrayTipoCaso']       = $arrayTipoCaso;
        $array['arrayPeriodos']       = $arrayPeriodos;
        $array['arrayTipoAfectacion'] = $arrayTipoAfectacion;
        $array['arrayHipotesis']      = $arrayHipotesis;
        $array['arrayParametros']     = $arrayParametrosCab;
          
        
        return $this->render('financieroBundle:AdmiMotivoNCIndisponibilidad:motivosNCIndisponibilidad.html.twig',$array);     
    }

    /**
     * getListadoParametrosCabAjaxAction, Obtiene la cabecera de los parámetro 
     * MOTIVOS POR NC POR INDISPONIBILIDAD 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_458-1")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                            = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'PARAMETROS DE INDISPONIBILIDAD PARA NC';
        $arrayParametros['strModulo']           = 'SOPORTE';
        $arrayParametros['strProceso']          = 'NOTAS DE CREDITO';          
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
     * MOTIVOS POR NC POR INDISPONIBILIDAD 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_458-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                             = $this->getRequest();
        $objResponse                            = new JsonResponse();
        $arrayParamCab                          = array();
        $arrayParamCab['strBuscaCabecera']      = "SI";
        $arrayParamCab['strNombreParametroCab'] = 'PARAMETROS DE INDISPONIBILIDAD PARA NC';
        $arrayParamCab['strEstado']             = 'Activo';  
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
                
                $strEsSelector = '';
                $strEsMultiple = '';
                
                //Validar si el parametro es  multiple ingreso, se devolvera el parametro S para control en la pantalla
                //Validar si el parametro es  multiple seleccion, se devolvera el parametro C para control en la pantalla                
                $arrayParamCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                      'SOPORTE', 
                                                                      '',
                                                                      'PARAMETROS DET MULTIPLES',
                                                                      $arrayAdmiParamDet['strDescripcionDet'], 
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $objRequest->getSession()->get('idEmpresa'));

                if(!empty($arrayParamCab))
                {                                        
                    $strEsMultiple = $arrayParamCab['valor3'];
                    $strEsSelector = $arrayParamCab['valor2'];                    
                }

                $arrayAdmiParametroDetResult[] = array('intIdParametroDet' => $arrayAdmiParamDet['intIdParametroDet'],
                                                       'strDescripcionDet' => $arrayAdmiParamDet['strDescripcionDet'],
                                                       'strValor1'         => $arrayAdmiParamDet['strValor1'],
                                                       'strValor2'         => $arrayAdmiParamDet['strValor2'],
                                                       'strValor3'         => $strEsMultiple,
                                                       'strValor4'         => $strEsSelector,
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
     * creaMotivoParametroDetAjaxAction, crea nuevos parametros
     * MOTIVOS POR NC POR INDISPONIBILIDAD 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_458-1")
     */
    public function creaMotivoParametroDetAjaxAction()
    {
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $emSoporte                             = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayParametrosDet                    = json_decode($objRequest->get('jsonCreaParametrosDet'));
        $serviceUtil                           = $this->get('schema.Util');
        
        $strEmpresaCod = $objSession->get('idEmpresa');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        
        
        try
        {         
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro' => 'PARAMETROS DE INDISPONIBILIDAD PARA NC', 
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
                    //si no existe repetido
                    if(!is_object($objAdmiParametroDetSearch))
                    {
                        $strValor2 = '';

                        if($objParametrosDet->strDescripcion=='TIPO DE PERIODO')
                        {
                            $arrayParamPeriodos   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                      'SOPORTE', 
                                                                      '',
                                                                      'PERIODOS',
                                                                      $objParametrosDet->strValor1, 
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $objRequest->getSession()->get('idEmpresa'));

                            if(!empty($arrayParamPeriodos))
                            {
                                $strValor2 = $arrayParamPeriodos['valor2'];
                            }
                            else
                            {
                                throw new \Exception('Tipo de Período envíado no es válido');
                            }
                        }
                        else if($objParametrosDet->strDescripcion=='TIPO CASO')
                        {
                            $objTipoCaso   = $emSoporte->getRepository('schemaBundle:AdmiTipoCaso')->findOneBy(array('nombreTipoCaso' => $objParametrosDet->strValor1,
                                                                                                                     'estado'         => 'Activo'));

                            if(is_object($objTipoCaso))
                            {
                                $strValor2 = $objTipoCaso->getId();
                            }
                            else
                            {
                                throw new \Exception('Tipo de Caso envíado no es válido');
                            }
                        }
                        else if($objParametrosDet->strDescripcion=='MOTIVO DE INDISPONIBILIDAD PARA NC')
                        {
                            $objHipotesis   = $emSoporte->getRepository('schemaBundle:AdmiHipotesis')->findOneBy(array('nombreHipotesis' => $objParametrosDet->strValor1,
                                                                                                                       'estado'          => array('Activo','Modificado'),
                                                                                                                       'empresaCod'      => $objRequest->getSession()->get('idEmpresa')
                                                                                                                      ));

                            if(is_object($objHipotesis))
                            {
                                $strValor2 = $objHipotesis->getId();
                            }
                            else
                            {
                                 throw new \Exception('Motivo enviado no es válido');
                            }
                        }
                        else if($objAdmiParametroDetSearch->getDescripcion()=='TIPO AFECTACION')
                        {
                            $arrayParamPeriodos   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                      'SOPORTE', 
                                                                      '',
                                                                      'TIPO AFECTACION',
                                                                      $objParametrosDet->strValor1, 
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $objRequest->getSession()->get('idEmpresa'));

                            if(!empty($arrayParamPeriodos))
                            {
                                $strValor2 = $arrayParamPeriodos['valor2'];
                            }
                            else
                            {
                                 throw new \Exception('Tipo de Afectación enviada no es válido');
                            }
                        }    

                        //Se determina si el parametro FIJO es configurable como multiple opcion y si este se permite agregar mas de una vez
                        $strEsMulitple = '';//S o null
                        $strTipoOpcion = '';//T o C

                        $arrayEsMutiple   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                      'SOPORTE', 
                                                                      '',
                                                                      'PARAMETROS DET MULTIPLES',
                                                                      $objParametrosDet->strDescripcion, 
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $objRequest->getSession()->get('idEmpresa'));

                        if(!empty($arrayEsMutiple))
                        {
                            $strEsMulitple = $arrayEsMutiple['valor2'];
                            $strTipoOpcion = $arrayEsMutiple['valor3'];
                        }

                        $objAdmiParametroDet = new AdmiParametroDet();
                        $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                        $objAdmiParametroDet->setDescripcion(trim($objParametrosDet->strDescripcion));
                        $objAdmiParametroDet->setValor1(trim($objParametrosDet->strValor1));
                        $objAdmiParametroDet->setValor2(trim($strValor2));
                        $objAdmiParametroDet->setValor3(trim($strTipoOpcion));
                        $objAdmiParametroDet->setValor4(trim($strEsMulitple));
                        $objAdmiParametroDet->setEmpresaCod($strEmpresaCod);
                        $objAdmiParametroDet->setEstado("Activo");
                        $objAdmiParametroDet->setUsrCreacion($objSession->get('user'));
                        $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                        $objAdmiParametroDet->setIpCreacion($objRequest->getClientIp());                    

                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();

                    }                       
                 
                endforeach;                
                $emGeneral->getConnection()->commit();
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus('Se realizó el proceso con éxito.');
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se encontró la cabecera del parámetro.');
            }
        }
        catch(\Exception $obj)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiMotivosNCIndisponibilidad:creaMotivoParametroDetAjaxAction',
                                      $obj->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     );
            
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' '.$obj->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }     
    
 
    /**
     * actualizarDetalleAjaxAction, actualiza parametros/motivos adicionales
     * MOTIVOS POR NC POR INDISPONIBILIDAD 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_458-1")
     */
    public function actualizarDetalleAjaxAction()
    {
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $emSoporte                             = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceUtil                           = $this->get('schema.Util');
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $arrayJsonData = json_decode($objRequest->get('data'));
            
            foreach($arrayJsonData as $objJsonData):
                                
                $strValor2 = '';
            
                $objAdmiParametroDetSearch = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($objJsonData->intIdParametroDet);
            
                if(is_object($objAdmiParametroDetSearch))
                {
                    if($objAdmiParametroDetSearch->getDescripcion()=='TIPO DE PERIODO')
                    {
                        $arrayParamPeriodos   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                  'SOPORTE', 
                                                                  '',
                                                                  'PERIODOS',
                                                                  $objJsonData->strValor1, 
                                                                  '',
                                                                  '',
                                                                  '', 
                                                                  '', 
                                                                  $objRequest->getSession()->get('idEmpresa'));
                        
                        if(!empty($arrayParamPeriodos))
                        {
                            $strValor2 = $arrayParamPeriodos['valor2'];
                        }
                        else
                        {
                             throw new \Exception('Tipo de Período enviado no es válido');
                        }
                    }       
                    else if($objAdmiParametroDetSearch->getDescripcion()=='TIPO AFECTACION')
                    {
                        $arrayParamPeriodos   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS GENERALES PARA NC INDISPONIBILIDAD', 
                                                                  'SOPORTE', 
                                                                  '',
                                                                  'TIPO AFECTACION',
                                                                  $objJsonData->strValor1, 
                                                                  '',
                                                                  '',
                                                                  '', 
                                                                  '', 
                                                                  $objRequest->getSession()->get('idEmpresa'));
                        
                        if(!empty($arrayParamPeriodos))
                        {
                            $strValor2 = $arrayParamPeriodos['valor2'];
                        }
                        else
                        {
                             throw new \Exception('Tipo de Afectación enviada no es válido');
                        }
                    }    
                    else if($objAdmiParametroDetSearch->getDescripcion()=='TIPO CASO')
                    {
                        $objTipoCaso   = $emSoporte->getRepository('schemaBundle:AdmiTipoCaso')->findOneBy(array('nombreTipoCaso' => $objJsonData->strValor1,
                                                                                                                 'estado'         => 'Activo'));
                              
                        if(is_object($objTipoCaso))
                        {
                            $strValor2 = $objTipoCaso->getId();
                        }
                        else
                        {
                             throw new \Exception('Tipo de Caso envíado no es válido');
                        }
                    }
                    else if($objAdmiParametroDetSearch->getDescripcion()=='MOTIVO DE INDISPONIBILIDAD PARA NC')
                    {
                         $objHipotesis   = $emSoporte->getRepository('schemaBundle:AdmiHipotesis')->findOneBy(array('nombreHipotesis' => $objJsonData->strValor1,
                                                                                                                   'estado'          => array('Activo','Modificado'),
                                                                                                                   'empresaCod'      => $objRequest->getSession()->get('idEmpresa')
                                                                                                                  ));
                              
                        if(is_object($objHipotesis))
                        {
                            $strValor2 = $objHipotesis->getId();
                        }
                        else
                        {
                             throw new \Exception('Motivo enviado no es válido');
                        }
                    }                                        
                                        
                    $objAdmiParametroDetSearch->setValor1(trim($objJsonData->strValor1));
                    $objAdmiParametroDetSearch->setValor2(trim($strValor2));
                    $objAdmiParametroDetSearch->setUsrUltMod($objSession->get('user'));
                    $objAdmiParametroDetSearch->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroDetSearch->setIpUltMod($objRequest->getClientIp()); 
                    $emGeneral->persist($objAdmiParametroDetSearch);
                    $emGeneral->flush();
                }
                                
            endforeach;
                        
            $emGeneral->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus('Se realizó el proceso con éxito.');
            
        } 
        catch (\Exception $obj) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiMotivosNCIndisponibilidad:actualizarDetalleAjaxAction',
                                      $obj->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     );
            
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' '.$obj->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * deleteDetalleAjaxAction, elimina parametros/motivos adicionales
     * MOTIVOS POR NC POR INDISPONIBILIDAD 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-04-2021
     * 
     * @return json con el total de registros y un array que contiene la información del parámetro.
     *
     * @Secure(roles="ROLE_458-1")
     */
    public function deleteDetalleAjaxAction()
    {
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $serviceUtil                           = $this->get('schema.Util');
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {            
            $intDetalle = $objRequest->get('data');
            
            $objAdmiParametroDetSearch = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($intDetalle);
            
            if(is_object($objAdmiParametroDetSearch))
            {
                $objAdmiParametroDetSearch->setEstado('Eliminado');
                $objAdmiParametroDetSearch->setUsrUltMod($objSession->get('user'));
                $objAdmiParametroDetSearch->setFeUltMod(new \DateTime('now'));
                $objAdmiParametroDetSearch->setIpUltMod($objRequest->getClientIp()); 
                $emGeneral->persist($objAdmiParametroDetSearch);
                $emGeneral->flush();
            }
                        
            $emGeneral->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus('Se realizó el proceso con éxito.');
            
        } 
        catch (\Exception $obj) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiMotivosNCIndisponibilidad.deleteDetalleAjaxAction',
                                      $obj->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     );
            
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' '.$obj->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
}