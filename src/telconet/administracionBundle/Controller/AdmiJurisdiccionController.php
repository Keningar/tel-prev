<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiJurisdiccion;
use telconet\schemaBundle\Entity\AdmiCantonJurisdiccion;
use telconet\schemaBundle\Form\AdmiJurisdiccionType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiJurisdiccionController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_117-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiJurisdiccion')->findAll();

        return $this->render('administracionBundle:AdmiJurisdiccion:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_117-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $jurisdiccion = $em->find('schemaBundle:AdmiJurisdiccion', $id)) {
            throw new NotFoundHttpException('No existe la Jurisdiccion que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiJurisdiccion:show.html.twig', array(
            'jurisdiccion'   => $jurisdiccion,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_117-2")
    */
    public function newAction()
    {
        $entity = new AdmiJurisdiccion();
        $form   = $this->createForm(new AdmiJurisdiccionType($options), $entity);
        
        return $this->render('administracionBundle:AdmiJurisdiccion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para crear la jurisdiccion
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-01-2015
     * 
     * @Secure(roles="ROLE_117-3")
     */
    public function createAction()
    {
        $entity = new AdmiJurisdiccion();
        $request = $this->getRequest();
        $session = $request->getSession();
        
        $form = $this->createForm(new AdmiJurisdiccionType($options), $entity);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager('telconet_infraestructura'); 

        $peticion = $this->get('request');
        if($form->isValid())
        {
            $em->getConnection()->beginTransaction();
            try
            {
                $entity->setEstado("Activo");
                $entity->setOficinaId($session->get('idOficina'));

                /* Para que guarde la fecha y el usuario correspondiente */
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($session->get('user'));
                $entity->setUsrUltMod($session->get('user'));
                $entity->setFeUltMod(new \DateTime('now'));

                // Save
                $em->persist($entity);
                $em->flush();

                $json_relacion_sistema  = json_decode($peticion->get('relaciones'));
                $array_relacion_sistema = $json_relacion_sistema->relaciones;
                foreach($array_relacion_sistema as $relacion)
                {
                    $tmp_canton = null;
                    $mailTecnico = null;
                    $ipReserva = null;

                    if($relacion->canton_id)
                    {
                        $tmp_canton = ($relacion->canton_id);
                    }
                    if($relacion->mail_tecnico)
                    {
                        $mailTecnico = ($relacion->mail_tecnico);
                    }
                    if($relacion->ip_reserva)
                    {
                        $ipReserva = ($relacion->ip_reserva);
                    }

                    $cantonJurisdiccion = new AdmiCantonJurisdiccion;

                    $cantonJurisdiccion->setJurisdiccionId($entity->getId());
                    $cantonJurisdiccion->setCantonId($tmp_canton);
                    $cantonJurisdiccion->setMailTecnico($mailTecnico);
                    $cantonJurisdiccion->setIpReserva($ipReserva);
                    $cantonJurisdiccion->setEstado("Activo");
                    $cantonJurisdiccion->setUsrCreacion($session->get('user'));
                    $cantonJurisdiccion->setFeCreacion(new \DateTime('now'));
                    $cantonJurisdiccion->setUsrUltMod($session->get('user'));
                    $cantonJurisdiccion->setFeUltMod(new \DateTime('now'));

                    // Save
                    $em->persist($cantonJurisdiccion);
                    $em->flush();
                }

                $em->getConnection()->commit();
            }
            catch(Exception $e)
            {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            return $this->redirect($this->generateUrl('admijurisdiccion_show', array('id' => $entity->getId())));
        }

        $parametros = array(
            'entity' => $entity,
            'form'   => $form->createView()
        );

        return $this->render('schemaBundle:AdmiJurisdiccion:new.html.twig', $parametros);
    }
    
    /**
    * @Secure(roles="ROLE_117-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $jurisdiccion = $em->find('schemaBundle:AdmiJurisdiccion', $id)) {
            throw new NotFoundHttpException('No existe la Jurisdiccion que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiJurisdiccionType(), $jurisdiccion);
//        $formulario->setData($conector);

        return $this->render('administracionBundle:AdmiJurisdiccion:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'jurisdiccion'   => $jurisdiccion));
    }
    
    /**
     * @Secure(roles="ROLE_117-5")
     * 
     * Documentación para el método 'updateAction'.
     * 
     * Funcion que sirve para actualizar la jurisdicción
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-01-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 08-07-2016
     *  Se agrega el envío del parámetro $strUsuario  al método "borrarDistintosEleccion".
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:AdmiJurisdiccion')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiJurisdiccion entity.');
        }

        $request    = $this->getRequest();
        $session    = $request->getSession();
        $peticion   = $this->get('request');
        $strUsuario = $session->get('user');

        $editForm   = $this->createForm(new AdmiJurisdiccionType($options), $entity);
        $editForm->handleRequest($request);
        
        if($editForm->isValid())
        {
            $em->getConnection()->beginTransaction();
            try
            {
                $entity->setEstado("Modificado");

                /* Para que guarde la fecha y el usuario correspondiente */
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($strUsuario);
                // Save
                $em->persist($entity);
                $em->flush();
                $json_relacion_sistema = json_decode($peticion->get('relaciones'));
                $array_relacion_sistema = $json_relacion_sistema->relaciones;
                $array_relaciones = array();

                foreach($array_relacion_sistema as $relacion)
                {
                    $cantonJurisdiccion = null;

                    if($relacion->mailTecnico)
                    {
                        $mailTecnico = ($relacion->mailTecnico);
                    }
                    if($relacion->ipReserva)
                    {
                        $ipReserva = ($relacion->ipReserva);
                    }
                    if($relacion->canton_id)
                    {
                        $tmp_canton = ($relacion->canton_id);
                    }

                    if($relacion->idCantonJurisdiccion)
                    {
                        print("tiene id,");
                        $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->find($relacion->idCantonJurisdiccion);
                    }
                    else
                    {
                        print("no tiene id,");
                        $cantonJurisdiccion = new AdmiCantonJurisdiccion();
                    }

                    $cantonJurisdiccion->setJurisdiccionId($entity->getId());
                    $cantonJurisdiccion->setCantonId($tmp_canton);
                    $cantonJurisdiccion->setMailTecnico($mailTecnico);
                    $cantonJurisdiccion->setIpReserva($ipReserva);
                    $cantonJurisdiccion->setEstado("Activo");
                    $cantonJurisdiccion->setUsrCreacion($strUsuario);
                    $cantonJurisdiccion->setFeCreacion(new \DateTime('now'));
                    $cantonJurisdiccion->setUsrUltMod($strUsuario);
                    $cantonJurisdiccion->setFeUltMod(new \DateTime('now'));

                    // Save
                    $em->persist($cantonJurisdiccion);
                    $em->flush();

                    $array_relaciones[] = $cantonJurisdiccion->getId();
                }

                $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->borrarDistintosEleccion($array_relaciones, $entity->getId(), $strUsuario);
                $em->getConnection()->commit();
            }
            catch(Exception $e)
            {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            return $this->redirect($this->generateUrl('admijurisdiccion_show', array('id' => $entity->getId())));
        }

        $parametros = array(
            'entity' => $entity,
            'form' => $form->createView()
        );

        if($error)
            $parametros['error'] = $error;

        return $this->render('seguridadBundle:SistModulo:edit.html.twig', $parametros);
    }

    /**
     * Documentación para el método 'getCantonesJurisdiccionesAction'.
     * 
     * Método para obtener el listado de cantones por Jurisdicción.
     *
     * @param int $id id de la jurisdicción 
     * 
     * @return Response Lista de Cantones.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     */
    public function getCantonesJurisdiccionesAction($id)
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');

        $intStart = '';
        $intLimit = '';

        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                        ->generarJsonCantonesJurisdicciones($id, "Activo", $intStart, $intLimit);
        
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }
    
    /**
     * Funcion que sirve para actualizar la jurisdiccion
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-01-2015
     * @Secure(roles="ROLE_117-8")
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:AdmiJurisdiccion')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiJurisdiccion entity.');
        }

        $em->getConnection()->beginTransaction();

        //jurisdiccion
        $entity->setEstado("Eliminado");
        $entity->setUsrUltMod($session->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $em->persist($entity);

        //canton jurisdiccion
        $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findBy(array("jurisdiccionId" => $entity->getId()));
        for($i = 0; $i < count($cantonJurisdiccion); $i++)
        {
            $cantonJurisdiccionObj = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->find($cantonJurisdiccion[$i]->getId());
            $cantonJurisdiccionObj->setEstado("Eliminado");
            $em->persist($cantonJurisdiccionObj);
        }

        $em->flush();
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('admijurisdiccion'));
    }

    /**
    * @Secure(roles="ROLE_117-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiJurisdiccion', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					//jurisdiccion
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($session->get('user'));
					$em->persist($entity);
				
					//canton jurisdiccion
					$cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findBy(array( "jurisdiccionId" =>$entity->getId()));
					for($i=0;$i<count($cantonJurisdiccion);$i++){
						$cantonJurisdiccionObj = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->find($cantonJurisdiccion[$i]->getId());
						$cantonJurisdiccionObj->setEstado("Eliminado");
						$em->persist($cantonJurisdiccionObj);
					}
                
					$em->flush();
                }
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_117-46")
     * 
     * Documentación para el método getEncontradosAction.
     * 
     * Función que obtiene el listado de las jurisdicciones por empresa.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-07-2016
     * @since   1.0
     * Se elimina la generación y el envío del entity manager.
     * 
     * @return Response Listado de jurisdicciones.
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $idEmpresa=$session->get('idEmpresa');
        
        $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiJurisdiccion')
            ->generarJsonJurisdicciones($nombre, $idEmpresa, $estado, $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método getJurisdiccionesAction
     * 
     * Función que obtiene el listado de las jurisdicciones por empresa.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-07-2016
     * @since   1.0
     * Se elimina la generación y el envío del entity manager.
     * 
     * @return Response Listado de jurisdicciones.
     */
    public function getJurisdiccionesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $empresaId=$session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                                       ->getRepository('schemaBundle:AdmiJurisdiccion')
                                       ->generarJsonJurisdicciones('', $empresaId, 'Eliminado', $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método getJurisdiccionesPorNombreAction
     * 
     * Función que obtiene el listado de las jurisdicciones por empresa.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-06-2018
     * 
     * @return Response Listado de jurisdicciones.
     */
    public function getJurisdiccionesPorNombreAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $objSession  = $objPeticion->getSession();
        $strCodEmpresa=$objSession->get('idEmpresa');
        
        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                                       ->getRepository('schemaBundle:AdmiJurisdiccion')
                                       ->generarJsonJurisdiccionesPorNombre("", $strCodEmpresa);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }    
}