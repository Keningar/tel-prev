<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento de Netlifezone.
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 05-09-2018
 */
class NetlifezoneWSController extends BaseWSController
{
    private $sha256MD = "7abcf6dac49247ef3111e13199b86b909f5ee306b0599c64c5b76b20ca3972a2";
    /**
     * Función que sirve para procesar las opciones que vienen desde el portal de Netlifezone
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 23-08-2018
     * 
     * @param \Symfony\Component\HttpFoundation\Request $objRequest
     * @return \Symfony\Component\HttpFoundation\Response $objResponse
     * 
     */
    public function procesarAction(Request $objRequest)
    {
        $objCsrfProvi  = $this->get('form.csrf_provider');
        $arrayDataWs   = json_decode($objRequest->getContent(),true);
        $arrayResponse = array();
        $objResponse   = new Response();
        $strOpcion     = $arrayDataWs['op'];
        $arrayData     = $arrayDataWs['data'];
        $arrayParametrosEmp              = array();
        $arrayParametrosEmp['strOpcion'] = $arrayDataWs['op'];
        $arrayParametrosEmp['strCadenaEmpresa'] = $arrayData['empresa'];
        $arrayRespuesaIntention     = $this->retornaEmpresaId($arrayParametrosEmp);
        $strIntention               = $arrayRespuesaIntention['strIntention'];
        $arrayData['strEmpresaCod'] = $arrayRespuesaIntention['strEmpresaCod'];
        if(!$objCsrfProvi->isCsrfTokenValid2($strIntention, $arrayDataWs['token']))
        {
            $arrayResponse['status']  = 403;
            $arrayResponse['mensaje'] = "Token invalido, por favor intente nuevamente.";
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponse));
            return $objResponse;
        }
        $serviceNetlifezone = $this->get('tecnico.Wifi');
        if($strOpcion)
        {
            switch($strOpcion)
            {
                case 'cambiarPasswordNZ':
                    $arrayResponseService = $serviceNetlifezone->cambiarPasswordWifi($arrayData);
                    $arrayResponse['status']  = $arrayResponseService['strStatus'];
                    $arrayResponse['mensaje'] = $arrayResponseService['strMensaje'];
                    break;
                case 'resetearPasswordNZ':
                    $arrayResponseService = $serviceNetlifezone->resetearPasswordWifi($arrayData);
                    $arrayResponse['status']  = $arrayResponseService['strStatus'];
                    $arrayResponse['mensaje'] = $arrayResponseService['strMensaje'];
                    break;
                case 'obtenerCiudades':
                    $arrayCiudades = array();
                    try
                    {
                        $emComercial     = $this->get('doctrine')->getManager('telconet');
                        $arrayCantonResp = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                                           ->getCantonesPorNombre();
                        foreach ($arrayCantonResp as $objCanton):
                            $arrayCiudades[$objCanton->getId()]= $objCanton->getNombreCanton();
                        endforeach;
                        $arrayResponse['status'] = "OK";
                        $arrayResponse['ciudades'] = $arrayCiudades;
                    }
                    catch (\Exception $objEx)
                    {
                        $arrayCiudades = array();
                        $arrayResponse['status'] = "ERROR";
                        $arrayResponse['ciudades'] = $arrayCiudades;
                    }
                    break;
                case 'obtenerFormasPago':
                    $arrayFormasPago= array();
                    try
                    {
                        $emComercial     = $this->get('doctrine')->getManager('telconet');
                        $arrayFormasPagoResp = $emComercial->getRepository('schemaBundle:AdmiFormaPago')
                                                       ->findFormasPagoXEstado('Activo')
                                                       ->getQuery()
                                                       ->getResult();
                        foreach ($arrayFormasPagoResp as $objFormaPago):
                            $arrayFormasPago[$objFormaPago->getId()]= $objFormaPago->getDescripcionFormaPago();
                        endforeach;
                        $arrayResponse['status'] = "OK";
                        $arrayResponse['formasPago'] = $arrayFormasPago;
                    }
                    catch (\Exception $objEx)
                    {
                        $arrayFormasPago = array();
                        $arrayResponse['status'] = "ERROR";
                        $arrayResponse['formasPago'] = $arrayFormasPago;
                    }
                    break;
                default:
                    $arrayResponse['status']  = $this->status['METODO'];
                    $arrayResponse['mensaje'] = $this->mensaje['METODO'];
            }
        }
        if(isset($arrayResponse))
        {
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponse));
        }
        return $objResponse;
    }
    
    /**
     * retornaEmpresaId
     * 
     * Documentación para el método 'retornaEmpresaId'.
     *
     * Método que retorna el id de empresa dado un sha256 del prefijo
     *
     * @param array $arrayParametros [
     *                                 strCadenaEmpresa    Sha256 de empresa a procesar
     *                                 strProceso          Proceso a ejecutar
     *                               ]
     * @return array $arrayRespuesta.
     *
     * @author  Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 23-08-2018
     * @since   1.0
     */
    public function retornaEmpresaId($arrayParametros)
    {
        $arrayRespuesta = array();
        $arrayRespuesta['strIntention']  = array();
        $arrayRespuesta['strEmpresaCod'] = "";
        $strIntention = "";
        if($this->sha256MD == $arrayParametros['strCadenaEmpresa'] )
        {
            if ($arrayParametros['strOpcion'] == "cambiarPasswordNZ")
            {
                $strIntention = "cambiar-password-netlifezone-"."MD";
            }
            else if ($arrayParametros['strOpcion'] == "resetearPasswordNZ")
            {
                $strIntention = "resetear-password-netlifezone-"."MD";
            }
            else if ($arrayParametros['strOpcion'] == "obtenerCiudades")
            {
                $strIntention = "obtener-ciudades-"."MD";
            }
            else if ($arrayParametros['strOpcion'] == "obtenerFormasPago")
            {
                $strIntention = "obtener-formasPago-"."MD";
            }
            $arrayRespuesta['strIntention']  = $strIntention;
            $arrayRespuesta['strEmpresaCod'] = "18";
        }
        return $arrayRespuesta;
    }
}
