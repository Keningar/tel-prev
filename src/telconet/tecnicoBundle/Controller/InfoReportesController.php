<?php
/**
* Controlador utilizado para las transacciones en la pantalla de consulta de ldap de clientess
* 
* @author John Vera         <javera@telconet.ec>
* @version 1.0 22-01-2016
* 
*/
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use \PHPExcel_Cell_DataType;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Border;
use \PHPExcel_Worksheet_MemoryDrawing;
use JMS\SecurityExtraBundle\Annotation\Secure;

class InfoReportesController extends Controller {
    
    
    /**
    * Funcion que llama la pantalla de consulta de ldap
    * 
    * @return mixed $respuesta Retorna el json con los registros consultados
    *
    * @author John Vera         <javera@telconet.ec>
    * @version 1.0 22-01-2016
    * 
    */
    public function indexAction() {

        return $this->render('tecnicoBundle:reportesClienteLdap:index.html.twig', array()); 
    }
    
    /**
     * getLdapClientesAction
     * Funcion que obtiene los registros del ldap segun un login o un elemento 
     * 
     * @return mixed $respuesta Retorna el json con los registros consultados
     * 
     * @author John Vera         <javera@telconet.ec>
     * @version 1.0 22-01-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2018 Se agrega el prefijo empresa como parámetro para la consulta getDatosLdapPorId
     * 
     */
    public function getLdapClientesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager('telconet');
        $session           = $request->getSession();
        $idEmpresa         = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        
        /* @var $migracion InfoServicioTecnicoService */
        $tecnicoService = $this->get('tecnico.InfoServicioTecnico');
        
        $objProdInternet         = $em->getRepository('schemaBundle:AdmiProducto')
                                ->findOneBy(array("esPreferencia" => "SI", 
                                                  "nombreTecnico" => "INTERNET", 
                                                  "empresaCod"    => $idEmpresa, 
                                                  "estado"        => "Activo"));
        
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['login'] = $request->get('login');
        $arrayParametros['idElemento'] = $request->get('idElemento');
        $arrayParametros['estado'] = $request->get('estado');
        $arrayParametros['tecnicoService'] = $tecnicoService;
        $arrayParametros['objProdInternet'] = $objProdInternet;
        $arrayParametros['start'] = $request->get('start');
        $arrayParametros['limit'] = $request->get('limit');
        
        $respuestaServicios = $em->getRepository('schemaBundle:InfoPunto')->getDatosLdapPorId($arrayParametros);
        
        if($respuestaServicios)
        {
            $data = '{"total":"' . $respuestaServicios['total'] . '","encontrados":' . json_encode($respuestaServicios['registros']) . '}';
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }

        $respuesta->setContent($data);

        return $respuesta;
    }
    
    /**
     * exportResumenPagosLineaAction
     * Funcion que realiza un excel con la información del ldap de todos los clientes de un olt
     * 
     * @return mixed $respuesta Retorna un archivo excel
     * 
     * @author John Vera         <javera@telconet.ec>
     * @version 1.0 22-01-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2018 Se agrega como parámetro el prefijo empresa para la función getDatosLdapPorId
     * 
     */
    public function exportLdapClientesAction()
    {
        try
        {

            $respuesta = new Response();
            $respuesta->headers->set('Content-Type', 'text/json');
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager('telconet');
            $session           = $request->getSession();
            $idEmpresa         = $session->get('idEmpresa');
            $strPrefijoEmpresa = $session->get('prefijoEmpresa');
            $idElemento        = $request->get('idElemento');

            /* @var $migracion InfoServicioTecnicoService */
            $tecnicoService = $this->get('tecnico.InfoServicioTecnico');

            $objProdInternet         = $em->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array("esPreferencia" => "SI", 
                                                      "nombreTecnico" => "INTERNET", 
                                                      "empresaCod"    => $idEmpresa, 
                                                      "estado"        => "Activo"));

            $arrayParametros['idElemento'] = $idElemento;
            $arrayParametros['tecnicoService'] = $tecnicoService;
            $arrayParametros['objProdInternet'] = $objProdInternet;
            $arrayParametros['estado'] = array('In-Corte', 'Activo');
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            
            $respuestaServicios = $em->getRepository('schemaBundle:InfoPunto')->getDatosLdapPorId($arrayParametros);

            $objPHPExcel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '1024MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel->getProperties()->setCreator("TELCOS++");
            $objPHPExcel->getProperties()->setLastModifiedBy($strUsuario);
            $objPHPExcel->getProperties()->setTitle("Reporte de ldap clientes");
            $objPHPExcel->getProperties()->setSubject("Reporte de ldap clientes");
            $objPHPExcel->getProperties()->setDescription("Muestra la configuracion de ldap de cada cliente.");
            $objPHPExcel->getProperties()->setKeywords("Asociado");
            $objPHPExcel->getProperties()->setCategory("Reporte");

            //Crea estilo para el titulo del reporte
            $arrayStyleTitulo = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 12,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
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

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte de ldap de clientes');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
            
            //Obtiene la ruta de la imagen
            $strPath  = $this->get('kernel')->getRootDir() . '/../web/public/images/netlife.jpg';
            $objImage = imagecreatefromjpeg($strPath);
            //Si obtiene la imagen la crea en la celda A1
            if($objImage){
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('TELCOS++');
                $objDrawing->setDescription('TELCOS++');
                $objDrawing->setImageResource($objImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(100);
                $objDrawing->setWidth(138);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', 'Login')
                ->setCellValue('B2', 'id servicio')
                ->setCellValue('C2', 'Nombre Elemento')
                ->setCellValue('D2', 'Estado Servicio')
                ->setCellValue('E2', 'Description')
                ->setCellValue('F2', 'Cn')
                ->setCellValue('G2', 'Sn')
                ->setCellValue('H2', 'tnEmpresa')
                ->setCellValue('I2', 'tnClientId')
                ->setCellValue('J2', 'tnClientClass')
                ->setCellValue('K2', 'tnStatus')
                ->setCellValue('L2', 'tnPolicy')
                ->setCellValue('M2', 'macAddress');
                
            $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($arrayStyleCabecera);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $intCounterRows = 3;
            foreach($respuestaServicios['registros']  as $arrayDatosPagosLinea):
                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':M'. $intCounterRows)->applyFromArray($arrayStyleBodyTable);
                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':M'. $intCounterRows)
                            ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($intCounterRows)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->setCellValue('A'. $intCounterRows, $arrayDatosPagosLinea['login']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'. $intCounterRows, $arrayDatosPagosLinea['idServicio']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'. $intCounterRows, $arrayDatosPagosLinea['nombreElemento']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'. $intCounterRows, $arrayDatosPagosLinea['estadoServicio']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'. $intCounterRows, $arrayDatosPagosLinea['description']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'. $intCounterRows, $arrayDatosPagosLinea['cn']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'. $intCounterRows, $arrayDatosPagosLinea['sn']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'. $intCounterRows, $arrayDatosPagosLinea['tnEmpresa']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'. $intCounterRows, $arrayDatosPagosLinea['tnClientId']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'. $intCounterRows, $arrayDatosPagosLinea['tnClientClass']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'. $intCounterRows, $arrayDatosPagosLinea['tnStatus']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'. $intCounterRows, $arrayDatosPagosLinea['tnPolicy']);
                $objPHPExcel->getActiveSheet()->setCellValue('M'. $intCounterRows, $arrayDatosPagosLinea['macAddress']);
                $intCounterRows = $intCounterRows + 1;
            endforeach;

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="ReporteLdapClientes' . date('d_M_Y') . '.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } catch (\Exception $ex) {
            exit;
        }
    }//exportResumenPagosLineaAction


}
