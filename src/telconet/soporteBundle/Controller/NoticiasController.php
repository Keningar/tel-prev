<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\PlantillaNotificacionExternaType;
use telconet\schemaBundle\Form\PlantillaNotificacionInternaType;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\soporteBundle\Service\PlantillaService;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NoticiasController extends Controller implements TokenAuthenticatedController
{

    /**
     * @Secure(roles="ROLE_84-1")
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());

        //se agrega control de roles permitidos
        $rolesPermitidos = array();
        //MODULO 259 - Soporte/AdministracionNoticias
        if(true === $this->get('security.context')->isGranted('ROLE_259-1857'))
        {
            $rolesPermitidos[] = 'ROLE_259-1857'; //AdministracionNoticias
        }

        return $this->render('soporteBundle:Noticias:index.html.twig', array(
                'item' => $entityItemMenu,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_84-2")
     */
    public function newAction()
    {
        $session = $this->get('request')->getSession();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());

        $form = $this->createForm(new PlantillaNotificacionExternaType());

        return $this->render('soporteBundle:Noticias:new.html.twig', array(
                'item' => $entityItemMenu,
                'form' => $form->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_84-3")
     */
    public function createAction()
    {
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");

        $request = $this->getRequest();
        $peticion = $this->get('request');
        $sessio = $peticion->getSession();
        $empresaCod = $sessio->get('idEmpresa');

        $form = $this->createForm(new PlantillaNotificacionExternaType());
        $form->handleRequest($request);

        if($form->isValid())
        {
            $emComunicacion->getConnection()->beginTransaction();
            // Try and make the transaction
            try
            {
                $parametros = $peticion->get('telconet_schemabundle_plantillaNotificacionExternatype');
                $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
                $entityClaseDocumento = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                    array('nombreClaseDocumento' => 'Notificacion Interna Noticia'));
                $infoDocumento = new InfoDocumento();
                $infoDocumento->setMensaje($parametros['plantilla_mail']);
                $infoDocumento->setNombreDocumento($parametros['nombrePlantilla']);
                $infoDocumento->setClaseDocumentoId($entityClaseDocumento);
                $infoDocumento->setFeCreacion(new \DateTime('now'));
                $infoDocumento->setEstado("Activo");
                $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
                $infoDocumento->setIpCreacion($peticion->getClientIp());
                $infoDocumento->setEmpresaCod($empresaCod);
                $fechaDesde = $parametros['fecha_desde'];
                $fechaHasta = $parametros['fecha_hasta'];
                $infoDocumento->setFechaPublicacionDesde(new \DateTime($fechaDesde));
                $infoDocumento->setFechaPublicacionHasta(new \DateTime($fechaHasta));
                $emComunicacion->persist($infoDocumento);
                $emComunicacion->flush();

                $boolTodoBien = true;

                if($infoDocumento && $infoDocumento->getId())
                {
                    if($_FILES && count($_FILES) > 0)
                    {
                        if(isset($_FILES["archivo"]))
                        {
                            $archivoSubido = $_FILES["archivo"];
                            if($archivoSubido && count($archivoSubido) > 0)
                            {
                                $tamano = $archivoSubido['size'];
                                $tipo = $archivoSubido['type'];
                                $archivo = $archivoSubido['name'];

                                $arrayArchivo = explode('.', $archivo);
                                $countArray = count($arrayArchivo);
                                $extArchivo = $arrayArchivo[$countArray - 1];

                                //$prefijo = substr(md5(uniqid(rand())),0,6);

                                if($archivo != "")
                                {
                                    $nuevoNombre = "imagen_" . $infoDocumento->getId() . "." . $extArchivo;
                                    $destino = $this->getPathImages() . $nuevoNombre;

                                    if(copy($archivoSubido['tmp_name'], $destino))
                                    {
                                        $infoDocumento->setUbicacionLogicaDocumento($nuevoNombre);
                                        $infoDocumento->setUbicacionFisicaDocumento($destino);
                                        $emComunicacion->persist($infoDocumento);
                                        $emComunicacion->flush();

                                        //exec("chmod $destino 0777");

                                        $boolTodoBien = true;
                                        //$status = "Archivo subido: <b>".$archivo."</b>";
                                    }
                                    else
                                    {
                                        $boolTodoBien = false;
                                        //$status = "Error al subir el archivo";
                                    }
                                }
                            }//FIN IF ARCHIVO SUBIDO
                        }//FIN IF ARCHIVO
                    }//FIN IF FILES
                }

                if($boolTodoBien)
                {
                    $emComunicacion->getConnection()->commit();
                    return $this->redirect($this->generateUrl('noticias_show', array('id' => $infoDocumento->getId())));
                }
                else
                {
                    $emComunicacion->getConnection()->rollback();
                    $emComunicacion->getConnection()->close();

                    $parametros = array(
                        'item' => $entityItemMenu,
                        'entity' => $infoDocumento,
                        'form' => $form->createView()
                    );

                    return $this->render('soporteBundle:Noticias:new.html.twig', $parametros);
                }//FIN ERROR
            }
            catch(Exception $e)
            {
                // Rollback the failed transaction attempt
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
        }

        $parametros = array(
            'item' => $entityItemMenu,
            'entity' => $infoDocumento,
            'form' => $form->createView()
        );

        return $this->render('soporteBundle:Noticias:new.html.twig', $parametros);
    }

    /**
     * @Secure(roles="ROLE_84-6")
     */
    public function showAction($id)
    {
        $session = $this->get('request')->getSession();

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());

        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);
        if(!$documento)
        {
            throw $this->createNotFoundException('No se encuentra la plantilla.');
        }

        $fechaDesde = $documento->getFechaPublicacionDesde()->format('Y-m-d H:i:s');
        $fechaHasta = $documento->getFechaPublicacionHasta()->format('Y-m-d H:i:s');
        $parametros = array(
            'item' => $entityItemMenu,
            'documento' => $documento,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        );

        return $this->render('soporteBundle:Noticias:show.html.twig', $parametros);
    }

    /**
     * @Secure(roles="ROLE_84-7")
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $ssesion = $peticion->getSession();

        $codEmpresa = "";

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $tipo = $peticion->query->get('tipo');
        $nombre = $peticion->query->get('nombre');
        $estado = $peticion->query->get('estado');
        $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $entityClaseDocumento = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
            array('nombreClaseDocumento' => 'Notificacion Interna Noticia'));
        if($entityClaseDocumento)
        {
            // Obtener listado de plantillas servicio PlantillaService
            /* @var servicioPlantilla PlantillaService */
            $servicioPlantilla = $this->get('soporte.ListaPlantilla');
            $respuestaListaPlantillas = $servicioPlantilla->listarPlantillas($entityClaseDocumento->getId(), $nombre, $estado, $codEmpresa, $start, $limit);
            $respuesta->setContent($respuestaListaPlantillas);
        }
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_84-8")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $request = $this->getRequest();

        $entity = $em->getRepository('schemaBundle:InfoDocumento')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }

        $entity->setEstado("Eliminado");
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $em->flush();

        return $this->redirect($this->generateUrl('noticias_principal'));
    }

    /**
     * @Secure(roles="ROLE_84-8")
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $peticion = $this->get('request');

        $parametro = $peticion->get('param');

        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $array_valor = explode("|", $parametro);
        foreach($array_valor as $id):

            if(null == $entity = $em->find('schemaBundle:InfoDocumento', $id))
            {
                $respuesta->setContent("No existe la entidad");
            }
            else
            {
                $entity->setEstado("Eliminado");
                $em->persist($entity);

                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_84-4")
     */
    public function editAction($id)
    {
        $session = $this->get('request')->getSession();

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");

        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());

        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);

        if(!$documento)
        {
            throw $this->createNotFoundException('Unable to find Plantilla entity.');
        }

        $form = $this->createForm(new PlantillaNotificacionExternaType());
        $fechaDesde = $documento->getFechaPublicacionDesde()->format('Y-m-d H:i:s');
        $fechaHasta = $documento->getFechaPublicacionHasta()->format('Y-m-d H:i:s');
        return $this->render('soporteBundle:Noticias:edit.html.twig', array(
                'item' => $entityItemMenu,
                'form' => $form->createView(),
                'documento' => $documento,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta
        ));
    }

    /**
     * @Secure(roles="ROLE_84-5")
     */
    public function updateAction($id)
    {
        $request = $this->getRequest();
        $peticion = $this->get('request');

        $form = $this->createForm(new PlantillaNotificacionExternaType());
        $form->handleRequest($request);

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");

        if($form->isValid())
        {
            $emComunicacion->getConnection()->beginTransaction();
            $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);
            // Try and make the transaction
            try
            {
                $parametros = $peticion->get('telconet_schemabundle_plantillaNotificacionExternatype');

                //$tipo = $parametros['tipo'];  //Se obtiene el tipo de la plantilla obtenido del comboBox
                //$clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneByNombreClaseDocumento($tipoPlantilla);
                //$clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneById($tipo);
                $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
                $entityClaseDocumento = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                    array('nombreClaseDocumento' => 'Notificacion Interna Noticia'));

                $documento->setMensaje($parametros['plantilla_mail']);
                $documento->setNombreDocumento($parametros['nombrePlantilla']);
                $documento->setClaseDocumentoId($entityClaseDocumento);
                $documento->setFechaPublicacionDesde(new \DateTime($parametros['fecha_desde']));
                $documento->setFechaPublicacionHasta(new \DateTime($parametros['fecha_hasta']));

                $emComunicacion->persist($documento);
                $emComunicacion->flush();

                $emComunicacion->getConnection()->commit();
                return $this->redirect($this->generateUrl('noticias_show', array('id' => $documento->getId())));
                
            }
            catch(Exception $e)
            {
                // Rollback the failed transaction attempt
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
        }

        $parametros = array(
            'item' => $entityItemMenu,
            'entity' => $documento,
            'form' => $form->createView()
        );

        return $this->render('soporteBundle:Noticias:new.html.twig', $parametros);
    }

    /**
     *  
     * Metodo encargado de procesar imagenes que pueden ser incluidas en Noticias, utilizan el microservicio 
     * de NFS desde la opción Notificaciones > Noticias > Nueva Noticia
     * 
     * @return json con resultado del proceso
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 22-06-2021 Se realizan ajustes para subir archivos al NFS, además se renombran 
     * archivos que tienen caracteres especiales y espacios antes de ser subidos
     * 
     * @Secure(roles="ROLE_84-525")
     * 
     */
    public function fileUploadAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');

        $peticion = $this->get('request');
        $strUser = $peticion->getSession()->get('user');
        $strPrefijoEmpresa  = $peticion->getSession()->get('prefijoEmpresa');
        $strEmpresaCod = $peticion->getSession()->get('idEmpresa');
        $strIp = $peticion->getClientIp();
        $objServiceUtil = $this->get('schema.Util');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $boolEsExito = false;

        $emComunicacion->getConnection()->beginTransaction();

        if($_FILES && !empty($_FILES))
        {
            $archivoSubido = $_FILES["archivo"];
            if($archivoSubido && !empty($archivoSubido))
            {
                $strTamanio = $archivoSubido['size'];
                $fltTamanioKb = round(((int)$strTamanio)/1024,2);
                $strTipo    = $archivoSubido['type'];
                $strDestinoTmp = $archivoSubido['tmp_name'];
                $strArchivo = $archivoSubido['name'];

                $arrayArchivo = explode('.', $strArchivo);
                $countArray = count($arrayArchivo);
                $nombreArchivo = $arrayArchivo[0];
                $extArchivo = $arrayArchivo[$countArray - 1];

                $prefijo = substr(md5(uniqid(rand())), 0, 6);

                if($strDestinoTmp != "")
                {
                    $strNuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;

                    $strPatronABuscar = '/[^a-zA-Z0-9._-]/';
                    $strCaracterReemplazo = '_';
                    $strNuevoNombre = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombre);

                    $strFile         = base64_encode(file_get_contents($strDestinoTmp));
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $strPrefijoEmpresa,
                                            'strApp'               => "TelcosWeb",
                                            'arrayPathAdicional'   => [],
                                            'strBase64'            => $strFile,
                                            'strNombreArchivo'     => $strNuevoNombre,
                                            'strUsrCreacion'       => $strUser,
                                            'strSubModulo'         => "Noticias");

                    $arrayRespNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);

                    if ($arrayRespNfs['intStatus'] == 200 )
                    {
                        $strUrlArchivo = $arrayRespNfs['strUrlArchivo'];
                            
                        $entityClaseDocumento = $emComunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                            array('nombreClaseDocumento' => 'Notificacion Int. Noticia - Imagen normal'));
                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setMensaje("Imagen de tamanio normal para asociar al modulo de Noticias");
                        $objInfoDocumento->setNombreDocumento($strNuevoNombre);
                        $objInfoDocumento->setClaseDocumentoId($entityClaseDocumento);
                        $objInfoDocumento->setUbicacionLogicaDocumento($strTipo.' '.$fltTamanioKb.'Kb');
                        $objInfoDocumento->setUbicacionFisicaDocumento($strUrlArchivo);
                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumento->setEstado("Activo");
                        $objInfoDocumento->setUsrCreacion($strUser);
                        $objInfoDocumento->setIpCreacion($strIp);
                        $objInfoDocumento->setEmpresaCod($strEmpresaCod);
                        $emComunicacion->persist($objInfoDocumento);
                        $emComunicacion->flush();
                        $emComunicacion->getConnection()->commit();
                        
                        $objResultado = '{"success":true,"fileName":"' . $strNuevoNombre . '","fileSize":' . $fltTamanioKb . '}';
                        $boolEsExito = true;
                           
                    }                    
                }                    
            }//FIN IF ARCHIVO SUBIDO
        }//FIN IF FILES

        if (!$boolEsExito)
        {
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $objResultado = '{"success":false,"message":"Ocurrio un error al procesar la imagen en el sistema","fileSize":' . 0 . '}'; 
        }            

        $respuesta->setContent($objResultado);
        return $respuesta;
    }

    /**
     * Metodo encargado de enlistar imágenes que pueden ser incluidas en Noticias, 
     * desde la opción Notificaciones > Noticias > Nueva Noticia
     * 
     * @return json con resultado del proceso
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 22-06-2021 Se realizan ajustes para consultar las imágenes que han sido subidas a NFS
     * desde el módulo de Noticias
     *  
     * @Secure(roles="ROLE_84-526")
     */
    public function listarArchivosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $host = $this->container->getParameter('host');
        $objPeticion = $this->get('request');
        $strEmpresaCod = $objPeticion->getSession()->get('idEmpresa');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');

        $entityClaseDocumentoNormal = $emComunicacion->getRepository('schemaBundle:AdmiClaseDocumento')
            ->findOneBy(array('nombreClaseDocumento' => 'Notificacion Int. Noticia - Imagen normal'));

        $arrayParametros = array('tipo'    => $entityClaseDocumentoNormal->getId(),
                                 'empresa' => $strEmpresaCod,
                                 'estado'  => 'Activo');

        $arrayImagenes = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getDocumentos($arrayParametros,'','');

        if( $arrayImagenes['total'] > 0 )
        {
            foreach($arrayImagenes['registros'] as $objImagenNormal)
            {
                $strNombreImagenNormal = $objImagenNormal->getNombreDocumento();

                $arrayDetalleImagen = explode(' ', $objImagenNormal->getUbicacionLogicaDocumento());
                $strRutaImagen = $objImagenNormal->getUbicacionFisicaDocumento();
                $strPesoImagen = $arrayDetalleImagen[1];
                $arrayNombreImagen = explode('.', $strNombreImagenNormal);
                $strNombreImagen = $arrayNombreImagen[0];
                $strExtensionImagen = strtoupper($arrayNombreImagen[1]);
                $strExtensionImagenOficial = strtoupper($arrayNombreImagen[1]);

                $arrayImagenesNoticias[] = array(
                    'name' => $strNombreImagen,
                    'thumb' => $strNombreImagenNormal,
                    'imagen_mini' => $strRutaImagen,
                    'imagen_media' => $strRutaImagen,
                    'host_base' => $host,
                    'url' => $strRutaImagen,
                    'ext' => $strExtensionImagen,
                    'type' => 'image',
                    'extension' => $strExtensionImagenOficial,
                    'dimension' => '-',
                    'peso' => $strPesoImagen,
                );
            }
        }

        $objResultado = json_encode($arrayImagenesNoticias);

        $respuesta->setContent($objResultado);
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_84-526")    //Porque va esto????
     */
    /*     * ***************************************************************************** */
    //		OBTENER LAS PLANTILLAS DE SMS O CORREO PARA BUSQUEDA
    /*     * ****************************************************************************** */
    public function getTipoNoticiasAction()
    {

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:AdmiClaseDocumento')
            ->generarJsonEntidades($nombre, $estado, $start, $limit);

        $respuesta->setContent($objJson);


        return $respuesta;
    }

    /*     * ***************************************************************************** */

    //		OBTENER LAS PLANTILLAS DE SMS O CORREO PARA BUSQUEDA
    /*     * ****************************************************************************** */
    public function getPathImages()
    {
        return $this->container->getParameter("path_telcos") . "telcos/web/public/uploads/imagesPlantilla/";
    }


}
