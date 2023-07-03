<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoLog;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * 
 * Ver Logs Documentos controller.
 *
 * Controlador que se encargará de consultar los logs de los documentos digitales
 *
 * @author Brando Tomala <btomala@telconet.ec>
 * @version 1.0 17-05-2021
 */
class VerLogsDocumentosController extends Controller
{

     /**464
     * @Secure(roles="ROLE_457-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra la pantalla inicial con el reporte de logs de documentos digitales.
     *
     * @return Response 
     *
     * @author Brando Tomala <btomala@telconet.ec>
     * @version 1.0 17-05-2021
     */
    public function indexAction()
    {
        return $this->render( 'comercialBundle:verLogsDocumentosDigitales:index.html.twig');
    }


    /**
     * Documentación para el método 'verLogsDocumentosDigitales'.
     * Obtiene informacion de los logs registrados por visualización/impresion de documentos de cliente
     * @author Brando Tomala <btomala@telconet.ec>
     * @version 1.0 20-05-2021   
     * @return response       
     */     
    public function gridInfoLogsDocumentosDigitalesAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $arrayFechaDesde   = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta   = explode('T', $objRequest->get("fechaHasta"));
        $strNombre         = $objRequest->get("nombre");
        $strApellido       = $objRequest->get("apellido");
        $intLimit          = $objRequest->get("limit");
        $intStart          = $objRequest->get("start");
        $intPage           = $objRequest->get("page");
        $strIdentificacion = $objRequest->get("identificacion");
        $intIdEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $strLogin          = $objRequest->get("login");
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        
        
        $arrayParametros   = [];
        $arrayParametros['idEmpresa']      = $intIdEmpresa;
        $arrayParametros['fechaDesde']     = $arrayFechaDesde[0];
        $arrayParametros['fechaHasta']     = $arrayFechaHasta[0];
        $arrayParametros['nombre']         = strtoupper($strNombre);
        $arrayParametros['apellido']       = strtoupper($strApellido);
        $arrayParametros['identificacion'] = $strIdentificacion;
        $arrayParametros['limit']          = $intLimit;
        $arrayParametros['page']           = $intPage;
        $arrayParametros['start']          = $intStart;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['login']          = strtoupper($strLogin);
        
        $arrayResultado  = $emComercial->getRepository('schemaBundle:InfoVisualizacionDocHist')->getHistDocumentoDigital($arrayParametros);     
        $arrayRegistros  = $arrayResultado['registros'];
        $intTotal        = $arrayResultado['total']; 


        $objResponse = new Response(json_encode(array('total'=>$intTotal,'infoLogsDocumentosDigitales'=>$arrayRegistros )));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }    
    
}

