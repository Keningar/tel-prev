<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Form\InfoElementoRadioType;
use telconet\schemaBundle\Form\InfoElementoPopType;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Response;

class InfoElementoRadioController extends Controller
{ 
    /*
     * 
     * Documentación para el método 'indexRadioAction'.
     *
     * Metodo utilizado redireccionar a la pantalla principal de la Administracion de Radio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 06-08-2015
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 27-11-2020 Se agrega la acción para la generación de operatividad masiva
     */
    public function indexRadioAction()
    {
        $em              = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento    = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array("nombreTipoElemento" => "RADIO"));
        $rolesPermitidos = array();

        //Se agrega codigo para agregar Roles
        if(true === $this->get('security.context')->isGranted('ROLE_155-2777'))
        {
            $rolesPermitidos[] = 'ROLE_155-2777'; //Dejar Sin Operatividad
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_155-7797'))
        {
            $rolesPermitidos[] = 'ROLE_155-7797'; 
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

        return $this->render('tecnicoBundle:InfoElementoRadio:index.html.twig', array('entities' => $entities,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    public function newRadioAction(){
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoRadioType(), $entity);

        return $this->render('tecnicoBundle:InfoElementoRadio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Documentación para el método 'createRadioAction'.
     *
     * Metodo utilizado para crear la nueva Radio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-05-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @since 1.0
     */
    public function createRadioAction()
    {
        $request                = $this->get('request');
        $session                = $request->getSession();
        $peticion               = $this->get('request');
        $em                     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $elementoRadio          = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoRadioType(), $elementoRadio);
        $parametros             = $request->request->get('telconet_schemabundle_infoelementoradiotype');
        $nombreElemento         = $parametros['nombreElemento'];
        $ipRadio                = $parametros['ipElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $nodoElementoId         = $parametros['nodoElementoId'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $strMacElemento         = $parametros['macElemento'];
        $intInterfaceSwitchId   = $parametros['interfaceSwitchId'];
        $strTipoElementoRed     = $parametros['tipoElementoRed'];
        $intRadioInicioId       = $parametros['radioInicioId'];
        $strSid                 = $parametros['sid'];
        $boolMensajeUsuario     = false;
        
        $em->beginTransaction();
        try
        {
            $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')
                               ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            //verificar que no se repita la ip
            $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "ip" =>$ipRadio, "estado"=>"Activo"));
            if($ipRepetida)
            {
                $boolMensajeUsuario = true;
                throw new \Exception('Ip ya existe en otro Elemento, favor revisar!');
            }

            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneBy(array( "nombreElemento" =>$nombreElemento, "estado"=>"Activo"));
            if($elementoRepetido)
            {
                $boolMensajeUsuario = true;
                throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);

            $elementoRadio->setNombreElemento($nombreElemento);
            $elementoRadio->setDescripcionElemento($descripcionElemento);
            $elementoRadio->setModeloElementoId($modeloElemento);
            $elementoRadio->setUsrResponsable($session->get('user'));
            $elementoRadio->setUsrCreacion($session->get('user'));
            $elementoRadio->setFeCreacion(new \DateTime('now'));
            $elementoRadio->setIpCreacion($peticion->getClientIp());
            $elementoRadio->setEstado("Activo");
            $em->persist($elementoRadio);
            $em->flush();

            //buscar el interface Modelo
            $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array( "modeloElementoId" =>$modeloElementoId));
            foreach($interfaceModelo as $im)
            {
                $cantidadInterfaces = $im->getCantidadInterface();
                $formato            = $im->getFormatoInterface();

                for($i=1;$i<=$cantidadInterfaces;$i++)
                {
                    $interfaceElemento       = new InfoInterfaceElemento();
                    $format                  = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0].$i;
                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setElementoId($elementoRadio);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($peticion->getClientIp());

                    $em->persist($interfaceElemento);
                }
            }
            $em->flush();

            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            $relacionElemento->setElementoIdA($nodoElementoId);
            $relacionElemento->setElementoIdB($elementoRadio->getId());
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("nodo contiene radio");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($relacionElemento);

            //ip elemento
            $ipElemento = new InfoIp();
            $ipElemento->setElementoId($elementoRadio->getId());
            $ipElemento->setIp($ipRadio);
            $ipElemento->setVersionIp("IPV4");
            $ipElemento->setEstado("Activo");
            $ipElemento->setUsrCreacion($session->get('user'));
            $ipElemento->setFeCreacion(new \DateTime('now'));
            $ipElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ipElemento);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elementoRadio);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un Radio");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);

            //tomar datos nodo
            $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                               ->findOneBy(array("elementoId"=>$nodoElementoId));
            $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $nodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $nodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene a la radio ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                $boolMensajeUsuario = true;
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
            $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
            $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
            $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elementoRadio);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elementoRadio);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($peticion->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);

            //caracteristica para almacenar Mac
            $objDetalleElementoMac = new InfoDetalleElemento();
            $objDetalleElementoMac->setElementoId($elementoRadio->getId());
            $objDetalleElementoMac->setDetalleNombre("MAC");
            $objDetalleElementoMac->setDetalleValor($strMacElemento);
            $objDetalleElementoMac->setDetalleDescripcion("MAC");
            $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoMac->setUsrCreacion($session->get('user'));
            $objDetalleElementoMac->setIpCreacion($peticion->getClientIp());
            $objDetalleElementoMac->setEstado('Activo');
            $em->persist($objDetalleElementoMac);

            //caracteristica para almacenar el tipo de elemento de red
            $objDetalleElementoRed = new InfoDetalleElemento();
            $objDetalleElementoRed->setElementoId($elementoRadio->getId());
            $objDetalleElementoRed->setDetalleNombre("TIPO ELEMENTO RED");
            $objDetalleElementoRed->setDetalleValor($strTipoElementoRed);
            $objDetalleElementoRed->setDetalleDescripcion("TIPO ELEMENTO RED");
            $objDetalleElementoRed->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoRed->setUsrCreacion($session->get('user'));
            $objDetalleElementoRed->setIpCreacion($peticion->getClientIp());
            $objDetalleElementoRed->setEstado('Activo');
            $em->persist($objDetalleElementoRed);
            
            //caracteristica para almacenar el sid del elemento
            $objDetalleElementoSid = new InfoDetalleElemento();
            $objDetalleElementoSid->setElementoId($elementoRadio->getId());
            $objDetalleElementoSid->setDetalleNombre("SID");
            $objDetalleElementoSid->setDetalleValor($strSid);
            $objDetalleElementoSid->setDetalleDescripcion("SID");
            $objDetalleElementoSid->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoSid->setUsrCreacion($session->get('user'));
            $objDetalleElementoSid->setIpCreacion($peticion->getClientIp());
            $objDetalleElementoSid->setEstado('Activo');
            $em->persist($objDetalleElementoSid);
            
            if ($strTipoElementoRed == 'BACKBONE')
            {
                //obtener Interface Elemento Ini Id
                $objInterfaceElementoIni = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($intInterfaceSwitchId);

                $objInterfaceElementoIni->setEstado('connected');
                $em->persist($objInterfaceElementoIni);

                //obtener Interface Elemento Fin Id
                $objInterfaceElementoFin = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                              ->findOneBy(array("elementoId"              => $elementoRadio->getId(),
                                                                "nombreInterfaceElemento" => "wlan1",
                                                                "estado"                  => "not connect"));

                $objInterfaceElementoFin->setEstado('connected');
                $em->persist($objInterfaceElementoFin);

                $objInfoEnlace = new InfoEnlace();
                $objInfoEnlace->setInterfaceElementoIniId($objInterfaceElementoIni);
                $objInfoEnlace->setInterfaceElementoFinId($objInterfaceElementoFin);
                $objInfoEnlace->setTipoMedioId($objTipoMedio);
                $objInfoEnlace->setTipoEnlace("PRINCIPAL");

                $objInfoEnlace->setCapacidadInput(1);
                $objInfoEnlace->setCapacidadOutput(1);
                $objInfoEnlace->setUnidadMedidaInput("mbps");
                $objInfoEnlace->setUnidadMedidaOutput("mbps");

                $objInfoEnlace->setCapacidadIniFin(1);
                $objInfoEnlace->setCapacidadFinIni(1);
                $objInfoEnlace->setUnidadMedidaUp("mbps");
                $objInfoEnlace->setUnidadMedidaDown("mbps");
                $objInfoEnlace->setEstado("Activo");
                $objInfoEnlace->setUsrCreacion($session->get('user'));
                $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                $objInfoEnlace->setIpCreacion($peticion->getClientIp());
                $em->persist($objInfoEnlace);
                $em->flush();
            }
            else
            {
                //obtener Interface Elemento Fin Id
                $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                   "nombreInterfaceElemento" => "esp1",
                                                                   "estado"                  => "not connect"));
                if (!$objInterfaceElementoIniRep)
                {
                    //obtener Interface Elemento Fin Id
                    $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                       "nombreInterfaceElemento" => "esp1",
                                                                       "estado"                  => "connected"));
                }
                else
                {
                    $objInterfaceElementoIniRep->setEstado('connected');
                    $em->persist($objInterfaceElementoIniRep);
                }
                //obtener Interface Elemento Fin Id
                $objInterfaceElementoFinRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->findOneBy(array("elementoId"              => $elementoRadio->getId(),
                                                                   "nombreInterfaceElemento" => "esp1",
                                                                   "estado"                  => "not connect"));
                
                $objInterfaceElementoFinRep->setEstado('connected');
                $em->persist($objInterfaceElementoFinRep);
                    
                $objInfoEnlaceRep = new InfoEnlace();
                $objInfoEnlaceRep->setInterfaceElementoIniId($objInterfaceElementoIniRep);
                $objInfoEnlaceRep->setInterfaceElementoFinId($objInterfaceElementoFinRep);
                $objInfoEnlaceRep->setTipoMedioId($objTipoMedio);
                $objInfoEnlaceRep->setTipoEnlace("PRINCIPAL");

                $objInfoEnlaceRep->setCapacidadInput(1);
                $objInfoEnlaceRep->setCapacidadOutput(1);
                $objInfoEnlaceRep->setUnidadMedidaInput("mbps");
                $objInfoEnlaceRep->setUnidadMedidaOutput("mbps");

                $objInfoEnlaceRep->setCapacidadIniFin(1);
                $objInfoEnlaceRep->setCapacidadFinIni(1);
                $objInfoEnlaceRep->setUnidadMedidaUp("mbps");
                $objInfoEnlaceRep->setUnidadMedidaDown("mbps");
                $objInfoEnlaceRep->setEstado("Activo");
                $objInfoEnlaceRep->setUsrCreacion($session->get('user'));
                $objInfoEnlaceRep->setFeCreacion(new \DateTime('now'));
                $objInfoEnlaceRep->setIpCreacion($peticion->getClientIp());
                $em->persist($objInfoEnlaceRep);
                $em->flush();
            }
            
            $em->flush();
            $em->commit();

            return $this->redirect($this->generateUrl('elementoradio_showRadio', array('id' => $elementoRadio->getId())));
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            error_log("Error: ".$e->getMessage());
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementoradio_newRadio'));
        }
    }
    
    /**
     * Documentación para el método 'editRadioAction'.
     *
     * Metodo utilizado para retornar a la pagina de edición de una Radio 
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-05-2016
     * 
     * @since 1.0
     */
    public function editRadioAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -radio- que se quiere modificar');
        }
        else
        {
            $ipElemento         = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "elementoId" => $elemento->getId(),
                                                                                              "estado"     => "Activo"));
            $nodoElemento       = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdB" => $elemento->getId(), 
                                                                                                            "estado"      => "Activo"));
            $elementoUbica      = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneBy(array("elementoId" => $elemento->getId()));
            $ubicacion          = $em->getRepository('schemaBundle:InfoUbicacion')->findOneBy(array("id" => $elementoUbica->getUbicacionId()));
            $parroquia          = $em->getRepository('schemaBundle:AdmiParroquia')->findOneBy(array("id" => $ubicacion->getParroquiaId()));
            $canton             = $em->getRepository('schemaBundle:AdmiCanton')->findOneBy(array("id" => $parroquia->getCantonId()));
            $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')->findOneBy(array("cantonId" => $canton->getId()));
            if($nodoElemento)
            {
                $objElementoNodo    = $em->getRepository('schemaBundle:InfoElemento')->find($nodoElemento->getElementoIdA());
                $elemento->setNodoElementoId($nodoElemento->getElementoIdA());
            }
            $objDetElementoMac  = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $elemento->getId(),
                                                                                                           "detalleNombre" => "MAC"));
            if ($ipElemento)
            {
                $elemento->setIpElemento($ipElemento->getIp());
            }
            
            if($objDetElementoMac)
            {
                $elemento->setMacElemento($objDetElementoMac->getDetalleValor());
            }
            
            $objDetElementoSid  = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $elemento->getId(),
                                                                                                           "detalleNombre" => "SID"));
            
            if($objDetElementoSid)
            {
                $elemento->setSid($objDetElementoSid->getDetalleValor());
            }
            
            $objDetElementoTep  = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $elemento->getId(),
                                                                                                           "detalleNombre" => "TIPO ELEMENTO RED"));
            
            if($objDetElementoTep)
            {
                $elemento->setTipoElementoRed($objDetElementoTep->getDetalleValor());
            }
            
            $objInterfaceElemento  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                        ->findOneBy(array( "elementoId"              => $elemento->getId(),
                                                           "nombreInterfaceElemento" => "wlan1",
                                                           "estado"                  => "connected"));
            //recuper switch padre de elemento
            if($objInterfaceElemento)
            {
                $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                    ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElemento->getId(), 
                                                       "estado"                 => "Activo"));
                if ($objInfoEnlace)
                {
                    $elemento->setSwitchElementoId($objInfoEnlace->getInterfaceElementoIniId()->getElementoId()->getId());
                    $elemento->setInterfaceSwitchId($objInfoEnlace->getInterfaceElementoIniId()->getId());
                }
            }
            //recuper radio padre de elemento repetidora
            else
            {
                $objInterfaceElemento  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findOneBy(array( "elementoId"              => $elemento->getId(),
                                                               "nombreInterfaceElemento" => "esp1",
                                                               "estado"                  => "connected"));
                if($objInterfaceElemento)
                {
                    $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                        ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElemento->getId(), 
                                                           "estado"                 => "Activo"));
                    $elemento->setRadioInicioId($objInfoEnlace->getInterfaceElementoIniId()->getElementoId()->getId());
                    $objInfoEnlace = null;
                }
            }
        }

        $formulario =$this->createForm(new InfoElementoRadioType(), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoRadio:edit.html.twig', 
                                array(
                                        'edit_form'             => $formulario->createView(),
                                        'radio'                 => $elemento,
                                        'ipElemento'            => $ipElemento,
                                        'ubicacion'             => $ubicacion,
                                        'cantonJurisdiccion'    => $cantonJurisdiccion,
                                        'nodoElemento'          => $nodoElemento?$nodoElemento:null,
                                        'nombreNodo'            => $objElementoNodo?$objElementoNodo->getNombreElemento():null,
                                        'nombreSwitch'          => $objInfoEnlace?$objInfoEnlace->getInterfaceElementoIniId()
                                                                                                ->getElementoId()
                                                                                                ->getNombreElemento():null,
                                        'nombreInterfaceSwitch' => $objInfoEnlace?$objInfoEnlace->getInterfaceElementoIniId()
                                                                                                ->getNombreInterfaceElemento():null
                                     )
                            );
    }
    
    /**
     * Documentación para el método 'updateRadioAction'.
     *
     * Metodo utilizado para actualizar la Radio
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-05-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @since 1.0
     * 
     */
    public function updateRadioAction($id)
    {
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $request    = $this->get('request');
        $peticion   = $this->get('request');
        $session    = $request->getSession();
        $entity     = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $parametros             = $request->request->get('telconet_schemabundle_infoelementoradiotype');
        $nombreElemento         = $parametros['nombreElemento'];
        $nodoElementoId         = $parametros['nodoElementoId'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $ipElemento             = $parametros['ipElemento'];
        $strMacElemento         = $parametros['macElemento'];
        $intInterfaceSwitchId   = $parametros['interfaceSwitchId'];
        $strTipoElementoRed     = $parametros['tipoElementoRed'];
        $intRadioInicioId       = $parametros['radioInicioId'];
        $strSid                 = $parametros['sid'];
        $ipElementoId           = $request->request->get('idIpElemento');
        $idUbicacion            = $request->request->get('idUbicacion');
        $modeloElemento         = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
        $ipElementoObj          = $em->getRepository('schemaBundle:InfoIp')->find($ipElementoId);
        $strTipoElementoRedAnt  = '';
        $boolMensajeUsuario     = false;
        
        $em->beginTransaction();
        try
        {
            $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')
                               ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            //verificar que no se repita la ip
            if ($ipElementoObj->getIp() != $ipElemento)
            {
                $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array( "ip" =>$ipElemento, "estado"=>"Activo"));
                if($ipRepetida)
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('Ip ya existe en otro Elemento, favor revisar!');
                }
            }

            if ($entity->getNombreElemento() != $nombreElemento)
            {
                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array( "nombreElemento" =>$nombreElemento, "estado"=>"Activo"));
                if($elementoRepetido)
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                }
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

            $relacionElemento     = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdB" => $entity,
                                                                                                              "estado"      => "Activo"));
            //ver si se cambio de pop
            $nodoElementoAnterior = $relacionElemento->getElementoIdA();

            if($nodoElementoAnterior != $nodoElementoId)
            {
                //cambiar la relacion elemento
                $relacionElemento->setElementoIdA($nodoElementoId);
                $relacionElemento->setUsrCreacion($session->get('user'));
                $relacionElemento->setFeCreacion(new \DateTime('now'));
                $relacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($relacionElemento);

                //tomar datos del nuevo pop
                $popEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                  ->findOneBy(array("elementoId"=>$nodoElementoId));
                $nodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                  ->find($popEmpresaElementoUbicacion->getUbicacionId()->getId());
                
                
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $nodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $nodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene a la radio ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
                if($arrayRespuestaCoordenadas["status"] === "ERROR")
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                }
                //cambiar ubicacion de la radio
                $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
                $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
                $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
                $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
                $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
                $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
                $ubicacionElemento->setParroquiaId($parroquia);
                $ubicacionElemento->setUsrCreacion($session->get('user'));
                $ubicacionElemento->setFeCreacion(new \DateTime('now'));
                $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ubicacionElemento);
            }

            //ip elemento
            $ipElementoObj->setIp(trim($ipElemento));
            $ipElementoObj->setUsrCreacion($session->get('user'));
            $ipElementoObj->setFeCreacion(new \DateTime('now'));
            $ipElementoObj->setIpCreacion($peticion->getClientIp());
            $em->persist($ipElementoObj);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Modificado");
            $historialElemento->setObservacion("Se modifico un Radio");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);
            
            //detalle elemento mac
            $objDetalleElementoMac     = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $entity->getId(),
                                                                                                                  "detalleNombre" => "MAC"));
            if ($objDetalleElementoMac)
            {
                $objDetalleElementoMac->setDetalleValor($strMacElemento);
                $em->persist($objDetalleElementoMac);
            }
            else
            {
                //caracteristica para almacenar Mac
                $objDetalleElementoMac = new InfoDetalleElemento();
                $objDetalleElementoMac->setElementoId($entity->getId());
                $objDetalleElementoMac->setDetalleNombre("MAC");
                $objDetalleElementoMac->setDetalleValor($strMacElemento);
                $objDetalleElementoMac->setDetalleDescripcion("MAC");
                $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoMac->setUsrCreacion($session->get('user'));
                $objDetalleElementoMac->setIpCreacion($peticion->getClientIp());
                $objDetalleElementoMac->setEstado('Activo');
                $em->persist($objDetalleElementoMac);
            }

            //detalle elemento tipo elemento red
            $objDetalleElementoRed     = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array("elementoId"    => $entity->getId(),
                                                                                                                 "detalleNombre" => "TIPO ELEMENTO RED"));
            if ($objDetalleElementoRed)
            {
                $strTipoElementoRedAnt = $objDetalleElementoRed->getDetalleValor();
                $objDetalleElementoRed->setDetalleValor($strTipoElementoRed);
                $em->persist($objDetalleElementoRed);
            }
            else
            {
                $strTipoElementoRedAnt = '';
                //caracteristica para almacenar el tipo de elemento de red
                $objDetalleElementoRed = new InfoDetalleElemento();
                $objDetalleElementoRed->setElementoId($entity->getId());
                $objDetalleElementoRed->setDetalleNombre("TIPO ELEMENTO RED");
                $objDetalleElementoRed->setDetalleValor($strTipoElementoRed);
                $objDetalleElementoRed->setDetalleDescripcion("TIPO ELEMENTO RED");
                $objDetalleElementoRed->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoRed->setUsrCreacion($session->get('user'));
                $objDetalleElementoRed->setIpCreacion($peticion->getClientIp());
                $objDetalleElementoRed->setEstado('Activo');
                $em->persist($objDetalleElementoRed);
            }
            
            //detalle elemento tipo elemento red
            $objDetalleElementoSid     = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array("elementoId"    => $entity->getId(),
                                                                                                                 "detalleNombre" => "SID"));
            if ($objDetalleElementoSid)
            {
                $objDetalleElementoSid->setDetalleValor($strSid);
                $em->persist($objDetalleElementoSid);
            }
            else
            {
                 //caracteristica para almacenar el sid del elemento
                $objDetalleElementoSid = new InfoDetalleElemento();
                $objDetalleElementoSid->setElementoId($entity->getId());
                $objDetalleElementoSid->setDetalleNombre("SID");
                $objDetalleElementoSid->setDetalleValor($strSid);
                $objDetalleElementoSid->setDetalleDescripcion("SID");
                $objDetalleElementoSid->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoSid->setUsrCreacion($session->get('user'));
                $objDetalleElementoSid->setIpCreacion($peticion->getClientIp());
                $objDetalleElementoSid->setEstado('Activo');
                $em->persist($objDetalleElementoSid);
            }
            if ($strTipoElementoRedAnt != '')
            {
                if ($strTipoElementoRedAnt == 'BACKBONE')
                {
                    $objInterfaceElementoRadio  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->findOneBy(array( "elementoId"              => $entity->getId(),
                                                                            "nombreInterfaceElemento" => "wlan1",
                                                                            "estado"                  => "connected"));
                    if($objInterfaceElementoRadio)
                    {
                        $objInfoEnlaceAnt = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElementoRadio->getId(), 
                                                               "estado"                 => "Activo"));
                    }
                    if ($strTipoElementoRed == 'BACKBONE')
                    {
                        if ($objInfoEnlaceAnt)
                        {
                            if ($objInfoEnlaceAnt->getInterfaceElementoIniId()->getId() != $intInterfaceSwitchId)
                            {
                                //eliminar enlace a switch anterior
                                $objInfoEnlaceAnt->setEstado("Eliminado"); 
                                $em->persist($objInfoEnlaceAnt);
                                $em->flush();

                                //liberar interface switch
                                $objInterfaceElementoSwitchAnt = $objInfoEnlaceAnt->getInterfaceElementoIniId();
                                $objInterfaceElementoSwitchAnt->setEstado("not connect");
                                $em->persist($objInterfaceElementoSwitchAnt);
                                $em->flush();

                                $objInterfaceElementoSwitch  = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($intInterfaceSwitchId);

                                $objInfoEnlace = new InfoEnlace();
                                $objInfoEnlace->setInterfaceElementoIniId($objInterfaceElementoSwitch);
                                $objInfoEnlace->setInterfaceElementoFinId($objInterfaceElementoRadio);
                                $objInfoEnlace->setTipoMedioId($objTipoMedio);
                                $objInfoEnlace->setTipoEnlace("PRINCIPAL");

                                $objInfoEnlace->setCapacidadInput(1);
                                $objInfoEnlace->setCapacidadOutput(1);
                                $objInfoEnlace->setUnidadMedidaInput("mbps");
                                $objInfoEnlace->setUnidadMedidaOutput("mbps");

                                $objInfoEnlace->setCapacidadIniFin(1);
                                $objInfoEnlace->setCapacidadFinIni(1);
                                $objInfoEnlace->setUnidadMedidaUp("mbps");
                                $objInfoEnlace->setUnidadMedidaDown("mbps");
                                $objInfoEnlace->setEstado("Activo");
                                $objInfoEnlace->setUsrCreacion($session->get('user'));
                                $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                                $objInfoEnlace->setIpCreacion($peticion->getClientIp());
                                $em->persist($objInfoEnlace);
                                $em->flush();

                                $objInterfaceElementoSwitch->setEstado("connected");
                                $em->persist($objInterfaceElementoSwitch);
                                $em->flush();
                            }
                        }
                    }
                    else
                    {
                        if ($objInfoEnlaceAnt)
                        {
                            //eliminar enlace a switch anterior
                            $objInfoEnlaceAnt->setEstado("Eliminado"); 
                            $em->persist($objInfoEnlaceAnt);
                            $em->flush();

                            //liberar interface switch
                            $objInterfaceElementoSwitchAnt = $objInfoEnlaceAnt->getInterfaceElementoIniId();
                            $objInterfaceElementoSwitchAnt->setEstado("not connect");
                            $em->persist($objInterfaceElementoSwitchAnt);
                            $em->flush();
                            
                            //liberar interface switch
                            $objInterfaceElementoEleFinAnt = $objInfoEnlaceAnt->getInterfaceElementoFinId();
                            $objInterfaceElementoEleFinAnt->setEstado("not connect");
                            $em->persist($objInterfaceElementoEleFinAnt);
                            $em->flush();
                            
                            //obtener Interface Elemento Fin Id
                            $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                               "nombreInterfaceElemento" => "esp1",
                                                                               "estado"                  => "not connect"));
                            if (!$objInterfaceElementoIniRep)
                            {
                                //obtener Interface Elemento Fin Id
                                $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                                   "nombreInterfaceElemento" => "esp1",
                                                                                   "estado"                  => "connected"));
                            }
                            else
                            {
                                $objInterfaceElementoIniRep->setEstado('connected');
                                $em->persist($objInterfaceElementoIniRep);
                            }
                            
                            //obtener Interface Elemento Fin Id
                            $objInterfaceElementoFinRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findOneBy(array("elementoId"              => $entity->getId(),
                                                                               "nombreInterfaceElemento" => "esp1",
                                                                               "estado"                  => "not connect"));
                            if (!$objInterfaceElementoFinRep)
                            {
                                //obtener Interface Elemento Fin Id
                                $objInterfaceElementoFinRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->findOneBy(array("elementoId"              => $entity->getId(),
                                                                                   "nombreInterfaceElemento" => "esp1",
                                                                                   "estado"                  => "connected"));
                            }
                            else
                            {
                                $objInterfaceElementoFinRep->setEstado('connected');
                                $em->persist($objInterfaceElementoFinRep);
                            }

                            $objInfoEnlace = new InfoEnlace();
                            $objInfoEnlace->setInterfaceElementoIniId($objInterfaceElementoIniRep);
                            $objInfoEnlace->setInterfaceElementoFinId($objInterfaceElementoFinRep);
                            $objInfoEnlace->setTipoMedioId($objTipoMedio);
                            $objInfoEnlace->setTipoEnlace("PRINCIPAL");

                            $objInfoEnlace->setCapacidadInput(1);
                            $objInfoEnlace->setCapacidadOutput(1);
                            $objInfoEnlace->setUnidadMedidaInput("mbps");
                            $objInfoEnlace->setUnidadMedidaOutput("mbps");

                            $objInfoEnlace->setCapacidadIniFin(1);
                            $objInfoEnlace->setCapacidadFinIni(1);
                            $objInfoEnlace->setUnidadMedidaUp("mbps");
                            $objInfoEnlace->setUnidadMedidaDown("mbps");
                            $objInfoEnlace->setEstado("Activo");
                            $objInfoEnlace->setUsrCreacion($session->get('user'));
                            $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                            $objInfoEnlace->setIpCreacion($peticion->getClientIp());
                            $em->persist($objInfoEnlace);
                            $em->flush();
                        }
                    }
                }
                else
                {
                    $objInterfaceElementoRadio  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array( "elementoId"              => $entity->getId(),
                                                                        "nombreInterfaceElemento" => "esp1",
                                                                        "estado"                  => "connected"));
                    if($objInterfaceElementoRadio)
                    {
                        $objInfoEnlaceAnt = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElementoRadio->getId(), 
                                                               "estado"                 => "Activo"));
                    }
                    if ($strTipoElementoRed == 'BACKBONE')
                    {
                        if ($objInfoEnlaceAnt)
                        {
                            //eliminar enlace a switch anterior
                            $objInfoEnlaceAnt->setEstado("Eliminado"); 
                            $em->persist($objInfoEnlaceAnt);
                            $em->flush();

                            //liberar interface switch
                            $objInterfaceElementoEleIniAnt = $objInfoEnlaceAnt->getInterfaceElementoIniId();
                            $objInterfaceElementoEleIniAnt->setEstado("not connect");
                            $em->persist($objInterfaceElementoEleIniAnt);
                            $em->flush();
                            
                            //liberar interface switch
                            $objInterfaceElementoEleFinAnt = $objInfoEnlaceAnt->getInterfaceElementoFinId();
                            $objInterfaceElementoEleFinAnt->setEstado("not connect");
                            $em->persist($objInterfaceElementoEleFinAnt);
                            $em->flush();
                            
                            //obtener Interface Elemento Ini Id
                            $objInterfaceElementoIni = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($intInterfaceSwitchId);

                            $objInterfaceElementoIni->setEstado('connected');
                            $em->persist($objInterfaceElementoIni);
                            
                            //obtener Interface Elemento Fin Id
                            $objInterfaceElementoFinRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findOneBy(array("elementoId"              => $entity->getId(),
                                                                               "nombreInterfaceElemento" => "wlan1",
                                                                               "estado"                  => "not connect"));
                            if (!$objInterfaceElementoFinRep)
                            {
                                //obtener Interface Elemento Fin Id
                                $objInterfaceElementoFinRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->findOneBy(array("elementoId"              => $entity->getId(),
                                                                                   "nombreInterfaceElemento" => "wlan1",
                                                                                   "estado"                  => "connected"));
                            }
                            else
                            {
                                $objInterfaceElementoFinRep->setEstado('connected');
                                $em->persist($objInterfaceElementoFinRep);
                            }

                            $objInfoEnlace = new InfoEnlace();
                            $objInfoEnlace->setInterfaceElementoIniId($objInterfaceElementoIni);
                            $objInfoEnlace->setInterfaceElementoFinId($objInterfaceElementoFinRep);
                            $objInfoEnlace->setTipoMedioId($objTipoMedio);
                            $objInfoEnlace->setTipoEnlace("PRINCIPAL");

                            $objInfoEnlace->setCapacidadInput(1);
                            $objInfoEnlace->setCapacidadOutput(1);
                            $objInfoEnlace->setUnidadMedidaInput("mbps");
                            $objInfoEnlace->setUnidadMedidaOutput("mbps");

                            $objInfoEnlace->setCapacidadIniFin(1);
                            $objInfoEnlace->setCapacidadFinIni(1);
                            $objInfoEnlace->setUnidadMedidaUp("mbps");
                            $objInfoEnlace->setUnidadMedidaDown("mbps");
                            $objInfoEnlace->setEstado("Activo");
                            $objInfoEnlace->setUsrCreacion($session->get('user'));
                            $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                            $objInfoEnlace->setIpCreacion($peticion->getClientIp());
                            $em->persist($objInfoEnlace);
                            $em->flush();
                        }
                    }
                    else
                    {
                        //obtener Interface Elemento Fin Id
                        $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                           "nombreInterfaceElemento" => "esp1",
                                                                           "estado"                  => "not connect"));
                        if (!$objInterfaceElementoIniRep)
                        {
                            //obtener Interface Elemento Fin Id
                            $objInterfaceElementoIniRep = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findOneBy(array("elementoId"              => $intRadioInicioId,
                                                                               "nombreInterfaceElemento" => "esp1",
                                                                               "estado"                  => "connected"));
                        }
                        
                        if ($objInfoEnlaceAnt)
                        {
                            if ($objInfoEnlaceAnt->getInterfaceElementoIniId()->getId() != $objInterfaceElementoIniRep->getId())
                            {
                                //eliminar enlace a switch anterior
                                $objInfoEnlaceAnt->setEstado("Eliminado"); 
                                $em->persist($objInfoEnlaceAnt);
                                $em->flush();

                                //liberar interface switch
                                $objInterfaceElementoIniEleAnt = $objInfoEnlaceAnt->getInterfaceElementoIniId();
                                $objInterfaceElementoIniEleAnt->setEstado("not connect");
                                $em->persist($objInterfaceElementoIniEleAnt);
                                $em->flush();

                                $objInfoEnlace = new InfoEnlace();
                                $objInfoEnlace->setInterfaceElementoIniId($objInterfaceElementoIniRep);
                                $objInfoEnlace->setInterfaceElementoFinId($objInterfaceElementoRadio);
                                $objInfoEnlace->setTipoMedioId($objTipoMedio);
                                $objInfoEnlace->setTipoEnlace("PRINCIPAL");

                                $objInfoEnlace->setCapacidadInput(1);
                                $objInfoEnlace->setCapacidadOutput(1);
                                $objInfoEnlace->setUnidadMedidaInput("mbps");
                                $objInfoEnlace->setUnidadMedidaOutput("mbps");

                                $objInfoEnlace->setCapacidadIniFin(1);
                                $objInfoEnlace->setCapacidadFinIni(1);
                                $objInfoEnlace->setUnidadMedidaUp("mbps");
                                $objInfoEnlace->setUnidadMedidaDown("mbps");
                                $objInfoEnlace->setEstado("Activo");
                                $objInfoEnlace->setUsrCreacion($session->get('user'));
                                $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                                $objInfoEnlace->setIpCreacion($peticion->getClientIp());
                                $em->persist($objInfoEnlace);
                                $em->flush();

                                $objInterfaceElementoIniRep->setEstado("connected");
                                $em->persist($objInterfaceElementoIniRep);
                                $em->flush();
                            }
                        }
                    }
                }
            }

            $em->flush();
            $em->commit();

            return $this->redirect($this->generateUrl('elementoradio_showRadio', array('id' => $entity->getId())));

        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            error_log("Error: ".$e->getMessage());
            if($boolMensajeUsuario)
            {
                $strMensajeError = $e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementoradio_editRadio', array('id' => $id)));
        } 
    }
    
    /*
     * 
     * Documentación para el método 'deleteRadioAction'.
     *
     * Metodo utilizado para eliminar Radio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 31-07-2015
     */
    public function deleteRadioAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request   = $this->getRequest();
        $peticion  = $this->get('request');
        $session   = $request->getSession();

        $em        = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC       = $this->getDoctrine()->getManager('telconet');
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

            if(!$entity)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }

            //detalle elemento tipo elemento red
            $objDetalleElementoRed     = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array("elementoId"    => $entity->getId(),
                                                              "detalleNombre" => "TIPO ELEMENTO RED"));
            if ($objDetalleElementoRed)
            {
                $strTipoElementoRed = $objDetalleElementoRed->getDetalleValor();
                if ($strTipoElementoRed == 'BACKBONE')
                {
                    $objServiciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findBy(array("elementoConectorId" => $entity->getId()));
                    for($i = 0; $i < count($objServiciosTec); $i++)
                    {
                        $intServicioId = $objServiciosTec[$i]->getServicioId()->getId();
                        $objServicio   = $emC->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                        $strEstadoServ = $objServicio->getEstado();

                        if($strEstadoServ == "Activo")
                        {
                            return $respuesta->setContent("SERVICIOS ACTIVOS");
                        }
                    }
                    
                    $objInterfaceElementoRadioEsp  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array( "elementoId"              => $entity->getId(),
                                                                        "nombreInterfaceElemento" => "esp1",
                                                                        "estado"                  => "connected"));
                    if($objInterfaceElementoRadioEsp)
                    {
                        $objInfoEnlaceEsp = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array( "interfaceElementoIniId" => $objInterfaceElementoRadioEsp->getId(), 
                                                               "estado"                 => "Activo"));
                        if ($objInfoEnlaceEsp)
                        {
                           return $respuesta->setContent("ENLACES ACTIVOS");                        
                        }
                    }
                    
                    $objInterfaceElementoRadio  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array( "elementoId"              => $entity->getId(),
                                                                        "nombreInterfaceElemento" => "wlan1",
                                                                        "estado"                  => "connected"));
                    if($objInterfaceElementoRadio)
                    {
                        $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElementoRadio->getId(), 
                                                               "estado"                 => "Activo"));
                        if ($objInfoEnlace)
                        {
                            //eliminar enlace a switch anterior
                            $objInfoEnlace->setEstado("Eliminado"); 
                            $em->persist($objInfoEnlace);
                            $em->flush();
                            
                            $objInterfaceElementoSwitch = $objInfoEnlace->getInterfaceElementoIniId();
                            //eliminar enlace a switch anterior
                            $objInterfaceElementoSwitch->setEstado("not connect"); 
                            $em->persist($objInterfaceElementoSwitch);
                            $em->flush();                            
                        }
                    }
                }
                else
                {
                    $objInterfaceElementoRadio  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array( "elementoId"              => $entity->getId(),
                                                                        "nombreInterfaceElemento" => "esp1",
                                                                        "estado"                  => "connected"));
                    if($objInterfaceElementoRadio)
                    {
                        $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array( "interfaceElementoIniId" => $objInterfaceElementoRadio->getId(), 
                                                               "estado"                 => "Activo"));
                        if ($objInfoEnlace)
                        {
                            if ($strTipoElementoRed == 'REPETIDORA IN')
                            {
                                return $respuesta->setContent("ENLACES ACTIVOS");                        
                            }
                            else
                            {
                                $objServiciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')
                                                       ->findBy(array("elementoClienteId" => $objInfoEnlace->getInterfaceElementoFinId()
                                                                                                           ->getElementoId()->getId()));
                                for($i = 0; $i < count($objServiciosTec); $i++)
                                {
                                    $intServicioId = $objServiciosTec[$i]->getServicioId()->getId();
                                    $objServicio   = $emC->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                                    $strEstadoServ = $objServicio->getEstado();
                                    if($strEstadoServ == "Activo")
                                    {
                                        return $respuesta->setContent("SERVICIOS ACTIVOS");
                                    }
                                }
                            }
                            //eliminar enlace a switch anterior
                            $objInfoEnlace->setEstado("Eliminado"); 
                            $em->persist($objInfoEnlace);
                            $em->flush();
                        }
                    }
                }
            }
            else
            {
                $serviciosTec = $emC->getRepository('schemaBundle:InfoServicioTecnico')->findBy(array("elementoId" => $entity->getId()));
                for($i = 0; $i < count($serviciosTec); $i++)
                {
                    $servicioId = $serviciosTec[$i]->getServicioId()->getId();
                    $servicio   = $emC->getRepository('schemaBundle:InfoServicio')->find($servicioId);
                    $estadoServ = $servicio->getEstado();

                    if($estadoServ == "Activo")
                    {
                        return $respuesta->setContent("SERVICIOS ACTIVOS");
                    }
                }
            }

            //elemento

            $entity->setUsrCreacion($session->get('user'));
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setEstado("Eliminado");
            $entity->setIpCreacion($peticion->getClientIp());
            $em->persist($entity);

            //ip
            $ip = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("elementoId" => $entity->getId()));
            $ip->setEstado("Eliminado");
            $em->persist($ip);

            //interfaces
            $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $entity->getId()));
            for($i = 0; $i < count($interfaceElemento); $i++)
            {
                $interface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElemento[$i]->getId());
                $interface->setEstado("Eliminado");
                $em->persist($interface);
            }

            //relacion elemento
            $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array("elementoIdB" => $entity));
            $relacionElemento->setEstado("Eliminado");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($relacionElemento);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Eliminado");
            $historialElemento->setObservacion("Se elimino un Radio");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($historialElemento);

            /* Info detelle elemento -> RADIO OPERATIVO
             * Se agrega detalle elemento para agregar registro de validacion de Operatividad de RADIO
             */
            $objDetalleElemento = new InfoDetalleElemento();
            $objDetalleElemento->setElementoId($entity->getId());
            $objDetalleElemento->setDetalleNombre("RADIO OPERATIVO");
            $objDetalleElemento->setDetalleValor("NO");
            $objDetalleElemento->setDetalleDescripcion("RADIO OPERATIVO");
            $objDetalleElemento->setUsrCreacion($session->get('user'));
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objDetalleElemento);

            $em->flush();
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('elementoradio'));
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

    /*
     * 
     * Documentación para el método 'deleteAjaxRadioAction'.
     *
     * Metodo utilizado para eliminar Elementos Radio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 31-07-2015
     */
    public function deleteAjaxRadioAction()
    {
        $respuesta   = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion    = $this->get('request');
        $parametro   = $peticion->get('param');
        $em          = $this->getDoctrine()->getManager("telconet_infraestructura");
        $array_valor = explode("|", $parametro);
        
        foreach($array_valor as $id):
            if(null == $entity = $em->find('schemaBundle:InfoElemento', $id))
            {
                $respuesta->setContent("No existe la entidad");
            }
            else
            {
                //elemento
                $entity->setUsrCreacion($session->get('user'));
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setIpCreacion("1.1.1.1");
                $em->persist($entity);

                //interfaces
                $interfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $entity->getId()));
                for($i = 0; $i < count($interfaceElemento); $i++)
                {
                    $interface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElemento[$i]->getId());
                    $interface->setEstado("Eliminado");
                    $em->persist($interface);
                }

                //relacion elemento
                $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elmentoIdB" => $entity));
                $relacionElemento[0]->setEstado("Eliminado");
                $relacionElemento[0]->setUsrCreacion($session->get('user'));
                $relacionElemento[0]->setFeCreacion(new \DateTime('now'));
                $relacionElemento[0]->setIpCreacion("0.0.0.0");
                $em->persist($relacionElemento[0]);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Eliminado");
                $historialElemento->setObservacion("Se elimino un Radio");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion("1.1.1.1");
                $em->persist($historialElemento);
                
                /* Info detelle elemento -> RADIO OPERATIVO
                 * Se agrega detalle elemento para agregar registro de validacion de Operatividad de RADIO
                 */
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($entity->getId());
                $objDetalleElemento->setDetalleNombre("RADIO OPERATIVO");
                $objDetalleElemento->setDetalleValor("NO");
                $objDetalleElemento->setDetalleDescripcion("RADIO OPERATIVO");
                $objDetalleElemento->setUsrCreacion($session->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objDetalleElemento);

                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);

        return $respuesta;
    }

     /*
     * 
     * Documentación para el método 'showRadioAction'.
     *
     * Metodo utilizado para mostrar información de Elementos Radio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 03-06-2016
     * 
     * @since 1.0
     */
    public function showRadioAction($id){
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');
        
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
            
            $objDetElementoMac  = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $elemento->getId(),
                                                                                                           "detalleNombre" => "MAC"));
            if ($objDetElementoMac)
            {
                $elemento->setMacElemento($objDetElementoMac->getDetalleValor());
            }
            
             $objDetElementoSid  = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array( "elementoId"    => $elemento->getId(),
                                                                                                           "detalleNombre" => "SID"));
            
            if($objDetElementoSid)
            {
                $elemento->setSid($objDetElementoSid->getDetalleValor());
            }
            
            $objInterfaceElemento  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                        ->findOneBy(array( "elementoId"              => $elemento->getId(),
                                                           "nombreInterfaceElemento" => "wlan1",
                                                           "estado"                  => "connected"));
            if($objInterfaceElemento)
            {
                $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                    ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElemento->getId(), 
                                                       "estado"                 => "Activo"));
                if ($objInfoEnlace)
                {
                    $elemento->setSwitchElementoId($objInfoEnlace->getInterfaceElementoIniId()->getElementoId()->getNombreElemento());
                    $elemento->setInterfaceSwitchId($objInfoEnlace->getInterfaceElementoIniId()->getNombreInterfaceElemento());
                }
            }
            //recuper radio padre de elemento repetidora
            else
            {
                $objInterfaceElemento  = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findOneBy(array( "elementoId"              => $elemento->getId(),
                                                               "nombreInterfaceElemento" => "esp1",
                                                               "estado"                  => "connected"));
                if($objInterfaceElemento)
                {
                    $objInfoEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                        ->findOneBy(array( "interfaceElementoFinId" => $objInterfaceElemento->getId(), 
                                                           "estado"                 => "Activo"));
                    if ($objInfoEnlace)
                    {
                        $elemento->setRadioInicioId($objInfoEnlace->getInterfaceElementoIniId()->getElementoId()->getNombreElemento());
                        $objInfoEnlace = null;
                    }
                }
            }
        }

        return $this->render('tecnicoBundle:InfoElementoRadio:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    public function buscarInterfacesAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idElemento = $peticion->get('idElemento');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonInterfacesPorElemento($idElemento,"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getEncontradosRadioAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"RADIO"));
        
        $peticion = $this->get('request');
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $ipElemento = $peticion->query->get('ipElemento');
        $modeloElemento = $peticion->query->get('modeloElemento');
        $marcaElemento = $peticion->query->get('marcaElemento');
        $canton = $peticion->query->get('canton');
        $jurisdiccion = $peticion->query->get('jurisdiccion');
        $popElemento = $peticion->query->get('popElemento');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonRadios(strtoupper($nombreElemento),$ipElemento,$modeloElemento,$marcaElemento,$tipoElemento[0]->getId(),$canton,$jurisdiccion,$popElemento,$estado,$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function cargarDatosRadioAction(){
       $respuesta = new Response();
       $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idRadio = $peticion->get('idRadio');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosRadio($idRadio, $em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function mostrarMacsRadioAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSoporte = $this->get('doctrine')->getManager('telconet_soporte');
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emSeguridad = $this->get('doctrine')->getManager('telconet_seguridad');
        $emComercial = $this->get('doctrine')->getManager('telconet');
        
        $peticion = $this->get('request');
        
        $modeloElemento = $peticion->get('modelo');
        
        $modelo = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneBy(array( "nombreModeloElemento" =>$modeloElemento));
        
        
        $objJson = $this->getDoctrine()
                ->getManager("telconet_infraestructura")
                ->getRepository('schemaBundle:AdmiModeloElemento')
                ->generarJsonDocumentoPorModelo("encontrarNumbersMac".$modelo->getNombreModeloElemento(),$modelo->getId(),$emSoporte,$emComunicacion,$emSeguridad,$emInfraestructura);
            $posicion = strpos($objJson, "{");
            $respuestaDocumentoPorModelo = substr($objJson, $posicion);
            $outDocumentoPorModelo = json_decode($respuestaDocumentoPorModelo);
        
        $arr = $outDocumentoPorModelo->encontrados;
        
//        print_r($arr[0]->script);
//        die();
        
        $script = $arr[0]->script;
        $idDocumento= $arr[0]->idDocumento;
        $usuario= $arr[0]->usuario;
        $protocolo= $arr[0]->protocolo;
        $idElemento = $peticion->get('idElemento');
        
        if($script=="0"){
            $script = "NO EXISTE RELACION TAREA - ACCION";
            $jsonObj = "ERROR";
        }
        else{
            $comando = "java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_radios.jar '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$idElemento."' ''";
            error_log($comando);
//            print($comando);
//            die();
            $salida= shell_exec($comando);
            $pos = strpos($salida, "{"); 
            
            if($pos==0){
                $data=json_encode(array('status'=>'error','mensaje'=>'No se pudo conectar a la base'));
                $jsonObj= '{"total":"1","encontrados":'.$data.'}';
            }
            else{
                $jsonObj= substr($salida, $pos);
            }
            
        }
        
        return $respuesta->setContent($jsonObj."&".$script);
    }
    
    /**
     * @Secure(roles="ROLE_155-2777")
     * 
     * Documentación para el método 'quitarOperatividad'.
     *
     * Metodo utilizado para ingresar caracteristica de operatividad al elemento
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-08-2015
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 26-11-2020 Se agrega validación de que si ya existe el registro en la info_detalle_elemento, actualice el campo detalle_valor
     */
    public function quitarOperatividadAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $intIdElemento      = $peticion->get('idElemento');

        $emInfraestructura->beginTransaction();
        $emComercial->beginTransaction();

        try
        {
            //Consultamos si existe en la INFO_DETALLE_ELEMENTO
            $objDetalleElemento     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array("elementoId"    => $intIdElemento,
                                                           "detalleNombre" => "RADIO OPERATIVO"));
            if (is_object($objDetalleElemento))
            {
                $objDetalleElemento->setDetalleValor("NO");
            }
            else
            {
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($intIdElemento);
                $objDetalleElemento->setDetalleNombre("RADIO OPERATIVO");
                $objDetalleElemento->setDetalleValor("NO");
                $objDetalleElemento->setDetalleDescripcion("RADIO OPERATIVO");
                $objDetalleElemento->setUsrCreacion($session->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($peticion->getClientIp());
                $objDetalleElemento->setEstado("Activo");
            }
            
            $emInfraestructura->persist($objDetalleElemento);
            $emInfraestructura->flush();

            $strMotivoElemento = 'Se deja sin operatividad al elemento ';
            
            $objInfoElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                         ->findOneBy(array("id"    => $intIdElemento));
                
            if (!is_object($objInfoElemento))
            {
                return $respuesta->setContent("Elemento no existe");
            }
            
            $strMotivoElemento = $strMotivoElemento.$objInfoElemento->getNombreElemento();
            $objInfoHistorialElemento = new InfoHistorialElemento();
            $objInfoHistorialElemento->setElementoId($objInfoElemento);
            $objInfoHistorialElemento->setObservacion($strMotivoElemento);
            $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objInfoHistorialElemento->setUsrCreacion($session->get('user'));
            $objInfoHistorialElemento->setIpCreacion($peticion->getClientIp());
            $objInfoHistorialElemento->setEstadoElemento("Activo");
            $emInfraestructura->persist($objInfoHistorialElemento);
            $emInfraestructura->flush();
            
            //Detalle elemento tipo elemento red
            $objDetalleElementoRed     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array("elementoId"    => $intIdElemento,
                                                              "detalleNombre" => "TIPO ELEMENTO RED"));
            if ($objDetalleElementoRed)
            {
                $strTipoElementoRed = $objDetalleElementoRed->getDetalleValor();
                if ($strTipoElementoRed == 'BACKBONE')
                {
                    $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findBy(array("elementoConectorId" => $intIdElemento));
                    for($intI = 0; $intI < count($objServiciosTec); $intI++)
                    {
                        $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                        $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                        
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objInfoServicio);
                        $objServicioHist->setObservacion($strMotivoElemento);
                        $objServicioHist->setIpCreacion($peticion->getClientIp());
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setUsrCreacion($session->get('user'));
                        $objServicioHist->setEstado("Activo");
                        $emComercial->persist($objServicioHist);
                        $emComercial->flush();
                    }
                }
                else
                {
                    if ($strTipoElementoRed == 'CLIENTE')
                    {
                        $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findBy(array("elementoClienteId" => $intIdElemento));
                        for($intI = 0; $intI < count($objServiciosTec); $intI++)
                        {
                            $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                            
                            $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objInfoServicio);
                            $objServicioHist->setObservacion($strMotivoElemento);
                            $objServicioHist->setIpCreacion($peticion->getClientIp());
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setUsrCreacion($session->get('user'));
                            $objServicioHist->setEstado("Activo");
                            $emComercial->persist($objServicioHist);
                            $emComercial->flush();
                        }
                    }
                }
            }
            
            $emComercial->commit();
            $emInfraestructura->commit();
            
            return $respuesta->setContent("OK");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
                $emComercial->close();
            }
            
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
                $emInfraestructura->close();
            }
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            return $respuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }
    
    public function generaNombreRepetidoraAction()
    {
        $respuesta           = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em                  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion            = $this->get('request');
        
        $strNombreElemento   = $peticion->get('nombreElemento');
        $strtipoRepetidora   = $peticion->get('tipoRepetidora');
        
        try
        {
            if ($strtipoRepetidora == 'OUT')
            {
                $strNombreElemento = substr($strNombreElemento, 0, -5);  
            }
            
            $strGeneraNombre = 'pendiente';
            $intContador     = 1;
            while ($strGeneraNombre == 'pendiente')
            {
                $strNombreElementoGenerado = $strNombreElemento.'-'.$intContador.'-'.$strtipoRepetidora;
                $objInfoElemento = $em->getRepository('schemaBundle:InfoElemento')
                                      ->findOneBy(array( "nombreElemento" =>$strNombreElementoGenerado, "estado"=>"Activo"));
                if (!$objInfoElemento)
                {
                    $strGeneraNombre = 'generado';
                }
                $intContador = $intContador +1;
            }
        }
        catch(\Exception $e)
        {
            $strNombreElementoGenerado = "ERROR";
        }
        return $respuesta->setContent($strNombreElementoGenerado);
    }
    
    /**
     * @Secure(roles="ROLE_155-2777")
     * 
     * Documentación para el método 'agregarOperatividad'.
     *
     * Metodo utilizado para actualizar caracteristica de operatividad al elemento
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     */
    public function agregarOperatividadAction()
    {
        $objRequest   = $this->getRequest();
               
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $intIdElemento      = $objPeticion->get('idElemento');

        $emInfraestructura->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            //detalle elemento RADIO OPERATIVO
            $objDetalleElemento     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array("elementoId"    => $intIdElemento,
                                                           "detalleNombre" => "RADIO OPERATIVO"));
            if (is_object($objDetalleElemento))
            {
                $objDetalleElemento->setDetalleValor("SI");
                $objDetalleElemento->setEstado("Activo");
                $emInfraestructura->persist($objDetalleElemento);
                $emInfraestructura->flush();
                
                $objInfoElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                         ->findOneBy(array("id"    => $intIdElemento));
                
                if (!is_object($objInfoElemento))
                {
                    return $objRespuesta->setContent("Elemento no existe");
                }
                
                $strMotivoElemento = 'Se deja con operatividad al elemento ';
                $strMotivoElemento = $strMotivoElemento.$objInfoElemento->getNombreElemento();
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objInfoElemento);
                $objInfoHistorialElemento->setObservacion($strMotivoElemento);
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($strUsrSesion);
                $objInfoHistorialElemento->setIpCreacion($strIpClient);
                $objInfoHistorialElemento->setEstadoElemento("Activo");
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                
                //Detalle elemento tipo elemento red
                $objDetalleElementoRed     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy(array("elementoId"    => $intIdElemento,
                                                                  "detalleNombre" => "TIPO ELEMENTO RED"));
                if ($objDetalleElementoRed)
                {
                    $strTipoElementoRed = $objDetalleElementoRed->getDetalleValor();
                    if ($strTipoElementoRed == 'BACKBONE')
                    {
                        $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                               ->findBy(array("elementoConectorId" => $intIdElemento));
                        for($intI = 0; $intI < count($objServiciosTec); $intI++)
                        {
                            $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                            
                            $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objInfoServicio);
                            $objServicioHist->setObservacion($strMotivoElemento);
                            $objServicioHist->setIpCreacion($strIpClient);
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setUsrCreacion($strUsrSesion);
                            $objServicioHist->setEstado("Activo");
                            $emComercial->persist($objServicioHist);
                            $emComercial->flush();
                        }
                    }
                    else
                    {
                        if ($strTipoElementoRed == 'CLIENTE')
                        {
                            $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                               ->findBy(array("elementoClienteId" => $intIdElemento));
                            for($intI = 0; $intI < count($objServiciosTec); $intI++)
                            {
                                $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);

                                $objServicioHist = new InfoServicioHistorial();
                                $objServicioHist->setServicioId($objInfoServicio);
                                $objServicioHist->setObservacion($strMotivoElemento);
                                $objServicioHist->setIpCreacion($strIpClient);
                                $objServicioHist->setFeCreacion(new \DateTime('now'));
                                $objServicioHist->setUsrCreacion($strUsrSesion);
                                $objServicioHist->setEstado("Activo");
                                $emComercial->persist($objServicioHist);
                                $emComercial->flush();
                            }
                        }
                    }
                }
                
                $emComercial->getConnection()->commit();
                $emInfraestructura->getConnection()->commit();
                return $objRespuesta->setContent("OK");
            }
            else
            {
                return $objRespuesta->setContent("NO EXISTE REGISTRO PARA ACTUALIZAR");
            }
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $strMensajeError = "Error: " . $e->getMessage();
            error_log($strMensajeError);
            return $objRespuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }
    
    /**
     * @Secure(roles="ROLE_155-7797")
     *
     * Documentación para el método 'generarOperatividadMasivoTNAction'.
     *
     * Renderiza la pantalla para generar la operatividad masiva TN
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     *
     * @return Render Pantalla Generar Operatividad Masivo TN.
     */
    public function generarOperatividadMasivoTNAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strPrefEmpre = $objSession->get('prefijoEmpresa');
        $arrayCliente = $objSession->get('cliente');
        $emComercial  = $this->getDoctrine()->getManager();
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");
        //seteo el tipo de proceso
        $strTipoProceso = 'OperatividadMasivaTN';
        //verifico si la empresa es TN
        if( $strPrefEmpre != 'TN' )
        {
            throw $this->createNotFoundException('La empresa en sesión es la incorrecta, debe ser Telconet.');
        }
        //seteo las variables del cliente
        $booleanPerEmp  = false;
        $strRazonSocial = null;
        $intIdPerEmpRol = null;
        $strTipoIdentificacion  = '';
        $strIdentificacion      = '';
        $intMaxServiciosAgregar = 100;
        //verifico si existe un login en session
        if( !empty($arrayCliente) && isset($arrayCliente['id_persona_empresa_rol']) && isset($arrayCliente['id_persona']) )
        {
            $booleanPerEmp  = true;
            $intIdPerEmpRol = $arrayCliente['id_persona_empresa_rol'];
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente['id_persona']);
            $strRazonSocial = $objInfoPersona->getRazonSocial();
            $strTipoIdentificacion = $objInfoPersona->getTipoIdentificacion();
            $strIdentificacion     = $objInfoPersona->getIdentificacionCliente();
        }
        
        return $this->render('tecnicoBundle:InfoElementoRadio:generarOperatividadMasivo.html.twig', array(
                'strTipoProceso'         => $strTipoProceso,
                'booleanPerEmp'          => $booleanPerEmp,
                'strRazonSocial'         => $strRazonSocial,
                'intIdper'               => $intIdPerEmpRol,
                'intMaxServiciosAgregar' => $intMaxServiciosAgregar,
                'strTipoIdentificacion'  => $strTipoIdentificacion,
                'strIdentificacion'      => $strIdentificacion
        ));
    }
    
    /**
     * Documentación para el método 'getAjaxComboRazonSocialAction'.
     *
     * Obtiene el listado de la razon social de los clientes
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     *
     * @return Response $objResponse - Lista de la razon social de los clientes
     */
    public function getAjaxComboRazonSocialAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSession->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emComercial  = $this->getDoctrine()->getManager();

        $strEstado       = 'Activo';
        $intIdEmpresa    = $objSession->get('idEmpresa');
        $strRazonSocial  = $objRequest->get("query");
        $strCliente      = $objRequest->get("query");
        $intLimit        = $objRequest->get("limit");
        $intStart        = $objRequest->get("start");
        $intPage         = $objRequest->get("page");
        $strModulo       = 'Cliente';
        $strPrefijoEmp   = $objSession->get('prefijoEmpresa');
        $strTipoPersonal = 'Otros';

        try
        {
            $arrayParametros = array();
            $arrayParametros['estado']         = $strEstado;
            $arrayParametros['idEmpresa']      = $intIdEmpresa;
            $arrayParametros['fechaDesde']     = null;
            $arrayParametros['fechaHasta']     = null;
            $arrayParametros['nombre']         = null;
            $arrayParametros['apellido']       = null;
            $arrayParametros['razon_social']   = $strRazonSocial;
            $arrayParametros['strCliente']     = $strCliente;
            $arrayParametros['limit']          = $intLimit;
            $arrayParametros['start']          = $intStart;
            $arrayParametros['page']           = $intPage;
            $arrayParametros['tipo_persona']   = 'Cliente';
            $arrayParametros['usuario']        = '';
            $arrayParametros['strModulo']             = $strModulo;
            $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmp;
            $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
            $arrayParametros['intIdPersonEmpresaRol'] = null;
            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonasPorCriterios($arrayParametros);

            $arrayResult    = array();
            $arrayRegistros = $arrayResultado['registros'];
            foreach($arrayRegistros as $arrayData)
            {
                $strNombre = $arrayData['razon_social'];
                if(empty($strNombre))
                {
                    $strNombre = $arrayData['nombres'].' '.$arrayData['apellidos'];
                }
                //agrego al arreglo el id y el nombre de la razon social
                $arrayResult[]  = array(
                    'id'                  => $arrayData['id'],
                    'nombre'              => $strNombre,
                    'tipo_identificacion' => $arrayData['tipo_identificacion'],
                    'identificacion'      => $arrayData['identificacion']
                );
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRadioController.getAjaxComboRazonSocialAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getAjaxServiciosPorClienteTNAction'.
     *
     * Obtiene el listado de los servicios por cliente TN
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     *
     * @return Response $objResponse - Lista de los servicios por cliente TN
     */
    public function getAjaxServiciosPorClienteTNAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSession->get('user');
        $serviceUtil    = $this->get('schema.Util');
        $intIdPerEmpRol = $objRequest->get("intIdPerEmpRol");
        $intIdEmpresa   = $objSession->get('idEmpresa');
        $strTipoProceso   = $objRequest->get("tipoProceso");
        $intIdPunto       = $objRequest->get("intIdPunto");
        $arrayIdServicios = json_decode($objRequest->get('arrayIdServicios'));

        try
        {
            //verifico el tipo de proceso para setear la variable
            if($strTipoProceso == "OperatividadMasivaTN")
            {
                $strEstadoServicio = 'Activo';
            }
            
            //seteo el arreglo de los resultados
            $arrayResult    = array();
            //verifico si existe el id del cliente
            if( !empty($intIdPerEmpRol) )
            {
                $arrayParametros   = array(
                        'intIdEmpresa'          => $intIdEmpresa,
                        'intIdPerEmpRol'        => $intIdPerEmpRol,
                        'intIdPunto'            => $intIdPunto,
                        'arrayIdServicios'      => $arrayIdServicios,
                        'strEstadoPunto'        => 'Activo',
                        'strEstadoServicio'     => $strEstadoServicio,
                        'strTipoProcesoCab'     => $strTipoProceso,
                        'strEstadoMasivoCab'    => 'Pendiente',
                        'strEstadoMasivoDet'    => 'Pendiente',
                        'strUsrSesion'          => $strUsrSesion,
                        'strIpClient'           => $strIpClient,
                );
                $arrayDatosServicios = $this->getDatosServiciosPorRazonSocial($arrayParametros);
                if( $arrayDatosServicios['status'] == 'OK' )
                {
                    //agrego los servicios al arreglo
                    $arrayResult = $arrayDatosServicios['result'];
                }
                else
                {
                    throw new \Exception($arrayDatosServicios['result']);
                }
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRadioController.getAjaxServiciosPorClienteTNAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getAjaxLoginsPorRazonSocialAction'.
     *
     * Obtiene el listado de los logins por la razon social del cliente
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     *
     * @return Response $objResponse - Lista de los logins por la razon social del cliente
     */
    public function getAjaxLoginsPorRazonSocialAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSession->get('user');
        $serviceUtil  = $this->get('schema.Util');

        $intIdEmpresa = $objSession->get('idEmpresa');
        $intIdPerRol  = $objRequest->get("intIdPerEmpRol");
        $strLogin     = $objRequest->get("query");
        $strTipoProceso   = $objRequest->get("tipoProceso");
        $arrayIdServicios = json_decode($objRequest->get('arrayIdServicios'));

        try
        {
            //verifico el tipo de proceso para setear la variable
            if($strTipoProceso == "OperatividadMasivaTN")
            {
                $strEstadoServicio = 'Activo';
            }
            
            $arrayParametros   = array(
                    'intIdEmpresa'          => $intIdEmpresa,
                    'intIdPerEmpRol'        => $intIdPerRol,
                    'strLogin'              => $strLogin,
                    'arrayIdServicios'      => $arrayIdServicios,
                    'strEstadoPunto'        => 'Activo',
                    'strEstadoServicio'     => $strEstadoServicio,
                    'strTipoProcesoCab'     => $strTipoProceso,
                    'strEstadoMasivoCab'    => 'Pendiente',
                    'strEstadoMasivoDet'    => 'Pendiente',
                    'strUsrSesion'          => $strUsrSesion,
                    'strIpClient'           => $strIpClient,
            );
            $arrayResultado = $this->getDatosServiciosPorRazonSocial($arrayParametros);

            $arrayResult    = array();
            //agrego al arreglo la selección para todos
            $arrayResult[]  = array(
                'id'     => null,
                'login'  => 'Todos',
            );
            if( $arrayResultado['status'] == 'OK' )
            {
                foreach($arrayResultado['result'] as $arrayData)
                {
                    //agrego al arreglo el id y el login
                    $arrayResult[]  = array(
                        'id'     => $arrayData['idLogin'],
                        'login'  => $arrayData['login'],
                    );
                }
            }

            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRadioController.getAjaxLoginsPorRazonSocialAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getDatosServiciosPorRazonSocial'.
     *
     * Obtiene el listado de los Ap por cliente
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 26-11-2020
     *
     * @param Array $arrayParametros [
     *                                  intIdEmpresa          => id de la empresa
     *                                  intIdPerEmpRol        => id del cliente
     *                                  intIdPunto            => id del punto
     *                                  strLogin              => login del punto
     *                                  arrayIdServicios      => array de id de servicios
     *                                  strEstadoPunto        => estado del punto
     *                                  strEstadoServicio     => estado de los servicios
     *                                  strTipoProcesoCab     => tipo del proceso masivo cab
     *                                  strEstadoMasivoCab    => estado del proceso masivo cab
     *                                  strEstadoMasivoDet    => estado del proceso masivo det
     *                                  strUsrSesion          => nombre usuario en sesión
     *                                  strIpClient           => ip de sesión
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                   'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                   'result'    => arreglo con la información de los servicios o mensaje de error
     *                               ]
     */
    public function getDatosServiciosPorRazonSocial($arrayParametros)
    {
        $serviceUtil  = $this->get('schema.Util');
        $emComercial  = $this->getDoctrine()->getManager();
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");

        $strUsrSesion       = $arrayParametros['strUsrSesion'];
        $strIpClient        = $arrayParametros['strIpClient'];
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $intIdPerEmpRol     = $arrayParametros['intIdPerEmpRol'];
        $intIdPunto         = $arrayParametros['intIdPunto'];
        $arrayIdServicios   = isset($arrayParametros['arrayIdServicios']) ? $arrayParametros['arrayIdServicios'] : null;
        $strLogin           = isset($arrayParametros['strLogin']) ? $arrayParametros['strLogin'] : null;
        $strEstadoPunto     = $arrayParametros['strEstadoPunto'];
        $strEstadoServicio  = $arrayParametros['strEstadoServicio'];
        $strTipoProcesoCab  = $arrayParametros['strTipoProcesoCab'];
        $strEstadoMasivoCab = $arrayParametros['strEstadoMasivoCab'];
        $strEstadoMasivoDet = $arrayParametros['strEstadoMasivoDet'];

        try
        {
            //seteo el arreglo de los resultados
            $arrayResult      = array();

            $objInfoPerEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerEmpRol);
            //verifico si existe el cliente
            if( !is_object($objInfoPerEmpRol) )
            {
                throw new \Exception("No se encuentra el cliente, por favor notificar a Sistemas.");
            }

            $arrayDatosParametros  = array(
                    'intIdEmpresa'          => $intIdEmpresa,
                    'intIdPerEmpRol'        => $objInfoPerEmpRol->getId(),
                    'intIdPunto'            => $intIdPunto,
                    'strLogin'              => $strLogin,
                    'arrayIdServicios'      => $arrayIdServicios,
                    'strTipoEnlace'         => 'PRINCIPAL',
                    'strGrupo'              => 'DATACENTER',
                    'strEstadoPunto'        => $strEstadoPunto,
                    'strEstadoServicio'     => $strEstadoServicio,
                    'strTipoProcesoCab'     => $strTipoProcesoCab,
                    'strEstadoMasivoCab'    => $strEstadoMasivoCab,
                    'strEstadoMasivoDet'    => $strEstadoMasivoDet,
                    'strTipoOperativo'      => 'RADIO OPERATIVO'
            );
            $arrayDatosServicios = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getRadioPorClienteTN($arrayDatosParametros);
            if( $arrayDatosServicios['status'] == 'OK' )
            {
                foreach($arrayDatosServicios['result'] as $arrayDatos)
                {
                    //agrego los servicios al arreglo
                    $arrayResult[] = $arrayDatos;
                }
                $arrayResultado = array(
                    'status' => 'OK',
                    'result' => $arrayResult
                );
            }
            else
            {
                throw new \Exception($arrayDatosServicios['result']);
            }
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRadioController.getDatosServiciosPorRazonSocial',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        return $arrayResultado;
    }
    
    /**
     * @Secure(roles="ROLE_155-7797")
     *
     * Documentación para el método 'ejecutarOperatividadMasivoTNAction'.
     *
     * Método para generar la operatividad de los radios masivo TN
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 30-11-2020
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function ejecutarOperatividadMasivoTNAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $arrayIdServicios   = json_decode($objRequest->get('arrayObjServicios'));
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoProceso     = $objRequest->get("tipoProceso");
        
        $emInfraestructura->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if( !empty($arrayIdServicios) && is_array($arrayIdServicios) && count($arrayIdServicios) > 0)
            {
                //genero la operatividad de los radios seleccionados
                foreach($arrayIdServicios as $objIdServicio)
                {
                    //Consultamos si existe en la INFO_DETALLE_ELEMENTO
                    $objDetalleElemento     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                              ->findOneBy(array("elementoId"    => $objIdServicio->idElemento,
                                                                "detalleNombre" => "RADIO OPERATIVO"));
                    
                    if ($objIdServicio->esOperativo == 'SI')
                    {
                        if (is_object($objDetalleElemento))
                        {
                            $objDetalleElemento->setDetalleValor("NO");
                        }
                        else
                        {
                            $objDetalleElemento = new InfoDetalleElemento();
                            $objDetalleElemento->setElementoId($objIdServicio->idElemento);
                            $objDetalleElemento->setDetalleNombre("RADIO OPERATIVO");
                            $objDetalleElemento->setDetalleValor("NO");
                            $objDetalleElemento->setDetalleDescripcion("RADIO OPERATIVO");
                            $objDetalleElemento->setUsrCreacion($strUsrSesion);
                            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                            $objDetalleElemento->setIpCreacion($strIpClient);
                            $objDetalleElemento->setEstado("Activo");
                        }
                        $strMotivoElemento = 'Se deja sin operatividad al elemento ';
                    }
                    else
                    {
                        if (is_object($objDetalleElemento))
                        {
                            $objDetalleElemento->setDetalleValor("SI");
                        }
                        $strMotivoElemento = 'Se deja con operatividad al elemento ';
                    }
                    
                    $objInfoElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                         ->findOneBy(array("id"    => $objIdServicio->idElemento));
                
                    if (!is_object($objInfoElemento))
                    {
                        $strMensaje = 'Elemento no existe';
            
                        //seteo el arreglo del resultado
                        $arrayResultado = array(
                            'status'   => 'ERROR',
                            'mensaje'  => $strMensaje
                        );
                        
                        $objResponse = new JsonResponse();
                        $objResponse->setContent(json_encode($arrayResultado));

                        return $objResponse;
                    }
                    $strMotivoElemento = $strMotivoElemento.$objInfoElemento->getNombreElemento();
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objInfoElemento);
                    $objInfoHistorialElemento->setObservacion($strMotivoElemento);
                    $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoHistorialElemento->setUsrCreacion($strUsrSesion);
                    $objInfoHistorialElemento->setIpCreacion($strIpClient);
                    $objInfoHistorialElemento->setEstadoElemento("Activo");
                    
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();
                    
                    $emInfraestructura->persist($objDetalleElemento);
                    $emInfraestructura->flush();
                    
                    //Detalle elemento tipo elemento red
                    $objDetalleElementoRed     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array("elementoId"    => $objIdServicio->idElemento,
                                                                      "detalleNombre" => "TIPO ELEMENTO RED"));
                    if ($objDetalleElementoRed)
                    {
                        $strTipoElementoRed = $objDetalleElementoRed->getDetalleValor();
                        if ($strTipoElementoRed == 'BACKBONE')
                        {
                            $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findBy(array("elementoConectorId" => $objIdServicio->idElemento));
                            for($intI = 0; $intI < count($objServiciosTec); $intI++)
                            {
                                $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);

                                $objServicioHist = new InfoServicioHistorial();
                                $objServicioHist->setServicioId($objInfoServicio);
                                $objServicioHist->setObservacion($strMotivoElemento);
                                $objServicioHist->setIpCreacion($strIpClient);
                                $objServicioHist->setFeCreacion(new \DateTime('now'));
                                $objServicioHist->setUsrCreacion($strUsrSesion);
                                $objServicioHist->setEstado("Activo");
                                $emComercial->persist($objServicioHist);
                                $emComercial->flush();
                            }
                        }
                        else
                        {
                            if ($strTipoElementoRed == 'CLIENTE')
                            {
                                $objServiciosTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findBy(array("elementoClienteId" => $objIdServicio->idElemento));
                                for($intI = 0; $intI < count($objServiciosTec); $intI++)
                                {
                                    $intServicioId   = $objServiciosTec[$intI]->getServicioId()->getId();
                                    $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);

                                    $objServicioHist = new InfoServicioHistorial();
                                    $objServicioHist->setServicioId($objInfoServicio);
                                    $objServicioHist->setObservacion($strMotivoElemento);
                                    $objServicioHist->setIpCreacion($strIpClient);
                                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                                    $objServicioHist->setUsrCreacion($strUsrSesion);
                                    $objServicioHist->setEstado("Activo");
                                    $emComercial->persist($objServicioHist);
                                    $emComercial->flush();
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                throw new \Exception("No se está recibiendo los registros, por favor notificar a Sistemas.");
            }

            //guardar todos los cambios
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->commit();
                $emInfraestructura->getConnection()->close();
            }
            
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();
            }
            
            $strMensaje = 'Se generó correctamente la operatividad masiva de los radios.';
            
            //seteo el arreglo del resultado
            $arrayResultado = array(
                'status'   => 'OK',
                'mensaje'  => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            //seteo el arreglo del resultado
            $arrayResultado = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }
            
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoRadioController.ejecutarOperatividadMasivoTNAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent(json_encode($arrayResultado));

        return $objResponse;
    }
    
}