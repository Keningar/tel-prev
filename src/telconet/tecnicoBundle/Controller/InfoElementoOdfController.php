<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
use telconet\schemaBundle\Form\InfoElementoOdfType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Documentación para la clase 'InfoElementoOdf'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Odf's
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 02-03-2015
 */

class InfoElementoOdfController extends Controller
{
    /**
     * @Secure(roles="ROLE_275-1")
     * 
     * Documentación para el método 'indexOdfAction'.
     *
     * Metodo utilizado para retornar a la pagina principal de la administracion de Odf's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     */
    public function indexOdfAction()
    {
        $rolesPermitidos = array();

        //MODULO 275 - ODF
        if(true === $this->get('security.context')->isGranted('ROLE_275-4'))
        {
            $rolesPermitidos[] = 'ROLE_275-4'; //editar elemento ODF
        }
        if(true === $this->get('security.context')->isGranted('ROLE_275-8'))
        {
            $rolesPermitidos[] = 'ROLE_275-8'; //eliminar elemento ODF
        }
        if(true === $this->get('security.context')->isGranted('ROLE_275-6'))
        {
            $rolesPermitidos[] = 'ROLE_275-6'; //ver elemento ODF
        }
        if(true === $this->get('security.context')->isGranted('ROLE_275-2137'))
        {
            $rolesPermitidos[] = 'ROLE_275-2137'; //ver administracion puertos ODF
        }
        
        return $this->render('tecnicoBundle:InfoElementoOdf:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_275-2")
     * 
     * Documentación para el método 'newOdfAction'.
     *
     * Metodo utilizado para retornar a la pagina de creacion de nuevo Odf
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     */
    public function newOdfAction()
    {
        $objElemento = new InfoElemento();
        $form        = $this->createForm(new InfoElementoOdfType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoOdf:new.html.twig', array(
                             'form' => $form->createView())
                            );
    }

    /**
     * @Secure(roles="ROLE_275-3")
     * 
     * Documentación para el método 'createOdfAction'.
     *
     * Metodo utilizado para crear el nuevo Odf
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 04-07-2016    Se agrega nuevo filtro para marcar equipos que aprovisionan factibilidad automatica para TN
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function createOdfAction()
    {
        ini_set('max_execution_time', 3000000);
        $peticion                  = $this->get('request');
        $session                   = $peticion->getSession();
        $em                        = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objElementoOdf            = new InfoElemento();
        $arrayParametros           = $peticion->request->get('telconet_schemabundle_infoelementoodftype');
        $strNombreElemento         = $arrayParametros['nombreElemento'];
        $intModeloElementoId       = $arrayParametros['modeloElementoId'];
        $intNodoElementoId         = $arrayParametros['nodoElementoId'];
        $intRackElementoId         = $arrayParametros['rackElementoId'];
        $intUnidadRack             = $arrayParametros['unidadRack'];
        $intClaseTipoMedio         = $arrayParametros['claseTipoMedioId'];
        $strDescripcionElemento    = $arrayParametros['descripcionElemento'];
        $strFactibilidadAutomatica = $arrayParametros['factibilidadAutomatica'];
        $strUnidadesOcupadas       = "";
        $intUnidadMaximaU          = 0;
        $form                      = $this->createForm(new InfoElementoOdfType(), $objElementoOdf);
        $boolMensajeUsuario        = false;
        $em->beginTransaction();
        try
        {
            
            $objTipoMedio           = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                         ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            $form->bind($peticion);
            if ($form->isValid()) 
            {
                //verificar que el nombre del elemento no se repita
                $objElementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("nombreElemento" => $strNombreElemento, "estado" => "Activo"));
                if($objElementoRepetido)
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                }

                $objModeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $intModeloElementoId);
                $objElementoOdf->setNombreElemento($strNombreElemento);
                $objElementoOdf->setDescripcionElemento($strDescripcionElemento);
                $objElementoOdf->setModeloElementoId($objModeloElemento);
                $objElementoOdf->setUsrResponsable($session->get('user'));
                $objElementoOdf->setUsrCreacion($session->get('user'));
                $objElementoOdf->setFeCreacion(new \DateTime('now'));
                $objElementoOdf->setIpCreacion($peticion->getClientIp());
                $objElementoOdf->setEstado("Activo");
                $em->persist($objElementoOdf);
                $em->flush();

                //buscar el interface Modelo
                $objInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                         ->findBy(array("modeloElementoId" => $intModeloElementoId));
                foreach($objInterfaceModelo as $im)
                {
                    $intCantidadInterfaces = $im->getCantidadInterface();
                    $formato = $im->getFormatoInterface();

                    for($i = 1; $i <= $intCantidadInterfaces; $i++)
                    {
                        $objInterfaceElemento = new InfoInterfaceElemento();

                        $format = explode("?", $formato);
                        $strNombreInterfaceElemento = $format[0] . $i;

                        $objInterfaceElemento->setNombreInterfaceElemento($strNombreInterfaceElemento);
                        $objInterfaceElemento->setElementoId($objElementoOdf);
                        $objInterfaceElemento->setEstado("not connect");
                        $objInterfaceElemento->setUsrCreacion($session->get('user'));
                        $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                        $objInterfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($objInterfaceElemento);
                        $em->flush();

                        if (trim($format[0])== "OUT")
                        {
                            $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                              ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                "estado"           => "Activo",
                                                                "numeroHilo"       => $i));
                            if ( $objAdmiHilo )
                            {
                                $objDetalletInterface = new InfoDetalleInterface();
                                $objDetalletInterface->setInterfaceElementoId($objInterfaceElemento);
                                $objDetalletInterface->setDetalleNombre("Color Hilo");
                                $objDetalletInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                $objDetalletInterface->setUsrCreacion($session->get('user'));
                                $objDetalletInterface->setFeCreacion(new \DateTime('now'));
                                $objDetalletInterface->setIpCreacion($peticion->getClientIp());

                                $em->persist($objDetalletInterface);
                                $em->flush();
                            }
                        }

                    }
                }
                //se crean relaciones entre interfaces del elemento para mayor escabilidad
                $objInterfacesElementos = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                             ->findBy(array( "elementoId" =>$objElementoOdf->getId()));
                foreach($objInterfacesElementos as $objInterfaceElemento)
                {
                    $pos = strpos($objInterfaceElemento->getNombreInterfaceElemento(), 'IN ');
                    if ($pos !== false)
                    {
                        $strNombreInterfaceOut = str_replace("IN ", "OUT ", $objInterfaceElemento->getNombreInterfaceElemento());
                        
                    
                        $objInterfaceOut = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                              ->findOneBy(array("elementoId"=>$objElementoOdf->getId(),
                                                             "nombreInterfaceElemento"=>$strNombreInterfaceOut));
                        $enlace  = new InfoEnlace();
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

                $objElementoRack            = $em->getRepository('schemaBundle:InfoElemento')->find($intRackElementoId);
                $objInterfaceModeloRack     = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));
                $objElementoUnidadRack      = $em->getRepository('schemaBundle:InfoElemento')->find($intUnidadRack);
                $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                              (int) $objModeloElemento->getURack() - 1;
                if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('No se puede ubicar el Odf en el Rack porque se sobrepasa el tamaño de unidades!');
                }
                //obtener todas las unidades del rack
                $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                   ->findBy(array("elementoIdA"              => $intRackElementoId,
                                                                  "estado"                   => "Activo"
                                                                 )
                                                           );
                //se verifica disponibilidad de unidades y se asignan recursos
                for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                {
                    $intElementoUnidadId     = 0;
                    $objRelacionElementoRack = null;
                    foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                    {
                        $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                            ->find($objRelacionElementoUDRack->getElementoIdB());
                        if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                        {
                            $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                        }
                    }
                    $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                  ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                    "estado"                  => "Activo"
                                                                   )
                                                             );
                    if($objRelacionElementoRack)
                    {
                        if ($strUnidadesOcupadas == "")
                        {
                            $strUnidadesOcupadas = $t;
                        }
                        else
                        {
                            $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                        }
                    }
                    else
                    {
                        //relacion elemento
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                        $objRelacionElemento->setElementoIdB($objElementoOdf->getId());
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Rack contiene Odf");
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($session->get('user'));
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                        $em->persist($objRelacionElemento);
                    }
                }
                if($strUnidadesOcupadas != "")
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception('No se puede ubicar el Odf en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                }

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoOdf);
                $objHistorialElemento->setEstadoElemento("Activo");
                $objHistorialElemento->setObservacion("Se ingreso un Odf");
                $objHistorialElemento->setUsrCreacion($session->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objHistorialElemento);

                //tomar datos nodo
                $objNodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                      ->findOneBy(array("elementoId" => $intNodoElementoId));
                $objNodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                      ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                //info ubicacion
                $objParroquia         = $em->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objNodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objNodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al odf ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
                if($arrayRespuestaCoordenadas["status"] === "ERROR")
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                }
                $objUbicacionElemento = new InfoUbicacion();
                $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
                $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
                $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
                $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
                $objUbicacionElemento->setParroquiaId($objParroquia);
                $objUbicacionElemento->setUsrCreacion($session->get('user'));
                $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                $objUbicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objUbicacionElemento);

                //empresa elemento ubicacion
                $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                $objEmpresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $objEmpresaElementoUbica->setElementoId($objElementoOdf);
                $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                $objEmpresaElementoUbica->setUsrCreacion($session->get('user'));
                $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $objEmpresaElementoUbica->setIpCreacion($peticion->getClientIp());
                $em->persist($objEmpresaElementoUbica);

                //empresa elemento
                $objEmpresaElemento = new InfoEmpresaElemento();
                $objEmpresaElemento->setElementoId($objElementoOdf);
                $objEmpresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $objEmpresaElemento->setEstado("Activo");
                $objEmpresaElemento->setUsrCreacion($session->get('user'));
                $objEmpresaElemento->setIpCreacion($peticion->getClientIp());
                $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($objEmpresaElemento);

                $em->flush();
                
                //caracteristica para saber que clase tipo medio utiliza
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objElementoOdf->getId());
                $objDetalleElemento->setDetalleNombre("claseTipoMedioId");
                $objDetalleElemento->setDetalleValor($intClaseTipoMedio);
                $objDetalleElemento->setDetalleDescripcion("Caracteristicas para almacenar la clase tipo medio");
                $objDetalleElemento->setEstado("Activo");
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setUsrCreacion($session->get('user'));
                $objDetalleElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objDetalleElemento);
                $em->flush();
                
                //caracteristica para saber que clase tipo medio utiliza
                $objDetalleElementoFac = new InfoDetalleElemento();
                $objDetalleElementoFac->setElementoId($objElementoOdf->getId());
                $objDetalleElementoFac->setDetalleNombre("FACTIBILIDAD AUTOMATICA");
                $objDetalleElementoFac->setDetalleValor($strFactibilidadAutomatica);
                $objDetalleElementoFac->setDetalleDescripcion("FACTIBILIDAD AUTOMATICA");
                $objDetalleElementoFac->setEstado("Activo");
                $objDetalleElementoFac->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoFac->setUsrCreacion($session->get('user'));
                $objDetalleElementoFac->setIpCreacion($peticion->getClientIp());
                $em->persist($objDetalleElementoFac);
                $em->flush();
                
                $em->commit();
                return $this->redirect($this->generateUrl('elementoodf_showOdf', array('id' => $objElementoOdf->getId())));
            }
        }
        catch (\Exception $e) 
        {
            if($boolMensajeUsuario)
            {
                $strMensajeError = "Error: ".$e->getMessage();
            }
            else
            {
                $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
            }
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            error_log("Error: ".$e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->render('tecnicoBundle:InfoElementoOdf:new.html.twig', array('form' => $form->createView()));
        }  
    }

    /**
     * @Secure(roles="ROLE_275-4")
     * 
     * Documentación para el método 'editOdfAction'.
     *
     * Metodo utilizado para retornar a la pagina de edición de un Odf
     * @param integer $id
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 01-07-2016
     * Se valida que exista elementos padres para la asignación de valores relacionados el ODF principal
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 01-07-2016  Se agrega recuperacion de caracteristica de aprovisionamiento de fac. automatica usada en Tn
     */
    public function editOdfAction($id)
    {
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');
        $em        = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strMsgErr = '';
        
        if(null == $objElemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -ODF- que se quiere modificar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento     = $this->get('tecnico.InfoServicioTecnico');
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            $ubicacion         = $respuestaElemento['ubicacion'];
            
            
            $objElementoPadreOlt = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                      ->findOneBy(array("elementoIdB" => $objElemento->getId(),"estado" => "Activo"));
           
            //se obtiene información de elementos contenedores de Odf para presentar en pantalla
            if($objElementoPadreOlt)
            {
                $objElementoPadre = $em->find('schemaBundle:InfoElemento', $objElementoPadreOlt->getElementoIdA());
                //se obtiene unidad donde esta ubicado el OLT
                if($objElementoPadre)
                {
                    $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                  ->findOneBy(array("elementoIdB" => $objElementoPadre->getId(),"estado" => "Activo"));
                    if($objRelacionElementoRack)
                    {
                        $objRelacionElementoNodo = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                      ->findOneBy(array("elementoIdB" => $objRelacionElementoRack->getElementoIdA(),
                                                                        "estado"      => "Activo"));
                        if($objRelacionElementoNodo)
                        {
                            $objElemento->setNodoElementoId($objRelacionElementoNodo->getElementoIdA());
                        }
                        else
                        {
                            $strMsgErr .= '\n-NODO';
                        }
                        
                        $objElemento->setRackElementoId($objRelacionElementoRack->getElementoIdA());
                    }
                    else
                    {
                        $strMsgErr .= '\n-RACK';
                    }
                    
                    $objElemento->setUnidadRack($objElementoPadre->getId());
                }
                else
                {
                    $strMsgErr .= '\n-UNIDAD';
                }

            }
            else
            {
                $strMsgErr .= '\n-RELACIÓN CON ELEMENTOS';
            }
            
            if($strMsgErr !== '')
            {
                $strMsgErr = 'ODF no disponde de: ' . $strMsgErr . '\nVerificar la data por empresa.';
            }


            $objDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                     ->findOneBy(array("detalleNombre" => "claseTipoMedioId",
                                                       "elementoId"    => $objElemento->getId()));
            if($objDetalleElemento)
            {
                $objElemento->setClaseTipoMedioId($objDetalleElemento->getDetalleValor());
            }
        
            $objDetalleElementoFac = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array("detalleNombre" => "FACTIBILIDAD AUTOMATICA",
                                                          "elementoId"    => $objElemento->getId()));
            if($objDetalleElementoFac)
            {
                $objElemento->setFactibilidadAutomatica($objDetalleElementoFac->getDetalleValor());
            }
        }

        $formulario = $this->createForm(new InfoElementoOdfType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoOdf:edit.html.twig', array('edit_form' => $formulario->createView(),
                                                                                   'odf'       => $objElemento,
                                                                                   'msgErr'    => $strMsgErr,
                                                                                   'ubicacion' => $ubicacion));       
    }

    /**
     * @Secure(roles="ROLE_275-5")
     * 
     * Documentación para el método 'updateOdfAction'.
     *
     * Metodo utilizado para actualizar el Odf
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 29-10-2015 Se crea funcionabilidad de edición
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 04-07-2016    Se agrega nuevo filtro para marcar equipos que aprovisionan factibilidad automatica para TN
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 18-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     */
    public function updateOdfAction($id)
    {
        $em                         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC                        = $this->getDoctrine()->getManager('telconet');
        $peticion                   = $this->get('request');
        $session                    = $peticion->getSession();
        $request                    = $this->get('request');
        $arrayParametros            = $request->request->get('telconet_schemabundle_infoelementoodftype');
        $strNombreElemento          = $arrayParametros['nombreElemento'];
        $intNodoElementoId          = $arrayParametros['nodoElementoId'];
        $intClaseTipoMedio          = $arrayParametros['claseTipoMedioId'];
        $strFactibilidadAutomatica  = $arrayParametros['factibilidadAutomatica'];
        $strDescripcionElemento     = $arrayParametros['descripcionElemento'];
        $intModeloElementoId        = $arrayParametros['modeloElementoId'];
        $intRackElementoId          = $arrayParametros['rackElementoId'];
        $intUnidadRack              = $arrayParametros['unidadRack'];
        $intIdUbicacion             = $request->request->get('idUbicacion');
        $intNodoElementoAntes       = "";
        $intRackElementoAntes       = "";
        $intUnidadRackAntes         = "";
        $strUnidadesOcupadas        = "";
        $boolMensajeUsuario         = false;
        
        $em->beginTransaction();
        try
        {
            $objModeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $intModeloElementoId);
            $objElemento       = $em->getRepository('schemaBundle:InfoElemento')->find($id);
            $objTipoMedio      = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                    ->findOneBy(array("codigoTipoMedio" => 'FO', "estado" => "Activo"));
            if(!$objElemento)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }
            //revisar si es cambio de modelo
            $intModeloAnterior = $objElemento->getModeloElementoId();
            $flag              = 0;
            if($intModeloAnterior->getId() != $objModeloElemento->getId())
            {
                $objInterfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $intModeloAnterior->getId()));
                $objInterfaceModeloNuevo    = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $objModeloElemento->getId()));
                
                $intCantAnterior    = $objInterfaceModeloAnterior->getCantidadInterface();
                $intCantNueva       = $objInterfaceModeloNuevo->getCantidadInterface();
                $strFormatoAnterior = $objInterfaceModeloAnterior->getFormatoInterface();
                $strFormatoNuevo    = $objInterfaceModeloNuevo->getFormatoInterface();
                
                $objDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array("detalleNombre" => "claseTipoMedioId",
                                                           "elementoId"    => $objElemento->getId()));
                $objDetalleElemento->setDetalleValor($intClaseTipoMedio);
                $em->persist($objDetalleElemento);
                
                //buscar el interface Modelo
                $entityAdmiInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                ->findBy(array("modeloElementoId" => $intModeloAnterior->getId()));                
                
                if($intCantAnterior > $intCantNueva)
                {
                    //se valida que no se encuentre ocupada alguna interface del ODF
                    foreach($entityAdmiInterfaceModelo as $entityInterfaceModelo)
                    {
                        $strFormatoAnterior = $entityInterfaceModelo->getFormatoInterface();
                        $format             = explode("?", $strFormatoAnterior);
                        //revisar puertos restantes si estan ocupados
                        for($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                        {
                            $strNombreInterfaceElementoAnterior = $format[0] . $i;
                            
                            $entityInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->findOneBy(array("elementoId"              => $objElemento->getId(), 
                                                                            "nombreInterfaceElemento" => $strNombreInterfaceElementoAnterior,
                                                                            "estado"                  =>"connected"));

                            if ($entityInterfaceElemento)
                            {
                                $flag = 1;
                                break;
                            }
                         }
                         if($flag == 1)
                         {
                             break;
                         }
                    }
                    //en caso de esta la bandera FLAG en 0 podemos continunar con la Edición
                    if($flag == 0)
                    {
                        //actualizar hilos de la interface
                        foreach($entityAdmiInterfaceModelo as $entityInterfaceModelo)
                        {
                            $strFormatoNuevo = $entityInterfaceModelo->getFormatoInterface();
                            $format          = explode("?", $strFormatoNuevo);
                            if (trim($format[0])== "OUT")
                            {
                                //revisar puertos restantes si estan ocupados
                                for($i = 1; $i <= $intCantNueva; $i++)
                                {
                                    $strNombreInterfaceElementoAnterior = $format[0] . $i;

                                    $arrayInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->findBy(array("elementoId"              => $objElemento->getId(), 
                                                                                "nombreInterfaceElemento" => $strNombreInterfaceElementoAnterior));
                                    //se procede a modificar hilos de puertos
                                    foreach($arrayInterfaceElemento as $objInterface)
                                    {
                                        if($objInterface->getEstado() != "deleted")
                                        {
                                            $objDetalleInterface = $em->getRepository('schemaBundle:InfoDetalleInterface')
                                                                      ->findOneBy(array("interfaceElementoId" => $objInterface->getId(),
                                                                                        "detalleNombre"       => "Color Hilo"));
                                            if ( $objDetalleInterface )
                                            {
                                                $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                                                  ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                                    "estado"           => "Activo",
                                                                                    "numeroHilo"       => $i));
                                                if ( $objAdmiHilo )
                                                {
                                                    $objDetalleInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                                    $em->persist($objDetalleInterface);
                                                }
                                            }
                                            else
                                            {
                                                $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                                                  ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                                    "estado"           => "Activo",
                                                                                    "numeroHilo"       => $i));
                                                if ( $objAdmiHilo )
                                                {
                                                    $objDetalletInterface = new InfoDetalleInterface();
                                                    $objDetalletInterface->setInterfaceElementoId($objInterface);
                                                    $objDetalletInterface->setDetalleNombre("Color Hilo");
                                                    $objDetalletInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                                    $objDetalletInterface->setUsrCreacion($session->get('user'));
                                                    $objDetalletInterface->setFeCreacion(new \DateTime('now'));
                                                    $objDetalletInterface->setIpCreacion($peticion->getClientIp());

                                                    $em->persist($objDetalletInterface);
                                                }

                                            }
                                        }//fin de if($interface->getEstado() != "deleted")
                                    }//fin de foreach($entityInterfaceElemento as $interface)
                                 }//fin de for($i = 1; $i <= $intCantNueva; $i++)     
                             }//fin de if (trim($format[0])== "OUT")
                        }//fin de foreach($entityAdmiInterfaceModelo as $entity)
                        
                        //cambiar estado a eliminado de interface 
                        foreach($entityAdmiInterfaceModelo as $entityInterfaceModelo)
                        {
                            $strFormatoAnterior = $entityInterfaceModelo->getFormatoInterface();
                            $format             = explode("?", $strFormatoAnterior);
                            for($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                            {
                                $strNombreInterfaceElementoAnterior = $format[0] . $i;

                                $arrayInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findBy(array("elementoId"              => $objElemento->getId(), 
                                                                            "nombreInterfaceElemento" => $strNombreInterfaceElementoAnterior));

                                foreach($arrayInterfaceElemento as $objInterface)
                                {
                                    if($objInterface->getEstado() != "deleted")
                                    {
                                        $objInterface->setEstado("deleted");
                                        $objInterface->setUsrCreacion($session->get('user'));
                                        $objInterface->setFeCreacion(new \DateTime('now'));
                                        $objInterface->setIpCreacion($peticion->getClientIp());
                                        $em->persist($objInterface);
                                    }
                                }
                            }//fin de for($i = ($intCantNueva + 1); $i <= $intCantAnterior; $i++)
                        }//fin de foreach($entityAdmiInterfaceModelo as $entity)
                    }
                }
                else if($intCantAnterior < $intCantNueva)
                {
                    //actualizar hilos de la interface
                    foreach($entityAdmiInterfaceModelo as $entityInterfaceModelo)
                    {
                        $strFormatoNuevo = $entityInterfaceModelo->getFormatoInterface();
                        $format          = explode("?", $strFormatoNuevo);
                        if (trim($format[0])== "OUT")
                        {
                            for($i = 1; $i <= $intCantAnterior; $i++)
                            {
                                $strNombreInterfaceElemento = $format[0] . $i;

                                $arrayInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findBy(array("elementoId"              => $objElemento->getId(), 
                                                                             "nombreInterfaceElemento" => $strNombreInterfaceElemento));
                                foreach($arrayInterfaceElemento as $objInterface)
                                {
                                    if($objInterface->getEstado() != "deleted")
                                    {

                                        $objDetalleInterface = $em->getRepository('schemaBundle:InfoDetalleInterface')
                                                                  ->findOneBy(array("interfaceElementoId" => $objInterface,
                                                                                     "detalleNombre"      => "Color Hilo"));
                                        if ( $objDetalleInterface )
                                        {
                                            $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                                              ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                                "estado"           => "Activo",
                                                                                "numeroHilo"       => $i));
                                            if ( $objAdmiHilo )
                                            {
                                                $objDetalleInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                                $em->persist($objDetalleInterface);
                                            }
                                        }
                                        else
                                        {
                                            $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                                              ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                                "estado"           => "Activo",
                                                                                "numeroHilo"       => $i));
                                            if ( $objAdmiHilo )
                                            {
                                                $objDetalletInterface = new InfoDetalleInterface();
                                                $objDetalletInterface->setInterfaceElementoId($objInterface);
                                                $objDetalletInterface->setDetalleNombre("Color Hilo");
                                                $objDetalletInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                                $objDetalletInterface->setUsrCreacion($session->get('user'));
                                                $objDetalletInterface->setFeCreacion(new \DateTime('now'));
                                                $objDetalletInterface->setIpCreacion($peticion->getClientIp());

                                                $em->persist($objDetalletInterface);
                                            }

                                        }
                                    }//fin de if($interface->getEstado() != "deleted")
                                }//fin de foreach($entityInterfaceElemento as $interface)
                            }//fin de for($i = 1; $i <= $intCantAnterior; $i++)
                        }
                    }//fin de foreach($entityAdmiInterfaceModelo as $entity)  
                    //crear las nuevas interfaces con sus respectivos hilos
                    //crear nuevas interfaces
                    foreach($entityAdmiInterfaceModelo as $entityInterfaceModelo)
                    {
                        $strFormatoNuevo = $entityInterfaceModelo->getFormatoInterface();
                        $format          = explode("?", $strFormatoNuevo);
                        for($i = ($intCantAnterior + 1); $i <= $intCantNueva; $i++)
                        {
                            $objInterfaceElemento            = new InfoInterfaceElemento();
                            $strNombreInterfaceElementoNuevo = $format[0] . $i;
                            $objInterfaceElemento->setNombreInterfaceElemento($strNombreInterfaceElementoNuevo);
                            $objInterfaceElemento->setElementoId($objElemento);
                            $objInterfaceElemento->setEstado("not connect");
                            $objInterfaceElemento->setUsrCreacion($session->get('user'));
                            $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                            $objInterfaceElemento->setIpCreacion($peticion->getClientIp());

                            $em->persist($objInterfaceElemento);
                            $em->flush();

                            if (trim($format[0])== "OUT")
                            {
                                $objAdmiHilo = $em->getRepository('schemaBundle:AdmiHilo')
                                                  ->findOneBy(array("claseTipoMedioId" => $intClaseTipoMedio,
                                                                    "estado"           => "Activo",
                                                                    "numeroHilo"       => $i));
                                if ( $objAdmiHilo )
                                {
                                    $objDetalletInterface = new InfoDetalleInterface();
                                    $objDetalletInterface->setInterfaceElementoId($objInterfaceElemento);
                                    $objDetalletInterface->setDetalleNombre("Color Hilo");
                                    $objDetalletInterface->setDetalleValor($objAdmiHilo->getColorHilo());
                                    $objDetalletInterface->setUsrCreacion($session->get('user'));
                                    $objDetalletInterface->setFeCreacion(new \DateTime('now'));
                                    $objDetalletInterface->setIpCreacion($peticion->getClientIp());

                                    $em->persist($objDetalletInterface);
                                    $em->flush();
                                }
                            }//fin de if (trim($format[0])== "OUT")
                        }//fin de for($i = ($intCantAnterior + 1); $i <= $intCantNueva; $i++)
                    }//fin de foreach($entityAdmiInterfaceModelo as $entity)
                    //se crean relaciones entre interfaces del elemento para mayor escabilidad
                    $objInterfacesElementos = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->findBy(array( "elementoId" => $objElemento->getId(),
                                                                 "estado"     => 'not connect'));
                    foreach($objInterfacesElementos as $objInterfaceElemento)
                    {
                        $pos = strpos($objInterfaceElemento->getNombreInterfaceElemento(), 'IN ');
                        if ($pos !== false)
                        {
                            $strNombreInterfaceOut = str_replace("IN ", "OUT ", $objInterfaceElemento->getNombreInterfaceElemento());


                            $arrayInterfaceOut = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findBy(array("elementoId"              => $objElemento->getId(),
                                                                    "nombreInterfaceElemento" => $strNombreInterfaceOut));
                            
                            foreach($arrayInterfaceOut as $objInterfaceOut)
                            {
                                 if($objInterfaceOut->getEstado() != "deleted")
                                 {
                                     $entityInterfaceOut = $objInterfaceOut;
                                     break;
                                 }
                            }
                            
                            //se valida si existe enlace
                            $entityEnlace = $em->getRepository('schemaBundle:InfoEnlace')
                                               ->findOneBy(array( "interfaceElementoIniId" => $objInterfaceElemento->getId(),
                                                                  "interfaceElementoFinId" => $entityInterfaceOut->getId(),
                                                                  "estado"                 => 'Activo'
                                                                )
                                                          );
                            //se crean enlaces para nuevos puertos creados
                            if (!$entityEnlace)
                            {
                                $enlace  = new InfoEnlace();
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


            if($flag == 0)
            {
                //elemento
                $objElemento->setNombreElemento($strNombreElemento);
                $objElemento->setDescripcionElemento($strDescripcionElemento);
                $objElemento->setModeloElementoId($objModeloElemento);
                $objElemento->setUsrResponsable($session->get('user'));
                $objElemento->setUsrCreacion($session->get('user'));
                $objElemento->setFeCreacion(new \DateTime('now'));
                $objElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objElemento);
                
                $intNodoElementoAntes = 0;
                $intRackElementoAntes = 0;
                $intUnidadRackAntes   = 0;

                //se verifica si disponde del elemento contedor antiguo del odf
                $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                          ->findOneBy(array("elementoIdB" => $objElemento,"estado" => "Activo"));
                if($objRelacionElemento)
                {
                    $objElementoPadre = $em->find('schemaBundle:InfoElemento', $objRelacionElemento->getElementoIdA());
                    if($objElementoPadre)
                    {
                        $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                      ->findOneBy(array("elementoIdB" => $objElementoPadre->getId(),"estado" => "Activo"));
                        if($objRelacionElementoRack)
                        {
                            $intRackElementoAntes    = $objRelacionElementoRack->getElementoIdA();
                            $objRelacionElementoNodo = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                          ->findOneBy(array("elementoIdB" => $objRelacionElementoRack->getElementoIdA(),
                                                                             "estado" => "Activo"));
                            if($objRelacionElementoNodo)
                            {
                                $intNodoElementoAntes = $objRelacionElementoNodo->getElementoIdA();
                            }
                        }
                        //se obtiene unidad donde esta ubicado el ODF
                        $intUnidadRackAntes = $objElementoPadre->getId();
                    }
                }
                
                //se verifica si cambio el elemento contedor del Olt
                if ( $intNodoElementoAntes != $intNodoElementoId || 
                     $intRackElementoAntes != $intRackElementoId || 
                     $intUnidadRackAntes   != $intUnidadRack )
                 {
                    //eliminar recursos antiguos
                    $objRelacionesElementoAntes     = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                         ->findBy(array("elementoIdB" => $objElemento,"estado" => "Activo"));
                    foreach($objRelacionesElementoAntes as $objRelacionElementoAntes)
                    {
                        $objRelacionElementoAntes->setEstado("Eliminado");
                        $em->persist($objRelacionElementoAntes);   
                        $em->flush();
                    }
                    
                    $objElementoRack            = $em->getRepository('schemaBundle:InfoElemento')->find($intRackElementoId);
                    $objInterfaceModeloRack     = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                     ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));
                    $objElementoUnidadRack      = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array("id" => $intUnidadRack));
                    $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                                  (int) $objModeloElemento->getURack() - 1;
                    if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception('No se puede ubicar el Odf en el Rack porque se sobrepasa el tamaño de unidades!');
                    }
                    //obtener todas las unidades del rack
                    $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                       ->findBy(array("elementoIdA"              => $intRackElementoId,
                                                                      "estado"                   => "Activo"
                                                                     )
                                                               );
                    //se verifica disponibilidad de unidades y se asignan recursos
                    for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                    {
                        $intElementoUnidadId     = 0;
                        $objRelacionElementoRack = null;
                        foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                        {
                            $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objRelacionElementoUDRack->getElementoIdB());
                            if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                            {
                                $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                            }
                        }
                        $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                      ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                        "estado"                  => "Activo"
                                                                       )
                                                                 );
                        if($objRelacionElementoRack)
                        {
                            if ($strUnidadesOcupadas == "")
                            {
                                $strUnidadesOcupadas = $t;
                            }
                            else
                            {
                                $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                            }
                        }
                        else
                        {
                            //relacion elemento
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                            $objRelacionElemento->setElementoIdB($objElemento->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("Rack contiene Odf");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($session->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                            $em->persist($objRelacionElemento);
                        }
                    }
                    if($strUnidadesOcupadas != "")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception('No se puede ubicar el Odf en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                    }
                    
                    
                    //se actualiza ubicación del Odf
                    $objNodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                          ->findOneBy(array("elementoId" => $intNodoElementoId));
                    $objNodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                          ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                    //cambiar ubicacion del Olt
                    $objParroquia = $em->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objNodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objNodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al odf ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    $objUbicacionElemento = $em->find('schemaBundle:InfoUbicacion', $intIdUbicacion);
                    $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
                    $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
                    $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
                    $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
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
                $objHistorialElemento->setObservacion("Se modifico el ODF");
                $objHistorialElemento->setUsrCreacion($session->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objHistorialElemento);
                
                $objDetalleElementoFac = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array("detalleNombre" => "FACTIBILIDAD AUTOMATICA",
                                                              "elementoId"    => $objElemento->getId()));
                $objDetalleElementoFac->setDetalleValor($strFactibilidadAutomatica);
                $em->persist($objDetalleElementoFac);

                $em->flush();
                $em->commit();

                return $this->redirect($this->generateUrl('elementoodf_showOdf', array('id' => $objElemento->getId())));
            }
            else
            {
                $boolMensajeUsuario = true;
                throw new \Exception('Elemento aun tiene puertos a eliminar con estado Conectado, favor regularice!');
            }
        }
        catch (\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
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
            error_log("Error: ".$e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl('elementoodf_editOdf', array('id' => $id)));
        }
    }

    /**
     * @Secure(roles="ROLE_275-8")
     * 
     * Documentación para el método 'deleteOdfAction'.
     *
     * Metodo utilizado para eliminar un Odf
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function deleteOdfAction()
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
            $objElemento    = $em->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);

            if(!$objElemento)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }

            $objInterfacesElementos = $emC->getRepository('schemaBundle:InfoInterfaceElemento')->findBy(array("elementoId" => $objElemento->getId()));
            for($i = 0; $i < count($objInterfacesElementos); $i++)
            {

                $strEstadoInterface = $objInterfacesElementos[$i]->getEstado();

                if($strEstadoInterface != "not connect")
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
            for($i = 0; $i < count($objInterfaceElemento); $i++)
            {
                $objInterface = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($objInterfaceElemento[$i]->getId());
                $objInterface->setEstado("Eliminado");
                $em->persist($objInterface);
            }

            //relacion elemento
            $objRelacionesElementos = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elementoIdB" => $objElemento));
            foreach($objRelacionesElementos as $objRelacionElemento)
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
            $objHistorialElemento->setObservacion("Se elimino un Odf");
            $objHistorialElemento->setUsrCreacion($session->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objHistorialElemento);

            $em->flush();
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('elementoodf'));
            
        }
        catch (\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $mensajeError = "Error: ".$e->getMessage();
            error_log($mensajeError);
            return $respuesta->setContent("PROBLEMAS TRANSACCION");
        }
    }

    /**
     * @Secure(roles="ROLE_275-6")
     * 
     * Documentación para el método 'showOdfAction'.
     *
     * Metodo utilizado para mostrar la pagina de información de un Odf
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     */
    public function showOdfAction($id)
    {
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $objElemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento     = $this->get('tecnico.InfoServicioTecnico');
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);

            $ipElemento     = $respuestaElemento['ipElemento'];
            $arrayHistorial = $respuestaElemento['historialElemento'];
            $objUbicacion   = $respuestaElemento['ubicacion'];
            $jurisdiccion   = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoOdf:show.html.twig', array(
                             'elemento'          => $objElemento,
                             'ipElemento'        => $ipElemento,
                             'historialElemento' => $arrayHistorial,
                             'ubicacion'         => $objUbicacion,
                             'jurisdiccion'      => $jurisdiccion,
                             'flag'              => $peticion->get('flag'))
                            );
    }

    /**
     * @Secure(roles="ROLE_275-2157")
     * 
     * Documentación para el método 'getEncontradosOdfAction'.
     *
     * Metodo utilizado para obtener información de los Odf's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-03-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2021 - Se define en un solo arreglo de parámetro el filtro para los Odf
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.2
     * @since 13-04-2023  Se agrega el prefijo Empresa para concatenar al filtro de busqueda por empresa.
     * 
     */
    public function ajaxGetEncontradosOdfAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $session            = $this->get('session');
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento       = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => "ODF"));
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
}