<?php
namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

/**
 * Emergencia Sanitaria controller.
 *
 */
class EmergenciaSanitariaController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_446-1")
    * crearEmergenciaSanitariaAction, permite visualizar la pantalla para ingresar los criterios de diferidos por
    * emergencia sanitaria.
    * 
    * @author : José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019.
    * @since 1.0
    */
    public function crearEmergenciaSanitariaAction()
    {
        return $this->render('financieroBundle:EmergenciaSanitaria:crearEmergenciaSanitaria.html.twig', array());
    }

    /**
     * getCiclos, obtiene los ciclos de facturación por código de empresa.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-04-2020     
     * @param array $arrayParametros [
     *                                "strEmpresaCod" => Empresa en sesión
     *                               ]
     *
     * @return Response lista de los ciclos de facturación por código empresa.
     */
    public function getCiclosAction()
    {       
        $objRequest                       = $this->getRequest();
        $strEmpresaCod                    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros                  = array();
        $arrayParametros['strEmpresaCod'] = $strEmpresaCod;
        $serviceEmergenciaSanitaria       = $this->get('financiero.EmergenciaSanitaria');
        $arrayCiclos                      = $serviceEmergenciaSanitaria->getCiclos($arrayParametros);
        $objResponse                      = new Response(json_encode(array('ciclos_facturacion'=> $arrayCiclos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getParametrosDetAction, obtiene los valores que se encuentran parametrizados en ADMI_PARAMETRO_CAB.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-04-2020
     *
     * @return Response lista de valores parametrizados.
     */
    public function getParametrosDetAction()
    {   
        $objRequest         = $this->getRequest();
        $strParametroCab    = $objRequest->get('strParametroCab');
        $strDescripcionDet  = $objRequest->get('strDescripcionDet');
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral          = $this->get('doctrine')->getManager('telconet');

        $arrayListPeriodos  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get($strParametroCab,
                                              'COMERCIAL',
                                              '',
                                              $strDescripcionDet,
                                              '',
                                              '',
                                              '',
                                              '',
                                              '',
                                              $strEmpresaCod,
                                              'valor3');
        $arrayValores       = array();
        
        foreach($arrayListPeriodos as $objPeriodo)
        {
            $arrayValores[] = array('id' => $objPeriodo['valor1'], 'nombre' => $objPeriodo['valor1']);
        }
        sort($arrayValores);
        $objResponse        = new Response(json_encode(array('arrayValores' => $arrayValores)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    * @Secure(roles="ROLE_446-1")
    * ejecutarEmerSanitAction()
    * Función que crea un Proceso Masivo para generar un reporte o ejecutar las NCI por emergencia sanitaria.
    * Tipos de PMA para emergencia sanitaria : ReporteEmerSanit" ó "EjecutarEmerSanit"
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 07-04-2020
    * 
    * @return $strResponse
    */    
    public function ejecutarEmerSanitAction()
    {                      
        $objRequest                     = $this->getRequest();
        $objSesion                      = $objRequest->getSession();
        $emComercial                    = $this->getDoctrine()->getManager('telconet');
        $strSaldoDesde                  = $objRequest->get('strSaldoDesde');
        $strSaldoHasta                  = $objRequest->get('strSaldoHasta');
        $strMesesDiferir                = $objRequest->get('strMesesDiferir');
        $arrayCiclosFacturacion         = $objRequest->get('arrayCiclosFacturacion');
        $strCiclosFacturacion           = implode(",", $arrayCiclosFacturacion);
        $arrayEstadoServicio            = $objRequest->get('arrayEstadoServicio');
        $strEstadoServicio              = implode(",", $arrayEstadoServicio);
        $strMotivo                      = $objRequest->get('strMotivo');
        $strUsrCreacion                 = $objSesion->get('user');
        $strCodEmpresa                  = $objSesion->get('idEmpresa');
        $strIpCreacion                  = $objRequest->getClientIp();
        $arrayParametros                = array('strSaldoDesde'          => $strSaldoDesde,
                                                'strSaldoHasta'          => $strSaldoHasta,
                                                'strMesesDiferir'        => $strMesesDiferir,
                                                'strCiclosFacturacion'   => $strCiclosFacturacion,
                                                'strEstadoServicio'      => $strEstadoServicio,
                                                'strUsrCreacion'         => $strUsrCreacion,
                                                'strCodEmpresa'          => $strCodEmpresa,
                                                'strIpCreacion'          => $strIpCreacion,
                                                'strMotivo'              => $strMotivo);
        try
        {                        
            $serviceEmergenciaSanitaria  = $this->get('financiero.EmergenciaSanitaria');
            $strResponse                 = $serviceEmergenciaSanitaria->crearProcesoMasivo($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al crear el proceso masivo, por favor consulte con el Administrador.";
        }

        return new Response($strResponse);
    }
}
