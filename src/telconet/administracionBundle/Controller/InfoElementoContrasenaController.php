<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElementoContrasena;
use telconet\schemaBundle\Form\InfoElementoContrasenaType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

/**
 * Clase que sirve para la administracion de contraseñas por elemento.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 12-02-2015
 */
class InfoElementoContrasenaController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Funcion que sirve para cargar la pantalla inicial de la Administracion de
     * Contraseñas
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-1")
     */
    public function indexAction()
    {
        if (true === $this->get('security.context')->isGranted('ROLE_271-1'))
        {
                $rolesPermitidos[] = 'ROLE_271-1'; //index
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-46'))
        {
                $rolesPermitidos[] = 'ROLE_271-46'; //encontrados
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-6'))
        {
                $rolesPermitidos[] = 'ROLE_271-6'; //show
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-2'))
        {
                $rolesPermitidos[] = 'ROLE_271-2'; //new
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-3'))
        {
                $rolesPermitidos[] = 'ROLE_271-3'; //create
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-8'))
        {
                $rolesPermitidos[] = 'ROLE_271-8'; //delete
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-9'))
        {
                $rolesPermitidos[] = 'ROLE_271-9'; //delete ajax
        }
        if (true === $this->get('security.context')->isGranted('ROLE_271-2077'))
        {
                $rolesPermitidos[] = 'ROLE_271-2077'; //ver contrasena encriptada
        }
        
        return $this->render('administracionBundle:InfoElementoContrasena:index.html.twig',array('rolesPermitidos' => $rolesPermitidos));
    }
    
    /**
     * Funcion que sirve para cargar el grid con datos, el cual se encuentra
     * en la pantalla inicial de la administracion de Contraseñas
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-46")
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $nombreElemento = $peticion->query->get('nombreElemento');
        $estado = $peticion->query->get('estado');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElementoContrasena')
            ->generarJsonElementoContrasena($nombreElemento, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Funcion que sirve para mostrar la informacion especifica de un Registro
     * de Contrasena
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-6")
     */
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objeto = $em->find('schemaBundle:InfoElementoContrasena', $id)) {
            throw new NotFoundHttpException('No existe la Contrasena que se quiere mostrar');
        }

        return $this->render('administracionBundle:InfoElementoContrasena:show.html.twig', array(
            'objeto'    => $objeto,
            'flag'      =>$peticion->get('flag')
        ));
    }
    
    /**
     * Funcion que sirve para cargar la plantilla de una nueva contrasena para un elemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-2")
     */
    public function newAction(){
        $entity = new InfoElementoContrasena();
        $form   = $this->createForm(new InfoElementoContrasenaType(), $entity);

        return $this->render('administracionBundle:InfoElementoContrasena:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para ingresar un registro en la base, correspondiente
     * a la Administracion de Contrasena
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-3")
     */
    public function createAction()
    {
        $em         = $this->get('doctrine')->getManager('telconet_infraestructura');
        $request    = $this->get('request');    
        $session    = $request->getSession();
        $objeto     = new InfoElementoContrasena();
        $parametros = $request->request->get('telconet_schemabundle_infoelementocontrasenatype');
        $elementoId = $parametros['elemento'];
        $usuarioId  = $parametros['usuario'];
        $contrasena = $parametros['contrasena'];
        
        //encriptar contrasena
        $encriptacion = $this->get('seguridad.crypt');
        $contrasenaEnc = $encriptacion->encriptar($contrasena); 
        
        $em->getConnection()->beginTransaction();
           
        try
        {
            $elemento = $em->getRepository('schemaBundle:InfoElemento')->find($elementoId);
            $usuario = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')->find($usuarioId);
            
            //elemento - contrasena
            $objeto->setElementoId($elemento);
            $objeto->setUsuarioId($usuario);
            $objeto->setContrasena($contrasenaEnc);
            $objeto->setEstado('Activo');
            $objeto->setFeCreacion(new \DateTime('now'));
            $objeto->setIpCreacion($request->getClientIp());
            $objeto->setUsrCreacion($session->get('user'));
            $em->persist($objeto);
            $em->flush();
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            
             $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: '.$e->getMessage().'!');
            return $this->redirect($this->generateUrl('infoelementocontrasena_new'));
        }       
        
        return $this->redirect($this->generateUrl('infoelementocontrasena_show', array('id' => $objeto->getId())));
    }
    
    /**
     * Funcion que sirve para eliminar un registro de la base (actualizacion de estado), via html
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-8")
     */
    public function deleteAction($id)
    {
        $request    = $this->get('request');    
        $session    = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:InfoElementoContrasena')->find($id);

        $em->getConnection()->beginTransaction();
        try
        {
            if(!$entity)
            {
                throw $this->createNotFoundException('Unable to find Contrasena entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($session->get('user'));

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
            return $this->redirect($this->generateUrl('infoelementocontrasena'));
        }

        return $this->redirect($this->generateUrl('infoelementocontrasena'));
    }

    /**
     * Funcion que sirve para eliminar un registro de la base (actualizacion del estado) via ajax
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-9")
     */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        $em->getConnection()->beginTransaction();
        
        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:InfoElementoContrasena', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($entity->getEstado()) != "eliminado")
                    {
                        $entity->setEstado("Eliminado");
                        $entity->setFeCreacion(new \DateTime('now'));
                        $entity->setUsrCreacion($peticion->getSession()->get('user'));
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
            return $this->redirect($this->generateUrl('infoelementocontrasena'));
        }
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }

        return $respuesta;
    }
    
    /**
     * Funcion que sirve para ver la contrasena de un elemento desencriptada
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-02-2015
     * @Secure(roles="ROLE_271-2077")
     */
    public function verContrasenaEncriptadaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $contrasenaEnc = $peticion->get('contrasena');
        
        //encriptar contrasena
        $encriptacion = $this->get('seguridad.crypt');
        $contrasena = $encriptacion->descencriptar($contrasenaEnc); 
        
        $respuesta->setContent($contrasena);

        return $respuesta;
    }
}