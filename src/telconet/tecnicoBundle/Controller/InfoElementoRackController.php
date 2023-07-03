<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\tecnicoBundle\Controller;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Form\InfoElementoRackType;
use telconet\tecnicoBundle\Resources\util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * Documentación para la clase 'InfoElementoRack'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Rack's
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 15-02-2015
 */

class InfoElementoRackController extends Controller implements TokenAuthenticatedController
{
    /*
     * @Secure(roles="ROLE_273-1)
     * 
     * Documentación para el método 'indexRackAction'.
     *
     * Metodo utilizado para retornar a la pagina principal de la administracion de Rack's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function indexRackAction()
    {
        $rolesPermitidos = array();

        //MODULO 273 - RACK
        if(true === $this->get('security.context')->isGranted('ROLE_273-4'))
        {
            $rolesPermitidos[] = 'ROLE_273-4'; //editar elemento RACK
        }
        if(true === $this->get('security.context')->isGranted('ROLE_273-8'))
        {
            $rolesPermitidos[] = 'ROLE_273-8'; //eliminar elemento RACK
        }
        if(true === $this->get('security.context')->isGranted('ROLE_273-6'))
        {
            $rolesPermitidos[] = 'ROLE_273-6'; //ver elemento RACK
        }
        if(true === $this->get('security.context')->isGranted('ROLE_273-2137'))
        {
            $rolesPermitidos[] = 'ROLE_273-2137'; //ver administracion unidad RACK
        }
        
        return $this->render('tecnicoBundle:InfoElementoRack:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /*
     * @Secure(roles="ROLE_273-2")
     * 
     * Documentación para el método 'newRackAction'.
     *
     * Metodo utilizado para retornar a la pagina de creacion de nuevo Rack
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function newRackAction()
    {
        $objElemento = new InfoElemento();
        $form   = $this->createForm(new InfoElementoRackType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoRack:new.html.twig', array(
                             'entity' => $objElemento,
                             'form' => $form->createView())
                            );
    }

    /**
     * @Secure(roles="ROLE_273-3")
     * 
     * Documentación para el método 'createRackAction'.
     *
     * Metodo utilizado para crear el nuevo Rack
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 08-09-2017 - Se ajusta creacion de Racks para que soporte esquemas de Data Center
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se modifican las validaciones por cambio en función ingresarRack
     */
    public function createRackAction()
    {
        $objRequest             = $this->get('request');
        $emInfraestructura      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $arrayParametros        = $objRequest->request->get('telconet_schemabundle_infoelementoracktype');
        $strTipoRack            = $objRequest->get('hd-tipo-rack');
        $strJsonDatos           = $objRequest->get('hd-racks-dc');
        $strNombreCanton        = $objRequest->get('hd-canton');
        $strNombreElemento      = $arrayParametros['nombreElemento'];
        $intModeloElementoId    = $arrayParametros['modeloElementoId'];
        $intNodoElementoId      = $arrayParametros['nodoElementoId'];
        $strDescripcionElemento = $arrayParametros['descripcionElemento'];      
        $form                   = $this->createForm(new InfoElementoRackType(), new InfoElemento());
        $serviceUtil            = $this->get('schema.Util');
        $form->bind($objRequest);
        $boolMensajeUsuario     = false;
        if ($form->isValid()) 
        {
            $emInfraestructura->getConnection()->beginTransaction();
            
            try
            {
                $arrayParametros                           = array();
                $arrayParametros['objRequest']             = $objRequest;
                $arrayParametros['emInfraestructura']      = $emInfraestructura;
                $arrayParametros['intModeloElementoId']    = $intModeloElementoId;
                $arrayParametros['intNodoElementoId']      = $intNodoElementoId;
                $arrayParametros['strTipoRack']            = $strTipoRack;
                
                //Creacion de RACKS unitarios relacionados con un NODO distinto a DATA CENTER
                if($strTipoRack == 'S')
                {
                    $arrayParametros['strNombreElemento']      = $strNombreElemento;
                    $arrayParametros['strDescripcionElemento'] = $strDescripcionElemento;
                    
                    $arrayIngresarRack  = $this->ingresarRack($arrayParametros);
                    if($arrayIngresarRack["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayIngresarRack['mensaje']);
                    }
                    $objElementoRack = $arrayIngresarRack["objElementoRack"];
                }
                else//Para Racks pertenecientes a DATA CENTER
                {
                    $arrayJson = json_decode($strJsonDatos);
                    
                    foreach($arrayJson as $jsonData)
                    {
                        $arrayParametros['strNombreFila']          = $jsonData->idFila;
                        $arrayParametros['strNombreElemento']      = $jsonData->nombreRack;
                        $arrayParametros['strDescripcionElemento'] = $jsonData->descripcionRack;
                        $arrayParametros['strDimensiones']         = $jsonData->dimensiones;
                        $arrayParametros['strNombreCanton']        = $strNombreCanton;
                        
                        if($jsonData->accion == 'agregar')
                        {
                            $arrayIngresarRack  = $this->ingresarRack($arrayParametros);
                            
                            if($arrayIngresarRack["status"] === "ERROR")
                            {
                                $boolMensajeUsuario = true;
                                throw new \Exception($arrayIngresarRack['mensaje']);
                            }
                            $objElementoRack = $arrayIngresarRack["objElementoRack"];
                        }
                        else//Si la accion es eliminar o liberar espacio de piso
                        {
                            $strEstado = '';

                            if($jsonData->accion == 'eliminar')
                            {
                                $strEstado = 'Eliminado';
                            } 
                            else
                            {
                                $strEstado = 'InactivoEspacio';
                            }
                            
                            $objElementoFila = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                 ->findOneByNombreElemento($jsonData->idFila);
                            
                            if(is_object($objElementoFila))
                            {
                                $objElementoFila->setEstado($strEstado);
                                $emInfraestructura->persist($objElementoFila);
                                $emInfraestructura->flush();
                            }
                        }
                    }
                }
                
                $emInfraestructura->commit();
                
                if($strTipoRack == 'S')
                {
                    return $this->redirect($this->generateUrl('elementorack_showRack', array('id' => $objElementoRack->getId())));
                }
                else
                {
                    return $this->redirect($this->generateUrl('elementorack'));
                }
            }
            catch (\Exception $ex) 
            {
                if ($emInfraestructura->getConnection()->isTransactionActive())
                {
                    $emInfraestructura->rollback();
                }
                
                $emInfraestructura->close();
                
                $serviceUtil->insertError(  'Telcos+', 
                                            'ajaxGuardarFactibilidadHousingAction', 
                                            $ex->getMessage(), 
                                            $objRequest->getSession()->get('user'), 
                                            $objRequest->getClientIp()
                                          );
                if($boolMensajeUsuario)
                {
                    $strMensajeError = $ex->getMessage();
                }
                else
                {
                    $strMensajeError = 'Existieron problemas al procesar la transacción, favor notificar a Sistemas.';
                }
                $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
                return $this->redirect($this->generateUrl('elementorack_newRack'));
            }
        } 
    }
    
    /**
     * Metodo encargado para crear los racks de acuerdo al tipo de rack ( Normal o DC ) enviado como parametros
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 07-09-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión 
     *                           y se modifica la función para devolver un arreglo y validar de  manera correcta el mensaje de error
     * 
     * @param Array $arrayParametros [
     *                                  objRequest
     *                                  emInfraestructura
     *                                  strTipoRack
     *                                  strNombreElemento
     *                                  intModeloElementoId
     *                                  strDescripcionElemento
     *                                  strNombreFila
     *                                  intNodoElementoId
     *                               ]
     * @return Array $arrayResultado[
     *                                  status              => estado del proceso
     *                                  mensaje             => mensaje de error
     *                                  $objElementoRack    => elemento rack
     *                              ]
     */
    private function ingresarRack($arrayParametros)
    {
        $objRequest        = $arrayParametros['objRequest'];
        $objSession        = $objRequest->getSession();
        $strTipoRack       = $arrayParametros['strTipoRack'];
        $emInfraestructura = $arrayParametros['emInfraestructura'];
        $strMensaje        = "";
        try
        {
            //verificar que el nombre del elemento no se repita
            $objElementoRepetido = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->findOneBy(array( "nombreElemento" => $arrayParametros['strNombreElemento'], 
                                                                        "estado" => "Activo"));

            if(is_object($objElementoRepetido))
            {
                if($strTipoRack == 'DC')
                {
                    $objEmpresaUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                         ->findOneBy(array('elementoId' => $objElementoRepetido->getId()));
                    if(is_object($objEmpresaUbica))
                    {
                        $objUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                          ->find($objEmpresaUbica->getUbicacionId()->getId());

                        if(is_object($objUbicacion))
                        {
                            $objParroquia = $emInfraestructura->getRepository('schemaBundle:AdmiParroquia')->find($objUbicacion->getParroquiaId());

                            if(is_object($objParroquia))
                            {
                                $strCantonExistente = $objParroquia->getCantonId()->getNombreCanton();

                                //Si el canton del cual viene la creacion es igual al canton del que esta registrado con el mismo nombre
                                //devuelve mensaje de alerta al usuario
                                if($strCantonExistente == $arrayParametros['strNombreCanton'])
                                {
                                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                                }
                            }
                        }
                    }
                }
                else
                {
                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                }
            }
            
            $objModeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $arrayParametros['intModeloElementoId']);
            $objElementoRack   = new InfoElemento();
            $objElementoRack->setNombreElemento($arrayParametros['strNombreElemento']);
            $objElementoRack->setDescripcionElemento($arrayParametros['strDescripcionElemento']);
            $objElementoRack->setModeloElementoId($objModeloElemento);
            $objElementoRack->setUsrResponsable($objSession->get('user'));
            $objElementoRack->setUsrCreacion($objSession->get('user'));
            $objElementoRack->setFeCreacion(new \DateTime('now'));
            $objElementoRack->setIpCreacion($objRequest->getClientIp());
            $objElementoRack->setEstado("Activo");
            $emInfraestructura->persist($objElementoRack);
            $emInfraestructura->flush();

            //buscar el interface Modelo
            $objInterfaceModelo = $emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                    ->findBy(array("modeloElementoId" => $arrayParametros['intModeloElementoId']));

            //se busca modelos de Unidades de Rack para poder crear las Unidades de Rack
            $objModeloElementoUDRack  = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                          ->findOneBy(array("nombreModeloElemento"=>"UDRACK"));

            foreach($objInterfaceModelo as $objIm)
            {
                $intCantidadInterfaces = $objIm->getCantidadInterface();

                for($i = 1; $i <= $intCantidadInterfaces; $i++)
                {
                    $objElemento = new InfoElemento();

                    $strNombreElemento = $i;

                    $objElemento->setNombreElemento($strNombreElemento);
                    $objElemento->setDescripcionElemento("Unidad de rack");
                    $objElemento->setModeloElementoId($objModeloElementoUDRack);
                    $objElemento->setUsrResponsable($objSession->get('user'));
                    $objElemento->setUsrCreacion($objSession->get('user'));
                    $objElemento->setFeCreacion(new \DateTime('now'));
                    $objElemento->setIpCreacion($objRequest->getClientIp());
                    $objElemento->setEstado("Activo");
                    $emInfraestructura->persist($objElemento);
                    $emInfraestructura->flush();
                    //relacion elemento
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($objElementoRack->getId());
                    $objRelacionElemento->setElementoIdB($objElemento->getId());
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("Rack contiene unidades");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objRelacionElemento);
                    $emInfraestructura->flush();
                }
            }

            //Si el tipo es Data Center se agrega la relacion entre la fila DC con el Rack
            if($strTipoRack == 'DC')
            {
                $objElementoFila = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->findOneByNombreElemento($arrayParametros['strNombreFila']);
                if(is_object($objElementoFila))
                {
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($objElementoFila->getId());
                    $objRelacionElemento->setElementoIdB($objElementoRack->getId());
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("Fila contiene Rack");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objRelacionElemento);

                    //Se activa la fila que es ocupada
                    $objElementoFila->setEstado('Activo');
                    $emInfraestructura->persist($objElementoFila);
                }
            }

            if(isset($arrayParametros['strDimensiones']) && !empty($arrayParametros['strDimensiones']))
            {
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objElementoRack->getId());
                $objDetalleElemento->setDetalleNombre("DIMENSION RACK");
                $objDetalleElemento->setDetalleValor($arrayParametros['strDimensiones']);
                $objDetalleElemento->setDetalleDescripcion("DIMENSIONES DE UN RACK");
                $objDetalleElemento->setUsrCreacion($objSession->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($objRequest->getClientIp());
                $objDetalleElemento->setEstado('Activo');
                $emInfraestructura->persist($objDetalleElemento);
                $emInfraestructura->flush();
            }                

            //relacion elemento
            $objRelacionElemento = new InfoRelacionElemento();
            $objRelacionElemento->setElementoIdA($arrayParametros['intNodoElementoId']);
            $objRelacionElemento->setElementoIdB($objElementoRack->getId());
            $objRelacionElemento->setTipoRelacion("CONTIENE");
            $objRelacionElemento->setObservacion("nodo contiene rack");
            $objRelacionElemento->setEstado("Activo");
            $objRelacionElemento->setUsrCreacion($objSession->get('user'));
            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objRelacionElemento);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoRack);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Se ingreso un Rack");
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);

            //tomar datos nodo
            $objNodoEmpresaElementoUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                 ->findOneBy(array("elementoId" => $arrayParametros['intNodoElementoId']));
            $objNodoUbicacion                = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                 ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                    "latitudElemento"       => 
                                                                                                    $objNodoUbicacion->getLatitudUbicacion(),
                                                                                                    "longitudElemento"      => 
                                                                                                    $objNodoUbicacion->getLongitudUbicacion(),
                                                                                                    "msjTipoElemento"       => "del nodo ",
                                                                                                    "msjTipoElementoPadre"  =>
                                                                                                    "que contiene al rack ",
                                                                                                    "msjAdicional"          => 
                                                                                                    "por favor regularizar en la administración"
                                                                                                    ." de Nodos"
                                                                                                  ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }

            //info ubicacion
            $objParroquia         = $emInfraestructura->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
            $objUbicacionElemento = new InfoUbicacion();
            $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
            $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
            $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
            $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
            $objUbicacionElemento->setParroquiaId($objParroquia);
            $objUbicacionElemento->setUsrCreacion($objSession->get('user'));
            $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
            $objUbicacionElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objUbicacionElemento);

            //empresa elemento ubicacion
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objSession->get('idEmpresa'));
            $objEmpresaElementoUbica->setElementoId($objElementoRack);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
            $objEmpresaElementoUbica->setUsrCreacion($objSession->get('user'));
            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $objEmpresaElementoUbica->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objEmpresaElementoUbica);

            //empresa elemento
            $objEmpresaElemento = new InfoEmpresaElemento();
            $objEmpresaElemento->setElementoId($objElementoRack);
            $objEmpresaElemento->setEmpresaCod($objSession->get('idEmpresa'));
            $objEmpresaElemento->setEstado("Activo");
            $objEmpresaElemento->setUsrCreacion($objSession->get('user'));
            $objEmpresaElemento->setIpCreacion($objRequest->getClientIp());
            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
            $emInfraestructura->persist($objEmpresaElemento);

            $emInfraestructura->flush();
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $objElementoRack    = null;
        }
        $arrayResultado = array("status" => $strStatus, "mensaje" => $strMensaje, "objElementoRack" => $objElementoRack);
        return $arrayResultado;
    }
    
    /*
     * @Secure(roles="ROLE_273-4")
     * 
     * Documentación para el método 'editRackAction'.
     *
     * Metodo utilizado para retornar a la pagina de edición de un Rack
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function editRackAction($id)
    {
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');
        $em        = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $objElemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -rack- que se quiere modificar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento     = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            $ubicacion         = $respuestaElemento['ubicacion'];
        }

        $formulario = $this->createForm(new InfoElementoRackType(), $objElemento);

        return $this->render('tecnicoBundle:InfoElementoRack:edit.html.twig', array(
                             'edit_form' => $formulario->createView(),
                             'rack' => $objElemento,
                             'ubicacion' => $ubicacion)
                            );
    }

    /*
     * @Secure(roles="ROLE_273-5")
     * 
     * Documentación para el método 'updateRackAction'.
     *
     * Metodo utilizado para actualizar el Rack
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function updateRackAction($id)
    {
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC            = $this->getDoctrine()->getManager('telconet');
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $request                = $this->get('request');
        $arrayParametros        = $request->request->get('telconet_schemabundle_infoelementoracktype');
        $strNombreElemento      = $arrayParametros['nombreElemento'];
        $intNodoElementoId      = $arrayParametros['nodoElementoId'];
        $strDescripcionElemento = $arrayParametros['descripcionElemento'];
        $intModeloElementoId    = $arrayParametros['modeloElementoId'];
        $intIdUbicacion         = $request->request->get('idUbicacion');
        $objModeloElemento      = $em->find('schemaBundle:AdmiModeloElemento', $intModeloElementoId);
        $boolUnidadesOcupadas   = false;
        $boolMensajeUsuario     = false;

        $em->beginTransaction();
        try
		{
            $objElemento    = $em->getRepository('schemaBundle:InfoElemento')->find($id);
            if(!$objElemento)
            {
                throw $this->createNotFoundException('Unable to find InfoElemento entity.');
            }
            //revisar si es cambio de modelo
            $intModeloAnterior = $objElemento->getModeloElementoId();
            $flag = 0;
            if($intModeloAnterior->getId() != $objModeloElemento->getId())
            {
                $objInterfaceModeloAnterior = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $intModeloAnterior->getId()));
                $objInterfaceModeloNuevo    = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                 ->findOneBy(array("modeloElementoId" => $objModeloElemento->getId()));

                $intCantAnterior    = $objInterfaceModeloAnterior->getCantidadInterface();
                $intCantNueva       = $objInterfaceModeloNuevo->getCantidadInterface();
                if ( $intCantAnterior > $intCantNueva )
                {
                    //se verifica si estan ocupadas las unidades
                    $objRelacionesElementoRackUnidad = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                          ->findBy(array("elementoIdA" => $objElemento->getId(),
                                                                         "estado"      =>"Activo"));
                    
                    foreach($objRelacionesElementoRackUnidad as $objRelacionElementoRackUnidad)
                    {
                        
                        $objElementoUnidadRack    = $em->getRepository('schemaBundle:InfoElemento')
                                                       ->find($objRelacionElementoRackUnidad->getElementoIdB());
                        if ((int)$objElementoUnidadRack->getNombreElemento() > $intCantNueva)
                        {
                            $objRelacionElementoRackUnidadDet = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                                   ->findBy(array("elementoIdA" => $objElementoUnidadRack->getId(),
                                                                                  "estado"      =>"Activo"));
                            if ($objRelacionElementoRackUnidadDet)
                            {
                                $boolUnidadesOcupadas = true;
                                break;
                            }
                            else
                            {
                                $objRelacionElementoRackUnidadEliminar = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                                            ->findOneBy(array("elementoIdB" => $objElementoUnidadRack->getId(),
                                                                                              "estado"      =>"Activo"));
                                $objRelacionElementoRackUnidadEliminar->setEstado('Eliminado');
                                $em->persist($objRelacionElementoRackUnidadEliminar);
                                $objElementoUnidadRack->setEstado('Eliminado');
                                $em->persist($objElementoUnidadRack);
                            }
                        }
                    }
                    //se valida bandera de unidades ocupadas
                    if ($boolUnidadesOcupadas)
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception('Se encuentran unidades ocupadas después de la unidad '.$intCantNueva.', favor revisar!');
                    }
                }
                else if ( $intCantNueva > $intCantAnterior )
                {
                    //se busca modelos de Unidades de Rack para poder crear las Unidades de Rack
                    $objModeloElementoUDRack  = $em->getRepository('schemaBundle:AdmiModeloElemento')
                                                   ->findOneBy(array("nombreModeloElemento"=>"UDRACK"));
                    //se crean las nuevas interfaces
                    for($i = $intCantAnterior+1; $i <= $intCantNueva; $i++)
                    {
                        //nueva unidad de rack
                        $objElementoUnidad = new InfoElemento();
                        $objElementoUnidad->setNombreElemento($i);
                        $objElementoUnidad->setDescripcionElemento("Unidad de rack");
                        $objElementoUnidad->setModeloElementoId($objModeloElementoUDRack);
                        $objElementoUnidad->setUsrResponsable($session->get('user'));
                        $objElementoUnidad->setUsrCreacion($session->get('user'));
                        $objElementoUnidad->setFeCreacion(new \DateTime('now'));
                        $objElementoUnidad->setIpCreacion($peticion->getClientIp());
                        $objElementoUnidad->setEstado("Activo");
                        $em->persist($objElementoUnidad);
                        $em->flush();
                        //relacion elemento
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($objElemento->getId());
                        $objRelacionElemento->setElementoIdB($objElementoUnidad->getId());
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Rack contiene unidades");
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($session->get('user'));
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                        $em->persist($objRelacionElemento);
                    }
                }

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

                $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array("elementoIdB" => $objElemento));
                //ver si se cambio de nodo
                $objNodoElementoAnterior = $objRelacionElemento->getElementoIdA();

                if($objNodoElementoAnterior != $intNodoElementoId)
                {
                    //cambiar la relacion elemento
                    $objRelacionElemento->setElementoIdA($intNodoElementoId);
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("nodo contiene rack");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($session->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($objRelacionElemento);

                    //tomar datos nodo
                    $objNodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                      ->findOneBy(array("elementoId" => $intNodoElementoId));
                    $objNodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                      ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());
                    
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objNodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objNodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al rack ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    
                    //cambiar ubicacion del dslam
                    $objParroquia         = $em->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
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
                $objHistorialElemento->setObservacion("Se modifico el Rack");
                $objHistorialElemento->setUsrCreacion($session->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($objHistorialElemento);

                $em->flush();
                $em->commit();

                return $this->redirect($this->generateUrl('elementorack_showRack', array('id' => $objElemento->getId())));
            }
            else
            {
                $boolMensajeUsuario = true;
                throw new \Exception('El elemento aún tiene servicios en puertos que ya no se van a usar, favor regularice!');
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
            return $this->redirect($this->generateUrl('elementorack_editRack', array('id' => $id)));
        }
    }

    /*
     * @Secure(roles="ROLE_273-8")
     * 
     * Documentación para el método 'deleteRackAction'.
     *
     * Metodo utilizado para eliminar un Rack
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function deleteRackAction()
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

            //obtener todas las unidades del rack
            $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                               ->findBy(array("elementoIdA"              => $objElemento,
                                                              "estado"                   => "Activo"
                                                             )
                                                       );
            //se valida si existen unidades ocupadas
            foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
            {
                $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objRelacionElementoUDRack->getElementoIdB());
                $objRelacionElementoRack       = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                    ->findOneBy(array("elementoIdA"             => $objElementoUnidadRackDet,
                                                                      "estado"                  => "Activo"
                                                                      )
                                                                );
                if ($objRelacionElementoRack)
                {
                    $em->getConnection()->rollback();
                    $em->getConnection()->close();
                    return $respuesta->setContent("UNIDADES OCUPADAS");
                }
                
            }

            //elemento
            $objElemento->setEstado("Eliminado");
            $objElemento->setUsrCreacion($session->get('user'));
            $objElemento->setFeCreacion(new \DateTime('now'));
            $objElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objElemento);

            //se valida si existen unidades ocupadas
            foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
            {
                $objRelacionElementoUDRack->setEstado("Eliminado");
                $em->persist($objRelacionElementoUDRack);
                $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objRelacionElementoUDRack->getElementoIdB());
                $objElementoUnidadRackDet->setEstado("Eliminado");
                $em->persist($objElementoUnidadRackDet);
            }

            //relacion elemento
            $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elementoIdB" => $objElemento));
            $objRelacionElemento[0]->setEstado("Eliminado");
            $objRelacionElemento[0]->setUsrCreacion($session->get('user'));
            $objRelacionElemento[0]->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento[0]->setIpCreacion($peticion->getClientIp());
            $em->persist($objRelacionElemento[0]);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setObservacion("Se elimino un Rack");
            $objHistorialElemento->setUsrCreacion($session->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($objHistorialElemento);

            $em->flush();
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('elementorack'));
            
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

    /*
     * @Secure(roles="ROLE_273-6")
     * 
     * Documentación para el método 'showRackAction'.
     *
     * Metodo utilizado para mostrar la pagina de información de un Rack
     * @param integer $id
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 30-05-2016   Se agrega recuperación de unidades de racks ocupadas para ser mostradas al usuario
     * 
     * @since 1.0
     */
    public function showRackAction($id)
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
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);

            $ipElemento     = $respuestaElemento['ipElemento'];
            $arrayHistorial = $respuestaElemento['historialElemento'];
            $objUbicacion   = $respuestaElemento['ubicacion'];
            $jurisdiccion   = $respuestaElemento['jurisdiccion'];
            
            //obtiene ocupacion de elemento
            $objRelacionesElementos = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                         ->findBy(array("elementoIdA" => $id,
                                                        "estado"      =>"Activo"),
                                                  array("id"           => "ASC")
                                                 );
            $unidadesRack = array();
            $intContador  = 0;
            $strUniOcu    = "";
            $strUniOcuTip = "";
            $strUniIni    = "";
            $strUniFin    = "";
            //se valida ocupacion de unidades de rack para ser mostradas al usuario
            foreach($objRelacionesElementos as $objRelacionElemento)
            {
                $intContador  = $intContador + 1;
                if ($strUniIni == "")
                {
                    $strUniIni = $intContador;
                }
                $objRelacionElementoRackUnidadDet = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                       ->findOneBy(array("elementoIdA" => $objRelacionElemento->getElementoIdB(),
                                                                         "estado"      =>"Activo"));
                if (!$objRelacionElementoRackUnidadDet)
                {
                    if ($strUniOcu != "")
                    {
                        $unidadesRack[]= array("numeroUnidad"   => $strUniIni.'-'.$strUniFin,
                                               "nombreElemento" => $strUniOcu?$strUniOcu:"DISPONIBLE",
                                               "tipoElemento"   => $strUniOcuTip?$strUniOcuTip:"");
                        $strUniOcu    = "";
                        $strUniOcuTip = "";
                        $strUniIni    = $intContador;
                        $strUniFin    = $intContador;
                    }
                    else
                    {
                        $strUniFin    = $intContador;
                    }
                }
                else
                {
                    $objElementoUnidadRackOcupada = $em->getRepository('schemaBundle:InfoElemento')
                                                       ->find($objRelacionElementoRackUnidadDet->getElementoIdB());
                    if ($strUniOcu == "")
                    {  
                        if ($intContador == 1)
                        {
                             $strUniFin = $intContador;
                        }
                        else
                        {
                            $unidadesRack[] = array("numeroUnidad"   => $strUniIni.'-'.$strUniFin,
                                                    "nombreElemento" => $strUniOcu?$strUniOcu:"DISPONIBLE",
                                                    "tipoElemento"   => $strUniOcuTip?$strUniOcuTip:"");
                            $strUniIni = $intContador;
                            $strUniFin = $intContador;
                        }
                        $strUniOcu    = $objElementoUnidadRackOcupada->getNombreElemento();
                        $strUniOcuTip = $objElementoUnidadRackOcupada->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                    }
                    else
                    {
                        if ($strUniOcu == $objElementoUnidadRackOcupada->getNombreElemento())
                        {
                            $strUniFin = $intContador;
                        }
                        else
                        {
                            $unidadesRack[]= array("numeroUnidad"   => $strUniIni.'-'.$strUniFin,
                                                   "nombreElemento" => $strUniOcu?$strUniOcu:"DISPONIBLE",
                                                   "tipoElemento"   => $strUniOcuTip?$strUniOcuTip:"");
                            $strUniOcu    = $objElementoUnidadRackOcupada->getNombreElemento();
                            $strUniOcuTip = $objElementoUnidadRackOcupada->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                            $strUniIni    = $intContador;
                            $strUniFin    = $intContador;
                        }
                    }
                }
            }
            $unidadesRack[]= array("numeroUnidad"   => $strUniIni.'-'.$strUniFin,
                                   "nombreElemento" => $strUniOcu?$strUniOcu:"DISPONIBLE",
                                   "tipoElemento"   => $strUniOcuTip?$strUniOcuTip:"");

        }

        return $this->render('tecnicoBundle:InfoElementoRack:show.html.twig', 
                             array(
                                'elemento'          => $objElemento,
                                'ipElemento'        => $ipElemento,
                                'historialElemento' => $arrayHistorial,
                                'ubicacion'         => $objUbicacion,
                                'jurisdiccion'      => $jurisdiccion,
                                'flag'              => $peticion->get('flag'),
                                'unidadesRack'      => $unidadesRack
                             )
                            );
    }

    /*
     * @Secure(roles="ROLE_273-2117")
     * 
     * Documentación para el método 'getEncontradosRackAction'.
     *
     * Metodo utilizado para obtener información de los Rack's
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2021 - Se define en un solo arreglo de parámetro el filtro para los Rack
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.2 17-04-2023 - Se agrega validación de filtro por empresa
     */
    public function getEncontradosRackAction()
    {
        set_time_limit(400000);
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $session            = $this->get('session');
        $em                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento       = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => "RACK"));
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
        $strPrefijoEmpresa  = $session->get('prefijoEmpresa');

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
            'prefijoEmpresa'    => $prefijoEmpresa,
        );
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonOlts($arrayParametros);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /*
     * @Secure(roles="ROLE_273-2118")
     * 
     * Documentación para el método 'obtenerDatosRackAction'.
     *
     * Metodo utilizado para obtener información del Rack
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-02-2015
     */
    public function obtenerDatosRackAction()
    {
        $respuesta = new Response();
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion   = $this->get('request');
        $idRack     = $peticion->get('idRack');
        $objJson    = $this->getDoctrine()
                           ->getManager("telconet_infraestructura")
                           ->getRepository('schemaBundle:InfoElemento')
                           ->generarJsonCargarDatosOlt($idRack, $em);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * Metodo encargado de obtener la matriz de filas y racks de acuerdo a la ciudad enviada como parametro
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 07-09-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInformacionRacksDCAction()
    {
        $objRequest        = $this->get('request');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayParametros                    = array();
        $arrayParametros['strNombreCanton'] = $objRequest->get('nombreCanton');
        $arrayPosiciones = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getArrayInformacionFilaRacks($arrayParametros);
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayPosiciones);
        
        return $objResponse;
    }

}