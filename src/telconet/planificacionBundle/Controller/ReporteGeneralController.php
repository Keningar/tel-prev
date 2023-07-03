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

class ReporteGeneralController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_144-1")
	*/
    public function indexAction()
    { 
    
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_144-37'))
		{
	$rolesPermitidos[] = 'ROLE_144-37';
	}
	
	
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("144", "1");
        
        return $this->render('planificacionBundle:ReporteGeneral:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
        
    /*
     * Llena el grid de consulta.
	 */
	/**
	* @Secure(roles="ROLE_144-7")
	*/
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $fechaDesdeSolPlanif = explode('T',$peticion->query->get('fechaDesdeSolPlanif'));
        $fechaHastaSolPlanif = explode('T',$peticion->query->get('fechaHastaSolPlanif'));
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanificacion'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanificacion'));		
        $estado =$peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";
        
        $parametros = array();
        $parametros["fechaDesdeSolPlanif"]= $fechaDesdeSolPlanif ? $fechaDesdeSolPlanif[0] : "";
        $parametros["fechaHastaSolPlanif"]= $fechaHastaSolPlanif ? $fechaHastaSolPlanif[0] : "";
        $parametros["fechaDesdePlanif"]= $fechaDesdePlanif ? $fechaDesdePlanif[0] : "";
        $parametros["fechaHastaPlanif"]= $fechaHastaPlanif ? $fechaHastaPlanif[0] : "";
        $parametros["login2"]=$peticion->query->get('login2') ? $peticion->query->get('login2') : "";	
        $parametros["descripcionPunto"]=$peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "";
        $parametros["vendedor"]=$peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "";
        $parametros["ciudad"]=$peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "";
        $parametros["numOrdenServicio"]=$peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "";
        $parametros["estado"]=$peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";
        $parametros["tipoSolicitud"]= $peticion->query->get('tipoSolicitud') ? $peticion->query->get('tipoSolicitud') : "";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonReporteGeneral($em, $em_general, $start, $limit, $estado, $parametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    } 

	/**
	* @Secure(roles="ROLE_144-37")
	*/
    //Se modifica el procedimiento para mejorar rendimiento de generacion de archivos
    public function exportarConsultaAction() {

        $peticion = $this->get('request');
        $fechaDesdeSolPlanif = '';
        $fechaHastaSolPlanif = '';
        $fechaDesdePlanif = '';
        $fechaHastaPlanif = '';

        $fechaDesdeSolPlanif = ($peticion->query->get('fechaDesdeSolPlanif')) ? explode('T', $peticion->query->get('fechaDesdeSolPlanif')) : "";
        $fechaHastaSolPlanif = ($peticion->query->get('fechaHastaSolPlanif')) ? explode('T', $peticion->query->get('fechaHastaSolPlanif')) : "";
        $fechaDesdePlanif = ($peticion->query->get('fechaDesdePlanificacion')) ? explode('T', $peticion->query->get('fechaDesdePlanificacion')) : "";
        $fechaHastaPlanif = ($peticion->query->get('fechaHastaPlanificacion')) ? explode('T', $peticion->query->get('fechaHastaPlanificacion')) : "";
        $estado = $peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";

        $parametros = array();
        $fechaDesdeSolPlanif = $fechaDesdeSolPlanif ? $fechaDesdeSolPlanif[0] : "";
        $fechaHastaSolPlanif = $fechaHastaSolPlanif ? $fechaHastaSolPlanif[0] : "";
        $fechaDesdePlanif = $fechaDesdePlanif ? $fechaDesdePlanif[0] : "";
        $fechaHastaPlanif = $fechaHastaPlanif ? $fechaHastaPlanif[0] : "";
        $login = $peticion->query->get('login2') ? $peticion->query->get('login2') : "";
        $descripcionPunto = $peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "";
        $vendedor = $peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "";
        $ciudad = $peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "";
        $numOrdenServicio = $peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "";
        $estado = $peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";
        $tipoSolicitud = $peticion->query->get('tipoSolicitud') ? $peticion->query->get('tipoSolicitud') : "Todos";
        
        $parametros["fechaDesdeSolPlanif"]= $fechaDesdeSolPlanif;
        $parametros["fechaHastaSolPlanif"]= $fechaHastaSolPlanif;
        $parametros["fechaDesdePlanif"]= $fechaDesdePlanif;
        $parametros["fechaHastaPlanif"]= $fechaHastaPlanif;
        $parametros["login2"]= $login;	
        $parametros["descripcionPunto"]= $descripcionPunto;
        $parametros["vendedor"]= $vendedor;
        $parametros["ciudad"]= $ciudad;
        $parametros["numOrdenServicio"]= $numOrdenServicio;
        $parametros["estado"]= $estado;

        $em = $this->getDoctrine()->getManager("telconet");
        $em_general = $this->getDoctrine()->getManager("telconet_general");

        $estado = ($estado ? $estado : "Todos");

        try {
            if (isset($fechaDesdeSolPlanif)) {
                if ($fechaDesdeSolPlanif && $fechaDesdeSolPlanif != "") {
                    $dateF = explode("-", $fechaDesdeSolPlanif);
                    $fechaSql = date("d/m/Y", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2]));
                    $fechaDesdeSolPlanif = $fechaSql;
                }
            }
            if (isset($fechaHastaSolPlanif)) {
                if ($fechaHastaSolPlanif && $fechaHastaSolPlanif != "") {
                    $dateF = explode("-", $fechaHastaSolPlanif);
                    $fechaSqlAdd = strtotime(date("d-m-Y", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                    $fechaSql = date("d/m/Y", $fechaSqlAdd);
                    $fechaHastaSolPlanif = $fechaSql;
                }
            }

            if (isset($fechaDesdePlanif)) {
                if ($fechaDesdePlanif && $fechaDesdePlanif != "") {
                    $dateF = explode("-", $fechaDesdePlanif);
                    $fechaSql = date("d/m/Y", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2]));
                    $fechaDesdePlanif = $fechaSql;
                }
            }
            if (isset($fechaHastaPlanif)) {
                if ($fechaHastaPlanif && $fechaHastaPlanif != "") {
                    $dateF = explode("-", $fechaHastaPlanif);
                    $fechaSqlAdd = strtotime(date("d-m-Y", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                    $fechaSql = date("d/m/Y", $fechaSqlAdd);
                    $fechaHastaPlanif = $fechaSql;
                }
            }
            
            //se realiza la invocacion al procedure que realiza el procesamiento de la información a generar en archivo excel
            $sql = "BEGIN GeneraInformacionExcel.generarInfoExcelReporteGeneral(:estado, :tipoSolicitud, :fechaDesdeSolPlani, :fechaHastaSolPlani, :fechaDesdePlani, :fechaHastaPlani, :login, :descripcionPunto, :vendedor, :ciudad, :numOrdenServicio, :codigoArchivo, :mensajeError); END;";
            $stmt = $em->getConnection()->prepare($sql);
            $codigoArchivoSalida = str_repeat(' ', 5000);
            $mensajeErrorSalida = str_repeat(' ', 5000);
            $stmt->bindParam('estado', $estado);
            $stmt->bindParam('tipoSolicitud', $tipoSolicitud);
            $stmt->bindParam('fechaDesdeSolPlani', $fechaDesdeSolPlanif);
            $stmt->bindParam('fechaHastaSolPlani', $fechaHastaSolPlanif);
            $stmt->bindParam('fechaDesdePlani', $fechaDesdePlanif);
            $stmt->bindParam('fechaHastaPlani', $fechaHastaPlanif);
            $stmt->bindParam('login', $login);
            $stmt->bindParam('descripcionPunto', $descripcionPunto);
            $stmt->bindParam('vendedor', $vendedor);
            $stmt->bindParam('ciudad', $ciudad);
            $stmt->bindParam('numOrdenServicio', $numOrdenServicio);
            $stmt->bindParam('codigoArchivo', $codigoArchivoSalida);
            $stmt->bindParam('mensajeError', $mensajeErrorSalida);
            $stmt->execute();

            //se valida parametro de salida de error    
            if ($mensajeErrorSalida) {
                $registrosExcel = $em->getRepository('schemaBundle:InfoRegistroExcel')
                        ->findBy(array("codigoArchivo" => 0));
            } else {

                $codigoArchivoSalida = $codigoArchivoSalida + 0;
                $registrosExcel = $em->getRepository('schemaBundle:InfoRegistroExcel')
                        ->findBy(array("codigoArchivo" => $codigoArchivoSalida));
            }
            
            //se invoca a procedimiento  para generar archivo excel
            $this->generateExcelConsulta2($registrosExcel, $em, $em_general, $parametros, $peticion->getSession()->get('user'));
        } catch (\Exception $e) {
            $mensajeError = "Error: " . $e->getMessage();
        }
    }  
	
    public static function generateExcelConsulta($datos, $em, $em_general, $parametros, $usuario)
    {
		error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateReporteGeneral.xls");
       
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Reporte General de Planificacion");
        $objPHPExcel->getProperties()->setSubject("Reporte General de Planificacion");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de planificacion (general).");
        $objPHPExcel->getProperties()->setKeywords("Planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

		$objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['numOrdenServicio']=="")?'Todos': $parametros['numOrdenServicio']);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.($parametros['login2']=="")?'Todos': $parametros['login2']);
        $objPHPExcel->getActiveSheet()->setCellValue('E9',''.($parametros['descripcionPunto']=="")?'Todos': $parametros['descripcionPunto']);
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.($parametros['vendedor']=="")?'Todos': $parametros['vendedor']);
        $objPHPExcel->getActiveSheet()->setCellValue('E10',''.($parametros['ciudad']=="")?'Todos': $parametros['ciudad']);
        $objPHPExcel->getActiveSheet()->setCellValue('C11',''.($parametros['fechaDesdeSolPlanif']=="")?'Todos': $parametros['fechaDesdeSolPlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('C12',''.($parametros['fechaHastaSolPlanif']=="")?'Todos': $parametros['fechaHastaSolPlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('F11',''.($parametros['fechaDesdePlanif']=="")?'Todos': $parametros['fechaDesdePlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('F12',''.($parametros['fechaHastaPlanif']=="")?'Todos': $parametros['fechaHastaPlanif']);
		
        $i=16;		
        foreach ($datos as $data):
			$nombreProductoPlan = "";
			if(isset($data["idServicio"]))
			{
				if($data["idServicio"])
				{
					$Servicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($data["idServicio"]); 
					$nombreProducto =  ($Servicio->getProductoId() ? $Servicio->getProductoId()->getDescripcionProducto() : "");  
					$nombrePlan =  ($Servicio->getPlanId() ? $Servicio->getPlanId()->getNombrePlan() : "");  
					$nombreProductoPlan = $nombreProducto . $nombrePlan;
				}
			}
			$nombreVendedor = (isset($data["nombreVendedor"]) ?  ($data["nombreVendedor"] ? $data["nombreVendedor"] : "") : "");
			$nombreSector =  (isset($data["nombreSector"]) ? ($data["nombreSector"] ? $data["nombreSector"]  : "") : "");
			$parroquia =  (isset($data["nombreParroquia"]) ? ($data["nombreParroquia"] ? $data["nombreParroquia"]  : "") : "");
			$ciudad =  (isset($data["nombreCanton"]) ? ($data["nombreCanton"] ? $data["nombreCanton"]  : "") : "");
			$cliente = (isset($data["razonSocial"]) ||  isset($data["nombres"]) ? ($data["razonSocial"] ? $data["razonSocial"] : $data["nombres"] . " " . $data["apellidos"]) : "");
			$coordenadas = (isset($data["longitud"]) && isset($data["latitud"]) ? $data["longitud"] . ", ". $data["latitud"] : "");				          
			$latitud =  (isset($data["latitud"]) ? ($data["latitud"] ? $data["latitud"]  : "") : "");
			$longitud =  (isset($data["longitud"]) ? ($data["longitud"] ? $data["longitud"]  : ""): "");
			
			$feSolicitaPlanificacion = (isset($data["feCreacion"]) ? ($data["feCreacion"] ? strval(date_format($data["feCreacion"],"d/m/Y G:i")) : "" ) : "");    
			$fechaPlanificacionReal = "";
			$fePlanificada = "";
			$HoraIniPlanificada = "";
			$HoraFinPlanificada = "";
			$nombrePlanifica = "";
			if( strtoupper($data["estado"])==strtoupper("Planificada") || strtoupper($data["estado"])==strtoupper("Replanificada"))
			{
				$fePlanificada = (isset($data["feIniPlan"]) ? ($data["feIniPlan"] ? strval(date_format($data["feIniPlan"],"d/m/Y")) : "") : ""); 
				$HoraIniPlanificada = (isset($data["feIniPlan"]) ? ($data["feIniPlan"] ? strval(date_format($data["feIniPlan"],"h:i")) : "") : "");  
				$HoraFinPlanificada = (isset($data["feFinPlan"]) ? ($data["feFinPlan"] ? strval(date_format($data["feFinPlan"],"h:i")) : "") : ""); 
				
				//$usrPlanifica =  ($data["usrPlanifica"] ? $data["usrPlanifica"] : "");
				if(isset($data["usrPlanifica"]))
				{
					if($data["usrPlanifica"] && $data["usrPlanifica"]!="")
					{
						$entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneByLogin($data["usrPlanifica"]); 
						$nombrePlanifica = ($entityPersona ? $entityPersona->getNombres() . " " . $entityPersona->getApellidos() : "");
					}
				}
			
				$fechaPlanificacionReal = $fePlanificada . " (" . $HoraIniPlanificada . " - " . $HoraFinPlanificada . ")";
			}  
			
			$nombreMotivo = "";
			if(isset($data["motivoId"]))
			{
				if($data["motivoId"] && $data["motivoId"]!="")
				{
					$EntityMotivo = $em_general->getRepository('schemaBundle:AdmiMotivo')->findOneById($data["motivoId"]);
					$nombreMotivo =  ($EntityMotivo ? ($EntityMotivo->getNombreMotivo() ? $EntityMotivo->getNombreMotivo() : "") : "");  
				}
			}
				
			$idDetalleSolicitud =  (isset($data["idDetalleSolicitud"]) ? ($data["idDetalleSolicitud"] ? $data["idDetalleSolicitud"]  : 0) : 0); 
			$idServicio =  (isset($data["idServicio"]) ? ($data["idServicio"] ? $data["idServicio"]  : 0) : 0); 
			$idPunto =  (isset($data["idPunto"]) ? ($data["idPunto"] ? $data["idPunto"]  : 0) : 0); 
			$idOrdenTrabajo =  (isset($data["idOrdenTrabajo"]) ? ($data["idOrdenTrabajo"] ? $data["idOrdenTrabajo"]  : 0) : 0); 
			$numeroOrdenTrabajo =  (isset($data["numeroOrdenTrabajo"]) ? ($data["numeroOrdenTrabajo"] ? $data["numeroOrdenTrabajo"]  : "") : "");
			
			//agregar contactos
			$data['contactosTelefonosFijos'] = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findContactosByLoginAndFormaContacto($data['login'], 'Telefono Fijo');
			$data['contactosTelefonosMovil'] = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findContactosByLoginAndFormaContacto($data['login'], 'Telefono Movil');
			$data['contactosTelefonosMovilClaro'] = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findContactosByLoginAndFormaContacto($data['login'], 'Telefono Movil Claro');
			$data['contactosTelefonosMovilMovistar'] = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findContactosByLoginAndFormaContacto($data['login'], 'Telefono Movil Movistar');
			$data['contactosTelefonosMovilCnt'] = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findContactosByLoginAndFormaContacto($data['login'], 'Telefono Movil CNT');
			
			$numContactos = 0;
			$contactos ="";
			//////
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($numeroOrdenTrabajo));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($cliente));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($nombreVendedor));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($data["login"]));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($nombreProductoPlan));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($ciudad));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($coordenadas));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim($data["direccion"]));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, trim($nombreSector));
			
			if($data['contactosTelefonosFijos']){
				foreach($data['contactosTelefonosFijos'] as $contactosTelefonosFijos){
					if($numContactos==0)
						$contactos = $contactosTelefonosFijos['valor'];
					else	
						$contactos = $contactos." - ".$contactosTelefonosFijos['valor'];
					$numContactos++;	
				}
			}
			if($data['contactosTelefonosMovil']){
				foreach($data['contactosTelefonosMovil'] as $contactosTelefonosMovil){
					if($numContactos==0)
						$contactos = $contactosTelefonosMovil['valor'];
					else	
						$contactos = $contactos." - ".$contactosTelefonosMovil['valor'];
					$numContactos++;		
				}
			}
			if($data['contactosTelefonosMovilClaro']){
				foreach($data['contactosTelefonosMovilClaro'] as $contactosTelefonosMovilClaro){
					if($numContactos==0)
						$contactos = $contactosTelefonosMovilClaro['valor'];
					else	
						$contactos = $contactos." - ".$contactosTelefonosMovilClaro['valor'];
					$numContactos++;	
				}
			}
			if($data['contactosTelefonosMovilMovistar']){
				foreach($data['contactosTelefonosMovilMovistar'] as $contactosTelefonosMovilMovistar){
					if($numContactos==0)
						$contactos = $contactosTelefonosMovilMovistar['valor'];
					else	
						$contactos = $contactos." - ".$contactosTelefonosMovilMovistar['valor'];
					$numContactos++;	
				}
			}
			if($data['contactosTelefonosMovilCnt']){
				foreach($data['contactosTelefonosMovilCnt'] as $contactosTelefonosMovilCnt){
					if($numContactos==0)
						$contactos = $contactosTelefonosMovilCnt['valor'];
					else	
						$contactos = $contactos." - ".$contactosTelefonosMovilCnt['valor'];
					$numContactos++;	
				}
			}
			
			$tipoOrden = $Servicio->getTipoOrden();
			
			if($tipoOrden=="N")
				$tipoOrden = "Nueva";
			else if($tipoOrden=="R")
					$tipoOrden = "Reubicacion";
            else if($tipoOrden=="T")
					$tipoOrden = "Traslado";
			else
				$tipoOrden = "Nueva";
				
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $contactos);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $tipoOrden);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $feSolicitaPlanificacion.' '.$feSolicitaPlanificacion,'G:i');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $fechaPlanificacionReal);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, trim($nombrePlanifica));
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, trim($data["estado"]));
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim($nombreMotivo));
	    
	    if($data['tipoSolicitudId']){
            ///obtener el tipo de solicitud
             $admiTipoSolicitud=$em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($data['tipoSolicitudId']);
            // obtener admi tipo solicitud
             $descripcion =$admiTipoSolicitud->getDescripcionSolicitud();
              $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, trim($descripcion));
           // echo($descripcion); die();
           }    
	    
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

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_General_Planificacion_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //se crea funcion con nuevo funcionamiento para generar archivo excel
    public static function generateExcelConsulta2($datos, $em, $em_general, $parametros, $usuario)
    {
		error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateReporteGeneral.xls");
       
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Reporte General de Planificacion");
        $objPHPExcel->getProperties()->setSubject("Reporte General de Planificacion");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de planificacion (general).");
        $objPHPExcel->getProperties()->setKeywords("Planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

		$objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['numOrdenServicio']=="")?'Todos': $parametros['numOrdenServicio']);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.($parametros['login2']=="")?'Todos': $parametros['login2']);
        $objPHPExcel->getActiveSheet()->setCellValue('E9',''.($parametros['descripcionPunto']=="")?'Todos': $parametros['descripcionPunto']);
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.($parametros['vendedor']=="")?'Todos': $parametros['vendedor']);
        $objPHPExcel->getActiveSheet()->setCellValue('E10',''.($parametros['ciudad']=="")?'Todos': $parametros['ciudad']);
        $objPHPExcel->getActiveSheet()->setCellValue('C11',''.($parametros['fechaDesdeSolPlanif']=="")?'Todos': $parametros['fechaDesdeSolPlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('C12',''.($parametros['fechaHastaSolPlanif']=="")?'Todos': $parametros['fechaHastaSolPlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('F11',''.($parametros['fechaDesdePlanif']=="")?'Todos': $parametros['fechaDesdePlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('F12',''.($parametros['fechaHastaPlanif']=="")?'Todos': $parametros['fechaHastaPlanif']);
		
        $i=16;		
        foreach ($datos as $data):
            
            $numeroOrdenTrabajo = "";
            $cliente = "";
            $nombreVendedor = "";    
            $login = "";    
            $nombreProductoPlan = "";
            $ciudad = "";
            $coordenadas = "";        
            $direccion = "";
            $nombreSector = "";        
            $contactos = "";         
            $tipoOrden = ""; 
            $feSolicitaPlanificacion = ""; 
            $fechaPlanificacionReal = ""; 
            $nombrePlanifica = "";    
            $estado = "";         
            $nombreMotivo = "";         
            $descripcion = "";   
            
            
            //se realiza el seteo de los valores de las filas del archivo
            $numeroOrdenTrabajo =  $data->getNumeroOrdenTrabajo()."";
            $cliente = $data->getCliente()."";
            $nombreVendedor = $data->getNombreVendedor()."";
            $login =  $data->getLogin()."";
            $nombreProductoPlan =  $data->getNombreProductoPlan()."";
            $ciudad =  $data->getCiudad()."";
            $coordenadas = $data->getCoordenadas()."";
            $direccion =  $data->getDireccion()."";
            $nombreSector =  $data->getNombreSector()."";
            $contactos =  $data->getContactos()."";
            $tipoOrden =  $data->getTipoOrden()."";
            $feSolicitaPlanificacion = $data->getFecSolicitaPlanificacion()."";
            $fechaPlanificacionReal = $data->getFechaPlanificacionReal()."";
            $nombrePlanifica = $data->getNombrePlanifica()."";
            $estado = $data->getEstado()."";
            $nombreMotivo = $data->getNombreMotivo()."";
            $descripcion = $data->getDescripcion()."";

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, trim($numeroOrdenTrabajo));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, trim($cliente));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($nombreVendedor));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, trim($login));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, trim($nombreProductoPlan));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($ciudad));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($coordenadas));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim($direccion));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, trim($nombreSector));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $contactos);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $tipoOrden);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $feSolicitaPlanificacion.' '.$feSolicitaPlanificacion,'G:i');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $fechaPlanificacionReal);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, trim($nombrePlanifica));
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, trim($estado));
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim($nombreMotivo));
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, trim($descripcion));
            $i=$i+1;
        endforeach;
		
		

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_General_Planificacion_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
            }
