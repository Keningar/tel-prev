<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Form\InfoElementoNodoWifiType;
use Telconet\tecnicoBundle\Resources\util\Util;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

/**
 * InfoElementoNodoWifiController
 *
 * logica de negocio de los elementos nodo wifi
 *
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 09-03-2016
 */

class InfoElementoNodoWifiController extends Controller
{ 
    
    /**
    * indexNodoWifiAction
    * funcion que valida los permisos y renderiza el index de la administración
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 04-05-2016
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 04-10-2016   se aumento la opción de ingreso de lemento wifi
    */ 
    
    public function indexAction(){
        
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_341-3919'))
        {
                $rolesPermitidos[] = 'ROLE_341-3919'; //NewNodoWifi
        }
        if (true === $this->get('security.context')->isGranted('ROLE_341-3899'))
        {
                $rolesPermitidos[] = 'ROLE_341-3899'; //EditNodoWifi
        }
        if (true === $this->get('security.context')->isGranted('ROLE_341-3920'))
        {
                $rolesPermitidos[] = 'ROLE_341-3920'; //eliminar elemento caja
        }
        if (true === $this->get('security.context')->isGranted('ROLE_341-4617'))
        {
                $rolesPermitidos[] = 'ROLE_341-4617'; //cambio de nodo wifi
        }
        if (true === $this->get('security.context')->isGranted('ROLE_341-4817'))
        {
                $rolesPermitidos[] = 'ROLE_341-4817'; //ingresarElementoWifi
        }           
        
        return $this->render('tecnicoBundle:InfoElementoNodoWifi:index.html.twig', array(
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * newAction
    * renderiza el formulario para un ingreso nuevo
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function newAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoNodoWifiType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoNodoWifi:new.html.twig', array(
                             'entity' => $entity,
                             'form'   => $form->createView()
        ));
    }

    /**
     * createAction
     * función que crea el elemento en la base de datos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function createAction()
    {
        $request            = $this->get('request');
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $elemento           = new InfoElemento();
        $parametros         = $request->request->get('telconet_schemabundle_infoelementonodowifitype');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $strCodEmpresa      = $session->get('idEmpresa');
        $objForm            = $this->createForm(new InfoElementoNodoWifiType(array("empresaId" => $strCodEmpresa)), $elemento);
        $nombreElemento     = strtoupper($parametros['nombreElemento']);
        $modeloElementoId   = $parametros['modeloElementoId'];
        $parroquiaId        = $parametros['parroquiaId'];
        $alturaSnm          = $parametros['alturaSnm'];
        $longitudUbicacion  = $parametros['longitudUbicacion'];
        $latitudUbicacion   = $parametros['latitudUbicacion'];
        $idJurisdiccion     = $parametros['jurisdiccionId'];
        $direccionUbicacion = $parametros['direccionUbicacion'];
        $descripcionElemento= $parametros['descripcionElemento'];

        $em->beginTransaction();
        try
        {
            $objForm->handleRequest($request);
            $modeloElemento = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($modeloElementoId);
            
            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                     "estado"           => array("Activo", "Pendiente","Factible","PreFactibilidad"),
                                                     "modeloElementoId" => $modeloElemento->getId()));

            if($elementoRepetido)
            {
                throw new \Exception('Nombre ya existe en otro Elemento con estado '.$elementoRepetido->getEstado());
            }
            $objSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud('SOLICITUD NODO WIFI');

            $elemento->setNombreElemento($nombreElemento);
            $elemento->setDescripcionElemento($descripcionElemento);
            $elemento->setModeloElementoId($modeloElemento);
            $elemento->setUsrResponsable($session->get('user'));
            $elemento->setUsrCreacion($session->get('user'));
            $elemento->setFeCreacion(new \DateTime('now'));
            $elemento->setIpCreacion($peticion->getClientIp());
            $elemento->setEstado("Pendiente");

            $em->persist($elemento);
            $em->flush();  

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento("Pendiente");
            $historialElemento->setObservacion("Se ingreso un nodo wifi");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);
            $em->flush();
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del nodo wifi "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($latitudUbicacion);
            $ubicacionElemento->setLongitudUbicacion($longitudUbicacion);
            $ubicacionElemento->setDireccionUbicacion($direccionUbicacion);
            $ubicacionElemento->setAlturaSnm($alturaSnm);
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ubicacionElemento);
            $em->flush();

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elemento);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);
            $em->flush();

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elemento);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($peticion->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);
            $em->flush();

            //crear la solicitud de factiblidad
            $entityDetalleSolicitud = new infoDetalleSolicitud();
            $entityDetalleSolicitud->setTipoSolicitudId($objSolicitud);
            $entityDetalleSolicitud->setObservacion('Creado desde la administración de nodo wifi');
            $entityDetalleSolicitud->setUsrCreacion($session->get('user'));
            $entityDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolicitud->setEstado("FactibilidadEnProceso");
            $entityDetalleSolicitud->setElementoId($elemento->getId());
            $em->persist($entityDetalleSolicitud);
            $em->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityDetalleSolHist = new InfoDetalleSolHist();
            $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
            $entityDetalleSolHist->setEstado('PreFactibilidad');
            $entityDetalleSolHist->setObservacion('Creado desde la administración');
            $em->persist($entityDetalleSolHist);
            $em->flush();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', "Error: " . $e->getMessage());
            return $this->render('tecnicoBundle:InfoElementoNodoWifi:new.html.twig', array(
                                                                                            'entity' => $elemento,
                                                                                            'form'   => $objForm->createView()
                                ));
        }
     
        return $this->redirect($this->generateUrl('elementoNodoWifi_show', array('id' => $elemento->getId())));
        
    }
   
    /**
    * editAction
    * función que renderiza el formulario para una edición
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function editAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) 
        {
            throw new NotFouncargarDatosNodoWifidHttpException('No existe el elemento que se quiere modificar');
        }
        else
        {            
            $elementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findBy(array( "elementoId" =>$elemento->getId()));
            $ubicacion = $em->getRepository('schemaBundle:InfoUbicacion')->findBy(array( "id" =>$elementoUbica[0]->getUbicacionId()));
        }

        $formulario =$this->createForm(new InfoElementoNodoWifiType(array("empresaId"=>$empresaId)), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoNodoWifi:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'caja'                  => $elemento,
                                'ubicacion'             => $ubicacion[0])
                            );
    }
    
   /**
    * updateAction
    * funcion que actualiza el elemento en la BD
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    * 
    */     
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }

        $request            = $this->get('request');
        $session            = $request->getSession();
        $parametros         = $request->request->get('telconet_schemabundle_infoelementonodowifitype');        
        $nombreElemento     = strtoupper($parametros['nombreElemento']);
        $descripcionElemento= $parametros['descripcionElemento'];
        $modeloElementoId   = $parametros['modeloElementoId'];
        $jurisdiccionId     = $parametros['jurisdiccionId'];
        $cantonId           = $parametros['cantonId'];
        $parroquiaId        = $parametros['parroquiaId'];
        $direccionUbicacion = $request->request->get('direccionUbicacion');
        $longitudUbicacion  = $request->request->get('longitudUbicacion');
        $latitudUbicacion   = $request->request->get('latitudUbicacion');
        $alturaSnm          = $request->request->get('alturaSnm');
        $idUbicacion        = $request->request->get('idUbicacion');
        
        $em->beginTransaction();
        try
        {
            $parroquia  = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);

            $objElementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneByElementoId($id);
            $idParroquia    = '';
            $longitud       = '';
            $latitud        = '';
            $direccion      = '';
            $altura         = '';
            if($objElementoUbica)
            {
                $objUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->findOneById($objElementoUbica->getUbicacionId());
                $idParroquia    = $objUbicacion->getParroquiaId()->getId();
                $longitud       = $objUbicacion->getLongitudUbicacion();
                $latitud        = $objUbicacion->getLatitudUbicacion();
                $direccion      = $objUbicacion->getDireccionUbicacion();
                $altura         = $objUbicacion->getAlturaSnm();
            }
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del nodo wifi "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            
            if ($longitud != $longitudUbicacion || $latitud != $latitudUbicacion || $parroquiaId != $idParroquia 
                || strtoupper($direccion)!= strtoupper($direccionUbicacion)|| $alturaSnm != $altura)
            {
                $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findByElementoIdA($id);

                foreach($objRelacionElemento as $elemento)
                {
                    $objElementoUbicaHijo = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                               ->findOneByElementoId($elemento->getElementoIdB());

                    if($objElementoUbicaHijo)
                    {
                        //info ubicacion
                        $ubicacionElementoHijo = $em->find('schemaBundle:InfoUbicacion', $objElementoUbicaHijo->getUbicacionId()->getId());
                        $ubicacionElementoHijo->setLatitudUbicacion($latitudUbicacion);
                        $ubicacionElementoHijo->setLongitudUbicacion($longitudUbicacion);
                        $ubicacionElementoHijo->setDireccionUbicacion($direccionUbicacion);
                        $ubicacionElementoHijo->setAlturaSnm($alturaSnm);
                        $ubicacionElementoHijo->setParroquiaId($parroquia);
                        $em->persist($ubicacionElementoHijo);
                    }
                }
            }        

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            //elemento
            
            if($nombreElemento != $entity->getNombreElemento())
            {
                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                         "estado"           => array("Activo", "Pendiente","Factible","PreFactibilidad"),
                                                         "modeloElementoId" => $modeloElementoId));
                
                if($elementoRepetido)
                {
                    $this->get('session')->getFlashBag()->add('notice', 'Nombre ya existe en otro Elemento con estado '.$elementoRepetido->getEstado());
                    return $this->redirect($this->generateUrl('elementoNodoWifi_edit', array('id' => $entity->getId())));
                }
                else
                {
                    $entity->setNombreElemento($nombreElemento);
                }
            }

            $entity->setDescripcionElemento($descripcionElemento);
            $entity->setModeloElementoId($modeloElemento);
            $entity->setUsrResponsable($session->get('user'));
            $entity->setUsrCreacion($session->get('user'));
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setIpCreacion($request->getClientIp());   
            $em->persist($entity);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Modificado");
            $historialElemento->setObservacion("Se modifico el nodo wifi");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);

            //info ubicacion        
            $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
            $ubicacionElemento->setLatitudUbicacion($latitudUbicacion);
            $ubicacionElemento->setLongitudUbicacion($longitudUbicacion);
            $ubicacionElemento->setDireccionUbicacion($direccionUbicacion);
            $ubicacionElemento->setAlturaSnm($alturaSnm);
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($ubicacionElemento);

            $em->flush();
            $em->commit();

            return $this->redirect($this->generateUrl('elementoNodoWifi_show', array('id' => $entity->getId())));

        }        
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementoNodoWifi_edit', array('id' => $entity->getId())));
        }
        
    }
    
   /**
    * showAction
    * funcion que renderiza el form en donde aparece la información del elemento
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */         
    public function showAction($id){
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $empresaId=$session->get('idEmpresa');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoNodoWifi:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    /**
    * getEncontrados
    * obtiene todos los registro de este tipo ingresados en la base de datos para mostrarlos en el grid
    *
    * @return json con la data de los nodo clientes
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
    */    
    public function getEncontradosAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta      = new Response();
        $session        = $this->get('session');
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $peticion       = $this->get('request');        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $canton         = $peticion->query->get('canton');
        $estado         = $peticion->query->get('estado');
        $idEmpresa      = $session->get('idEmpresa');
        $modeloElemento = $peticion->get('modeloElemento');        
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        
        $arrayParametros = array();
        $arrayParametros['start']           = $start;
        $arrayParametros['limit']           = $limit;
        $arrayParametros["idModeloElemento"]= $modeloElemento;
        $arrayParametros["codEmpresa"]      = $idEmpresa;
        $arrayParametros["nombreNodo"]      = $nombreElemento;
        $arrayParametros["idCanton"]        = $canton;
        $arrayParametros["estadoElemento"]  = $estado;
        
        $respuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoElemento')
                                                    ->getJsonRegistrosNodoWifi($arrayParametros);

        $respuesta->setContent($respuestaSolicitudes);

        return $respuesta;
    }
    
   /**
    * cargarDatosAction
    * funcion que retorna un json con los datos del elemento
    *
    * @return json con datos del elemento
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function cargarDatosAction(){
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC        = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $empresaId  = $session->get('idEmpresa');
        $idElemento = $peticion->get('idElemento');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonCargarDatosCaja($idElemento, $empresaId, $em, $emC);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
   /**
    * deleteAjaxAction
    * función que elimina uno o varios elementos
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em->getConnection()->beginTransaction();
        
        $array_valor = explode("|",$parametro);
        
        $objTipoSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD NODO WIFI',
                                                                                'estado' => 'Activo'));
        
        try{
        
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) 
            {
                $respuesta->setContent("No existe la entidad");
            }
            else
            {
                $flag=0;
                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdA" =>$entity->getId(), 
                                                                                                           "estado" => "Activo"));
                for($i=0;$i<count($relacionElemento);$i++)
                {
                    $elementoContenidoId = $relacionElemento[$i]->getElementoIdB();
                    $elementoContenido = $em->find('schemaBundle:InfoElemento', $elementoContenidoId);
                    if($elementoContenido->getEstado()=="Activo"){
                        $flag=1;
                        break;
                    }
                }                
                        
                $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneBy(array('elementoId'=>$id, 
                                                                                                 'tipoSolicitudId' => $objTipoSolicitud->getId()));
                
                if($flag==0)
                {
                    //elemento
                    $entity->setEstado("Eliminado");
                    $entity->setUsrCreacion($session->get('user'));
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setIpCreacion($peticion->getClientIp());  
                    $em->persist($entity);

                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($entity);
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setObservacion("Se elimino el Nodo Cliente en la administración");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    //relacion elemento
                    for($i=0;$i<count($relacionElemento);$i++){
                        $relacion = $relacionElemento[$i];
                        $relacion->setEstado("Eliminado");
                        $relacion->setUsrCreacion($session->get('user'));
                        $relacion->setFeCreacion(new \DateTime('now'));
                        $relacion->setIpCreacion($peticion->getClientIp());
                        $em->persist($relacion);
                    }

                    //empresa elemento
                    $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array( "elementoId" =>$entity));
                    $empresaElemento->setEstado("Eliminado");
                    $empresaElemento->setObservacion("Se elimino la caja");
                    $empresaElemento->setUsrCreacion($session->get('user'));
                    $empresaElemento->setFeCreacion(new \DateTime('now'));
                    $empresaElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($empresaElemento);
                    //finalizo la solicitud
                    if ($objSolicitud->getEstado() != 'Finalizada' )
                    {  
                        $objSolicitud->setUsrRechazo($session->get('user'));
                        $objSolicitud->setFeRechazo(new \DateTime('now'));
                        $objSolicitud->setEstado("Eliminado");
                        $em->persist($objSolicitud);
                        
                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($session->get('user'));
                        $entityDetalleSolHist->setEstado('Eliminado');
                        $entityDetalleSolHist->setObservacion('Eliminada desde la administración');
                        $em->persist($entityDetalleSolHist);

                    }                    
                    $em->flush();
                    $respuesta->setContent("OK");
                    $mensaje = 'Se eliminó correctamente '.$entity->getNombreElemento().$mensaje;
                }
                else
                {
                    $respuesta->setContent("Aun Existen Elementos Activos dentro de este Elemento. ".$mensaje);
                    break;
                }
            }
        endforeach;

        $em->getConnection()->commit();
        
        }        
        catch(\Exception $e)
        {

            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);

        }
        
        return $respuesta;
    }
    
   /**
    * getElementosPorElementoAction
    * funcion que obtiene los elementos relacionados en la tabla infoRelacionElemento
    *
    * @return json
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function getElementosPorElementoAction($id){
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request            = $this->get('request');
        $session            = $request->getSession();
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_infraestructura');
        $empresa            = $session->get('idEmpresa');        
        $peticion           = $this->get('request');        
        $cajaId             = $id;
        $start              = $peticion->query->get('start');
        $limit              = $peticion->query->get('limit');
        
        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                     ->generarJsonElementosPorCaja($cajaId,$empresa,"Activo",$start, $limit, $emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    /**
     * getModelosPorTipoElementoAction
     * funcion que obtiene los  modelos por el tipo de elemento
     *
     * @return json
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     */
    public function getModelosPorTipoElementoAction()
    {
        $respuesta              = new Response();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion               = $this->get('request');
        $tipoElemento           = $peticion->get('tipoElemento');

        $arrayTmpParametros     = array( 'estadoActivo' => 'Activo', 'tipoElemento' => array($tipoElemento) );
        
        $result    = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                       ->getJsonModeloElementosByCriterios( $arrayTmpParametros );

        $respuesta->setContent($result);

        return $respuesta;
    }    

     /**
     * activarClienteAction
     * funcion que activa el servicio al cliente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     *
     * @author Jesús Bozada <jbozadfa@telconet.ec>
     * @version 1.1 01-02-2018    Se agrega confirmación automatica de servicios segun lo solicitado
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 22-01-2018   Se agrega programación para poder activar servicios WIFI reutilizando equipos en servicios originados por traslados
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 11-06-2020 - Se agrega bandera para las acciones del control bw de la interface
     *
     * @since 1.0
     */
    public function activarClienteAction()
    {
        //OBTENCION DE PARAMETROS-----------------------------------------------*/
        ini_set('max_execution_time', 800000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $result     = "";
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $session->set('nombreAccionBw', 'activar');
        $emComercial= $this->getDoctrine()->getManager("telconet");

        $arrayPeticiones = array(
            'idEmpresa'             => $session->get('idEmpresa'),
            'prefijoEmpresa'        => $session->get('prefijoEmpresa'),
            'idServicio'            => $peticion->get('idServicio'),
            'idProducto'            => $peticion->get('idProducto'),
            'perfil'                => $peticion->get('perfil'),
            'login'                 => $peticion->get('login'),
            'capacidad1'            => $peticion->get('capacidad1'),
            'interfaceElementoId'   => $peticion->get('interfaceElementoId'),
            'interfaceElementoSplitterId' => $peticion->get('interfaceElementoSplitterId'),
            'ultimaMilla'           => $peticion->get('ultimaMilla'),
            'macWifi'               => $peticion->get('macWifi'),
            'serieWifi'             => $peticion->get('serieWifi'),
            'modeloWifi'            => $peticion->get('modeloWifi'),
            'ssid'                  => $peticion->get('ssid'),
            'password'              => $peticion->get('password'),
            'numeroPc'              => $peticion->get('numPc'),
            'modoOperacion'         => $peticion->get('modoOperacion'),
            'ipElementoCliente'     => $peticion->get('ipElementoCliente'),
            'vlan'                  => $peticion->get('vlan'),
            'observacion'           => $peticion->get('observacionCliente'),
            'strEsWifiExistente'    => $peticion->get('strEsWifiExistente'),
            //valores de sesion
            'usrCreacion'           => $session->get('user'),
            'idPersonaEmpresaRol'   => $session->get('idPersonaEmpresaRol'),
            'ipCreacion'            => $peticion->getClientIp(),
            'idOficina'             => $session->get('idOficina'),
            //valores para activar servicio TN
            'serieNuevoCpe'         => $peticion->get('serieNuevoCpe'),
            'modeloNuevoCpe'        => $peticion->get('modeloNuevoCpe'),
            'macNuevoCpe'           => $peticion->get('macNuevoCpe'),
            'nombreNuevoCpe'        => $peticion->get('nombreNuevoCpe'),
            //valores para conectarse al naf
            'serNaf' => $this->container->getParameter('database_host_naf'),
            'ptoNaf' => $this->container->getParameter('database_port_naf'),
            'sidNaf' => $this->container->getParameter('database_name_naf'),
            'usrNaf' => $this->container->getParameter('user_naf'),
            'pswNaf' => $this->container->getParameter('passwd_naf')
        );

        /* @var $activacion InfoElementoWifiService */
        $activacion = $this->get('tecnico.InfoElementoWifi');
        //---------------------------------------------------------------------*/
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
        $respuestaArray = $activacion->activarCliente($arrayPeticiones);
        $status = $respuestaArray[0]['status'];
        $mensaje = $respuestaArray[0]['mensaje'];
        //----------------------------------------------------------------------*/
        //--------RESPUESTA-----------------------------------------------------*/
        if($status == "OK")
        {
            $result = "OK";
            //finalizar solicitud planificacion
            $objTipoSolicitudPlanficacion = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                          "estado"               => "Activo"));
            $objSolicitudPlanficacion = $emComercial
                ->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneBy(array("servicioId"      => $peticion->get('idServicio'),
                                                                                      "tipoSolicitudId" => $objTipoSolicitudPlanficacion->getId(),
                                                                                      "estado"          => "Asignada"));

            if($objSolicitudPlanficacion)
            {
                $objSolicitudPlanficacion->setEstado("Finalizada");
                $emComercial->persist($objSolicitudPlanficacion);
                $emComercial->flush();

                //crear historial para la solicitud
                $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudPlanficacion);
                $objHistorialSolicitudPlani->setEstado("Finalizada");
                $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                $objHistorialSolicitudPlani->setUsrCreacion($session->get('user'));
                $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitudPlani->setIpCreacion($peticion->getClientIp());
                $emComercial->persist($objHistorialSolicitudPlani);
                $emComercial->flush();
            }
            
            $objServicio   = $emComercial->getRepository('schemaBundle:InfoServicio')
                                         ->find($peticion->get('idServicio'));
            if (is_object($objServicio))
            {
                $strTipoOrden      = $objServicio->getTipoOrden();
                $strPrefijoEmpresa = $session->get('prefijoEmpresa');
                //SE CONFIRMA AUTOMATICAMENTE LOS SERVICIOS CON TIPO DE ORDEN N
                if ($strPrefijoEmpresa == 'TN' && 
                    (strpos($objServicio->getProductoId()->getGrupo(),'DATACENTER') === false) &&
                    $strTipoOrden == "N"
                   )
                {
                    $arrayPeticiones = array(
                                            'idEmpresa'                     => $session->get('idEmpresa'),
                                            'prefijoEmpresa'                => $session->get('prefijoEmpresa'),
                                            'idServicio'                    => $objServicio->getId(),
                                            'idProducto'                    => $objServicio->getProductoId()->getId(),
                                            'observacionActivarServicio'    => "Se confirmo el servicio",
                                            'idAccion'                      => "847",//accion quemada en javascript
                                            'usrCreacion'                   => $session->get('user'),
                                            'ipCreacion'                    => $peticion->getClientIp()
                                            );

                   /* @var $confirmar InfoConfirmarServicio */
                   $serviceConfirmarServicio = $this->get('tecnico.InfoConfirmarServicio');
                   //*----------------------------------------------------------------------*/

                   $arrayRespuesta = $serviceConfirmarServicio->confirmarServicio($arrayPeticiones);
                   $status         = $arrayRespuesta[0]['status'];
                   $mensaje        = $arrayRespuesta[0]['mensaje'];
                }
            }
        }
        
        
        else if($status == "ERROR")
        {
            if($mensaje == "java.net.ConnectException: Connection timed out")
            {
                $result = "SIN CONEXION";
            }
            if($mensaje == "NO ID CLIENTE")
            {
                $result = "NO ID CLIENTE";
            }
            else if($mensaje == "NO EXISTE TAREA")
            {
                $result = $mensaje;
            }
            else
            {
                $result = $mensaje;
            }
        }
        else if($status == "NAF")
        {
            $result = $mensaje;
        }
        else if($status == "NA")
        {
            $result = "ERROR DESCONOCIDO";
        }
        else if($status == "ERROR SCE")
        {
            $result = "ERROR SCE";
        }
        else
        {
            $result = $mensaje;
        }

        return $respuesta->setContent($result);
    }
          
     /**
     * cortarClienteAction
     * funcion que corta el servicio al ciente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-06-2020 - Se agrega bandera para las acciones del control bw de la interface
     *
     */ 

    public function cortarClienteAction()
    {
        ini_set('max_execution_time', 650000);
        //*DECLARACION DE VARIABLES----------------------------------------------*/
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $session->set('nombreAccionBw', 'cortar');
                
        $arrayPeticiones=array(
                                'idEmpresa'             => $session->get('idEmpresa'),
                                'prefijoEmpresa'        => $session->get('prefijoEmpresa'),
                                'idServicio'            => $peticion->get('idServicio'),
                                'idProducto'            => $peticion->get('idProducto'),
                                'motivo'                => $peticion->get('motivo'),
                                'capacidad1'            => $peticion->get('capacidad1'),
                                'capacidad2'            => $peticion->get('capacidad2'),
                                'usrCreacion'           => $session->get('user'),
                                'ipCreacion'            => $peticion->getClientIp(),
                                'idAccion'              => $peticion->get('idAccion')
                                );
        
        /* @var $cortar InfoElementoWifi */
        $cortar = $this->get('tecnico.InfoElementoWifi');
        //*----------------------------------------------------------------------*/
        
        $respuestaArray = $cortar->cortarServicio($arrayPeticiones);
        
        
        return $respuesta->setContent($respuestaArray[0]['mensaje']);
    }
    
     /**
     * reconectarClienteAction
     * funcion que reconecta el servicio al ciente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-06-2020 - Se agrega bandera para las acciones del control bw de la interface
     *
     */     
    public function reconectarClienteAction()
    {
        ini_set('max_execution_time', 650000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $session->set('nombreAccionBw', 'reconectar');
        
        $arrayPeticiones=array(
                                'idEmpresa'             => $session->get('idEmpresa'),
                                'prefijoEmpresa'        => $session->get('prefijoEmpresa'),
                                'idServicio'            => $peticion->get('idServicio'),
                                'idProducto'            => $peticion->get('idProducto'),
                                'usrCreacion'           => $session->get('user'),
                                'ipCreacion'            => $peticion->getClientIp(),
                                'idAccion'              => $peticion->get('idAccion'),
                                );
        
        /* @var $reconectar InfoElementoWifiServicio */
        $reconectar = $this->get('tecnico.InfoElementoWifi');
        //*----------------------------------------------------------------------*/
        
        $respuestaArray = $reconectar->reconectarServicio($arrayPeticiones);        
        
        return $respuesta->setContent($respuestaArray[0]['mensaje']);
    }
    
     /**
     * cancelarClienteAction
     * funcion que cancela el servicio al ciente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 01-09-2017 -  Se obtiene el departamento de session
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 10-12-2019 - Se agrega lógica para poder cancelar servicios WIFI Alquiler Equipos.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 11-06-2020 - Se agrega bandera para las acciones del control bw de la interface
     *
     */   
     public function cancelarClienteAction()
     {
        //*DECLARACION DE VARIABLES----------------------------------------------*/
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $session->set('nombreAccionBw', 'cancelar');
        
        $arrayPeticiones=array( 'intIdDepartamento'     => $session->get('idDepartamento'),
                                'idEmpresa'             => $session->get('idEmpresa'),
                                'prefijoEmpresa'        => $session->get('prefijoEmpresa'),
                                'idServicio'            => $peticion->get('idServicio'),
                                'idProducto'            => $peticion->get('idProducto'),
                                'motivo'                => $peticion->get('motivo'),
                                'login'                 => $peticion->get('login'),
                                'idAccion'              => $peticion->get('idAccion'),
                                'usrCreacion'           => $session->get('user'),
                                'ipCreacion'            => $peticion->getClientIp(),
                                'idPersonaEmpresaRol'   => $session->get('idPersonaEmpresaRol')
                                );
        
        /* @var $cancelar InfoElementoWifi */
        $cancelar = $this->get('tecnico.InfoElementoWifi');
        //*----------------------------------------------------------------------*/

         if ($peticion->get('idProducto'))
         {
             $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                        ->find($peticion->get('idProducto'));
             if (is_object($objProducto))
             {
                 if ($objProducto->getDescripcionProducto() == 'WIFI Alquiler Equipos')
                 {
                     $arrayRespuesta = $cancelar->cancelarWifiAlquilerEquipos($arrayPeticiones);
                 }
                 else
                 {
                     $arrayRespuesta = $cancelar->cancelarServicio($arrayPeticiones);
                 }

             }
         }


        
        return $respuesta->setContent($arrayRespuesta[0]['mensaje']);
    }
    
     /**
     * cambiarElementoClienteAction
     * funcion que permite realizar el cambio de elemento del cliente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 01-09-2017 Se obtiene el departamento de la session
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 22-05-2019 - Se agrega el concepto de ingreso de responsable de retiro de equipos
     */
     public function cambiarElementoClienteAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $arrayPeticiones = array('intIdDepartamento'    => $session->get('intIdDepartamento'),
                                'idEmpresa'             => $session->get('idEmpresa'),
                                'prefijoEmpresa'        => $session->get('prefijoEmpresa'),
                                'idServicio'            => $peticion->get('idServicio'),
                                'idElemento'            => $peticion->get('idElemento'),
                                'modeloCpe'             => $peticion->get('modeloCpe'),
                                'ipCpe'                 => $peticion->get('ipCpe'),
                                'idResponsable'         => $peticion->get('idResponsable'),
                                'tipoResponsable'       => $peticion->get('tipoResponsable'),
                                'nombreCpe'             => $peticion->get('nombreCpe'),
                                'macCpe'                => $peticion->get('macCpe'),
                                'serieCpe'              => $peticion->get('serieCpe'),
                                'descripcionCpe'        => $peticion->get('descripcionCpe'),
                                'tipoElementoCpe'       => $peticion->get('tipoElementoCpe'),
                                'usrCreacion'           => $session->get('user'),
                                'ipCreacion'            => $peticion->getClientIp(),
                                'serNaf'                => $this->container->getParameter('database_host_naf'),
                                'ptoNaf'                => $this->container->getParameter('database_port_naf'),
                                'sidNaf'                => $this->container->getParameter('database_name_naf'),
                                'usrNaf'                => $this->container->getParameter('user_naf'),
                                'pswNaf'                => $this->container->getParameter('passwd_naf'),
                                'host'                  => $this->container->getParameter('host')
                            );
        
        /* @var $serviceCambioElemento InfoElementoWifiService */
        $serviceCambioElemento = $this->get('tecnico.InfoElementoWifi');
        
        $respuestaArray = $serviceCambioElemento->cambioElemento($arrayPeticiones);
        
        if($respuestaArray[0]['status']!="OK"){
            $result = $respuestaArray[0]['mensaje'];
        }
        else{
            $result = "OK";
        }
        
        return $respuesta->setContent($result);
    }
    
     /**
     * ingresarElemento
     * funcion que ingresa el elemento cliente para regularizar la data del mismo
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 26-09-2016
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 01-05-2020 - Se agregan 2 indices nuevos al arreglo de peticiiones 'boolRequiereRegistro', 'boolTieneFlujo'
     *                            y 'arrayCaractAdicionales' para el funcionamiento de servicios INSTALACION_SIMULTANEA.
     * 
     * @Secure(roles="ROLE_341-4817")
     */
    public function ingresarElementoAction()
    {
        ini_set('max_execution_time', 800000);
        $objRespuesta  = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $arrayResult= "";
        $objPeticion   = $this->get('request');
        $objSession    = $objPeticion->getSession();

        $arrayPeticiones = array(
            'intIdEmpresa'             => $objSession->get('idEmpresa'),
            'prefijoEmpresa'           => $objSession->get('prefijoEmpresa'),
            'intIdServicio'            => $objPeticion->get('idServicio'),
            'intIdProducto'            => $objPeticion->get('idProducto'),
            'strLogin'                 => $objPeticion->get('login'),
            'intCapacidad1'            => $objPeticion->get('capacidad1'),
            'strUltimaMilla'           => $objPeticion->get('ultimaMilla'),
            'strMacWifi'               => $objPeticion->get('macWifi'),
            'strSerieWifi'             => $objPeticion->get('serieWifi'),
            'strModeloWifi'            => $objPeticion->get('modeloWifi'),
            'strSsid'                  => $objPeticion->get('ssid'),
            'strPassword'              => $objPeticion->get('password'),
            'intNumeroPc'              => $objPeticion->get('numPc'),
            'strModoOperacion'         => $objPeticion->get('modoOperacion'),
            'intVlan'                  => $objPeticion->get('vlan'),
            'strObservacion'           => $objPeticion->get('observacionCliente'),
            'strUsrCreacion'           => $objSession->get('user'),
            'strIpCreacion'            => $objPeticion->getClientIp(),
            'ipElementoCliente'        => $objPeticion->get('ipElementoCliente'),
            'boolRequiereRegistro'     => $objPeticion->get('boolRequiereRegistro'),
            'boolTieneFlujo'           => $objPeticion->get('boolTieneFlujo'),
            'boolValidaNaf'           => $objPeticion->get('boolValidaNaf'),
            'arrayCaractAdicionales'   => json_decode($objPeticion->get('arrayCaractAdicionales'), true),
            'intIdUsrCreacion'         => $objSession->get('id_empleado')
        );

        /* @var $activacion InfoElementoWifiService */
        $objActivacion = $this->get('tecnico.InfoElementoWifi');
        //---------------------------------------------------------------------*/
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
        $arrayRespuestaArray = $objActivacion->ingresoElemento($arrayPeticiones);

        $arrayResult = $arrayRespuestaArray[0]['mensaje'];
        //----------------------------------------------------------------------*/
            
        return $objRespuesta->setContent($arrayResult);

    }
    
     /**
     * cambioNodoWifiAction
     * funcion que permite realizar el cambio de nodo wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 15-09-2016 se obtiene el producto del servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 20-09-2016 validaciones para data inconsistente
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 06-04-2017 Se aumenta la modificacion de los bw en los concentradores.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 11-06-2020 - Se agrega bandera para las acciones del control bw de la interface
     *
     * @Secure(roles="ROLE_341-4617")
     */
    
    public function cambioNodoWifiAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $session        = $this->get('request')->getSession();
        $peticion       = $this->get('request');
        $objBufferHilo  = '';
        $modeloNodoWifi = $peticion->get('modeloNodoWifi');
        $idServicio     = $peticion->get('idServicio');
        $serviceUtil    = $this->get('schema.Util');
        $session->set('nombreAccionBw', 'cambioNodoWifiLogico');
        //si es nodo wifi de backbone
        if($modeloNodoWifi == 'BACKBONE')
        {
            $idElementoConector             = $peticion->get('idCasette');
            $idInterfaceElementoConector    = $peticion->get('idInterfaceCasette');
            $idElementoContenedor           = $peticion->get('intIdElementoCaja');
            $idElemento                     = $peticion->get('idElementoWifi');
            $idInterfaceElemento            = $peticion->get('idInterfaceElementoWifi');
            $idInterfaceOdf                 = $peticion->get('intInterfaceOdf');
            $um                             = 'Fibra Optica';
        }
        //si es de cliente
        else
        {
            $idElementoConector          = $peticion->get('idElementoWifi');
            $idInterfaceElementoConector = $peticion->get('idInterfaceElementoWifi');
            $idElementoContenedor        = $peticion->get('idNodoWifi');
            $um                          = 'UTP';
        }
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $serviceServicioGeneral = $this->get('tecnico.InfoServicioTecnico');
        $emInfraestructura->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                      ->findOneById($idInterfaceElementoConector);

            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
            
            $objProducto = $objServicio->getProductoId();
            
            /* @var $servicioGeneral InfoServicioTecnico */
            $servicioGeneral = $this->get('tecnico.InfoServicioTecnico');            
            //consulto la capacidad del servicio nuevo
            $objSpcCapacidad = $servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProducto);
            $intCapacidad = 0;
            if(is_object($objSpcCapacidad))
            {
                $intCapacidad = $objSpcCapacidad->getValor();
            }
            else
            {
                throw new \Exception('El servicio no tiene capacidad.');
            }     
            
            /* @var $serviceWifi InfoElementoWifi */
            $serviceWifi = $this->get('tecnico.InfoElementoWifi');
            
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $objServicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
            $arrayCambioBw['strOperacion']      = 'RESTA';
            $arrayCambioBw['usrCreacion']       = $session->get('user');
            $arrayCambioBw['ipCreacion']        = $peticion->getClientIp();
            
            $arrayCambio = $serviceWifi->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }            

            $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());
            
            $objAdmiTipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                  ->findOneByNombreTipoMedio($um);
            if(!$objAdmiTipoMedio)
            {
                $respuesta->setContent('No existen datos para la ultima milla '.$um);
                return $respuesta;
            }
            
            $objNodoWifi = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($peticion->get('idNodoWifi'));           

            //consulto el servicio navegacion del nodo wifi
            $objdetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array('detalleNombre' => "ID_PUNTO",
                                                                      "elementoId"    => $peticion->get('idNodoWifi'),
                                                                      'estado'        => 'Activo'));
            if($objdetalleElemento)
            {
                $objServicioNavega = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->findOneBy(array('puntoId'                    => $objdetalleElemento->getDetalleValor(),
                                                                   "descripcionPresentaFactura" => "Concentrador L3MPLS Navegacion",
                                                                   'estado'                     => 'Activo'));
                if($objServicioNavega)
                {
                    $objServicioTecnicoNavega = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->findOneByServicioId($objServicioNavega->getId());
                }

            }              
            if(!$objServicioTecnico)
            {
                $objServicioTecnico  = new InfoServicioTecnico();                
                $objServicioTecnico->setServicioId($objServicio);
                $objServicioTecnico->setTipoEnlace('PRINCIPAL');           
                $objServicioTecnico->setUltimaMillaId($objAdmiTipoMedio->getId());
                $emComercial->persist($objServicioTecnico);
                $emComercial->flush();
                
            }
            
            if($objServicio->getEstado() == 'Activo' || $objServicio->getEstado() == 'In-Corte')
            {
                
                //elimino el enlace y libero las interfaces
                if($objServicioTecnico->getInterfaceElementoConectorId())
                {
                    $objEnlaceEdit = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                   ->findOneBy(array('interfaceElementoIniId' => $objServicioTecnico->getInterfaceElementoConectorId(),
                                                     'estado'                 => 'Activo'));
                    if($objEnlaceEdit)
                    {
                        $objInterfaceIni = $objEnlaceEdit->getInterfaceElementoIniId();
                        $objInterfaceFin = $objEnlaceEdit->getInterfaceElementoFinId();

                        $objEnlaceEdit->setEstado("Eliminado");
                        $emInfraestructura->persist($objEnlaceEdit);
                        $emInfraestructura->flush();

                        $objInterfaceIni->setEstado("not connect");
                        $emInfraestructura->persist($objInterfaceIni);
                        $emInfraestructura->flush();

                        $objInterfaceFin->setEstado("not connect");
                        $emInfraestructura->persist($objInterfaceFin);
                        $emInfraestructura->flush();

                    }
                    else
                    {
                        if($objServicioTecnico->getInterfaceElementoConectorId())
                        {
                            //no tiene enlace pero si debo reversar el puerto del router wifi
                            $objInterfaceElementoEdit = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($objServicioTecnico->getInterfaceElementoConectorId());

                            $objInterfaceElementoEdit->setEstado('not connect');
                            $emInfraestructura->persist($objInterfaceElementoEdit);
                            $emInfraestructura->flush();
                        }
                    }                    
                }
                                
                $objSpcEnlace = $serviceServicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);
                
                if($objSpcEnlace)
                {
                    $objSpcEnlace->setEstado("Eliminado");
                    $emComercial->persist($objSpcEnlace);
                    $emComercial->flush();
                }
                
                $objSpc = $serviceServicioGeneral->getServicioProductoCaracteristica($objServicio, "INTERFACE_ELEMENTO_ID", $objProducto);

                //elimino el enlace del elemento wifi a el odf (caso backbone)
                if($objSpc)
                {
                    //elimino la caracteristica
                    $objSpc->setEstado("Eliminado");
                    $emComercial->persist($objSpc);
                    $emComercial->flush();
                    //eliminamos el enlace
                    $objEnlaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                       ->findOneBy(array('interfaceElementoIniId' => $objSpc->getValor(),
                                                                         'estado'                 => 'Activo'));

                    if($objEnlaceElemento)
                    {
                        $objInterfaceIni = $objEnlaceElemento->getInterfaceElementoIniId();
                        $objInterfaceFin = $objEnlaceElemento->getInterfaceElementoFinId();

                        $objEnlaceElemento->setEstado("Eliminado");
                        $emInfraestructura->persist($objEnlaceElemento);
                        $emInfraestructura->flush();

                        $objInterfaceIni->setEstado("not connect");
                        $emInfraestructura->persist($objInterfaceIni);
                        $emInfraestructura->flush();

                        $objInterfaceFin->setEstado("not connect");
                        $emInfraestructura->persist($objInterfaceFin);
                        $emInfraestructura->flush();

                    }
                    else
                    {
                        if($objSpc->getValor())
                        {
                            //no tiene enlace pero si debo reversar el puerto del router wifi
                            $objInterfaceElementoEdit = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($objSpc->getValor());

                            $objInterfaceElementoEdit->setEstado('not connect');
                            $emInfraestructura->persist($objInterfaceElementoEdit);
                            $emInfraestructura->flush();
                        }
                    }
                }
            }
            else
            {
                $respuesta->setContent('No se puede editar factibilidad porque el servicio está en estado ' . $objServicio->getEstado());
                return $respuesta;
            }

            //actualizo el puerto de la interface a connected

            $objInterfaceElemento->setEstado('connected');
            $emInfraestructura->persist($objInterfaceElemento);
            $emInfraestructura->flush();

            if($modeloNodoWifi == 'BACKBONE')
            {
                //obtengo el IN del odf
                $objEnlaceOdfIn = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                    ->findOneBy(array('interfaceElementoFinId' => $idInterfaceOdf,
                                                                      'estado' => 'Activo'));
                if($objEnlaceOdfIn)
                {
                    $objInterfaceOdf = $objEnlaceOdfIn->getinterfaceElementoIniId();
                }

                //actualizo a ocupado la interface del elemento wifi
                $objInterfaceElementoWifi = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneById($idInterfaceElemento);
                $objInterfaceElementoWifi->setEstado('connected');
                $emInfraestructura->persist($objInterfaceElementoWifi);
                $emInfraestructura->flush();

                //guardo el elemento wifi como servicio prod caract                    
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "INTERFACE_ELEMENTO_ID", 
                                                                                $idInterfaceElemento, $session->get('user'));
                if ($objServicioTecnicoNavega)
                {
                    $idInterfaceElemento = $objServicioTecnicoNavega->getInterfaceElementoId();
                }
                else
                {
                    //obtengo el sw de backbone y lo relaciono al odf
                    $idInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->getInterfaceElementoPadre($idElemento, 'ELEMENTO', 'SWITCH');
                }

                if($idInterfaceElemento)
                {
                    $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($idInterfaceElemento);
                    $idElemento = $objInterfaceElemento->getElementoId()->getId();
                    $nombreElemento = $objInterfaceElemento->getElementoId()->getNombreElemento();
                    $nombreInterfaceElemento = $objInterfaceElemento->getNombreInterfaceElemento();
                }
                else
                {
                    $respuesta->setContent("El elemento no está enlazado a un Switch de Backbone, favor crear la relación. ");
                    return $respuesta;
                }

                if($objInterfaceElemento)
                {

                    $arrayBufferHilo = $emInfraestructura->getRepository('schemaBundle:InfoBufferHilo')
                                                         ->getBufferHiloBy('ROJO', 'TRANSPARENTE', 'FIBRA DE 1 HILO', 10);
                    $idHiloBuffer = $arrayBufferHilo['registros'][0]['idBufferHilo'];

                    if($idHiloBuffer)
                    {
                        $objBufferHilo = $emInfraestructura->getRepository('schemaBundle:InfoBufferHilo')->find($idHiloBuffer);
                    }
                    //creo el enlace del router wifi con el odf
                    $enlace = new InfoEnlace();
                    $enlace->setInterfaceElementoIniId($objInterfaceElementoWifi);
                    $enlace->setInterfaceElementoFinId($objInterfaceOdf);
                    $enlace->setTipoMedioId($objAdmiTipoMedio);
                    $enlace->setTipoEnlace("PRINCIPAL");
                    $enlace->setEstado("Activo");
                    $enlace->setBufferId($objBufferHilo);
                    $enlace->setUsrCreacion($session->get('user'));
                    $enlace->setFeCreacion(new \DateTime('now'));
                    $enlace->setIpCreacion($peticion->getClientIp());
                    $emInfraestructura->persist($enlace);
                    $emInfraestructura->flush();

                    $objInterfaceOdf->setEstado('connected');
                    $emInfraestructura->persist($objInterfaceOdf);
                    $emInfraestructura->flush();

                    $objInterfaceElemento->setEstado('connected');
                    $emInfraestructura->persist($objInterfaceElemento);
                    $emInfraestructura->flush();
                }
            }
            else
            {
                
                if ($objServicioTecnicoNavega)
                {
                    $idInterfaceElemento = $objServicioTecnicoNavega->getInterfaceElementoId();
                }
                else
                {
                    //obtengo el id elemento del element padre
                    $idInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->getInterfaceElementoPadre($idElementoConector, 'ELEMENTO', 'SWITCH');
                }
                if($idInterfaceElemento)
                {
                    $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneById($idInterfaceElemento);
                    if($objInterfaceElemento)
                    {
                        $idElemento = $objInterfaceElemento->getElementoId()->getId();
                        $nombreElemento = $objInterfaceElemento->getElementoId()->getNombreElemento();
                        $nombreInterfaceElemento = $objInterfaceElemento->getNombreInterfaceElemento();
                    }
                }
                else
                {
                    $respuesta->setContent("El elemento no está enlazado a un Switch de Backbone, favor revisar los enlaces. ");
                    return $respuesta;
                }
                //guardo la interface del elemetno wifi
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                $objProducto,
                                                                                "INTERFACE_ELEMENTO_ID", 
                                                                                $idInterfaceElementoConector, 
                                                                                $session->get('user'));
            }

            $objElementoInterfaceConector = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($idInterfaceElementoConector);
            if($objElementoInterfaceConector)
            {
                $nombreElementoConector = $objElementoInterfaceConector->getElementoId()->getNombreElemento();
                $nombreInterfaceElementoConector = $objElementoInterfaceConector->getNombreInterfaceElemento();
            }

            
            $objServicioTecnico->setElementoId($idElemento);
            $objServicioTecnico->setInterfaceElementoId($idInterfaceElemento);
            $objServicioTecnico->setElementoConectorId($idElementoConector);
            $objServicioTecnico->setInterfaceElementoConectorId($idInterfaceElementoConector);
            $objServicioTecnico->setElementoContenedorId($idElementoContenedor);
            $emComercial->persist($objServicioTecnico);
            $emComercial->flush();
            
            if($objServicioTecnico->getInterfaceElementoClienteId())
            {
                
                $objInterfaceConector = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($objServicioTecnico->getInterfaceElementoConectorId());
                $objInterfaceCliente = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->find($objServicioTecnico->getInterfaceElementoClienteId());
                if ($objInterfaceConector && $objInterfaceCliente)
                {
                    //creo el enlace con el elemento cliente id
                    $enlaceCliente = new InfoEnlace();
                    $enlaceCliente->setInterfaceElementoIniId($objInterfaceConector);
                    $enlaceCliente->setInterfaceElementoFinId($objInterfaceCliente);
                    $enlaceCliente->setTipoMedioId($objAdmiTipoMedio);
                    $enlaceCliente->setTipoEnlace("PRINCIPAL");
                    $enlaceCliente->setEstado("Activo");
                    $enlaceCliente->setUsrCreacion($session->get('user'));
                    $enlaceCliente->setFeCreacion(new \DateTime('now'));
                    $enlaceCliente->setIpCreacion($peticion->getClientIp());
                    $emInfraestructura->persist($enlaceCliente);
                    $emInfraestructura->flush();
                }
            }

            $objInterfaceElemento->setEstado('connected');
            $emInfraestructura->persist($objInterfaceElemento);
            $emInfraestructura->flush();

            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion('Se realizó cambio de Nodo Wifi: '
                . '<br> Nodo: ' . $objNodoWifi->getNombreElemento()
                . '<br> Elemento: ' . $nombreElemento
                . '<br> Puerto: ' . $nombreInterfaceElemento
                . '<br> Elemento Conector: ' . $nombreElementoConector
                . '<br> Puerto: ' . $nombreInterfaceElementoConector);
            $objServicioHistorial->setUsrCreacion($session->get('user'));
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($peticion->getClientIp());
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $emComercial->persist($objServicioHistorial);
            $emComercial->flush();
            
            //ingreso el enlace de datos
            if($objServicioNavega)
            {
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                $objProducto, 
                                                                                "ENLACE_DATOS", 
                                                                                $objServicioNavega->getId(), 
                                                                                $session->get('user'));
            }    
            
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $objServicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
            $arrayCambioBw['strOperacion']      = 'SUMA';
            $arrayCambioBw['usrCreacion']       = $session->get('user');
            $arrayCambioBw['ipCreacion']        = $peticion->getClientIp();
            
            $arrayCambio = $serviceWifi->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }
 
            $emComercial->getConnection()->commit();
            $emInfraestructura->getConnection()->commit();
            $respuesta->setContent("OK");
        }
        catch(\Exception $ex)
        {
            $emInfraestructura->getConnection()->rollback();
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError('Telcos+', 'cambioNodoWifiLogico', $ex->getMessage(), $session->get('user'), $peticion->getClientIp());
            $mensajeError = "Error en la ejecución, notificar a sistemas.";
            $respuesta->setContent($mensajeError);
        }
        $emInfraestructura->getConnection()->close();
        $emComercial->getConnection()->close();
        return $respuesta;

    }
    
     /**
     * getLoginPorNodo
     * obtener los logines por nodo wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-09-2016
     */ 

    public function getLoginPorNodoAction()
    {
        ini_set('max_execution_time', 650000);
        //Declaracion de variables
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
                
        $objPeticion   = $this->get('request');
        
        $intIdElemento = $objPeticion->get('idElemento');
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $strRespuestaJson = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                              ->getJsonLoginPorNodoWifi($intIdElemento);
        
        return $objRespuesta->setContent($strRespuestaJson);
    }    

}
