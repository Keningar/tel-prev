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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiBasesController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_417-1")
    */
    public function indexAction()
    {
    
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_417-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_417-6';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_417-4'))
        {
            $arrayRolesPermitidos[] = 'ROLE_417-4';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_417-8'))
        {
            $arrayRolesPermitidos[] = 'ROLE_417-8';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_417-9'))
        {
            $arrayRolesPermitidos[] = 'ROLE_417-9';
        }

        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");
        $entityAdmiParametro         = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->findAll();

        return $this->render('administracionBundle:AdmiBases:index.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entityAdmiParametro,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    /**
     * @Secure(roles="ROLE_417-6")
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
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $entityItemMenu    = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");

        if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId))
        {
            throw new NotFoundHttpException('No existe el AdmiBase que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiBases:show.html.twig', array(
                                                                                        'item'              => $entityItemMenu,
                                                                                        'caracteristica'    => $objAdmiParametroDet
                                                                                    ));
    }
    
    /**
    * @Secure(roles="ROLE_417-2")
    */
    public function newAction()
    {
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $strTipo             = "crear";
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;        
        $arrayValor           = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getVendedores($strTipo,$intIdEmpresa);         
        array_push($arrayValor,'crearBase');
        $entityAdmiParametro = new AdmiParametroDet();
        $objForm                = $this->createForm(new AdmiParametroDetType(array('arrayValor'=>$arrayValor)), $entity);
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");

        return $this->render('administracionBundle:AdmiBases:new.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entityAdmiParametro,
            'form'   => $objForm->createView()
        ));
    }    
    
    /**
     * @Secure(roles="ROLE_417-3")
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
     * @version 1.1 11-12-2021 - Se modifica para registrar las bases de internet/datos y business solutions.
     * @since 1.1
     */
    public function createAction()
    {
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUserSession      = $objSession->get('user');
        $strDatetimeActual   = new \DateTime('now');
        $strEstadoActivo     = 'Activo';
        $strDescripcion      = 'BASES POR VENDEDOR';
        $strIpCreacion       = $objRequest->getClientIp();
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');	
        $emComercial         = $this->getDoctrine()->getManager('telconet');	
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");		
        $objAdmiParametroDet = new AdmiParametroDet();
        $boolError           = false;
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        $objForm = $this->createForm(new AdmiParametroDetType(), $objAdmiParametroDet);        
            
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $arrayParametros = $objRequest->get('AdmiParametroDet');            
            $strVendedores = $arrayParametros['valor5'];
            $strBaseID     = $arrayParametros['valor6'];
            $strBaseBs     = $arrayParametros['valor7'];
            $strBase       = $arrayParametros['valor3'];
            $strMes        = $arrayParametros['valor1'];
            $strAnio       = $arrayParametros['valor2'];
            
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'DASHBOARD_COMERCIAL',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );

            $objInfoPersona         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->findOneBy(array(
                                                                   'login'   => $strVendedores,
                                                                   'estado'  => 'Activo'
                                                                )
                                                            );            

            $objAdmiParametroDet->setParametroId($objAdmiParametroCab);            
            $objAdmiParametroDet->setDescripcion($strDescripcion);            
            $objAdmiParametroDet->setValor1($strMes);
            $objAdmiParametroDet->setValor2($strAnio);            
            $objAdmiParametroDet->setValor6($strBaseID);
            $objAdmiParametroDet->setValor7($strBaseBs);
            $objAdmiParametroDet->setValor3($strBase);
            $objAdmiParametroDet->setValor4($objInfoPersona->getId());
            $objAdmiParametroDet->setEstado($strEstadoActivo);            
            $objAdmiParametroDet->setUsrCreacion($strUserSession);
            $objAdmiParametroDet->setFeCreacion($strDatetimeActual );
            $objAdmiParametroDet->setIpCreacion($strIpCreacion);
            $objAdmiParametroDet->setEmpresaCod($intIdEmpresa);
            $objAdmiParametroDet->setValor5($strVendedores);
            
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
            return $this->render('administracionBundle:AdmiBases:new.html.twig', array(
                                                                                        'item'           => $entityItemMenu,
                                                                                        'caracteristica' => $objAdmiParametroDet,
                                                                                        'form'           => $objForm->createView()
                                                                                       ));
        }
        else
        {
            
            return $this->redirect($this->generateUrl('com_admibases_show', array('intId' => $objAdmiParametroDet->getId())));
        }
    }
    
    /**
     * @Secure(roles="ROLE_417-4")
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
     */
    public function editAction($intId)
    {        
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");
        $strTipo        = "editar";
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $intIdEmpresa   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayValor      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getVendedores($strTipo,$intIdEmpresa);        

        if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId)) 
        {
            throw new NotFoundHttpException('No existe tal registro que se quiere modificar');
        }    
        array_push($arrayValor,$objAdmiParametroDet->getValor1());
        array_push($arrayValor,$objAdmiParametroDet->getValor2());
        array_push($arrayValor,'editarBase');
        
        $objFormulario           = $this->createForm(new AdmiParametroDetType(array('arrayValor'=>$arrayValor)), $objAdmiParametroDet);
        return $this->render('administracionBundle:AdmiBases:edit.html.twig', array(
                                                                                        'item'              => $entityItemMenu,
                                                                                        'edit_form'         => $objFormulario->createView(),
                                                                                        'caracteristica'    => $objAdmiParametroDet
                                                                                    ));
    }
    
    /**
     * @Secure(roles="ROLE_417-5")
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
     * @version 1.1 11-12-2021 - Se modifica para registrar las bases de internet/datos y business solutions.
     * @since 1.1
     */
    public function updateAction($intId)
    {
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");        
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("417", "1");
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
            $strBase       = $arrayParametros['valor3'];
            $strBaseID     = $arrayParametros['valor6'];
            $strBaseBS     = $arrayParametros['valor7'];
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'DASHBOARD_COMERCIAL',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );
            $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
            $objAdmiParametroDet->setValor3($strBase);
            $objAdmiParametroDet->setValor6($strBaseID);
            $objAdmiParametroDet->setValor7($strBaseBS);
            $objAdmiParametroDet->setEstado($strEstadoActivo);     
            $objAdmiParametroDet->setUsrUltMod($strUserSession);
            $objAdmiParametroDet->setFeUltMod($strDatetimeActual );
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
            return $this->render('administracionBundle:AdmiBases:edit.html.twig', array(
                                                                                        'item'           => $entityItemMenu,
                                                                                        'caracteristica' => $objAdmiParametroDet,
                                                                                        'form'           => $objEditForm->createView()
                                                                                       ));
        }        
        else
        {            
            return $this->redirect($this->generateUrl('com_admibases_show', array('intId' => $intId)));
        }
    }
    
    /**
     * @Secure(roles="ROLE_417-8")
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
            $objAdmiParametroDet->setFeUltMod($strDatetimeActual );
			
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
            return $this->redirect($this->generateUrl('com_admibases_show', array('intId' => $intId)));
        }
        else
        {
            return $this->redirect($this->generateUrl('com_admibases'));
        }
    }

    /**
     * @Secure(roles="ROLE_417-9")
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
                        $objAdmiParametroDet->setFeUltMod($strDatetimeActual );

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
    * @Secure(roles="ROLE_417-7")
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
            ->generarJson($arrayParametro);
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
    public function verificarBaseAction()
    {
        $objResponse      = new Response();
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
                                                                     'valor1'      => $strMes,
                                                                     'valor2'      => $strAnio,
                                                                     'descripcion' => 'BASES POR VENDEDOR',
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
     * @Secure(roles="ROLE_417-9")
     * 
     * Documentación para el método 'masivoAjaxAction'.
     *
     * Ingresa de forma masiva las Bases de los vendedores.
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
        $strDescripcion      = 'BASES POR VENDEDOR';
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
                $strBaseID      = $arrayDatos[3];
                $strBaseBS      = $arrayDatos[4];
                $strValor       = $arrayDatos[5];
                if($strVendedor =='' && $strMes =='' && $strAno =='' && $strValor =='')
                {
                    throw new \Exception('Los datos ingresados estan incompletos, favor revisar.');
                }
                $objAdmiParametroDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                   'parametroId'   => $objAdmiParametroCab->getId(),
                                                                   'descripcion'   => $strDescripcion,
                                                                   'estado'        => 'Activo',
                                                                   'valor1'        => $strMes,
                                                                   'valor2'        => $strAno,
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
                    $objAdmiParametroDet->setValor1($strMes);
                    $objAdmiParametroDet->setValor2($strAno);            
                    $objAdmiParametroDet->setValor3($strValor);
                    $objAdmiParametroDet->setValor4($objInfoPersona->getId());
                    $objAdmiParametroDet->setEstado($strEstadoActivo);            
                    $objAdmiParametroDet->setUsrCreacion($strUserSession);
                    $objAdmiParametroDet->setFeCreacion($strDatetimeActual );
                    $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                    $objAdmiParametroDet->setEmpresaCod($intIdEmpresa);
                    $objAdmiParametroDet->setValor5($strVendedor);
                    $objAdmiParametroDet->setValor6($strBaseID);
                    $objAdmiParametroDet->setValor7($strBaseBS);
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
