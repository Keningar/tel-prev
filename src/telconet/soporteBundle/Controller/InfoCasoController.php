<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoCaso;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleHipotesis;
use telconet\schemaBundle\Entity\InfoDetalleTareaElemento;
use telconet\schemaBundle\Entity\InfoDetalleMaterial;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoDestinatario;
use telconet\schemaBundle\Entity\InfoDetalleTareaTramo;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoCasoAsignacion;
use telconet\schemaBundle\Entity\InfoCasoHistorial;
use telconet\schemaBundle\Entity\InfoCasoTiempoAsignacion;
use telconet\schemaBundle\Entity\InfoTareaTiempoAsignacion;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoCuadrillaTarea;
use telconet\schemaBundle\Entity\InfoEncuesta;
use telconet\schemaBundle\Entity\InfoEncuestaPregunta;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;

use telconet\schemaBundle\Form\InfoCasoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\soporteBundle\Service\SoporteService;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * InfoCaso controller.
 *
 */
class InfoCasoController extends Controller implements TokenAuthenticatedController
{
    /**
    * indexAction
    *  
    * 
    * @version 1.0
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 15-11-2016 Se realizan ajustes para enviar información del id del departamento y del cantón en sesión
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.2 03-15-2018 Se agrega el rol para la asignacion de tareas por medio de la interface de Hal
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.3 06-11-2018 Se adiciona el párametro strOrigen para precargar el grid de casos creados desde el móvil
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.4 06-09-2019 Se adiciona el párametro buscaPorArbolHipotesis con el cual se consulta si la empresa que esta en sesión
    *                         esta parametrizada para consultar las hipótesis en forma de arbol
    * 
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.5 10-02-2021 Se adiciona el párametro fechaDetTareas fecha de referencia para cierre de casos
    *
    * @author David De La Cruz <ddelacruz@telconet.ec>
    * @version 1.6 
    * @since 15-03-2022 Se actualiza para encapsular en una función el arreglo de roles permitidos, 
    * para reutilizar esta función, en el método de consulta de Casos creados en Extranet TN.
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.7 13-01-2023 - Se agrega funcionalidad para validar perfil e insertar log asociado.
    *
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.8 14-04-2023 - Se renombra variable origen utilizada para insertar logs.
    */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_78-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-6';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-39'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-39';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-42'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-42';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-32'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-32';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-31'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-31';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-33'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-33';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-51'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-51';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_78-36'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-36';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_57-5837'))
        {
            $arrayRolesPermitidos[] = 'ROLE_57-5837'; //solicitar informe ejecutivo
        }
        if (true === $this->get('security.context')->isGranted('ROLE_57-5838'))
        {
            $arrayRolesPermitidos[] = 'ROLE_57-5838'; //editar informa ejecutivo 
        }        
        // Rol permitido para asignar tareas mediante la pestaña de hal
        if (true === $this->get('security.context')->isGranted('ROLE_78-5822'))
        {
            $arrayRolesPermitidos[] = 'ROLE_78-5822';
        }

        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $clienteSesion = $objSession->get('cliente');
        
        $boolClienteSesion = false;
        
        if($clienteSesion)
        {
            $boolClienteSesion = true;
        }
        $strOrigen    = ($objRequest->query->get('strOrigen') ? $objRequest->query->get('strOrigen') : "");
        $strTipoConsulta = ($objRequest->query->get('strTipoConsulta') ? $objRequest->query->get('strTipoConsulta') : "");
        $emComercial  = $this->getDoctrine()->getManager("telconet");
        $emSeguridad  = $this->getDoctrine()->getManager("telconet_seguridad");
        $objEmGeneral = $this->getDoctrine()->getManager("telconet_general");
        
        $strPrefijoEmpresaSession       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdCantonUsrSession          = 0;
        $intIdOficinaSesion             = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        if($intIdOficinaSesion)
        {
            $objOficinaSesion           = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            if(is_object($objOficinaSesion))
            {
                $intIdCantonUsrSession  = $objOficinaSesion->getCantonId();
            }
        }
        $intIdDepartamentoUsrSession    = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $entityItemMenu                 = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");		
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $arrayAdmiParametroDet = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strEmpresaCod);
        if($arrayAdmiParametroDet['valor2']==='S')
        {
            $strBuscarPorArbolHipotesis = 'S';
        }


        $objParamCierreCaso = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAMETROS_CIERRE_CASO',
                                                     'SOPORTE',
                                                     'CIERRE_CASO',
                                                     'FECHA_REFERENCIA_CIERRE_CASOS',
                                                     '', '', '', '', '', 
                                                     $strEmpresaCod);


        $strFechaDetTareas = '';
        if($objParamCierreCaso)
        {
            $strFechaDetTareas = $objParamCierreCaso['valor1'];
        }

        $strUsrCreacion       = $objSession->get('user');
        $arrayCliente         = $objSession->get('cliente');
        $arrayPtoCliente      = $objSession->get('ptoCliente'); 
        $strIpCreacion        = $objRequest->getClientIp();
        $serviceInfoLog       = $this->get('comercial.InfoLog');
        $serviceTokenCas      = $this->get('seguridad.TokenCas');
        $arrayDatosCliente    = array();

        if($strPrefijoEmpresaSession == 'MD')
        { 
            if((true === $this->get('security.context')->isGranted('ROLE_78-1')))
            {          
                if(!empty($arrayCliente))
                {
                     $objInfoPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayCliente['id']);

                     if(is_object($objInfoPersona))
                     {
                         $arrayDatosCliente['nombres']            = $objInfoPersona->getNombres();
                         $arrayDatosCliente['apellidos']          = $objInfoPersona->getApellidos();
                         $arrayDatosCliente['razon_social']       = $objInfoPersona->getRazonSocial();
                         $arrayDatosCliente['identificacion']     = $objInfoPersona->getIdentificacionCliente();
                         $arrayDatosCliente['tipoTributario']     = $objInfoPersona->getTipoTributario();
                         $arrayDatosCliente['tipoIdentificacion'] = $objInfoPersona->getTipoIdentificacion();
                         $arrayDatosCliente['login']              = $arrayPtoCliente['login'];

                     }                 
                } 
                $strOrigenLog     = '';
                $strMetodo        = '';
                $objAdmiParametroCab = $objEmGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                                      'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objParamDetOrigen = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                        'descripcion' => 'ORIGEN',
                                                                        'empresaCod'  => $strEmpresaCod,
                                                                        'estado'      => 'Activo'));

                    $objParamDetMetodo = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                        'observacion'     => 'CASOS',
                                                                        'empresaCod'      => $strEmpresaCod,
                                                                        'estado'          => 'Activo'));           
                    if(is_object($objParamDetOrigen))
                    {
                        $strOrigenLog  = $objParamDetOrigen->getValor1();
                    }

                    if(is_object($objParamDetMetodo))
                    {
                        $strMetodo  = $objParamDetMetodo->getValor1();
                    }             
                }
                $arrayParametrosLog                   = array();
                $arrayParametrosLog['strOrigen']      = $strOrigenLog;
                $arrayParametrosLog['strMetodo']      = $strMetodo;
                $arrayParametrosLog['strTipoEvento']  = 'INFO';
                $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
                $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
                $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
                $arrayParametrosLog['strIdKafka']     = '';
                $arrayParametrosLog['request']        = $arrayDatosCliente;


                $arrayTokenCas               = $serviceTokenCas->generarTokenCas();
                $arrayParametrosLog['token'] = $arrayTokenCas['strToken'];
                $serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            }
            else if(false === $this->get('security.context')->isGranted('ROLE_78-1'))
            {
                return $this->render('soporteBundle:InfoCaso:accesoDenegado.html.twig');
            }        
        }
        
        return $this->render('soporteBundle:info_caso:index.html.twig', array(
            'strOrigen'                     => $strOrigen,
            'item'                          => $entityItemMenu,
            'rolesPermitidos'               => $arrayRolesPermitidos,
            'clienteSesion'                 => $boolClienteSesion,
            'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
            'intIdCantonUsrSession'         => $intIdCantonUsrSession,
            'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
            'buscaPorArbolHipotesis'        => $strBuscarPorArbolHipotesis,
            'fechaDetTareas'                => $strFechaDetTareas,
            'strTipoConsulta'               => $strTipoConsulta
		));
    }
    /**
    * showAction
    *
    * Esta funcion muestra el detalle de la informacion de un numero de Caso
    *
    * @param String $id
    *
    * @author José Bedón <jobedon@telconet.ec>
    * @version 2.5 15-01-2021 Se adiciona detalle de las tareas para TN,
    *                         Se adiciona el párametro fechaDetTareas fecha de referencia para cierre de casos
    * 
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 2.4 06-09-2019 Se adiciona el párametro buscaPorArbolHipotesis con el cual se consulta si la empresa que esta en sesión
    *                         esta parametrizada para consultar las hipótesis en forma de arbol
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.3 22-04-2019 - En el return del método, se agregan los valores totales del cierre del caso.
    *                         - Se obtiene el número de casos aperturados de clientes, previo al masivo creado.
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.2 21-11-2018 - Se reemplaza el método generarJsonAfectadosTotalXCaso por getAfectadosCaso
     *                          para optimizar el tiempo de respuesta de los afectados.
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 2.1 03-15-2018 Se agrega el rol para la asignacion de tareas por medio de la interface de Hal
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 2.0 14-11-2017 Se realizan ajustes para que cuando no exista ya activo el usuario creador del caso y se necesite
    *                         su departamento permita al menos tener gestión de hipotesis sobre el caso dado que si no el caso
    *                         quedaria sin ninguna accion
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.9 09-03-2017 Se realizan ajustes para agregar la empresa de creación del caso
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.8 15-11-2016 Se realizan ajustes para enviar información del id del departamento y del cantón en sesión 
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.7 11-11-2016 Se realizan ajustes para calcular el tiempo de un caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.6 12-10-2016 Se agrega validacion para identificar el departamento del usuario creador del caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.5 16-09-2016 Se realiza ajuste que permita Agregar Hipotesis a un integrante del mismo departamento, en los casos
    *                         en donde solo se ha ingresado sintoma.
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.4 22-06-2016 Se realizan ajustes para los casos con las tareas canceladas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 27-04-2016 Se realiza ajuste para que se presente el boton de cerrar caso cuando existan tareas que esten canceladas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 05-04-2016 Se realizan ajustes para permitir actualizar la fecha de asignacion
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes para presentar la accion de Cerrar Caso dependiendo del rol del usuario en session
    *
    * @version 1.0
    *
    * @Secure(roles="ROLE_78-6")
    */ 
    public function showAction($id)
    {
        $session = $this->get('request')->getSession();

        $em                 = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objSoporteService  = $this->get('soporte.SoporteService');
        $arrayPermisos      = array();

        $intTiempoCliente     = 0;
        $intTiempoEmpresa     = 0;
        $intTiempoIncidencia  = 0;
        $intTiempoTotalCierre = 0;
        $intCantidadCasosAp   = 0;

        // Rol permitido para asignar tareas mediante la pestaña de hal
        if (true === $this->get('security.context')->isGranted('ROLE_78-5822'))
        {
            $arrayPermisos[] = 'ROLE_78-5822';
        }

        $band                       = "N";
        $mostrarHipotesis           = "N";
        $strEmpresaCreacion         = "";
        $entity                     = $em->getRepository('schemaBundle:InfoCaso')->find($id);
        $prefijoEmpresa             = $session->get('prefijoEmpresa');
        $strEmpresaCod              = $session->get('idEmpresa');
        $strBuscarPorArbolHipotesis = 'N';
        $strEsCasoNuevoEsquema      = "N";
        $intIdCantonSesion          = 0;
        $intIdOficinaSesion         = $session->get('idOficina') ? $session->get('idOficina') : 0;
        $intIdDepartamentoSesion    = $session->get('idDepartamento') ? $session->get('idDepartamento') : 0;
        if($intIdOficinaSesion)
        {
            $objOficinaSesion   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            if(is_object($objOficinaSesion))
            {
                $intIdCantonSesion  = $objOficinaSesion->getCantonId();
            }
        }
        
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoCaso entity.');
        }

        $notificacion = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($entity->getTipoNotificacionId());
        $deleteForm = $this->createDeleteForm($id);

        $flag1 = $em->getRepository('schemaBundle:InfoCaso')->tieneHipotesisSinSintomas($id); //Bandera para saber si tiene hipotesis agregadas sin sintomas
        $flag2 = $em->getRepository('schemaBundle:InfoCaso')->tieneHipotesis($id); //Bandera para saber si tiene hipotesis agregadas se puede ingresar tarea
        $flag3 = $em->getRepository('schemaBundle:InfoCaso')->tieneTareas($id); //Bandera para saber si tiene tareas agregada
        $ultimo_estado = $em->getRepository('schemaBundle:InfoCaso')->getUltimoEstado($id);

        $ultimo_asignado = $em->getRepository('schemaBundle:InfoCaso')->getUltimaAsignacion($id);
        $tareasTodas = $em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($id, 'Todas');
        $tareasAbiertas = $em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($id, 'Abiertas');
        $tareasFinalizadasSolucion = $em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($id, 'FinalizadasSolucion');

        $tareasCanceladas   = $em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($id, 'Canceladas');

        $flagBoolAsignado = ($ultimo_asignado && count($ultimo_asignado) > 0 ? ($ultimo_asignado->getAsignadoId() != '' ? true : false) : false);

        $flagAsignado = ($flagBoolAsignado ? ($session->get("idDepartamento") == $ultimo_asignado->getAsignadoId() ? true : false) : false);

        $usuarioApertura = "";
        $usuarioCierre = "";

        if($entity->getUsrCreacion())
        {
            $empleadoCreacion = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($entity->getUsrCreacion());
            if($empleadoCreacion && count($empleadoCreacion) > 0)
            {
                $usuarioApertura = (($empleadoCreacion->getNombres() && $empleadoCreacion->getApellidos()) ? $empleadoCreacion->getNombres() . " " . $empleadoCreacion->getApellidos() : "");
            }
            else
            {
                $usuarioApertura = $entity->getUsrCreacion();
            }
        }

        if($ultimo_estado == "Cerrado")
        {
            $entidadUltimoEstado = $em->getRepository('schemaBundle:InfoCaso')->getUltimoEstado($id, 'entidad');

            if($entidadUltimoEstado->getUsrCreacion())
            {

                $empleadoCierre = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->getPersonaPorLogin($entidadUltimoEstado->getUsrCreacion());
                if($empleadoCierre && count($empleadoCierre) > 0)
                {
                    $usuarioCierre = (($empleadoCierre->getNombres() && $empleadoCierre->getApellidos()) ? 
                                      $empleadoCierre->getNombres() . " " . $empleadoCierre->getApellidos() : "");
                }
            }
        }

        $titulo_fin = "N/A";
        if($entity->getTituloFinHip())
        {
            $Hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($entity->getTituloFinHip());
            $titulo_fin = ($Hipotesis ? ($Hipotesis->getNombreHipotesis() ? $Hipotesis->getNombreHipotesis() : "N/A") : "N/A");
        }
        else
        {
            $titulo_fin = ($entity->getTituloFin() ? $entity->getTituloFin() : "N/A");
        }

        $flagCreador = ($session->get("user") == $entity->getUsrCreacion() ? true : false);

        $flagTareasTodas = ($tareasTodas > 0 ? true : false);
        $flagTareasAbiertas = ($tareasAbiertas > 0 ? false : true);
        $flagCerrarCasoTN=false;
        $flagTareasTodasCanceladas=false;

        //Para la empresa TN se valida si el departamento del usuario en session es igual al del creador del caso
        if($prefijoEmpresa == "TN")
        {
            $flagCerrarCasoTN = true;
            if($entity)
            {
                $objInfoPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array("login" => $entity->getUsrCreacion(),"estado" => "Activo"));

                if($objInfoPersona)
                {
                    $intDepartamentoCreadorCaso = $emComercial->getRepository('schemaBundle:InfoCaso')
                                                              ->getDepartamentoPorLoginYEmpresa($entity->getUsrCreacion(),$entity->getEmpresaCod());

                    if($intDepartamentoCreadorCaso>0)
                    {
                        if($intDepartamentoCreadorCaso == $session->get("idDepartamento"))
                        {
                            $mostrarHipotesis = "S" ;
                        }
                    }
                    else
                    {
                        //En caso de que no exista departamento se permite mostrar la herramienta de hipotesis para que se
                        //pueda tener gestión sobre el caso, dado que es un escenario donde el creador ya se encuentra inactivo
                        $mostrarHipotesis = "S" ;
                    }
                }
            }
        }
        
        $flagTareasSolucionadas = ($tareasFinalizadasSolucion > 0 ? false : true);
        
        if(count($em->getRepository('schemaBundle:InfoCaso')->getTareasSinSolucion($entity->getId())) > 0)
        {
            $flagTareasSolucionadas = true;
            $flagCerrarCasoTN       = false;
        }
            
        $nombreOficina = "";
        $nombreEmpresa = "";
        $nombresAsignadoPor = "";
        $feAsignacion = '';

        if($ultimo_asignado)
        {
            if($ultimo_asignado->getPersonaEmpresaRolId())
            {
                $InfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findOneById($ultimo_asignado->getPersonaEmpresaRolId());

                if($InfoPersonaEmpresaRol && count($InfoPersonaEmpresaRol) > 0)
                {
                    $oficinaEntity = $InfoPersonaEmpresaRol->getOficinaId();
                    $empresaEntity = $oficinaEntity->getEmpresaId();

                    $nombreOficina = ($oficinaEntity ? ($oficinaEntity->getNombreOficina() ? $oficinaEntity->getNombreOficina() : "") : "");
                    $nombreEmpresa = ($empresaEntity ? ($empresaEntity->getNombreEmpresa() ? $empresaEntity->getNombreEmpresa() : "") : "");
                }
            }

            $feAsignacion = $ultimo_asignado->getFeCreacion() ? date_format($ultimo_asignado->getFeCreacion(), "d-m-Y G:i") : "";
            $usrAsignadoPor = $ultimo_asignado->getUsrCreacion() ? $ultimo_asignado->getUsrCreacion() : "";
            if($usrAsignadoPor)
            {
                $empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($usrAsignadoPor);
                if($empleado && count($empleado) > 0)
                {
                    $nombresAsignadoPor = (($empleado->getNombres() && $empleado->getApellidos()) ? 
                                            $empleado->getNombres() . " " . $empleado->getApellidos() : "");
                }
            }
        }

        $tienetareas = $em->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($entity->getId(), 'Todas');

        $fechaFinal = "";
        $horaFinal = "";
        $tiempoTotal = '';

        $infoCasoTiempoAsignacion = $em->getRepository('schemaBundle:InfoCaso')->getTiempoCaso($entity->getId());

        if($infoCasoTiempoAsignacion)
        {

            $tiempoTotal = $infoCasoTiempoAsignacion[0]['tiempoTotalCasoSolucion'] . ' minutos';
        }

        if($tienetareas > 0)
        { //Si el caso tiene Tareas
            if(count($em->getRepository('schemaBundle:InfoCaso')->getTareasSinSolucion($entity->getId())) == 0)
            { // tiene tareas cerradas
                $fechaFinalizacion = $em->getRepository('schemaBundle:InfoCaso')->getFechaTareaSolucion($entity->getId());

                if($fechaFinalizacion && $fechaFinalizacion[0]['fecha'] != "")
                {                   
                    $fechaFinA = explode(" ", $fechaFinalizacion[0]['fecha']);

                    $fechaFin = $fechaFinA[0];
                    $horaFin  = $fechaFinA[1];

                    $fechaS = explode("-", $fechaFin);
                    $horaS  = explode(":", $horaFin);

                    $fechaFinal = $fechaS[2] . '-' . $fechaS[1] . '-' . $fechaS[0];
                    $horaFinal  = $horaS[0]  . ':' . $horaS[1];
                }

                if($tiempoTotal == '')
                {
                    $tiempoTotal = 'Finalizada';
                }
            }
            else
            {
                $tiempoTotal = 'Sin Finalizar';
            }
        }
        else if($tiempoTotal == '')
        {
            $tiempoTotal = 'Sin Finalizar';
        }

        

        if($flagBoolAsignado && $flagAsignado)
        {
            $esDepartamento = true;
        }
        else
        {
            $esDepartamento = false;
        }

        if(($tareasCanceladas == $tareasTodas) && $tareasTodas != 0)
        {
            $flagTareasAbiertas = false;
            $flagTareasSolucionadas = true;
            $flagCerrarCasoTN       = false;
            $flag1 = false;
            $flagTareasTodas = false;
            $flagTareasTodasCanceladas=true;

            $fechaUltima = $em->getRepository('schemaBundle:InfoCaso')->getFechaUltimaTareaFinalizada($id, 'Cancelada');           

            $fechaFinA = explode(" ", $fechaUltima[0]['fecha']);

            $fechaFin = $fechaFinA[0];
            $horaFin = $fechaFinA[1];

            $fechaS = explode("-", $fechaFin);
            $horaS  = explode(":", $horaFin);

            $fechaFinal = $fechaS[2] . '-' . $fechaS[1] . '-' . $fechaS[0];
            $horaFinal  = $horaS[0] . ':' . $horaS[1];
        }
        
        /************** Se obtiene la Ultima Milla del elemento/Cliente afectado en el caso **************/
        
        $arrayParamsUMAfectada['idCaso']   = $entity->getId();
        $arrayParamsUMAfectada['prefijo']  = $prefijoEmpresa;
        $arrayParamsUMAfectada['tipoCaso'] = $entity->getTipoCasoId()->getNombreTipoCaso();
        $arrayParamsUMAfectada['em']       = $emInfraestructura;
        
        $strUltimaMilla = $this->getDoctrine()->getManager("telconet_soporte")->getRepository("schemaBundle:InfoCaso")
                                              ->obtenerInfoElementoAfectadoPorCaso($arrayParamsUMAfectada);
        
        /***************************************************************************************************/
        
        $detalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')->findByCasoId($id);

        $hipotesisIniciales = '';

        if($detalleHipotesis)
        {

            foreach($detalleHipotesis as $detalle)
            {

                if($detalle->getHipotesisId() != null)
                {
                    $hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')
                                    ->find($detalle->getHipotesisId()->getId());
                    $hipotesisIniciales .= $hipotesis->getNombreHipotesis() . ', ';
                }                
            }
        }
        
        //Se coloca fecha Actual para que se gestione al momento de asignar tarea
        $fechaActual = new \DateTime('now');
        $date = $fechaActual->format('Y-m-d H:i');     
        
        $casoEsMigracion = false;

        //Se obtiene informacion si el caso tiene relacion con un cliente migrado
        $objJsonAfectados = $em->getRepository('schemaBundle:InfoCaso')
                ->getAfectadosCaso( array('intIdCaso' => $entity->getId()));

        if($objJsonAfectados && $objJsonAfectados!='')
        {
            $objArrayData = json_decode($objJsonAfectados)->encontrados[0];
            if($objArrayData->tipo_afectado=='Cliente')
            {
                $puntoId = $objArrayData->id_afectado;
                
                $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->getIdsServicioPorIdPunto($puntoId);
                
                if($servicio)
                {
                    $objTipoSolicitudMigra = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD MIGRACION'));
                    
                    if($objTipoSolicitudMigra)
                    {

                        $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->findOneBy(array('servicioId'     =>$servicio,
                                                                             'tipoSolicitudId'=>$objTipoSolicitudMigra->getId()));                                                                         

                        if($objDetalleSolicitud && $objDetalleSolicitud->getEstado() != 'Finalizada')
                        {                        
                            $casoEsMigracion = true;
                        }
                    
                    }
                }                            
            }            
        }

        if($entity->getOrigen()=='E')
        {
            $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId);
            $arrayCuentaExtranet = $objSoporteService->getConsultaCuentaExtranet(array('usuario' => $entity->getUsrCreacion(),
                                                                                       'contexto' => $objPunto->getPersonaEmpresaRolId()->getId()));
            $usuarioApertura = $arrayCuentaExtranet['nombres'].' '.$arrayCuentaExtranet['apellidos'] .' ('.$entity->getUsrCreacion().')';
        }


        //Se Verifica si se debe presentar la accion de Cerrar Caso
        $IdPersonaEmpresaRol = $session->get('idPersonaEmpresaRol');
        $flag4               = $em->getRepository('schemaBundle:InfoCaso')->getPresentarVentanaCerrarCaso($id,$emComercial,$IdPersonaEmpresaRol);

        //Se valida si el tipo de caso esta permitido para actualizar la fecha de asignacion
        $existeTipoCaso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAMETRO TIPOS DE CASOS',
                                             'SOPORTE',
                                             'CASOS',
                                             'MOVILIZACION',
                                             $entity->getTipoCasoId()->getId(),
                                             '',
                                             '',
                                             '',
                                             '',
                                             '');

        if($existeTipoCaso)
        {
            $band = "S";
        }
        
        $arrayParametros["intIdCaso"]   = $entity->getId();
        $arrayParametros["strTipoRepo"] = "COUNT";
        //Se realiza el calculo del tiempo de casos, en base a los nuevos botones de iniciar,pausar y reanudar tareas
        $intNumeroTareas = $em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);

        if($intNumeroTareas > 0)
        {
            $arrayParametros["intIdCaso"]   = $entity->getId();
            $arrayParametros["strTipoRepo"] = "SUM";

            $intTiempoTotalCaso    = $em->getRepository('schemaBundle:InfoDetalle')->getTiempoTotalTareasDeCasos($arrayParametros);
            $strEsCasoNuevoEsquema = "S";
        }

        //Se obtiene la empresa que crea el caso
        $objInfoEmpresaGrupo = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($entity->getEmpresaCod());

        if(is_object($objInfoEmpresaGrupo))
        {
            $strEmpresaCreacion = $objInfoEmpresaGrupo->getNombreEmpresa();
        }

        //Obtenemos los tiempo totales del caso una vez cerrado.
        if ($ultimo_estado === 'Cerrado')
        {
            $intTiempoCliente     = $infoCasoTiempoAsignacion[0]['tiempoClienteAsignado'] . ' minutos';
            $intTiempoEmpresa     = $infoCasoTiempoAsignacion[0]['tiempoEmpresaAsignado'] . ' minutos';
            $intTiempoIncidencia  = $infoCasoTiempoAsignacion[0]['tiempoTotalCaso']       . ' minutos';
            $intTiempoTotalCierre = $infoCasoTiempoAsignacion[0]['tiempoTotal']           . ' minutos';
        }

        if ($ultimo_estado !== "Cerrado" && $prefijoEmpresa === 'TN'
                && $entity->getTipoCasoId()->getNombreTipoCaso() === 'Backbone')
        {
            
            $arrayRespuesta = $em->getRepository("schemaBundle:InfoCaso")
                    ->getObtenerCasosClientes(array ('intIdCaso' => $entity->getId(),
                                                    'objContainer' => $this->container));

            if ($arrayRespuesta['status'] !== 'fail')
            {
                $arrayCasos = (array) json_decode($arrayRespuesta['result']);

                if ($arrayCasos['status'] !== 'fail')
                {
                    $intCantidadCasosAp = count($arrayCasos['result']);
                }
            }
        }

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strEmpresaCod);
        if($arrayAdmiParametroDet['valor2']==='S')
        {
            $strBuscarPorArbolHipotesis = 'S';
        }


        // Listado de Tareas Solucion
        $arrayTareasSoluciones = [];
        if ($prefijoEmpresa == 'TN')
        {
            $arrayTareasSoluciones = $em->getRepository('schemaBundle:InfoCaso')->getTareasSolucionPorCaso([
                                                            'idCaso' => $id
                                                        ]);
        }

        $objParamCierreCaso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAMETROS_CIERRE_CASO',
                                                     'SOPORTE',
                                                     'CIERRE_CASO',
                                                     'FECHA_REFERENCIA_CIERRE_CASOS',
                                                     '', '', '', '', '', 
                                                     $strEmpresaCod);


        $strFechaDetTareas = '';
        if($objParamCierreCaso)
        {
            $strFechaDetTareas = $objParamCierreCaso['valor1'];
        }

        $strMantProgramado = "N";
        $strFechaInicioMT = "";
        $strFechaFinMT ="";
        $strTiempoAfectacionMT="";
        $strTipoAfectacion="";
        $strTipoNotificacion="";

        if (($prefijoEmpresa === 'MD'||$prefijoEmpresa === 'EN')&& $entity->getTipoCasoId()->getNombreTipoCaso() === 'Backbone')
        {
            $objInfoMantProgramado = $em->getRepository('schemaBundle:InfoMantenimientoProgramado')->findBy(array('casoId'=>$id));            
            
            if(!empty($objInfoMantProgramado) && count($objInfoMantProgramado)>0)
            {
                $strMantProgramado     = "S";
                $strFechaInicioMT      = $objInfoMantProgramado[0]->getFechaInicio();
                $strFechaFinMT         = $objInfoMantProgramado[0]->getFechaFin();
                $strTiempoAfectacionMT = $objInfoMantProgramado[0]->getTiempoAfectacion();
                $strTipoAfectacion     = $objInfoMantProgramado[0]->getTipoAfectacion();
                $strTipoNotificacion   = $objInfoMantProgramado[0]->getTipoNotificacion();
            }
        }


        $parametros = array(
            'cantidadCasosAp'           => $intCantidadCasosAp,
            'tiempoCliente'             => $intTiempoCliente,
            'tiempoEmpresa'             => $intTiempoEmpresa,
            'tiempoIncidencia'          => $intTiempoIncidencia,
            'tiempoTotalCierre'         => $intTiempoTotalCierre,
            'entity'                    => $entity,
            'tituloFin'                 => $titulo_fin,
            'notificacion'              => $notificacion,
            'delete_form'               => $deleteForm->createView(),
            'flag1'                     => $flag1,
            'flag2'                     => $flag2,
            'flag3'                     => $flag3,
            'flag4'                     => $flag4,
            'nuevo_esquema'             => $strEsCasoNuevoEsquema,
            'tiempo_total_caso'         => $intTiempoTotalCaso,
            'mostrarHipotesis'          => $mostrarHipotesis,
            'flagCreador'               => $flagCreador,
            'flagBoolAsignado'          => $flagBoolAsignado,
            'flagAsignado'              => $flagAsignado,
            'flagTareasTodas'           => $flagTareasTodas,
            'flagTareasAbiertas'        => $flagTareasAbiertas,
            'flagTareasSolucionadas'    => $flagTareasSolucionadas,
            'flagCerrarCasoTN'          => $flagCerrarCasoTN,
            'flagTareasTodasCanceladas' => $flagTareasTodasCanceladas,
            'ultimo_estado'             => $ultimo_estado,
            'tiempo_total'              => $tiempoTotal,
            'empresa'                   => $prefijoEmpresa,
            'fechaFin'                  => $fechaFinal,
            'horaFin'                   => $horaFinal,
            'esDepartamento'            => $esDepartamento,
            'elementoAfectado'          => $strUltimaMilla,
            'hipotesisIniciales'        => $hipotesisIniciales,
            'usuarioApertura'           => ($usuarioApertura ? ucwords(strtolower($usuarioApertura)) : "N/A"),
            'usuarioCierre'             => ($usuarioCierre ? ucwords(strtolower($usuarioCierre)) : "N/A"),
            'departamento_asignado'     => ($ultimo_asignado ? ($ultimo_asignado->getAsignadoNombre() ? 
                                           ucwords(strtolower($ultimo_asignado->getAsignadoNombre())) : "N/A") : "N/A"),
            'empleado_asignado'         => ($ultimo_asignado ? ($ultimo_asignado->getRefAsignadoNombre() ? 
                                           ucwords(strtolower($ultimo_asignado->getRefAsignadoNombre())) : "N/A") : "N/A"),
            'oficina_asignada'          => ($nombreOficina ? ucwords(strtolower($nombreOficina)) : "N/A"),
            'empresa_asignada'          => ($nombreEmpresa ? $nombreEmpresa : "N/A"),
            'empresa_creadora'          => ($strEmpresaCreacion ? $strEmpresaCreacion : "N/A"),
            'asignado_por'              => ($nombresAsignadoPor ? ucwords(strtolower($nombresAsignadoPor)) : "N/A"),
            'fecha_asignacionCaso'      => ($feAsignacion ? $feAsignacion : "N/A"),            
            'date'                      => $date,
            'casoMigracion'             => $casoEsMigracion,
            'band'                      => $band,
            'tipoCaso'                  => $entity->getTipoCasoId()->getNombreTipoCaso(),
            'intIdCantonSesion'         => $intIdCantonSesion,
            'intIdDepartamentoSesion'   => $intIdDepartamentoSesion,
            'arrayPermisos'             => $arrayPermisos,
            'buscaPorArbolHipotesis'    => $strBuscarPorArbolHipotesis,
            'solucionesTareas'          => $arrayTareasSoluciones,
            'fechaDetTareas'            => $strFechaDetTareas,
            'mantenimientoProgramado'   => $strMantProgramado,
            'fechaInicioMT'             => $strFechaInicioMT,
            'fechaFinMT'                => $strFechaFinMT,
            'tiempoAfectacionMT'        => $strTiempoAfectacionMT,
            'tipoAfectacion'            => $strTipoAfectacion,
            'tipoNotificacion'          => $strTipoNotificacion
        );

        return $this->render('soporteBundle:InfoCaso:show.html.twig', $parametros);
    }

     /**
     * newAction
     *
     * Esta funcion muestra el formulario que se utiliza para crear un Nuevo Caso
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 06-11-2018 - Se envia el parámetro de asignaciones pendientes del usuario en sesión
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 07-12-2015 Se realizan ajustes para que se pueda vizualizar los tipos de casos segun el departamento
     *                         del usuario que esta conectado.
     *
     * @version 1.0
     *
     * @Secure(roles="ROLE_78-2")
     *
     */
     public function newAction()
     {
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $idSolicitud    = $peticion->query->get('idSolicitud');
        $departamento   = $session->get('departamento');
        $strUsrSesion   = $session->get('user');
        $intIdEmpresa   = $session->get('idEmpresa');
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $em_soporte     = $this->getDoctrine()->getManager("telconet_soporte");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $arraytipoCaso = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getTipoCasosXDepartamento($departamento);

        $fechaActual  = new \DateTime('now');
        $date         = $fechaActual->format('Y-m-d');
        $cliente      = $session->get('ptoCliente');       
        $codEmpresa   = $session->get('prefijoEmpresa');

        if($cliente)
        {
            $cl = $cliente['id'];
        }
        else
        {
            $cl = "no cliente";
        }

        $entity = new InfoCaso();
        $form   = $this->createForm(new InfoCasoType(array('arraytipoCaso' => $arraytipoCaso)), $entity);

        $arrayAsignaciones = $em_soporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->findBy(array(
                                                                                                             "usrAsignado"  => $strUsrSesion,
                                                                                                             "tipoAtencion" => "CASO",
                                                                                                             "estado"       => "Pendiente",
                                                                                                             "empresaCod"   => $intIdEmpresa
                                                                                                            ), 
                                                                                                        array('feCreacion' => 'ASC')
                                                                                                       );

        return $this->render('soporteBundle:InfoCaso:new.html.twig', array('item'              => $entityItemMenu,
                                                                           'entity'            => $entity,
                                                                           'fechaActual'       => $date,
                                                                           'cliente'           => $cl,
                                                                            //Se envia mensaje null por default (esto vendrá lleno cuando se gestione
                                                                            //                                   algun error en la creacion)
                                                                           'mensaje'           => null,
                                                                           'idSolicitud'       => $idSolicitud,
                                                                           'form'              => $form->createView(),
                                                                           'empresa'           => $codEmpresa,
                                                                           'arrayAsignaciones' => $arrayAsignaciones,
                                                                           //Se envia en blanco para la primera vez que se requiere crear el caso
                                                                           'jsonSintoma' => ''));
     }

    /**
    * createAction
    *
    * Esta funcion crea el nuevo caso que se esta ingresando
    *
    * @author Miguel Angulo Sanchéz <jmangulos@telconet.ec>
    * @version 1.4 17-07-2019 - Se agrega validación para que no permita crear casos tipo técnico sin tener sesión de cliente activa.
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.3 06-11-2018 - Se agrega que se grabe el id del caso en la asignación si el id de asignación no esta vacio
    *
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.2 18-05-2016 - Se envia mensaje de error completo e informacion persistente cuando la generacion del caso falle para reintento
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para que se pueda vizualizar los tipos de casos segun el departamento
    *                         del usuario que esta conectado.
    * @version 1.0
    *
    * @Secure(roles="ROLE_78-3")
    */ 
    public function createAction()
    {
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");
        $idSolicitud    = $_GET["idSolicitud"];
        $entity         = new InfoCaso();
        $request        = $this->getRequest();
        $peticion       = $this->get('request');
        $em             = $this->getDoctrine()->getManager("telconet");
        $em_soporte     = $this->getDoctrine()->getManager("telconet_soporte");

        $session       = $peticion->getSession();
        $departamento  = $session->get('departamento');

        $arraytipoCaso = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getTipoCasosXDepartamento($departamento);

        $codEmpresa    = $session->get('idEmpresa');

        $form = $this->createForm(new InfoCasoType(array('arraytipoCaso' => $arraytipoCaso)), $entity);
        $form->handleRequest($request);

        if($form->getNormData()->getTipoCasoId()->getNombreTipoCaso() == 'Tecnico' ||
           $form->getNormData()->getTipoCasoId()->getNombreTipoCaso() == 'Arcotel')
        {
            $objPtoCliente      = $session->get('ptoCliente');
            
            
            if( empty($objPtoCliente) )
            {
                $strMensaje = "no cliente";

                $arrayParametros = array(
                                        'item'        => $entityItemMenu,
                                        'entity'      => $entity,
                                        'fechaActual' => $date,
                                        'cliente'     => $strMensaje,
                                        'mensaje'     => $mensaje,
                                        'form'        => $form->createView(),
                                        'idSolicitud' => 0,
                                        'empresa'     => $session->get('prefijoEmpresa'),
                                        //Se envia la informacion generada por primera vez para mantener la persistencia de la informacion
                                        'jsonSintoma' => $peticion->get('sintomas_escogidos'),
                                        'errorSesion' => 'Error'
                                        );

                // Se retorna a la pagina de creacion de Caso cuando
                return $this->render('soporteBundle:InfoCaso:new.html.twig', $arrayParametros);
            }
        }
        
        /* @var $soporteService SoporteService */
        $soporteService = $this->get('soporte.SoporteService');
        $retorno        = $soporteService->crearCaso($peticion, $entity, $form, $codEmpresa);

        //Se verifica Tipo de error de retorno en caso de existir manejado por el Service        
        if(stripos($retorno, 'ERROR')!==FALSE)
        {
            $fechaActual = new \DateTime('now');
            $date        = $fechaActual->format('Y-m-d');
            
            switch($retorno)
            {
                case "ORA-ERROR":
                    $mensaje = "ORA-ERROR"; //Se produce cuando se viola el constraint por numero de caso
                    break;               
                case "SINTOMA-ERROR":
                    $mensaje = "Error al guardar Sintoma, por favor intente nuevamente";                    
                    break;
                case "AFECTADO-ERROR":                
                    $mensaje = "Error al guardar Afectado, por favor intente nuevamente"; 
                    break;
                default : //Cuando se produce algun error particular a nivel transaccional                    
                    $mensaje = $retorno;
            }
            
            $cliente      = $session->get('ptoCliente');                   

            if($cliente)
            {
                $cl = $cliente['id'];
            }
            else
            {
                $cl = "no cliente";
            }

            $parametros = array(
                'item'        => $entityItemMenu,
                'entity'      => $entity,
                'fechaActual' => $date,
                'cliente'     => $cl,
                'mensaje'     => $mensaje,
                'form'        => $form->createView(),
                'idSolicitud' => 0,
                'empresa'     => $session->get('prefijoEmpresa'),
                //Se envia la informacion generada por primera vez para mantener la persistencia de la informacion
                //para efectos de iteraciones cuando se generan casos simultaneos y corregir  numeraciones repetidas
                'jsonSintoma' => $peticion->get('sintomas_escogidos')
            );

            //Se retorna a la pagina de creacion de Caso cuando se lance la excepcion de error de ORACLE
            //Esto generará un submit por debajo para enviar la petición nuevamente
            return $this->render('soporteBundle:InfoCaso:new.html.twig', $parametros);
        }
        else
        {
            //creo la relacion con la solicitud de migración
            if($idSolicitud)
            {
                $objCaract = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                  ->findOneBy(array('descripcionCaracteristica'=>'CASO', 'estado'=>'Activo'));

                $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                  ->findOneById($idSolicitud);

                $entityDetalle = new InfoDetalleSolCaract();
                $entityDetalle->setCaracteristicaId($objCaract);
                $entityDetalle->setValor($entity->getId());
                $entityDetalle->setDetalleSolicitudId($objSolicitud);
                $entityDetalle->setEstado('Activo');
                $entityDetalle->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalle->setFeCreacion(new \DateTime('now'));

                $em->persist($entityDetalle);
                $em->flush(); 

            }

            //Graba el número de caso en la asignación
            if ($peticion->get('asignacionSolicitud'))
            {
                $objInfoCaso = $em_soporte->getRepository('schemaBundle:InfoCaso')->findOneById($retorno);
                if(!empty($objInfoCaso))
                {
                    $arrayParametrosAsig['intIdAsignacion'] = $peticion->get('asignacionSolicitud');
                    $arrayParametrosAsig['strNumeroTarea']  = $objInfoCaso->getNumeroCaso();
                    $arrayParametrosAsig['strTipoAtencion'] = 'CASO';
                    $arrayParametrosAsig['strTipoProblema'] = '';
                    $arrayParametrosAsig['strUsuario']      = $peticion->getSession()->get('user');
                    $arrayParametrosAsig['strIpCreacion']   = $peticion->getClientIp();
                    $soporteService->agregarNumeroEnAsignacionSolicitud($arrayParametrosAsig);
                }
            }
            //Si no existe error se creara el caso sin ningun problema
            return $this->redirect($this->generateUrl('infocaso_show', array('id' => $retorno)));
        }
    }

    /**
    * updateDateAction
    *
    * Funcion que permite actualizar la fecha de asignacion de una caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 05-04-2016
    *
    * @Secure(roles="ROLE_78-3")
    */
    public function updateDateAction()
    {
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");

        $peticion = $this->get('request');
        $idCaso   = $peticion->get("idCaso");
        $fecha    = $peticion->get("fecha_asignacion");
        $hora     = $peticion->get("hora_asignacion");

        $date     = date_create(date('Y-m-d H:i', strtotime($fecha . ' ' . $hora)));

        //Se consulta el ultimo estado de asignacion del caso
        $ultimo_asignado    = $em_soporte->getRepository('schemaBundle:InfoCaso')->getUltimaAsignacion($idCaso);

        $infoCasoAsignacion = $em_soporte->getRepository('schemaBundle:InfoCasoAsignacion')->find($ultimo_asignado->getId());

        $infoCasoAsignacion->setFeCreacion($date);
        $infoCasoAsignacion->setUsrCreacion($peticion->getSession()->get('user'));
        $infoCasoAsignacion->setIpCreacion($peticion->getClientIp());

        $em_soporte->persist($infoCasoAsignacion);
        $em_soporte->flush();

        return $this->redirect($this->generateUrl('infocaso_show', array('id' => $idCaso)));
    }

    /**
    * editAction
    *
    * Esta funcion muestra el formulario para editar un caso existente
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para que se pueda vizualizar los tipos de casos segun el departamento
    *                         del usuario que esta conectado.
    * @version 1.0
    *
    * @param integer  $id Id del caso a editar
    *
    */
    public function editAction($id)
    {
        $session        = $this->get('request')->getSession();
        $departamento   = $session->get('departamento');
        $em             = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $em_soporte     = $this->getDoctrine()->getManager("telconet_soporte");
        $arraytipoCaso  = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getTipoCasosXDepartamento($departamento);
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $entity = $em->getRepository('schemaBundle:InfoCaso')->find($id);
		
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoCaso entity.');
        }
        
        $editForm   = $this->createForm(new InfoCasoType(array('arraytipoCaso' => $arraytipoCaso)), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros = array('item'        => $entityItemMenu,
                            'entity'      => $entity,
                            'edit_form'   => $editForm->createView(),
                            'delete_form' => $deleteForm->createView());

        return $this->render('soporteBundle:InfoCaso:edit.html.twig',$parametros);
    }

    /**
    * updateAction
    *
    * Esta funcion edita el caso actual que es enviado por parametro
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015 Se realizan ajustes para que se pueda vizualizar los tipos de casos segun el departamento
    *                         del usuario que esta conectado.
    * @version 1.0
    *
    * @param integer  $id Id del caso a editar
    *
    */
    public function updateAction($id)
    {
        $session       = $this->get('request')->getSession();
        $departamento  = $session->get('departamento');

        $em             = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $em_soporte     = $this->getDoctrine()->getManager("telconet_soporte");
        $arraytipoCaso  = $em_soporte->getRepository('schemaBundle:AdmiTipoCaso')->getTipoCasosXDepartamento($departamento);
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");
        $entity         = $em->getRepository('schemaBundle:InfoCaso')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoCaso entity.');
        }

        $editForm   = $this->createForm(new InfoCasoType(array('arraytipoCaso' => $arraytipoCaso)), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->handleRequest($request);

        if ($editForm->isValid())
        {
                $entity->setEstado("Modificado");
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($peticion->getSession()->get('user'));
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('infocaso_edit', array('id' => $id)));
        }
	
		$parametros = array('item'        => $entityItemMenu,
                                   'entity'      => $entity,
                                   'edit_form'   => $editForm->createView(),
                                   'delete_form' => $deleteForm->createView(),
                                   //'img_opcion_menu'=>$img_opcion
        );
        
        if ($error)
        {
            $parametros['error'] = $error;
        }
		
        return $this->render('soporteBundle:InfoCaso:edit.html.twig', $parametros );
    }

    /**
     * Deletes a InfoCaso entity.
     *
     */
    /**
    * @Secure(roles="ROLE_78-8")
    */ 
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:InfoCaso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoCaso entity.');
            }
			
            
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($peticion->getSession()->get('user'));
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infocaso'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /*
    * Llena el grid de consulta.
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 22-05-2018 Se agrega el informe del caso y se debe enviar la conexion de comunicacion
    * 
    * @Secure(roles="ROLE_78-7")
    */ 
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $session = $peticion->getSession();

        $em                = $this->getDoctrine()->getManager("telconet");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emComunicacion    = $this->getDoctrine()->getManager("telconet_comunicacion");
                
        $parametros = array();
        $parametros['numero']         = $peticion->query->get('numero');
        $parametros['estado']         = $peticion->query->get('estado');
        $parametros['empresa']        = $peticion->query->get('empresa');
        $parametros['tituloInicial']  = $peticion->query->get('tituloInicial');
        $parametros['versionInicial'] = $peticion->query->get('versionInicial');
        $parametros['tituloFinal']    = $peticion->query->get('tituloFinal');
        $parametros['tituloFinalHip'] = $peticion->query->get('tituloFinalHip');
        $parametros['versionFinal']   = $peticion->query->get('versionFinal');
        $parametros['nivelCriticidad']= $peticion->query->get('nivelCriticidad');
        $parametros['tipoCaso']       = $peticion->query->get('tipoCaso');
        $parametros['usrApertura']    = $peticion->query->get('usrApertura');
        $parametros['usrCierre']      = $peticion->query->get('usrCierre');
        $parametros['boolSearch']     = $peticion->query->get('boolSearch');
        $parametros['page']           = $peticion->query->get('page');
        

        $varSessionCliente     = ($session->get('cliente') ? $session->get('cliente') : "");
        $varSessionPtoCliente  = ($session->get('ptoCliente') ? $session->get('ptoCliente') : "");
        $nombreClienteAfectado = ($varSessionCliente ? ($varSessionCliente['razon_social'] ? 
                                  $varSessionCliente['razon_social'] : 
                                  $varSessionCliente['nombres'] . " " . $varSessionCliente['apellidos']) : "");
        $loginPuntoCliente     = ($varSessionPtoCliente ? 
                                 ($varSessionPtoCliente['login'] ? $varSessionPtoCliente['login'] : "") : "");
                                         
        $parametros['clienteAfectado'] = ($nombreClienteAfectado ? 
                                      $nombreClienteAfectado : $peticion->query->get('clienteAfectado'));
        $parametros['loginAfectado']   = ($loginPuntoCliente ? 
                                          $loginPuntoCliente : $peticion->query->get('loginAfectado'));


        $feAperturaDesde = explode('T', $peticion->query->get('feAperturaDesde'));
        $feAperturaHasta = explode('T', $peticion->query->get('feAperturaHasta'));
        $feCierreDesde   = explode('T', $peticion->query->get('feCierreDesde'));
        $feCierreHasta   = explode('T', $peticion->query->get('feCierreHasta'));

        $parametros['feAperturaDesde'] = $feAperturaDesde ? $feAperturaDesde[0] : 0;
        $parametros['feAperturaHasta'] = $feAperturaHasta ? $feAperturaHasta[0] : 0;
        $parametros['feCierreDesde']   = $feCierreDesde ? $feCierreDesde[0] : 0;
        $parametros['feCierreHasta']   = $feCierreHasta ? $feCierreHasta[0] : 0;

        $parametros['departamento_id'] = $peticion->query->get('ca_departamento');
        $parametros['empleado_id']     = $peticion->query->get('ca_empleado');
        $parametros['canton_id']       = $peticion->query->get('ca_ciudad');
        $parametros["strOrigen"]       = $peticion->query->get('strOrigen') ? $peticion->query->get('strOrigen') : "";
        $parametros["strTipoConsulta"] = $peticion->query->get('strTipoConsulta') ? $peticion->query->get('strTipoConsulta') : "";

        $boolExistenParams = $this->existenParametros($parametros);

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        if($parametros['empresa'] && $parametros['empresa'] != "")
        {
            $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($parametros['empresa']);
            $parametros['idEmpresaSeleccion'] = $empresa->getId();
        }
        else
        {
            $parametros['idEmpresaSeleccion'] = $session->get('idEmpresa');
        }

        $ids = null;

        $idsDeps = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getDepartamentosRolXLoginEmpleado($session->get('user'));

        foreach($idsDeps as $id)
        {
            $ids[] = $id['departamentoId'];
        }                
                
        //Si un cliente viene en sesion o existe al menos un parametros
        //se realiza la busqueda, caso contrario se muestra el grid limpio
        if( ($nombreClienteAfectado && $loginPuntoCliente ) || $boolExistenParams )
        {            
            $objJson = $this->getDoctrine()
                            ->getManager("telconet_soporte")
                            ->getRepository('schemaBundle:InfoCaso')
                            ->generarJsonCasos($parametros, $start, $limit, $session, $em, $ids, $emInfraestructura, $emGeneral, $emComunicacion);
        }
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }                

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * 
     * Matodo que permite agregar nuevos afectados a un CASO
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 25-05-2016 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxAgregarAfectadosAction()
    {
        ini_set('max_execution_time', 3000000);
        $strRespuesta = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        $strPeticion       = $this->get('request');        
        
        $strJsonAfectados = $strPeticion->get('jsonAfectados');
        $intIdCaso        = $strPeticion->get('idCaso');

        $arrayParametros['jsonAfectados'] = $strJsonAfectados;
        $arrayParametros['idCaso']        = $intIdCaso;
        $arrayParametros['esNuevo']       = false;
        $arrayParametros['request']       = $strPeticion;
        
        /* @var $soporteService SoporteService */
        $soporteService = $this->get('soporte.SoporteService');
        $objJson        = $soporteService->agregarAfectadosCaso($arrayParametros);
        
        $strRespuesta->setContent($objJson);
        return $strRespuesta;
    }
    
    /**
    * Documentación para la funcion getDocumentosCasoAction().
    *
    * Esta funcion es la encargada de llenar el grid de la consulta de Documentos cargados en la creacion del caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 15-01-2016
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
    * 
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 22-09-2016 Se agrega en la consulta de los documentos el parámetro del usuario en sesión para validar si 
    *                         éste puede o no eliminar un archivo adjunto
    * 
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.3 08-02-2017 Se modifica el envío de parámetros a la función que obtiene los archivos adjuntos a una tarea, agregando el
    *                         parámetros para reconocer si una tarea es o no una incidencia, auditoría o mantenimiento
    */
    public function getDocumentosCasoAction()
    {
        $strRespuesta = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        $strPeticion        = $this->get('request');
        $objSession         = $strPeticion->getSession();
        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strIdCaso          = $strPeticion->query->get('idCaso');
        $strIdTarea         = $strPeticion->query->get('idTarea');
        $strTareaIncAudMant = $strPeticion->get("strTareaIncAudMant") ? $strPeticion->get("strTareaIncAudMant") : "N";
        
        $arrayParametrosDoc                         = array();
        $arrayParametrosDoc["intIdCaso"]            = $strIdCaso;
        $arrayParametrosDoc["strTareaIncAudMant"]   = $strTareaIncAudMant;
        $arrayParametrosDoc["intIdDetalle"]         = $strIdTarea;
        $strPathTelcos                              = $this->container->getParameter('path_telcos');
        $arrayParametrosDoc["strPathTelcos"]        = $strPathTelcos."telcos/web";
        
        $objJson = $emComunicacion->getRepository('schemaBundle:InfoCaso')->getJsonDocumentosCaso(  $arrayParametrosDoc,
                                                                                                    $emInfraestructura,
                                                                                                    $objSession->get('user')
                                                                                                 );
        $strRespuesta->setContent($objJson);

        return $strRespuesta;
    }

    /**
    * Documentación para la funcion getDocumentosCasoEncontradosAction().
    *
    * Esta funcion es la encargada de retornar el numero de documentos que fueron agregados en el caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 18-01-2016
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
    * 
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 22-09-2016 Se agrega en la consulta de los documentos el parámetro del usuario en sesión para validar si 
    *                         éste puede o no eliminar un archivo adjunto
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.3 08-02-2017 Se modifica el envío de parámetros a la función que obtiene los archivos adjuntos a una tarea, agregando el
    *                         parámetros para reconocer si una tarea es o no una incidencia, auditoría o mantenimiento 
    */
    public function getDocumentosCasoEncontradosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strIdCaso          = $objPeticion->get("idCaso")?$objPeticion->get("idCaso"):"";
        $strIdTarea         = $objPeticion->get("idTarea")?$objPeticion->get("idTarea"):"";
        $strTareaIncAudMant = $objPeticion->get("strTareaIncAudMant") ? $objPeticion->get("strTareaIncAudMant") : "N";
        
        $arrayParametrosDoc                         = array();
        $arrayParametrosDoc["intIdCaso"]            = $strIdCaso;
        $arrayParametrosDoc["strTareaIncAudMant"]   = $strTareaIncAudMant;
        $arrayParametrosDoc["intIdDetalle"]         = $strIdTarea;
        
        $arrayDatos = $emComunicacion->getRepository('schemaBundle:InfoCaso')->getDocumentosCaso(   $arrayParametrosDoc,
                                                                                                    $emInfraestructura,
                                                                                                    $objSession->get('user')
                                                                                                );
        $objRespuesta->setContent(json_encode(array('total' => $arrayDatos['total'])));

        return $objRespuesta;
    }

    /**
     * existenParametros
     *
     * Metodo encargado de verificar si existen o no los filtros enviados como parametros
     *
     * @param array  $arrayParametros    
     * 
     * @return boolean 
     *
     * @author Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.0 04-08-2015
     */
    public function existenParametros($arrayParametros)
    {
        $contador = 0;
        
        foreach($arrayParametros as $obj)
        {
            if(trim($obj)!='' || $obj != null)
            {
                $contador ++;
            }            
        }
        
        if($contador>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 
     * Funcion que obtiene el elemento/tipoElemento afectado en un Caso al asignar una TAREA
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 22-05-2016
     * 
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getElementoAfectadoXDetalleAction()
    {    
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $peticion = $this->get('request');
        
        $emSoporte = $this->getDoctrine()->getManager("telconet_soporte");                

        $id_caso = $peticion->get('id_caso');     
        
        $objInfoCaso = $emSoporte->getRepository("schemaBundle:InfoCaso")->find($id_caso);
        
        $arrayParamsUMAfectada['idCaso']   = $id_caso;
        $arrayParamsUMAfectada['prefijo']  = $peticion->getSession()->get('prefijoEmpresa');
        $arrayParamsUMAfectada['tipoCaso'] = $objInfoCaso->getTipoCasoId()->getNombreTipoCaso();
        $arrayParamsUMAfectada['em']       = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $strUltimaMilla = $this->getDoctrine()->getManager("telconet_soporte")
                               ->getRepository("schemaBundle:InfoCaso")
                               ->obtenerInfoElementoAfectadoPorCaso($arrayParamsUMAfectada);
               
        $respuesta->setContent($strUltimaMilla);

        return $respuesta;
    }

    /**
    * @Secure(roles="ROLE_78-8")
    */ 
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoCaso', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                $entity->setEstado("Eliminado");
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($peticion->getSession()->get('user'));
                $em->persist($entity);
                
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /**
    * Documentación de la funcion 'getTiposElementosAction'.
    *
    * Método que Llena el grid de consulta de los tipos de elementos
    *
    * @return Response retorna el resultado de la operación
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 17-12-2015 Se realizan ajustes para presentar los elementos en el panel de Movilizacion
    * 
    * @version 1.0
    *
    * @Secure(roles="ROLE_78-53")
    *
    */
    public function getTiposElementosAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $session = $peticion->getSession();                
        
        $nombre = $peticion->query->get('query');
        $estado = $peticion->query->get('estado');
        $activoFijo = $peticion->query->get('activoFijo');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        //Se obtiene el id del caso en el escenario de que se gestione con uno y asi poder obtener la empresa 
        //del mismo y obtener las tareas relacionadas a esta
        //de lo contrario se obtiene
        //informacion de las taraes con la empresa en sesion
        $caso   = $peticion->query->get('caso')?$peticion->query->get('caso'):'';
        
        $codEmpresa = $session->get('idEmpresa');                

        //Se verifica que si se quiere obtener las tareas para relacionar un caso
        //Se valide con la empresa de la cual proviene el mismo y así mostrar sus
        //tareas segun la empresa en el que fue creado
        if($caso != '')
        {
	      $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
	      if($caso)
	      {
		    $codEmpresa = $caso->getEmpresaCod();	      
	      }	      
	}		

        $parametros['nombre']     = $nombre;
        $parametros['estado']     = 'Activo';
        $parametros['codEmpresa'] = $codEmpresa;
        $parametros['start']      = $start;
        $parametros['limit']      = $limit;
        $parametros['activoFijo'] = $activoFijo;
        
        $objJson = $this->getDoctrine()
			->getManager("telconet_infraestructura")
			->getRepository('schemaBundle:AdmiTipoElemento')
			->generarJsonTiposElementos($parametros);
			
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    public function obtenerDatosCasosCierreAction()
    {    

	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em  =$this->getDoctrine()->getManager('telconet_soporte');                
        $emC =$this->getDoctrine()->getManager('telconet');     
        
        $peticion = $this->get('request');
        
        $session = $peticion->getSession();
        
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        
        //if($codEmpresa == 'TN') $codEmpresa = 'MD';
        
        $idCaso = $peticion->get('id_caso');   
               
        $esCancelado = $peticion->get('es_cancelado');       
        
        $caso = $em->getRepository('schemaBundle:InfoCaso')->find($idCaso); 
        
        if($caso)
	{
	      $empresa = $emC->getRepository('schemaBundle:InfoEmpresaGrupo')->find($caso->getEmpresaCod());
	      if($empresa)
		    $prefijoEmpresa = $empresa->getPrefijo();  
	}	
        
        $objJson = $em->getRepository('schemaBundle:InfoCaso')
                      ->obtenerDatosCierreCaso($caso,$prefijoEmpresa,$em,$esCancelado);
        
        $respuesta->setContent($objJson);
                
        
        return $respuesta;
    
    }
	
    /**
    * Documentación de la funcion 'getElementosAction'.
    *
    * Método que Llena el grid de consulta de los elementos asociados el tipo de elemento seleccionado, en la opcion de crear
    * un caso
    *
    * @return $respuesta retorna el resultado de la operación
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 26-07-2016 Se realiza ajustes porque se agrega elementos en la creacion de actividades
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 17-12-2015
    *
    * @Secure(roles="ROLE_78-44")
    */ 
    public function getElementosAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $em = $this->getDoctrine()->getManager("telconet_soporte");

        $session = $peticion->getSession();

        $query  = $peticion->query->get('query');
        $nombre = $query?$query:$peticion->query->get('nombreElemento');        

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $actividades  = $peticion->query->get('actividades');
        $tipoElemento = $peticion->query->get('tipoElemento');
        $activoFijo   = $peticion->query->get('activoFijo');

        $caso = $peticion->query->get('caso') ? $peticion->query->get('caso') : '';

        $codEmpresa = $session->get('idEmpresa');

        //Se verifica que si se quiere obtener el elemento para relacionar un caso
        //Se valide con la empresa de la cual proviene el mismo y así mostralo segun la empresa en el que fue creado
        if($caso != '')
        {
            $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
            if($caso)
            {
                $codEmpresa = $caso->getEmpresaCod();
            }
        }

        $parametros['nombre']       = $nombre;
        $parametros['estado']       = 'Activo';
        $parametros['tipoElemento'] = $tipoElemento;
        $parametros['codEmpresa']   = $codEmpresa;
        $parametros['start']        = $start;
        $parametros['limit']        = $limit;
        $parametros['activoFijo']   = $activoFijo;
        $parametros['actividades']  = $actividades;

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosXTipo($parametros);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
    * getEncontradosAction
    *
    * Esta funcion retorna la lista de los elmentos seleccionado en la pantalla de agregar afectados, en la opcion de crear un caso
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.7 11-08-2016  Se realizan ajustes para agregar a los clientes afectados de un splitter
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.6 19-07-2016  Se realizan ajustes para que se permita agregar afectados por ciudad,pe,anillo
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.5 22-05-2016  Se realizan ajustes para que se permita agregar afectados de un caso, a aquellos elementos que no tienen interfaz
    *
    * @author Allan Suarez    <arsuarez@telconet.ec>
    * @version 1.4 16-05-2016  Se realizan los ajustes para presentar afectados realcionados con proveedores
    * 
    * @author Allan Suarez    <arsuarez@telconet.ec>
    * @version 1.3 09-01-2016  Se realizan los ajustes para presentar los afectados en el panel de servicios por cliente
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 06-01-2016  Se realizan los ajustes para presentar los afectados en el panel de empleados y servidores
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 17-12-2015  Se realizan ajustes para presentar los elementos seleccionado en la nueva pantalla de movilizacion
    *                          en la opcion de crear un caso
    *
    * @version 1.0
    *
    * @return JSON $respuesta
    *
    * @Secure(roles="ROLE_78-46")
    */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $peticion          = $this->get('request');

        $codEmpresa        = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $band              = $peticion->get('band');
        $id_param          = $peticion->get('id_param');
        $tipo_param        = $peticion->get('tipo_param');
        $name_param        = $peticion->get('name_param');
        $strWsNetWorking   = $peticion->get('wsNetWorking');
        $strNombreElemento = $peticion->get('nombreElemento');
        $strNumeroAnillo   = $peticion->get('numeroAnillo');
        $intStart          = $peticion->get('start');
        $intLimit          = $peticion->get('limit');
        $prefijoEmpresa    = $peticion->get('prefijoEmpresa');
        $serviceTecnico    = $this->get('tecnico.InfoServicioTecnico');

        if($band == "cliente")
        {
            $arrayResultado = $this->getDoctrine()
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:InfoPunto')
                    ->getResultadoPuntosParaAfectacionCasos($tipo_param, $name_param,$intStart,$intLimit);
            
            $resultado = $arrayResultado['resultado'];
            if($resultado)
            {
                foreach($resultado as $data)
                {
                    $arrayEncontrados[] = array('id_parte_afectada'     => $data['idPunto'],
                                                'nombre_parte_afectada' => $data['login'],
                                                'id_descripcion_1'      => '',
                                                'nombre_descripcion_1'  => $data['nombres'],
                                                'id_descripcion_2'      => '',
                                                'nombre_descripcion_2'  => $data['estado']);
                }

                $arrayRespuesta = array('total'=> $arrayResultado['total'] , 'encontrados' => $arrayEncontrados);                          
            }
            else
            {   
                $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                 
            }
            $objJson=json_encode($arrayRespuesta); 
        }
        else if($band == "Elemento")
        {
            if($tipo_param=="Puertos")
            {
                $objJson = $this->getDoctrine()
                    ->getManager("telconet_infraestructura")
                    ->getRepository('schemaBundle:InfoElemento')
                    ->generarJsonInterfacesXElemento($id_param, 'connected', '', '');
            }
            else if($tipo_param == "Logines")
            {
                $strNombreTipoElemento = "";
                $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($id_param);
                if($objInfoElemento)
                {
                    $strNombreTipoElemento = $objInfoElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                }

                if($strNombreTipoElemento == "SPLITTER")
                {
                    $objJson = $this->getDoctrine()
                                    ->getManager("telconet_infraestructura")
                                    ->getRepository('schemaBundle:InfoElemento')
                                    ->getJsonAfectadosPorSplitter($id_param,$codEmpresa);
                }
                else
                {
                    $objJson = $this->getDoctrine()
                                    ->getManager("telconet_infraestructura")
                                    ->getRepository('schemaBundle:InfoElemento')
                                    ->generarJsonLoginesXElemento($id_param);
                }
            }
            else if($tipo_param == "Ninguna")
            {
                $objJson = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:InfoElemento')
                                ->generarJsonActivoFijo($id_param,$name_param);
            }
            else if($tipo_param == "Ciudad")
            {
                $parametros = array();
                $parametros["tipoElemento"]    = "SWITCH";
                $parametros["cantonId"]        = $id_param;
                $parametros["codEmpresa"]      = $codEmpresa;
                $parametros["tipoConsulta"]    = "getEncontrados";
                $parametros["wsNetWorking"]    = $strWsNetWorking;
                $parametros["pe"]              = $strNombreElemento;
                $parametros["anillo"]          = $strNumeroAnillo;
                $parametros["servicioTecnico"] = $serviceTecnico;

                $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                             ->generarJsonElementosPorCiudadYTipoYEmpresa($parametros);
            }
            else
            {
                $objJson = '{"total":"0","encontrados":[]}';
            }
        }
        else if($band == "planProducto")
        {
            //tipo_param es false cuando se busca clientes relacionados a un tipo de producto
            if($tipo_param == "false")
            {
                $objJson = $this->getDoctrine()
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:AdmiProducto')
                    ->generarJsonLoginesXProducto($id_param, $codEmpresa,$intStart,$intLimit);
            }
            //tipo_param es true cuando se busca clientes relacionados a un tipo de plan
            else
            {
                $objJson = $this->getDoctrine()
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:InfoPlanCab')
                    ->getJsonClientesPorPlanId($id_param);
            }
        }
        else if($band == "ActivoFijo" || $band == "servidor")
        {
            $objJson = $this->getDoctrine()
                ->getManager("telconet_infraestructura")
                ->getRepository('schemaBundle:InfoElemento')
                ->generarJsonActivoFijo($id_param,$name_param);
        }
        else if($band == "empleado")
        {
            if($prefijoEmpresa!="")
            {
                $empresa =  $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                                        ->findOneBy(array('prefijo'=>$prefijoEmpresa));
                if($empresa)
                {
                    $codEmpresa = $empresa->getId();
                }
            }

            $objJson = $this->getDoctrine()
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getJsonPersonaAfectada($id_param,$name_param,$codEmpresa);
        }
        else if($band == "empleadoDepartamento")
        {
            if($prefijoEmpresa!="")
            {
                $empresa =  $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                                        ->findOneBy(array('prefijo'=>$prefijoEmpresa));
                if($empresa)
                {
                    $codEmpresa = $empresa->getId();
                }
            }

            $objJson = $this->getDoctrine()
                            ->getManager("telconet")
                            ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->getJsonPersonasPorDepartamentoAfectado($id_param,$tipo_param,$codEmpresa);
        }
        else if($band == "servicios")
        {
            $arrayResultado =  $this->getDoctrine()->getManager("telconet")
                                            ->getRepository("schemaBundle:AdmiProducto")->getResultadoServiciosAfectadosSla($tipo_param,$id_param);
            $resultado = $arrayResultado['resultado'];
            if($resultado)
            {
                foreach($resultado as $data)
                {
                    $arrayEncontrados[] = array('id_parte_afectada'     => $data['idServicio'],
                                                'nombre_parte_afectada' => $data['nombreProducto'],
                                                'id_descripcion_1'      => '',
                                                'nombre_descripcion_1'  => $data['nombreProducto'],
                                                'id_descripcion_2'      => '',
                                                'nombre_descripcion_2'  => $data['estado']);
                }

                $arrayRespuesta = array('total'=> $arrayResultado['total'] , 'encontrados' => $arrayEncontrados);                          
            }
            else
            {   
                $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                 
            }
            $objJson=json_encode($arrayRespuesta);       
        }
        else if($band == "proveedores")
        {
            //Se obtiene los roles empresa de un Tipo Rol 'Proveedor'
            $rolEmpresa     = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                                                          ->getRolEmpresaPorTipo($codEmpresa,"Proveedor","Proveedor Internacional");
            
            $objRol     = $this->getDoctrine()->getManager("telconet_general")->getRepository('schemaBundle:AdmiRol')
                                                                          ->findOneByDescripcionRol("Proveedor Internacional");
            
            $arrayResultado =  $this->getDoctrine()->getManager("telconet")
                                            ->getRepository("schemaBundle:InfoPersona")
                                            ->getRegistrosContratistas($codEmpresa,$rolEmpresa['idPerfil'],$objRol->getId(),$id_param);            
            $arrayEncontrados = array();
            
            if ($arrayResultado['total']!=0)
            {
                foreach ($arrayResultado['registros'] as $data)
                {                    
                    if($data["razonSocial"])
                    {
                        $nombre = $data["razonSocial"];
                    }
                    else
                    {
                        $nombre = $data["nombres"]." ".$data["apellidos"];
                    }
                    
                    $arrayEncontrados[] = array('id_parte_afectada'     => $data["id"],
                                                'nombre_parte_afectada' => $nombre,
                                                'id_descripcion_1'      => '',
                                                'nombre_descripcion_1'  => $nombre,
                                                'id_descripcion_2'      => '',
                                                'nombre_descripcion_2'  => $data["estado"]);                 
                } 
            }
            
            $arrayRespuesta = array('total'=> $arrayResultado['total'] , 'encontrados' => $arrayEncontrados);            
            $objJson=json_encode($arrayRespuesta);   
        }
        else{
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);        
        return $respuesta;
    }

    /**
    * getElementosPorCiudadYTipoYEmpresaAction
    *
    * Esta funcion retorna los elementos de una ciudad, de un tipo especifico y que pertenescan a una empresa especifica
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 18-07-2016
    *
    * @return JSON $respuesta
    *
    */
    public function getElementosPorCiudadYTipoYEmpresaAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objPeticion       = $this->getRequest();
        $objSession        = $objPeticion->getSession();
        $objRespuesta      = new Response();
        $strTipoElemento   = $objPeticion->get("tipoElemento");
        $intCantonId       = $objPeticion->get("cantonId");
        $strCodEmpresa     = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $usrCreacion       = $objSession->get('user');
        $strIpClient       = $objPeticion->getClientIp();
        $serviceUtil       = $this->get('schema.Util');
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $arrayParametros["tipoElemento"]     = $strTipoElemento;
        $arrayParametros["cantonId"]         = $intCantonId;
        $arrayParametros["codEmpresa"]       = $strCodEmpresa;
        $arrayParametros["tipoConsulta"]     = "getElementos";
        $arrayParametros["wsNetWorking"]     = "N";
        $arrayParametros["pe"]               = "";
        $arrayParametros["anillo"]           = "";
        $arrayParametros["servicioTecnico"]  = "";

        try
        {
            $JsonElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                               ->generarJsonElementosPorCiudadYTipoYEmpresa($arrayParametros);

            $objRespuesta->setContent($JsonElementos);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'InfoCasoController->getElementosPorCiudadYTipoYEmpresaAction', $ex->getMessage(), $usrCreacion, $strIpClient);
        }

        return $objRespuesta;
    }

    /**
    * getAnillosAction
    *
    * Esta funcion retorna los anillos configurados
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 18-07-2016
    *
    * @return JSON $respuesta
    *
    */
    public function getAnillosAction()
    {
        $objRespuesta        = new Response();
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        $objPeticion         = $this->getRequest();
        $objSession          = $objPeticion->getSession();
        $usrCreacion         = $objSession->get('user');
        $strIpClient         = $objPeticion->getClientIp();
        $serviceUtil         = $this->get('schema.Util');

        $objRespuesta->headers->set('Content-Type', 'text/plain');

        try
        {
            $JsonElementos = $serviceInfoElemento->generarJsonAnillos();
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'InfoCasoController->getAnillosAction', $ex->getMessage(), $usrCreacion, $strIpClient);
        }

        $objRespuesta->setContent($JsonElementos);

        return $objRespuesta;
    }

    /**
    *
    * Documentación de la funcion 'getBanderaPanelAction'.
    *
    * Método que retorna una bandera para determinar si el tipo de caso seleccionado es Movilziacion
    *
    * @return Response retorna el resultado de la operación
    *
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.1 06-12-2015 Se realizan ajustes para obtener la configuracion de los paneles a presentarse segun el Tipo de Caso seleccionado
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 17-12-2015
    */
    public function getBanderaPanelAction()
    {
        $emSoporte    = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral    = $this->getDoctrine()->getManager('telconet_general');
        $objPeticion  = $this->getRequest();
        $objRespuesta = new Response();
        $tipoCasoId   = $objPeticion->get("tipoCaso");
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $objTipoCaso = $emSoporte->getRepository('schemaBundle:AdmiTipoCaso')->find($tipoCasoId);

        //Se obtiene la configuracion de los paneles que se visualizaran de manera dinamica por tipo de caso escogido
        $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get("PANELES_AFECTACION_CASOS",'SOPORTE','CASOS',null,$objTipoCaso->getNombreTipoCaso(),null,null,null);
        $arrayRespuesta = array();

        if(count($arrayResultado)>0)
        {
            foreach($arrayResultado as $data)  
            {
                $arrayRespuesta[] = array('panel' => $data['valor2'], //Devuelve el valor del Panel a mostrar en pantalla
                                          'titulo'=> $data['valor3']  //El titulo del panel relacionado a mostrar
                                         );
            }
        }

        return $objRespuesta->setContent(json_encode($arrayRespuesta));
    }
    
    /**
     * 
     * Documentación de la funcion 'ajaxGetServiciosPorClienteSesionAction'.
     *
     * Método que retorna los servicios activos del cliente en sesion para eleccion de afectados
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 09-01-2016 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function ajaxGetServiciosPorClienteSesionAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $idPunto    = $peticion->query->get('idPunto');  
        
        $jsonResultado = $emComercial->getRepository("schemaBundle:AdmiProducto")->getJsonServiciosAfectadosSla($idPunto,null);
        
        $respuesta->setContent($jsonResultado);
        return $respuesta;
    }
    
    /**
     * Documentación de la funcion 'ajaxGetPlanesProductosCasoAction'.
     *
     * Método que retorna los planes o productos para asiganr afectados dentro del caso segun el tipo de busqueda enviada via ajax
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 10-01-2016 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetPlanesProductosCasoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $esPlan = $peticion->query->get('esPlan')=='true'?true:false;                  
        
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $arrayObjPlanesProductos    = Array();        
        
        if($esPlan)
        {
            $arrayObjPlanesProductos = $emComercial->getRepository("schemaBundle:InfoPlanCab")
                                          ->findBy(array('estado'=>'Activo','empresaCod'=>$codEmpresa));
        }
        else
        {
            $arrayObjPlanesProductos = $emComercial->getRepository("schemaBundle:AdmiProducto")
                                             ->findBy(array('estado'=>'Activo','empresaCod'=>$codEmpresa));
        }
        
        $arrayResultado = Array();
        
        $intCont = count($arrayObjPlanesProductos);
        
        foreach($arrayObjPlanesProductos as $planProducto)
        {
            $arrayResultado[] = array(
                                     'id'          => $planProducto->getId(),
                                     'codigo'      => !$esPlan?$planProducto->getCodigoProducto():$planProducto->getCodigoPlan(),
                                     'nombre'      => !$esPlan?$planProducto->getDescripcionProducto():$planProducto->getNombrePlan(),
                                     'descripcion' => !$esPlan?'':$planProducto->getDescripcionPlan()
                                    );                       
        }
        
        $arrayRespuesta = array('total'=> $intCont , 'encontrados' => $arrayResultado);                
        
        $respuesta->setContent(json_encode($arrayRespuesta));
        return $respuesta;
    }
    
    

	/**
	* @Secure(roles="ROLE_78-226")
	*/
	public function getDetallesAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoDetalle')
				            ->generarJsonDetallesXCaso($id);		
				
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    
    public function getMotivosReprogramacionAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    
	    $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");     
	    $em_general = $this->getDoctrine()->getManager("telconet_general");     
	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');
	    
 	    $modulo = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->getSistModuloSoporte();

	    $ids = '(';
	    $i=1;
 	    foreach($modulo as $m){
		  if($i < count($modulo))
		      $ids .= $m->getId().',';
		  else $ids .= $m->getId();
		  $i++;
 	    }
 	    $ids .= ')'; 	     	    
 	     	    
 	    $objJson = $em_general->getRepository('schemaBundle:AdmiMotivo')->generarJsonMotivos($ids); 	   	 
				    
	    $respuesta->setContent($objJson);
	    return $respuesta;
    
    }

    /**
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 20-11-2018 - Se reemplaza el método generarJsonCriteriosTotalXCaso por getCriteriosAfectadosCaso
     *                           para optimizar el tiempo de respuesta de los criterios afectados.
     *
     * @Secure(roles="ROLE_78-42")
     */
    public function getCriteriosAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $boolTodos   = $objPeticion->query->get('todos') == "YES";
        $intStart    = $objPeticion->query->get('start');
        $intLimit    = $objPeticion->query->get('limit');

		if($boolTodos)
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
                                            ->getCriteriosAfectadosCaso(array ('intIdCaso' => $id,
                                                                               'intStart'  => $intStart,
                                                                               'intLimit'  => $intLimit));
		}
		else
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonCriteriosXCaso($id, "NO", "NO");
        }
		
        $respuesta->setContent($objJson);
        return $respuesta;
    }
  
    /**
    * @Secure(roles="ROLE_78-42")
    */ 
    public function getCriterios2Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');  
        $em = $this->getDoctrine()->getManager('telconet_soporte');
		
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('id') ? $peticion->query->get('id') : 0;
        $nombre_sintoma = $peticion->query->get('id_sintoma') ? $peticion->query->get('id_sintoma') : "";
        $nombre_hipotesis = $peticion->query->get('id_hipotesis') ? $peticion->query->get('id_hipotesis') : "";
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');        
		
		$id_sintoma = "NO";
		$id_hipotesis = "NO";		
		if($nombre_sintoma && $nombre_sintoma != "" && $nombre_sintoma != "NO")
		{
			$sintomas = $em->getRepository('schemaBundle:AdmiSintoma')->findOneByNombreSintoma($nombre_sintoma);
			$id_sintoma = ($sintomas && count($sintomas)>0) ? $sintomas->getId() : "NO";
		}		
		if($nombre_hipotesis && $nombre_hipotesis != "" && $nombre_hipotesis != "NO")
		{
			$hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findOneByNombreHipotesis($nombre_hipotesis);
			$id_hipotesis = ($hipotesis && count($hipotesis)>0) ? $hipotesis->getId() : "NO";
		}
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoCaso')
            ->generarJsonCriteriosXCaso($id, $id_sintoma, $id_hipotesis,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 20-11-2018 - Se reemplaza el método generarJsonAfectadosTotalXCaso por getAfectadosCaso
     *                           para optimizar el tiempo de respuesta de los afectados.
     *
     * @Secure(roles="ROLE_78-39")
     */
    public function getAfectadosAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $boolTodos   = $objPeticion->query->get('todos') == "YES";
        $intStart    = $objPeticion->query->get('start');
        $intLimit    = $objPeticion->query->get('limit');

		if($boolTodos)
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
                                            ->getAfectadosCaso( array('intIdCaso' => $id,
                                                                      'intStart'  => $intStart,
                                                                      'intStart'  => $intLimit));
		}
		else
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonAfectadosXCaso($id, "NO", "NO");
        }
		
        $respuesta->setContent($objJson);        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-39")
    */ 
    public function getAfectados2Action()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');        
        $em = $this->getDoctrine()->getManager('telconet_soporte');
		
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('id') ? $peticion->query->get('id') : 0;
        $nombre_sintoma = $peticion->query->get('id_sintoma') ? $peticion->query->get('id_sintoma') : "";
        $nombre_hipotesis = $peticion->query->get('id_hipotesis') ? $peticion->query->get('id_hipotesis') : "";
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');        
		
		$id_sintoma = "NO";
		$id_hipotesis = "NO";		
		if($nombre_sintoma && $nombre_sintoma != "" && $nombre_sintoma != "NO")
		{
			$sintomas = $em->getRepository('schemaBundle:AdmiSintoma')->findOneByNombreSintoma($nombre_sintoma);
			$id_sintoma = ($sintomas && count($sintomas)>0) ? $sintomas->getId() : "NO";
		}		
		if($nombre_hipotesis && $nombre_hipotesis != "" && $nombre_hipotesis != "NO")
		{
			$hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findOneByNombreHipotesis($nombre_hipotesis);
			$id_hipotesis = ($hipotesis && count($hipotesis)>0) ? $hipotesis->getId() : "NO";
			
			if($nombre_sintoma == "" && $id_sintoma == "NO" && $id_hipotesis != "NO")
			{
				$id_sintoma = "";
			}
		}
			
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoCaso')
            ->generarJsonAfectadosXCaso($id, $id_sintoma, $id_hipotesis);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    /**
    * @Secure(roles="ROLE_78-45")
    */ 
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
    
    public function getOficinasAction()
    {
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $em = $this->getDoctrine()->getManager('telconet');
	    
	    $peticion   = $this->get('request');
	    $idEmpresa  = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");        
	    
	    $tipo       = ($peticion->get('tipo') ? $peticion->get('tipo') : "");   
	    $codEmpresa = ($peticion->get('idEmpresa') ? $peticion->get('idEmpresa') : ""); 
	    $nombreOficina = ($peticion->get('query') ? $peticion->get('query') : ""); 
	    
	    if($tipo && $tipo=='prefijo'){
	          if($codEmpresa)
		      $codEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($codEmpresa);
	    }else $codEmpresa = $idEmpresa;
	  
	    $objJson = $em->getRepository('schemaBundle:InfoCaso')->generarInfoOficinaGrupo($codEmpresa,$nombreOficina);
	  
	    $respuesta->setContent($objJson);     
	    return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-49")
    */ 
    public function getSintomasXCasoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('id');
        $boolCriteriosAfectados = $peticion->query->get('boolCriteriosAfectados') == "NO" ?  false : true ;
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalle')
            ->generarJsonSintomasXCaso($id, $boolCriteriosAfectados);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-47")
    */ 
    public function getHipotesisXCasoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('id');
        $band = $peticion->query->get('band');                               
        
	$emComercial = $this->getDoctrine()->getManager('telconet');			
		
	if($band == "tarea")
	{
		    $objJson = $this->getDoctrine()
				    ->getManager("telconet_soporte")
				    ->getRepository('schemaBundle:InfoDetalle')
				    ->generarJsonHipotesisXCaso_Tarea($id, $emComercial);
	}
	else
	{
		    $objJson = $this->getDoctrine()
				    ->getManager("telconet_soporte")
				    ->getRepository('schemaBundle:InfoDetalle')
				    ->generarJsonHipotesisXCaso($id, $emComercial);		
	}
	
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-54")
    */ 
    public function getTramosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('id');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoTramo')
            ->generarJsonTramos();
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-52")
    */ 
    public function getTiposAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $peticion = $this->get('request');
        $nombre     = $peticion->query->get('query');
        $estado     = $peticion->query->get('estado');
        
        $codEmpresa = $peticion->getSession()->get('idEmpresa');
              
        $caso       = $peticion->query->get('caso')?$peticion->query->get('caso'):'';                
  
        if($caso != '')
        {
	      $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
	      if($caso)
	      {
		    $codEmpresa = $caso->getEmpresaCod();	      
	      }	      
	}       		
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $tipo  = $peticion->query->get('tipo');                    
                        
        if($tipo=="Tramo")
        {
	      $objJson = $this->getDoctrine()
	      ->getManager("telconet_infraestructura")
	      ->getRepository('schemaBundle:InfoTramo')
	      ->generarJsonTramos();
        }
        else
        {
	      $objJson = $this->getDoctrine()
	      ->getManager("telconet_infraestructura")
	      ->getRepository('schemaBundle:InfoElemento')
	      ->generarJsonElementosTarea($nombre,'Activo',$tipo,$start,$limit,$codEmpresa);            
        }
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-40")
    */ 
    public function getAreasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
         $session = $peticion->getSession();
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");  
        
        
        $nombre = $peticion->query->get('query');
        $id = $peticion->query->get('id_param');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";
	
	if($paramEmpresa!=""){
	      
	      $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
	      if($empresa)$codEmpresa = $empresa->getId();
	}
                        
		
		$objJson = $this->getDoctrine()
						->getManager()
						->getRepository('schemaBundle:AdmiDepartamento')
						->generarJsonAreasXOficina($id, $nombre,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /*******************************************************************************************/
    /**
     * Funcion que consulta las ciudades por empresa
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-06-2016 Se habilita la busqueda del combo ciudad
     *
     * @version 1.0
     *
     * @return array $respuesta
     */
    public function getCiudadesPorEmpresaAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
	    	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');	    	    
	    $ciudad = $peticion->query->get('query');
	    $origen = $peticion->query->get('origen') ? $peticion->query->get('origen') : "";

	    $em = $this->getDoctrine()->getManager("telconet");
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";	    	    
	    
	    if($paramEmpresa!=""){
		  
		  $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
		  if($empresa)$codEmpresa = $empresa->getId();
	    }
		    
	    $objJson = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
	    ->generarJsonCiudadesPorEmpresa($codEmpresa,$ciudad,$origen);
	    
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;        
    
    }
    /**
     * Funcion getPrefijosTelefonoAction: consulta los prefijos de número de telefonos fijos
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.0 31/03/2023
     *
     * @return $respuesta
     */
    public function getPrefijosTelefonoAction()
    {
	    $objRespuesta = new Response();
	    $objRespuesta->headers->set('Content-Type', 'text/json');	    
	    $arrayEncontrados = array();
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");        
        
        $arrayLineasTelefonia  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get(   'PARAMETROS_LINEAS_TELEFONIA',
                                                                                                    null,
                                                                                                    'FLUJO ACTIVACION',
                                                                                                    'PREFIJOS_PROVINCIA',
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null);

        if (!empty($arrayLineasTelefonia))
        {
            foreach ($arrayLineasTelefonia as $objLinea)
            {
                $strCodigoArea = '0' . $objLinea['valor2'];
                $boolExisteCodigo = false;
                foreach ($arrayEncontrados as $val)
                {
                    if ($val['codigo'] === $strCodigoArea)
                    {
                        $boolExisteCodigo = true;
                    }
                }
                if (!$boolExisteCodigo)
                {
                    $arrayEncontrados[] = array('codigo' => $strCodigoArea);
                } 
            }
            sort($arrayEncontrados);
            $objData =json_encode($arrayEncontrados);
            $objRespuestaJson= '{"total":"' . count($arrayEncontrados) . '","encontrados":'.$objData.'}';                
        }
        else
        {
            $objRespuestaJson= '{"total":0,"encontrados":[]}';            
        }
       
        $objRespuesta->setContent($objRespuestaJson);
	    
	    return $objRespuesta;
    }

    /**
     * Funcion getDepartamentosPorEmpresaYCiudadAction: que consulta los departamentos por empresa y ciudad
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 24-06-2016 Se realiza ajustes por cambios en el grid de tareas, agregar nuevo filtro departamento origen de la tarea
     *
     * @version 1.0
     *
     * @return array $respuesta
     */
     public function getDepartamentosPorEmpresaYCiudadAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
	    	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');
	    
	    $id_canton = $peticion->query->get('id_canton')?$peticion->query->get('id_canton'):'';
	    
	    $em = $this->getDoctrine()->getManager("telconet");
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";
	    
	    $nombreDep = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	    $origen = $peticion->query->get('origen') ? $peticion->query->get('origen') : "";
	    if($paramEmpresa!=""){
		  
		  $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
		  if($empresa)$codEmpresa = $empresa->getId();
	    }

	    $objJson = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
	    ->generarJsonDepartamentosPorCiudadYEmpresa($codEmpresa,$id_canton,$nombreDep,$origen);
	    
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;    
    
    
    
    
    
    }
    
    
    
    /**
    * getCuadrillasAction
    * 
    * Esta funcion retorna las cuadrillas registrados en estado diferente de Eliminado
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 13-10-2015 
    *
    * @author modificado Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 17-05-2015 Se realizan ajustes para que combo busque por cuadrilla
    *
    * @param array  $parametros [$start,$limit,$estado,$departamentoId,$nombreCuadrilla]
    *
    * @return array $respuesta  Objeto en formato JSON
    *
    */
     public function getCuadrillasAction()
     {    
         $respuesta = new Response();
         $respuesta->headers->set('Content-Type', 'text/json');
         $peticion = $this->get('request');
        
         $start        = $peticion->query->get('start');
         $limit        = $peticion->query->get('limit');
         $estado       = $peticion->query->get('estado');
         $departamento = $peticion->query->get('departamento');
         $nombreCuadrilla = $peticion->query->get('query');
         $strOrigenP      = $peticion->query->get('strOrigenP');
         $em      = $this->getDoctrine()->getManager("telconet");

         $parametros["start"]           = $start;
         $parametros["limit"]           = $limit;
         $parametros["estado"]          = $estado;
         $parametros["departamentoId"]  = $departamento;
         $parametros["nombreCuadrilla"] = $nombreCuadrilla;
         $parametros["strOrigenP"]      = $strOrigenP;

         $objJson = $em->getRepository('schemaBundle:AdmiCuadrilla')->generarJsonCuadrillas($parametros);

         $respuesta->setContent($objJson);

         return $respuesta;    
    }    
    
    
    /**
    * getContratistaAction
    *
    * Esta funcion retorna las empresas externas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 24-12-2015
    *
    * @return array $respuesta  Objeto en formato JSON
    *
    */
     public function getContratistasAction()
     {
         $respuesta = new Response();
         $respuesta->headers->set('Content-Type', 'text/json');
         $peticion     = $this->get('request');
         $session      = $peticion->getSession();
         $codEmpresa   = $session->get('idEmpresa');
         $strRol       = $peticion->query->get('rol');         

         $em      = $this->getDoctrine()->getManager("telconet");

         //Se obtiene los roles empresa de un Tipo Rol 'Proveedor' y rol enviado como parametro
         $rolEmpresa = $em->getRepository('schemaBundle:InfoPersona')->getRolEmpresaPorTipo($codEmpresa,"Proveedor",$strRol);

         $objJson = $em->getRepository('schemaBundle:InfoPersona')->generarJsonContratistas($codEmpresa,$rolEmpresa["idPerfil"]);

         $respuesta->setContent($objJson);

         return $respuesta;
    }
    
     /**
     *
     * Documentación de la funcion 'getBanderaPresentarCerrarCasoAction'.
     *
     * Método que retorna una bandera para saber si se debe o no presentar la accion de Cerrar Caso
     *
     * @return $objRespuesta
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 29-10-2015
     */
    public function getBanderaPresentarCerrarCasoAction()
    {
        $emSoporte    = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $objPeticion  = $this->getRequest();
        $peticion     = $this->get('request');
        $session      = $peticion->getSession();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $idCaso              = $objPeticion->get("idCaso");
        $IdPersonaEmpresaRol = $session->get('idPersonaEmpresaRol');
        $bandera             = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                         ->getPresentarVentanaCerrarCaso($idCaso,$emComercial,$IdPersonaEmpresaRol);

        return $objRespuesta->setContent(json_encode(array('flag' => $bandera)));
    }
    
    public function getEmpleadosPorDepartamentoCiudadAction(){
    
    
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $emSoporte = $this->getDoctrine()->getManager("telconet_general");        
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $prefijoEmpresa = $session->get('prefijoEmpresa');                
        
        $nombre = $peticion->query->get('query');     
        
        $id_departamento = $peticion->query->get('id_departamento'); //departamento
        $id_canton = $peticion->query->get('id_canton');
        $departamentoCaso = $peticion->query->get('departamento_caso') ? $peticion->query->get('departamento_caso') : 0; //departamento del caso asignado                
        $soloJefesSINO = $peticion->query->get('es_jefe')?$peticion->query->get('es_jefe'):'no';
       
       if($soloJefesSINO=='si')$soloJefes = true;else $soloJefes=false;
       
       $empresas = '';
       
	if(!$soloJefes){
	    $soloJefes = true;
		  
	    $empresas='';                     
	    
	    if($departamentoCaso!=0){    
	      if($id_departamento == $departamentoCaso)$soloJefes = false; // si el depaetamento asignado del caso es igual al departamento a asignar tarea                                                    
	    }else $soloJefes = false;
	
	}		
		
	
	$codEmpresa = "";
	$codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 	
	
	$paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";
	  
	  if($paramEmpresa!=""){
		
		$empresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
		if($empresa)$codEmpresa = $empresa->getId();
	  }
	
	if($empresas!='')$codEmpresa = $empresas;			
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');    
        
        $cantones_unique = array();
		
        $objJson = $this->getDoctrine()
			->getManager("telconet")
			->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->generarJsonEmpleadosXDepartamento($id_departamento, '', $nombre, $soloJefes, true, $codEmpresa, $cantones_unique ,'no',$id_canton);
						
        $respuesta->setContent($objJson);
        
        return $respuesta;        
    
    }
    
    /**
     *
     * Documentación de la funcion 'getPersonaPorLoginAction'.
     *
     * Función que retorna los nombres completos de una persona en base a su login.
     *
     * @return $objRespuesta
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */
    public function getPersonaPorLoginAction()
    {
        $objPeticion    = $this->get('request');
        $strLogin       = ($objPeticion->get('login') ? $objPeticion->get('login') : "");
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $objRespuesta   = new Response();
        $serviceUtil    = $this->get('schema.Util');
        try
        {
            $strEmpleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($strLogin);
            if($strEmpleado && count($strEmpleado) > 0)
            {
                $objRespuesta->setContent($strEmpleado);
            }
            else
            {
                $objRespuesta->setContent('');
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudDescuentoController->getPersonaPorLoginAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return $objRespuesta;
    }
    
    
    /*******************************************************************************************/
    /**
    * @Secure(roles="ROLE_78-43")
    */ 
    public function getDepartamentosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $idEmpresa  = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        
        $nombre     = $peticion->query->get('query');
        $id         = $peticion->query->get('id_param');
        $id_oficina = $peticion->query->get('id_oficina');
        $prefijo    = $peticion->query->get('empresa');
        
        if($prefijo){
	      $codEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijo);
	      $codEmpresa = $codEmpresa->getId();
        }else $codEmpresa = $idEmpresa;
           	
        $objJson = $this->getDoctrine()
        ->getManager()
        ->getRepository('schemaBundle:AdmiDepartamento')
        ->generarJsonDepartamentosXArea($id, $id_oficina, $nombre,$codEmpresa);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-158")
    */ 
    public function getEmpleadosXDepartamentoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $emSoporte = $this->getDoctrine()->getManager("telconet_general");        
        $emComercial = $this->getDoctrine()->getManager("telconet");               
        
        $nombre = $peticion->query->get('query');
        
        $id = $peticion->query->get('id_param'); //departamento
        $id_oficina = $peticion->query->get('id_oficina'); //id_oficina                
        
        $id_caso = $peticion->query->get('id_caso') ? $peticion->query->get('id_caso') : '';                
        
        $soloJefes = $peticion->query->get('soloJefes') == "S" ? true : false ;                    
		
	$codEmpresa = "";
	if(!$soloJefes)
	{
	//	$id_oficina  = '';
		$codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");  
					
	}
	else
	{
	//	$id_oficina  = '';		
		$entityOficina = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoOficinaGrupo')->findOneById($id_oficina);
		$codEmpresa = ($entityOficina ? $entityOficina->getEmpresaId()->getId() : "");
					
		
	}			
	
	$cantones_unique = array();
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
                
        $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";                
	
        if($paramEmpresa!=""){
              
              $empresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
              if($empresa)$codEmpresa = $empresa->getId();
        }		
		
        $objJson = $this->getDoctrine()
			->getManager("telconet")
			->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->generarJsonEmpleadosXDepartamento($id, $id_oficina, $nombre, $soloJefes, true, $codEmpresa, $cantones_unique);
						
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getEmpleadosAllXDepartamentoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $emSoporte = $this->getDoctrine()->getManager("telconet_general");        
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        
        $nombre = $peticion->query->get('query');
        
        $id = $peticion->query->get('id_param'); //departamento
        $id_oficina = $peticion->query->get('id_oficina');
        $departamentoCaso = $peticion->query->get('departamento') ? $peticion->query->get('departamento') : 0; //departamento del caso asignado
        
        $id_caso = $peticion->query->get('id_caso') ? $peticion->query->get('id_caso') : '';
       
        $soloJefes = true;
        
        $esMD = 'no';
        $strEsEN = 'no';
        $empresas='';                          
              
        $cont =0;              
        
        if($id == $departamentoCaso)$soloJefes = false; // si el depaetamento asignado del caso es igual al departamento a asignar tarea        
        
        if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN')
        {                       
        
            $departamento = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')->find($id);
                     
            if($departamento){                                                            
        
                if(strtolower($departamento->getNombreDepartamento())=='operativos'){
                                        
                    $tn = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>'TN'));
                    $md = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>'MD'));
                    $strEn = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>'EN'));
                    
                    if($prefijoEmpresa == 'MD') 
                    {
                        $empresas = '('.$tn->getId().','.$md->getId().')';
                    } 
                    else  
                    {
                        $empresas = '('.$tn->getId().','.$strEn->getId().')';
                    }
                                               
                    $idsDeps = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')->getDepartamentosByEmpresaYNombre($empresas,$departamento->getNombreDepartamento());
        
                    $ids = "(";
                    $i=1;
                    foreach ($idsDeps as $id){
                                       
                        if($i < count($idsDeps))
                            $ids .= $id->getId().",";
                        else $ids .= $id->getId();
                        $i++;
                        
                        if($id->getId()==$departamentoCaso)$cont++;
                    }
                    $ids .= ")";
                    
                    $id = $ids;   

                    if($prefijoEmpresa == 'MD') 
                    {
                        $esMD = 'si'; 
                    } 
                    else   
                    {
                        $strEsEN = 'si'; 
                    }                                            
        
                } 
            }
        }        
        
      
        if($cont!=0)$soloJefes=false;
     
		
	$codEmpresa = "";
	if(!$soloJefes)
	{
		//$id_oficina  = '';
		$codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");  
					
	}
	else
	{
		//$id_oficina  = '';
		
		$entityOficina = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoOficinaGrupo')->findOneById($id_oficina);
		$codEmpresa = ($entityOficina ? $entityOficina->getEmpresaId()->getId() : "");
					
		
	}	
	
	if($empresas!='')$codEmpresa = $empresas;
	
	$cantones_unique = array();
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');    

		if($prefijoEmpresa == 'MD') 
        {
            $objJson = $this->getDoctrine()
			->getManager("telconet")
			->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->generarJsonEmpleadosXDepartamento($id, $id_oficina, $nombre, $soloJefes, true, $codEmpresa, $cantones_unique , $esMD);
        } 
        else   
        {
            $objJson = $this->getDoctrine()
			->getManager("telconet")
			->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->generarJsonEmpleadosXDepartamento($id, $id_oficina, $nombre, $soloJefes, true, $codEmpresa, $cantones_unique , $strEsEN);
        }  
						
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
 
    /**
     * verSeguimientoTareaAction
     *
     * Esta funcion retorna la informacion de los seguimientos de una tarea
     *
     * @author  Desarrollo Inicial
     * @version 1.0
     *
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 20-09-2016 Se realizan ajustes por concepto de seguimientos internos
     *
     * @return json $objRespuesta
     *
     */
    public function verSeguimientoTareaAction()
    {
        $objRespuesta    = new Response();
        $arrayParametros = array();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion             = $this->get('request');
        $objSession              = $objPeticion->getSession();
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strDepartamentoSession  = $objSession->get('idDepartamento');
        $intIdDetalle            = $objPeticion->query->get('id_detalle');
        
        $emSoporte   = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');

        $arrayParametros["emComercial"]         = $emComercial;
        $arrayParametros["emGeneral"]           = $emGeneral;
        $arrayParametros["idDetalle"]           = $intIdDetalle;
        $arrayParametros["codEmpresa"]          = $strCodEmpresa;
        $arrayParametros["departamentoSession"] = $strDepartamentoSession;
        $arrayParametros["objSoporteService"]   = $this->get('soporte.SoporteService');

        $objJson = $emSoporte->getRepository('schemaBundle:InfoCaso')->generarJsonSeguimientoXTarea($arrayParametros);

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
     * getTareaAsignadoAction
     * 
     * Esta funcion retorna la informacion de a quien esta asiganda una tarea.
     * 
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015 
     * 
     * @param array  $cuadrilla
     * 
     * @return array $respuesta Objeto JSON
     *
     * 
     * @Secure(roles="ROLE_78-50")
     * 
     */ 
    public function getTareaAsignadoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');        
        $peticion  = $this->get('request');

        $id               = $peticion->query->get('id_detalle');
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $em               = $this->getDoctrine()->getManager('telconet_soporte');
        $ultimaAsignacion = $em->getRepository('schemaBundle:InfoDetalleAsignacion')->getUltimaAsignacion($id);                        
        $departamento     = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($ultimaAsignacion->getAsignadoId());                

        if($ultimaAsignacion->getTipoAsignado() == "EMPLEADO")
        {   
            $area             = $departamento->getAreaId()->getNombreArea();            
            $oficina          = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                        ->findBy(array('departamentoId'=>$departamento->getId(),
                                                       'personaId'     =>$ultimaAsignacion->getRefAsignadoId()));             
        if($oficina[0])
            $oficina = $oficina[0]->getOficinaId()->getNombreOficina();
        else 
            $oficina = '';
	
        }

        $arr_encontrados = array();
        
        if($ultimaAsignacion->getTipoAsignado() == "CUADRILLA")
        {
            
            $datosLider         = $emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                              ->getDatosLiderCuadrilla($ultimaAsignacion->getRefAsignadoId());                            
            
            $tipoAsignado       = "Cuadrilla";
            $arr_encontrados[]  = array('oficina'      => ($datosLider['nombreDeOficina'])?$datosLider['nombreDeOficina']:"N/A",
                                        'area'         => ($datosLider['nombreDeArea'])?$datosLider['nombreDeArea']:"N/A",
                                        'departamento' => ($datosLider['nombreDeDepartamento'])?$datosLider['nombreDeDepartamento']:"N/A",
                                        'empleado'     => ($ultimaAsignacion->getAsignadoNombre())?$ultimaAsignacion->getAsignadoNombre():"N/A",
                                        'tipoAsignado' => $tipoAsignado);            
        }
        elseif($ultimaAsignacion->getTipoAsignado() == "EMPLEADO")
        {
            $tipoAsignado      = "Empleado";            
            $arr_encontrados[] = array('oficina'      => $oficina,
                                       'area'         => $area,
                                       'departamento' => $departamento->getNombreDepartamento(),
                                       'empleado'     => ($ultimaAsignacion->getRefAsignadoNombre())?$ultimaAsignacion->getRefAsignadoNombre():"N/A",
                                       'tipoAsignado' => $tipoAsignado);
        }
        elseif($ultimaAsignacion->getTipoAsignado() == "EMPRESAEXTERNA")
        {
            $contratista       = $emComercial->getRepository('schemaBundle:InfoPersona')->find($ultimaAsignacion->getAsignadoId());
            $tipoAsignado      = "Contratista";
            $arr_encontrados[] = array('oficina'      => "N/A",
                                       'area'         => "N/A",
                                       'departamento' => "N/A",
                                       'empleado'     => ($contratista->__toString())?$contratista->__toString():"N/A",
                                       'tipoAsignado' => $tipoAsignado);
        }
        
        $data       = json_encode($arr_encontrados);
        $resultado  = '{"success":true,"message":"Loaded data","total":"1","encontrados":'.$data.'}';

        $respuesta->setContent($resultado);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-51")
    */ 
    public function getTareasXCasoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $id = $peticion->query->get('id');
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial = $this->getDoctrine()->getManager('telconet');
                        
        $agregarTareas = $peticion->query->get('agregarTarea');
                
                        
        $objJson = $this->getDoctrine()
        ->getManager("telconet_soporte")
        ->getRepository('schemaBundle:InfoDetalle')
        ->generarJsonTareasXCaso($id,$emInfraestructura, $session,$emComercial,$agregarTareas);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-48")
    */ 
    public function getMaterialesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");  
        $prefijoEmpresa = ($peticion->getSession()->get('prefijoEmpresa') ? $peticion->getSession()->get('prefijoEmpresa') : ""); 
       
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
		
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
		
        $objJson = $this->getDoctrine()
			->getManager("telconet_soporte")
			->getRepository('schemaBundle:InfoCaso')
			->generarJsonMateriales($em_naf, $codEmpresa, $prefijoEmpresa);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
        
		
    public function getMaterialesByTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");   
        $prefijoEmpresa = ($peticion->getSession()->get('prefijoEmpresa') ? $peticion->getSession()->get('prefijoEmpresa') : "");   
        $id_detalle = $peticion->query->get('id_detalle');   
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
		
        $objJson = $this->getDoctrine()
			->getManager("telconet_soporte")
			->getRepository('schemaBundle:InfoCaso')
			->generarJsonMaterialesByTarea($em, $em_naf, $start, $limit, $id_detalle, $codEmpresa,$prefijoEmpresa);
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
	
    /**
    * @Secure(roles="ROLE_78-41")
    */ 
    public function getClientesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('query');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");        
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
            ->findClientesXEmpresa($nombre, $codEmpresa, $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-159")
    */ 
    public function getSegmentosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('query');        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiJurisdiccion')
            ->findJurisdiccionXEmpresa($nombre, $codEmpresa, $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
       
    /**
    * @Secure(roles="ROLE_78-160")
    */  
    public function getProductosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('query');        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiProducto')
            ->findProductoXEmpresa($nombre, $codEmpresa, $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * actualizarHipotesisAction - Funcion que ingresa las hipotesis y asigna el caso al responsable que se selecciona
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 05-07-2016 Se valida que si ingresan caracteres de apertura y cierre de tags en la observacion, se eliminan
    * 
    * @author Jesús Bozada <jbozada@telconet.ec>
    * @version 1.2 17-01-2020 Se agrega departamento de TÉCNICO SUCURSAL en validación de solicitudes de 
    *                         migración MD de servicios asociados al caso.
    *
    * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
    * @version 1.3 11-02-2021 Se Parametriza los departamentos que pueden cambiar el estado de la SOLICITUD DE MIGRACION.
    *
    * @since 1.1
    *
    * @author Andrés Montero Holguin <amontero@telconet.ec>
    * @version 1.3 10-02-2021 - Se agrega programación para llamar a la función soporteService.replicarTareaAGestionPendientes para poder replicar
    *                           casos a la tabla INFO_ASIGNACION_SOLICITUD.
    *
    * @author Fernando López <filopez@telconet.ec>
    * @version 1.4 30-12-2021 - Se agrega login de cliente seleccionado al crear un caso, para poder realizar validacion de pendientes de noc
    *
    * @version 1.0
    *
    * @return array $respuesta
    *
    * @Secure(roles="ROLE_78-31")
    */ 
    public function actualizarHipotesisAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $session    = $peticion->getSession();
        $empresaCod = $session->get('idEmpresa');
        $id_caso    = $peticion->get('id_caso');

        $json = json_decode($peticion->get('hipotesis'));
        
        $array = $json->hipotesis;

        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');

        $em->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();                
         
        $strClienteLogin = "";

        try
        {
            //Departamento y empleado al cual se le asigna el caso
            $departamento = null;
            $empleado     = null;
            $personaRol   = null;
            $motivo       = '';
            $observacionCaso = "";
            /* @var $soporteService SoporteService */
            $soporteService  = $this->get('soporte.SoporteService');
            //Se verifica que el asignado del caso sea operativo y luego que si ese cliente en sesion tiene una
            //solocitud en estado Pendiente por proceso de migracion
            
            $clienteSesion = $session->get('ptoCliente');            
            
            if($clienteSesion)
            {
                $strClienteLogin = $clienteSesion['login'];

                $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->getIdsServicioPorIdPunto($clienteSesion['id']);
                
                if($servicio)
                {
                    $objTipoSolicitudMigra = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD MIGRACION'));
                    
                    if($objTipoSolicitudMigra)
                    {

                        $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->findOneBy(array('servicioId'     =>$servicio,
                                                                             'tipoSolicitudId'=>$objTipoSolicitudMigra->getId(),
                                                                             'estado'=>'Pendiente'));
                        if($objDetalleSolicitud)
                        {
                            //Se obtiene el departamento que se OPERATIVOS - TELCONET
                            $departamento = $array[0]->departamento_asignacionCaso;
                            
                            //Se obtiene los departamentos a cambiar estado AsignadoTarea
                            $arrayDepartamentoAsignado = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('CAMBIO_ESTADO_SOLICITUD_MIGRACION',
                                                                        'SOPORTE',
                                                                        '', 
                                                                        'DEPARTAMENTO_ASIGNADO',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '18');
                            $arrayValorDep = null;
                            foreach($arrayDepartamentoAsignado as $intKey =>  $arrayDepartamento)
                            {
                                $arrayValorDep[$intKey]=$arrayDepartamento['valor1'];
                            }

                            //se agrega id de departamento utilizado en las migraciones de servicios de provincias
                            if($departamento && is_array($arrayValorDep) && 
                                in_array($departamento, $arrayValorDep))
                            {
                                $objDetalleSolicitud->setEstado('AsignadoTarea');
                                $emComercial->persist($objDetalleSolicitud);
                                $emComercial->flush();

                                $objDetalleSolHistorial = new InfoDetalleSolHist();
                                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetalleSolHistorial->setEstado("AsignadoTarea");
                                $objDetalleSolHistorial->setObservacion("Solicitud en estado asignado Tarea por asignacion de CASO de migracion");
                                $objDetalleSolHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolHistorial->setIpCreacion($peticion->getClientIp());
                                $objDetalleSolHistorial->setMotivoId($objDetalleSolicitud->getMotivoId());
                                $emComercial->persist($objDetalleSolHistorial);
                                $emComercial->flush();

                            }
                        }
                    
                    }
                }
            }                  
            
            foreach($array as $sh)
            {
                //Se setea el ultimo asignado para actualizar las demas hipotesis con la misma asignacion
                if($sh->origen == 'Nuevo')
                {
                    if(isset($sh->empleado_asignacionCaso))
                    {
                        $departamento = $this->getDoctrine()
                            ->getManager("telconet_general")
                            ->getRepository('schemaBundle:AdmiDepartamento')
                            ->find($sh->departamento_asignacionCaso);
                    }

                    if(isset($sh->empleado_asignacionCaso))
                    {
                        $empleado = $this->getDoctrine()
                            ->getManager("telconet")
                            ->getRepository('schemaBundle:InfoPersona')
                            ->find($sh->empleado_asignacionCaso);
                    }

                    if(isset($sh->personaEmpresaRol_asignacionCaso))
                    {
                        $personaRol = $sh->personaEmpresaRol_asignacionCaso;
                    }

                    if(isset($sh->observacion_asignacionCaso))
                    {
                        $motivo = $sh->observacion_asignacionCaso;
                    }
                }
            }

            //Se recorren todas las hipotesis para realizar las diferentes afectacion de los procesos de asignacion
            //cuando estas cambien
            foreach($array as $sh)
            {
                $caso = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);
                $infoDetalleHipotesis = false;

                $hip = $em->getRepository('schemaBundle:AdmiHipotesis')->findByNombreHipotesis($sh->nombre_hipotesis);

                if (!is_object($hip[0])) 
                {
                    throw new \Exception('Hipotesis no encontrada, por favor validar o autenticarse nuevamente');
                }

                $detHip = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                    ->findBy(array('casoId' => $id_caso, 'hipotesisId' => $hip[0]->getId()));

                //En caso de que exista el detalle hipotesis quiere decir ya existe hipotesis ingresadas
                if($detHip)
                {
                    $esNuevo = false;
                    $cambiaDeAsignacion = false;

                    $infoCasoAsig = $em->getRepository('schemaBundle:InfoCasoAsignacion')->findByDetalleHipotesisId($detHip[0]->getId());

                    foreach($infoCasoAsig as $info):

                        if($info->getAsignadoId() != $sh->departamento_asignacionCaso)
                        {
                            $cambiaDeAsignacion = true; //Existe detalle hipotesis pero es asignado a un nuevo departamento
                            break;
                        }

                    endforeach;
                }
                else
                {
                    //La asignacion es nueva (cuando recien se abre un caso)
                    $cambiaDeAsignacion = true;
                    $esNuevo            = true;
                }

                //se obtiene los sintoma con los que el caso es creado
                $sintomasGrabados = $em->getRepository('schemaBundle:InfoDetalleHipotesis')->getSintomasByCaso($id_caso);

                if($sintomasGrabados && count($sintomasGrabados) > 0) //Todos los sintomas que se tienen grabados en el caso
                {
                    $numeroSintomas = 0; //Se cuentan todos los sintomas

                    foreach($sintomasGrabados as $sintomaRe)
                    {
                        $sintoma = $em->getRepository('schemaBundle:AdmiSintoma')->find($sintomaRe["id"]);
                        $detalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                            ->findBy(array('casoId' => $id_caso,
                            'sintomaId' => $sintoma->getId(),
                            'hipotesisId' => null));

                        //Significa que es una HIPOTESIS NUEVA o existente
                        if($sh->origen != "" && $esNuevo)
                        {
                            if(count($detalleHipotesis) > 0)
                            {
                                $hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findByNombreHipotesis($sh->nombre_hipotesis);
                                $infoDetalleHipotesis = $detalleHipotesis[0];

                                $infoDetalleHipotesis->setHipotesisId($hipotesis[0]);
                                $infoDetalleHipotesis->setEstado("Modificado");
                                $infoDetalleHipotesis->setObservacion("Actualizacion de Hipotesis");
                                $em->persist($infoDetalleHipotesis);
                                $em->flush();

                                $infoDetalle = $em->getRepository('schemaBundle:InfoDetalle')
                                    ->findOneByDetalleHipotesisId($infoDetalleHipotesis->getId());
                            }
                            else
                            {

                                $hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findByNombreHipotesis($sh->nombre_hipotesis);

                                $infoDetalleHipotesis = new InfoDetalleHipotesis();
                                $infoDetalleHipotesis->setCasoId($caso);
                                $infoDetalleHipotesis->setSintomaId($sintoma);
                                $infoDetalleHipotesis->setHipotesisId($hipotesis[0]);
                                $infoDetalleHipotesis->setEstado("Creado");
                                $infoDetalleHipotesis->setObservacion("Actualizacion de Hipotesis");
                                $infoDetalleHipotesis->setFeCreacion(new \DateTime('now'));
                                $infoDetalleHipotesis->setUsrCreacion($peticion->getSession()->get('user'));
                                $infoDetalleHipotesis->setIpCreacion($peticion->getClientIp());
                                $em->persist($infoDetalleHipotesis);
                                $em->flush();
                            }
                        }

                        //Si la hipotesis es nueva ó esta cambia de asignacion
                        if($esNuevo || $cambiaDeAsignacion)
                        {
                            $historial = new InfoCasoHistorial();
                            $historial->setCasoId($caso);
                            $historial->setObservacion("Asignacion del caso");
                            $historial->setEstado("Asignado");
                            $historial->setFeCreacion(new \DateTime('now'));
                            $historial->setUsrCreacion($peticion->getSession()->get('user'));
                            $historial->setIpCreacion($peticion->getClientIp());
                            $em->persist($historial);
                            $em->flush();

                            /*

                              SE DETERMINA SI LA ASIGNACION ES NUEVA SE CREA SINO SE ACTUALIZA LA YA EXISTENTE SOBRE UNA HIPOTESIS

                             */

                            if(!$detHip)
                            {
                                $asignacion = new InfoCasoAsignacion();
                            }
                            else
                            {
                                $asignacion = $em->getRepository('schemaBundle:InfoCasoAsignacion')
                                        ->findByDetalleHipotesisId($detHip[$numeroSintomas]->getId())[0];
                            }

                            if($detHip)
                            {

                                $infoDetalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                                                           ->find($detHip[$numeroSintomas]->getId());

                                $asignacion->setDetalleHipotesisId($infoDetalleHipotesis);
                            }
                            else
                            {
                                $asignacion->setDetalleHipotesisId($infoDetalleHipotesis);
                            }

                            $infoDetalle = new InfoDetalle();
                            $infoDetalle->setDetalleHipotesisId($infoDetalleHipotesis->getId());
                            $infoDetalle->setPesoPresupuestado(0);
                            $infoDetalle->setValorPresupuestado(0);
                            $infoDetalle->setFeCreacion(new \DateTime('now'));
                            $infoDetalle->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoDetalle->setIpCreacion($peticion->getClientIp());
                            $em->persist($infoDetalle);
                            $em->flush();

                            if($departamento)
                            {
                                if ($departamento->getId() == null)
                                {
                                    throw new \Exception('Id de departamento no encontrado, por favor validar o autenticarse nuevamente');
                                }

                                $asignacion->setAsignadoId($departamento->getId());
                                $asignacion->setAsignadoNombre($departamento->getNombreDepartamento());
                            }

                            if($empleado)
                            {
                                $asignacion->setRefAsignadoId($empleado->getId());
                                $asignacion->setRefAsignadoNombre($empleado->getNombres() . ' ' . $empleado->getApellidos());
                            }
                            
                            if($personaRol)
                            {                                
                                $asignacion->setPersonaEmpresaRolId($personaRol);
                            }

                            //Se eliminan simbolos de tags
                            $observacionCaso = $soporteService->eliminarSimbolosDeTags($sh->observacion_asignacionCaso);

                            $asignacion->setMotivo($observacionCaso);
                            $asignacion->setUsrCreacion($peticion->getSession()->get('user'));
                            $asignacion->setFeCreacion(new \DateTime('now'));
                            $asignacion->setIpCreacion($peticion->getClientIp());
                            $em->persist($asignacion);
                            $em->flush();

                            $numeroSintomas++;


                            //************************************************************************************
                            //************************ ENVIO MAILS Y COMUNICACION ********************************
                            //************************************************************************************                                                      

                            $clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                                ->findOneByNombreClaseDocumento("Notificacion");

                            if($sh->asunto_asignacionCaso == "")
                            {
                                $sh->asunto_asignacionCaso = "Asignacion de Caso : " . $caso->getNumeroCaso();
                            }

                            $infoDocumento = new InfoDocumento();
                            $infoDocumento->setClaseDocumentoId($clase);
                            $infoDocumento->setMensaje($sh->asunto_asignacionCaso);
                            $infoDocumento->setEstado('Activo');
                            $infoDocumento->setNombreDocumento($sh->asunto_asignacionCaso);
                            $infoDocumento->setFeCreacion(new \DateTime('now'));
                            $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoDocumento->setIpCreacion($peticion->getClientIp());
                            $infoDocumento->setEmpresaCod($empresaCod);
                            $emComunicacion->persist($infoDocumento);
                            $emComunicacion->flush();

                            $infoComunicacion = new InfoComunicacion();
                            $infoComunicacion->setCasoId($id_caso);
                            $infoComunicacion->setDetalleId($infoDetalle->getId());
                            $infoComunicacion->setFormaContactoId(5);
                            $infoComunicacion->setClaseComunicacion("Enviado");
                            $infoComunicacion->setFechaComunicacion(new \DateTime('now'));
                            $infoComunicacion->setFeCreacion(new \DateTime('now'));
                            $infoComunicacion->setEstado('Activo');
                            $infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoComunicacion->setIpCreacion($peticion->getClientIp());
                            $infoComunicacion->setEmpresaCod($empresaCod);
                            $emComunicacion->persist($infoComunicacion);
                            $emComunicacion->flush();

                            $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
                            $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
                            $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
                            $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                            $infoDocumentoComunicacion->setEstado('Activo');
                            $infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
                            $emComunicacion->persist($infoDocumentoComunicacion);
                            $emComunicacion->flush();

                            if($sh->empleado_asignacionCaso)
                            {
                                $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                    ->findOneBy(array('personaId' => $empleado->getId(), 'formaContactoId' => 5, 'estado' => "Activo"));
 									
                                if($infoPersonaFormaContacto)
                                {
                                    $to[] = $infoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                                }
                            }

                            /*
                              OBTENCION DEL CANTON DEL ENCARGADO DE LA TAREA
                             */

                            $empresa = '';
                            $departamentoId = '';
                            $cantonId = '';

                            if($departamento)
                            {
                                $empresa = $departamento->getEmpresaCod();
                                $departamentoId = $departamento->getId();
                            }


                            $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($asignacion->getPersonaEmpresaRolId());

                            if($infoPersonaEmpresaRol)
                            {
                                $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                       ->find($infoPersonaEmpresaRol->getOficinaId()->getId());
                                $cantonId = $oficina->getCantonId();
                            }

                            /*******************************************************************

                              USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS

                             * ********************************************************************* */

                            /* @var $envioPlantilla EnvioPlantilla */
                            $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                            
                            //Obtencion de los afectados por caso agrupados
                            $arrayAfectados = $soporteService->getAfectacionDetalladaPorCaso($caso->getId());

                            $parametros = array('caso'            => $caso,
                                                'afectadoPadre'   => $arrayAfectados['afectadosPadre'],
                                                'afectadoDetalle' => $arrayAfectados['afectadosDetalle'], 
                                                'tieneDetalle'    => $arrayAfectados['tieneDetalle'], 
                                                'asignacion'      => $asignacion,
                                                'empleadoLogeado' => $peticion->getSession()->get('empleado'),
                                                'empresa'         => $peticion->getSession()->get('prefijoEmpresa')
                            );

                            $envioPlantilla->generarEnvioPlantilla($sh->asunto_asignacionCaso, 
                                                                   $to, 
                                                                   'CASOASIG', 
                                                                   $parametros, 
                                                                   $empresa, 
                                                                   $cantonId, 
                                                                   $departamentoId);
                        }

                        //Hipotesis no tiene cambio pero en caso de que suceda cambio en alguna otra hipotesis de asignacion
                        //la misma se reflejará en las demás hipotesis existentes
                        else
                        {
                            //Se obtiene la asignacion actual de la hipotesis cuando existe siempre
                            $asignacion = $em->getRepository('schemaBundle:InfoCasoAsignacion')
                                             ->findByDetalleHipotesisId($detHip[$numeroSintomas]->getId())[0];

                            if($asignacion)
                            {
                                if($departamento)
                                {
                                    $asignacion->setAsignadoId($departamento->getId());
                                    $asignacion->setAsignadoNombre($departamento->getNombreDepartamento());
                                }

                                if($empleado)
                                {
                                    $asignacion->setRefAsignadoId($empleado->getId());
                                    $asignacion->setRefAsignadoNombre($empleado->getNombres() . ' ' . $empleado->getApellidos());
                                }
                                
                                if($personaRol)
                                {
                                    $asignacion->setPersonaEmpresaRolId($personaRol);
                                }

                                $asignacion->setUsrCreacion($peticion->getSession()->get('user'));
                                $asignacion->setFeCreacion(new \DateTime('now'));
                                $asignacion->setIpCreacion($peticion->getClientIp());
                                $em->persist($asignacion);
                                $em->flush();
                            }
                        }
                    }//fin foreach
                }//if sintomas					                    

                if(!is_object($oficina))
                {
                    throw new \Exception('Oficina no encontrada, por favor validar o autenticarse nuevamente');
                }

                if(!is_object($departamento))
                {
                    throw new \Exception('Departamento no encontrado, por favor validar o autenticarse nuevamente');
                }

                $arrayParametrosAsig['intDepartamentoId']  = $departamento->getId();
                $arrayParametrosAsig['strTipoAtencion']    = 'CASO';
                $arrayParametrosAsig['strLogin']           = $strClienteLogin;
                $arrayParametrosAsig['strTipoProblema']    = $caso->getTipoCasoId()->getNombreTipoCaso();
                $arrayParametrosAsig['strNombreReporta']   = "";
                $arrayParametrosAsig['strNombreSitio']     = "";
                $arrayParametrosAsig['strCriticidad']      = "Alta";
                $arrayParametrosAsig['strAgente']          = $empleado->getLogin();
                $arrayParametrosAsig['strDetalle']         = $caso->getVersionIni();
                $arrayParametrosAsig['strNumero']          = $caso->getNumeroCaso();
                $arrayParametrosAsig['idEmpresa']          = $empresa;
                $arrayParametrosAsig['strUsrCreacion']     = $peticion->getSession()->get('user');
                $arrayParametrosAsig['intOficinaId']       = $oficina->getId();
                $arrayParametrosAsig['strIpCreacion']      = $peticion->getClientIp();
                $arrayParametrosAsig['arrayAsigProact']    = "";
                $arrayParametrosAsig['intTipoCasoId']      = $caso->getTipoCasoId()->getId();
                $arrayParametrosAsig['intFormaContactoId'] = $caso->getTipoNotificacionId();
                $arrayParametrosAsig['intReferenciaId']    = $caso->getId();
                $arrayParametrosAsig['arrayAfectados']     = $arrayAfectados;

                $arrayParametrosGestionPend[] = $arrayParametrosAsig;

            }//fin foreach global de las hipotesis

            $em->getConnection()->commit();
            $emComercial->getConnection()->commit();
            $emComunicacion->getConnection()->commit();
            $emInfraestructura->getConnection()->commit();

            $resultado = json_encode(array('success' => true));

            foreach($arrayParametrosGestionPend as $arrayParamGestPend)
            {
                $soporteService->replicarTareaAGestionPendientes($arrayParamGestPend);
            }
        }
        catch(Exception $e)
        {

            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();

            $resultado = json_encode(array('success' => false, 'mensaje' => $e));
        }

        $respuesta->setContent($resultado);

        return $respuesta;
    }

    /**
    * Documentación de la funcion 'actualizarSintomasAction'.
    *
    * Método que actualiza los sintomas de un caso
    *
    * @return Response retorna el resultado de la operación
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 28-10-2016  Se realizan ajustes por mejoras en la funcion getClientesXInterfacesId del infoServicioRepository
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 17-12-2015 Se realizan ajustes por motivo del nuevo panel de Movilizacion
    * 
    * @version 1.0
    *
    * @Secure(roles="ROLE_78-32")
    */
    public function actualizarSintomasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');

        $id_caso = $peticion->get('id_caso');
        //$json_sintomas = $peticion->query->get('sintomas');

        $json = json_decode($peticion->get('sintomas'));
        $array = $json->sintomas;
        
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');
		
        $em->getConnection()->beginTransaction();        
        $emInfraestructura->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
		
        try {
            foreach($array as $sintoma)
            {
				$idSintoma = $sintoma->id_sintoma;
				if(is_int($sintoma->id_sintoma))
				{
					$idSintoma = $sintoma->id_sintoma;
				}
				else
				{
					$entitySintoma = $em->getRepository('schemaBundle:AdmiSintoma')->findOneByNombreSintoma($sintoma->nombre_sintoma);
					$idSintoma = $entitySintoma->getId();
				}
				
                $sintomas = $em->getRepository('schemaBundle:AdmiSintoma')->findOneById($idSintoma);
                $caso = $em->getRepository('schemaBundle:InfoCaso')->findOneById($id_caso);
				$detalles = $em->getRepository('schemaBundle:InfoDetalleHipotesis')->getOneDetalleByCasoSintoma($id_caso, $idSintoma);
					
				if(count($detalles)>0){
					$infoDetalleHipotesis = $detalles;
					$infoDetalle = $em->getRepository('schemaBundle:InfoDetalle')->findOneByDetalleHipotesisId($infoDetalleHipotesis->getId());
					$afectadosViejos = $em->getRepository('schemaBundle:InfoParteAfectada')->findByDetalleId($infoDetalle->getId());
					if($afectadosViejos && count($afectadosViejos)>0)
					{
						foreach($afectadosViejos as $key => $entityAfectado)
						{	
							$em->remove($entityAfectado);    
							$em->flush();  
						}
					}
					
					$borrarCriterios = $em->getRepository('schemaBundle:InfoCriterioAfectado')->deleteCriteriosByDetalle($infoDetalle->getId());	
				}		
				else
				{
					$infoDetalleHipotesis = new InfoDetalleHipotesis();
					$infoDetalleHipotesis->setCasoId($caso);
					$infoDetalleHipotesis->setSintomaId($sintomas);
					$infoDetalleHipotesis->setEstado("Creado");
					$infoDetalleHipotesis->setObservacion("Actualizacion de Sintomas");
					$infoDetalleHipotesis->setFeCreacion(new \DateTime('now'));
					$infoDetalleHipotesis->setUsrCreacion($peticion->getSession()->get('user'));
					$infoDetalleHipotesis->setIpCreacion($peticion->getClientIp());
					
					$em->persist($infoDetalleHipotesis);
					$em->flush();
					
					$infoDetalle = new InfoDetalle();
					$infoDetalle->setDetalleHipotesisId($infoDetalleHipotesis->getId());
					$infoDetalle->setPesoPresupuestado(0);
					$infoDetalle->setValorPresupuestado(0);
					$infoDetalle->setFeCreacion(new \DateTime('now'));
					$infoDetalle->setUsrCreacion($peticion->getSession()->get('user'));
					$infoDetalle->setIpCreacion($peticion->getClientIp());

					$em->persist($infoDetalle);
					$em->flush();
				}
	
				$cliente_sesion = $peticion->getSession()->get('cliente'); 
				$ptoCliente_sesion = $peticion->getSession()->get('ptoCliente');  
				$existeCriterio = false; $existeCriterioAfectado = false;
				if($ptoCliente_sesion)
				{
					$puntoId = $ptoCliente_sesion['id'];
					$puntoLogin = $ptoCliente_sesion['login'];
					$clienteId = $cliente_sesion['id'];
					$clienteNombre = ($cliente_sesion['razon_social'] ? $cliente_sesion['razon_social'] : $cliente_sesion['nombres'] . " " . $cliente_sesion['apellidos']);
					
					
					if(isset($sintoma->criterios_sintoma) && $sintoma->criterios_sintoma!="")
					{
						$json_criterios = json_decode($sintoma->criterios_sintoma);
						if($json_criterios->total>0){
							if(isset($json_criterios->criterios) && $json_criterios->criterios!="")
							{
								$array_criterios = $json_criterios->criterios;
								foreach($array_criterios as $cri)
								{
									if($cri->criterio == "Clientes" && $cri->opcion == "Cliente: " . $clienteNombre . " | OPCION: Punto Cliente")
									{
										$existeCriterio = true;
									}
								}
								
								$json_afectados = json_decode($sintoma->afectados_sintoma);
								$array_afectados = $json_afectados->afectados;
								foreach($array_afectados as $afec)
								{
									if($afec->id_afectado == $puntoId && $afec->nombre_afectado == $puntoLogin && $afec->descripcion_afectado == $clienteNombre)
									{
										$existeCriterioAfectado = true;
									}
								}
							}
						}
					}
					
					if(!$existeCriterio && !$existeCriterioAfectado)
					{
						$criterio = new InfoCriterioAfectado();
						$criterio->setId("1");         
						$criterio->setDetalleId($infoDetalle);
						$criterio->setCriterio("Clientes");
						$criterio->setOpcion("Cliente: " . $clienteNombre . " | OPCION: Punto Cliente");
						$criterio->setFeCreacion(new \DateTime('now'));
						$criterio->setUsrCreacion($peticion->getSession()->get('user'));
						$criterio->setIpCreacion($peticion->getClientIp());
						$em->persist($criterio);
						$em->flush();
													
						$afectado = new InfoParteAfectada();  
						$afectado->setTipoAfectado ("Cliente");
						$afectado->setDetalleId($infoDetalle->getId());
						$afectado->setCriterioAfectadoId($criterio->getId());
						$afectado->setAfectadoId($puntoId);
						$afectado->setFeIniIncidencia($caso->getFeApertura());                        
						$afectado->setAfectadoNombre($puntoLogin);
						$afectado->setAfectadoDescripcion($clienteNombre);
						$afectado->setFeCreacion(new \DateTime('now'));
						$afectado->setUsrCreacion($peticion->getSession()->get('user'));
						$afectado->setIpCreacion($peticion->getClientIp());
						$em->persist($afectado);
						$em->flush();	
					}						
				}
				
				$idCriterioGlobal = (($ptoCliente_sesion && !$existeCriterio && !$existeCriterioAfectado) ? 1 : 0);
						
				if(isset($sintoma->criterios_sintoma) && $sintoma->criterios_sintoma!="")
				{
					$json_criterios = json_decode($sintoma->criterios_sintoma);
					if($json_criterios->total>0){
						if(isset($json_criterios->criterios) && $json_criterios->criterios!="")
						{
							$array_criterios = $json_criterios->criterios;
							foreach($array_criterios as $cri)
							{
								if($ptoCliente_sesion && !$existeCriterio && !$existeCriterioAfectado)
									$idCriterioI = $cri->id_criterio_afectado + 1;  
								else
									$idCriterioI = $cri->id_criterio_afectado; 
								
								$idCriterioGlobal = (($idCriterioGlobal < $idCriterioI) ? $idCriterioI : $idCriterioGlobal);
								
								$criterio = new InfoCriterioAfectado();
								$criterio->setId($idCriterioI); 
								$criterio->setDetalleId($infoDetalle);
								$criterio->setCriterio($cri->criterio);
								$criterio->setOpcion($cri->opcion);
								$criterio->setFeCreacion(new \DateTime('now'));
								$criterio->setUsrCreacion($peticion->getSession()->get('user'));
								$criterio->setIpCreacion($peticion->getClientIp());
								$em->persist($criterio);
								$em->flush();
							}
							
							$ArrayAfectadosElementos = "";
									
							$json_afectados = json_decode($sintoma->afectados_sintoma);
							$array_afectados = $json_afectados->afectados;
							foreach($array_afectados as $afec)
							{
								if($ptoCliente_sesion && !$existeCriterio && !$existeCriterioAfectado)
									$idCriterioJ = ($afec->id_criterio) + 1;  
								else
									$idCriterioJ = ($afec->id_criterio);   
									
								$criterio = $em->getRepository('schemaBundle:InfoCriterioAfectado')->findOneBy(array('id' => $idCriterioJ,'detalleId'=>$infoDetalle->getId()));
								
								$afectado = new InfoParteAfectada();
								
								if(strpos($criterio->getOpcion(), "Punto Cliente")!==false)
									$afectado->setTipoAfectado("Cliente");
								else
									$afectado->setTipoAfectado("Elemento");
											
								$afectado->setDetalleId($infoDetalle->getId());
								$afectado->setCriterioAfectadoId($criterio->getId());
								$afectado->setAfectadoId($afec->id_afectado);
								$afectado->setFeIniIncidencia($caso->getFeApertura());                        
								$afectado->setAfectadoNombre($afec->nombre_afectado);
								$afectado->setAfectadoDescripcionId($afec->id_afectado_descripcion);	
								$afectado->setAfectadoDescripcion($afec->descripcion_afectado);
								$afectado->setFeCreacion(new \DateTime('now'));
								$afectado->setUsrCreacion($peticion->getSession()->get('user'));
								$afectado->setIpCreacion($peticion->getClientIp());
								$em->persist($afectado);
								$em->flush();
								
								if($afectado->getTipoAfectado() == "Elemento")
								{
									$arrayAfecElemento["afectado_id"] = $afectado->getAfectadoId();
									$arrayAfecElemento["afectado_nombre"] = $afectado->getAfectadoNombre();
									$arrayAfecElemento["afectado_descripcion"] = $afectado->getAfectadoDescripcion();
									$arrayAfecElemento["afectado_descripcion_id"] = $afectado->getAfectadoDescripcionId();
									
									$ArrayAfectadosElementos[] = $arrayAfecElemento;											
								}
							}
							
							//************** RONALD *****************  AQUI INGRESAR LOS CLIENTES QUE PERTENECEN A LOS ELEMENTOS....
							if($ArrayAfectadosElementos && count($ArrayAfectadosElementos)>0)
							{	
								$arrayIdsInterfaces = false;
								foreach($ArrayAfectadosElementos as $afectadoElem)
								{
									$afectadoElemetoId = $afectadoElem["afectado_id"] ? $afectadoElem["afectado_id"] : "";
									$afectadoInterfaceId = $afectadoElem["afectado_descripcion_id"] ? $afectadoElem["afectado_descripcion_id"] : "";
									
									$Data_Afectado_TipoElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getTipoElementoXElementoId($afectadoElemetoId);
									$Afectado_TipoElemento = ($Data_Afectado_TipoElemento ? $Data_Afectado_TipoElemento["nombreTipoElemento"] : "");
									
									if($Afectado_TipoElemento == "DSLAM" || $Afectado_TipoElemento == "RADIO")
									{
										if($afectadoInterfaceId && $afectadoInterfaceId!='')
										{
											$arrayIdsInterfaces[] = $afectadoInterfaceId;
										}
										else
										{
											$Data_Interfaces = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getIdsInterfacesXElementoId($afectadoElemetoId);
											if($Data_Interfaces && count($Data_Interfaces)>0)
											{
												foreach($Data_Interfaces as $valueDataInterfaces)
												{
													if($valueDataInterfaces && $valueDataInterfaces["id"])
														$arrayIdsInterfaces[] = $valueDataInterfaces["id"];
												}
											}
										}
									}// fin DSLAM y RADIO											
								}//fin foreach arrayAfectadosElementos
								
                                //Se agrega validacion para que no se consulte los clientes por Interface por el tema de elementos activo fijo
                                //dado que este tipo de elementos no tienen interface
                                if($arrayIdsInterfaces && $arrayIdsInterfaces != "")
                                {
                                    if(count($arrayIdsInterfaces) > 999)
                                    {
                                        $strCadenaInterfaces = "";
                                        foreach($arrayIdsInterfaces as $idInterface)
                                        {
                                            $strCadenaInterfaces .=  $idInterface . ",";
                                        }

                                        $arrayParametros["strCadenaInterfaces"] = $strCadenaInterfaces;

                                        //Se carga la tabla temporal con las interfaces consultadas en el momento
                                        $intProcesoIdTablaTemporal = $emComercial->getRepository("schemaBundle:InfoInterfacesAfectadas")
                                                                                 ->cargaTmpInterfacesAfectadas($arrayParametros);

                                        $arrayParametros["intProcesoIdTablaTemporal"] = $intProcesoIdTablaTemporal;

                                        $Data_ClientesInterfaces = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                                               ->getClientesXInterfacesTmp($arrayParametros);

                                        $arrayParametros["intProcesoId"] = $intProcesoIdTablaTemporal;
                                        //Se elimina la tabla temporal
                                        $strMensajeError = $emComercial->getRepository("schemaBundle:InfoInterfacesAfectadas")
                                                                       ->borraTmpInterfacesAfectadas($arrayParametros);
                                    }
                                    else
                                    {
                                        $Data_ClientesInterfaces = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                                               ->getClientesXInterfacesId($arrayIdsInterfaces);
                                    }

                                    if($Data_ClientesInterfaces && count($Data_ClientesInterfaces)>0)
                                    {
                                        foreach($Data_ClientesInterfaces as $valueClienteInterface)
                                        {
                                            $data_puntoId = $valueClienteInterface['idPunto'];
                                            $data_puntoLogin = $valueClienteInterface['login'];
                                            $data_clienteId = $valueClienteInterface['idPersona'];
                                            $data_clienteNombre = ($valueClienteInterface['razonSocial'] ? $valueClienteInterface['razonSocial'] : $valueClienteInterface['nombres'] . " " . $valueClienteInterface['apellidos']);

                                            $data_existeCriterio = false; $data_existeCriterioAfectado = false;
                                            if(isset($sintoma->criterios_sintoma) && $sintoma->criterios_sintoma!="")
                                            {
                                                $json_criterios = json_decode($sintoma->criterios_sintoma);
                                                if($json_criterios->total>0){
                                                    if(isset($json_criterios->criterios) && $json_criterios->criterios!="")
                                                    {
                                                        $array_criterios = $json_criterios->criterios;
                                                        foreach($array_criterios as $cri)
                                                        {
                                                            if($cri->criterio == "Clientes" && $cri->opcion == "Cliente: " . $data_clienteNombre . " | OPCION: Punto Cliente")
                                                            {
                                                                $data_existeCriterio = true;
                                                            }
                                                        }

                                                        $json_afectados = json_decode($sintoma->afectados_sintoma);
                                                        $array_afectados = $json_afectados->afectados;
                                                        foreach($array_afectados as $afec)
                                                        {
                                                            if($afec->id_afectado == $data_puntoId && $afec->nombre_afectado == $data_puntoLogin && $afec->descripcion_afectado == $data_clienteNombre)
                                                            {
                                                                $data_existeCriterioAfectado = true;
                                                            }
                                                        }
                                                    }
                                                }
                                            }//fin isset criterios sintoma


                                            if($ptoCliente_sesion)
                                            {
                                                $puntoId2 = $ptoCliente_sesion['id'];
                                                $puntoLogin2 = $ptoCliente_sesion['login'];
                                                $clienteId2 = $cliente_sesion['id'];
                                                $clienteNombre2 = ($cliente_sesion['razon_social'] ? $cliente_sesion['razon_social'] : $cliente_sesion['nombres'] . " " . $cliente_sesion['apellidos']);


                                                if($clienteNombre2 == $data_clienteNombre)
                                                {
                                                    $data_existeCriterio = true;
                                                }
                                                if($puntoId2 == $data_puntoId && $puntoLogin2 == $data_puntoLogin && $clienteNombre2 == $data_clienteNombre)
                                                {
                                                    $data_existeCriterioAfectado = true;
                                                }
                                            }//fin ptoclientesession

                                            if(!$data_existeCriterio && !$data_existeCriterioAfectado)
                                            {
                                                $idCriterioGlobal = $idCriterioGlobal + 1;

                                                $criterio = new InfoCriterioAfectado();
                                                $criterio->setId($idCriterioGlobal);
                                                $criterio->setDetalleId($infoDetalle);
                                                $criterio->setCriterio("Clientes");
                                                $criterio->setOpcion("Cliente: " . $data_clienteNombre . " | OPCION: Punto Cliente");
                                                $criterio->setFeCreacion(new \DateTime('now'));
                                                $criterio->setUsrCreacion($peticion->getSession()->get('user'));
                                                $criterio->setIpCreacion($peticion->getClientIp());
                                                $em->persist($criterio);
                                                $em->flush();

                                                $afectado = new InfoParteAfectada();
                                                $afectado->setTipoAfectado ("Cliente");
                                                $afectado->setDetalleId($infoDetalle->getId());
                                                $afectado->setCriterioAfectadoId($criterio->getId());
                                                $afectado->setAfectadoId($data_puntoId);
                                                $afectado->setFeIniIncidencia($caso->getFeApertura());
                                                $afectado->setAfectadoNombre($data_puntoLogin);
                                                $afectado->setAfectadoDescripcion($data_clienteNombre);
                                                $afectado->setFeCreacion(new \DateTime('now'));
                                                $afectado->setUsrCreacion($peticion->getSession()->get('user'));
                                                $afectado->setIpCreacion($peticion->getClientIp());
                                                $em->persist($afectado);
                                                $em->flush();
                                            }//fin if data existe
                                        }
                                    }
                                }
							}//************** RONALD ***************** fin if arrayAfectadosElementos
						
						}//if ultimo de CRITERIOS SINTOMAS
					}//if CRITERIOS JSON >0
				}//if ! isset CRITERIOS JSON
			}//if array SINTOMAS ESCOGIDOS
			
			
			$em->getConnection()->commit();
            $emComercial->getConnection()->commit();
            $emComunicacion->getConnection()->commit();
            $emInfraestructura->getConnection()->commit();
			
			$resultado = json_encode(array('success'=>true));
        }catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
			
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
        
        $respuesta->setContent($resultado);        
        return $respuesta;
    }
    
    /**
      * actualizarTareasAction
      *
      * Funcion que ingresas las tareas de un caso
      * 
      *          
      * @return $respuesta Mensaje de respuesta de la transaccion,
      *
      * @author Modificado: Diego Guamán <deguaman@telconet.ec>
      * @version 3.1 31/03/2023 - Se guarda información de registro de contacto del cliente en los registros de Seguimiento
      *
      * @author Pedro Velez <psvelez@telconet.ec>
      * @version 3.0 15-10-2021 - Se elimina filtro de tareas por dpto Operaciones Urbanas para tracking map
      *     
      * @author Pedro Velez <psvelez@telconet.ec>
      * @version 3.0 19-09-2021 - Se agrega llamado al proceso de envio de tracking hacia megadatos.
      *
      * @author Andrés Montero <amontero@telconet.ec>
      * @version 2.9 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
      *
      * @author Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 2.8 10-06-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
      *
      * @author Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 2.7 13-07-2019 - Se agrega el parámetro strSolicitante para identificar
      *                           quien solicita las sugerencias de hal.
      *
      * @author Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 2.6 12-09-2018 - Se almacena la información del caso al momento de insertar el error para tener
      *                           una mejor precisión en la búsqueda
      *                         - Se agrega el tiempo limite de espera.
      *
      * @author Germán Valenzuela <gvalenzuela@telconet.ec>
      * @version 2.5 27-08-2018 - 1.- Se agrega el nuevo parámetro Atender Antes, para el envío al WS de confirmación Hal.
      *                           2.- Se agrega en el catch del método un error log y almacenos el error en la INFO_ERROR.
      *                           3.- Se almacena el error en las tareas no creadas.
      *
      * Se agrega la empresaCod de la persona en sesión en el arreglo que se envía al crear la tarea HAL (crearTareaCasoSoporte)
      * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
      * @version 2.4 23-07-2018
      *
      * Se modifica la funcion para obtener los parametros necesarios para la reasignacion automatica con el proceso de HAL
      * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
      * @version 2.3 26-03-2018
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 2.2 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 2.1 02-10-2017 - Se realizan ajustes en la creacion de tareas simultaneas, debido a que a partir de la segunda tarea
      *                           no se esta registrando historial
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 2.0 14-09-2017 - Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.9 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
      *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
      *
      * @author Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.8 05-07-2016 Se valida si se ingresan caracteres de apertura y cierre de tags, se eliminan
      *
      * @author Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.7 29-06-2016 Se corrige la forma de obtener el departamento del responsable asignado (si es tipo asignado empleado o cuadrilla),
      *                         cuando es una signacion a una cuadrilla que no tiene lider se setea como responsable al primer integrante que se
      *                         encuentra y se valida que el departamento sea obligatorio para el envio de las notificaciones( solo para TN )
      *
      * @author Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.6 23-06-2016 Se asocia el CANTON_ID en la table INFO_DETALLE_ASIGNACION, para determinar la oficina de que canton crea la tarea
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.5 23-06-2016 Se guarda el estado de la tarea en el seguimiento
      *
      * @author Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.4 20-06-2016 Se agrega condicional provisional para que cuando el tramo venga nulo no se ingrese
      *
      * @author Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.3 24-05-2016 Se agrega el campo DEPARTAMENTO_ID en la tabla INFO_DETALLE_ASIGNACION, para determinar que
      *                         departamento creo la tarea
      *
      * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
      * @version 1.2 20-05-2016 - Se agrega ajuste para que se ingrese la observacion en la tabla INFO_DETALLE, debido que a lo que se
      *                           consultan las tareas en el modulo de Tareas no se visualizan las observaciones
      *
      * @version 1.1 21-10-2015 - Se agregan validaciones a la logica, a razon
      *                           de incluir el nuevo concepto de asignacion 
      *                           de tareas a cuadrillas
      *
      * @version 1.0 Version Inicial
      *
      *
      * @Secure(roles="ROLE_78-33")
      *
      */
    public function actualizarTareasAction()
    {
        set_time_limit(240); //Cuatro minutos de espera

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager('telconet_soporte');        
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emSoporte = $this->getDoctrine()->getManager('telconet_soporte');

        $peticion = $this->get('request');

        $session = $peticion->getSession();
        $strUserSession = $session->get('user');
        $codEmpresa        = $session->get('idEmpresa');
        $intIdDepartamento = $session->get('idDepartamento');
        $prefijoEmpresa    = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $id_caso           = $peticion->get('id_caso');
        $arrayParametrosHist = array();
        $strIpCreacion       = $peticion->getClientIp();
        $serviceUtil         = $this->get('schema.Util');
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;        
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();
        $serviceSoporte = $this->get('soporte.SoporteService');
        $serviceProceso = $this->get('soporte.ProcesoService');
        $json = json_decode($peticion->get('tareas'));
        $array = $json->tareas;

        $em->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        $observacionTarea   = "";
        $strNombreProceso   = "";
        $arrayTareasCreadas = array();

        $arrayTareasCreadasAutom  = array();
        $arrayTareasCreadasManual = array();
        $strEsHal = 'N';
        $intDeptoHalId = 0 ;
        $arrayDepTraking   = array(128);
        $arrayNotificaPush = array();

        try
        {
            /* @var $soporteService SoporteService */
            $soporteService = $this->get('soporte.SoporteService');
            foreach($array as $tarea)
            {
                if ($tarea->tipo_operacion ===  "AUTOMATICA")
                {
                    $strFechaReserva     = $tarea->fechaTiempoVigencia;
                    $objDateFechaReserva = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
                    $objDateNow          = new \DateTime('now');

                    if ($objDateNow < $objDateFechaReserva)
                    {
                        // Parseamos la fecha de ejecucion y hora de ejecucion
                        $arrayFechaEjecucion    = explode("T", $tarea->fechaEjecucion);
                        $arrayHoraHoraEjecucion = explode("T", $tarea->horaEjecucion);
                        $strFechaSolicitada     = $arrayFechaEjecucion[0].' '.($arrayHoraHoraEjecucion[1] ?
                            $arrayHoraHoraEjecucion[1] : $arrayHoraHoraEjecucion[0]);

                        // Obtenemos el objeto de info caso
                        $objInfoCaso = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);

                        // Validaciones para obtener el objeto de info detalle hipotesis
                        if($tarea->id_sintomaTarea != "")
                        {
                            $arrayInfoDetalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                                ->findBy(array ('sintomaId'   => $tarea->id_sintomaTarea,
                                                'hipotesisId' => $tarea->id_hipotesisTarea,
                                                'casoId'      => $id_caso));
                        }
                        else
                        {
                            $arrayInfoDetalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                                ->findBy(array ('hipotesisId' => $tarea->id_hipotesisTarea,
                                                'casoId'      => $id_caso));
                        }

                        // Obtenemos el objeto de info tarea a partir del nombre
                        $arrayAdmiTarea = $em->getRepository('schemaBundle:AdmiTarea')
                            ->findBy(array ('nombreTarea' => $tarea->id_tarea,
                                            'estado'      => 'Activo'));

                        if (!is_object($arrayAdmiTarea[0])) 
                        {
                            throw new \Exception('Tarea no encontrada, por favor validar o autenticarse nuevamente');
                        }

                        //Creamos la tarea para enviar a hal
                        $arrayRespuestaCrearTarea = $serviceSoporte->crearTareaCasoSoporte(array (
                                'objInfoCaso'            => $objInfoCaso,
                                'objDetalleHipotesis'    => $arrayInfoDetalleHipotesis[0],
                                'strNombreProceso'       => $arrayAdmiTarea[0]->getProcesoId()->getNombreProceso(),
                                'strNombreTarea'         => $arrayAdmiTarea[0]->getNombreTarea(),
                                'strFechaHoraSolicitada' => $strFechaSolicitada,
                                'strObservacionTarea'    => 'Hal - Asignación Automatica',
                                'strUserCreacion'        => $peticion->getSession()->get('user'),
                                'strIpCreacion'          => $peticion->getClientIp(),
                                'boolAsignarTarea'       => false,
                                'intFormaContacto'       => 5,
                                'intIdEmpresa'           => $codEmpresa
                        ));

                        error_log('Respuesta de confirmación Hal: '.json_decode($arrayRespuestaCrearTarea));

                        // Validamos si la respuesta fue invalida, caso contrario seguimos con el flujo
                        if (strtoupper($arrayRespuestaCrearTarea['mensaje']) === 'FAIL')
                        {
                            $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                           'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                           'mensaje'   => 'Tarea no creada');

                            error_log('InfoCasoController.actualizarTareasAction.crearTareaCasoSoporte: '.
                                      ' IdCaso: '.$objInfoCaso->getId().
                                      ' NumeroCaso: '.$objInfoCaso->getNumeroCaso().
                                      ' User: '.$strUserSession.
                                      ' IpUser: '.$strIpCreacion.
                                      ' Descripcion: '.$arrayRespuestaCrearTarea['descripcion']);

                            $serviceUtil->insertError('Telcos+',
                                                      'InfoCasoController.actualizarTareasAction',
                                                      'crearTareaCasoSoporte:'.
                                                        ' IdCaso: '.$objInfoCaso->getId().
                                                        ' NumeroCaso: '.$objInfoCaso->getNumeroCaso().
                                                        ' Descripcion: '.$arrayRespuestaCrearTarea['descripcion'],
                                                       $strUserSession,
                                                       $strIpCreacion);
                        }
                        else
                        {
                            // Establecemos la comunicacion con hal
                            $arrayRespuestaAsignaHal = $serviceSoporte->procesoAutomaticoHalAsigna(array (
                                 'intIdDetalle'           => intval($arrayRespuestaCrearTarea['numeroDetalle']),
                                 'intIdComunicacion'      => intval($arrayRespuestaCrearTarea['numeroTarea']),
                                 'intIdPersonaEmpresaRol' => intval($session->get('idPersonaEmpresaRol')) ,
                                 'intIdSugerencia'        => intval($tarea->idSugerencia),
                                 'boolEresHal'            => true,
                                 'strAtenderAntes'        => $tarea->atenderAntes,
                                 'strSolicitante'         => 'NA',
                                 'strUrl'                 => $this->container->getParameter('ws_hal_confirmaAsignacionAutHal')
                            ));

                            // Validamos si la comunicacion o la respuesta de hal fueron invalidas,
                            // caso contrario seguimos con el flujo
                            if (strtoupper($arrayRespuestaAsignaHal['mensaje']) != 'OK' 
                                || strtoupper($arrayRespuestaAsignaHal['result']['respuesta']) != 'OK')
                            {
                                // Eliminar la tarea
                                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                    ->find($arrayRespuestaCrearTarea['numeroTarea']);

                                $objInfoDetalle = $em->getRepository('schemaBundle:InfoDetalle')
                                    ->find($arrayRespuestaCrearTarea['numeroDetalle']);

                                $serviceUtil->insertError('Telcos+',
                                                          'InfoCasoController.actualizarTareasAction',
                                                          'procesoAutomaticoHalAsigna:'.
                                                           ' IdCaso: '.$objInfoCaso->getId().
                                                           ' NumeroCaso: '.$objInfoCaso->getNumeroCaso().
                                                           ' IdComunicacion: '.$objInfoComunicacion->getId().
                                                           ' IdDetalle: '.$objInfoDetalle->getId().
                                                           ' Descripcion: '.($arrayRespuestaAsignaHal['descripcion'] ?
                                                               $arrayRespuestaAsignaHal['descripcion'] :
                                                               $arrayRespuestaAsignaHal['result']['mensaje']),
                                                           $strUserSession,
                                                           $strIpCreacion);

                                $emComunicacion->remove($objInfoComunicacion);
                                $emComunicacion->flush();
                                $em->remove($objInfoDetalle);
                                $em->flush();

                                $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                               'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                               'mensaje'   => 'Tarea no creada');
                            }
                            else
                            {
                                // Obtenemos el resultado de hal
                                $arrayResult = $arrayRespuestaAsignaHal['result'];

                                $objInfoDetalle = $em->getRepository('schemaBundle:InfoDetalle')
                                    ->find($arrayRespuestaCrearTarea['numeroDetalle']);

                                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                    ->find($arrayRespuestaCrearTarea['numeroTarea']);

                                $objDateFechaSolicitada = date_create(date('Y-m-d H:i',
                                    strtotime($arrayResult['fecha'].' '.$arrayResult['horaIni'])));

                                $objInfoDetalle->setFeSolicitada($objDateFechaSolicitada);
                                $em->persist($objInfoDetalle);
                                $em->flush();

                                // Procedemos con la asignacion de la tarea
                                $arrayRespuestaAsignarTarea = $serviceSoporte->setAsginarResponsableTarea(array (
                                        'strTipoAsignacion'       => $arrayResult['tipoAsignado'],
                                        'intIdAsignadoTarea'      => $arrayResult['idAsignado'],
                                        'objInfoDetalle'          => $objInfoDetalle,
                                        'strMotivoTarea'          => 'Hal - Asignación Automatica',
                                        'strUserCreacion'         => $peticion->getSession()->get('user'),
                                        'strIpCreacion'           => $peticion->getClientIp(),
                                        'intIdEmpresa'            => $codEmpresa,
                                        'strNombreProceso'        => $arrayAdmiTarea[0]->getProcesoId()->getNombreProceso(),
                                        'strNombreTarea'          => $arrayAdmiTarea[0]->getNombreTarea(),
                                        'strUsuarioAsigna'        => "Hal - Proceso Automatico",
                                        'strPrefijoEmpresa'       => $prefijoEmpresa,
                                        'objInfoComunicacion'     => $objInfoComunicacion,
                                        'strTareaRapida'          => 'N',
                                        'strTipoTarea'            => 'C',
                                        'objInfoCaso'             => $objInfoCaso,
                                        'intIdDepartamentoOrigen' => $intIdDepartamento,
                                        'dateFechaSolicitada'     => $objDateFechaSolicitada,
                                        'boolReprogramada'        => $arrayRespuestaCrearTarea['esReprogramada']
                                ));

                                // Si la asignacion de la tarea es invalida, se procede con la eliminacion de la tarea
                                if (strtoupper($arrayRespuestaAsignarTarea['mensaje']) != 'OK')
                                {
                                    // Eliminar la tarea
                                    $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                        ->find($arrayRespuestaCrearTarea['numeroTarea']);

                                    $objInfoDetalle = $em->getRepository('schemaBundle:InfoDetalle')
                                        ->find($arrayRespuestaCrearTarea['numeroDetalle']);

                                    error_log('InfoCasoController.actualizarTareasAction.setAsginarResponsableTarea: '.
                                              ' IdCaso: '.$objInfoCaso->getId().
                                              ' NumeroCaso: '.$objInfoCaso->getNumeroCaso().
                                              ' IdComunicacion: '.$objInfoComunicacion->getId().
                                              ' IdDetalle: '.$objInfoDetalle->getId().
                                              ' User: '.$strUserSession.
                                              ' IpUser: '.$strIpCreacion.
                                              ' Descripcion: '.$arrayRespuestaAsignarTarea['descripcion']);

                                    $serviceUtil->insertError('Telcos+',
                                                              'InfoCasoController.actualizarTareasAction',
                                                              'setAsginarResponsableTarea:'.
                                                                ' IdCaso: '.$objInfoCaso->getId().
                                                                ' NumeroCaso: '.$objInfoCaso->getNumeroCaso().
                                                                ' IdComunicacion: '.$objInfoComunicacion->getId().
                                                                ' IdDetalle: '.$objInfoDetalle->getId().
                                                                ' Descripcion: '.$arrayRespuestaAsignarTarea['descripcion'],
                                                              $strUserSession,
                                                              $strIpCreacion);

                                    $emComunicacion->remove($objInfoComunicacion);
                                    $emComunicacion->flush();
                                    $em->remove($objInfoDetalle);
                                    $em->flush();

                                    $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                                   'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                                   'mensaje'   => 'Tarea no creada');
                                }
                                else
                                {
                                    //Proceso para el cálculo de los tiempos de la tarea
                                    $serviceSoporte->calcularTiempoEstado(array(
                                        'strEstadoActual'    => "Asignada",
                                        'intIdDetalle'       => $objInfoDetalle->getId(),
                                        'strTipoReprograma'  => null,
                                        'intMinutosEmpresa'  => $arrayResult['mEmpresa'],
                                        'intMinutosCliente'  => $arrayResult['mCliente'],
                                        'strUser'            => $strUserSession,
                                        'strIp'              => $strIpCreacion));

                                    $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                                   'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                                   'mensaje'   => 'Tarea creada');
                                    $arrayTareasCreadasAutom[] = $objInfoDetalle->getId();
                                    $strEsHal = 'S';
                                    $intDeptoHalId = $arrayRespuestaAsignarTarea['intDepartamentoId'];
                                }
                            }

                        }
                    }
                    else
                    {
                        $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                       'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                       'mensaje'   => 'Tarea no creada - Tiempo de reserva ha finalizado..!!');
                    }
                }
                else
                {
                    $fecha = explode("T", $tarea->fechaEjecucion);
                    $hora  = explode("T", $tarea->horaEjecucion);

                    $date = date_create(date('Y-m-d H:i', strtotime($fecha[0] . ' ' . $hora[1]))); //Tiempo de Ejecucion
                    //Si la fecha de ejecucion es mayor a la actual
                    if($date > new \DateTime('now'))
                        $reprogramadaInicio = true;
                    else
                        $reprogramadaInicio = false;

                    $caso = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);

                    if($tarea->id_sintomaTarea != "")
                    {
                        $infoDetalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                            ->findBy(array('sintomaId' => $tarea->id_sintomaTarea,
                            'hipotesisId' => $tarea->id_hipotesisTarea,
                            'casoId' => $id_caso));
                    }
                    else
                    {
                        $infoDetalleHipotesis = $em->getRepository('schemaBundle:InfoDetalleHipotesis')
                            ->findBy(array('hipotesisId' => $tarea->id_hipotesisTarea,
                            'casoId' => $id_caso));
                    }
                    $admiTarea = $em->getRepository('schemaBundle:AdmiTarea')->getTareasXNombre($tarea->id_tarea);

                    $infoDetalleTodo = $em->getRepository('schemaBundle:InfoDetalle')
                        ->findByDetalleHipotesisId($infoDetalleHipotesis[0]->getId());

                    if (!is_object($infoDetalleHipotesis[0])) 
                    {
                        throw new \Exception('Detalle de Hipotesis no encontrado, por favor validar o autenticarse nuevamente');
                    }

                    if (!is_object($admiTarea[0])) 
                    {
                        throw new \Exception('Tarea no encontrada, por favor validar o autenticarse nuevamente');
                    }

                    $infoDetalle = new InfoDetalle();
                    $infoDetalle->setDetalleHipotesisId($infoDetalleHipotesis[0]->getId());
                    $infoDetalle->setTareaId($admiTarea[0]);
                    $infoDetalle->setPesoPresupuestado(0);

                    //Se eliminan simbolos de tags
                    $observacionTarea = $soporteService->eliminarSimbolosDeTags($tarea->observacion);

                    $infoDetalle->setObservacion($observacionTarea);
                    $infoDetalle->setValorPresupuestado(0);
                    $infoDetalle->setFeCreacion(new \DateTime('now'));
                    $infoDetalle->setFeSolicitada($date);
                    $infoDetalle->setUsrCreacion($peticion->getSession()->get('user'));
                    $infoDetalle->setIpCreacion($peticion->getClientIp());
                    $em->persist($infoDetalle);
                    $em->flush();

                    //Se agrega esta condicion provisionalmente para que puedan asignar tareas y hasta que se revise la informacion
                    //tecnica migrada
                    if($tarea->idTipo)
                    {
                        if($tarea->nombreTipoElemento == "Tramo")
                        {

                            $InfoDetalleTareaTramo = new InfoDetalleTareaTramo();
                            $InfoDetalleTareaTramo->setDetalleId($infoDetalle);
                            $InfoDetalleTareaTramo->setTramoId($tarea->idTipo);
                            $InfoDetalleTareaTramo->setFeCreacion(new \DateTime('now'));
                            $InfoDetalleTareaTramo->setUsrCreacion($peticion->getSession()->get('user'));
                            $InfoDetalleTareaTramo->setIpCreacion($peticion->getClientIp());

                            $em->persist($InfoDetalleTareaTramo);
                            $em->flush();
                        }
                        else
                        {

                            $InfoDetalleTareaElemento = new InfoDetalleTareaElemento();
                            $InfoDetalleTareaElemento->setDetalleId($infoDetalle);
                            $InfoDetalleTareaElemento->setElementoId($tarea->idTipo);
                            $InfoDetalleTareaElemento->setFeCreacion(new \DateTime('now'));
                            $InfoDetalleTareaElemento->setUsrCreacion($peticion->getSession()->get('user'));
                            $InfoDetalleTareaElemento->setIpCreacion($peticion->getClientIp());

                            $em->persist($InfoDetalleTareaElemento);
                            $em->flush();
                        }
                    }

                    /*
                      OBTIENE EL DEPARTAMENTO AL CUAL EL CASO FUE ASIGNADO EN CASO DE REALIZAR
                      ESTE EVENTO
                     */
                    $infoPersonaEmpresaRol = $this->getDoctrine()->getManager("telconet")
                                                  ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->find($tarea->id_personaEmpresaRol);

                    if($infoPersonaEmpresaRol)
                    {
                        $departamentoId = $infoPersonaEmpresaRol->getDepartamentoId();
                    }
                    else
                    {
                        $departamentoId = $tarea->id_asignado;
                    }
                    $departamento = '';
                    if($tarea->tipo_asignado == "EMPLEADO")
                    {   
                        $departamento = $this->getDoctrine()->getManager("telconet_general")->getRepository('schemaBundle:AdmiDepartamento')
                                                                                            ->find($departamentoId);

                        $strDeparAsignado = $departamento->getNombreDepartamento();
                    }
                    /************************************************************************ */
                    //	      SE REFERENCIA A QUIEN HA SIDO ADISGNADA LA TAREA
                    /************************************************************************ */             
                    $infoDetalleAsignacion = new InfoDetalleAsignacion();

                    $idPersona        = "";
                    $existeIntegrante = "0";
                    $bandera          = 0;
                    $strEmpresaRolId = "";
                    if($tarea->tipo_asignado == "CUADRILLA")
                    {    
                        $cuadrillaTarea = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoCuadrillaTarea')
                                               ->getIntegrantesCuadrilla($tarea->id_asignado);    
                        $entityEmpresaRolId = "";
                        $strDepartamento = "";
                        if(count($cuadrillaTarea) > 0)
                        {      
                            foreach($cuadrillaTarea as $datoCuadrilla)
                            {                          
                                $infoCuadrilla = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoCuadrilla')
                                                                     ->getLiderCuadrilla($datoCuadrilla['idPersona']);

                                if($infoCuadrilla)
                                {
                                    $bandera          = 1;
                                    $existeIntegrante = "1";
                                    $infoDetalleAsignacion->setPersonaEmpresaRolId($infoCuadrilla[0]['personaEmpresaRolId']);
                                    $strEmpresaRolId = $infoCuadrilla[0]['personaEmpresaRolId'];
                                    $idPersona = $datoCuadrilla['idPersona'];
                                    break;
                                }
                            }  

                            if($bandera == 0)
                            {                          
                                foreach($cuadrillaTarea as $datoCuadrilla)
                                {                                                                                                                                              
                                    $intRol = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiCuadrilla')
                                                                  ->getRolJefeCuadrilla(); 


                                    $infoPersonaEmpresaRol = $this->getDoctrine()->getManager("telconet")
                                                                   ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                   ->findOneBy(array('empresaRolId' => $intRol, 
                                                                                     'personaId'    => $datoCuadrilla['idPersona']));  
                                    if($infoPersonaEmpresaRol)
                                    {
                                        $bandera          = 1;
                                        $existeIntegrante = "1";                                    
                                        $infoDetalleAsignacion->setPersonaEmpresaRolId($infoPersonaEmpresaRol->getId());
                                        $strEmpresaRolId = $infoPersonaEmpresaRol->getId();
                                        $idPersona = $datoCuadrilla['idPersona'];
                                        break;
                                    }
                                }                         
                            }
                            //Se setea como responsable de la tarea al primer integrante de la cuadrilla que se encuentre
                            if($existeIntegrante == "0")
                            {
                                $infoDetalleAsignacion->setRefAsignadoId($cuadrillaTarea[0]['idPersona']);
                                $infoDetalleAsignacion->setRefAsignadoNombre($cuadrillaTarea[0]['nombres'] ." ".$cuadrillaTarea[0]['apellidos']);
                                $infoDetalleAsignacion->setPersonaEmpresaRolId($cuadrillaTarea[0]['empresaRolId']);
                                $strEmpresaRolId = $cuadrillaTarea[0]['empresaRolId'];
                            }
                        }

                        if($strEmpresaRolId)
                        {
                            $entityEmpresaRolId = $this->getDoctrine()->getManager("telconet")
                                                       ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($strEmpresaRolId);

                            if($entityEmpresaRolId)
                            {
                                $strDepartamento = $entityEmpresaRolId->getDepartamentoId();

                                $departamento = $this->getDoctrine()->getManager("telconet_general")->getRepository('schemaBundle:AdmiDepartamento')
                                                                                                    ->find($strDepartamento);
                            }
                        }
                    }
                    else
                    {    
                        if(isset($tarea->id_personaEmpresaRol))
                        {
                            $infoDetalleAsignacion->setPersonaEmpresaRolId($tarea->id_personaEmpresaRol);
                        }
                    }
                    $infoDetalleAsignacion->setDetalleId($infoDetalle);


                    if($tarea->tipo_asignado == "CUADRILLA" || $tarea->tipo_asignado == "EMPRESAEXTERNA")
                    { 
                        $infoDetalleAsignacion->setAsignadoNombre($tarea->nombre_asignado);
                        $infoDetalleAsignacion->setAsignadoId($tarea->id_asignado);                      
                    }
                    else
                    {
                        $infoDetalleAsignacion->setAsignadoNombre($departamento->getNombreDepartamento());
                        $infoDetalleAsignacion->setAsignadoId($departamento->getId());                                          
                    }
                    //SE OBTIENE LA PERSONA INVOLUCRADA EN LA ASIGNACION
                    if($tarea->tipo_asignado == "CUADRILLA")
                    {
                        if($existeIntegrante == "1")
                        {
                            $empleadoLider = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                                  ->find($idPersona);   

                            $infoDetalleAsignacion->setRefAsignadoId(($empleadoLider->getId())?$empleadoLider->getId():"");                    
                            $infoDetalleAsignacion->setRefAsignadoNombre(($empleadoLider->__toString())?$empleadoLider->__toString():""); 
                        }
                    } 
                    elseif($tarea->id_refAsignado)
                    {
                        $empleado = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                         ->find($tarea->id_refAsignado);

                        $infoDetalleAsignacion->setRefAsignadoId($empleado->getId());                    
                        $infoDetalleAsignacion->setRefAsignadoNombre($empleado->__toString()); 
                    } 

                    $infoDetalleAsignacion->setMotivo($observacionTarea);
                    $infoDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                    $infoDetalleAsignacion->setUsrCreacion($peticion->getSession()->get('user'));
                    $infoDetalleAsignacion->setIpCreacion($peticion->getClientIp());
                    $infoDetalleAsignacion->setTipoAsignado($tarea->tipo_asignado);
                    $infoDetalleAsignacion->setDepartamentoId($session->get('idDepartamento'));

                    if($session->get('idPersonaEmpresaRol'))
                    {
                        $entityPersonaEmpresaRol  = $this->getDoctrine()->getManager("telconet")->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                  ->find($session->get('idPersonaEmpresaRol'));

                        if($entityPersonaEmpresaRol->getOficinaId())
                        {
                            $infoOficinaGrupo  = $this->getDoctrine()->getManager("telconet")->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                      ->find($entityPersonaEmpresaRol->getOficinaId());
                            if($infoOficinaGrupo->getCantonId())
                            {
                                $infoDetalleAsignacion->setCantonId($infoOficinaGrupo->getCantonId());
                            }
                        }
                    }

                    $em->persist($infoDetalleAsignacion);
                    $em->flush(); 


                    //Se ingresa el historial de la tarea
                    if(is_object($infoDetalle))
                    {
                        $arrayParametrosHist["intDetalleId"] = $infoDetalle->getId();
                    }
                    $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                    $arrayParametrosHist["strAccion"]       = "Asignada";

                    if($tarea->tipo_asignado == "CUADRILLA" || $tarea->tipo_asignado == "EMPRESAEXTERNA")
                    {
                        $arrayParametrosHist["intAsignadoId"] = $tarea->id_asignado;
                    }
                    else
                    {
                        $arrayParametrosHist["intAsignadoId"] = $departamento->getId();          
                    }

                    $arrayParametrosHist["strOpcion"] = "Historial";
                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                                   

                    /************************************************************************************** */
                    //				       AFECTADOS DEL CASO (LOGIN O ELEMENTOS)
                    /************************************************************************************** */
                    $afectados = $objJson = $this->getDoctrine()
                                                ->getManager("telconet_soporte")
                                                ->getRepository('schemaBundle:InfoCaso')
                                                ->getRegistrosAfectadosTotalXCaso($id_caso);

                    /*******************************************************************

                        SE ESTABLECE SEGUIMIENTO DE ESTA TAREA ASIGNADA PARA REGISTRO DE
                        HISTORIAL

                     ******************************************************************* */

                    if($reprogramadaInicio)
                    {   
                        if($tarea->tipo_asignado == "CUADRILLA")
                        {                    
                            $mensaje = "Tarea fue Asignada a la cuadrilla " . $tarea->nombre_asignado . 
                                       " y Reprogramada para el " . date_format($date, 'Y-m-d H:i');
                        }
                        else if($tarea->tipo_asignado == "EMPRESAEXTERNA")
                        {
                            $mensaje = "Tarea fue Asignada a " . $tarea->nombre_asignado .
                                       " y Reprogramada para el " . date_format($date, 'Y-m-d H:i');
                        }
                        else
                        {
                            $mensaje = "Tarea fue Asignada a " . $empleado . " y Reprogramada para el " . date_format($date, 'Y-m-d H:i');                        
                        }
                    }
                    else
                    {
                        if($tarea->tipo_asignado == "CUADRILLA")
                        {
                            $mensaje = "Tarea fue Asignada a la Cuadrilla " . $tarea->nombre_asignado;                                                
                        }
                        else if($tarea->tipo_asignado == "EMPRESAEXTERNA")
                        {
                            $mensaje = "Tarea fue Asignada a " . $tarea->nombre_asignado;
                        }
                        else
                        {
                            $mensaje = "Tarea fue Asignada a " . $empleado;
                        }
                    }

                    //Se ingresa el seguimiento de la tarea
                    $arrayParametrosHist["strObservacion"]  = $mensaje;
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                    $arrayParametrosHist["strOpcion"]       = "Seguimiento";                

                    $soporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                 

                    /************************************************************************************** */
                    //		       GUARDAR INFORMACIÓN DE CONTACTO DEL CLIENTE EN SEGUIMIENTO
                    /************************************************************************************** */
                    if(isset($tarea->existenDatosContactoCliente) && $tarea->existenDatosContactoCliente)
                    {

                        $strContactoNombre = (isset($tarea->nombreClienteARecibir) && $tarea->nombreClienteARecibir!="")
                                                ? $tarea->nombreClienteARecibir : "NA";
                        $strContactoTelefono = (isset($tarea->telefonoClienteARecibir) && $tarea->telefonoClienteARecibir!="")
                                                ? $tarea->telefonoClienteARecibir : "NA";
                        $strContactoCargo = (isset($tarea->cargoClienteARecibir) && $tarea->cargoClienteARecibir!="")
                                                ? $tarea->cargoClienteARecibir : "NA";
                        $strContactoCorreo = (isset($tarea->correoClienteARecibir) && $tarea->correoClienteARecibir!="")
                                                ? $tarea->correoClienteARecibir : "NA";
                        $strContactoConvencional = (isset($tarea->convencionalClienteARecibir) && $tarea->convencionalClienteARecibir!="")
                                                ? $tarea->convencionalClienteARecibir : "NA";
                        
                        $arraySeguimientoRegistroContacto= [];
                        array_push($arraySeguimientoRegistroContacto, (object)[
                            'nombre' => $strContactoNombre,
                            'celular' => $strContactoTelefono,
                            'cargo' => $strContactoCargo,
                            'correo' => $strContactoCorreo,
                            'convencional' => $strContactoConvencional,
                            'estado' => 'Temporal',
                        ]);
                        $arrayParametrosHist["strObservacion"] = $serviceUtil->obtenerHtmlSeguimientoRegistroContactos(
                            json_encode($arraySeguimientoRegistroContacto)
                        );
                        $arrayParametrosHist["strOpcion"] = "Seguimiento";
                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);    
                    }

                    if($tarea->tipo_asignado == "CUADRILLA")
                    {    
                        //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                        $cuadrillaTarea = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoCuadrillaTarea')
                                               ->getIntegrantesCuadrilla($tarea->id_asignado);                                                       

                        foreach($cuadrillaTarea as $datoCuadrilla)
                        {                         

                            $infoCuadrillaTarea = new InfoCuadrillaTarea();
                            $infoCuadrillaTarea->setDetalleId($infoDetalle);   
                            $infoCuadrillaTarea->setCuadrillaId($tarea->id_asignado);  
                            $infoCuadrillaTarea->setPersonaId($datoCuadrilla['idPersona']);                                   
                            $infoCuadrillaTarea->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoCuadrillaTarea->setFeCreacion(new \DateTime('now'));     
                            $infoCuadrillaTarea->setIpCreacion($peticion->getClientIp());             
                            $em->persist($infoCuadrillaTarea); 
                            $em->flush();  
                        }
                        //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                    }                

                    /*                 * ************************************************************************************* */
                    //				SE ESTABLECE LA COMUNICACION
                    /*                 * ************************************************************************************* */

                    $clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneByNombreClaseDocumento("Notificacion");

                    if($tarea->asunto == "")
                    {
                        $tarea->asunto = "Asignacion de Tarea perteneciente al Caso : " . $caso->getNumeroCaso();
                    }

                    $infoDocumento = new InfoDocumento();
                    $infoDocumento->setClaseDocumentoId($clase);

                    if($tarea->tipo_asignado == "CUADRILLA")
                        $infoDocumento->setMensaje("Asignacion de Tarea a la Cuadrilla" . $tarea->nombre_asignado);
                    else if($tarea->tipo_asignado == "EMPRESAEXTERNA")
                        $infoDocumento->setMensaje("Asignacion de Tarea a " . $tarea->nombre_asignado);
                    else                
                        $infoDocumento->setMensaje("Asignacion de Tarea a " . $empleado);


                    $infoDocumento->setEstado('Activo');
                    $infoDocumento->setNombreDocumento($tarea->asunto);
                    $infoDocumento->setFeCreacion(new \DateTime('now'));
                    $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
                    $infoDocumento->setIpCreacion($peticion->getClientIp());
                    $infoDocumento->setEmpresaCod($codEmpresa);
                    $emComunicacion->persist($infoDocumento);
                    $emComunicacion->flush();

                    $infoComunicacion = new InfoComunicacion();
                    $infoComunicacion->setCasoId($id_caso);
                    $infoComunicacion->setDetalleId($infoDetalle->getId());
                    $infoComunicacion->setFormaContactoId(5);
                    $infoComunicacion->setClaseComunicacion("Enviado");
                    $infoComunicacion->setFechaComunicacion(new \DateTime('now'));
                    $infoComunicacion->setFeCreacion(new \DateTime('now'));
                    $infoComunicacion->setEstado('Activo');
                    $infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
                    $infoComunicacion->setIpCreacion($peticion->getClientIp());
                    $infoComunicacion->setEmpresaCod($codEmpresa);
                    $emComunicacion->persist($infoComunicacion);
                    $emComunicacion->flush();

                    $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
                    $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
                    $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
                    $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                    $infoDocumentoComunicacion->setEstado('Activo');
                    $infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
                    $infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
                    $emComunicacion->persist($infoDocumentoComunicacion);
                    $emComunicacion->flush();              

                    /********************************************************************* */
                    //			       CORREO DE PERSONA AFECTADA
                    /********************************************************************* */
                    if($tarea->tipo_asignado == "CUADRILLA")
                    {
                        $personaIdCorreo = $idPersona;
                    }
                    else if($tarea->tipo_asignado == "EMPRESAEXTERNA")
                    {
                        $personaIdCorreo = $tarea->id_asignado;
                    }
                    elseif($tarea->id_refAsignado)
                    {
                        $personaIdCorreo = $empleado->getId();
                    }
                    //Se setea null el array cuando el responsable no tenga una forma de contacto relacionada Activa
                    $to = array();
                    $to[] = null;
                    if($tarea->id_refAsignado || $tarea->tipo_asignado == "CUADRILLA" || $tarea->tipo_asignado == "EMPRESAEXTERNA")
                    {
                        $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                            ->findOneBy(array('personaId' => $personaIdCorreo, 
                                              'formaContactoId' => 5, 
                                              'estado' => "Activo"
                                             )
                                        );

                        if($infoPersonaFormaContacto)
                        {
                            $to[] = $infoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                        }
                    }

                    /*
                      OBTENCION DEL CANTON DEL ENCARGADO DE LA TAREA
                     */
                    $empresa = '';
                    $canton  = '';                

                    if($departamento)
                    {
                            $empresa = $departamento->getEmpresaCod();
                            $departamento = $departamento->getId();                                            
                    }
                    else
                    {
                        $departamento = '';
                    }

                    if($tarea->tipo_asignado == "EMPLEADO" && isset($tarea->id_personaEmpresaRol))
                    {
                        $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->find($tarea->id_personaEmpresaRol);
                    }
                    elseif($tarea->tipo_asignado == "CUADRILLA" && $strEmpresaRolId)
                    {
                        $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                             ->find($strEmpresaRolId);
                    }

                    if($infoPersonaEmpresaRol)
                    {
                        $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                               ->find($infoPersonaEmpresaRol->getOficinaId()->getId());
                        $canton = $oficina->getCantonId();
                    }
                    else
                    {
                        $canton = '';
                    }

                    /********************************************************************

                      USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS

                     * ********************************************************************* */
                    if(is_object($infoDetalle))
                    {
                        $objAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($infoDetalle->getTareaId());
                    }

                    if(is_object($objAdmiTarea))
                    {
                        $strNombreProceso = $objAdmiTarea->getProcesoId()->getNombreProceso();
                        $strNombreTarea   = $objAdmiTarea->getNombreTarea();
                    }

                    $strAsunto = $tarea->asunto . " | PROCESO: ".$strNombreProceso;

                    /* @var $envioPlantilla EnvioPlantilla */
                    $envioPlantilla = $this->get('soporte.EnvioPlantilla');

                    $parametros = array('nombreProceso'  => $strNombreProceso,
                                        'nombreTarea'    => $strNombreTarea,
                                        'caso' => $caso,
                                        'afectados' => $afectados,
                                        'asignacion' => $infoDetalleAsignacion,
                                        'empleadoLogeado' => $peticion->getSession()->get('empleado'),
                                        'empresa' => $peticion->getSession()->get('prefijoEmpresa'),
                                        'detalle' => $infoDetalle
                    );
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

                        $envioPlantilla->generarEnvioPlantilla($strAsunto, $to, 'TAREA', $parametros, $empresa, $canton, $departamento);
                    }
                    
                    $arrayTareasCreadas[] = array ('tarea'     => $tarea->id_tarea,
                                                   'hipotesis' => $tarea->nombre_hipotesisTarea,
                                                   'mensaje'   => 'Tarea creada');
                    $arrayTareasCreadasManual[] = $arrayParametrosHist["intDetalleId"];
                    if($tarea->tipo_asignado == "EMPLEADO")
                    {
                        $strUserAsignado = $empleado->getRazonSocial() !== null ? $empleado->getRazonSocial() 
                                                                                : $empleado->getNombres().' '.$empleado->getApellidos();

                        $arrayDatosSysCloudCenter[] = array ('strNombreTarea'      => $admiTarea[0]->getNombreTarea(),
                                                             'strNombreProceso'    => $admiTarea[0]->getProcesoId()->getNombreProceso(),
                                                             'strObservacion'      => $observacionTarea,
                                                             'strFechaApertura'    => $fecha[0],
                                                             'strHoraApertura'     => $hora[1],
                                                             'strUser'             => $strUserSession,
                                                             'strIpAsigna'         => $strIpCreacion,
                                                             'strUserAsigna'       => $peticion->getSession()->get('empleado'),
                                                             'strDeparAsigna'      => $peticion->getSession()->get('departamento'),
                                                             'strUserAsignado'     => $strUserAsignado,
                                                             'strDeparAsignado'    => $strDeparAsignado,
                                                             'objInfoComunicacion' => $infoComunicacion);
                    }
                }
            }

            $em->getConnection()->commit();
            $emComercial->getConnection()->commit();
            $emComunicacion->getConnection()->commit();
            
            if ($strEsHal == 'S')
            {                    
                $strRespuesta= $serviceSoporte->guardarTareaCaracteristica(array (
                                    'strDescripcionCaracteristica' => 'CODIGO_TRABAJO',
                                    'intComunicacionId'            => $arrayRespuestaCrearTarea['numeroTarea'],
                                    'idDetalle'                    => $arrayRespuestaCrearTarea['numeroDetalle'],
                                    'strUsrCreacion'               => $peticion->getSession()->get('user'),
                                    'strIpCreacion'                => $peticion->getClientIp(),
                                    'strCodigoTrabajo'             => substr(str_shuffle("123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10)
                            ));

                    $strCommand = 'nohup php /home/telcos/app/console Envia:Tracking ';
                    $strCommand = $strCommand . escapeshellarg($strUserSession). ' ';
                    $strCommand = $strCommand . escapeshellarg($strIpCreacion). ' ';
                    $strCommand = $strCommand . '"Tarea Asignada" ';                    
                    $strCommand = $strCommand . escapeshellarg($arrayRespuestaCrearTarea['numeroDetalle']). ' ';

                    $strCommand = $strCommand .'>/dev/null 2>/dev/null &';
                    shell_exec($strCommand);

                    /************************************************************************************** */
                    //		       GUARDAR INFORMACIÓN DE DATOS DE CONTACTO EN SEGUIMIENTO HAL
                    /************************************************************************************** */                   
                    if(isset($tarea->existenDatosContactoCliente) && $tarea->existenDatosContactoCliente)
                    {
                        $arrayParametrosHist["intDetalleId"] = $arrayRespuestaCrearTarea['numeroDetalle']; 
                        $arrayParametrosHist["strEstadoActual"] = "Asignada";
                        $arrayParametrosHist["intAsignadoId"] = $intDeptoHalId;
                        
                        $strContactoNombre = (isset($tarea->nombreClienteARecibir) && $tarea->nombreClienteARecibir!="")
                            ? $tarea->nombreClienteARecibir : "NA";
                        $strContactoTelefono = (isset($tarea->telefonoClienteARecibir) && $tarea->telefonoClienteARecibir!="")
                            ? $tarea->telefonoClienteARecibir : "NA";
                        $strContactoCargo = (isset($tarea->cargoClienteARecibir) && $tarea->cargoClienteARecibir!="")
                            ? $tarea->cargoClienteARecibir : "NA";
                        $strContactoCorreo = (isset($tarea->correoClienteARecibir) && $tarea->correoClienteARecibir!="")
                            ? $tarea->correoClienteARecibir : "NA";
                        $strContactoConvencional = (isset($tarea->convencionalClienteARecibir) && $tarea->convencionalClienteARecibir!="")
                            ? $tarea->convencionalClienteARecibir : "NA";
                        $arraySeguimientoRegistroContacto= [];
                        array_push($arraySeguimientoRegistroContacto, (object)[
                            'nombre' => $strContactoNombre,
                            'celular' => $strContactoTelefono,
                            'cargo' => $strContactoCargo,
                            'correo' => $strContactoCorreo,
                            'convencional' => $strContactoConvencional,
                            'estado' => 'Temporal',
                        ]);
                        $arrayParametrosHist["strObservacion"] = $serviceUtil->obtenerHtmlSeguimientoRegistroContactos(
                            json_encode($arraySeguimientoRegistroContacto)
                        );
                        $arrayParametrosHist["strOpcion"] = "Seguimiento";
                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);    
                    }
                       
            }   

            for($intIndiceTaut=0;$intIndiceTaut <= count($arrayTareasCreadasAutom);$intIndiceTaut++)
            {
                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $arrayTareasCreadasAutom[$intIndiceTaut];
                $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
                $soporteService->crearInfoTarea($arrayParametrosInfoTarea);
            }
            for($intIndiceTman=0;$intIndiceTman <= count($arrayTareasCreadasManual);$intIndiceTman++)
            {
                //Proceso que graba tarea en INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = $arrayTareasCreadasManual[$intIndiceTman];
                $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
                $soporteService->crearInfoTarea($arrayParametrosInfoTarea);
            }
            if (!empty($arrayDatosSysCloudCenter) && count($arrayDatosSysCloudCenter) > 0)
            {
                foreach ($arrayDatosSysCloudCenter as $arrayDatosSysCloud)
                {
                    $serviceProceso->putTareasSysCluod($arrayDatosSysCloud);
                }
            }

            $objInfoCasoNoti = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);
            
            if($objInfoCasoNoti->getTipoCasoId()->getId() == 124 && ($codEmpresa == 18 || $codEmpresa == 33))
            {
                $objInfoMantProgramado = $em->getRepository('schemaBundle:InfoMantenimientoProgramado')
                ->findBy(array('casoId'=>$id_caso)); 
                if(empty($objInfoMantProgramado) && count($objInfoMantProgramado)<=0)
                {
                    $arrayNotificaPush["intCasoId"]      = $id_caso;
                    $arrayNotificaPush["strCodEmpresa"]  = $codEmpresa;
                    $arrayNotificaPush["strTipoProceso"] = "CreacionCasoManual";
                    $arrayNotificaPush["strUserSession"] = $strUserSession;
                    $arrayNotificaPush["strIpCreacion"]  = $strIpCreacion;
    
                    $soporteService->guardaNotificacionPush($arrayNotificaPush);
                } 
            }

            $objResultado = json_encode(array('success' => true , 'mensaje' => $arrayTareasCreadas));
        }
        catch(\Exception $objException)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoCasoController.actualizarTareasAction',
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpCreacion);

            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();        

            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Error al crear la tarea'));
        }

        $objRespuesta->setContent($objResultado);

        return $objRespuesta;
    }    

    /**
     * Función que obtiene los intervalos
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @since 1.0
     * @version 1.0 24-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 02-10-2018 - Se agrega el tiempo limite de espera.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 12-07-2019 - Se agrega el parámetro strSolicitante para identificar
     *                           quien solicita las sugerencias de hal.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 29-11-2019 - Se agrega los parametros intIdDetalleSolicitud y strEsInstalacion para 
     * poder solicitar sugerencias HAL en las acciones de planificación: programar y replanificar.
     * 
     */
    public function getIntervalosHalAction()
    {
        set_time_limit(240); //Cuatro minutos de espera

        $objPeticion       = $this->get('request');
        $strIpCreacion     = $objPeticion->getClientIp();
        $strUserSession    = $objPeticion->getSession()->get('user');
        $intIdDetalle      = $objPeticion->query->get('idDetalle');
        $intIdCaso         = $objPeticion->query->get('idCaso');
        $intIdHipotesis    = $objPeticion->query->get('idHipotesis');
        $strNombreTarea    = $objPeticion->query->get('idAdmiTarea');
        $intNIntentos      = $objPeticion->query->get('nIntentos');
        $strFechaSugerida  = $objPeticion->query->get('fechaSugerida');
        $strHoraSugerida   = $objPeticion->query->get('horaSugerida');
        $intTipoHal        = $objPeticion->query->get('tipoHal');
        $strSolicitante    = $objPeticion->query->get('solicitante');
        $intIdDetSolic     = $objPeticion->query->get('idDetSolicitud');
        $strEsInstalacion  = $objPeticion->query->get('esInstalacion');
        $intIdPersona      = $objPeticion->getSession()->get('idPersonaEmpresaRol');
        $intIdComunicacion = $objPeticion->query->get('idComunicacion');
        $serviceSoporte    = $this->get('soporte.SoporteService');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil       = $this->get('schema.Util');
        $objRespuesta      = new Response();
        $intNOpciones      = 1;
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $arrayIntervalos  = array();

        try
        {
            if ($intTipoHal == 2)
            {
                if ($strFechaSugerida != "")
                {
                    $arrayFechaSugerida = explode("T", $strFechaSugerida);
                    $strFechaSugerida   = $arrayFechaSugerida[0];
                }

                if ($strHoraSugerida != "")
                {
                    $arrayHoraSugerida = explode("T", $strHoraSugerida);
                    $strHoraSugerida   = $arrayHoraSugerida[1];
                }

                $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('PLANIFICACION_SOPORTE_HAL','SOPORTE','','CANTIDAD_OPCIONES_INTERVALOS','','','','','','');

                if (empty($arrayAdmiParametroDet) || count($arrayAdmiParametroDet) < 1)
                {
                    $intNOpciones = 3;
                }
                else
                {
                    $intNOpciones = $arrayAdmiParametroDet['valor1'];
                }
            }

            if (is_null($intIdDetalle))
            {
                $arrayAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->getTareasXNombre($strNombreTarea);
                $intIdTarea     = intval($arrayAdmiTarea[0]->getId());
                $intIdCaso      = intval($intIdCaso);
                $intIdHipotesis = intval($intIdHipotesis);
            }
            else
            {
                $intIdDetalle = intval($intIdDetalle);
            }

            if ($strEsInstalacion === 'S')
            {
                // Array para el envio a hal
                $arrayParametrosHal = array ('intIdDetalleSolicitud'  => intval($intIdDetSolic),
                                             'intIdDetalle'           => $intIdDetalle,
                                             'intIdComunicacion'      => $intIdComunicacion,
                                             'strEsInstalacion'       => $strEsInstalacion,
                                             'intIdPersonaEmpresaRol' => intval($intIdPersona),
                                             'intNOpciones'           => intval($intNOpciones),
                                             'intNIntentos'           => intval($intNIntentos),
                                             'strFechaSugerida'       => $strFechaSugerida,
                                             'strHoraSugerida'        => $strHoraSugerida,
                                             'boolConfirmar'          => false,
                                             'strSolicitante'         => $strSolicitante,
                                             'strUrl'                 => $this->container->getParameter('ws_hal_solicitaSugerenciaInstalacion'));
            }
            else
            {
                // Array para el envio a hal
                $arrayParametrosHal = array ('intIdDetalle'           => $intIdDetalle,
                                            'intIdCaso'              => $intIdCaso,
                                            'intIdHipotesis'         => $intIdHipotesis,
                                            'intIdAdmiTarea'         => $intIdTarea,
                                            'intIdPersonaEmpresaRol' => intval($intIdPersona),
                                            'intNOpciones'           => intval($intNOpciones),
                                            'intNIntentos'           => intval($intNIntentos),
                                            'strFechaSugerida'       => $strFechaSugerida,
                                            'strHoraSugerida'        => $strHoraSugerida,
                                            'boolConfirmar'          => false,
                                            'strSolicitante'         => $strSolicitante,
                                            'strUrl'                 => $this->container->getParameter('ws_hal_solicitaSugerencia'));
            }
            // Establecemos la comunicacion con hal
            $arrayRespuestaHal  = $serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);

            if (strtoupper($arrayRespuestaHal['mensaje']) == 'FAIL')
            {
                $serviceUtil->insertError('Telcos+',
                                           'InfoCasoController.getIntervalosHalAction',
                                           'getSolicitarConfirmarSugerenciasHal: '.$arrayRespuestaHal['descripcion'],
                                           $strUserSession,
                                           $strIpCreacion);
            }
            else
            {
                if ($arrayRespuestaHal['result']['respuesta'] === 'conSugerencias')
                {
                    $strMensajeHal = '<b>'.$arrayRespuestaHal['result']['mensaje'].'</b>';
                    foreach ($arrayRespuestaHal['result']['sugerencias'] as $arrayDatos)
                    {
                        $objDateTiempoVigencia = new \DateTime('now');
                        $objDateTiempoVigencia->modify('+'.$arrayDatos['segTiempoVigencia'].' second');
                        $arrayDatos['fechaVigencia'] = date_format($objDateTiempoVigencia, 'Y-m-d H:i:s');
                        $arrayDatos['horaVigencia']  = date_format($objDateTiempoVigencia, 'H:i:s');
                        $arrayIntervalos[]           = $arrayDatos;
                    }
                }
                elseif ($arrayRespuestaHal['result']['respuesta'] === 'sinSugerencias')
                {
                    $strMensajeHal = '<b style="color:green";>'.$arrayRespuestaHal['result']['mensaje'].'</b>';
                }
                else
                {
                    $strMensajeHal = '<b style="color:red";>'.$arrayRespuestaHal['result']['mensaje'].'</b>';
                }

                $arrayRespuesta['intervalos'] = $arrayIntervalos;
                $arrayRespuesta['mensaje']    = $strMensajeHal;
            }

            $objResultado = json_encode($arrayRespuesta);
        }
        catch(\Exception $objException)
        {
            error_log("Error - InfoCasoController.getIntervalosHalAction -> Detalle: ".$objException->getMessage());
            $serviceUtil->insertError('Telcos+',
                                      'InfoCasoController.getIntervalosHalAction',
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpCreacion);
        }

        $objRespuesta->setContent($objResultado);

        return $objRespuesta;
    }

    /**
     * Función encargada de confirmar la reserva de Hal Sugiere / Cliente Sugiere
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @since 1.0
     * @version 1.0 24-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 02-10-2018 - Se agrega el tiempo limite de espera.
     */
    public function confirmarReservaHalAction()
    {
        set_time_limit(240); //Cuatro minutos de espera

        $objRequest      = $this->get('request');
        $intIdDetalle    = $objRequest->get('idDetalle');
        $intIdCaso       = $objRequest->get('idCaso');
        $intIdHipotesis  = $objRequest->get('idHipotesis');
        $strNombreTarea  = $objRequest->get('idAdmiTarea');
        $intIdSugerencia = $objRequest->get('idSugerencia');
        $strFechaReserva = $objRequest->get('fechaVigencia');
        $objSession      = $objRequest->getSession();
        $intIdPersona    = $objSession->get('idPersonaEmpresaRol');
        $serviceSoporte  = $this->get('soporte.SoporteService');
        $emSoporte       = $this->getDoctrine()->getManager('telconet_soporte');
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $strMensajeError = 'Fallo en la comunicación, por favor intente de nuevo si el problema persiste comunique a sistemas..!!';

        try
        {
            $objDateFechaReserva = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
            $objDateNow          = new \DateTime('now');

            // Validamos el tiempo de reserva
            if ($objDateNow < $objDateFechaReserva)
            {
                if (is_null($intIdDetalle))
                {
                    $arrayAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->getTareasXNombre($strNombreTarea);
                    $intIdTarea     = intval($arrayAdmiTarea[0]->getId());
                    $intIdCaso      = intval($intIdCaso);
                    $intIdHipotesis = intval($intIdHipotesis);
                }
                else
                {
                    $intIdDetalle = intval($intIdDetalle);
                }

                // Parametros para el envio a hal
                $arrayParametrosHal = array ('intIdDetalle'           => $intIdDetalle,
                                             'intIdCaso'              => $intIdCaso,
                                             'intIdHipotesis'         => $intIdHipotesis,
                                             'intIdAdmiTarea'         => $intIdTarea,
                                             'intIdPersonaEmpresaRol' => intval($intIdPersona),
                                             'intIdSugerencia'        => intval($intIdSugerencia),
                                             'boolConfirmar'          => true,
                                             'strUrl'                 => $this->container->getParameter('ws_hal_confirmaSugerencia'));

                // Se establece la comunicacion
                $arrayRespuestaHal  = $serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);

                // Validaciones de la respuesta de hal
                if (strtoupper($arrayRespuestaHal['mensaje']) == 'FAIL')
                {
                    $arrayRespuesta['success'] = false;
                    $arrayRespuesta['mensaje'] = $strMensajeError;
                    error_log("Error en el metodo InfoCasoController.confirmarReservaHalAction -> ".$arrayRespuestaHal['descripcion']);
                }
                elseif (strtoupper($arrayRespuestaHal['result']['respuesta']) == 'FAIL')
                {
                    $arrayRespuesta['success'] = false;
                    $arrayRespuesta['mensaje'] = $arrayRespuestaHal['result']['mensaje'];
                }
                else
                {
                    // A este punto se obtuvo una respuesta positiva de hal
                    $arrayRespuesta['success']             = true;
                    $arrayRespuesta['mensaje']             = ($arrayRespuestaHal['result']['mensaje']
                        ? $arrayRespuestaHal['result']['mensaje'] : 'Hal confirmo la reserva');
                    $arrayRespuesta['segTiempoVigencia']   = $arrayRespuestaHal['result']['segTiempoVigencia'];
                    $objDateNow->modify('+'.$arrayRespuesta['segTiempoVigencia'].' second');
                    $arrayRespuesta['fechaTiempoVigencia'] = date_format($objDateNow, 'Y-m-d H:i:s');
                    $arrayRespuesta['horaTiempoVigencia']  = date_format($objDateNow, 'H:i:s');
                }
            }
            else
            {
                $arrayRespuesta['success']      = false;
                $arrayRespuesta['noDisponible'] = true;
                $arrayRespuesta['mensaje']      = 'El tiempo de reserva para la sugerencia escogida ha culminado..!!';
            }
            $objResultado = json_encode($arrayRespuesta);
        }
        catch(\Exception $objException)
        {
            error_log("Error - InfoCasoController.confirmarReservaHalAction -> Detalle: ".$objException->getMessage());
            $objResultado = json_encode(array ('success' => false,
                                               'mensaje' => $strMensajeError));
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_78-157")
     * 
     * Funcion que sirve para ingresar el seguimiento de una tarea
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 1.0
     * @version 1.1 20-07-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 19-09-2016 Se realizan ajustes para incluir el concepto de ingresar seguimientos internos
     *
     */
    public function ingresarSeguimientoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion        = $this->get('request');
        $session         = $peticion->getSession();
        $idEmpresa       = $session->get('idEmpresa');
        $prefijoEmpresa  = $session->get('prefijoEmpresa');
        $intDepartamento = $session->get('idDepartamento');
        $idCaso          = $peticion->get('id_caso');
        $idDetalle       = $peticion->get('id_detalle');
        $seguimiento     = $peticion->get('seguimiento');
        $strInterno      = $peticion->get('registroInterno')?$peticion->get('registroInterno'):"N";
        $empleado        = $peticion->get('empleado');
        $usrCreacion     = $session->get('user');
        $ipCreacion      = $peticion->getClientIp();
        
        $arrayParametros = array(
                                    'idEmpresa'     => $idEmpresa,
                                    'prefijoEmpresa'=> $prefijoEmpresa,
                                    'departamento'  => $intDepartamento,
                                    'idCaso'        => $idCaso,
                                    'idDetalle'     => $idDetalle,
                                    'seguimiento'   => $seguimiento,
                                    'regInterno'    => $strInterno,
                                    'empleado'      => $empleado,
                                    'usrCreacion'   => $usrCreacion,
                                    'ipCreacion'    => $ipCreacion
                                );
        
        /* @var $serviceSoporte SoporteService */
        $serviceSoporte = $this->get('soporte.SoporteService');
        //---------------------------------------------------------------------*/
        
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
        $respuestaArray = $serviceSoporte->ingresarSeguimientoTarea($arrayParametros);
        //----------------------------------------------------------------------*/
        
        //--------RESPUESTA-----------------------------------------------------*/
        $resultado = json_encode($respuestaArray);
        //----------------------------------------------------------------------*/
        $respuesta->setContent($resultado);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_78-38")
     * 
     * Funcion que sirve para llamar al service de Finalizar Tarea.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 1.0
     * @version 1.1 21-07-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-05-2016 Se agrega el Login Afectado como parametro en la plantilla
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 08-07-2016 Se realiza ajustes para enviar la longitud y latitud cuando se finaliza una tarea de corte de fibra de un tipo de
     *                         caso Backbone
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 09-08-2017 -  Se ontiene el departamento del usuario en session
     *
     */
    public function finalizarTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $codEmpresa         = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
        $intIdDepartamento  = $session->get('idDepartamento') ? $session->get('idDepartamento') : "";
        $idCaso             = $peticion->get('id_caso');
        $esSolucion         = $peticion->get('es_solucion');
        $idDetalle          = $peticion->get('id_detalle');
        $json               = json_decode($peticion->get('materiales'));
        $tiempoTotal        = $peticion->get('tiempo_total');
        $fechaEjecucion     = $peticion->get('tiempo_ejecucion');
        $horaEjecucion      = $peticion->get('hora_ejecucion');
        $tareaFinal         = ($peticion->get('tarea_final') ? $peticion->get('tarea_final') : "");
        $observacion        = $peticion->get('observacion');
        $fechaCierre        = $peticion->get('tiempo_cierre');
        $horaCierre         = $peticion->get('hora_cierre');
        $tarea              = $peticion->get('tarea');
        $fechaApertura      = $peticion->get('fecha_apertura');
        $horaApertura       = $peticion->get('hora_apertura');
        $clientes           = $peticion->get('clientes');
        $usrCreacion        = $peticion->getSession()->get('user');
        $ipCreacion         = $peticion->getClientIp();
        $empleado           = $peticion->getSession()->get('empleado');
        $prefijoEmpresa     = $peticion->getSession()->get('prefijoEmpresa');
        $longitud           = ($peticion->get('longitud') ? $peticion->get('longitud') : "");
        $latitud            = ($peticion->get('latitud') ? $peticion->get('latitud') : "");
        $longitudManga1     = ($peticion->get('longitud') ? $peticion->get('longitudManga1') : "");
        $latitudManga1      = ($peticion->get('latitud') ? $peticion->get('latitudManga1') : "");
        $longitudManga2     = ($peticion->get('longitud') ? $peticion->get('longitudManga2') : "");
        $latitudManga2      = ($peticion->get('latitud') ? $peticion->get('latitudManga2') : "");


        $arrayParametros = array(
                                    'idEmpresa'         => $codEmpresa,
                                    'prefijoEmpresa'    => $prefijoEmpresa,
                                    'idCaso'            => $idCaso,
                                    'idDetalle'         => $idDetalle,
                                    'intIdDepartamento' => $intIdDepartamento,
                                    'tarea'             => $tarea,
                                    'tiempoTotal'       => $tiempoTotal,
                                    'fechaCierre'       => $fechaCierre,
                                    'horaCierre'        => $horaCierre,
                                    'fechaEjecucion'    => $fechaEjecucion,
                                    'horaEjecucion'     => $horaEjecucion,
                                    'esSolucion'        => $esSolucion,
                                    'fechaApertura'     => $fechaApertura,
                                    'horaApertura'      => $horaApertura,
                                    'clientes'          => $clientes,
                                    'tareaFinal'        => $tareaFinal,
                                    'jsonMateriales'    => $json,
                                    'idAsignado'        => "",
                                    'observacion'       => $observacion,
                                    'longitud'          => $longitud,
                                    'latitud'           => $latitud,
                                    'longitudManga1'    => $longitudManga1,
                                    'latitudManga1'     => $latitudManga1,
                                    'longitudManga2'    => $longitudManga2,
                                    'latitudManga2'     => $latitudManga2,
                                    'empleado'          => $empleado,
                                    'usrCreacion'       => $usrCreacion,
                                    'ipCreacion'        => $ipCreacion
                                );
        
        /* @var $ingresarSeguimiento SoporteService */
        $serviceSoporte = $this->get('soporte.SoporteService');
        //---------------------------------------------------------------------*/
        
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
        $respuestaArray = $serviceSoporte->finalizarTarea($arrayParametros);
        
        $resultado = json_encode($respuestaArray);
        //----------------------------------------------------------------------*/
        
        $respuesta->setContent($resultado);
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
    * @Secure(roles="ROLE_78-35")
    */ 
    public function asignarCasoAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        
        $peticion = $this->get('request');
        
        $id_caso = $peticion->get('id_caso');
        $em->getConnection()->beginTransaction();
        
        try {
            $caso = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);

	    /************************************************************/
	    //  	ASIGNAR CASO A DEPARTAMENTO ASIGNADO
	    /************************************************************/
            $asignacion = new InfoCasoAsignacion();
            $asignacion->setCasoId($caso);

            $departamento = $this->getDoctrine()
                            ->getManager("telconet_general")
                            ->getRepository('schemaBundle:AdmiDepartamento')
                            ->find($peticion->get('departamento'));
                            
            $asignacion->setAsignadoId($departamento->getId());
            $asignacion->setAsignadoNombre($departamento->getNombreDepartamento());
            
            if($peticion->get('empleado')){
            
		  $empleado = $this->getDoctrine()
			  ->getManager("telconet")
			  ->getRepository('schemaBundle:InfoPersona')
			  ->find($peticion->get('empleado'));
			  
		  $asignacion->setRefAsignadoId($empleado->getId());
		  $asignacion->setRefAsignadoNombre($empleado->getNombres().' '.$empleado->getApellidos());

            }

            $asignacion->setMotivo($peticion->get('observacion'));
            $asignacion->setUsrCreacion($peticion->getSession()->get('user'));
            $asignacion->setFeCreacion(new \DateTime('now'));
            $asignacion->setIpCreacion($peticion->getClientIp());
            $em->persist($asignacion);
            $em->flush();
            $em->getConnection()->commit();
            
            $resultado = json_encode(array('success'=>true));
            
        }catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
        
        $respuesta->setContent($resultado);
        
        return $respuesta;
        
    }
    
    /**
     * @Secure(roles="ROLE_78-36")
     * 
     * Funcion que sirve para invocar al service para cerrar el caso
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 1.0
     * @version 1.1 22-07-2015
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 25-05-2016 Se realizan ajustes para poder actualizar el tipo de afectacion al cerrar del caso
     * 
     * @author modificado Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.3 28-12-2020 Se envía como parámetro idPersonaEmpresaRol
     */ 
    public function cerrarCasoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $session = $peticion->getSession();

        $codEmpresa             = $session->get('idEmpresa');
        $prefijoEmpresa         = $session->get('prefijoEmpresa');
        $idCaso                 = $peticion->get('id_caso');
        $fechaCierre            = $peticion->get('fe_cierre');
        $horaCierre             = $peticion->get('hora_cierre');
        $tituloFinalHipotesis   = $peticion->get('tituloFinalHipotesis');
        $tipo_afectacion        = $peticion->get('tipo_afectacion');
        $versionFinal           = $peticion->get('versionFinal');
        $tiempoTotalSolucion    = $peticion->get('tiempo_total_caso');
        $usrCreacion            = $peticion->getSession()->get('user');
        $ipCreacion             = $peticion->getClientIp();
        $empleado               = $peticion->getSession()->get('empleado');
        $idEmpleado             = $peticion->getSession()->get('id_empleado');
        $idDepartamento         = $peticion->getSession()->get('idDepartamento');
        $intPersonaEmpresaRol   = $session->get('idPersonaEmpresaRol');

        $strGuardar            = $peticion->get('strGuardar');
        $strIndisponibilidadI  = $peticion->get('strIndisponibilidadI');
        $strTipoI              = $peticion->get('strTipoI');
        $intTiempoAfectacionI  = $peticion->get('intTiempoAfectacionI');
        $strMasivoI            = $peticion->get('strMasivoI');
        $intComboResponsableI  = $peticion->get('intComboResponsableI');
        $intClientesAfectadosI = $peticion->get('intClientesAfectadosI');
        $strObservacionesI     = $peticion->get('strObservacionesI');
        $strOltI               = $peticion->get('strOltI');
        $strPuertoI            = $peticion->get('strPuertoI');
        $strCajaI              = $peticion->get('strCajaI');
        $strSplitterI          = $peticion->get('strSplitterI');
        $intIdHipotesisInicialI = $peticion->get('intIdHipotesisInicialI');

        $arrayParametros = array(
                                    'idEmpresa'             => $codEmpresa,
                                    'prefijoEmpresa'        => $prefijoEmpresa,
                                    'idCaso'                => $idCaso,
                                    'fechaCierre'           => $fechaCierre,
                                    'horaCierre'            => $horaCierre,
                                    'tituloFinalHipotesis'  => $tituloFinalHipotesis,
                                    'versionFinalHipotesis' => $versionFinal,
                                    'tiempoTotalCaso'       => $tiempoTotalSolucion,
                                    'usrCreacion'           => $usrCreacion,
                                    'ipCreacion'            => $ipCreacion,
                                    'idDepartamento'        => $idDepartamento,
                                    'idEmpleado'            => $idEmpleado,
                                    'empleado'              => $empleado,
                                    'tipo_afectacion'       => $tipo_afectacion,
                                    'intPersonaEmpresaRol'  => $intPersonaEmpresaRol,
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
                                    'strSplitterI'               => $strSplitterI,
                                    'intIdHipotesisInicialI'      => $intIdHipotesisInicialI
                                );
        
        /* @var $ingresarSeguimiento SoporteService */
        $serviceSoporte = $this->get('soporte.SoporteService');
        //---------------------------------------------------------------------*/
        
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
        $respuestaArray = $serviceSoporte->cerrarCaso($arrayParametros);
        
        $resultado = json_encode($respuestaArray);
        //----------------------------------------------------------------------*/
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_78-34")
     * 
     * @version Inicial 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2016 Se guarda el estado de la tarea en el seguimiento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 16-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     */
    public function administrarTareaAsignadaAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        
        $peticion = $this->get('request');
        
        $id_detalle_asignacion = $peticion->get('id');  
        
        $session = $peticion->getSession();
        
        $codEmpresa          = $session->get('idEmpresa');
        $intIdDepartamento   = $session->get('idDepartamento');
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $peticion->getSession()->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();
        
        $em->getConnection()->beginTransaction();
        
        try {
            $detalleAsignacion = $em->getRepository('schemaBundle:InfoDetalleAsignacion')->find($id_detalle_asignacion);
            
            $detalle = $em->getRepository('schemaBundle:InfoDetalle')->find($detalleAsignacion->getDetalleId());
            
            $diferencia = $this->obtenerDiferenciaFechas($detalle->getFeSolicitada(),new \DateTime('now'));                                              			   
                        
            
            if($peticion->get('bandera') == 'Aceptada'){
		   if($diferencia==0 && !$tareaFueAceptada)
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
            $arrayParametrosHist["intDetalleId"]    = $detalleAsignacion->getDetalleId();
            $arrayParametrosHist["strObservacion"]  = $peticion->get('observacion');
            $arrayParametrosHist["strEstadoActual"] = $peticion->get('bandera');                    
            $arrayParametrosHist["strFeCreacion"]   = $diferencia==0?new \DateTime('now'):$detalle->getFeSolicitada();
            $arrayParametrosHist["strAccion"]       = $peticion->get('bandera');

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);            
            
            
            //Se ingresa el seguimiento de la tarea
            $arrayParametrosHist["intDetalleId"]    = $detalle->getId();
            $arrayParametrosHist["strObservacion"]  = "Tarea fue ".$peticion->get('bandera');
            $arrayParametrosHist["strOpcion"]       = "Seguimiento";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist); 

            $em->getConnection()->commit();
            $resultado = json_encode(array('success'=>true));

            //Proceso que graba tarea en INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = $arrayParametrosHist["intDetalleId"];
            $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametrosHist["strUsrCreacion"];
            $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

        }catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
        
        $respuesta->setContent($resultado);
        
        return $respuesta;
        
    }
    
    /**
    * administrarTareaAsignadaGridAction
    *
    * Funcion que acepta o rechaza una tarea
    *
    * @version Inicial 1.0
    *
    * @author modificado Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 10-11-2015 Se realizan ajustes para enviar notificacion de via mail cuando se rechaza una tarea
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 23-06-2016 Se guarda el estado de la tarea en el seguimiento
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
    *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
    *
    * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.4 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
    * 
    * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
    * @version 1.5 19-06-2018 - Se modifico programación en envio de notificación para seleccionar los puntos afectados 
    *                           de los casos desde la información registrada en Telcos, y no del punto en sesión
    *
    * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.6 24-04-2019 - Se agrega el método *calcularTiempoEstado*, para identificar el tiempo de la tarea cuando cambia
    *                           de estado.
    *
    * @since 1.4
    *
    * @return array $respuesta  Objeto en formato JSON
    *
    *
    * @Secure(roles="ROLE_78-156")
    *
    */
    public function administrarTareaAsignadaGridAction()
    {

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em             = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
                
        $codEmpresa        = $session->get('idEmpresa');
        $prefijoEmpresa    = $session->get('prefijoEmpresa');
        $intIdDepartamento = $session->get('idDepartamento');
        $id_detalle      = $peticion->get('id');
        $id_caso         = $peticion->get('id_caso');
        $nombreTarea     = $peticion->get('nombreTarea');
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        $strNombreProceso    = "";
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $peticion->getSession()->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();        
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            $detalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);

            if($detalle->getFeSolicitada()< new \DateTime('now') || $detalle->getFeSolicitada()== new \DateTime('now'))
                $esProgramada = false;
            else
                $esProgramada = true;
	    
            $numeroAceptaciones = $em->getRepository('schemaBundle:InfoDetalle')
                                     ->getNumeroAceptacionesTarea($id_detalle,'Aceptada');
					
            if($numeroAceptaciones[0]['cont']==0)
                $tieneAceptaciones = false;
            else
                $tieneAceptaciones = true;
            
            
            if($peticion->get('bandera') == 'Aceptada')
            {
                if(!$esProgramada && !$tieneAceptaciones)
                    $detalle->setFeSolicitada(new \DateTime('now'));
            }
            else
                if($peticion->get('bandera') == 'Rechazada')
                {
                    $detalle->setEsSolucion('N');
                }
            
            $em->persist($detalle);
            $em->flush();            

            //Se ingresa el historial de la tarea
            $arrayParametrosHist["strObservacion"]  = $peticion->get('observacion');
            $arrayParametrosHist["strEstadoActual"] = $peticion->get('bandera');   
            $arrayParametrosHist["intDetalleId"]    = $detalle->getId(); 
            $arrayParametrosHist["strFeCreacion"]   = !$esProgramada?new \DateTime('now'):$detalle->getFeSolicitada();
            $arrayParametrosHist["strAccion"]       = $peticion->get('bandera');

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);            

            //Función encargada de calcular los tiempos de las tareas.
            $serviceSoporte->calcularTiempoEstado(array('strEstadoActual'   => $arrayParametrosHist["strEstadoActual"],
                                                        'intIdDetalle'      => $arrayParametrosHist["intDetalleId"],
                                                        'strTipoReprograma' => null,
                                                        'strUser'           => $arrayParametrosHist["strUsrCreacion"],
                                                        'strIp'             => $arrayParametrosHist["strIpCreacion"]));

            //Se ingresa el seguimiento de la tarea
            $arrayParametrosHist["strObservacion"] = "Tarea fue ".$peticion->get('bandera');
            $arrayParametrosHist["strOpcion"]      = "Seguimiento";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

            if($peticion->get('bandera') == 'Rechazada')
            {
                /***********************************************************************
                     USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS
                 ***********************************************************************/
                $arrayParametros['emSoporte']      = $em;
                $arrayParametros['emComercial']    = $emComercial;
                $arrayParametros['emComunicacion'] = $emComunicacion;
                $arrayParametros['emGeneral']      = $emGeneral;
                $arrayParametros['detalleId']      = $id_detalle;
                $arrayParametros['casoId']         = $id_caso;
                $arrayParametros['asunto']         = "Tarea Rechazada";
                $persona                           = null;
                
                $arrayDatos             = $em->getRepository("schemaBundle:InfoDetalleAsignacion")->getInfoPlantillaCorreo($arrayParametros);
                $infoDetalleAsignacion  = $em->getRepository('schemaBundle:InfoDetalleAsignacion')->findByDetalleId($detalle->getId());
                
                $persona                = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                      ->findOneByLogin($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getUsrCreacion());

                $to[] = $arrayDatos['destinatario'];

                if(is_object($detalle))
                {
                    $objAdmiTarea = $detalle->getTareaId();
                }

                if(is_object($objAdmiTarea))
                {
                    $strNombreProceso = $objAdmiTarea->getProcesoId()->getNombreProceso();
                }

                $strAsunto = $strAsunto . " | PROCESO: ".$strNombreProceso;

                $parametros = array('idCaso'            => $id_caso,
                                    'perteneceACaso'    => $arrayDatos['perteneceACaso'],
                                    'numeracion'        => $arrayDatos['numeracion'],
                                    'referencia'        => $arrayDatos['numeracionReferencia'],
                                    'asignacion'        => $infoDetalleAsignacion[count($infoDetalleAsignacion) - 1],
                                    'persona'           => $persona ? $persona : false,
                                    'nombreProceso'     => $strNombreProceso,
                                    'nombreTarea'       => $nombreTarea ? $nombreTarea : '',
                                    'estado'            => 'Rechazada',
                                    'empresa'           => $prefijoEmpresa);                

                    
                $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                $serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto, $to, 'TAREARECHAZADA', $parametros,
                                                              $arrayDatos['empresa'], $arrayDatos['canton'], $arrayDatos['departamento']);

                /***********************************************************************
                     USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS
                ***********************************************************************/
                
                $objInfoCaso      = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);
                
                if (($objInfoCaso->getTipoCasoId()->getNombreTipoCaso()=='Tecnico')||($objInfoCaso->getTipoCasoId()->getNombreTipoCaso()=='Arcotel'))
                {
                    $arrayAfectacionPadres = $em->getRepository('schemaBundle:InfoCaso')
                                                ->getRegistrosAfectadosTotalXCaso($id_caso,'Cliente','Data');
                    foreach($arrayAfectacionPadres as $arrayAfectadoPadre)
                    {
                        $arrayParametrosSMS = array();
                        $arrayParametrosSMS['puntoId']      = $arrayAfectadoPadre['afectadoId'];
                        $arrayParametrosSMS['personaId']    = "";
                        $arrayParametrosSMS['destinatario'] = "CLI";
                        $arrayParametrosSMS['tipoEnvio']    = "OUT";
                        $arrayParametrosSMS['tipoNotifica'] = "SMS";
                        $arrayParametrosSMS['empresa']      = $codEmpresa;
                        $arrayParametrosSMS['tipoEvento']   = "RECHAZADA";
                        $arrayParametrosSMS['usuario']      = $session->get('user');
                        $arrayParametrosSMS['casoId']       = $objInfoCaso->getId();
                        $arrayParametrosSMS['detalleId']    = "";
                        $arrayParametrosSMS['asignacion']   = "";
                        $serviceSoporte->enviaSMSCasoCliente($arrayParametrosSMS);
                        $arrayParametrosCorreo = array();
                        $arrayParametrosCorreo['puntoId']        = $arrayAfectadoPadre['afectadoId'];
                        $arrayParametrosCorreo['usuario']        = $session->get('user');
                        $arrayParametrosCorreo['caso']           = $objInfoCaso;
                        $arrayParametrosCorreo['idDepartamento'] = $intIdDepartamento;
                        $arrayParametrosCorreo['empresa']        = $codEmpresa;
                        $arrayParametrosCorreo['codPlantilla']   = "CASORECHAZACLI";
                        $arrayParametrosCorreo['asunto']        = "Rechazo del caso";
                        $arrayParametrosCorreo['observacion']    =$peticion->get('observacion');
                        $serviceSoporte->enviaCorreoClientesCasos($arrayParametrosCorreo);
                    }
                }
            }

            $em->getConnection()->commit();
            $resultado = json_encode(array('success'=>true));

            //Proceso que graba tarea en INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = $arrayParametrosHist["intDetalleId"];
            $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametrosHist["strUsrCreacion"];
            $objServiceSoporte                          = $this->get('soporte.SoporteService');
            $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
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
     * @Secure(roles="ROLE_78-156")
     * 
     * reprogramarTareaAction
     * 
     * Funcion que reprograma una tarea
     * @version Inicial 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2016 Se guarda el estado de la tarea en el seguimiento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 19-06-2018 - Se modifico programación en envio de notificación para seleccionar los puntos afectados 
     *                           de los casos desde la información registrada en Telcos, y no del punto en sesión
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 24-04-2019 - Se agrega el método *calcularTiempoEstado* que sirve para obtener el tiempo cuando
     *                           el estado de la tarea cambia y identificar si fue por usuario o cliente.
     *
     * @since 1.2
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @return array $respuesta  Objeto en formato JSON
     * 
     */
    public function reprogramarTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();
        $codEmpresa        = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        $intIdDepartamento = $session->get('idDepartamento');
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $arrayParametrosHist = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $peticion->getSession()->get('user');
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();          

        $id_detalle = $peticion->get('id_detalle');
        $observacion = $peticion->get('observacion') ? $peticion->get('observacion') : "Tarea Reprogramada";
        $motivoId = $peticion->get('motivo');

	$fecha = explode("T", $peticion->get('fe_ejecucion'));
	$hora  = explode("T", $peticion->get('ho_ejecucion'));		
	
	$date = date_create(date('Y-m-d H:i',strtotime($fecha[0].' '.$hora[1])));	
	
	if($date < new \DateTime('now'))$esReprogramadaAtras = true; else $esReprogramadaAtras = false;
			
        $em->getConnection()->beginTransaction();
        try {
	    
	    $detalle = $em->getRepository('schemaBundle:InfoDetalle')->find($id_detalle);
            $detalle->setFeSolicitada($date);
	    $em->persist($detalle);
	    $em->flush();
          
            //Se ingresa el historial de la tarea
            $arrayParametrosHist["strObservacion"]  = $observacion;
            $arrayParametrosHist["strEstadoActual"] = "Reprogramada";   
            $arrayParametrosHist["intDetalleId"]    = $detalle->getId(); 
            $arrayParametrosHist["strMotivo"]       = $motivoId; 
            $arrayParametrosHist["strFeCreacion"]   = $esReprogramadaAtras ? $date : new \DateTime('now');
            $arrayParametrosHist["strAccion"]       = "Reprogramada";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);            

            //Función encargada de calcular los tiempos de las tareas.
            $serviceSoporte->calcularTiempoEstado(array('strEstadoActual'    => "Reprogramada",
                                                        'objFechaReprograma' => $date,
                                                        'intIdDetalle'       => $arrayParametrosHist["intDetalleId"],
                                                        'strTipoReprograma'  => $arrayParametrosHist["strMotivo"],
                                                        'strUser'            => $arrayParametrosHist["strUsrCreacion"],
                                                        'strIp'              => $arrayParametrosHist["strIpCreacion"]));

            //Se ingresa el seguimiento de la tarea
            $arrayParametrosHist["strObservacion"] = "Tarea Reprogramada para el " . date_format($date, 'Y-m-d H:i') . " Motivo : " . $observacion;
            $arrayParametrosHist["strOpcion"]      = "Seguimiento";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist); 
            
            /******************************************************/
            /******************Envio de sms y correo***************/
            $arrayCaso = $em->getRepository('schemaBundle:InfoDetalle')
                            ->tareaPerteneceACaso($id_detalle);
            if ($arrayCaso[0]['caso']!=0)
            {
                $arrayDetalleHip = $em->getRepository('schemaBundle:InfoDetalle')
                                      ->getCasoPadreTarea($id_detalle);
                $intCasoId = $arrayDetalleHip[0]->getCasoId()->getId();
                $objInfoCaso      = $em->getRepository('schemaBundle:InfoCaso')->find($intCasoId);
                if (($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Tecnico')||
                   ($objInfoCaso->getTipoCasoId()->getNombreTipoCaso() == 'Arcotel'))
                {
                    $arrayAfectacionPadres = $em->getRepository('schemaBundle:InfoCaso')
                                                ->getRegistrosAfectadosTotalXCaso($intCasoId,'Cliente','Data');
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
                        $arrayParametrosSMS['usuario']      = $session->get('user');
                        $arrayParametrosSMS['casoId']       = $objInfoCaso->getId();
                        $arrayParametrosSMS['detalleId']    = "";
                        $arrayParametrosSMS['asignacion']   = "";
                        $serviceSoporte->enviaSMSCasoCliente($arrayParametrosSMS);
                        $arrayParametrosCorreo = array();
                        $arrayParametrosCorreo['puntoId']        = $arrayAfectadoPadre['afectadoId'];
                        $arrayParametrosCorreo['usuario']        = $session->get('user');
                        $arrayParametrosCorreo['caso']           = $objInfoCaso;
                        $arrayParametrosCorreo['idDepartamento'] = $intIdDepartamento;
                        $arrayParametrosCorreo['empresa']        = $codEmpresa;
                        $arrayParametrosCorreo['codPlantilla']   = "CASOREPROGRACLI";
                        $arrayParametrosCorreo['asunto']         = "Reprogramación del caso";
                        if ($motivoId == 'C')
                        {
                            $arrayParametrosCorreo['observacion']    = "Cliente Solicita Reprogramar ".$observacion;
                        }
                        elseif ($motivoId == 'T')
                        {
                            $arrayParametrosCorreo['observacion']    = "Tecnico Solicita Reprogramar ".$observacion;
                        }else
                        {
                            $arrayParametrosCorreo['observacion']    = $observacion;
                        }
                        $serviceSoporte->enviaCorreoClientesCasos($arrayParametrosCorreo);
                    }
                }
            }

            $em->getConnection()->commit();
            $resultado = json_encode(array('success'=>true));

            //Proceso que graba tarea en INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = $arrayParametrosHist["intDetalleId"];
            $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametrosHist["strUsrCreacion"];
            $objServiceSoporte                          = $this->get('soporte.SoporteService');
            $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

        }catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
		
        $respuesta->setContent($resultado);        
        return $respuesta;
    }
    
    public function exportarExcelAfectadosAction(){
    
	$peticion = $this->get('request');
        
        $session = $peticion->getSession();
        
        $usuario = $session->get('user');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $emC = $this->getDoctrine()->getManager("telconet");
		        
        $id_caso = $peticion->get('hid_id_caso');   
        
        $caso = $em->getRepository('schemaBundle:InfoCaso')->find($id_caso);
    
	$resultado = $em->getRepository('schemaBundle:InfoCaso')->getRegistrosAfectadosTotalXCaso($id_caso);
	
	$elementosContenedores = $em->getRepository('schemaBundle:InfoCaso')->getRegistrosAfectadosTotalXCaso($id_caso,'Elemento');
	
	$elementoDescripcion = '';
	
	if($elementosContenedores){
	     
	     foreach($elementosContenedores as $elemento):
		    
		   $elementoDescripcion .= $elemento['afectadoNombre'].' '.$elemento['afectadoDescripcion'].', ';
	     
	     endforeach;
	     
	     
	     $elementoDescripcion = ' Problema Masivo en : '.$elementoDescripcion;
	      
	}
	else $elementoDescripcion = ' Problema en Cliente';
        
        error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaAfectados.xls");       
                
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Afectados por Caso");
        $objPHPExcel->getProperties()->setSubject("Consulta de Afectados por Caso");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de Afectados por Caso.");
        $objPHPExcel->getProperties()->setKeywords("Afectados por Caso");
        $objPHPExcel->getProperties()->setCategory("Reporte");
        
        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.$caso->getNumeroCaso());
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.$elementoDescripcion);       
	
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$i = 12;
	
	foreach($resultado as $afectado):
	
		$contacto = '';  $correos = ''; 
			
		if($afectado['tipoAfectado'] != 'Elemento'){		
		
			$punto = $emC->getRepository('schemaBundle:InfoPunto')->find($afectado['afectadoId']);
			
			if($punto){						
						      
				$contacto = $emC->getRepository('schemaBundle:InfoPersonaFormaContacto')
							->findContactosTelefonicosPorPunto($punto->getId());	
			
				$correos = $emC->getRepository('schemaBundle:InfoPersonaFormaContacto')
							->findCorreosPorPunto($punto->getId());
		
			}
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,''.$afectado['afectadoDescripcion']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,''.$afectado['afectadoNombre']);       
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,''.$correos);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,''.$contacto);   
			
			$i = $i + 1;
		
		}				
		
	endforeach; 

        $objPHPExcel->getActiveSheet()->setTitle('Reporte');        
        $objPHPExcel->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Casos_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
	
	
    }

    /**
     * Documentación de la función 'exportarConsultaAction'.
     *
     * Función que consulta los casos filtrando por los parámetros enviados por el usuario,
     * para luego ser exportado a Excel.
     *
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 25-06-2019 - Se establece el estándar de calidad en la función y se agrega el llamado al
     *                           proceso *jobReporteCasos* encargado de generar el reporte de casos en la base de datos
     *                           y el archivo generado es enviado por correo al usuario.
     *                         - Se establece el control de exportación por usuario.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 28-06-2019 - Se valida el parámetro empleado_id en caso que no venga vacio y poder obtener el
     *                           id del empleado asignado.
     *
     * @Secure(roles="ROLE_78-37")
     */
    public function exportarConsultaAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objPeticion     = $this->get('request');
        $objSession      = $objPeticion->getSession();
        $emSoporte       = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil  = $this->get('schema.Util');
        $strUser         = $objSession->get('user');
        $arrayParametros = array();
        $strIp          =  $objPeticion->getClientIp();

        try
        {
            /**
             * Antes de iniciar con el parseo de los datos, verificamos que el usuario
             * no tenga el proceso de reporte ejecutándose.
             */
            $arrayResultJob = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->existeJobReporteTarea(array ('strNombreJob' => 'JOB_REPORTE_CASOS_'.$strUser));

            if ($arrayResultJob['status'] === 'fail')
            {
                throw new \Exception($arrayResultJob['message']);
            }

            if ($arrayResultJob['status'] === 'ok' && $arrayResultJob['cantidad'] > 0)
            {
                throw new \Exception('Error : Estimado usuario ya cuenta con un proceso ejecutándose.<br/>'.
                                             'Por favor intente de nuevo en unos minutos.');
            }

            /**
             * Verificación del control de generación del reporte.
             */
            $intPersonaEmpresaRol     = $objSession->get('idPersonaEmpresaRol');
            $intCantidadExportacion   = 10; //cantidad máxima permitida de exportación por defecto.
            $objFechaActual           = new \DateTime(date_format(new \DateTime('now'), "d-m-Y"));
            $objSoporteProcesoService = $this->get('soporte.SoporteProcesos');
            $boolReiniciarCaract      = false;

            $arrayData = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne( 'PARAMETROS_REPORTE_CASOS','SOPORTE','','CANTIDAD_GENERA_REPORTE','','','','','','');

            if (!empty($arrayData) && isset($arrayData['valor1']) &&
                !empty($arrayData['valor1']) && intval($arrayData['valor1']) > 0)
            {
                //Cantidad máxima permitida de exportación.
                $intCantidadExportacion = intval($arrayData['valor1']);
            }

            //Obtenemos la característica 'EXPORTAR_REPORTE_CASOS'.
            $objAdmiCaracteristica = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array ('descripcionCaracteristica' => 'EXPORTAR_REPORTE_CASOS',
                                       'estado'                    => 'Activo'));

            if (!is_object($objAdmiCaracteristica))
            {
                throw new \Exception('Error : La característica de exportación no se encuentra Activa. '
                        .'Por favor comuníquese con Sistemas.');
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
                    throw new \Exception('Error : Estimado usuario, ha superado la cantidad máxima de exportación de casos '.
                                                 'para el día de hoy. Por favor volver a intentar el día de mañana.');
                }

                if ($objFechaCreacion->getTimestamp() < $objFechaActual->getTimestamp())
                {
                    $boolReiniciarCaract = true;
                }
            }

            //Actualizamos o creamos la característica 'EXPORTAR_REPORTE_CASOS'
            $objSoporteProcesoService->putInfoInfoPersonaEmpresaRolCarac(
                    array ('intIdPersonaEmpresaRol' => $intPersonaEmpresaRol,
                           'strCaracteristica'      => 'EXPORTAR_REPORTE_CASOS',
                           'strUsuarioCrea'         => $objSession->get('user'),
                           'boolReiniciar'          => $boolReiniciarCaract,
                           'strIpCrea'              => $objPeticion->getClientIp()));

            /**
             * Obtenemos los filtros realizados por el usuario.
             */
            $arrayParametros['strUsuarioSolicita']  = $strUser;
            $arrayParametros['strIpSolicita']       = $strIp;
            $arrayParametros['prefijoEmpresa']      = $objSession->get('prefijoEmpresa');
            $arrayParametros['numero']              = $objPeticion->get('txtNumero');
            $arrayParametros['tituloInicial']       = $objPeticion->get('txtTituloInicial');
            $arrayParametros['versionInicial']      = $objPeticion->get('txtVersionInicial');
            $arrayParametros['tituloFinal']         = $objPeticion->get('txtTituloFinal');
            $arrayParametros['versionFinal']        = $objPeticion->get('txtVersionFinal');
            $arrayParametros['tituloFinalHip']      = $objPeticion->get('hid_comboHipotesis');
            $arrayParametros['pres_tituloFinalHip'] = $objPeticion->get('comboHipotesis_index');
            $arrayParametros['estado']              = $objPeticion->get('hid_sltEstado');
            $arrayParametros['nivelCriticidad']     = $objPeticion->get('hid_comboNivelCriticidad');
            $arrayParametros['tipoCaso']            = $objPeticion->get('hid_comboTipoCaso');
            $arrayParametros['usrApertura']         = $objPeticion->get('hid_usrCreacion');
            $arrayParametros['usrCierre']           = $objPeticion->get('hid_usrCierre');
            $arrayParametros['departamento_id']     = $objPeticion->get('hid_comboDepartamento');
            $arrayParametros['empleado_id']         = $objPeticion->get('hid_comboEmpleado');
            $arrayParametros['canton_id']           = $objPeticion->get('hid_comboCiudad');
            $arrayParametros['empresa']             = $objPeticion->get('hid_empresa');
            $arrayParametros['idEmpresaSeleccion']  = $objSession->get('idEmpresa');

            if ($arrayParametros["empleado_id"] && $arrayParametros["empleado_id"] != "")
            {
                $arrayParametros['empleado_id'] = explode('@@', $arrayParametros["empleado_id"])[0];
            }

            if ($arrayParametros['empresa'] && $arrayParametros['empresa'] !== "")
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                        ->findOneByPrefijo($arrayParametros['empresa']);

                if (is_object($objEmpresa))
                {
                    $arrayParametros['idEmpresaSeleccion'] = $objEmpresa->getId();
                }
            }

            $arraySessionCliente      = ($objSession->get('cliente') ? $objSession->get('cliente') : "");
            $arraySessionPtoCliente   = ($objSession->get('ptoCliente') ? $objSession->get('ptoCliente') : "");

            $strNombreClienteAfectado = ($arraySessionCliente ? (
                                            $arraySessionCliente['razon_social'] ? $arraySessionCliente['razon_social'] :
                                            $arraySessionCliente['nombres']." ".$arraySessionCliente['apellidos']) : "");
            $strLoginPuntoCliente     = ($arraySessionPtoCliente ? (
                                            $arraySessionPtoCliente['login'] ? $arraySessionPtoCliente['login'] : "") : "");

            $arrayParametros['clienteAfectado'] = ($strNombreClienteAfectado ?
                                                    $strNombreClienteAfectado : $objPeticion->get('txtClienteAfectado'));
            $arrayParametros['loginAfectado']   = ($strLoginPuntoCliente ?
                                                    $strLoginPuntoCliente : $objPeticion->get('txtLoginAfectado'));

            $arrayFeAperturaDesde = explode('T', $objPeticion->get('feAperturaDesde'));
            $arrayFeAperturaHasta = explode('T', $objPeticion->get('feAperturaHasta'));
            $arrayFeCierreDesde   = explode('T', $objPeticion->get('feCierreDesde'));
            $arrayFeCierreHasta   = explode('T', $objPeticion->get('feCierreHasta'));

            $arrayParametros['feAperturaDesde'] = $arrayFeAperturaDesde ? $arrayFeAperturaDesde[0] : '';
            $arrayParametros['feAperturaHasta'] = $arrayFeAperturaHasta ? $arrayFeAperturaHasta[0] : '';
            $arrayParametros['feCierreDesde']   = $arrayFeCierreDesde   ? $arrayFeCierreDesde[0]   : '';
            $arrayParametros['feCierreHasta']   = $arrayFeCierreHasta   ? $arrayFeCierreHasta[0]   : '';
            $arrayParametros['empresa']         = '';

            //Método que realiza el reporte en la base de datos.
            $arrayReporteCasos = $emSoporte->getRepository('schemaBundle:InfoCaso')->jobReporteCasos($arrayParametros);

            if ($arrayReporteCasos['status'] == 'fail')
            {
                throw new \Exception($arrayReporteCasos['message']);
            }

            $objResponse->setContent(json_encode($arrayReporteCasos));
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al generar el Reporte de Casos.<br/>'.
                          'Si el problema persiste comuníquese con Sistemas.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'InfoCasoController->exportarConsultaAction',
                                          $objException->getMessage(),
                                          $strUser,
                                          $strIp);

            $objResponse->setContent(json_encode(array('status' => 'fail', 'message' => $strMessage)));
        }
        return $objResponse;
    }

    /**
     * Documentacion para la funcion generateExcelConsulta
     *
     * Funcion que sirve para generar el reporte excel de casos
     *
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-08-2016 Se obtiene el primer afectado que se encuentre y segun eso mostrar los casos relacionados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 19-08-2016 Se agrega en el reporte excel el tiempo total de los casos y el departamento que cerro la ultima tarea.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 14-09-2016 Se obtiene el nombre de la ciudad del departamento que cerro la ultima tarea, solo para la empresa TN
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 07-07-2017 En la creación del reporte excel de Casos se obtienen los datos del punto de los tipo de afectado Cliente.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 14-08-2017 Se modifican los nombres de las variables recibidas en la respuesta de la consulta de los seguimientos de las tareas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 26-08-2017 Se realizan ajustes para obtener solo los afectados tipo cliente y elemento, adicional en el reporte excel se cambia
     *                         el nombre de la celda de "Departamento Ult. Tarea" a "Departamento Ult. Tarea Finalizada".
     *
     * @param String $casos
     * @param String $em
     * @param String $parametros
     * @param String $usuario
     * @param String $emC
     * @param String $emI
     * @param String $prefijoEmpresa
     *
     */
    public static function generateExcelConsulta($casos,$em,$parametros,$usuario,$emC,$emI,$prefijoEmpresa)
    {
	error_reporting(E_ALL);
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        
        
        if($prefijoEmpresa=='TTCO')
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaCasosTTCO.xls");
        else $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaCasosMD.xls");
       
                
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Casos");
        $objPHPExcel->getProperties()->setSubject("Consulta de Casos");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de casos.");
        $objPHPExcel->getProperties()->setKeywords("Casos");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
        
        $departamento = $emC->getRepository('schemaBundle:AdmiDepartamento')->find($parametros['departamento_id']);
        if($departamento)$nombreDepartamento = $departamento->getNombreDepartamento();
        else $nombreDepartamento = '-';
        
        $empleado = $emC->getRepository('schemaBundle:InfoPersona')->find(explode('@@', $parametros["empleado_id"])[0]);
        if($empleado)$empleadoNombre = $empleado->getNombres().' '.$empleado->getApellidos();
        else $empleadoNombre = '-';
                
        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['numero']=="")?'Todos': $parametros['numero']);
        $objPHPExcel->getActiveSheet()->setCellValue('F8',''.($parametros['estado']=="" ? 'Todos': $parametros['estado']));
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.($parametros['tipoCaso']=="" ? 'Todos': $parametros['tipoCaso']));
        $objPHPExcel->getActiveSheet()->setCellValue('F9',''.($parametros['nivelCriticidad']=="" ? 'Todos': $parametros['nivelCriticidad']));
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.($parametros['tituloInicial']=="" ? 'Todos': $parametros['tituloInicial']));
        $objPHPExcel->getActiveSheet()->setCellValue('F10',''.($parametros['versionInicial']=="" ? 'Todos': $parametros['versionInicial']));
        $objPHPExcel->getActiveSheet()->setCellValue('B11',''.(($parametros['tituloFinal']=="" ? 'Todos': $parametros['tituloFinal']) . " | " .($parametros['pres_tituloFinalHip']=="" ? 'Todos': $parametros['pres_tituloFinalHip'])) );
        $objPHPExcel->getActiveSheet()->setCellValue('F11',''.($parametros['versionFinal']=="")?'Todos': $parametros['versionFinal']);
        $objPHPExcel->getActiveSheet()->setCellValue('B12',''.($parametros['loginAfectado']=="")?'Todos': $parametros['loginAfectado']);
        $objPHPExcel->getActiveSheet()->setCellValue('F12',''.($parametros['clienteAfectado']=="")?'Todos': $parametros['clienteAfectado']);
		
        $objPHPExcel->getActiveSheet()->setCellValue('B13',''.'-');
        $objPHPExcel->getActiveSheet()->setCellValue('F13',''.'-');
        $objPHPExcel->getActiveSheet()->setCellValue('B14',''.$nombreDepartamento);
        $objPHPExcel->getActiveSheet()->setCellValue('F14',''.$empleadoNombre);		
        $objPHPExcel->getActiveSheet()->setCellValue('B15',''.$parametros['usrApertura']==""?'Todos':$parametros['usrApertura']);
        $objPHPExcel->getActiveSheet()->setCellValue('F15',''.$parametros['usrCierre']==""?'Todos':$parametros['usrCierre']);
        
        $objPHPExcel->getActiveSheet()->setCellValue('C16',''.($parametros['feAperturaDesde']=="")?'Todos': $parametros['feAperturaDesde']);
        $objPHPExcel->getActiveSheet()->setCellValue('C17',''.($parametros['feAperturaHasta']=="")?'Todos': $parametros['feAperturaHasta']);
        $objPHPExcel->getActiveSheet()->setCellValue('G16',''.($parametros['feCierreDesde']=="")?'Todos': $parametros['feCierreDesde']);
        $objPHPExcel->getActiveSheet()->setCellValue('G17',''.($parametros['feCierreHasta']=="")?'Todos': $parametros['feCierreHasta']);
		
        $i=21;  

        foreach ($casos as $caso):
            // Se toma el primer afectado que se encuentre
            $strLoginAfectado      = "";
            $strTiempoTotal        = "";
            $intDetalleHistorialId = 0;
            $strDepartamentoAsig   = "";
            $strCiudadDestino      = "";
            //Se obtiene el tiempo total de los casos
            $arrayTiempoCaso = $em->getRepository('schemaBundle:InfoCaso')->getTiempoCaso($caso->getId());
            if($arrayTiempoCaso)
            {
                $strTiempoTotal = $arrayTiempoCaso[0]['tiempoTotalCasoSolucion'] . ' minutos';
            }
            //Se obtiene el departamento que cerro la ultima tarea
            $arraySeguimientosCaso = $em->getRepository('schemaBundle:InfoDetalleSeguimiento')
                                        ->getTareasSeguimientosPorCriterios(array("intIdCaso"   =>  $caso->getId()));
            foreach($arraySeguimientosCaso['arrayResultado'] as $seguimiento)
            {
                $intPos = strpos($seguimiento["strObsSeguim"], "Finalizada");
                if($intPos && ($seguimiento["intIdDetalleHistorial"] > $intDetalleHistorialId) )
                {
                    $intDetalleHistorialId  = $seguimiento["intIdDetalleHistorial"];
                    $strDepartamentoAsig    = $seguimiento["strDepartamento"];
                    $intPersonaEmpresaRolId = $seguimiento["intIdPerAsignacion"];
                }
            }
            
            if($prefijoEmpresa == "TN")
            {
                $strDepartamentoBuscarCiudad = $em->getRepository('schemaBundle:AdmiParametroDet')->getOne('DEPARTAMENTO CIUDAD REPORTE CASOS',
                                                                                                           'SOPORTE',
                                                                                                           'CASOS',
                                                                                                           'DEPARTAMENTO TECNICA SUCURSAL','','','','','','');
                //Si es departamento TECNICA SUCURSAL se obtiene el nombre de la ciudad
                if(strtoupper($strDepartamentoAsig) == $strDepartamentoBuscarCiudad["valor1"])
                {
                    $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
                    if($objInfoPersonaEmpresaRol)
                    {
                        $objInfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')
                                                  ->find($objInfoPersonaEmpresaRol->getOficinaId());
                        if($objInfoOficinaGrupo)
                        {
                            $objAdmiCanton = $em->getRepository('schemaBundle:AdmiCanton')
                                                ->find($objInfoOficinaGrupo->getCantonId());
                            if($objAdmiCanton)
                            {
                                $strCiudadDestino = " - " . $objAdmiCanton->getNombreCanton();
                            }
                        }
                    }
                }
            }

            $arrayDetalleInicial = $em->getRepository('schemaBundle:InfoDetalle')->getDetalleInicialCaso($caso->getId());
            if($arrayDetalleInicial[0]["detalleInicial"])
            {
                $objParteAfectada = $em->getRepository('schemaBundle:InfoParteAfectada')
                                       ->findByDetalleId($arrayDetalleInicial[0]["detalleInicial"]);
                if($objParteAfectada)
                {
                    $strLoginAfectado = $objParteAfectada[0]->getAfectadoNombre();
                }
            }
            $ultimo_estado = $em->getRepository('schemaBundle:InfoCaso')->getUltimoEstado($caso->getId());
                                              
	    $titulo_fin = "N/A";
	    if($caso->getTituloFinHip())
	    {
		    $Hipotesis = $em->getRepository('schemaBundle:AdmiHipotesis')->findOneById($caso->getTituloFinHip());
		    $titulo_fin = ($Hipotesis ? ($Hipotesis->getNombreHipotesis() ? $Hipotesis->getNombreHipotesis() : "N/A") : "N/A"); 
	    }
	    else
	    {
		    $titulo_fin = ($caso->getTituloFin() ? $caso->getTituloFin() : "N/A");
	    }
				
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $caso->getNumeroCaso());
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $caso->getTituloIni());
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $titulo_fin);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, date_format($caso->getFeApertura(),'Y-m-d').' '.date_format($caso->getFeApertura(),'G:i'));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, ($caso->getFeCierre())?date_format($caso->getFeCierre(),'Y-m-d').' '.date_format($caso->getFeCierre(),'G:i'):"");
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, ($ultimo_estado)?$ultimo_estado:"Asignado");            
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $caso->getVersionIni());
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $caso->getVersionFin());
                                           
            $infoDetalleHipotesis=$em->getRepository('schemaBundle:InfoDetalleHipotesis')->getOneDetalleByIdCaso($caso->getId());
                        
            $afectadoLogin="";
            $afectadoDescripcion="";
            $caja="";
            $olt="";
            $direccion="";
	    $contacto = "";
	    $tarea = "";
	    $departamento="";
	    $estadoTarea = "";
	    $estado = "";
	    
	    if($infoDetalleHipotesis)		  
		  $infoCasoAsignacion = $em->getRepository('schemaBundle:InfoCaso')->getUltimaAsignacion($caso->getId());
	    else $infoCasoAsignacion=null;
	    
	    if($infoCasoAsignacion)
		  $departamento = $infoCasoAsignacion->getAsignadoNombre()?$infoCasoAsignacion->getAsignadoNombre():'Sin Asignacion';
	    	    
            foreach ($infoDetalleHipotesis as $hipotesis):
                              
              //OBTENGO TODOS LOS DETALLE GENERADOS SOBRE UN CASO ( DETALLE RELACIONADO A LAS TAREAS Y A LAS PARTES AFECTADAS)				  
              $infoDetalles=$em->getRepository('schemaBundle:InfoDetalle')->findBy(array('detalleHipotesisId'=> $hipotesis['idDetalleHipotesis'] ));                            	      
                
              foreach ($infoDetalles as $detalle):      		    		       
                       
                       if($detalle->getTareaId()){
                       			                                                			  
			    $infoTarea = $em->getRepository('schemaBundle:AdmiTarea')->find($detalle->getTareaId());
			    $infoTareaDetalle = $em->getRepository('schemaBundle:InfoDetalle')->getUltimoEstadoDetalle($detalle->getId());			    			   			    			  			    
			    $tarea .= $infoTarea->getNombreTarea().' , ';			    
			    $estadoTarea .= $infoTareaDetalle[0]['estado'].' , '; 
                       
                       }
                       /*	
			    OBTENER EL DETALLE_ID PRIMERO QUE ES EL QUE RELACIONA LOS AFECTADOS
                       */
                $arrayTipoAfectados = array("Cliente","Elemento");
                $arrayParametrosAfectado["intDetalleId"]       = $detalle->getId();
                $arrayParametrosAfectado["arrayTipoAfectados"] = $arrayTipoAfectados;

                $objInfoParteriorteAfectada=$em->getRepository('schemaBundle:InfoParteAfectada')->getAfectadosPorCaso($arrayParametrosAfectado);

                       foreach ($objInfoParteriorteAfectada as $parteAfectada):
                       
			   if($parteAfectada->getTipoAfectado() == 'Elemento'){
				
			       $afectadoLogin=  $afectadoLogin. $parteAfectada->getAfectadoNombre(). ' ' ;
			       $afectadoDescripcion= $afectadoDescripcion .  $parteAfectada->getAfectadoDescripcion(). ' ';
			       $olt = $afectadoLogin;
			       break;
			       
			   }
			   else if ($parteAfectada->getTipoAfectado() == 'Cliente')
			   {
				$afectadoLogin=  $afectadoLogin. $parteAfectada->getAfectadoNombre(). ' ' ;
				$afectadoDescripcion= $afectadoDescripcion .  $parteAfectada->getAfectadoDescripcion(). ' ';
							                               			   
				if($parteAfectada->getAfectadoNombre()!=''){
									
					$punto = $emC->getRepository('schemaBundle:InfoPunto')->find($parteAfectada->getAfectadoId());
																
					if($punto){												
					
						$direccion = $punto->getDireccion();
									      
						$contacto = $emC->getRepository('schemaBundle:InfoPersonaFormaContacto')
									->findContactosTelefonicosPorPunto($punto->getId());																		  
														  
						$servicio = $emC->getRepository('schemaBundle:InfoServicio')->getIdsServicioPorIdPunto($punto->getId());												
						
						if($servicio){
							$servicioTecnico = $emC->getRepository('schemaBundle:InfoServicioTecnico')
										->findOneBy(array('servicioId'=>$servicio[0]->getId()));				      				      
                                                        $estado = $servicio[0]->getEstado();										    
						}else $servicioTecnico=null;
									  						
						if($servicioTecnico && $servicioTecnico->getId()!=0){
						
							if($servicioTecnico->getElementoContenedorId())
							      $caja = $emI->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoContenedorId());
							else $caja = null;
							
							if($servicioTecnico->getElementoId())
							      $olt  = $emI->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
							else $olt = null;
							
							$caja = $caja?$caja->getNombreElemento():""; 
							$olt  = $olt?$olt->getNombreElemento():"";	
						
						}else{
						      $caja = "";
						      $olt  = $afectadoDescripcion;						
						}				      				      				 
					}else{                                              						
						$caja = "";
						$olt  = $afectadoDescripcion;						
					}
			      
				}else{
					$caja = "";
					$olt  = $afectadoDescripcion;			      
				}
			      
                           }
                           else
                           {
                               $afectadoLogin       = "";
                               $afectadoDescripcion = "";
                               $olt                 = "";
                           }

                     endforeach;
                     
                     $emI->clear();
                     $emC->clear();
                     
                  endforeach;
                  
                  $em->clear();
                  
                endforeach;

            //Se consulta los ultimos casos que tenga asociado un login
	    if($strLoginAfectado && $parametros['idEmpresaSeleccion'])
            $casosAnteriores  = $em->getRepository('schemaBundle:InfoCaso')
                                   ->getUltimosCasosLogin(trim($strLoginAfectado),$parametros['idEmpresaSeleccion']);

            foreach($casosAnteriores as $casosLogin)
            {
                $casosXLogin = $casosLogin['listadoCasos'];
            }

	    $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $afectadoLogin);
	    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $afectadoDescripcion);                                                              
	    $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $caja);
	    $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $olt);
	    $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $direccion);
	    $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $contacto);
	    $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $departamento);
	    $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $tarea);
	    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $estadoTarea);
	    $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $estado);
	    $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $strTiempoTotal);
	    $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $strDepartamentoAsig.$strCiudadDestino);
	    $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $casosXLogin);

            $contacto = '';
            $estadoTarea = '';
            $tarea  ='';
               
            $i=$i+1;                                    
            
        endforeach;
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Casos_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
    /**
     * ajaxGetEmpresasPorSistema
     *
     * Metodo encargado de obtener el nombre de cada empresa ligada a la aplicacion en gestion
     *
     * @return json con los alias de la plantilla
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 - Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 - Se realizan ajustes por modificacion de la funcion generarJsonEmpresasPorSistema
     * 
     * Actualización: Se envia parametros como arreglo en la función generarJsonEmpresasPorSistema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 - 20/11/2018
     */
    public function ajaxGetEmpresasPorSistemaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion   = $this->get('request');
        $objSession = $peticion->getSession();
        $intPais    = $objSession->get('intIdPais');
        $strApp     = $peticion->get('app');
        $em         = $this->getDoctrine()->getManager('telconet');

        $arrayParametros                    = array();
        $arrayParametros['app']             =  $strApp;
        $arrayParametros['prefijoExcluido'] =  "";
        $arrayParametros['pais']            =  $intPais;
        $objResultado = $em->getRepository("schemaBundle:InfoEmpresaGrupo")->generarJsonEmpresasPorSistema($arrayParametros);

        $respuesta->setContent($objResultado);        
        return $respuesta;
    }
    
    
     /**
     * getMotivosPausarTareaAction
     *
     * Metodo encargado de obtener los motivos asociados a la herramienta de Pausar Tarea
     *
     * @return json con los motivos correspondientes
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-11-2016
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 08-05-2019 - Se agrega el departamento en sesión del usuario.
     *
     */
    public function getMotivosPausarTareaAction()
    {
        $objResponse    = new Response();
        $arrayResultado = null;
        $objResponse->headers->set('Content-Type', 'text/json');
        $obPeticion     = $this->get('request');
        $strOpcion      = $obPeticion->get('opcion');
        $soporteService = $this->get('soporte.SoporteService');

        $arrayParametros["strOpcion"]         = $strOpcion;
        $arrayParametros["strIdDepartamento"] = $obPeticion->getSession()->get('idDepartamento');

        $arrayResultado = $soporteService->obtenerMotivosPorOpcion($arrayParametros);

        $objResponse->setContent($arrayResultado);
        return $objResponse;
    }

    /**
     * getCasosDashboard
     *
     * Método que retorna los casos asignados al usuario logueado en el mes                        
     * 
     * @param Request $objRequest
     * @param String  $strFiltro Indicará si se hará la consulta para el gráfico de barras
     *                           o para el listado de casos asignados.
     * 
     * @return JsonResponse $response         
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 07-08-2015
     */  
    public function getCasosDashboardAction(Request $objRequest, $strFiltro)
    {
        $response        = new JsonResponse();
        $arrayCasos      = array();
        $arrayParametros = array();
        $arrayEstados    = array(
                                    'Asignados' => 'Asignado',
                                    'Cerrados'  => 'Cerrado'
                                );
        
        $objSession = $objRequest->getSession();
        
        if( $strFiltro == 'barras' || $strFiltro == 'listado' )
        {
            $intEmpleadoId = $objSession->get("id_empleado");
        }
        else
        {
            $intEmpleadoId = null;
        }

        $intDepartamentoId  = $objSession->get("idDepartamento");
        $intEmpresa         = $objSession->get("idEmpresa");
        
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $intCurrentMonth = date("m");
        $intCurrentYear  = date("Y");
        $timeMes         = mktime(0, 0, 0, $intCurrentMonth, 1, $intCurrentYear);
        $intNumeroDeDias = intval(date("t", $timeMes));
        
        $strFeAperturaDesde = "01-".$intCurrentMonth."-".$intCurrentYear;
        $strFeAperturaHasta = $intNumeroDeDias."-".$intCurrentMonth."-".$intCurrentYear;

        $arrayParametros["feAperturaDesde"]    = $strFeAperturaDesde;
        $arrayParametros["feAperturaHasta"]    = $strFeAperturaHasta;
        $arrayParametros["empleado_id"]        = $intEmpleadoId;
        $arrayParametros["departamento_id"]    = $intDepartamentoId;
        $arrayParametros["idEmpresaSeleccion"] = $intEmpresa;
        
        switch( $strFiltro )
        {
            case 'barras':  case 'graficoPastel':
                
                foreach( $arrayEstados as $strKey => $strValue )
                {
                    $arrayParametros["estado"] = $strValue;

                    $jsonTmpCasos = null;
                    $jsonTmpCasos = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                              ->generarJsonCasos( $arrayParametros, 
                                                                  '',
                                                                  '',
                                                                  $objSession, 
                                                                  $emComercial,
                                                                  null, 
                                                                  $emInfraestructura, 
                                                                  $emGeneral );

                    if( $jsonTmpCasos )
                    {
                        $objTmpJsonCasos = json_decode($jsonTmpCasos);

                        $arrayItemCaso          = array();
                        $arrayItemCaso['name']  = $strKey;
                        $arrayItemCaso['value'] = $objTmpJsonCasos->total ? $objTmpJsonCasos->total : 0;

                        $arrayCasos[] = $arrayItemCaso;
                    }
                }

                $response->setData( array( 'arrayCasos' => $arrayCasos ) );
                    
                break;
                
            
            case 'listado':
                
                $arrayParametros["estado"] = $arrayEstados['Asignados'];

                $start = $objRequest->query->get('start');
                $limit = $objRequest->query->get('limit');

                $jsonTmpCasos     = null;
                $intTotal         = 0;
                $arrayEncontrados = array();
                $jsonTmpCasos     = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                              ->generarJsonCasos( $arrayParametros, 
                                                                  $start,
                                                                  $limit,
                                                                  $objSession, 
                                                                  $emComercial,
                                                                  null, 
                                                                  $emInfraestructura, 
                                                                  $emGeneral );

                if( $jsonTmpCasos )
                {
                    $objTmpJsonCasos = json_decode($jsonTmpCasos);

                    $intTotal         = $objTmpJsonCasos->total ? $objTmpJsonCasos->total : 0;
                    $arrayEncontrados = $objTmpJsonCasos->encontrados ? $objTmpJsonCasos->encontrados : array();
                }

                $response->setData(
                                    array(
                                            'total'           => $intTotal,
                                            'encontrados'     => array(
                                                                        'feAperturaDesde' => $strFeAperturaDesde,
                                                                        'feAperturaHasta' => $strFeAperturaHasta,
                                                                        'estadoCaso'      => $arrayEstados['Asignados'],
                                                                        'empleadoId'      => $intEmpleadoId,
                                                                        'departamentoId'  => $intDepartamentoId,
                                                                        'casos'           => $arrayEncontrados
                                                                      )
                                         )
                                  );
                    
                break;
        }
                    
        return $response;
    }
    
    /**
     * ajaxGetDetallesTareasTNAction
     *
     * Método que retorna las tareas que han sido finalizadas en un CASO
     * 
     * @return JsonResponse $response         
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-05-2016
     * 
     * @Secure(roles="ROLE_78-226")
    */
    public function ajaxGetDetallesTareasTNAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');

        $idCaso     = $objRequest->get('id_caso') ? $objRequest->get('id_caso') : '';
        $parametros = array("idCaso"=>$idCaso,"estado"=>"Finalizada");
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:InfoDetalle')
                        ->generarJsonDetallesTareasTNXParametros($parametros);		

        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
     * ajaxGuardarTareasEsSolucionTNAction
     *
     * Método que obtiene las tareas para cerrar un caso en TN
     * 
     * @return JsonResponse $respuest
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-05-2016
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 22-06-2016 Se obtiene el último estado de las tareas de los seguimientos
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     */
    public function ajaxGuardarTareasEsSolucionTNAction()
    {
        $emSoporte                  = $this->getDoctrine()->getManager("telconet_soporte");
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $codEmpresa                 = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $usrCreacion                = ($objSession->get('user') ? $objSession->get('user') : "") ;
        $intIdDepartamento          = $objSession->get('idDepartamento');
        $intIdCaso                  = $objRequest->get('id_caso') ? $objRequest->get('id_caso') : '';
        $jsonTareasCerrarCasoTN     = $objRequest->get('tareasCerrarCasoTN') ? $objRequest->get('tareasCerrarCasoTN') : '';
        $stringEsSolucion           = "";
        $observacionVersionFinal    = "";   
        $serviceSoporte             = $this->get('soporte.SoporteService');
        $arrayParametrosHist        = array();
        
        $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Seguimiento";     
        
        $respuesta                  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $boolBanEsSolucion       = false;
        
        if( $jsonTareasCerrarCasoTN )
        {
            $objTmpJsonTareasCerrarCasoTN = json_decode($jsonTareasCerrarCasoTN);
            $intTotalTareasCerrarCasoTN         = $objTmpJsonTareasCerrarCasoTN->total;
            
            if( $intTotalTareasCerrarCasoTN )
            {
                if( $intTotalTareasCerrarCasoTN > 0 )
                {
                    $emSoporte->getConnection()->beginTransaction();
                    try
                    {
                        $arrayTareasCerrarCasoTN = $objTmpJsonTareasCerrarCasoTN->tareas;
                        foreach( $arrayTareasCerrarCasoTN as $objItemTareaCerrarCasoTN )
                        {
                            $boolBanEsSolucion          = false;
                            $observacionEsSolucion      = "";
                            $detalle_id_TN              = $objItemTareaCerrarCasoTN->id_detalle;
                            $es_solucion_TN             = $objItemTareaCerrarCasoTN->es_solucion_TN;
                            $hereda_version_final_TN    = $objItemTareaCerrarCasoTN->hereda_version_final_TN;
                            $observacion_detalle        = $objItemTareaCerrarCasoTN->observacion_detalle;

                            $detalle                = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($detalle_id_TN);
                            $estadoTareaSeguimiento = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($detalle_id_TN);
                            
                            if ($codEmpresa == 10)
                            {
                                $arrayDetalleTareas = $objItemTareaCerrarCasoTN->detalle;

                                foreach ( $arrayDetalleTareas as $objtItemDetalleTarea )
                                {
                                    $intIdDetalleHist           = $objtItemDetalleTarea->idDetalleHist;
                                    $intEsSolucionTNDet         = $objtItemDetalleTarea->es_solucion_TN_det;
                                    $intHeredaVersionFinalTNDet = $objtItemDetalleTarea->hereda_version_final_TN_det;

                                    $objDetalleHist = $emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')->find($intIdDetalleHist);
                                    
                                    if ($intEsSolucionTNDet==1)
                                    {
                                        $strEsSolucionDet = "S";
                                        $boolBanEsSolucion   = true;
                                    }
                                    else
                                    {
                                        $strEsSolucionDet = "N";
                                    }
                                    
                                    $objDetalleHist->setEsSolucion($strEsSolucionDet);
                                    $emSoporte->persist($objDetalleHist);
                                    $emSoporte->flush();
                                    
                                }
                            }

                            if($es_solucion_TN==1 || $boolBanEsSolucion)
                            {
                                $observacionEsSolucion  = "Tarea ( SOLUCIONA CASO )";
                                $stringEsSolucion       = "S";
                            }
                            else
                            {
                                $stringEsSolucion       = "N";
                                $observacionEsSolucion  = "Tarea ( NO SOLUCIONA CASO )";
                            }
                            
                            if($hereda_version_final_TN==1)
                            {
                                $observacionVersionFinal=$observacion_detalle;
                            }

                            //Se ingresa el seguimiento de la tarea
                            $arrayParametrosHist["strObservacion"]  = $observacionEsSolucion;
                            $arrayParametrosHist["strEstadoActual"] = $estadoTareaSeguimiento;     
                            $arrayParametrosHist["intDetalleId"]    = $detalle_id_TN;

                            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                            
                            $detalle->setEsSolucion($stringEsSolucion);
                            $emSoporte->persist($detalle);
                            $emSoporte->flush();
                        }
                        
                        $emSoporte->getConnection()->commit();
                        
                        $fechaFinalizacion = $emSoporte->getRepository('schemaBundle:InfoCaso')->getFechaTareaSolucion($intIdCaso,"TN");
                        if($fechaFinalizacion && $fechaFinalizacion[0]['fecha'] != "")
                        {                   
                            $fechaFinA = explode(" ", $fechaFinalizacion[0]['fecha']);

                            $fechaFin = $fechaFinA[0];
                            $horaFin  = $fechaFinA[1];

                            $fechaS = explode("-", $fechaFin);
                            $horaS  = explode(":", $horaFin);

                            $fechaFinal = $fechaS[2] . '-' . $fechaS[1] . '-' . $fechaS[0];
                            $horaFinal  = $horaS[0]  . ':' . $horaS[1];
                        }
                        
                        $resultado = json_encode(array( 'success'                   =>true,
                                                        'fechaFinal'                =>$fechaFinal,
                                                        'horaFinal'                 =>$horaFinal,
                                                        'observacionVersionFinal'   =>$observacionVersionFinal));
                    }
                    catch(Exception $e)
                    {
                        $emSoporte->getConnection()->rollback();
                        $emSoporte->getConnection()->close();
                        $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
                    }
                }
                else
                {
                    $resultado = json_encode(array('success'=>false,'mensaje'=>"No hay tareas"));
                }
            }
            else
            {
                $resultado = json_encode(array('success'=>false,'mensaje'=>"No hay tareas"));
            }
        }
        else
        {
            $resultado = json_encode(array('success'=>false,'mensaje'=>"No hay tareas"));
        }
        $respuesta->setContent($resultado);        
        return $respuesta;
    }
    
    
    /**
     * solicitarInformeEjecutivoAction
     *
     * solicitar la generacion de informe ejecutivo
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2018
     *
     * @author Modificado - Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 06-06-2018 - Se realizan cambios para controlar que no se repitan los numero de las tareas
     *
     * @author Modificado - Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 07-06-2018 Se realiza cambios para validar que solo el que solicita el reporte pueda finalizar la tarea
     * 
     * @author Modificado - John Vera <rcabrera@telconet.ec>
     * @version 1.3 22-06-2018 Se valida que cuando sea un caso de backbone muestre los valores resumidos.
     * 
     * @author Modificado - Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.4 23-12-2020 Se migra la lógica a un service soporteService.solicitarInformeEjecutivo
     */
    public function solicitarInformeEjecutivoAction()
    {

         $arrayDetalles = array();

        try 
        {

            $objResponse = new JsonResponse();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objRequest = $this->getRequest();
            $objSession = $objRequest->getSession();
            $intIdCaso = $objRequest->get('idCaso');
            $intIdComunicacion = "";
            $intPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
            $intDepartamentoOrigen = $objSession->get('idDepartamento');

            $arrayParametros['idPersonaEmpresaRol'] = $intPersonaEmpresaRol;
            $arrayParametros['idDepartamento']      = $intDepartamentoOrigen;
            $arrayParametros['user']                = $objSession->get('user');
            $arrayParametros['clientIp']            = $objRequest->getClientIp();
            $arrayParametros['idCaso']              = $intIdCaso;
            
            $serviceSoporte = $this->get('soporte.SoporteService');
            $arrayResultado = $serviceSoporte->solicitarInformeEjecutivo($arrayParametros);
            
            $objResponse->setContent(json_encode($arrayResultado));
            return $objResponse;
            
        } catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
            $objResponse->setContent(json_encode(array('status' => 'ERROR', 'mensaje' => $ex->getMessage())));
            return $objResponse;
        }
    }
    
    
    
    /**
    * ingresarPreguntaRespuesta
    *
    * Ingresar las preguntas con sus respuestas al sistema
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.2 15-03-2021 - Actualización: Se valida que al guardar el pdf retorne la url del archivo guardado
    * 
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.1 11-01-2021 - Actualización: Se agrega order by descendente al obtener el último idDocumentoRelacion del caso
    *
    * @return jsonResponse $respuest
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 23-05-2016
    *
    */        
    public function ajaxReasignarInformeEjecutivoAction()
    {
        try
        {
            
            $objResponse    = new JsonResponse();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objRequest     = $this->getRequest();
            $intIdCaso      = $objRequest->get('idCaso');
            $objSession     = $objRequest->getSession();
            
            $intEmpresa         = $objSession->get('idEmpresa');
            $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
            $strUser            = $objSession->get('user');
            $strHost            = $objRequest->getClientIp();
            
            $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
            
            
            $objDocumentoRelacion  = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                    ->findOneBy(array(  'casoId' => $intIdCaso, 
                                                                        'estado' => 'Activo', 
                                                                        'modulo' => 'SOPORTE'),array('id'=>'desc'));

            if(!is_object($objDocumentoRelacion))
            {
                throw new \Exception('No existe documento relacion.');
            }
            
            
                 
            $objPersonaEMpresaRol = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->find($objDocumentoRelacion->getPersonaEmpresaRolId());
            
            if(!is_object($objPersonaEMpresaRol))
            {
                throw new \Exception('No existe persona empresa rol.');
            }
            
            
            $arrayParametros = array('idEmpresa'             => $intEmpresa,
                                     'prefijoEmpresa'        => $strPrefijoEmpresa,
                                     'id_detalle'            => $objDocumentoRelacion->getDetalleId(),
                                     'motivo'                => 'Se finalizo la edicion del informe y se reasigna.',
                                     'departamento_asignado' => $objPersonaEMpresaRol->getDepartamentoId(),
                                     'empleado_asignado'     => $objPersonaEMpresaRol->getPersonaId()->getId().'@@'.$objPersonaEMpresaRol->getId(),
                                     'tipo_asignado'         => 'EMPLEADO',
                                     'id_departamento'       => $objPersonaEMpresaRol->getDepartamentoId(),
                                     'user'                  => $strUser,
                                     'clientIp'              => $strHost );
            
            /* @var $serviceSoporte SoporteService */
            $serviceSoporte = $this->get('soporte.SoporteService');
            $arrayResultado = $serviceSoporte->reasignarTarea($arrayParametros);
            
            if(!$arrayResultado['success'])
            {
                throw new \Exception($arrayResultado['mensaje']);                
            }
           
            //consulto la encuesta para actualizar su estado
            $objEncuesta  = $emComunicacion->getRepository('schemaBundle:InfoEncuesta')->find($objDocumentoRelacion->getEncuestaId());
            
            if(is_object($objEncuesta))
            {
                $objEncuesta->setEstado('Activo');
                $emComunicacion->persist($objEncuesta);                        
                $emComunicacion->flush();    
                
            }
            
            //adjunto el documento a la tarea
                                   
            $strNombreDocumento = 'informe-ejecutivo-' . $intIdCaso . '-' . trim(date("Y-m-d")) .'-'.trim(date("h-i-s")). '.pdf';
            
            //genero el pdf
            $arrayParametro['intCaso'] = $intIdCaso;
            $arrayParametro['strNombreDocumento'] = $strNombreDocumento;
            $arrayParametro['strPrefijoEmpresa'] = $strPrefijoEmpresa;

            $strUrlDocumento = $this->guardarPdf($arrayParametro);
            if(!$strUrlDocumento || $strUrlDocumento == 'ERROR')
            {
                throw new \Exception("Error al guardar documento en la reasignación de informe ejecutivo");                
            }
            $objTipoDoc = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');     
            //InfoDocumento
            $objDocumento = new InfoDocumento();
            $objDocumento->setTipoDocumentoId($objTipoDoc);
            $objDocumento->setTipoDocumentoGeneralId(8);
            $objDocumento->setNombreDocumento('Informe Ejecutivo Caso : ' . $intIdCaso);
            $objDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
            $objDocumento->setUbicacionFisicaDocumento($strUrlDocumento);
            $objDocumento->setEstado('Activo');
            $objDocumento->setEmpresaCod($intEmpresa);
            $objDocumento->setFechaDocumento(new \DateTime('now'));
            $objDocumento->setUsrCreacion($strUser);
            $objDocumento->setFeCreacion(new \DateTime('now'));
            $objDocumento->setIpCreacion($strHost);
            $emComunicacion->persist($objDocumento);
            $emComunicacion->flush();

            //InfoDocumentoRelacion
            $objDocumentoRelacionTarea = new InfoDocumentoRelacion();
            $objDocumentoRelacionTarea->setDocumentoId($objDocumento->getId());
            $objDocumentoRelacionTarea->setModulo('SOPORTE');
            $objDocumentoRelacionTarea->setDetalleId($objDocumentoRelacion->getDetalleId());
            $objDocumentoRelacionTarea->setUsrCreacion($strUser);
            $objDocumentoRelacionTarea->setFeCreacion(new \DateTime('now'));
            $objDocumentoRelacionTarea->setEstado('Activo');
            $emComunicacion->persist($objDocumentoRelacionTarea);
            $emComunicacion->flush();
            
            $emComunicacion->getConnection()->commit();            

            $objResponse->setContent(json_encode(array('status' => 'OK', 'mensaje' => $arrayResultado['asignado'])));
            

            return $objResponse;
        }
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
            $objResponse->setContent(json_encode(array('status' => 'ERROR', 'mensaje' => $ex->getMessage())));
            return $objResponse;  
        }      
        
        
    }
    
    
    /**
     * ingresarPreguntaRespuesta
     *
     * Ingresar las preguntas con sus respuestas al sistema
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     *
     */    

    public function ingresarPreguntaRespuesta($arrayParametro)
    {
        $strPregunta    = $arrayParametro['strPregunta'];
        $intEncuesta    = $arrayParametro['intEncuesta'];
        $strRespuesta   = $arrayParametro['strRespuesta'];       
        $strUser        = $arrayParametro['strUser'];        
        $strHost        = $arrayParametro['strHost'];
                
        try
        {            

            $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");

            $objPregunta = $emComunicacion->getRepository('schemaBundle:AdmiPregunta')->findOneBy(array(  'pregunta' => $strPregunta, 
                                                                                                            'estado' => 'Activo' ));

            if(is_object($objPregunta))
            {
                $objEncuestaPregunta = new InfoEncuestaPregunta();
                $objEncuestaPregunta->setPreguntaId($objPregunta->getId());
                $objEncuestaPregunta->setEncuestaId($intEncuesta);
                $objEncuestaPregunta->setValor($strRespuesta);
                $objEncuestaPregunta->setEstado('Activo');
                $objEncuestaPregunta->setFeCreacion(new \DateTime('now'));
                $objEncuestaPregunta->setUsrCreacion($strUser);
                $objEncuestaPregunta->setIpCreacion($strHost);
                $emComunicacion->persist($objEncuestaPregunta);
                $emComunicacion->flush();
            }

            return 'OK';
        
        } 
        catch (Exception $ex) 
        {
            return $ex->getMessage();
        }
    }
    
    
    /**
     * solicitarInformeEjecutivoAction
     *
     * solicitar la generacion de informe ejecutivo
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     *
     */    
    public function getInformeEjecutivoAction()
    {       
        try
        {
            
            $objResponse    = new JsonResponse();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objRequest     = $this->getRequest();
            $intIdCaso      = $objRequest->get('idCaso');

            $arrayEncontrados = $this->obtenerPreguntasCaso($intIdCaso);

            if(count($arrayEncontrados) > 0)
            {
                $arrayRespuesta = array('total' => count($arrayEncontrados), 'encontrados' => $arrayEncontrados);
            }
            else
            {
                $arrayRespuesta = array('total' => 0, 'encontrados' => '');
            }
            
            $objResponse->setContent(json_encode($arrayRespuesta));
             
            return $objResponse;

        }
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
            return $objResponse->setContent('ERROR');
        }
        
    }    
    
    
    /**
     * solicitarInformeEjecutivoAction
     *
     * solicitar la generacion de informe ejecutivo
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     *
     */    
    public function editarInformeEjecutivoAction()
    {

        try
        {

            $objResponse = new JsonResponse();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objRequest = $this->getRequest();
            $intEncuestaPregunta = $objRequest->get('idEncuestaPregunta');
            $strRespuesta = $objRequest->get('respuesta');
            $arrayResult = array('status' => 'OK', 'mensaje' => 'Transaccion Exitosa');

            $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");

            $objEncuestaPregunta = $emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')->find($intEncuestaPregunta);

            if(is_object($objEncuestaPregunta))
            {
                $objEncuestaPregunta->setValor($strRespuesta);
                $emComunicacion->persist($objEncuestaPregunta);
                $emComunicacion->flush();
            }
            else
            {
                $arrayResult = array('status' => 'ERROR', 'mensaje' => 'No se encuentra el registro.');
            }

            $objResponse->setContent(json_encode($arrayResult));

            return $objResponse;
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
    }
    
    /**
     * obtenerPreguntasCaso
     *
     * funcion que obtiene las preguntas y respuestas de un caso
     * 
     * @return array $arrayEncontrados
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     *
     */      

    public function obtenerPreguntasCaso($intIdCaso)
    {
        $i = 1;
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");

        $objEncuesta = $emComunicacion->getRepository('schemaBundle:InfoEncuesta')->findOneBy(array('codigo' => $intIdCaso,
                                                                                                    'descripcionEncuesta' => 'CASO'));
        if(is_object($objEncuesta))
        {
            $arrayPreguntaEncuesta = $emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                                    ->findBy(array('encuestaId' => $objEncuesta->getId(), 'estado' => 'Activo'));

            foreach($arrayPreguntaEncuesta as $objPreguntaResp)
            {
                $intCont = $i++;
                $objPregunta = $emComunicacion->getRepository('schemaBundle:AdmiPregunta')->find($objPreguntaResp->getPreguntaId());

                $arrayEncontrados[] = array(
                    'idEncuestaPregunta'=> $objPreguntaResp->getId(),
                    'numero'            => $intCont,
                    'pregunta'          => $objPregunta->getPregunta(),
                    'respuesta'         => $objPreguntaResp->getValor()
                );
            }
        }

        return $arrayEncontrados;
    }

    /**
     * solicitarInformeEjecutivoAction
     *
     * solicitar la generacion de informe ejecutivo
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     * 
     * Se actualizan llamados de  logos de TN.
     * Se agrega fechas para nuevos formatos. 
     * se elimina error_log
     * 
     * @author Wilmer Vera  <wvera@telconet.ec>
     * @version 1.1 23-09-2021
     *
     */    
    
    public function generarPdfInformeEjecutivoAction()
    {

        try
        {

            $objResponse = new JsonResponse();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objRequest = $this->getRequest();
            $objSession = $objRequest->getSession();
            $intIdCaso = $objRequest->get('idCaso');
            $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');


            $arrayEncontrados = $this->obtenerPreguntasCaso($intIdCaso);

            //segun el prefijo indico la imagen que saldrá en el pdf
            $strUrlImagenes = $this->container->getParameter('imageServer');
            if($strPrefijoEmpresa == 'MD')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_netlife_big.jpg";
            }
            elseif($strPrefijoEmpresa == 'EN')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_ecuanet.png";
            }
            elseif($strPrefijoEmpresa == 'TTCO')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_transtelco_new.jpg";
            }
            elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/sit/notificaciones/logo-tn.png";
            }
            else
            {
                //CONDICIÓN SIN PROGRAMACIÓN
            }

            $objHtml = $this->renderView('soporteBundle:info_caso:informeEjecutivo.html.twig', array(
                'arrayPreguntasInforme' => $arrayEncontrados));
            
            $objFooter = $this->renderView('soporteBundle:info_caso:footer.html.twig', array(
                    //Variables for the template
                ));
            
           $objHeader = $this->renderView('soporteBundle:info_caso:header.html.twig', array(
                'logoEmpresa' => $strLogoEmpresa ,
                'strFecha' => date("m/d/y") ));


            $arrayOptions = [
                'footer-html'   => $objFooter,
                'footer-right'  => '[page]',
                'header-html'   => $objHeader,
                'margin-top'    => 45,
                'margin-right'  => 20,
                'margin-bottom' => 20,
                'margin-left'   => 20
            ];

            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($objHtml, $arrayOptions), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=informe-ejecutivo-' . $intIdCaso . '-' . trim(date("Y-m-d")) . '.pdf',
                )
            );
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            return $objResponse->setContent('ERROR');
        }
    }
    
    /**
     * guardarPdf
     *
     * guarda pdf en una ruta especifica
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 15-03-2021 - Actualización: Se cambiar forma de guardar archivos pdf, ahora guarda por medio del microservicio nfs en gluster
     * 
     * @return jsonResponse $respuest
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-05-2016
     *
     */        
    
    public function guardarPdf($arrayParametro)
    {
        $serviceUtil    = $this->get('schema.Util');
        try
        {
            $intIdCaso          = $arrayParametro['intCaso'];
            $strNombreDocumento = $arrayParametro['strNombreDocumento'];
            $strPrefijoEmpresa  = $arrayParametro['strPrefijoEmpresa'];
            $strUser            = $arrayParametro['strUser'];
            $objServiceUtil     = $this->get('schema.Util');

            $arrayEncontrados = $this->obtenerPreguntasCaso($intIdCaso);

            //segun el prefijo indico la imagen que saldrá en el pdf
            $strUrlImagenes = $this->container->getParameter('imageServer');
            if($strPrefijoEmpresa == 'MD')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_netlife_big.jpg";
            }
            elseif($strPrefijoEmpresa == 'EN')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_ecuanet.png";
            }
            elseif($strPrefijoEmpresa == 'TTCO')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/telcos/logo_transtelco_new.jpg";
            }
            elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
            {
                $strLogoEmpresa = $strUrlImagenes . "/others/sit/notificaciones/logo-tn.png";
            }
            else
            {
                //CONDICIÓN SIN PROGRAMACIÓN
            }

            $objHtml = $this->renderView('soporteBundle:info_caso:informeEjecutivo.html.twig', array(
                'arrayPreguntasInforme' => $arrayEncontrados));
            
            $objFooter = $this->renderView('soporteBundle:info_caso:footer.html.twig', array(
                   //Variables for the template
                ));
            
            $objHeader = $this->renderView('soporteBundle:info_caso:header.html.twig', array(
                'logoEmpresa' => $strLogoEmpresa ,
                'strFecha' => date("m/d/y") ));

            $arrayOptions = [
                'footer-html'   => $objFooter,
                'footer-right'  => '[page]',
                'header-html'   => $objHeader,
                'margin-top'    => 45,
                'margin-right'  => 20,
                'margin-bottom' => 20,
                'margin-left'   => 20
            ];

            $strApp       = "TelcosWeb";
            $strSubModulo = "InformeEjecutivo";

            $strFileBase64 = $this->get('knp_snappy.pdf')->getOutputFromHtml($objHtml, $arrayOptions);
            $strFileBase64 = base64_encode($strFileBase64);
                            //####################################
                            //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                            //####################################
                            $arrayParamNfs = array(
                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                'strApp'               => $strApp ,
                                'arrayPathAdicional'   => [],
                                'strBase64'            => $strFileBase64,
                                'strNombreArchivo'     => $strNombreDocumento,
                                'strUsrCreacion'       => $strUser,
                                'strSubModulo'         => $strSubModulo);

                            $arrayRespNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);
                            //##################################
                            //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                            //##################################
                            if ($arrayRespNfs['intStatus'] == 200 )
                            {
                                return $arrayRespNfs['strUrlArchivo'];
                            }
                            else
                            {
                                throw new \Exception('Ocurrio un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                            }
            
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoCasoController->guardarPdf',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpSession);
            return '';
        }
    }

    /**
     * verSeguimientoTareasXCasoAction
     *
     * Esta función retorna la información de los seguimientos de una tarea
     * 
     * @since 1.0
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-08-2017 Se realizan ajustes a la consulta de los seguimientos invocada desde este función
     * 
     * @return json $objRespuesta
     *
     */
    public function verSeguimientoTareasXCasoAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->getRequest();
        $intIdCaso      = $objRequest->get('id_caso');
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $strJsonData    = $emSoporte->getRepository('schemaBundle:InfoDetalleSeguimiento')
                                    ->getJSONTareasSeguimientosPorCriterios(array("intIdCaso" => $intIdCaso));
        $objResponse->setContent($strJsonData);
        return $objResponse;
    }

    /**
     * putServicioAfectadoAction
     * Función que permite afectar un servicio.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0
     * @since 12-07-2018
     * @return json $objResponse
     */
    public function putServicioAfectadoAction()
    {
        $arrayResultado         = null;
        $objPeticion            = $this->get('request');
        $objSoporteService      = $this->get('soporte.SoporteService');
        $arrayParametros        = array(
                                        'peticion'          => $objPeticion,
                                        'afectarServicio'   => json_decode($objPeticion->get('jsonAfectadosServicios'),true),
                                        'idCaso'            => $objPeticion->get('casoId')
                                        );
        $arrayResultado = $objSoporteService->putServicioAfectado($arrayParametros);

        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * getCasosMovilAction
    * Método que llena el grid con los casos creados desde el móvil.
    *
    * @Secure(roles="ROLE_78-1")
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.0 - 06-11-2018
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.1 - 07-07-2020 Existe afectación con la variable buscaPorArbolHipotesis al momento de consultar los casos que se crea
    *                         desde la app telco manager se setea un valor por default.
    */
    public function getCasosMovilAction()
    {
        $arrayRolesPermitidos = array();

        $strOrigen   = "casosMoviles";
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();

        $boolClienteSesion = true;

        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');

        $strBuscarPorArbolHipotesis = 'N';

        $strPrefijoEmpresaSession       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdCantonUsrSession          = 0;
        $intIdOficinaSesion             = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        if($intIdOficinaSesion)
        {
            $objOficinaSesion           = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            if(is_object($objOficinaSesion))
            {
                $intIdCantonUsrSession  = $objOficinaSesion->getCantonId();
            }
        }
        $intIdDepartamentoUsrSession    = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $entityItemMenu                 = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("78", "1");
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("EMPRESA_APLICA_PROCESO", "", "", "","CONSULTA_ARBOL_HIPOTESIS", "", "", "","",$strEmpresaCod);
        if($arrayAdmiParametroDet['valor2']==='S')
        {
            $strBuscarPorArbolHipotesis = 'S';
        }

        return $this->render('soporteBundle:info_caso:index.html.twig', array(
            'strOrigen'                     => $strOrigen,
            'item'                          => $entityItemMenu,
            'rolesPermitidos'               => $arrayRolesPermitidos,
            'clienteSesion'                 => $boolClienteSesion,
            'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
            'intIdCantonUsrSession'         => $intIdCantonUsrSession,
            'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
            'buscaPorArbolHipotesis'        => $strBuscarPorArbolHipotesis
		));
    }

    /**
     * Función que que obtiene todo los casos aperturados de un cliente.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 29-04-2019
     *
     * @return $objRespuesta
     */
    public function getCasosAperturadosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceUtil    = $this->get('schema.Util');
        $objPeticion    = $this->get('request');
        $strUserSession = $objPeticion->getSession()->get('user');
        $strIpSession   = $objPeticion->getClientIp();
        $intIdCso       = $objPeticion->get('idCaso');

        try
        {
            $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoCaso")
                    ->getObtenerCasosClientes(array ('intIdCaso' => $intIdCso,
                                                    'objContainer' => $this->container));

            if ($arrayRespuesta['status'] === 'fail')
            {
                throw new \Exception($arrayRespuesta['message']);
            }

            $arrayCasos = (array) json_decode($arrayRespuesta['result']);

            if ($arrayCasos['status'] === 'fail')
            {
                throw new \Exception('Error : '.$arrayCasos['message']);
            }

            $objResultado = json_encode(array ('status' => true,
                                               'total'  => count($arrayCasos['result']),
                                               'casos'  => $arrayCasos['result']));
        }
        catch(\Exception $objException)
        {
            $strMessage = 'Error al obtener los casos aperturados';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
            }

            $serviceUtil->insertError('Telcos+',
                                      'InfoCasoController->getCasosAperturadosAction',
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpSession);

            $objResultado = json_encode(array ('status'  => false,
                                               'message' => $strMessage));
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

    /**
     * Revisa las soluciones realizadas para el caso
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 - 29/01/2021
     */
    public function revisarSolucionesAction()
    {
        $emSoporte   = $this->getDoctrine()->getManager("telconet_soporte");
        $objRequest  = $this->get('request');

        $intIdCaso   = $objRequest->get('id_caso') ? $objRequest->get('id_caso') : '';
        
        $arrayParametros['idCaso'] = $intIdCaso;

        $arraySoluciones = $emSoporte->getRepository("schemaBundle:InfoDetalleHistorial")->getHistorialPorCaso($arrayParametros);
        
        return new JsonResponse([
            'total' => count($arraySoluciones),
            'encontrados' => $arraySoluciones
        ]);
    }

    public function getActividadesPuntoAfectadoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil    = $this->get('schema.Util');
        $objPeticion    = $this->get('request');
        $strUserSession = $objPeticion->getSession()->get('user');
        $strCodEmpresa  = $objPeticion->getSession()->get('idEmpresa');
        $strIpSession   = $objPeticion->getClientIp();
        $intLimit       = $objPeticion->get("limit");
        $intStart       = $objPeticion->get("start");
        $strLogin       = $objPeticion->getSession()->get("ptoCliente")['login'];

        try
        {

            $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('TIEMPO_AFECTACIONES_LOGIN','SOPORTE','','CANTIDAD_EN_HORAS','','','','','',$strCodEmpresa);

            $objFechaActual = new \DateTime('now');
            $intNumeroDiaSemana = $objFechaActual->format('N');

            $intTiempoHoras = $arrayAdmiParametroDet['valor'.$intNumeroDiaSemana];
            $objFechaActual->modify('-'.$intTiempoHoras.' hours');          

            $arrayParametros   = array();
            $arrayParametros['limit'] = $intLimit;
            $arrayParametros['start'] = $intStart;
            $arrayParametros['fechaPivote'] = $objFechaActual->format('d/m/Y H:i:s');
            $arrayParametros['login'] = $strLogin;

            $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoCaso")
                    ->getActividadesPuntoAfectado($arrayParametros);

            if ($arrayRespuesta['status'] === 'fail')
            {
                throw new \Exception($arrayRespuesta['message']);
            }

            $objResultado = json_encode($arrayRespuesta);
        }
        catch(\Exception $objException)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoCasoController->getActividadesPuntoAfectadoAction',
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpSession);
            $objResultado = json_encode($arrayRespuesta);
       
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

    /**
     * getNivelesCriticidadAction
     * Función que obtiene los Niveles de Criticidad que puede tener un Caso
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0
     * @since 02-02-2022
     * @return json $objResponse
     */
    public function getNivelesCriticidadAction()
    {
        $objSoporteService      = $this->get('soporte.SoporteService');
        $arrayResultado = $objSoporteService->getNivelesCriticidad();
        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * actualizarAction
     * Función que actualiza los datos modificables de un Caso no creado en Telcos
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0
     * @since 03-02-2022
     * @return json $objResponse
     */
    public function actualizarAction()
    {
        $objRequest  = $this->get('request');
        $objSoporteService = $this->get('soporte.SoporteService');
        $arrayData = array('idCaso' => $objRequest->get('idCaso'),
                           'request' => $objRequest);
        $arrayResultado = $objSoporteService->actualizaCaso($arrayData);
        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getCantidadCasosExtranetAction
     * Función que obtiene la cantidad de Casos creados en Extranet, e identifica si tienen tareas creadas
     *
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.0
     * @since 15-03-2022
     * @return json $objResponse
     */
    public function getCantidadCasosExtranetAction()
    {
        $objSoporteService  = $this->get('soporte.SoporteService');
        $arrayResultado = $objSoporteService->getCantidadCasosSegunTareas(
                                                                            array(
                                                                                'codEmpresa'          => '10',
                                                                                'origen'              => 'E',
                                                                                'codigoFormaContacto' => 'EXTR'
                                                                            )
                                                                        );
        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
     /**
     * funcion que devuelve los tipos de afectaciones para los casos backbone 
     * marcados como mantenimiento programado
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 - 29/03/2022
     */
    public function getTipoAfectacionAction()
    {
        $arrayParametro = array("tipo_busqueda" => "TIPO_AFECTACION");
        $objSoporteService      = $this->get('soporte.SoporteService');
        $arrayResultado = $objSoporteService->getTipoParametrosCasos($arrayParametro);
        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }

    /**
     * funcion que devuelve los tipos de notificaciones para los casos backbone 
     * marcados como mantenimiento programado
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 - 29/03/2022
     */
    public function getTipoNotificacionAction()
    {
        $arrayParametro = array("tipo_busqueda" => "TIPO_NOTIFICACION");
        $objSoporteService      = $this->get('soporte.SoporteService');
        $arrayResultado = $objSoporteService->getTipoParametrosCasos($arrayParametro);
        $objResponse    = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }


    /**
     * Función que devuelve las Juridicciones PE activas
     * @author David Valdivieso <dvaldiviezon@telconet.ec>
     * @version 1.0 - 18/04/2023
     */
    public function getJurisdiccionesPeAction(){
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');

	    $peticion = $this->get('request');
	    $session  = $peticion->getSession();
	    $strCodEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
	    	    
	    $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'JURISDICCIONES_PE',
                                                              'estado'          => 'Activo'));
        
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                             'estado'      => 'Activo'));
            
        $arrayJurisdicciones = array();
        foreach ($arrayAdmiParametroDet as $objAdmiParametroDet)
        {
            $arrayJurisdicciones[] = array('jurisdiccion_pe' => $objAdmiParametroDet->getValor1());
        }

        $arrayResultado = array(
            'total' => count($arrayJurisdicciones),
            'encontrados' => $arrayJurisdicciones
        );

        $objJson = json_encode($arrayResultado);
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;
    }

}
