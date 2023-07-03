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
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\schemaBundle\Form\InfoElementoNodoClienteType;
use Telconet\tecnicoBundle\Resources\util\Util;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * InfoElementoNodoClienteController
 *
 * logica de negocio de los elementos edificacion
 *
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 09-03-2016
 */

class InfoElementoNodoClienteController extends Controller
{ 
    
    /**
    * indexNodoClienteAction
    * funcion que valida los permisos y renderiza el index de la administración
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function indexNodoClienteAction(){
        
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_327-3398'))
        {
                $rolesPermitidos[] = 'ROLE_327-3398'; //editar elemento caja
        }
        if (true === $this->get('security.context')->isGranted('ROLE_327-3399'))
        {
                $rolesPermitidos[] = 'ROLE_327-3399'; //eliminar elemento caja
        }        
        
        return $this->render('tecnicoBundle:InfoElementoNodoCliente:index.html.twig', array(
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * newNodoClienteAction
    * renderiza el formulario para un ingreso nuevo
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function newNodoClienteAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoNodoClienteType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoNodoCliente:new.html.twig', array(
                             'entity' => $entity,
                             'form'   => $form->createView()
        ));
    }

    /**
     * createNodoClienteAction
     * función que crea el elemento en la base de datos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 22-05-2017 - Se modifica para que cuando el edificio sea de tipo NODO SATELITAL no genere solicitud de factibilidad
     *                          y se cree como un esquema PSEUDOPE de manera automatica
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     */
    public function createNodoClienteAction()
    {
        $request            = $this->get('request');
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $elemento           = new InfoElemento();
        $form               = $this->createForm(new InfoElementoNodoClienteType(), $elemento);
        $parametros         = $request->request->get('telconet_schemabundle_infoelementonodoclientetype');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $nombreElemento     = strtoupper($parametros['nombreElemento']);
        $modeloElementoId   = $parametros['modeloElementoId'];
        $parroquiaId        = $parametros['parroquiaId'];
        $alturaSnm          = $parametros['alturaSnm'];
        $longitudUbicacion  = $parametros['longitudUbicacion'];
        
        $latitudUbicacion   = $parametros['latitudUbicacion'];
        $direccionUbicacion = $parametros['direccionUbicacion'];
        $descripcionElemento= $parametros['descripcionElemento'];

        $em->beginTransaction();
        try
        {
      
            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                     "estado"           => array("Activo", "Pendiente","Factible","PreFactibilidad"),
                                                     "modeloElementoId" => $modeloElementoId));

            if($elementoRepetido)
            {
                throw new \Exception('Nombre ya existe en otro Elemento con estado '.$elementoRepetido->getEstado());
            }
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "de la edificación"));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                
            }
            $objSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud('SOLICITUD EDIFICACION');

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
            
            $strEstado                 = 'Pendiente';
            $strEstadoSolicitud        = 'PreFactibilidad';
            $boolEsEdifcioConvencional = true;
            
            if($modeloElemento->getNombreModeloElemento() == 'NODO SATELITAL')
            {
                $strEstado                 = 'Activo';
                $strEstadoSolicitud        = 'FactibilidadEquipos';
                $boolEsEdifcioConvencional = false;
            }

            $elemento->setNombreElemento($nombreElemento);
            $elemento->setDescripcionElemento($descripcionElemento);
            $elemento->setModeloElementoId($modeloElemento);
            $elemento->setUsrResponsable($session->get('user'));
            $elemento->setUsrCreacion($session->get('user'));
            $elemento->setFeCreacion(new \DateTime('now'));
            $elemento->setIpCreacion($peticion->getClientIp());
            $elemento->setEstado($strEstado);

            $em->persist($elemento);
            $em->flush();  

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento($strEstado);
            $historialElemento->setObservacion("Se ingreso una Edificación");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);
            $em->flush();

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
            
            $entityDetalleSolicitud = new infoDetalleSolicitud();
            $entityDetalleSolicitud->setTipoSolicitudId($objSolicitud);
            $entityDetalleSolicitud->setObservacion('Creado desde la administración de Edificacion');
            $entityDetalleSolicitud->setUsrCreacion($session->get('user'));
            $entityDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolicitud->setEstado($strEstadoSolicitud);
            $entityDetalleSolicitud->setElementoId($elemento->getId());
            $em->persist($entityDetalleSolicitud);
            $em->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityDetalleSolHist = new InfoDetalleSolHist();
            $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
            $entityDetalleSolHist->setEstado($strEstadoSolicitud);
            $entityDetalleSolHist->setObservacion('Creado desde la administración');
            $em->persist($entityDetalleSolHist);
            $em->flush();
            
            //Si es de tipo NODO SATELITAL genera la configuracion de esquema PseudoPe
            if(!$boolEsEdifcioConvencional)
            {
                //Si es nodo SATELITAL se crean la referencia PSEUDOPE automaticamente sin pasar por Factibilidad Manual de parte
                //de GIS
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setEstado('Activo');
                $objInfoDetalleElemento->setElementoId($elemento->getId());
                $objInfoDetalleElemento->setDetalleNombre('ADMINISTRA');
                $objInfoDetalleElemento->setDetalleValor('CLIENTE');
                $objInfoDetalleElemento->setDetalleDescripcion('ADMINISTRA');
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoDetalleElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objInfoDetalleElemento);
                $em->flush();
                
                $objInfoDetalleElemento1 = new InfoDetalleElemento();
                $objInfoDetalleElemento1->setEstado('Activo');
                $objInfoDetalleElemento1->setElementoId($elemento->getId());
                $objInfoDetalleElemento1->setDetalleNombre('TIPO_ELEMENTO_RED');
                $objInfoDetalleElemento1->setDetalleValor('PSEUDO_PE');
                $objInfoDetalleElemento1->setDetalleDescripcion('TIPO_ELEMENTO_RED');
                $objInfoDetalleElemento1->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento1->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoDetalleElemento1->setIpCreacion($peticion->getClientIp());
                $em->persist($objInfoDetalleElemento1);
                $em->flush();     
                
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setEstado('Activo');
                $objInfoDetalleElemento->setElementoId($elemento->getId());
                $objInfoDetalleElemento->setDetalleNombre('TIPO_ADMINISTRACION');
                $objInfoDetalleElemento->setDetalleValor('SATELITAL');
                $objInfoDetalleElemento->setDetalleDescripcion('TIPO_ADMINISTRACION');
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoDetalleElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objInfoDetalleElemento);
                $em->flush(); 
            }
            
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
            return $this->redirect($this->generateUrl('elementoNodoCliente_new'));
        }
     
        return $this->redirect($this->generateUrl('elementoNodoCliente_show', array('id' => $elemento->getId())));
        
    }
   
    /**
    * editNodoClienteAction
    * función que renderiza el formulario para una edición
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function editNodoClienteAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) 
        {
            throw new NotFouncargarDatosNodoClientedHttpException('No existe el elemento que se quiere modificar');
        }
        else
        {            
            $elementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findBy(array( "elementoId" =>$elemento->getId()));
            $ubicacion = $em->getRepository('schemaBundle:InfoUbicacion')->findBy(array( "id" =>$elementoUbica[0]->getUbicacionId()));
        }

        $formulario =$this->createForm(new InfoElementoNodoClienteType(array("empresaId"=>$empresaId)), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoNodoCliente:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'caja'                  => $elemento,
                                'ubicacion'             => $ubicacion[0])
                            );
    }
    
   /**
    * updateNodoClienteAction
    * funcion que actualiza el elemento en la BD
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
    */     
    public function updateNodoClienteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }

        $request            = $this->get('request');
        $session            = $request->getSession();
        $parametros         = $request->request->get('telconet_schemabundle_infoelementonodoclientetype');        
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
                                                                                                        "msjTipoElemento"   => "del nodo cliente "));
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
                    return $this->redirect($this->generateUrl('elementoNodoCliente_edit', array('id' => $entity->getId())));
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
            $historialElemento->setObservacion("Se modifico la edificación");
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

            return $this->redirect($this->generateUrl('elementoNodoCliente_show', array('id' => $entity->getId())));

        }        
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', "Error: " . $e->getMessage());
            return $this->redirect($this->generateUrl('elementoNodoCliente_edit', array('id' => $entity->getId())));
        }
        
    }
    
   /**
    * showNodoClienteAction
    * funcion que renderiza el form en donde aparece la información del elemento
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */         
    public function showNodoClienteAction($id){
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

        return $this->render('tecnicoBundle:InfoElementoNodoCliente:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    /**
    * getEncontradosNodoCliente
    * obtiene las nodo clientes ingresados en la base
    *
    * @return json con la data de los nodo clientes
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 15-05-2016 nuevo filtro de direccion
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.2 07-06-2017 Se envia en parametro nombre del modelo del elemento a ser buscado
    *
    * @author Joel Broncano <jbroncano@telconet.ec>
    * @version 1.2 08-03-2023 Se envia en parametro MD cuando se conslta con EN
    */    
    public function getEncontradosNodoClienteAction(){
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
        $direccion      = $peticion->get('direccion');
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        $strModelo      = "";
        
        $objSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD EDIFICACION',
                                                                            'estado' => 'Activo'));

        $arrayParametros = array();
        $arrayParametros['start']           = $start;
        $arrayParametros['limit']           = $limit;
        $arrayParametros["idSolicitud"]     = $objSolicitud->getId();
        //Se consulta con Md cuando se esta con la empesa EN
        $arrayParametros["codEmpresa"]      = $idEmpresa=="33"?"18":$idEmpresa;
        $arrayParametros["nombreNodo"]      = $nombreElemento;
        $arrayParametros["idCanton"]        = $canton;
        $arrayParametros["modeloElemento"]  = $modeloElemento;
        $arrayParametros["estadoElemento"]  = $estado;
        $arrayParametros["direccion"]       = $direccion;
        
        $objModeloElemento = $em->getRepository("schemaBundle:AdmiModeloElemento")->find($modeloElemento);
        
        if(is_object($objModeloElemento))
        {
            $strModelo = $objModeloElemento->getNombreModeloElemento();
        }
        
        $arrayParametros["nombreModelo"]    = $strModelo;
        
        $respuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoElemento')
                                                    ->getJsonRegistrosEdificacion($arrayParametros);

        $respuesta->setContent($respuestaSolicitudes);

        return $respuesta;
    }
    
   /**
    * cargarDatosNodoClienteAction
    * funcion que retorna un json con los datos del elemento
    *
    * @return json con datos del elemento
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function cargarDatosNodoClienteAction(){
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
    * deleteAjaxNodoClienteAction
    * función que elimina uno o varios elementos
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function deleteAjaxNodoClienteAction(){
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
                                                              ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD EDIFICACION',
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
    * getElementosPorNodoClienteAction
    * funcion que obtiene los elementos relacionados en la tabla infoRelacionElemento
    *
    * @return json
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function getElementosPorNodoClienteAction($id){
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
     * Metodo que devuelve la informacion de los elementos de tipo pseudope
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 30-01-2017
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetElementosPseudoPeAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objRequest        =  $this->getRequest();        
        $strQuery          =  $objRequest->get('query')?$objRequest->get('query'):'';
        
        $arrayParametros             = array();
        $arrayParametros['strQuery'] = $strQuery;
        
        $objJson           = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getJsonResultadoEdificiosPseudoPe($arrayParametros);
        
        $objResponse = new Response();
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
}