<?php

namespace telconet\financieroBundle\Service;

class InfoDetalleDocumentoService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;

    /**
     * string $host
     */
    private $host;

    /**
     * string $path
     */
    private $path_telcos;

    /**
     * string $path
     */
    private $strPathJava;

    /**
     * string $strScriptPathJava
     */
    private $strScriptPathJava;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emfinan = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->host = $container->getParameter('host_scripts');
        $this->path_telcos = $container->getParameter('path_telcos');
        $this->strPathJava = $container->getParameter('path_java_soporte');
        $this->strScriptPathJava = $container->getParameter('path_script_java_soporte');
    }

    /**
     * Genera los detalles de los documentos financieros FAC, FACP, NC, NCI
     * @param parametros variables del controlador
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 23-10-2014
     */
    function generarDetalleDocumentos($arrayParametrosIn)
    {

        $strPrefijoEmpresa = $arrayParametrosIn["strPrefijoEmpresa"];
        $intIdDocumento = $arrayParametrosIn["id"];
        $strCodDocumento = $arrayParametrosIn["strCodDocumento"];
        $strEstado = $arrayParametrosIn["estado"];

        //Si la entidad existe se debe generar o llenar la tabla de detalle
        $pathFileLogger = $this->path_telcos . "telcos/src/telconet/financieroBundle/batch/logs/";

        //Parametros
        $strScript = "/home/scripts-telcos/md/financiero/sources/genera-detalles-documentos-financieros/dist/generarDetallesDocumento.jar";
        $strParametros = $this->host .
            "|||" .
            $strPrefijoEmpresa .
            "|" .
            $intIdDocumento .
            "|" .
            $strCodDocumento .
            "|" .
            $strEstado .
            "|N|" .
            $pathFileLogger;
        $strEsperaRespuesta = 'NO';

        $strComunicacion = "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar";
        $strLogScript = "/home/telcos/app/Resources/scripts/log/log.txt";

        $strComando = "nohup ".$this->strPathJava." -jar -Djava.security.egd=file:/dev/./urandom " . $this->path_telcos . $strComunicacion." '" .
            $strScript . "' '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $this->host . "' '" .
            $this->strScriptPathJava."' >> " . $this->path_telcos .$strLogScript." &";
        shell_exec($strComando);
    }

}
