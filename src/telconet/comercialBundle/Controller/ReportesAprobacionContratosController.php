<?php

namespace telconet\comercialBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\ReturnResponse;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * ReportesAprobacionContratosController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Reportes de Aprobacion de Contratos
 *
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 06-07-2017
 */
class ReportesAprobacionContratosController extends Controller
{
   
   /**
    * @Secure(roles="ROLE_388-1")
    * 
    * Documentación para el método 'indexAction'.
    *
    * Muestra la pantalla para escoger filtros para la generación del Reporte de Aprobación de Contratos
    *
    * @return render 
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 06-07-2017
    */
    public function indexAction()
    {
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $objItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("388", "1");
        return $this->render('comercialBundle:ReportesAprobacionContratos:index.html.twig', array('item' => $objItemMenu));
    }
    
   /**
    * getPtosCoberturaByEmpresaAction, obtiene la informacion de los Puntos de Cobertura por empresa en sesion y estados
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-07-2017
    * 
    * @return \Symfony\Component\HttpFoundation\JsonResponse
    */
    public function getPtosCoberturaByEmpresaAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $objReturnResponse       = new ReturnResponse();
        $emComercial             = $this->getDoctrine()->getManager();

        $arrayParametros                             = array();
        $arrayParametros['strPrefijoEmpresa']        = $objSession->get('prefijoEmpresa'); 
        $arrayParametros['arrayEstadoJurisdiccion']  = array('Activo','Modificado');
        $arrayParametros['strEstadoOficina']         = 'Activo';      
        $strAppendOficina                            = $objRequest->get('strAppendDatos');
        
        $objPtosCoberturaByEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
                                                 ->getPtosCoberturaByEmpresa($arrayParametros);

        $arrayPtosCobertura = array();
        
        if(!empty($strAppendOficina))
        {
            $arrayPtosCobertura[0] = array('intIdObj'          => 0,
                                           'strDescripcionObj' => $strAppendOficina);
        }
        $arrayPtosCobertura = array_merge($arrayPtosCobertura,$objPtosCoberturaByEmpresa->getRegistros());

        $objReturnResponse->setRegistros($arrayPtosCobertura);
        $objReturnResponse->setTotal($objPtosCoberturaByEmpresa->getTotal());
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        
        $objJsonResponse = new JsonResponse((array) $objReturnResponse);

        return $objJsonResponse;
    }

    /**
    * @Secure(roles="ROLE_388-1")
    * Documentación para el método 'gridReporteAprobacionContratosAction'.
    *
    * Retorna resultado de consulta de Reporte de Gestion de Aprobación de Contratos
    *
    * @param cbxIdPtoCobertura, ids para filtrar por punto de cobertura
    * @param fechaPrePlanificacionDesde, rango inicial para fecha de pre-planificación
    * @param fechaPrePlanificacionHasta, rango final para fecha de pre-planificación
    * 
    * @return JsonResponse
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 07-07-2017
    */
    public function gridReporteAprobacionContratosAction()
    {
        ini_set('max_execution_time', 9999999999);
        $arrayParametros       = array();
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $strIpClient           = $objRequest->getClientIp();
        $strUsrSesion          = $objSession->get('user');
        $emComercial           = $this->getDoctrine()->getManager();
                
        $arrayParametros['strIdsPtoCobertura'] = $objRequest->get("cbxIdPtoCobertura");
        
        $arrayFechaPrePlanificacionDesde = explode('T', $objRequest->get("fechaPrePlanificacionDesde"));
        $arrayFechaPrePlanificacionHasta = explode('T', $objRequest->get("fechaPrePlanificacionHasta"));
        
        $strFechaPrePlanificacionDesde = (isset($arrayFechaPrePlanificacionDesde) ? $arrayFechaPrePlanificacionDesde[0] : 0);
        $strFechaPrePlanificacionHasta = (isset($arrayFechaPrePlanificacionHasta) ? $arrayFechaPrePlanificacionHasta[0] : 0);
        
        if($strFechaPrePlanificacionDesde && $strFechaPrePlanificacionDesde != "0")
        {
            $arrayParametros['strFechaPrePlanificacionDesde'] = date_format(date_create($strFechaPrePlanificacionDesde), "d/m/Y");
        }
        
        if($strFechaPrePlanificacionHasta && $strFechaPrePlanificacionHasta != "0")
        {
            $arrayParametros['strFechaPrePlanificacionHasta'] = date_format(date_create($strFechaPrePlanificacionHasta." +1 day"), "d/m/Y");
        }
                
        $objOciCon      = oci_connect(
                                        $this->container->getParameter('user_comercial'),
                                        $this->container->getParameter('passwd_comercial'),
                                        $this->container->getParameter('database_dsn')
                                     );
        
        $objCursor       = oci_new_cursor($objOciCon);
       
        $arrayParametros['strEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);
        $arrayParametros['intStart']          = $objRequest->get('start');
        $arrayParametros['intLimit']          = $objRequest->get('limit');
        $arrayParametros['objOciCon']         = $objOciCon;
        $arrayParametros['objCursor']         = $objCursor;
        
        $arrayReporteAprobacionContratos = array();
        $objJsonResponse                 = new JsonResponse($arrayReporteAprobacionContratos);
        
        try
        {
            $arrayReporteAprobacionContratos  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getJsonReporteAprobacionContratos($arrayParametros);

            $objJsonResponse->setData($arrayReporteAprobacionContratos);
        }
        catch (\Exception $e)
        {               
            $serviceUtil->insertError('Telcos+',
                                      'ReportesAprobacionContratosController.gridReporteAprobacionContratosAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        return $objJsonResponse;
    }

    /**
    * @Secure(roles="ROLE_388-1")
    * Documentación para el método 'generarRptAprobContratosAction'.
    *
    * Metodo que permite enviar los parametros para la generación y envío por mail de los Reportes
    * Detallado y Resumido de Gestion de Administracion de Contratos.
    *
    * @param cbxIdPtoCobertura, ids para filtrar por punto de cobertura
    * @param fechaPrePlanificacionDesde, rango inicial para fecha de pre-planificación
    * @param fechaPrePlanificacionHasta, rango final para fecha de pre-planificación
    * 
    * @return JsonResponse
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 10-07-2017
    */
    public function generarRptAprobContratosAction()
    {
        ini_set('max_execution_time', 9999999999);
        set_time_limit(5);
        $arrayParametros       = array();
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $serviceUtil           = $this->get('schema.Util');
        $strIpClient           = $objRequest->getClientIp();
        $strUsrSesion          = $objSession->get('user');
        $emComercial           = $this->getDoctrine()->getManager();
                
        $arrayParametros['strPrefijoEmpresa']  = $objSession->get('prefijoEmpresa');
        $arrayParametros['strIdsPtoCobertura'] = $objRequest->get("cbxIdPtoCobertura");
        
        $arrayFechaPrePlanificacionDesde = explode('T', $objRequest->get("fechaPrePlanificacionDesde"));
        $arrayFechaPrePlanificacionHasta = explode('T', $objRequest->get("fechaPrePlanificacionHasta")); 
        
        $strFechaPrePlanificacionDesde = (isset($arrayFechaPrePlanificacionDesde) ? $arrayFechaPrePlanificacionDesde[0] : 0);
        $strFechaPrePlanificacionHasta = (isset($arrayFechaPrePlanificacionHasta) ? $arrayFechaPrePlanificacionHasta[0] : 0);

        if($strFechaPrePlanificacionDesde && $strFechaPrePlanificacionDesde != "0")
        {
            $arrayParametros['strFechaPrePlanificacionDesde'] = date_format(date_create($strFechaPrePlanificacionDesde), "d/m/Y");
        }
        
        if($strFechaPrePlanificacionHasta && $strFechaPrePlanificacionHasta != "0")
        {
            $arrayParametros['strFechaPrePlanificacionHasta'] = date_format(date_create($strFechaPrePlanificacionHasta." +1 day"), "d/m/Y");
        }
                
        $arrayParametros['strEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);
        
        try
        {
            $strResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->generarRptAprobContratos($arrayParametros);
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'ReportesAprobacionContratosController.generarRptAprobContratosAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
        }
        
        $objJsonResponse = new JsonResponse($strResultado);
        return $objJsonResponse;
    }

}
