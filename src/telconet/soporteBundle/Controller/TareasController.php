<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDestinatario;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoCuadrillaTarea;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaTiempoAsignacion;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoTareaTiempoParcial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;


use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Repository\InfoDetalleHistorialRepository;

class TareasController extends Controller implements TokenAuthenticatedController
{
    const CARACTERISTICA_SOLICITUD = 'SOLICITUD_TAREA_CLIENTE';
    const ESTADO_ELIMINADO         = 'Eliminado';
    const ESTADO_ACTIVO            = 'Activo';
    const ESTADO_APROBADO          = 'Aprobado';
    
    /**
     * @Secure(roles="ROLE_197-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra la pantalla inicial de la opción Tareas del Módulo de Soporte.
     *
     * @return Response $respuesta
     *
     * @author Modificado: Fernando López <filopez@telconet.ec>
     * @version 1.6 06-05-2022 - Se cambia template de vista de tareas.
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.5 30-09-2019 - Se agrega consultar si la empresa usa el árbol de hipótesis para mostrarlo en el cierre de caso.
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.4 01-03-2019 - Se agrega la recepción del número de la actividad y con esto cargar la actividad directamente en el grid
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 28-11-2017 - Se agrega el concepto del indicador de tareas por departamento
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-11-2016 - Se envía los parámetros de la empresa, ciudad y departamento del usuario en sesión
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-09-2015 - Se agregaron las variables '$strDepartamentoPersonaSession' y
     *                           '$strPuntoPersonaSession' con las cuales se verifica si existe 
     *                           un cliente en session para mostrar solo la información de dicho 
     *                           cliente.
     * 
     * @version 1.0 Version Inicial
     */     
    public function indexAction()
    {
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_197-1237'))
        {
            $rolesPermitidos[] = 'ROLE_197-1237';
        }
        $strOrigen          = "tareasPorEmpleado";
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $intNumeroActividad = $objRequest->get('numTarea') ? $objRequest->get('numTarea') : "";
        $emSeguridad        = $this->getDoctrine()->getManager("telconet_seguridad");
        $emComercial        = $this->getDoctrine()->getManager();       
        $objSession->set("strBanderaTareasDepartamento","N");
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

        $strPuntoPersonaSession         = $objRequest->request->get('puntoPersonaSession') ? $objRequest->request->get('puntoPersonaSession') : '';
        $strDepartamentoPersonaSession  = $objRequest->request->get('departamentoSession') ? $objRequest->request->get('departamentoSession') : '';

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("197", "1");
        
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$objSession->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());   
        $arrayAdmiParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strEmpresaCod);
        $strBuscarPorArbolHipotesis = 'N';
        if($arrayAdmiParametroDet['valor2']==='S')
        {
            $strBuscarPorArbolHipotesis = 'S';
        }
        return $this->render('soporteBundle:Tareas:indexTareas.html.twig', array(     'strOrigen'                     => $strOrigen,
                                                                                'intNumeroActividad'            => $intNumeroActividad,
                                                                                'item'                          => $entityItemMenu,
                                                                                'rolesPermitidos'               => $rolesPermitidos,
                                                                                'puntoPersonaSession'           => $strPuntoPersonaSession,
                                                                                'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
                                                                                'intIdCantonUsrSession'         => $intIdCantonUsrSession,
                                                                                'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
                                                                                'departamentoSession'           => $strDepartamentoPersonaSession,
                                                                                'buscaPorArbolHipotesis'        => $strBuscarPorArbolHipotesis
                                                                           )
                            );
    }

    /**
     * Variable $status que sirve para controlar los tipos de error replica
     * de los wsController.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 28/07/2020
     * 
     */
    protected $status = array ( 'OK'               => 200, 
                                'ERROR'            => 500, 
                                'TOKEN'            => 403, 
                                'NULL'             => 204, 
                                'METODO'           => 404, 
                                'CONSULTA'         => 400, 
                                'ERROR_PARCIAL'    => 206,
                                'CLAVE_EXPIRADA'   => 300,
                                'DATOS_NO_VALIDOS' => 505);
    
    /**
     * Variable $status que sirve para controlar los tipos de error replica
     * de los wsController.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 28/07/2020
     * 
     */
    protected $mensaje = array ('OK'                => 'CONSULTA EXITOSA', 
                                'ERROR'             => 'INTERNAL ERROR', 
                                'TOKEN'             => 'TOKEN INVALIDO', 
                                'NULL'              => 'SIN CONTENIDO', 
                                'METODO'            => 'METODO NO EXISTE', 
                                'CONSULTA'          => 'ERROR EN CONSULTA',
                                'CLAVE_EXPIRADA'    => 'Su clave ha caducado, por favor cambiarla en Telcos Web. - https://telcos.telconet.ec',
                                'DATOS_NO_VALIDOS'  => 'Sus datos no fueron validados correctamente');


    /**
     * @Secure(roles="ROLE_197-7")
     *
     * Documentación para el método 'gridAction'.
     *
     * Realizará la búsqueda de las tareas creadas que correspondan a los criterios 
     * ingresados por los usuarios.
     *
     * @return Response $respuesta
     * 
     * @version 1.0 Version Inicial
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 21-08-2015 - Se agregaron las variables '$arraySessionCliente' y
     *                           '$arraySessionPtoCliente' con las cuales se verifica si 
     *                           existe un cliente en session para mostrar solo la 
     *                           información de dicho cliente.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 21-08-2015 - Se eliminan las variables '$arraySessionCliente' y '$arraySessionPtoCliente', 
     *                           y se agrega la variable '$strDepartamentoSession' con la cuale se verifica si 
     *                           existe un cliente en session para mostrar solo la información de dicho cliente.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 11-12-2015 - Se modifica para enviar el parametro ["caracteristicaSolicitud"] al momento de obtener la data de las tareas.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 16-05-2016 - Se realizan ajustes para validar que no se puedan gestionar tareas de otros departamentos, cuando no se consulte
     *                           por departamento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 24-05-2016 - Se realizan ajustes para poder buscar por el departamento que creo la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 23-06-2016 - Se asocia el CANTON_ID en la table INFO_DETALLE_ASIGNACION, para determinar la oficina de que canton crea la tarea y
     *                           se agrega un filtro para saber a que ciudad se asigno la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 30-06-2016 - Se le envia un parametro mas al generarJsonMisTareas, para que internamente pueda calcular el numero de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 22-07-2016 - Se realizan ajustes para implementar el concepto de subtareas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 25-10-2016 - Se realizan ajustes para presentar las tareas pendientes del usuario en session
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 02-02-2017 - Se consulta el parámetro de la observación de la tarea para verificar si ésta ha sido reasignada por cambio de 
     *                           departamento del empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 28-11-2017 - Se agrega el concepto del indicador de tareas por departamento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.3 31-05-2018 - Se agrega credencial: indicadorTareasNacional, para que la informacion del indicador de tareas departamental,
     *                           sea a nivel nacional
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 2.4 22-11-2018 - Se agrega credencial: verTareasTodasEmpresas, para que se puedan ver las tareas de todas las empresas.
     *
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 2.5 12-12-2018 - Se agrega el envio de parametro intPersonaEmpresaRol a la función
     *                           generarJsonMisTareas para que se ejecute el pausar tareas abiertas
     *                           cuando se inicie o reanude una tarea.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.6 13-12-2018 - Se envía información de id empresa a la función de búsquedas de tareas.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.7 09-05-2019 - Al momento de obtener las tareas previo se envía el usuario la ip y
     *                           el objeto de la clase SoporteService.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.8 15-06-2019 - Se cambia la manera de obtener el id de las cuadrillas y estados por un array.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.9 04-07-2019 - Se obtiene las credenciales y la cadena de conexión del esquema 'DB_SOPORTE' para
     *                           poder enviarlos al proceso que se encarga de obtener el sysrefcursor de las tareas
     *                           solicitadas por el usuario.
     * 
     * @author Modificado: Néstor Naula <nnaulal@telconet.ec>
     * @version 3.0 03-09-2019 - Se agrega la variable strVerTareasEcucert que permite ver o no las tareas de ECUCERT.
     * @since 2.9
     * 
     * 
     * @author Modificado: Ronny Morán <rmoranc@telconet.ec>
     * @version 3.1 09-07-2020 - Se agrega la variable boolConfirmaIpSoporteTn que valida el permiso para visualizar el botón
     *                           de confirmar un enlace en tareas de soporte TN. 
     * @since 2.9
     * 
     * @author Modificado: José Guamán <jaguamanp@telconet.ec>
     * @version 3.2 25-11-2022 - Se agrega validación para buscar por todos los departamentos cuando el filtro sea asignado.
     * 
     *  
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
                
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $existeFiltro = "N";
        $parametros = array();

        $parametros["strOrigen"]  = $peticion->query->get('strOrigen') ? $peticion->query->get('strOrigen') : "";

        if($parametros["strOrigen"] == "tareasPorDepartamento")
        {
            $session->set("strBanderaTareasDepartamento","S");
        }
        else
        {
            $session->set("strBanderaTareasDepartamento","N");
        }

        $parametros["cliente"]    = $peticion->query->get('cliente') ? $peticion->query->get('cliente') : "";
        $parametros["tarea"]      = $peticion->query->get('tarea') ? $peticion->query->get('tarea') : '';
        $parametros["asignado"]   = $peticion->query->get('asignado') ? $peticion->query->get('asignado') : '';
        $parametros["estado"]     = $peticion->query->get('estado') ? json_decode($peticion->query->get('estado')) : "Todos";
        $parametros["actividad"]  = $peticion->query->get('numeroActividad') ? $peticion->query->get('numeroActividad') : "";
        $parametros["caso"]       = $peticion->query->get('numeroCaso') ? $peticion->query->get('numeroCaso') : "";
        $parametros["tareaPadre"] = $peticion->query->get('numeroTareaPadre') ? $peticion->query->get('numeroTareaPadre') : "";
        $strDepartamentoSession = $peticion->query->get('departamentoSession') ? $peticion->query->get('departamentoSession') : "";
        $departamento           = $peticion->query->get('departamento') ? $peticion->query->get('departamento') : "";
        $intProceso             = $peticion->query->get('proceso') ? $peticion->query->get('proceso') : "";
        $departamentoOrig       = $peticion->query->get('departamentoOrig') ? $peticion->query->get('departamentoOrig') : "";
        $ciudadOrigen           = $peticion->query->get('ciudadOrigen') ? $peticion->query->get('ciudadOrigen') : "";
        $ciudadDestino          = $peticion->query->get('ciudadDestino') ? $peticion->query->get('ciudadDestino') : "";
        $arrayCuadrilla         = $peticion->query->get('cuadrilla') ? json_decode($peticion->query->get('cuadrilla')) : "";
        $strOpcionBusqueda      = $peticion->query->get('opcionBusqueda') ? $peticion->query->get('opcionBusqueda') : "";
        $strVerTareasEcucert    = $peticion->query->get('verTareasEcucert') ? $peticion->query->get('verTareasEcucert') : "";
        $strQueryAllTask        = $peticion->query->get('queryAllTask') ? $peticion->query->get('queryAllTask') : "";
        $feSolicitadaDesde = explode('T', $peticion->query->get('feSolicitadaDesde'));
        $feSolicitadaHasta = explode('T', $peticion->query->get('feSolicitadaHasta'));
        $feFinalizadaDesde = explode('T', $peticion->query->get('feFinalizadaDesde'));
        $feFinalizadaHasta = explode('T', $peticion->query->get('feFinalizadaHasta'));

        $parametros['feSolicitadaDesde'] = $feSolicitadaDesde ? $feSolicitadaDesde[0] : 0;
        $parametros['intProceso']        = $intProceso;
        $parametros['feSolicitadaHasta'] = $feSolicitadaHasta ? $feSolicitadaHasta[0] : 0;
        $parametros['feFinalizadaDesde'] = $feFinalizadaDesde ? $feFinalizadaDesde[0] : 0;
        $parametros['feFinalizadaHasta'] = $feFinalizadaHasta ? $feFinalizadaHasta[0] : 0;
        $parametros["strOpcionBusqueda"] = "N";
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
        $prefijoEmpresa = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $intPersonaEmpresaRol =  $session->get('idPersonaEmpresaRol');
        $idEmpleado = ($session->get('id_empleado') ? $session->get('id_empleado') : "");
        $idDepartamento = $departamento == "" ? ($session->get('idDepartamento') ? $session->get('idDepartamento') : "") : $departamento; 
        

        $parametros["tipo"] = "ByDepartamento";
        if($departamento)
            $existeFiltro = "S";
        
        $parametros["prefijoEmpresa"]     = ($prefijoEmpresa ? $prefijoEmpresa : "");
        $parametros["codEmpresa"]     = ($codEmpresa ? $codEmpresa : "");
        $parametros["idUsuario"]      = ($idEmpleado ? $idEmpleado : "");
        $parametros["idDepartamento"] = $strDepartamentoSession ? null : ( $idDepartamento ? $idDepartamento : "");        
        $parametros["idCuadrilla"]    = null;

        $booleanVerTareasTodasEmpresas = $this->get('security.context')->isGranted('ROLE_197-6157');
        $booleanRegistroActivos        = $this->get('security.context')->isGranted('ROLE_197-6779');
        $boolConfirmaIpSoporteTn       = $this->get('security.context')->isGranted('ROLE_197-7397'); 
        $boolValidarEnlaceSoporteTn    = $this->get('security.context')->isGranted('ROLE_197-7437');
        $parametros["intPersonaEmpresaRol"] = $intPersonaEmpresaRol;

        //Se consulta si la persona en sesion tiene la credencial: verTareasTodasEmpresas (ROLE_197_6157) y la empresa Telconet
        if (true === $booleanVerTareasTodasEmpresas && $session->get('prefijoEmpresa') === "TN" )
        {
            $objPersona             = $emComercial->getRepository("schemaBundle:InfoPersona")->find($idEmpleado);
            $arrayPersonaEmpresaRol = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->findBy( array( 'personaId'      => $objPersona,
                                                                     'estado'      => 'Activo' ) );
            $arrayIdsPersonaEmpresaRol = array();
            $arrayIdsDepartamento      = array();
            foreach($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
            {
                $arrayIdsPersonaEmpresaRol[] = $objPersonaEmpresaRol->getId();
                $arrayIdsDepartamento[]      = $objPersonaEmpresaRol->getDepartamentoId();
            }
            $parametros["arrayPersonaEmpresaRol"] = $arrayIdsPersonaEmpresaRol;
            $parametros["arrayDepartamentos"]     = $arrayIdsDepartamento;
        }
        else
        {
            $parametros["arrayPersonaEmpresaRol"] = array($intPersonaEmpresaRol);
            $parametros["arrayDepartamentos"]     = array($parametros["idDepartamento"]);
        }
        if($parametros["caso"] && $parametros["caso"] != "")
        {
            $parametros["tipo"] = "ByCaso";    
        }

        //Adecuación para mostrar directamente la tarea relacionada a una actividad sin necesidad
        //de buscar por empresa o departamento, el filtro debe tener el numero de tarea/actividad en cuestión
        if($departamento == "" && $parametros["actividad"] != "")
        {
            $parametros["idDepartamento"] = null;
        }

        if($departamentoOrig)
        {
            $parametros["departamentoOrig"] = $departamentoOrig;
        }

        if($ciudadOrigen)
        {
            $parametros["ciudadOrigen"] = $ciudadOrigen;
        }

        if($ciudadDestino)
        {
            $parametros["ciudadDestino"] = $ciudadDestino;
        }

        //Se envia informacion de cuadrilla para consultar sus tareas asignadas de ser requerido        
        if (!empty($arrayCuadrilla) && is_array($arrayCuadrilla))
        {
            $parametros["tipo"]           = "ByCuadrilla";
            $parametros["idCuadrilla"]    = $arrayCuadrilla;
            $parametros["idDepartamento"] = null;
        }

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        if($departamento != '')
        {
            $isDepartamento = false;
        }
        else
        {
            $isDepartamento = true;
        }
        
        $parametros["caracteristicaSolicitud"] = self::CARACTERISTICA_SOLICITUD;

        $parametros["emComercial"]         = $emComercial;
        $parametros["emComunicacion"]      = $emComunicacion;
        $parametros["start"]               = $start;
        $parametros["limit"]               = $limit;
        $parametros["isDepartamento"]      = $isDepartamento;
        $objInfoPersonaEmpresaRol = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRol);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $parametros["oficinaSession"] = $objInfoPersonaEmpresaRol->getOficinaId()->getId();
        }
        $parametros["departamentoSession"] = $session->get('idDepartamento');
        $parametros["existeFiltro"]        = $existeFiltro;
        if($strOpcionBusqueda)
        {
            $parametros["strOpcionBusqueda"] = $strOpcionBusqueda;
        }
        
        $strMsgReasignacionAutomaticaCambioDep      = "";
        $arrayRegistroMsgReasignacionCambioDep      = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne( 'MSG_REASIGNACION_TAREA_CAMBIO_DEPARTAMENTO', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '' );

        if( $arrayRegistroMsgReasignacionCambioDep )
        {
            $strMsgReasignacionAutomaticaCambioDep = $arrayRegistroMsgReasignacionCambioDep['valor1'];
        }
        $parametros["strMsgReasignacionAutomaticaCambioDep"] = $strMsgReasignacionAutomaticaCambioDep;

        //Se consulta si la persona en session tiene la credencial: indicadorTareasNacional
        $arrayParametrosPerfil["intIdPersonaRol"] = $intPersonaEmpresaRol;
        $arrayParametrosPerfil["strNombrePerfil"] = "indicadorTareasNacional";

        $strTienePerfil = $emSoporte->getRepository('schemaBundle:SeguRelacionSistema')->getPerfilPorPersona($arrayParametrosPerfil);

        $parametros['strVerTareasEcucert']          = $strVerTareasEcucert;
        $parametros["strTieneCredencial"]           = $strTienePerfil;
        $parametros["booleanVerTareasTodasEmpresa"] = $booleanVerTareasTodasEmpresas;
        $parametros["intIdEmpresa"]                 = $session->get('idEmpresa');
        $parametros["serviceSoporte"]               = $this->get('soporte.SoporteService');
        $parametros["strUser"]                      = $session->get('user');
        $parametros["strIp"]                        = $peticion->getClientIp();
        $parametros["permiteRegistroActivos"]       = $booleanRegistroActivos;
        $parametros['serviceUtil'] = $this->get('schema.Util');
        $parametros["ociCon"] = array('userSoporte' => $this->container->getParameter('user_soporte'),
                                      'passSoporte' => $this->container->getParameter('passwd_soporte'),
                                      'databaseDsn' => $this->container->getParameter('database_dsn'));
        $parametros["permiteConfirIpSopTn"]         = $boolConfirmaIpSoporteTn; 
        $parametros["permiteValidarEnlaceSopTn"]    = $boolValidarEnlaceSoporteTn;
        $parametros["queryAllTask"]                 = $strQueryAllTask;
        $arrayUsarNuevoGrid   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne( 'VALIDACION PARA USAR NUEVA FUNCION GRID TAREAS', 
                                                            'SOPORTE', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '' );
        if ( count($arrayUsarNuevoGrid)>0         && 
             $arrayUsarNuevoGrid['valor1'] == 'S' && 
             ($arrayUsarNuevoGrid['valor2'] == '' || $arrayUsarNuevoGrid['valor2'] == $idDepartamento) )
        {
            $objJson = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                ->generarJsonInfoTareas($parametros);
        }
        else
        {
            $objJson = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                ->generarJsonMisTareas($parametros);
        }

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    
     /**
     * Documentación de la funcion 'getIndicadorTareas'.
     *
     * Método que retorna la cantidad de tareas que estan pendientes por persona y por departamento
     *
     * @return JSON $objRespuesta retorna el numero de tareas pendientes por persona y por departamento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-11-2017
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 31-05-2018 - Se agrega credencial: indicadorTareasNacional, para que el indicador salga a nivel nacional
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 04-12-2018 -  Se agrega la fecha por defecto para obtener un mejor tiempo de respuesta en los indicadores de tareas.
     *
     */
    public function getIndicadorTareasAction()
    {
        $emSoporte            = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $objPeticion          = $this->getRequest();
        $objSession           = $objPeticion->getSession();
        $arrayParametros      = array();
        $objRespuesta         = new JsonResponse();
        $intPersonaEmpresaRol = $objPeticion->get("personaEmpresaRol");
        $intIdDepartamento    = $objSession->get("idDepartamento");
        $objRespuesta->headers->set('Content-Type', 'text/json');

        //Se calcula el numero de tareas abiertas que tiene asignadas el usuario en session
        $arrayParametros["intPersonaEmpresaRolId"] = $intPersonaEmpresaRol;
        $arrayParametros["strTipoConsulta"]        = "CantidadTareasAbiertas";
        $arrayParametros["arrayEstados"]           = array('Cancelada','Rechazada','Finalizada','Anulada');
        $arrayParametros["strTipoConsulta"]        = "persona";
        $arrayParametros["intPersonaEmpresaRol"]   = $intPersonaEmpresaRol;

        $arrayFechaDefecto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

        if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
            checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
        {
            $strFechaDefecto = $arrayFechaDefecto['valor1'].'-'. //Año
                               $arrayFechaDefecto['valor2'].'-'. //Mes
                               $arrayFechaDefecto['valor3'];     //Día

            $arrayParametros['strFechaDefecto'] = $strFechaDefecto;
        }

        $arrayRespuesta   = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getDetalleTareas($arrayParametros);
        $intTareasPersona = $arrayRespuesta["intCantidadTareas"];
        //Se calcula el numero de tareas abiertas por departamento
        $objInfoPersonaEmpresaRol = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRol);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $arrayParametros["intOficinaId"]      = $objInfoPersonaEmpresaRol->getOficinaId()->getId();
            $arrayParametros["intIdDepartamento"] = $objInfoPersonaEmpresaRol->getDepartamentoId();
        }
        $arrayParametros["strEstado"] = "Activo";
        $arrayParametros["intDepartamentoId"] = $intIdDepartamento;
        $arrayParametros["strTipoConsulta"]   = "departamento";

        //Se consulta si la persona en session tiene la credencial: indicadorTareasNacional
        $arrayParametrosPerfil["intIdPersonaRol"] = $intPersonaEmpresaRol;
        $arrayParametrosPerfil["strNombrePerfil"] = "indicadorTareasNacional";

        $strTienePerfil = $emSoporte->getRepository('schemaBundle:SeguRelacionSistema')->getPerfilPorPersona($arrayParametrosPerfil);

        $arrayParametros["strTieneCredencial"] = $strTienePerfil;
        $arrayTareasPendientesDepartamento     = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                           ->getDetalleTareas($arrayParametros);

        $objSession->set('numeroTareasAbiertasDepartamento', $arrayTareasPendientesDepartamento["intCantidadTareas"]);
        $objSession->set('numeroTareasAbiertas', $intTareasPersona);

        $intCantidadCasoMovil = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCantidadCasoMovil();
        $objSession->set('numeroTareasAbiertasMovil', $intCantidadCasoMovil);

        return $objRespuesta->setData(array('tareasPorDepartamento' => $arrayTareasPendientesDepartamento["intCantidadTareas"],
                                            'tareasPersonales'      => $intTareasPersona,
                                            'cantCasosMoviles'      => $intCantidadCasoMovil));
    }

     /**
     *
     * Documentación para el método 'ajaxGetMiembrosCuadrilla'.
     *
     * Obtener los miembros relacionados a una cuadrilla enviada como parametro
     *
     * @return Response $respuesta          
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 13-10-2015 
     *      
     */         
    public function ajaxGetMiembrosCuadrillaAction()
    {
        $respuesta = new Response();        
        $respuesta->headers->set('Content-Type', 'text/json');        
        $peticion = $this->get('request');                        
        $em       = $this->getDoctrine()->getManager('telconet');
        
        /*
         * Se setea la empresa TN -> Telconet ya que todos los oporativos y cuadrillas pertenecen a esta empresa para la operacion
         */
        $objEmpresaGrupo = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo('TN');                        
        $idCuadrilla     = $peticion->get('idCuadrilla');        
        $objJson         = $em->getRepository("schemaBundle:AdmiCuadrilla")->getJsonMiembrosPorCuadrilla($idCuadrilla,$objEmpresaGrupo->getId());
            
        $respuesta->setContent($objJson);
        
        return $respuesta;        
    }
    
    
     /**
     * Documentación de la funcion 'getDatosRespuestaAutomatica'.
     *
     * Método encargado de obtener los datos de la ultima persona que reasigno o creo una tarea
     *
     * @return objRespuesta retorna los datos consultados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-12-2017
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-06-2018 - Se agrega parametro al metodo: getResultadoJefeDepartamentoEmpresa
     */
    public function getDatosRespuestaAutomaticaAction()
    {
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $arrayRespuesta     = array();
        $arrayEmpleadoJefe  = array();
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $objRespuesta       = new JsonResponse();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $intIdPersonaEmpresaRol     = "";
        $intDepartamentoId          = "";
        $intIdPersonaRolResponsable = "";
        $intDetalleId               = $objPeticion->get("intDetalleId");

        $arrayRespuesta["strPrefijoSession"]      = $objSession->get('prefijoEmpresa');
        $arrayRespuesta["intCiudadSession"]       = $objSession->get('intIdCanton');
        $arrayRespuesta["intDepartamentoSession"] = $objSession->get('idDepartamento');

        $arrayDatosUsuario  = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                        ->getResultadoUltimoDetalleAsignacionTareaRechazada(array("intIdDetalle" => $intDetalleId ));

        //Se busca el ultimo usuario que reasigno la tarea
        if(!empty($arrayDatosUsuario))
        {
            $objUltimoUsuarioOrigen = $arrayDatosUsuario[0];

            if(is_object($objUltimoUsuarioOrigen))
            {
                $intIdPersonaEmpresaRol = $objUltimoUsuarioOrigen->getPersonaEmpresaRolId();
            }

            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($intIdPersonaEmpresaRol);

            if(is_object($objInfoPersonaEmpresaRol)&& $objInfoPersonaEmpresaRol->getEstado() != "Activo")
            {
                //Si el rol esta inactivo se reasigna la tarea al jefe del ultimo departamento que pertenecio
                $intDepartamentoId = $objInfoPersonaEmpresaRol->getDepartamentoId();

                $objAdmiDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                   ->find($intDepartamentoId);

                if(is_object($objAdmiDepartamento))
                {
                    $arrayEmpleadoJefe = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                     ->getResultadoJefeDepartamentoEmpresa($intDepartamentoId,
                                                                                           $objAdmiDepartamento->getEmpresaCod(),'');
                    if(!empty($arrayEmpleadoJefe["personaEmpresaRolId"]))
                    {
                        $intIdPersonaRolResponsable = $arrayEmpleadoJefe["personaEmpresaRolId"];
                    }
                    else
                    {
                        $intIdPersonaRolResponsable = $objInfoPersonaEmpresaRol->getId();
                    }
                }

                $intIdPersonaEmpresaRol = $intIdPersonaRolResponsable;
            }

        }//Se busca el usuario que creo la tarea
        else
        {
            $objDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intDetalleId);

            if(is_object($objDetalle))
            {
                $strUsrCreacionDetalle = $objDetalle->getUsrCreacion();

                $intNumeroTarea = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->getMinimaComunicacionPorDetalleId($intDetalleId);

                if(!empty($strUsrCreacionDetalle))
                {
                    $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($intNumeroTarea);

                    if(is_object($objInfoComunicacion))
                    {
                        $strCodEmpresaUserCrea     = $objInfoComunicacion->getEmpresaCod();
                        $arrayParametrosPersonaRol = array("strLoginPersona"       => $strUsrCreacionDetalle,
                                                           "idEmpresa"             => $strCodEmpresaUserCrea,
                                                           "estado"                => 'Activo',
                                                           "strDescripcionTipoRol" => 'Empleado');

                        $arrayDatosUserCrea = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                          ->getResultadoPersonaEmpresaRolPorCriterios($arrayParametrosPersonaRol);

                        $arrayResultadoUserCrea = $arrayDatosUserCrea['resultado'];

                        if(!empty($arrayResultadoUserCrea[0]))
                        {
                            $intIdPersonaEmpresaRol = $arrayResultadoUserCrea[0]["idPersonaEmpresaRol"];
                        }
                        else
                        {
                            $arrayParametrosPersonaRol = array("strLoginPersona"       => $strUsrCreacionDetalle,
                                                               "idEmpresa"             => $strCodEmpresaUserCrea,
                                                               "strDescripcionTipoRol" => 'Empleado');

                            $arrayDatosUserCrea = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                              ->getResultadoPersonaEmpresaRolPorCriterios($arrayParametrosPersonaRol);

                            $arrayResultadoUserCrea = $arrayDatosUserCrea['resultado'];

                            if(!empty($arrayResultadoUserCrea[0]))
                            {
                                $intIdPersonaEmpresaRol = $arrayResultadoUserCrea[0]["idPersonaEmpresaRol"];

                                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->find($intIdPersonaEmpresaRol);
                            }

                            if(is_object($objInfoPersonaEmpresaRol))
                            {

                                //Si el rol esta inactivo se reasigna la tarea al jefe del ultimo departamento que pertenecio
                                $intDepartamentoId = $objInfoPersonaEmpresaRol->getDepartamentoId();

                                $objAdmiDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                   ->find($intDepartamentoId);

                                if(is_object($objAdmiDepartamento))
                                {
                                    $arrayEmpleadoJefe = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                     ->getResultadoJefeDepartamentoEmpresa($intDepartamentoId,
                                                                                                           $objAdmiDepartamento->getEmpresaCod(),'');

                                    if(!empty($arrayEmpleadoJefe["personaEmpresaRolId"]))
                                    {
                                        $intIdPersonaRolResponsable = $arrayEmpleadoJefe["personaEmpresaRolId"];
                                    }
                                    else
                                    {
                                        $intIdPersonaRolResponsable = $objInfoPersonaEmpresaRol->getId();
                                    }
                                }

                                $intIdPersonaEmpresaRol = $intIdPersonaRolResponsable;
                            }
                        }
                    }
                }
            }
        }

        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($intIdPersonaEmpresaRol);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            //Se obtiene el usuario
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->find($objInfoPersonaEmpresaRol->getPersonaId()->getId());

            if(is_object($objInfoPersona))
            {
                if($objInfoPersonaEmpresaRol->getEstado() != 'Activo')
                {
                    $arrayRespuesta["intPersonaEmpresaRol"] = "";
                    $arrayRespuesta["strNombreEmpleado"]    = "";
                    $arrayRespuesta["strUsuarioRespuesta"]  = "";
                }
                else
                {
                    $arrayRespuesta["intPersonaEmpresaRol"] = $objInfoPersonaEmpresaRol->getId();
                    $arrayRespuesta["strNombreEmpleado"]    = $objInfoPersona->__toString();
                    $arrayRespuesta["strUsuarioRespuesta"]  = $objInfoPersona->getId().'@@'.$arrayRespuesta["intPersonaEmpresaRol"];
                }
            }

            //Se obtiene el departamento
            $intDepartamentoId = $objInfoPersonaEmpresaRol->getDepartamentoId();

            if(!empty($intDepartamentoId))
            {
                $objAdmiDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')->find($intDepartamentoId);

                if(is_object($objAdmiDepartamento))
                {
                    $arrayRespuesta["intIdDepartamento"]     = $objAdmiDepartamento->getId();
                    $arrayRespuesta["strNombreDepartamento"] = $objAdmiDepartamento->getNombreDepartamento();

                    $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                       ->find($objAdmiDepartamento->getEmpresaCod());

                    if(is_object($objInfoEmpresaGrupo))
                    {
                        $arrayRespuesta["strPrefijo"]       = $objInfoEmpresaGrupo->getPrefijo();
                        $arrayRespuesta["intIdEmpresa"]     = $objInfoEmpresaGrupo->getId();
                        $arrayRespuesta["strNombreEmpresa"] = $objInfoEmpresaGrupo->getNombreEmpresa();
                    }
                }
            }

            //Se obtiene la ciudad
            if(is_object($objInfoPersonaEmpresaRol->getOficinaId()))
            {
                $intIdOficina = $objInfoPersonaEmpresaRol->getOficinaId()->getId();

                $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);

                if(is_object($objInfoOficinaGrupo))
                {
                    $objAdmiCanton = $emComercial->getRepository('schemaBundle:AdmiCanton')->find($objInfoOficinaGrupo->getCantonId());

                    if(is_object($objAdmiCanton))
                    {
                        $arrayRespuesta["intIdCiudad"] = $objAdmiCanton->getId();
                        $arrayRespuesta["strCiudad"]   = $objAdmiCanton->getNombreCanton();
                    }
                }
            }
        }
    
        return $objRespuesta->setData(array('strUsuarioRespuesta'    => $arrayRespuesta["strUsuarioRespuesta"],
                                            'strPrefijoEmpresa'      => $arrayRespuesta["strPrefijo"],
                                            'intIdCiudad'            => $arrayRespuesta["intIdCiudad"],
                                            'intDepartamento'        => $arrayRespuesta["intIdDepartamento"],
                                            'strUsuarioRespuesta'    => $arrayRespuesta["strUsuarioRespuesta"],
                                            'strPrefijoSession'      => $arrayRespuesta["strPrefijoSession"],
                                            'intCiudadSession'       => $arrayRespuesta["intCiudadSession"],
                                            'intDepartamentoSession' => $arrayRespuesta["intDepartamentoSession"]));
    }

    /**     
     *
     * Documentación para el método 'administrarTareaAsignadaAction'
     *
     * @return Response $respuesta
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2016 Se guarda el estado de la tarea al guardar un seguimiento.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 12-09-2016 Se valida que la tarea se encuentre abierta para poder ejecutar la herramienta
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 08-11-2016 Se incluyen los botones de iniciar,pausar y reanudar tareas.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 20-04-2017 Se realizan ajustes para que los usuarios que inicien o reanuden una tarea queden como responsables
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 17-04-2019 - Se agrega el parámetro jsonDatosPausa que obtiene el tipo y tiempo de pausa de una tarea.
     * 
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 26-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
     *                           realizar en la tarea es válida.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     */
    public function administrarTareaAsignadaAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_soporte');
         
        $peticion = $this->get('request');
        
        $session = $peticion->getSession();

        $codEmpresa             = $session->get('idEmpresa');
        $strUser                = $session->get('user');
        $intPersonaEmpresaRolId = $session->get('idPersonaEmpresaRol');    
        $intIdDepartamento      = $session->get('idDepartamento');
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $session->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";     
        
        $id_detalle       = $peticion->get('id');
        $strOrigen        = $peticion->get('origen');
        $strIpUser        = $peticion->getClientIp();
        $intPtoCliente    = $session->get('ptoCliente');
        $arrayParametrosHist["strIpCreacion"] = $strIpUser;

        $intIdDetalleHist = $peticion->get('intIdDetalleHist');
        if ($intIdDetalleHist !== '' && !empty($intIdDetalleHist))
        {
            $arrayValidarAccion = $serviceSoporte->validarAccionTarea(array('intIdDetalle'     => $id_detalle,
                                                                            'intIdDetalleHist' => $intIdDetalleHist));

            if (!$arrayValidarAccion['boolRespuesta'])
            {
               return $respuesta->setContent(json_encode(array('success'       => false,
                                                               'seguirAccion'  => $arrayValidarAccion['boolRespuesta'],
                                                               'mensaje'       => $arrayValidarAccion['strMensaje'])));
            }
        }

        $em->getConnection()->beginTransaction();
        
        try
        {
            if($strOrigen == 'iniciar' || $strOrigen == 'pausar' || $strOrigen == 'reanudar')
            {
                if ($strOrigen == 'pausar' && $peticion->get('jsonDatosPausa') !== '')
                {
                    $objDatosPausa = json_decode($peticion->get('jsonDatosPausa'));
                    $strTipoPausa  = $objDatosPausa->tipo;
                    $intTiempo     = $objDatosPausa->tiempo;
                }

                $objDetalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);

                $arrayParametros["strTipo"]              = $strOrigen;
                $arrayParametros["objDetalle"]           = $objDetalle;
                $arrayParametros["strObservacion"]       = $peticion->get('observacion');
                $arrayParametros["strUser"]              = $strUser;
                $arrayParametros["strIpUser"]            = $strIpUser;
                $arrayParametros["strCodEmpresa"]        = $codEmpresa;
                $arrayParametros["intPersonaEmpresaRol"] = $intPersonaEmpresaRolId;
                $arrayParametros["intPtoCliente"]        = $intPtoCliente;
                $arrayParametros["idDepartamento"]       = $intIdDepartamento;
                $arrayParametros["strTipoReprograma"]    = $strTipoPausa;
                $arrayParametros["intTiempo"]            = $intTiempo;

                $soporteService = $this->get('soporte.SoporteService');
                $retorno        = $soporteService->administrarTarea($arrayParametros);

                $resultado = json_encode(array('success'=>true));
            }
            else
            {
                $strEstadoActualTarea = $em->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($id_detalle);

                if($strEstadoActualTarea  != "Cancelada" && $strEstadoActualTarea  != "Rechazada" && $strEstadoActualTarea  != "Finalizada")
                {
                    $detalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);

                    if($detalle->getFeSolicitada()< new \DateTime('now') || $detalle->getFeSolicitada()== new \DateTime('now'))
                          $esProgramada = false;
                    else $esProgramada = true;

                     $numeroAceptaciones = $em->getRepository('schemaBundle:InfoDetalle')
                                              ->getNumeroAceptacionesTarea($id_detalle,'Aceptada');

                     if($numeroAceptaciones[0]['cont']==0)$tieneAceptaciones = false; else $tieneAceptaciones = true;

                    if($peticion->get('bandera') == 'Aceptada'){
                          if(!$esProgramada && !$tieneAceptaciones)
                                $detalle->setFeSolicitada(new \DateTime('now'));
                          $em->persist($detalle);
                          $em->flush();
                    }
                     else if($peticion->get('bandera') == 'Rechazada'){
                          $detalle->esSolucion('N');
                          $em->persist($detalle);
                          $em->flush();
                    }

                    //Se ingresa el historial de la tarea
                    if(is_object($detalle))
                    {
                        $arrayParametrosHist["intDetalleId"] = $detalle->getId();
                    }
                    $arrayParametrosHist["strObservacion"]  = $peticion->get('observacion');
                    $arrayParametrosHist["strEstadoActual"] = $peticion->get('bandera');
                    $arrayParametrosHist["strFeCreacion"]   = !$esProgramada?new \DateTime('now'):$detalle->getFeSolicitada();
                    $arrayParametrosHist["strAccion"]       = $peticion->get('bandera');

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                     
                    

                    //Se ingresa el seguimiento de la tarea
                    $arrayParametrosHist["strObservacion"]  = "Tarea fue ".$peticion->get('bandera');
                    $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist); 

                    $resultado = json_encode(array('success'=>true));

                }
                else
                {
                    $resultado = json_encode(array('success'=>true,'mensaje'=>"cerrada"));
                }
            }
            $em->getConnection()->commit();

            //ACTUALIZA TAREA EN INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId'] = isset($id_detalle)? $id_detalle:null;
            $arrayParametrosInfoTarea['strUsrUltMod'] = isset($strUser)? $strUser:'';
            $serviceSoporte->actualizarInfoTarea($arrayParametrosInfoTarea);
        }
        catch (Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
        
        $respuesta->setContent($resultado);
        
        return $respuesta;
        
    }     
        
     /**
     * 
     * Documentación de la funcion 'getTareasAbiertas'.
     * 
     * Método que retorna la cantidad de tareas en ejecución que tiene un usuario
     * 
     * @return object objResponse
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 18-04-2017
     */
    public function getTareasAbiertasAction()
    {
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $objPeticion        = $this->getRequest();
        $objResponse        = new JsonResponse();        
        $arrayParametros    = array();
        $arrayRespuesta     = array();
        $strTareas          = "";
        $intContador        = 0;        

        $intPersonaEmpresaRol = $objPeticion->get("personaEmpresaRolId");        

        //Se obtiene la cantidad de tareas que se estan ejecutando
        $arrayParametros["intPersonaEmpresaRolId"] = $intPersonaEmpresaRol;
        $arrayParametros["strTipoConsulta"]        = "TareasEjecutando";
        $arrayParametros["arrayEstados"]           = array('Aceptada');
        $arrayParametros["strEstado"]              = "Activo";

        $arrayRespuesta = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getNumeroTareasAbiertas($arrayParametros);

        foreach($arrayRespuesta["arrayTareasEjecutando"] as $arrayTareaEjecutada)
        {
            if($strTareas != "")
            {
                $strTareas .= "," . $arrayTareaEjecutada["numeroTarea"];
            }
            else
            {
                $strTareas = $arrayTareaEjecutada["numeroTarea"];
            }
        }

        $arrayRespuesta["strTareas"] = $strTareas;
        
        $objResponse->setData($arrayRespuesta);     

        return $objResponse;
    }
    
    
     /**
     * 
     * Documentación de la función 'ejecutarPausarTareas'.
     * 
     * Método encargado de Pausar las tareas que tiene en ejecución un usuario
     * 
     * @return object objResponse
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-04-2017
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 5-11-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
     * @since 1.0
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 23-04-2020 - Se elimina el llamado al proceso de Sys Cloud-Center.
     * @since 1.1
     * 
     */
    public function ejecutarPausarTareasAction()
    {
        $objPeticion            = $this->getRequest();
        $objSession             = $objPeticion->getSession();
        $arrayParametros        = array();
        $arrayRespuesta         = array();
        $objResponse            = new JsonResponse();
        $serviceSoporte         = $this->get('soporte.SoporteService');
        $serviceProceso         = $this->get('soporte.ProcesoService');
        $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $boolProcesaSysCloud    = false;

        $intNumeroTarea        = $objPeticion->get('numeroTarea');
        $strNombreTarea        = $objPeticion->get('nombre_tarea');
        $strNombreProceso      = $objPeticion->get('nombre_proceso');
        $strNombrePersona      = $objPeticion->get('asignado_nombre');
        $strDepartamento       = $objPeticion->get('departamento_nombre');
        $intIdDetalleTarea     = $objPeticion->get('id_detalle');
        $strEmpleado           = $objPeticion->getSession()->get('empleado');
        $strDepartamentoAs     = $objPeticion->getSession()->get('departamento');
        $strObservacion        = "Tarea Pausada";

        $intPersonaEmpresaRol = $objPeticion->get("personaEmpresaRolId");        

        $arrayParametros["intPersonaEmpresaRolId"] = $intPersonaEmpresaRol;
        $arrayParametros["strCodigoEmpresa"]       = $objSession->get('idEmpresa');        
        $arrayParametros["strUser"]                = $objSession->get('user');
        $arrayParametros["strIpUser"]              = $objPeticion->getClientIp();

        $arrayRespuesta = $serviceSoporte->ejecutarPausarTareas($arrayParametros);   

        $objResponse->setData($arrayRespuesta);     

        return $objResponse;
    }
    

     /**
     * @Secure(roles="ROLE_197-41")
     *
     * Documentación para el método 'getClientesAction'.
     *
     * Retorna los logins existentes de los puntos de los clientes que cumplan con el parámetro ingresado por
     * el usuario.
     *
     * @return Response 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 11-09-2015 - Se valida que la variable 'nombre' tenga un valor diferente de vacío o null
     *                           para minorizar el tiempo de respuesta de la consulta a la base de datos.
     *
     * @version 1.0 Version Inicial
     */ 
    public function getClientesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));        
        
        $objJson = '';
        
        if( $nombre )
        {
            $objJson = $this->getDoctrine()
                            ->getManager("telconet")
                            ->getRepository('schemaBundle:InfoPunto')
                            ->generarJsonClientes($nombre);
        }
            
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

     /**
     * Funcion que retorna los estados de las tareas
     *
     * @return JSON $objResponse
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-12-2016
     */
    public function getEstadosAction()
    {
        $objPeticion        = $this->get('request');
        $strEstado          = $objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "Activo";
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las tareas configuradas para la tarea
        $arrayEstadosTareas = $emSoporte->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get("ESTADO_TAREAS",'SOPORTE','TAREAS',"","",$strEstado,"","","","");

        if(isset($arrayEstadosTareas))
        {
            foreach($arrayEstadosTareas as $arrayIndiceTarea)
            {
                $arrayEncontrados[] = array('nombre_estado_tarea' => $arrayIndiceTarea["valor1"]);
            }
            $intNumeroRegistros = count($arrayEstadosTareas);
        }
        else
        {
            $arrayEncontrados[] = "no existen registros";
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
    * @Secure(roles="ROLE_197-544")
    */   
	public function ajaxGetTareasByProcesoAction()
    {
		$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombreProceso = $peticion->query->get('nombreProceso');
        $estado = $peticion->query->get('estado');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
		
        $em = $this->getDoctrine()->getManager("telconet");
		$em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        
		if(!$nombreProceso)
			$nombreProceso = "TAREAS SOPORTE";
		if(!$estado)
			$estado = "Activo";
			
        $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTarea')
            ->generarJsonTareasByProcesoAndTarea($em, "", "", "",$estado, $entityProceso->getId(), $codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
	}
	
	 
    /**
     * 
     * @Secure(roles="ROLE_197-584")
     * 
     * reprogramarTareaAction
     * 
     * Funcion que reprograma una tarea
     * @version Inicial 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2016 Se guarda el estado de la tarea en el seguimiento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 05-07-2016 Se valida que si ingresan caracteres de apertura y cierre de tags en la observacion, se eliminan
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 12-09-2016 Se valida que la tarea se encuentre abierta para poder ejecutar la herramienta
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 19-06-2018 - Se modifico programación en envio de notificación para seleccionar los puntos afectados 
     *                           de los casos desde la información registrada en Telcos, y no del punto en sesión
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 17-04-2019 - Se agrega el método genérico para el cálculo del los tiempos de la tarea.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 26-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
     *                           realizar en la tarea es válida.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.9 10-09-2021 - Se agrega llamado a proceso que se encarga de enviar tracking a megadatos
     * 
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 2.0 15-10-2021 - Se elimina filtro de tareas por dpto Operaciones Urbanas para tracking map
     *
     * @return array $respuesta  Objeto en formato JSON
     * 
     */
    public function reprogramarTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $strUsuario  = $session->get('user');

        $codEmpresa        = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $intIdDepartamento   = $session->get('idDepartamento');
        $objPtoCliente       = $session->get('ptoCliente');
        $arrayDepTraking     = array(128);
        $boolEsHal           = false;
        $emSoporte           = $this->getDoctrine()->getManager('telconet_soporte');

        if (is_array($objPtoCliente))
        {
            $intPtoCliente =  $objPtoCliente['id'];
        }
        else
        {
            $intPtoCliente =  $objPtoCliente;
        }
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUsuario;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();
        $arrayParametrosHist["strOpcion"]               = "Historial";        
		
        $id_detalle = $peticion->get('id_detalle');
        $strObservacion = $peticion->get('observacion') ? $peticion->get('observacion') : "Tarea Reprogramada";
        $motivo = $peticion->get('motivo');        
        
        $strFecha = explode("T", $peticion->get('fe_ejecucion'));
        $strHora  = explode("T", $peticion->get('ho_ejecucion'));		
        
        $objDate = date_create(date('Y-m-d H:i',strtotime($strFecha[0].' '.$strHora[1])));

        $intIdDetalleHist = $peticion->get('intIdDetalleHist');
        if ($intIdDetalleHist !== '' && !empty($intIdDetalleHist))
        {
            $arrayValidarAccion = $serviceSoporte->validarAccionTarea(array('intIdDetalle'     => $id_detalle,
                                                                            'intIdDetalleHist' => $intIdDetalleHist));

            if (!$arrayValidarAccion['boolRespuesta'])
            {
            return $respuesta->setContent(json_encode(array('success'       => false,
                                                            'seguirAccion'  => $arrayValidarAccion['boolRespuesta'],
                                                            'mensaje'       => $arrayValidarAccion['strMensaje'])));
            }
        }
        
        //Si es reprogramada hacia atras
        if($objDate < new \DateTime('now'))
        {
            $boolEsReprogramadaAtras = true;
        } 
        else 
        {
            $boolEsReprogramadaAtras = false;
        }

        $em->getConnection()->beginTransaction();
        try
        {
            $strEstadoActualTarea = $em->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($id_detalle);

            if($strEstadoActualTarea  != "Cancelada" && $strEstadoActualTarea  != "Rechazada" && $strEstadoActualTarea  != "Finalizada")
            {
                $detalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);
                $detalle->setFeSolicitada($objDate);
                $em->persist($detalle);
                $em->flush();

                /* @var $soporteService SoporteService */
                $soporteService = $this->get('soporte.SoporteService');
                //Se eliminan simbolos de tags
                $strObservacion = $soporteService->eliminarSimbolosDeTags($strObservacion);                

                //Se ingresa el historial de la tarea
                $arrayParametrosHist["intDetalleId"]    = $id_detalle;
                $arrayParametrosHist["strObservacion"]  = $strObservacion;
                $arrayParametrosHist["strEstadoActual"] = "Reprogramada";
                $arrayParametrosHist["strFeCreacion"]   = $boolEsReprogramadaAtras ? $objDate : new \DateTime('now');
                $arrayParametrosHist["strAccion"]       = "Reprogramada";
                
                if($motivo != "")
                {
                    $arrayParametrosHist["strMotivo"] = $motivo; 
                }
                else
                {
                    $arrayParametrosHist["strMotivo"] = "T";
                }

                $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                //Función encargada de calcular los tiempos de las tareas.
                $serviceSoporte->calcularTiempoEstado(array('strEstadoActual'    => "Reprogramada",
                                                            'intIdDetalle'       => $id_detalle,
                                                            'objFechaReprograma' => $objDate,
                                                            'strTipoReprograma'  => $arrayParametrosHist["strMotivo"],
                                                            'strUser'            => $strUsuario,
                                                            'strIp'              => $peticion->getClientIp()));

                //Se ingresa el seguimiento de la tarea
                $arrayParametrosHist["strObservacion"]  = "Tarea Reprogramada para el " . 
                                                          date_format($objDate, 'Y-m-d H:i')." Motivo : ".$strObservacion;
                $arrayParametrosHist["strOpcion"]       = "Seguimiento";

                $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                $em->getConnection()->commit();
                $resultado = json_encode(array('success'=>true));                

                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $arrayParametrosHist["intDetalleId"];
                $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametrosHist["strUsrCreacion"];
                $objServiceSoporte                          = $this->get('soporte.SoporteService');
                $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

                /******************Envio de sms y correo***************/
                $arrayCaso = $em->getRepository('schemaBundle:InfoDetalle')->tareaPerteneceACaso($id_detalle);
                if ($arrayCaso[0]['caso']!=0)
                {
                    $arrayDetalleHip = $em->getRepository('schemaBundle:InfoDetalle')->getCasoPadreTarea($id_detalle);
                    $intIdCaso = $arrayDetalleHip[0]->getCasoId()->getId();
                    $objInfoCaso  = $em->getRepository('schemaBundle:InfoCaso')->find($intIdCaso);
                    if (($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Tecnico')||
                       ($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Arcotel'))
                    {
                        $arrayAfectacionPadres = $em->getRepository('schemaBundle:InfoCaso')
                                                    ->getRegistrosAfectadosTotalXCaso($intIdCaso,'Cliente','Data');
                        foreach($arrayAfectacionPadres as $arrayAfectadoPadre)
                        {
                            $arrayParametrosSMS = array();
                            $arrayParametrosSMS['puntoId']      = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosSMS['personaId']    = "";
                            $arrayParametrosSMS['destinatario'] = "CLI";
                            $arrayParametrosSMS['tipoEnvio']    = "OUT";
                            $arrayParametrosSMS['tipoNotifica'] = "SMS";
                            $arrayParametrosSMS['empresa']      = $codEmpresa;
                            $arrayParametrosSMS['tipoEvento']   = "REPROGRAMAR";
                            $arrayParametrosSMS['usuario']      = $strUsuario;
                            $arrayParametrosSMS['casoId']       = $objInfoCaso->getId();
                            $arrayParametrosSMS['detalleId']    = "";
                            $arrayParametrosSMS['asignacion']   = "";
                            $soporteService->enviaSMSCasoCliente($arrayParametrosSMS);
                            $arrayParametrosCorreo = array();
                            $arrayParametrosCorreo['puntoId']        = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosCorreo['usuario']        = $strUsuario;
                            $arrayParametrosCorreo['caso']           = $objInfoCaso;
                            $arrayParametrosCorreo['idDepartamento'] = $intIdDepartamento;
                            $arrayParametrosCorreo['empresa']        = $codEmpresa;
                            $arrayParametrosCorreo['codPlantilla']   = "CASOREPROGRACLI";
                            $arrayParametrosCorreo['asunto']         = "Reprogramación del caso";
                            if ($motivo == 'C')
                            {
                                $arrayParametrosCorreo['observacion']    = "Cliente Solicita Reprogramar ".$strObservacion;
                            }
                            elseif ($motivo == 'T')
                            {
                                $arrayParametrosCorreo['observacion']    = "Tecnico Solicita Reprogramar ".$strObservacion;
                            }else
                            {
                                $arrayParametrosCorreo['observacion']    = $strObservacion;
                            }
                            $soporteService->enviaCorreoClientesCasos($arrayParametrosCorreo);
                        }
                    }
                }

                $boolEsHal = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                       ->isAsignadoHal(array( 'intDetalleId' => $id_detalle));
                
                if($boolEsHal)
                {
                
                    $strCommand = 'nohup php /home/telcos/app/console Envia:Tracking ';
                    $strCommand = $strCommand . escapeshellarg($strUsuario). ' ';
                    $strCommand = $strCommand . escapeshellarg($arrayParametrosHist["strIpCreacion"]). ' ';
                    $strCommand = $strCommand . '"Tarea Reprogramada" ';
                    $strCommand = $strCommand . escapeshellarg($id_detalle). ' ';

                    $strCommand = $strCommand .'>/dev/null 2>/dev/null &';
                    shell_exec($strCommand);
                }                
            }
            else
            {
                $resultado = json_encode(array('success'=>true,'mensaje'=>"cerrada"));
            }
        }
        catch (Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
		
        $respuesta->setContent($resultado);        
        return $respuesta;
    }
	
    /**
     * @Secure(roles="ROLE_197-585")
     * 
     * cancelarTareaAction
     *
     * Funcion que cancela, rechaza o anula una tarea
     *
     * @return array $respuesta  Objeto en formato JSON
     *
     * @version Inicial 1.0
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 10-11-2015 Se realizan ajustes para enviar notificacion de via mail cuando se rechaza una tarea
     *
     * @author Modificado Edson Franco <efranco@telconet.ec>
     * @version 1.2 11-12-2015 - Se realiza el ajuste para que al rechazar o cancelar una tarea se elimine la solicitud de facturación que tiene
     *                           asociada la tarea
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 23-06-2016 Se guarda el estado de la tarea en el seguimiento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 05-07-2016 Se valida que si ingresan caracteres de apertura y cierre de tags en la observacion, se eliminan
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 12-09-2016 Se valida que la tarea se encuentre abierta para poder ejecutar la herramienta
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 11-11-2016 Se registra el tiempo total de la tarea
     *
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 18-11-2016 Se agrega el flujo de reasignación a la penúltima persona que fue asignada cuando la tarea es rechazada y si no existe
     *                         y cuando se quiera rechazar una tarea recién asignada, se devolverá la tarea a la persona quién la creó.
     *                         Sólo se enviará notificación en la reasignación para la persona que rechazó y su respectivo departamento y a la 
     *                         persona reasignada y su respectivo departamento
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 06-12-2016 Se modifica la manera de obtener la persona quién ha creado la tarea, ya que cuando hay un login asociado, 
     *                         el remitente que se está guardando es el id del punto y no el id de la persona
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 06-01-2016  - Se valida el estado Activo en la INFO_PERSONA_EMPRESA_ROL, porque al momento en que se asigna
     *                            una tarea automatica por rechazo de la misma, no se considera el estado Activo y en ciertos
     *                            casos se asignan empresa_roles inactivos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 14-02-2017 - Se modifica la consulta para obtener la información del empleado quien creó la tarea, ya que se presentan problemas
     *                           en el rechazo de tareas cuando el empleado se encuentra en estado pendiente. Con el cambio solo se validará el
     *                           estado de dicha persona en la info_persona_empresa_rol. Además se agrega un mensaje de error, para saber con mayor
     *                           exactitud el motivo de error en el rechazo de una tarea.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 15-12-2017 -  En la opcion de rechazar tarea se obtiene el departamento actual del ultimo usuario
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.3 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
     *
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.3 09-02-2018 - Al momento de rechazar la tarea seteamos el estado Rechazada en la tabla INFO_TAREA_TIEMPO_PARCIAL.
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.4 19-06-2018 - Se modifico programación en envio de notificación para seleccionar los  puntos afectados 
     *                           de los casos desde la información registrada en  Telcos, y no del punto en sesión
     * @since 2.3
     * 
     * @author Modificado: Jose Angulo <jmangulos@telconet.ec>
     * @version 2.5 18-03-2019 - Se modifico programación en envio de notificación para cancelación de tareas
     * 
     * @author Modificado: Allan Suárez <arsuarez@telconet.ec>
     * @version 2.6 21-03-2019 - Notificaciones de envio de correo filtrado por empresa, cantón y departamento para generar correo hacia usuario
     *                           iniciador
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.7 17-04-2019 - Se agrega los métodos genéricos para el cálculo de los tiempos de las tareas, y así obtener
     *                           los tiempos totales de las pausas y reprogramaciones tanto del cliente como de la empresa.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.8 28-10-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
     * @since 2.7
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.8 26-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
     *                           realizar en la tarea es válida.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 3.0 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.1 02-01-2021 - Se modifica los parámetros que se envian al sistema de Sys Cloud-Center, por motivos
     *                           que cuando se rechaza la tarea, se envía un asignado incorrecto.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.2 08-04-2021 - Se crea validacion para permitir rechazar el producto de cableado ethernet cuando se 
     *                           cancele la tarea y deje grabado en el historial el motivo
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.3 05-05-2021 - Se arregla bug que rechazaba solicitudes de planificacion, solo debe hacerlo por CE
     *                           y se genere el respectivo historial de su rechazo al cancelar la tarea
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 2.0 19-10-2021 - Se realiza llamado a proceso de tracking map para tareas de caso de soporte hal
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 2.1 13-10-2022 - Se realiza validacion para tareas con servicios NG-FIREWALL que seran enviadas
     *                           al Orquestador para validacion de procesos 
     */
    public function cancelarTareaAction()
    {
        $objResponse        = new JsonResponse();
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');

        $serviceProceso     = $this->get('soporte.ProcesoService');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');

        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $datetimeActual     = new \DateTime('now');
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objRequest->getClientIp();

        $strCodEmpresa      = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa  = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");
        $intIdDepartamento  = $objSession->get('idDepartamento');

        $intNumeroTarea        = $objRequest->get('numeroTarea');
        $strNombreTarea        = $objRequest->get('nombreTarea');
        $strNombreProcesoSys   = $objRequest->get('nombre_proceso');
        $strNombrePersona      = $objRequest->get('asignado_nombre');
        $strDepartamento       = $objRequest->get('departamento_nombre');
        $strEmpleado           = $objSession->get('empleado');
        $strDepartamentoAs     = $objSession->get('departamento');

        $intIdDetalle       = $objRequest->get('id_detalle');
        $intIdCaso          = $objRequest->get('id_caso') ? $objRequest->get('id_caso') : 0;
        $strObservacion     = $objRequest->get('observacion');
        $strTipo            = $objRequest->get('tipo');
        $strEstadoDetalle   = ucfirst($strTipo);
        $intIdPerSession    = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $serviceUtil        = $this->get('schema.Util');
        $objPtoCliente      = $objSession->get('ptoCliente');
        $boolEsHal          = false;

        if (is_array($objPtoCliente))
        {
            $intPtoCliente =  $objPtoCliente['id'];
        }
        else
        {
            $intPtoCliente =  $objPtoCliente;
        }
        $arrayParametrosHist = array();
        $arrayParametrosCorreo = array();
        $boolEjecutaEnvioMail = false;
        $strNombreProceso    = "";

        $boolProcesaSysCloud = false;

        $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;
        $arrayParametrosHist["strOpcion"]               = "Historial";        

        $intIdDetalleHist = $objRequest->get('intIdDetalleHist');
        if ($intIdDetalleHist !== '' && !empty($intIdDetalleHist))
        {
            $arrayValidarAccion = $this->get('soporte.SoporteService')->validarAccionTarea(array('intIdDetalle'     => $intIdDetalle,
                                                                                                 'intIdDetalleHist' => $intIdDetalleHist));

            if (!$arrayValidarAccion['boolRespuesta'])
            {
               return $objResponse->setContent(json_encode(array('success'      => false,
                                                                 'seguirAccion' => $arrayValidarAccion['boolRespuesta'],
                                                                 'mensaje'      => $arrayValidarAccion['strMensaje'])));
            }
        }

        if($strTipo == 'rechazada')
        {
            $strObservacion = $objRequest->get('observacion') ? $objRequest->get('observacion') : "Tarea Rechazada";
        }
        else if($strTipo == 'anulada')
        {
            $strObservacion = $objRequest->get('observacion') ? $objRequest->get('observacion') : "Tarea Anulada";
        }
        else
        {
            $strObservacion = $objRequest->get('observacion') ? $objRequest->get('observacion') : "Tarea Cancelada";
        }

        $emSoporte->getConnection()->beginTransaction();
        try
        {
            $strEstadoActualTarea = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($intIdDetalle);

            if($strEstadoActualTarea  != "Cancelada" && $strEstadoActualTarea  != "Rechazada" && $strEstadoActualTarea  != "Finalizada"
                && $strEstadoActualTarea  != "Anulada" )
            {
                if($strPrefijoEmpresa  == 'TN')
                {
                    $arrayParametroInstancia  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne(
                                                                  'CARACTERISTICA_DE_LA_INSTANCIA', 
                                                                  'SOPORTE', 
                                                                  '', 
                                                                  'CARACTERISTICA_ASOCIADA_A_UN_SERVICIO_CREADO_DESDE_EL_ORQUESTADOR',
                                                                  '', 
                                                                  '', 
                                                                  '', 
                                                                  '',
                                                                  '',
                                                                  'TN');
                    
                    $strCaracteristicaInstancia                         = $arrayParametroInstancia['valor1'];
            
                    $arrayParametrosCaracteristicas['caracteristica']   = $strCaracteristicaInstancia;
                    $arrayParametrosNumeroTarea['numeroTarea']          = $intNumeroTarea;
        
                    $arrayDatos     = array ('arrayIdComunicacion'      => $arrayParametrosNumeroTarea,
                                             'arrayCaracteristica'      => $arrayParametrosCaracteristicas,
                                             'strEstado'                => 'Activo');
        
                    $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoTareaCaracteristica")
                                                ->getTareasCaracteristicas($arrayDatos);

                    if (!is_object($arrayRespuesta) && !empty($arrayRespuesta))
                    {
                        $strCaracteristica  = $arrayRespuesta['result'][0]['descripcionCaracteristica'];
                        $intIdServicio      = $arrayRespuesta['result'][0]['valor'];
                    }

                    if(!empty($intIdServicio))
                    {
                        $objServicio        = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio); 
                        
                        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                        {
                            $objProducto        = $objServicio->getProductoId();
                            $objProductoParam   = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objProducto->getId());
                        }
                        
                        if(is_object($objProductoParam))
                        {
                            $strNombreProducto      = $objProductoParam->getDescripcionProducto();
                        }

                        $objServicioProdCarc        = $serviceTecnico->getCaracteristicaServicio($objServicio, $strCaracteristicaInstancia);  
                        
                        if(!empty($objServicioProdCarc))
                        {
                            $intInstanceId = $objServicioProdCarc;
                        }
                    }
                    
                    $arrayAdmiParametro      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->getOne(
                                                                 'PARAMETROS_PRODUCTOS_ORQUESTADOR', 
                                                                 'COMERCIAL', 
                                                                 '', 
                                                                 'PRODUCTOS_PERMITIDOS',
                                                                 '', 
                                                                 '', 
                                                                 '', 
                                                                 '',
                                                                 '',
                                                                 10);

                    $strProductoPermitido    = $arrayAdmiParametro['valor1'];
                    
                    /*
                    * Se verifica si la tarea tiene una caracteristica de INSTANCIA_ID_ORQ
                    * Se verifica que el servicio sea SECURITY NG FIREWALL
                    */
                    
                    if($strCaracteristica  == $strCaracteristicaInstancia && $strProductoPermitido == $strNombreProducto)
                    {  
                    
                        $arrayParametroList     = [
                                    ['parameterName'   => 'idTarea', 
                                     'dataType'        => 'TEXTO', 
                                     'nativeTypeValue' => $intNumeroTarea],

                                    ['parameterName'   => 'Estado', 
                                     'dataType'        => 'TEXTO', 
                                     'nativeTypeValue' => 'ERROR'],

                                    ['parameterName'   => 'Error', 
                                     'dataType'        => 'TEXTO', 
                                     'nativeTypeValue' => 'Se finaliza el flujo de esta instancia. Observacion: '. $strObservacion] //observacion
                                     ];

                        $arrayParametros = array('resultCode'                => 'OK',
                                                 'productName'               => str_replace(" ", "_",$strNombreProducto),
                                                 'instanceId'                => $intInstanceId,
                                                 'processName'               => 'ACTIVACION',
                                                 'nativeValueParameterList'  => $arrayParametroList,
                                                 'Usuario'                   => 'Telcos+');
                        
                        $arrayRespuestaOrq = $this->get('comercial.Orquestador')->getOrquestador($arrayParametros);
                    }
                }
                /* @var $soporteService SoporteService */
                $soporteService = $this->get('soporte.SoporteService');
                //Se eliminan simbolos de tags
                $strObservacion = $soporteService->eliminarSimbolosDeTags($strObservacion);

                $objDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalle);
                $objDetalle->setEsSolucion('N');
                $emSoporte->persist($objDetalle);
                $emSoporte->flush();

                //Se ingresa el historial de la tarea
                $arrayParametrosHist["intDetalleId"]    = $intIdDetalle;
                $arrayParametrosHist["strObservacion"]  = $strObservacion;
                $arrayParametrosHist["strEstadoActual"] = $strEstadoDetalle;
                $arrayParametrosHist["strAccion"]       = $strEstadoDetalle;

                $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                     

                //Función encargada de calcular los tiempos de las tareas.
                $soporteService->calcularTiempoEstado(array('strEstadoActual'   => $arrayParametrosHist["strEstadoActual"],
                                                            'intIdDetalle'      => $intIdDetalle,
                                                            'strTipoReprograma' => null,
                                                            'strUser'           => $strUserSession,
                                                            'strIp'             => $strIpCreacion));

				$strObservacionSeguimiento  = "";		
                if($strTipo == 'rechazada')
                {
                    $strObservacionSeguimiento = "Tarea fue Rechazada , Obs : ".$strObservacion;
                }
                else if($strTipo == 'anulada')
                {
                    $strObservacionSeguimiento = "Tarea fue Anulada , Obs : ".$strObservacion;
                }
                else
                {
                    $strObservacionSeguimiento = "Tarea fue Cancelada , Obs : ".$strObservacion;
                }

                //Se ingresa el seguimiento de la tarea
                $arrayParametrosHist["strObservacion"] = $strObservacionSeguimiento;
                $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                   

                $arrayParametros["intDetalleId"] = $intIdDetalle;

                /*
                 * Se verifica si una tarea tiene asociada una solicitud de facturación
                 */
                $arrayTmpParametros = array(
                                                'accionTarea'             => $strTipo,
                                                'caracteristicaSolicitud' => self::CARACTERISTICA_SOLICITUD,
                                                'detalleId'               => $intIdDetalle,
                                                'estadoActivo'            => self::ESTADO_ACTIVO,
                                                'estadoEliminado'         => self::ESTADO_ELIMINADO,
                                                'feCreacion'              => $datetimeActual,
                                                'usrCreacion'             => $strUserSession,
                                                'ipCreacion'              => $strIpCreacion,
                                           );

                $serviceSoporte = $this->get('soporte.SoporteService');
                $boolRespuesta  = $serviceSoporte->verificarSolicitudFacturacion($arrayTmpParametros);
                /*
                 * Fin Se verifica si una tarea tiene asociada una solicitud de facturación
                 */

                $intTareasAbiertas      = 0;
                $intTareasSolucionadas  = 0;

                if($strTipo == 'rechazada')
                {
                    $boolCrearReasignacionXRechazo          = false;
                    $strTipoReasignacionXRechazo            = "";
                    $intUltimoIdRefAsignadoRechazo          = 0;
                    $strUltimoRefAsignacionNombreRechazo    = "";
                    $intUltimoIdAsignadoRechazo             = 0;
                    $strUltimoAsignadoNombreRechazo         = "";
                    $intIdPersonaEmpresaRolRechazo          = 0;
                    $intIdDepartamentoReasignacionRechazo   = 0;
                    $intIdCantonReasignacionRechazo         = 0;
                    $strMotivoReasignacion                  = "Tarea Reasignada automáticamente por rechazo de tarea";
                    
                    /*
                     * Se obtiene el penúltimo registro de asignaciones, ya que éste tiene el último registro de asignación antes de la asignación
                     * hacia la persona que rechazará
                     */
                    $arrayDetalleAsignacionRechazo  = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                ->getResultadoUltimoDetalleAsignacionTareaRechazada(array("intIdDetalle"  => 
                                                                                                                          $intIdDetalle
                                                                                                                    ));
                    $strMsjErrorAsignacionRechazo   = "";
                    if($arrayDetalleAsignacionRechazo)
                    {
                        $objUltimoDetalleAsignacionRechazo  = $arrayDetalleAsignacionRechazo[0];
                        
                        if(is_object($objUltimoDetalleAsignacionRechazo))
                        {

                            $objInfoPersonaEmpresaRolAsig = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->find($objUltimoDetalleAsignacionRechazo->getPersonaEmpresaRolId());

                            if(is_object($objInfoPersonaEmpresaRolAsig))
                            {
                                $intUltimoIdAsignadoRechazo = $objInfoPersonaEmpresaRolAsig->getDepartamentoId();

                                $objAdmiDepartamento = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                                                                 ->find($intUltimoIdAsignadoRechazo);
                                if(is_object($objAdmiDepartamento))
                                {
                                    $strUltimoAsignadoNombreRechazo = $objAdmiDepartamento->getNombreDepartamento();
                                }

                                $strTipoReasignacionXRechazo    = 'EMPLEADO';
                            }

                            $intUltimoIdRefAsignadoRechazo          = $objUltimoDetalleAsignacionRechazo->getRefAsignadoId();
                            $strUltimoRefAsignacionNombreRechazo    = $objUltimoDetalleAsignacionRechazo->getRefAsignadoNombre();
                            $intIdPersonaEmpresaRolRechazo          = $objUltimoDetalleAsignacionRechazo->getPersonaEmpresaRolId();
                            $intIdDepartamentoReasignacionRechazo   = $objUltimoDetalleAsignacionRechazo->getDepartamentoId();
                            $intIdCantonReasignacionRechazo         = $objUltimoDetalleAsignacionRechazo->getCantonId();
                            $boolCrearReasignacionXRechazo          = true;
                        }
                        else
                        {
                            $strMsjErrorAsignacionRechazo = "No se ha podido obtener un objeto con la información de la última asignación ";
                        }
                    }
                    else
                    {
                        /*
                         * Cuando no existe la penúltima asignación, es decir la tarea solo ha tenido una asignación y el usuario desea rechazarla,
                         * la tarea debería regresar a quién creó la tarea
                         * 
                         */
                        $strUsrCreacionDetalle  = $objDetalle->getUsrCreacion();
                        $intIdTarea             = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                                 ->getMinimaComunicacionPorDetalleId($intIdDetalle);
                        
                        if($strUsrCreacionDetalle)
                        {
                            $objTareaComunicacion   = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($intIdTarea);
                            if(is_object($objTareaComunicacion))
                            {
                                $strEmpresaCodCreaTarea     = $objTareaComunicacion->getEmpresaCod();
                                $arrayParamsPerTarea        = array("strLoginPersona"       => $strUsrCreacionDetalle,
                                                                    "idEmpresa"             => $strEmpresaCodCreaTarea,
                                                                    "estado"                => 'Activo',
                                                                    "strDescripcionTipoRol" => 'Empleado'
                                                                   );

                                $arrayRespuestaPerCreaTarea = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                          ->getResultadoPersonaEmpresaRolPorCriterios($arrayParamsPerTarea);

                                $arrayResultadoPerCreaTarea = $arrayRespuestaPerCreaTarea['resultado'];
                                
                                if ($arrayResultadoPerCreaTarea) 
                                {
                                    if($arrayResultadoPerCreaTarea[0])
                                    {
                                        $intIdPersonaEmpresaRolCreaTarea        = $arrayResultadoPerCreaTarea[0]["idPersonaEmpresaRol"];
                                        $intIdPersonaCreaTarea                  = $arrayResultadoPerCreaTarea[0]["idPersona"];
                                        $strNombreCompletoPersonaCreaTarea      = $arrayResultadoPerCreaTarea[0]["nombres"] . " " .
                                                                                  $arrayResultadoPerCreaTarea[0]["apellidos"];
                                        
                                        $intIdDepartamentoCreaTarea             = $arrayResultadoPerCreaTarea[0]["idDepartamento"];
                                        $strNombreDepartamentoCreaTarea         = $arrayResultadoPerCreaTarea[0]["nombreDepartamento"];
                                        $intIdOficinaPerCreaTarea               = $arrayResultadoPerCreaTarea[0]["idOficina"];

                                        $boolCrearReasignacionXRechazo          = true;
                                        $strTipoReasignacionXRechazo            = "EMPLEADO";
                                        $intUltimoIdRefAsignadoRechazo          = $intIdPersonaCreaTarea;
                                        $strUltimoRefAsignacionNombreRechazo    = $strNombreCompletoPersonaCreaTarea;
                                        $intUltimoIdAsignadoRechazo             = $intIdDepartamentoCreaTarea;
                                        $strUltimoAsignadoNombreRechazo         = $strNombreDepartamentoCreaTarea;
                                        $intIdPersonaEmpresaRolRechazo          = $intIdPersonaEmpresaRolCreaTarea;

                                        $intIdDepartamentoReasignacionRechazo   = $intIdDepartamentoCreaTarea;

                                        if($intIdOficinaPerCreaTarea)
                                        {
                                            $objOficinaPerCreaTarea = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                                  ->find($intIdOficinaPerCreaTarea);
                                            if(is_object($objOficinaPerCreaTarea))
                                            {
                                                $intCantonPerCreaTarea  = $objOficinaPerCreaTarea->getCantonId();
                                            }
                                        }
                                        $intIdCantonReasignacionRechazo         = $intCantonPerCreaTarea;
                                    }
                                }
                                else
                                {
                                    $strMsjErrorAsignacionRechazo = "No se puede obtener la información de la persona empresa rol con el login "
                                                                    .$strUsrCreacionDetalle;
                                }
                            }
                            else
                            {
                                $strMsjErrorAsignacionRechazo = "No se puede obtener la tarea con el id ".$intIdTarea;
                            }
                        }
                        else
                        {
                            $strMsjErrorAsignacionRechazo = "No se puede obtener el usuario de creacion del detalle con id ".$intIdDetalle;
                        }
                    }
                    
                    
                    /*
                     * Se crea el registro para que la tarea quede en estado Asignada y regrese a la última persona que fue asignada 
                     * antes de haber sido asignada a quién está rechazando la tarea
                     */

                    if($boolCrearReasignacionXRechazo)
                    {
                        $intIdDepPerRechaza     = 0;
                        $intIdCantonPerRechaza  = 0;
                        $arrayTo                = array();
                        $objPerRechaza          = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerSession);
                        if(is_object($objPerRechaza))
                        {
                            $objPersonaRechaza  = $objPerRechaza->getPersonaId();
                            
                            if(is_object($objPersonaRechaza))
                            {
                                $arrayInfoPersonaRechazaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                                    ->findBy(array(  'personaId'         => $objPersonaRechaza->getId(),
                                                                                                     'formaContactoId'   => 5,
                                                                                                     'estado'            => "Activo"
                                                                                            ));
                                if($arrayInfoPersonaRechazaFormaContacto)
                                {
                                    foreach($arrayInfoPersonaRechazaFormaContacto as $objInfoPersonaRechazaFormaContacto)
                                    {
                                        $arrayTo[] = $objInfoPersonaRechazaFormaContacto->getValor(); //Correos Persona que Rechaza
                                    }
                                }
                                
                                $arrayParamsPlantillaAsig   = array("codigo"    => "TAREAASIG",
                                                                    "estado"    => array("Activo","Modificado"));

                                $objPlantillaAsig           = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                                             ->findOneBy($arrayParamsPlantillaAsig);

                                $objOficinaPerRechaza   = $objPerRechaza->getOficinaId();
                                $intIdDepPerRechaza     = $objPerRechaza->getDepartamentoId();
                                if(is_object($objOficinaPerRechaza))
                                {
                                    $intIdCantonPerRechaza  = $objOficinaPerRechaza->getCantonId();
                                }
                                
                                if(is_object($objPlantillaAsig))
                                {
                                    $arrayAliasPerRechaza      = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                                                ->getAliasXPlantilla( $objPlantillaAsig->getId(), 
                                                                                                      $strCodEmpresa, 
                                                                                                      $intIdCantonPerRechaza, 
                                                                                                      $intIdDepPerRechaza,
                                                                                                      "NO");
                                    if(isset($arrayAliasPerRechaza) && !empty($arrayAliasPerRechaza))
                                    {
                                        $arrayTo = array_merge($arrayTo, $arrayAliasPerRechaza);
                                    }
                                }
                            }
                        }
                        
                        /*Creando el registro de la reasignación por rechazo*/
                        $objInfoDetalleReasignacionXRechazo = new InfoDetalleAsignacion();
                        $objInfoDetalleReasignacionXRechazo->setDetalleId($objDetalle);
                        $objInfoDetalleReasignacionXRechazo->setAsignadoId($intUltimoIdAsignadoRechazo);
                        $objInfoDetalleReasignacionXRechazo->setAsignadoNombre($strUltimoAsignadoNombreRechazo);
                        $objInfoDetalleReasignacionXRechazo->setTipoAsignado($strTipoReasignacionXRechazo);
                        $objInfoDetalleReasignacionXRechazo->setPersonaEmpresaRolId($intIdPersonaEmpresaRolRechazo);
                        $objInfoDetalleReasignacionXRechazo->setRefAsignadoId($intUltimoIdRefAsignadoRechazo);
                        $objInfoDetalleReasignacionXRechazo->setRefAsignadoNombre($strUltimoRefAsignacionNombreRechazo);
                        $objInfoDetalleReasignacionXRechazo->setDepartamentoId($intIdDepPerRechaza);
                        $objInfoDetalleReasignacionXRechazo->setCantonId($intIdCantonPerRechaza);
                        $objInfoDetalleReasignacionXRechazo->setMotivo($strMotivoReasignacion);
                        $objInfoDetalleReasignacionXRechazo->setUsrCreacion($strUserSession);
                        $objInfoDetalleReasignacionXRechazo->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleReasignacionXRechazo->setIpCreacion($strIpCreacion);
                        $emSoporte->persist($objInfoDetalleReasignacionXRechazo);
                        $emSoporte->flush();

                        //Se ingresa el historial de la tarea
                        $arrayParametrosHist["strObservacion"]  = $strMotivoReasignacion;
                        $arrayParametrosHist["strOpcion"]       = "Historial";
                        $arrayParametrosHist["strEstadoActual"] = "Asignada";
                        $arrayParametrosHist["strAccion"]       = "Asignada";

                        $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                        

                        //Función encargada de calcular los tiempos de las tareas.
                        $soporteService->calcularTiempoEstado(array('strEstadoActual'   => 'Asignada',
                                                                    'intIdDetalle'      => $intIdDetalle,
                                                                    'strTipoReprograma' => null,
                                                                    'strUser'           => $strUserSession,
                                                                    'strIp'             => $strIpCreacion));

                        $strNombreReasignado        = "";

                        if($strTipoReasignacionXRechazo=="CUADRILLA")
                        {
                            $strNombreReasignado    = "la cuadrilla ".$strUltimoAsignadoNombreRechazo;
                        }
                        else
                        {
                            $strNombreReasignado    = $strUltimoRefAsignacionNombreRechazo;
                        }

                        /*Se crea el seguimiento con la información de la reasignación automática por rechazo*/
                        $arrayParametrosHist["strObservacion"] = "Tarea es Regresada y Reasignada a ".$strNombreReasignado;
                        $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                        $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                        
                        /*Envío de Correo al realizar la Reasignación*/
                        $boolPerteneceACaso      = false;

                        $objCountCaso            = $emSoporte->getRepository('schemaBundle:InfoDetalle')->tareaPerteneceACaso($intIdDetalle);

                        $strNumeracionReferencia = "";

                        if($objCountCaso[0]['caso'] != 0)
                        {
                            $boolPerteneceACaso       = true;
                            $arrayHipotesis           = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getCasoPadreTarea($intIdDetalle);
                            if(is_object($arrayHipotesis[0]))
                            {
                                $objCaso                  = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                                      ->find($arrayHipotesis[0]->getCasoId()->getId());
                                if(is_object($objCaso))
                                {
                                    $intIdCaso                = $objCaso->getId();
                                    $strNumeroCasoTarea       = $objCaso->getNumeroCaso();
                                    $strNumeracionReferencia  = ' al Caso #' . $strNumeroCasoTarea;
                                }
                            }
                        }
                        else
                        {
                            if($objDetalle)
                            {
                                //Se obtiene el numero de la tarea en base al id_detalle
                                $intNumeroTarea             = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                                             ->getMinimaComunicacionPorDetalleId($intIdDetalle);

                                $strNumeroCasoTarea         = $intNumeroTarea ? $intNumeroTarea : "";
                                $strNumeracionReferencia    = ' a la Actividad #' . $strNumeroCasoTarea;
                            }
                        }

                        if($strTipoReasignacionXRechazo == "CUADRILLA")
                        {
                            if($intIdPersonaEmpresaRolRechazo)
                            {
                                $objInfoPersonaEmpresaRolReasignacion = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                    ->find($intIdPersonaEmpresaRolRechazo);
                                if(is_object($objInfoPersonaEmpresaRolReasignacion))
                                {
                                    $objDepartamentoReasignacion = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                             ->find($objInfoPersonaEmpresaRolReasignacion->getDepartamentoId());
                                }
                            }
                        }
                        else
                        {
                            $objDepartamentoReasignacion = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                     ->find($intUltimoIdAsignadoRechazo);
                        }

                        $intIdPersonaReasignacion   = 0;
                        if($intUltimoIdRefAsignadoRechazo || $strTipoReasignacionXRechazo == "EMPRESAEXTERNA")
                        {
                            if($strTipoReasignacionXRechazo == "EMPRESAEXTERNA")
                            {
                                $objContratista = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intUltimoIdAsignadoRechazo);
                                if(is_object($objContratista))
                                {
                                    $intIdPersonaReasignacion  = $objContratista->getId();
                                }
                            }
                            else
                            {
                                $intIdPersonaReasignacion = $intUltimoIdRefAsignadoRechazo;
                            }

                            $arrayInfoPersonaReasignadaFormaContacto  = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                                    ->findBy(array(  'personaId'         => $intIdPersonaReasignacion,
                                                                                                     'formaContactoId'   => 5,
                                                                                                     'estado'            => "Activo"
                                                                                            ));

                            if($arrayInfoPersonaReasignadaFormaContacto)
                            {
                                foreach($arrayInfoPersonaReasignadaFormaContacto as $objInfoPersonaReasignadaFormaContacto)
                                {
                                    $arrayTo[] = $objInfoPersonaReasignadaFormaContacto->getValor(); //Correos Persona Reasignada
                                }
                            }
                        }

                        $objTarea   = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($objDetalle->getTareaId());
                        $strAsunto  = "Rechazo de asignacion";

                        /*
                          OBTENCION DEL CANTÓN DEL ENCARGADO DE LA TAREA
                         */
                        $strCodEmpresaReasignacion      = '';
                        $intIdDepartamentoReasignacion  = 0;

                        if(is_object($objDepartamentoReasignacion))
                        {
                            $strCodEmpresaReasignacion      = $objDepartamentoReasignacion->getEmpresaCod();
                            $intIdDepartamentoReasignacion  = $objDepartamentoReasignacion->getId();
                        }

                        if(is_object($objInfoDetalleReasignacionXRechazo) 
                            && ( $strTipoReasignacionXRechazo == "EMPLEADO" || $strTipoReasignacionXRechazo == "CUADRILLA"))
                        {
                            $objInfoPersonaEmpresaRolReasignacion = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                ->find($intIdPersonaEmpresaRolRechazo);
                        }
                        $intIdCantonReasignacion = 0;
                        if(is_object($objInfoPersonaEmpresaRolReasignacion) 
                            && ( $strTipoReasignacionXRechazo == "EMPLEADO" || $strTipoReasignacionXRechazo == "CUADRILLA"))
                        {

                            $objOficinaReasignacion = $objInfoPersonaEmpresaRolReasignacion->getOficinaId();
                            if(is_object($objOficinaReasignacion))
                            {
                                $intIdCantonReasignacion    = $objOficinaReasignacion->getCantonId();
                            }
                        }

                        if(is_object($objTarea))
                        {
                            $strNombreProceso = $objTarea->getProcesoId()->getNombreProceso();
                        }

                        $strAsunto = $strAsunto . " | PROCESO: ".$strNombreProceso;

                        $arrayParametros = array(
                                                    'idCaso'            => $intIdCaso,
                                                    'perteneceACaso'    => $boolPerteneceACaso,
                                                    'numeracion'        => $strNumeroCasoTarea,
                                                    'referencia'        => $strNumeracionReferencia,
                                                    'asignacion'        => $objInfoDetalleReasignacionXRechazo,
                                                    'persona'           => $objPersonaRechaza ? $objPersonaRechaza : false,
                                                    'nombreProceso'     => $strNombreProceso,
                                                    'nombreTarea'       => $objTarea->getNombreTarea() ? $objTarea->getNombreTarea() : '',
                                                    'estado'            => $objTarea->getEstado() ? $objTarea->getEstado() : '',
                                                    'empleadoLogeado'   => $objSession->get('empleado'),
                                                    'empresa'           => $strPrefijoEmpresa,
                                                    'detalle'           => $objDetalle,
                                                    'observacion'       => $strMotivoReasignacion ? $strMotivoReasignacion : "");

                        $boolEnviaNotificacion = true;
                        //Se agrega validacion solo para TN, para que se envie la notificacion es necesario que llegue un departamento
                        if($strPrefijoEmpresa == "TN")
                        {
                            if(is_object($objDepartamentoReasignacion))
                            {
                                $boolEnviaNotificacion = true;
                            }
                            else
                            {
                                $boolEnviaNotificacion = false;
                            }
                        }

                        if($boolEnviaNotificacion)
                        {
                            $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                            $serviceEnvioPlantilla->generarEnvioPlantilla(
                                                                            $strAsunto, 
                                                                            $arrayTo, 
                                                                            'TAREAASIG', 
                                                                            $arrayParametros, 
                                                                            $strCodEmpresaReasignacion, 
                                                                            $intIdCantonReasignacion, 
                                                                            $intIdDepartamentoReasignacion
                                                                         );
                            $arrayParametrosCorreo['codPlantilla']   = "CASORECHAZACLI";
                            $arrayParametrosCorreo['asunto']        = "Rechazo del caso";
                            $boolEjecutaEnvioMail = true;
                        }

                        //Notificación a syscloud por rechazo de tarea.
                        $objInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")->find($intNumeroTarea);
                        if (is_object($objInfoComunicacion) && strtoupper($strTipoReasignacionXRechazo) == 'EMPLEADO')
                        {
                            $serviceProceso->putTareasSysCluod(array('strNombreTarea'      => $strNombreTarea,
                                                                     'strNombreProceso'    => $strNombreProcesoSys,
                                                                     'strObservacion'      => $strObservacion,
                                                                     'strFechaApertura'    => date("Y-m-d"),
                                                                     'strHoraApertura'     => date('H:i:s'),
                                                                     'strUser'             => $strUserSession,
                                                                     'strIpAsigna'         => $strIpCreacion,
                                                                     'strUserAsigna'       => $strEmpleado,
                                                                     'strDeparAsigna'      => $strDepartamentoAs,
                                                                     'strUserAsignado'     => $strUltimoRefAsignacionNombreRechazo,
                                                                     'strDeparAsignado'    => $strUltimoAsignadoNombreRechazo,
                                                                     'objInfoComunicacion' => $objInfoComunicacion));
                        }
                    }
                    else
                    {
                        //No se pudo crear la reasignación
                        throw new NotFoundHttpException('No se pudo realizar la reasignación para la tarea con id_detalle '
                                                        .$intIdDetalle." Motivo: ".$strMsjErrorAsignacionRechazo);
                    }
                }
                else
                {
                    if($strTipo == 'cancelada')
                    {
                        $strEstadoCableado = "Rechazada";
                        $arrayParametrosCorreo['codPlantilla']   = "CASOCANCELACLI";
                        $arrayParametrosCorreo['asunto']        = "Cancela el caso";
                        $boolEjecutaEnvioMail = true;

                        // Valida que exista el objeto
                        $intIdSolicitud = $objDetalle->getDetalleSolicitudId();
                        if(!empty($intIdSolicitud))
                        {
                            /* Iniciamos mejora para rechazar la solicitud de cableado */
                            $objDetalleSol = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
                            if(is_object($objDetalleSol))
                            {
                                // Si es un producto adicional rechazamos tambien el servicio
                                $arrayParametroTipos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                                            'Solicitud cableado ethernet','','','','','','18');
                                if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                                {
                                    $objCableParametro = $arrayParametroTipos[0];
                                }
                                // Validamos que solo sea solicitudes de CE
                                $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneById($objDetalleSol->getTipoSolicitudId()->getId());
                                if ($objTipoSolicitud->getDescripcionSolicitud() == $objCableParametro['valor2'])
                                {
                                    // Actualizamos el estado de la solicitud
                                    $objDetalleSol->setEstado($strEstadoCableado);
                                    $emComercial->persist($objDetalleSol);
                                    $emComercial->flush();
                                    // Ingresamos el historial del detalle de la solicitud
                                    $entityDetalleSolHist = new InfoDetalleSolHist();
                                    $entityDetalleSolHist->setDetalleSolicitudId($objDetalleSol);
                                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleSolHist->setUsrCreacion($strUserSession);
                                    $entityDetalleSolHist->setEstado($strEstadoCableado);
                                    $emComercial->persist($entityDetalleSolHist);
                                    $emComercial->flush();
                                    // Verificamos si viene como producto adicional
                                    $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findOneById($objDetalleSol->getServicioId()->getId());
                                    if ($objServicio->getProductoId() != null &&
                                        $objServicio->getProductoId()->getId() == $objCableParametro['valor1'])
                                    {
                                        $objServicio->setEstado($strEstadoCableado);
                                        $emComercial->persist($objServicio);
                                        $emComercial->flush();
                                    }
                                    // Ingresamos el historial del detalle de la solicitud
                                    $objServicioHist = new InfoServicioHistorial();
                                    $objServicioHist->setServicioId($objServicio);
                                    $objServicioHist->setObservacion('Se rechaza el producto cableado ethernet');
                                    $objServicioHist->setIpCreacion($strIpCreacion);
                                    $objServicioHist->setUsrCreacion($strUserSession);
                                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                                    $objServicioHist->setEstado($strEstadoCableado);
                                    $emComercial->persist($objServicioHist);
                                    $emComercial->flush();
                                }
                            }
                        }
                    }

                    //Función que permite realizar el cálculo del tiempo total de la tarea.
                    $soporteService->calcularTareaTiempoAsignacion(array('intIdDetalle'   => $intIdDetalle,
                                                                         'intIdCaso'      => $intIdCaso != 0 ? $intIdCaso : null,
                                                                         'strObservacion' => $strObservacionSeguimiento,
                                                                         'strUser'        => $strUserSession,
                                                                         'strIp'          => $strIpCreacion));

                    if ($intIdCaso)
                    {
                        $intTareasAbiertas      = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                            ->getCountTareasAbiertas($intIdCaso, 'Abiertas');
                        $intTareasSolucionadas  = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                            ->getCountTareasAbiertas($intIdCaso, 'FinalizadasSolucion');
                    }

                    //Notificación a syscloud por anulación o cancelación de tarea.
                    $objInfoDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);
                    if (is_object($objInfoDetalle) && strpos($objInfoDetalle->getObservacion(),'Sys Cloud-Center') !== false)
                    {
                        $boolProcesaSysCloud = true;
                    }

                    if (!$boolProcesaSysCloud)
                    {
                        $arrayInfoDetalleAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                ->findByDetalleId($intIdDetalle);

                        foreach ($arrayInfoDetalleAsignacion as $objDetalleAsignacion)
                        {
                            if (is_object($objDetalleAsignacion))
                            {
                                $objAdmiDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                        ->find($objDetalleAsignacion->getAsignadoId());
                            }

                            if (is_object($objAdmiDepartamento))
                            {
                                $arrayAdmiParametroUsrs = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS',
                                                 'SOPORTE','','','',
                                                  $objAdmiDepartamento->getNombreDepartamento(),'','','','');
                            }

                            if (!empty($arrayAdmiParametroUsrs) && $arrayAdmiParametroUsrs > 0)
                            {
                                $boolProcesaSysCloud = true;
                                break;
                            }
                        }
                    }

                    if ($boolProcesaSysCloud)
                    {
                        $objInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")->find($intNumeroTarea);
                        if (is_object($objInfoComunicacion))
                        {
                            $serviceProceso->putTareasSysCluod(array('strNombreTarea'      => $strNombreTarea,
                                                                     'strNombreProceso'    => $strNombreProcesoSys,
                                                                     'strObservacion'      => $strObservacion,
                                                                     'strFechaApertura'    => date("Y-m-d"),
                                                                     'strHoraApertura'     => date('H:i:s'),
                                                                     'strUser'             => $strUserSession,
                                                                     'strIpAsigna'         => $strIpCreacion,
                                                                     'strUserAsigna'       => $strEmpleado,
                                                                     'strDeparAsigna'      => $strDepartamentoAs,
                                                                     'strUserAsignado'     => $strNombrePersona,
                                                                     'strDeparAsignado'    => $strDepartamento,
                                                                     'objInfoComunicacion' => $objInfoComunicacion,
                                                                     'boolBanderaSyscloud' => 'S'));
                        }
                    }
                }

                /******************************************************/
                /******************Envio de sms y correo***************/
                if ($intIdCaso && $boolEjecutaEnvioMail)
                {
                    $objInfoCaso      = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($intIdCaso);
                    if (($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Tecnico')||
                       ($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Arcotel'))
                    {
                        $arrayAfectacionPadres = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                           ->getRegistrosAfectadosTotalXCaso($intIdCaso,'Cliente','Data');
                        foreach($arrayAfectacionPadres as $arrayAfectadoPadre)
                        {
                            $arrayParametrosSMS = array();
                            $arrayParametrosSMS['puntoId']      = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosSMS['personaId']    = "";
                            $arrayParametrosSMS['destinatario'] = "CLI";
                            $arrayParametrosSMS['tipoEnvio']    = "OUT";
                            $arrayParametrosSMS['tipoNotifica'] = "SMS";
                            $arrayParametrosSMS['empresa']      = $strCodEmpresa;
                            $arrayParametrosSMS['tipoEvento']   = strtoupper($strTipo);
                            $arrayParametrosSMS['usuario']      = $objSession->get('user');
                            $arrayParametrosSMS['casoId']       = $objInfoCaso->getId();
                            $arrayParametrosSMS['detalleId']    = "";
                            $arrayParametrosSMS['asignacion']   = "";
                            $soporteService->enviaSMSCasoCliente($arrayParametrosSMS);
                            $arrayParametrosCorreo['puntoId']        = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosCorreo['usuario']        = $objSession->get('user');
                            $arrayParametrosCorreo['caso']           = $objInfoCaso;
                            $arrayParametrosCorreo['idDepartamento'] = $intIdDepartamento;
                            $arrayParametrosCorreo['empresa']        = $strCodEmpresa;
                            $arrayParametrosCorreo['observacion']    =$strObservacion;
                            $soporteService->enviaCorreoClientesCasos($arrayParametrosCorreo);
                        }
                    }

                    // Notificacion al usuario                                  
                    $objAdmiFormaContacto      = $emSoporte->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->findPorDescripcionFormaContacto('Correo Electronico'); 
                    
                    //Devuelve la última asignación de la tarea
                    $objInfoDetalleAsignacion  = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                           ->getUltimaAsignacion($intIdDetalle);
                    
                    $strNumeracion           = '';
                    $strNumeracionReferencia = '';
                    $intCanton               = 0;
                    $strEmpresa              = $objInfoCaso->getEmpresaCod();
                    $intDepartamento         = 0;
                    $objPersona              = null;
                    $strNombreProceso        = '';
                    $arrayTo                 = array();
                    
                    if (is_object($objInfoDetalleAsignacion) && 
                        is_object($objAdmiFormaContacto)     && 
                        is_object($objInfoCaso)              &&
                        is_object($objDetalle))
                    {
                        
                        $objPersona                 = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->findOneByLogin($objInfoDetalleAsignacion->getUsrCreacion());  
                        
                        $intReceptor                = $objInfoDetalleAsignacion->getRefAsignadoId();
                    
                        $arrayInfoDetalleHistorial  = $emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')
                                                                ->getPrimerAsignado($objDetalle->getUsrCreacion(),
                                                                                    $objInfoCaso->getEmpresaCod(),
                                                                                    $objAdmiFormaContacto->getId());
                        
                        $objPersonaFormaContacto    = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                  ->findOneBy(array(
                                                                              'personaId'        => $intReceptor,
                                                                              'formaContactoId'  => $objAdmiFormaContacto->getId(),
                                                                              'estado'           => "Activo"
                                                                             )
                                                                        );  
                        
                        $strNumeracion            = $objInfoCaso->getNumeroCaso();
                        $strNumeracionReferencia  = ' al Caso #' . $strNumeracion; 
                        
                        if (!empty($arrayInfoDetalleHistorial))
                        {                        
                            $intCanton          = $arrayInfoDetalleHistorial['cantonId'];                              
                            $intDepartamento    = $arrayInfoDetalleHistorial['departamentoId'];
                            $arrayTo[]          = $arrayInfoDetalleHistorial['correo']; //Correo Persona Que asigno el caso por primera vez
                        }
                        
                        if(is_object($objPersonaFormaContacto))
                        {
                           $arrayTo[] = $objPersonaFormaContacto->getValor(); //Correo Persona Asignada
                        }  
                    }
                                               
                    $objTarea   = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($objDetalle->getTareaId());                    
                
                    if(is_object($objTarea))
                    {
                        $strNombreProceso = $objTarea->getProcesoId()->getNombreProceso();
                    }
                                                                                                                                
                    $arrayParametros = array(
                                        'idCaso'            => $intIdCaso,
                                        'idDetalle'         => $intIdDetalle,
                                        'perteneceACaso'    => true ,//A
                                        'numeracion'        => $strNumeracion,//
                                        'referencia'        => $strNumeracionReferencia,
                                        'asignacion'        => $objInfoDetalleAsignacion,
                                        'persona'           => is_object($objPersona) ? $objPersona : null,
                                        'nombreProceso'     => $strNombreProceso,
                                        'nombreTarea'       => is_object($objTarea) ? $objTarea->getNombreTarea() : '',
                                        'estado'            => 'Cancelado',
                                        'empleadoLogeado'   => $intIdPerSession,
                                        'empresa'           => $strPrefijoEmpresa,                                    
                                        'observacion'       => $strObservacion
                                       );                    
                    
                    $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                    $serviceEnvioPlantilla->generarEnvioPlantilla(
                                                                    "Cancelación Tarea", 
                                                                    $arrayTo, 
                                                                    'TAREACANCE', 
                                                                    $arrayParametros, 
                                                                    $strEmpresa, 
                                                                    $intCanton, 
                                                                    $intDepartamento
                                                                 );                    
                }
                
                $emSoporte->getConnection()->commit();
                $objJson = json_encode(array('success'=>true,'tareasAbiertas'=>$intTareasAbiertas,'tareasSolucionadas'=>$intTareasSolucionadas));


                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $intIdDetalle;
                $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
                $objServiceSoporte                          = $this->get('soporte.SoporteService');
                $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
            }
            else
            {
                $objJson = json_encode(array('success'=>true,'mensaje'=>"cerrada"));
            }

            if ($strTipo == 'cancelada')
            {
                $boolEsHal = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                       ->isAsignadoHal(array( 'intDetalleId' => $intIdDetalle)); 

                if($boolEsHal)
                { 
                   
                    $strCommand = 'nohup php /home/telcos/app/console Envia:Tracking ';
                    $strCommand = $strCommand . escapeshellarg($strUserSession). ' ';
                    $strCommand = $strCommand . escapeshellarg($strIpCreacion). ' ';
                    $strCommand = $strCommand . '"Tarea Cancelada" ';
                    $strCommand = $strCommand . escapeshellarg($intIdDetalle). ' ';

                    $strCommand = $strCommand .'>/dev/null 2>/dev/null &';
                    shell_exec($strCommand);
                }
            }
        }
        catch (\Exception $e)
        {
            if($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $emSoporte->getConnection()->close();
            
            $serviceUtil->insertError(  'Telcos+', 
                                        'TareasController->cancelarTareaAction', 
                                        $e->getMessage(), 
                                        $strUserSession, 
                                        $strIpCreacion);
            $objJson = json_encode(array('success'=>false,'mensaje'=>"Ha ocurrido un error. Por favor notificar a Sistemas"));
        }
        
        $objResponse->setContent($objJson);        
        return $objResponse;
    }
     
    /**
     * @Secure(roles="ROLE_197-38")
     * 
     * Funcion que sirve para llamar al service de Finalizar Tarea
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 1.0
     * @version 1.1 21-07-2015
     *
     * @author Modificado Edson Franco <efranco@telconet.ec>
     * @version 1.2 11-12-2015 - Se realiza el ajuste para que enviar parámetros necesarios para verificar si la tarea a finalizar tiene una
     *                           solicitud de facturación
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 30-05-2016 Se agrega el Login Afectado como parametro en la plantilla
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 08-07-2016 Se realiza ajustes para enviar la longitud y latitud cuando se finaliza una tarea de corte de fibra de un tipo de
     *                         caso Backbone
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 08-11-2016 Se registra el tiempo total de la tarea al ser finalizada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 09-08-2017 -  Se ontiene el departamento del usuario en session
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 26-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
     *                           realizar en la tarea es válida.
     * @author Modificado: Néstor Naula <nnaulal@telconet.ec>
     * @version 1.7 23-10-2019 -  Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Centerz.
     * @since 1.6
     * 
     * @author Modificado: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.8 31-03-2020 -  Se agrega variable que establece si la tarea es interdepartamental
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 04-01-2021 - Se elimina el proceso de notificación a syscloud por motivos que el código fue
     *                           movido al service que tiene el método de finalizar la tarea.
     *
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 2.0 15-01-2021 - Se agrega tarea al historico del detalle de tarea, para seguimiento
     *                           de la tarea principal
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 2.1 26-05-2021 - Se agrega variable departamentoId, utilizado en árbol de fin de tareas.
     * 
     * @author Pero Velez <psvelez@telconet.ec>
     * @version 2.2 01-10-2021 - Se agrega variable boolFinalTareaAnterior, para validar la terminacion
     *                           de la tarea con la tarea anterior.
     * 
     */
    public function finalizarTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $objPeticion           = $this->get('request');
        $objSession            = $objPeticion->getSession();
        $intCodEmpresa         = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $intIdDetalle          = $objPeticion->get('id_detalle');
        $strObservacion        = $objPeticion->get('observacion') ? $objPeticion->get('observacion') : "Tarea finalizada";
        $objTarea              = $objPeticion->get('tarea');        
        $intTiempoTotal        = $objPeticion->get('tiempo_total');
        $strTiempoCierre       = $objPeticion->get('tiempo_cierre');
        $strHoraCierre         = $objPeticion->get('hora_cierre');
        $intTiempoEjecucion    = $objPeticion->get('tiempo_ejecucion');
        $intHoraEjecucion      = $objPeticion->get('hora_ejecucion');
        $objClientes           = $objPeticion->get('clientes') ? $objPeticion->get('clientes') : "";
        $strEsSolucion         = $objPeticion->get('esSolucion');        
        $strFechaApertura      = $objPeticion->get('fecha_apertura');
        $strHoraApertura       = $objPeticion->get('hora_apertura');
        $strTareaFinal         = ($objPeticion->get('tarea_final') ? $objPeticion->get('tarea_final') : "");
        $strObservacionManga   = ($objPeticion->get('observacionManga') ? $objPeticion->get('observacionManga') : "");
        $floatLongitud         = ($objPeticion->get('longitud') ? $objPeticion->get('longitud') : "");
        $floatLatitud          = ($objPeticion->get('latitud') ? $objPeticion->get('latitud') : "");
        $floatLongitudManga1   = ($objPeticion->get('longitud') ? $objPeticion->get('longitudManga1') : "");
        $floatLatitudManga1    = ($objPeticion->get('latitud') ? $objPeticion->get('latitudManga1') : "");
        $floatLongitudManga2   = ($objPeticion->get('longitud') ? $objPeticion->get('longitudManga2') : "");
        $floatLatitudManga2    = ($objPeticion->get('latitud') ? $objPeticion->get('latitudManga2') : "");
        $strDuracionTarea      = ($objPeticion->get('duracionTarea') ? $objPeticion->get('duracionTarea') : "");
        $strUsrCreacion        = $objPeticion->getSession()->get('user');
        $strIpCreacion         = $objPeticion->getClientIp();
        $strEmpleado           = $objPeticion->getSession()->get('empleado');
        $strPrefijoEmpresa     = $objPeticion->getSession()->get('prefijoEmpresa');
        $intIdDepartamento     = $objPeticion->getSession()->get('idDepartamento');
        $strDepartamentoAs     = $objPeticion->getSession()->get('departamento');
        $booleanRegistroActivos= $this->get('security.context')->isGranted('ROLE_197-6779');
        $idCaso                = "";
        $intNumeroTarea        = $objPeticion->get('numeroTarea');
        $strNombreTarea        = $objPeticion->get('nombre_tarea');
        $strNombreProceso      = $objPeticion->get('nombre_proceso');
        $strNombrePersona      = $objPeticion->get('asignado_nombre');
        $strDepartamento       = $objPeticion->get('departamento_nombre');
        $boolEsInterdep        = $objPeticion->get('esInterdepartamental');
        $intIdMotivoFinaliza   = $objPeticion->get('idMotivoFinaliza');
        $intIdFinTarea         = $objPeticion->get('idFinTarea');

        $strGuardar            = $objPeticion->get('strGuardar');
        $strIndisponibilidadI  = $objPeticion->get('strIndisponibilidadI');
        $strTipoI              = $objPeticion->get('strTipoI');
        $intTiempoAfectacionI  = $objPeticion->get('intTiempoAfectacionI');
        $strMasivoI            = $objPeticion->get('strMasivoI');
        $intComboResponsableI  = $objPeticion->get('intComboResponsableI');
        $intClientesAfectadosI = $objPeticion->get('intClientesAfectadosI');
        $strObservacionesI     = $objPeticion->get('strObservacionesI');
        $strOltI               = $objPeticion->get('strOltI');
        $strPuertoI            = $objPeticion->get('strPuertoI');
        $strCajaI              = $objPeticion->get('strCajaI');
        $strSplitterI          = $objPeticion->get('strSplitterI');
        
        
        $boolProcesaSysCloud   = false;

        $emSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $objServiceUtil        = $this->get('schema.Util');
        $serviceProceso        = $this->get('soporte.ProcesoService');

        $strFechaFin            = explode("T", $strTiempoCierre);
        $strHoraFin             = explode("T", $strHoraCierre);
        $objDate                = date_create(date('Y-m-d H:i', strtotime($strFechaFin[0] . ' ' . $strHoraFin[1])));
        $boolFinalTareaAnterior = false;	
        $strFinalTareaAnterior   = ($objPeticion->get('boolFinalTareaAnterior') ? $objPeticion->get('boolFinalTareaAnterior') : "false");

        if ($strFinalTareaAnterior == "true")
        {
            $boolFinalTareaAnterior = true;
        }

        $arrayParametros = array(
                                    'idEmpresa'               => $intCodEmpresa,
                                    'prefijoEmpresa'          => $strPrefijoEmpresa,
                                    'idCaso'                  => $idCaso,
                                    'idDetalle'               => $intIdDetalle,
                                    'intIdDepartamento'       => $intIdDepartamento,
                                    'tarea'                   => $objTarea,
                                    'tiempoTotal'             => $intTiempoTotal,
                                    'fechaCierre'             => $strTiempoCierre,
                                    'horaCierre'              => $strHoraCierre,
                                    'fechaEjecucion'          => $intTiempoEjecucion,
                                    'horaEjecucion'           => $intHoraEjecucion,
                                    'clientes'                => $objClientes,
                                    'fechaApertura'           => $strFechaApertura,
                                    'horaApertura'            => $strHoraApertura,
                                    'tareaFinal'              => $strTareaFinal,            
                                    'idAsignado'              => "",
                                    'esSolucion'              => $strEsSolucion,
                                    'observacion'             => $strObservacion,
                                    'jsonMateriales'          => "",
                                    'empleado'                => $strEmpleado,
                                    'usrCreacion'             => $strUsrCreacion,
                                    'ipCreacion'              => $strIpCreacion,
                                    'accionTarea'             => 'finalizada',
                                    'observacionManga'        => $strObservacionManga,
                                    'longitud'                => $floatLongitud,
                                    'latitud'                 => $floatLatitud,
                                    'longitudManga1'          => $floatLongitudManga1,
                                    'latitudManga1'           => $floatLatitudManga1,
                                    'longitudManga2'          => $floatLongitudManga2,
                                    'latitudManga2'           => $floatLatitudManga2,
                                    'strDuracionTarea'        => $strDuracionTarea,
                                    'caracteristicaSolicitud' => self::CARACTERISTICA_SOLICITUD,
                                    'detalleId'               => $intIdDetalle,
                                    'estadoActivo'            => self::ESTADO_ACTIVO,
                                    'estadoEliminado'         => self::ESTADO_ELIMINADO,
                                    'estadoAprobado'          => self::ESTADO_APROBADO,
                                    'feCreacion'              => new \DateTime(),
                                    'permiteRegistroActivos'  => $booleanRegistroActivos,
                                    'esInterdepartamental'    => $boolEsInterdep,
                                    'numeroTarea'             => $intNumeroTarea,
                                    'idMotivoFinaliza'        => $intIdMotivoFinaliza,
                                    'boolEsAppSyscloud'       => false,
                                    'idFinTarea'              => $intIdFinTarea,
                                    'departamentoId'          => $intIdDepartamento,
                                    'boolFinalTareaAnterior'  => $boolFinalTareaAnterior,
                                    'strGuardar'                 => $strGuardar,
                                    'strIndisponibilidadI'       => $strIndisponibilidadI,
                                    'strTipoI'                   => $strTipoI,
                                    'intTiempoAfectacionI'       => $intTiempoAfectacionI,
                                    'strMasivoI'                 => $strMasivoI,
                                    'intComboResponsableI'       => $intComboResponsableI,
                                    'intClientesAfectadosI'      => $intClientesAfectadosI,
                                    'strObservacionesI'          => $strObservacionesI,
                                    'strOltI'                    => $strOltI,
                                    'strPuertoI'                 => $strPuertoI,
                                    'strCajaI'                   => $strCajaI,
                                    'strSplitterI'               => $strSplitterI
                                );

        try
        {      
            /* @var $serviceSoporte SoporteService */
            $serviceSoporte = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            $intIdDetalleHist = $objPeticion->get('intIdDetalleHist');
            if ($intIdDetalleHist !== '' && !empty($intIdDetalleHist))
            {
                $arrayValidarAccion = $serviceSoporte->validarAccionTarea(array('intIdDetalle'     => $intIdDetalle,
                                                                                'intIdDetalleHist' => $intIdDetalleHist));

                if (!$arrayValidarAccion['boolRespuesta'])
                {
                return $respuesta->setContent(json_encode(array('success'      => false,
                                                                'seguirAccion' => $arrayValidarAccion['boolRespuesta'],
                                                                'mensaje'      => $arrayValidarAccion['strMensaje'])));
                }
            }
            
            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            $arrayRespuestaArray = $serviceSoporte->finalizarTarea($arrayParametros);
            
            $objAdmiCaracteristicaSer = $emSoporte->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'INSTANCIA_ID_ORQ',
                                                       'estado'                    => 'Activo'));

            if (is_object($objAdmiCaracteristicaSer) && !empty($objAdmiCaracteristicaSer))
            {
                $objInfoTareaCaracteristicaSer = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy(array ('tareaId'          => $intNumeroTarea,
                                           'caracteristicaId' => $objAdmiCaracteristicaSer->getId(),
                                           'estado'           => 'Activo'));
                if(is_object($objInfoTareaCaracteristicaSer))
                {
                    $serviceOrquestador  = $this->get('comercial.Orquestador');
                    $arrayDatosTarea = array('intTarea'    => $intNumeroTarea,
                                             'idInstancia' => $objInfoTareaCaracteristicaSer->getValor());
                    $arrayRespOrq = $serviceOrquestador->putCerrarTareaOrq($arrayDatosTarea);

                }
                
            }

        }
        catch(\Exception $objException)
        {
            $strMessage = 'Error al finalizar la tarea, si el problema persiste comunique a Sistemas.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'TareasController->finalizarTareaAction',
                                          $objException->getMessage(),
                                          $strUsrCreacion,
                                          $strIpCreacion);

            $arrayRespuestaArray = array('success' => false, 'mensaje' => $strMessage);
        }

        $arrayResultado = json_encode($arrayRespuestaArray);
        //----------------------------------------------------------------------*/
        //----------------------------------------------------------------------*/
        try
        {
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $arrayParametrosHE = array('intIdEmpresa'            => $intCodEmpresa,
                                           'intIdDetalle'            => $intIdDetalle,
                                           'strIdTarea'              => $intIdFinTarea,
                                           'intNumeroTarea'          => $intNumeroTarea,
                                           'strUsrCreacion'          => $strUsrCreacion);
                $arrayRespuestaArray = $serviceSoporte->crearCreacionHETareaFinalizada($arrayParametrosHE);
            }
         }
         catch(\Exception $objException)
         {
            $strMessage = 'Error al finalizar la tarea, si el problema persiste comunique a Sistemas.';
            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }
            $objServiceUtil->insertError('Telcos+',
                                         'TareasController->finalizarTareaAction.crearCreacionHETareaFinalizada',
                                         $objException->getMessage(),
                                         $strUsrCreacion,
                                         $strIpCreacion);
            $arrayRespuestaArray = array('success' => false, 'mensaje' => $strMessage);
         }
        //------------------------------------FIN----------------------------------------*/
        $respuesta->setContent($arrayResultado);
        return $respuesta;
    }

    public function obtenerDiferenciaFechas($fechaFin, $fechaInicio){

     
	   
 	    $inicio =  date_format($fechaInicio, 'Y-m-d H:i');//.' '.date_format($fechaInicio, 'H:i').' ';
 	    $fin    =  date_format($fechaFin, 'Y-m-d H:i');//.' '.date_format($fechaFin, 'H:i').' '; 	     	 
 	    
 	    
 	     	
	    $fechaInicio = strtotime(date_format($fechaInicio, 'Y-m-d'));
	    $fechaFin    = strtotime(date_format($fechaFin, 'Y-m-d'));		    	 
	    
	   
	    $horaInicio = explode(":",explode(" ",$inicio)[1]);
	    $horaFin    = explode(":",explode(" ",$fin)[1]);	    	   
	    
	    $dif = $fechaFin - $fechaInicio;
	    
	    
	    
	    $numeroDias = $dif/60/60/24;
	    
	  
	    
	    if($numeroDias>0){
	    
		$numeroDias = $numeroDias -1 ;
		
		$minutosInicio = (24*60) - ( $horaInicio[0]*60 + $horaInicio[1] );
		$minutosFin    = $horaFin[0]*60 + $horaFin[1];
		
		$minutosTotales = $minutosInicio + $minutosFin;
		
		$minutosAsjudicar = abs( ($numeroDias*1440) + $minutosTotales);
	    
	    
	    }else{
	    
		$minutosInicio = $horaInicio[0]*60 + $horaInicio[1] ;
		$minutosFin    = $horaFin[0]*60 + $horaFin[1]; 
		
		$minutosAsjudicar = $minutosFin - $minutosInicio;
	    
	    }
	    
	    return $minutosAsjudicar;
	    	  
    }

    /**
    * reasignarTareaAction
    *
    * Esta funcion reasigna una tarea existente
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 3.3 04-05-2021 - Actualización: En la replicación a las asignaciones se agrega que envie 
    *                                          el proceso_id y tarea_id de la tarea replicada.
    *
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 3.2 15-01-2021 - Se agrega tarea en el historial del detalle para seguimiendo 
    *                           del historico de la tarea principal
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 3.2 08-07-2020 - Actualización: Se agrega bloque de código para replicar en asignaciones si se realiza la reasignación de la tarea
    *                                          y si esta permitido hacerlo según departamento parametrizado.
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 3.1 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 3.0 06-02-2020 - Se valida el envío del request al proceso SysCloud cuando el último asignado sea
    *                           parte del personal de data center
    * @since 2.9
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.9 10-06-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.8 17-04-2019 - Se agrega los métodos genéricos para el cálculo del los tiempos de la tarea.
    *
    * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
    * @version 2.7 19-06-2018 - Se modifico programación en envio de notificación para seleccionar los puntos afectados 
    *                           de los casos desde la información registrada en Telcos, y no del punto en sesión
    * @since 2.6
    * 
    * @author Modificado: John Vera <javera@telconet.ec>
    * @version 2.6 28-05-2018 - Se agrego una validacion para que cambie el estado de la encuesta
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.5 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.4 14-09-2017 -  Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.3 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
    *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.2 05-12-2016 Cuando se reasigne una tarea a un departamento distinto al actual el estado de la tarea quedara Asignada caso
    *                         contrario Aceptada
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.1 15-09-2016 Se aplica la funcion getMinimaComunicacionPorDetalleId para obtener el numero de la tarea
    *                         y se elimina la generacion de un nuevo numero de tarea
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 2.0 12-09-2016 Se valida que la tarea se encuentre abierta para poder ejecutar la herramienta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.9 07-07-2016 Se considera el estado "Activo", al obtener el entityInfoPersonaEmpresaRol de los empleados a los cuales se les
    *                         asigna una tarea.
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.8 05-07-2016 Se valida que si ingresan caracteres de apertura y cierre de tags, se eliminan
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.7 29-06-2016 Se realiza ajuste para setear como responsable de una tarea al primer integrante que se encuentre para una cuadrilla,
    *                         esto para el caso cuando la cuadrilla no tenga un lider asignado, adicional se valida que el departamento sea
    *                         obligatorio para el envio de la notificacion (solo para TN)
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.6 27-06-2016 Se realiza ajuste para obtener correctamente el id del departamento, dependiendo si es asignacion tipo cuadrilla o
    *                         diferente
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.5 23-06-2016 Se asocia el CANTON_ID en la table INFO_DETALLE_ASIGNACION, para determinar la oficina de que canton crea la tarea
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.4 23-06-2016 Se guarda el estado de la tarea en el seguimiento
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 26-05-2016 El motivo que se ingrese al reasignar la tarea solo se ingresara como un nuevo seguimiento
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 18-05-2016 Se crea el motivo como nuevo seguimiento y se registra la observacion ingresada a lo que se reasigna una tarea
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes para poder asignar una tarea una empresa externa
    *
    * @version 1.0
    *
    * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.9 26-12-2019 - Se agrega el método 'validarAccionTarea', para verificar si la acción a
    *                           realizar en la tarea es válida.
    *
    * @author Modificado: Ronny Morán <rmoranc@telconet.ec>
    * @version 2.10 06-10-2020 - Se agrega en seguimiento el fin de tarea seleccionado y motivo en caso de tenerlos.
    *                            Se guarda el motivo en la tabla InfoTareaCaracteristica.
    *  
    */
    function reasignarTareaAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $codEmpresa     = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $prefijoEmpresa = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");
        $id_detalle     = $objRequest->get('id_detalle');
        $id_tarea       = $objRequest->get('id_tarea');
        $intIdDepartamento      = $objSession->get('idDepartamento');
        $motivo                 = $objRequest->get('motivo');
        $departamento_asignado  = $objRequest->get('departamento_asignado')?$objRequest->get('departamento_asignado') : '';
        $empleado_asignado      = ($objRequest->get('empleado_asignado') ? $objRequest->get('empleado_asignado') : "0");
        $cuadrilla_asignada = $objRequest->get('cuadrilla_asignada');
        $contratista_asignado = $objRequest->get('contratista_asignada');
        $tipoAsignado = $objRequest->get('tipo_asignado');
        $numeracion = "";
        $fechaE = $objRequest->get('fecha_ejecucion');
        $horaE = $objRequest->get('hora_ejecucion');
        $arrayParametrosHist = array();
        $strNombreProceso    = "";
        $objPtoCliente       = $objSession->get('ptoCliente');
        $strNombreTarea      = $objRequest->get('nombre_tarea');
        $strNumeroTarea      = $objRequest->get('numero_tarea');
        $strNombreFinTarea   = $objRequest->get('nombreFinTarea');
        $intIdFinTarea       = $objRequest->get('idFinTarea'); 
        $strMotivoFinTarea   = $objRequest->get('motivoFinTarea');
        $intIdMotivoFinTarea = $objRequest->get('idMotivoFinTarea');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $strFinTareaMotivo   = "";
        $boolEsHal           = false;

        if (is_array($objPtoCliente))
        {
            $intPtoCliente =  $objPtoCliente['id'];
        }
        else
        {
            $intPtoCliente =  $objPtoCliente;
        }

        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $objSession->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $objRequest->getClientIp();
        $arrayParametrosHist["strOpcion"]               = "Historial";        

        if($cuadrilla_asignada)
        {
            $tipoAsignado = "cuadrilla";
        }
        elseif($empleado_asignado)
        {
            $tipoAsignado = "empleado";
        }
        else
        {
            $tipoAsignado = "contratista";
        }

        $strObservacionAsignacion   = "";

        $fecha = explode("T", $fechaE);
        $hora = explode("T", $horaE);
        $date = date_create(date('Y-m-d H:i', strtotime($fecha[0] . ' ' . $hora[1])));    //Fecha de Reprogramacion	

        if($date < new \DateTime('now'))
            $esReprogramadaAtras = true;
        else
            $esReprogramadaAtras = false;

        if($date > new \DateTime('now'))
            $esAsignadaReprogramada = true;
        else
            $esAsignadaReprogramada = false;

        /* @var $soporteService SoporteService */
        $soporteService = $this->get('soporte.SoporteService');
        $serviceProceso = $this->get('soporte.ProcesoService');

        $intIdDetalleHist = $objRequest->get('intIdDetalleHist');
        if ($intIdDetalleHist !== '' && !empty($intIdDetalleHist))
        {
            $arrayValidarAccion = $soporteService->validarAccionTarea(array('intIdDetalle'     => $id_detalle,
                                                                            'intIdDetalleHist' => $intIdDetalleHist));

            if (!$arrayValidarAccion['boolRespuesta'])
            {
               return $objResponse->setContent(json_encode(array('success'      => false,
                                                                 'seguirAccion' => $arrayValidarAccion['boolRespuesta'],
                                                                 'mensaje'      => $arrayValidarAccion['strMensaje'])));
            }
        }

        //Se eliminan simbolos de tags
        $motivo = $soporteService->eliminarSimbolosDeTags($motivo);


        $nombreDepartamento = "";

        if(isset($departamento_asignado))
        {
            if($departamento_asignado != '')
            {
                $departamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')->find($departamento_asignado);
                $nombreDepartamento = $departamento->getNombreDepartamento();
            }
        }

        $emSoporte->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();

        try
        {
            $strEstadoActualTarea = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($id_detalle);

            if($strEstadoActualTarea != "Cancelada" && $strEstadoActualTarea != "Rechazada" && $strEstadoActualTarea != "Finalizada")
            {
                $detalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);

                if($esAsignadaReprogramada || $esReprogramadaAtras ||
                        $date < $detalle->getFeSolicitada())
                {
                    $detalle->setFeSolicitada($date);
                }

                $emSoporte->persist($detalle);
                $emSoporte->flush();

                if($tipoAsignado == "empleado")
                {
                    $empleado_asignado = explode("@@", $empleado_asignado);
                    $persona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($empleado_asignado[0]);

                    $nombrePersona = "";
                    if(isset($persona))
                    {
                        if(($persona->getApellidos() != ''))
                        {
                            $nombrePersona = $persona->getNombres() . " " . $persona->getApellidos();
                        }
                        else
                        {
                            $nombrePersona = $persona->getRazonSocial();
                        }
                    }
                }
                elseif($tipoAsignado == "cuadrilla")
                {
                    $cuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($cuadrilla_asignada);
                }
                elseif($tipoAsignado == "contratista")
                {
                    $contratista = $emComercial->getRepository('schemaBundle:InfoPersona')->find($contratista_asignado);
                }

                $InfoDetalleAsignacion = new \telconet\schemaBundle\Entity\InfoDetalleAsignacion();
                $InfoDetalleAsignacion->setDetalleId($detalle);

                $objPersonaEmpresaRol = "";
                if($tipoAsignado == "empleado")
                {
                    $InfoDetalleAsignacion->setAsignadoId($departamento_asignado);
                    $InfoDetalleAsignacion->setAsignadoNombre($nombreDepartamento);
                    $InfoDetalleAsignacion->setRefAsignadoId($empleado_asignado[0]);
                    $InfoDetalleAsignacion->setRefAsignadoNombre($nombrePersona);
                    $InfoDetalleAsignacion->setTipoAsignado("EMPLEADO");
                    $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->findOneBy(array('personaId' => $persona->getId(),
                        'departamentoId' => $departamento_asignado,
                        'estado' => "Activo"));
                }
                elseif($tipoAsignado == "contratista")
                {
                    $InfoDetalleAsignacion->setAsignadoId($contratista_asignado);
                    $InfoDetalleAsignacion->setTipoAsignado("EMPRESAEXTERNA");

                    if($contratista)
                        $InfoDetalleAsignacion->setAsignadoNombre($contratista->__toString());
                }
                elseif($tipoAsignado == "cuadrilla")
                {
                    $InfoDetalleAsignacion->setTipoAsignado("CUADRILLA");
                    $InfoDetalleAsignacion->setAsignadoId($cuadrilla_asignada);
                    $InfoDetalleAsignacion->setAsignadoNombre($cuadrilla->getNombreCuadrilla());

                    $cuadrillaTarea = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                        ->getIntegrantesCuadrilla($cuadrilla_asignada);
                    $bandera = 0;
                    $esLider = "0";


                    if(count($cuadrillaTarea) > 0)
                    {
                        foreach($cuadrillaTarea as $datoCuadrilla)
                        {
                            $infoCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                ->getLiderCuadrilla($datoCuadrilla['idPersona']);

                            if($infoCuadrilla)
                            {
                                $bandera = 1;
                                $empleadoLider = $emComercial->getRepository('schemaBundle:InfoPersona')->find($datoCuadrilla['idPersona']);
                                $objPersonaEmpresaRol = $infoCuadrilla[0]['personaEmpresaRolId'];
                                $InfoDetalleAsignacion->setRefAsignadoId(($empleadoLider->getId()) ? $empleadoLider->getId() : "");
                                $InfoDetalleAsignacion->setRefAsignadoNombre(($empleadoLider->__toString()) ? $empleadoLider->__toString() : "");
                                break;
                            }
                        }

                        if($bandera == 0)
                        {
                            foreach($cuadrillaTarea as $datoCuadrilla)
                            {
                                $intRol = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getRolJefeCuadrilla();
                                $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                    ->findOneBy(array('empresaRolId' => $intRol,
                                    'personaId' => $datoCuadrilla['idPersona'],
                                    'estado' => "Activo"));
                                if($infoPersonaEmpresaRol)
                                {
                                    $bandera = 1;
                                    $objPersonaEmpresaRol = $infoPersonaEmpresaRol->getId();
                                    $idPersona = $datoCuadrilla['idPersona'];

                                    $empleadoLider = $emComercial->getRepository('schemaBundle:InfoPersona')->find($idPersona);

                                    $InfoDetalleAsignacion->setRefAsignadoId(($empleadoLider->getId()) ? $empleadoLider->getId() : "");
                                    $InfoDetalleAsignacion->setRefAsignadoNombre(($empleadoLider->__toString()) ? $empleadoLider->__toString() : "");
                                    break;
                                }
                            }
                        }

                        //Se setea como responsable de la tarea al primer integrante de la cuadrilla que se encuentre
                        if($bandera == 0)
                        {
                            $InfoDetalleAsignacion->setRefAsignadoId($cuadrillaTarea[0]['idPersona']);
                            $InfoDetalleAsignacion->setRefAsignadoNombre($cuadrillaTarea[0]['nombres'] . " " . $cuadrillaTarea[0]['apellidos']);
                            $InfoDetalleAsignacion->setPersonaEmpresaRolId($cuadrillaTarea[0]['empresaRolId']);
                            $objPersonaEmpresaRol = $cuadrillaTarea[0]['empresaRolId'];
                        }
                    }
                }


                if(isset($motivo))
                {
                    if($motivo != '')
                    {
                        $InfoDetalleAsignacion->setMotivo($motivo);
                    }
                }
                $InfoDetalleAsignacion->setUsrCreacion($objRequest->getSession()->get('user'));
                $InfoDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $InfoDetalleAsignacion->setIpCreacion($objRequest->getClientIp());
                $InfoDetalleAsignacion->setDepartamentoId($objSession->get('idDepartamento'));

                if($objSession->get('idPersonaEmpresaRol'))
                {
                    $entityPersonaEmpresaRol = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                           ->find($objSession->get('idPersonaEmpresaRol'));

                    if($entityPersonaEmpresaRol->getOficinaId())
                    {
                        $infoOficinaGrupo = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                            ->find($entityPersonaEmpresaRol->getOficinaId());
                        if($infoOficinaGrupo->getCantonId())
                        {
                            $InfoDetalleAsignacion->setCantonId($infoOficinaGrupo->getCantonId());
                        }
                    }
                }

                if($tipoAsignado == "empleado")
                {
                    if($infoPersonaEmpresaRol->getId())
                    {
                        $InfoDetalleAsignacion->setPersonaEmpresaRolId($infoPersonaEmpresaRol->getId());
                    }
                }
                elseif($tipoAsignado == "cuadrilla" && $bandera == 1)
                {
                    $InfoDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                }


                $emSoporte->persist($InfoDetalleAsignacion);
                $emSoporte->flush();

                if($tipoAsignado == "cuadrilla")
                {
                    $afectadosInfoCuadrillaTarea = $emSoporte->getRepository('schemaBundle:InfoCuadrillaTarea')
                        ->findByDetalleId($detalle);

                    if($afectadosInfoCuadrillaTarea && count($afectadosInfoCuadrillaTarea) > 0)
                    {
                        foreach($afectadosInfoCuadrillaTarea as $key => $entityAfectado)
                        {
                            $emSoporte->remove($entityAfectado);
                            $emSoporte->flush();
                        }
                    }

                    //*********************ACTUALIZO INTEGRANTES DE CUADRILLA**********************
                    $cuadrillaTarea = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                        ->getIntegrantesCuadrilla($cuadrilla_asignada);

                    foreach($cuadrillaTarea as $datoCuadrilla)
                    {

                        $infoCuadrillaTarea = new InfoCuadrillaTarea();
                        $infoCuadrillaTarea->setDetalleId($detalle);
                        $infoCuadrillaTarea->setCuadrillaId($cuadrilla_asignada);
                        $infoCuadrillaTarea->setPersonaId($datoCuadrilla['idPersona']);
                        $infoCuadrillaTarea->setUsrCreacion($objRequest->getSession()->get('user'));
                        $infoCuadrillaTarea->setFeCreacion(new \DateTime('now'));
                        $infoCuadrillaTarea->setIpCreacion($objRequest->getClientIp());
                        $emSoporte->persist($infoCuadrillaTarea);
                        $emSoporte->flush();
                    }
                    //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                }

                $strEstadoTareaReasignada = "Asignada";

                if($tipoAsignado == "cuadrilla" && $objPersonaEmpresaRol != "")
                {
                    $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($objPersonaEmpresaRol);

                    if(is_object($objPersonaEmpresaRol))
                    {
                        $departamento_asignado = $objPersonaEmpresaRol->getDepartamentoId();
                    }
                }

                if(is_object($detalle))
                {
                    $arrayParametrosHist["intDetalleId"] = $detalle->getId();
                }

                if(!$esAsignadaReprogramada)
                {
                    $arrayParametrosHist["intAsignadoId"] = $departamento_asignado;

                    if($tipoAsignado == "contratista")
                    {
                        $arrayParametrosHist["intAsignadoId"] = $contratista_asignado;
                    }
                }

                // Se ingresa la tarea al historial
                $arrayParametrosHist["intTareaId"]        = $intIdFinTarea;
                $arrayParametrosHist["intIdMotivo"]       = $intIdMotivoFinTarea;
                $arrayParametrosHist["strMotivoFinTarea"] = $motivo;
                
                //Ingresar Historial de la tarea
                $arrayParametrosHist["strObservacion"]  = "Tarea Reasignada - Módulo Tareas";
                $arrayParametrosHist["strEstadoActual"] = $strEstadoTareaReasignada;
                $arrayParametrosHist["strAccion"]       = "Reasignada";

                $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                //Ingresar Seguimiento de la tarea
                $strObservacionAsignacion         = "Tarea fue Reasignada a ";
                $arrayParametrosHist["strOpcion"] = "Seguimiento";

                if($tipoAsignado == "empleado")
                {
                    $arrayParametrosHist["strObservacion"] = $strObservacionAsignacion . $nombrePersona;
                }
                elseif($tipoAsignado == "cuadrilla")
                {
                    $arrayParametrosHist["strObservacion"] = $strObservacionAsignacion . "la cuadrilla " . $cuadrilla->getNombreCuadrilla();
                }
                elseif($tipoAsignado == "contratista")
                {
                    $arrayParametrosHist["strObservacion"] = $strObservacionAsignacion . $contratista->__toString();
                }

                $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);          
                
                if($strNombreFinTarea != "")
                {
                    $strFinTareaMotivo  = "<br/><b>Fin de tarea seleccionado:</b> ".$strNombreFinTarea.
                                          "<br/><b>Motivo: </b>".$strMotivoFinTarea;      
                    
                    if($intIdMotivoFinTarea != null && !empty($intIdMotivoFinTarea))                    
                    {
                        $objAdmiCaracteristica = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneByDescripcionCaracteristica('MOTIVO_FINALIZA_TAREA');
                        if (is_object($objAdmiCaracteristica))
                        {
                            $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                            $objInfoTareaCaracteristica->setTareaId($strNumeroTarea);
                            $objInfoTareaCaracteristica->setDetalleId($id_detalle);
                            $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristica->getId());
                            $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                            $objInfoTareaCaracteristica->setUsrCreacion($objRequest->getSession()->get('user'));
                            $objInfoTareaCaracteristica->setIpCreacion($objRequest->getClientIp());
                            $objInfoTareaCaracteristica->setValor($intIdMotivoFinTarea);
                            $objInfoTareaCaracteristica->setEstado('Activo');
                            $emSoporte->persist($objInfoTareaCaracteristica);
                            $emSoporte->flush();
                        }
                    }

                }    


                //Se crea como seguimiento el motivo ingresado
                if($motivo || $strNombreFinTarea != "")
                {
                    //Ingresar Seguimiento de la tarea
                    $arrayParametrosHist["strObservacion"]  = $motivo. $strFinTareaMotivo;

                    $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);  
                }

                if($esAsignadaReprogramada)
                {
                    //Ingresar historial de la tarea
                    $arrayParametrosHist["strOpcion"]       = "Historial";
                    $arrayParametrosHist["strObservacion"]  = "Tarea Reprogramada - Módulo Tareas";
                    $arrayParametrosHist["strEstadoActual"] = "Reprogramada";
                    $arrayParametrosHist["strAccion"]       = "Reprogramada";

                    $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                    //Ingresar seguimiento de la tarea
                    $arrayParametrosHist["strOpcion"]       = "Seguimiento";
                    $arrayParametrosHist["strObservacion"]  = "Tarea fue Reprogramada para el " . date_format($date, 'Y-m-d H:i');

                    $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                }

                //Función encargada de calcular los tiempos de las tareas
                $soporteService->calcularTiempoEstado(array('strEstadoActual' => $arrayParametrosHist["strEstadoActual"],
                                                            'intIdDetalle'    => $id_detalle,
                                                            'strUser'         => $objSession->get('user'),
                                                            'strIp'           => $objRequest->getClientIp()));

                /*
                  ENVIO DE NOTIFICACION DE CORREO EN REASIGNACION DE TAREAS
                 */
                $perteneceACaso = false;
                $idCaso = null;

                $caso = $emSoporte->getRepository('schemaBundle:InfoDetalle')->tareaPerteneceACaso($detalle->getId());

                $numeracionReferencia = null;

                if($caso[0]['caso'] != 0)
                {
                    $perteneceACaso         = true;
                    $hipotesis              = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getCasoPadreTarea($detalle->getId());
                    $caso                   = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($hipotesis[0]->getCasoId()->getId());
                    $idCaso                 = $caso->getId();
                    $numeracion             = $caso->getNumeroCaso();
                    $numeracionReferencia   = ' al Caso #' . $numeracion;
                }
                else
                {
                    if($detalle)
                    {
                        //Se obtiene el numero de la tarea en base al id_detalle
                        $intNumeroTarea = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                            ->getMinimaComunicacionPorDetalleId($detalle->getId());

                        $numeracion = $intNumeroTarea ? $intNumeroTarea : "";
                        $numeracionReferencia = ' a la Actividad #' . $numeracion;
                    }
                }

                if($tipoAsignado == "cuadrilla")
                {
                    if($InfoDetalleAsignacion->getPersonaEmpresaRolId())
                    {
                        $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->find($InfoDetalleAsignacion->getPersonaEmpresaRolId());
                        $departamento = $this->getDoctrine()
                            ->getManager("telconet_general")
                            ->getRepository('schemaBundle:AdmiDepartamento')
                            ->find($infoPersonaEmpresaRol->getDepartamentoId());
                    }
                }
                else
                {
                    $departamento = $this->getDoctrine()
                        ->getManager("telconet_general")
                        ->getRepository('schemaBundle:AdmiDepartamento')
                        ->find($InfoDetalleAsignacion->getAsignadoId());
                }

                $persona = null;
                $receptor = "";
                if($InfoDetalleAsignacion->getRefAsignadoId() || $tipoAsignado == "contratista")
                {
                    if($tipoAsignado == "contratista")
                    {
                        $receptor = $contratista->getId();
                    }
                    else
                    {
                        $receptor = $InfoDetalleAsignacion->getRefAsignadoId();
                    }

                    $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findOneBy(array('personaId' => $receptor,
                        'formaContactoId' => 5,
                        'estado' => "Activo"));

                    if($infoPersonaFormaContacto)
                    {
                        $to[] = $infoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                    }

                    $formaContactoAlias = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                        ->findOneBy(array('descripcionFormaContacto' => "Alias",
                        'estado' => "Activo"));


                    $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findOneBy(array('personaId' => $receptor,
                        'formaContactoId' => $formaContactoAlias->getId(),
                        'estado' => "Activo"));

                    if($infoPersonaFormaContacto)
                    {
                        $to[] = $infoPersonaFormaContacto->getValor(); //Correo de forma de contacto Alias
                    }

                    $persona = $emComercial->getRepository('schemaBundle:InfoPersona')
                        ->findOneByLogin($objRequest->getSession()->get('user'));
                }
                
                //valido que si es una tarea de informe ejecutivo ponga el informe en pendiente
                

                $tarea = $objJson = $this->getDoctrine()->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:AdmiTarea')->find($detalle->getTareaId());
                
                

                if(is_object($tarea) && $tarea->getNombreTarea() == 'Realizar Informe Ejecutivo de Incidente')
                {
                    $objDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                           ->findOneBy(array('detalleId' => $detalle->getId(),
                                                                                'casoId' => $caso->getId(),
                                                                                'estado' => 'Activo',
                                                                                'modulo' => 'SOPORTE'));
                    if(is_object($objDocumentoRelacion))
                    {
                        //consulto la encuesta para actualizar su estado
                        $objEncuesta = $emComunicacion->getRepository('schemaBundle:InfoEncuesta')->find($objDocumentoRelacion->getEncuestaId());

                        if(is_object($objEncuesta))
                        {
                            $objEncuesta->setEstado('Pendiente');
                            $emComunicacion->persist($objEncuesta);
                            $emComunicacion->flush();
                            $emComunicacion->getConnection()->commit();
                        }
                    }
                }
                

                $asunto = "Asignacion de Tarea";

                /*
                  OBTENCION DEL CANTON DEL ENCARGADO DE LA TAREA
                 */
                $empresa = '';

                if($departamento)
                {
                    $empresa = $departamento->getEmpresaCod();
                    $departamento = $departamento->getId();
                }
                else
                {
                    $departamento = '';
                }
                if($InfoDetalleAsignacion && ( $tipoAsignado == "empleado" || $tipoAsignado == "cuadrilla"))
                {
                    $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->find($InfoDetalleAsignacion->getPersonaEmpresaRolId());
                }
                if($infoPersonaEmpresaRol && ( $tipoAsignado == "empleado" || $tipoAsignado == "cuadrilla"))
                {
                    $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                        ->find($infoPersonaEmpresaRol->getOficinaId()->getId());
                    $canton = $oficina->getCantonId();
                }
                else
                {
                    $canton = '';
                }
                /*                 * *******************************************************************

                  USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS

                 * ********************************************************************* */
                if(is_object($tarea))
                {
                    $strNombreProceso = $tarea->getProcesoId()->getNombreProceso();
                }

                $asunto = $asunto . " | PROCESO: ".$strNombreProceso;

                /* @var $envioPlantilla EnvioPlantilla */
                $envioPlantilla = $this->get('soporte.EnvioPlantilla');

                $parametros = array('idCaso' => $idCaso,
                    'perteneceACaso' => $perteneceACaso,
                    'numeracion' => $numeracion,
                    'referencia' => $numeracionReferencia,
                    'asignacion' => $InfoDetalleAsignacion,
                    'persona' => $persona ? $persona : false,
                    'nombreProceso' => $strNombreProceso,
                    'nombreTarea' => $tarea->getNombreTarea() ? $tarea->getNombreTarea() : '',
                    'estado' => $tarea->getEstado() ? $tarea->getEstado() : '',
                    'empleadoLogeado' => $objRequest->getSession()->get('empleado'),
                    'empresa' => $objRequest->getSession()->get('prefijoEmpresa'),
                    'detalle' => $detalle,
                    'observacion' => $motivo ? $motivo : "");

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

                    $envioPlantilla->generarEnvioPlantilla($asunto, $to, 'TAREAASIG', $parametros, $empresa, $canton, $departamento);
                }
                
                if ($idCaso)
                {
                    $objInfoCaso      = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);
                    if (($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Tecnico')||
                       ($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Arcotel'))
                    {
                        $arrayAfectacionPadres = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                           ->getRegistrosAfectadosTotalXCaso($idCaso,'Cliente','Data');
                        foreach($arrayAfectacionPadres as $arrayAfectadoPadre)
                        {
                            $arrayParametrosSMS = array();
                            $arrayParametrosSMS['puntoId']      = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosSMS['personaId']    = "";
                            $arrayParametrosSMS['destinatario'] = "CLI";
                            $arrayParametrosSMS['tipoEnvio']    = "OUT";
                            $arrayParametrosSMS['tipoNotifica'] = "SMS";
                            $arrayParametrosSMS['empresa']      = $codEmpresa;
                            $arrayParametrosSMS['tipoEvento']   = "REASIGNAR";
                            $arrayParametrosSMS['usuario']      = $objSession->get('user');
                            $arrayParametrosSMS['casoId']       = $objInfoCaso->getId();
                            $arrayParametrosSMS['detalleId']    = "";
                            $arrayParametrosSMS['asignacion']   = "";
                            $soporteService->enviaSMSCasoCliente($arrayParametrosSMS);
                            $arrayParametrosCorreo = array();
                            $arrayParametrosCorreo['puntoId']        = $arrayAfectadoPadre['afectadoId'];
                            $arrayParametrosCorreo['usuario']        = $objSession->get('user');
                            $arrayParametrosCorreo['caso']           = $objInfoCaso;
                            $arrayParametrosCorreo['idDepartamento'] = $intIdDepartamento;
                            $arrayParametrosCorreo['empresa']        = $codEmpresa;
                            $arrayParametrosCorreo['codPlantilla']  = "CASOREASIGNCLI";
                            $arrayParametrosCorreo['asunto']        = "Reasignacion de caso";
                            $arrayParametrosCorreo['observacion']    =$motivo;
                            $soporteService->enviaCorreoClientesCasos($arrayParametrosCorreo);
                        }
                    }
                }
                $emSoporte->getConnection()->commit();

                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $objRequest->get('id_detalle');
                $arrayParametrosInfoTarea['strUsrCreacion'] = $objSession->get('user');
                $objServiceSoporte                          = $this->get('soporte.SoporteService');
                $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

                $arrayParametrosGestionPend['intDepartamentoId']  = $departamento_asignado;
                $arrayParametrosGestionPend['strNumero']          = $strNumeroTarea;
                $arrayParametrosGestionPend['idEmpresa']          = $objSession->get('idEmpresa');
                $arrayParametrosGestionPend['strUsrCreacion']     = $objSession->get('user');
                $arrayParametrosGestionPend['strIpCreacion']      = $objRequest->getClientIp();
                $arrayParametrosGestionPend['intReferenciaId']    = $strNumeroTarea;
                $arrayParametrosGestionPend['intProcesoId']       = $tarea->getProcesoId()->getNombreProceso();
                $arrayParametrosGestionPend['intTareaId']         = $tarea->getId();
                $objServiceSoporte->replicarTareaAGestionPendientes($arrayParametrosGestionPend);

                //Proceso para crear la tarea en el sistema de DC - Sys Cloud Center.
                if($tipoAsignado === "empleado")
                {
                    $objInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")->find($strNumeroTarea);

                    if (is_object($objInfoComunicacion))
                    {
                        $objInfoDetalleAsignacion  = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                               ->getUltimaAsignacion($id_detalle);
                        if(!empty($objInfoDetalleAsignacion))
                        {
                            $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
                            $objAdmiDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                ->find($objInfoDetalleAsignacion->getAsignadoId());
                            

                            if(!empty($objAdmiDepartamento))
                            {
                                $arrayAdmiParametroUsrs = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS','SOPORTE',
                                                                        '','','',$objAdmiDepartamento->getNombreDepartamento(),'','','','');
                            
                                if (!empty($arrayAdmiParametroUsrs) && $arrayAdmiParametroUsrs >0 )
                                {
                                    $objDate = date_create(date('Y-m-d H:i', strtotime($fecha[0] . ' ' . $hora[1])));
                                    $serviceProceso->putTareasSysCluod(array ('strNombreTarea'      => $strNombreTarea,
                                                                        'strNombreProceso'    => $strNombreProceso,
                                                                        'strObservacion'      => $motivo,
                                                                        'strFechaApertura'    => date_format($objDate, 'Y-m-d'),
                                                                        'strHoraApertura'     => date_format($objDate, 'H:i:s'),
                                                                        'strUser'             => $objSession->get('user'),
                                                                        'strIpAsigna'         => $objRequest->getClientIp(),
                                                                        'strUserAsigna'       => $objSession->get('empleado'),
                                                                        'strDeparAsigna'      => $objSession->get('departamento'),
                                                                        'strUserAsignado'     => $nombrePersona,
                                                                        'strDeparAsignado'    => $nombreDepartamento,
                                                                        'objInfoComunicacion' => $objInfoComunicacion));
                                }
                            }
                        }
                    }
                }

                $resultado = json_encode(array('success' => true));

                $boolEsHal = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                       ->isAsignadoHal(array( 'intDetalleId' => $objRequest->get('id_detalle')));
                
                if($boolEsHal)
                {
                    $strCommand = 'nohup php /home/telcos/app/console Envia:Tracking ';
                    $strCommand = $strCommand . escapeshellarg($objSession->get('user')). ' ';
                    $strCommand = $strCommand . escapeshellarg($objRequest->getClientIp()). ' ';
                    $strCommand = $strCommand . '"Tarea Reasignada" ';
                    $strCommand = $strCommand . escapeshellarg($objRequest->get('id_detalle')). ' ';

                    $strCommand = $strCommand .'>/dev/null 2>/dev/null &';
                    shell_exec($strCommand);
                } 

            }
            else
            {
                $resultado = json_encode(array('success' => true, 'mensaje' => "cerrada"));
            }
        }
        catch(\Exception $e)
        {
            if($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $emSoporte->getConnection()->close();
            $emComunicacion->getConnection()->close();
            $resultado = json_encode(array('success' => false, 'mensaje' => $e));
        }

        $objResponse->setContent($resultado);
        return $objResponse;
    }
    

     /**
     * @Secure(roles="ROLE_197-1")
     *
     * Documentación para el método 'indexTareasPorDepartamentoAction'.
     *
     * Muestra la pantalla inicial de la opción Tareas del Módulo de Soporte con las tareas pendientes del departamento en session.
     *
     * @return Response $respuesta
     *
     * @author Modificado: Fernando López <filopez@telconet.ec>
     * @version 1.3 06-05-2022 - Se cambia template de vista de tareas.
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.2 30-09-2019 - Se agrega consultar si la empresa usa el árbol de hipótesis para mostrarlo en el cierre de caso.
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.1 01-03-2019 - Se agrega enviar vacia la variable del número de actividad
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 27-11-2017
     *
     */
    public function indexTareasPorDepartamentoAction()
    {
        $strRolesPermitidos = array();

        if (true === $this->get('security.context')->isGranted('ROLE_197-1237'))
        {
            $strRolesPermitidos[] = 'ROLE_197-1237';
        }

        $strOrigen      = "tareasPorDepartamento";
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $objSession->set("strBanderaTareasDepartamento","S");
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");
        $emComercial    = $this->getDoctrine()->getManager();

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

        $strPuntoPersonaSession         = $objRequest->request->get('puntoPersonaSession') ? $objRequest->request->get('puntoPersonaSession') : '';
        $strDepartamentoPersonaSession  = $objRequest->request->get('departamentoSession') ? $objRequest->request->get('departamentoSession') : '';

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("197", "1");

        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
		$objSession->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());
        $arrayAdmiParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strEmpresaCod);
        $strBuscarPorArbolHipotesis = 'N';
        if($arrayAdmiParametroDet['valor2']==='S')
        {
            $strBuscarPorArbolHipotesis = 'S';
        }
        return $this->render('soporteBundle:Tareas:indexTareas.html.twig', array(     'strOrigen'                     => $strOrigen,
                                                                                'intNumeroActividad'            => "",
                                                                                'item'                          => $entityItemMenu,
                                                                                'rolesPermitidos'               => $strRolesPermitidos,
                                                                                'puntoPersonaSession'           => $strPuntoPersonaSession,
                                                                                'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
                                                                                'intIdCantonUsrSession'         => $intIdCantonUsrSession,
                                                                                'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
                                                                                'departamentoSession'           => $strDepartamentoPersonaSession,
                                                                                'buscaPorArbolHipotesis'        => $strBuscarPorArbolHipotesis
                                                                           )
                            );
    }    

    function getHistorialTareasAction(){
        $em = $this->getDoctrine()->getManager('telconet_soporte');
         $peticion = $this->get('request');
         $id_detalle=$peticion->query->get('id_detalle');                 
        
         $infoDetalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);
        $infoDetalleHistorial = $em->getRepository('schemaBundle:InfoDetalleHistorial')->findBy(array('detalleId'=>$infoDetalle->getId()));
        foreach($infoDetalleHistorial as $dato){
            $arreglo=array (
              'idDetalleHistorial'=>$dato->getId(),
               'observacion'=> $dato->getObservacion(),
                'estado'=>$dato->getEstado(),
                'usrCreacion'=>$dato->getUsrCreacion(),
                'feCreacion'=>$dato->getFeCreacion()
            );
        }
        
        if(!empty($arreglo)){
            $total=count($arreglo);
            $response= new Response(json_encode(array('total' => $total,'registros'=>$arreglo)  ));
        }else{
            $response= new Response(json_encode(array('total' => 0,'registros'=>$arreglo)  ));
        }
        
        $response->headers->set('Content-type', 'text/json');
        return $response;
        
    }    
    
    
    /**
      * exportarConsultaAction
      *
      * Método que consulta las tareas filtrada por los parámetros enviados por el usuario,
      * para luego dicha consulta ser exportada a Excel
      *
      * @return Documento de Excel
      *
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.9 04-07-2019 - Se consulta si la persona en sesión tiene la credencial 'indicadorTareasNacional'.
      *                         - Se agrega el filtro por tarea padre.
      *
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.8 20-06-2019 - AL momento de armar el string separado por coma(,) para los departamentos,
      *                           se valida que no sea nulo el departamento.
      *
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.7 15-06-2019 - Se cambia la manera de obtener el filtro de cuadrilla y estado por un array.
      *
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.6 04-06-2019 - Se establece el estándar de calidad en el método y se agrega el llamado a la
      *                           función jobReporteTareas encargada de generar el reporte de tareas que será
      *                           enviado por correo al usuario.
      *                         - Se establece el control de exportación por usuario.
      *
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.5 11-12-2018 -  Se envia parametros necesarios a getRegistrosMisTareas para poder mostrar tareas de todas las empresas
      *                            si tiene el perfil asignado. 
      *  
      * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 1.4 04-12-2018 -  Se agrega la fecha por defecto en caso que no exista ningún filtro por fecha.
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.3 19-01-2018 - Se agrega concepto de indicador de tareas pendientes por departamento
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.2 26-10-2016 - Se realizan ajustes para presentar las tareas pendientes del usuario en session
      *
      * @author Modificado: Edson Franco <efranco@telconet.ec>
      * @version 1.1 12-08-2015 Mejora a la función para que cuando hayan enviado el
      *                         parámetro 'filtroUsuario' busque las tareas relacionadas
      *                         al usuario logueado. 
      * 
      * @version 1.0 Version Inicial
      */
    public function exportarConsultaAction()
    {
        ini_set('max_execution_time', 3000000);

        $objResponse     = new Response();
        $arrayParametros = array();
        $objPeticion     = $this->get('request');
        $objSession      = $objPeticion->getSession();
        $objServiceUtil  = $this->get('schema.Util');
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $emSoporte       = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $objResponse->headers->set('Content-Type', 'text/json');

        try
        {
            $intPersonaEmpresaRol     = $objSession->get('idPersonaEmpresaRol');
            $intCantidadExportacion   = 3; //cantidad maxima permitida de exportación por defecto.
            $objFechaActual           = new \DateTime(date_format(new \DateTime('now'), "d-m-Y"));
            $objSoporteProcesoService = $this->get('soporte.SoporteProcesos');
            $boolReiniciarCaract      = false;

            /**
             * Verificación del control de generación del reporte
             */
            $arrayData = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne( 'PARAMETROS_REPORTE_TAREAS','SOPORTE','','CANTIDAD_GENERA_REPORTE','','','','','','');

            if (!empty($arrayData) && isset($arrayData['valor1']) &&
                !empty($arrayData['valor1']) && intval($arrayData['valor1']) > 0)
            {
                //Cantidad maxima permitida de exportación.
                $intCantidadExportacion = intval($arrayData['valor1']);
            }

            //Verificamos si la tarea tiene característica EXPORTAR_REPORTE_TAREAS Activa.
            $objAdmiCaracteristica = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array ('descripcionCaracteristica' => 'EXPORTAR_REPORTE_TAREAS',
                                       'estado'                    => 'Activo'));

            if (!is_object($objAdmiCaracteristica))
            {
                throw new \Exception('Error : La característica de exportación no se encuentra Activa. '
                        .'Por favor comuniquese con Sistemas.');
            }

            $objInfoPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                    ->findOneBy(array ('caracteristicaId'    => $objAdmiCaracteristica->getId(),
                                       'personaEmpresaRolId' => $intPersonaEmpresaRol));

            if (is_object($objInfoPersonaEmpresaRolCarac))
            {
                $intValor         = $objInfoPersonaEmpresaRolCarac->getValor();
                $objFechaCreacion = new \DateTime(date_format($objInfoPersonaEmpresaRolCarac->getFeCreacion(), "d-m-Y"));

                if ($objFechaCreacion->getTimestamp() === $objFechaActual->getTimestamp()
                        && intval($intValor) >= $intCantidadExportacion)
                {
                    throw new \Exception('Error : Estimado usuario, ha superado la cantidad máxima de exportación de tareas '
                            .'para el día de hoy. '
                            .'Por favor volver a intentar el día de mañana.');
                }

                if ($objFechaCreacion->getTimestamp() < $objFechaActual->getTimestamp())
                {
                    $boolReiniciarCaract = true;
                }
            }

            //Actualizamos o creamos la característica
            $objSoporteProcesoService->putInfoInfoPersonaEmpresaRolCarac(
                    array ('intIdPersonaEmpresaRol' => $intPersonaEmpresaRol,
                           'strCaracteristica'      => 'EXPORTAR_REPORTE_TAREAS',
                           'strUsuarioCrea'         => $objSession->get('user'),
                           'boolReiniciar'          => $boolReiniciarCaract,
                           'strIpCrea'              => $objPeticion->getClientIp()));

            /**
             * Antes de iniciar con el parseo de lo datos, verificamos que el usuario
             * no tenga el proceso de reporte ejecutandose.
             */
            $arrayResultJob = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->existeJobReporteTarea(array ('strNombreJob' => 'JOB_REPORTE_TAREAS_'.$objSession->get('user')));

            if ($arrayResultJob['status'] === 'ok' && $arrayResultJob['cantidad'] > 0)
            {
                throw new \Exception('Error : Estimado usuario ya cuenta con un proceso ejecutándose. Por favor intente '
                        . 'de nuevo en unos minutos.');
            }
            else
            {
                if ($arrayResultJob['status'] === 'fail')
                {
                    throw new \Exception($arrayResultJob['message']);
                }
            }

            $arrayParametros["cliente"]    = $objPeticion->get('hid_cliente')     ? $objPeticion->get('hid_cliente')     : '';
            $arrayParametros["tarea"]      = $objPeticion->get('hid_tarea')       ? $objPeticion->get('hid_tarea')       : '';
            $arrayParametros["asignado"]   = $objPeticion->get('hid_asignado')    ? $objPeticion->get('hid_asignado')    : '';
            $arrayParametros["tareaPadre"] = $objPeticion->get('hid_TareaPadre')  ? $objPeticion->get('hid_TareaPadre')  : '';
            $arrayParametros["actividad"]  = $objPeticion->get('hid_numeroTarea') ? $objPeticion->get('hid_numeroTarea') : '';
            $arrayParametros["caso"]       = $objPeticion->get('hid_numeroCaso')  ? $objPeticion->get('hid_numeroCaso')  : '';

            $arrayParametros["strOpcionBusqueda"] = $objPeticion->get('hid_opcion_busqueda') ?
                                                        $objPeticion->get('hid_opcion_busqueda') : "";
            $strBanderaTareasDepartamento         = $objSession->get("strBanderaTareasDepartamento") ?
                                                        $objSession->get("strBanderaTareasDepartamento") : 'N';

            $intDepartamento = $objPeticion->get('hid_departamento') ? $objPeticion->get('hid_departamento') : "";
            $intmpresaFiltro = $objPeticion->get('hid_empresa')      ? $objPeticion->get('hid_empresa')      : null;
            $intCuadrilla    = $objPeticion->get('hid_cuadrilla')    ? $objPeticion->get('hid_cuadrilla')    : "";

            $arrayFeSolicitadaDesde = explode('T', $objPeticion->get('feSolicitadaDesde'));
            $arrayFeSolicitadaHasta = explode('T', $objPeticion->get('feSolicitadaHasta'));
            $arrayFeFinalizadaDesde = explode('T', $objPeticion->get('feFinalizadaDesde'));
            $arrayFeFinalizadaHasta = explode('T', $objPeticion->get('feFinalizadaHasta'));

            $arrayParametros['feSolicitadaDesde'] = $arrayFeSolicitadaDesde ? $arrayFeSolicitadaDesde[0] : '';
            $arrayParametros['feSolicitadaHasta'] = $arrayFeSolicitadaHasta ? $arrayFeSolicitadaHasta[0] : '';
            $arrayParametros['feFinalizadaDesde'] = $arrayFeFinalizadaDesde ? $arrayFeFinalizadaDesde[0] : '';
            $arrayParametros['feFinalizadaHasta'] = $arrayFeFinalizadaHasta ? $arrayFeFinalizadaHasta[0] : '';

            $strCodEmpresa     = ($objSession->get('idEmpresa')   ? $objSession->get('idEmpresa')   : "");
            $intIdEmpleado     = ($objSession->get('id_empleado') ? $objSession->get('id_empleado') : "");

            $intIdDepartamento = $intDepartamento == "" ? ($objSession->get('idDepartamento') ?
                                                           $objSession->get('idDepartamento') : "") : $intDepartamento;

            $arrayParametros["tipo"]                 = "ByDepartamento";
            $arrayParametros["codEmpresa"]           = $strCodEmpresa ? $strCodEmpresa : "";
            $arrayParametros["idUsuario"]            = $intIdEmpleado ? $intIdEmpleado : "";
            $arrayParametros["idDepartamento"]       = $intIdDepartamento ? $intIdDepartamento : "";
            $arrayParametros["idCuadrilla"]          = null;
            $arrayParametros["intPersonaEmpresaRol"] = $intPersonaEmpresaRol;

            //Se consulta si la persona en sesión tiene la credencial 'indicadorTareasNacional'
            $strTienePerfil = $emSoporte->getRepository('schemaBundle:SeguRelacionSistema')
                    ->getPerfilPorPersona(array ('intIdPersonaRol' => $intPersonaEmpresaRol,
                                                 'strNombrePerfil' => 'indicadorTareasNacional'));

            $arrayParametros["strTieneCredencial"] = $strTienePerfil;

            $booleanVerTareasTodasEmpresas                   = $this->get('security.context')->isGranted('ROLE_197-6157');
            $arrayParametros["booleanVerTareasTodasEmpresa"] = $booleanVerTareasTodasEmpresas;

            /**
             * Se consulta si la persona en sesion tiene la credencial: verTareasTodasEmpresas (ROLE_197_6157)
             * y la empresa Telconet
             */
            if (true === $booleanVerTareasTodasEmpresas && $objSession->get('prefijoEmpresa') === "TN" )
            {
                $objPersona             = $emComercial->getRepository("schemaBundle:InfoPersona")->find($intIdEmpleado);
                $arrayPersonaEmpresaRol = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                     ->findBy( array( 'personaId'      => $objPersona,
                                                                         'estado'      => 'Activo' ) );
                $arrayIdsPersonaEmpresaRol = array();
                $arrayIdsDepartamento      = array();

                foreach($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
                {
                    $arrayIdsPersonaEmpresaRol[] = $objPersonaEmpresaRol->getId();
                    $arrayIdsDepartamento[]      = $objPersonaEmpresaRol->getDepartamentoId();
                }

                $arrayParametros["arrayPersonaEmpresaRol"] = $arrayIdsPersonaEmpresaRol;
                $arrayParametros["arrayDepartamentos"]     = $arrayIdsDepartamento;
            }
            else
            {
                $arrayParametros["arrayPersonaEmpresaRol"] = array($intPersonaEmpresaRol);
                $arrayParametros["arrayDepartamentos"]     = array($arrayParametros["idDepartamento"]);
            }

            //Adecuación para mostrar directamente la tarea relacionada a una actividad sin necesidad
            //de buscar por empresa o departamento, el filtro debe tener el numero de tarea/actividad en cuestión
            if($intDepartamento == "" && $arrayParametros["actividad"] != "")
            {
                $arrayParametros["idDepartamento"] = null;
            }

            //Se envia informacion de cuadrilla para consultar sus tareas asignadas de ser requerido
            if($intCuadrilla != "")
            {
                $arrayParametros["tipo"]           = "ByCuadrilla";
                $arrayParametros["idCuadrilla"]    = $intCuadrilla;
                $arrayParametros["idDepartamento"] = null;
            }

            if($arrayParametros["caso"] != "")
            {
                $arrayParametros["tipo"] = "ByCaso";
            }

            $strNombreDepartamento = '';
            $strNombreCuadrilla    = '';
            $arrayParametros['nombreAsignado']='';

            //Se obtiene el nombre del departamento en el cual se esta buscando
            if($arrayParametros["idDepartamento"])
            {
                $objDepartamento = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                        ->find($arrayParametros["idDepartamento"]);

                if($objDepartamento)
                {
                    $strNombreDepartamento             = $objDepartamento->getNombreDepartamento();
                    $arrayParametros['nombreAsignado'] = $strNombreDepartamento;
                }
            }

            //Se obtiene el nombre de la cuadrilla en la cual se esta buscando
            if($arrayParametros["idCuadrilla"] && $arrayParametros["idDepartamento"] == null
                    && $arrayParametros["idCuadrilla"] != 'Todos')
            {
                $arrayNombresCuadrillas = explode(',',$objPeticion->get('hid_nombreCuadrilla'));
                $arrayNombresCuadrillas = array_map('strtoupper', $arrayNombresCuadrillas);
                foreach ($arrayNombresCuadrillas as $strValue)
                {
                    if ($strNombreCuadrilla == '' || is_null($strNombreCuadrilla))
                    {
                        $strNombreCuadrilla = "''''".$strValue."''''";
                    }
                    else
                    {
                        $strNombreCuadrilla = $strNombreCuadrilla .",''''".$strValue."''''";
                    }
                }
                $arrayParametros['nombreAsignado'] = $strNombreCuadrilla;
            }

            //Se realiza esta validación para controlar el máximo de caracteres permitidos.
            if (strlen($strNombreCuadrilla) > 3500)
            {
                throw new \Exception('Error : Ha superado el limite máximo de cuadrillas selecciondas.<br/><br/>'
                        . '<b>Por favor seleccionar menos cuadrillas.</b>');
            }

            $strEstados  = $objPeticion->get('hid_estado') ? $objPeticion->get('hid_estado') : '';
            if ($strEstados !== 'Todos' && $strEstados !== '' && !is_null($strEstados))
            {
                $arrayEstados = explode(',',$strEstados);
                $arrayEstados = array_map('strtoupper', $arrayEstados);
                $strEstados   = '';
                foreach ($arrayEstados as $strValue)
                {
                    if ($strEstados == '' || is_null($strEstados))
                    {
                        $strEstados = "''''".$strValue."''''";
                    }
                    else
                    {
                        $strEstados = $strEstados .",''''".$strValue."''''";
                    }
                }
            }

            $arrayParametros["estado"] = $strEstados;

            //Verificará si se realizará la consulta por Usuario
            $arrayParametros["filtroUsuario"] = $objPeticion->get('filtroUsuario') ? $objPeticion->get('filtroUsuario') : "";

            if($strBanderaTareasDepartamento == "S")
            {
                $arrayParametros["strOrigen"] = "tareasPorDepartamento";

                $objInfoPersonaEmpresaRol = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->find($intPersonaEmpresaRol);

                if(is_object($objInfoPersonaEmpresaRol))
                {
                    $arrayParametros["oficinaSession"]      = $objInfoPersonaEmpresaRol->getOficinaId()->getId();
                    $arrayParametros["departamentoSession"] = $objInfoPersonaEmpresaRol->getDepartamentoId();
                }
            }
            else
            {
                $arrayParametros["strOrigen"] = "tareasPorEmpleado";
            }

            //Obtenemos el parámetro de la fecha por defecto
            if ( (!isset($arrayParametros["feFinalizadaHasta"]) || $arrayParametros["feFinalizadaHasta"] === '') &&
                 (!isset($arrayParametros["feFinalizadaDesde"]) || $arrayParametros["feFinalizadaDesde"] === '') &&
                 (!isset($arrayParametros["feSolicitadaHasta"]) || $arrayParametros["feSolicitadaHasta"] === '') &&
                 (!isset($arrayParametros["feSolicitadaDesde"]) || $arrayParametros["feSolicitadaDesde"] === ''))
            {
                $arrayFechaDefecto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

                if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
                    checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
                {
                    $strFechaDefecto = $arrayFechaDefecto['valor1'].'-'. //Año
                                       $arrayFechaDefecto['valor2'].'-'. //Mes
                                       $arrayFechaDefecto['valor3'];     //Día

                    $arrayParametros['strFechaDefecto'] = $strFechaDefecto;
                }
            }

            $strIdDepartamentos = null;
            if (isset($arrayParametros["arrayDepartamentos"]) && !empty($arrayParametros["arrayDepartamentos"]))
            {
                foreach ($arrayParametros["arrayDepartamentos"] as $intIdDepartamento)
                {
                    if ($intIdDepartamento !== null && $intIdDepartamento !== '')
                    {
                        if ($strIdDepartamentos == null)
                        {
                            $strIdDepartamentos = $intIdDepartamento;
                        }
                        else
                        {
                            $strIdDepartamentos .= ','.$intIdDepartamento;
                        }
                    }
                }
                $arrayParametros["arrayDepartamentosP"] = $strIdDepartamentos;
            }

            $strIdPersonaEmpresaRol= null;
            if (isset($arrayParametros["arrayPersonaEmpresaRol"]) && !empty($arrayParametros["arrayPersonaEmpresaRol"]))
            {
                foreach ($arrayParametros["arrayPersonaEmpresaRol"] as $intIdPersonaEmpresaRol)
                {
                    if ($strIdPersonaEmpresaRol == null)
                    {
                        $strIdPersonaEmpresaRol = $intIdPersonaEmpresaRol;
                    }
                    else
                    {
                        $strIdPersonaEmpresaRol .= ','.$intIdPersonaEmpresaRol;
                    }
                }
                $arrayParametros["arrayPersonaEmpresaRolP"] = $strIdPersonaEmpresaRol;
            }

            if($intmpresaFiltro && $intmpresaFiltro != '')
            {
                $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                        ->findOneByPrefijo($intmpresaFiltro);
            }
            else
            {
                $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                        ->find($strCodEmpresa);
            }

            $arrayParametros['strTodaLasEmpresa']  = $booleanVerTareasTodasEmpresas ? 'S' : 'N';
            $arrayParametros['strNombreEmpresa']   = $objInfoEmpresaGrupo->getNombreEmpresa();
            $arrayParametros['strUsuarioSolicita'] = $objSession->get('user');

            $arrayUsarNuevoGrid   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne( 'VALIDACION PARA USAR NUEVA FUNCION GRID TAREAS', 
                                                        'SOPORTE', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '' );
            $arrayParametros['tablaConsulta'] = 'MisTareas';

            if ( count($arrayUsarNuevoGrid)>0         && 
                 $arrayUsarNuevoGrid['valor1'] == 'S' && 
               ($arrayUsarNuevoGrid['valor2'] == '' || $arrayUsarNuevoGrid['valor2'] == $intIdDepartamento) )
            {
                $arrayParametros['tablaConsulta'] = 'InfoTarea';
            }

            //Método que realiza el reporte en la base de datos.
            $arrayReporteTareas = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->jobReporteTareas($arrayParametros);

            if ($arrayReporteTareas['status'] == 'fail')
            {
                throw new \Exception($arrayReporteTareas['message']);
            }

            $objResponse->setContent(json_encode($arrayReporteTareas));
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al generar el reporte, si el problema persiste comunique a Sistemas.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'TareasController->exportarConsultaAction',
                                          $objException->getMessage(),
                                          $objSession->get('user'),
                                          $objPeticion->getClientIp());

            $objResponse->setContent(json_encode(array('status' => 'fail', 'message' => $strMessage)));
        }
        return $objResponse;
    }

    /**
      * exportarConsultaTareas
      *
      * Exportar las tareas consultas a Excel.
      * 
      * @param array $parametros
      * @param array $tareas
      * @param string $usuario
      * @param entity Manager $emComercial
      * @param entity Manager $emSoporte
      * @param entity Manager $emComunicacion
      * @param entity Manager $emGeneral
      * @param string $empresa
      *          
      * @return Documento Excel
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.3 15-08-2016 - Se realizan ajustes por cambios en la funcion getMinimaComunicacionPorDetalleId y se quita logica
      *                           imnecesaria para el calculo del numero de la tarea
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.2 27-07-2016 - Se agrega el nombre del proceso y el numero de la tarea asiganda
      *
      * @author Modificado: Edson Franco <efranco@telconet.ec>
      * @version 1.1 06-08-2015 - Se cambia la función getPersonaPorLogin por 
      *                           getDatosPersonaPorLogin para corregir problema
      *                           al exportar las tareas 
      * 
      * @version 1.0 Version Inicial
      * 
      */
    public function exportarConsultaTareas($parametros,$tareas , $usuario ,$emComercial,$emSoporte,$emComunicacion,$emGeneral,$empresa)
    {         	       
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);

        $objPHPExcel = new PHPExcel();

        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateConsultaTareas.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Tareas");
        $objPHPExcel->getProperties()->setSubject("Consulta de Tareas");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de Tareas.");
        $objPHPExcel->getProperties()->setKeywords("Tareas");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3', $usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', '' . ($parametros['asignado'] == "" ? 'Todos' : $parametros['asignado']));
        $objPHPExcel->getActiveSheet()->setCellValue('B9', '' . ($parametros['estado'] == "" ? 'Todos' : $parametros['estado']));
        $objPHPExcel->getActiveSheet()->setCellValue('B10', '' . ($parametros['actividad'] == "" ? 'Todos' : $parametros['actividad']));
        $objPHPExcel->getActiveSheet()->setCellValue('B11', '' . ($parametros['caso'] == "" ? 'Todos' : $parametros['caso']));
        $objPHPExcel->getActiveSheet()->setCellValue('C12', '' . ($parametros['feSolicitadaDesde'] == "" ? 
                                                                 'Todos' : $parametros['feSolicitadaDesde']));
        $objPHPExcel->getActiveSheet()->setCellValue('C13', '' . ($parametros['feSolicitadaHasta'] == "" ? 
                                                                 'Todos' : $parametros['feSolicitadaHasta']));
        $objPHPExcel->getActiveSheet()->setCellValue('C14', '' . ($parametros['feFinalizadaDesde'] == "" ? 
                                                                 'Todos' : $parametros['feFinalizadaDesde']));
        $objPHPExcel->getActiveSheet()->setCellValue('C15', '' . ($parametros['feFinalizadaHasta'] == "" ? 
                                                                 'Todos' : $parametros['feFinalizadaHasta']));        
        $objPHPExcel->getActiveSheet()->setCellValue('B16', '' . ( $parametros['idDepartamento'] ? 
                                                                  ($parametros['nombreAsignado'] . ' ( ' . $empresa . ' )') : 'N/A' ));        
        $objPHPExcel->getActiveSheet()->setCellValue('D16', '' . ( $parametros['idCuadrilla'] ?  $parametros['nombreAsignado'] : 'N/A' ));

        $i = 19;

        
        
        foreach($tareas['resultados'] as $datos):

            $ClientesAfectados = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getRegistrosAfectadosTotal($datos["idDetalle"], 
                                                                                                                   'Cliente', 
                                                                                                                   'Data');
            //Se obtiene el numero de la tarea en base al id_detalle
            $numeroTareaAsignada = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                  ->getMinimaComunicacionPorDetalleId($datos["idDetalle"]);

            if($datos["idTarea"])
            {
                $objAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById($datos["idTarea"]);

                if($objAdmiTarea)
                {
                    $objAdmiProceso = $emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneById($objAdmiTarea->getProcesoId());
                    if($objAdmiProceso)
                    {
                        $nombre_proceso = $objAdmiProceso->getNombreProceso();
                    }
                }
            }

            $string_clientes = "";
            $numero_caso = "";
            $nombreCliente = '';
            $string_dir_ptos_clientes= "";
            $contacto = '';

            if($ClientesAfectados && count($ClientesAfectados) > 0)
            {
                $arrayClientes = false;
                $arrayNombreCl = false;
                $arrayDirPtoClientes=false;
                
                foreach($ClientesAfectados as $clientAfect)
                {
                    $arrayClientes[] = $clientAfect["afectadoNombre"];
                    $arrayNombreCl[] = $clientAfect["afectadoDescripcion"];
                    $strDirPtoCliente = "";

                    if($clientAfect["afectadoId"])
                    {
                        $entityPunto=$emComercial->getRepository('schemaBundle:InfoPunto')->find($clientAfect["afectadoId"]);
                        if($entityPunto)
                        {
                           $strDirPtoCliente = $entityPunto->getDireccion();
                        }
                        $contacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->findContactosTelefonicosPorPunto($clientAfect["afectadoId"]);
                    }
                    $arrayDirPtoClientes[]=$strDirPtoCliente;
                    
                }
                
                $string_clientes_1 = implode(",", $arrayClientes);
                $string_clientes = "" . $string_clientes_1 . "";

                $string_clientes_2 = implode(",", $arrayNombreCl);
                $nombreCliente = "" . $string_clientes_2 . " ";
                
                $string_clientes_3 = implode(",", $arrayDirPtoClientes);
                $string_dir_ptos_clientes = "" . $string_clientes_3 . " ";
                
            }
            
            $feSolicitada     = ($datos["feSolicitada"] ? strval(date_format($datos["feSolicitada"], "d-m-Y H:i")) : "");            
            $feTareaHistorial = ($datos["feTareaHistorial"] ? strval(date_format($datos["feTareaHistorial"], "d-m-Y H:i")) : "");

            $nombreActualizadoPor = "";
            $usrTareaHistorial    = ($datos["usrTareaHistorial"] ? $datos["usrTareaHistorial"] : "");
            
            if($usrTareaHistorial)
            {
                $empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getDatosPersonaPorLogin($usrTareaHistorial);
                if($empleado && count($empleado) > 0)
                {                    
                    $nombreActualizadoPor = sprintf($empleado);
                }
            }

            $hipotesis = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getCasoPadreTarea($datos["idDetalle"]);

            
            if($hipotesis)
            {
                $infoCaso = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($hipotesis[0]->getCasoId()->getId());
                $numero_caso = $infoCaso->getNumeroCaso();
            }

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $numeroTareaAsignada ? $numeroTareaAsignada : "");
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $numero_caso);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $string_clientes);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $nombreCliente);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $string_dir_ptos_clientes);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $nombre_proceso ? $nombre_proceso : "");
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $datos["nombreTarea"]);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $datos["observacion"]);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $datos["refAsignadoNombre"]?
                                                          ucwords(strtolower($datos["refAsignadoNombre"])) : $datos["asignadoNombre"]);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $feSolicitada);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $nombreActualizadoPor ? ucwords(strtolower($nombreActualizadoPor)) : '');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $feTareaHistorial);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $datos["estado"]);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $contacto);

            $i = $i + 1;
            
            $emSoporte->clear();
            $emComercial->clear();

        endforeach;

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Tareas_' . date('d_M_Y') . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
        
    /**
    * obtenerHoraServerAction
    *
    * Método que obtiene la hora y fecha del servidor para gestion de calculo de tiempos en las tareas
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 10-02-2015       
    */
    public function obtenerHoraServerAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        try
        {
        
            $fechaActual =  new \DateTime('now');   
            $fecha       =  $fechaActual->format('Y-m-d');
            $hora        =  $fechaActual->format('H:i');                        

            $response= json_encode(array('success'=> true ,'fechaActual' => $fecha,'horaActual'=>$hora));   
        
        }
        catch(\Exception $e)
        {
             $response= json_encode(array('success'=> false , 'error'=>$e->getMessage()));   
        }
                
        $respuesta->setContent($response);
        return $respuesta;
    }
    
    
    /**
     * getDashboard
     *
     * Método que retorna las tareas asignadas al usuario logueado en el mes                        
     * 
     * @param Request $objRequest
     * @param String  $strFiltro Indicará si se hará la consulta para el gráfico de barras
     *                           o para el listado de casos asignados.
     * 
     * @return JsonResponse $response         
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 09-05-2019 - Al momento de obtener las tareas previo se envía el usuario la ip y
     *                           el objeto de la clase SoporteService.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-06-2016 - Se le envia un parametro mas al generarJsonMisTareas, para que internamente pueda calcular el numero de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 19-05-2016 - Se realizan ajustes por cambio en los parametros de la funcion generarJsonMisTareas
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 07-08-2015
     */  
    public function getDashboardAction(Request $objRequest, $strFiltro)
    {
        $response        = new JsonResponse();
        $arrayTareas     = array();
        $arrayParametros = array();
        $arrayEstados    = array(
                                    'Asignadas'   => 'Asignada',
                                    'Finalizadas' => 'Finalizada'
                                );
        
        $objSession         = $objRequest->getSession();
        $intEmpleadoId      = $objSession->get("id_empleado");
        $intDepartamentoId  = $objSession->get("idDepartamento");
        $intEmpresa         = $objSession->get("idEmpresa");
        
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $emComunicacion    = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $intCurrentMonth = date("m");
        $intCurrentYear  = date("Y");
        $timeMes         = mktime(0, 0, 0, $intCurrentMonth, 1, $intCurrentYear);
        $intNumeroDeDias = intval(date("t", $timeMes));
        
        $strFeSolicitadaDesde = "01-".$intCurrentMonth."-".$intCurrentYear;
        $strFeSolicitadaHasta = $intNumeroDeDias."-".$intCurrentMonth."-".$intCurrentYear;

        $arrayParametros["feSolicitadaDesde"]  = $strFeSolicitadaDesde;
        $arrayParametros["feSolicitadaHasta"]  = $strFeSolicitadaHasta;
        $arrayParametros["idUsuario"]          = $intEmpleadoId;
        $arrayParametros["tipo"]               = "ByDepartamento";
        $arrayParametros["idDepartamento"]     = $intDepartamentoId;
        $arrayParametros["codEmpresa"]         = $intEmpresa;
        $arrayParametros["idCuadrilla"]        = null;
        
        if( $strFiltro == 'barras' || $strFiltro == 'listado' )
        {
            $arrayParametros["filtroUsuario"] = "ByUsuario";
        }
        else
        {
            $arrayParametros["filtroUsuario"] = null;
        }
        
        switch( $strFiltro )
        {
            case 'graficoPastel':
                
                $arrayEstados = array(
                                        'Aceptadas'   => 'Aceptada',
                                        'Asignadas'   => 'Asignada',
                                        'Canceladas'  => 'Cancelada',
                                        'Finalizadas' => 'Finalizada',
                                        'Rechazadas'  => 'Rechazada'
                                    );
                
            case 'barras':  
                
                $arrayResultados = array();
                
                $arrayParametros["filtroGroupBy"] = 'estados';

                //Se obtiene el nombre del departamento en el cual se esta buscando
                if($arrayParametros["idDepartamento"] && $arrayParametros["idCuadrilla"]==null)
                {
                    $objDepartamento = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                                                 ->find( $arrayParametros["idDepartamento"] );
                    if($objDepartamento)
                    {
                        $nombreDepartamento = $objDepartamento->getNombreDepartamento();
                        
                        $arrayParametros['nombreAsignado'] = $nombreDepartamento;
                    }
                }

                $arrayResultados = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                             ->getRegistrosMisTareas($arrayParametros, '', '', 'data');
                
                if( $arrayResultados )
                {
                    foreach($arrayEstados as $key => $value)
                    {
                        $arrayItem          = array();
                        $arrayItem['name']  = $key;
                        $arrayItem['value'] = 0;
                        
                        if( $arrayResultados['total'] > 0 )
                        {
                            foreach($arrayResultados['resultados'] as $arrayItemResultado)
                            {
                                if( $value == $arrayItemResultado['estado'])
                                {
                                    $arrayItem['value'] = $arrayItemResultado['total'];
                                }
                            }
                        }
                        
                        $arrayTareas[] = $arrayItem;
                    }
                }

                $response->setData( array( 'arrayTareas' => $arrayTareas ) );
                    
                break;
                
                
            case 'listado':
                
                    $arrayParametros["estado"] = $arrayEstados['Finalizadas'];
                
                    $start = $objRequest->query->get('start');
                    $limit = $objRequest->query->get('limit');
                    
                    $jsonTmpTareas    = null;
                    $intTotal         = 0;
                    $arrayEncontrados = null;

                    $arrayParametros["emComercial"]         = $emComercial;
                    $arrayParametros["emComunicacion"]      = $emComunicacion;
                    $arrayParametros["start"]               = $start;
                    $arrayParametros["limit"]               = $limit;
                    $arrayParametros["departamentoSession"] = "";
                    $arrayParametros["existeFiltro"]        = "S";

                    $arrayParametros["serviceSoporte"] = $this->get('soporte.SoporteService');
                    $arrayParametros["strUser"]        = $objSession->get('user');
                    $arrayParametros["strIp"]          = $objRequest->getClientIp();

                    $jsonTmpTareas    = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                  ->generarJsonMisTareas($arrayParametros);

                    if ($jsonTmpTareas )
                    {
                        $objTmpJsonTareas = json_decode($jsonTmpTareas);
                        
                        $intTotal         = $objTmpJsonTareas->total;
                        $arrayEncontrados = $objTmpJsonTareas->encontrados;
                    }

                    $response->setData(
                                        array(
                                                'total'           => $intTotal,
                                                'encontrados'     => array(
                                                                            'filtroUsuario'     => 'ByUsuario',
                                                                            'tipo'              => 'ByDepartamento',
                                                                            'codEmpresa'        => $intEmpresa,
                                                                            'idCuadrilla'       => null,
                                                                            'tareas'            => $arrayEncontrados
                                                                          )
                                             )
                                      );
                    
                break;
        }
                    
        return $response;
    }

    public function ajaxSetPuntoSessionLoginAction()
    {
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $objResponse = new Response();
        $strMensaje  = "";
        
        $objResponse->headers->set('Content-Type', 'text/plain');
        
        $strLoginPto = $objRequest->get('strLogin');
            
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $emComercial->getConnection()->beginTransaction();
		
        try{
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneByLogin(trim($strLoginPto));
            
            if(is_object($objInfoPunto))
            {
                $intIdPunto = $objInfoPunto->getId(); 
                $emComercial->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($intIdPunto,$objSession);
                $strMensaje .= $intIdPunto;
                $objResponse->setContent($strMensaje);
                $emComercial->getConnection()->commit();                
            }
            
        }catch(\Exception $e){
            $emComercial->getConnection()->rollback();
            error_log("Error : ".$e->getMessage());
            $strMensaje = "Error";
            $objResponse->setContent($strMensaje);
        }
        
        return $objResponse;
    }     

    /**
     * Función que confirma a hal la sugerencia seleccionada por el usuario.
     *
     * @return $objRespuesta
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 11-04-2018
     *
     * Modificacion: Se modifica el metodo para recibir el id de comunicacion por motivos que se recibió el id de tarea.
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 13-06-2018
     *
     * Modificación: Se modifica la variable $intIdComunicacion del arreglo $arrayRespuestaAsignaHal por intIdComunicacion,
     *               por motivos que estaba mal nombrada.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 07-08-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 27-08-2018 - Se agrega el nuevo parámetro atenderAntes, para el envío al WS de confirmación Hal.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 12-09-2018 - Se almacena la información de la tarea y el usuario al momento de insertar
     *                           el error para tener una mejor precisión en la búsqueda.
     *                         - Se agrega el tiempo limite de espera.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 12-07-2019 - Se agrega el parámetro strSolicitante para identificar
     *                           quien solicita las sugerencias de hal.
     *
     */
    public function confirmarSugerenciaHalAction()
    {
        set_time_limit(240); //Cuatro minutos de espera

        $objRespuesta           = new Response();
        $objPeticion            = $this->get('request');
        $strUserSession         = $objPeticion->getSession()->get('user');
        $intIdPersonaEmpresaRol = $objPeticion->getSession()->get('idPersonaEmpresaRol');
        $strIpCreacion          = $objPeticion->getClientIp();
        $intIdDetalle           = $objPeticion->get('idDetalle');
        $intIdComunicacion      = $objPeticion->get('idComunicacion');
        $intIdSugerencia        = $objPeticion->get('idSugerencia');
        $strAtenderAntes        = $objPeticion->get('atenderAntes');
        $strSolicitante         = $objPeticion->get('solicitante');
        $serviceSoporte         = $this->get('soporte.SoporteService');
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $strMensaje             = 'Fallo en la comunicación con hal<br/ >Si el problema persiste comuniquese con sistemas..!!';
        $strControlador         = 'TareasController.confirmarSugerenciaHalAction';
        $objRespuesta->headers->set('Content-Type', 'text/json');

        try
        {
            // Establecemos la comunicacion con hal
            $arrayRespuestaAsignaHal = $serviceSoporte->procesoAutomaticoHalAsigna(array (
                    'intIdDetalle'           => intval($intIdDetalle),
                    'intIdComunicacion'      => intval($intIdComunicacion),
                    'intIdPersonaEmpresaRol' => intval($intIdPersonaEmpresaRol),
                    'intIdSugerencia'        => intval($intIdSugerencia),
                    'strAtenderAntes'        => $strAtenderAntes,
                    'strSolicitante'         => $strSolicitante,
                    'boolEresHal'            => true,
                    'strUrl'                 => $this->container->getParameter('ws_hal_confirmaAsignacionAutHal')
            ));

            // Validamos si la comunicacion o la respuesta de hal fueron invalidas, caso contrario seguimos con el flujo
            if (strtoupper($arrayRespuestaAsignaHal['mensaje']) != 'OK'
                || strtoupper($arrayRespuestaAsignaHal['result']['respuesta']) != 'OK')
            {
                $arrayRespuesta['success'] = false;
                $arrayRespuesta['mensaje'] = $strMensaje;

                if (!is_null($arrayRespuestaAsignaHal['descripcion']))
                {
                    $strMensajeError = $arrayRespuestaAsignaHal['descripcion'];
                }
                elseif (!is_null($arrayRespuestaAsignaHal['result']['mensaje']))
                {
                    $strMensajeError = $arrayRespuestaAsignaHal['result']['mensaje'];
                }
                else
                {
                    $strMensajeError = 'No se Obtuvo la descripcion del error.';
                }

                // Almacenmos el error para el posterior seguimiento.
                $serviceUtil->insertError('Telcos+',
                                          $strControlador,
                                          ' IdComunicacion: '.$intIdComunicacion.
                                          ' IdDetalle: '.$intIdDetalle.
                                          ' IdPersonaEmpresaRol: '.$intIdPersonaEmpresaRol.
                                          ' Descripcion: '.$strMensajeError,
                                          $strUserSession,
                                          $strIpCreacion);
            }
            else
            {
                $arrayRespuesta['success']      = true;
                $arrayRespuesta['tipoAsignado'] = $arrayRespuestaAsignaHal['result']['tipoAsignado'];
                $arrayRespuesta['idAsignado']   = $arrayRespuestaAsignaHal['result']['idAsignado'];
                $arrayRespuesta['fecha']        = $arrayRespuestaAsignaHal['result']['fecha'];
                $arrayRespuesta['horaIni']      = $arrayRespuestaAsignaHal['result']['horaIni'];

                if ($arrayRespuesta['tipoAsignado'] === 'empleado')
                {
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->find($arrayRespuesta['idAsignado']);

                    $arrayRespuesta['empleadoAsignado']     = $objInfoPersonaEmpresaRol->getPersonaId();
                    $arrayRespuesta['departamentoAsignado'] = $objInfoPersonaEmpresaRol->getDepartamentoId();
                }
                else
                {
                    $arrayInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->findBy( array ('cuadrillaId' => $arrayRespuesta['idAsignado'],
                                         'estado'      => 'Activo'));

                    $arrayRespuesta['cuadrillaAsignada']    = $arrayRespuesta['idAsignado'];
                    $arrayRespuesta['departamentoAsignado'] = $arrayInfoPersonaEmpresaRol[0]->getDepartamentoId();
                }
            }

            $objResultado = json_encode($arrayRespuesta);
        }
        catch(\Exception $objException)
        {
            error_log('Error - '.$strControlador.' -> Mensaje: '.$objException->getMessage());
            $arrayRespuesta['success'] = false;
            $arrayRespuesta['mensaje'] = $strMensaje;
            $serviceUtil->insertError('Telcos+',
                                       $strControlador,
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpCreacion);
            $objResultado = json_encode($arrayRespuesta);
        }

        $objRespuesta->setContent($objResultado);

        return $objRespuesta;
    }

    /**
     * ajaxGetEmpresasHabilitadas
     *
     * Metodo encargado de obtener el nombre de cada empresa habilitadas en la aplicación en gestión
     *
     * @return json con las empresas por sistema
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 - 20/11/2018
     * 
     * Actualización: Se reemplaza función generarJsonEmpresasPorSistema por generarJsonEmpresasVisiblesEnTareas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 - 17/12/2018
     * 
     */
    public function ajaxGetEmpresasHabilitadasAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion    = $this->get('request');
        $objSession     = $objPeticion->getSession();
        $strPrefijo     = $objSession->get('prefijoEmpresa');
        $strTienePerfil = "N";

        $objEmComercial = $this->getDoctrine()->getManager('telconet');

        //si la persona en sesión tiene la credencial: verTareasTodasEmpresas (ROLE_197_6157)
        //se consulta empresas habilitadas para mostrarlas en el combo
        if (true === $this->get('security.context')->isGranted('ROLE_197-6157'))
        {
            $strTienePerfil = 'S';
        }

        $arrayParametros                    = array();
        $arrayParametros['prefijoConsulta'] =  $strPrefijo;
        $arrayParametros['prefijoExcluido'] =  "";
        $arrayParametros['tienePerfil']     =  $strTienePerfil;
        $objResultado = $objEmComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->generarJsonEmpresasVisiblesEnTareas($arrayParametros);

        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

    /**
     * ajaxReintentoTareaSysCloudAction
     *
     * Función que reintanta la creación de la tarea en el Sistema de Data-Center - Sys Cloud-Center.
     *
     * @return $objRespuesta
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 22-01-2018
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 06-02-2020 - Se cambia el formato de la fecha y hora a la actual.
     * @since 1.0
     *
     */
    public function ajaxReintentoTareaSysCloudAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $serviceProceso = $this->get('soporte.ProcesoService');
        $serviceUtil    = $this->get('schema.Util');
        $objPeticion    = $this->get('request');
        $strUserSession = $objPeticion->getSession()->get('user');
        $strIpSession   = $objPeticion->getClientIp();
        $objDatos       = json_decode($objPeticion->get('datos'));

        try
        {
            if ($objDatos->numeroTarea === null || $objDatos->numeroTarea === '')
            {
                throw new \Exception('Error : Número de tarea Nulo..!!');
            }

            $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($objDatos->numeroTarea);

            if (!is_object($objInfoComunicacion))
            {
                throw new \Exception('Error : El número de tarea '.$objDatos->numeroTarea.' no se encuentra registrado');
            }

            $arrayRespuesta = $serviceProceso->putTareasSysCluod(
                    array ('strNombreTarea'      => $objDatos->nombreTarea,
                           'strNombreProceso'    => $objDatos->nombreProceso,
                           'strObservacion'      => $objDatos->observacion,
                           'strFechaApertura'    => date("Y-m-d"),
                           'strHoraApertura'     => date('H:i:s'),
                           'strUser'             => $strUserSession,
                           'strIpAsigna'         => $strIpSession,
                           'strUserAsigna'       => $objPeticion->getSession()->get('empleado'),
                           'strDeparAsigna'      => $objPeticion->getSession()->get('departamento'),
                           'strUserAsignado'     => $objDatos->asignado,
                           'strDeparAsignado'    => $objDatos->depAsignado,
                           'objInfoComunicacion' => $objInfoComunicacion,));

            if (empty($arrayRespuesta) || $arrayRespuesta['status'] === 'fail')
            {
                throw new \Exception($arrayRespuesta['message']);
            }

            $objResultado = json_encode(array ('status'  => true,
                                               'message' => $arrayRespuesta['message']));
        }
        catch(\Exception $objException)
        {
            $strMessage = 'Error al crear la tarea en Sys Cloud-Center';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
            }

            $serviceUtil->insertError('Telcos+',
                                      'TareasController->ajaxReintentoTareaSysCloudAction',
                                       $objException->getMessage().' - '.$objPeticion->get('datos'),
                                       $strUserSession,
                                       $strIpSession);

            $objResultado = json_encode(array ('status'  => false,
                                               'message' => $strMessage));
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
    
    
    /**
     * confirmarIpServicioSoporteAction
     *
     * Función que ingresa el progreso de confirmación de la ip del servicio en una tarea de soporte Telconet.
     *
     * @return $objRespuesta
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 04-07-2020
     * 
     *
     */
    public function confirmarIpServicioSoporteAction()
    {
        $serviceSoporte             = $this->get('soporte.SoporteService');
        $objPeticion                = $this->get('request');
        $arrayParametros            = json_decode($objPeticion->get('data'),true);    
        $intIdEmpresa               = $arrayParametros['idEmpresa'];
        $intIdComunicacion          = $arrayParametros['idComunicacion'];
        $intDetalleId               = $arrayParametros['idDetalle'];
        $strCodigoProgreso          = $arrayParametros['strCodigoProgreso'];
        $intIdServicio              = $arrayParametros['idServicio'];     
        $strOrigenProgreso          = $arrayParametros['strOrigenProgreso'];
        $serviceUtil                = $this->get('schema.Util'); 
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $arrayRequest               = $this->get('request');
        $strIpCreacion              = $arrayRequest->getClientIp();
        $objRespuesta               = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        try 
        {
            $arrayProgresoActa     = array(
                                            'strCodEmpresa'         => $intIdEmpresa,
                                            'intIdTarea'            => $intIdComunicacion,
                                            'intIdDetalle'          => $intDetalleId,
                                            'strCodigoTipoProgreso' => $strCodigoProgreso,
                                            'intIdServicio'         => $intIdServicio,
                                            'strOrigen'             => $strOrigenProgreso,
                                            'strUsrCreacion'        => $objSession->get('user'),
                                            'strIpCreacion'         => $strIpCreacion);
                        
            $arrayRespuesta    = $serviceSoporte->ingresarProgresoTarea($arrayProgresoActa);
            $strStatus         = $arrayRespuesta['status'];
            $strMensaje        = $arrayRespuesta['mensaje'];
            
        }
        catch(\Exception $ex)
        {
            $strStatus      = "ERROR";
            $strMensaje     = "Se presentaron errores al confirmar el enlace.";
            $serviceUtil->insertError('Telcos+', 
                                      'TareasController.confirmarIpServicioSoporteAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $strIpCreacion
                                     );
        }
        
        $arrayResultado['status']    = $strStatus;
        $arrayResultado['mensaje']   = $strMensaje;
        $objRespuesta->setContent(json_encode($arrayResultado));
        
        return $objRespuesta;
    }

    /**
     * validarServicioSoporteAction
     *
     * Función que consume la lógica de validación de enlaces.
     *
     * @return $objRespuesta
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 28-07-2020
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 08-12-2020
     * Se agrega campo "strTieneProgConfirIPserv" al retorno de variables para confirmar si
     * ya tiene el progreso de validación de enlaces
     *
     */
    public function validarServicioSoporteAction()
    {
        
        $objPeticion                = $this->get('request');
        $arrayParametros            = json_decode($objPeticion->get('data'),true);    
        $serviceUtil                = $this->get('schema.Util'); 
        $objRespuesta               = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $emGeneral                      = $this->getDoctrine()->getManager('telconet_general');
        $emComercial                    = $this->getDoctrine()->getManager("telconet");
        $serviceTecnico                 = $this->get('tecnico.InfoServicioTecnico');
        $serviceUtil                    = $this->get('schema.Util');
        $strProgresoConfirIpServ        = "CONFIRMA_IP_SERVICIO";
        
        $intEmpresaId                   = $arrayParametros['idEmpresa'];
        $intServicioId                  = $arrayParametros['servicioId'];
        $intCasoId                      = $arrayParametros['casoId'];
        $intDetalleId                   = $arrayParametros['idDetalle'];
        $strUsrCreacion                 = $arrayParametros['user'];
        $intComunicacionId              = $arrayParametros['idComunicacion'];
        $strEmpresaCod                  = $arrayParametros['empresaCod'];
        $strUltimaMilla                 = $arrayParametros['ultimaMilla'];
        $intDepartamentoId              = $arrayParametros['departamentoId'];

        $arrayRespuesta                 = array();
        $intIdCabEnlaceFibra            = 0;
        
        $strParametroLatenciaMax        = "";
        $strParametroPaquetesRecibir    = "";
        $strParametroLatenciaPromedio   = "";
        $arrayDataParametroConfirm      = "";
        $strUnidadLatencia              = "";
      
        try
        {
            $arrayParametroUnidadLat = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('UNIDADES_CONFIRMAR_ENLACE', 
                        '', 
                        '', 
                        '', 
                        'UNIDAD_LATENCIA_ENLACE', 
                        '', 
                        '', 
                        ''
                    );

            if (is_array($arrayParametroUnidadLat))
            {
                $strUnidadLatencia = !empty($arrayParametroUnidadLat['valor2']) ? $arrayParametroUnidadLat['valor2'] : "";
            }
        
            $arrayParamEnlacesFibra     = array(
                                                    'nombreParametro' => $strUltimaMilla,
                                                    'estado'          => "Activo"
                                                );
            
            $arrayParamFiltroTipoIps    = array(
                                                    'nombreParametro' => "VALIDACION_ENLACE_TIPOS_IP",
                                                    'estado'          => "Activo"
                                        );

            $entityParametroFiltroCabecera  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneByNombreParametro($arrayParamFiltroTipoIps);

            $entityParametroCab             = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneByNombreParametro($arrayParamEnlacesFibra);

            
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdCabEnlaceFibra  = $entityParametroCab->getId();
                $intIdCabFiltroValida = $entityParametroFiltroCabecera->getId(); 

                $arrayDetLatMax             = array( 
                                                    'estado'      => "Activo", 
                                                    'parametroId' => $intIdCabEnlaceFibra,
                                                    'descripcion' => "MAX_LATENCIA_MAXIMA"
                                                    );

                $arrayDetPaqRecibido        = array( 
                                                    'estado'      => "Activo", 
                                                    'parametroId' => $intIdCabEnlaceFibra,
                                                    'descripcion' => "MIN_PORCENTAJE_PAQUETES_RECIBIDO"
                                                    );

                $arrayDetLatAvg             = array( 
                                                    'estado'      => "Activo", 
                                                    'parametroId' => $intIdCabEnlaceFibra,
                                                    'descripcion' => "MAX_LATENCIA_MEDIA"
                                                    );
                
               $arrayDetFiltro             = array( 
                                                    'estado'      => "Activo", 
                                                    'parametroId' => $intIdCabFiltroValida,
                                                    'valor1'      => "FILTRO_VTIPOS_IP"
                                                    );
                
                $objDetLatMax               = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy($arrayDetLatMax);
                $objDetPaqRecibido          = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy($arrayDetPaqRecibido);
                $objDetLatAvg               = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy($arrayDetLatAvg);
                $objDetFiltro               = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy($arrayDetFiltro);
                                                        
                
                
                if(isset($objDetLatMax))
                {
                    $strParametroLatenciaMax         = $objDetLatMax->getValor1() ? $objDetLatMax->getValor1() : '';
                }
                if(isset($objDetPaqRecibido))
                {
                    $strParametroPaquetesRecibir     = $objDetPaqRecibido->getValor1() ? $objDetPaqRecibido->getValor1() : '';
                }
                if(isset($objDetLatAvg))
                {
                    $strParametroLatenciaPromedio    = $objDetLatAvg->getValor1() ? $objDetLatAvg->getValor1() : '';
                }
                if(isset($objDetFiltro))
                {
                    $arrayFiltro                     = array($objDetFiltro->getValor2(),$objDetFiltro->getValor3(),$objDetFiltro->getValor4()); 
                }
                
                $arrayDataPaquetes          = array(
                                                    'sent'          => (int)$strParametroPaquetesRecibir,
                                                    'received'      => (int)$strParametroPaquetesRecibir,
                                                    'lost'          => 0
                                                );

                $arrayDataParametroConfirm  = array(
                                                        'statusPing'            => false,
                                                        'packages'              => $arrayDataPaquetes,
                                                        'latency'               => $arrayDataLatencia
                                                    );

            }
            
            
            $arrayValidaProgreso        = array(
                                                'intServicioId'          => $intServicioId,
                                                'intEmpresaCod'          => $intEmpresaId,
                                                'tipoProgreso'           => $strProgresoConfirIpServ,
                                                'detalleId'              => $intDetalleId,
                                                'casoId'                 => $intCasoId,                            
                                                'user'                   => $strUsrCreacion
                                                );
            
            $strTieneConfirIPserv   = $emGeneral->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->validaProgresoTarea($arrayValidaProgreso);
            $arrayDataCliente           = array(
                                                    'intServicioId'       => $intServicioId,
                                                    'arrayFiltro'         => $arrayFiltro
                                                    );

            $arrayDataTecnica           = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                      ->getIpWanClienteSoporte($arrayDataCliente);

            if($strTieneConfirIPserv == 'NO')
            {
              

                $arrayDataRegion            = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                      ->getRegionClienteSoporte($arrayDataCliente);
                
                $arrayConfirmarEnlace       = array(
                                                        'intServicioId'             => $intServicioId,
                                                        'intEmpresaCod'             => $intEmpresaId,
                                                        'tipoProgreso'              => $strProgresoConfirIpServ,
                                                        'detalleId'                 => $intDetalleId,
                                                        'user'                      => $strUsrCreacion,
                                                        'idComunicacion'            => $intComunicacionId,
                                                        'ipWanCliente'              => $arrayDataTecnica['ipWanCliente'],
                                                        'empresaCod'                => $strEmpresaCod,
                                                        'ultimaMilla'               => $strUltimaMilla,
                                                        'parametroLatenciaMax'      => $strParametroLatenciaMax,
                                                        'parametroPaquetesRecibir'  => $strParametroPaquetesRecibir,
                                                        'parametroLatenciaPromedio' => $strParametroLatenciaPromedio,
                                                        'regionCliente'             => $arrayDataRegion['nombreRegion'], 
                                                        'departamentoId'            => $intDepartamentoId,
                                                        'unidadLatencia'            => $strUnidadLatencia,
                                                        'origenWeb'                 => true
                                                    );
                
                
                $arrayResponseNw    = $serviceTecnico->confirmarEnlaceWsNwSoporte($arrayConfirmarEnlace);
                
                $strResult          = $arrayResponseNw['result'];
                $strMensaje         = $arrayResponseNw['mensaje'];
                $arrayData          = $arrayResponseNw['data'];
                $boolStatusPing     = false;  

                if($arrayResponseNw['result'])
                {
                    $boolStatusPing = true;
                }
            }
            else
            {
                $arrayDataParametroConfirm['strTieneProgConfirIPserv'] = $strTieneConfirIPserv;
                $strResult          = true;
                $strMensaje         = "El enlace fue validado correctamente.";
                $arrayData          = $arrayDataParametroConfirm;
                $boolStatusPing     = true;
            } 
            
            $arrayRespuesta['message']              = $this->mensaje['OK'];
            $arrayRespuesta['status']               = $this->status['OK'];
            $arrayRespuesta['result']               = $strResult;
            $arrayRespuesta['data']                 = $arrayData;
            $arrayRespuesta['data']['statusPing']   = $boolStatusPing;
            $arrayRespuesta['data']['message']      = $strMensaje;
            $arrayRespuesta['data']['ipClient']     = $arrayDataTecnica['ipWanCliente'];
            
        }
        catch(\Exception $exception)
        {

            $arrayRespuesta['message']              = $this->mensaje['ERROR'];
            $arrayRespuesta['status']               = $this->status['ERROR'];
            $arrayRespuesta['result']               = false;
            $arrayRespuesta['data']                 = $arrayDataParametroConfirm;
            $arrayRespuesta['data']['message']      = $arrayRespuesta['message'];
            $arrayRespuesta['data']['ipClient']     = $arrayDataTecnica['ipWanCliente'];
            $strClass                               = "TecnicoWSController";
            $strAppMethod                           = "getValidarEnlaces";
            
            
            $serviceUtil->insertLog(array(
                              'enterpriseCode'      => "10",
                              'logType'             => 1,
                              'logOrigin'           => 'TELCOS',
                              'application'         => 'TELCOS',
                              'appClass'            => $strClass,
                              'appMethod'           => $strAppMethod,
                              'descriptionError'    => $exception->getMessage(),
                              'status'              => 'Fallido',
                              'inParameters'        => json_encode($arrayData),
                              'creationUser'        => $strUsrCreacion));
            
        }
        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }

    /**
     * permiteCrearKmlAction
     *
     * Función que añade la caracteristica
     * para permitir que una tarea pueda crear un KML desde el aplicativo móvil
     *
     * @return $objRespuesta
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 22-11-2020
     * 
     *
     */
    public function permiteCrearKmlAction()
    {
        $objPeticion                = $this->get('request');
        $arrayParametros            = json_decode($objPeticion->get('data'),true);    
        $serviceUtil                = $this->get('schema.Util'); 
        $objRespuesta               = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $emGeneral                      = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte                      = $this->getDoctrine()->getManager("telconet_soporte");
        $emSoporte->getConnection()->beginTransaction();
        $serviceUtil                    = $this->get('schema.Util');
        
        $intDetalleId                   = $arrayParametros['idDetalle'];
        $strUsrCreacion                 = $arrayParametros['user'];
        $intComunicacionId              = $arrayParametros['idComunicacion'];
        $arrayRespuesta                 = array();
        $strMensajeValidacion           = '';
      
        try
        {
            $objParametroCab        = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array("nombreParametro" =>'CONF_ERRORES_GENERALES_TMO',
                                                                    "estado"          => "Activo"));
            if(is_object($objParametroCab))
            {
                $objParametroDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy(array("parametroId"    =>$objParametroCab->getId(),
                                                                        "estado"         => "Activo"));
                if($objParametroDet)
                {
                    $strMensajeValidacion = $objParametroDet->getValor1();
                }                                                                        
            }
            //se obtiene la caracteristica que se desea poner a nivel de tarea.
            $objAdmiCaracteristica = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array ('descripcionCaracteristica' => 'AUTH_CREACION_KML',
                                                                  'estado'                    => 'Activo'));
            if (!is_object($objAdmiCaracteristica))
            {
                throw new \Exception('Error : La característica de exportación no se encuentra Activa. '
                        .'Por favor comuniquese con Sistemas.');
            }
            $objInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                    ->findOneBy(array ('detalleId'        => $intDetalleId,
                                       'caracteristicaId' => $objAdmiCaracteristica->getId(),
                                       'estado'           => 'Activo'));

            if ($objInfoTareaCaracteristica == null && !is_object($objInfoTareaCaracteristica))
            {
                $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                $objInfoTareaCaracteristica->setTareaId($intComunicacionId);
                $objInfoTareaCaracteristica->setDetalleId($intDetalleId);
                $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristica->getId());
                $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                $objInfoTareaCaracteristica->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objInfoTareaCaracteristica->setIpCreacion($objPeticion->getClientIp());
                $objInfoTareaCaracteristica->setValor("S");
                $objInfoTareaCaracteristica->setEstado('Activo');
                $emSoporte->persist($objInfoTareaCaracteristica);
                $emSoporte->flush();
                
            }
            $emSoporte->getConnection()->commit();
            $strResult          = true;
            $strMensaje         = "La caracteristica fue empleada correctamente.";
            
            $arrayRespuesta['message']              = $this->mensaje['OK'];
            $arrayRespuesta['status']               = $this->status['OK'];
            $arrayRespuesta['result']               = $strResult;
            $arrayRespuesta['data']['message']      = $strMensaje;
            $arrayRespuesta['data']['ipClient']     = $arrayDataTecnica['ipWanCliente'];
            
        }
        catch(\Exception $exception)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
           
            $arrayRespuesta['message']              = $strMensajeValidacion;
            $arrayRespuesta['status']               = $this->status['ERROR'];
            $arrayRespuesta['result']               = false;
            $arrayRespuesta['data']['message']      = $arrayRespuesta['message'];
            $arrayRespuesta['data']['ipClient']     = $arrayDataTecnica['ipWanCliente'];
            $strClass                               = "TecnicoWSController";
            $strAppMethod                           = "permiteCrearKmlAction";
            
            $serviceUtil->insertLog(array(
                              'enterpriseCode'      => "10",
                              'logType'             => 1,
                              'logOrigin'           => 'TELCOS',
                              'application'         => 'TELCOS',
                              'appClass'            => $strClass,
                              'appMethod'           => $strAppMethod,
                              'descriptionError'    => $exception->getMessage(),
                              'status'              => 'Fallido',
                              'inParameters'        => json_encode($arrayData),
                              'creationUser'        => $strUsrCreacion));
            
        }
        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }
    /**
    *
    * Función que retorna los motivos de categorias de tareas
    *
    * @return $objRespuesta
    *
    * @author Andrés Montero H <amontero@telconet.ec>
    * @version 1.0 31-07-2020
    * 
    *
    */
    public function getMotivosCategoriaTareasAction()
    {
        $serviceSoporte       = $this->get('soporte.SoporteService');
        $objPeticion          = $this->get('request');
        $strValor1            = $objPeticion->get('valor1');
        $strValor2            = $objPeticion->get('valor2');
        $strValor3            = $objPeticion->get('valor3');
        $serviceUtil          = $this->get('schema.Util');
        $objSession           = $this->getRequest()->getSession();
        $objRequest           = $this->get('request');
        $strIpCreacion        = $objRequest->getClientIp();
        $objRespuesta         = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        try 
        {
            $arrayParametros     = array(
                                            'strValor1'   => $strValor1,
                                            'strValor2'   => $strValor2,
                                            'strValor3'   => $strValor3,
                                        );
                        
            $arrayRespuesta    = $serviceSoporte->getMotivosCategoriaTareas($arrayParametros);
            
        }
        catch(\Exception $ex)
        {
            $strStatus      = "ERROR";
            $strMensaje     = "Se presentaron errores al obtener motivos de categorías de tareas.";
            $serviceUtil->insertError('Telcos+', 
                                      'TareasController.getMotivosCategoriaTareasAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $strIpCreacion
                                     );
        }
        
        $objRespuesta->setContent($arrayRespuesta);
        return $objRespuesta;
    }

    /*
    * Documentación para la función 'ajaxNotificarCancelarSugerenciasHal'.
    *
    * Función que notifica a Hal cancelar Sugerencias
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 27-10-2020
    * @return JsonResponse
    *
    */
    public function ajaxNotificarCancelarSugerenciasHalAction()
    {
        $objRequest            = $this->get('request');
        $strIdsSugerencia      = $objRequest->get('idSugerencia');
        $objServiceUtil        = $this->get('schema.Util');
        $objSession            = $objRequest->getSession();
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $objEmSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strStatus             = 'OK';
        $objServiceSoporte     = $this->get('soporte.SoporteService');
        $arrayRespuesta        = array();
        $strIdsSugerencia      = isset($strIdsSugerencia)?substr($strIdsSugerencia,0,strlen($strIdsSugerencia)-1):"";
        $arraySugerencias      = explode("|",$strIdsSugerencia);
        /*========================= INICIO NOTIFICACION HAL ==========================*/
        $arrayRespuesta = $objServiceSoporte->notificacionesHal(
                            array ('strModulo'  => 'NOTIFICARCANCELARSUGERENCIA',
                                    'strUser'   =>  $strUsrCreacion,
                                    'strIp'     =>  $strIpCreacion,
                                    'arrayJson' =>  array (
                                                            'idPersona'          => $intIdPersonEmpresaRol,
                                                            'listaIdsSugerencia' => $arraySugerencias
                                                          )
                                  ));
        /*========================== FIN NOTIFICACION HAL ============================*/

        // Validamos la respuesta de la DB
        if ($arrayRespuesta['result']['status'] == '200' || $arrayRespuesta['result']['status'] == 200 || 
            strtoupper($arrayRespuesta['result']['respuesta']) == 'OK' )
        {
            $strMensaje = 'Se ejecuto correctamente el proceso';
            $strStatus  = 'Ok';
        }
        else
        {
            $strMensaje = 'Se produjo un error al ejecutar el proceso, notificar a Sistemas. ';
            $strStatus  = 'Error';
            
            $strMensajeInfoError .=isset($arrayRespuesta['result']['msg'])?
                                   $arrayRespuesta['result']['msg']:(isset($arrayRespuesta['result']['mensaje'])?
                                   $arrayRespuesta['result']['mensaje']:"");

            $objServiceUtil->insertError('Telcos+', 
                                         'ajaxNotificarCancelarSugerenciasHalAction', 
                                         $strMensajeInfoError,
                                         $strUsrCreacion, 
                                         $strIpCreacion);
        }

        $objResponse = new JsonResponse();
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => 'Se ejecuto proceso correctamente'));
        return $objResponse;
    }


    /**
     * getDetalleTareaAction
     *
     * Método que retorna el detalle de tarea por los diferentes departamentos
     * 
     * @return JsonResponse $response         
     *
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 15-01-2021
     * 
    */
    public function getDetalleTareaAction()
    {

        $objRequest   = $this->get('request');
        $objEmSoporte = $this->getDoctrine()->getManager('telconet_soporte');

        $intIdDetalle = $objRequest->get('idDetalle') ? $objRequest->get('idDetalle') : '';

        $arrayParametros['idDetalle']  = $intIdDetalle;

        $objInfoDetHistRepo = $objEmSoporte->getRepository("schemaBundle:InfoDetalleHistorial");
        $arrayDetallet = $objInfoDetHistRepo->generarDetalleHistoriaTarea($arrayParametros);
        
        return new JsonResponse($arrayDetallet);
    }

}
