<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Form\InfoElementoCajaType;

use Symfony\Component\HttpFoundation\Response;

class InfoElementoCajaController extends Controller
{ 
    public function indexCajaAction(){
        
        $rolesPermitidos = array();
        //MODULO 232 - CAJA
        
        if (true === $this->get('security.context')->isGranted('ROLE_232-5'))
        {
                $rolesPermitidos[] = 'ROLE_232-5'; //editar elemento caja
        }
        if (true === $this->get('security.context')->isGranted('ROLE_232-8'))
        {
                $rolesPermitidos[] = 'ROLE_232-8'; //eliminar elemento caja
        }
        if (true === $this->get('security.context')->isGranted('ROLE_232-6'))
        {
                $rolesPermitidos[] = 'ROLE_232-6'; //ver elemento caja
        }
        
        
        return $this->render('tecnicoBundle:InfoElementoCaja:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    public function newCajaAction(){
        $request = $this->get('request');
        $session  = $request->getSession();
        $empresaId=$session->get('idEmpresa');
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoCajaType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoCaja:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /*     
     * Documentación para el método 'createCajaAction'
     * 
     * Método que crea un elemento de tipo caja
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 14-01-2015 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 14-05-2016 - Se modifica el método para que guarde los detalles del elemento en estado 'Activo'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 22-11-2018 - Se actualiza el campo id por el idEdificacion con el objetivo de solucionar un error que se esta presentando en la
     *                           creación de cajas
     */
    public function createCajaAction(){
        $request    = $this->get('request');
        $em         = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $parametros = $request->request->get('telconet_schemabundle_infoelementocajatype');
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $nombreElemento = $parametros['nombreElemento'];
        $modeloElementoId = $parametros['modeloElementoId'];
        $ubicadoEn = $parametros['ubicadoEn'];
        $parroquiaId = $parametros['parroquiaId'];
        $alturaSnm = $parametros['alturaSnm'];
        $longitudUbicacion = $parametros['longitudUbicacion'];
        $latitudUbicacion = $parametros['latitudUbicacion'];
        $direccionUbicacion = $parametros['direccionUbicacion'];
        $descripcionElemento = $parametros['descripcionElemento'];
        $nivel = $parametros['nivel'];
        $idNodoCliente = $parametros['idEdificacion'];
        $elemento   = new InfoElemento();
        $objForm    = $this->createForm(new InfoElementoCajaType(array("empresaId" => $session->get('idEmpresa'))), $elemento);
        
        $em->beginTransaction();
        try
        {
            $objForm->handleRequest($request);
            //verificar que el nombre del elemento no se repita
            $objElementoRepetido = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array("nombreElemento"    => $nombreElemento, 
                                                                                                    "estado"            => "Activo"));
            if($objElementoRepetido)
            {
                throw new \Exception("Nombre ya existe en otro Elemento, favor revisar!");
            }
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "de la caja "
                                                                                                 ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            $elemento->setNombreElemento($nombreElemento);
            $elemento->setDescripcionElemento($descripcionElemento);
            $elemento->setModeloElementoId($modeloElemento);
            $elemento->setUsrResponsable($session->get('user'));
            $elemento->setUsrCreacion($session->get('user'));
            $elemento->setFeCreacion(new \DateTime('now'));
            $elemento->setIpCreacion($peticion->getClientIp());       

            $em->persist($elemento);
            $em->flush();

            $elemento->setEstado("Activo");
            $em->persist($elemento);
            $em->flush();

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso una Caja de Dispersion");
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

            //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
            $detalle = new InfoDetalleElemento();
            $detalle->setElementoId($elemento->getId());
            $detalle->setDetalleNombre("UBICADO EN");
            $detalle->setDetalleValor($ubicadoEn);
            $detalle->setDetalleDescripcion("Caracteristicas para indicar donde se ubica el Elemento");
            $detalle->setFeCreacion(new \DateTime('now'));
            $detalle->setUsrCreacion($session->get('user'));
            $detalle->setIpCreacion($peticion->getClientIp());
            $detalle->setEstado('Activo');
            $em->persist($detalle);
            $em->flush();

            //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
            $detalle1 = new InfoDetalleElemento();
            $detalle1->setElementoId($elemento->getId());
            $detalle1->setDetalleNombre("NIVEL");
            $detalle1->setDetalleValor($nivel);
            $detalle1->setDetalleDescripcion("Caracteristica para indicar el nivel");
            $detalle1->setFeCreacion(new \DateTime('now'));
            $detalle1->setUsrCreacion($session->get('user'));
            $detalle1->setIpCreacion($peticion->getClientIp());
            $detalle1->setEstado('Activo');
            $em->persist($detalle1);
            $em->flush();

            if ($idNodoCliente)
            {   
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($idNodoCliente);
                $objRelacionElemento->setElementoIdB($elemento->getId());
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Nodo Cliente contiene Caja");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($session->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objRelacionElemento);
                $em->flush();
            }
            $em->commit();

            return $this->redirect($this->generateUrl('elementocaja_showCaja', array('id' => $elemento->getId())));
        } 
        catch (\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->render('tecnicoBundle:InfoElementoCaja:new.html.twig', array(
                                                                                        'entity' => $elemento,
                                                                                        'form'   => $objForm->createView()
                                ));
        }
    }
    
    /**
     * Documentación para el método 'editCajaAction'
     * 
     * Método que edita la información de un elemento de tipo caja
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 30-10-2017 - Se modifica la opción para actualizar las coordenadas de una misma caja para todas las empresas asociadas
     *                           a dicho elemento.
     *                           Además se actualizan las coordenadas de todos los elementos relacionados a dicha caja sin importar la empresa
     *                           a la que estén asociadas
     * 
     **/
    public function editCajaAction($id){
        $request = $this->get('request');
        $session  = $request->getSession();
        $empresaId=$session->get('idEmpresa');
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) {
            throw new NotFoundHttpException('No existe el elemento -CAJA DISPERSION- que se quiere modificar');
        }
        else{
            $objElementoUbica = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findBy(array("elementoId"    => $elemento->getId(),
                                                                                                          "empresaCod"    => $empresaId));
            $objUbicacion           = $em->getRepository('schemaBundle:InfoUbicacion')->findBy(array("id"=>$objElementoUbica[0]->getUbicacionId()));
            $objParroquia           = $em->getRepository('schemaBundle:AdmiParroquia')->findBy(array("id"=>$objUbicacion[0]->getParroquiaId()));
            $objCanton              = $em->getRepository('schemaBundle:AdmiCanton')->findBy(array("id"=>$objParroquia[0]->getCantonId()));
            $objCantonJurisdiccion  = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findBy(array( "cantonId" =>$objCanton[0]->getId()));
        }

        $formulario =$this->createForm(new InfoElementoCajaType(array("empresaId"=>$empresaId)), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoCaja:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'caja'                  => $elemento,
                                'ubicacion'             => $objUbicacion[0],
                                'cantonJurisdiccion'    => $objCantonJurisdiccion[0])
                            );
    }
    
    
    /**
     * Documentación para el método 'updateCajaAction'
     * 
     * Método que actualiza la información de un elemento de tipo caja
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 14-01-2015 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 14-05-2016 - Se modifica el método para que actualice los detalles del elemento en estado 'Activo'
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 14-10-2016 - Se agrega la modificación del detalle Ubicado en
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 30-10-2017 - Se modifica la opción para actualizar las coordenadas de una misma caja para todas las empresas asociadas
     *                           a dicho elemento.
     *                           Además se actualizan las coordenadas de todos los elementos relacionados a dicha caja sin importar la empresa
     *                           a la que estén asociadas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 22-11-2018 - Se actualiza el campo id por el idEdificacion con el objetivo de solucionar un error que se esta presentando en la
     *                           creación de cajas
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.7 10-04-2019 - Se valida si hay un cambio a nivel de coordenadas y se añade una observación con el detalle de las
     *                           coordenadas cambiadas.
     **/
    public function updateCajaAction($id)
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objCaja            = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($id);
        if(!$objCaja)
        {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }

        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIpClient            = $objRequest->getClientIp();
        $parametros             = $objRequest->request->get('telconet_schemabundle_infoelementocajatype');

        $nombreElemento         = $parametros['nombreElemento'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $parroquiaId            = $parametros['parroquiaId'];
        $direccionUbicacion     = $objRequest->get('direccionUbicacion');
        $longitudUbicacion      = $objRequest->get('longitudUbicacion');
        $latitudUbicacion       = $objRequest->get('latitudUbicacion');
        $alturaSnm              = $objRequest->get('alturaSnm');
        $nivel                  = $parametros['nivel'];
        $idNodoCliente          = $parametros['idEdificacion'];
        $strUbicado             = $parametros['ubicadoEn'];
        
        $emInfraestructura->beginTransaction();
        try
        {
            $parroquia = $emInfraestructura->find('schemaBundle:AdmiParroquia', $parroquiaId);

            $objElementoUbica   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneByElementoId($id);
            $idParroquia        = '';
            $longitud           = '';
            $latitud            = '';
            $direccion          = '';
            $altura             = '';
            if($objElementoUbica)
            {
                $objUbicacion   = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')->findOneById($objElementoUbica->getUbicacionId());
                $idParroquia    = $objUbicacion->getParroquiaId()->getId();
                $longitud       = $objUbicacion->getLongitudUbicacion();
                $latitud        = $objUbicacion->getLatitudUbicacion();
                $direccion      = $objUbicacion->getDireccionUbicacion();
                $altura         = $objUbicacion->getAlturaSnm();
            }

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "de la caja "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            
            if($longitud != $longitudUbicacion || $latitud != $latitudUbicacion || $parroquiaId != $idParroquia || 
                strtoupper($direccion) != strtoupper($direccionUbicacion) || $alturaSnm != $altura)
            {
                //Sólo se consulta por los elementos cuya relación se encuentre en estado 'Activo'
                $objRelacionElemento    = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                            ->findBy(array( "elementoIdA"   => $id,
                                                                            "estado"        => "Activo"));
                foreach($objRelacionElemento as $elemento)
                {
                    $objElementoUbicaHijos  = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                ->findByElementoId($elemento->getElementoIdB());

                    foreach($objElementoUbicaHijos as $objElementoUbicaHijo)
                    {
                        if(is_object($objElementoUbicaHijo))
                        {
                            $objUbicacionHijo = $objElementoUbicaHijo->getUbicacionId();
                            if(is_object($objUbicacionHijo))
                            {
                                $objUbicacionHijo->setLatitudUbicacion($latitudUbicacion);
                                $objUbicacionHijo->setLongitudUbicacion($longitudUbicacion);
                                $objUbicacionHijo->setDireccionUbicacion($direccionUbicacion);
                                $objUbicacionHijo->setAlturaSnm($alturaSnm);
                                $objUbicacionHijo->setParroquiaId($parroquia);
                                $emInfraestructura->persist($objUbicacionHijo);
                            }
                        }
                    }
                }
            }
            
            $modeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            //elemento
            $objCaja->setNombreElemento($nombreElemento);
            $objCaja->setDescripcionElemento($descripcionElemento);
            $objCaja->setModeloElementoId($modeloElemento);
            $objCaja->setUsrResponsable($objSession->get('user'));
            $objCaja->setUsrCreacion($objSession->get('user'));
            $objCaja->setFeCreacion(new \DateTime('now'));
            $objCaja->setIpCreacion($strIpClient);
            $emInfraestructura->persist($objCaja);
            if(($longitud != $longitudUbicacion)||($latitud != $latitudUbicacion))
            {
                $strObservacion = "Coordenadas Modificadas.
                    </br>  Lat-Long Actuales   : (".$latitudUbicacion.",".$longitudUbicacion.")
                    </br>  Lat-Long Anteriores : (".$latitud.",".$longitud.")"; 
            }
            else
            {
                $strObservacion = "Se modificó la Caja";
            }
            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($objCaja);
            $historialElemento->setEstadoElemento("Modificado");
            $historialElemento->setObservacion($strObservacion);
            $historialElemento->setUsrCreacion($objSession->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($strIpClient);
            $emInfraestructura->persist($historialElemento);

            $objEmpresaCajasUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findByElementoId($id);
            foreach($objEmpresaCajasUbica as $objEmpresaCajaUbica)
            {
                if(is_object($objEmpresaCajaUbica))
                {
                    $objUbicacionCaja = $objEmpresaCajaUbica->getUbicacionId();
                    if(is_object($objUbicacionCaja))
                    {
                        $objUbicacionCaja->setLatitudUbicacion($latitudUbicacion);
                        $objUbicacionCaja->setLongitudUbicacion($longitudUbicacion);
                        $objUbicacionCaja->setDireccionUbicacion($direccionUbicacion);
                        $objUbicacionCaja->setAlturaSnm($alturaSnm);
                        $objUbicacionCaja->setParroquiaId($parroquia);
                        $objUbicacionCaja->setUsrCreacion($objSession->get('user'));
                        $objUbicacionCaja->setFeCreacion(new \DateTime('now'));
                        $objUbicacionCaja->setIpCreacion($strIpClient);
                        $emInfraestructura->persist($objUbicacionCaja);
                    }
                }
            }

            $objRelacionElementoOld = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                        ->findOneBy(array(  'elementoIdB'   => $id,
                                                                            'estado'        => 'Activo'));
            if($objRelacionElementoOld)
            {
                if($idNodoCliente)
                {
                    //actaulizamos el nodo cliente
                    if($idNodoCliente != $objRelacionElementoOld->getElementoIdA())
                    {
                        $objRelacionElementoOld->setEstado('Eliminado');
                        $emInfraestructura->persist($objRelacionElementoOld);

                        //relacion elemento
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($idNodoCliente);
                        $objRelacionElemento->setElementoIdB($id);
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Nodo Cliente contiene Caja");
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($strIpClient);
                        $emInfraestructura->persist($objRelacionElemento);
                    }
                }
                else
                {
                    $objRelacionElementoOld->setEstado('Eliminado');
                    $emInfraestructura->persist($objRelacionElementoOld);
                }
            }
            else
            {
                if($idNodoCliente)
                {
                    //relacion elemento
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($idNodoCliente);
                    $objRelacionElemento->setElementoIdB($id);
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("Nodo Cliente contiene Caja");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($strIpClient);
                    $emInfraestructura->persist($objRelacionElemento);
                }
            }

            //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
            $objDetalleNivel = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                 ->findOneBy(array("elementoId" => $id, "detalleNombre" => "NIVEL"));
            $objDetalleNivel->setDetalleValor($nivel);
            $objDetalleNivel->setEstado('Activo');
            $emInfraestructura->persist($objDetalleNivel);
            $emInfraestructura->flush();

            if($strUbicado)
            {
                //caracteristica para conocer donde está ubicado
                $objDetalleUbicadoOld   = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array(  "elementoId"    => $id, 
                                                                                "detalleNombre" => "UBICADO EN", 
                                                                                "estado"        => "Activo"));

                if(is_object($objDetalleUbicadoOld))
                {
                    //actualizo si ya existe debido a la programación de todo este método
                    $objDetalleUbicadoOld->setDetalleValor($strUbicado);
                    $objDetalleUbicadoOld->setUsrCreacion($objSession->get('user'));
                    $emInfraestructura->persist($objDetalleUbicadoOld);
                    $emInfraestructura->flush();
                }
                else
                {
                    //si no existe creamos el nuevo registro
                    $objDetalleUbicado = new InfoDetalleElemento();
                    $objDetalleUbicado->setElementoId($id);
                    $objDetalleUbicado->setDetalleNombre("UBICADO EN");
                    $objDetalleUbicado->setDetalleValor($strUbicado);
                    $objDetalleUbicado->setDetalleDescripcion("Caracteristicas para indicar donde se ubica el Elemento");
                    $objDetalleUbicado->setFeCreacion(new \DateTime('now'));
                    $objDetalleUbicado->setUsrCreacion($objSession->get('user'));
                    $objDetalleUbicado->setIpCreacion($strIpClient);
                    $objDetalleUbicado->setEstado('Activo');
                    $emInfraestructura->persist($objDetalleUbicado);
                    $emInfraestructura->flush();
                }
            }
            /*
             * Bloque que busca los detalle del elemento Caja y les actualiza el estado a 'Activo'
             */
            $arrayInfoDetallesElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->findByElementoId($id);

            foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
            {
                $objInfoDetalleElemento->setEstado('Activo');
                $emInfraestructura->persist($objInfoDetalleElemento);
                $emInfraestructura->flush();
            }
            /*
             * Fin Bloque que busca los detalle del elemento Caja y les actualiza el estado a 'Activo'
             */

            $emInfraestructura->flush();
            $emInfraestructura->commit();

            return $this->redirect($this->generateUrl('elementocaja_showCaja', array('id' => $objCaja->getId())));
        } 
        catch (\Exception $e) 
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            $emInfraestructura->close();
            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementocaja_editCaja', array('id' => $id)));
        }
    }

    /*
     * @version 1.1 14-01-2015 John Vera Nodo cliente
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.2 14-01-2015 Se aumenta el detalle Ubicado En
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 30-10-2017 Se invoca a la función para obtener los datos de la caja filtrando la información de la ubicación por empresa
     * 
     */
    public function showCajaAction($id){
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
            $arrayRespuestaElemento = $datosElemento->obtenerDatosElementoEmpresa(array("intIdElemento"  => $id, 
                                                                                        "strCodEmpresa"  => $empresaId));
            
            $ipElemento         = $arrayRespuestaElemento['ipElemento'];
            $arrayHistorial     = $arrayRespuestaElemento['historialElemento'];
            $ubicacion          = $arrayRespuestaElemento['ubicacion'];
            $jurisdiccion       = $arrayRespuestaElemento['jurisdiccion'];
            $nodoCliente        = $arrayRespuestaElemento['nodoCliente'];
            
            //Se obtiene el detalle ubicacado en
            $objDetalleElementoUbicado = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array('elementoId'    => $id,
                                                              'detalleNombre' => 'UBICADO EN',
                                                              'estado'        => 'Activo'));
            $strUbicadoEn = '';
            if(is_object($objDetalleElementoUbicado))
            {
                $strUbicadoEn = $objDetalleElementoUbicado->getDetalleValor();
            }
           
        }

        return $this->render('tecnicoBundle:InfoElementoCaja:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'nodoCliente'       => $nodoCliente,
            'strUbicadoEn'      => $strUbicadoEn,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    public function getEncontradosCajaAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"CAJA DISPERSION"));
        
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
            ->generarJsonCajas(strtoupper($nombreElemento),$modeloElemento,$marcaElemento,$tipoElemento[0]->getId(),$canton,$jurisdiccion,$estado,$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function cargarDatosCajaAction(){
        $respuesta = new Response();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC = $this->getDoctrine()->getManager('telconet');
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $empresaId=$session->get('idEmpresa');

        $idCaja = $peticion->get('idCaja');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosCaja($idCaja, $empresaId, $em, $emC);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    
    /**     
     * Documentación para el método 'deleteAjaxCajaAction'
     * 
     * Método que eliminar la información de un elemento de tipo caja
     * 
     * @version 1.0 Version Inicial 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-05-2016 - Se modifica el método para que actualice los detalles del elemento en estado 'Eliminado'
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 28-07-2017 - Se modifica el método para cuando se requiera eliminar solo elimine las relaciones que tenga creadas
     *                           y no pregunte si el estado de los elementos contenidos es Eliminado, dado que no premisa para eliminar una caja
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 18-08-2020 - Se modifica el método para cuando se requiera eliminar la caja lo debe hacer siempre y cuando el elemento que 
     *                           lo contiene no se encuentre en estado Activo
     * 
     */
    public function deleteAjaxCajaAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $session  = $request->getSession();
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em->getConnection()->beginTransaction();
        
        $array_valor = explode("|",$parametro);
        
        foreach($array_valor as $id):
            //variable para la eliminación de cajas
            $intFlagEliminacion = 0;
        
            if (null == $objCaja = $em->find('schemaBundle:InfoElemento', $id)) 
            {
                $respuesta->setContent("No existe la entidad");
            }
            else
            {
                //Se obtiene todas las relaciones de contenencia para poder darle de baja a la caja de dispersion
                $arrayRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                            ->findBy(array( "elementoIdA" =>$objCaja->getId()));
                
                //Consultamos los elementos que contiene la caja si se encuentran en estado Activo
                foreach($arrayRelacionElemento as $objRelacionElementoContiene)
                {
                    if($objRelacionElementoContiene->getEstado() == "Activo")
                    {
                        //No se podrá eliminar la caja ya que tiene elementos en estado Activo
                        $intFlagEliminacion = 1;
                    }
                }
                
                //Consultamos por la variable de eliminación. Si está en 0 se puede eliminar la caja caso contrario no se lo puede hacer
                if ($intFlagEliminacion == 0)
                {
                    $objCaja->setEstado("Eliminado");
                    $objCaja->setUsrCreacion($session->get('user'));
                    $objCaja->setFeCreacion(new \DateTime('now'));
                    $objCaja->setIpCreacion($peticion->getClientIp());  
                    $em->persist($objCaja);

                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($objCaja);
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setObservacion("Se elimino la Caja");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    foreach($arrayRelacionElemento as $objRelacionElemento)
                    {
                        $objRelacionElemento->setEstado("Eliminado");
                        $objRelacionElemento->setUsrCreacion($session->get('user'));
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                        $em->persist($objRelacionElemento);
                    }
                
                    //empresa elemento
                    $objEmpresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array( "elementoId" =>$objCaja));
                    $objEmpresaElemento->setEstado("Eliminado");
                    $objEmpresaElemento->setObservacion("Se elimino la caja");
                    $objEmpresaElemento->setUsrCreacion($session->get('user'));
                    $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                    $objEmpresaElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($objEmpresaElemento);

                    /*
                    * Bloque que busca los detalle del elemento Caja y les actualiza el estado a 'Eliminado'
                    */
                    $arrayInfoDetallesElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findByElementoId($id);

                    foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                    {
                        $objInfoDetalleElemento->setEstado('Eliminado');
                        $em->persist($objInfoDetalleElemento);
                        $em->flush();
                    }
                    /*
                    * Fin Bloque que busca los detalle del elemento Caja y les actualiza el estado a 'Eliminado'
                    */

                    $em->flush();
                
                    $respuesta->setContent("OK");
                }
                else
                {
                    $respuesta->setContent("No se puede eliminar la caja " .$objCaja->getNombreElemento()." ya que contiene elementos Activos");
                }
            }
        endforeach;
        
        $em->getConnection()->commit();
        
        return $respuesta;
    }
    
    public function getElementosPorCajaAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->get('request');
        $session  = $request->getSession();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $empresa = $session->get('idEmpresa');
        
        $peticion = $this->get('request');
        
        $cajaId = $id;
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosPorCaja($cajaId,$empresa,"Activo",$start, 100, $emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'ajaxGetElementosPorCajaAction'.
     *
     * Método utilizado para generar el Json de todos los elementos contenidos en una caja o contenedor mayor
     *
     * @param string idElemento id del elemento contenedor o CAJA
     * @param string tipoElemento  nombre del tipo elemento contenido a buscar
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'idElemento':'',
     *                                   'nombreElemento':'',
     *                                   }]
     *                      }]
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 07-04-2016
    */
    public function ajaxGetElementosPorCajaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->get('request');
        $session = $request->getSession();                

        $peticion = $this->get('request');
        
        $idElemento   = $peticion->get('idElemento');        
        $tipoElemento = $peticion->get('tipoElemento');        

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->getJsonElementosPorContenedor($idElemento, $tipoElemento, $session->get('idEmpresa'));
        
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}