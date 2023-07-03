<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoCuenta;
use telconet\schemaBundle\Form\AdmiTipoCuentaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiTipoCuentaController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * indexAction, Redirecciona al index del listado de Tipos de Cuenta
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 04-05-2015
     * @return redirecciona a la pagina del index del listado de Tipos de Cuenta
     * 
     * Actualización: Consulta tipo cuenta por id de país
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 10-07-2017
     * 
     */
    /**
     * @Secure(roles="ROLE_45-1")
     */
    public function indexAction()
    {
        $objRequest           = $this->getRequest();
        $intIdPais            = $objRequest->getSession()->get('intIdPais');
        $em                   = $this->getDoctrine()->getManager('telconet_general');
        $entityAdmiTipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findBy(array('paisId'=>$intIdPais));

        $arrayRolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_45-4'))
        {
            $arrayRolesPermitidos[] = 'ROLE_45-4'; //Editar Tipos Cuenta
        }

        if(true === $this->get('security.context')->isGranted('ROLE_45-9'))
        {
            $arrayRolesPermitidos[] = 'ROLE_45-9'; //Eliminar Tipos CUenta
        }
        return $this->render('administracionBundle:AdmiTipoCuenta:index.html.twig', array('tipocuenta' => $entityAdmiTipoCuenta,
                                                                                          'rolesPermitidos' => $arrayRolesPermitidos));
    }//indexAction

    /**
    * @Secure(roles="ROLE_45-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("45", "1");

        if (null == $tipocuenta = $em->find('schemaBundle:AdmiTipoCuenta', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoCuenta que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoCuenta:show.html.twig', array(
            'item' => $entityItemMenu,
            'tipocuenta'   => $tipocuenta,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_45-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("45", "1");
        $entity = new AdmiTipoCuenta();
        $form   = $this->createForm(new AdmiTipoCuentaType(), $entity);

        return $this->render('administracionBundle:AdmiTipoCuenta:new.html.twig', array(
            'item' => $entityItemMenu,
            'tipocuenta' => $entity,
            'form'   => $form->createView()
        ));
    }
    /**
    * Funcion que crea un registro para AdmiTipoCuenta
    * @author Andrés Montero <amontero@telconet.ec>
    * @param Array $arrayParametros
    * @version 1.0 23-05-2014
    * @return object
    * 
    * Actualización: Se agrega campo PaisId en la creacion de registro de AdmiTipoCuenta
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    */
    /**
    * @Secure(roles="ROLE_45-3")
    */
    public function createAction()
    {
        $request        = $this->get('request');
        $em             = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("45", "1");
        $entity         = new AdmiTipoCuenta();
        $form           = $this->createForm(new AdmiTipoCuentaType(), $entity);
        
        $entity->setPaisId($request->getSession()->get('intIdPais'));
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admitipocuenta_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTipoCuenta:new.html.twig', array(
            'item' => $entityItemMenu,
            'tipocuenta' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_45-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("45", "1");

        if (null == $tipocuenta = $em->find('schemaBundle:AdmiTipoCuenta', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoCuenta que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoCuentaType(), $tipocuenta);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiTipoCuenta:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'tipocuenta'   => $tipocuenta));
    }
    
    /**
    * @Secure(roles="ROLE_45-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("45", "1");
        $entity = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoCuenta entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoCuentaType(), $entity);

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

            return $this->redirect($this->generateUrl('admitipocuenta_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoCuenta:edit.html.twig',array(
            'item' => $entityItemMenu,
            'tipocuenta'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_45-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoCuenta entity.');
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

        return $this->redirect($this->generateUrl('admitipocuenta'));
    }

    /**
    * @Secure(roles="ROLE_45-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoCuenta', $id)) {
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
    /**
    * Funcion que obtiene los tipos de cuenta para enviarlos al grid de administracion
    * @author Andrés Montero <amontero@telconet.ec>
    * @param Array $arrayParametros
    * @version 1.0 23-05-2014
    * @return object
    * 
    * Actualización: Se envia parametros en arreglo a la funcion generarJson
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    * 
    */
    /**
    * @Secure(roles="ROLE_45-7")
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

        $arrayParametros = array();
        $arrayParametros['strNombre'] = $nombre;
        $arrayParametros['strEstado'] = $estado;
        $arrayParametros['intStart']  = $start;
        $arrayParametros['intLimit']  = $limit;
        $arrayParametros['intIdPais'] = $peticion->getSession()->get('intIdPais');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiTipoCuenta')
            ->generarJson($arrayParametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getTipoCuentaJsonAction, obtiene los diferentes tipos de cuenta en un array
     * @version 1.0 Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0 19-01.2015
     * @return Response retorna los tipos de cuenta en un array
     * 
     * Actualización: Se envia parametros en arreglo a la funcion getRegistros
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 10-07-2017
     */
    public function getTipoCuentaJsonAction()
    {
        $objRequest      = $this->getRequest();
        $intIdPais       = $objRequest->getSession()->get('intIdPais');
        $intTotal        = 0;
        $arrayParametros = array();
        $arrayParametros['strNombre'] = '';
        $arrayParametros['strEstado'] = 'Activo';
        $arrayParametros['intStart']  = 0;
        $arrayParametros['intLimit']  = 100;
        $arrayParametros['intIdPais'] = $intIdPais;
        $objAdmiTipoCuenta = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiTipoCuenta')
                                                                         ->getRegistros($arrayParametros);
        $arrayTipoCuenta = array();
        foreach($objAdmiTipoCuenta as $objAdmiTipoCuenta)
        {
            $intTotal          = $intTotal + 1;
            $arrayTipoCuenta[] = array('intIdAdmiTipoCUenta'    => $objAdmiTipoCuenta->getId(), 
                                       'strDescCuenta'          => $objAdmiTipoCuenta->getDescripcionCuenta());
        }
        $objResponse = new Response(json_encode(array('jsonTipoCuenta'  => $arrayTipoCuenta, 
                                                      'intTotal'        => $intTotal)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getTipoCuentaJsonAction
    
}