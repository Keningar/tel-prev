<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiOid;
use telconet\schemaBundle\Form\AdmiOidType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiOidController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_120-1")
     * 
     * indexAction
     *
     * Metodo encargado de retornar la vista inicial de los OID's.
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para que se envie el tipo de elemento para obtener las marcas a las que se relacionan
     *                           los OID's
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiOid')->findAll();

        return $this->render('administracionBundle:AdmiOid:index.html.twig', array('entities' => $entities, 'tipoElemento' => 'UPS'));
    }
    
    /**
    * @Secure(roles="ROLE_120-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $oid = $em->find('schemaBundle:AdmiOid', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiOid:show.html.twig', array(
            'oid'   => $oid,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_120-2")
    */
    public function newAction()
    {
        $entity = new AdmiOid();
        $form   = $this->createForm(new AdmiOidType(), $entity);

        return $this->render('administracionBundle:AdmiOid:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * @Secure(roles="ROLE_120-3")
     * 
     * updateAction
     *
     * Metodo encargado de crear un OID
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para que use el nombre del usuario en session al crear el OID
     */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiOid();
        $form    = $this->createForm(new AdmiOidType(), $entity);
        
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
            
            return $this->redirect($this->generateUrl('admioid_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiOid:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_120-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $oid = $em->find('schemaBundle:AdmiOid', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiOidType(), $oid);
//        $formulario->setData($oid);

        return $this->render('administracionBundle:AdmiOid:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'oid'   => $oid));
    }
    
    /**
     * @Secure(roles="ROLE_120-5")
     * 
     * updateAction
     *
     * Metodo encargado de actualizar la información de un OID
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para que use el nombre del usuario en session al cambiar el estado del OID
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiOid')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiOid entity.');
        }

        $editForm   = $this->createForm(new AdmiOidType(), $entity);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setEstado('Modificado');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admioid_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiOid:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
     * @Secure(roles="ROLE_120-8")
     * 
     * deleteAction
     *
     * Metodo encargado de eliminar un OID
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para que use el nombre del usuario en session al cambiar el estado del OID
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiOid')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiOid entity.');
        }
        
        $estado = 'Eliminado';
        $entity->setEstado($estado);
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));

        $em->persist($entity);	
        $em->flush();

        return $this->redirect($this->generateUrl('admioid'));
    }
    

    /**
     * @Secure(roles="ROLE_120-9")
     * 
     * deleteAjaxAction
     *
     * Metodo encargado de eliminar un OID
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para que use el nombre del usuario en session al cambiar el estado del OID
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiOid', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($peticion->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_120-46")
     * 
     * getEncontradosAction
     *
     * Metodo encargado de retornar toda la información de los OID existentes
     *
     * @return Response   $respuesta
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para retorne la informacion adecuada de los OID
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $objRequest        = $this->get('request');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strQueryNombre    = $objRequest->query->get('query') ? $objRequest->query->get('query') : "";
        $strNombre         = ($strQueryNombre != '' ? $strQueryNombre : $objRequest->query->get('nombre'));
        $intIdMarca        = $objRequest->query->get('marcaElemento') ? $objRequest->query->get('marcaElemento') : 0;
        $strEstado         = $objRequest->query->get('estado') ? $objRequest->query->get('estado') : 'Todos';
        $intStart          = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit          = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $arrayParametros = array(
                                    'nombreOid' => $strNombre,
                                    'idMarca'   => $intIdMarca,
                                    'estado'    => $strEstado,
                                    'inicio'    => $intStart,
                                    'limite'    => $intLimit
                                );
        
        $objJson = $emInfraestructura->getRepository('schemaBundle:AdmiOid')->generarJsonOids( $arrayParametros );
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getOidsAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiOid')
            ->generarJsonOids("","","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}