<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\AdmiParametroDetType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiMetasController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_418-1")
    */
    public function indexAction()
    {
    
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_418-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_418-6';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_418-4'))
        {
            $arrayRolesPermitidos[] = 'ROLE_418-4';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_418-8'))
        {
            $arrayRolesPermitidos[] = 'ROLE_418-8';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_418-9'))
        {
            $arrayRolesPermitidos[] = 'ROLE_418-9';
        }

        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");
        $entityAdmiParametro = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->findAll();

        return $this->render('administracionBundle:AdmiMetas:index.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entityAdmiParametro,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    /**
     * @Secure(roles="ROLE_418-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de una base guardada
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     */
    public function showAction($intId)
    {
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");

        if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId))
        {
            throw new NotFoundHttpException('No existe el AdmiMetas que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiMetas:show.html.twig', array(
                                                                                        'item'              => $entityItemMenu,
                                                                                        'caracteristica'    => $objAdmiParametroDet
                                                                                    ));
    }
    
    /**
    * @Secure(roles="ROLE_418-2")
    */
    public function newAction()
    {
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $strTipo             = "crearMeta";
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayValor          = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getVendedores($strTipo,$intIdEmpresa);
        array_push($arrayValor,'meta');
        $entityAdmiParametro = new AdmiParametroDet();
        $objForm             = $this->createForm(new AdmiParametroDetType(array('arrayValor'=>$arrayValor)), $entityAdmiParametro);
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");

        return $this->render('administracionBundle:AdmiMetas:new.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entityAdmiParametro,
            'form'   => $objForm->createView()
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_418-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda la base ingresada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 11-12-2021 - Se modifica para registrar las metas de internet/datos y business solutions.
     * @since 1.1
     */
    public function createAction()
    {
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUserSession      = $objSession->get('user');
        $strDatetimeActual   = new \DateTime('now');
        $strEstadoActivo     = 'Activo';
        $strDescripcion      = 'METAS POR VENDEDOR';
        $strIpCreacion       = $objRequest->getClientIp();
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');	
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");		
        $objAdmiParametroDet = new AdmiParametroDet();
        $boolError           = false;
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        $objForm = $this->createForm(new AdmiParametroDetType(), $objAdmiParametroDet);        
            
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $arrayParametros = $objRequest->get('AdmiParametroDet'); 
            $strVendedores   = $arrayParametros['valor5'];
            $strMRC          = $arrayParametros['valor3'];
            $strNRC          = $arrayParametros['valor4'];
            $strAnio         = $arrayParametros['valor2'];
            $strMes          = $arrayParametros['valor1'];
            $intMetaID       = $arrayParametros['valor6'];   
            $intMetaBs       = $arrayParametros['valor7'];
            
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'DASHBOARD_COMERCIAL',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );

            $objAdmiParametroDet->setParametroId($objAdmiParametroCab);            
            $objAdmiParametroDet->setDescripcion($strDescripcion);            
            $objAdmiParametroDet->setValor1($intMetaID);
            $objAdmiParametroDet->setValor2($intMetaBs);            
            $objAdmiParametroDet->setValor3($strMRC);
            $objAdmiParametroDet->setValor4($strNRC);
            $objAdmiParametroDet->setEstado($strEstadoActivo);            
            $objAdmiParametroDet->setUsrCreacion($strUserSession);
            $objAdmiParametroDet->setFeCreacion($strDatetimeActual);
            $objAdmiParametroDet->setIpCreacion($strIpCreacion);
            $objAdmiParametroDet->setEmpresaCod($intIdEmpresa);
            $objAdmiParametroDet->setValor5($strVendedores);
            $objAdmiParametroDet->setValor6($strMes);
            $objAdmiParametroDet->setValor7($strAnio);
            $objAdmiParametroDet->setObservacion('Valor3=MRC, Valor4=NRC');
            
            $emGeneral->persist($objAdmiParametroDet);                
            $emGeneral->flush();            

            if ($emGeneral->getConnection()->isTransactionActive())
            {                
                $emGeneral->getConnection()->commit();
            }

        }
        catch (Exception $ex)
        {
            $boolError = true;
            
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }
        
        if( $boolError )
        {
            return $this->render('administracionBundle:AdmiMetas:new.html.twig', array(
                                                                                        'item'           => $entityItemMenu,
                                                                                        'caracteristica' => $objAdmiParametroDet,
                                                                                        'form'           => $objForm->createView()
                                                                                       ));
        }
        else
        {            
            return $this->redirect($this->generateUrl('com_admimetas_show', array('intId' => $objAdmiParametroDet->getId())));
        }
    }
    
    /**
     * @Secure(roles="ROLE_418-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Edita la información de la base seleccionada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>    
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 11-12-2021 - Se modifica para registrar las metas de internet/datos y business solutions.
     * @since 1.1 
     */
    public function editAction($intId)
    {        
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");
        $strTipo        = "editarMeta";
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $intIdEmpresa   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayValor      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getVendedores($strTipo,$intIdEmpresa);

        if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId)) 
        {
            throw new NotFoundHttpException('No existe tal registro que se quiere modificar');
        }

        array_push($arrayValor,$objAdmiParametroDet->getValor1() != 'MRC'? $objAdmiParametroDet->getValor1():0);
        array_push($arrayValor,$objAdmiParametroDet->getValor2() != 'NRC'? $objAdmiParametroDet->getValor1():0);
        array_push($arrayValor,$objAdmiParametroDet->getValor6());
        array_push($arrayValor,$objAdmiParametroDet->getValor7());
        array_push($arrayValor,'editarMeta');

        $objFormulario = $this->createForm(new AdmiParametroDetType(array('arrayValor'=>$arrayValor)), $objAdmiParametroDet);
        return $this->render('administracionBundle:AdmiMetas:edit.html.twig', array(
                                                                                        'item'              => $entityItemMenu,
                                                                                        'edit_form'         => $objFormulario->createView(),
                                                                                        'caracteristica'    => $objAdmiParametroDet
                                                                                    ));
    }
    
    /**
     * @Secure(roles="ROLE_418-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de la base seleccionada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 11-12-2021 - Se modifica para registrar las metas de internet/datos y business solutions.
     * @since 1.1
     */
    public function updateAction($intId)
    {
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");        
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");
        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($intId);
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $strUserSession      = $objSession->get('user');
        $strDatetimeActual   = new \DateTime('now');
        $strEstadoActivo     = 'Activo';        
        $strIpCreacion       = $objRequest->getClientIp();
        $boolError           = false;        

        if (!$objAdmiParametroDet)
        {
            throw $this->createNotFoundException('No se ha encontrado caracteristica en nuestra base de datos.');
        }
        $objEditForm = $this->createForm(new AdmiParametroDetType(), $objAdmiParametroDet);
                
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {            
            $arrayParametros = $objRequest->get('AdmiParametroDet');            
            $strMRC          = $arrayParametros['valor3'];
            $strNRC          = $arrayParametros['valor4'];
            $intMetaID       = $arrayParametros['valor6'];
            $intMetaBs       = $arrayParametros['valor7'];

            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'DASHBOARD_COMERCIAL',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );
            $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
            $objAdmiParametroDet->setValor1($intMetaID);
            $objAdmiParametroDet->setValor2($intMetaBs);
            $objAdmiParametroDet->setValor3($strMRC);
            $objAdmiParametroDet->setValor4($strNRC);
            $objAdmiParametroDet->setEstado($strEstadoActivo);     
            $objAdmiParametroDet->setUsrUltMod($strUserSession);
            $objAdmiParametroDet->setFeUltMod($strDatetimeActual);
            $objAdmiParametroDet->setIpCreacion($strIpCreacion);

            $emGeneral->persist($objAdmiParametroDet);                
            $emGeneral->flush();

            if ($emGeneral->getConnection()->isTransactionActive())
            {                
                $emGeneral->getConnection()->commit();
            }
            
        }
        catch (Exception $ex)
        {
            $boolError = true;            
            error_log($ex->getMessage());
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }        
        if( $boolError )
        {
            return $this->render('administracionBundle:AdmiMetas:edit.html.twig', array(
                                                                                        'item'           => $entityItemMenu,
                                                                                        'caracteristica' => $objAdmiParametroDet,
                                                                                        'form'           => $objEditForm->createView()
                                                                                       ));
        }        
        else
        {            
            return $this->redirect($this->generateUrl('com_admimetas_show', array('intId' => $intId)));
        }
    }
    
    /**
     * @Secure(roles="ROLE_418-8")
     * 
     * Documentación para el método 'deleteAction'.
     *
     * Cambia de estado a 'Eliminado' de una base seleccionada
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     */
    public function deleteAction($intId)
    {
        $objRequest         = $this->getRequest();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strDatetimeActual  = new \DateTime('now');
        $strEstadoEliminado = 'Eliminado';
        $boolError          = false;

        $objAdmiParametroDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($intId);

        if (!$objAdmiParametroDet)
        {
            throw $this->createNotFoundException('No se registro seleccionado por el usuario.');
        }
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $objAdmiParametroDet->setEstado($strEstadoEliminado);     
            $objAdmiParametroDet->setUsrUltMod($strUserSession);
            $objAdmiParametroDet->setFeUltMod($strDatetimeActual);
			
            $emGeneral->persist($objAdmiParametroDet);                
            $emGeneral->flush();

            if ($emGeneral->getConnection()->isTransactionActive())
            {                
                $emGeneral->getConnection()->commit();
            }

        }
        catch (Exception $ex)
        {
            $boolError = true;                    
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }

        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }

        if( $boolError )
        {
            return $this->redirect($this->generateUrl('com_admimetas_show', array('intId' => $intId)));
        }
        else
        {
            return $this->redirect($this->generateUrl('com_admimetas'));
        }
    }

    /**
     * @Secure(roles="ROLE_418-9")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Cambia de estado a 'Eliminado' de una base seleccionada
     *
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     */
    public function deleteAjaxAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');        
        $objRequest         = $this->getRequest();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strDatetimeActual  = new \DateTime('now');
        $strEstadoEliminado = 'Eliminado';
        $strParametro       = $objRequest->get('param');
        $arrayParametro     = explode("|",$strParametro);        
        $intCantidadTotal   = count($arrayParametro);
        $emGeneral->getConnection()->beginTransaction();
                
        try
        {
            if( $intCantidadTotal>1 )
            {
                $strMensaje= "¡".$intCantidadTotal." registros eliminados de forma exitosa!";
            }
            else
            {
                $strMensaje="¡El registro se ha eliminado de forma exitosa!";
            }            
            foreach($arrayParametro as $intId)
            {
                if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId))
                {
                    $objRespuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($objAdmiParametroDet->getEstado()) != "eliminado")
                    {
                        $objAdmiParametroDet->setEstado($strEstadoEliminado);     
                        $objAdmiParametroDet->setUsrUltMod($strUserSession);
                        $objAdmiParametroDet->setFeUltMod($strDatetimeActual);

                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();
                                                                    
                    }
                    $objRespuesta->setContent($strMensaje);
                }//(null == $objAdmiParametroDet = $emComercial->find('schemaBundle:AdmiBase', $intId))
            }//foreach($arrayParametro as $intId)
            
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }
        }
        catch (Exception $ex)
        {                    
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }
        
        return $objRespuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_418-7")
    */
    public function gridAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');        
        $objPeticion    = $this->get('request');        
		$objQueryNombre = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $strNombre      = ($objQueryNombre != '' ? $objQueryNombre : $objPeticion->query->get('nombre'));
        $strApellido    = $objPeticion->query->get('apellido');
        $strLogin       = $objPeticion->query->get('login');
        $strMes         = $objPeticion->query->get('mes');        
        $intStart       = $objPeticion->query->get('start');
        $intLimit       = $objPeticion->query->get('limit');
        $arrayParametro = array();

        $arrayParametro['strNombre']   = $strNombre;
        $arrayParametro['strApellido'] = $strApellido;
        $arrayParametro['strMes']      = $strMes;        
        $arrayParametro['strLogin']    = $strLogin;        
        $arrayParametro['intStart']    = $intStart;
        $arrayParametro['intLimit']    = $intLimit;        
        $objJson = $this->getDoctrine()
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:AdmiParametroDet')
                    ->generarJsonMeta($arrayParametro);
        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }    
    
    /**
     * Documentación para el método 'verificarCaracteristicaAction'.
     *
     * Verifica que las bases no hayan sido ingresadas anteriormente.
     * 
     * @return Response 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     */
    public function verificarMetaAction()
    {
        $objResponse   = new Response();
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strMensaje    = 'OK';
        $emComercial   = $this->getDoctrine()->getManager('telconet');
        $strVendedores = $objRequest->request->get('strVendedores') ? $objRequest->request->get('strVendedores') : '';
        $strMes        = $objRequest->request->get('strMes') ? $objRequest->request->get('strMes') : '';
        $strAnio       = $objRequest->request->get('strAnio') ? $objRequest->request->get('strAnio') : '';        
        $intIdEmpresa  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        if( $intIdEmpresa != 0 )
        {
            $objAdmiParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy( array( 'valor5'      => $strVendedores,
                                                                     'valor6'      => $strMes,
                                                                     'valor7'      => $strAnio,
                                                                     'descripcion' => 'METAS POR VENDEDOR',
                                                                     'estado'      => 'Activo' ));   
            if( $objAdmiParametroDet )
            {
                
                $strMensaje = 'Registro existente...';
            }//( $objAdmiParametroDet )
        }
        else
        {
            $strMensaje = 'No tiene existe empresa en sessión';
        }
        
        $objResponse->setContent( $strMensaje );
        
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_418-9")
     * 
     * Documentación para el método 'masivoAjaxAction'.
     *
     * Ingresa de forma masiva las Metas de los vendedores.
     *
     * @version 1.0 Version Inicial
     *
     * @author David León <mdleon@telconet.ec>
     */
    public function masivoAjaxAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        
        $objRequest         = $this->getRequest();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strDatetimeActual  = new \DateTime('now');
        $strParametro       = $objRequest->get('param');
        
        
        $intCantidadTotal   = count($arrayParametros);
        $strEstadoActivo     = 'Activo';
        $strDescripcion      = 'METAS POR VENDEDOR';
        $strIpCreacion       = $objRequest->getClientIp();
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');	
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emGeneral->getConnection()->beginTransaction();
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayParametros = array();
        $arrayDatos      = array();
        $intContador     = 0;
        $strMensaje= "¡Datos Cargados!";
        try
        {            
            foreach (explode("\n", $strParametro) as $line) 
            {
                foreach (str_getcsv($line) as $fields) 
                {
                  array_Push($arrayParametros, $fields);
                }
            }

            if( !is_array($arrayParametros) )
            {
                $strMensaje= "¡El archivo se encuentra vacío, Favor revisar!";
            }
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'DASHBOARD_COMERCIAL',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );
            foreach($arrayParametros as $arrayParametro)
            {
                $intContador++;
                if($intContador ==1 || $arrayParametro=='')
                {
                    continue;
                }
                $arrayDatos = explode(';',$arrayParametro);
                $strVendedor    = $arrayDatos[0];
                $strMes         = $arrayDatos[1];
                $strAno         = $arrayDatos[2];
                $intMetaID      = $arrayDatos[3];
                $intMetaBs      = $arrayDatos[4];
                $strMRC         = $arrayDatos[5];
                $strNRC         = $arrayDatos[6];
                if($strVendedor =='' || $strMes =='' || $strAno =='' || $intMetaID =='' || $intMetaBs =='' || $strMRC =='' || $strNRC =='')
                {
                    throw new \Exception('Los datos ingresados estan incompletos o no es el archivo correcto, favor revisar.');
                }
                $objAdmiParametroDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                   'parametroId'   => $objAdmiParametroCab->getId(),
                                                                   'descripcion'   => $strDescripcion,
                                                                   'estado'        => 'Activo',
                                                                   'valor6'        => $strMes,
                                                                   'valor7'        => $strAno,
                                                                   'valor5'        => $strVendedor
                                                                )
                                                            );
                if (!is_object($objAdmiParametroDet))
                {
                    $objInfoPersona         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->findOneBy(array(
                                                                           'login'   => $strVendedor,
                                                                           'estado'  => 'Activo'
                                                                        )
                                                                    );   
                    if(!is_object($objInfoPersona))
                    {
                        throw new \Exception('El Login '.$strVendedor.' no corresponde a un login activo, favor revisar.');
                    }
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);            
                    $objAdmiParametroDet->setDescripcion($strDescripcion);            
                    $objAdmiParametroDet->setValor1($intMetaID);
                    $objAdmiParametroDet->setValor2($intMetaBs);            
                    $objAdmiParametroDet->setValor3($strMRC);
                    $objAdmiParametroDet->setValor4($strNRC);
                    $objAdmiParametroDet->setEstado($strEstadoActivo);            
                    $objAdmiParametroDet->setUsrCreacion($strUserSession);
                    $objAdmiParametroDet->setFeCreacion($strDatetimeActual);
                    $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                    $objAdmiParametroDet->setEmpresaCod($intIdEmpresa);
                    $objAdmiParametroDet->setValor5($strVendedor);
                    $objAdmiParametroDet->setValor6($strMes);
                    $objAdmiParametroDet->setValor7($strAno);
                    $objAdmiParametroDet->setObservacion('Valor3=MRC, Valor4=NRC');

                    $emGeneral->persist($objAdmiParametroDet);                
                    $emGeneral->flush();            
                }
            }
            
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }
        }
        catch (\Exception $e)
        {
            $strMensaje = $e->getMessage();

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }
        $objRespuesta->setContent($strMensaje);
        return $objRespuesta;
    }
}
