<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiUsuarioAcceso;
use telconet\schemaBundle\Form\AdmiUsuarioAccesoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiUsuarioAccesoController extends Controller
{ 
    /**
     * Funcion que sirve para cargar los usuarios de manera inicial
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_132-1'))
        {
            $rolesPermitidos[] = 'ROLE_132-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-46'))
        {
            $rolesPermitidos[] = 'ROLE_132-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-6'))
        {
            $rolesPermitidos[] = 'ROLE_132-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-2'))
        {
            $rolesPermitidos[] = 'ROLE_132-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-3'))
        {
            $rolesPermitidos[] = 'ROLE_132-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-4'))
        {
            $rolesPermitidos[] = 'ROLE_132-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-5'))
        {
            $rolesPermitidos[] = 'ROLE_132-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-8'))
        {
            $rolesPermitidos[] = 'ROLE_132-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_132-9'))
        {
            $rolesPermitidos[] = 'ROLE_132-9'; //delete ajax
        }

        return $this->render('administracionBundle:AdmiUsuarioAcceso:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    public function getUsuariosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiUsuarioAcceso')
            ->generarJsonUsuarios("Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener los usuarios que 
     * se encuentran registrados en la base de datos.
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-46")
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion       = $this->get('request');
        $nombre         = $peticion->query->get('nombreUsuarioAcceso');
        $estado         = $peticion->query->get('estado');
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiUsuarioAcceso')
            ->generarJsonUsuariosAcceso($nombre, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener la relacion entre usuario y modelo
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     */
    public function getRelacionUsuarioModeloAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion       = $this->get('request');
        $idUsuario      = $peticion->query->get('idUsuario');
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiUsuarioAcceso')
            ->generarJsonRelacionUsuarioModelo($idUsuario, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Funcion que sirve para ver los datos especificos de un Usuario
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-6")
     */
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objeto = $em->find('schemaBundle:AdmiUsuarioAcceso', $id)) {
            throw new NotFoundHttpException('No existe el Usuario que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiUsuarioAcceso:show.html.twig', array(
            'objeto'    => $objeto,
            'flag'      => $peticion->get('flag')
        ));
    }
    
    /**
     * Funcion que sirve para cargar el twig de nuevo usuario acceso
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-2")
    */
    public function newAction(){
        $entity = new AdmiUsuarioAcceso();
        $form   = $this->createForm(new AdmiUsuarioAccesoType(), $entity);

        return $this->render('administracionBundle:AdmiUsuarioAcceso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para ingresar los datos en la base de datos
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-3")
    */
    public function createAction(){
        $entity  = new AdmiUsuarioAcceso();
        $request = $this->getRequest();
        $session = $request->getSession();
        
        $em     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $form   = $this->createForm(new AdmiUsuarioAccesoType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            //verificar que el usuario no se repita
            $objetoRepetido = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')
                                 ->findOneBy(array( "nombreUsuarioAcceso" =>$form->getData()->getNombreUsuarioAcceso(), "estado"=>"Activo"));
            if($objetoRepetido){
                $this->get('session')->getFlashBag()->add('notice', 'Ya existe ese usuario, favor revisar!');
                return $this->redirect($this->generateUrl('admiusuarioacceso_new'));
            }
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($session->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));
            
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admiusuarioacceso_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiUsuarioAcceso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));        
    }
    
    /**
     * Funcion que sirve para cargar el twig de edicion del usuario de acceso
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objeto = $em->find('schemaBundle:AdmiUsuarioAcceso', $id)) {
            throw new NotFoundHttpException('No existe el Usuario que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiUsuarioAccesoType(), $objeto);

        return $this->render('administracionBundle:AdmiUsuarioAcceso:edit.html.twig', array(
                             'edit_form'    => $formulario->createView(),
                             'objeto'       => $objeto));
    }
    
    /**
     * Funcion que sirve para realizar la edicion del usuario acceso
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-5")

    */
    public function updateAction($id)
    {
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity     = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiUsuarioAcceso entity.');
        }

        $editForm = $this->createForm(new AdmiUsuarioAccesoType(), $entity);

        $editForm->bind($request);

        if ($editForm->isValid()) {
            //verificar que el usuario no se repita
            $objetoRepetido = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')
                                 ->findOneBy(array( "nombreUsuarioAcceso" =>$editForm->getData()->getNombreUsuarioAcceso(), "estado"=>"Activo"));
            if($objetoRepetido && $objetoRepetido->getId()!=$id){
                $this->get('session')->getFlashBag()->add('notice', 'Ya existe ese usuario, favor revisar!');
                return $this->redirect($this->generateUrl('admiusuarioacceso_edit', array('id' => $id)));
            }
            
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiusuarioacceso_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiUsuarioAcceso:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
     * Funcion que sirve para cambiar de estado a Eliminado de un usuario acceso mediante ajax request
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-9")
    */
    public function deleteAjaxAction(){
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        
        $em->getConnection()->beginTransaction();
        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:AdmiUsuarioAcceso', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($entity->getEstado()) != "eliminado")
                    {
                        $entity->setEstado("Eliminado");
                        $entity->setFeUltMod(new \DateTime('now'));
                        $entity->setUsrUltMod($session->get('user'));
                        $em->persist($entity);
                        $em->flush();
                    }

                    $respuesta->setContent("Se elimino la entidad");
                }
            endforeach;
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admiusuarioacceso'));
        }
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }

        return $respuesta;
    }
    
    /**
     * Funcion que cambia el estado a Eliminado al usuario acceso mediante html
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @Secure(roles="ROLE_132-8")
    */
   public function deleteAction($id)
    {
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity     = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')->find($id);     
        
        $em->getConnection()->beginTransaction();
        try
        {
            if(!$entity)
            {
                throw $this->createNotFoundException('No existe la entidad');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));

            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admiusuarioacceso'));
        }

        return $this->redirect($this->generateUrl('admiusuarioacceso'));
    }

}