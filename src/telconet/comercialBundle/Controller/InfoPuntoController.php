<?php

namespace telconet\comercialBundle\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\administracionBundle\Service\UtilidadesService;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoPuntoFormaContacto;
use telconet\schemaBundle\Form\InfoPuntoType;
use telconet\schemaBundle\Form\InfoPuntoArchivoDigitalType;
use telconet\schemaBundle\Entity\InfoContactoServicio;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\comercialBundle\Controller\ClienteController;
use telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPuntoHistorial;
use telconet\schemaBundle\Form\InfoDocumentoType;
use Doctrine\Common\Collections\ArrayCollection;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\AdmiNumeracion;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\soporteBundle\Service\ProcesoService;
use telconet\comercialBundle\Service\ClienteService;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\ReturnResponse;

use telconet\planificacionBundle\Service\PlanificarService;

/**
 * InfoPunto controller.
 *
 */
class InfoPuntoController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_9-1")
    */ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('schemaBundle:InfoPunto')->findAll();

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        return $this->render('comercialBundle:infopunto:index.html.twig', array(
            'entities' => '',
            'item' => $entityItemMenu
        ));
    }
 
    /**
    * @Secure(roles="ROLE_9-7377")
    * 
    * ajaxEjecutarEmergenciaSanitariaPto Opción que permite diferir Facturas por Emergencia Sanitaria para al punto en sesion.    
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-06-2020
    *                
    * @param intIdPunto ,            Id Punto
    * @param intMesesDiferir,        Numero de meses a diferir
    * 
    * @return $strResponse
    *
    */    
    public function ajaxEjecutarEmergenciaSanitariaPtoAction()
    {                      
        $objRequest               = $this->getRequest();
        $objSesion                = $objRequest->getSession();
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $emFinanciero             = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral                = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura        = $this->getDoctrine()->getManager("telconet_infraestructura");
        $intIdPunto               = $objRequest->get('intIdPunto');       
        $strMesesDiferir          = $objRequest->get('strMesesDiferir');                       
        $strUsrCreacion           = $objSesion->get('user');
        $strCodEmpresa            = $objSesion->get('idEmpresa');        
        $strIpCreacion            = $objRequest->getClientIp();                        
        $arrayParametros          = array();                        
        $arrayParametros          = array(
                                          'intIdPunto'             => $intIdPunto,
                                          'strMesesDiferir'        => $strMesesDiferir,
                                          'strUsrCreacion'         => $strUsrCreacion,
                                          'strCodEmpresa'          => $strCodEmpresa,
                                          'strIpCreacion'          => $strIpCreacion,
                                          'strMotivo'             => 'EjecutarEmerSanitPto'
                                    );    
               
        //Validación o bloqueo General del proceso de diferido individual, si existe un proceso masivo tipo 'EjecutarEmerSanit' y en estado pendiente
        $arrayParamProcesoMasivo               = array();
        $arrayParamProcesoMasivo['strProceso'] = 'EjecutarEmerSanit';
        $arrayParamProcesoMasivo['strEstado']  = "Pendiente";
        $strCantidadProcesoMasivo = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                      ->getProcesosPendientes($arrayParamProcesoMasivo);

        if ($strCantidadProcesoMasivo > 0)
        {
            $strResponse = "Diferimiento masivo en proceso. Favor intente más tarde";
            return new Response($strResponse);       
        }
        
        //Validaciones para permitir Procesamiento Individual de Diferidos.       
        $strParametroPadre          = 'PROCESO_EMER_SANITARIA';
        $strModulo                  = 'COMERCIAL';
        $strDescripcion             = 'VALIDA_PERMITE_PROCESO_INDIVIDUAL';
        $strPermiteProcIndividual   = 'N';
        //Obtiene parametro para determinar si se realiza Validacion para procesamiento Individual por punto VALIDA_PERMITE_PROCESO_INDIVIDUAL.
        //Si es S pregunta por el parametro NUMERO_PROCESO_PERMITIDOS si es N no realiza ninguna acción. 
        $arrayParametroDet       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne($strParametroPadre, $strModulo, '', $strDescripcion, '', '', '', '', '', $strCodEmpresa);
        
        if( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
        {
            $strPermiteProcIndividual = $arrayParametroDet["valor1"];
        }
        if($strPermiteProcIndividual == 'S')             
        {
            //Permite ejecutar Proceso de Diferidos luego de #(N) proceso(s) masivo(s) ejecutado(s) en estado Pendiente/Finalizado por punto cliente
            $strDescripcion              = 'NUMERO_PROCESO_PERMITIDOS';
            $intNumeroProcesosPermitidos = 0;
            $arrayParametroDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne($strParametroPadre, $strModulo, '', $strDescripcion, '', '', '', '', '', $strCodEmpresa);
        
            if( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
            {
                $intNumeroProcesosPermitidos = intval($arrayParametroDet["valor1"]);
            }
            //obtiene la cantidad de Procesos de Diferidos de Facturas que se han generado por punto, se considera en estado Pendiente y Finalizado.
            $intCantProcDiferidoPorPto = 0;                        
            $intCantProcDiferidoPorPto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->getCantProcDiferidoPorPto(array('intIdPunto' => $intIdPunto)); 
         
            $intCantProcDiferidoPorPto = (!empty($intCantProcDiferidoPorPto) ? $intCantProcDiferidoPorPto : 0 );  
        
            if($intCantProcDiferidoPorPto >= $intNumeroProcesosPermitidos)
            {
               $strResponse = "No se pudo ejecutar Diferido de Facturas. Cliente ya posee deuda diferida";
               return new Response($strResponse); 
            }            
        }
        else
        {
            $strResponse = "No se pudo ejecutar Diferido de Facturas. Opción se encuentra deshabilitada.";
            return new Response($strResponse); 
        }
        //Validación de Documentos NC o NCI en Proceso de Activación
        $intCantidadNcPorPunto = 0;                        
        $intCantidadNcPorPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                              ->getCantidadNcPorPunto(array('intIdPunto' => $intIdPunto)); 
         
        $intCantidadNcPorPunto = (!empty($intCantidadNcPorPunto) ? $intCantidadNcPorPunto : 0 );  
       
        if($intCantidadNcPorPunto>0)
        {
            $strResponse = "No se pudo ejecutar Diferido de Facturas. Tiene Notas Créditos pendientes de Aprobación/Autorización";
            return new Response($strResponse);
        }

        //Validación de Punto Cliente con saldo a favor.
        $intCantidadAntPorPunto = 0;                        
        $intCantidadAntPorPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                               ->getAnticiposPendientes(array('arrayTiposDoc' => array("ANT","ANTS","ANTC"),
                                                                              'strEstado'     => 'Pendiente',
                                                                              'intIdPunto'    => $intIdPunto)); 
         
        $intCantidadAntPorPunto = (!empty($intCantidadAntPorPunto) ? $intCantidadAntPorPunto : 0 );  
       
        if($intCantidadAntPorPunto>0)
        {
            $strResponse = "No se pudo ejecutar Diferido de Facturas. Cliente con saldo a favor realizar cruce manual para diferir la deuda";
            return new Response($strResponse);
        }
        
        //Validación si el servicio mandatorio del punto cumple con los estados parametrizados, "1" si cumple y "0" no cumple.
        $intCantidadServiciosCumple   = 0;                        
        $arrayCantidadServiciosCumple = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                     ->getCumpleEstadosDiferido(array('intPuntoId' => $intIdPunto)); 
         
        $intCantidadServiciosCumple = (!empty($arrayCantidadServiciosCumple[0]['valor']) ? $arrayCantidadServiciosCumple[0]['valor'] : 0 );  
       
        if($intCantidadServiciosCumple == 0)
        {         
            $strResponse = "No se pudo ejecutar Diferido de Facturas. El Punto no posee estado del servicio de internet válido".
                " [". $arrayCantidadServiciosCumple[0]['valores'] . "]";
            return new Response($strResponse);            
        }
        
        //Validación si el punto cumple con las formas de pago parametrizados, "1" si cumple y "0" no cumple.
        $intCantidadFormasPagoCumple   = 0;                        
        $arrayCantidadFormasPagoCumple = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->getCumpleFormaPagoDiferido(array('intPuntoId' => $intIdPunto)); 
         
        $intCantidadFormasPagoCumple = (!empty($arrayCantidadFormasPagoCumple[0]['valor']) ? $arrayCantidadFormasPagoCumple[0]['valor'] : 0 );
       
        if($intCantidadFormasPagoCumple == 0)
        {                        
            $strResponse = "No se pudo ejecutar Diferido de Facturas. El Punto no posee una de las Forma de Pago válidas".
                " [". $arrayCantidadFormasPagoCumple[0]['valores'] . "]";
            return new Response($strResponse);
        }                
        try
        {                                    
            $serviceEmergenciaSanitaria  = $this->get('financiero.EmergenciaSanitaria');
            $strResponse                 = $serviceEmergenciaSanitaria->crearProcesoMasivo($arrayParametros);  
            if ($strResponse == 'OK')
            {
                $arrayParametrosSol  = array();                        
                $arrayParametrosSol  = array(                                                  
                                             'strTipoPma'      => 'EjecutarEmerSanitPto',
                                             'strCodEmpresa'   => $strCodEmpresa,
                                             'strEstado'       => 'Pendiente',
                                             'intIdPunto'      => $intIdPunto, 
                                             'strUsrCreacion'  => $strUsrCreacion,                                       
                                             'strIpCreacion'   => $strIpCreacion
                                            );    
                $arrayResponse  = $serviceEmergenciaSanitaria->crearSolicitudesNci($arrayParametrosSol);
                if ($arrayResponse['strResultado'] == 'OK')
                {
                    $intIdProcesoMasivoCab = $arrayResponse['intIdProcesoMasivoCab'];
                    $arrayParametrosNci  = array();                        
                    $arrayParametrosNci  = array(                                                                                               
                                                 'strCodEmpresa'            => $strCodEmpresa,                                                 
                                                 'strUsrCreacion'           => 'telcos_diferido',                                       
                                                 'strIpCreacion'            => $strIpCreacion,
                                                 'strDescripcionSolicitud'  => 'SOLICITUD DIFERIDO DE FACTURA POR EMERGENCIA SANITARIA',
                                                 'intIdPunto'               => $intIdPunto                                                 
                                                );    
                    $strResponse  = $serviceEmergenciaSanitaria->ejecutaSolDiferido($arrayParametrosNci);               
                    if ($strResponse == 'OK')
                    {
                        $arrayParametrosNdi  = array();                        
                        $arrayParametrosNdi  = array(                                                                                               
                                                 'strCodEmpresa'         => $strCodEmpresa,                                                 
                                                 'strUsrCreacion'        => 'telcos_diferido',                                       
                                                 'strIpCreacion'         => $strIpCreacion,                                                 
                                                 'intIdPunto'            => $intIdPunto,
                                                 'intIdProcesoMasivoCab' => $intIdProcesoMasivoCab
                                                );    
                    $strResponse  = $serviceEmergenciaSanitaria->ejecutaNdiDiferido($arrayParametrosNdi);               
                    }                    
                }                
            }
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al ejecutar el Proceso de Diferido de Facturas, por favor consulte con el Administrador";           
        }
        
        return new Response($strResponse);

    }
    /**
     * @Secure(roles="ROLE_9-6")    
     * Funcion que muestra informacion del punto Clientes y sus servicios contratados
     * Consideraciones: Se verifica si existe o ha cambiado el Punto en sesion si es asi se guarda en sesion el registro 
     * y se consulta la informacion que será usada para cargar al toolbar.
     * 
     * Se modifica agregando funcion que obtenga si se permite o no ingresar a la edicion de la informacion del Punto
     * en base al ROlES o Credenciales, al estado del servicio asociado al login y en base a las Solicitudes de Factibilidad
     * Se agrega opcion Cambio de vendedor que sera visible solo si se posee la Credencial.
     * Consideraciones: 
     * 1) Si posee ROL o Credencial para "Edicion de Punto" solo podra editar la informacion del Punto si este no posee Servicios en estados
     * posteriores a la Factibilidad y si no Posee Solicitudes en estado "Factible, FactibilidadEnProceso, PreAprobacionMateriales,
     * PreFactibilidad"
     * 2) Si Posee Rol o Credencial para opcion "Cambio de Vendedor" podra visualizar pantalla de cambio de vendedor y Contactos
     *
     * @author : telcos
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 10-06-2014
     * @version 1.1 05-03-2015  
     *       
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 07-09-2015
     * Se corrige Bug para que muestre todas las solicitudes de Migracion que tenga el Punto
     * 
     * @author Kenneth Jiménez <kjimenez@telconet.ec>
     * @version 1.3 15-12-2015 nuevo rol para realizar enlace de datos de los servicios
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.4 18-12-2015
     * Se agrega la visualización del Canal y del punto de venta en la Edición del punto del cliente.
     * 
     * Los Canales almacenan en los campos:
     * Valor1 => Identificador del Canal.
     * Valor2 => Descriptivo del Canal.
     * Valor3 => Identificador de grupo 'CANAL' .
     * 
     * Los Puntos de Venta almacenan en los campos:
     * Valor1 => Identificador del Punto de Venta.
     * Valor2 => Descriptivo del Punto de Venta.
     * Valor3 => Identificador del Canal.
     * Valor4 => Descriptivo del Canal.
     * Valor5 => Nombre de la oficina asociada al punto de venta.
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.5 22-03-2016
     * Se agrega validación para mostrar la información del Canal y Punto de venta asociado solo para la empresa MegaDatos.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.6 15-05-2016
     * Se muestra el Nombre del Ejecutivo de Cobranzas para la empresa TN.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.7 22-05-2016
     * Se envía el parámetro $strEsPrepago(S/N) a la vista.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.8 08-06-2016
     * Se inicializan los valores de sesión del cliente
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>       
     * @version 1.9 2016-06-18 Ajuste en uso de varible $objNodoCliente sin inicializar
     *                         y asignación sin inicializar de $strNombreLoginTrasladado
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 2.0 26-06-2016 - Se envía mediante la variable $rolesPermitidos el rol de 'RenovacionPlanes100_100'
     *      
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 2.1 01-06-2016
     * Se agrega el Perfil para el cambio de frecuencia del servicio
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>       
     * @version 2.2 05-07-2016 - Se envía mediante la variable $rolesPermitidos el rol de 'Tn_Editar_Descripcion_Presenta_Factura'
     *      
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 2.3 07-07-2016
     * En caso de ser NULL el Punto Dato Adicional se lo inicializa para mostrar normalmente en el twig.
     * 
     * @author Jesus Bozada<jbozada@telconet.ec>       
     * @version 2.4 10-08-2016
     * Se agrega rol para accion que realiza el cambio de Id de Plan de servicios con estado Activo ó AsignadoTarea
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 2.4 05-08-2016
     * Se setea el valor $rol en caso de no existir rol en sesión.  
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 2.5 16-10-2016 - Se consulta para TN la forma de pago del cliente, para que sea mostrada en la información del punto. Adicional se
     *                           crea el perfil 'PerfilEditarFormaPagoPorPuntoFacturacion'[ROLE_9-4857] que le permitirá al usuario editar la forma
     *                           de pago del punto de facturación.
     * 
     * @author Jesus Bozada<jbozada@telconet.ec>       
     * @version 2.6 10-08-2016    Se agrega recuperación de solicitudes no finalizadas del tipo SOLICITUD AGREGAR EQUIPO
     * @since 2.5
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>       
     * @version 2.7 16-05-2017    Se agrega detalle del Edificio en cuanto a tipo de administracion
     * @since 2.6
     * 
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 2.8 18-05-2017 - Se agrega el rol 'ROLE:9-5317' que corresponde a 'Editar la plantilla de comisionistas' y se consulta el máximo
     *                           valor permitido para el ingreso de las comisiones.
     * 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.9 22-01-2018   Se agrega programación por implementación de traslados y reubicacion en TN
     * @since 2.8
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.9 26-02-2018 - Se adiciona los permisos para actualizar las coordenadas del punto del cliente.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.10 27-02-2018 - Se oculta el boton de actualizar las coordenadas. 
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.11 12-03-2018 - Se agrega permiso para edicion de soluciones para TN
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.12 26-06-2018 - Se agrega nuevo estado: PendienteAutorizar, en la consulta que retorna una alerta en el show del punto,
     *                            con las solicitudes a revizar
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 3.0 06-09-2018 - Se agregan permisos para opciones de producto telefonia fija netvoice
     *         //Cancelar líneas //Agregar líneas //Cambiar línea //Editar línea //Cancelar líneas //Factibilidad líneas //Detalle llamada línea
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 26-10-2018 - Se obtiene la información de la solicitud de aprobación de servicio asociada al cliente
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 3.2
     * @since 28-01-2019 - Se obtiene la información referente al tipo de origen del punto
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.2 07-02-2019 - Se envía variable indicando si se muestra o no la opción de subir contratos externos digitales para puntos
     *                           que contenga algun producto de facturación por consumo agregado dentro de sus servicios, sólo para TN
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 3.3 29-08-2019 - Se agrega permiso VerArchivoInspeccionComercial para ver archivos de inspección del servicio "Wifi Alquiler Equipos".
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 3.4 21-02-2020 - Se consulta si el producto COU LINEA TELEFONIA FIJA tiene la marca de activación simultánea.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.5 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD AGREGAR EQUIPO MASIVO'
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 3.6 22-06-2020 - Se agrega Rol para la opción de Diferido de Facturas por Emergencia Sanitaria por Punto.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.7 31-07-2020 - Se consulta la imagen de la ruta del croquis del punto provonga del NFS remoto.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 3.8 30-11-2020 - Se mejora la validación de activación simultánea.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.9 18-12-2020 - Se agrega perfil: gestionarMapaRecorrido para gestionar el mapa de recorrido del cliente en los
     *                           productos SSID_MOVIL
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 4.0 09-05-2021 - Se agrega validación para saber si el punto en sesión es de un cliente tipo distribuidor.
     * 
     * @author Christian Yunga <cyungat@telconet.ec>
     * @version 4.1 14-01-2023 Se agrega validación de perfiles solo los perfiles permitidos pueden ingresar a esta opcion.
     *                         Si pasa la validación entonces se registrara el inicio de sesion junto con el login del cliente al 
     *                         que se esta consultando  solo para empresa MD.
     *
     * @author Joel Ontuña <jontuna@telconet.ec>
     * @version 4.1 19-12-2022 - Se agrega un array de parámetros para usar el método setSessionByIdPunto
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 4.2 16-02-2023 - Se agrega una validación para que cuando el prefijo de la empresa sea ‘MD’
     *                           y el punto a visualizar se haya creado mediante el flujo ‘Proceso Traslado’ 
     *                           se validará el estado del proceso y en caso de que sea  ‘Pendiente’ 
     *                           se obtendra una redirección al punto de control (Vista ‘Trasladar Servicios’) 
     *                           con los datos pertinentes.
     * @param integer $id   //Id del punto 
     * @param integer $rol  //rol que posee el registro : Pre-cliente / Cliente
     * @see \telconet\schemaBundle\Entity\InfoServicio
     * @see \telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
     * @return Renders a view.
     */
    public function showAction($id, $rol)
    {
        $peticion                = $this->get('request');
        $session                 = $peticion->getSession();
        $em                      = $this->getDoctrine()->getManager();
        $em_seguridad            = $this->getDoctrine()->getManager("telconet_seguridad");
        $emFinanciero            = $this->getDoctrine()->getManager("telconet_financiero");
        $emInfraestructura       = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $emComercial             = $this->getDoctrine()->getManager("telconet");
        $entityItemMenu          = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "1");
        $codEmpresa              = $session->get('idEmpresa');
        $strPrefijoEmpresa       = $session->get('prefijoEmpresa');
        $request                 = $this->getRequest();
        $cliente                 = $request->getSession()->get('cliente');
        $ptoCliente              = $session->get('ptoCliente');
        $boolPermiteEditarPto    = false;
        $tipoRol                 = '';
        $strFormaPagoFacturacion = '';
        $serviceUtil             = $this->get('schema.Util');
        $strUsrCreacion          = $session->get('user');
        $strIpCreacion           = $peticion->getClientIp();
        $strParametroPadre       = 'VALOR_MAXIMO_COMISION_VENTA';
        $strModulo               = 'COMERCIAL';
        $floatValorMaxComision   = 0;
        $arrayParametroDet       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne($strParametroPadre, $strModulo, '', '', '', '', '', '', '', $codEmpresa);
        $serviceTecnico          = $this->get('tecnico.InfoServicioTecnico');
        $strFlagActivacion       = 'N';
        $strFlagActiSimul        = 'N';
        $objPlanificarService    = $this->get('planificacion.planificar');
        $boolCroquisUrl          = false;
        $strNombreArcCroquis     = '';
        $strNombreArcDigital     = '';
        $strLlenoDatosBancario  = 'S';
        $boolValueTrue           = true;
        $boolValueFalse          = false;
        
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $arrayCliente      = $objSession->get('cliente');
        $arrayPtoCliente   = $objSession->get('ptoCliente');
        $strCodEmpresa     = $objSession->get('idEmpresa');
        $serviceTokenCas   = $this->get('seguridad.TokenCas');
        $serviceInfoLog    = $this->get('comercial.InfoLog');
        $arrayDatosCliente = array();
        
        if($strPrefijoEmpresa == 'MD' &&  (true === $this->get('security.context')->isGranted('ROLE_9-6')))
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
            $strOrigen        = '';
            $strMetodo        = '';
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                              'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'ORIGEN',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));

                $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                   'observacion'     => 'DATOS DEL PUNTO',
                                                                   'empresaCod'      => $strCodEmpresa,
                                                                   'estado'          => 'Activo'));           
                if(is_object($objParamDetOrigen))
                {
                    $strOrigen  = $objParamDetOrigen->getValor1();
                }

                if(is_object($objParamDetMetodo))
                {
                    $strMetodo  = $objParamDetMetodo->getValor1();
                }             
            }
            $arrayParametrosLog                   = array();
            $arrayParametrosLog['strOrigen']      = $strOrigen;
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
        else if($strPrefijoEmpresa == 'MD' &&  (false === $this->get('security.context')->isGranted('ROLE_9-6')))
        {
            return $this->render('comercialBundle:infopunto:accesoDenegado.html.twig');
        }
        if($strPrefijoEmpresa == 'MD')
        {

            $strEstadoProceso         = null;
            $objInfoPuntoCaracProceso = null;
            $objInfoPuntoCaracEstado  = null;
            $objInfoPuntoCaracOrigen  = null;
            $boolEsProcesoContinuo    = null;

            $objCaracProcesoContinuo = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                    ->findOneBy(
                                                        array(
                                                            "descripcionCaracteristica" => 'ES_PROCESO_CONTINUO',
                                                            "estado"                    => 'Activo'
                                                        )
                                                    );

            $objCaracEstadoProceso = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                    ->findOneBy(
                                                        array(
                                                            "descripcionCaracteristica" => 'ESTADO_PROCESO_PUNTO',
                                                            "estado"                    => 'Activo'
                                                        )
                                                    );

            $objCaracPuntoOrigen = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                ->findOneBy(
                                                    array(
                                                        "descripcionCaracteristica" => 'PUNTO_ORIGEN_CREACION',
                                                        "estado"                    => 'Activo'
                                                    )
                                                );

            if(!empty($objCaracProcesoContinuo) && is_object($objCaracProcesoContinuo) &&
                !empty($objCaracEstadoProceso) && is_object($objCaracEstadoProceso) &&
                !empty($objCaracPuntoOrigen) && is_object($objCaracPuntoOrigen))
            {
                $objInfoPuntoCaracProceso  = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                            ->findOneBy(
                                                                array(
                                                                    'puntoId'          => $id,
                                                                    'caracteristicaId' => $objCaracProcesoContinuo->getId()
                                                                )
                                                            );
            }

            if(!empty($objInfoPuntoCaracProceso) && is_object($objInfoPuntoCaracProceso) &&
                $objInfoPuntoCaracProceso->getValor() == 'S')
            {
                $objInfoPuntoCaracEstado  = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                            ->findOneBy(
                                                                array(
                                                                    'puntoId'          => $id,
                                                                    'caracteristicaId' => $objCaracEstadoProceso->getId()
                                                                )
                                                            );
            }
if (!empty($objInfoPuntoCaracEstado) && is_object($objInfoPuntoCaracEstado) &&
                $objInfoPuntoCaracEstado->getValor() == 'Pendiente')
            {
                $objInfoPuntoCaracOrigen  = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                            ->findOneBy(
                                                                array(
                                                                    'puntoId'          => $id,
                                                                    'caracteristicaId' => $objCaracPuntoOrigen->getId()
                                                                )
                                                            );
            }

            if (!empty($objInfoPuntoCaracOrigen) && is_object($objInfoPuntoCaracOrigen))
            {
                $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objInfoPuntoCaracOrigen->getValor());
                return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios', 
                                            array('id' => $id, 'rol' => $rol, 'strTipo' => 'continuo', 
                                            'intIdPuntoAnterior' => $objInfoPunto->getId())));
            }
        }


        
        if( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
        {
            $floatValorMaxComision = $arrayParametroDet["valor1"];
        }//( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )

        $session->set('numServicios', 0);
        $session->set('serviciosPunto', '');
        $session->set('datosFinancierosPunto', '');

        if($cliente)
        {
            $tipoRol = $cliente['nombre_tipo_rol'];
        }

        // Si no Hay cliente en sesión se toma el que se recibe por parámetro
        $tipoRol = $tipoRol == '' ? $rol : $tipoRol;

        $entity = $em->getRepository('schemaBundle:InfoPunto')->find($id);

        if( $entity )
        {

            $usrVendedor    = $entity->getUsrVendedor();
            $nombreVendedor = "No identificado";
            if($usrVendedor)
            {
                $objVendedor = $em->getRepository('schemaBundle:InfoPersona')->findOneByLogin($usrVendedor);
                if($objVendedor)
                {
                    $nombreVendedor = $objVendedor->__toString();
                }
            }
        }

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        //Obtengo el total de saldo de Facturas por Punto para el Diferido de Facturas
        $arrayParametrosSaldoFact = array();
        $arraySaldo = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                   ->obtieneSaldoPorPunto($entity->getId());
        
        $arrayParametrosSaldoFact = array('intIdPunto' => $entity->getId());
        $fltTotalSaldoFactPorPto  = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                 ->getTotalSaldoFactPorPto($arrayParametrosSaldoFact);
        
        $fltTotalSaldoFactPorPto = (!empty($fltTotalSaldoFactPorPto) ? round($fltTotalSaldoFactPorPto, 2) : 0 );  
       
        //Obtiene el valor minimo de Factura parametrizado para evaluar las facturas por saldo que ingresan al proceso de Diferido.
        $strParametroPadre       = 'PROCESO_EMER_SANITARIA';
        $strModulo               = 'COMERCIAL';
        $strDescripcion          = 'VALOR_FACT_MIN';
        $fltValorFactMin         = 0;        
        $arrayParametroDet       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne($strParametroPadre, $strModulo, '', $strDescripcion, '', '', '', '', '', $codEmpresa);
        
        if( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
        {
            $fltValorFactMin = intval($arrayParametroDet["valor1"]);
        }
        

        $strRutaCroquis = $entity->getPath();
        $strNombreArcCroquis = basename($entity->getPath());
        $strNombreArcDigital = basename($entity->getPathDigital());

        if(isset($strRutaCroquis) && filter_var($strRutaCroquis, FILTER_VALIDATE_URL))
        {
            $boolCroquisUrl = true;
        }
        //Verifico si para Punto es permitido realizar la Edicion de Punto (Login)
        $boolPermiteEditarPto =  $em->getRepository('schemaBundle:InfoServicio')->verificaPermiteEditarPto($id);

        $idServicioProvieneTraslado = $em->getRepository('schemaBundle:InfoServicio')->obtenerIdServicioProvieneTraslado($id);

        $strNombreLoginTrasladado = "";
        if( $idServicioProvieneTraslado )
        {
            $entityServicioProvieneTraslado = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                ->find($idServicioProvieneTraslado);
            $strNombreLoginTrasladado = $em->getRepository('schemaBundle:InfoServicio')
                ->obtenerLoginProvieneTraslado($entityServicioProvieneTraslado->getValor());
        }

        if($session->get('ptoCliente') != $id)
        {
            $arrayParametrosAdicionales = array();
            $arrayParametrosAdicionales['serviceUtil']  = $this->get('schema.Util');
            $arrayParametrosAdicionales['strIpSession'] = $strIpCreacion;
            $arrayParametrosAdicionales['serviceRDA'] = $this->get('tecnico.RedAccesoMiddleware');

            $em->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($id, $session, $arrayParametrosAdicionales);
            $ptoCliente   = $session->get('ptoCliente');


                if ($codEmpresa == 10)
                {
                    $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                    ->getPuntosFacturacionAndFacturasAbiertasByIdPunto($ptoCliente['id'], $em, $codEmpresa);

                }else if ($codEmpresa == 18 || $codEmpresa == 33)
                {
                    try
                    {
                        $arrayParametros = array();
                        $arrayParametros["intIdPunto"] = $ptoCliente['id'];
                        $arrayParametros["em"] = $em;
                        $arrayParametros["codEmpresa"] = $codEmpresa;
                        $arrayParametros["identificacion"] = $cliente['identificacion'];
                        $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                        ->getPuntosAndFacturasAbiertasByIdentificacion($arrayParametros);


                    } catch (\Exception $th)
                    {
                        $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                        ->getPuntosFacturacionAndFacturasAbiertasByIdPunto( $ptoCliente['id'],
                                                                        $em,
                                                                        $codEmpresa);
                    }
                    
                }

            $session->set('datosFinancierosPunto', $arraySaldoYFacturasAbiertasPunto);

            //servicios del login para toolbar
            $serviciosSession = array();
            $query = $em->createQuery(
                "SELECT s
				      FROM schemaBundle:InfoServicio s
				      WHERE s.puntoId=" . $ptoCliente['id'] . " 
				      AND  lower(s.estado) not in ('eliminado','anulado','rechazada','rechazado')
				      order by s.feCreacion ASC"
            );

            $servicios = $query->getResult();

            if( $servicios )
            {
                foreach( $servicios as $servicio )
                {
                    $servicioSession  = array();
                    $infoPlan         = $servicio->getPlanId();
                    $intAdmiProducto  = $servicio->getProductoId();
                    $strFlagActiSimul = 'N';
                    if( $infoPlan )
                    {
                        $servicioSession['nombre'] = $infoPlan->getNombrePlan();
                    }
                    if( $intAdmiProducto )
                    {
                        $servicioSession['nombre'] = $intAdmiProducto->getDescripcionProducto();
                        $intProductoCou            = $intAdmiProducto->getId();
                    }
                    $servicioSession['estado'] = $servicio->getEstado();
                    $serviciosSession[]        = $servicioSession;
                    
                    //Obtener la caracteristica del producto para conocer que flujo seguir
                    if ($intProductoCou == 1204)
                    {
                        $objCaract = $serviceTecnico->getServicioProductoCaracteristica($servicio,
                                                                                    'CATEGORIAS TELEFONIA',
                                                                                    $servicio->getProductoId());
                                    
                        if(is_object($objCaract))
                        {
                            $strCategoria       = $objCaract->getValor();
                        }
                        
                        if ($strCategoria == 'FIJA ANALOGA' || $strCategoria == 'FIJA SIP TRUNK')
                        {
                            //Preguntamos si es activación simultánea y consultamos el estado del servicio tradicional
                            $arrayCouSim          = $objPlanificarService->getIdTradInstSim($servicio->getId());
                            $intIdServTradicional = $arrayCouSim[0];
                            if($intIdServTradicional !== null && $intIdServTradicional != "null" && !empty($intIdServTradicional))
                            {
                                $objServTradicional = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServTradicional);
                                if((is_object($objServTradicional)) && ($objServTradicional->getEstado() == 'Activo'))
                                {
                                    $strFlagActivacion    = 'S';
                                }
                            }
                        }
                    }
                    //Consultamos si el producto es de CANAL TELEFONIA para realizar la búsqueda de la marca de activación simultánea
                    if ($servicio->getDescripcionPresentaFactura() == 'CANAL TELEFONIA')
                    {
                        //Preguntamos si es activación simultánea y consultamos el estado del servicio tradicional
                        $arrayCouSim          = $objPlanificarService->getIdTradInstSimCanaTelefonia($servicio->getId(), $servicio->getProductoId());
                        $intIdServTradicional = $arrayCouSim[0];
                        if($intIdServTradicional !== null && $intIdServTradicional != "null" && !empty($intIdServTradicional))
                        {
                            $objServTradicional = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServTradicional);
                            if((is_object($objServTradicional)) && ($objServTradicional->getEstado() == 'Activo'))
                            {
                                $strFlagActivacion    = 'S';
                            }
                            $strFlagActiSimul = 'S';
                        }
                    }
                }

                $session->set('numServicios', count($serviciosSession));
                $session->set('serviciosPunto', $serviciosSession);
            }
        }

        //OBTIENE EL ULTIMO ESTADO DEL CLIENTE
        $clienteController = new ClienteController();
        $clienteController->setContainer($this->container);
        $datosHistorial    = $clienteController->obtieneUltimoEstadoClientePorPersonaEmpresaRol($entity->getPersonaEmpresaRolId()
            ->getId(), $rol, $codEmpresa);
        //Obtiene el ultimo estado del cliente o pre-cliente
        $historial                = $datosHistorial['historial'];
        $estadoCliente            = $datosHistorial['estado'];
        $deleteForm               = $this->createDeleteForm($id);
        $entityPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($entity->getId());
        $entityPuntoEdificio      = null;
        $entityPuntoEdificioDatoAdicional = null;
        if($entityPuntoDatoAdicional)
        {
            if($entityPuntoDatoAdicional->getPuntoEdificioId())
            {
                $entityPuntoEdificioDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                       ->findOneByPuntoId($entityPuntoDatoAdicional->getPuntoEdificioId());
            }
            $strNombreNodoCliente = "";
            if($entityPuntoDatoAdicional->getElementoId())
            {
                //se agrega el nombre del nodo cliente
                $objNodoCliente = $em->getRepository('schemaBundle:InfoElemento')->findOneById($entityPuntoDatoAdicional->getElementoId()->getId());
                if($objNodoCliente)
                {
                    $strNombreNodoCliente = $objNodoCliente->getNombreElemento();
                }
            }
        }
        else
        {
            $entityPuntoDatoAdicional = new InfoPuntoDatoAdicional();
        }  
        //obtiene formas de contacto por pto
        $formasContactoServ    = null;
        $arrFormasContactoServ = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
            ->findContactosServicioPorEstadoPorPunto($id, 'Activo', 9999999, 1, 0);
        if($arrFormasContactoServ['registros'])
        {
            $formasContactoServ = $arrFormasContactoServ['registros'];
        }
        //Obtiene formas de contacto por cliente o prospecto
        $formasContacto = null;
        $clienteSesion  = $session->get('cliente');
        //SE LIMITO A QUE CONSULTE MAXIMO 6 FORMAS DE CONTACTO HASTA RESOLVER EL PROBLEMA
        $arrformasContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
            ->findPorEstadoPorPersona($clienteSesion['id'], 'Activo', 6, 1, 0);
        if($arrformasContacto['registros'])
        {
            $formasContacto = $arrformasContacto['registros'];
        }
        $formasContactoPunto    = "";
        $arrformasContactoPunto = $em->getRepository('schemaBundle:InfoPuntoFormaContacto')
            ->findPorEstadoPorPunto($id, 'Activo', 6, 0);
        if($arrformasContactoPunto['registros'])
        {
            $formasContactoPunto = $arrformasContactoPunto['registros'];
        }
        
        $objSolicitudMigracion = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                 ->getSolicitudPorPunto($ptoCliente['id'], 'SOLICITUD MIGRACION', 
                                   array('Planificada','PrePlanificada','Replanificada','AsignadoTarea','Asignada','In-Corte','Pendiente'));
        
        $arraySolicitudAgregarEquipo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->getSolicitudPorPunto( $ptoCliente['id'], 
                                                                'SOLICITUD AGREGAR EQUIPO', 
                                                                array('Planificada',
                                                                      'PrePlanificada',
                                                                      'Replanificada',
                                                                      'AsignadoTarea',
                                                                      'Asignada',
                                                                      'Detenido'));

        $arraySolicitudAgregarEquipoMasivo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->getSolicitudPorPunto($ptoCliente['id'],
                                                                      'SOLICITUD AGREGAR EQUIPO MASIVO',
                                                                      array('Planificada',
                                                                            'PrePlanificada',
                                                                            'Replanificada',
                                                                            'AsignadoTarea',
                                                                            'Asignada',
                                                                            'Detenido'));

        $arraySolicitudTraslado = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->getSolicitudPorPunto( $ptoCliente['id'], 
                                                             'SOLICITUD TRASLADO', 
                                                             array('Pendiente',
                                                                   'Aprobado',
                                                                   'PendienteAutorizar'));
        
        $arraySolicitudReubicacion = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->getSolicitudPorPunto( $ptoCliente['id'], 
                                                                'SOLICITUD REUBICACION', 
                                                                array('Planificada',
                                                                      'Pendiente',
                                                                      'Aprobado',
                                                                      'PrePlanificada',
                                                                      'Replanificada',
                                                                      'AsignadoTarea',
                                                                      'Asignada',
                                                                      'Detenido'));
        
        $arraySolicitudReubicacionTraslado = array_merge($arraySolicitudTraslado,$arraySolicitudReubicacion);
        
        if( $strPrefijoEmpresa == 'TN' )
        {
            $arrayParamsSolicitud               = array("strEstadoSolicitud"            => "Pendiente",
                                                        "intValorDetSolCaract"          => $entity->getPersonaEmpresaRolId()->getId(),
                                                        "strDescripcionSolicitud"       => 'SOLICITUD APROBACION SERVICIO',
                                                        "strDescripcionCaracteristica"  => 'ID_PERSONA_ROL',
                                                        "strConServicio"                => "SI");
            $arrayRespuestaSolicitudServicio    = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                     ->getSolicitudesPorDetSolCaracts($arrayParamsSolicitud);
            $arraySolicitudAprobServicio        = $arrayRespuestaSolicitudServicio["arrayResultado"];
        }
        else
        {
            $arraySolicitudAprobServicio = array();
        }
        
        // Se obtiene si es prepago(S) o No (N)
        $strEsPrepago = $entity->getPersonaEmpresaRolId()->getEsPrepago();
        
        // Solo la empresa MegaDatos puede consultar el canal y el punto de venta
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto   = $this->get('comercial.InfoPunto');
            // Se consulta el Canal y el Punto de venta asociado al Punto.
            $objCanalPuntoVenta = $serviceInfoPunto->getCanalPuntoVenta($entity, $codEmpresa);
        }
        else
        {
            $objCanalPuntoVenta['strCanalDesc']      = '';
            $objCanalPuntoVenta['strPuntoVentaDesc'] = '';
        }
        
        $strEjecutivoCobranzas           = '';
        $boolContieneProductosPorConsumo = false;
        
        if($strPrefijoEmpresa == 'TN')
        {
            if($entity->getUsrCobranzas())
            {
                $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login' => $entity->getUsrCobranzas()));

                if($entityPersona)
                {
                    $strEjecutivoCobranzas = $entityPersona->getNombres() . ' ' . $entityPersona->getApellidos();
                }
            }
            
            if(is_object($entity))
            {
                //Verificar si el punto contiene productos creados de tipo FACTURACION POR CONSUMO para mostrar acción
                //de subir o agregar archivos digitales externos
                $arrayServicios = $emComercial->getRepository("schemaBundle:InfoServicio")->findByPuntoId($entity->getId());
                
                foreach($arrayServicios as $objServicio)
                {
                    $strEstado = $objServicio->getEstado();
                    
                    if($strEstado != 'Cancel' || $strEstado != 'Eliminado' || $strEstado != 'Anulado')
                    {
                        $serviceTecnico                  = $this->get('tecnico.InfoServicioTecnico');
                        $boolContieneProductosPorConsumo = $serviceTecnico->isContieneCaracteristica($objServicio->getProductoId(),
                                                                                                     'FACTURACION POR CONSUMO');
                        //Si al menos contiene uno servicios de estas
                        //caracteristicas continua como true con el flujo
                        if($boolContieneProductosPorConsumo)
                        {
                            break;
                        }
                    }
                }
            }               
        }

        /*
         * Se verifica que el cliente ya tenga una solicitud de migracion creada
         */        
        $tieneCasoMigracionCreado = false;
        
        if($objSolicitudMigracion)
        {
            foreach($objSolicitudMigracion as $itemSolicitudMigracion)
            {
                $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array('descripcionCaracteristica' => 'CASO'));

                if($objAdmiCaracteristica)
                {
                    $objSolCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objAdmiCaracteristica->getId(),
                        'detalleSolicitudId' => $itemSolicitudMigracion['id']));

                    if($objSolCaract)//Si existe o no un registro relacion con el Caso
                    {
                        $tieneCasoMigracionCreado = true;
                    }
                }
            }
        }
        
        /**
         * Bloque que verifica la forma de pago del cliente SOLO para TN
         */
        if( $strPrefijoEmpresa == 'TN' )
        {
            try
            {
                if( is_object($entity) )
                {
                    $intIdPunto      = $entity->getId() ? $entity->getId() : 0;
                    $intIdPersonaRol = 0;
                    $objPersonalRol  = $entity->getPersonaEmpresaRolId();

                    if( is_object($objPersonalRol) )
                    {
                        $intIdPersonaRol = $objPersonalRol->getId() ? $objPersonalRol->getId() : 0;
                    }

                    $arrayTmpParametros = array('intIdPersonaRol' => $intIdPersonaRol, 'intIdPunto' => $intIdPunto);
                    $arrayResultado     = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                       ->getFormaPagoCliente($arrayTmpParametros);

                    if( isset($arrayResultado['strDescripcionFormaPago']) && !empty($arrayResultado['strDescripcionFormaPago']) )
                    {
                        $strFormaPagoFacturacion = $arrayResultado['strDescripcionFormaPago'];
                    }//( !empty($arrayResultado) )
                }//( is_object($entity) )
            }//try
            catch(\Exception $e)
            {
                $serviceUtil->insertError('Telcos+', 'showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            }
        }//( $strPrefijoEmpresa == 'TN' )
            
        //se agrega control de roles permitidos
        $rolesPermitidos = array();
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-1779'))
        {
            $rolesPermitidos[] = 'ROLE_13-1779'; //ANULAR PUNTO
        }
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-225'))
        {
            $rolesPermitidos[] = 'ROLE_13-225'; //ANULAR SERVICIO
        }
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-2597'))
        {
            $rolesPermitidos[] = 'ROLE_13-2597'; //VER CARACTERSITICAS
        }
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-4577'))
        {
            $rolesPermitidos[] = 'ROLE_13-4577'; //ACTUALIZAR ID DE PLAN
        }
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-2618'))
        {
            $rolesPermitidos[] = 'ROLE_13-2618'; //EDITAR CARACTERSITICAS
        }
        //MODULO 322 - ENLACE_DATOS
        if(true === $this->get('security.context')->isGranted('ROLE_322-3337'))
        {
            $rolesPermitidos[] = 'ROLE_322-3337'; //ENLAZAR 
        }
        //MODULO 9 - PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_9-3937'))
        {
            $rolesPermitidos[] = 'ROLE_9-3937'; //RENOVACION PLANES 100/100 
        }
        //MODULO 13 - COMERCIAL/EDICION DE DESCRIPCION PRESENTA FACTURA
        if(true === $this->get('security.context')->isGranted('ROLE_13-4357'))
        {
            $rolesPermitidos[] = 'ROLE_13-4357'; //EDICION DE DESCRIPCION PRESENTA FACTURA
        }
        //MODULO 13 - CARGAR Y VISUALIZAR ARCHIVOS PDF
        if(true === $this->get('security.context')->isGranted('ROLE_13-7837'))
        {
            $rolesPermitidos[] = 'ROLE_13-7837'; //GESTIONAR ARCHIVO ADJUNTO PARA SSID_MOVIL
        }
        //MODULO 151 - CLIENTES
        if(true === $this->get('security.context')->isGranted('ROLE_151-4337'))
        {
            $rolesPermitidos[] = 'ROLE_151-4337'; //CAMBIAR FRECUENCIA FACTURACIÓN
        }
        //MODULO 151 - CLIENTES
        if(true === $this->get('security.context')->isGranted('ROLE_151-5677'))
        {
            $rolesPermitidos[] = 'ROLE_151-5677'; //CAMBIO TIPO MEDIO
        }
        //MODULO 9 - PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_9-4857'))
        {
            $rolesPermitidos[] = 'ROLE_9-4857'; //EDITAR FORMA DE PAGO FACTURACION 
        }
        
        //MODULO 9 - PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_9-5317'))
        {
            $rolesPermitidos[] = 'ROLE_9-5317'; //EDITAR PLANTILLA DE COMISIONISTAS
        }
        
        //MODULO 404 - PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_404-5637'))
        {
            $rolesPermitidos[] = 'ROLE_404-5637'; //TRASLADO DE SERVICIOS TN
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_404-5657'))
        {
            $rolesPermitidos[] = 'ROLE_404-5657'; //REUBICACION DE SERVICIOS TN
        }
        
        //MODULO 9 - EDITAR SOLUCIONES
        if(true === $this->get('security.context')->isGranted('ROLE_9-5717'))
        {
            $rolesPermitidos[] = 'ROLE_9-5717'; //EDITAR SOLUCIONES TN
        }
        
        //CANCELAR LINEAS
        if(true === $this->get('security.context')->isGranted('ROLE_415-6038'))
        {
            $rolesPermitidos[] = 'ROLE_415-6038';
        }

        //Agregar líneas
        if(true === $this->get('security.context')->isGranted('ROLE_415-6043'))
        {
            $rolesPermitidos[] = 'ROLE_415-6043';
        }
        
        //Cambiar línea
        if(true === $this->get('security.context')->isGranted('ROLE_415-6041'))
        {
            $rolesPermitidos[] = 'ROLE_415-6041';
        }
        
        //Cambiar línea
        if(true === $this->get('security.context')->isGranted('ROLE_415-6042'))
        {
            $rolesPermitidos[] = 'ROLE_415-6042';
        }        
        
        //Editar línea
        if(true === $this->get('security.context')->isGranted('ROLE_415-6039'))
        {
            $rolesPermitidos[] = 'ROLE_415-6039';
        }
        
        //Cancelar líneas
        if(true === $this->get('security.context')->isGranted('ROLE_415-6038'))
        {
            $rolesPermitidos[] = 'ROLE_415-6038';
        }
        
        //Factibilidad líneas 
        if(true === $this->get('security.context')->isGranted('ROLE_415-6037'))
        {
            $rolesPermitidos[] = 'ROLE_415-6037';
        }
        
        //Detalle llamada línea
        if(true === $this->get('security.context')->isGranted('ROLE_415-6046'))
        {
            $rolesPermitidos[] = 'ROLE_415-6046';
        }        
        
        /*Ver Archivo de Inspección*/
        if($this->get('security.context')->isGranted('ROLE_13-6617'))
        {
            $rolesPermitidos[] = 'ROLE_13-6617';
        }

        /*Factibilidad FWA*/
        if($this->get('security.context')->isGranted('ROLE_9-6797'))
        {
            $rolesPermitidos[] = 'ROLE_9-6797';
        }

         /*Diferido de Facturas por Emergencia Sanitaria*/
        if($this->get('security.context')->isGranted('ROLE_9-7377'))
        {
            $rolesPermitidos[] = 'ROLE_9-7377';
        }
        //MODULO 151 - CLIENTES
        if(true === $this->get('security.context')->isGranted('ROLE_151-8177'))
        {
            //ASOCIAR MASCARILLA CAMARA
            $rolesPermitidos[] = 'ROLE_151-8177';
        }
        
        //MODULO 9 - PUNTO
        $arrayRespuestaCoordenada = [];
        $arrayRespuestaCoordenada['boolTieneTarea'] = false;
        if(true === $this->get('security.context')->isGranted('ROLE_9-5697'))
        {
            $rolesPermitidos[] = 'ROLE_9-5697'; //ACTUALIZAR COORDENADAS DEL PUNTO
            $objCaracteristica         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneByDescripcionCaracteristica("ID_METROS_MAXIMO");

            $arrayParametrosCoordenada = array(
                                                'intIdPunto'            => $id,
                                                'intIdCaracteristica'   => $objCaracteristica->getId(),
                                                'strEstado'             => "Activo"
                                              );
            $objRespuestaCoordenada    = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                 ->obtenerCoordenadaSugerida($arrayParametrosCoordenada);
            if(is_object($objRespuestaCoordenada))
            {
                $arrayRespuestaCoordenada['boolTieneTarea'] = true;
            }
        }

        $strTipoAdministracion = 'TELCONET';
        
        //Mostrar informacion detallada del Edificio
        if(is_object($entityPuntoDatoAdicional) && $entityPuntoDatoAdicional->getElementoId())
        {
            $intElementoEdificio         = $entityPuntoDatoAdicional->getElementoId()->getId();
            
            $objDetalleElementoAministra = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                             ->findOneBy(array('detalleNombre'  =>  'ADMINISTRA',
                                                                               'estado'         =>  'Activo',
                                                                               'elementoId'     =>  $intElementoEdificio
                                                                              )
                                                                        );
            if(is_object($objDetalleElementoAministra))
            {
                if($objDetalleElementoAministra->getDetalleValor() == 'CLIENTE')
                {
                    $objDetalleElementoTipoAministra = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                         ->findOneBy(array('detalleNombre'  =>  'TIPO_ADMINISTRACION',
                                                                                           'estado'         =>  'Activo',
                                                                                           'elementoId'     =>  $intElementoEdificio
                                                                                          )
                                                                                     );
                    if(is_object($objDetalleElementoTipoAministra))
                    {
                        $strTipoAdministracion = $objDetalleElementoTipoAministra->getDetalleValor();
                    }
                }
            }
        }

        //Se realizar validación del contrato/adendum por persona
        $strMensajeContrato = "";
        $strMensajeAdendum  = "";
        $objContratoCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                       ->findOneBy(
                                        array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                              "estado"                      => 'Activo'));

        $objAdendumCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(
                                        array("descripcionCaracteristica"   => 'FORMA_REALIZACION_ADENDUM',
                                              "estado"                      => 'Activo'));


        $entityContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                      ->findOneBy(
                                            array("personaEmpresaRolId"     => $entity->getPersonaEmpresaRolId(),
                                                  "estado"                  => array('Pendiente','PorAutorizar')
                                            ));

        if(is_object($entityContrato))
        {
            $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                            ->findOneBy(
                                                                array("caracteristicaId"  => $objContratoCaract,
                                                                      "contratoId"        => $entityContrato,
                                                                      "estado"            => "Activo",
                                                                      "valor2"            => "I"
                                                                ));
            
            if(is_object($entityCaractContrato))
            {
                $strMensajeContrato = " Login: ".$entity->getLogin()." con #Contrato: ".$entityContrato->getNumeroContrato().
                " tiene pendiente culminar el flujo de contrato ".$entityCaractContrato->getValor1().".";
            }

            $entityCaractAdendum = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                                ->findOneBy(
                                                                    array("caracteristicaId"  => $objAdendumCaract,
                                                                          "contratoId"        => $entityContrato,
                                                                          "estado"            => "Activo",
                                                                          "valor2"            => "I"
                                                                    ));

            if(is_object($entityCaractAdendum))
            {
                $strMensajeAdendum = " Login: ".$entity->getLogin().
                " tiene pendiente culminar el flujo de adendum ".$entityCaractAdendum->getValor1().".";
            }
        }

        //Se obtiene el tipo origen del punto en caso que lo tuviese.
        $arrayTipoOrigen = $this->get("comercial.InfoPunto")->getCaractTipoOrigenPuntoxIdPunto(array("strEmpresaCod" => $codEmpresa,
                                                                                                     "objPuntoId"    => $entity));
        //Bloque que verifica si el punto es de un distribuidor
        $objCaractRazonSocialCltDist    = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                      ->findOneBy(array("descripcionCaracteristica" => 'RAZON_SOCIAL_CLT_DISTRIBUIDOR',
                                                                        "estado"                    => 'Activo'));
        $objCaractIdentificacionCltDist = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                      ->findOneBy(array("descripcionCaracteristica" => 'IDENTIFICACION_CLT_DISTRIBUIDOR',
                                                                        "estado"                    => 'Activo'));
        if((!empty($objCaractRazonSocialCltDist)    && is_object($objCaractRazonSocialCltDist)) && 
           (!empty($objCaractIdentificacionCltDist) && is_object($objCaractIdentificacionCltDist)))
        {
            $objInfoPuntoCaractRazonSocialClt = $emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
                                                            ->findOneBy(array("puntoId"          => $entity,
                                                                              "caracteristicaId" => $objCaractRazonSocialCltDist,
                                                                              "estado"           => "Activo"));
            $objInfoPuntoCaractIdentificacionClt = $emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
                                                               ->findOneBy(array("puntoId"          => $entity,
                                                                                 "caracteristicaId" => $objCaractIdentificacionCltDist,
                                                                                 "estado"           => "Activo"));
            if((!empty($objInfoPuntoCaractRazonSocialClt)    && is_object($objInfoPuntoCaractRazonSocialClt)) && 
               (!empty($objInfoPuntoCaractIdentificacionClt) && is_object($objInfoPuntoCaractIdentificacionClt)))
            {
                $strRazonSocialCltDistribuidor    = $objInfoPuntoCaractRazonSocialClt->getValor() ? 
                                                    $objInfoPuntoCaractRazonSocialClt->getValor():"";
                $strIdentificacionCltDistribuidor = $objInfoPuntoCaractIdentificacionClt->getValor() ? 
                                                    $objInfoPuntoCaractIdentificacionClt->getValor():"";
                $boolVerDistribuidor              = true;
            }
        }
        if($strPrefijoEmpresa == 'MD' ||  $strPrefijoEmpresa == 'EN'  )
        {
            $arrayParametros    = array(
                'puntoId'               => $entity->getId() ? $entity->getId() : 0,
                'personaEmpresaRolId'   => $entity->getPersonaEmpresaRolId()->getId(),
                'usrCreacion'           => $strUsrCreacion,
                'empresaCod'            => $codEmpresa
               );
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto   = $this->get('comercial.InfoPunto');
            $arrayRespuesta     = $serviceInfoPunto->getDataLinksContratoCliente($arrayParametros);
            if(!empty($arrayRespuesta) && is_array($arrayRespuesta) 
                && $arrayRespuesta['status'] == 'OK'
                && $arrayRespuesta['data']
                && $arrayRespuesta['data']['haslinkDatosBancarios'] == $boolValueTrue
                && $arrayRespuesta['data']['hastieneClausulasSaved'] == $boolValueFalse)
            {
                $strLlenoDatosBancario = 'N';
            }
        }
        return $this->render('comercialBundle:infopunto:show.html.twig', array(
                'item'                              => $entityItemMenu,
                'entity'                            => $entity,
                'entityPuntoDatoAdicional'          => $entityPuntoDatoAdicional,
                'entityPuntoEdificioDatoAdicional'  => $entityPuntoEdificioDatoAdicional,
                'delete_form'                       => $deleteForm->createView(),
                'formasContactoServ'                => $formasContactoServ,
                'formasContacto'                    => $formasContacto,
                'formasContactoPunto'               => $formasContactoPunto,
                'rol'                               => $tipoRol,
                'prefijoEmpresa'                    => $strPrefijoEmpresa,
                'canalDesc'                         => $objCanalPuntoVenta['strCanalDesc'],      // Nombre del Canal
                'puntoVentaDesc'                    => $objCanalPuntoVenta['strPuntoVentaDesc'], // Nombre del Punto de Ventas
                'esPrepago'                         => $strEsPrepago,
                'estadoCliente'                     => $estadoCliente,
                'ejecutivoCobranzas'                => $strEjecutivoCobranzas,
                'objNombreLoginTrasladado'          => $strNombreLoginTrasladado,
                'nombreVendedor'                    => $nombreVendedor,
                'objSolicitudMigracion'             => $objSolicitudMigracion,
                'arraySolicitudAgregarEquipo'       => $arraySolicitudAgregarEquipo,
                'arraySolicitudAgregarEquipoMasivo' => $arraySolicitudAgregarEquipoMasivo,
                'arraySolicitudReubicacionTraslado' => $arraySolicitudReubicacionTraslado,
                'arraySolicitudAprobServicio'       => $arraySolicitudAprobServicio,
                'rolesPermitidos'                   => $rolesPermitidos,
                'tieneCasoMigracionCreado'          => $tieneCasoMigracionCreado,
                'boolPermiteEditarPto'              => $boolPermiteEditarPto,
                'nombreNodoCliente'                 => $strNombreNodoCliente,
                'strFormaPagoFacturacion'           => $strFormaPagoFacturacion,
                'tipoAdministracion'                => $strTipoAdministracion,
                'floatValorMaxComision'             => $floatValorMaxComision,
                'boolTieneTarea'                    => $arrayRespuestaCoordenada['boolTieneTarea'],
                'arrayTipoOrigen'                   => $arrayTipoOrigen,
                'boolContieneFacturacionConsumo'    => $boolContieneProductosPorConsumo,
                'strFlagActivacion'                 => $strFlagActivacion,
                'fltTotalSaldoFactPorPto'           => $fltTotalSaldoFactPorPto,
                'fltValorFactMin'                   => $fltValorFactMin,
                'boolCroquisUrl'                    => $boolCroquisUrl,
                'strMensajeContrato'                => $strMensajeContrato,
                'strMensajeAdendum'                 => $strMensajeAdendum,
                'strNombreArcCroquis'               => $strNombreArcCroquis,
                'strNombreArcDigital'               => $strNombreArcDigital,
                'strRazonSocialCltDistribuidor'     => $strRazonSocialCltDistribuidor    ? $strRazonSocialCltDistribuidor :"",
                'strIdentificacionCltDistribuidor'  => $strIdentificacionCltDistribuidor ? $strIdentificacionCltDistribuidor :"",
                'llenoDatosBancario'                => $strLlenoDatosBancario,
                'boolVerDistribuidor'               => $boolVerDistribuidor ? $boolVerDistribuidor:false
              
            ));
    }

      
    /**
     * @Secure(roles="ROLE_9-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Redenriza la ventana para creación de un nuevo punto del cliente.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 18-12-2015
     * Se envía el idOficina para su procesamiento en caso de que el CANAL elegido sea CANAL_INTERNO.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.2 22-03-2016
     * Se envía el prefijo de la empresa, de ser MegaDatos se permite la selección del Canal y Punto de venta.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.3 14-04-2016
     * Se verifica que el usuario sesion sea un empleado vendedor.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.4 09-05-2016
     * Se modifica la verificación del usuario vendedor por departamentos y roles.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.5 22-05-2016
     * En el combo "Tipo Ubicacion" se selecciona por default el valor [Abierto] Sólo para la empresa TN.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.6 29-05-2016
     * Se quita la definición del filtro de los departamentos de los cuales podrán realizar ventas, se centraliza en InfoPersonaRepository.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.7 22-06-2016
     * Se quitan el resto de filtros de consulta del vendedor.
     * Se prepara el método para salvar los valores del punto ingresado en caso de error.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.8 29-07-2016
     * Se verifica que el cliente no tenga padres de facturación válidos para definir de manera obligatoria al nuevo punto como padre de facturación.
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.9 07-06-2017
     * Manejo de parámetro para el máximo numero de correos y el máximo de números de teléfono en la ventana de datos de envio cuando sea padre de 
     * facturacion para TN.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>       
     * @version 1.10 30-06-2017
     * Se añade a los parámetros intIdPais y strNombrePais 
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.11
     * @since 28-01-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.0 09-05-2021 - Se agrega validación para visualizar si el cliente en sesión es de tipo distribuidor.
     *
     * Se define si es necesario presentar el comboBox para el tipo de Origen del punto.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.1 18-11-2021 - Se agrega validación para empresa MD, donde se realiza el consumo microservicio 
     *                           validacionesPuntoAdicional. En el caso de no cumplir con las validaciones del microservicio se
     *                           presenta mensaje en pantalla y bloquea el botón de crear nuevo punto, aplica sólo para rol cliente.
     * 
     * @author Joel Broncano <jbroncano@telconet.ec>
     * @version 2.2 09-11-2021 - Se  No se debe obligar a cargar un documento en el campo “Archivo”b
     *                                            ya que en este campo suben la imagen del contrato físico
     *                                            firmado, pero con este cambio ya no existirá el bloc y el nuevo
     *                                            modelo de contrato físico no se genera aún en esta instancia.
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.3 05-12-2022 - Se agrega validaciones por vendedor mediante el usuario en sesión y tipo de origen al momento 
     *                           de realizar el consumo microservicio validacionesPuntoAdicional.
     *
    
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 2.4 10-02-2023 se agrega validacion de reconocimiento si el punto a 
     *                         crear sera por el nuevo flujo ademas de una redireccion en caso de edicion al 
     *                         retorceder de la vista traslado
     *                         Se envia nuevas variables a reenderizar en la vista
     */
    public function newAction($idCli, $idPer, $rol)
    {
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();	
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $em_seguridad      = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu    = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "1");
        $empresaId         = $session->get('idEmpresa');
        $intOficinaId      = $session->get('idOficina');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $usrCreacion       = $session->get('user');
        $objInfoPuntoRepo  = $emComercial->getRepository('schemaBundle:InfoPunto');
        $boolValidaFileDigital = false;
        $strLoginEmpleado       = '';
        $strNombreEmpleado      = '';
        $strNombreParametro     = 'DATOS_DE_ENVIO';
        $strModulo              = 'COMERCIAL';
        $strProceso             = 'CLIENTE';
        $intNumeroMaxCorreos    = 0;
        $intNumeroMaxTelefonos  = 0;
        $arrayParametrosCabDet  = array ();
        $strEsDistribuidor      = "NO";
        $intPuntoId             = $peticion->query->get('idPunto');
        $strTipo                =$peticion->query->get('strTipo');
        $strIpCreacion          = $peticion->getClientIp() ? $peticion->getClientIp() : '127.0.0.1';
        $emGeneral              = $this->get('doctrine')->getManager('telconet_general');
        $strEditarCampos    ="";
        $entity  = new InfoPunto();
        $intPuntoId             = $peticion->query->get('idPunto');
        $strTipo                =$peticion->query->get('strTipo');
        $strEditarCampos    ="";


        if ($strTipo == 'continuo' && $strPrefijoEmpresa == 'MD')
        {
            $strEstadoProceso         = null;
            $objInfoPuntoCaracOrigen  = null;
            $objInfoPuntoCaracEstado  = null;

            $objCaracEstadoProceso = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                    ->findOneBy(
                                                        array(
                                                            "descripcionCaracteristica" => 'ESTADO_PROCESO_PUNTO',
                                                            "estado"                    => 'Activo'
                                                        )
                                                    );

            $objCaracPuntoOrigen = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                ->findOneBy(
                                                    array(
                                                        "descripcionCaracteristica" => 'PUNTO_ORIGEN_CREACION',
                                                        "estado"                    => 'Activo'
                                                    )
                                                );

            if(!empty($objCaracEstadoProceso) && is_object($objCaracEstadoProceso) &&
                !empty($objCaracPuntoOrigen) && is_object($objCaracPuntoOrigen))
            {
                $objInfoPuntoCaracOrigen = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                            ->obtenerCaracteristicaPorValor(
                                                                array(
                                                                    'valor' => $intPuntoId,
                                                                    'intIdCaracteristica' => $objCaracPuntoOrigen->getId()
                                                                )
                                                            );
            }

            if (!empty($objInfoPuntoCaracOrigen) && is_object($objInfoPuntoCaracOrigen))
            {
                $intIdPuntoActual         = $objInfoPuntoCaracOrigen->getPuntoId()->getId();
                $objInfoPuntoCaracEstado  = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                            ->findOneBy(
                                                                array(
                                                                    'puntoId'          => $intIdPuntoActual,
                                                                    'caracteristicaId' => $objCaracEstadoProceso->getId()
                                                                )
                                                            );
            }

            if (!empty($objInfoPuntoCaracEstado) && is_object($objInfoPuntoCaracEstado))
            {
                $strEstadoProceso = $objInfoPuntoCaracEstado->getValor();
            }

            if ($strEstadoProceso == 'Pendiente')
            {
                $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objInfoPuntoCaracEstado->getPuntoId());
                return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios', 
                                                array('id' => $objInfoPunto->getId(), 
                                                'rol' => $rol, 'strTipo' => $strTipo, 
                                                'intIdPuntoAnterior' => $intPuntoId)));
            }


        }
        // Se valida que si la empresa es MD sea obligatorio el ingreso del archivo desde el segundo punto del cliente.
        if($strPrefijoEmpresa == 'MD')
        {
            $arrayEstados = array('Anulado', 'Cancel', 'Cancelado', 'Eliminado', 'Pendiente', 'PendienteEdif');
            $arrayTotalPtsCliente = $objInfoPuntoRepo->findTotalPtosCliente($idCli, $empresaId, $arrayEstados);
            if(intVal($arrayTotalPtsCliente['total']) > 0)
            {
                $boolValidaFileDigital = true;
            }
            //validacion de nuevo flujo
            if($strTipo=="editar"|$strTipo=="continuo")
            {
                $arrayParamEditar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('EDITAR_CAMPOS_PUNTO_MD','COMERCIAL','',
                'EDITAR_CAMPOS','','','',
                '','',$empresaId);
                if(count($arrayParamEditar)>0)
                {
                    $strEditarCampos    =$arrayParamEditar[0]['valor1'];
                }
             if($strTipo=="editar")
             {
                 $arrayParametros=array
                 (
                     'intPuntoId'=>$intPuntoId,
                     'intEmpresaId'=>$empresaId,
                     'strPrefijoEmpresa'=>$strPrefijoEmpresa,
                     'strTipo'=>$strTipo,
                     'strEditarCampos'=>$strEditarCampos,
                     'intOficinaId'=>$intOficinaId
                                 );
               return  $this->EditarPuntoNuevoProceso($arrayParametros);
         
             }
            }
        }
        
        // Se valida que si la empresa es EN sea obligatorio el ingreso del archivo desde el segundo punto del cliente.
        if($strPrefijoEmpresa == 'EN' || $strPrefijoEmpresa == 'MD')
        {
            $arrayEstados = array('Anulado', 'Cancel', 'Cancelado', 'Eliminado', 'Pendiente', 'PendienteEdif');
            $intTotalPtsCliente = $objInfoPuntoRepo->findTotalPtosCliente($idCli, $empresaId, $arrayEstados);
            if(intVal($intTotalPtsCliente['total']) > 0)
            {
                $boolValidaFileDigital = true;
            }
        }
        
        // Se obtiene el tipo de ubicación "Abierto" para seleccionar por defecto.
        $entityUbicacion = null;
        if($strPrefijoEmpresa == 'TN')
        {
            $entityUbicacion = $emComercial->getRepository('schemaBundle:AdmiTipoUbicacion')
                                           ->findOneBy(array('descripcionTipoUbicacion' => 'Abierto'));

            if( !$entityUbicacion)
            {
                throw new \Exception("Unable to find AdmiTipoUbicacion");
            }
        }
        
        $arrayOptions = array('validaFile'        => true, 
                              'validaFileDigital' => $boolValidaFileDigital, 
                              'empresaId'         => $empresaId, 
                              'ubicacionDefault'  => $entityUbicacion);
        $form    = $this->createForm(new InfoPuntoType($arrayOptions), $entity);
        $cliente = $emComercial->getRepository('schemaBundle:InfoPersona')->find($idCli);
        
        $arrayParametros['EMPRESA'] = $empresaId;
        $arrayParametros['LOGIN']   = $usrCreacion;
        
        $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

        if($arrayResultado['TOTAL'] > 0)
        {
            $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
            $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
        }

        $strPfObligatorio = 'N';
        if($strPrefijoEmpresa == 'TN')
        {
            $arrayEstados     = array('Anulado', 'Cancel', 'Cancelado', 'Eliminado');
            $arrayPuntosPF    = $objInfoPuntoRepo->findPtosPadrePorEmpresaPorCliente($empresaId, $idCli, $arrayEstados);

            if($arrayPuntosPF == null || count($arrayPuntosPF) == 0)
            {
                $strPfObligatorio = 'S';
            }
        }
        else
        {
            $strPfObligatorio = 'S';
        }

        if($strPrefijoEmpresa == 'TN')
        {
            $intNumeroMaxCorreos                    = 2;
            $intNumeroMaxTelefonos                  = 2;
            
            $arrayParametrosCabDet['strNombreParametro']  = $strNombreParametro;
            $arrayParametrosCabDet['strModulo']           = $strModulo;
            $arrayParametrosCabDet['strProceso']          = $strProceso;
            $arrayParametrosCabDet['strPrefijoEmpresa']   = $strPrefijoEmpresa;
            $arrayParametrosCabDet['strIdEmpresa']        = $empresaId;
            
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto                       = $this->get('comercial.InfoPunto');
            $arrayNumeroMaximosCorreoTelefono       = $serviceInfoPunto->obtenerNumeroMaximoDeCorreosTelefonos( $arrayParametrosCabDet );
            
            if($arrayNumeroMaximosCorreoTelefono && count($arrayNumeroMaximosCorreoTelefono) > 0)
            {
                $intNumeroMaxCorreos   = $arrayNumeroMaximosCorreoTelefono['intNumeroMaxCorreos'];
                $intNumeroMaxTelefonos = $arrayNumeroMaximosCorreoTelefono['intNumeroMaxTelefonos'];
            }
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($idPer);
            if(!empty($objInfoPersonaEmpresaRol) && is_object($objInfoPersonaEmpresaRol))
            {
                $objCaracteristicaDist = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array('descripcionCaracteristica' => 'ES_DISTRIBUIDOR',
                                                                       'estado'                    => 'Activo'));
                if(!empty($objCaracteristicaDist) && is_object($objCaracteristicaDist))
                {
                    $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->findOneBy(array('estado'              => 'Activo',
                                                                               'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                               'caracteristicaId'    => $objCaracteristicaDist));
                    $strEsDistribuidor = (is_object($objPersonaEmpresaRolCarac) && !empty($objPersonaEmpresaRolCarac)) 
                                          ? $objPersonaEmpresaRolCarac->getValor():"NO";
                }
            }
        }

        $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                 "strEmpresaCod"    => $empresaId);
        $strAplicaTipoOrigenTecnologia   = $this->get('schema.Util')->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
        
        //validación por el consumo del microservicio ms-comp-cliente
        $strStatusBloqueo = "";
        $strMsjBloqueo    = "";
        if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' ) && $rol == 'Cliente')
        {
            $strTipoOrigen = "";
  
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas   = $serviceTokenCas->generarTokenCas();
                        
            $arrayParametros = array();
            $arrayParametros['usrCreacion']    = $usrCreacion;
            $arrayParametros['clienteIp']      = $strIpCreacion;       
            $arrayParametros['token']          = $arrayTokenCas['strToken'];
            $arrayParametros['idEmpresa']      = $empresaId;
            $arrayParametros['idPersona']      = $idCli; 
            $arrayParametros['tipoOrigen']        = $strTipoOrigen;

            $serviceInfoPunto = $this->get('comercial.InfoPunto');
            $arrayResponse    =  $serviceInfoPunto->validacionesPuntoAdicionalMs($arrayParametros);

            if($arrayResponse["strStatus"] == "ERROR")
            {
                $strStatusBloqueo = "ERROR";
                $strMsjBloqueo    = $arrayResponse["strMensaje"];
            } 
            //En caso de existir error se muestra mensaje en pantalla
            if($strStatusBloqueo == "ERROR")
            {
                $session->getFlashBag()->add('error', $strMsjBloqueo); 
            } 
        }


       
        return $this->render('comercialBundle:infopunto:new.html.twig', array('item'                => $entityItemMenu,
                                                                              'entity'              => $entity,
                                                                              'form'                => $form->createView(),
                                                                              'login'               => "",  
                                                                              'cliente'             => $cliente,
                                                                              'idPer'               => $idPer,
                                                                              'oficina'             => $intOficinaId,
                                                                              'prefijoEmpresa'      => $strPrefijoEmpresa,
                                                                              'rol'                 => $rol,
                                                                              'pf_obligatorio'      => $strPfObligatorio,
                                                                              'loginEmpleado'       => $strLoginEmpleado,
                                                                              'nombreEmpleado'      => $strNombreEmpleado,
                                                                              'numeroMaxCorreos'    => $intNumeroMaxCorreos,
                                                                              'numeroMaxTelefonos'  => $intNumeroMaxTelefonos,
                                                                              'strAplicaTipoOrigen' => $strAplicaTipoOrigenTecnologia,
                                                                              'strEsDistribuidor'   => $strEsDistribuidor,
                                                                              'strStatusBloqueo'    => $strStatusBloqueo,
                                                                              'strMsjBloqueo'       => $strMsjBloqueo,
                                                                              'strTipo'             => $strTipo,
                                                                              'strEditarCampos'     =>$strEditarCampos,
                                                                              'intPuntoId'          =>$intPuntoId
                                                                              ));
    }
    
    public function ajaxGeneraLoginAction()
    {
        $request = $this->getRequest();
        $codEmpresa = $request->getSession()->get('idEmpresa');
        $idCanton = trim($request->request->get("idCanton"));
        $idCliente = trim($request->request->get("idCliente"));
        $tipoNegocio = trim($request->request->get("tipoNegocio"));                

        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $response = $serviceInfoPunto->generarLogin($codEmpresa, $idCanton, $idCliente, $tipoNegocio);
        
        return new Response($response);
    }   	
    
    /**
     * @Secure(roles="ROLE_9-3")
     * Documentación para el método 'createAction'.
     *
     * Metodo para guardar informacion de un Punto Cliente
     * Consideracion: Valida Coordenadas correctas
     *
     * Se aumenta campo origen WEB ya que se requiere que se identifiquen los Clientes que han sido ingresados 
     * por la versión Web y los que se ingresaron mediante el Mobil.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 13-03-2015    
     * @version 1.2 26-03-2015                
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
     * @version 1.3 04-12-2015
     * @since 1.2
     * Se modifica el trigger DB_COMERCIAL."BEFORE_INFO_PUNTO_LOGIN" para que evite la inserción repetida del nuevo punto ingresado
     * El error ORA:-20999 controla esta excepción, en caso de darse se redirecciona a la vista de consulta del listado de clientes.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.4 29-07-2016
     * Se envía el parámetro $datos_form['prefijoEmpresa'] al service de creación del punto.
     * Por Excepción: Se verifica que el cliente no tenga padres de facturación válidos para definir de manera obligatoria 
     *                al nuevo punto como padre de facturación.
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>       
     * @version 1.5 08-06-2017
     * Se agrega los parámetros para habilitar datos de envio al array $datos_form tales como: nombreDatoEnvio, sectorDatoEnvio, direccionDatoEnvio, 
     * correoElectronicoDatoEnvio, telefonoDatoEnvio, cuando al punto a crear es padre de facturacion para TN.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>       
     * @version 1.6 30-06-2017
     * Se obtiene el parámetro strNombrePais para validar las coordenadas del País que se envía (Ecuador o Panamá) y la forma de contactos
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.7
     * Se envía por parámetro el tipo de origen del punto.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.8 - 15-08-2019
     * Se retorna el campo strAplicaTipoOrigen en la reedirecion por error a la pagina de inicio.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.9 11-09-2019
     * Se agrega envío de array de parámetros en llamada a función del service para crear nuevo punto.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.0 09-05-2021 - Se agrega lógica para guardar razón social e identificación en caso de ser el punto de un cliente tipo distribuidor.
     *
     * @param  request $request       
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function createAction(Request $request, $idPer)
    {
        $session       = $request->getSession();
        $codEmpresa    = $session->get('idEmpresa');
        $strPrefijo    = $session->get('prefijoEmpresa');
        $usrCreacion   = $session->get('user');
        $intOficinaId  = $session->get('idOficina');
        $strNombrePais = $session->get('strNombrePais');
        $intIdPais     = $session->get('intIdPais');        
        $clientIp      = $request->getClientIp();
        $strError      = '';
        $entity        = new InfoPunto();
        $strEsDistribuidor = "NO";
        $strTipo=$request->request->get('strTipo');
        $serviceUtil   = $this->get('schema.Util');
        $intIdPuntoAnterior = $request->request->get('intPuntoAnterior');
        $strNombrePuntoAtencion=$request->request->get('strNombrePuntodeAtencion');
        $strNombreOrigen=$request->request->get('strNombreOrigen');
        $emGeneral                = $this->get('doctrine')->getManager('telconet_general');
        $arrayDatos = array( 'strTipo'=> $strTipo,
                             'strNombreOrigen'=>$request->request->get('strNombreOrigen'),
                             'strNombrePuntoAtencion'=>$request->request->get('strNombrePuntodeAtencion'),
                             'tipoNegocioId'=>$request->request->get('tipoNegocioId'),
                             'intIdPuntoAnterior' => $intIdPuntoAnterior
                            );
        
        $objDatosForm   = array_merge($arrayDatos,
                                    $request->request->get('infopuntotype'),
                                    $request->request->get('infopuntodatoadicionaltype'),
                                    $request->request->get('infopuntoextratype'),
                                    $request->files->get('infopuntotype')
                                    
                                );
        $strEditarCampos="";
        if($strTipo=="continuo"|$strTipo=="editar")
        {
            $arrayParamEditar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('EDITAR_CAMPOS_PUNTO_MD','COMERCIAL','',
            'EDITAR_CAMPOS','','','',
            '','',$codEmpresa);
            if(count($arrayParamEditar)>0)
            {
                $strEditarCampos    =$arrayParamEditar[0]['valor1'];
            }
        }
       
       $strLoginVendedor  = $objDatosForm['loginVendedor'];
       
       if ( empty( $strLoginVendedor ) )
       {
           $objDatosForm['loginVendedor'] = $request->request->get('loginVend');
       }
       
        $arrayCoordenadas = array(
                'grados_la'           => $request->request->get('grados_la'),
                'minutos_la'          => $request->request->get('minutos_la'),
                'segundos_la'         => $request->request->get('segundos_la'),
                'decimas_segundos_la' => $request->request->get('decimas_segundos_la'),
                'latitud'             => $request->request->get('latitud'),
                'grados_lo'           => $request->request->get('grados_lo'),
                'minutos_lo'          => $request->request->get('minutos_lo'),
                'segundos_lo'         => $request->request->get('segundos_lo'),
                'decimas_segundos_lo' => $request->request->get('decimas_segundos_lo'),
                'longitud'            => $request->request->get('longitud'));
        $arrayDatosForm = array_merge(
                $arrayCoordenadas,     
                $request->request->get('infopuntoextratype'));
        
        //Agrego origen_web al arreglo que se envia al service y le envio con "S"
        $arrayOrigenWeb = array('origen_web'=>'S');
        $objDatosForm     = array_merge($objDatosForm, $arrayOrigenWeb);
        
        $objDatosForm['prefijoEmpresa'] = $strPrefijo;
        
        if ( $strPrefijo == 'TN' )
        {
            if($objDatosForm['esPadreFacturacion'] == 'S')
            {
                $objDatosForm['nombreDatoEnvio']            = $request->request->get('nombreDatoEnvio') ? 
                                                              $request->request->get('nombreDatoEnvio') : '';
                $objDatosForm['sectorDatoEnvio']            = $request->request->get('sectorDatoEnvio') ? 
                                                              $request->request->get('sectorDatoEnvio') : '';
                $objDatosForm['direccionDatoEnvio']         = $request->request->get('direccionDatoEnvio') ? 
                                                              $request->request->get('direccionDatoEnvio') : '';
                $objDatosForm['correoElectronicoDatoEnvio'] = $request->request->get('correoElectronicoDatoEnvio') ? 
                                                              $request->request->get('correoElectronicoDatoEnvio') : '';
                $objDatosForm['telefonoDatoEnvio']          = $request->request->get('telefonoDatoEnvio') ? 
                                                              $request->request->get('telefonoDatoEnvio') : '';
            }
            $objDatosForm['razonSocialCltDistribuidor']    = $request->request->get('razonSocialCltDistribuidor') 
                                                           ? $request->request->get('razonSocialCltDistribuidor') : '';
            $objDatosForm['identificacionCltDistribuidor'] = $request->request->get('identificacionCltDistribuidor') 
                                                           ? $request->request->get('identificacionCltDistribuidor') : '';
            $strEsDistribuidor                           = !empty($objDatosForm['razonSocialCltDistribuidor']) ? "SI":"NO";
        }

        $objDatosForm['intIdPais']     = $intIdPais;
        $objDatosForm['strNombrePais'] = $strNombrePais;
        $objDatosForm['strTipoOrigen'] = $request->request->get('strTipoOrigenSelected') ? $request->request->get('strTipoOrigenSelected') : '';
        $objDatosForm['strTipoOrigen'] = $objDatosForm['strTipoOrigen'] == 'Seleccione' ? '' : $objDatosForm['strTipoOrigen'];
        $objDatosForm['strSolInfCli']  = $request->request->get('solicitarInfoClient') ? $request->request->get('solicitarInfoClient') : '';

        try
        {
            
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto = $this->get('comercial.InfoPunto');
            //Valido el ingreso de Coordenadas del Punto Cliente
            $arrayValidaciones  = $serviceInfoPunto->validaCoordenadas($arrayDatosForm, $strNombrePais);
            if($arrayValidaciones)
            {
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {
                        $strError = $strError . $value . ".\n";
                    }
                }
                throw new \Exception("No se pudo Ingresar el Punto Cliente - " . $strError);
            }
            else
            {       
                
                $serviceTokenCas = $this->get('seguridad.TokenCas');
                $arrayTokenCas = $serviceTokenCas->generarTokenCas();   
                $arrayParametrosPunto =  array('strCodEmpresa'        => $codEmpresa,
                                               'strUsrCreacion'       => $usrCreacion,
                                               'strClientIp'          => $clientIp,
                                               'arrayDatosForm'       => $objDatosForm,
                                               'arrayFormasContacto'  => null,
                                               'token'=> $arrayTokenCas['strToken']
                                              );
                $entity = $serviceInfoPunto->crearPunto($arrayParametrosPunto);

                if ($strPrefijo == 'MD' && $strTipo == 'continuo')
                {
                    return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios',
                    array('id' => $entity->getId(), 'rol' => $objDatosForm['rol'], 
                    'strTipo' => $strTipo, 'intIdPuntoAnterior' => $intIdPuntoAnterior)));
                }
            
                return $this->redirect($this->generateUrl('infopunto_show', 
                array('id' => $entity->getId(), 'rol' => $objDatosForm['rol'])));
            }
        }
        catch (\Exception $e) 
        {
            if(preg_match('/ORA-20999/', $e->getMessage()))
            {
                $arrayOraError = explode('#', $e->getMessage());
                $intIndice = 0;
                //Se ubica el mensaje de error personalizado dentro del error de oracle.
                for($i = 0; i < count($arrayOraError); $i++)
                {
                    if(preg_match('/ERR-20999/', $arrayOraError[$i]))
                    {
                        $intIndice = $i;
                        break;
                    }
                }
                //Se extrae la cadena del mensaje de error personalizado en el trigger*.
                $strMsgError  = $arrayOraError[$intIndice];
                $arrayMensaje = explode('$', $strMsgError);
                if(preg_match("/ $usrCreacion/ ", $arrayMensaje[1]))
                {
                    $session->getFlashBag()->add('notice', 'Ud. ingresó anteriormente este punto, por favor verifique los datos del mismo.');
                    return $this->redirect($this->generateUrl('infopunto_show', array('id' => $arrayMensaje[2], 'rol' => $objDatosForm['rol'])));
                }
                else
                {
                    $session->getFlashBag()->add('error', $arrayMensaje[1]);
                }
            }
            else
            {
                $session->getFlashBag()->add('error', $e->getMessage());
            }///{idCli}/{idPer}/{rol}
            $em               = $this->getDoctrine()->getManager('telconet');
            $em_seguridad     = $this->getDoctrine()->getManager("telconet_seguridad");       
            $entityItemMenu   = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "1");
            $objCliente          = $em->getRepository('schemaBundle:InfoPersona')->find($objDatosForm['personaId']);
            $objInfoPuntoRepo = $em->getRepository('schemaBundle:InfoPunto');
        
            $boolValidaFileDigital = false;
        
            // Se valida que si la empresa es EN sea obligatorio el ingreso del archivo desde el segundo punto del cliente.
            if($strPrefijo == 'EN'  || $strPrefijo == 'MD' )
            {
                $intTotalPtsCliente = $objInfoPuntoRepo->findTotalPtosCliente($objDatosForm['personaId'], $codEmpresa);
                if(intVal($intTotalPtsCliente) > 0)
                {
                    $boolValidaFileDigital = true;
                }
            }
            // Se obtiene el tipo de ubicación "Abierto" para seleccionar por defecto.
            
            $objDatosForm['tipoNegocioId']   = $em->getRepository('schemaBundle:AdmiTipoNegocio')->find($objDatosForm['tipoNegocioId']);
            $objDatosForm['tipoUbicacionId'] = $em->getRepository('schemaBundle:AdmiTipoUbicacion')->find($objDatosForm['tipoUbicacionId']);
            
            $form = $this->createForm(new InfoPuntoType(array('validaFile'        => true, 
                                                              'validaFileDigital' => $boolValidaFileDigital, 
                                                              'empresaId'         => $codEmpresa, 
                                                              'datos'             => $objDatosForm)), $entity);
            $arrayParametros['EMPRESA'] = $codEmpresa;
            $arrayParametros['LOGIN']   = $objDatosForm['loginVendedor'];

            $arrayResultado = $em->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

            $strLoginEmpleado  = '';
            $strNombreEmpleado = '';
            
            if($arrayResultado['TOTAL'] > 0)
            {
                $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
                $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
            }
            
            $strEsError = 'N';
            if($objDatosForm['file'] != null || $objDatosForm['fileDigital'] !=  null)
            {
                $strEsError = 'S';
            }
            
            $strPfObligatorio = 'N';
            
            if($strPrefijo == 'TN')
            {
                $arrayEstados  = array('Anulado', 'Cancel', 'Cancelado', 'Eliminado');
                $arrayPuntosPF = $objInfoPuntoRepo->findPtosPadrePorEmpresaPorCliente($empresaId, $objDatosForm['personaId'], $arrayEstados);

                if($arrayPuntosPF == null || count($arrayPuntosPF) == 0)
                {
                    $strPfObligatorio = 'S';
                }
            }
            else
            {
                $strPfObligatorio = 'S';
            }
            $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                     "strEmpresaCod"    => $codEmpresa);
            $strAplicaTipoOrigenTecnologia   = $this->get('schema.Util')->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
            $serviceUtil->insertError('Telcos', 'InfoPuntoController.createAction', $e->getMessage(), $usrCreacion, '127.0.0.1');
            
            
            return $this->render('comercialBundle:infopunto:new.html.twig', array('item'             => $entityItemMenu,
                                                                                  'entity'           => $entity,
                                                                                  'form'             => $form->createView(),
                                                                                  'login'            => $objDatosForm['login'],  
                                                                                  'cliente'          => $objCliente,
                                                                                  'idPer'            => $idPer,
                                                                                  'oficina'          => $intOficinaId,
                                                                                  'prefijoEmpresa'   => $strPrefijo,
                                                                                  'formasDeContacto' => $objDatosForm['formas_contacto'],
                                                                                  'latitudFloat'     => $objDatosForm['latitudFloat'],
                                                                                  'longitudFloat'    => $objDatosForm['longitudFloat'],
                                                                                  'ptoCoberturaId'   => $objDatosForm['ptoCoberturaId'],
                                                                                  'cantonId'         => $objDatosForm['cantonId'],
                                                                                  'parroquiaId'      => $objDatosForm['parroquiaId'],
                                                                                  'sectorId'         => $objDatosForm['sectorId'],
                                                                                  'canal'            => $objDatosForm['canal'],
                                                                                  'puntoVenta'       => $objDatosForm['punto_venta'],
                                                                                  'esError'          => $strEsError,
                                                                                  'rol'              => $objDatosForm['rol'],
                                                                                  'pf_obligatorio'   => $strPfObligatorio,
                                                                                  'loginEmpleado'    => $strLoginEmpleado,
                                                                                  'nombreEmpleado'   => $strNombreEmpleado,
                                                                                  'strAplicaTipoOrigen' => $strAplicaTipoOrigenTecnologia,
                                                                                  'strEsDistribuidor'   => $strEsDistribuidor,
                                                                                  'strTipo'             =>$strTipo,
                                                                                  'strEditarCampos'=>$strEditarCampos,
                                                                                  'intPuntoId'=>$intIdPuntoAnterior,
                                                                                  'strNombreOrigen'=>$strNombreOrigen,
                                                                                  'strNombrePuntoAtencion'=>$strNombrePuntoAtencion));
        }        
    }
  	
    /**
     * Función que obtiene el tipo de origen del punto
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 28-01-2019
     */
    public function getTipoOrigenAction()
    {
        $objRequest            = $this->getRequest();
        $strEmpresaCod         = $objRequest->getSession()->get('idEmpresa');
        $arrayOrigenPunto      = $this->get('comercial.InfoPunto')->getTipoOrigenPunto(array("strEmpresaCod" => $strEmpresaCod));
        return new Response(json_encode(array("registros"=> $arrayOrigenPunto)));
    }
    /**
     * @Secure(roles="ROLE_9-4")
     * Documentación para el método 'editAction'.
     *
     * Obtiene y muestra informacion del Punto y permite la edicion de la informacion completa del Punto
     * Consideracion: Verifica si tiene permitido realizar la edicion de la informacion del Punto
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 10-03-2015            
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.2 18-12-2015
     * Se agrega la visualización del Canal y del punto de venta en la Edición del punto del cliente.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.3 22-03-2016
     * Se envía y se valida el prefijo de la empresa, de ser MegaDatos se permite la selección del Canal y Punto de venta.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>       
     * @version 1.4 17-05-2016
     * Se valida campo ElementoId de la tabla InfoPuntoDatoAdicional
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.5 01-09-2016
     * Se modifica la obtención del usuario vendedor por el método estándar considerando todos los estados.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.6
     * @since 28-01-2019
     * Se envía a la vista si aplica al flujo de tipo de origen del punto.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 09-06-2021 -Se parametriza que la pantalla de edicion de Punto permita o no la edicion de datos geograficos: 
     *                          en base a parametro: 'PARAMETROS_REINGRESO_OS_AUTOMATICA' detalle: 'PERMITE_EDITAR_DATOS_GEOGRAFICOS'
     *                          Se habilita la actualización de Jurisdiccion, canton, parroquia, sector si el parametro esta en "S"
     * 
     * Los Canales almacenan en los campos:
     * Valor1 => Identificador del Canal.
     * Valor2 => Descriptivo del Canal.
     * Valor3 => Identificador de grupo 'CANAL' .
     * 
     * Los Puntos de Venta almacenan en los campos:
     * Valor1 => Identificador del Punto de Venta.
     * Valor2 => Descriptivo del Punto de Venta.
     * Valor3 => Identificador del Canal.
     * Valor4 => Descriptivo del Canal.
     * Valor5 => Nombre de la oficina asociada al punto de venta.
     *                          
     * @param integer $id   //Id del punto 
     * @param integer $rol  //rol que posee el registro : Pre-cliente / Cliente
     * @return Renders a view.
     * 
     */
    public function editAction($id, $rol)
    {
        $em                       = $this->getDoctrine()->getManager();

        $em_seguridad             = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral                = $this->get('doctrine')->getManager('telconet_general');
        $entityItemMenu           = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "4");
        $request                  = $this->getRequest();
        $idEmpresa                = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa        = $request->getSession()->get('prefijoEmpresa');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto         = $this->get('comercial.InfoPunto');
        $datos                    = $serviceInfoPunto->obtenerDatosPunto($id);
        $entity                   = $datos['punto'];
        $entityPuntoDatoAdicional = $datos['puntoDatoAdicional'];
        $cliente                  = $datos['cliente'];
        $entitySector             = $datos['sector'];
        $entityParroquia          = $datos['parroquia'];
        $boolCamposLectura        = false;
        if( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        //Verifico si entro en edicion de Punto que tenga permitido la edicion del Punto 
        $boolPermiteEditarPto = $em->getRepository('schemaBundle:InfoServicio')->verificaPermiteEditarPto($id);
        if($boolPermiteEditarPto==false)
        {
            $this->get('session')->getFlashBag()->add('notice', 'No tiene permitido editar la informacion del Punto.');
            return $this->redirect($this->generateUrl('infopunto_show', array('id' => $entity->getId(), 'rol' => $rol)));
        }
        else
        {

            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto   = $this->get('comercial.InfoPunto');
            
            //se agrega el nombre del nodo cliente
            if($entityPuntoDatoAdicional && $entityPuntoDatoAdicional->getElementoId())
            {
                $objNodoCliente       = $em->getRepository('schemaBundle:InfoElemento')
                                           ->findOneById($entityPuntoDatoAdicional->getElementoId()->getId());
                $strNombreNodoCliente = "";
                if($objNodoCliente)
                {
                    $strNombreNodoCliente = $objNodoCliente->getNombreElemento();
                    $intIdNodoCliente     = $objNodoCliente->getId();
                }
            }
            // Solo la empresa MegaDatos puede mostrar el canal y punto de venta en la edición, sin embargo NO se permite modificar estos valores.
            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {
                $boolCamposLectura = true;
                /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                // Se consulta el Canal y el Punto de venta asociado al Punto.
                $objCanalPuntoVenta = $serviceInfoPunto->getCanalPuntoVenta($entity, $idEmpresa);
                $intCuantos = $em->getRepository('schemaBundle:InfoPunto')->getSolicitudRechazada($entity->getId());
                if ($intCuantos >0)
                {
                    $boolCamposLectura = false;                    
                }
            }
            else
            {
                $objCanalPuntoVenta['strCanal']          = '';
                $objCanalPuntoVenta['strCanalDesc']      = '';
                $objCanalPuntoVenta['strPuntoVenta']     = '';
                $objCanalPuntoVenta['strPuntoVentaDesc'] = '';
            }
            
            $strLoginEmpleado  = '';
            $strNombreEmpleado = '';
            
            if($entity->getUsrVendedor())
            {            
                $arrayParametros['EMPRESA'] = $idEmpresa;
                $arrayParametros['LOGIN']   = $entity->getUsrVendedor();            
                $arrayParametros['TODOS']   = true; // El vendedor puede tener rol diferente a Activo o Modificado

                $arrayResultado = $em->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

                if($arrayResultado['TOTAL'] > 0)
                {
                    $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
                    $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
                }           
            }
            
            $editForm = $this->createForm(new InfoPuntoType(array('validaFile'        => true, 
                                                                  'validaFileDigital' => true, 
                                                                  'empresaId'         => $idEmpresa)), $entity);
            
            $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                     "strEmpresaCod"    => $idEmpresa);
            $strAplicaTipoOrigenTecnologia   = $this->get('schema.Util')->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
            if ('S' == $strAplicaTipoOrigenTecnologia)
            {
                //Se obtiene si el cliente tiene el tipo de origen del punto.
                $arrayTipoOrigen = $this->get('comercial.InfoPunto')->getCaractTipoOrigenPuntoxIdPunto(array("strEmpresaCod" => $idEmpresa,
                                                                                                             "objPuntoId"    => $entity));
            }
            
            //Se obtiene el parametro para habilitar si permite o no edición de Datos geograficos.
            $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_REINGRESO_OS_AUTOMATICA', 
                                                         'COMERCIAL', '', 'PERMITE_EDITAR_DATOS_GEOGRAFICOS',
                                                         '', '', '', '', '', $idEmpresa);

            $strEditaDatosGeograficos  = (isset($arrayValoresParametros["valor1"])
                                          && !empty($arrayValoresParametros["valor1"])) ? $arrayValoresParametros["valor1"]
                                          : 'S';  
            
            return $this->render('comercialBundle:infopunto:edit.html.twig', array(
                'item'                     => $entityItemMenu,
                'entity'                   => $entity,
                'entityPuntoDatoAdicional' => $entityPuntoDatoAdicional,
                'edit_form'                => $editForm->createView(),
                'login'                    => $entity->getLogin(),
                'cliente'                  => $cliente,
                'rol'                      => $rol,
                'loginEmpleado'            => $strLoginEmpleado,
                'nombreEmpleado'           => $strNombreEmpleado,
                'prefijoEmpresa'           => $strPrefijoEmpresa,
                'canal'                    => $objCanalPuntoVenta['strCanal'],          // Identificador del Canal
                'canalDesc'                => $objCanalPuntoVenta['strCanalDesc'],      // Nombre del Canal
                'puntoVenta'               => $objCanalPuntoVenta['strPuntoVenta'],     // Identificador del Punto de Venta
                'puntoVentaDesc'           => $objCanalPuntoVenta['strPuntoVentaDesc'], // Nombre del Punto de Venta
                'cantonId'                 => $entityParroquia->getCantonId()->getId(),
                'parroquiaId'              => $entitySector->getParroquiaId()->getId(),
                'nombreNodoCliente'        => $strNombreNodoCliente,
                'idNodoCliente'            => $intIdNodoCliente,
                'arrayTipoOrigenSelected'  => $arrayTipoOrigen,
                'strAplicaTipoOrigen'      => $strAplicaTipoOrigenTecnologia,
                'strEditaDatosGeograficos' => $strEditaDatosGeograficos,
                'boolCamposLectura'        => $boolCamposLectura
            ));
        }
    }
    
    /**
     * @Secure(roles="ROLE_9-5")
     * Documentación para el método 'updateAction'.
     *
     * Guarda formulario para editar informacion del Punto
     * 
     * Consideracion: Valida Coordenadas correctas    
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 10-03-2015
     *    
     * Consideracion: Se Valida el ingreso de las formas de Contacto
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.2 02-09-2015 
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.3 18-12-2015
     * Se agrega el guardado del Canal y del punto de venta en la Edición del punto del cliente.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.4 22-03-2016
     * Se valida el prefijo de la empresa, de ser MegaDatos se permite la actualización del Canal y Punto de venta.
     *                     
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.5 29-07-2016
     * Se elimina la actualización del campo esPadreFacturacion = 'S'
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.6 01-09-2016
     * Se agrega el envío del parámetro $arrayParametros['TODOS'] = true que indica que no se filtre por el estado del rol del vendedor.
     *     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" y se agrega strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.8 08-12-2016
     * Se habilita edición de punto con dependencia de edificio.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>       
     * @version 1.9 30-06-2017
     * Se obtiene el parámetro strNombrePais para validar las coordenadas del País que se envía (Ecuador o Panamá) y las validaciones 
     * de forma de contacto
     * 
     * 
     * @author Jorge Veliz <jlveliz@telconet.ec>       
     * @version 2.0 30-06-2021
     * consumo del ms 
     *
     * @author Joel Broncano <jbroncano@telconet.ec>     se agrega las  validaciones para EN   
     * @version 2.1 30-06-2021
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 2.2 10-02-2023 Se agregan nuevas variables
     *                      origen de requerimiento y punto de atencion para enviar a guardar en la edicion si proviene por el nuevo flujo
     *                          Se agrega validacion para redireccion a vista de traslados en caso de ser nuevo flujo por el campo strTipo
     *                     
     * @param request $request   
     * @param integer $id   //Id del punto 
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function updateAction(Request $request, $id)
    {
        $em                = $this->getDoctrine()->getManager('telconet');
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();
        $entity            = $em->getRepository('schemaBundle:InfoPunto')->find($id);
        $empresaId         = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $intOficinaId      = $session->get('idOficina');
        $intIdPais         = $session->get('intIdPais');
        $strNombrePais     = $session->get('strNombrePais');
        $datos_form        = $request->request->get('infopuntotype');
        $datos_form_ad     = $request->request->get('infopuntodatoadicionaltype');
        $datos_form_extra  = $request->request->get('infopuntoextratype');
        $datos_form_files  = $request->files->get('infopuntotype');
        $serviceUtil       = $this->get('schema.Util');
        $strLogin          = $session->get('user');
        $entity->setFile($datos_form_files['file']);
        $boolCamposLectura        = false;
        $strTipo=$request->request->get('strTipo');
        $strNombreOrigen=$request->request->get('strNombreOrigen');
        $strNombrePuntoAtencion=$request->request->get('strNombrePuntodeAtencion');
        $strClientIp                 = $request->getClientIp();
        $emGeneral                = $this->get('doctrine')->getManager('telconet_general');
        $intIdOrigen=0;
        if($strTipo!=null&&empty($datos_form['tipoNegocioId'])&&$strPrefijoEmpresa=="MD")
        {
            $datos_form['tipoNegocioId']=$request->request->get('tipoNegocioId');
            $objOrigenCaracteristica = $em->getRepository("schemaBundle:AdmiCaracteristica")
            ->findOneBy(
                array(
                    "descripcionCaracteristica" => 'ORIGEN_REQUERIMIENTO',
                    "estado"                    => 'Activo'
                )
            );

        if(empty($objOrigenCaracteristica) || !is_object($objOrigenCaracteristica))
        {
            throw new \Exception("No se encontró característica ORIGEN_REQUERIMIENTO, con los parámetros enviados.");
        }
        $objPuntoAtencionCaracteristica  = $em->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(
                        array(
                            "descripcionCaracteristica" => 'PUNTO_ATENCION',
                            "estado"                    => 'Activo'
                        )
                    );

                if(empty($objPuntoAtencionCaracteristica) || !is_object($objPuntoAtencionCaracteristica))
                {
                    throw new \Exception("No se encontró característica PUNTO_ATENCION, con los parámetros enviados.");
                }
                $objIdOrigenPuntoCaracteristica = $em->getRepository("schemaBundle:AdmiCaracteristica")
            ->findOneBy(
                array(
                    "descripcionCaracteristica" => 'PUNTO_ORIGEN_CREACION',
                    "estado"                    => 'Activo'
                )
            );

        if(empty($objIdOrigenPuntoCaracteristica) || !is_object($objIdOrigenPuntoCaracteristica))
        {
            throw new \Exception("No se encontró característica PUNTO_ORIGEN_CREACION, , con los parámetros enviados.");
        }

        $objIdPuntoCaracteristicaOrigen=$em->getRepository("schemaBundle:InfoPuntoCaracteristica")
        ->findOneBy(
            [
                'puntoId'=>$id,
                'caracteristicaId'=>$objIdOrigenPuntoCaracteristica->getId(),
                'estado'=>'Activo'
            ]
        );
        $intIdOrigen=$objIdPuntoCaracteristicaOrigen!=null?$objIdPuntoCaracteristicaOrigen->getValor():0;
        $objInfoPuntoCaracteristicaOrigen=$em->getRepository("schemaBundle:InfoPuntoCaracteristica")
        ->findOneBy(
            [
                'puntoId'=>$id,
                'caracteristicaId'=>$objOrigenCaracteristica->getId(),
                'estado'=>'Activo'
            ]
        );
        $objInfoPuntoCaracteristicaAtencion=$em->getRepository("schemaBundle:InfoPuntoCaracteristica")
        ->findOneBy(
            [
                'puntoId'=>$id,
                'caracteristicaId'=>$objPuntoAtencionCaracteristica->getId(),
                'estado'=>'Activo'
            ]
        );
        if($objInfoPuntoCaracteristicaOrigen!=null)
        {
            $objInfoPuntoCaracteristicaOrigen->setValor($strNombreOrigen);
            $em->persist($objInfoPuntoCaracteristicaOrigen);
            if($objInfoPuntoCaracteristicaAtencion!=null)
            {
                if($strNombreOrigen=="ATC")
                {
                    $objInfoPuntoCaracteristicaAtencion->setValor($strNombrePuntoAtencion);
                    $em->persist($objInfoPuntoCaracteristicaAtencion);
                }
                else
                {
                    $objInfoPuntoCaracteristicaAtencion->setEstado('Inactivo');
                    $em->persist($objInfoPuntoCaracteristicaAtencion);
                }
                
            }
            else
            {
                if($strNombreOrigen=="ATC")
                {
                    $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristica->setValor($strNombrePuntoAtencion);
                    $objInfoPuntoCaracteristica->setCaracteristicaId($objPuntoAtencionCaracteristica);
                    $objInfoPuntoCaracteristica->setPuntoId($entity);
                    $objInfoPuntoCaracteristica->setEstado('Activo');
                    $objInfoPuntoCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoCaracteristica->setUsrCreacion($strLogin);
                    $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);
            
                    $em->persist($objInfoPuntoCaracteristica);
                }
            }
        
        }
        
        }

        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto  = $this->get('comercial.InfoPunto');

        $strError          ='';   
        $formas_contacto   = array();
        $arrayCoordenadas  = array(
                'grados_la'           => $request->request->get('grados_la'),
                'minutos_la'          => $request->request->get('minutos_la'),
                'segundos_la'         => $request->request->get('segundos_la'),
                'decimas_segundos_la' => $request->request->get('decimas_segundos_la'),
                'latitud'             => $request->request->get('latitud'),
                'grados_lo'           => $request->request->get('grados_lo'),
                'minutos_lo'          => $request->request->get('minutos_lo'),
                'segundos_lo'         => $request->request->get('segundos_lo'),
                'decimas_segundos_lo' => $request->request->get('decimas_segundos_lo'),
                'longitud'            => $request->request->get('longitud'));
        $arrayDatosForm = array_merge(
                $arrayCoordenadas,     
                $request->request->get('infopuntoextratype'));
        
        $array_formas_contacto = explode(",", $datos_form_extra['formas_contacto']);
        $a = 0;
        $x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++)
        {
            if ($a == 3) 
            {
                $a = 0;
                $x++;
            }
            if ($a == 1)
                $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)
                $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }        

        $em->getConnection()->beginTransaction();
        try{
            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            
            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             * que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $strPrefijoEmpresa;
            $arrayParamFormasContac['arrayFormasContacto'] = $formas_contacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
            $arrayParamFormasContac['strNombrePais']       = $strNombrePais;
            $arrayParamFormasContac['intIdPais']           = $intIdPais;
            $arrayValidaciones   = $serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
            if($arrayValidaciones)
            {    
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {                      
                        $strError = $strError.$value.".\n";                        
                    }
                }
                throw new \Exception("No se pudo Editar el Punto Cliente - " . $strError);
            } 
            //Edita datos del punto
            $entityAdmiJurisdiccion=$em->getRepository('schemaBundle:AdmiJurisdiccion')->find($datos_form_extra['ptoCoberturaId']);
            $entity->setPuntoCoberturaId($entityAdmiJurisdiccion);
            $entityAdmiTipoNegocio=$em->getRepository('schemaBundle:AdmiTipoNegocio')->find($datos_form['tipoNegocioId']);
            $entity->setTipoNegocioId($entityAdmiTipoNegocio);
            $entityAdmiTipoUbicacion=$em->getRepository('schemaBundle:AdmiTipoUbicacion')->find($datos_form['tipoUbicacionId']);
            $entity->setTipoUbicacionId($entityAdmiTipoUbicacion);
            $entityAdmiSector=$em->getRepository('schemaBundle:AdmiSector')->find($datos_form_extra['sectorId']);
            $entity->setLogin($datos_form_ad['login']);                
            $entity->setSectorId($entityAdmiSector);                
            $entity->setDireccion($datos_form['direccion']);
            $entity->setDescripcionPunto($datos_form['descripcionpunto']);                
            $entity->setNombrePunto($datos_form['nombrepunto']);                
            $entity->setSectorId($entityAdmiSector);     
                        
            //Valido el ingreso de Coordenadas del Punto Cliente
            $arrayValidaciones  = $serviceInfoPunto->validaCoordenadas($arrayDatosForm,$strNombrePais);
            if($arrayValidaciones)
            {
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {
                        $strError = $strError . $value . ".\n";
                    }
                }
                throw new \Exception("No se pudo Editar el Punto Cliente - " . $strError);
            }
        
            $entity->setLatitud($datos_form_extra['latitudFloat']);
            $entity->setLongitud($datos_form_extra['longitudFloat']);
            $entity->setUsrVendedor($datos_form_extra['loginVendedor']);				
            $entity->setObservacion($datos_form['observacion']);				
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setAccion('Actualizacion Punto');            
            $entity->setIpUltMod($peticion->getClientIp());            
            $entity->setUsrUltMod($session->get('user'));
            if ($entity->getFile())
            {
                $entity->preUpload();
                $strNombreApp       = 'TelcosWeb';
                $arrayPathAdicional = [];
                $strSubModulo = 'PuntoCroquis';

                $arrayParamNfs          = array(
                    'prefijoEmpresa'       => $strPrefijoEmpresa,
                    'strApp'               => $strNombreApp,
                    'strSubModulo'         => $strSubModulo,
                    'arrayPathAdicional'   => $arrayPathAdicional,
                    'strBase64'            => base64_encode(file_get_contents($entity->getFile())),
                    'strNombreArchivo'     => $entity->getPath(),
                    'strUsrCreacion'       => $strLogin);
                $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                if(isset($arrayRespNfs))
                {
                    if($arrayRespNfs['intStatus'] == 200)
                    {
                        $entity->setPath($arrayRespNfs['strUrlArchivo']);
                        $entity->setFile(null);
                        $em->persist($entity);
                    }
                    else
                    {
                        throw new \Exception('No se pudo crear el punto, error al cargar el croquis');
                    }
                }
                else
                {
                    throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                }

            }
            if ($entity->getFileDigital())
            {
                $entity->preUploadDigital();
                $entity->uploadDigital();
            }                
            $em->persist($entity);
            //Se actualiza/Crea la característica por tipo de origen del punto
            //Sólo el usuario con perfil puede actualizar la característica.
            $booleanTipoOrigenRol            = $this->get('security.context')->isGranted('ROLE_9-6377');
            $strTipoOrigenSelected           = $request->request->get('strTipoOrigenSelected');
            $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                     "strEmpresaCod"    => $empresaId);
            $strAplicaTipoOrigenTecnologia   = $this->get('schema.Util')->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
            if ("S" == $strAplicaTipoOrigenTecnologia && $booleanTipoOrigenRol)
            {
                $strMensaje            = " crea ";
                $booleanHistorial      = false;
                $arrayOrigenPunto      = $serviceInfoPunto->getCaractTipoOrigenPuntoxIdPunto(array("strEmpresaCod" => $empresaId,
                                                                                                   "objPuntoId"    => $entity));
                if ($arrayOrigenPunto["valores"]["intInfoPuntoCaracteristicaId"] > 0 &&
                    $strTipoOrigenSelected != $arrayOrigenPunto["valores"]["strDescripcionCaracteristica"])
                {
                    $objInfoPuntoCaracteristica = $em->getRepository("schemaBundle:InfoPuntoCaracteristica")
                                                     ->findOneById($arrayOrigenPunto["valores"]["intInfoPuntoCaracteristicaId"]);
                    $objInfoPuntoCaracteristica->setEstado("Inactivo");
                    $em->persist($objInfoPuntoCaracteristica);
                    $strMensaje       = " actualiza ";
                    $booleanHistorial = true;
                }
                if ($strTipoOrigenSelected && $strTipoOrigenSelected != $arrayOrigenPunto["valores"]["strDescripcionCaracteristica"])
                {
                    $objInfoCaracteristica   = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                  ->findOneBy(array("descripcionCaracteristica" => $strTipoOrigenSelected,
                                                                    "estado"                    => 'Activo'));
                    $objCaracteristicaOrigen = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                  ->findOneBy(array("descripcionCaracteristica" => 'TIPO_ORIGEN_TECNOLOGIA',
                                                                    "estado"                    => 'Activo'));
                    if (is_null($objInfoCaracteristica))
                    {
                        throw new \Exception("No es posible registrar la característica seleccionada para el tipo de origen del punto.");
                    }
                    if (is_null($objCaracteristicaOrigen))
                    {
                        throw new \Exception("No es posible registrar la característica para el tipo de origen del punto.");
                    }
                    $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristica->setValor($objInfoCaracteristica->getId());
                    $objInfoPuntoCaracteristica->setCaracteristicaId($objCaracteristicaOrigen);
                    $objInfoPuntoCaracteristica->setPuntoId($entity);
                    $objInfoPuntoCaracteristica->setEstado('Activo');
                    $objInfoPuntoCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoCaracteristica->setUsrCreacion($session->get('user'));
                    $objInfoPuntoCaracteristica->setIpCreacion($request->getClientIp());
                    $em->persist($objInfoPuntoCaracteristica);
                    $booleanHistorial = true;
                }
                if ($booleanHistorial)
                {
                    //Se inserta el historial por agregar la característica
                    $objInfoPuntoHistorial = new InfoPuntoHistorial();
                    $objInfoPuntoHistorial->setPuntoId($entity);
                    $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoHistorial->setUsrCreacion($session->get('user'));
                    $objInfoPuntoHistorial->setIpCreacion($request->getClientIp());
                    $objInfoPuntoHistorial->setValor('Se' .$strMensaje .'la característica por el tipo de origen de la tecnología.');
                    $em->persist($objInfoPuntoHistorial);
                }
            }
            
            // Solo en la empresa MegaDatos se puede validar la actualización del punto en sus valores de Canal y Punto de venta.
            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' )
            {
                //Criterios de consulta de la característica del punto
                $arrayParametros           = array('entityPunto'                  => $entity,
                                                   'strDescripcionCaracteristica' => 'PUNTO_DE_VENTA_CANAL',
                                                   'strEstado'                    => 'Activo');
                $entityPuntoCaracteristica = $em->getRepository('schemaBundle:InfoPuntoCaracteristica')->getOnePuntoCaracteristica($arrayParametros);

                //Se crea la característica de punto de venta si no existe
                $arrayParametros['ESUPDATE']      = "SI"; 
                $arrayParametros['PUNTO']         = $entity;
                $arrayParametros['CANAL']         = $datos_form_extra['canal'];
                $arrayParametros['PUNTOVENTA']    = $datos_form_extra['punto_venta'];
                $arrayParametros['OFICINAID']     = $intOficinaId;
                $arrayParametros['CLIENTEIP']     = $request->getClientIp();
                $arrayParametros['USRCREACION']   = $session->get('user');
                $arrayParametros['EMPRESACOD']    = $empresaId;                
                if($entityPuntoCaracteristica == null)
                {
                    $arrayParametros['ESUPDATE']      = "NO"; 
                } 
                $serviceInfoPunto->guardarCanalPuntoVenta($arrayParametros);

            }

            //Edita datos adicionales del punto
            $entityInfoPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($id);
            $entityInfoPuntoDatoAdicional->setDependeDeEdificio($datos_form_ad['dependedeedificio']);
            
            if ( $datos_form_ad['dependedeedificio'] == 'S'){
                    $entityInfoElemento = $em->getRepository('schemaBundle:InfoElemento')->find($datos_form_ad['puntoedificioid']);
                    $entityInfoPuntoDatoAdicional->setElementoId($entityInfoElemento);
            }
            else
            {
                    $entityInfoPuntoDatoAdicional->setElementoId(NULL);
            }

            $entityInfoPuntoDatoAdicional->setFeUltMod(new \DateTime('now'));
            $entityInfoPuntoDatoAdicional->setUsrUltMod($session->get('user'));
            $this->get('schema.Validator')->validateAndThrowException($entityInfoPuntoDatoAdicional);
            $em->persist($entityInfoPuntoDatoAdicional);
                        
            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            $arrayPersonaFormasContacto = $em->getRepository('schemaBundle:InfoPuntoFormaContacto')
                ->findPorEstadoPorPunto($entity->getId(), 'Activo', 10000, 0);
            $ObjPersonasFormasContacto  = $arrayPersonaFormasContacto['registros'];
            if($ObjPersonasFormasContacto)
            {
                foreach($ObjPersonasFormasContacto as $emp)
                {
                    $emp->setEstado('Inactivo');
                    $em->persist($emp);
                }
            }                       
            //REGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i=0;$i < count($formas_contacto);$i++)
            {
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')
                                        ->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);                
                $entity_punto_forma_contacto=$em->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                        ->findBy(array('puntoId' => $entity->getId(),'valor'=>$formas_contacto[$i]["valor"],
                                                       'formaContactoId'=>$entityAdmiFormaContacto->getId())); 
                if($entity_punto_forma_contacto)
                {                    
                    foreach($entity_punto_forma_contacto as $entityFormaContacto)
                    {
                        $entityFormaContacto->setEstado('Activo');
                        $em->persist($entityFormaContacto);
                    }
                }
                else
                {                    
                    $entity_persona_forma_contacto = new InfoPuntoFormaContacto();
                    $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                    $entity_persona_forma_contacto->setEstado("Activo");
                    $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                    $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')
                        ->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                    $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entity_persona_forma_contacto->setIpCreacion($request->getClientIp());
                    $entity_persona_forma_contacto->setPuntoId($entity);
                    $entity_persona_forma_contacto->setUsrCreacion($session->get('user'));
                    $em->persist($entity_persona_forma_contacto);
                }
            }                        
            $em->flush();                    
            $em->getConnection()->commit();

            if ($strPrefijoEmpresa == 'MD' && $strTipo == 'editar')
            {
                return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios', 
                array('id' => $entity->getId(), 'rol' => $datos_form_extra['rol'], 
                'strTipo' => 'continuo', 'intIdPuntoAnterior' => $intIdOrigen)));
            }
            return $this->redirect($this->generateUrl('infopunto_show', array('id' => $entity->getId(),'rol'=>$datos_form_extra['rol'])));
        }
        catch (\Exception $e) 
        {
            $em_seguridad      = $this->getDoctrine()->getManager("telconet_seguridad");
            $entityItemMenu    = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "4");
        
            $em->getConnection()->rollback();
            $em->getConnection()->close();             
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            
            $datos                    = $serviceInfoPunto->obtenerDatosPunto($id);
            $cliente                  = $datos['cliente'];
            $entityPuntoDatoAdicional = $datos['puntoDatoAdicional'];
            
            $datos_form['tipoNegocioId']   = $em->getRepository('schemaBundle:AdmiTipoNegocio')->find($datos_form['tipoNegocioId']);
            $datos_form['tipoUbicacionId'] = $em->getRepository('schemaBundle:AdmiTipoUbicacion')->find($datos_form['tipoUbicacionId']);
        
            $editForm = $this->createForm(new InfoPuntoType(array('validaFile'        => true, 
                                                                  'validaFileDigital' => true, 
                                                                  'datos'             => $datos_form, 
                                                                  'empresaId'         => $empresaId)), $entity);
            $arrayParametros['EMPRESA'] = $empresaId;
            $arrayParametros['LOGIN']   = $datos_form_extra['loginVendedor'];
            $arrayParametros['TODOS']   = true; // El vendedor puede tener rol diferente a Activo o Modificado

            $arrayResultado = $em->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

            $strLoginEmpleado  = '';
            $strNombreEmpleado = '';
            
            if($arrayResultado['TOTAL'] > 0)
            {
                $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
                $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
            }
            
            // Solo la empresa MegaDatos puede mostrar el canal y punto de venta en la edición, sin embargo NO se permite modificar estos valores.
            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {
                /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                // Se consulta el Canal y el Punto de venta asociado al Punto.
                $objCanalPuntoVenta = $serviceInfoPunto->getCanalPuntoVenta($entity, $empresaId);
            }
            else
            {
                $objCanalPuntoVenta['strCanal']          = '';
                $objCanalPuntoVenta['strCanalDesc']      = '';
                $objCanalPuntoVenta['strPuntoVenta']     = '';
                $objCanalPuntoVenta['strPuntoVentaDesc'] = '';
            }
            
             //se agrega el nombre del nodo cliente
            if($entityPuntoDatoAdicional->getElementoId())
            {
                $objNodoCliente       = $em->getRepository('schemaBundle:InfoElemento')
                                           ->findOneById($entityPuntoDatoAdicional->getElementoId()->getId());
                $strNombreNodoCliente = "";
                if($objNodoCliente)
                {
                    $strNombreNodoCliente = $objNodoCliente->getNombreElemento();
                    $intIdNodoCliente     = $objNodoCliente->getId();
                }
            }

            if($strTipo=="editar")
            {
                $strEditarCampos="";
                $arrayParamEditar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('EDITAR_CAMPOS_PUNTO_MD','COMERCIAL','',
                'EDITAR_CAMPOS','','','',
                '','',$empresaId);
                if(count($arrayParamEditar)>0)
                {
                    $strEditarCampos    =$arrayParamEditar[0]['valor1'];
                }
                $arrayParametros=array
                (
                    'intPuntoId'=>$id,
                    'intEmpresaId'=>$empresaId,
                    'strPrefijoEmpresa'=>$strPrefijoEmpresa,
                    'strTipo'=>$strTipo,
                    'strEditarCampos'=>$strEditarCampos,
                    'intOficinaId'=>$intOficinaId,
                    'mensajeError'=>$e,
                    'session'=>$this->get('session')
                                );
              return  $this->EditarPuntoNuevoProceso($arrayParametros);
        
            }
            
            return $this->render('comercialBundle:infopunto:edit.html.twig', 
                                 array( 'item'                     => $entityItemMenu,
                                        'entity'                   => $entity,
                                        'entityPuntoDatoAdicional' => $entityPuntoDatoAdicional,
                                        'edit_form'                => $editForm->createView(),
                                        'login'                    => $entity->getLogin(),
                                        'cliente'                  => $cliente,
                                        'rol'                      => $datos_form_extra['rol'],
                                        'loginEmpleado'            => $strLoginEmpleado,
                                        'nombreEmpleado'           => $strNombreEmpleado,
                                        'prefijoEmpresa'           => $strPrefijoEmpresa,
                                        'formasDeContacto'         => $datos_form_extra['formas_contacto'],
                                        'latitudFloat'             => $datos_form_extra['latitudFloat'],
                                        'longitudFloat'            => $datos_form_extra['longitudFloat'],
                                        'cargaFormasContacto'      => false,
                                        'canal'                    => $objCanalPuntoVenta['strCanal'],          // Identificador del Canal
                                        'canalDesc'                => $objCanalPuntoVenta['strCanalDesc'],      // Nombre del Canal
                                        'puntoVenta'               => $objCanalPuntoVenta['strPuntoVenta'],     // Identificador del Punto de Venta
                                        'puntoVentaDesc'           => $objCanalPuntoVenta['strPuntoVentaDesc'], // Nombre del Punto de Venta
                                        'ptoCoberturaId'           => $datos_form_extra['ptoCoberturaId'],
                                        'cantonId'                 => $datos_form_extra['cantonId'],
                                        'parroquiaId'              => $datos_form_extra['parroquiaId'],
                                        'sectorId'                 => $datos_form_extra['sectorId'],
                                        'nombreNodoCliente'        => $strNombreNodoCliente,
                                        'idNodoCliente'            => $intIdNodoCliente,
                                        'boolCamposLectura'        => $boolCamposLectura
                                    ));
        }
    }	
	
    /**
    * @Secure(roles="ROLE_9-8")
    */ 
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPunto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPunto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infopunto'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function ajaxGetEdificiosAction() {
        $request = $this->getRequest();
        $session  = $request->getSession();        
        $nombre = $request->get("nombre");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $codEmpresa = $session->get('idEmpresa');
        
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $resultado = $serviceInfoPunto->obtenerPuntosEdificios($codEmpresa, $nombre, $limit, $page, $start);
        
        $arreglo = $resultado['registros'];
        if (empty($arreglo))
        {
            $arreglo = array(array(
                            'idPto' => "",
                            'cliente' => "",
                            'login' => "",
                            'descripcionPunto' => "",
                            'Direccion' => ""
            ));
        }
        $response = new Response(json_encode(array('total' => $resultado['total'], 'edificios' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }  
    
    /**
    * Documentación para el método 'ajaxGetPuntosAction'.
    *
    * Obtiene puntos de un cliente.
    * @param $idCli (id de cliente que solicita puntos), $rol (rol de quien obtiene los puntos)
    * @return json con informacion de puntos.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 23-09-2014
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 11-12-2014
    * @since 1.0
    */
   public function ajaxGetPuntosAction($idCli, $rol)
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strNombre      = $objRequest->get("nombre");
        $intLimit       = $objRequest->get("limit");
        $intPage        = $objRequest->get("page");
        $intStart       = $objRequest->get("start");
        $intIdEsPadre   = $objRequest->get("idespadre");
        $intIdEmpresa   = $objSession->get('idEmpresa');
        $em             = $this->get('doctrine')->getManager('telconet');
        $arrayInfoPunto = $em->getRepository('schemaBundle:InfoPunto')
                             ->findPtosPorEmpresaPorClientePorRol($intIdEmpresa, 
                                                                  $idCli, 
                                                                  $strNombre, 
                                                                  $rol, 
                                                                  $intLimit, 
                                                                  $intPage, 
                                                                  $intStart, 
                                                                  $intIdEsPadre);
        $arrayResultInfoPunto   = $arrayInfoPunto['registros'];
        $intTotalRegistros      = $arrayInfoPunto['total'];
        $intCounter = 1;

        foreach($arrayResultInfoPunto as $arrayInfoPunto):
            $strEsPadre                 = 'No';
            $strEsElectronica           = 'No';
            $strGastoAdministrativo     = 'No';
            $strCiudad                  = '';
            $strParroquia               = '';
            $strSector                  = '';
            $intCiudadId                = '';
            $intParroquiaId             = '';
            $intSectorId                = '';
            $strDatosEnvio              = '';
            $strDireccionEnvio          = '';
            $strTelefonoEnvio           = '';
            $strEmailEnvio              = '';
            $strNombreEnvio             = '';
            $EntityInfoPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayInfoPunto['id']);
            if($EntityInfoPuntoDatoAdicional)
            {
                if($EntityInfoPuntoDatoAdicional->getEsPadreFacturacion())
                {
                    $strEsPadre = $EntityInfoPuntoDatoAdicional->getEsPadreFacturacion();
                }
                if($strEsPadre == 'S')
                {
                    $strEsPadre = 'Si';
                }
                elseif($strEsPadre == 'N')
                {
                    $strEsPadre = 'No';
                }
                if($EntityInfoPuntoDatoAdicional->getGastoAdministrativo() == 'S'){
                    $strGastoAdministrativo = 'Si';
                }
                if($EntityInfoPuntoDatoAdicional->getSectorId() != null)
                {
                    $entitySector       = $EntityInfoPuntoDatoAdicional->getSectorId();
                    $entityParroquia    = $entitySector->getParroquiaId();
                    $entityCanton       = $entityParroquia->getCantonId();
                    $strCiudad          = $entityCanton->getNombreCanton();
                    $intCiudadId        = $entityCanton->getId();
                    $strParroquia       = $entityParroquia->getNombreParroquia();
                    $intParroquiaId     = $entityParroquia->getId();
                    $strSector          = $entitySector->getNombreSector();
                    $intSectorId        = $entitySector->getId();
                }
                $strDatosEnvio          = $EntityInfoPuntoDatoAdicional->getDatosEnvio();
                if($strDatosEnvio == 'S')
                {
                    $strDatosEnvio = 'Si';
                }
                elseif($strDatosEnvio == 'N')
                {
                    $strDatosEnvio = 'No';
                }
                $strDireccionEnvio  = $EntityInfoPuntoDatoAdicional->getDireccionEnvio();
                $strTelefonoEnvio   = $EntityInfoPuntoDatoAdicional->getTelefonoEnvio();
                $strEmailEnvio      = $EntityInfoPuntoDatoAdicional->getEmailEnvio();
                $strNombreEnvio     = $EntityInfoPuntoDatoAdicional->getNombreEnvio();
                $strEsElectronica   = $EntityInfoPuntoDatoAdicional->getEsElectronica();
                if($strEsElectronica == 'S')
                {
                    $strEsElectronica = 'Si';
                }
                elseif($strEsElectronica == 'N')
                {
                    $strEsElectronica = 'No';
                }
            }
            $strLinkVer         = $this->generateUrl('infopunto_show', array('id' => $arrayInfoPunto['id'], 'rol' => $rol));
            $strLinkEliminar    = '#';
            $strLinkEditar      = "#";
            if($arrayInfoPunto['razonSocial'])
            {
                $strNombreCompletoCliente = $arrayInfoPunto['razonSocial'];
            }
            else
            {
                $strNombreCompletoCliente = $arrayInfoPunto['nombres'] . ' ' . $arrayInfoPunto['apellidos'];
            }
            //se agrega validacion para permitir anulacion de punto
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto       = $this->get('comercial.InfoPunto');
            $strPermiteAnularPunto  = $serviceInfoPunto->permiteAnularPtoCliente($arrayInfoPunto['id']);
            if($arrayInfoPunto['estado'] != 'Anulado')
            {
                $arrayResultado[] = array(
                    'idPto'                 => $arrayInfoPunto['id'],
                    'cliente'               => $strNombreCompletoCliente,
                    'login'                 => $arrayInfoPunto['login'],
                    'descripcionPunto'      => $arrayInfoPunto['descripcionPunto'],
                    'direccion'             => $arrayInfoPunto['direccion'],
                    'estado'                => $arrayInfoPunto['estado'],
                    'esPadre'               => $strEsPadre,
                    'datosEnvio'            => $strDatosEnvio,
                    'nombreEnvio'           => $strNombreEnvio,
                    'ciudadEnvio'           => $strCiudad,
                    'parroquiaEnvio'        => $strParroquia,
                    'sectorEnvio'           => $strSector,
                    'id_ciudadEnvio'        => $intCiudadId,
                    'id_parroquiaEnvio'     => $intParroquiaId,
                    'id_sectorEnvio'        => $intSectorId,
                    'direccionEnvio'        => $strDireccionEnvio,
                    'telefonoEnvio'         => $strTelefonoEnvio,
                    'emailEnvio'            => $strEmailEnvio,
                    'linkVer'               => $strLinkVer,
                    'linkEditar'            => $strLinkEditar,
                    'linkEliminar'          => $strLinkEliminar,
                    'esElectronica'         => $strEsElectronica,
                    'permiteAnularPunto'    => $strPermiteAnularPunto,
                    'strGastoAdministrativo'=> $strGastoAdministrativo
                );
            }
            $intCounter++;
        endforeach;
        if(!empty($arrayResultado))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'ptos' => $arrayResultado)));
        }
        else
        {
            $arrayResultado[] = array();
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'ptos' => $arrayResultado)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * actualizarGastoAdministrativoAction, permite actualizar en la info_punto_dato_adicional el campo gastoAdministrativo
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 11-12-2014
     * @since 1.0
     * @return Response $objResponse Contiene el mensaje de respuesta luego de ejecutar la accion
     * 
     * @Secure(roles="ROLE_8-1977")
     */
    public function actualizarGastoAdministrativoAction()
    {
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strUsuario         = $objSession->get('user');
        $intIdPunto         = $objRequest->get("idPunto");
        $strGastAdmiValor   = $objRequest->get("strGastoAdministrativo");
        $em                 = $this->getDoctrine()->getManager('telconet');
        try
        {
            $em->getConnection()->beginTransaction();
            $entityInfoPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($intIdPunto);
            if($entityInfoPuntoDatoAdicional){
                $entityInfoPuntoDatoAdicional->setGastoAdministrativo($strGastAdmiValor);
                $entityInfoPuntoDatoAdicional->setUsrUltMod($strUsuario);
                $entityInfoPuntoDatoAdicional->setFeUltMod(new \DateTime('now'));
                $em->persist($entityInfoPuntoDatoAdicional);
                $em->flush();
                $em->getConnection()->commit();
                $objResponse->setContent("Se actualizo correctamente");
            }else{
                $objResponse->setContent("No se encontraron registros.");
            }
        }
        catch(\Exception $ex)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $objResponse->setContent("Existio un error al actualizar el registro - " . $ex->getMessage());
        }
        return $objResponse;
    }

    /**
    * Documentación para el método 'activaInactivaFacturacionElectronicaAction'.
    *
    * Activa e Inabilita la bandera de facturacion electronica de un punto.
    * @param idPunto (ajax), valor (ajax)
    * @return response.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 23-09-2014
    */
    public function activaInactivaFacturacionElectronicaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request = $this->getRequest();
        $idPunto = $request->get("idPunto");
        $valor = $request->get("valor");
        $em = $this->getDoctrine()->getManager('telconet');
        $em->getConnection()->beginTransaction();
        try
        {
            $datosAdicionales = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($idPunto);
            $datosAdicionales->setEsElectronica($valor);
            $em->persist($datosAdicionales);
            $em->flush();

            $em->getConnection()->commit();
            $respuesta->setContent("Se edito registro con exito.");
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            error_log($e->getMessage());
            $respuesta->setContent("Se presentaron problemas al tratar de editar el registro. Notificar a Sistemas.");
        }


        return $respuesta;
    }

    /**
    * Documentación para el método 'ajaxAnulaPuntoAction'.
    *
    * Realiza la anulación de un punto.
    * @param idPunto (ajax)
    * @return response.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 01-10-2014
    *
    * @author Daniel Guzmán <ddguzman@telconet.ec>
    * @version 1.1 16-02-2023 Se agrega una validación para que cuando el prefijo de la empresa sea ‘MD’
    *                         y el punto a ser anulado se haya creado por el flujo ‘Proceso Traslado’ 
    *                         se cambien el estado del proceso del flujo  a “Anulado”.
    * 
    * @Secure(roles="ROLE_13-1779")
    */
    public function ajaxAnulaPuntoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $idPunto = $request->get("idPunto");
        $strPrefijoEmpresa   = $session->get('prefijoEmpresa');
        $strTipoProceso      = $request->get("strTipo") ? $request->get("strTipo") : '';
        $strMensajeRespuesta = "Se anulo el punto con exito.";
        $emGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $em = $this->getDoctrine()->getManager('telconet');
        $em->getConnection()->beginTransaction();
        try
        {
            if($strPrefijoEmpresa == 'MD')
            {
                $objInfoPuntoCaracEstado  = null;
                $objInfoPuntoTipoProceso  = null;

                $objCaracEstadoProceso = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                        ->findOneBy(
                                                            array(
                                                                "descripcionCaracteristica" => 'ESTADO_PROCESO_PUNTO',
                                                                "estado"                    => 'Activo'
                                                            )
                                                        );

                $objCaracTipoProceso = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
                                                        ->findOneBy(
                                                            array(
                                                                "descripcionCaracteristica" => 'ES_PROCESO_CONTINUO',
                                                                "estado"                    => 'Activo'
                                                            )
                                                        );

                if (!empty($objCaracEstadoProceso) && is_object($objCaracEstadoProceso) &&
                    !empty($objCaracTipoProceso) && is_object($objCaracTipoProceso))
                {
                    $objInfoPuntoCaracEstado  = $em->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                        ->findOneBy(
                                                            array(
                                                                'puntoId'          => $idPunto,
                                                                'caracteristicaId' => $objCaracEstadoProceso->getId()
                                                            )
                                                        );

                    $objInfoPuntoTipoProceso = $em->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                        ->findOneBy(
                                                            array(
                                                                'puntoId'          => $idPunto,
                                                                'caracteristicaId' => $objCaracTipoProceso->getId()
                                                            )
                                                        );
                }

                if(!empty($objInfoPuntoCaracEstado) && is_object($objInfoPuntoCaracEstado) &&
                    !empty($objInfoPuntoTipoProceso) && is_object($objInfoPuntoTipoProceso) &&
                    $objInfoPuntoTipoProceso->getValor() == 'S')
                {
                    $objInfoPuntoCaracEstado->setValor('Anulado');
                    $objInfoPuntoCaracEstado->setFeUltMod(new \DateTime('now'));
                    $objInfoPuntoCaracEstado->setUsrUltMod($session->get('user'));
                    $em->persist($objInfoPuntoCaracEstado);

                    $strMensajeRespuesta = "Se anulo el Proceso Traslado exitosamente. 
                    <br> <b>Por favor espere mientras se le redirige al punto original!</b>";
                }
            }
            $entityPunto = $em->getRepository('schemaBundle:InfoPunto')->find($idPunto);
            $entityPunto->setEstado('Anulado');
            $entityPunto->setAccion('Anulación de Punto');
            $entityPunto->setFeUltMod(new \DateTime('now'));
            $entityPunto->setIpUltMod($request->getClientIp());
            $entityPunto->setUsrUltMod($session->get('user'));
            $em->persist($entityPunto);
            $em->flush();

            $em->getConnection()->commit();
            $respuesta->setContent($strMensajeRespuesta);
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            error_log($e->getMessage());
            $respuesta->setContent("Se presentaron problemas al tratar de anular el punto. Notificar a Sistemas.");
        }


        return $respuesta;
    }
    /**
     * ajaxGetPuntosPersonaEmpresaRolAction
     *
     * Metodo para obtener los puntos clientes(Logines) por Cliente (Persona_Empresa_Rol)
     * Se agregan criterios de Busqueda en el Grid.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 12-02-2016
     * Se realiza funcion que reciba arreglo de parametros con los criterios de busqueda, se agregan funciones en Repositorio 
     * para formar el Json y la Consulta.
     * @param integer $idper
     * @param string  $rol    
     * @param string  $strEstadoPunto
     * @param string  $strDireccion
     * @param string  $strFechaDesde
     * @param string  $strFechaHasta
     * @param string  $strLogin     
     * @param string  $strNombrePunto  
     * @param string  $strCodEmpresa   
     * @param string  $strEsPadre      
     * @param integer $limit     
     * @param integer $start   
     * @param string  $serviceInfoPunto
     * @return JSON
     */
    public function ajaxGetPuntosPersonaEmpresaRolAction($idper, $rol)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request          = $this->getRequest();
        $session          = $request->getSession();        
        $strEstadoPunto   = $request->get("estado_punto");
        $strDireccion     = $request->get("txtDireccion");        
        $arrayFechaDesde  = explode('T', $request->get("txtFechaDesde"));
        $arrayFechaHasta  = explode('T', $request->get("txtFechaHasta"));
        $strLogin         = $request->get("txtLogin");
        $strNombrePunto   = $request->get("txtNombrePunto");        
        $strCodEmpresa    = $request->getSession()->get('idEmpresa');
        $strEsPadre       = $request->get("idespadre");
        $intLimit         = $request->get("limit");        
        $intStart         = $request->get("start");
        $serviceInfoPunto = $this->get('comercial.InfoPunto');         
        
        $arrayParametros = array('idper'            => $idper,
                                 'rol'              => $rol,
                                 'strEstadoPunto'   => $strEstadoPunto,
                                 'strDireccion'     => $strDireccion,
                                 'strFechaDesde'    => $arrayFechaDesde[0],
                                 'strFechaHasta'    => $arrayFechaHasta[0],
                                 'strLogin'         => $strLogin,
                                 'strNombrePunto'   => $strNombrePunto,                
                                 'strCodEmpresa'    => $strCodEmpresa,
                                 'strEsPadre'       => $strEsPadre,
                                 'intStart'         => $intStart,                                
                                 'intLimit'         => $intLimit,
                                 'serviceInfoPunto' => $serviceInfoPunto                
            );  
                
        $em            = $this->get('doctrine')->getManager('telconet');        
        $objJsonPuntos = $em->getRepository('schemaBundle:InfoPunto')->getJsonFindPtosPorPersonaEmpresaRol($arrayParametros);        
        $respuesta->setContent($objJsonPuntos);        
        return $respuesta;                
    }
    
    public function ajaxGetPuntosPadreAction($idCli) {
        $request = $this->getRequest();
        $session  = $request->getSession();         
        $idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
            $datos= $em->getRepository('schemaBundle:InfoPunto')->findPtosPadrePorEmpresaPorCliente($idEmpresa,$idCli);

        foreach ($datos as $datos):
            $arreglo[] = array(
                'idpadre' => $datos['id'],
                'login' => $datos['login'],
                'descripcionPunto' => $datos['descripcionPunto']
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('padres' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('padres' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_9-3758")
     * 
     * Documentación para el método 'getAjaxComboEjecutivosCobranzasAction'.
     *
     * Retorna listado de los ejecutivos de cobranza para llenar el combobox
     *
     * @return Response Lista de Ejecutivos de Cobranza
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function getAjaxComboEjecutivosCobranzasAction()
    {
        $objRequest = $this->getRequest();
        
        $arrayParametros['EMPRESA']      = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['EJECUTIVO']    = $objRequest->get('query');
        $arrayParametros['DEPARTAMENTO'] = 'COBRANZAS';
        $arrayParametros['ESTADOS']      = array('Activo', 'Modificado');
        $arrayParametros['ROLES']        = 'RECAUDADOR|COBRANZA|ASISTENTE|JEFE DEPARTAMENTAL'; // Roles que debe tener el personal de TN
        
        $strJsonEjecutivosCobranza = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPunto')
                                                                                   ->getJsonEjecutivosCobranza($arrayParametros);
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonEjecutivosCobranza);
        
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_151-3759")
     * 
     * Documentación para el método 'getAjaxPuntosClienteAction'.
     *
     * Retorna listado de Todos los Puntos del Cliente por empresa
     *
     * @return Response Lista de Puntos del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function getAjaxPuntosClienteAction($idCliente)
    {
        $objRequest = $this->getRequest();
        $objSesion  = $objRequest->getSession();
        
        $arrayParametros['EMPRESA']    = $objSesion->get('idEmpresa');
        $arrayParametros['PERSONA']    = $idCliente;
        $arrayParametros['LOGIN']      = trim($objRequest->get('login'));
        $arrayParametros['ESTADOS']    = explode(',', $objRequest->get('estado'));
        $arrayParametros['ASIGNADO']   = $objRequest->get('asignado');
        $arrayParametros['LIMIT']      = $objRequest->get('limit');
        $arrayParametros['START']      = $objRequest->get('start');
        
        $strJsonPuntosCliente = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPunto')
                                                                              ->getJsonPuntosCliente($arrayParametros);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonPuntosCliente);
        
        return $objRespuesta;
    }
    
    /**
     * 
     * @Secure(roles="ROLE_151-6437")
     * 
     * Documentación para el método 'getPuntosTelcoHomeClienteAction'.
     *
     * Retorna listado de Todos los Puntos TelcoHome del Cliente
     *
     * @return Response Lista de Puntos del cliente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-03-2019
     * 
     * @param integer $intIdPersonaCliente id de la persona
     * @return $objJsonResponse
     * 
     */
    public function getPuntosTelcoHomeClienteAction($intIdPersonaCliente)
    {
        $objRequest = $this->getRequest();
        $objSesion  = $objRequest->getSession();
        
        $arrayParametros                = array(    'strCodEmpresa'         => $objSesion->get('idEmpresa'),
                                                    'strLogin'              => trim($objRequest->get('login')),
                                                    'strLoginFact'          => trim($objRequest->get('loginFact')),
                                                    'intIdPersona'          => $intIdPersonaCliente,
                                                    'strDescripcionRol'     => 'Cliente',
                                                    'strNombreTecnico'      => "TELCOHOME",
                                                    'strEstadoServicio'     => $objRequest->get('estadoServicio'),
                                                    'arrayEstadoServicios'  => array('Activo','In-Corte'),
                                                    'arrayEstadosPunto'     => array('Activo','In-Corte')
                                                );
        
        $arrayRespuestaPuntosCliente    = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPunto')
                                                                                        ->getPuntosClienteByCriterios($arrayParametros);
        $strJsonPuntosCliente           = json_encode($arrayRespuestaPuntosCliente);
        $objJsonResponse                = new JsonResponse();
        $objJsonResponse->setContent($strJsonPuntosCliente);
        return $objJsonResponse;
    }
    
    
    /**
     * 
     * Documentación para el método 'getDetallesParametrosAction'.
     *
     * Lista todos los detalles del parámetro enviado
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     */
    public function getDetallesParametrosAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strNombreParametro = $objRequest->get('nombreParametro') ? $objRequest->get('nombreParametro') : "PROCESOS_MASIVOS_TELCOHOME";
        $emGeneral          = $this->getDoctrine()->getManager("telconet");
        
        $arrayResultadosDetalles = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get( $strNombreParametro, 
                                                                                                    "", 
                                                                                                    "", 
                                                                                                    "", 
                                                                                                    "", 
                                                                                                    "",
                                                                                                    "", 
                                                                                                    "",
                                                                                                    "",
                                                                                                    $objSesion->get('idEmpresa'));
        $objResponse->setData($arrayResultadosDetalles);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_9-3637")
     * 
     * Documentación para el método 'ajaxAsignarEjecutivoAction'.
     *
     * Método que asigna el Ejecutivo de Cobranzas (UsrCobranzas) a los puntos especificados
     *
     * @return Response Mensaje resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function ajaxAsignarEjecutivoCobranzasAction()
    {
        $objResponse  = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        
        $objEntityManager = $this->get('doctrine')->getManager('telconet');
        
        try
        {
            $objRequest    = $this->get('request');
            $objRepository = $objEntityManager->getRepository('schemaBundle:InfoPunto');

            $strEjecutivoCobranzas = $objRequest->get('ejecutivo');
            $arrayPuntosCliente    = explode(',', $objRequest->get('puntos'));//  Se convierte a Array la cadena serializada

            $objEntityManager->getConnection()->beginTransaction();

            // Recorremos el listado de puntos a asignar el ejecutivo de cobranzas
            foreach($arrayPuntosCliente as $intIdPunto)
            {
                $entityPunto = $objRepository->find($intIdPunto);// Encontramos la entidad InfoPunto
                
                if(!$entityPunto)
                {
                    throw new \Exception("Entity not found $intIdPunto");
                }
                
                $entityPunto->setUsrCobranzas($strEjecutivoCobranzas);// Se establece el Ejecutivo de cobranzas del punto del cliente.
                $entityPunto->setFeUltMod(new \DateTime('now'));
                $entityPunto->setUsrUltMod($objRequest->getSession()->get('user'));
                $entityPunto->setIpUltMod($objRequest->getClientIp());

                $objEntityManager->persist($entityPunto);
            }

            $objEntityManager->flush();
            $objEntityManager->getConnection()->commit();
            $objResponse->setContent("OK");
        }
        catch(Exception $ex)
        {
            $objEntityManager->getConnection()->rollback();
            $objEntityManager->getConnection()->close();
            
            $objResponse->setContent($ex->getMessage());
        }
        
        return $objResponse;
    }   
    
    public function ajaxValidaLoginAction()
    {
        $request = $this->getRequest();
        $login = trim($request->request->get("login"));
        
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $response = $serviceInfoPunto->validarLogin($login);
        
        return new Response($response);
    }
	
    public function serviciosAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity= $em->getRepository('schemaBundle:InfoPunto')->find($id);
        return $this->render('comercialBundle:infopunto:servicios.html.twig', 
                array('entity'=>$entity));
    }

    /**
     * Documentación para el método 'serviciosGridAction'.
     *
     * Método utilizado para obtener los servicios de un punto en formato json
     *
     * @return Json response
     *
     * @throws \Exception
     *
     * @version 1.0
     *
     * @author  Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.1 15-12-2015 obtención de información para servicios de transmisión de datos
     *
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 15-04-2016 Se aumenta tiempo de ejecución de consulta por servicio a nivel de controlador.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.3 29-04-2016
     * Se Agrega el campo Tipo Enlace en el listado de servicios.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.4 27-06-2016
     * Se corrige el cálculo del precio total; se considera el valor del descuento para determinar el precio total.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 29-06-2016 - Se agrega al grid la fecha de renovación del plan
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.6 30-06-2016
     * Se da formato de moneda a la presentación de los precios.
     * Se calcula el precio total basado en P.V.P., Descuento(valor/porcentaje) y Cantidad.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.7 07-07-2016
     * Se obtienen los datos de Ancho de banda del producto y la relación con su concentrador.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.8 12-07-2016
     * Se corrige el cálculo del precio total en función de la cantidad, precio unitario y descuento.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.9 21-07-2016
     * Se obtiene el id del servicio backup siempre que disponga de él.
     * Se envía el campo del servicio: MesesRestantes.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 2.0 05-08-2016
     * TN: Se modifica la verificación de que el producto tenga o no anexo técnico, para MD siempre será 'NO'.
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 2.1 01-08-2016
     * Se obtiene el Vendedor asignado al Servicio.
     *
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 2.2 31-08-2016
     * Se agrega el envío del parámetro $arrayParametros['TODOS'] = true que indica que no se filtre por el estado del rol del vendedor
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 2.3 27-09-2016
     * Se obtiene campo Clasificacion del producto que define si es Datos, Internet, Etc.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.4 15-02-2017 - Se añade validación cuando se pregunta por el tipo de venta. Si el servicio está marcado en el campo 'ES_VENTA' como
     *                           'E' se mostrará en el grid de servicios como 'VENTA EXTERNA'. Adicional se valida si el servicio no requiere
     *                           factibilidad cuando está asociado a un producto y se encuentra en estado 'Pre-servicio' o 'Rechazado', para ello se
     *                           verifica si el producto tiene asociado la característica 'NO_REQUIERE_FACTIBILIDAD'
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.5 09-05-2017 - Se muestra informacion den la descripcion del producto si un servicio tiene la relacion con una Tercerizadora dada
     *                           su ultima milla escogida ( TERCERIZADA )
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.6 18-05-2017 Se agrega variable para obtener varlor del campo descuento unitario para su visualización en pantalla de servicios.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.7 25-05-2017
     *                          - Se envia variable indicando si un servicio puede o no tener un servicio backup
     *                          - Se reordena el grid para Servicios de TN en orden jerarquico dependiendo del
     *                            Servicio PRINCIPAL seguidos de sus BACKUPS
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.8 30-05-2017 - Se cambia la forma de consultar los servicios agregados al login del cliente en sessión.
     *                           Se incluye manejo de excepciones con try y catch.
     *                           Se verifica si el servicio tiene asociado una plantilla de comisionistas que pueda ser editada. Para ello se usa la
     *                           variable '$boolTieneComisionistas'.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.9 05-06-2017 Se cambia entitymanager em_comercial a emComercial dado que la variable em_comercial fue cambiado previo
     *                         a la subida que involucra el fallo.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.10 21-01-2018 Obtengo el tipo medio del servicio, identificar si el servicio backup comparte el mismo CPE con el principal, se debe
     *                         visualizar el boton de cambio de tipo medio solo al principal, caso contrario se mostrara el boton en ambos servicios,
     *                         en el grid de servicio se visualiza en el campo Orden el label Cambio Tipo Medio en el caso que el tipo orden sea C.
     *                         a la subida que involucra el fallo
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.0 29-08-2017 Se ajusta para que envie variable indicando en servicios que necesitan anexos tecnicos o comerciales
     *                         y estos no existen y de acuerdo al estado del servicio, si los servicios pueden seguir o no el flujo en el sistema
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.1 19-01-2018 Se envia variable que indica si un login de una determinada razon social esta configurado para poder pedir migracion
     *                         de datos de factibilidad de otro login que se encuentre previamente parametrizado
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.2 13-06-2018 Se agrega validación para servicios Backup creados por Cambio Tipo Medio
     * @since 3.1
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.3 13-08-2018 Se modifica para obtener el resumen de los productos de tipo hosting contratados usando las caracteristicas
     *                         modificadas y usando el esquema 1 producto n caracteristicas
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 3.4
     * @since 27-06-2018
     * Se agrega condición por else para mostrar cambioTipoMedio se fija boolMostrarCambioTipoMedio = false
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 3.5
     * @since 04-01-2019
     * Se agregan filtros necesarios para consultar servicios.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 3.6
     * @since 20-03-2019
     * Se agrega elemento en array 'arreglo[]' para que incluya el tipo de esquema y pueda ser devuelto
     * dentro del json que se utiliza para construir el grid de Servicios.
     *
     * @author Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 3.7
     * @since 13-03-2019
     * Se quita el  filtro para el estado eliminar, al consultar servicios.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 3.8 01-07-2019 Se modifica no asignar enlace Backup a los productos Internet Sdwan.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 3.9 08-07-2019 - Se Agregan los campos Grupo y Linea de Negocio al Grid de Servicios.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 4.0 05-08-2019 - Se Agrega la validación que los productos Sdwan no consten con la opción Backup.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 4.1 16-08-2019 - Se agrega campo 'nombreProducto' a la respuesta del metodo.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.2 30-09-2019 - Se agrega el parametro: 'serviceTecnico' a la funcion generarJsonPreFactibilidad, para consultar el tipo de red
     *                           por servicio
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 4.3 2020-01-10 Se agrega el envío de objEmSoporte necesario para la función generarJsonCoordinar.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.3 2020-02-26 Se agrega la variable de activación simultánea ya que si el servicio tradicional esta en
     *                         estado Activo debe aparecer el boton de solicitar factibilidad cuando la cetegoria sea FIJA ANALOGA O TRUNK
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.4 2020-07-01 - Si el producto del servicio es Housing, obtenemos el
     *                           espacio contratado para mostrarlo en el grid de servicios.
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 4.5 26-09-2019 - Se Agrega la validación para el reingreso de la orden de servicio automática y se
     *                           obtiene información adicional en caso que se requiera cambiar la cobertura del punto.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 4.6 18-02-2020 - Se modifican y se agregan validaciones para el reingreso de OS automática, como: validar Pre-planificación Previa,
     *                           se parametriza y modifica validación de Motivo de Rechazo/Anulación, se corrige validación de 30 días de Reingreso
     *                           y se crea validación de Documento de Devolución sobre facturas de instalación.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 4.7 27-01-2021 - Se agrega a la validacion getEstadosServiciosValidos que considere el plan con servicio de internet 
     *                           Se valida el estado del punto parametrizado en "PARAMETROS_REINGRESO_OS_AUTOMATICA" por "ESTADO_PUNTO", 
     *                           validación previa para la presentación del botón de Reingreso de OS.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 4.8 01-04-2021 - Se agrega información de promociones para que sean utilizados en la nueva pestaña de promociones para los contratos
     *                           C, AP y AS.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 4.9 21-07-2021 - Se agrega validación previa para la presentación del botón de Reingreso de OS y funcion getValidaServicioInternet
     *                           que verifica que el servicio posea plan con producto de internet dedicado en base al codigo "INTD" parametrizado,
     *                           parametro: "PARAMETROS_REINGRESO_OS_AUTOMATICA"detalle "CODIGO_PRODUCTO"     
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 5.0 10-06-2021 - En la pantalla de edición de Datos de OS para Reingreso Automatico de Orden de Servicio vía parámetro se agrega
     *                           validación para que se permita la edición de los Datos Geograficos.
     *                           Se habilita la actualización de Jurisdiccion, canton, parroquia, sector si el parametro esta en "S"
     *                           Parametro det: "PERMITE_EDITAR_DATOS_GEOGRAFICOS".
     *                           Se quita la restricción para la presentación del nombre de quien realizo el reingreso
     *                           Se habilita Flujo de Reingreso de ordenes de servicio para servicios con tipo de orden T: Traslado
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 5.1 14/11/2021  Se consulta el metraje filtrando los siguiente:
     *                          se busca el tipo de solicitud -SOLICITUD FACTIBILIDAD- para con ello filtrar la InfoDetalleSolicitud
     *                           y como final la InfoDetalleSolCaract
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 5.2 20-02-2022 - Se agrega el tipo de orden del servicio como retorno de la respuesta de la función
     * 
     * @author Josué Valencia <ajvalencia@telconet.ec>
     * @version 5.3 29-12-2022 - Se agrega validacion a productos  CLEAR CHANNEL PUNTO A PUNTO
     *                           para que soporten la creación de BACKUP
     * 
     * * @author Jorge Gómez <jigomez@telconet.ec>
     * @version 5.3 13-03-2023 - Se agrega obtencion del id_Peronsa_Empresa_Rol y se agrega como retorno de la respuesta de la función
     */
    public function serviciosGridAction($id)
    {
        ini_set('max_execution_time', 400000);
        $serviceUtilidades        =   $this->get('administracion.Utilidades');
        $serviceUtil              =   $this->get('schema.Util');
        $objRequest               =   $this->getRequest();
        $intLimit                 =   $objRequest->get("limit");
        $intStart                 =   $objRequest->get("start");
        $objSession               =   $objRequest->getSession();
        $strCodEmpresa            =   $objSession->get('idEmpresa');
        $strPrefijoEmpresa        =   $objSession->get('prefijoEmpresa');
        $strUsuarioCreacion       =   $objSession->get('user');
        $strIpCreacion            =   $objRequest->getClientIp();
        $strOpcion                =   $objRequest->get('strOpcion') ? $objRequest->get('strOpcion') : 'PUNTO';
        $strLoginVendedor         =   $objRequest->get('strLoginVendedor') ? $objRequest->get('strLoginVendedor') : '';
        $emComercial              =   $this->get('doctrine')->getManager('telconet');
        $emInfraestructura        =   $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral                =   $this->get('doctrine')->getManager('telconet_general');
        $emComunicacion           =   $this->get('doctrine')->getManager('telconet_comunicacion');
        $objEmSoporte             =   $this->get('doctrine')->getManager('telconet_soporte');
        $arrayFechaDesde          =   explode('T', $objRequest->get('fechaDesde'));
        $arrayFechaHasta          =   explode('T', $objRequest->get('fechaHasta'));
        $strFechaDesde            =   $arrayFechaDesde[0];
        $strFechaHasta            =   $arrayFechaHasta[0];
        $strEstado                =   $objRequest->get('estado');
        $strPlan                  =   $objRequest->get('plan');
        $strProducto              =   $objRequest->get('producto');
        $strFlagActivacion        =   $objRequest->get('strFlagActivacion');
        $objPlanificarService     =   $this->get('planificacion.planificar');
        
        $objServicioRepository    =   $emComercial->getRepository('schemaBundle:InfoServicio');
        $objInfoServicioService   =   $this->get('comercial.InfoServicio');
        $objCarIns                =   "";
        $objInfoServCaracIns      =   "";
        $strCodigoIns             =   "";
        $objCarMens               =   "";
        $objInfoServCaracMens     =   "";
        $strCodigoMens            =   "";
        $objCarBw                 =   "";
        $objInfoServCaracBw       =   "";
        $strCodigoBw              =   "";
        $serviceTecnico           =   $this->get('tecnico.InfoServicioTecnico');

        $arrayParametrosGrid   = array('EMPRESA'          => $strCodEmpresa, 
                                       'PUNTO'            => $id, 
                                       'LIMIT'            => $intLimit,
                                       'COUNT'            => true,
                                       'START'            => $intStart,
                                       'strLoginVendedor' => $strLoginVendedor,
                                       'strEstado'        => $strEstado,
                                       'strPlan'          => $strPlan,
                                       'strProducto'      => $strProducto,
                                       'strFechaDesde'    => $strFechaDesde,
                                       'strFechaHasta'    => $strFechaHasta
            );

        $strTipoMedio               = "";
        $boolMostrarCambioTipoMedio = false;
        $intIdElementoPrincipal     = "";
        $intIdElementoBackup        = "";
        
        $arrayParametrosEstadosNoIncluidos = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                    'strValorRetornar'  => 'descripcion',
                                                    'strNombreProceso'  => 'PUNTO',
                                                    'strNombreModulo'   => 'COMERCIAL',
                                                    'strNombreCabecera' => 'ESTADOS_GRID_SERVICIOS',
                                                    'strValor1Detalle'  => $strOpcion,
                                                    'strUsrCreacion'    => $strUsuarioCreacion,
                                                    'strIpCreacion'     => $strIpCreacion);
        
        $arrayResultadosEstadosNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosEstadosNoIncluidos);

        if( isset($arrayResultadosEstadosNoIncluidos['resultado']) && !empty($arrayResultadosEstadosNoIncluidos['resultado']) )
        {
            foreach($arrayResultadosEstadosNoIncluidos['resultado'] as $strEstadoNoIncluido)
            {
                $strEstadoNoIncluido = 'all';
                $arrayParametrosGrid['ESTADOS'][] = $strEstadoNoIncluido;
            }//foreach($arrayResultadosEstadosNoIncluidos['resultado'] as $strCargo)
        }//( isset($arrayResultadosEstadosNoIncluidos['resultado']) && !empty($arrayResultadosEstadosNoIncluidos['resultado']) )

        try
        {    
            //Obtiene la cantidad de servicios agregados al punto en session
            $intTotal = $objServicioRepository->getResultadoServiciosPorEmpresaPorPunto($arrayParametrosGrid);

            if( $intTotal > 0 )
            {
                //Obtiene los servicios agregados al punto en session
                $arrayParametrosGrid['COUNT'] = false;
                $arrayDatos                   = $objServicioRepository->getResultadoServiciosPorEmpresaPorPunto($arrayParametrosGrid);
            }//( $intTotal > 0 )
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoPuntoController.serviciosGridAction', 
                                       'Error al consultar los servicios para ser mostrados en el grid. - '.$e->getMessage(), 
                                       $strUsuarioCreacion, 
                                       $strIpCreacion );
        }//try

        $request               = $this->getRequest();
        $limit                 = $request->get("limit");
        $page                  = $request->get("page");
        $start                 = $request->get("start");
        $session               = $request->getSession();
        $idEmpresa             = $session->get('idEmpresa');
        $strPrefijoEmpresa     = $session->get('prefijoEmpresa');
        $em                    = $this->get('doctrine')->getManager('telconet');
        $em_infra              = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objServicioRepository = $em->getRepository('schemaBundle:InfoServicio');
        
        if($strPrefijoEmpresa == 'TN')
        {
            $arrayParametrosServicios                 = array();
            $arrayParametrosServicios['intStart']     = $start;
            $arrayParametrosServicios['intLimit']     = $limit;
            $arrayParametrosServicios['intIdPunto']   = $id;
            $arrayParametrosServicios['intIdEmpresa'] = $idEmpresa;
            $arrayParametrosServicios['strEstado']    = $strEstado;
            $arrayParametrosServicios['strPlan']      = $strPlan;
            $arrayParametrosServicios['strProducto']  = $strProducto;
            $arrayParametrosServicios['strFechaDesde']= $strFechaDesde;
            $arrayParametrosServicios['strFechaHasta']= $strFechaHasta;
           
            $arrayResultado = $objServicioRepository->getArrayServiciosPorPunto($arrayParametrosServicios);
            $strClienteem= $em->getRepository('schemaBundle:InfoServicio')->ClienteEm($id);
        }
        else
        {
            $arrayResultado = $objServicioRepository->findServiciosPorEmpresaPorPunto($idEmpresa, $id, $limit, $page, $start);
        }

        $datos = $arrayResultado['registros'];
        $total = $arrayResultado['total'];

        $ciudad="";
        $login2 = "";
        $tercializadora = "";
        $cliente = "";
        $direccion = "";
        $nombreSector = "";
        $esRecontratacion = "";
        $producto = "";
        $tipo_orden = "";
        $telefonos = "";
        $observacion = "";
        $i = 1;
        
        $clienteSesion = $objSession->get('cliente');
        $arrayEstados  = array("Planificada", "Replanificada", "Detenido", "Rechazada", "AsignadoTarea"); 
        
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $servicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');
        
        foreach($arrayDatos as $dato):
            $strFlagActiSimul   = 'N';
            $strFlagActivacion  = 'N';
            if($i % 2 == 0)
                $clase = 'k-alt';
            else
                $clase = '';
            $urlVer = "";
            $urlEditar          = $this->generateUrl('cliente_edit', array('id' => $dato->getId()));
            if($clienteSesion)
            {
                //se agrega validacion de rol del cliente para crear link correcto
                $tipoRol = $clienteSesion['nombre_tipo_rol'];
                if ($tipoRol == 'Cliente')
                {
                    $urlVerCliente = $this->generateUrl('cliente_show', array('id' => $clienteSesion['id'],
                                                                          'idper'=>$clienteSesion['id_persona_empresa_rol']));
                }
                else
                {
                    $urlVerCliente = $this->generateUrl('precliente_show', array('id' => $clienteSesion['id'],
                                                                          'idper'=>$clienteSesion['id_persona_empresa_rol']));
                }
            }   
            else
            {
                $urlVerCliente = "";
            }
            $boolMostrarCambioTipoMedio= false;
            $urlEliminar               = $this->generateUrl('infopunto_delete_servicio_ajax', array('id' => $dato->getId()));
            $strNoRequiereFactibilidad = "N";
            
            $linkVer = $urlVer;
            if($dato->getEstado() != "Convertido")
                $linkEditar = $urlEditar;
            else
                $linkEditar = "#";
            $linkEliminar = $urlEliminar;
            $tipoOrden = '';
            $idProducto = '';
            $descripcionProducto = '';
            $nombreTecnicoProducto = '';
            $esConcentrador = "";
            $objProducto = $dato->getProductoId();
            
            //Se saca campo clasificacion para definir si un producto es Datos, Internet, etc
            $strClasificacion = '';
            
            if($objProducto)
            {
                $tipo                  = 'producto';
                $idProducto            = $objProducto->getId();
                $nombreTecnicoProducto = $objProducto->getNombreTecnico();
                $esConcentrador        = $objProducto->getEsConcentrador();
                $strClasificacion      = $objProducto->getClasificacion();
                
                if($nombreTecnicoProducto == 'NETHOME')
                {
                    $strNombreProducto      = $objProducto->getDescripcionProducto();
                    $arrayParametrosNetHome = array("intIdServicio" => $dato->getId(),
                                                    "strProceso"    => "show");
                    $strHtmlNombreProducto = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->getDatosServicioProductoNethome($arrayParametrosNetHome);
                    $strHtmlNombreProducto = str_replace("NOMBREPRODUCTO", $strNombreProducto, $strHtmlNombreProducto);
                    $descripcionProducto   = $strHtmlNombreProducto;
                }
                else
                {
                    if($nombreTecnicoProducto != 'HOSTING')
                    {
                        // Se obtienen las características para el ancho de banda y concentrador.
                        $strDataServProd = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                       ->getResultadoDatosServicioProducto($dato->getId());
                        // Reemplazar tokens definidos en la respuesta del query.
                        $descripcionProducto = str_replace("STYLE_NAME_PROD",
                            "'color: #000!important; float: none!important; font-weight: bold; font-size: 9.5px;'", $strDataServProd);
                        $descripcionProducto = str_replace("STYLE_ARROWS_1",
                            "'color: #000!important; float: none!important; font-weight: bold; font-size: 17.5px;'", $descripcionProducto);
                        $descripcionProducto = str_replace("NAME_PRODUCT", $objProducto->getDescripcionProducto(), $descripcionProducto);
                        $descripcionProducto = str_replace("*", "'", $descripcionProducto);
                    }
                }
                //PARA CLOUD IAAS SE MUESTRA EL DETALLE DE CARACTERISTICAS CON SUS RESPECTIVOS VALORES
                $strCamposGrid = 
                                "<tr>"
                                    . "<td><b>Grupo &nbsp;</b></td>"
                                    . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                    . "<td><b style='color:#FF5733; font-size: 9.5px;'>".$objProducto->getGrupo()." </b></td>".
                                "</tr>"
                                ."<tr>"
                                    . "<td><b>L_Negocio &nbsp;</b></td>"
                                    . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                    . "<td><b style='color:#FF5733; font-size: 9.5px;'>".$objProducto->getLineaNegocio()." </b></td>".
                                "</tr>"
                                ."</table></td></tr>";

                if($nombreTecnicoProducto === 'HOUSING')
                {
                    $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                            ->findOneByServicioId($dato->getId());

                    $strDescripcionRecurso = is_object($objInfoServicioRecursoCab) ?
                            $objInfoServicioRecursoCab->getDescripcionRecurso() : null;

                    if ($strDescripcionRecurso !== null)
                    {
                        $strDescripcion       = $objProducto->getDescripcionProducto();
                        $descripcionProducto  = "<table><tr height='14px'><td valign='top'>".$strDescripcion."</td></tr>";
                        $descripcionProducto .= "<tr><td><table>".
                                                "<tr>"
                                                    ."<td><b>TIPO&nbsp;</b></td>"
                                                    ."<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                                    ."<td><b style='color:#46A0E2;font-size:9.5px;'>".$strDescripcionRecurso."</b></td>".
                                                "</tr>";
                        $descripcionProducto .= $strCamposGrid;
                        $descripcionProducto .= '</table>';
                    }
                }

                if($nombreTecnicoProducto == 'HOSTING')
                {
                    $strDescripcion      = $objProducto->getDescripcionProducto();
                    $descripcionProducto = "<table><tr height='14px'><td valign='top'>".$strDescripcion."</td></tr>";
                    
                    $boolEsAlquilerServidores = $servicioTecnicoService->isContieneCaracteristica($objProducto,'ES_ALQUILER_SERVIDORES');
                    $boolEsPoolRecursos       = $servicioTecnicoService->isContieneCaracteristica($objProducto,'ES_POOL_RECURSOS');
                    
                    if($boolEsAlquilerServidores)
                    {
                        $arrayParametrosRecursos                   = array();
                        $arrayParametrosRecursos['intIdServicio']  = $dato->getId();
                        $arrayParametrosRecursos['strTipoRecurso'] = 'TIPO ALQUILER SERVIDOR';
                        
                        $arrayServidores = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                       ->getArrayCaracteristicasPorTipoYServicio($arrayParametrosRecursos);
                        
                        if(!empty($arrayServidores))
                        {
                            $descripcionProducto .= "<tr><td><table><tr><td><b>SERVIDORES&nbsp;</b></td>"
                                                 . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"
                                                 . "</td><td><table>";
                            
                            foreach($arrayServidores as $array)
                            {
                                $descripcionProducto .= "<tr><td>"
                                                              . "<b><label style='color:#46A0E2;font-size:9.5px;'>".$array['nombreRecurso']
                                                              . "</b></label>"
                                                      . "</td></tr>";
                            }
                            
                            $descripcionProducto .= "</table></td></tr>";
                            $descripcionProducto .= $strCamposGrid;
                        }
                    }
                    
                    if($boolEsPoolRecursos)
                    {
                        $descripcionProducto .= "<tr><td><table>";
                        
                        //Obtener informacion de Pool consolidado
                        $arrayParametrosRecursos                   = array();
                        $arrayParametrosRecursos['intIdServicio']  = $dato->getId();
                        $arrayParametrosRecursos['strTipoRecurso'] = 'DISCO_VALUE';
                        
                        $arrayRecursos = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                     ->getArrayRecursosPoolPorTipo($arrayParametrosRecursos);
                        
                        if(!empty($arrayRecursos) && isset($arrayRecursos['totalRecurso']))
                        {
                            $descripcionProducto .= 
                                                     "<tr>"
                                                       . "<td><b>DISCO &nbsp;</b></td>"
                                                       . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                                       . "<td><b style='color:#46A0E2;'>".$arrayRecursos['totalRecurso']." (GB)</b></td>".
                                                     "</tr>";
                        }
                        
                        $arrayParametrosRecursos['strTipoRecurso'] = 'PROCESADOR_VALUE';
                        
                        $arrayRecursos = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                     ->getArrayRecursosPoolPorTipo($arrayParametrosRecursos);
                        
                        if(!empty($arrayRecursos) && isset($arrayRecursos['totalRecurso']))
                        {
                            $descripcionProducto .= 
                                                     "<tr>"
                                                       . "<td><b>PROCESADOR &nbsp;</b></td>"
                                                       . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                                       . "<td><b style='color:#46A0E2;'>".$arrayRecursos['totalRecurso']." (GB)</b></td>".
                                                     "</tr>";
                        }
                        
                        $arrayParametrosRecursos['strTipoRecurso'] = 'MEMORIA RAM_VALUE';
                        
                        $arrayRecursos = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                     ->getArrayRecursosPoolPorTipo($arrayParametrosRecursos);
                        
                        if(!empty($arrayRecursos) && isset($arrayRecursos['totalRecurso']))
                        {
                            $descripcionProducto .= 
                                                     "<tr>"
                                                       . "<td><b>MEMORIA &nbsp;</b></td>"
                                                       . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;</td>"
                                                       . "<td><b style='color:#46A0E2;'>".$arrayRecursos['totalRecurso']." (GB)</b></td>".
                                                     "</tr>";
                        }
                        $descripcionProducto .= $strCamposGrid;
                    }
                    if(!$boolEsPoolRecursos && !$boolEsAlquilerServidores)
                    {
                        $descripcionProducto .= "<tr><td><table>";
                        $descripcionProducto .= $strCamposGrid;
                    }
                    $descripcionProducto .= '</table>';
                }
                
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $idProducto, 
                                                         'strDescCaracteristica' => 'NO_REQUIERE_FACTIBILIDAD', 
                                                         'strEstado'             => 'Activo' );
                $strNoRequiereFactibilidad      = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            }
            elseif($dato->getPlanId())
            {
                $tipo = 'plan';
                $idProducto = $dato->getPlanId()->getId();
                $descripcionProducto = $dato->getPlanId()->getNombrePlan();
            }
            
            $boolUrlFactibilidad = false;
            
            if( strtolower($dato->getEstado()) == strtolower("Pre-servicio") || strtolower($dato->getEstado()) == strtolower("Rechazado") )
            {
                /**
                 * Se valida si el producto NO requiere factibilidad, para ello se valida si el valor de la variable '$strNoRequiereFactibilidad' es
                 * 'S' quiere decir que no requiere factibilidad, caso contrario si se requiere factibilidad.
                 */
                if( $strNoRequiereFactibilidad == "N" )
                {
                    $boolUrlFactibilidad = true;
                }
            }
            
            $strLinkFactibilidad = ($boolUrlFactibilidad ? 'si' : 'no');
            
            
            $entityOT = null;
            $numero_ot = null;
            if($dato->getOrdenTrabajoId())
            {
                $entityOT = $emComercial->getRepository('schemaBundle:InfoOrdenTrabajo')->findOneById($dato->getOrdenTrabajoId());
            }
            if($entityOT)
            {
                $tipoOrden = "";
                $numero_ot = $entityOT->getNumeroOrdenTrabajo();
            }

            if($dato->getTipoOrden() == 'N')
                $tipoOrden = 'Nueva';
            else if($dato->getTipoOrden() == 'R')
                $tipoOrden = 'Reubicacion';
            else if($dato->getTipoOrden() == 'T')
                $tipoOrden = 'Traslado';
            else if($dato->getTipoOrden() == 'C')
                $tipoOrden = 'Cambio Tipo Medio';
            else
                $tipoOrden = 'Nueva';

            $ultimaMilla   = 'N/A';
            $strTipoEnlace = '';
            $strFormaEnlace= '';
            
            $servicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($dato->getId());
            
            if($servicioTecnico)
            {
                if($servicioTecnico->getUltimaMillaId())
                {
                    $entityUltimaMilla = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($servicioTecnico->getUltimaMillaId());
                    $ultimaMilla       = $entityUltimaMilla->getCodigoTipoMedio();
                    $strTipoMedio      = $entityUltimaMilla->getNombreTipoMedio();
                }
                $strTipoEnlace  = strtoupper($servicioTecnico->getTipoEnlace());
                $strFormaEnlace = $strTipoEnlace;
                $entityInfoServicioProdCaract = null;
                // Si ya he activado un servicio por cambio de tipo medio inhabilito el botón para realizar uno nuevo.
                $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array('descripcionCaracteristica' => "ID_CAMBIO_TIPO_MEDIO",
                                                                   'estado'                    => "Activo"
                                                                  )
                                                            );
                if(is_object($objCaracteristica))
                {
                    $objProdCaractTipoMedio = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneBy(array(
                                                                            "productoId"       => $idProducto,
                                                                            "caracteristicaId" => $objCaracteristica->getId(),
                                                                            "estado"           => "Activo"
                                                                            ));
                    if(is_object($objProdCaractTipoMedio))
                    {
                        $entityInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findOneBy(array(
                                                                                     'productoCaracterisiticaId' => $objProdCaractTipoMedio->
                                                                                                                    getId(),
                                                                                     'valor'                     => $dato->getId(),
                                                                                     'estado'                    => "Activo"));
                    }
                }
                
                if($strTipoEnlace == 'BACKUP')
                {
                    $arrayInfServicio['intServicioId']                = $dato->getId();
                    $arrayInfServicio['strDescripcionCaracteristica'] = 'ES_BACKUP';
                    //Obtengo el servicio principal
                    $objInfoServicioTecnicio                          = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                    ->getObtenerValorDelServicio($arrayInfServicio);
                    if($objInfoServicioTecnicio['VALOR'])
                    {
                        //Obtengo información del servicio principal.
                        $servicioTecPrincipal = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($objInfoServicioTecnicio['VALOR']);

                        if($servicioTecPrincipal->getElementoClienteId() !== null)
                        {
                            //Verificar que el elemento cliente no sea directamente el CPE
                            $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                             ->find($servicioTecPrincipal->getElementoClienteId());
                            if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                            {
                                $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                                $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $servicioTecPrincipal->getInterfaceElementoClienteId();

                                $arrayDatoCpe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                                if($arrayDatoCpe['msg'] == "FOUND")
                                {
                                    $intIdElementoPrincipal = $arrayDatoCpe['idElemento'];
                                }
                            }
                        }
                    }
                    // Logíca para saber si el servicio backup comparte el mismo CPE.
                    // Si el servicio tiene informacion de cpe ( servicios de internet ) se presentarán los cps relacionados
                    if($servicioTecnico->getElementoClienteId() !== null)
                    {
                        //Verificar que el elemento cliente no sea directamente el CPE
                        $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->find($servicioTecnico->getElementoClienteId());
                        if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                        {
                            $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                            $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $servicioTecnico->getInterfaceElementoClienteId();

                            $arrayDatoCpe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                              ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                            if($arrayDatoCpe['msg'] == "FOUND")
                            {
                                $intIdElementoBackup = $arrayDatoCpe['idElemento'];
                            }
                        }
                    }
                    //Consultar si el elementoId(CPE) del principal y el backup son identicos es por que comparten el mismo CPE
                    if ($intIdElementoPrincipal == $intIdElementoBackup)
                    {
                        $objUltimaMillaFo       = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->findOneBy(array('nombreTipoMedio' => 'Fibra Optica',
                                                                                      'estado'          => 'Activo' ));
                        if (!is_object($objUltimaMillaFo))
                        {
                            throw new \Exception("Problemas al recupera ultima milla Fibra Optica.");
                        }
                            /* si el enlace principal y el backup estan en el mismo equipo, se debe validar que el enlace Principal
                               sea de UM = Fibra para que el cambio de tipo medio solo le aparezca al BackUP */
                            if (is_object($servicioTecPrincipal) && 
                                $servicioTecPrincipal->getUltimaMillaId() != $objUltimaMillaFo->getId() ||
                                ($servicioTecPrincipal                == $objUltimaMillaFo->getId() && 
                                 $servicioTecnico->getUltimaMillaId() != $objUltimaMillaFo->getId() &&
                                 is_object($entityInfoServicioProdCaract)
                                )
                               )
                            {
                                $boolMostrarCambioTipoMedio = true;
                            }
                    }
                    $strTipoEnlace = "<label style='color:green;'>BACKUP</label> "
                                    . "<label style='font-weight: bold; font-size: 17.5px;'><b>&#10548;</b></label>";
                }
                else
                {
                    if(is_object($entityInfoServicioProdCaract))
                    {
                        $boolMostrarCambioTipoMedio = true;
                    }
                }
            }
            
            //Si la ultima milla es TERCERIZADA se muestra la respectiva Tercerizadora relacionada
            if($ultimaMilla == 'TER')
            {
                $objServProdCaractTercerizada = $servicioTecnicoService->getServicioProductoCaracteristica($dato, 
                                                                                                           'TERCERIZADORA',
                                                                                                           $objProducto
                                                                                                          );      
                if(is_object($objServProdCaractTercerizada))
                {
                    $objPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->find(intval($objServProdCaractTercerizada->getValor()));
                    
                    if(is_object($objPersona))
                    {
                        $descripcionProducto   .= "<table><tr><td><label><b>TERCERIZADORA</b></label></td></tr>"
                                               . "<tr><td><label style='font-weight: bold; font-size: 17.5px;'>&#10551;</label>"
                                               . "<label style='color:blue;font-size: 9.5px;'>".$objPersona->getInformacionPersona()."</label></td>"
                                               . "</tr></table>";
                    }
                }
                else
                {
                    $descripcionProducto   .= "<table><tr><td><label><b>TERCERIZADORA</b></label></td></tr>"
                                               . "<tr><td><label style='font-weight: bold; font-size: 17.5px;'>&#10551;</label>"
                                               . "<label style='color:blue;font-size: 9.5px;'>No definida</label></td>"
                                               . "</tr></table>";
                }
            }
            
            $strEsVenta = $dato->getEsVenta();
            
            if($strEsVenta == 'N')
            {
                $strEsVenta = "NO";
            }
            elseif($strEsVenta == 'E')
            {
                $strEsVenta = "VENTA EXTERNA";
            }
            else
            {
                $strEsVenta = "SI";
            }
            
            //Valor $ 0.00 por defecto
            $strDctoUnitario   = "$  " . number_format(0, 2);
            $strValorDescuento = "$  " . number_format(0, 2);
            $fltValorDescuento = 0;
            
            if($dato->getValorDescuento())
            {
                $strValorDescuento = "$  " . number_format($dato->getValorDescuento(), 2);
                $fltValorDescuento = $dato->getValorDescuento();
            }
            elseif($dato->getPorcentajeDescuento())
            {
                $fltValorDescuento = ($dato->getPorcentajeDescuento() / 100) * $dato->getPrecioVenta();
            }
            
            if($dato->getDescuentoUnitario())
            {
                $strDctoUnitario = "$  " . number_format($dato->getDescuentoUnitario(), 2);
            }            
            
            $fltPrecioTotal = ($dato->getCantidad() * $dato->getPrecioVenta()) - $fltValorDescuento;
            
            $fltPrecioTotal = $fltPrecioTotal < 0 ? 0 : $fltPrecioTotal;
            
            $entityTipoSolicitud    = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                  ->findOneBy(array("servicioId"      => $dato->getId(), 
                                                                    "tipoSolicitudId" => $entityTipoSolicitud));
            $banderaAnulacionOrdenTrabajo = "";
            $idDetalleSolicitud = "";

            if($entityDetalleSolicitud)
            {
                if(in_array($entityDetalleSolicitud->getEstado(), $arrayEstados))
                {  
                    $banderaAnulacionOrdenTrabajo = "si";
                    $idDetalleSolicitud = $entityDetalleSolicitud->getId();

                    $datosBusqueda = array();
                    $datosBusqueda['fechaDesdePlanif'] = "";
                    $datosBusqueda['fechaHastaPlanif'] = "";
                    $datosBusqueda['fechaDesdeIngOrd'] = "";
                    $datosBusqueda['fechaHastaIngOrd'] = "";
                    $datosBusqueda['tipoSolicitud'] = "";
                    $datosBusqueda['estado'] = "";
                    $datosBusqueda['ciudad'] = "";
                    $datosBusqueda['idSector'] = "";
                    $datosBusqueda['identificacion'] = "";
                    $datosBusqueda['vendedor'] = "";
                    $datosBusqueda['nombres'] = "";
                    $datosBusqueda['apellidos'] = "";
                    $datosBusqueda['login'] = $dato->getPuntoId()->getLogin();
                    $datosBusqueda['descripcionPunto'] = "";
                    $datosBusqueda['codEmpresa'] = $strCodEmpresa;
                    $datosBusqueda['prefijoEmpresa'] = "";
                    $datosBusqueda['usrCreacion'] = "";
                    $datosBusqueda['start'] = "";
                    $datosBusqueda['limit'] = "";
                    $datosBusqueda['objEmSoporte'] = $objEmSoporte;
                    $datosBusqueda["ociCon"]       = array('userComercial' => $this->container->getParameter('user_comercial'),
                                                           'passComercial' => $this->container->getParameter('passwd_comercial'),
                                                           'databaseDsn'   => $this->container->getParameter('database_dsn'));

                    $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonCoordinar($datosBusqueda);

                    $objJson = json_decode($objJson, true);
                    $datosSolicitudes = $objJson['encontrados'];

                    foreach($datosSolicitudes as $dataSolicitudes):
                        if($idDetalleSolicitud == $dataSolicitudes['id_factibilidad'])
                        {
                            $tercializadora = $dataSolicitudes['tercerizadora'];
                            $cliente = $dataSolicitudes['cliente'];
                            $login2 = $dataSolicitudes['login2'];
                            $ciudad = $dataSolicitudes['ciudad'];
                            $direccion = $dataSolicitudes['direccion'];
                            $nombreSector = $dataSolicitudes['nombreSector'];
                            $esRecontratacion = $dataSolicitudes['esRecontratacion'];
                            $producto = $dataSolicitudes['producto'];
                            $tipo_orden = $dataSolicitudes['tipo_orden'];
                            $telefonos = $dataSolicitudes['telefonos'];
                            $observacion = $dataSolicitudes['observacion'];
                        }
                    endforeach;
                }
                else
                {
                    $banderaAnulacionOrdenTrabajo = "no";
                    $idDetalleSolicitud = "";
                }
            }
            else
            {
                //se agrega validacion de solicitudes de factibilidad para anulacion de servicio
                $entityTipoSolicitud    = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                      ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
                $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                      ->findOneBy(array("servicioId" => $dato->getId(), "tipoSolicitudId" => $entityTipoSolicitud));

                if($entityDetalleSolicitud)
                {
                    if(in_array($entityDetalleSolicitud->getEstado(), $arrayEstados))
                    {
                        $banderaAnulacionOrdenTrabajo = "si";
                        $idDetalleSolicitud = $entityDetalleSolicitud->getId();

                        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
                        $arrayParametros                             = array();
                        $arrayParametros["em"]                       = $emInfraestructura;
                        $arrayParametros["start"]                    = "";
                        $arrayParametros["limit"]                    = "";
                        $arrayParametros["search_fechaDesdePlanif"]  = "";
                        $arrayParametros["search_fechaHastaPlanif"]  = "";
                        $arrayParametros["search_login2"]            = $dato->getPuntoId()->getLogin();
                        $arrayParametros["search_descripcionPunto"]  = "";
                        $arrayParametros["search_vendedor"]          = "";
                        $arrayParametros["search_ciudad"]            = "";
                        $arrayParametros["search_numOrdenServicio"]  = "";
                        $arrayParametros["codEmpresa"]               = $strCodEmpresa;
                        $arrayParametros["ultimaMilla"]              = $ultimaMilla;
                        $arrayParametros["validaRechazado"]          = "SI";
                        $arrayParametros["serviceTecnico"]           = $serviceTecnico;

                        $objJson = $this->getDoctrine()
                            ->getManager("telconet")
                            ->getRepository('schemaBundle:InfoDetalleSolicitud')
                            ->generarJsonPreFactibilidad($arrayParametros);

                        $objJson = json_decode($objJson, true);
                        $datosSolicitudes = $objJson['encontrados'];
                        foreach($datosSolicitudes as $dataSolicitudes):
                            if($idDetalleSolicitud == $dataSolicitudes['id_factibilidad'])
                            {
                                $tercializadora = $dataSolicitudes['tercerizadora'];
                                $cliente = $dataSolicitudes['cliente'];
                                $login2 = $dataSolicitudes['login2'];
                                $ciudad = $dataSolicitudes['ciudad'];
                                $direccion = $dataSolicitudes['direccion'];
                                $nombreSector = $dataSolicitudes['nombreSector'];
                                $esRecontratacion = $dataSolicitudes['esRecontratacion'];
                                $producto = $dataSolicitudes['producto'];
                                $tipo_orden = $dataSolicitudes['tipo_orden'];
                                $telefonos = $dataSolicitudes['telefonos'];
                                $observacion = $dataSolicitudes['observacion'];
                            }
                        endforeach;
                    }
                    else
                    {
                        $banderaAnulacionOrdenTrabajo = "no";
                        $idDetalleSolicitud = "";
                    }
                }
                else
                {
                    $banderaAnulacionOrdenTrabajo = "no";
                    //se agrega validacion de solicitudes de factibilidad para anulacion de servicio
                    $entityTipoSolicitud    = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneByDescripcionSolicitud("SOLICITUD INFO TECNICA");
                    $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                          ->findOneBy( array( "servicioId"      => $dato->getId(), 
                                                                              "tipoSolicitudId" => $entityTipoSolicitud ) );
                    if($entityDetalleSolicitud)
                    {
                        $idDetalleSolicitud = $entityDetalleSolicitud->getId();
                    }
                    else
                    {
                        $idDetalleSolicitud = "";
                    }                        
                }
            }
            
            /*
             * Bloque que escribe la fecha de renovación del plan cuando el plan requiere renovacion
             */
            $strFechaRenovacion          = "";
            $boolMostrarAccionRenovacion = false;
            
            if( $tipo == 'plan' )
            {
                $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array('estado' => 'Activo', 'descripcionCaracteristica' => 'REQUIERE_RENOVACION'));
                
                if( $objAdmiCaracteristica )
                {
                    $objInfoPlanCaracteristica = $emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                             ->findOneBy( array( 'estado'            => 'Activo',
                                                                                 'planId'            => $dato->getPlanId(),
                                                                                 'caracteristicaId'  => $objAdmiCaracteristica ) );
                    
                    if( $objInfoPlanCaracteristica )
                    {
                        if( $objInfoPlanCaracteristica->getValor() == 'SI' && trim($dato->getEstado()) == 'Activo' )
                        {
                            $strFechaRenovacion      = $objServicioRepository->getFechaRenovacion($dato->getId());
                        }//( $objInfoPlanCaracteristica->getValor() == 'SI' )
                    }//( $objInfoPlanCaracteristica )
                }//( $objAdmiCaracteristica )
            }//( $tipo == 'plan' )
            elseif( $tipo == 'producto' )
            {
                $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array('estado' => 'Activo', 'descripcionCaracteristica' => 'REQUIERE_RENOVACION'));
                
                if( $objAdmiCaracteristica )
                {
                    $objAdmiProductoCaracteristica = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                 ->findOneBy( array( 'estado'            => 'Activo', 
                                                                                     'productoId'        => $dato->getProductoId(), 
                                                                                     'caracteristicaId'  => $objAdmiCaracteristica ) );
                    
                    if( $objAdmiProductoCaracteristica )
                    {
                        $strFechaRenovacion = $objServicioRepository->getFechaRenovacion($dato->getId());
                    }//( $objAdmiProductoCaracteristica )
                }//( $objAdmiCaracteristica )
            }//( $tipo == 'producto' )
            
            
            if( !empty($strFechaRenovacion) )
            {
                $arrayFechaRenovacion    = explode("/", $strFechaRenovacion);
                $datetimeFechaRenovacionSumar  = new \DateTime( $arrayFechaRenovacion[2]."-".$arrayFechaRenovacion[1]."-"
                                                                .$arrayFechaRenovacion[0]);
                $datetimeFechaRenovacionRestar = new \DateTime( $arrayFechaRenovacion[2]."-".$arrayFechaRenovacion[1]."-"
                                                                .$arrayFechaRenovacion[0]);

                $datetimeFechaActual      = new \DateTime("now");
                $dateintervalMesesAntes   = new \DateInterval('P3M');
                $dateintervalMesesDespues = new \DateInterval('P2M');

                $datetimeFechaRenovacionSumar->add($dateintervalMesesDespues);
                $datetimeFechaRenovacionRestar->sub($dateintervalMesesAntes);
                
                if( $datetimeFechaActual >= $datetimeFechaRenovacionRestar && $datetimeFechaActual <= $datetimeFechaRenovacionSumar )
                {
                    $boolMostrarAccionRenovacion = true;
                }
            }//( !empty($strFechaRenovacion) )
            /*
             * Fin Bloque que escribe la fecha de renovación del plan cuando el plan requiere renovacion
             */
            
            $tieneAnexo          = 'NO';
            $intIdServicioBackUp = null;
            
            if($strPrefijoEmpresa == 'TN')
            {
                // Se verifica si el producto(si lo es) posee anexo técnico.
                $arrayParametrosAnexo                  = array();
                $arrayParametrosAnexo['intIdProducto'] = $idProducto;
                $arrayParametrosAnexo['strTipo']       = $tipo;
                $arrayParametrosAnexo['strTipoAnexo']  = 'ANEXO_TECNICO';
                $tieneAnexo = $this->consultarAnexoTecnico($arrayParametrosAnexo);
                
                // Se obtiene el servicio backup del actual servicio
                $intIdServicioBackUp = $objServicioRepository->getResultadoBackupServicio($dato->getId());
                $intIdServicioBackUp = $intIdServicioBackUp ? intval($intIdServicioBackUp) : $intIdServicioBackUp;
            }            
            
            $strNombreEmpleado = '';
            if($dato->getUsrVendedor())
            {
                $arrayParametros['EMPRESA'] = $strCodEmpresa;
                $arrayParametros['LOGIN']   = $dato->getUsrVendedor();
                $arrayParametros['TODOS']   = true; // El vendedor puede tener rol diferente a Activo o Modificado

                $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

                if($arrayResultado['TOTAL'] > 0)
                {                    
                    $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
                }
                else
                {
                     $objInfoPersonaVendedor = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                           ->findOneBy( array( 'login' => $dato->getUsrVendedor()));
                     if(is_object($objInfoPersonaVendedor))
                     {
                         $strNombreEmpleado = $objInfoPersonaVendedor->getNombres(). ' ' .$objInfoPersonaVendedor->getApellidos(); 
                     }
                }
            }

            $strRequiereBackup = 'NO';
            //Obtengo la Descripcion del Producto Clear a Channel Parametrizado
            $arrayParDet= $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('ESTADO_CLEAR_CHANNEL','COMERCIAL','','ESTADO_CLEAR_CHANNEL','','','','','',$strCodEmpresa);
            $strDescripProducto = $arrayParDet["valor1"];
            
            if($strPrefijoEmpresa == 'TN')
            {
                //Se envia bandera indicado si el servicio creado segun el producto se le puede crear o no un BACKUP
                $arrayParametros   = array('intIdServicio'     => $dato->getId());
                $objServicioCh   = $emComercial->getRepository('schemaBundle:InfoServicio')->find($dato->getId());
                if($objProducto->getNombreTecnico()!=='INTERNET SDWAN' && $objProducto->getNombreTecnico()!=='L3MPLS SDWAN')
                {
                    $strRequiereBackup = $this->get('comercial.InfoServicio')->validarServicioRequiereBackup($arrayParametros);    
                }
                if($strRequiereBackup == 'SI' && $strDescripProducto == $objProducto->getDescripcionProducto())
                {
                    $objServicioProductoTransp = $servicioTecnicoService->getServicioProductoCaracteristica($objServicioCh,
                                                                                            "REQUIERE TRANSPORTE",
                                                                                            $objProducto);

                        if($objServicioProductoTransp)
                        {
                            $strRequiereBackup = ($objServicioProductoTransp->getValor() == 'NO')?'SI':'NO';
                        }
                        else
                        {
                            $strRequiereBackup = 'NO';
                        }
                }
            }
            
            //SE VERIFICA SI EL SERVICIO CONTIENE PLANTILLA DE COMISIONISTAS ACTIVA PARA QUE PUEDA SER EDITADA POR EL USUARIO PERMITIDO
            $boolTieneComisionistas   = false;
            $strEstadoPlantillaBuscar = 'Activo';
                
            //SE VERIFICA SI EL ESTADO DEL SERVICIO ES VALIDO PARA PODER VER O EDITAR LA PLANTILLA DE COMISIONISTA
            $arrayParametroPlantillaComisionista = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('ESTADOS_GRID_SERVICIOS', 
                                                                      'COMERCIAL', 
                                                                      'PUNTO',
                                                                      $dato->getEstado(), 
                                                                      'CAMBIO_VENDEDOR',
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $strCodEmpresa);

            if( isset($arrayParametroPlantillaComisionista['id']) && !empty($arrayParametroPlantillaComisionista['id']) )
            {
                $strEstadoPlantillaBuscar = $dato->getEstado();
            }//( isset($arrayParametroPlantillaComisionista['id']) && !empty($arrayParametroPlantillaComisionista['id']) )
            
            if(true === $this->get('security.context')->isGranted('ROLE_9-5337'))//VER PLANTILLA DE COMISIONISTAS
            {
                $arrayPlantillaComisionista = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                          ->findBy( array('estado' => $strEstadoPlantillaBuscar, 'servicioId' => $dato) );

                if( !empty($arrayPlantillaComisionista) )
                {
                    $boolTieneComisionistas = true;
                }//( !empty($arrayPlantillaComisionista) )
            }//(true === $this->get('security.context')->isGranted('ROLE_9-5337'))
            
            
            /**
             * BLOQUE QUE VERIFICA SI EL SERVICIO TIENE ASOCIADO UNA PLANTILLA DE COMISIONISTA CON ROL VENDEDOR LA CUAL GENERARA UNA SOLICITUD POR
             * CAMBIO DE VENDEDOR
             */
            $boolGeneraSolicitudVendedor = false;
            $arrayParametrosComision     = array('arrayEstados'       => array('Activo'),
                                                 'intIdServicio'      => $dato->getId(),
                                                 'strRolComisionista' => 'VENDEDOR');
            $arrayResultadoComision      = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->getServicioComision($arrayParametrosComision);
            
            if( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] > 0 )
            {
                $boolGeneraSolicitudVendedor = true;
            }//( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] > 0 )

            //Se envia valores para validar si el tipo de producto necesita anexo tecnico/comercial creado para poder seguir con el 
            //flujo
            
            $strContinuaFlujoNormal = 'SI';
            
            //Si el estado es Pre-Servicio y este a su vez requiere anexo tecnico, se verifica si ya tiene la informacion
            //completa cargada en el sistema, este pueda continuar con su flujo de acuerdo a su configuracion
            if($tieneAnexo == 'SI' && $dato->getEstado() == 'Pre-servicio')
            {               
                $boolTieneAnexoTecnico   = false;
                $boolTieneAnexoComercial = false;

                //Validar que se encuentre subida la informacion de anexos comerciales
                $objRelacionDocumento = $emComunicacion->getRepository("schemaBundle:InfoDocumentoRelacion")
                                                       ->findOneBy(array('servicioId' => $dato->getId(),
                                                                         'modulo'     => 'TECNICO',
                                                                         'estado'     => 'Activo'));
                if(is_object($objRelacionDocumento))
                {
                    $boolTieneAnexoTecnico = true;
                }

                //Validar si el producto debe tener anexo comercial
                $arrayParametrosAnexo                  = array();
                $arrayParametrosAnexo['intIdProducto'] = $idProducto;
                $arrayParametrosAnexo['strTipo']       = $tipo;
                $arrayParametrosAnexo['strTipoAnexo']  = 'ANEXO_COMERCIAL';
                $strTieneAnexoComercial = $this->consultarAnexoTecnico($arrayParametrosAnexo);

                if($strTieneAnexoComercial == 'SI')
                {
                    //Validar que se encuentre subida la informacion de anexos comerciales
                    $objRelacionDocumento = $emComunicacion->getRepository("schemaBundle:InfoDocumentoRelacion")
                                                           ->findOneBy(array('servicioId' => $dato->getId(),
                                                                             'modulo'     => 'COMERCIAL',
                                                                             'estado'     => 'Activo'));
                    if(is_object($objRelacionDocumento))
                    {
                        $boolTieneAnexoComercial = true;
                    }

                    if($boolTieneAnexoTecnico && $boolTieneAnexoComercial)
                    {
                        $strContinuaFlujoNormal = 'SI';
                    }
                    else
                    {
                        $strContinuaFlujoNormal = 'NO';
                    }
                }
                else
                {
                    if(!$boolTieneAnexoTecnico)
                    {
                        $strContinuaFlujoNormal = 'NO';
                    }
                }
            }
            
            $strSolucion                     = '';
            $strTieneOpcionMigracionDataFact = 'NO';
            
            if($strPrefijoEmpresa == 'TN')
            {
                $arrayParametroSolucion                  = array();
                $arrayParametroSolucion['objServicio']   = $dato;
                $arrayParametroSolucion['strCodEmpresa'] = $strCodEmpresa;
                $strSolucion = $servicioTecnicoService->getNombreGrupoSolucionServicios($arrayParametroSolucion);
                
                //Verificar si el servicio pertenece a un cliente desde donde se va a migrar
                $arrayCliente = $session->get('cliente');
                
                $strRazonSocialCliente = isset($arrayCliente['razon_social'])?$arrayCliente['razon_social']:
                                               $arrayCliente['nombres'].' '.$arrayCliente['apellidos'];
                
                $arrayInfoEnvio   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('RAZON SOCIAL CON OPCION DE INTERCONEXION', 
                                                      'COMERCIAL', 
                                                      '',
                                                      '',
                                                      $strRazonSocialCliente, 
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $strCodEmpresa);
                if(!empty($arrayInfoEnvio))
                {
                    $arrayTipoProductoInterconexion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('PRODUCTOS A MIGRAR POR INTERCONEXION', 
                                                                          'COMERCIAL', 
                                                                          '',
                                                                          $strRazonSocialCliente,
                                                                          '', 
                                                                          '',
                                                                          '',
                                                                          '', 
                                                                          '', 
                                                                          $strCodEmpresa);
                    if(!empty($arrayTipoProductoInterconexion))
                    {
                        foreach($arrayTipoProductoInterconexion as $array)
                        {
                            if($nombreTecnicoProducto == $array['valor1'])
                            {
                                $strTieneOpcionMigracionDataFact = 'SI';
                                break;
                            }
                        }
                    }
                }
            }

            //Consultamos si el producto es de CANAL TELEFONIA para realizar la búsqueda de la marca de activación simultánea
            if ($dato->getDescripcionPresentaFactura() == 'CANAL TELEFONIA')
            {
                //Preguntamos si es activación simultánea y consultamos el estado del servicio tradicional
                $arrayCouSim          = $objPlanificarService->getIdTradInstSimCanaTelefonia($dato->getId(),$idProducto);
                $intIdServTradicional = $arrayCouSim[0];
                if ($intIdServTradicional !== null)
                {
                    $objServTradicional = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServTradicional);
                    if((is_object($objServTradicional)) && ($objServTradicional->getEstado() == 'Activo'))
                    {
                        $strFlagActivacion    = 'S';
                    }
                        $strFlagActiSimul = 'S';
                }
            }
                        
            //seteo la variable de requerimiento del enlace de datos
            $strReqEnlaceDatos = 'SI';
            
            //seteo la variable para la opción de eliminar el servicio
            $strOpcionEliminarServicio = 'SI';
            
            //seteo la relación mascarilla
            $booleanRelCamMascarilla = false;
            //seteo el tipo red
            $strTipoRed = "";
            if($strPrefijoEmpresa == 'TN')
            {
                $strTipoRed = "";
                //se verifica si el producto tambien pertenece a GPON para setear por default tipo red MPLS
                $arrayParProductoGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('NUEVA_RED_GPON_TN',
                                                     'COMERCIAL',
                                                     '',
                                                     'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
                                                     $dato->getProductoId()->getId(),
                                                     '',
                                                     '',
                                                     'S',
                                                     'RELACION_PRODUCTO_CARACTERISTICA',
                                                     $strCodEmpresa);
                if(isset($arrayParProductoGpon) && !empty($arrayParProductoGpon))
                {
                    $strTipoRed = "MPLS";
                }
                //se obtiene el parametro si se configura el enlace de datos del producto
                $arrayParametroEnlaceDatos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
                                                             'TECNICO',
                                                             '',
                                                            '',
                                                            $dato->getProductoId()->getId(),
                                                            'ENLACE_DATOS',
                                                            '',
                                                            '',
                                                            '',
                                                            $strCodEmpresa);
                if( isset($arrayParametroEnlaceDatos) && !empty($arrayParametroEnlaceDatos) )
                {
                    $strReqEnlaceDatos = $arrayParametroEnlaceDatos['valor3'];
                }
                //obtener tipo red
                $objCaractTipoRed = $serviceTecnico->getServicioProductoCaracteristica($dato, 'TIPO_RED',
                                                                                       $dato->getProductoId());
                if(is_object($objCaractTipoRed))
                {
                    //seteo el tipo red
                    $strTipoRed = $objCaractTipoRed->getValor();
                    //se obtiene el parametro si se configura el enlace de datos del producto
                    $arrayParEnlaceDatosGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('NUEVA_RED_GPON_TN',
                                                         'COMERCIAL',
                                                         '',
                                                         '',
                                                         $dato->getProductoId()->getId(),
                                                         'ENLACE_DATOS',
                                                         $objCaractTipoRed->getValor(),
                                                         '',
                                                         '',
                                                         $strCodEmpresa);
                    if( isset($arrayParEnlaceDatosGpon) && !empty($arrayParEnlaceDatosGpon) )
                    {
                        $strReqEnlaceDatos = $arrayParEnlaceDatosGpon['valor4'];
                    }
                }
                //se obtiene el parametro de la opción de eliminar el servicio
                $arrayParEliminarServicio  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
                                                             'TECNICO',
                                                             '',
                                                             '',
                                                             $dato->getProductoId()->getId(),
                                                             'OPCION_PRODUCTO_ELIMINAR',
                                                             '',
                                                             '',
                                                             '',
                                                             $strCodEmpresa);
                if( isset($arrayParEliminarServicio) && !empty($arrayParEliminarServicio) )
                {
                    $strOpcionEliminarServicio = $arrayParEliminarServicio['valor3'];
                }
                //validar mascarilla asociacion
                if($dato->getProductoId()->getNombreTecnico() === "SERVICIOS-CAMARA-SAFECITY")
                {
                    $objVerRelaciontMascarilla = $serviceTecnico->getServicioProductoCaracteristica($dato,
                                                                                                    'RELACION_MASCARILLA_CAMARA_SAFECITY',
                                                                                                    $dato->getProductoId());
                    if(is_object($objVerRelaciontMascarilla))
                    {
                        $booleanRelCamMascarilla = true;
                    }
                }
            }
            
            //Variables adicionales para el reingreso de la OS automática.
            $intIdPuntoCobertura   = '';
            $intIdCanton           = '';
            $intIdParroquia        = '';
            $intIdSector           = '';
            $strElementoEdificio   = '';
            $intElementoEdificioId = '';
            $strDependeDeEdificio  = '';
            $boolMostrarBotonOSA   = false;

            //Se verifica los tipo_orden parametrizadas las cuales son permitidas para la ejecucion del flujo de Reingreso Automatico de OS
            $arrayListadoTipoOrden = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                          ->get("PARAMETROS_REINGRESO_OS_AUTOMATICA", "COMERCIAL", "REINGRESO AUTOMATICO", 
                                                "TIPO_ORDEN", "", "", "", "", "",
                                                $strCodEmpresa);
            $arrayTipoOrden = array();
        
            foreach($arrayListadoTipoOrden as $objTipoOrden)
            {
                $arrayTipoOrden[] = $objTipoOrden['valor1'];
            }
            //Se verifica si el Tipo_Orden se encuentra dentro de los valores parametrizados.
            $boolTipoOrden= in_array($dato->getTipoOrden(), $arrayTipoOrden);
            
            //Se verifica los estados del punto parametrizados los cuales son permitidos para la ejecución del flujo de Reingreso Automatico de OS
            $arrayListadoEstadosPto = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get("PARAMETROS_REINGRESO_OS_AUTOMATICA", "COMERCIAL", "REINGRESO AUTOMATICO", 
                                                    "ESTADO_PUNTO", "", "", "", "", "",
                                                    $strCodEmpresa);
            $arrayEstadoPunto = array();
        
            foreach($arrayListadoEstadosPto as $objEstadoPunto)
            {
                $arrayEstadoPunto[] = $objEstadoPunto['valor1'];
            }
            //Se verifica si el estado del Punto en session se encuentra dentro de los valores parametrizados.
            $boolEstadoPunto = in_array($dato->getPuntoId()->getEstado(), $arrayEstadoPunto);
            
            
            $intExisteServicio = 0;
            $intExisteServicio = $emComercial->getRepository("schemaBundle:InfoServicio")
                                             ->getEstadosServiciosValidos(array('intIdPunto'             => $dato->getPuntoId()->getId(),
                                                                                'strNombreParametro'     => 'PARAMETROS_REINGRESO_OS_AUTOMATICA',
                                                                                'strEstado'              => 'Activo', 
                                                                                'strDetParametro'        => 'ESTADOS_SERVICIO_VALIDA_REINGRESO',
                                                                                'strDetParamCodProd'     => 'CODIGO_PRODUCTO',
                                                                                'strDetParamEstadoPunto' => 'ESTADO_PUNTO'));
            $intServicioInternet = 0;
            $intServicioInternet = $emComercial->getRepository("schemaBundle:InfoServicio")
                                               ->getValidaServicioInternet(array( 'intIdServicio'         => $dato->getId(),
                                                                                  'strNombreParametro'    => 'PARAMETROS_REINGRESO_OS_AUTOMATICA',
                                                                                  'strEstado'             => 'Activo',
                                                                                  'strDetParamCodProd'    => 'CODIGO_PRODUCTO',
                                                                                ));
            //Validación para habilitar el botón de reingreso de orden de servicio automática.
            $arrayValidarOS = $objInfoServicioService->validarReingresoOrdenServicio(array('strUsuarioCreacion'=> $strUsuarioCreacion,
                                                                                           'strIpCreacion'     => $strIpCreacion,
                                                                                           'intIdServicio'     => $dato->getId(),
                                                                                           'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                                                           'strCodEmpresa'     => $strCodEmpresa,
                                                                                           'strFlujo'          => array('validarDatosGeograficos',
                                                                                                                        'validarMotivos',
                                                                                                                        'validarPrePlanificacion',
                                                                                                                        'validarDiasOrdenServicio',
                                                                                                                        'validarReingresoFinalizado'
                                                                                                                       )
                                                                                           ));
            
            if (!empty($arrayValidarOS) && $arrayValidarOS['status'] && $boolTipoOrden && $boolEstadoPunto && $intExisteServicio == 0
                && $intServicioInternet ==1)
            {
                $boolMostrarBotonOSA = true;

                $objInfoPuntoDatoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                         ->findOneByPuntoId($dato->getPuntoId()->getId());

                if (is_object($objInfoPuntoDatoAdicional))
                {
                    $strDependeDeEdificio = $objInfoPuntoDatoAdicional->getDependeDeEdificio();

                    if (is_object($objInfoPuntoDatoAdicional->getElementoId()))
                    {
                        $strElementoEdificio   = $objInfoPuntoDatoAdicional->getElementoId()->getNombreElemento();
                        $intElementoEdificioId = $objInfoPuntoDatoAdicional->getElementoId()->getId();
                    }
                }

                //Obtenemos la información adicional del punto.
                if (is_object($dato->getPuntoId()->getPuntoCoberturaId()))
                {
                    $intIdPuntoCobertura = $dato->getPuntoId()->getPuntoCoberturaId()->getId();
                }

                if (is_object($dato->getPuntoId()->getSectorId()))
                {
                    $intIdSector = $dato->getPuntoId()->getSectorId()->getId();

                    if (is_object($dato->getPuntoId()->getSectorId()->getParroquiaId()))
                    {
                        $intIdParroquia = $dato->getPuntoId()->getSectorId()->getParroquiaId()->getId();

                        if (is_object($dato->getPuntoId()->getSectorId()->getParroquiaId()->getCantonId()))
                        {
                            $intIdCanton = $dato->getPuntoId()->getSectorId()->getParroquiaId()->getCantonId()->getId();
                        }
                    }
                }
            }

            if ($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {   
                $strCodigoIns  = "";
                $strCodigoMens = "";
                $strCodigoBw   = "";
                $objCarIns     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => "PROM_COD_INST",
                                                               'estado'                    => "Activo"));
                $objCarMens    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => "PROM_COD_NUEVO",
                                                               'estado'                    => "Activo"));
                $objCarBw      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => "PROM_COD_BW",
                                                               'estado'                    => "Activo"));
                if ($objCarIns)
                {   
                    $objInfoServCaracIns   = "";
                    $objInfoServCaracIns   = $emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                                                         ->findOneBy(array("servicioId"       => $dato->getId(),
                                                                           "caracteristicaId" => $objCarIns));
                    if (!empty($objInfoServCaracIns))
                    {   
                        $arrayParametrosIns                       = "";
                        $arrayParametrosIns['strIdTipoPromocion'] = $objInfoServCaracIns->getValor();
                        $arrayParametrosIns['strCodEmpresa']      = $strCodEmpresa;
                        $strCodigoIns    = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                       ->getCodigoPromocion($arrayParametrosIns);
                    }
                }
                if ($objCarMens)
                {
                    $objInfoServCaracMens  = "";
                    $objInfoServCaracMens  = $emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                                                         ->findOneBy(array("servicioId"       => $dato->getId(),
                                                                           "caracteristicaId" => $objCarMens));
                    if (!empty($objInfoServCaracMens))
                    {
                        $arrayParametrosMens                       = "";
                        $arrayParametrosMens['strIdTipoPromocion'] = $objInfoServCaracMens->getValor();
                        $arrayParametrosMens['strCodEmpresa']      = $strCodEmpresa;
                        $strCodigoMens    = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                        ->getCodigoPromocion($arrayParametrosMens);
                    }
                }
                if ($objCarBw)
                {
                    $objInfoServCaracBw    = "";
                    $objInfoServCaracBw    = $emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                                                         ->findOneBy(array("servicioId"       => $dato->getId(),
                                                                           "caracteristicaId" => $objCarBw));
                    if (!empty($objInfoServCaracBw))
                    {
                        $arrayParametrosBw                       = "";
                        $arrayParametrosBw['strIdTipoPromocion'] = $objInfoServCaracBw->getValor();
                        $arrayParametrosBw['strCodEmpresa']      = $strCodEmpresa;
                        $strCodigoBw    = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                      ->getCodigoPromocion($arrayParametrosBw);
                    }
                }
                
            }


            //Se obtiene el parametro para habilitar si permite o no edición de Datos geograficos.
            $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_REINGRESO_OS_AUTOMATICA', 
                                                         'COMERCIAL', '', 'PERMITE_EDITAR_DATOS_GEOGRAFICOS',
                                                         '', '', '', '', '', $strCodEmpresa);

            $strEditaDatosGeograficos  = (isset($arrayValoresParametros["valor1"])
                                          && !empty($arrayValoresParametros["valor1"])) ? $arrayValoresParametros["valor1"]
                                          : 'S';                        
        
        // Se consulta el metraje filtrando los siguiente:
        // se busca el tipo de solicitud -SOLICITUD FACTIBILIDAD- para con ello filtrar la InfoDetalleSolicitud y la InfoDetalleSolCaract
        $strMetrajeC           = "";
        $strModuloC            = 'COMERCIAL';
        $strMensajeC           = '';
        $strIdServicioC        = $dato->getId();
        $strEstadoServicio     = $dato->getEstado();
        $floatValorCaractOCivil          = 0;
        $floatValorCaractFibraMetros     = 0;
        $floatValorCaractOtrosMateriales = 0;
        $floatValorCaractCancPorCli      = 0;
        $floatValorCaractAsumeCli        = 0;
        $floatValorCaractAsumeEmpresa    = 0;
        $strUuidPaquete                  = '';
        if($strPrefijoEmpresa == "TN")
        {
            $objCaracteristicaFibraMetros          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD');                                            
            $objCaracteristicaOCivil          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
            $objCaracteristicaOtrosMateriales = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
            $objCaracteristicaCancPorCli      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');
            $objCaracteristicaAsumeCli        = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');
            $objCaracteristicaAsumeEmpresa    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');

            // Inicio: consulta si hay archivos como evidencia de excedente
            $strNombreDocumento = 'Adjunto Archivo de Evidencia';
            $strEvidencia       = null;
    
            $strDocumentoRelacionC  = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                    ->findBy(array("servicioId"    => $strIdServicioC,
                                                                "estado"        => "Activo"));        
            if(count($strDocumentoRelacionC) > 0)
            {
                foreach($strDocumentoRelacionC as $documento)
                {
                    if ($strNombreDocumento)
                    {
                        $strArchivoC = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                    ->findOneBy(array(
                                                        'id' => $documento->getDocumentoId(),
                                                        'nombreDocumento' => $strNombreDocumento  ));
                    }
                    else
                    {
                        $strArchivoC = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                    ->find($documento->getDocumentoId());
                    }
    
                    if (is_object($strArchivoC))
                    {
                        $arrayEncontrados[] = array('ubicacionLogica' => $strArchivoC->getUbicacionLogicaDocumento(),
                            'feCreacion' => ($strArchivoC->getFeCreacion() ? date_format($strArchivoC->getFeCreacion(), "d-m-Y H:i") : ""),
                            'linkVerDocumento' => $strArchivoC->getUbicacionFisicaDocumento(),
                            'idDocumento' => $strArchivoC->getId());
                    }
    
                }
                $objData        = json_encode($arrayEncontrados);
                $strEvidencia = 'Cliente tiene documento(s) de evidencia';                    
            }               
            // Fin: consulta si hay archivos como evidencia de excedente
            
            $boolSegumiento         = false;
            $strNombreProducto =  ($dato->getProductoId() ? $dato->getProductoId()->getDescripcionProducto() : "");  
            $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                    "COMERCIAL", 
                                                                    "", 
                                                                    "", 
                                                                    $strNombreProducto, 
                                                                    "", 
                                                                    "",
                                                                    "",
                                                                    "",
                                                                    $strCodEmpresa
                                                                );
            if(!is_array($arrayParametrosDet) && empty($arrayParametrosDet))
            {
                $boolSegumiento = false;
            }
            else
            {
                $boolSegumiento = true;
            }
            //Código que valida si el producto pertenece a la vertical connectivity
            if(is_object($dato->getProductoId()))
            {
                $strLineaNegocio        = $dato->getProductoId()->getLineaNegocio();
                $strGrupo               = $dato->getProductoId()->getGrupo();
            }
            $arrayParametrosDet = null;
            $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("Vertical para proyecto excedente de material", 
                                                                    "PLANIFICACIÓN", 
                                                                    "", 
                                                                    "", 
                                                                    $strLineaNegocio, 
                                                                    $strGrupo, 
                                                                    "",
                                                                    "",
                                                                    "",
                                                                    $strCodEmpresa
                                                                );

            // Con el id_serviciodel servicio se va a consultar la SOLICITUD MATERIALES EXCEDENTES
            $objTipoSolExcMaterial = $em->getRepository("schemaBundle:AdmiTipoSolicitud")
                                ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');
                                
            $objSolicitudExcedente = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                    ->findUlitmoDetalleSolicitudByIds( $strIdServicioC,$objTipoSolExcMaterial[0]->getId());

            $strSolExcedenteMaterial = 'No existen solicitudes previas';

            if($objSolicitudExcedente)
            {
                
                if( ($objSolicitudExcedente->getEstado()=='Aprobado')&&(count($strDocumentoRelacionC) > 0)
                    &&($strEstadoServicio=='Factible')  )
                {
                    $strSolExcedenteMaterial = null;
                }
                else
                {
                $strSolExcedenteMaterial = 'Solicitud de excedente de material #'.
                                            $objSolicitudExcedente->getId().' en estado '.
                                            $objSolicitudExcedente->getEstado().'.';                            
                }
            }
            // Busca el tipo de solicitud factibilidad
            $emTipoSolicitudFactibilidad = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD FACTIBILIDAD",
                                                        "estado"               => "Activo"));
            if($emTipoSolicitudFactibilidad)
            {
                // Busca el tipo de solicitud factibilidad con el id_servicio 
                $emDetalleSolicitudFactibilidad = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findOneBy(array("servicioId"      => $strIdServicioC,
                                                              "tipoSolicitudId" => $emTipoSolicitudFactibilidad->getId()));
                if($emDetalleSolicitudFactibilidad)
                {
                    $intIdDetalleSolicitudFactibilidad = $emDetalleSolicitudFactibilidad->getId();
                    // Busca en el detalle solicitud caract el tipo de solicitud factibilidad con el id_servicio, la caract de metraje

                    $entityMetraje = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                    ->getSolicitudCaractPorTipoCaracteristica($emDetalleSolicitudFactibilidad->getId(),'METRAJE FACTIBILIDAD');

                    $objInfoDetalleSolCaractFibraMetros          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaFibraMetros,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                    $objInfoDetalleSolCaractOCivil          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaOCivil,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                    $objInfoDetalleSolCaractOtrosMateriales = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaOtrosMateriales,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                    $objInfoDetalleSolCaractCancPorCli      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaCancPorCli,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                    $objInfoDetalleSolCaractAsumeCli        = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeCli,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                    $objInfoDetalleSolCaractAsumeEmpresa    =  $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeEmpresa,
                                                            'detalleSolicitudId' => $emDetalleSolicitudFactibilidad->getId()   ));
                }
         
                    // Busca el tipo de solicitud planificación
                    $emTipoSolicitudPlan = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                "estado"             => "Activo"));
                    if($emTipoSolicitudPlan)
                    {
                        // Busca la de solicitud planificación del id_servicio 
                        $emDetalleSolicitudPlan = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array("servicioId"      => $strIdServicioC,
                                                                    "tipoSolicitudId" => $emTipoSolicitudPlan->getId()));
                        if($emDetalleSolicitudPlan)
                        {
                            $objInfoDetalleSolCaractFibraMetrosPlan          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaFibraMetros,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                            $objInfoDetalleSolCaractOCivilPlan          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaOCivil,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                            $objInfoDetalleSolCaractOtrosMaterialesPlan = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaOtrosMateriales,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                            $objInfoDetalleSolCaractCancPorCliPlan      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaCancPorCli,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                            $objInfoDetalleSolCaractAsumeCliPlan        = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeCli,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                            $objInfoDetalleSolCaractAsumeEmpresaPlan    =  $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeEmpresa,
                                                                    'detalleSolicitudId' => $emDetalleSolicitudPlan->getId()   ));
                        }
                    }

                    if(!$objInfoDetalleSolCaractFibraMetrosPlan)
                    {
                        if($objInfoDetalleSolCaractFibraMetros)
                        {
                            $strMetrajeC              = $objInfoDetalleSolCaractFibraMetros->getValor();
                        }
                        if($objInfoDetalleSolCaractOCivil)
                        {
                            $floatValorCaractOCivil = $objInfoDetalleSolCaractOCivil->getValor();
                        }
                        if($objInfoDetalleSolCaractOtrosMateriales)
                        {
                            $floatValorCaractOtrosMateriales = $objInfoDetalleSolCaractOtrosMateriales->getValor();
                        }
                        if($objInfoDetalleSolCaractCancPorCli)
                        {
                            $floatValorCaractCancPorCli = $objInfoDetalleSolCaractCancPorCli->getValor();
                        }
                        if($objInfoDetalleSolCaractAsumeCli)
                        {
                            $floatValorCaractAsumeCli = $objInfoDetalleSolCaractAsumeCli->getValor();
                        }
                        if($objInfoDetalleSolCaractAsumeEmpresa)
                        {
                            $floatValorCaractAsumeEmpresa = $objInfoDetalleSolCaractAsumeEmpresa->getValor();
                        }
                    }
                    else
                    {
                        if($objInfoDetalleSolCaractFibraMetrosPlan)
                        {
                            $strMetrajeC              = $objInfoDetalleSolCaractFibraMetrosPlan->getValor();
                        }
                        if($objInfoDetalleSolCaractOCivilPlan)
                        {
                            $floatValorCaractOCivil = $objInfoDetalleSolCaractOCivilPlan->getValor();
                        }
                        if($objInfoDetalleSolCaractOtrosMaterialesPlan)
                        {
                            $floatValorCaractOtrosMateriales = $objInfoDetalleSolCaractOtrosMaterialesPlan->getValor();
                        }
                        if($objInfoDetalleSolCaractCancPorCliPlan)
                        {
                            $floatValorCaractCancPorCli = $objInfoDetalleSolCaractCancPorCliPlan->getValor();
                        }
                        if($objInfoDetalleSolCaractAsumeCliPlan)
                        {
                            $floatValorCaractAsumeCli = $objInfoDetalleSolCaractAsumeCliPlan->getValor();
                        }
                        if($objInfoDetalleSolCaractAsumeEmpresaPlan)
                        {
                            $floatValorCaractAsumeEmpresa = $objInfoDetalleSolCaractAsumeEmpresaPlan->getValor();
                        }

                    }
                  $arrayParametrosFibra =   $em->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne("Precio de fibra", 
                                                                             "SOPORTE", 
                                                                             "", 
                                                                             "Precio de fibra", 
                                                                             "", 
                                                                             "", 
                                                                             "",
                                                                             "",
                                                                             "",
                                                                             10
                                                                           );
                if(is_array($arrayParametrosFibra) && !empty($arrayParametrosFibra) && $strMetrajeC)
                {
                    $intPrecioFibra = $arrayParametrosFibra['valor1'];
                    $arrayParametrosMaximoFibra =   $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('Metraje que cubre el precio de instalación',
                                                                "COMERCIAL",
                                                                "",
                                                                'Metraje que cubre el precio de instalación',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                10);
                    if(isset($arrayParametrosMaximoFibra["valor1"]) && !empty($arrayParametrosMaximoFibra["valor1"]))
                    {
                        $intMetrosDeDistancia = $arrayParametrosMaximoFibra["valor1"];
                    }
                }               

            }

            //si es producto paquete de horas soporte
            $objParametroDetValProd =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne("VALIDA_PRODUCTO_PAQUETE_HORAS_SOPORTE", //nombre parametro cab
                            "SOPORTE", "", 
                            "VALORES QUE AYUDAN A IDENTIFICAR QUE PRODUCTO ES PARA LA MUESTRA DE OPCIONES EN LA VISTA", //descripcion det
                            "", "", "", "", "", $strCodEmpresa
                        );
            if (($objParametroDetValProd)) 
            {
                $strValorProductoPaqHoras             = $objParametroDetValProd['valor1'];
                $strValorProductoPaqHorasRec          = $objParametroDetValProd['valor2'];
                $strLoginPunto                        = $dato->getPuntoId()->getLogin();
                $intIdServicio                        = $dato->getId();
                //Se valida el producto
                if (($strNombreProducto == $strValorProductoPaqHoras)|| ($strNombreProducto == $strValorProductoPaqHorasRec)) 
                {
                    // Para saber si es replica o no.
                   $objServicioReplica            = $em->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array("servicioId" => $intIdServicio,
                                                                          "accion"     => "replicaPaqueteHoras"));
                    if($objServicioReplica)
                    {
                        $boolEsReplica            = true;
                    }

                    $objInfoPaqSopServ          = $objEmSoporte->getRepository('schemaBundle:InfoPaqueteSoporteServ')
                                                ->soporteServPorLogin(array("loginPuntoSoporte"  => $strLoginPunto)); 
                    if ($objInfoPaqSopServ)                            
                    {
                        $intPaqueteSoporteCabId     = $objInfoPaqSopServ[0]['paqueteSoporteCabId'];
                        $objInfoPaqueteSoporteCab   = $objEmSoporte->getRepository('schemaBundle:InfoPaqueteSoporteCab')
                                                    ->soporteCabPorCabId(array("idPaqueteSoporteCab" => $intPaqueteSoporteCabId));
                        $intIdServicio              = $objInfoPaqueteSoporteCab[0]['servicioId'];
                    }
                    $arrayObtenerUuid = $objEmSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
                                                    ->obtenerUuidPaquete($intIdServicio); //consulta por el servicio_id.
                    if ($arrayObtenerUuid) 
                    {
                        $strUuidPaquete         = $arrayObtenerUuid[0]['strUuidPaquete'];
                        $intPersonaEmpresaRolId = $dato->getPuntoId()->getPersonaEmpresaRolId()->getId(); //persona_empresa_rol_id
                    }
                }
            }
        }
            $strTipoOrden = $dato->getTipoOrden();

            $arreglo[] = array(
                'idServicio'                   => $dato->getId(),
                'tipo'                         => $tipo,
                'idPersonaRol'                 => $strClienteem,
                'idPunto'                      => $dato->getPuntoId()->getId(),
                'descripcionPunto'             => $dato->getPuntoId()->getDescripcionPunto(),
                'descripcionPresentaFactura'   => $dato->getDescripcionPresentaFactura(),
                'loginPadreFact'               => ($dato->getPuntoFacturacionId()) ? $dato->getPuntoFacturacionId()->getLogin() : "NA",
                'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                'idProducto'                   => $idProducto,
                'strTipoRed'                   => $strTipoRed,
                'booleanRelCamMascarilla'      => $booleanRelCamMascarilla,
                'descripcionProducto'          => $descripcionProducto,
                'nombreTecnicoProducto'        => $nombreTecnicoProducto,
                'esConcentrador'               => $esConcentrador,
                'cantidad'                     => $dato->getCantidad(),
                'fechaCreacion'                => strval(date_format($dato->getFeCreacion(), "d/m/Y")),
                'precioVenta'                  => "$  " . number_format($dato->getPrecioVenta(), 2),
                'precioInstalacion'            => "$  " . number_format($dato->getPrecioInstalacion(), 2),
                'loginAux'                     => $dato->getLoginAux(),                
                'valorDescuento'               => $dato->getValorDescuento(),
                'porcentajeDescuento'          =>  ($dato->getPorcentajeDescuento()) ? $dato->getPorcentajeDescuento()."%" : "0%",
                'descuento'                    => $strValorDescuento,
                'descuentoUnitario'            => $strDctoUnitario,
                'estado'                       => $dato->getEstado(),
                'numeroOT'                     => $numero_ot,
                'linkVer'                      => $linkVer,
                'linkEditar'                   => $linkEditar,
                'linkEliminar'                 => $linkEliminar,
                'linkFactibilidad'             => $strLinkFactibilidad,
                'clase'                        => $clase,
                'boton'                        => "",
                'tipoOrden'                    => $tipoOrden,
                'esVenta'                      => $strEsVenta,
                'ultimaMilla'                  => $ultimaMilla,
                'tipoMedio'                    => $strTipoMedio,
                'tipoEnlace'                   => $strTipoEnlace,
                'formaEnlace'                  => $strFormaEnlace,
                'boolMostrarCambioTipoMedio'   => $boolMostrarCambioTipoMedio,
                'frecuenciaProducto'           => $dato->getFrecuenciaProducto(),
                'mesesRestantes'               => $dato->getMesesRestantes(),
                'banderaAnulacionOrdenTrabajo' => $banderaAnulacionOrdenTrabajo,
                'id_factibilidad'              => $idDetalleSolicitud,
                'tercializadora'               => $tercializadora,
                'cliente'                      => $cliente,
                'login2'                       => $login2,
                'ciudad'                       => $ciudad,
                'direccion'                    => $direccion,
                'nombreSector'                 => $nombreSector,
                'esRecontratacion'             => $esRecontratacion,
                'producto'                     => $producto,
                'tipo_orden'                   => $tipo_orden,
                'tipoOrdenServicio'            => $strTipoOrden,
                'telefonos'                    => $telefonos,
                'observacion'                  => $observacion,
                'linkVerCliente'               => $urlVerCliente,
                'strFechaRenovacionServicio'   => $strFechaRenovacion,
                'boolMostrarAccionRenovacion'  => $boolMostrarAccionRenovacion,
                'precioTotal'                  => "$  " . number_format($fltPrecioTotal, 2),
                'anexoTecnico'                 => $tieneAnexo,
                'backup'                       => $intIdServicioBackUp,
                'nombre_vendedor'              => $strNombreEmpleado,
                'strClasificacion'             => $strClasificacion,
                'strRequiereBackup'            => $strRequiereBackup,
                'boolTieneComisionistas'       => $boolTieneComisionistas,
                'boolGeneraSolicitudVendedor'  => $boolGeneraSolicitudVendedor,
                'strContinuaFlujoNormal'       => $strContinuaFlujoNormal,
                'strSolucion'                  => $strSolucion,
                'esSolucion'                   => empty($strSolucion)?'N':'S',
                'strReqEnlaceDatos'            => isset($strReqEnlaceDatos) && !empty($strReqEnlaceDatos) ? $strReqEnlaceDatos : 'SI',
                'strOpcionEliminarServicio'    => isset($strOpcionEliminarServicio) && !empty($strOpcionEliminarServicio) ?
                                                  $strOpcionEliminarServicio : 'SI',
                'tieneOpcionMigracionFact'     => $strTieneOpcionMigracionDataFact,
                'tipoEsquema'                  => $objPlanificarService->getTipoEsquema($dato),
                'strFlagActivacion'            => $strFlagActivacion,
                'strFlagActiSimul'             => $strFlagActiSimul,
                'nombreProducto'               => method_exists($objProducto, 'getDescripcionProducto') ?
                                                  $objProducto->getDescripcionProducto() :
                                                  null,
                'boolMostrarBotonOSA'          => $boolMostrarBotonOSA,                
                'idPersona'                    => $dato->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                'latitud'                      => $dato->getPuntoId()->getLatitud(),
                'longitud'                     => $dato->getPuntoId()->getLongitud(),
                'intIdPuntoCobertura'          => $intIdPuntoCobertura,
                'intIdCanton'                  => $intIdCanton,
                'intIdParroquia'               => $intIdParroquia,
                'intIdSector'                  => $intIdSector,
                'intElementoEdificioId'        => $intElementoEdificioId,
                'strElementoEdificio'          => $strElementoEdificio,
                'strDependeDeEdificio'         => $strDependeDeEdificio,
                'boolTipoOrden'                => $boolTipoOrden,
                'strCodigoIns'                 => $strCodigoIns,
                'strCodigoMens'                => $strCodigoMens,
                'strCodigoBw'                  => $strCodigoBw,
                'strEditaDatosGeograficos'     => $strEditaDatosGeograficos,
                'strMetraje'                   => $strMetrajeC,
                'strModulo'                    => $strModuloC,
                'intPrecioFibra'               => $intPrecioFibra,
                'intMetrosDeDistancia'         => $intMetrosDeDistancia,
                'strSolExcedenteMaterial'      => $strSolExcedenteMaterial,
                'strMensaje'                   => $strMensajeC,
                'intIdFactibilidad'            => $intIdDetalleSolicitudFactibilidad,
                'floatValorCaractFibraMetros'  => $floatValorCaractFibraMetros,
                'floatValorCaractOCivil'       => $floatValorCaractOCivil,
                'floatValorCaractOtrosMateriales' => $floatValorCaractOtrosMateriales,
                'floatValorCaractCancPorCli'      => $floatValorCaractCancPorCli,
                'floatValorCaractAsumeCli'        => $floatValorCaractAsumeCli,
                'floatValorCaractAsumeEmpresa'    => $floatValorCaractAsumeEmpresa,
                'strEvidencia'                    => $strEvidencia,
                'arrayParametrosDet'              => $arrayParametrosDet,
                'strUuidPaquete'                  => $strUuidPaquete,
                'intPersonaEmpresaRolId'          => $intPersonaEmpresaRolId,
                'strValorProductoPaqHoras'        => $strValorProductoPaqHoras,
                'strValorProductoPaqHorasRec'     => $strValorProductoPaqHorasRec,
                'boolEsReplica'                   => $boolEsReplica      
            );

            $i++;
        endforeach;
        
        if(!empty($arreglo))
        {
            $response = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arreglo)));
        }
        else
        {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * 
     * Metodo encargado de obtener la informacion del Servicio principal que se desee crear un backup para replicar su informacion
     * dependiendo si es concentrador o simplemente utilizar parte de la informacion para transaccionar el nuevo servicio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 06-12-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 31-07-2017 Se obtiene informacion de la zona del backup directamente de su servicio principal mas no de la zona del punto
     * 
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.2 15-01-2018 Se recupera el login_aux del servicio.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetInformacionCrearServicioBackupAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $objResponse   = new JsonResponse();        
        $intIdServicio = $objRequest->get('idServicio');        
        $intIdBackup   = $objRequest->get('idServicioBackup');
        $emComercial   = $this->getDoctrine()->getManager('telconet');            
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objServicio   = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        
        $serviceTecnico                 = $this->get('tecnico.InfoServicioTecnico');
        $strZona                        = "";
        $arrayCaracteristicas           = array();
        $arrayCaracteristicasReferencia = array();
        $strNombreVendedor              = '';
        $strLoginVendedor               = '';
        $objPuntoFact                   = null;
        $boolMostrarCambioTipoMedio     = false;
        $intIdElementoPrincipal         = "";
        $intIdElementoBackup            = "";
        
        if(is_object($objServicio))
        {
            //Se obtiene la informacion de zona del servicio principal
            $objServProdCaract = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                    'Zona',
                                                                                    $objServicio->getProductoId());
            if(is_object($objServProdCaract))
            {
                $strZona = $objServProdCaract->getValor();
            }
            
            $objPunto     = $objServicio->getPuntoId();
            
            $objPuntoFact = $objServicio->getPuntoFacturacionId();                        
            
            $arrayProdCaracteristica  = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findByProductoIdyEstado($objServicio->getProductoId()->getId(), "Activo");
            
            
            
            $objAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                ->findOneBy(array( "descripcionFormaContacto" => "Correo Electronico",
                                                                   "estado"                   => 'Activo'));
            
            //Se recorre las caracteristicas y se verifica de acuerdo al tipo de producto que informacion se obtiene para ser enviado a crear 
            //el servicio Backup, esta información es utilizada para el calculo de las formulas de precio al momento de crear el nuevo
            //Servicio
            foreach($arrayProdCaracteristica as $objProdCaracteristica)
            {
                $strCaracteristica = $objProdCaracteristica->getCaracteristicaId()->getDescripcionCaracteristica();
                
                //Si se trata de la caracteristica de correo electronico se envia como parametro el valor del mismo
                if ($strCaracteristica=="CORREO ELECTRONICO")
                {
                    if(is_object($objAdmiFormaContacto))
                    {
                        $objPuntoFormaContacto =     $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                 ->findOneBy(array( "puntoId"         => $objPunto->getId(),
                                                                                    "formaContactoId" => $objAdmiFormaContacto->getId()));
                        if(is_object($objPuntoFormaContacto))
                        {
                            $arrayCaracteristicas["[CORREO ELECTRONICO]"] = $objPuntoFormaContacto->getValor();
                        }
                    }
                }
                //Si la caracteristica es TIENE INTERNET devuelve el parametro SI
                else if ($strCaracteristica == "TIENE INTERNET")
                {
                    $arrayCaracteristicas["[TIENE INTERNET]"] = "\"SI\"";
                }
                
                //Para los tipo de ingreso S se envia informacion referente a que Grupo de Negocio y Zona se refiere para enviar como parametro
                //en la creacion y calculo de nuevo precio
                if($objProdCaracteristica->getCaracteristicaId()->getTipoIngreso() == 'S' )
                {
                    if($strCaracteristica == 'Grupo Negocio')
                    {
                        $objGrupoNegocio= $emComercial->getRepository('schemaBundle:InfoPunto')->getGrupoNegocioByPuntoId($objPunto->getId());

                        if(is_object($objGrupoNegocio))
                        {
                            $arrayCaracteristicas["[Grupo Negocio]"] = $objGrupoNegocio->getGrupoNegocio();
                        }
                    }
                    else if($strCaracteristica == 'Zona')
                    {
                        $arrayCaracteristicas["[Zona]"] = $strZona;
                    }
                }
                
                $arrayCaracteristicasReferencia[] = array('idCaracteristica' => $objProdCaracteristica->getId(),
                                                          'caracteristica'   => $strCaracteristica
                                                         );
            }
            
            $objInfoPunto      = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objPunto->getId());
            
            if(is_object($objInfoPunto))                
            {            
                if($objInfoPunto->getUsrVendedor())
                {
                    $arrayParametros['EMPRESA'] = $objSession->get('idEmpresa');
                    $arrayParametros['LOGIN']   = $objInfoPunto->getUsrVendedor();            

                    $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

                    if($arrayResultado['TOTAL'] > 0)
                    {
                        $strLoginVendedor  = $arrayResultado['REGISTROS']['login'];
                        $strNombreVendedor = $arrayResultado['REGISTROS']['nombre'];
                    }          
                }
            }
        }
                
        $arrayInformacion = $emComercial->getRepository("schemaBundle:AdmiProducto")
                                        ->getArrayInformacionParaServicioBackup($intIdServicio);
        
        if(!empty($intIdBackup))
        {
            $servicioTecnicoPrincipal = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intIdServicio);
            if($servicioTecnicoPrincipal->getElementoClienteId() !== null)
            {
                //Verificar que el elemento cliente no sea directamente el CPE
                $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                 ->find($servicioTecnicoPrincipal->getElementoClienteId());
                if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                {
                    $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                    $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $servicioTecnicoPrincipal->getInterfaceElementoClienteId();

                    $arrayDatoCpe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                      ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                    if($arrayDatoCpe['msg'] == "FOUND")
                    {
                        $intIdElementoPrincipal = $arrayDatoCpe['idElemento'];
                    }
                }
            }
            $servicioTecnicoBackup = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intIdBackup);
            if($servicioTecnicoBackup->getElementoClienteId() !== null)
            {
                //Verificar que el elemento cliente no sea directamente el CPE
                $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                 ->find($servicioTecnicoBackup->getElementoClienteId());
                if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                {
                    $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                    $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $servicioTecnicoBackup->getInterfaceElementoClienteId();

                    $arrayDatoCpe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                      ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                    if($arrayDatoCpe['msg'] == "FOUND")
                    {
                        $intIdElementoBackup = $arrayDatoCpe['idElemento'];
                    }
                }
            }
            //Consultar si el elementoId(CPE) del principal y el backup son identicos es por que comparten el mismo CPE
            if ($intIdElementoPrincipal == $intIdElementoBackup)
            {
                $boolMostrarCambioTipoMedio = true;
            }
        }

        // Verificar que el servicio principal y backup no comparten el mismo CPE no visualizar la opción de seleccionar
        // el servicio que se va a realizar el cambio de tipo medio.

        $arrayInformacion['zona']               = $strZona;
        $arrayInformacion['caracteristicas']    = $arrayCaracteristicas;
        $arrayInformacion['login']              = $strLoginVendedor;
        $arrayInformacion['nombre']             = $strNombreVendedor;
        $arrayInformacion['loginAux']           = $objServicio->getLoginAux();
        $arrayInformacion['boolCambioMedio']    = $boolMostrarCambioTipoMedio;
        $arrayInformacion['refCaracteristicas'] = $arrayCaracteristicasReferencia;
        $arrayInformacion['loginPadreFact']     = is_object($objPuntoFact)?$objPuntoFact->getLogin():'';
        $arrayInformacion['idPadreFact']        = is_object($objPuntoFact)?$objPuntoFact->getId():0;

        $objResponse->setData($arrayInformacion);
        return $objResponse;
    }

    /**
     * Metodo utilizado para verificar si puede ser anulado un punto
     *
     * @param integer $idPunto (ajax)
     * @return response
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function permiteAnularPuntoAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $idPunto = $peticion->get('idPunto');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $strPermiteAnularPunto = $serviceInfoPunto->permiteAnularPtoCliente($idPunto);
        $respuesta->setContent($strPermiteAnularPunto);
        return $respuesta;
    }
    
    /**
     * Metodo utilizado para eliminar servicio via ajax
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @author Edgar Holguin<eholguin@telconet.ec>
     * @version 1.1 22-06-2016 Se eliminan solicitudes asociadas al servicio eliminado, se crea el respectivo historial.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 21-07-2016
     * Se agrega la eliminación de las relaciones del servicio backup
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 10-01-2017 Se agrega validación para liberar puertos de INTERNET WIFI
     * 
     * Se elimina codigo que borra la característica ES_BACKUP asociada a la admi_producto
     * cuando se elimina el servicio.
     * Se eliminan las características asociadas al servicio.
     * 
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.4 08-02-2017 
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 28-03-2017 Se agrega bloque de codigo que elimina las relaciones backup con el servicio principal
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 19-04-2017 - Se eliminan los registros asociados al servico de la tabla 'INFO_SERVICIO_COMISION'
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.7 22-06-2017 - Se agrega validación para liberar puertos de los productos internet dedicado y L3
     * 
     * @author John Vera R <javera@telconet.ec>
     * @version 1.8 15-08-2017 se procede a validar que cuando sea un concentrador verifique si tiene extremos
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 05-10-2017 Se agrega código para eliminar caracteristica SERVICIO_MISMA_ULTIMA_MILLA de todos los servicios que dependan
     *                         del servicio rechazado
     * @since 1.8
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 16-10-2017 Se agrega actualización de valor de caracteristica correo electronico para servicios de internet protegido
     * @since 1.9
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.11 07-05-2018- Se agrega que al momento de eliminar el servicio que se realice el proceso de reverso de la factura
     *                           de contrato digital, se debe validar que se genere NC de Reverso solo si no existe ya asociada una NC Activa. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 26-06-2018 Se agrega la liberación de la interface al eliminar un servicio Small Business
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.1
     * @since 23-10-2018
     * Se agrega la llamada a la función obtieneFacturasAGenerarNCxEliminarOS para obtener las facturas a las cuales se les debe generar NC.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.2 27-11-2018 Se agregan validaciones para poder gestionar servicios de la empresa TNP
     * @since 2.0
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.2 06-12-2018- Se pasa logica a service del proceso de reverso de la factura de contrato digital.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 04-02-2019 Se agregan validaciones para eliminar un servicio TELCOHOME
     * 
     * @author Josselhin Moreira <kjmoreia@telconet.ec>
     * @version 2.3 06-12-2018- Se agregan motivos para la eliminación del servicio.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.4 29-04-2020 Se agrega verificación de si el servicio necesita aprobación para enviar la notificación con número de cuentas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 23-04-2020 Se agrega el envío del parámetro objProductoPref en lugar del parámetro strNombreTecnicoPref a la función 
     *                          gestionarServiciosAdicionales, debido a los cambios realizados para la reestructuración de servicios Small Business
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.6 18-09-2020 - Se elimina servicio relacionado FastCloud para productos DirectLink-MPLS
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 15-04-2021 Se agrega la verificación de si es el último servicio adicional extender que se está eliminando para proceder 
     *                         a eliminar la solicitud agregar equipo por cambio a ONT V5.
     *                         Además se agrega eliminación de servicios extenders al eliminar el servicio de Internet
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.8 21-05-2021 - Se agrega validación para eliminar los servicios adicionales
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.9 26-05-2021 Se valida si el servicio es tipo de red GPON para ejecutar el método de liberarInterfaceSplitter
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.10 12-08-2021 Se agrega mensaje en caso de error al eliminar servicios adicionales parametrizados por eliminación de servicio
     *                          de Internet 
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.11 18-08-2021 - Se anexa validacion para que al eliminar un servicio de internet, se eliminen tambien
     *                           los servicios adicionales manuales y automaticos parametrizados.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.8 11-07-2021 - Se inactiva en la info adendum los servicios para MD 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.12 31-01-2022 Se modifica la programación para que al eliminar un extende rdual band se eliminen todas las solicitudes de cambio de
     *                          ont por extender que se encuentren gestionándose.
     *
     * @param integer $intMotivoId (ajax)
     * @param string $strObservacion (ajax)
     * @return response
     */
    public function delete_servicio_ajaxAction() 
    {
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em         = $this->getDoctrine()->getManager('telconet');
        $emGeneral  = $this->getDoctrine()->getManager("telconet_general");
        //Obtiene parametros enviados desde el ajax
        $peticion                = $this->get('request');
        $servicioId              = $peticion->get('idservicio');
        $intMotivoId             = $peticion->get('id_motivo');
        
        //Se agrega codigo para eliminacion de solicitud del servicio
        $intSolicitudId          = $peticion->get('id_solicitud');
        $prefijoEmpresa          = $session->get('prefijoEmpresa');
        $strCaracteristicaCorreo = "CORREO ELECTRONICO";
        $strValorAntesCorreo     = "";
        $strEstadoAntesCorreo    = "";        
        $strUsrCreacion          = $session->get('user');
        $strEmpresaCod           = $session->get('idEmpresa');
        $strIpCreacion           = $request->getClientIp();
        $intIdPersEmpreRol       = $session->get('idPersonaEmpresaRol');
        $intIdDepartamento       = $session->get('idDepartamento');
        $strError                = "Error al eliminar la orden de servicio:";
        $serviceUtil             = $this->get('schema.Util');
        $arrayParametroReversoNC = array();
        $serviceTecnico          = $this->get('tecnico.InfoServicioTecnico');
        $serviceCoordinar2       = $this->get('planificacion.Coordinar2');

        $em->getConnection()->beginTransaction();
        try 
        {
            $objInfoServicio = $em->getRepository('schemaBundle:InfoServicio')->find($servicioId);
            if(!is_object($objInfoServicio))
            { 
                throw new \Exception('No encontro el Servicio que se desea Eliminar');
            }
            $objInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($objInfoServicio->getPuntoId()->getId());
            if(!is_object($objInfoPunto))
            { 
                throw new \Exception('No encontro el Punto al cual pertenece el servicio que desea eliminar');
            }
            
            $objMotivoServicio = $em->getRepository('schemaBundle:AdmiMotivo')->findOneById($intMotivoId);
            if(!is_object($objMotivoServicio) && $objInfoServicio->getEstado() != 'Pre-servicio')
            {   
                throw new \Exception('No encontro el Motivo que desea consultar');
            }
            
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy(array("nombreParametro" => "MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR",
                                                           "estado"          => "Activo"));
            if (is_object($objParametroCab) && $objInfoServicio->getEstado() != 'Pre-servicio')
            {
                $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findBy(array("parametroId" => $objParametroCab,
                                                              "estado"      => "Activo"));
                
                if ($arrayParametroDet)
                {                                                
                    foreach ($arrayParametroDet as $parametroDet)
                    {
                        if ($parametroDet->getValor2() === $objMotivoServicio->getNombreMotivo() &&
                            $parametroDet->getValor1() === "S")
                        {
                            $arrayParametroReversoNC = array ('strPrefijoEmpresa'       => $prefijoEmpresa,
                                              'strEmpresaCod'           => $strEmpresaCod,
                                              'strUsrCreacion'          => $strUsrCreacion,
                                              'strIpCreacion'           => $strIpCreacion,                                              
                                              'strMotivo'               => $objMotivoServicio->getNombreMotivo(),                                              
                                              'objInfoPunto'            => $objInfoPunto,
                                              'objInfoServicio'         => $objInfoServicio);
                           
                            //Se realiza reverso de Facturas de Contrato digital.
                            $serviceNotaCredito  = $this->get('financiero.InfoNotaCredito');
                            $strMensajeReversoNC = $serviceNotaCredito->generarReversoFacturasContratoFisicoDigital($arrayParametroReversoNC);
                            if($strMensajeReversoNC)
                            {
                                throw new \Exception($strMensajeReversoNC);
                            }
                        }
                    }
                }
            }
            
            $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
            
            $objServicioRepository = $em->getRepository('schemaBundle:InfoServicio');
            $entity                = $objServicioRepository->find($servicioId);
            
            if (!$entity) 
            {
                throw $this->createNotFoundException('No se encontro el prospecto buscado');
            }
            //INACTIVA LA PERSONA
            $strEstadoActual = $entity->getEstado();
            $entity->setEstado('Eliminado');
            $em->persist($entity);
            $em->flush();

            //INACTIVA EN LA INFO ADENDUM
            if($strEmpresaCod == 18)
            {
                $objAdendum = $em->getRepository('schemaBundle:InfoAdendum')
                                 ->findOneBy(array("puntoId"    => $objInfoPunto->getId(),
                                                   "servicioId" => $objInfoServicio->getId()));

                $objAdendum->setEstado('Eliminado');
                $em->persist($objAdendum);
                $em->flush();
            }

            // Eliminamos todos los servicios adicionales indicados por la parametrizacion
            $objPlanServicio = $objInfoServicio->getPlanId();
            $objProdServicio = $objInfoServicio->getProductoId();
            if (!empty($objPlanServicio) && empty($objProdServicio))
            {
                $arrayDatosParametros = array(
                    "objServicio"     => $objInfoServicio,
                    "strEstado"       => "Eliminado",
                    "strObservacion"  => "Se elimina servicio adicional con servicio de internet",
                    "intCodEmpresa"   => $strEmpresaCod,
                    "strIpCreacion"   => $strIpCreacion,
                    "strUserCreacion" => $strUsrCreacion
                );
                $serviceCoordinar2->cancelarProdAdicionalesAut($arrayDatosParametros);

                $arrayDatosEliminar = array(
                    "idPunto"      => $objInfoServicio->getPuntoId()->getId(),
                    "idServicio"   => $objInfoServicio->getId(),
                    "estadoActual" => $strEstadoActual,
                    "estado"       => "Eliminado",
                    "observacion"  => "Se elimina el producto en simultaneo con el servicio de internet",
                    "usuario"      => $strUsrCreacion,
                    "ipCreacion"   => $strIpCreacion,
                    "idEmpresa"    => $strEmpresaCod,
                    "idPersonaRol"   => $intIdPersEmpreRol,
                    "idDepartamento" => $intIdDepartamento
                );
                $serviceCoordinar2->cancelacionSimulServicios($arrayDatosEliminar);
            }

            /**
             * Bloque que elimina la comisiones ingresadas en la tabla 'INFO_SERVICIO_COMISION'
             */
            $arrayInfoServicioComision = $em->getRepository('schemaBundle:InfoServicioComision')
                                            ->findBy( array('servicioId' => $entity, 'estado' => 'Activo') );
            
            if( !empty($arrayInfoServicioComision) )
            {
                foreach($arrayInfoServicioComision as $objInfoServicioComision)
                {
                    $objInfoServicioComision->setEstado('Eliminado');
                    $objInfoServicioComision->setUsrUltMod($session->get('user'));
                    $objInfoServicioComision->setFeUltMod(new \DateTime('now'));
                    $objInfoServicioComision->setIpUltMod($request->getClientIp());
                    $em->persist($objInfoServicioComision);
                    $em->flush();
                }//foreach($arrayInfoServicioComision as $objInfoServicioComision)
            }//( !empty($arrayInfoServicioComision) )
            
            if ($prefijoEmpresa == "TN" && is_object($objInfoServicio->getProductoId()))
            {
                /***OBTENER LOS SERVICIOS ADICIONALES PARA ELIMINAR***/
                $arrayParServAdd = array(
                    "intIdProducto"      => $objInfoServicio->getProductoId()->getId(),
                    "intIdServicio"      => $objInfoServicio->getId(),
                    "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                    "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
                );
                $arrayProdCaracConfProAdd  = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
                if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
                {
                    foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                    {
                        $objServicioConfProAdd = $em->getRepository('schemaBundle:InfoServicio')
                                                                ->find($arrayServicioConfProAdd['idServicio']);
                        if(is_object($objServicioConfProAdd))
                        {
                            $objServicioConfProAdd->setEstado('Eliminado');
                            $em->persist($objServicioConfProAdd);
                            $em->flush();
                            //vaciar servicio tecnico
                            $objServicioTecAdd = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findOneByServicioId($objServicioConfProAdd->getId());
                            if(is_object($objServicioTecAdd))
                            {
                                $objServicioTecAdd->setElementoId(null);
                                $objServicioTecAdd->setInterfaceElementoId(null);
                                $objServicioTecAdd->setElementoContenedorId(null);
                                $objServicioTecAdd->setElementoConectorId(null);
                                $objServicioTecAdd->setInterfaceElementoConectorId(null);
                                $objServicioTecAdd->setElementoClienteId(null);
                                $objServicioTecAdd->setInterfaceElementoClienteId(null);
                                $em->persist($objServicioTecAdd);
                                $em->flush();
                            }
                            //guardar historial del servicio adicional
                            $objSerHisConfProAdd = new InfoServicioHistorial();
                            $objSerHisConfProAdd->setServicioId($objServicioConfProAdd);
                            $objSerHisConfProAdd->setIpCreacion($request->getClientIp());
                            $objSerHisConfProAdd->setFeCreacion(new \DateTime('now'));
                            $objSerHisConfProAdd->setUsrCreacion($session->get('user'));
                            $objSerHisConfProAdd->setObservacion('Servicio eliminado por estar relacionado con el servicio '.
                                                                 $objInfoServicio->getProductoId()->getDescripcionProducto().
                                                                 ' que también fue eliminado.');
                            $objSerHisConfProAdd->setMotivoId($intMotivoId);
                            $objSerHisConfProAdd->setEstado($objServicioConfProAdd->getEstado());
                            $em->persist($objSerHisConfProAdd);
                            $em->flush();
                            //se elimina las solicitudes
                            $arrayDetSolServAdd = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                        ->findByServicioId($objServicioConfProAdd->getId());
                            foreach($arrayDetSolServAdd as $objDetSolServAdd)
                            {
                                $objDetSolServAdd->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud Usr: "
                                                                  .$session->get('user'));
                                $objDetSolServAdd->setEstado("Eliminada");
                                $em->persist($objDetSolServAdd);
                                $em->flush();
                                //guardar historial de la solicitud del servicio adicional
                                $objDetSolHistServAdd = new InfoDetalleSolHist();
                                $objDetSolHistServAdd->setDetalleSolicitudId($objDetSolServAdd);
                                $objDetSolHistServAdd->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud");
                                $objDetSolHistServAdd->setIpCreacion($peticion->getClientIp());
                                $objDetSolHistServAdd->setFeCreacion(new \DateTime('now'));
                                $objDetSolHistServAdd->setUsrCreacion($session->get('user'));
                                $objDetSolHistServAdd->setEstado('Eliminada');
                                $em->persist($objDetSolHistServAdd);
                                $em->flush();
                            }
                            // Se eliminan las caracteristicas asociadas al servicio
                            $arrayCaractServAdd = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                        ->findByServicioId($objServicioConfProAdd->getId());
                            foreach($arrayCaractServAdd as $objCaractServAdd)
                            {
                                $objCaractServAdd->setEstado('Eliminado');
                                $objCaractServAdd->setFeUltMod(new \DateTime('now'));
                                $objCaractServAdd->setUsrUltMod($session->get('user'));
                                $em->persist($objCaractServAdd);
                                $em->flush();
                            }
                            //se elimina las comisiones
                            $arrayComisionServAdd = $em->getRepository('schemaBundle:InfoServicioComision')
                                                            ->findBy(array('servicioId' => $objServicioConfProAdd, 'estado' => 'Activo'));
                            foreach($arrayComisionServAdd as $objComisionServAdd)
                            {
                                $objComisionServAdd->setEstado('Eliminado');
                                $objComisionServAdd->setUsrUltMod($session->get('user'));
                                $objComisionServAdd->setFeUltMod(new \DateTime('now'));
                                $objComisionServAdd->setIpUltMod($request->getClientIp());
                                $em->persist($objComisionServAdd);
                                $em->flush();
                            }
                        }
                    }
                }
                $serviceInfoServicio        = $this->get('comercial.InfoServicio');
                if($objInfoServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS")
                {
                    $arrayParamsAdicionales     = array("objServicioPref"           => $objInfoServicio,
                                                        "objProductoPref"           => $objInfoServicio->getProductoId(),
                                                        "strUsrCreacion"            => $session->get('user'),
                                                        "strObservacionServicio"    => 
                                                        "Se elimina el servicio por eliminación de servicio preferencial",
                                                        "strIpClient"               => $request->getClientIp(),
                                                        "strCodEmpresa"             => $strEmpresaCod,
                                                        "strNuevoEstadoCaracts"     => "Eliminado",
                                                        "strNuevoEstadoSol"         => "Eliminada",
                                                        "strObservacionSol"         => "Se realizó la eliminación de servicio por eliminación de "
                                                                                       ."servicio preferencial, y se da de baja a la solicitud"
                                                        );
                    $arrayRespuestaAdicionales  = $serviceInfoServicio->gestionarServiciosAdicionales($arrayParamsAdicionales);
                    if($arrayRespuestaAdicionales["strStatus"] !== "OK")
                    { 
                        throw new \Exception($arrayRespuestaAdicionales["strMensaje"] );
                    }
                }
                else if($objInfoServicio->getProductoId()->getNombreTecnico() === "TELCOHOME")
                {
                    $objSpcVelocidadTelcoHome   = $serviceServicioTecnico->getServicioProductoCaracteristica(   $objInfoServicio,
                                                                                                                'VELOCIDAD_TELCOHOME',
                                                                                                                $objInfoServicio->getProductoId()
                                                                                                            );
                    if(is_object($objSpcVelocidadTelcoHome))
                    {
                        $strVelocidadTelcoHome      = $objSpcVelocidadTelcoHome->getValor();
                        $arrayInfoVelocidadAutoriza = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne( 'PROD_VELOCIDAD_TELCOHOME', 
                                                                          '', 
                                                                          '', 
                                                                          '', 
                                                                          $strVelocidadTelcoHome,
                                                                          '', 
                                                                          'SI', 
                                                                          '', 
                                                                          '', 
                                                                          '');
                        if(!empty($arrayInfoVelocidadAutoriza))
                        {
                            $arrayParamsNotif       = array("objServicio"       => $objInfoServicio,
                                                            "strPrefijoEmpresa" => $prefijoEmpresa,
                                                            "strCodEmpresa"     => $strEmpresaCod,
                                                            "strAccion"         => "ELIMINAR",
                                                            "strUsrCreacion"    => $session->get('user'),
                                                            "strIpClient"       => $request->getClientIp(),
                                                            "strObservacion"    => "Se eliminó el servicio");
                            $arrayRespuestaNotif    = $serviceInfoServicio->gestionNotifTelcoHome($arrayParamsNotif);
                            if($arrayRespuestaNotif["strStatus"] !== "OK")
                            { 
                                throw new \Exception($arrayRespuestaNotif["strMensaje"] );
                            }
                        }
                    }
                }
            }

            //eliminar característica mascarilla
            if ($prefijoEmpresa == "TN" && is_object($objInfoServicio->getProductoId()))
            {
                $objAdmiCaractCamara      = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array('descripcionCaracteristica' => "RELACION_MASCARILLA_CAMARA_SAFECITY",
                                                                          'estado'                    => "Activo"));
                $objServicioProdCaractCam = $serviceTecnico->getServicioProductoCaracteristica($objInfoServicio,
                                                                                               'RELACION_MASCARILLA_CAMARA_SAFECITY',
                                                                                               $objInfoServicio->getProductoId());
                if(is_object($objAdmiCaractCamara) && is_object($objServicioProdCaractCam))
                {
                    $objServicioRelacionMasc = $em->getRepository('schemaBundle:InfoServicio')->find($objServicioProdCaractCam->getValor());
                    if(is_object($objServicioRelacionMasc))
                    {
                        $objProductoCaractCam = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array('caracteristicaId' => $objAdmiCaractCamara->getId(),
                                                                      'productoId'       => $objServicioRelacionMasc->getProductoId()->getId(),
                                                                      'estado'           => "Activo"));
                        if(is_object($objProductoCaractCam))
                        {
                            if($objServicioRelacionMasc->getProductoId()->getNombreTecnico() === "SERVICIOS-CAMARA-SAFECITY")
                            {
                                $arrayServicioProdCaractRel = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findByServicioId($objServicioRelacionMasc->getId());
                                foreach($arrayServicioProdCaractRel as $objServicioProdCaractRel)
                                {
                                    $objServicioProdCaractRel->setEstado('Eliminado');
                                    $objServicioProdCaractRel->setFeUltMod(new \DateTime('now'));
                                    $objServicioProdCaractRel->setUsrUltMod($session->get('user'));
                                    $em->persist($objServicioProdCaractRel);
                                    $em->flush();
                                }
                                //eliminar servicio
                                $objServicioRelacionMasc->setEstado('Eliminado');
                                $em->persist($objServicioRelacionMasc);
                                $em->flush();
                                //REGISTRA EN LA TABLA DE HISTORIAL
                                $objServicioHistorialCamRel = new InfoServicioHistorial();
                                $objServicioHistorialCamRel->setServicioId($objServicioRelacionMasc);
                                $objServicioHistorialCamRel->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorialCamRel->setUsrCreacion($session->get('user'));
                                $objServicioHistorialCamRel->setIpCreacion($request->getClientIp());
                                $objServicioHistorialCamRel->setObservacion('Servicio eliminado por estar relacionado con el servicio '.
                                                                            $objInfoServicio->getProductoId()->getDescripcionProducto().
                                                                            ' que también fue eliminado.');
                                $objServicioHistorialCamRel->setMotivoId($intMotivoId);
                                $objServicioHistorialCamRel->setEstado($objServicioRelacionMasc->getEstado());
                                $em->persist($objServicioHistorialCamRel);
                                $em->flush();
                            }
                            else
                            {
                                $objServicioProdCaractRel = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findOneBy(array('productoCaracterisiticaId' => $objProductoCaractCam->getId(),
                                                                                  'servicioId'                => $objServicioRelacionMasc->getId(),
                                                                                  'estado'                    => "Activo"));
                                if(is_object($objServicioProdCaractRel))
                                {
                                    $objServicioProdCaractRel->setEstado('Eliminado');
                                    $objServicioProdCaractRel->setFeUltMod(new \DateTime('now'));
                                    $objServicioProdCaractRel->setUsrUltMod($session->get('user'));
                                    $em->persist($objServicioProdCaractRel);
                                    $em->flush();
                                    //REGISTRA EN LA TABLA DE HISTORIAL
                                    $objServicioHistorialCam = new InfoServicioHistorial();
                                    $objServicioHistorialCam->setServicioId($objServicioRelacionMasc);
                                    $objServicioHistorialCam->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialCam->setUsrCreacion($session->get('user'));
                                    $objServicioHistorialCam->setIpCreacion($request->getClientIp());
                                    $objServicioHistorialCam->setObservacion('Se eliminó la característica de la mascarilla asociada.');
                                    $objServicioHistorialCam->setEstado($objServicioRelacionMasc->getEstado());
                                    $em->persist($objServicioHistorialCam);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            }

            //SE ELIMINA SOLICITUD DE ASIGNACION DE INFO TECNICA, PORQUE SEGUIA MOSTRANDOSE LA SOLICITUD EN LA OPCION DE ASIGNACION DE RECURSOS DE RED
            if ($intSolicitudId)
            {
                $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intSolicitudId);
                $entityDetalleSolicitud->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud");
                $entityDetalleSolicitud->setEstado("Anulado");
                $em->persist($entityDetalleSolicitud);
                $em->flush();
                
                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud");
                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalleSolHist->setEstado('Anulado');
                $em->persist($entityDetalleSolHist);
                $em->flush();
            }
            
            // Se eliminan solicitudes creadas para ese servicio
            
            $arrayInfoDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findByServicioId($servicioId);
            
            if(count($arrayInfoDetalleSolicitud)>0)
            {           
                foreach($arrayInfoDetalleSolicitud as $entityInfoDetalleSolicitud)
                {
                    $boolEliminaSolicitud = false;
                    if(is_object($entityInfoDetalleSolicitud->getTipoSolicitudId())
                        && $entityInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() === "SOLICITUD APROBACION SERVICIO")
                    {
                        $arrayParamsServiciosTelcoHome  = array(
                                                                    "intIdDetalleSolicitud"         => $entityInfoDetalleSolicitud->getId(),
                                                                    "arrayEstadosSolicitudes"       => array("Pendiente","Aprobada"),
                                                                    "strDescripcionCaracteristica"  => "VELOCIDAD_TELCOHOME",
                                                                    "strConServicio"                => "SI",
                                                                    "strBuscarServiciosAsociados"   => "SI",
                                                                    "arrayEstadosServiciosNotIn"    => array('Rechazado', 'Rechazada', 'Cancelado', 
                                                                                                             'Anulado', 'Cancel', 'Eliminado', 
                                                                                                             'Reubicado', 'Trasladado'),
                                                                    "intLimit"                      => 1
                                                                );  

                        $arrayRespuestaServiciosTelcoHome   = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                 ->getSolicitudesPorDetSolCaracts($arrayParamsServiciosTelcoHome);
                        $intTotalServiciosIngrTelcoHome     = $arrayRespuestaServiciosTelcoHome['intTotal'];
                        if($intTotalServiciosIngrTelcoHome > 0)
                        {
                            $arrayServicioTelcoHomeNuevo    = $arrayRespuestaServiciosTelcoHome["arrayResultado"][0];
                            $intIdServicioTelcoHomeNuevo    = $arrayServicioTelcoHomeNuevo["idServicioAsociado"];
                            $strLoginTelcoHomeNuevo         = $arrayServicioTelcoHomeNuevo["loginPuntoAsociado"];
                            $strLoginTelcoHomeSolicitud     = $arrayServicioTelcoHomeNuevo["loginPuntoSolicitud"];
                            $objInfoServicioTelcoHomeNuevo  = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicioTelcoHomeNuevo);
                            if(is_object($objInfoServicioTelcoHomeNuevo))
                            {
                                
                                $entityInfoDetalleSolicitud->setServicioId($objInfoServicioTelcoHomeNuevo);
                                $em->persist($entityInfoDetalleSolicitud);
                                $em->flush();
                                
                                $objDetalleSolHistTelcoHome = new InfoDetalleSolHist();
                                $objDetalleSolHistTelcoHome->setDetalleSolicitudId($entityInfoDetalleSolicitud);
                                $objDetalleSolHistTelcoHome->setObservacion("Se modifica automáticamente el servicio Telcohome "
                                                                            ."asociado a esta solicitud.<br>"
                                                                            ."Login Anterior: ".$strLoginTelcoHomeSolicitud."<br>"
                                                                            ."Login Nuevo: ".$strLoginTelcoHomeNuevo);
                                $objDetalleSolHistTelcoHome->setIpCreacion($peticion->getClientIp());
                                $objDetalleSolHistTelcoHome->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolHistTelcoHome->setUsrCreacion($strUsrCreacion);
                                $objDetalleSolHistTelcoHome->setEstado($entityInfoDetalleSolicitud->getEstado());
                                $em->persist($objDetalleSolHistTelcoHome);
                                $em->flush();
                            }
                        }
                        else
                        {
                            $boolEliminaSolicitud = true;
                        }
                    }
                    else
                    {
                        $boolEliminaSolicitud = true;
                    }
                    if($boolEliminaSolicitud)
                    {
                        $entityInfoDetalleSolicitud->setEstado('Eliminada');
                        $entityInfoDetalleSolicitud->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud Usr: "
                                                                    .$peticion->getSession()->get('user')
                                                                   );                
                        $em->persist($entityInfoDetalleSolicitud);
                        $em->flush();
                         //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityInfoDetalleSolHist = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHist->setDetalleSolicitudId($entityInfoDetalleSolicitud);
                        $entityInfoDetalleSolHist->setObservacion("Se realizo la eliminacion de servicio, y se da de baja a la solicitud");
                        $entityInfoDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityInfoDetalleSolHist->setEstado('Eliminada');
                        $em->persist($entityInfoDetalleSolHist);
                        $em->flush();
                    }
                }
            }

            //verificar si es servicio adicional
            $booleanServAdicional = false;
            if(is_object($objInfoServicio->getProductoId()))
            {
                $arrayValidarServAddGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY', 
                                                                 'COMERCIAL',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 'AGREGAR_SERVICIO_ADICIONAL',
                                                                 '',
                                                                 $objInfoServicio->getProductoId()->getId(),
                                                                 '',
                                                                 $strEmpresaCod);
                if( (isset($arrayValidarServAddGpon) && !empty($arrayValidarServAddGpon))
                    || $objInfoServicio->getProductoId()->getNombreTecnico() == 'SERVICIOS-CAMARA-SAFECITY' )
                {
                    $booleanServAdicional = true;
                }
            }

            if ((( $prefijoEmpresa == "MD"  || $prefijoEmpresa == "EN")&& is_object($objInfoServicio->getPlanId()))
                || (is_object($objInfoServicio->getProductoId()) 
                    && ($objInfoServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                        || $objInfoServicio->getProductoId()->getNombreTecnico() === "TELCOHOME")) 
                || ($prefijoEmpresa == "TNP" && is_object($objInfoServicio->getPlanId()))
               )
            {
                /* @var $serviceInterfaceElemento InfoInterfaceElementoService */
                $serviceInterfaceElemento       = $this->get('tecnico.InfoInterfaceElemento');
                $arrayRespuestaLiberaSplitter   = $serviceInterfaceElemento->liberarInterfaceSplitter(array("objServicio"       => $objInfoServicio,
                                                                                                            "strUsrCreacion"    => $strUsrCreacion,
                                                                                                            "strIpCreacion"     => $strIpCreacion,
                                                                                                            "strProcesoLibera"  => 
                                                                                                            " por eliminación del servicio"));
                $strStatusLiberaSplitter    = $arrayRespuestaLiberaSplitter["status"];
                $strMensajeLiberaSplitter   = $arrayRespuestaLiberaSplitter["mensaje"];
                if($strStatusLiberaSplitter === "ERROR")
                {
                    $em->getConnection()->rollback();
                    $em->getConnection()->close();
                    $respuesta->setContent($strMensajeLiberaSplitter);
                    return $respuesta;
                }
            }
            else if ($prefijoEmpresa == "TN" && !$booleanServAdicional)
            {
                $boolFlush = false;
                $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
                //verificar si es GPON el servicio
                $booleanTipoRedGpon = false;
                if(is_object($objInfoServicio->getProductoId()))
                {
                    $objCaractTipoRed = $serviceServicioTecnico->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                   "TIPO_RED",
                                                                                                   $objInfoServicio->getProductoId());
                    if(is_object($objCaractTipoRed))
                    {
                        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'VERIFICAR TIPO RED',
                                                                                            'VERIFICAR_GPON',
                                                                                            $objCaractTipoRed->getValor(),
                                                                                            '',
                                                                                            '',
                                                                                            '');
                        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                        {
                            $booleanTipoRedGpon = true;
                        }
                    }
                }
                //verifico si es GPON para liberar splitter
                if($booleanTipoRedGpon)
                {
                    $serviceInterfaceElemento     = $this->get('tecnico.InfoInterfaceElemento');
                    $arrayRespuestaLiberaSplitter = $serviceInterfaceElemento->liberarInterfaceSplitter(
                                                                            array("objServicio"      => $objInfoServicio,
                                                                                  "strUsrCreacion"   => $strUsrCreacion,
                                                                                  "strIpCreacion"    => $strIpCreacion,
                                                                                  "strVerificaLiberacion" => "SI",
                                                                                  "strPrefijoEmpresa"     => $prefijoEmpresa,
                                                                                  "booleanTipoRedGpon"    => $booleanTipoRedGpon,
                                                                                  "strProcesoLibera" => " por eliminación del servicio"));
                    $strStatusLiberaSplitter    = $arrayRespuestaLiberaSplitter["status"];
                    $strMensajeLiberaSplitter   = $arrayRespuestaLiberaSplitter["mensaje"];
                    if($strStatusLiberaSplitter === "ERROR")
                    {
                        throw new \Exception($strMensajeLiberaSplitter);
                    }
                }
                else
                {
                    //se reversa la factibilidad
                    $arrayParametros['intIdServicio']   = $servicioId;
                    $strMensaje = $serviceTecnico->reversaFactibilidad($arrayParametros);
                    if($strMensaje)
                    {
                        throw new \Exception($strMensaje);
                    }
                }

                // Eliminación de las relaciones backup con el servicio principal
                $entityInfoServicioProdCaract = $objServicioRepository->getResultadoBackupServicio($servicioId, true);
                
                if($entityInfoServicioProdCaract)
                {
                    $entityInfoServicioProdCaract->setEstado('Eliminado');
                    $entityInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                    $entityInfoServicioProdCaract->setUsrUltMod($peticion->getSession()->get('user'));

                    $em->persist($entityInfoServicioProdCaract);
                    $boolFlush = true;
                }
                                
                if($boolFlush)
                {
                    $em->flush();               
                }
                
                if(is_object($entity->getProductoId()))
                {
                    //si es wifi se liberan los puertos
                    if($entity->getProductoId()->getDescripcionProducto() == 'INTERNET WIFI')
                    {
                        $arrayParametros = array();
                        $arrayParametros['intIdServicio']   = $servicioId;
                        $arrayParametros['strUsrCreacion']  = $session->get('user');
                        $arrayParametros["strIpCreacion"]   = $request->getClientIp();                

                        $serviceInfoWifi = $this->get('tecnico.InfoElementoWifi');
                        $arrayResultado = $serviceInfoWifi->liberarPuertoWifi($arrayParametros);

                        if($arrayResultado['strStatus'] == 'ERROR')
                        {
                            $respuesta->setContent($arrayResultado['strMensaje']);
                            return $respuesta;
                        }
                    }
                    
                    //cuando el servicio es TN y es un producto concentrador se debe validar que no tenga extremos
                    if($entity->getProductoId()->getEsConcentrador() == 'SI')
                    {
                        $arrayResult = $serviceTecnico->getServiciosPorConcentrador($arrayParametros);
                        
                        if($arrayResult['strMensaje'])
                        {
                            if($arrayResult['strStatus'] == 'OK')
                            {
                                $respuesta->setContent('<b>No se puede Eliminar el servicio concentrador, debido a que tiene extremos enlazados:</b>'
                                                       . '<br><br>'.$arrayResult['strMensaje']);
                            }
                            else
                            {
                                $respuesta->setContent($arrayResult['strMensaje']);
                            }
                            return $respuesta;
                        }
                    }                    
                }
                
                //se procede a eliminar todas las caracteristicas SERVICIO_MISMA_ULTIMA_MILLA que dependan de este servicio
                $serviceTecnico->eliminarDependenciaMismaUM($entity, 
                                                            $session->get('user'),
                                                            $request->getClientIp());
            }
            
            $objAdmiProducto = $entity->getProductoId();
            
            if (is_object($objAdmiProducto))
            {
                if($objAdmiProducto->getNombreTecnico() === "EXTENDER_DUAL_BAND")
                {
                    $arrayRespuestaServiciosEdb = $serviceServicioTecnico->obtenerServiciosPorProducto(
                                                                            array(  "intIdPunto"                    => $objInfoPunto->getId(),
                                                                                    "arrayNombresTecnicoProducto"   => 
                                                                                    array("EXTENDER_DUAL_BAND"),
                                                                                    "strCodEmpresa"                 => $strEmpresaCod));
                    $intContadorServiciosEdb    = $arrayRespuestaServiciosEdb["intContadorServiciosPorProducto"];
                    if(intval($intContadorServiciosEdb) == 0)
                    {
                        $arrayRespuestaServInternetValido   = $serviceServicioTecnico->obtieneServicioInternetValido(array( "intIdPunto"    => 
                                                                                                                            $objInfoPunto->getId(),
                                                                                                                            "strCodEmpresa" => 
                                                                                                                            $strEmpresaCod
                                                                                                                  ));
                        $strStatusServInternetValido    = $arrayRespuestaServInternetValido["status"];
                        $objServicioInternetValido      = $arrayRespuestaServInternetValido["objServicioInternet"];
                        if($strStatusServInternetValido === "OK" && is_object($objServicioInternetValido))
                        {
                            $arrayRespVerifSolCambioDeOntXExtender  = $serviceServicioTecnico->verificaSolCambioDeOntPorServicioExtender(
                                                                                array(
                                                                                    "intIdServicioInternet" => 
                                                                                    $objServicioInternetValido->getId(),
                                                                                    "strCodEmpresa"         => 
                                                                                    $strEmpresaCod,
                                                                                    "strMotivoCambioOnt"    => 
                                                                                    "CAMBIO ONT POR AGREGAR EXTENDER"));
                            $strStatusVerifSolCambioDeOntXExtender      = $arrayRespVerifSolCambioDeOntXExtender["status"];
                            $strObtieneSolAgregarEquipoCambioOntParaExt = 
                                $arrayRespVerifSolCambioDeOntXExtender["strObtieneSolAgregarEquipoCambioOnt"];
                            $objSolAgregarEquipoAbiertaCambioOntParaExt = 
                                $arrayRespVerifSolCambioDeOntXExtender["objSolAgregarEquipoCambioOnt"];
                            if($strStatusVerifSolCambioDeOntXExtender === "OK" && $strObtieneSolAgregarEquipoCambioOntParaExt === "SI" 
                                && is_object($objSolAgregarEquipoAbiertaCambioOntParaExt))
                            {
                                $arrayDetsSolCaracts    = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                             ->findBy(array("detalleSolicitudId" => 
                                                                            $objSolAgregarEquipoAbiertaCambioOntParaExt->getId()));
                                if(isset($arrayDetsSolCaracts) && !empty($arrayDetsSolCaracts))
                                {
                                    foreach($arrayDetsSolCaracts as $objDetSolCaract)
                                    {
                                        $objDetSolCaract->setEstado("Eliminada");
                                        $objDetSolCaract->setUsrUltMod($strUsrCreacion);
                                        $objDetSolCaract->setFeUltMod(new \DateTime('now'));
                                        $em->persist($objDetSolCaract);
                                        $em->flush();
                                    }
                                }
                                $objSolAgregarEquipoAbiertaCambioOntParaExt->setEstado("Eliminada");
                                $em->persist($objSolAgregarEquipoAbiertaCambioOntParaExt);
                                $em->flush();
                            }
                        }
                    }
                }
                
                $booleanValidaProducto                = strpos($objAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO');
                $booleanValidaProductoProteccionTotal = strpos($objAdmiProducto->getDescripcionProducto(), 'I. PROTECCION');
                $objServProdCaractCorreo = $serviceServicioTecnico->getServicioProductoCaracteristica($entity, 
                                                                                                      $strCaracteristicaCorreo,
                                                                                                      $objAdmiProducto
                                                                                                     ); 
                if (is_object($objServProdCaractCorreo) &&
                    ($booleanValidaProducto !== false ||
                    $booleanValidaProductoProteccionTotal !== false))
                {
                    $strValorAntesCorreo  = $objServProdCaractCorreo->getValor();
                    $strEstadoAntesCorreo = $objServProdCaractCorreo->getEstado();
                    $objServProdCaractCorreo->setValor('');
                    $objServProdCaractCorreo->setEstado('Eliminado');
                    $objServProdCaractCorreo->setFeUltMod(new \DateTime('now'));
                    $objServProdCaractCorreo->setUsrUltMod($session->get('user'));
                    $em->persist($objServProdCaractCorreo);
                    $em->flush();

                    //REGISTRA EN LA TABLA DE HISTORIAL
                    $entityServicioHistorial = new InfoServicioHistorial();
                    $entityServicioHistorial->setServicioId($entity);
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $entityServicioHistorial->setUsrCreacion($session->get('user'));
                    $entityServicioHistorial->setIpCreacion($request->getClientIp());
                    $entityServicioHistorial->setObservacion('Se eliminó el servicio'); 
                    $entityServicioHistorial->setMotivoId($intMotivoId);
                    $entityServicioHistorial->setObservacion('Se actualizo caracteristica '.$strCaracteristicaCorreo.' con ID '.
                                                             $objServProdCaractCorreo->getId().' : <br>'.
                                                             'Valores Anteriores: <br>'.  
                                                             '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                             '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                             'Valores Actuales: <br>'.  
                                                             '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                             '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                    $entityServicioHistorial->setAccion('actualizaCaracteristica');
                    $entityServicioHistorial->setEstado($entity->getEstado());
                    $em->persist($entityServicioHistorial);
                    $em->flush();
                }
            }
            
            // Se eliminan las caracteristicas asociadas al servicio
            $arrayInfoServicioProdCaract = $em->getRepository('schemaBundle:InfoServicioProdCaract')->findByServicioId($servicioId);
            
            if(count($arrayInfoServicioProdCaract)>0)
            {           
                foreach($arrayInfoServicioProdCaract as $objInfoServicioProdCaract)
                {
                    $objAdmiProductoCaract = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                            ->find($objInfoServicioProdCaract->getProductoCaracterisiticaId());
                    if(is_object($objAdmiProductoCaract))
                    {
                        $objCaracteristicaRel  = $objAdmiProductoCaract->getCaracteristicaId();
                        if( $objCaracteristicaRel->getDescripcionCaracteristica() == 'RELACION_FAST_CLOUD' )
                        {
                            $objInfoServicioRel = $em->getRepository('schemaBundle:InfoServicio')->find($objInfoServicioProdCaract->getValor());
                            if(is_object($objInfoServicioRel))
                            {
                                $objInfoServicioRel->setEstado('Eliminado');
                                $em->persist($objInfoServicioRel);
                                //se agrega historial de eliminación del servicio
                                $objServicioHistorialRel = new InfoServicioHistorial();
                                $objServicioHistorialRel->setServicioId($objInfoServicioRel);
                                $objServicioHistorialRel->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorialRel->setUsrCreacion($session->get('user'));
                                $objServicioHistorialRel->setIpCreacion($request->getClientIp());
                                $objServicioHistorialRel->setMotivoId($intMotivoId);
                                $objServicioHistorialRel->setObservacion('Se eliminó el servicio');
                                $objServicioHistorialRel->setEstado($objInfoServicioRel->getEstado());
                                $em->persist($objServicioHistorialRel);
                            }
                        }
                    }
                    $objInfoServicioProdCaract->setEstado('Eliminado');
                    $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                    $objInfoServicioProdCaract->setUsrUltMod($session->get('user'));
                    $em->persist($objInfoServicioProdCaract);
                }
                $em->flush();
            }
            
            //REGISTRA EN LA TABLA DE HISTORIAL
            $entityServicioHistorial = new InfoServicioHistorial();
            $entityServicioHistorial->setServicioId($entity);
            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
            $entityServicioHistorial->setUsrCreacion($session->get('user'));
            $entityServicioHistorial->setIpCreacion($request->getClientIp()); //$intMotivoId
            $entityServicioHistorial->setMotivoId($intMotivoId);
            $entityServicioHistorial->setObservacion('Se eliminó el servicio');
            $entityServicioHistorial->setEstado($entity->getEstado());
            $em->persist($entityServicioHistorial);
            $em->flush();
            
            $em->getConnection()->commit();
            $strStatusPrincipal = "OK";
            $strMensajeEliminacion = "Se eliminó el Servicio con éxito.";
        } catch (\Exception $e) 
        {
            $strStatusPrincipal = "ERROR";
            error_log($e->getMessage());
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $serviceUtil->insertError('Telcos', 'delete_servicio_ajaxAction', $strError . $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $strMensajeEliminacion = !is_null($strMensajeReversoNC) ? $strMensajeReversoNC :
                                                                      "Error al tratar de eliminar registro. Consulte con el Administrador.";
        }
        $strMensajeEliminacionAdicionales   = "";
        if($strStatusPrincipal === "OK" && $prefijoEmpresa == "MD" 
            && is_object($objInfoServicio) && is_object($objInfoServicio->getPlanId()))
        {
            try
            {
                $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
                //Eliminación de servicios simultáneamente
                $arrayRespuestaProdInternetEnPlan   = $serviceTecnico->obtieneProductoEnPlan(
                                                                                                array(  "intIdPlan"                 => 
                                                                                                        $objInfoServicio->getPlanId(),
                                                                                                        "strNombreTecnicoProducto"  => 
                                                                                                        "INTERNET"));
                $strProdInternetEnPlan              = $arrayRespuestaProdInternetEnPlan["strProductoEnPlan"];
                if($strProdInternetEnPlan === "SI")
                {
                    $arrayProdAsociadosAEliminar    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(  'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        'NOMBRES_TECNICOS_ELIMINACION_SIMULTANEA_X_INTERNET',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $strEmpresaCod);
                    if(isset($arrayProdAsociadosAEliminar) && !empty($arrayProdAsociadosAEliminar))
                    {
                        foreach($arrayProdAsociadosAEliminar as $arrayNombreTecnicoServicioAEliminar)
                        {
                            $arrayRespuestaAdicionales  = $serviceTecnico->cancelaOEliminaServiciosDualBand(
                                                                                array(  "intIdPunto"                    => $objInfoPunto->getId(),
                                                                                        "strNombreTecnicoProducto"      => 
                                                                                        $arrayNombreTecnicoServicioAEliminar["valor2"],
                                                                                        "intIdServicioUnicoACancelar"   => null,
                                                                                        "intIdServicioANoCancelar"      => null,
                                                                                        "strEliminaDataTecnica"         => "NO",
                                                                                        "strObsProcesoEjecutante"       => " por eliminación de "
                                                                                        ."servicio de Internet",
                                                                                        "strUsrCreacion"                => $session->get('user'),
                                                                                        "strIpCreacion"                 => $request->getClientIp()));
                            $strStatusAdicionales       = $arrayRespuestaAdicionales["status"];
                            if($strStatusAdicionales !== "OK")
                            {
                                $strMensajeEliminacionAdicionales   = "No se pudo ejecutar la eliminación de algún servicio asociado ".
                                                                      " al servicio principal de Internet";
                            }
                            
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                error_log("No se pudo ejecutar la eliminación de los servicios asociados al servicio principal ID_SERVICIO: ".$servicioId);
            }
        }
        $strMensajeEliminacion .= $strMensajeEliminacionAdicionales;
        $respuesta->setContent($strMensajeEliminacion);
        return $respuesta;
    }

    //obtiene los contactos que pertenecen al cliente
    public function getContactosClienteAction($idCli) {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $idServicio=$request->get("idserv");
        $session  = $request->getSession(); 
        $idEmpresa = $session->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');

        $resultado = $em->getRepository('schemaBundle:InfoPersonaContacto')->findPorCliente($idEmpresa,$idCli,$limit,$page,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        //print_r($contactosAsignadosActuales);die;
        $i = 1;
        foreach ($datos as $dato):
            $contactosAsignadosActuales = $em->getRepository('schemaBundle:InfoContactoServicio')->findPorServicioPorPersonaContacto($idServicio,$dato->getId());
            if($contactosAsignadosActuales)
                $ingresado=true;
            else
                $ingresado=false;
            
            $arreglo[] = array(
                    'idPersonaContacto' => $dato->getId(),
                    'idPersona' => $dato->getContactoId()->getId(),
                    'nombres'=>$dato->getContactoId()->getNombres(),
                    'apellidos' => $dato->getContactoId()->getApellidos(),
                    'estado' => $dato->getEstado(),
                    'ingresado'=>$ingresado
                );
            
            $i++;
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'contactos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'contactos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } 
    
    public function asignaContactosAServiciosAction() {
        $request = $this->getRequest();
        $session  = $request->getSession(); 
        $idEmpresa = $session->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');
        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('contactos');
        $idServicio = $peticion->get('servicio');
        $array_contactos=explode(',',$parametro);
        //$respuesta->setContent(count($array_contactos)." Serv:".$idServicio);
        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_contactos as $id):
                $entity = new InfoContactoServicio();
                $entityPersonaContacto=$em->getRepository('schemaBundle:InfoPersonaContacto')->find($id);
                $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $entity->setPersonaContactoId($entityPersonaContacto);
                $entity->setServicioId($entityServicio);
                $entity->setIpCreacion($request->getClientIp());
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($session->get('user')); 
                $entity->setEstado('Activo');
                $em->persist($entity);
                $em->flush();
            endforeach;
            $em->getConnection()->commit();

           $response = new Response(json_encode(array('success' =>true ))); 

        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent("error al tratar de ingresar registros. Consulte con el Administrador.");
            $response = new Response(json_encode(array('success' =>false )));            
 
        }
        
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    //obtiene los contactos que pertenecen al servicio
    public function getContactosServicioAction() {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $idServicio=$request->get("idserv");
        $session  = $request->getSession(); 
        $idEmpresa = $session->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');

        $datos = $em->getRepository('schemaBundle:InfoContactoServicio')->findPorServicio($idServicio);        
        //print_r($contactosAsignadosActuales);die;
        $i = 1;
        foreach ($datos as $dato): 
            
            $arrayFormasContacto=$em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($dato->getPersonaContactoId()->getContactoId()->getId(),'Activo',100000,1,0);
            $entityPersonaFormasContacto=$arrayFormasContacto['registros'];
            $entityPersonaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($dato->getPersonaContactoId()->getContactoId()->getId(), 'Contacto',$idEmpresa);
            $entityRol=$em->getRepository('schemaBundle:AdmiRol')->find($entityPersonaEmpresaRol->getEmpresaRolId()->getRolId());
            foreach($entityPersonaFormasContacto as $dato1):
            $arreglo[] = array(
                    'idPersonaContacto' => $dato->getId(),
                    'idPersona' => $dato->getPersonaContactoId()->getContactoId()->getId(),
                    'nombres'=>$dato->getPersonaContactoId()->getContactoId()->getNombres(),
                    'apellidos' => $dato->getPersonaContactoId()->getContactoId()->getApellidos(),
                    'contacto' => $entityRol->getDescripcionRol().": ".$dato->getPersonaContactoId()->getContactoId()->getNombres()." ".$dato->getPersonaContactoId()->getContactoId()->getApellidos(),
                    'estado' => $dato->getPersonaContactoId()->getContactoId()->getEstado(),
                    'idPersonaFormaContacto' => $dato1->getId(),
                    'formaContacto' => $dato1->getFormaContactoId()->getDescripcionFormaContacto(),
                    'valor' => $dato1->getValor()
                );
            endforeach;
            $i++;
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('contactos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('contactos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
        //funcion para dashboard
	public function ajaxPtosClientesPtoCoberturaMesAction()
	{
		$request = $this->getRequest();
		$session  = $request->getSession();
                $em = $this->get('doctrine')->getManager('telconet');				
		$codigoEstado=$request->query->get('est');
		
		$fechaActual=date('l Y');
		$fechaActual="1 ".$fechaActual;
		$fechaComparacion = strtotime($fechaActual);
                $calculo= strtotime("31 days", $fechaComparacion); //Le aumentamos 31 dias
                $fechaFin= date("Y-m-d", $calculo);
                $fechaIni= date('Y-m')."-01";
		$PtosCobertura= $em->getRepository('schemaBundle:InfoPunto')->findPtosAgrupadosPorPuntosCobertura($session->get('idEmpresa'),$fechaIni,$fechaFin);
				
		foreach($PtosCobertura as $dato){	
			$arreglo[]= array(
				'name'=> sprintf("%s",$dato['puntoCobertura']),
				'data1'=> sprintf("%s",$dato['total'])
				);  
		}	
		if (empty($arreglo)){
			$arreglo[]= array(
				'name'=> "",
				'data1'=> ""
				);  
		}
		$response = new Response(json_encode(array('puntosCobertura'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;	
	}

        //funcion para dashboard
	public function ajaxPtosClientesTipoNegocioMesAction()
	{
		$request = $this->getRequest();
		$session  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet');				
		$codigoEstado=$request->query->get('est');
		
		$fechaActual=date('l Y');
		$fechaActual="1 ".$fechaActual;
		$fechaComparacion = strtotime($fechaActual);
    $calculo= strtotime("31 days", $fechaComparacion); //Le aumentamos 31 dias
    $fechaFin= date("Y-m-d", $calculo);
	$fechaIni= date('Y-m')."-01";
$TiposNegocio=$em->getRepository('schemaBundle:InfoPunto')->findPtosClienteAgrupadosPorTipoNegocio($session->get('idEmpresa'),$fechaIni,$fechaFin);
				
		foreach($TiposNegocio as $dato){	
			$arreglo[]= array(
				'name'=> sprintf("%s",$dato['tipoNegocio']),
				'data1'=> sprintf("%s",$dato['total'])
				);  
		}	
		if (empty($arreglo)){
			$arreglo[]= array(
				'name'=> "",
				'data1'=> ""
				);  
		}
		$response = new Response(json_encode(array('tiposNegocio'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;	
	}        

    /**
     * Documentación para el método 'ajaxGetTotalPtosAction'.
     *
     * Obtiene la cantidad total de puntos del cliente.
     * 
     * @author  Alejandro Domínguez Vargas <adominguez@telconet.ec>       
     * @version 1.1 27-06-2016
     * @since   1.0
     * Se modifica el manejo del dato con la cantidad de puntos del cliente
     */
    public function ajaxGetTotalPtosAction($id)
	{
        $request   = $this->getRequest();
        $session   = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $em        = $this->get('doctrine')->getManager('telconet');
        $totalPtos = $em->getRepository('schemaBundle:InfoPunto')->findTotalPtosCliente($id, $idEmpresa);
        
        $arreglo[] = array('total' => intVal($totalPtos));
        
        $response = new Response(json_encode(array('total_ptos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function asignarPadreAHijoAction($id,$idper){
        $em = $this->getDoctrine()->getManager();
        $estado="Activo";
        $request = $this->getRequest();
        $session  = $request->getSession(); 
        $idEmpresa = $session->get('idEmpresa'); 
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $formFormaPago=null;
        $deleteForm = $this->createDeleteForm($id);
        $entityContrato = $em->getRepository('schemaBundle:InfoContrato')->findContratosPorEmpresaPorEstadoPorPersona($estado,$idEmpresa,$id);
        //print_r($entityContrato);die;
        if($entityContrato){
            if($entityContrato->getFormaPagoId()->getDescripcionFormaPago()!="Efectivo")
            {
                //Busco por id y por estado -- falta por estado
                $formFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findPorContratoIdYEstado($id,$estado);
            }        
        }
        return $this->render('comercialBundle:infopunto:asignarPadreAHijo.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
                    'contrato'=> $entityContrato,
                    'formFormaPago'=>$formFormaPago,
					'idper'=>$idper
                ));       
    }
    /**
    * asignarPadre_ajaxAction, Asigna un nuevo  padre de facturacion a un dterminado servicio.
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 23-11-2016  Se agrega registro de historial del servicio asociado con la asignación de un nuevo padre de facturación.
    * @since 1.0
    */    
    public function asignarPadre_ajaxAction() 
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession(); 
        $idEmpresa      = $objSession->get('idEmpresa'); 
        $strUsrCreacion = $objSession->get('user');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion    = $this->get('request');
        $parametro   = $peticion->get('param');
        $padre       = $peticion->get('padre');
        $array_valor = explode("|", $parametro);

        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_valor as $id):
                $objInfoServicio = $em->getRepository('schemaBundle:InfoServicio')->find($id);
                if (!is_object($objInfoServicio))
                {
                    throw $this->createNotFoundException('No se encontro el punto buscado');
                }
                $objPuntoFacturacionActual = $objInfoServicio->getPuntoFacturacionId();
                
                $objPuntoFacturacionNuevo = $em->getRepository('schemaBundle:InfoPunto')->find($padre);
                $objInfoServicio->setPuntoFacturacionId($objPuntoFacturacionNuevo);
                $em->persist($objInfoServicio);
                $em->flush();
                
                $strObservacion = "";
                
                if(is_object($objPuntoFacturacionActual) && is_object($objPuntoFacturacionNuevo))
                {
                    $strObservacion = " Cambio de punto de facturacion. Anterior: ".$objPuntoFacturacionActual->getLogin().
                                      " Nuevo : ".$objPuntoFacturacionNuevo->getLogin();
                }
                
                $objInfoServicioHistorial = new InfoServicioHistorial();
                $objInfoServicioHistorial->setServicioId($objInfoServicio);
                $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                $objInfoServicioHistorial->setObservacion($strObservacion);
                $objInfoServicioHistorial->setAccion('asignarPadreFacturacion');
                $em->persist($objInfoServicioHistorial); 
                $em->flush();
                
            endforeach;
            $em->getConnection()->commit();
            $objResponse->setContent("Se agrego el registros con exito.");
        }catch (\Exception $e) 
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $objResponse->setContent($e->getMessage());
        }
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getAjaxComboVendedoresAction'.
     *
     * Método que obtiene el listado de los empleados por empresa que tienen característica ES_VENDEDOR
     * 
     * @return Response Lista de Vendedores.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 14-04-2016
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.2 09-05-2016
     * Se agregan departamentos para el filtrado de vendedores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.3 10-05-2016
     * Se agrega el filtrado LIKE de los departamentos AGENCIA.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.4 29-05-2016
     * Se quita la definición del filtro de los departamentos de los cuales podrán realizar ventas, se centraliza en InfoPersonaRepository.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 26-04-2017 - Se valida que cuando la empresa sea TN se busque al personal que contenga la caracteristica de 'VENDEDOR'
     * 
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.6 22-11-2018 Se realiza cambio para quela consulta de vendedores se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente aparecerá los vendedores asignados al asistente
     *                         en caso de ser vendedor aparecerá solo el
     *                         en caso de ser subgerente aparecerá los vendedores que reportan al subgerente
     *                         en caso de ser gerente aparecerá u otro cargo, no aplican los cambios
     *
     * @author : Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.7 29-11-2019 Implementación para obtener vendedores independientemente del estado, ya sea por salida de la
     *                         empresa o cambio de cargo.
     *
     */
    public function getAjaxComboVendedoresAction()
    {
        $objPeticion                = $this->getRequest();
        $objSesion                  = $objPeticion->getSession();
        $strPrefijoEmpresa          = $objSesion->get('prefijoEmpresa');
        $strCodEmpresa              = $objSesion->get('idEmpresa');
        $intIdPersonaEmpresaRol     = $objSesion->get('idPersonaEmpresaRol');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $serviceUtilidades          = $this->get('administracion.Utilidades');
        $strUsuarioSession          = $objSesion->get('user');
        $strIpCreacion              = $objPeticion->getClientIp();
        $strJsonResultado           = '{"total":"0", "registros":null}';
        $arrayVendedores            = array();
        $strTipoPersonal            = 'Otros';
        $arrayParametros['EMPRESA'] = $strCodEmpresa;
        $arrayParametros['NOMBRE']  = strtoupper($objPeticion->get('query'));
        $arrayParametros['LIMIT']   = $objPeticion->get("limit");
        $arrayParametros['START']   = $objPeticion->get("start");
        $strFiltrarTodosEstados     = $objPeticion->get('strFiltrarTodosEstados', 'N');

        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuarioSession);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        if( $strPrefijoEmpresa == "TN" )
        {
            $arrayParametros                        = array();
            $arrayParametros['usuario']             = $intIdPersonaEmpresaRol;
            $arrayParametros['empresa']             = $strCodEmpresa;
            $arrayParametros['estadoActivo']        = 'Activo';
            $arrayParametros['caracteristicaCargo'] = 'CARGO_GRUPO_ROLES_PERSONAL';
            $arrayParametros['nombreArea']          = 'Comercial';
            $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');

            /**
             * BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
             */
            $arrayRolesNoIncluidos = array();
            $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                            'strValorRetornar'  => 'descripcion',
                                            'strNombreProceso'  => 'JEFES',
                                            'strNombreModulo'   => 'COMERCIAL',
                                            'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                            'strUsrCreacion'    => $strUsuarioSession,
                                            'strIpCreacion'     => $strIpCreacion );

            $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
            {
                foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                {
                    $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                }//foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )

                $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
            }//( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )

            /**
             * BLOQUE QUE BUSCA LOS ROLES PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
             */
            $arrayRolesIncluidos                       = array();
            $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

            $arrayResultadosRolesIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
            {
                foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                {
                    $arrayRolesIncluidos[] = $strRolIncluido;
                }//foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )

                $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
            }//( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )

            /**
             * SE VALIDA QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
             */
            $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                  'strValorRetornar'  => 'valor1',
                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                  'strNombreModulo'   => 'COMERCIAL',
                                                  'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                  'strValor2Detalle'  => 'COMERCIAL',
                                                  'strUsrCreacion'    => $strUsuarioSession,
                                                  'strIpCreacion'     => $strIpCreacion);

            $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

            if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
            {
                $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
            }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )

            /**
             * SE OBTIENE EL CARGO DE VENDEDOR DEL PARAMETRO 'GRUPO_ROLES_PERSONAL'
             */
            $arrayParametrosCargoVendedor = array('strCodEmpresa'     => $strCodEmpresa,
                                                  'strValorRetornar'  => 'id',
                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                  'strNombreModulo'   => 'COMERCIAL',
                                                  'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                  'strValor3Detalle'  => 'VENDEDOR',
                                                  'strUsrCreacion'    => $strUsuarioSession,
                                                  'strIpCreacion'     => $strIpCreacion);

            $arrayResultadosCargoVendedor = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVendedor);

            if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
            {
                foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdCargoVendedor )
                {
                    $arrayParametros['criterios']['cargo'] = $intIdCargoVendedor;
                }//foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdDepartamento )
            }//( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
            $arrayParametros['strPrefijoEmpresa']       = $strPrefijoEmpresa;
            $arrayParametros['strTipoPersonal']         = $strTipoPersonal;
            $arrayParametros['intIdPersonEmpresaRol']   = $intIdPersonaEmpresaRol;
            $arrayParametros['strFiltrarTodosEstados']  = $strFiltrarTodosEstados;

            $arrayPersonalVendedor = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->findPersonalByCriterios($arrayParametros);
            
            if( isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros']) 
                && isset($arrayPersonalVendedor['total']) && $arrayPersonalVendedor['total'] > 0 )
            {
                foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                {
                    $strNombreVendedor      = ( isset($arrayVendedor['nombres']) && !empty($arrayVendedor['nombres']) )
                        ? ucwords(strtolower($arrayVendedor['nombres'])).' ' : '';
                    $strNombreVendedor      .= ( isset($arrayVendedor['apellidos']) && !empty($arrayVendedor['apellidos']) )
                        ? ucwords(strtolower($arrayVendedor['apellidos'])) : '';
                    $strLoginVendedor       = ( isset($arrayVendedor['login']) && !empty($arrayVendedor['login']) )
                        ? $arrayVendedor['login'] : '';
                    $intIdPersona           = ( isset($arrayVendedor['id']) && !empty($arrayVendedor['id']) )
                        ? $arrayVendedor['id'] : 0;
                    $intIdPersonaEmpresaRol = ( isset($arrayVendedor['idPersonaEmpresaRol']) && !empty($arrayVendedor['idPersonaEmpresaRol']) )
                        ? $arrayVendedor['idPersonaEmpresaRol'] : 0;

                    $arrayItemVendedor                           = array();
                    $arrayItemVendedor['nombre']                 = $strNombreVendedor;
                    $arrayItemVendedor['login']                  = $strLoginVendedor;
                    $arrayItemVendedor['intIdPersona']           = $intIdPersona;
                    $arrayItemVendedor['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
                    $arrayVendedores[]                           = $arrayItemVendedor;
                }//foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                
                $strJsonResultado = '{"total":"' . $arrayPersonalVendedor['total'] . '","registros":' . json_encode($arrayVendedores) . '}';
            }//( isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros'])...
        }
        else
        {
            $strJsonResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getJsonVendedoresPorEmpresa($arrayParametros);
        }//( $strPrefijoEmpresa == "TN" )
                
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent($strJsonResultado);
        return $response;	        
    }	
    
    /**
     * Documentación para el método 'getCanalesAction'.
     * 
     * Método para obtener la lista de canales disponibles.
     * 
     * Los Canales almacenan en los campos:
     * Valor1 => Identificador del Canal.
     * Valor2 => Descriptivo del Canal.
     * Valor3 => Identificador de grupo 'CANAL' .
     * 
     * @return Response Lista de Canales.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 11-12-2015
     */
    public function getCanalesAction()
    {
        $intEmpresaId   = $this->get('request')->getSession()->get('idEmpresa');
        $objManager     = $this->get('doctrine')->getManager('telconet');
        $strCanales     = 'CANALES_PUNTO_VENTA';
        $strModulo      = 'COMERCIAL';
        $strVal3        = 'CANAL';
        $listaCanales   = $objManager->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strCanales, $strModulo, '', '', '', '', $strVal3, '', '', $intEmpresaId);
        $arregloCanales = array();
        
        foreach($listaCanales as $entityCanal)
        {
            $arregloCanales[] = array('descripcion' => $entityCanal['valor2'], 'canal' => $entityCanal['valor1']);
        }
        sort($arregloCanales);
        $objResponse = new Response(json_encode(array('canales' => $arregloCanales)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getPuntosVentaAction'.
     * 
     * Método para obtener la lista de Puntos de venta por canal.
     * 
     * Los Puntos de Venta almacenan en los campos
     * Valor1 => Identificador del Punto de Venta.
     * Valor2 => Descriptivo del Punto de Venta.
     * Valor3 => Identificador del Canal.
     * Valor4 => Descriptivo del Canal.
     * Valor5 => Nombre de la oficina asociada al punto de venta.
     * 
     * @return Response Lista de Puntos de Venta.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 11-12-2015
     */
    public function getPuntosVentaAction()
    {
        $intEmpresaId       = $this->get('request')->getSession()->get('idEmpresa');
        $objManager         = $this->get('doctrine')->getManager('telconet');
        $strCanales         = 'CANALES_PUNTO_VENTA';
        $strModulo          = 'COMERCIAL';
        $strVal3            = $this->get('request')->get('canal');
        $arregloPuntosVenta = array();
        
        if($strVal3)
        {
            $listaPuntosVenta = $objManager->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get($strCanales, $strModulo, '', '', '', '', $strVal3, '', '', $intEmpresaId);
            foreach($listaPuntosVenta as $entityPuntoVenta)
            {
                $arregloPuntosVenta[] = array('descripcion' => $entityPuntoVenta['valor2'], 'punto_venta' => $entityPuntoVenta['valor1']);
            }
        }
        sort($arregloPuntosVenta); // Ordeno ascendentemente por descripción
        $objResponse = new Response(json_encode(array('puntos_venta' => $arregloPuntosVenta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    public function listaPtosCobertura_ajaxAction(){
        $request = $this->getRequest();
        $codEmpresa = $request->getSession()->get('idEmpresa');
        $nombre = $request->get('query');

        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $arreglo = $serviceInfoPunto->obtenerPuntosCobertura($codEmpresa, $nombre);

        $response = new Response(json_encode(array('jurisdicciones'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    public function listaCantonesJurisdiccion_ajaxAction(){
        $request = $this->getRequest();
        $idjurisdiccion = $request->get('idjurisdiccion');
        $nombre = $request->get('query'); 

        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $arreglo = $serviceInfoPunto->obtenerCantonesJurisdiccion($idjurisdiccion, $nombre);

        $response = new Response(json_encode(array('cantones'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	        
    }

    /**
     * solicitarFactibilidadAjaxAction, metodo que hace el llamado a los procesos de factibilidad.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 Se agrega validacion para la empresa TN
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 Se agrega filtrado de producto en consulta a tabla AdmiProductoCaracteristica
     * 
     * @author Alejandro Domínguez vargas<adominguez@telconet.ec>
     * @version 1.3 21-06-2016
     * Validación de producto L3MPLS: PRINCIPAL Requiere estar Enlazado y BACKUP No se necesita enlazar.
     * 
     * @author Alexander Samaniego
     * @version 1.4 27-06-2016 Se agrega estado para la busqueda del contacto tenico
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 15-09-2016 se estableció que se debe comparar por nombre tecnico del producto wifi
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 21-05-2018 Cuando el nombre tecnico es DATOSDC tambien se obliga a estar enlazado con un concentrador
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.7 14-05-2019 | Se modifica funcion "solicitarFactibilidadProducto" para que ahora requiera un
     * arreglo de de parametros, esto solo afecta al Producto Internet Wifi.
     *
     * @since 1.3
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.7 01-07-2019 Se ingresan Validaciones para el producto sdwan, se realiza substring al tipo de enlace
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.8 05-08-2019 Se valida que el producto L3MPLS SDWAN al igual que el L3MPLS se encuentre enlazado antes de
     *                         solicitar factibilidad. 
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.9 04-09-2019 Se valida que el producto DATOS DC SDWAN  se encuentre enlazado antes de
     *                         solicitar factibilidad.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.10 05-11-2019 Se valida si es un producto FWA y el concentrador virtual del mismo se encuentra en estado
     *                          Activo se pueda dar la factibilidad del mismo.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.0 18-09-2020 - Se agrega el parámetro si el producto necesita la configuración del enlace de datos para productos DirectLink-MPLS.
     */
    public function solicitarFactibilidadAjaxAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intId              = $objRequest->get('id');
        $intIdProducto      = $objRequest->get('idProducto');
        $strClienteIp       = $objRequest->getClientIp();
        $strUsrCreacion     = $objSession->get('user');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $arrayCliente       = $objSession->get('cliente');
        $arrayPuntoSession  = $objSession->get('ptoCliente');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
                   
        // Se procedera a crear la solicitud defactibilidad para TN si tiene contacto tecnico.
        if("TN" === $strPrefijoEmpresa)
        {
            $entityAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findByDescripcionCaracteristica("ENLACE_DATOS");
            //Pregunta si existe la caracteristica
            if($entityAdmiCaracteristica)
            {
                $entityAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array('descripcionCaracteristica' => "ENLACE_DATOS",
                                                                          'estado'                    => "Activo"
                                                                         )
                                                                   );
                //Pregunta si existe la caracteristica
                if($entityAdmiCaracteristica)
                {
                    $entityAdmiProductoCaracteristica = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array('caracteristicaId' => $entityAdmiCaracteristica->getId(),
                                                                                      'productoId'       => $intIdProducto,
                                                                                      'estado'           => "Activo"
                                                                                     )
                                                                               );
                    if($entityAdmiProductoCaracteristica)
                    {
                        $entityInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array('productoCaracterisiticaId' => $entityAdmiProductoCaracteristica->getId(),
                                                                       'servicioId'                => $intId,
                                                                       'estado'                    => "Activo"));
                    }
                }
            }

            $entityInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);

            //Pregunta si existe el servicio
            if($entityInfoServicio)
            {
                if($entityInfoServicio->getProductoId())
                {
                    $strReqEnlaceDatos = 'SI';
                    $boolEsPrincipal = false;
                    $boolSdwan       = false;
                    // Se obtiene el servicio técnico para validar el tipo de enlace.
                    $entityInfoServicioTec = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array('servicioId' => $entityInfoServicio));
                    if($entityInfoServicioTec && "PRINCIPAL" === substr($entityInfoServicioTec->getTipoEnlace(),0, 9))
                    {
                        // Solo si es principal se requiere enlazar el producto.
                        $boolEsPrincipal = true;
                    }
                    if ("L3MPLS SDWAN" === $entityInfoServicio->getProductoId()->getNombreTecnico() ||
                        "DATOS DC SDWAN" === $entityInfoServicio->getProductoId()->getNombreTecnico())
                    {
                        $boolSdwan = true;
                    }
                    //se obtiene el parametro si se configura el enlace de datos del producto
                    $arrayParametroEnlaceDatos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
                                                         'TECNICO',
                                                         '',
                                                         '',
                                                         $entityInfoServicio->getProductoId()->getId(),
                                                         'ENLACE_DATOS',
                                                         '',
                                                         '',
                                                         '',
                                                         $strCodEmpresa);
                    if( isset($arrayParametroEnlaceDatos) && !empty($arrayParametroEnlaceDatos) )
                    {
                        $strReqEnlaceDatos = $arrayParametroEnlaceDatos['valor3'];
                    }
                    //obtener tipo red
                    $objCaractTipoRed = $serviceTecnico->getServicioProductoCaracteristica($entityInfoServicio, 'TIPO_RED',
                                                                                           $entityInfoServicio->getProductoId());
                    if(is_object($objCaractTipoRed))
                    {
                        //se obtiene el parametro si se configura el enlace de datos del producto
                        $arrayParEnlaceDatosGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                             'COMERCIAL',
                                                             '',
                                                             '',
                                                             $entityInfoServicio->getProductoId()->getId(),
                                                             'ENLACE_DATOS',
                                                             $objCaractTipoRed->getValor(),
                                                             '',
                                                             '',
                                                             $strCodEmpresa);
                        if( isset($arrayParEnlaceDatosGpon) && !empty($arrayParEnlaceDatosGpon) )
                        {
                            $strReqEnlaceDatos = $arrayParEnlaceDatosGpon['valor4'];
                        }
                    }
                    //verifico si el servicio es requerido un enlace de datos
                    if(("L3MPLS" === $entityInfoServicio->getProductoId()->getNombreTecnico() ||
                        "DATOSDC" === $entityInfoServicio->getProductoId()->getNombreTecnico() ||
                        $boolSdwan) &&
                        "NO" === $entityInfoServicio->getProductoId()->getEsConcentrador() &&
                        $boolEsPrincipal &&
                        !$entityInfoServicioProdCaract &&
                        $strReqEnlaceDatos === "SI")
                    {
                        $strProducto = $entityInfoServicio->getProductoId()->getNombreTecnico();
                        $objResponse->setContent("No se creo la solicitud de factibilidad. <br> "
                                               . "Producto <b>".$strProducto."</b> debe estar previamente enlazado.");
                        return $objResponse;
                    }
                }

                if($arrayCliente)
                {
                    //Si no tiene cliente en sesion termina el metodo.
                    if(empty($arrayCliente['id_persona_empresa_rol']))
                    {
                        $objResponse->setContent("No tiene empresa rol TN.");
                        return $objResponse;
                    }

                    $arrayGetPersonaContacto = array();
                    $arrayGetPersonaContacto['strJoinPunto']    = '';
                    $arrayGetPersonaContacto['arrayTipoRol']    = ['arrayDescripcionTipoRol'    => ['Contacto']];
                    $arrayGetPersonaContacto['arrayEmpresaRol'] = ['arrayEmpresaCod'            => [$strCodEmpresa]];
                    $arrayGetPersonaContacto['arrayPerEmpRol']  = ['arrayEstadoPerEmpRol'       => ['Eliminado', 'Anulado', 'Inactivo'],
                                                                   'strComparadorEstPER'        => 'NOT IN'];
                    $arrayGetPersonaContacto['arrayRol']        = ['arrayDescripcionRol'        => ['Contacto Tecnico']];
                    $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPersonaEmpresaRol'   => [$arrayCliente['id_persona_empresa_rol']],
                                                                             'arrayPunto'               => [$arrayPuntoSession['id']],
                                                                             'arrayEstPerPuntoContacto' => ['Activo']];

                    //Busca contactos
                    $jsonData = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                            ->getJSONContactoClienteByTipoRol($arrayGetPersonaContacto);

                    $objJsonData = json_decode($jsonData);
                    //Si no encontro contacto tecnico termina el metodo.

                    if(0 === $objJsonData->total)
                    {
                        $arrayGetPersonaContacto['strJoinPunto']    = 'PUNTO';
                        $jsonData = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                ->getJSONContactoClienteByTipoRol($arrayGetPersonaContacto);
                        $objJsonData = json_decode($jsonData);
                        if(0 === $objJsonData->total)
                        {
                            $objResponse->setContent("No se creo la solicitud de factibilidad. <br> Cliente no tiene contacto tecnico.");
                            return $objResponse;
                        }
                    }
                }
            }
             
            
        }
        
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            $entityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);
            $nombreProducto = '';
            if($entityServicio)
            {
                $objProducto = $entityServicio->getProductoId();
                if($objProducto)
                {
                    $nombreProducto = $objProducto->getNombreTecnico();
                }
            }
            //valido que si es wifi debe entrar por un flujo diferente
            if($nombreProducto == 'INTERNET WIFI')
            {
                $arrayParams = array(
                    'intId'             =>  $intId,
                    'strUsrCreacion'    =>  $strUsrCreacion,
                    'strClienteIp'      =>  $strClienteIp,
                    'objRequest'        =>  $objRequest->getSession(),
                    'objPeticion'       =>  $this->get('request')
                );

                $strContent = $serviceInfoServicio->solicitarFactibilidadProducto($arrayParams);
                
            }
            else
            {
                if($nombreProducto === 'DATOS FWA')
                {
                    $boolConcentradorPorActivar = true;
                    //Consultar el concentrador virtual si esta activo.
                    if(is_object($entityServicio->getPuntoId()))
                    {
                        $objPersonaEmpresaRol = $entityServicio->getPuntoId()->getPersonaEmpresaRolId();
                        $intIdOfiServ         = is_object($entityServicio->getPuntoId()->getPuntoCoberturaId()) ?
                                                            $entityServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId() : 0;
                        $objOficina           = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                            ->find($intIdOfiServ);
                        if(is_object($objOficina))
                        {
                            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                              ->find($objOficina->getCantonId());
                            if(is_object($objCanton))
                            {
                                $strRegionServicio = $objCanton->getRegion();
                            }
                        }
                        if(is_object($objPersonaEmpresaRol))
                        {
                            //Consultar si tiene un concentrador virtual de INTERCONEXION
                            $objCaracConcentradorFWA = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array(
                                                                                      "descripcionCaracteristica" => 'CONCENTRADOR_FWA',
                                                                                      "estado"                    => 'Activo'
                                                                                    ));
                            $arrayParamConcentraInter= $emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                 ->getOne('CONCENTRADOR INTERCONEXION FWA',
                                                                          'COMERCIAL',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '');
                            if( isset($arrayParamConcentraInter['valor1']) && !empty($arrayParamConcentraInter['valor1']) )
                            {
                                $strNombreTecnico = $arrayParamConcentraInter['valor1'];
                            }
                            $objProductoConcinter    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                   ->findOneBy(array("nombreTecnico"   =>  $strNombreTecnico,
                                                                                     "estado"          =>  "Activo",
                                                                                     "esConcentrador"  =>  "SI",
                                                                                     "empresaCod"      =>  $strCodEmpresa));
                            $objAdmiProdCaract       = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy(array('caracteristicaId' => $objCaracConcentradorFWA,
                                                                                     'productoId'       => $objProductoConcinter,
                                                                                     'estado'           => "Activo"
                                                                                    )
                                                                               );

                            $arrayConcentradorVirtual= $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findBy(array('productoCaracterisiticaId' => $objAdmiProdCaract->getId(),
                                                                                  'servicioId'                => $entityServicio->getId(),
                                                                                  'estado'                    => "Activo"));
                            if(isset($arrayConcentradorVirtual) && !empty($arrayConcentradorVirtual))
                            {
                                foreach($arrayConcentradorVirtual as $objPerEmprRolCarac)
                                {
                                    $objServicioConcentradorVirtual = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                  ->findOneById($objPerEmprRolCarac->getValor());

                                    $intIdOfiServConcentra          = is_object($objServicioConcentradorVirtual->getPuntoId()
                                                                                                               ->getPuntoCoberturaId()) ?
                                                                        $objServicioConcentradorVirtual->getPuntoId()->getPuntoCoberturaId()
                                                                                                                     ->getOficinaId() : 0;
                                    $objOfiConcentrador             = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                                  ->find($intIdOfiServConcentra);
                                    if(is_object($objOfiConcentrador))
                                    {
                                        $objCantonConcentra = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                                          ->find($objOfiConcentrador->getCantonId());
                                        if(is_object($objCantonConcentra))
                                        {
                                            $strRegionConcentradorVirtual = $objCantonConcentra->getRegion();
                                        }
                                    }

                                    if($strRegionServicio == $strRegionConcentradorVirtual)
                                    {
                                        if($objServicioConcentradorVirtual->getEstado() == 'Activo')
                                        {
                                            $boolConcentradorPorActivar = false;
                                        }
                                        else
                                        {
                                            $boolConcentradorPorActivar = true;
                                        }
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                throw new \Exception("No existe la característica CONCENTRADOR_FWA, este servicio debe estar previamente enlazado "
                                                    . " a un concentrador.");
                            }
                        }
                    }
                    if($boolConcentradorPorActivar)
                    {
                        throw new \Exception("El concentrador virtual FWA no esta activo aún ");
                    }

                }
                $strContent = $serviceInfoServicio->solicitarFactibilidadServicio( $strCodEmpresa,
                                                                                $strPrefijoEmpresa, 
                                                                                $intId, 
                                                                                $strUsrCreacion, 
                                                                                $strClienteIp);
            }
        }

        catch(\Exception $e)
        {
            $strContent = 'Error: <br>' . $e->getMessage();
        }

        $objResponse->setContent($strContent);
        return $objResponse;
    } //solicitarFactibilidadAjaxAction

    /**
    * Funcion que descargar archivo Digitales 
    * 
    * @author Jorge Veliz <jlveliz@telconet.ec>
    * @since 20-06-2021
    * @version 1.0
    *
    */

    public function downloadAction($id)
    {
        $emInfoPunto = $this->getDoctrine()->getManager();
        $entityPunto = $emInfoPunto->getRepository('schemaBundle:InfoPunto')->find($id);
        $strRutaArchDigital = $entityPunto->getPathDigital();
        $strNuevoNombre = '';
        if(isset($strRutaArchDigital) && filter_var($strRutaArchDigital, FILTER_VALIDATE_URL))
        {
            $strPath = $entityPunto->getPathDigital();
            $strNuevoNombre = basename($strRutaArchDigital);
        }else
        {
            $strPath = $entityPunto->getAbsolutePathDigital();
            $strNuevoNombre = $strRutaArchDigital;
        }


        $arrayOpts = array(
            
            'http'=>array(
              'method'=>"GET",
              'header'=>"Accept-language: en\r\n" .
                        "Cookie: foo=bar\r\n"
            )
        );
          
        $objContext = stream_context_create($arrayOpts);

        $strContent = file_get_contents($strPath, false, $objContext);

 
        //echo "archivoDigital:".$entity->getPathDigital();
        //echo "path:".$path;die;
        $objResponse = new Response();

        //set headers
        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="'.$strNuevoNombre);


        $objResponse->setContent($strContent);
        return $objResponse;
    }   


    public function newArchivoDigitalAction($idPto)
    {		
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $em = $this->getDoctrine()->getManager("telconet");       
        $emComunicacion =   $this->get('doctrine')->getManager('telconet_comunicacion');
        $emGeneral     = $this->getDoctrine()->getManager("telconet_general");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "1");    	

        $entity = new InfoPunto();
        $form   = $this->createForm(new InfoPuntoArchivoDigitalType(array('validaFileDigital'=>true)), $entity);
        $entityPunto  = $em->getRepository('schemaBundle:InfoPunto')->find($idPto);
        $objDocumentosRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findBy(array("puntoId"=>$idPto));
        $arrayDocumentos = array();
        foreach($objDocumentosRelacion as $objDocRel)
        {
            $objDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($objDocRel->getDocumentoId());
            $objTipoDocumento = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->find($objDocumento->getTipoDocumentoGeneralId());
            $arrayDocumentos[] = array("id" => $objTipoDocumento->getId(), 
                                       "descripcion" => $objTipoDocumento->getDescripcionTipoDocumento(), 
                                       "fecha" => $objDocumento->getFeCreacion()->format('Y-m-d H:i:s'));
        }

        $objAdendum = $em->getRepository('schemaBundle:InfoAdendum')->findOneBy(array("puntoId" => $idPto));
        if ($objAdendum->getTipo() == "AP")
        {
            $objDocumentosRelacion2 = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                     ->findBy(array("numeroAdendum"=>$objAdendum->getNumero()));
        }
        if ($objAdendum->getTipo() == "C")
        {
            $objDocumentosRelacion2 = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                     ->findBy(array("contratoId"=>$objAdendum->getContratoId()));
        }
        if ($objDocumentosRelacion2)
        {
            foreach($objDocumentosRelacion2 as $objDocRel)
            {
                $objDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($objDocRel->getDocumentoId());
                $objTipoDocumento = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                              ->find($objDocumento->getTipoDocumentoGeneralId());
                $arrayDocumentos[] = array("id" => $objTipoDocumento->getId(), 
                                           "descripcion" => $objTipoDocumento->getDescripcionTipoDocumento(), 
                                           "fecha" => $objDocumento->getFeCreacion()->format('Y-m-d H:i:s'));
            }    
        }

    	$entityAdmiRol = $em->getRepository('schemaBundle:AdmiRol')->find($entityPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
        return $this->render('comercialBundle:infopunto:newArchivoDigital.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entityPunto,
            'form'   => $form->createView(),
            'idPto'  => $idPto,
            'rol' =>$entityAdmiRol->getTipoRolId()->getDescripcionTipoRol(),
            'prefijoEmpresa' => $strPrefijoEmpresa,
            'documentos' => $arrayDocumentos
        ));
    }     
    
     /**
    * Funcion que Subiry grabar en tabla archivo Digitales 
    * 
    * @author Jorge Veliz <jlveliz@telconet.ec>
    * @since 20-06-2021
    * @version 1.0
    *
    * @author Edgar Pin Villavicencio
    * @since 09-11-2022
    * @version 1.1 Se cambia el esquema de emComunicacion a em para hacer persist y flush en infoDocumento e infoDocumentoRelacion
    *
    * @author Alex Gómez <algomez@telconet.ec>
    * @since 25-11-2022
    * @version 1.2 Se añade validación por tipo de documento, previo carga del archivo
    */

    public function grabaSubirArchivoAction(Request $request) {
        //$peticion = $this->get('request');
        
        $serviceUtil = $this->get('schema.Util');
        $em = $this->getDoctrine()->getManager('telconet');
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $datos_form_extra=$request->request->get('infopuntoextratype');

        $entity  = $em->getRepository('schemaBundle:InfoPunto')->find($datos_form_extra['idpunto']);
        $entityAdmiRol = $em->getRepository('schemaBundle:AdmiRol')->find($entity->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
        //echo 'TpoRol:'.$entityAdmiRol->getTipoRolId()->getDescripcionTipoRol();
        //die;
        $objSession         = $request->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strCodEmpresa      = $objSession->get('idEmpresa');  
        $strLogin           = $objSession->get('user');

        $form = $this->createForm(new InfoPuntoArchivoDigitalType(array('validaFileDigital'=>true)), $entity);
        $form->handleRequest($request);


            $em->getConnection()->beginTransaction();
            try{

                if(empty($datos_form_extra['idTipoDocumento']))
                {
                    throw new \Exception('No se ha seleccionado el tipo del documento a cargar.');
                }
                
                //echo $entity->getFileDigital();die;
                if ($entity->getFileDigital()){
                    $entity->preUploadDigital();
                    $strNombreApp       = 'TelcosWeb';
                    $arrayPathAdicional = [];
                    $strSubModulo = "PuntoArchivoDigital";
                    $serviceTokenCas = $this->get('seguridad.TokenCas');
                    $arrayTokenCas = $serviceTokenCas->generarTokenCas();    
                    error_log("token " . json_encode($arrayTokenCas));     
                    $arrayParamNfs          = array(
                        'prefijoEmpresa'       => $strPrefijoEmpresa,
                        'strApp'               => $strNombreApp,
                        'strSubModulo'         => $strSubModulo,
                        'arrayPathAdicional'   => $arrayPathAdicional,
                        'strBase64'            => base64_encode(file_get_contents($entity->getFileDigital())),
                        'strNombreArchivo'     => $entity->getPathDigital(),
                        'strUsrCreacion'       => $strLogin,
                        'token'                => $arrayTokenCas['strToken']
                    );
                    
                    $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    
                    if(isset($arrayRespNfs))
                    {
                        if($arrayRespNfs['intStatus'] == 200)
                        {
                            $entity->setPathDigital($arrayRespNfs['strUrlArchivo']);
                            $entity->setFileDigital(null);
                            //guardo el archivo en info_documento e info_documento_relacion
                            $objContrato = $em->getRepository("schemaBundle:InfoContrato")
                                             ->findOneBy(array("personaEmpresaRolId" => $entity->getPersonaEmpresaRolId()->getId(),
                                                               "estado" => array('Activo', 'Pendiente', 'PorAutorizar')));
                            $intIdContrato = null;
                            $intNumAdendum = null;
                            $strNumeroContrato = "";
                            if ($objContrato)
                            {
                                $intIdContrato = $objContrato->getId();
                                $strNumeroContrato = $objContrato->getNumeroContrato();
                                $objAdendum = $em->getRepository("schemaBundle:InfoAdendum")
                                                  ->findOneBy(array("puntoId" => $entity->getId(), 
                                                                    "tipo" => "AP"));
                                if ($objAdendum)
                                {
                                    $intNumAdendum = $objAdendum->getNumero();     
                                }                               
                                
                            }

                            $objTipoDocumentoGeneral = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                 ->find($datos_form_extra['idTipoDocumento']);

                            $strNombreDocumento = "";
                            if( $objTipoDocumentoGeneral != null )
                            {   
                                $strNombreDocumento .= $objTipoDocumentoGeneral->getDescripcionTipoDocumento();
                            }
                            $strNombreDocumento .= "_" . $strNumeroContrato;
                            $strNombreDocumento = str_replace(" ", "_", $strNombreDocumento);
                            
                            $objDocumento = new InfoDocumento();
                            $objDocumento->setNombreDocumento($arrayRespNfs['strFileName']);
                            $objDocumento->setUbicacionLogicaDocumento($arrayRespNfs['strFileName']);
                            $objDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);
                            $objDocumento->setContratoId($intIdContrato);
                            $objDocumento->setUsrCreacion($strLogin);
                            $objDocumento->setIpCreacion("127.0.0.1");
                            $objDocumento->setEstado("Activo");
                            $objDocumento->setEmpresaCod($strCodEmpresa);
                            $objDocumento->setFeCreacion(new \DateTime('now'));
                            $objDocumento->setTipoDocumentoGeneralId($datos_form_extra['idTipoDocumento']); 
                            $em->persist($objDocumento);
                            $em->flush();

                            $objDocumentoRelacion = new InfoDocumentoRelacion();
                            $objDocumentoRelacion->setDocumentoId($objDocumento->getId());
                            $objDocumentoRelacion->setModulo("COMERCIAL");
                            $objDocumentoRelacion->setPuntoId($entity->getId());
                            $objDocumentoRelacion->setContratoId($intIdContrato);
                            $objDocumentoRelacion->setNumeroAdendum($intNumAdendum);
                            $objDocumentoRelacion->setPersonaEmpresaRolId($entity->getPersonaEmpresaRolId()->getId());
                            $objDocumentoRelacion->setEstado("Activo");
                            $objDocumentoRelacion->setFeCreacion(new \DateTime('now')); 
                            $objDocumentoRelacion->setUsrCreacion($strLogin);   
                            $em->persist($objDocumentoRelacion);
                            $em->flush();

                        }
                        else
                        {
                            throw new \Exception('Error al cargar el archivo digital - ' . $arrayRespNfs['strMensaje']);
                        }
                    }
                    else

                    {
                        throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                    }

                }                
                $em->persist($entity);
                $em->flush();               
                $em->getConnection()->commit();
                
                $this->get('session')->getFlashBag()->add('subida', "Archivo subido con éxito");
                return $this->redirect($this->generateUrl('infopunto_show', array('id' => $entity->getId(),'rol'=>$entityAdmiRol->getTipoRolId()->getDescripcionTipoRol())));
            }
            catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->getConnection()->close();     
			$this->get('session')->getFlashBag()->add('notice', $e->getMessage());			
				return $this->redirect($this->generateUrl('infopunto_newarchivodigital', 
				array('idPto' => $entity->getId())));                
            }        
    }  
 
//ESTA FUNCION MUESTRA LA FORMAS DE CONTACTO DE UN PUNTO
    /**
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 20-07-2022
     * Se modifica para que si es empresa MD y no hay formas de contacto en el punto se traiga la forma de contacto de la persona
     */
    public function formasContactoPuntoGridAction() {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $personaid = $request->get("personaid");
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        //Cuando sea inicio puedo sacar los 30 registros
        $resultado = $serviceInfoPunto->obtenerFormasContactoPorPunto($personaid, $limit, $start);
        $arreglo = $resultado['registros'];
        $intCount =  $resultado['total'];

        if (empty($arreglo))
        {
            //Si no tengo informacion del punto busco en la forma de contactos de la persona
            $arreglo = array(array());
            if ($idEmpresa == '18')
            {
                $resultado = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getFormasContactoPersonaPunto($personaid);
                $intCount = count($resultado);
                $arreglo = $resultado;
                if (empty($arreglo))
                {
                    $arreglo = array(array());
                }   
                
            }
        }
        error_log(json_encode(array('total' => $intCount, 'personaFormasContacto' => $arreglo)));
        $objResponse = new Response(json_encode(array('total' => $intCount, 'personaFormasContacto' => $arreglo)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @author Bryan Fonseca <bfonseca@telconet.ec>
     * @version 1.0 07-12-2022
     * Muestra las formas de contacto por ID_PERSONA. 
     */
    public function formasContactoPersonaAction() 
    {
        $objRequest = $this->getRequest();
        $intLimit = $objRequest->get("limit");
        $intStart = $objRequest->get("start");
        $intPersonaid = $objRequest->get("personaid");
        try 
        {
            // Se consiguen las formas de contacto por cliente
            $objResultado = $this->get('comercial.Cliente')->obtenerFormasContactoPorPersona($intPersonaid, $intLimit, $intStart);
            $arrayRegistros = $objResultado['registros'];
            $intCount =  $objResultado['total'];
            $objResponse = new Response(json_encode(array('total' => $intCount, 'personaFormasContacto' => $arrayRegistros)));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        } catch (\Exception $e) 
        {
            $arrayError = [
                'error' => 'Error interno del servidor.'
            ];
            $objResponse = new Response(json_encode([]));
            error_log(json_encode($arrayError));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
    }

    /**
     * listaPtosClientSesionAction
     *
     * Metodo para obtener los puntos clientes(Logines)      
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 12-02-2016
     * Se modifica que envie arreglo de parametros a consulta del repositorio
     * 
     * @return $respuesta
     */
    public function listaPtosClientSesionAction()
    {
        $respuesta         = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');        
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $ptoCliente_sesion = $session->get('ptoCliente');
        $puntoId           = $ptoCliente_sesion['id'];
        $cliente_sesion    = $session->get('cliente');
        $idEmpresa         = $session->get('idEmpresa');
        $idper             = $cliente_sesion['id_persona_empresa_rol'];

        $em = $this->get('doctrine')->getManager('telconet');
        $arrayParametros = array('idper'            => $idper,
                                 'rol'              => '',
                                 'strEstadoPunto'   => '',
                                 'strDireccion'     => '',
                                 'strFechaDesde'    => '',
                                 'strFechaHasta'    => '',
                                 'strLogin'         => '',
                                 'strNombrePunto'   => '',
                                 'strCodEmpresa'    => $idEmpresa,
                                 'strEsPadre'       => '',
                                 'intStart'         => 0,
                                 'intLimit'         => 999999999,
                                 'serviceInfoPunto' => ''
                                 );
        $em            = $this->get('doctrine')->getManager('telconet');        
        $objJsonPuntos = $em->getRepository('schemaBundle:InfoPunto')->getJsonFindPtosPorPersonaEmpresaRol($arrayParametros);        
        $respuesta->setContent($objJsonPuntos);        
        return $respuesta;              
    }

    /**
    * Documentación para el método 'verHistorialPuntoAction'.
    *
    * Obtiene el historial de un punto del cliente
    * @return json con historial del punto.
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 07-10-2014
    */
   public function verHistorialPuntoAction()
    {

        $request = $this->getRequest();
        $session = $request->getSession();
        $idPunto = $request->get('idPunto');
        $em = $this->get('doctrine')->getManager('telconet');
        $arr_encontrados = array();
        $num = 0;

        if($idPunto)
        {
            $objPuntoHistorial = $em->getRepository('schemaBundle:InfoPuntoHistorial')->findByPuntoId($idPunto);

            $num = count($objPuntoHistorial);

            foreach($objPuntoHistorial as $puntoHistorial)
            {

                $arr_encontrados[] = array('accion' => $puntoHistorial->getAccion(),
                                           'valor'  => str_replace("|", "<br>", $puntoHistorial->getValor()),
                                           'user'   => $puntoHistorial->getUsrCreacion(),
                                           'fecha'  => date_format($puntoHistorial->getFeCreacion(), 'd-m-Y H:i:s'),
                                           'ip'     => $puntoHistorial->getIpCreacion());
            }

            $response = new Response(json_encode(array('total' => $num, 'data' => $arr_encontrados)));
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }
        else
        {
            $response = new Response(json_encode(array('total' => $num, 'data' => '')));
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }
    }

    /**
     * @Secure(roles="ROLE_281-2257")
     * Documentación para el método 'cambioVendedorAction'.
     *
     * Obtiene y muestra informacion resumida del punto y permite la edicion del Vendedor y de los Contactos del punto.
     * Desde la versión 1.1 este método sólo guarda los datos del cambio de vendedor.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 09-03-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 21-10-2015
     * @since 1.0
     * Se agregan roles para separar el cambio de vendedor de las edición de las formas de contacto
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 26-04-2017 - Se envía al twig la variable 'strPrefijoEmpresa' para saber el prefijo de la empresa en sessión.
     *                           Se verifica si la empresa del usuario en sessión permite realizar el cambio de vendedor por servicio. Para ello se
     *                           envía la variable '$strVendedorPorServicio' para validar a nivel del twig si se debe presentar la opción 
     *                           correspondiente.
     * 
     * @param integer $id   //Id del punto 
     * @param integer $rol  //rol que posee el registro : Pre-cliente / Cliente
     * @return Renders a view.
     */
    public function cambioVendedorAction($id, $rol)
    {
        $emSeguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral     = $this->getDoctrine()->getManager("telconet_general");
        $objItemMenu   = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "4");
        $request       = $this->getRequest();
        $strCodEmpresa = $request->getSession()->get('idEmpresa');
        
        
        /**
         * Bloque que verifica si la empresa en sessión tiene habilitada la opción de edición del vendedor por servicio.
         */
        $strVendedorPorServicio   = "N";
        $arrayVendedorPorServicio = $emGeneral->getRepository("schemaBundle:AdmiParametroDet")->getOne('EDICION_VENDEDOR_POR_SERVICIO',
                                                                                                       'COMERCIAL',
                                                                                                       'CAMBIO_VENDEDOR',
                                                                                                       '',
                                                                                                       '',
                                                                                                       '',
                                                                                                       '',
                                                                                                       '',
                                                                                                       '',
                                                                                                       $strCodEmpresa);
        if( isset($arrayVendedorPorServicio['valor1']) && $arrayVendedorPorServicio['valor1'] == "S" )
        {
            $strVendedorPorServicio = $arrayVendedorPorServicio['valor1'];
        }//( isset($arrayVendedorPorServicio['valor1']) && $arrayVendedorPorServicio['valor1'] == "S" )
        
        
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        
        $serviceInfoPunto       = $this->get('comercial.InfoPunto');
        $arrayDatosPuntoCliente = $serviceInfoPunto->obtenerDatosPunto($id);
        $objPunto               = $arrayDatosPuntoCliente['punto'];
        $objPuntoDatoAdicional  = $arrayDatosPuntoCliente['puntoDatoAdicional'];
        $objCliente             = $arrayDatosPuntoCliente['cliente'];
        
        if(!$objPunto)
        {
            throw new \Exception('Unable to find InfoPersona entity.');
        }
        
        $objEditForm = $this->createForm(new InfoPuntoType(array('validaFile'        => false,
                                                                 'validaFileDigital' => false,
                                                                 'empresaId'         => $strCodEmpresa)), $objPunto);

        return $this->render("comercialBundle:infopunto:cambioVendedor.html.twig", array('objItemMenu'           => $objItemMenu,
                                                                                         'objPunto'              => $objPunto,
                                                                                         'objPuntoDatoAdicional' => $objPuntoDatoAdicional,
                                                                                         'objEditForm'           => $objEditForm->createView(),
                                                                                         'strLogin'              => $objPunto->getLogin(),
                                                                                         'objCliente'            => $objCliente,
                                                                                         'rol'                   => $rol,
                                                                                         'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                                                                                         'strVendedorPorServicio'=> $strVendedorPorServicio));
    }
    
      /**
     * @Secure(roles="ROLE_281-2258")
     * Documentación para el método 'actualizarFormasContactoAction'.
     *
     * Obtiene y muestra informacion resumida del punto y permite la edicion del Vendedor y de los Contactos del punto.
     * Desde la versión 1.1 este método sólo guarda los datos del cambio de vendedor.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 19-11-2015
     * @since 1.0
     * Se agregan roles para separar el cambio de vendedor de las edición de las formas de contacto
     * 
     * @param integer $id   //Id del punto 
     * @param integer $rol  //rol que posee el registro : Pre-cliente / Cliente
     * @return Renders a view.
     */
    public function actualizarFormasContactoAction($id, $rol)
    {
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $objItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("9", "4");
        $request      = $this->getRequest();
        $intIdEmpresa = $request->getSession()->get('idEmpresa');
        
        $serviceInfoPunto       = $this->get('comercial.InfoPunto');
        $arrayDatosPuntoCliente = $serviceInfoPunto->obtenerDatosPunto($id);
        $objPunto               = $arrayDatosPuntoCliente['punto'];
        $objPuntoDatoAdicional  = $arrayDatosPuntoCliente['puntoDatoAdicional'];
        $objCliente             = $arrayDatosPuntoCliente['cliente'];
        
        if(!$objPunto)
        {
            throw new \Exception('Unable to find InfoPersona entity.');
        }
        $objEditForm = $this->createForm(new InfoPuntoType(array('validaFile'        => false,
                                                                 'validaFileDigital' => false,
                                                                 'empresaId'         => $intIdEmpresa)), $objPunto);
        if(true === $this->get('security.context')->isGranted('ROLE_281-3097'))
        {
            $rolesPermitidos[] = 'ROLE_281-3097'; //Eliminar Formas Contacto
        }
        return $this->render("comercialBundle:infopunto:cambioVendedorFormasContacto.html.twig", array('objItemMenu'           => $objItemMenu,
                                                                                        'objPunto'              => $objPunto,
                                                                                        'objPuntoDatoAdicional' => $objPuntoDatoAdicional,
                                                                                        'objEditForm'           => $objEditForm->createView(),
             
                                                                                        'strLogin'              => $objPunto->getLogin(),
                                                                                        'objCliente'            => $objCliente,
                                                                                        'rol'                   => $rol,
                                                                                        'rolesPermitidos'       => $rolesPermitidos));

    }

    /**
     *
     * Documentación para el método 'guardaCambioVendedorAction'.
     *
     * Funcion que guarda formulario de Cambio de Vendedor y Contactos
     * 
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 10-03-2015 
     *
     * Descripcion: Se agrega metodo en service encargado de validar las formas de contactos ingresadas
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>          
     * @version 1.1 01-09-2015 
     *            
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-11-2016
     * Se agrega strPrefijoEmpresa, Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 26-04-2017 - Se valida que cuando las variables '$arrayInfoPunto', '$arrayInfoPuntoDatoAdicional' y '$arrayInfoPuntoExtra' 
     *                           contengan un valor diferente de NULL se realice la unión de la información a la variable '$arrayDatosForm' con la
     *                           función 'array_merge'
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4 04-07-2017
     * Se agregan las variables strNombrePais e intIdPais para realizar las validaciones por el país de la empresa.
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 27-10-2020
     * Se elimina la anotacion Secure por motivos que la validacion del rol se encuentra en el twig.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.6 13-07-2021 - Se valida la transacción.
     *
     * @param request $request   
     * @param integer $id   //Id del punto 
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
   public function guardaCambioVendedorAction(Request $request, $id)
    {
        $session                     = $request->getSession();        
        $strUsrCreacion              = $session->get('user');
        $strClientIp                 = $request->getClientIp();
        $strPrefijoEmpresa           = $session->get('prefijoEmpresa');
        $strCodEmpresa               = $session->get('idEmpresa');
        $intPersonaEmpresaRol        = $session->get('idPersonaEmpresaRol');
        $intIdPais                   = $session->get('intIdPais');
        $strNombrePais               = $session->get('strNombrePais');
        $arrayInfoPunto              = $request->request->get('infopuntotype');
        $arrayInfoPuntoDatoAdicional = $request->request->get('infopuntodatoadicionaltype');
        $arrayInfoPuntoExtra         = $request->request->get('infopuntoextratype');
        $arrayDatosForm              = array('strPrefijoEmpresa'    => $strPrefijoEmpresa, 
                                             'strCodEmpresa'        => $strCodEmpresa, 
                                             'intPersonaEmpresaRol' => $intPersonaEmpresaRol);
        
        if( !empty($arrayInfoPunto) )
        {
            $arrayDatosForm = array_merge( $arrayDatosForm, $arrayInfoPunto);
        }
        
        if( !empty($arrayInfoPuntoDatoAdicional) )
        {
            $arrayDatosForm = array_merge( $arrayDatosForm, $arrayInfoPuntoDatoAdicional);
        }
        
        if( !empty($arrayInfoPuntoExtra) )
        {
            $arrayDatosForm = array_merge( $arrayDatosForm, $arrayInfoPuntoExtra);
        }
        
        $em              = $this->getDoctrine()->getManager('telconet');
        $objInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($id);
        
        try
        {
            $arrayDatosForm['strNombrePais']   = $strNombrePais;
            $arrayDatosForm['intIdPais']       = $intIdPais;
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto    = $this->get('comercial.InfoPunto');
            $objInfoPuntoEditado = $serviceInfoPunto->guardaCambioVendedor($objInfoPunto, $strUsrCreacion, $strClientIp, $arrayDatosForm);
            
            $this->get('session')->getFlashBag()->add('subida', 'Se ha actualizado la información del punto de manera exitosa.');

            return $this->redirect($this->generateUrl('infopunto_show', array('id'  => $objInfoPuntoEditado->getId(), 
                                                                              'rol' => $arrayDatosForm['rol'])));
        }
        catch (\Exception $e) 
        { 
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            if($request->request->get('cambioVendedor') == '1')
            {
                $strRedirect = 'infopunto_cambioVendedor';
            }
            else
            {
                $strRedirect = 'infopunto_actualizarFormasContacto';
            }
            return $this->redirect($this->generateUrl($strRedirect, array('id' => $objInfoPunto->getId(), 'rol' => $arrayDatosForm['rol'])));
        }        
    }
    
    /**
     * Documentación para el método 'getServicioProductoCaracteristicasAjaxAction'.
     *
     * Funcion que retorna caracterisiticas del servicio del cliente
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 07-05-2015            
     * @param request $request   
     * @param integer $id     //Id del servicio 
     * @param String  $estado //Estado del servicio 
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function getServicioProductoCaracteristicasAjaxAction()
    {
        $objRespuesta     = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion      = $this->get('request');
        $intIdServicio = ($objPeticion->query->get('idServicio') ? $objPeticion->query->get('idServicio') : "");
        $strEstado     = ($objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "");
        $strCaraClienteEm = ($objPeticion->query->get('clienteEm')== "true" ? "SERIE_EQUIPO_PTZ" : "");
       
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoServicioProdCaract')
            ->generarJsonCaracteristicasServicios($intIdServicio, $strEstado, $strCaraClienteEm);
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
     * Documentación para el método 'getCamarasEmAjaxAction'.
     *
     * Funcion que retorna si el cliente pertenece al flujo CLIENTES SOPORTE LOGICO CAMARA
     *
     * @author Jorge Gómez <jigomez@telconet.ec>
     * @version 1.0 13-03-2023            
     * @param request $request   
     * @param integer $intIdPersonaRol     //Id de Persona Empresa Rol 
     * @param integer $intIdProducto     //Id de Producto  
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function getCamarasEmAjaxAction()
    {
        $objRespuesta     = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion      = $this->get('request');
        $objSession           = $objPeticion->getSession();
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $intIdPersonaRol = ($objPeticion->query->get('idPersonaRol') ? $objPeticion->query->get('idPersonaRol') : "");
        $intIdProducto   = ($objPeticion->query->get('idProducto') ? $objPeticion->query->get('idProducto') : "");
        $objServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $strNombreParametro = "CLIENTES SOPORTE LOGICO CAMARA";

        
        $arrayCliente = $objServicioTecnico->getClienteSoporteLogico(array(
            'strNombreParametro' => $strNombreParametro,
            'intIdPersonaRol' => $intIdPersonaRol,
            'intIdProducto' => $intIdProducto, 
            'strCodEmpresa' => $strCodEmpresa )
        );
           
        $intTotal  = count($arrayCliente);
        $objRespuesta->setContent($intTotal);
        
        return $objRespuesta;
    }
    

    /**
     * Documentación para el método 'getPosteAction'.
     *
     * Funcion que retorna los tipos de poste para instalación de cámaras
     *
     * @author Jorge Gómez <jigomez@telconet.ec>
     * @version 1.0 13-03-2023            
     * @param request $request   
     * @param string $strCodEmpresa     //Codigo de empresa
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function getPosteAction()
    { 
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $arrayEncontrados     = array();
        $objServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $strNombreParametro = "TIPOS DE POSTE";
    
        try
        {

            $arrayTiposPoste = $objServicioTecnico->getClienteSoporteLogico(array(
                'strNombreParametro' => $strNombreParametro,
                'strCodEmpresa' => $strCodEmpresa )
            );

            $intTotal = count($arrayTiposPoste);

            for($intCiclo=0; $intCiclo < $intTotal; $intCiclo++)
            {
                $arrayEncontrados[] = array(
                    'idPoste'          => $arrayTiposPoste[$intCiclo]["valor2"],
                    'nombrePoste' => $arrayTiposPoste[$intCiclo]["valor2"]
                  );
            }       
            
            $strData      = json_encode($arrayEncontrados);
            $strResultado = '{"total":"'.$intTotal.'","encontrados":'.$strData.'}';

            $objRespuesta->setContent($strResultado);
        
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxObtenerTiposProblemaAction',
                                       'Error al consultar los tipos de problema. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
       
        return $objRespuesta;
    }
    /**
     * @Secure(roles="ROLE_13-2618")
     * Documentación para el método 'actualizarCaracteristicaAjaxAction'.
     *
     * Funcion que actualiza la caracteristica del cliente
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 08-05-2015            
     * @param request $request   
     * @param String  $valor                //Valor del servicio producto caracteristica
     * @param integer $idServicioProdCaract //Id del servicio producto caracteristica
     * @param String  $estado               //Estado del servicio producto caracteristica
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    public function actualizarCaracteristicaAjaxAction()
    {
        $objRespuesta  = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");
        $em            = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $objPeticion             = $this->get('request');
        $intIdServProdCaract     = $objPeticion->get('idServicioProdCaract');
        $strEstado               = $objPeticion->get('estado');
        $strValor                = $objPeticion->get('valor');
        $strCaracteristica       = $objPeticion->get('caracteristica');
        $strValorAntes           = "";
        $strEstadoAntes          = "";

        $em->getConnection()->beginTransaction();
        
        try {

            $entityServicioProdCaract = $em->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdServProdCaract);
            if (!$entityServicioProdCaract) {
                throw $this->createNotFoundException('No se encontro la caracteristica del servicio!');
            }
            $strValorAntes  = $entityServicioProdCaract->getValor();
            $strEstadoAntes = $entityServicioProdCaract->getEstado();
            //Actualiza el servicio producto caracteristica
            $entityServicioProdCaract->setEstado($strEstado);
            $entityServicioProdCaract->setValor($strValor);
            $entityServicioProdCaract->setFeUltMod(new \DateTime('now'));
            $em->persist($entityServicioProdCaract);
            $em->flush();
            
            $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->find($entityServicioProdCaract->getServicioId());
            if (!$entityServicio) 
            {
                throw $this->createNotFoundException('No se encontro el servicio!');
            }
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($entityServicio);
            $entityServicioHist->setObservacion('Se actualizo caracteristica '.$strCaracteristica.' con ID '.$intIdServProdCaract.' : <br>'.
                                                'Valores Anteriores: <br>'.  
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntes.'<br>'.
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntes.'<br>'.
                                                'Valores Actuales: <br>'.  
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValor.'<br>'.
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstado);
            $entityServicioHist->setIpCreacion($objPeticion->getClientIp());
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setAccion('actualizaCaracteristica');
            $entityServicioHist->setUsrCreacion($objPeticion->getSession()->get('user'));
            $entityServicioHist->setEstado($entityServicio->getEstado());
            $em->persist($entityServicioHist);
            $em->flush();

            $em->getConnection()->commit();
            $objRespuesta->setContent("OK");
        } catch (\Exception $e) {
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $objRespuesta->setContent("Error");
        }


        return $objRespuesta;
        
        
        
    }

    /**
     * documentosFileUploadAction
     *
     * Metodo encargado de procesar los archivos que se elijan en el formulario sobre los productos de
     * seguridad logica y coloca en el directorio de destino fisico y despues guarda en la base de forma
     * logica.
     *
     * @return json con resultado del proceso
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 01-06-2016
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 29-08-2017 - Se envia el tipo de MODULO ( Anexo ) con el cual se desea registrar el documento
     *                         - Se parametriza envio de valores a la creacion del anexo tecnico
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 16-08-2019 - Se modifica el destino del archivo por un valor enviado en el objeto de petición
     *                           para que los archivos se guarden en rutas distintas y con observacion adecuada.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.3 05-03-2020 - Se realiza ajuste para que se reciba por request un objeto que contiene la info necesaria
     *                           para llamar al servicio que genera las rutas con el nuevo formato establecido.
     *
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.4 22-04-2021 - Se realiza cambio para el guardado del archivo al NFS
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.5 03-05-2021 - Se realiza corrección para el guardado de archivos.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.6 03-06-2021 - Se realiza validación para el guardado de archivo de Rutas.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.7 09-06-2022 - Se modifica lógica para guardar el NFS para que todos los ANEXOS TECNICOS se guarden
     * @since 1.6
     * 
     */ 
    public function documentosFileUploadAction()
    {
        $request              = $this->getRequest();
        $peticion             = $this->get('request');
        $serviceUtil          = $this->get('schema.Util');

        $servicio             = $peticion->get('idServicio') ? $peticion->get('idServicio') : null;
        $arrayData            = $peticion->get('data') ? json_decode($peticion->get('data'),true) : null;
        $strTipo              = $peticion->get('tipo') ? $peticion->get('tipo') : null;
        $strUbicacion         = $peticion->get('strUbicacion') ? $peticion->get('strUbicacion') : 'tecnico/anexo_tecnico/';
        $strObservacion       = $peticion->get('strObservacion') ? $peticion->get('strObservacion') : 'Se ingresó anexo';
        $strCambioEstado      = $peticion->get('strCambioEstado') ? $peticion->get('strCambioEstado') : null;
        $strNombreDocumento   = $peticion->get('strNombreDocumento') ? $peticion->get('strNombreDocumento') : 'Anexo Tecnico';
        $strMensaje           = $peticion->get('strMensaje') ? $peticion->get('strMensaje') : 'Anexo Tecnico para productos Seguridad Logica';
        $serverRoot           = $_SERVER['DOCUMENT_ROOT'];
        $objSession           = $request->getSession();
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa');
        $strEsInspeccionRadio = $peticion->get('strEsInspeccionRadio');
        $strEsIngresoRutas    = $peticion->get('strEsIngresoRutas');
        $strLogin             = $peticion->get('strLogin');

        $strApp               = isset($arrayData['app']) ? $arrayData['app'] : 'TelcosWeb';
        $strSubModulo         = isset($arrayData['submodulo']) ? $arrayData['submodulo'] : 'AnexoTecnico';

        $strUsuario           = $peticion->getSession()->get('user');
        $emComercial          = $this->getDoctrine()->getManager('telconet');

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');

        try
        {           
            $file = $request->files;

            $objArchivo = $file->get('archivo');

            if($servicio)
            {

                if(empty($strLogin))
                {
                    $entityServicio     = $emComercial->getRepository('schemaBundle:InfoServicio')->find($servicio);
                    $entityPunto        = $emComercial->getRepository('schemaBundle:InfoPunto')->find($entityServicio->getPuntoId());
                    $strLogin           = $entityPunto->getLogin();
                }
            }
            else
            {
                $strResultadoJson = '{"success":false,"respuesta":"Ha ocurrido un error con el servicio, por favor reporte a Sistemas"}';
            }

            //Verifica si existe el archivo
            if($file && count($file) > 0)
            {
                //Verifica si se ha obtenido el archivo
                if(isset($objArchivo) && count($objArchivo) > 0)
                {
                    $tipo = $objArchivo->guessExtension(); //extension		
                    $archivo = $objArchivo->getClientOriginalName();
                    $tamano = $objArchivo->getClientSize();//Tamano de archivo
                    //Se divide para obtener nombre y extension de archivo
                    $arrayArchivo = explode('.', $archivo);
                    $countArray = count($arrayArchivo);
                    $extArchivo = $arrayArchivo[$countArray - 1];

                    $prefijo = substr(md5(uniqid(rand())), 0, 6);
                    
                    $strNuevoNombreDocumento = $strNombreDocumento . "_" . $prefijo . "." . $extArchivo;
                    $strNuevoNombreDocumento = str_replace(" ", "_", $strNuevoNombreDocumento);

                    if($archivo != "")
                    {
                        $nuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;

                        $nuevoNombre = str_replace(" ", "_", $nuevoNombre);

                        if($strPrefijoEmpresa == "TN" && !$arrayData)
                        {
                            $nuevoNombre = $nombreArchivo . "." . $extArchivo;
                            $destino = $serverRoot . "/public/uploads/tn/";
                            if($servicio)
                            {
                                // Se verifica si existe directorio creado por fecha actual
                                $destino .= $strUbicacion;
                                $soporteService = $this->get('soporte.SoporteService');
                                $directorioFechaActual = $soporteService->generarDirectorioFechaActual($destino);
                                $destino .= $directorioFechaActual;
                            }
                        }
                        else if($arrayData && isset($strEsInspeccionRadio) && $strEsInspeccionRadio == "SI")
                        {
                            $strApp                 = $arrayData['app'];
                            $strModulo              = $arrayData['modulo'];
                            $strSubModulo           = $arrayData['submodulo'];
                            $strNombreDocument      = str_replace(" ", "_", $strNombreDocumento);
                            $arrayKey               = array("key"=>$strLogin);
                            $strArchivo             = file_get_contents($objArchivo);
                            $strBase64Archivopdf    = base64_encode($strArchivo);
                            $arrayPathAdicional     = array($arrayKey);
                            $arrayParamNfs          = array(
                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                'strApp'               => $strApp,
                                'strSubModulo'         => $strSubModulo,
                                'arrayPathAdicional'   => $arrayPathAdicional,
                                'strBase64'            => $strBase64Archivopdf,
                                'strNombreArchivo'     => $strNombreDocument. "." .$extArchivo,
                                'strUsrCreacion'       => $peticion->getSession()->get('user'));
                                
                            $arrayRespuestaNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                        }
                        else if($arrayData && isset($strEsIngresoRutas) && $strEsIngresoRutas == "SI")
                        {
                            $strApp                 = $arrayData['app'];
                            $strModulo              = $arrayData['modulo'];
                            $strSubModulo           = $arrayData['submodulo'];
                            $strNombreDocument      = 'ingresoRutas';
                            $arrayKey               = array("key"=>$peticion->getSession()->get('user'));
                            $strArchivo             = file_get_contents($objArchivo);
                            $strBase64Archivo       = base64_encode($strArchivo);
                            
                            $strCadenaRandom            = substr(md5(uniqid(rand())),0,6);
                            $strNuevoNombreArchivo      = $strNombreDocument . "_" . date('Y-m-d') . "_". $prefijo;
                            $strCaracteresAReemplazar   = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                            $strCaracteresReemplazo     = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                            $strNuevoNombreArchivo      = strtr($strNuevoNombreArchivo, $strCaracteresAReemplazar, $strCaracteresReemplazo);
                                                        
                            $arrayPathAdicional     = array($arrayKey);
                            $arrayParamNfs          = array(
                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                'strApp'               => $strApp,
                                'strSubModulo'         => $strSubModulo,
                                'arrayPathAdicional'   => $arrayPathAdicional,
                                'strBase64'            => $strBase64Archivo,
                                'strNombreArchivo'     => $strNuevoNombreArchivo. "." .$extArchivo,
                                'strUsrCreacion'       => $peticion->getSession()->get('user'));
                                
                            $arrayRespuestaNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                            
                            if(is_array($arrayRespuestaNfs) && !empty($arrayRespuestaNfs) && $arrayRespuestaNfs['intStatus'] == 200)
                            {
                                $arrayParametros                        = array();
                                $arrayParametros['intIdServicio']       = $servicio;
                                $arrayParametros['strNuevoNombre']      = $strNuevoNombreArchivo. "." .$extArchivo;
                                $arrayParametros['strDestino']          = $arrayRespuestaNfs['strUrlArchivo'];
                                $arrayParametros['objRequest']          = $peticion;
                                $arrayParametros['strTipoModulo']       = $strTipo;
                                $arrayParametros['strObservacion']      = $strObservacion;
                                $arrayParametros['strCambioEstado']     = $strCambioEstado;
                                $arrayParametros['strNombreDocumento']  = $strNombreDocumento;
                                $arrayParametros['strMensaje']          = $strMensaje;
                                $arrayParametros['strEsIngresoRutas']   = $strEsIngresoRutas;
                                //guarda en la infodocumento
                                $intIdDocumento   = $this->guardarAnexoTecnico($arrayParametros);
                                $strResultadoJson = '{"success":true,"fileName":"' . $strNombreDocumento . 
                                                     '","fileSize":"' . $tamano . 
                                                     '" ,"filePath":"'. base64_encode($arrayRespuestaNfs['strUrlArchivo']) . 
                                                     '","respuesta":"Se ha anexado el archivo"}';
                            }
                            
                            //Descargar el archivo para realizar el proceso en la base de datos
                            $arrayParametros['intIdDocumento']   = $intIdDocumento;
                            $strUrlSubida = $this->getDescargaDocumentosRutas($arrayParametros);
                            
                            //llamar al procedimiento en la base para realizar la subida de rutas
                            $arrayParamsSubidaCsv   = array(
                                                "intIdDocumento"             => $intIdDocumento,
                                                "strNombreArchivoRuta"       => $strUrlSubida,
                                                "strExtensionArchivoRuta"    => $extArchivo,
                                                "strUsrCreacion"             => $peticion->getSession()->get('user')
                                                );
                
                            $arrayRespuestaSubidaCsv    = $this->ejecutaSubidaCsvRutas($arrayParamsSubidaCsv);
                            $strStatusSubidaCsv         = $arrayRespuestaSubidaCsv["status"];
                            $strMensajeSubidaCsv        = $arrayRespuestaSubidaCsv["mensaje"];
                            
                            if($strStatusSubidaCsv !== "OK")
                            {
                                $strResultadoJson = '{"success":false,"respuesta":"' . $strMensajeSubidaCsv .'"}';
                            }
                            
                                                        
                        }
                        elseif ($strPrefijoEmpresa == "TN" && $arrayData)
                        {
                            /*Definimos el servicio que nos proporcionará la nueva ruta.*/
                            /* @var ProcesoService */
                            $serviceProceso = $this->get('soporte.ProcesoService');

                            /*Definimos las variables necesarias para el servicio.*/
                            $strApp            = $arrayData['app'];
                            $strModulo         = $arrayData['modulo'];
                            $strSubModulo      = $arrayData['submodulo'];

                            /*Hacemos el llamado al servicio para obtener la ruta.*/
                            $strUbicacionResponse =  $serviceProceso->getFilePath(
                                        array(
                                            'strApp'            => $strApp,
                                            'strModulo'         => $strModulo,
                                            'strSubModulo'      => $strSubModulo,
                                            'strPrefijoEmpresa' => $strPrefijoEmpresa
                                        )
                            );

                            /*Validamos una respuesta afirmativa.*/
                            if ($strUbicacionResponse['status'] == 'ok')
                            {
                                /*Definimos ruta nueva con el standard actual.*/
                                $destino = $serverRoot . "/public/uploads/" . $strUbicacionResponse['path'];
                            }
                            else
                            {
                                /*En caso de ocurrir error, levantamos una excepción y devolvemos el mensaje.*/
                                throw new \Exception($strUbicacionResponse['message']);
                            }
                        }
                                                
                        //Si proviene de subida a partir del servicio del cliente
                        if(!empty($strLogin))
                        {            
                            if(($tipo && ($tipo == 'jpg' || $tipo == 'jpeg' || $tipo == 'pdf' || $tipo == 'doc' || $tipo == 'docx' || 
                                $tipo == 'xlsx' || $tipo == 'xls' || $tipo == 'odt' || $tipo == 'ods' || $tipo == 'zip')))
                            {

                                $arrayKey               = array("key"=>$strLogin);
                                $strArchivo             = file_get_contents($objArchivo);
                                $strBase64Archivopdf    = base64_encode($strArchivo);
                                $arrayPathAdicional     = array($arrayKey);

                                $arrayParamNfs          = array(
                                    'prefijoEmpresa'       => $strPrefijoEmpresa,
                                    'strApp'               => $strApp,
                                    'strSubModulo'         => $strSubModulo,
                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                    'strBase64'            => $strBase64Archivopdf,
                                    'strNombreArchivo'     => $strNuevoNombreDocumento,
                                    'strUsrCreacion'       => $strUsuario
                                );
    
                                $arrayRespuestaNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);

                                if(is_array($arrayRespuestaNfs) && !empty($arrayRespuestaNfs) && $arrayRespuestaNfs['intStatus'] == 200)
                                {
                                    $arrayParametros                        = array();
                                    $arrayParametros['intIdServicio']       = $servicio;
                                    $arrayParametros['strNuevoNombre']      = $strNuevoNombreDocumento;
                                    $arrayParametros['strDestino']          = $arrayRespuestaNfs['strUrlArchivo'];
                                    $arrayParametros['objRequest']          = $peticion;
                                    $arrayParametros['strTipoModulo']       = $strTipo;
                                    $arrayParametros['strObservacion']      = $strObservacion;
                                    $arrayParametros['strCambioEstado']     = $strCambioEstado;
                                    $arrayParametros['strNombreDocumento']  = $strNuevoNombreDocumento;
                                    $arrayParametros['strMensaje']          = $strMensaje;
                                    //guarda en la infodocumento
                                    $boolGuardaDoc    = $this->guardarAnexoTecnico($arrayParametros);
                                    $strResultadoJson = '{"success":true,"fileName":"' . $strNombreDocumento . 
                                                        '","fileSize":"' . $tamano . 
                                                        '" ,"filePath":"'. base64_encode($arrayRespuestaNfs['strUrlArchivo']) . 
                                                        '","respuesta":"Se ha anexado el archivo"}';
                                }
                                else
                                {
                                    $strResultadoJson = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
                                }

                                if($boolGuardaDoc && $strCambioEstado)
                                {
                                    /*Si se recibe el parámetro strCambioEstado se cambiara el estado del servicio.*/
                                    $entityServicio      = $emComercial->getRepository('schemaBundle:InfoServicio')->find($servicio);
                                    /*Obtengo el objeto de la solicitud de factibilidad.*/
                                    $entityTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                        ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");


                                    $emComercial->getConnection()->beginTransaction();
                                    /*Creo una solicitud de factibilidad en estado 'Factible'.*/
                                    $entitySolicitud = new InfoDetalleSolicitud();
                                    $entitySolicitud->setServicioId($entityServicio);
                                    $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);
                                    $entitySolicitud->setEstado('Factible');
                                    $entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));
                                    $entitySolicitud->setFeCreacion(new \DateTime('now'));
                                    $emComercial->persist($entitySolicitud);
                                    $emComercial->flush();
                                    /*Establezco el estado del servicio de acuerdo a lo recibido por el parámetro.*/
                                    $entityServicio->setEstado($strCambioEstado);
                                    $emComercial->persist($entityServicio);
                                    $emComercial->flush();
                                    $emComercial->commit();
                                }
                                else if (!$boolGuardaDoc)
                                {
                                    $strResultadoJson = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
                                }
                            }
                            else
                            {

                                $strResultadoJson = '{"success":false,"respuesta":"Debe subir archivo con formato PDF, JPG, '
                                    . 'WORD o EXCEL"}';
                            }
                        }
                        else
                        {
                            if ($strEsIngresoRutas !== "SI")
                            {
                                $strResultadoJson = '{"success":false,"respuesta":"Ha ocurrido un error con el servicio, '
                                                    . 'por favor reporte a Sistemas"}';
                            }
                        }
                    }
                    else
                    {
                        $strResultadoJson = '{"success":false,"fileName":"","fileSize":"' . 0 . '", "filePath":""}';
                    }
                }//FIN IF ARCHIVO SUBIDO
                else
                {
                    $strResultadoJson = '{"success":false,"fileName":"","fileSize":"' . 0 . '", "filePath":""}';
                }
            }//FIN IF FILES
            else
            {
                $strResultadoJson = '{"success":false,"fileName":"","fileSize":"' . 0 . '", "filePath":""}';
            }

            $respuesta->setContent($strResultadoJson);
            return $respuesta;
        }
        catch(\Exception $e)
        {
            $strResultado = '{"success":false,"respuesta":"' . $e->getMessage() . '"}';
            $respuesta->setContent($strResultado);
            return $respuesta;
        }
    }
    
    /**
     * subirDocumentoAnexoAction
     *
     * Metodo encargado de subir un documento adjunto
     *
     * @return json con resultado del proceso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-12-2020
     */
    public function subirDocumentoAnexoAction()
    {
        $objRequest            = $this->getRequest();
        $objPeticion           = $this->get('request');
        $strIpUsuarioCreacion  = $objPeticion->getClientIp();

        $intIdServicio          = $objPeticion->get('idServicio') ? $objPeticion->get('idServicio') : null;
        $strNombreDocumento     = "mapa-recorrido-cliente";
        $strDestino             = "";
        $strBanderaAnexoTecnico = "S";
        $strMensaje             = "Se adjunto mapa de recorrido del cliente, como archivo anexo";
        $strServerRoot          = $this->container->getParameter('path_telcos');
        $strRutaMapaDeRecorrido = $this->container->getParameter('strRutaMapaDeRecorrido');
        $objSession             = $objRequest->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $serviceUtil            = $this->get('schema.Util');

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/html');

        try
        {
            $objFile    = $objRequest->files;
            $objArchivo = $objFile->get('archivo');

            //Verifica si se ha obtenido el archivo
            if(isset($objArchivo) && count($objArchivo) > 0)
            {
                $strTipo    = $objArchivo->guessExtension(); //extension
                $strArchivo = $objArchivo->getClientOriginalName();

                //Se divide para obtener nombre y extension de archivo
                $arrayArchivo     = explode('.', $strArchivo);
                $arrayCount       = count($arrayArchivo);
                $strNombreArchivo = $arrayArchivo[0];
                $strExtArchivo    = $arrayArchivo[$arrayCount - 1];

                $strPrefijoEmpresa = substr(md5(uniqid(rand())), 0, 6);

                $strNuevoNombre = $strNombreArchivo . "_" . $strPrefijoEmpresa . "." . $strExtArchivo;
                $strNuevoNombre = str_replace(" ", "_", $strNuevoNombre);
                $strNuevoNombre = $strNombreArchivo . "-" .date("Ymd_His").".".$strExtArchivo;
                $strDestino = $strServerRoot . $strRutaMapaDeRecorrido;

                if($strTipo && ($strTipo == 'pdf'))
                {
                    $objArchivo->move($strDestino, $strNuevoNombre);

                    $arrayParametros                            = array();
                    $arrayParametros['intIdServicio']           = $intIdServicio;
                    $arrayParametros['strNuevoNombre']          = $strNuevoNombre;
                    $arrayParametros['strDestino']              = $strDestino . $strNuevoNombre;
                    $arrayParametros['objRequest']              = $objPeticion;
                    $arrayParametros['strTipoModulo']           = "COMERCIAL";
                    $arrayParametros['strObservacion']          = $strMensaje;
                    $arrayParametros['strNombreDocumento']      = $strNombreDocumento;
                    $arrayParametros['strMensaje']              = $strMensaje;
                    $arrayParametros['strBanderaAnexoTecnixo']  = $strBanderaAnexoTecnico;

                    $intBool = $this->guardarAnexoTecnico($arrayParametros);

                    if($intBool)
                    {
                        $boolSuccess = true;
                        $strMensaje  = "El archivo fue anexado con exito";
                    }
                }
                else
                {
                    $boolSuccess = false;
                    $strMensaje  = "Solo se permiten archivos con extension PDF";
                }
            }
        }
        catch(\Exception $e)
        {
            $boolSuccess = false;
            $strMensaje  = "Se presento un error en la carga del archivo, notificar a Sistemas.";

            $serviceUtil->insertError('Telcos+',
                                      'InfoPuntoController.subirDocumentoAnexoAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpUsuarioCreacion
                                     );
        }

        $strResultado = '{"success":"'.$boolSuccess.'","respuesta":"'.$strMensaje.'"}';
        $objRespuesta->setContent($strResultado);

        return $objRespuesta;
    }


    /**
     * @Secure(roles="ROLE_9-6")    
     * 
     * Función que renueva un plan o un producto de un servicio
     *
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 1.0 27-04-2016
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 1.1 30-08-2016 - Se corrige que al validar la fecha de renovación del plan 100 se busque por servicio.
     * 
     * @return Response
     */
    public function renovacionPlanAction()
    {
        $objRespuesta        = new Response;
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objPeticion         = $this->get('request');
        $intIdServicio       = ($objPeticion->request->get('intIdServicio') ? $objPeticion->request->get('intIdServicio') : "");
        $strFechaRenovacion  = ($objPeticion->request->get('strFechaRenovacion') ? $objPeticion->request->get('strFechaRenovacion') : "");
        $strMensajeRespuesta = "OK";
        
        $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServicio);
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            if(!$objInfoServicio) 
            {
                $strMensajeRespuesta = "No se encontro el servicio";
            }
            else
            {
                if( !$strFechaRenovacion )
                {
                    $strMensajeRespuesta = "Fecha de renovación del plan no válida";
                }
                else
                {
                    $arrayFechaRenovacion    = explode("/", $strFechaRenovacion);
                    $datetimeFechaRenovacion = new \DateTime($arrayFechaRenovacion[2]."-".$arrayFechaRenovacion[1]."-".$arrayFechaRenovacion[0]);

                    $objInfoServicioHistorial = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                            ->findOneBy( array( 'accion'     => 'renovacionPlan',
                                                                                'servicioId' => $objInfoServicio,
                                                                                'feCreacion' => $datetimeFechaRenovacion) );
                    
                    if( !$objInfoServicioHistorial )
                    {
                        $objInfoServicio->setMesesRestantes($objInfoServicio->getFrecuenciaProducto());
                        $emComercial->persist($objInfoServicio);
                        $emComercial->flush();

                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objInfoServicio);
                        $objInfoServicioHistorial->setObservacion('Fecha de renovación del plan');
                        $objInfoServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                        $objInfoServicioHistorial->setFeCreacion($datetimeFechaRenovacion);
                        $objInfoServicioHistorial->setAccion('renovacionPlan');
                        $objInfoServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                        $emComercial->persist($objInfoServicioHistorial);
                        $emComercial->flush();

                        $objInfoServicioHistorial2 = new InfoServicioHistorial();
                        $objInfoServicioHistorial2->setServicioId($objInfoServicio);
                        $objInfoServicioHistorial2->setObservacion('Se realiza renovación del plan');
                        $objInfoServicioHistorial2->setIpCreacion($objPeticion->getClientIp());
                        $objInfoServicioHistorial2->setFeCreacion(new \DateTime("now"));
                        $objInfoServicioHistorial2->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objInfoServicioHistorial2->setEstado($objInfoServicio->getEstado());
                        $emComercial->persist($objInfoServicioHistorial2);
                        $emComercial->flush();
                    }
                    else
                    {
                        $strMensajeRespuesta = "El plan ya ha sido renovado con anterioridad";
                    }//( !$objInfoServicioHistorial ) 
                }//( !$strFechaRenovacion )
            }//(!$objInfoServicio) 

            $emComercial->getConnection()->commit();
        }
        catch (Exception $e)
        {
            $strMensajeRespuesta = $e->getMessage();
            
            error_log($strMensajeRespuesta);
            
            $emComercial->getConnection()->rollback();
        }
        
        $emComercial->getConnection()->close();
        
        $objRespuesta->setContent($strMensajeRespuesta);
        
        return $objRespuesta;
    }
    

    /**
     * guardarAnexoTecnico
     *
     * Metodo encargado de guardar la informacion documental relacionada al ANEXO TECNICO del producto
     * SEGURIDAD LOGICA
     *          
     * @return boolean
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 02-06-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 29-08-2017 - Se modifica para que el modulo de la relacion sea enviado como parametro
     *                         - Se parametriza entradas de la funcion
     *                         - Se ingresa historial en el servicio de creacion de anexo tecnico
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 14-08-2019 | Se modifica parametros que establecen el Nombre del documento y el mensaje,
     *                           para que estos se establezcan mediante un parametro y no un valor quemado.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 17-12-2020 - Se agrega logica por subida de archivo anexo de SSID_MOVIL
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.4 03-06-2021 - Se agrega logica por subida de archivo de Rutas
     * 
     */
    public function guardarAnexoTecnico($arrayParametros)
    {
        $strBanderaAnexoTecnixo = $arrayParametros['strBanderaAnexoTecnixo']?$arrayParametros['strBanderaAnexoTecnixo']:"N";
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emComunicacion         = $this->getDoctrine()->getManager('telconet_comunicacion');
        $strEsIngresoRutas      = $arrayParametros['strEsIngresoRutas'];

        if ($strEsIngresoRutas == "SI")
        {
            $emComunicacion->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();
                
            try
            {                    
                //se crea el documento que incurre en esta comunicacion
                $entity = new InfoDocumento();

                $entity->setNombreDocumento($arrayParametros['strNombreDocumento']);
                $entity->setUbicacionFisicaDocumento($arrayParametros['strDestino']);
                $entity->setUbicacionLogicaDocumento($arrayParametros['strNuevoNombre']);
                $entity->setMensaje($arrayParametros['strMensaje']);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setFechaDocumento(new \DateTime('now'));
                $entity->setIpCreacion($arrayParametros['objRequest']->getClientIp());
                $entity->setUsrCreacion($arrayParametros['objRequest']->getSession()->get('user'));
                $entity->setEmpresaCod($arrayParametros['objRequest']->getSession()->get('idEmpresa'));
                    
                $emComunicacion->persist($entity);
                $emComunicacion->flush();
                $intIdDocumento = $entity->getId();
                
                $emComunicacion->getConnection()->commit();
                $emComercial->getConnection()->commit();
                    
                return $intIdDocumento;
            }
            catch(\Exception $e)
            {
                if($emComunicacion->getConnection()->isTransactionActive())
                {
                    $emComunicacion->getConnection()->rollback();
                    $emComunicacion->getConnection()->close();
                }
                   
                if($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->rollback();
                    $emComercial->getConnection()->close();
                }

                return false;
            }
        }
        else
        {
            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['intIdServicio']);
            if(is_object($objServicio))
            {
                $idPunto = $objServicio->getPuntoId()->getId();

                if($idPunto)
                {
                    $emComunicacion->getConnection()->beginTransaction();
                    $emComercial->getConnection()->beginTransaction();

                    try
                    {                    
                        $punto  = $emComercial->getRepository('schemaBundle:InfoPunto')->find($idPunto);
                        //se crea el documento que incurre en esta comunicacion
                        $entity = new InfoDocumento();

                        $entity->setNombreDocumento($arrayParametros['strNombreDocumento']);
                        $entity->setUbicacionFisicaDocumento($arrayParametros['strDestino']);
                        $entity->setUbicacionLogicaDocumento($arrayParametros['strNuevoNombre']);
                        $entity->setMensaje($arrayParametros['strMensaje']);
                        $entity->setEstado('Activo');
                        $entity->setFeCreacion(new \DateTime('now'));
                        $entity->setFechaDocumento(new \DateTime('now'));
                        $entity->setIpCreacion($arrayParametros['objRequest']->getClientIp());
                        $entity->setUsrCreacion($arrayParametros['objRequest']->getSession()->get('user'));
                        $entity->setEmpresaCod($arrayParametros['objRequest']->getSession()->get('idEmpresa'));

                        $emComunicacion->persist($entity);
                        $emComunicacion->flush();

                        //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el tipo del documento con el servicio y con la
                        //solicitud de PLANIFICACION
                        $entityRelacion = new InfoDocumentoRelacion();

                        $entityRelacion->setModulo($arrayParametros['strTipoModulo']);
                        $entityRelacion->setEstado('Activo');
                        $entityRelacion->setFeCreacion(new \DateTime('now'));
                        $entityRelacion->setUsrCreacion($arrayParametros['objRequest']->getSession()->get('user'));
                        $entityRelacion->setPuntoId($idPunto);
                        $entityRelacion->setServicioId($arrayParametros['intIdServicio']);
                        $entityRelacion->setPersonaEmpresaRolId($punto->getPersonaEmpresaRolId()->getId());

                        $entityRelacion->setDocumentoId($entity->getId());

                        $emComunicacion->persist($entityRelacion);
                        $emComunicacion->flush();

                        //Generar Historial de subida de anexo al servicio
                        if($strBanderaAnexoTecnixo == "S")
                        {
                            $strObservacion = $arrayParametros['strObservacion'];
                        }
                        else
                        {
                            $strObservacion = "<b>" . $arrayParametros['strObservacion'] . "</b>" . ' <b>' .
                                                      $arrayParametros['strTipoModulo'] . '</b>: ' .
                                                      $arrayParametros['strNuevoNombre'];
                        }

                        $strEstado = isset($arrayParametros['strCambioEstado']) ?
                                           $arrayParametros['strCambioEstado'] :
                                           $objServicio->getEstado();

                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objServicio);
                        $objInfoServicioHistorial->setObservacion($strObservacion);
                        $objInfoServicioHistorial->setIpCreacion($arrayParametros['objRequest']->getClientIp());
                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioHistorial->setUsrCreacion($arrayParametros['objRequest']->getSession()->get('user'));
                        $objInfoServicioHistorial->setEstado($strEstado);
                        $emComercial->persist($objInfoServicioHistorial);
                        $emComercial->flush();
                        
                        $emComunicacion->getConnection()->commit();
                        $emComercial->getConnection()->commit();

                        return true;
                    }
                    catch(\Exception $e)
                    {
                        if($emComunicacion->getConnection()->isTransactionActive())
                        {
                            $emComunicacion->getConnection()->rollback();
                            $emComunicacion->getConnection()->close();
                        }
                        if($emComercial->getConnection()->isTransactionActive())
                        {
                            $emComercial->getConnection()->rollback();
                            $emComercial->getConnection()->close();
                        }
                        return false;
                    }
                }
            }
        }
    }

    /**
     * Documentación para la funcion getDocumentosCasoAction().
     *
     * Esta funcion es la encargada de llenar el grid de la consulta de Documentos cargados en prodcutos
     * de Seguridad Logica
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 06-06-2016
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 14-08-2019 | Se modifica lógica para que busque el documento de acuerdo al nombre que
     *                           se recibe por parámetro.
     *
     */
    public function getVerDocumentosAction()
    {
        $arrDocumentos  = array();
        $objResultado   = "";
        $strRespuesta   = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        $request        = $this->getRequest();
        $peticion       = $this->get('request');
        $strIdServicio  = $peticion->get('idServicio') ? $peticion->get('idServicio') : null;
        $strNombreDocumento = $peticion->get('strNombreDocumento') ? $peticion->get('strNombreDocumento') : null;



        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
        $documentoRelacion  = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                             ->findBy(array("servicioId"    => $strIdServicio,
                                                            "estado"        => "Activo"));        
        if(count($documentoRelacion) > 0)
        {
            foreach($documentoRelacion as $documento)
            {
                if ($strNombreDocumento)
                {
                    $archivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                              ->findOneBy(array(
                                                  'id' => $documento->getDocumentoId(),
                                                  'nombreDocumento' => $strNombreDocumento
                                              ));
                }
                else
                {
                    $archivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                              ->find($documento->getDocumentoId());
                }

                if (is_object($archivo))
                {
                    $arrayEncontrados[] = array('ubicacionLogica' => $archivo->getUbicacionLogicaDocumento(),
                        'feCreacion' =>
                            ($archivo->getFeCreacion() ? date_format($archivo->getFeCreacion(), "d-m-Y H:i") : ""),
                        'linkVerDocumento' => $archivo->getUbicacionFisicaDocumento(),
                        'idDocumento' => $archivo->getId());
                }

            }
            $objData        = json_encode($arrayEncontrados);
            $objResultado   = '{"total":"' . count($arrayEncontrados) . '","encontrados":' . $objData . '}';
        }
        else
        {
            $objResultado   = '{"total":"0","encontrados":[]}';
        }

        $strRespuesta->setContent($objResultado);
        return $strRespuesta;
    }

    /**
     * Documentación para la funcion getDocumentosEncontradosAction().
     *
     * Esta funcion es la encargada de retornar el numero de documentos que fueron agregados
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 07-06-2016

     */
    public function getDocumentosEncontradosAction()
    {
        $strRespuesta   = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        $request        = $this->getRequest();
        $peticion       = $this->get('request');
        $strIdServicio  = $peticion->get('idServicio') ? $peticion->get('idServicio') : null;

        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
        $documentoRelacion  = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                             ->findOneBy(array("servicioId" => $strIdServicio,
                                                               "estado"     => "Activo"));
        $total = count($documentoRelacion);
        if($total > 0)
        {
            $arrDocumentos = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                            ->findBy(array("id" => $documentoRelacion->getDocumentoId()));
            $strRespuesta->setContent(json_encode(array('total' => count($arrDocumentos))));
        }
        else
        {
            $strRespuesta->setContent(json_encode(array('total' => $total)));
        }

        return $strRespuesta;
    }
    
    /**
     * Documentación para el método 'getDescargaDocumentos'.
     * Este metodo obtiene los documentos a partir de la url
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 22-06-2016
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.1 22-04-2021 - Se realiza cambio para la descarga del archivo subido al NFS.
     * 
     */
    public function getDescargaDocumentosAction()
    {
        $objRequest = $this->getRequest();
        $idDocumento = $objRequest->get('idDocumento');
        //Buscar el documento
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objArchivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($idDocumento);
        if($objArchivo)
        {
            $strUrl = $objArchivo->getUbicacionFisicaDocumento();
            $arrayUrl = explode("/", $strUrl);
            $file = fopen($strUrl, "r") or die("Unable to open file!");
            $fileSize = filesize($strUrl);
            $strFile = fread($file, $fileSize);
            if(!$strFile)
            {
                $strFile = stream_get_contents($file);
                $fileSize = filesize($strUrl);
            }
            fclose($file);

            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'mime/type');
            $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $arrayUrl[sizeof($arrayUrl) - 1]);
            $objResponse->headers->set('Content-Length', $fileSize);
            $objResponse->setContent($strFile);
            return $objResponse;
        }
        else
        {
            throw $this->createNotFoundException();
        }
    }
    
    /**
     * @Secure(roles="ROLE_13-4357")
     * Documentación para el método 'editarDescPresentaFactAjaxAction'.
     *
     * Funcion que actualiza la descripcion del Servicio a presentarse en la Factura
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 04-07-2016            
     * @param request $request   
     * @param integer $idServicio                 //Id del Servicio
     * @param string  $descripcionPresentaFactura //descripcion del Servicio a Presentarse en la Factura     
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 25-04-2017 Se aumentó la validación para que no se pueda cambiar el nombre de la factura cuando sea 
     *                         'Concentrador L3MPLS Administracion' o 'Concentrador L3MPLS Navegacion'
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 25-04-2017 Se aumentó la validación para que no se pueda cambiar el nombre de la factura cuando sea 
     *                         CANAL TELEFONIA
     */
    public function editarDescPresentaFactAjaxAction()
    {
        $objRespuesta  = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");
        $em            = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $objPeticion                 = $this->get('request');
        $intIdServicio               = $objPeticion->get('idServicio');        
        $strDescPresentaFactura      = $objPeticion->get('descripcionPresentaFactura');
        $strDescPresentaFacturaAntes = "";       

        $em->getConnection()->beginTransaction();
        
        try
        {
            $objInfoServicio = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!$objInfoServicio)
            {
                throw $this->createNotFoundException('No se encontro el Servicio!');
            }
            $strDescPresentaFacturaAntes  = $objInfoServicio->getDescripcionPresentaFactura();            
            
            if($strDescPresentaFacturaAntes == 'Concentrador L3MPLS Administracion'||$strDescPresentaFacturaAntes == 'Concentrador L3MPLS Navegacion'
               ||$strDescPresentaFactura == 'Concentrador L3MPLS Administracion'||$strDescPresentaFactura == 'Concentrador L3MPLS Navegacion')
            {
                $objRespuesta->setContent('ERROR WIFI');
                return $objRespuesta;
            }
            
            if($strDescPresentaFacturaAntes == 'CANAL TELEFONIA' ||$strDescPresentaFactura == 'CANAL TELEFONIA')
            {
                $objRespuesta->setContent('ERROR_CANAL_TELEFONIA');
                return $objRespuesta;
            }            

            $objInfoServicio->setDescripcionPresentaFactura($strDescPresentaFactura);                        
            $em->persist($objInfoServicio);
            $em->flush();
                        
            $objInfoServicioHistorial = new InfoServicioHistorial();
            $objInfoServicioHistorial->setServicioId($objInfoServicio);
            $objInfoServicioHistorial->setObservacion('Se actualizo Descripcion Presenta Factura : <br>'.
                                                'Valor Anterior: <br>'.  
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Desc.Fact:  '.$strDescPresentaFacturaAntes.'<br>'.
                                                'Valores Actuales: <br>'.  
                                                '&nbsp;&nbsp;&nbsp;&nbsp;Desc Fact:  '.$strDescPresentaFactura.'<br>');
            $objInfoServicioHistorial->setIpCreacion($objPeticion->getClientIp());
            $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoServicioHistorial->setAccion('editarDescPresentaFact');
            $objInfoServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
            $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
            $em->persist($objInfoServicioHistorial);
            $em->flush();

            $em->getConnection()->commit();
            $objRespuesta->setContent("OK");
        } 
        catch (\Exception $e)
        {
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $objRespuesta->setContent("Error");
        }
        
        return $objRespuesta;
    }
    
    /**
     * Función que agrega características para el soporte de Seguridad Electronica Video Vigilancia
     * Cliente EMSEGURIDAD CAMARAS PTZ
     *
     * @author Jorge Gómez <jigomez@telconet.ec>
     * @version 1.0 13-03-2023            
     * @param request $request   
     * @param integer $intIdServicio     //Id de Servicio
     * @param integer $intIdProducto     //Id de Producto  
     * @param string $strSerie     //Serie
     * @param string $strMac     //Mac 
     * @param string $strIdCamara     //IdCamara
     * @param string $strAlturaPosteCamara     //Altura de poste 
     * @param string $strTipoPosteCamara     //Tipo de poste
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     */
    
    public function AgregarCaracAction()
    {
        $objRespuesta  = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");

        $objPeticion         = $this->getRequest();
        $intIdEmpresa    = $objPeticion->getSession()->get('idEmpresa');
        $strUser         = $objPeticion->getSession()->get('user');
        $strIpUser       = $objPeticion->getClientIp();

        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $objServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        
        //Obtiene parametros enviados desde el ajax

        $intIdServicio               = $objPeticion->get('idServicio');        
        $intIdProducto               = $objPeticion->get('idProducto');
        $strSerie                    = $objPeticion->get('serieCamara');
        $strMac                      = $objPeticion->get('macCamara');
        $strModelo                   = $objPeticion->get('modeloCamara');
        $strIdCamara                 = $objPeticion->get('idCamara');
        $strAlturaPosteCamara        = $objPeticion->get('alturaPosteCamara');
        $strTipoPosteCamara          = $objPeticion->get('tipoPosteCamara');
        $intIdPersonaEmpresaRol      = $objPeticion->get('idPersonaRol');
        $intIdPunto      = $objPeticion->get('idPunto');

        $objProducto       = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);
        $objInfoServicio   = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        $objInfoPunto      = $emInfraestructura->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);

        $objPersonaEmpresaRolUsr = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
        ->find($intIdPersonaEmpresaRol);

        $objServicioTecnico->generarLoginAuxiliar($objInfoServicio->getId());
        $emComercial->getConnection()->beginTransaction();       

        try
        {
                
            $arrayParametrosAuditoria["intIdPersona"]    = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
            $arrayParametrosAuditoria["strNumeroSerie"]  = $strSerie;
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Activacion Cliente';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            $arrayParametrosAuditoria["strLogin"]        = $objInfoPunto->getLogin();
            $arrayParametrosAuditoria["strUsrCreacion"]  = $strUser;
            $serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);


            $arrayParametrosIngresoNaf["intIdEmpresa"]  = $intIdEmpresa;
            $arrayParametrosIngresoNaf["strSerie"]  = $strSerie;
            $arrayParametrosIngresoNaf["strUser"]  = $strUser;
            $arrayParametrosIngresoNaf["strIpUser"]  = $strIpUser;
            $serviceInfoElemento->ingresaInstalacionNaf($arrayParametrosIngresoNaf);

            
            $strObservacionServicio .= "Equipo Nuevo <br/>";
            $strObservacionServicio .= "Modelo: ".$strModelo."<br/>";
            $strObservacionServicio .= "Serie: ".$strSerie."<br/>";
            $strObservacionServicio .= "Mac: ".$strMac."<br/>";

            $strEstado = $objInfoServicio->getEstado();
            $objServHistServicio   = new InfoServicioHistorial();
            $objServHistServicio->setServicioId($objInfoServicio);
            $objServHistServicio->setObservacion($strObservacionServicio);
            $objServHistServicio->setEstado($strEstado);
            $objServHistServicio->setAccion('Activacion');
            $objServHistServicio->setUsrCreacion($strUser);
            $objServHistServicio->setFeCreacion(new \DateTime('now'));
            $objServHistServicio->setIpCreacion($strIpUser);
            $emComercial->persist($objServHistServicio);
            $emComercial->flush();

            $objServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,
            $objProducto,
            'SERIE_EQUIPO_PTZ',
            $strSerie,
            'jigomez'
            );
            $objServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,
            $objProducto,
            'MAC',
            $strMac,
            'jigomez'
            );
            $objServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,
            $objProducto,
            'ID_CAMARA',
            $strIdCamara,
            'jigomez'
            );
            $objServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,
            $objProducto,
            'ALTURA_POSTE',
            $strAlturaPosteCamara,
            'jigomez'
            );
            $objServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,
            $objProducto,
            'TIPO_POSTE',
            $strTipoPosteCamara,
            'jigomez'
            );
            
            $emComercial->getConnection()->commit();
            $objRespuesta->setContent("OK");
        } 
        catch (\Exception $e)
        {
            $strMensajeError = "Error: " . $e->getMessage();
            error_log($strMensajeError);
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $objRespuesta->setContent("Error");
        }
        
        return $objRespuesta;
    }
    /**
     * Documentación para el método 'consultarAnexoTecnico'.
     * 
     * Esta funcion verifica si hay la caracteristica ANEXO_TECNICO asociado a un producto
     *
     * @author Andres Flores <aoflores@telconet.ec>
     * @version 1.0 11-07-2016
     *
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 05-08-2016
     * Se cambia el nivel de acceso al método y el nombre general 'consultarCaracteristica' al específico 'consultarAnexoTecnico'.
     * Se agrega parámetro del tipo de servicio que se está evaluando
     * Se revueve 'NO' en caso de excepción y de que NO sea un producto.
     * 
     * @param int $intIdProducto        
     * @throws Exception    
     * @return texto de confirmación si encontró la información.
     */
    private function consultarAnexoTecnico($arrayParametros)
    {
        $strTieneAnexo = 'NO';
        try
        {
            if(strtolower($arrayParametros['strTipo']) === 'producto')
            {
                $em              = $this->getDoctrine()->getManager('telconet');
                $objAdmiProducto = $em->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros['intIdProducto']);
                
                if(!$objAdmiProducto)
                {
                    throw $this->createNotFoundException('No se encontro el producto!');
                }

                $objAdmiCaract = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array("descripcionCaracteristica" => $arrayParametros['strTipoAnexo']));
                if(!$objAdmiCaract)
                {
                    throw $this->createNotFoundException('No se encontro la caracteristica');
                }

                $arrayAdmiProdCaract = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                          ->findBy(array("productoId"       => $objAdmiProducto,
                                                         "caracteristicaId" => $objAdmiCaract));
                if(count($arrayAdmiProdCaract) > 0)
                {
                    $strTieneAnexo = "SI";
                }
            }
        } catch (Exception $ex) 
        {
            $strTieneAnexo = "NO";
        }
        return $strTieneAnexo;
    }
    
    
    
    /**
     * Función que verifica la factibilidad de un producto de telefonía
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 11-07-2016
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 17-02-2020    - Consulta si el producto COU LINEAS TELEFONIA FIJA tiene la marca de 
     *                              activación simultánea
     *
     * @param int $idServicio       
     * @throws Exception    
     * @return texto de confirmación si encontró la información.
     */
    
    public function solicitarFactibilidadTelefoniaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
                
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $intServicioId          = $objRequest->get('idServicio');
            $strUser                = $objSession->get('user');
            $strIp                  = $objRequest->getClientIp();
            $serviceTelefonia       = $this->get('tecnico.InfoTelefonia');
            $strPrefijo             = $objSession->get('prefijoEmpresa');
            $serviceInfoServicio    = $this->get('comercial.infoservicio');
            $strActivaSimultanea    = null;
            
            // Consulta si el producto tiene la marca de activación simultánea
            $objInstalacionSimultanea    = $serviceInfoServicio->getIdServicioTradicionalInstalacionCou($intServicioId);
            if ($objInstalacionSimultanea['intIdServTradicional'] !== null)
            {
                $strActivaSimultanea = $objInstalacionSimultanea['intIdServTradicional'];
            }
            
            $arrayParametros    = array('intServicio'   => $intServicioId,
                                     'strIp'            => $strIp,
                                     'strUser'          => $strUser,
                                     'strActivaSim'     => $strActivaSimultanea,
                                     'strPrefijoEmpresa'=> $strPrefijo);
            
            $arrayResult        = $serviceTelefonia->solicitarFactibilidadTelefonia($arrayParametros);            
            
            if($arrayResult['status'] == 'ERROR')
            {
                throw new \Exception($arrayResult['mensaje']);
            }

            $objResponse->setContent("OK");

            return $objResponse;
        }
        catch(\Exception $ex)
        {
            $objResponse->setContent($ex->getMessage());
            return $objResponse;
            
        }
    }
    
    
    /**
     * Funcion que muestra las líneas telefonicas
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 11-07-2016
     *
     * @param int $idServicio       
     * @throws Exception    
     * @return texto de confirmación si encontró la información.
     */
    
    public function verLineasTelefonicasAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        try
        {
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $objRequest     = $this->getRequest();
            $intServicioId  = $objRequest->get('idServicio');
            $objServicio    = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioId);
            

            if(is_object($objServicio))
            {
                $arrayParams['intServicio'] = $objServicio->getId();
                $arrayLineas = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->getLineasTn($arrayParams);                
               
            }

            $intTotal = count($arrayLineas);
                
            if($intTotal > 0)
            {
                $strData = json_encode($arrayLineas);
                $objJson = '{"total":"' . $intTotal . '","encontrados":' . $strData . '}';
            }
            else
            {
                $objJson = '{"total":"0","encontrados":[]}';
            }
            
            $objResponse->setContent($objJson);  
            
            return $objResponse;
        }
        catch(\Exception $ex)
        {
            $objResponse->setContent($ex->getMessage());
            return $objResponse;           
        }
    }
    
    
    /**
     * Funcion que verifica la factibilidad de un producto de telefonia
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 11-07-2016
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 se aumentó el parámetro empresa para la función activar número
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 09-01-2019 Se agrega historial del nuevo número agregado.
     * 
     * @param int $idServicio       
     * @throws Exception    
     * @return texto de confirmación si encontró la información.
     */
    
    public function nuevasLineasTelefonicasAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        try
        {
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $objRequest     = $this->getRequest();
            $objSession     = $objRequest->getSession();
            $intServicioId  = $objRequest->get('idServicio');
            $intCantidad    = $objRequest->get('cantidad');
            $intCanales     = $objRequest->get('canales');
            $strUser        = $objSession->get('user');
            $strIp          = $objRequest->getClientIp();
            $strPrefijo     = $objSession->get('prefijoEmpresa');
            $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
            $serviceActivar = $this->get('tecnico.InfoTelefonia');

            $objServicio    = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioId);

            if(is_object($objServicio))
            {

                $objCaract = $serviceTecnico->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA',
                                                                                $objServicio->getProductoId());

                if(is_object($objCaract))
                {
                    if($objCaract->getValor() != 'FIJA SIP TRUNK' &&  $intCanales> 2)
                    {
                        throw new \Exception('Esta categoria no acepta mas de 2 canales.');
                    }
                    
                    
                    //cuando es analogica se debe validar el numero de puerto del equipo
                    if($objCaract->getValor() == 'FIJA ANALOGA')
                    {
                        $objServicioTecnico = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($intServicioId);
                        
                        if(is_object($objServicioTecnico))
                        {
                            $intElemento = $objServicioTecnico->getElementoClienteId();
                            
                            $objInterface = $emComercial->getRepository("schemaBundle:InfoInterfaceElemento")
                                                        ->findOneBy(array( 'elementoId' => $intElemento,
                                                                           'estado' => 'not connect'));
                            if(!is_object($objInterface))
                            {
                                throw new \Exception('No existen puertos disponibles para esta Línea.');
                            }
                        }

                    }

                }

                $objParametro = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                  ->findOneBy(array("nombreParametro" => 'PARAMETROS_LINEAS_TELEFONIA',
                                                    "estado" => 'Activo'));

                if(is_object($objParametro))
                {
                    $objParametroDominio = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->findOneBy(array( "descripcion" => 'DOMINIO',
                                                                                "parametroId" => $objParametro->getId(),
                                                                                'valor1'      => $objCaract->getValor(),
                                                                                "estado"      => 'Activo'));

                    if(is_object($objParametroDominio))
                    {
                        $strParametroDominio = $objParametroDominio->getValor2();
                    }
                }
                
                $objSpcCuenta = $serviceTecnico
                                     ->getServicioProductoCaracteristica($objServicio, 'ID CUENTA NETVOICE', $objServicio->getProductoId());

                if(is_object($objSpcCuenta))
                {
                    $intCuentaNetvoice = $objSpcCuenta->getValor();
                }
                else
                {
                    throw new \Exception('No hay cuenta de netvoice.');
                }           
                
                for($i = 0; $i < $intCantidad; $i++)
                {
                    $arrayPeticiones['intIdServicio']       = $objServicio->getId();
                    $arrayPeticiones['strDominio']          = $strParametroDominio;
                    $arrayPeticiones['intCanales']          = $intCanales;
                    $arrayPeticiones['strUser']             = $strUser;
                    $arrayPeticiones['strIpClient']         = $strIp;
                    $arrayPeticiones['intCuentaNetvoice']   = $intCuentaNetvoice;
                    $arrayPeticiones['strPrefijoEmpresa']   = $strPrefijo;                    

                    //asigno cada línea
                    $arrayResult = $serviceActivar->asignarLineaTN($arrayPeticiones);

                    if($arrayResult['status'] == 'ERROR')
                    {
                        throw new \Exception($arrayResult['mensaje']);
                    }

                    //luego procedo a activar las líneas 
                    $arrayActivarNumero['intElemento']      = $intElemento;
                    $arrayActivarNumero['intSpc']           = $arrayResult['intNumero'];
                    $arrayActivarNumero['strPrefijoEmpresa']= $strPrefijo;
                    //envio el numero a activar y el puerto que va a ocupar
                    $arrayResultActivar = $serviceActivar->activarNumero($arrayActivarNumero);                    
                
                    if($arrayResultActivar['status'] != 'OK')
                    {
                        throw new \Exception('Error '.$arrayResultActivar['mensaje']);
                    }

                }                
                
                //cambio el ancho de banda
                $arrayCambioBw = array();

                $arrayCambioBw['objServicio']       = $objServicio;
                $arrayCambioBw['intCapacidadNueva'] = $intCantidad * 100;
                $arrayCambioBw['strOperacion']      = '+';
                $arrayCambioBw['usrCreacion']       = $strUser;
                $arrayCambioBw['ipCreacion']        = $strIp;

                $arrayCambio = $serviceActivar->cambioAnchoBanda($arrayCambioBw);

                if($arrayCambio['status'] == 'ERROR')
                {
                    throw new \Exception($arrayCambio['mensaje']);
                }
                
                //REGISTRA EN LA TABLA DE HISTORIAL
                $entityServicioHistorial = new InfoServicioHistorial();
                $entityServicioHistorial->setServicioId($objServicio);
                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                $entityServicioHistorial->setUsrCreacion($strUser);
                $entityServicioHistorial->setIpCreacion($strIp);
                $entityServicioHistorial->setObservacion('Se agregó el número '.$arrayResult['strTelefono']);
                $entityServicioHistorial->setEstado($objServicio->getEstado());
                $emComercial->persist($entityServicioHistorial);
                $emComercial->flush();           

            }

            $objResponse->setContent("OK");

            return $objResponse;
        }
        catch(\Exception $ex)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $objResponse->setContent($ex->getMessage());
            return $objResponse;            
        }
    }    

    /**
     * solicitarFactibilidadGeneralAction, metodo que hace el llamado a los procesos de factibilidad.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 12-06-2017 - Se valida que si es SATELITAL no pregunte si se desea escoger misma UM
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 18-09-2017 - Cuando se trate de un flujo de DATACENTER se generara factibilidad Manual
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.3 23-01-2018 - Cuando se trate de un flujo para el producto 'INTERNET SMALL BUSINESS' se genera factibilidad General.
     * 
     * @author Allan Suarez C <arsuarez@telconet.ec>
     * @version 1.4 23-02-2018 - Se valida que cuando un servicio tenga caracteristica de migracion de informacion de Fact. no realice
     *                           busqueda de Factibilidad automatica
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.5 14-02-2019 Se válida que no se duplique una solicitud de factibilidad, que se encuentre en estado: Factible o Prefactibilidad
     * @since 1.4
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 04-02-2019 - Cuando se trate de un flujo para el producto TELCOHOME se debe generar sólo factibilidad General.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.7 12-11-2019 - Se agrega lógica para la creación de un concentrador virtual de interconexion de forma automática
     *                           siempre y cuando el producto a activar sea Datos Fwa.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 25-05-2021 - Se valida que los servicios con producto Datos SafeCity, sigan el flujo de factibilidad general.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 14-06-2021 Se modifica el envió de parámetros del método getServiciosPorUmTipoEnlace en un solo arreglo
     *
     */
    public function solicitarFactibilidadGeneralAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intServicioId      = $objRequest->get('id');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $arrayPuntoSession  = $objSession->get('ptoCliente');
        $intIdPuntoSession  = $arrayPuntoSession['id'];
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
        
        $intUltimaMillaId   = 0;
        $strTipoEnlace      = '';
        
        $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicioId);
        if ($objServicioTecnico)
        {
            $intUltimaMillaId = $objServicioTecnico->getUltimaMillaId();
            $strTipoEnlace    = substr($objServicioTecnico->getTipoEnlace(), 0, 9);
        }

        $strTipoRed  = "";
        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioId);
        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
        {
            //obtener tipo red
            $objCaractTipoRed = $serviceTecnico->getServicioProductoCaracteristica($objServicio, 'TIPO_RED',
                                                                                   $objServicio->getProductoId());
            if(is_object($objCaractTipoRed))
            {
                $strTipoRed = $objCaractTipoRed->getValor();
            }
        }

        // Obtenemos cantidad de servicios que posean la misma ultima milla y tipo de enlace del servicio pasado como parámetro
        
        $arrayServiciosUM = $emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->getServiciosPorUmTipoEnlace(array("intIdEmpresa"     => $intIdEmpresa,
                                                                            "strTipoRed"       => $strTipoRed,
                                                                            "intIdPunto"       => $intIdPuntoSession,
                                                                            "intUltimaMillaId" => $intUltimaMillaId,
                                                                            "strTipoEnlace"    => $strTipoEnlace));
        $intTotalServicios = $arrayServiciosUM['total'];  
        
        $boolPreguntaMismaUm = true;
        
        if(is_object($objServicio))
        {
            $boolEsPseudoPe = $emComercial->getRepository("schemaBundle:InfoServicio")->esServicioPseudoPe($objServicio);
            
            //Si es PseudoPe no pregunta por utilizacion de misma UM
            if($boolEsPseudoPe)
            {
                $boolPreguntaMismaUm = false;
            }
            
            //Verificar si es servicio Migrado
            $objCaract = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                            'INTERCONEXION_CLIENTES',
                                                                            $objServicio->getProductoId()
                                                                            );
            if(is_object($objCaract) && $objCaract->getValor()=='S')
            {
                $boolPreguntaMismaUm = false;
            }
        }
        
        $arrayTipoFactFiltrar       = array('PreFactibilidad','Factible');
                    
        $entityTipoSolicitud        = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");

        $arrayParametrosSolicitudes = array('servicioId'        => $intServicioId,
                                            'tipoSolicitudId'   => $entityTipoSolicitud->getId(),
                                            'estado'            => $arrayTipoFactFiltrar);

        $entityDetalleSolFac        = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                  ->findOneBy($arrayParametrosSolicitudes);

        if((isset($entityDetalleSolFac) && !empty($entityDetalleSolFac)))
        {
            $objResponse->setContent("factibilidadREPETIDA");
        }
        else
        {
            //Cuando existe mas de un servicio dentro de un Punto se pregunta si desea utilizar otra UM similar,para
            //el caso de VSAT no utiliza ultimas millas similares
            if("TN" === $strPrefijoEmpresa && $intTotalServicios >= 1  
                && ($objServicio->getProductoId()->getNombreTecnico() !== 'INTERNET SMALL BUSINESS'
                && $objServicio->getProductoId()->getNombreTecnico() !== 'TELCOHOME')
                && $objServicio->getProductoId()->getNombreTecnico() !== 'DATOS FWA'
                && $objServicio->getProductoId()->getNombreTecnico() !== 'DATOS SAFECITY')
            {
                if($boolPreguntaMismaUm)
                {
                    $objResponse->setContent("factibilidadUM");   
                }
                else
                {
                    $objResponse->setContent("factibilidadGen");
                }
            }
            else
            {
                $objResponse->setContent("factibilidadGen");                     
            }
        }
        
        return $objResponse;           
    }
    
    /**
     * getServiciosUMAction, metodo que carga el listado de servicios con la misma ultima milla y tipo de enlace.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 28-09-2017 - Se ajusta para que no muestre como servicios a heredar misma UM los que se encuentren en DC
     *                           dado que no es Flujo de la Metro
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 29-10-2018 Se realiza validación para que no considere ultima milla cuando es un servicio CANAL TELEFONIA
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 05-11-2018 Se realiza validación para que considere ultima milla TER cuando es un servicio CANAL TELEFONIA
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.4 02-01-2019 Se realice ajuste para que se puede utilizar misma última milla para enlaces DC dentro de una misma solución.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.5 27-01-2020 Se permite escoger Ultima Milla de un enlace Backup para Internet Sdwan.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 11-02-2021 Para Internet SDWAN se permite seleccionar misma UM de grupos distintos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 14-06-2021 Se modifica el envió de parámetros del método getServiciosPorUmTipoEnlace en un solo arreglo
     *
     * @author Joel Muñoz <jrmunoz@telconet.ec>
     * @version 1.9 28-07-2022 Se agrega funcionalidad para obtener de forma dinámica los productos SDWAN que pueden ser utilizados para aplicar UM
     * 
     * @author Joel Muñoz <jrmunoz@telconet.ec>
     * @version 2.0 12-12-2022 Se agregó funcionalidad para devolver la UM únicamente del servicio a migrar cuando es una migración SDWAN
     */
    public function getServiciosUMAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();   
        $objResponse->headers->set('Content-Type', 'text/json');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPuntoSession  = $objSession->get('ptoCliente');
        $intPuntoSessionId  = $arrayPuntoSession['id'];  
        $intEmpresaId       = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intServicioId      = $objRequest->get('intIdServicio');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
        $intUltimaMillaId   = 0;
        $strTipoEnlace      = '';
        
        $objUmFo    = $emComercial->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio('FO');
        $objUmUtp   = $emComercial->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio('UTP');
        $objUmTer   = $emComercial->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio('TER');
                
        $objServicioNuevo = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
        $objProductoNuevo = $objServicioNuevo->getProductoId();
        
        // Obtengo la UM del servicio enviado como parámetro
        $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicioId);
        
        if ($objServicioTecnico)
        {
            if($objServicioNuevo->getDescripcionPresentaFactura() == 'CANAL TELEFONIA')
            {
                if(is_object($objUmFo) && is_object($objUmUtp))
                {
                    $intUltimaMillaId = array($objUmFo->getId(), $objUmUtp->getId(), $objUmTer->getId());
                }
            }
            else
            {
                $intUltimaMillaId = array($objServicioTecnico->getUltimaMillaId());
            }
            
            $strTipoEnlace    = substr($objServicioTecnico->getTipoEnlace(), 0, 9);
        }
        
        $strGrupo          = '';
        $intNumeroSolucion = 0;
        $boolEsDC          = false;
        
        if(is_object($objProductoNuevo))
        {
            $strGrupo = $objProductoNuevo->getGrupo();
        }
        
        if($strGrupo == 'DATACENTER')
        {
            $objCaract = $serviceTecnico->getServicioProductoCaracteristica($objServicioNuevo,
                                                                            'SECUENCIAL_GRUPO',
                                                                            $objServicioNuevo->getProductoId()
                                                                            );
            if(is_object($objCaract))
            {
                $boolEsDC          = true;
                $intNumeroSolucion = $objCaract->getValor();
            }
        }

        $strTipoRed  = "";
        if(is_object($objServicioNuevo) && is_object($objServicioNuevo->getProductoId()))
        {
            //obtener tipo red
            $objCaractTipoRed = $serviceTecnico->getServicioProductoCaracteristica($objServicioNuevo, 'TIPO_RED',
                                                                                   $objServicioNuevo->getProductoId());
            if(is_object($objCaractTipoRed))
            {
                $strTipoRed = $objCaractTipoRed->getValor();
            }
        }

        // Obtenemos listado de servicios que posean la misma ultima milla y tipo de enlace del servicio pasado como parámetro
        // Creamos array pasando el dato cuando el producto en sdwan y permita escoger backup
        $arrayServiciosUM = $emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->getServiciosPorUmTipoEnlace(array("intIdEmpresa"     => $intEmpresaId,
                                                                            "strTipoRed"       => $strTipoRed,
                                                                            "intIdPunto"       => $intPuntoSessionId,
                                                                            "intUltimaMillaId" => $intUltimaMillaId,
                                                                            "strTipoEnlace"    => $strTipoEnlace));

        // SE OBTIENE VALOR1 DE PARAMETRO_DET
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $arrayDataPunto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('LISTA_PRODUCTOS_SDWAN_MIGRACIONES',
                'COMERCIAL',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '');

        $arrayDescripClearChannel = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('ESTADO_CLEAR_CHANNEL',
                'COMERCIAL',
                '',
                'ESTADO_CLEAR_CHANNEL',
                '',
                '',
                '',
                '',
                '',
                $intEmpresaId);
        

        //SE VALIDA Y FORMATEA COMO JSON
        if (is_array($arrayDataPunto) && array_key_exists('valor1', $arrayDataPunto))
        {
            $arrayDataPunto = json_decode($arrayDataPunto['valor1'], true);
        }
        else
        {
            $arrayDataPunto = array();
        }


        if (is_object($objProductoNuevo)
            && !empty($objProductoNuevo)
            && in_array($objProductoNuevo->getNombreTecnico(),
                $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->array_column($arrayDataPunto, 'nombre')))
        {
            $strTipoEnlace    = "BACKUP";
            $arrayServiciosUMB = $emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->getServiciosPorUmTipoEnlace(array("intIdEmpresa"     => $intEmpresaId,
                                                                            "intIdPunto"       => $intPuntoSessionId,
                                                                            "intUltimaMillaId" => $intUltimaMillaId,
                                                                            "strTipoEnlace"    => $strTipoEnlace));
            if(!isset($arrayServiciosUMB['registros']) || $arrayServiciosUMB['total']>0)
            {
                $arrayServiciosUM['registros'] = array_merge($arrayServiciosUM['registros'], $arrayServiciosUMB['registros']);
            }
        }
        $serviciosUM = $arrayServiciosUM['registros'];

        if(count($serviciosUM) > 0)
        {
            foreach($serviciosUM as $objServicio):
                
                $objProducto=$emComercial->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                $boolDescartoServicio = true;
                if($intEmpresaId == '10')
                {
                    $boolDescartoServicio = $arrayDescripClearChannel["valor1"] != $objProducto->getDescripcionProducto();
                }
                
                $strGrupoServicioExistente = $objProducto->getGrupo();
                if($boolDescartoServicio)
                {
                    //No solo validar misma ultima milla sino validar el grupo del producto dado que no se puede mezclar
                    //con servicios entre DC y la METRO
                    if($strGrupoServicioExistente == $strGrupo ||
                    (is_object($objProductoNuevo) && $objProductoNuevo->getNombreTecnico()=='INTERNET SDWAN' ) ||
                    (is_object($objProductoNuevo) && $objProductoNuevo->getNombreTecnico()=='L3MPLS SDWAN' ))
                    {
                        //Si se trata de datacenter sólo se analizará y mostrará los servicios de comunicaciones que se encuentren en la misma solución
                        if($boolEsDC)
                        {
                            $objCaractServicioPto = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                        'SECUENCIAL_GRUPO',
                                                                                                        $objServicio->getProductoId()
                                                                                                        );
                            if(is_object($objCaractServicioPto))
                            {                        
                                if($intNumeroSolucion != $objCaractServicioPto->getValor())
                                {
                                    //Si se trata de DC y si no es la misma solución no mostrará la información del enlace dado que siempre
                                    //debe ser la misma solución
                                    continue;
                                }
                            }
                            else
                            {
                                //si es Datacenter y no tiene registrado un número de solución, continúa dado que no se encuentra regularizado
                                continue;
                            }
                        }
                 
                        $strDescripcionProducto = $objProducto->getDescripcionProducto();  

                        $strUltimaMilla             = '';
                        $objInfoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($objServicio->getId());
                        if ($objInfoServicioTecnico)
                        {
                            if($objInfoServicioTecnico->getUltimaMillaId()!=null)
                            {
                                $objUltimaMilla = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->find($objInfoServicioTecnico->getUltimaMillaId());
                                $strUltimaMilla = $objUltimaMilla->getNombreTipoMedio();
                            }
                        }  
    
                        //
                        $objCaractEsMigracionSDWAN = $serviceTecnico->getServicioProductoCaracteristica(
                                                                                        $objServicioNuevo,
                                                                                        'Migración de Tecnología SDWAN',
                                                                                        $objProductoNuevo);

                        if(is_object($objCaractEsMigracionSDWAN) && $objCaractEsMigracionSDWAN->getValor() == 'S')
                        {
                            if($objProductoNuevo->getNombreTecnico()=='INTERNET SDWAN')
                            {
                                $booleanSdwanIntMpls  = $objServicio->getProductoId()->getNombreTecnico() === 'INTMPLS';
                                $booleanSdwanInternet = ($objServicio->getProductoId()->getNombreTecnico() === 'INTERNET' &&
                                    strtoupper($objServicio->getProductoId()->getDescripcionProducto()) === 'INTERNET DEDICADO');
                                if(!$booleanSdwanIntMpls && !$booleanSdwanInternet)
                                {
                                    continue;
                                }
                            }
                            if($objProductoNuevo->getNombreTecnico()=='L3MPLS SDWAN'
                               && $objServicio->getProductoId()->getNombreTecnico() != 'L3MPLS')
                            {
                                continue;
                            }
                            //
                            $objServicioExistSdwan = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->createQueryBuilder('car')
                                        ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                                                'pc.id = car.productoCaracterisiticaId')
                                        ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                                        ->where('car.valor = :idServicioMigrado')
                                        ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                                        ->andWhere("car.estado = :estadoActivo")
                                        ->setParameter('idServicioMigrado', $objServicio->getId())
                                        ->setParameter('desCaracteristica', "SERVICIO_MIGRADO_SDWAN")
                                        ->setParameter('estadoActivo', 'Activo')
                                        ->setMaxResults(1)
                                        ->getQuery()
                                        ->getOneOrNullResult();
                            if(is_object($objServicioExistSdwan))
                            {
                                continue;
                            }

                            //
                            $objCaractCantidadUsuSDWAN = $serviceTecnico->getServicioProductoCaracteristica(
                                                                                        $objServicioNuevo,
                                                                                        'CANTIDAD USUARIOS SDWAN',
                                                                                        $objProductoNuevo);
                            //
                            $objCaractCapacidadSDWAN  = $serviceTecnico->getServicioProductoCaracteristica(
                                                                                        $objServicioNuevo,
                                                                                        'CAPACIDAD1',
                                                                                        $objProductoNuevo);
                            if(!is_object($objCaractCantidadUsuSDWAN) || !is_object($objCaractCapacidadSDWAN))
                            {
                                continue;
                            }
                            //
                            $arrayParametros = array();
                            $arrayParametros['idServicio']        = $objServicio->getId();
                            $arrayParametros['idPunto']           = $objServicio->getPuntoId()->getId();
                            $arrayParametros['strCantidadUsuarios']  = $objCaractCantidadUsuSDWAN->getValor();
                            $arrayParametros['strCapacidadUsuarios'] = $objCaractCapacidadSDWAN->getValor();
            
                            //verificar
                            $serviceInfoServicio        = $this->get('comercial.InfoServicio');
                            $arrayServicioCumpleSDWAN = $serviceInfoServicio->validarMigracionServicioSDWAN($arrayParametros);
                            if($arrayServicioCumpleSDWAN['esValido'] === false)
                            {
                                continue;
                            }
                        }
                        $arrayServiciosUm[] = array(
                                                    'intIdServicio'   => $objServicio->getId(),
                                                    'strLoginAux'     => $objServicio->getLoginAux(),
                                                    'strDescProducto' => $strDescripcionProducto,
                                                    'strDescFactura'  => $objServicioNuevo->getDescripcionPresentaFactura(),
                                                    'strUltimaMilla'  => $strUltimaMilla,
                                                    'strEstado'       => $objServicio->getEstado()
                                                    );
                    }
                }
            endforeach;
            $intTotal = count($arrayServiciosUm);    
            if($intTotal > 0)
            {
                $data = json_encode($arrayServiciosUm);
                $objJson = '{"total":"' . $intTotal . '","jsonServiciosUM":' . $data . '}';
            }
            else
            {
                $objJson = '{"total":"0","jsonServiciosUM":[]}';
            }
            $objResponse->setContent($objJson);
            
            return $objResponse;            
            
        }  
        return $objResponse->setContent('No existen servicios para generar factibilidad.');
    }
    
    /**
     * getCamarasPorPuntoAction
     * 
     * @author Versión Inicial
     * @version 1.0
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 01-08-2022 - Se cambia el parámetro para obtener el id del producto de la cámara safecity.
     * 
     * @return Response $obResponse
     */
    public function getCamarasPorPuntoAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();   
        $objResponse->headers->set('Content-Type', 'text/json');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPuntoSession  = $objSession->get('ptoCliente');
        $intPuntoSessionId  = $arrayPuntoSession['id'];  
        $intEmpresaId       = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');

        if(!empty($intPuntoSessionId))
        {
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intPuntoSessionId);
        }

        //Se consulta el servicio adicional DATOS GPON
        $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                        'COMERCIAL',
                                                        '',
                                                        '',
                                                        'PRODUCTO_ADICIONAL_CAMARA',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $intEmpresaId);

        if(!empty($arrayParametrosDet["valor2"]) && isset($arrayParametrosDet["valor2"]))
        {
            $intIdProductoCamaraSafecity = $arrayParametrosDet["valor2"];
        }

        if(!empty($intIdProductoCamaraSafecity))
        {
            $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoCamaraSafecity);
        }

        $arrayEstadosPermitidos = array();
        $arrayParametrosEstados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                '',
                                                                                                'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '');
        foreach($arrayParametrosEstados as $arrayDetalles)
        {
            $arrayEstadosPermitidos[] = $arrayDetalles['valor2'];
        }

        $objInfoServicioCamaras = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findBy(array("puntoId"    => $objInfoPunto,
                                                             "productoId" => $objAdmiProducto));

        foreach($objInfoServicioCamaras as $objInfoServicio)
        {
            $objSerCaractMascarilla = $serviceTecnico->getServicioProductoCaracteristica($objInfoServicio, 'RELACION_MASCARILLA_CAMARA_SAFECITY',
                                                                                         $objInfoServicio->getProductoId());
            if(!is_object($objSerCaractMascarilla) && !in_array($objInfoServicio->getEstado(),$arrayEstadosPermitidos))
            {
                $arrayCamarasActivas[] = array('intIdServicio'   => $objInfoServicio->getId(),
                                               'strLoginAux'     => $objInfoServicio->getLoginAux(),
                                               'strDescProducto' => $objInfoServicio->getProductoId()->getDescripcionProducto(),
                                               'strEstado'       => $objInfoServicio->getEstado());
            }
        }
            
        $intTotal = count($arrayCamarasActivas);    
        if($intTotal > 0)
        {
            $objData = json_encode($arrayCamarasActivas);
            $objJson = '{"total":"' . $intTotal . '","jsonServiciosCAMARAS":' . $objData . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonServiciosCAMARAS":[]}';
        }
        $objResponse->setContent($objJson);

        return $objResponse;
    }     
    
    
    /**
     * generarFactibilidadUMAction, método que genera solicitud de factibilidad usando UM de servicio existente.
     * 
     * @return \Symfony\Component\HttpFoundation\Response    
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 26-07-2016 - Se modifica para que envie service tecnico y replicar caracteristica TIPO_FACTIBILIDAD en nuevo servicio
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 27-07-2016 Se agregan parametros necesarios enviados para la generación de orden de trabajo y solicitud de planificación   
     */
    public function generarFactibilidadUMAction()
    {     
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->getDoctrine()->getManager("telconet");

        $emComercial->getConnection()->beginTransaction();    
        
        $serviceInfoServicio = $this->get('comercial.InfoServicio');        
        
        $arrayParametros = array ();
        $arrayParametros['intEmpresaId']        = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']   = $objSession->get('prefijoEmpresa');  
        $arrayParametros['intServicioOrigenId'] = $objRequest->get('idServicioOrigen');  
        $arrayParametros['intServicioUMId']     = $objRequest->get('idServicioUm');  
        $arrayParametros['strUsrCreacion']      = $objSession->get('user');  
        $arrayParametros['strClienteIp']        = $objRequest->getClientIp();  
        $arrayParametros['intOficinaId']        = $objSession->get('idOficina');
        $arrayPuntoSession                      = $objSession->get('ptoCliente');
        $arrayParametros['intPuntoId']          = $arrayPuntoSession['id'];          
        $arrayParametros['serviceTecnico']      = $this->get('tecnico.InfoServicioTecnico');

       
        try
        {
            $strContent = $serviceInfoServicio->generarFactibilidadServicioMismaUm($arrayParametros); 
        }
        catch(\Exception $e)
        {
            $strContent = 'Error: <br>' . $e->getMessage();
        }
        $objResponse->setContent($strContent); 
        
        return $objResponse;             
    }    
    
     
    /**
     * asociarMascarillasACamarasAction, método que genera solicitud de factibilidad usando UM de servicio existente.
     * 
     * @return \Symfony\Component\HttpFoundation\Response    asociarMascarillasACamaras
     * 
     */
    public function asociarMascarillasACamarasAction()
    {     
        $objResponse   = new JsonResponse;
        $arrayResponse = array();
        $strStatus     = "OK";
        $strMensaje    = "";
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();        
 
        
        $serviceInfoServicio = $this->get('comercial.InfoServicio');        
        
        $arrayParametros = array ();
        $arrayParametros['intEmpresaId']          = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']     = $objSession->get('prefijoEmpresa');  
        $arrayParametros['intServicioOrigenId']   = $objRequest->get('idServicioOrigen');  
        $arrayParametros['intIdServicioCamara']   = $objRequest->get('idServicioCamara');  
        $arrayParametros['strTipoDeServicio']     = "SERVICIO_MASCARILLA";  
        $arrayParametros['strUsrCreacion']        = $objSession->get('user');  
        $arrayParametros['strIpCreacion']         = $objRequest->getClientIp();  
        $arrayParametros['strDepartamentoOrigen'] = $objSession->get('idDepartamento');
         
        try
        {
            $arrayRespuesta = $serviceInfoServicio->relacionarServicioACamara($arrayParametros);
            
            $strStatus  = $arrayRespuesta["status"];
            $strMensaje = $arrayRespuesta["respuesta"];
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Ocurrio un error al momento de asociar el servicio mascarilla a la camara, Favor notificar a Sistemas";
        }
        
        $arrayResponse["status"]  = $strStatus;
        $arrayResponse["mensaje"] = $strMensaje;
                
        $objResponse->setData($arrayResponse);
        
        return $objResponse;                 
    }   
    
    
    /**
     * Documentación para el método 'gridAjaxHistorialClienteAction'.
     *
     * Retorna listado del historial del punto.
     *
     * @param int $intIdPunto IdPunto del Cliente o Precliente
     *
     * @return Response listado con el historial del punto.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 08-08-2016
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 14-08-2019 Se agrega envío de acción a ser visualizada en historial del punto.
     */
    public function gridHistorialAction($intIdPunto)
    {
        $jsonResponse   = new JsonResponse();
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $arrayResultado = array("total" => 0, "registros" => array());
        $intContador    = 0;
        
        $arrayInfoPuntoHistorial = $emComercial->getRepository('schemaBundle:InfoPuntoHistorial')->findByPuntoId($intIdPunto);
        
        if( !empty($arrayInfoPuntoHistorial) )
        {
            foreach( $arrayInfoPuntoHistorial as $objInfoPuntoHistorial )
            {
                $arrayItem              = array();
                $arrayItem["detalle"]   = $objInfoPuntoHistorial->getValor();
                $arrayItem["accion"]    = $objInfoPuntoHistorial->getAccion();
                $arrayItem["usuario"]   = $objInfoPuntoHistorial->getUsrCreacion();
                $arrayItem["fecha"]     = $objInfoPuntoHistorial->getFeCreacion()
                                          ? strval(date_format($objInfoPuntoHistorial->getFeCreacion(), "d/m/Y G:i")) : '';
                
                $arrayResultado["registros"][] = $arrayItem;
                
                $intContador++;
            }//foreach( $arrayInfoPuntoHistorial as $objInfoPuntoHistorial )
            
            $arrayResultado["total"] = $intContador;
        }//( !empty($arrayInfoPuntoHistorial) )
        
        
        $jsonResponse->setData($arrayResultado);
        
        return $jsonResponse;
    }

    /**
     * ajaxValidaTrasladoMdAction
     * 
     * Funcion encargada de obtener la característica DIFERENTE TECNOLOGIA FACTIBILIDAD para poder mostrar la pantalla para la entrega de equipos
     * en caso de factiblidad por traslado en diferente tecnología
     *
     * @author Creado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 11-01-2022 Version Inicial
     *
     * return JSON $objResponse
     */
    public function ajaxValidaTrasladoMdAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $objResponse = new JsonResponse();
        $emComercial = $this->getDoctrine()->getManager();
        $serviceUtil = $this->get('schema.Util');
        $strIpCreacion      = $objRequest->getClientIp();
        $intIdServicio      = $objRequest->get('idServicio');
        $strUsrCreacion     = $objSession->get('user');
        $strCodigoEmpresa   = $objSession->get('idEmpresa');
        $strEstadoRespuesta = "OK";
        $strMensajeRespuesta      = "";
        $strDiferenteTecnologia   = "NO";
        $serviceServicioComercial = $this->get('comercial.InfoServicio');
        try
        {
            $arrayParametrosCaracteristica = array();
            $arrayParametrosCaracteristica['intIdServicio']     = $intIdServicio;
            $arrayParametrosCaracteristica['strCaracteristica'] ='DIFERENTE TECNOLOGIA FACTIBILIDAD';
            $arrayParametrosCaracteristica['strCodEmpresa']     = $strCodigoEmpresa;
            $arrayParametrosCaracteristica['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosCaracteristica['strIpCreacion']     = $strIpCreacion;
            $objServProdCaractFactibilidad = $serviceServicioComercial->obtieneProductoCaracteristicaInternet($arrayParametrosCaracteristica);
            if(is_object($objServProdCaractFactibilidad))
            {
                $strDiferenteTecnologia = $objServProdCaractFactibilidad->getValor();
                $strMensajeRespuesta    = "Factibilidad de diferente tecnología";
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.ajaxValidaTrasladoMdAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
            $strEstadoRespuesta  = "ERROR";
            $strMensajeRespuesta = "Se presento un error al obtener la característica de factibilidad";
        }
        $objResponse->setData(array('strStatus'              => $strEstadoRespuesta,
                                    'strMensaje'             => $strMensajeRespuesta,
                                    'strDiferenteTecnologia' => $strDiferenteTecnologia));
        return $objResponse;
    }

    /**
     * ajaxGetElementosRetirarTrasladoAction
     * 
     * Funcion encargada de obtener los elementos que el cliente debe entregar al realizar un traslado
     * en diferente tecnología MD
     *
     * @author Creado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 11-01-2022 Version Inicial
     *
     * return JSON $objResponse
     */
    public function ajaxGetElementosRetirarTrasladoAction()
    {
        $intCount     = 0;
		$objRespuesta = new Response();
        $objRequest   = $this->get('request');
        $objSession   = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $intIdServicio    = $objRequest->get('idServicio');
        $strResultado     = '{"total":"0","encontrados":[{}]}';
        $strIpCreacion    = $objRequest->getClientIp();
        $strUsrCreacion   = $objSession->get('user');
        $strCodigoEmpresa = $objSession->get('idEmpresa');
        $serviceServicio  = $this->get('comercial.InfoServicio');
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            $arrayParametros = array();
            $arrayParametros['intIdServicio']  = $intIdServicio;
            $arrayParametros["strIpCreacion"]  = $strIpCreacion;
            $arrayParametros["strUsrCreacion"] = $strUsrCreacion;
            $arrayParametros["strCodEmpresa"]  = $strCodigoEmpresa;
            $arrayElementosRetirar = $serviceServicio->getElementosRetirarTraslado($arrayParametros);
            $strResultado =json_encode($arrayElementosRetirar);
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.ajaxGetElementosRetirarTrasladoAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
        }
        $objRespuesta->setContent($strResultado);
        return $objRespuesta;
    }

    /**
     * ajaxGrabaEquiposEntregadosAction
     * 
     * Funcion encargada de guardar información de elementos entregados por parte del cliente
     * en diferente tecnología MD
     *
     * @author Creado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 11-01-2022 Version Inicial
     *
     * return JSON $objResponse
     */
    public function ajaxGrabaEquiposEntregadosAction()
    {
        $objRespuesta = new Response();
        $emComercial    = $this->getDoctrine()->getManager();
        $emSoporte      = $this->get('doctrine')->getManager('telconet_soporte');
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $intIdServicio  = $objRequest->get('idServicio');
        $strDatosElementos = $objRequest->get('datosElementos');
        $strIpCreacion     = $objRequest->getClientIp();
        $strUsrCreacion    = $objSession->get('user');
        $strCodigoEmpresa             = $objSession->get('idEmpresa');
        $serviceInfoElemento          = $this->get('tecnico.InfoElemento');
        $serviceServicioComercial     = $this->get('comercial.InfoServicio');
        $arrayRespuesta               = array();
        $arrayRespuesta['strEstado']  = 'ERROR';
        $strRegistrarDetalleTarea     = 'NO';
        $arrayRespuesta['strMensaje'] = 'Ocurrieron problemas al registrar la información.';
        $strObservacionTarea          = "Entrega de equipos : ";
        $strEquiposObservacionTarea   = "Equipos : </br>";
        $strEquiposTarea              = "PARCIAL";
        $intContadorElementosEntregados = 0;
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            $objAdmiCaracTarea = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => "ID_TAREA_TRASLADO",
                                                               "estado"                    => "Activo"));
            if(!is_object($objAdmiCaracTarea) && empty($objAdmiCaracTarea))
            {
                throw new \Exception("No existe Objeto para la característica ID_TAREA_TRASLADO");
            }
            $objAdmiCaracElemento = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO",
                                                                  "estado"                    => "Activo"));
            if(!is_object($objAdmiCaracElemento) && empty($objAdmiCaracElemento))
            {
                throw new \Exception("No existe Objeto para la característica ELEMENTO");
            }
            $objAdmiCaracObs = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "OBSERVACION",
                                                             "estado"                    => "Activo"));
            if(!is_object($objAdmiCaracObs) && empty($objAdmiCaracObs))
            {
                throw new \Exception("No existe Objeto para la característica OBSERVACION");
            }
            $objAdmiCaracEstado = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array("descripcionCaracteristica" => "ESTADO",
                                                                "estado"                    => "Activo"));
            if(!is_object($objAdmiCaracEstado) && empty($objAdmiCaracEstado))
            {
                throw new \Exception("No existe Objeto para la característica ESTADO");
            }
            $objTipoSolicitudRegEle = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneBy(array("descripcionSolicitud" => "SOLICITUD REGISTRO ELEMENTOS",
                                                                    "estado"               => "Activo"));
            if(!is_object($objTipoSolicitudRegEle) && empty($objTipoSolicitudRegEle))
            {
                throw new \Exception("No existe Objeto para el tipo de Solicitud Registro Elementos");
            }

            $arrayParametrosCaracteristica = array();
            $arrayParametrosCaracteristica['intIdServicio']     = $intIdServicio;
            $arrayParametrosCaracteristica['strCaracteristica'] ='TRASLADO' ;
            $arrayParametrosCaracteristica['strCodEmpresa']     = $strCodigoEmpresa;
            $arrayParametrosCaracteristica['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosCaracteristica['strIpCreacion']     = $strIpCreacion;
            $objServProdCaractTraslado = $serviceServicioComercial->obtieneProductoCaracteristicaInternet($arrayParametrosCaracteristica);
            if(is_object($objServProdCaractTraslado))
            {
                $objServicioOrigen = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objServProdCaractTraslado->getValor());
                $objDetTipoSolRegEle= new InfoDetalleSolicitud();
                $objDetTipoSolRegEle->setTipoSolicitudId($objTipoSolicitudRegEle);
                $objDetTipoSolRegEle->setObservacion('Se registran equipos entregados por el usuario');
                $objDetTipoSolRegEle->setFeCreacion(new \DateTime('now'));
                $objDetTipoSolRegEle->setUsrCreacion($strUsrCreacion);
                $objDetTipoSolRegEle->setEstado('Finalizada');
                $objDetTipoSolRegEle->setServicioId($objServicioOrigen);
                $emComercial->persist($objDetTipoSolRegEle);
                $emComercial->flush();
                $objDetTipoSolRegEleHist = new InfoDetalleSolHist();
                $objDetTipoSolRegEleHist->setDetalleSolicitudId($objDetTipoSolRegEle);
                $objDetTipoSolRegEleHist->setEstado($objDetTipoSolRegEle->getEstado());
                $objDetTipoSolRegEleHist->setFeCreacion(new \DateTime('now'));
                $objDetTipoSolRegEleHist->setUsrCreacion($strUsrCreacion);
                $objDetTipoSolRegEleHist->setObservacion('Se crea y se finaliza automáticamente esta solicitud');
                $objDetTipoSolRegEleHist->setIpCreacion($strIpCreacion);
                $emComercial->persist($objDetTipoSolRegEleHist);
                $emComercial->flush();
                $arrayElementosRegistrar       = json_decode($strDatosElementos, true);
                $arrayElementoRegistrarIterate = $arrayElementosRegistrar['elementos'];
                $intCantidadElementos          = count($arrayElementoRegistrarIterate);
                foreach($arrayElementoRegistrarIterate as $arrayElementoRegistrar)
                {
                    $objDetTipoSolRegEleCaracEle = new InfoDetalleSolCaract();
                    $objDetTipoSolRegEleCaracEle->setCaracteristicaId($objAdmiCaracElemento);
                    $objDetTipoSolRegEleCaracEle->setValor($arrayElementoRegistrar['idElemento']);
                    $objDetTipoSolRegEleCaracEle->setDetalleSolicitudId($objDetTipoSolRegEle);
                    $objDetTipoSolRegEleCaracEle->setEstado($objDetTipoSolRegEle->getEstado());
                    $objDetTipoSolRegEleCaracEle->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolRegEleCaracEle->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolRegEleCaracEle);
                    $emComercial->flush();
                    $intDetSolCaract = $objDetTipoSolRegEleCaracEle->getId();
                    $objDetTipoSolRegEleCaracObs = new InfoDetalleSolCaract();
                    $objDetTipoSolRegEleCaracObs->setCaracteristicaId($objAdmiCaracObs);
                    $objDetTipoSolRegEleCaracObs->setValor($arrayElementoRegistrar['observacion']);
                    $objDetTipoSolRegEleCaracObs->setDetalleSolicitudId($objDetTipoSolRegEle);
                    $objDetTipoSolRegEleCaracObs->setEstado($objDetTipoSolRegEle->getEstado());
                    $objDetTipoSolRegEleCaracObs->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolRegEleCaracObs->setUsrCreacion($strUsrCreacion);
                    $objDetTipoSolRegEleCaracObs->setDetalleSolCaractId($intDetSolCaract);
                    $emComercial->persist($objDetTipoSolRegEleCaracObs);
                    $emComercial->flush();
                    $objDetTipoSolRegEleCaracEstado = new InfoDetalleSolCaract();
                    $objDetTipoSolRegEleCaracEstado->setCaracteristicaId($objAdmiCaracEstado);
                    $objDetTipoSolRegEleCaracEstado->setValor($arrayElementoRegistrar['estadoElemento']);
                    $objDetTipoSolRegEleCaracEstado->setDetalleSolicitudId($objDetTipoSolRegEle);
                    $objDetTipoSolRegEleCaracEstado->setEstado($objDetTipoSolRegEle->getEstado());
                    $objDetTipoSolRegEleCaracEstado->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolRegEleCaracEstado->setUsrCreacion($strUsrCreacion);
                    $objDetTipoSolRegEleCaracEstado->setDetalleSolCaractId($intDetSolCaract);
                    $emComercial->persist($objDetTipoSolRegEleCaracEstado);
                    $emComercial->flush();
                    if ($arrayElementoRegistrar['estadoElemento'] === "EnOficinaMd")
                    {

                        // Se actualiza el registro de la caracteristica con estado entregado (En oficina)
                        if (empty($arrayElementoRegistrar['idElemento']))
                        {
                            $objServCaractEle = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->find($arrayElementoRegistrar['idServicioProdCaract']);
                            if (is_object($objServCaractEle))
                            {
                                $strValorProducto = str_replace("NO ENTREGADO",
                                                                $arrayElementoRegistrar['estadoElemento'],
                                                                $objServCaractEle->getValor());
                                $objServCaractEle->setValor($strValorProducto);
                                $emComercial->persist($objServCaractEle);
                                $emComercial->flush();
                            }
                        }
                        else
                        {
                            $intContadorElementosEntregados++;
                            //registrar elementro trazabilidad
                            $arrayParametrosAuditoria = array();
                            $arrayParametrosAuditoria["strOrigen"] = "cargaMasiva";
                            $arrayParametrosAuditoria["strNumeroSerie"]  = $arrayElementoRegistrar['serieElemento'];
                            $arrayParametrosAuditoria["strLogin"]        = $objServicioOrigen->getPuntoId()->getLogin();
                            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo'; //estado de elemento recuperado en telcos
                            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado'; //estado de naf
                            $arrayParametrosAuditoria["strEstadoActivo"] = 'EnOficinaMd'; //estado entregado por usuario
                            $arrayParametrosAuditoria["strUbicacion"]    = 'EnOficina'; //default
                            $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;
                            $arrayParametrosAuditoria["strCodEmpresa"]   = $strCodigoEmpresa;
                            $arrayParametrosAuditoria["strTransaccion"]  = "Traslado de servicio - Entrega de cliente en oficina";
                            $arrayParametrosAuditoria["intOficinaId"]    = 0;
                            //Se ingresa el tracking del elemento
                            $serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                        }
                    }
                    $strEquiposObservacionTarea = $strEquiposObservacionTarea . $arrayElementoRegistrar['serieElemento'] .
                                                  " - " . $arrayElementoRegistrar['tipoElemento'] . " - " .
                                                  $arrayElementoRegistrar['estadoElemento'] . "</br>";
                }
                if (count($arrayElementoRegistrarIterate) === $intContadorElementosEntregados )
                {
                    $strEquiposTarea = "SI";
                }
                
                $strObservacionTarea          = $strObservacionTarea . $strEquiposTarea . "</br></br>" . $strEquiposObservacionTarea;
                //Busca la caracteristica Ruta Georeferencial por punto.
                $entityInfoPuntoCaracteristicaOrigen = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                                   ->findOneBy(array('puntoId'          => $objServicioOrigen->getPuntoId()->getId(),
                                                                                     'caracteristicaId' => $objAdmiCaracTarea,
                                                                                     'estado'           => 'Activo'
                                                                                    ));
                //Termina el metodo cuando no encuentra la entidad InfoPuntoCaracteristica.
                if(is_object($entityInfoPuntoCaracteristicaOrigen))
                {
                    $objDetalleOrigen = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                  ->find($entityInfoPuntoCaracteristicaOrigen->getValor());
                    if(is_object($objDetalleOrigen))
                    {
                        $objInfoDetalleAsignacionOrigen = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                                    ->getUltimaAsignacion($objDetalleOrigen->getId());
                        if (is_object($objInfoDetalleAsignacionOrigen))
                        {
                            $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                            $objInfoTareaSeguimiento->setDetalleId($objDetalleOrigen->getId());
                            $objInfoTareaSeguimiento->setObservacion($strObservacionTarea);
                            $objInfoTareaSeguimiento->setUsrCreacion($strUsrCreacion);
                            $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                            $objInfoTareaSeguimiento->setEmpresaCod($strCodigoEmpresa);
                            $objInfoTareaSeguimiento->setEstadoTarea("Finalizada");
                            $objInfoTareaSeguimiento->setDepartamentoId($objInfoDetalleAsignacionOrigen->getDepartamentoId());
                            $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objInfoDetalleAsignacionOrigen->getPersonaEmpresaRolId());
                            $emSoporte->persist($objInfoTareaSeguimiento);
                            $emSoporte->flush();
                        }
                    }
                }
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                //Busca la caracteristica Ruta Georeferencial por punto.
                $entityInfoPuntoCaracteristica = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                             ->findOneBy(array('puntoId'          => $objServicio->getPuntoId()->getId(), 
                                                                               'caracteristicaId' => $objAdmiCaracTarea,
                                                                               'estado'           => 'Activo'
                                                                              ));
                //Termina el metodo cuando no encuentra la entidad InfoPuntoCaracteristica.
                if(is_object($entityInfoPuntoCaracteristica))
                {
                    $objDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                            ->find($entityInfoPuntoCaracteristica->getValor());
                    if(is_object($objDetalle))
                    {
                        $objInfoDetalleAsignacion = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                                ->getUltimaAsignacion($objDetalle->getId());
                        if (is_object($objInfoDetalleAsignacion))
                        {
                            $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                            $objInfoTareaSeguimiento->setDetalleId($objDetalle->getId());
                            $objInfoTareaSeguimiento->setObservacion($strObservacionTarea);
                            $objInfoTareaSeguimiento->setUsrCreacion($strUsrCreacion);
                            $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                            $objInfoTareaSeguimiento->setEmpresaCod($strCodigoEmpresa);
                            $objInfoTareaSeguimiento->setEstadoTarea("Finalizada");
                            $objInfoTareaSeguimiento->setDepartamentoId($objInfoDetalleAsignacion->getDepartamentoId());
                            $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objInfoDetalleAsignacion->getPersonaEmpresaRolId());
                            $emSoporte->persist($objInfoTareaSeguimiento);
                            $emSoporte->flush();
                        }
                    }
                }
                $arrayRespuesta['strEstado']  = 'OK';
                $arrayRespuesta['strMensaje'] = 'Se registró la información correctamente.';
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.ajaxGrabaEquiposEntregadosAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
        }
        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }
    
    /**
     * ajaxValidaElementosOrigenTrasladoAction
     * 
     * Funcion encargada de obtener los elementos que el cliente tiene en el origen del traslado.
     * Cuando es un traslado MD entre diferentes tecnologías se indica una alerta de que se facturarán dichos equipos
     *
     * @author Creado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 24-01-2022 Version Inicial
     *
     * return JSON $objResponse
     */
    public function ajaxValidaElementosOrigenTrasladoAction()
    {
        $intCount       = 0;
		$objRespuesta   = new Response();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $intIdServicio  = $objRequest->get('idServicio');
        $strIpCreacion  = $objRequest->getClientIp();
        $strUsrCreacion = $objSession->get('user');
        $serviceServicio        = $this->get('comercial.InfoServicio');
        $strCodigoEmpresa       = $objSession->get('idEmpresa');
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $arrayRespuesta = array();
        $arrayRespuesta["strEstado"]  = "ERROR";
        $arrayRespuesta["strMensaje"] = "Existieron problemas al validar los equipos entregados por el cliente.";
        $arrayRespuesta["strExistenEquiposFacturar"] = "";
        try
        {
            $arrayParametros = array();
            $arrayParametros['intIdServicio']  = $intIdServicio;
            $arrayParametros['strIpCreacion']  = $strIpCreacion;
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['strCodEmpresa']  = $strCodigoEmpresa;
            $arrayRespuesta = $serviceServicio->obtenerValidaElementosOrigenTraslados($arrayParametros);
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.ajaxValidaElementosOrigenTrasladoAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
        }
        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_9-4857")
     * 
     * Documentación para el método 'actualizarFormaPagoFacturacionAction'.
     *
     * Actualiza la forma de pago del punto, que será utilizado en la facturación.
     *
     * @return JsonResponse $arrayResultadoJson ['strDescripcionFormaPago' => 'Descripción de la forma de pago a la cual fue actualizado el punto',
     *                                           'strEstado'               => 'Variable que indica el estado del proceso',
     *                                           'strError'                => 'Variable que contiene el mensaje de error en caso de existir' ]
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-10-2016
     */
    public function actualizarFormaPagoFacturacionAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSesion               = $objRequest->getSession();
        $strUsrCreacion          = $objSesion->get('user');
        $strCodigoFormaPago      = $objRequest->get('strCodigoFormaPago');
        $intIdPunto              = $objRequest->get('intIdPunto');
        $emComercial             = $this->getDoctrine()->getManager();
        $strIpCreacion           = $objRequest->getClientIp();
        $strDescripcionFormaPago = "";
        $arrayResultadoJson      = array();
        $serviceUtil             = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            if( empty($strCodigoFormaPago) )
            {
                throw new \Exception("No se ha enviado la forma de pago que se desea actualizar");
            }
            
            $objAdmiFormaPago = $emComercial->getRepository('schemaBundle:AdmiFormaPago')
                                            ->findOneBy( array('estado' => 'Activo', 'codigoFormaPago' => $strCodigoFormaPago ) );
            
            if( !is_object($objAdmiFormaPago) )
            {
                throw new \Exception("No se encontró el registro de la forma de pago que se desea actualizar");
            }
            
            if( empty($intIdPunto) )
            {
                throw new \Exception("No se ha enviado el punto del cliente que se desea actualizar");
            }
            
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneById($intIdPunto);
            
            if( !is_object($objInfoPunto) )
            {
                throw new \Exception("No se encontró el punto del cliente que se desea actualizar");
            }
            
            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy( array('estado' => 'Activo', 'descripcionCaracteristica' => 'FORMA_PAGO' ) );
            
            if( !is_object($objAdmiCaracteristica) )
            {
                throw new Exception("No se encontró la característica 'FORMA_PAGO' que se desea guardar");
            }
            
            $arrayInfoPuntoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                         ->findBy( array( 'puntoId'          => $objInfoPunto, 
                                                                          'caracteristicaId' => $objAdmiCaracteristica,
                                                                          'estado'           => 'Activo' ) );
            
            if( !empty($arrayInfoPuntoCaracteristicas) )
            {
                foreach($arrayInfoPuntoCaracteristicas as $objInfoPuntoCaracteristica)
                {
                    $objInfoPuntoCaracteristica->setFeUltMod(new \DateTime('now'));
                    $objInfoPuntoCaracteristica->setUsrUltMod($strUsrCreacion);
                    $objInfoPuntoCaracteristica->setEstado('Inactivo');
                    $emComercial->persist($objInfoPuntoCaracteristica);
                }
            }
            
            $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
            $objInfoPuntoCaracteristica->setValor($strCodigoFormaPago);
            $objInfoPuntoCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
            $objInfoPuntoCaracteristica->setPuntoId($objInfoPunto);
            $objInfoPuntoCaracteristica->setEstado('Activo');
            $objInfoPuntoCaracteristica->setFeCreacion(new \DateTime('now'));
            $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
            $objInfoPuntoCaracteristica->setIpCreacion($strIpCreacion);
            $emComercial->persist($objInfoPuntoCaracteristica);
            
            $objInfoPuntoHistorial = new InfoPuntoHistorial();
            $objInfoPuntoHistorial->setPuntoId($objInfoPunto);
            $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoPuntoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoPuntoHistorial->setIpCreacion($strIpCreacion);
            $objInfoPuntoHistorial->setValor($strCodigoFormaPago);
            $objInfoPuntoHistorial->setAccion('actualizarFormaPagoFacturacion');
            $emComercial->persist($objInfoPuntoHistorial);
            
            $emComercial->flush();
            $emComercial->commit();
            
            $strDescripcionFormaPago                       = $objAdmiFormaPago->getDescripcionFormaPago();
            $arrayResultadoJson['strDescripcionFormaPago'] = $strDescripcionFormaPago;
            $arrayResultadoJson['strEstado']               = "OK";
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $emComercial->close();
                
            $serviceUtil->insertError('Telcos+', 'actualizarFormaPagoFacturacionAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            
            $arrayResultadoJson['strEstado'] = "ERROR";
            $arrayResultadoJson['strError']  = "No se concluyó la transacción con éxito. <br>Comuníquese con Sistemas para solucionar.<br>Error: " . 
                                                $e->getMessage();
        }
        
        $objJsonResponse->setData($arrayResultadoJson);
        
        return $objJsonResponse;
    }
    
    /**
     * @Secure(roles="ROLE_9-5077")
     * 
     * Documentación para el método 'newEditContratoExternoDigitalAction'.
     * 
     * Funcion para el ingreso y edicion de Nuevos Contratos Externos Digitales
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 13-02-2017 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 23-07-2018 -  Se envía prefijo empresa para validar utilización del lado del cliente 
     *                            Se obtiene tipo de documento para identificar los contratos venidos para CLOUDFORM
     *      
     * @param integer $intIdPunto
     * @param string  $strRol
     * 
     * @return Renders a view.
     */	    
    public function newEditContratoExternoDigitalAction($intIdPunto, $strRol)
    {        
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();        
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : 'MD';        
        $emComercial              = $this->getDoctrine()->getManager("telconet");
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion           = $this->getDoctrine()->getManager('telconet_comunicacion');		        
        $arrayParametros          = array();
        $objInfoPunto             = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
        if (!is_object($objInfoPunto)) 
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontró registro del Punto Cliente.');            
            return $this->render('comercialBundle:infopunto:newEditContratoExternoDigital.html.twig',$arrayParametros); 
        }
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());
        
        if (!is_object($objInfoPersonaEmpresaRol)) 
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontró registro del Cliente.');            
            return $this->render('comercialBundle:infopunto:newEditContratoExternoDigital.html.twig',$arrayParametros); 
        }
        $arrayInformacionPersona['puntoId'] = $objInfoPunto->getLogin();
        $arrayInformacionPersona['cliente'] = sprintf("%s", $objInfoPersonaEmpresaRol->getPersonaId());
        
        // Tipos de Documentos
        $arrayTipoDocumentos = array();
        
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByCodigoTipoDocumento("VTAEX");
        }
        else
        {
            $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByCodigoTipoDocumento("CLOUD");
        }
        
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }    
        
        //Proveedor -> ROL : Proveedor Venta       
        $arrayProveedores                = array();
        $arrayParam                      = array();
        $arrayParam['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParam['strDescripRol']     = 'Proveedor Venta';
        $arrayParam['strEstado']         = 'Activo';
        
        $objProveedores = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonasProveedorVentaExterna($arrayParam);
        foreach ( $objProveedores as $objProveedores )
        {   
           $arrayProveedores[$objProveedores->getId()] = sprintf("%s", $objProveedores->getPersonaId());           
        }
        
        $formDocumentos = $this->createForm(new InfoDocumentoType(array('validaFile'=>true,
                                                                        'arrayTipoDocumentos'=>$arrayTipoDocumentos)), new InfoDocumento());
        
        $arrayParametros = array('formDocumentos'    => $formDocumentos->createView());   
        $arrayParametros['objInfoPunto']             = $objInfoPunto;
        $arrayParametros['objInfoPersonaEmpresaRol'] = $objInfoPersonaEmpresaRol;        
        $arrayParametros['arrayTipoDocumentos']      = $arrayTipoDocumentos;
        $arrayParametros['arrayProveedores']         = $arrayProveedores;
        $arrayParametros['arrayInfoCliente']         = $arrayInformacionPersona;
        $arrayParametros['strRol']                   = $strRol;         
        $arrayParametros['strPrefijoEmpresa']        = $strPrefijoEmpresa;        
        
         return $this->render('comercialBundle:infopunto:newEditContratoExternoDigital.html.twig',$arrayParametros);       
    }
       
    /**
    * @Secure(roles="ROLE_9-5077")
    * Documentación para el método 'gridContratoExternoDigitalAction'.
    *
    * Retorna resultado Listado de archivos digitales asociados a un Login y sus servicios de Tipo Venta Externa
    *
    * @param  integer $intIdPunto
    * @return JsonResponse
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 13-02-2017 
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.1 24-07-2017 - Se envía información de tipo de documento de acuerdo a la empresa en sesión
    *                           para poder discriminar la consulta principal
    */
    public function gridContratoExternoDigitalAction($intIdPunto) 
    {
        $arrayParametros       = array();
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();  
        $serviceUtil           = $this->get('schema.Util');
        $strIpClient           = $objRequest->getClientIp();
        $strUsrSesion          = $objSession->get('user');         
        $emComunicacion        = $this->getDoctrine()->getManager('telconet_comunicacion');	
                
        $arrayParametros['intIdPunto']        = $intIdPunto;
        $arrayParametros['intStart']          = $objRequest->get('start');
        $arrayParametros['intLimit']          = $objRequest->get('limit');                  
        $arrayParametros['serviceRouter']     = $this->container->get('router');  
        
        if($objSession->get('prefijoEmpresa') == 'MD' || $objSession->get('prefijoEmpresa') == 'EN')
        {
            $strCodigoTipoDoc = 'VTAEX';
        }
        else
        {
            $strCodigoTipoDoc = 'CLOUD';
        }
        
        $arrayParametros['strCodigoTipoDoc']  = $strCodigoTipoDoc;
        
        $arrayContratosExternosDigitales = array();
        $objJsonResponse                 = new JsonResponse($arrayContratosExternosDigitales);
             
        try
        {        
            $arrayContratosExternosDigitales  = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                               ->getContratosExternosDigitales($arrayParametros);
            $objJsonResponse->setData($arrayContratosExternosDigitales);
              
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'InfoPuntoController.gridContratoExternoDigital',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }  
                                
        return $objJsonResponse;                
    }
    
   /** 
    * Documentación para el método 'eliminarContratoExternoDigitalAction'.
    * 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 15-02-2017  
    * 
    * @param integer $intIdDocumento     
    *  
    */
    public function eliminarContratoExternoDigitalAction($intIdDocumento)
    {   
        $arrayParametros  = array();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user');   
        $intIdPunto       = $objRequest->request->get("intIdPunto");
        $strRol           = $objRequest->request->get("strRol");
        
        $emComunicacion   = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);           
        if ( !is_object($objInfoDocumento) ) 
        {
            throw $this->createNotFoundException('No se encontró Documento a Eliminar');
        }                        
           
        $arrayParametros['intIdDocumento'] = $intIdDocumento;
        $arrayParametros['strUsrSesion']   = $strUsrSesion;            
        try
        {
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto    = $this->get('comercial.InfoPunto');
            $serviceInfoPunto->eliminarContratoExternoDigital($arrayParametros);                
            return $this->redirect($this->generateUrl('infopunto_newEditContratoExternoDigital', 
                                                      array('intIdPunto'  => $intIdPunto,
                                                            'strRol'      => $strRol)));        
        }
        catch (\Exception $e)
        {   
            $serviceUtil->insertError('Telcos+', 
                                      'InfoPuntoController.eliminarContratoExternoDigitalAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('infopunto_newEditContratoExternoDigital',
                                                      array('intIdPunto'  => $intIdPunto,
                                                            'strRol'      => $strRol)));         
        }             
    }
       
    /** 
    * Documentación para el método 'descargarContratoExternoDigitalAction'.
    * 
    * Descripcion: Descripcion: Metodo encargado de descargar los documentos a partir del id de la referencia enviada
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 17-02-2017  
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 04-05-2021 - Se valida que las urls que contengan http no se le agruege la ruta interna de telcos
    * @since 1.0
    *
    * @param integer $intIdDocumento     
    *  
    */
    public function descargarContratoExternoDigitalAction($intIdDocumento)
    {        
        $emComunicacion    = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento  = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);               
        $strPath           = $objInfoDocumento->getUbicacionFisicaDocumento();        
        $strPathTelcos     = $this->container->getParameter('path_telcos');  
        if(strpos($strPath, 'http') !== 0)
        {
            $strPath = $strPathTelcos.$strPath ;
        }      
        $strContent        = file_get_contents($strPath);        
        $objResponse       = new Response();
        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="'.$objInfoDocumento->getUbicacionLogicaDocumento());
        $objResponse->setContent($strContent);
        return $objResponse;       
    }
   
    /**    
    * Documentación para el método 'gridServiciosVtaExternaAction'.
    *
    * Retorna resultado Listado de Servicios de Tipo Venta Externa (ES_VENTA = 'E'),
    * solo servicios que no posean asociado un documento digital de Tipo 'VTAEX' -> VENTA EXTERNA
    * y que se encuentren en los estados Pre-servicio, Factible, PrePlanificada, Planificada, AsignadoTarea
    *
    * @param  integer $intIdPunto
    * @return JsonResponse
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 13-02-2017  
    */
    public function gridServiciosVtaExternaAction($intIdPunto) 
    {
        $arrayParametros = array();
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();  
        $serviceUtil     = $this->get('schema.Util');
        $strIpClient     = $objRequest->getClientIp();
        $strUsrSesion    = $objSession->get('user'); 
        $strCodEmpresa   = $objSession->get('idEmpresa');
        $emComunicacion  = $this->getDoctrine()->getManager('telconet_comunicacion');	
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');	
                
        $strParamPadre          = 'ESTADOS SERVICIOS VENTA EXTERNA NETVOICE';
        $arrayEstados           = array();
        $arrayEstadosParametros = array();
        $strModulo              = 'COMERCIAL';
        $arrayEstados           = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strCodEmpresa);
        foreach($arrayEstados as $arrayEstado)
        {  
            $arrayEstadosParametros[] = $arrayEstado['valor1'];
        }           
        $arrayParametros['arrayEstados'] = $arrayEstadosParametros;
        $arrayParametros['intIdPunto']   = $intIdPunto;                                 
            
        $arrayJsonServicios = array();
        $objJsonResponse    = new JsonResponse($arrayJsonServicios);
             
        try
        {        
            $arrayJsonServicios  = $emComunicacion ->getRepository('schemaBundle:InfoPunto')
                                                   ->getServiciosVtaExterna($arrayParametros);
            $objJsonResponse->setData($arrayJsonServicios);   
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'InfoPuntoController.gridServiciosVtaExternaAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }                                  
        return $objJsonResponse;                
    }
    	
    /**
     * @Secure(roles="ROLE_9-5077")
     * 
     * Documentación para el método 'guardarContratoExternoDigitalAction'.
     * 
     * Funcion para guardar Contratos Externos Digitales
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 17-02-2017 
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 30-07-2018 Se agrega y se envía prefijo Empresa para realizar gestión de ingreso de documento adicional a nivel de empresa
     *      
     * @param integer $intIdPunto
     * @param string  $strRol
     * @param request $objRequest  
     * 
     * @return a RedirectResponse to the given URL.
     */	    
    public function guardarContratoExternoDigitalAction(Request $objRequest, $intIdPunto, $strRol)
    {       
        $strIpClient         = $objRequest->getClientIp();
        $objSession          = $objRequest->getSession();
        $strUsrCreacion      = $objSession->get('user');
        $strCodEmpresa       = $objSession->get('idEmpresa');                  
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');                  
        $arrayDatosFormFiles = $objRequest->files->get('infodocumentotype');               
        $arrayDatosFormTipos = $objRequest->get('infodocumentotype');
        $key                 = key($arrayDatosFormTipos);        
        $arrayTipoDocumentos = array ();             
        $serviceUtil         = $this->get('schema.Util');
        
        foreach ($arrayDatosFormTipos as $key => $arrayTipos)
        {                           
            foreach ( $arrayTipos as $keyTipo => $strValor)
            {                     
                $arrayTipoDocumentos[$keyTipo] = $strValor;                
            }
        }             
        $arrayDatosForm = array_merge(array('arrayDatosFormFiles' => $arrayDatosFormFiles),
                                      array('arrayTipoDocumentos' => $arrayTipoDocumentos));  
        
        $arrayListadoServicios = $objRequest->get("array_listado_servicios");
        $arrayServicios        = explode("|", $arrayListadoServicios);
            
        $arrayParametros                           = array();
        $arrayParametros['idPersonaRolProveedor']  = $objRequest->get('idPersonaRolProveedor');
        $arrayParametros['arrayDatosForm']         = $arrayDatosForm;
        $arrayParametros['intIdPunto']             = $intIdPunto;
        $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
        $arrayParametros['strUsrCreacion']         = $strUsrCreacion;
        $arrayParametros['strIpClient']            = $strIpClient;
        $arrayParametros['arrayServicios']         = $arrayServicios;
        $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
        
        try
        {
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto = $this->get('comercial.InfoPunto');
            $objInfoPunto     = $serviceInfoPunto->guardarContratoExternoDigital($arrayParametros);
            
            return $this->redirect($this->generateUrl('infopunto_newEditContratoExternoDigital', 
                                                          array('intIdPunto' => $objInfoPunto->getId(),
                                                                'strRol'     => $strRol)));    
        }
        catch (\Exception $e)
        {   
            $serviceUtil->insertError('Telcos+', 
                                      'InfoPuntoController.guardarContratoExternoDigitalAction',
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient
                                     ); 
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            
            return $this->redirect($this->generateUrl('infopunto_newEditContratoExternoDigital',
                                                          array('intIdPunto' => $intIdPunto,
                                                                'strRol'     => $strRol)));    
        }
    }
    
    
    /**
     * Documentación para el método 'getPlantillaComisionistaGridServiciosAction'.
     * 
     * Función que obtiene la información de la plantilla de comisionistas que se podrá editar o ver a nivel del grid de servicios
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 05-05-2017
     * 
     * @return JsonResponse $objJsonResponse
     */	    
    public function getPlantillaComisionistaGridServiciosAction()
    {
        $objJsonResponse   = new JsonResponse();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil       = $this->get('schema.Util');
        $serviceUtilidades = $this->get('administracion.Utilidades');
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strIpCreacion     = $objRequest->getClientIp();
        $strUsuario        = $objSession->get('user');
        $strCodEmpresa     = $objSession->get('idEmpresa');
        
        $arrayPlantillaComisionista = array('arrayResultados' => array(), 'intTotal' => 0, 'strMensajeError' => null);
        
        try
        {
            $intIdServicio   = $objRequest->query->get('intIdServicio') ? $objRequest->query->get('intIdServicio') : 0;
            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServicio);
            
            if( !is_object($objInfoServicio) )
            {
                throw new \Exception('No se ha encontrado el objeto de servicio para buscar la plantilla de comisionistas.');
            }//( !is_object($objInfoServicio) )
            
            $objAdmiProducto = $objInfoServicio->getProductoId();
            
            if( !is_object($objAdmiProducto) )
            {
                throw new \Exception('No se ha encontrado el objeto de producto asociado al servicio.');
            }//( !is_object($objAdmiProducto) )
            
            $strGrupoProducto         = $objAdmiProducto->getGrupo();
            $boolEditarComisionistas  = false;
            $strEstadoPlantillaBuscar = 'Activo';

            //SE VERIFICA SI EL ESTADO DEL SERVICIO ES VALIDO PARA PODER EDITAR LA PLANTILLA DE COMISIONISTA
            $arrayParametroPlantillaComisionista = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('ESTADOS_GRID_SERVICIOS', 
                                                                      'COMERCIAL', 
                                                                      'PUNTO',
                                                                      $objInfoServicio->getEstado(), 
                                                                      'CAMBIO_VENDEDOR',
                                                                      '',
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $strCodEmpresa);

            if( isset($arrayParametroPlantillaComisionista['id']) && !empty($arrayParametroPlantillaComisionista['id']) )
            {
                $strEstadoPlantillaBuscar = $objInfoServicio->getEstado();
            }
            else
            {
                $boolEditarComisionistas = true;
            }//( isset($arrayParametroPlantillaComisionista['id']) && !empty($arrayParametroPlantillaComisionista['id']) )
            
            $arrayInfoServiciosComision = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                      ->findBy( array( 'servicioId' => $objInfoServicio,
                                                                       'estado'     => $strEstadoPlantillaBuscar ) );
            
            if( !empty($arrayInfoServiciosComision) )
            {
                foreach($arrayInfoServiciosComision as $objInfoServicioComision)
                {
                    if( is_object($objInfoServicioComision) )
                    {
                        $arrayItem                               = array();
                        $arrayItem['intIdServicio']              = $intIdServicio;
                        $arrayItem['strGrupoProducto']           = $strGrupoProducto;
                        $arrayItem['intIdServicioComision']      = $objInfoServicioComision->getId();
                        $arrayItem['floatComisionVenta']         = $objInfoServicioComision->getComisionVenta();
                        $arrayItem['floatComisionMantenimiento'] = $objInfoServicioComision->getComisionMantenimiento();
                        $arrayItem['boolEditarComisionistas']    = $boolEditarComisionistas;
                        
                        $objInfoPersonaEmpresaRol = $objInfoServicioComision->getPersonaEmpresaRolId();
                        if( !is_object($objInfoPersonaEmpresaRol) )
                        {
                            throw new \Exception('No se ha encontrado al personal que comisiona.');
                        }//( !is_object($objInfoPersonaEmpresaRol) )
                        
                        $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();
                        if( !is_object($objInfoPersona) )
                        {
                            throw new \Exception('No se ha encontrado la información personal de la persona que comisiona.');
                        }//( !is_object($objInfoPersona) )
                        
                        $strNombreComisionista               = ucwords(strtolower(trim($objInfoPersona->__toString())));
                        $arrayItem['strPersonaComisionista'] = $strNombreComisionista;
                        $arrayItem['intIdPersonaEmpresaRol'] = $objInfoPersonaEmpresaRol->getId();
                        
                        $objAdmiComisionDet = $objInfoServicioComision->getComisionDetId();
                        if( !is_object($objAdmiComisionDet) )
                        {
                            throw new \Exception('No se ha encontrado el detalle de la comision.');
                        }//( !is_object($objAdmiComisionDet) )
                        
                        
                        //SE OBTIENE EL TIPO DE COMISIONISTA
                        $intIdParametroDet = $objAdmiComisionDet->getParametroDetId();
                        if( empty($intIdParametroDet) )
                        {
                            throw new \Exception('No se ha encontrado el id del parametro de detalle.');
                        }//( empty($intIdParametroDet) )
                        
                        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneById($intIdParametroDet);
                        if( empty($objAdmiParametroDet) )
                        {
                            throw new \Exception('No se ha encontrado la información del parametro detalle.');
                        }//( empty($objAdmiParametroDet) )
                        
                        $arrayItem['intIdCargo']          = $objAdmiParametroDet->getId();
                        $arrayItem['strRolComisionista']  = $objAdmiParametroDet->getDescripcion();
                        $arrayItem['strTipoComisionista'] = $objAdmiParametroDet->getValor3();
                        
                        $arrayPlantillaComisionista['arrayResultados'][] = $arrayItem;
                    }//( is_object($objInfoServicioComision) )
                }//foreach($arrayInfoServiciosComision as $objInfoServicioComision)
            }
            else
            {
                $arrayPlantillaComisionista['strMensajeError'] = 'No se ha encontrado plantilla de comisionistas para el servicio seleccionado';
            }//( !empty($arrayInfoServiciosComision) )
        }
        catch(\Exception $e) 
        {
            $arrayPlantillaComisionista['strMensajeError'] = 'Hubo un problema al obtener la plantilla de comisionistas';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoPuntoController.getPlantillaComisionistaGridServiciosAction', 
                                       'Error al obtener la plantilla de comisionistas. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arrayPlantillaComisionista);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para el método 'getPersonalComisionistaAction'.
     * 
     * Función que obtiene los empleados que se mostrarán en los combos de los comisionistas
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 07-05-2017
     * 
     * @return JsonResponse $objJsonResponse
     */	    
    public function getPersonalComisionistaAction()
    {
        $objJsonResponse        = new JsonResponse();
        $serviceUtil            = $this->get('schema.Util');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $serviceJefesComercial  = $this->get('administracion.JefesComercial');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $intIdDepartamento      = $objSession->get('idDepartamento');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCaracteristicaCargo = ( $strPrefijoEmpresa == "TN" ) ? 'CARGO_GRUPO_ROLES_PERSONAL' : 'CARGO';
        $strTipoPersonal        = 'Otros';
        $arrayPersonalComisionista = array('arrayResultados' => array(), 'intTotal' => 0, 'strMensajeError' => null);
        $intTotal                  = 0;
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuario);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        try
        {
            $intIdPersonalSelected = $objRequest->query->get('intIdPersonaEmpresaRol') ? $objRequest->query->get('intIdPersonaEmpresaRol') : 0;
            $strTipoComisionista   = $objRequest->query->get('strTipoComisionista') ? $objRequest->query->get('strTipoComisionista') : '';
            $strGrupoProducto      = $objRequest->query->get('strGrupoProducto') ? $objRequest->query->get('strGrupoProducto') : '';
            $intIdCargo            = $objRequest->query->get('intIdCargo') ? $objRequest->query->get('intIdCargo') : 0;
            
            if( !empty($strTipoComisionista) && !empty($intIdCargo) )
            {
                $arrayParametros                        = array();
                $arrayParametros['usuario']             = $intIdPersonaEmpresaRol;
                $arrayParametros['empresa']             = $strCodEmpresa;
                $arrayParametros['estadoActivo']        = 'Activo';
                $arrayParametros['caracteristicaCargo'] = $strCaracteristicaCargo;
                $arrayParametros['departamento']        = $intIdDepartamento;
                $arrayParametros['nombreArea']          = 'Comercial';
                $arrayParametros['exceptoUsr']          = array($intIdPersonalSelected);
                $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');


                /**
                 * BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
                 */
                $arrayRolesNoIncluidos = array();
                $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                'strValorRetornar'  => 'descripcion',
                                                'strNombreProceso'  => 'JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                'strUsrCreacion'    => $strUsuario,
                                                'strIpCreacion'     => $strIpCreacion );

                $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                    {
                        $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                    }//foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )

                    $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
                }//( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )


                /**
                 * BLOQUE QUE BUSCA LOS ROLES PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
                 */
                $arrayRolesIncluidos                       = array();
                $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

                $arrayResultadosRolesIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                    {
                        $arrayRolesIncluidos[] = $strRolIncluido;
                    }//foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )

                    $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
                }//( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )


                /**
                 * SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 
                 * 'GRUPO_DEPARTAMENTOS'
                 */
                if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
                {
                    $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                    $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonaEmpresaRol;
                    $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                          'strValorRetornar'  => 'valor1',
                                                          'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                          'strNombreModulo'   => 'COMERCIAL',
                                                          'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                          'strValor2Detalle'  => 'COMERCIAL',
                                                          'strUsrCreacion'    => $strUsuario,
                                                          'strIpCreacion'     => $strIpCreacion);

                    $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                    if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                    {
                        $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                    }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
                
                if( $strTipoComisionista == "GERENTE_PRODUCTO" )
                {
                    if( empty($strGrupoProducto) )
                    {
                        throw new \Exception('No se ha enviado el grupo del producto para poder obtener los gerentes de productos respectivos.');
                    }//( empty($strGrupoProducto) )
                    
                    $arrayParametros['strAsignadosProducto'] = $strGrupoProducto;
                }//( $strTipoComisionista == "GERENTE_PRODUCTO" )
                else
                {
                    $arrayParametros['criterios']['cargo'] = $intIdCargo;
                }
                
                $arrayResultadosPersonalComisionista = $serviceJefesComercial->getListadoEmpleados( $arrayParametros );

                if( isset($arrayResultadosPersonalComisionista['usuarios']) && !empty($arrayResultadosPersonalComisionista['usuarios']) )
                {
                    foreach( $arrayResultadosPersonalComisionista['usuarios'] as $arrayUsuario )
                    {
                        if( isset($arrayUsuario['intIdPersonaEmpresaRol']) && !empty($arrayUsuario['intIdPersonaEmpresaRol'])
                            && isset($arrayUsuario['strEmpleado']) && !empty($arrayUsuario['strEmpleado']) )
                        {
                            $arrayPersonalComisionista['arrayResultados'][] = $arrayUsuario;
                            $intTotal++;
                        }//( isset($arrayUsuario['intIdPersonaEmpresaRol']) && !empty($arrayUsuario['intIdPersonaEmpresaRol'])...
                    }//foreach( $arrayResultadosPersonalComisionista['usuarios'] as $arrayUsuario )
                }//( isset($arrayResultadosPersonalComisionista['usuarios']) && !empty($arrayResultadosPersonalComisionista['usuarios']) )
                else
                {
                    $arrayPersonalComisionista['strMensajeError'] = 'No se encontró Personal para ser seleccionado.';
                }
                
                $arrayPersonalComisionista['intTotal'] = $intTotal;
            }
            else
            {
                throw new \Exception('No se han enviado todos los parámetros necesarios para la búsqueda de los empleados que se presentarán en el '.
                                     'combo a elegir del personal. TipoComisionista('.$strTipoComisionista.'), IdCargo('.$intIdCargo.')');
            }//( !empty($strTipoComisionista) && !empty($intIdCargo) )
        }
        catch(\Exception $e) 
        {
            $arrayPersonalComisionista['strMensajeError'] = 'Hubo un problema al obtener el personal comisionista para seleccionar';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoPuntoController.getPersonalComisionistaAction', 
                                       'Error al obtener al personal para seleccionar como comisionistas. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arrayPersonalComisionista);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para el método 'crearSolicitudesPuntoAction'.
     * 
     * Función que crea las solicitudes asociadas a los servicios seleccionados por el usuario
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 08-05-2017
     * 
     * @return Response $objResponse
     */	    
    public function crearSolicitudesPuntoAction()
    {
        $objResponse         = new Response();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $serviceUtil         = $this->get('schema.Util');
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strIpCreacion       = $objRequest->getClientIp();
        $strUsuario          = $objSession->get('user');
        $strMensajeRespuesta = "OK";
        

        $emComercial->getConnection()->beginTransaction();

        try
        {
            $intIdPersonaComisionistaNew = $objRequest->query->get('intIdPersonaComisionistaNew')
                                           ? $objRequest->query->get('intIdPersonaComisionistaNew') : 0;
            $intIdPersonaComisionistaOld = $objRequest->query->get('intIdPersonaComisionistaOld')
                                           ? $objRequest->query->get('intIdPersonaComisionistaOld') : 0;
            $strPersonaComisionistaNew   = $objRequest->query->get('strPersonaComisionistaNew')
                                           ? $objRequest->query->get('strPersonaComisionistaNew') : '';
            $strPersonaComisionistaOld   = $objRequest->query->get('strPersonaComisionistaOld')
                                           ? $objRequest->query->get('strPersonaComisionistaOld') : 'NO TIENE ASIGNADO';
            $intIdServicioComision       = $objRequest->query->get('intIdServicioComision') ? $objRequest->query->get('intIdServicioComision') : 0;
            $floatComisionVentaNew       = $objRequest->query->get('floatComisionVentaNew')
                                           ? $objRequest->query->get('floatComisionVentaNew') : 0;
            $floatComisionVentaOld       = $objRequest->query->get('floatComisionVentaOld')
                                           ? $objRequest->query->get('floatComisionVentaOld') : 0;
            $strTipoComisionista         = $objRequest->query->get('strTipoComisionista') ? $objRequest->query->get('strTipoComisionista') : '';
            $intIdServicioSelected       = $objRequest->query->get('intIdServicioSelected')
                                           ? $objRequest->query->get('intIdServicioSelected') : 0;
            $strNombresSolicitudes       = $objRequest->query->get('strNombreSolicitud') ? $objRequest->query->get('strNombreSolicitud') : '';
            
            if( !empty($intIdServicioSelected) && !empty($strNombresSolicitudes) )
            {
                //SE VERIFICA SI EL ID DEL SERVICIO EXISTE
                $objServicioSelected = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServicioSelected);
                
                if( !is_object($objServicioSelected) )
                {
                    throw new \Exception('No se ha encontrado el servicio para generarle la solicitud.');
                }//( !is_object($objServicioSelected) )

                $arrayNombresSolicitudes = explode('|', $strNombresSolicitudes);
                
                if( !empty($arrayNombresSolicitudes) )
                {
                    foreach($arrayNombresSolicitudes as $strNombreSolicitud)
                    {
                        if( !empty($strNombreSolicitud) )
                        {
                            //SE OBTIENE LA SOLICITUD A CREAR
                            $objAdmiTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                                ->findOneBy( array('estado'               => 'Activo', 
                                                                                   'descripcionSolicitud' => $strNombreSolicitud) );

                            if( !is_object($objAdmiTipoSolicitud) )
                            {
                                throw new \Exception('No se encontró el tipo de solicitud a generar.');
                            }//( !is_object($objCambioVendedorSolicitud) )

                            $strObservacion = "Se crea la solicitud: ".$strNombreSolicitud;
                            
                            //SE CREA LA SOLICITUD
                            $objDetalleSolicitud = new InfoDetalleSolicitud();
                            $objDetalleSolicitud->setServicioId($objServicioSelected);
                            $objDetalleSolicitud->setTipoSolicitudId($objAdmiTipoSolicitud);
                            $objDetalleSolicitud->setObservacion($strObservacion);
                            $objDetalleSolicitud->setUsrCreacion($strUsuario);
                            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolicitud->setEstado("Pendiente");
                            $emComercial->persist($objDetalleSolicitud);

                            //SE GUARDA EL HISTORIAL DE LA SOLICITUD CREADA
                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolHist->setIpCreacion($strIpCreacion);
                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHist->setUsrCreacion($strUsuario);
                            $objDetalleSolHist->setEstado('Pendiente');
                            $objDetalleSolHist->setObservacion($strObservacion);
                            $emComercial->persist($objDetalleSolHist);
                            
                            if( $strNombreSolicitud == "SOLICITUD CAMBIO PERSONAL PLANTILLA" )
                            {
                                if( $intIdPersonaComisionistaNew > 0 && !empty($strTipoComisionista) )
                                {
                                    //SE OBTIENE LA CARACTERISTICA DEL CAMBIO RESPECTIVA
                                    $arrayCaracteristicasParametros = array('estado'                    => 'Activo', 
                                                                            'descripcionCaracteristica' => 'CAMBIO_'.$strTipoComisionista);
                                    $objCambioCaracteristica        = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                  ->findOneBy( $arrayCaracteristicasParametros );

                                    if( !is_object($objCambioCaracteristica) )
                                    {
                                        throw new \Exception('No se encontró la característica por el cambio a realizar. ('.
                                                             $strTipoComisionista.')');
                                    }//( !is_object($objCambioCaracteristica) )

                                    $objDetalleSolCaracteristicas = new InfoDetalleSolCaract();
                                    $objDetalleSolCaracteristicas->setCaracteristicaId($objCambioCaracteristica);
                                    $objDetalleSolCaracteristicas->setDetalleSolicitudId($objDetalleSolicitud);
                                    $objDetalleSolCaracteristicas->setEstado('Activo');
                                    $objDetalleSolCaracteristicas->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolCaracteristicas->setUsrCreacion($strUsuario);
                                    $objDetalleSolCaracteristicas->setValor($intIdPersonaComisionistaNew);
                                    $emComercial->persist($objDetalleSolCaracteristicas);
                                    
                                    $strObservacion = "Se desea realizar el siguiente cambio de ".$strTipoComisionista.":<br/>";
                                    
                                    if( $strTipoComisionista == "VENDEDOR" )
                                    {
                                        //INFORMACION DEL VENDEDOR Y SUBGERENTE ACTUAL
                                        $strObservacion .= "<b>".$strTipoComisionista." Actual:</b> ".$strPersonaComisionistaOld."<br/>";
                                        
                                        /**
                                         * SE VERIFICA SI EL SERVICIO TIENE ASOCIADO EN LA INFO_SERVICIO_COMISION EL CARGO DE SUBGERENTE PARA SER
                                         * ACTUALIZADO
                                         */
                                        $boolTieneSubgerente     = false;
                                        $arrayParametrosComision = array('arrayEstados'       => array('Activo'),
                                                                         'intIdServicio'      => $intIdServicioSelected,
                                                                         'strRolComisionista' => 'SUBGERENTE');
                                        $arrayResultadoComision  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                               ->getServicioComision($arrayParametrosComision);

                                        if( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1
                                            && isset($arrayResultadoComision['arrayRegistros']) 
                                            && !empty($arrayResultadoComision['arrayRegistros']) )
                                        {
                                            $boolTieneSubgerente = true;
                                        }//( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1...

                                        if( $boolTieneSubgerente )
                                        {
                                            $objComisionistaOld = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                              ->findOneById($intIdPersonaComisionistaOld);

                                            if( !is_object($objComisionistaOld) )
                                            {
                                                throw new \Exception('No se encontró el objecto del comisionista antiguo.');
                                            }//( !is_object($objComisionistaOld) )

                                            $intReportaPersonaOld = $objComisionistaOld->getReportaPersonaEmpresaRolId();

                                            if( $intReportaPersonaOld > 0 )
                                            {
                                                $objReportaPersonaEmpresaRolOld = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                                              ->findOneById($intReportaPersonaOld);

                                                if( !is_object($objReportaPersonaEmpresaRolOld) )
                                                {
                                                    throw new \Exception('No se encontró el objecto del subgerente al que reporta el vendedor '.
                                                                         'actual.');
                                                }//( !is_object($objReportaPersonaEmpresaRolOld) )

                                                $objReportaPersonaOld = $objReportaPersonaEmpresaRolOld->getPersonaId();

                                                if( !is_object($objReportaPersonaOld) )
                                                {
                                                    throw new \Exception('No se encontró la información personal del subgerente al que reporta el '.
                                                                         'vendedor actual.');
                                                }//( !is_object($objReportaPersonaOld) )

                                                $strNombreReportaOld = ucwords(strtolower(trim($objReportaPersonaOld->__toString())));
                                                $strObservacion      .= "<b>SUBGERENTE Actual:</b> ".$strNombreReportaOld."<br/>";
                                            }//( $intReportaPersonaOld > 0 )
                                        }//( $boolTieneSubgerente )


                                        //INFORMACION DEL VENDEDOR Y SUBGERENTE NUEVO
                                        $strObservacion .= "<b>Nuevo ".$strTipoComisionista.":</b> ".$strPersonaComisionistaNew."<br/>";

                                        if( $boolTieneSubgerente )
                                        {
                                            $objComisionistanNew = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                               ->findOneById($intIdPersonaComisionistaNew);

                                            if( !is_object($objComisionistanNew) )
                                            {
                                                throw new \Exception('No se encontró el objecto del comisionista nuevo.');
                                            }//( !is_object($objComisionistanNew) )

                                            $intReportaPersonaNew = $objComisionistanNew->getReportaPersonaEmpresaRolId();

                                            if( $intReportaPersonaNew > 0 )
                                            {
                                                $objReportaPersonaEmpresaRolNew = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                                              ->findOneById($intReportaPersonaNew);

                                                if( !is_object($objReportaPersonaEmpresaRolNew) )
                                                {
                                                    throw new \Exception('No se encontró el objecto del subgerente al que reporta el vendedor '.
                                                                         'nuevo.');
                                                }//( !is_object($objReportaPersonaEmpresaRolNew) )

                                                $objReportaPersonaNew = $objReportaPersonaEmpresaRolNew->getPersonaId();

                                                if( !is_object($objReportaPersonaNew) )
                                                {
                                                    throw new \Exception('No se encontró la información personal del subgerente al que reporta el '.
                                                                         'vendedor nuevo.');
                                                }//( !is_object($objReportaPersonaNew) )

                                                $strNombreReportaNew = ucwords(strtolower(trim($objReportaPersonaNew->__toString())));
                                                $strObservacion      .= "<b>Nuevo SUBGERENTE:</b> ".$strNombreReportaNew."<br/>";

                                                //SE OBTIENE LA CARACTERISTICA DEL CAMBIO RESPECTIVA
                                                $arrayCaracteristicasParametros['descripcionCaracteristica'] = 'CAMBIO_SUBGERENTE';

                                                $objCambioCaracteristica = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                       ->findOneBy( $arrayCaracteristicasParametros );

                                                if( !is_object($objCambioCaracteristica) )
                                                {
                                                    throw new \Exception('No se encontró la característica por el cambio de SUBGERENTE a realizar.');
                                                }//( !is_object($objCambioCaracteristica) )

                                                $objDetalleSolCaracteristicaSubgerente = new InfoDetalleSolCaract();
                                                $objDetalleSolCaracteristicaSubgerente->setCaracteristicaId($objCambioCaracteristica);
                                                $objDetalleSolCaracteristicaSubgerente->setDetalleSolicitudId($objDetalleSolicitud);
                                                $objDetalleSolCaracteristicaSubgerente->setEstado('Activo');
                                                $objDetalleSolCaracteristicaSubgerente->setFeCreacion(new \DateTime('now'));
                                                $objDetalleSolCaracteristicaSubgerente->setUsrCreacion($strUsuario);
                                                $objDetalleSolCaracteristicaSubgerente->setValor($intReportaPersonaNew);
                                                $emComercial->persist($objDetalleSolCaracteristicaSubgerente);
                                            }//( $intReportaPersonaNew > 0 )
                                        }//( $boolTieneSubgerente )
                                    }
                                    else
                                    {
                                        if( !empty($strPersonaComisionistaOld) && !empty($strPersonaComisionistaNew) )
                                        {
                                            $strObservacion .= "<b>".$strTipoComisionista." Actual:</b> ".$strPersonaComisionistaOld."<br/>";
                                            $strObservacion .= "<b>Nuevo ".$strTipoComisionista.":</b> ".$strPersonaComisionistaNew;
                                        }
                                        else
                                        {
                                            throw new \Exception('No se ha encontrado el comisionista nuevo ni antiguo que se desean cambiar.');
                                        }//( !empty($strPersonaComisionistaOld) && !empty($strPersonaComisionistaNew) )
                                    }//( $strTipoComisionista == "VENDEDOR" )
                                }
                                else
                                {
                                    throw new \Exception('No se han enviado los parámetros correspondientes para realizar el cambio de personal en '.
                                                         'la plantilla de comisionistas. IdPersonalNuevo('.$intIdPersonaComisionistaNew.'), '.
                                                         'TipoComisionista('.$strTipoComisionista.')');
                                }//( !empty($intIdPersonaComisionistaNew) && !empty($strCaracteristicaSolicitud) )
                            }//( $strNombreSolicitud == "SOLICITUD CAMBIO COMISION" )
                            elseif( $strNombreSolicitud == "SOLICITUD CAMBIO COMISION" )
                            {
                                if( $intIdServicioComision > 0 )
                                {
                                    if( floatval($floatComisionVentaNew) > 0 )
                                    {
                                        $objDetalleSolicitud->setPrecioDescuento($floatComisionVentaNew);
                                    }
                                    else
                                    {
                                        throw new \Exception('El valor de comisión escrito no es válido');
                                    }//( floatval($floatComisionVentaNew) > 0 )

                                    //SE OBTIENE LA CARACTERISTICA DEL CAMBIO COMISION
                                    $arrayCaracteristicasParametros = array('estado'                    => 'Activo', 
                                                                            'descripcionCaracteristica' => 'CAMBIO_COMISION');
                                    $objCambioCaracteristica        = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                  ->findOneBy( $arrayCaracteristicasParametros );

                                    if( !is_object($objCambioCaracteristica) )
                                    {
                                        throw new \Exception('No se encontró la característica por el cambio de comisión');
                                    }//( !is_object($objCambioCaracteristica) )

                                    $objDetalleSolCaracteristicas = new InfoDetalleSolCaract();
                                    $objDetalleSolCaracteristicas->setCaracteristicaId($objCambioCaracteristica);
                                    $objDetalleSolCaracteristicas->setDetalleSolicitudId($objDetalleSolicitud);
                                    $objDetalleSolCaracteristicas->setEstado('Activo');
                                    $objDetalleSolCaracteristicas->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolCaracteristicas->setUsrCreacion($strUsuario);
                                    $objDetalleSolCaracteristicas->setValor($intIdServicioComision);
                                    $emComercial->persist($objDetalleSolCaracteristicas);
                                    
                                    $strObservacion = "Se desea realizar el siguiente cambio de Comisión al ".$strTipoComisionista.":<br/>";
                                    $strObservacion .= "<b>Comisión Actual:</b> ".$floatComisionVentaOld."<br/>";
                                    $strObservacion .= "<b>Nueva Comisión:</b> ".$floatComisionVentaNew;
                                }
                                else
                                {
                                    throw new \Exception('No se ha enviado el id del servicio comision que se desea editar.');
                                }//( $intIdServicioComision > 0 )
                            }//( $strNombreSolicitud == "SOLICITUD CAMBIO COMISION" )
                            
                            $objDetalleSolicitud->setObservacion($strObservacion);
                            $emComercial->persist($objDetalleSolicitud);
                            
                            $objDetalleSolHist->setObservacion($strObservacion);
                            $emComercial->persist($objDetalleSolHist);
                        }//( !empty($strNombreSolicitud) )
                    }//foreach($arrayNombresSolicitudes as $strNombreSolicitud)
                }
                else
                {
                    throw new \Exception('No se ha enviado el nombre de la solicitud a crear');
                }//( !empty($arrayNombresSolicitudes) )

                $emComercial->flush();
                $emComercial->getConnection()->commit();
                
                $strMensajeRespuesta = 'Se ha(n) creado la(s) solicitud(es) con éxito.';
            }
            else
            {
                throw new \Exception('No se han enviado todos los parámetros necesarios para la creación de la solicitud. IdServicio('.
                                     $intIdServicioSelected.'), Solicitudes('.$strNombresSolicitudes.')');
            }//( !empty($intIdServicioSelected) )
        }
        catch(\Exception $e) 
        {
            $strMensajeRespuesta = 'Hubo un problema al obtener al generar la(s) solicitud(es).';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoPuntoController.getPersonalComisionistaAction', 
                                       'Error al obtener al personal para seleccionar como comisionistas. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
            
            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();
        }
        
        $objResponse->setContent($strMensajeRespuesta);
        
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'ajaxVerificaDatosDeEnvioPunto'.
     * 
     * Función que verifica la asignacion de datos de envio al punto de facturacion en la InfoPuntoDatoAdicional.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 16-06-2017
     *
     */	   
    public function ajaxVerificaDatosDeEnvioPuntoAction()
    {   
        //Obtiene parametros enviados desde el ajax
        $ObjRequest                 = $this->get('request');
        $intIdPuntoPadre            = $ObjRequest->get('idPuntoPadre');
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $em                         = $this->getDoctrine()->getManager('telconet');
        $strMessage                 = '';
        $intContadorTieneDatosEnvio = 1;
        $strNombreEnvio             = '';
        $objAdmiSector              = null;
        $strDireccionEnvio          = '';
        $strEmailEnvio              = '';
        $strTelefonoEnvio           = '';
        
        try {
            if( isset( $intIdPuntoPadre ) && !empty( $intIdPuntoPadre ) )
            {
                $entityInfoPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($intIdPuntoPadre);

                if( is_object($entityInfoPuntoDatoAdicional) )
                {   
                    $strNombreEnvio =  $entityInfoPuntoDatoAdicional->getNombreEnvio();
                    
                    if(empty( $strNombreEnvio ))
                    {
                        $intContadorTieneDatosEnvio = $intContadorTieneDatosEnvio + 1;
                    }
                    
                    $objAdmiSector = $entityInfoPuntoDatoAdicional->getSectorId();
                    
                    if(!is_object( $objAdmiSector ))
                    { 
                        $intContadorTieneDatosEnvio = $intContadorTieneDatosEnvio + 1;
                    }
                    
                    $strDireccionEnvio =  $entityInfoPuntoDatoAdicional->getDireccionEnvio();
                    
                    if(empty( $strDireccionEnvio ))
                    {
                        $intContadorTieneDatosEnvio = $intContadorTieneDatosEnvio + 1;
                    }
                    
                    $strEmailEnvio = $entityInfoPuntoDatoAdicional->getEmailEnvio();
                    
                    if(empty( $strEmailEnvio ))
                    {
                        $intContadorTieneDatosEnvio = $intContadorTieneDatosEnvio + 1;
                    }
                    
                    $strTelefonoEnvio = $entityInfoPuntoDatoAdicional->getTelefonoEnvio();
                    
                    if(empty( $strTelefonoEnvio ))
                    {
                        $intContadorTieneDatosEnvio = $intContadorTieneDatosEnvio + 1;
                    }
                }

                if ($intContadorTieneDatosEnvio > 1)
                {
                    $strMessage = 'No se ha definido Datos de Envio al padre de facturacion, favor proceder con la asignacion para continuar.'; 
                }
                else
                {
                    $strMessage = 'OK'; 
                }
            }
            
            $objResponse->setContent($strMessage);
            
        }catch (\Exception $e) 
        {
            error_log('InfoPuntoController.ajaxVerificaDatosDeEnvioPuntoAction: ' . $e->getMessage());
            $objResponse->setContent($e->getMessage());
        }
        
        return $objResponse;
    }

    /**
     * Documentación para el método 'obteneCoordenadaSugeridaAction'.
     *
     * Función que obtiene las coordenadas sugeridas para actualizar la información del punto
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26-02-2018
     *
     */
    public function obteneCoordenadaSugeridaAction()
    {
        $objRequest            = $this->get('request');
        $intIdPunto            = $objRequest->get('idPunto');
        $serviceUtil           = $this->get('schema.Util');
        $objJsonResponse       = new JsonResponse();
        $strIpCreacion         = $objRequest->getClientIp();
        $objSession            = $objRequest->getSession();
        $strUsuario            = $objSession->get('user');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $servicePunto          = $this->get('comercial.InfoPunto');
        try
        {
            $arrayParametrosCoordenada = array('intIdPunto'  => $intIdPunto);
            $objCoordenadaSugerida     = $servicePunto->obtenerCoordenadaSugerida($arrayParametrosCoordenada);
            if(is_object($objCoordenadaSugerida))
            {
                $arrayCoordenadaExplode                     = explode(",",$objCoordenadaSugerida->getValor());
                $arrayCoordenadaSugerida['strLatitud']      = $arrayCoordenadaExplode[0];
                $arrayCoordenadaSugerida['strLongitud']     = $arrayCoordenadaExplode[1];
                $arrayCoordenadaSugerida['boolTieneTarea']  = true;
            }
            else
            {
                $arrayCoordenadaSugerida['strLatitud']      = "";
                $arrayCoordenadaSugerida['strLongitud']     = "";
                $arrayCoordenadaSugerida['boolTieneTarea']  = false;
            }
        }
        catch(\Exception $e)
        {
            $arrayCoordenadaSugerida['strMensajeError'] = 'Hubo un problema al obtener las coordenadas del punto';

            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.obteneCoordenadaSugeridaAction',
                                       'Error al obtener las coordenadas sugeridas. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData($arrayCoordenadaSugerida);
        return $objJsonResponse;
    }

    /**
     * Documentación para el método 'guardarCoordenadasSugeridasAction'.
     *
     * Función que actualiza la coordenada del punto.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26-02-2018
     *
     */
    public function guardarCoordenadasSugeridasAction()
    {
        $objRequest            = $this->get('request');
        $intIdPunto            = $objRequest->get('idPunto');
        $strLatitud            = $objRequest->get('strLatitud');
        $strLongitud           = $objRequest->get('strLongitud');
        $serviceUtil           = $this->get('schema.Util');
        $objJsonResponse       = new JsonResponse();
        $strIpCreacion         = $objRequest->getClientIp();
        $objSession            = $objRequest->getSession();
        $strUsuario            = $objSession->get('user');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $servicePunto          = $this->get('comercial.InfoPunto');
        try
        {
            $arrayCoordenadaSugerida['strMensaje'] = 'Hubo un problema al actualizar las coordenadas del punto.';
            $arrayParametrosCoordenada             = array('intIdPunto'       => $intIdPunto,
                                                           'strLatitud'       => $strLatitud,
                                                           'strLongitud'      => $strLongitud,
                                                           'strUsrCreacion'   => $strUsuario,
                                                           'strIpCreacion'    => $strIpCreacion);
            $arrayRespuestaActualizacion           = $servicePunto->actualizarCoordenadaSugerida($arrayParametrosCoordenada);
            if( !empty($arrayRespuestaActualizacion) )
            {
                if( isset( $arrayRespuestaActualizacion["strStatus"] ) && !empty( $arrayRespuestaActualizacion["strStatus"] ) )
                {
                    if($arrayRespuestaActualizacion["strStatus"] == "OK")
                    {
                        $arrayCoordenadaSugerida['strMensaje'] = 'Se actualizarón las coordenadas del punto.';
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $arrayCoordenadaSugerida['strMensaje'] = 'Hubo un problema al actualizar las coordenadas del punto.';

            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoPuntoController.guardarCoordenadasSugeridasAction',
                                       'Error al obtener las coordenadas sugeridas. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData($arrayCoordenadaSugerida);
        return $objJsonResponse;
    }
    
    /**
     * 
     * Metodo que se encarga de redireccionar a la pantalla de edicion de Soluciones
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 13-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 16-17-2020 - Se obtiene las soluciones del punto mediante las nuevas estructuras de solución.
     *
     * @Secure(roles="ROLE_9-5717")
     *
     * @param type $intIdPunto
     * @return type
     */
    public function editarSolucionAction($intIdPunto)
    {
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $serviceInfoSolucion = $this->get('comercial.InfoSolucion');
        $strIpUsuario        = $objRequest->getClientIp();
        $strUsuario          = $objSession->get('user');
        $strLoginEmpleado    = '';
        $strNombreEmpleado   = '';

        $objPunto             = $emComercial->getRepository("schemaBundle:InfoPunto")->find($intIdPunto);
        $arrayFrecuenciaItem  = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get("FRECUENCIA_FACTURACION", "", "", "", "", "", "", "", "", $objSession->get('idEmpresa'));

        $arrayParametros            = array();
        $arrayParametros['EMPRESA'] = $objSession->get('idEmpresa');
        $arrayParametros['LOGIN']   = is_object($objPunto)?$objPunto->getUsrVendedor():'';

        $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

        if($arrayResultado['TOTAL'] > 0)
        {
            $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
            $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
        }

        $arrayCliente = $objSession->get('cliente');

        if(!empty($arrayCliente))
        {
            $tipoRol = $arrayCliente['nombre_tipo_rol'];
        }

        //Obtener el resumen de las soluciones existentes por punto
        $arraySolucionesPorPunto = array();
        $arrayRequest  = array ('puntoId' => $intIdPunto,'estado' => 'Activo');
        $arrayResponse = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $strUsuario,
                                                              'strIp'        =>  $strIpUsuario,
                                                              'strOpcion'    => 'soluciondc',
                                                              'strEndPoint'  => 'listarSolucionesPorPunto',
                                                              'arrayRequest' =>  $arrayRequest));

        if ($arrayResponse['status'] && !empty($arrayResponse['data']))
        {
            $arraySolucionesPorPunto = $arrayResponse['data'];
        }

        return $this->render('comercialBundle:infopunto:editarSolucion.html.twig',
                              array('arraySolucionesPorPunto' => $arraySolucionesPorPunto,
                                    'idPunto'                 => $intIdPunto,
                                    'rol'                     => $tipoRol,
                                    'prefijoEmpresa'          => $objSession->get('prefijoEmpresa'),
                                    'loginEmpleado'           => $strLoginEmpleado,
                                    'nombreEmpleado'          => $strNombreEmpleado,
                                    'frecuencia'              => $arrayFrecuenciaItem));
    }
    
    /*
     * @author Josselhin Moreira Quezada<kjmoreira@telconet.ec>
     * Documentación para el método 'getMotivosEliminarAction'.
     * @version 1.0
     * @since 2-04-2019
     * Método que retorna los motivos para eliminar según la relación del sistema.
     * 
     * @return JsonResponse $objRespuesta
     */
    public function getMotivosEliminarAction()
    {
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        
        $intStart = $objPeticion->query->get('start');
        $intLimit = $objPeticion->query->get('limit');
        
        $entitySeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>137, "accionId"=>100));
	$objRelacionSistemaId      = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;      		
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_general")
                        ->getRepository('schemaBundle:AdmiMotivo')
                        ->generarJson("","Activo",$intStart,$intLimit, $objRelacionSistemaId);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    /**
     * formasContactoPorCodigoAjaxAction, obtiene las formas de contacto por código parametrizado.
     *      
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function formasContactoPorCodigoAjaxAction()
    {
        $objSession             = $this->get('request')->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $serviceCliente         = $this->get('comercial.Cliente');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $objReturnResponse      = new ReturnResponse();
        $arrayCodFormasContacto = array();
        $arrayFormasContacto    = array();
        
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('COD_FORMA_CONTACTO', 
                                                      'COMERCIAL',
                                                      'COD_FORMA_CONTACTO', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '',  
                                                      '',  
                                                      '',  
                                                      $strCodEmpresa);

            if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
            {                                
                foreach($arrayAdmiParametroDet as $arrayParametro)
                {
                    $arrayCodFormasContacto[] = $arrayParametro['valor1'];
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'Error: No existen parametros configurados.');
                $objResponse        = new Response();
                $objResponse->headers->set('Content-Type', 'text/json');                
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }             
            
            $arrayFormasContacto = $serviceCliente->getFormasContactoByCodigo($arrayCodFormasContacto);
       
        }
        else
        {
            $arrayFormasContacto = $serviceCliente->obtenerFormasContacto();
        }

        $objResponse         = new Response(json_encode(array('formasContacto' => $arrayFormasContacto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * ajaxFormasContactoPuntoPorTipoAction
     *
     * Función para obtener las formas de contactos del punto cliente por Tipo de forma de contacto.
     *
     * @return $objRespuesta
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-03-2020 
     */
    public function ajaxFormasContactoPuntoPorTipoAction()
    {
        $objRequest             = $this->getRequest();
        $intLimit               = $objRequest->get("limit");        
        $intStart               = $objRequest->get("start");
        $intPuntoId             = $objRequest->get("puntoId");        
        $emComercial            = $this->get('doctrine')->getManager('telconet');      
        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $objSession             = $this->get('request')->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $arrayCodFormasContacto = array();
        $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('COD_FORMA_CONTACTO', 
                                                  'COMERCIAL',
                                                  'COD_FORMA_CONTACTO', 
                                                  '',  
                                                  '',
                                                  '', 
                                                  '',  
                                                  '',  
                                                  '', 
                                                  $strCodEmpresa);

        if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
        {                                
            foreach($arrayAdmiParametroDet as $arrayParametro)
            {
                $arrayCodFormasContacto[] = $arrayParametro['valor1'];
            }
        }
       
        $arrayParametros  = array ('intPuntoId'              => $intPuntoId,
                                   'strEstado'               => 'Activo',                                   
                                   'arrayCodFormasContacto'  => $arrayCodFormasContacto,
                                   'intLimit'                => $intLimit,
                                   'intStart'                => $intStart);
        
        $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')->getArrayFormasContactoPorTipo($arrayParametros);
        $arrayRegistros = $arrayResultado['registros'];
        if(empty($arrayRegistros))
        {
            $arrayRegistros = array(array());
        }
        $objResponse = new Response(json_encode(array('total' => $arrayResultado['total'], 'personaFormasContacto' => $arrayRegistros)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * ajaxReingresoOrdenServicioAction
     *
     * Función que reingresa la orden de servicio automática mediante un proceso en base de datos.
     *
     * @return $objRespuesta
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 26-08-2019
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 18-02-2020 - Se modifican y se agregan validaciones para el reingreso de OS automática, como: validar Pre-planificación Previa,
     *                           se parametriza y modifica validación de Motivo de Rechazo/Anulación, se corrige validación de 30 días de Reingreso
     *                           y se crea validación de Documento de Devolución sobre facturas de instalación. 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 18-05-2021  -Se modifica validación PR-001-OP-004: Validar Documento de Devolución.
     *                           Se deberá permitir reingresar la OS cuando existe una devolución siempre que se tenga una nueva factura de 
     *                           instalación pagada.
     *                           Se agrega validación que la OS tenga el contrato asociado con un estado activo y que el cliente también se  
     *                           encuentre en estado activo. 
     *                           Se habilita Edición de los Datos Geograficos en base a la lectura del parametro 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
     *                           y parametro detalle: 'PERMITE_EDITAR_DATOS_GEOGRAFICOS' que permitirá habilitar los combos para la edición de la 
     *                           sectorización (Jurisdicción, Canton,Parroquia, Sector) del punto.
     *                           Se agrega al array de parametros el nuevo Login generado por edicion de los Datos geograficos (Jurisdicción, Canton,
     *                           parroquia, Sector)
     *                           Se agrega validacion que exista la relacion del servicio origen del traslado en el caso del Reingreso de una
     *                           Orden de servicio de tipo: T (Traslado) 
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.3 30-09-2022  Se añade nuevos parámetros para la recepción, validación y reingreso de Servicios Adicionales con estado
     *                            Anulado o Rechazadas asociados al punto.
     *                          Se invoca nueva función para obtener array de servicios adicionales a reingresar.
     */
    public function ajaxReingresoOrdenServicioAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $serviceUtil            = $this->get('schema.Util');
        $objPeticion            = $this->get('request');
        $objSession             = $objPeticion->getSession();
        $strUserSession         = $objSession->get('user');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strIpSession           = $objPeticion->getClientIp();
        $arrayDatos             = (array) json_decode($objPeticion->get('datos'));
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $objInfoPuntoRepo       = $emComercial->getRepository('schemaBundle:InfoPunto');
        $objInfoServicioService = $this->get('comercial.InfoServicio');
        $servicePersonaFormCont = $this->get('comercial.InfoPersonaFormaContacto');
        $intIdPais              = $objSession->get('intIdPais');
        $strNombrePais          = $objSession->get('strNombrePais');
        $strRSAdicionales       = $arrayDatos['strRSAdicionales'];
        $arrayIdServAdicionales = array();
        $arrayFormasContacto    = array();
        $intA                   = 0;
        $intX                   = 0;

        try
        {
            if (empty($arrayDatos))
            {
                throw new \Exception('Error : Error al obtener los datos.<br/>'.
                                     'Si el problema persiste, por favor comunicar a Sistemas.');
            }

            //Verificamos que el usuario no tenga un proceso ejecutándose.
            $strNombreJob   = 'JOB_REINGRESO_OS_'.$arrayDatos['intIdServicio'].'';
            $arrayResultJob = $emComercial->getRepository('schemaBundle:InfoDetalle')
                                          ->existeJobReporteTarea(array ('strNombreJob' => $strNombreJob));

            if ($arrayResultJob['status'] === 'fail')
            {
                throw new \Exception($arrayResultJob['message']);
            }

            if ($arrayResultJob['status'] === 'ok' && $arrayResultJob['cantidad'] > 0)
            {
                throw new \Exception('Error : <b>Estimado usuario</b>.<br/><br/>'.
                                     'El servicio ya cuenta con un proceso ejecutándose, por favor<br/>'.
                                     'intente de nuevo en unos minutos.');
            }

            //Realizamos las validaciones necesarias antes de clonar el servicio.
            $arrayValidarOS = $objInfoServicioService->validarReingresoOrdenServicio(array('strUsuarioCreacion' => $strUserSession,
                                                                                           'strIpCreacion'      => $strIpSession,
                                                                                           'intIdServicio'      => $arrayDatos['intIdServicio'],
                                                                                           'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                                                           'strCodEmpresa'      => $strCodEmpresa,
                                                                                           'intIdPersona'       => $arrayDatos['intIdPersona'],
                                                                                           'strSoloValidar'     => $arrayDatos['soloValidar'],
                                                                                           'strFlujo'           => array('validaOrigenTraslado',
                                                                                                                         'validaEstadosCliente',
                                                                                                                         'validaEstadosContrato',
                                                                                                                         'validarDatosGeograficos',
                                                                                                                         'validarDiasOrdenServicio',
                                                                                                                         'validarFormaPago',
                                                                                                                         'validarFacturaInstalacion',
                                                                                                                         'validarReingresoEjecutado',
                                                                                                                         'devoluciones')));
            
            if (!$arrayValidarOS['status'])
            {
                throw new \Exception($arrayValidarOS['message']);
            }

            /**
             * Bandera que sirve solo para realizar las validaciones antes
             * de habilitar la ventana informativa y de modificación.
             */
            if (strtoupper($arrayDatos['soloValidar']) === 'SI')
            {
                $arrayRespuestaProceso = array ('status' => true);
            }
            else
            {
                //Valida si existen servicios adicionales por reingresar
                if ($strRSAdicionales == "S")
                {
                    $arrayParametros = array('strUsuarioCreacion' => $strUserSession,
                                            'strIpCreacion'      => $strIpSession,
                                            'intIdServicio'      => $arrayDatos['intIdServicio'],
                                            'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                            'strCodEmpresa'      => $strCodEmpresa);

                    $arrayValidaRSOAdicional = $objInfoServicioService->validaRSOAdicionales($arrayParametros);
                    if ($arrayValidaRSOAdicional['status'] != 'OK')
                    {
                        throw new \Exception($arrayValidaRSOAdicional['mensaje']);
                    }
                    $arrayIdServAdicionales = $arrayValidaRSOAdicional['arrayIdServAdicionales'];
                }

                if ($arrayDatos['strFormasContactos'] !== '' && !empty($arrayDatos['strFormasContactos']))
                {
                    $arrayFormasCont = explode(",",$arrayDatos['strFormasContactos']);

                    foreach ($arrayFormasCont as $strDatos)
                    {
                        if($intA == 3)
                        {
                            $intA = 0;
                            $intX++;
                        }

                        if ($intA == 1)
                        {
                            $arrayFormasContacto[$intX]['formaContacto'] = $strDatos;
                        }

                        if ($intA == 2)
                        {
                            $arrayFormasContacto[$intX]['valor'] = $strDatos;
                        }

                        $intA++;
                    }

                    //Proceso para validar las formas de contacto
                    $arrayValidaciones = $servicePersonaFormCont->validarFormasContactos(array ('strPrefijoEmpresa'   => $strPrefijoEmpresa,
                                                                                                'arrayFormasContacto' => $arrayFormasContacto,
                                                                                                'strOpcionPermitida'  => 'SI',
                                                                                                'strNombrePais'       => $strNombrePais,
                                                                                                'intIdPais'           => $intIdPais));

                    if (!empty($arrayValidaciones))
                    {
                        foreach($arrayValidaciones as $arrayMensajesValidaciones)
                        {
                            foreach($arrayMensajesValidaciones as $strMensajeValidacion)
                            {
                                $strError = $strError.$strMensajeValidacion."<br/>";
                            }
                        }

                        throw new \Exception("Error : ".$strError);
                    }
                }

                //Proceso que levanta el job con auto-drop para el reingreso de la OS automática.
                $arrayDatos['strUsuario']           = $strUserSession;
                $arrayDatos['strIp']                = $strIpSession;
                $arrayDatos['strFormasContactos']   = null;
                $arrayDatos['arrayFormasContactos'] = $arrayFormasContacto;
                $arrayDatos['strCodEmpresa']        = $strCodEmpresa;
                $arrayDatos['strPrefijoEmpresa']    = $strPrefijoEmpresa;                
                $arrayDatos['strNombrePais']        = $strNombrePais;                
                $arrayDatos['intIdPais']            = $intIdPais;                
                $arrayDatos['serviceUtil']          = $serviceUtil;
                $arrayDatos['intIdServicioNuevo']   = 'Ln_IdServicioNuevo'; // Parametro Usado para hacer replace con el IdServicioNuevo clonado

                //Parametros para reingreso de servicios adicionales
                $arrayDatos['arrayIdServAdicionales'] = $arrayIdServAdicionales;
                $arrayDatos['arrayIdServiciosNuevosAdc']  = 'Lt_IdServiciosNuevosAdc';

                $arrayRespuestaProceso              = $objInfoPuntoRepo->jobReingresoAutomatico($arrayDatos);

            }

            $objResultado = json_encode($arrayRespuestaProceso);
        }
        catch (\Exception $objException)
        {
            $strMessage = 'El proceso de creación de la orden de servicio automática ha fallado.<br/>'.
                          'Por favor verificar la observación en el historial del servicio,<br/>'.
                          'y si no se ha registrado ningún comentario, por favor comunicar a Sistemas.';

            $boolInsertError = false;
            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
                $boolInsertError = true;
            }

            if (!$boolInsertError)
            {
                $serviceUtil->insertError('Telcos+',
                                          'InfoPuntoController->ajaxReingresoOrdenServicioAction',
                                           substr($objException->getMessage(), 0, 4000),
                                           $strUserSession,
                                           $strIpSession);
            }
            $objResultado = json_encode(array ('status'  => false,
                                               'message' => $strMessage));
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

   /**
     *
     * Documentación para la función 'getValidaCltDistribuidorAction'.
     *
     * Función que retorna si existe o no la identificación como Cliente o Pre-cliente de TN.
     *
     * @return $objResponse - Si/No.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 24-05-2021
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     * 
     * @author Henry Pérez García <hrperez@telconet.ec>
     * @version 1.2 25-05-2023 - Se agrega filtro de estado activo para verifica si existe cliente en TelcoS+,
     *                           se valida en TelcoCRM que exista almenos una propuesta abierta.
     */
    public function getValidaCltDistribuidorAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIdentificacion      = $objRequest->get("strIdentificacion")   ? $objRequest->get("strIdentificacion"):"";
        $strTipoIdentificacion  = $objRequest->get("strTipoIdentificacion")? $objRequest->get("strTipoIdentificacion"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')      ? $objSession->get('prefijoEmpresa'):"";
        $intIdPais              = $objRequest->getSession()->get('intIdPais');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceComercialCrm    = $this->get('comercial.ComercialCRM');
        $serviceUtil            = $this->get('schema.Util');
        $strMensaje             = "";
        try
        {
            if(!empty($strIdentificacion))
            {
                $arrayParamValidaIdentifica = array(
                                                        'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                        'strIdentificacionCliente'  => $strIdentificacion,
                                                        'intIdPais'                 => $intIdPais,
                                                        'strCodEmpresa'             => $intIdEmpresa
                                                    );
                $strMensaje = $emComercial->getRepository('schemaBundle:InfoPersona')
                              ->validarIdentificacionTipo($arrayParamValidaIdentifica);
                $arrayParametros                      = array();
                $arrayParametros['idEmpresa']         = $intIdEmpresa;
                $arrayParametros['razon_social']      = $strRazonSocial;
                $arrayParametros['estado']            = "Activo";
                $arrayParametros['identificacion']    = $strIdentificacion;
                $arrayParametros['tipo_persona']      = array('cliente','pre-cliente');
                $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $arrayResultado                       = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->findPersonasPorCriterios($arrayParametros);
                $arrayRegistros                       = $arrayResultado['registros'];
                $intTotal                             = $arrayResultado['total'];
                if(!empty($arrayRegistros) && is_array($arrayRegistros))
                {
                    $strMensaje = "Identificación ingresada, pertenece a un cliente de Telconet, ingresado en TelcoS+.";
                }
                else
                {
                    $arrayParametrosCRM   = array("strIdentificacion"  => $strIdentificacion,
                                                  "strRazonSocial"     => $strRazonSocial);
                    $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCRM,
                                                  "strOp"              => 'getDatosCliente',
                                                  "strFuncion"         => 'procesar');
                    $arrayRespuestaWSCrm  = $serviceComercialCrm->getRequestCRM($arrayParametrosWSCrm);
                    if(!empty($arrayRespuestaWSCrm) && (is_array($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"])))
                    {
                        foreach($arrayRespuestaWSCrm["resultado"] as $arrayItemWSCrm)
                        {
                            if(!empty($arrayItemWSCrm->strPropuestaCRM) && intval($arrayItemWSCrm->strPropuestaCRM)>0)
                            {
                                $strMensaje = "Identificación ingresada, pertenece a un cliente de Telconet, ingresado en TelcoCRM.";
                                break;
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+',
                                      'InfoPuntoController.getValidaCltDistribuidorAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode($strMensaje));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec> 
     * @version 1.0 07-07-2021
     * #GEO Consumo de ms catalogos , entrega ubicacion en base a las coordenadas
     * 
     * @return JsonResponse $objRespuesta
     */
    public function ajaxVerificarCatalogoAction()  
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user') : "";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp() : '127.0.0.1';
        $strLatitud             = $objRequest->get('latitud');
        $strLongitud            = $objRequest->get('longitud');
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $arrayParametros = array();

        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();
 
        $arrayParametros['usrCreacion'] = $strUsrCreacion;
        $arrayParametros['strIpMod']   =$strIpCreacion;       
        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        $arrayParametros['idEmpresa'] = $intIdEmpresa  ;
        $arrayParametros['latitud'] = $strLatitud;
        $arrayParametros['longitud'] = $strLongitud;
        $serviceUtil            = $this->get('schema.Util'); 
        $arrayResponse          = array(); 
        
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $arrayResponse =  $serviceInfoPunto->verificarCatalogoMS($arrayParametros);
        
        $arrayResponse = new Response(json_encode( $arrayResponse ));
        $arrayResponse->headers->set('Content-type', 'text/json');
        return $arrayResponse;
    }
    
    /**
     * Documentación para el método 'getDescargaDocumentosRutas'.
     * Este metodo obtiene los documentos a partir de la url
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 03-06-2021 
     * 
     */
    public function getDescargaDocumentosRutas($arrayParametros)
    {
        $intIdDocumento = $arrayParametros["intIdDocumento"];
        //Buscar el documento
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objArchivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);
        if($objArchivo)
        {
            $strUrl = $objArchivo->getUbicacionFisicaDocumento();
            return $strUrl;
        }
        else
        {
            throw $this->createNotFoundException();
        }
    }
    
    /**
     * Función que sirve para ejecutar el procedimiento de Base de Datos que ejecuta la validación del archivo csv y creación de rutas 
     * masivas
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 31-05-2021
     * 
     */
    public function ejecutaSubidaCsvRutas($arrayParametros)
    {
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strNombreArchivoRuta       = $arrayParametros["strNombreArchivoRuta"];
        $strExtensionArchivoRuta    = $arrayParametros["strExtensionArchivoRuta"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdDocumento             = $arrayParametros["intIdDocumento"];
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';

        try
        {
            if(!isset($strNombreArchivoRuta) || empty($strNombreArchivoRuta))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el nombre del archivo subido');
            }
            
            if(!isset($strExtensionArchivoRuta) || empty($strExtensionArchivoRuta))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener la extensión del archivo subido');
            }
            
            if(!isset($strUsrCreacion) || empty($strUsrCreacion))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el usuario en sesión');
            }
            
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_UPLOAD_CSV_RUTAS(
                                    :Pn_IdArchivoCsvPsm,
                                    :Pv_NombreArchivoPsm,
                                    :Pv_ExtensionArchivoPsm,
                                    :Pv_UsrCreacion,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pn_IdArchivoCsvPsm', $intIdDocumento);
            oci_bind_by_name($objStmt, ':Pv_NombreArchivoPsm', $strNombreArchivoRuta);
            oci_bind_by_name($objStmt, ':Pv_ExtensionArchivoPsm', $strExtensionArchivoRuta);
            oci_bind_by_name($objStmt, ':Pv_UsrCreacion', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 5);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar ejecutar la creación de scopes/policy. Por favor comuníquese con Sistemas!";
            }
            $strStatus  = "ERROR";
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * CargaDeArchivosMultiplesAction
     *
     * Metodo encargado de procesar el o los archivos de evidencia que el usuario desea subir de negociación
     *
     * @return json con resultado del proceso
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 23-09-2021
     * 
     */
    public function CargaDeArchivosMultiplesAction()
    {
        $objRequest     = $this->get('request');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');

        $intIdServicio      = $objRequest->get('intIdServicio') ? $objRequest->get('intIdServicio') : 0;
        $strCodigoDocumento = $objRequest->get('codigo') ? $objRequest->get('codigo') : "";
        $strSubirEnMsNfs    = $objRequest->get('subirEnMsNfs') ? $objRequest->get('subirEnMsNfs') : "N";
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");	
        $serviceUtil        = $this->get('schema.Util'); 
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $strUser            = $objSession->get('user') ? $objSession->get('user') : "";
        $strIdEmpresa       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $objServicio        = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        $strIpCreacion      = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $strEstadoEnviado   = "Aprobado";
        $strMetrajeC                       = 0;
        $floatValorCaractOCivil            = 0;
        $floatValorCaractFibraMetros       = 0;
        $floatValorCaractOtrosMateriales   = 0;
        $floatTotalPagar                   = 0;
        $arrayRespuesta     = array();
        
        //SOLICITUD DE MATERIALES EXCEDENTES                                        
        $entityTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");
        //SOLICITUD DE PLANIFICACION                                        
        $entityTipoSolicitudPla        = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

        $arrayArchivos      = $this->getRequest()->files->get('archivos');

        $arrayParametros     = array(
            "intIdServicio"         => $intIdServicio,
            "strCodigoDocumento"    => $strCodigoDocumento,
            "strPrefijoEmpresa"     => $strPrefijoEmpresa,
            "strUser"               => $strUser,
            "strIdEmpresa"          => $strIdEmpresa,
            "arrayArchivos"         => $arrayArchivos
        );
        //service donde está guardarArchivosMultiplesEvidenciasEnNfs
        $serviceCliente         = $this->get('comercial.Cliente'); 
        //servicio donde está registroSolicitudDeExcedenteMateriales
        $serviceSolicitud       = $this->get('comercial.Solicitudes');
        //servicio donde está registroSolicitudMaterial
        $serviceAutorizaciones  = $this->get('comercial.Autorizaciones');
        $strCobraTodo           = $objRequest->get('txt_copagos') ? $objRequest->get('txt_copagos') : "NO";
        try
        {
            if ($strSubirEnMsNfs == 'S') 
            {
                $arrayRespuesta = $serviceCliente->guardarArchivosMultiplesEvidenciasEnNfs($arrayParametros);

                // Si el correo se sube ok, el proceso se realiza
                if($arrayRespuesta['status']=='Ok')
                {
                    // Preguntar si tiene una solicitud de excedentes                
                    $objDetalleSolicitudExc = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                            "tipoSolicitudId" => $entityTipoSolicitud->getId()));
                    $boolCrearSolicitud = true;

                    if((is_object($objDetalleSolicitudExc))  && 
                    ( ($objDetalleSolicitudExc->getEstado()=='Pendiente')  || ($objDetalleSolicitudExc->getEstado()=='Aprobado') )   )
                    {
                        $boolCrearSolicitud = false;
                    } 
                        // Si envía NO el archivo se registra con la solicitud aprobada, el info_solicitud_material, se preplanifica, etc
                        if(($boolCrearSolicitud) && ($strCobraTodo!='on'))
                        {
                            $strSeguimiento = 'Se crea una solicitud de excedentes en estado '.$strEstadoEnviado.'.
                                            <br> Cliente autoriza el cobro.';
                            // Se crea la solicitud de excedentes
                            $arrayParametrosSolExc = array(
                                                        "emComercial"                => $emComercial,
                                                        "strClienteIp"               => $strIpCreacion,
                                                        "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                        "objServicio"                => $objServicio,
                                                        "strSeguimiento"             => $strSeguimiento,
                                                        "strUsrCreacion"             => $strUser,
                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                            $arrayVerificar = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                    <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                            }

                            // Solicitud de planificacion
                            $objDetalleSolicitudPla = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                                                    "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));
                            if(is_object($objDetalleSolicitudPla))
                            {
                                $strEstadoInfoDetalleSolicitud     = $objDetalleSolicitudPla->getEstado();
                                $intIdDetalleSolicitud             = $objDetalleSolicitudPla->getId();
                                
                                /* Para mostrar los valores registrados de Otros materiales, precio de obra civil, 
                                Cancela el cliente, asume el cliente, etc  */
                                $objCaracteristicaFibraMetros     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD');
                                $objCaracteristicaOCivil          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
                                $objCaracteristicaOtrosMateriales = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
                                                        
                                $objInfoDetalleSolCaractFibraMetros          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaFibraMetros,
                                                                        'detalleSolicitudId' => $intIdDetalleSolicitud  ));
                                $objInfoDetalleSolCaractOCivil          = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaOCivil,
                                                                        'detalleSolicitudId' => $intIdDetalleSolicitud  ));
                                $objInfoDetalleSolCaractOtrosMateriales = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaOtrosMateriales,
                                                                        'detalleSolicitudId' => $intIdDetalleSolicitud  ));
                
                                // Si existe la entidad obtiene el valor.
                                
                                if($objInfoDetalleSolCaractFibraMetros)
                                {
                                    $floatValorCaractFibraMetros     = $objInfoDetalleSolCaractFibraMetros->getValor();
                                }
                                if($objInfoDetalleSolCaractOCivil)
                                {
                                    $floatValorCaractOCivil          = $objInfoDetalleSolCaractOCivil->getValor();
                                }
                                if($objInfoDetalleSolCaractOtrosMateriales)
                                {
                                    $floatValorCaractOtrosMateriales = $objInfoDetalleSolCaractOtrosMateriales->getValor();
                                }
                                
                                    $emTipoSolicitudFac = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD FACTIBILIDAD",
                                                                                        "estado"               => "Activo"));
                                    if($emTipoSolicitudFac)
                                    {
                                        // Busca el tipo de solicitud factibilidad con el id_servicio 
                                        $emDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy(array("servicioId"      => $intIdServicio,
                                                                                    "tipoSolicitudId" => $emTipoSolicitudFac->getId()));
                                        if($emDetalleSolicitud)
                                        {
                                            // Busca en el detalle solicitud caractiristica el tipo de solicitud factibilidad 
                                            //con el id_servicio, la caracteristica de metraje
                                            $entityMetraje = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->getSolicitudCaractPorTipoCaracteristica($emDetalleSolicitud->getId(),
                                                                                                            'METRAJE FACTIBILIDAD');

                                            $objInfoDetalleSolCaractOCivilF = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array('caracteristicaId' => $objCaracteristicaOCivil,
                                                                                'detalleSolicitudId' => $emDetalleSolicitud->getId()));
                                            $objInfoDetalleSolCaractOtrosMaterialesF = $emComercial
                                                            ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array('caracteristicaId' => $objCaracteristicaOtrosMateriales,
                                                                            'detalleSolicitudId' => $emDetalleSolicitud->getId()));

                                            // Si existe la entidad obtiene el valor.                                      
                                            if($entityMetraje)
                                            {
                                                $strMetrajeC = $entityMetraje[0]->getValor();
                                            }
                                            if($objInfoDetalleSolCaractOCivilF)
                                            {
                                                $floatValorCaractOCivilF          = $objInfoDetalleSolCaractOCivilF->getValor();
                                            }
                                            if($objInfoDetalleSolCaractOtrosMaterialesF)
                                            {
                                                $floatValorCaractOtrosMaterialesF = $objInfoDetalleSolCaractOtrosMaterialesF->getValor();
                                            }
                                        }
                                    }

                                    // Buscar el parámetro del precio de fibra
                                    $arrayParametrosFibra =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne("Precio de fibra", 
                                                                                        "SOPORTE", 
                                                                                        "", 
                                                                                        "Precio de fibra", 
                                                                                        "", 
                                                                                        "", 
                                                                                        "",
                                                                                        "",
                                                                                        "",
                                                                                        10
                                                                                    );
                                    if(is_array($arrayParametrosFibra) && !empty($arrayParametrosFibra) && $strMetrajeC)
                                    {
                                        $intPrecioFibra = $arrayParametrosFibra['valor1'];
                                        $arrayParametrosMaximoFibra =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('Metraje que cubre el precio de instalación',
                                                                                    "COMERCIAL",
                                                                                    "",
                                                                                    'Metraje que cubre el precio de instalación',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    10);
                                        if(isset($arrayParametrosMaximoFibra["valor1"]) && !empty($arrayParametrosMaximoFibra["valor1"]))
                                        {
                                            $intMetrosDeDistancia = $arrayParametrosMaximoFibra["valor1"];
                                        }
                                    }                                    
                                    
                                    if($floatValorCaractFibraMetros)
                                    {
                                        // Se pregunta si el metraje pasa lo permitido
                                        if($floatValorCaractFibraMetros > $intMetrosDeDistancia)
                                        {
                                            $floatTotalExcedente = $floatValorCaractFibraMetros - $intMetrosDeDistancia;
                                            $floatTotalPagar    = $intPrecioFibra * $floatTotalExcedente;

                                            if(($floatValorCaractOCivil!=0) || ($floatValorCaractOtrosMateriales!=0)) 
                                            {
                                                $floatTotalPagar = $floatTotalPagar + 
                                                $floatValorCaractOCivil + $floatValorCaractOtrosMateriales ;
                                            }
                                        }
                                        else
                                        {
                                            if(($floatValorCaractOCivil!=0) || ($floatValorCaractOtrosMateriales!=0)) 
                                            {
                                                $floatTotalPagar = $floatValorCaractOCivil + $floatValorCaractOtrosMateriales ;
                                            }
                                        }
                                    }
                                    // si no tiene valores la sol_planif, coje los valores de la solicitud_factibilidad
                                    else
                                    {
                                        // Se pregunta si el metraje pasa lo permitido
                                        if($strMetrajeC > $intMetrosDeDistancia)
                                        {
                                            $floatTotalExcedente = $strMetrajeC - $intMetrosDeDistancia;
                                            $floatTotalPagar    = $intPrecioFibra * $floatTotalExcedente;

                                            if(($floatValorCaractOCivilF!=0) || ($floatValorCaractOtrosMaterialesF!=0)) 
                                            {
                                                $floatTotalPagar = $floatTotalPagar 
                                                            + $floatValorCaractOCivilF + $floatValorCaractOtrosMaterialesF ;
                                            }
                                        }
                                        else
                                        {
                                            if(($floatValorCaractOCivilF!=0) || ($floatValorCaractOtrosMaterialesF!=0)) 
                                            {
                                                $floatTotalPagar = $floatValorCaractOCivilF + $floatValorCaractOtrosMaterialesF ;
                                            }
                                        }
                                    }

                                    if($floatTotalPagar==0)
                                    {
                                        throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                    }

                                    $objParametroCabCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                                "modulo"=>'COMERCIAL',
                                                                                "estado"=>'Activo'));
                                    if(is_object($objParametroCabCodigo))
                                    {                        
                                        $objParamDetCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->findOneBy(array("descripcion" => 'CODIGO DE MATERIAL DE FIBRA OPTICA',
                                                                                "parametroId" => $objParametroCabCodigo->getId(),
                                                                                "estado"      => 'Activo'));
                                
                                        //Variable del código del material para insertar a la infodetalleSolMaterial.
                                        $strCodigoMaterial  = $objParamDetCodigo->getValor1();
                                    }
                                    else
                                    {
                                        throw new \Exception(': NO SE ENCONTRÓ:<br> <b>INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN</b>');
                                    }
                                    
                                    //formatear a solo dos decimales
                                    $floatTotalPagar = number_format($floatTotalPagar, 2,'.','');    

                                    $intCantidadEstimada    = 1;
                                    $strCostoMaterial       = 0;
                                    $intCantidadCliente     = 1;
                                    $intCantidadUsada       = 0;
                                    $intCantidadFacturada   = 1;
                                    $strPrecioVentaMaterial = $floatTotalPagar;
                                    $strValorCobrado        = $floatTotalPagar;
                                    /* Se debe generar facturación automática por el valor del excedente 
                                    que cancelará el cliente, se registra los valores en InfoDetalleSolMaterial*/                                   
                                    //ENVÍO VALORES EN INFO DETALLE SOLICITUD MATERIAL 
                                    $arrayParametrosSolMat = array(
                                                            "emComercial"                => $emComercial,
                                                            "strClienteIp"               => $strIpCreacion,
                                                            "intIdDetalleSolicitud"      => $intIdDetalleSolicitud,
                                                            "strUsrCreacion"             => $strUser,
                                                            "strCodigoMaterial"          => $strCodigoMaterial,
                                                            "strCostoMaterial"           => $strCostoMaterial,
                                                            "strPrecioVentaMaterial"     => $strPrecioVentaMaterial,
                                                            "intCantidadEstimada"        => $intCantidadEstimada,
                                                            "intCantidadCliente"         => $intCantidadCliente,
                                                            "intCantidadUsada"           => $intCantidadUsada,
                                                            "intCantidadFacturada"       => $intCantidadFacturada,
                                                            "strValorCobrado"            => $strValorCobrado);                    
                                    $arrayVerificar = $serviceAutorizaciones->registroSolicitudMaterial($arrayParametrosSolMat);
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudMaterial
                                                            <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }
                                    $strEstadoServicio = $objServicio->getEstado();
                                    if($strEstadoServicio !== 'Anulado') 
                                    {
                                        $strEstadoEnviado = "PrePlanificada";
                                        $arrayParametrosPrePla = array(
                                                            "emComercial"                => $emComercial,
                                                            "strEstadoEnviado"           => $strEstadoEnviado,
                                                            "objServicio"                => $objServicio,
                                                            "strClienteIp"               => $strIpCreacion,
                                                            "strUsrCreacion"             => $strUser);
                                        $arrayVerificar = $serviceAutorizaciones
                                                    ->registroEstadoPrePlanificadaInfoDetalleSolicitud($arrayParametrosPrePla);
                                        if($arrayVerificar['status'] == 'ERROR' )
                                        {
                                        throw new \Exception(': EN: registroEstadoPrePlanificadaInfoDetalleSolicitud 
                                                <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                        }
                                    }
                                    //GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial,
                                    $strSeguimiento   = 'El cliente autorizó el excedente de materiales ';
                                    $arrayParametrosTraServ = array(
                                                "emComercial"                => $emComercial,
                                                "strClienteIp"               => $strIpCreacion,
                                                "objServicio"                => $objServicio,
                                                "strSeguimiento"             => $strSeguimiento,
                                                "strUsrCreacion"             => $strUser,
                                                "strAccion"                  => '',
                                                "strEstadoEnviado"           => $objServicio->getEstado() );
                                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO:registroTrazabilidadDelServicio
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }
                            }

                            $arrayRespuesta['mensaje'] = $arrayRespuesta['mensaje'].'<br/> en donde el cliente autoriza el cobro';
                        }
                }
                else 
                {
                    throw new \Exception('En la carga de archivos NFS');
                }
            }
            else 
            {
                throw new \Exception('No se autoriza, no se puede realizar la subida de archivo por NFS');
            }

            $strResultado   = '{"success": ' . $arrayRespuesta['success'] . ', "respuesta":"' . $arrayRespuesta['mensaje'] . '"}';
            $objResponse->setContent($strResultado);
            return $objResponse;
        }
        catch(\Exception $objE)
        {
            $strMensajeError  = 'Ha ocurrido un error, por favor reporte a Sistemas';

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();

            if (strpos(strtolower($objE->getMessage()), strtolower("Archivo con extensión")) >= 0)
            {
                $strMensajeError .=  $objE->getMessage();
            }
            $serviceUtil->insertError('Telcos+',
                        'ClienteService.CargaDeArchivosMultiplesAction',
                        'Error ClienteService.CargaDeArchivosMultiplesAction:'.$objE->getMessage(),
                                            $strUser,
                                            '127.0.0.1');
            error_log($objE->getMessage());
            $strResultado   = $strMensajeError;
            $objResponse->setContent($strResultado);
            return $objResponse;
        }
    }

    /**
     * Método que retorna si se debe solicitar la información del cliente dependiendo si tiene activo el ROL.
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec> 
     * @version 1.0 30-01-2022
     * 
     * @return JsonResponse $objRespuesta
     */
    public function solicitarInfoClienteAction()  
    {
        $arrayResponse          = array(); 
        
        //MODULO 477 - ClausulaContrato
        if(true === $this->get('security.context')->isGranted('ROLE_477-1'))
        {
            $arrayResponse =  array('solicitarInfoCliente' => 'S');
        }
        else
        {
            $arrayResponse =  array('solicitarInfoCliente' => 'N');
        }
        
        
        $arrayResponse = new Response(json_encode( $arrayResponse ));
        $arrayResponse->headers->set('Content-type', 'text/json');
        return $arrayResponse;
    }

    public function ajaxTipoDocumentosGeneralAction() 
    {
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        try
        {
            $arrayTipoDocumento = array();
            $objTiposDocumentos = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                            ->findBy(array("estado"     => "Activo",
                                                           "mostrarApp" => "S"));
            foreach($objTiposDocumentos as $objTiposDocumentos)
            {
                $arrayTipoDocumento[] = array('id' => $objTiposDocumentos->getId(), 'nombre' => $objTiposDocumentos->getDescripcionTipoDocumento());
            }
            $arrayRespuesta['registros'] = $arrayTipoDocumento;
            $objResponse = new Response(json_encode($arrayRespuesta));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
            }
        catch(\Exception $e)
        {
            error_log("error " . json_encode($e));
            $arrayRespuesta["mensaje"] = "No se pudo obtener el listado de documentos";
            return json_encode($arrayRespuesta);
        }
    } 
    /**
     * Función que devuelve lo valores de origen, punto de atencion, tipo de negocio, vendedor, 
     * canal , punto de venta y tipo de ubicacion del punto de origen
     *
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 1.0 16-02-2023
     */
    
    public function ajaxDatosPuntoOrigenAction()
    {
        $objRequest     = $this->get('request');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        $intIdPunto      = $objRequest->request->get('intPuntoId');
        $strTipo            = $objRequest->request->get('strTipo');
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $emComercial             = $this->getDoctrine()->getManager("telconet");
        $objPunto=null;
        $objVendedor=null;
        try
        {
            $objPunto=$emComercial->getRepository("schemaBundle:InfoPunto")
            ->find($intIdPunto);
            $objVendedor=$objPunto!=null?$emComercial->getRepository("schemaBundle:InfoPersona")
            ->findOneBy(['login'=>$objPunto->getUsrVendedor()]):null;
            $objProcesoCaracteristicaPuntoVenta = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
            ->findOneBy(
                array(
                    "descripcionCaracteristica" => 'PUNTO_DE_VENTA_CANAL',
                    "estado"                    => 'Activo'
                )
            );
            $objProcesoCaracteristicaPuntoAtencion = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
            ->findOneBy(
                array(
                    "descripcionCaracteristica" => 'PUNTO_ATENCION',
                    "estado"                    => 'Activo'
                )
            );
            $objProcesoCaracteristicaOrigen = $emGeneral->getRepository("schemaBundle:AdmiCaracteristica")
            ->findOneBy(
                array(
                    "descripcionCaracteristica" => 'ORIGEN_REQUERIMIENTO',
                    "estado"                    => 'Activo'
                )
            );
            $objInfoPuntoCaracteristica=$emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
            ->findOneBy(
                [
                    'puntoId'=>$intIdPunto,
                    'caracteristicaId'=>$objProcesoCaracteristicaPuntoVenta->getId()
                ]
            );
            $strOrigen="";
            $strPuntoAtencion="";
            if($strTipo=="editar")
            {
                $objInfoPuntoCaracteristicaOrigen=$emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
                ->findOneBy(
                    [
                        'puntoId'=>$intIdPunto,
                        'caracteristicaId'=>$objProcesoCaracteristicaOrigen->getId(),
                        'estado'=>'Activo'
                    ]
                );
                $strOrigen=$objInfoPuntoCaracteristicaOrigen!=null?
                 $objInfoPuntoCaracteristicaOrigen->getValor():"";
                $objInfoPuntoCaracteristicaPuntoAtencion=$emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
                ->findOneBy(
                    [
                        'puntoId'=>$intIdPunto,
                        'caracteristicaId'=>$objProcesoCaracteristicaPuntoAtencion->getId(),
                        'estado'=>'Activo'
                    ]
                );
                $strPuntoAtencion=$objInfoPuntoCaracteristicaPuntoAtencion!=null?
                 $objInfoPuntoCaracteristicaPuntoAtencion->getValor():"";

            }
            $strCanales     = 'CANALES_PUNTO_VENTA';
            $strModulo      = 'COMERCIAL';
            $strVal1        = $objInfoPuntoCaracteristica->getValor();
            $arrayDatosCanal   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->get($strCanales, $strModulo, '', '', $strVal1 , '', '', '', '', '18');

            if(count($arrayDatosCanal)>0)
            {
                $arrayRespuesta=array
                (
                    'strCanal'=>$arrayDatosCanal[0]['valor3'],
                    'strPuntoVenta'=>$arrayDatosCanal[0]['valor1'],
                    'intTipoNegocio'=>$objPunto->getTipoNegocioId()->getId(),
                    'objVendedor'=>$objVendedor->getLogin(),
                    'strOrigen'=>$strOrigen,
                    'strPuntoAtencion'=>$strPuntoAtencion,
                    'tipoUbicacionId'=>$objPunto->getTipoUbicacionId()->getId()
                );
            }
         
            $objResponse = new Response(json_encode($arrayRespuesta));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
            }
        catch(\Exception $e)
        {
            error_log("error " . json_encode($e));
            $arrayRespuesta["mensaje"] = "No se pudo obtener los campos del punto de origen";
            return json_encode($arrayRespuesta);
        }
    }

      /**
     * Función que devuelve la vista de crear punto en caso de ser una edicion por el nuevo flujo de proceso traslado
     *
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 1.0 16-02-2023
     * @param type $arrayParametros[
     *                             intPuntoId :punto actual creado
     *                             intEmpresaId : id de la empresa
     *                             strPrefijoEmpresa : prefijo MD o TN
     *                             strTipo : tipo de flujo 
     *                             strEditarCampos : validacion parametrizada si se permite editar cierto campos o no
     *                             intOficinaId :id de departamento
     *                             strMensajeError : string de error
     *                             objSession : session de usuario logueado
     *   ]
     * 
     */
    public function EditarPuntoNuevoProceso($arrayParametros)
    {
        $intIdPunto         = $arrayParametros['intPuntoId'];
        $intEmpresaId       = $arrayParametros['intEmpresaId'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strTipo            = $arrayParametros['strTipo'];
        $strEditarCampos    = $arrayParametros['strEditarCampos'];
        $intOficinaId       = $arrayParametros['intOficinaId'];
        $strMensajeError       = $arrayParametros['mensajeError'];
        $objSession            = $arrayParametros['session'];
        $strEsDistribuidor  ="NO";
        $emComercial        = $this->getDoctrine()->getManager('telconet');       
        $emSeguridad       = $this->getDoctrine()->getManager("telconet_seguridad"); 
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $objInfoPuntoRepo   = $emComercial->getRepository('schemaBundle:InfoPunto');
        $objPunto           =$emComercial->getRepository("schemaBundle:InfoPunto")
                                ->find($intIdPunto);      
        $entityItemMenu     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                ->searchItemMenuByModulo("9", "1");
        $objCliente            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                ->find($objPunto->getPersonaEmpresaRolId()->getPersonaId());
        
        if($strMensajeError!=null)
        {
            $objSession->getFlashBag()->add('error', $strMensajeError->getMessage());
        }
        // Se obtiene el tipo de ubicación "Abierto" para seleccionar por defecto.
        
        $arrayDatosForm['tipoNegocioId']   = $objPunto->getTipoNegocioId()->getId();
        $arrayDatosForm['tipoUbicacionId'] = $objPunto->getTipoUbicacionId()->getId();
        
        $objForm = $this->createForm(new InfoPuntoType(array('validaFile'        => true, 
                                                          'validaFileDigital' => true, 
                                                          'empresaId'         => $intEmpresaId, 
                                                          'datos'             => $arrayDatosForm)), $objPunto);
        $arrayParametrosEnvio['EMPRESA'] = $intEmpresaId;
        $arrayParametrosEnvio['LOGIN']   = $objPunto->getUsrVendedor();

        $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametrosEnvio);

        $strLoginEmpleado  = '';
        $strNombreEmpleado = '';
        
        if($arrayResultado['TOTAL'] > 0)
        {
            $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
            $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
        }
        
        $strPfObligatorio = 'N';
        
        if($strPrefijoEmpresa == 'MD')
        {
            $strPfObligatorio = 'S';
        }
        $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                 "strEmpresaCod"    => $intEmpresaId);
        $strAplicaTipoOrigenTecnologia   = $this->get('schema.Util')->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
       
        $intIdEmpresaRol=$objPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId();
        $objRolPersona= $emGeneral->getRepository("schemaBundle:AdmiRol")
                        ->find($intIdEmpresaRol); 
        $strTipoRol=$objRolPersona->getTipoRolId()->getDescripcionTipoRol();

$objValor=$objForm->createView();
      

        

    return $this->render('comercialBundle:infopunto:new.html.twig', array('item'                => $entityItemMenu,
    'entity'              => $objPunto,
    'form'                => $objForm->createView(),
    'login'               => $objPunto->getLogin(),  
    'cliente'             => $objCliente,
    'idPer'               => $objPunto->getPersonaEmpresaRolId()->getPersonaId(),
    'oficina'             => $intOficinaId,
    'prefijoEmpresa'      => $strPrefijoEmpresa,
    'rol'                 => $strTipoRol,
    'pf_obligatorio'      => $strPfObligatorio,
    'loginEmpleado'       => $strLoginEmpleado,
    'nombreEmpleado'      => $strNombreEmpleado,
    'numeroMaxCorreos'    => 0,
    'numeroMaxTelefonos'  => 0,
    'strAplicaTipoOrigen' => $strAplicaTipoOrigenTecnologia,
    'strEsDistribuidor'   => $strEsDistribuidor,
    'strStatusBloqueo'    => "",
    'strMsjBloqueo'       => "",
    'strTipo'             => $strTipo,
    'strEditarCampos'     =>$strEditarCampos,
    'intPuntoId'          =>$intIdPunto,
    'latitudFloat'     => $objPunto->getLatitud(),
    'longitudFloat'    => $objPunto->getLongitud(),
    'ptoCoberturaId'   => $objPunto->getPuntoCoberturaId()->getId(),
    'cantonId'         => $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getId(),
    'parroquiaId'      => $objPunto->getSectorId()->getParroquiaId()->getId(),
    'sectorId'         => $objPunto->getSectorId()->getId(),
    'esError'=>'S'
    ));
}    
    
}
