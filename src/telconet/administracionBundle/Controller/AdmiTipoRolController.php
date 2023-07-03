<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoRol;
use telconet\schemaBundle\Form\AdmiTipoRolType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;

class AdmiTipoRolController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_49-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");

        $entities = $em->getRepository('schemaBundle:AdmiTipoRol')->findAll();

        return $this->render('administracionBundle:AdmiTipoRol:index.html.twig', array(
            'item' => $entityItemMenu,
            'tiporol' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_49-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");

        if (null == $tiporol = $em->find('schemaBundle:AdmiTipoRol', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoRol que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoRol:show.html.twig', array(
            'item' => $entityItemMenu,
            'tiporol'   => $tiporol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_49-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");
        $entity = new AdmiTipoRol();
        $form   = $this->createForm(new AdmiTipoRolType(), $entity);

        return $this->render('administracionBundle:AdmiTipoRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiporol' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_49-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");
        $entity  = new AdmiTipoRol();
        $form    = $this->createForm(new AdmiTipoRolType(), $entity);
        
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));
          
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admitiporol_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTipoRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiporol' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_49-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");

        if (null == $tiporol = $em->find('schemaBundle:AdmiTipoRol', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoRol que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoRolType(), $tiporol);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiTipoRol:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'tiporol'   => $tiporol));
    }
    
    /**
    * @Secure(roles="ROLE_49-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("49", "1");
        $entity = $em->getRepository('schemaBundle:AdmiTipoRol')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoRol entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoRolType(), $entity);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admitiporol_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoRol:edit.html.twig',array(
            'item' => $entityItemMenu,
            'tiporol'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_49-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiTipoRol')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoRol entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admitiporol'));
    }

    /**
    * @Secure(roles="ROLE_49-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoRol', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($request->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_49-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiTipoRol')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getAdmiRolbyTipoRolAction, obtiene la informacion de los tipos de roles
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAdmiRolbyTipoRolAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $objReturnResponse       = new ReturnResponse();
        $objResponse             = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $strDescripcionTipoRol      = $objRequest->get('strDescripcionTipoRol');
        $strEstadoTipoRol           = $objRequest->get('strEstadoTipoRol');
        $strComparadorEstTipoRol    = $objRequest->get('strComparadorEstTipoRol');
        $strEstadoRol               = $objRequest->get('strEstadoRol');
        $intIdPersona               = $objRequest->get('intIdPersona');
        $strDisponiblesPersona      = $objRequest->get('strDisponiblesPersona');
        $strComparadorEstRol        = $objRequest->get('strComparadorEstRol'); 
        $strEstadoEmpresaRol        = $objRequest->get('strEstadoEmpresaRol');
        $strComparadorEmpRol        = $objRequest->get('strComparadorEmpRol');
        $strComparadorPerEmpRolDis  = $objRequest->get('strComparadorPerEmpRolDis');
        $strEstadoPerEmpRolDis      = $objRequest->get('strEstadoPerEmpRolDis');
        $strComparadorEmpRolDis     = $objRequest->get('strComparadorEmpRolDis');
        $strEstadoEmpRolDis         = $objRequest->get('strEstadoEmpRolDis');
        $strAppendRol               = $objRequest->get('strAppendRol');
        $intLimit                   = $objRequest->get('limit');
        $intStart                   = $objRequest->get('start');
        $emComercial                = $this->getDoctrine()->getManager();
        $booleanShowAppendRol       = false;

        if(empty($strEstadoTipoRol))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando el estado de los titulos.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $arrayEstadoTipoRol = array_map('trim', explode(",", $strEstadoTipoRol));

        if(empty($strDescripcionTipoRol))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando descripcion tipo rol.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $arrayDescripcionTipoRol = array_map('trim', explode(",", $strDescripcionTipoRol));
        $arrayEstadoPerEmpRolDis = array_map('trim', explode(",", $strEstadoPerEmpRolDis));
        $arrayEstadoRol          = array_map('trim', explode(",", $strEstadoRol));
        $arrayEstadoEmpresaRol   = array_map('trim', explode(",", $strEstadoEmpresaRol));
        $arrayEstadoEmpRolDis    = array_map('trim', explode(",", $strEstadoEmpRolDis));

        $arrayParamRolByTipoRol = array();
        $arrayParamRolByTipoRol['arrayTipoRol']          = ['arrayDescripcionTipoRol' => $arrayDescripcionTipoRol, 
                                                            'arrayEstado'             => $arrayEstadoTipoRol,
                                                            'strComparadorEstadoATR'  => $strComparadorEstTipoRol];
        $arrayParamRolByTipoRol['arrayRol']              = ['arrayEstado'             => $arrayEstadoRol,
                                                            'strComparadorEstadoAR'   => $strComparadorEstRol];
        $arrayParamRolByTipoRol['arrayEmpresaRol']       = ['arrayEstado'             => $arrayEstadoEmpresaRol,
                                                            'strComparadorEstadoIER'  => $strComparadorEmpRol];
        $arrayParamRolByTipoRol['arrayEmpresa']          = ['arrayEstado'             => ['Activo'],
                                                            'arrayPrefijo'            => [$objSession->get('prefijoEmpresa')]];
        $arrayParamRolByTipoRol['arrayPersona']          = ['arrayPersona'            => [$intIdPersona],
                                                            'arrayEstado'             => $arrayEstadoPerEmpRolDis,
                                                            'strComparadorEstado'     => $strComparadorPerEmpRolDis];
        $arrayParamRolByTipoRol['arrayEmpRolDis']        = ['arrayEstado'             => $arrayEstadoEmpRolDis,
                                                            'strComparadorEstado'     => $strComparadorEmpRolDis];
        $arrayParamRolByTipoRol['strDisponiblesPersona'] = $strDisponiblesPersona;
        $arrayParamRolByTipoRol['strOrderBy']            = 'ASC';
        $arrayParamRolByTipoRol['intLimit']              = $intLimit;
        $arrayParamRolByTipoRol['intStart']              = $intStart;

        $objRolByTipoRol = $emComercial->getRepository('schemaBundle:AdmiTipoRol')->getRolByTipoRol($arrayParamRolByTipoRol);

        $arrayRol = array();
        if(!empty($strAppendRol))
        {
            $arrayRol[] = array('intIdRol'          => 0,
                                'strDescripcionRol' => $strAppendRol,
                                'intIdEmpresaRol'   => 0,
                                'strEstado'         => '',
                                'strUsrCreacion'    => '',
                                'strFeCreacion'     => '');
        }
        foreach($objRolByTipoRol->getRegistros() as $arrayRolByTipoRol):
            $booleanShowAppendRol = true;
            $arrayRol[] = array('intIdRol'          => $arrayRolByTipoRol['intIdRol'],
                                'strDescripcionRol' => $arrayRolByTipoRol['strDescripcionRol'],
                                'intIdEmpresaRol'   => $arrayRolByTipoRol['intIdEmpresaRol'],
                                'strEstado'         => $arrayRolByTipoRol['strEstadoRol'],
                                'strUsrCreacion'    => $arrayRolByTipoRol['strUsrCreacionRol'],
                                'strFeCreacion'     => ($arrayRolByTipoRol['dateFeCreacionRol']) ? 
                                                        date_format($arrayRolByTipoRol['dateFeCreacionRol'], "d-m-Y H:i:s") : '');

        endforeach;
        if(!$booleanShowAppendRol)
        {
            $arrayRol = array();
        }

        $objReturnResponse->setRegistros($arrayRol);
        $objReturnResponse->setTotal($objRolByTipoRol->getTotal());
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));

        return $objResponse;
    }

}