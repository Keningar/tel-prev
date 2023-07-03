<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


 /**
  * Documentación para la clase 'ReporteCambioEquipoController'.
  *
  * Esta es la clase Controller que contiene las funciones de
  * indexAction(),gridAction(),exportarConsultaAction(),generateExcelConsulta()
  * del nuevo resporte de Cambio de Equipos
  * 
  * @author Richard Cabrera <rcabrera@telconet.ec>
  * @version 1.0 27-07-2015
  */

class ReporteCambioEquipoController extends Controller { 
    
    
    /**
    * Documentación para la funcion indexAction().
    *
    * Esta funcion llama al twig principal: planificacionBundle:ReporteCambioEquipos:index.html.twig  
    *   
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-07-2015
    * 
    */    

    /**
	* @Secure(roles="ROLE_295-1")
	*/
    public function indexAction() {
        
        $arrayRolesPermitidos = array();
	    if (true === $this->get('security.context')->isGranted('ROLE_295-37'))
	    {
            $arrayRolesPermitidos[] = 'ROLE_295-37';
        }	
	
        $strEm             = $this->getDoctrine()->getManager('telconet_general');
        $strEm_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $strEntityItemMenu = $strEm_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("295", "1");
        
        return $this->render('planificacionBundle:ReporteCambioEquipos:index.html.twig', array('item'            => $strEntityItemMenu,
                                                                                               'rolesPermitidos' => $arrayRolesPermitidos));
    }
    
    /**
    * Documentación para la funcion gridAction().
    * 
    * Esta funcion es la encargada de llenar el grid de la consulta.
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-07-2015
    * 
    */
    
    /**
	* @Secure(roles="ROLE_295-7")
	*/
    public function gridAction() {

        $strSession          = $this->get( 'session' );
        $strPrefijoEmpresa   = $strSession->get('prefijoEmpresa');
        $strRespuesta        = new Response();
        $strRespuesta        ->headers->set('Content-Type', 'text/json');        
        $strPeticion         = $this->get('request');        
        $strFechaDesdePlanif = explode('T',$strPeticion->query->get('fechaDesdePlanif'));
        $strFechaHastaPlanif = explode('T',$strPeticion->query->get('fechaHastaPlanif'));        
        $strEstado           = $strPeticion->query->get('estado') ? $strPeticion->query->get('estado') : "Todos";		
        $arrayParametros     = array();
        
        $arrayParametros["fechaDesdePlanif"] = $strFechaDesdePlanif ? $strFechaDesdePlanif[0] : "";
        $arrayParametros["fechaHastaPlanif"] = $strFechaHastaPlanif ? $strFechaHastaPlanif[0] : "";        
        $arrayParametros["estado"]           = $strPeticion->query->get('estado') ? $strPeticion->query->get('estado') : "Todos";       
		
        $strStart                            = $strPeticion->query->get('start');
        $strLimit                            = $strPeticion->query->get('limit');                       
        $strEm                               = $this->getDoctrine()->getManager("telconet");
        $strEm_general                       = $this->getDoctrine()->getManager("telconet_general");  
        
        $arrayParametros["start"]            = $strStart;
        $arrayParametros["limit"]            = $strLimit;     
                
        $objJson                             = $strEm->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->generarJsonReporteCambioEquipo($strEstado, $arrayParametros,$strPrefijoEmpresa);        
        $strRespuesta  ->setContent($objJson);
        
        return $strRespuesta;
    } 
	
     
    /**
    * Documentación para la funcion exportarConsultaAction().
    * 
    * Esta funcion realiza el llamado a un paquete de la BD, en el cual se migro todas las validaciones
    * del PHP, por motivo de mejorar el rendimiento del proceso de exportacion del archivo. 
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-07-2015
    * 
    */

	/**
	* @Secure(roles="ROLE_295-37")
	*/
    public function exportarConsultaAction() {

        $strSession          = $this->get( 'session' );
        $strPrefijoEmpresa   = $strSession->get('prefijoEmpresa');        
        $strPeticion         = $this->get('request');		
        $strFechaDesdePlanif = explode('T',$strPeticion->query->get('fechaDesdePlanif'));
        $strFechaHastaPlanif = explode('T',$strPeticion->query->get('fechaHastaPlanif'));        
        $strEstado           = $strPeticion->query->get('estado') ? $strPeticion->query->get('estado') : "Todos";
        
        $arayParametros                     = array();        
        $arayParametros["fechaDesdePlanif"] = $strFechaDesdePlanif[0]!="null" ? $strFechaDesdePlanif[0] : "";
        $arayParametros["fechaHastaPlanif"] = $strFechaHastaPlanif[0]!="null" ? $strFechaHastaPlanif[0] : "";        
        $arayParametros["estado"]           = $strPeticion->query->get('estado') ? $strPeticion->query->get('estado') : "Todos";            
        $arayParametros["start"]            = "";        
        $arayParametros["limit"]            = "";   
	
		$strEm                              = $this->getDoctrine()->getManager("telconet");
        $strEm_general                      = $this->getDoctrine()->getManager("telconet_general");
        $strEmInfraestructura               = $this->getDoctrine()->getManager("telconet_infraestructura");		
        $strEstado                          = ($strEstado ? $strEstado : "Todos");
        
		$arrayRegistros                     = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                          ->getRegistrosReporteCambioEquipo('datos',                                                          
                                                                                                                            $strEstado,
                                                                                                                            $arayParametros,
                                                                                                                            $strPrefijoEmpresa); 

		
        $this->generateExcelConsulta($arrayRegistros, $arayParametros, $strPeticion->getSession()->get('user'),$strPrefijoEmpresa);
    }            
    
    /**
    * Documentación para la funcion generateExcelConsulta().
    * 
    * Esta funcion realiza la exportacion y escritura de los registros del reporte de Cambio de Equipos
    * en un archivo excel 
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-07-2015
    * 
    */
    public static function generateExcelConsulta($datos, $parametros, $usuario,$prefijoEmpresa) {
		
        error_reporting(E_ALL);
     
        $objPHPExcel      = new PHPExcel();       
        $objCacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings = array( ' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);
                
        $objReader     = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel   = $objReader->load(__DIR__."/../Resources/templatesExcel/templateCambioEquipo".$prefijoEmpresa.".xls");
                       
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Reporte Cambio de Equipo");
        $objPHPExcel->getProperties()->setSubject("Reporte Cambio de Equipo");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de reporte (Cambio de Equipos).");
        $objPHPExcel->getProperties()->setKeywords("Planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', strval(date_format(new \DateTime('now'), "d/m/Y")) );
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	
        $objPHPExcel->getActiveSheet()->setCellValue('E8',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('C8',''.($parametros['fechaDesdePlanif']=="")?'Todos': $parametros['fechaDesdePlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9',''.($parametros['fechaHastaPlanif']=="")?'Todos': $parametros['fechaHastaPlanif']);       
		
        $i=13;		
        foreach ($datos as $arrayData):            
                        
			$strNombres     = (isset($arrayData["nombres"]) ?  ($arrayData["nombres"] ? $arrayData["nombres"] : "") : "");
			$strApellidos   = (isset($arrayData["apellidos"]) ? ($arrayData["apellidos"] ? $arrayData["apellidos"]  : "") : "");
			$strDireccion   = (isset($arrayData["direccion"]) ? ($arrayData["direccion"] ? $arrayData["direccion"]  : "") : "");
			$strMotivo      = (isset($arrayData["motivo"]) ? ($arrayData["motivo"] ? $arrayData["motivo"]  : "") : "");
			$strUsrCreacion = (isset($arrayData["usrCreacion"]) ? ($arrayData["usrCreacion"] ? $arrayData["usrCreacion"]  : "") : "");
			$strFeCreacion  = (isset($arrayData["feCreacion"]) ? ($arrayData["feCreacion"] ? strval(date_format($arrayData["feCreacion"],"d/m/Y G:i")) : "") : "");  
			$strEstado      = (isset($arrayData["estado"]) ? ($arrayData["estado"] ? $arrayData["estado"]  : "") : "");                                    
            			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($strNombres));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($strApellidos));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($strDireccion));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($strMotivo));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($strUsrCreacion));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($strFeCreacion));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($strEstado));
    
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
        header('Content-Disposition: attachment;filename="Reporte_Cambio_Equipos'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter ->save('php://output');
        exit;
    }
   
}
