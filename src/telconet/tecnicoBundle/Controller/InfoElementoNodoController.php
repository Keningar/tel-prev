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
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoEspacioFisico;
use telconet\schemaBundle\Entity\InfoMedidor;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoContactoNodo;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoDocumentoTag;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Form\InfoElementoNodoType;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleTareaElemento;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoCuadrillaTarea;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
use telconet\schemaBundle\Form\AdmiMotivoType;
use telconet\schemaBundle\Form\PreClienteType;
use telconet\schemaBundle\Form\InfoDocumentoType;
use telconet\seguridadBundle\Service\CryptService;
use telconet\comercialBundle\Service\InfoPersonaFormaContactoService;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoBitacoraAccesoNodo;
use telconet\schemaBundle\Form\InfoBitacoraAccesoNodoType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \PHPExcel_IOFactory;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Alignment;
use \PHPExcel;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Border;
use \PHPExcel_Worksheet_MemoryDrawing;

class InfoElementoNodoController extends Controller
{ 
    private $UPLOAD_PATH = 'public/uploads/documentos/';

    /**
     * @Secure(roles="ROLE_154-1")
     *
     * @author Germán valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 21-06-2021 - Se agrega el departamento, canton y oficina en sesión.
     * @since 1.0
     */
    public function indexNodoAction()
    {
        $objSession                  = $this->get('request')->getSession();
        $strPrefijoEmpresaSession    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdDepartamentoUsrSession = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $intIdOficinaSesion          = $objSession->get('idOficina')      ? $objSession->get('idOficina')      : 0;
        $emComercial                 = $this->getDoctrine()->getManager();
        $intIdCantonUsrSession       = 0;

        if ($intIdOficinaSesion)
        {
            $objOficinaSesion      = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            $intIdCantonUsrSession = is_object($objOficinaSesion) ? $objOficinaSesion->getCantonId() : 0;
        }

        $rolesPermitidos = array();

        if(true === $this->get('security.context')->isGranted('ROLE_154-2197'))//crear
        {
            $rolesPermitidos[] = 'ROLE_154-2197';
        } 
        if(true === $this->get('security.context')->isGranted('ROLE_154-2198'))//eliminar 
        {
            $rolesPermitidos[] = 'ROLE_154-2198';
        } 
        if(true === $this->get('security.context')->isGranted('ROLE_154-2199'))//editar 
        {
            $rolesPermitidos[] = 'ROLE_154-2199';
        } 
        if(true === $this->get('security.context')->isGranted('ROLE_154-6'))//show 
        {
            $rolesPermitidos[] = 'ROLE_154-6';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_154-6817'))
        {
            $rolesPermitidos[] = 'ROLE_154-6817'; 
        }
        /*Ingresar Periodo Mantenimiento*/
        if(true === $this->get('security.context')->isGranted('ROLE_154-7097'))
        {
            $rolesPermitidos[] = 'ROLE_154-7097';
        }

        return $this->render('tecnicoBundle:InfoElementoNodo:index.html.twig', array(
            'rolesPermitidos'             => $rolesPermitidos,
            'strPrefijoEmpresaSession'    => $strPrefijoEmpresaSession,
            'intIdDepartamentoUsrSession' => $intIdDepartamentoUsrSession,
            'intIdCantonUsrSession'       => $intIdCantonUsrSession
        ));
    }

    /**
     * ingresaInfoMantenimientoAction
     *
     * Método que se encarga de recibir la peticion, formatear los parámetros y enviarlos al servicio
     * para su ejecución.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 30-01-2020 - Versión Inicial.
     *
     * @return JsonResponse
     */
    public function ingresaInfoMantenimientoAction()
    {
        $objRequest             = $this->get('request');
        $serviceInfoelemento    = $this->get('tecnico.infoelemento');

        $intIdElemento          = $objRequest->get('idElemento');
        $intPeriodo             = $objRequest->get('intPeriodo');
        $strFechaProxMan        = $objRequest->get('strFechaProxMan');

        $arrayParams            = array(
            'intIdElemento'     => $intIdElemento,
            'intPeriodo'        => $intPeriodo,            'intIdElemento'     => $intIdElemento,
            'intPeriodo'        => $intPeriodo,
            'strFechaProxMan'   => $strFechaProxMan,
            'strFechaProxMan'   => $strFechaProxMan,
            'objRequest'        => $objRequest
        );

        return new JsonResponse($serviceInfoelemento->ingresaInfoMantenimientoNodo($arrayParams));

    }

    /**
     * getCicloMantenimientoNodoAction
     *
     * Método que se encarga de retornar un JSON con los valores del parámetro 'CICLO MANTENIMIENTO'
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 11-02-2020 - Versión Inicial.
     *
     * @return JsonResponse
     */
    public function getCicloMantenimientoNodoAction()
    {

        $objRequest             = $this->get('request');
        $serviceInfoelemento    = $this->get('tecnico.infoelemento');

        $arrayParams            = array(
            'objRequest'        => $objRequest
        );

        return new JsonResponse($serviceInfoelemento->getCicloMantenimientoNodo($arrayParams));
    }
    
    /**
      * getNewNodoParametros
      *
      * Método que devuelve array que es renderizado en twig que contiene los formularios para crear nuevo NODO
      * 
      * @return array                                                                         
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015   
      *
      * @author Antonio Ayala <afayala@telconet.ec>
      * @version 1.1 14-06-2019. Se agrego nuevo campo de medidor eléctrico
      * 
      * @author Adrian Ortega <amortega@telconet.ec>
      * @version 1.2 09-09-2019 - Se agrega campo de mantenimiento de una torre, cuando el elemento sea tipo Radio.
      */ 
    public function getNewNodoParametros($error='')
    {
        $arrayMotivo = array();  
        
        $em                = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $emComunicacion    = $this->get('doctrine')->getManager('telconet_comunicacion');
        
        $objMotivos               = $em->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorModuloPorItemMenuPorAccion('nodo',null,'index');         
        $arrayObjTipoMedio        = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->getTiposMedios('','','Activo','','');                                 
        $arrayObjDetalles         = $emInfraestructura->getRepository('schemaBundle:AdmiDetalle')->getDetalles('','CLASE NODO','Activo','','');  
        $arrayObjTipoMedidor      = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedidor')->getTiposMedidores('','Activo','','');  
        $arrayObjMedidorElectrico
                             = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedidor')->getMedidoresElectricos('PROYECTO NODO','Activo','');
        $arrayObjClaseMedidor     = $emInfraestructura->getRepository('schemaBundle:AdmiClaseMedidor')->getClasesMedidores('','Activo','','');  
        $arrayObjTipoRol          = $emGeneral->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto Nodo'); 
        $arrayObjTimeMantenimiento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->get('MANTENIMIENTO TORRES','','','CICLO MANTENIMIENTO','','','','','','');
        
        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }
        
        $arrayTagDocumento = array();
        $objTagDocumento  = $emComunicacion->getRepository('schemaBundle:AdmiTagDocumento')->findByEstado("Activo");                   
        foreach ( $objTagDocumento as $objTagDocumento )
        {   
           $arrayTagDocumento[$objTagDocumento->getId()] = $objTagDocumento->getTagDocumento();
        }
                
        foreach($objMotivos as $motivo)
        {            
            $arrayMotivo[$motivo->getId()] = $motivo->getNombreMotivo();
        }        
                                        
        $entity        = new InfoElemento();                
        $entityMotivo  = new AdmiMotivo();        
        $entityPersona = new InfoPersona();       
        $entityDoc     = new InfoDocumento();       
        
        $form         = $this->createForm(new InfoElementoNodoType(), $entity);
        $formMotivo   = $this->createForm(new AdmiMotivoType(array("motivos"=>$arrayMotivo)), $entityMotivo);  
        $formContacto = $this->createForm(new PreClienteType(), $entityPersona);
        $form_documentos = $this->createForm(new InfoDocumentoType(array('validaFile'=>true,
                                                                         'arrayTipoDocumentos'=>$arrayTipoDocumentos,
                                                                         'arrayTagDocumentos' =>$arrayTagDocumento), $entityDoc)); 
        
        return array(
            'entity'            => $entity,
            'form'              => $form->createView(),
            'formMotivo'        => $formMotivo->createView(),
            'formPersona'       => $formContacto->createView(),
            'form_documentos'   => $form_documentos->createView(),
            'tipoMedio'         => $arrayObjTipoMedio,
            'claseNodo'         => $arrayObjDetalles,
            'tipoMedidor'       => $arrayObjTipoMedidor,
            'medidorElectrico'  => $arrayObjMedidorElectrico,
            'claseMedidor'      => $arrayObjClaseMedidor,
            'tipoRol'           => $arrayObjTipoRol,
            'tags'              => $arrayTagDocumento,
            'error'             => $error,
            'cicloMantenimiento' => $arrayObjTimeMantenimiento
        );
    }
    
    /**
     * 
     * Mostrar los roles posibles vinculados a los contacto de los nodos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 02-08-2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetPersonaContactoRolesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $emGeneral        = $this->get('doctrine')->getManager('telconet_general');
                
        $arrayObjTipoRol  = $emGeneral->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto Nodo');      
        
        if($arrayObjTipoRol)
        {
            $total = count($arrayObjTipoRol);
            
            $array = array();
                        
            foreach($arrayObjTipoRol as $data)
            {
                $array[] = array('idRol'=>$data->getId(),'nombreRol'=>$data->getDescripcionRol());
            }
            
            $data     = json_encode($array);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
        }
      
        $respuesta->setContent($resultado);
                
        return $respuesta;
    }
    
    /**
      * ajaxGetMotivosAction
      *
      * Método que devuelve json con los motivos de la solicitud que se crea con el nodo
      * 
      * @return array                                                                         
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015           
      */
    public function ajaxGetMotivosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em  = $this->get('doctrine')->getManager('telconet');
                
        $arrayMotivos   = $em->getRepository('schemaBundle:AdmiMotivo')
                           ->findMotivosPorModuloPorItemMenuPorAccion('nodo',null,'index');     
        
        if($arrayMotivos)
        {
            $total = count($arrayMotivos);
            
            $array[] = array('idMotivo'=>'Todos','nombreMotivo'=>'Todos');
            
            foreach($arrayMotivos as $data)
            {
                $array[] = array('idMotivo'=>$data->getId(),'nombreMotivo'=>$data->getNombreMotivo());
            }
            
            $data     = json_encode($array);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
        }
      
        $respuesta->setContent($resultado);
                
        return $respuesta;
    }
    
    /**
      * ajaxGetClaseNodoAction
      *
      * Método que devuelve json con los detalles determinados como clase de nodo
      * 
      * @return array                                                                         
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015           
      */
    public function ajaxGetClaseNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em  = $this->get('doctrine')->getManager('telconet_infraestructura');
                
        $arrayDetalles   = $em->getRepository('schemaBundle:AdmiDetalle')->getDetalles('','CLASE NODO','Activo','','');  
        
        if($arrayDetalles)
        {
            $total = count($arrayDetalles);
            
            $array[] = array('idDetalle'=>'Todos','nombreDetalle'=>'Todos');
            foreach($arrayDetalles as $data)
            {
                $array[] = array('idDetalle'=>$data->getId(),'nombreDetalle'=>$data->getNombreDetalle());
            }
            
            $data     = json_encode($array);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
        }
      
        $respuesta->setContent($resultado);
                
        return $respuesta;
    }            
    
    public function newNodoAction()
    {                      
        return $this->render('tecnicoBundle:InfoElementoNodo:new.html.twig',$this->getNewNodoParametros());
    }
    
    /**
     * createNodoAction
     *
     * Método que ingresa toda la informacion de los nodos en la base de datos  
     * 
     * @version 1.0 - Version Inicial
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.2 25-02-2015 - Se modifica metodo ingresando informacion mas completa de los nodos de acuerdo a como se encuentra
     *                           Aplicativo de Nodos que se migra
     *                         - Se valida si razon social existe para poder actualizar
     *                         - Se establece que los nodos sean visibles para TN y MD
     *                         - Se establece por default tipo de documento 9 de imagenes
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.3 21-03-2016 - Se modifica ingreso de nodo con nueva ruta para las imagenes por nodo
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.4 20-07-2016 - Se invoca service para actualizar contactos a la persona ( existente ) en caso de ser necesario
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 01-08-2016 - Se crea RACK cuando el nodo sea de tipo CLIENTE ESPECIFICO
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 28-11-2018 - Se agregan validaciones para gestionar productos de la empresa TNP
     * @since 1.6
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 17-06-2019 - Se agrega campo de medidor eléctrico.
     * Se eliminó el campo cobertura nodo.
     * Se eliminó el campo género.
     * Se eliminó el campo título.
     * 
     * @author Adrian Ortega <amortega@telconet.ec>
     * @version 1.9 09-09-2019 - Se ingresan detalles de mantenimiento preventivo solo cuando el elemento sea tipo Radio.
     *
     * @author Gabriela Mora <gmora@telconet.ec>
     * @version 1.10 11-10-2022 - Se añade estado activo a espacio físico.
     * 
     * @Secure(roles="ROLE_154-2197")
     */
    public function createNodoAction()
    {
        $request = $this->get('request');
        
        $em             = $this->get('doctrine')->getManager('telconet_infraestructura'); 
        $emComercial    = $this->get('doctrine')->getManager('telconet'); 
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion'); 
        
        $em->getConnection()->beginTransaction(); 
        $emComunicacion->getConnection()->beginTransaction();   
                
        try
        {                                       
            $empresaCod    = $request->getSession()->get('idEmpresa');

            $parametros         = $request->get('telconet_schemabundle_infoelementonodotype');
            $parametrosMotivo   = $request->get('telconet_schemabundle_admimotivotype');
            $parametrosContacto = $request->get('preclientetype');                          

            //Informacion Tab Datos Generales
            $nombreElemento     = $parametros['nombreElemento'];
            $modeloElementoId   = $parametros['modeloElementoId'];  
            
            $observacionElemento    = $parametros['observacion'];
            $motivoSolicitudId      = $parametrosMotivo['nombreMotivo']; //Solicitud
            $esEdificio             = $request->get('cmb_es_edificio'); //detalle
            $alturaMaxima           = $request->get('txt_altura_maxima'); //detalle
            $claseNodo              = $request->get('cmb_clase_nodo');  //detalle
            $numeroMedidor          = $request->get('txt_numero_medidor');
            $claseMedidor           = $request->get('cmb_clase_medidor'); 
            $tipoMedidor            = $request->get('cmb_tipo_medidor');
            $tipoMedioNodo          = $request->get('hd_info_tipoMedio'); //Tipo json 
            $strCicloMantenimiento = $request->get('cmb_ciclo_mantenimiento');

            //Informacion Tab Datos Local                          
            $parroquiaId        = $parametros['parroquiaId'];
            $direccionUbicacion = $parametros['direccionUbicacion'];
            $alturaSnm          = $parametros['alturaSnm'];
            $descripcionElemento = $parametros['descripcionElemento'];
            $accesoPermanente   = $parametros['accesoPermanente']; 
            $longitudUbicacion  = $parametros['longitudUbicacion'];
            $latitudUbicacion   = $parametros['latitudUbicacion'];
            $arrayTipoEspacio    = $request->get('hd_info_espacio')!=""?json_decode($request->get('hd_info_espacio')):""; //Tipo json

            //Informacion Tab Datos Contacto
            $contactoNodoExiste = $parametrosContacto['yaexiste'];
            $contactoNodoExisteRol = $parametrosContacto['yaexisteRol'];
            $contactoNodoId     = $parametrosContacto['idPersona'];
            $tipoContactoNodo   = $request->get('cmb_tipo_contacto_nodo');
            $tipoIdentificacion = $parametrosContacto['tipoIdentificacion'];
            $identificacionCliente = $parametrosContacto['identificacionCliente'];
            $tipoTributario     = $parametrosContacto['tipoTributario'];
            $nombres            = $parametrosContacto['nombres'];
            $apellidos          = $parametrosContacto['apellidos'];
            $razonSocial        = $request->get('razonSocial');
            $nacionalidad       = $parametrosContacto['nacionalidad'];
            $arrayDatosContacto  = $request->get('hd_info_contacto')!=""?json_decode($request->get('hd_info_contacto')):""; //Tipo json                        

            //Informacion Galeria de Fotos
            $arrayInfoImagenes   = $request->files->get('infodocumentotype');                                       
            $parametrosDocumento = $request->get('infodocumentotype');                                                 
            $objModeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
            
            $objElementoNodo  = new InfoElemento();
            $objElementoNodo->setNombreElemento($nombreElemento);
            $objElementoNodo->setDescripcionElemento($descripcionElemento);
            $objElementoNodo->setModeloElementoId($objModeloElemento);
            $objElementoNodo->setObservacion($observacionElemento);
            $objElementoNodo->setAccesoPermanente($accesoPermanente);
            $objElementoNodo->setEstado("Activo");
            $objElementoNodo->setUsrResponsable($request->getSession()->get('user'));
            $objElementoNodo->setUsrCreacion($request->getSession()->get('user'));
            $objElementoNodo->setFeCreacion(new \DateTime('now'));
            $objElementoNodo->setIpCreacion($request->getClientIp());       
            $em->persist($objElementoNodo);
            $em->flush();
            
             //solcitud y historial
            $objTipoSolicitud = $em->getRepository("schemaBundle:AdmiTipoSolicitud")
                                    ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD NUEVO NODO'));
            
            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolicitud->setMotivoId($motivoSolicitudId);
            $objDetalleSolicitud->setElementoId($objElementoNodo->getId());
            $objDetalleSolicitud->setEstado("Pendiente");     
            $objDetalleSolicitud->setObservacion($descripcionElemento);     
            $objDetalleSolicitud->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));            
            $objDetalleSolicitud->setFeEjecucion(new \DateTime('now'));            
            $em->persist($objDetalleSolicitud);
            $em->flush();
            
            $objSolicitudHistorial = new InfoDetalleSolHist();
            $objSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objSolicitudHistorial->setEstado("Pendiente");
            $objSolicitudHistorial->setObservacion("Se crea Solicitud de Nodo");     
            $objSolicitudHistorial->setMotivoId($motivoSolicitudId);
            $objSolicitudHistorial->setUsrCreacion($request->getSession()->get('user'));
            $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objSolicitudHistorial->setIpCreacion($request->getClientIp());
            $em->persist($objSolicitudHistorial);
                     
            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoNodo);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Se ingreso un nodo");
            $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($request->getClientIp());
            $em->persist($objHistorialElemento);
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del nodo "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }

            //info ubicacion
            $objParroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);
            $objUbicacionElemento = new InfoUbicacion();
            $objUbicacionElemento->setLatitudUbicacion($latitudUbicacion);
            $objUbicacionElemento->setLongitudUbicacion($longitudUbicacion);
            $objUbicacionElemento->setDireccionUbicacion($direccionUbicacion);
            $objUbicacionElemento->setAlturaSnm($alturaSnm);
            $objUbicacionElemento->setParroquiaId($objParroquia);
            $objUbicacionElemento->setUsrCreacion($request->getSession()->get('user'));
            $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
            $objUbicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($objUbicacionElemento);
           
            //empresa elemento ( NODOS DEBE SER VISIBLE PARA TODAS LAS EMPRESAS )
            //MEGADATOS
            $objInfoEmpresaGrupo = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("MD");
            
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElementoUbica->setElementoId($objElementoNodo);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
            $objEmpresaElementoUbica->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $objEmpresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($objEmpresaElementoUbica);
            
            $objEmpresaElemento = new InfoEmpresaElemento();
            $objEmpresaElemento->setElementoId($objElementoNodo);
            $objEmpresaElemento->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElemento->setEstado("Activo");
            $objEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElemento->setIpCreacion($request->getClientIp());
            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($objEmpresaElemento);
            //TELCONET
            $objInfoEmpresaGrupo = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("TN");
            
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElementoUbica->setElementoId($objElementoNodo);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
            $objEmpresaElementoUbica->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $objEmpresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($objEmpresaElementoUbica);
            
            $objEmpresaElemento = new InfoEmpresaElemento();
            $objEmpresaElemento->setElementoId($objElementoNodo);
            $objEmpresaElemento->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElemento->setEstado("Activo");
            $objEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElemento->setIpCreacion($request->getClientIp());
            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($objEmpresaElemento);
            //TELCONET PANAMA
            $objInfoEmpresaGrupo = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("TNP");
            
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElementoUbica->setElementoId($objElementoNodo);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
            $objEmpresaElementoUbica->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $objEmpresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($objEmpresaElementoUbica);
            
            $objEmpresaElemento = new InfoEmpresaElemento();
            $objEmpresaElemento->setElementoId($objElementoNodo);
            $objEmpresaElemento->setEmpresaCod($objInfoEmpresaGrupo->getId());
            $objEmpresaElemento->setEstado("Activo");
            $objEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
            $objEmpresaElemento->setIpCreacion($request->getClientIp());
            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($objEmpresaElemento);
            
            $intValorTotalEspacio = 0;
            
            //espacio fisico
            for($i=0;$i<$arrayTipoEspacio->total; $i++)
            {
                $objEspacioFisico = new InfoEspacioFisico();
                $objTipoEspacio   = $em->getRepository('schemaBundle:AdmiTipoEspacio')
                                    ->findOneBy(array('nombreTipoEspacio'=>$arrayTipoEspacio->data[$i]->tipoEspacioId));
                $objEspacioFisico->setTipoEspacioFisicoId($objTipoEspacio->getId());
                $objEspacioFisico->setNodoId($objElementoNodo->getId());
                $objEspacioFisico->setAlto($arrayTipoEspacio->data[$i]->alto);
                $objEspacioFisico->setAncho($arrayTipoEspacio->data[$i]->ancho);
                $objEspacioFisico->setLargo($arrayTipoEspacio->data[$i]->largo);
                $objEspacioFisico->setValor($arrayTipoEspacio->data[$i]->valor);
                $objEspacioFisico->setUsrCreacion($request->getSession()->get('user'));
                $objEspacioFisico->setFeCreacion(new \DateTime('now'));
                $objEspacioFisico->setIpCreacion($request->getClientIp());
                $objEspacioFisico->setEstado("Activo");
                $em->persist($objEspacioFisico);
                
                $intValorTotalEspacio = $intValorTotalEspacio + $arrayTipoEspacio->data[$i]->valor;
            }
            
            //Se guardan los valores totales de espacio para generacion posterior de contrato
            $objAdmiCaracteristica = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                         ->findOneBy(array('descripcionCaracteristica'=>'VALOR_NODO'));
            
            if($objAdmiCaracteristica)
            {
                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristica);
                $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $objInfoDetalleSolCaract->setValor($intValorTotalEspacio);
                $objInfoDetalleSolCaract->setEstado("Activo");
                $objInfoDetalleSolCaract->setUsrCreacion($request->getSession()->get('user'));
                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $em->persist($objInfoDetalleSolCaract);
                $em->flush();
            }
            
            //medidor/nodo
            $objMedidor = new InfoMedidor();
            $objTipoMedidor         = $em->find('schemaBundle:AdmiTipoMedidor', $tipoMedidor);
            $objClaseMedidor        = $em->find('schemaBundle:AdmiClaseMedidor', $claseMedidor);
            $objMedidor->setNodoId($objElementoNodo->getId());
            $objMedidor->setTipoMedidorId($objTipoMedidor);
            $objMedidor->setClaseMedidorId($objClaseMedidor);
            $objMedidor->setNumeroMedidor($numeroMedidor);
            $objMedidor->setUsrCreacion($request->getSession()->get('user'));
            $objMedidor->setFeCreacion(new \DateTime('now'));
            $objMedidor->setIpCreacion($request->getClientIp());
            $strMedidorElectrico = $request->get('cmb_medidor_electrico');
            if($strMedidorElectrico === "Seleccione")
            {
                $strMedidorElectrico = null;
            }
            $objMedidor->setMedidorElectrico($strMedidorElectrico);
            $em->persist($objMedidor);

            //Info detelle elemento -> TORRE
            $objDetalleElemento = new InfoDetalleElemento();
            $objDetalleElemento->setElementoId($objElementoNodo->getId());
            $objDetalleElemento->setDetalleNombre("TORRE");
            $objDetalleElemento->setDetalleValor($esEdificio);
            $objDetalleElemento->setDetalleDescripcion($esEdificio=='SI'?$alturaMaxima:0);
            $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($request->getClientIp());
            $objDetalleElemento->setEstado('Activo');
            $em->persist($objDetalleElemento);
            
            //Info detelle elemento -> CLASE
            $objDetalleElemento = new InfoDetalleElemento();
            $clase = $em->find('schemaBundle:AdmiDetalle', $claseNodo);
            $objDetalleElemento->setElementoId($objElementoNodo->getId());
            $objDetalleElemento->setDetalleNombre("CLASE");
            $objDetalleElemento->setDetalleValor($clase->getNombreDetalle());
            $objDetalleElemento->setDetalleDescripcion($clase->getNombreDetalle());
            $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($request->getClientIp());
            $objDetalleElemento->setEstado('Activo');
            $em->persist($objDetalleElemento);
            
            //Info detelle elemento -> TIPO MEDIO            
            $objDetalleElemento = new InfoDetalleElemento();                
            $objDetalleElemento->setElementoId($objElementoNodo->getId());
            $objDetalleElemento->setDetalleNombre("TIPO MEDIO");
            $objDetalleElemento->setDetalleValor($tipoMedioNodo);
            $objDetalleElemento->setDetalleDescripcion($tipoMedioNodo);
            $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($request->getClientIp());
            $objDetalleElemento->setEstado('Activo');
            $em->persist($objDetalleElemento); 
            
            //Se valida si Aplica Torre y se guarda el ciclo de mantenimiento            
            if($esEdificio == 'SI')
            {
                //Info detalle elemento -> MANTENIMIENTO TORRE -> registro
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objElementoNodo->getId());
                $objDetalleElemento->setDetalleNombre("MANTENIMIENTO TORRE");
                $objDetalleElemento->setDetalleValor($strCicloMantenimiento);
                $objDetalleElemento->setDetalleDescripcion("Registro de mantenimiento de Torre");
                $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleElemento->setFeCreacion(new\DateTime('now'));
                $objDetalleElemento->setIpCreacion($request->getClientIp());
                $objDetalleElemento->setEstado('Activo');
                $em->persist($objDetalleElemento);
                
                //Cálculo de próximo mantenimiento
                $strProximoMantenimiento = date('d/m/Y', strtotime('+'.$strCicloMantenimiento.'month'));
                                
                //Info detalle elemento -> PROXIMO MANTENIMIENTO TORRE -> próximo mantenimiento
                $objDetalleElemento = new InfoDetalleElemento();     
                $objDetalleElemento->setElementoId($objElementoNodo->getId());
                $objDetalleElemento->setDetalleNombre("PROXIMO MANTENIMIENTO TORRE");
                $objDetalleElemento->setDetalleValor($strProximoMantenimiento);
                $objDetalleElemento->setDetalleDescripcion("Próxima fecha de mantenimiento de Torre");
                $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleElemento->setFeCreacion(new\DateTime('now'));
                $objDetalleElemento->setIpCreacion($request->getClientIp());
                $objDetalleElemento->setEstado('Activo');
                $em->persist($objDetalleElemento);                               
            }
            
            $objPersona    = null;
            $objEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorIdRolPorEmpresa($tipoContactoNodo,$empresaCod);                         
            //Referencia de Contacto
            if($contactoNodoExiste=='S')//EL contacto existe dentro de la base
            {
                $objPersona  = $em->getRepository('schemaBundle:InfoPersona')->find($contactoNodoId);   
                               
                if($tipoTributario == 'JUR')
                {                    
                    $objPersona->setRazonSocial($razonSocial);                    
                }
                else
                {
                    $objPersona->setNombres($nombres);
                    $objPersona->setApellidos($apellidos);
                }

                $objPersona->setNacionalidad($nacionalidad);
                $objPersona->setTipoIdentificacion($tipoIdentificacion);
                $objPersona->setTipoTributario($tipoTributario);

                $em->persist($objPersona);
                                
                if($contactoNodoExisteRol != 'S')//El contacto existente existe dentro de la base y tiene rol Contacto Nodo
                {                    
                    //Solo se guarda la referencia en la info_nodo_contacto                          
                    $personaEmpresaRol = new InfoPersonaEmpresaRol();                    
                    $personaEmpresaRol->setPersonaId($objPersona);
                    $personaEmpresaRol->setEmpresaRolId($objEmpresaRol);
                    $personaEmpresaRol->setEstado("Activo");
                    $personaEmpresaRol->setUsrCreacion($request->getSession()->get('user'));
                    $personaEmpresaRol->setFeCreacion(new \DateTime('now'));
                    $personaEmpresaRol->setIpCreacion($request->getClientIp());
                    $em->persist($personaEmpresaRol);                                       
                }
                
                /* @var $servicePersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContacto */
                $servicePersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
                
                //Editar Formas de Contacto en caso de ser necesario
                $arrayParametros = array(
                                        'idPersona'          => $contactoNodoId,
                                        'jsonFormasContacto' => $request->get('hd_info_contacto'),
                                        'usrCreacion'        => $request->getSession()->get('user'),
                                        'ipCreacion'         => $request->getClientIp()
                                    );
            
                //Actualizar las formas de contacto de la persona
                $servicePersonaFormaContacto->agregarActualizarEliminarFormasContacto($arrayParametros);
                
            }
            else //se crea una nueva persona con los roles, formas de contacto y referencia en la info_nodo_contacto
            {                
                $objPersona = new InfoPersona();                                
                $objPersona->setIdentificacionCliente($identificacionCliente);
                $objPersona->setOrigenProspecto("N");
                $objPersona->setNombres($nombres);
                $objPersona->setApellidos($apellidos);
                $objPersona->setNacionalidad($nacionalidad);
                $objPersona->setTipoIdentificacion($tipoIdentificacion);
                $objPersona->setTipoTributario($tipoTributario);
                $objPersona->setEstado("Activo");
                $objPersona->setRazonSocial($razonSocial);
                $objPersona->setUsrCreacion($request->getSession()->get('user'));
                $objPersona->setFeCreacion(new \DateTime('now'));
                $objPersona->setIpCreacion($request->getClientIp());
                $em->persist($objPersona);
                $em->flush();
                                
                $personaEmpresaRol = new InfoPersonaEmpresaRol();                    
                $personaEmpresaRol->setPersonaId($objPersona);
                $personaEmpresaRol->setEmpresaRolId($objEmpresaRol);
                $personaEmpresaRol->setEstado("Activo");
                $personaEmpresaRol->setUsrCreacion($request->getSession()->get('user'));
                $personaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $personaEmpresaRol->setIpCreacion($request->getClientIp());
                $em->persist($personaEmpresaRol);
                
                //Se guardan las formas de Contacto
                for($i=0;$i<$arrayDatosContacto->total; $i++)                
                {
                    $objPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $objFormaContacto   = $em->getRepository('schemaBundle:AdmiFormaContacto')
                                                      ->findOneBy(array('descripcionFormaContacto'=>$arrayDatosContacto->data[$i]->formaContacto));
                    
                    $objPersonaFormaContacto->setPersonaId($objPersona);
                    $objPersonaFormaContacto->setFormaContactoId($objFormaContacto);
                    $objPersonaFormaContacto->setValor($arrayDatosContacto->data[$i]->valor);                    
                    $objPersonaFormaContacto->setEstado("Activo");     
                    $objPersonaFormaContacto->setUsrCreacion($request->getSession()->get('user'));
                    $objPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                    $objPersonaFormaContacto->setIpCreacion($request->getClientIp());
                    $em->persist($objPersonaFormaContacto);
                }                                
            }
            
            $objContactoNodo = new InfoContactoNodo();
            $objContactoNodo->setNodoId($objElementoNodo);
            $objContactoNodo->setPersonaId($objPersona->getId());
            $objContactoNodo->setUsrCreacion($request->getSession()->get('user'));
            $objContactoNodo->setFeCreacion(new \DateTime('now'));
            $objContactoNodo->setIpCreacion($request->getClientIp());
            $em->persist($objContactoNodo);
            
            //imagenes en info documento    
            $cont = 0;                        
            
            if($arrayInfoImagenes['imagenes'][0])
            {
                foreach ($arrayInfoImagenes as $key => $imagenes)                 
                {  
                    foreach ( $imagenes as $key_imagen => $value) 
                    {        
                        if( $value )
                        {                                                                                               
                            $objTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumento')->find(1);
                            $objInfoDocumento = new InfoDocumento(); 
                            $objInfoDocumento->setNombreDocumento(str_replace(" ", "_", $nombreElemento));
                            $objInfoDocumento->setUploadDir($this->UPLOAD_PATH.'nodos/nodo_'.$objElementoNodo->getId().'/fotos');
                            $objInfoDocumento->setFile( $value );                           
                            $objInfoDocumento->setFechaDocumento(new \DateTime('now'));                                                                 
                            $objInfoDocumento->setUsrCreacion($request->getSession()->get('user'));
                            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                            $objInfoDocumento->setIpCreacion($request->getClientIp());
                            $objInfoDocumento->setEstado('Activo');                   
                            $objInfoDocumento->setEmpresaCod($empresaCod);    
                            $objInfoDocumento->setTipoDocumentoGeneralId(9);
                            $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);       //id de tipo de imagen basico 

                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();                                
                            } 
                            
                            $objInfoDocumento->setNombreDocumento("Imagen Nodo : ".$objElementoNodo->getNombreElemento());    

                            $em->persist($objInfoDocumento);      
                            $em->flush();

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                            $objInfoDocumentoRelacion->setModulo('TECNICO');                                                        
                            $objInfoDocumentoRelacion->setElementoId($objElementoNodo->getId());                             
                            $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                            $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));                        
                            $objInfoDocumentoRelacion->setUsrCreacion($request->getSession()->get('user'));
                            $em->persist($objInfoDocumentoRelacion);   

                            $objDocumentoTag = new InfoDocumentoTag();
                            $objDocumentoTag->setDocumentoId($objInfoDocumento->getId());
                            $objDocumentoTag->setTagDocumentoId($parametrosDocumento['tags'][$cont]);
                            $objDocumentoTag->setEstado('Activo');                                                                                   
                            $objDocumentoTag->setFeCreacion(new \DateTime('now'));                        
                            $objDocumentoTag->setUsrCreacion($request->getSession()->get('user'));
                            $objDocumentoTag->setIpCreacion($request->getClientIp());
                            $em->persist($objDocumentoTag);  
                            
                            $cont++;
                        }
                    }
                }
            }
            
            //Se ingresa al RACK de manera automatica cuando se trata de un nodo especifciado para CLIENTE
            if($clase->getNombreDetalle() == "CLIENTE ESPECIFICO")
            {
                $objModeloElemento = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneBy(array('nombreModeloElemento' => 'RACK 45 U',
                                                                                                            'estado'               => 'Activo'
                                                                                                           ));
                $objElementoRack = new InfoElemento();
                $objElementoRack->setNombreElemento("RACK-NODO-CLIENTE");
                $objElementoRack->setDescripcionElemento("RACK-NODO-CLIENTE ".$nombreElemento);
                $objElementoRack->setModeloElementoId($objModeloElemento);                
                $objElementoRack->setUsrCreacion($request->getSession()->get('user'));
                $objElementoRack->setFeCreacion(new \DateTime('now'));
                $objElementoRack->setIpCreacion($request->getClientIp());
                $objElementoRack->setEstado("Activo");
                $em->persist($objElementoRack);
                $em->flush();

                //buscar el interface Modelo
                $objInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                         ->findBy(array("modeloElementoId" => $objModeloElemento->getId()));
                
                //se busca modelos de Unidades de Rack para poder crear las Unidades de Rack
                $objModeloElementoUDRack  = $em->getRepository('schemaBundle:AdmiModeloElemento')
                                               ->findOneBy(array("nombreModeloElemento"=>"UDRACK"));
                
                foreach($objInterfaceModelo as $im)
                {
                    $intCantidadInterfaces = $im->getCantidadInterface();                    

                    for($i = 1; $i <= $intCantidadInterfaces; $i++)
                    {
                        $objElemento = new InfoElemento();

                        $strNombreElemento = $i;
                        
                        $objElemento->setNombreElemento($strNombreElemento);
                        $objElemento->setDescripcionElemento("Unidad de rack");
                        $objElemento->setModeloElementoId($objModeloElementoUDRack);
                        $objElemento->setUsrResponsable($request->getSession()->get('user'));
                        $objElemento->setUsrCreacion($request->getSession()->get('user'));
                        $objElemento->setFeCreacion(new \DateTime('now'));
                        $objElemento->setIpCreacion($request->getClientIp());
                        $objElemento->setEstado("Activo");
                        $em->persist($objElemento);
                        $em->flush();
                        //relacion elemento
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($objElementoRack->getId());
                        $objRelacionElemento->setElementoIdB($objElemento->getId());
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Rack contiene unidades");
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($request->getSession()->get('user'));
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($request->getClientIp());
                        $em->persist($objRelacionElemento);    
                    }
                }

                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($objElementoNodo->getId());
                $objRelacionElemento->setElementoIdB($objElementoRack->getId());
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("nodo contiene rack");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($request->getSession()->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($request->getClientIp());
                $em->persist($objRelacionElemento);

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoRack);
                $objHistorialElemento->setEstadoElemento("Activo");
                $objHistorialElemento->setObservacion("Se ingreso un Rack");
                $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($request->getClientIp());
                $em->persist($objHistorialElemento);

                //tomar datos nodo
                $objNodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                      ->findOneBy(array("elementoId" => $objElementoNodo->getId()));
                $objNodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                      ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                //info ubicacion
                $objParroquia         = $em->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
                $objUbicacionElemento = new InfoUbicacion();
                $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
                $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
                $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
                $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
                $objUbicacionElemento->setParroquiaId($objParroquia);
                $objUbicacionElemento->setUsrCreacion($request->getSession()->get('user'));
                $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                $objUbicacionElemento->setIpCreacion($request->getClientIp());
                $em->persist($objUbicacionElemento);

                //empresa elemento ubicacion
                $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                $objEmpresaElementoUbica->setEmpresaCod($request->getSession()->get('idEmpresa'));
                $objEmpresaElementoUbica->setElementoId($objElementoRack);
                $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                $objEmpresaElementoUbica->setUsrCreacion($request->getSession()->get('user'));
                $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $objEmpresaElementoUbica->setIpCreacion($request->getClientIp());
                $em->persist($objEmpresaElementoUbica);

                //empresa elemento
                $objEmpresaElemento = new InfoEmpresaElemento();
                $objEmpresaElemento->setElementoId($objElementoRack);
                $objEmpresaElemento->setEmpresaCod($request->getSession()->get('idEmpresa'));
                $objEmpresaElemento->setEstado("Activo");
                $objEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
                $objEmpresaElemento->setIpCreacion($request->getClientIp());
                $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($objEmpresaElemento);
            }
            
            $em->flush();            
                                       
            $em->commit();                            
                                
            return $this->redirect($this->generateUrl('elementonodo_showNodo', array('id' => $objElementoNodo->getId())));
        
        }
        catch(\Exception $e)
        {                              
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            $em->close(); 
                        
            error_log($e->getMessage());
            
            return $this->render('tecnicoBundle:InfoElementoNodo:new.html.twig',
                   $this->getNewNodoParametros("Error de Ingreso, notificar a Sistemas"));
        }
        
    }
    
    /**
      * editNodoAction
      *
      * Método que redirecciona a la ventana de edicion del nodo con la informacion existente
      *
      * @author Steven Ruano <sruano@telconet.ec>
      * @version 1.8 17-01-2023 - Se agrega variable tipoSolicitudId para que solo traiga los nodos con tipo solicitud id 114
      *  
      * @author Adrian Ortega <amortega@telconet.ec>
      * @version 1.7 09-09-2019 - Se ingresan detalles de mantenimiento preventivo solo cuando el elemento sea tipo Radio
      * 
      * @author Antonio Ayala <afayala@telconet.ec>
      * @version 1.6 17-06-2019 - Se agrega campo medidor eléctrico
      * Se eliminó el campo cobertura nodo.
      * Se eliminó el campo género.
      * Se eliminó el campo título.
      *  
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.5 25-02-2015 - Se modifica metodo ingresando informacion mas completa de los nodos de acuerdo a como se encuentra
      *                            Aplicativo de Nodos que se migra
      * 
      * @author John Vera <javera@telconet.ec>
      * @version 1.4 09-05-2018 - Se valida campos consultados de la base de datos
      * 
      * @version 1.0 - Version Inicial
      *
      * @Secure(roles="ROLE_154-2199")
      */ 
    public function editNodoAction($id)
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intIdEmpresa          = $objSession->get('idEmpresa');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura"); 
        $emSeguridad = $this->getDoctrine()->getManager("telconet"); 
        
        $arrayMotivo            = array();
        $strMedidorElectrico    = null;

        if(null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el elemento -nodo- que se quiere modificar');
        }
        else
        {
            $objUbicacionElemento     = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findBy(array("elementoId" => $elemento->getId(),
                                                                                                    "empresaCod"=>$intIdEmpresa));
            $objUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                           ->findBy(array("id" => $objUbicacionElemento[0]->getUbicacionId()));
            $arrayObjTipoMedio        = $em->getRepository('schemaBundle:AdmiTipoMedio')->getTiposMedios('','','Activo','','');
            $arrayObjDetalles         = $em->getRepository('schemaBundle:AdmiDetalle')->getDetalles('','CLASE NODO','Activo','','');
            $arrayObjTipoMedidor      = $em->getRepository('schemaBundle:AdmiTipoMedidor')->getTiposMedidores('','Activo','','');
            $arrayObjMedidorElectrico = $em->getRepository('schemaBundle:AdmiTipoMedidor')->getMedidoresElectricos('PROYECTO NODO','Activo','','','');
            
            
            $arrayObjClaseMedidor       = $em->getRepository('schemaBundle:AdmiClaseMedidor')->getClasesMedidores('','Activo','',''); 
            $objInfoMedidor             = $em->getRepository('schemaBundle:InfoMedidor')->findOneBy(array('nodoId'=>$id));
            $objMotivos                 = $emSeguridad->getRepository('schemaBundle:AdmiMotivo')
                                                 ->findMotivosPorModuloPorItemMenuPorAccion('nodo',null,'index');         
            
            if($objInfoMedidor)
            {
                $objTipoMedidorNodo         = $em->getRepository('schemaBundle:AdmiTipoMedidor')->find($objInfoMedidor->getTipoMedidorId());
                $objClaseMedidorNodo        = $em->getRepository('schemaBundle:AdmiClaseMedidor')->find($objInfoMedidor->getClaseMedidorId());
                $strMedidorElectrico        = $objInfoMedidor->getMedidorElectrico();
            }
            if ($strMedidorElectrico === null)
            {
                $strMedidorElectrico = 'Seleccione';
            }
            
            $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD NUEVO NODO'));
            
            $objSolicitud   = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                 ->findOneBy(array('elementoId'=>$id,
                                  'tipoSolicitudId'=>$objTipoSolicitud->getId()));                        
            
            if($objSolicitud)
            {
                $objMotivo = $em->getRepository('schemaBundle:AdmiMotivo')->find($objSolicitud->getMotivoId());            
            }
            
            foreach($objMotivos as $motivo)
            {            
                $arrayMotivo[$motivo->getId()] = $motivo->getNombreMotivo();
            }  
            
            //Se obtiene los detalles relacionados al elemento
            $arrayDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findBy(array('elementoId'=>$id));  
            
            $esFactibleTorre    = null;
            $alturaMaxima       = null;
            $tipoNodo           = null;
            $claseNodo          = null;
            $strFechaMantenimiento  = null;
            $strFechaActual         = null;
                                    
            
            if(count($arrayDetalleElemento)!=0)
            {            
                $strTipoMedioNodo = null;

                foreach($arrayDetalleElemento as $objDetalleElemento)
                {                
                    if($objDetalleElemento->getDetalleNombre() == 'TIPO MEDIO')
                    {                    
                        $strTipoMedioNodo = trim($objDetalleElemento->getDetalleValor());
                    }
                    if($objDetalleElemento->getDetalleNombre() == 'CLASE')
                    {                    
                        $claseNodo = trim($objDetalleElemento->getDetalleValor());    

                        if($claseNodo)
                        {
                            $objDetalle = $em->getRepository('schemaBundle:AdmiDetalle')
                                             ->findOneBy(array('nombreDetalle'=>$claseNodo,'tipo'=>'CLASE NODO'));
                            if($objDetalle)
                            {
                                $claseNodo = $objDetalle->getId();
                            }
                        }
                    }                   
                    if($objDetalleElemento->getDetalleNombre() == 'TORRE')
                    {                    
                        $esFactibleTorre = trim($objDetalleElemento->getDetalleValor());
                        
                        if($objDetalleElemento->getDetalleValor() == 'SI' || $objDetalleElemento->getDetalleValor() == 'S'  )
                        {
                            $alturaMaxima = $objDetalleElemento->getDetalleDescripcion();
                        }
                        else
                        {
                            $alturaMaxima = "";
                        }
                    }
                    else if ($objDetalleElemento->getDetalleNombre() == 'PROXIMO MANTENIMIENTO TORRE' && $objDetalleElemento->getEstado() == 'Activo')
                    {
                        $strFechaTemporal = trim($objDetalleElemento->getDetalleValor()); 
                        
                        if (!empty($strFechaTemporal)) 
                        {
                            $strTimestamp = strtotime($strFechaTemporal);
                            if ($strTimestamp === false) 
                            {
                                $strTimestamp = strtotime(str_replace('/', '-', $strFechaTemporal));
                            }
                            $strFechaMantenimiento = date("Y-m-d", $strTimestamp);                            
                        }
                    }
                }

                $tipoNodo = $strTipoMedioNodo;
                $strFechaActual =  date("Y-m-d");
            }                   
        }
        
        $formMotivo   = $this->createForm(new AdmiMotivoType(array("motivos"=>$arrayMotivo)), new AdmiMotivo());
        $formulario   = $this->createForm(new InfoElementoNodoType(), $elemento);
                
        return $this->render('tecnicoBundle:InfoElementoNodo:edit.html.twig', array(
                'edit_form'             => $formulario->createView(),
                'edit_formMotivo'       => $formMotivo->createView(),            
                'nodo'                  => $elemento,
                'ubicacion'             => $objUbicacion[0],
                'idNodo'                => $id,
                'tipoMedio'             => $arrayObjTipoMedio,
                'claseNodo'             => $arrayObjDetalles,
                'tipoMedidor'           => $arrayObjTipoMedidor,
                'medidorElectrico'      => $arrayObjMedidorElectrico,
                'claseMedidor'          => $arrayObjClaseMedidor,
                'medidor'               => $objInfoMedidor,
                'tipoMedidorNodo'       => $objTipoMedidorNodo,
                'medidorElectricoNodo'  => $strMedidorElectrico,
                'claseMedidorNodo'      => $objClaseMedidorNodo,
                'motivo'                => $objMotivo,
                'esFactibleTorre'       => $esFactibleTorre,
                'alturaMaxima'          => $alturaMaxima,
                'tipoNodo'              => $tipoNodo,
                'clase'                 => $claseNodo,
                'fechaMantenimiento'    => $strFechaMantenimiento,
                'fechaActual'           => $strFechaActual,
                )
        );
    }

    /**
     * updateNodoAction
     *
     * Método que actualiza la informacion de los nodos
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.3 01-08-2016 - Se modifica para que cuando se necesita actualizar soporte la creacion de rack segun la clase de nodo, si no existe
     *                            la crea caso contrario continua
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.2 24-03-2016 - Se modifica que estado de actualizacion se mantenga en Activo ya que el historial guarda la informacion
     *                           de actualizacion
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.3 15-07-2016 - Se Actualiza valor de nodo cuando se actualiza valores de espacio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 09-05-2018 - Se valida cuando no hay clase de medidor
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.6 18-06-2019 - Se actualiza campo de medidor eléctrico
     * Se eliminó el campo cobertura nodo.
     * Se eliminó el campo género.
     * Se eliminó el campo título.
     * 
     * @author Adrian Ortega <amortega@telconet.ec>
     * @version 1.7 09-09-2019 - Se ingresan detalles de mantenimiento preventivo solo cuando el elemento sea tipo Radio
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.8 30-01-2020 - Se agrega funcionalidad para que guarde el estado de la caracteristica 'PROXIMO MANTENIMIENTO TORRE'.
     *  
     * @author Gabriela Mora <gmora@telconet.ec>
     * @version 1.9 11-10-2022 - Se añade estado a espacios físicos y se hace las validaciones correspondientes.
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 2.0 06-03-2023 - se añade validacion para que solo obtenga los  detalles solicitudes id 114.
     * 
     * @version 1.0 - Version Inicial   
     */
    public function updateNodoAction($id)
    {
        $request = $this->get('request');
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');               

        $em->beginTransaction();     
        
        if(null == $objElementoNodo = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere editar');
        }
        else
        {
            $boolMensajeUsuario = false;
            try
            {                                       
                $empresaCod    = $request->getSession()->get('idEmpresa');

                $parametros         = $request->get('telconet_schemabundle_infoelementonodotype');
                $parametrosMotivo   = $request->get('telconet_schemabundle_admimotivotype');                                   

                //Informacion Tab Datos Generales
                $nombreElemento         = $parametros['nombreElemento'];
                $modeloElementoId       = $parametros['modeloElementoId'];      
                $observacionElemento    = $parametros['observacion'];
                $motivoSolicitudId      = $parametrosMotivo['nombreMotivo']; //Solicitud
                $esEdificio             = $request->get('cmb_es_edificio'); //detalle
                $alturaMaxima           = $request->get('txt_altura_maxima'); //detalle
                $claseNodo              = $request->get('cmb_clase_nodo');  //detalle
                $numeroMedidor          = $request->get('txt_numero_medidor');
                $claseMedidor           = $request->get('cmb_clase_medidor'); 
                $tipoMedidor            = $request->get('cmb_tipo_medidor');
                $strMedidorElectrico    = $request->get('cmb_medidor_electrico');
                $tipoMedioNodo          = $request->get('hd_info_tipoMedio');
                $strFechaMantenimiento  = $request->get('txt_fecha_mantenimiento');

                //Informacion Tab Datos Local                          
                $parroquiaId        = $parametros['parroquiaId'];
                $direccionUbicacion = $request->get('direccionUbicacion');
                $alturaSnm          = $request->get('alturaSnm');
                $descripcionElemento = $parametros['descripcionElemento'];
                $accesoPermanente   = $parametros['accesoPermanente']; 
                $longitudUbicacion  = $request->get('longitudUbicacion');
                $latitudUbicacion   = $request->get('latitudUbicacion');
                $arrayTipoEspacio   = $request->get('hd_info_espacio')!=""?json_decode($request->get('hd_info_espacio')):""; //Tipo json                     
                                
                $objModeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
                                                           
                $objElementoNodo->setNombreElemento($nombreElemento);                
                $objElementoNodo->setModeloElementoId($objModeloElemento);
                $objElementoNodo->setObservacion($observacionElemento);
                $objElementoNodo->setAccesoPermanente($accesoPermanente);                
                $objElementoNodo->setDescripcionElemento($descripcionElemento);                
                $objElementoNodo->setUsrResponsable($request->getSession()->get('user'));
                $objElementoNodo->setUsrCreacion($request->getSession()->get('user'));
                $objElementoNodo->setFeCreacion(new \DateTime('now'));
                $objElementoNodo->setIpCreacion($request->getClientIp());       
                $em->persist($objElementoNodo);                

                //SolicitudTipoID
                $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD NUEVO NODO'));
                
                 //solcitud y historial              
                $objDetalleSolicitud = $em->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                ->findOneBy(array("elementoId"=>$id,
                                                                  "tipoSolicitudId" =>
                                                                  $objTipoSolicitud->getId()));
                                                                  
                $objDetalleSolicitud->setMotivoId($motivoSolicitudId);                                
                $objDetalleSolicitud->setObservacion($descripcionElemento);     
                $objDetalleSolicitud->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));            
                $em->persist($objDetalleSolicitud);                 

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoNodo);
                $objHistorialElemento->setEstadoElemento("Modificado");
                $objHistorialElemento->setObservacion("Se Modifico nodo");
                $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($request->getClientIp());
                $em->persist($objHistorialElemento);
                
                $objEmpresaElementoUbica = $em->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                                              ->findOneBy(array('elementoId'=>$objElementoNodo,'empresaCod'=>$empresaCod));
                
                $objUbicacionElemento = $em->getRepository("schemaBundle:InfoUbicacion")->find($objEmpresaElementoUbica->getUbicacionId()->getId());
                
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del nodo "));
                if($arrayRespuestaCoordenadas["status"] === "ERROR")
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                }
                
                //info ubicacion
                $objParroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);                
                $objUbicacionElemento->setLatitudUbicacion($latitudUbicacion);
                $objUbicacionElemento->setLongitudUbicacion($longitudUbicacion);
                $objUbicacionElemento->setDireccionUbicacion($direccionUbicacion);
                $objUbicacionElemento->setAlturaSnm($alturaSnm);
                $objUbicacionElemento->setParroquiaId($objParroquia);
                $objUbicacionElemento->setUsrCreacion($request->getSession()->get('user'));
                $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                $objUbicacionElemento->setIpCreacion($request->getClientIp());
                $em->persist($objUbicacionElemento);  
                
                $intValorTotalNodo = 0;

                //espacio fisico
                for($i=0;$i<$arrayTipoEspacio->total; $i++)
                {                                                                                                    
                    
                    $objTipoEspacio   = $em->getRepository('schemaBundle:AdmiTipoEspacio')
                                        ->findOneBy(array('nombreTipoEspacio'=>$arrayTipoEspacio->data[$i]->tipoEspacioId));
                    
                    //Se verifica que tipoEspacio exista
                    $objEspacioFisico = $em->getRepository('schemaBundle:InfoEspacioFisico')
                                           ->find($arrayTipoEspacio->data[$i]->id);
                    
                    if($objEspacioFisico)
                    {   
                        if($objEspacioFisico->getEstado() != 'Eliminado')
                        {
                            $objEspacioFisico->setTipoEspacioFisicoId($objTipoEspacio->getId());
                            $objEspacioFisico->setAlto($arrayTipoEspacio->data[$i]->alto);
                            $objEspacioFisico->setAncho($arrayTipoEspacio->data[$i]->ancho);
                            $objEspacioFisico->setLargo($arrayTipoEspacio->data[$i]->largo);
                            $objEspacioFisico->setValor($arrayTipoEspacio->data[$i]->valor);
                            $objEspacioFisico->setUsrCreacion($request->getSession()->get('user'));
                            $objEspacioFisico->setFeCreacion(new \DateTime('now'));
                            $objEspacioFisico->setIpCreacion($request->getClientIp());
                            $objEspacioFisico->setEstado('Activo');
                            $em->persist($objEspacioFisico);
                        }                  
                    }
                    else //Se crea registro adicional
                    {
                        $objEspacioFisico = new InfoEspacioFisico();
                        $objEspacioFisico->setTipoEspacioFisicoId($objTipoEspacio->getId());
                        $objEspacioFisico->setNodoId($objElementoNodo->getId());
                        $objEspacioFisico->setAlto($arrayTipoEspacio->data[$i]->alto);
                        $objEspacioFisico->setAncho($arrayTipoEspacio->data[$i]->ancho);
                        $objEspacioFisico->setLargo($arrayTipoEspacio->data[$i]->largo);
                        $objEspacioFisico->setValor($arrayTipoEspacio->data[$i]->valor);
                        $objEspacioFisico->setUsrCreacion($request->getSession()->get('user'));
                        $objEspacioFisico->setFeCreacion(new \DateTime('now'));
                        $objEspacioFisico->setIpCreacion($request->getClientIp());
                        $objEspacioFisico->setEstado('Activo');
                        $em->persist($objEspacioFisico);
                    }

                    $intValorTotalNodo = $intValorTotalNodo + $arrayTipoEspacio->data[$i]->valor;
                }
                
                //Actualizar informacion de espacio para mantener los valores concordantes en todas las dependencias del modulo  
                //Se guardan los valores totales de espacio para generacion posterior de contrato
                $objAdmiCaracteristica   = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                              ->findOneBy(array('descripcionCaracteristica'=>'VALOR_NODO'));

                $objInfoDetalleSolCaract = $em->getRepository("schemaBundle:InfoDetalleSolCaract")
                                              ->findOneBy(array('detalleSolicitudId'=> $objDetalleSolicitud->getId(),
                                                                'caracteristicaId'  => $objAdmiCaracteristica->getId())
                                                         );                   
                if($objInfoDetalleSolCaract)
                {                
                    $objInfoDetalleSolCaract->setValor($intValorTotalNodo);                
                    $objInfoDetalleSolCaract->setUsrUltMod($request->getSession()->get('user'));
                    $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                    $em->persist($objInfoDetalleSolCaract);
                    $em->flush();
                }

                //medidor/nodo
                $objMedidor = $em->getRepository("schemaBundle:InfoMedidor")->findOneBy(array('nodoId'=>$id));
                
                $objTipoMedidor  = $em->find('schemaBundle:AdmiTipoMedidor', $tipoMedidor);
                
                if($claseMedidor > 0)
                {
                    $objClaseMedidor = $em->find('schemaBundle:AdmiClaseMedidor', $claseMedidor);
                    
                    if(is_object($objClaseMedidor))
                    {
                        $objMedidor->setClaseMedidorId($objClaseMedidor);
                    }
                }
                                
                $objMedidor->setTipoMedidorId($objTipoMedidor);
                $objMedidor->setNumeroMedidor($numeroMedidor);
                $objMedidor->setUsrCreacion($request->getSession()->get('user'));
                $objMedidor->setFeCreacion(new \DateTime('now'));
                $objMedidor->setIpCreacion($request->getClientIp());
                if($strMedidorElectrico === "Seleccione")
                {
                    $strMedidorElectrico = null;
                }
                $objMedidor->setMedidorElectrico($strMedidorElectrico);
                $em->persist($objMedidor);
                                                
                //Info detelle elemento -> TORRE
                $objDetalleElemento = $em->getRepository("schemaBundle:InfoDetalleElemento")
                                         ->findOneBy(array('detalleNombre'=>'TORRE','elementoId'=>$id));

                if($objDetalleElemento)
                {
                    $objDetalleElemento->setDetalleValor($esEdificio);
                    $objDetalleElemento->setDetalleDescripcion($esEdificio=='SI'?$alturaMaxima:0);                                       
                }
                else
                {
                    $objDetalleElemento = new InfoDetalleElemento();
                    $objDetalleElemento->setElementoId($objElementoNodo->getId());
                    $objDetalleElemento->setDetalleNombre("TORRE");
                    $objDetalleElemento->setDetalleValor($esEdificio);
                    $objDetalleElemento->setDetalleDescripcion($esEdificio=='SI'?$alturaMaxima:0);                       
                }

                $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($request->getClientIp());
                $em->persist($objDetalleElemento);
                
                //Se verifica si aplica Torre
                if ($esEdificio =='SI')
                {
                    //Info detalle elemento -> PROXIMO MANTENIMIENTO TORRE -> próximo mantenimiento
                    $objDetalleElemento = $em->getRepository("schemaBundle:InfoDetalleElemento")
                                                 ->findOneBy(array('detalleNombre'=>'PROXIMO MANTENIMIENTO TORRE',
                                                                   'elementoId'=>$id,
                                                                   'estado'=>'Activo'));
                    if($objDetalleElemento)
                    {
                        $strFechaMantenimiento = date("d/m/Y", strtotime($strFechaMantenimiento));
                        
                        $objDetalleElemento->setDetalleValor($strFechaMantenimiento);
                    }
                    else
                    {
                        $objDetalleElemento = new InfoDetalleElemento();
                        $objDetalleElemento->setElementoId($objElementoNodo->getId());
                        $objDetalleElemento->setDetalleNombre("PROXIMO MANTENIMIENTO TORRE");
                        $objDetalleElemento->setDetalleValor($strFechaMantenimiento);
                        $objDetalleElemento->setEstado('Activo');
                        $objDetalleElemento->setDetalleDescripcion("Próxima fecha de mantenimiento de Torre");               
                    }
                    $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                    $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objDetalleElemento->setIpCreacion($request->getClientIp());
                    $em->persist($objDetalleElemento);
                    
                }

                //Info detelle elemento -> CLASE
                $objDetalleElemento = $em->getRepository("schemaBundle:InfoDetalleElemento")
                                         ->findOneBy(array('detalleNombre'=>'CLASE','elementoId'=>$id));

                $clase = $em->find('schemaBundle:AdmiDetalle', $claseNodo);  

                if($objDetalleElemento)
                {
                    $objDetalleElemento->setDetalleValor($clase->getNombreDetalle());
                    $objDetalleElemento->setDetalleDescripcion($clase->getNombreDetalle());
                }
                else
                {
                    $objDetalleElemento = new InfoDetalleElemento();                        
                    $objDetalleElemento->setElementoId($objElementoNodo->getId());
                    $objDetalleElemento->setDetalleNombre("CLASE");
                    $objDetalleElemento->setDetalleValor($clase->getNombreDetalle());
                    $objDetalleElemento->setDetalleDescripcion($clase->getNombreDetalle());
                }

                $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($request->getClientIp());
                $em->persist($objDetalleElemento);

                //Info detelle elemento -> TIPO MEDIO
                $objDetalleElemento = $em->getRepository("schemaBundle:InfoDetalleElemento")
                                         ->findOneBy(array('detalleNombre'=>'TIPO MEDIO','elementoId'=>$id));

                if($objDetalleElemento)
                {
                    $objDetalleElemento->setDetalleValor($tipoMedioNodo);
                    $objDetalleElemento->setDetalleDescripcion($tipoMedioNodo);
                }
                else
                {
                    $objDetalleElemento = new InfoDetalleElemento();                
                    $objDetalleElemento->setElementoId($objElementoNodo->getId());
                    $objDetalleElemento->setDetalleNombre("TIPO MEDIO");
                    $objDetalleElemento->setDetalleValor($tipoMedioNodo);
                    $objDetalleElemento->setDetalleDescripcion($tipoMedioNodo);
                }

                $objDetalleElemento->setUsrCreacion($request->getSession()->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($request->getClientIp());
                $em->persist($objDetalleElemento);                
                
                //Verificar si la clase del nodo es CLIENTE ESPECIFICO                
                if($clase)
                {
                    if($clase->getNombreDetalle() == 'CLIENTE ESPECIFICO')
                    {
                        $boolRackExiste = false;
                        
                        //Verificamos que no contenga un rack para poder crear la relacion                    
                        $arrayContenidos = $em->getRepository('schemaBundle:InfoElemento')->getResultadoElementosContenidosNodo($id);
                        
                        foreach($arrayContenidos['resultado'] as $elemento)
                        {
                            if($elemento['nombreElemento'] == 'RACK NODO CLIENTE')
                            {
                                $boolRackExiste = true;
                                break;
                            }
                        }
                        
                        if(!$boolRackExiste)
                        {
                            $objModeloElemento = $em->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->findOneBy(array('nombreModeloElemento' => 'RACK 45 U',
                                                                      'estado'               => 'Activo'
                                                                     ));

                            $objElementoRack = new InfoElemento();
                            $objElementoRack->setNombreElemento("RACK-NODO-CLIENTE");
                            $objElementoRack->setDescripcionElemento("RACK-NODO-CLIENTE ".$nombreElemento);
                            $objElementoRack->setModeloElementoId($objModeloElemento);                
                            $objElementoRack->setUsrCreacion($request->getSession()->get('user'));
                            $objElementoRack->setFeCreacion(new \DateTime('now'));
                            $objElementoRack->setIpCreacion($request->getClientIp());
                            $objElementoRack->setEstado("Activo");
                            $em->persist($objElementoRack);
                            $em->flush();

                            //buscar el interface Modelo
                            $objInterfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                     ->findBy(array("modeloElementoId" => $objModeloElemento->getId()));

                            //se busca modelos de Unidades de Rack para poder crear las Unidades de Rack
                            $objModeloElementoUDRack  = $em->getRepository('schemaBundle:AdmiModeloElemento')
                                                           ->findOneBy(array("nombreModeloElemento"=>"UDRACK"));

                            foreach($objInterfaceModelo as $im)
                            {
                                $intCantidadInterfaces = $im->getCantidadInterface();                    

                                for($i = 1; $i <= $intCantidadInterfaces; $i++)
                                {
                                    $objElemento = new InfoElemento();

                                    $strNombreElemento = $i;

                                    $objElemento->setNombreElemento($strNombreElemento);
                                    $objElemento->setDescripcionElemento("Unidad de rack");
                                    $objElemento->setModeloElementoId($objModeloElementoUDRack);
                                    $objElemento->setUsrResponsable($request->getSession()->get('user'));
                                    $objElemento->setUsrCreacion($request->getSession()->get('user'));
                                    $objElemento->setFeCreacion(new \DateTime('now'));
                                    $objElemento->setIpCreacion($request->getClientIp());
                                    $objElemento->setEstado("Activo");
                                    $em->persist($objElemento);
                                    $em->flush();
                                    //relacion elemento
                                    $objRelacionElemento = new InfoRelacionElemento();
                                    $objRelacionElemento->setElementoIdA($objElementoRack->getId());
                                    $objRelacionElemento->setElementoIdB($objElemento->getId());
                                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                                    $objRelacionElemento->setObservacion("Rack contiene unidades");
                                    $objRelacionElemento->setEstado("Activo");
                                    $objRelacionElemento->setUsrCreacion($request->getSession()->get('user'));
                                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                                    $objRelacionElemento->setIpCreacion($request->getClientIp());
                                    $em->persist($objRelacionElemento);    
                                }
                            }

                            //relacion elemento
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($objElementoNodo->getId());
                            $objRelacionElemento->setElementoIdB($objElementoRack->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("nodo contiene rack");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($request->getSession()->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($request->getClientIp());
                            $em->persist($objRelacionElemento);

                            //historial elemento
                            $objHistorialElemento = new InfoHistorialElemento();
                            $objHistorialElemento->setElementoId($objElementoRack);
                            $objHistorialElemento->setEstadoElemento("Activo");
                            $objHistorialElemento->setObservacion("Se ingreso un Rack");
                            $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
                            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objHistorialElemento->setIpCreacion($request->getClientIp());
                            $em->persist($objHistorialElemento);

                            //tomar datos nodo
                            $objNodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                  ->findOneBy(array("elementoId" => $objElementoNodo->getId()));
                            $objNodoUbicacion                = $em->getRepository('schemaBundle:InfoUbicacion')
                                                                  ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                            //info ubicacion
                            $objParroquia         = $em->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
                            $objUbicacionElemento = new InfoUbicacion();
                            $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
                            $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
                            $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
                            $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
                            $objUbicacionElemento->setParroquiaId($objParroquia);
                            $objUbicacionElemento->setUsrCreacion($request->getSession()->get('user'));
                            $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                            $objUbicacionElemento->setIpCreacion($request->getClientIp());
                            $em->persist($objUbicacionElemento);

                            //empresa elemento ubicacion
                            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                            $objEmpresaElementoUbica->setEmpresaCod($request->getSession()->get('idEmpresa'));
                            $objEmpresaElementoUbica->setElementoId($objElementoRack);
                            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                            $objEmpresaElementoUbica->setUsrCreacion($request->getSession()->get('user'));
                            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                            $objEmpresaElementoUbica->setIpCreacion($request->getClientIp());
                            $em->persist($objEmpresaElementoUbica);

                            //empresa elemento
                            $objEmpresaElemento = new InfoEmpresaElemento();
                            $objEmpresaElemento->setElementoId($objElementoRack);
                            $objEmpresaElemento->setEmpresaCod($request->getSession()->get('idEmpresa'));
                            $objEmpresaElemento->setEstado("Activo");
                            $objEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
                            $objEmpresaElemento->setIpCreacion($request->getClientIp());
                            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                            $em->persist($objEmpresaElemento);
                        }
                    }
                }

                //Actualizar espacio fisico con estado pendiente a eliminado
                $arrayEspacioFisicoPendiente = $em->getRepository('schemaBundle:InfoEspacioFisico')
                                                  ->getEspacioFisicoPendiente($id);             
                if(count($arrayEspacioFisicoPendiente) > 0)
                {
                    foreach($arrayEspacioFisicoPendiente as $intIdEspacio)
                    {
                        $objEspacioFisico = $em->getRepository('schemaBundle:InfoEspacioFisico')
                                               ->find($intIdEspacio); 
                        if($objEspacioFisico)
                        {
                            $objEspacioFisico->setEstado('Eliminado');
                            $em->persist($objEspacioFisico);
                        }
                    }
                }

                $em->flush();

                $em->commit();                        

                return $this->redirect($this->generateUrl('elementonodo_showNodo', array('id' => $objElementoNodo->getId())));

            }
            catch(\Exception $e)
            {
                if($boolMensajeUsuario)
                {
                    $strMensajeError = $e->getMessage();
                }
                else
                {
                    $strMensajeError = "Error de Ingreso, notificar a Sistemas";
                }
                if($em->getConnection()->isTransactionActive())
                {                
                    $em->rollback();
                }
                $em->close();
                error_log($e->getMessage());
                return $this->render('tecnicoBundle:InfoElementoNodo:new.html.twig',
                       $this->getNewNodoParametros($strMensajeError));
            }
        
        }
    }           
    
    /**
      * ajaxDeleteNodoAction
      *
      * Método que elimina el nodo y anula la solicitud
      *                                                            
      * @return resultado
      * 
      * @author Antonio Ayala  <afayala@telconet.ec>
      * @version 1.3 13-06-2019 - Se agrego validación para eliminar nodo si los elementos relacionados
      * al nodo se encuentran en estado Eliminado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.2 24-03-2016 - Se establece un solo estado cuando los nodos son eliminados
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 25-02-2015 
      * 
      * @version 1.0 - Version Inicial
      * 
      * @Secure(roles="ROLE_154-2198")
      */  
    public function ajaxDeleteNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');

        $idNodo = $request->get('id');

        $em     = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objEms = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $em->getConnection()->beginTransaction();
        
        //Antes de eliminar el nodo se preguntara si tiene elementos relacionados y si lo tiene debe
        //estar en estado eliminado caso contrario no se podrá eliminar el nodo
        $objQueryResult = $objEms->getRepository('schemaBundle:InfoElemento')->getResultadoConteoElementosContenidosNodo($idNodo);
                   
        $intTotalActivos = $objQueryResult['intTotal'];
        if ($intTotalActivos > 0)
        {
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'No se puede eliminar el nodo ya que contiene elementos Activos'));
            $respuesta->setContent($objResultado);
            return $respuesta;
        }
        
        try
        {
            $objElemento = $em->find('schemaBundle:InfoElemento', $idNodo);
            
            $objSolicitud  =  $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneBy(array('elementoId'=>$idNodo));

            //elemento
            $objElemento->setUsrCreacion($request->getSession()->get('user'));
            $objElemento->setFeCreacion(new \DateTime('now'));
            $objElemento->setIpCreacion($request->getClientIp());
            $objElemento->setEstado("Eliminado");
            $em->persist($objElemento);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setObservacion("Se elimino el Nodo");
            $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($request->getClientIp());
            $em->persist($objHistorialElemento);
            
            //Se verifica si el nodo tiene una solicitud
            if($objSolicitud)
            {
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
                                
                $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                $objSolicitudHistorial->setUsrCreacion($request->getSession()->get('user'));
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($request->getClientIp());
                                                
                $objSolicitud->setEstado('Eliminada');
                $objSolicitudHistorial->setEstado("Eliminada");
                $objSolicitudHistorial->setObservacion("Se Elimina Solicitud por Nodo Eliminado");                   
                                
                $objSolicitud->setFeRechazo(new \DateTime('now'));
                
                $em->persist($objSolicitud);
                $em->persist($objSolicitudHistorial);
            }            
            
            $strMensaje = "Se Eliminó Nodo";

            $em->flush();
            $em->getConnection()->commit();
            
            $objResultado = json_encode(array('success'=>true,'mensaje'=>$strMensaje));
            $respuesta->setContent($objResultado);
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }

            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al eliminar Nodo'));
            $respuesta->setContent($objResultado);
        }
        return $respuesta;
    }

    /**
      * showNodoAction
      *
      * Método que redirecciona a ventana del show del nodo consultado     
      *                               
      * @param $id
      *                                                
      * @return json con resultado
      * 
      * @author Adrian Ortega <amortega@telconet.ec>
      * @version 1.4 09-09-2019 - Se modifica metodo obteniendo informacion de los detalles de mantenimiento del elemento nodo
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.3 15-07-2016 - Se devuelve informacion si un nodo tiene renovacion de contrato para retroalimentar al usuario cada que se 
      *                           actualice el valor del mismo
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.2 24-03-2016 - Se realiza ordenamiento DESC del historial del nodo
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 25-02-2015 - Se modifica metodo obteniendo información mas completa relacionado a los nodos
      * 
      * @version 1.0 - Version Inicial
      * 
      * @Secure(roles="ROLE_154-6")
      */   
    public function showNodoAction($id)
    {
        $peticion = $this->get('request');
        $session  = $peticion->getSession();        
        
        $objEm = $this->getDoctrine()->getManager("telconet_infraestructura");        
        
        $arrayParams = array();
        
        $objElemento = $objEm->find('schemaBundle:InfoElemento', $id);
        
        if(!$objElemento)
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {            
            
            $arrayParams['id'] = $id;
            $arrayParams['empresa']   = $session->get('idEmpresa');                        
            $arrayParams['region']    = "";                      
            $arrayParams['provincia'] = "";                      
            $arrayParams['canton']    = "";                      
            $arrayParams['parroquia'] = "";                      
            $arrayParams['estado']    = "";                      
            $arrayParams['nombre']    = "";   
            
            $objQueryResult = $objEm->getRepository('schemaBundle:InfoElemento')->getElementoNodo($arrayParams,'data');                        
            
            $arrayResultado = $objQueryResult->getArrayResult();
            
            $resultado = $arrayResultado[0];
            
            //Se obtiene historial del Elemento
            $objNodoHistorial = $objEm->getRepository('schemaBundle:InfoHistorialElemento')
                                   ->findBy(array('elementoId'=>$resultado['idElemento']),array('feCreacion'=>'DESC')); 
            
            $alturaTorre = 'N/A';
            $strCicloMantenimiento  = 'N/A';
            $strProximoMantenimiento = 'N/A';
            
            //Busca la altura de la torre en que caso que exista la descripcion cuando el Nodo sea una Torre
            if($resultado['torre']!='N/A' && $resultado['torre']!='NO')
            {
                $objDetalleElemento = $objEm->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->findOneBy(array('elementoId'=>$id,'detalleNombre'=>'TORRE'));
                
                if($objDetalleElemento)
                {
                    $alturaTorre = $objDetalleElemento->getDetalleDescripcion();
                }
                
                $objDetalleElemento = $objEm->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array('elementoId'=>$id,'detalleNombre'=>'MANTENIMIENTO TORRE','estado'=>'Activo'));
                
                if($objDetalleElemento)
                {
                    $strCicloMantenimiento = $objDetalleElemento->getDetalleValor();
                }
                
                $objDetalleElemento = $objEm->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array('elementoId'=>$id,'detalleNombre'=>'PROXIMO MANTENIMIENTO TORRE','estado'=>'Activo'));
                
                if($objDetalleElemento)
                {
                    $strProximoMantenimiento = $objDetalleElemento->getDetalleValor();
                }
            }     
            
            $tieneRenovacion = '';
            
            //Verificar si existe renovacion de contrato
            
            if($resultado['estadoSolicitud'] != 'Finalizada')
            {
                if($resultado['cantFinalizadas']>0)
                {
                    $tieneRenovacion = ' ( Nodo con Renovación de CONTRATO )';
                }
            }
            else //Si ya existe una solcitud Finalizado debe tener al menos 2 estados Finalizados en historial
            {
                if($resultado['cantFinalizadas']>1)
                {
                    $tieneRenovacion = ' ( Nodo con Renovación de CONTRATO )';
                }
            }
        }

        return $this->render('tecnicoBundle:InfoElementoNodo:show.html.twig', array(
            'data'              => $resultado,
            'idElemento'        => $id,
            'historialElemento' => $objNodoHistorial,
            'alturaTorre'       => $alturaTorre,
            'tieneRenovacion'   => $tieneRenovacion,
            'cicloMantenimiento'  => $strCicloMantenimiento,
            'proximoMantenimiento' => $strProximoMantenimiento
        ));
    }
    
     /**
      * ajaxUpdateNombreNodoAction
      *
      * Método que actualiza el nombre del Nodo de manera directa    
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 03-03-2015
      */    
    public function ajaxUpdateNombreNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $idNodo     = $this->get('request')->get('id');                
        $nombreNodo = $this->get('request')->get('nombre');     
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $em->getConnection()->beginTransaction(); 
        
        try
        {                    
            $objNodoElemento = $em->find('schemaBundle:InfoElemento', $idNodo);

            $objNodoElemento->setNombreElemento($nombreNodo);
            $em->persist($objNodoElemento);
            $em->flush();

            $em->getConnection()->commit(); 

            $resultado = json_encode(array('success'=>true,'mensaje'=>'Nombre de Nodo Actualizado'));
            $respuesta->setContent($resultado);
        
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $resultado = json_encode(array('success'=>false,'mensaje'=>'Error al actualizar nombre Nodo'));
            $respuesta->setContent($resultado);
        }
                
        return $respuesta;
    }
    
      /**
      * ajaxUpdateMedidorAction
      *
      * Método que actualiza la informacion del medidor del nodo    
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 03-03-2015
      *
      */    
    public function ajaxUpdateMedidorAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $idMedidor              = $this->get('request')->get('id');                
        $numeroMedidor          = $this->get('request')->get('numeroMedidor');     
        $tipoMedidor            = $this->get('request')->get('tipoMedidor');     
        $claseMedidor           = $this->get('request')->get('claseMedidor');     
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $em->getConnection()->beginTransaction(); 
        
        try
        {                    
            $objMedidor = $em->getRepository('schemaBundle:InfoMedidor')->find($idMedidor);
            
            $objTipoMedidor  = $em->getRepository('schemaBundle:AdmiTipoMedidor')->find($tipoMedidor);
            $objClaseMedidor = $em->getRepository('schemaBundle:AdmiClaseMedidor')->find($claseMedidor);

            $objMedidor->setNumeroMedidor($numeroMedidor);
            $objMedidor->setTipoMedidorId($objTipoMedidor);
            $objMedidor->setClaseMedidorId($objClaseMedidor);
            $em->persist($objMedidor);
            $em->flush();

            $em->getConnection()->commit(); 

            $resultado = json_encode(array('success'=>true,'mensaje'=>'Informacion de Medidor Actualizada'));
            $respuesta->setContent($resultado);
        
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $resultado = json_encode(array('success'=>false,'mensaje'=>'Error al actualizar informacion de medidor'));
            $respuesta->setContent($resultado);
        }
                
        return $respuesta;
    }
    
     /**
      * ajaxGetInfoEspacioNodoAction
      *
      * Método que obtiene el json de la informacion de tipo espacio por cada nodo consultado     
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */     
    public function ajaxGetInfoEspacioNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $idNodo = $this->get('request')->get('idNodo');                
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoEspacioFisico')
                        ->generarJsonEspacioFisico($idNodo);
        
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }
    
    /**
      * ajaxGetContactoNodoAction
      *
      * Método que obtiene el json de la informacion de contacto por cada nodo consultado     
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */     
    public function ajaxGetContactoNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $idNodo = $this->get('request')->get('idNodo');                
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoContactoNodo')
                        ->generarJsonContactoNodo($idNodo);
        
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }
    
     /**
      * ajaxGetFormaContactoPorContactoNodo
      *
      * Método que obtiene el json de la informacion de forma de contacto por contacto de nodo     
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */     
    public function ajaxGetFormaContactoPorContactoNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $idPersona = $this->get('request')->get('idPersona');                
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->getFormasContactoPorPersona($idPersona);
        
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }
    
    /**
     * 
     * Metodo encargado de actualizar las formas de contacto de los contactos relacionados a los nodos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se llama a service para que realice la actualizacion de las formas de contacto
     * @since 20/07/2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 18/07/2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxActualizarFormaContactoNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion             = $this->get('request');
        
        /* @var $servicePersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContacto */
        $servicePersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
        
        try
        {                               
            $arrayParametros = array(
                                        'idPersona'          => $peticion->get('idPersona'),
                                        'jsonFormasContacto' => $peticion->get('formasContacto'),
                                        'usrCreacion'        => $peticion->getSession()->get('user'),
                                        'ipCreacion'         => $peticion->getClientIp()
                                    );
            
            //Actualizar las formas de contacto de la persona
            $arrayResponde = $servicePersonaFormaContacto->agregarActualizarEliminarFormasContacto($arrayParametros);
                                   
            $resultado = json_encode($arrayResponde);                    
        }
        catch(\Exception $e)
        {            
            error_log($e->getMessage());
            
            $resultado = json_encode(array('success'=>false,'mensaje'=>'Error al actualizar contacto : '.$e->getMessage()));                      
        }
                
        $respuesta->setContent($resultado);
        
        return $respuesta;
    } 
    
    /**
     * 
     * Metodo encargado de actualizar la informacion de contacto de un nodo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 01-08-2016
     * @version 1.0
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @since 12-08-2020 - Se actualiza el contacto seleccionado en la tabla Info_Contacto_Nodo
     * @version 1.1
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxActualizarInformacionContactoNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion      = $this->get('request');        
        $empresaCod    = $peticion->getSession()->get('idEmpresa');
        
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');  
        
        $intIdPersona           =    $peticion->get('idPersona');
        $strTipoIdentificacion  =    $peticion->get('tipoIdentificacion');
        $strTipoTributario      =    $peticion->get('tipoTributario');
        $strIdentificacion      =    $peticion->get('identificacion');
        $strNombres             =    $peticion->get('nombres');
        $strApellidos           =    $peticion->get('apellidos');
        $strRazonSocial         =    $peticion->get('razonSocial');
        $intTipoContacto        =    $peticion->get('tipoContacto');
        $cambioTipoContacto     =    $peticion->get('cambioTipoContacto');
        $intTipoContactoAnterior=    $peticion->get('tipoContactoAnterior');
        $intIdNodo              =    $peticion->get('intNodoId');
        
        $objPersonaContacto     =    $emComercial->getRepository("schemaBundle:InfoPersona")->find($intIdPersona);
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {                                        
            $objPersonaContactoNodo = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneBy(array('identificacionCliente'   => $strIdentificacion)
                                                    );
            if(is_object($objPersonaContactoNodo))
            {
                $objContactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")
                                                     ->findOneBy(array('nodoId'   => $intIdNodo)
                                                    ); 
                if(is_object($objContactoNodo))
                {
                    $objContactoNodo->setPersonaId($objPersonaContactoNodo->getId());
                    $emInfraestructura->persist($objContactoNodo);
                    $emInfraestructura->flush();
                }
            }
                        
            //Nuevo Rol
            $objEmpresaRol         = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                 ->findPorIdRolPorEmpresa($intTipoContacto,$empresaCod);
                
            //Rol Anterior
            $objEmpresaRolAnterior = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                 ->findPorIdRolPorEmpresa($intTipoContactoAnterior,$empresaCod);
                
            //Se elimina el rol anterior del contacto cuando existe cambio
            $objPersonaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                         ->findOneBy(array('empresaRolId' => $objEmpresaRolAnterior->getId(),
                                                           'estado'       => 'Activo',
                                                           'personaId'    => $objPersonaContacto->getId())
                                                        );                
            if($objPersonaRol)
            {
                $objPersonaRol->setEstado('Eliminado');
                $emComercial->persist($objPersonaRol);
                $emComercial->flush();
            }
                
            //Solo se guarda la referencia en la info_nodo_contacto                          
            $personaEmpresaRol = new InfoPersonaEmpresaRol();                    
            $personaEmpresaRol->setPersonaId($objPersonaContactoNodo);
            $personaEmpresaRol->setEmpresaRolId($objEmpresaRol);
            $personaEmpresaRol->setEstado("Activo");
            $personaEmpresaRol->setUsrCreacion($peticion->getSession()->get('user'));
            $personaEmpresaRol->setFeCreacion(new \DateTime('now'));
            $personaEmpresaRol->setIpCreacion($peticion->getClientIp());
            $emComercial->persist($personaEmpresaRol);                  
            $emComercial->flush();      
                                               
            if($emComercial->getConnection()->isTransactionActive())
            {                
                $emComercial->getConnection()->commit(); 
            }
            
            $resultado = json_encode(array('success'=>true,'mensaje'=>'Información de Contacto Actualizada Correctamente'));
        }
        catch(\Exception $e)
        {            
            error_log($e->getMessage());
            
            if($emComercial->getConnection()->isTransactionActive())
            {                
                $emComercial->getConnection()->rollback();
            }
            
            $resultado = json_encode(array('success'=>false,'mensaje'=>'Error al actualizar contacto : '.$e->getMessage()));                      
        }
                    
        $respuesta->setContent($resultado);
        
        return $respuesta;
    }
    
    /**
      * getEncontradosNodoAction
      *
      * Método que obtiene la informacion de los nodos en la pantalla inicial  
      *                                                                             
      * @return json con resultado
      * 
      * @version 1.0 - Version Inicial
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.5 - 25/02/2015 - Modificacion de cnsulta ingresando mas variables
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.6 - 13-04-2016 - Se modifica para que cuando se envíe el parametro 'query' que corresponde a la consulta remote del ExtJs se
      *                              consulte por nombre del Nodo
      * 
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.7 - 06-06-2016 - Se agrega validación para no realizar cargas totales de información de nodos
      * 
      * @author Antonio Ayala  <afayala@telconet.ec>
      * @version 1.8 - 11-06-2019 - Se modificó línea de codigo para que obtenga el estado del nodo correctamente
      *
      * @author Antonio Ayala  <afayala@telconet.ec>
      * @version 1.9 - 20-06-2019 - Se incremento identificación como criterio de búsqueda
      *
      * @author Pablo Pin <ppin@telconet.ec>
      * @version 2.0 29-01-2020 - Se agrega al arreglo de parámetros el objeto 'serviceInfoElemento', 
      *                           para poder llamar al servicio desde el repositorio.
      *
      * @author Felix Caicedo <facaicedo@telconet.ec>
      * @version 2.1 26-05-2021 - Se agrega el parámetro es multiplataforma para la validación de los nodos multiplataforma
      *
      */
    public function getEncontradosNodoAction()
    {
        set_time_limit(400000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $this->get('session');

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $arrayParams = array();
        
        $strNombreElemento = $peticion->query->get('query');
        
        $arrayParams['nombre']              = $strNombreElemento ? $strNombreElemento : $peticion->query->get('nombreElemento');
        $arrayParams['identificacion']      = $peticion->get('identificacion');
        $arrayParams['empresa']             = $session->get('idEmpresa');
        $arrayParams['estado']              = $peticion->query->get('estadoNodo');
        $arrayParams['estadoSol']           = $peticion->query->get('estadoSolicitud');
        $arrayParams['motivo']              = $peticion->query->get('motivo');
        $arrayParams['canton']              = $peticion->query->get('canton');
        $arrayParams['provincia']           = $peticion->query->get('provincia');
        $arrayParams['serviceInfoElemento'] = $this->get('tecnico.infoelemento');
        $arrayParams['esMultiplataforma']   = $peticion->get('esMultiplataforma') ? $peticion->get('esMultiplataforma') : 'NO';
        $strProcesoBusqueda                 = $peticion->query->get('procesoBusqueda');
        
        $arrayParams['torre']      = null;  
                       
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');  
                
        $clase = '';
        
        if($peticion->query->get('clase') && ($peticion->query->get('clase')!='' && $peticion->query->get('clase')!='Todos'))
        {
            $objDetalle = $em->getRepository("schemaBundle:AdmiDetalle")->find($peticion->query->get('clase'));
            if($objDetalle)
            {
                $clase = $objDetalle->getNombreDetalle();
            }
        }
        
        $arrayParams['clase']      = $clase;
        
        if ($strProcesoBusqueda == 'limitado')
        {
            if ($arrayParams['nombre'] !='')
            {
                $objJson = $em->getRepository('schemaBundle:InfoElemento')->generarJsonElementoNodo($arrayParams,$start,$limit);
            }
            else
            {
                $objJson = '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objJson = $em->getRepository('schemaBundle:InfoElemento')->generarJsonElementoNodo($arrayParams,$start,$limit);
        }
    
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
      * cargarDatosNodoActionAjax
      *
      * Método que obtiene la informacion a llenar en los combos al momento de editar la informacion del nodo
      *                                                                             
      * @return json con resultado            
      * 
      * @version 1.0 - Version Inicial
      */ 
    public function ajaxCargarDatosNodoAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $idNodo = $peticion->get('idNodo');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonCargarDatosNodo($idNodo);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * ajaxCargarElementosContenidosAction
     *
     * Metodo que devuelve los resultados de todos los elementos contenidos en un NODO determinado
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-02-2016
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 08-04-2021 - Se modifica los parámetros al momento de llamar a la función 'getJsonElementosContenidosNodo'.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxCargarElementosContenidosAction()
    {
        $objResponse            = new JsonResponse();
        $objRequest             = $this->get('request');
        $emInfraestructura      = $this->getDoctrine()->getManager("telconet_infraestructura");
        $intIdNodo              = $objRequest->get('idNodo');
        $intIdElementoPrincipal = $objRequest->get('idElementoPrincipal');
        $strNombreElemento      = $objRequest->get('nombreElemento');
        $strEstadoRelacion      = $objRequest->get('estadoRelacion');
        $strTipoElemento        = $objRequest->get('tipoElemento');
        $boolEsSecundario       = (boolean) $objRequest->get('boolEsSecundario');

        $arrayParametros = array();
        $arrayParametros['boolNodo']               = true;
        $arrayParametros['boolEsSecundario']       = $boolEsSecundario;
        $arrayParametros['intIdNodo']              = $intIdNodo;
        $arrayParametros['intIdElementoPrincipal'] = $intIdElementoPrincipal;
        $arrayParametros['arrayFiltros']           = array('strNombreElemento' => $strNombreElemento,
                                                           'strEstadoRelacion' => $strEstadoRelacion);
        $arrayParametros['strTipoElemento']        = $strTipoElemento;

        $strJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                ->getJsonElementosContenidosNodo($arrayParametros);

        $objResponse->setContent($strJson);
        return $objResponse;
    }

    /**
     * ajaxCrearTareaNodoAction
     * 
     * Metodo encargado de generar una tarea relacionada al elemento NODO
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 26-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se agrega observación al detalle de la Tarea para que se muestre en la gestión de Tareas
     * @since 21-07-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 22-07-2016 - Se realizan ajustes para poder reutilizar esta funcion desde el modulo de Tareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 13-11-2016 - Se realizan ajustes para heredar el login de la tarea padre en las subtareas
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 29-03-2017 - Se realizan ajustes para que todas las tareas generadas se realicen con estado Asignada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 29-03-2017 - Se realizan ajustes para obtener correctamente el departamento del responsable cuando la tarea es asignada
     *                           a una cuadrilla y se define variable por sugerencia del Jenkins
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 14-09-2017 - Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 27-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
     *                           realizar en la tarea es válida.
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 21-01-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.0 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 2.1 21-12-2020 - Actualización: Se agrega bloque de código para que se actualice en la DB_COMUNICACION.INFO_ENCUESTA_PREGUNTA
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.2 17-05-2021 - Actualización: Se realiza corrección para que se puedan grabar las tareas en la tabla DB_SOPORTE.INFO_TAREA
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.3 06-07-2021 - Si la creación de la tarea es por un nodo, se registra en las partes afectadas el elemento.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxCrearTareaNodoAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        
        $session = $peticion->getSession();

        $codEmpresa        = $session->get('idEmpresa');
        $intIdDepartamento = $session->get('idDepartamento');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');        
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceUtil        = $this->get('schema.Util');

        $intIdPersonaEmpresaRol = "";
        $intIdNodo            = $peticion->get('idNodo');   
        $strNombreNodo        = $peticion->get('nombreNodo');   
        $intIdPersonaEmpRol   = $peticion->get('personaEmpresaRol');
        $intIdTarea           = $peticion->get('idTarea');  
        $intAsignadoId        = $peticion->get('asignadoId');        
        $strNombreAsignado    = $peticion->get('nombreAsignado');        
        $intRefAsignadoId     = $peticion->get('refAsignadoId');        
        $strRefAsignadoNombre = $peticion->get('refAsignadoNombre');        
        $strObservacion       = $peticion->get('observacion');                     
        $objFechaEjecucion    = $peticion->get('fechaEjecucion');        
        $objHoraEjecucion     = $peticion->get('horaEjecucion');                  
        $strTipoAsignacion    = $peticion->get('tipoAsignacion'); 
        $strEmpresaAsignacion = $peticion->get('empresaAsignacion');
        $intDetalleIdRelac    = $peticion->get('detalleIdRelac')?$peticion->get('detalleIdRelac'):"";
        $numeroTarea          = $peticion->get('numeroTarea');
        $intIdPregunta        = $peticion->get('intPregunta');
        $serviceSoporte       = $this->get('soporte.SoporteService');
        $serviceProceso       = $this->get('soporte.ProcesoService');
        $arrayParametrosHist  = array();
        $strNombreProceso     = "";

        $intIdDetalleHist = $peticion->get('intIdDetalleHist');
        $strValidarAccion = $peticion->get('strValidarAccion');

        if ($intIdDetalleHist  !== '' && !empty($intIdDetalleHist)  &&
            $intDetalleIdRelac !== '' && !empty($intDetalleIdRelac) &&
            $strValidarAccion  === 'SI')
        {
            $arrayValidarAccion = $serviceSoporte->validarAccionTarea(array('intIdDetalle'     => $intDetalleIdRelac,
                                                                            'intIdDetalleHist' => $intIdDetalleHist));

            if (!$arrayValidarAccion['boolRespuesta'])
            {
               return $respuesta->setContent(json_encode(array('success'      => false,
                                                               'seguirAccion' => $arrayValidarAccion['boolRespuesta'],
                                                               'mensaje'      => $arrayValidarAccion['strMensaje'])));
            }
        }

        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $peticion->getSession()->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();

        $strAsunto            = "";
        $strEmpresaExterna    = "EMPRESAEXTERNA";
        $fecha = explode("T", $objFechaEjecucion);
        $hora  = explode("T", $objHoraEjecucion);                

        $objDate = date_create(date('Y-m-d H:i', strtotime($fecha[0] . ' ' . $hora[1]))); //Tiempo de Ejecucion
        
        //Si la fecha de ejecucion es mayor a la actual se determina una reprogramacion inicial
        if($objDate > new \DateTime('now'))
        {
            $boolReprogramadaInicio = true;
        }
        else
        {
            $boolReprogramadaInicio = false;
        }
        
        $emSoporte->getConnection()->beginTransaction();        
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            $objTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea);
                        
            $objInfoDetalle = new InfoDetalle();            
            $objInfoDetalle->setTareaId($objTarea);
            $objInfoDetalle->setPesoPresupuestado(0);
            $objInfoDetalle->setValorPresupuestado(0);
            $objInfoDetalle->setObservacion($strObservacion);
            $objInfoDetalle->setFeCreacion(new \DateTime('now'));
            $objInfoDetalle->setFeSolicitada($objDate);
            $objInfoDetalle->setObservacion($strObservacion);
            $objInfoDetalle->setUsrCreacion($peticion->getSession()->get('user'));
            $objInfoDetalle->setIpCreacion($peticion->getClientIp());

            //Se hace la relacion de la subtarea con la tarea
            if($intDetalleIdRelac)
            {
                $objInfoDetalle->setDetalleIdRelacionado($intDetalleIdRelac);
            }

            $emSoporte->persist($objInfoDetalle);
            $emSoporte->flush();
            
            //Se hereda el mismo login de la tarea padre
            if($intDetalleIdRelac)
            {
                $objInfoParteAfectada = $emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                  ->findOneByDetalleId($intDetalleIdRelac);

                if(is_object($objInfoParteAfectada))
                {
                    $objInfoCriterioAfectado = $emSoporte->getRepository('schemaBundle:InfoCriterioAfectado')
                                                         ->findOneByDetalleId($intDetalleIdRelac);

                    if(is_object($objInfoCriterioAfectado))
                    {
                        $objInfoCriterioAfectadoNuevo = new InfoCriterioAfectado();
                        $objInfoCriterioAfectadoNuevo->setId($objInfoCriterioAfectado->getId());
                        $objInfoCriterioAfectadoNuevo->setDetalleId($objInfoDetalle);
                        $objInfoCriterioAfectadoNuevo->setCriterio("Clientes");
                        $objInfoCriterioAfectadoNuevo->setOpcion($objInfoCriterioAfectado->getOpcion());
                        $objInfoCriterioAfectadoNuevo->setFeCreacion(new \DateTime('now'));
                        $objInfoCriterioAfectadoNuevo->setUsrCreacion($peticion->getSession()->get('user'));
                        $objInfoCriterioAfectadoNuevo->setIpCreacion($peticion->getClientIp());
                        $emSoporte->persist($objInfoCriterioAfectadoNuevo);
                        $emSoporte->flush();
                    }

                    $objInfoParteAfectadaNuevo = new InfoParteAfectada();
                    $objInfoParteAfectadaNuevo->setTipoAfectado($objInfoParteAfectada->getTipoAfectado());
                    $objInfoParteAfectadaNuevo->setDetalleId($objInfoDetalle->getId());
                    $objInfoParteAfectadaNuevo->setCriterioAfectadoId($objInfoParteAfectada->getCriterioAfectadoId());
                    $objInfoParteAfectadaNuevo->setAfectadoId($objInfoParteAfectada->getAfectadoId());
                    $objInfoParteAfectadaNuevo->setFeIniIncidencia($objInfoParteAfectada->getFeIniIncidencia());
                    $objInfoParteAfectadaNuevo->setAfectadoNombre($objInfoParteAfectada->getAfectadoNombre());
                    $objInfoParteAfectadaNuevo->setAfectadoDescripcion($objInfoParteAfectada->getAfectadoDescripcion());
                    $objInfoParteAfectadaNuevo->setFeCreacion(new \DateTime('now'));
                    $objInfoParteAfectadaNuevo->setUsrCreacion($peticion->getSession()->get('user'));
                    $objInfoParteAfectadaNuevo->setIpCreacion($peticion->getClientIp());
                    $emSoporte->persist($objInfoParteAfectadaNuevo);
                    $emSoporte->flush();
                }
            }

            //Se valida para que solo se relacione la tarea con el elemento, solo cuando el origen sea del modulo Tecnico
            if($intIdNodo)
            {
                //Se relaciona la Tarea con el Elemento Nodo
                $objInfoDetalleTareaElemento = new InfoDetalleTareaElemento();
                $objInfoDetalleTareaElemento->setDetalleId($objInfoDetalle);
                $objInfoDetalleTareaElemento->setElementoId($intIdNodo);
                $objInfoDetalleTareaElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleTareaElemento->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoDetalleTareaElemento->setIpCreacion($peticion->getClientIp());
                $emSoporte->persist($objInfoDetalleTareaElemento);
                $emSoporte->flush();

                //Creamos la parte afectada.
                $objInfoElementoNodo = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdNodo);
                if (is_object($objInfoElementoNodo))
                {

                    // Si descripcion de elemento tiene mas de 200 caracteres se guarda log
                    if(strlen($objInfoElementoNodo->getDescripcionElemento()) > 200)
                    {

                        $serviceUtil->insertLog(array(
                            'enterpriseCode'   => $codEmpresa,
                            'logType'          => 1,
                            'logOrigin'        => 'TELCOS',
                            'application'      => 'TELCOS',
                            'appClass'         => 'InfoElementoNodoController',
                            'appMethod'        => 'ajaxCrearTareaNodoAction',
                            'descriptionError' => 'Descripcion de elemento mayor a 200 caracteres, ' . 
                                                  'se trunca para que permita registro en INFO_PARTE_AFECTADA',
                            'status'           => 'Exitoso',
                            'inParameters'     => 'Elemento: ' . $intIdNodo . 
                                                  ' - Longuitud: ' . strlen($objInfoElementoNodo->getDescripcionElemento()) . 
                                                  ' - String: ' . $objInfoElementoNodo->getDescripcionElemento(),
                            'creationUser'     => $peticion->getSession()->get('user')));

                    }

                    $strOpcion = 'Elemento: '.$objInfoElementoNodo->getNombreElemento();
                    $objInfoCriterioAfectado = new InfoCriterioAfectado();
                    $objInfoCriterioAfectado->setId(1);
                    $objInfoCriterioAfectado->setDetalleId($objInfoDetalle);
                    $objInfoCriterioAfectado->setCriterio("Elementos");
                    $objInfoCriterioAfectado->setOpcion($strOpcion);
                    $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                    $objInfoCriterioAfectado->setUsrCreacion($peticion->getSession()->get('user'));
                    $objInfoCriterioAfectado->setIpCreacion($peticion->getClientIp());
                    $emSoporte->persist($objInfoCriterioAfectado);
                    $emSoporte->flush();

                    $objInfoParteAfectada = new InfoParteAfectada();
                    $objInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                    $objInfoParteAfectada->setDetalleId($objInfoDetalle->getId());
                    $objInfoParteAfectada->setFeIniIncidencia($objInfoDetalle->getFeCreacion());
                    $objInfoParteAfectada->setTipoAfectado("Elemento");
                    $objInfoParteAfectada->setAfectadoId($objInfoElementoNodo->getId());
                    $objInfoParteAfectada->setAfectadoNombre($objInfoElementoNodo->getNombreElemento());
                    $objInfoParteAfectada->setAfectadoDescripcion(mb_substr($objInfoElementoNodo->getDescripcionElemento(),0,200));
                    $objInfoParteAfectada->setFeCreacion(new \DateTime('now'));
                    $objInfoParteAfectada->setUsrCreacion($peticion->getSession()->get('user'));
                    $objInfoParteAfectada->setIpCreacion($peticion->getClientIp());
                    $emSoporte->persist($objInfoParteAfectada);
                    $emSoporte->flush();
                }

            }

            //Se establece la asignacion de la cuadrilla en funcion del tipo de asignado
            $objInfoDetalleAsignacion = new InfoDetalleAsignacion();
            $objInfoDetalleAsignacion->setDetalleId($objInfoDetalle);
            $objInfoDetalleAsignacion->setMotivo($strObservacion);
            $objInfoDetalleAsignacion->setUsrCreacion($peticion->getSession()->get('user'));
            $objInfoDetalleAsignacion->setFeCreacion(new \DateTime('now'));
            $objInfoDetalleAsignacion->setIpCreacion($peticion->getClientIp());
            $objInfoDetalleAsignacion->setTipoAsignado($strTipoAsignacion);
            $objInfoDetalleAsignacion->setAsignadoId($intAsignadoId);
            $objInfoDetalleAsignacion->setAsignadoNombre($strNombreAsignado);
            $objInfoDetalleAsignacion->setRefAsignadoId($intRefAsignadoId);
            $objInfoDetalleAsignacion->setRefAsignadoNombre($strRefAsignadoNombre);
            $objInfoDetalleAsignacion->setPersonaEmpresaRolId($intIdPersonaEmpRol);
            $objInfoDetalleAsignacion->setDepartamentoId($session->get('idDepartamento'));
            $intIdPersonaEmpresaRol  = $intIdPersonaEmpRol;
            $intIdPersona            = null;
            $boolExisteIntegrante    = false;
            $boolTieneLiderCuadrilla = false;
            
            if($strTipoAsignacion == 'CUADRILLA')                
            {
                $arrayCuadrillaTarea = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')->getIntegrantesCuadrilla($intAsignadoId);
                if(count($arrayCuadrillaTarea) > 0)
                {      
                    foreach($arrayCuadrillaTarea as $datoCuadrilla)
                    {                          
                        $objInfoCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                                        ->getLiderCuadrilla($datoCuadrilla['idPersona']);

                        //Si existe Lider de cuadrilla se setea a informacion
                        if($objInfoCuadrilla)
                        {
                            $boolTieneLiderCuadrilla = true;
                            $boolExisteIntegrante    = true;
                            $objInfoDetalleAsignacion->setPersonaEmpresaRolId($objInfoCuadrilla[0]['personaEmpresaRolId']);
                            $intIdPersonaEmpresaRol = $objInfoCuadrilla[0]['personaEmpresaRolId'];
                            $intIdPersonaEmpRol = $objInfoCuadrilla[0]['personaEmpresaRolId'];
                            $intIdPersona = $datoCuadrilla['idPersona'];
                            break;
                        }
                    }  

                    //Si no existe lider de cuadrilla, se escoge otro miembro de la misma para referenciarlo en el detalle de la tarea
                    if(!$boolTieneLiderCuadrilla)
                    {                          
                        foreach($arrayCuadrillaTarea as $datoCuadrilla)
                        {                                                                                                                                              
                            $intRol = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getRolJefeCuadrilla(); 

                            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->findOneBy(array('empresaRolId' => $intRol, 
                                                                                      'personaId'    => $datoCuadrilla['idPersona'],
                                                                                      'estado'       => "Activo")
                                                                               );  
                            if($objInfoPersonaEmpresaRol)
                            {                                    
                                $boolExisteIntegrante = true;                                    
                                $objInfoDetalleAsignacion->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol->getId());
                                $intIdPersonaEmpresaRol = $objInfoPersonaEmpresaRol->getId();
                                $intIdPersonaEmpRol = $objInfoPersonaEmpresaRol->getId();
                                $intIdPersona = $datoCuadrilla['idPersona'];
                                break;
                            }
                        }                         
                    }   

                    //Si existe al menos un integrante en la cuadrilla se guarda la referencia en la asignacion de la tarea
                    if($boolExisteIntegrante)
                    {
                        $empleadoLider = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);                           
                        $objInfoDetalleAsignacion->setRefAsignadoId(($empleadoLider->getId())?$empleadoLider->getId():"");                    
                        $objInfoDetalleAsignacion->setRefAsignadoNombre(($empleadoLider->__toString())?$empleadoLider->__toString():""); 
                    }
                }     
                
                //Se ingresas integrantes de la cuadrilla para vincularlos a la tarea respectiva
                foreach($arrayCuadrillaTarea as $datoCuadrilla)
                {
                    $objInfoCuadrillaTarea = new InfoCuadrillaTarea();
                    $objInfoCuadrillaTarea->setDetalleId($objInfoDetalle);
                    $objInfoCuadrillaTarea->setCuadrillaId($intAsignadoId);
                    $objInfoCuadrillaTarea->setPersonaId($datoCuadrilla['idPersona']);
                    $objInfoCuadrillaTarea->setUsrCreacion($peticion->getSession()->get('user'));
                    $objInfoCuadrillaTarea->setFeCreacion(new \DateTime('now'));
                    $objInfoCuadrillaTarea->setIpCreacion($peticion->getClientIp());
                    $emSoporte->persist($objInfoCuadrillaTarea);
                    $emSoporte->flush();
                }
            }

            if($session->get('idPersonaEmpresaRol'))
            {
                $objPersonaEmpresaRol  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                     ->find($session->get('idPersonaEmpresaRol'));

                if($objPersonaEmpresaRol)
                {
                    $objInfoOficinaGrupo  = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                        ->find($objPersonaEmpresaRol->getOficinaId());
                    if($objInfoOficinaGrupo)
                    {
                        $objInfoDetalleAsignacion->setCantonId($objInfoOficinaGrupo->getCantonId());
                    }
                }
            }

            $emSoporte->persist($objInfoDetalleAsignacion);
            $emSoporte->flush(); 

            //Se ingresa el historial de la tarea
            if(is_object($objInfoDetalle))
            {
                $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();            
            }
                        
            $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
            $arrayParametrosHist["strEstadoActual"] = "Asignada";                    
            $arrayParametrosHist["intAsignadoId"]   = $intAsignadoId;
            $arrayParametrosHist["strAccion"]       = "Asignada";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                       
            
            //Obtencion del mensaje generico para el seguiemiento de la tarea
            if($boolReprogramadaInicio)
            {
                if($strTipoAsignacion == "CUADRILLA")
                {
                    $mensaje = "Tarea fue Asignada a la cuadrilla ".$strNombreAsignado." y Reprogramada para el ".date_format($objDate,'Y-m-d H:i');
                }
                else if($strTipoAsignacion == $strEmpresaExterna)
                {
                    $mensaje = "Tarea fue Asignada a " . $strNombreAsignado ." y Reprogramada para el " . date_format($objDate,'Y-m-d H:i');
                }
                else
                {
                    $mensaje = "Tarea fue Asignada a " . $strRefAsignadoNombre . " y Reprogramada para el " . date_format($objDate,'Y-m-d H:i');
                }
            }
            else
            {
                if($strTipoAsignacion == "CUADRILLA")
                {
                    $mensaje = "Tarea fue Asignada a la Cuadrilla " . $strNombreAsignado;
                }
                else if($strTipoAsignacion == $strEmpresaExterna)
                {
                    $mensaje = "Tarea fue Asignada a " . $strNombreAsignado;
                }
                else
                {
                    $mensaje = "Tarea fue Asignada a " . $strRefAsignadoNombre;
                }
            }

            $arrayParametrosHist["strObservacion"]  = $mensaje;
            $arrayParametrosHist["strEstadoActual"] = "Asignada";                    
            $arrayParametrosHist["strOpcion"]       = "Seguimiento";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);              
            
            //Notificacion de Correo de Tarea generada
            if($intIdNodo)
            {
                $strAsunto = "Asignación de Tarea relacionada con el NODO : ".$strNombreNodo;
            }
            else
            {
                if($numeroTarea)
                {
                    $strAsunto = "Asignación de Subtarea relacionada a la Tarea # : ".$numeroTarea;
                }
                else
                {
                    $strAsunto = "Asignación de Tarea ";
                }
            }
            $objClase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneByNombreClaseDocumento("Notificacion");
            
            switch($strTipoAsignacion)
            {
                case 'EMPLEADO':
                    $mensaje = "Asignacion de Tarea a " . $strRefAsignadoNombre;
                    $personaIdCorreo = $intRefAsignadoId;
                    break;
                case 'CUADRILLA':
                    $mensaje = "Asignacion de Tarea a la Cuadrilla" . $strNombreAsignado;
                    $personaIdCorreo = $intIdPersona;
                    break;
                case $strEmpresaExterna:
                    $mensaje = "Asignacion de Tarea a " . $strNombreAsignado;
                    $personaIdCorreo = $intAsignadoId;
                    break;
            }
                        
            $objInfoDocumento = new InfoDocumento();
            $objInfoDocumento->setMensaje($mensaje);
            $objInfoDocumento->setClaseDocumentoId($objClase);
            $objInfoDocumento->setEstado('Activo');
            $objInfoDocumento->setNombreDocumento($strAsunto);
            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
            $objInfoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
            $objInfoDocumento->setIpCreacion($peticion->getClientIp());
            $objInfoDocumento->setEmpresaCod($codEmpresa);
            $emComunicacion->persist($objInfoDocumento);
            $emComunicacion->flush();

            $objInfoComunicacion = new InfoComunicacion();            
            $objInfoComunicacion->setDetalleId($objInfoDetalle->getId());
            $objInfoComunicacion->setFormaContactoId(5);
            $objInfoComunicacion->setClaseComunicacion("Enviado");
            $objInfoComunicacion->setFechaComunicacion(new \DateTime('now'));
            $objInfoComunicacion->setFeCreacion(new \DateTime('now'));
            $objInfoComunicacion->setEstado('Activo');
            $objInfoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
            $objInfoComunicacion->setIpCreacion($peticion->getClientIp());
            $objInfoComunicacion->setEmpresaCod($codEmpresa);
            $emComunicacion->persist($objInfoComunicacion);
            $emComunicacion->flush();

            $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
            $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacion);
            $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
            $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoComunicacion->setEstado('Activo');
            $objInfoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
            $objInfoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
            $emComunicacion->persist($objInfoDocumentoComunicacion);
            $emComunicacion->flush(); 
            
            if($intRefAsignadoId || $strTipoAsignacion == "CUADRILLA" || $strTipoAsignacion == $strEmpresaExterna)
            {
                $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                           ->findOneBy(array('personaId'       => $personaIdCorreo,
                                                                             'formaContactoId' => 5,
                                                                             'estado'          => "Activo"
                                                                            ));                

                if($objInfoPersonaFormaContacto)
                {
                    $to[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                }
            }


            // Se agrega creacion de solicitud y caracteristica

            if($intIdNodo)
            {

                //Obtenemos el tipo de motivo.
                $strCaracteristica        = "GESTIÓN POR ACCESOS AL NODO";
                $objAdmiTipoMotivo= $emComercial->getRepository("schemaBundle:AdmiMotivo")
                    ->findOneBy(array("nombreMotivo" => $strCaracteristica,
                                      "estado"                    => "Activo"));

                if (!is_object($objAdmiTipoMotivo))
                {
                    throw new \Exception("Error : No se logro obtener el motivo ($strCaracteristica).");
                }

                //Obtenemos el tipo de solicitud.
                $strCaracteristica        = "SOLICITUD PLANIFICACION";
                $objAdmiTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                    ->findOneBy(array("descripcionSolicitud" => $strCaracteristica,
                                      "estado"                    => "Activo"));

                if (!is_object($objAdmiTipoSolicitud))
                {
                    throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
                }

                //Obtenemos la característica para la solicitud.
                $strCaracteristica        = "ELEMENTO NODO";
                $objAdmiCaracteristicaSol =  $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                        "estado"                    => "Activo"));

                if (!is_object($objAdmiCaracteristicaSol))
                {
                    throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
                }

                //Obtenemos la característica para la tarea.
                $strCaracteristica        = "SOLICITUD NODO";
                $objAdmiCaracteristicaTar =  $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                        "estado"                    => "Activo"));

                if (!is_object($objAdmiCaracteristicaTar))
                {
                    throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
                }


                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setTipoSolicitudId($objAdmiTipoSolicitud);
                $objDetalleSolicitud->setMotivoId($objAdmiTipoMotivo->getId());
                $objDetalleSolicitud->setObservacion("INSTALACION");
                $objDetalleSolicitud->setElementoId($intIdNodo);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($peticion->getSession()->get('user'));
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setFeEjecucion(new \DateTime('now'));
                $emComercial->persist($objDetalleSolicitud);
                $emComercial->flush();

                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objSolicitudHistorial->setEstado("AsignadoTarea");
                $objSolicitudHistorial->setObservacion("Se crea Solicitud elementos en Nodo");
                $objSolicitudHistorial->setMotivoId($objAdmiTipoMotivo->getId());
                $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                $objSolicitudHistorial->setIpCreacion($peticion->getClientIp());
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();

                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristicaSol);
                $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $objInfoDetalleSolCaract->setValor($intIdNodo);
                $objInfoDetalleSolCaract->setEstado("AsignadoTarea");
                $objInfoDetalleSolCaract->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objInfoDetalleSolCaract);
                $emComercial->flush();

                $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                $objInfoTareaCaracteristica->setTareaId($objInfoComunicacion->getId());
                $objInfoTareaCaracteristica->setDetalleId($objInfoDetalle->getId());
                $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristicaTar->getId());
                $objInfoTareaCaracteristica->setValor($objDetalleSolicitud->getId());
                $objInfoTareaCaracteristica->setEstado("Activo");
                $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                $objInfoTareaCaracteristica->setUsrCreacion($peticion->getSession()->get('user'));
                $objInfoTareaCaracteristica->setIpCreacion($peticion->getClientIp());
                $emSoporte->persist($objInfoTareaCaracteristica);
                $emSoporte->flush();

            }


            //Envio de Notificacion de generacion de nueva tarea
            $empresa      = '';
            $canton       = '';
            $departamento = '';
            
            if($strTipoAsignacion == "EMPLEADO" || $strTipoAsignacion == "CUADRILLA" || $strTipoAsignacion == $strEmpresaExterna)
            {
                $empresa                  = $strEmpresaAsignacion;
                $departamento             = $intAsignadoId;
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpRol);

                if($objInfoPersonaEmpresaRol)
                {
                    $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                           ->find($objInfoPersonaEmpresaRol->getOficinaId()->getId());

                    $canton = $oficina->getCantonId();

                    if ($strTipoAsignacion == "CUADRILLA")
                    {
                        $departamento = $objInfoPersonaEmpresaRol->getDepartamentoId();
                    }
                }
            }

            if(is_object($objTarea))
            {
                $strNombreProceso = $objTarea->getProcesoId()->getNombreProceso();
            }

            $strAsunto = $strAsunto . " | PROCESO: ".$strNombreProceso;

            /* @var $envioPlantilla EnvioPlantilla */
            $envioPlantilla = $this->get('soporte.EnvioPlantilla');

            $arrayParametros = array('nombreProceso'  => $strNombreProceso,
                                     'actividad'      => $objInfoComunicacion,
                                     'asignacion'     => $objInfoDetalleAsignacion,
                                     'nombreTarea'    => $objTarea->getNombreTarea(),
                                     'empleadoLogeado'=> $peticion->getSession()->get('empleado'),
                                     'empresa'        => $peticion->getSession()->get('prefijoEmpresa'),
                                     'detalle'        => $objInfoDetalle
                                    );	

            $envioPlantilla->generarEnvioPlantilla($strAsunto, $to, 'TAREAACT', $arrayParametros, $empresa, $canton, $departamento);           

            //Proceso para crear la tarea en el sistema de DC - Sys Cloud Center.
            if ($strTipoAsignacion === "EMPLEADO")
            {
                 $serviceProceso->putTareasSysCluod(array ('strNombreTarea'      => $objTarea->getNombreTarea(),
                                                           'strNombreProceso'    => $objTarea->getProcesoId()->getNombreProceso(),
                                                           'strObservacion'      => $strObservacion,
                                                           'strFechaApertura'    => $fecha[0],
                                                           'strHoraApertura'     => $hora[1],
                                                           'strUser'             => $session->get('user'),
                                                           'strIpAsigna'         => $peticion->getClientIp(),
                                                           'strUserAsigna'       => $session->get('empleado'),
                                                           'strDeparAsigna'      => $session->get('departamento'),
                                                           'strUserAsignado'     => $strRefAsignadoNombre,
                                                           'strDeparAsignado'    => $strNombreAsignado,
                                                           'objInfoComunicacion' => $objInfoComunicacion));
            }
            if($intIdPregunta)
            {
                $objAdmiCaracteristica = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneByDescripcionCaracteristica('Archivo');
                if (is_object($objAdmiCaracteristica))
                {
                    $objPregunta = $emComunicacion->getRepository("schemaBundle:InfoEncuestaPregunta")->find($intIdPregunta);
                    if (is_object($objPregunta))
                    {
                        $strValor = $objPregunta->getValor() . "<br>"
                                    . "Número Tarea: " . $objInfoComunicacion->getId() . "<br>"
                                    . "Nombre Tarea: " . $objTarea->getNombreTarea()   . "<br>"
                                    . "Departamento asignado: "  . $strNombreAsignado  . "<br>"
                                    . "Empleado asignado: "      . $strRefAsignadoNombre  . "<br>"
                                    . "Observación: "            . $strObservacion        . "<br>";
                        $objPregunta->setValor($strValor);
                        $emComunicacion->persist($objPregunta);
                        $emComunicacion->flush();
                        $objEncuesta = $emComunicacion->getRepository("schemaBundle:InfoEncuesta")->find($objPregunta->getEncuestaId());
                        $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                        $objInfoTareaCaracteristica->setTareaId($objInfoComunicacion->getId());
                        $objInfoTareaCaracteristica->setDetalleId($objInfoDetalle->getId());
                        $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristica->getId());
                        $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                        $objInfoTareaCaracteristica->setUsrCreacion($session->get('user'));
                        $objInfoTareaCaracteristica->setIpCreacion($peticion->getClientIp());
                        $objInfoTareaCaracteristica->setValor($objEncuesta->getCodigo());
                        $objInfoTareaCaracteristica->setEstado('Activo');
                        $emSoporte->persist($objInfoTareaCaracteristica);
                        $emSoporte->flush();
                    }
                }
            }
            
            $emSoporte->getConnection()->commit();
            $emComercial->getConnection()->commit();
            $emComunicacion->getConnection()->commit(); 
            
            $jsonResultado = json_encode(array('success' => true, 'mensaje' => 'Tarea No. '. $objInfoComunicacion->getId() .' creada correctamente'));

            //Proceso que graba tarea en INFO_TAREA
            if (is_object($objInfoDetalle))
            {
                $arrayParametrosInfoTarea['intDetalleId']   = $objInfoDetalle->getId();
                $arrayParametrosInfoTarea['strUsrCreacion'] = $peticion->getSession()->get('user');
                $objServiceSoporte                          = $this->get('soporte.SoporteService');
                $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
            }

        } 
        catch (Exception $ex) 
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();        

            $jsonResultado = json_encode(array('success' => false, 'mensaje' => $ex->getMessage()));
        }                                        
        
        $respuesta->setContent($jsonResultado);

        return $respuesta;
    }
    
    /**
     * ajaxVerTareaNodoAction
     * 
     * Metodo encargado de ver las tareas relacionadas al nodo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 26-02-2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxVerTareaNodoAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');                

        $peticion = $this->get('request');

        $idNodo = $peticion->get('idNodo');

        $objJson = $this->getDoctrine()->getManager("telconet_soporte")->getRepository('schemaBundle:AdmiTarea')
                                                                       ->getJsonTareasPorElementoNodo($idNodo);        
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
      * ajaxShowImagenesNodo
      *
      * Método que obtiene las imagenes ingresadas por solicitud
      *                                                                             
      * @return json con resultado            
      * 
      * @author Allan Suarez <arsuarez@telconet.ec>
      * @version 1.0 - 09-03-2015
      */ 
    public function ajaxShowImagenesNodoAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');                

        $peticion = $this->get('request');

        $idNodo = $peticion->get('idNodo');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:InfoDocumento')
            ->getDocumentoImagenesNodo($idNodo);
        
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
      * ajaxFileUploadNodo
      *
      * Método que guarda una unica imagen al nodo
      *                                                                             
      * @return json con resultado            
      * 
      * @author <arsuarez@telconet.ec>
      * @version 1.0 - 10-03-2015
      * 
      * @author <arsuarez@telconet.ec>
      * @version 1.1 - 19-03-2016 - Edicion de ubicacion fisica de la imagen del nodo
      */ 
    public function ajaxFileUploadNodoAction()
    {
        $request = $this->getRequest();
        $peticion = $this->get('request');
        
        $empresaCod = $peticion->getSession()->get('idEmpresa');
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');
        
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');  
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $em->getConnection()->beginTransaction(); 

        $idNodo = $peticion->get('idNodo');
        $tag    = $peticion->get('tag');
        
        $fileRoot = $this->container->getParameter('ruta_upload_documentos');
        
        $path = $this->container->getParameter('path_telcos');
        
        $ubicacionFisica = '';
        
        $boolUploadOk = false;

        try
        {           
            $file = $request->files;

            $objArchivo = $file->get('archivo');

            if($file && count($file) > 0)
            {
                if(isset($objArchivo))
                {
                    if($objArchivo && count($objArchivo) > 0)
                    {                        	
                        $archivo = $objArchivo->getClientOriginalName();                        

                        $arrayArchivo = explode('.', $archivo);
                        $countArray   = count($arrayArchivo);                        
                        $extArchivo   = $arrayArchivo[$countArray - 1];
                        
                        $objElementoNodo  = $em->getRepository('schemaBundle:InfoElemento')->find($idNodo);
                        
                        $objInfoDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                    ->findOneBy(array('elementoId'   => $idNodo,
                                                                                      'detalleNombre'=>'IMAGEN NODO')
                                                                                ); 

                        $prefijo = uniqid();

                        if($archivo != "")
                        {
                            $nuevoNombre = $objElementoNodo->getNombreElemento() . "_" . $prefijo . "." . $extArchivo;

                            $nuevoNombre = str_replace(" ", "_", $nuevoNombre);        
                            
                            $idNodoRef   = $idNodo;                            
                            
                            //Si es un nodo migrado usamos la misma ruta de imagenes predefinidas
                            if($objInfoDetalleElemento)
                            {
                                $idNodoRef = $objInfoDetalleElemento->getDetalleValor();                                
                            }                            
                            
                            $filePath = $fileRoot."nodos/nodo_".$idNodoRef."/fotos/";
                            $destino  = $path.$filePath;
                            
                            $ubicacionFisica = $destino.$nuevoNombre;
                            
                            if($objArchivo->move($destino, $nuevoNombre))
                            {
                                //Guardar imagen
                                $objTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumento')->find(1);
                                
                                $objInfoDocumento = new InfoDocumento();                                    
                                $objInfoDocumento->setNombreDocumento("Imagen Nodo : ".$objElementoNodo->getNombreElemento()); 
                                $objInfoDocumento->setUbicacionLogicaDocumento($nuevoNombre);
                                $objInfoDocumento->setUbicacionFisicaDocumento($this->UPLOAD_PATH."nodos/nodo_".$idNodoRef."/fotos/".$nuevoNombre);
                                $objInfoDocumento->setFechaDocumento(new \DateTime('now'));                                                                 
                                $objInfoDocumento->setUsrCreacion($request->getSession()->get('user'));
                                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumento->setIpCreacion($request->getClientIp());
                                $objInfoDocumento->setEstado('Activo');         
                                $objInfoDocumento->setEmpresaCod($empresaCod);    
                                $objInfoDocumento->setTipoDocumentoGeneralId(9);//id de tipo documento Elemento
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);       //id de tipo de imagen basico                                                                                                         
                                $em->persist($objInfoDocumento);      
                                $em->flush();
                                                                
                                $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                                $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                                $objInfoDocumentoRelacion->setModulo('TECNICO');                                                           
                                $objInfoDocumentoRelacion->setElementoId($idNodo);                                 
                                $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                                $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));                        
                                $objInfoDocumentoRelacion->setUsrCreacion($request->getSession()->get('user'));
                                $em->persist($objInfoDocumentoRelacion);      
    
                                $objDocumentoTag = new InfoDocumentoTag();
                                $objDocumentoTag->setDocumentoId($objInfoDocumento->getId());
                                $objDocumentoTag->setTagDocumentoId($tag);
                                $objDocumentoTag->setEstado('Activo');                                                                                   
                                $objDocumentoTag->setFeCreacion(new \DateTime('now'));                        
                                $objDocumentoTag->setUsrCreacion($request->getSession()->get('user'));
                                $objDocumentoTag->setIpCreacion($request->getClientIp());
                                $em->persist($objDocumentoTag);  

                                $em->flush();
                                $em->getConnection()->commit(); 
                                
                                $boolUploadOk = true;                                                               
                            }                                                                                 
                        }                        
                    }//FIN IF ARCHIVO SUBIDO                    
                }//FIN IF ARCHIVO                
            }//FIN IF FILES            
            
            if($boolUploadOk)
            {
                $resultado = '{"success":true,"respuesta":"Archivo Procesado Correctamente"}';                                                                                              
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            }

            $respuesta->setContent($resultado);
            return $respuesta;
        }
        catch(\Exception $e)
        {            
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            unlink($ubicacionFisica);
            
            $resultado = '{"success":false,"respuesta":"' . $e->getMessage() . '"}';
            $respuesta->setContent($resultado);
            return $respuesta;
        }
    }

    /**
      * ajaxEditarImagenNodoAction
      *
      * Método que guarda una unica imagen al nodo
      *                                                                             
      * @return json con resultado            
      * 
      * @author Allan Suarez <arsuarez@telconet.ec>
      * @version 1.0 - 12-03-2015
      * 
      * @author Allan Suarez <arsuarez@telconet.ec>
      * @version 1.1 - 19-03-2016 - Modificacion de ruta destino de imagen de nodo
      */ 
    public function ajaxEditarImagenNodoAction()
    {
        $request = $this->getRequest();
        $peticion = $this->get('request');                
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');
        
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');  
        
        $ubicacionFisica = '';
        
        try
        {
        
            $em->getConnection()->beginTransaction(); 

            $idImagen      = $peticion->get('idImagen');
            $idTagNuevo    = $peticion->get('tagNuevo');
            $idTagViejo    = $peticion->get('tagViejo');
            $idNodo        = $peticion->get('idNodo');

            $boolCambiaTag = false;

            if($idTagNuevo!='' && ($idTagNuevo != $idTagViejo))
            {
                $boolCambiaTag = true;
            }

            $fileRoot = $this->container->getParameter('ruta_upload_documentos');
            $path     = $this->container->getParameter('path_telcos');           

            $boolUploadOk = true;

            $objInfoDocumento = $em->getRepository("schemaBundle:InfoDocumento")->find($idImagen);   

            $objInfoElemento  = $em->getRepository("schemaBundle:InfoElemento")->find($idNodo);

            $objInfoDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findOneBy(array('elementoId'   => $idNodo,
                                                                          'detalleNombre'=>'IMAGEN NODO')
                                                               );                          
            $file = $request->files;

            $objArchivo = $file->get('archivo');

            if($file && count($file) > 0)
            {
                if(isset($objArchivo))
                {
                    if($objArchivo && count($objArchivo) > 0)
                    {                        	
                        $archivo = $objArchivo->getClientOriginalName();                        

                        $arrayArchivo = explode('.', $archivo);
                        $countArray = count($arrayArchivo);                        
                        $extArchivo = $arrayArchivo[$countArray - 1];

                        $prefijo = uniqid();

                        if($archivo != "")
                        {
                            $nuevoNombre = $objInfoElemento->getNombreElemento() . "_" . $prefijo . "." . $extArchivo;

                            $nuevoNombre = str_replace(" ", "_", $nuevoNombre);                                                             
                                                        
                            //Si es un nodo migrado usamos la misma ruta de imagenes predefinidas
                            if($objInfoDetalleElemento)
                            {
                                $idNodo = $objInfoDetalleElemento->getDetalleValor();                                
                            }
                            
                            $filePath = $fileRoot."nodos/nodo_".$idNodo."/fotos/";
                            $destino  = $path.$filePath;
                            
                            $ubicacionFisica = $destino.$nuevoNombre;
                            
                            if($objArchivo->move($destino, $nuevoNombre))
                            {
                                
                                $ubicacionActual = $path.$objInfoDocumento->getUbicacionFisicaDocumento();
                                
                                $objInfoDocumento->setUbicacionLogicaDocumento($nuevoNombre);
                                $objInfoDocumento->setUbicacionFisicaDocumento($this->UPLOAD_PATH."nodos/nodo_".$idNodo."/fotos/".$nuevoNombre); 
                                $objInfoDocumento->setUsrCreacion($request->getSession()->get('user'));
                                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumento->setIpCreacion($request->getClientIp());
                                $objInfoDocumento->setEstado('Modificado');                                                                                   
                                $em->persist($objInfoDocumento);      
                                $em->flush();                                                                                                                                                                                                
                                
                                unlink($ubicacionActual);
                            }                                                                                 
                        }                        
                    }//FIN IF ARCHIVO SUBIDO                    
                }//FIN IF ARCHIVO                
            }//FIN IF FILES    
            
            //Si cambia tag se actualiza la relacion con el TAG seleccionado
            if($boolCambiaTag)
            {
                $objDocumentoTag  = $em->getRepository("schemaBundle:InfoDocumentoTag")
                                       ->findOneBy(array('tagDocumentoId'=>$idTagViejo,'documentoId'=>$idImagen));

                if($objDocumentoTag)
                {
                    $objDocumentoTag->setTagDocumentoId($idTagNuevo);
                    $objDocumentoTag->setEstado('Modificado');                                                                                   
                    $objDocumentoTag->setFeCreacion(new \DateTime('now'));                        
                    $objDocumentoTag->setUsrCreacion($request->getSession()->get('user'));
                    $objDocumentoTag->setIpCreacion($request->getClientIp());
                    $em->persist($objDocumentoTag);  
                    $em->flush();                                        
                }                                
            }
            
            if($boolUploadOk)
            {
                $resultado = '{"success":true,"respuesta":"Imagen Editada Correctamente"}';      
                
                $em->getConnection()->commit();
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            }

            $respuesta->setContent($resultado);
            return $respuesta;
        }
        catch(\Exception $e)
        {            
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            unlink($ubicacionFisica);
            
            $resultado = '{"success":true,"respuesta":"' . $e->getMessage() . '"}';
            $respuesta->setContent($resultado);
            return $respuesta;
        }
    }
    
    /**
      * ajaxGetTagsAction
      *
      * Metodo que obtiene los tags para relacionarlos a las imagenes
      *                                                                             
      * @return json con resultado            
      * 
      * @author Allan Suarez <arsuarez@telconet.ec>
      * 
      * @version 1.0 - 10-03-2015
      */
    public function ajaxGetTagsAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');                       
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:AdmiTagDocumento')
            ->generarJsonEntidades('','Activo','','');
        
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
      * ajaxEliminarImagenNodo
      *
      * Metodo que elimina la imagen relacionada al nodo
      *                                                                             
      * @return json con resultado            
      * 
      * @version 1.0 - 10-03-2015
      */
    public function ajaxEliminarImagenNodoAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');
        
        $path = $this->container->getParameter('path_telcos');
        
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $em->getConnection()->beginTransaction();
        
        $peticion = $this->get('request');
        $idImagen = $peticion->get('idImagen');
        
        try
        {        
            $objDocumento = $em->getRepository("schemaBundle:InfoDocumento")->find($idImagen);

            if($objDocumento)
            {
                $rutaImagen = $path.$objDocumento->getUbicacionFisicaDocumento();
                $objDocumento->setEstado('Eliminado');
                $objDocumento->setFecreacion(new \DateTime('now'));
                $objDocumento->setUsrCreacion($peticion->getSession()->get('user'));            
                $em->persist($objDocumento);  

                $em->flush();
                $em->getConnection()->commit(); 

                unlink($rutaImagen);

                $resultado = '{"success":true,"respuesta":"Imagen borrada correctamente"}';
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"No existe imagen relacionada para eliminar"}';
            }                               
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
        }
                
        $respuesta->setContent($resultado);
        return $respuesta;
    }  
    
    /**
     * Metodo que se encarga de exportar el reporte de los Nodos escogidos mediante los filtros definidos
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 29-02-2016
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 20-06-2019 - Se agrega nuevo campo de búsqueda que es la identificación del cliente
     *
     */
    public function exportarNodosAction()
    {       
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $peticion = $this->get('request');        
        $session  = $peticion->getSession();  
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");                     

        $arrayParams = array();                                                        
        
        $arrayParams['nombre']          = $peticion->query->get('nombreElemento');
        $arrayParams['identificacion']  = $peticion->query->get('identificacion');
        $arrayParams['empresa']         = $session->get('idEmpresa');
        $arrayParams['estado']          = $peticion->query->get('estadoNodo');
        $arrayParams['estadoSol']       = $peticion->query->get('estadoSolicitud');
        $arrayParams['motivo']          = $peticion->query->get('motivo');        
        $arrayParams['canton']          = $peticion->query->get('canton');       
        $arrayParams['provincia']       = $peticion->query->get('provincia');  
        $arrayParams['usuario']         = $session->get('user');   
        
        $clase = null;
        
        if($peticion->query->get('clase') && ($peticion->query->get('clase')!='null' && $peticion->query->get('clase')!='Todos'))
        {
            $objDetalle = $emInfraestructura->getRepository("schemaBundle:AdmiDetalle")->find($peticion->query->get('clase'));
            if($objDetalle)
            {
                $clase = $objDetalle->getNombreDetalle();
            }
        }        
        
        $arrayParams['clase']      = $clase;
        $arrayParams['em']         = $emInfraestructura;
        
        $arrayResultado = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getResultadoExportarNodos($arrayParams);                   
                
        $this->exportarExcelNodos($arrayResultado['resultado'],$arrayParams);
        
    }
    
    /**
     * Metodo encargado de exportar la informacion de los Nodos existentes
     * 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 29-02-2016
     * 
     * @param type $resultado
     * @param type $arrayParametros
     */
    public function exportarExcelNodos($resultado, $arrayParametros)
    {
        try
        {
            $objPHPExcel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '1024MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel->getProperties()->setCreator("TELCOS+");
            $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros['usuario']);
            $objPHPExcel->getProperties()->setSubject("Reporte Nodos");
            $objPHPExcel->getProperties()->setDescription("Reporte Nodos");
            $objPHPExcel->getProperties()->setKeywords("Nodos");
            $objPHPExcel->getProperties()->setCategory("Reporte");
            
            /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
            $serviceCrypt = $this->get('seguridad.Crypt');
            
            //Obteniendo valor de filtros
            $strProvincia = "-";
            $strCanton    = "-";
            $strMotivo    = "-";
            
            if($arrayParametros['provincia'] && $arrayParametros['provincia']!='null')
            {
                $objProvincia = $arrayParametros['em']->getRepository('schemaBundle:AdmiProvincia')->find($arrayParametros['provincia']);
                if($objProvincia)
                {
                    $strProvincia = $objProvincia->getNombreProvincia();
                }
            }
            
            if($arrayParametros['canton'] && $arrayParametros['canton']!='null')
            {
                $objCanton = $arrayParametros['em']->getRepository('schemaBundle:AdmiCanton')->find($arrayParametros['canton']);
                if($objCanton)
                {
                    $strCanton = $objCanton->getNombreCanton();
                }
            }
            
            if($arrayParametros['motivo'] && $arrayParametros['motivo']!='null')
            {
                $objMotivo = $arrayParametros['em']->getRepository('schemaBundle:AdmiMotivo')->find($arrayParametros['motivo']);
                if($objMotivo)
                {
                    $strMotivo = $objMotivo->getNombreMotivo();
                }
            }
            
            //Crea estilo para el titulo del reporte
            $arrayStyleTitulo = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 12,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                     'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM
                                    ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $arrayStyleMensajes = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            );
            
            $arrayStyleFiltros = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            );

            //Crea estilo para la cabecera del reporte
            $arrayStyleCabecera = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '888888')
                )
            );

            //Crea estilo para el cuerpo del reporte
            $arrayStyleBodyTable = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $strPath = $this->get('kernel')->getRootDir() . '/../web/public/images/logo_telconet.jpg';
            
            $columnBegin = 10;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $columnBegin, 'Nombre')
                ->setCellValue('B' . $columnBegin, 'Solicitante')
                ->setCellValue('C' . $columnBegin, 'Estado')
                ->setCellValue('D' . $columnBegin, 'Provincia')
                ->setCellValue('E' . $columnBegin, 'Canton')
                ->setCellValue('F' . $columnBegin, 'Direccion')
                ->setCellValue('G' . $columnBegin, 'Longitud')
                ->setCellValue('H' . $columnBegin, 'Latitud')
                ->setCellValue('I' . $columnBegin, 'Clase')
                ->setCellValue('J' . $columnBegin, 'Tipo')
                ->setCellValue('K' . $columnBegin, 'Clase Medidor')
                ->setCellValue('L' . $columnBegin, 'Tipo Medidor')
                ->setCellValue('M' . $columnBegin, 'Numero Medidor')
                ->setCellValue('N' . $columnBegin, 'Tipo Contacto')
                ->setCellValue('O' . $columnBegin, 'Nombre')
                ->setCellValue('P' . $columnBegin, 'Tipo ID')
                ->setCellValue('Q' . $columnBegin, 'Identificacion')
                ->setCellValue('R' . $columnBegin, 'Arriendo')
                ->setCellValue('S' . $columnBegin, 'Anticipo')
                ->setCellValue('T' . $columnBegin, 'Inicio')
                ->setCellValue('U' . $columnBegin, 'Duracion')
                ->setCellValue('V' . $columnBegin, 'Fin')
                ->setCellValue('W' . $columnBegin, 'Tipo Cta.')
                ->setCellValue('X' . $columnBegin, 'No. Cta')
                ->setCellValue('Y' . $columnBegin, 'Banco')
                ->setCellValue('Z' . $columnBegin, 'Forma Pago');               

            $objPHPExcel->getActiveSheet()->getStyle('A10:Z10')->applyFromArray($arrayStyleCabecera);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle('A10:Z10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);

            $objPHPExcel->getActiveSheet()->mergeCells('A1:Z1');            
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte de Nodos');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
            
            $objPHPExcel->getActiveSheet()->mergeCells('A2:Z2');
            
            $objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
            $objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
            $objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
            $objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
            $objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
            $objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
            $objPHPExcel->getActiveSheet()->mergeCells('A9:B9');
            
            $objPHPExcel->getActiveSheet()->mergeCells('C3:Z3');
            $objPHPExcel->getActiveSheet()->mergeCells('C4:Z4');
            $objPHPExcel->getActiveSheet()->mergeCells('C5:Z5');
            $objPHPExcel->getActiveSheet()->mergeCells('C6:Z6');
            $objPHPExcel->getActiveSheet()->mergeCells('C7:Z7');
            $objPHPExcel->getActiveSheet()->mergeCells('C8:Z8');
            $objPHPExcel->getActiveSheet()->mergeCells('C9:Z9');
            
            //Filtros
            $objPHPExcel->getActiveSheet()->setCellValue('A2', 'FILTROS');
            $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($arrayStyleMensajes);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Nombre Nodo:');
            $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C3', $arrayParametros['nombre']);
            $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Motivo:');
            $objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C4', $strMotivo);
            $objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A5', 'Clase Nodo:');
            $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C5', $arrayParametros['clase']!=null?$arrayParametros['clase']:'-');
            $objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A6', 'Provincia:');
            $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C6', $strProvincia);
            $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Canton:');
            $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C7', $strCanton);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A8', 'Estado Nodo:');
            $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C8', $arrayParametros['estado']);
            $objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($arrayStyleFiltros);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A9', 'Estado Solicitud:');
            $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('C9', $arrayParametros['estadoSol']);
            $objPHPExcel->getActiveSheet()->getStyle('C9')->applyFromArray($arrayStyleFiltros);

            $objImage = imagecreatefromjpeg($strPath);

            //Si obtiene la imagen la crea en la celda A1
            if($objImage)
            {
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('TELCOS++');
                $objDrawing->setDescription('TELCOS++');
                $objDrawing->setImageResource($objImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(200);
                $objDrawing->setWidth(80);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            //Se carga la Data en las celdas
            $columnBegin ++;

            foreach($resultado as $data)
            {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':Z' . $columnBegin)->applyFromArray($arrayStyleBodyTable);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':Z' . $columnBegin)
                    ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                
                $objPHPExcel->getActiveSheet()->getRowDimension($columnBegin)->setRowHeight(20);
                
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, $data['nombreElemento']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $columnBegin, $data['solicitante']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $columnBegin, $data['estadoSolicitud']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $columnBegin, $data['nombreProvincia']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $columnBegin, $data['nombreCanton']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $columnBegin, $data['direccionUbicacion']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $columnBegin, $data['longitudUbicacion']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $columnBegin, $data['latitudUbicacion']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $columnBegin, $data['clase']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $columnBegin, $data['tipoMedio']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $columnBegin, $data['nombreClaseMedidor']);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $columnBegin, $data['nombreTipoMedidor']);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $columnBegin, $data['numeroMedidor']);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $columnBegin, $data['descripcionRol']);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $columnBegin, $data['contacto']);
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $columnBegin, $data['tipoIdentificacion']);
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $columnBegin, $data['identificacion']);
                $objPHPExcel->getActiveSheet()->setCellValue('R' . $columnBegin, $data['valor']);
                $objPHPExcel->getActiveSheet()->setCellValue('S' . $columnBegin, $data['anticipo']);
                $objPHPExcel->getActiveSheet()->setCellValue('T' . $columnBegin, $data['inicio']);
                $objPHPExcel->getActiveSheet()->setCellValue('U' . $columnBegin, $data['duracion']);
                $objPHPExcel->getActiveSheet()->setCellValue('V' . $columnBegin, $data['fin']);
                $objPHPExcel->getActiveSheet()->setCellValue('W' . $columnBegin, $data['tipoCuenta']);
                $objPHPExcel->getActiveSheet()->setCellValue('X' . $columnBegin, $serviceCrypt->descencriptar($data['numeroCuenta']));
                $objPHPExcel->getActiveSheet()->setCellValue('Y' . $columnBegin, $data['banco']);
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $columnBegin, $data['formaPago']);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);

                $columnBegin++;
            }

            $columnBegin++;
            
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Reporte_NODOS.xls');
            
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        catch(\Exception $ex)
        {
            error_log($ex);
            exit;
        }
    }
    
      /**
     * @Secure(roles="ROLE_273-3")
     * 
     * Documentación para el método 'ingresoNuevoElementoAction'.
     *
     * Metodo utilizado para crear los nuevos elementos que van en el nodo seleccionado
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 28-06-2019
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.0 01-03-2023  -   Se agrega el parámetro strClase que permite identificar la clase de los elementos pertenecientes aa un Nodo.
     */
    public function ingresoNuevoElementoAction()
    {
        $objRespuesta           = new JsonResponse();
                
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $emInfraestructura      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $arrayParametros        = $objRequest->request->get('telconet_schemabundle_infoelementoracktype');
        $strTipoElemento        = $objRequest->get('tipoElemento');
        $strNombreElemento      = $objRequest->get('nombreElemento');
        $strSerieElemento       = $objRequest->get('serieElemento');
        $strDescripcionElemento = $objRequest->get('descripcionElemento');
        $intMarcaElemento       = $objRequest->get('marcaElemento');
        $intModeloElemento      = $objRequest->get('modeloElemento');
        $intIdElemento          = $objRequest->get('idElemento');
        $intIdResponsable       = $objRequest->get('idResponsable');
        $strClase               = $objRequest->get('claseElemento');
        $serviceUtil            = $this->get('schema.Util');
        
        $arrayRolesPermitidos = array();
 	                                    
        if(true === $this->get('security.context')->isGranted('ROLE_154-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_154-6';
        }
        
        $emInfraestructura->getConnection()->beginTransaction();
            
        try
        {
            $arrayParametros                           = array();
            $arrayParametros['objRequest']             = $objRequest;
            $arrayParametros['emInfraestructura']      = $emInfraestructura;
            $arrayParametros['strTipoElemento']        = $strTipoElemento;
            $arrayParametros['strNombreElemento']      = strtoupper($strNombreElemento);
            $arrayParametros['strSerieElemento']       = $strSerieElemento;
            $arrayParametros['strDescripcionElemento'] = strtoupper($strDescripcionElemento);
            $arrayParametros['intMarcaElementoId']     = $intMarcaElemento;
            $arrayParametros['intModeloElementoId']    = $intModeloElemento;
            $arrayParametros['intNodoElementoId']      = $intIdElemento;  
            $arrayParametros['strClase']               = $strClase;           
                
            //Creacion de nuevos elementos
            $arrayIngresarElemento  = $this->ingresarElementoNodo($arrayParametros);
            if($arrayIngresarElemento["status"] === "ERROR")
            {
                $objRespuesta->setContent($arrayIngresarElemento['mensaje']);
                if ($emInfraestructura->getConnection()->isTransactionActive())
                {
                    $emInfraestructura->rollback();
                }
                
                $emInfraestructura->close();
                return $objRespuesta;
            }
                           
            $objRespuesta->setContent("OK");
        }
        catch (\Exception $objEx)
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
                
            $emInfraestructura->close();
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->ingresoNuevoElementoAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al Ingresar Elemento. Notificar a Sistemas'));
            $objRespuesta->setContent($objResultado);
        }
        return $objRespuesta;
    }
    
     /**
     * Metodo encargado para crear los nuevos elementos de un nodo enviado como parametros
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 27-06-2019
     * 
     * @author Angel Cudco <acudco@telconet.ec> 
     * @version 1.1 2023-02-17 - Se agrega la clse (PRIMARIA -SECUNDARIA) de los elementos (UPS- RECTIFICADOR - TRANSFORMADOR - AIRE ACONDICIONADO)
     *                           en el arrayParametros.
     *                         - Se registra la trazabilidad del elemento.
     *                         - Se registra en la tabla ARAF_CONTROL_CUSTODIO   
     *
     * @param Array $arrayParametros [
     *                                  objRequest
     *                                  emInfraestructura
     *                                  strTipoRack
     *                                  strNombreElemento
     *                                  intModeloElementoId
     *                                  strDescripcionElemento
     *                                  strNombreFila
     *                                  intNodoElementoId,
     *                                  srtClaseElemento
     *                               ]
     * @return Array $arrayResultado[
     *                                  status              => estado del proceso
     *                                  mensaje             => mensaje de error
     *                                  $objElementoRack    => elemento rack
     *                              ]
     */
    private function ingresarElementoNodo($arrayParametros)
    {
        $objRequest        = $arrayParametros['objRequest'];
        $objSession        = $objRequest->getSession();
        $strUserSession    = $objSession->get('user');
        $strIpCreacion     = $objRequest->getClientIp();
        $strTipoElemento   = $arrayParametros['strTipoElemento'];
        $emInfraestructura = $arrayParametros['emInfraestructura'];
        $strSerieElemento  = $arrayParametros['strSerieElemento'];
        $serviceUtil       = $this->get('schema.Util');
        $strMensaje        = "";
        $strMenEle         = "";

        $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
        $emNaf                      = $this->getDoctrine()->getManager('telconet_naf');
        $emComercial                = $this->getDoctrine()->getManager("telconet");   
        
        $strResponsableTrazabilidad ="";
        $intIdNodoHijo="";

        $intIdResponsableNodo="";

        try
        {            
            //verificar que el nombre del elemento no se repita
            $objElementoRepetido = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->findOneBy(array( "nombreElemento" => $arrayParametros['strNombreElemento'],
                                                                        "estado" => "Activo"));

            if(is_object($objElementoRepetido))
            {
                $strMenEle = "ERROR";
                throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');                              
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
            $objElementoRack->setSerieFisica($arrayParametros['strSerieElemento']);
            $emInfraestructura->persist($objElementoRack);
            $emInfraestructura->flush();

            //buscar el interface Modelo
            $objInterfaceModelo = $emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                    ->findBy(array("modeloElementoId" => $arrayParametros['intModeloElementoId']));

            //se busca modelos de Unidades de Rack para poder crear las Unidades de Rack si el elemento es RACK
            if($strTipoElemento == 'RACK')
            {
                $objModeloElementoUDRack  = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                          ->findOneBy(array("nombreModeloElemento"=>"UDRACK"));
            }
            
            //Si es RACK se crean las unidades
            if($strTipoElemento == 'RACK')
            {
                foreach($objInterfaceModelo as $objIm)
                {
                    $intCantidadInterfaces = $objIm->getCantidadInterface();

                    for($intI = 1; $intI <= $intCantidadInterfaces; $intI++)
                    {
                        $objElemento = new InfoElemento();

                        $strNombreElemento = $intI;

                        $objElemento->setNombreElemento($strNombreElemento);
                        $objElemento->setDescripcionElemento("Unidad de rack");
                        $objElemento->setModeloElementoId($objModeloElementoUDRack);
                        $objElemento->setUsrResponsable($objSession->get('user')); //cambio en el responsable
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
            
            $arrayElementosClase = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedidor')
                                                     ->getElementosConClase(array("strParametro"=> "ELEMENTOS CON CLASE",
                                                                                  "strEstado"   => "Activo"));  

            $intBand=0;                                                                                  
            foreach($arrayElementosClase as $elemento)
            {
                if($elemento['tipoElemento']==$strTipoElemento)
                {
                    $intBand=1;
                }
            }
            
            if($intBand!=0)                                   
            {
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objElementoRack->getId());
                $objDetalleElemento->setDetalleNombre("CLASE");
                $objDetalleElemento->setDetalleValor($arrayParametros['strClase']);
                $objDetalleElemento->setDetalleDescripcion("INSTALACION DE UN ".$strTipoElemento);
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
            $objRelacionElemento->setObservacion("nodo contiene nuevo elemento");
            $objRelacionElemento->setEstado("Activo");
            $objRelacionElemento->setUsrCreacion($objSession->get('user'));
            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objRelacionElemento);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoRack);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Se ingreso un nuevo Elemento");
            $objHistorialElemento->setUsrCreacion($objSession->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objHistorialElemento);

            //tomar datos nodo
            $objNodoEmpresaElementoUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                 ->findOneBy(array("elementoId" => $arrayParametros['intNodoElementoId']));
            $objNodoUbicacion                = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                 ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());

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
            $emInfraestructura->commit();
            $strStatus = "OK";

            /*
            ** inicio del registro de la trazabilidad del elemento
            */        
            //tomar nombre nodo
            $objDatosNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                              ->findOneBy(array("id" => $arrayParametros['intNodoElementoId']));
                                                                                                                                   
            $objContactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")
                                                ->findOneByNodoId($arrayParametros['intNodoElementoId']);

            //validación que exista el contacto del nodo
            if (empty($objContactoNodo))
            {                           
                $objNodoPadre = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                  ->findOneBy(array('elementoIdB'   => $arrayParametros['intNodoElementoId'],
                                                                    'estado'        => 'Activo' ));
    
                //contacto del nodo, se descarta el contenedor
                $objContactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")
                                                      ->findOneByNodoId($objNodoPadre->getElementoIdA());
 
                //datos del nodo ppadre
                $objDatosNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                  ->findOneBy(array("id" => $objNodoPadre->getElementoIdA()));

            } 
            
            if (is_object($objContactoNodo) && !empty($objContactoNodo))
            {

                //buscamos el responsable del NODO
                $objPersona = $emInfraestructura->getRepository("schemaBundle:InfoPersona")
                                                                ->findOneById($objContactoNodo->getPersonaId()); 
                                                                                                
                if(is_object($objPersona) && !empty($objPersona))
                {                    
                    if($objPersona->getRazonSocial() != "")
                    {
                        $strResponsableTrazabilidad = $objPersona->getRazonSocial();
                    }
                    else if($objPersona->getNombres() != "" && $objPersona->getApellidos() != "")
                    {
                        $strResponsableTrazabilidad = $objPersona->getApellidos() . " " . $objPersona->getNombres();
                    }
                    else if($objPersona->getRepresentanteLegal() != "")
                    {
                        $strResponsableTrazabilidad = $objPersona->getRepresentanteLegal();
                    }
                    else
                    {
                        $strResponsableTrazabilidad = "";
                    }
                }
                else
                {
                    throw new \Exception('Error : No se pudo encontrar el Responsable del Nodo para Registrar la Trazabilidad.');
                }
            }
            else
            {
                throw new \Exception('Error : No se pudo encontrar el contacto del Nodo.');
            }

            //array con los datos para el registro de trazabilidad
            $arrayParametrosAuditoria = array();            
            $arrayParametrosAuditoria["strNumeroSerie"]  = $arrayParametros['strSerieElemento'];
            $arrayParametrosAuditoria["strLogin"]        = $objDatosNodo->getNombreElemento();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo'; //estado de elemento recuperado en telcos
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado'; //estado de naf
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo'; //estado entregado por usuario
            $arrayParametrosAuditoria["strUbicacion"]    = 'Nodo'; //default
            $arrayParametrosAuditoria["strUsrCreacion"]  = $objSession->get('user');
            $arrayParametrosAuditoria["strCodEmpresa"]   = $objSession->get('idEmpresa');
            $arrayParametrosAuditoria["strTransaccion"]  = "Instalacion Nodo";
            $arrayParametrosAuditoria["intOficinaId"]    = 0;            
            $arrayParametrosAuditoria["strObservacion"]  = "Instalacion Nodo";           
            $arrayParametrosAuditoria["strResponsable"]  = $strResponsableTrazabilidad;
            $arrayParametrosAuditoria["boolPerteneceElementoNodo"]  = true;  
                        
            //Se ingresa el tracking del elemento
            $serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            /*
            ** fin del registro de la trazabilidad del elemento
            */     


            /*
            ** inicio de la actualización en el NAF y registro en control custodio
            */    

            $objInArticuloInstalacion = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                            ->findOneBy(array("numeroSerie" =>strtoupper($arrayParametros["strSerieElemento"]),
                                              "estado"=>'PI',
                                              "modelo"=>$objModeloElemento->getNombreModeloElemento()));
                        
            if (is_object($objInArticuloInstalacion) && !empty($objInArticuloInstalacion))
            {               
                //actualización en NAF
                $objInArticuloInstalacion->setEstado('IN');
                $objInArticuloInstalacion->setSaldo(0);                
                $emNaf->persist($objInArticuloInstalacion);
                $emNaf->flush();
                                              
            }
            else
            {
                throw new \Exception('Error : No se pudo actualizar el ArticulosInstalacion.');
            }

            //busqueda para control custodio                
            $objEncargado = $emComercial->getRepository("schemaBundle:InfoPersona")
                                        ->findOneBy(array("identificacionCliente"=>$objInArticuloInstalacion->getCedula()));

            if (is_object($objEncargado) && !empty($objEncargado))
            {
                $arrayEquipos     = array();
                $arrayEquipos[]   = array('strNumeroSerie' => $arrayParametros['strSerieElemento'],
                        // 'intIdControl'    => $intIdControl,
                        'intCantidadEnt'  => 1,
                        'intCantidadRec'  => 1,
                        'strTipoArticulo' => 'Equipos');

                $arrayCargaDescarga = array();
                $arrayCargaDescarga['boolRegistrarTraking']     =  false;                        
                $arrayCargaDescarga['intIdElementoNodo']        =  $arrayParametros['intNodoElementoId'];
                $arrayCargaDescarga['strTipoRecibe']            =  'Nodo';               
                $arrayCargaDescarga['intIdEmpresa']             =  $objSession->get('idEmpresa');
                $arrayCargaDescarga['strTipoActividad']         =  'InstalacionNodo';
                $arrayCargaDescarga['strTipoTransaccion']       =  'InstalacionNodo';
                $arrayCargaDescarga['strObservacion']           =  'Instalacion en el Nodo';
                $arrayCargaDescarga['arrayEquipos']             =  $arrayEquipos;
                $arrayCargaDescarga['strEstadoSolicitud']       =  'Asignado';
                $arrayCargaDescarga['strUsuario']               =  $objSession->get('user');
                $arrayCargaDescarga['strIpUsuario']             =  $objRequest->getClientIp();    
                
                //registro en control custodio                                       
                $serviceInfoElemento->cargaDescargaActivos($arrayCargaDescarga);

            }
            else
            {
                throw new \Exception('Error : No se pudo encontrar al empleado encargado del elemento.');
            }  

            /*
            ** fin de la actualización en el NAF y registro en control custodio
            */ 
        }
        catch(\Exception $objEx)
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->ingresarElementoNodo',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $strStatus          = "ERROR";
            $strMensaje         = $objEx->getMessage();
            $objElementoRack    = null;
        }
        $arrayResultado = array("status" => $strStatus, 
                                "mensaje" => $strMensaje, 
                                "objElementoRack" => $objElementoRack,
                                "statusEleme" => $strMenEle);
        return $arrayResultado;
    }
    
    /**
      * ajaxDeleteElementoAction
      *
      * Método que elimina el elemento del nodo
      *
      * @return resultado
      *
      * @author Antonio Ayala  <afayala@telconet.ec>
      * @version 1.0 28-06-2019 
      *
      * @Secure(roles="ROLE_154-2198")
      */  
    public function ajaxDeleteElementoAction()
    {
        $objRespuesta       = new JsonResponse();
                
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $serviceUtil        = $this->get('schema.Util');
        $intContador        = 0;
        $intValida          = 0;

        $intIdElemento = $objRequest->get('id');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
                
        $em->getConnection()->beginTransaction();    
        
        try
        {
            $objRelacionElemento = $this->getDoctrine()
                                        ->getManager("telconet_infraestructura")
                                        ->getRepository('schemaBundle:InfoElemento')
                                        ->getJsonElementosPorContenedor($intIdElemento, "", $objSession->get('idEmpresa'));
            
            $objContador = json_decode($objRelacionElemento);
            
            $intContador = $objContador->{'total'};
            
            if($intContador > 0)
            {
                $objResultado = json_encode(array('success' => false, 'mensaje' => 'No se puede eliminar. Contiene elementos activos'));
                $objRespuesta->setContent($objResultado);
                return $objRespuesta;
            }
            
            $objElemento = $em->find('schemaBundle:InfoElemento', $intIdElemento);
            
            //Obtener modelo del elemento
            
            $intModeloElementoId =  $objElemento->getModeloElementoId();
            $objModeloElemento   =  $this->getDoctrine()
                                          ->getManager("telconet_infraestructura")
                                          ->getRepository('schemaBundle:AdmiModeloElemento')->find($intModeloElementoId);
            
            //Obtener nombre del tipo del elemento
            $intTipoElementoId      = $objModeloElemento->getTipoElementoId();
            $objTipoElemento        =  $this->getDoctrine()
                                            ->getManager("telconet_infraestructura")
                                            ->getRepository('schemaBundle:AdmiTipoElemento')->find($intTipoElementoId);
            $strNombreTipElemento   = $objTipoElemento->getNombreTipoElemento(); 
            
            
            //Se consulta si el elemento que se va a eliminar se encuentra dentro del parámetro
            $objDetalle = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:AdmiTipoMedidor')
                                ->getMedidoresElectricos("ELEMENTOS NODOS","Activo","");
                
            if($objDetalle)
            {
                foreach($objDetalle as $objData)
                {
                    $strValor = $objData->getValor1();
                    if ($strValor === $strNombreTipElemento)
                    {
                        $intValida = 1;
                    }
                }
            }
            
            if ($intValida == 1)
            {
                $objElemento->setUsrCreacion($objRequest->getSession()->get('user'));
                $objElemento->setFeCreacion(new \DateTime('now'));
                $objElemento->setIpCreacion($objRequest->getClientIp());
                $objElemento->setEstado("Eliminado");
                $em->persist($objElemento);
                
            }
                       
            //relacion elemento
            $objRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array("elementoIdB" => $objElemento));
            $objRelacionElemento[0]->setEstado("Eliminado");
            $objRelacionElemento[0]->setUsrCreacion($objRequest->getSession()->get('user'));
            $objRelacionElemento[0]->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento[0]->setIpCreacion($objRequest->getClientIp());
            $em->persist($objRelacionElemento[0]);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setObservacion("Se elimino el Elemento");
            $objHistorialElemento->setUsrCreacion($objRequest->getSession()->get('user'));
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($objRequest->getClientIp());
            $em->persist($objHistorialElemento);
            
            $strMensaje = "Se Eliminó el elemento seleccionado";

            $em->flush();
            $em->getConnection()->commit();
            
            $objResultado = json_encode(array('success'=>true,'mensaje'=>$strMensaje));
            $objRespuesta->setContent($objResultado);
        }
        catch(\Exception $objEx)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->ajaxDeleteElementoAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al eliminar Elemento. Notificar con Sistemas'));
            $objRespuesta->setContent($objResultado);
        }
        return $objRespuesta;
    }

     /**
     * Documentación para el método 'ingresoMasivoElementoAction'.
     *
     * Metodo encargado de procesar el archivo de elementos y los
     * coloca en el directorio de destino para luego tomar su información básica y guardarlos en la base
     *
     * @return Response
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 02-07-2019
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.1 02-03-2023 - Se agrega en el CSV la clase de los elementos, cuando no tienen clase de coloca N/A o "".
     *                         - Se incluye el registro de trazabilidad y el registro en ARAF_CONTROL_CUSTODIO
     */
    public function ingresoMasivoElementoAction()
    {
        $objRequest            = $this->get('request');
        $strServerRoot         = $this->container->getParameter('path_telcos');
        $objSession            = $objRequest->getSession();
        $strUserSession        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $objFile               = $objRequest->files;
        $objArchivo            = $objFile->get('archivoElemento');
        $strNombreArchivo      = $objArchivo->getClientOriginalName();
        $strTamanioArchivo     = $objArchivo->getClientSize();
        $strUrlDestino         = $strServerRoot.'telcos/web/public/uploads/documentos';
        $strUrlFileElementos   = $strUrlDestino.'/'.$strNombreArchivo; 
        $strUsrSesion          = $objSession->get('user');
        $intIdEmpresa          = $objSession->get('idEmpresa');
        $objArchivoElementos   = $objFile   ->get('archivoElemento');
        $serviceUtil           = $this->get('schema.Util');        
        $serviceInfoServicioTecnico = $this->get('tecnico.InfoServicioTecnico');        
        
        $objRespuesta          = new JsonResponse();
                
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial           = $this->getDoctrine()->getManager("telconet");
        $serviceInfoElemento   = $this->get("tecnico.InfoElemento");
        $emNaf                 = $this->getDoctrine()->getManager('telconet_naf');        

        $arrayDatosNaf =array();
    
        if($strTamanioArchivo>2000000)
        {
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al cargar el Archivo, tamaño máximo permitido 2MB'));
            $objRespuesta->setContent($objResultado);
            return $objRespuesta;
        }
        
        $emInfraestructura->getConnection()->beginTransaction();
                
        try
        {
            if($objArchivoElementos)
            {
                if($objArchivoElementos->move($strUrlDestino, $strNombreArchivo))
                {                 
                    // Lectura del archivo de contactos
                    $objFileReadElementos     = fopen($strUrlFileElementos, "r");
                    $strFileElementosError    = "reporte_elementos_no_ingresados_" . date('Ymd') . "_" . date('His') . ".txt";
                    $strUrlFileElementosError = $strUrlDestino . '/' . $strFileElementosError;
                    $objFileElementosError    = fopen($strUrlFileElementosError, "w");
                    $boolEnviaNotificacion    = false;
                    $intContador              = 0;
                    $intContErrores           = 0;
                    $boolIngreso              = false;

                    while(( $arrayDatos = fgetcsv($objFileReadElementos, 10000, ",", "\\")) !== false)
                    {
                        if($arrayDatos)
                        {                            
                            if($intContador > 0)
                            {
                                // INFORMACION DE NUEVO ELEMENTO
                                $strNombreContenedor    = trim($arrayDatos[0]);
                                $strTipoElemento        = trim($arrayDatos[1]);
                                $strClase               = trim($arrayDatos[2]);
                                $strSerieElemento       = trim($arrayDatos[3]);                                
                                $strDescripcionElemento = "";
                                $strMarca               = "";
                                $strModelo              = "";
                                $strMensaje             = "";
                                // VALIDAR NODO/CONTENEDOR

                                $arrayDatosNodoContenedor  = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                            ->getDatosContenedor(array('strNombreElemento' => $strNombreContenedor));

                                if (empty($arrayDatosNodoContenedor))
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nNo Existe el Nodo/contenedor con\n");
                                    fwrite($objFileElementosError, " - Nombre: " . $strNombreContenedor . "\n");
                                    fwrite($objFileElementosError, "Por favor revíselo.\n");
                                    continue;
                                }
                                else
                                {
                                    $intIdElemento     = $arrayDatosNodoContenedor[0]['intIdElemento'];
                                    $strBandContenedor = $arrayDatosNodoContenedor[0]['strTipoContenedor'];
                                }
                               
                                // VALIDAR TIPO DE ELEMENTO
                                $arrayTipos = $emComercial->getRepository('schemaBundle:AdmiTipoMedidor')
                                                              ->getElementosConClase(array('strParametro' => 'ELEMENTOS NODOS',
                                                                                           'strEstado'    => 'Activo'));

                                $intBand=0;                                                                                  
                                foreach($arrayTipos as $tipo)
                                {
                                    if($tipo['tipoElemento']==strtoupper($strTipoElemento))
                                    {
                                        $intBand=1;
                                    }
                                }

                                if($intBand==0)
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nNo existe el tipo de elemento\n");
                                    fwrite($objFileElementosError, " - Tipo: " . strtoupper($strTipoElemento) . "\n");
                                    fwrite($objFileElementosError, "Por favor revíselo.\n");
                                    continue;
                                }

                                //validar serie y obtener datos
                                $arrayDatosNaf = $emComercial->getRepository('schemaBundle:InArticulosInstalacion')
                                                                    ->validarSerieNaf(array("strSerie"  => $strSerieElemento,
                                                                                            "strEstado" => 'PI',
                                                                                            "strTipo"   => $strTipoElemento));
                                                                    
                                if(isset($arrayDatosNaf) && !empty($arrayDatosNaf))
                                {
                                    $strModelo               = $arrayDatosNaf[0]['modeloElemento'];
                                    $strMarca                = $arrayDatosNaf[0]['marcaElemento'];                
                                    $strDescripcionElemento  = $arrayDatosNaf[0]['descripcion'];
                                }

                                else
                                {
                                    
                                    $objInArticuloInstalacion = $emComercial->getRepository('schemaBundle:InArticulosInstalacion')
                                                                                ->findBy(array("numeroSerie" =>strtoupper($strSerieElemento)));
                                                                        
                                    if(isset($objInArticuloInstalacion) && !empty($objInArticuloInstalacion))
                                    {
                                        foreach ($objInArticuloInstalacion as $articulo):
                                            $strEstadoNaf = $articulo->getEstado();
                                            if($strEstadoNaf == "RE")
                                            {
                                                $strMensaje = $strMensaje. "Presenta el estado de Retirado. ";
                                            }
                                            else if($strEstadoNaf == "IN")
                                            {
                                                $strMensaje = $strMensaje. "Ya se encuentra Instalado. ";
                                            }
                                            else if($strEstadoNaf == "PR")
                                            {
                                                $strMensaje = $strMensaje. "Tiene una Solicitud de retiro. ";
                                            }
                                            else if($strEstadoNaf == "PI")
                                            {
                                                $strMensaje = $strMensaje. "Presenta un modelo y/o marca no regularizado(s). ";
                                            }
                                            else
                                            {
                                                $strMensaje = "Presenta un estado Desconocido. ";
                                            }
                                        endforeach;
                                    }
                                    else
                                    {
                                        $strMensaje = "El equipo no se encuentra registrado en el NAF.";
                                    }

                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nEl elemento con: \n");
                                    fwrite($objFileElementosError, " - Serie: " . $strSerieElemento . "\n");
                                    fwrite($objFileElementosError, "".$strMensaje."\n");
                                    continue;
                                }
                                                                                                 
                                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->findOneBy(array('nombreModeloElemento' => $strModelo,
                                                                      'estado'               => 'Activo'));
                                if (!$objModeloElemento)
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nEl Modelo encontrado en NAF de la serie: ".$strSerieElemento."\n");
                                    fwrite($objFileElementosError, " - Modelo: " . $strModelo . "\n");
                                    fwrite($objFileElementosError, "No existe en TELCOS, Por favor regularizarlo.\n");
                                    continue;
                                }
                                else
                                {
                                    $intModeloElemento  = $objModeloElemento->getId();
                                }
                                                                
                                $objMarcaElemento = $emInfraestructura->getRepository('schemaBundle:AdmiMarcaElemento')
                                                    ->findOneBy(array('nombreMarcaElemento' => $strMarca,
                                                                      'estado'              => 'Activo'));
                                if (!$objMarcaElemento)
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nLa marca encontrado en NAF de la serie: ".$strSerieElemento."\n");
                                    fwrite($objFileElementosError, "\n Modelo: ".$strModelo."\n");
                                    fwrite($objFileElementosError, " - Nombre: " . $strMarca . "\n");
                                    fwrite($objFileElementosError, "No existe en TELCOS, Por favor regularizarlo.\n");
                                    continue;
                                }
                                else
                                {
                                    $intMarcaElemento  = $objMarcaElemento->getId();
                                }

                                $arrayNombreElemento=$serviceInfoServicioTecnico
                                                        ->generarNombreAutomatico(array('strTipoElemento'=>$strTipoElemento,
                                                                                        'intIdNodoContenedor'=>$intIdElemento,
                                                                                        'strClaseElemento'=>strtoupper($strClase),
                                                                                        'strContenedor'=>$strBandContenedor));
                            
                                $strMensajeBand               = $arrayNombreElemento[0]['status'];
                                $strNombreElemento            = $arrayNombreElemento[0]['mensaje'];  

                                if ($strMensajeBand!='OK')
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\nNo se pudo egnerar el nombre para el  elemento con serie: "
                                                                    .$strSerieElemento."\n");
                                    fwrite($objFileElementosError, "Por favor revisarlo.\n");                       
                                    continue;
                                }

                                $arrayParametros                           = array();
                                $arrayParametros['objRequest']             = $objRequest;
                                $arrayParametros['emInfraestructura']      = $emInfraestructura;
                                $arrayParametros['strTipoElemento']        = strtoupper($strTipoElemento);
                                $arrayParametros['strNombreElemento']      = $strNombreElemento;
                                $arrayParametros['strSerieElemento']       = $strSerieElemento;
                                $arrayParametros['strDescripcionElemento'] = $strDescripcionElemento;
                                $arrayParametros['intMarcaElementoId']     = $intMarcaElemento;
                                $arrayParametros['intModeloElementoId']    = $intModeloElemento;
                                $arrayParametros['intNodoElementoId']      = $intIdElemento;
                                $arrayParametros['strClase']               = $strClase;
                            
                                $arrayIngresarElemento  = $this->ingresarElementoNodo($arrayParametros);
                                
                                {//inicio registro de trazabilidad por algún error    
                                                                        
                                    $objInfoElementoTraz = $emInfraestructura->getRepository('schemaBundle:InfoElementoTrazabilidad')
                                                                        ->findOneBy( array('numeroSerie' => $strSerieElemento, 
                                                                                           'estadoNaf' => 'PendienteInstalar') );
                                    if ($objInfoElementoTraz)
                                    {
                                        $arrayParametrosAuditoria = array();     
                                        $arrayParametrosAuditoria["boolPerteneceElementoNodo"]  = true;         
                                        $arrayParametrosAuditoria["strNumeroSerie"]             = $arrayParametros['strSerieElemento'];
                                        $arrayParametrosAuditoria["strLogin"]                   = $strNombreContenedor;
                                        $arrayParametrosAuditoria["strEstadoTelcos"]            = 'Activo'; //estado de elemento recuperado en telcos
                                        $arrayParametrosAuditoria["strEstadoNaf"]               = 'Instalado'; //estado de naf
                                        $arrayParametrosAuditoria["strEstadoActivo"]            = 'Activo'; //estado entregado por usuario
                                        $arrayParametrosAuditoria["strUbicacion"]               = 'Nodo'; //default
                                        $arrayParametrosAuditoria["strUsrCreacion"]             = $objSession->get('user');
                                        $arrayParametrosAuditoria["strCodEmpresa"]              = $objSession->get('idEmpresa');
                                        $arrayParametrosAuditoria["strTransaccion"]             = "Instalacion Nodo";
                                        $arrayParametrosAuditoria["intOficinaId"]               = 0;            
                                        $arrayParametrosAuditoria["strObservacion"]             = "Instalacion Nodo";
                                        $serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                                    }

                                    $objInArticuloInstalacion = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                                                    ->findOneBy(array("numeroSerie" =>strtoupper($arrayParametros["strSerieElemento"]),
                                                                    "estado"=>'PI',
                                                                    "modelo"=>$strModelo));
                                                
                                    if (is_object($objInArticuloInstalacion) && !empty($objInArticuloInstalacion))
                                    {               
                                        //actualización en NAF
                                        $objInArticuloInstalacion->setEstado('IN');
                                        $objInArticuloInstalacion->setSaldo(0);                                        
                                        $emNaf->persist($objInArticuloInstalacion);
                                        $emNaf->flush();                                                                    
                                    }

                                    $arrayEquipos     = array();
                                    $arrayEquipos[]   = array('strNumeroSerie' => $arrayParametros['strSerieElemento'],
                                            'intCantidadEnt'  => 1,
                                            'intCantidadRec'  => 1,
                                            'strTipoArticulo' => 'Equipos');

                                    $arrayCargaDescarga = array();
                                    $arrayCargaDescarga['boolRegistrarTraking']     =  false;                        
                                    $arrayCargaDescarga['intIdElementoNodo']        =  $arrayParametros['intNodoElementoId'];
                                    $arrayCargaDescarga['strTipoRecibe']            =  'Nodo';                                
                                    $arrayCargaDescarga['intIdEmpresa']             =  $objSession->get('idEmpresa');
                                    $arrayCargaDescarga['strTipoActividad']         =  'InstalacionNodo';
                                    $arrayCargaDescarga['strTipoTransaccion']       =  'InstalacionNodo';
                                    $arrayCargaDescarga['strObservacion']           =  'Instalacion en el Nodo';
                                    $arrayCargaDescarga['arrayEquipos']             =  $arrayEquipos;
                                    $arrayCargaDescarga['strEstadoSolicitud']       =  'Asignado';
                                    $arrayCargaDescarga['strUsuario']               =  $objSession->get('user');
                                    $arrayCargaDescarga['strIpUsuario']             =  $objRequest->getClientIp();
                                    $serviceInfoElemento->cargaDescargaActivos($arrayCargaDescarga);                                    
                                }//inicio registro de trazabilidad por algún error
                                
                                if($arrayIngresarElemento["statusEleme"] === "ERROR")
                                {
                                    $intContErrores++;
                                    $boolEnviaNotificacion      = true;
                                    fwrite($objFileElementosError, "\n".$arrayIngresarElemento['mensaje']."\n");
                                    fwrite($objFileElementosError, " - Nombre: " . $strNombreElemento . "\n");
                                    continue;
                                }
                                else
                                {
                                    $boolIngreso = true;
                                }                                
                            }
                            
                        }
                        else
                        {
                            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al cargar el Archivo, archivo vacío'));
                            $objRespuesta->setContent($objResultado);
                            return $objRespuesta;
                        }
                        $intContador++;
                    }

                    if($intContErrores>0 && $boolIngreso)
                    {
                        $objResultado = json_encode(array('success' => true, 
                                                          'mensaje' => 'Archivo subido exitosamente, se presentaron errores. 
                                                          Por favor revise el reporte de elementos no ingresados que fue enviado a su correo'));
                    }
                    elseif($intContErrores>0 && !$boolIngreso)
                    {
                        $objResultado = json_encode(array('success' => true, 
                                                          'mensaje' => 'Archivo subido exitosamente. 
                                                          No se registraron los elementos. 
                                                          Por favor revise el reporte de elementos no ingresados que fue enviado a su correo'));
                    }
                    elseif($intContErrores==0 && $boolIngreso)
                    {
                        $objResultado = json_encode(array('success' => true, 
                                                          'mensaje' => 'Archivo subido exitosamente. No se presentaron errores'));
                    }
                                               
                }
                else
                {
                    chmod($strUrlFileElementos, 777);
                    chmod($strUrlFileElementosError, 777);
                    unlink($strUrlFileElementos);
                    unlink($strUrlFileElementosError);
                    $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al cargar el Archivo'));

                    throw new \Exception('Error al cargar el Archivo, favor revisar.');
                }

                // ENVIO DE NOTIFICACION CON ARCHIVO ADJUNTO DE ELEMENTOS NO INGRESADOS
                if($boolEnviaNotificacion)
                {
                    $objEmpleadoSesion     = $emInfraestructura->getRepository('schemaBundle:InfoPersona')
                                        ->findOneBy(array('login' => $strUsrSesion));
                    if($objEmpleadoSesion)
                    {
                        $objFormaContacto   = $emInfraestructura->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                            'estado' => 'Activo'
                                                            )
                                                    );

                        $objAdmiFormaContactoEmp = $emInfraestructura->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->findOneBy(array('personaId'       => $objEmpleadoSesion->getId(),
                                                                'formaContactoId' => $objFormaContacto->getId(),
                                                                'estado'          => 'Activo'
                                                                )
                                                            );

                        $strEmailEmpleadoSesion = strtolower($objAdmiFormaContactoEmp->getValor());
                        $arrayDestinatarios[]   = $strEmailEmpleadoSesion;
                    }
                    else
                    {
                        $arrayDestinatarios[]   = 'notificaciones_telcos@telconet.ec';
                    }

                    $strAsunto          = "Reporte de Elementos no Ingresados";
                    $strCodigoPlantilla = "ELEMENT_NI";
                    $objEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
                    $objEnvioPlantilla->generarEnvioPlantilla($strAsunto, $arrayDestinatarios, $strCodigoPlantilla, null, $intIdEmpresa, '', '',
                                                                    $strUrlFileElementosError, false, 'notificaciones_telcos@telconet.ec');
                }
                
                fclose($objFileReadElementos);               
                fclose($objFileElementosError);

                chmod($strUrlFileElementos, 0777);
                chmod($strUrlFileElementosError, 0777);
                unlink($strUrlFileElementos);                
                $objRespuesta->setContent($objResultado);
                return $objRespuesta;
            }
            else
            {
                $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al cargar el Archivo'));
                throw new \Exception('Error al cargar el Archivo, favor revisar.');
            }
        } 
        catch (Exception $objEx)
        {
            $emInfraestructura->getConnection()->rollback();
            chmod($strUrlFileElementos, 777);
            chmod($strUrlFileElementosError, 777);
            unlink($strUrlFileElementos);
            unlink($strUrlFileElementosError);
            
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->ingresoMasivoElementoAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al cargar el Archivo. Notificar con Sistemas'));
        }                
    }
    
    /**
     * * ajaxCargarElementosContenedorAction
     *
     * Metodo que devuelve los resultados de todos los elementos contenidos en un NODO determinado
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 16-07-2019
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxCargarElementosContenedorAction()
    {
        $objRespuesta = new JsonResponse();

        $objPeticion = $this->get('request');
        
        $objSession            = $objPeticion->getSession();
        $strUserSession        = $objSession->get('user');
        $strIpCreacion         = $objPeticion->getClientIp();
        $serviceUtil           = $this->get('schema.Util');

        $intIdNodo = $objPeticion->get('idNodo');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->getJsonElementosContenedorNodo($intIdNodo,$strUserSession,$strIpCreacion,$serviceUtil);
        
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    /**
      * Metodo que se encarga de realizar mantenimiento a los Nodos de Tipo Radio(Torre),
      *  y a su vez crea la tarea respectiva al coordinador a cargo para el mantenimiento 
      * 
      * @author Adian Ortega <amortega@telconet.ec>
      * @version 1.0
      * @since 09-09-2019
      * @Secure(roles="ROLE_154-6817")
      * 
      */
    public function realizarMantenimientoAction()
    {
        $strRespuesta     = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion      = $this->get('request');
        
        $objSession       = $objPeticion->getSession();
        $strUserSession   = $objSession->get('user');
        $strIpCreacion    = $objPeticion->getClientIp();
        $strFecha         = date('d/m/Y');
        
        $intIdNodo        = $objPeticion->get('idNodo');
    
        $strEm            = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $strMensajEerror  = str_repeat(' ', 4000);
    
        $strSql           = "BEGIN INKG_MANTENIMIENTO_TORRE.P_ENVIO_NOTIFICACION(:Pn_IdNodo,:Pv_DetalleValor,:Pv_Usuario,:Pv_Ip,:Pv_Mensaje); END;";
        $strStmt          = $strEm->getConnection()->prepare($strSql);
        
        $strStmt->bindParam('Pn_IdNodo', $intIdNodo);
        $strStmt->bindParam('Pv_DetalleValor', $strFecha);
        $strStmt->bindParam('Pv_Usuario', $strUserSession);
        $strStmt->bindParam('Pv_Ip', $strIpCreacion);
        $strStmt->bindParam('Pv_Mensaje', $strMensajEerror);
        $strStmt->execute();
        
        if ($strMensajEerror == 'SUCCESS')
        {
            $strMensajeResponse = 'OK';            
        }
        else
        {
            $strMensajeResponse = $strMensajEerror;            
        }
        
        return $strRespuesta->setContent($strMensajeResponse);
    }

    /**
     * Función encargada de migrar los elementos de un nodo x a un nodo y.
     *
     * @Secure(roles="ROLE_154-8158")
     *
     * @author Germán valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 08-04-2021
     */
    public function migracionNodoAction()
    {
        $objResponse         = new JsonResponse();
        $emInfraestructura   = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $strIpUsuario        = $objRequest->getClientIp();
        $strUsuario          = $objSession->get('user');
        $intIdElementoActual = $objRequest->get('intIdElementoActual');
        $intIdElementoNuevo  = $objRequest->get('intIdElementoNuevo');
        $arrayElementos      = json_decode($objRequest->get('strJsonElementos'));
        $serviceUtil         = $this->get('schema.Util');
        $strMensaje          = 'Migración realizada con exito!';

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objInfoElementoActual = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($intIdElementoActual);

            if (!is_object($objInfoElementoActual))
            {
                throw new \Exception('Error : No se logro encontrar información del nodo a migrar.');
            }

            $objInfoElementoNuevo  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($intIdElementoNuevo);

            if (!is_object($objInfoElementoNuevo))
            {
                throw new \Exception('Error : No se logro encontrar la información del nodo nuevo!');
            }

            foreach ($arrayElementos as $intIdElemento)
            {
                $objInfoRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                        ->findOneBy(array('elementoIdA' => $intIdElementoActual,
                                          'elementoIdB' => $intIdElemento,
                                          'estado'      => 'Activo'));

                if (!is_object($objInfoRelacionElemento))
                {
                    throw new \Exception('Error : No se logro encontrar la relación de los elementos a migrar!');
                }

                $objInfoRelacionElemento->setEstado("Eliminado");
                $emInfraestructura->persist($objInfoRelacionElemento);
                $emInfraestructura->flush();

                $objInfoElementoMigrado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($intIdElemento);

                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objInfoElementoActual);
                $objHistorialElemento->setEstadoElemento($objInfoElementoActual->getEstado());
                $objHistorialElemento->setObservacion("Se elimina la relación con el elemento: ".
                        $objInfoElementoMigrado->getNombreElemento());
                $objHistorialElemento->setUsrCreacion($strUsuario);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($strIpUsuario);
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();

                //Nueva relación elemento.
                $objRelacionElementoNuevo = new InfoRelacionElemento();
                $objRelacionElementoNuevo->setElementoIdA($intIdElementoNuevo);
                $objRelacionElementoNuevo->setElementoIdB($intIdElemento);
                $objRelacionElementoNuevo->setTipoRelacion("CONTIENE");
                $objRelacionElementoNuevo->setObservacion("nodo contiene nuevo elemento");
                $objRelacionElementoNuevo->setEstado("Activo");
                $objRelacionElementoNuevo->setUsrCreacion($strUsuario);
                $objRelacionElementoNuevo->setFeCreacion(new \DateTime('now'));
                $objRelacionElementoNuevo->setIpCreacion($strIpUsuario);
                $emInfraestructura->persist($objRelacionElementoNuevo);
                $emInfraestructura->flush();

                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objInfoElementoNuevo);
                $objHistorialElemento->setEstadoElemento($objInfoElementoNuevo->getEstado());
                $objHistorialElemento->setObservacion("Se crea la relación con el elemento: ".
                        $objInfoElementoMigrado->getNombreElemento());
                $objHistorialElemento->setUsrCreacion($strUsuario);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($strIpUsuario);
                $emInfraestructura->persist($objHistorialElemento);
                $emInfraestructura->flush();
            }

            $emInfraestructura->getConnection()->commit();
            $arrayRespuesta = array("status"  => true, "mensaje" => $strMensaje);
        }
        catch (\Exception $objException)
        {
            $strMensaje = 'Error al migrar los elementos del nodo!!.';

            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMensaje = explode("Error : ", $objException->getMessage())[1];
            }

            $serviceUtil->insertError('InfoElementoNodoController',
                                      'migracionMigracionNodoAction',
                                       $objException->getMessage(),
                                       $strUsuario,
                                       $strIpUsuario);

            $arrayRespuesta = array("status"  => false,
                                    "mensaje" => $strMensaje);
        }
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     * Función que devuelve los motivos de acuerdo al tipo de solicitud.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 10-06-2021
     */
    public function motivosEquiposAction()
    {
        $objResponse      = new JsonResponse();
        $objRequest       = $this->get('request');
        $strTipoSolicitud = $objRequest->get('tipoSolicitud');
        $emGeneral        = $this->get('doctrine')->getManager('telconet_general');
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $arrayMotivos     = array();

        //Obtenemos el id de la relación sistema para obtener los motivos.
        $arrayAdmiParametro = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('GESTION_ELEMENTOS_NODO','TECNICO','','',$strTipoSolicitud,'','','');

        if (isset($arrayAdmiParametro['valor2']) && !empty($arrayAdmiParametro['valor2']))
        {
            $intIdRelacionSistema = $arrayAdmiParametro['valor2'];
            $strSolicitud         = $arrayAdmiParametro['valor3'];
            $arrayAdmiMotivo      = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($intIdRelacionSistema);
            $objAdmiTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                        ->findOneByDescripcionSolicitud($strSolicitud);

            foreach ($arrayAdmiMotivo as $objAdmiMotivo)
            {
                $arrayMotivos[] = array('idMotivo'        => $objAdmiMotivo->getId(),
                                        'descripcion'     => $objAdmiMotivo->getNombreMotivo(),
                                        'idTipoSolicitud' => $objAdmiTipoSolicitud->getId());
            }
        }

        $objResponse->setData($arrayMotivos);
        return $objResponse;
    }

    /**
     * Función encargada de crear las solicitudes respectivas de un cambio de elemento en un nodo
     * o retiro de elemento de un nodo.
     *
     * @Secure(roles="ROLE_154-8138")
     *
     * @author Germán valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 08-06-2021
     */
    public function crearSolicitudElementosNodoAction()
    {
        $objResponse              =  new JsonResponse();
        $emComercial              =  $this->getDoctrine()->getManager('telconet');
        $serviceUtil              =  $this->get('schema.Util');
        $serviceSoporte           =  $this->get('soporte.SoporteService');
        $objRequest               =  $this->getRequest();
        $objSession               =  $objRequest->getSession();
        $strIpUsuario             =  $objRequest->getClientIp();
        $strUsuario               =  $objSession->get('user');
        $strNombreUsuario         =  $objSession->get('empleado');
        $strIdDepartamentoUsuario =  $objSession->get('idDepartamento');
        $objDatosSolicitud        =  json_decode($objRequest->get('jsonDatosSolicitud'));
        $objDatosTarea            =  json_decode($objRequest->get('jsonDatosTarea'));
        $strMensaje               = 'Solicitud creada con éxito.';

        $intIdEmpresa = $objSession->get('idEmpresa');

        $emComercial->getConnection()->beginTransaction();
        $emNaf = $this->get('doctrine')->getManager('telconet_naf');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSoporte = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {

            
            $objNaf = $emNaf->getRepository('schemaBundle:InfoElemento');
            $objInfraestructura = $emInfraestructura->getRepository('schemaBundle:InfoElemento');

            // obtengo nombre de nodo
            $strNombreNodo = $objInfraestructura->getNombreNodo($objDatosSolicitud->idElementoNodo);

            // recorremos array elementos
            foreach ($objDatosSolicitud->arrayElementos as $objElemento)
            {

                $strTipoCustodio = '';


                // verificamos si serie existe en la control custodio
                $objNafExiste = $objNaf->getElementoSerieNaf($objElemento->serieElemento);

                // 1 - Si existe en Naf
                if($objNafExiste[0]['custodio_id'])
                {

                    // lleno datos para llamado a procedimiento
                    $arrayParametros['numeroSerie']         = $objElemento->serieElemento; 
                    $arrayParametros['caracteristicaId']    = null;
                    $arrayParametros['empresaId']           = $intIdEmpresa;
                    $arrayParametros['intidPersonaEntrega'] = $objNafExiste[0]['custodio_id'];
                    $arrayParametros['cantidadEnt']         = $objNafExiste[0]['cantidad'];
                    $arrayParametros['intidPersonaRecibe']  = $objDatosSolicitud->idElementoNodo;
                    $arrayParametros['cantidadRec']         = $objNafExiste[0]['cantidad'];
                    $arrayParametros['tipoTransaccion']     = 'Tarea';
                    $arrayParametros['transaccionId']       = 0;
                    $arrayParametros['tipoActividad']       = 'SoporteNodo';
                    $arrayParametros['tareaId']             = null;
                    $arrayParametros['login']               = $strNombreNodo;
                    $arrayParametros['loginEmpleado']       = $strUsuario;
                    $arrayParametros['idControl']           = $objNafExiste[0]['id_control'];
                    $arrayParametros['observacion']         = null;
                    $arrayParametros['tipoArticulo']        = 'Equipos';

                    $strTipoCustodio = $objNafExiste[0]['tipo_custodio'];
                    

                // Si no existe en Naf
                }else
                {
                    // 2 -Serie ingresada manualmente
                    if($objElemento->serieOrigen === 'manual')
                    {

                        $strObservacion = 'Registro generado manualmente por creacion de solicitud';

                    // 3 - Serie ingresada automaticamente
                    }
                    else if($objElemento->serieOrigen === 'automatica')
                    {
    
                        $strObservacion = 'Registro generado automaticamente por creacion de solicitud';
                        
                    }

                    // lleno datos para llamado a procedimiento
                    $arrayParametros['numeroSerie']         = $objElemento->serieElemento;
                    $arrayParametros['caracteristicaId']    = null;
                    $arrayParametros['empresaId']           = $intIdEmpresa;
                    $arrayParametros['intidPersonaEntrega'] = null;
                    $arrayParametros['cantidadEnt']         = null;
                    $arrayParametros['intidPersonaRecibe']  = $objDatosSolicitud->idElementoNodo;
                    $arrayParametros['cantidadRec']         = 1;
                    $arrayParametros['tipoTransaccion']     = 'Tarea';
                    $arrayParametros['transaccionId']       = 0;
                    $arrayParametros['tipoActividad']       = 'SoporteNodo';
                    $arrayParametros['tareaId']             = null;
                    $arrayParametros['login']               = $strNombreNodo;
                    $arrayParametros['loginEmpleado']       = $strUsuario;
                    $arrayParametros['idControl']           = 0;
                    $arrayParametros['observacion']         = $strObservacion;
                    $arrayParametros['tipoArticulo']        = 'Equipos';

                } 
                
                
                // 1 - llamado a procedimiento
                $arrayParametros['strUser']             = $this->container->getParameter('user_naf');
                $arrayParametros['strPass']             = $this->container->getParameter('passwd_naf');
                $arrayParametros['objDb']               = $this->container->getParameter('database_dsn_naf');

                if($strTipoCustodio != 'Nodo')
                {
                    
                    $arrayRespuesta = $emSoporte->getRepository('schemaBundle:InfoDetalleMaterial')
                    ->putguardarRegistroNaf2($arrayParametros);

                    if(is_array($arrayRespuesta))
                    {
                    throw new \Exception($arrayRespuesta['message']);
                    }

                }
               


                // 2 - verificamos si serie existe en la info elemento
                $intInfoElementoExiste = $objInfraestructura->getElementoSerieInfoElemento($objElemento->serieElemento);

                // Si no existe actualizo serie en info elemento
                if($intInfoElementoExiste == 0)
                {           
                    $intInfoElementoExiste = $objInfraestructura->updateElementoSerieFisica($objElemento->idElemento, $objElemento->serieElemento);
                }



                // 3 - insertar caracteristica si no existe en naf y la serie se genero de manera automatica
                if($objNafExiste[0]['custodio_id'] == null && $objElemento->serieOrigen === 'automatica') 
                {
                    // se ingresa nueva caracteristica
                    $objInfoDetalleElemento = new InfoDetalleElemento();          
                    $objInfoDetalleElemento->setElementoId($objElemento->idElemento);
                    $objInfoDetalleElemento->setDetalleNombre("SERIE_AUTOMATICA");
                    $objInfoDetalleElemento->setDetalleValor($objElemento->serieElemento);
                    $objInfoDetalleElemento->setDetalleDescripcion("Serie generada automaticamente");
                    $objInfoDetalleElemento->setUsrCreacion($strUsuario);
                    $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleElemento->setIpCreacion($strIpUsuario);
                    $objInfoDetalleElemento->setEstado("Activo");
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }

            }
            




            $arrayDatosCaracteriticas = array();
            $arrayFechaEjecucion      = explode("T", $objDatosTarea->fechaEjecucion);
            $arrayHoraEjecucion       = explode("T", $objDatosTarea->horaEjecucion);
            $strFechaEjecucion        = date('Y-m-d H:i', strtotime($arrayFechaEjecucion[0].' '.$arrayHoraEjecucion[1]));
            $objFechaEjecucion        = new \DateTime($strFechaEjecucion);

            //Obtenemos el tipo de solicitud.
            $objAdmiTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                    ->find($objDatosSolicitud->idTipoSolicitud);

            if (!is_object($objAdmiTipoSolicitud))
            {
                throw new \Exception('Error : No se logro obtener el tipo de solicitud.');
            }

            //Obtenemos la característica para la solicitud.
            $strCaracteristica        = "ELEMENTO NODO";
            $objAdmiCaracteristicaSol =  $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                      "estado"                    => "Activo"));

            if (!is_object($objAdmiCaracteristicaSol))
            {
                throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
            }

            //Obtenemos la característica para la tarea.
            $strCaracteristica        = "SOLICITUD NODO";
            $objAdmiCaracteristicaTar =  $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                      "estado"                    => "Activo"));

            if (!is_object($objAdmiCaracteristicaTar))
            {
                throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
            }

            //Creación de la solicitud.
            foreach ($objDatosSolicitud->arrayElementos as $objElemento)
            {
                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setTipoSolicitudId($objAdmiTipoSolicitud);
                $objDetalleSolicitud->setMotivoId($objDatosSolicitud->idMotivo);
                $objDetalleSolicitud->setObservacion($objDatosSolicitud->observacion);
                $objDetalleSolicitud->setElementoId($objDatosSolicitud->idElementoNodo);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUsuario);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setFeEjecucion($objFechaEjecucion);
                $emComercial->persist($objDetalleSolicitud);
                $emComercial->flush();

                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objSolicitudHistorial->setEstado("AsignadoTarea");
                $objSolicitudHistorial->setObservacion("Se crea Solicitud elementos en Nodo");
                $objSolicitudHistorial->setMotivoId($objDatosSolicitud->idMotivo);
                $objSolicitudHistorial->setUsrCreacion($strUsuario);
                $objSolicitudHistorial->setIpCreacion($strIpUsuario);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();


                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristicaSol);
                $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $objInfoDetalleSolCaract->setValor($objElemento->idElemento);
                $objInfoDetalleSolCaract->setEstado("AsignadoTarea");
                $objInfoDetalleSolCaract->setUsrCreacion($strUsuario);
                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objInfoDetalleSolCaract);
                $emComercial->flush();


                $arrayDatosCaracteriticas[] = array('intCaracteristicaId' => $objAdmiCaracteristicaTar->getId(),
                                                    'strValor'            => $objDetalleSolicitud->getId());
            }

            //Parámetros para crear la tarea.
            $arrayParametrosTarea = array();
            $arrayParametrosTarea['strTipoTarea']             = 'T';
            $arrayParametrosTarea['intIdEmpresa']             = $objDatosTarea->idEmpresa;
            $arrayParametrosTarea['strPrefijoEmpresa']        = $objDatosTarea->prefijoEmpresa;
            $arrayParametrosTarea['strNombreProceso']         = $objDatosTarea->nombreProceso;
            $arrayParametrosTarea['strNombreTarea']           = $objDatosTarea->nombreTarea;
            $arrayParametrosTarea['strUserCreacion']          = $strUsuario;
            $arrayParametrosTarea['strUsuarioAsigna']         = $strNombreUsuario;
            $arrayParametrosTarea['strIpCreacion']            = $strIpUsuario;
            $arrayParametrosTarea['strTipoAsignacion']        = $objDatosTarea->tipoAsignado;
            $arrayParametrosTarea['strFechaHoraSolicitada']   = $strFechaEjecucion;
            $arrayParametrosTarea['intIdDepartamentoOrigen']  = $strIdDepartamentoUsuario;
            $arrayParametrosTarea['arrayDatosCaracteriticas'] = $arrayDatosCaracteriticas;
            $arrayParametrosTarea['arrayElementosAfectados']  = array($objDatosSolicitud->idElementoNodo);

            if ($objDatosTarea->tipoAsignado === 'empleado')
            {
                $arrayParametrosTarea['intIdPersonaEmpresaRol'] = $objDatosTarea->personaEmpresaRol;
            }

            if ($objDatosTarea->tipoAsignado === 'cuadrilla')
            {
                $arrayParametrosTarea['intIdCuadrilla'] = $objDatosTarea->asignadoId;
            }

            $strObservacion = $objDatosTarea->observacion !== null && $objDatosTarea->observacion !== '' ?
                              $objDatosTarea->observacion : $objDatosSolicitud->observacion;

            $arrayParametrosTarea['strMotivoTarea']      = $strObservacion;
            $arrayParametrosTarea['strObservacionTarea'] = $strObservacion;

            $arrayRespuestaTarea = $serviceSoporte->crearTareaCasoSoporte($arrayParametrosTarea);

            if ($arrayRespuestaTarea['mensaje'] === 'fail')
            {
                throw new \Exception($arrayRespuestaTarea['descripcion']);
            }

            $emComercial->getConnection()->commit();

            $strMensaje.= '<br/>Número de tarea: <b>'.$arrayRespuestaTarea['numeroTarea'].'</b>';
            $arrayRespuesta = array("status" => true,"mensaje" => $strMensaje);
        }
        catch (\Exception $objException)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

            $strMensaje = 'Error al crear la solicitud.';
            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMensaje = explode("Error : ", $objException->getMessage())[1];
            }

            $serviceUtil->insertError('InfoElementoNodoController',
                                      'crearSolicitudElementosNodoAction',
                                       $objException->getMessage(),
                                       $strUsuario,
                                       $strIpUsuario);

            $arrayRespuesta = array("status"  => false,"mensaje" => $strMensaje);
        }
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     * Función encargada de obtener los elementos que cuentan con una solicitud
     * de cambio de equipo.
     *
     * @author Germán valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 14-06-2021
     */
    public function obtenerSolicitudesElementosNodoAction()
    {

        try
        {

            $objResponse       = new JsonResponse();
            $objRequest        = $this->getRequest();
            $intIdElementoNodo = $objRequest->get('IdElementoNodo');
            $emComercial       = $this->getDoctrine()->getManager('telconet');
            $serviceUtil       = $this->get('schema.Util');

            $arrayParametros = array();
            $arrayParametros['intIdElementoNodo']  = $intIdElementoNodo;
            $arrayParametros['arrayTipoSolicitud'] = array('SOLICITUD CAMBIO EQUIPO');
            $arrayParametros['boolDatosElemento']  = true;
            $arrayParametros['boolObtenerTarea']   = true;

            $arraySolicitudes = $emComercial->getRepository('schemaBundle:InfoElemento')
                    ->obtenerSolicitudesElementoNodo($arrayParametros);
                    
            //Se registra log de los parametros de entrada y de salida
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => $intIdEmpresa,
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'InfoElementoRepository',
                'appMethod'        => 'obtenerSolicitudesElementoNodo',
                'descriptionError' => 'Resultado: ' . json_encode($arraySolicitudes, 128),
                'inParameters'     => json_encode($arrayParametros, 128),
                'status'           => 'Exitoso',
                'creationUser'     => $strUsuario));

            }
            catch (\Exception $objException)
            {
                $strMensaje = 'Error al cambiar el elemento.';
                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMensaje = explode("Error : ", $objException->getMessage())[1];
                }
    
                $serviceUtil->insertLog(array(
                    'enterpriseCode'   => $intIdEmpresa,
                    'logType'          => 1,
                    'logOrigin'        => 'TELCOS',
                    'application'      => 'TELCOS',
                    'appClass'         => 'InfoElementoRepository',
                    'appMethod'        => 'obtenerSolicitudesElementoNodo',
                    'descriptionError' => 'Resultado: ' . json_encode($arraySolicitudes, 128),
                    'inParameters'     => json_encode($arrayParametros, 128),
                    'status'           => 'Fallido',
                    'creationUser'     => $strUsuario));
    
                $arrayRespuesta = array("status" => false,"message" => $strMensaje);
            }    
    

        $objResponse->setData($arraySolicitudes);
        return $objResponse;
    }

    /**
     * Método encargado de cambiar un elemento que pertenece a un nodo.
     *
     * @Secure(roles="ROLE_154-8139")
     *
     * @author Germán valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 14-06-2021
     */
    public function cambiarElementoNodoAction()
    {
        $objResponse               = new JsonResponse();
        $objRequest                = $this->getRequest();
        $strUsuario                = $objRequest->getSession()->get('user');
        $intIdEmpresa              = $objRequest->getSession()->get('idEmpresa');
        $strIpUsuario              = $objRequest->getClientIp();
        $intIdSolicitud            = $objRequest->get('idSolicitud');
        $intNumeroTarea            = $objRequest->get('numeroTarea');
        $intIdElementoNodo         = $objRequest->get('idElementoNodo');
        $intIdElemento             = $objRequest->get('idElemento');
        $strNombreNuevoElemento    = $objRequest->get('nombreNuevoElemento');
        $strSerieNuevoElemento     = $objRequest->get('serieNuevoElemento');
        $strModeloNuevoElemento    = $objRequest->get('modeloNuevoElemento');
        $strMacNuevoElemento       = $objRequest->get('macNuevoElemento');
        $strTipoElemento           = $objRequest->get('tipoElemento');
        $serviceUtil               = $this->get('schema.Util');
        $serviceInfoCambioElemento = $this->get("tecnico.InfoCambioElemento");
        $emComercial               = $this->getDoctrine()->getManager('telconet');
        $emSoporte                 = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            //Obtenemos la característica de la tarea.
            $strCaracteristica     = "SOLICITUD NODO";
            $objAdmiCaracteristica =  $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,"estado" => "Activo"));

            if (!is_object($objAdmiCaracteristica))
            {
                throw new \Exception("Error : No se logro obtener la característica ($strCaracteristica).");
            }

            //Obtenemos la característica de la solicitud y tarea.
            $objInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                    ->findOneBy(array('caracteristicaId' => $objAdmiCaracteristica->getId(),
                                      'tareaId'          => $intNumeroTarea,
                                      'valor'            => $intIdSolicitud));

            if (!is_object($objInfoTareaCaracteristica))
            {
                throw new \Exception("Error : No se logro obtener el detalle de la tarea.");
            }

            //Obtenemos la ultima asignación de la tarea.
            $objInfoDetalleAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                    ->getUltimaAsignacion($objInfoTareaCaracteristica->getDetalleId());

            if (!is_object($objInfoDetalleAsignacion))
            {
                throw new \Exception("Error : No se logro obtener el asignado de la tarea.");
            }

            //Parametros para realizar el cambio de equipo.
            $arrayParametros = array();
            $arrayParametros['boolPerteneceElementoNodo'] =  true;
            $arrayParametros['intIdSolicitud']            =  $intIdSolicitud;
            $arrayParametros['intNumeroTarea']            =  $intNumeroTarea;
            $arrayParametros['intIdDetalle']              =  $objInfoTareaCaracteristica->getDetalleId();
            $arrayParametros['intIdEmpresa']              =  $intIdEmpresa;
            $arrayParametros['strTipoResponsable']        = 'E';
            $arrayParametros['intIdResponsable']          =  $objInfoDetalleAsignacion->getPersonaEmpresaRolId();
            $arrayParametros['intIdServicio']             =  null;
            $arrayParametros['intIdElementoNodo']         =  $intIdElementoNodo;
            $arrayParametros['intIdDispositivoActual']    =  $intIdElemento;
            $arrayParametros['strNombreNuevoElemento']    =  $strNombreNuevoElemento;
            $arrayParametros['strSerieDispositivoNuevo']  =  $strSerieNuevoElemento;
            $arrayParametros['strModeloDispositivoNuevo'] =  $strModeloNuevoElemento;
            $arrayParametros['strMacDispositivoNuevo']    =  $strMacNuevoElemento;
            $arrayParametros['strTipoDispositivoNuevo']   =  $strTipoElemento;
            $arrayParametros['strUsuario']                =  $strUsuario;
            $arrayParametros['strIpUsuario']              =  $strIpUsuario;
            $arrayRespuesta = $serviceInfoCambioElemento->cambioDispositivoNodo($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $strMensaje = 'Error al cambiar el elemento.';
            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMensaje = explode("Error : ", $objException->getMessage())[1];
            }

            $serviceUtil->insertError('InfoElementoNodoController',
                                      'cambiarElementoNodoAction',
                                       $objException->getMessage(),
                                       $strUsuario,
                                       $strIpUsuario);

            $arrayRespuesta = array("status" => false,"message" => $strMensaje);
        }
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }


    /**
     * Método encargado de obtener fecha registrada en la base de datos para 
     * concatenar al numero de serie automatica.
     *
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     *
     */ 
    public function getElementoSerieAutomaticaAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->getRequest();
        
        
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');

        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $objJson = $objJson->getElementoSerieAutomatica();

        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }


    /**
     * Método encargado de validar si serie ingresada
     * ya existe.
     *
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     *
     */ 
    public function validaElementoSerieAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion     = $this->getRequest();
        $strSerie  = $objPeticion->get('strSerie');
        
        
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');

        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $objJson = $objJson->getElementoSerieInfoElemento($strSerie);

        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    /**
      *
      * Método que elimina de forma lógica espacio(s) físico(s) del nodo.
      *                                                            
      * @return resultado
      * 
      * @author Gabriela Mora
      * @version 1.0 11-10-2022 - Version Inicial
      * 
      */  
      public function ajaxDeleteEspacioFisicoAction()
      {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $objPeticion     = $this->getRequest();
        $intIdEspacio  = $objPeticion->get('idEspacio');

        if($intIdEspacio)
        {
            $objEm = $this->get('doctrine')->getManager('telconet_infraestructura');               
            $objEm->beginTransaction();     
            $objEspacioFisico = $objEm->getRepository('schemaBundle:InfoEspacioFisico')
                                   ->find($intIdEspacio);

            $objEspacioFisico->setEstado('Pendiente');

            $objEm->persist($objEspacioFisico);
            $objEm->flush();
            $objEm->commit();

            $strMensaje = 'Se ha eliminado el espacio fisico con id: ' + strval($intIdEspacio);
            
            $objResultado = json_encode(array('success'=>true,'mensaje'=>$strMensaje));
            $objRespuesta->setContent($objResultado);
        }
        else
        {
            $strMensaje = 'Id de espacio fisico nulo.';
            $objResultado = json_encode(array('success'=>false,'mensaje'=>$strMensaje));
            $objRespuesta->setContent($objResultado);
        }

        return $objRespuesta;
        
      }


         /**
     * indexBitacoraAction
     *
     * Método que se encarga de recibir la peticion y listar los
     * registros de la bitacora.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     *
     * @return View
    */
    public function indexBitacoraAction()
    {
        $objSession                  = $this->get('request')->getSession();
        $strPrefijoEmpresaSession    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";

        if (true === $this->get('security.context')->isGranted('ROLE_154-8597') && $strPrefijoEmpresaSession == "TN")
        {
            return $this->render('tecnicoBundle:InfoElementoNodo/InfoBitacora:index.html.twig');
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicación.'));
    }

    /**
     * * getDepartamentoAction
     *
     * Método que devuelve los resultados de todos los departamentos
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getDepartamentoAction()
    {
        $objRequest = $this->get('request');
        $objSession = $this->get('session');

        $strEmpresaCod = $objSession->get('idEmpresa');

        $strNombre = $objRequest->get('query');

        $arrayParams = array();

        if (!empty($strEmpresaCod))
        {
            $arrayParams['empresaCod'] = $strEmpresaCod;
        }

        if (!empty($strNombre))
        {
            $arrayParams['nombre'] = $strNombre;
        }

        $serviceIBAN = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceIBAN->getDepartamentos($arrayParams);

        return new JsonResponse($objResponse);
    }

    /**
     * getTareaDetalleAction
     *
     * Método que devuelve los resultados de todos los departamentos
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTareaDetalleAction()
    {
        $arrayRequest = $this->get('request');
        $objRespuesta       = new JsonResponse();
        $arrayDetalleTarea  = array();
        $intTotal           = 0;
        $intValorTareaCaduca = 0;
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');

        if ($arrayRequest->get('idTarea'))
        {
            $serviceIBAN = $this->get('tecnico.InfoBitacoraAccesoNodo');
            $objResponse = $serviceIBAN->getTareaDetalle($arrayRequest->get('idTarea'));
            $objCaducaTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->findAccesoNodoByParam('TAREA_CADUCA');
            
            if (!empty($objResponse) && !empty($objCaducaTarea)) 
            {
                $strDateTarea = new \DateTime($objResponse['result']['fecha_solicitada']);
                $strDateActual = new \DateTime('now');
                $intDiasActual = $strDateTarea->diff($strDateActual);
                $intValorTareaCaduca = $objCaducaTarea->getValor2();
                if($intDiasActual->format('%d') <= $intValorTareaCaduca)
                {
                    $intTotal   = count($objResponse);
                    $arrayDetalleTarea = $objResponse['result'];
                }
            }
        }

        $objRespuesta->setData(array('total' => $intTotal, 'encontrados' => array($arrayDetalleTarea)));
        return $objRespuesta;
    }

    /**
     * getElementosNodoAction
     *
     * Funcion que devuelve los nodos encontrados
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 16-02-2023
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getElementosBitacoraAction()
    {
        $objRequest = $this->get('request');
        $objResponse = array();
        $strNombreNodo = $objRequest->get('query');
        $arrayParams = array();

        if (!empty($strNombreNodo))
        {
            $arrayParams['nombreNodo'] = $strNombreNodo;
            $arrayParams['estado'] = 'Activo';
            $serviceIBAN = $this->get('tecnico.InfoBitacoraAccesoNodo');
            $objResponse = $serviceIBAN->getElementoBitacora($arrayParams);
        }

        return new JsonResponse($objResponse);
    }

    /**
     * * getTareaAction
     *
     * Método que devuelve los resultados de todos los departamentos
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTareaAction()
    {
        $objRequest = $this->get('request');

        $strId = $objRequest->get('query');

        $arrayParams = array();

        if (!empty($strId))
        {
            $arrayParams['comunicacionId'] = $strId;
        }

        $serviceIBAN = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceIBAN->getTareas($arrayParams);

        return new JsonResponse($objResponse);
    }

    /**
     * * getBitacoraAccesoNodoAction
     *
     * Método que devuelve los resultados de todos las bitácoras de acceso a un NODO
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBitacorasAccesoNodoAction()
    {
        $objRequest = $this->get('request');
        $objSession = $this->get('session');

        $strEmpresaCod = $objSession->get('idEmpresa');

        $intStart = $objRequest->get('start');
        $intLimit = $objRequest->get('limit');

        $strNombreNodo = $objRequest->get('nodo');
        $strTecnicoAsignado = $objRequest->get('tecnicoAsignado');
        $strEstado = $objRequest->get('estado');
        $strCiudad = $objRequest->get('canton');
        $strFeCreacion = $objRequest->get('fechaApertura');
        $strDepartamento = $objRequest->get('departamento');
        $strElementoRelacionado = $objRequest->get('elementoRelacionado');
        $strFeMod = $objRequest->get('fechaCierre');
        $strTarea = $objRequest->get('tarea');

        $arrayParams = array();

        $arrayParams['intStart'] = $intStart;
        $arrayParams['intLimit'] = $intLimit;

        if (!empty($strEmpresaCod))
        {
            $arrayParams['empresaCod'] = $strEmpresaCod;
        }

        if (!empty($strNombreNodo))
        {
            $arrayParams['nombreNodo'] = $strNombreNodo;
        }

        if (!empty($strDepartamento))
        {
            $arrayParams['departamento'] = $strDepartamento;
        }


        if (!empty($strTecnicoAsignado))
        {
            $arrayParams['tecnicoAsignado'] = $strTecnicoAsignado;
        }

        if (!empty($strEstado))
        {
            $arrayParams['estado'] = $strEstado;
        }

        if (!empty($strFeCreacion))
        {
            $arrayParams['feCreacion'] = $strFeCreacion;
        }

        if (!empty($strCiudad))
        {
            $arrayParams['canton'] = $strCiudad;
        }

        if (!empty($strFeMod))
        {
            $arrayParams['feMod'] = $strFeMod;
        }

        if (!empty($strElementoRelacionado))
        {
            $arrayParams['elementoRelacionado'] = $strElementoRelacionado;
        }

        if (!empty($strTarea))
        {
            $arrayParams['tarea'] = $strTarea;
        }

        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->getBitacoras($arrayParams);

        return new JsonResponse($objResponse);
    }


    /**
     * newBitacoraAction
     *
     * Método que se encarga de recibir la petición y crear un
     * registro de una bitácora.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     *
     * @return View
    */
    public function newBitacoraAction()
    {
        $entityIBAN = new InfoBitacoraAccesoNodo();
        $objIBANT   = $this->createForm(new InfoBitacoraAccesoNodoType(), $entityIBAN);

        $objSession                  = $this->get('request')->getSession();
        $strPrefijoEmpresaSession    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";

        if (true === $this->get('security.context')->isGranted('ROLE_154-8597') && $strPrefijoEmpresaSession == "TN")
        {
            return $this->render(
                'tecnicoBundle:InfoElementoNodo/InfoBitacora:new.html.twig',
                array(
                    'entity' => $entityIBAN,
                    'form'   => $objIBANT->createView(),
                    'error'  => false
                )
            );
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicación.'));
    }

    /**
     * createBitacoraAction
     *
     * Método que apertura una bitácora en la base de datos  
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     */
    public function createBitacoraAction()
    {
        
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();

        $objIBANT = $objRequest->get('telconet_schemabundle_infobitacoraaccesonodotype');
        
        $arrayParams = array();
        
        if (!empty($objIBANT['tareaId']))
        {
            $arrayParams['tareaId'] = $objIBANT['tareaId'];
        }

        if (!empty($objIBANT['departamento']))
        {
            $arrayParams['departamentoId'] = $objIBANT['departamento'];
        }

        if (!empty($objIBANT['tecnicoAsignado']))
        {
            $arrayParams['tecnicoAsignado'] = $objIBANT['tecnicoAsignado'];
        }

        if (!empty($objIBANT['elementoNodoNombre']))
        {
            $arrayParams['elementoNodoNombre'] = $objIBANT['elementoNodoNombre'];
        }

        if (!empty($objIBANT['elemento']))
        {
            $arrayParams['elemento'] = $objIBANT['elemento'];
        }

        if (!empty($objIBANT['telefono']))
        {
            $arrayParams['telefono'] = $objIBANT['telefono'];
        }

        if (!empty($objIBANT['canton']))
        {
            $arrayParams['cantonNombre'] = $objIBANT['canton'];
        }

        if (!empty($objIBANT['observacion']))
        {
            $arrayParams['observacion'] = $objIBANT['observacion'];
        }

        if (!empty($objIBANT['codigos']))
        {
            $arrayParams['codigos'] = $objIBANT['codigos'];
        }
        
        $arrayParams['usrCreacion'] = $objSession->get('user');
        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->createBitacora($arrayParams);

        if ($objResponse['status'] != 200)
        {           
            return $this->render(
                'tecnicoBundle:InfoElementoNodo/InfoBitacora:new.html.twig',
                $this->getNewBitacora(true, $objResponse['mensaje'])
            );
        }

        $objIBAN = $objResponse['data'];
        return $this->redirect($this->generateUrl('elementonodo_showBitacora', array('intId' => $objIBAN['id'])));
    }

    private function getNewBitacora($boolSuccess, $strMensaje)
    {

        $entityIBAN = new InfoBitacoraAccesoNodo();
        $objIBANT   = $this->createForm(new InfoBitacoraAccesoNodoType(), $entityIBAN);

        return array(
            'entity'  => $entityIBAN,
            'form'    => $objIBANT->createView(),
            'error'   => $boolSuccess,
            'mensaje' => $strMensaje
        );
    }


    /**
      * editBitacoraAction
      *
      * Método que redirecciona a la ventana de edicion de la bitácora con la información existente
      *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
      *
      */ 
    //@Secure roles="ROLE_462-10962"
    public function editBitacoraAction($intId)
    {
        if (empty($intId))
        {
            throw new NotFoundHttpException('No existe la bitácora que se quiere modificar');
        }

        $arrayParams = array(
            'bitacoraId' => $intId,
            'estado' => 'Abierta'
        );

        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->getBitacora($arrayParams);

        if($objResponse['status'] != 200)
        {
            throw new NotFoundHttpException('No existe la bitácora que se quiere modificar');
        }

        $objIBAN = $objResponse['data'];
        if($objIBAN->getLoginAux() == null)
        {
            $objIBAN->setLoginAux('NA');
        }
        
        $objIBAN->setCodigos('');
        $objIBAN->setObservacion('');

        $objIBANT = $this->createForm(new InfoBitacoraAccesoNodoType(), $objIBAN);
                
        return $this->render('tecnicoBundle:InfoElementoNodo/InfoBitacora:edit.html.twig',
             array(
                 'obj' => $objIBAN,
                 'edit_form' => $objIBANT->createView(),
                 'error' => false
             )
        );
    }


    /**
     * updateBitacoraAction
     *
     * Método que cierra una una bitácora en la base de datos  
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0
     * @since 30-11-2022
     */
    public function updateBitacoraAction($intId)
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();

        if (empty($intId))
        {
            throw new NotFoundHttpException('No existe la bitácora que se quiere modificar');
        }

        $arrayParams = array(
            'bitacoraId' => $intId,
            'estado' => 'Abierta'
        );

        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->getBitacora($arrayParams);

        if($objResponse['status'] != 200)
        {
            throw new NotFoundHttpException('No existe la -bitácora- que se quiere modificar');
        }

        $objIBAN = $objResponse['data'];

        $objIBANT = $objRequest->get('telconet_schemabundle_infobitacoraaccesonodotype');

        $arrayParams = array();
        $arrayParams['bitacoraId'] = $intId;
        $arrayParams['tareaId'] = $objIBAN->getTareaId();
        $arrayParams['usrCreacion'] = $objSession->get('user');
        if (!empty($objIBANT['observacion']))
        {
            $arrayParams['observacion'] = $objIBANT['observacion'];
        }
        if (!empty($objIBANT['codigos']))
        {
            $arrayParams['codigos'] = $objIBANT['codigos'];
        }

        if($objIBAN->getElemento() != $objRequest->get('combo_elemento') && $objRequest->get('combo_elemento') != 'NA')
        {
            $arrayParams['elementoNuevo'] = $objRequest->get('combo_elemento');
        }

        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->updateBitacora($arrayParams);

        if ($objResponse['status'] != 200)
        {
            $objIBANT = $this->createForm(new InfoBitacoraAccesoNodoType(), $objIBAN);
            return $this->render('tecnicoBundle:InfoElementoNodo/InfoBitacora:edit.html.twig',
                 array(
                     'obj' => $objIBAN,
                     'edit_form' => $objIBANT->createView(),
                     'error' => true,
                     'mensaje' => $objResponse['mensaje']
                 )
            );
        }

        return $this->redirect(
            $this->generateUrl('elementonodo_showBitacora', array('intId' => $objIBAN->getId()))
        );
    }

    /**
      * showBitacoraAction
      *
      * Método que redirecciona a ventana del show de la bitácora consultada
      *
      * @param $id
      * @author Jeampier Carriel <jacarriel@telconet.ec>
      * @version 1.0
      * @since 30-11-2022
      */ 
      // @Secure(roles="ROLE_154-6")
    public function showBitacoraAction($intId)
    {
        if (empty($intId))
        {
            throw new NotFoundHttpException('No existe la -bitácora- que se quiere modificar');
        }

        $arrayParams = array(
            'bitacoraId' => $intId
        );

        $serviceBitacoraAccesoNodo = $this->get('tecnico.InfoBitacoraAccesoNodo');
        $objResponse = $serviceBitacoraAccesoNodo->getBitacora($arrayParams);

        if($objResponse['status'] != 200)
        {
            throw new NotFoundHttpException('No existe la -bitácora- que se quiere modificar');
        }

        $objIBAN = $objResponse['data'];

        return $this->render(
            'tecnicoBundle:InfoElementoNodo/InfoBitacora:show.html.twig',
            array('obj' => $objIBAN)
        );
    }

     /**
      * Metodo que se encarga de actualizar el nombre de un elemento perteneciente a un Nodo
      * 
      * @author Geovanny Cudco <acudco@telconet.ec>
      * @version 1.0
      * @since 02-03-2023
      */
      public function editarNombreElementoAction()
      {

        $objRespuesta           = new JsonResponse();
                
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $emInfraestructura      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $arrayParametros        = $objRequest->request->get('telconet_schemabundle_infoelementoracktype');
        $strTipoElemento        = $objRequest->get('tipoElemento');
        $strNombreElemento      = $objRequest->get('nombreElemento');
        $strSerieElemento       = $objRequest->get('serieElemento');        
        $intIdElemento          = $objRequest->get('idElemento');
        $serviceInfoelemento    = $this->get('tecnico.infoelemento');
        $strClase               = $objRequest->get('claseElemento');
        $serviceUtil            = $this->get('schema.Util');
        
        $arrayRolesPermitidos = array();
 	                                    
        if(true === $this->get('security.context')->isGranted('ROLE_154-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_154-6';
        }
        
        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $arrayParametros                           = array();
            $arrayParametros['objRequest']             = $objRequest;
            $arrayParametros['emInfraestructura']      = $emInfraestructura;
            $arrayParametros['strTipoElemento']        = $strTipoElemento;
            $arrayParametros['strNombreElemento']      = $strNombreElemento;
            $arrayParametros['strSerieElemento']       = $strSerieElemento;            
            $arrayParametros['intNodoElementoId']      = $intIdElemento;  
            $arrayParametros['strClase']               = $strClase;   
            $arrayParametros['strUserSession']         = $strUserSession;  
            $arrayParametros['strIpCreacion']          = $strIpCreacion;  

                
            //Creacion de nuevos elementos            
            $arrayEditarElemento  = $serviceInfoelemento->actualizarNombreElementoNodo($arrayParametros);

            if($arrayEditarElemento["status"] === "ERROR")
            {
                $objRespuesta->setContent($arrayEditarElemento['mensaje']);
                if ($emInfraestructura->getConnection()->isTransactionActive())
                {
                    $emInfraestructura->rollback();
                }
                
                $emInfraestructura->close();
                return $objRespuesta;
            }
                           
            $objRespuesta->setContent("OK");

        } 
        catch (\Exception $objEx) 
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
                
            $emInfraestructura->close();
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->editarNombreElementoAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al editar Elemento. Notificar a Sistemas'));
            $objRespuesta->setContent($objResultado);
        }

        return $objRespuesta;
      }

      /**
      * Metodo que se encarga de validar la serie, previa a la instalación del elemento en el nodo
      * 
      * @author Geovanny Cudco <acudco@telconet.ec>
      * @version 1.0
      * @since 07-04-2023
      */
      public function validarSeriePreInstlacionAction()
      {
        $objRespuesta           = new JsonResponse();
        $objRequest             = $this->get('request');
        $strSerieElemento       = $objRequest->get('serieElemento');
        $serviceUtil            = $this->get('schema.Util');
        $emNaf                  = $this->get('doctrine')->getManager('telconet_naf');

        try 
        {
            $arrayDatosNaf = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                                              ->findBy(array("numeroSerie" =>strtoupper($strSerieElemento)));

            if(isset($arrayDatosNaf) && !empty($arrayDatosNaf))
            {
                $objRespuesta->setContent("OK");
            }
            else
            {
                $objRespuesta->setContent("ERROR");
            }
        }
        catch (\Exception $objEx)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->comprobacionSerieAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al validar el elemento. Notificar a Sistemas'));
            $objRespuesta->setContent($objResultado);
        }
        return $objRespuesta;
      } 


       /**
      * Metodo que se encarga de validar el tipo de elemento cuando se lo va a editar
      * 
      * @author Geovanny Cudco <acudco@telconet.ec>
      * @version 1.0
      * @since 10-04-2023
      */
      public function validarTipoElementoAction()
      {
        $objRespuesta           = new JsonResponse();
        $objRequest             = $this->get('request');
        $strTipoElemento        = $objRequest->get('tipoElemento');
        $serviceUtil            = $this->get('schema.Util');        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        
        try
        {
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro'   => 'ELEMENTOS NODOS',
                                                                   'estado'            => 'Activo'));

            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy( array( 'parametroId' => $objAdmiParametroCab->getId(),
                                                                    'valor1'      => $strTipoElemento,
                                                                    'estado'      => 'Activo' ));

            if (!empty($objAdmiParametroDet)) 
            {
                $strSiglas = $objAdmiParametroDet->getValor3();
                $objRespuesta->setContent($strSiglas);
            }
            else
            {
                $objRespuesta->setContent("NA");
            }
        } 
        catch (\Exception $objEx)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoNodoController->validarTipoElemento',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al obtener las siglas del elemento. Notificar a Sistemas'));
            $objRespuesta->setContent($objResultado);
        }
        return $objRespuesta;
      }
}