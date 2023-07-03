<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoElemento;
use telconet\schemaBundle\Form\AdmiTipoElementoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdmiTipoElementoController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Funcion que sirve para cargar la pagina inicial de la administracion
     * de Tipo de Elemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-02-2015
     * @Secure(roles="ROLE_127-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_127-1'))
        {
            $rolesPermitidos[] = 'ROLE_127-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-46'))
        {
            $rolesPermitidos[] = 'ROLE_127-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-6'))
        {
            $rolesPermitidos[] = 'ROLE_127-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-2'))
        {
            $rolesPermitidos[] = 'ROLE_127-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-3'))
        {
            $rolesPermitidos[] = 'ROLE_127-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-4'))
        {
            $rolesPermitidos[] = 'ROLE_127-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-5'))
        {
            $rolesPermitidos[] = 'ROLE_127-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-8'))
        {
            $rolesPermitidos[] = 'ROLE_127-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_127-9'))
        {
            $rolesPermitidos[] = 'ROLE_127-9'; //delete ajax
        }

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiTipoElemento')->findAll();

        return $this->render('administracionBundle:AdmiTipoElemento:index.html.twig', array(
                'entities'        => $entities,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    public function ajaxListAllAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $tipos = $em->getRepository('schemaBundle:AdmiTipoElemento')->findAll();
        $i=1;
        foreach ($tipos as $tipo){
            if($i % 2==0)
                    $class='k-alt';
            else
                    $class='';
            
            $urlVer = $this->generateUrl('admitipoelemento_show', array('id' => $tipo->getId()));
            $urlEditar = $this->generateUrl('admitipoelemento_edit', array('id' => $tipo->getId()));

            $arreglo[]= array(
                'id'=> $tipo->getId(),
                'nombreTipoElemento'=> $tipo->getNombreTipoElemento(),
                'descripcionTipoElemento'=> $tipo->getDescripcionTipoElemento(),
                'estado' => $tipo->getEstado(),
                'fechaCreacion'=> strval(date_format($tipo->getFeCreacion(),"d/m/Y G:i")),
                'usuarioCreacion'=> $tipo->getUsrCreacion(),
                'urlVer'=> $urlVer,
                'urlEditar'=> $urlEditar,
                'clase'=> $class
            );  
            $i++;
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'nombreTipoElemento'=> "",
                    'descripcionTipoElemento'=> "",
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
    * @Secure(roles="ROLE_127-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipoElemento = $em->find('schemaBundle:AdmiTipoElemento', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Elemento que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoElemento:show.html.twig', array(
            'tipoelemento'   => $tipoElemento,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_127-2")
    */
    public function newAction()
    {
        $entity = new AdmiTipoElemento();
        $form   = $this->createForm(new AdmiTipoElementoType(), $entity);

        return $this->render('administracionBundle:AdmiTipoElemento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para crear un registro de AdmiTipoElemento en la base
     * 
     * @author John Vera <javera@telconet.ec 
     * @version 1.1 17-10-2016 Se elimina registros de modificacion en ingreso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-02-2015
     * @Secure(roles="ROLE_127-3")
     */
    public function createAction()
    {
        $request            = $this->get('request');
        $session            = $request->getSession();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $parametros         = $request->request->get('telconet_schemabundle_admitipoelementotype');
        $parametroDetObj    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($parametros['esDe']);
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity             = new AdmiTipoElemento();
        $form               = $this->createForm(new AdmiTipoElementoType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($session->get('user'));

        $entity->setEsDe($parametroDetObj->getValor1());
        
        $form->bind($request);
        
        if($form->isValid())
        {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('admitipoelemento_show', array('id' => $entity->getId())));
        }

        return $this->render('administracionBundle:AdmiTipoElemento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));        
    }
    
    /**
    * @Secure(roles="ROLE_127-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipo = $em->find('schemaBundle:AdmiTipoElemento', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Elemento que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoElementoType(), $tipo);
//        $formulario->setData($tipo);

        return $this->render('administracionBundle:AdmiTipoElemento:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'tipo'   => $tipo));
    }
    
    /**
     * Funcion que sirve para actualizar un registro de AdmiTipoElemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-02-2015
     * @Secure(roles="ROLE_127-5")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        $entity = $em->getRepository('schemaBundle:AdmiTipoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoElemento entity.');
        }
        
        $request = $this->get('request');
        $session    = $request->getSession();
        $parametros = $request->request->get('telconet_schemabundle_admitipoelementotype');
        $parametroDetObj = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($parametros['esDe']);
        
        $editForm   = $this->createForm(new AdmiTipoElementoType(), $entity);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));
            $entity->setEsDe($parametroDetObj->getValor1());
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admitipoelemento_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoElemento:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_127-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiTipoElemento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoElemento entity.');
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

        return $this->redirect($this->generateUrl('admitipoelemento'));
    }

    /**
     * Funcion que sirve para cambiar estado a uno o varios registros
     * de tipo de elemento, via ajax
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-02-2015
     * @Secure(roles="ROLE_127-9")
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $peticion = $this->get('request');

        $parametro = $peticion->get('param');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        $array_valor = explode("|", $parametro);
        foreach($array_valor as $id):
            if(null == $entity = $em->find('schemaBundle:AdmiTipoElemento', $id))
            {
                $respuesta->setContent("No existe la entidad");
            }
            else
            {
                if(strtolower($entity->getEstado()) != "eliminado")
                {
                    $entity->setEstado("Eliminado");
                    $entity->setFeUltMod(new \DateTime('now'));
                    $entity->setUsrUltMod($peticion->getSession()->get('user'));
                    $em->persist($entity);
                    $em->flush();
                }

                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;

        return $respuesta;
    }

    /**
     * Funcion que sirve para obtener los datos de un objeto 
     * AdmiTipoElemento.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-02-2015
     */
    public function getDatosTipoElementoAction($id)
    {
        $respuesta = new Response();
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        $respuesta->headers->set('Content-Type', 'text/json');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoElemento')
            ->generarJsonCargarDatosTipoElemento($id, $emGeneral);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_127-46")
    */
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoElemento')
            ->generarJsonTiposElementosAdministracion($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * getTiposElementosAction
     *
     * Funcion que retorna los tipos de elementos
     *
     * @return $respuesta JSON
     *
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 28-12-2015 Se realizan ajustes por modificacion de funcion de Repository
     *
     */
    public function getTiposElementosAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $start    = $peticion->query->get('start');
        $limit    = $peticion->query->get('limit');
        
        $parametros["nombre"] = "";
        $parametros["estado"] = "Activo";
        $parametros["start"]  = $start;
        $parametros["limit"]  = $limit;
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoElemento')
            ->generarJsonTiposElementos($parametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getTiposElementosBackboneAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoElemento')
            ->generarJsonTiposElementosBackbone("BACKBONE","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'getTiposElementosNodosAction'.
     *
     * Obtiene registros de tipos de elementos de un nodo
     * @return Response $respuesta  listado de registros de tipo de elementos en objeto json
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 11-07-2019
     *
     */
    public function getTiposElementosNodosAction()
    {
        $objRespuesta      = new JsonResponse();
                
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoMedidor')
            ->generarJsonMedidoresElectricos("ELEMENTOS NODOS","Activo","");
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'getNivelesElementosAction'.
     *
     * Obtiene niveles de elementos padres que pertenencen a un nodo
     * @return Response $respuesta  listado de registros de elementos padres en objeto json
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 11-07-2019
     *
     */
    public function getNivelesElementosAction()
    {
        $objRespuesta      = new JsonResponse();
                
        $objPeticion       = $this->get('request');
        $strParametro      = $objPeticion->get('param');
        $intIdNodo         = $objPeticion->get('id');
        $intContador       = 0;
        
        $objEm = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objDetalle = $objEm->getRepository('schemaBundle:AdmiTipoMedidor')
                         ->getMedidoresElectricos("ELEMENTOS NODOS","Activo",$strParametro);
                
        if($objDetalle)
        {
            foreach($objDetalle as $objData)
            {
                $strPadre = $objData->getValor2();
                $objJson = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:InfoElemento')
                                ->getJsonElementoContenedor($intIdNodo,$strPadre);
                
                $objContador = json_decode($objJson);
                $intContador = $objContador->{'total'};
            }
        }
        $arrayResultadoRegistro = array('nivel'    =>  $strPadre, 'contador'  => $intContador );
        $objRespuesta->setData($arrayResultadoRegistro);
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'getPadresElementosNodosAction'.
     *
     * Obtiene registro del elemento contenedor
     * @return Response $respuesta  listado de registros del elemento contenedor en objeto json
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 12-07-2019
     *
     */
    public function getPadresElementosNodosAction()
    {
        $objJsonResponse    = new JsonResponse();
                
        $objPeticion    = $this->get('request');
        $intIdNodo      = $objPeticion->get('id');
        $strValor2      = $objPeticion->get('valor2');
                
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->getJsonElementoContenedor($intIdNodo,$strValor2);
        
        $objJsonResponse->setContent($objJson);
        
        return $objJsonResponse;
    }
    
    /**
     * getTiposElementosRutaAction
     *
     * Funcion que retorna los tipos de elementos Ruta
     *
     * @return $objJsonResponse JSON
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     *
     */
    public function getTiposElementosRutaAction()
    {
        $objJsonResponse  = new Response();
        $objJsonResponse->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();
        $arrayParametros["descripcion"] = "RUTA";
        $arrayParametros["estado"]      = "Activo";
                
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoElemento')
            ->generarJsonTiposElementosRuta($arrayParametros);
        $objJsonResponse->setContent($objJson);
        
        return $objJsonResponse;
    }


    /**
     * getElementosConClaseAction
     *
     * Funcion que retorna los elementos que tieenen asociada una clase.
     *
     * @return $objJsonResponse JSON
     *
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.0
     *
     */
    public function getElementosConClaseAction()
    {
        $arrayRespuesta                  = array();
        $arrayParametros                 = array();
        $arrayParametros["strParametro"] = "ELEMENTOS CON CLASE";
        $arrayParametros["strEstado"]    = "Activo";
                
        $arrayRespuesta = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoMedidor')
            ->getElementosConClase($arrayParametros);

        if(!$arrayRespuesta)
        {
            $arrayRespuesta["respuesta"] = "No se encontraron elementos";
            $arrayRespuesta["status"] = "Error";
        }

        $objJsonResponse = new Response(json_encode($arrayRespuesta));
        $objJsonResponse->headers->set('Content-type', 'text/json');		
        return $objJsonResponse;
    }


    /**
     * getContenedoresNodoAction
     *
     * Función que retorna los contenedores asociados a un elemeneto y pertenecientes al mismo nodo.
     *
     * @return $objJsonResponse JSON
     *
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.0
     *
     */
    public function getContenedoresNodoAction()
    {
        $objPeticion                            = $this->get('request');
        $arrayParametros                        = array();
        $arrayParametros["strParametro"]        = "ELEMENTOS CON CONTENEDORES";
        $arrayParametros["intIdNodo"]           = $objPeticion->get('idNodo');
        $arrayParametros["strTipoElemento"]     = $objPeticion->get('tipoElemento');
        $arrayRespuesta                         = array();

        //comprobación 

        $arrayValidacion = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:AdmiTipoElemento')
                                ->verificarContenedor($arrayParametros);
        
        if($arrayValidacion)
        {
            $arrayTemp = $this->getDoctrine()
                                    ->getManager("telconet_infraestructura")
                                    ->getRepository('schemaBundle:AdmiTipoElemento')
                                    ->getContenedoresNodo($arrayParametros);

            if($arrayTemp)
            {
                $arrayRespuesta["encontrados"] = $arrayTemp;
                $arrayRespuesta["total"] = count($arrayTemp);
                $arrayRespuesta["status"] = "Ok";
            }
            else
            {
                $arrayRespuesta["encontrados"] = "No se encontraron elementos";
                $arrayRespuesta["total"] = 0;
                $arrayRespuesta["status"] = "Error";
            }                                    
        }
        else
        {
            $arrayRespuesta["encontrados"] = "No tiene contenedor";
            $arrayRespuesta["total"] = -1;
            $arrayRespuesta["status"] = "NC";
        }
       
        $objJsonResponse = new Response(json_encode($arrayRespuesta));
        $objJsonResponse->headers->set('Content-type', 'text/json');		
        return $objJsonResponse;        
    }

}