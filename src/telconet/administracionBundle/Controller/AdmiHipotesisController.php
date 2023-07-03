<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiHipotesis;
use telconet\schemaBundle\Form\AdmiHipotesisType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiHipotesisController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_55-1")
    */
    public function indexAction()
    {
	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_55-8'))
	{
	    $rolesPermitidos[] = 'ROLE_55-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_55-9'))
	{
	    $rolesPermitidos[] = 'ROLE_55-9';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_55-6'))
	{
	    $rolesPermitidos[] = 'ROLE_55-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_55-4'))
	{
	    $rolesPermitidos[] = 'ROLE_55-4';
	}
	
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");

        $entities = $em->getRepository('schemaBundle:AdmiHipotesis')->findAll();

        return $this->render('administracionBundle:AdmiHipotesis:index.html.twig', array(
            'item' => $entityItemMenu,
            'hipotesis' => $entities,
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
    * @Secure(roles="ROLE_55-6")
    */
    public function showAction($id)
    {
        $peticion       = $this->get('request');
        $em             = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");
        $nombreTipoCaso = "";

        if (null == $hipotesis = $em->find('schemaBundle:AdmiHipotesis', $id)) {
            throw new NotFoundHttpException('No existe el AdmiHipotesis que se quiere mostrar');
        }

        if($hipotesis->getTipoCasoId())
        {
            $entityTipoCaso = $em->getRepository('schemaBundle:AdmiTipoCaso')->find($hipotesis->getTipoCasoId());
            $nombreTipoCaso = $entityTipoCaso->getNombreTipoCaso();
        }

        return $this->render('administracionBundle:AdmiHipotesis:show.html.twig', array(
            'item'           => $entityItemMenu,
            'hipotesis'      => $hipotesis,
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
    * @Secure(roles="ROLE_55-2")
    */
    public function newAction()
    {
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $em_soporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayTiposCasos = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");
        $entity          = new AdmiHipotesis();
        $form            = $this->createForm(new AdmiHipotesisType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);

        return $this->render('administracionBundle:AdmiHipotesis:new.html.twig', array(
            'item'      => $entityItemMenu,
            'hipotesis' => $entity,
            'form'      => $form->createView()
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
    * @Secure(roles="ROLE_55-3")
    */
    public function createAction()
    {
        $request         = $this->get('request');
        $em              = $this->get('doctrine')->getManager('telconet_soporte');
        $em_comercial    = $this->get('doctrine')->getManager('telconet');
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");
        $entity         = new AdmiHipotesis();
        $form           = $this->createForm(new AdmiHipotesisType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);
                
        $session    = $request->getSession();
        $codEmpresa = $session->get('idEmpresa');
       
        $form->bind($request);
        
        if ($form->isValid())
        {
            $em->getConnection()->beginTransaction();

            $entity->setEmpresaCod($codEmpresa);
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));

            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admihipotesis_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiHipotesis:new.html.twig', array(
            'item'      => $entityItemMenu,
            'hipotesis' => $entity,
            'form'      => $form->createView()
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
    * @Secure(roles="ROLE_55-4")
    */
    public function editAction($id)
    {
        $em              = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");

        if (null == $hipotesis = $em->find('schemaBundle:AdmiHipotesis', $id))
        {
            throw new NotFoundHttpException('No existe el AdmiHipotesis que se quiere modificar');
        }

        $formulario = $this->createForm(new AdmiHipotesisType(array('arrayTiposCasos'=>$arrayTiposCasos)), $hipotesis);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiHipotesis:edit.html.twig', array(
            'item'      => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'hipotesis' => $hipotesis));
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
    * @Secure(roles="ROLE_55-5")
    */
    public function updateAction($id)
    {
        $em              = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $arrayTiposCasos = $em->getRepository('schemaBundle:AdmiTipoCaso')->getArrayTipoCaso();
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("55", "1");
        $entity          = $em->getRepository('schemaBundle:AdmiHipotesis')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiHipotesis entity.');
        }

        $editForm = $this->createForm(new AdmiHipotesisType(array('arrayTiposCasos'=>$arrayTiposCasos)), $entity);
        $request  = $this->getRequest();

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

            return $this->redirect($this->generateUrl('admihipotesis_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiHipotesis:edit.html.twig',array(
            'item'      => $entityItemMenu,
            'hipotesis' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_55-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiHipotesis')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiHipotesis entity.');
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

        return $this->redirect($this->generateUrl('admihipotesis'));
    }

    /**
    * @Secure(roles="ROLE_55-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiHipotesis', $id)) {
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
    * Se agrega variable para filtrar las hipotesis por departamento
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.3 19-11-2020
    *
    * Actualización: Se agrega enviar por parámetros padreHipotesis, buscarSinPadre y buscarTodosNivel2 a la función que obtiene la data
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.2 06-09-2019 
    * 
    * gridAction
    *
    * Esta funcion llena el grid de la consulta de Hipotesis
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para vizualizar las Hipotesis segun el Tipo ce Caso que se selecciono
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_55-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $session              = $peticion->getSession();
        $parametros           = array();
        $queryNombre          = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre               = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado               = $peticion->query->get('estado');    
        $intPadreHipotesis    = $peticion->query->get('padreHipotesis');
        $strBuscarSinPadre    = $peticion->query->get('buscarSinPadre');

        $intDepartamento      = $session->get('idDepartamento');

        //Se obtiene el id del caso en el escenario de que se gestione con uno y asi poder obtener la empresa 
        //del mismo y obtener las hipotesis relacionadas a esta
        //de lo contrario se obtiene
        //informacion de las hipotesis con la empresa en sesion
        $caso        = $peticion->query->get('caso')?$peticion->query->get('caso'):'';
        
        $codEmpresa  = $session->get('idEmpresa');
        
        //Se verifica que si se quiere obtener las Hipotesis para relacionar un caso
        //Se valide con la empresa de la cual proviene el mismo y así mostrar sus
        //hipotesis segun la empresa en el que fue creado
        if($caso != '')
        {
            $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
            if($caso)
            {
                $codEmpresa = $caso->getEmpresaCod();
                $tipoCaso   = $caso->getTipoCasoId()->getid();
            }
        }
        
        $intStart = $peticion->query->get('start');
        $intLimit = $peticion->query->get('limit');
        
        //Se arma un array de parametros para enviarlos al Repositorio
        $parametros["nombre"]            = $nombre;
        $parametros["estado"]            = $estado;
        $parametros["codEmpresa"]        = $codEmpresa;
        $parametros["tipoCaso"]          = $tipoCaso;
        $parametros["padreHipotesis"]    = $intPadreHipotesis;
        $parametros["buscarSinPadre"]    = $strBuscarSinPadre;
        $parametros["depart"]            = $intDepartamento;
        $parametros["start"]            = $intStart;
        $parametros["limit"]            = $intLimit;


        $objJson = $em->getRepository('schemaBundle:AdmiHipotesis')
                      ->generarJson($parametros);
                      
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * Muestra las opciones para poder editar el arbol de hipotesis
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 06-09-2019
     * @since 1.0
     * @return render a adminArbolHipotesis.html.twig
     */
    /**
    * @Secure(roles="ROLE_439-6702")
    */
    public function adminArbolHipotesisAction()
    {
        $objPeticion          = $this->getRequest();
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objSession           = $objPeticion->getSession();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_439-6697'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6697';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6698'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6698';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6699'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6699';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6700'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6700';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6701'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6701';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6717'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6717';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6718'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6718';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_439-6719'))
        {
            $arrayRolesPermitidos[] = 'ROLE_439-6719';
        }
        $strConsultaArbol = 'N';
        $arrayAdmiParametro = $objEmSoporte->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strCodEmpresa);
        if($arrayAdmiParametro['valor2']==='S')
        {
            $strConsultaArbol = 'S';
        }
        return $this->render('administracionBundle:AdmiHipotesis:adminArbolHipotesis.html.twig', array(
            'consultaArbol'   => $strConsultaArbol,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     * Permite crear una hipótesis según los parametros que recibe
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 06-09-2019
     * @since 1.0
     * @return render objeto response
     */
    public function crearHipotesisArbolAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion       = $this->getRequest();
        $objEmSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $objSession        = $objPeticion->getSession();
        $strNombre         = $objPeticion->get('nombre');
        $intPadreHipotesis = $objPeticion->get('padreHipotesis') ? $objPeticion->get('padreHipotesis') : "0";
        $strDescripcion    = $objPeticion->get('descripcion');
        $intTipoCaso       = $objPeticion->get('tipoCaso');
        $strCodEmpresa     = $objSession->get('idEmpresa');
        try
        {
            $objEmSoporte->getConnection()->beginTransaction();
            $objAdmiHipotesis = new AdmiHipotesis();
            $objAdmiHipotesis->setEmpresaCod($strCodEmpresa);
            $objAdmiHipotesis->setHipotesisId($intPadreHipotesis);
            $objAdmiHipotesis->setNombreHipotesis($strNombre);
            $objAdmiHipotesis->setDescripcionHipotesis($strDescripcion);
            if( isset($intTipoCaso) && intval($intTipoCaso) > 0)
            {
                $objAdmiHipotesis->setTipoCasoId($intTipoCaso);
            }
            elseif( isset($intTipoCaso) && intval($intTipoCaso) === 0)
            {
                $objAdmiHipotesis->setTipoCasoId(null);
            }
            $objAdmiHipotesis->setEstado('Activo');
            $objAdmiHipotesis->setFeCreacion(new \DateTime('now'));
            $objAdmiHipotesis->setUsrCreacion($objSession->get('user'));
            $objAdmiHipotesis->setFeUltMod(new \DateTime('now'));
            $objAdmiHipotesis->setUsrUltMod($objSession->get('user'));
            $objEmSoporte->persist($objAdmiHipotesis);
            $objEmSoporte->flush();
            $objEmSoporte->commit();
            $objResultado = json_encode(array('success'=>true));
        }
        catch (Exception $e)
        {
            $objEmSoporte->getConnection()->rollback();
            $objEmSoporte->getConnection()->close();
            $objResultado = json_encode(array('success'=>false,'mensaje'=>'Ocurrió un problema y no se pudo crear la hipótesis'));
            error_log($e->getMessage());
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
    /**
     * Permite editar una hipótesis según los parametros que recibe
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 06-09-2019
     * @since 1.0
     * @return render objeto response
     */
    public function editarHipotesisArbolAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion       = $this->getRequest();
        $objEmSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $objSession        = $objPeticion->getSession();
        $strNombre         = $objPeticion->get('nombre');
        $intPadreHipotesis = $objPeticion->get('padreHipotesis') ? $objPeticion->get('padreHipotesis') : "0";
        $strDescripcion    = $objPeticion->get('descripcion');
        $intTipoCaso       = $objPeticion->get('tipoCaso');
        $intIdHipotesis    = $objPeticion->get('idHipotesis');
        try
        {
            $objEmSoporte->getConnection()->beginTransaction();
            $objAdmiHipotesis = $objEmSoporte->getRepository('schemaBundle:AdmiHipotesis')->find($intIdHipotesis);

            if (!$objAdmiHipotesis)
            {
                throw $this->createNotFoundException('Unable to find AdmiHipotesis entity.');
            }
            if( isset($intPadreHipotesis) && intval($intPadreHipotesis) > 0 )
            {
                $objAdmiHipotesis->setHipotesisId($intPadreHipotesis);
            }
            if(isset($strNombre) && $strNombre !== "")
            {
                $objAdmiHipotesis->setNombreHipotesis($strNombre);
            }
            if(isset($strDescripcion) && $strDescripcion !== "")
            {
                $objAdmiHipotesis->setDescripcionHipotesis($strDescripcion);
            }
            if( isset($intTipoCaso) && intval($intTipoCaso) > 0)
            {
                $objAdmiHipotesis->setTipoCasoId($intTipoCaso);
            }
            elseif( isset($intTipoCaso) && intval($intTipoCaso) === 0)
            {
                $objAdmiHipotesis->setTipoCasoId(null);
            }
            $objAdmiHipotesis->setFeUltMod(new \DateTime('now'));
            $objAdmiHipotesis->setUsrUltMod($objSession->get('user'));
            $objEmSoporte->persist($objAdmiHipotesis);
            $objEmSoporte->flush();
            $objEmSoporte->commit();
            $objResultado = json_encode(array('success'=>true));
        }
        catch (Exception $e)
        {
            $objEmSoporte->getConnection()->rollback();
            $objEmSoporte->getConnection()->close();
            $objResultado = json_encode(array('success'=>false,'mensaje'=>'Ocurrió un problema y no se pudo editar la hipótesis'));
            error_log($e->getMessage());
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
    /**
     * Permite eliminar una hipótesis según id de hipótesis que recibe por parámetro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 06-09-2019
     * @since 1.0
     * @return render objeto response
     */
    public function eliminarHipotesisArbolAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion       = $this->getRequest();
        $objSession        = $objPeticion->getSession();
        $objEmSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $intIdHipotesis    = $objPeticion->get('idHipotesis');
        try
        {
            $objEmSoporte->getConnection()->beginTransaction();
            $objAdmiHipotesis = $objEmSoporte->getRepository('schemaBundle:AdmiHipotesis')->find($intIdHipotesis);

            if (!$objAdmiHipotesis)
            {
                throw $this->createNotFoundException('Unable to find AdmiHipotesis entity.');
            }
            $arrayAdmiHipotesisArbolH = $objEmSoporte->getRepository('schemaBundle:AdmiHipotesis')
                                                     ->findBy(array('hipotesisId'=>$intIdHipotesis));
            foreach($arrayAdmiHipotesisArbolH as $objHipotesisH)
            {
                $objHipotesisH->setFeUltMod(new \DateTime('now'));
                $objHipotesisH->setUsrUltMod($objSession->get('user'));
                $objHipotesisH->setEstado('Eliminado');
                $objEmSoporte->persist($objHipotesisH);
                $arrayAdmiHipotesisArbolN = $objEmSoporte->getRepository('schemaBundle:AdmiHipotesis')
                                                         ->findBy(array('hipotesisId'=>$objHipotesisH->getId()));
                foreach($arrayAdmiHipotesisArbolN as $objHipotesisN)
                {
                    $objHipotesisN->setFeUltMod(new \DateTime('now'));
                    $objHipotesisN->setUsrUltMod($objSession->get('user'));
                    $objHipotesisN->setEstado('Eliminado');
                    $objEmSoporte->persist($objHipotesisN);
                }
            }
            $objAdmiHipotesis->setFeUltMod(new \DateTime('now'));
            $objAdmiHipotesis->setUsrUltMod($objSession->get('user'));
            $objAdmiHipotesis->setEstado('Eliminado');
            $objEmSoporte->persist($objAdmiHipotesis);
            $objEmSoporte->flush();
            $objEmSoporte->commit();
            $objResultado = json_encode(array('success'=>true));
        }
        catch (Exception $e)
        {
            $objEmSoporte->getConnection()->rollback();
            $objEmSoporte->getConnection()->close();
            $objResultado = json_encode(array('success'=>false,'mensaje'=>'Ocurrió un problema y no se pudo editar la hipótesis'));
            error_log($e->getMessage());
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
}