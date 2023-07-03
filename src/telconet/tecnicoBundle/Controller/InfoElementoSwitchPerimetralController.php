<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Form\InfoElementoSwitchPerimetralType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Telconet\tecnicoBundle\Resources\util\Util;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * InfoElementoSwitchPerimetralController
 *
 * logica de negocio de los elementos 
 *
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 09-03-2016
 */

class InfoElementoSwitchPerimetralController extends Controller
{ 
    
    /**
    * indexSwitchPerimetralAction
    * funcion que valida los permisos y renderiza el index de la administración
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
    */ 
    
    public function indexAction(){
        
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_358-4497'))
        {
                $rolesPermitidos[] = 'ROLE_358-4497'; //NewSwitchPerimetral
        }
        if (true === $this->get('security.context')->isGranted('ROLE_358-4517'))
        {
                $rolesPermitidos[] = 'ROLE_358-4517'; //EditSwitchPerimetral
        }
        if (true === $this->get('security.context')->isGranted('ROLE_358-4518'))
        {
                $rolesPermitidos[] = 'ROLE_358-4518'; //eliminar elemento 
        }
      
        
        return $this->render('tecnicoBundle:InfoElementoSwitchPerimetral:index.html.twig', array(
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_358-4497")
    * 
    * newAction
    * renderiza el formulario para un ingreso nuevo
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
    */ 
    
    public function newAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoSwitchPerimetralType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoSwitchPerimetral:new.html.twig', array(
                             'entity' => $entity,
                             'form'   => $form->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_358-4497")
     * 
     * createAction
     * función que crea el elemento en la base de datos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 27-07-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 11-10-2016 Se aumenta validación para que el elemento se procese en el NAF
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function createAction()
    {
        $request            = $this->get('request');
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $elemento           = new InfoElemento();
        $form = $this->createForm(new InfoElementoSwitchPerimetralType(), $elemento);
        $parametros         = $request->request->get('telconet_schemabundle_infoelementoswitchperimetraltype');
        $mac                = $request->request->get('mac');
        $session            = $request->getSession();
        $empresaId          = $session->get('idEmpresa');
        $nombreElemento     = $parametros['nombreElemento'];
        $serieFisica        = $parametros['serieFisica'];
        $modeloElementoId   = $parametros['modeloElementoId'];
        $versionOs          = $parametros['versionOs'];
        $nombreNodoElementoId = $request->request->get('combo_nodos');
        $descripcionElemento = $parametros['descripcionElemento'];

        $em->beginTransaction();
        $emComercial->beginTransaction();
        
        try
        {
            $objNodo = $em->getRepository('schemaBundle:InfoElemento')->findOneByNombreElemento($nombreNodoElementoId);

            $modeloElemento = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($modeloElementoId);

            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneBy(array("nombreElemento" => $nombreElemento,
                                                             "estado" => array("Activo", "Pendiente", "Factible", "PreFactibilidad"),
                                                   "modeloElementoId" => $modeloElemento->getId()));

            if($elementoRepetido)
            {
                throw new \Exception('Nombre ya existe en otro Elemento con estado ' . $elementoRepetido->getEstado());
            }

            $elemento->setNombreElemento($nombreElemento);
            $elemento->setDescripcionElemento($descripcionElemento);
            $elemento->setModeloElementoId($modeloElemento);
            $elemento->setSerieFisica($serieFisica);
            $elemento->setVersionOs($versionOs);
            $elemento->setUsrResponsable($session->get('user'));
            $elemento->setUsrCreacion($session->get('user'));
            $elemento->setFeCreacion(new \DateTime('now'));
            $elemento->setIpCreacion($request->getClientIp());
            $elemento->setEstado("Activo");

            $em->persist($elemento);
            $em->flush();

            //buscar el interface Modelo
            $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array("modeloElementoId" => $modeloElementoId));
            
            if(!$interfaceModelo)
            {
                throw new \Exception('El modelo no tiene registrado interfaces ');            
            }
            
            foreach($interfaceModelo as $im)
            {
                $cantidadInterfaces = $im->getCantidadInterface();
                $formato = $im->getFormatoInterface();

                $start = 1;
                $fin = $cantidadInterfaces;

                for($i = $start; $i <= $fin; $i++)
                {
                    $interfaceElemento = new InfoInterfaceElemento();
                    $format = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0] . $i;
                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setElementoId($elemento);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($request->getClientIp());

                    $em->persist($interfaceElemento);
                }
            }

            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            $relacionElemento->setElementoIdA($objNodo->getId());
            $relacionElemento->setElementoIdB($elemento->getId());
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("Nodo Wifi contiene Switch");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($relacionElemento);


            //tomar datos nodo
            $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                               ->findOneBy(array("elementoId" => $objNodo->getId()));
            $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());

            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $nodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $nodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al switch ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
            $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
            $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
            $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elemento);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elemento);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($request->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);


            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso el elemento");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);
            $em->flush();


            //caracteristica para saber donde esta ubicada la switch  (pedestal - edificio)
            $detalle = new InfoDetalleElemento();
            $detalle->setElementoId($elemento->getId());
            $detalle->setDetalleNombre("TIPO ELEMENTO RED");
            $detalle->setDetalleValor("WIFI");
            $detalle->setDetalleDescripcion("Caracteristicas para indicar que es un switch de uso Wifi");
            $detalle->setFeCreacion(new \DateTime('now'));
            $detalle->setUsrCreacion($session->get('user'));
            $detalle->setIpCreacion($request->getClientIp());
            $detalle->setEstado('Activo');
            $em->persist($detalle);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la switch  (pedestal - edificio)
            $detalle1 = new InfoDetalleElemento();
            $detalle1->setElementoId($elemento->getId());
            $detalle1->setDetalleNombre("PROPIEDAD");
            $detalle1->setDetalleValor("EMPRESA");
            $detalle1->setDetalleDescripcion("Caracteristicas para indicar la propiedad");
            $detalle1->setFeCreacion(new \DateTime('now'));
            $detalle1->setUsrCreacion($session->get('user'));
            $detalle1->setIpCreacion($request->getClientIp());
            $detalle1->setEstado('Activo');
            $em->persist($detalle1);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la switch  (pedestal - edificio)
            $detalle2 = new InfoDetalleElemento();
            $detalle2->setElementoId($elemento->getId());
            $detalle2->setDetalleNombre("MAC");
            $detalle2->setDetalleValor($mac);
            $detalle2->setDetalleDescripcion("Caracteristicas mac");
            $detalle2->setFeCreacion(new \DateTime('now'));
            $detalle2->setUsrCreacion($session->get('user'));
            $detalle2->setIpCreacion($request->getClientIp());
            $detalle2->setEstado('Activo');
            $em->persist($detalle2);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la switch  (pedestal - edificio)
            $detalle4 = new InfoDetalleElemento();
            $detalle4->setElementoId($elemento->getId());
            $detalle4->setDetalleNombre("ADMINISTRA");
            $detalle4->setDetalleValor("TELCONET-CONFIGURACIÓN");
            $detalle4->setDetalleDescripcion("Caracteristicas para indicar quien administra");
            $detalle4->setFeCreacion(new \DateTime('now'));
            $detalle4->setUsrCreacion($session->get('user'));
            $detalle4->setIpCreacion($request->getClientIp());
            $detalle4->setEstado('Activo');
            $em->persist($detalle4);
            $em->flush();
            
            //actualizamos registro en el naf del cpe
            $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                        'identificacionCliente' => '',
                                        'empresaCod'            => $empresaId,
                                        'modeloCpe'             => '',
                                        'serieCpe'              => $serieFisica,
                                        'cantidad'              => '1');

            $serviceCambioElemento = $this->get('tecnico.InfoCambioElemento');
            $strMensajeError = $serviceCambioElemento->procesaInstalacionElemento($arrayParametrosNaf);
            if(strlen(trim($strMensajeError)) > 0)
            {
                throw new \Exception($strMensajeError);
            }

            $emComercial->commit();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->close();
            $mensajeError = "Error: " . $e->getMessage();
            $this->get('session')->getFlashBag()->add('notice', $mensajeError);
            return $this->redirect($this->generateUrl('elementoSwitchPerimetral_new'));
        }

        return $this->redirect($this->generateUrl('elementoSwitchPerimetral_show', array('id' => $elemento->getId())));
    }

    /**
    * @Secure(roles="ROLE_358-4517")
    * 
    * editAction
    * función que renderiza el formulario para una edición
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016    */     
    public function editAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");
        $capacidad  = '';

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) 
        {
            throw $this->createNotFoundException('No existe el elemento que se quiere modificar');
        }
        else
        {            

            $ojRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array('elementoIdB'=>$elemento->getId(),
                                                                                                           'estado' => 'Activo'));
            $objElementoContenedor = $em->getRepository('schemaBundle:InfoElemento')->find($ojRelacionElemento->getElementoIdA());
            if($objElementoContenedor)
            {
                $nombreElementoContenedor = $objElementoContenedor->getNombreElemento() ;
            }
            $infoIp = $em->getRepository('schemaBundle:InfoIp')->findOneByElementoId($elemento->getId());
            if($infoIp)
            {
                $ip = $infoIp->getIp();
            }

        }

        $formulario =$this->createForm(new InfoElementoSwitchPerimetralType(array("empresaId"=>$empresaId)), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoSwitchPerimetral:edit.html.twig', array(
                                'edit_form'                 => $formulario->createView(),
                                'objElemento'               => $elemento,
                                'nombreElementoContenedor'  => $nombreElementoContenedor,
                                'ip'                        => $ip)
                            );
    }
    
   /**
    * @Secure(roles="ROLE_358-4517")
    * 
    * updateAction
    * funcion que actualiza el elemento en la BD
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
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
        $parametros         = $request->request->get('telconet_schemabundle_infoelementoswitchperimetraltype');        
        $nombreElemento     = $parametros['nombreElemento'];
        $descripcionElemento= $parametros['descripcionElemento'];
        $serieFisica        = $parametros['serieFisica'];
        $versionOs          = $parametros['versionOs'];

        $em->getConnection()->beginTransaction();
       
        try
        {
                       
            if($nombreElemento != $entity->getNombreElemento())
            {
                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                         "modeloElementoId" => $entity->getModeloElementoId(),
                                                         "estado"           => array("Activo", "Pendiente","Factible","PreFactibilidad")));
                
                if($elementoRepetido)
                {
                    $this->get('session')->getFlashBag()->add('notice', 'Nombre ya existe en otro Elemento con estado '.
                                                                        $elementoRepetido->getEstado());
                    return $this->redirect($this->generateUrl('elementoSwitchPerimetral_edit', array('id' => $entity->getId())));
                }
                else
                {
                    $observacion = 'Nombre: '.$entity->getNombreElemento().'<br>';
                    $entity->setNombreElemento($nombreElemento);

                }
            }
            
            if($entity->getDescripcionElemento() != $descripcionElemento)
            {
                $observacion .= 'Descripcion: '.$entity->getDescripcionElemento().' <br> ';

                $entity->setDescripcionElemento($descripcionElemento);
                $em->persist($entity);
            }
            
            if($entity->getSerieFisica() != $serieFisica)
            {
                $observacion .= 'Serie Fisica: '.$entity->getSerieFisica().' <br> ';

                $entity->setSerieFisica($serieFisica);
                $em->persist($entity);
            }  
            
            if($entity->getVersionOs() != $versionOs)
            {
                $observacion .= 'Version Os: '.$entity->getVersionOs().' <br> ';

                $entity->setVersionOs($versionOs);
                $em->persist($entity);
            } 
            
           
            if ($observacion)
            {
                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Modificado");
                $historialElemento->setObservacion('Dato Anterior: <br>'.$observacion);
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($request->getClientIp());
                $em->persist($historialElemento);
            }
            
            $em->flush();
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('elementoSwitchPerimetral_show', array('id' => $entity->getId())));

        }        
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $mensajeError = "Error: " . $e->getMessage();
            $this->get('session')->getFlashBag()->add('notice', $mensajeError);
            return $this->redirect($this->generateUrl('elementoSwitchPerimetral_show', array('id' => $entity->getId())));
        }
        
    }
    
   /**
    * showAction
    * funcion que renderiza el form en donde aparece la información del elemento
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
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
            
            //obtenemos la mac y la capacidad
            $objDetalleCapacidad = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'    => $id, 
                                                                                                           'estado'        => 'Activo', 
                                                                                                           'detalleNombre' => 'CAPACIDAD'));
            if($objDetalleCapacidad)
            {
                $capacidad = $objDetalleCapacidad->getDetalleValor();
            }
            $objDetalleMac = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'      => $id, 
                                                                                                     'estado'          => 'Activo', 
                                                                                                     'detalleNombre'   => 'MAC'));
            if($objDetalleMac)
            {
                $mac = $objDetalleMac->getDetalleValor();
            }            
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoSwitchPerimetral:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'mac'               => $mac,
            'capacidad'         => $capacidad,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    /**
    * getEncontrados
    * obtiene todos los registro de este tipo ingresados en la base de datos para mostrarlos en el grid
    *
    * @return json con la data 
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 11-07-2017   Se regulariza los cambios hechos en produccion debido a que no carga correctamente el grid
    */    
    public function getEncontradosAction(){
        ini_set('max_execution_time', 3000000);
        $objRespuesta       = new Response();
        $objSession         = $this->get('session');
        $objPeticion        = $this->get('request');        
        $strNombreElemento  = $objPeticion->query->get('nombreElemento');
        $intCanton          = $objPeticion->query->get('canton');
        $strEstado          = $objPeticion->query->get('estado');
        $intEmpresa         = $objSession->get('idEmpresa');
        $strModeloElemento  = $objPeticion->get('modeloElemento');        
        $intStart           = $objPeticion->query->get('start');
        $intLimit           = $objPeticion->query->get('limit');
        
        $arrayParametros = array();
        $arrayParametros['start']           = $intStart;
        $arrayParametros['limit']           = $intLimit;
        $arrayParametros["idModeloElemento"]= $strModeloElemento;
        $arrayParametros["codEmpresa"]      = $intEmpresa;
        $arrayParametros["nombreNodo"]      = $strNombreElemento;
        $arrayParametros["idCanton"]        = $intCanton;
        $arrayParametros["estado"]          = $strEstado;
        
        $respuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoElemento')
                                                    ->getJsonRegistrosSwitchPerimetral($arrayParametros);

        $objRespuesta->setContent($respuestaSolicitudes);

        return $objRespuesta;
    }
    
   /**
    * cargarDatosAction
    * funcion que retorna un json con los datos del elemento
    *
    * @return json con datos del elemento
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
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
    * @Secure(roles="ROLE_358-4518")
    * 
    * deleteAjaxAction
    * función que elimina uno o varios elementos
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
    */     
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $request    = $this->get('request');
        $session    = $request->getSession();
        $peticion   = $this->get('request');
        $parametro  = $peticion->get('param');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em->getConnection()->beginTransaction();

        $array_valor = explode("|", $parametro);

        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:InfoElemento', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array("elementoIdB" => $entity->getId(),
                                                                                                                 "estado" => "Activo"));
                    if($relacionElemento)
                    {
                        //consulto el elemento que lo contiene
                        $elementoContenedorId = $relacionElemento->getElementoIdA();
                        $elementoContenedor = $em->find('schemaBundle:InfoElemento', $elementoContenedorId);

                        //relacion elemento
                        $relacionElemento->setEstado("Eliminado");
                        $relacionElemento->setUsrCreacion($session->get('user'));
                        $relacionElemento->setFeCreacion(new \DateTime('now'));
                        $relacionElemento->setIpCreacion($peticion->getClientIp());
                        $em->persist($relacionElemento);

                        //actualizo el elemento que lo contiene a estado pendiente
                        $elementoContenedor->setEstado('Pendiente');
                        $elementoContenedor->setUsrCreacion($session->get('user'));
                        $elementoContenedor->setFeCreacion(new \DateTime('now'));
                        $elementoContenedor->setIpCreacion($peticion->getClientIp());
                        $em->persist($elementoContenedor);
                    }

                    //elemento
                    $entity->setEstado("Eliminado");
                    $entity->setUsrCreacion($session->get('user'));
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setIpCreacion($peticion->getClientIp());
                    $em->persist($entity);

                    $objDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findBy(array("elementoId" => $id));

                    foreach($objDetalleElemento as $registro)
                    {
                        $registro->setEstado('Eliminado');
                        $em->persist($registro);
                        $em->flush();
                    }
                    
                    //elimino las interfaces del elemento
                    $objInterface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $id));
                    
                    foreach($objInterface as $registro)
                    {
                        //verifico si tiene enlace el puerto y lo elimino
                        $objEnlace = $em->getRepository('schemaBundle:InfoEnlace')->findOneBy(array("interfaceElementoFinId" => $registro->getId(),
                                                                                                 "estado"                 => "Activo"));
                        
                        if($objEnlace)
                        {
                            $objEnlace->setEstado('Eliminado');
                            $em->persist($objEnlace);
                            $em->flush();                            
                        }
                        
                        $registro->setEstado('Eliminado');
                        $em->persist($registro);
                        $em->flush();
                    }
                    
                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($entity);
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setObservacion("Se elimino el elemento en la administración");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    //empresa elemento
                    $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array("elementoId" => $entity));
                    $empresaElemento->setEstado("Eliminado");
                    $empresaElemento->setObservacion("Se elimino la switch ");
                    $empresaElemento->setUsrCreacion($session->get('user'));
                    $empresaElemento->setFeCreacion(new \DateTime('now'));
                    $empresaElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($empresaElemento);

                    $em->flush();
                    $respuesta->setContent("OK");
                    $mensaje = 'Se eliminó correctamente ' . $entity->getNombreElemento() . $mensaje;
                }
            endforeach;

            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * getModelosPorTipoElementoAction
     * funcion que obtiene los  modelos por el tipo de elemento
     *
     * @return json
     * 
     * @author John Vera <javera@telconet.ec>
    * @version 1.0 27-07-2016
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
     * getElementoPorTipoAction
     * obtiene elementos por tipo
     *
     * @return json
     * 
     * @author John Vera <javera@telconet.ec>
    * @version 1.0 02-09-2016
     */
    public function getElementoPorTipoAction()
    {
        $respuesta              = new Response();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion               = $this->get('request');
        $strTipoElemento           = $peticion->get('tipoElemento');
        $strEstado                 = $peticion->get('estado');
        $strNombreElemento         = $peticion->get('query');

       
        $result    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                       ->getJsonElementosPorTipoEstado( $strTipoElemento, $strEstado, $strNombreElemento );

        $respuesta->setContent($result);

        return $respuesta;
    }    
   
}