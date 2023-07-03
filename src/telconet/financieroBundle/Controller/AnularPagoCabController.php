<?php

namespace telconet\financieroBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Form\InfoPagoCabType;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;

use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Form\InfoPagoDetType;
use telconet\schemaBundle\Form\InfoRecaudacionType;
use telconet\financieroBundle\Controller\InfoPagoDetController;
use telconet\tecnicoBundle\Controller\InfoServicioController;
use telconet\contabilizacionesBundle\Controller\PagosController;
use telconet\contabilizacionesBundle\Controller\AnticiposController;
use telconet\contabilizacionesBundle\Controller\AnticiposSinClienteController;
use telconet\financieroBundle\Controller\AdmiFormatoDebitoController;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoRecaudacion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\financieroBundle\Service\InfoPagoService;
use telconet\financieroBundle\Service\InfoPagoDetService;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\ReturnResponse;

/**
 * AnularPagoCab controller.
 * 
 * 
 * @package    financieroBundle
 * @subpackage Controller
 * @author     Wilson Quinto <wquinto@telconet.ec>
 */
class AnularPagoCabController extends Controller implements TokenAuthenticatedController
{

    /**
     * 
     * Lists all AnularPagoCab entities.
     *
     * @return render
     */
    public function indexAnulacionPagosAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $intIdEmpresa = $objSession->get('idEmpresa');
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $arrayCanalPagosEnlinea=$emFinanciero->getRepository('schemaBundle:AdmiCanalPagoLinea')->findBy(array('estadoCanalPagoLinea' => 'Activo'));
        $arrayTipoDocumentos = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findBy(array('estado' => 'Activo'));
        $arrayFormaPago = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->findBy(array('esMonetario' => 'S','estado' => 'Activo'));
        $arrayBancos = $emGeneral->getRepository('schemaBundle:AdmiBanco')->findBy(array('estado' => 'Activo'));
        $arrayCiclo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->findBy(array('empresaCod' => $intIdEmpresa));
        return $this->render('financieroBundle:Anularpagos:indexAnulacionpago.html.twig', array(
            'tipoDocumentos'=> $arrayTipoDocumentos,
            'tipoPagos'=> $arrayFormaPago,
            'bancos'=> $arrayBancos,
            'canalPagos'=> $arrayCanalPagosEnlinea,
            'ciclo'=> $arrayCiclo,
            'estadoPagos' => $this->estadoPagos()
        ));
    }
    /*
     * estadoPagos, valores de filtro de estado de pago
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function estadoPagos()
    {
        $arrayEstados[]= array('idEstado'=>'Asignado','descripcion'=> 'Asignado');
        $arrayEstados[]= array('idEstado'=>'Pendiente','descripcion'=> 'Pendiente');
        $arrayEstados[]= array('idEstado'=>'Cerrado','descripcion'=> 'Cerrado');
        
		return $arrayEstados;
		
    }

     /*
     * parametrosPaginaAction, valores de parametros configurables
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function parametrosPaginaAction()
    {
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
        ->findOneBy(array('nombreParametro'   => 'PARAM_ANULACION_PAGOS',
                           'estado'            => 'Activo') );
        $arrayParametros =$emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->findBy( array( 'parametroId' => $objAdmiParametroCab->getId(),
                        'valor1'=> 'PAGINA',
                         'estado'      => 'Activo' ) );
        $arrayParametrosMap=array();

        foreach($arrayParametros as $parametro)
        {
            $arrayParametrosMap[strtolower($parametro->getValor2())] =$parametro->getValor3();
        }

        $objResult=array("error"=>false,"msg"=>"OK","parametros"=>$arrayParametrosMap);
        return $this->resultResponse($objResult);
    }

    /*
     * obtenerListaPagoAction, obtener lista de pagos por filtros
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function obtenerListaPagoAction()
    {
        ini_set('max_execution_time', 600000);
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $intIdEmpresa = $objSession->get('idEmpresa');
        $arrayFeDesde = explode('T', $objRequest->get("fecha_pago_desde"));
        $arrayFeHasta = explode('T', $objRequest->get("fecha_pago_hasta"));
        $strLogin = $objRequest->get('login');
        $strUsuCreacion = $objRequest->get('usu_creacion');
        $strNumPago = $objRequest->get('num_pago');
        $arrayTipoPago = $objRequest->get('tipo_pagos');
        $arrayTipoDocumento = $objRequest->get('tipo_documentos');
        $arrayCanalPago = $objRequest->get('canal_pagos');
        $arrayEstadoPago = $objRequest->get('estado_pagos');
        $arrayBanco= $objRequest->get('bancos');
        $arrayCiclo = $objRequest->get('c_facturacion');

        $arrayFiltro = array();
        $arrayFiltro['idEmpresa'] =$intIdEmpresa;
        $arrayFiltro['login'] =  $strLogin;
        $arrayFiltro['usuCreacion'] =  $strUsuCreacion;
        $arrayFiltro['numeroPago'] =  $strNumPago;
        $arrayFiltro['tipoPago'] = $arrayTipoPago;
        $arrayFiltro['tipoDocumento'] = $arrayTipoDocumento;
        $arrayFiltro['canalPago'] = $arrayCanalPago;
        $arrayFiltro['ciclo'] = $arrayCiclo;
        $arrayFiltro['banco'] = $arrayBanco;
        if(!is_null($arrayEstadoPago))
        {
            $arrayFiltro['estado'] = $arrayEstadoPago;
        }else
        {
            $arrayFiltro['estado'] = array('Asignado','Pendiente','Cerrado');
        }
        $arrayFiltro['fechaInicio'] = $arrayFeDesde[0];
        $arrayFiltro['fechaFin'] = $arrayFeHasta[0];

        if((empty($arrayFiltro['fechaInicio']) || empty($arrayFiltro['fechaFin'])) 
        && empty($arrayFiltro['numeroPago']))
        {
            $objResult=array("error"=>true,"msg"=>"Se debe especificar filtro por rango de fecha o numero de pago");
        }else
        {   
            $objResult=$emFinanciero->getRepository('schemaBundle:InfoPagoCab')->getListPayment($arrayFiltro);
        }
        

		return $this->resultResponse($objResult);

    }

    /*
     * anularPagosAction, almacenar pagos a anular
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function anularPagosAction()
    {
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest = $this->getRequest();
        $strIp = $objRequest->getClientIp();
        $objSession = $objRequest->getSession();
        $strIdEmpresa = $objSession->get('idEmpresa');
        $strUser = $objSession->get('user');
       
        $strRawRequest = file_get_contents('php://input');
        $arrayJson=json_decode($strRawRequest,true);
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {
            //Escribo en INFO_PROCESO_MASIVO_CAB
            $entityProcesoMasivoCab = new InfoProcesoMasivoCab();
            $entityProcesoMasivoCab->setTipoProceso("AnulacionPagoCliente");
            $entityProcesoMasivoCab->setEmpresaCod($strIdEmpresa); //EMPRESA_COD
            $entityProcesoMasivoCab->setEstado("Pendiente");
            $entityProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $entityProcesoMasivoCab->setUsrCreacion($strUser);
            $entityProcesoMasivoCab->setIpCreacion($strIp);
            $emInfraestructura->persist($entityProcesoMasivoCab);
            $emInfraestructura->flush();
            foreach($arrayJson["pagos"] as $objPago)
            {
                //Escribo en INFO_PROCESO_MASIVO_DET
                $entityProcesoMasivoDet = new InfoProcesoMasivoDet();
                $entityProcesoMasivoDet->setProcesoMasivoCabId($entityProcesoMasivoCab);
                $entityProcesoMasivoDet->setPagoId($objPago["id"]);
                $entityProcesoMasivoDet->setEstado("Pendiente");
                $intPuntoId=$objPago["puntoId"]==null?0:$objPago["puntoId"];
                $entityProcesoMasivoDet->setPuntoId($intPuntoId);
                $entityProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $entityProcesoMasivoDet->setUsrCreacion($strUser);
                $emInfraestructura->persist($entityProcesoMasivoDet);
                $emInfraestructura->flush();
            }
            //guardar todos los cambios
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->flush();
                $emInfraestructura->getConnection()->commit();
                $emInfraestructura->getConnection()->close();
            }
        }catch(Exception $ex)
        {
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
            return $this->resultResponse(array("error"=>true,"msg"=>$ex->getMessage()));
        }
        return $this->resultResponse(array("error"=>false,"msg"=>"Transaccion realizada")); 
    }

    /*
     * listaPagoArchivoExcelAction, obtener lista de pago de un excel
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function listaPagoArchivoExcelAction()
    {

        $objRequest              = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $strUser = $objSession->get('user');



        $objValidFile=$this->validFileExcel($_FILES['archivoPagoExcel']);
        if($objValidFile['error'])
        {
            return $this->resultResponse($objValidFile);
        }

        $arrayFiltro = array();
        $arrayFiltro['usrCreacion'] =$strUser;
        $arrayFiltro['idEmpresa'] =$intIdEmpresa;
        $arrayFiltro['prefijoEmpres'] =$strPrefijoEmpresa;
        $arrayFiltro['nombreArchivo'] =$_FILES['archivoPagoExcel']['name'];
        $objFileData=$this->getValueRowExcel($_FILES['archivoPagoExcel'],$arrayFiltro);
		return $this->resultResponse($objFileData);
    }

    /*
     * getValueRowExcel, obtener datos de excel
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function getValueRowExcel($objFile,$arrayFiltro)
    {
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayResponse=array();
        $strInputFile = $objFile['tmp_name'];
        $arrayArchivo     = explode('.', $arrayFiltro['nombreArchivo']);
        $intCountArray       = count($arrayArchivo);
        $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
        $strPrefijo       = substr(uniqid(rand()), 0, 6);
        $strFechaActual = strval(date_format(new \DateTime('now'), "dmYGi"));

        $strNuevoNombre = $strPrefijo ."_".$strFechaActual. "." . $strExtArchivo;

        $strData = file_get_contents( $strInputFile );

        try 
        {
            //inicializacion de objeto de lectura de excel
            $objInputFileType = PHPExcel_IOFactory::identify($strInputFile);
            $objReader = PHPExcel_IOFactory::createReader($objInputFileType);
            $objPHPExcel = $objReader->load($strInputFile);
        } catch(Exception $e) 
        {
            return array("error"=>true,"msg"=>"Error en lectura de archivo");
        }
        //Obtener dimension de archivo
        $objSheet = $objPHPExcel->getSheet(0); 
           
        $strHeaderNum = $objSheet->getCellByColumnAndRow(0, 1)->getValue();
        $strHeaderVal = $objSheet->getCellByColumnAndRow(1, 1)->getValue();
        if(strtoupper($strHeaderNum)!='NUMERO PAGO' || strtoupper($strHeaderVal)!='VALOR')
        {
                return array("error"=>true,"msg"=>"La cabecera del archivo es incorrecta");
        }else
        {
            $arrayParamNfs = array(
                'prefijoEmpresa'       => 'MD',
                'strApp'               => 'TelcosWeb',
                'strSubModulo'         => 'AnularPago',
                'arrayPathAdicional'   => [],
                'strBase64'            =>  base64_encode($strData),
                'strNombreArchivo'     => $strNuevoNombre,
                'strUsrCreacion'       => $arrayFiltro['usrCreacion']);
    
            $objServiceUtil     = $this->get('schema.Util');
            $arrayRespNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);
            if($arrayRespNfs['intStatus']==200 )
            {
                $arrayFiltro['urlFile'] =$arrayRespNfs['strUrlArchivo'];
                
                $arrayResponse  =$emFinanciero->getRepository ( 'schemaBundle:InfoPagoCab' )
                                        ->getListPaymentExcel($arrayFiltro);
            }else
            {
                return array("error"=>true,"msg"=>"No se puede almacenar archivo para realizar la busqueda, verifique configuracion");
            }
            
        }
       return $arrayResponse;
    }

    /*
     * validFileExcel, verifica validez de excel
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function validFileExcel($objFile)
    {
        if(isset($objFile) && $objFile['tmp_name'])
        {
            if(!$objFile['error'])
            {
                $strExtension = strtoupper(pathinfo($objFile['name'], PATHINFO_EXTENSION));
                //Validacion de extension de archivo
                if($strExtension != 'XLSX')
                {
                    return array("error"=>true,"msg"=>"Archivo subido no es un excel valido");
                }
            }else
            {
                return array("error"=>true,"msg"=>"Error en carga de archivo");
            }
        }
        return array("error"=>false,"msg"=>"archivo valido");
    }

    /*
     * resultResponse, respuesta generia de consultas
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function resultResponse($objResult)
    {
        $objResponse = new Response(json_encode($objResult));
		$objResponse->headers->set('Content-type', 'text/json');
		return $objResponse;
    }

    /*
     * validField, valida campos vacios o nulos
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 16-04-2015
     * @since 1.0
     */
    public function validField($strField)
    {
        if(empty($strField) || is_null($strField))
        {
            return true;
        }
        return false;
    }
    
    
}
