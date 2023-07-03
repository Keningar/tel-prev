<?php

namespace telconet\tecnicoBundle\Service;


use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ReubicacionNetlifeService
{

    private $objContainer;
    private $emGeneral;
    private $emComercial;
    private $emSoporte;
    private $emComunicacion;
    private $serviceUtil;
    private $servicieServicioTecnico;
    private $serviceTokenCas;
    private $serviceEnvioPlantilla;
    private $serviceInfoLog;

    public function setDependencies(Container $objContainer)
    {
        $this->objContainer = $objContainer;
        $this->emComercial  = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral    = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->emSoporte    = $objContainer->get('doctrine')->getManager("telconet_soporte");
        $this->emComunicacion    = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->serviceUtil  = $objContainer->get('schema.Util');
        $this->serviceTokenCas  = $objContainer->get('seguridad.TokenCas');
        $this->servicieServicioTecnico = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceEnvioPlantilla   = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceInfoLog          = $objContainer->get('comercial.InfoLog');
    }

    /**
     * solicitudReubicacion
     *
     * Función encargada de retornar si el cliente tiene o no una Solicitud de Reubicacion
     * en proceso.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 23/05/2023
     *
     * @param array $arrayParametros [
     *                                intIdProveedor    => Código del proveedor que realizará el proceso
     *                                strLoginCliente   => Login del cliente que realizará el proceso
     *                                strCanal          => Canal por el cual se realizará el proceso
     *                                strUsuario        => usuario realizará el proceso
     *                                strIpCliente      => ip del cliente
     *                                strCodEmpresa     => Codigo empresa
     *                               ]
     * @return array $arrayResponse [
     *                                code    => Codigo de error
     *                                status  => Estado de ejecución del proceso
     *                                mensaje => Mensaje de ejecución del proceso
     *                                data    => Data de respuesta
     *                               ]
     */

    public function solicitudReubicacion($arrayParametros)
    {
        list($arrayParametros, $intIdProveedor, $strLoginCliente, $strCanal,
            $intIdentificacionCliente, $strCodEmpresa, $strUsuarioAsigna, $strIpCliente,
            $strAppMethod) = $this->getVariables($arrayParametros);
        $strClass          = "TecnicoWSController";
        $arrayDataResponse = array();
        $arrayResponse     = array();
        $arrayConFigMeth   = array();
        $strError          = '';


        try
        {

            $arrayConFigMeth   = $this->obtenerValoresCorreoReu($strAppMethod);
            if($arrayConFigMeth['status'] == "ERROR")
            {
                $strError = $arrayConFigMeth['strError'];
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['request']        = $arrayParametros['data'];
            $arrayTokenCas                        = $this->serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            if (empty($strLoginCliente)  || empty($strCodEmpresa)  || empty($intIdentificacionCliente) ||
                empty($strUsuarioAsigna) || empty($intIdProveedor) || empty($strCanal))
            {
                $strError = 'No se han enviado los parámetros adecuados para procesar la información. - 
                strLoginCliente('.$strLoginCliente.'), strCodEmpresa('.$strCodEmpresa.'), strTipoProceso('
                    .$intIdentificacionCliente.'),'.'strUsuarioAsigna('.$strUsuarioAsigna.'), intIdProveedor('
                    .$intIdProveedor.').strCanal('.$strCanal.')';
                throw new Exception("NULL");
            }
            if ($strCodEmpresa != '18')
            {
                $strError = 'Codigo de empresa no se encuentra registrado en base';
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayRequestLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);

            if ($arrayRequestLogs['status'] != 'OK' && $arrayRequestLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }


            $objPtoCliente = $this->emComercial->getRepository('schemaBundle:InfoPunto')->findBy(
                                                                    array('login' => $strLoginCliente));
            if (!is_object($objPtoCliente[0]))
            {
                $strError = 'No existe ese Login en base';
                throw new Exception("ERROR_PARCIAL");
            }
            $objSolititudReubi = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->getSolicitudPorPunto($objPtoCliente[0]->getId(),
                    'SOLICITUD REUBICACION',
                    array('Planificada',
                        'Pendiente',
                        'Aprobado',
                        'PrePlanificada',
                        'Replanificada',
                        'AsignadoTarea',
                        'Asignada',
                        'Detenido'));

            if (count($objSolititudReubi) >= 1 && is_array($objSolititudReubi))
            {
                $arrayResponse['code'] = 1;
                $arrayResponse['status'] = "OK";
                $arrayResponse['mensaje'] = str_replace("{{UBICACION}}", $objPtoCliente[0]->getDireccion(),
                                                        $arrayConFigMeth['strMensajeError']);
                $arrayDataResponse['idSolicitud']    = $objSolititudReubi[0]['id'];
                $arrayDataResponse['direccionPunto'] =  $objPtoCliente[0]->getDireccion();
                $arrayDataResponse['solicitud'] = 'SI';
            }
            else
            {
                $arrayResponse['code'] = 0;
                $arrayResponse['status'] = "OK";
                $arrayResponse['mensaje'] = "Transacción exitosa";
                $arrayDataResponse['idSolicitud'] = '';
                $arrayDataResponse['solicitud'] = 'NO';
            }
            $arrayResponse['data'] = $arrayDataResponse;

            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['response']       = $arrayResponse['data'];
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            $arrayResponseLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            if ($arrayResponseLogs['status'] != 'OK' && $arrayResponseLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }
            error_log("Respuesta que responde el web service " . print_r($arrayResponse,true));

        }
        catch (Exception $objEx)
        {

            $arrayResponse = $this->getArrayResponse($objEx, array(
                'arrayResponse' =>$arrayResponse, 'arrayConFigMeth'=>$arrayConFigMeth, 'strError'=>$strError,
                'strCanal'=>$strCanal,  'strAppMethod'=>$strAppMethod,
                'intIdentificacionCliente'=>$intIdentificacionCliente,'strLoginCliente'=>$strLoginCliente,
                'strClass'=>$strClass, 'intIdProveedor'=>$intIdProveedor));
            
        }

        return $arrayResponse;
    }

    /**
     * servicioSuspendidoReubicacion
     *
     * Función encargada de retornar si el servicio se encuentra en estado Suspendido(In-Corte).
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 24/05/2023
     *
     * @param array $arrayParametros [
     *                                intIdProveedor    => Código del proveedor que realizará el proceso
     *                                strLoginCliente   => Login del cliente que realizará el proceso
     *                                strCanal          => Canal por el cual se realizará el proceso
     *                                strUsuario        => usuario realizará el proceso
     *                                strIpCliente      => ip del cliente
     *                                strCodEmpresa     => Codigo empresa
     *                               ]
     * @return array $arrayResponse [
     *                                code    => Codigo de error
     *                                status  => Estado de ejecución del proceso
     *                                mensaje => Mensaje de ejecución del proceso
     *                                data    => Data de respuesta
     *                               ]
     */

    public function servicioSuspendidoReubicacion($arrayParametros)
    {
        list($arrayParametros, $intIdProveedor, $strLoginCliente, $strCanal,
            $intIdentificacionCliente, $strCodEmpresa, $strUsuarioAsigna, $strIpCliente,
            $strAppMethod) = $this->getVariables($arrayParametros);
        $strClass          = "TecnicoWSController";
        $arrayResponse     = array();
        $arrayDataResponse = array();
        $arrayConFigMeth   = array();
        $strError          = '';


        try
        {

            $arrayConFigMeth   = $this->obtenerValoresCorreoReu($strAppMethod);
            if($arrayConFigMeth['status'] == "ERROR")
            {
                $strError = $arrayConFigMeth['strError'];
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['request']        = $arrayParametros['data'];
            $arrayTokenCas                        = $this->serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            if (empty($strLoginCliente)  || empty($strCodEmpresa)  || empty($intIdentificacionCliente) ||
                empty($strUsuarioAsigna) || empty($intIdProveedor) || empty($strCanal))
            {
                $strError = 'No se han enviado los parámetros adecuados para procesar la información. - 
                strLoginCliente('.$strLoginCliente.'), strCodEmpresa('.$strCodEmpresa.'), strTipoProceso('
                    .$intIdentificacionCliente.'),'.'strUsuarioAsigna('.$strUsuarioAsigna.'), intIdProveedor('
                    .$intIdProveedor.').strCanal('.$strCanal.')';
                throw new Exception("NULL");
            }

            if ($strCodEmpresa != '18')
            {
                $strError = 'Codigo de empresa no se encuentra registrado en base';
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayRequestLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);

            if ($arrayRequestLogs['status'] != 'OK' && $arrayRequestLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }

            $objPtoCliente = $this->emComercial->getRepository('schemaBundle:InfoPunto')->findBy(
                array('login' => $strLoginCliente));
            if (!is_object($objPtoCliente[0]))
            {
                $strError = 'No existe ese Login en base';
                throw new Exception("ERROR_PARCIAL");
            }
            $objSolititudReubi   = $this->servicieServicioTecnico->obtieneServicioInternetValido(
                array("intIdPunto"=>$objPtoCliente[0]->getId(),
                "strCodEmpresa" =>$strCodEmpresa,
                "omiteEstadoPunto"  => "SI"));

            if (is_object($objSolititudReubi['objServicioInternet']) &&
                $objSolititudReubi['objServicioInternet']->getEstado() !='In-Corte')
            {
                $arrayResponse['code']               = 0;
                $arrayResponse['status']             = "OK";
                $arrayResponse['mensaje']            = "Transacción exitosa";
                $arrayDataResponse['idSuspension'] = '';
                $arrayDataResponse['suspension']   = 'NO';
            }
            elseif (!is_object($objSolititudReubi['objServicioInternet']))
            {
                $strError = 'Cliente no posee un servicio de internet valido';
                throw new Exception("ERROR_PARCIAL");
            }
            else
            {
                $arrayResponse['code']                 = 1;
                $arrayResponse['status']               = "OK";
                $arrayResponse['mensaje']              = $arrayConFigMeth['strMensajeError'];
                $arrayDataResponse['idSuspension']  = $objSolititudReubi['objServicioInternet']->getId();
                $arrayDataResponse['suspension']    =    'SI';
            }
            $arrayResponse['data'] = $arrayDataResponse;

            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['response']       = $arrayResponse['data'];
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            $arrayResponseLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            if ($arrayResponseLogs['status'] != 'OK' && $arrayResponseLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }
            error_log("Respuesta que responde el web service " . print_r($arrayResponse,true));

        }
        catch (Exception $objEx)
        {
            $arrayResponse = $this->getArrayResponse($objEx, array(
                'arrayResponse' =>$arrayResponse, 'arrayConFigMeth'=>$arrayConFigMeth, 'strError'=>$strError,
                'strCanal'=>$strCanal,  'strAppMethod'=>$strAppMethod,
                'intIdentificacionCliente'=>$intIdentificacionCliente,'strLoginCliente'=>$strLoginCliente,
                'strClass'=>$strClass, 'intIdProveedor'=>$intIdProveedor));

        }

        return $arrayResponse;
    }

    /**
     * suspensionTemporalReubicacion
     *
     * Función encargada de retornar si el cliente tiene contratado un plan de suspension temporal
     * o no.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 25/05/2023
     *
     * @param array $arrayParametros [
     *                                intIdProveedor    => Código del proveedor que realizará el proceso
     *                                strLoginCliente   => Login del cliente que realizará el proceso
     *                                strCanal          => Canal por el cual se realizará el proceso
     *                                strUsuario        => usuario realizará el proceso
     *                                strIpCliente      => ip del cliente
     *                                strCodEmpresa     => Codigo empresa
     *                               ]
     * @return array $arrayResponse [
     *                                code    => Codigo de error
     *                                status  => Estado de ejecución del proceso
     *                                mensaje => Mensaje de ejecución del proceso
     *                                data    => Data de respuesta
     *                               ]
     */
    public function suspensionTemporalReubicacion($arrayParametros)
    {
        list($arrayParametros, $intIdProveedor, $strLoginCliente, $strCanal,
            $intIdentificacionCliente, $strCodEmpresa, $strUsuarioAsigna, $strIpCliente,
            $strAppMethod) = $this->getVariables($arrayParametros);
        $strClass          = "TecnicoWSController";
        $arrayResponse     = array();
        $arrayDataResponse = array();
        $arrayConFigMeth   = array();
        $arrayEstadosNoPer = array('In-Corte', 'Eliminado', 'Cancel','Inactivo');
        $strError          = '';


        try
        {

            $arrayConFigMeth   = $this->obtenerValoresCorreoReu($strAppMethod);
            if($arrayConFigMeth['status'] == "ERROR")
            {
                $strError = $arrayConFigMeth['strError'];
                throw new Exception("ERROR_PARCIAL");
            }
            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['request']        = $arrayParametros['data'];
            $arrayTokenCas                        = $this->serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            if (empty($strLoginCliente)  || empty($strCodEmpresa)  || empty($intIdentificacionCliente) ||
                empty($strUsuarioAsigna) || empty($intIdProveedor) || empty($strCanal))
            {
                $strError = 'No se han enviado los parámetros adecuados para procesar la información. - 
                strLoginCliente('.$strLoginCliente.'), strCodEmpresa('.$strCodEmpresa.'), strTipoProceso('
                    .$intIdentificacionCliente.'),'.'strUsuarioAsigna('.$strUsuarioAsigna.'), intIdProveedor('
                    .$intIdProveedor.').strCanal('.$strCanal.')';
                throw new Exception("NULL");
            }

            if ($strCodEmpresa != '18')
            {
                $strError = 'Codigo de empresa no se encuentra registrado en base';
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayRequestLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);

            if ($arrayRequestLogs['status'] != 'OK' && $arrayRequestLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }


            $objPtoCliente = $this->emComercial->getRepository('schemaBundle:InfoPunto')->findBy(
                array('login' => $strLoginCliente));
            if (!is_object($objPtoCliente[0]))
            {
                $strError = 'No existe el Login proveedor registrado en base';
                throw new Exception("ERROR_PARCIAL");
            }
            $objSolititudReubi   = $this->servicieServicioTecnico->obtieneServicioInternetValido(
                array("intIdPunto"=>$objPtoCliente[0]->getId(),
                "strCodEmpresa" =>$strCodEmpresa,
                "omiteEstadoPunto"  => "SI"));

            if (is_object($objSolititudReubi['objServicioInternet']) &&
                !in_array($objSolititudReubi['objServicioInternet']->getEstado(),$arrayEstadosNoPer))
            {
                $objPlanCliente = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->findBy(
                    array('id'=>$objSolititudReubi['objServicioInternet']->getPlanId()->getId(),
                        'empresaCod'=>$strCodEmpresa));
                if(strtoupper($arrayConFigMeth['strPlanSuspen']) != strtoupper($objPlanCliente[0]->getDescripcionPlan()))
                {
                    $arrayResponse['code']               = 0;
                    $arrayResponse['status']             = "OK";
                    $arrayResponse['mensaje']            = "Transacción exitosa";
                    $arrayDataResponse['nombrePlan'] = '';
                    $arrayDataResponse['plan']   = 'NO';

                }
                else
                {
                    $arrayResponse['code']                 = 1;
                    $arrayResponse['status']               = "OK";
                    $arrayResponse['mensaje']              =  str_replace("{{NOMBREPLAN}}",
                                                                $objPlanCliente[0]->getNombrePlan(),
                                                                $arrayConFigMeth['strMensajeError']) ;
                    $arrayDataResponse['idPlan']    =  $objPlanCliente[0]->getId();
                    $arrayDataResponse['nombrePlan']    =  $objPlanCliente[0]->getNombrePlan();
                    $arrayDataResponse['plan']          =  'SI';
                }
            }
            elseif (!is_object($objSolititudReubi['objServicioInternet']) ||
                in_array($objSolititudReubi['objServicioInternet']->getEstado(),$arrayEstadosNoPer))
            {
                $strError = 'Cliente no posee un servicio de internet valido';
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayResponse['data'] = $arrayDataResponse;

            $arrayParametrosLog['strOrigen']      = $arrayConFigMeth['strOrigen'];
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['response']       = $arrayResponse['data'];
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            $arrayResponseLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            if ($arrayResponseLogs['status'] != 'OK' && $arrayResponseLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }
            error_log("Respuesta que responde el web service " . print_r($arrayResponse,true));

        }
        catch (Exception $objEx)
        {
            $arrayResponse = $this->getArrayResponse($objEx, array(
                'arrayResponse' =>$arrayResponse, 'arrayConFigMeth'=>$arrayConFigMeth, 'strError'=>$strError,
                'strCanal'=>$strCanal,  'strAppMethod'=>$strAppMethod,
                'intIdentificacionCliente'=>$intIdentificacionCliente,'strLoginCliente'=>$strLoginCliente,
                'strClass'=>$strClass, 'intIdProveedor'=>$intIdProveedor));

        }

        return $arrayResponse;
    }

    /**
     * obtenerValoresCorreoReu
     *
     * Función encargada de retornar los parametros de configuracion para un op en especifico de reubicacion.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 25/05/2023
     *
     * @param string $strAppMethod  =  op que se envia.
     * @return array $arrayResponse [
     *                                strAsunto       => Asunto del correo de acuerdo al op.
     *                                strCuerpoCorreo => Cuerpo del correo de acuerdo al op.
     *                                strMensajeError => Mensaje error del correo de acuerdo al op.
     *                                strOrigen       => Origen registro Logs.
     *                                strMensajeGen   => Mensaje generico del correo de acuerdo al op.
     *                                strPlanSuspen   => Validacion para el plan de Suspension Temporal.
     *                               ]
     */
    public function obtenerValoresCorreoReu($strAppMethod)
    {
        $arrayParamReubi = '';
        $arrayRespuesta  = array();
        $strError        = '';
        $arrayParamTo      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                'TECNICO',
                'CONFIGURACION REUBICACION',
                'CORREO_ELECTRONICO_DESTINATARIO_REUBICACION',
                '',
                '',
                '',
                '',
                '',
                '18');
        $arrayParamFrom    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                'TECNICO',
                'CONFIGURACION REUBICACION',
                'CORREO_ELECTRONICO_REMITENTE_REUBICACION',
                '',
                '',
                '',
                '',
                '',
                '18');

        $arrayRegLogs    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CONFIGURACION_PARAMETROS_LOGS',
                'TECNICO',
                '',
                'CABECERA_ORIGEN',
                '',
                '',
                '',
                '',
                '',
                '18');

        try
        {
            if($strAppMethod=='getReubicacionSolicitud')
            {
                $arrayParamReubi   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                        'TECNICO',
                        'CONFIGURACION REUBICACION',
                        'ASUNTO_REUBICACION',
                        'EN_PROCESO_REUBICACION',
                        '',
                        '',
                        '',
                        '',
                        '18');
            }
            elseif($strAppMethod=='getReubicacionServicioSuspendido')
            {
                $arrayParamReubi   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                        'TECNICO',
                        'CONFIGURACION REUBICACION',
                        'ASUNTO_REUBICACION',
                        'ESTADO_INCORTE',
                        '',
                        '',
                        '',
                        '',
                        '18');
            }
            elseif($strAppMethod=='getReubicacionPlanSuspensionTemporal')
            {
                $arrayParamReubi   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                        'TECNICO',
                        'CONFIGURACION REUBICACION',
                        'ASUNTO_REUBICACION',
                        'PLAN_SUSPENSION',
                        '',
                        '',
                        '',
                        '',
                        '18');
                $arrayPlanSuspension = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get("TIPO_PLAN_POR_SUSPENSION", //Nombre parametro
                        "TECNICO",   //Modulo
                        "",          //Proceso
                        "",          //Descripcion
                        "",          //Valor1
                        "SUSPENSION",//Valor2
                        "","","","");
                $arrayRespuesta['strPlanSuspen']    = $arrayPlanSuspension[0]['valor1'];
            }
            elseif($strAppMethod=='getReubicacionPrecioEquipo')
            {
                $arrayParamReubi   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get('CONFIGURACION_PARAMETROS_REUBICACION',
                        'TECNICO',
                        'CONFIGURACION REUBICACION',
                        'ASUNTO_REUBICACION',
                        'PRECIO_ONT',
                        '',
                        '',
                        '',
                        '',
                        '18');
            }
            if (!is_array($arrayParamReubi[0]))
            {
                $strError = 'No estan configurados los parametros en base.';
                throw new Exception("ERROR_PARCIAL");
            }

            if(count($arrayParamFrom)>1)
            {
                $strError = 'No puede existir mas de un remitente';
                $strRemitente = $arrayParamFrom[0]['valor1'];
                $arrayDestina = $this->serviceUtil->obtenerValoresParametro($arrayParamTo);
                $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->
                                                        getPlantillaXCodigoYEmpresa($arrayParamReubi[0]['valor3']);
                $arrayRespuesta['strAsunto']       = $arrayParamReubi[0]['valor2'];
                $arrayRespuesta['strCuerpoCorreo'] = $objPlantilla[0]->getPlantilla();
                $arrayRespuesta['strMensajeError'] = $arrayParamReubi[0]['valor4'];
                $arrayRespuesta['strOrigen']       = $arrayRegLogs[0]['valor1'];
                $arrayRespuesta['strMensajeGen']   = $arrayParamReubi[0]['valor5'];
                $arrayRespuesta['strWS']           = $arrayParamReubi[0]['valor6'];
                $arrayRespuesta['strRemitente']    = $strRemitente;
                $arrayRespuesta['arrayDestina']    = $arrayDestina;
                throw new Exception("ERROR_PARCIAL");
            }
            $strRemitente = $arrayParamFrom[0]['valor1'];
            $arrayDestina = $this->serviceUtil->obtenerValoresParametro($arrayParamTo);
            $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->
                                                    getPlantillaXCodigoYEmpresa($arrayParamReubi[0]['valor3']);
            $arrayRespuesta['strAsunto']       = $arrayParamReubi[0]['valor2'];
            $arrayRespuesta['strCuerpoCorreo'] = $objPlantilla[0]->getPlantilla();
            $arrayRespuesta['strMensajeError'] = $arrayParamReubi[0]['valor4'];
            $arrayRespuesta['strOrigen']       = $arrayRegLogs[0]['valor1'];
            $arrayRespuesta['strMensajeGen']   = $arrayParamReubi[0]['valor5'];
            $arrayRespuesta['strWS']           = $arrayParamReubi[0]['valor6'];
            $arrayRespuesta['strRemitente']    = $strRemitente;
            $arrayRespuesta['arrayDestina']    = $arrayDestina;

        }
        catch (Exception $objEx)
        {
            if ($objEx->getMessage() == "ERROR_PARCIAL")
            {
                $arrayRespuesta['code'] = 102;

            }
            else
            {
                $strError = $objEx->getMessage();
                $arrayRespuesta['code'] = 100;
            }
            $arrayRespuesta['status'] = "ERROR";
            $arrayRespuesta['mensaje'] = $arrayParamReubi[0]['valor5'];
            $arrayRespuesta['strError'] = $strError;

        }

        return $arrayRespuesta;
    }

    /**
     * precioReubicacion
     *
     * Función encargada de retornar el precio a pagar por una Solicitud de Reubicacion.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 23/05/2023
     *
     * @param array $arrayParametros [
     *                                intIdProveedor    => Código del proveedor que realizará el proceso
     *                                strLoginCliente   => Login del cliente que realizará el proceso
     *                                strCanal          => Canal por el cual se realizará el proceso
     *                                strUsuario        => usuario realizará el proceso
     *                                strIpCliente      => ip del cliente
     *                                strCodEmpresa     => Codigo empresa
     *                               ]
     * @return array $arrayResponse [
     *                                code    => Codigo de error
     *                                status  => Estado de ejecución del proceso
     *                                mensaje => Mensaje de ejecución del proceso
     *                                data    => Data de respuesta
     *                               ]
     */

    public function precioReubicacion($arrayParametros)
    {
        list($arrayParametros, $intIdProveedor, $strLoginCliente, $strCanal,
            $intIdentificacionCliente, $strCodEmpresa, $strUsuarioAsigna, $strIpCliente,
            $strAppMethod) = $this->getVariables($arrayParametros);
        $strClass          = "TecnicoWSController";
        $arrayDataResponse = array();
        $arrayResponse     = array();
        $arrayConFigMeth   = array();
        $strError          = '';


        try
        {

            $arrayConFigMeth   = $this->obtenerValoresCorreoReu($strAppMethod);
            if($arrayConFigMeth['status'] == "ERROR")
            {
                $strError = $arrayConFigMeth['strError'];
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayParametrosLog['strOrigen']      = 'RegistroTecnicoWS';
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['request']        = $arrayParametros['data'];
            $arrayTokenCas                        = $this->serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            if (empty($strLoginCliente)  || empty($strCodEmpresa)  || empty($intIdentificacionCliente) ||
                empty($strUsuarioAsigna) || empty($intIdProveedor) || empty($strCanal))
            {
                $strError = 'No se han enviado los parámetros adecuados para procesar la información. - 
                strLoginCliente('.$strLoginCliente.'), strCodEmpresa('.$strCodEmpresa.'), strTipoProceso('
                    .$intIdentificacionCliente.'),'.'strUsuarioAsigna('.$strUsuarioAsigna.'), intIdProveedor('
                    .$intIdProveedor.').strCanal('.$strCanal.')';
                throw new Exception("NULL");
            }

            if ($strCodEmpresa != '18')
            {
                $strError = 'Codigo de empresa no se encuentra registrado en base';
                throw new Exception("ERROR_PARCIAL");
            }

            $arrayRequestLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);

            if ($arrayRequestLogs['status'] != 'OK' && $arrayRequestLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }


            $objPtoCliente = $this->emComercial->getRepository('schemaBundle:InfoPunto')->findBy(
                array('login' => $strLoginCliente));
            if (!is_object($objPtoCliente[0]))
            {
                $strError = 'No existe ese Login en base';
                throw new Exception("ERROR_PARCIAL");
            }

            $objSolititudReubi = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                ->obtenerPrecioPlanReubicacion(array('strNombrePlan' => 'REUBICACION',
                                                    'strCodEmpresa' => $strCodEmpresa));


            if (is_array($objSolititudReubi[0]) && !empty($objSolititudReubi[0]['floatPrecioPlan']))
            {
                $arrayResponse['code'] = 0;
                $arrayResponse['status'] = "OK";
                $arrayResponse['mensaje'] = str_replace("{{PRECIO}}",
                                                        $objSolititudReubi[0]['floatPrecioPlan'],
                                                        $arrayConFigMeth['strMensajeError']);
                $arrayDataResponse['precio'] = $objSolititudReubi[0]['floatPrecioPlan'];
            }
            else
            {
                $arrayResponse['code'] = 1;
                $arrayResponse['status'] = "OK";
                $arrayResponse['mensaje'] = $arrayConFigMeth['strMensajeGen'];
                $strError = 'No existe el precio por reubicacion configurado en base';
                throw new Exception("ERROR_PARCIAL");
            }
            $arrayResponse['data'] = $arrayDataResponse;

            $arrayParametrosLog['strOrigen']      = 'RegistroTecnicoWS';
            $arrayParametrosLog['strMetodo']      = $strAppMethod;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = '127.0.0.1';
            $arrayParametrosLog['strUsrUltMod']   = $strUsuarioAsigna;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['ipCliente']      = $strIpCliente;
            $arrayParametrosLog['response']       = $arrayResponse['data'];
            $arrayParametrosLog['token']          = $arrayTokenCas['strToken'];
            $arrayResponseLogs = $this->serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            if ($arrayResponseLogs['status'] != 'OK' && $arrayResponseLogs['code'] != 0)
            {
                $strError = 'No existe conexion con el microservicio de Logs';
                throw new Exception("ERROR_PARCIAL");
            }
            error_log("Respuesta que responde el web service " . print_r($arrayResponse,true));

        }
        catch (Exception $objEx)
        {

            $arrayResponse = $this->getArrayResponse($objEx, array(
                'arrayResponse' =>$arrayResponse, 'arrayConFigMeth'=>$arrayConFigMeth, 'strError'=>$strError,
                'strCanal'=>$strCanal,  'strAppMethod'=>$strAppMethod,
                'intIdentificacionCliente'=>$intIdentificacionCliente,'strLoginCliente'=>$strLoginCliente,
                'strClass'=>$strClass, 'intIdProveedor'=>$intIdProveedor));

        }

        return $arrayResponse;
    }


    /**
     * getArrayResponse
     *
     * Función encargada de retornar array de respuesta en caso de error de acuerdo al codigo de error.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 23/05/2023
     *
     * @param Exception $ex
     * @param $arrayParams
     * @return mixed
     */
    public function getArrayResponse(Exception $objEx,  array $arrayParams )
    {
        $arrayResponse   = $arrayParams['arrayResponse'];
        $arrayConFigMeth = $arrayParams['arrayConFigMeth'];
        $strError        = $arrayParams['strError'];
        $strCanal        = $arrayParams['strCanal'];
        $strAppMethod    = $arrayParams['strAppMethod'];
        $intIdentificacionCliente = $arrayParams['intIdentificacionCliente'];
        $strLoginCliente = $arrayParams['strLoginCliente'];
        $strClass        = $arrayParams['strClass'];
        $intIdProveedor  = $arrayParams['intIdProveedor'];
        if ($objEx->getMessage() == "NULL")
        {
            $arrayResponse['code'] = 101;
            $arrayResponse['status'] = "ERROR";
        }
        elseif ($objEx->getMessage() == "ERROR_PARCIAL")
        {
            if($arrayResponse['code'] ==1)
            {
                $arrayResponse['code'] = $arrayParams['arrayResponse']['code'];
                $arrayResponse['status'] = $arrayParams['arrayResponse']['status'];
            }
            else
            {
                $arrayResponse['code'] = 102;
                $arrayResponse['status'] = "ERROR";
            }

        }
        else
        {
            $strError = $objEx->getMessage();
            $arrayResponse['code'] = 100;
            $arrayResponse['status'] = "ERROR";
        }
        $arrayResponse['mensaje'] = $arrayConFigMeth['strMensajeGen'];
        $arrayResponse['data'] = $this->getArrayDataResponse($strAppMethod);
        $strError = '['.$intIdProveedor.']['.$strCanal.']-'.$strError;
        $strCuerpoCorreo = str_replace("{{CANAL}}", $strCanal, $arrayConFigMeth['strCuerpoCorreo']);
        $strCuerpoCorreo = str_replace("{{WS}}", $arrayConFigMeth['strWS'].$strAppMethod,
                                                                                            $strCuerpoCorreo);
        $strCuerpoCorreo = str_replace("{{IDENTIFICACION}}", $intIdentificacionCliente, $strCuerpoCorreo);
        $strCuerpoCorreo = str_replace("{{LOGINCLIENTE}}", $strLoginCliente, $strCuerpoCorreo);
        $strCuerpoCorreo = str_replace("{{ERRORTELCOS}}", $strError, $strCuerpoCorreo);

        $boolEnvioPlantilla = $this->serviceEnvioPlantilla->enviarCorreoFrom(
           array('strAsunto' =>$arrayConFigMeth['strAsunto'],
                 'strFrom'   =>$arrayConFigMeth['strRemitente'],
                 'arrayTo'   =>$arrayConFigMeth['arrayDestina'],
                 'strMensaje'=>$strCuerpoCorreo)
            );

        if (!$boolEnvioPlantilla)
        {
            $arrayErrorLog = array(
                'enterpriseCode' => "18",
                'logType' => 1,
                'logOrigin' => 'TELCOS',
                'application' => 'TELCOS',
                'appClass' => $strClass,
                'appMethod' => $strAppMethod,
                'descriptionError' => $objEx->getMessage(),
                'status' => 'Fallido',
                'dataResponse' => $arrayResponse['data'],
                'creationUser' => 'TELCOS');
        }
        return $arrayResponse;
    }

    /**
     *
     * getArrayDataResponse
     *
     * Función encargada de retornar el array data de respuesta en caso de error de acuerdo al codigo de error.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 23/05/2023
     *
     * @param $strAppMethod
     * @return array
     */
    public function getArrayDataResponse($strAppMethod)
    {
        $arrayDataResponse = array();
        if ($strAppMethod == 'getReubicacionPlanSuspensionTemporal')
        {
            $arrayDataResponse['idPlan'] = '';
            $arrayDataResponse['nombrePlan'] = '';
            $arrayDataResponse['plan'] = '';
        }
        elseif ($strAppMethod == 'getReubicacionServicioSuspendido')
        {
            $arrayDataResponse['idSuspension'] = '';
            $arrayDataResponse['suspension'] = '';

        }
        elseif ($strAppMethod == 'getReubicacionSolicitud')
        {
            $arrayDataResponse['idSolicitud'] = '';
            $arrayDataResponse['solicitud'] = '';
        }
        elseif ($strAppMethod == 'getReubicacionPrecioEquipo')
        {
            $arrayDataResponse['precio'] = '';
        }
        return $arrayDataResponse;
    }

    /**
     *
     * getVariables
     *
     * Función encargada de retornar Lista de variables a utilizar para los procesos de reubicacion.
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec>
     * @version 1.0 23/05/2023
     *
     * @param array $arrayParametros
     * @return array
     */
    public function getVariables(array $arrayParametros)
    {
        $intIdProveedor = (isset($arrayParametros['data']['idProveedor']) &&
            !empty($arrayParametros['data']['idProveedor']))
            ? $arrayParametros['data']['idProveedor'] : null;
        $strLoginCliente = (isset($arrayParametros['data']['login']) &&
            !empty($arrayParametros['data']['login']))
            ? $arrayParametros['data']['login'] : null;
        $strCanal = (isset($arrayParametros['data']['canal']) &&
            !empty($arrayParametros['data']['canal']))
            ? $arrayParametros['data']['canal'] : null;
        $intIdentificacionCliente = (isset($arrayParametros['data']['identificacionCliente']) &&
            !empty($arrayParametros['data']['identificacionCliente']))
            ? $arrayParametros['data']['identificacionCliente'] : null;
        $strCodEmpresa = (isset($arrayParametros['data']['codEmpresa']) &&
            !empty($arrayParametros['data']['codEmpresa']))
            ? $arrayParametros['data']['codEmpresa'] : null;
        $strUsuarioAsigna = (isset($arrayParametros['data']['usuario']) &&
            !empty($arrayParametros['data']['usuario']))
            ? $arrayParametros['data']['usuario'] : null;
        $strIpCliente = (isset($arrayParametros['data']['ipUsuario']) &&
            !empty($arrayParametros['data']['ipUsuario']))
            ? $arrayParametros['data']['ipUsuario'] : '127.0.0.1';
        $strAppMethod = (isset($arrayParametros['op']) &&
            !empty($arrayParametros['op']))
            ? $arrayParametros['op'] : null;
        return array($arrayParametros, $intIdProveedor, $strLoginCliente, $strCanal, $intIdentificacionCliente,
            $strCodEmpresa, $strUsuarioAsigna, $strIpCliente, $strAppMethod);
    }


}