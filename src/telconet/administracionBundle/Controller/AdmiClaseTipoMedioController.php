<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiClaseTipoMedio;
use telconet\schemaBundle\Form\AdmiClaseTipoMedioType;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

/**
 * Clase para la Administracion de Clase Tipo Medio 
 * 
 * @author creado Francisco Adum <fadum@telconet.ec>
 * @version 1.0 24-02-2015
 */
class AdmiClaseTipoMedioController extends Controller
{ 
    /**
     * Funcion que sirve para cargar las clases de tipos de medio de manera inicial
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_274-1'))
        {
            $rolesPermitidos[] = 'ROLE_274-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-46'))
        {
            $rolesPermitidos[] = 'ROLE_274-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-6'))
        {
            $rolesPermitidos[] = 'ROLE_274-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-2'))
        {
            $rolesPermitidos[] = 'ROLE_274-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-3'))
        {
            $rolesPermitidos[] = 'ROLE_274-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-4'))
        {
            $rolesPermitidos[] = 'ROLE_274-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-5'))
        {
            $rolesPermitidos[] = 'ROLE_274-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-8'))
        {
            $rolesPermitidos[] = 'ROLE_274-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_274-9'))
        {
            $rolesPermitidos[] = 'ROLE_274-9'; //delete ajax
        }

        return $this->render('administracionBundle:AdmiClaseTipoMedio:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que sirve para mostrar los datos especificios de un ClaseTipoMedio
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-6")
     */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objeto = $em->find('schemaBundle:AdmiClaseTipoMedio', $id)) {
            throw new NotFoundHttpException('No existe la Clase Tipo Medio que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiClaseTipoMedio:show.html.twig', array(
            'objeto'  => $objeto,
            'flag'    => $peticion->get('flag')
        ));
    }
    
    /**
     * Funcion que sirve para cargar el twig de nuevo clasetipomedio
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-2")
     */
    public function newAction()
    {
        $entity = new AdmiClaseTipoMedio();
        $form   = $this->createForm(new AdmiClaseTipoMedioType(), $entity);

        return $this->render('administracionBundle:AdmiClaseTipoMedio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para ingresar los datos de un clasetipomedio a la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-3")
     */
    public function createAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $em      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiClaseTipoMedio();
        $form    = $this->createForm(new AdmiClaseTipoMedioType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($session->get('user'));
        
        $em->getConnection()->beginTransaction();

        try
        {
            $form->bind($request);

            if($form->isValid())
            {
                $em->persist($entity);
                $em->flush();
            }
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admiclasetipomedio_new'));
        }


        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admiclasetipomedio_show', array('id' => $entity->getId())));
    }

    /**
     * Funcion que sirve para cargar el twig de edicion de un clasetipomedio
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param $id   int
     * @Secure(roles="ROLE_274-4")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $clasetipomedio = $em->find('schemaBundle:AdmiClaseTipoMedio', $id)) 
        {
            throw new NotFoundHttpException('No existe la Clase Tipo Medio que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiClaseTipoMedioType(), $clasetipomedio);

        return $this->render('administracionBundle:AdmiClaseTipoMedio:edit.html.twig', array(
                             'edit_form'    => $formulario->createView(),
                             'objeto'       => $clasetipomedio));
    }
    
    /**
     * Funcion que sirve para actualizar datos de un clasetipomedio
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_274-5")
     */
    public function updateAction($id)
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiClaseTipoMedio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encontro la Clase tipo medio solicitada.');
        }

        $editForm   = $this->createForm(new AdmiClaseTipoMedioType(), $entity);
        $em->getConnection()->beginTransaction();

        try
        {
            $editForm->bind($request);

            if($editForm->isValid())
            {
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($session->get('user'));

                $em->persist($entity);
                $em->flush();
            }
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admiclasetipomedio_edit', array('id' => $id)));
        }

        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admiclasetipomedio_show', array('id' => $id)));
    }

    /**
     * Funcion que sirve para cambiar de estado a Eliminado de un clasetipomedio mediante html
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_274-8")
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiClaseTipoMedio')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('No se encontro la Clase tipo medio solicitada.');
        }

        $em->getConnection()->beginTransaction();
        try
        {
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));

            $em->persist($entity);
            $em->flush();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admiclasetipomedio'));
        }

        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admiclasetipomedio'));
    }

    /**
     * Funcion que sirve para cambiar de estado a Eliminado de uno o mas clasetipomedios mediante ajax
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-9")
     */
    public function deleteAjaxAction(){
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $request    = $this->getRequest();
        $session    = $request->getSession();        
        $parametro  = $request->get('param');        
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em->getConnection()->beginTransaction();
        
        try
        {
            $array_valor = explode("|", $parametro);
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:AdmiClaseTipoMedio', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    $estado = 'Eliminado';
                    $entity->setEstado($estado);
                    $entity->setFeUltMod(new \DateTime('now'));
                    $entity->setUsrUltMod($session->get('user'));

                    $em->persist($entity);
                    $em->flush();
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
            return $this->redirect($this->generateUrl('admiclasetipomedio'));
        }

        $em->getConnection()->commit();
        $respuesta->setContent("Se elimino la entidad");
        return $respuesta;
    }

    /**
     * Funcion que sirve para obtener las clases de tipo medio que se encuentran
     * registrados en la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_274-46")
     */
   public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion       = $this->get('request');        
        $tipoMedioId    = $peticion->get('tipoMedioId');
        $nombreClase    = $peticion->get('numeroClaseTipoMedio');
        $estado         = $peticion->get('estado');
        $start          = $peticion->get('start');
        $limit          = $peticion->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiClaseTipoMedio')
            ->generarJsonClaseTipoMedio($tipoMedioId, $nombreClase, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
}