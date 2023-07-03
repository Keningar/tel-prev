<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para el controlador 'AdmiCanalPagoLineaController'.
 * 
 * AdmiParametroCabController, Contiene los metodos para la administracion de la estructura AdmiCanalPagoLinea
 * 
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 09-09-2015
 */
class AdmiCanalPagoLineaController extends Controller
{
    /**
     * getListadoCanalPagosLineaAjaxAction, Obtiene el listado de los canales que se encuentra en la estructura ADMI_CANAL_PAGO_LINEA
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * 
     * @version 1.0 28-09-2015
     * @return json  Retorna el objeto que contiene la informacion de los canales de pagos en linea
     *
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 10-05-2023  Capturamos el id de Empresa en sesion para filtro en combo de canales.
     */
    public function getListadoCanalPagosLineaAjaxAction()
    {
        $emFinanciero                           = $this->getDoctrine()->getManager("telconet_financiero");
        $objRequest                             = $this->getRequest();
        $arrayParametros                        = array();
        $arrayParametros['intStart']            = $objRequest->get('start');
        $arrayParametros['intLimit']            = $objRequest->get('limit');
        $arrayParametros['intIdCanalPagoLinea'] = $objRequest->get('intIdCanalPagoLinea');
        $arrayParametros['strEstado']           = $objRequest->get('strEstado');
        $arrayParametros['strTipoObjeto']       = $objRequest->get('strTipoObjeto');
        $arrayResponseAdmiCanalPagosLinea       = array();

        //Se obtiene codigo de empresa y se lo agrega a arrayParametros.
        $objSession                      = $objRequest->getSession();
        $intEmpresaId                    = $objSession->get('idEmpresa');
        $arrayParametros['intEmpresaId'] = $intEmpresaId;

        //Obtiene los regsitros de la entidad AdmiParametroCab
        $arrayAdmiCanalPagosLinea = $emFinanciero->getRepository('schemaBundle:AdmiCanalPagoLinea')->getListaCanalPagosLinea($arrayParametros);
        
        //Valida que no tenga mensaje de error la consulta
        if('100' === $arrayAdmiCanalPagosLinea['strStatus'])
        {
            //Pregunta si es combo para inicializar el array con un key => 0 y value Todos
            if("ComboBox" === $arrayParametros['strTipoObjeto'])
            {
                $arrayResponseAdmiCanalPagosLinea[] = array('intIdCanalPagoLinea'          => 0,
                                                            'strDescripcionCanalPagoLinea' => 'Todos');
            }
            //Itera el array de los datos obtenidos
            foreach($arrayAdmiCanalPagosLinea['arrayDatos'] as $objAdmiCanalPagosLinea):
                $arrayResponseAdmiCanalPagosLinea[] = array(
                                                 'intIdCanalPagoLinea'          => $objAdmiCanalPagosLinea->getId(),
                                                 'strFormaPgo'                  => $objAdmiCanalPagosLinea->getFormaPago()->getDescripcionFormaPago(),
                                                 'strCodigoCanalPagoLinea'      => $objAdmiCanalPagosLinea->getCodigoCanalPagoLinea(),
                                                 'strDescripcionCanalPagoLinea' => $objAdmiCanalPagosLinea->getDescripcionCanalPagoLinea(),
                                                 'strEstadoCanalPagoLinea'      => $objAdmiCanalPagosLinea->getEstadoCanalPagoLinea(),
                                                 'strNombreCanalPagoLinea'      => $objAdmiCanalPagosLinea->getNombreCanalPagoLinea(),
                                                 'strUsuarioCanalPagoLinea'     => $objAdmiCanalPagosLinea->getUsuarioCanalPagoLinea(),
                                                 'strUsrCreacion'               => $objAdmiCanalPagosLinea->getUsrCreacion(),
                                                 'strFeCreacion'                => $objAdmiCanalPagosLinea->getFeCreacion()->format('d-M-Y'));
            endforeach;
        }
        $objResponse = new Response(json_encode(array('jsonResponseAdmiCanalPagosLinea' => $arrayResponseAdmiCanalPagosLinea,
                                                      'intTotal'                        => $arrayAdmiCanalPagosLinea['intTotal'],
                                                      'strMensaje'                      => $arrayAdmiCanalPagosLinea['strMensaje'],
                                                      'strStatus'                       => $arrayAdmiCanalPagosLinea['strStatus'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getListadoCanalPagosLineaAjaxAction

}