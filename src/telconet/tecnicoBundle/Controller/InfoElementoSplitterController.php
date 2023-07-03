<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Form\InfoElementoSplitterType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class InfoElementoSplitterController extends Controller implements TokenAuthenticatedController
{ 
    public function indexSplitterAction(){
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $rolesPermitidos = array();

        //MODULO 233 - SPLITTER
        
        if (true === $this->get('security.context')->isGranted('ROLE_233-4'))
        {
                $rolesPermitidos[] = 'ROLE_233-4'; //editar elemento splitter
        }
        if (true === $this->get('security.context')->isGranted('ROLE_233-8'))
        {
                $rolesPermitidos[] = 'ROLE_233-8'; //eliminar elemento splitter
        }
        if (true === $this->get('security.context')->isGranted('ROLE_233-6'))
        {
                $rolesPermitidos[] = 'ROLE_233-6'; //ver elemento splitter
        }
        if (true === $this->get('security.context')->isGranted('ROLE_233-828'))
        {
                $rolesPermitidos[] = 'ROLE_233-828'; //administrar puertos elemento splitter
        }
        if (true === $this->get('security.context')->isGranted('ROLE_272-2097'))
        {
                $rolesPermitidos[] = 'ROLE_272-2097'; //clonar splitter
        }
        
        return $this->render('tecnicoBundle:InfoElementoSplitter:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    public function newSplitterAction(){
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoSplitterType(), $entity);

        return $this->render('tecnicoBundle:InfoElementoSplitter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    
    /*     
     * Documentación para el método 'createSplitterAction'
     * 
     * Método que gurda la información de un elemento de tipo splitter
     * 
     * @version 1.0 Version Inicial 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-05-2016 - Se modifica el método para que guarde los detalles del elemento en estado 'Eliminado'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     */
    public function createSplitterAction()
    {
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $elementoSplitter   = new InfoElemento();
        $form               = $this->createForm(new InfoElementoSplitterType(), $elementoSplitter);
        
        $em->beginTransaction();
        try
        {
            $parametros             = $request->request->get('telconet_schemabundle_infoelementosplittertype');
            $objTipoMedio           = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                         ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));

            $nombreElemento = $parametros['nombreElemento'];
            $modeloElementoId = $parametros['modeloElementoId'];
            $elementoContenedorId = $parametros['elementoContenedorId'];
            $descripcionElemento = $parametros['descripcionElemento'];
            $nivel = $parametros['nivel'];

            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array( "nombreElemento" =>$nombreElemento, "estado"=>"Activo"));
            if($elementoRepetido){
                $this->get('session')->getFlashBag()->add('notice', 'Nombre ya existe en otro Elemento, favor revisar!');
                return $this->redirect($this->generateUrl('elementosplitter_newSplitter'));
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            $elementoSplitter->setNombreElemento($nombreElemento);
            $elementoSplitter->setDescripcionElemento($descripcionElemento);
            $elementoSplitter->setModeloElementoId($modeloElemento);
            $elementoSplitter->setUsrResponsable($session->get('user'));
            $elementoSplitter->setUsrCreacion($session->get('user'));
            $elementoSplitter->setFeCreacion(new \DateTime('now'));
            $elementoSplitter->setIpCreacion($peticion->getClientIp());       

            $form->handleRequest($request);
            $em->persist($elementoSplitter);
            $em->flush();

            $elementoSplitter->setEstado("Activo");
            $em->persist($elementoSplitter);
            $em->flush();

            //buscar el interface Modelo
            $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array( "modeloElementoId" =>$modeloElementoId));
            foreach($interfaceModelo as $im){
                $cantidadInterfaces = $im->getCantidadInterface();
                $formato = $im->getFormatoInterface();

                for($i=1;$i<=$cantidadInterfaces;$i++){
                    $interfaceElemento = new InfoInterfaceElemento();

                    $format = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0].$i;

                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setElementoId($elementoSplitter);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($peticion->getClientIp());

                    $em->persist($interfaceElemento);
                    $em->flush();

                }
            }

            //se crean relaciones entre interfaces del elemento para mayor escabilidad
            $objInterfaceIn = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                 ->findOneBy(array("elementoId"=>$elementoSplitter->getId(),
                                                   "nombreInterfaceElemento"=>'IN 1'));
            $objInterfacesElementos = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                        ->findBy(array("elementoId"=>$elementoSplitter->getId()));
            foreach($objInterfacesElementos as $objInterfaceElemento)
            {
                $pos = strpos($objInterfaceElemento->getNombreInterfaceElemento(), 'IN ');
                if ($pos === false)
                {
                    $enlace  = new InfoEnlace();
                    $enlace->setInterfaceElementoIniId($objInterfaceIn);
                    $enlace->setInterfaceElementoFinId($objInterfaceElemento);
                    $enlace->setTipoMedioId($objTipoMedio);
                    $enlace->setTipoEnlace("PRINCIPAL");

                    $enlace->setCapacidadInput(1);
                    $enlace->setCapacidadOutput(1);
                    $enlace->setUnidadMedidaInput("mbps");
                    $enlace->setUnidadMedidaOutput("mbps");

                    $enlace->setCapacidadIniFin(1);
                    $enlace->setCapacidadFinIni(1);
                    $enlace->setUnidadMedidaUp("mbps");
                    $enlace->setUnidadMedidaDown("mbps");
                    $enlace->setEstado("Activo");
                    $enlace->setUsrCreacion($session->get('user'));
                    $enlace->setFeCreacion(new \DateTime('now'));
                    $enlace->setIpCreacion($peticion->getClientIp());
                    $em->persist($enlace);
                    $em->flush();
                }
            }

            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            $relacionElemento->setElementoIdA($elementoContenedorId);
            $relacionElemento->setElementoIdB($elementoSplitter->getId());
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("pop contiene dslam");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($relacionElemento);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elementoSplitter);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un Splitter");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);

            //tomar datos caja
            $contendorEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array("elementoId"=>$elementoContenedorId));
            $contenedorUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($contendorEmpresaElementoUbicacion->getUbicacionId()->getId());

            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $contenedorUbicacion->getParroquiaId());

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                "latitudElemento"       => 
                                                                                                $contenedorUbicacion->getLatitudUbicacion(),
                                                                                                "longitudElemento"      => 
                                                                                                $contenedorUbicacion->getLongitudUbicacion(),
                                                                                                "msjTipoElemento"       => "de Nodo/Caja",
                                                                                                "msjTipoElementoPadre"  =>
                                                                                                "que contiene al splitter ",
                                                                                                "msjAdicional"          => 
                                                                                                "por favor regularizar en la administración"
                                                                                                ." de Nodos/Cajas"
                                                                                             ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($contenedorUbicacion->getLatitudUbicacion());
            $ubicacionElemento->setLongitudUbicacion($contenedorUbicacion->getLongitudUbicacion());
            $ubicacionElemento->setDireccionUbicacion($contenedorUbicacion->getDireccionUbicacion());
            $ubicacionElemento->setAlturaSnm($contenedorUbicacion->getAlturaSnm());
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elementoSplitter);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elementoSplitter);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($peticion->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);

            //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
            $detalle1 = new InfoDetalleElemento();
            $detalle1->setElementoId($elementoSplitter->getId());
            $detalle1->setDetalleNombre("NIVEL");
            $detalle1->setDetalleValor($nivel);
            $detalle1->setDetalleDescripcion("Caracteristica para indicar el nivel");
            $detalle1->setFeCreacion(new \DateTime('now'));
            $detalle1->setUsrCreacion($session->get('user'));
            $detalle1->setIpCreacion($peticion->getClientIp());
            $detalle1->setEstado('Activo');
            $em->persist($detalle1);
            $em->flush();

            $em->flush();
            $em->commit();
            return $this->redirect($this->generateUrl('elementosplitter_showSplitter', array('id' => $elementoSplitter->getId())));
        } 
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->render('tecnicoBundle:InfoElementoSplitter:new.html.twig', array(
                                                                                            'entity' => $elementoSplitter,
                                                                                            'form'   => $form->createView()));
        }
    }
    
    public function editSplitterAction($id){
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) {
            throw new NotFoundHttpException('No existe el elemento -splitter- que se quiere modificar');
        }
        else{
            
            $contenedorElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdA" =>$elemento->getId()));
            $elementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array( "elementoId" =>$elemento->getId()));
            $ubicacion = $em->getRepository('schemaBundle:InfoUbicacion')->findOneBy(array( "id" =>$elementoUbica->getUbicacionId()));
            $parroquia = $em->getRepository('schemaBundle:AdmiParroquia')->findOneBy(array( "id" =>$ubicacion->getParroquiaId()));
            $canton = $em->getRepository('schemaBundle:AdmiCanton')->findOneBy(array( "id" =>$parroquia->getCantonId()));
            $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findOneBy(array( "cantonId" =>$canton->getId()));
        }

        $formulario =$this->createForm(new InfoElementoSplitterType(), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoSplitter:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'splitter'                 => $elemento,
                                'ubicacion'             => $ubicacion,
                                'cantonJurisdiccion'    => $cantonJurisdiccion,
                                'contenedorElemento'           => $contenedorElemento)
                            );
    }
    
    /**
     * Documentación para el método 'updateSplitterAction'.
     * Funcion que permite actualizar la información y puertos de Splitters
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 21-10-2015 - Se agrega creación de enlaces 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 14-05-2016 - Se modifica el método para que actualice los detalles del elemento en estado 'Eliminado'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 19-08-2020 - Si se realiza el cambio de splitter a una nueva caja se deberá actualizar la información en la INFO_SERVICIO_TECNICO
     *      
     * @version 1.1
     * @return view
     */
    public function updateSplitterAction($id)
    {
        $em       = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC      = $this->getDoctrine()->getManager('telconet');
        $request  = $this->get('request');
        $peticion = $this->get('request');
        $session  = $request->getSession();
        $entity   = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $parametros             = $request->request->get('telconet_schemabundle_infoelementosplittertype');
        $nombreElemento         = $parametros['nombreElemento'];
        $contenidoEn            = $parametros['contenidoEn'];
        $elementoContenedorId   = $parametros['elementoContenedorId'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $idUbicacion            = $request->request->get('idUbicacion');
        $nivel                  = $parametros['nivel'];
        $modeloElemento         = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
        //revisar si es cambio de modelo
        $modeloAnterior         = $entity->getModeloElementoId();
        $flag                   = 0;
        $observacion            = "Se modifico el elemento ";
        $boolMensajeUsuario     = false;
        $em->getConnection()->beginTransaction();
        
        try
        {
            if($modeloAnterior->getId()!=$modeloElemento->getId())
            {
                $observacion             = $observacion . "<br>Se modifico el modelo, anterior: ".$modeloAnterior->getNombreModeloElemento().","
                                           ."nuevo: ".$modeloElemento->getNombreModeloElemento();
                $interfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                           ->findOneBy(array( "modeloElementoId" => $modeloAnterior->getId()));
                $interfaceModeloNuevo    = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                           ->findOneBy(array( "modeloElementoId" => $modeloElemento->getId()));

                $objTipoMedio            = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                              ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));

                $cantAnterior    = $interfaceModeloAnterior->getCantidadInterface();
                $cantNueva       = $interfaceModeloNuevo->getCantidadInterface();
                $formatoAnterior = $interfaceModeloAnterior->getFormatoInterface();
                $formatoNuevo    = $interfaceModeloNuevo->getFormatoInterface();

                if($cantAnterior == $cantNueva)
                {
                    //solo cambiar formato
                    for($i=1;$i<=$cantAnterior;$i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array( "elementoId"              => $entity->getId(), 
                                                                "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++)
                        {
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado()!="deleted")
                            {
                                $formatN                      = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }
                }
                else if($cantAnterior > $cantNueva)
                {
                    //revisar puertos restantes si tienen servicio
                    for($i=($cantNueva+1);$i<=$cantAnterior;$i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array( "elementoId"              => $entity->getId(), 
                                                                "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++)
                        {
                            $interface = $interfaceElemento[$j];

                            //buscar servicios tecnicos por puerto splitter
                            $arrayServicioTecnico = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findBy(array( "interfaceElementoConectorId" => $interface->getId()));
                            for($k=0;$k<count($arrayServicioTecnico);$k++)
                            {
                                $servicioTecnico = $arrayServicioTecnico[$k];
                                if($servicioTecnico!=null || $servicioTecnico!="")
                                {
                                    $servicio = $servicioTecnico->getServicioId();

                                    //verificar que el servicio no este en estado activo, cortado o en proceso de activacion
                                    if($servicio->getEstado() == "Cancel"     || $servicio->getEstado() == "Trasladado" ||
                                        $servicio->getEstado() == "Reubicado" || $servicio->getEstado() == "Eliminado"  ||
                                        $servicio->getEstado() == "Anulado"   || $servicio->getEstado() == "Rechazado"  ||
                                        $servicio->getEstado() == "Rechazada")
                                    {
                                        $flag = 0;
                                    }
                                    else
                                    {
                                        $flag = 1;
                                        break;
                                    }
                                }
                            }
                            if( $flag ==1 )
                            {
                                break;
                            }
                        }

                        if( $flag == 1 )
                        {
                            break;
                        }

                    }

                    if($flag==0){
                        //actualizar las interfaces
                        for($i=1;$i<=$cantNueva;$i++)
                        {
                            $format                          = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0].$i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array( "elementoId"              => $entity->getId(), 
                                                                    "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                            for($j=0;$j<count($interfaceElemento);$j++)
                            {
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado()!="deleted")
                                {
                                    $formatN                      = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                    $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                    $interface->setUsrCreacion($session->get('user'));
                                    $interface->setFeCreacion(new \DateTime('now'));
                                    $interface->setIpCreacion($peticion->getClientIp());
                                    $em->persist($interface);
                                }
                            }
                        }//fin de for

                        //cambiar estado a eliminado
                        for($i=($cantNueva+1);$i<=$cantAnterior;$i++)
                        {
                            $format                          = explode("?", $formatoAnterior);
                            $nombreInterfaceElementoAnterior = $format[0].$i;

                            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array( "elementoId"              => $entity->getId(), 
                                                                    "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                            for($j=0;$j<count($interfaceElemento);$j++)
                            {
                                $interface = $interfaceElemento[$j];

                                if($interface->getEstado()!="deleted")
                                {
                                    $formatN = explode("?", $formatoNuevo);
                                    $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                    $interface->setEstado("deleted");
                                    $interface->setUsrCreacion($session->get('user'));
                                    $interface->setFeCreacion(new \DateTime('now'));
                                    $interface->setIpCreacion($peticion->getClientIp());
                                    $em->persist($interface);
                                }
                            }
                        }
                    }
                }
                else if($cantAnterior < $cantNueva)
                {
                    $objInterfaceIn = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                         ->findOneBy(array("elementoId"              => $entity->getId(),
                                                           "nombreInterfaceElemento" => 'IN 1'));
                    //actualizar las interfaces
                    for($i=1;$i<=$cantAnterior;$i++)
                    {
                        $format                          = explode("?", $formatoAnterior);
                        $nombreInterfaceElementoAnterior = $format[0].$i;

                        $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findBy(array( "elementoId"              => $entity->getId(), 
                                                                "nombreInterfaceElemento" => $nombreInterfaceElementoAnterior));

                        for($j=0;$j<count($interfaceElemento);$j++)
                        {
                            $interface = $interfaceElemento[$j];

                            if($interface->getEstado()!="deleted")
                            {
                                $formatN = explode("?", $formatoNuevo);
                                $nombreInterfaceElementoNuevo = $formatN[0].$i;

                                $interface->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                                $interface->setUsrCreacion($session->get('user'));
                                $interface->setFeCreacion(new \DateTime('now'));
                                $interface->setIpCreacion($peticion->getClientIp());
                                $em->persist($interface);
                            }
                        }
                    }

                    //crear las nuevas interfaces
                    for($i=($cantAnterior+1);$i<=$cantNueva;$i++)
                    {
                        $interfaceElemento            = new InfoInterfaceElemento();
                        $formatN                      = explode("?", $formatoNuevo);
                        $nombreInterfaceElementoNuevo = $formatN[0].$i;

                        $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElementoNuevo);
                        $interfaceElemento->setElementoId($entity);
                        $interfaceElemento->setEstado("not connect");
                        $interfaceElemento->setUsrCreacion($session->get('user'));
                        $interfaceElemento->setFeCreacion(new \DateTime('now'));
                        $interfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($interfaceElemento);
                        $em->flush();
                        //se crean relaciones entre interfaces del elemento para mayor escabilidad
                        $enlace  = new InfoEnlace();
                        $enlace->setInterfaceElementoIniId($objInterfaceIn);
                        $enlace->setInterfaceElementoFinId($interfaceElemento);
                        $enlace->setTipoMedioId($objTipoMedio);
                        $enlace->setTipoEnlace("PRINCIPAL");

                        $enlace->setCapacidadInput(1);
                        $enlace->setCapacidadOutput(1);
                        $enlace->setUnidadMedidaInput("mbps");
                        $enlace->setUnidadMedidaOutput("mbps");

                        $enlace->setCapacidadIniFin(1);
                        $enlace->setCapacidadFinIni(1);
                        $enlace->setUnidadMedidaUp("mbps");
                        $enlace->setUnidadMedidaDown("mbps");
                        $enlace->setEstado("Activo");
                        $enlace->setUsrCreacion($session->get('user'));
                        $enlace->setFeCreacion(new \DateTime('now'));
                        $enlace->setIpCreacion($peticion->getClientIp());
                        $em->persist($enlace);
                        $em->flush();
                    }
                }
            }
        
        
            if( $flag == 0 )
            {
                if($entity->getNombreElemento()!=$nombreElemento)
                {
                    $observacion = $observacion . "<br> se modifico el nombre, "
                                   . "anterior: ".$entity->getNombreElemento().","
                                   . "nuevo: ".$nombreElemento;
                }

                //elemento
                $entity->setNombreElemento($nombreElemento);
                $entity->setDescripcionElemento($descripcionElemento);
                $entity->setModeloElementoId($modeloElemento);
                $entity->setUsrResponsable($session->get('user'));
                $entity->setUsrCreacion($session->get('user'));
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setIpCreacion($peticion->getClientIp());   
                $em->persist($entity);

                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                       ->findOneBy(array( "elementoIdB" => $entity->getId()));
                //ver si se cambio de contenedor
                $contenedorElementoAnteriorId = $relacionElemento->getElementoIdA();

                if($contenedorElementoAnteriorId != $elementoContenedorId)
                {
                    $objContenedorElementoAnterior = $em->getRepository('schemaBundle:InfoElemento')
                                                        ->find($contenedorElementoAnteriorId);
                    $objContenedorElemento         = $em->getRepository('schemaBundle:InfoElemento')
                                                        ->find($elementoContenedorId);
                    $observacion                   = $observacion . "<br> Se modifico contenedor, "
                                                     . "anterior: ".$objContenedorElementoAnterior->getNombreElemento().","
                                                     . "nuevo: ".$objContenedorElemento->getNombreElemento()."";
                    //cambiar la relacion elemento
                    $relacionElemento->setElementoIdA($elementoContenedorId);
                    $relacionElemento->setTipoRelacion("CONTIENE");
                    $relacionElemento->setObservacion($contenidoEn." contiene splitter");
                    $relacionElemento->setEstado("Activo");
                    $relacionElemento->setUsrCreacion($session->get('user'));
                    $relacionElemento->setFeCreacion(new \DateTime('now'));
                    $relacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($relacionElemento);

                    //tomar datos contenedor
                    $contenedorEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                             ->findOneBy(array("elementoId" => $elementoContenedorId));
                    $contenedorUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                             ->find($contenedorEmpresaElementoUbicacion->getUbicacionId()->getId());

                    //cambiar ubicacion del splitter
                    $parroquia         = $em->find('schemaBundle:AdmiParroquia', $contenedorUbicacion->getParroquiaId());
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $contenedorUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $contenedorUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "de la caja ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al splitter ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Cajas"
                                                                                                     ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
                    $ubicacionElemento->setLatitudUbicacion($contenedorUbicacion->getLatitudUbicacion());
                    $ubicacionElemento->setLongitudUbicacion($contenedorUbicacion->getLongitudUbicacion());
                    $ubicacionElemento->setDireccionUbicacion($contenedorUbicacion->getDireccionUbicacion());
                    $ubicacionElemento->setAlturaSnm($contenedorUbicacion->getAlturaSnm());
                    $ubicacionElemento->setParroquiaId($parroquia);
                    $ubicacionElemento->setUsrCreacion($session->get('user'));
                    $ubicacionElemento->setFeCreacion(new \DateTime('now'));
                    $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($ubicacionElemento);
                    
                    //Consulta el elemento contenedor anterior para Actualizar la INFO_SERVICIO_TECNICO  con el nuevo elemento_contenedor_id
                    $arrayInfoServicioTecnico = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->findBy(array( "elementoContenedorId" => $contenedorElementoAnteriorId));
                    foreach($arrayInfoServicioTecnico as $objInfoServicioTecnico)
                    {
                        //Consultamos si el servicio esta en estado Activo o In-Corte
                        $objInfoServicio = $emC->getRepository('schemaBundle:InfoServicio')
                                               ->find($objInfoServicioTecnico->getServicioId()->getId());
                        if(is_object($objInfoServicio) && ($objInfoServicio->getEstado() == "Activo" || $objInfoServicio->getEstado() == "In-Corte"))
                        {
                            //Actualiza la tabla Info_Servicio_Tecnico con el nuevo elemento_contenedor_id
                            $objInfoServicioTecnico->setElementoContenedorId($elementoContenedorId);
                            $emC->persist($objInfoServicioTecnico);
                            $emC->flush();
                        }    
                    }
                }

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Modificado");
                $historialElemento->setObservacion($observacion);
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                //detalle de nivel
                $detalle1 = $em->getRepository('schemaBundle:InfoDetalleElemento')
                               ->findOneBy(array( "elementoId" => $id, "detalleNombre" => "NIVEL" ));
                $detalle1->setDetalleValor($nivel);
                $detalle1->setEstado('Activo');
                $em->persist($detalle1);
                $em->flush();
                
                /*
                 * Bloque que busca los detalle del elemento Splitter y les actualiza el estado a 'Activo'
                 */
                $arrayInfoDetallesElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findByElementoId($id);

                foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                {
                    $objInfoDetalleElemento->setEstado('Activo');
                    $em->persist($objInfoDetalleElemento);
                    $em->flush();
                }
                /*
                 * Fin Bloque que busca los detalle del elemento Splitter y les actualiza el estado a 'Activo'
                 */

                $em->flush();
                $em->commit();

                return $this->redirect($this->generateUrl('elementosplitter_showSplitter', array('id' => $entity->getId())));
            }
            else
            {
                $boolMensajeUsuario = true;
                throw new \Exception('El elemento aún tiene servicios en puertos que ya no se van a usar, favor regularice!');
            }
        }
        catch (\Exception $e) 
        {
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas!';
            }
            
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            error_log("Error: ".$e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementosplitter_editSplitter', array('id' => $entity->getId())));
        }
        
        
    }
    
    public function showSplitterAction($id){
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
            
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoSplitter:show.html.twig', array(
            'elemento'              => $elemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion,
            'flag'                  => $peticion->get('flag')
        ));
    }
    
    public function getEncontradosSplitterAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"SPLITTER"));
        
        $peticion = $this->get('request');
        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $modeloElemento = $peticion->query->get('modeloElemento');
        $marcaElemento = $peticion->query->get('marcaElemento');
        $canton = $peticion->query->get('canton');
        $jurisdiccion = $peticion->query->get('jurisdiccion');
        $contenidoEn = $peticion->query->get('contenidoEn');
        $elementoContenedor = $peticion->query->get('elementoContenedor');
        $estado = $peticion->query->get('estado');
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonSplitters(strtoupper($nombreElemento),$modeloElemento,$marcaElemento,$tipoElemento[0]->getId(),$canton,$jurisdiccion,$contenidoEn,$elementoContenedor,$estado,$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'buscarElementoContenedorAction'.
     *
     * Metodo utilizado para obtener información de los elementos contenedores
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 17-12-2015 Se agrega cambio para filtrado de registros mediante peticiones ajax por parametros 
     * @since 1.0
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 26-08-2016 Se adicciona los parametros jurisdiccion y estado, el método generarJsonElementoContenedor
     *                         recibirá por parametro un array
     */
    public function buscarElementoContenedorAction(){
        ini_set('max_execution_time', 400000);
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion    = $this->get('request');
        $objSession     = $this->get('session');
        $tipoElemento   = $objPeticion->get('tipoElemento');        
        $nombreElemento = $objPeticion->query->get('query');
        $jurisdiccion   = '';
        $serviceUtil    = $this->get('schema.Util');
        $strUser        = $objSession->get("user");
        $strIpClient    = $objPeticion->getClientIp();
        $arrayParams    = array();
                
        try
        { 
            $jurisdiccion   = $objPeticion->get('jurisdiccion');
            $estado         = 'Activo';
            if($objPeticion->get('estado'))
            {
                $estado   = $objPeticion->get('estado');
            }

            if (!$nombreElemento)
            {
                $nombreElemento = $objPeticion->get('nombreElemento');
            }      

            $arrayParams['nombreElemento']  = $nombreElemento;
            $arrayParams['tipoElemento']    = $tipoElemento;
            $arrayParams['idEmpresa']       = $objSession->get('idEmpresa');
            $arrayParams['idOficina']       = $objSession->get('oficina');            
            $arrayParams['estado']          = $estado;
            $arrayParams['start']           = $objPeticion->get('start');
            $arrayParams['limit']           = $objPeticion->get('limit');
            $arrayParams['jurisdiccion']    = $jurisdiccion;

            $objJson        = $this->getDoctrine()
                                   ->getManager("telconet_infraestructura")
                                   ->getRepository('schemaBundle:InfoElemento')
                                   ->generarJsonElementoContenedor($arrayParams);

            $arrayRespuesta = json_decode($objJson);
            if($arrayRespuesta->status != 'OK')
            {
                throw new \Exception($arrayRespuesta->mensaje);
            }

            $objRespuesta->setContent($objJson);
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'buscarElementoContenedorAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("Se presentaron errores al ejecutar la acción.");
            
        }
        return $objRespuesta;
    }
    
    public function cargarDatosSplitterAction(){
       $respuesta = new Response();
       $em = $this->getDoctrine()->getManager('telconet_infraestructura');
       
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idSplitter = $peticion->get('idSplitter');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosSplitter($idSplitter,$em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    
    /**
     * Documentación para el método 'deleteAjaxSplitterAction'.
     * 
     * Funcion que permite eliminar la información y puertos de Splitters
     * 
     * @version 1.0 Version Inicial 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-05-2016 - Se modifica el método para que actualice los detalles del elemento en estado 'Eliminado'
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 05-06-2017 - Se agregan validaciones para cambiar estado interface en estado not connect de splitter a eliminar
     * @since 1.1
     */
    public function deleteAjaxSplitterAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emC = $this->getDoctrine()->getManager("telconet");
        $em->getConnection()->beginTransaction();
        
        $array_valor = explode("|",$parametro);
        
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                $nivelObj = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId" =>$entity->getId(), "detalleNombre"=>"NIVEL"));
                $nivel = $nivelObj->getDetalleValor();
                $interfaces = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array( "elementoId" =>$entity->getId()));
                
                $flag=0;
                if($nivel=="1"){
                    for($i=0;$i<count($interfaces);$i++){
                        if($interfaces[$i]->getNombreInterfaceElemento()!="IN 1"){
                            $enlaceOut = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findBy(array( "interfaceElementoIniId" =>$interfaces[$i]->getId(),
                                                            "estado" => 'Activo'
                                                          ));
                            for($j=0;$j<count($enlaceOut);$j++)
                            {
                                    $flag=1;
                                    break;
                            }
                            if($flag==1){
                                break;
                            }  
                        }
                    }
                }
                else if($nivel=="2"){
                    for($i=0;$i<count($interfaces);$i++){
                        if($interfaces[$i]->getNombreInterfaceElemento()!="IN 1"){
                            $serviciosTecnicos = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array( "interfaceElementoId" =>$interfaces[$i]->getId()));
                            for($j=0;$j<count($serviciosTecnicos);$j++){
                                $servicio = $emC->find('schemaBundle:InfoServicio', $serviciosTecnicos[$j]->getServicioId());
                                if($servicio->getEstado()=="Activo" || $servicio->getEstado()=="In-Corte"){
                                    $flag=2;
                                    break;
                                }
                            }
                            if($flag==2){
                                break;
                            }    
                        }
                    }
                }
                
                if($flag==0){
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
                    $historialElemento->setObservacion("Se elimino el Splitter");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    //relacion elemento
                    $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$entity->getId()));
                    for($i=0;$i<count($relacionElemento);$i++){
                        $relacion = $relacionElemento[$i];
                        $relacion->setEstado("Eliminado");
                        $relacion->setUsrCreacion($session->get('user'));
                        $relacion->setFeCreacion(new \DateTime('now'));
                        $relacion->setIpCreacion($peticion->getClientIp());
                        $em->persist($relacion);
                    }
                    
                    //enlaces
                    for($i=0;$i<count($interfaces);$i++){
                        $enlaceIn = $em->getRepository('schemaBundle:InfoEnlace')->findBy(array( "interfaceElementoFinId" =>$interfaces[$i]->getId()));
                        for($j=0;$j<count($enlaceIn);$j++){
                            $enlace = $enlaceIn[$j];
                            $enlace->setEstado("Eliminado");
                            $enlace->setUsrCreacion($session->get('user'));
                            $enlace->setFeCreacion(new \DateTime('now'));
                            $enlace->setIpCreacion($peticion->getClientIp());
                            $em->persist($enlace);
                        }
                        
                        $enlaceOut = $em->getRepository('schemaBundle:InfoEnlace')->findBy(array( "interfaceElementoIniId" =>$interfaces[$i]->getId()));
                        for($j=0;$j<count($enlaceOut);$j++){
                            $enlace = $enlaceOut[$j];
                            $enlace->setEstado("Eliminado");
                            $enlace->setUsrCreacion($session->get('user'));
                            $enlace->setFeCreacion(new \DateTime('now'));
                            $enlace->setIpCreacion($peticion->getClientIp());
                            $em->persist($enlace);
                        }
                        //se proceder a eliminar las interfaces libres del splitter
                        if ($interfaces[$i]->getEstado() == "not connect")
                        {
                            $interfaces[$i]->setEstado("deleted");
                            $em->persist($interfaces[$i]);
                        }
                        
                    }
                    
                    //empresa elemento
                    $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array( "elementoId" =>$entity));
                    $empresaElemento->setEstado("Eliminado");
                    $empresaElemento->setObservacion("Se elimino el splitter");
                    $empresaElemento->setUsrCreacion($session->get('user'));
                    $empresaElemento->setFeCreacion(new \DateTime('now'));
                    $empresaElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($empresaElemento);

                    /*
                     * Bloque que busca los detalle del elemento Splitter y les actualiza el estado a 'Eliminado'
                     */
                    $arrayInfoDetallesElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findByElementoId($id);

                    foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                    {
                        $objInfoDetalleElemento->setEstado('Eliminado');
                        $em->persist($objInfoDetalleElemento);
                        $em->flush();
                    }
                    /*
                     * Fin Bloque que busca los detalle del elemento Splitter y les actualiza el estado a 'Eliminado'
                     */

                    $em->flush();
                }
                else if($flag==1){
                    $respuesta->setContent("ENLACES ACTIVOS");
                    break;
                }
                else if($flag==2){
                    $respuesta->setContent("SERVICIOS ACTIVOS");
                    break;
                }
                $respuesta->setContent("OK");
            }
        endforeach;
        
        $em->getConnection()->commit();
        
        return $respuesta;
    }
    
    /**
     * Funcion que edita los estados de los puertos
     * de un splitter.
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 12-05-2016  Se agrega validacion de puertos IN de Elemento
     *
     * @return String con dos valores: String 'status' indica OK/ERROR, String 'mensaje' 
     * indica el mensaje a presentar en caso de ERROR, separados por un |
     * 
     * @since 1.0
     */
    public function administrarPuertosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $jsonInterfaces = $peticion->get('interfaces');
        $json_interfaces = json_decode($jsonInterfaces);
        $arrayInterfaces = $json_interfaces->interfaces;

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            for($i = 0; $i < count($arrayInterfaces); $i++)
            {
                $idInterface = $arrayInterfaces[$i]->idInterfaceElemento;
                $estadoNuevo = $this->getEstadoPuerto($arrayInterfaces[$i]->estado);

                $interface = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($idInterface);
                $intFlag = 1;
                $intContSinServicio = 0;
                $intContConServicio = 0;
                
                $servicioTecnicoArray = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findBy(array("interfaceElementoConectorId" => $idInterface));

                //si se hizo un cambio de estado en la administracion
                if($interface->getEstado() != $estadoNuevo)
                {
                    $boolEsOut = strpos($interface->getNombreInterfaceElemento(), "OUT");
                    //validaciones para interface OUT
                    if ( $boolEsOut !== false )
                    {
                        $serviciosPorInterface = $emComercial->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->getServiciosPorInterface($idInterface, $codEmpresa);
                        $noServicios =count($serviciosPorInterface);
                        //si pasa a libre, dañado, inactivo, se verifica que ya no existan servicios activos
                        if($estadoNuevo == "not connect" || $estadoNuevo == "err-disabled" || $estadoNuevo == "disabled")
                        {
                            if($noServicios > 0)
                            {
                                //bandera que indica que aun existen servicios activos
                                $intFlag = 0;
                            }
                            else
                            {
                                //bandera que indica que todos los servicios estan cancelados
                                $intFlag = 1;
                            }
                        }//cierre if - not connect
                        //si el estado es libre y va a pasar a conectado, dañado o inactivo
                        else if($interface->getEstado() == "not connect" && 
                            ($estadoNuevo == "connected" || $estadoNuevo == "err-disabled" || $estadoNuevo == "disabled"))
                        {
                            if($noServicios == 1)
                            {
                                //bandera que indica que solo tiene 1 servicio activo
                                $intFlag = 1;
                            }
                            elseif ($noServicios > 1)
                            {
                                //bandera que indica que aun existen servicios activos
                                $intFlag = 4;
                            }else
                            {
                                //bandera que indica que todos los servicios estan cancelados
                                $intFlag = 3;
                            }

                        }
                        //si el estado es factible y pasa a conectado
                        else if(($interface->getEstado() == "Factible" && $estadoNuevo == "connected") ||
                            ($interface->getEstado() == "reserved" && $estadoNuevo == "connected"))
                        {
                            //bandera que significa accion no permitida y requiere uso de otras herramientas en el sistema
                            $intFlag = 2;
                        }
                        //si el estado es conectado y pasa a factible
                        else if(($interface->getEstado() == "connected" && $estadoNuevo == "Factible") ||
                            ($interface->getEstado() == "connected" && $estadoNuevo == "reserved"))
                        {
                            //bandera que significa accion no permitida y requiere uso de otras herramientas en el sistema
                            $intFlag = 2;
                        }
                        //demas cambios de estados.
                        else
                        {
                            //bandera que significa accion no permitida
                            $intFlag = -1;
                        }

                        //bandera que indica que aun existen servicios activos
                        if($intFlag == 0)
                        {
                            return $respuesta->setContent("ERROR|ERROR: No se puede cambiar el estado del "
                                    . "puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "aun existen Servicios Activos, Cortados o "
                                    . "en Proceso de Activacion, <br>"
                                    . "Favor Revisar!");
                        }
                        //bandera que indica que todos los servicios estan cancelados
                        else if($intFlag == 1)
                        {
                            //setear observacion para el historial del elemento
                            $strObservacionHistorial = "Se edito puerto: " . $interface->getNombreInterfaceElemento() . " <br>"
                                . "estado anterior:" . $interface->getEstado() . " <br>"
                                . "nuevo estado:" . $estadoNuevo;

                            //realizar cambio de estado
                            $interface->setEstado($estadoNuevo);
                            $emInfraestructura->persist($interface);
                            $emInfraestructura->flush();

                            $objElemento = $interface->getElementoId();

                            //historial del elemento
                            $objHistorialElemento = new InfoHistorialElemento();
                            $objHistorialElemento->setElementoId($objElemento);
                            $objHistorialElemento->setEstadoElemento($objElemento->getEstado());
                            $objHistorialElemento->setObservacion($strObservacionHistorial);
                            $objHistorialElemento->setUsrCreacion($session->get('user'));
                            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                            $emInfraestructura->persist($objHistorialElemento);
                            $emInfraestructura->flush();
                        }
                        //bandera que significa accion no permitida y requiere uso de otras herramientas en el sistema
                        else if($intFlag == 2)
                        {
                            return $respuesta->setContent("ERROR|NO PERMITIDO: No se debe realizar este cambio de estado, <br>"
                                    . "del puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "Favor Utilizar las herramientas del Sistema!");
                        }
                        //bandera que significa que no existe servicios activos
                        else if($intFlag == 3)
                        {
                            return $respuesta->setContent("ERROR|ERROR: No se puede cambiar el estado del "
                                    . "puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "no existen Servicios Activos, Cortados o "
                                    . "en Proceso de Activacion, <br>"
                                    . "Favor Revisar!");
                        }
                        //bandera que significa que existe mas de un servicio activo en el puerto
                        else if($intFlag == 4)
                        {
                            return $respuesta->setContent("ERROR|ERROR: No se puede cambiar el estado del "
                                    . "puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "existen mas de 1 Servicio en estado Activo, Cortados o "
                                    . "en Proceso de Activacion, <br>"
                                    . "Favor Revisar!");
                        }
                        //bandera que significa accion no permitida
                        else
                        {
                            return $respuesta->setContent("ERROR|NO PERMITIDO: No puede realizar el cambio de estado del "
                                    . "puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "Favor Revisar!");
                        }
                    }
                    else//validaciones para interface IN
                    {
                        if( ($interface->getEstado() == "not connect" && ($estadoNuevo == "connected" || $estadoNuevo == "err-disabled")) || 
                            ($interface->getEstado() == "connected" && $estadoNuevo == "err-disabled"))
                        {
                            //bandera que permite actualización
                            $intFlag = 1;
                        }
                        else
                        {
                            $intFlag = 0;
                        }
                        
                        if($intFlag == 1)
                        {
                            //setear observacion para el historial del elemento
                            $strObservacionHistorial = "Se edito puerto: " . $interface->getNombreInterfaceElemento() . " <br>"
                                . "estado anterior:" . $interface->getEstado() . " <br>"
                                . "nuevo estado:" . $estadoNuevo;

                            //realizar cambio de estado
                            $interface->setEstado($estadoNuevo);
                            $emInfraestructura->persist($interface);
                            $emInfraestructura->flush();

                            $objElemento = $interface->getElementoId();

                            //historial del elemento
                            $objHistorialElemento = new InfoHistorialElemento();
                            $objHistorialElemento->setElementoId($objElemento);
                            $objHistorialElemento->setEstadoElemento($objElemento->getEstado());
                            $objHistorialElemento->setObservacion($strObservacionHistorial);
                            $objHistorialElemento->setUsrCreacion($session->get('user'));
                            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                            $emInfraestructura->persist($objHistorialElemento);
                            $emInfraestructura->flush();
                        }
                        else
                        {
                            return $respuesta->setContent("ERROR|ERROR: No se puede cambiar el estado del "
                                    . "puerto:<b>" . $interface->getNombreInterfaceElemento() . "</b>, <br>"
                                    . "cambio no permitido.");
                        }
                        
                    }
                }//end if
            }//cierre for
        }
        catch(Exception $ex)
        {
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }

            $result = "ERROR|ERROR:" . $ex->getMessage();
            return $respuesta->setContent($result);
        }
        //*----------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if($emInfraestructura->getConnection()->isTransactionActive())
        {
            $emInfraestructura->getConnection()->commit();
        }

        $emInfraestructura->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $result = "OK|Se editaron los puertos correctamente!";

        return $respuesta->setContent($result);
    }

    /**
     * Funcion que devuelve el estado correcto
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @param String $estado Estado en lenguaje entendible por el usuario
     * @return String estado correcto
     */
    private function getEstadoPuerto($estado)
    {
        if($estado == "Libre")
        {
            return "not connect";
        }
        else if($estado == "Ocupado")
        {
            return "connected";
        }
        else if($estado == "Dañado")
        {
            return "err-disabled";
        }
        else if($estado == "Inactivo")
        {
            return "disabled";
        }
        else if($estado == "Reservado")
        {
            return "reserved";
        }
        else if($estado == "Factible")
        {
            return "Factible";
        }
    }
    
    /**
     * getSplitterPorNivelAction
     * Obtiene los splitters según el nivel si es L1 o L2
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 19-02-2015
     * 
     * @return string $respuesta
     */

    
    public function getSplitterPorNivelAction()
    {
        $respuesta = new Response();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombreSplitter = $peticion->get('query');
        $nivelSplitter = $peticion->get('nivelSplitter');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->getJsonSplitterPorNivel($nivelSplitter, $nombreSplitter);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
     /**
     * clonarSplitterAction
     * funcion que permite clonar la data de un splitter e inactivar el splitter original
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 19-02-2015
     * 
     * @return string $resultado
     */

    public function clonarSplitterAction()
    {
        $respuesta = new Response();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $idSplitterOriginal = $peticion->get('idElemento');
        $idInterfaceSplitterL1 = $peticion->get('idPuertoSplitter'); 
        $nombreNuevoElemento = $peticion->get('nombreSpliterNuevo');
        $ipIngresa  = $peticion->getClientIp();
        $userCrea   = $session->get('user');
        $idEmpresa  = $session->get('idEmpresa'); 

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->clonarSplitter($idSplitterOriginal, $idInterfaceSplitterL1, $nombreNuevoElemento,$idEmpresa, $userCrea, $ipIngresa);
                
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * getSplitterAnteriorAction
     * Obtiene los splitters que han sido clonados
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 19-02-2015
     * @version 1.1 25-03-2015
     * 
     * @return string $respuesta
     */
    
    public function getSplitterAnteriorAction()
    {
        $respuesta = new Response();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombreSplitter = $peticion->get('query');
        $nivelSplitter = $peticion->get('nivelSplitter');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->getJsonSplitterAnterior($nivelSplitter, $nombreSplitter);
        $respuesta->setContent($objJson);

        return $respuesta;
    } 
    
    /**
     * getSplitterNuevoAction
     * Obtiene los splitters resultantes de una clonacion
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 19-02-2015
     * @version 1.1 25-03-2015
     * 
     * @return string $respuesta
     */
    
    public function getSplitterNuevoAction()
    {
        $respuesta = new Response();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombreSplitter = $peticion->get('query');
        $nivelSplitter = $peticion->get('nivelSplitter');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->getJsonSplitterNuevo($nivelSplitter, $nombreSplitter);
        $respuesta->setContent($objJson);

        return $respuesta;
    }     
    
    /**
     * 
     * Documentación para el método 'getSplitterEnlaceOltAction'.
     * 
     * Metodo verifica si se han creado los enlaces al momento de migrar al OLT con la nueva tecnología.
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0
     * @since 07-03-2019
     * 
     * @return $objRespuesta
     * 
     * 
     */
    public function getSplitterEnlaceOltAction()
    {
        
        $objRespuesta                    = new JsonResponse();
        $objRequest                      = $this->getRequest();
        $emInfraestructura               = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intInterfaceElementoConector    = json_decode($objRequest->get('idInterface'));
        
        $arrayResultado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->getElementoPadre($intInterfaceElementoConector, 'INTERFACE', 'OLT');
            
        if($arrayResultado)
        {
            $arrayRespuesta = array(
                            'status' => 'EXITO' ,
                            'mensaje'    => "Enlaces correctos"
                            );
        }
        else
        {
            $arrayRespuesta = array(
                            'status' => 'ERROR' ,
                            'mensaje'    => "Enlaces incorrectos, por favor notificar a GIS"
                            );
        }        
        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }

}