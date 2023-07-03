<?php

namespace telconet\comercialBundle\Controller;
use telconet\schemaBundle\Entity\InfoReporteHistorial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * 
 * ReportesDeVendedoresController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Reportes de Pagos por  Vendedor
 *
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0 03-10-2016
 */
class ReportePagosVendedorController extends Controller
{

   /**
    * @Secure(roles="ROLE_361-1")
    * 
    * Documentación para el método 'indexAction'.
    *
    * Redirecciona al twig  de consulta de pagos por vendedor.
    *
    * @return render 
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 03-10-2016
    */
    public function indexAction()
    {
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("361", "1");
        return $this->render('comercialBundle:ReportePagosVendedor:index.html.twig', array('item' => $entityItemMenu));        
    }
    
   /**
    * @Secure(roles="ROLE_361-1") 
    *
    * Documentación para el método 'gridReportePagosVendedorAction'.
    *
    * Muestra la pantalla de consulta de pagos por vendedor.
    *
    * @return Response 
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 03-10-2016
    */
    public function gridPagosVendedorAction()
    {
        $objResponse    = new Response();
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();  
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSession->get('user');        
        $emFinanciero   = $this->getDoctrine()->getManager('telconet_financiero');        
        $strDns         = $this->container->getParameter('database_dsn');      
        $objOciCon      = oci_connect(
                                        $this->container->getParameter('user_financiero'),
                                        $this->container->getParameter('passwd_financiero'), 
                                        $strDns
                                     );          
        $objCursor       = oci_new_cursor($objOciCon); 
        $arrayParametros = array();
                              
        $objResponse->headers->set('Content-Type', 'text/json');
        

        $arrayParametros['strEmailUsrSesion'] = ""; 
        $arrayParametros['strFechaDesde']     = "";
        $arrayParametros['strFechaHasta']     = "";             
        $arrayParametros['intEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);
        $arrayParametros['strPrefijoEmpresa'] = trim($objSession->get('prefijoEmpresa'));     
        $arrayParametros['strIdentificacion'] = trim($objRequest->get("identificacion"));
        $arrayParametros['strRazonSocial']    = trim($objRequest->get("razonSocial"));
        $arrayParametros['strNombres']        = trim($objRequest->get("nombre"));
        $arrayParametros['strApellidos']      = trim($objRequest->get("apellido"));
        $arrayParametros['intStart']          = $objRequest->query->get('start');
        $arrayParametros['intLimit']          = $objRequest->query->get('limit');       
        $arrayParametros['oci_con']           = $objOciCon;  
        $arrayParametros['cursor']            = $objCursor; 
     
        $arrayFechaDesde                      = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta                      = explode('T', $objRequest->get("fechaHasta")); 
                 
       
        $strFechaCreacionDesde = (isset($arrayFechaDesde) ? $arrayFechaDesde[0] : 0);
        $strFechaCreacionHasta = (isset($arrayFechaHasta) ? $arrayFechaHasta[0] : 0);
                   
        if($strFechaCreacionDesde && $strFechaCreacionDesde != "0")
        {
            $arrayParametros['strFechaDesde'] = date_format(date_create($strFechaCreacionDesde), "d/m/Y");

        }
        if($strFechaCreacionHasta && $strFechaCreacionHasta != "0")
        {
            $arrayParametros['strFechaHasta'] = date_format(date_create($strFechaCreacionHasta." +1 day"), "d/m/Y");     
        } 
        
        try
        {        
            $objJsonPagosVendedor  = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                  ->getJsonPagosPorVendedor($arrayParametros);          
        
            $objResponse->setContent($objJsonPagosVendedor);
        
        }
        catch (\Exception $e) 
        {   
            error_log($e->getMessage());
            $strMensaje= 'Error al generar reporte .';
            $serviceUtil->insertError('Telcos+', 
                                      'ReportePagosVendedorController.gridPagosVendedorAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
        }        
        
        return $objResponse;
    }
    
   /**
    * @Secure(roles="ROLE_361-1")
    * 
    * generarReportePagosVendedorAction
    * Metodo que permite enviar los parametros para la generación y envío del reporte via mail .
    *  
    * @return Response 
    * 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 16-09-2016 
    */     
    public function generarReportePagosVendedorAction()
    {
        $objResponse  = new Response();
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession(); 
        $serviceUtil  = $this->get('schema.Util');
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSession->get('user');           
        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');    
                              
        $objResponse->headers->set('Content-Type', 'text/json');
        $strValorFormaContacto                = ""; 
        $arrayParametros                      = array();
        $arrayParametros['strEmailUsrSesion'] = "";  
        $arrayParametros['strFechaDesde']     = "";
        $arrayParametros['strFechaHasta']     = "";                
        $arrayParametros['intEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);
        $arrayParametros['strPrefijoEmpresa'] = trim($objSession->get('prefijoEmpresa'));     
        $arrayParametros['strIdentificacion'] = trim($objRequest->get("identificacion"));
        $arrayParametros['strRazonSocial']    = trim($objRequest->get("razonSocial"));
        $arrayParametros['strNombres']        = trim($objRequest->get("nombre"));
        $arrayParametros['strApellidos']      = trim($objRequest->get("apellido"));

        $arrayFechaDesde                      = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta                      = explode('T', $objRequest->get("fechaHasta"));        
        
        $strFechaCreacionDesde                = (isset($arrayFechaDesde) ? $arrayFechaDesde[0] : 0);
        $strFechaCreacionHasta                = (isset($arrayFechaHasta) ? $arrayFechaHasta[0] : 0);

        if($strFechaCreacionDesde && $strFechaCreacionDesde != "0")
        {
            $arrayParametros['strFechaDesde'] = date_format(date_create($strFechaCreacionDesde), "d/m/Y");

        }
        if($strFechaCreacionHasta && $strFechaCreacionHasta != "0")
        {
            $arrayParametros['strFechaHasta'] = date_format(date_create($strFechaCreacionHasta." +1 day"), "d/m/Y");     
        } 
        
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objSession->get('idPersonaEmpresaRol'));

        if(is_object($objInfoPersonaEmpresaRol))
        {

            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');
                    
            if(!is_null($strValorFormaContacto))
            {
                $arrayParametros['strEmailUsrSesion'] = strtolower($strValorFormaContacto);
            }
        }
        
        $emFinanciero->getConnection()->beginTransaction();
        $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->generarReportePagosPorVendedor($arrayParametros); 
        $strMensaje = 'Se envio a generar el reporte.';
        try
        {
            
            // Registro de historial de generación de reporte
            $objInfoReporteHistorial = new InfoReporteHistorial();
            $objInfoReporteHistorial->setEmpresaCod(trim($arrayParametros['strPrefijoEmpresa']));
            $objInfoReporteHistorial->setCodigoTipoReporte('PAGV');
            $objInfoReporteHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoReporteHistorial->setUsrCreacion($arrayParametros['strUsrSesion']);
            $objInfoReporteHistorial->setEmailUsrCreacion($arrayParametros['strEmailUsrSesion']);
            $objInfoReporteHistorial->setEstado('Activo');
            $objInfoReporteHistorial->setAplicacion('Telcos'); 
            $emFinanciero->persist($objInfoReporteHistorial);
            $emFinanciero->flush();            
            $emFinanciero->getConnection()->commit();            
            
        }
        catch (\Exception $e) 
        {
            $emFinanciero->getConnection()->close();
            $emFinanciero->getConnection()->rollback();	
            $strMensaje= 'Error al generar reporte .';
            $serviceUtil->insertError('Telcos+', 
                                      'ReportePagosVendedorController.generarReportePagosVendedorAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );               
        }
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;          
        
    }    


}