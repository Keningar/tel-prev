<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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

class ReporteSupertelController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_200-1")
	*/
    public function indexAction()
    {
		$rolesPermitidos = array();
		if (true === $this->get('security.context')->isGranted('ROLE_200-37'))
			{
		$rolesPermitidos[] = 'ROLE_200-37';
		}
	
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("144", "1");
        
        return $this->render('planificacionBundle:ReporteSupertel:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
        
    /*
     * Llena el grid de consulta.
	 */
	/**
	* @Secure(roles="ROLE_200-7")
	*/
    public function gridAction()
    {
		$peticion = $this->get('request');
        $prefijoEmpresa = $peticion->getSession()->get('prefijoEmpresa');
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');
        
		if ($handle = opendir('/home/telcos/web/public/reportesSupertel')) {
			while (false !== ($file = readdir($handle))) {
				$archivo = sprintf("%s",$file);
				$partsReporte = explode("-",$archivo);
				$partsNombreReporte = explode("_",$archivo);
				$nombreReporte = $partsNombreReporte[1];
				
				if($nombreReporte && $partsReporte[0]==$prefijoEmpresa)
					$reportesArray[] = array('nombre_reporte' => $nombreReporte,'link_exportar' => '/public/reportesSupertel/'.$archivo);
			}
			$data = '{"total":"'.count($reportesArray).'","encontrados":'.json_encode($reportesArray).'}';
			closedir($handle);
		}
		else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
    } 

	/**
	* @Secure(roles="ROLE_200-37")
	*/
    public function exportarConsultaAction()
    {
		$formato = 'D M d Y H:i:s';
        $peticion = $this->get('request');
		
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");					
        $fechaDesde = explode(' GMT',$peticion->get('fechaDesde'));        $fechaHasta = explode(' GMT',$peticion->get('fechaHasta'));	
        $fechaUEDesde = explode(' GMT',$peticion->get('fechaUEDesde'));    $fechaUEHasta = explode(' GMT',$peticion->get('fechaUEHasta'));		
        $estado = $peticion->get('estado') ? $peticion->get('estado') : "Todos";
       
		$fechaDesdeString = '';		$fechaDesde = $fechaDesde ? $fechaDesde[0] : "";
		if($fechaDesde != "")
		{
			$fechaD = \DateTime::createFromFormat($formato, $fechaDesde);
			$fechaDesdeString = date_format($fechaD, 'Y-m-d'); 
		}
		$fechaHastaString = '';		$fechaHasta = $fechaHasta ? $fechaHasta[0] : "";
		if($fechaHasta != "")
		{
			$fechaH = \DateTime::createFromFormat($formato, $fechaHasta);
			$fechaHastaString = date_format($fechaH, 'Y-m-d'); 
		}
		
		$fechaUEDesdeString = '';		$fechaUEDesde = $fechaUEDesde ? $fechaUEDesde[0] : "";
		if($fechaUEDesde != "")
		{
			$fechaUED = \DateTime::createFromFormat($formato, $fechaUEDesde);
			$fechaUEDesdeString = date_format($fechaUED, 'Y-m-d'); 
		}
		$fechaUEHastaString = '';		$fechaUEHasta = $fechaUEHasta ? $fechaUEHasta[0] : "";
		if($fechaUEHasta != "")
		{
			$fechaUEH = \DateTime::createFromFormat($formato, $fechaUEHasta);
			$fechaUEHastaString = date_format($fechaUEH, 'Y-m-d'); 
		}
		
        $parametros = array();		
        $parametros["fechaDesde"] = $fechaDesdeString ? $fechaDesdeString : "";
        $parametros["fechaHasta"] = $fechaHastaString ? $fechaHastaString : "";
        $parametros["fechaUEDesde"] = $fechaUEDesdeString ? $fechaUEDesdeString : "";
        $parametros["fechaUEHasta"] = $fechaUEHastaString ? $fechaUEHastaString : "";
        $parametros["estado"] = $peticion->get('estado') ? $peticion->get('estado') : "Todos";
        $parametros["codEmpresa"] = $codEmpresa ? $codEmpresa : "Todos";
		
        $em = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
		$totalRegistros = $em->getRepository('schemaBundle:VistaClientesSupertel')->getReporteSupertel($parametros, '', ''); //10000
        $num = $totalRegistros['total'];
		$registros = $totalRegistros['registros'];
		
        $this->generateExcelConsulta($registros, $em, $emInfraestructura, $parametros, $peticion->getSession()->get('user'));
    }
    
    public static function generateExcelConsulta($datos, $em, $emInfraestructura, $parametros, $usuario)
    {
		error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateReporteSupertel.xls");
         
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Reporte Supertel");
        $objPHPExcel->getProperties()->setSubject("Reporte Supertel");
        $objPHPExcel->getProperties()->setDescription("Resultado de clientes para la Supertel.");
        $objPHPExcel->getProperties()->setKeywords("Supertel");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('C9',''.($parametros['fechaDesde']=="")?'Todos': $parametros['fechaDesde']);
        $objPHPExcel->getActiveSheet()->setCellValue('C10',''.($parametros['fechaHasta']=="")?'Todos': $parametros['fechaHasta']);
        $objPHPExcel->getActiveSheet()->setCellValue('F9',''.($parametros['fechaUEDesde']=="")?'Todos': $parametros['fechaUEDesde']);
        $objPHPExcel->getActiveSheet()->setCellValue('F10',''.($parametros['fechaUEHasta']=="")?'Todos': $parametros['fechaUEHasta']);
		
        $i=14;		
        foreach ($datos as $data):
			$master_account = "";	$coordenadas = "";				
			$telefono1 = "";	$telefonos2 = "";
			$tipo_enlace = "";
			$numeroPC = "";			$velocidad_contratada = "";
			$usuarioActivacion = "";	$usuarioUltimoEstado = "";
			$id_elemento = ""; 			$nombre_elemento = "";
			$id_elemento_padre = "";	$nombre_elemento_padre = "";	
			
			$telefono1=  $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getStringFormasContactoParaSession($data["idPersona"], "Telefono Fijo");
			$telefonos2=  $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getStringFormasContactoParaSession($data["idPersona"], "Telefono Movil");
				
			if($data["ultimaMillaId"])
			{
				$infoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')->getTipoMedioByUltimaMilla($data["ultimaMillaId"]);
				if($infoEnlace && count($infoEnlace)>0)
				{
					$tipo_enlace = $infoEnlace[0]["nombreTipoMedio"];
				}
				/*$infoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')->findOneById($data["ultimaMillaId"]);					
				$tipoMedioEntity = (($infoEnlace && count($infoEnlace)>0) ? ($infoEnlace->getTipoMedioId() ? $infoEnlace->getTipoMedioId() : "") : "");
				
				if($tipoMedioEntity && count($tipoMedioEntity)>0)
				{
					$id_tipo_enlace = $tipoMedioEntity->getId() ? $tipoMedioEntity->getId() : "";
					$tipo_enlace = $tipoMedioEntity->getNombreTipoMedio() ? $tipoMedioEntity->getNombreTipoMedio() : "";
				}//fin if tipomedio 
				*/
			}// fin if ultima milla id
			
			if($data["idProducto"] && $data["idPlanDet"])
			{
				$registro_Velocidad = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaracteristicaByParametros($data["idPlanDet"], $data["idProducto"], 'CAPACIDAD1');
				$registro_NumPC = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaracteristicaByParametros($data["idPlanDet"], $data["idProducto"], 'NUMERO PC');
				
				if($registro_Velocidad && count($registro_Velocidad)>0)
				{
					$velocidad_contratada = $registro_Velocidad[0]["valor"];
				}
				if($registro_NumPC && count($registro_NumPC)>0)
				{
					$numeroPC = $registro_NumPC[0]["valor"];
				}
				//CARACTERSISTICAS NUMEROPC 10  CAPACIDAD1 1  INFO SERVICIO PROD CARAC
				/*$entityCaracteristica_Velocidad = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("CAPACIDAD1");
				$entityCaracteristica_NumPC = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("NUMERO PC");
				$caracteristica_Velocidad = ($entityCaracteristica_Velocidad ? ($entityCaracteristica_Velocidad->getId() ? $entityCaracteristica_Velocidad->getId() : '') : ''); 
				$caracteristica_NumPC = ($entityCaracteristica_NumPC ? ($entityCaracteristica_NumPC->getId() ? $entityCaracteristica_NumPC->getId() : '') : '');   
				
				if($caracteristica_Velocidad)
				{
					$productoCaracteristica_Velocidad = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array("productoId"=>$data["idProducto"], "caracteristicaId" => $caracteristica_Velocidad));
					$prodcarac_Velocidad = ($productoCaracteristica_Velocidad ? ($productoCaracteristica_Velocidad->getId() ? $productoCaracteristica_Velocidad->getId() : '') : '');  
					
					if($prodcarac_Velocidad)
					{
						$registro_Velocidad = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array("planDetId"=>$data["idPlanDet"], "productoCaracterisiticaId" => $prodcarac_Velocidad));
						$velocidad_contratada = ($registro_Velocidad ? ($registro_Velocidad->getId() ? $registro_Velocidad->getId() : '') : '');  
					}
				}
				if($caracteristica_NumPC)
				{
					$productoCaracteristica_NumPC = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array("productoId"=>$data["idProducto"], "caracteristicaId" => $caracteristica_NumPC));
					$prodcarac_NumPC = ($productoCaracteristica_NumPC ? ($productoCaracteristica_NumPC->getId() ? $productoCaracteristica_NumPC->getId() : '') : '');  
					
					if($prodcarac_NumPC)
					{
						$registro_NumPC = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array("planDetId"=>$data["idPlanDet"], "productoCaracterisiticaId" => $prodcarac_NumPC));
						$numeroPC = ($registro_NumPC ? ($registro_NumPC->getId() ? $registro_NumPC->getId() : '') : '');  
					}
				}*/
			}
			
			if($data["usuarioActivacion"])
			{
				$empleadoActivacion = $em->getRepository('schemaBundle:InfoPersona')->getMiniPersonaPorLogin($data["usuarioActivacion"]);
				if($empleadoActivacion && count($empleadoActivacion)>0)
				{
					$usuarioActivacion = $empleadoActivacion[0]["nombres"] . " " . $empleadoActivacion[0]["apellidos"];
					//(($empleadoActivacion->getNombres() && $empleadoActivacion->getApellidos()) ? $empleadoActivacion->getNombres() . " " . $empleadoActivacion->getApellidos() : "");
				}
			}
			if($data["usuarioUltimoEstado"])
			{
				$empleadoUltimoEstado = $em->getRepository('schemaBundle:InfoPersona')->getMiniPersonaPorLogin($data["usuarioUltimoEstado"]);
				if($empleadoUltimoEstado && count($empleadoUltimoEstado)>0)
				{
					$usuarioUltimoEstado = $empleadoUltimoEstado[0]["nombres"] . " " . $empleadoUltimoEstado[0]["apellidos"];
					//$usuarioUltimoEstado = (($empleadoUltimoEstado->getNombres() && $empleadoUltimoEstado->getApellidos()) ? $empleadoUltimoEstado->getNombres() . " " . $empleadoUltimoEstado->getApellidos() : "");
				}
			}
						
			if($data["interfaceElementoId"])
			{
				$infoElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->getElementosByInterfaceElemento($data["interfaceElementoId"]);
				if($infoElemento && count($infoElemento)>0)
				{
					$id_elemento = $infoElemento[0]["idElemento"];
					$nombre_elemento = $infoElemento[0]["nombreElemento"];
					$id_elemento_padre = $infoElemento[0]["idElementoPadre"];
					$nombre_elemento_padre = $infoElemento[0]["nombreElementoPadre"];
				}
				/*$interfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->findOneById($data["interfaceElementoId"]);					
				$dslamElemento = (($interfaceElemento && count($interfaceElemento)>0) ? ($interfaceElemento->getElementoId() ? $interfaceElemento->getElementoId() : "") : "");
				
				if($dslamElemento && count($dslamElemento)>0)
				{
					$id_elemento = $dslamElemento->getId() ? $dslamElemento->getId() : "";
					$nombre_elemento = $dslamElemento->getNombreElemento() ? $dslamElemento->getNombreElemento() : "";
					
					$relacionPop = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$dslamElemento->getId()));
					if($relacionPop && count($relacionPop)>0)
					{
						$popElementoId = $relacionPop[0]->getElementoIdA();
						$popElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($popElementoId);
						
						if($popElemento && count($popElemento)>0)
						{
							$id_elemento_padre = $popElemento->getId() ? $popElemento->getId() : "";
							$nombre_elemento_padre = $popElemento->getNombreElemento() ? $popElemento->getNombreElemento() : "";	
						}//fin if pop elemento
					}	//fin if relacion pop
				}//fin if dslam elemento*/
			}// fin if interface elemento id
				
			$ultimo_estado = ($data["ultimoEstado"] ? $data["ultimoEstado"] : "");
			$fecha_ultimo_estado = ( $ultimo_estado != "Activo" ? ($data["fechaUltimoEstado"] ? date_format($data["fechaUltimoEstado"],"d-m-Y G:i") : "") : "");
						
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($data["login"] ? $data["login"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, ($data["fechaActivacion"] ? date_format($data["fechaActivacion"],"d-m-Y G:i") : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($data["nombreCanton"] ? $data["nombreCanton"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($data["nombreParroquia"] ? $data["nombreParroquia"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($data["nombres"] ? $data["nombres"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($data["apellidos"] ? $data["apellidos"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($data["razonSocial"] ? $data["razonSocial"] : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim(($data["direccionPunto"] ? $data["direccionPunto"] : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, trim(($telefono1 ? $telefono1 : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, trim(($telefonos2 ? $telefonos2 : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, trim(($numeroPC ? $numeroPC : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, trim(($tipo_enlace ? $tipo_enlace : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, trim(($velocidad_contratada ? $velocidad_contratada : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, trim(($data["tipoContrato"] ? $data["tipoContrato"] : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, trim(($ultimo_estado ? $ultimo_estado : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim(($master_account ? $master_account : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, trim(($data["tipoCuenta"] ? $data["tipoCuenta"] : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, trim(($nombre_elemento_padre ? $nombre_elemento_padre : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, ($fecha_ultimo_estado ? $fecha_ultimo_estado : ""));
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, trim(($data["servicio"] ? $data["servicio"] : "")));
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, trim(($data["descripcionProducto"] ? $data["descripcionProducto"] : "")));
										
            $i=$i+1;
        endforeach;
		
		
//        Util::addBorderThinB($objPHPExcel,'A'.($i-1).':I'.($i-1));
        // Merge cells
        // Set document security
        // $objPHPExcel->getSecurity()->setWorkbookPassword("PHPExcel");
        // $objPHPExcel->getSecurity()->setLockWindows(true);
        // $objPHPExcel->getSecurity()->setLockStructure(true);

        // Set sheet security
        // $objPHPExcel->getActiveSheet()->getProtection()->setPassword('PHPExcel');
        // $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
        // $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
        // $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
        // $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);

        // Set page orientation and size
        //$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

		$nombreExporta =  "Reporte_General_Planificacion_" . date('d_M_Y_G_i') . ".xls";
		
        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_General_Planificacion_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
		//$objWriter->save("/home/telcos/web/public/uploads/supertel/$nombreExporta");

        exit;
    }
}
