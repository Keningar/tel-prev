<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiSintoma;
use telconet\schemaBundle\Form\AdmiSintomaType;

use telconet\schemaBundle\Form\InfoEmpresaGrupo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiSintomaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_56-1")
    */
    public function indexAction()
    {
	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_56-8'))
	{
	    $rolesPermitidos[] = 'ROLE_56-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_56-9'))
	{
	    $rolesPermitidos[] = 'ROLE_56-9';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_56-6'))
	{
	    $rolesPermitidos[] = 'ROLE_56-6';
	}
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");

        $entities = $em->getRepository('schemaBundle:AdmiSintoma')->findAll();

        return $this->render('administracionBundle:AdmiSintoma:index.html.twig', array(
            'item' => $entityItemMenu,
            'sintoma' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * showAction
    *
    * Esta funcion muestra el formulario para presentar la informacion de un sintoma
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para incluir los tipos de casos en la administracion de sintomas
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-6")
    */
    public function showAction($id)
    {
        $peticion       = $this->get('request');
        $em             = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");
        $nombreTipoCaso = "";
        if (null == $sintoma = $em->find('schemaBundle:AdmiSintoma', $id))
        {
            throw new NotFoundHttpException('No existe el AdmiSintoma que se quiere mostrar');
        }

        if($sintoma->getTipoCasoId())
        {
            $entityTipoCaso = $em->getRepository('schemaBundle:AdmiTipoCaso')->find($sintoma->getTipoCasoId());
            $nombreTipoCaso = $entityTipoCaso->getNombreTipoCaso();
        }
        return $this->render('administracionBundle:AdmiSintoma:show.html.twig', array(
            'item'           => $entityItemMenu,
            'sintoma'        => $sintoma,
            'flag'           => $peticion->get('flag'),
            'nombreTipoCaso' => $nombreTipoCaso));
    }
    
    /**
    * newAction
    *
    * Esta funcion muestra el formulario para crear un sintoma
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para incluir los tipos de casos en la administracion de sintomas
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-2")
    */
    public function newAction()
    {
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $em_soporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");
        $arrayTiposCasos = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();

        $entity          = new AdmiSintoma();
        $form            = $this->createForm(new AdmiSintomaType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);

        return $this->render('administracionBundle:AdmiSintoma:new.html.twig', array(
            'item'    => $entityItemMenu,
            'sintoma' => $entity,
            'form'    => $form->createView()
        ));
    }
    
    /**
    * createAction
    *
    * Esta funcion realiza la creacion de un sintoma
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para incluir los tipos de casos en la administracion de sintomas
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-3")
    */
    public function createAction()
    {
        $request         = $this->get('request');
        $em              = $this->get('doctrine')->getManager('telconet_soporte');
        $em_comercial    = $this->get('doctrine')->getManager('telconet');
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");
        $session         = $request->getSession();
        $codEmpresa      = $session->get('idEmpresa');
        
        $infoEmp = $em_comercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($codEmpresa);
        $entity  = new AdmiSintoma();
        $form    = $this->createForm(new AdmiSintomaType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);
        
        $form->bind($request);
        
        if ($form->isValid())
        {
            $em->getConnection()->beginTransaction();

            $entity->setEmpresaCod($infoEmp->getId());
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));

            $em->persist($entity);            
            $em->flush();                        
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('admisintoma_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiSintoma:new.html.twig', array(
            'item'    => $entityItemMenu,
            'sintoma' => $entity,
            'form'    => $form->createView()
        ));
        
    }
    
    /**
    * editAction
    *
    * Esta funcion muestra el formulario para editar un sintoma
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para incluir los tipos de casos en la administracion de sintomas
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-4")
    */
    public function editAction($id)
    {
        $em              = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        if (null == $sintoma = $em->find('schemaBundle:AdmiSintoma', $id))
        {
            throw new NotFoundHttpException('No existe el AdmiSintoma que se quiere modificar');
        }

        $formulario = $this->createForm(new AdmiSintomaType(array('arrayTiposCasos'=>$arrayTiposCasos)), $sintoma);

        return $this->render('administracionBundle:AdmiSintoma:edit.html.twig', array(
            'item'      => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'sintoma'   => $sintoma));
    }
    
    /**
    * updateAction
    *
    * Esta funcion realiza la actualizacion de un sintoma
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para incluir los tipos de casos en la administracion de sintomas
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-5")
    */
    public function updateAction($id)
    {
        $em              = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("56", "1");
        $entity          = $em->getRepository('schemaBundle:AdmiSintoma')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiSintoma entity.');
        }

        $editForm   = $this->createForm(new AdmiSintomaType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);
        $request    = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid())
        {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/

            if ($request->get("editTipoCaso") == 'todos')
            {
                $entity->setTipoCasoId(null);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admisintoma_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiSintoma:edit.html.twig',array(
            'item'      => $entityItemMenu,
            'sintoma'   => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_56-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiSintoma')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiSintoma entity.');
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

        return $this->redirect($this->generateUrl('admisintoma'));
    }

    /**
    * @Secure(roles="ROLE_56-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiSintoma', $id)) {
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
    
    /**
    * 
    * Se agrega variable para filtrar los sintomas por departamento
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.2 19-11-2020
    *
    * gridAction
    *
    * Esta funcion llena el grid de la consulta de Sintomas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para vizualizar los Sintomas segun el Tipo ce Caso que se selecciono
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_56-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $session     = $peticion->getSession();
        $parametros  = array();
        $codEmpresa  = $session->get('idEmpresa');

        $intDepartamento = $session->get('idDepartamento');
        
        $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre      = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado      = $peticion->query->get('estado');
        $tipoCaso    = $peticion->query->get('tipoCaso');
        //Se obtiene el id del caso en el escenario de que se gestione con uno y asi poder obtener la empresa 
        //del mismo y obtener los sintomas relacionadas a esta
        //de lo contrario se obtiene
        //informacion de los sintomas con la empresa en sesion
        $caso  = $peticion->query->get('caso')?$peticion->query->get('caso'):'';
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');                    

        //Se verifica que si se quiere obtener los sintomas para relacionar un caso
        //Se valide con la empresa de la cual proviene el mismo y así mostrar sus
        //sintomas segun la empresa en el que fue creado
        if($caso != '')
        {
            $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
            if($caso)
            {
                $codEmpresa = $caso->getEmpresaCod();
                $tipoCaso   = $caso->getTipoCasoId()->getid();
            }
        }
        //Se arma un array de parametros para enviarlos al Repositorio
        $parametros["nombre"]     = $nombre;
        $parametros["estado"]     = $estado;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["codEmpresa"] = $codEmpresa ? $codEmpresa : "";
        $parametros["tipoCaso"]   = $tipoCaso;
        $parametros["depart"]     = $intDepartamento;
        
        $objJson = $em->getRepository('schemaBundle:AdmiSintoma')
                      ->generarJson($parametros);
                          
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}