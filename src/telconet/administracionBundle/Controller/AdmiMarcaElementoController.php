<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiMarcaElemento;
use telconet\schemaBundle\Form\AdmiMarcaElementoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiMarcaElementoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_118-1")
    */
    public function indexAction(){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiMarcaElemento')->findAll();

        return $this->render('administracionBundle:AdmiMarcaElemento:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function ajaxListAllAction(){
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $marcas = $em->getRepository('schemaBundle:AdmiMarcaElemento')->findAll();
        $i=1;
        foreach ($marcas as $marca){
            if($i % 2==0)
                    $class='k-alt';
            else
                    $class='';
            
            $urlVer = $this->generateUrl('admimarcaelemento_show', array('id' => $marca->getId()));
            $urlEditar = $this->generateUrl('admimarcaelemento_edit', array('id' => $marca->getId()));

            $arreglo[]= array(
                'id'=> $marca->getId(),
                'nombreMarcaElemento'=> $marca->getNombreMarcaElemento(),
                'descripcionMarcaElemento'=> $marca->getDescripcionMarcaElemento(),
                'estado' => $marca->getEstado(),
                'fechaCreacion'=> strval(date_format($marca->getFeCreacion(),"d/m/Y G:i")),
                'usuarioCreacion'=> $marca->getUsrCreacion(),
                'urlVer'=> $urlVer,
                'urlEditar'=> $urlEditar,
                'clase'=> $class
            );  
            $i++;
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'nombreMarcaElemento'=> "",
                    'descripcionMarcaElemento'=> "",
                    'estado' => "",
                    'fechaCreacion'=> "",
                    'usuarioCreacion'=> "",
                    'urlVer'=> "",
                    'urlEditar'=> "",
                    'clase'=> ""
                    );
        }		
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;	
    }
    
    /**
    * @Secure(roles="ROLE_118-6")
    */
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $marcaElemento = $em->find('schemaBundle:AdmiMarcaElemento', $id)) {
            throw new NotFoundHttpException('No existe la marca que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiMarcaElemento:show.html.twig', array(
            'marcaelemento'   => $marcaElemento,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_118-2")
    */
    public function newAction(){
        $entity = new AdmiMarcaElemento();
        $form   = $this->createForm(new AdmiMarcaElementoType(), $entity);

        return $this->render('administracionBundle:AdmiMarcaElemento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_118-3")
    */
    public function createAction(){
        $request = $this->get('request');
        $objSession  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiMarcaElemento();
        $form    = $this->createForm(new AdmiMarcaElementoType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($objSession->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admimarcaelemento_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiMarcaElemento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_118-4")
    */
    public function editAction($id){
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $marca = $em->find('schemaBundle:AdmiMarcaElemento', $id)) {
            throw new NotFoundHttpException('No existe la Marca que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiMarcaElementoType(), $marca);
//        $formulario->setData($marca);

        return $this->render('administracionBundle:AdmiMarcaElemento:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'marca'   => $marca));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_118-5")
    */
    public function updateAction($id){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiMarcaElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiMarcaElemento entity.');
        }

        $editForm   = $this->createForm(new AdmiMarcaElementoType(), $entity);

        $request = $this->getRequest();
        $objSession  = $request->getSession();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($objSession->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admimarcaelemento_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiMarcaElemento:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_118-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiMarcaElemento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiMarcaElemento entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($objSession->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admimarcaelemento'));
    }

    /**
    * @Secure(roles="ROLE_118-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiMarcaElemento', $id)) {
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
    * @Secure(roles="ROLE_118-46")
    */
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('nombre');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementos($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = 100;
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementos("","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosDslamAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"DSLAM"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosRadioAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"RADIO"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosPopAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"POP"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'getMarcasElementosTipoAction'.
     *
     * Obtiene registros de marcas de elementos segun el tipo de elemento
     * @return Response      $respuesta  listado de registros de marcas de elementos en objeto json
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 18-02-2015
     * 
     * Se cambian formatos de variables y para que la busqueda retorne solo un objeto.
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.2 8-12-2015
     * 
     * @since 1.0
     */
    public function getMarcasElementosTipoAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion       = $this->get('request');
        $tipoElemento   = $peticion->query->get('tipoElemento');
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        
        $objTipoElemento    = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => $tipoElemento));
        $objJson            = $this->getDoctrine()->getManager("telconet_infraestructura")
                               ->getRepository('schemaBundle:AdmiMarcaElemento')
                               ->generarJsonMarcasElementosPorTipo($objTipoElemento->getId(), "Activo", $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Documentación para el método 'getMarcasOltMigracionAction'.
     *
     * Obtiene registros de marcas de elementos segun el tipo de elemento excepto las marcas de tipo TELLION
     * @return Response      $objResponse  listado de registros de marcas de elementos en objeto json
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0 15-02-2023
     * 
     * @since 1.0
     */
    public function getMarcasOltMigracionAction()
    {
        $objResponse      = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objRequest         = $this->get('request');
        $strTipoElemento    = $objRequest->query->get('tipoElemento');
        $strStart              = $objRequest->query->get('start');
        $strLimit              = $objRequest->query->get('limit');
        
        $objTipoElemento    = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                            ->findOneBy(array("nombreTipoElemento" => $strTipoElemento));
        $objJson            = $this->getDoctrine()->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:AdmiMarcaElemento')
                            ->generarJsonMarcasElementosPorTipo($objTipoElemento->getId(), "Activo", $strStart, $strLimit);

        $objParametroCab      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                            array('nombreParametro' => 'MIGRACION_OLT_ALTA_DENSIDAD',
                                            'estado'                => 'Activo'));

        $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                        array("parametroId" => $objParametroCab->getId(),
                                        "estado"      => "Activo",
                                        "valor1"      => "TIPO_MARCA"));
        
        $objJsonResponse = array();
        $objMarcas = json_decode($objJson);
        $objMarcasEncontrados = $objMarcas->encontrados;
        foreach ($objMarcasEncontrados as $objMarca)
        {
            if($objMarca->nombreMarcaElemento != $objParametroDet->getValor2())
            {
                $objJsonResponse[]=array('idMarcaElemento' =>$objMarca->idMarcaElemento,
                                        'nombreMarcaElemento' =>trim($objMarca->nombreMarcaElemento),
                                        'estado' => $objMarca->estado,
                                        'action1' => $objMarca->action1,
                                        'action2' => $objMarca->action2,
                                        'action3' => $objMarca->action3);
            }
        }

        $objData=json_encode($objJsonResponse);
        $strResultado= '{"total":"'.count($objJsonResponse).'","encontrados":'.$objData.'}';

        $objResponse->setContent($strResultado);

        return $objResponse;
    }

    public function getMarcasElementosSplitterAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"SPLITTER"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosCajaAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"CAJA DISPERSION"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que obtiene las marcas de los elementos
     * por tipo CPE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     */
    public function getMarcasElementosCpeAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
//        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array( "nombreTipoElemento" =>"CPE"));
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipoCpe("CPE","Activo",$start,100);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getMarcasElementosServidorAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')
                           ->findOneBy(array("nombreTipoElemento" => "SERVIDOR", "estado"=>"Activo"));

        $peticion = $this->get('request');

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiMarcaElemento')
            ->generarJsonMarcasElementosPorTipo($tipoElemento->getId(), "Activo", $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}