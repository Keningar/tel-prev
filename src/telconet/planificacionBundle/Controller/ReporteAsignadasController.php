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

class ReporteAsignadasController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_145-1")
	*/
    public function indexAction()
    {      
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_145-37'))
		{
	$rolesPermitidos[] = 'ROLE_145-37';
	}
	
	
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("145", "1");
        
        return $this->render('planificacionBundle:ReporteAsignadas:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
	* @Secure(roles="ROLE_145-7")
	*/
    public function gridAction()
    {
        
        $session  = $this->get( 'session' );
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
        $fechaDesdeAsignacion = explode('T',$peticion->query->get('fechaDesdeAsignacion'));
        $fechaHastaAsignacion = explode('T',$peticion->query->get('fechaHastaAsignacion'));	
        $estado =$peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";
		
        $parametros = array();
        $parametros["fechaDesdePlanif"]= $fechaDesdePlanif ? $fechaDesdePlanif[0] : "";
        $parametros["fechaHastaPlanif"]= $fechaHastaPlanif ? $fechaHastaPlanif[0] : "";
        $parametros["fechaDesdeAsignacion"]= $fechaDesdeAsignacion ? $fechaDesdeAsignacion[0] : "";
        $parametros["fechaHastaAsignacion"]= $fechaHastaAsignacion ? $fechaHastaAsignacion[0] : "";
        $parametros["login2"]=$peticion->query->get('login2') ? $peticion->query->get('login2') : "";	
        $parametros["descripcionPunto"]=$peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "";
        $parametros["vendedor"]=$peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "";
        $parametros["ciudad"]=$peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "";
        $parametros["numOrdenServicio"]=$peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "";
        $parametros["estado"]=$peticion->query->get('estado') ? $peticion->query->get('estado') : "Todos";
        $parametros["tipoResponsable"]=$peticion->query->get('tipoResponsable') ? $peticion->query->get('tipoResponsable') : "todos";
        $parametros["codigoResponsable"]=$peticion->query->get('codigoResponsable') ? $peticion->query->get('codigoResponsable') : 0;
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonReporteAsignadas($em, $em_general, $start, $limit, $estado, $parametros, $prefijoEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    } 
    
    
    
    /**
    * Funcion: exportarConsultaAction
    *
    * Se modifica la funcion con el objetivo de agilitar el proceso de exportar el Reporte de Asignadas,
    * ejecutando un procedimiento de la Base Datos: GeneraInformacionExcel.generarInfoExcelReporteAsigna
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 03-08-2015
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 08-07-2016 Se realiza ajustes para enviar el codigo de la empresa al paquete de BD que genera el reporte
    
	
	/**
	* @Secure(roles="ROLE_145-37")
	*/
    public function exportarConsultaAction()
    {
        
        $objSession        = $this->get( 'session' );
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $strCodigoEmpresa  = $objSession->get('idEmpresa');
        $objPeticion       = $this->get('request');

        $strFechaDesdePlanif      = explode('T',$objPeticion->query->get('fechaDesdePlanif'));
        $strFechaHastaPlanif      = explode('T',$objPeticion->query->get('fechaHastaPlanif'));
        $strFechaDesdeAsignacion  = explode('T',$objPeticion->query->get('fechaDesdeAsignacion'));
        $strFechaHastaAsignacion  = explode('T',$objPeticion->query->get('fechaHastaAsignacion'));	
        $strEstado                = $objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "Todos";

        $strFechaDesdePlanif      = $strFechaDesdePlanif[0]!="null" ? $strFechaDesdePlanif[0] : "";
        $strFechaHastaPlanif      = $strFechaHastaPlanif[0]!="null" ? $strFechaHastaPlanif[0] : "";
        $strFechaDesdeAsignacion  = $strFechaDesdeAsignacion[0]!="null" ? $strFechaDesdeAsignacion[0] : "";
        $strFechaHastaAsignacion  = $strFechaHastaAsignacion[0]!="null" ? $strFechaHastaAsignacion[0] : "";
        $strLogin2                = $objPeticion->query->get('login2') ? $objPeticion->query->get('login2') : "";	
        $strDescripcionPunto      = $objPeticion->query->get('descripcionPunto') ? $objPeticion->query->get('descripcionPunto') : "";
        $strVendedor              = $objPeticion->query->get('vendedor') ? $objPeticion->query->get('vendedor') : "";
        $strCiudad                = $objPeticion->query->get('ciudad') ? $objPeticion->query->get('ciudad') : "";
        $strNumOrdenServicio      = $objPeticion->query->get('numOrdenServicio') ? $objPeticion->query->get('numOrdenServicio') : "";        
        $strTipoResponsable       = $objPeticion->query->get('tipoResponsable') ? $objPeticion->query->get('tipoResponsable') : "todos";
        $strCodigoResponsable     = $objPeticion->query->get('codigoResponsable') ? $objPeticion->query->get('codigoResponsable') : 0;
        
        $arrayParametros                        = array();
        $arrayParametros["fechaDesdePlanif"]    = $strFechaDesdePlanif[0]!="null" ? $strFechaDesdePlanif[0] : "";
        $arrayParametros["fechaHastaPlanif"]    = $strFechaHastaPlanif[0]!="null" ? $strFechaHastaPlanif[0] : "";
        $arrayParametros["fechaDesdeAsignacion"]= $strFechaDesdeAsignacion[0]!="null" ? $strFechaDesdeAsignacion[0] : "";
        $arrayParametros["fechaHastaAsignacion"]= $strFechaHastaAsignacion[0]!="null" ? $strFechaHastaAsignacion[0] : "";
        $arrayParametros["login2"]              = $objPeticion->query->get('login2') ? $objPeticion->query->get('login2') : "";	
        $arrayParametros["descripcionPunto"]    = $objPeticion->query->get('descripcionPunto') ? $objPeticion->query->get('descripcionPunto') : "";
        $arrayParametros["vendedor"]            = $objPeticion->query->get('vendedor') ? $objPeticion->query->get('vendedor') : "";
        $arrayParametros["ciudad"]              = $objPeticion->query->get('ciudad') ? $objPeticion->query->get('ciudad') : "";
        $arrayParametros["numOrdenServicio"]    = $objPeticion->query->get('numOrdenServicio') ? $objPeticion->query->get('numOrdenServicio') : "";
        $arrayParametros["estado"]              = $objPeticion->query->get('estado') ? $objPeticion->query->get('estado') : "Todos";
        $arrayParametros["tipoResponsable"]     = $objPeticion->query->get('tipoResponsable') ? $objPeticion->query->get('tipoResponsable') : "todos";
        $arrayParametros["codigoResponsable"]   = $objPeticion->query->get('codigoResponsable') ? $objPeticion->query->get('codigoResponsable') : 0;
       
        $em                = $this->getDoctrine()->getManager("telconet");
        $em_general        = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
		
        $strEstado = ($strEstado ? $strEstado : "Todos");

        
        try {
            if (isset($strFechaDesdePlanif)) {
                if ($strFechaDesdePlanif && $strFechaDesdePlanif != "") {
                    $strDateF            = explode("-", $strFechaDesdePlanif);
                    $strFechaSql         = date("d/m/Y", strtotime($strDateF[0] . "-" . $strDateF[1] . "-" . $strDateF[2]));
                    $strFechaDesdePlanif = $strFechaSql;
                }
            }
            if (isset($strFechaHastaPlanif)) {
                if ($strFechaHastaPlanif && $strFechaHastaPlanif != "") {
                    $strDateF            = explode("-", $strFechaHastaPlanif);
                    $strFechaSqlAdd      = strtotime(date("d-m-Y", strtotime($strDateF[0] . "-" . $strDateF[1] . "-" . $strDateF[2])) . " +1 day");
                    $strFechaSql         = date("d/m/Y", $strFechaSqlAdd);
                    $strFechaHastaPlanif = $strFechaSql;
                }
            }
            if (isset($strFechaDesdeAsignacion)) {
                if ($strFechaDesdeAsignacion && $strFechaDesdeAsignacion != "") {
                    $strDateF                = explode("-", $strFechaDesdeAsignacion);
                    $strFechaSql             = date("d/m/Y", strtotime($strDateF[0] . "-" . $strDateF[1] . "-" . $strDateF[2]));
                    $strFechaDesdeAsignacion = $strFechaSql;
                }
            }
            if (isset($strFechaHastaAsignacion)) {
                if ($strFechaHastaAsignacion && $strFechaHastaAsignacion != "") {
                    $strDateF                = explode("-", $strFechaHastaAsignacion);
                    $strFechaSqlAdd          = strtotime(date("d-m-Y", strtotime($strDateF[0] . "-" . $strDateF[1] . "-" . $strDateF[2])) . " +1 day");
                    $strFechaSql             = date("d/m/Y", $strFechaSqlAdd);
                    $strFechaHastaAsignacion = $strFechaSql;
                }
            }            

             $arrayParametrosBD                       = array();
             $arrayParametrosBD['estado']             = $strEstado;
             $arrayParametrosBD['fechaDesdeSolPlani'] = $strFechaDesdePlanif;
             $arrayParametrosBD['fechaHastaSolPlani'] = $strFechaHastaPlanif;
             $arrayParametrosBD['fechaDesdePlani']    = $strFechaDesdeAsignacion;
             $arrayParametrosBD['fechaHastaPlani']    = $strFechaHastaAsignacion;
             $arrayParametrosBD['login2']             = $strLogin2;
             $arrayParametrosBD['descripcionPunto']   = $strDescripcionPunto;
             $arrayParametrosBD['vendedor']           = $strVendedor;             
             $arrayParametrosBD['ciudad']             = $strCiudad;
             $arrayParametrosBD['numOrdenServicio']   = $strNumOrdenServicio;
             $arrayParametrosBD['tipoResponsable']    = $strTipoResponsable;
             $arrayParametrosBD['codigoResponsable']  = $strCodigoResponsable;             
             $arrayParametrosBD['prefijoEmpresa']     = $strPrefijoEmpresa;
             $arrayParametrosBD['codigoEmpresa']      = $strCodigoEmpresa;

             $respuesta = array();
             $respuesta = $this->getDoctrine()
                                ->getManager("telconet")
                                ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                ->generarRegistrosAsignadas($arrayParametrosBD);
             

             $arrayParametros["fechaDesdePlanif"]    = $strFechaDesdePlanif;
             $arrayParametros["fechaHastaPlanif"]    = $strFechaHastaPlanif;
             $arrayParametros["fechaDesdeAsignacion"]= $strFechaDesdeAsignacion;
             $arrayParametros["fechaHastaAsignacion"]= $strFechaHastaAsignacion;
             
             
             $strCodigoArchivoSalida = $respuesta['identificadorArchivo'];
             $strMensajeErrorSalida = $respuesta['mensajeError'];
             

             //se valida parametro de salida de error                
             if (!$strMensajeErrorSalida) {
                 //se suma + 0 para convertir el parametro en un entero
                 $strCodigoArchivoSalida = $strCodigoArchivoSalida + 0;
                 $strRegistrosExcel      = $em->getRepository('schemaBundle:InfoRegistroExcel')
                                             ->findBy(array("codigoArchivo" => $strCodigoArchivoSalida));
             } 
            
            //se invoca a procedimiento  para generar archivo excel
            $this->generateExcelConsulta($strRegistrosExcel,
                                          $arrayParametros, 
                                          $objPeticion->getSession()->get('user'),
                                          $strPrefijoEmpresa);
        
            
            } catch (\Exception $e) {
                $mensajeError = "Error: " . $e->getMessage();
        }
            

    }       
    
    /**
    * Funcion: generateExcelConsulta2
    *
    * Se crea esta funcion para exportar el Reporte de Asignadas a Excel, que va a ser llamada por
    * la funcion exportarConsultaAction
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 03-08-2015
    *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.1 10-04-2023 - Se agrega validacion por prefijo empresa para Ecuanet.
    *
    */
    
    public static function generateExcelConsulta($datos,$parametros,$usuario,$prefijoEmpresa)
    {
		error_reporting(E_ALL);

        $objPHPExcel      = new PHPExcel();
       
        $objCacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);
        $objReader   = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateReporteAsignadas".$prefijoEmpresa.".xls");        

        $strDateF    = explode("/",$parametros['fechaDesdePlanif']);
        $strFechaSql = date("Y-m-d", strtotime($strDateF[2]."-".$strDateF[1]."-".$strDateF[0]));

        if(isset($parametros["fechaDesdePlanif"]))
		{
	        if($parametros["fechaDesdePlanif"] && $parametros["fechaDesdePlanif"]!="")
	        {
                $strDateF            = explode("/",$parametros['fechaDesdePlanif']);
                $strFechaSql         = date("Y-m-d", strtotime($strDateF[2]."-".$strDateF[1]."-".$strDateF[0]));
                $strFechaDesdePlanif = trim($strFechaSql);    
	        }
		}
        
        if(isset($parametros["fechaHastaPlanif"]))
		{
	    if($parametros["fechaHastaPlanif"] && $parametros["fechaDesdePlanif"]!="")
	        {
                $strDateF            = explode("/",$parametros['fechaHastaPlanif']);
                $strFechaSql         = date("Y-m-d", strtotime($strDateF[2]."-".$strDateF[1]."-".$strDateF[0]));
                $strFechaHastaPlanif = trim($strFechaSql);    
	        }
		}
        
        if(isset($parametros["fechaDesdeAsignacion"]))
		{
	        if($parametros["fechaDesdeAsignacion"] && $parametros["fechaDesdeAsignacion"]!="")
	        {
                $strDateF                = explode("/",$parametros['fechaDesdeAsignacion']);
                $strFechaSql             = date("Y-m-d", strtotime($strDateF[2]."-".$strDateF[1]."-".$strDateF[0]));
                $strFechaDesdeAsignacion = trim($strFechaSql);    
	        }
		}
        
        if(isset($parametros["fechaHastaAsignacion"]))
		{
	        if($parametros["fechaHastaAsignacion"] && $parametros["fechaHastaAsignacion"]!="")
	        {
                $strDateF                = explode("/",$parametros['fechaHastaAsignacion']);
                $strFechaSql             = date("Y-m-d", strtotime($strDateF[2]."-".$strDateF[1]."-".$strDateF[0]));
                $strFechaHastaAsignacion = trim($strFechaSql);    
	        }
		}

        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Reporte Asignadas de Planificacion");
        $objPHPExcel->getProperties()->setSubject("Reporte Asignadas de Planificacion");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de planificacion (Asignadas).");
        $objPHPExcel->getProperties()->setKeywords("Planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('B4',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('B5', strval(date_format(new \DateTime('now'), "d/m/Y")) );
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('E10',''.($parametros['estado']=="")?"Todos":$parametros['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['login2']=="")?'Todos': $parametros['login2']);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',''.($parametros['descripcionPunto']=="")?'Todos': $parametros['descripcionPunto']);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',''.($parametros['vendedor']=="")?'Todos': $parametros['vendedor']);
        $objPHPExcel->getActiveSheet()->setCellValue('E9',''.($parametros['ciudad']=="")?'Todos': $parametros['ciudad']);
        $objPHPExcel->getActiveSheet()->setCellValue('B11',''.($strFechaDesdePlanif=="")?'Todos': $strFechaDesdePlanif);
        $objPHPExcel->getActiveSheet()->setCellValue('C11',''.($strFechaHastaPlanif=="")?'Todos': $strFechaHastaPlanif);
        $objPHPExcel->getActiveSheet()->setCellValue('E11',''.($strFechaDesdeAsignacion=="")?'Todos': $strFechaDesdeAsignacion);
        $objPHPExcel->getActiveSheet()->setCellValue('F11',''.($strFechaHastaAsignacion=="")?'Todos': $strFechaHastaAsignacion);
		

        $i=16;		
        foreach ($datos as $arrayData):


            $strDescripcionSolicitud    = "";
            $strCliente                 = "";
            $strNombreVendedor          = "";
            $strNombreProductoPlan      = "";
            $strTipoOrden               = "";
            $strLogin                   = "";
            $strCoordenadas             = "";
            $strCiudad                  = "";
            $strDireccion               = "";
            $strNombreSector            = "";
            $strObservacion             = "";
            $strTelefonos               = "";
            $strFechaPlanificacionReal  = "";
			$strFeAsignada              = "";
			$strNombreTarea             = "";
            $strNombreAsigna            = "";
            $strObservacionSolicitud    = "";
            $strAsignado                = "";                        
            $strNombreElemento          = "";
			$strNombreInterface         = "";
			$strIpsCliente              = "";
			$strEstado                  = "";
			$strCaja                    = "";
			$strSplitter                = "";
			$intSplitter                = "";
             
            $strDescripcionSolicitud    =  $arrayData->getDescripcion()."";
            $strCliente                 =  $arrayData->getCliente()."";
            $strNombreVendedor          =  $arrayData->getNombreVendedor()."";
            $strNombreProductoPlan      =  $arrayData->getNombreProductoPlan()."";
            $strTipoOrden               =  $arrayData->getTipoOrden()."";
            $strLogin                   =  $arrayData->getLogin()."";
            $strCoordenadas             =  $arrayData->getCoordenadas()."";
            $strCiudad                  =  $arrayData->getCiudad()."";
            $strDireccion               =  $arrayData->getDireccion()."";
            $strNombreSector            =  $arrayData->getNombreSector()."";
            $strObservacion             =  $arrayData->getObservacion()."";
            $strTelefonos               =  $arrayData->getContactos()."";
            $strFechaPlanificacionReal  =  $arrayData->getFechaPlanificacionReal()."";
			$strFeAsignada              =  $arrayData->getFecSolicitaPlanificacion()."";
			$strNombreTarea             =  $arrayData->getNombreTarea()."";
            $strNombreAsigna            =  $arrayData->getNombreAsigna()."";
            $strObservacionSolicitud    =  $arrayData->getObservacionSolicitud()."";
            $strAsignado                =  $arrayData->getAsignado()."";                     
            $strNombreElemento          =  $arrayData->getNombreElemento()."";
            $strNombreInterface         =  $arrayData->getNombreInterface()."";
			$strIpsCliente              =  $arrayData->getIpsCliente()."";
			$strEstado                  =  $arrayData->getEstado()."";
			$strCaja                    =  $arrayData->getCaja()."";
			$strSplitter                =  $arrayData->getSplitter()."";
			$intSplitter                =  $arrayData->getIdSplitter()."";
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$strDescripcionSolicitud);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $strCliente);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $strNombreVendedor);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $strNombreProductoPlan);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $strTipoOrden);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $strLogin);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $strCoordenadas);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $strCiudad);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $strDireccion);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $strNombreSector);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $strObservacion);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $strTelefonos);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $strFechaPlanificacionReal);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $strFeAsignada);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $strNombreTarea);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $strNombreAsigna);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $strObservacionSolicitud);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $strAsignado);
            if($prefijoEmpresa=="TTCO"){
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $strNombreElemento);
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $strNombreInterface);
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $strIpsCliente);
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $strEstado);
            }
            if($prefijoEmpresa=="MD" || $prefijoEmpresa=="EN"){
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $strCaja);
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $strSplitter);
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $intSplitter);
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $strIpsCliente);    
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $strEstado);
            }
            
            $i=$i+1;
        endforeach;
				        
        // Merge cells
        // Set document security
        $objPHPExcel->getSecurity()->setWorkbookPassword("PHPExcel");
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Asignadas_Planificacion_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter ->save('php://output');
        exit;
    }

}