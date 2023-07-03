<?php

namespace telconet\comercialBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaRepresentante;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Service\UtilService;
use Symfony\Component\HttpFoundation\File\File;
use telconet\comercialBundle\WebService\ComercialMobileWSResponse\PersonaResponseNew;
use telconet\comercialBundle\WebService\ComercialMobile\PersonaComplexTypeNew;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerPersonaResponseNew;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerDatosClienteResponse;
use telconet\comercialBundle\Service\InfoContratoDigitalService;
use telconet\comercialBundle\Service\PreClienteService;
use telconet\schemaBundle\Entity\InfoCotizacionCab;
use telconet\schemaBundle\Entity\InfoCotizacionDet;
use telconet\schemaBundle\Entity\InfoServicioCaracteristica;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\comercialBundle\Service\ComercialCrmService;
use telconet\comercialBundle\Service\InfoCotizacionService;
use telconet\comercialBundle\Service\ComercialMobileService;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPreClienteResponseNew;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPuntoResponse;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\comercialBundle\Service\ComercialExamenCovidService;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\comercialBundle\Service\RegularizaContratosAdendumsService;
use telconet\soporteBundle\Service\SoporteService;

use \PHPExcel_IOFactory;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento del
 * Mobil Comercial.
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 07-08-2018
 * 
 * Bug.-Se envía token en los metodos getCatalogosEmpresa y getDashboard
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.1 19-12-2018
 * 
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.2 12-04-2019 - Telco CRM requiere recuperar la información del servicio y punto de
 *                           un cliente TN en base a la identificación.
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.3 27-05-2021 - Se agregan constantes para obtener características
 */
class ComercialMobileWSControllerRest extends BaseWSController
{
    const CARACTERISTICA_TIPO_LOGUEO      = 'TIPO DE LOGUEO';
    const CARACTERISTICA_PLANIFICA_ONLINE = 'PLANIFICA ONLINE';
    /**
     * Función que sirve para procesar las opciones que vienen desde el mobil comercial
     * 
     * @param $objRequest
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 27-05-2019 Se añade nuevo proceso 'crearPreClienteOrigenCRM' para crear un Pre-Cliente en TelcoS+
     *                         con origen de TelcoCRM.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 23-07-2019 Se añade nuevo proceso 'validarIdentificacion'
     *                         para validar las identificaciones ingresadas desde TelcoCRM.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 25-07-2019 Se añade dos procesos 'getBasesyMetasVendedor' para retornar las bases o metas de los vendedores
     *                         que requiere el TelcoCRM y 'getContactos' para retornar los contactos por medio de los parámetros
     *                         que se recibe desde TelcoCRM.
     * 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 25-07-2019 Se agrega llamada a funciones para creación de prospecto, punto, servicio, generar login y factibilidad .
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.5 03-05-2020 Se agrega Proceso getClientes que creara el cliente en el telcos.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.6 14-05-2020 Se agrega Proceso getEmpleado devuelve los datos del empleado y sus formas de contacto.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.7 10-06-2020 Se agrega Proceso getUpdateBackup para realizar el cambio de capacidad en los enlaces Backup de Bg..
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.8 11-06-2020 Se añade nuevo proceso 'getPersonaCRM' para retornar la información del cliente o pre-cliente.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.9 11-07-2020 Se añade nuevo metodo para la regularizacion de contratos
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.0 17-07-2020 Se añade nuevo proceso 'editEstadoServicioProyectoTelcos' para editar
     *                         el estado de los servicios asociados a un proyecto en TelcoCRM.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.1 18-06-2020 - Nuevos procesos para representante legal de persona jurídica
     *
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.2 04-09-2019 - Se agrega el método putReingresoOrdenServicio, para realizar el reingreso de la
     *                           orden de servicio automática.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.3 11-12-2020 - Se añade nuevo proceso 'putCrearTareaTelcoCRM'  para crear tareas masivas desde TelcoCRM 
     *                           y 'putNotificacionCotizacionTelcoCRM' para enviar correos al momento de crear cotización.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.4 15-03-2021 - Se añade nuevo proceso 'getCatalogoExtranet' para obtener el catalogo para crear
     *                           puntos en la extranet.
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 2.5 15-03-2021 - Se añade nuevo proceso 'putTrasladarServicios' para realizar el proceso de traslado,
     *                           factibilidad y creación de orden de trabajo de manera automatica.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 2.5 11-11-2020 - Se agrega nuevo proceso para validar los códigos promocionales para la empresa MD y se agrega
     *                           el switch strOp2 motivo que el switch strOp2 sobrepasa el límite de case por regla de sonar. 
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.7 31-08-2020 Nuevo proceso 'getPuntosClientePorPagina' para obtener información de puntos de un cliente de forma paginada.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.8 20-11-2020 Nuevo proceso 'validarCodigosPromocionales' que los codigos promocionales de los servicios del contrato o adendum.
     *
     *  @author Leonela Burgos <mlburgos@telconet.ec>
     *  @version 2.9 21-11-2022 - Se añade nuevo proceso 'crearTareaCasoSoporte' para poder crear una Tarea Caso soporte.
     *
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayData               = json_decode($objRequest->getContent(),true);
        $strToken                = "";
        $objResponse             = new Response();
        $strOp                   = $arrayData['op'];
        $strOp2                  = $arrayData['op'];
        $strExisteOp2            = "N";
        $serviceSoporte          = $this->get('soporte.SoporteService');
        $serviceComercialCrm     = $this->get('comercial.ComercialCRM');
        $serviceComercialMobile  = $this->get('comercial.ComercialMobile');
        $serviceContratoDigital  = $this->get('comercial.ContratoDigital');
        $serviceComercialCovid   = $this->get('comercial.ComercialExamenCovid');
        if($arrayData['source'] && $strOp <> "insertLog")
        {

           $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);
            if(!$strToken)
            {
                return new Response(json_encode(array(
                        'status' => 403,
                        'message' => "token invalido"
                        )
                    )
                );
            }
        } 
    

        if($strOp2)
        {
            switch($strOp2)
            {
                case 'putValidaCodigoPromocional':
                    $arrayResponse = $this->putValidaCodigoPromocional($arrayData['data']);
                    $strExisteOp2  = "S";
                    break;
                case 'getPuntosClientePorPagina':
                    $arrayResponse = $serviceComercialMobile->getPuntosClientePorPagina($arrayData['data']);
                    $strExisteOp2  = "S";
                    break;
                default:
                    break;
            }
        }
        
        if($strOp && $strExisteOp2 === "N")
        {
            switch($strOp)
            {
                case 'insertLog':
                    $arrayResponse = $this->insertLog($arrayData['data']);
                    break;
                case 'getCatalogos':
                    $arrayResponse = $serviceComercialMobile->getCatalogos();
                    break;
                case 'getParameters':
                    $arrayResponse = $this->getParameters($arrayData['data']);
                    break;
                case 'getCatalogosEmpresa':
                    $arrayResponse = $serviceComercialMobile->getCatalogosEmpresa($arrayData['data']);
                    break;
                case 'getPersona':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayData['data']['user']       = $arrayData['user'];
                    $arrayData['data']['clientIp']   = $objRequest->getClientIp();
                    $arrayResponse                   = $this->getPersona($arrayData['data']);
                    break;
                case 'crearTareaCasoSoporte':
                    $arrayResponse = $serviceSoporte->crearTareaCasoSoporte($arrayData['data']);
                    break;
                case 'crearPreClienteOrigenCRM':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $this->crearPreClienteOrigenCRM($arrayData['data']);
                    break;
                case 'validarIdentificacion':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $this->validarIdentificacion($arrayData['data']);
                    break;
                case 'getBasesyMetasVendedor':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCrm->getBasesyMetasVendedor($arrayData['data']);
                    break;
                case 'getPersonaCRM':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCrm->getPersonaCRM($arrayData['data']);
                    break;
                case 'getContactos':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCrm->getContactos($arrayData['data']);
                    break;
                case 'editEstadoServicioProyectoTelcos':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCrm->editEstadoServicioProyectoTelcos($arrayData['data']);
                    break;
                case 'getHolding':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCrm->getHolding($arrayData['data']);
                    break;
                case 'putCrearTareaTelcoCRM':
                    $arrayResponse                   = $serviceComercialCrm->putCrearTareaTelcoCRM($arrayData['data']);
                    break;
                case 'putNotificacionCotizacionTelcoCRM':
                    $arrayResponse                   = $serviceComercialCrm->putNotificacionCotizacionTelcoCRM($arrayData['data']);
                    break;
                case 'getCaracteristicasProducto':
                    $arrayResponse                   = $serviceComercialCrm->getCaracteristicasProducto($arrayData['data']);
                    break;
                case 'getDatosPuntoServicio':
                    $arrayResponse                   = $serviceComercialCrm->getDatosPuntoServicio($arrayData['data']);
                    break;
                case 'putCotizacion':
                    $arrayResponse = $this->putCotizacion($arrayData);
                    break;
                case 'getCatalogoExtranet':
                    $arrayResponse = $this->getCatalogoExtranet($arrayData);
                    break;
                case 'getListaCotizacion':
                    $arrayResponse = $this->getListaCotizacion($arrayData);
                    break;
                case 'getPuntosCliente':
                    $arrayResponse = $this->getPuntosCliente($arrayData['data']);
                    break;
                case 'getLog':
                    $arrayResponse = $this->getLog($arrayData['data']);
                    break;
                case 'getDashboard':
                    $arrayResponse = $this->getDashboard($arrayData['data']);
                    break;
                case 'putPlanificacion':
                    $arrayData['data']['user'] = $arrayData['user'];

                    $arrayResponse = $this->putPlanificacion($arrayData['data']);
                    break;
                case 'getListaPlanificacion':
                    $arrayResponse = $this->getListaPlanificacion($arrayData['data']);
                    break;
                case 'putContrato':
                    $arrayData['data']['ip']            = $arrayData['ip'];
                    $arrayData['data']['strAplicacion'] = $arrayData['source']['name'];
                    $arrayResponse                      = $this->putContrato($arrayData['data']);
                    break;
                case 'putAutorizarContrato':
                    $arrayData["data"]["user"]          = $arrayData['user'];
                    $arrayData['data']['ip']            = $arrayData['ip'];
                    $arrayData['data']['strAplicacion'] = $arrayData['source']['name'];
                    $arrayResponse                      = $this->putAutorizarContrato($arrayData['data']);
                    break;
                case 'getPinSecurity':
                    $arrayData["data"]["user"] = $arrayData['user'];
                    $arrayResponse             = $this->getPinSecurity($arrayData['data']);
                    break;
                case 'getEstadoCuentaCliente':
                    $arrayData["data"]["user"] = $arrayData['user'];
                    $arrayResponse             = $serviceComercialMobile->getEstadoCuentaCliente($arrayData['data']);
                    break;
                case 'getLoginPunto':
                    $arrayResponse = $serviceComercialMobile->getLoginPunto($arrayData['data']);
                    break;
                case 'putPreCliente':
                    $arrayData['data']['origen'] = $arrayData['source']['name'];
                    $arrayResponse               = $this->putPreCliente($arrayData['data']);
                    break;
                case 'putPunto':
                    $arrayData['data']['strAplicacion'] = $arrayData['source']['name'];
                    $arrayResponse                      = $this->putPunto($arrayData['data']);
                    break;
                case 'putServicio':
                    $arrayResponse = $this->putServicio($arrayData['data']);
                    break;
                case 'solicitarFactibilidadServicio':
                    $arrayResponse = $serviceComercialMobile->solicitarFactibilidadServicio($arrayData['data']);
                    break;
                case 'getValidacionesServicios':
                    $arrayResponse = $serviceContratoDigital->getValidacionesServicios($arrayData['data']);
                    break;
                case 'getClientes':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCovid->getClientes($arrayData['data']);
                    break;
                case 'getEmpleado':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $serviceComercialCovid->getEmpleado($arrayData['data']);
                    break;
                case 'getUpdateBackup':
                    $arrayData['data']['aplication'] = $arrayData['source']['name'];
                    $arrayResponse                   = $this->getUpdateBackup();
                    break;
                case 'putRegularizaContratosAdendums':
                    $arrayResponse = $this->putRegularizaContratosAdendums($arrayData['data']);
                    break;
                case 'updateRepresentanteLegalPersonaJuridica':
                    $arrayData['data']['application'] = $arrayData['source']['name'];
                    $arrayResponse                    = $serviceComercialMobile->updateRepresentanteLegalPersonaJuridica($arrayData['data']);
                    break;
                case 'getRepresentanteLegalPersonaJuridica':
                    $arrayData['data']['application'] = $arrayData['source']['name'];
                    $arrayData['data']['usrCreacion'] = $arrayData['user'];
                    $arrayResponse                    = $serviceComercialMobile->getRepresentanteLegalPersonaJuridica($arrayData['data']);
                    break;    
                case 'putReingresoOrdenServicio':
                    $arrayResponse = $this->putReingresoOrdenServicio($arrayData['data']);
                    break;
                case 'putTrasladarServicios':
                    $arrayResponse = $this->putTrasladarServicios($arrayData['data']);
                    break;
                case 'getProductosAdicionales':
                    $arrayResponse = $this->getProductosAdicionales($arrayData);
                    break;
                case 'getAccesoPorRol':
                    $arrayResponse = $serviceComercialMobile->getAccesoPorRol($arrayData['data']);    
                    break;
                 
                default:
                    $arrayResponse['status']  = $this->status['METODO'];
                    $arrayResponse['message'] = $this->mensaje['METODO'];
            }
        }

        $arrayResponseFinal = null;

        if(isset($arrayResponse))
        {
            if(in_array($strOp, array('getCatalogosEmpresa', 'getDashboard')))
            {
                $arrayResponse = str_replace('"token": false', '"token": "' . $strToken . '"', $arrayResponse);
                $objResponse   = new Response();
                $objResponse->headers->set('Content-Type', 'text/json');
                $objResponse->setContent($arrayResponse);
            }
            else
            {
                $arrayResponseFinal          = $arrayResponse;
                $arrayResponseFinal['token'] = $strToken;
                $objResponse                 = new Response();
                $objResponse->headers->set('Content-Type', 'text/json');
                $objResponse->setContent(json_encode($arrayResponseFinal));
            }
        }
        return $objResponse;
    }
    
    /**
     * Devuelve una referencia a un archivo existente en el servidor segun la ruta dada
     * @param string $strPath
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function getFile($strPath)
    {
        $objFile = new File($strPath);
        return $objFile;
    }

    /**
     * Escribe un archivo con la data base64 y devuelve una referencia al mismo
     * @param string $strData (encodado en base64)
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function writeAndGetFile($strData)
    {
        if (empty($strData))
        {
            return null;
        }
        return $this->getFile($this->writeTempFile($strData));
    }    

    /**
     * 
     * Método encargado de realizar el proceso de reingreso de orden de servicio.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-09-2019
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    public function putReingresoOrdenServicio($arrayData)
    {
        $strUsuario             = $arrayData['strUsuario'] ? $arrayData['strUsuario'] : 'telcos_reingresos';
        $strIp                  = $arrayData['strIp'] ? $arrayData['strIp'] : '127.0.0.1';
        /* @var $objInfoServicioService \telconet\comercialBundle\Service\InfoServicioService */
        $objInfoServicioService = $this->get('comercial.InfoServicio');
        /* @var $objServiceUtil \telconet\schemaBundle\Service\UtilService */
        $objServiceUtil         = $this->get('schema.Util');

        try
        {
            $arrayRespuesta = $objInfoServicioService->reingresoOrdenServicio($arrayData);            
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error en el proceso de reingreso - putReingresoOrdenServicio';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'ComercialMobileWSControllerRest->putReingresoOrdenServicio',
                                          $objException->getMessage(),
                                          $strUsuario,
                                          $strIp);

             $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /*-
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 02-08-2018  -Permite insertar un registro en el log
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 11-02-2020  Corrección de sintaxis en catch
     *
     **/
    private function insertLog($arrayData)
    {
        $arrayResultado = array();
        $serviceUtil = $this->get('schema.Util');
        try
        {
            if ($serviceUtil->validarParametrosLog($arrayData))
            {
                $objReturnResponse = $serviceUtil->insertLog($arrayData);
                $arrayResultado['status']    = $objReturnResponse->getStrStatus();
                $arrayResultado['message']   = $objReturnResponse->getStrMessageStatus();
                $arrayResultado['success']   = true;

            }
            else
            {
                $arrayResultado['status']    = $this->status['DATOS_NO_VALIDOS'];
                $arrayResultado['message']   = $this->mensaje['DATOS_NO_VALIDOS'];
                $arrayResultado['success']   = true;
            }
        } 
        catch (\Exception $ex)
        {
            $arrayResultado['status']    = $this->status['ERROR'];
            $arrayResultado['message']   = $ex->getMessage();
            $arrayResultado['success']   = true;
        }
        return $arrayResultado;
    }

    /**
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 07-08-2018  -Envia los parametros que necesita el apk para su funcionamiento
     * 
     * Bug: Se corrige el valor del timeout que apuntaba a un valor erroneo
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.1 18-12-2018
     * 
     * Se modifica el valor del timeout para que sea traido desde un parametro
     * @author Edgar Pin villavicencio<epin@telconet.ec>
     * @version 1.2 18-01-2019
     * 
     * Se modifica para agregar nuevos parametros para contrato digital
     * @author Edgar Pin Villavicencio  <epin@telconet.ec>
     * @version 1.3 19-02-2019
     *
     * @author Christian Jaramillo Espinoza  <cjaramilloe@telconet.ec>
     * @version 1.4 08-10-2020 Se agrega parámetros para activar la paginación de puntos y puntos por página
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.5 21-10-2021 Se adiciona el perfil para visualizar la planificaciones.
     * @version 1.6 21-02-2022 Se adiciona el perfil para solicitar información al cliente.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 05-01-2023 Se adiciona codificación para mostrar el nodo contactosFiltro.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.8 25-11-2022 Se adiciona parametro para activar envío de mensajes por whatsapp
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.9 03-01-2023 Se adiciona parametro para activar el envio de mensajes por whatsapp para regularizacion de clientes
     * 
     * @author Miguel Guzman <mguzman@telconet.ec>
     * @version 1.10 02-03-2023 Se adiciona parametro para jurisdicciones con planificacion y consideracion para empresa
     *   
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 2.0 20-01-2023 Se adiciona parametro para visualizar pantalla la busqueda del cliente 
     *    
     **/
    private function getParameters($arrayData)
    {

        try
        {
       
        $strFormaContactoSitio  = $this->container->getParameter('planificacion.mobile.codFormaContactoSitio');
        $strFiltroGrupoProducto = $this->container->getParameter('comercial.cotizacion.grupoproducto');
        $intNumeroDias          = $this->container->getParameter('comercial.cotizacion.filtro_maximo_dias');
        $strTimeOut             = $this->container->getParameter('ws_time_out_comercial');
        $strDeptoTn             = $this->container->getParameter('comercial_depto_log_tn');
        $strDeptoMd             = $this->container->getParameter('comercial_depto_log_md');
        $strPermiteAdendum      = $this->container->getParameter('contrato_digital_permiteAdendum');
        $strPermiteContratoPJ   = $this->container->getParameter('contrato_digital_permiteContratoPersonaJuridica');
        $strPermiteCambioFP     = $this->container->getParameter('contrato_digital_permiteCambioFormaPagoAdendum');
        $intIdPersona           = $arrayData['idPersona'];
        $intEmpresaCod          = $arrayData['empresaCod'];

        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');

        //Obtengo el porcentaje del iva actual
        $emGeneral             = $this->get('doctrine.orm.telconet_general_entity_manager');
        $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->findOneBy(array('tipoImpuesto' => 'IVA',
                                                                                                         'estado'       => 'Activo'));

        $arrayParamsPaginacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAMETROS_TM_COMERCIAL', 'COMERCIAL', '', 
                                    'ACTIVA_PAGINACION_PUNTOS', '', '', '', '', '', $intEmpresaCod);

        $arrayParamsPtosPagina = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAMETROS_TM_COMERCIAL', 'COMERCIAL', '',
                                     'PUNTOS_POR_PAGINA', '', '', '', '', '', $intEmpresaCod);

        $arrayDeptoTn = explode("|",$strDeptoTn);
        $arrayDeptoMd = explode("|",$strDeptoMd);

        $arrayDeptoKv = array();
        foreach ($arrayDeptoTn as $strDpto)
        {
           $arrayDeptoKv[] = array("k" => $strDpto,
                                   "v" => ""); 
        }
        $arrayDeptoTn = $arrayDeptoKv;
        $arrayDeptoKv = array();
        foreach ($arrayDeptoMd as $strDpto)
        {
           $arrayDeptoKv[] = array("k" => $strDpto,
                                   "v" => ""); 
        }
        $arrayDeptoMd = $arrayDeptoKv;
        $arrayResultado  = array(); 
        $arrayParameters = array();

        $arrayDeptoLog   = array(array("k"     => "10",
                                       "v"     => "Telconet",
                                       "items" => $arrayDeptoTn),
                                 array("k"     => "18",
                                       "v"     => "MegaDatos",
                                       "items" => $arrayDeptoMd));

        //Inicia busqueda de parametros de jurisdicciones
        $arrayJurisdiccionGuayaquil = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('JURISDICCIONES', 'COMERCIAL', '', 'GUAYAQUIL', '', '', '', '', '', $intEmpresaCod);

        $arrayJurisdiccionQuito = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('JURISDICCIONES', 'COMERCIAL', '', 'QUITO', '', '', '', '', '', $intEmpresaCod);

        $arrayJurisdiccionGuayaquil = [
            "k" => $arrayJurisdiccionGuayaquil['valor1'],
            "v" => $arrayJurisdiccionGuayaquil['valor2']
        ];

        $arrayJurisdiccionQuito = [
            "k" => $arrayJurisdiccionQuito['valor1'],
            "v" => $arrayJurisdiccionQuito['valor2']
        ];

        $arrayJurisdicion = array($arrayJurisdiccionGuayaquil, $arrayJurisdiccionQuito);
       
        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                          ->getPerfilPlanificacion($intIdPersona,'visualizarPlanTodos');

        $arrayParameters["planificaRolTodos"] = 0;
        if($strTienePerfil === 'S')
        {
            $arrayParameters["planificaRolTodos"] = 1;
        }


        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                              ->getPerfilPlanificacion($intIdPersona,'Respuestas de Políticas y Cláusulas');

        $arrayParameters["pantallaPersonaEncuesta"] = 0;
        if($strTienePerfil === 'S')
        {
          $arrayParameters["pantallaPersonaEncuesta"] = 1;
        }

        $strVerProspecto     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                          ->getAccesoPorModuloAccion($intIdPersona, "precliente", "formularioprospecto");
        $arrayParameters["pantallaProspecto"] = $strVerProspecto == "S" ? 1 : 0;                                          

        $strVerregularizacion = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                            ->getAccesoPorModuloAccion($intIdPersona, "cliente", "regularizacioncliente");
        $arrayParameters["pantallaRegularizacion"] = $strVerregularizacion == "S" ? 1 : 0;                                          


        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                          ->getPerfilPlanificacion($intIdPersona, 'visualizarPlanUsuario');
        $arrayParameters["planificaOnlineUsuario"] = 0;
        if($strTienePerfil === 'S')
        {
            $arrayParameters["planificaOnlineUsuario"] = 1;
        }

        $arrayParamDiaMaxPlan = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('CANT_DIA_MAX_PLANIFICACION',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $intEmpresaCod);
        if(!empty($arrayParamDiaMaxPlan))
        {
            $arrayParameters["cantDiaMax"] = $arrayParamDiaMaxPlan['valor1'];
        }

        $arrayParametersContactoFiltro =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                                                                '',
                                                                '',
                                                                'CONTACTOS FILTRO',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $intEmpresaCod);
        if(!empty($arrayParametersContactoFiltro))
        {
            $arrayParameters["contactosFiltro"] = $arrayParametersContactoFiltro['valor1'];
        }
        else
        {
            $arrayParameters["contactosFiltro"] ='';
        }

     
        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                    ->getPerfilPlanificacion($intIdPersona,'solicitarInformaciónCliente');


        $arrayParameters["requiereInformacionCliente"] = $strTienePerfil;



        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                              ->getPerfilPlanificacion($intIdPersona,'verClienteApp');

        $arrayParameters["perfilverClienteApp"] = $strTienePerfil;



        $arrayParametrosDet     = $this->getManager()
                ->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                            '',
                            '',
                            'MOSTRAR CLAUSULA',
                            '',
                            'CLAUSULA_CONTRATO',
                            '',
                            '',
                            '',
                            $intEmpresaCod);

        if(!empty($arrayParametrosDet))
        {
        $arrayParameters["verClausula"] = $arrayParametrosDet['valor1'];
        }
        else
        {
        $arrayParameters["verClausula"] = 'N';
        }

        
        $arrayParametrosDet     = $this->getManager()
                ->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                            '',
                            '',
                            'MOSTRAR DATOS BANCARIO',
                            '',
                            'LINK_BANCARIO',
                            '',
                            '',
                            '',
                            $intEmpresaCod);

        if(!empty($arrayParametrosDet))
        {
        $arrayParameters["verDatoBancario"] = $arrayParametrosDet['valor1'];
        }
        else
        {
        $arrayParameters["verDatoBancario"] = 'N';
        }
        
        $arrayParametersWhatsApp =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                  '',
                  '',
                  'NOTIFICACION_WHATSAPP',
                  '',
                  '',
                  '',
                  '',
                  '',
                  $intEmpresaCod);
        if(!empty($arrayParametersWhatsApp))
        {
        $arrayParameters["whastappAceptacion"] = $arrayParametersWhatsApp['valor1'];
        }
        else
        {
        $arrayParameters["whastappAceptacion"] ='';
        }


        $arrayParamsWhatsapp = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAMETROS_FORMULARIO_ACEPTACION_PROSPECTO', 'COMERCIAL', '', 
                                             'ENVIO_WHATSAPP', '', '', '', '', 'flujo de prospectos', $intEmpresaCod);
        $arrayParamsWhatsappReg = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAMETROS_FORMULARIO_REGULARIZACION_CLIENTE', 'COMERCIAL', '', 
                                                      'ENVIO_WHATSAPP_REGULARIZACION_CLIENTE', '', '', '', '', 
                                                      'regularización de clientes', $intEmpresaCod);
                                              
        $arrayParameters["timeOutSession"]                 = $strTimeOut;
        $arrayParameters["contactSiteCodes"]               = $strFormaContactoSitio;
        $arrayParameters["systemPorcentIva"]               = $entityAdmiImpuestoIva->getPorcentajeImpuesto();
        $arrayParameters["productGroupIdle"]               = $strFiltroGrupoProducto;
        $arrayParameters["cotizationFilterMaxDayRange"]    = $intNumeroDias;
        $arrayParameters["permisoLog"]                     = $arrayDeptoLog;
        $arrayParameters["permiteContratoPersonaJuridica"] = $strPermiteContratoPJ;
        $arrayParameters["permiteAdendum"]                 = $strPermiteAdendum;
        $arrayParameters["permiteCambioFormaPagoAdendum"]  = $strPermiteCambioFP;
        $arrayParameters["jurisdiccionConPlanificacion"]   = $arrayJurisdicion;
        $arrayParameters["tipo_logueo"]                    = $intTipoLogueo;
        $arrayParameters["envioWhatsAppProspecto"]         = $arrayParamsWhatsapp["valor1"] == "SI" ? 1 : 0;
        $arrayParameters["envioWhatsAppRegularizacion"]    = $arrayParamsWhatsappReg["valor1"] == "SI" ? 1 : 0;
        
        if(!is_null($arrayParamsPaginacion) && !is_null($arrayParamsPaginacion['valor1']) && !empty($arrayParamsPaginacion['valor1']))
        {
            $arrayParameters['activaPaginacionPuntos'] = $arrayParamsPaginacion['valor1'];
        }
        else
        {
            $arrayParameters['activaPaginacionPuntos'] =  'N';
        }

        if(!is_null($arrayParamsPtosPagina) && !is_null($arrayParamsPtosPagina['valor1']) && !empty($arrayParamsPtosPagina['valor1']))
        {
            $arrayParameters['puntosPorPagina'] = $arrayParamsPtosPagina['valor1'];
        }
        else
        {
            $arrayParameters['puntosPorPagina'] =  -1;
        }
        error_log("result " . json_encode($arrayResultado, 1));
        $arrayResultado['response'] = $arrayParameters;
        $arrayResultado['status']   = $this->status['OK'];
        $arrayResultado['message']  = $this->mensaje['OK'];
        $arrayResultado['success']  = true;
    }catch(\Exception $ex)
    {
        $arrayRespuesta['status']  = $this->status['ERROR'];
        $arrayRespuesta['message'] = $ex->getMessage();
        $arrayRespuesta['success'] = true;
       
        return $arrayRespuesta;
    }
        return $arrayResultado;
    }

    /**
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 10-09-2018 - Guarda la cotización como un documento adjunto al contrato y lo envia por mail al cliente
     * 
     */
    private function putCotizacion($arrayData)
    {
        $serviceInfoCotizacion = $this->get('comercial.InfoCotizacion');
        $arrayResponse = $serviceInfoCotizacion->putCotizacion($arrayData);
        return $arrayResponse;
    }

    /**
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 10-09-2018 - Lista las cotizaciones según el filtro establecido
     * 
     */
    private function getListaCotizacion($arrayData)
    {
        $arrayParametros = array();
        $arrayParametros['strCodEmpresa']            = $arrayData['data']['filterEnterpriseCode'];
        $arrayParametros['intNumeroCotizacion']      = $arrayData['data']['filterCotizationNum'];
        $arrayParametros['strIdentificacionCliente'] = $arrayData['data']['filterCustomerIdentification'];
        $arrayParametros['strFechaDesde']            = isset($arrayData['data']['filterCotizationStartDate']) ? 
                                                       $arrayData['data']['filterCotizationStartDate'] . " 00:00:00" : " 00:00:00" ;
        $arrayParametros['strFechaHasta']            = isset($arrayData['data']['filterCotizationEndDate']) ?
                                                       $arrayData['data']['filterCotizationEndDate'] . " 23:59:59" : " 23:59:59" ;
        $arrayParametros['strLoginVendedor']         = $arrayData['data']['filterLoginVendor'];

        $serviceInfoCotizacion = $this->get('comercial.InfoCotizacion');
        $arrayResponse = $serviceInfoCotizacion->getListaCotizacion($arrayData);

        return $arrayResponse;
    }

    /**
     * Documentación para la función crearPreClienteOrigenCRM.
     *
     * Función que realiza la creación de un Pre-cliente con origen de TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa, ingresada en la aplicación TelcoCRM.
     *                                  "strCodEmpresa"         => Código de la empresa, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoIdentificacion" => Tipo de identificación del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strRuc"                => Ruc de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoEmpresa"        => Tipo de empresa del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strDireccionCuenta"    => Dirección de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strTipoCliente"        => Tipo tributario (Natural/Juridico), ingresada en la aplicación TelcoCRM.
     *                                  "strPagaIva"            => 'S' si paga Iva el cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strOficinaFacturacion" => Oficina de facturación, ingresada en la aplicación TelcoCRM.
     *                                  "strNacionalidad"       => Nacionalidad del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombreCuenta"       => Nombre de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "strRepresentanteLegal" => Representante legal del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strGenero"             => Género del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombreCliente"      => Nombre del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strApellidoCliente"    => Apellido del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "arrayFormaContactos"   => Formas de contactos del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strNombrePais"         => Nombre del pais.
     *                                  "intIdPais"             => Id del pais.
     *                                  "strContribuyente"      => Indica si es contribuyente especial.
     *                                  "strConadis"            => Número conadis del cliente.
     *                                  "strEstadoCivil"        => Estado civil del cliente.
     *                                  "strPrepago"            => Valida si es prepago.
     *                                  "intTitulo"             => Título del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strFechaNacimiento"    => Fecha de nacimiento del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strUsuarioCreacion"    => Usuario en sessión en la aplicación TelcoCRM.
     *                                  "aplication"            => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"  => Estado de la respuesta
     *                                  "mensaje" => Mensaje de la respuesta
     *                                  "success" => Indica el éxito de la transacción
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 27-05-2019
     *
     */
    private function crearPreClienteOrigenCRM($arrayParametros)
    {
        $servicePreCliente = $this->get('comercial.PreCliente');
        $strRespuesta      = $servicePreCliente->crearPreClienteOrigenCRM($arrayParametros);
        $serviceUtil       = $this->get('schema.Util');
        $arrayRespuesta    = array();
        try
        {
            $arrayRespuesta['status']  = $this->status['OK'];
            $arrayRespuesta['mensaje'] = $strRespuesta;
            $arrayRespuesta['success'] = true;
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']  = $this->status['ERROR'];
            $arrayRespuesta['mensaje'] = $this->mensaje['ERROR'];
            $arrayRespuesta['success'] = true;

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "crearPreClienteOrigenCRM";
            $arrayParametrosLog['appAction']        = "crearPreClienteOrigenCRM";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayParametros);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuarioCreacion'];
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }
    /**
     * Documentación para la función validarIdentificacion.
     *
     * Función que realiza validaciones de la identificación ingresadas en TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strTipoIdentificacion" => Tipo de identificación del cliente, ingresada en la aplicación TelcoCRM.
     *                                  "strRuc"                => Ruc de la cuenta, ingresada en la aplicación TelcoCRM.
     *                                  "intIdPais"             => Id del pais.
     *                                  "aplication"            => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"  => Estado de la respuesta
     *                                  "mensaje" => Mensaje de la respuesta
     *                                  "success" => Indica el éxito de la transacción
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 23-07-2019
     *
     */
    private function validarIdentificacion($arrayParametros)
    {
        $servicePreCliente = $this->get('comercial.PreCliente');
        $strRespuesta      = $servicePreCliente->validarIdentificacion($arrayParametros);
        $serviceUtil       = $this->get('schema.Util');
        $arrayRespuesta    = array();
        try
        {
            $arrayRespuesta['status']  = $this->status['OK'];
            $arrayRespuesta['mensaje'] = $strRespuesta;
            $arrayRespuesta['success'] = true;
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']  = $this->status['ERROR'];
            $arrayRespuesta['mensaje'] = $this->mensaje['ERROR'];
            $arrayRespuesta['success'] = false;

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuarioCreacion'];
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }
    /**
     * Metodo que obtiene los puntos de un cliente mediante un filto
     * 
     * @param array $arrayData
     * @return array
     * @author epin <epin@telconet.ec>
     * @version 1.0
     */
    private function getPuntosCliente($arrayData)
    {
        $serviceComercialMobile    = $this->get('comercial.ComercialMobile');
        $objPersona                = $serviceComercialMobile->obtenerDatosPersonaPrv($arrayData);
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto          = $this->get('comercial.InfoPunto');
        $serviceUtil               = $this->get('schema.Util');
        $arrayPuntos               = array();
        try
        {
            if ($objPersona)
            {
                $strRol = "Cliente";
                $arrayParametros = array("strCodEmpresa"          => $arrayData['strCodEmpresa'],
                                         "intIdCliente"           => $objPersona->id,
                                         "strRol"                 => $strRol,
                                         "strCriteriaFilterPoint" => $arrayData['criteriaFilterPoint'],
                                         "strTextFilterPoint"     => $arrayData['textFilterPoint'],
                                         "esPadre"                => null);
                $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosClienteFilter($arrayParametros);
                if(!$arrayPuntos)
                {
                    $strRol = "Pre-cliente";
                    $arrayParametros = array("strCodEmpresa"          => $arrayData['strCodEmpresa'],
                                             "intIdCliente"           => $objPersona->id,
                                             "strRol"                 => $strRol,
                                             "strCriteriaFilterPoint" => $arrayData['criteriaFilterPoint'],
                                             "strTextFilterPoint"     => $arrayData['textFilterPoint'],
                                             "esPadre"                => null);
                    $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosClienteFilter($arrayParametros);
                }
                if (!$arrayPuntos)
                {
                    $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                    $arrayRespuesta['message']   = "No se han encontrado resultados para el criterio de busqueda";
                    $arrayRespuesta['success']   = true;
                    return $arrayRespuesta;
                    
                }
                //se agrega recuperacion de contrato en caso de que exista
                //obtiene rol activos o pendientes de la persona con rol Pre-cliente
                //puse el else porque no me dejaba pasar el sonnar
                $arrayEstadoContrato = array();
                $arrayEstadoRol     = array();
                if ($strRol == "Cliente")
                {
                    $arrayEstadoContrato[] = 'Activo';
                    $arrayEstadoRol[]      = 'Activo';
                }
                else
                {
                    $arrayEstadoContrato[] = 'Pendiente';
                    $arrayEstadoContrato[] = 'PorAutorizar';
                    $arrayEstadoRol[]      = 'Activo';
                    $arrayEstadoRol[]      = 'Pendiente';
                }
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['message']   = "Cliente no Existe!";
                $arrayRespuesta['success']   = true;
                return $arrayRespuesta;
            }
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['status']  = $this->status['ERROR'];
            $arrayRespuesta['message'] = $this->mensaje['ERROR'];
            $arrayRespuesta['success'] = true;
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = "127.0.0.1";
            $serviceUtil->insertLog($arrayParametrosLog);
            return $arrayRespuesta;
        }
        $arrayRespuesta = array();
        $arrayRespuesta["response"]["puntos"] = $arrayPuntos;
        $arrayRespuesta['status']    = $this->status['OK'];
        $arrayRespuesta['message']   = $this->mensaje['OK'];
        $arrayRespuesta['success']   = true;
        return $arrayRespuesta;
    }

    /**
     * Metodo que obtiene el log de errores
     * 
     * @param array $arrayData
     * @return array
     * @author epin <epin@telconet.ec>
     * @version 1.0
     * 
     */    
    private function getLog($arrayData)
    {
        try
        {
            $emGeneral = $this->get('doctrine.orm.telconet_general_entity_manager');
            $arrayLog  = $emGeneral->getRepository('schemaBundle:InfoLog')
                                  ->getLog($arrayData);
            $arrayRespuesta = array();
            $arrayRespuesta["response"] = $arrayLog;
            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['message']  = $this->mensaje['OK'];
            $arrayRespuesta['success']  = true;    
        }
        catch (\Exception $e)
        {
            $arrayRespuesta["response"] = array();
            $arrayRespuesta['status']   = $this->status['500'];
            $arrayRespuesta['message']  = $this->mensaje['No se pudo obtener el log'];
            $arrayRespuesta['success']  = true;    

        }
        return $arrayRespuesta;
    }
    /**
     * Metodo que Devuelve el dashboard de TM-COMERCIAL
     * 
     * @param array $arrayData
     * @return array
     * @author epin <epin@telconet.ec>
     * @version 1.0
     * 
     * Se valida si el objeto viene nulo, devolver un json con mensaje que no hay datos para mostrar
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 11-02-2020  Corrección de sintaxis en catch
     *
     * 
     */    
    private function getDashboard($arrayData)
    {        
        try
        {
            $strCodEmpresa = $arrayData['enterpriseCode'];
            $objCatalogo = $this->getDoctrine()
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:AdmiCatalogos')
                    ->findOneBy(array("codEmpresa" => $strCodEmpresa,
                                      "tipo"       => "DASHBOARD" . $arrayData['loginVendor']));
            $strJsonRespuesta = '{"response": "", "status": "200","message": "No hay datos para mostrar", "success": true, "token": false}';
            if ($objCatalogo <> null )
            {
                $strJsonRespuesta = $objCatalogo->getJsonCatalogo();
            }
        }
        catch (\Exception $ex)
        {
            $strJsonRespuesta = '{"status": "500","message": "' . $ex->getMessage() . '", "success": true, "token": false}';
        }
        return $strJsonRespuesta;
        
    }

    /**
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 10-09-2018 - Lista de Planificaciones Pendientes
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 11-02-2020  Corrección de sintaxis en catch
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 17-07-2020  Retorno de la razón social si se tratase de una persona jurídica TM Comercial
     *
     */
    private function getListaPlanificacion($arrayData)
    {
        $arrayParametros = array();
        $arrayParametros['strCodEmpresa'] = $arrayData['enterpriseCode'];
        $arrayParametros['strUsuario']    = $arrayData['loginVendor'];
        if (!isset($arrayData['enterpriseCode']) || !isset($arrayData['loginVendor']))
        {
            $arrayRespuesta['status']  = "500";
            $arrayRespuesta['message'] = "No hay parametros para la consulta";
            $arrayRespuesta['success'] = true;  
            return $arrayRespuesta;            
        }
        $serviceUtil = $this->get('schema.Util');

        try
        {
            $emComercial = $this->getDoctrine()->getManager('telconet');
            $arrayPlanificaciones = $emComercial->getRepository('schemaBundle:InfoServicio')->getServicioPendientesInstalacion($arrayParametros);
            

            $arrayResponse['PlanificationList'] = array();
            
            foreach ($arrayPlanificaciones as $arrayPlanificacion)
            {
                $arrayCab = array();
                $arrayCab['customerFirstName']      = ($arrayPlanificacion['tipoTributario'] == 'NAT')
                                                          ? $arrayPlanificacion['nombres']
                                                          : $arrayPlanificacion['razonSocial'];
                $arrayCab['customerLastName']       = ($arrayPlanificacion['tipoTributario'] == 'NAT')
                                                          ? $arrayPlanificacion['apellidos']
                                                          : '';
                $arrayCab['customerIdentification'] = $arrayPlanificacion['identificacionCliente'];
                
                $arrayList = array();
                $arrayList['customerLoginPoint']           = $arrayPlanificacion['login'];
                $arrayList['customerService']              = $arrayPlanificacion['servicio'];
                $arrayList['instalationDate']              = $arrayPlanificacion['fechaInicio'];
                $arrayList['instalationTime']              = $arrayPlanificacion['horaInicio'] . " - " . $arrayPlanificacion['horaFin'];
                $arrayList['instalationState']             = $arrayPlanificacion['estado'];
                $arrayList['instalationOrigin']            = $arrayPlanificacion['origen'];
                $arrayList['instalationContactSiteName']   = "xxxxxxxxxxxxxxx";
                $arrayList['instalationContactSiteNumber'] = "xxxxxxxxxxxxxxx";
                if ($arrayPlanificacion['origen'] == "MOVIL" && $arrayPlanificacion['estado'] == "Planificada")
                {
                    $strNombre = strstr($arrayPlanificacion['observacion'], ':');
                    $intPosicion = strpos($strNombre, "Teléfono");
                    $strNombre = trim(substr($strNombre, 2, ($intPosicion-2)));
                    $intPosicion = strpos($arrayPlanificacion['observacion'], "<br>");
                    $strNumero   = substr(substr($arrayPlanificacion['observacion'], 0, $intPosicion), -10);
                    $arrayList['instalationContactSiteName']   = $strNombre;
                    $arrayList['instalationContactSiteNumber'] = $strNumero;
                }

                
                $arrayCab['itemList'][] = $arrayList;
                $arrayResponse['PlanificationList'][] = $arrayCab;
            }
            
            $arrayRespuesta['response'] = $arrayResponse;
            $arrayRespuesta['status']  = "200";
            $arrayRespuesta['message'] = "OK";
            $arrayRespuesta['success'] = true;
        }
        catch (\Exception $ex) 
        {
            $arrayRespuesta['status']  = "500";
            $arrayRespuesta['message'] = "ERROR";
            $arrayRespuesta['success'] = true;

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['data']['filterLoginVendor'];

            $serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método que permite la planificación de instalación de un servicio desde la aplicación comercial
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 09-01-2018
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 14-11-2019 Se corrige funcionalidad que no restaba cupo al devolver el listado de planificaciones
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 11-02-2020  Envío del código y prefijo de la empresa para planificación y
     *                          corrección de sintaxis en catch
     *
     */    
    private function putPlanificacion($arrayData) 
    {
        $serviceUtil = $this->get('schema.Util');
        try
        {
            $objSolicitudPrePlanificacion = $this->getDoctrine() 
                    ->getManager("telconet")
                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                    ->findOneBy(array("servicioId" => $arrayData['servicioId'],
                "tipoSolicitudId" => "8"));
            $arrayFechaProgramacion = explode("T", $arrayData['fechaDesde']);
            $arrayFecha             = explode("T", $arrayData['fechaDesde']);
            $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
            $arrayHoraF             = explode(":", $arrayFecha[1]);

            $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));

            $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));
            $arrayFechaI = $arrayFechaI->format("Y-m-d");


            $arrayFecha2       = explode("T", $arrayData['fechaHasta']);
            $arrayF2           = explode("-", $arrayFechaProgramacion[0]);
            $arrayHoraF2       = explode(":", $arrayFecha2[1]);
            $arrayFechaInicio2 = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
            $arrayFechaSms     = date("d/m/Y", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));

            $strHoraInicioServicio = $arrayHoraF;
            $strHoraFinServicio    = $arrayHoraF2;
            $strHoraInicio         = $arrayHoraF;
            $strHoraFin            = $arrayHoraF2;
            $strNombreContacto     = $arrayData['nombreContactoSitio'];
            $strNumeroTelefono     = $arrayData['numeroContactoSitio'];

            $arrayParametros['strOrigen']               = "MOVIL";
            $arrayParametros['strCodEmpresa']           = '18';
            $arrayParametros['strPrefijoEmpresa']       = 'MD';
            $arrayParametros['intIdFactibilidad']       = $objSolicitudPrePlanificacion->getId();
            $arrayParametros['dateF']                   = $arrayF;
            $arrayParametros['dateFecha']               = $arrayFecha;
            $arrayParametros['strFechaInicio']          = $arrayFechaInicio;
            $arrayParametros['strFechaFin']             = $arrayFechaInicio2;
            $arrayParametros['strHoraInicioServicio']   = $strHoraInicioServicio;
            $arrayParametros['strHoraFinServicio']      = $strHoraFinServicio;
            $arrayParametros['dateFechaProgramacion']   = $arrayFechaI;
            $arrayParametros['strHoraInicio']           = $strHoraInicio;
            $arrayParametros['strHoraFin']              = $strHoraFin; 
            $arrayParametros['strObservacionServicio']  = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
            $arrayParametros['strIpCreacion']           = "127.0.0.1";
            $arrayParametros['strUsrCreacion']          = $arrayData['user'];
            $arrayParametros['strObservacionSolicitud'] = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
            $arrayParametros['strNumeroTelefonico']     = $arrayData['numeroTelefonico'];
            $arrayParametros['arrayFechaSms']           = $arrayFechaSms;
            $arrayParametros['strEmail']                = $arrayData['eMail'];

            //valido si hay cupo disponible
            $emComercial = $this->getDoctrine()->getManager('telconet');
            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                         ->findOneById($arrayParametros['intIdFactibilidad']);
            $strFechaPar = substr($arrayFechaInicio, 0, -1);
            $strFechaPar .= "1";
            $strFechaPar = str_replace("-", "/", $strFechaPar);

            $strFechaPar2 = str_replace("-", "/", $arrayFechaInicio);

            $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                            ->getPuntoId()
                            ->getPuntoCoberturaId()->getId();

            $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');

            $arrayPar    = array(
                "strFecha"        => $strFechaPar,
                "intJurisdiccion" =>  $intJurisdicionId,
                "intHoraCierre"   => $intHoraCierre);

            $arrayCount  = $emComercial
                ->getRepository('schemaBundle:InfoCupoPlanificacion')
                ->getCountOcupados($arrayPar);
            $intCuantos = $arrayCount['CUANTOS'];

            $arrayParAgenda = array("strFechaDesde"     => $strFechaPar2,
                                    "intJurisdiccionId" => $intJurisdicionId);

            $entityAgendaDetalle  = $emComercial
                 ->getRepository('schemaBundle:InfoAgendaCupoDet')
                 ->getDetalleAgenda($arrayParAgenda);

            $intCupoMobile = 0;
            foreach ($entityAgendaDetalle['registros'] as $entityDet)
            {
              $intCupoMobile += $entityDet->getCuposMovil();
            }
            $objServicePlanificacion = $this->get('planificacion.Planificar');
            
            if ($intCuantos >= $intCupoMobile)
            {
                $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdicionId));
                $arrayResultado = array();
                $arrayResultado['response']['fechaAgenda']     = $arrayPlanificacion;
    
                $arrayResultado['response']['codigoRespuesta'] = 1; 

                $arrayResultado['status']  = "200";
                $arrayResultado['message'] = "No hay cupo disponible para este horario, seleccione otro horario por favor!";
                $arrayResultado['success'] = true;
                
                $arrayParametrosLog['enterpriseCode']   = "18";
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $arrayResultado['message'];
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = $arrayData['user'];

                $serviceUtil->insertLog($arrayParametrosLog);            
                
               //codigoRespuesta 
                return $arrayResultado;
            }

            $arrayRespuesta = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);
            $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdicionId));
            $arrayResultado = array();
            $arrayResultado['response']['fechaAgenda']     = $arrayPlanificacion;

            $arrayResultado['response']['codigoRespuesta'] = $arrayRespuesta['codigoRespuesta'];
            $arrayResultado['status']    = "500";
            if ($arrayRespuesta['codigoRespuesta'] == 2)
            {
                $arrayResultado['status']    = "200";
            }
            $arrayResultado['message']   = $arrayRespuesta['mensaje'];
            $arrayResultado['success']   = true;

            return $arrayResultado;
            
        } 
        catch (\Exception $ex) 
        {
            $arrayResultado['response']['codigoRespuesta'] = 0;
            $arrayResultado['status']    = "500";
            $arrayResultado['message']   = "Error en guardar Planificación";
            $arrayResultado['success']   = true;
            
            $arrayParametrosLog['enterpriseCode']   = "18";
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['data']['user'];

            $serviceUtil->insertLog($arrayParametrosLog);            
            
        }
    }
    
    /**
     * Método que crea el contrato para un cliente
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 21-01-2019
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 02-09-2019 Se agrega validación para identificar el tipo de excepción generada.
     * 
     * Se modifica la lógica para realizar generación y envío de pin por separado.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.2 03/12/2019
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 25-09-2019 Se agrega funcionalidad para adendum de puntos y servicios
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 19-01-2020 Envio de usuario creador para persistencia en InfoLog,
     *                         Corrección en comparación de resultado al crear certificado digital,
     *                         Reemplazo de función implode por json_encode para evitar errores si
     *                         existiesen instancias date dentro del array a convertir.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 20-02-2020 Se corrige validación cuando SD responde success pero no se genera certificado
     *                         Se consulta en la base si existe certificado, caso contrario se rechaza el contrato
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 27-02-2020 Se corrige cuando es un adendum y no se puede generar el certificado no se reversa el estado
     *                         del adendum
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 06-04-2020 - Se valida si un cliente ya tiene un contrato Activo.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.8 18-06-2020 - Implementación de persona jurídica.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.9 29-12-2020 - Se valida que venga el tipo de contrato si no se devuelve error
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.10 20-05-2021 - Se corrige variable de punto que era servicio y se envia el punto para documentar certificado
     * 
     */
    public function putContrato($arrayData)
    {
        ini_set('max_execution_time', 16000000);
        $strClientIp              = isset($arrayData['ip']) ? $arrayData['ip'] : '127.0.0.1';
        $arrayData['ip']          = $strClientIp;
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato      = $this->get('comercial.InfoContrato');
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil              = $this->get('schema.Util');
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoAprobService */
        $serviceInfoContratoAprob = $this->get('comercial.InfoContratoAprob');
        $serviceComercialMobile   = $this->get('comercial.ComercialMobile');
        $serviceContratoDigital   = $this->get('comercial.InfoContratoDigital');
        $servicePromocion         = $this->get('comercial.Promocion');
        
        $arrayPromocion           = $arrayData['contrato']['lstCodigosPromocionales'];
        $booleanRechazaCd         = false;
        $objContrato              = null;
        $arrayContrato            = null;
        $boolNFS                  = false;
        try
        {
            //valido que el usuario tenga la característica de usuario
            $objCaracteristica =  $this->getManager("telconet")->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy(array( "descripcionCaracteristica" => "USUARIO"));
            $arrayCriterios = array ('caracteristicaId' => $objCaracteristica->getId(),
                                     'valor'            => $arrayData['contrato']['documentoIdentificacion']);

            $entityPersonaEmpresaRolCarac =  $this->getManager("telconet")->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                          ->findOneBy($arrayCriterios);

            $arrayParametrosDet           = $this->getManager()
                                                 ->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAMETROS_ESTRUCTURA_RUTAS_TELCOS',
                                                          '',
                                                          '',
                                                          '',
                                                          isset($arrayData['strAplicacion']) ? $arrayData['strAplicacion'] : 'TELCOS',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '');
            if(!empty($arrayParametrosDet))
            {
                $strAplicacion          = $arrayParametrosDet['valor2'];
                $arrayData['strApp']    = $strAplicacion;
            }

            $arrayParametrosDetNFS        = $this->getManager()
                                                 ->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('BANDERA_NFS',
                                                          '',
                                                          '',
                                                          '',
                                                          'S',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '');
            if(isset($arrayParametrosDetNFS) && $arrayParametrosDetNFS['valor1'] === 'S')
            {
                $boolNFS              = true;
                $arrayData['bandNfs'] = $boolNFS;
            }
            if (!$entityPersonaEmpresaRolCarac)
            {

                $arrayResultado['response'] = array();
                $arrayResultado['status']   = $this->status['ERROR'];            
                $arrayResultado['message']  = "Cliente no posee característica de Usuario";
                return $arrayResultado;
            } 

            $objFechaContrato = new \DateTime('now');
            $arrayData['contrato']['feFinContratoPost'] = $objFechaContrato;
            //valido que la fecha del dispositivo sea igual a la del servidor
            //se agrega generacion de archivos temporales para subida de archivos de contratos
            $arrayTipoDocumentos = array (); 
            $arrayDatosFormFiles = array (); 
            foreach($arrayData['contrato']['files'] as $arrayFile):
                $strTipo                  = $arrayFile['tipoDocumentoGeneralId'];
                $arrayTipoDocumentos[]    = $strTipo;
                $objArchivo               = $this->writeAndGetFile($arrayFile['file']);
                $arrayDatosFormFiles[]    = $objArchivo;    
            endforeach;
            // Se configuran los parametros iniciales con los que iniciara el contrato
            // Dado que es necesario ingresar un pin para autorizar el contrato este no
            // debe iniciar con un estado PENDINTE sino con uno anterior
            $arrayData['contrato']['arrayTipoDocumentos'] = $arrayTipoDocumentos;
            // Estado Inicial Contrato
            $arrayData['contrato']['valorEstado']         = 'PorAutorizar';
            $arrayData['contrato']['datos_form_files']    = array('imagenes',
                                                                   $arrayDatosFormFiles);
            // Al crear el contrato se debe indicar el origen del mismo
            $arrayParametrosContrato                   = array();
            $arrayParametrosContrato['codEmpresa']     = $arrayData['codEmpresa'];
            $arrayParametrosContrato['prefijoEmpresa'] = $arrayData['prefijoEmpresa']; 
            $arrayParametrosContrato['idOficina']      = $arrayData['idOficina']; 
            $arrayParametrosContrato['usrCreacion']    = $arrayData['usrCreacion']; 
            $arrayParametrosContrato['clientIp']       = $strClientIp;
            $arrayParametrosContrato['datos_form']     = $arrayData['contrato']; 
            $arrayParametrosContrato['check']          = null; 
            $arrayParametrosContrato['clausula']       = null;
            $arrayParametrosContrato['origen']         = 'MOVIL';
            $arrayParametrosContrato['servicios']      = $arrayData['contrato']['servicios'];
            $arrayParametrosContrato['bandNfs']        = $boolNFS;
            $arrayParametrosContrato['strApp']         = $strAplicacion;
            $arrayParametrosContrato['arrayPromocion'] = $arrayPromocion;
            if ($arrayData['strTipo'] == "C")
            { 
                $objContratoValido = $this->getManager("telconet")
                                            ->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array(
                                                            "personaEmpresaRolId"  => $arrayData['contrato']['personaEmpresaRolId'],
                                                            "estado"               => "Activo"
                                                        ));
                if (is_object($objContratoValido))
                {
                    $arrayResultado['response'] = array();
                    $arrayResultado['status']   = $this->status['ERROR'];
                    $arrayResultado['message']  = "Cliente ya tiene un contrato Activo";
                    return $arrayResultado;
                }

                if (!($arrayParametrosContrato['datos_form']['tipoContratoId']))
                {
                    $arrayResultado['response'] = array();
                    $arrayResultado['status']   = $this->status['ERROR'];
                    $arrayResultado['message']  = "No se recibió el tipo de contrato";
                    return $arrayResultado;                    
                }
                //valido que el contrato no exista, sino devuelvo mensaje de error
                $objContratoValido = $this->getManager("telconet")
                                            ->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array(
                                                            "personaEmpresaRolId"  => $arrayData['contrato']['personaEmpresaRolId'],
                                                            "estado"               => "PorAutorizar"
                                                        ));
       
                $objServicio = $this->getManager("telconet")->getRepository('schemaBundle:InfoServicio')
                                                        ->findOneBy(array(
                                                                        "id" => $arrayData['contrato']['serviceContractId']
                                                                    ));

                $objPunto = $this->getManager("telconet")->getRepository('schemaBundle:InfoPunto')
                                                         ->find($objServicio->getPuntoId());
                if (is_object($objContratoValido) && is_object($objServicio))
                {
    
                    $arrayResultado['response'] = array();
                    $arrayResultado['status']   = $this->status['ERROR'];            
                    $arrayResultado['message']  = "Contrato se encuentra en proceso de guardar";
                    return $arrayResultado;
                } 

                
                $objContrato = $serviceInfoContrato->crearContrato($arrayParametrosContrato);
                $strMensaje    = "";
                $strMensajeSms = "SMS no enviado";
     
                if(is_object($objContrato))
                {
                    
                    $arrayContrato["strCodEmpresa"] = $arrayData["codEmpresa"];
                    $arrayContrato["contrato"]      = $objContrato;
                    $arrayContrato["strIp"]         = $arrayData["ip"];
                    $arrayContrato["strUsuario"]    = $arrayData["usrCreacion"];
                    $arrayContrato['bandNfs']       = $boolNFS;
                    $arrayContrato['strApp']        = $strAplicacion;
                    $arrayContrato['objPerEmpRol']  = $objContrato->getPersonaEmpresaRolId();
                    $arrayData['objPerEmpRol']      = $objContrato->getPersonaEmpresaRolId();
                    $arrayContrato['prefijoEmpresa']= $arrayData['prefijoEmpresa'];
                    //valido las formas de contacto telefono y mail en la persona y en el punto

                    $arrayFp = $serviceContratoDigital->obtenerFormasDeContactoByPersonaId($objContrato->getPersonaEmpresaRolId()->getPersonaId());
                    if (!$arrayFp)
                    {
                        throw new \Exception('ERROR_PARCIAL -' . "No se encontro forma de contacto para la persona", 1);
                    }
                    if (!$arrayFp['celular'])
                    {
                        $arrayFpTelf = array("Telefono Movil", "Telefono Movil Claro", "Telefono Movil Movistar", "Telefono Movil CNT");
                        foreach ($arrayFpTelf as $strFp)
                        {
                            $arrayContactosTelf     = $this->getManager("telconet")->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                        ->findContactosByPunto($objServicio->getPuntoId()->getLogin(), $strFp);
                            foreach ($arrayContactosTelf as $arrayContactoT) 
                            {
                                $arrayFp['celular'] = $arrayContactoT['valor'];
                                break;
                            }
                            if ($arrayFp['celular'])
                            {
                                break;
                            }
                        } 
                    }
                    if (!$arrayFp['celular'])
                    {
                        throw new \Exception('ERROR_PARCIAL -' . "No se encontro numero de telefono movil para la persona", 1);

                    }
                    if (!$arrayFp['correo'])
                    {
                        $arrayContactosTelf     = $this->getManager("telconet")->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                        ->findContactosByPunto($objPunto->getPuntoId()->getLogin(), 'Correo Electronico');
                        foreach ($arrayContactosTelf as $arrayContactoT) 
                        {
                            $arrayFp['correo'] = $arrayContactoT['valor'];
                            break;
                        }
                    }    
                    if (!$arrayFp['correo'])
                    {
                        throw new \Exception('ERROR_PARCIAL -' . "No se encontro correo electronico para la persona", 1);

                    } 
                    // Se solicita la generacion de un certificado para poder firmar los documentos
                   
                    $arrayCrearCd                 = $serviceContratoDigital->crearCertificadoNew($arrayContrato);
                                  
                    
                    $strIdentificacionCertificado = ($arrayCrearCd['datos']['strTipoTributario'] == 'NAT')
                        ? $arrayData['contrato']['documentoIdentificacion']
                        : $arrayCrearCd['datos']['representanteLegal']['identificacion'];

                    $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                                           ->findOneBy(array("numCedula" => $strIdentificacionCertificado,
                                                             "estado"    => "valido"));

                    //Se consulta si fue creado el certificado
                    if ($arrayCrearCd['salida'] == '1' && count($objCertificado) > 0)
                    {
                        // Si el certificado fue generado correctamente entonces procedemos a documentarlo
                        // Se envian todos los documentos de soporte a la Entidad emisora del certificado
                        // valido si el certificado no se guardo en la data

                        $arrayData['objPunto'] = $objPunto; 
                        $arrayDocumentarCd  = $serviceContratoDigital->documentarCertificadoNew($objContrato,$arrayData);
                        $strMensaje         = $arrayCrearCd['mensaje'];
                        //Consulta si fue documentado el certificado
    
                        if ($arrayDocumentarCd['status'] == '200')
                        {
                            $strMensaje .= " (".$arrayDocumentarCd['mensaje'].")";
                            $arrayResponse    = array( 
                                                        'idContrato'     => $objContrato->getId(), 
                                                        'numeroContrato' => $objContrato->getNumeroContrato(),
                                                        'estadoContrato' => $objContrato->getEstado(),
                                                        'mensajeSms'     => $strMensajeSms
                                                        );
    
                            // Generamos el PIN con el cual se confirmara el contrato
                            /* @var $serviceSeguridad SeguridadService */
                            $serviceSeguridad                    = $this->get('seguridad.Seguridad');
                            $arrayGeneracionPin                  = array ();
                            $arrayGeneracionPin['strUsername']   = $arrayData['usrCreacion'];
                            $arrayGeneracionPin['strCodEmpresa'] = $arrayData['codEmpresa'];
                            $arrayResponseService                = $serviceSeguridad->generarPinSecurity($arrayGeneracionPin);
                            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
                            {
                                $strMensajeError = "ERROR ENVIO SMS: Empresa: "        . $arrayData['codEmpresa'] . 
                                                                   " Telefono: "       . $arrayData['contrato']['numeroTelefonico'] .
                                                                   $arrayResponseService['mensaje'] . 
                                                                   " identificación: " . $arrayData['contrato']['documentoIdentificacion'] .
                                                                   " idContrato => "   . $objContrato->getId();
                                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                                $arrayParametrosLog['logType']          = "0";
                                $arrayParametrosLog['logOrigin']        = "TELCOS";
                                $arrayParametrosLog['application']      = basename(__FILE__);
                                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                                $arrayParametrosLog['messageUser']      = "Error";
                                $arrayParametrosLog['status']           = "Fallido";
                                $arrayParametrosLog['descriptionError'] = $strMensajeError;
                                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                                $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
                                $serviceUtil->insertLog($arrayParametrosLog);  
                            }
                            else if (isset($arrayResponseService['pin']))
                            {
                                $arrayResponse['response']['pin']    = $arrayResponseService['pin'];
                                //Preparo la data para el envío del pin
                                $arrayDataEnvio                      = array();
                                $arrayDataEnvio['strPin']            = $arrayResponseService['pin'];
                                $arrayDataEnvio['strNumeroTlf']      = $arrayData['contrato']['numeroTelefonico'];
                                $arrayDataEnvio['strIdentificacion'] = $arrayData['contrato']['documentoIdentificacion'];
                                $arrayDataEnvio['strPersonaId']      = $arrayData['contrato']['idcliente'];
                                $arrayDataEnvio['strUsername']       = $arrayData['usrCreacion'];
                                $arrayDataEnvio['strCodEmpresa']     = $arrayData['codEmpresa'];
                                //Realizo el envío del pin
                                $arrayResponseEnvioPin   = $serviceSeguridad->enviarPinSecurity($arrayDataEnvio);
                                if (isset($arrayResponseEnvioPin['mensaje']))
                                {
                                    $strMensajeSms = $arrayResponseEnvioPin['mensaje'];
                                }
                            }                            

                            $objAdendums = $this->getManager()->getRepository('schemaBundle:InfoAdendum')
                                                                ->findBy(array("tipo"       => $arrayData['strTipo'],
                                                                               "contratoId" => $objContrato->getId()));    
                            if ($objAdendums)
                            {
                                foreach ($objAdendums as $entityAdendum)
                                {
                                    $entityAdendum->setEstado("PorAutorizar");
                                    $this->getManager()->persist($entityAdendum);
                                    $this->getManager()->flush();    
                                }
                            }
                            else
                            {
                                throw new \Exception('ERROR_PARCIAL ' . "No Existe Adendum para Autorizar", 1);
                            }   
                                                                   
                            $arrayResultadoPer = $serviceComercialMobile
                                                ->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                                   "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                                   "strIdentificacion" => $arrayData['contrato']['documentoIdentificacion'],
                                                                   "user"              => $arrayData['usrCreacion']));
                            
                            $arrayResponse['persona']    = $arrayResultadoPer['response']->persona;
                            $arrayResponse['planes']     = $arrayResultadoPer['response']->planes;
                            $arrayResponse['pin']        = $arrayResponseService['pin'];
                            $arrayResponse['mensajeSms'] = $strMensajeSms;
                            $arrayResultado['message']   = "Contrato creado Exitosamente!";
                            $arrayResultado['status']    = $this->status['OK'];
                        }
                        else
                        {
                                $strMensaje      .= $arrayDocumentarCd['mensaje']?$arrayDocumentarCd['mensaje']:", Error al documentar certificado";
                                $booleanRechazaCd = true;
                        }
                    }
                    else
                    {
                        $strMensaje       = $arrayCrearCd['mensaje'] ? $arrayCrearCd['mensaje'] : "Error al crear certificado";
                        $booleanRechazaCd = true;
                    }
                }
                else
                {
                    $arrayResultado['message'] = "Error cuando se intento crear el contrato";
                }
                //Se consulta si se debe rechazar contrato o no
                if($booleanRechazaCd)
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = 'Contrato en Estado Rechazado';
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
    
                    $serviceUtil->insertLog($arrayParametrosLog); 
                    
                    $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                    $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                    $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                    $arrayParametrosRechazo['objContrato']    = $objContrato;
                    //Se rechaza automticamente el contrato porque el proceso no se completo
                    $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                    $arrayResultado['status']  = $this->status['ERROR'];                  
                    $arrayResultado['message'] = $strMensaje;
                }
            }
            else
            {
                $arrayParametrosContrato                      = array();
                $arrayParametrosContrato['strCodEmpresa']     = $arrayData['codEmpresa'];
                $arrayParametrosContrato['strPrefijoEmpresa'] = $arrayData['prefijoEmpresa']; 
                $arrayParametrosContrato['intIdOficina']      = $arrayData['idOficina']; 
                $arrayParametrosContrato['strUsrCreacion']    = $arrayData['usrCreacion']; 
                $arrayParametrosContrato['strClientIp']       = $strClientIp;
                $arrayParametrosContrato['arrayDatosForm']    = $arrayData['contrato']; 
                $arrayParametrosContrato['strOrigen']         = 'MOVIL';
                $arrayParametrosContrato['arrayServicios']    = $arrayData['contrato']['servicios'];
                $arrayParametrosContrato['bandNfs']           = $boolNFS;
                $arrayParametrosContrato['strApp']            = $strAplicacion;
                $arrayParametrosContrato['intPersEmprRol']    = $arrayData['contrato']['personaEmpresaRolId'];


                $objContrato = $this->getManager("telconet")
                                ->getRepository('schemaBundle:InfoContrato')
                                ->findOneBy(array(
                                                    "personaEmpresaRolId"  => $arrayData['contrato']['personaEmpresaRolId'],
                                                    "estado"               => "Activo"
                                                    ));
                if (!is_object($objContrato))
                {
                    $objContrato = $this->getManager("telconet")
                    ->getRepository('schemaBundle:InfoContrato')
                    ->findOneBy(array(
                                        "personaEmpresaRolId"  => $arrayData['contrato']['personaEmpresaRolId'],
                                        "estado"               => "Pendiente"
                                        ));
                }
                if (!is_object($objContrato))
                {
                    $arrayResultado['response'] = array();
                    $arrayResultado['status']   = $this->status['ERROR'];            
                    $arrayResultado['message']  = "No se puede crear Adendum, necesita guardar un contrato primero";
                    return $arrayResultado;
                }
                $objPunto = $this->getManager("telconet")->getRepository('schemaBundle:InfoServicio')
                                                        ->findOneBy(array(
                                                                        "id" => $arrayData['contrato']['serviceContractId']
                                                                    ));
                $arrayFp = $serviceContratoDigital->obtenerFormasDeContactoByPersonaId($objContrato->getPersonaEmpresaRolId()->getPersonaId());
                if (!$arrayFp)
                {
                    throw new \Exception('ERROR_PARCIAL -' . "No se encontro forma de contacto para la persona", 1);
                }
                if (!$arrayFp['celular'])
                {
                    $arrayFpTelf = array("Telefono Movil", "Telefono Movil Claro", "Telefono Movil Movistar", "Telefono Movil CNT");
                    foreach ($arrayFpTelf as $strFp)
                    {
                        $arrayContactosTelf     = $this->getManager("telconet")->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                    ->findContactosByPunto($objPunto->getPuntoId()->getLogin(), $strFp);
                        foreach ($arrayContactosTelf as $arrayContactoT) 
                        {
                            $arrayFp['celular'] = $arrayContactoT['valor'];
                            break;
                        }
                        if ($arrayFp['celular'])
                        {
                            break;
                        }
                    } 
                }
                if (!$arrayFp['celular'])
                {
                    throw new \Exception('ERROR_PARCIAL -' . "No se encontro numero de telefono movil para la persona", 1);

                }
                if (!$arrayFp['correo'])
                {
                    $arrayContactosTelf     = $this->getManager("telconet")->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                ->findContactosByPunto($objPunto->getPuntoId()->getLogin(), 'Correo Electronico');
                    foreach ($arrayContactosTelf as $arrayContactoT) 
                    {
                        $arrayFp['correo'] = $arrayContactoT['valor'];
                        break;
                    }
                }    
                if (!$arrayFp['correo'])
                {
                    throw new \Exception('ERROR_PARCIAL -' . "No se encontro correo electronico para la persona", 1);

                } 

                $arrayParametrosContrato['intIdContrato']          = $objContrato->getId();
                $arrayParametrosContrato['strTipo']                = $arrayData['strTipo'];
                $arrayParametrosContrato['strCambioNumeroTarjeta'] = $arrayData['cambioNumeroTarjeta'];
                $arrayParametrosContrato['arrayPromocion']         = $arrayPromocion;
                $serviceInfoContrato->crearAdendum($arrayParametrosContrato);
                //obtengo el numero del adendum
                foreach ($arrayParametrosContrato['arrayServicios'] as $intServicioId)
                {
                    $entityAdendum = $this->getManager('telconet')->getRepository('schemaBundle:InfoAdendum')
                                                                  ->findOneBy(array('servicioId' => $intServicioId));
                    if ($entityAdendum)
                    {
                        $arrayData['strNumeroAdendum'] = $entityAdendum->getNumero();
                    }
                }
                $strMensaje    = "";
                $strMensajeSms = "SMS no enviado";

                $arrayContrato["strCodEmpresa"] = $arrayData["codEmpresa"];
                $arrayContrato["contrato"]      = $objContrato;
                $arrayContrato["strIp"]         = $arrayData["ip"];
                $arrayContrato["strUsuario"]    = $arrayData["usrCreacion"];
                $arrayContrato['bandNfs']       = $boolNFS;
                $arrayContrato['strApp']        = $strAplicacion;
                $arrayContrato['objPerEmpRol']  = $objContrato->getPersonaEmpresaRolId();
                $arrayData['objPerEmpRol']      = $objContrato->getPersonaEmpresaRolId();
                $arrayContrato['prefijoEmpresa']= $arrayData['prefijoEmpresa'];
                // Se solicita la generacion de un certificado para poder firmar los documentos
                $serviceContratoDigital = $this->get('comercial.InfoContratoDigital');
                $arrayCrearCd           = $serviceContratoDigital->crearCertificadoNew($arrayContrato);

                $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                                      ->findOneBy(array("numCedula" =>
                                           $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getTipoTributario() == 'NAT'
                                                ? $arrayData['contrato']['documentoIdentificacion']
                                                : $arrayCrearCd['datos']['representanteLegal']['identificacion'],
                                                         "estado"    => "valido"));


                //Se consulta si fue creado el certificado
                if ($arrayCrearCd['salida'] == '1' && count($objCertificado) > 0)
                {
                    // Si el certificado fue generado correctamente entonces procedemos a documentarlo
                    // Se envian todos los documentos de soporte a la Entidad emisora del certificado
                    $arrayDocumentarCd  = $serviceContratoDigital->documentarCertificadoNew($objContrato,$arrayData);
                    $strMensaje         = $arrayCrearCd['mensaje'];
                    //Consulta si fue documentado el certificado

                    if ($arrayDocumentarCd['status'] == '200')
                    {
                        $strMensaje .= " (".$arrayDocumentarCd['mensaje'].")";
                        $arrayResponse    = array( 
                                                    'idContrato'     => $objContrato->getId(), 
                                                    'numeroContrato' => $objContrato->getNumeroContrato(),
                                                    'estadoContrato' => $objContrato->getEstado(),
                                                    'mensajeSms'     => $strMensajeSms
                                                    );

                        // Generamos el PIN con el cual se confirmara el contrato
                        /* @var $serviceSeguridad SeguridadService */
                        $serviceSeguridad = $this->get('seguridad.Seguridad');

                        $arrayResponseService = $serviceSeguridad->generarPinSecurity($arrayData['contrato']['documentoIdentificacion'], 
                                                                                        $arrayData['contrato']['numeroTelefonico'], 
                                                                                        $arrayData['codEmpresa']);
                        if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
                        {
                            $strMensajeError = "ERROR ENVIO SMS: Empresa: "        . $arrayData['codEmpresa'] . 
                                                                " Telefono: "       . $arrayData['contrato']['numeroTelefonico'] .
                                                                $arrayResponseService['mensaje'] . 
                                                                " identificación: " . $arrayData['contrato']['documentoIdentificacion'] .
                                                                " idContrato => "   . $objContrato->getId();
                            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                            $arrayParametrosLog['logType']          = "0";
                            $arrayParametrosLog['logOrigin']        = "TELCOS";
                            $arrayParametrosLog['application']      = basename(__FILE__);
                            $arrayParametrosLog['appClass']         = basename(__CLASS__);
                            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                            $arrayParametrosLog['messageUser']      = "Error";
                            $arrayParametrosLog['status']           = "Fallido";
                            $arrayParametrosLog['descriptionError'] = $strMensajeError;
                            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];

                            $serviceUtil->insertLog($arrayParametrosLog);  
                        } 
                        else if (isset($arrayResponseService['pin']))
                        {
                            $arrayResponse['response']['pin']    = $arrayResponseService['pin'];
                            //Preparo la data para el envío del pin
                            $arrayDataEnvio                      = array();
                            $arrayDataEnvio['strPin']            = $arrayResponseService['pin'];
                            $arrayDataEnvio['strNumeroTlf']      = $arrayData['contrato']['numeroTelefonico'];
                            $arrayDataEnvio['strIdentificacion'] = $arrayData['contrato']['documentoIdentificacion'];
                            $arrayDataEnvio['strPersonaId']      = $arrayData['contrato']['idcliente'];
                            $arrayDataEnvio['strUsername']       = $arrayData['usrCreacion'];
                            $arrayDataEnvio['strCodEmpresa']     = $arrayData['codEmpresa'];
                            //Realizo el envío del pin
                            $arrayResponseEnvioPin   = $serviceSeguridad->enviarPinSecurity($arrayDataEnvio);
                            if (isset($arrayResponseEnvioPin['mensaje']))
                            {
                                $strMensajeSms = $arrayResponseEnvioPin['mensaje'];
                            }
                        }                            
                        

                        $objAdendums = $this->getManager()->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array("tipo"   => $arrayData['strTipo'],
                                                                           "numero" => $arrayData['numeroAdendum'] ));



                        $arrayResponsePer = $serviceComercialMobile
                                            ->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                                "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                                "strIdentificacion" => $arrayData['contrato']['documentoIdentificacion'],
                                                                "user"              => $arrayData['usrCreacion']));

                        $arrayResponse['persona']    = $arrayResponsePer['response']->persona;
                        $arrayResponse['planes']     = $arrayResponsePer['response']->planes;
                        $arrayResponse['pin']        = $arrayResponseService['pin'];
                        $arrayResponse['mensajeSms'] = $strMensajeSms;
                        $arrayResultado['message']   = "Adendum creado Exitosamente!";
                        $arrayResultado['status']    = $this->status['OK'];
                    }
                    else
                    {
                            $strMensaje      .= $arrayDocumentarCd['mensaje']?$arrayDocumentarCd['mensaje']:", Error al documentar certificado";
                            $booleanRechazaCd = true;
                    }
                }
                else
                {
                    $strMensaje       = $arrayCrearCd['mensaje'] ? $arrayCrearCd['mensaje'] : "Error al crear certificado";
                    $booleanRechazaCd = true;
                }
                //Se consulta si se debe rechazar contrato o no
                if($booleanRechazaCd)
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = 'Adendum en Estado Rechazado';
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];

                    $serviceUtil->insertLog($arrayParametrosLog); 
                
                    $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                    $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                    $arrayParametrosRechazo['strMotivo']      = 'Adendum en Estado Rechazado';
                    $arrayParametrosRechazo['objContrato']    = $objContrato;
                    $arrayResultado['status']  = $this->status['ERROR'];                  
                    $arrayResultado['message'] = $strMensaje;

                    foreach ($arrayParametrosContrato['arrayServicios'] as $intServicioId)
                    {
                        $entityAdendum = $this->getManager('telconet')->getRepository('schemaBundle:InfoAdendum')
                                                                      ->findOneBy(array('servicioId' => $intServicioId));
                        if ($entityAdendum)
                        {   $entityAdendum->setTipo(null);
                            $entityAdendum->setNumero(null);
                            $entityAdendum->setContratoId(null);
                            $entityAdendum->setEstado("Pendiente");
                            $this->getManager()->persist($entityAdendum);
                            $this->getManager()->flush(); 
                        }
                    }                     
                }
            }
        }                                                     
        catch (\Exception $objException)
        {
            $arrayResultado['response'] = array();
            $arrayResultado['status']   = $this->status['ERROR'];            
            $arrayResultado['message']  = ($objException->getCode() === 1 || $objException->getCode() === 206)
                                              ? $objException->getMessage()
                                              : $this->mensaje['ERROR'];

            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];    

            $serviceUtil->insertLog($arrayParametrosLog); 
            
            if(is_object($objContrato) && $arrayData['strTipo'] == "C")
            {
                $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                $arrayParametrosRechazo['objContrato']    = $objContrato;

                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = 'Contrato en Estado Rechazado';
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];    

                $serviceUtil->insertLog($arrayParametrosLog);                 
                //Se rechaza automticamente el contrato porque el proceso no se completo
                $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
            }

            else
            {
                foreach ($arrayParametrosContrato['servicios'] as $intServicioId)
                {

                    $entityAdendum = $this->getManager('telconet')->getRepository('schemaBundle:InfoAdendum')
                                                                  ->findOneBy(array('servicioId' => $intServicioId));
                    if ($entityAdendum)
                    {   $entityAdendum->setTipo(null);
                        $entityAdendum->setNumero(null);
                        $entityAdendum->setContratoId(null);
                        $entityAdendum->setEstado("Pendiente");
                        $this->getManager()->persist($entityAdendum);
                        $this->getManager()->flush(); 
                    }
                } 
            }
        }
        $arrayResultado['response'] = $arrayResponse;
        $arrayResultado['success']  = true;
        return $arrayResultado;
    }

    /**
     * Escribe un archivo temporal con la data que debe estar encodada en base64,
     * devuelve la ruta del archivo creado en /tmp, con prefijo "telcosws_"
     * @param string $strData (encodado en base64)
     * @return string ruta del archivo creado
     */
    private function writeTempFile($strData)
    {
        $strPath = tempnam('/tmp', 'telcosws_');
        $strIfp = fopen($strPath, "wb");
        if (strpos($strData, ',') !== false)
        {
            $strData = explode(',', $strData)[1];
        }
        fwrite($strIfp, base64_decode($strData));
        fclose($strIfp);
        return $strPath;
    }
    
    /**
     * Método que permite la autorización de un contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 25-07-2019  Se invoca llamada a función getPersona mediante instancia del service ComercialService.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 13-09-2019 se agrega parametro con la extension de los documentos ".pdf" para guardar los documentos de contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 20-09-2019 bug.- se corrige el estado del contrato para poder reversar en caso de que no encuentre un certificado digital valido
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.4 09-01-2020 - Se obtiene información de Instalación por promociones.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 09-02-2020 bug.- Se valida el punto y el servicio al momento de autorizar el contrato,
     *                         si no se encuentra el punto o el servicio se reversa el contrato
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 12-02-2020 bug - Se actualiza el estado de adendum a pendiente si es adendum de punto y 
     *                               no tiene 100% de descuento en promoción de instalación
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.7 11-03-2020 - Se obtiene parámetros de estados de planes para contrato digital.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.8 06-04-2020 - Se inicializa la variable de descuento, se mejora el mensaje de respuesta en el catch de
     *                           la presente función.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.9 20-04-2020 - Se valida que si variable de descuento viene null se reversa el contrato
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.10 30-04-2020 - Se valida para que el mensaje que viene de ms de firma no se muestre en la aplicación
     *                            pero si se guarde en la info_log
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.11 03-05-2020 Se valida que exista certificado valido para adendum de punto, sino se reversa el adendum
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.12 13-04-2020 Corrección al mostrar mensaje adecuado al usuario ante algún error.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.13 18-06-2020 - Implementación de persona jurídica.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.14 15-07-2020 - Transferencia de documentos digitales a un servidor remoto.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.15 25-09-2020 - Cambio del formato del nombre de la carpeta donde se almacena los documentos digitales
     *                             en el servidor remoto.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.16 29-09-2020 Se adiciona lógica para almacenar los archivos del contrato en el servidor NFS.
     *
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.16 29-10-2020 - Se agrega reverso de contrato por caducidad de certificado digital
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.17 07-12-2020 - Validaciones para el contrato o adendum cuando se encuentre ya autorizados.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.18 10-12-2020 - Para los valores strTipo = C se cambia la funcionalidad para que trabaje con el punto recibido 
     *                            como parametro de entrada y no con el primer punto cliente padre.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.19 01-02-2021 -  Se corrige para que no coja el primer servicio sino el servicio que tenga el plan de internet
     *                             para validar el contrato
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se eliminó consumo de este método, se implementó  autorizacion de contrato desde ms 
     * @deprecated
     */
    private function putAutorizarContrato($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $strMensaje                 = "";
        $arrayResponse              = array();
        $strUsrCreacion             = $arrayData['user'];
        $arrayServicios             = $arrayData['servicios'];
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil                = $this->get('schema.Util');
        /* @var $serviceSeguridad SeguridadService */
        $serviceSeguridad           = $this->get('seguridad.Seguridad');
        $objServiceCrypt            = $this->get('seguridad.crypt');
        
        /* @var $serviceContratoAprob InfoContratoAprobService */
        $serviceContratoAprob       = $this->get('comercial.InfoContratoAprob');
        $serviceContrato            = $this->get('comercial.InfoContrato');
        $strHayCertificado          = "S";
        $serviceComercialMobile     = $this->get('comercial.ComercialMobile');
        $serviceContratoDigital     = $this->get('comercial.InfoContratoDigital');
        $serviceCertificacion       = $this->get('comercial.CertificacionDocumentos');
        $strClientIp                = isset($arrayData['ip']) ? $arrayData['ip'] : '127.0.0.1';
        $arrayData['ip']            = $strClientIp;
        $strNombreCarpetaDocumentos = '';
        $boolNFS                    = false;

        try
        {
            $objPersonaContrato = $serviceComercialMobile->obtenerDatosPersonaPrv(array('strCodEmpresa'     => $arrayData['codEmpresa'],
                                                                                        'strIdentificacion' => $arrayData['usuario'],
                                                                                        'strPrefijoEmpresa' => $arrayData['prefijoEmpresa']));

            if(is_null($objPersonaContrato))
            {
                throw new \Exception("No se pudo recuperar información del cliente al  autorizar contrato", 1);
            }

            $arrayParametrosDet     = $this->getManager()
                                           ->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('ESTADO_AUTORIZADO_CONTRATO_ADENDUM',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
            if(!empty($arrayParametrosDet) && isset($arrayParametrosDet))
            {
                $arrayEstados  = explode(',',$arrayParametrosDet['valor1']);
                $strTipo       = ($arrayData['strTipo'] == 'C') ? 'contrato' : 'adendum';
                $strMensaje    = str_replace('{{tipo}}',$strTipo,$arrayParametrosDet['valor2']);
                $intIdServicio = 0;
                foreach ($arrayServicios as $intIdServicio)
                {
                    $entityServicio = $this->getManager('telconet')
                                            ->getRepository('schemaBundle:InfoServicio')
                                            ->find($intIdServicio);
                    if ($entityServicio && $entityServicio->getPlanId())
                    {
                        break;
                    }

                }
                $objAdendumEst = $this->getManager('telconet')
                                      ->getRepository('schemaBundle:InfoAdendum')
                                      ->findOneBy(array('servicioId' => $intIdServicio,
                                                        'contratoId' => $arrayData['contrato']));
                if (is_object($objAdendumEst) && in_array($objAdendumEst->getEstado(), $arrayEstados) )
                {
                    throw new \Exception('ERROR_PARCIAL', 1);
                }
            }

            if($objPersonaContrato->tipoTributario == 'JUR')
            {
                $arrayRepLegal = $serviceComercialMobile
                                     ->obtenerDatosRepresentanteLegal(array('strTipoIdentificacion'   => $objPersonaContrato->tipoIdentificacion,
                                                                            'strIdentificacion'       => $objPersonaContrato->identificacionCliente,
                                                                            'strCodEmpresa'           => $arrayData['codEmpresa'],
                                                                            'strPrefijoEmpresa'       => $arrayData['prefijoEmpresa'],
                                                                            'usrCreacion'             => $arrayData['user'],
                                                                            'booleanOrigenGetPersona' => true));

                if(is_null($arrayRepLegal) || empty($arrayRepLegal))
                {
                    throw new \Exception("No se pudo recuperar información del representante legal", 1);
                }
                else
                {
                    $objPersonaContrato->representanteLegalJuridico = $arrayRepLegal;
                }
            }

            $arrayParametrosDet = $this->getManager()
                                       ->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getOne('PARAMETROS_ESTRUCTURA_RUTAS_TELCOS',
                                                '',
                                                '',
                                                '',
                                                isset($arrayData['strAplicacion']) ? $arrayData['strAplicacion'] : 'TELCOS',
                                                '',
                                                '',
                                                '',
                                                '',
                                                '');
            if(!empty($arrayParametrosDet))
            {
                $strAplicacion          = $arrayParametrosDet['valor2'];
                $arrayData['strApp']    = $strAplicacion;
            }

            $arrayParametrosDetNFS  = $this->getManager()
                                           ->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('BANDERA_NFS',
                                                    '',
                                                    '',
                                                    '',
                                                    'S',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
            if(isset($arrayParametrosDetNFS) && $arrayParametrosDetNFS['valor1'] === 'S')
            {
                $boolNFS              = true;
            }
            $arrayData['bandNfs'] = $boolNFS;

            if  ($arrayData['strTipo'] == 'C')
            {
                $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                                        ->findOneBy(array("numCedula" => ($objPersonaContrato->tipoTributario == 'NAT')
                                                                             ? $objPersonaContrato->identificacionCliente
                                                                             : $objPersonaContrato->representanteLegalJuridico['identificacion'],
                                                            "estado"  => "valido"));
                if(count($objCertificado) <= 0)
                {
                    $strHayCertificado = "N";
                    $objContrato = $this->getManager("telconet")->getRepository('schemaBundle:InfoContrato')
                                                                ->findOneBy(array(
                                                                            "id"     => $arrayData['contrato'],
                                                                            "estado" => "PorAutorizar"
                                                                            ));
                    $strMensaje =  'No se encuentra certificado, se reversa el contrato digital. Por favor volver a consultar cliente';           
                    $arrayParametros['objContrato']    = $objContrato;
                    $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                    $arrayParametros['strIpCreacion']  = "127.0.0.1";
                    $arrayParametros['strMotivo']      = $strMensaje;
                    
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = $strMensaje;
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                    $serviceUtil->insertLog($arrayParametrosLog);   
                    
                    $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorCertificado($arrayParametros);
                    throw new \Exception('ERROR_PARCIAL', 1);
                }
                //valido que el certificado este dentro de los 3 dias de validez

                $boolValido = $serviceCertificacion->isValidoCertificadoId($objCertificado->getId());
                if (!$boolValido)
                {

                    $strHayCertificado = "N";
                    $objContrato = $this->getManager("telconet")->getRepository('schemaBundle:InfoContrato')
                                                                ->findOneBy(array(
                                                                            "id"     => $arrayData['contrato'],
                                                                            "estado" => "PorAutorizar"
                                                                            ));
                    $strMensaje =  'Firma Electrónica Expirada, Favor volver a consultar la persona y procese nuevamente';
                    $arrayParametros['objContrato']    = $objContrato;
                    $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                    $arrayParametros['strIpCreacion']  = "127.0.0.1";
                    $arrayParametros['strMotivo']      = $strMensaje;
                    //obtengo los servicios ligados al contrato
                    $objAdendums = $this->getManager("telconet")->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array("contratoId" => $arrayData['contrato'],
                                                                            "tipo"       => "C"));
                    $entityServicio = null;
                    //recorro los adendum para buscar el servicio de internet
                    foreach ($objAdendums as $objAdendum)
                    {
                        $entityServicio = $this->getManager("telconet")->getRepository('schemaBundle:InfoServicio')
                                                                    ->find($objAdendum->getServicioId());
                        if ($entityServicio->getPlanId())
                        {
                            break;
                        }
                    }

                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = $strMensaje;
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;

                    $serviceUtil->insertLog($arrayParametrosLog);

                    $arrayParametros['strEstado'] = 'Anulado';
                    $arrayParametros['strAccion'] = 'Eliminar';
                    $arrayParametros['objServicio'] = $entityServicio;
                    $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorCertificadoCaducado($arrayParametros);

                    $arrayResponse['message'] = $strMensaje;
                    throw new \Exception('ERROR_PARCIAL', 1);
                    
                }



                $objContrato   = $this->getManager()->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneBy(array("id" => $arrayData['contrato']));                    
                
                $objPunto =  $this->getManager()->getRepository('schemaBundle:InfoPunto')
                                ->find($arrayData['puntoId']);
                if(!is_object($objPunto))
                { 
                    $strMensaje =  'No se encuentra el punto, se reversa el contrato digital. Por favor volver a consultar cliente';
                    $arrayParametrosRechazo['strUsrCreacion'] = $strUsrCreacion;
                    $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                    $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                    $arrayParametrosRechazo['objContrato']    = $objContrato;
                    
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";

                    $arrayParametrosLog['descriptionError'] = $strMensaje . " Parametros: " . $objContrato->getPersonaEmpresaRolId()->getId();

                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                    $serviceUtil->insertLog($arrayParametrosLog);   
                    
                    $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                    
                    throw new \Exception('ERROR_PARCIAL', 1);
                }    
                        
                $arrayServicios  = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                            ->findTodosServiciosXEstado($objPunto->getPersonaEmpresaRolId()->getId(),
                                                                        0,
                                                                        1000,
                                                                        "Factible");
                

                if(!$arrayServicios['registros'])

                {
                    $strMensaje =  'No se encuentra el servicio, se reversa el contrato digital. Por favor volver a consultar cliente';
                    $arrayParametrosRechazo['strUsrCreacion'] = $strUsrCreacion;
                    $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                    $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                    $arrayParametrosRechazo['objContrato']    = $objContrato;
                    
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";

                    $arrayParametrosLog['descriptionError'] = $strMensaje . " Parametros: " . $objPunto->getPersonaEmpresaRolId()->getId();

                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                    $serviceUtil->insertLog($arrayParametrosLog);   
                    
                    $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                    
                    throw new \Exception('ERROR_PARCIAL', 1);
                } 

            }
            if  ($arrayData['strTipo'] == 'AS' || $arrayData['strTipo'] == 'AP')
            {
                $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                                        ->findOneBy(array("numCedula" => ($objPersonaContrato->tipoTributario == 'NAT')
                                                                             ? $objPersonaContrato->identificacionCliente
                                                                             : $objPersonaContrato->representanteLegalJuridico['identificacion'],
                                                            "estado"  => "valido"));
                if(count($objCertificado) <= 0)
                {
                    $strHayCertificado = "N";
                                
                    $strMensaje =  'No se encuentra certificado valido. Por favor volver a consultar cliente';           

                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = $strMensaje;
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                    $serviceUtil->insertLog($arrayParametrosLog);   

                    $serviceContratoDigital->reversarAdendum(array('strTipo'   => $arrayData['strTipo'],
                                                                   'strNumero' => $arrayData['numeroAdendum']));

                
                    throw new \Exception('ERROR_PARCIAL', 1);
                }
                $boolValido = $serviceCertificacion->isValidoCertificadoId($objCertificado->getId());
                if (!$boolValido)
                {

                    $strHayCertificado = "N";

                    $strMensaje =  'Firma Electrónica Expirada, Favor volver a consultar la persona y procese nuevamente';
                    //obtengo los servicios ligados al contrato
                    $objAdendums = $this->getManager("telconet")->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array("numero" => $arrayData['numeroAdendum'],
                                                                            "tipo"       => $arrayData['strTipo']));
                    $entityServicio = null;
                    //recorro los adendum para buscar el servicio de internet
                    foreach ($objAdendums as $objAdendum)
                    {
                        $entityServicio = $this->getManager("telconet")->getRepository('schemaBundle:InfoServicio')
                                                                    ->find($objAdendum->getServicioId());
                        if ($entityServicio->getPlanId())
                        {
                            break;
                        }
                    }

                    $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = $strMensaje;
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;

                    $serviceUtil->insertLog($arrayParametrosLog);
                    $arrayParametros['strTipo'] = $arrayData['strTipo'];
                    $arrayParametros['strNumero'] = $arrayData['numeroAdendum'];
                    $arrayParametros['strEstado'] = 'Anulado';
                    $arrayParametros['strAccion'] = 'Eliminar';
                    $arrayParametros['objServicio'] = $entityServicio;
                    $serviceContratoDigital->reversarAdendumCertificadoCaducado($arrayParametros);

                    $arrayResponse['message'] = $strMensaje;
                    throw new \Exception('ERROR_PARCIAL', 1);

                }


            }        
            // ========================================================================
            // Validacion del PinCode
            // ========================================================================       
            $arrayResponseService    = $serviceSeguridad->validarPinSecurity($arrayData['pincode'],
                                                                             $arrayData['usuario'],
                                                                             $arrayData['codEmpresa']);

            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];

                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $strMensaje;
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                $serviceUtil->insertLog($arrayParametrosLog);  

                throw new \Exception('ERROR_PARCIAL', 1);
            }
            // ========================================================================
            // Autorizar el contrato digitalmente
            // ========================================================================
            // ========================================================================
            // Enviar a firmar los documentos 
            // ========================================================================
            //Obtener datos contrato

            $arrayData['hayCertificado'] = $strHayCertificado;

            $arrayDocumentosFirmados = $serviceContratoDigital->firmarDocumentosNew($arrayData);

            if ($arrayDocumentosFirmados['salida']  == '0')
            {
                $arrayResponse['message'] = $arrayDocumentosFirmados['mensaje'];

                $strMensaje = $arrayDocumentosFirmados['mensaje'];
                throw new \Exception('ERROR_PARCIAL', 1);
            }
            if ($arrayDocumentosFirmados['salida'] == '1')
            {
                $intIdContrato = $arrayData['contrato'];
                $objContrato   = $this->getManager()->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneBy(array("id" => $arrayData['contrato']));
                $strMensaje="Contrato |";
                                 
                $arrayDocumentos    = $arrayDocumentosFirmados['documentos'];

                if(is_object($objContrato))
                {
                    if ($arrayData['strTipo'] == "C")
                    {
                        $strPrefixMensaje = 'El contrato: ' . $objContrato->getNumeroContrato();
                        $arrayResponseService['strObservacionHistorial'] = $strPrefixMensaje . ' ' . $arrayResponseService['strObservacionHistorial'];
    
                        // ================================================================================
                        $objPersonaEmpresaRol   = $serviceContratoAprob->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                        $arrayUltimaMilla       = $serviceContratoDigital->getDatosUltimaMilla($objPersonaEmpresaRol);
    
                        // Verificamos que tengamos la ultima milla del servicio contratado
                  
                        if(!empty($arrayUltimaMilla))
                        {
                            $objPunto =  $this->getManager()->getRepository('schemaBundle:InfoPunto')
                                         ->findPrimerPtoClientePadreActivoPorPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                                if(is_object($objPunto))
                                { 
                                    
                                    $arrayServicios  = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                             ->findTodosServiciosXEstado($objPunto->getPersonaEmpresaRolId()->getId(),
                                                                                            0,
                                                                                            1000,
                                                                                            "Factible");

                                    $objAdmiParametroCabPlan  = $this->getManager()->getRepository('schemaBundle:AdmiParametroCab')
                                                                                  ->findOneBy( array('nombreParametro' => 'ESTADO_PLAN_CONTRATO', 
                                                                                                     'estado'          => 'Activo') );
                                        if(is_object($objAdmiParametroCabPlan))
                                        {        
                                            $arrayParametroDetPlan = $this->getManager()->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->findBy(  array ("parametroId" => $objAdmiParametroCabPlan->getId(),
                                                                                    "estado"      => "Activo" ));
                                            if ($arrayParametroDetPlan)
                                            {
                                                foreach($arrayParametroDetPlan as $objAdmiParametroDet)
                                                {
                                                      //Estados permitidos
                                                    $arrayEstadosPlanPermitidos[]=$objAdmiParametroDet->getValor1();
                                                                           
                                                }
                                            }

                                        }
                                    
                                    if($arrayServicios['registros'])
                                    { 
                                        foreach($arrayServicios['registros'] as $objValue)
                                        {
                                        $arrayParametrosPlan = array("arrayEstadosPlan" => $arrayEstadosPlanPermitidos,
                                                                     "planId"            => $objValue->getPlanId());
                                        $emComercial = $this->getDoctrine()->getManager('telconet');
                                        $arrayPlanDet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->getPlanesContratoDigital($arrayParametrosPlan);
          
                                            foreach($arrayPlanDet as $objPlanDet)
                                            {
                                                $objProducto = $this->getManager()->getRepository('schemaBundle:AdmiProducto')
                                                                                  ->findOneBy(array("id" => $objPlanDet->getProductoId()));
                                                $strMensaje.="NombreTecnico: {$objProducto->getNombreTecnico()} |";                                  
                                                if($objProducto->getNombreTecnico() === "INTERNET")
                                                {
                                                    $strPromIns = 'PROM_INS';
                                                    $strMensaje.="intIdPunto: {$objPunto->getId()} | intIdServicio: {$objValue->getId()} 
                                                        | strCodigoGrupoPromocion: {$strPromIns} | intCodEmpresa: {$arrayData['codEmpresa']} |";
                                                    $arrayParametrosIns = array(
                                                        'intIdPunto'               => $objPunto->getId(),
                                                        'intIdServicio'            => $objValue->getId(),
                                                        'strCodigoGrupoPromocion'  => $strPromIns,
                                                        'intCodEmpresa'            => $arrayData['codEmpresa']
                                                     );
                                                    $arrayContratoPromoIns[]  = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                                                                  ->getPromocionesContrato($arrayParametrosIns);

                                                    if($arrayContratoPromoIns 
                                                       && array_key_exists('strCodError',$arrayContratoPromoIns[0]) 
                                                       && $arrayContratoPromoIns[0]['strCodError']=='503')
                                                    {
                                                        $strMensaje =  $arrayContratoPromoIns[0]['strMensaje'];
                                                        $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                                                        $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                                                        $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                                                        $arrayParametrosRechazo['objContrato']    = $objContrato;  
                                                        $objPersonaEmpresaRol = $serviceContratoAprob
                                                                                ->rechazarContratoPorError($arrayParametrosRechazo);
                                                        throw new \Exception('ERROR_PARCIAL', 1);
                                                    }                                                    
                                                  
                                                    if ($arrayContratoPromoIns && 
                                                        $arrayContratoPromoIns[0]['intDescuento'] === null) 
                                                    {
                                                        $strMensaje = "No se pudo obtener información de promociones";
                                                        $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                                                        $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                                                        $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                                                        $arrayParametrosRechazo['objContrato']    = $objContrato;
                                                        $objPersonaEmpresaRol     = $serviceContratoAprob
                                                                                    ->rechazarContratoPorError($arrayParametrosRechazo);
                                                        throw new \Exception('ERROR_PARCIAL', 1);
                                                    }

                                                    $intPorcentaje = $arrayContratoPromoIns[0]['intDescuento'];
                                                    $strMensaje.=" intPorcentaje: {$intPorcentaje} |";


                                               }
                                            }
                                        }
                                    }
                                }

                                // Si el porcentaje de descuesto es el 100% entonces se debe activar el contrato
                                // automaticamente
                                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                                $arrayParametrosLog['logType']          = "0";
                                $arrayParametrosLog['logOrigin']        = "TELCOS";
                                $arrayParametrosLog['application']      = basename(__FILE__);
                                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                                $arrayParametrosLog['messageUser']      = "";
                                $arrayParametrosLog['status']           = "Info";
                                $arrayParametrosLog['descriptionError'] = $strMensaje;
                                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                                $arrayParametrosLog['creationUser']     = $strUsrCreacion;
                                $serviceUtil->insertLog($arrayParametrosLog);                                 
                                if($intPorcentaje == 100)
                                {
                                    /**
                                    * Obtiene los servicios de la persona empresa rol
                                    */ 
                                    $arrayServiciosEncontrados = array();
    
                                    if($arrayData['prefijoEmpresa']=='TN')
                                    {
                                        $arrayEstado        = array( 'Rechazado', 
                                                                     'Rechazada', 
                                                                     'Cancelado', 
                                                                     'Anulado', 
                                                                     'Cancel', 
                                                                     'Eliminado', 
                                                                     'Reubicado', 
                                                                     'Trasladado' );
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstadoTn( $objPersonaEmpresaRol->getId(), 
                                                                                                                 0, 
                                                                                                                 10000, 
                                                                                                                 $arrayEstado );
                                    }
                                    else
                                    {
                                        $strEstadoTmp       = 'Factible';
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstado( $objPersonaEmpresaRol->getId(),
                                                                                                               0, 
                                                                                                               10000, 
                                                                                                               $strEstadoTmp );
                                    }
    
                                    $arrayRegistrosServicios = $arrayResultadoTmp['registros'];
    
                                    if( !empty($arrayRegistrosServicios) )
                                    {
                                        foreach($arrayRegistrosServicios as $objServicioTmp)
                                        {
                                            $entityAdendum = $this->getManager('telconet')->getRepository('schemaBundle:InfoAdendum')
                                                                  ->findOneBy(array('servicioId' => $objServicioTmp->getId(),
                                                                                    'contratoId' => $arrayData['contrato']));
                                            if ($entityAdendum)       
                                            {
                                                $arrayServiciosEncontrados[] = $objServicioTmp->getId();
                                            }                                 
                                        }
                                    }

                                    // --------------------------------------------------------------------------
                                    //obtener el arreglo de los datos de formas de pago
                                    $arrayFormaPago       = array();
                                    $intIdTipoCuenta      = 0;
                                    $intIdFormaPago       = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
    
                                    if($objContratoFormaPago)
                                    {
                                        $arrayFormaPago['bancoTipoCuentaId']    = $objContratoFormaPago->getBancoTipoCuentaId() ?
                                                                                  $objContratoFormaPago->getBancoTipoCuentaId()->getId() : 0;
                                        $arrayFormaPago['numeroCtaTarjeta']     = $objServiceCrypt
                                                                                        ->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
                                        $arrayFormaPago['mesVencimiento']       = $objContratoFormaPago->getMesVencimiento();
                                        $arrayFormaPago['anioVencimiento']      = $objContratoFormaPago->getAnioVencimiento();
                                        $arrayFormaPago['codigoVerificacion']   = $objContratoFormaPago->getCodigoVerificacion();
                                        $arrayFormaPago['titularCuenta']        = $objContratoFormaPago->getTitularCuenta();
                                        $intIdTipoCuenta                        = $objContratoFormaPago->getTipoCuentaId() ? 
                                                                                  $objContratoFormaPago->getTipoCuentaId()->getId() : 0;
                                    }
                                    $arrayParametros = array();
                                    $arrayParametros['intIdContrato']           = $intIdContrato;
                                    $arrayParametros['arrayPersona']            = array();
                                    $arrayParametros['arrayPersonaExtra']       = array();
                                    $arrayParametros['arrayFormasContacto']     = array();
                                    $arrayParametros['arrayFormaPago']          = $arrayFormaPago;
                                    $arrayParametros['intIdFormaPago']          = $intIdFormaPago;
                                    $arrayParametros['intIdTipoCuenta']         = $intIdTipoCuenta;
                                    $arrayParametros['arrayServicios']          = $arrayServiciosEncontrados;
                                    $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
                                    $arrayParametros['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametros['strPrefijoEmpresa']       = $arrayData['prefijoEmpresa'];
                                    $arrayParametros['strOrigen']               = 'MOVIL';
                                    $arrayParametros['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametros['strEmpresaCod']           = $arrayData['codEmpresa'];
    
                                    // Aprobacion del contrato
                                    $arrayGuardarProceso = $serviceContratoAprob->guardarProcesoAprobContrato($arrayParametros);
                                    if($arrayGuardarProceso 
                                       && array_key_exists('status',$arrayGuardarProceso) 
                                       && $arrayGuardarProceso['status']=='ERROR_SERVICE')
                                    {
                                        $strMensaje = $arrayGuardarProceso['mensaje'];
                                        throw new \Exception('ERROR_PARCIAL ' . $strMensaje, 1);
                                    }
                                    $strEstadoContrato = 'Activo';
                                }
                                else
                                {
                                    $arrayParametrosContrato                            = array();
                                    $arrayParametrosContrato["intIdContrato"]           = $intIdContrato;
                                    $arrayParametrosContrato['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametrosContrato['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametrosContrato["strOrigen"]               = "MOVIL";
    
                                    $serviceContrato->setearDatosContrato($arrayParametrosContrato);
                                    $strEstadoContrato = 'Pendiente';
                                }
                                $objAdendums = $this->getManager()->getRepository('schemaBundle:InfoAdendum')
                                                                    ->findBy(array("tipo"       => $arrayData['strTipo'],
                                                                                   "contratoId" => $intIdContrato));
                                if ($objAdendums)
                                {
                                    foreach ($objAdendums as $entityAdendum)
                                    {
                                        $entityAdendum->setEstado($strEstadoContrato);
                                        $entityAdendum->setFeModifica(new \DateTime('now'));
                                        $this->getManager()->persist($entityAdendum);
                                    }
                                }

                                $objContrato->setEstado($strEstadoContrato);
                                $this->getManager("telconet")->persist($objContrato);
                                $this->getManager("telconet")->flush();
                       
                        }    
    
                    }
                    $objPersonaEmpresaRol   = $serviceContratoAprob->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                    if ($arrayData['strTipo'] !== "C")
                    {

                        $strMensaje ="Adendum |";
                        
                        $objPunto =  $this->getManager()->getRepository('schemaBundle:InfoPunto')->find($arrayData['puntoId']);
                                          
                        if(is_object($objPunto))
                        { 
                            
                            $arrayServicios  = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                        ->findTodosServiciosXEstado($objPunto->getPersonaEmpresaRolId()->getId(),
                                                                                    0,
                                                                                    1000,
                                                                                    "Factible");
          
                            $objAdmiParametroCabPlan  = $this->getManager()->getRepository('schemaBundle:AdmiParametroCab')
                                                                          ->findOneBy( array('nombreParametro' => 'ESTADO_PLAN_CONTRATO', 
                                                                                             'estado'          => 'Activo') );
                                if(is_object($objAdmiParametroCabPlan))
                                {        
                                    $arrayParametroDetPlan = $this->getManager()->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->findBy(  array ("parametroId" => $objAdmiParametroCabPlan->getId(),
                                                                            "estado"      => "Activo" ));
                                    if ($arrayParametroDetPlan)
                                    {
                                        foreach($arrayParametroDetPlan as $objAdmiParametroDet)
                                        {
                                              //Estados permitidos
                                            $arrayEstadosPlanPermitidos[]=$objAdmiParametroDet->getValor1();

                                        }
                                    }

                                }
                            $objServicioPlan = null;
                            if($arrayServicios['registros'])
                            { 
                                foreach($arrayServicios['registros'] as $objValue)
                                {
                                        $arrayParametrosPlan = array("arrayEstadosPlan" => $arrayEstadosPlanPermitidos,
                                                                     "planId"            => $objValue->getPlanId());
                                        $emComercial = $this->getDoctrine()->getManager('telconet');
                                        $arrayPlanDet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->getPlanesContratoDigital($arrayParametrosPlan);

                                    foreach($arrayPlanDet as $objPlanDet)
                                    {
                                        $objProducto = $this->getManager()->getRepository('schemaBundle:AdmiProducto')
                                                                            ->findOneBy(array("id" => $objPlanDet->getProductoId()));
                                        $strMensaje.="NombreTecnico: {$objProducto->getNombreTecnico()} |";
                                        if($objProducto->getNombreTecnico() === "INTERNET")
                                        {
                                            $objServicioPlan = $objValue;                                    
                                            $strPromIns = 'PROM_INS';
                                            $arrayParametrosIns = array(
                                                'intIdPunto'               => $objPunto->getId(),
                                                'intIdServicio'            => $objValue->getId(),
                                                'strCodigoGrupoPromocion'  => $strPromIns,
                                                'intCodEmpresa'            => $arrayData['codEmpresa']
                                                );
                                            $strMensaje.="intIdPunto: {$objPunto->getId()} | intIdServicio: {$objValue->getId()} 
                                            | strCodigoGrupoPromocion: {$strPromIns} | intCodEmpresa: {$arrayData['codEmpresa']} |";
                                            $arrayContratoPromoIns[]  = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                                                            ->getPromocionesContrato($arrayParametrosIns);


                                            if($arrayContratoPromoIns 
                                                && array_key_exists('strCodError',$arrayContratoPromoIns[0]) 
                                                && $arrayContratoPromoIns[0]['strCodError']=='503')
                                            {
                                                $strMensaje =  $arrayContratoPromoIns[0]['strMensaje'];
                                                throw new \Exception('ERROR_PARCIAL', 1);
                                            }                                                    
                                            if ($arrayContratoPromoIns && 
                                               $arrayContratoPromoIns[0]['intDescuento'] === null) 
                                            {
                                                $strMensaje = "No se pudo obtener información de promociones";
                                                $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                                                $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                                                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                                                $arrayParametrosRechazo['objContrato']    = $objContrato;
                                                $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                                                throw new \Exception('ERROR_PARCIAL', 1);
                                            }
                                            $intPorcentaje = $arrayContratoPromoIns[0]['intDescuento'];
                                            $strMensaje.=" intPorcentaje: {$intPorcentaje} |";



                                        }
                                    }
                                }
                            }
                        }

                        $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                        $arrayParametrosLog['logType']          = "0";
                        $arrayParametrosLog['logOrigin']        = "TELCOS";
                        $arrayParametrosLog['application']      = basename(__FILE__);
                        $arrayParametrosLog['appClass']         = basename(__CLASS__);
                        $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                        $arrayParametrosLog['appAction']        = "revision_promociones";
                        $arrayParametrosLog['messageUser']      = "";
                        $arrayParametrosLog['status']           = "Info";
                        $arrayParametrosLog['descriptionError'] = $strMensaje;
                        $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                        $arrayParametrosLog['creationUser']     = $strUsrCreacion;
                        $serviceUtil->insertLog($arrayParametrosLog); 
                        if($intPorcentaje == 100 || $arrayData['strTipo'] == 'AS')
                        {

                            $arrayServiciosEncontrados = array();
        
                            if($arrayData['prefijoEmpresa']=='TN')
                            {
                                $arrayEstado        = array( 'Rechazado', 
                                                            'Rechazada', 
                                                            'Cancelado', 
                                                            'Anulado', 
                                                            'Cancel', 
                                                            'Eliminado', 
                                                            'Reubicado', 
                                                            'Trasladado' );
                                $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstadoTn( $objPersonaEmpresaRol->getId(), 
                                                                                                        0, 
                                                                                                        10000, 
                                                                                                        $arrayEstado );
                            }
                            else
                            {
                                $strEstadoTmp       = 'Factible';
                                $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstado( $objPersonaEmpresaRol->getId(),
                                                                                                    0, 
                                                                                                    10000, 
                                                                                                    $strEstadoTmp );
                            }

                            $arrayRegistrosServicios = $arrayResultadoTmp['registros'];

                            if( !empty($arrayRegistrosServicios) )
                            {
                                foreach($arrayRegistrosServicios as $objServicioTmp)
                                {
                                    $entityAdendum = $this->getManager('telconet')->getRepository('schemaBundle:InfoAdendum')
                                                        ->findOneBy(array('servicioId' => $objServicioTmp->getId(),
                                                                            'tipo'       => $arrayData['strTipo'],
                                                                            'numero'     => $arrayData['numeroAdendum']));
                                    if ($entityAdendum)       
                                    {
                                        $arrayServiciosEncontrados[] = $objServicioTmp->getId();
                                    }                                 
                                }
                            }
                            $arrayParametros = array();
                            $arrayParametros['arrayServicios']          = $arrayServiciosEncontrados;
                            $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
                            $arrayParametros['strIpCreacion']           = '127.0.0.1';
                            $arrayParametros['strPrefijoEmpresa']       = $arrayData['prefijoEmpresa'];
                            $arrayParametros['strOrigen']               = 'MOVIL';
                            $arrayParametros['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                            $arrayParametros['strEmpresaCod']           = $arrayData['codEmpresa'];
                            $arrayParametros['personaEmpresaRolId']     = $objPersonaEmpresaRol->getId();
                            $arrayParametrosContrato['bandNfs']        = $boolNFS;
                            $arrayParametrosContrato['strApp']         = $strAplicacion;


                            // Aprobacion del contrato
                            $arrayGuardarProceso = $serviceContratoAprob->aprobarAdendum($arrayParametros);
                            if($arrayGuardarProceso 
                            && array_key_exists('status',$arrayGuardarProceso) 
                            && $arrayGuardarProceso['status']=='ERROR_SERVICE')
                            {
                                $strMensaje = $arrayGuardarProceso['mensaje'];
                                throw new \Exception('ERROR_PARCIAL ' . $strMensaje, 1);
                            }
                            //actualizamos los estados del adendum
                            $objAdendums = $this->getManager()->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array("tipo"   => $arrayData['strTipo'],
                                                                           "numero" => $arrayData['numeroAdendum']));
                            if ($objAdendums)
                            {
                                $strEstadoAdendum = 'Pendiente';
                                if ($intPorcentaje == 100 || $arrayData['strTipo'] == 'AS')
                                {
                                    $strEstadoAdendum = 'Activo';
                                }
                                foreach ($objAdendums as $entityAdendum)
                                {
                                    $entityAdendum->setFeModifica(new \DateTime('now'));
                                    $entityAdendum->setUsrModifica($strUsrCreacion);
                                    $entityAdendum->setEstado($strEstadoAdendum);
                                    $this->getManager()->persist($entityAdendum);
                                    $this->getManager()->flush();    
                                }
                            }
                            else
                            {
                                throw new \Exception('ERROR_PARCIAL ' . "No Existe Adendum para Autorizar", 1);
                            }   
                        } 
                        else
                        {
                            $objAdendums = $this->getManager()->getRepository('schemaBundle:InfoAdendum')
                                                              ->findBy(array("tipo"   => $arrayData['strTipo'],
                                                                             "numero" => $arrayData['numeroAdendum']));
                            if ($objAdendums)
                            {
                                foreach ($objAdendums as $entityAdendum)
                                {
                                    $entityAdendum->setFeModifica(new \DateTime('now'));
                                    $entityAdendum->setUsrModifica($strUsrCreacion);
                                    $entityAdendum->setEstado("Pendiente");
                                    $this->getManager()->persist($entityAdendum);
                                    $this->getManager()->flush();    
                                }
                            }
                            else
                            {
                                throw new \Exception('ERROR_PARCIAL ' . "No Existe Adendum para Autorizar", 1);
                            }   
                        } 
                        if ($arrayData['strTipo'] == 'AP')
                        {
                            $objCaracteristica  = $this->getManager()->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array('descripcionCaracteristica' => 'PROM_INSTALACION',
                                                                         'tipo'                      => 'COMERCIAL',
                                                                         'estado'                    => 'Activo'));

                            $objInfoServCarac = $this->getManager()->getRepository("schemaBundle:InfoServicioCaracteristica")
                                                                    ->findOneBy(array("servicioId"       => $objServicioPlan->getId(),
                                                                                    "caracteristicaId" => $objCaracteristica,
                                                                                    "estado"           => "Activo"));
                            if (!is_object($objInfoServCarac))
                            {
                                $objInfoServicioCaracteristica = new InfoServicioCaracteristica();
                                $objInfoServicioCaracteristica->setServicioId($entityServicio);
                                $objInfoServicioCaracteristica->setCaracteristicaId($objCaracteristica);
                                $objInfoServicioCaracteristica->setEstado("Activo");
                                $objInfoServicioCaracteristica->setObservacion("Se crea característica para facturación por punto adicional");
                                $objInfoServicioCaracteristica->setUsrCreacion($arrayData['usrCreacion']);
                                $objInfoServicioCaracteristica->setFeCreacion(new \DateTime('now'));
                                $objInfoServicioCaracteristica->setIpCreacion($strClientIp);
                                $objInfoServicioCaracteristica->setValor("");
                                $this->getManager()->persist($objInfoServicioCaracteristica);
                                $this->getManager()->flush();
                            }
                        }

                    }
                    // Guardamos los documentos firmados
                    $objPersonaEmpresaRol   = $objContrato->getPersonaEmpresaRolId();
                    $strIdentificacion      = is_object($objPersonaEmpresaRol->getPersonaId()) ?
                                                $objPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente()
                                                : 'SIN_IDENTIFICACION';
                    $arrayPathAdicional     = null;
                    $arrayPathAdicional[]   = array('key' => $strIdentificacion);

                    $arrayParametrosDocumentos['codEmpresa']     = $arrayData['codEmpresa'];
                    $arrayParametrosDocumentos['strUsrCreacion'] = $arrayData['user'];
                    $arrayParametrosDocumentos['strTipoArchivo'] = 'PDF';
                    $arrayParametrosDocumentos['intContratoId']  = $intIdContrato;
                    $arrayParametrosDocumentos['enviaMails']     = $arrayDocumentosFirmados['enviaMails'];
                    $arrayParametrosDocumentos['strExtension']   = '.pdf';
                    $arrayParametrosDocumentos['strTipo']        = $arrayData['strTipo'];
                    $arrayParametrosDocumentos['strNumeroAdendum'] = $arrayData['numeroAdendum'];
                    $arrayParametrosDocumentos['prefijoEmpresa']    = $arrayData['prefijoEmpresa'];
                    $arrayParametrosDocumentos['arrayPathAdicional']= $arrayPathAdicional;
                    $arrayParametrosDocumentos['strApp']            = $strAplicacion;
                    $arrayParametrosDocumentos['bandNfs']           = $boolNFS;
                    $serviceContratoDigital->guardarDocumentos($arrayParametrosDocumentos,$arrayDocumentos);

                    // ========================================================================
                    // Envio de documentos a security data
                    // ========================================================================

                    if($objPersonaContrato->tipoTributario == 'JUR')
                    {
                        $strNombreCarpetaDocumentos = str_replace(' ', '_', strtolower($arrayRepLegal['nombres'])) . '_' .
                                                      str_replace(' ', '_', strtolower($arrayRepLegal['apellidos'])) . '_' .
                                                      $objPersonaContrato->identificacionCliente . '_' . $arrayRepLegal['identificacion'] . '_' .
                                                      $objCertificado->getSerialNumber();
                    }
                    else
                    {
                        $strNombreCarpetaDocumentos = str_replace(' ', '_', strtoupper($objPersonaContrato->nombres)) . '_' .
                                                      str_replace(' ', '_', strtoupper($objPersonaContrato->apellidos)) . '_' .
                                                      $objPersonaContrato->identificacionCliente . '_' . $objCertificado->getSerialNumber();
                    }

                    $serviceContratoDigital->transferirDocumentosContratoDigital(array(
                                                 'intIdContrato'     => $objContrato->getId(),
                                                 'strNumeroAdendum'  => $arrayData['numeroAdendum'],
                                                 'strIdentificacion' => $objPersonaContrato->identificacionCliente,
                                                 'strNombreCarpeta'  => $strNombreCarpetaDocumentos,
                                                 'strCodEmpresa'     => $arrayData['codEmpresa'],
                                                 'bandNfs'           => $boolNFS,
                                                 'strUsrCreacion'    => $arrayData['user']));

                    $arrayResponse['status']  = $this->status['OK'];
                    $arrayResponse['success'] = true ;

                    $arrayResponse = $serviceComercialMobile->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                                               "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                                               "strIdentificacion" => $objPersonaContrato->identificacionCliente,
                                                                               "arrayServicios"    => $arrayServicios));

                    $arrayResponse['message'] = $arrayData['strTipo'] == "C" ? "contrato " : "adendum ";
                    $arrayResponse['message'] = "Su " . $arrayResponse['message'] . "ha sido Firmado Digitalmente";
                }
                else
                {
                    
                    $arrayResponse['status']                  = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['message']                 = "Numero de contrato invalido.";
                }
            }
        }
        catch (\Exception $e)
        {

            if ($e->getMessage() == 'ERROR_PARCIAL')
            {
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['message'] = $strMensaje;
                
            }
            else
            {
                $strMensaje               = "ERROR";
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['message'] = $strMensaje;
            }
            
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $strMensaje;
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData);
            $arrayParametrosLog['creationUser']     = $strUsrCreacion;  
                
            $serviceUtil->insertLog($arrayParametrosLog);              

        }
        return $arrayResponse;
    }

/**
     * Método que genera un pin de seguridad para la autorización de un contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 02-04-2019
     * 
     * Se modifica la lógica para realizar generación y envío de pin por separado.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.1 03/12/2019
     */        
    private function getPinSecurity($arrayData)
    {
        $arrayResponse    = array();
        $strMensaje       = "";
        $serviceSeguridad = $this->get('seguridad.Seguridad');
        $serviceUtil      = $this->get('schema.Util');        
        /* @var $serviceSeguridad SeguridadService */
        try
        {
            // Generacion del PIN Security
            $arrayGeneracionPin                  = array ();
            $arrayGeneracionPin['strUsername']   = $arrayData['user'];
            $arrayGeneracionPin['strCodEmpresa'] = $arrayData['codEmpresa'];
            $arrayResponseService = $serviceSeguridad->generarPinSecurity($arrayGeneracionPin);
            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE')
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            }
            if (isset($arrayResponseService['pin']))
            {
                $arrayResponse['response']['pin']    = $arrayResponseService['pin'];
                //Preparo la data para el envío del pin
                $arrayDataEnvio                      = array();
                $arrayDataEnvio['strPin']            = $arrayResponseService['pin'];
                $arrayDataEnvio['strNumeroTlf']      = $arrayData['numero'];
                $arrayDataEnvio['strIdentificacion'] = $arrayData['usuario'];
                $arrayDataEnvio['strPersonaId']      = $arrayData['idcliente'];
                $arrayDataEnvio['strUsername']       = $arrayData['user'];
                $arrayDataEnvio['strCodEmpresa']     = $arrayData['codEmpresa'];
                //Realizo el envío del pin
                $arrayResponseEnvioPin               = $serviceSeguridad->enviarPinSecurity($arrayDataEnvio);
                $arrayResponse['status']             = $arrayResponseEnvioPin['status'];
                if (isset($arrayResponseEnvioPin['mensaje']))
                {
                    $arrayResponse['message']        = $arrayResponseEnvioPin['mensaje'];
                }
            }
        }
        catch (\Exception $e)
        {
            if ($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                $arrayResponse['message']    = $strMensaje;
            }
            else
            {
                $arrayResponse['status']     = $this->status['ERROR'];
                $arrayResponse['message']    = $this->mensaje['ERROR'];
                $strMensaje = $e->getMessage();
            }
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Error";
            $arrayParametrosLog['descriptionError'] = $strMensaje;
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];  
            $serviceUtil->insertLog($arrayParametrosLog);
            return $arrayResponse;
        }
        $arrayResponse['success'] = true ;
        return $arrayResponse;
    }    

    /**
     * Método que realiza la creación de un pre-cliente
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 05-07-2019 Se migra funcionalidad existente a nuevo WS. Se agrega registro de logs.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 18-06-2020 - Implementación de persona jurídica
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.3 23-09-2022 - Se agrega el nodo message
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se eliminó consumo de este método, se implementó creación de prospecto desde ms 
     * @deprecated
     *
     */   
    public function putPreCliente($arrayData)
    { 
        $serviceUtil            = $this->get('schema.Util');
        $serviceComercialMobile = $this->get('comercial.ComercialMobile');
        $strClientIp            = '127.0.0.1';
        $strMensajeRepLeg       = '';
        /* @var $arrayDatosPersona PersonaComplexType */
        try
        {
            $arrayParametros = array("strCodEmpresa"      => $arrayData['codEmpresa'],
                                     "strIdentificacion"  => $arrayData['persona']['identificacionCliente'],
                                     "strPrefijoEmpresa"  => $arrayData['prefijoEmpresa']);
            
            $objPersona = $serviceComercialMobile->obtenerDatosPersonaPrv($arrayParametros);

            if (!is_null($objPersona) && 
                (in_array('Pre-cliente', $objPersona->roles) || in_array('Cliente', $objPersona->roles)) )
            {
                // si la persona existe como pre-cliente, devolver error
                $strMensaje = 'Persona ya existe como Pre-Cliente o Cliente';
                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "INFO";
                $arrayParametrosLog['status']           = "Info";
                $arrayParametrosLog['descriptionError'] = $strMensaje;
                $arrayParametrosLog['inParameters']     = json_encode($arrayData);
                $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];    

                $serviceUtil->insertLog($arrayParametrosLog);

                $arrayResponse['response']  = new CrearPreClienteResponseNew($objPersona, $strMensaje);
                $arrayResponse['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResponse['message']   = $strMensaje;
                $arrayResponse['mensaje']   = $strMensaje;
                $arrayResponse['success']   = true;
                $arrayResponse['token']     = null ;
            }
            else
            {
                $objPersona = new InfoPersona();

                if ($arrayData['persona']['fechaNacimiento'])
                {
                    $arrayData['persona']['fechaNacimiento'] = \DateTime::createFromFormat('d/m/Y H:i:s', 
                                                                                           $arrayData['persona']['fechaNacimiento'] . ' 00:00:00');
                }
                $arrayData['persona']['yaexiste'] = (empty($arrayData['persona']['id']) ? 'N' : 'S');

         
                $arrayParametrosPreCliente =   array('strCodEmpresa'        => $arrayData['codEmpresa'],
                                                     'intOficinaId'         => $arrayData['idOficina'],
                                                     'strUsrCreacion'       => $arrayData['usrCreacion'],
                                                     'strClientIp'          => $strClientIp,
                                                     'arrayDatosForm'       => $arrayData['persona'],
                                                     'strPrefijoEmpresa'    => $arrayData['prefijoEmpresa'],
                                                     'arrayFormasContacto'  => $arrayData['persona']['formasContacto']);



                $objRequest  = $this->get('request');
                $strIpCreacion          = $objRequest->getClientIp()? $objRequest->getClientIp() : '127.0.0.1';
                $arrayPersona= $arrayData['persona']; 
                $arrayPersonaJuridica= $arrayData['persona']['representanteLegalJuridico'];

                if($arrayPersona['fechaNacimiento']!=null)
                {
                    $strFechaNacimiento = strval(date_format($arrayPersona['fechaNacimiento'], "Y-m-d"));           
                    $arrayFechasNacimientos = explode("-",$strFechaNacimiento);
                }

                $arrayDatosForm=array(
                    'identificacionCliente'         =>  $arrayPersona['identificacionCliente'],
                    'origenIngresos'                =>  null,
                    'nacionalidad'                  =>  $arrayPersona['nacionalidad'],
                    'nombres'                       =>  $arrayPersona['nombres']?$arrayPersona['nombres']:'',
                    'apellidos'                     =>  $arrayPersona['apellidos']?$arrayPersona['apellidos']:'',
                    'razonSocial'                   =>  $arrayPersona['razonSocial']?$arrayPersona['razonSocial']:null,
                    'representanteLegal'            =>  $arrayPersonaJuridica['nombres']?
                                                        $arrayPersonaJuridica['nombres'].' '.$arrayPersonaJuridica['apellidos']:null,
                    'estadoCivil'                   =>  $arrayPersona['estadoCivil'],
                    'fechaNacimiento'               =>   array(
                                                            "month" =>$arrayFechasNacimientos!=null? $arrayFechasNacimientos[1]:null,
                                                            "day"  => $arrayFechasNacimientos!=null? $arrayFechasNacimientos[2]:null,
                                                            "year" => $arrayFechasNacimientos!=null? $arrayFechasNacimientos[0]:null, ),
                    'referido'                      =>  null,
                    'idreferido'                    =>  null,
                    'idperreferido'                 =>  null,
                    'id'                            =>  $arrayData['persona']['id'] ? $arrayData['persona']['id']: '',
                    'yaexiste'                      =>  $arrayPersona['yaexiste'], 
                    'formaPagoId'                   =>  $arrayPersona['formaPagoId'], 
                    'tipoCuentaId'                  =>  $arrayPersona['tipoCuentaId'], 
                    'bancoTipoCuentaId'             =>  $arrayPersona['bancoTipoCuentaId'],  
                    'tipoEmpresa'                   =>  $arrayPersona['tipoEmpresa']?$arrayPersona['tipoEmpresa']:null,   
                    'direccionTributaria'           =>  $arrayPersona['direccionTributaria']?$arrayPersona['direccionTributaria']:null,   
                    'contribuyenteEspecial'         =>  null,   
                    'pagaIva'                       =>  null, 
                    'numeroConadis'                 =>  null, 
                    'tituloId'                      =>  $arrayPersona['tituloId'],   
                    'tipoIdentificacion'            =>  $arrayPersona['tipoIdentificacion'],   
                    'genero'                        =>  $arrayPersona['genero'],   
                    'tipoTributario'                =>  $arrayPersona['tipoTributario']?$arrayPersona['tipoTributario']:null,    
                    'idOficinaFacturacion'          =>  null,    
                    'esPrepago'                     =>  null  
                                       
                );

                 
                $serviceTokenCas = $this->get('seguridad.TokenCas');
                $arrayTokenCas = $serviceTokenCas->generarTokenCas(); 
 
            
                $arrayParametrosPreCliente  =  array(  
                    'token'                => $arrayTokenCas['strToken'],                  
                    'codEmpresa'           => $arrayData['codEmpresa'],
                    'oficinaId'            => $arrayData['idOficina'],
                    'usrCreacion'          => $arrayData['usrCreacion'],
                    'clientIp'             => $strIpCreacion,
                    'origenWeb'            =>  'M',
                    'idPais'               => 1 ,
                    'datosForm'            => $arrayDatosForm,
                    'prefijoEmpresa'       => $arrayData['prefijoEmpresa'], 
                    'formaContacto'        => $arrayPersona['formasContacto'],
                    'strRecomendacionTarjeta' => $arrayData['recomendacionTarjeta'],
                    'representanteLegal'   => []
                ); 

                /* @var $servicePreClienteMs \telconet\comercialBundle\Service\PreClienteMsService */ 
                $servicePreClienteMs = $this->get('comercial.PreClienteMs');
                $arrayResultado      =  $servicePreClienteMs->wsCrearProspecto($arrayParametrosPreCliente);

                if($arrayResultado['strStatus'] == "OK")
                {

                   $emComercial = $this->getDoctrine()->getManager('telconet');
                   $arrayEstado= array('Pendiente', 'Activo'); 
                   $arrayRol= array('Cliente', 'Pre-cliente'); 
                   $strEmpresa=  $arrayData['codEmpresa']; 
                   $arrayInfoPerEmpRolCli = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol');  
                   $arrayInfoPerEmpRolCli = $arrayInfoPerEmpRolCli->
                   buscaClientesPorIdentificacionTipoRolEmpresaEstados($arrayPersona['identificacionCliente'],
                                                                  $arrayRol,
                                                                  $strEmpresa,
                                                                  $arrayEstado
                                                                  );
 
                   $objPersonaEmpresaRol = $arrayInfoPerEmpRolCli[0];

                    if(is_object($objPersonaEmpresaRol))
                    {
                        $strIdentificacion   = $objPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente();
    
                        if ($arrayData['codEmpresa'] == '18' && $arrayData['persona']['tipoTributario'] == 'JUR')
                        {
    
                          
                            $arrayData['persona']['objPersonaEmpresaRol'] = $objPersonaEmpresaRol;
                            $arrayResultadoRepLeg = $serviceComercialMobile->createRepresentanteLegalPersonaJuridica($arrayData);
    
                            if(!$arrayResultadoRepLeg['success'])
                            {
                                $strMensajeRepLeg = $arrayResultadoRepLeg['message'];
                            }
                        }
                    } 


                    $arrayResultado  = $serviceComercialMobile->getPersona(array("strCodEmpresa"         => $arrayData['codEmpresa'],
                                                                             "strPrefijoEmpresa"     => $arrayData['prefijoEmpresa'],
                                                                             "strIdentificacion"     => $strIdentificacion,
                                                                             "strTipoIdentificacion" => $arrayData['persona']['tipoIdentificacion'],
                                                                             "user"                  => $arrayData['usrCreacion'],
                                                                             "nuevo"                 => "S"));


                    $objPersona                = $arrayResultado['response']->persona;
                    $arrayPlanes               = $arrayResultado['response']->planes;
                    $arrayResponse['response'] = array('persona'=> $objPersona, 'planes' => $arrayPlanes);                
                    $arrayResponse['status']   = $this->status['OK'];
                    $arrayResponse['message']  = 'Prospecto ingresado correctamente. ' . $strMensajeRepLeg;
                    $arrayResponse['mensaje']  = 'Prospecto ingresado correctamente. ' . $strMensajeRepLeg;
                    $arrayResponse['success']  = true;




                }else
                {
                    $arrayResponse['status']   = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['message']  = $arrayResultado['strMensaje'];
                    $arrayResponse['mensaje']  = $arrayResultado['strMensaje'];
                    $arrayResponse['success']  = true;
                }
                
            }
        }
        catch (\Exception $e)
        {        

            error_log("mori por aqui");
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];  
            $serviceUtil->insertLog($arrayParametrosLog);    
            $arrayResponse['status']                = $this->status['ERROR'];
            if( get_class($e) === 'Exception')
            {
                $arrayResponse['message'] = $e->getMessage();
                $arrayResponse['mensaje'] = $e->getMessage();
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
            }
            else
            {
                $arrayResponse['message'] = $this->mensaje['ERROR'];
                $arrayResponse['mensaje'] = $this->mensaje['ERROR'];
                $arrayResponse['status']  = $this->status['ERROR'];
            }
            
            $arrayResponse['success']               = true ;
            
            return $arrayResponse;
        }        
        return $arrayResponse;
    }
    
    /**
     * Método que obtiene el catálogo de extranet
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 15-03-2021
     * 
     */   
    public function getCatalogoExtranet($arrayData)
    {
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceSoporte     = $this->get('soporte.SoporteService');

        $arrayResponse      = array();
        $arrayRespuestaTipo = array();
        $strIdKey           = "id";
        $strNombreCanton    = "canton";
        $strNombreParroquia = "parroquia";
        $strNombreSector    = "sector";
        $strEstadoActivo    = "Activo";
        $strSolicitud       = "SOLICITUD EDIFICACION";
        $strTipo            = $arrayData['data']['tipo'];
        $strIdEmpresa       = $arrayData['data']['idEmpresa'];
        $intIdPuntoCobertura= $arrayData['data']['idPuntoCobertura'];
        $intIdCanton        = $arrayData['data']['idCanton'];
        $intIdParroquia     = $arrayData['data']['idParroquia'];

        $arrayParametros = array('JURISDICCIONID'   => '',
                                 'CANTONID'         => '',
                                 'PARROQUIAID'      => '',
                                 'NOMBRE'           => null,
                                 'EMPRESA'          => $strIdEmpresa,
                                 'ESTADO'           => $strEstadoActivo,
                                 'VALUE'            => $strIdKey,
                                 'DISPLAY'          => '');

        try
        {
            if (empty($strIdEmpresa))
            {
                throw new \Exception("Falta la variable idEmpresa en el request");
            }

            if (empty($strTipo))
            {
                throw new \Exception("Falta la variable tipo en el request");
            }

            if ($strTipo == 'CA' && empty($intIdPuntoCobertura))
            {
                throw new \Exception("Falta la variable idPuntoCobertura en el request");
            }

            if ($strTipo == 'PA' && empty($intIdCanton))
            {
                throw new \Exception("Falta la variable idCanton en el request");
            }

            if ($strTipo == 'SE' && empty($intIdParroquia))
            {
                throw new \Exception("Falta la variable idCanton en el request");
            }

            if ($strTipo == 'EDIF' && empty($intIdCanton))
            {
                throw new \Exception("Falta la variable idCanton en el request");
            }
            
            if($strTipo == 'PC')
            {
                $arrayJurisdiccion = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                                       ->getResultadoJurisdiccionesPorEmpresa($strIdEmpresa);

                foreach ($arrayJurisdiccion as $objJurisdiccion)
                {
                    array_push($arrayRespuestaTipo,
                                    array(
                                        'id'                => $objJurisdiccion->getId(),
                                        'puntoCobertura'    => $objJurisdiccion->getNombreJurisdiccion()
                                        )
                                );
                }           
            }
            else if($strTipo == 'CA')
            {
                $arrayParametros['JURISDICCIONID'] = $intIdPuntoCobertura;
                $arrayParametros['DISPLAY']        = $strNombreCanton;
                
                $arrayCanton = $emInfraestructura->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                                                 ->getCantonesJurisdicciones($arrayParametros);

                $arrayRespuestaTipo = $arrayCanton;

            }
            else if($strTipo == 'PA')
            {
                $arrayParametros['CANTONID']       = $intIdCanton;
                $arrayParametros['DISPLAY']        = $strNombreParroquia;
                
                $arrayParroquia = $emInfraestructura->getRepository('schemaBundle:AdmiParroquia')
                                                    ->getParroquiasPorCantonPorNombre($arrayParametros);

                $arrayRespuestaTipo = $arrayParroquia;

            }
            else if($strTipo == 'SE')
            {
                $arrayParametros['PARROQUIAID']    = $intIdParroquia;
                $arrayParametros['DISPLAY']        = $strNombreSector;
                
                $arraySector = $emInfraestructura->getRepository('schemaBundle:AdmiSector')
                                                    ->getSectoresPorParroquiaPorNombre($arrayParametros);

                $arrayRespuestaTipo = $arraySector;
            }
            else if($strTipo == 'EDIF')
            {
                 // puntoEdificio
                $objSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneBy(array('descripcionSolicitud' => $strSolicitud,
                                                              'estado'               => $strEstadoActivo));

                $arrayParametros = array();
                $arrayParametros["idSolicitud"]     = $objSolicitud->getId();
                $arrayParametros["codEmpresa"]      = $strIdEmpresa;
                $arrayParametros["idCanton"]        = $intIdPuntoCobertura;
                $arrayParametros["estadoElemento"]  = $strEstadoActivo;
                $arrayParametros['start']           = 0;
                $arrayParametros['limit']           = 100000;
                $arrayParametros["nombreNodo"]      = "";
                $arrayParametros["modeloElemento"]  = "";
                $arrayParametros["direccion"]       = "";

                $arrayEdificios     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->getRegistrosEdificacion($arrayParametros);
 
                $arrayRespuestaTipo = $arrayEdificios['registros'];
            }
            else if($strTipo == 'TN')
            {
                $arrayTiposNegocio = $emComercial->getRepository('schemaBundle:AdmiTipoNegocio')
                                                ->findBy(
                                                    array("estado"      => $strEstadoActivo,
                                                          "empresaCod"  => $strIdEmpresa)
                                                );

                foreach ($arrayTiposNegocio as $objTipoNegocio)
                {
                    array_push($arrayRespuestaTipo,
                                    array(
                                        'id'             => $objTipoNegocio->getId(),
                                        'codigo'         => $objTipoNegocio->getCodigoTipoNegocio(),
                                        'tipoNegocio'    => $objTipoNegocio->getNombreTipoNegocio()
                                        )
                                );
                }  
            }
            else if($strTipo == 'TU')
            {
                $arrayUbicacion = $emComercial->getRepository('schemaBundle:AdmiTipoUbicacion')
                                              ->findBy(
                                                  array('estado' => $strEstadoActivo)
                                                );

                foreach ($arrayUbicacion as $objUbicacion)
                {
                    array_push($arrayRespuestaTipo,
                                    array(
                                        'id'             => $objUbicacion->getId(),
                                        'codigo'         => $objUbicacion->getCodigoTipoUbicacion(),
                                        'tipoUbicacion'  => $objUbicacion->getDescripcionTipoUbicacion()
                                        )
                                );
                }  
            }
            else if($strTipo == 'FC')
            {
                
                $arrayFormasContactos = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                    ->findBy(array("estado"  => $strEstadoActivo));
                
                foreach ($arrayFormasContactos as $objFormaContacto)
                {
                    array_push($arrayRespuestaTipo,
                                    array(
                                        'id'             => $objFormaContacto->getId(),
                                        'FormaContacto'  => $objFormaContacto->getDescripcionFormaContacto()
                                        )
                                );
                }  
                
            }
            else
            {
                throw new \Exception("No existe el tipo");
            } 
            
            $arrayResponse['response'] = $arrayRespuestaTipo; 
            $arrayResponse['message']  = "Proceso Realizado";
            $arrayResponse['status']   = $this->status['OK'];
        }
        catch (\Exception $e)
        {
            $arrayResponse['message'] = $e->getMessage();
            $arrayResponse['status']  = $this->status['ERROR'];  

            $arrayParametrosEnvio =   array('idEmpresa'          => "18",
                                            'strUsrCreacion'     => "telcos",
                                            'strIpCreacion'      => "127.0.0.1",
                                            'strError'           => $e->getMessage(),
                                            'prefijoEmpresa'     => "MD",
                                            'strCorreoCliente'   => "N",
                                            'strTipoCorreo'      => "CATALOGO",
                                            'strAsuntoCorreo'    => "Error al consultar ubicaciónpara traslados"
                                        );
            
            $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
        }
        return $arrayResponse;
    }
    
    /**
     * Método que realiza la creación de un punto
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-07-2019 Se invoca a función getPersona para setear objeto response. Se agrega registro de logs.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 12-06-2020 Se procede a consumir el ms-core-gen-nfs para almacenar los archivos en el NFS remoto.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.3 12-03-2021 se procede a agregar proceso para el canal de extranet.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 01-02-2022 Se adiciona el nodo de solicitar información del cliente.
     */   
    public function putPunto($arrayData)
    {
        $strClientIp             = '127.0.0.1';
        $serviceComercialMobile  = $this->get('comercial.ComercialMobile');
        $serviceUtil             = $this->get('schema.Util');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto        = $this->get('comercial.InfoPunto');
        $serviceSoporte          = $this->get('soporte.SoporteService');
        $emComercial             = $this->getDoctrine()->getManager();
        $objServicioRepository   = $emComercial->getRepository('schemaBundle:InfoServicio');
        $boolNFS                 = false;
        $strIdentificacionCliente= "";
        try
        {
            $arrayData['punto']['fileBase64']  = $arrayData['punto']['file'];
            $arrayData['punto']['file']        = (empty($arrayData['punto']['file']) ? null : $this->writeAndGetFile($arrayData['punto']['file']));
            $arrayData['punto']['fileDigital'] = (empty($arrayData['punto']['fileDigital']) ? null : 
                                                          $this->writeAndGetFile($arrayData['punto']['fileDigital']));

            $arrayParametrosDet                 = $this->getManager()
                                                       ->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('PARAMETROS_ESTRUCTURA_RUTAS_TELCOS',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 isset($arrayData['strAplicacion']) ? $arrayData['strAplicacion'] : 'TELCOS',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '');
                if(!empty($arrayParametrosDet))
                {
                   $arrayData['punto']['strApp']  = $arrayParametrosDet['valor2'];
                }

            //se agrega codigo para validar informacion y poder crear el punto de manera correcta
            if ($arrayData['punto']['nombrePunto'])
            {
                $arrayData['punto']['nombrepunto'] = $arrayData['punto']['nombrePunto'];
            }
            if ($arrayData['punto']['descripcionPunto'])
            {
                $arrayData['punto']['descripcionpunto'] = $arrayData['punto']['descripcionPunto'];
            }
            if ($arrayData['punto']['dependeDeEdificio'])
            {
                $arrayData['punto']['dependedeedificio'] = $arrayData['punto']['dependeDeEdificio'];
            }
            if ($arrayData['punto']['puntoEdificioId'])
            {
                $arrayData['punto']['puntoedificioid'] = $arrayData['punto']['puntoEdificioId'];
            }
            if ($arrayData['punto']['esEdificio'])
            {
                $arrayData['punto']['esedificio'] = $arrayData['punto']['esEdificio'];
            }
            if ($arrayData['punto']['idCanalVenta'])
            {
                $arrayData['punto']['canal'] = $arrayData['punto']['idCanalVenta'];
            }
            if ($arrayData['punto']['idPtoVenta'])
            {
                $arrayData['punto']['punto_venta'] = $arrayData['punto']['idPtoVenta'];
            }
            if ($arrayData['idOficina'])
            {
                $arrayData['punto']['oficina'] = $arrayData['idOficina'];
            }
            if ($arrayData['punto']['origenWeb'])
            {

                $arrayData['punto']['origen_web'] = "WEB" ? "S" : "N";
            }
            if ($arrayData['prefijoEmpresa'])
            {
                $arrayData['punto']['prefijoEmpresa'] = isset($arrayData['prefijoEmpresa']) ? $arrayData['prefijoEmpresa'] : "MD";
            }
            if ($arrayData['punto']['requireInfoCliente'])
            {
                $arrayData['punto']['strSolInfCli'] = $arrayData['punto']['requireInfoCliente'];
            }
            
            $arrayParametrosDetNFS        = $this->getManager()
                                                 ->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('BANDERA_NFS',
                                                          '',
                                                          '',
                                                          '',
                                                          'S',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '');
            if(isset($arrayParametrosDetNFS) && $arrayParametrosDetNFS['valor1'] === 'S')
            {
                $boolNFS = true;
            }
            
            
            //generar login de manera automatica para el canal de extranet
            if($arrayData['punto']['idCanalVenta']=="CANAL_EXTRANET")
            {
                
                //validar varibles del request
            
                  if(empty($arrayData['usrCreacion']))
                  {
                     throw new \Exception("Falta la variable usrCreacion en el request");
                  }
            
                  if(empty($arrayData['prefijoEmpresa']))
                  {
                     throw new \Exception("Falta la variable prefijoEmpresa en el request");
                  }
                  
                  if(empty($arrayData['codEmpresa']))
                  {
                     throw new \Exception("Falta la variable codEmpresa en el request");
                  }
            
            
                  if (empty($arrayData['punto']['direccion']))
                  {
                     throw new \Exception("Falta la variable direccion en el request");
                  }
            
                  if (empty($arrayData['punto']['tipoNegocioId']))
                  {
                    throw new \Exception("Falta la variable direccion en el request");
                  }
            
                  if(count($arrayData['punto']['formasContacto'])==0 || count($arrayData['punto']['formasContacto'])==1 )
                  {
                      throw new \Exception("Debe agregar dos formas de contacto");
                  }
            
                  if (empty($arrayData['punto']['ptoCoberturaId']))
                  {
                      throw new \Exception("Falta la variable ptoCoberturaId en el request");
                  }
            
                  if (empty($arrayData['punto']['latitudFloat']))
                  {
                      throw new \Exception("Falta la variable latitudFloat en el request");
                  }
            
                  if (empty($arrayData['punto']['longitudFloat']))
                  {
                      throw new \Exception("Falta la variable longitudFloat en el request");
                  }
            
                  if (empty($arrayData['punto']['cantonId']))
                  {
                      throw new \Exception("Falta la variable cantonId en el request");
                  }
            
                  if (empty($arrayData['punto']['parroquiaId']))
                  {
                      throw new \Exception("Falta la variable parroquiaId en el request");
                  }
            
                  if (empty($arrayData['punto']['identificacionCliente']))
                  {
                     throw new \Exception("Falta la variable identificacionCliente en el request");
                  }
                
                  if (empty($arrayData['punto']['descripcionpunto']))
                  {
                     throw new \Exception("Falta la variable descripcionpunto en el request");
                  }
            
                  if (empty($arrayData['punto']['idPtoVenta']))
                  {
                     throw new \Exception("Falta la variable idPtoVenta en el request");
                  }
            
                  if (empty($arrayData['punto']['sectorId']))
                  {
                     throw new \Exception("Falta la variable sectorId en el request");
                  }
            
                  if (empty($arrayData['punto']['tipoUbicacionId']))
                  {
                     throw new \Exception("Falta la variable tipoUbicacionId en el request");
                  }
            
                  if (empty($arrayData['punto']['rol']))
                  {
                     throw new \Exception("Falta la variable rol en el request");
                  }
            
                  if(empty($arrayData['punto']['loginVendedor']))
                  {
                     throw new \Exception("Falta la variable loginVendedor en el request");
                  }
                
                  if (empty($arrayData['punto']['esPadreFacturacion']))
                  {
                     throw new \Exception("Falta la variable esPadreFacturacion en el request");
                  }
            
                  if (empty($arrayData['punto']['dependedeedificio']))
                  {
                     throw new \Exception("Falta la variable dependedeedificio en el request");
                  }
                  
                  
                $fltLatitud  = str_replace(",", ".", $arrayData['punto']['latitudFloat']);
                $fltLongitud = str_replace(",", ".", $arrayData['punto']['longitudFloat']);
                  
                
                $arrayData['punto']['latitudFloat']  = $fltLatitud;
                $arrayData['punto']['longitudFloat'] = $fltLongitud;
                
                 //obtener idOficina del cliente
                  
                $entityAdmiJurisdiccion  = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
                                                       ->findOneById($arrayData['punto']['ptoCoberturaId']);
                  
                $arrayData['punto']['oficina'] = $entityAdmiJurisdiccion->getOficinaId();
                
                 //obtener idPersona del cliente
                
                $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                 ->findOneBy(array('identificacionCliente'  => $arrayData['punto']['identificacionCliente']));  
                 
                $arrayData['punto']['personaId'] = $entityInfoPersona->getId();
                
                $strLoginCliente  = $serviceInfoPunto->generarLogin($arrayData['codEmpresa'],
                                                                    $arrayData['punto']['cantonId'],
                                                                    $arrayData['punto']['personaId'],
                                                                    $arrayData['punto']['tipoNegocioId']);
                
                if(empty($strLoginCliente))
                {
                    throw new \Exception('Error al generar login de manera automatica por'
                                          . 'favor comunicarse con sistemas');
                }
                else
                {
                    $arrayData['punto']['login'] = $strLoginCliente;
                }
                
                
            }
            
            $arrayParametrosPunto =  array('strCodEmpresa'        => $arrayData['codEmpresa'],
                                           'strUsrCreacion'       => $arrayData['usrCreacion'],
                                           'strClientIp'          => $strClientIp,
                                           'bandNfs'              => $boolNFS,
                                           'arrayDatosForm'       => $arrayData['punto'],
                                           'arrayFormasContacto'  => $arrayData['punto']['formasContacto'],
                                           'strCanal'             => $arrayData['punto']['idCanalVenta']);
            
            $objInfoPunto        = $serviceInfoPunto->crearPunto($arrayParametrosPunto);
            
            if($arrayData['punto']['idCanalVenta']=="CANAL_EXTRANET")
            {
                
                if(is_object($objInfoPunto))
                {
                    
                    $strLoginPunto       = $objInfoPunto->getLogin();
                    $intIdPunto          = $objInfoPunto->getId();
                    
                    $arrayResponse['response'] = array('login'=>$strLoginPunto,'idPunto' => $intIdPunto);
                    $arrayResponse['status']   = "OK";
                    $arrayResponse['message']  = 'Punto ingresado correctamente.'; 
                    
                    
                }
                else
                {
                    $arrayResponse['response'] = array();            
                    $arrayResponse['status']   = "ERROR";
                    $arrayResponse['message']  = "Estimado cliente, al momento no podemos procesar"
                                               . " tu solicitud. Intenta más tarde";
                    
                    $strIdentificacionCliente = $arrayData['punto']['identificacionCliente'];
                    $strMensaje   =  $objInfoPunto["strMensaje"];
                    
                    $serviceUtil->insertError('Telcos+',
                                              'ComercialMobileWsControllerRest->putPunto',
                                              $strMensaje,
                                              $arrayData["usrCreacion"],
                                              $strClientIp);
                    
                    $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Error al crear punto de destino",
                                                    'strLoginOrigen'     => "",
                                                    'strIdentificacion'  => $strIdentificacionCliente,
                                                    'strTipoCorreo'      => "PUNTO",
                                                    'idEmpresa'          => $arrayData['codEmpresa'],
                                                    'strUsrCreacion'     => $arrayData['usrCreacion'],
                                                    'strIpCreacion'      => $strClientIp,
                                                    'strError'           => $strMensaje,
                                                    'prefijoEmpresa'     => $arrayData['prefijoEmpresa'],
                                                    'strCorreoCliente'   => "N");
                
                
                    $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
                    
                    
                }
                
                
            }
            else
            {
               if(is_object($objInfoPunto))
               {
                  $strIdentificacion         = $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
               
                  $arrayResultado            = $serviceComercialMobile->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                                                       "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                                                       "strIdentificacion" => $strIdentificacion,
                                                                                       "user"              => $arrayData['usrCreacion']));
                  $objPersona                = $arrayResultado['response']->persona;
                  $arrayPlanes               = $arrayResultado['response']->planes;
                  $arrayResponse['response'] = array('persona'=> $objPersona, 'planes' => $arrayPlanes);
                  $arrayResponse['status']   = $this->status['OK'];
                  $arrayResponse['message']  = 'Punto ingresado correctamente.';             
               }
               else
               {
                  $arrayResponse['response'] = array();            
                  $arrayResponse['status']   = $this->status['ERROR'];
                  $arrayResponse['message']  = $this->mensaje['ERROR'];                             
               }           
               $arrayResponse['success']  = true ;
                
                
                
            }

            
        }
        catch (\Exception $e)
        {
            $arrayResponse['response'] = array(); 
            
            if($arrayData['punto']['idCanalVenta']=="CANAL_EXTRANET")
            {
                
                $arrayResponse['status']  = "Error";
                $arrayResponse['message'] = "Estimado cliente, al momento no podemos "
                                          . "procesar tu solicitud. Intenta más tarde”";
                
                $serviceUtil->insertError('Telcos+',
                                          'ComercialMobileWsControllerRest->putPunto',
                                          $e->getMessage(),
                                          $arrayData["usrCreacion"],
                                          $strClientIp);
                
                $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Error al crear punto de destino",
                                                'strLoginOrigen'     => "",
                                                'strIdentificacion'  => $arrayData['punto']['identificacionCliente'],
                                                'strTipoCorreo'      => "PUNTO",
                                                'idEmpresa'          => $arrayData['codEmpresa'],
                                                'strUsrCreacion'     => $arrayData['usrCreacion'],
                                                'strIpCreacion'      => $strClientIp,
                                                'strError'           => $e->getMessage(),
                                                'prefijoEmpresa'     => $arrayData['prefijoEmpresa'],
                                                'strCorreoCliente'   => "N");
                
                
                $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
                
                
            }
            else
            {
                if( get_class($e) === 'Exception')
                {
                   $arrayResponse['message'] = $e->getMessage();
                   $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                }
                else
                {
                   $arrayResponse['message'] = $this->mensaje['ERROR'];
                   $arrayResponse['status']  = $this->status['ERROR'];
                }
                $arrayResponse['success']  = true ;
            
                $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $e->getMessage();
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];  
                $serviceUtil->insertLog($arrayParametrosLog);
            }
                   
        }
        $arrayResponse['mensaje'] =  $arrayResponse['message']; 
        return $arrayResponse;
    }

    /**
     * Método que realiza la creación de un servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 12-07-2019 Se agrega registro de logs.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 25-09-2019 Se agrega origen al Servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 15-01-2020 Se agrega validación temporal para que se actualice a la nueva versión del tm-comercial
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 12-05-2020 bug.- Se agrega el campo login_vendedor para crear el servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 02-05-2020 Se eliminan las caracteristicas que se agregaban de manera predeterminada
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.6 31-10-2022 Se actualiza la validación del correo, según el producto.
     */    
    public function putServicio($arrayData)
    {
        $serviceUtil             = $this->get('schema.Util');
        $serviceComercialMobile  = $this->get('comercial.ComercialMobile');
        $emGeneral = $this->get('doctrine.orm.telconet_general_entity_manager');
        $strClientIp             = '127.0.0.1';
        for ($intI=0; $intI < count($arrayData['servicios']); $intI++) 
        {
            if ($arrayData['servicios'][$intI]['frecuencia'] )
            {
                $arrayData['servicios'][$intI]['frecuencia'] =  strtolower($arrayData['servicios'][$intI]['frecuencia']) == 'mensual' ? "1" : "0";
                $arrayData['servicios'][$intI]['login_vendedor'] = $arrayData['usrCreacion'];
            }

        }
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            if (!$arrayData['origen'])
            {
                throw new \Exception("Para realizar esta acción necesita instalar la nueva versión 1.0.5");
            }
            $arrayParamsServicio = array(   "codEmpresa"        => $arrayData['codEmpresa'],
                                            "idOficina"         => $arrayData['idOficina'],
                                            "entityPunto"       => $arrayData['idPto'],
                                            "entityRol"         => null,
                                            "usrCreacion"       => $arrayData['usrCreacion'],
                                            "clientIp"          => $strClientIp,
                                            "tipoOrden"         => $arrayData['tipoOrden'],
                                            "ultimaMillaId"     => $arrayData['ultimaMillaId'],
                                            "servicios"         => $arrayData['servicios'],
                                            "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                            "session"           => null,
                                            "strOrigen"         => $arrayData['origen']
                                        );    
            
            $objInfoPunto  = $this->getManager()->getRepository('schemaBundle:InfoPunto')->find($arrayData['idPto']);
            if(is_object($objInfoPunto))
            {
                $strIdentificacion = $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
            }
            else
            {
                throw new \Exception("No se encuentra el punto para ingresar Servicio");                
            }
            $arrayValorParametros =  $this->getManager()->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('PARAMETROS_TM_COMERCIAL',
                                                   'COMERCIAL',
                                                   '',
                                                   'ESTADO_SERVICIO',
                                                   '',
                                                   '',
                                                   '',
                                                   '',
                                                   '',
                                                   '');
            $arrayEstadoPlan = ($arrayValorParametros['valor1']) ? $arrayValorParametros['valor1'] : null;
            $arrayEstadoPlan = explode(",", $arrayEstadoPlan);  
            $arrayEstadoProd = ($arrayValorParametros['valor2']) ? $arrayValorParametros['valor2'] : null;
            $arrayEstadoProd = explode(",", $arrayEstadoProd);
            $strMinutos      = $arrayValorParametros['valor3'];                        
            $arrayServicios = array();
            
                        //parametro que devuelve la data de los productos a validar el correo existente.
            foreach ($arrayData['servicios'] as $arrayServicio)
            {
                if ($arrayServicio['info'] == "C")
                {
                    $objServicio = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                      ->findBy(array("puntoId"     => $objInfoPunto->getId(),
                                                                     "productoId"  => $arrayServicio['codigo'],
                                                                     "cantidad"    => $arrayServicio['cantidad'],
                                                                     "estado"      => $arrayEstadoProd));  
                    if ($objServicio)
                    {
                        foreach ($objServicio as $objSer) 
                        {
                            $objFecha = $objSer->getFeCreacion();
                            $objFecha->add(new \DateInterval('PT' . $strMinutos . 'M'));
                            $objAdendum = $this->getManager()
                                               ->getRepository('schemaBundle:InfoAdendum')
                                               ->findOneByServicioId($objSer->getId());
                            if ($objAdendum)
                            {
                                foreach($objAdendum as $objAde)
                                {
                                    if ($objAde->getTipo() == null 
                                        && ($objFecha >=  new \DateTime('now')))
                                    {

                                        throw new \Exception("Este producto ya fue agregado en este punto. 
                                                              Favor volver a consultar la identificación");
                                    }
                                }
                            }
                            else
                            {
                                if ($objFecha >=  new \DateTime('now'))
                                {
                                    throw new \Exception("Este producto ya fue agregado en este punto. 
                                                          Favor volver a consultar la identificación"); 
                                }            
                            }                                

                        }
                    }
                    $arrayServ = array();

                    foreach ($arrayServicio['caracteristicasProducto'] as $arrayCaracteristica )
                    {
                        $objCaracteristica  = $this->getManager()->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" => $arrayCaracteristica['k']));
                        $objCaracProd       = $this->getManager()->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                 ->findOneby( array("productoId"       => $arrayServicio['productoId'],
                                                                                    "caracteristicaId" => $objCaracteristica->getId()) );
                        $arrayServ[] = array('idCaracteristica' => $objCaracProd->getId(),
                                             'caracteristica'   => $arrayCaracteristica['k'],
                                             'valor'            => $arrayCaracteristica['v']
                                             );
                        $arrayParametroValidaCorreo =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('VALIDA_CORREO_EXISTENTE_PROD_STREAMING',//nombre parametro cab
                                                                'COMERCIAL', //modulo cab
                                                                'OBTENER_DATA',//proceso cab
                                                                'PRODUCTO_TV', //descripcion det
                                                                $arrayServicio["nombreTecnico"],
                                                                $arrayCaracteristica['k'],'','','',
                                                                '18'); //empresa

                        if (!empty($arrayParametroValidaCorreo))
                        {
                            $arrayParametros["nombreTecnico"]             = $arrayParametroValidaCorreo['valor1'];
                            $arrayParametros["descripcionCaracteristica"] = $arrayParametroValidaCorreo['valor2'];
                            $arrayParametros["descripcionProducto"]       = $arrayParametroValidaCorreo['valor3'];
                            $arrayParametros["estadoSpc"]                 = $arrayParametroValidaCorreo['valor4'];
                            $arrayParametros["inEstadoServ"]              = explode(",", $arrayParametroValidaCorreo['valor5']);
                            $arrayParametros["valorSpc"]                  = $arrayCaracteristica['v'];

                            $objExiste = $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                                            ->existeCaraceristicaServicio($arrayParametros);

                            if ($objExiste) 
                            {
                                throw new \Exception("El correo electronico ingresado ya fue usado en " .
                                "otra Suscripción del producto ".$arrayParametroValidaCorreo['valor7'].", favor ingresar otro correo.");
                            }
                        }
                    }
                    $arrayServicio['caracteristicasProducto'] = $arrayServ;    
                }
                
                $arrayServicios[] = $arrayServicio;
            }
            
            $arrayParamsServicio['servicios'] = $arrayServicios;
            $serviceInfoServicio->crearServicio($arrayParamsServicio);

            $arrayResultado        = $serviceComercialMobile->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                                               "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                                               "strIdentificacion" => $strIdentificacion,
                                                                               "user"              => $arrayData['usrCreacion']));
            $objPersona                = $arrayResultado['response']->persona;
            $arrayPlanes               = $arrayResultado['response']->planes;
            $arrayResponse['response'] = array('persona'=> $objPersona, 'planes' => $arrayPlanes);          
            $arrayResponse['status']   = $this->status['OK'];
            $arrayResponse['message']  = 'Servicio ingresado correctamente.';
            $arrayResponse['success']  = true;          
        }
        catch (\Exception $e)
        {
            $arrayResponse['response']  = array();
            
            if( get_class($e) === 'Exception')
            {
                $arrayResponse['message'] = $e->getMessage();
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
            }
            else
            {
                $arrayResponse['message'] = $this->mensaje['ERROR'];
                $arrayResponse['status']  = $this->status['ERROR'];
            }
            $arrayResponse['success']   = true ;
            
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];  
            $serviceUtil->insertLog($arrayParametrosLog);            
        }
        return $arrayResponse;
    }
    
    /**
    * Documentación para la función 'getUpdateBackup'.
    *
    * Función encargada de regularizar cambio de planes de Backup para Bg.
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.0 - 09-06-2020
    *
    */
    public function getUpdateBackup()
    {
        $strStatus       = "200";
        $serviceUtil     = $this->get('schema.Util');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        try
        {
            
            $arrayPuntosRegularizar = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                                ->getDatosUpdateBackup();
            foreach ($arrayPuntosRegularizar as $arrayPunto )
            {
                $arrayDatosElemento = $emComercial->getRepository("schemaBundle:InfoServicio")->
                                                                                getDatosElemento(array('loginAux'=>$arrayPunto['login_aux']));
                
                if(is_array($arrayDatosElemento) && !empty($arrayDatosElemento))
                {
                    $objDatoCapacidad1 = $emComercial->getRepository("schemaBundle:InfoServicio")->getCaracteristicaServicio(
                                                                                      array('intIdServicio'=>$arrayDatosElemento[0]['servicio'],
                                                                                            'strDescripcionCaracteristica'=>'CAPACIDAD1'));
                    $objDatoCapacidad2 = $emComercial->getRepository("schemaBundle:InfoServicio")->getCaracteristicaServicio(
                                                                                      array('intIdServicio'=>$arrayDatosElemento[0]['servicio'],
                                                                                            'strDescripcionCaracteristica'=>'CAPACIDAD2'));
                    $arrayPeticionesBwBck = array();
                    $arrayPeticionesBwBck['url'] = 'configBW';
                    $arrayPeticionesBwBck['accion'] = 'reconectar';
                    $arrayPeticionesBwBck['sw'] = $arrayDatosElemento[0]['sw'];
                    $arrayPeticionesBwBck['pto'] = $arrayDatosElemento[0]['puerto'];
                    $arrayPeticionesBwBck['bw_up'] = intval($objDatoCapacidad1->getValor()+$arrayPunto['aumento']);
                    $arrayPeticionesBwBck['bw_down'] = intval($objDatoCapacidad2->getValor()+$arrayPunto['aumento']);
                    $arrayPeticionesBwBck['servicio'] = $arrayDatosElemento[0]['producto'];
                    $arrayPeticionesBwBck['login_aux'] = $arrayPunto['login_aux'];
                    $arrayPeticionesBwBck['user_name'] = 'regularizaBw';
                    $arrayPeticionesBwBck['user_ip'] = '127.0.0.1';

                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaBwBck = $this->container->get('tecnico.NetworkingScripts')->callNetworkingWebService($arrayPeticionesBwBck);

                    if($arrayRespuestaBwBck['status'] != 'OK')
                    {
                        $strStatus = $arrayRespuestaBwBck['status'];
                        $arrayParametrosLog['enterpriseCode']   = '10';
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "TELCOS";
                        $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                        $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
                        $arrayParametrosLog['appMethod']        = "getUpdateBackup";
                        $arrayParametrosLog['appAction']        = "getUpdateBackup";
                        $arrayParametrosLog['messageUser']      = "ERROR";
                        $arrayParametrosLog['status']           = "Error";
                        $arrayParametrosLog['descriptionError'] = $arrayPunto['login_aux'];
                        $arrayParametrosLog['creationUser']     = 'regularizaBw';  
                        $serviceUtil->insertLog($arrayParametrosLog);
                    }
                    else
                    {
                        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($arrayDatosElemento[0]['servicio']);
                        
                        $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objServicio);
                            $objServicioHist->setObservacion('Regularización plan backup bg;'.$objDatoCapacidad1->getValor() .' MB a '. 
                                ($objDatoCapacidad1->getValor()+$arrayPunto['aumento']).' MB');
                            $objServicioHist->setIpCreacion('127.0.0.1');
                            $objServicioHist->setUsrCreacion('regularizaBw');
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setEstado('Activo');
                            $this->getManager()->persist($objServicioHist);
                        
                        $objDatoCapacidad1->setValor($objDatoCapacidad1->getValor()+$arrayPunto['aumento']);
                        $this->getManager()->persist($objDatoCapacidad1);

                        $objDatoCapacidad2->setValor($objDatoCapacidad2->getValor()+$arrayPunto['aumento']);
                        
                        $this->getManager()->persist($objDatoCapacidad2);
                        $this->getManager()->flush(); 
                        
                    }
                }
            }   
            
        }
        catch( \Exception $e )
        {
            $strMensajeError = $e->getMessage();
            $strStatus       = "500";
            $arrayParametrosLog['enterpriseCode']   = '10';
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "getUpdateBackup";
            $arrayParametrosLog['appAction']        = "getUpdateBackup";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Error";
            $arrayParametrosLog['descriptionError'] = $strMensajeError;
            $arrayParametrosLog['creationUser']     = 'regularizaBw';  
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        $arrayRespuesta = array('error'  => $strMensajeError,
                                'status' => $strStatus);
        return $arrayRespuesta;
    }

    /**
    * Método para regularización de contratos y adendums mal generados.
    *
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 - 07-07-2020
    *
    */
    public function putRegularizaContratosAdendums($arrayData)
    {
        //Para probar en desarrollo

        $arrayData['strArchivo'] = __DIR__."/../../../../web/public/instructivos/soporte/" . $arrayData["archivo"];
        //Para probar en desarrollo

        ini_set('max_execution_time', 400000);
        $strMensaje             = "";
        $arrayResponse          = array();
        $strUsrCreacion         = 'TELCOS';
        $arrayServicios         = $arrayData['servicios'];
        $serviceUtil            = $this->get('schema.Util');
        $serviceSeguridad       = $this->get('seguridad.Seguridad');
        $objServiceCrypt        = $this->get('seguridad.crypt');
        $serviceContratoAprob   = $this->get('comercial.InfoContratoAprob');
        $serviceContrato        = $this->get('comercial.InfoContrato');
        $serviceComercialMobile = $this->get('comercial.ComercialMobile');
        $serviceContratoDigital = $this->get('comercial.InfoContratoDigital');
        $serviceRegularizaCYA   = $this->get('comercial.RegularizaContratosAdendums');

        $strArchivo         = $arrayData['strArchivo'];
        $arrayData['contrato'] = "";

        $objInputFileType   = PHPExcel_IOFactory::identify($strArchivo);
        $objReader          = PHPExcel_IOFactory::createReader($objInputFileType);
        $objPHPExcel        = $objReader->load($strArchivo);
        $objSheet           = $objPHPExcel->getSheet(0);
        $intHighestRow      = $objSheet->getHighestRow();
        $objHighestColumn   = $objSheet->getHighestColumn();

        for ($intRow = 2; $intRow <= $intHighestRow; $intRow++)
        {
            try
            {

                $arrayData['usuario'] = $objSheet->getCell("A".$intRow)->getValue();
                $arrayData['strTipo'] = $objSheet->getCell("D".$intRow)->getValue() == "Contrato" ? "C" :
                                        $objSheet->getCell("D".$intRow)->getValue() == "Punto Adicional" ? "AP" : "AS";



                $arrayData['strNumeroContrato'] = $objSheet->getCell("E".$intRow)->getValue();
                $arrayData['strNumeroAdendum'] = $objSheet->getCell("F".$intRow)->getValue();
                $arrayData['regulariza'] = "S";
                error_log("cliente: " . $arrayData['usuario'] . " contrato: " .  $arrayData['strNumeroContrato']);
                //Obtener id_contrato a regularizar
                $objContrato = $this->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoContrato')
                                    ->findOneBy(array(
                                                    "numeroContrato"  => $arrayData['strNumeroContrato']
                                                ));
                if ($objContrato)
                {
                    //valido que el contrato o adendum no sea por regularizacion o web
                    if ($arrayData['srtTipo'] == 'C' && $objContrato->getOrigen() == 'WEB')
                    {
                        continue;
                    }
                    if ($arrayData['strTipo'] != 'C')
                    {
                        $objAdendum = $this->getManager("telconet")->getRepository('schemaBundle:InfoAdendum')
                                                        ->findOneBy(array('numero'     => $arrayData['strNumeroAdendum']),
                                                                    array('feCreacion' => 'DESC'));
                        if ($objAdendum)
                        {
                            if ($objAdendum->getUsrCreacion() == 'TELCOS_MIGRA' || $objAdendum->getUsrCreacion() == 'TELCOS_MIGRACION')
                            {
                                continue;
                            }
                            $arrayData['strTipo'] = $objAdendum->getTipo();
                        }

                    }

                    $arrayContrato["strCodEmpresa"] = $arrayData["strCodEmpresa"];
                    $arrayContrato["contrato"]      = $objContrato;
                    $arrayContrato["strIp"]         = "127.0.0.1";
                    $arrayContrato["strUsuario"]    = "regularizacion";
                    $serviceContratoDigital = $this->get('comercial.InfoContratoDigital');
                    $arrayCrearCd           = $serviceContratoDigital->crearCertificadoNew($arrayContrato);
                    $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                                        ->findOneBy(array("numCedula" => $arrayData['usuario'],
                                                            "estado"    => "valido"));

                    //Se consulta si fue creado el certificado
                    if ($arrayCrearCd['salida'] == '1' && count($objCertificado) > 0)
                    {
                        //Genero un pin ya validado
                        $arrayResponse    = array();
                        $strMensaje       = "";
                        /* @var $serviceSeguridad SeguridadService */
                            // Generacion del PIN Security
                        $arrayGeneracionPin                  = array ();
                        $arrayGeneracionPin['strUsername']   = $arrayData['usuario'];
                        $arrayGeneracionPin['strCodEmpresa'] = $arrayData['strCodEmpresa'];
                        $arrayResponseService = $serviceRegularizaCYA->generarPinValidado($arrayGeneracionPin);
                        if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE')
                        {
                            $strMensaje = $arrayResponseService['mensaje'];
                            throw new \Exception('ERROR_PARCIAL');
                        }
                    }

                    // Se solicita la generacion de un certificado para poder firmar los documentos

                    // ========================================================================
                    // Autorizar el contrato digitalmente
                    // ========================================================================
                    // ========================================================================
                    // Enviar a firmar los documentos
                    // ========================================================================
                    //Obtener datos contrato

                    $intIdContrato = $objContrato->getId();
                    $arrayData['contrato'] = $objContrato;
                    $arrayDocumentosFirmados = $serviceRegularizaCYA->firmarDocumentosNewRegularizacion($arrayData);

                    if ($arrayDocumentosFirmados['salida']  == '0')
                    {
                        throw new \Exception($arrayDocumentosFirmados['mensaje']);
                    }

                    if ($arrayDocumentosFirmados['salida'] == '1')
                    {
                        $arrayDocumentos    = $arrayDocumentosFirmados['documentos'];
                        $arrayParametrosDocumentos['codEmpresa']     = $arrayData['strCodEmpresa'];
                        $arrayParametrosDocumentos['strUsrCreacion'] = $arrayData['user'];
                        $arrayParametrosDocumentos['strTipoArchivo'] = 'PDF';
                        $arrayParametrosDocumentos['intContratoId']  = $intIdContrato;
                        $arrayParametrosDocumentos['enviaMails']     = $arrayDocumentosFirmados['enviaMails'];
                        $arrayParametrosDocumentos['strExtension']   = '.pdf';
                        $arrayParametrosDocumentos['strTipo']        = $arrayData['strTipo'];
                        $arrayParametrosDocumentos['strNumeroAdendum'] = $arrayData['strNumeroAdendum'];
                        $serviceRegularizaCYA->guardarDocumentos($arrayParametrosDocumentos,$arrayDocumentos);
                        $arrayResponse['status']                  = $this->status['OK'];

                    }

                }
                else
                {
                    error_log("no hay contrato");
                    throw new \Exception("No se encontro contrato para regularizar");
                }
            }
            catch (\Exception $e)
            {
                if ($e->getMessage() == 'ERROR_PARCIAL')
                {
                    $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['message'] = $strMensaje;

                }
                else
                {
                    $strMensaje               = $e->getMessage();
                    $arrayResponse['status']  = $this->status['ERROR'];
                    $arrayResponse['message'] = $strMensaje;
                }

                $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appMethod']        = "putRegularizaContratosAdendums";
                $arrayParametrosLog['appAction']        = "putRegularizaContratosAdendums";
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $strMensaje;
                $arrayParametrosLog['inParameters']     = json_encode($arrayData);
                $arrayParametrosLog['creationUser']     = "REGULARIZACION";

                $serviceUtil->insertLog($arrayParametrosLog);
            }
        }

        return $arrayResponse;
    }

    /**
    * Método para obtener informacion de persona y validar si se debe rechazar contrato con inconsistencias.
    *
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 - 17-12-2020
    *
    *
    * Se realiza el registro de log de tipo info,error al momento que el movil comercial consuma la petición
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.1 - 15-01-2023
    * 
    *
    */    
    public function getPersona($arrayData)
    {
        $serviceComercialMobile  = $this->get('comercial.ComercialMobile');
        $serviceContratoAprob   = $this->get('comercial.InfoContratoAprob');
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $serviceInfoLog         = $this->get('comercial.InfoLog');
        $emGeneral                              = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil            = $this->get('schema.Util');
        $arrayParametrosLog                   = array();
        $strOrigen        = '';
        $strMetodo        = '';
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();
        $strCodEmpresa    =    $arrayData['strCodEmpresa'];
        try
        {

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
                                                                   'observacion' => 'BUSCAR CLIENTE TM COMERCIAL',
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

            $arrayData['tokenCas']    =$arrayTokenCas['strToken'];
            $arrayData['strIpMod']    =$arrayData['clientIp']; 
            $arrayData['strUserMod']  =$arrayData['user'];     


            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = $arrayData['clientIp'];
            $arrayParametrosLog['strUsrUltMod']   = $arrayData['user'];
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['strIdKafka']     = '';
            $arrayParametrosLog['request']        = $arrayData;
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];

            $arrayParametrosLog['strOrigen']      = $strOrigen;
            $arrayParametrosLog['strMetodo']      = $strMetodo;
    
 
            $arrayRespuesta = $serviceComercialMobile->getPersona($arrayData);
            
            if ($arrayRespuesta['boolRechazar'])
            {
                $arrayRechazo = array();
                
                $arrayRechazo['objContrato']          = $arrayRespuesta['objContrato'];
                $arrayRechazo['strUsrCreacion']       = $arrayData['user']; 
                $arrayRechazo['strIpCreacion']        = "127.0.0.1"; 
                $arrayRechazo['strMotivo']            = "Contrato en Estado Rechazado"; 
                $arrayRechazo['strCodEmpresa']        = $arrayData['strCodEmpresa']; 
                $serviceContratoAprob->rechazarContratoSinServiciosActivo($arrayRechazo);
                $arrayRespuesta = $serviceComercialMobile->getPersona($arrayData);
            }

            $arrayParametrosLog['response']      = $arrayRespuesta;
            $serviceInfoLog->registrarLogsMs($arrayParametrosLog);

        }
        catch (\Exception $e)
        {
            $arrayParametrosLog['strTipoEvento']  = 'ERROR';
            $arrayParametrosLog['strIpUltMod']    = $arrayData['clientIp'];
            $arrayParametrosLog['strUsrUltMod']   = $arrayData['user'];
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['strIdKafka']     = '';
            $arrayParametrosLog['request']        = $arrayData;
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            $arrayParametrosLog['response']       =  $e->getMessage();
            $arrayRespuesta['success']            = false;
            if ($e->getMessage() == 'ERROR_PARCIAL')
            {
                $arrayRespuesta['status']  = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['message'] = $arrayParametrosLog['response'];

            }
            else
            {
                $strMensaje               = $this->mensaje['ERROR'];
                $arrayRespuesta['status']  = $this->status['ERROR'];
                $arrayRespuesta['message'] = $arrayParametrosLog['response'];
            }

           if (empty($strOrigen) )
            {
                $arrayParametrosLog['strOrigen']      = 'registrarLogsClientes';
            }
            

            if (empty($strMetodo))
            {
                $arrayParametrosLog['strMetodo']      = 'verClienteApp';
            }
            
            $serviceInfoLog->registrarLogsMs($arrayParametrosLog);
        }


        return $arrayRespuesta;

    }
    
    /**
     * Método para obtener los productos adicionales con sus respectivas caracteristicas..
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 22-05-2021
     *
     * @param type $arrayData
     */
    public function getProductosAdicionales($arrayData)
    {
        $serviceAdmiProducto    = $this->get('comercial.AdmiProducto');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
            $arrayResponse              = $serviceAdmiProducto->getProductosAdicionales($arrayData['data']);
            $arrayResponse['status']    = $this->status['OK'];
            $arrayResponse['message']   = $this->mensaje['OK'];
            $arrayResponse['success']   = true;
        }
        catch (\Exception $e)
        {
            $arrayResponse['success']   = false;
            if ($e->getMessage() == 'ERROR_PARCIAL')
            {
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['message'] = $this->mensaje['ERROR_PARCIAL'];

            }
            else
            {
                $strMensaje               = $this->mensaje['ERROR'];
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['message'] = $strMensaje;
            }

            $arrayParametrosLog['enterpriseCode']   = $arrayData['data']['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "getProductosAdicionales";
            $arrayParametrosLog['appAction']        = "getProductosAdicionales";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];

            $serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayResponse;
    }

    /**
     * Metodo encargado de realizar el proceso de traslado, factibilidad y de convertir a orden de trabajo
     * para el canal de la extranet.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0
     * @since 12-03-2021
     *
     * @param type $arrayData
     */
    public function putTrasladarServicios($arrayData)
    {
    
        $arrayParametros            = "";
        $arrayRespuesta             = "";
        $arrayResultadoTraslado     = "";
        $arrayResultadoFactibilidad = "";
        $emComercial                = $this->getDoctrine()->getManager();
        $objServicioRepository      = $emComercial->getRepository('schemaBundle:InfoServicio');
        $serviceInfoServicio        = $this->get('comercial.InfoServicio');
        $serviceSoporte             = $this->get('soporte.SoporteService');
        $strLoginOrigen             = "";
        $strLoginDestino            = "";
        $serviceUtil                = $this->get('schema.Util');
        $strIdentificacion          = "";
        $entityCabError             = "";
        $entityDetError             = "";
        
        try 
        {
            
            //obtener mensaje de error parametrizado
            $entityCabError = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                              ->findOneBy(array('nombreParametro'  => 'ERRORES_TRASLADO',
                                                                'estado'           => 'Activo',
                                                                'modulo'           => 'TRASLADO_EXTRANET'));
            
            
            $entityDetError = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findOneBy(array('parametroId'  => $entityCabError->getId(),
                                                                'estado'       => 'Activo'));
            
            
            //variables para conexion a la base de datos mediante conexion OCI
            $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
            $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
            $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');
            
            //Validar Campos del request
        
            if (empty($arrayData["Canal"]))
            {
                  throw new \Exception("Falta la variable Canal en el request");
            }
            
            if (empty($arrayData["PrefijoEmpresa"]))
            {
                throw new \Exception("Falta la variable PrefijoEmpresa en el request");
            }
            
            if (empty($arrayData["EmpresaCod"]))
            {
                throw new \Exception("Falta la variable EmpresaCod en el request");
            }
            
            if (empty($arrayData["UsuarioCreacion"]))
            {
                throw new \Exception("Falta la variable UsuarioCreacion en el request");
            }
            
            if (empty($arrayData["UsuarioVendedor"]))
            {
                throw new \Exception("Falta la variable UsuarioVendedor en el request");
            }
            
            if (empty($arrayData["BanderaAutorizarSol"]))
            {
                throw new \Exception("Falta la variable BanderaAutorizarSol en el request");
            }
            
            if (empty($arrayData["loginDestino"]))
            {
                throw new \Exception("Falta la variable loginDestino en el request");
            }
            
            if (empty($arrayData["loginOrigen"]))
            {
                throw new \Exception("Falta la variable loginOrigen en el request");
            }
            
            if (empty($arrayData["IpCreacion"]))
            {
                throw new \Exception("Falta la variable IpCreacion en el request");
            }
             
            //obtener id punto de origen y punto de destino
            
            $entityPuntoOrigen = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                 ->findOneBy(array('login'  => $arrayData["loginOrigen"]));
            
            
            $entityPuntoDestino = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                 ->findOneBy(array('login'  => $arrayData["loginDestino"]));
            
            
            $arrayData["IdPuntoDestino"] = $entityPuntoDestino->getId();
            $arrayData["IdPuntoOrigen"]  = $entityPuntoOrigen->getId();
            
            
            //Variables para traslado de servicios
        
            $arrayParametros["strCanal"]                 = $arrayData["Canal"];
            $arrayParametros["strPrefijoEmpresa"]        = $arrayData["PrefijoEmpresa"];
            $arrayParametros["strEmpresaCod"]            = $arrayData["EmpresaCod"];
            $arrayParametros["strUsuarioCreacion"]       = $arrayData["UsuarioCreacion"];
            $arrayParametros["strUsuarioVendedor"]       = $arrayData["UsuarioVendedor"];
            $arrayParametros["strBanderaAutorizarSol"]   = $arrayData["BanderaAutorizarSol"];
            $arrayParametros["intIdPuntoCliente"]        = $arrayData["IdPuntoDestino"];
            $arrayParametros["intIdPuntoOrigen"]         = $arrayData["IdPuntoOrigen"];
            $arrayParametros["strIpCreacion"]            = $arrayData["IpCreacion"];
            $arrayParametros['ociCon']                   = $arrayOciCon;
        
            
            $strLoginOrigen  = $arrayData["loginOrigen"];
            $strLoginDestino = $arrayData["loginDestino"];
            
            
            //obtener información del cliente del punto destino
            $intIdPersonaRol = $entityPuntoDestino->getPersonaEmpresaRolId();
            
            $entityPersonaRol   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneById($intIdPersonaRol);
            
            $intIdPersona = $entityPersonaRol->getPersonaId();
            
            //Obtener campo id oficina del punto destino
            $entityInfoOficina   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                               ->findOneById($entityPersonaRol->getOficinaId());
            
            $intIdOficina = $entityInfoOficina->getId();
            
            $arrayParametros["intIdOficina"] = $intIdOficina;
            
            $entityPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->findOneById($intIdPersona);
            
            $strIdentificacion = $entityPersona->getIdentificacionCliente();
            
           
            //obtener información del cliente del punto origen
            $intIdPersonaRolOrigen = $entityPuntoOrigen->getPersonaEmpresaRolId();
            
            $entityPersonaRolOrigen  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneById($intIdPersonaRolOrigen);
            
            $intIdPersonaOrigen = $entityPersonaRolOrigen->getPersonaId();
            
            $entityPersonaOrigen  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->findOneById($intIdPersonaOrigen);
            
            $strIdentificacionOrigen = $entityPersonaOrigen->getIdentificacionCliente();
            
            
            // validar que no se traslade los servicios a un punto de otro cliente
            if($strIdentificacionOrigen !== $strIdentificacion)
            {
                throw new \Exception("No se puede trasladar los servicios del punto "
                                   . "de un cliente a un punto de otro cliente");
            }
            
            
            //validar que el punto de destino no tenga un servicio
            
            $arrayServicioDestino = $objServicioRepository->getPuntoDestino($arrayData["IdPuntoDestino"]);
            
            
            if(!empty($arrayServicioDestino))
            {
                throw new \Exception("Estimado cliente, ya se realizó un proceso de traslado para el login ".$strLoginDestino);
                
            }
           
            //ejecución de service para realizar el traslado de servicio
        
            $arrayResultadoTraslado  =  $serviceInfoServicio->trasladarServiciosPunto($arrayParametros);        
        
            $arrayRespuesta['status']  = $arrayResultadoTraslado['strStatus'];
            $arrayRespuesta['mensaje'] = $arrayResultadoTraslado['strMensaje'];
            $arrayParametros["intIdServicioInternet"] = $arrayResultadoTraslado['IdServicioConInternet'];
            $arrayParametros["arrayServiciosTrasladados"] = $arrayResultadoTraslado['arrayServiciosTrasladados'];
        
            if($arrayRespuesta['status']=="OK")
            {
              $arrayResultadoFactibilidad = $serviceInfoServicio->solicitarFactibilidadServicioExtranet($arrayParametros);
            }
            else
            {
               $arrayRespuesta['status']  = "Error";
               $arrayRespuesta['mensaje'] = $entityDetError->getValor1();
               
               $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Traslado no procesado",
                                               'strLoginOrigen'     => $strLoginOrigen,
                                               'strLoginDestino'    => $strLoginDestino,
                                               'strTipoCorreo'      => "TRASLADO",
                                               'strIdentificacion'  => $strIdentificacion,
                                               'strEstadoServicio'  => "",
                                               'idEmpresa'          => $arrayData["EmpresaCod"],
                                               'strUsrCreacion'     => $arrayData["UsuarioCreacion"],
                                               'strIpCreacion'      => $arrayData["IpCreacion"],
                                               'strError'           => $arrayResultadoTraslado['strMensaje'],
                                               'prefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                               'strCorreoCliente'   => "N");
            
                $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
            }
        
            if($arrayResultadoFactibilidad["status"] == 500)
            { 
               $arrayResultadoRollback = $objServicioRepository->getRollbackTraslado($arrayParametros);
               $strStatus              = $arrayResultadoRollback["strStatus"];
            
               // obtener el estado del servicio trasladado
               $entityServicio  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findOneById($arrayParametros["intIdServicioInternet"]);
               
               $strEstadoServicio = $entityServicio->getEstado();
               
               
               if($strStatus == "OK")
               {
                  $arrayRespuesta['status']  = "ERROR";
                  $arrayRespuesta['mensaje'] = $entityDetError->getValor1();
                  
                  $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Traslado no procesado",
                                                  'strLoginOrigen'     => $strLoginOrigen,
                                                  'strLoginDestino'    => $strLoginDestino,
                                                  'strTipoCorreo'      => "TRASLADO",
                                                  'strEstadoServicio'  => $strEstadoServicio,
                                                  'strIdentificacion'  => $strIdentificacion,
                                                  'idEmpresa'          => $arrayData["EmpresaCod"],
                                                  'strUsrCreacion'     => $arrayData["UsuarioCreacion"],
                                                  'strIpCreacion'      => $arrayData["IpCreacion"],
                                                  'strError'           => "",
                                                  'prefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                                  'strCorreoCliente'   => "N",
                                                  'intIdServicio'      => $arrayParametros["intIdServicioInternet"]);
            
                  $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
                  
                  
               }
            
            
            }
            elseif($arrayResultadoFactibilidad["status"]==200)
            {
  
                // se valida si el punto tiene saldo
               $entityPuntoSaldo   = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->getPuntoDeuda($arrayData["IdPuntoOrigen"]);
            
               $fltSaldoPunto = $entityPuntoSaldo[0]["saldo"];
            
               if($fltSaldoPunto>0)
               {
                  $strTieneDeuda = "S";
               }
               else
               {
                  $strTieneDeuda = "N";
               }
                
               $arrayParametrosOT  = array('strOficina'             => $intIdOficina,
                                           'strUser'                => $arrayData["UsuarioCreacion"],
                                           'strIp'                  => $arrayData["IpCreacion"],
                                           'array_valor'            => array($arrayParametros["intIdServicioInternet"]),
                                           'strMensajeObservacion'  => "Proceso de convertir orden de trabajo
                                                                        mediante la extranet",                                    
                                           'strOTClienteConDeuda'   => $strTieneDeuda,
                                           'intIdPunto'             => $arrayData["IdPuntoDestino"],
                                           'strCodEmpresa'          => $arrayData["EmpresaCod"],
                                           'strPrefijoEmpresa'      => $arrayData["PrefijoEmpresa"]);
            
               $strMensajeOT       = "Se generaron las ordenes de trabajo de los servicios seleccionados.";
               $serviceConvertirOT = $this->get('comercial.ConvertirOrdenTrabajo');
               $strResponseOT      = $serviceConvertirOT->convertirOrdenTrabajo($arrayParametrosOT); 
            
               $strPost    = strpos($strResponseOT,$strMensajeOT);
            
               if($strPost !== false)
               {
                  $arrayRespuesta['status'] = "OK";
                  $arrayRespuesta['mensaje'] = "El proceso de traslado fue ingresado correctamente";
                  
                  // obtener el estado del servicio trasladado
                  $entityServicio  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findOneById($arrayParametros["intIdServicioInternet"]);
               
                  $strEstadoServicio = $entityServicio->getEstado();
                  
                  $arrayRespuesta['loginOrigen']  = $strLoginOrigen;
                  $arrayRespuesta['loginDestino'] = $strLoginDestino;
                  
                  //crear Tarea Rapida
                  
                  //obtener datos parametrizados para la creacion de la tarea
                  $entityParametroCab = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro'  => 'DATOS_CREACION_TAREA',
                                                                      'estado'           => 'Activo',
                                                                      'modulo'           => 'TRASLADO_EXTRANET'));
            
            
                  $entityParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array('parametroId'  => $entityParametroCab->getId(),
                                                                      'estado'       => 'Activo'));
            
            
                  //obtener los datos del empleado para creacion de tarea
                  $entityDatosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->findOneBy(array('login'  => $entityParametroDet->getValor1(),
                                                                      'estado' => 'Activo'));
            
                  $strEmpleado   = $entityDatosUsuario->getNombres()." ".$entityDatosUsuario->getApellidos();
            
                  //obtener id departamento del empleado que creará la tarea
                  $arrayParametrosEmpresa = array('strEmpresaCod'   => $arrayData["EmpresaCod"],
                                                  'strLogin'        => $entityParametroDet->getValor1());
            
                  $arrayDatosEmpresa = $objServicioRepository->getInformacionEmpleado($arrayParametrosEmpresa);
 
                  $intIdAsignado       = $entityDatosUsuario->getId();
            
                  foreach($arrayDatosEmpresa as $strDatosEmpresa)
                  {
                     $strNombreDepartamento = $strDatosEmpresa['nombreDepartamento'];
                     $strCiudad             = $strDatosEmpresa['nombreCanton'];
                  }
            
            
                  $arrayParametrosTarea = array('strIdEmpresa'          => $arrayData["EmpresaCod"],
                                                'strPrefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                                'strNombreTarea'        => $entityParametroDet->getValor2(),
                                                'strObservacion'        => $entityParametroDet->getValor3(),
                                                'strNombreDepartamento' => $strNombreDepartamento,
                                                'strCiudad'             => $strCiudad,
                                                'strEmpleado'           => $strEmpleado,
                                                'strUsrCreacion'        => $entityParametroDet->getValor1(),
                                                'strIp'                 => $arrayData["IpCreacion"],
                                                'strOrigen'             => 'WEB-TN',
                                                'strLogin'              => $strLoginDestino,
                                                'intPuntoId'            => $arrayData["IdPuntoDestino"],
                                                'strCodEmpresaOrig'     => $arrayData["EmpresaCod"],
                                                'strOrigenComunicacion' => $entityParametroDet->getValor4(),
                                                'strClase'              => $entityParametroDet->getValor5());
                
                  $arrayRespuestaTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                  $strStatusTarea    = $arrayRespuestaTarea['status'];
                  $strMensajeTarea   = $arrayRespuestaTarea['mensaje'];
                  $intIdTarea        = $arrayRespuestaTarea['id'];
                  $intIdDetalleTarea = $arrayRespuestaTarea['idDetalle'];
            
                  if($strStatusTarea == "OK")
                  {
                      // se finaliza la tarea creada
                      $intIdAsignado       = $entityDatosUsuario->getId();
                      $objFechaHoy         = new \DateTime('now');
                      $strFechaFinaliza    = $objFechaHoy->format('Y-m-d');
                      $strHoraFinaliza     = $objFechaHoy->format('H:i:s');
                
                      $arrayParametrosTareaF = array(
                              'idEmpresa'             => $arrayData["EmpresaCod"],
                              'prefijoEmpresa'        => $arrayData["PrefijoEmpresa"],
                              'idCaso'                => "",
                              'idDetalle'             => $intIdDetalleTarea,
                              'tarea'                 => $intIdTarea,
                              'fechaCierre'           => $strFechaFinaliza,
                              'horaCierre'            => $strHoraFinaliza,
                              'fechaEjecucion'        => $strFechaFinaliza,
                              'horaEjecucion'         => $strHoraFinaliza,
                              'esSolucion'            => "",
                              'fechaApertura'         => "",
                              'horaApertura'          => "",
                              'jsonMateriales'        => "",
                              'idAsignado'            => $intIdAsignado,
                              'observacion'           => "Se finaliza tarea en forma automática",
                              'empleado'              => $strEmpleado,
                              'usrCreacion'           => $entityParametroDet->getValor1(),
                              'ipCreacion'            => $arrayData["IpCreacion"],
                              'strEnviaDepartamento'  => "N",
                              "clientes"              => $strLoginDestino,
                              "strOrigenComunicacion" => $entityParametroDet->getValor4(),
                              "strClase"              => $entityParametroDet->getValor5()
                       );
                
                       $arrayRespuestaFinaliza = $serviceSoporte->finalizarTarea($arrayParametrosTareaF);
                       $strStatusFinaliza      = $arrayRespuestaFinaliza['status'];
                
                       if($strStatusFinaliza == "OK")
                       {
                           $arrayRespuestaFinaliza["strMensaje"] = "Se creó la tarea rápida!";
                       }
                       else
                       {
                          $arrayRespuestaFinaliza["strMensaje"] = "Se creó la tarea rápida, pero no fue finalizada!";
                       }
                
                
                  }
                  else
                  {
                      $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWsControllerRest->putTrasladarServicios',
                                      $strMensajeTarea,
                                      $arrayData["UsuarioCreacion"],
                                      $arrayData["IpCreacion"]);
                  }
                  
                  $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Traslado procesado correctamente",
                                                  'strLoginOrigen'     => $strLoginOrigen,
                                                  'strLoginDestino'    => $strLoginDestino,
                                                  'strTipoCorreo'      => "TRASLADO_AUTOMATICO",
                                                  'strIdentificacion'  => $strIdentificacion,
                                                  'idEmpresa'          => $arrayData["EmpresaCod"],
                                                  'strUsrCreacion'     => $arrayData["UsuarioCreacion"],
                                                  'strIpCreacion'      => $arrayData["IpCreacion"],
                                                  'strEstadoServicio'  => $strEstadoServicio,
                                                  'strError'           => " Se realiza el traslado del servicio desde la "
                                                                        . " Extranet de forma correcta",
                                                  'prefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                                  'strCorreoCliente'   => "S",
                                                  'intIdServicio'      => $arrayParametros["intIdServicioInternet"]);
            
                  $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
                  
               }
               else
               {
                  
                  $arrayResultadoRollback     = $objServicioRepository->getRollbackTraslado($arrayParametros);
                  $arrayResultadoFactibilidad = $serviceInfoServicio->rechazoFactibilidadExtranet($arrayParametros);
                  
                  if($arrayResultadoRollback["strStatus"] == "OK" && $arrayResultadoFactibilidad['status']==200)
                  {
                      $arrayRespuesta['status']  = "Error";
                      $arrayRespuesta['mensaje'] = $entityDetError->getValor1();
                  }
                  
               }
            
            
            }
            elseif($arrayResultadoFactibilidad["status"]==205)
            {
             
                  // obtener el estado del servicio trasladado
                  $entityServicio  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findOneById($arrayParametros["intIdServicioInternet"]);
               
                  $strEstadoServicio = $entityServicio->getEstado();
                
                  $arrayRespuesta['status']  = "Error";
                  $arrayRespuesta['mensaje'] = "Su solicitud de traslado está en proceso";
                  
                  $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Traslado pendiente de factibilidad",
                                                  'strLoginOrigen'     => $strLoginOrigen,
                                                  'strLoginDestino'    => $strLoginDestino,
                                                  'strTipoCorreo'      => "TRASLADO_MANUAL",
                                                  'strIdentificacion'  => $strIdentificacion,
                                                  'strEstadoServicio'  => $strEstadoServicio,
                                                  'idEmpresa'          => $arrayData["EmpresaCod"],
                                                  'strUsrCreacion'     => $arrayData["UsuarioCreacion"],
                                                  'strIpCreacion'      => $arrayData["IpCreacion"],
                                                  'strError'           => "",
                                                  'prefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                                  'strCorreoCliente'   => "S",
                                                  'intIdServicio'      => $arrayParametros["intIdServicioInternet"]);
            
                   $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
            
            }
        
        
            
        } 
        catch (\Exception $e) 
        {
            
            $arrayRespuesta["status"]  = "Error";
            $arrayRespuesta["mensaje"] = $entityDetError->getValor1();
            
            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWsControllerRest->putTrasladarServicios',
                                      $e->getMessage(),
                                      $arrayData["UsuarioCreacion"],
                                      $arrayData["IpCreacion"]);
            
            
            $arrayParametrosEnvio =   array('strAsuntoCorreo'    => "Traslado no procesado",
                                            'strLoginOrigen'     => $strLoginOrigen,
                                            'strLoginDestino'    => $strLoginDestino,
                                            'strTipoCorreo'      => "TRASLADO",
                                            'strIdentificacion'  => $strIdentificacion,
                                            'strEstadoServicio'  => "",
                                            'idEmpresa'          => $arrayData["EmpresaCod"],
                                            'strUsrCreacion'     => $arrayData["UsuarioCreacion"],
                                            'strIpCreacion'      => $arrayData["IpCreacion"],
                                            'strError'           => $e->getMessage(),
                                            'prefijoEmpresa'     => $arrayData["PrefijoEmpresa"],
                                            'strCorreoCliente'   => "N",
                                            'intIdServicio'      => $arrayParametros["intIdServicioInternet"]);
            
            $serviceSoporte->enviarPlantillaExtranet($arrayParametrosEnvio);
            
            
        }
        
       return $arrayRespuesta;
       
    }

    /**
     * 
     * Método encargado de validar un código promocional. 
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 11-11-2020
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    public function putValidaCodigoPromocional($arrayData)
    {
        $strUsuario             = $arrayData['strUsuario'] ? $arrayData['strUsuario'] : 'telcos_mapeo_promo';
        $strIp                  = $arrayData['strIp'] ? $arrayData['strIp'] : '127.0.0.1';
        /* @var $objPromocionService \telconet\comercialBundle\Service\PromocionService */
        $objPromocionService    = $this->get('comercial.Promocion');
        /* @var $objServiceUtil \telconet\schemaBundle\Service\UtilService */
        $objServiceUtil         = $this->get('schema.Util');
        try
        {
            $arrayRespuesta = $objPromocionService->validaCodigoPromocionMasiva($arrayData);
        }
        catch(\Exception $objException)
        {
            $strMessageControlado = 'Ocurrió un error al validar el código ingresado.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'ComercialMobileWSControllerRest->putValidaCodigoPromocional',
                                         $strMessage,
                                         $strUsuario,
                                         $strIp);

            $arrayRespuesta = array ('response' => $strMessageControlado,
                                     'status'   => 500,
                                     'message'  => 'ERROR',
                                     'success'  => false);
            return $arrayRespuesta;
        }
        return $arrayRespuesta;
    }

}