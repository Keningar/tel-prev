<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Form\ClienteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\comercialBundle\Service\InfoPersonaFormaContactoService;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
class SuspensionTratamientoController extends Controller implements TokenAuthenticatedController
{
    /**
     * 
     *
     * Documentación para el método 'indexAction'.
     * Muestra la pagina principal del modulo de Suspension de Tratamiento en Derechos del Titular
     *
     * @return Response.
     *
     * @version 1.0 Version Inicial
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 19-02-2021 - Se agrega perfil para habilitar y deshabilitar los usuarios en el servidor del Tacacs
     */
    public function indexAction()
    {
        $objSession = $this->get('request')->getSession();
		
        $objEmSeguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $objEmSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("494", "1");    	
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
       
        $objEntity = new InfoPersona();
        
      
        $objForm = $this->createForm(new ClienteType(), $objEntity);
      
        return $this->render('administracionBundle:Oposicion:index.html.twig', array(
                    'entity' => $objEntity,
                    'form' => $objForm->createView(),
                    'opcion' => $entityItemMenu->getNombreItemMenu()
                ));
    }


}