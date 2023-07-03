<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaReferido;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\AdmiTipoCuenta;
use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\tecnicoBundle\Service\InfoInterfaceElementoService;

class RepresentanteLegalMsService

{
    private $serviceUtil;
    private $serviceRestClient;
 
    private $strUrlRepresentanteLegalVerificar;
    private $strUrlRepresentanteLegalConsultar;
    private $strUrlRepresentanteLegalActualizar;


    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->serviceUtil = $objContainer->get('schema.Util');
        $this->serviceRestClient = $objContainer->get('schema.RestClient');
        $this->strUrlRepresentanteLegalVerificar = $objContainer->getParameter('ws_ms_representanteLegal_verificar_url');
        $this->strUrlRepresentanteLegalConsultar = $objContainer->getParameter('ws_ms_representanteLegal_consultar_url');
        $this->strUrlRepresentanteLegalActualizar = $objContainer->getParameter('ws_ms_representanteLegal_actualizar_url');

    }


    /**
     * Método para consumir el ms para verificar disponibilidad de representante legal
     *
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     **/
    public function wsVerificarRepresentanteLegal( $arrayParametros)
    {
        $arrayResultado = array();
        $strIpMod    = $arrayParametros['clientIp'];
        $strUserMod  = $arrayParametros['usrCreacion'];
        $strTokenCas = $arrayParametros['token'];
        try 
        {
            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $strTokenCas
                )
            );



            $strUrl = $this->strUrlRepresentanteLegalVerificar;
            $strJsonData = json_encode($arrayParametros);

            $arrayResponseJson = $this->serviceRestClient->postJSON($strUrl, $strJsonData, $objOptions);
            $strJsonRespuesta = json_decode($arrayResponseJson['result'], true);


            $arrayResultado = array(
                'strStatus' => $strJsonRespuesta['status'],
                'strMensaje' => $strJsonRespuesta['message'],
                'objData' => $strJsonRespuesta['data']
            );

            if (empty($arrayResultado['strMensaje']))
            {
                $arrayResultado['strMensaje'] = "No existe conectividad con el WS ms-core-com-persona.";
            }


        }
        catch (\Exception $e)
        {
            $arrayResultado['strMensaje'] = $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'RepresentanteLegalMsService.wsVerificarRepresentanteLegal',
                'Error RepresentanteLegalMsService.wsVerificarRepresentanteLegal:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }

    /**
     * Método para consumir el ms para consultar lista de representante legal
     *
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     **/
    public function wsConsultarRepresentanteLegal( $arrayParametros)
    {
        $arrayResultado = array();
        $strIpMod    = $arrayParametros['clientIp'];
        $strUserMod  = $arrayParametros['usrCreacion'];
        $strTokenCas = $arrayParametros['token'];
        try 
        {
            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $strTokenCas
                )
            );



            $strUrl = $this->strUrlRepresentanteLegalConsultar;
            $strJsonData = json_encode($arrayParametros);

            $arrayResponseJson = $this->serviceRestClient->postJSON($strUrl, $strJsonData, $objOptions);
            $strJsonRespuesta = json_decode($arrayResponseJson['result'], true);


            $arrayResultado = array(
                'strStatus' => $strJsonRespuesta['status'],
                'strMensaje' => $strJsonRespuesta['message'],
                'objData' => $strJsonRespuesta['data']
            );

            if (empty($arrayResultado['strMensaje']))
            {
                $arrayResultado['strMensaje'] = "No existe conectividad con el WS ms-core-com-persona.";
            }


        }
        catch (\Exception $e) 
        {
            $arrayResultado['strMensaje'] = $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'RepresentanteLegalMsService.wsConsultarRepresentanteLegal',
                'Error RepresentanteLegalMsService.wsConsultarRepresentanteLegal:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }


    /**
     * Método para consumir el ms para actualizar lista de representate legal
     *
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     **/
    public function wsActualizarRepresentanteLegal($arrayParametros)
    {
            $arrayResultado = array();
            $strIpMod    = $arrayParametros['clientIp'];
            $strUserMod  = $arrayParametros['usrCreacion'];
            $strTokenCas = $arrayParametros['token'];
            try 
            {
                $objOptions = array(
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'tokencas: ' . $strTokenCas
                    )
                );
    
    
    
                $strUrl = $this->strUrlRepresentanteLegalActualizar;
                $strJsonData = json_encode($arrayParametros);
    
                $arrayResponseJson = $this->serviceRestClient->postJSON($strUrl, $strJsonData, $objOptions);
                $strJsonRespuesta = json_decode($arrayResponseJson['result'], true);
    
    
                $arrayResultado = array(
                    'strStatus' => $strJsonRespuesta['status'],
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
    
                if (empty($arrayResultado['strMensaje']))
                {
                    $arrayResultado['strMensaje'] = "No existe conectividad con el WS ms-core-com-persona.";
                }
    
    
            }
            catch (\Exception $e)
            {
                $arrayResultado['strMensaje'] = $e->getMessage();
    
                $this->serviceUtil->insertError(
                    'Telcos+',
                    'RepresentanteLegalMsService.wsActualizarRepresentanteLegal',
                    'Error RepresentanteLegalMsService.wsActualizarRepresentanteLegal:' . $e->getMessage(),
                    $strUserMod,
                    $strIpMod
                );
            }
            return $arrayResultado;
        }
    
}
