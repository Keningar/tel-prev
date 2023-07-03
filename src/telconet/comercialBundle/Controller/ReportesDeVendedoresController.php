<?php

namespace telconet\comercialBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * ReportesDeVendedoresController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Reportes de Vendedores
 *
 * @author Hector Ortega <haortega@telconet.ec>
 * @version 1.0 19-09-2016
 */
class ReportesDeVendedoresController extends Controller
{

    /**
     * @Secure(roles="ROLE_360-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra la pantalla inicial para escoger el vendedor y el mes del reporte.
     *
     * @return Response 
     *
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.0 19-09-2016
     */
    public function indexAction()
    {
        return $this->render('comercialBundle:ReportesDeVendedores:index.html.twig');
    }


    /**
     * @Secure(roles="ROLE_360-1")
     *
     * Documentación para el método 'gridPuntosServiciosPorVendedorAction'.
     *
     * Muestra los puntos y los servicios por codigo de Vendedor.
     *
     * @return Response 
     *
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.0 19-09-2016
     */
    public function gridPuntosServiciosPorVendedorAction()
    {   
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');
        
        $request                            = $this->getRequest();
        $arrayParametros['usuarioVendedor'] = $request->get("usuarioVendedor");
        $arrayParametros['codEmpresa']      = $request->getSession()->get('idEmpresa');
        $arrayParametros['fecha']           = $request->get('fecha');    
        $emComercial                        = $this->get('doctrine')->getManager('telconet');
        $jsonServiciosPorPunto              = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->getJsonPuntosServiciosPorVendedor($arrayParametros);       
        $response->setContent($jsonServiciosPorPunto);
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_360-1")
     *
     * Documentación para el método 'excelPuntosServiciosPorVendedorAction'.
     *
     * Genera el excel con el reporte de los puntos y servicios or vendedor.
     *
     * @return Response 
     *
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.0 19-09-2016
     */
    public function excelPuntosServiciosPorVendedorAction()
    {

        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // Establecer propiedades
        $objPHPExcel->getProperties()
                    ->setCreator("Telcos")
                    ->setLastModifiedBy("Telcos")
                    ->setTitle("Documento Excel de Clientes")
                    ->setSubject("Documento Excel de Clientes")
                    ->setDescription("")
                    ->setKeywords("Excel Office 2007 openxml php")
                    ->setCategory("Excel");
        $styleArray = array(
            'font' => array(
                 'bold' => true
            )
        );

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'FECHA CREACION')
                    ->setCellValue('B1', 'CLIENTE')
                    ->setCellValue('C1', 'DIRECCION')
                    ->setCellValue('D1', 'PUNTO')
                    ->setCellValue('E1', 'DIRECCION PUNTO')
                    ->setCellValue('F1', 'PUNTO FACTURACION')
                    ->setCellValue('G1', 'PRODUCTO/PLAN (SERVICIO)')
                    ->setCellValue('H1', 'FECHA ACTIVACION SERVICIO')
                    ->setCellValue('I1', 'PRECIO')
                    ->setCellValue('J1', 'CANTIDAD')
                    ->setCellValue('K1', 'PORCENTAJE')
                    ->setCellValue('L1', 'VALOR DESCUENTO')
                    ->setCellValue('M1', 'ES VENTA');
       
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('M1')->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        
        $request                            = $this->getRequest();
        $arrayParametros['usuarioVendedor'] = $request->get("usuarioVendedor");
        $arrayParametros['codEmpresa']      = $request->getSession()->get('idEmpresa');
        $arrayParametros['fecha']           = $request->get('fecha');    
        $emComercial                        = $this->get('doctrine')->getManager('telconet');
        $arrayServicios                     = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->getJsonPuntosServiciosPorVendedor($arrayParametros);
        $arrayServicios                     = json_decode($arrayServicios, true);
        $arrayServiciosRetornados           = $arrayServicios['encontrados'];

        $i = 2;
        foreach($arrayServiciosRetornados as $servicio)
        {   
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $servicio['feCreacionServ']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $servicio['nombreCliente']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $servicio['direccionCliente']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $servicio['loginPunto']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $servicio['direccionPunto']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $servicio['loginPuntoFacturacion']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $servicio['planProducto']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $servicio['fechaActivacionServicio']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $servicio['precioVenta']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $servicio['cantidad']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $servicio['porcentajeDescuento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $servicio['valorDescuento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $servicio['esVenta']);
            $i++;
        }
        
        $objPHPExcel->getActiveSheet()->setTitle('Servicios por Vendedor');
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);

        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_servicios_por_vendedor.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
        exit;
    }

}