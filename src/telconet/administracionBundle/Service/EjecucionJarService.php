<?php

namespace telconet\administracionBundle\Service;

use Symfony\Component\HttpFoundation\Response;

class EjecucionJarService
{
    public function setDependencies()
    {

    }
    /**
    * Metodo para ejecutar comandos en el servidor
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.0 02-03-2018
    * @param  array  $arrayParamertros
    * @return string $strStatus
    */
    public function execJar($arrayParamertros)
    {
        $strLineaComando = "";
        $strStatus =  '';
        foreach($arrayParamertros as $strComando):
            $strLineaComando .= $strComando;
        endforeach;
        if (!empty($strLineaComando))
        {
            $strSalida     = shell_exec($strLineaComando);
            $intPos        = strpos($strSalida, "{");
            $arrayResultado = substr($strSalida, $intPos);
            $objResultado  = json_decode($arrayResultado);
            $strStatus     = $objResultado->status;
            return $strStatus;
        }
        return $strStatus;
    }
}
