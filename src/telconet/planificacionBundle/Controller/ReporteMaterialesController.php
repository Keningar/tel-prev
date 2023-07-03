<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
  * Documentación para la clase 'ReporteMaterialesController'.
  *
  * Esta es la clase Controller que contiene las funciones de
  * indexAction(),gridAction()
  * del nuevo visor de materiales utilizados en las instalaciones
  * 
  * @author Richard Cabrera <rcabrera@telconet.ec>
  * @version 1.0 07-09-2015
  */

class ReporteMaterialesController extends Controller 
{
    
    /**
    * Documentación para la funcion indexAction().
    *
    * Esta funcion llama al twig principal: planificacionBundle:ReporteMateriales:index.html.twig  
    *   
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 07-09-2015       
    *
    *
    * @Secure(roles="ROLE_299-1")
    * 
    */
    public function indexAction() 
    {
        
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_299-37'))
        {
            $arrayRolesPermitidos[] = 'ROLE_299-37'; //Modulo Reporte Materiales - accion exportarConsulta
        }	
        if (true === $this->get('security.context')->isGranted('ROLE_299-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_299-7'; //Modulo Reporte Materiales - accion grid
        }
	        
        $strEm_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $strEntityItemMenu = $strEm_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("299", "1");
        
        return $this->render('planificacionBundle:ReporteMateriales:index.html.twig', array('item'          => $strEntityItemMenu,
                                                                                          'rolesPermitidos' => $arrayRolesPermitidos));
    }
    
    
    
    /**
    * Documentación para la funcion gridAction().
    * 
    * Esta funcion es la encargada de llenar el grid de la consulta.
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 07-09-2015
    * 
    *
    * @Secure(roles="ROLE_299-7")
    * 
    */
    public function gridAction() 
    {

        
        $objPeticion         = $this->get('request');   
        $strSession          = $this->get( 'session' );
        $strPrefijoEmpresa   = $strSession->get('prefijoEmpresa');        

        $strRespuesta        = new Response();
        $strRespuesta        ->headers->set('Content-Type', 'text/json');        
        $strPeticion         = $this->get('request');        
        $strFechaDesdePlanif = explode('T',$strPeticion->query->get('fechaDesdePlanif'));
        $strFechaHastaPlanif = explode('T',$strPeticion->query->get('fechaHastaPlanif'));        
        $arrayParametros     = array();
        
        $arrayParametros["fechaDesdePlanif"] = $strFechaDesdePlanif ? $strFechaDesdePlanif[0] : "";
        $arrayParametros["fechaHastaPlanif"] = $strFechaHastaPlanif ? $strFechaHastaPlanif[0] : "";            
		
        $strStart                            = $strPeticion->query->get('start');
        $strLimit                            = $strPeticion->query->get('limit');                       
        $emComercial                         = $this->getDoctrine()->getManager("telconet");
        
        $arrayParametros["start"]            = $strStart;
        $arrayParametros["limit"]            = $strLimit;     
                
        $objJson                             = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->generarJsonReporteMateriales($arrayParametros,$strPrefijoEmpresa);        
        $strRespuesta  ->setContent($objJson);
        
        return $strRespuesta;
    } 
    
    
    
    /**
    * Documentación para la funcion exportarConsultaAction().
    * 
    * Esta funcion realiza exportacion a excel de los materiales utilizados en la instalcion de un
    * determinado rango de fechas
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 07-09-2015
    * 
    * @Secure(roles="ROLE_299-37")
    * 
    */
    public function exportarConsultaAction() 
    {

        $objPeticion         = $this->get('request');        
        $objSession          = $objPeticion->getSession();                             
        
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');  
        $user                = $objSession->get('user');
        
        $strFechaDesdePlanif = explode('T',$objPeticion->query->get('fechaDesdePlanif'));
        $strFechaHastaPlanif = explode('T',$objPeticion->query->get('fechaHastaPlanif'));        
        
        $arrayParametros                     = array();        
        $arrayParametros["fechaDesdePlanif"] = $strFechaDesdePlanif[0]!="null" ? $strFechaDesdePlanif[0] : "";
        $arrayParametros["fechaHastaPlanif"] = $strFechaHastaPlanif[0]!="null" ? $strFechaHastaPlanif[0] : "";                 
        $arrayParametros["start"]            = "";        
        $arrayParametros["limit"]            = "";   
        
        $arrayRegistros                      = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                           ->getRegistrosReporteMateriales('datos',                                                          
                                                                                                                           $arrayParametros,                                                                                                                           
                                                                                                                           $strPrefijoEmpresa); 

		
        $this->generateExcelConsulta($arrayRegistros, $arrayParametros,$user,$strPrefijoEmpresa);
    }            
    
    /**
    * Documentación para la funcion generateExcelConsulta().
    * 
    * Esta funcion realiza la exportacion y escritura de los registros del reporte de Cambio de Equipos
    * en un archivo excel 
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 07-09-2015
    * 
    * @param array  $datos                  Registros de la consulta a la BD
    * @param array  $parametros
    * @param string $usuario                Usuario que genera el reporte
    * @param string $strPrefijoEmpresa      Prefijo de la Empresa
    * 
    * 
    */
    public static function generateExcelConsulta($datos, $parametros, $usuario,$strPrefijoEmpresa) 
    {
		
        error_reporting(E_ALL);
     
        $objPHPExcel      = new PHPExcel();       
        $objCacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings = array( ' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);
                
        $objReader     = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel   = $objReader->load(__DIR__."/../Resources/templatesExcel/templateMaterialesInstalacion.xls");
                       
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Visor de Materiales de Instalacion");
        $objPHPExcel->getProperties()->setSubject("Visor de Materiales de Instalacion");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda del visor (Materiales de Instalacion).");
        $objPHPExcel->getProperties()->setKeywords("Planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', strval(date_format(new \DateTime('now'), "d/m/Y")) );
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	
        $objPHPExcel->getActiveSheet()->setCellValue('C8',''.($parametros['fechaDesdePlanif']=="")?'Todos': $parametros['fechaDesdePlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9',''.($parametros['fechaHastaPlanif']=="")?'Todos': $parametros['fechaHastaPlanif']);       
		
        $i=13;		
        foreach ($datos as $arrayData):                                    
            $strCantidad    = (isset($arrayData["cantidad"]) ? ($arrayData["cantidad"] ? $arrayData["cantidad"]  : "") : "");
            $strMaterialCod = (isset($arrayData["materialCod"]) ? ($arrayData["materialCod"] ? $arrayData["materialCod"]  : "") : "");
            $strDescripcion = (isset($arrayData["descripcion"]) ? ($arrayData["descripcion"] ? $arrayData["descripcion"]  : "") : "");
            $strUnidad      = (isset($arrayData["unidad"]) ? ($arrayData["unidad"] ? $arrayData["unidad"]  : "") : "");                                
            			
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($strCantidad));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($strMaterialCod));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($strDescripcion));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($strUnidad));            
    
            $i=$i+1;
        endforeach;
		
        // Merge cells
        // Set document security
        $objPHPExcel->getSecurity()   ->setWorkbookPassword("PHPExcel");

        // Set page orientation and size        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Materiales_Instalacion'.date('d_M_Y').$strPrefijoEmpresa.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter ->save('php://output');
        exit;
    }
    
}
