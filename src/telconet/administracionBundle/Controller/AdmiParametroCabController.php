<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \telconet\schemaBundle\Entity\AdmiParametroDet;
use \telconet\schemaBundle\Entity\AdmiParametroCab;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Documentación para el controlador 'AdmiParametroCabController'.
 * AdmiParametroCabController, Contiene los metodos para la administracion de las estructuras ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET.
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 09-09-2015
 */
class AdmiParametroCabController extends Controller
{
    /**
     * indexAction, Redirecciona al index de la administración de parámetros.
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return redireccion al index de la administración de admiParametroCab
     *
     * @Secure(roles="ROLE_300-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_300-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_300-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_300-3'))
        {
            $arrayRolesPermitidos[] = 'ROLE_300-3'; //Rol Create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_300-5'))
        {
            $arrayRolesPermitidos[] = 'ROLE_300-5'; //Rol Update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_300-9'))
        {
            $arrayRolesPermitidos[] = 'ROLE_300-9'; //Rol Delete
        }
        return $this->render('administracionBundle:AdmiParametroCab:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }//indexAction

    /**
     * getListadoParametrosCabAjaxAction, Obtiene los parámetros de la estructura ADMI_PARAMETRO_CAB según los filtros enviados en el request
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_300-1")
     */
    public function getListadoParametrosCabAjaxAction()
    {
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['intStart']            = $objRequest->get('start');
        $arrayParametros['intLimit']            = $objRequest->get('limit');
        $arrayParametros['strNombreParametro']  = $objRequest->get('strNombreParametro');
        $arrayParametros['strDescripcion']      = $objRequest->get('strDescripcion');
        $arrayParametros['strModulo']           = $objRequest->get('strModulo');
        $arrayParametros['strProceso']          = $objRequest->get('strProceso');
        $arrayParametros['strUsrCreacion']      = $objRequest->get('strUsrCreacion');
        $arrayParametros['strEstado']           = $objRequest->get('strEstado');
        
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
                                                       'strFeCreacion'      => $arrayAdmiParamCab['strFeCreacion']->format('d-M-Y'),
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
     * getListadoParametrosDetAjaxAction, Obtiene los parametros de la estructura ADMI_PARAMETRO_DET según los filtros enviados en el request
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * @return json con el total de registros y un array formado con la data obtenida
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * Se agrega a los filtros de búsqueda el Código de la Empresa
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 17-01-2017    Se agrega validación para poder buscar detalles de parametros enviando el
     *                            nombre de la cabecera como parametro de la funcion
     * @since 1.1
     * 
     * @Secure(roles="ROLE_300-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                            = $this->getRequest();
        $arrayParametros                       = array();
        $arrayParametros['intStart']           = $objRequest->get('start');
        $arrayParametros['intLimit']           = $objRequest->get('limit');
        $arrayParametros['intIdParametroCab']  = $objRequest->get('intIdParametroCab');
        $arrayParametros['strDescripcionDet']  = $objRequest->get('strDescripcionDet');
        $arrayParametros['strValor1']          = $objRequest->get('strValor1');
        $arrayParametros['strValor2']          = $objRequest->get('strValor2');
        $arrayParametros['strValor3']          = $objRequest->get('strValor3');
        $arrayParametros['strValor4']          = $objRequest->get('strValor4');
        $arrayParametros['strUsrCreacion']     = $objRequest->get('strUsrCreacion');
        $arrayParametros['strEstado']          = $objRequest->get('strEstado');
        $arrayParametros['strBuscaCabecera']   = $objRequest->get('strBuscaCabecera');
        $arrayParametros['strNombreParametro'] = $objRequest->get('strNombreParametro');
        $arrayParametros['strEmpresaCod']      = $objRequest->getSession()->get('idEmpresa');
        /* se agrega validación para poder buscar detalles de parametros enviando como parametro el 
           nombre de la cabecera de los parametros */
        if(!empty($arrayParametros['strBuscaCabecera']))
        {
            if ($arrayParametros['strBuscaCabecera'] == "SI")
            {
                if(!empty($arrayParametros['strNombreParametro']))
                {
                    $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametro'], 'estado' => "Activo");
                    $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
                    if (is_object($objAdmiParametroCab))
                    {
                        $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
                    }
                }
            }
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
                                                       'strFeCreacion'     => $arrayAdmiParamDet['strFeCreacion']->format('d-M-Y'));
            endforeach;
        }
        $objResponse = new Response(json_encode(array('jsonAdmiParametroDetResult' => $arrayAdmiParametroDetResult,
                                                      'intTotalParametros'         => $arrayAdmiParametroDet['intTotal'],
                                                      'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getListadoParametrosDetAjaxAction

    /**
     * creaParametroCabAjaxAction, Crea registros en la ADMI_PARAMETRO_CAB y ADMI_PARAMETRO_DET
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return json con un código de estatus y un mensaje de la acción realizada
     *
     * @Secure(roles="ROLE_300-3")
     */
    public function creaParametroCabAjaxAction()
    {
        $objRequest                              = $this->getRequest();
        $objSession                              = $objRequest->getSession();
        $objReturnResponse                       = new ReturnResponse();
        $emGeneral                               = $this->getDoctrine()->getManager("telconet_general");
        $objParametrosDet                        = json_decode($objRequest->get('jsonCreaParametrosDet'));
        $arrayParametros['strNombreParametroCab']= $objRequest->get('strNombreParametroCab');
        $arrayParametros['strDescripcionCab']    = $objRequest->get('strDescripcionCab');
        $arrayParametros['strModuloCab']         = $objRequest->get('strModuloCab');
        $arrayParametros['strProcesoCab']        = $objRequest->get('strProcesoCab');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que el nombre del parámetro no se enviado vacío, caso contrario crea una excepción.
            if(empty($arrayParametros['strNombreParametroCab']))
            {
                throw new \Exception('No se está enviando el nombre del parámetro.');
            }
            
            //Valida que el parámetro no este repetido, caso contrario crea una excepción.
            if(true === $this->validaParametroCab($arrayParametros))
            {
                throw new \Exception('El parámetro ya existe.');
            }
            
            //Instacia un nuevo objeto de la entidad AdmiParametroCab
            $entityAdmiParametroCab = new AdmiParametroCab();
            $entityAdmiParametroCab->setDescripcion($arrayParametros['strDescripcionCab']);
            $entityAdmiParametroCab->setNombreParametro($arrayParametros['strNombreParametroCab']);
            $entityAdmiParametroCab->setModulo($arrayParametros['strModuloCab']);
            $entityAdmiParametroCab->setProceso($arrayParametros['strProcesoCab']);
            $entityAdmiParametroCab->setEstado('Activo');
            $entityAdmiParametroCab->setFeCreacion(new \DateTime('now'));
            $entityAdmiParametroCab->setUsrCreacion($objSession->get('user'));
            $entityAdmiParametroCab->setIpCreacion($objRequest->getClientIp());
            $emGeneral->persist($entityAdmiParametroCab);
            $emGeneral->flush();
            
            //Si el json obtenido de $objParametrosDet contiene datos lo itera y crea registros en la entidad AdmiParametroDet
            foreach($objParametrosDet->arrayData as $objParametrosDet):
                //Instacia un nuevo objeto de la entidad AdmiParametroDet 
                $entityAdmiParametroDet = new AdmiParametroDet();
                $entityAdmiParametroDet->setParametroId($entityAdmiParametroCab);
                $entityAdmiParametroDet->setDescripcion(trim($objParametrosDet->strDescripcion));
                $entityAdmiParametroDet->setValor1(trim($objParametrosDet->strValor1));
                $entityAdmiParametroDet->setValor2(trim($objParametrosDet->strValor2));
                $entityAdmiParametroDet->setValor3(trim($objParametrosDet->strValor3));
                $entityAdmiParametroDet->setValor4(trim($objParametrosDet->strValor4));
                $entityAdmiParametroDet->setEstado("Activo");
                $entityAdmiParametroDet->setUsrCreacion($objSession->get('user'));
                $entityAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                $entityAdmiParametroDet->setIpCreacion($objRequest->getClientIp());                
                $emGeneral->persist($entityAdmiParametroDet);
                $emGeneral->flush();
            endforeach;
            $emGeneral->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch (\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR." ". $ex->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }//creaParametroCabAjaxAction

    /**
     * creaParametroDetAjaxAction, Crea registro en la ADMI_PARAMETRO_DET
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * @return json con un código de estatus y un mensaje de acción realizada
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * Se inserta el código de la empresa en la creación del detalle del parámetro
     * 
     * @Secure(roles="ROLE_300-3")
     */
    public function creaParametroDetAjaxAction()
    {
        $objRequest                            = $this->getRequest();
        $objSession                            = $objRequest->getSession();
        $objReturnResponse                     = new ReturnResponse();
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros['intIdParametroCab']  = $objRequest->get('intParametroCab');
        $arrayParametros['strDescripcion']     = $objRequest->get('strDescripcion');
        $arrayParametros['strValor1']          = $objRequest->get('strValor1');
        $arrayParametros['strValor2']          = $objRequest->get('strValor2');
        $arrayParametros['strValor3']          = $objRequest->get('strValor3');
        $arrayParametros['strValor4']          = $objRequest->get('strValor4');
        $arrayParametros['strEmpresaCod']      = $objSession->get('idEmpresa');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $arrayParametros['intIdParametroCab'] haya sido enviado en el request.
            if(!empty($arrayParametros['intIdParametroCab']))
            {
                //Busca el parámetro enviado en el request $arrayParametros['intIdParametroCab'].
                $entityAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->find($arrayParametros['intIdParametroCab']);
                
                //Valida que el objeto no sea nulo.
                if($entityAdmiParametroCab)
                {
                    //Setea el objeto en el elemento intIdParametroCab para realizar la busqueda en el metodo validaParametroDet.
                    $arrayParametros['intIdParametroCab'] = $entityAdmiParametroCab;
                    
                    //Valida que el parámetro no exista en la base.
                    if(true === $this->validaParametroDet($arrayParametros))
                    {
                        throw new \Exception('El parámetro ya existe.');
                    }
                    //Crea una instancia del objeto AdmiParametroDet
                    $entityAdmiParametroDet = new AdmiParametroDet();
                    $entityAdmiParametroDet->setParametroId($entityAdmiParametroCab);
                    $entityAdmiParametroDet->setDescripcion(trim($arrayParametros['strDescripcion']));
                    $entityAdmiParametroDet->setValor1(trim($arrayParametros['strValor1']));
                    $entityAdmiParametroDet->setValor2(trim($arrayParametros['strValor2']));
                    $entityAdmiParametroDet->setValor3(trim($arrayParametros['strValor3']));
                    $entityAdmiParametroDet->setValor4(trim($arrayParametros['strValor4']));
                    $entityAdmiParametroDet->setEmpresaCod($arrayParametros['strEmpresaCod']);
                    $entityAdmiParametroDet->setEstado("Activo");
                    $entityAdmiParametroDet->setUsrCreacion($objSession->get('user'));
                    $entityAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                    $entityAdmiParametroDet->setIpCreacion($objRequest->getClientIp());
                    $emGeneral->persist($entityAdmiParametroDet);
                    $emGeneral->flush();
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
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se está enviando la cabecera del parámetro.');
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
    }//creaParametroDetAjaxAction

    /**
     * actualizaParametroCabAjaxAction, actualiza un registro en la ADMI_PARAMETRO_CAB
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     *
     * @Secure(roles="ROLE_300-5")
     */
    public function actualizaParametroCabAjaxAction()
    {
        $objRequest                              = $this->getRequest();
        $objSession                              = $objRequest->getSession();
        $objReturnResponse                       = new ReturnResponse();
        $emGeneral                               = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros['intIdParametroCab']    = $objRequest->get('intIdParametro');
        $arrayParametros['strNombreParametroCab']= $objRequest->get('strNombreParametro');
        $arrayParametros['strDescripcionCab']    = $objRequest->get('strDescripcion');
        $arrayParametros['strModuloCab']         = $objRequest->get('strModulo');
        $arrayParametros['strProcesoCab']        = $objRequest->get('strProceso');
        $strActualizaSoloDescripcion             = $objRequest->get('strActualizaSoloDescripcion');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $arrayParametros['intIdParametroCab'] haya sido enviado en el request.
            if(!empty($arrayParametros['intIdParametroCab']))
            {
                
                //Busca el parámetro enviado en el request $arrayParametros['intIdParametroCab'] en la entidad AdmiParametroCab.
                $entityAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->find($arrayParametros['intIdParametroCab']);
                
                //Valida que el objeto no sea nulo.
                if($entityAdmiParametroCab)
                {
                    /*Valida que el parámetro exista en la base y si la actualización es solo la descripción
                     * Lanza la excepción cuando el parámetro existe y cuando no solo se envia a actualizar la descripción del parámetro.
                     */
                    if(true == $this->validaParametroCab($arrayParametros) && "NO" === $strActualizaSoloDescripcion)
                    {
                        throw new \Exception('El parámetro ya existe.');
                    }
                    
                    $entityAdmiParametroCab->setDescripcion(trim($arrayParametros['strDescripcionCab']));
                    
                    //Actualiza cuando no solo se ha enviado a actualizar la descripción del parámetro.
                    if("NO" === $strActualizaSoloDescripcion)
                    {    
                        $entityAdmiParametroCab->setNombreParametro(trim($arrayParametros['strNombreParametroCab']));
                        $entityAdmiParametroCab->setModulo(trim($arrayParametros['strModuloCab']));
                        $entityAdmiParametroCab->setProceso(trim($arrayParametros['strProcesoCab']));
                    }

                    $entityAdmiParametroCab->setUsrUltMod($objSession->get('user'));
                    $entityAdmiParametroCab->setFeUltMod(new \DateTime('now'));
                    $entityAdmiParametroCab->setIpUltMod($objRequest->getClientIp());
                    $emGeneral->persist($entityAdmiParametroCab);
                    $emGeneral->flush();
                    $emGeneral->getConnection()->commit();
                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
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
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR.' '.$ex->getMessage());
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }//actualizaParametroCabAjaxAction

    /**
     * actualizaParametroDetAjaxAction, actualiza un registro en la ADMI_PARAMETRO_DET
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * Se inserta el código de la empresa en la actualización del detalle del parámetro
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     *
     * @Secure(roles="ROLE_300-5")
     */
    public function actualizaParametroDetAjaxAction()
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
        $arrayParametros['strEmpresaCod']      = $objSession->get('idEmpresa');
        $strActualizaSoloDescripcion           = $objRequest->get('strActualizaSoloDescripcion');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $arrayParametros['intIdParametroDet'] haya sido enviado en el request.
            if(!empty($arrayParametros['intIdParametroDet']))
            {
                
                //Busca el parámetro enviado en el request $arrayParametros['intIdParametroDet'] en la entidad AdmiParametroDet.
                $entityAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($arrayParametros['intIdParametroDet']);
                
                //Valida que el objeto no sea nulo.
                if($entityAdmiParametroDet)
                {
                    //Setea el objeto en el elemento intIdParametroCab para realizar la busqueda en el metodo validaParametroDet.
                    $arrayParametros['intIdParametroCab']  = $entityAdmiParametroDet->getParametroId();
                    
                    /*Valida que el parámetro exista en la base y si la actualización es solo la descripción
                     * Lanza la excepción cuando el parámetro existe y cuando no solo se envia a actualizar la descripción del parámetro.
                     */
                    if(true == $this->validaParametroDet($arrayParametros) && "NO" === $strActualizaSoloDescripcion)
                    {
                        throw new \Exception('El parámetro ya existe.');
                    }

                    $entityAdmiParametroDet->setDescripcion(trim($arrayParametros['strDescripcion']));
                    
                    //Actualiza cuando no solo se ha enviado a actualizar la descripción del parámetro.
                    if("NO" === $strActualizaSoloDescripcion)
                    {
                        $entityAdmiParametroDet->setValor1(trim($arrayParametros['strValor1']));
                        $entityAdmiParametroDet->setValor2(trim($arrayParametros['strValor2']));
                        $entityAdmiParametroDet->setValor3(trim($arrayParametros['strValor3']));
                        $entityAdmiParametroDet->setValor4(trim($arrayParametros['strValor4']));
                    }

                    $entityAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                    $entityAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $entityAdmiParametroDet->setIpUltMod($objRequest->getClientIp());
                    $emGeneral->persist($entityAdmiParametroDet);
                    $emGeneral->flush();
                    $emGeneral->getConnection()->commit();
                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);

                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR.' No se encontró el parámetro en la base.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR.' No está enviando el parámetro.');
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
    }//actualizaParametroDetAjaxAction
    
    /**
     * eliminarParametroCabAjaxAction, Cambia el estado de Activo a Eliminado de la tabla ADMI_PARAMETRO_CAB
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     *
     * @Secure(roles="ROLE_300-9")
     */
    public function eliminarParametroCabAjaxAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $intIdParametroCab = $objRequest->get('intParametroCab');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $intIdParametroCab haya sido enviado en el request.
            if(!empty($intIdParametroCab))
            {
                //Busca el parámetro enviado en el request $entityAdmiParametroCab en la entidad AdmiParametroCab.
                $entityAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->find($intIdParametroCab);

                //Si el objeto éxiste realiza el cambio de estado.
                if($entityAdmiParametroCab)
                {
                    $entityAdmiParametroCab->setEstado("Eliminado");
                    $entityAdmiParametroCab->setUsrUltMod($objSession->get('user'));
                    $entityAdmiParametroCab->setFeUltMod(new \DateTime('now'));
                    $entityAdmiParametroCab->setIpUltMod($objRequest->getClientIp());
                    $emGeneral->persist($entityAdmiParametroCab);
                    $emGeneral->flush();

                    //Obtiene los parámetros de la entidad AdmiParametroDet buscados por la refencia $entityAdmiParametroCab->getId()
                    $entityAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array('parametroId' => $entityAdmiParametroCab->getId()));

                    //Itera los paraámetros obtenidos en $entityAdmiParametroDet para setearlos con estado Eliminado.
                    foreach($entityAdmiParametroDet as $objAdmiParametroDet):
                        $objAdmiParametroDet->setEstado('Eliminado');
                        $objAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                        $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                        $objAdmiParametroDet->setIpUltMod($objRequest->getClientIp());
                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();
                    endforeach;
                    $emGeneral->getConnection()->commit();
                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se encontró el parámetro en la base.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se está enviando el parámetro.');
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
    }//eliminarParametroCabAjaxAction
    
    /**
     * eliminarParametroDetAjaxAction, Cambia el estado de Activo a Eliminado de la tabla ADMI_PARAMETRO_DET
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se cambia el código del módulo 312 a 300
     * 
     * @return json con un código de estatus y un mensaje de acción realizada
     *
     * @Secure(roles="ROLE_300-9")
     */
    public function eliminarParametroDetAjaxAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $intIdParametroDet = $objRequest->get('intIdParametroDet');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $intIdParametroDet haya sido enviado en el request.
            if(!empty($intIdParametroDet))
            {

                //Busca el parámetro enviado en el request $entityAdmiParametroCab.
                $entityAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($intIdParametroDet);

                //Si el objeto éxiste realiza el cambio de estado.
                if($entityAdmiParametroDet)
                {
                    //Cambia el estado del parámetro de Activo a Eliminado.
                    $entityAdmiParametroDet->setEstado('Eliminado');
                    $entityAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                    $entityAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $entityAdmiParametroDet->setIpUltMod($objRequest->getClientIp());
                    $emGeneral->persist($entityAdmiParametroDet);
                    $emGeneral->flush();
                    $emGeneral->getConnection()->commit();
                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se encontró el parámetro en la base.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se está enviando el parámetro.');
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
    }//eliminarParametroDetAjaxAction
    
    /**
     * validaParametroCab, busca el parametro en la ADMI_PARAMETRO_CAB con los filtros envíados
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * @param array $arrayParametroDet[
                                        'strNombreParametroCab' => campo nombreParametro de estructura ADMI_PARAMETRO_CAB,
                                        'strModuloCab'          => campo modulo de estructura ADMI_PARAMETRO_CAB,
                                        'strProcesoCab'         => campo proceso de estructura ADMI_PARAMETRO_CAB
                                      ]
     * @return boolean con true si encontro el parametro, false caso contrario
     */
    private function validaParametroCab($arrayParametroCab)
    {
        $emGeneral                                  = $this->getDoctrine()->getManager("telconet_general");
        $boolExiste                                 = false;
        $arrayParametroCab['strNombreParametroCab'] = trim($arrayParametroCab['strNombreParametroCab']);
        $arrayParametroCab['strModuloCab']          = trim($arrayParametroCab['strModuloCab']);
        $arrayParametroCab['strProcesoCab']         = trim($arrayParametroCab['strProcesoCab']);

        /*Pregunta si el elemento strNombreParametroCab está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroCab['strNombreParametroCab']))
        {
            $arrayParametroCab['strNombreParametroCab'] = null;
        }

        /*Pregunta si el elemento strModuloCab está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroCab['strModuloCab']))
        {
            $arrayParametroCab['strModuloCab'] = null;
        }

        /*Pregunta si el elemento strProcesoCab está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroCab['strProcesoCab']))
        {
            $arrayParametroCab['strProcesoCab'] = null;
        }

        //Se realiza la busqueda en la entidad AdmiParametroCab con los parámetros enviados
        $entityAdmiParametroCabSearch = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findBy(array('nombreParametro' => $arrayParametroCab['strNombreParametroCab'],
                                                                 'modulo'          => $arrayParametroCab['strModuloCab'],
                                                                 'proceso'         => $arrayParametroCab['strProcesoCab'],
                                                                 'estado'          => 'Activo'));

        //Si el objeto no es vacío retorna true
        if($entityAdmiParametroCabSearch)
        {
            $boolExiste = true;
        }
        return $boolExiste;
    }//validaParametroCab
    
    /**
     * validaParametroDet, busca el parametro en la ADMI_PARAMETRO_DET con los filtros envíados
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * @param array $arrayParametroDet[
                                        'strValor1'     => campo valor1 de estructura ADMI_PARAMETRO_DET,
                                        'strValor2'     => campo valor2 de estructura ADMI_PARAMETRO_DET,
                                        'strValor3'     => campo valor3 de estructura ADMI_PARAMETRO_DET,
                                        'strValor4'     => campo valor4 de estructura ADMI_PARAMETRO_DET,
                                        'strEmpresaCod' => campo empresaCod de estructura ADMI_PARAMETRO_DET,
                                      ]
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se agrega el código de la empresa 'strEmpresaCod' para la validación
     * 
     * @return boolean con true si encontro el parametro, false caso contrario
     */
    private function validaParametroDet($arrayParametroDet)
    {
        $emGeneral                      = $this->getDoctrine()->getManager("telconet_general");
        $boolExiste                     = false;
        $arrayParametroDet['strValor1'] = trim($arrayParametroDet['strValor1']);
        $arrayParametroDet['strValor2'] = trim($arrayParametroDet['strValor2']);
        $arrayParametroDet['strValor3'] = trim($arrayParametroDet['strValor3']);
        $arrayParametroDet['strValor4'] = trim($arrayParametroDet['strValor4']);
        
        $arrayParametroDet['strEmpresaCod'] = trim($arrayParametroDet['strEmpresaCod']);
        
        /*Pregunta si el elemento strValor1 está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroDet['strValor1']))
        {
            $arrayParametroDet['strValor1'] = null;
        }
        
        /*Pregunta si el elemento strValor2 está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroDet['strValor2']))
        {
            $arrayParametroDet['strValor2'] = null;
        }
        
        /*Pregunta si el elemento strValor3 está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroDet['strValor3']))
        {
            $arrayParametroDet['strValor3'] = null;
        }
        
        /*Pregunta si el elemento strValor4 está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroDet['strValor4']))
        {
            $arrayParametroDet['strValor4'] = null;
        }
        
        /*Pregunta si el elemento strEmpresaCod está vacío para setearlo en null.
         * Se realiza esto para que el doctrine cree la clausa la where [param => null] = null 
         */
        if(empty($arrayParametroDet['strEmpresaCod']))
        {
            $arrayParametroDet['strEmpresaCod'] = null;
        }
        
        //Se realiza la busqueda en la entidad AdmiParametroDet con los parámetros enviados
        $entityAdmiParametroDetSearch = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findBy(array('parametroId' => $arrayParametroDet['intIdParametroCab'],
                                                                 'valor1'      => $arrayParametroDet['strValor1'],
                                                                 'valor2'      => $arrayParametroDet['strValor2'],
                                                                 'valor3'      => $arrayParametroDet['strValor3'],
                                                                 'valor4'      => $arrayParametroDet['strValor4'],
                                                                 'empresaCod'  => $arrayParametroDet['strEmpresaCod'],
                                                                 'estado'      => 'Activo'));

        //Si el objeto no es vacío retorna true
        if($entityAdmiParametroDetSearch)
        {
            $boolExiste = true;
        }
        return $boolExiste;
    }//validaParametroDet
    
    /**
     * validarDisponibilidadOpcionPorHoraAjaxAction
     * 
     * Valida la disponibilidad de una opcion segun la hora de Inicio y Fin parametrizada, esta hora debe estar almacenada en los parametros
     * con formato de 24 horas, ej: inicio: 15:00:00 fin: 17:00:00
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 23-03-2017
     * 
     * @return JsonResponse [
     *                        'strPermiteAcceso' 
     *                        'strHoraInicio' 
     *                        'strHoraFin'
     *                      ]
     */
    public function validarDisponibilidadOpcionPorHoraAjaxAction()
    {
        $objResponse                             = new JsonResponse();
        $emGeneral                               = $this->getDoctrine()->getManager("telconet_general");
        $objPeticion                             = $this->get('request');
        $strOpcionTelcos                         = $objPeticion->get('opcionTelcos');
        $strPermiteAcceso                        = "NO";
        $strHoras                                = "";
        $strMinutos                              = "";
        $strSegundos                             = "";
        $strHmsInicio                            = "";
        $strHmsFin                               = "";
        $arrayRespuesta                          = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('TIEMPO_PERMITIDO_OPCION', 
                                                                      '', 
                                                                      '', 
                                                                      $strOpcionTelcos, 
                                                                      '', 
                                                                      '', 
                                                                      '', 
                                                                      '');
        
        if (is_array($arrayRespuesta))
        {
            $strHmsInicio     = !empty($arrayRespuesta['valor1'])?$arrayRespuesta['valor1']:"";
            $strHmsFin        = !empty($arrayRespuesta['valor2'])?$arrayRespuesta['valor2']:"";
            $strHmsReferencia = date('G:i:s');
            
            list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsInicio), 3, 0);
            $intSegundosInicio                  = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
            list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsFin), 3, 0);
            $intSegundosFin                     = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
            list($strHoras, $strMinutos, $strSegundos) = array_pad(preg_split('/[^\d]+/', $strHmsReferencia), 3, 0);
            $intSegundoReferencia               = 3600 * $strHoras + 60 * $strMinutos + $strSegundos;
            if($intSegundosInicio <= $intSegundosFin)
            {
                if($intSegundoReferencia >= $intSegundosInicio && $intSegundoReferencia <= $intSegundosFin)
                {
                    $strPermiteAcceso = "SI";
                }
                else
                {
                    $strPermiteAcceso = "NO";
                }
            }
            else
            {
                if($intSegundoReferencia >= $intSegundosInicio || $intSegundoReferencia <= $intSegundosFin)
                {
                    $strPermiteAcceso = "SI";
                }
                else
                {
                    $strPermiteAcceso = "NO";
                }
            }
        }

        $objResponse->setData( array('strPermiteAcceso' => $strPermiteAcceso,
                                     'strHoraInicio'    => $strHmsInicio,
                                     'strHoraFin'       => $strHmsFin) );
        
        return $objResponse;
    }
}
