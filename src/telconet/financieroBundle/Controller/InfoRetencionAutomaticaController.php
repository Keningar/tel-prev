<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoCab;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoDet;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\comercialBundle\Service\InfoContratoDigitalService;
use telconet\financieroBundle\Service\InfoPagoService;
use telconet\financieroBundle\Service\InfoPagoDetService;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use DOMDocument;

/**
 * InfoRetencionAutomatica controller.
 *
 */
class InfoRetencionAutomaticaController extends Controller implements TokenAuthenticatedController
{

    /**
     * @Secure(roles="ROLE_65-7457")
     * indexAction()
     * Función que renderiza la página principal de retenciones subidas mediante un archivo.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 22-01-2021
     * @since 1.0
     * 
     * @return render - Página de Consulta de estados de cuenta - pagos.
     */
    public function indexAction()
    {          
        return $this->render('financieroBundle:infoRetencionAutomatica:index.html.twig', array());
    }
    /**
     * getEstadosAction()
     * Función que retorna los estados para consulta de las estados de cuenta.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 22-01-2020
     * @since 1.0
     *    
     * @return $objResponse - Listado de Estados.
     */
    public function getEstadosAction() 
    {
        $arrayEstados[] = array('id' => 'Pendiente', 'nombre' => 'Pendiente');
        $arrayEstados[] = array('id' => 'Procesado', 'nombre' => 'Procesado');
        $arrayEstados[] = array('id' => 'Eliminado', 'nombre' => 'Eliminado');	
        $objResponse = new Response(json_encode(array('estados_cta' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
   /**
     * @Secure(roles="ROLE_65-7817")
     * gridRetencionesAction()
     * Función que obtiene un listado de cabeceras de retenciones.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 22-01-2021
     * @since 1.0
     *
     * @return $objResponse 
     */
    public function gridRetencionesAction()
    {
        $objRequest     = $this->getRequest();
        $strFechaDesde  = $objRequest->get("strFechaDesde");
        $strFechaHasta  = $objRequest->get("strFechaHasta");
       
        $objSession     = $objRequest->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $emFinanciero   = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'TIPO FORMA PAGO',
                                                               'empresaCod'  => $strEmpresaCod,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
                $strTipoFormaPago  = $objAdmiParametroDet->getValor1();
            }
        }          

        $arrayParametros                       = array();
        $arrayParametros['strFechaDesde']      = $strFechaDesde;
        $arrayParametros['strFechaHasta']      = $strFechaHasta;
        $arrayParametros['strEmpresaCod']      = $strEmpresaCod;
        $arrayParametros['strTipoFormaPago']   = $strTipoFormaPago;

        $arrayResultado   = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')->getPagosAutomaticosPorCriterios($arrayParametros);
        $arrayRegistros   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        $arrayRetenciones = array();
        foreach($arrayRegistros as $arrayDatos):    
            $strEditFeAutorizacion = 'S';
            $strEstado             = $arrayDatos['strEstado'];
            $strCliente            = $arrayDatos['strCliente'];
            $strReferencia         = $arrayDatos['strReferencia'];
            $intIdPagoAutomatico   = $arrayDatos['intIdPagoAutomatico'];
            $strFeCreacion         = strval(date_format($arrayDatos['dateFeCreacion'], "Y-m-d"));
            $strUsrCreacion        = $arrayDatos['strUsrCreacion'];
            
            $arrayRetenciones[] =   array( 'intIdPagoAutomatico'  => $intIdPagoAutomatico,
                                           'strCliente'           => $strCliente,
                                           'strReferencia'        => $strReferencia,
                                           'strEstado'            => $strEstado,
                                           'strFeCreacion'        => $strFeCreacion,
                                           'strUsrCreacion'       => $strUsrCreacion,                                                          
                                           'strOpAcciones'    => array('linkVer'=> $this->generateUrl('infoRetencionAutomatica_verDetalleRetencion', 
                                                                 array('intIdPagoAutomatico' => $intIdPagoAutomatico)),
                                                                       'intIdPagoAutomatico' => $intIdPagoAutomatico,
                                                                       'strEditFechaAut'     => $strEditFeAutorizacion,
                                                                       'strEstado'           => $strEstado)
                                   );
        endforeach;
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayRetenciones)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_65-7817")
     * newAction()
     * Función que renderiza la página principal para subir retención mediante un archivo xml.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 22-01-2021
     * @since 1.0
     * 
     * @return render
     */
    public function newAction()
    {          
        return $this->render('financieroBundle:infoRetencionAutomatica:new.html.twig', array());
    }

    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * documentosFileUploadAction     *
     * Método encargado de procesar el archivo subido en el formulario y coloca en el directorio de destino físico y después guarda 
     * en la base de forma logica.
     *
     * @return json con resultado del proceso
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-01-2021 
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 13-07-2021 Se agrega funcionalidad para estandarizar lectura de formatos de xml de retención.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 17-09-2021 Se agrega funcionalidad para  lectura de uno o varios formatos de xml de retención.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.3 08-02-2022 Se modifica el apartado de traer los datos correspondientes al nfs.
     */ 
    public function documentosFileUploadAction()
    {
        $objRequest                = $this->getRequest();        
        $objSession                = $objRequest->getSession();
        $strUser                   = $objSession->get('user');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $arrayPuntoSession         = $objSession->get('ptoCliente');
        $intIdPuntoSession         = (!empty($arrayPuntoSession['id'])) ? $arrayPuntoSession['id'] : -1;
        $strFechaActual            = strval(date_format(new \DateTime('now'), "dmYGi"));
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil               = $this->get('schema.Util');

        $arrayInfoFile             = $_FILES['xml_retencion'];
        $arrayArchivos             = $arrayInfoFile["name"];
        $arrayPathArchivos         = $arrayInfoFile["tmp_name"];
        $intContArchivos           = 0;
        /*Definimos las variables necesarias para el servicio.*/
        $strApp                    = '';
        $strSubModulo              = '';
        $strResultadoFinal         = '';
        $strResultado              = '';
        $strArchivo                = '';
        $strSplitChar              = '*';
        $strSplitRet               = '/';
        $objRespuesta              = new Response();     
        
        $objRespuesta->headers->set('Content-Type', 'text/html');
        try
        {
            if($arrayInfoFile && count($arrayInfoFile) > 0)
            {
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                                          'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                        'descripcion' => 'CONFIGURACION NFS',
                                                                        'empresaCod'  => $strCodEmpresa,
                                                                        'estado'      => 'Activo'));
                    if(is_object($objAdmiParametroDet))
                    {
                        $strPathAdicional  = $objAdmiParametroDet->getValor1();
                        $strApp            = $objAdmiParametroDet->getValor2();
                        $strSubModulo      = $objAdmiParametroDet->getValor3();
                    }
                    else
                    {
                        throw new \Exception('Error, no existe la configuración requerida para PATH ADICIONAL ');
                    }                          
                }
                $arrayPathAdicional[] = array('key' => $strPathAdicional);
                foreach ($arrayArchivos as $strKey => $strValue) 
                {
                    $strFileNameOrig  = $arrayArchivos[$strKey];
                    $arrayArchivo     = explode('.', $strFileNameOrig);
                    $intCountArray    = count($arrayArchivo);
                    $strNombreArchivo = $arrayArchivo[0];
                    $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
                    $strPrefijo       = substr(md5(uniqid(rand())), 0, 6);

                    if(($strExtArchivo && ($strExtArchivo == 'xml' || $strExtArchivo == 'XML')))
                    {
                        $strNuevoNombre = "RET_" . $strPrefijo ."_".$strFechaActual. "." . $strExtArchivo;
                        $strTmpName           = $arrayPathArchivos[$strKey];       
                        $strArchivo      = base64_encode(file_get_contents($strTmpName));
                        $arrayParamNfs   = array(
                                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                                'strApp'               => $strApp,
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => $strArchivo,
                                                'strNombreArchivo'     => $strNuevoNombre,
                                                'strUsrCreacion'       => $strUser,
                                                'strSubModulo'         => $strSubModulo);
                        $arrayUbicacionResponse = $serviceUtil->guardarArchivosNfs($arrayParamNfs);

                        if($arrayUbicacionResponse['intStatus']=='500')
                        {
                            throw new \Exception($arrayUbicacionResponse['strMensaje']);    
                        }

                        $strTargetPath = $arrayUbicacionResponse['strUrlArchivo'];
                        
                        $arrayParametros                           = array();
                        $arrayParametros['strNuevoNombre']         = $strNuevoNombre;
                        $arrayParametros['strDestino']             = $strTargetPath;
                        $arrayParametros['objRequest']             = $objRequest;
                        $arrayParametros['strTipoModulo']          = $strExtArchivo;
                        $arrayParametros['strNombreDocumento']     = $strNombreArchivo;
                        $arrayParametros['intIdPuntoSession']      = $intIdPuntoSession;
                        $arrayParametros['strUser']                = $strUser;
                        $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
                        $arrayParametros['strTargetPath']          = $strTargetPath;
                        $arrayParametros['strClientIp']            = $objRequest->getClientIp();


                        $strResultado     = $this->isProcesarArchivo($arrayParametros);
                        if($strResultado==='OK')
                        {
                            $strResultadoFinal .= '¡{'.$strFileNameOrig.'}{Archivo subido correctamente.}!';                                
                        }
                        else
                        {
                            $strResultadoFinal .= '¡{'.$strFileNameOrig.'}{'.$strResultado.'}!';                              
                        }
 

                    }
                    else
                    {
                        $strResultado = 'ERROR';
                        $strResultadoFinal .= '¡{'.$strFileNameOrig.'}{Extensión de archivo no válida.Favor verificar.}!';                        
                    }
                    $intContArchivos++;
                }
                if($strResultado==='OK')
                {                
                    $strResultadoFinal .= $strSplitChar.$strResultado.$strSplitRet;
                }
                else
                {
                    $strResultadoFinal .= $strSplitChar.''.$strSplitRet;
                }
            }
            else
            {
                throw new \Exception('Error : Archivo no válido.'); 
            }

        }
        catch(\Exception $e)
        {
            $strResultado = error_log('ERROR ===> '.$e->getMessage());
            if(strlen($e->getMessage())<100)
            {
                $strResultadoFinal .= $strSplitRet.'{Error al procesar Archivos:} '.$strSplitRet.'  {Detalle:} '.$e->getMessage();
            }
            else 
            {
                $strResultadoFinal  .= $strSplitRet.'Error al procesar Archivo. Favor revisar formato. ';
            }
        }
        $strResponse = htmlspecialchars($strResultadoFinal);
        $objRespuesta->setContent($strResponse);

        return $objRespuesta;        
    }

    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * isGuardarDocumento
     *
     * Método encargado de guardar la información  relacionada al nuevo documento
     *          
     * @return boolean
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-01-2021 
     * 
     */ 
    public function isGuardarDocumento($arrayParametros)
    {
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            //se crea el documento que incurre en esta comunicacion
            $objInfoDocumento = new InfoDocumento();
            $objInfoDocumento->setNombreDocumento($arrayParametros['strNombreDocumento']);
            $objInfoDocumento->setUbicacionFisicaDocumento($arrayParametros['strDestino']);
            $objInfoDocumento->setUbicacionLogicaDocumento($arrayParametros['strNuevoNombre']);
            $objInfoDocumento->setMensaje($arrayParametros['strMensaje']);
            $objInfoDocumento->setEstado('Activo');
            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
            $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
            $objInfoDocumento->setIpCreacion($arrayParametros['objRequest']->getClientIp());
            $objInfoDocumento->setUsrCreacion($arrayParametros['strUser']);
            $objInfoDocumento->setEmpresaCod($arrayParametros['strCodEmpresa']);
            $emComunicacion->persist($objInfoDocumento);
            $emComunicacion->flush();
            $emComunicacion->getConnection()->commit();
            $emComercial->getConnection()->commit();

            return true;
        }
        catch(\Exception $e)
        {   error_log('Error en isGuardarDocumento '.$e->getMessage());
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }                   

            return false;
        }
    }

    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * isProcesarArchivo
     *
     * Método encargado de leer y guardar la información  relacionada al nuevo documento.
     *          
     * @return boolean
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-01-2021 
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 06-07-2021 Se agrega validación para lectura de tag fechaAutorización, validación para leactura de base imponible (retención 
     *                         a la fuente). 
     * 
     * @version 1.2 16-07-2021 Se agregan validaciones para formatos escalados. Se agrega  consideración en formato de porcentaje a retener.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.3 27-08-2021 Se limpia la razon social de exceso de espacios
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.4 30-08-2021 Se agrega validacion de varias facturas en una misma retencion
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 31-08-2021 Se modifica la validacion de factura no encontrada.
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.6 06-09-2021 Se valida que el numero de referencia no este guardado con el mismo usuario en base
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.7 19-10-2021 Se se agrega validación para verificar duplicidad previo al registro de una retención.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 08-11-2021 Se se agrega validación para verificar lectura de campo identificación por medio de tag 'ruc' usando substring a 
     *                         partir de la ubicación del tag razón social.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.9 30-12-2021 Se cambia la longitud del substring de razon social desde el tag nombreComercial (si hubiere), hasta el tag estab.
     *                         Se valida que los tags impuesto tenga sus respectivos subtags.
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.10 04-02-2022 Se extrae la posicion del nombreComercial a partir de la primera ocurrencia.
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.11 14-03-2022 Se modifica la funcion para no duplicar detalles de pagos automaticos
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.12 11-07-2022 Se cierra la conexion antes de abrir una nueva.
     */ 
    public function isProcesarArchivo($arrayParametros)
    {
        $strResultado          = 'OK';
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $servicePagoAutomatico = $this->get('financiero.InfoPagoAutomatico'); 
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strAbsolutePath       = $arrayParametros['strTargetPath'];
        $boolSumarizaBaseImp   = false;
        $boolErrorAdicional    = false;
        $boolNuevoFormato      = false;
        $boolNuevoFormatoAd    = false;
        $boolFormatoC          = false;
        $boolFormatoValido     = false;
        $boolTagRetenciones    = false;
        $boolError             = false;
        $strEstadoHist         = 'Pendiente';
        $strEstado             = 'Pendiente';
        $strObservacionHist    = 'Se crea retención en estado Pendiente';
        $strRucEmpresa         = '';
        $floatTotalBaseImp     = 0;
        $floatBaseImpCalc      = 0;
        $arrayPorcentRetener   = array();        
        $objInfoEmpresaGrupo   = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
        
        if($emFinanciero->getConnection()->isTransactionActive())
        {
            $emFinanciero->getConnection()->close(); 
        }

        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            if(is_object($objInfoEmpresaGrupo))
            {
                $strRucEmpresa = $objInfoEmpresaGrupo->getRuc();
            }
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                              'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'TIPO FORMA PAGO',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParametroDet))
                {
                    $strTipoFormaPago  = $objAdmiParametroDet->getValor1();
                }
                
                $objAdmiParamDetCdata = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'FORMATO_A',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetCdata))
                {
                    $strReplaceValor1  = $objAdmiParamDetCdata->getValor1();
                    $strReplaceValor2  = $objAdmiParamDetCdata->getValor2();
                    $strReplaceValor3  = $objAdmiParamDetCdata->getValor3();
                    $strReplaceValor4  = $objAdmiParamDetCdata->getValor4();
                    $strReplaceValor5  = $objAdmiParamDetCdata->getValor5();
                    $strReplaceValor6  = $objAdmiParamDetCdata->getValor6();
                }                
                
                $objAdmiParamDetCdataB = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'FORMATO_B',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetCdataB))
                {
                    $strReplaceValor7  = $objAdmiParamDetCdataB->getValor1();
                    $strReplaceValor8  = $objAdmiParamDetCdataB->getValor2();
                    $strReplaceValor9  = $objAdmiParamDetCdataB->getValor3();
                    $strReplaceValor10 = $objAdmiParamDetCdataB->getValor5();
                }
                
                $objAdmiParamDetFormatoC = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'FORMATO_C',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetFormatoC))
                {
                    $strTagRespuestaComprobante  = $objAdmiParamDetFormatoC->getValor1();
                    $strTagClaveAccesoConsultada = $objAdmiParamDetFormatoC->getValor2();
                    $strTagNumeroComprobantes    = $objAdmiParamDetFormatoC->getValor3();
                    $strTagAutorizaciones        = $objAdmiParamDetFormatoC->getValor4();
                    $strTagAutorizacion          = $objAdmiParamDetFormatoC->getValor5();
                    $strReplaceValor11           = $objAdmiParamDetFormatoC->getValor6();
                    $strReplaceValor12           = $objAdmiParamDetFormatoC->getValor7();
                }

                $objAdmiParamDetFormatoD = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'FORMATO_D',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetFormatoD))
                {
                    $strReplaceValor13           = $objAdmiParamDetFormatoD->getValor1();
                    $strReplaceValor14           = $objAdmiParamDetFormatoD->getValor2();
                    $strReplaceValor15           = $objAdmiParamDetFormatoD->getValor3();
                    $strReplaceValor16           = $objAdmiParamDetFormatoD->getValor4();
                    $strReplaceValor17           = $objAdmiParamDetFormatoD->getValor5();
                    $strReplaceValor18           = $objAdmiParamDetFormatoD->getValor6();
                    $strReplaceValor19           = $objAdmiParamDetFormatoD->getValor7();
                } 
                
                $objAdmiParamDetFormatoE = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'FORMATO_E',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetFormatoE))
                {
                    $strReplaceValor20     = $objAdmiParamDetFormatoE->getValor1();
                    $strReplaceValor21     = $objAdmiParamDetFormatoE->getValor2();
                    $strReplaceValor22     = $objAdmiParamDetFormatoE->getValor3();
                    $strReplaceValor23     = $objAdmiParamDetFormatoE->getValor4();
                    $strReplaceValor24     = $objAdmiParamDetFormatoE->getValor5();
                    $strReplaceValor25     = $objAdmiParamDetFormatoE->getValor6();
                    $strReplaceValor26     = $objAdmiParamDetFormatoE->getValor7(); 
                }                 
                $objAdmiParamDetFormatoF = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'FORMATO_F',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
                if(is_object($objAdmiParamDetFormatoF))
                {
                    $intLongituMax               = intval($objAdmiParamDetFormatoF->getValor1());
                    $strTagInfoAdicional         = $objAdmiParamDetFormatoF->getValor2();
                    $strTagInfoFactura           = $objAdmiParamDetFormatoF->getValor3();
                    $strTagXmlVersion            = $objAdmiParamDetFormatoF->getValor4();
                    $strTagRazonSocialComprador  = $objAdmiParamDetFormatoF->getValor5();
                    $strTagCodigoRetenc          = $objAdmiParamDetFormatoF->getValor6();
                    $strTagAgenteRetencion       = $objAdmiParamDetFormatoF->getValor7(); 
                }
                $objTagComprobante = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                 'descripcion' => 'TAG COMPROBANTE',
                                                                 'empresaCod'  => $strCodEmpresa,
                                                                 'estado'      => 'Activo'));
                if(is_object($objTagComprobante))
                {
                    $strTagAutorizacion       = $objTagComprobante->getValor2();
                    $strTagEstado             = $objTagComprobante->getValor2();
                    $strTagFechaAutorizacion  = $objTagComprobante->getValor4();
                    $strTagComprobante        = $objTagComprobante->getValor6();
                    $strTagCompRetencion      = $objTagComprobante->getValor7();
                }
                
                $objTagInfoCompRetencion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                       'descripcion' => 'TAG INFO COMP RETENCION',
                                                                       'empresaCod'  => $strCodEmpresa,
                                                                       'estado'      => 'Activo'));
                if(is_object($objTagInfoCompRetencion))
                {
                    $strTagInfoCompRetencion             = $objTagInfoCompRetencion->getValor1();
                    $strTagRazonSocialSujetoRetenido     = $objTagInfoCompRetencion->getValor5();
                    $strTagIdentificacionSujetoRetenido  = $objTagInfoCompRetencion->getValor6();
                }

                $objTagInfoNombreComercial = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                       'descripcion' => 'TAG NOMBRE COMERCIAL',
                                                                       'empresaCod'  => $strCodEmpresa,
                                                                       'estado'      => 'Activo'));
                if(is_object($objTagInfoNombreComercial))
                {
                    $strTagNombreComercial               = $objTagInfoNombreComercial->getValor1();
                }

                $objTagInfoTributaria = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                    'descripcion' => 'TAG INFO TRIBUTARIA',
                                                                    'empresaCod'  => $strCodEmpresa,
                                                                    'estado'      => 'Activo'));
                if(is_object($objTagInfoTributaria))
                {
                    $strTagInfoTributaria   = $objTagInfoTributaria->getValor1();
                    $strTagRazonSocial      = $objTagInfoTributaria->getValor2();
                    $strTagRuc              = $objTagInfoTributaria->getValor3();
                    $strTagEstab            = $objTagInfoTributaria->getValor5();
                    $strTagPtoEmi           = $objTagInfoTributaria->getValor6();
                    $strTagSecuencial       = $objTagInfoTributaria->getValor7();
                }
                
                $objTagImpuestos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'TAG IMPUESTOS',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
                if(is_object($objTagImpuestos))
                {
                    $strTagImpuestos          = $objTagImpuestos->getValor1();
                    $strTagImpuesto           = $objTagImpuestos->getValor2();
                    $strTagCodigo             = $objTagImpuestos->getValor3();
                    $strTagBaseImponible      = $objTagImpuestos->getValor4();
                    $strTagPorcentaje         = $objTagImpuestos->getValor5();
                    $strTagValorRetener       = $objTagImpuestos->getValor6();
                    $strTagNumDocSustento     = $objTagImpuestos->getValor7(); 
                }                
                $objTagRetenciones = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                 'descripcion' => 'TAG RETENCIONES',
                                                                 'empresaCod'  => $strCodEmpresa,
                                                                 'estado'      => 'Activo'));
                if(is_object($objTagRetenciones))
                {
                    $strTagRetenciones  = $objTagRetenciones->getValor1();
                    $strTagRetencion    = $objTagRetenciones->getValor2();
                    $strTagCodigo       = $objTagRetenciones->getValor3();
                    $strTagCodRetencion = $objTagRetenciones->getValor4();
                }
                
                $objTagDocSustento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                    'descripcion' => 'TAG DOC SUSTENTO',
                                                                    'empresaCod'  => $strCodEmpresa,
                                                                    'estado'      => 'Activo'));
                if(is_object($objTagDocSustento))
                {
                    $strTagDocsSustento     = $objTagDocSustento->getValor1();
                    $strTagDocSustento      = $objTagDocSustento->getValor2();
                    $strTagNumDocSustento   = $objTagDocSustento->getValor4();                    
                }

                $objTagImpDocSustento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                    'descripcion' => 'TAG IMPUESTOS DOC SUSTENTO',
                                                                    'empresaCod'  => $strCodEmpresa,
                                                                    'estado'      => 'Activo'));
                if(is_object($objTagImpDocSustento))
                {
                    $strTagImpDocsSustento     = $objTagDocSustento->getValor1();
                    $strTagImpDocSustento      = $objTagDocSustento->getValor2();
                }                
                $objTagFecha = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                    'descripcion' => 'TAG FECHA',
                                                                    'empresaCod'  => $strCodEmpresa,
                                                                    'estado'      => 'Activo'));
                if(is_object($objTagFecha))
                {
                    $strTagYear            = $objTagFecha->getValor1();
                    $strTagMonth           = $objTagFecha->getValor2();
                    $strTagDay             = $objTagFecha->getValor3();
                    $strTagFeAutorizacion  = $objTagFecha->getValor4();                    
                }
                
                $objParamEstadoXml = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                 'descripcion' => 'ESTADO XML',
                                                                 'empresaCod'  => $strCodEmpresa,
                                                                 'estado'      => 'Activo'));
                if(is_object($objParamEstadoXml))
                {
                    $strEstadoXmlPendiente   = $objParamEstadoXml->getValor1();
                    $strEstadoXmlAutorizado  = $objParamEstadoXml->getValor2();
                }
                
                $objParamMargenError = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                 'descripcion' => 'MARGEN ERROR',
                                                                 'empresaCod'  => $strCodEmpresa,
                                                                 'estado'      => 'Activo'));
                if(is_object($objParamMargenError))
                {
                    $strMargenError   = $objParamMargenError->getValor1();
                    $floatMargenError = round(floatval($strMargenError),2);
                }                
                
            }
            else
            {
                throw new \Exception('Parámetro no existente. Favor revisar.');
            }

            $objAdmiParDetRetFuente = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                  'descripcion' => 'COD_RET_FUENTE',
                                                                  'empresaCod'  => $strCodEmpresa,
                                                                  'estado'      => 'Activo'));

            if(is_object($objAdmiParDetRetFuente))
            {
                $strCodigoRetFuente = $objAdmiParDetRetFuente->getValor1();
            }

            $objAdmiParDetRetIva = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                  'descripcion' => 'COD_RET_IVA',
                                                                  'empresaCod'  => $strCodEmpresa,
                                                                  'estado'      => 'Activo'));
            if(is_object($objAdmiParDetRetIva))
            {
                $strCodigoRetIva = $objAdmiParDetRetIva->getValor1();
            }

            if(!isset($strCodigoRetFuente)||!isset($strCodigoRetIva))
            {
                $strResultado  = 'Código de retención es inválido. Favor revisar.';
            } 

            $objFormaPagoExcluida = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                 'descripcion' => 'FORMA PAGO EXCLUIDA',
                                                                 'empresaCod'  => $strCodEmpresa,
                                                                 'estado'      => 'Activo'));
            if(is_object($objFormaPagoExcluida))
            {
                $intFormaPagoExcluida  = intval($objFormaPagoExcluida->getValor1());
            }            
            $objXmlDocument = file_get_contents($arrayParametros['strTargetPath']);
            if(strpos($objXmlDocument,$strTagFeAutorizacion)!==false)
            {
               $objXmlDocument     = str_replace('   class="fechaAutorizacion"','', $objXmlDocument);
               $objXmlDocument     = str_replace("   class='fechaAutorizacion'",'', $objXmlDocument);                
               $objXmlDocument     = str_replace(' class="fechaAutorizacion"','', $objXmlDocument);
               $objXmlDocument     = str_replace(" class='fechaAutorizacion'",'', $objXmlDocument);
               $objXmlDocument     = str_replace('orig__','', $objXmlDocument);

               if(strpos($objXmlDocument,$strTagYear)===false)
               {
                    $strFechaRetencion  = substr ($objXmlDocument,(strpos($objXmlDocument,$strTagFeAutorizacion)+18),10);
                    if(strpos($strFechaRetencion,'/') > 0)
                    {
                        if(strpos($strFechaRetencion,' ') > 0)
                        {
                            $arrayFeTransaccionNew  =  explode(' ', $strFechaRetencion);
                            $arrayFeTransaccion     =  explode('/', $arrayFeTransaccionNew[0]);
                        }
                        else
                        {
                            $arrayFeTransaccion  =  explode('/', $strFechaRetencion);
                        }
                    }
                    else
                    {
                        $arrayFeTransaccion  =  explode('-', $strFechaRetencion);
                    }                                                             

                    if(count($arrayFeTransaccion)==3)
                    {
                        if(in_array($arrayFeTransaccion[2],range(1900,2500)) && in_array($arrayFeTransaccion[1],range(1,12)))
                        {
                            $strFechaRetencion = $arrayFeTransaccion[2].'/'.$arrayFeTransaccion[1].'/'.$arrayFeTransaccion[0];
                        }
                        else if(in_array($arrayFeTransaccion[0],range(1900,2500)) && in_array($arrayFeTransaccion[1],range(1,12)))
                        {
                            $strFechaRetencion = $arrayFeTransaccion[0].'/'.$arrayFeTransaccion[1].'/'.$arrayFeTransaccion[2];
                        }                            
                        else 
                        {
                            $strFechaRetencion = $arrayFeTransaccion[2].'/'.$arrayFeTransaccion[0].'/'.$arrayFeTransaccion[1];
                        }                            
                    }                    
               }
               else
               {
                   $strAnio = str_pad(substr ($objXmlDocument,(strpos($objXmlDocument,$strTagYear)+5),4),4, "0", STR_PAD_LEFT);
                   if(strpos(substr($objXmlDocument,(strpos($objXmlDocument,$strTagMonth)+6),2),'<')!==false)
                   {
                       $strMes  = str_pad(substr($objXmlDocument,(strpos($objXmlDocument,$strTagMonth)+6),1),2, "0", STR_PAD_LEFT);
                   }
                   else
                   {
                       $strMes  = str_pad(substr ($objXmlDocument,(strpos($objXmlDocument,$strTagMonth)+6),2),2, "0", STR_PAD_LEFT);
                   }

                   if(strpos(substr($objXmlDocument,(strpos($objXmlDocument,$strTagDay)+4),2),'<')!==false)
                   {
                       $strDia  = str_pad(substr($objXmlDocument,(strpos($objXmlDocument,$strTagDay)+4),1),2, "0", STR_PAD_LEFT);
                   }
                   else
                   {
                       $strDia  = str_pad(substr ($objXmlDocument,(strpos($objXmlDocument,$strTagDay)+4),2),2, "0", STR_PAD_LEFT);
                   }                    

                   $strFechaRetencion = $strAnio.'-'.$strMes.'-'.$strDia;
               }
            }
            $objXmlDocumentTmp = $objXmlDocument;
            $objXmlDocument = preg_replace('([^A-Za-z0-9.&; ])', '', $objXmlDocument);
            
            if(strpos($objXmlDocument,$strTagInfoFactura)!==false)
            {
                $strResultado = 'Formato ingresado no es una retención válida. Favor verificar.';
            }             
            if(strpos($objXmlDocument,$strTagXmlVersion)!==false)
            {
                $strPosIni = strpos($objXmlDocument,'xml');
            } 
            else if(strpos($objXmlDocument,$strTagAutorizacion)!==false)
            {
                $strPosIni = strpos($objXmlDocument,$strTagAutorizacion);
            }              
            else if(strpos($objXmlDocument,$strTagCompRetencion)!==false)
            {
                $strPosIni = strpos($objXmlDocument,$strTagCompRetencion);
            }            
            if(strpos($objXmlDocument,$strTagInfoAdicional)!==false)
            {
                $strPosFin = strripos($objXmlDocument,$strTagInfoAdicional);
            }            
            else if(strpos($objXmlDocument,$strTagRetenciones)!==false)
            {
                $strPosFin = strripos($objXmlDocument,$strTagRetenciones);
            }
            else if(!isset($strPosFin))
            {
                $strPosFin   = strripos($objXmlDocument,$strTagImpuestos);
            }

            $strLong         = $strPosFin - $strPosIni;
            $strComprobante  = substr ($objXmlDocument,$strPosIni,$strLong);
            
            if(strlen($strComprobante) === 0)
            {
                $strResultado = 'Formato no soportado. Favor revisar.';
            }            
            else if(strlen($strComprobante) <= $intLongituMax)
            {
                $strComprobante = str_replace($strReplaceValor20,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor21,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor8,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor22,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor23,"", $strComprobante);                
                $strComprobante = str_replace($strReplaceValor24,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor25,"", $strComprobante);
                $strComprobante = str_replace($strReplaceValor26,"", $strComprobante);
                $strComprobante = str_replace($strTagImpuestos,"", $strComprobante);
                $strComprobante = str_replace($strTagImpDocSustento,"", $strComprobante);
                $strComprobante = str_replace($strTagRazonSocialSujetoRetenido,"", $strComprobante);
                $strComprobante = str_replace($strTagRazonSocialComprador,"", $strComprobante);
                $strComprobante = str_replace('razon1SocialDestinatario',"", $strComprobante);
                $strComprobante = str_replace('razon1SocialTransportista',"", $strComprobante);
                

                $strComprobante  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getVarcharClean($strComprobante);
                $strPosIni = strpos($strComprobante,$strTagIdentificacionSujetoRetenido)+28;
                $strPosFin = strripos($strComprobante,$strTagIdentificacionSujetoRetenido)-1;
                $strLong   = $strPosFin - $strPosIni;

                $strRucSujetoRetenido = substr ($strComprobante,$strPosIni,$strLong);
                if(isset($strRucSujetoRetenido))

                {
                    $strPosFinRazonSocial = strripos($strComprobante,$strTagRazonSocial);
                    $strPosFinEstab = strpos($strComprobante,$strTagEstab);
                    $strLon = $strPosFinEstab - $strPosFinRazonSocial;
                    $strSubStringComprobante  = substr ($strComprobante,$strPosFinRazonSocial,$strLon);
                    $strLonNomCom=strpos($strComprobante, $strTagNombreComercial);
                    if($strLonNomCom>0)
                    {
                        $strPosFinRazonSocial = strpos($strComprobante, $strTagNombreComercial, $strLonNomCom + strlen($strTagNombreComercial) );
                        $strSubStringComprobante  = substr ($strComprobante,$strPosFinRazonSocial);
                    }
                    $strPosIni = strpos($strSubStringComprobante,$strTagRuc)+3; 
                    $strPosFin = strripos($strSubStringComprobante,$strTagRuc);
                    $strLong   = $strPosFin - $strPosIni;                   
                    $strIdentificacion =  substr ($strSubStringComprobante,$strPosIni,$strLong);
                    if(strlen($strIdentificacion)>13)
                    {
                        $strIdentificacion = substr ($strSubStringComprobante,$strPosIni,13);
                    }

                    $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findByIdentificacionTipoRolEmpresa($strIdentificacion, 'Cliente', $strCodEmpresa);
                    if(is_object($objPersonaEmpresaRol))
                    {
                        $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),"estado"=>"Activo"));
                        if(is_object($objContrato))
                        {
                            $intIdFormaPago = $objContrato->getFormaPagoId()->getId();
                            $objFormaPago   = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
                            if(is_object($objFormaPago) && $intIdFormaPago === $intFormaPagoExcluida)
                            {
                                $strResultado = 'Cliente con forma de pago no permitida ('
                                    .strtoupper($objFormaPago->getDescripcionFormaPago()).') para registro de Retención.'
                                    . 'Favor Verificar.';
                                return $strResultado;                                
                            }
                        }                        
                    }
                    
                    $strPosIni = strpos($strComprobante,$strTagRazonSocial)+11;
                    $strPosFin = strripos($strComprobante,$strTagRazonSocial);
                    $strLong   = $strPosFin - $strPosIni;              
                    $strRazonSocial =  substr ($strComprobante,$strPosIni,$strLong);
                    $strRazonSocial = preg_replace('/\s+/', ' ', $strRazonSocial);
                    $strRazonSocial = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                    ->getVarcharClean($strRazonSocial);                    
                    $strPosIni = strpos($strComprobante,$strTagEstab)+5;
                    $strPosFin = strpos($strComprobante,$strTagPtoEmi)-5;
                    $strLong   = $strPosFin - $strPosIni;                    
                    $strEstab  = substr ($strComprobante,$strPosIni,3);
                    $strPosIni = strpos($strComprobante,$strTagPtoEmi)+6;
                    $strPosFin = strripos($strComprobante,$strTagPtoEmi);
                    $strLong   = $strPosFin - $strPosIni;                    
                    $strPtoEmi = substr ($strComprobante,$strPosIni,$strLong);
                    $strPosIni = strpos($strComprobante,$strTagSecuencial)+10;
                    $strPosFin = strripos($strComprobante,$strTagSecuencial);
                    $strLong   = $strPosFin - $strPosIni;                    
                    $strSecuencial = substr ($strComprobante,$strPosIni,$strLong);
                     
                    if(strlen($strSecuencial)>10)
                    {
                        $strSecuencial = substr ($strComprobante,$strPosIni,9);
                    } 
                    if(!isset($strEstab)||!isset($strPtoEmi)||!isset($strSecuencial))
                    {
                        $strResultado = 'Número de retención es inválido. Verificar tags (estab-ptoEmi-secuencial). ';
                        return $strResultado;
                    }

                    $strReferencia      = $strEstab.$strPtoEmi.$strSecuencial;
                    $arrayDetExistentes = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                      ->findBy(array('numeroReferencia' => $strReferencia, 
                                                                      'estado'          => array("Procesado", "Pendiente","Error")));
                    $boolExisteRetCliente = false;

                    if(count($arrayDetExistentes)>0)
                    {
                        foreach ($arrayDetExistentes as $objInfoPagoAutDetExistente)
                        {
                            if(is_object($objInfoPagoAutDetExistente))
                            {
                                $intIdPagAutSearch        = $objInfoPagoAutDetExistente->getPagoAutomaticoId();
                                $objInfoPagoAutCabSearch  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                         ->findOneBy(array('id'        => $intIdPagAutSearch,
                                                                                           'estado'    => array("Pendiente","Procesado","Error")));
                                if(is_object($objInfoPagoAutCabSearch) && $strIdentificacion===$objInfoPagoAutCabSearch->getIdentificacionCliente())
                                {
                                    $boolExisteRetCliente = true;
                                    $strResultado = 'Retención ya registrada para este cliente. Verificar tags (estab-ptoEmi-secuencial). ';
                                    return $strResultado;
                                }
                            }                        
                        }
                    }

                    $arrayParametrosRetDuplic = array();
                    $serviceInfoPagoDet = $this->get('financiero.InfoPagoDet');
                    $arrayParametrosRetDuplic["intIdentificacion"] = $strIdentificacion;
                    $arrayParametrosRetDuplic["intNumRef"] = $strReferencia;
                    $arrayParametrosRetDuplic["intIdFormaPago"] = null;
                    $arrayParametrosRetDuplic["strCodEmpresa"] = $strCodEmpresa;
                    $arrayParametrosRetDuplic["arrayDetExistentes"] = $arrayDetExistentes;
                    $strRpta = $serviceInfoPagoDet->getRetencionesDuplicadas($arrayParametrosRetDuplic);
                    if($strRpta != "")
                    {
                        $boolExisteRetCliente = true;
                        $strResultado = 'Retención ya registrada para este cliente. Verificar tags (estab-ptoEmi-secuencial).  '; 
                        return $strResultado;
                    }

                    $arrayParametrosCab = array(            
                        "intCuentaContableId"  => 0,
                        "strRutaArchivo"       => $strAbsolutePath,
                        "strNombreArchivo"     => $arrayParametros['strNombreDocumento'],
                        "strEstado"            => $strEstado,
                        "strIpCreacion"        => $arrayParametros['strClientIp'],
                        "dateFeCreacion"       => new \DateTime('now'),
                        "strUsrCreacion"       => $arrayParametros['strUser'],
                        "strTipoFormaPago"     => $strTipoFormaPago
                    );

                    if(count($arrayDetExistentes)<=0 || !$boolExisteRetCliente)
                    {
                        $objInfoPagoAutomaticoCab = $servicePagoAutomatico->ingresarPagoAutomaticoCab($arrayParametrosCab);
                    }   
                    
                    $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('AUTOMATIZACION_RETENCIONES', 
                                                                  'FINANCIERO', 
                                                                  'AUTOMATIZACION_RETENCIONES', 
                                                                  'ESTADO CLIENTE',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strCodEmpresa);

                    if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
                    {
                        $arrayEstados = array();
                        foreach($arrayAdmiParametroDet as $arrayParametro)
                        {
                            $arrayEstados[] = $arrayParametro['valor1'];
                        }
                    }

                    $arrayParametrosClt = array(            
                        "strIdentificacion"  => $strIdentificacion,
                        "strEmpresaCod"      => $strCodEmpresa,
                        "strDescRol"         => 'Cliente',
                        "arrayEstados"       => $arrayEstados
                    );                    
                    $arrayPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                          ->getClienteByParametros($arrayParametrosClt);
                                                        
                 
                    if(count($arrayPersonaEmpresaRol)<=0)
                    {
                        $strResultado = 'Retención no pertenece a un cliente existente. Favor revisar. ';
                        return $strResultado;
                    }                 
                    $objInfoPagoAutomaticoCab->setRazonSocial($strRazonSocial);
                    $objInfoPagoAutomaticoCab->setIdentificacionCliente($strIdentificacion);
                    $emFinanciero->persist($objInfoPagoAutomaticoCab);
                    $emFinanciero->flush();                    
                    if(strpos($strComprobante,$strTagRetenciones)!==false)
                    {
                        $strComprobante = str_replace($strTagRetenciones,'', $strComprobante);
                        $strComprobante = str_replace($strTagCodRetencion,'', $strComprobante);                        
                        $strComprobante = str_replace($strTagCompRetencion,'', $strComprobante);
                        $strComprobante = str_replace($objTagInfoCompRetencion,'', $strComprobante);
                        $strComprobante = str_replace($strTagCodigoRetenc,"", $strComprobante);
                        $strComprobante = str_replace($strTagAgenteRetencion,"", $strComprobante);
                        $strPosIni      = strpos($strComprobante,$strTagNumDocSustento)+14;
                        $strPosFin      = strripos($strComprobante,$strTagNumDocSustento);
                        $strLong        = $strPosFin - $strPosIni;                    
                        $strNumDocSustento  = substr ($strComprobante,$strPosIni,$strLong);
                        if(strlen($strNumDocSustento)>15)
                        {
                            $strNumDocSustento = substr ($strComprobante,$strPosIni,15);
                        }                         
                        $boolTagRetenciones = true;
                        
                        $arrayParametrosRec                 = array();
                        $arrayParametrosRec['strHaystack']  = $strComprobante;
                        $arrayParametrosRec['strNeedle']    = $strTagRetencion;            
                        $arrayParametrosRec['strOffset']    = 0;
                                                
                        $arrayPosImpuestos  = $servicePagoAutomatico->strposRecursive($arrayParametrosRec);
                        if(count($arrayPosImpuestos)===0)
                        {
                            $boolTagRetenciones = false;
                            $strComprobante     = str_replace($strTagImpuestos,"", $strComprobante);
                            $arrayParametrosRec                 = array();
                            $arrayParametrosRec['strHaystack']  = $strComprobante;
                            $arrayParametrosRec['strNeedle']    = $strTagImpuesto;            
                            $arrayParametrosRec['strOffset']    = 0;                        
                            $arrayPosImpuestos = $servicePagoAutomatico->strposRecursive($arrayParametrosRec);                            
                        }
                        
                    }
                    else 
                    {
                        $strComprobante = str_replace($strTagImpuestos,"", $strComprobante);
                        $arrayParametrosRec                 = array();
                        $arrayParametrosRec['strHaystack']  = $strComprobante;
                        $arrayParametrosRec['strNeedle']    = $strTagImpuesto;            
                        $arrayParametrosRec['strOffset']    = 0;                        
                        $arrayPosImpuestos = $servicePagoAutomatico->strposRecursive($arrayParametrosRec);
                    }

                    $intContPosImp = 0;
                    for($intContPosImp = 0; $intContPosImp < count($arrayPosImpuestos); $intContPosImp++ )
                    {
                        if($intContPosImp%2==0)
                        {
                            $strPosIni    = $arrayPosImpuestos[$intContPosImp]+6;
                            $strPosFin    = $arrayPosImpuestos[$intContPosImp+1];
                            $strLong      = $strPosFin - $strPosIni;                    
                            $strImpuesto  = substr ($strComprobante,$strPosIni,$strLong);
                            if(isset($strImpuesto) && $strLong>0)
                            {
                                $strImpuesto = str_replace(' ','', $strImpuesto);
                                $strImpuesto = str_replace($strTagCodRetencion,'', $strImpuesto);
                                $strPosIni   = strpos($strImpuesto,$strTagCodigo)+6;
                                $strPosFin   = strripos($strImpuesto,$strTagCodigo);
                                $strLong     = $strPosFin - $strPosIni;                    
                                $strCodigo   = substr ($strImpuesto,$strPosIni,$strLong);
                                
                                $strPosIni        = strpos($strImpuesto,$strTagBaseImponible)+13;
                                $strPosFin        = strripos($strImpuesto,$strTagBaseImponible);
                                $strLong          = $strPosFin - $strPosIni;                    
                                $strBaseImponible = substr ($strImpuesto,$strPosIni,$strLong);
                                
                                $strPosIni     = strpos($strImpuesto,$strTagPorcentaje)+17;
                                $strPosFin     = strripos($strImpuesto,$strTagPorcentaje);
                                $strLong       = $strPosFin - $strPosIni;                    
                                $strPorcentaje = substr ($strImpuesto,$strPosIni,$strLong);
                                $strPosIni       = strpos($strImpuesto,$strTagValorRetener)+13;
                                $strPosFin       = strripos($strImpuesto,$strTagValorRetener);
                                $strLong         = $strPosFin - $strPosIni;                    
                                $strValorRetener = substr ($strImpuesto,$strPosIni,$strLong);
                                
                                if(!$boolTagRetenciones)
                                {
                                    $strPosIni         = strpos($strImpuesto,$strTagNumDocSustento)+14;
                                    $strPosFin         = strripos($strImpuesto,$strTagNumDocSustento);
                                    $strLong           = $strPosFin - $strPosIni;
                                    $strNuevoNumDocSus = strpos($strImpuesto,$strNumDocSustento); //verifica si cambia el numDocSustento
                                    if(!isset($strNumDocSustento) || $strNuevoNumDocSus===false)
                                    {
                                        $strNumDocSustento = substr ($strImpuesto,$strPosIni,$strLong);
                                        if(strlen($strNumDocSustento)>15)
                                        {
                                            $strNumDocSustento = substr ($strImpuesto,$strPosIni,15);
                                        }                                         
                                    }
                                }
                                $strPorcentaje = strval(number_format($strPorcentaje,2));
                                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->findOneBy(array('parametroId'=> $objAdmiParametroCab,
                                                                                   'descripcion'=> 'FORMA PAGO',
                                                                                   'valor1'     => $strCodigo,
                                                                                   'valor2'     => $strPorcentaje,
                                                                                   'empresaCod' => $strCodEmpresa,
                                                                                   'estado'     => 'Activo'));
                                if(is_object($objAdmiParametroDet))
                                {
                                    $strCodigoFormaPago = $objAdmiParametroDet->getValor3();

                                    $objAdmiFormaPago   = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')
                                                                    ->findOneBy(array('codigoFormaPago' => $strCodigoFormaPago, 
                                                                                      'estado'          => 'Activo'));
                                    if(is_object($objAdmiFormaPago))
                                    {
                                        $intIdFormaPagoRetencion = $objAdmiFormaPago->getId();
                                    }
                                    else
                                    {
                                        $intIdFormaPagoRetencion = null;
                                    }
                                }                               
                                
                            }
                            if($strLong<=0) 
                            {
                                break;
                            }
                            $strEstadoHist                        = 'Pendiente';
                            $strObservacionHist                   = 'Se crea retención en estado Pendiente';
                            $arrayParametros['strNumDocSustento'] = $strNumDocSustento;
                            $strNumeroFactura   = $servicePagoAutomatico->getNumDocumentoByNumDocSustento($arrayParametros);

                            if(isset($strNumeroFactura))
                            {
                                $arrayParametrosFact                        = array();          
                                $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFactura;            
                                $arrayParametrosFact['strCodEmpresa']       = $strCodEmpresa;  

                                $arrayDatosFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                  ->getInformacionDocumento($arrayParametrosFact);
                                if($arrayDatosFactura && count($arrayDatosFactura)>0 && isset($arrayDatosFactura[0]['intIdDocumento']))
                                {
                                    $intIdDocumento    = $arrayDatosFactura[0]['intIdDocumento'];
                                    $intIdPunto        = $arrayDatosFactura[0]['intIdPunto'];
                                    if(isset($intIdPunto))
                                    {
                                        $objInfoPunto      = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                                        if(is_object($objInfoPunto))
                                        {
                                            $objPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();
                                        }
                                        if(!is_object($objPersonaEmpresaRol))
                                        {
                                            $strResultado = 'Retención no pertenece a un cliente existente. Favor revisar. ';
                                            return $strResultado;
                                        }
                                    }
                                    $floatBaseImpCalc  = round(floatval($arrayDatosFactura[0]['floatBaseImp']),2);
                                    $floatBaseImpIva   = 0;
                                    $floatBaseImpIce   = 0;
                                    $arrayTotalIva     =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                       ->getValorImpuesto($intIdDocumento, 'IVA');
                                    if (!empty($arrayTotalIva)) 
                                    {

                                        $floatBaseImpIva   = round(floatval($arrayTotalIva[0]['totalImpuesto']), 2);

                                    }
                                    $arrayTotalIce =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                   ->getValorImpuesto($intIdDocumento, 'ICE'); 
                                    if (!empty($arrayTotalIce))
                                    {
                                        $floatBaseImpIce   = round(floatval($arrayTotalIce[0]['totalImpuesto']), 2);
                                        if(round(floatval($arrayImpuesto['baseImponible']),2) !== $floatBaseImpCalc && 
                                           $strCodigo===$strCodigoRetFuente)
                                        {
                                            $floatDifBasImp = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpCalc),2);
                                            $floatDifBasImp = round($floatDifBasImp,2);
                                            if($floatDifBasImp<0)
                                            {
                                                $floatDifBasImp = abs($floatDifBasImp);
                                            }

                                            if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2) && 
                                                ($floatDifBasImp>$floatMargenError) && floatval($strValorRetener) > 0)
                                            {                                                          
                                                $floatBaseImpCalc += $floatBaseImpIce;
                                            }
                                        }
                                    }                        
                                }
                                else
                                {
                                    $strObservacionHist = 'Número de factura no existe para un cliente. Favor Revisar';
                                    $strEstadoHist      = 'Error';
                                    $boolErrorAdicional = true;
                                    $strResultado = $strObservacionHist;
                                    return $strResultado;
                                }
                                if(is_object($objPersonaEmpresaRol))
                                {
                                    $objInfoPagoAutomaticoCab->setOficinaId($objPersonaEmpresaRol->getOficinaId()->getId());
                                    $emFinanciero->persist($objInfoPagoAutomaticoCab);
                                    $emFinanciero->flush();                                     
                                }                                             
                            }
                            
                            if(((!isset($strCodigo) || !isset($strPorcentaje) || 
                                !isset($strBaseImponible)    || !isset($strFechaRetencion) ||
                                !isset($strValorRetener) || !isset($strReferencia) ||
                                (isset($strValorRetener) && floatval($strValorRetener)) < 0 ) 
                                && isset($intIdDocumento) && $strEstadoHist !== 'Error'))
                            {
                                if(!isset($strCodigo))
                                {
                                    $strObservacionHist    = 'Tag codigo con información incorrecta. Favor Revisar';
                                }
                                else if(!isset($strPorcentaje))
                                {
                                    $strObservacionHist    = 'Tag porcentajeRetener con información incorrecta. Favor Revisar';
                                }
                                else if(!isset($strBaseImponible))
                                {
                                    $strObservacionHist    = 'Tag baseImponible con información incorrecta. Favor Revisar';
                                }
                                else if(!isset($strValorRetener) || floatval($strValorRetener) < 0)
                                {
                                    $strObservacionHist    = 'Tag valorRetenido con información incorrecta. Favor Revisar';
                                }
                                else if(!isset($strReferencia))
                                {
                                    $strObservacionHist    = 'Número de retención incorrecto. Favor Revisar';
                                }
                                else if((isset($strValorRetener) && floatval($strValorRetener) < 0))
                                {
                                    $strObservacionHist    = 'Valor de retención es incorrecto. Favor Revisar';
                                }
                               
                                $boolErrorAdicional = true;
                            }                            
                            if( !isset($strFechaRetencion) ||  trim($strFechaRetencion)==='' )
                            {
                                $strEstadoHist      = 'Error';
                                $strObservacionHist = 'Fecha de proceso de retención es incorrecto. Favor Revisar';                              
                                $boolErrorAdicional = true;
                            } 
                  
                            // Retención Fuente
                            if(isset($strCodigo) && $strCodigo===$strCodigoRetFuente && 
                               isset($strBaseImponible) && isset($floatBaseImpCalc) && isset($intIdDocumento) && !$boolSumarizaBaseImp
                               && $strEstadoHist !== 'Error' && (round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2)) 
                               && floatval($strValorRetener)>0)
                            {
                                $floatDifBaseImp = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpCalc),2);
                                $floatDifBaseImp = round(abs($floatDifBaseImp),2);
                                if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2) 
                                    && ($floatDifBaseImp>$floatMargenError))
                                {                                    
                                    $strObservacionHist = 'Valor de Base Imponible Xml es diferente a Base Imponible Calculada. Favor Revisar';
                                    $strEstadoHist      = 'Error';
                                }
                            }
                            // Retención Iva
                            if(isset($strCodigo) && $strCodigo=== $strCodigoRetIva && 
                               isset($strBaseImponible)&& isset($floatBaseImpIva) && isset($intIdDocumento) && !$boolSumarizaBaseImp
                               && $strEstadoHist !== 'Error')
                            {
                                $floatDiferencia = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpIva),2);
                                $floatDiferencia = round(abs($floatDiferencia),2);
                                if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpIva),2) 
                                    && ($floatDiferencia>$floatMargenError))
                                {
                                    $strObservacionHist    = 'Valor de Base Imponible Xml es diferente a Base Imponible Iva. Favor Revisar';
                                    $strEstadoHist         = 'Error';  
                                }                       
                            }
                            $arrayInfoPagoAutomaticoDet                         = array();
                            $arrayInfoPagoAutomaticoDet['intPagoAutomaticoId']  = $objInfoPagoAutomaticoCab->getId();
                            if($strEstadoHist === 'Error')
                            {
                                $arrayInfoPagoAutomaticoDet['strEstado'] = "Error";
                                $objInfoPagoAutomaticoCab->setEstado("Error");                         
                            } 

                            if(floatval($strValorRetener) == 0 && isset($intIdDocumento))
                            {
                                $strObservacionHist    = 'No procesar.';
                                $strEstadoHist         = 'No Procesa';
                                $arrayInfoPagoAutomaticoDet['strEstado'] = "No Procesa";
                            }                   

                            $emFinanciero->persist($objInfoPagoAutomaticoCab);
                            $emFinanciero->flush();                    
                            if(is_object($objPersonaEmpresaRol))
                            {
                                $arrayInfoPagoAutomaticoDet['intPersonaEmpresaRolId'] = $objPersonaEmpresaRol->getId();
                                $arrayInfoPagoAutomaticoDet['strEstado'] = "Pendiente";
                            }
                            if($strEstadoHist === 'Error')
                            {
                                $arrayInfoPagoAutomaticoDet['strEstado'] = "Error";
                                $objInfoPagoAutomaticoCab->setEstado("Error");                         
                            }

                            if(floatval($strValorRetener) == 0 && isset($intIdDocumento))
                            {
                                $strObservacionHist    = 'No procesar.';
                                $strEstadoHist         = 'No Procesa';
                                $arrayInfoPagoAutomaticoDet['strEstado'] = "No Procesa";
                            }                   

                            $emFinanciero->persist($objInfoPagoAutomaticoCab);
                            $emFinanciero->flush();
                    
                            $arrayInfoPagoAutomaticoDet['intIdFormaPagoRetencion'] = $intIdFormaPagoRetencion;
                            $arrayInfoPagoAutomaticoDet['strReferencia']           = $strReferencia;
                            $strObservacion = "Ret#".$strReferencia.' aplica Factura #'.$strNumeroFactura.' Fecha '.$strFechaRetencion;
                            $arrayInfoPagoAutomaticoDet['strObservacion']          = $strObservacion;
                            $arrayInfoPagoAutomaticoDet['floatMonto']              = $strValorRetener;
                            $arrayInfoPagoAutomaticoDet['dateFeCreacion']          = new \DateTime('now');
                            $arrayInfoPagoAutomaticoDet['strIpCreacion']           = $arrayParametros['strClientIp'];
                            $arrayInfoPagoAutomaticoDet['strUsrCreacion']          = $arrayParametros['strUser'];
                            $arrayInfoPagoAutomaticoDet['intIdDocumento']          = $intIdDocumento;
                            $arrayInfoPagoAutomaticoDet['strFecha']                = $strFechaRetencion;
                            $arrayInfoPagoAutomaticoDet['floatBaseImponible']      = $strBaseImponible;
                            $arrayInfoPagoAutomaticoDet['floatBaseImpCalc']        = $floatBaseImpCalc;
                            $arrayInfoPagoAutomaticoDet['strCodigo']               = $strCodigo;
                            $arrayInfoPagoAutomaticoDet['floatPorcentaje']         = $strPorcentaje;
                            $arrayInfoPagoAutomaticoDet['strCodEmpresa']           = $strCodEmpresa;
                            $arrayInfoPagoAutomaticoDet['strNumDocSustento']       = $strNumDocSustento;

                            $arrayDetExistentes = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                  ->findBy(array('numeroReferencia' => $strReferencia, 
                                                  'estado'          => array("Procesado", "Pendiente","Error")));
                                                
                            if(count($arrayDetExistentes)<=0 || !$boolExisteRetCliente)
                            {
                                $objInfoPagoAutomaticoDet = $servicePagoAutomatico->ingresarPagoAutomaticoDet($arrayInfoPagoAutomaticoDet);
                                $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                                $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                                $objInfoPagoAutomaticoHist->setEstado($strEstadoHist);
                                $objInfoPagoAutomaticoHist->setObservacion($strObservacionHist);
                                $objInfoPagoAutomaticoHist->setIpCreacion($arrayParametros['strClientIp']);
                                $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                                $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUser']);
                                $emFinanciero->persist($objInfoPagoAutomaticoHist);
                                $emFinanciero->flush();                                 
                            }                       
                        }
                    }
                }
                $boolFormatoValido = true;
            }

            if(!$boolFormatoValido)
            {
            $objXmlDocument = $objXmlDocumentTmp;
            $objXmlDocument = str_replace($strReplaceValor1,"", $objXmlDocument);
            $objXmlDocument = str_replace($strReplaceValor2,"", $objXmlDocument);
            $objXmlDocument = str_replace($strReplaceValor3,$strReplaceValor4, $objXmlDocument);                
            $objXmlDocument = str_replace($strReplaceValor5,$strReplaceValor6, $objXmlDocument);
            $objXmlDocument = str_replace($strReplaceValor10,$strReplaceValor6, $objXmlDocument);
            $objXmlDocument = str_replace($strReplaceValor11,'', $objXmlDocument);
            $objXmlDocument = str_replace($strReplaceValor12,'', $objXmlDocument);
            if(strpos($objXmlDocument,$strReplaceValor19)!==false)
            {
                $intPosInit    = strpos($objXmlDocument,$strReplaceValor18);
                $intPosMed     = strpos($objXmlDocument,$strReplaceValor19);
                $intPosFin     = strpos($objXmlDocument,substr($objXmlDocument,-1));
                $strTagsInicio = trim(substr($objXmlDocument,0,$intPosInit));
                $strTagsFin    = trim(substr($objXmlDocument,$intPosMed,$intPosFin));         

                $objXmlDocument = str_replace($strTagsInicio,'', $objXmlDocument);
                $objXmlDocument = str_replace($strTagsFin,'', $objXmlDocument);
            }
            
            if(strpos($objXmlDocument,$strReplaceValor13)!==false && strpos($objXmlDocument,$strReplaceValor19)===false)
            {
                $objXmlDocument = str_replace($strReplaceValor13,'', $objXmlDocument);
                $objXmlDocument = str_replace($strReplaceValor14,'', $objXmlDocument);
            }
            
            if(strpos($objXmlDocument,$strReplaceValor15)!==false && strpos($objXmlDocument,$strReplaceValor19)===false)
            {
                $objXmlDocument = str_replace($strReplaceValor15,$strReplaceValor17, $objXmlDocument);
            }

            if(strpos($objXmlDocument,$strReplaceValor16)!==false && strpos($objXmlDocument,$strReplaceValor19)===false)
            {
                $objXmlDocument = str_replace($strReplaceValor16,$strReplaceValor17, $objXmlDocument);
            }

            
            $objXmlElement  = $objXmlDocument;

            $objXmlDocument = simplexml_load_string($objXmlDocument,null, LIBXML_NOCDATA);
            
            if(!is_object($objXmlDocument) && !is_string($objXmlDocument))
            {   
                $objXmlDocument = simplexml_load_string(utf8_encode($objXmlElement),null, LIBXML_NOCDATA);
            }
            $objJsonDocument       = json_encode($objXmlElement);
            $arrayCompRetPrincipal = json_decode($objJsonDocument, true);

            if(!isset($objXmlDocument->comprobante))
            {
              $arrayXmlDocument = $objXmlDocument->infoAdicional;
              if(isset($objXmlDocument->impuestos))
              {
                $arrayXmlDocument = $objXmlDocument->impuestos;
              }
              $boolNuevoFormato   = true;
              $boolNuevoFormatoAd = true;
            }
            else 
            {
                $arrayXmlDocument = $objXmlDocument->comprobante;
            }
            if(isset($arrayCompRetPrincipal[$strTagClaveAccesoConsultada]))
            {
                $arrayPrincipal     = $arrayCompRetPrincipal[$strTagAutorizaciones];
                $arrayCompRetValida = $arrayCompRetPrincipal[$strTagAutorizaciones][$strTagAutorizacion][$strTagAutorizacion];
                
                if(count($arrayCompRetValida)===0)
                {
                    $arrayCompRetPrincipal = $arrayCompRetPrincipal[$strTagAutorizaciones][$strTagAutorizacion];
                }
                else 
                {
                    $arrayCompRetPrincipal = $arrayCompRetValida;
                }
                if(isset($arrayCompRetPrincipal[$strTagComprobante]))
                {
                    $arrayXmlDocument      = $arrayPrincipal;
                    if(!isset($arrayCompRetPrincipal[$strTagComprobante][$strTagCompRetencion]))
                    {
                        $objXmlDocumentB       = simplexml_load_string($arrayCompRetPrincipal[$strTagComprobante],null, LIBXML_NOCDATA);
                        $objJsonDocumentB      = json_encode($objXmlDocumentB);
                        $arrayComprobanteRet   = json_decode($objJsonDocumentB, true);
                    }
                    else 
                    {
                        $arrayComprobanteRet = $arrayCompRetPrincipal[$strTagComprobante][$strTagCompRetencion];
                    }
                    $boolFormatoC          = true;
                    $boolNuevoFormato      = false;
                    $boolNuevoFormatoAd    = false;                    
                }
            } 
            if(isset($arrayCompRetPrincipal['infoAdicional']))
            {
                
                $arrayXmlDocument    = $arrayCompRetPrincipal['infoAdicional'];
                $arrayComprobanteRet = $arrayCompRetPrincipal;
                $boolNuevoFormato    = false;
            }
            if(isset($arrayCompRetPrincipal[$strTagEstado]) || isset($strFechaRetencion)) 
            {
                if(isset($arrayCompRetPrincipal[$strTagEstado]) && $arrayCompRetPrincipal[$strTagEstado]===$strEstadoXmlPendiente 
                    && !isset($strFechaRetencion))
                {
                    $strEstado          =  'Error';
                    $strFechaRetencion  =  '';
                }
                else if((isset($arrayCompRetPrincipal[$strTagEstado]) && $arrayCompRetPrincipal[$strTagEstado]===$strEstadoXmlAutorizado 
                    && isset($arrayCompRetPrincipal[$strTagFechaAutorizacion])) || isset($strFechaRetencion))
                {
                    if(isset($arrayCompRetPrincipal[$strTagEstado]) && !is_string($arrayCompRetPrincipal[$strTagFechaAutorizacion]) 
                        && !isset($strFechaRetencion))
                    {
                        $strAnio = str_pad($arrayCompRetPrincipal[$strTagFechaAutorizacion][$strTagYear],4, "0", STR_PAD_LEFT);
                        $strMes  = str_pad($arrayCompRetPrincipal[$strTagFechaAutorizacion][$strTagMonth],2, "0", STR_PAD_LEFT);
                        $strDia  = str_pad($arrayCompRetPrincipal[$strTagFechaAutorizacion][$strTagDay],2, "0", STR_PAD_LEFT);
                        $strFechaRetencion = $strAnio.'-'.$strMes.'-'.$strDia;
                    }
                    else
                    {
                        if(!isset($strFechaRetencion))
                        {
                            $strFechaRetencion  = substr ($arrayCompRetPrincipal[$strTagFechaAutorizacion],0,10);
                        }
                        if(strpos($strFechaRetencion,'/') > 0)
                        {
                            if(strpos($strFechaRetencion,' ') > 0)
                            {
                                $arrayFeTransaccionNew  =  explode(' ', $strFechaRetencion);
                                $arrayFeTransaccion     =  explode('/', $arrayFeTransaccionNew[0]);
                            }
                            else
                            {
                                $arrayFeTransaccion  =  explode('/', $strFechaRetencion);
                            }
                        }
                        else
                        {
                            $arrayFeTransaccion  =  explode('-', $strFechaRetencion);
                        }                                                             

                        if(count($arrayFeTransaccion)==3)
                        {
                            if(in_array($arrayFeTransaccion[2],range(1900,2500)) && in_array($arrayFeTransaccion[1],range(1,12)))
                            {
                                $strFechaRetencion = $arrayFeTransaccion[2].'/'.$arrayFeTransaccion[1].'/'.$arrayFeTransaccion[0];
                            }
                            else if(in_array($arrayFeTransaccion[0],range(1900,2500)) && in_array($arrayFeTransaccion[1],range(1,12)))
                            {
                                $strFechaRetencion = $arrayFeTransaccion[0].'/'.$arrayFeTransaccion[1].'/'.$arrayFeTransaccion[2];
                            }                            
                            else 
                            {
                                $strFechaRetencion = $arrayFeTransaccion[2].'/'.$arrayFeTransaccion[0].'/'.$arrayFeTransaccion[1];
                            }                            
                        }                     
                    }
                }                
            }
            $arrayParametrosCab = array(            
                "intCuentaContableId"  => 0,
                "strRutaArchivo"       => $strAbsolutePath,
                "strNombreArchivo"     => $arrayParametros['strNombreDocumento'],
                "strEstado"            => $strEstado,
                "strIpCreacion"        => $arrayParametros['strClientIp'],
                "dateFeCreacion"       => new \DateTime('now'),
                "strUsrCreacion"       => $arrayParametros['strUser'],
                "strTipoFormaPago"     => $strTipoFormaPago
            );
        
            $objInfoPagoAutomaticoCab = $servicePagoAutomatico->ingresarPagoAutomaticoCab($arrayParametrosCab);

            foreach($arrayXmlDocument as $objComprobanteRetencion) 
            {
                $boolFormatoValido     = true;
                if(!$boolNuevoFormato && count($objComprobanteRetencion->comprobanteRetencion[0])>0)
                {
                    $boolNuevoFormato = true;
                }                

                if(!$boolNuevoFormato)
                {
                    if(!$boolFormatoC && !isset($arrayComprobanteRet))
                    {                    
                        $objComprobanteRetencion = str_replace($strReplaceValor7,$strReplaceValor8, $objComprobanteRetencion);                
                        $objComprobanteRetencion = str_replace($strReplaceValor9,$strReplaceValor8, $objComprobanteRetencion);

                        $objXmlComprobanteRet = simplexml_load_string($objComprobanteRetencion,null, LIBXML_NOCDATA);

                        if($objXmlComprobanteRet === false)
                        {
                            $strResultado = 'Error en formato de archivo xml. Favor revisar.';
                            return $strResultado;
                        }                  

                        if(!$objXmlComprobanteRet)
                        {
                            $boolError    = true;
                            $strResultado = 'Error en formato de archivo xml. Favor revisar.'; 
                            return $strResultado;
                        }

                        $objJsonDocument      = json_encode($objXmlComprobanteRet);
                        $arrayComprobanteRet  = json_decode($objJsonDocument, true);
                    }
                    if(!isset($arrayComprobanteRet[$strTagInfoCompRetencion]) && !$boolError)
                    {
                        $boolError    = true;
                        $strResultado = 'Error en formato de archivo xml. Favor revisar tag infoCompRetencion.';                         
                    }              
                    if(!isset($arrayComprobanteRet[$strTagInfoCompRetencion][$strTagIdentificacionSujetoRetenido]) && !$boolError)
                    {
                         $boolError    = true;
                         $strResultado = 'Tag Identificación de sujeto retenido es inválido.'; 
                    }
                    $strRucSujetoRetenido= $arrayComprobanteRet[$strTagInfoCompRetencion][$strTagIdentificacionSujetoRetenido];
                }
                else
                {
                    if($boolNuevoFormatoAd)
                    {
                        $strRucSujetoRetenido = $objXmlDocument->infoCompRetencion[0]->$strTagIdentificacionSujetoRetenido;
                        $strRazonSocial      = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagRazonSocial);
                    }
                    else 
                    {
                        $strRucSujetoRetenido=$objComprobanteRetencion->comprobanteRetencion[0]
                                                                      ->infoCompRetencion[0]->$strTagIdentificacionSujetoRetenido;
                    }
                }
                
                if(strcmp($strRucEmpresa,$strRucSujetoRetenido)!==0 && !$boolError)
                {
                    $strResultado = 'Identificación de sujeto retenido es inválido.Favor revisar'; 
                    $boolError    = true;
                }
                
                if(!$boolNuevoFormato)
                {              
                    $strRazonSocial      = $arrayComprobanteRet[$strTagInfoTributaria][$strTagRazonSocial];
                    $strIdentificacion   = $arrayComprobanteRet[$strTagInfoTributaria][$strTagRuc];
                    $strEstab            = $arrayComprobanteRet[$strTagInfoTributaria][$strTagEstab];
                    $strPtoEmi           = $arrayComprobanteRet[$strTagInfoTributaria][$strTagPtoEmi];
                    $strSecuencial       = $arrayComprobanteRet[$strTagInfoTributaria][$strTagSecuencial];
                }
                else
                {
                    if($boolNuevoFormatoAd)
                    {
                        $strRazonSocial      = strval($objXmlDocument->infoTributaria[0]->$strTagRazonSocial);
                        $strIdentificacion   = strval($objXmlDocument->infoTributaria[0]->$strTagRuc);
                        $strEstab            = strval($objXmlDocument->infoTributaria[0]->$strTagEstab);
                        $strPtoEmi           = strval($objXmlDocument->infoTributaria[0]->$strTagPtoEmi);
                        $strSecuencial       = strval($objXmlDocument->infoTributaria[0]->$strTagSecuencial);                     

                    }
                    else 
                    {
                        $strRazonSocial      = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagRazonSocial);
                        $strIdentificacion   = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagRuc);
                        $strEstab            = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagEstab);
                        $strPtoEmi           = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagPtoEmi);
                        $strSecuencial       = strval($objComprobanteRetencion->comprobanteRetencion[0]->infoTributaria[0]->$strTagSecuencial);
                    }                    
                    
                }
                
                if(!isset($strEstab)||!isset($strPtoEmi)||!isset($strSecuencial) && !$boolError)
                {
                    $strResultado = 'Número de retención es inválido. Verificar tags (estab-ptoEmi-secuencial).';  
                    $boolError    = true;
                }
                
                $strReferencia      = $strEstab.$strPtoEmi.$strSecuencial;
                $arrayDetExistentes = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                  ->findBy(array('numeroReferencia' => $strReferencia, 
                                                                  'estado'          => array("Procesado", "Pendiente","Error")));

                if(count($arrayDetExistentes)>0 && !$boolError)
                {
                    foreach ($arrayDetExistentes as $objInfoPagoAutDetExistente)
                    {
                        if(is_object($objInfoPagoAutDetExistente))
                        {
                            $intIdPagAutSearch        = $objInfoPagoAutDetExistente->getPagoAutomaticoId();
                            $objInfoPagoAutCabSearch  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                     ->findOneBy(array('id'        => $intIdPagAutSearch,
                                                                                       'estado'    => array("Pendiente","Procesado","Error")));
                            if(is_object($objInfoPagoAutCabSearch) && $strIdentificacion===$objInfoPagoAutCabSearch->getIdentificacionCliente())
                            {
                                $strResultado = 'Retención registrada para este cliente. Verificar tags (estab-ptoEmi-secuencial).'; 
                                $boolError    = true;
                            }
                        }                        
                    }
                }

                $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->findByIdentificacionTipoRolEmpresa($strIdentificacion,'Cliente', $strCodEmpresa);
                
                if(is_object($objPersonaEmpresaRol))
                {
                    $objInfoPagoAutomaticoCab->setOficinaId($objPersonaEmpresaRol->getOficinaId()->getId());
                }                
                $objInfoPagoAutomaticoCab->setRazonSocial($strRazonSocial);
                $objInfoPagoAutomaticoCab->setIdentificacionCliente($strIdentificacion);
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();
                
                if(!$boolNuevoFormato)
                {
                    if($arrayComprobanteRet[$strTagDocsSustento] && count($arrayComprobanteRet[$strTagDocsSustento]>0))
                    {
                        $arrayImpuestos = $arrayComprobanteRet[$strTagDocsSustento][$strTagDocSustento][$strTagRetenciones][$strTagRetencion];
                    }                
                    else
                    {
                        if(count(($arrayComprobanteRet[$strTagImpuestos][$strTagImpuesto]))===8)
                        {
                            $arrayImpuestos = $arrayComprobanteRet[$strTagImpuestos];
                        }
                        else 
                        {
                            $arrayImpuestos = $arrayComprobanteRet[$strTagImpuestos][$strTagImpuesto];
                        }                    
                    }                   
                    if(!$arrayImpuestos && !$boolError)
                    {
                        $strResultado = 'Formato de retención es inválido. Favor revisar tag de impuestos. ';  
                        $boolError    = true;
                    }
                }
                else 
                {
                    if($boolNuevoFormatoAd)
                    {
                        $arrayImpuestos = $objXmlDocument->impuestos[0]->impuesto;
                    }
                    else 
                    {                    
                        $arrayImpuestos = $objComprobanteRetencion->comprobanteRetencion[0]->impuestos[0];
                    }
                }
                foreach($arrayImpuestos as $arrayImpuesto)
                {
                    $strEstadoHist         = 'Pendiente';
                    $strObservacionHist    = 'Se crea retención en estado Pendiente';
                    if(!$boolNuevoFormato)
                    {
                        if($arrayComprobanteRet[$strTagDocsSustento] && count($arrayComprobanteRet[$strTagDocsSustento]>0))
                        {
                            $arrayParametros['strNumDocSustento'] = 
                                $arrayComprobanteRet[$strTagDocsSustento][$strTagDocSustento][$strTagNumDocSustento];
                        }
                        else
                        {
                            $arrayParametros['strNumDocSustento'] = $arrayImpuesto[$strTagNumDocSustento]; 
                        }
                    }
                    else 
                    {
                        $arrayParametros['strNumDocSustento'] = strval($arrayImpuesto[0]->$strTagNumDocSustento);
                    }
                    $strNumeroFactura   = $servicePagoAutomatico->getNumDocumentoByNumDocSustento($arrayParametros);
                    if(is_object($objAdmiParametroCab))
                    {
                        if(!$boolNuevoFormato)
                        {
                            $strCodigo         = strval($arrayImpuesto[$strTagCodigo]);
                            $strPorcentaje     = strval(number_format($arrayImpuesto[$strTagPorcentaje],2));
                            $strBaseImponible  = strval($arrayImpuesto[$strTagBaseImponible]);
                            $strValorRetener   = strval($arrayImpuesto[$strTagValorRetener]);
                        }
                        else 
                        {
                            $strCodigo         = strval($arrayImpuesto[0]->$strTagCodigo);
                            $strPorcentaje     = strval($arrayImpuesto[0]->$strTagPorcentaje); 
                            $strBaseImponible  = strval($arrayImpuesto[0]->$strTagBaseImponible);
                            $strValorRetener   = strval($arrayImpuesto[0]->$strTagValorRetener);                            
                        }                        

                        $strPorcentaje = strval(number_format($strPorcentaje,2));
                        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneBy(array('parametroId'=> $objAdmiParametroCab,
                                                                           'descripcion'=> 'FORMA PAGO',
                                                                           'valor1'     => $strCodigo,
                                                                           'valor2'     => $strPorcentaje,
                                                                           'empresaCod' => $strCodEmpresa,
                                                                           'estado'     => 'Activo'));
                        if(is_object($objAdmiParametroDet))
                        {
                            $strCodigoFormaPago = $objAdmiParametroDet->getValor3();
                            $objAdmiFormaPago   = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')
                                                            ->findOneBy(array('codigoFormaPago' => $strCodigoFormaPago, 
                                                                              'estado'          => 'Activo'));
                            if(is_object($objAdmiFormaPago))
                            {
                                $intIdFormaPagoRetencion = $objAdmiFormaPago->getId();
                            }
                            else
                            {
                                $intIdFormaPagoRetencion = null;
                            }
                        } 
                    }
                    $arrayInfoPagoAutomaticoDet                         = array();
                    $arrayInfoPagoAutomaticoDet['intPagoAutomaticoId']  = $objInfoPagoAutomaticoCab->getId();                   
                                       
                    
                    $arrayParametrosFact                        = array();          
                    $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFactura;            
                    $arrayParametrosFact['strCodEmpresa']       = $strCodEmpresa;  

                    $arrayDatosFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->getInformacionDocumento($arrayParametrosFact);
                    if($arrayDatosFactura && count($arrayDatosFactura)>0 && isset($arrayDatosFactura[0]['intIdDocumento']))
                    {
                        $intIdDocumento    = $arrayDatosFactura[0]['intIdDocumento'];
                        $intIdPunto        = $arrayDatosFactura[0]['intIdPunto'];
                        if(isset($intIdPunto))
                        {
                            $objInfoPunto      = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                            if(is_object($objInfoPunto))
                            {
                                $objPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();
                                if(is_object($objPersonaEmpresaRol))
                                {
                                    $arrayInfoPagoAutomaticoDet['intPersonaEmpresaRolId'] = $objPersonaEmpresaRol->getId();
                                    $arrayInfoPagoAutomaticoDet['strEstado'] = "Pendiente";
                                }
                                else if(!$boolError)
                                {
                                    $strResultado = 'Retención no pertenece a un cliente existente. Favor revisar. ';
                                    $boolError    = true;
                                }
                            }
                        }
                                                
                        $floatBaseImpCalc  = round(floatval($arrayDatosFactura[0]['floatBaseImp']),2);
                        $floatBaseImpIva   = 0;
                        $floatBaseImpIce   = 0;
                        $arrayTotalIva     =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                           ->getValorImpuesto($intIdDocumento, 'IVA');
                        if (!empty($arrayTotalIva)) 
                        {

                            $floatBaseImpIva   = round(floatval($arrayTotalIva[0]['totalImpuesto']), 2);

                        }
                        $arrayTotalIce =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->getValorImpuesto($intIdDocumento, 'ICE'); 
                        if (!empty($arrayTotalIce))
                        {
                            $floatBaseImpIce   = round(floatval($arrayTotalIce[0]['totalImpuesto']), 2);
                            if(round(floatval($arrayImpuesto['baseImponible']),2) !== $floatBaseImpCalc && 
                               $strCodigo===$strCodigoRetFuente)
                            {
                                $floatDifBasImp = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpCalc),2);
                                $floatDifBasImp = round($floatDifBasImp,2);
                                if($floatDifBasImp<0)
                                {
                                    $floatDifBasImp = abs($floatDifBasImp);
                                }
                               
                                if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2) && 
                                    ($floatDifBasImp>$floatMargenError) && floatval($strValorRetener) > 0)
                                {                                                          
                                    $floatBaseImpCalc += $floatBaseImpIce;
                                }
                            }
                        }                        
                    }
                    else
                    {
                        $strObservacionHist = 'Número de factura no existe para un cliente. Favor Revisar';
                        $strEstadoHist      = 'Error';
                        $boolErrorAdicional = true;
                    }

                    if($arrayComprobanteRet[$strTagDocsSustento] && count($arrayComprobanteRet[$strTagDocsSustento]>0)
                        && !$boolNuevoFormato)
                    {
                       if(!isset($arrayComprobanteRet[$strTagDocsSustento][$strTagDocSustento][$strTagNumDocSustento]))
                       {
                           $strObservacionHist = 'Tag numDocSustento con información incorrecta. Favor Revisar';
                           $strEstadoHist      = 'Error';
                           $boolErrorAdicional = true;
                       }
                                              
                    }
                    else
                    {
                        if(isset($strPorcentaje))
                        {
                            if(!in_array($strPorcentaje,$arrayPorcentRetener))
                            {
                                $arrayPorcentRetener[] = $strPorcentaje;
                            }
                            else 
                            {
                                $boolSumarizaBaseImp   = true;
                            }
                        }
                        if(!$boolNuevoFormato)
                        {
                            $strNumDocSustento = strval($arrayImpuesto[$strTagNumDocSustento]);
                        }
                        else 
                        {
                            $strNumDocSustento = strval($arrayImpuesto[0]->$strTagNumDocSustento);
                        }
                        
                        if(!isset($strNumDocSustento))
                        {
                            $strObservacionHist    = 'Tag numDocSustento con información incorrecta. Favor Revisar';
                            $strEstadoHist         = 'Error';
                            $boolErrorAdicional    = true;
                        }

                    }
 
                    if(!isset($intIdFormaPagoRetencion))
                    {
                        $strObservacionHist    = 'Error en porcentaje de retención. Favor Revisar';
                        $strEstadoHist         = 'Error';
                        $boolErrorAdicional    = true;
                    }
                    
                    if((!isset($arrayCompRetPrincipal[$strTagEstado]) && !isset($strFechaRetencion)) || 
                       (isset($arrayCompRetPrincipal[$strTagEstado]) && $arrayCompRetPrincipal[$strTagEstado]==='PENDIENTE') ||
                        !isset($strFechaRetencion))
                    {
                        $strObservacionHist    = 'Fecha de proceso de retención es incorrecto. Favor Revisar';
                        $strEstadoHist         = 'Error';
                        $boolErrorAdicional    = true;                 
                    }                    
                    
                    if((!isset($strCodigo) || !isset($strPorcentaje) || 
                        !isset($strBaseImponible)    ||
                        !isset($strValorRetener) || !isset($strReferencia) ||
                        (isset($strValorRetener) && floatval($strValorRetener)) < 0 ) 
                        && isset($intIdDocumento) && $strEstadoHist !== 'Error' && !$boolNuevoFormato)
                    {
                        if(!isset($strCodigo))
                        {
                            $strObservacionHist    = 'Tag codigo con información incorrecta. Favor Revisar';
                        }
                        else if(!isset($strPorcentaje))
                        {
                            $strObservacionHist    = 'Tag porcentajeRetener con información incorrecta. Favor Revisar';
                        }
                        else if(!isset($strBaseImponible))
                        {
                            $strObservacionHist    = 'Tag baseImponible con información incorrecta. Favor Revisar';
                        }
                        else if(!isset($strValorRetener) || floatval($strValorRetener) < 0)
                        {
                            $strObservacionHist    = 'Tag valorRetenido con información incorrecta. Favor Revisar';
                        }
                        else if(!isset($strReferencia))
                        {
                            $strObservacionHist    = 'Número de retención incorrecto. Favor Revisar';
                        }
                        else if((isset($strValorRetener) && floatval($strValorRetener)<0))
                        {
                            $strObservacionHist    = 'Valor de retención es incorrecto. Favor Revisar';
                        }
                        $boolErrorAdicional = true;
                    }
                  
                    // Retención Fuente
                    if(isset($strCodigo) && $strCodigo===$strCodigoRetFuente && 
                       isset($strBaseImponible) && isset($floatBaseImpCalc) && isset($intIdDocumento) && !$boolSumarizaBaseImp
                       && $strEstadoHist !== 'Error' && (round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2)) 
                       && floatval($strValorRetener)>0)
                    {
                        $floatDifBaseImp = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpCalc),2);
                        $floatDifBaseImp = round(abs($floatDifBaseImp),2);
                        if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpCalc),2) && ($floatDifBaseImp>$floatMargenError))
                        {                        
                            $strObservacionHist = 'Valor de Base Imponible Xml es diferente a Base Imponible Calculada. Favor Revisar';
                            $strEstadoHist      = 'Error';
                        }
                    }
                    // Retención Iva
                    if(isset($strCodigo) && $strCodigo=== $strCodigoRetIva && 
                       isset($strBaseImponible)&& isset($floatBaseImpIva) && isset($intIdDocumento) && !$boolSumarizaBaseImp
                       && $strEstadoHist !== 'Error')
                    {
                        $floatDiferencia = round(floatval($strBaseImponible),2) - round(floatval($floatBaseImpIva),2);
                        $floatDiferencia = round(abs($floatDiferencia),2);
                        if(round(floatval($strBaseImponible),2) !== round(floatval($floatBaseImpIva),2) && ($floatDiferencia>$floatMargenError))
                        {
                            $strObservacionHist    = 'Valor de Base Imponible Xml es diferente a Base Imponible Iva. Favor Revisar';
                            $strEstadoHist         = 'Error';  
                        }                       
                    }
                    
                    if($strEstadoHist === 'Error')
                    {
                        $arrayInfoPagoAutomaticoDet['strEstado'] = "Error";
                        $objInfoPagoAutomaticoCab->setEstado("Error");                         
                    }
                    
                    if(floatval($strValorRetener) == 0 && isset($intIdDocumento))
                    {
                        $strObservacionHist    = 'No procesar.';
                        $strEstadoHist         = 'No Procesa';
                        $arrayInfoPagoAutomaticoDet['strEstado'] = "No Procesa";
                    }                   
                    
                    $emFinanciero->persist($objInfoPagoAutomaticoCab);
                    $emFinanciero->flush();                    
                    
                    $arrayInfoPagoAutomaticoDet['intIdFormaPagoRetencion'] = $intIdFormaPagoRetencion;
                    $arrayInfoPagoAutomaticoDet['strReferencia']           = $strReferencia;
                    $strObservacion = "Ret#".$strReferencia.' aplica Factura #'.$strNumeroFactura.' Fecha '.$strFechaRetencion;
                    $arrayInfoPagoAutomaticoDet['strObservacion']          = $strObservacion;
                    $arrayInfoPagoAutomaticoDet['floatMonto']              = $strValorRetener;
                    $arrayInfoPagoAutomaticoDet['dateFeCreacion']          = new \DateTime('now');
                    $arrayInfoPagoAutomaticoDet['strIpCreacion']           = $arrayParametros['strClientIp'];
                    $arrayInfoPagoAutomaticoDet['strUsrCreacion']          = $arrayParametros['strUser'];
                    $arrayInfoPagoAutomaticoDet['intIdDocumento']          = $intIdDocumento;
                    $arrayInfoPagoAutomaticoDet['strFecha']                = $strFechaRetencion;
                    $arrayInfoPagoAutomaticoDet['floatBaseImponible']      = $strBaseImponible;
                    $arrayInfoPagoAutomaticoDet['floatBaseImpCalc']        = $floatBaseImpCalc;
                    $arrayInfoPagoAutomaticoDet['strCodigo']               = $strCodigo;
                    $arrayInfoPagoAutomaticoDet['floatPorcentaje']         = $strPorcentaje;
                    $arrayInfoPagoAutomaticoDet['strCodEmpresa']           = $strCodEmpresa;
                    
                    if($arrayComprobanteRet[$strTagDocsSustento] && count($arrayComprobanteRet[$strTagDocsSustento]>0)
                       && !$boolNuevoFormato)
                    {
                        $strNumDocSustentoFact = $arrayComprobanteRet[$strTagDocsSustento][$strTagDocSustento][$strTagNumDocSustento];
                        $arrayInfoPagoAutomaticoDet['strNumDocSustento'] = $strNumDocSustentoFact;
                    }
                    else
                    {
                         $arrayInfoPagoAutomaticoDet['strNumDocSustento'] = $strNumDocSustento;
                    }
                    $objInfoPagoAutomaticoDet = $servicePagoAutomatico->ingresarPagoAutomaticoDet($arrayInfoPagoAutomaticoDet);
                   
                    //Graba historial de detalle de retención.
                   $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                   $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                   $objInfoPagoAutomaticoHist->setEstado($strEstadoHist);
                   $objInfoPagoAutomaticoHist->setObservacion($strObservacionHist);
                   $objInfoPagoAutomaticoHist->setIpCreacion($arrayParametros['strClientIp']);
                   $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                   $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUser']);
                   $emFinanciero->persist($objInfoPagoAutomaticoHist);
                   $emFinanciero->flush();                     

                }
            }

            }
            $intIdPagAutCab                   = $objInfoPagoAutomaticoCab->getId();
            $arrayInfoPagoAutDetRetFuente     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                             ->findBy( array('pagoAutomaticoId' => $intIdPagAutCab,
                                                                             'codigoImpuesto'   => $strCodigoRetFuente));
            // Obtengo base imponible total de registros con retención a la fuente.
            foreach ($arrayInfoPagoAutDetRetFuente as $objInfoPagoAutDetRetFuente)
            {
                $floatTotalBaseImpRetFuente += floatval($objInfoPagoAutDetRetFuente->getBaseImponible());
            }
            
            if(isset($strCodigoRetFuente)  && (round(floatval($floatBaseImpCalc),2) === round(floatval($floatTotalBaseImpRetFuente),2)) 
               && floatval($floatTotalBaseImpRetFuente)>0  && isset($strFechaRetencion))
            {
                foreach ($arrayInfoPagoAutDetRetFuente as $objInfoPagoAutDetRetFuente)
                {
                    $objInfoPagoAutDetRetFuente->setEstado("Pendiente");
                    $emFinanciero->persist($objInfoPagoAutDetRetFuente);
                    $emFinanciero->flush();             
                }
            }  
            
             $arrayInfoPagoAutDetRetIva     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                             ->findBy( array('pagoAutomaticoId' => $intIdPagAutCab,
                                                                             'codigoImpuesto'   => $strCodigoRetIva));
            // Obtengo base imponible total de registros con retención iva.
            foreach ($arrayInfoPagoAutDetRetIva as $objInfoPagoAutDetRetIva)
            {
                $floatTotalBaseImpRetIva += floatval($objInfoPagoAutDetRetIva->getBaseImponible());
            }
            
            if(isset($strCodigoRetIva)  && (round(floatval($floatBaseImpIva),2) === round(floatval($floatTotalBaseImpRetIva),2)) 
               && floatval($floatTotalBaseImpRetIva)>0  && isset($strFechaRetencion))
            {
                foreach ($arrayInfoPagoAutDetRetIva as $objInfoPagoAutDetRetIva)
                {
                    $objInfoPagoAutDetRetIva->setEstado("Pendiente");
                    $emFinanciero->persist($objInfoPagoAutDetRetIva);
                    $emFinanciero->flush();             
                }
            }             
            

            if($boolSumarizaBaseImp && !$boolErrorAdicional)
            {
                foreach ($arrayPorcentRetener as $floatPorcentajeRetener)
                {
                    $arrayInfoPagoAutDetPorcentRet    = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                     ->findBy( array('pagoAutomaticoId'    => $objInfoPagoAutomaticoCab->getId(),
                                                                                     'porcentajeRetencion' => $floatPorcentajeRetener));
                    // Obtengo base imponible total
                    foreach ($arrayInfoPagoAutDetPorcentRet as $objInfoPagoAutDetPorcentRet)
                    {
                        if($objInfoPagoAutDetPorcentRet->getEstado() !== 'No Procesa')
                        {
                            $floatTotalBaseImp += floatval($objInfoPagoAutDetPorcentRet->getBaseImponible());
                        }                        
                    }
                    // Valido base imponible total vs base imponible calculada
                    if($floatTotalBaseImp === $floatBaseImpCalc  && isset($strFechaRetencion))
                    {
                        $boolActualizaCab = false;
                        foreach ($arrayInfoPagoAutDetPorcentRet as $objInfoPagoAutDetPorcentRet)
                        {
                            $objInfoPagoAutDetPorcentRet->setEstado("Pendiente");
                            $emFinanciero->persist($objInfoPagoAutDetPorcentRet);
                            $emFinanciero->flush();
                            //Graba historial de detalle de retención.
                            $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                            $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutDetPorcentRet);
                            $objInfoPagoAutomaticoHist->setEstado('Pendiente');
                            $objInfoPagoAutomaticoHist->setObservacion('Se crea retención en estado Pendiente');
                            $objInfoPagoAutomaticoHist->setIpCreacion($arrayParametros['strClientIp']);
                            $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                            $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUser']);
                            $emFinanciero->persist($objInfoPagoAutomaticoHist);
                            $emFinanciero->flush();
                            $boolActualizaCab = true;
                        }
                        if($boolActualizaCab  && isset($strFechaRetencion))
                        {
                            $objInfoPagoAutomaticoCab->setEstado("Pendiente");
                            $emFinanciero->persist($objInfoPagoAutomaticoCab);
                            $emFinanciero->flush();
                        }
                    }                   
                }
            }
            
            if(!$boolFormatoValido && !$boolError)
            {
                $strResultado = 'Formato no soportado. Favor revisar. ';
                $boolError    = true;
            }
            
            $arrayInfoPagoAutDetNoProcesa  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->findBy( array('pagoAutomaticoId' => $objInfoPagoAutomaticoCab->getId(),
                                                                            'estado'           => 'No Procesa'));
            $arrayInfoPagoAutDetPendiente  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->findBy( array('pagoAutomaticoId' => $objInfoPagoAutomaticoCab->getId(),
                                                                          'estado'           => 'Pendiente'));
            $arrayInfoPagoAutDetError  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->findBy( array('pagoAutomaticoId' => $objInfoPagoAutomaticoCab->getId(),
                                                                          'estado'           => 'Error'));              
            $arrayInfoPagoAutDetalles      = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->findBy( array('pagoAutomaticoId' => $objInfoPagoAutomaticoCab->getId()));
            if(count($arrayInfoPagoAutDetNoProcesa) === count($arrayInfoPagoAutDetalles))
            {
                $objInfoPagoAutomaticoCab->setEstado("No Procesa");
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();                  
            }
            if(count($arrayInfoPagoAutDetPendiente) === count($arrayInfoPagoAutDetalles)  && isset($strFechaRetencion))
            {
                $objInfoPagoAutomaticoCab->setEstado("Pendiente");
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();                  
            }           
            if(count($arrayInfoPagoAutDetNoProcesa) >=0 && count($arrayInfoPagoAutDetPendiente)>0 && count($arrayInfoPagoAutDetError)===0
                && isset($strFechaRetencion))
            {
                $objInfoPagoAutomaticoCab->setEstado("Pendiente");
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();                  
            }           
            
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();
            
        } 
        catch (Exception $ex) 
        {
            error_log('Error al procesar Archivo '.$e->getMessage());
          
            $strResultado  = 'Error al procesar Archivo. Favor revisar formato.'; 
            if($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
                $emFinanciero->getConnection()->close(); 
            }           
        }
        return $strResultado;
    }
    
      /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * verDetalleRetencionAction()
     * Función que renderiza la página de Ver detalle de una retención.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 08-02-2021 
     * @since 1.0
     * 
     * @param int $intIdPagoAutomatico => id del pago automático
     * 
     * @return render - Página de Ver Estado de Cuenta.
     */
    public function verDetalleRetencionAction($intIdPagoAutomatico)
    {
        $emFinanciero    = $this->getDoctrine()->getManager("telconet_financiero");
        $strCliente      = "";        
        $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                  ->find($intIdPagoAutomatico); 
        
        if(is_object($objInfoPagoAutomaticoCab))
        {
            $strCliente  =  $objInfoPagoAutomaticoCab->getRazonSocial();           
        }       

        return $this->render('financieroBundle:infoRetencionAutomatica:verDetalleRetencion.html.twig',
                              array('intIdPagoAutomatico'  => $intIdPagoAutomatico,'strCliente' => $strCliente));
    }        
         
   /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * gridDetalleAction()
     * Función que obtiene un listado de detalles de retención de la tabla INFO PAGO AUTOMATICO DET.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-02-2021
     * @since 1.0
     *
     * @return $objResponse 
     */

    public function gridDetalleAction()
    {
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();        
        $strCodEmpresa        = $objSession->get('idEmpresa');         
        $intIdPagoAutomatico  = $objRequest->get("intIdPagoAutomatico");
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emFinanciero         = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        
        $servicePagoAutomatico      = $this->get('financiero.InfoPagoAutomatico');
        $objInfoPagoAutomaticoCab   = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                   ->find($intIdPagoAutomatico);

        $arrayInfoPagoAutomaticoDet  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                            ->findBy(array( 'pagoAutomaticoId'      =>  $intIdPagoAutomatico),
                                                     array( 'id'                    => 'DESC'));
        $intTotal      = count($arrayInfoPagoAutomaticoDet);
        $arrayDetalles = array();
        
        $intIndex      = 0;
       
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                               ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                                 'estado'          => 'Activo'));
        
        foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet):
            $strLogin                                   = '';
            $arrayParametrosDoc['strNumDocSustento']    = $objInfoPagoAutomaticoDet->getNumeroFactura();
            $arrayParametrosDoc['strCodEmpresa']        = $strCodEmpresa;
            $strNumeroFacturaSri                        = $servicePagoAutomatico->getNumDocumentoByNumDocSustento($arrayParametrosDoc);      
            $arrayParametrosFact                        = array();
            $arrayParametrosFact['strTipoDocumento']    = 'FAC';            
            $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFacturaSri;            
            $arrayParametrosFact['strCodEmpresa']       = $strCodEmpresa;  

            $arrayDatosFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                              ->getInformacionDocumento($arrayParametrosFact);
            
            if(count($arrayDatosFactura)>0)
            {
                $floatBaseImpIva   = 0;
                $intIdDocumento    = $arrayDatosFactura[0]['intIdDocumento'];
                $intIdPunto        = $arrayDatosFactura[0]['intIdPunto'];
                $arrayParametros   = array('intIdDocumento' => $intIdDocumento, 'intReferenciaId' => '');
                $arrayGetSaldoXFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                              ->getSaldosXFactura($arrayParametros);
                
                $arrayTotalIva =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                               ->getValorImpuesto($intIdDocumento, 'IVA'); 
                if (!empty($arrayTotalIva))
                {
                    $floatBaseImpIva   = round(floatval($arrayTotalIva[0]['totalImpuesto']), 2);
                }
               
                $floatSaldoFactura  = (!empty($arrayGetSaldoXFactura['intSaldo'])) ? round($arrayGetSaldoXFactura['intSaldo'],2) : 0;

                $objInfoPunto       = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                if(is_object($objInfoPunto))
                {
                    $strLogin = $objInfoPunto->getLogin();
                }
            }
            if(is_object($objAdmiParametroCab))
            {
                $strPorcentajeFormat = strval(number_format($objInfoPagoAutomaticoDet->getPorcentajeRetencion(),2));
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                              'descripcion' => 'FORMA PAGO',
                                                              'valor1'      => strval($objInfoPagoAutomaticoDet->getCodigoImpuesto()),
                                                              'valor2'      => $strPorcentajeFormat,
                                                              'empresaCod'  => $strCodEmpresa,
                                                              'estado'      => 'Activo'));
                if(is_object($objAdmiParametroDet))
                {
                    $strDescripcionFormaPago = $objAdmiParametroDet->getValor4();
                }
                else
                {
                   $strDescripcionFormaPago = 'No definido';   
                }
            }
            $strUrlShow   = '';
            $strEstado       = $objInfoPagoAutomaticoDet->getEstado();
            $intIdPagoAutDet = $objInfoPagoAutomaticoDet->getId();
            if(isset($intIdDocumento))
            {
                $strUrlShow  = $this->generateUrl('infodocumentofinancierocab_show', array('id' => $intIdDocumento));
            }
        
            $floatBaseImponibleXml = round($objInfoPagoAutomaticoDet->getBaseImponible(), 2);
            $floatBaseImponible    = round($objInfoPagoAutomaticoDet->getBaseImponibleCal(), 2);
            
            $arrayDetalles[] = array('intIdPagoAutDet'        => $objInfoPagoAutomaticoDet->getId(),
                                     'strFecha'               => $objInfoPagoAutomaticoDet->getFecha(),
                                     'strCliente'             => $objInfoPagoAutomaticoCab->getRazonSocial(),
                                     'strLogin'               => $strLogin,
                                     'strFactura'             => $strNumeroFacturaSri,
                                     'strSaldo'               => number_format($floatSaldoFactura,2),              
                                     'strBaseImponible'       => number_format($floatBaseImponibleXml,2),
                                     'strBaseImponibleCal'    => number_format($floatBaseImponible,2),
                                     'strBaseImponibleIva'    => number_format($floatBaseImpIva,2),
                                     'strFormaPago'           => $strDescripcionFormaPago,
                                     'strPorcentajeRetencion' => number_format($objInfoPagoAutomaticoDet->getPorcentajeRetencion(),2),
                                     'strValor'               => number_format($objInfoPagoAutomaticoDet->getMonto(),2),                
                                     'strEstado'              => $strEstado,
                                     'strAcciones'            => array('intIdPagoAutDet'  => $intIdPagoAutDet,
                                                                       'intIdDocumento'   => $intIdDocumento,
                                                                       'linkVer'          => $strUrlShow,
                                                                       'strEstado'        => $strEstado)                
                                    );
            $intIndex++;
        endforeach;

        if(empty($arrayDetalles))
        {

            $arrayDetalles[] = array('intIdPagoAutDet'        => '',
                                     'strFecha'               => '',
                                     'strCliente'             => '',
                                     'strLogin'               => '',
                                     'strFactura'             => '',
                                     'strSaldo'               => '',
                                     'strBaseImponible'       => '',              
                                     'strBaseImponibleCal'    => '',
                                     'strFormaPago'           => '',
                                     'strPorcentajeRetencion' => '',
                                     'strValor'               => '',
                                     'strEstado'              => '',
                                     'strAcciones'            => array('strEstado'  => '')  
                                   );
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayDetalles)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * @Secure(roles="ROLE_65-7817")    
    * ajaxProcesarRetencionesAction()
    * Función que crea un Proceso Masivo para el procesamiento de retenciones.
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 05-04-2021
    *
    * @param arrayIdsGrupoPromocion,   Array con los ids de los grupos de Promociones ADMI_GRUPO_PROMOCION       
    * 
    * @return $strResponse
    */    
    public function ajaxProcesarRetencionesAction()
    {                      
        $objRequest               = $this->getRequest();
        $objSesion                = $objRequest->getSession();
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $arrayIdsRetSelecionadas  = $objRequest->get('arrayIdsRetenciones'); 
        $strIdsRetencionesSelect  = implode(",", $arrayIdsRetSelecionadas);
        $strUsrCreacion           = $objSesion->get('user');
        $strCodEmpresa            = $objSesion->get('idEmpresa');
        $strPrefijoEmpresa        = $objSesion->get('prefijoEmpresa');        
        $strIpCreacion            = $objRequest->getClientIp();
        
        $arrayParametros          = array(
                                          'arrayIdsPagosAutomaticos'  => $arrayIdsRetSelecionadas,
                                          'strIdsRetencionesSelect'   => $strIdsRetencionesSelect,
                                          'strUsrCreacion'            => $strUsrCreacion,
                                          'strCodEmpresa'             => $strCodEmpresa,
                                          'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                                          'strIpCreacion'             => $strIpCreacion
                                         );    
        try
        {                        
            $servicePagoAutomatico = $this->get('financiero.InfoPagoAutomatico');
            $strResponse           = $servicePagoAutomatico->procesarRetenciones($arrayParametros);                
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al procesar la(s) Retencion(es), por favor consulte con el Administrador.";           
        }

        return new Response($strResponse);
    }

    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * eliminarRetencionAction()
     * Función que cambia a estado Eliminado el registro correspondiente a una retención.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 14-04-2021 
     * @since 1.0
     *
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 14-09-2021 
     * @since 1.1 Se agrega seteo de fecha y usuario de última modificación.
     *  
     * @param int $intIdPagoAutomatico => id del pago automático
     * 
     * @return render - Renderiza a listado de retenciones..
     */
    public function eliminarRetencionAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strUsrCreacion     = $objSesion->get('user');      
        $strIpCreacion      = $objRequest->getClientIp();        
        $strObservacion     = $objRequest->get("observacionEliminar");
        $intIdMotivo        = intval($objRequest->get('motivos_retencion'));
        $intIdPagAutomatico = intval($objRequest->get("idPagoAutomatico"));         
        $emFinanciero       = $this->getDoctrine()->getManager("telconet_financiero");
        $strResponse        = 'Ok';
        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                      ->find($intIdPagAutomatico);
            
            $arrayInfoPagoAutomaticoDet     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                           ->findBy(array( "pagoAutomaticoId" => $intIdPagAutomatico));            

            if(is_object($objInfoPagoAutomaticoCab) && count($arrayInfoPagoAutomaticoDet)>0)
            {                                 
                $objInfoPagoAutomaticoCab->setEstado('Eliminado');
                $objInfoPagoAutomaticoCab->setObservacion($strObservacion);
                $objInfoPagoAutomaticoCab->setFeUltMod(new \DateTime('now'));
                $objInfoPagoAutomaticoCab->setUsrUltMod($strUsrCreacion);                
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();              
            }
            else 
            {
                $strResponse = "Error";
            }
        
            foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet): 
                
                $objInfoPagoAutomaticoDet->setEstado('Eliminado');
                $objInfoPagoAutomaticoDet->setObservacion($strObservacion);
                $objInfoPagoAutomaticoDet->setFeUltMod(new \DateTime('now'));
                $objInfoPagoAutomaticoDet->setUsrUltMod($strUsrCreacion);                 
                $emFinanciero->persist($objInfoPagoAutomaticoDet);                
                $emFinanciero->flush();   
                
                //Graba historial de detalle de retención.
                $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                $objInfoPagoAutomaticoHist->setMotivoId($intIdMotivo);
                $objInfoPagoAutomaticoHist->setEstado('Eliminado');
                $objInfoPagoAutomaticoHist->setObservacion($strObservacion);
                $objInfoPagoAutomaticoHist->setIpCreacion($strIpCreacion);
                $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                $objInfoPagoAutomaticoHist->setUsrCreacion($strUsrCreacion);
                $emFinanciero->persist($objInfoPagoAutomaticoHist);
                $emFinanciero->flush(); 
            endforeach;
            
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();             
            
        }
        catch(\Exception $e)
        {
            error_log('Error al eliminar retencion '.$e->getMessage());
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $strResponse = "Error";           
        }
        return new Response($strResponse);
    }
    
    /**
     * @Secure(roles="ROLE_65-7817")
     * Muestra la información del historial de la retención enviada como parámetro
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 27-04-2021
     * 
     */    
    public function gridHistorialAction()
    {             
        $objRequest      = $this->getRequest();
        $emFinanciero    = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $intIdPagoAutDet = $objRequest->get('intIdPagoAutDet');
        $objJsonResponse = new JsonResponse();
        $arrayResultado  = array("total" => 0, "registros" => array());
        $intContador     = 0;
        
        $arrayInfoHistorial = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoHist')
                              ->findBy(array('detallePagoAutomaticoId' => $intIdPagoAutDet),
                                       array('feCreacion' => 'asc', 'id' => 'asc'));        
        
        if( !empty($arrayInfoHistorial) )
        {
            foreach( $arrayInfoHistorial as $objInfoPagoAutomaticoHist )
            {
                
                if($objInfoPagoAutomaticoHist->getMotivoId() != null)
                {
                    $objAdmiMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($objInfoPagoAutomaticoHist->getMotivoId());

                    if(is_object($objAdmiMotivo))
                    {
                        $strNombreMotivo = $objAdmiMotivo->getNombreMotivo();
                    }
                    else
                    {
                        $strNombreMotivo = "";
                    }
                }
                else
                {
                    $strNombreMotivo = "";
                }                
                $arrayItem              = array();
                $arrayItem["detalle"]   = $objInfoPagoAutomaticoHist->getObservacion();
                $arrayItem["estado"]    = $objInfoPagoAutomaticoHist->getEstado();
                $arrayItem["usuario"]   = $objInfoPagoAutomaticoHist->getUsrCreacion();
                $arrayItem["fecha"]     = $objInfoPagoAutomaticoHist->getFeCreacion()
                                          ? strval(date_format($objInfoPagoAutomaticoHist->getFeCreacion(), "d/m/Y G:i")) : '';
                $arrayItem["motivo"]    = $strNombreMotivo;
                
                $arrayResultado["registros"][] = $arrayItem;
                
                $intContador++;
            }
            
            $arrayResultado["total"] = $intContador;
        }       
        
        $objJsonResponse->setData($arrayResultado);
        
        return $objJsonResponse;        
    }

    /**
     * @Secure(roles="ROLE_65-7817")
     * Método para renderizar el detalle de una retención.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 13-05-2021
     * 
     */      
    public function editarFechaRetencionAction()
    {
        $objRequest            = $this->getRequest();
        $objSesion             = $objRequest->getSession();
        $strUsrCreacion        = $objSesion->get('user');
        $strCodEmpresa         = $objSesion->get('idEmpresa');
        $strIpCreacion         = $objRequest->getClientIp();        
        $strFeAutorizacion     = $objRequest->get("fecha_autorizacion");
        $intIdPagAutomatico    = intval($objRequest->get("idPagAutomatico"));         
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $servicePagoAutomatico = $this->get('financiero.InfoPagoAutomatico'); 
        
        $strResponse        = 'Ok';
        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                      ->find($intIdPagAutomatico);
            
            $arrayInfoPagoAutomaticoDet     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                           ->findBy(array( "pagoAutomaticoId" => $intIdPagAutomatico));
        
            foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet):
                $strReferencia                        = $objInfoPagoAutomaticoDet->getNumeroReferencia();
                $arrayParametros['strNumDocSustento'] = $objInfoPagoAutomaticoDet->getNumeroFactura();
                $arrayParametros['strCodEmpresa']     = $strCodEmpresa;
                $strNumeroFactura  = $servicePagoAutomatico->getNumDocumentoByNumDocSustento($arrayParametros);
                
                $arrayParametrosFact                        = array();
                $arrayParametrosFact['strTipoDocumento']    = 'FAC';            
                $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFactura;            
                $arrayParametrosFact['strCodEmpresa']       = $strCodEmpresa;  

                $arrayDatosFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->getInformacionDocumento($arrayParametrosFact);

                if(isset($strNumeroFactura) && (count($arrayDatosFactura)>0))
                {
                    $objInfoPagoAutomaticoDet->setObservacion("Ret#".$strReferencia.' aplica Factura #'.$strNumeroFactura.
                                                                  ' Fecha '.$strFeAutorizacion);
                    if(floatval($objInfoPagoAutomaticoDet->getMonto())>0)
                    {
                        $objInfoPagoAutomaticoDet->setEstado('Pendiente');
                    }
                    $objInfoPagoAutomaticoDet->setFecha($strFeAutorizacion);
                    $emFinanciero->persist($objInfoPagoAutomaticoDet);
                    $emFinanciero->flush();   

                    $strEstadoHist = $objInfoPagoAutomaticoDet->getEstado();
                    //Graba historial de detalle de retención.
                    $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                    $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                    $objInfoPagoAutomaticoHist->setEstado($strEstadoHist);
                    $objInfoPagoAutomaticoHist->setObservacion('Se actualiza fecha de proceso de la retención');
                    $objInfoPagoAutomaticoHist->setIpCreacion($strIpCreacion);
                    $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                    $objInfoPagoAutomaticoHist->setUsrCreacion($strUsrCreacion);
                    $emFinanciero->persist($objInfoPagoAutomaticoHist);
                    $emFinanciero->flush();
                }
            endforeach;
            
            if(is_object($objInfoPagoAutomaticoCab) && count($arrayInfoPagoAutomaticoDet)>0)
            {                                 
                $objInfoPagoAutomaticoCab->setEstado('Pendiente');
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();
            }            
            
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();             
            
        }
        catch(\Exception $e)
        {
            error_log('Error al editar fecha de proceso en la retencion '.$e->getMessage());
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $strResponse = "Error";           
        }
        return new Response($strResponse);
    }
    
     /**
     * @Secure(roles="ROLE_65-7817")
     * cargarReporteAction()
     * Función que renderiza la página principal para cargar y leer reporte de retención mediante un archivo excel.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 04-06-2021
     * @since 1.0
     * 
     * @return render
     */
    public function cargarReporteAction()
    {          
        return $this->render('financieroBundle:infoRetencionAutomatica:cargarReporte.html.twig', array());
    }
    
    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * procesarReporteAction     *
     * Metodo encargado de procesar el reporte de tributación en formato excel.
     *
     * @return json con resultado del proceso
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 04-06-2021 
     *
     */ 
    public function procesarReporteAction()
    {
        $objRequest                = $this->getRequest();        
        $objSession                = $objRequest->getSession();
        $strUser                   = $objSession->get('user');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $arrayPuntoSession         = $objSession->get('ptoCliente');
        $intIdPuntoSession         = (!empty($arrayPuntoSession['id'])) ? $arrayPuntoSession['id'] : -1;
        $strFechaActual            = strval(date_format(new \DateTime('now'), "dmYGi"));
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil               = $this->get('schema.Util');

        $arrayInfoFile             = $_FILES['rpt_retencion'];
        $strArchivo                = $arrayInfoFile["name"];
        /*Definimos las variables necesarias para el servicio.*/
        $strApp                    = '';
        $strSubModulo              = '';
        $objRespuesta              = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/html');

        try
        {
            if($arrayInfoFile && count($arrayInfoFile) > 0)
            {
                $arrayArchivo     = explode('.', $strArchivo);
                $intCountArray    = count($arrayArchivo);
                $strNombreArchivo = $arrayArchivo[0];
                $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
                $strPrefijo       = substr(md5(uniqid(rand())), 0, 6);

                if(($strExtArchivo && $strExtArchivo == 'xlsx'))
                {
                    $strNuevoNombre = "RPT_" . $strPrefijo ."_".$strFechaActual. "." . $strExtArchivo;

                    $arrayParametros                           = array();
                    $arrayParametros['strNuevoNombre']         = $strNuevoNombre;
                    $arrayParametros['objRequest']             = $objRequest;
                    $arrayParametros['strTipoModulo']          = $strExtArchivo;
                    $arrayParametros['strNombreDocumento']     = $strNombreArchivo;
                    $arrayParametros['intIdPuntoSession']      = $intIdPuntoSession;
                    $arrayParametros['strUser']                = $strUser;
                    $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
                    $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
                    $arrayParametros['strClientIp']            = $objRequest->getClientIp();

                    $strResultado = $this->isProcesarRptTributacion($arrayParametros);

                }
                else
                {
                    throw new \Exception('Extensión de archivo no válida.Favor verificar que sea formato xlsx'); 
                }

            }
            else
            {
                throw new \Exception('Error : Archivo no válido.'); 
            }
                         
            $objRespuesta->setContent($strResultado);
            
            return $objRespuesta;
        }
        catch(\Exception $e)
        {
            $strResultado = 'Error-Error al procesar archivo . '.$e->getMessage();
            $objRespuesta->setContent($strResultado);
            return $objRespuesta;
        }
    }
    
    /**
     * @Secure(roles="ROLE_65-7817")
     * 
     * isProcesarRptTributacion
     *
     * Método encargado de leer y procesar la información  del reporte enviado como parámetro.
     *          
     * @return boolean
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-06-2021 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 07-07-2021 Se agrega funcionalidad para generación y envío archivo de respuesta.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 25-05-2022 Se agrega validación para excluir cambio a estado Pendiente de registros en estado Eliminado.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 01-06-2022 Se agrega filtro para que consulte sólo retenciones con estado Error.
     */ 
    public function isProcesarRptTributacion($arrayParametros)
    {
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'];
        $strUser               = $arrayParametros['strUser'];
        $intNumDetallesValidos = 0;
        $strResultado          = 'OK';
        $objRequest            = $this->getRequest();
        $objArchivo            = $objRequest->files->get('rpt_retencion');
        $strAbsolutePath       = $objArchivo->getPathName();
        $strInputFile          = $_FILES['rpt_retencion']['tmp_name'];
        $arrayArchivo          = explode('.', $_FILES['rpt_retencion']['name']);
        $intCountArray         = count($arrayArchivo);
        $strExtArchivo         = $arrayArchivo[$intCountArray - 1];
        $strPrefijo            = substr(uniqid(rand()), 0, 6);
        $strFechaActual        = strval(date_format(new \DateTime('now'), "dmYGi"));
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $objServiceUtil        = $this->get('schema.Util');
        $strApp                = '';
        $strSubModulo          = '';
        $strNuevoNombre = $strPrefijo ."_".$strFechaActual. "." . $strExtArchivo;
        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'CONFIGURACION NFS',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
                $strPathAdicional  = $objAdmiParametroDet->getValor1();
                $strApp            = $objAdmiParametroDet->getValor2();
                $strSubModulo      = $objAdmiParametroDet->getValor3();                           

            }
            else
            {
                throw new \Exception('Error, no existe la configuración requerida para PATH ADICIONAL ');
            }                          
        }        

        $strData = file_get_contents( $strInputFile );
        $arrayPathAdicional[]   = array('key' => $strPathAdicional);
        
        $arrayParamNfs = array(
            'prefijoEmpresa'       => $strPrefijoEmpresa,
            'strApp'               => $strApp,
            'strSubModulo'         => $strSubModulo,
            'arrayPathAdicional'   => $arrayPathAdicional,
            'strBase64'            =>  base64_encode($strData),
            'strNombreArchivo'     => $strNuevoNombre,
            'strUsrCreacion'       => $strUser);
        $arrayResponseNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);

        if($arrayResponseNfs['intStatus']=='500')
        {
            throw new \Exception($arrayResponseNfs['strMensaje']);    
        }
        // Ruta donde se almacena reporte de tributación
        $strTargetPath = $arrayResponseNfs['strUrlArchivo'];      
 
                        
        $objAdmiFormatoPagoAut     = $emFinanciero->getRepository('schemaBundle:AdmiFormatoPagoAutomatico')
                                                  ->findOneBy(array( "empresaCod"        => $strCodEmpresa,
                                                                     "colValidaTipo"     => "RPT_RET",
                                                                     "estado"            => "Activo"));
       
        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            if(is_object($objAdmiFormatoPagoAut) && isset($strAbsolutePath))
            {
                $strFormatoFecha   = $objAdmiFormatoPagoAut->getFormatoFecha();
                $objReader         = PHPExcel_IOFactory::createReaderForFile($strAbsolutePath);
                $objReader->setReadDataOnly(true);

                $objXLS = $objReader->load($strAbsolutePath);
                
                for ($intNumhoja = 0; $intNumhoja < $objXLS->getSheetCount(); $intNumhoja++)
                {
                    $objWorksheet = $objXLS->getSheet($intNumhoja);

                    $strTitle = trim(strtoupper($objWorksheet->getTitle()));
                    

                    if (isset($strTitle))
                    {
                        $objWorksheet->setRightToLeft(false); 
                        $intHighestRow  = $objWorksheet->getHighestRow();
                        
                        for ($intCont = intval($objAdmiFormatoPagoAut->getFilaInicia()); $intCont <= intval($intHighestRow); $intCont++)
                        {
                            $boolActualizaFecha = false;
                            $strIdentificacionFile = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColValidaRef(),
                                                     $intCont)->getValue();
                            $strReferencia = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColReferencia(),$intCont)->getValue();
                            $strReferencia = str_replace("-","", $strReferencia);
                            
                            $arrayInfoPagoAutDet     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                    ->findBy(array( "empresaCod"        => $strCodEmpresa,
                                                                                    "numeroReferencia"  => $strReferencia,
                                                                                    "estado"            => "Error",
                                                                                    "fecha"             => null));
                            if(count($arrayInfoPagoAutDet)>0)
                            {
                                foreach($arrayInfoPagoAutDet as $objInfoPagoAutDet):
                                    


                                    $strFechaTransaccion = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColFecha(),
                                                                                                $intCont)->getValue();
                                    $intIdPagAutomatico  = $objInfoPagoAutDet->getPagoAutomaticoId();
                                    
                                    $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                              ->find($intIdPagAutomatico);
                                                                              
                                    if(is_object($objInfoPagoAutomaticoCab))
                                    {
                                        $strIdentificacionCliente = $objInfoPagoAutomaticoCab->getIdentificacionCliente();
                                        if(strcmp($strIdentificacionFile,$strIdentificacionCliente)===0)
                                        {
                                            $boolActualizaFecha = true;
                                        }
                                    }                                
                                    if($boolActualizaFecha)
                                    {
                                        if(strpos($strFechaTransaccion,'/') > 0)
                                        {
                                            if(strpos($strFechaTransaccion,' ') > 0)
                                            {
                                                $arrayFeTransaccionNew  =  explode(' ', $strFechaTransaccion);
                                                $arrayFeTransaccion     =  explode('/', $arrayFeTransaccionNew[0]);
                                            }
                                            else
                                            {
                                                $arrayFeTransaccion  =  explode('/', $strFechaTransaccion);
                                            }
                                        }
                                        else
                                        {
                                            $arrayFeTransaccion  =  explode('-', $strFechaTransaccion);
                                        }                                
                                        $strFecha            = PHPExcel_Shared_Date::ExcelToPHP($strFechaTransaccion);
                                        $strFecha            = strtotime("+1 day",$strFecha);
                                        $strFecha            = date("Y-m-d", $strFecha);                                

                                        if(count($arrayFeTransaccion)==3 && isset($strFormatoFecha))
                                        {
                                            $strFechaTransaccion = $arrayFeTransaccion[2].'/'.$arrayFeTransaccion[1].'/'.$arrayFeTransaccion[0];
                                        }
                                        
                                        if($objInfoPagoAutDet->getEstado() !== 'Eliminado')
                                        {
                                            $objInfoPagoAutDet->setEstado('Pendiente');
                                            $objInfoPagoAutDet->setFecha($strFechaTransaccion);
                                            $emFinanciero->persist($objInfoPagoAutDet);
                                            $emFinanciero->flush();

                                            $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                                            $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutDet);
                                            $objInfoPagoAutomaticoHist->setEstado('Pendiente');
                                            $objInfoPagoAutomaticoHist->setObservacion('Se actualiza fecha de proceso (Reporte de Tributación).');
                                            $objInfoPagoAutomaticoHist->setIpCreacion($arrayParametros['strClientIp']);
                                            $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                                            $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUser']);
                                            $emFinanciero->persist($objInfoPagoAutomaticoHist);
                                            $emFinanciero->flush();
                                        }
                                    }                                    
                                endforeach;
                                if($objInfoPagoAutomaticoCab->getEstado() !== 'Eliminado')
                                {
                                    $objInfoPagoAutomaticoCab->setEstado("Pendiente");
                                    $emFinanciero->persist($objInfoPagoAutomaticoCab);
                                    $emFinanciero->flush();
                                }
                            }
                            $intNumDetallesValidos++;
                        }
                    }                  
                }
                if($arrayResponseNfs['intStatus']==200)
                {
                    $arrayParamRptRetExist                     = array();
                    $arrayParamRptRetExist['strUrlFile']       = $strTargetPath;
                    $arrayParamRptRetExist['strUsrCreacion']   = $arrayParametros['strUser'];
                    $arrayParamRptRetExist['strCodEmpresa']    = $strCodEmpresa;
                    $arrayParamRptRetExist['strIpCreacion']    = $arrayParametros['strClientIp'];

                    $arrayResponse = $emFinanciero->getRepository ( 'schemaBundle:InfoPagoAutomaticoCab' )
                                                  ->procesaRptRetencionesExistentes($arrayParamRptRetExist);
                    if(['strStatus']==='Error')
                    {
                        error_log('Error al generar reporte de retenciones existentes');                      
                    }
                }else
                {
                    return array("error"=>true,"msg"=>"No se puede almacenar archivo para realizar la busqueda, verifique configuracion");
                }              
                $emFinanciero->getConnection()->commit();
                $emFinanciero->getConnection()->close();                
            }
            
        } catch (Exception $ex) 
        {
            $strResultado          = 'Error al procesar reporte.'; 
            if($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
                $emFinanciero->getConnection()->close(); 
            }             
           
        }
        return $strResultado;        
    }
    
    
    /**
     * Documentación para el método 'getMotivosRetencion'.
     * Función que retorna listado de motivos asociados a la funcionalidad de retenciones.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 10-06-2021
     * 
     * @return object $objResponse
     */    
    public function getMotivosRetencionAction()
    {
        $objRequest      = $this->getRequest();        
        $objSession      = $objRequest->getSession();
        $strCodEmpresa   = $objSession->get('idEmpresa');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');       
        $emSeguridad     = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {         
            $objAdmiParamDetInfoMotivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                     'descripcion' => 'CONFIGURACION MOTIVO',
                                                                     'empresaCod'  => $strCodEmpresa,
                                                                     'estado'      => 'Activo'));
            if(is_object($objAdmiParamDetInfoMotivo))
            {
                $strNombreModulo   = $objAdmiParamDetInfoMotivo->getValor1();
                $strNombreItemMenu = $objAdmiParamDetInfoMotivo->getValor2();
                $strNombreAccion   = $objAdmiParamDetInfoMotivo->getValor3();
                $strEstado         = $objAdmiParamDetInfoMotivo->getEstado();
            }
        }        
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => $strNombreModulo)); 
        
        $objSistItemMenu = $emSeguridad->getRepository('schemaBundle:SistItemMenu')
                                       ->findOneBy(array( 'nombreItemMenu' => $strNombreItemMenu,'estado' => $strEstado));
        
        $objSistAccion = $emSeguridad->getRepository('schemaBundle:SistAccion')
                                     ->findOneBy(array( 'nombreAccion' => $strNombreAccion,'estado' => $strEstado));
        
        if(is_object($objSistModulo) && is_object($objSistItemMenu) && is_object($objSistAccion))
        {  
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId'   => $objSistModulo->getId(),
                                                                     'itemMenuId' => $objSistItemMenu->getId(),
                                                                     'accionId'   => $objSistAccion->getId()));

            if(is_object($objSeguRelacionSistema))
            {
                $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                            ->loadMotivos($objSeguRelacionSistema->getId());

                foreach($arrayResultado as $objMotivo):
                    $arrayMotivos[] = array(
                        'id'          => $objMotivo->getId(),
                        'descripcion' => $objMotivo->getNombreMotivo()
                    );
                                    
                endforeach;
                
                
            }
        }
        $objResponse = new Response(json_encode(array('lista_motivos' => $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }   
}
