<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\soporteBundle\Service\SoporteService;
use \PHPExcel_IOFactory;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Alignment;
use \PHPExcel;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Border;
use \PHPExcel_Worksheet_MemoryDrawing;

class ReporteSlaController extends Controller implements TokenAuthenticatedController
{

    /**
     * 
     * Metodo que redirecciona al index de la pantalla de calculo del Sla del cliente
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 11-12-2015
     * 
     * @Secure(roles="ROLE_318-1")
     */
    public function indexAction()
    {        
        return $this->render('soporteBundle:ReporteSla:index.html.twig', array());
    }
       
    
    /**
     * 
     * Metodo que obtiene los clientes seleccionados por filtro para calcular SLA
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 11-12-2015
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 20-01-2021 - Se agrega el filtro por identificación del cliente.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_318-7")
     */    
    public function ajaxGridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet");

        $peticion = $this->get('request');
        $session = $peticion->getSession();          
        
        $arrayParametros = Array();
        
        $arrayParametros['razonSocial'] = $peticion->query->get('razonSocial');
        $arrayParametros['nombres']     = $peticion->query->get('nombres');
        $arrayParametros['apellidos']   = $peticion->query->get('apellidos');
        $arrayParametros['identificacion'] = $peticion->query->get('identificacion');
        $arrayParametros['estado']      = $peticion->query->get('estado');
        $arrayParametros['producto']    = $peticion->query->get('producto');
        $arrayParametros['oficina']     = $peticion->query->get('oficina');
        $arrayParametros['empresa']     = $session->get('idEmpresa');
        $arrayParametros['start']       = $peticion->query->get('start');
        $arrayParametros['limit']       = $peticion->query->get('limit');

        $jsonResultado = $em->getRepository("schemaBundle:InfoPunto")->getJsonPuntosCalculoSla($arrayParametros);

        $respuesta->setContent($jsonResultado);
        return $respuesta;
    }
    
    /**     
     * Metodo que obtiene los productos activos
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 11-12-2015
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetProductosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $nombre = $peticion->query->get('query');        
        
        $jsonResultado = $em->getRepository("schemaBundle:AdmiProducto")->findProductoXEmpresa($nombre,$session->get('idEmpresa'));
        
        $respuesta->setContent($jsonResultado);
        
        return $respuesta;
    }
    
    /**
     * Metodo que obtiene los servicios escogidos por punto que fueron afectados en la incidencia
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 11-12-2015    
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetServiciosAfectadosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $peticion = $this->get('request');        
        
        $idServicio = $peticion->query->get('idServicio');        
        $idPunto    = $peticion->query->get('idPunto');        
        
        $jsonResultado = $em->getRepository("schemaBundle:AdmiProducto")->getJsonServiciosAfectadosSla($idPunto,$idServicio);
        
        $respuesta->setContent($jsonResultado);
        
        return $respuesta;
    }        
    
    /**
     * Metodo que obtiene la informacion general de casos, disponibilidad y resumen de los clientes a calcular Sla
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 17-12-2015  
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 07-09-2016 Cuando se seleccionen mas de 500 registros para el calculo del SLA, los puntos y servicios de la
     *                         Razon Social se obtendran internamente
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetInfoSlaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $arrayParametros    = array();
        $arrayParametrosSLA = array();
        
        $emSoporte   = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $rangoFechaDesde = explode('T',$peticion->query->get('rangoDesde'));        
        $rangoFechaHasta = explode('T',$peticion->query->get('rangoHasta'));     
                
        $arrayParametros['rangoDesde'] = $rangoFechaDesde ? $rangoFechaDesde[0] : 0;
        $arrayParametros['rangoHasta'] = $rangoFechaHasta ? $rangoFechaHasta[0] : 0;
        $arrayParametros['start']      = $peticion->query->get('start');
        $arrayParametros['limit']      = $peticion->query->get('limit');

        $strParams       = $peticion->query->get('params');
        
        $arrayPuntos        = array();
        $arrayServicios     = array();
        $strPuntosServicios = "";
        
        //Si la seleccion es mayor de 500 registros, se obtienen los puntos y servicios por interno
        if(substr($strParams,0,1) == "S")
        {
            $arrayParametrosSLA['razonSocial'] = $peticion->query->get('razonSocial');
            $arrayParametrosSLA['nombres']     = $peticion->query->get('nombres');
            $arrayParametrosSLA['apellidos']   = $peticion->query->get('apellidos');
            $arrayParametrosSLA['estado']      = $peticion->query->get('estado');
            $arrayParametrosSLA['producto']    = $peticion->query->get('producto');
            $arrayParametrosSLA['oficina']     = $peticion->query->get('oficina');
            $arrayParametrosSLA['start']       = $peticion->query->get('calculoIni');
            $arrayParametrosSLA['limit']       = $peticion->query->get('calculoFin');
            $arrayParametrosSLA['empresa']     = $session->get('idEmpresa');

            $arrayResultado = $emComercial->getRepository("schemaBundle:InfoPunto")->getPuntosCalculoSla($arrayParametrosSLA);
            $arrayResultado = $arrayResultado['resultado'];

            if($arrayResultado)
            {
                foreach($arrayResultado as $arrayData)
                {
                    $strServicio        = $arrayData['idServicio']?$arrayData['idServicio']:"0";
                    $strPuntosServicios = $strPuntosServicios . $arrayData['idPunto'] . "-" . $strServicio . "|";
                }
            }
            $strParams = $strPuntosServicios;
        }

        $arrayData = explode("|",$strParams);
        
        foreach($arrayData  as $param)
        {
            if($param != '')
            {
                $arrayParams = explode('-',$param);
                $arrayPuntos[]    = $arrayParams[0];
                $arrayServicios[] = $arrayParams[1];
            }
        }

        $arrayParametros['puntos']    = $arrayPuntos;
        $arrayParametros['servicios'] = $arrayServicios;         
        $arrayParametros['service']   = $this->get('soporte.SoporteService');
        $arrayParametros['accion']    = $peticion->query->get('accion');
        
        $jsonResultado = $emSoporte->getRepository("schemaBundle:InfoCaso")->getJsonInfoSla($arrayParametros);
     
        $respuesta->setContent($jsonResultado);
        
        return $respuesta;
    }
        
    /**
     * Metodo que se encarga de exportar los reportes SLA detallados y consolidados de los clientes seleccionados
     * 
     * @author Allan Suarez <arsuarerz@telconet.ec>
     * @version 1.0
     * @since 19-12-2015  
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 07-09-2016 Cuando se seleccionen mas de 500 registros para el calculo del SLA, los puntos y servicios de la
     *                         Razon Social se obtendran internamente
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 21-01-2021 - Se modifica el método aplicando los estandares de calidad y se agrega el llamado
     *                           al nuevo proceso que genera el reporte en la base de datos y es enviado por correo.
     */
    public function descargarSlaAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $objServiceUtil     = $this->get('schema.Util');
        $strIpUsuario       = $objRequest->getClientIp();
        $strUsuario         = $objSession->get('user');
        $strTipo            = $objRequest->get('tipo');
        $strIdEmpresa       = $objSession->get('idEmpresa');
        $strParams          = $objRequest->get('params');
        $strVersionOficial  = $objRequest->get('versionOficial');
        $strGeneracionTotal = $objRequest->get('generacionTotal');
        $objFechaDesde      = new \DateTime($objRequest->get('rangoDesde'));
        $objFechaHasta      = new \DateTime($objRequest->get('rangoHasta'));
        $strProducto        = $objRequest->get('producto') != "" && $objRequest->get('producto') != "null" ?
                              $objRequest->get('producto') : "";
        $strOficina         = $objRequest->get('oficina') != "" && $objRequest->get('oficina')  != "null" ?
                              $objRequest->get('oficina') : "";

        try
        {
            //Verificamos que el usuario no tenga el proceso de reporte ejecutandose.
            $strNombreJob   = substr('JOB_SLA_'.strtoupper($strTipo).'_'.strtoupper($strUsuario),0,30);
            $arrayResultJob = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->existeJobReporteTarea(array ('strNombreJob' => $strNombreJob));

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

            $arrayParametros = array();
            $arrayParametros['start']          = $objRequest->get('calculoIni');
            $arrayParametros['limit']          = $objRequest->get('calculoFin');
            $arrayParametros['razonSocial']    = $objRequest->get('razonSocial');
            $arrayParametros['nombres']        = $objRequest->get('nombres');
            $arrayParametros['apellidos']      = $objRequest->get('apellidos');
            $arrayParametros['identificacion'] = $objRequest->get('identificacion');
            $arrayParametros['estado']         = $objRequest->get('estado');
            $arrayParametros['empresa']        = $strIdEmpresa;
            $arrayParametros['producto']       = $strProducto;
            $arrayParametros['oficina']        = $strOficina;

            //Si la selección es mayor de 500 registros, se obtienen los puntos y servicios por interno.
            if (substr($strParams,0,1) == "S")
            {
                $arrayResultado = $emComercial->getRepository("schemaBundle:InfoPunto")
                        ->getPuntosCalculoSla($arrayParametros);

                if ($arrayResultado['resultado'])
                {
                    foreach ($arrayResultado['resultado'] as $arrayData)
                    {
                        $strServicio        = $arrayData['idServicio'] ? $arrayData['idServicio'] : "0";
                        $strPuntosServicios = $strPuntosServicios.$arrayData['idPunto']."-".$strServicio."|";
                    }
                }

                $strParams = $strPuntosServicios;
            }

            $arrayPuntos = array();
            $arrayData   = explode("|",$strParams);
            foreach ($arrayData  as $strParam)
            {
                if ($strParam != '')
                {
                    $arrayParams   = explode('-',$strParam);
                    $arrayPuntos[] = $arrayParams[0];
                }
            }

            $strPuntos = implode(",", $arrayPuntos);
            unset($arrayParametros['start']);
            unset($arrayParametros['limit']);
            unset($arrayParametros['estado']);

            $arrayParametros['tipo']              =  $strTipo;
            $arrayParametros['nombreJob']         =  $strNombreJob;
            $arrayParametros['rangoDesde']        =  $objFechaDesde->format('Y-m-d');
            $arrayParametros['rangoHasta']        =  $objFechaHasta->format('Y-m-d');
            $arrayParametros['versionOficial']    =  $strVersionOficial == 'true';
            $arrayParametros['usuario']           =  $strUsuario;
            $arrayParametros['ipUsuario']         =  $strIpUsuario;
            $arrayParametros['generacionTotal']   =  $strGeneracionTotal == 'true';
            $arrayParametros['tipoRol']           = 'Cliente';
            $arrayParametros['estadoPunto']       =  $objRequest->get('estado');
            $arrayParametros['cliente']           =  $objRequest->get('cliente');
            $arrayParametros['tipoAfectado']      = 'Cliente';
            $arrayParametros['estadoCaso']        = 'Cerrado';
            $arrayParametros['tipoReporte']       = 'consolidado';
            $arrayParametros['tipoAfectacion']    = 'CAIDA';
            $arrayParametros['sinTipoAfectacion'] = 'SINAFECTACION';
            $arrayParametros['puntos']            =  $strPuntos;

            //Método que realiza el reporte en la base de datos.
            $arrayReporteSla = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->jobReporteSla($arrayParametros);

            if (!$arrayReporteSla['status'])
            {
                throw new \Exception($arrayReporteSla['message']);
            }

            $objResponse->setData(array('status'  =>  true,
                                        'message' => 'En breves minutos llegará el reporte a su correo.'));
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al generar el reporte sla. Si el problema persiste comunique a Sistemas.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            if (strlen($objException->getMessage()) > 4000) 
            {
                $arrayMensaje = explode("':", $objException->getMessage());
                
                foreach ($arrayMensaje as $key => $value) 
                {
                    if (strlen($value) < 4000) 
                    {
                        $objServiceUtil->insertError('Telcos+',
                                         'ReporteSlaController->descargarSlaAction',
                                          $value,
                                          $strUsuario,
                                          $strIpUsuario);
                    }
                } 
            }else
            {
                $objServiceUtil->insertError('Telcos+',
                                         'ReporteSlaController->descargarSlaAction',
                                          $objException->getMessage(),
                                          $strUsuario,
                                          $strIpUsuario);
            }

            $objResponse->setData(array('status' => false,'message' => $strMessage));
        }
        return $objResponse;
    }

    /**
     * Metodo encargado de exportar el reporte SLA Consolidado de todos los clientes consultados
     * 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 19-12-2015
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 20-05-2019 - Se agrega el logo actual de telconet.
     *
     * @param type $resultado
     * @param type $arrayParametros
     */
    public function exportarReporteSlaConsolidado($resultado, $arrayParametros)
    {
        try
        {
            $objPHPExcel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '1024MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel->getProperties()->setCreator("TELCOS++");
            $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros['usuario']);
            $objPHPExcel->getProperties()->setSubject("Reporte SLA Consolidado");
            $objPHPExcel->getProperties()->setDescription("Reporte SLA Consolidado");
            $objPHPExcel->getProperties()->setKeywords("SLA");
            $objPHPExcel->getProperties()->setCategory("Reporte");

            /* @var $soporteService SoporteService */
            $soporteService = $this->get('soporte.SoporteService');

            //Crea estilo para el titulo del reporte
            $arrayStyleTitulo = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 12,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $arrayStyleMensajes = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            );

            //Crea estilo para la cabecera del reporte
            $arrayStyleCabecera = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '888888')
                )
            );

            //Crea estilo para el cuerpo del reporte
            $arrayStyleBodyTable = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $strNombreImagen = 'logo-tn.jpg';
            $strPath         = $this->get('kernel')->getRootDir() . '/../web/public/images/'.$strNombreImagen;

            $login = " ";
            $columnBegin = 3;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $columnBegin, 'Punto Sucursal')
                ->setCellValue('B' . $columnBegin, 'Login')
                ->setCellValue('C' . $columnBegin, 'Porcentaje de Disponibilidad (%)')
                ->setCellValue('D' . $columnBegin, 'Minutos Total de Tickets')
                ->setCellValue('E' . $columnBegin, 'Casos');

            $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->applyFromArray($arrayStyleCabecera);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);

            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte SLA Consolidado');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
            $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Cliente : ' . $arrayParametros['cliente']);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($arrayStyleTitulo);

            $objImage = imagecreatefromjpeg($strPath);

            //Si obtiene la imagen la crea en la celda A1
            if($objImage)
            {
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('TELCOS++');
                $objDrawing->setDescription('TELCOS++');
                $objDrawing->setImageResource($objImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(200);
                $objDrawing->setWidth(80);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            //Se carga la Data en las celdas
            $columnBegin ++;

            foreach($resultado as $data)
            {

                $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':E' . $columnBegin)->applyFromArray($arrayStyleBodyTable);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':E' . $columnBegin)
                    ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($columnBegin)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, $data['puntoDisponibilidad']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $columnBegin, $soporteService->
                        completarDecimalesPorcentajes($data['porcentajeDisponibilidad']));
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $columnBegin, $data['loginDisponibilidad']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $columnBegin, $data['minutosTotalDisponibilidad']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $columnBegin, $data['casos']);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

                $columnBegin++;
            }

            $columnBegin++;
            
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $columnBegin . ':D' . $columnBegin);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin)->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, 'Porcentaje promedio del "uptime" desde ' . 
                $arrayParametros['rangoDesde'] . ' hasta ' . $arrayParametros['rangoHasta']);
            
            $columnBegin++;
            
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $columnBegin . ':D' . $columnBegin);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin)->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, 'Para poder ver el detalle de los puntos, por favor descargar el '
            . 'Reporte de SLA Detallado.');
            
            $columnBegin++;
            
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $columnBegin . ':D' . $columnBegin);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin)->applyFromArray($arrayStyleMensajes);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, 'Por favor cualquier duda comunicarse al '
                . 'departamento IPCC 3900111 ext. 8000');
                      
            $objPHPExcel->getActiveSheet()->setTitle($login);

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Reporte_SLA_' .
                str_replace(" ", "_", $arrayParametros['cliente']) . '_de_' . $arrayParametros['rangoDesde'] . '_al_' . 
                $arrayParametros['rangoHasta'] . 'CONSOLIDADO.xls"');
            
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        catch(\Exception $ex)
        {
            error_log($ex);
            exit;
        }
    }

    /**
     * Metodo encargado de exportar el reporte SLA Detallado de todos los clientes consultados, se genera un worksheet por cada
     * login analizado
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 20-05-2019 - Se agrega el logo actual de telconet.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 01-03-2017 Se realizan ajustes para mostrar la Versión Oficial de los casos.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 16-10-2016 Se realizan ajustes en el formato del excel , porque se estan agregando un campo para identificar
     *                         el tiempo de enlace. Adicional se esta agregando el nombre del punto.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 Se realiza ajustes para obtener el % de disponibilidad de un servicio, desde el calculo del consolidado.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se realiza truncado en el nombre a mostrar en cada title/pestaña de hoja de excel con informacion, se trunca a 30 caracteres
     * @since 29-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 19-12-2015
     * 
     * @param type $resultado
     * @param type $arrayParametros
     */
    public function exportarReporteSlaDetallado($arrayParametros)
    {
        try{
            $objPHPExcel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '1024MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $emComercial = $this->getDoctrine()->getManager("telconet");

            $objPHPExcel->getProperties()->setCreator("TELCOS++");
            $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros['usuario']);            
            $objPHPExcel->getProperties()->setSubject("Reporte SLA Detallado");
            $objPHPExcel->getProperties()->setDescription("Reporte SLA Detallado");
            $objPHPExcel->getProperties()->setKeywords("SLA");
            $objPHPExcel->getProperties()->setCategory("Reporte");
            
            /* @var $soporteService SoporteService */
            $soporteService = $this->get('soporte.SoporteService');

            //Crea estilo para el titulo del reporte
            $arrayStyleTitulo = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 12,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $arrayStyleMensajes = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            );

            //Crea estilo para la cabecera del reporte
            $arrayStyleCabecera = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '888888')
                )
            );

            //Crea estilo para el cuerpo del reporte
            $arrayStyleBodyTable = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $strNombreImagen = 'logo-tn.jpg';
            $strPath         = $this->get('kernel')->getRootDir() . '/../web/public/images/'.$strNombreImagen;
                        
            $i=0;            
            
            $versionOficial = $arrayParametros['versionOficial']=='true'?true:false;

            if($arrayParametros)
            {
                $arrayDisponibilidad = $arrayParametros['em']->getRepository("schemaBundle:InfoCaso")
                                                             ->getDisponibilidadClientesSla($arrayParametros);
            }
            $strNombrePunto = "";
            $strLogin       = "";
            foreach($arrayParametros['puntos']  as $punto)
            {
                $arrayParametros['punto'] = $punto;

                //Obtener login del punto
                $objPunto  = $arrayParametros['em']->getRepository("schemaBundle:InfoPunto")->find($punto);

                if(is_object($objPunto))
                {
                    $strNombrePunto = $objPunto->getNombrePunto();
                    $strLogin       = $objPunto->getLogin();
                }

                //Consulta de la infor por cada cliente para generar cada sheet con la data consultada                
                $arrayResultado  = $arrayParametros['em']->getRepository("schemaBundle:InfoCaso")
                                                         ->getResultadoResumenDetalladoClientesSla($arrayParametros);               

                $resultado       = $arrayResultado['resultado'];
                $intContClientes = $arrayResultado['total'];
                
                $columnBegin = 4;
                                                
                $objPHPExcel->setActiveSheetIndex($i)
                    ->setCellValue('A' . $columnBegin, 'Fecha')
                    ->setCellValue('B' . $columnBegin, 'Uptime (%)')
                    ->setCellValue('C' . $columnBegin, 'Tiempo Incidencia (Min.)')
                    ->setCellValue('D' . $columnBegin, 'Inicio de Incidencia')
                    ->setCellValue('E' . $columnBegin, 'Fin de Incidencia')
                    ->setCellValue('F' . $columnBegin, 'Tipo Evento')
                    ->setCellValue('G' . $columnBegin, 'Numero Caso')
                    ->setCellValue('H' . $columnBegin, 'Login')
                    ->setCellValue('I' . $columnBegin, 'T. Enlace')
                    ->setCellValue('J' . $columnBegin, 'Servicios Afectados');

                $column = "J";
                if($versionOficial)
                {
                    $column = "K";
                    $objPHPExcel->setActiveSheetIndex($i)->setCellValue('K'.$columnBegin, 'Version Oficial');
                }                
                                
                $objPHPExcel->getActiveSheet()->getStyle('A4:' . $column . '4')->applyFromArray($arrayStyleCabecera);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->getStyle('A4:' . $column . '4')->getBorders()->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $column . '1');
                $objPHPExcel->getActiveSheet()->mergeCells('A2:' . $column . '2');
                $objPHPExcel->getActiveSheet()->mergeCells('A3:' . $column . '3');
                $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte SLA detallado');
                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
                $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Cliente : ' . $arrayParametros['cliente'].' - ');
                $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($arrayStyleTitulo);
                $objPHPExcel->getActiveSheet()->setCellValue('A3',$strNombrePunto);
                $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($arrayStyleTitulo);

                $objImage = imagecreatefromjpeg($strPath);
                
                //Si obtiene la imagen la crea en la celda A1
                if($objImage)
                {
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setName('TELCOS++');
                    $objDrawing->setDescription('TELCOS++');
                    $objDrawing->setImageResource($objImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(120);
                    $objDrawing->setWidth(138);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }

                //Se carga la Data en las celdas
                $columnBegin ++;
                                
                $promedioUptime =0;
                $promedioMinutos=0;
                
                foreach($resultado as $data)
                {
                    $date            = explode(" ",$data['rango']);
                    $strTiposEnlaces = "";
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':' . $column . $columnBegin)->applyFromArray($arrayStyleBodyTable);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $columnBegin . ':' . $column . $columnBegin)
                        ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($columnBegin)->setRowHeight(20);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $columnBegin, $date[0]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $columnBegin, $soporteService->
                            completarDecimalesPorcentajes($data['uptime']));
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $columnBegin, $data['minutos']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $columnBegin, $data['inicioIncidencia']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $columnBegin, $data['finIncidencia']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $columnBegin, $data['afectacion']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $columnBegin, $data['caso']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $columnBegin, $data['login']);

                    if($data['servicios'] && $strLogin)
                    {
                        //Se obtiene el id del caso
                        $objCaso = $arrayParametros['em']->getRepository("schemaBundle:InfoCaso")->findOneByNumeroCaso($data['caso']);
                        if(is_object($objCaso))
                        {
                            $intIdCaso = $objCaso->getId();
                            $arrayDetalleInicial = $arrayParametros['em']->getRepository('schemaBundle:InfoDetalle')
                                                                         ->getDetalleInicialCaso($intIdCaso);
                            if($arrayDetalleInicial[0]["detalleInicial"])
                            {
                                $arrayParametros["strLogin"]     = $strLogin;
                                $arrayParametros["intDetalleId"] = $arrayDetalleInicial[0]["detalleInicial"];

                                //Se obtiene los tipos de enlaces que tiene el punto, EJ: PRINCIPAL,BACKUP o ambos PRINCIPAL;BACKUP
                                $strTiposEnlaces  = $emComercial->getRepository("schemaBundle:InfoCaso")
                                                                ->getTipoEnlacesPorCasoAfectadoElementos($arrayParametros);

                                if(empty($strTiposEnlaces))
                                {
                                    $strTiposEnlaces  = $emComercial->getRepository("schemaBundle:InfoCaso")
                                                                    ->getTipoEnlacesPorCasoAfectadoServicio($arrayParametros);
                                }

                                //Si no se encuentra Afectados Tipo Afectado Servicio ni Elemento se consultan todas los servicios
                                if(empty($strTiposEnlaces) && $punto)
                                {
                                    $strTiposEnlaces  = $emComercial->getRepository("schemaBundle:InfoCaso")
                                                                    ->getTipoDeEnlacesPorPunto($punto);
                                }
                            }
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $columnBegin, $strTiposEnlaces);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $columnBegin, $data['servicios']);
                    if($versionOficial)
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue('K'. $columnBegin, $data['hipotesis']);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                    }
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);                                                         
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);  
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);  
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true); 
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                    
                    $promedioUptime  = $promedioUptime  + $data['uptime'];
                    $promedioMinutos = $promedioMinutos + $data['minutos'];
                    
                    $columnBegin++;
                }
                
                $objPHPExcel->getActiveSheet()->getStyle('B'. $columnBegin .':C'. $columnBegin)
                                ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('B'. $columnBegin .':C'. $columnBegin)->applyFromArray($arrayStyleCabecera);                

                if(is_object($objPunto))
                {
                    foreach($arrayDisponibilidad['resultado'] as $arrayItemDisponibilidad)
                    {
                        if($objPunto->getLogin() == $arrayItemDisponibilidad['loginDisponibilidad'])
                        {
                            $objPHPExcel->getActiveSheet()
                                        ->setCellValue('B' . $columnBegin,
                                                       $soporteService->completarDecimalesPorcentajes
                                                       ($arrayItemDisponibilidad['porcentajeDisponibilidad']));
                            break;
                        }
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue('C'. $columnBegin, $promedioMinutos);    
                
                $objPHPExcel->getActiveSheet()->mergeCells('D'. $columnBegin.':G'.$columnBegin);   
                $objPHPExcel->getActiveSheet()->getStyle('D'. $columnBegin)->applyFromArray($arrayStyleMensajes);
                $objPHPExcel->getActiveSheet()->setCellValue('D'. $columnBegin,
                    'Porcentaje promedio del "uptime" desde '.$arrayParametros['rangoDesde'].' hasta '.$arrayParametros['rangoHasta']);          
                                
                $objPHPExcel->getActiveSheet()->setTitle(substr($objPunto->getLogin(),0,30));
                $objPHPExcel->createSheet();
                                
                $i++;                                
            }
            
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Reporte_SLA_'.
                   str_replace(" ", "_", $arrayParametros['cliente']).'_de_'.$arrayParametros['rangoDesde'].'_al_'.
                   $arrayParametros['rangoHasta'].'_DETALLADO.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } catch (\Exception $ex) {
            error_log($ex);
            exit;
        }
    }
    
}
