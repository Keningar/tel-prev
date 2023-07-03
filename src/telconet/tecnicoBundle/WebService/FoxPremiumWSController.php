<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of FoxPremiumWSController
 *
 * @author lcabrera
 */
class FoxPremiumWSController extends BaseWSController
{

    /**
     * Función que restablece la contraseña de un usuario desde FOX
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 19-06-2018
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 05-12-2018
     * Se modifica el método de la petición de GET a POST.
     * Se reciben los parámetros desde el cuerpo de la petición en JSON.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 28-09-2020
     * Se modifica ws para la selección del producto a consultar (PARAMOUNT, NOGGION O FOXPREMIUM)
     * 
     *
     * @param \Symfony\Component\HttpFoundation\Request $objRequest
     * @return \Symfony\Component\HttpFoundation\Response $objResponse
     *
     */
    public function restablecerContraseniaDesdeFoxAction(Request $objRequest)
    {
        $arrayRequest      = json_decode($objRequest->getContent(),true);
        $strUsername       = $arrayRequest['username'];
        $strEmpresaCod     = $arrayRequest['empresaCod'];
        $strClientIp       = $objRequest->getClientIp();
        $strProducto       = $arrayRequest['producto'];

        if ( (!isset($strUsername) || !isset($strProducto)) || (empty($strUsername) || empty($strProducto)) )
        {
            return new Response(json_encode(array('status' => "ERROR", 'message' => "El campo usuario y producto es obligatorio.")));
        }

        $serviceFoxPremium = $this->get("tecnico.FoxPremium");
        $arrayRespuestaJson = $serviceFoxPremium
                            ->reiniciaContraeniaDesdeFox(array("strUsuario"    => strtolower($strUsername),
                                                               "strProducto"   => $strProducto,
                                                               "token"         => $arrayRequest['token'],
                                                               "strEmpresaCod" => $strEmpresaCod ? $strEmpresaCod : '18',
                                                               "strClientIp"   => $strClientIp ? $strClientIp : '127.0.0.1'));
        return new Response($arrayRespuestaJson);
    }

    /**
     * Función que crea una nueva contraseña para productos que no generan credenciales
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 22-08-2022
     *
     * @param \Symfony\Component\HttpFoundation\Request $objRequest
     * @return \Symfony\Component\HttpFoundation\Response $objResponse
     *
     */
    public function crearContraseniaDesdeFoxAction(Request $objRequest)
    {
        $arrayRequest      = json_decode($objRequest->getContent(),true);
        $strUsername       = $arrayRequest['username'];
        $strEmpresaCod     = $arrayRequest['empresaCod'];
        $strClientIp       = $objRequest->getClientIp();
        $strProducto       = $arrayRequest['producto'];
        $strPassword       = $arrayRequest['newPassword'];
        $strCrearPwd       = $arrayRequest['crearPassword'];
        if ( (!isset($strUsername) || !isset($strProducto)) || (empty($strUsername) || empty($strProducto)) )
        {
            return new Response(json_encode(array('status' => "ERROR", 'message' => "El campo usuario y producto es obligatorio.")));
        }
        if (!isset($strCrearPwd) || empty($strCrearPwd) || !isset($strPassword) || empty($strPassword))
        {
            return new Response(json_encode(array('status' => "ERROR", 'message' => "El campo crearPassword y newPassword es obligatorio.")));
        }
        $serviceFoxPremium = $this->get("tecnico.FoxPremium");
        $arrayRespuestaJson = $serviceFoxPremium
                            ->crearContraseniaDesdeFox(array("strUsuario"           => strtolower($strUsername),
                                                               "strProducto"        => $strProducto,
                                                               "strEmpresaCod"      => $strEmpresaCod ? $strEmpresaCod : '18',
                                                               "strClientIp"        => $strClientIp ? $strClientIp : '127.0.0.1',
                                                               "strPassword"        => $strPassword,
                                                               "strCrearPassword"   => $strCrearPwd));
        return new Response($arrayRespuestaJson);
    }
}
