<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\CallActivityType;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoCaso;
use telconet\schemaBundle\Entity\InfoDetalleHipotesis;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoCasoAsignacion;
use telconet\schemaBundle\Entity\InfoCasoHistorial;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoTareaTiempoAsignacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleTareaElemento;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class CallActivityController extends Controller implements TokenAuthenticatedController
{
    const CLASE_REQUERIMIENTOS_CLIENTES     = 'Requerimientos de Clientes';
    const SOLICITUD_REQUERIMIENTOS_CLIENTES = 'SOLICITUD REQUERIMIENTOS DE CLIENTES';
    const NOMBRE_TECNICO_PRODUCTO           = 'INTERNET';
    const ESTADO_ACTIVO                     = 'Activo';
    const ESTADO_APROBADA                   = 'Aprobada';
    const ESTADO_PENDIENTE                  = 'Pendiente';
    const CARACTERISTICA_SOLICITUD          = 'SOLICITUD_TAREA_CLIENTE';
    const ESTADO_ELIMINADO                  = 'Eliminado';
    const ESTADO_CANCELADA                  = 'Cancelada';
    const ESTADO_CERRADO                    = 'Cerrado';
    
    /**
    * @Secure(roles="ROLE_80-1")
    */ 
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();  
        
        $clienteSesion = $session->get('cliente');
        $strPrefijoEmpresaSession = $session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "";
        
        $boolClienteSesion = false;
        
        if($clienteSesion)
        {
            $boolClienteSesion = true;
        }
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");  	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());      

        return $this->render('soporteBundle:CallActivity:index.html.twig', array(
            'item'          => $entityItemMenu,
            'clienteSesion' => $boolClienteSesion,
            'strPrefijoEmpresaSession' => $strPrefijoEmpresaSession
		));
	}
    
    /**
     * Documentacion para 'newAction'
     * 
     * @Secure(roles="ROLE_80-2")
     * 
     * Método que crea el formulario para la creación de una actividad que puede ser un Caso o una Tarea
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-12-2015 - Se envía la constante 'CLASE_REQUERIMIENTOS_CLIENTES' para validar que al elegir esta clase de actividad podrán 
     *                           facturarle al cliente una vez finalizada la Tarea.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-11-2016 - Se envía los parámetros de la empresa, ciudad y departamento del usuario en sesión
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 06-11-2018 - Se envia el parámetro de asignaciones pendientes del usuario en sesión
     *          
     */     
    public function newAction()
    {
        $objSession     = $this->get('request')->getSession();
        $fechaActual    = new \DateTime('now');   
        $fecha          = $fechaActual->format('Y-m-d');
        $hora           = $fechaActual->format('H:i');
        $strUsrSesion   = $objSession->get('user');
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");
        $emComercial    = $this->getDoctrine()->getManager();
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");	
        
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $form   = $this->createForm(new CallActivityType());
        
        $punto_cliente  = $objSession->get('ptoCliente');
        $cliente        = $objSession->get('cliente');
        $idCliente      = "";
        $nombresCliente = "";
        $idPunto        = "";
        $loginPunto     = "";
        
        $strPrefijoEmpresaSession       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdCantonUsrSession          = 0;
        $intIdOficinaSesion             = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        if($intIdOficinaSesion)
        {
            $objOficinaSesion           = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            if(is_object($objOficinaSesion))
            {
                $intIdCantonUsrSession   = $objOficinaSesion->getCantonId();
            }
        }
        $intIdDepartamentoUsrSession     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $intIdEmpresa                    = $objSession->get('idEmpresa');
        
        $rolesPermitidos = array();
        //MODULO 8 - CLIENTE
        if(true === $this->get('security.context')->isGranted('ROLE_8-4017'))
        {
            $rolesPermitidos[] = 'ROLE_8-4017';
        }
        
        if(isset($punto_cliente) && isset($cliente))//retorna true si la variable existe y no es null
        { 
            if((!empty($punto_cliente)) && (!empty($cliente)) )// si no esta esta vacio en texto, numero o arreglo o si es null true
            {
                $idCliente = $punto_cliente['id_persona'];
                 
                if($cliente['nombres']=='')// si el nombre es vacio , lo lleno con la razon social
                {
                    $nombresCliente = $cliente['razon_social'];
                }
                else
                {
                    $nombresCliente = $cliente['nombres']."".$cliente['apellidos'];
                }
                  
                $idPunto=$punto_cliente['id'];
                $loginPunto=$punto_cliente['login'];
            }//((!empty($punto_cliente)) && (!empty($cliente)) )
        }//(isset($punto_cliente) && isset($cliente))
        
        $arrayAsignaciones = $emSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->findBy(array(
                                                                                                             "usrAsignado"  => $strUsrSesion,
                                                                                                             "tipoAtencion" => "TAREA",
                                                                                                             "estado"       => "Pendiente",
                                                                                                             "empresaCod"   => $intIdEmpresa
                                                                                                            )
                                                                                                      );

        return $this->render('soporteBundle:CallActivity:new.html.twig', array(
                                                                                'item'                          => $entityItemMenu,
                                                                                'form'                          => $form->createView(),            
                                                                                'idCliente'                     => $idCliente,
                                                                                'nombresCliente'                => $nombresCliente,
                                                                                'idPunto'                       => $idPunto,
                                                                                'loginPunto'                    => $loginPunto,
                                                                                'fecha'                         => $fecha,
                                                                                'hora'                          => $hora,
                                                                                'rolesPermitidos'               => $rolesPermitidos,
                                                                                'requerimientoClientes'         => self::CLASE_REQUERIMIENTOS_CLIENTES,
                                                                                'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
                                                                                'intIdCantonUsrSession'         => $intIdCantonUsrSession,
                                                                                'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
                                                                                'arrayAsignaciones'             => $arrayAsignaciones
                                                                               ));
    }
    
    /**
     * ajaxGetTareasByProcesoAction
     *
     * Metodo encargado de obtener el nombre de las empresas que sean diferentes a la de session
     *
     * @return json con las empresas
     *
     * @version 1.0 Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 18-03-2016
     *
     * @Secure(roles="ROLE_80-544")
     */
    public function ajaxGetTareasByProcesoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager("telconet");
        $peticion = $this->get('request');

        $nombreProceso = $peticion->query->get('nombreProceso');
        $estado = $peticion->query->get('estado');

        $prefijoEmpresa = $peticion->get('prefijoEmpresa') ? $peticion->get('prefijoEmpresa') : "";

        if($prefijoEmpresa != "" && $prefijoEmpresa != "N/A")
        {
            $emEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo' => $prefijoEmpresa));
            $codEmpresa = $emEmpresa->getId();
        }
        else
        {
            $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        }

        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");

        if(!$nombreProceso)
            $nombreProceso = "TAREAS SOPORTE";
        if(!$estado)
            $estado = "Activo";

        $idProceso = $peticion->query->get('id');
        $strNombreTarea = $peticion->query->get('query');

        $objJson = $this->getDoctrine()
                        ->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:AdmiTarea')
                        ->generarJsonTareasByProcesoAndTarea($em, "", "",$strNombreTarea, $estado, $idProceso, $codEmpresa);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * ajaxGetEmpresasDiferente
     *
     * Metodo encargado de obtener el nombre de las empresas que sean diferentes a la de session
     *
     * @return json con las empresas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 18-03-2016
     *
     * Actualización: Se envia parametros como arreglo en la función generarJsonEmpresasPorSistema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 - 20/11/2018
     * 
     * Actualización: Se reemplaza función generarJsonEmpresasPorSistema por generarJsonEmpresasVisiblesEnTareas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 - 17/12/2018
     *
     */
    public function getEmpresasDiferenteAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $strPrefijo     = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $em             = $this->getDoctrine()->getManager('telconet');
        $strTienePerfil = "N";
        //si la persona en sesión tiene la credencial: verTareasTodasEmpresas (ROLE_197_6157) 
        //se consulta empresas habilitadas para mostrarlas en el combo
        if (true === $this->get('security.context')->isGranted('ROLE_197-6157'))
        {
            $strTienePerfil = 'S';
        }
        $arrayParametros                    = array();
        $arrayParametros['prefijoConsulta'] =  $strPrefijo;
        $arrayParametros['prefijoExcluido'] =  array($strPrefijo);
        $arrayParametros['tienePerfil']     =  $strTienePerfil;
        $arrayResultado                     = $em->getRepository("schemaBundle:InfoEmpresaGrupo")
                                                 ->generarJsonEmpresasVisiblesEnTareas($arrayParametros);

        $respuesta->setContent($arrayResultado);
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_80-3")
     * 
     * createAction
     *
     * @version 1.0 Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 11-14-2016 Se realizan ajustes para solo tomar en cuenta el estado Activo al obtener el info_persona_empresa_rol
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-03-2016 Se realiza ajustes por integracion de concepto de Tarea Rapida, para que permita finalizar una tarea inmediatamente
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 16-05-2016 Se realiza ajustes para crear las tareas por actividades como tipo asignado "EMPLEADO"
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 24-05-2016 Se agrega el campo DEPARTAMENTO_ID en la tabla INFO_DETALLE_ASIGNACION, para determinar que departamento creo la tarea
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 30-05-2016 - Al guardar una actividad de Tipo Tarea y Clase 'Requerimientos de Clientes' se deberá generar una factura si el
     *                           usuario lo requiere.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 23-06-2016 Se asocia el CANTON_ID en la table INFO_DETALLE_ASIGNACION, para determinar la oficina de que canton crea la tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 29-06-2016 Se valida que el departamento sea obligatorio, para el envio de la notificacion (solo para TN)
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 31-06-2016 Se incorpora la subida de archivos desde la creación de las actividades
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 05-07-2016 Se valida que si ingresan caracteres de apertura y cierre de tags en la observacion, se eliminan
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 26-07-2016 Se realiza ajustes porque se agrega elementos en la creacion de actividades
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 28-07-2016 Cuando se crea una actividad sin un login en sesion se setea como remitente de la tarea al usuario conectado
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.2 02-09-2016 - Se cambia que cuando vaya a crear una tarea de clase 'Requerimientos de Clientes' y la misma deba facturarse
     *                           ya no consulte un servicio de nombre técnico 'INTERNET', puesto que para estos requerimientos es indistinto el 
     *                           producto asociado al servicio.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 17-11-2016 - Se cambia el estado de las Actividades a Asignada y se modifica la respectiva observación del seguimiento
     *                           de dicha tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.5 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.6 06-11-2018 - Se agrega que se grabe el id de la tarea en la asignación si el id de asignación no esta vacio
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.7 18-01-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.8 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 2.9 16-10-2020 - Se crea Solicitud de Facturación, características e historiales de la misma, si la tarea es de Reubicación.
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 3.0 08-03-2021 - Se implementa llamada a función que sube archivos por medio de microservicio nfs
     *
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 3.0 10-02-2021 - Se agrega programación para llamar a la función soporteService.replicarTareaAGestionPendientes para poder replicar
     *                           tareas a la tabla INFO_ASIGNACION_SOLICITUD.
     * 
     * @author Luis Arcángel Farro <lfarro@telconet.ec>
     * @version 3.1 12-01-2023 - Se agrega variables para identificar el proceso BOC - ACTIVACIÓN SERVICIOS DC 
     *                           y la tarea SERVICIO CLOUD - VALIDACIÓN Y ACTIVACION. Posterior se define una
     *                           variable que almacene el correo destino en caso que coincidan el proceso y la
     * proces                    tarea identificadas.
     * @author Liseth Candelario <lcandelrio@telconet.ec>
     * @version 3.1 14-11-2022 - Se extrae variabeles para poder hacer un registro nuevo si es que el objRequest trae valores del
     *                           paquete de horas de soporte, tambièn se realiza un regisstro extra a la info_parte_afectada 
     *                           para tener el servicio seleccionado.
     * 
     */
    public function createAction()
    {
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");            
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");     
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");
        
        $strEstadoAsignacion = 'Asignada';
        $request             = $this->getRequest();
        $objRequest          = $this->get('request');
        $session             = $objRequest->getSession();
        $actividad           = $objRequest->get("tipoGeneraActividad");
        $tareaRapida         = $objRequest->get("cboxTareaRapida");
        $intIdAsignacion     = $objRequest->get("asignacionSolicitud");
        $codEmpresa          = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
        $prefijoEmpresa      = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $boolCheckedFacturar = $objRequest->get('esFacturable') ? $objRequest->get('esFacturable') : false;
        $floatValorAFacturar = $objRequest->get('valorAFacturar') ? $objRequest->get('valorAFacturar') : 0.00;
        $floatValorAFacturar = str_replace(',', '', $floatValorAFacturar);
        $floatValorAFacturar = floatval($floatValorAFacturar);
        $datetimeActual      = new \DateTime('now');
        $strUserSession      = $session->get('user');
        $intIdDepartamento   = $session->get('idDepartamento');
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $serviceProceso      = $this->get('soporte.ProcesoService');
        $strIpCreacion       = $objRequest->getClientIp();
        $observacionAct      = "";
        $strNombreProceso    = "";
        $form = $this->createForm(new CallActivityType());      
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;
        // Se captura el parametro del combo proceso
        $strParamComboProceso = $objRequest->get('comboProcesos-inputEl');
        // Se captura el parametro del combo tarea
        $strParamComboTarea = $objRequest->get('comboTarea-inputEl');
        // Se define el correo destinatario
        $arrayEnvioCorreoProcesoBoc  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('ENVIO_CORREO_BOC',
                                                                                                'SOPORTE',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                10);
        
        //SE EXTRAEN LOS DATOS PARA UTILIZARLOS EN PAQUETE DE SOPORTES
        $boolEsPaqueteDeSoporte = $objRequest->get("bool_paqueteSoporte");
        $strProductoLogin       = $objRequest->get("descripcion_producto");

        $form->handleRequest($request);
        
        if( $form->isValid() )
        {
            $emComunicacion->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();
            $emSoporte->getConnection()->beginTransaction();
            
            try 
            {
                $parametros         = $objRequest->get('telconet_schemabundle_callactivitytype');
                $intIdFormaContacto = $parametros['tipo'];
                $punto              = ( $objRequest->get("login_cliente") && $objRequest->get("login_cliente") != "" 
                                        && $objRequest->get("login_cliente") != "undefined" 
                                        ? $emComercial->getRepository("schemaBundle:InfoPunto")->find($objRequest->get("login_cliente")) : "");
                $puntoLogin         = ($punto ? ($punto->getLogin() ? $punto->getLogin() : "") : "");
                $puntoId            = ($punto ? ($punto->getId() ? $punto->getId() : "") : "");
                $personaEmresaRolId = ($punto ? $punto->getPersonaEmpresaRolId() : "");
                $cliente            = ($personaEmresaRolId ? $personaEmresaRolId->getPersonaId() : "");
                $nombreCliente      = ($cliente ? ($cliente->getRazonSocial() ? $cliente->getRazonSocial() : 
                (($cliente->getNombres() || $cliente->getApellidos()) ? $cliente->getNombres() . " " . $cliente->getApellidos() : "") ): "");
		
                //Cuando la actividad seleccionada es un 'Caso'
                if($actividad == "C")
                {
                    $caso = new InfoCaso();
                    $caso->setTipoCasoId($emSoporte->getRepository("schemaBundle:AdmiTipoCaso")->find("1"));
                    $caso->setNivelCriticidadId($emSoporte->getRepository("schemaBundle:AdmiNivelCriticidad")->find(1));
                    $caso->setTipoNotificacionId($parametros['tipo']);
                    $caso->setNumeroCaso($emSoporte->getRepository('schemaBundle:InfoCaso')->getNumeroCasoNext());
                    $fecha = date_create(date('Y-m-d H:i',strtotime($objRequest->get('fecha_apertura').' '.$objRequest->get('hora_apertura'))));
                    $caso->setFeApertura($fecha);
                    $caso->setFeCreacion(new \DateTime('now'));
                    $caso->setUsrCreacion($session->get('user'));
                    $caso->setIpCreacion($objRequest->getClientIp());
                    $caso->setEmpresaCod($session->get('idEmpresa'));
                    $emSoporte->persist($caso);
                    $emSoporte->flush();
                    
                    $idSintoma = $objRequest->get("sintoma");
                    $sintomas  = $emSoporte->getRepository('schemaBundle:AdmiSintoma')->findOneById($idSintoma);
			    
                    $infoDetalleHipotesis = new InfoDetalleHipotesis();
                    $infoDetalleHipotesis->setCasoId($caso);
                    $infoDetalleHipotesis->setSintomaId($sintomas);
                    $infoDetalleHipotesis->setEstado("Creado");
                    $infoDetalleHipotesis->setObservacion("Creacion del Caso");
                    $infoDetalleHipotesis->setFeCreacion(new \DateTime('now'));
                    $infoDetalleHipotesis->setUsrCreacion($session->get('user'));
                    $infoDetalleHipotesis->setIpCreacion($objRequest->getClientIp());					
                    $emSoporte->persist($infoDetalleHipotesis);
                    $emSoporte->flush();
				    
                    $infoDetalle = new InfoDetalle();
                    $infoDetalle->setDetalleHipotesisId($infoDetalleHipotesis->getId());
                    $infoDetalle->setPesoPresupuestado(0);
                    $infoDetalle->setValorPresupuestado(0);
                    $infoDetalle->setFeCreacion(new \DateTime('now'));
                    $infoDetalle->setUsrCreacion($session->get('user'));
                    $infoDetalle->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($infoDetalle);
                    $emSoporte->flush();
					
                    $asignacion = new InfoCasoAsignacion();
                    $asignacion->setDetalleHipotesisId($infoDetalleHipotesis);
                    $asignacion->setAsignadoId($session->get('idDepartamento'));
                    $asignacion->setAsignadoNombre($session->get('departamento'));
                    $asignacion->setRefAsignadoId($session->get('id_empleado'));
                    $asignacion->setRefAsignadoNombre($session->get('empleado'));
                    
                    if($session->get('idPersonaEmpresaRol'))
                    { 
                        $asignacion->setPersonaEmpresaRolId($session->get('idPersonaEmpresaRol')); 
                    }
                    
                    $asignacion->setMotivo("Creacion del caso.");
                    $asignacion->setUsrCreacion($session->get('user'));
                    $asignacion->setFeCreacion(new \DateTime('now'));
                    $asignacion->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($asignacion);
					$emSoporte->flush();					
					
                    $historial = new InfoCasoHistorial();
                    $historial->setCasoId($caso);
                    $historial->setObservacion("Creacion del caso");
                    $historial->setEstado("Creado");
                    $historial->setFeCreacion(new \DateTime('now'));
                    $historial->setUsrCreacion($session->get('user'));
                    $historial->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($historial);
                    $emSoporte->flush();
					
                    $criterio = new InfoCriterioAfectado();
                    $criterio->setId("1");         
                    $criterio->setDetalleId($infoDetalle);
                    $criterio->setCriterio("Clientes");
                    $criterio->setOpcion("Cliente: " . $nombreCliente . " | OPCION: Punto Cliente");
                    $criterio->setFeCreacion(new \DateTime('now'));
                    $criterio->setUsrCreacion($session->get('user'));
                    $criterio->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($criterio);
                    $emSoporte->flush();
									    
                    $afectado = new InfoParteAfectada();  
                    $afectado->setTipoAfectado("Cliente");
                    $afectado->setDetalleId($infoDetalle->getId());
                    $afectado->setCriterioAfectadoId($criterio->getId());
                    $afectado->setAfectadoId($puntoId);
                    $afectado->setFeIniIncidencia($caso->getFeApertura());                        
                    $afectado->setAfectadoNombre($puntoLogin);
                    $afectado->setAfectadoDescripcion($nombreCliente);
                    $afectado->setFeCreacion(new \DateTime('now'));
                    $afectado->setUsrCreacion($session->get('user'));
                    $afectado->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($afectado);
                    $emSoporte->flush();	
                }//($actividad == "C")
                
                /*******************************************************************************************************/
                //    		AUTOSIGNACION DE TAREAS DE USUARIO EN SESION EN CASO DE NO REALIZAR ASIGNACION
                /*******************************************************************************************************/
                $idEmpAsignado      = $objRequest->get('empleado'); //empleado asignado
                $idDepartamento     = $objRequest->get("departamento_asignado");
                $nombreDepartamento = $objRequest->get("departamento_asignado_nombre");  
                $nombreEmpAsignado  = "";   
                
                if($idEmpAsignado)
                {
                    $idEmpAsignado    = explode("@@",$idEmpAsignado);                     
                    $empleadoAsignado = $idEmpAsignado[0];
                }
                else
                {
                    $userLoegueado = $session->get('user');		
                    $usuario       = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneBy(array('login'=>$userLoegueado));
			    
                    $empleadoAsignado = $usuario->getId();			    			    
			    
                    $infoPersonaER = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->getDepartamentoPersonaLogueada($empleadoAsignado,$codEmpresa);	
					
                    $idDepartamento     = $infoPersonaER[0]["departamento"];
                    $departamento       = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($idDepartamento);
                    $nombreDepartamento =  $departamento->getNombreDepartamento();
                }
                /************************************************************************************************/
                /* @var $objSoporteService SoporteService */
                $objSoporteService = $this->get('soporte.SoporteService');

                //Cuando la actividad seleccionada es una 'Tarea'
                if($actividad == "T")
                {	
                    $fecha = date_create(date('Y-m-d H:i',strtotime($objRequest->get('fecha_apertura').' '.$objRequest->get('hora_apertura'))));
		
                    $infoDetalle = new InfoDetalle();
                    $infoDetalle->setTareaId($emSoporte->getRepository("schemaBundle:AdmiTarea")->find($objRequest->get("tarea")));

                    //Se eliminan simbolos de tags
                    $observacionAct = $objSoporteService->eliminarSimbolosDeTags($objRequest->get("observacion_contenido"));

                    $infoDetalle->setObservacion($observacionAct);
                    $infoDetalle->setPesoPresupuestado(0);
                    $infoDetalle->setValorPresupuestado(0);
                    $infoDetalle->setFeSolicitada($fecha);
                    $infoDetalle->setFeCreacion(new \DateTime('now'));
                    $infoDetalle->setUsrCreacion($session->get('user'));
                    $infoDetalle->setIpCreacion($objRequest->getClientIp());
                    $emSoporte->persist($infoDetalle);
                    $emSoporte->flush();
                    
                    $arrayParametrosHist["intDetalleId"] = $infoDetalle->getId();

                    if($objRequest->get("idElemento"))
                    {
                        $arrayElementos = explode("|",$objRequest->get("idElemento"));

                        //Se relaciona la Tarea con el Elemento Seleccionado
                        for($intIndiceEl=0; $intIndiceEl < count($arrayElementos); $intIndiceEl++)
                        {
                            $intIdElemento = intval($arrayElementos[$intIndiceEl]);
                            if ($intIdElemento>0)
                            {
                                $objInfoDetalleTareaElemento = new InfoDetalleTareaElemento();
                                $objInfoDetalleTareaElemento->setDetalleId($infoDetalle);
                                $objInfoDetalleTareaElemento->setElementoId($intIdElemento);
                                $objInfoDetalleTareaElemento->setFeCreacion(new \DateTime('now'));
                                $objInfoDetalleTareaElemento->setUsrCreacion($session->get('user'));
                                $objInfoDetalleTareaElemento->setIpCreacion($objRequest->getClientIp());
                                $emSoporte->persist($objInfoDetalleTareaElemento);
                                $emSoporte->flush();
                            }
                        }
                    }
                    $entityDetalleAsignacion = new InfoDetalleAsignacion();
                    $entityDetalleAsignacion->setDetalleId($infoDetalle);					                          
			
                    if($observacionAct)
                    {
                        $entityDetalleAsignacion->setMotivo($observacionAct);
                    }
                    
                    $entityDetalleAsignacion->setAsignadoId($idDepartamento);
                    $entityDetalleAsignacion->setAsignadoNombre($nombreDepartamento);
                                 
                    if($empleadoAsignado && $empleadoAsignado!='' && isset($empleadoAsignado))
                    {
                        $persona=$emComercial->getRepository("schemaBundle:InfoPersona")->find($empleadoAsignado);
                        
                        if($persona->getNombres()=="")
                        {
                            $nombreEmpAsignado=$persona->getRazonSocial();
                        }
                        else
                        {
                            $nombreEmpAsignado=$persona->getNombres()." " .$persona->getApellidos();
                        }//($persona->getNombres()=="")
                        
                        $entityDetalleAsignacion->setRefAsignadoId($empleadoAsignado);
                        $entityDetalleAsignacion->setRefAsignadoNombre($nombreEmpAsignado); 
                        
                        //persona_empresa_rol_id
                        $personaAsignada    = $emComercial->getRepository("schemaBundle:InfoPersona")->find($empleadoAsignado);
                        $PersonaEmpresaRol  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                          ->findOneBy( array( 'personaId'      => $personaAsignada, 
                                                                              'departamentoId' => $idDepartamento,
                                                                              'estado'         => 'Activo' ) );
                        $entityDetalleAsignacion->setPersonaEmpresaRolId($PersonaEmpresaRol->getId()); 
                    }
                    else
                    {
                        $entityDetalleAsignacion->setRefAsignadoId($session->get('id_empleado'));
                        $entityDetalleAsignacion->setRefAsignadoNombre($session->get('empleado'));                                                
                        $entityDetalleAsignacion->setRefAsignadoNombre($session->get('idPersonaEmpresaRol'));

                    }//($empleadoAsignado && $empleadoAsignado!='' && isset($empleadoAsignado))
                    
                    $entityDetalleAsignacion->setIpCreacion($objRequest->getClientIp());
                    $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                    $entityDetalleAsignacion->setUsrCreacion($session->get('user'));
		            $entityDetalleAsignacion->setTipoAsignado("EMPLEADO");
                	$entityDetalleAsignacion->setDepartamentoId($session->get('idDepartamento'));

                    if($session->get('idPersonaEmpresaRol'))
                    {
                        $entityPersonaEmpresaRol  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                ->find($session->get('idPersonaEmpresaRol'));

                        if($entityPersonaEmpresaRol->getOficinaId())
                        {
                            $entityInfoOficinaGrupo  = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                    ->find($entityPersonaEmpresaRol->getOficinaId());

                            if($entityInfoOficinaGrupo->getCantonId())
                            {
                                $entityDetalleAsignacion->setCantonId($entityInfoOficinaGrupo->getCantonId());
                            }
                        }
                    }

                    $emSoporte->persist($entityDetalleAsignacion);
                    $emSoporte->flush();                       
                                        
                    //Se ingresa el historial de la tarea
                    $arrayParametrosHist["strObservacion"]  = "Tarea Asignada - Modulo de Actividades";
                    $arrayParametrosHist["strEstadoActual"] = $strEstadoAsignacion;  
                    $arrayParametrosHist["strAccion"]       = "Asignada";

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                    if($cliente && $nombreCliente != "")
                    {
                        $criterio = new InfoCriterioAfectado();
                        $criterio->setId("1");         
                        $criterio->setDetalleId($infoDetalle);
                        $criterio->setCriterio("Clientes");
                        $criterio->setOpcion("Cliente: " . $nombreCliente . " | OPCION: Punto Cliente");
                        $criterio->setFeCreacion(new \DateTime('now'));
                        $criterio->setUsrCreacion($session->get('user'));
                        $criterio->setIpCreacion($objRequest->getClientIp());
                        $emSoporte->persist($criterio);
                        $emSoporte->flush();
											
                        $afectado = new InfoParteAfectada();  
                        $afectado->setTipoAfectado("Cliente");
                        $afectado->setDetalleId($infoDetalle->getId());
                        $afectado->setCriterioAfectadoId($criterio->getId());
                        $afectado->setAfectadoId($puntoId);
                        $afectado->setFeIniIncidencia($infoDetalle->getFeSolicitada());                        
                        $afectado->setAfectadoNombre($puntoLogin);
                        $afectado->setAfectadoDescripcion($nombreCliente);
                        $afectado->setFeCreacion(new \DateTime('now'));
                        $afectado->setUsrCreacion($session->get('user'));
                        $afectado->setIpCreacion($objRequest->getClientIp());
                        $emSoporte->persist($afectado);
                        $emSoporte->flush();	

                        if($boolEsPaqueteDeSoporte =='S')
                        {
                            $objServicio     = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->findOneBy(array('loginAux' => $strProductoLogin));
                            $intServicioId   = $objServicio->getId();

                            $afectado = new InfoParteAfectada();  
                            $afectado->setTipoAfectado("Servicio");
                            $afectado->setDetalleId($infoDetalle->getId());
                            $afectado->setCriterioAfectadoId($criterio->getId());
                            $afectado->setAfectadoId($intServicioId);
                            $afectado->setFeIniIncidencia($infoDetalle->getFeSolicitada());                        
                            $afectado->setAfectadoNombre($strProductoLogin);
                            $afectado->setAfectadoDescripcion($nombreCliente);
                            $afectado->setFeCreacion(new \DateTime('now'));
                            $afectado->setUsrCreacion($session->get('user'));
                            $afectado->setIpCreacion($objRequest->getClientIp());
                            $emSoporte->persist($afectado);
                            $emSoporte->flush();
                        }
                    }//($cliente && $nombreCliente != "")


                    //Se ingresa el seguimiento de la tarea
                    $arrayParametrosHist["strObservacion"]  = "Tarea fue Asignada a ".$nombreEmpAsignado;
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                    $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    
                    //Si es una tarea rapida se finaliza la tarea de inmediato pero se deja el historial
                    if($tareaRapida == "S")
                    {
                        $infoTareaTiempoAsignacion = new InfoTareaTiempoAsignacion();
                        $infoTareaTiempoAsignacion->setDetalleId($infoDetalle->getId());
                        $infoTareaTiempoAsignacion->setTiempoCliente(0);
                        $infoTareaTiempoAsignacion->setTiempoEmpresa(1);
                        $infoTareaTiempoAsignacion->setObservacion("Tarea rapida");
                        $infoTareaTiempoAsignacion->setFeCreacion(new \DateTime('now'));
                        $infoTareaTiempoAsignacion->setUsrCreacion($session->get('user'));
                        $infoTareaTiempoAsignacion->setFeEjecucion(new \DateTime('now'));
                        $infoTareaTiempoAsignacion->setFeFinalizacion(new \DateTime('now'));
                        $emSoporte->persist($infoTareaTiempoAsignacion);
                        $emSoporte->flush();

                        
                        //Se ingresa el historial de la tarea
                        $arrayParametrosHist["strObservacion"]  = "Se finaliza por tarea rapida";
                        $arrayParametrosHist["strEstadoActual"] = "Finalizada";
                        $arrayParametrosHist["strOpcion"]       = "Historial";
                        $arrayParametrosHist["strAccion"]       = "Finalizada";

                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);


                        //Se ingresa el historial de la tarea
                        $arrayParametrosHist["strObservacion"]  = "Tarea fue Finalizada Obs : Tarea Rapida";
                        $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    }
                }//($actividad == "T")
			
                
                /***************************************************************************************************/
                //		ENVIO MAIL DE NOTIFICACION POR CREACION DE TAREA/CASO
                /***************************************************************************************************/	
                $departamento = ''; 
                $canton       = '';  
                $empresa      = '';
                
                //EN caso de que no exista un tipo de llamada definida y se establezca a manera informativa
                if($entityDetalleAsignacion)
                {
                    $departamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                              ->find( $actividad == "T" ? $entityDetalleAsignacion->getAsignadoId():$asignacion->getAsignadoId() );								
					
                    if( ($entityDetalleAsignacion && $entityDetalleAsignacion->getRefAsignadoId()) 
                        || ($asignacion && $asignacion->getRefAsignadoId()) )
                    {		     
                        $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                            ->findOneBy( array( 'personaId'      => ( $actividad == "T" 
                                                                                                       ? $entityDetalleAsignacion->getRefAsignadoId()
                                                                                                       : $asignacion->getRefAsignadoId()
                                                                                                    ),
                                                                               'formaContactoId' => 5,
                                                                               'estado'          => "Activo"));					  

                        if($infoPersonaFormaContacto)	
                        {
                            $to[] = $infoPersonaFormaContacto->getValor(); //Correo Persona Asignada	
                        }

                        if(is_array($arrayEnvioCorreoProcesoBoc) && !empty($arrayEnvioCorreoProcesoBoc))
                        {
                            foreach ($arrayEnvioCorreoProcesoBoc as $arrayBocProceso)
                            { 
                                $strCorreoDestinatario = $arrayBocProceso['valor3'];
                                $strComboProceso = $arrayBocProceso['valor1'];
                                $strComboTarea = $arrayBocProceso['valor2'];
                                if($strParamComboProceso == $strComboProceso && $strParamComboTarea == $strComboTarea)
                                {
                                    $to[] = $strCorreoDestinatario;
                                }
                            }

                        }

                        $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                             ->find( $actividad == "T" 
                                                                     ? $entityDetalleAsignacion->getPersonaEmpresaRolId() 
                                                                     : $asignacion->getPersonaEmpresaRolId() );											

                        if($infoPersonaEmpresaRol)
                        {		  
                              $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                     ->find($infoPersonaEmpresaRol->getOficinaId()->getId());
                              $canton  = $oficina->getCantonId();
                        }
                        else
                        {
                            $canton  = '';
                        }	
                    }//( ($entityDetalleAsignacion && $entityDetalleAsignacion->getRefAsignadoId()) 
                     //|| ($asignacion && $asignacion->getRefAsignadoId()) )							
			
                    if($departamento)
                    {
                        $empresa      = $departamento->getEmpresaCod();
                        $departamento = $departamento->getId();												
                    }
                }//($entityDetalleAsignacion)
                /********************************************************************************************************/				
								                                              
                               						      
                $objAdmiClaseDocumento = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->find($parametros['claseDocumento']);
               
                $infoDocumento = new InfoDocumento();
                $infoDocumento->setMensaje($parametros['observacion']);
                $infoDocumento->setNombreDocumento("Registro de llamada.");
                $infoDocumento->setClaseDocumentoId($objAdmiClaseDocumento);
                $infoDocumento->setFeCreacion(new \DateTime('now'));
                $infoDocumento->setEstado("Activo");
                $infoDocumento->setUsrCreacion($session->get('user'));
                $infoDocumento->setIpCreacion($objRequest->getClientIp());
                $infoDocumento->setEmpresaCod($codEmpresa);
                $emComunicacion->persist($infoDocumento);
                $emComunicacion->flush();
                                
                $infoComunicacion = new InfoComunicacion();
                $infoComunicacion->setFormaContactoId($parametros['tipo']);
                if($puntoLogin)
                {
                    $infoComunicacion->setRemitenteId($puntoId);
                    $infoComunicacion->setRemitenteNombre($puntoLogin);
                }
                else
                {
                    $infoComunicacion->setRemitenteId($session->get('id_empleado'));
                    $infoComunicacion->setRemitenteNombre($session->get('empleado'));
                }
                $infoComunicacion->setClaseComunicacion("Recibido");
                
                //Cuando la actividad seleccionada es un 'Caso'
                if($actividad == "C")
                { 
                    $infoComunicacion->setCasoId($caso->getId()); 
                }
                
                
                //Cuando la actividad seleccionada es una 'Tarea'
                if($actividad == "T")
                { 
                    $infoComunicacion->setDetalleId($infoDetalle->getId()); 
                    
                    if( $objAdmiClaseDocumento )
                    {
                        if( $objAdmiClaseDocumento->getNombreClaseDocumento() == self::CLASE_REQUERIMIENTOS_CLIENTES )
                        {
                            if( $boolCheckedFacturar )
                            {
                                $arrayParametros = array( 'estadosServicios'         => array('Activo', 'In-Corte'),
                                                          'login'                    => $puntoLogin,
                                                          'empresaCod'               => $codEmpresa,
                                                          'limite'                   => 1 );

                                $arrayResultados = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getServiciosByCriterios( $arrayParametros );
                                
                                if( !empty($arrayResultados['registros']) )
                                {
                                    $objServicio = $arrayResultados['registros'][0];
                                    
                                    if( empty($objServicio) )
                                    {
                                        throw new \Exception('No se encontró servicio para asociar la solicitud de requerimiento');
                                    }
                                    
                                    $objMotivo   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                             ->findOneByNombreMotivo('Solicitud al crear tarea por requerimientos de clientes');
                                    $intIdMotivo = 0;
                                    if( $objMotivo )
                                    {
                                        $intIdMotivo = $objMotivo->getId();
                                    }
                                    
                                    $strObservacion   = 'Se crea solicitud por '.self::CLASE_REQUERIMIENTOS_CLIENTES;
                                    $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                    ->findOneByDescripcionSolicitud( self::SOLICITUD_REQUERIMIENTOS_CLIENTES );
                                    
                                    $objDetalleSolicitud = new InfoDetalleSolicitud();
                                    $objDetalleSolicitud->setEstado(self::ESTADO_PENDIENTE);
                                    $objDetalleSolicitud->setFeCreacion($datetimeActual);
                                    $objDetalleSolicitud->setUsrCreacion($strUserSession);
                                    $objDetalleSolicitud->setServicioId($objServicio);
                                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                                    $objDetalleSolicitud->setMotivoId($intIdMotivo);
                                    $objDetalleSolicitud->setPrecioDescuento($floatValorAFacturar);
                                    $objDetalleSolicitud->setObservacion($strObservacion);
                                    $emComercial->persist($objDetalleSolicitud);
                                    $emComercial->flush();
                                    
                                    
                                    $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                         ->findOneByDescripcionCaracteristica( self::CARACTERISTICA_SOLICITUD );
                                    
                                    $objDetalleSolCarac = new InfoDetalleSolCaract();
                                    $objDetalleSolCarac->setDetalleSolicitudId($objDetalleSolicitud);
                                    $objDetalleSolCarac->setEstado(self::ESTADO_ACTIVO);
                                    $objDetalleSolCarac->setFeCreacion($datetimeActual);
                                    $objDetalleSolCarac->setUsrCreacion($strUserSession);
                                    $objDetalleSolCarac->setValor($infoDetalle->getId());
                                    $objDetalleSolCarac->setCaracteristicaId($objAdmiCaracteristica);
                                    $emComercial->persist($objDetalleSolCarac);
                                    $emComercial->flush();
                                    
                                    
                                    $objDetalleSolHist = new InfoDetalleSolHist();
                                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                                    $objDetalleSolHist->setEstado(self::ESTADO_PENDIENTE);
                                    $objDetalleSolHist->setFeCreacion($datetimeActual);
                                    $objDetalleSolHist->setIpCreacion($strIpCreacion);
                                    $objDetalleSolHist->setUsrCreacion($strUserSession);
                                    $objDetalleSolHist->setMotivoId($intIdMotivo);
                                    $objDetalleSolHist->setObservacion($strObservacion);
                                    $emComercial->persist($objDetalleSolHist);
                                    $emComercial->flush();
                                }//( !empty($arrayResultados['registros']) )
                                else
                                {
                                    throw new \Exception('El cliente no tiene servicio en estado Activo o In-Corte');
                                }//( empty($arrayResultados['registros']) )
                            }//( $boolCheckedFacturar )
                        }//( $clase->getNombreClaseDocumento() == self::CLASE_REQUERIMIENTOS_CLIENTES )
                    }//( $clase )
                }//($actividad == "T")
                
                $fecha = date_create(date('Y-m-d H:i',strtotime($objRequest->get('fecha_apertura').' '.$objRequest->get('hora_apertura'))));
                $infoComunicacion->setFechaComunicacion($fecha);
                $infoComunicacion->setEstado("Activo");
                $infoComunicacion->setFeCreacion(new \DateTime('now'));
                $infoComunicacion->setUsrCreacion($session->get('user'));
                $infoComunicacion->setIpCreacion($objRequest->getClientIp());
                $infoComunicacion->setEmpresaCod($codEmpresa);
                $emComunicacion->persist($infoComunicacion);
                $emComunicacion->flush();
                
                $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
                $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
                $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
                $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                $infoDocumentoComunicacion->setEstado('Activo');
                $infoDocumentoComunicacion->setUsrCreacion($session->get('user'));
                $infoDocumentoComunicacion->setIpCreacion($objRequest->getClientIp());
                $emComunicacion->persist($infoDocumentoComunicacion);
                $emComunicacion->flush();         
                
                //Graba el número de tarea en la asignación
                if (!empty($intIdAsignacion))
                {
                    $arrayParametrosAsig['intIdAsignacion'] = $intIdAsignacion;
                    $arrayParametrosAsig['strNumeroTarea']  = $infoComunicacion->getId();
                    $arrayParametrosAsig['strTipoAtencion'] = 'TAREA';
                    $arrayParametrosAsig['strTipoProblema'] = '';
                    $arrayParametrosAsig['strUsuario']      = $strUserSession;
                    $arrayParametrosAsig['strIpCreacion']   = $strIpCreacion;
                    $serviceSoporte->agregarNumeroEnAsignacionSolicitud($arrayParametrosAsig);
                }
                $strActividad = "";
                    
                //Cuando la actividad seleccionada es un 'Caso'
                if($actividad == "C")
                {       
                    $afectados    =	$objJson = $this->getDoctrine()
                                                    ->getManager("telconet_soporte")
                                                    ->getRepository('schemaBundle:InfoCaso')
                                                    ->getRegistrosAfectadosTotalXCaso($caso->getId());
                    $asunto       = "Creacion de Caso ".$caso->getNumeroCaso();
                    $strActividad = "CASO";  
                    $parametros   = array(  'caso'             => $caso, 
                                            'afectados'        => $afectados, 							    							    
                                            'empleadoLogeado'  => $objRequest->getSession()->get('empleado'),
                                            'empresa'          => $objRequest->getSession()->get('prefijoEmpresa')
                                         );
                }//($actividad == "C")
                
                
                //Cuando la actividad seleccionada es una 'Tarea'
                if($actividad == "T")
                {      		    
                    $detalle      = $objJson = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($infoDetalle->getId());
                    $tarea        = $objJson = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($detalle->getTareaId());

                    if(is_object($tarea))
                    {
                        $strNombreProceso = $tarea->getProcesoId()->getNombreProceso();
                    }

                    $asunto       = "Nueva Tarea, Actividad #".$infoComunicacion->getId()." | PROCESO: ".$strNombreProceso;
                    $strActividad = "TAREAACT";

                    $parametros   = array(  'nombreProceso'     => $strNombreProceso,
                                            'actividad'         => $infoComunicacion,
                                            'asignacion'        => $entityDetalleAsignacion,
                                            'nombreTarea'       => $tarea->getNombreTarea(),
                                            'empleadoLogeado'   => $objRequest->getSession()->get('empleado'),
                                            'empresa'           => $objRequest->getSession()->get('prefijoEmpresa'),
                                            'detalle'           => $infoDetalle,
                                            'tareaRapida'       => $tareaRapida
                                         );					  					  		  
                }//($actividad == "T")

                $objSoporteService            = $this->get('soporte.SoporteService');
                $arrayRespuestaExtensionesRes = $objSoporteService->getExtensionesDeArchivosRestringidas();
                if ($arrayRespuestaExtensionesRes['success'] == 'false' )
                {
                    throw new \Exception($arrayRespuesta['mensaje']);
                }

                $arrayArchivos           = $this->getRequest()->files->get('archivos');
                if(isset($arrayArchivos[1]) && is_object($arrayArchivos[1]))
                {
                    $strNombreArchivo        = $arrayArchivos[1]->getClientOriginalName();
                    $arrayPartsNombreArchivo = explode('.', $strNombreArchivo);
                    $strLast                 = array_pop($arrayPartsNombreArchivo);
                    $arrayPartsNombreArchivo = array(implode('_', $arrayPartsNombreArchivo), $strLast);
                    $strExtArchivo           = $arrayPartsNombreArchivo[1];

                    $strExtensionesRestringidas   = $arrayRespuestaExtensionesRes['extensiones'];

                    /* Se validan extensiones restringidas */ 
                    if (!(strpos($strExtensionesRestringidas, strtolower($strExtArchivo)) === false))
                    {
                        throw new \Exception('Archivo con extensión (' . $strExtArchivo . ') no permitida');
                    }
                }
                /**********************************************************************
                 *** USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS ***
                 ***********************************************************************/
                /* @var $envioPlantilla EnvioPlantilla */
                $envioPlantilla = $this->get('soporte.EnvioPlantilla');

                $enviaNotificacion = true;
                //Se agrega validacion solo para TN, para que se envie la notificacion es necesario que llegue un departamento
                if($prefijoEmpresa == "TN")
                {
                    if($departamento)
                    {
                        $enviaNotificacion = true;
                    }
                    else
                    {
                        $enviaNotificacion = false;
                    }
                }

                if($enviaNotificacion)
                {
                    $envioPlantilla->generarEnvioPlantilla($asunto, $to , $strActividad, $parametros , $empresa , $canton, $departamento);
                }
                
                /****************************************************************/
                // Se crea Solicitud de Facturación si la tarea es de Reubicación//
                /****************************************************************/
                $strNombreParametro = "NOMBRES_TAREAS_REUBICACION";
                $strTareaNueva      = $objRequest->get("comboTarea-inputEl");
                $arrayTareasReub    = array();
                $strUsrCreaReub     = "telcos_reubica";

                $arrayParamTareas   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get($strNombreParametro,'FINANCIERO','','','','','','','',$codEmpresa,'');
                
                foreach($arrayParamTareas as $arrayTarea)
                {
                    array_push($arrayTareasReub, $arrayTarea['valor1']);
                }
                
                // Se verifica que la tarea se encuentre en los parametros de NOMBRES_TAREAS_REUBICACION.
                if (in_array($strTareaNueva,$arrayTareasReub) && ($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")) 
                {
                    //Se obtiene el servicio mandatorio (Servicio de Internet).    
                    $arrayServicioInt   = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                      ->getServicioPreferenciaByPunto(['intIdPunto' => $puntoId]);
                    
                    if($arrayServicioInt[0]['ID_SERVICIO'] == null || $arrayServicioInt[0]['ID_SERVICIO'] == "")
                    {
                        throw new \Exception("No existe un Punto en sesión o el Servicio de Internet no se encuentra activo.");
                    }
                    
                    $objServicioInt     = $emComercial->getRepository("schemaBundle:InfoServicio")->find($arrayServicioInt[0]['ID_SERVICIO']);
                    
                    //Se obtiene el tipo de solicitud para facturar por reubicación.
                    $objTipoSolReub     = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                      ->findOneBy( array('descripcionSolicitud' => 'SOLICITUD FACTURACION POR REUBICACION',
                                                                         'estado'               => "Activo"));
                    
                    //Se obtiene  la característica  'NUMERO_TAREA_REUBICACION', para enlazarla con la solicitud de la factura.
                    $objCaractTareaReub = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                      ->findOneBy( array('descripcionCaracteristica' => "NUMERO_TAREA_REUBICACION",
                                                                         'estado'                    => "Activo") );
                       
                    //Obtiene el precio del plan de Reubicación
                    $arrayParamPlanReub  = array('strNombrePlan' => 'REUBICACION', 'strCodEmpresa' => $codEmpresa);
                    $arrayPrecioPlan = $emSoporte->getRepository('schemaBundle:InfoDetalle')->obtenerPrecioPlanReubicacion($arrayParamPlanReub);
                    $floatPrecioPlan = $arrayPrecioPlan[0]['floatPrecioPlan'];
                    
                    $strDescSolFactReub  = "Se crea Solicitud de Factura por Reubicación según tarea No. ".$infoComunicacion->getId();
                    
                    //Se obtiene el motivo de la Solicitud de Factura
                    $arrayParamMotivo   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PROCESO_REUBICACION','FINANCIERO','','MOTIVO_SOLICITUD_FACT','','','','','',$codEmpresa,'');
                    
                    $objMotivo   = $emGeneral->getRepository("schemaBundle:AdmiMotivo")
                                             ->findOneBy(array('nombreMotivo' => $arrayParamMotivo[0]['valor1']));
                    
                    $intIdMotivo = is_object($objMotivo) ? $objMotivo->getId() : null;
                    
                    if(is_object($objServicioInt) && is_object($objTipoSolReub) && is_object($objCaractTareaReub))
                    {
                        //GUARDAR INFO DETALLE SOLICICITUD - Solicitud de Factura
                        $objSolFactReub = new InfoDetalleSolicitud();
                        $objSolFactReub->setServicioId($objServicioInt);
                        $objSolFactReub->setTipoSolicitudId($objTipoSolReub);
                        $objSolFactReub->setMotivoId($intIdMotivo);
                        $objSolFactReub->setEstado("Pendiente");
                        $objSolFactReub->setUsrCreacion($strUsrCreaReub);
                        $objSolFactReub->setFeCreacion(new \DateTime('now'));
                        $objSolFactReub->setPrecioDescuento($floatPrecioPlan);
                        $objSolFactReub->setObservacion($strDescSolFactReub);
                        $emComercial->persist($objSolFactReub);

                        //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                        $objDetSolFactReubHist = new InfoDetalleSolHist();
                        $objDetSolFactReubHist->setDetalleSolicitudId($objSolFactReub);
                        $objDetSolFactReubHist->setObservacion($strDescSolFactReub);
                        $objDetSolFactReubHist->setIpCreacion($objRequest->getClientIp());
                        $objDetSolFactReubHist->setFeCreacion(new \DateTime('now'));
                        $objDetSolFactReubHist->setUsrCreacion($strUsrCreaReub);
                        $objDetSolFactReubHist->setEstado("Pendiente");
                        $emComercial->persist($objDetSolFactReubHist);
                                           
                        $objDetSolCaractTareaReub = new InfoDetalleSolCaract();
                        $objDetSolCaractTareaReub->setCaracteristicaId($objCaractTareaReub);
                        $objDetSolCaractTareaReub->setDetalleSolicitudId($objSolFactReub);
                        $objDetSolCaractTareaReub->setEstado('Activo');
                        $objDetSolCaractTareaReub->setFeCreacion(new \DateTime('now'));
                        $objDetSolCaractTareaReub->setUsrCreacion($strUsrCreaReub);
                        $objDetSolCaractTareaReub->setValor($infoComunicacion->getId());
                        $emComercial->persist($objDetSolCaractTareaReub);
                        
                        $emComercial->flush();
                        
                    }   
                }
                
                $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                   ->findOneBy(array('id' => $intIdFormaContacto));
                
                
                if(!is_object($entityFormaContacto))
                {
                    throw new \Exception("Error al obtener información sobre la forma de contacto");
                }
                
                if($entityFormaContacto->getDescripcionFormaContacto()=="ATC" && $objRequest->get("intPuntoAtencion") != "")
                {
                 
                    $entityCaracteristicas = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array('descripcionCaracteristica' => 'PUNTO_ATENCION'));
                    
                    if(!is_object($entityCaracteristicas))
                    {
                        throw new \Exception("Error al obtener información de la características");
                    }
                    
                    $objTareaCaracteristica = new InfoTareaCaracteristica();
                    $objTareaCaracteristica->setTareaId($infoComunicacion->getId());
                    $objTareaCaracteristica->setDetalleId($infoDetalle->getId());
                    $objTareaCaracteristica->setCaracteristicaId($entityCaracteristicas->getId());
                    $objTareaCaracteristica->setValor($objRequest->get("intPuntoAtencion"));
                    $objTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objTareaCaracteristica->setUsrCreacion($session->get('user'));
                    $objTareaCaracteristica->setIpCreacion($objRequest->getClientIp());
                    $objTareaCaracteristica->setEstado("Activo");
                    $emSoporte->persist($objTareaCaracteristica);
                    $emSoporte->flush();
                    
                }
                
                
                $emComunicacion->getConnection()->commit();
                $emComercial->getConnection()->commit();                
                $emSoporte->getConnection()->commit();
            
                $emComercial->getConnection()->close();
                $emComunicacion->getConnection()->close();
                $emSoporte->getConnection()->close();
                
                /**Guardar Archivos**/
                $arrayParametrosArchivos     = array(
                    "idCaso"                => 0,
                    "idTarea"               => $infoDetalle->getId(),
                    "servicio"              => 0,
                    "origenCaso"            => "N",
                    "origenTarea"           => "S",
                    "strCodigoDocumento"    => "",
                    "strPrefijoEmpresa"     => $session->get('prefijoEmpresa'),
                    "strUser"               => $session->get('user'),
                    "strIdEmpresa"          => $session->get('idEmpresa'),
                    "arrayArchivos"         => $arrayArchivos
                );

                $objSoporteService->guardarMultiplesAdjuntosCasosTareasEnNfs($arrayParametrosArchivos);


                //Proceso para el envío de la tarea al sistema de Sys Cloud-Center de Data Center.
                if (is_object($infoComunicacion) && $objRequest->get("tarea") !== '' && $objRequest->get("tarea") !== null)
                {
                    $objPersonaAsignado = $emComercial->getRepository("schemaBundle:InfoPersona")->find($empleadoAsignado);
                    $objAdmiTarea       = $emSoporte->getRepository("schemaBundle:AdmiTarea")->find($objRequest->get("tarea"));

                    if (is_object($objPersonaAsignado) && is_object($objAdmiTarea)
                            && is_object($objAdmiTarea->getProcesoId()))
                    {
                        $strUserAsignado = $objPersonaAsignado->getRazonSocial() !== null
                                            ? $objPersonaAsignado->getRazonSocial()
                                            : $objPersonaAsignado->getNombres().' '.$objPersonaAsignado->getApellidos();

                        $serviceProceso->putTareasSysCluod(array ('strNombreTarea'      => $objAdmiTarea->getNombreTarea(),
                                                                  'strNombreProceso'    => $objAdmiTarea->getProcesoId()
                                                                                                ->getNombreProceso(),
                                                                  'strObservacion'      => $observacionAct,
                                                                  'strFechaApertura'    => $objRequest->get('fecha_apertura'),
                                                                  'strHoraApertura'     => $objRequest->get('hora_apertura'),
                                                                  'strUser'             => $session->get('user'),
                                                                  'strIpAsigna'         => $objRequest->getClientIp(),
                                                                  'strUserAsigna'       => $session->get('empleado'),
                                                                  'strDeparAsigna'      => $session->get('departamento'),
                                                                  'strUserAsignado'     => $strUserAsignado,
                                                                  'strDeparAsignado'    => $nombreDepartamento,
                                                                  'objInfoComunicacion' => $infoComunicacion));
                    }
                }


                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']    = is_object($infoDetalle)? $infoDetalle->getId():null;
                $arrayParametrosInfoTarea['strUsrCreacion']  = isset($strUserSession)? $strUserSession:null;
                $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);

                $arrayParametrosGestionPend['intDepartamentoId']  = $idDepartamento;
                $arrayParametrosGestionPend['strTipoAtencion']    = 'TAREA';
                $arrayParametrosGestionPend['strLogin']           = $puntoLogin;
                $arrayParametrosGestionPend['strTipoProblema']    = $infoDetalle->getTareaId()->getNombreTarea();
                $arrayParametrosGestionPend['strNombreReporta']   = "";
                $arrayParametrosGestionPend['strNombreSitio']     = "";
                $arrayParametrosGestionPend['strCriticidad']      = "Alta";
                $arrayParametrosGestionPend['strAgente']          = $objPersonaAsignado->getLogin();
                $arrayParametrosGestionPend['strDetalle']         = $observacionAct;
                $arrayParametrosGestionPend['strNumero']          = $infoComunicacion->getId();
                $arrayParametrosGestionPend['idEmpresa']          = $session->get('idEmpresa');
                $arrayParametrosGestionPend['strUsrCreacion']     = $session->get('user');
                $arrayParametrosGestionPend['intOficinaId']       = $entityPersonaEmpresaRol->getOficinaId()->getId();
                $arrayParametrosGestionPend['strIpCreacion']      = $objRequest->getClientIp();
                $arrayParametrosGestionPend['arrayAsigProact']    = "";
                $arrayParametrosGestionPend['intTareaId']         = $infoDetalle->getTareaId()->getId();
                $arrayParametrosGestionPend['intProcesoId']       = $infoDetalle->getTareaId()->getProcesoId()->getId();
                $arrayParametrosGestionPend['intFormaContactoId'] = $infoComunicacion->getFormaContactoId();
                $arrayParametrosGestionPend['intReferenciaId']    = $infoComunicacion->getId();
                $objSoporteService->replicarTareaAGestionPendientes($arrayParametrosGestionPend);


                return $this->redirect($this->generateUrl('callactivity_show', array('id' => $infoComunicacion->getId())));                            
            } 
            catch(\Exception $e) 
            {
                error_log($e->getMessage());
                
                $emComercial->getConnection()->rollback();
                $emComunicacion->getConnection()->rollback();
                $emSoporte->getConnection()->rollback();
            
                $emComercial->getConnection()->close();
                $emComunicacion->getConnection()->close();
                $emSoporte->getConnection()->close();
                
                $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            }
        }//if( $form->isValid() )
        return $this->redirect($this->generateUrl('callactivity_new')); 
    }
    
    
    	  public function notificaOperacion($view,$asunto,$to = null){


	
		  $to[]="notificaciones_telcos@telconet.ec";			
			
			foreach($to as $correo){
		  			    			  
			    if($correo!=null && $correo!="")
			

			      if(strlen($correo) > 5)
				  
				  $correos[] = $correo;


			}
						
			

				$message = \Swift_Message::newInstance()
							->setSubject($asunto)
							->setFrom('notificaciones_telcos@telconet.ec')
							->setTo($correos)
							->setBody($view,'text/html');


				$this->get('mailer')->send($message);



                         
	  }
    
      
    /**
     * Documentacion para 'showAction'
     * 
     * @Secure(roles="ROLE_80-6")
     * 
     * Método que muestra la información de la actividad creada
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 10-12-2015 - Se envía si la tarea o actividad creada fue marcada como 'Se debe facturar'.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 25-07-2016 - Se realizan ajustes para presentar subtareas asociadas a la tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 27-07-2016 Se realiza ajustes porque se agrega elementos en la creacion de actividades
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.4 23-10-2020 - Se realizan validaciones para presentar el botón de Solicitud de Nota de Credito por reubicación.
     *
     */
    public function showAction($id)
    {
        $session           = $this->get('request')->getSession();
        $emComunicacion    = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em_seguridad      = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");     
        $entityItemMenu    = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");
        $strNombreElemento = "";
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        
        $entity = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($id);
		
		$formaContacto   = "N/A";
		$formaContactoId = $entity->getFormaContactoId();
		if($formaContactoId)
		{
			$formaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($formaContactoId);
		}
		
        
        $documentoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoComunicacion')->findByComunicacionId($entity->getId());
        $objDocumento          = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                ->find($documentoComunicacion[0]->getDocumentoId()->getId());
        
        $strClase = '';
        
        if( $objDocumento )
        {
             $strClase = $objDocumento->getClaseDocumentoId() ? $objDocumento->getClaseDocumentoId()->getNombreClaseDocumento() : '';
        }
           
        if (!$entity)
        {
            throw $this->createNotFoundException('No se encuentra la actividad asociada.');
        }

        $origenGenera = ($entity->getCasoId() != '' ? "Caso" : "");
        $origenGenera = ($entity->getDetalleId() != '' && $entity->getCasoId() == '' ? "Tarea" : $origenGenera);
        $origenGenera = ($entity->getDetalleId() == '' && $entity->getCasoId() == '' ? "Ninguno" : $origenGenera);

        $numero                   = "";
        $nombreTarea              = ""; 
        $estado                   = ""; 
        $estadoCaso               = "";
        $nombreAsignada           = ""; 
        $departamentoAsignado     = ""; 
        $departamentoCreador      = ''; 
        $nombreCreador            = '';
        $strLoginAfectado         = "";
        $strNombreClienteAfectado = "";
        $boolEsFacturable         = false;
        $floatValorAFacturar      = 0.00;
		
		$esTarea = true;
		
        if($origenGenera == "Caso")
        {
            $caso       = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneById($entity->getCasoId());
            $numero     = ($caso ? $caso->getNumeroCaso() : "");
            $estadoCaso = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstadoCaso($entity->getCasoId());
        }
        
        if($origenGenera == "Tarea")
        {
            $detalle     = $emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($entity->getDetalleId());
            $nombreTarea = ($detalle ? ($detalle->getTareaId() ? ( $detalle->getTareaId()->getNombreTarea() 
                                                                   ? $detalle->getTareaId()->getNombreTarea() : "") : "") : "");
			
			$detalleAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->findByDetalleId($detalle->getId());
			
			$nombreAsignada       = $detalleAsignacion[count($detalleAsignacion)-1]->getRefAsignadoNombre();
			$departamentoAsignado = $detalleAsignacion[count($detalleAsignacion)-1]->getAsignadoNombre();
			
			$loginCreador = $detalleAsignacion[count($detalleAsignacion)-1]->getUsrCreacion();
			
			$persona = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($loginCreador);
			
			$nombreCreador = $persona->getNombres().' '.$persona->getApellidos();			
			
			$estado  = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($detalle->getId());	
			$esTarea = true;
            
            if( $strClase == self::CLASE_REQUERIMIENTOS_CLIENTES )
            {
                $objAfectado = $emSoporte->getRepository('schemaBundle:InfoParteAfectada')->findOneByDetalleId($detalle);
                
                if( $objAfectado )
                {
                    $strLoginAfectado         = $objAfectado->getAfectadoNombre();
                    $strNombreClienteAfectado = $objAfectado->getAfectadoDescripcion();
                    $objAdmiCaracteristica    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneByDescripcionCaracteristica( self::CARACTERISTICA_SOLICITUD );
                    $objDetalleSolCarac       = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy( array( 'valor'            => $detalle->getId(), 
                                                                                'caracteristicaId' => $objAdmiCaracteristica) );

                    if( $objDetalleSolCarac )
                    {
                        $boolEsFacturable    = true;
                        $floatValorAFacturar = $objDetalleSolCarac->getDetalleSolicitudId() 
                                               ? $objDetalleSolCarac->getDetalleSolicitudId()->getPrecioDescuento() : 0.00;
                        $floatValorAFacturar = number_format(floatval($floatValorAFacturar), 2, '.', ',');
                    }//( $objDetalleSolCarac )
                }//( $objAfectado )
            }//( $strClase == self::CLASE_REQUERIMIENTOS_CLIENTES )

            if($detalle)
            {
                $objInfoDetalleTareaElemento = $emSoporte->getRepository('schemaBundle:InfoDetalleTareaElemento')
                                                         ->findOneByDetalleId($detalle->getId());
                if($objInfoDetalleTareaElemento)
                {
                    $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->find($objInfoDetalleTareaElemento->getElementoId());
                    if($objInfoElemento)
                    {
                        $strNombreElemento = $objInfoElemento->getNombreElemento();
                    }
                }
            }
		}//($origenGenera == "Tarea")        
        
        
        $strUrlShowCaso = "";
        
        if( $numero )
        {
            $strUrlShowCaso = $this->generateUrl('infocaso_show', array('id' => $entity->getCasoId()));
        }
		
        $deleteForm = $this->createDeleteForm($id);

        //Se obtiene el numero de subtareas que existen
        $entitySubtareas   = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getRegistrosSubtareas($id);
        $intTotalSubTareas = count($entitySubtareas['registros']);
        
        
        //*******************************************************************//
        //VALIDACIÓN PARA PRESENTAR EL BOTÓN DE SOLICITUD DE NC REUBICACIÓN.//
        //*****************************************************************//         
        $arrayTareasReub   = array();
        $strSolicitaNCReub = "N";
        $intIdPunto        = ($session->get('ptoCliente') ? $session->get('ptoCliente')['id'] : "");
        $intCodEmpresa     = $session->get('idEmpresa') ? $session->get('idEmpresa') : "";
        $strPrefijoEmpresa = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $serviceSoporte    = $this->get('soporte.SoporteService');
        
        // Se obtiene la solicitud de FC o NC que esta enlazada la tarea de reubicación.
        $arraySolFcSinSolNcReub = $serviceSoporte->obtieneSolFcSinSolNcReub(array('intIdTarea'=> $id));
        
        //Se obtiene el parametro de días de vigencia que debe tener la tarea de reubicación.
        $arrayVigTareaReub = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->get('PROCESO_REUBICACION','FINANCIERO','',
                                             'VIGENCIA_SOLICITUD_NC','','','','','',$intCodEmpresa,'');
        
        // Se obtiene el número de días que tiene creada la tarea de reubicación.
        $objTareaReub       = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($id);
        $objFechaActual     = new \DateTime('now');
        $objFechaTarea      = $objTareaReub->getFechaComunicacion();
        $objDiffFecha       = $objFechaActual->diff($objFechaTarea);
        $intDiasTareaCreada = $objDiffFecha->days;
        
        // Se obtiene el parametro de los nombres de tareas de reubicación.
        $arrayParamTareaReub   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('NOMBRES_TAREAS_REUBICACION','FINANCIERO','','','','','','','',$intCodEmpresa,'');

        foreach($arrayParamTareaReub as $arrayTareaReub)
        {
            array_push($arrayTareasReub, $arrayTareaReub['valor1']);
        }
        
        // Se valida si existe una factura activa ligada a la solicitud de Reubicación, para crear inmediatamente la NC       
        $arrayParamFactReub = array('intIdTarea' => $id,
                                    'intIdPunto' => $intIdPunto);
        $arrayFactConNcReub = $serviceSoporte->obtieneFactConNcReub($arrayParamFactReub);
        
        // Estado de tarea de Reubicación diferente a Anulada, Rechazada, Cancelada
        $arrayEstados      = array("Anulada", "Rechazada", "Cancelada");
        $strEstadoAplicaNc = in_array($estado,$arrayEstados) ? 'N' : 'S';
            
        // Se verifica que la tarea sea de reubicación,
        // Que la tarea tenga un estado diferente de "Anulada", "Rechazada", "Cancelada",
        // Que exista solicitud de FC sin solicitudes de NC enlazadas a la tarea,
        // Que no exista una NC enlazada a la factura que se emitió por Solicitud de FC por reubicación,
        // Que tenga la tarea N días creada según el parámetro VIGENCIA_SOLICITUD_NC. 
        if (in_array($nombreTarea,$arrayTareasReub) && ($strEstadoAplicaNc == 'S') && 
           ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") && !empty($arraySolFcSinSolNcReub) 
            && empty($arrayFactConNcReub) && ($intDiasTareaCreada <= $arrayVigTareaReub[0]['valor1'])) 
        {
            $strSolicitaNCReub = "S";
        }
        
        $strPuntoAtencion = "";
        if (is_object($formaContacto))
        {
            //Obtener nombre del punto de atencion
            $strDescripcionFC = $formaContacto->getDescripcionFormaContacto();

            if($strDescripcionFC === "ATC")
            {
                $entityTareaCaracteristica   = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                                                            ->findOneBy(array('tareaId' => $id));
                    
                if(is_object($entityTareaCaracteristica))
                {
                    $intIdPuntoAtencion = $entityTareaCaracteristica->getValor();
                    
                    
                    $entityPuntoCaracteristica  = $emComercial->getRepository('schemaBundle:AdmiPuntoAtencion')
                                                                ->findOneBy(array('id' => $intIdPuntoAtencion));
                    
                    
                    if(is_object($entityPuntoCaracteristica))
                    {
                        $strPuntoAtencion = $entityPuntoCaracteristica->getNombrePuntoAtencion();
                    }
                    
                }
            }
        }
        
        $parametros = array(
                                'item'                  => $entityItemMenu,
                                'entity'                => $entity,
                                'forma'                 => $formaContacto,
                                'documento'             => $objDocumento,
                                'origenGenera'          => $origenGenera,
	                            'idCaso'                => $entity->getCasoId(),
                                'numero'                => $numero,
                                'nombreTarea'           => $nombreTarea,
                                'numeroTarea'           => $id,
                                'nombreElemento'        => $strNombreElemento,
                                'cantidadTareas'        => $intTotalSubTareas,
                                'estadoCaso'            => $estadoCaso,
                                'estadoTarea'           => $estado,
                                'clase'                 => $objDocumento->getClaseDocumentoId(),
                                'departamento'          => $departamentoAsignado?$departamentoAsignado:"",
                                'empleado'              => $nombreAsignada?$nombreAsignada:"",
                                'creador'               => $nombreCreador,
                                'delete_form'           => $deleteForm->createView(),
                                'strUrlShowCaso'        => $strUrlShowCaso,
                                'numero'                => $numero,
                                'loginAfectado'         => $strLoginAfectado,
                                'nombreClienteAfectado' => $strNombreClienteAfectado,
                                'boolEsFacturable'      => $boolEsFacturable,
                                'floatValorAFacturar'   => $floatValorAFacturar,
                                'strSolicitaNCReub'     => $strSolicitaNCReub,
                                'strPuntoAtencion'      => $strPuntoAtencion
                           );
        
        return $this->render('soporteBundle:CallActivity:show.html.twig', $parametros);
    }
    

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
    * @Secure(roles="ROLE_80-4")
    */ 
    public function editAction($id)
    {
        $session = $this->get('request')->getSession();
		
        $form   = $this->createForm(new CallActivityType());
        
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        
        $comunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($id);
        $documentoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoComunicacion')->findByComunicacionId($comunicacion->getId());
        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($documentoComunicacion[0]->getDocumentoId()->getId());
        
		$claseDocumento = $documento->getClaseDocumentoId();
		$detalle = ($claseDocumento ? ($claseDocumento->getId() ? $claseDocumento->getId() : "") : "");
        
        return $this->render('soporteBundle:CallActivity:edit.html.twig', array(
            'item' => $entityItemMenu,
            'form'   => $form->createView(),
            'formaContacto' => $comunicacion->getFormaContactoId(),
            'detalle'   => $detalle,
            'fecha'  => $comunicacion->getFechaComunicacion(),
            'observacion' => $documento->getMensaje(),
            'comunicacion' => $comunicacion,
        ));
    }
    
    /**
    * @Secure(roles="ROLE_80-5")
    */     
    public function updateAction($id)
    {
		$emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("80", "1");
		
        $request = $this->getRequest();
        $peticion = $this->get('request');
        
        $form    = $this->createForm(new CallActivityType());
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $emComunicacion->getConnection()->beginTransaction();
           
            // Try and make the transaction
            try {
                $parametros = $peticion->get('telconet_schemabundle_callactivitytype');
                
                $infoComunicacion = $emComunicacion->getRepository("schemaBundle:InfoComunicacion")->find($id);
                $infoComunicacion->setFormaContactoId($parametros['tipo']);
                $punto = $emComercial->getRepository("schemaBundle:InfoPunto")->find($peticion->get("cliente"));
                $infoComunicacion->setRemitenteId($peticion->get('cliente'));
                $infoComunicacion->setRemitenteNombre($punto->getLogin());
                $infoComunicacion->setClaseComunicacion("Recibido");
                $fecha = date_create(date('Y-m-d H:i',strtotime($peticion->get('fecha_apertura').' '.$peticion->get('hora_apertura'))));
                $infoComunicacion->setFechaComunicacion($fecha);
                $infoComunicacion->setEstado("Modificado");
                $emComunicacion->persist($infoComunicacion);
                $emComunicacion->flush();
                
                $documentoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoComunicacion')->findByComunicacionId($infoComunicacion->getId());
                $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($documentoComunicacion[0]->getDocumentoId()->getId());
        
                $clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->find($parametros['claseDocumento']);
                $documento->setMensaje($parametros['observacion']);
                $documento->setClaseDocumentoId($clase);
                $documento->setEstado("Modificado");
                $emComunicacion->persist($documento);
                $emComunicacion->flush();
                
                
                $documentoComunicacion[0]->setEstado('Modificado');
                $emComunicacion->persist($documentoComunicacion[0]);
                $emComunicacion->flush();
                
                                        
                $emComunicacion->getConnection()->commit();
                
                return $this->redirect($this->generateUrl('callactivity_show', array('id' => $infoComunicacion->getId())));
                
                
            //proc_close(proc_open ("/usr/bin/java -jar /home/trouble_tickets/src/Telconet/TroubleTicketsBundle/Resources/batch/TTBackboneSit/dist/TTBackboneSit.jar '".$info_ticket->getIdTicket()."' '".$empleado->getIdEmpleado()."' '".date_format($info_ticket->getFeApertura(),"Y-m-d").' '.date_format($info_ticket->getHoraApertura(),"G:i:s")."' '".Util::getRealIpAddr()."' &", array(), $foo));
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('soporteBundle:CallActivity:new.html.twig', $parametros);
    }
    
    /**
     * Documentacion para 'deleteAction'
     * 
     * @Secure(roles="ROLE_80-8")
     * 
     * Método que elimina las actividades creadas
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 10/12/2015 - Se modifica para que al eliminar una actividad  y además se elimine la solicitud de facturación del cliente 
     *                           si la actividad iba ha ser facturada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     */     
    public function deleteAction($id)
    {
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $datetimeActual = new \DateTime('now');
        $strUserSession = $objSession->get('user');
        $strIpCreacion  = $objRequest->getClientIp();
        $intCodEmpresa  = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0);
        $intIdDepartamento   = $objSession->get("idDepartamento");
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $intCodEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;        

        $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($id);

        
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
        
        try 
        {
            if(!$objInfoComunicacion)
            {
                throw new Exception('No se encontró actividad en nuestra base de datos.');
            }
            else
            {
                $intIdDetalle = $objInfoComunicacion->getDetalleId() ? $objInfoComunicacion->getDetalleId() : 0;
                $intIdCaso    = $objInfoComunicacion->getCasoId() ? $objInfoComunicacion->getCasoId() : 0;
                
                $strObservacion = 'Se elimina la actividad';
                $objMotivo      = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strObservacion);
                $intIdMotivo    = 0;
                if( $objMotivo )
                {
                    $intIdMotivo = $objMotivo->getId();
                }

                if( $intIdDetalle )
                {
                    $objInfoDetalle        = $emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);
                    $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica( self::CARACTERISTICA_SOLICITUD );
                    $objDetalleSolCarac    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                         ->findOneBy( array( 'valor'            => $intIdDetalle, 
                                                                             'caracteristicaId' => $objAdmiCaracteristica,
                                                                             'estado'           => self::ESTADO_ACTIVO ) );

                    if( $objDetalleSolCarac )
                    {
                        $objDetalleSolCarac->setEstado(self::ESTADO_ELIMINADO);
                        $emComercial->persist($objDetalleSolCarac);
                        $emComercial->flush();
                        
                        

                        $objDetalleSolicitud = $objDetalleSolCarac->getDetalleSolicitudId() ? $objDetalleSolCarac->getDetalleSolicitudId() : null;

                        if( $objDetalleSolicitud )
                        {
                            $objDetalleSolicitud->setEstado(self::ESTADO_ELIMINADO);
                            $emComercial->persist($objDetalleSolicitud);
                            $emComercial->flush();

                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolHist->setEstado(self::ESTADO_ELIMINADO);
                            $objDetalleSolHist->setFeCreacion($datetimeActual);
                            $objDetalleSolHist->setIpCreacion($strIpCreacion);
                            $objDetalleSolHist->setUsrCreacion($strUserSession);
                            $objDetalleSolHist->setMotivoId($intIdMotivo);
                            $objDetalleSolHist->setObservacion($strObservacion);
                            $emComercial->persist($objDetalleSolHist);
                            $emComercial->flush();
                        }//( $objDetalleSolicitud )
                    }//( $objDetalleSolCarac )
                    
                    //Se ingresa historial de la tarea
                    if(is_object($objInfoDetalle))
                    {
                        $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();
                    }
                    $arrayParametrosHist["strObservacion"]  = $strObservacion;
                    $arrayParametrosHist["strEstadoActual"] = self::ESTADO_CANCELADA;
                    $arrayParametrosHist["strOpcion"]       = "Historial";
                    $arrayParametrosHist["strAccion"]       = "Cancelada";

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                    
                    
                    //Se ingresa seguimiento de la tarea  
                    $strObservacionSeguimiento = "Tarea fue Cancelada , Obs : ".$strObservacion;
                    
                    $arrayParametrosHist["strObservacion"]  = $strObservacionSeguimiento;
                    $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);  
                }
                
                
                if( $intIdCaso )
                {
                    $objInfoCaso = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneById($intIdCaso);
                    
                    $objInfoCasoHistorial = new InfoCasoHistorial();
                    $objInfoCasoHistorial->setCasoId($objInfoCaso);
                    $objInfoCasoHistorial->setEstado(self::ESTADO_CERRADO);
                    $objInfoCasoHistorial->setFeCreacion($datetimeActual);
                    $objInfoCasoHistorial->setIpCreacion($strIpCreacion);
                    $objInfoCasoHistorial->setUsrCreacion($strUserSession);
                    $objInfoCasoHistorial->setObservacion($strObservacion);
                    $emSoporte->persist($objInfoCasoHistorial);
                    $emSoporte->flush();
                }//( $intIdCaso )

                $objInfoComunicacion->setEstado(self::ESTADO_ELIMINADO);
                $emComunicacion->persist($objInfoComunicacion);                
                $emComunicacion->flush();

                $emComunicacion->getConnection()->commit();
                $emComercial->getConnection()->commit();
                $emSoporte->getConnection()->commit();
                
                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $intIdDetalle;
                $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
                $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

            }//(!$objInfoComunicacion)     
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComunicacion->getConnection()->rollback();
            $emSoporte->getConnection()->rollback();
        }//try

        $emComercial->getConnection()->close();
        $emComunicacion->getConnection()->close();
        $emSoporte->getConnection()->close();

        return $this->redirect($this->generateUrl('callactivity'));
    }
    
	
    /**
     * Documentacion para 'gridAction'
     * 
     * @Secure(roles="ROLE_80-8")
     * 
     * Método que elimina las actividades creadas
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 10/12/2015 - Se modifica para que al eliminar una actividad  y además se elimine la solicitud de facturación del cliente 
     *                           si la actividad iba ha ser facturada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA

     */ 
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $datetimeActual = new \DateTime('now');
        $strUserSession = $objSession->get('user');
        $strIpCreacion  = $objRequest->getClientIp();
        $intCodEmpresa  = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0);
        $strActividades = $objRequest->get('param');
        $strMensaje     = "Se eliminar(on) la(s) actividad(es) seleccionada(s) con éxito";
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');

        $intIdDepartamento   = $objSession->get('idDepartamento');
        $arrayActividades    = explode("|", $strActividades);
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $intCodEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;         
        
        
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
            
        try 
        {
            $intContador = 0;
            
            foreach($arrayActividades as $intId)
            {
                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->findOneById($intId);
                
                if( $objInfoComunicacion === null )
                {
                    if( $intContador == 0 )
                    {
                        $strMensaje .= "<br/><br/>Excepto las siguientes actividades:<br>";
                        
                        $intContador++;
                    }
                    
                    $strMensaje .= "La actividad <b>#".$intId."</b> no se encontró en nuestra base de datos.<br/>";
                }
                else
                {
                    $intIdDetalle = $objInfoComunicacion->getDetalleId() ? $objInfoComunicacion->getDetalleId() : 0;
                    $intIdCaso    = $objInfoComunicacion->getCasoId() ? $objInfoComunicacion->getCasoId() : 0;
                    
                    $strObservacion = 'Se elimina la actividad';
                    $objMotivo      = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strObservacion);
                    $intIdMotivo    = 0;
                    if( $objMotivo )
                    {
                        $intIdMotivo = $objMotivo->getId();
                    }
                    
                    if( $intIdDetalle )
                    {
                        $objInfoDetalle        = $emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);
                        $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneByDescripcionCaracteristica( self::CARACTERISTICA_SOLICITUD );
                        $objDetalleSolCarac    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                             ->findOneBy( array( 'valor'            => $intIdDetalle, 
                                                                                 'caracteristicaId' => $objAdmiCaracteristica,
                                                                                 'estado'           => self::ESTADO_ACTIVO ) );

                        if( $objDetalleSolCarac )
                        {
                            $objDetalleSolCarac->setEstado(self::ESTADO_ELIMINADO);
                            $emComercial->persist($objDetalleSolCarac);
                            $emComercial->flush();
                            
                            $objDetalleSolicitud = $objDetalleSolCarac->getDetalleSolicitudId() 
                                                   ? $objDetalleSolCarac->getDetalleSolicitudId() : null;
                            
                            if( $objDetalleSolicitud )
                            {
                                $objDetalleSolicitud->setEstado(self::ESTADO_ELIMINADO);
                                $emComercial->persist($objDetalleSolicitud);
                                $emComercial->flush();
                                
                                $objDetalleSolHist = new InfoDetalleSolHist();
                                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetalleSolHist->setEstado(self::ESTADO_ELIMINADO);
                                $objDetalleSolHist->setFeCreacion($datetimeActual);
                                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                                $objDetalleSolHist->setUsrCreacion($strUserSession);
                                $objDetalleSolHist->setMotivoId($intIdMotivo);
                                $objDetalleSolHist->setObservacion($strObservacion);
                                $emComercial->persist($objDetalleSolHist);
                                $emComercial->flush();
                            }
                        }

                        
                        //Se ingresa historial de la tarea
                        if(is_object($objInfoDetalle))
                        {
                            $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();
                        }
                        $arrayParametrosHist["strObservacion"]  = $strObservacion;
                        $arrayParametrosHist["strEstadoActual"] = self::ESTADO_CANCELADA;
                        $arrayParametrosHist["strOpcion"]       = "Historial";
                        $arrayParametrosHist["strAccion"]       = "Cancelada";

                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                    

                        //Se ingresa seguimiento de la tarea  
                        $strObservacionSeguimiento = "Tarea fue Cancelada , Obs : ".$strObservacion;

                        $arrayParametrosHist["strObservacion"]  = $strObservacionSeguimiento;
                        $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    }
                    
                    if( $intIdCaso )
                    {
                        $objInfoCaso = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneById($intIdCaso);

                        $objInfoCasoHistorial = new InfoCasoHistorial();
                        $objInfoCasoHistorial->setCasoId($objInfoCaso);
                        $objInfoCasoHistorial->setEstado(self::ESTADO_CERRADO);
                        $objInfoCasoHistorial->setFeCreacion($datetimeActual);
                        $objInfoCasoHistorial->setIpCreacion($strIpCreacion);
                        $objInfoCasoHistorial->setUsrCreacion($strUserSession);
                        $objInfoCasoHistorial->setObservacion($strObservacion);
                        $emSoporte->persist($objInfoCasoHistorial);
                        $emSoporte->flush();
                    }//( $intIdCaso )
                    
                    $objInfoComunicacion->setEstado(self::ESTADO_ELIMINADO);
                    $emComunicacion->persist($objInfoComunicacion);                
                    $emComunicacion->flush();
                    
                }//( $objInfoComunicacion === null )
            }//foreach($arrayActividades as $intId)
            
            $emComunicacion->getConnection()->commit();
            $emComercial->getConnection()->commit();      
            $emSoporte->getConnection()->commit();                    

            //Proceso que graba tarea en INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = $intIdDetalle;
            $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
            $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComunicacion->getConnection()->rollback();
            $emSoporte->getConnection()->rollback();
        }//try

        $emComercial->getConnection()->close();
        $emComunicacion->getConnection()->close();
        $emSoporte->getConnection()->close();
        
        
        return $respuesta->setContent($strMensaje);		
    }
	
    
    
    /**
     * Documentacion para 'gridAction'
     * 
     * @Secure(roles="ROLE_80-7")
     * 
     * Método que retorna el listado de las actividades ingresadas por los usuarios
     * 
     * @version 1.0 Version Inicial
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite cargar las llamadas del dia y del area
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 06-05-2016 - Se modifica para que retorne las actividades que están en estado 'Activo', cuando no se selecciona el combo
     *                           estado.    
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.5 11-01-2021 Se agrega validación con perfil para el botón de finalizar tarea.
     */ 
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $idDepartamento = "";
        $peticion = $this->get('request');
        $session = $peticion->getSession();
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
		        
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
				
        $parametros                     = array();
        $parametros['mensaje']          = $peticion->query->get('mensaje') ? $peticion->query->get('mensaje') : "";
        $parametros['tipo_genera']      = $peticion->query->get('tipo_genera') ? $peticion->query->get('tipo_genera') : "N";
        $parametros['idClaseDocumento'] = $peticion->query->get('idClaseDocumento') ? $peticion->query->get('idClaseDocumento') : "";
        $parametros['estado']           = $peticion->query->get('estado') ? $peticion->query->get('estado') : 'Activo';
        $parametros['asignado']         = $peticion->query->get('asignado');                
        $parametros['actividad']        = $peticion->query->get('numeroActividad');   
		
        $varSessionCliente      = ($session->get('cliente') ? $session->get('cliente') : "");
        $varSessionPtoCliente   = ($session->get('ptoCliente') ? $session->get('ptoCliente') : "");
        $nombreClienteAfectado  = ($varSessionCliente ? ($varSessionCliente['razon_social'] ? $varSessionCliente['razon_social'] : $varSessionCliente['nombres'] . " " . $varSessionCliente['apellidos']) : "");  
        $loginPuntoCliente      = ($varSessionPtoCliente ? ($varSessionPtoCliente['login'] ? $varSessionPtoCliente['login'] : "") : ""); 
		$parametros['login']    = ($loginPuntoCliente ? $loginPuntoCliente : $peticion->query->get('login'));                                
		
        $feDesde               = explode('T',$peticion->query->get('feDesde'));
        $feHasta               = explode('T',$peticion->query->get('feHasta'));
        $parametros['feDesde'] = $feDesde ? $feDesde[0] : 0 ;
        $parametros['feHasta'] = $feHasta ? $feHasta[0] : 0 ;
        $empresaCod            = $session->get('idEmpresa');
        $parametros['empresa'] = $empresaCod;

        if($parametros["idPunto"] == "" && $parametros["login"] == "" && $parametros["idClaseDocumento"] == "" && $parametros["tipo_genera"] == "N"
           && $parametros["estado"] == "Todos" && $parametros["feDesde"] == "" && $parametros["feHasta"] == "" && $parametros["asignado"] == ""
           && $parametros["actividad"] == "")
        {
            $idDepartamento = $session->get('idDepartamento');
        }   

        $parametros['departamento'] = $idDepartamento;
        $parametros['boolPerfilFinalizarTarea'] = $this->get('security.context')->isGranted('ROLE_80-38');

        $objJson = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                  ->generarJsonComunicaciones($parametros, $start, $limit, $emSoporte);
        $respuesta->setContent($objJson);

        return $respuesta;
    }


     /**
     * getSubtareasAction
     *
     * Metodo que retorna las subtareas de una tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 22-07-2016
     *
     */
    public function getSubtareasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');

        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');

        $comunicacionId = $peticion->query->get('comunicacionId') ? $peticion->query->get('comunicacionId') : "";

        $objJson = $emSoporte->getRepository('schemaBundle:InfoDetalle')->generarJsonSubtareas($comunicacionId,$emComunicacion);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    public function getEmpleadosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('query');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:VistaEmpleados')
            ->generarJsonEntidades($nombre,'Todos','','');
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_80-41")
    */ 
    public function getClientesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPunto')
            ->generarJsonClientes($nombre);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para exportar en excel la informacion de actividades realizadas dado filtros especificos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 24-08-2015 ( Se corrige obtencion de filtros  y se adapta metodo para que funcione con los cambios
     *                           realizados a nivel de repositorio en la mejora del query base )
     * 
     * @version 1.0 Version Inicial
     *              
     * @Secure(roles="ROLE_80-37")
    */ 
    public function exportarConsultaAction()
    {
    
        $peticion = $this->get('request');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $parametros = array();
              
        $parametros['tipo_general'] = $peticion->get('hid_tipoGenera');          
        $parametros['idClaseDocumento'] = $peticion->get('hid_claseDocumento');  
        
        $parametros['asignado']  = $peticion->get('hid_asignado');  
        $parametros['actividad'] = $peticion->get('hid_actividad');  
              
        $parametros['feDesde'] = $peticion->get('hid_feDesde') ? date_format(date_create($peticion->get('hid_feDesde')), 'Y-m-d') : 0 ;
        $parametros['feHasta'] = $peticion->get('hid_feHsta') ? date_format(date_create($peticion->get('hid_feHasta')), 'Y-m-d') : 0 ;                                
        
        $session = $peticion->getSession();
        
        $parametros['empresa'] = $session->get('idEmpresa');          
                
        $varSessionPtoCliente = ($session->get('ptoCliente') ? $session->get('ptoCliente') : null);	
        $loginPuntoCliente = ($varSessionPtoCliente ? ($varSessionPtoCliente['login'] ? $varSessionPtoCliente['login'] : null) : null); 
		        
        $parametros['login'] = ($loginPuntoCliente ? $loginPuntoCliente : $peticion->get('hid_login'));
                     
        //Se encvia string 'data' para que obtenga solo la informacion requerida completa para la exportacion
        $resultado = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->getComunicaciones($parametros,'','','data');
                        
        $llamadas = null;

        if(isset($resultado))
        {            
            $llamadas = $resultado;
        }

        $this->generateExcelConsulta($llamadas, $emComunicacion, $parametros, $peticion->getSession()->get('user'));
    }

    public static function generateExcelConsulta($llamadas,$em,$parametros,$usuario)
    {
        
	error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaLlamadas.xls");
       
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Llamadas");
        $objPHPExcel->getProperties()->setSubject("Consulta de Llamadas");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de llamadas.");
        $objPHPExcel->getProperties()->setKeywords("Llamadas");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['tipo']=="")?'Todos': $parametros['nombre']);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9',''.($parametros['feDesde']=="")?'Todos': $parametros['feDesde']);
        $objPHPExcel->getActiveSheet()->setCellValue('F9',''.($parametros['feHasta']=="")?'Todos': $parametros['feHasta']);
        
        $i=14;
        foreach ($llamadas as $llamada):        		    

            $documentoComunicacion = $em->getRepository('schemaBundle:InfoDocumentoComunicacion')->findByComunicacionId($llamada['idComunicacion']);
            $documento = $em->getRepository('schemaBundle:InfoDocumento')->find($documentoComunicacion[0]->getDocumentoId()->getId());

	    $claseDocumento = $documento->getClaseDocumentoId();
	    $tipo = ($claseDocumento ? ($claseDocumento->getNombreClaseDocumento() ? $claseDocumento->getNombreClaseDocumento() : "") : "");
			
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $llamada['idComunicacion']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $tipo);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $llamada['remitenteNombre']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $documento->getMensaje());
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, ($llamada['fechaComunicacion'])?date_format($llamada['fechaComunicacion'],'Y-m-d').' '.date_format($llamada['fechaComunicacion'],'G:i'):"");
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $llamada['estado']);

            $i=$i+1;
        endforeach;
//        Util::addBorderThinB($objPHPExcel,'A'.($i-1).':I'.($i-1));
        // Merge cells
        // Set document security
        $objPHPExcel->getSecurity()->setWorkbookPassword("PHPExcel");
        $objPHPExcel->getSecurity()->setLockWindows(true);
        $objPHPExcel->getSecurity()->setLockStructure(true);

        // Set sheet security
        //$objPHPExcel->getActiveSheet()->getProtection()->setPassword('PHPExcel');
        $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
        $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);

        // Set page orientation and size
        //$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Llamadas_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
    
    /*
     * Método que obtiene Login de los clientes
     *
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.1
     * @since 25-04-2019
     *
     * @return Object $objResponse
     */
    public function getLoginClientesAction() 
    {
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $intIdEmpresa  = $objSession->get('idEmpresa')?$objSession->get('idEmpresa'):'';
        $intIdPersona  = $objRequest->query->get("idPersona")?$objRequest->query->get("idPersona"):'';
        $strLogin      = $objRequest->query->get("query")?$objRequest->query->get("query"):'';
        $em            = $this->getDoctrine('doctrine')->getManager('telconet_financiero');        
        
        $arrayLoginCliente = $em->getRepository('schemaBundle:InfoPunto')
                                ->loginClientes(array('strRol'      => 'Cliente',
                                                      'intIdPersona'=> $intIdPersona,
                                                      'strLogin'    => $strLogin,
                                                      'intIdEmpresa'=> $intIdEmpresa,
                                                      'strEstadoPer'=> 'Activo',
                                                      'strEstadoPto'=> 'Activo' ));

        $objResponse = new JsonResponse();
        $objResponse->setData($arrayLoginCliente);  

        return $objResponse;
    }
    
    

    
    

    public function getNombreClientesConLoginActivosAction()
    {
        
        
        
        $request = $this->getRequest();
	$session= $request->getSession();
	$idEmpresa = $session->get('idEmpresa');
	$em=$this->getDoctrine('doctrine')->getManager('telconet_financiero');   
        
         
        $completarNombre =  $_GET["query"];
         
                
        $resultado = $em->getRepository('schemaBundle:InfoPunto')->nombreClientesConLogins('',$idEmpresa,$completarNombre); 
            
        
        $datos = $resultado['registros'];
         $total = $resultado['total'];
        
         
         foreach ($datos as $dato){
             $cliente="";
             
             
             if(!$dato['nombres'] && !$dato['apellidos'] ){
                 $cliente=$dato['razonSocial']; 
             }else{
               $cliente= $dato['nombres']." ".$dato['apellidos']; 
             }
             
             //$cliente= $dato['nombres']." ".$dato['apellidos']; 
              $arreglo[] = array(
                'id' => $dato['id'],
                'cliente' => $cliente,
                'identificacionCliente' => $dato['identificacionCliente']
                
                
               
                
            );
         }
         
         if (!empty($datos))
            $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
        else {
             $arreglo[] = array(
                 'id' => -1,
                'cliente' => "No exiten datos",
                'identificacionCliente' =>"00000000"
             );
            
             $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
        }
        
           $response->headers->set('Content-type', 'text/json');
         
           return $response;
    }
    
    
    
 public function getEmpleadosXDepartamentoAction()
 {
      
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
                
        $id_oficina = $peticion->query->get('id_oficina');
        $id_departamento = $peticion->query->get('id_param');                         
        
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");  
        
	$soloJefes=false;
	$cantones_unique = array();	
	$nombre="";
	
	$em = $this->getDoctrine()->getManager("telconet");
        $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";                
	
	if($paramEmpresa!=""){
	      
	      $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
	      if($empresa)$codEmpresa = $empresa->getId();
	}
	
         //$em = $this->get('doctrine')->getManager('telconet');
                     
         $resultado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    //->generarTodosEmpleadosXDepartamento($id_departamento, $id_oficina, $nombre, $soloJefes, true, $codEmpresa, $cantones_unique);
		    ->generarJsonEmpleadosXDepartamento($id_departamento, $id_oficina, $nombre, $soloJefes, true, $codEmpresa, $cantones_unique);
	
	if(isset($resultado)){
	 $datos = $resultado['registros'];  // esto se hace solo si resultado es un array
         
         $total = $resultado['total'];  
	}else{
	 $datos = '[]';  // esto se hace solo si resultado es un array
         
         $total = 0;  
	}
          
        
        
         
          foreach ($datos as $dato){
              // /*echo*/($dato['idPersona']); die();
                $arreglo[] = array(
                'id_empleado' => $dato['idPersona'],
                'nombre_empleado'  =>ucwords(strtolower(trim($dato["nombres"].' '.$dato["apellidos"]))),
                
               );
             
         }
         
         if (!empty($datos))
            $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
        else {
             $arreglo[] = array();
            
             $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
        }
        
           $response->headers->set('Content-type', 'text/json');
       
        
        return $response;
               
               
              
    }
    
    public function ponerSesionPuntoAction(){
         $respuesta = new Response();
         $respuesta->headers->set('Content-Type', 'text/json');
         
          $request = $this->getRequest();
          
           $session = $request->getSession();
         //  $idPunto=$request->get("idLogin");// asi es para post
           $idPunto=$request->query->get('idLogin'); // asi es para get
           
           if(isset($idPunto) && $idPunto!='' && (!empty($idPunto))){
                $em=$this->getDoctrine('doctrine')->getManager('telconet'); 
                 $em->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($idPunto,$session); 
                 
                 $response = new Response(json_encode(array('sucess' => true, 'message' => 'Se agrego el cliente en sesion')));
           }else{
               $response = new Response(json_encode(array('sucess' => false, 'message' => 'No se agrego el cliente en sesion')));
           }
         
          return $response;
          
     }
         
    /**
     * Documentación para el método 'validaAplicaNotaCreditoAction'.
     *
     * Función que valida si aplica o no aplica, la Nota de Crédito para el proceso de reubicación.
     *
     * @return Object $objResponse.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 23-10-2020
     */   
    public function validaAplicaNotaCreditoAction()
    {       
        $objSession      = $this->get('request')->getSession();
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $emFinanciero    = $this->getDoctrine()->getManager('telconet_financiero');
        $emComunicacion  = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");  
        $emSoporte       = $this->getDoctrine()->getManager('telconet_soporte');
        
        try
        {
            $strNombreParamCab   = "PROCESO_REUBICACION";
            $strNombreDetMesServ = "MESES_SERVICIO_ACTIVO";
            $strNombreDetDiasLab = "DIAS_HABILES TAREA_REUBICACION";
                        
            $objPuntoCliente = $objSession->get('ptoCliente');
            $intIdPunto      = $objPuntoCliente['id'];
            $intIdTareaReub  = $this->get('request')->get('intIdTareaReub');
            $strCodEmpresa   = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");

            $arrayServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->getServicioPreferenciaByPunto(['intIdPunto' => $intIdPunto]);
            $intIdServicioInternet = $arrayServicio[0]['ID_SERVICIO'];

            $intMesesActivo  = str_pad(' ', 30);
            $strSqlMesesAct  = "BEGIN :intMesesActivo := DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.F_GET_MESES_ACTIVO(:Fn_IdServicio); END;";
            $objStmtMesesAct = $emFinanciero->getConnection()->prepare($strSqlMesesAct);
            $objStmtMesesAct->bindParam('Fn_IdServicio', $intIdServicioInternet);
            $objStmtMesesAct->bindParam('intMesesActivo', $intMesesActivo);
            $objStmtMesesAct->execute();

            $boolAplicaDiasLab = "N";
            $objTareaReub      = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($intIdTareaReub);
            $intDetalleId      = is_object($objTareaReub) ? $objTareaReub->getDetalleId() : 0;
            $strEstadoTarea    = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($intDetalleId);	
                        
            if($objTareaReub && $strEstadoTarea != 'Eliminada')
            {
                $objFechaActual     = new \DateTime('now');
                $objFechaTarea      = $objTareaReub->getFechaComunicacion();
                $objDiffFecha       = $objFechaActual->diff($objFechaTarea);
                $intDiasTareaCreada = $objDiffFecha->days;
                
                $arrayParamDiasLab   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->get($strNombreParamCab, 'FINANCIERO', '', $strNombreDetDiasLab, '', '', '', '', '', $strCodEmpresa, '');
                
                $boolAplicaDiasLab  = ($intDiasTareaCreada > $arrayParamDiasLab[0]['valor1']) ? "S" : "N";
            }

            $arrayParamMesesServ = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get($strNombreParamCab, 'FINANCIERO', '', $strNombreDetMesServ, '', '', '', '', '', $strCodEmpresa, '');

            $strAplicaNc  = (($intMesesActivo >= $arrayParamMesesServ[0]['valor1']) || $boolAplicaDiasLab == "S") ? "S" : "N";

            $objResponse = new Response(json_encode(array('strAplicaNc' => $strAplicaNc)));
            $objResponse->headers->set('Content-type', 'text/json');
            
            return $objResponse;
        }
        catch(\Exception $e)
        {
            $strAplicaNc = "N";

            $objResponse = new Response(json_encode(array('strAplicaNc' => $strAplicaNc)));
            $objResponse->headers->set('Content-type', 'text/json');
            
            return $objResponse;
        }
    }

    /**
     * Documentación para el método 'getPorcentajesNcAction'.
     *
     * Función que obtiene los porcentajes parametrizados para la Nota de Crédito en el proceso de reubicación.
     *
     * @return Object $objResponse.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 24-10-2020 
     */
    public function getPorcentajesNcAction()
    {   
        $objRequest         = $this->getRequest();
        $strParametroCab    = $objRequest->get('strParametroCab');
        $strDescripcionDet  = $objRequest->get('strDescripcionDet');
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        
        try
        {
            $arrayParamPorcentajeNc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->get($strParametroCab, 'FINANCIERO', '', $strDescripcionDet, '', '', '', '', '', $strEmpresaCod, '');

            $arrayPorcentaje = explode('|', $arrayParamPorcentajeNc[0]['valor1']);
            $arrayResultado = array();

            foreach($arrayPorcentaje as $intPorcentaje)
            {
                $arrayResultado[] = array('id' => $intPorcentaje, 'valor' => $intPorcentaje);
            }
            sort($arrayResultado);
            
            $objResponse = new Response(json_encode(array('porcentajesNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
        catch(\Exception $e)
        {
            $arrayResultado = array();
            $objResponse    = new Response(json_encode(array('porcentajesNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
    }
    
    
    /**
     * Documentación para el método 'getMotivosNcAction'.
     *
     * Función que obtiene los motivos parametrizados para la Nota de Crédito en el proceso de reubicación.
     *
     * @return Object $objResponse.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 24-10-2020
     */
    public function getMotivosNcAction()
    {   
        $objRequest         = $this->getRequest();
        $strParametroCab    = $objRequest->get('strParametroCab');
        $strDescripcionDet  = $objRequest->get('strDescripcionDet');
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        
        try
        {
            $arrayParamMotivosNc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->get($strParametroCab, 'FINANCIERO', '', $strDescripcionDet, '', '', '', '', '', $strEmpresaCod, '');

            $arrayResultado = array();

            foreach($arrayParamMotivosNc as $arrayMotivoNc)
            {
                $arrayResultado[] = array('id' => $arrayMotivoNc['id'], 'valor' => $arrayMotivoNc['valor1']);
            }
            
            $objResponse = new Response(json_encode(array('motivosNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
        catch(\Exception $e)
        {
            $arrayResultado = array();
            $objResponse    = new Response(json_encode(array('porcentajesNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
    }
   
     
    /**
     * Documentación para el método 'getPersonalAutorizadoNcAction'. 
     *
     * Función que obtiene el personal autorizado parametrizado para la Nota de Crédito en el proceso de reubicación.
     *
     * @return Object $objResponse.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 24-10-2020
     */
    public function getPersonalAutorizadoNcAction()
    {   
        $objRequest         = $this->getRequest();
        $strParametroCab    = $objRequest->get('strParametroCab');
        $strDescripcionDet  = $objRequest->get('strDescripcionDet');
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $serviceSoporte     = $this->get('soporte.SoporteService');
        
        try
        {
            $arrayParamPersonalAut = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParametroCab, 'FINANCIERO', '', $strDescripcionDet, '', '', '', '', '', $strEmpresaCod, '');

            $arrayResultado = array();

            foreach($arrayParamPersonalAut as $arraySqlPerAutNc)
            {
                $arrayResultado = array_merge($arrayResultado, 
                                                  $serviceSoporte->obtienePersonalAutNc(array('strSqlPerAutNc' => $arraySqlPerAutNc['valor1'])));
            }

            $objResponse = new Response(json_encode(array('personalAutorizadoNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
        catch(\Exception $e)
        {
            $arrayResultado = array();
            $objResponse = new Response(json_encode(array('personalAutorizadoNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
    }

    /**
     * Documentación para el método 'ejecutarNcReubicacionAction'.
     *
     * Función que crea la Solicitud de Nota de Crédito o crea la Nota de Crédito si tiene una factura activa de reubicación.
     * Adicional se crea tarea de registro interno con cierre inmediato.
     *
     * @return Object $objResponse.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 24-10-2020
     */
    public function ejecutarNcReubicacionAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $intIdTareaReub    = $objRequest->get('intIdTareaReub');
        $strCumpleReqNc    = $objRequest->get('strCumpleReqNc');
        $strMotivoNc       = $objRequest->get('strMotivoNc');
        $arrayCaracNc      = $objRequest->get('arrayCaracNc');
        $objPuntoCliente   = $objSession->get('ptoCliente');
        $intIdPunto        = $objPuntoCliente['id'];
        $strEmpresaCod     = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");
        $serviceSoporte    = $this->get('soporte.SoporteService');
        $strUsrCreaReub    = "telcos_reubica";
        
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        $emComercial   = $this->getDoctrine()->getManager('telconet');
        $emFinanciero  = $this->getDoctrine()->getManager('telconet_financiero');
        
        $emComercial->getConnection()->beginTransaction();
        $emFinanciero->getConnection()->beginTransaction();

        try
        {
            //Se obtiene el servicio mandatorio (Servicio de Internet).    
            $arrayServicioInt= $emComercial->getRepository('schemaBundle:InfoServicio')->getServicioPreferenciaByPunto(['intIdPunto' => $intIdPunto]);
            $objServicioInt  = $emComercial->getRepository("schemaBundle:InfoServicio")->find($arrayServicioInt[0]['ID_SERVICIO']);

            //Se obtiene el tipo de solicitud para facturar por reubicación.
            $objTipoSolReub = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                          ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD NOTA CREDITO POR REUBICACION',
                                                            'estado' => "Activo"));

            //Se obtiene  la característica  'NUMERO_TAREA_REUB', para enlazarla con la solicitud de la NC.
            $objCaractTareaReub = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                              ->findOneBy(array('descripcionCaracteristica' => "NUMERO_TAREA_REUBICACION",
                                                                'estado' => "Activo"));
            
            //Se obtiene  la característica  'SOLICITUD FACTURA POR REUBICACION', para enlazarla con la solicitud de la NC.
            $objCaractFactReub = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                           ->findOneBy(array('descripcionCaracteristica' => "SOLICITUD_FACT_REUBICACION",
                                                             'estado' => "Activo"));

            if(is_object($objServicioInt) && is_object($objTipoSolReub) && is_object($objCaractTareaReub) && is_object($objCaractFactReub))
            {
                //Se obtiene  el id de 'SOLICITUD FACTURA POR REUBICACION'.      
                $arrayParamSolCaractFactReub = array('strDescCaracteristica' => 'NUMERO_TAREA_REUBICACION',
                                                     'strEstado'             => 'Activo',
                                                     'intIdTarea'            => $intIdTareaReub);
                $arraySolCaractFactReub = $serviceSoporte->obtieneSolCaractFactReub($arrayParamSolCaractFactReub);
            
                //Se obtiene el id Motivo para la solicitud de NC.           
                $objMotivoNc = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findOneBy(array('nombreMotivo' => $strMotivoNc));
                
                if (!is_object($objMotivoNc))
                {
                    $arrayParamMotivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PROCESO_REUBICACION','FINANCIERO','','MOTIVO_SOLICITUD_FACT','','','','','',$strEmpresaCod,'');
                    
                    $objMotivoNc = $emGeneral->getRepository("schemaBundle:AdmiMotivo")
                                             ->findOneBy(array('nombreMotivo' => $arrayParamMotivo[0]['valor1']));
                    
                    // Se agrega característica MOTIVO_NC_REUBICACION, cuando cumple requisitos de Nota de Crédito.
                    if ($objMotivoNc->getId() != null)
                    {
                        $arrayCaracNc['MOTIVO_NC_REUBICACION'] = $objMotivoNc->getNombreMotivo();
                    }
                    
                }
                $intIdMotivoNc = is_object($objMotivoNc) ? $objMotivoNc->getId() : null;

                $strDescSolNcReub  = "Se crea Solicitud de Nota de Crédito por Reubicación según tarea No. ".$intIdTareaReub;
                
                
                //GUARDAR INFO DETALLE SOLICICITUD - Solicitud de Nota de Credito
                $objSolNcReub = new InfoDetalleSolicitud();
                $objSolNcReub->setServicioId($objServicioInt);
                $objSolNcReub->setTipoSolicitudId($objTipoSolReub);
                $objSolNcReub->setMotivoId($intIdMotivoNc);
                $objSolNcReub->setEstado("Pendiente");
                $objSolNcReub->setUsrCreacion($strUsrCreaReub);
                $objSolNcReub->setFeCreacion(new \DateTime('now'));
                $objSolNcReub->setObservacion($strDescSolNcReub);
                $emComercial->persist($objSolNcReub);

                //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                $objDetSolNcReubHist = new InfoDetalleSolHist();
                $objDetSolNcReubHist->setDetalleSolicitudId($objSolNcReub);
                $objDetSolNcReubHist->setObservacion($strDescSolNcReub);
                $objDetSolNcReubHist->setIpCreacion($objRequest->getClientIp());
                $objDetSolNcReubHist->setFeCreacion(new \DateTime('now'));
                $objDetSolNcReubHist->setUsrCreacion($strUsrCreaReub);
                $objDetSolNcReubHist->setEstado("Pendiente");
                $emComercial->persist($objDetSolNcReubHist);
                
                // Se agregan características NUMERO_TAREA_REUBICACION y SOLICITUD_FACT_REUBICACION al arreglo de características de la solicitud.
                $arrayCaracNc['NUMERO_TAREA_REUBICACION']   = $intIdTareaReub;
                $arrayCaracNc['SOLICITUD_FACT_REUBICACION'] = $arraySolCaractFactReub[0]['intIdSolFact'];

                foreach ($arrayCaracNc as $strClaveCaracNc => $strValorCaracNc) 
                {
                    $objCaractSolNc = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                               ->findOneBy(array('descripcionCaracteristica' => $strClaveCaracNc,'estado' => "Activo"));
                    
                    if (is_object($objCaractSolNc))
                    {
                        $objDetSolCaractNcReub = new InfoDetalleSolCaract();
                        $objDetSolCaractNcReub->setCaracteristicaId($objCaractSolNc);
                        $objDetSolCaractNcReub->setDetalleSolicitudId($objSolNcReub);
                        $objDetSolCaractNcReub->setEstado('Activo');
                        $objDetSolCaractNcReub->setFeCreacion(new \DateTime('now'));
                        $objDetSolCaractNcReub->setUsrCreacion($strUsrCreaReub);
                        $objDetSolCaractNcReub->setValor($strValorCaracNc);
                        $emComercial->persist($objDetSolCaractNcReub); 
                    }                          
                }
                
                // Se valida si existe una factura activa ligada a la solicitud de Reubicación, para crear inmediatamente la NC       
                $arrayParamFactReub = array('intIdTarea' => $intIdTareaReub,
                                            'intIdPunto' => $intIdPunto);
                $arrayFactSinNcReub = $serviceSoporte->obtieneFactSinNcReub($arrayParamFactReub);
            
                if(!empty($arrayFactSinNcReub))
                {
                  
                    $objTipoDocNc       = $emFinanciero->getRepository("schemaBundle:AdmiTipoDocumentoFinanciero")
                                                   ->findOneBy(array('codigoTipoDocumento' => 'NC','estado' => 'Activo'));
                    
                    $intIdTipoDocumento = is_object($objTipoDocNc) ? $objTipoDocNc->getId() : null;
                    
                    $objFacturaReub = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                  ->find($arrayFactSinNcReub[0]['intIdDocumento']);
                    
                    $intIdOficina           = is_object($objFacturaReub) ? $objFacturaReub->getOficinaId() : null;
                    $strValorOriginal       = "Y";
                    $strPorcentajeServicio  = "N";
                    $strProporcionalPorDias = "N";
                    $intPorcentaje          = 100;
                    
                    if($strCumpleReqNc == "N" && !empty($arrayCaracNc))
                    {
                        foreach ($arrayCaracNc as $strClaveCaracNc => $strValorCaracNc) 
                        {
                            if($strClaveCaracNc == 'PORCENTAJE_NC_REUBICACION')
                            {
                                $intPorcentaje          = $strValorCaracNc;
                                $strValorOriginal       = "N";
                                $strPorcentajeServicio  = "Y";
                                $strProporcionalPorDias = "N";
                            }
                        }    
                    }

                    // Se invoca a la función creaNotaCredito
                    $arrayParametrosNc = array('intIdDocumento'        => $arrayFactSinNcReub[0]['intIdDocumento'], 
                                               'intTipoDocumentoId'    => $intIdTipoDocumento,
                                               'intIdMotivo'           => $intIdMotivoNc,
                                               'strValorOriginal'      => $strValorOriginal,
                                               'strPorcentajeServicio' => $strPorcentajeServicio,
                                               'intPorcentaje'         => $intPorcentaje,
                                               'intIdOficina'          => $intIdOficina, 
                                               'intIdEmpresa'          => $strEmpresaCod);

                    $arrayResultNc = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->creaNotaCredito($arrayParametrosNc);

                    if ($arrayResultNc['strMessageError'] != "" || $arrayResultNc['strMessageError'] != null)
                    {
                        throw new \Exception("Ocurrió un error al crear Nota de Crédito, a factura asociada que se encuentra activa.");
                    }
                    
                    //Se actualiza el estado de la solicitud de Nota de Credito a Finalizada
                    $objSolNcReub->setEstado("Finalizada");
                    $emComercial->persist($objSolNcReub);

                    //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                    $objDetSolNcReubHistFin = new InfoDetalleSolHist();
                    $objDetSolNcReubHistFin->setDetalleSolicitudId($objSolNcReub);
                    $objDetSolNcReubHistFin->setObservacion('Se finaliza la solicitud');
                    $objDetSolNcReubHistFin->setIpCreacion($objRequest->getClientIp());
                    $objDetSolNcReubHistFin->setFeCreacion(new \DateTime('now'));
                    $objDetSolNcReubHistFin->setUsrCreacion($strUsrCreaReub);
                    $objDetSolNcReubHistFin->setEstado("Finalizada");
                    $emComercial->persist($objDetSolNcReubHistFin);
                    
                    // Se agregan características al arreglo de características de la NC.                          
                    $entityInfoDocumentoFinCab = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                              ->find($arrayResultNc['intIdDocumentoNC']);

                    foreach ($arrayCaracNc as $strClaveCaracNc => $strValorCaracNc) 
                    {
                        $objCaractNc = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                   ->findOneBy(array('descripcionCaracteristica' => $strClaveCaracNc,'estado' => "Activo"));
                        
                        if (is_object($objCaractNc))
                        {
                            $objInfoDocCaract = new InfoDocumentoCaracteristica();
                            $objInfoDocCaract->setCaracteristicaId($objCaractNc->getId());
                            $objInfoDocCaract->setDocumentoId($entityInfoDocumentoFinCab);
                            $objInfoDocCaract->setEstado('Activo');
                            $objInfoDocCaract->setFeCreacion(new \DateTime('now'));
                            $objInfoDocCaract->setIpCreacion($objRequest->getClientIp());
                            $objInfoDocCaract->setUsrCreacion($strUsrCreaReub);
                            $objInfoDocCaract->setValor($strValorCaracNc);
                            $emFinanciero->persist($objInfoDocCaract);
                        }
   
                    }
                                   
                    // Se realiza la numeración de la nota crédito
                    $arrayParamNumeraNc = array('intIdDocumento'    => $arrayResultNc['intIdDocumentoNC'],
                                                'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                'strObsHistorial'   => 'La nota nota de credito fue creada con exito',
                                                'strUsrCreacion'    => $strUsrCreaReub);
                    
                    $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->numeraNotaCredito($arrayParamNumeraNc);
       
                }//if(!empty($arrayFactSinNcReub))
                 
                 //Se crea tarea de registro interno con cierre inmediato
                 $strParamCabTareaAut = 'PROCESO_REUBICACION';
                 $arrayParamTareaAut  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->get($strParamCabTareaAut, 'FINANCIERO', '', 'TAREA_AUTOMATICA_REUBICACION',
                                                        '', '', '', '', '', $strEmpresaCod, '');

                $serviceSoporte->crearTareaCasoSoporte(
                    array (
                            "intIdPersonaEmpresaRol" => $arrayParamTareaAut[0]['valor1'],
                            "intIdEmpresa"           => $strEmpresaCod,
                            "strPrefijoEmpresa"      => $strPrefijoEmpresa,
                            "strNombreTarea"         => $arrayParamTareaAut[0]['valor2'],
                            "strNombreProceso"       => $arrayParamTareaAut[0]['valor3'],
                            "strUserCreacion"        => $objSession->get('user'),
                            "strIpCreacion"          => $objRequest->getClientIp(),
                            "strObservacionTarea"    => $arrayParamTareaAut[0]['valor4'].$intIdTareaReub,
                            "strUsuarioAsigna"       => $arrayParamTareaAut[0]['valor5'],
                            "strTipoAsignacion"      => $arrayParamTareaAut[0]['valor6'],
                            "strTipoTarea"           => "T",
                            "strTareaRapida"         => "S",
                            "boolAsignarTarea"       => true,
                            "intPuntoId"             => $intIdPunto,
                            "strFechaHoraSolicitada" => null 
                    ));
                
                $emComercial->flush();
                $emFinanciero->flush();
                
            }
            
            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();
            
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();

            $strMensaje = 'La emisión de solicitud de Nota de Crédito se ejecutó con éxito.';
            $strStatus  = 'OK';
            
            $objResponse = new Response(json_encode(array('strStatus'=>$strStatus, 'strMensaje'=>$strMensaje)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();

            $strMensaje = "Ocurrió un error al crear solicitud de Nota de Crédito, comúniquese con el Departamento de Sistemas.";
            $strStatus  = 'ERROR';
            
            $objResponse = new Response(json_encode(array('strStatus'=>$strStatus, 'strMensaje'=>$strMensaje)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
    }

    /**
    * getProcesosAction
    *
    * Funcion que retorna la lista de procesos visibles configurados para una empresa
    *
    * @return json con las empresas
    *
    * @version 1.0 Version Inicial
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 18-03-2016 Se realiza un ajuste para que el combo de procesos que se encuentra en la opcion de nueva llamada pueda buscar.
    *
    * @Secure(roles="ROLE_80-544")
    */
    public function getProcesosAction(){
       
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_comercial = $this->getDoctrine()->getManager("telconet");
        
        $request = $this->getRequest();
          
        $session = $request->getSession();
        $prefijoEmpresa    = $request->get('prefijoEmpresa') ? $request->get('prefijoEmpresa') : "";
        
        if($prefijoEmpresa != "" && $prefijoEmpresa != "N/A")
        {
            $emEmpresa  = $em_comercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo' => $prefijoEmpresa));
            $empresaCod = $emEmpresa->getId();
        }
        else
        {
            $empresaCod = $session->get('idEmpresa');
        }
        
        $strNombreProceso = $request->query->get('query') ? $request->query->get('query') : "";
        //$admi_procesos= $em_soporte->getRepository('schemaBundle:AdmiProceso')->findBy(array('estado'=> 'Activo'));
        //$parametros, $nombre,$estado,$start,$limit,$empresaCod,$esVisible
        $admi_procesos= $em_soporte->getRepository('schemaBundle:AdmiProceso')->getRegistros(null,$strNombreProceso,"Activo","","",$empresaCod,'SI');
        
        foreach ($admi_procesos as $procesos){
            $arreglo[] = array(
                'id'=> $procesos->getId(),
                'nombreProceso'=>$procesos->getNombreProceso()
            );
        }
        /// debo retornar la repsuesta
        if(isset($arreglo)){/// 
            if(!empty($arreglo)){// si  no esta vacio
                $total=count($arreglo);
                $respuesta=new Response(json_encode(array('total'=>$total, 'registros'=>$arreglo))); 
                
            }else{
              $arreglo=array();
              $respuesta=new Response(json_encode(array('total'=>0, 'registros'=>$arreglo))); 
            }
       }else{
           $arreglo=array();
           $respuesta=new Response(json_encode(array('total'=>0, 'registros'=>$arreglo)));
       }
       return $respuesta;
    
    }
    
    /**
     *
     * Funcion que trae el listado de puntos de atención.
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 28-05-2021
     *  
     * @return array $objResponse
     * 
     */
    public function ajaxGetComboPuntoAtencionAction()
    {  
        $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objResponse         = new JsonResponse();
        $arrayPuntosAtencion = array();
        
        $strEmpresaCod = $objSession->get('idEmpresa');
        
        $arrayPuntosAtencion = $emSoporte->getRepository("schemaBundle:AdmiPuntoAtencion")->getPuntosAtencion($strEmpresaCod);
        
        $objResponse->setData(array('jsonPuntosAtencion'  => json_encode($arrayPuntosAtencion)));
        
        return $objResponse;
        
    }
    
    /**
     *
     * Funcion que obtiene el nombre de la forma de contacto por el id.
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 28-05-2021
     *  
     * @return array $objResponse
     * 
     */
    public function ajaxGetObtenerNombreOrigenAction()
    {
     
        $objRequest   = $this->getRequest();
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $objResponse  = new JsonResponse();
        
        $intIdFormaContacto = $objRequest->get("intIdOrigen");
        
        $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->findOneBy(array('id' => $intIdFormaContacto));
        
        if(!empty($entityFormaContacto))
        {
            $strNombreFormaContacto['strNombreFormaContacto'] = $entityFormaContacto->getDescripcionFormaContacto();
        }
        else
        {
            $strNombreFormaContacto['strNombreFormaContacto'] = "";
        }
        
        $objResponse->setData($strNombreFormaContacto);
        
        return $objResponse;
    }
    
    //////
 
    
    ///////////////////taty:  fin////////////////////////
 
    

    
}