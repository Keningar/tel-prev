<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;


/**
  * Clase EstadoEnviosController
  *
  * Clase que se encarga ir mostrando la gestion de envios correos/sms despachados 
  * verificando estados
  *
  * @author Allan Suárez C. <arsuarez@telconet.ec>
  * @version 1.0 29-08-2014
  */    

class EstadoEnviosController extends Controller implements TokenAuthenticatedController
{
     /**
     * indexAction
     *
     * Metodo encargado de dirigir a la pagina principal del CRUD y ver los documentos creados,
     * Recibe el modulo para determinar que index o busqueda mostrar en cada modulo          
     *
     * @return index
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */
       
    public function indexAction()
    {    		        
        $verLog = false;
        
        if (true === $this->get('security.context')->isGranted('ROLE_258-1717'))
        {
                $verLog = true;
        }
        
    	return $this->render('soporteBundle:estado_envios:index.html.twig',array(	 
			      'verLog'=>$verLog
			));			    
    }               
         
    /**
     * gridAction
     *
     * Metodo encargado de invocar al Service que se encarag de mostrar los documentos
     * relacionados al modulo que sean invocados          
     *
     * @return json con informacion a mostrar
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */      
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em  = $this->get('doctrine')->getManager('telconet_comunicacion');   

        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $arrayConexion = array();
        
        $arrayConexion['dsn']    = $this->container->getParameter('database_dsn');
        $arrayConexion['user']   = $this->container->getParameter('user_comunicacion');
        $arrayConexion['pass']   = $this->container->getParameter('passwd_comunicacion');

        $arrayParametros = array();
        
        $strNombre = $peticion->query->get('nombre');
        $strEstado = $peticion->query->get('estado');
        $strClase  = $peticion->query->get('clase');
        $strTipo   = $peticion->query->get('tipo');

        if($strNombre ==! "")
        {
            $arrayParametros["nombre"]  = $strNombre;
        }
        else 
        {
            $arrayParametros["nombre"]  = null;
        }
        if(strcmp($strEstado, "Todos") !== 0)
        {
            $arrayParametros["estado"]  = $strEstado;
        }
        else
        {
            $arrayParametros["estado"]  = null;
        }
        if(strcmp($strClase, "Todos") !== 0)
        {
            $arrayParametros["clase"]  = $strClase;
        }
        else
        {
            $arrayParametros["clase"]  = null;
        }
        if(strcmp($strTipo, "Todos") !== 0)
        {
            $arrayParametros["tipo"]  = $strTipo;
        }
        else{
            $arrayParametros["tipo"]  = null;
        }         
        
        $arrayParametros['empresa'] = $session->get('idEmpresa');
          
        if($arrayParametros["clase"])
        {
            $objClaseDocumento = $em->getRepository('schemaBundle:AdmiClaseDocumento')
                        ->findOneByNombreClaseDocumento(array('nombreClaseDocumento'=>'Notificacion Externa '.$arrayParametros["clase"]));
            if($objClaseDocumento)
            {
                $arrayParametros["clase"] = $objClaseDocumento->getId();            
            }
        }
        
        $strIsOcupado = $em->getRepository('schemaBundle:InfoDocumentoComunMasiva')->getEstadoEquipo();
        
        $arrayParametros['isOcupado'] = $strIsOcupado;
                
        $intStart = $peticion->query->get('start');
        $intLimit = $peticion->query->get('limit');  
        
        try{
       
            $objJson = $em->getRepository('schemaBundle:InfoDocumentoComunMasiva')
                          ->getEstadoEnvios($arrayParametros, $arrayConexion ,$intStart, $intLimit);             
        
        }catch(\Exception $ex)
        {
             throw new \Exception($ex->getMessage());                         
        }

        $respuesta->setContent($objJson);
        return $respuesta;
    }   
    
    /**
     * reenviarAction
     *
     * Metodo encargado de colocar el envio en estado Pendiente para que sea ejecutado por el
     * script de envio Pendientes
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */   
    public function reenviarAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');

        $intIdComun = $peticion->get('idComun');

        try
        {

            $objdocumentoComHistorial = $emComunicacion->getRepository("schemaBundle:InfoDocumentoComunHistorial")
                ->findOneBy(array('documComunMasivaId' => $intIdComun));

            $emComunicacion->getConnection()->beginTransaction();

            $objdocumentoComHistorial->setEstado('Reenvio');
            $objdocumentoComHistorial->setObservacion('Comunicacion Pasa a estado Reenvio');
            $objdocumentoComHistorial->setSeguimiento('No Procesado');

            $emComunicacion->persist($objdocumentoComHistorial);
            $emComunicacion->flush();
            $emComunicacion->getConnection()->commit();
            
            //Se ejecuta script de envios Pendientes/Programados
                        
            $path = $this->container->getParameter('path_telcos');
            $hostScripts = $this->container->getParameter('host_scripts');
            $strPathJava = $this->container->getParameter('path_java_soporte');
            $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');
            $tipo = 'Reenvio'; 
            
            $strParametros = $hostScripts . "|" .$path. "|" .$tipo;
            
            $strRutaScript = "/home/scripts-telcos/md/soporte/sources/telcos-envio-pendientes/dist/TelcosEnvioPendientes.jar";
            $strEsperaRespuesta = "NO";
        
            $strComunicacion = "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar";
            $strLogScript = "/home/telcos/app/Resources/scripts/log/log.txt";
            $strSecurity = "-Djava.security.egd=file:/dev/./urandom";

            //Se llama a Script que comunica via SSH             
            $strComando = "nohup ".$strPathJava." -jar " .$strSecurity. " " . $path . $strComunicacion. " '" . $strRutaScript . "' ".
                       " '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $hostScripts . "' '".
                       $strScriptPathJava."' >> " .$strLogScript." &";  
            
            shell_exec($strComando);                                                

            $resultado = json_encode(array('success' => true, 'mensaje' => 'Comunicacion se coloca en cola de envío'));
        }
        catch(\Exception $ex)
        {
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $resultado = json_encode(array('success' => false, 'mensaje' => $ex->getMessage()));
        }

        $respuesta->setContent($resultado);
        return $respuesta;
    }

     /**
     * cancelarEnvioAction
     *
     * Metodo encargado de cancelar los envios en estado Pendientes o Programados
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */  
    public function cancelarEnvioAction()
    {

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');

        $intIdComun = $peticion->get('idComun');

        try
        {

            $objdocumentoComHistorial = $emComunicacion->getRepository("schemaBundle:InfoDocumentoComunHistorial")
                ->findOneBy(array('documComunMasivaId' => $intIdComun));

            $emComunicacion->getConnection()->beginTransaction();


            $objdocumentoComHistorial->setEstado('Cancelado');
            $objdocumentoComHistorial->setObservacion('Envio Pendiente/Programado es cancelado');

            $emComunicacion->persist($objdocumentoComHistorial);
            $emComunicacion->flush();
            $emComunicacion->getConnection()->commit();

            $resultado = json_encode(array('success' => true, 'mensaje' => 'Envio Pendiente/Programado Cancelado'));
        }
        catch(\Exception $ex)
        {
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $resultado = json_encode(array('success' => false, 'mensaje' => $ex->getMessage()));
        }

        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
     /**
     * cancelarEjecucionAction
     *
     * Metodo encargado de cancelar los envios con estado Enviando/Conectando en caso
     * de existir un problema en el proceso
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 15-10-2014
     *     
     * @Secure(roles="ROLE_258-1717")
     */ 
    public function cambiarEstadoAction()
    {

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $emComunicacion->getConnection()->beginTransaction();

        $intIdComun      = $peticion->get('id');
        $strEstadoCambio = $peticion->get('estado');

        try
        {

            $objdocumentoComHistorial = $emComunicacion->getRepository("schemaBundle:InfoDocumentoComunHistorial")
                ->findOneBy(array('documComunMasivaId' => $intIdComun));            

            $objdocumentoComHistorial->setEstado($strEstadoCambio);
            $objdocumentoComHistorial->setObservacion('Comunicacion en estado '.$strEstadoCambio);

            $emComunicacion->persist($objdocumentoComHistorial);
            $emComunicacion->flush();
            $emComunicacion->getConnection()->commit();

            $resultado = json_encode(array('success' => true, 'mensaje' => 'Estado Cambiado a '.$strEstadoCambio));
        }
        catch(\Exception $ex)
        {
            if ($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
            $resultado = json_encode(array('success' => false, 'mensaje' => $ex->getMessage()));
        }

        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * desconectarEquipoAction
     *
     * Metodo encargado cambiar el campo EQUIPO_OCUPADO DE 'S' A 'N' cuando algún proceso falle
     * y poder seguir con el flujo normal
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 15-10-2014
     *      
     * @Secure(roles="ROLE_258-1717")
     */ 
    public function desconectarEquipoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');       

        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');  
        $emComunicacion->getConnection()->beginTransaction();

        try
        {

            $arrayInfoDocComHist = $emComunicacion->getRepository('schemaBundle:InfoDocumentoComunMasiva')->getRegistroEquipoOcupado();
                        
            if($arrayInfoDocComHist)
            {
                foreach($arrayInfoDocComHist as $obj)
                {                    
                    $obj->setEquipoOcupado('N');
                    $emComunicacion->persist($obj);
                    $emComunicacion->flush();
                }
            }
           
            $emComunicacion->getConnection()->commit();

            $resultado = json_encode(array('success' => true, 'mensaje' => 'Equipo Desconectado'));
        }
        catch(\Exception $ex)
        {
            if ($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
            $resultado = json_encode(array('success' => false, 'mensaje' => $ex->getMessage()));
        }

        $respuesta->setContent($resultado);
        return $respuesta;
    }

     /**
     * exportarNoEnviadosAction
     *
     * Metodo encargado invocar metodo que realiza la exportacion de clientes que no pudieron ser notificados
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */  
    public function exportarNoEnviadosAction()
    {
        ini_set('max_execution_time', 3000000);       

        $peticion = $this->get('request');
        $session  = $peticion->getSession();

        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');         

        $intComunicacionId = $peticion->get('hid') ? $peticion->get('hid') : "";
        
        $arrayObjComunicacionMasiva = $emComunicacion->getRepository("schemaBundle:InfoDocumentoComunMasiva")
                                                ->getNotificacionesSinEnviar($intComunicacionId);
        
        $objComunicacionMasiva = $emComunicacion->getRepository("schemaBundle:InfoDocumentoComunMasiva")
                                                ->find($arrayObjComunicacionMasiva[0]->getId());
                
        $intDocumentoId   = 0;
        
        $arrayParametros = array();
        
        if($objComunicacionMasiva)
        {
            $arrayParametros['puntos'] = $objComunicacionMasiva->getPuntosEnviar();
            $arrayParametros['tipo']   = $objComunicacionMasiva->getTipoEnvio();
            $arrayParametros['fecha']  = $objComunicacionMasiva->getFeCreacion();
            
            $intDocumentoId    = $objComunicacionMasiva->getDocumentoId()->getId();
            
            if($intDocumentoId)
            {
                $objDocumento = $emComunicacion->getRepository("schemaBundle:InfoDocumento")->find($intDocumentoId);
                
                if($objDocumento)
                {
                    $arrayParametros['documento'] = $objDocumento->getNombreDocumento();
                    $arrayParametros['clase']     = $objDocumento->getClaseDocumentoId()->getId();
                }
                
            }
        }
       
        $this->exportarContactosNoEnviados($arrayParametros,$emComercial,$session);
    }

     /**
     * exportarContactosNoEnviados
     *
     * Metodo encargado de generar el Excel con los clientes que no pudieron ser notificados
     * 
     * @return json con mensaje
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-08-2014
     */  
    public function exportarContactosNoEnviados($arrayParametros, $emComercial, $session)
    {

         error_reporting(E_ALL);
         ini_set('max_execution_time', 3000000);               

        $objPHPExcel = new PHPExcel();                

        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        //Variables de Sesion
        $strUsuario = $session->get('user');
        
        //Puntos a enviar        
        $strPuntos = $arrayParametros['puntos'];
        $arrayPuntos = explode("-",$strPuntos);
        
        $intNumeroNoEnviados = sizeof($arrayPuntos);

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateConsultaNoEnviados.xls");
        
        //$objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Clientes sin envio");
        $objPHPExcel->getProperties()->setSubject("Consulta de Clientes sin envio");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda.");
        $objPHPExcel->getProperties()->setKeywords("No Enviados");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3', $strUsuario);
        
        $strFechaCreacion = date_format($arrayParametros['fecha'], 'Y-m-d H:i:s');

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', '' . ($arrayParametros['documento']? $arrayParametros['documento']:''));
        $objPHPExcel->getActiveSheet()->setCellValue('B9', '' . ($arrayParametros['tipo']? $arrayParametros['tipo']:''));
        $objPHPExcel->getActiveSheet()->setCellValue('B10','' . $intNumeroNoEnviados." Puntos");
        $objPHPExcel->getActiveSheet()->setCellValue('B11','' . $strFechaCreacion);
        
        $i = 15;

        foreach($arrayPuntos as $punto):

            $arrayDatosPunto = $emComercial->getRepository("schemaBundle:InfoPuntoFormaContacto")->getDatosPunto($punto);
        
            $strNombre = "";
            $strLogin  = "";
        
            if($arrayDatosPunto)
            {
                $strNombre = $arrayDatosPunto[0]['nombre'];
                $strLogin  = $arrayDatosPunto[0]['login'];
                
                $strContactoMovilErroneos = '';
                $strContactoFijosErroneos = '';
                
                //Bloque solo se ejecuta cuando los envios son via SMS
                if($arrayParametros['clase']!=1)
                {
                
                    $strContactoMovilErroneos = $emComercial->getRepository("schemaBundle:InfoPuntoFormaContacto")
                                               ->getContactosPorFormasContactoId($punto,"MOVIL");

                    $strContactoFijosErroneos = $emComercial->getRepository("schemaBundle:InfoPuntoFormaContacto")
                                               ->getContactosPorFormasContactoId($punto,"FIJO");
                
                }else
                {
                    $strContactoFijosErroneos = 'Sin Correo';
                    $strContactoMovilErroneos = 'Sin Correo';
                }
                
                
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $strNombre);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $strLogin);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $strContactoMovilErroneos);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $strContactoFijosErroneos);
                
                $i = $i + 1;
                
            }

          
           
            $emComercial->clear();

        endforeach;

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
             
        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_clientes_sin_envio_' . date('d_M_Y') . '.xls"');
        header('Cache-Control: max-age=0');
         
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
                
    }
    
     /**
     * ajaxConsultarLogEjecucionAction
     *
     * Metodo encargado de generar consulta de log ejecucion del envio actual
     * 
     * @return string con log a mostrar
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 09-09-2014
     */  
    public function ajaxConsultarLogEjecucionAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');       
        $strTipoLog = $peticion->get('tipo');
        
        $strNombreLog = '';
                
        if($strTipoLog == 'inmediato')
        {
            $strNombreLog = '/home/scripts-telcos/md/soporte/logs/envio-notificaciones-externas-masivas/EnvioNotificacionesMasivas.log';
        }
        else if($strTipoLog == 'pendiente')
        {
            $strNombreLog = '/home/scripts-telcos/md/soporte/logs/telcos-envio-pendientes/TelcosEnvioPendientes.log';
        }
        else
        {
            $strNombreLog = '/home/scripts-telcos/md/soporte/logs/sms-server/SmsServer.log';
        }
                                      
        $strPathLog = $strNombreLog;
        
        $cmd = 'tail -20 '.$strPathLog;
        $output = shell_exec($cmd);
        
        $respuesta->setContent(nl2br($output));
        
        return $respuesta;
    }

}