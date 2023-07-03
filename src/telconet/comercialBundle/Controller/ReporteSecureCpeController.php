<?php

namespace telconet\comercialBundle\Controller;
use telconet\schemaBundle\Entity\InfoReporteHistorial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * 
 * ReporteSecureCpeController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Reportes de Productos Secure Cpe
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 03-09-2021
 */
class ReporteSecureCpeController extends Controller
{

   /**
    * @Secure(roles="ROLE_361-1")
    * 
    * Documentación para el método 'indexAction'.
    *
    * Redirecciona al twig  de consulta de productos Secure Cpe.
    *
    * @return render 
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 03-09-2021
    */
    public function indexAction()
    {
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("361", "1");
        return $this->render('comercialBundle:ReporteSecureCpe:index.html.twig', array('item' => $entityItemMenu));        
    }
    
   /**
    * @Secure(roles="ROLE_361-1") 
    *
    * Documentación para el método 'gridSecureCpeAction'.
    *
    * Muestra la pantalla de consulta de productos Secure Cpe.
    *
    * @return Response 
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 03-09-2021
    */
    public function gridSecureCpeAction()
    {
        $objResponse    = new Response();
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();  
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSession->get('user');        
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $strDns         = $this->container->getParameter('database_dsn');      
        $objOciCon      = oci_connect(
                                        $this->container->getParameter('user_infraestructura'),
                                        $this->container->getParameter('passwd_infraestructura'), 
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
            $objJsonSecureCpe  = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->getReporteSecureCpe($arrayParametros);          
        
            $objResponse->setContent($objJsonSecureCpe);
        
        }
        catch (\Exception $e) 
        {   
            error_log($e->getMessage());
            $serviceUtil->insertError('Telcos+', 
                                      'ReporteSecureCpeController.gridSecureCpeAction',
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
    * generarReporteSecureCpeAction
    * Metodo que permite enviar los parametros para la generación del reporte en excel .
    *  
    * @return Response 
    * 
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 03-09-2021 
    */     
    public function generarReporteSecureCpeAction()
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
        $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->generarReporteSecureCpe($arrayParametros); 
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
                                      'ReporteSecureCpeController.generarReporteSecureCpeAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );               
        }
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;          
        
    }    
}



