<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Form\InfoElementoPopType;
use Telconet\tecnicoBundle\Resources\util\Util;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class InfoElementoPopController extends Controller
{ 
    public function indexPopAction(){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $parametros = array();
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"POP"));
        
        //MODULO 50 - POP
        
        if (true === $this->get('security.context')->isGranted('ROLE_150-5'))
        {
                $rolesPermitidos[] = 'ROLE_150-5'; //editar elemento pop
        }
        if (true === $this->get('security.context')->isGranted('ROLE_150-8'))
        {
                $rolesPermitidos[] = 'ROLE_150-8'; //eliminar elemento pop
        }
        if (true === $this->get('security.context')->isGranted('ROLE_150-6'))
        {
                $rolesPermitidos[] = 'ROLE_150-6'; //ver elemento pop
        }
        
        $parametros["nombre"]       = '';
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $tipoElemento[0]->getId();
        $parametros["start"]        = '0';
        $parametros["limit"]        = '1000';

        $entities = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->getElementosXTipo($parametros);
        return $this->render('tecnicoBundle:InfoElementoPop:index.html.twig', array(
            'entities' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    public function newPopAction(){
        $request = $this->get('request');
        $session  = $request->getSession();
        $empresaId=$session->get('idEmpresa');
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoPopType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoPop:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Función para crear un elemento pop
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * @since 1.0
     */
    public function createPopAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $em->beginTransaction();
        try
        {
            $elementoPop  = new InfoElemento();
            $form    = $this->createForm(new InfoElementoPopType(), $elementoPop);

            $parametros = $request->request->get('telconet_schemabundle_infoelementopoptype');
            $peticion = $this->get('request');
            $session  = $peticion->getSession();
            $nombreElemento = $parametros['nombreElemento'];
            $modeloElementoId = $parametros['modeloElementoId'];
            $jurisdiccionId = $parametros['jurisdiccionId'];
            $cantonId = $parametros['cantonId'];
            $parroquiaId = $parametros['parroquiaId'];
            $alturaSnm = $parametros['alturaSnm'];
            $longitudUbicacion = $parametros['longitudUbicacion'];
            $latitudUbicacion = $parametros['latitudUbicacion'];
            $direccionUbicacion = $parametros['direccionUbicacion'];
            $descripcionElemento = $parametros['descripcionElemento'];

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            $elementoPop->setNombreElemento($nombreElemento);
            $elementoPop->setDescripcionElemento($descripcionElemento);
            $elementoPop->setModeloElementoId($modeloElemento);
            $elementoPop->setUsrResponsable($session->get('user'));
            $elementoPop->setUsrCreacion($session->get('user'));
            $elementoPop->setFeCreacion(new \DateTime('now'));
            $elementoPop->setIpCreacion($peticion->getClientIp());       

            $form->handleRequest($request);

            $em->persist($elementoPop);
            $em->flush();

            $elementoPop->setEstado("Activo");
            $em->persist($elementoPop);
            $em->flush();

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elementoPop);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un Pop");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);
            $em->flush();

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del pop "));
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
            $empresaElementoUbica->setElementoId($elementoPop);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);
            $em->flush();

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elementoPop);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($peticion->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);
            $em->flush();

            $em->commit();
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementopop_newPop'));
        }
        
        return $this->redirect($this->generateUrl('elementopop_showPop', array('id' => $elementoPop->getId())));
        
    }
    
    /**
     * Función para editar un elemento pop
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-09-2018 - Se corrige función para editar correctamente un elemento
     * @since 1.0
     */
    public function editPopAction($id){
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $em             = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $objElemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -pop- que se quiere modificar');
        }
        else
        {
            $objElementoUbica       = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                         ->findOneBy(array("elementoId" => $objElemento->getId(),
                                                           "empresaCod" => $strCodEmpresa));
            $objUbicacion           = $em->getRepository('schemaBundle:InfoUbicacion')
                                         ->findOneBy(array("id" => $objElementoUbica->getUbicacionId()));
            $objParroquia           = $em->getRepository('schemaBundle:AdmiParroquia')
                                         ->findOneBy(array("id" => $objUbicacion->getParroquiaId()));
            $objCanton              = $em->getRepository('schemaBundle:AdmiCanton')
                                         ->findOneBy(array("id" => $objParroquia->getCantonId()));
            $objCantonJurisdiccion  = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                                         ->findOneBy(array("cantonId" => $objCanton->getId()));
        }
        $objForm = $this->createForm(new InfoElementoPopType(array("empresaId" => $strCodEmpresa)), $objElemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoPop:edit.html.twig', 
                                array(
                                        'edit_form'             => $objForm->createView(),
                                        'pop'                   => $objElemento,
                                        'ubicacion'             => $objUbicacion,
                                        'cantonJurisdiccion'    => $objCantonJurisdiccion));
    }

    /**
     * Función para actualizar un elemento pop
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * @since 1.0
     */
    public function updatePopAction($id){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em->beginTransaction();
        try
        {
            $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }

            $request = $this->get('request');
            $peticion = $this->get('request');
            $session  = $peticion->getSession();
            $parametros = $request->request->get('telconet_schemabundle_infoelementopoptype');

            $nombreElemento = $parametros['nombreElemento'];
            $descripcionElemento = $parametros['descripcionElemento'];
            $modeloElementoId = $parametros['modeloElementoId'];
            $jurisdiccionId = $parametros['jurisdiccionId'];
            $cantonId = $parametros['cantonId'];
            $parroquiaId = $parametros['parroquiaId'];
            $direccionUbicacion = $request->request->get('direccionUbicacion');
            $longitudUbicacion = $request->request->get('longitudUbicacion');
            $latitudUbicacion = $request->request->get('latitudUbicacion');
            $alturaSnm = $request->request->get('alturaSnm');
            $idUbicacion = $request->request->get('idUbicacion');
            //$nodoElementoId = $parametros['nodoElementoId'];

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            //elemento
            $entity->setNombreElemento($nombreElemento);
            $entity->setDescripcionElemento($descripcionElemento);
            $entity->setModeloElementoId($modeloElemento);
            $entity->setUsrResponsable($session->get('user'));
            $entity->setUsrCreacion($session->get('user'));
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setIpCreacion($peticion->getClientIp());   
            $em->persist($entity);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Modificado");
            $historialElemento->setObservacion("Se modifico un Pop");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del pop "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);
            $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
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
            $em->commit();
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementopop_editPop', array('id' => $id)));
        }
        return $this->redirect($this->generateUrl('elementopop_showPop', array('id' => $entity->getId())));
    }
    
    public function deletePopAction($id){
        $request = $this->getRequest();
        $session  = $request->getSession();
        $peticion = $this->get('request');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $em->getConnection()->beginTransaction();

        //elemento
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setIpCreacion($peticion->getClientIp());  
        $em->persist($entity);

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($entity);
        $historialElemento->setEstadoElemento("Eliminado");
        $historialElemento->setObservacion("Se elimino un Pop");
        $historialElemento->setUsrCreacion($session->get('user'));
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($peticion->getClientIp());
        $em->persist($historialElemento);
        
        //relacion elemento
        $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elmentoIdB" =>$entity));
        $relacionElemento[0]->setEstado("Eliminado");
        $relacionElemento[0]->setUsrCreacion($session->get('user'));
        $relacionElemento[0]->setFeCreacion(new \DateTime('now'));
        $relacionElemento[0]->setIpCreacion($peticion->getClientIp());
        $em->persist($relacionElemento[0]);
            
        $em->flush();
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('elementopop'));
    }
    
    public function deleteAjaxPopAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
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
                $historialElemento->setObservacion("Se elimino un Pop");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);
                
                //relacion elemento
                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elmentoIdB" =>$entity));
                $relacionElemento[0]->setEstado("Eliminado");
                $relacionElemento[0]->setUsrCreacion($session->get('user'));
                $relacionElemento[0]->setFeCreacion(new \DateTime('now'));
                $relacionElemento[0]->setIpCreacion($peticion->getClientIp());
                $em->persist($relacionElemento[0]);
                
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    public function showPopAction($id){
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

            $ipElemento     = $respuestaElemento['ipElemento'];
            $arrayHistorial = $respuestaElemento['historialElemento'];
            $ubicacion      = $respuestaElemento['ubicacion'];
            $jurisdiccion   = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoPop:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    public function getEncontradosPopAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"POP"));
        
        $peticion = $this->get('request');
        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $modeloElemento = $peticion->query->get('modeloElemento');
        $marcaElemento = $peticion->query->get('marcaElemento');
        $canton = $peticion->query->get('canton');
        $jurisdiccion = $peticion->query->get('jurisdiccion');
        $estado = $peticion->query->get('estado');
        $idEmpresa = $session->get('idEmpresa');
        
        $start = $peticion->query->get('start');
        $limit = 1000;
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonPops(strtoupper($nombreElemento),$modeloElemento,$marcaElemento,$tipoElemento[0]->getId(),$canton,$jurisdiccion,$estado,$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * Función para cargar los datos de un elemento pop
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-09-2018 Se modifica el envío de parámetros a la función generarJsonCargarDatosPop, agregándole el id de la empresa
     * @since 1.0
     */
    public function cargarDatosPopAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objSession     = $this->get('session');
        $objRequest     = $this->getRequest();
        $objJson        = $this->getDoctrine()->getManager("telconet_infraestructura")
                                              ->getRepository('schemaBundle:InfoElemento')
                                              ->generarJsonCargarDatosPop(array("idPop"         => $objRequest->get('idPop'), 
                                                                                "codEmpresa"    => $objSession->get('idEmpresa')));
        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }

    public function getElementosPorPopAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->get('request');
        $session  = $request->getSession();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $empresa = $session->get('idEmpresa');
        
        $peticion = $this->get('request');
        
        $popId = $id;
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosPorPop($popId,$empresa,"Activo",$start, 100, $emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
}