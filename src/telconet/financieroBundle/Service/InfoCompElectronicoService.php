<?php

namespace telconet\financieroBundle\Service;
use telconet\schemaBundle\Entity\InfoMensajeCompElec;
use telconet\schemaBundle\Entity\ReturnResponse;

/**
 * Documentación para la clase 'InfoCompElectronicoService'.
 *
 * La clase InfoCompElectronicoService Contiene metodos para la generacion de los Documentos ATS en formato XML
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 15-08-2014
 */
class InfoCompElectronicoService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcomprobante;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $usercomprobante;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $pswdcomprobante;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $userfinan;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $pswdfinan;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $dsndatabase;

    //urlWeb service
    private $url_wsdl_ws;
    
    //user service
    private $user_ws;
    
    //password service
    private $passwd_ws;
    
    //urlWebGT serviceGT
    private $strUrlWsdlWsGt;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom            = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan          = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->userfinan        = $container->getParameter('user_financiero');
        $this->pswdfinan        = $container->getParameter('passwd_financiero');
        $this->emcomprobante    = $container->get('doctrine.orm.telconet_comprobante_entity_manager');
        $this->usercomprobante  = $container->getParameter('user_comprobantes');
        $this->pswdcomprobante  = $container->getParameter('passwd_comprobantes');
        $this->dsndatabase      = $container->getParameter('database_dsn');
        $this->url_wsdl_ws      = $container->getParameter('url_wsdl_ws_fact_electronica');
        $this->user_ws          = $container->getParameter('user_ws_fact_electronica');
        $this->passwd_ws        = $container->getParameter('passwd_ws_fact_electronica');
        $this->strUrlWsdlWsGt   = $container->getParameter('url_wsdl_ws_fact_electronica_gt');
    }

    /**
     * Documentación para el método 'getAts'.
     *
     * El metodo hace el llamado al procedimiento en la base el cual genera el ATS en formato XML
     * tambien retorna el tamaño del documento
     * 
     * 
     * Se ha usado OCI porque el enity manager de Doctrine no soporta
     * variables tipo clob devueltas desde un procedimiento
     *
     * @param array  $arrayParamsIn    Contiene los parametros para la generacion del XML(Anexo Transaccional)
     * (strIdEmpresa => Contiene el Id Empresa | strMes => Contiene el mes | strAnio => Contiene el Año
     *
     * @return array $arrayReturn
     * (
     * 'clobDocumentoAts' => Retorna el documento(ATS) generado por el procedimiento, 
     * 'nameFile' => Retorna el nombre del Anexo Transaccional, 
     * 'intTamanio' => Retorna el tamaño del Anexo Transaccional
     * 'strMessage' => Retorna un mensaje si ha ocurrido un error
     * 'boolCheck' => Retorna true o false
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */
    public function getAts($arrayParamsIn)
    {

        $intTamanio         = 0;
        $strMessageError    = "";
        $strPreEmpresa      = "";
        /* Se crea la fecha de inicio ($strFechaInicio) concatenando como default: "01" por ser el primer dia del mes,
         * se concatena el mes => strMes, a este se le aumenta un cero a la izquierda con la funcion str_pad
         * para que tenga formato de dos digitos y por ultimo se concatena el año
         */
        $strFechaInicio = '01-' . str_pad($arrayParamsIn['strMes'], 2, "0", STR_PAD_LEFT) . '-' . $arrayParamsIn['strAnio'];
        /* La funcion "cal_days_in_month" obtiene el ultimo dia del mes, enviandole como parametros
         * el mes y el año del cual se quiera obtener el ultimo dia.
         */
        $intNumDay = cal_days_in_month(CAL_GREGORIAN, $arrayParamsIn['strMes'], $arrayParamsIn['strAnio']);
        /* Se crea la fecha fin ($strFechaFin) concatenando "$intNumDay" como ultimo dia del mes, concatenando
         * al mes => strMes, a este se le aumenta un cero a la izquierda con la funcion str_pad para que tenga como
         * formato dos digitos y por ultimo se concatena el año
         */
        $strFechaFin = $intNumDay . '-' . str_pad($arrayParamsIn['strMes'], 2, "0", STR_PAD_LEFT) . '-' . $arrayParamsIn['strAnio'];
        $strSql = "BEGIN FNCK_CONSULTS.GET_ATS(:Pn_IdEmpresa, "
            . ":Pv_FechaInicio, :Pv_FechaFin, :Pxml_Ats, :Pn_Tamanio, :Pv_PreEmpresa, :Pv_MessageError); END;";
        try
        {
            //Obtiene la conexion
            $rscCon = oci_connect($this->userfinan, $this->pswdfinan, $this->dsndatabase) or $this->throw_exceptionOci(oci_error());

            //Prepara la sentencia 
            $rscStmt = oci_parse($rscCon, $strSql) or $this->throw_exceptionOci(oci_error());

            //Declaro variable tipo CLOB
            $clobAtsDocument = oci_new_descriptor($rscCon, OCI_D_LOB);

            //Enlazo las variables enviadas como parametros con las variables de entrada y salida del Procedimiento
            oci_bind_by_name($rscStmt, ':Pn_IdEmpresa', $arrayParamsIn['strIdEmpresa']) 
                or $this->throw_exceptionBind('Error al enlazar Pn_IdEmpresa con valor: ' . $arrayParamsIn['strIdEmpresa']);
            oci_bind_by_name($rscStmt, ':Pv_FechaInicio', $strFechaInicio) 
                or $this->throw_exceptionBind('Error al enlazar Pv_FechaInicio con valor ' . $strFechaInicio);
            oci_bind_by_name($rscStmt, ':Pv_FechaFin', $strFechaFin) 
                or $this->throw_exceptionBind('Error al enlazar Pv_FechaFin con valor ' . $strFechaFin);
            oci_bind_by_name($rscStmt, ':Pxml_Ats', $clobAtsDocument, -1, OCI_B_CLOB) 
                or $this->throw_exceptionBind('Error al enlazar Pxml_Ats');
            oci_bind_by_name($rscStmt, ':Pn_Tamanio', $intTamanio, 9) 
                or $this->throw_exceptionBind('Error al enlazar Pn_Tamanio');
            oci_bind_by_name($rscStmt, ':Pv_PreEmpresa', $strPreEmpresa, 10) 
                or $this->throw_exceptionBind('Error al enlazar Pv_PreEmpresa');
            oci_bind_by_name($rscStmt, ':Pv_MessageError', $strMessageError, 2000) 
                or $this->throw_exceptionBind('Error al enlazar Pv_MessageError');

            //Ejecutamos la sentencia
            oci_execute($rscStmt);

            //Pregunto si el parametro de mensaje de error es diferente de null
            if(!$strMessageError)
            {
                $strNameFile = $strPreEmpresa . '_' . $arrayParamsIn['strMes'] . '_' . $arrayParamsIn['strAnio'] . '.xml';

                $arrayReturn = array('clobDocumentoAts' => html_entity_decode($clobAtsDocument->load()),
                    'nameFile' => $strNameFile,
                    'intTamanio' => $intTamanio,
                    'boolCheck' => true);
            }
            else
            {
                //Si la variable $strMessageError no es nula devuelve el mensaje enviado desde el procediento
                $arrayReturn = array('strMessage' => $strMessageError,
                    '. ' => false);
            }
        }
        catch(\Exception $e)
        {
            $arrayReturn = array('strMessage' => $e->getMessage(),
                'boolCheck' => false);
            $this->insertErrorLog('ATS', 'getAts', $e->getMessage());
        }
        return $arrayReturn;
    }//getAts

    /**
     * Documentación para el método 'insertsDocumentoAts'.
     *
     * El metodo insertsDocumentoAts inserta los ATS descargados por los usuarios
     * lo cual queda registrado como log en la estructura info_anexo_transaccional
     * que permitira buscar el Anexo y su valor total generado en un instante de tiempo
     * y por que usuario fue generado.
     * 
     * Se ha usado OCI porque el enity manager de Doctrine no soporta
     * variables tipo clob, la cual se requiere para insertar el anexo transaccional generado.
     *
     * @param array  $arrayParamsIn    Contiene los parametros para la generacion del XML(Anexo Transaccional)
     * (strIdEmpresa => Contiene el Id Empresa | strMes => Contiene el mes | strAnio => Contiene el Año
     * strUsuario => Contiene el usuario cual descarga el archivo | clobDocumentoAts => contiene el archivo que se ha descargado
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */
    public function insertsDocumentoAts($arrayParamsIn)
    {
        $strSql = "BEGIN FNCK_TRANSACTION.INSERT_ANEXO_TRANSACCIONAL(:Pv_Mes, "
            . ":Pv_Anio, :Pv_AnexoTransaccional, :Pv_UsrCreacion, :Pv_EmpresaCod); END;";
        try
        {
            //Obtiene la conexion
            $rscCon = oci_connect($this->userfinan, $this->pswdfinan, $this->dsndatabase) or $this->throw_exceptionOci(oci_error());

            //Prepara la sentencia 
            $rscStmt = oci_parse($rscCon, $strSql) or $this->throw_exceptionOci(oci_error());
            $clobAtsDocument = oci_new_descriptor($rscCon, OCI_D_LOB);
            //Enlazo las variables enviadas como parametros con las variables de entrada y salida del Procedimiento
            oci_bind_by_name($rscStmt, ':Pv_Mes', $arrayParamsIn['strMes']) 
                or $this->throw_exceptionBind('Error al enlazar strMes con valor: ' . $arrayParamsIn['strMes']);
            oci_bind_by_name($rscStmt, ':Pv_Anio', $arrayParamsIn['strAnio']) 
                or $this->throw_exceptionBind('Error al enlazar strAnio con valor: ' . $arrayParamsIn['strAnio']);
            oci_bind_by_name($rscStmt, ':Pv_AnexoTransaccional', $clobAtsDocument, -1, OCI_B_CLOB) 
                or $this->throw_exceptionBind('Error al enlazar Pv_AnexoTransaccional');
            oci_bind_by_name($rscStmt, ':Pv_UsrCreacion', $arrayParamsIn['strUsuario']) 
                or $this->throw_exceptionBind('Error al enlazar strAnio con valor: ' . $arrayParamsIn['strUsuario']);
            oci_bind_by_name($rscStmt, ':Pv_EmpresaCod', $arrayParamsIn['strEmpresaCod']) 
                or $this->throw_exceptionBind('Error al enlazar strAnio con valor: ' . $arrayParamsIn['strEmpresaCod']);
            $clobAtsDocument->writetemporary(html_entity_decode($arrayParamsIn['clobDocumentoAts']));
            //Ejecutamos la sentencia
            oci_execute($rscStmt);
            $clobAtsDocument->free();
        }
        catch(\Exception $e)
        {
            $this->insertErrorLog('ATS', 'insertsDocumentoAts', $e->getMessage());
        }
    }

    /**
     * Documentación para el método 'insertErrorLog'.
     * El metodo (insertErrorLog) llama al procedimiento INSERT_ERROR en el paquete FNCK_TRANSACTION
     * 
     * @param  String    $strAplicacion   Se usa para enviar el nombre de la aplicacion de donde es llamado
     * el procedimiento
     * @param  String    $strProceso      Se usa para enviar el nombre del proceso en ejecucion    
     * @param  String    $strDetalleError Se usa para enviar el error capturado
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-28 conversión en public para poder invocarla desde otros Controller
     */
    public function insertErrorLog($strAplicacion, $strProceso, $strDetalleError)
    {
        try
        {
            $sql = "BEGIN FNCK_TRANSACTION.INSERT_ERROR(:Pv_Aplicacion, :Pv_Proceso, :Pv_DetalleError); END;";
            $stmt = $this->emfinan->getConnection()->prepare($sql);
            $stmt->bindParam('Pv_Aplicacion', $strAplicacion);
            $stmt->bindParam('Pv_Proceso', $strProceso);
            $stmt->bindParam('Pv_DetalleError', $strDetalleError);
            $stmt->execute();
        }
        catch(\Exception $ex)
        {
            echo $ex->getMessage();
        }
    }

    /**
     * Documentación para el método 'throw_exceptionBind'.
     *
     * Este metodo captura la excepcion al enlazar los parametros de entrada con las variables del procedimiento
     *
     * @param  string   $strMessage Contiene el mensaje enviado desde la funcion de la cual se requiere capturar la excepcion
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */
    private function throw_exceptionBind($strMessage)
    {
        throw new \Exception($strMessage);
    }

    /**
     * Documentación para el método 'throw_ExceptionOci'.
     *
     * Este metodo captura la excepcion al conectar y preparar la sentencia.
     *
     * @param  string   $strMessage Contiene el mensaje enviado desde la funcion de la cual se requiere capturar la excepcion
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */
    private function throw_ExceptionOci($objMessage)
    {
        throw new \Exception($objMessage['message']);
    }
    
    /**
     * Documentación para el método 'getCompElectronicosPdfXml'.
     * Este metodo retorna los documentos PDF y XML de los comprobantes electronicos
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     * @return array   $arrayDocumento Retorna el documento xml y pdf y un txt en caso de error
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-04-2018 Se setea valor para tiempo de timeout. 
     */
    public function getCompElectronicosPdfXml($intIdDocumento)
    {
        ini_set('default_socket_timeout', 400000);
        $arrayInfoComprobanteElectronico = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                          ->getClaveAccesobyId($intIdDocumento);
        $arrayDocumento = array('xml' => "",
            'pdf' => "",
            'txt' => "");
        try
        {
            if(!empty($arrayInfoComprobanteElectronico) && $arrayInfoComprobanteElectronico[0]["estado"] == 5)
            {
                $objWebServices = new \SoapClient($this->url_wsdl_ws, array('trace' => 1,
                                                                            'exceptions'=> 1,
                                                                            'connection_timeout'=> 2));
                $arrayParamsSend = array(array("rucEmpresa" => $arrayInfoComprobanteElectronico[0]["ruc"],
                        "claveAcceso" => $arrayInfoComprobanteElectronico[0]["claveAcceso"],
                        "usuario" => $this->user_ws,
                        "clave" => $this->passwd_ws
                ));
                $objResult = $objWebServices->__soapCall('consultaComprobanteArchivo', $arrayParamsSend);
                if($objResult)
                {
                    if($objResult->return->archivo == '' && $objResult->return->archivoPdf == '' && 
                       $objResult->return->mensajes->informacionAdicional != ''){
                        $arrayDocumento['txt'] = $objResult->return->mensajes->mensaje. ' '.$objResult->return->mensajes->informacionAdicional;
                    }else{
                        $arrayDocumento['xml'] = html_entity_decode($objResult->return->archivo);
                        $arrayDocumento['pdf'] = html_entity_decode($objResult->return->archivoPdf);
                    }
                }else{
                     $arrayDocumento['txt'] = 'No se obtuvo respuesta del web services.';
                }
            }
        }
        catch(\Exception $ex)
        {
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'getCompElectronicosPdfXml', $ex->getMessage());
            $arrayDocumento['txt'] = 'Existio un error - ' . $ex->getMessage();
        }
        return $arrayDocumento;
    }//getCompElectronicosPdfXml

    /**
     * Documentación para actualizarDatosContactoUsuario.
     * Método que realiza la llamada al WS de Comprobantes-Electrónicos para actualizar la información del cliente.
     *
     * @param  array $arrayParametros Recibe los parámetros para consumir el WS
     * @return array $arrayDocumento  Retorna un mensaje de éxito o error.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 05-04-2018
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 - Se obtiene el campo de la información adicional de la respuesta del WS.
     * @since 29-06-2018
     */
    public function actualizarDatosContactoUsuario($arrayParametros)
    {
        try
        {
            $objWebServices = new \SoapClient($this->url_wsdl_ws, array('trace'              => 1,
                                                                        'exceptions'         => 1,
                                                                        'connection_timeout' => 2));
            $arrayParametros["usuario"] = $this->user_ws;
            $arrayParametros["clave"]   = $this->passwd_ws;
            $objResult = $objWebServices->__soapCall('actualizarDatosContactoUsuario', array($arrayParametros));
            if($objResult)
            {
                $arrayRespuesta['mensaje'] = $objResult->return->detalle;
                $arrayRespuesta['estado']  = $objResult->return->estado;
                $arrayRespuesta['informacionAdicional'] = $objResult->return->mensajes->informacionAdicional;
            }else{
                $arrayRespuesta['mensaje'] = 'No se obtuvo respuesta del web service.';
                $arrayRespuesta['estado']  = 0;
                $arrayRespuesta['informacionAdicional'] ='No se obtuvo respuesta del web service.';
            }
        }
        catch(\Exception $ex)
        {
            $this->insertErrorLog('Agregar datos de envío', 'actualizarDatosContactoUsuario', $ex->getMessage());
            $arrayRespuesta['mensaje'] = 'Error al actualizar información en Comprobantes Electrónicos.';
            $arrayRespuesta['estado']  = 0;
            $arrayRespuesta['informacionAdicional'] ='Error al actualizar información en Comprobantes Electrónicos.';
        }
        return $arrayRespuesta;
    }//actualizarDatosContactoUsuario

    /**
     * Documentación para el método 'getMensajesCompElectronico'.
     * Obtiene el historial del comprobante electronico para ser mostrado en la vista.
     *
     * @param  array $arrayParametros Recibe el ID del Documento, y los limites de busqueda
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-02-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 06-05-2015
     * @since 1.1
     */
    public function getMensajesCompElectronico($arrayParametros)
    {
        try
        {
            //Obtiene los mensajes del comprobante
            $entityInfoMensajeCompElec = $this->emfinan->getRepository('schemaBundle:InfoMensajeCompElec')->getMensajesComprobantes($arrayParametros);
            //Itera los mensajes de comprobante
            foreach($entityInfoMensajeCompElec['arrayMensajes'] as $arrayIMCE ):
                $arrayInfoMensajeCompElec[] = array('tipo'                  => $arrayIMCE['tipo'],
                                                    'mensaje'               => $arrayIMCE['mensaje'],
                                                    'informacionAdicional'  => $arrayIMCE['informacionAdicional'],
                                                    'feCreacion'            => $arrayIMCE['feCreacion']->format('d/m/Y G:i')
                );
            endforeach;
            /*Si no tiene mensajes, se buscara el mensaje del error por el cual no se haya generado el comprobante y 
              se lo mostrara en el log de comprobantes electronicos.*/
            if(empty($entityInfoMensajeCompElec['arrayMensajes']))
            {
                //Se busca el comprobante electronico del documento
                $entityInfoComprobanteElectronico = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                                  ->findOneBy(array('documentoId' => $arrayParametros['intIdDocumento']));
                //De tenerlo se mostrara como mensaje Pendiente de Envio
                if(!empty($entityInfoComprobanteElectronico))
                {
                    $arrayInfoMensajeCompElec[] = array('tipo'                  => 'INFORMATIVO',
                                                        'mensaje'               => 'PENDIENTE DE ENVIO',
                                                        'informacionAdicional'  => 'EL DOCUMENTO SE ENCUENTRA PENDIENTE DE ENVIO',
                                                        'feCreacion'            => '');
                }
                else
                {
                    //Si no tiene comprobante se buscara por que causa no fue creado
                    $arrayInfoError = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                    ->getErrorMensajesFacturas($arrayParametros);
                    //Pregunta que tenga datos la variable
                    if($arrayInfoError['arrayMensajes'])
                    {
                        //Entra cuando no hay error en el metodo de busqueda getErrorMensajesFacturas
                        if(empty($arrayInfoError['strMensajeError']))
                        {
                            //Itera los mensajes de error
                            foreach($arrayInfoError['arrayMensajes'] as $objInfoError):
                                $arrayInfoMensajeCompElec[] = array('tipo'                  => 'ERROR',
                                                                    'mensaje'               => $objInfoError->getProceso(),
                                                                    'informacionAdicional'  => $objInfoError->getDetalleError(),
                                                                    'feCreacion'            => $objInfoError->getFeCreacion()->format('d/m/Y G:i'));                            
                            endforeach;
                            $entityInfoMensajeCompElec['intTotalMensajes']['intTotalMensajes'] = 
                                                                                             $arrayInfoError['intTotalMensajes']['intTotalMensajes'];
                        }
                        else
                        {
                            //Muestra el error ocurrido al realizar la busqueda
                            $arrayInfoMensajeCompElec[] = array('tipo'                  => 'ERROR',
                                                                'mensaje'               => 'ERROR',
                                                                'informacionAdicional'  => $arrayInfoError['strMensajeError'],
                                                                'feCreacion'            => '');
                        }
                    }
                    else
                    {
                        //Muestra que el documento no tiene comprobante electronico
                        $arrayInfoMensajeCompElec[] = array('tipo'              => 'INFORMATIVO',
                                                        'mensaje'               => 'VERIFICACION',
                                                        'informacionAdicional'  => 'EL DOCUMENTO NO CONTIENE COMPROBANTE ELECTRONICO',
                                                        'feCreacion'            => '');
                    }
                }
                
            }
            $arrayMensajesCompElec['arrayMensajes']    = $arrayInfoMensajeCompElec;
            $arrayMensajesCompElec['intTotalMensajes'] = $entityInfoMensajeCompElec['intTotalMensajes']['intTotalMensajes'];
        }
        catch(\Exception $ex)
        {
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'getMensajesCompElectronico', $ex->getMessage());
        }
        return $arrayMensajesCompElec;
    }//getMensajesCompElectronico
    
    /**
     * Documentación para el método 'getCompruebaEstadoComprobante'.
     * Obtiene 1 si el comprobante puede ser actualizado, solo podra actualizar el comprobante
     * siempre y cuando este en estado 0
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function getCompruebaEstadoComprobante($intIdDocumento, $intEstado){
        $boolCheck = false;
        $arrayDatos = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                                            ->getCompruebaEstadoComprobante($intIdDocumento, $intEstado);
        if(!empty($arrayDatos)){
            $boolCheck = true;
        }
        return $boolCheck;
    }
    /**
     * Documentación para el método 'actualizaComprobanteElectronico'.
     * Actualiza el comprobante que ha sido devuelto en estado 0 => con errores.
     *
     * @param  array $arrayParamsInDocument (
     * IdDocumento      Recibe el Id del documento
     * IdEmpresa        Recibe el Id de la empresa
     * IdTipoDocumento  Recibe el Tipo de documento FAC o NC
     * UsrModificacion  Recibe el usuario quien realiza la accion de modificar)
     * return $arrayParamsOutDocument(
     * RucEmpresa               Devuelve el Ruc de la empresa
     * NombreComprobante        Devuelve el nombre del comprobante
     * NombreTipoComprobante    Devuelve el nombre con el tipo del comprobante
     * Anio                     Devuelve el año
     * Mes                      Devuelve el mes
     * Dia                      Devuelve el dia
     * Message                  Devuelve un mensaje de error
     * boolCheck                Devuelve un true si se realizo la accion correctamente, false caso contrario.)
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     */
    public function actualizaComprobanteElectronico($arrayParamsInDocument)
    {
        $strSqlGetComprobanteElectronio = "BEGIN FNCK_COM_ELECTRONICO.COMP_ELEC_CAB(:Pn_IdDocumento,
                                                  :Pn_IdEmpresa,
                                                  :Pn_IdTipoDocumento,
                                                  :Pv_UsrCreacion,
                                                  :Pv_TipoTransaccion,
                                                  :Pv_RucEmpresa,
                                                  :Pclob_Comprobante,
                                                  :Pv_NombreComprobante,
                                                  :Pv_NombreTipoComprobante,
                                                  :Pv_Anio,
                                                  :Pv_Mes,
                                                  :Pv_Dia,
                                                  :Pv_MessageError); END;";
        try
        {
            //Obtiene la conexion
            $rscCon = oci_connect($this->userfinan, $this->pswdfinan, $this->dsndatabase) or $this->throw_exceptionOci(oci_error());

            //Prepara la sentencia
            $rscStmt = oci_parse($rscCon, $strSqlGetComprobanteElectronio) or $this->throw_exceptionOci(oci_error());

            //Declaro variable tipo CLOB
            $clobComprobanteElectronico = oci_new_descriptor($rscCon, OCI_D_LOB);

            $stmt = oci_parse($rscCon, $strSqlGetComprobanteElectronio);

            //Enlaza Pn_IdDocumento
            oci_bind_by_name($rscStmt, ':Pn_IdDocumento', $arrayParamsInDocument['IdDocumento']) 
                or $this->throw_exceptionBind('Error al enlazar Pn_IdDocumento');
            //Enlaza Pn_IdEmpresa
            oci_bind_by_name($rscStmt, ':Pn_IdEmpresa', $arrayParamsInDocument['IdEmpresa']) 
                or $this->throw_exceptionBind('Error al enlazar Pn_IdEmpresa');
            //Enlaza Pn_IdTipoDocumento
            oci_bind_by_name($rscStmt, ':Pn_IdTipoDocumento', $arrayParamsInDocument['IdTipoDocumento']) 
                or $this->throw_exceptionBind('Error al enlazar Pn_IdTipoDocumento');
            //Enlaza Pv_UsrCreacion
            oci_bind_by_name($rscStmt, ':Pv_UsrCreacion', $arrayParamsInDocument['UsrModificacion']) 
                or $this->throw_exceptionBind('Error al enlazar Pv_UsrCreacion');
            //Enlaza Pv_TipoTransaccion
            oci_bind_by_name($rscStmt, ':Pv_TipoTransaccion', $arrayParamsInDocument['TipoTransaccion']) 
                or $this->throw_exceptionBind('Error al enlazar Pv_UsrCreacion');
            //Enlaza Pv_RucEmpresa
            oci_bind_by_name($rscStmt, ':Pv_RucEmpresa', $arrayParamsOutDocument['RucEmpresa'], 13) 
                or $this->throw_exceptionBind('Error al enlazar Pv_RucEmpresa');
            //Enlaza Pclob_Comprobante
            oci_bind_by_name($rscStmt, ':Pclob_Comprobante', $clobComprobanteElectronico, -1, OCI_B_CLOB) 
                or $this->throw_exceptionBind('Error al enlazar Pclob_Comprobante');
            //Enlaza Pv_NombreComprobante
            oci_bind_by_name($rscStmt, ':Pv_NombreComprobante', $arrayParamsOutDocument['NombreComprobante'], 400) 
                or $this->throw_exceptionBind('Error al enlazar Pv_NombreComprobante');
            //Enlaza Pv_NombreTipoComprobante
            oci_bind_by_name($rscStmt, ':Pv_NombreTipoComprobante', $arrayParamsOutDocument['NombreTipoComprobante'], 400) 
                or $this->throw_exceptionBind('Error al enlazar Pv_NombreTipoComprobante');
            //Enlaza Pv_Anio
            oci_bind_by_name($rscStmt, ':Pv_Anio', $arrayParamsOutDocument['Anio'], 4) 
                or $this->throw_exceptionBind('Error al enlazar Pv_Anio');
            //Enlaza Pv_Mes
            oci_bind_by_name($rscStmt, ':Pv_Mes', $arrayParamsOutDocument['Mes'], 10) 
                or $this->throw_exceptionBind('Error al enlazar Pv_Mes');
            //Enlaza Pv_Dia
            oci_bind_by_name($rscStmt, ':Pv_Dia', $arrayParamsOutDocument['Dia'], 2) 
                or $this->throw_exceptionBind('Error al enlazar Pv_Dia');
            //Enlaza Pv_MessageError
            oci_bind_by_name($rscStmt, ':Pv_MessageError', $arrayParamsOutDocument['Message'], 2000) 
                or $this->throw_exceptionBind('Error al enlazar Pv_MessageError');
            //ejecuta la sentencia
            oci_execute($rscStmt);
            if($arrayParamsOutDocument['Message'] == null || $arrayParamsOutDocument['Message'] == ""){
                $arrayParamsOutDocument['Message'] = "Se actualizo el comprobante.";
                $arrayParamsOutDocument['boolCheck'] = true;
                $this->insertMensajeCompElec('INFORMATIVO', 
                                         'Actualizacion', 
                                         'Se actualizo el comprobante, el cual sera enviado al SRI', 
                                         $arrayParamsInDocument['IdDocumento']) 
                                            or $this->throw_exceptionBind('Error al llamar insertMensajeCompElec');
            }
        }
        catch(\Exception $ex)
        {
            $arrayParamsOutDocument['boolCheck'] = false;
            $arrayParamsOutDocument['Message'] = 'Error al tratar de actualizar el comprobante. '.$ex->getMessage();
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'actualizaComprobanteElectronico', $ex->getMessage());
        }
        return $arrayParamsOutDocument;
    }//actualizaComprobanteElectronico

    /**
     * Documentación para el método 'enviaNotificacionClienteComprobante'.
     * Envia notificacion de comprobantes electronicos
     * @author telcos
     * @version 1.0
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-04-2018 Se setea valor para tiempo de timeout. 
     * @param integer $intIdDocumento Recibe el id del documento
     * @param integer $intPuntoId     Recube el id del punto
     * @return array  $arrayResult    Retorna un boolCheck true cuando se realizo la accion correctamente y false en caso
     * contrario ambas con su respectivo mensaje de informacion.
     */
    public function enviaNotificacionClienteComprobante($intIdDocumento, $intPuntoId)
    {
        ini_set('default_socket_timeout', 400000);
        try
        {   
            $entityFormaContacto = $this->emfinan->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                                            ->findCorreosPorPunto($intPuntoId);
            $arrayInfoComprobanteElectronico = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                                                            ->getClaveAccesobyId($intIdDocumento);
            $arrayInfoDocumentoComp = $this->emcomprobante->getRepository('schemaBundle:InfoDocumentoComp')
                                                                                            ->getDocumentoFinan($intIdDocumento);
            $strCorreos = str_replace(",", ";", $entityFormaContacto);
            //Preguntamos si trae correo para hacer uso del metodo de EnviaPDFCorreo
            if(strlen($strCorreos) > 1)
            {
                $objWebServices = new \SoapClient($this->url_wsdl_ws);
                $arrayParamsSend = array(array("idDocumento" => $arrayInfoDocumentoComp[0]['id'], 
                "correo" => $strCorreos

                ));
                $objWsResult = $objWebServices->__soapCall('enviaPdfCorreo', $arrayParamsSend);
                $arrayResult = array('strMensaje' => $objWsResult->return, 'boolStatus' => true);
            }
            else
            {
                //si no tiene correo no se realizara la accion y retornara un mensaje de advertencia
                $arrayResult = array('strMensaje' => 'No enviado, revise si el usuario tiene correo', 'boolStatus' => false);
            }
        }
        catch(\Exception $ex)
        {
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'enviaNotificacionClienteComprobante', $ex->getMessage());
            $arrayResult = array('strMensaje' => 'No enviado, existio un error: ' . $ex->getMessage(), 'boolStatus' => false);
        }
        return $arrayResult;
    }//enviaNotificacionClienteComprobante

    /**
     * Inserta el mensaje de la accion que se ha realizado con el comprobante en este caso
     * se actualiza el comprobante
     * @param type $strTipo                 Recibe el tipo de mensaje
     * @param type $strMensaje              Recibe el mensaje
     * @param type $strInformacionAdicional Recibe informacion adicional
     * @param type $intDocumentoId          Recibe el id del documento
     * @return boolean                      Retorna true si realizo correctamente la accion, false caso contrario.
     */
    private function insertMensajeCompElec($strTipo, $strMensaje, $strInformacionAdicional, $intDocumentoId)
    {
        $boolCheck = true;
        try
        {
            $entityIDFC = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intDocumentoId);
            $entityInfoMensajeCE = new InfoMensajeCompElec();
            $entityInfoMensajeCE->setDocumentoId($entityIDFC);
            $entityInfoMensajeCE->setTipo($strTipo);
            $entityInfoMensajeCE->setMensaje($strMensaje);
            $entityInfoMensajeCE->setInformacionAdicional($strInformacionAdicional);
            $entityInfoMensajeCE->setFeCreacion(new \DateTime('now'));
            $this->emfinan->persist($entityInfoMensajeCE);
            $this->emfinan->flush();
        }
        catch(\Exception $ex)
        {
            $boolCheck = false;
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'insertMensajeCompElec', $ex->getMessage());
        }
        return $boolCheck;
    }//insertMensajeCompElec
    
    /**
     * obtieneNombreMes, retorna el nombre del mes enviando el numero del mes
     * @param   integer $intMes Obtiene el numero del mes
     * @return  string  Retirna el nombre del mes
     */
    public function obtieneNombreMes($intMes)
    {
        $arrayNombreMes = array('1' => 'Enero',
                                '2' => 'Febrero', 
                                '3' => 'Marzo', 
                                '4' => 'Abril', 
                                '5' => 'Mayo', 
                                '6' => 'Junio', 
                                '7' => 'Julio', 
                                '8' => 'Agosto', 
                                '9' => 'Septiembre', 
                                '10' => 'Octubre', 
                                '11' => 'Noviembre', 
                                '12' => 'Diciembre');
        return $arrayNombreMes[$intMes];
    }//obtieneNombreMes

    /** 
     * transaccionComprobanteElectronico, genera la consulta para simular un comprobante electronico
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @param array $arrayParametros[
     *                              'intIdDocumento'        => Recibe el id del documento
     *                              'intIdEmpresa'          => Recibe el id de la empresa
     *                              'intIdTipoDocumento'    => Recibe el tipo de documento id
     *                              'strUsuario'            => Recibe el usuario
     *                              'strTipoTransaccion'    => Recibe el tipo de transaccion, NINGUNA(No realiza ninguna accion), 
     *                                                         INSERT(Genera el comprobante en la base), 
     *                                                         UPDATE(Actualiza el comprobante en la base)
     *                              ]
     * @version 1.0 28-06-2016
     *
     * @return \telconet\schemaBundle\Entity\ReturnResponse -> $arrayParamsOutDocument[
     *                                                                                'strRucEmpresa'            => Retorna el ruc de la empresda
     *                                                                                'strNombreComprobante'     => Retorna el nombre del comprobante
     *                                                                                'strNombreTipoComprobante' => Retorna el nombre del tipo de doc
     *                                                                                'strAnio'                  => Retorna el año de creacion del xml
     *                                                                                'strMes'                   => Retorna el mes de creacion del xml
     *                                                                                'strDia'                   => Retorna el dia de creacion del xml
     *                                                                                'strMessage'               => Retorna un mensaje si existe un 
     *                                                                                                              error
     *                                                                                ]
     */
    public function transaccionComprobanteElectronico($arrayParametros)
    {
        $objReturnResponse                                    = new ReturnResponse();
        $arrayParamsOutDocument                               = array();
        $arrayParamsOutDocument['clobComprobanteElectronico'] = '<comprobante>Comprobante no existe</comprobante>';
        $arrayParamsOutDocument['strNombreComprobante']       = 'ComprobanteNoExiste';
        $strSqlGetComprobanteElectronio = "BEGIN FNCK_COM_ELECTRONICO.COMP_ELEC_CAB(:Pn_IdDocumento,
                                                  :Pn_IdEmpresa,
                                                  :Pn_IdTipoDocumento,
                                                  :Pv_UsrCreacion,
                                                  :Pv_TipoTransaccion,
                                                  :Pv_RucEmpresa,
                                                  :Pclob_Comprobante,
                                                  :Pv_NombreComprobante,
                                                  :Pv_NombreTipoComprobante,
                                                  :Pv_Anio,
                                                  :Pv_Mes,
                                                  :Pv_Dia,
                                                  :Pv_MessageError); END;";
        try
        {
            //Obtiene la conexion
            $rscCon = oci_connect($this->userfinan, $this->pswdfinan, $this->dsndatabase) or $this->throw_exceptionOci(oci_error());

            //Prepara la sentencia
            $rscStmt = oci_parse($rscCon, $strSqlGetComprobanteElectronio) or $this->throw_exceptionOci(oci_error());

            //Declaro variable tipo CLOB
            $clobComprobanteElectronico = oci_new_descriptor($rscCon, OCI_D_LOB);

            oci_parse($rscCon, $strSqlGetComprobanteElectronio);

            //Enlaza Pn_IdDocumento
            oci_bind_by_name($rscStmt, ':Pn_IdDocumento', $arrayParametros['intIdDocumento'])
                or $this->throw_exceptionBind('Error al enlazar Pn_IdDocumento');
            //Enlaza Pn_IdEmpresa
            oci_bind_by_name($rscStmt, ':Pn_IdEmpresa', $arrayParametros['intIdEmpresa'])
                or $this->throw_exceptionBind('Error al enlazar Pn_IdEmpresa');
            //Enlaza Pn_IdTipoDocumento
            oci_bind_by_name($rscStmt, ':Pn_IdTipoDocumento', $arrayParametros['intIdTipoDocumento'])
                or $this->throw_exceptionBind('Error al enlazar Pn_IdTipoDocumento');
            //Enlaza Pv_UsrCreacion
            oci_bind_by_name($rscStmt, ':Pv_UsrCreacion', $arrayParametros['strUsuario'])
                or $this->throw_exceptionBind('Error al enlazar Pv_UsrCreacion');
            //Enlaza Pv_TipoTransaccion
            oci_bind_by_name($rscStmt, ':Pv_TipoTransaccion', $arrayParametros['strTipoTransaccion'])
                or $this->throw_exceptionBind('Error al enlazar Pv_UsrCreacion');
            //Enlaza Pv_RucEmpresa
            oci_bind_by_name($rscStmt, ':Pv_RucEmpresa', $arrayParamsOutDocument['strRucEmpresa'], 13)
                or $this->throw_exceptionBind('Error al enlazar Pv_RucEmpresa');
            //Enlaza Pclob_Comprobante
            oci_bind_by_name($rscStmt, ':Pclob_Comprobante', $clobComprobanteElectronico, -1, OCI_B_CLOB)
                or $this->throw_exceptionBind('Error al enlazar Pclob_Comprobante');
            //Enlaza Pv_NombreComprobante
            oci_bind_by_name($rscStmt, ':Pv_NombreComprobante', $arrayParamsOutDocument['strNombreComprobante'], 400)
                or $this->throw_exceptionBind('Error al enlazar Pv_NombreComprobante');
            //Enlaza Pv_NombreTipoComprobante
            oci_bind_by_name($rscStmt, ':Pv_NombreTipoComprobante', $arrayParamsOutDocument['strNombreTipoComprobante'], 400)
                or $this->throw_exceptionBind('Error al enlazar Pv_NombreTipoComprobante');
            //Enlaza Pv_Anio
            oci_bind_by_name($rscStmt, ':Pv_Anio', $arrayParamsOutDocument['strAnio'], 4)
                or $this->throw_exceptionBind('Error al enlazar Pv_Anio');
            //Enlaza Pv_Mes
            oci_bind_by_name($rscStmt, ':Pv_Mes', $arrayParamsOutDocument['strMes'], 10)
                or $this->throw_exceptionBind('Error al enlazar Pv_Mes');
            //Enlaza Pv_Dia
            oci_bind_by_name($rscStmt, ':Pv_Dia', $arrayParamsOutDocument['strDia'], 2)
                or $this->throw_exceptionBind('Error al enlazar Pv_Dia');
            //Enlaza Pv_MessageError
            oci_bind_by_name($rscStmt, ':Pv_MessageError', $arrayParamsOutDocument['strMessage'], 2000)
                or $this->throw_exceptionBind('Error al enlazar Pv_MessageError');
            //ejecuta la sentencia
            oci_execute($rscStmt);

            if($clobComprobanteElectronico)
            {
                $arrayParamsOutDocument['clobComprobanteElectronico'] = $clobComprobanteElectronico->load();
                $arrayParamsOutDocument['strNombreComprobante']       = $arrayParamsOutDocument['strNombreComprobante'];
            }

            $objReturnResponse->setRegistros($arrayParamsOutDocument);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);

        }
        catch(\Exception $ex)
        {
            $arrayParamsOutDocument['clobComprobanteElectronico'] = '<error>Existio un error</error>';
            $arrayParamsOutDocument['strNombreComprobante']       = 'Error';
            $objReturnResponse->setRegistros($arrayParamsOutDocument);
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR);
            $this->insertErrorLog('TELCOS',
                                  'transaccionComprobanteElectronico',
                                  $arrayParametros['strUsuario'] . ' ' .
                                  new \DateTime('now') . ' ' .$ex->getMessage());
        }
        return $objReturnResponse;
    }//transaccionComprobanteElectronico

    /**
     * Documentación para el método 'anularComprobanteElectronico'.
     * Este metodo anula un comprobante electronico en DB_COMPROBANTES previa verificacion en el SRI
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     * @return array   $arrayDocumento Retorna el documento xml y pdf y un txt en caso de error
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 08-08-2016
     */
    public function anularComprobanteElectronico($intIdDocumento)
    {
        $arrayInfoComprobanteElectronico = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                         ->getClaveAccesobyId($intIdDocumento);
        $arrayDocumento = array('claveAcceso'   => "",
                                'estado'        => "",
                                'detalle'       => "",
                                'txt'           => "");
        try
        {
            if(!empty($arrayInfoComprobanteElectronico) && $arrayInfoComprobanteElectronico[0]["estado"] == 5)
            {
                $arrayDocumento['claveAcceso'] = $arrayInfoComprobanteElectronico[0]["claveAcceso"];
                
                $objWebServices = new \SoapClient($this->url_wsdl_ws, array('trace' => 1,
                                                                            'exceptions'=> 1,
                                                                            'connection_timeout'=> 2));
                $arrayParamsSend = array(array("rucEmpresa"     => $arrayInfoComprobanteElectronico[0]["ruc"],
                                                "claveAcceso"   => $arrayDocumento['claveAcceso'],
                                                "usuario"       => $this->user_ws,
                                                "clave"         => $this->passwd_ws
                                                ));
                $objResult = $objWebServices->__soapCall('anularComprobanteElectronico', $arrayParamsSend);
                if($objResult)
                {
                    $arrayDocumento['estado']       = $objResult->return->estado;
                    $arrayDocumento['detalle']      = $objResult->return->detalle;
                    $arrayDocumento['txt']          = $objResult->return->mensajes->mensaje. ' '.$objResult->return->mensajes->informacionAdicional;
                }
                else
                {
                    $arrayDocumento['claveAcceso']  = $arrayInfoComprobanteElectronico[0]["claveAcceso"];
                    $arrayDocumento['txt']          = 'No se obtuvo respuesta del web services.';
                }
            }
            else
            {
                $arrayDocumento['txt'] = 'No se encontró comprobante electrónico asociado.';
            }
        }
        catch(\Exception $ex)
        {
            $this->insertErrorLog('FACTURACION ELECTRONICA', 'anularComprobanteElectronico', $ex->getMessage());
            $arrayDocumento['txt'] = 'Existio un error - ' . $ex->getMessage();
        }
        return $arrayDocumento;
    }//anularComprobanteElectronico
    
    /**
     * Documentación para el método 'getCompruebaEstadoComprobante'.
     * Obtiene 1 si el comprobante puede ser actualizado, solo podra actualizar el comprobante
     * siempre y cuando este en estado 0
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 29-06-2017
     */
    public function getVerificaComprobanteByEstado($intIdDocumento, $arrayEstados)
    {
        $boolCheck = false;
        $arrayDatos = $this->emfinan->getRepository('schemaBundle:InfoComprobanteElectronico')
                                     ->getVerificaComprobanteByEstado($intIdDocumento, $arrayEstados);
        
        if(!empty($arrayDatos)){
            $boolCheck = true;
        }
        return $boolCheck;
    }//getVerificaComprobanteByEstado
    
    
     /**
     * Documentación para el método 'facturaElectronicaGt'.
     * 
     * Este metodo retorna realiza el consumo del Ws para la facturación electronica de GT.
     * 
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     * @param  Array   $arrayDatosFace Recibe el un array con todos los datos de la facturación
     * 
     * @return array  $arrayDocumento  Devuelve un array con la respuesta del web service
     * de la facturación electrónica.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 11-03-2019
     */
    public function facturaElectronicaGt($arrayDatosFace)
    {
          ini_set('default_socket_timeout', 400000);
          $strNoAplica     = 'N/A';
          $arrayDetalleDte = array();
          $arrayDocumento  = array('anotaciones'            => "",
                                    'descripcion'            => "",
                                    'valido'                 => "",
                                    'numeroDocumento'        => "",
                                    'numeroDte'              => "",
                                    'rangoFinalAutorizado'   => "",
                                    'rangoInicialAutorizado' => "" );
         
        try
        {
                $objWebServices  = new \SoapClient($this->strUrlWsdlWsGt, array('trace'             => 1,
                                                                                 'exceptions'        => 1,
                                                                                 'connection_timeout'=> 2));
                
                foreach ($arrayDatosFace['detalleDte'] as $strDetalle) 
                {
                    $arrayDetalleDte[] = array("cantidad"                          => $strDetalle['cantidad'],
                                                "codigoProducto"                    => $strDetalle['codigoProducto'],
                                                "descripcionProducto"               => $strDetalle['descripcionProducto'],
                                                "detalleImpuestosIva"               => $strDetalle['detalleImpuestosIva'],
                                                "importeExento"                     => $strDetalle['importeExento'],
                                                "importeNetoGravado"                => $strDetalle['importeNetoGravado'],
                                                "importeOtrosImpuestos"             => $strDetalle['importeOtrosImpuestos'],
                                                "importeTotalOperacion"             => $strDetalle['importeTotalOperacion'],
                                                "montoBruto"                        => $strDetalle['montoBruto'],
                                                "montoDescuento"                    => $strDetalle['montoDescuento'],
                                                "personalizado_01"                  => $strDetalle['personalizado_01'],
                                                "personalizado_02"                  => $strDetalle['personalizado_02'],
                                                "personalizado_03"                  => $strDetalle['personalizado_03'],
                                                "personalizado_04"                  => $strDetalle['personalizado_04'],
                                                "personalizado_05"                  => $strDetalle['personalizado_05'],
                                                "personalizado_06"                  => $strDetalle['personalizado_06'],
                                                "precioUnitario"                    => $strDetalle['precioUnitario'],
                                                "tipoProducto"                      => $strDetalle['tipoProducto'],
                                                "unidadMedida"                      => $strDetalle['unidadMedida']);
                    
                }// ($arrayDatosFace['detalleDte'] as $strDetalle)
              
                $arrayParamsSend = array( "registrarDte"=>array(
                                                    "dte"=>array("clave"                             => $arrayDatosFace['clave'],
                                                    "dte"=>array("cae"                               => $strNoAplica,
                                                                  "codigoEstablecimiento"             => $arrayDatosFace['codigoEstablecimiento'],
                                                                  "codigoMoneda"                      => $arrayDatosFace['codigoMoneda'],
                                                                  "correoComprador"                   => $arrayDatosFace['correoComprador'],
                                                                  "departamentoComprador"             => $arrayDatosFace['departamentoComprador'],
                                                                  "departamentoVendedor"              => $arrayDatosFace['departamentoVendedor'],
                                                                  "descripcionOtroImpuesto"           => $arrayDatosFace['departamentoVendedor'],
                                                                  $arrayDetalleDte,
                                                                  "detalleImpuestosIva"               => $arrayDatosFace['detalleImpuestosIva'],
                                                                  "direccionComercialComprador"       => $arrayDatosFace['direccionComercialComprador'],
                                                                  "direccionComercialVendedor"        => $arrayDatosFace['direccionComercialVendedor'],
                                                                  "estadoDocumento"                   => $arrayDatosFace['estadoDocumento'],
                                                                  "fechaAnulacion"                    => $arrayDatosFace['fechaAnulacion'],
                                                                  "fechaDocumento"                    => $arrayDatosFace['fechaDocumento'],
                                                                  "fechaResolucion"                   => $arrayDatosFace['fechaResolucion'],
                                                                  "idDispositivo"                     => $arrayDatosFace['idDispositivo'],
                                                                  "importeBruto"                      => $arrayDatosFace['importeBruto'],
                                                                  "importeDescuento"                  => $arrayDatosFace['importeDescuento'],
                                                                  "importeNetoGravado"                => $arrayDatosFace['importeNetoGravado'],
                                                                  "importeOtrosImpuestos"             => $arrayDatosFace['importeOtrosImpuestos'],
                                                                  "importeTotalExento"                => $arrayDatosFace['importeTotalExento'],
                                                                  "montoTotalOperacion"               => $arrayDatosFace['montoTotalOperacion'],
                                                                  "municipioComprador"                => $arrayDatosFace['municipioComprador'],
                                                                  "municipioVendedor"                 => $arrayDatosFace['municipioVendedor'],
                                                                  "nitComprador"                      => $arrayDatosFace['nitComprador'],
                                                                  "nitGFACE"                          => $arrayDatosFace['nitGFACE'],
                                                                  "nitVendedor"                       => $arrayDatosFace['nitVendedor'],
                                                                  "nombreComercialComprador"          => $arrayDatosFace['nombreComercialComprador'],
                                                                  "nombreComercialRazonSocialVendedor"=> $arrayDatosFace['nombreComercialRazonSocialVendedor'],
                                                                  "nombreCompletoVendedor"            => $arrayDatosFace['nombreCompletoVendedor'],
                                                                  "numeroDocumento"                   => $arrayDatosFace['numeroDocumento'],
                                                                  "numeroDte"                         => $arrayDatosFace['numeroDte'],
                                                                  "numeroResolucion"                  => $arrayDatosFace['numeroResolucion'],
                                                                  "observaciones"                     =>"N/A",
                                                                  "personalizado_01"                  =>"N/A",
                                                                  "personalizado_02"                  =>"N/A",
                                                                  "personalizado_03"                  =>"N/A",
                                                                  "personalizado_04"                  =>"N/A",
                                                                  "personalizado_05"                  =>"N/A",
                                                                  "personalizado_06"                  =>"N/A",
                                                                  "personalizado_07"                  =>"N/A",
                                                                  "personalizado_08"                  =>"N/A",
                                                                  "personalizado_09"                  =>"N/A",
                                                                  "personalizado_10"                  =>"N/A",
                                                                  "personalizado_11"                  =>"N/A",
                                                                  "personalizado_12"                  =>"N/A",
                                                                  "personalizado_13"                  =>"N/A",
                                                                  "personalizado_14"                  =>"N/A",
                                                                  "personalizado_15"                  =>"N/A",
                                                                  "personalizado_16"                  =>"N/A",
                                                                  "personalizado_17"                  =>"N/A",
                                                                  "personalizado_18"                  =>"N/A",
                                                                  "personalizado_19"                  =>"N/A",
                                                                  "personalizado_20"                  =>"N/A",
                                                                  "rangoFinalAutorizado"              =>"N/A",
                                                                  "rangoInicialAutorizado"            =>"N/A",
                                                                  "regimen2989"                       => $arrayDatosFace['regimen2989'],
                                                                  "regimenISR"                        => $arrayDatosFace['regimenISR'],
                                                                  "serieAutorizada"                   => $arrayDatosFace['serieAutorizada'],
                                                                  "serieDocumento"                    => $arrayDatosFace['serieDocumento'],
                                                                  "telefonoComprador"                 => $arrayDatosFace['telefonoComprador'],
                                                                  "tipoCambio"                        => $arrayDatosFace['tipoCambio'],
                                                                  "tipoDocumento"                     => $arrayDatosFace['tipoDocumento'],),
                                                                  "usuario"                           => $arrayDatosFace['usuario'],
                                                                  "validador"                         =>"N/A")));
                 
                $objResult = $objWebServices->__soapCall('registrarDte', $arrayParamsSend);

                if($objResult)
                {  
                    if($objResult->return->valido)
                    {
                       $arrayDocumento['anotaciones']            = html_entity_decode($objResult->return->anotaciones);
                       $arrayDocumento['descripcion']            = html_entity_decode($objResult->return->descripcion);
                       $arrayDocumento['valido']                 = html_entity_decode($objResult->return->valido);
                       $arrayDocumento['numeroDocumento']        = html_entity_decode($objResult->return->numeroDocumento);
                       $arrayDocumento['numeroDte']              = html_entity_decode($objResult->return->numeroDte);
                       $arrayDocumento['rangoFinalAutorizado']   = html_entity_decode($objResult->return->rangoFinalAutorizado);
                       $arrayDocumento['rangoInicialAutorizado'] = html_entity_decode($objResult->return->rangoInicialAutorizado);
                    }
                    else
                    {
                       $arrayDocumento['descripcion'] = html_entity_decode($objResult->return->descripcion);
                    }// ($objResult->return->valido)
                }
                else
                {
                     throw new \Exception("No se obtuvo respuesta del web service.");
                }// ($objResult)
      
        }
        catch(\Exception $ex)
        {
            $arrayDocumento['txt'] = 'Existio un error - ' . $ex->getMessage();
        }
        return $arrayDocumento;
    }//facturaElectronicaGt
 
}
