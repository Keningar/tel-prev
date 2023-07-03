<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Form\AdmiFormaPagoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiFormaPagoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_43-1")
    */
    public function indexAction()
    {
        //Se agregan roles
        if(true === $this->get('security.context')->isGranted('ROLE_43-4'))
        {
            $strRolesPermitidos[] = 'ROLE_43-4';
        }

        if(true === $this->get('security.context')->isGranted('ROLE_43-6'))
        {
            $strRolesPermitidos[] = 'ROLE_43-6';
        }

        if(true === $this->get('security.context')->isGranted('ROLE_43-8'))
        {
            $strRolesPermitidos[] = 'ROLE_43-8'; 
        }

        if(true === $this->get('security.context')->isGranted('ROLE_43-9'))
        {
            $strRolesPermitidos[] = 'ROLE_43-9';  
        }

        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");

        $entities = $em->getRepository('schemaBundle:AdmiFormaPago')->findAll();

        return $this->render('administracionBundle:AdmiFormaPago:index.html.twig', array(
            'item' => $entityItemMenu,
            'formapago' => $entities,
            'rolesPermitidos' => $strRolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_43-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");

        if (null == $formapago = $em->find('schemaBundle:AdmiFormaPago', $id)) {
            throw new NotFoundHttpException('No existe el AdmiFormaPago que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiFormaPago:show.html.twig', array(
            'item' => $entityItemMenu,
            'formapago'   => $formapago,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
     * @Secure(roles="ROLE_43-2")
     * 
     * Documentación para el método newAction
     * 
     * Método que genera la interfaz de usuario con el formulario respectivo para el ingreso de la forma de pago
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-11-2016 - Se modifica para obtener los tipos de forma de pagos de la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' usando el método
     *                           'getOpcionesTipoFormaPago' del service 'administracion.Utilidades'.
     * 
     * @return Response
     */
    public function newAction()
    {
        $emSeguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $serviceAdmiUtilidades = $this->get('administracion.Utilidades');
        
        $arrayEntityFormaPago    = $serviceAdmiUtilidades->getOpcionesTipoFormaPago();
        
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");
        $entity         = new AdmiFormaPago();
        $form           = $this->createForm(new AdmiFormaPagoType($arrayEntityFormaPago), $entity);

        return $this->render('administracionBundle:AdmiFormaPago:new.html.twig', array( 'item'      => $entityItemMenu,
                                                                                        'formapago' => $entity,
                                                                                        'form'      => $form->createView() ));
    }
    
    /**
     * @Secure(roles="ROLE_43-3")
     * 
     * Documentación para el método 'createAction'
     * 
     * Método que guarda el registro en base de la creación de la forma de pago ingresada por el usuario
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-11-2016 - Se modifica para obtener los tipos de forma de pagos de la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' usando el método
     *                           'getOpcionesTipoFormaPago' del service 'administracion.Utilidades' para poder validar el formulario ingresado por 
     *                           el usuario.
     * 
     * @return Response
     */
    public function createAction()
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $emGeneral             = $this->get('doctrine')->getManager('telconet_general');
        $emSeguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu        = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");
        $serviceUtil           = $this->get('schema.Util');
        $serviceAdmiUtilidades = $this->get('administracion.Utilidades');
        $arrayEntityFormaPago  = $serviceAdmiUtilidades->getOpcionesTipoFormaPago();
        
        $entity  = new AdmiFormaPago();
        $form    = $this->createForm(new AdmiFormaPagoType($arrayEntityFormaPago), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($objSession->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($objSession->get('user'));
        
        $form->bind($objRequest);
        
        if ($form->isValid())
        {
            $emGeneral->getConnection()->beginTransaction();
            
            try
            {
                $emGeneral->persist($entity);
                $emGeneral->flush();
                $emGeneral->getConnection()->commit();
            
                return $this->redirect($this->generateUrl('admiformapago_show', array('id' => $entity->getId())));
            } 
            catch(\Exception $e)
            {
                $serviceUtil->insertError( 'Telcos+', 
                                           'Crear Tipo Forma de Pago', 
                                           'Error al guardar el tipo de forma de pago. '.$e->getMessage(), 
                                           $objSession->get('user'), 
                                           $objRequest->getClientIp() );

                if( $emGeneral->getConnection()->isTransactionActive() )
                {
                    $emGeneral->getConnection()->rollback();
                }
                
                $emGeneral->getConnection()->close();
            }
        }
        
        return $this->render('administracionBundle:AdmiFormaPago:new.html.twig', array(
            'item' => $entityItemMenu,
            'formapago' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_43-4")
     * 
     * Documentación para el método newAction
     * 
     * Método que genera la interfaz de usuario con el formulario respectivo para el ingreso de la forma de pago
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-11-2016 - Se modifica para obtener los tipos de forma de pagos de la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' usando el método
     *                           'getOpcionesTipoFormaPago' del service 'administracion.Utilidades'.
     * 
     * @return Response
     */
    public function editAction($id)
    {
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu        = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");
        $serviceAdmiUtilidades = $this->get('administracion.Utilidades');
        $arrayEntityFormaPago  = $serviceAdmiUtilidades->getOpcionesTipoFormaPago();

        if (null == $formapago = $emGeneral->find('schemaBundle:AdmiFormaPago', $id))
        {
            throw new NotFoundHttpException('No existe el AdmiFormaPago que se quiere modificar');
        }
        
        $formulario = $this->createForm(new AdmiFormaPagoType($arrayEntityFormaPago), $formapago);

        return $this->render('administracionBundle:AdmiFormaPago:edit.html.twig', array( 'item'      => $entityItemMenu,
                                                                                         'edit_form' => $formulario->createView(),
                                                                                         'formapago' => $formapago ));
    }
    
    /**
     * @Secure(roles="ROLE_43-5")
     * 
     * Documentación para el método 'updateAction'
     * 
     * Método que actualiza el registro en base de la forma de pago modificada por el usuario
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-11-2016 - Se modifica para obtener los tipos de forma de pagos de la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' usando el método
     *                           'getOpcionesTipoFormaPago' del service 'administracion.Utilidades' para poder validar el formulario ingresado por 
     *                           el usuario.
     * 
     * @return Response
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("43", "1");
        $entity = $em->getRepository('schemaBundle:AdmiFormaPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiFormaPago entity.');
        }

        $serviceAdmiUtilidades = $this->get('administracion.Utilidades');
        $arrayEntityFormaPago  = $serviceAdmiUtilidades->getOpcionesTipoFormaPago();
        $editForm              = $this->createForm(new AdmiFormaPagoType($arrayEntityFormaPago), $entity);

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

            return $this->redirect($this->generateUrl('admiformapago_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiFormaPago:edit.html.twig',array(
            'item' => $entityItemMenu,
            'formapago'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_43-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiFormaPago')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiFormaPago entity.');
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

        return $this->redirect($this->generateUrl('admiformapago'));
    }

    /**
    * @Secure(roles="ROLE_43-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiFormaPago', $id)) {
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
    * @Secure(roles="ROLE_43-7")
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
            ->getRepository('schemaBundle:AdmiFormaPago')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getFormaPagoJsonAction, llama al metodo generarJsonFormaPago el cual retorna un json con la data y el total de datos
     * @version 1.0 Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0 19-01.2015
     * @return Response retorna un json armado con las formas de pago
     */
    public function getFormaPagoJsonAction()
    {
        $jsonFormaPagos = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiFormaPago')
                                                                      ->generarJsonFormaPago("Activo", 0, 100);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($jsonFormaPagos);
        return $objRespuesta;
    }//getFormaPagoJsonAction

}