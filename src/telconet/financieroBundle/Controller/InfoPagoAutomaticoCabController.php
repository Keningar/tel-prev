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
use telconet\financieroBundle\Service\InfoPagoService;
use telconet\financieroBundle\Service\InfoPagoDetService;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoCaract;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;

/**
 * InfoPagoCab controller.
 *
 */
class InfoPagoAutomaticoCabController extends Controller implements TokenAuthenticatedController
{

    /**
     * @Secure(roles="ROLE_65-7457")
     * indexAction()
     * Función que renderiza la página principal de pagos automáticos subidos mediante un archivo.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 19-08-2020
     * @since 1.0
     * 
     * @return render - Página de Consulta de estados de cuenta - pagos.
     */
    public function indexAction()
    {          
        return $this->render('financieroBundle:infoPagoAutomaticoCab:index.html.twig', array());
    }
    /**
     * getEstadosAction()
     * Función que retorna los estados para consulta de las estados de cuenta.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 27-10-2020
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
     * @Secure(roles="ROLE_65-7457")
     * gridEstadosCuentaAction()
     * Función que obtiene un listado de cabecesras de estado de cuenta de la tabla INFO PAGO AUTOMATICO CAB.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020
     * @since 1.0
     *
     * @return $objResponse 
     */
    public function gridEstadosCuentaAction()
    {
        $objRequest     = $this->getRequest();
        $strFechaDesde  = $objRequest->get("strFechaDesde");
        $strFechaHasta  = $objRequest->get("strFechaHasta");
        $intBcoCta      = $objRequest->get("intBcoCta");        
        $objSession     = $objRequest->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $emFinanciero   = $this->getDoctrine()->getManager("telconet_financiero");

        $arrayParametros                       = array();
        $arrayParametros['strFechaDesde']      = $strFechaDesde;
        $arrayParametros['strFechaHasta']      = $strFechaHasta;
        $arrayParametros['intCtaContableId']   = $intBcoCta;
        $arrayParametros['strEmpresaCod']      = $strEmpresaCod;
        

        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')->getEstadosCtaPorCriterios($arrayParametros);

        $arrayRegistros   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        $arrayPagos       = array();

        foreach($arrayRegistros as $arrayDatos):    
            
            $strEstado           = $arrayDatos['strEstado'];
            $strBanco            = $arrayDatos['strBanco'];
            $intIdPagoAutomatico = $arrayDatos['intIdPagoAutomatico'];
            $strFeCreacion       = strval(date_format($arrayDatos['dateFeCreacion'], "Y-m-d"));
            $strUsrCreacion      = $arrayDatos['strUsrCreacion'];
            
            $arrayPagos[] =   array('intIdPagoAutomatico'    => $intIdPagoAutomatico,
                                    'strBanco'               => $strBanco,
                                    'strEstado'              => $strEstado,
                                    'strFeCreacion'          => $strFeCreacion,
                                    'strUsrCreacion'         => $strUsrCreacion,
                                    'strOpAcciones'          => array('linkVer'     => $this->generateUrl('infoPagoAutomatico_verEstadoCuenta', 
                                                                                      array('intIdPagoAutomatico' => $intIdPagoAutomatico)),
                                                                      'intIdPagoAutomatico' => $intIdPagoAutomatico,
                                                                      'strEstado'           => $strEstado)
                                   );
        endforeach;
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayPagos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * getCuentasContablesBancoAction, obtiene la información de las cuentas contables en ADMI_CUENTA_CONTABLE.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020          
     *                    
     * @return Response lista de Cuentas contable.
     */
    public function getCuentasContablesBancoAction()
    {
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();        
        $strEmpresaCod   = $objSession->get('idEmpresa'); 
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero    = $this->getDoctrine()->getManager("telconet_financiero");
        $strTipo         = '';
        $strConsultaPara = '';
        $arrayListCuentasBancarias = [];
        $arrayCuentasBancarias     = [];
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION PAGOS', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'CUENTA CONTABLE',
                                                               'empresaCod'  => $strEmpresaCod,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
                $strTipo           = $objAdmiParametroDet->getValor1();
                $strConsultaPara   = $objAdmiParametroDet->getValor2();
            }
        }        
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                       ->getResultadoNumeroCuentasBancosContables($strTipo,$strEmpresaCod,$strConsultaPara);
        if(count($arrayResultado['registros'])>0)
        {
            $arrayListCuentasBancarias = $arrayResultado['registros'];
        }

        foreach($arrayListCuentasBancarias as $arrayCuenta)
        {
            $arrayCuentasBancarias[] = array('id'     => $arrayCuenta["id"], 
                                             'nombre' => trim($arrayCuenta["descripcion"]."   Cta.".$arrayCuenta["noCta"]));
        } 
        $objResponse = new Response(json_encode(array('cuentas_bancarias' => $arrayCuentasBancarias)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_65-7457")
     * newAction()
     * Función que renderiza la página principal para subir estado de cuenta mediante un archivo.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 19-08-2020
     * @since 1.0
     * 
     * @return render
     */
    public function newAction()
    {          
        return $this->render('financieroBundle:infoPagoAutomaticoCab:new.html.twig', array());
    } 
    
    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * documentosFileUploadAction     *
     * Metodo encargado de procesar el archivo subido en el formulario y coloca en el directorio de destino fisico y despues guarda 
     * en la base de forma logica.
     *
     * @return json con resultado del proceso
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 01-09-2020 
     *
     * @author Edgar Holguín <eholguin@telconet.ec
     * @version 1.1 30-06-2022 Se agrega funcionalidad para guardar archivo con NFS.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec
     * @version 1.2 11-07-2022 Se actualiza envio de mensajes de error. 
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
        $intIdCuentaContable       = intval($objRequest->get('banco_cuenta'));
        $emFinanciero              = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil               = $this->get('schema.Util');
        $strIpCreacion             = $objRequest->getClientIp();
        $strUsrCreacion            = $objSession->get('user');
        $strFechaActual            = strval(date_format(new \DateTime('now'), "dmYGi"));

        $objAdmiCuentaContable     = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                                  ->find($intIdCuentaContable);
        if(is_object($objAdmiCuentaContable))
        {
             $intIdBcoTipoCta = intval($objAdmiCuentaContable->getValorCampoReferencial());
             $strBcoCta       = $objAdmiCuentaContable->getDescripcion().'_'. $objAdmiCuentaContable->getNoCta();
        }
        else 
        {
            throw new \Exception('No existe cuenta contable asosiada.Favor revisar'); 
        }
        
        $objAdmiFormatoPagoAut     = $emFinanciero->getRepository('schemaBundle:AdmiFormatoPagoAutomatico')
                                                  ->findOneBy(array( "empresaCod"         => $strCodEmpresa,
                                                                      "bancoTipoCuentaId" => $intIdBcoTipoCta,
                                                                      "estado"            => "Activo"));

        if(!is_object($objAdmiFormatoPagoAut))
        {
            $objAdmiFormatoPagoAut     = $emFinanciero->getRepository('schemaBundle:AdmiFormatoPagoAutomatico')
                                                      ->findOneBy(array( "empresaCod"       => $strCodEmpresa,
                                                                         "cuentaContableId" => $intIdCuentaContable,
                                                                         "estado"           => "Activo"));
        }        
        $arrayInfoFile     = $_FILES['estado_cta'];
        $strArchivo        = $arrayInfoFile["name"];
        $strTamano         = $arrayInfoFile["size"];
        $serverRoot        = $_SERVER['DOCUMENT_ROOT'];
        /*Definimos las variables necesarias para el servicio.*/
        $strApp            = 'TELCOS';
        $strModulo         = 'FINANCIERO';
        $strSubModulo      = 'AUTOMATIZACION_PAGOS';
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/html');
        
        try
        {
            if(is_object($objAdmiFormatoPagoAut))
            {

                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION PAGOS', 
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

                //Verifica si existe el archivo
                if($arrayInfoFile && count($arrayInfoFile) > 0)
                {
                    $arrayArchivo     = explode('.', $strArchivo);
                    $countArray       = count($arrayArchivo);
                    $strNombreArchivo = $arrayArchivo[0];
                    $strExtArchivo    = $arrayArchivo[$countArray - 1];
                    $strPrefijo       = substr(md5(uniqid(rand())), 0, 6);

                    if(($strExtArchivo && ($strExtArchivo == 'xlsx' || $strExtArchivo == 'xls')))
                    {
                        $strNuevoNombre = $strBcoCta . "_" . $strPrefijo ."_".$strFechaActual. "." . trim($strExtArchivo);
                        $arrayPathAdicional[] = array('key' => $strPathAdicional);
                        $objArchivo      = $objRequest->files->get('estado_cta');

                        $strArchivo      = base64_encode(file_get_contents($objArchivo->getPathName()));
                        
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
                        $arrayParametros['strReadPath']            = $objArchivo->getPathName();
                        $arrayParametros['strDestino']             = $strTargetPath;
                        $arrayParametros['objRequest']             = $objRequest;
                        $arrayParametros['strTipoModulo']          = $strExtArchivo;
                        $arrayParametros['strNombreDocumento']     = $strNombreArchivo;
                        $arrayParametros['intIdPuntoSession']      = $intIdPuntoSession;
                        $arrayParametros['strUser']                = $strUser;
                        $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
                        $arrayParametros['objAdmiFormatoPagoAut']  = $objAdmiFormatoPagoAut;
                        $arrayParametros['objAdmiCuentaContable']  = $objAdmiCuentaContable;
                        $arrayParametros['strClientIp']            = $objRequest->getClientIp();

                        $bool = $this->boolGuardarDocumento($arrayParametros);

                        if($bool)
                        {
                            $strResultado = 'OK';
                            $this->boolProcesarArchivo($arrayParametros);

                        }
                        else
                        {
                            unlink($strTargetPath);
                            throw new \Exception('ErrorA');                            
                        }

                    }
                    else
                    {
                        throw new \Exception('ErrorB');
                    }

                }
                else
                {
                    throw new \Exception('ErrorC');
                }
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage()==='ErrorA'||$e->getMessage()==='ErrorB'||$e->getMessage()==='ErrorC'||$e->getMessage()==='ErrorD')
            {
                $strResultado = $e->getMessage();
            }
            else
            {
                $strResultado = 'Error al procesar archivo. ';
                $serviceUtil->insertError( 'Telcos+', 
                                           'InfoPagoAutomaticoController.documentosFileUploadAction', 
                                           'Error al guardar estado de cuenta. '.$e->getMessage(), 
                                           $strUsrCreacion, 
                                           $strIpCreacion);                
            }
        }
        $objRespuesta->setContent($strResultado);
        return $objRespuesta;        
    }

    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * boolGuardarDocumento
     *
     * Método encargado de guardar la información  relacionada al nuevo documento
     *          
     * @return boolean
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 01-09-2020 
     * 
     */

    public function boolGuardarDocumento($arrayParametros)
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
        {
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }                   

            return false;
        }
    }
    
    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * boolProcesarArchivo
     *
     * Método encargado de leer y guardar la información  relacionada al nuevo documento.
     *          
     * @return boolean
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 01-09-2020 
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 01-12-2020 Se corrige formato de fecha en lectura de columna fecha de fecha de archivo excel.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 30-12-2020 Se agrega validación que verifica si número de referencia viene en formato de string.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 20-01-2021 Se agrega validación para dar nuevo formato a fecha de proceso en estados de cuenta con formatos de fecha
     *                         no válidos para generación de pagos.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 29-01-2021 Se agrega validación que verifica formato de número de referencia cuando el mismo viene nulo desde el estado de cuenta.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 01-02-2021 Se modifica validación que verifica número de referencia cuando el mismo viene nulo desde el estado de cuenta.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.6 29-09-2021 Se parametriza el formato de fecha y se formatea la fecha-excel
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.7 30-06-2022 Se modifica seteo de parámetros para ruta de lectura y ruta destino de archivo.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.8 11-07-2022 Se agrega lectura de columna concepto. Se agrega validación conrespecto a número de referencia y valor.
     *
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.1 05-09-2020 Se elimina validaciones de duplicidad para banco machala
     * 
     */ 
    public function boolProcesarArchivo($arrayParametros)
    {
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");

        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strAbsolutePath       = $arrayParametros['strDestino'];
        $strReadPath           = $arrayParametros['strReadPath'];
        $objAdmiFormatoPagoAut = $arrayParametros['objAdmiFormatoPagoAut'];
        $objAdmiCuentaContable = $arrayParametros['objAdmiCuentaContable'];
        $intNumDetallesValidos = 0;
        $strFormatoFecha       = $objAdmiFormatoPagoAut->getFormatoFecha();

        $emFinanciero->getConnection()->beginTransaction();

        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION PAGOS', 
                                                           'estado'          => 'Activo'));
        try
        {
            if(is_object($objAdmiFormatoPagoAut) && isset($strAbsolutePath))
            {
                $strColValidaTipoFormato = $objAdmiFormatoPagoAut->getColValidaTipo();
                $arrayValidaTipoFormato  = explode('|',$strColValidaTipoFormato);
                $objReader               = PHPExcel_IOFactory::createReaderForFile($strReadPath);
                $objReader->setReadDataOnly(true);
                $objXLS = $objReader->load($strReadPath);
                $intIdFormatoPagAut= $objAdmiFormatoPagoAut->getId();
                $strConcepto = '';
                $objConceptoCaract = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                       ->findOneBy( array('estado'                    => 'Activo', 
                                                                          'descripcionCaracteristica' => 'CONCEPTO') ); 
                if(is_object($objConceptoCaract))
                { 
                    $objConceptoFormatoCaract = $emFinanciero->getRepository("schemaBundle:AdmiFormatoPagoAutCaract")
                                                             ->findOneBy( array('estado'                  => 'Activo', 
                                                                                'caracteristicaId'        => $objConceptoCaract->getId(),
                                                                                'formatoPagoAutomaticoId' => $intIdFormatoPagAut));
                    if(is_object($objConceptoFormatoCaract))
                    {
                        $strPosColConcepto =$objConceptoFormatoCaract->getValor();
                    }
                }
                $objInfoPagoAutomaticoCab = new InfoPagoAutomaticoCab();
                $objInfoPagoAutomaticoCab->setCuentaContableId($objAdmiCuentaContable->getId());
                $objInfoPagoAutomaticoCab->setBancoTipoCuentaId($objAdmiFormatoPagoAut->getBancoTipoCuentaId());
                $objInfoPagoAutomaticoCab->setRutaArchivo($strAbsolutePath);
                $objInfoPagoAutomaticoCab->setNombreArchivo($arrayParametros['strNombreDocumento']);
                $objInfoPagoAutomaticoCab->setEstado("Pendiente");
                $objInfoPagoAutomaticoCab->setFeCreacion(new \DateTime('now'));
                $objInfoPagoAutomaticoCab->setIpCreacion($arrayParametros['strClientIp']);
                $objInfoPagoAutomaticoCab->setUsrCreacion($arrayParametros['strUser']);
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();
                
                for ($intNumhoja = 0; $intNumhoja < $objXLS->getSheetCount(); $intNumhoja++)
                {
                    $objWorksheet = $objXLS->getSheet($intNumhoja);

                    $strTitle = trim(strtoupper($objWorksheet->getTitle()));
                    

                    if (isset($strTitle))
                    {
                        $objWorksheet->setRightToLeft(false); 
                        $highestRow = $objWorksheet->getHighestRow();
                        $highestColumn = $objWorksheet->getHighestColumn();
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                        
                        for ($i = intval($objAdmiFormatoPagoAut->getFilaInicia()); $i <= intval($highestRow); $i++)
                        {
                            $strColTipo          = trim($objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColTipo(),$i)->getValue());
                            
                            if(in_array($strColTipo,$arrayValidaTipoFormato) || in_array(explode(' ',$strColTipo)[0],$arrayValidaTipoFormato) )
                            {

                                $strValor            = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColMonto(),$i)->getValue();

                                if(strpos($strValor,'$')!== false)
                                {
                                    $strValor = str_replace('$','',$strValor);
                                }
                                //vaidacion para realizar el cambio de decimales de Banco Amazonas
                                $objAdmiParametroDetDBA = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                                    'descripcion' => 'DECIMALES_BANCO_AMAZONAS',
                                                                                    'valor1'      => $objAdmiFormatoPagoAut->getBancoTipoCuentaId(),
                                                                                    'estado'      => 'Activo'));
                                if(strpos($strValor,',')!== false && is_object($objAdmiParametroDetDBA))
                                {
                                    $strValor = str_replace('.','',$strValor);
                                    $strValor = str_replace(',','.',$strValor);
                                }
                                else
                                {
                                    $strValor = str_replace(',','',$strValor);

                                }                           
                                if(is_string($objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColReferencia(),$i)->getValue()))
                                {
                                    $strReferencia = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColReferencia(),$i)->getValue();
                                    $strReferencia = trim(preg_replace('/[a-zA-Z]/', '', $strReferencia));
                                }
                                else
                                {                                   
                                    $strReferencia       = strval(intval($objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut
                                        ->getColReferencia(),$i)->getCalculatedValue()));                                   
                                }
                               
                                $objInfoPagoAutomaticoDet = new InfoPagoAutomaticoDet();
                                $objInfoPagoAutomaticoDet->setPagoAutomaticoId($objInfoPagoAutomaticoCab->getId());
                                $objInfoPagoAutomaticoDet->setFormaPagoId(5);
                                if(isset($strReferencia) && $strReferencia!=='' && $strReferencia!==0 && $strReferencia!=='0')
                                {

                                    if(strpos($strReferencia, '#') !== false)
                                    {
                                    
                                        $arrayReferencia  =  explode('#', $strReferencia);
                                        $strReferencia    = $arrayReferencia[1];

                                    }                                    
                                    if(strpos($strReferencia, '\'') !== false)
                                    {
                                        $strReferencia = substr($strReferencia, 9);

                                    }
                                    if($strColTipo==='TW'||$strColTipo==='DP'||$strColTipo==='MC')
                                    {
                                        $strReferencia = trim(preg_replace('/-/', '', $strReferencia));
                                    }
                                    else
                                    {
                                        if(strpos($strReferencia, '-') !== false)
                                        {
                                            $arrayReferencia  =  explode('-', $strReferencia);
                                            $strReferencia    = $arrayReferencia[0].$arrayReferencia[1];
                                        }
                                        else
                                        {
                                            $arrayReferencia  =  explode(' ', $strReferencia);
                                            if(count($arrayReferencia)>0)
                                            {
                                                $strReferencia    = $arrayReferencia[0];
                                            }
                                        }
                                    }
                                    $strReferencia = preg_replace('([^A-Za-z0-9])', '', $strReferencia);
                                    
                                    $objInfoPagoAutomaticoDet->setNumeroReferencia($strReferencia);
                                }
                                else
                                {
                                    $strRefFechaActual= strval(date_format(new \DateTime('now'), "dmYGis"));
                                    $objInfoPagoAutomaticoDet->setNumeroReferencia($strRefFechaActual);
                                }
                                if(in_array($strColTipo,$arrayValidaTipoFormato) || in_array(explode(' ',$strColTipo)[0],$arrayValidaTipoFormato) )
                                {
                                    if(strpos($strColTipo, ' ') !== false && (explode(' ',$strColTipo)[0]==='N/C'))
                                    {
                                    
                                        $strColTipo = explode(' ',$strColTipo)[0];
                                    }                                    
                                    $objInfoPagoAutomaticoDet->setObservacion($strColTipo);
                                }
                                else
                                {
                                    $strColTipo = explode(' ',$strColTipo)[0];
                                    $objInfoPagoAutomaticoDet->setObservacion($strColTipo);
                                }
                                $objInfoPagoAutomaticoDet->setMonto(floatval($strValor));
                                $objInfoPagoAutomaticoDet->setEstado("Pendiente");
                                $objInfoPagoAutomaticoDet->setFeCreacion(new \DateTime('now'));
                                $objInfoPagoAutomaticoDet->setIpCreacion($arrayParametros['strClientIp']);
                                $objInfoPagoAutomaticoDet->setUsrCreacion($arrayParametros['strUser']);
                                
                                $strFechaTransaccion = $objWorksheet->getCellByColumnAndRow($objAdmiFormatoPagoAut->getColFecha(),$i)->getValue();

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
                                if(is_numeric($strFechaTransaccion))
                                {   
                                    $strFechaTransaccion=floor($strFechaTransaccion);
                                    //Cambio de Fecha Banco del austro cuando fecha es un estring                                 
                                    if(strlen($strFechaTransaccion)===8)
                                    {
                                        if(strpos($strFechaTransaccion,'/') <= 0 || strpos($strFechaTransaccion,'-') <= 0)
                                        {
                                            $strAnioTemp=substr($strFechaTransaccion,0,4);
                                            $strMesTemp=substr($strFechaTransaccion,4,2);
                                            $strDiaTemp=substr($strFechaTransaccion,6,2);
                                            
                                            $arrayFeTransaccion[2]=$strDiaTemp;
                                            $arrayFeTransaccion[1]=$strMesTemp;
                                            $arrayFeTransaccion[0]=$strAnioTemp;
                                        }
                                        $strFechaTransaccion=$arrayFeTransaccion[0]."/".$arrayFeTransaccion[1]."/".$arrayFeTransaccion[2];
                                    }
                                    
                                }                                
                                $strFecha            = PHPExcel_Shared_Date::ExcelToPHP($strFechaTransaccion);
                                $strFecha            = strtotime("+1 day",$strFecha);
                                $strFecha            = date("Y-m-d", $strFecha);
                                                              
                                if(count($arrayFeTransaccion)==3)
                                {
                                    if(strcmp($strFormatoFecha,'dd/mm/aaaa')==0 || strcmp($strFormatoFecha,'dd-mm-aaaa')==0)
                                    {
                                        $strFechaTransaccion = $arrayFeTransaccion[1].'/'.$arrayFeTransaccion[0].'/'.$arrayFeTransaccion[2];
                                    }
                                    try
                                    {
                                        $objFecha = new \DateTime($strFechaTransaccion);
                                    }
                                    catch(\Exception $e)
                                    {
                                        $strFechaTransaccion = $arrayFeTransaccion[0].'-'.$arrayFeTransaccion[1].'-'.$arrayFeTransaccion[2];
                                    }                                    
                                    $objInfoPagoAutomaticoDet->setFecha($strFechaTransaccion);
                                }
                                else
                                {
                                    $arrayValidaFechaFormato  = explode('-',$strFecha);
                                    if($arrayValidaFechaFormato)
                                    {
                                        $strFechaTransaccion = $strFecha;
                                        $objInfoPagoAutomaticoDet->setFecha($strFecha);
                                    }
                                    else 
                                    {
                                        $objInfoPagoAutomaticoDet->setFecha($strFechaTransaccion);
                                                                            
                                    }
                                }
                               // validacion Exclucion de Bancos 
                                $intBancoTipoCuentaId = $objInfoPagoAutomaticoCab->getBancoTipoCuentaId(); 
                                if(is_object($objAdmiParametroCab))
                                {              
                                    $objAdmiParametroDetEB = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                                    'descripcion' => 'EXCLUCION BANCOS',
                                                                                    'valor1'      => $intBancoTipoCuentaId,
                                                                                    'estado'      => 'Activo'));
                                }

                                if(is_object($objAdmiParametroDetEB))
                                {
                                    if($i===$objAdmiFormatoPagoAut->getFilaInicia())
                                    {
                                        $objInfoPagoAutDetExistente  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                                            ->findOneBy(array("numeroReferencia" => $strReferencia,
                                                                                                        "estado" => array('Pendiente', 'Procesado'),
                                                                                                        "fecha"  => $strFechaTransaccion,
                                                                                                        "monto"  => $strValor));
                                    }
                                }
                                else
                                {
                                        $objInfoPagoAutDetExistente  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                                    ->findOneBy(array("numeroReferencia" => $strReferencia,
                                                                                                        "estado" => array('Pendiente', 'Procesado'),
                                                                                                        "fecha"  => $strFechaTransaccion,
                                                                                                        "monto"  => $strValor));                
                                }    
                                                                                            

                                if(is_object($objInfoPagoAutDetExistente))
                                {
                                    throw new \Exception('ErrorD');
                                }                                
                                $emFinanciero->persist($objInfoPagoAutomaticoDet);
                                $emFinanciero->flush();

                                if(isset($strPosColConcepto) && $strPosColConcepto!=='')
                                {
                                    $strValColConcepto = $objWorksheet->getCellByColumnAndRow($strPosColConcepto,$i)->getValue();
                                    if(isset($strValColConcepto))
                                    {
                                        $strConcepto = $objWorksheet->getCellByColumnAndRow($strPosColConcepto,$i)->getValue();
                                        if ($strConcepto==="")
                                        {
                                            $strConcepto = 'N/A';
                                        }
                                    }
                                    else
                                    {
                                        $strConcepto = 'N/A';
                                    }
                                    
                                    $objInfoPagoAutomaticoCaract = new InfoPagoAutomaticoCaract();
                                    $objInfoPagoAutomaticoCaract->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet->getId());
                                    $objInfoPagoAutomaticoCaract->setCaracteristicaId($objConceptoCaract->getId());
                                    $objInfoPagoAutomaticoCaract->setEstado('Activo');
                                    $objInfoPagoAutomaticoCaract->setValor($strConcepto);
                                    $objInfoPagoAutomaticoCaract->setIpCreacion($arrayParametros['strClientIp']);
                                    $objInfoPagoAutomaticoCaract->setFeCreacion(new \DateTime('now'));
                                    $objInfoPagoAutomaticoCaract->setUsrCreacion($arrayParametros['strUser']);
                                    $emFinanciero->persist($objInfoPagoAutomaticoCaract);
                                    $emFinanciero->flush();                                    
                                }                                
                                //Graba historial de detalle de estado de cuenta.
                                $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                                $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                                $objInfoPagoAutomaticoHist->setEstado('Pendiente');
                                $objInfoPagoAutomaticoHist->setObservacion('Se crea detalle Pendiente.');
                                $objInfoPagoAutomaticoHist->setIpCreacion($arrayParametros['strClientIp']);
                                $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                                $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUser']);
                                $emFinanciero->persist($objInfoPagoAutomaticoHist);
                                $emFinanciero->flush();
                                $intNumDetallesValidos++;
                            }
                        }
                    }                
                }
                if($intNumDetallesValidos === 0)
                {
                    throw new \Exception('Formato de estado de cuenta incorrecto. Favor Verificar .'); 
                }                
                $emFinanciero->getConnection()->commit();
                $emFinanciero->getConnection()->close();                
            }
            
        } catch (Exception $ex) 
        {
            if($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
                $emFinanciero->getConnection()->close(); 
            }             
           
        }        
    }        
     
    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * verEstadoCuentaAction()
     * Función que renderiza la página de Ver detalle de un pago automático.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-09-2020 
     * @since 1.0
     * 
     * @param int $intIdPagoAutomatico => id del pago automático
     * 
     * @return render - Página de Ver Estado de Cuenta.
     */
    public function verEstadoCuentaAction($intIdPagoAutomatico)
    {
        $emFinanciero              = $this->getDoctrine()->getManager("telconet_financiero");
        $strDescripcionBcoCta      = "";
        
        $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                  ->find($intIdPagoAutomatico); 
        
        if(is_object($objInfoPagoAutomaticoCab))
        {
            $objAdmiCuentaContable  = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                                   ->find($objInfoPagoAutomaticoCab->getCuentaContableId());
            if(is_object($objAdmiCuentaContable))
            {
                $strDescripcionBcoCta = $objAdmiCuentaContable->getDescripcion()."-".$objAdmiCuentaContable->getNoCta();
            }
            
        }


        return $this->render('financieroBundle:infoPagoAutomaticoCab:verEstadoCuenta.html.twig',array('intIdPagoAutomatico'  => $intIdPagoAutomatico,
                                                                                                      'strDescripcionBcoCta' => $strDescripcionBcoCta));
    }
    
    /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * eliminarEstadoCuentaAction()
     * Función que cambia a estado Eliminado un estado de cuenta.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 13-10-2020 
     * @since 1.0
     * 
     * @param int $intIdPagoAutomatico => id del pago automático
     * 
     * @return render - Página de Ver Estado de Cuenta.
     */
    public function eliminarEstadoCuentaAction()
    {
        $objRequest         = $this->getRequest();
        $strObservacion     = $objRequest->get("observacionEliminar"); 
        $intIdPagAutomatico = intval($objRequest->get("idPagoAutomatico"));         
        $emFinanciero       = $this->getDoctrine()->getManager("telconet_financiero");

        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                      ->find($intIdPagAutomatico);
            
            $objInfoPagoAutomaticoDet     = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                         ->findOneBy(array( "pagoAutomaticoId" => $intIdPagAutomatico,
                                                                            "estado"           => "Procesado"));            

            if(is_object($objInfoPagoAutomaticoCab) && !(is_object($objInfoPagoAutomaticoDet)))
            {                                 
                $objInfoPagoAutomaticoCab->setEstado('Eliminado');
                $objInfoPagoAutomaticoCab->setObservacion($strObservacion);
                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                $emFinanciero->flush();
                
                $strResponse = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                            ->setEstadoPagAutDetByPagAutId($intIdPagAutomatico, "Eliminado");                
            }
            else 
            {
                $strResponse = "Error";
            }
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();             
            
        }
        catch(\Exception $e)
        {
            error_log('Error al eliminar estado de cuenta '.$e->getMessage());
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $strResponse = "Error";           
        }
        return new Response($strResponse);
    }    


   /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * gridDetalleAction()
     * Función que obtiene un listado de cabecesras de estado de cuenta de la tabla INFO PAGO AUTOMATICO DET.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 19-02-2021 Se agrega formato para correcta visualización a dos decimales de monto.
     * @since 1.0
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 22-04-2022 Se agrega lectura de columna concepto.
     * 
     * @return $objResponse 
     */

    public function gridDetalleAction()
    {
        $objRequest           = $this->getRequest();
        $arrayEstado          = $objRequest->get("strEstado");        
        $intIdPagoAutomatico  = $objRequest->get("intIdPagoAutomatico");
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emFinanciero         = $this->getDoctrine()->getManager("telconet_financiero");
        
        if(isset($arrayEstado[0]))
        {
            $arrayInfoPagoAutomaticoDet  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                           ->findBy(array( 'pagoAutomaticoId' => $intIdPagoAutomatico,'estado' => $arrayEstado[0]));           
        }
        else 
        {
            $arrayInfoPagoAutomaticoDet  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                            ->findBy(array( 'pagoAutomaticoId'      => $intIdPagoAutomatico),
                                                     array( 'id'                    => 'DESC'));
        }

        
        $intTotal         = count($arrayInfoPagoAutomaticoDet);
        
        $arrayDetalles = array();
        
        $intIndex      = 0;
        
        foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet):
            $strConcepto = '';
            $objConceptoCaract = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                   ->findOneBy( array('estado'                    => 'Activo', 
                                                                      'descripcionCaracteristica' => 'CONCEPTO') ); 
            if(is_object($objConceptoCaract))
            { 
                $objInfoConceptoCaract = $emFinanciero->getRepository("schemaBundle:InfoPagoAutomaticoCaract")
                                                      ->findOneBy(['estado'                  => 'Activo',
                                                                   'detallePagoAutomaticoId' => $objInfoPagoAutomaticoDet->getId(),
                                                                   'caracteristicaId'        => $objConceptoCaract->getId()
                                                                  ]);
                if(is_object($objInfoConceptoCaract))
                {
                    $strConcepto = $objInfoConceptoCaract->getValor();
                }
            }
            
            $arrayDetalles[] = array('intIdPagoAutDet'        => $objInfoPagoAutomaticoDet->getId(),
                                     'intIndex'               => $intIndex,
                                     'strFecha'               => $objInfoPagoAutomaticoDet->getFecha(),
                                     'strTipo'                => $objInfoPagoAutomaticoDet->getObservacion(),
                                     'strReferencia'          => $objInfoPagoAutomaticoDet->getNumeroReferencia(),
                                     'strMonto'               => number_format(floatval($objInfoPagoAutomaticoDet->getMonto()), 2, '.', ''),
                                     'strEstado'              => $objInfoPagoAutomaticoDet->getEstado(),
                                     'strConcepto'            => $strConcepto,
                                     'strFeCreacion'          => strval(date_format($objInfoPagoAutomaticoDet->getFeCreacion(), "Y-m-d")),                
                                     'strUsrCreacion'         => $objInfoPagoAutomaticoDet->getUsrCreacion(),
                                     'strValor'               => '<td><div style="width:120px;" class="overflowX"><input type="text"  '
                . '                                                        class="valor_factura form-control" id="valorPago_'
                                                                           .$intIndex.'" value="" /></div></td> ',
                                     'strAcciones'            => array('strEstado'       => $objInfoPagoAutomaticoDet->getEstado(),
                                                                       'intIndex'        => $intIndex,
                                                                       'strReferencia'   => $objInfoPagoAutomaticoDet->getNumeroReferencia(),
                                                                       'intIdPagoAutDet' => $objInfoPagoAutomaticoDet->getId(),
                                                                       'strEsNotificado' => $objInfoPagoAutomaticoDet->getEsNotificado()
                                                                       )                

                                    );
            $intIndex++;
        endforeach;

        if(empty($arrayDetalles))
        {

            $arrayDetalles[] = array('intIdPagoAutDet'        => '',
                                     'intIndex'               => '',
                                     'strFecha'               => '',
                                     'strTipo'                => '',
                                     'strReferencia'          => '',
                                     'strMonto'               => '',
                                     'strTotal'               => '',              
                                     'strCliente'             => '',
                                     'strLogin'               => '',
                                     'strFactura'             => '',
                                     'strSaldo'               => '',
                                     'strFormaPago'           => '',
                                     'strEstado'              => '',
                                     'strFeCreacion'          => '',                
                                     'strUsrCreacion'         => '',
                                     'strAcciones'            => array('strEstado'  => '')  
                                   );
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayDetalles)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }



     /**
     * getFormasPagoAutomaticoAction, obtiene la información de las formas de pago a utilizarse en pagos automáticos .
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 08-09-2020          
     *                    
     * @return Response lista de Formas de Pago.
     */
        
    public function getFormasPagoAutomaticoAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strFechaTrans          = $objRequest->get('fechaTransaccionDet');
        $intIdPagoAutomaticoDet = $objRequest->get('idPagoAutomaticoDet');       
        $strEmpresaCod          = $objSession->get('idEmpresa');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero           = $this->getDoctrine()->getManager("telconet_financiero");        
        $strTipoFormaPago       = '';
        $strVisiblePago         = '';
        $strEsParaContrato      = '';
        $arrayFormasPago        = [];
        $boolMesesAnteriores    = false;
        $arrayFormasPago[]      = array('id' => 0,'descripcion' => "Seleccione..");        
        $objDateMesActual       = date("m");
        $objDateAnioActual      = date("Y");
        $objInfoPagoAutomaticoDet  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                  ->find($intIdPagoAutomaticoDet); 
        if(is_object($objInfoPagoAutomaticoDet))
        {    
            $strFechaTrans          = $objInfoPagoAutomaticoDet->getFecha();
            $objDatetimeFechaTrans  = strtotime($strFechaTrans);
            $objDateMesTran         = date("m", $objDatetimeFechaTrans);
            $objDateAnioTran        = date("Y", $objDatetimeFechaTrans);
        }
        
        if($objDateAnioTran < $objDateAnioActual || ($objDateAnioTran === $objDateAnioActual  && $objDateMesTran < $objDateMesActual))
        {
            $boolMesesAnteriores = true;
        }
        
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION PAGOS', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {
            if($boolMesesAnteriores)
            {
                $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'FORMA PAGO',
                                                               'valor4'      => 'MESES_ANTERIORES',
                                                               'empresaCod'  => $strEmpresaCod,
                                                               'estado'      => 'Activo'));
            }
            else
            {
                $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'FORMA PAGO',
                                                               'valor4'      => null,
                                                               'empresaCod'  => $strEmpresaCod,
                                                               'estado'      => 'Activo'));
            }
            foreach($arrayParametrosDet as $objAdmiParametroDet)
            {
                $strDescFormaPago  = $objAdmiParametroDet->getValor1();
                $intIdFormaPago    = $objAdmiParametroDet->getValor2();            
                $arrayFormasPago[] = array('id'          => intval($intIdFormaPago), 
                                           'descripcion' => $strDescFormaPago);
            }             
        }
        
        $objResponse = new Response(json_encode(array('formas_pago' => $arrayFormasPago)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
    * getClientesAction, función que consulta los clientes según valores enviados como parámetro.
    * 
    * @author Adrián Limones <alimones@telconet.ec>
    * @version 1.0 08-09-2020          
    *                    
    * @return Response lista de clientes.
    */    
    public function getClientesAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strEmpresaCod  = $objSession->get('idEmpresa');        
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $strSearch      = $objRequest->get('searchTerm');
        $arrayParametros                    = [];
        $arrayParametros['strEstado']       = 'Activo';
        $arrayParametros['strRol']          = 'Cliente';
        $arrayParametros['strEmpresaCod']   = $strEmpresaCod;
       
        if(!isset($strSearch))
        {
            $arraylistaClientes = [];

        }
        else
        {
            $arrayParametros['strSearch']   = $strSearch;

            $arrayResultado  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getClienteParametros($arrayParametros);

            $arrayRegistros     = $arrayResultado['objRegistros'];
            $arraylistaClientes = [];

            foreach($arrayRegistros as $arrayRegistro)
            {          
                $arraylistaClientes[] = array('id'      => $arrayRegistro['intIdPersonaRol'], 
                                              'nombres' => $arrayRegistro['strNombres']);
            }             
            

        }
        $objResponse = new Response(json_encode($arraylistaClientes));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * getPuntosAction()
     * Función que carga todos los puntos en un combo de puntos.
     * 
     * @author Adrian Limones <alimonesr@telconet.ec>
     * @version 1.0 19-08-2020
     * @since 1.0
     * 
     * @return objResponse
     */
    public function getLoginesClienteAction()
    {
       $objRequest     = $this->getRequest();
       $objSession     = $objRequest->getSession();   
       $intIdCliente   = $objRequest->get('idPersona');
       $strEmpresaCod  = $objSession->get('idEmpresa');        
       $emComercial    = $this->getDoctrine()->getManager("telconet");       
       $arrayParametros                = array(); 
       $arrayParametros ['strEstado']  = array('Activo');
       $arrayRegistros  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                      ->findPtosPadreByEmpresaPorCliente($strEmpresaCod,$intIdCliente);        
        $arrayLogines   = [];
        
        foreach($arrayRegistros as $arrayRegistro)
        {           
            $arrayLogines[] = array('id'    => $arrayRegistro['id'], 
                                    'login' => $arrayRegistro['login']);
        } 
        $objResponse = new Response(json_encode(array('puntos' => $arrayLogines)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }   
    
    /**
     * getFacturasPendientesPtoAction()
     * Función que carga las facturas pendientes de un punto enviado como parámetro.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 17-09-2020
     * @since 1.0
     * 
     * @return objResponse
     */
    public function getFacturasPendientesPtoAction()
    {
        $objRequest     = $this->getRequest();  
        $intIdPtoSelect = $objRequest->get('idPunto');
        $emFinanciero   = $this->getDoctrine()->getManager("telconet_financiero");

        $arrayFacturas     = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->findFacturasPendientesxPunto($intIdPtoSelect);
        
        $arrayDatosFactura = [];
        
        if(count($arrayFacturas) > 0)
        {
            foreach($arrayFacturas as $objFactura)
            {
                $strNumeroFacturaSri = $objFactura->getNumeroFacturaSri();
                if(isset($strNumeroFacturaSri))
                {
                    $arrayDatosFactura[] = array('id'               => $objFactura->getId(), 
                                                 'numeroFacturaSri' => $strNumeroFacturaSri);
                }
                else
                {
                    $arrayDatosFactura[] = array('id'               => $objFactura->getId(), 
                                                 'numeroFacturaSri' => $objFactura->getNumFactMigracion());
                }                
            }
        }
        $objResponse = new Response(json_encode(array('facturas' => $arrayDatosFactura)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }
    
    /**
     * getSaldoFacturaAction()
     * Función que permite obtener el saldo de una factura.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 17-09-2020
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 19-02-2021 Se agrega formato para correcta visualización a dos decimales.
     * @since 1.0
     * 
     * @return objResponse
     */  
    public function getSaldoFacturaAction()
    {
       $objRequest     = $this->getRequest();
       $intIdDocumento = $objRequest->get('idFactura');        
       $emFinanciero   = $this->getDoctrine()->getManager("telconet_financiero");
       
       $objInfoDocumentoFinCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);
       
       if(is_object($objInfoDocumentoFinCab))
       {
           $floatSaldoFactura = $objInfoDocumentoFinCab->getValorTotal();
           
           $arrayParametros   = array('intIdDocumento' => $intIdDocumento, 'intReferenciaId' => '');

           $arrayGetSaldoXFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->getSaldosXFactura($arrayParametros);
            
           if(!empty($arrayGetSaldoXFactura['strMessageError']))
           {
               throw new Exception('Error al obtener el saldo de factura: '. $objInfoDocumentoFinCab->getNumeroFacturaSri());
           }
           else
           {
               $floatSaldoFactura = number_format(floatval($arrayGetSaldoXFactura['intSaldo']), 2, '.', '');
           }
       }
       $objResponse = new Response(json_encode(array('saldoFactura' => $floatSaldoFactura)));
       $objResponse->headers->set('Content-type', 'text/json');
       return $objResponse;
    }    
    

   /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * procesarPagoAction()
     * Función que realiza el procesamiento y generación de un pago mediante estado de cta.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-10-2020
     * @since 1.0
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 05-01-2021 Corrección en manejo de transacciones.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 19-02-2021 Se modifica ubicación de llamada a función para reactivar servicios.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 13-12-2021 Se agrega funcionalidad para obtener cuenta contable asociada a formas de pago 'MESES_ANTERIORES'.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 27-05-2022 Se agrega funcionalidad para generar y procesar el depósito respectivo (Si el pago tiene forma de pago depositable). 
     * 
     * @return $objResponse 
     * 
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.5 24-11-2022 Se cambia el proceso de procesar pagos, primero se procesan todos los pagos y despues se procesan los correos. 
     * 
     * @return $objResponse 
     *  
     */

    public function procesarPagoAction()
    {
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        
        $intEmpresaId         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa');         
        $strUsuarioCreacion   = $objSession->get('user');        
        $intIdPagoAutomatico  = $objRequest->get("intIdPagoAutomaticoCab");
        $intIdPagoAutDet      = $objRequest->get('intIdPagoAutDet');        
        $intIdCliente         = $objRequest->get('intIdCliente');
        $intIdFormaPago       = $objRequest->get('intIdFormaPago');
        $arrayDetallesEstCta  = $objRequest->get('arrayDetalles');
        $serviceUtil          = $this->get('schema.Util');
        $emFinanciero                  = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral                     = $this->getDoctrine()->getManager('telconet_general');
        $emComercial                   = $this->getDoctrine()->getManager();
        $serviceInfoPago               = $this->get('financiero.InfoPago'); 
        $serviceInfoPagoDet            = $this->get('financiero.InfoPagoDet');
        $serviceProcesoMasivo          = $this->get('tecnico.ProcesoMasivo');
        $serviceInfoPagAut             = $this->get('financiero.InfoPagoAutomatico');
        $serviceInfoPagoLinea          = $this->get('financiero.InfoPagoLinea');
        $arrPagosDetIdContabilidad     = array();
        $arrayParametroDet             = array();
        $strMsnErrorContabilidad       = '';
        $boolFormaPagoDepositable      = false;
        $arrayPtosNotifica             = [];
        $arrayParamsNotificacion       = array();
        
        $objInfoPersonaEmpRol      = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->find($intIdCliente);
        if(is_object($objInfoPersonaEmpRol))
        {
            $intOficinaId = $objInfoPersonaEmpRol->getOficinaId()->getId();
        }

        
        $arrayPagos             = array();
        $ArrayPtosAdd           = array();
        $arrayIdsPagosDepositar = array();
        foreach($arrayDetallesEstCta as $strDetalleEstCtaArray)
        {
            $arrayDetallesPago  = array();           
            $arrayDetalleEstCta = explode("|", $strDetalleEstCtaArray);
            $intIdPto           = intval($arrayDetalleEstCta[3]);

            foreach($arrayDetallesEstCta as $strArrayDetSearch)
            {
                $arrayDetSearch  = array();
                $arrayDetSearch = explode("|", $strArrayDetSearch);
                if ($intIdPto == $arrayDetSearch[3] && !in_array(intval($arrayDetSearch[3]),$ArrayPtosAdd))
                {
                    array_push($arrayDetallesPago,$arrayDetSearch);
                }
            }

            if((empty($arrayPagos))||
               (!empty($ArrayPtosAdd) && 
               count($arrayDetallesPago)>0 && 
               !in_array(intval($arrayDetallesPago[0][3]),$ArrayPtosAdd)))
            {
                array_push($arrayPagos,$arrayDetallesPago);

            }
            $ArrayPtosAdd[] = intval($intIdPto);
            
        }
        
        foreach($arrayPagos as $ArrayDetallesPago)
        {
            $boolCreaPago = false;
            $emFinanciero->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();

            try
            {                
                $valorCabeceraPago  = 0;
                
                $intIdPunto = intval($ArrayDetallesPago[0][3]);
                //CABECERA DEL PAGO-->>*************//
                //**********************************// 
                $entityInfoPagoCab    = new InfoPagoCab();
                $entityInfoPagoCab->setEmpresaId($intEmpresaId);
                $entityInfoPagoCab->setEstadoPago('Cerrado');
                $entityInfoPagoCab->setFeCreacion(new \DateTime('now'));
                //Obtener la numeracion de la tabla Admi_numeracion
                $objDatosNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                  ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "PAG");
                $strSecuenciaAsig = str_pad($objDatosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                $strNumeroPago    = $objDatosNumeracion->getNumeracionUno() . "-" .$objDatosNumeracion->getNumeracionDos() . "-" . $strSecuenciaAsig;

                //Actualizo la numeracion en la tabla
                $numero_act = ($objDatosNumeracion->getSecuencia() + 1);
                $objDatosNumeracion->setSecuencia($numero_act);
                $emComercial->persist($objDatosNumeracion);
                $emComercial->flush();

                $arrayPtosNotifica[]     = $intIdPunto;
                $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento('PAG');
                $entityInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityInfoPagoCab->setNumeroPago($strNumeroPago);
                $entityInfoPagoCab->setOficinaId($intOficinaId);
                $entityInfoPagoCab->setPuntoId($intIdPunto);
                $entityInfoPagoCab->setUsrCreacion($strUsuarioCreacion);
                $entityInfoPagoCab->setValorTotal($valorCabeceraPago);
                $entityInfoPagoCab->setDetallePagoAutomaticoId($intIdPagoAutDet);
                $emFinanciero->persist($entityInfoPagoCab);
                $emFinanciero->flush();

                //DETALLES DEL PAGO-->>*************//
                //**********************************//
                $arrayAnticipo = array();

                $objInfoPagoAutomaticoCab  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                          ->find($intIdPagoAutomatico);
                
                
                $objInfoPagoAutomaticoDet  = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->find($intIdPagoAutDet);
                
                if(is_object($objInfoPagoAutomaticoCab) && is_object($objInfoPagoAutomaticoDet))
                {
                    $floatValorPago = 0;
                    
                    $strFechaDeposito = $objInfoPagoAutomaticoDet->getFecha();                    

                    foreach($ArrayDetallesPago as $ArrayDetallePago)
                    {   
                        
                        $intIdFactura            = intval($ArrayDetallePago[4]);
                        
                        $intIdFormaPago          = intval($ArrayDetallePago[5]);                        
                        
                        $strReferencia           = $ArrayDetallePago[6];
                        
                        $floatValorDetPago       = floatval($ArrayDetallePago[7]);

                        $floatSaldoFactura       = floatval($ArrayDetallePago[8]);
                        
                        if($floatValorDetPago === $floatSaldoFactura || $floatValorDetPago > $floatSaldoFactura)
                        {
                            $floatValorPago          += $floatSaldoFactura;
                        }
                        else
                        {
                            $floatValorPago          += $floatValorDetPago;
                        }                        
                        $intIdCtaContable        = $objInfoPagoAutomaticoCab->getCuentaContableId();

                        $objAdmiCtaContable      = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')->find($intIdCtaContable);

                        $strDescCuentaContable   = $objAdmiCtaContable->getDescripcion().' '.$objAdmiCtaContable->getNoCta();

                        $objAdmiFormaPago        = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
                        
                        if($objAdmiFormaPago->getEsDepositable()==='S')
                        {
                            $boolFormaPagoDepositable  = true;
                        }

                        $strDescFormaPago        = $objAdmiFormaPago->getDescripcionFormaPago();

                        $strTipoFormaPago        = $objAdmiFormaPago->getTipoFormaPago();

                        if(isset($intIdFormaPago))
                        {
                            $strNombreParametro = 'FORMA_PAGO_MES_ANTERIOR';
                            $strModulo          = 'FINANCIERO';
                            $strProceso         = 'CUENTAS_CONTABLE';
                            $strDescripcion     = 'FORMA PAGO';
                            $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne($strNombreParametro,
                                                                             $strModulo,
                                                                             $strProceso,
                                                                             $strDescripcion,
                                                                             $intIdFormaPago,
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             $intEmpresaId);

                            if($arrayAdmiParametroDet && $arrayAdmiParametroDet['valor2'] == 'MESES_ANTERIORES' && 
                                is_object($objAdmiFormaPago) && is_object($objAdmiCtaContable))
                            {
                                $strNoCta = $objAdmiCtaContable->getNoCta();
                                $objAdmiTipoCtaContMesAnt = $emFinanciero->getRepository('schemaBundle:AdmiTipoCuentaContable')
                                                                         ->findOneBy(array("descripcion"=>'MESES_ANTERIORES'));
                                if(is_object($objAdmiTipoCtaContMesAnt))
                                {
                                    $intTipoCuentaContableId   = $objAdmiTipoCtaContMesAnt->getId();
                                    $objAdmiCtaContableMesAnt  = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                                                              ->findOneBy(array("noCta"               =>$strNoCta,
                                                                                                "tipoCuentaContableId"=>$intTipoCuentaContableId));
                                    if(is_object($objAdmiCtaContableMesAnt))
                                    {   
                                        $intIdCtaContable  = $objAdmiCtaContableMesAnt->getId();
                                    }
                                }
                                
                            }
                        }                        
                        $objInfoDocFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdFactura); 

                        $strNumeroFacturaSri     = $objInfoDocFinancieroCab->getNumeroFacturaSri();
                        
                        $strComentario           = $strDescCuentaContable.' Fact: '.$strNumeroFacturaSri.' #Doc: '.$strReferencia.
                                                   ' Fecha: '.$strFechaDeposito;
                        
                        $arrayDetallePago = array(  'idFormaPago'              => $intIdFormaPago,
                                                    'descripcionFormaPago'     => $strDescFormaPago,
                                                    'idFactura'                => $intIdFactura,
                                                    'numeroFactura'            => $strNumeroFacturaSri,
                                                    'idBanco'                  => null,
                                                    'descripcionBanco'         => null,
                                                    'idTipoCuenta'             => null,
                                                    'descripcionTipoCuenta'    => null,
                                                    'numeroReferencia'         => $strReferencia,
                                                    'valorPago'                => $floatValorDetPago,
                                                    'comentario'               => $strComentario,
                                                    'fechaDeposito'            => $strFechaDeposito,
                                                    'codigoDebito'             => null,
                                                    'cuentaContableId'         => $intIdCtaContable,
                                                    'descripcionCuentaContable'=> $strDescCuentaContable,
                                                    'numeroDocumento'          => $strReferencia,
                                                    'strTipoFormaPago'         => $strTipoFormaPago); 
                        //Se crea detalle del pago
                        $arrayResultadoIngresoDetallesPago= $serviceInfoPagoDet->agregarDetallePago(
                            $entityInfoPagoCab,$arrayDetallePago,new \DateTime('now'),$valorCabeceraPago);

                        //Si el pago se originó en Pagos en Linea se actualiza a conciliado
                        $entityPagoLinea = $serviceInfoPagoLinea->obtenerPagoLinea('extbcogye', $strReferencia);

                        if (is_object($entityPagoLinea))
                        {
                            //Cambia el estado del pago a conciliado
                            $entityPagoLinea->setEstadoPagoLinea('Conciliado');
                            $entityPagoLinea->setUsrUltMod($strUsuarioCreacion);
                            $entityPagoLinea->setFeUltMod(new \DateTime('now'));
                            $emFinanciero->persist($entityPagoLinea);
                            $emFinanciero->flush();
                            
                            //Crea array para generar el historial del pago
                            $arrayRequestPagoLineaHist = array();
                            $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
                            $arrayRequestPagoLineaHist['jsonRequest']      = '';
                            $arrayRequestPagoLineaHist['strProceso']       = 'conciliarPagoAction';
                            $arrayRequestPagoLineaHist['strUsrCreacion']   = $strUsuarioCreacion;

                            //Crea un historial del pago
                            $entityPagoLineaHistorial = $serviceInfoPagoLinea->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
                            $emFinanciero->persist($entityPagoLineaHistorial);
                            $emFinanciero->flush();
                        }

                        /**
                         * Bloque que verifica si el detalle del pago genera un anticipo
                         */
                        $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';

                        if( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && !empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )
                        {
                            $arrayAnticipoACrear = $arrayResultadoIngresoDetallesPago['arr_anticipo'];

                            if( isset($arrayAnticipoACrear['valorAnticipo']) && !empty($arrayAnticipoACrear['valorAnticipo']) )
                            {
                                $floatValorAnticipo = $arrayAnticipoACrear['valorAnticipo'];

                                if( floatval($floatValorAnticipo) > 0 )
                                {
                                    $arrayAnticipo[] = $arrayResultadoIngresoDetallesPago['arr_anticipo'];

                                    $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'S';
                                }//( floatval($floatValorAnticipo) > 0 )
                            }//( isset($arrayAnticipoACrear['valorAnticipo']) && !empty($arrayAnticipoACrear['valorAnticipo']) )
                        }//( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && !empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )

                        $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';

                        $valorCabeceraPago           = $arrayResultadoIngresoDetallesPago['valorCabeceraPago'];
                        $arrPagosDetIdContabilidad[] = $arrayResultadoIngresoDetallesPago;                

                    }
                    //Se setea valor total de cabecera y hago persistencia
                    $entityInfoPagoCab->setValorTotal($floatValorPago);
                    $emFinanciero->persist($entityInfoPagoCab);
                    $emFinanciero->flush();

                    //Ingresa historial para el pago
                    $serviceInfoPago->ingresaHistorialPago($entityInfoPagoCab, 'Cerrado', 
                        new \DateTime('now'), $strUsuarioCreacion, null, 'pago creado en forma manual');


                    //CONTABILIZA DETALLES DE PAGO
                    $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");

                    //**ANTICIPOS -->>***********//
                    //***************************// 
                    //Si sobro valor del pago procede a crear anticipo
                    if(count($arrayAnticipo) > 0)
                    {
                        $totalAnticipo = 0;
                        //SUMO el arreglo   
                        for($i = 0; $i < count($arrayAnticipo); $i++)
                        {
                            $totalAnticipo = $totalAnticipo + $arrayAnticipo[$i]['valorAnticipo'];
                        }
                        //SOLO SI LA SUMA DEL VALOR DEL ANTICIPO ES MAYOR A 0 SE CREA ANTICIPO.
                        if($totalAnticipo>0)
                        {    
                            //SE CREA LA CABECERA DEL ANTICIPO
                            $entityAnticipoCab = new InfoPagoCab();
                            $entityAnticipoCab->setPagoId($entityInfoPagoCab->getId());
                            $entityAnticipoCab->setEmpresaId($intEmpresaId);
                            $entityAnticipoCab->setEstadoPago('Pendiente');
                            $entityAnticipoCab->setFeCreacion(new \DateTime('now'));

                            //Obtener la numeracion de la tabla Admi_numeracion
                            $datosNumeracionAnticipo = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "ANT");
                            $strSecuenciaAsig = '';
                            $strSecuenciaAsig = str_pad($datosNumeracionAnticipo->getSecuencia(), 7, "0", STR_PAD_LEFT);
                            $numero_de_anticipo = $datosNumeracionAnticipo->getNumeracionUno() . 
                                "-" . $datosNumeracionAnticipo->getNumeracionDos() . "-" . $strSecuenciaAsig;
                            //Actualizo la numeracion en la tabla
                            $numero_act = ($datosNumeracionAnticipo->getSecuencia() + 1);
                            $datosNumeracionAnticipo->setSecuencia($numero_act);
                            $emComercial->persist($datosNumeracionAnticipo);
                            $emComercial->flush();

                            $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                ->findOneByCodigoTipoDocumento('ANT');
                            $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                            $entityAnticipoCab->setNumeroPago($numero_de_anticipo);
                            $entityAnticipoCab->setOficinaId($intOficinaId);
                            $entityAnticipoCab->setPuntoId($intIdPunto);
                            $entityAnticipoCab->setUsrCreacion($strUsuarioCreacion);
                            $entityAnticipoCab->setValorTotal($totalAnticipo);
                            $entityAnticipoCab->setDetallePagoAutomaticoId($intIdPagoAutDet);
                            $emFinanciero->persist($entityAnticipoCab);
                            $emFinanciero->flush();
                            for($i = 0; $i < count($arrayAnticipo); $i++)
                            {
                                if ($arrayAnticipo[$i]['valorAnticipo']>0)
                                {    
                                    //CREA LOS DETALLES DEL ANTICIPO
                                    $entityAnticipoDet = new InfoPagoDet();
                                    $entityAnticipoDet->setEstado('Pendiente');
                                    $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                                    $entityAnticipoDet->setUsrCreacion($strUsuarioCreacion);
                                    $entityAnticipoDet->setValorPago($arrayAnticipo[$i]['valorAnticipo']);
                                    $entityAnticipoDet->setComentario($arrayAnticipo[$i]['comentario'].'. (Anticipo generado como saldo a favor)');
                                    $entityAnticipoDet->setCuentaContableId($arrayAnticipo[$i]['cuentaContableId']);
                                    $entityAnticipoDet->setFeDeposito(new \DateTime($arrayAnticipo[$i]['fechaDeposito']));
                                    $entityAnticipoDet->setDepositado('N');
                                    $entityAnticipoDet->setPagoId($entityAnticipoCab);
                                    $entityAnticipoDet->setFormaPagoId($arrayAnticipo[$i]['formaPagoId']);
                                    $entityAnticipoDet->setBancoTipoCuentaId($arrayAnticipo[$i]['bancoTipoCuentaId']);
                                    $entityAnticipoDet->setNumeroReferencia($arrayAnticipo[$i]['numeroReferencia']);
                                    $entityAnticipoDet->setNumeroCuentaBanco($arrayAnticipo[$i]['numeroCtaBanco']);
                                    $emFinanciero->persist($entityAnticipoDet);
                                    $emFinanciero->flush();

                                    $arrayDetalleAnticipo        = array('intIdPagoDet' => $entityAnticipoDet->getId(), 'strGeneraAnticipo' => 'N');
                                    $arrPagosDetIdContabilidad[] = $arrayDetalleAnticipo;


                                }
                            }
                            //Ingresa historial para el pago
                            $serviceInfoPago->ingresaHistorialPago($entityAnticipoCab, 'Pendiente', new \DateTime('now'), 
                                $strUsuarioCreacion, null, 'Anticipo generado por pago #' . $entityInfoPagoCab->getNumeroPago() . 
                                ' creado en forma manual.');                     
                        }                            
                    }
                    //<<--FIN ANTICIPOS ***************//
                    $objInfoPagoAutomaticoDet->setEstado('Procesado');
                    $emFinanciero->persist($objInfoPagoAutomaticoDet);
                    $emFinanciero->flush();
                    
                    //Graba historial de detalle de estado de cuenta.
                    $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                    $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                    $objInfoPagoAutomaticoHist->setEstado('Procesado');
                    $objInfoPagoAutomaticoHist->setObservacion('Se cambia estado de detalle a Procesado.');
                    $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                    $objInfoPagoAutomaticoHist->setUsrCreacion($strUsuarioCreacion);
                    $emFinanciero->persist($objInfoPagoAutomaticoHist);
                    $emFinanciero->flush();                   

                    $intIdPagAutomatico = $objInfoPagoAutomaticoDet->getPagoAutomaticoId();
                    $arrayDetPendientes = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                       ->findBy(array('pagoAutomaticoId' => $intIdPagAutomatico, 
                                                                       'estado'          => 'Pendiente'));

                    if(count($arrayDetPendientes)===0)
                    {
                        $objInfoPagoAutomaticoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                 ->find($intIdPagAutomatico);
                        if(is_object($objInfoPagoAutomaticoCab))
                        {
                            $objInfoPagoAutomaticoCab->setEstado('Procesado'); 
                            $emFinanciero->persist($objInfoPagoAutomaticoCab);
                            $emFinanciero->flush();                                   
                        }
                    }	

                }

                $boolCreaPago = true;
                $emFinanciero->getConnection()->commit();
                $emComercial->getConnection()->commit();
                

                if($boolFormaPagoDepositable && $boolCreaPago)
                {                  
                    $arrayIdsPagosDepositar[] = $entityInfoPagoCab->getId();
                    
                    if(isset($entityAnticipoCab) && is_object($entityAnticipoCab))
                    {
                        $arrayIdsPagosDepositar[] = $entityAnticipoCab->getId();
                    }                    
                }
                
                $floatSaldoPendiente = 0;
              
                if(is_object($objInfoPersonaEmpRol))
                {
                    $objInfoPersona   = $objInfoPersonaEmpRol->getPersonaId();
                    $strNombreCliente = $objInfoPersona->getRazonSocial() ? $objInfoPersona->getRazonSocial() : $objInfoPersona->getNombres() . ' ' .
                        $objInfoPersona->getApellidos();
                    
                    $arraySaldoPendiente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $intIdCliente,
                                                                         "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                    if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                      !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                    {
                        $floatSaldoPendiente = round($arraySaldoPendiente["floatSaldoPendiente"],2);
                    }
                }
                
                $objInfoContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                               ->findOneByPersonaEmpresaRolId($intIdCliente);
                if(is_object($objInfoContrato))
                {
                    $objInfoContratoDatoAdicional = $emComercial->getRepository('schemaBundle:InfoContratoDatoAdicional')
                                                                ->findOneByContratoId($objInfoContrato->getId());

                    $objFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                               ->findOneById($intIdFactura);

                    if(is_object($objInfoContratoDatoAdicional))
                    {
                        $boolNotificaPago = $objInfoContratoDatoAdicional->getNotificaPago();
                        if($boolNotificaPago && $boolCreaPago)
                        {
                            $arrayParamNotificacion                           = [];
                            $arrayParamNotificacion['boolNotManual']          = false;
                            $arrayParamNotificacion['strCodEmpresa']          = $intEmpresaId;
                            $arrayParamNotificacion['strPrefijoEmpresa']      = $strPrefijoEmpresa;
                            $arrayParamNotificacion['intIdPersonaEmpresaRol'] = $intIdCliente;
                            $arrayParamNotificacion['strFormaPago']           = $strDescFormaPago;
                            $arrayParamNotificacion['intIdFormaPago']         = $intIdFormaPago;
                            if(is_object($entityAnticipoCab))
                            {
                                $arrayParamNotificacion['strValorPago']       = strval($entityInfoPagoCab->getValorTotal()+
                                                                                       $entityAnticipoCab->getValorTotal());
                            }else
                            {
                                $arrayParamNotificacion['strValorPago']       = strval($entityInfoPagoCab->getValorTotal());
                            }
                            $arrayParamNotificacion['strNombreCliente']       = $strNombreCliente;
                            $arrayParamNotificacion['strSaldoCliente']        = strval($floatSaldoPendiente);
                            $arrayParamNotificacion['strCodigoPlantilla']     = 'NOT_PAG_AUTO';
                            $arrayParamNotificacion['strModulo']              = 'FINANCIERO';
                            $arrayParamNotificacion['strNombreParametro']     = 'AUTOMATIZACION PAGOS';
                            $arrayParamNotificacion['strParametroDet']        = 'NOTIFICA_PAG_AUT';
                            $arrayParamNotificacion['strMensaje']             = '';
                            $arrayParamNotificacion['intOficinaId']           = $objFactura->getOficinaId();
                            $arrayParamNotificacion['idPersonaEmpresaRol']    = $objSession->get('idPersonaEmpresaRol');

                            array_push($arrayParamsNotificacion,$arrayParamNotificacion);                            
                        }
                    }
                }
                               
            }
            catch(\Exception $e)
            {
                $serviceUtil->insertError( 'Telcos+', 
                                            'procesarPagoAction', 
                                            'Error al procesar detalle de estado de cuenta. Msj: '.$e->getMessage(), 
                                            $strUsuarioCreacion, 
                                            $objRequest->getClientIp());                    
                if ($emFinanciero->getConnection()->isTransactionActive()) 
                {                        
                    $emFinanciero->getConnection()->rollback();
                }
                if ($emComercial->getConnection()->isTransactionActive()) 
                {                        
                    $emComercial->getConnection()->rollback();
                }         
                $emFinanciero->getConnection()->close();
                $emComercial->getConnection()->close();

                $response = new Response(json_encode(
                        array('idpago' => '', 'msg' => "error", 'servicios' => '',
                            'link' => $this->generateUrl('infopagocab'), 'msgerror' => $e->getMessage())));
                return $response;
            }
            if($boolCreaPago)
            {
                //REACTIVA SERVICIOS
                $arrayServiciosInCorte = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->findBy(array('estado'=>'In-Corte','puntoId'=>$intIdPunto));
                if (!empty($arrayServiciosInCorte))
                {
                    $arrayParamNotificacion                           = [];
                    $arrayParamNotInCorte['strCodEmpresa']          = $intEmpresaId;
                    $arrayParamNotInCorte['strNombreCliente']       = $strNombreCliente;
                    $arrayParamNotInCorte['arrayServiciosInCorte']  = $arrayServiciosInCorte;
                    $arrayParamNotInCorte['strClientIp']            = $objRequest->getClientIp();
                    $arrayParamNotInCorte['strCodigoPlantilla']     = 'NOT_CLT_INCORTE';
                    $arrayParamNotInCorte['strModulo']              = 'FINANCIERO';
                    $arrayParamNotInCorte['strNombreParametro']     = 'AUTOMATIZACION PAGOS';
                    $arrayParamNotInCorte['strParametroDet']        = 'NOT_CLIENTE_INCORTE';
                    $arrayParamNotInCorte['strMensaje']             = '';
                    $arrayParamNotInCorte['intOficinaId']           = $intOficinaId;

                    $serviceInfoPagAut->notificaClienteInCorte($arrayParamNotInCorte);                
                }

                try
                {
                    $arrayParams=array(
                    'puntos'          => array($intIdPunto),
                    'prefijoEmpresa'  => $strPrefijoEmpresa,
                    'empresaId'       => $intEmpresaId,
                    'oficinaId'       => $intOficinaId,
                    'usuarioCreacion' => $strUsuarioCreacion,    
                    'ip'              => $objRequest->getClientIp(),
                    'idPago'          => $entityInfoPagoCab->getId(),
                    'debitoId'        => null
                    );
                    $strMsg = '';
                    $strMsg = $serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);
                }
                catch(\Exception $e)
                {
                    error_log('Error al reactivar servicio '.$e->getMessage().' Msj: '.$strMsg);
                }
            }            
        }
        
        // Si pago tiene forma de pago depositable, se genera y procesa el depósito respectivo.
        if($boolFormaPagoDepositable && $boolCreaPago)
        {                  
            $arrayParametros                       = [];
            $arrayParametros['strEmpresaCod']      = $intEmpresaId;
            $arrayParametros['strPrefijoEmpresa']  = $strPrefijoEmpresa;                    
            $arrayParametros['intIdCtaContable']   = $intIdCtaContable;
            $arrayParametros['intOficinaId']       = $intOficinaId;
            $arrayParametros['strUsrCreacion']     = $strUsuarioCreacion;
            $arrayParametros['strIpCreacion']      = $objRequest->getClientIp();
            $arrayParametros['intIdPagoAutDet']    = $intIdPagoAutDet;
            $arrayParametros['arrayPagosDepositar']= $arrayIdsPagosDepositar;
            $arrayParametros['strEstado']          = 'Pendiente';
            $arrayParametros['boolDepManual']      = false;
            $arrayRespuesta = $serviceInfoPagAut->generarDeposito($arrayParametros);
            if($arrayRespuesta['boolStatus'])
            {
                $arrayParametros['intIdDeposito']   = $arrayRespuesta['intIdDeposito'];
                $arrayParametros['strFechaProcesa'] = $strFechaDeposito;
                $arrayParametros['strReferencia']   = $strReferencia;
                $arrayParametros['strEstado']       = 'Procesado';

                $serviceInfoPagAut->procesarDeposito($arrayParametros);
            }        
        }        
        //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
        if ($arrayParametroDet["valor2"]=="S")
        {    
            $objParametros['serviceUtil']=$this->get('schema.Util');
            $strMsnErrorContabilidad=$emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                ->contabilizarPagosAnticipo($intEmpresaId, $arrPagosDetIdContabilidad, $objParametros);              
        }

        
        foreach($arrayParamsNotificacion as $arrayParamNotificacion)
        {
            try
            {
                $serviceInfoPagAut->notificaPagoAutomatico($arrayParamNotificacion);
            }
            catch(\Exception $e)
            {
                $serviceUtil->insertError( 'Telcos+', 
                'procesarPagoAction.envioCorreos', 
                'Error al procesar los correos. Msj: '.$e->getMessage(), 
                $strUsuarioCreacion, 
                $objRequest->getClientIp()); 
            }
        }
        
        $response = new Response(json_encode(
                array('idpago' => '', 'msg' => "error", 'servicios' => '',
                    'link' => $this->generateUrl('infopagocab'), 'msgerror' => '')));          
        return $response;        
    }

    
    /**
     * Documentación para el método 'getListadoErroresAction'.
     *
     * Permite listar los errores presentes en el estado de cuenta
     * - Listado de pagos asociados a facturas anuladas
     * 
     * @return listado_errores Listado de errores.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 16-10-2020 Se adapta para envio de punto seleccionado.6
     *
     */
    public function getListadoErroresAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intIdptocliente    = $objRequest->get('idPuntoSelect');      
        $intEmpresaId       = $objSession->get('idEmpresa');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $arrayParametros      = array();
        $arrayListadoPagosDep = array();
        $arrayResultados      = array();
        $objResponse          = new Response();
        
        try
        {
            $em_financiero = $this->get('doctrine')->getManager('telconet_financiero');
            $objDb               = $this->container->getParameter('database_dsn');
            $strUserFinanciero   = $this->container->getParameter('user_financiero');
            $strPasswdFinanciero = $this->container->getParameter('passwd_financiero');  
        
            $objOciCon      = oci_connect(
                                           $strUserFinanciero,
                                           $strPasswdFinanciero, 
                                           $objDb
                                         );
            $objCursor       = oci_new_cursor($objOciCon); 
            /*
             * Errores:
             * - Pago asociado a factura anulada
             * - Pagos negativos
             * - Documentos asociados al pto sin referencias
             * - Recupero los pagos dependientes por punto.
             */
            $arrayParametros['intEmpresaId'] = $intEmpresaId;  
            $arrayParametros['intIdPunto']   = $intIdptocliente;
            $arrayParametros['oci_con']      = $objOciCon;
            $arrayParametros['cursor']       = $objCursor;
            
            $arrayListadoPagosDep            = $em_financiero->getRepository('schemaBundle:InfoPagoCab')
                                                             ->getListadoDePagosDependientes($arrayParametros);
            if($arrayListadoPagosDep && count($arrayListadoPagosDep)>0)
            {
                $arrayResultados = array_merge($arrayResultados, $arrayListadoPagosDep);
            }
        }
        catch (\Exception $e) 
        {   
            error_log($e->getMessage());
            $strMensaje= 'Error al listar los errores presentes en el estado de cuenta .';
            $serviceUtil->insertError('Telcos+', 
                                      'ReportesController.getListadoErroresAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
        }
        
        $objResponse = new Response(json_encode(array('listado_errores' => $arrayResultados)));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }    
   /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * getEstadoCuentaPorFactura()
     * Función que consulta el estado de cuenta por punto de una factura seleccionada enviada como parámetro.
     * 
     * @author Adrián Limones <alimonesr@telconet.ec>
     * @version 1.0 16-09-2020  
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 14-10-2020 Revisión e integración con lectura de factura seleccionada en nueva fila de estado de cuenta.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 26-07-2022  Se agrega el envío de parámetros a partir del pago seleccionado.
     *     
     * @return $objResponse 
     */
    public function getEstadoCuentaPorFacturaAction()
    {
        $objRequest            = $this->getRequest();
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial           = $this->getDoctrine()->getManager(); 

        $arrayParametros['user_financiero']           = $this->container->getParameter('user_financiero');
        $arrayParametros['passwd_financiero']         = $this->container->getParameter('passwd_financiero');
        $arrayParametros['database_dsn']              = $this->container->getParameter('database_dsn');
        $intIdPago      = $objRequest->get('idPagoSelect'); 
        
        $objInfoPagoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->find($intIdPago); 
        

        if(is_object($objInfoPagoCab))
        {
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                        ->find($objInfoPagoCab->getPuntoId());
            
            $intIdCliente           = $objInfoPunto->getPersonaEmpresaRolId()->getId();
            $strPuntosConcatenados  = $objInfoPagoCab->getPuntoId();           
            $objInfoPersonaEmpRol   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->find($intIdCliente);            
        
            if(is_object($objInfoPersonaEmpRol))
            {
                $intIdOficina = $objInfoPersonaEmpRol->getOficinaId()->getId();
            }  
        }
        
        /*
         * Cuando no hay fecha, se carga por defecto el anio actual
         * Proceso:
         * - Se debe obtener la fecha inicial del anio vigente
         * - Se debe pasar por parametro la fecha para el calculo de saldos
         * - Se debe imprimir los saldos en el json
         * - Se debe mandar la fecha al query de las facturas, para que me liste facturas >= a esa fecha
         * */

        $strFechaDesde                      = date("Y") . "-01-01";
        $strFechaHasta                      = "";       
        $arrayInfoDocumentoFinancieroCab    = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->findEstadoDeCuenta($intIdOficina, 
                                                                                 $strFechaDesde, 
                                                                                 $strFechaHasta, 
                                                                                 $strPuntosConcatenados);
        $arrayInfoDocFinCabAntPgPendientes  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->findAnticiposEstadoDeCuenta($intIdOficina, 
                                                                                          $strFechaDesde, 
                                                                                          $strFechaHasta, 
                                                                                          $strPuntosConcatenados, 
                                                                                          "Pendiente");
        $arrayInfoDocFinCabOG               = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->findEstadoDeCuentaOG($intIdOficina, 
                                                                                   $strFechaDesde, 
                                                                                   $strFechaHasta, 
                                                                                   $strPuntosConcatenados);
        $arrayInfoPagoCabAntAsig            = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                            ->obtenerAnticiposAsignados("Asignado", 
                                                                                        $strPuntosConcatenados, 
                                                                                        $strFechaDesde, 
                                                                                        $strFechaHasta);

        $intSumatoriaValorTotal     = 0;
        $arrayListadoEstadoCuenta   = $arrayInfoDocumentoFinancieroCab['registros'];
        $arrayAntPgPendientes       = $arrayInfoDocFinCabAntPgPendientes['registros'];
        $arrayAntAsig               = $arrayInfoPagoCabAntAsig['registros'];
        $arrayListadoMigracion      = $arrayInfoDocFinCabOG['registros'];

        if( !empty($arrayListadoMigracion) )
        {
            foreach($arrayListadoMigracion as $arrayListadoMigracion)
            {
                $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayListadoMigracion['puntoId']);
                $intValorIngreso    = "";
                $intValorEgreso     = "";
                $strTipoDocumento   = "";
                if($arrayListadoMigracion['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                       ->find($arrayListadoMigracion['tipoDocumentoId']);
                    $strTipoDocumento = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "+")
                {
                    $intSumatoriaValorTotal +=  $arrayListadoMigracion['valorTotal'];
                    $intValorIngreso        = $arrayListadoMigracion['valorTotal'];
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumatoriaValorTotal -=  $arrayListadoMigracion['valorTotal'];
                    $intValorEgreso         = $arrayListadoMigracion['valorTotal'];
                }                
                $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayListadoMigracion['oficinaId']);

                $strNumeroRefCtaBnco = "";
                $strNumeroRefCtaBnco = $arrayListadoMigracion['numeroReferencia'];

                if($strNumeroRefCtaBnco == "")
                {
                    if(isset($arrayListadoMigracion['numeroCuentaBanco']))
                    {
                        $strNumeroRefCtaBnco = $arrayListadoMigracion['numeroCuentaBanco'];
                    }
                }

                $strNumeroFactPagada = "";

                if(isset($arrayListadoMigracion['referenciaId']))
                {
                    if($strTipoDocumento == "PAG" || $strTipoDocumento == "PAGC" || $strTipoDocumento == "ANT" ||
                        $strTipoDocumento == "ANTC" || $strTipoDocumento == "ANTS" || $strTipoDocumento == "NC")
                    {
                        //echo $arrayListadoMigracion['referenciaId']."-";
                        if(isset($arrayListadoMigracion['referenciaId']))
                        {
                            $entityInfoDocumentoFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                              ->find($arrayListadoMigracion['referenciaId']);
                            if(isset($entityInfoDocumentoFinancieroCab))
                            {
                                $strNumeroFactPagada = $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri();
                            }
                        }
                        else
                        {
                            $strNumeroFactPagada = "";
                        }
                    }
                    else
                    {
                        $strNumeroFactPagada = "";
                    }
                }


                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayListadoMigracion['numeroFacturaSri'],
                    'valor_ingreso'           => $intValorIngreso,
                    'valor_egreso'            => $intValorEgreso,
                    'acumulado'               => round($intSumatoriaValorTotal, 2),
                    'Fecreacion'              => strval(date_format($arrayListadoMigracion['feCreacion'], "d/m/Y")),
                    'strFeEmision'            => '',
                    'strFeAutorizacion'       => '',
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => $strNumeroFactPagada,
                    'formaPago'               => $arrayListadoMigracion['codigoFormaPago'],
                    'numero'                  => $strNumeroRefCtaBnco,
                    'observacion'             => $strNumeroRefCtaBnco,
                    'boolSumatoriaValorTotal' => true
                );
            }
        }

        $intSumatoriaTotalMigracion = $intSumatoriaValorTotal;

        $arrayContenedorResultados[] = array(
            'documento'               => "",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $arrayContenedorResultados[] = array(
            'documento'               => "MOVIMIENTOS",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $intValorIngreso        = 0;
        $intValorEgreso         = 0;
        $intSumatoriaValorTotal = 0;
        if( !empty($arrayListadoEstadoCuenta) )
        {
            foreach($arrayListadoEstadoCuenta as $arrayListadoEstadoCuenta)
            {
                $intValorIngresoDoc = 0;
                $intValorEgresoDoc  = 0;
                $boolContinuarFlujo = true;
                $strObservacionInfoFinDocDet = "";
                
                /**
                 * Bloque que agrega las NDI al estado de cuenta SOLO SI no tiene asociado un PAGO_DET_ID en el detalle de la factura. Es decir,
                 * se verifica que el campo PAGO_DET_ID de la tabla DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET esté en NULL.
                 */
                if( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                {
                    $objInfoDocumentoFinancieroDet   = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                     ->findOneByDocumentoId($arrayListadoEstadoCuenta['id']);
                    
                    $objCaracteristica              = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneBy(array('descripcionCaracteristica' => 'PROCESO_DIFERIDO',
                                                                                    'tipo'                      => 'FINANCIERO',
                                                                                    'estado'                    => 'Activo' ));

                    $objInfoDocumentoCaracteristica = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                    ->findOneBy(array("documentoId"      => $arrayListadoEstadoCuenta['id'],
                                                                                      "caracteristicaId" => $objCaracteristica->getId()));
                    if(!is_object($objInfoDocumentoCaracteristica))
                    {
                        if( $objInfoDocumentoFinancieroDet != null )
                        {
                            $intIdPagoDet = $objInfoDocumentoFinancieroDet->getPagoDetId() ? $objInfoDocumentoFinancieroDet->getPagoDetId() : 0;

                            if( $intIdPagoDet > 0 )
                            {
                                $boolContinuarFlujo = false;
                            }//( $intIdPagoDet > 0 )
                        }//( $objInfoDocumentoFinancieroDet != null )
                        else
                        {
                            $boolContinuarFlujo = false;
                        }
                    }
                }//( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                
                
                if( $boolContinuarFlujo )
                {
                    $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayListadoEstadoCuenta['puntoId']);
                    $strTipoDocumento   = "";

                    if($arrayListadoEstadoCuenta['tipoDocumentoId'] != "")
                    {
                        $entityAdmiTipoDocumentoFinanciero  = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                            ->find($arrayListadoEstadoCuenta['tipoDocumentoId']);
                        $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                    }
                    else
                    {
                        $strTipoDocumento = "";
                    }

                    if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "+")
                    {
                        $intSumatoriaValorTotal     +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                        $intValorIngreso            +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                        $intTotalFacturas           +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                    }
                    
                    $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                         ->find($arrayListadoEstadoCuenta['oficinaId']);

                    $strNumeroRefCtaBnco = "";
                    $strNumeroRefCtaBnco = $arrayListadoEstadoCuenta['numeroReferencia'];

                    if($strNumeroRefCtaBnco == "")
                    {
                        if(isset($arrayListadoEstadoCuenta['numeroCuentaBanco']))
                            $strNumeroRefCtaBnco = $arrayListadoEstadoCuenta['numeroCuentaBanco'];
                    }

                    $strNumeroFactPagada = "";

                    $entityInfoDocumentoFinancieroDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                      ->findByDocumentoId($arrayListadoEstadoCuenta['id']);
                    foreach($entityInfoDocumentoFinancieroDet as $entityInfoDocumentoFinancieroDet)
                    {
                        $strObservacionDetalleFact = $entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle();
                        if(!empty($strObservacionDetalleFact))
                        {
                            $strObservacionInfoFinDocDet = preg_replace('([^A-Za-z0-9,-./])', 
                                                                        ' ', 
                                                                        $entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle());
                            break;
                        }
                    }

                    //Por cada factura busco sus pagos
                    $arrayParametros['intIdDocumento'] = $arrayListadoEstadoCuenta['id'];
                    
                    $cursorResult = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->getDocumentosRelacionados($arrayParametros);
                    
                    if( !empty($cursorResult) )
                    {
                        /**
                         * Bloque que agrega las NDI al estado de cuenta SOLO SI no tiene asociado un PAGO_DET_ID en el detalle de la factura. Es 
                         * decir, se verifica que el campo PAGO_DET_ID de la tabla DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET esté en NULL.
                         */
                        if( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                        {
                            $strFeCreacion      = strval(date_format($arrayListadoEstadoCuenta['feCreacion'], "d/m/Y"));
                            $intValorIngresoDoc += $arrayListadoEstadoCuenta['valorTotal'];

                            $arrayContenedorResultados[] = array( 'documento'               => $arrayListadoEstadoCuenta['numeroFacturaSri'],
                                                                  'valor_ingreso'           => round($arrayListadoEstadoCuenta['valorTotal'], 2),
                                                                  'valor_egreso'            => "0.00",
                                                                  'acumulado'               => "",
                                                                  'Fecreacion'              => $strFeCreacion,
                                                                  'strFeEmision'            => $arrayListadoEstadoCuenta['fecEmision'],
                                                                  'strFeAutorizacion'       => $arrayListadoEstadoCuenta['fecAutorizacion'],
                                                                  'tipoDocumento'           => $strTipoDocumento,
                                                                  'punto'                   => $entityInfoPunto->getLogin(),
                                                                  'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                                                  'referencia'              => $strNumeroFactPagada,
                                                                  'formaPago'               => $arrayListadoEstadoCuenta['codigoFormaPago'],
                                                                  'numero'                  => $strNumeroRefCtaBnco,
                                                                  'observacion'             => str_replace('cuota' ,
                                                                                                           '<font color="000000"><b>CUOTA</b>'
                                                                                                           . '</font>',
                                                                                                           $strObservacionInfoFinDocDet),
                                                                  'boolSumatoriaValorTotal' => true);

                        }//( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                    
                        if($arrayListadoEstadoCuenta['codigoTipoDocumento'] == "FAC" || $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "FACP")
                        {
                            $intValorIngresoDoc+=$arrayListadoEstadoCuenta['valorTotal'];

                            $arrayContenedorResultados[] = array(
                                'documento'               => $arrayListadoEstadoCuenta['numeroFacturaSri'],
                                'valor_ingreso'           => round($arrayListadoEstadoCuenta['valorTotal'], 2),
                                'valor_egreso'            => "0.00",
                                'acumulado'               => "",
                                'Fecreacion'              => strval(date_format($arrayListadoEstadoCuenta['feCreacion'], "d/m/Y")),
                                'strFeEmision'            => $arrayListadoEstadoCuenta['fecEmision'],
                                'strFeAutorizacion'       => $arrayListadoEstadoCuenta['fecAutorizacion'],
                                'tipoDocumento'           => $strTipoDocumento,
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => $strNumeroFactPagada,
                                'formaPago'               => $arrayListadoEstadoCuenta['codigoFormaPago'],
                                'numero'                  => $strNumeroRefCtaBnco,
                                'observacion'             => $strObservacionInfoFinDocDet,
                                'boolSumatoriaValorTotal' => true
                            );
                        }

                        while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) )
                        {

                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAG' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANT' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                            {

                                /**
                                 * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
                                 * al valor del Anticipo Original y si este se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
                                 * estado de cuenta en otro color.
                                 * */                
                                 $strEstado               = 'Cerrado';
                                 $boolSumatoriaValorTotal = true;
                                 $objAnticipoPorCruce     = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                                          ->getAnticipoPorCrucePorPagoDetIdPorEstado
                                                                            ($arrayDocumentosRelacionados['ID_PAGO_DET'],
                                                                             $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],$strEstado);
                                 if( !empty($objAnticipoPorCruce) )
                                 {
                                    $boolSumatoriaValorTotal = false;
                                 }

                                $intValorEgresoDoc      +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                if($boolSumatoriaValorTotal)
                                {
                                    $intSumatoriaValorTotal -=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                    $intValorEgreso         +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                }
                                $entityInfoPagoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                                   ->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => "0.00",
                                    'valor_egreso'            => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                                    'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                                    'observacion'             => $entityInfoPagoDet->getComentario(),
                                    'boolSumatoriaValorTotal' => $boolSumatoriaValorTotal
                                );
                            }

                            //Me devuelve todo el listado de documentos
                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ND' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NDI' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                            {
                                $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorIngreso        +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);

                                $entityInfoDocFinDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                     ->findByDocumentoId($arrayDocumentosRelacionados['ID_PAGO_DET']);
                                foreach($entityInfoDocFinDet as $entityInfoDocFinDet)
                                {
                                    $strObservacion = preg_replace('([^A-Za-z0-9])', ' ', $entityInfoDocFinDet->getObservacionesFacturaDetalle());
                                }

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'valor_egreso'            => "0.00",
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => "",
                                    'numero'                  => "",
                                    'observacion'             => $strObservacion,
                                    'boolSumatoriaValorTotal' => true
                                );

                                $arrayParametros['intIdDocumento']=$arrayDocumentosRelacionados['ID_PAGO_DET'];

                                $this->obtenerDetalleNotasDebito($arrayContenedorResultados, 
                                                                 $intValorIngresoDoc, 
                                                                 $intValorIngreso, 
                                                                 $intSumatoriaValorTotal, 
                                                                 $intValorEgresoDoc, 
                                                                 $intValorEgreso, 
                                                                 $entityInfoPunto, 
                                                                 $entityInfoOficinaGrupo, 
                                                                 $emFinanciero,
                                                                 $arrayParametros
                                                                );
                            }

                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NC' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NCI')
                            {

                                $intSumatoriaValorTotal -= round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorEgreso         += round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorEgresoDoc      += round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $arrayParametrosCab     =  array( "referenciaDocumentoId" => $arrayListadoEstadoCuenta['id'], 
                                                                  "estadoImpresionFact"   => "Activo",
                                                                  "numeroFacturaSri"      => $arrayDocumentosRelacionados['NUMERO_PAGO'] );
                                $entityInfoDocFinCab    =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                         ->findOneBy( $arrayParametrosCab );

                                if($entityInfoDocFinCab)
                                    $observacion= $entityInfoDocFinCab->getObservacion();
                                else
                                    $observacion= "";

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => "0.00",
                                    'valor_egreso'            => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => "",
                                    'numero'                  => "",
                                    'observacion'             => $observacion,
                                    'boolSumatoriaValorTotal' => true
                                );
                            }
                        }
                        //Envio el totalizado
                        $arrayContenedorResultados[] = array(
                            'documento'               => "Total:",
                            'valor_ingreso'           => round($intValorIngresoDoc, 2),
                            'valor_egreso'            => round($intValorEgresoDoc, 2),
                            'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                            'Fecreacion'              => "",
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => "",
                            'punto'                   => "",
                            'oficina'                 => "",
                            'referencia'              => "",
                            'boolSumatoriaValorTotal' => true
                        );

                        $intValorIngresoDoc = 0;
                        $intValorEgresoDoc  = 0;

                        //Termina de escribir todo envio en blanco
                        $arrayContenedorResultados[] = array(
                            'documento'               => "",
                            'valor_ingreso'           => "",
                            'valor_egreso'            => "",
                            'acumulado'               => "",
                            'Fecreacion'              => "",
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => "",
                            'punto'                   => "",
                            'oficina'                 => "",
                            'referencia'              => "",
                            'boolSumatoriaValorTotal' => true
                        );
                    }//($cursorResult)
                }//( $boolContinuarFlujo )
            }
        }        
        if( !empty($arrayAntPgPendientes) )
        {
            $intSumAntPgPendiente   = 0;
            $intValorAntPg          = 0;

            //Termina de escribir todo envio en blanco
            $arrayContenedorResultados[] = array(
                'documento'               => "Anticipos no aplicados",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );

            $intTotalAntPgPendientes    = 0;
            $intValorIngresoDoc         = 0;
            $intValorEgresoDoc          = 0;

            foreach($arrayAntPgPendientes as $arrayInfoDocFinCabAntPgPendientes)
            {
                $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayInfoDocFinCabAntPgPendientes['puntoId']);
                $strTipoDocumento   = "";


                if($arrayInfoDocFinCabAntPgPendientes['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero  = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                        ->find($arrayInfoDocFinCabAntPgPendientes['tipoDocumentoId']);
                    $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }
               /**
                * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
                * al valor del Anticipo Original y si este se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
                * estado de cuenta en otro color.
                * */                
                $strEstado               = 'Cerrado';
                $boolSumatoriaValorTotal = true;
                $objAnticipoPorCruce     = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                         ->getAnticipoPorCrucePorPagoDetIdPorEstado
                                                           ($arrayInfoDocFinCabAntPgPendientes['id'],$strTipoDocumento,$strEstado);
                if( !empty($objAnticipoPorCruce) )
                {
                    $boolSumatoriaValorTotal = false;
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumAntPgPendiente       +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    $intValorAntPg              =   round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    $intTotalAntPgPendientes    +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    if($boolSumatoriaValorTotal)
                    {
                        $intSumatoriaValorTotal     -=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                        $intValorEgreso             +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    }
                }

                $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                     ->find($arrayInfoDocFinCabAntPgPendientes['oficinaId']);

                $strNumeroRefCtaBnco    =   "";
                $intValorEgresoDoc      +=  $intValorAntPg;
                
                //Si el anticipo es recaudacion entonces agrega la fecha del anticipo en el comentario
                if ($arrayInfoDocFinCabAntPgPendientes['codigoFormaPago']=='REC')
                {
                    $arrayInfoDocFinCabAntPgPendientes['comentario'].=', fecha: '.
                        strval(date_format($arrayInfoDocFinCabAntPgPendientes['feCreacion'], "Y-m-d H:i:s"));
                }    
                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayInfoDocFinCabAntPgPendientes['numeroFacturaSri'],
                    'valor_ingreso'           => "0.00",
                    'valor_egreso'            => round($intValorAntPg, 2),
                    'acumulado'               => "",
                    'Fecreacion'              => $arrayInfoDocFinCabAntPgPendientes['feCreacion'],
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => "",
                    'formaPago'               => $arrayInfoDocFinCabAntPgPendientes['codigoFormaPago'],
                    'numero'                  => $arrayInfoDocFinCabAntPgPendientes['numeroReferencia'],
                    'observacion'             => $arrayInfoDocFinCabAntPgPendientes['comentario'],
                    'boolSumatoriaValorTotal' => $boolSumatoriaValorTotal
                );

                $arrayParametros['intIdDocumento']  = $arrayInfoDocFinCabAntPgPendientes['id'];
                $cursorAntPgPendientes              = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                    ->getNotaDebitoAntNoAplicados($arrayParametros);
                if( !empty($cursorAntPgPendientes) )
                {
                    while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorAntPgPendientes, OCI_ASSOC + OCI_RETURN_NULLS)) )
                    {

                        if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ND' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NDI' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                        {
                            $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intValorIngreso        +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $entityInfoDocFinDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                 ->findByDocumentoId($arrayDocumentosRelacionados['ID_PAGO_DET']);
                            foreach($entityInfoDocFinDet as $entityInfoDocFinDet)
                            {
                                $strObservacion = preg_replace('([^A-Za-z0-9])', ' ', $entityInfoDocFinDet->getObservacionesFacturaDetalle());
                            }

                            $arrayContenedorResultados[] = array(
                                'documento'               => $arrayDocumentosRelacionados['NUMERO_FACTURA_SRI'],
                                'valor_ingreso'           => round($arrayDocumentosRelacionados['PRECIO'], 2),
                                'valor_egreso'            => "0.00",
                                'acumulado'               => "",
                                'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                'strFeEmision'            => "",
                                'strFeAutorizacion'       => "",
                                'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => "",
                                'formaPago'               => "",
                                'numero'                  => "",
                                'observacion'             => $strObservacion,
                                'boolSumatoriaValorTotal' => true
                            );
                        }

                        if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAG' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANT' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                        {
                            $intValorEgresoDoc      +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intSumatoriaValorTotal -=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intValorEgreso         +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $entityInfoPagoDet      = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                                    ->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                            $arrayContenedorResultados[] = array(
                                'documento'               => "",
                                'valor_ingreso'           => "0.00",
                                'valor_egreso'            => round($arrayDocumentosRelacionados['PRECIO'], 2),
                                'acumulado'               => "",
                                'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                'strFeEmision'            => "",
                                'strFeAutorizacion'       => "",
                                'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => "",
                                'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                                'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                                'observacion'             => $entityInfoPagoDet->getComentario(),
                                'boolSumatoriaValorTotal' => true
                            );
                        }
                    }
                }
                //Envio el totalizado
                $arrayContenedorResultados[] = array(
                    'documento'               => "Total:",
                    'valor_ingreso'           => round($intValorIngresoDoc, 2),
                    'valor_egreso'            => round($intValorEgresoDoc, 2),
                    'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );

                $intValorIngresoDoc     = 0;
                $intValorEgresoDoc      = 0;

                //Termina de escribir todo envio en blanco
                $arrayContenedorResultados[] = array(
                    'documento'               => "",
                    'valor_ingreso'           => "",
                    'valor_egreso'            => "",
                    'acumulado'               => "",
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );
            }
        }
        
        if( !empty($arrayAntAsig) )
        {
            $intSumAntPgPendiente   = 0;
            $intValorAntPg          = 0;

            //Termina de escribir todo envio en blanco
            $arrayContenedorResultados[] = array(
                'documento'               => "Historial Anticipos asignados",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );

            $intTotalAntPgPendientes    = 0;
            $intValorIngresoDoc         = 0;
            $intValorEgresoDoc          = 0;

            foreach($arrayAntAsig as $arrayAntAsig)
            {
                $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayAntAsig['puntoId']);
                $strTipoDocumento   = "";


                if($arrayAntAsig['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero  = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                        ->find($arrayAntAsig['tipoDocumentoId']);
                    $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }
                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumAntPgPendiente   +=  round($arrayAntAsig['valorTotal'], 2);
                    $intValorAntPg          =   round($arrayAntAsig['valorTotal'], 2);
                    $intTotalAntPgPendientes+=  round($arrayAntAsig['valorTotal'], 2);
                    $intSumatoriaValorTotal -=  round($arrayAntAsig['valorTotal'], 2);
                    $intValorEgreso         +=  round($arrayAntAsig['valorTotal'], 2);
                }

                $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayAntAsig['oficinaId']);
                $strNumeroRefCtaBnco    = "";
                $intValorEgresoDoc      +=  $intValorAntPg;
                $strObservacionPago = $emFinanciero->getRepository('schemaBundle:InfoPagoHistorial')
                   ->obtenerHistorialDePago($arrayAntAsig['id']);
                //Si el anticipo es recaudacion entonces agrega la fecha del anticipo en el comentario
                if ($arrayAntAsig['recaudacionId'])
                {
                    $strObservacionPago['registro']['observacion'].=", fecha:".strval(date_format($arrayAntAsig['feCreacion'], "Y-m-d H:i:s"));
                }
                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayAntAsig['numeroPago'],
                    'valor_ingreso'           => "0.00",
                    'valor_egreso'            => round($intValorAntPg, 2),
                    'acumulado'               => "",
                    'Fecreacion'              => strval(date_format($arrayAntAsig['feCreacion'], "d/m/Y")),
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => "",
                    'formaPago'               => "",
                    'numero'                  => "",
                    'observacion'             => $strObservacionPago['registro']['observacion'],
                    'boolSumatoriaValorTotal' => true
                );

                $arrayParametros['intIdPago']       = $arrayAntAsig['id'];
                $cursorResult                       = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                    ->getAnticipoGenerados($arrayParametros);
                if( !empty($cursorResult) )
                {
                    while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) )
                    {
                        $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $intValorIngreso        +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $entityInfoPagoDet      =   $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                        $arrayContenedorResultados[] = array(
                            'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                            'valor_ingreso'           => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                            'valor_egreso'            => "0.00",
                            'acumulado'               => "",
                            'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                            'punto'                   => $entityInfoPunto->getLogin(),
                            'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                            'referencia'              => "",
                            'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                            'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                            'observacion'             => $entityInfoPagoDet->getComentario(),
                            'boolSumatoriaValorTotal' => true
                        );
                    }
                }


                //Envio el totalizado
                $arrayContenedorResultados[] = array(
                    'documento'               => "Total:",
                    'valor_ingreso'           => round($intValorIngresoDoc, 2),
                    'valor_egreso'            => round($intValorEgresoDoc, 2),
                    'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );

                $intValorIngresoDoc     = 0;
                $intValorEgresoDoc      = 0;

                //Termina de escribir todo envio en blanco
                $arrayContenedorResultados[] = array(
                    'documento'               => "",
                    'valor_ingreso'           => "",
                    'valor_egreso'            => "",
                    'acumulado'               => "",
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );
            }
        }

        //Termina de escribir todo envio en blanco
        $arrayContenedorResultados[] = array(
            'documento'               => "RESUMEN PTO CLIENTE:",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        if($intSumatoriaTotalMigracion > 0)
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "Migracion",
                'valor_ingreso'           => round($intSumatoriaTotalMigracion, 2),
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }
        else
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "Migracion",
                'valor_ingreso'           => "",
                'valor_egreso'            => round(abs($intSumatoriaTotalMigracion), 2),
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }

        $arrayContenedorResultados[] = array(
            'documento'               => "Debe",
            'valor_ingreso'           => round($intValorIngreso, 2),
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $arrayContenedorResultados[] = array(
            'documento'               => "Haber",
            'valor_ingreso'           => "",
            'valor_egreso'            => round($intValorEgreso, 2),
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        //Para el saldo debo considerar el valor de migracion
        $intSumatoriaValorTotal +=  $intSumatoriaTotalMigracion;

        $arrayContenedorResultados[] = array(
            'documento'               => "SALDO:",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => round($intSumatoriaValorTotal, 2),
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'numero'                  => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        if(empty($arrayContenedorResultados))
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }
        
        $objResponse = new Response(json_encode(array('documentos' => $arrayContenedorResultados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;        
        
    }

    /**
     * obtenerDetalleNotasDebito, obtiene el detalle de los documentos ND y NDI
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-10-2020 - Se estandariza código de función original (gvillalba).
     * @return Array de los detalles del documento
     */
    function obtenerDetalleNotasDebito(
                                        &$arrayContenedorResultados, 
                                        &$intValorIngresoDoc, 
                                        &$intValorIngreso, 
                                        &$intSumatoriaValorTotal, 
                                        &$intValorEgresoDoc, 
                                        &$intValorEgreso, 
                                        $entityInfoPunto, 
                                        $entityInfoOficinaGrupo, 
                                        $emFinanciero, 
                                        $arrayParametros)
    {
        $cursorResult = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getDocumentosRelacionados($arrayParametros);

        if($cursorResult)
        {
            while(($row = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) != false)
            {
                if(
                    $row['CODIGO_TIPO_DOCUMENTO'] == 'PAG' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANT' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                {

                    $intValorEgresoDoc+=round($row['VALOR_PAGO'], 2);

                    $intSumatoriaValorTotal-=round($row['VALOR_PAGO'], 2);
                    $intValorEgreso+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findOneById($row['ID_PAGO_DET']);

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => "0.00",
                        'valor_egreso'  => round($row['VALOR_PAGO'], 2),
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $entityInfoPunto->getLogin(),
                        'oficina'       => $entityInfoOficinaGrupo->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => $row['CODIGO_FORMA_PAGO'],
                        'numero'        => $row['NUMERO_REFERENCIA'],
                        'observacion'   => $observacion_int->getComentario(),
                    );
                }

                //Me devuelve todo el listado de documentos
                if(
                    $row['CODIGO_TIPO_DOCUMENTO'] == 'ND' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'NDI' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                {
                    $intValorIngresoDoc+=round($row['VALOR_PAGO'], 2);

                    $intSumatoriaValorTotal+=round($row['VALOR_PAGO'], 2);
                    $intValorIngreso+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($row['ID_PAGO_DET']);
                    foreach($observacion_int as $obs)
                        $observacion = preg_replace('([^A-Za-z0-9])', ' ', $obs->getObservacionesFacturaDetalle());

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => round($row['VALOR_PAGO'], 2),
                        'valor_egreso'  => "0.00",
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $entityInfoPunto->getLogin(),
                        'oficina'       => $entityInfoOficinaGrupo->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => "",
                        'numero'        => "",
                        'observacion'   => $observacion,
                    );

                    $arrayParametros['intIdDocumento'] = $row['ID_PAGO_DET'];
                    $this->obtenerDetalleNotasDebito(
                        $arrayContenedorResultados, 
                        $intValorIngresoDoc, 
                        $intValorIngreso, 
                        $intSumatoriaValorTotal, 
                        $intValorEgresoDoc, 
                        $intValorEgreso, 
                        $entityInfoPunto, 
                        $entityInfoOficinaGrupo, 
                        $emFinanciero, 
                        $arrayParametros);
                }

                if($row['CODIGO_TIPO_DOCUMENTO'] == 'NC' || $row['CODIGO_TIPO_DOCUMENTO'] == 'NCI')
                {
                    $intSumatoriaValorTotal-=round($row['VALOR_PAGO'], 2);
                    $intValorEgreso+=round($row['VALOR_PAGO'], 2);
                    $intValorEgresoDoc+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                    ->findByDocumentoId($row['ID_PAGO_DET']);
                    foreach($observacion_int as $obs)
                        $observacion = preg_replace('([^A-Za-z0-9])', ' ', $obs->getObservacionesFacturaDetalle());

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => "0.00",
                        'valor_egreso'  => round($row['VALOR_PAGO'], 2),
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $entityInfoPunto->getLogin(),
                        'oficina'       => $entityInfoOficinaGrupo->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => "",
                        'numero'        => "",
                        'observacion'   => $observacion,
                    );
                }
            }
        }
    }

    /**
     * getDetEstadoCuentaAction()
     * Función que permite obtener información de un detalle de estado de cuenta.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 20-04-2022
     * 
     * @return objResponse
     */  
    public function getDetEstadoCuentaAction()
    {
       $objRequest             = $this->getRequest();
       $intIdPagoAutomaticoDet = $objRequest->get('idDetalle');        
       $emFinanciero           = $this->getDoctrine()->getManager("telconet_financiero");
       
       $objInfoPagoAutomaticoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')->find($intIdPagoAutomaticoDet);
       
       if(is_object($objInfoPagoAutomaticoDet))
       {
           $intIdPagoAutCab = $objInfoPagoAutomaticoDet->getPagoAutomaticoId();
           $strFechaTrans   = $objInfoPagoAutomaticoDet->getFecha();
           $strTipo         = $objInfoPagoAutomaticoDet->getObservacion();
           $strReferencia   = $objInfoPagoAutomaticoDet->getNumeroReferencia();
           $floatMonto      = $objInfoPagoAutomaticoDet->getMonto();
           $strEstado       = $objInfoPagoAutomaticoDet->getEstado();
       }
       $objResponse = new Response(json_encode(array('idPagAutCab'      => $intIdPagoAutCab,'idPagAutDet' => $intIdPagoAutomaticoDet, 
                                                     'fechaTransaccion' => $strFechaTrans,'numeroReferencia' => $strReferencia,
                                                     'tipoTran'         => $strTipo,'monto' => $floatMonto,'estado' => $strEstado)));
       $objResponse->headers->set('Content-type', 'text/json');
       return $objResponse;
    }
    
   /**
     * @Secure(roles="ROLE_65-7457")
     * gridPagoPrecargadoAction()
     * Función que obtiene un listado de detalles para un pago precargado.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 09-05-2022
     * @since 1.0
     *
     * @return $objResponse 
     * 
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.1 08-12-2022   se agrega funcionalidad para que retorne el tiempo de sesion.
     * 
     * 
     */
    public function gridPagoPrecargadoAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();   
        $intIdCliente   = $objRequest->get('intIdCliente');
        $intIdFormaPago = $objRequest->get('intIdFormaPago');       
        $strEmpresaCod  = $objSession->get('idEmpresa');        
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $emFinanciero     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayParametros  = array(); 
        $arrayPagos       =  array(); 
        $intContDet       = 0;
        $intIdPtoSelect   = 0;
        $intIdDocumento   = 0;
        $arrayRegistros  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                      ->findPtosPadreByEmpresaPorCliente($strEmpresaCod,$intIdCliente);        

        foreach($arrayRegistros as $arrayRegistro)
        {
            $intIdPtoSelect = $arrayRegistro['id'];
            $strLogin       = $arrayRegistro['login'];
            
            $arrayFacturas     = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->findFacturasPendientesxPunto($intIdPtoSelect);

            if(count($arrayFacturas) > 0)
            {
                foreach($arrayFacturas as $objFactura)
                {
                    $strNumeroFacturaSri = $objFactura->getNumeroFacturaSri();
                    if(!isset($strNumeroFacturaSri))
                    {
                        $strNumeroFacturaSri = $objFactura->getNumFactMigracion();
                    } 
                    $intIdDocumento = $objFactura->getId();
                    $objInfoDocumentoFinCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);

                    if(is_object($objInfoDocumentoFinCab))
                    {
                        $floatSaldoFactura = $objInfoDocumentoFinCab->getValorTotal();

                        $arrayParametros   = array('intIdDocumento' => $intIdDocumento, 'intReferenciaId' => '');

                        $arrayGetSaldoXFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                               ->getSaldosXFactura($arrayParametros);

                        if(!empty($arrayGetSaldoXFactura['strMessageError']))
                        {
                            throw new Exception('Error al obtener el saldo de factura: '. $objInfoDocumentoFinCab->getNumeroFacturaSri());
                        }
                        else
                        {
                            $floatSaldoFactura = number_format(floatval($arrayGetSaldoXFactura['intSaldo']), 2, '.', '');
                        }
                        $arrayPagos[] =   array('intIdPagDet'            => $intContDet,
                                                'intIdCliente'           => $intIdCliente,
                                                'intIdFormaPago'         => $intIdFormaPago,
                                                'intIdPunto'             => $intIdPtoSelect,
                                                'strLogin'               => $strLogin,
                                                'intIdDocumento'         => $intIdDocumento,
                                                'strFactura'             => $strNumeroFacturaSri,
                                                'strSaldo'               => $floatSaldoFactura,
                                                'strValor'               => $floatSaldoFactura
                                               );    
                        $intContDet++;
                    }                    
                }
            }
        } 

        $intTotal = count($arrayPagos);
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayPagos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


    /**
     * 
     * getPagosEstadoCtaAction()
     * Función que obtiene el listado de pagos asociados a un detalle de estado de cuenta procesado.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 19-05-2022
     *  
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 27-06-2022 Se realiza envio del id del pago asociado como parámetro.
     * @return $objResponse 
     */

    public function getPagosEstadoCtaAction()
    {
        $objRequest       = $this->getRequest();
        $intIdPagoAutDet  = $objRequest->get("intIdPagoAutomaticoDet");
        $strOpcion        = $objRequest->get("strOpcion");
        $emFinanciero     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayDetalles    = [];
        $arrayParametros  = [];        
        $arrayParametros['intIdPagAutDet'] = $intIdPagoAutDet;
        $arrayParametros['strOpcion']      = $strOpcion;
        $strUrlVerPago      = '';
        $strUrlImprimirPago = '';
        
        $arrayPagos    = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->getPagosPorDetallePagoAutId($arrayParametros);

        foreach($arrayPagos as $arrayDetalle):
            
            if ($arrayDetalle['tipoDocumento']=='ANT')
            {
                $strUrlVerPago = $this->generateUrl('anticipo_show', array('id' => $arrayDetalle['idPago']));
            }
            elseif($arrayDetalle['tipoDocumento']=='PAG')
            {
                $strUrlVerPago = $this->generateUrl('infoPagoAutomatico_show', array('id' => intval($arrayDetalle['idPago'])));
            }
                    
            $strUrlImprimirPago = $this->generateUrl('infoPagoAutomatico_recibo', array('id' => intval($arrayDetalle['idPago'])));

            $arrayDetalles[] = array('intIdPago'         => $arrayDetalle['idPago'],
                                     'strTipo'           => $arrayDetalle['tipoDocumento'],
                                     'strOficina'        => $arrayDetalle['oficina'],
                                     'strNumeroPago'     => $arrayDetalle['numeroPago'],
                                     'strLogin'          => $arrayDetalle['login'],
                                     'strTotal'          => $arrayDetalle['valorTotal'],
                                     'strFecha'          => strval(date_format($arrayDetalle['fechaCreacion'], "d/m/Y G:i")),
                                     'strUser'           => $arrayDetalle['usrCreacion'],
                                     'strEstado'         => $arrayDetalle['estadoPago'],
                                     'strOpAcciones'     => array('strUrlVerPago'      => $strUrlVerPago,
                                                                  'strUrlImprimirPago' => $strUrlImprimirPago,
                                                                  'intIdPago'          => $arrayDetalle['idPago']));                 
           
        endforeach;

        $intTotal    = count($arrayDetalles);    
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayDetalles)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } 
    
   /**
     * @Secure(roles="ROLE_65-7457")
     * 
     * notificaPagoAction()
     * Función que invoca proceso para envío de notificación de pago .
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-06-2022
     * @since 1.0
     *
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.1 24-08-2022  -- Se agrega la validacion de esNotificado para cambio de estado a S     
     *   
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.2 11-11-2022  -- Se agrega la insercion de datos en la tabla historial para el mantenimiento de la misma 
     *      
     * @return $objResponse 
     */

    public function notificaPagoAction()
    {
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();      
        $intEmpresaId          = $objSession->get('idEmpresa');
        $intOficinaId          = $objSession->get('idOficina');
        $strUsrSesion          = $objSession->get('user');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');         
        $intIdPagoAutDet       = $objRequest->get('idPagoAutomaticoDet');  
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial           = $this->getDoctrine()->getManager();
        $serviceInfoPagAut     = $this->get('financiero.InfoPagoAutomatico');
        $floatSaldoPendiente   = 0;
        $floatValorTotal       = 0;
        $intIdPersonaEmpresaRol= null;
        $strNombreCliente      = '';
        $strTablaPagos         = '';
        

        $objInfoPagoAutDet = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                        ->findOneById($intIdPagoAutDet);        
        
        $objInfoPagoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                       ->findOneByDetallePagoAutomaticoId($intIdPagoAutDet);
        
        $arrayInfoPagos = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                       ->findByDetallePagoAutomaticoId($intIdPagoAutDet);        
        
        if(is_object($objInfoPagoCab))
        {
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                    ->find($objInfoPagoCab->getPuntoId());
            
            $objInfoPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();

            if(is_object($objInfoPersonaEmpresaRol))
            {
                $intIdPersonaEmpresaRol = $objInfoPersonaEmpresaRol->getId();
                $objInfoPersona         = $objInfoPersonaEmpresaRol->getPersonaId();
                if(is_object($objInfoPersona))
                {
                    $strNombreCliente = $objInfoPersona->getRazonSocial() ? $objInfoPersona->getRazonSocial() : $objInfoPersona->getNombres() . ' ' .
                        $objInfoPersona->getApellidos();
                }

                $arraySaldoPendiente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $intIdPersonaEmpresaRol,
                                                                     "strPrefijoEmpresa"     => $strPrefijoEmpresa));

                if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                  !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                {
                    $floatSaldoPendiente = $arraySaldoPendiente["floatSaldoPendiente"];
                }
            }
        }
        
        foreach($arrayInfoPagos as $objInfoPago)
        {
            $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                    ->find($objInfoPago->getPuntoId());            
            $arrayInfoPagosDet = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                              ->findByPagoId($objInfoPago->getId());             
            foreach($arrayInfoPagosDet as $objInfoPagoDet)
            {
                if($objInfoPagoDet->getEstado()!='Anulado'&&$objInfoPagoDet->getEstado()!='Pendiente'&&$objInfoPagoDet->getEstado()!='')
                {    
                    $objInfoDocFinanCab = $emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->find($objInfoPagoDet->getReferenciaId());

                    $strTablaPagos .= '<tr><td>'.$objInfoPunto->getLogin().'</td><td>'.
                        strval(date_format($objInfoDocFinanCab->getFeEmision(), "Y-m-d")).
                        '</td><td>'.$objInfoDocFinanCab->getNumeroFacturaSri().'</td><td>'.
                        strval(round($objInfoPagoDet->getValorPago(),2)).'</td></tr>';
                    $floatValorTotal += round($objInfoPagoDet->getValorPago(),2);
                }
                if($objInfoPagoDet->getEstado()=='Pendiente')
                {
                    $strTablaPagos .= '<tr><td>'.$objInfoPunto->getLogin().'</td><td>'.
                    strval(date_format($objInfoDocFinanCab->getFeEmision(), "Y-m-d")).
                    '</td><td></td><td>'.
                    strval(round($objInfoPagoDet->getValorPago(),2)).'</td></tr>';
                $floatValorTotal += round($objInfoPagoDet->getValorPago(),2);
                }
            }
        }

        
        $arrayParamNotificacion                           = [];
        $arrayParamNotificacion['boolNotManual']          = true;        
        $arrayParamNotificacion['strCodEmpresa']          = $intEmpresaId;
        $arrayParamNotificacion['strPrefijoEmpresa']      = $strPrefijoEmpresa;
        $arrayParamNotificacion['strNombreCliente']       = $strNombreCliente;
        $arrayParamNotificacion['strSaldoCliente']        = $floatSaldoPendiente;
        $arrayParamNotificacion['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
        $arrayParamNotificacion['strClientIp']            = $objRequest->getClientIp();
        $arrayParamNotificacion['strCodigoPlantilla']     = 'NOT_PAG_MANUAL';
        $arrayParamNotificacion['strModulo']              = 'FINANCIERO';
        $arrayParamNotificacion['strNombreParametro']     = 'AUTOMATIZACION PAGOS';
        $arrayParamNotificacion['strParametroDet']        = 'NOTIFICA_PAG_AUT';
        $arrayParamNotificacion['strTablaPagos']          = $strTablaPagos;
        $arrayParamNotificacion['floatValorTotal']        = $floatValorTotal;
        $arrayParamNotificacion['strMensaje']             = ''; 
        $arrayParamNotificacion['intOficinaId']           = $intOficinaId;
        $arrayParamNotificacion['idPersonaEmpresaRol']    = $objSession->get('idPersonaEmpresaRol');

        $strRespuesta = $serviceInfoPagAut->notificaPagoAutomatico($arrayParamNotificacion);

        if(is_object($objInfoPagoAutDet) && $objInfoPagoAutDet->getEsNotificado()!='S')
        {
            $objInfoPagoAutDet->setEsNotificado('S');
            $emFinanciero->persist($objInfoPagoAutDet);
            $emFinanciero->flush();    

            //Graba historial de detalle de estado de cuenta.
            $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
            $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutDet);
            $objInfoPagoAutomaticoHist->setEstado('Procesado');
            $objInfoPagoAutomaticoHist->setObservacion('Se envia correo manual a cliente.');
            $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
            $objInfoPagoAutomaticoHist->setUsrCreacion($strUsrSesion);
            $emFinanciero->persist($objInfoPagoAutomaticoHist);
            $emFinanciero->flush();                   

        }
        else
        {
            //Graba historial de detalle de estado de cuenta.
            $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
            $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutDet);
            $objInfoPagoAutomaticoHist->setEstado('Procesado');
            $objInfoPagoAutomaticoHist->setObservacion('Se reenvia correo manual a cliente.');
            $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
            $objInfoPagoAutomaticoHist->setUsrCreacion($strUsrSesion);
            $emFinanciero->persist($objInfoPagoAutomaticoHist);
            $emFinanciero->flush();   
        }

        $objResponse = new Response(json_encode(array('strRespuesta' => $strRespuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;        
       
    }
    
    /**
     * getMaxNumDetallesAction()
     * Función que permite obtener información del parámetro asociado al nnúmero maximo de detalles permitidos al crear un pago automático manual.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 16-06-2022
     * 
     * @return objResponse
     */  
    public function getMaxNumDetallesAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();      
        $strCodEmpresa          = $objSession->get('idEmpresa');       
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');      
        $strNombreParametro     = 'AUTOMATIZACION PAGOS';
        $strModulo              = 'FINANCIERO';
        $strParametroDet        = 'NUM_MAX_DET_PAGAUT';
        $arrayParametrosDet     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne($strNombreParametro,
                                                     $strModulo, 
                                                     '', '', $strParametroDet, '', '', '', '',$strCodEmpresa);

        if (isset($arrayParametrosDet['valor2']))
        {
            $intCantMaxRegistros = $arrayParametrosDet['valor2'];
        }
       $objResponse = new Response(json_encode(array('intCantMaxRegistros' => $intCantMaxRegistros)));
       $objResponse->headers->set('Content-type', 'text/json');
       return $objResponse;
    }    

    
    /**
     * 
     * getHistorialEstadoCtaAction()
     * Función que obtiene el listado del Historial asociados a un detalle de estado de cuenta procesado.
     * 
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.0 11-11-2022
     * @return $objResponse 
     */

    public function getHistorialEstadoCtaAction()
    {
        $objRequest       = $this->getRequest();
        $intIdPagoAutDet  = $objRequest->get("intIdPagoAutomaticoDet");
        $strOpcion        = $objRequest->get("strOpcion");
        $emFinanciero     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayDetalles    = [];
        $strUrlVerPago      = '';
        $strUrlImprimirPago = '';
        
        $arrayHitorialPago    = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoHist')
                        ->findBy(array('detallePagoAutomaticoId'=> $intIdPagoAutDet),
                                array('feCreacion' => 'ASC'));

        foreach($arrayHitorialPago as $arrayDetalle):
            
            $arrayDetalles[] = array('intIdHitorialPago'    => $arrayDetalle->getId(),
                                     'strObservacion'       => $arrayDetalle->getObservacion(),
                                     'strFecha'             => strval(date_format($arrayDetalle->getFeCreacion(), "d/m/Y G:i")),
                                     'strUser'              => $arrayDetalle->getUsrCreacion(),
                                     'strEstado'            => $arrayDetalle->getEstado());
           
        endforeach;

        $intTotal    = count($arrayDetalles);    
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayDetalles)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } 
    
}
