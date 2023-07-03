<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Form\InfoElementoCassetteType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Documentación para la clase 'InfoElementoCassette'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Cassette's
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 17-12-2015
 * @since 1.0
 */
class InfoElementoCassetteController extends Controller
{

    /**
     * @Secure(roles="ROLE_321-1")
     * 
     * Documentación para el método 'indexCassetteAction'.
     *
     * Metodo utilizado para retornar a la pagina principal de la administracion de Cassette's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     */
    public function indexCassetteAction()
    {
        $rolesPermitidos = array();

        //MODULO 321 - CASSETTE
        if (true === $this->get('security.context')->isGranted('ROLE_321-4'))
        {
            $rolesPermitidos[] = 'ROLE_321-4'; //editar elemento CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-8'))
        {
            $rolesPermitidos[] = 'ROLE_321-8'; //eliminar elemento CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-6'))
        {
            $rolesPermitidos[] = 'ROLE_321-6'; //ver elemento CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-3317'))
        {
            $rolesPermitidos[] = 'ROLE_321-3317'; //ver administracion puertos CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-2'))
        {
            $rolesPermitidos[] = 'ROLE_321-2'; //nuevo elemento CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-3'))
        {
            $rolesPermitidos[] = 'ROLE_321-3'; //crear elemento CASSETTE
        }
        if (true === $this->get('security.context')->isGranted('ROLE_321-5'))
        {
            $rolesPermitidos[] = 'ROLE_321-5'; //update elemento CASSETTE
        }

        return $this->render('tecnicoBundle:InfoElementoCassette:index.html.twig', array(
                    'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_321-2")
     * 
     * Documentación para el método 'newCassetteAction'.
     *
     * Metodo utilizado para retornar a la pagina de creacion de nuevo Cassette
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     */
    public function newCassetteAction()
    {
        $objElemento = new InfoElemento();
        $form        = $this->createForm(new InfoElementoCassetteType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoCassette:new.html.twig', array(
                             'form' => $form->createView())
        );
    }

    /**
     * @Secure(roles="ROLE_321-3")
     * 
     * Documentación para el método 'createCassetteAction'.
     *
     * Método utilizado para crear el nuevo Cassette
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @author Antonio Ayala Torres <afayala@telconet.ec>
     * @version 1.2 31-03-2022 - Se agrega tilde en el campo observacion en la palabra ingresó
     */
    public function createCassetteAction()
    {
        $peticion                = $this->get('request');
        $session                 = $peticion->getSession();
        $em                      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objElementoCassette     = new InfoElemento();
        $arrayParametros         = $peticion->request->get('telconet_schemabundle_infoelementocassettetype');
        $strNombreElemento       = $arrayParametros['nombreElemento'];
        $intModeloElementoId     = $arrayParametros['modeloElementoId'];
        $intElementoContenedorId = $arrayParametros['elementoContenedorId'];
        $strDescripcionElemento  = $arrayParametros['descripcionElemento'];
        $objForm                 = $this->createForm(new InfoElementoCassetteType(), $objElementoCassette);
        $boolMensajeUsuario      = false;
        $em->beginTransaction();
        try
        {
            $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')
                               ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            $objForm->bind($peticion);
            if ($objForm->isValid())
            {
                //verificar que el nombre del elemento no se repita
                $objElementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("nombreElemento" => $strNombreElemento, "estado" => "Activo"));

                if ($objElementoRepetido)
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                }

                $objModeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $intModeloElementoId);
                $objElementoCassette->setNombreElemento($strNombreElemento);
                $objElementoCassette->setDescripcionElemento($strDescripcionElemento);
                $objElementoCassette->setModeloElementoId($objModeloElemento);
                $objElementoCassette->setUsrResponsable($session->get('user'));
                $objElementoCassette->setUsrCreacion($session->get('user'));
                $objElementoCassette->setFeCreacion(new \DateTime('now'));
                $objElementoCassette->setIpCreacion($peticion->getClientIp());
                $objElementoCassette->setEstado("Activo");
                $em->persist($objElementoCassette);
                $em->flush();

                //buscar el interface Modelo y crear nuevos registros de Interface
                $objInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                         ->findBy(array("modeloElementoId" => $intModeloElementoId));
                foreach ($objInterfaceModelo as $im)
                {
                    $intCantidadInterfaces = $im->getCantidadInterface();
                    $formato               = $im->getFormatoInterface();

                    for ($i = 1; $i <= $intCantidadInterfaces; $i++)
                    {
                        $objInterfaceElemento       = new InfoInterfaceElemento();
                        $format                     = explode("?", $formato);
                        $strNombreInterfaceElemento = $format[0] . $i;

                        $objInterfaceElemento->setNombreInterfaceElemento($strNombreInterfaceElemento);
                        $objInterfaceElemento->setElementoId($objElementoCassette);
                        $objInterfaceElemento->setEstado("not connect");
                        $objInterfaceElemento->setUsrCreacion($session->get('user'));
                        $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                        $objInterfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($objInterfaceElemento);
                        $em->flush();
                    }
                }
                
                //se crean enlaces entre interfaces del elemento para mayor escabilidad
                $objInterfacesElementos = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                             ->findBy(array("elementoId" => $objElementoCassette->getId()));
                foreach ($objInterfacesElementos as $objInterfaceElemento)
                {
                    $pos = strpos($objInterfaceElemento->getNombreInterfaceElemento(), 'IN ');
                    if ($pos !== false)
                    {
                        $strNombreInterfaceOut = str_replace("IN ", "OUT ", $objInterfaceElemento->getNombreInterfaceElemento());
                        $objInterfaceOut       = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findOneBy(array("elementoId"              => $objElementoCassette->getId(),
                                                                      "nombreInterfaceElemento" => $strNombreInterfaceOut));
                        $enlace = new InfoEnlace();
                        $enlace->setInterfaceElementoIniId($objInterfaceElemento);
                        $enlace->setInterfaceElementoFinId($objInterfaceOut);
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

                //se crean registro necesarios para asignarle una ubicacion al elemento creado y asignarle elemento contenedor (CAJA)
                
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($intElementoContenedorId);
                $objRelacionElemento->setElementoIdB($objElementoCassette->getId());
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("caja contiene cassette");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($session->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objRelacionElemento);

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoCassette);
                $objHistorialElemento->setEstadoElemento("Activo");
                $objHistorialElemento->setObservacion("Se ingresó un Cassette");
                $objHistorialElemento->setUsrCreacion($session->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objHistorialElemento);

                //tomar datos nodo
                $objCajaEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                      ->findOneBy(array("elementoId" => $intElementoContenedorId));
                $objCajaUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')
                                       ->find($objCajaEmpresaElementoUbicacion->getUbicacionId()->getId());
                
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objCajaUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objCajaUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "de la caja ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al cassette ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Cajas"
                                                                                                     ));
                if($arrayRespuestaCoordenadas["status"] === "ERROR")
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                }
                //info ubicacion
                $objParroquia = $em->find('schemaBundle:AdmiParroquia', $objCajaUbicacion->getParroquiaId());
                $objUbicacionElemento = new InfoUbicacion();
                $objUbicacionElemento->setLatitudUbicacion($objCajaUbicacion->getLatitudUbicacion());
                $objUbicacionElemento->setLongitudUbicacion($objCajaUbicacion->getLongitudUbicacion());
                $objUbicacionElemento->setDireccionUbicacion($objCajaUbicacion->getDireccionUbicacion());
                $objUbicacionElemento->setAlturaSnm($objCajaUbicacion->getAlturaSnm());
                $objUbicacionElemento->setParroquiaId($objParroquia);
                $objUbicacionElemento->setUsrCreacion($session->get('user'));
                $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                $objUbicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objUbicacionElemento);

                //empresa elemento ubicacion
                $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                $objEmpresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $objEmpresaElementoUbica->setElementoId($objElementoCassette);
                $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                $objEmpresaElementoUbica->setUsrCreacion($session->get('user'));
                $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $objEmpresaElementoUbica->setIpCreacion($peticion->getClientIp());
                $em->persist($objEmpresaElementoUbica);

                //empresa elemento
                $objEmpresaElemento = new InfoEmpresaElemento();
                $objEmpresaElemento->setElementoId($objElementoCassette);
                $objEmpresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $objEmpresaElemento->setEstado("Activo");
                $objEmpresaElemento->setUsrCreacion($session->get('user'));
                $objEmpresaElemento->setIpCreacion($peticion->getClientIp());
                $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($objEmpresaElemento);

                $em->flush();
                $em->commit();

                return $this->redirect($this->generateUrl('elementocassette_showCassette', array('id' => $objElementoCassette->getId())));
            }
        }
        catch (\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            error_log("Error: " . $e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->render('tecnicoBundle:InfoElementoCassette:new.html.twig', array(
                                                                                            'form'   => $objForm->createView()
                                ));
        }
    }

    /**
     * @Secure(roles="ROLE_321-4")
     * 
     * Documentación para el método 'editCassetteAction'.
     *
     * Metodo utilizado para retornar a la pagina de edición de un Cassette
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     */
    public function editCassetteAction($id)
    {
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();
        $empresaId         = $session->get('idEmpresa');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -cassette- que se quiere modificar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $servicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');
            $arrayDatosElementos    = $servicioTecnicoService->obtenerDatosElemento($id, $empresaId);
            $objUbicacion           = $arrayDatosElementos['ubicacion'];

            $objElementoPadreCassette = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                          ->findOneBy(array("elementoIdB" => $objElemento->getId(), "estado" => "Activo"));
            $objElemento->setNodoElementoId($objElementoPadreCassette->getElementoIdA());
            $objElementoContenedor = $emInfraestructura->find('schemaBundle:InfoElemento', $objElementoPadreCassette->getElementoIdA());
            
        }

        $formulario = $this->createForm(new InfoElementoCassetteType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoCassette:edit.html.twig', array(
                             'edit_form'                => $formulario->createView(),
                             'odf'                      => $objElemento,
                             'ubicacion'                => $objUbicacion,
                             'nombreElementoContenedor' => $objElementoContenedor->getNombreElemento())
                            );
    }

    /**
     * @Secure(roles="ROLE_321-5")
     * 
     * Documentación para el método 'updateCassetteAction'.
     *
     * Metodo utilizado para actualizar el Cassette
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function updateCassetteAction($id)
    {
        $em                     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion               = $this->get('request');
        $session                = $peticion->getSession();
        $request                = $this->get('request');
        $arrayParametros        = $request->request->get('telconet_schemabundle_infoelementocassettetype');
        $strNombreElemento      = $arrayParametros['nombreElemento'];
        $intCajaElementoId      = $arrayParametros['elementoContenedorId'];
        $strDescripcionElemento = $arrayParametros['descripcionElemento'];
        $intModeloElementoId    = $arrayParametros['modeloElementoId'];
        $intIdUbicacion         = $request->request->get('idUbicacion');
        $intCajaElementoIdAntes = "";
        $boolMensajeUsuario     = false;

        $em->getConnection()->beginTransaction();
        try
        {
            $objModeloElemento  = $em->find('schemaBundle:AdmiModeloElemento', $intModeloElementoId);
            $objElemento        = $em->getRepository('schemaBundle:InfoElemento')->find($id);
            $objTipoMedio       = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                     ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            if (!$objElemento)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }
            
            $intModeloAnterior = $objElemento->getModeloElementoId();
            $flag              = 0;
            //revisar si es cambio de modelo para crear o eliminar interface
            if ($intModeloAnterior->getId() != $objModeloElemento->getId())
            {
                $objInterfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $intModeloAnterior->getId()));
                $objInterfaceModeloNuevo    = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $objModeloElemento->getId()));

                $intCantAnterior    = $objInterfaceModeloAnterior->getCantidadInterface();
                $intCantNueva       = $objInterfaceModeloNuevo->getCantidadInterface();
                $strFormatoAnterior = $objInterfaceModeloAnterior->getFormatoInterface();
                $strFormatoNuevo    = $objInterfaceModeloNuevo->getFormatoInterface();
                
                //buscar el interface Modelo
                $arrayAdmiInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                               ->findBy(array("modeloElementoId" => $intModeloAnterior->getId()));
                
                //se valida si la cantidad anterior de puertos es mayor a la cantidad nueva
                if ($intCantAnterior > $intCantNueva)
                {
                    //se valida que no se encuentre ocupada alguna interface del CASSETTE
                    foreach ($arrayAdmiInterfaceModelo as $objInterfaceModelo)
                    {
                        $strFormatoAnterior = $objInterfaceModelo->getFormatoInterface();
                        $format             = explode("?", $strFormatoAnterior);
                        //revisar puertos restantes si estan ocupados
                        for ($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                        {
                            $strNombreInterfaceElementoAnterior = $format[0] . $i;
                            $arrayInterfaceElemento           = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->findBy(array("elementoId"              => $objElemento->getId(),
                                                                                 "nombreInterfaceElemento" => $strNombreInterfaceElementoAnterior));
                            
                            foreach ($arrayInterfaceElemento as $objInterfaceElemento)
                            {   
                                if ($objInterfaceElemento->getEstado() != 'deleted' && $objInterfaceElemento->getEstado() != 'not connect')
                                {
                                    $flag = 1;
                                    break;
                                }
                            }
                        }
                        if ($flag == 1)
                        {
                            break;
                        }
                    }
                    //en caso de esta la bandera FLAG en 0 podemos continunar con la Edición
                    if ($flag == 0)
                    {
                        //cambiar estado a eliminado de interface 
                        foreach ($arrayAdmiInterfaceModelo as $objInterfaceModelo)
                        {
                            $strFormatoAnterior = $objInterfaceModelo->getFormatoInterface();
                            $format             = explode("?", $strFormatoAnterior);
                            for ($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                            {
                                $strNombreInterfaceElementoAnterior = $format[0] . $i;
                                $arrayInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findBy(array("elementoId" => $objElemento->getId(),
                                                                            "nombreInterfaceElemento" => $strNombreInterfaceElementoAnterior));
                                foreach ($arrayInterfaceElemento as $objInterfaceElementoViejo)
                                {
                                    if ($objInterfaceElementoViejo->getEstado() != "deleted")
                                    {
                                        $objInterfaceElementoViejo->setEstado("deleted");
                                        $objInterfaceElementoViejo->setUsrCreacion($session->get('user'));
                                        $objInterfaceElementoViejo->setFeCreacion(new \DateTime('now'));
                                        $objInterfaceElementoViejo->setIpCreacion($peticion->getClientIp());
                                        $em->persist($objInterfaceElementoViejo);
                                    }
                                }
                            }//fin de for($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                        }//fin de foreach($entityAdmiInterfaceModelo as $entity)
                    }
                }
                //se valida que la cantidad anterior sea menor a la cantidad nueva
                else if ($intCantAnterior < $intCantNueva)
                {
                    //crear nuevas interfaces
                    foreach ($arrayAdmiInterfaceModelo as $objInterfaceModelo)
                    {
                        $strFormatoNuevo = $objInterfaceModelo->getFormatoInterface();
                        $format          = explode("?", $strFormatoNuevo);
                        for ($i = ($intCantAnterior + 1); $i <= $intCantNueva; $i++)
                        {
                            $objInterfaceElemento = new InfoInterfaceElemento();
                            $strNombreInterfaceElementoNuevo = $format[0] . $i;
                            $objInterfaceElemento->setNombreInterfaceElemento($strNombreInterfaceElementoNuevo);
                            $objInterfaceElemento->setElementoId($objElemento);
                            $objInterfaceElemento->setEstado("not connect");
                            $objInterfaceElemento->setUsrCreacion($session->get('user'));
                            $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                            $objInterfaceElemento->setIpCreacion($peticion->getClientIp());
                            $em->persist($objInterfaceElemento);
                            $em->flush();
                        }//fin de for($i = ($intCantAnterior + 1); $i <= $intCantNueva; $i++)
                    }//fin de foreach($entityAdmiInterfaceModelo as $entity)
                    //se crean relaciones entre interfaces del elemento para mayor escabilidad
                    $objInterfacesElementos = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->findBy(array("elementoId" => $objElemento->getId(),
                                                                "estado"     => 'not connect'));
                    //se procede a crear nuevos enlaces internos del elemento
                    foreach ($objInterfacesElementos as $objInterfaceElemento)
                    {
                        $pos = strpos($objInterfaceElemento->getNombreInterfaceElemento(), 'IN ');
                        if ($pos !== false)
                        {
                            $strNombreInterfaceOut = str_replace("IN ", "OUT ", $objInterfaceElemento->getNombreInterfaceElemento());
                            $arrayInterfaceOut     = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->findBy(array("elementoId"              => $objElemento->getId(),
                                                                       "nombreInterfaceElemento" => $strNombreInterfaceOut));

                            foreach ($arrayInterfaceOut as $objInterfaceOut)
                            {
                                if ($objInterfaceOut->getEstado() != "deleted")
                                {
                                    $entityInterfaceOut = $objInterfaceOut;
                                    break;
                                }
                            }
                            //se valida si existe enlace
                            $entityEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                               ->findOneBy(array("interfaceElementoIniId" => $objInterfaceElemento->getId(),
                                                                 "interfaceElementoFinId" => $entityInterfaceOut->getId(),
                                                                 "estado"                 => 'Activo'
                                                                )
                                                           );
                            //se crean enlaces para nuevos puertos creados
                            if (!$entityEnlace)
                            {
                                $enlace = new InfoEnlace();
                                $enlace->setInterfaceElementoIniId($objInterfaceElemento);
                                $enlace->setInterfaceElementoFinId($entityInterfaceOut);
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
                    }//foreach($objInterfacesElementos as $objInterfaceElemento)
                }//fin de else if($intCantAnterior < $intCantNueva)
            }


            if ($flag == 0)
            {
                //se actualiza informacion del elemento Cassette
                $objElemento->setNombreElemento($strNombreElemento);
                $objElemento->setDescripcionElemento($strDescripcionElemento);
                $objElemento->setModeloElementoId($objModeloElemento);
                $objElemento->setUsrResponsable($session->get('user'));
                $objElemento->setUsrCreacion($session->get('user'));
                $objElemento->setFeCreacion(new \DateTime('now'));
                $objElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objElemento);


                //se verifica el elemento contedor antiguo del cassette
                $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                          ->findOneBy(array("elementoIdB" => $objElemento, "estado" => "Activo"));
                $objElementoPadre       = $em->find('schemaBundle:InfoElemento', $objRelacionElemento->getElementoIdA());
                $intCajaElementoIdAntes = $objElementoPadre->getId();

                //se verifica si cambio el elemento contedor del Cassette
                if ($intCajaElementoIdAntes != $intCajaElementoId)
                {
                    //eliminar recursos antiguos
                    $objRelacionesElementoAntes = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                     ->findBy(array("elementoIdB" => $objElemento, "estado" => "Activo"));
                    foreach ($objRelacionesElementoAntes as $objRelacionElementoAntes)
                    {
                        $objRelacionElementoAntes->setEstado("Eliminado");
                        $em->persist($objRelacionElementoAntes);
                        $em->flush();
                    }
                    
                    //relacion elemento
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($intCajaElementoId);
                    $objRelacionElemento->setElementoIdB($objElemento->getId());
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("caja contiene cassette");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($session->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($objRelacionElemento);

                    //se actualiza ubicación del Cassette
                    $objCajaEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                          ->findOneBy(array("elementoId" => $intCajaElementoId));
                    $objCajaUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')
                                           ->find($objCajaEmpresaElementoUbicacion->getUbicacionId()->getId());
                    
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objCajaUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objCajaUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "de la caja ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al cassette ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Cajas"
                                                                                                     ));
                    
                    
                    
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    
                    //cambiar ubicacion del Cassette
                    $objParroquia = $em->find('schemaBundle:AdmiParroquia', $objCajaUbicacion->getParroquiaId());
                    $objUbicacionElemento = $em->find('schemaBundle:InfoUbicacion', $intIdUbicacion);
                    $objUbicacionElemento->setLatitudUbicacion($objCajaUbicacion->getLatitudUbicacion());
                    $objUbicacionElemento->setLongitudUbicacion($objCajaUbicacion->getLongitudUbicacion());
                    $objUbicacionElemento->setDireccionUbicacion($objCajaUbicacion->getDireccionUbicacion());
                    $objUbicacionElemento->setAlturaSnm($objCajaUbicacion->getAlturaSnm());
                    $objUbicacionElemento->setParroquiaId($objParroquia);
                    $objUbicacionElemento->setUsrCreacion($session->get('user'));
                    $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                    $objUbicacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($objUbicacionElemento);
                }


                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElemento);
                $objHistorialElemento->setEstadoElemento("Modificado");
                $objHistorialElemento->setObservacion("Se modifico el Cassette");
                $objHistorialElemento->setUsrCreacion($session->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objHistorialElemento);

                $em->flush();
                $em->getConnection()->commit();

                return $this->redirect($this->generateUrl('elementocassette_showCassette', array('id' => $objElemento->getId())));
            }
            else
            {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                $this->get('session')->getFlashBag()->add('notice', 'Elemento aun tiene puertos a eliminar con estado Conectado, favor regularice!');
                return $this->redirect($this->generateUrl('elementocassette_editCassette', array('id' => $objElemento->getId())));
            }
        }
        catch (\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            error_log("Error: " . $e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementocassette_editCassette', array('id' => $id)));
        }
    }

    /**
     * @Secure(roles="ROLE_321-8")
     * 
     * Documentación para el método 'deleteCassetteAction'.
     *
     * Metodo utilizado para eliminar un Cassette
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     */
    public function deleteCassetteAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC            = $this->getDoctrine()->getManager('telconet');
        $intIdElemento  = $peticion->get('param');
        $em->getConnection()->beginTransaction();
        try
        {
            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);

            if (!$objElemento)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }

            $objInterfacesElementos = $emC->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $objElemento->getId()));
            for ($i = 0; $i < count($objInterfacesElementos); $i++)
            {
                $strEstadoInterface = $objInterfacesElementos[$i]->getEstado();
                if ($strEstadoInterface != "not connect" && $strEstadoInterface != 'deleted')
                {
                    return $respuesta->setContent("INTERFACES EN USO");
                }
            }

            //elemento
            $objElemento->setEstado("Eliminado");
            $objElemento->setUsrCreacion($session->get('user'));
            $objElemento->setFeCreacion(new \DateTime('now'));
            $objElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objElemento);

            //interfaces
            $objInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $objElemento->getId()));
            for ($i = 0; $i < count($objInterfaceElemento); $i++)
            {
                $objInterface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($objInterfaceElemento[$i]->getId());
                $objInterface->setEstado("Eliminado");
                $em->persist($objInterface);
            }

            //relacion elemento
            $objRelacionesElementos = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elementoIdB" => $objElemento));
            foreach ($objRelacionesElementos as $objRelacionElemento)
            {
                $objRelacionElemento->setEstado("Eliminado");
                $objRelacionElemento->setUsrCreacion($session->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objRelacionElemento);
            }

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setObservacion("Se elimino un Cassette");
            $objHistorialElemento->setUsrCreacion($session->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objHistorialElemento);

            $em->flush();
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('elementocassette'));
        }
        catch (\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            return $respuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }

    /**
     * @Secure(roles="ROLE_321-6")
     * 
     * Documentación para el método 'showCassetteAction'.
     *
     * Metodo utilizado para mostrar la pagina de información de un Cassette
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     */
    public function showCassetteAction($id)
    {
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $objElemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $servicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');
            $arrayRespuestaElemento = $servicioTecnicoService->obtenerDatosElemento($id, $empresaId);

            $arrayHistorial  = $arrayRespuestaElemento['historialElemento'];
            $objUbicacion    = $arrayRespuestaElemento['ubicacion'];
            $objJurisdiccion = $arrayRespuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoCassette:show.html.twig', array(
                             'elemento'          => $objElemento,
                             'historialElemento' => $arrayHistorial,
                             'ubicacion'         => $objUbicacion,
                             'jurisdiccion'      => $objJurisdiccion,
                             'flag'              => $peticion->get('flag'))
                            );
    }

    /**
     * @Secure(roles="ROLE_321-3317")
     * 
     * Documentación para el método 'ajaxGetEncontradosCassetteAction'.
     *
     * Metodo utilizado para obtener información de los Cassette's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * @since 1.0
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 23-08-2016 Se adiciona al combo de estado la palabra --seleccione-- esto se debe a que
     *                         no se procedera a cargar el grid la primera vez por lo tanto cuando se realice
     *                         una busqueda se procedera a modificar el valor de estado a TODOS.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 23-03-2021 - Se define en un solo arreglo de parámetro el filtro para los Cassette
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.3 17-04-2023 - Se agrega validación de filtro por empresa en sesión
     */
    public function ajaxGetEncontradosCassetteAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $session            = $this->get('session');
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento       = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => "CASSETTE"));
        $peticion           = $this->get('request');
        $strNombreElemento  = $peticion->query->get('nombreElemento');
        $ipElemento         = $peticion->query->get('ipElemento');
        $modeloElemento     = $peticion->query->get('modeloElemento');
        $marcaElemento      = $peticion->query->get('marcaElemento');
        $canton             = $peticion->query->get('canton');
        $jurisdiccion       = $peticion->query->get('jurisdiccion');
        $nodoElemento       = $peticion->query->get('popElemento');
        $estado             = $peticion->query->get('estado');
        $idEmpresa          = $session->get('idEmpresa');
        $start              = $peticion->query->get('start');
        $limit              = $peticion->query->get('limit');
        
        if(strtoupper($estado) == 'SELECCIONE')
        {
            $estado = 'Todos';
        }

        $arrayParametros    = array(
            'strNombreElemento' => strtoupper($strNombreElemento),
            'strIpElemento'     => $ipElemento,
            'strModeloElemento' => $modeloElemento,
            'strMarcaElemento'  => $marcaElemento,
            'strTipoElemento'   => $tipoElemento->getId(),
            'strCanton'         => $canton,
            'strJurisdiccion'   => $jurisdiccion,
            'strNodoElemento'   => $nodoElemento,
            'strEstado'         => $estado,
            'strIdEmpresa'      => $idEmpresa,
            'strStart'          => $start,
            'strLimit'          => $limit,
            'prefijoEmpresa'    => $session->get('prefijoEmpresa')
        );
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonOlts($arrayParametros);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_321-3317")
     * 
     * Documentación para el método 'ajaxGetEncontradosContenidosAction'.
     *
     * Metodo utilizado para obtener información de los contenidos en Cassette
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 09-12-2021
     */
    public function ajaxGetEncontradosContenidosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $arrayParametros = array();
        $arrayParametros['elemento'] = 'CASSETTE';
        $arrayParametros['nombre']   = 'TIPO_DE_CONTENIDO_CASSETTE';
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonContenido($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

}
