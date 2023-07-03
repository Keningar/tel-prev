<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\tecnicoBundle\Service;

/**
* AuthenticationFoxService, Service donde se invocará al procedimiento de autenticacion
* @author Sofía Fernández <sfernandez@telconet.ec>          
* @version 1.0 22-06-2018
*/
class AuthenticationFoxService
{
    private $emComercial;
    private $secretKey;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
       $this->emComercial = $container->get('doctrine')->getManager('telconet');
       $this->secretKey   = $container->getParameter('secret');
    }
    
    /**
     * autenticarUsuaruioYContrasena, funcion que invoca al paquete COMEK_CONSULTAS.P_GET_SSID_FOX
     * @param type array $arrayParametros
     * @return type array $arrayRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 22-26-2018
     */
    public function autenticarUsuaruioYContrasena($arrayParametros) 
    { 
        $strSsIdFox = str_repeat(' ', 200);
        $strCodPais = str_repeat(' ', 200);
        $strMensaje = str_repeat(' ', 200);
        
        $strSql     = 'BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_GET_SSID_FOX(:USUARIO_FOX, '
                                                                      . ':PASSWORD_FOX, '
                                                                      . ':KEY_ENCRIP, '
                                                                      . ':SSID_FOX, '
                                                                      . ':strCodPais, '
                                                                      . ':strMensaje); END;';
        try
        {
            $strStmt = $this->emComercial->getConnection()->prepare($strSql);
            $strStmt->bindParam('USUARIO_FOX',  strtolower($arrayParametros['username']));
            $strStmt->bindParam('PASSWORD_FOX', $arrayParametros['password']);
            $strStmt->bindParam('KEY_ENCRIP',   $this->secretKey);
            $strStmt->bindParam('SSID_FOX',     $strSsIdFox);
            $strStmt->bindParam('strCodPais',   $strCodPais);
            $strStmt->bindParam('strMensaje',   $strMensaje);
            $strStmt->execute();
        } 
        catch (\Exception $ex) 
        {
            error_log("AuthenticationFoxService->autenticarUsuaruioYContrasena " . $ex->getMessage());
        }
        $arrayRespuesta['SSID_FOX']   = $strSsIdFox;
        $arrayRespuesta['strCodPais'] = $strCodPais;
        $arrayRespuesta['strMensaje'] = $strMensaje;
        
        return $arrayRespuesta;

    }
}
