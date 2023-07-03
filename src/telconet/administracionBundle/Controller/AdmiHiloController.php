<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiHilo;
use telconet\schemaBundle\Form\AdmiHiloType;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiHiloController extends Controller 
{ 
    /**
     * Funcion que sirve para cargar los hilos de manera inicial
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_115-1'))
        {
            $rolesPermitidos[] = 'ROLE_115-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-46'))
        {
            $rolesPermitidos[] = 'ROLE_115-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-6'))
        {
            $rolesPermitidos[] = 'ROLE_115-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-2'))
        {
            $rolesPermitidos[] = 'ROLE_115-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-3'))
        {
            $rolesPermitidos[] = 'ROLE_115-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-4'))
        {
            $rolesPermitidos[] = 'ROLE_115-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-5'))
        {
            $rolesPermitidos[] = 'ROLE_115-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-8'))
        {
            $rolesPermitidos[] = 'ROLE_115-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_115-9'))
        {
            $rolesPermitidos[] = 'ROLE_115-9'; //delete ajax
        }

        return $this->render('administracionBundle:AdmiHilo:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que sirve para mostrar los datos especificios de un Hilo
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-6")
     */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $hilo = $em->find('schemaBundle:AdmiHilo', $id)) {
            throw new NotFoundHttpException('No se encontro el Hilo Solicitado.');
        }

        return $this->render('administracionBundle:AdmiHilo:show.html.twig', array(
            'hilo'  => $hilo,
            'flag'  => $peticion->get('flag')
        ));
    }
    
    /**
     * Funcion que sirve para cargar el twig de nuevo hilo
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-2")
     */
    public function newAction()
    {
        $entity = new AdmiHilo();
        $form   = $this->createForm(new AdmiHiloType(), $entity);

        return $this->render('administracionBundle:AdmiHilo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para ingresar los datos de un hilo a la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-3")
     */
    public function createAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $em      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiHilo();
        $form    = $this->createForm(new AdmiHiloType(), $entity);
        
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
            return $this->redirect($this->generateUrl('admihilo_new'));
        }

        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admihilo_show', array('id' => $entity->getId())));
    }

    /**
     * Funcion que sirve para cargar el twig de edicion de un hilo
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param $id   int
     * @Secure(roles="ROLE_115-4")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $hilo = $em->find('schemaBundle:AdmiHilo', $id)) 
        {
            throw new NotFoundHttpException('No se encontro el Hilo Solicitado.');
        }

        $formulario =$this->createForm(new AdmiHiloType(), $hilo);

        return $this->render('administracionBundle:AdmiHilo:edit.html.twig', array(
                             'edit_form' => $formulario->createView(),
                             'hilo'      => $hilo));
    }
    
    /**
     * Funcion que sirve para actualizar datos de un hilo
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_115-5")
     */
    public function updateAction($id)
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiHilo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encontro el Hilo Solicitado.');
        }

        $editForm   = $this->createForm(new AdmiHiloType(), $entity);
        $em->getConnection()->beginTransaction();
        try
        {
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
            return $this->redirect($this->generateUrl('admihilo_edit', array('id' => $id)));
        }
        $editForm->bind($request);



        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admihilo_show', array('id' => $id)));
    }

    /**
     * Funcion que sirve para cambiar de estado a Eliminado de un hilo mediante html
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_115-8")
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiHilo')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('No se encontro el Hilo Solicitado.');
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
            return $this->redirect($this->generateUrl('admihilo'));
        }

        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admihilo'));
    }

    /**
     * Funcion que sirve para cambiar de estado a Eliminado de uno o mas hilos mediante ajax
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-9")
     */
    public function deleteAjaxAction(){
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $request    = $this->getRequest();
        $session    = $request->getSession();        
        $parametro  = $request->get('param');        
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $em->getConnection()->beginTransaction();        
        $array_valor = explode("|",$parametro);
        
        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:AdmiHilo', $id))
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
            return $this->redirect($this->generateUrl('admihilo'));
        }

        $em->getConnection()->commit();
        $respuesta->setContent("Se elimino la entidad");
        return $respuesta;
    }

    /**
     * Funcion que sirve para obtener los hilos que se encuentran
     * registrados en la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_115-46")
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion   = $this->get('request');        
        $color      = $peticion->get('colorHilo');
        $numeroHilo = $peticion->get('numeroHilo');
        $estado     = $peticion->get('estado');        
        $start      = $peticion->get('start');
        $limit      = $peticion->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiHilo')
            ->generarJsonHilos($numeroHilo, $color, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Funcion que sirve para obtener los hilos por estado
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     */
    public function getHilosPorEstadoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $clase  = $peticion->query->get('claseTipoMedioId');
        $estado = $peticion->query->get('estado');
        $start  = $peticion->query->get('start');
        $limit  = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiHilo')
            ->generarJsonHilosPorEstado($clase, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    
    /**
     * Funcion que sirve para obtener los hilos por buffer
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 24-02-2015
     */
    public function getHilosPorBufferAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        
        $session = $peticion->getSession();

        $buffer  = $peticion->get('buffer');
        $estado  = $peticion->get('estado');        
        $empresa = $session->get('idEmpresa');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiHilo')
            ->generarJsonHilosPorBuffer($buffer, $empresa ,$estado);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}