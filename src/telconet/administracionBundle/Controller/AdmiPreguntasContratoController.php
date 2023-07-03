<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoPersona;

use telconet\schemaBundle\Form\AdmiHoldingType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiPreguntasContratoController extends Controller implements TokenAuthenticatedController
{

    const NOMBRE_PROCESO = 'LinkDatosBancario';
    const NOMBRE_DOCUMENTO = 'Contrato de adhesión';

  
    
    /**
     * getPreguntasAction, obtiene las respuestas que se encuentran almacenada en la tabla ADMI_RESPUESTA.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 26-01-2022    
     *                    
     * @return Response lista de ADMI_RESPUESTA.
     */
    public function getPreguntasAction()
    {   
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas();

        $strUrlListaPreg     = $this->container->getParameter('ws_ms_documento_url').'listaRespuesta';
        $serviceRestClient   = $this->get('schema.RestClient');
        
        $arrayOption         = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );
        $arrayListRespuesta  = $serviceRestClient->get($strUrlListaPreg, $arrayOption);
        $arrayRespuesta      = json_decode($arrayListRespuesta['result'], true);
                    
        $objResponse = new Response(json_encode(array('respuesta' => $arrayRespuesta['data'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * guardarClausulasAction, almacena la clausula de un contrato en el esquema DB_DOCUMENTO
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 26-01-2022    
     *                    
     * @return Response lista de ADMI_RESPUESTA.
     */
    public function guardarClausulasAction()
    {
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strUrlGuardarPreg   = $this->container->getParameter('ws_ms_documento_url').'guardarTerminosCondicion';
        $serviceRestClient   = $this->get('schema.RestClient');
        $arrayValoresDefault = array($objRequest->get("esDefaultEnunciado"));

        $strVisibleDocumento = ($objRequest->get("visibleEnDocumento") == "SI") ? 'S' : 'N';
        $arrayAtributo       = json_decode($objRequest->get("atributoEnunciado"), true);

        $arrayEnunciado[]     = array(
                                        'nombreEnunciado'                   => $objRequest->get("nombreEnunciado"),
                                        "descripcionEnunciado"              => $objRequest->get("descripcionEnunciado"),
                                        "tagPlantilla"                      => $objRequest->get("tagPlantilla"),
                                        "atributoEnunciado"                 => $arrayAtributo,
                                        "visibleEnDocumento"                => $strVisibleDocumento,
                                        "esDefaultEnunciado"                => $arrayValoresDefault,
                                        "requeridoParaContinuarEnunciado"   => $objRequest->get("requeridoParaContinuarEnunciado"),
                                        "requiereJustificacionEnunciado"    => $objRequest->get("requiereJustificacionEnunciado"),
                                        "tipoOpcionEnunciado"               => $objRequest->get("tipoOpcionEnunciado"),
                                        "arrRespuestas"                     => $objRequest->get("lstRespuestas")
        );

        $arrayParamClausula = array(
                                    'nombreProceso'     => self::NOMBRE_PROCESO,
                                    'nombreDocumento'   => self::NOMBRE_DOCUMENTO,
                                    'enunciado'         => $arrayEnunciado,
                                    'empresaCod'        => $strEmpresaCod,
                                    'ipCreacion'        => '127.0.0.1',
                                    'usrCreacion'       => $strUsrCreacion
        );

        $arrayOptions       = array(
                                        CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array(
                                            'Content-Type: application/json',
                                            'tokencas: ' . $arrayTokenCas['strToken']
                                        )
                                    );
        $arrayResponse      = $serviceRestClient->postJSON($strUrlGuardarPreg,
                                                           json_encode($arrayParamClausula),
                                                           $arrayOptions);
        $arrayRespuesta     = json_decode($arrayResponse['result'], true);
        $objResponse        = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * gridClausulasAction, método que obtiene un listado de clausulas en base al proceso y documento
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 26-01-2022    
     *                    
     * @return Response lista de ADMI_RESPUESTA.
     */
    public function gridClausulasAction()
    {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas();
        $strUrlObtenerGrid   = $this->container->getParameter('ws_ms_documento_url').'obtenerClausulaContrato';
        $serviceRestClient   = $this->get('schema.RestClient');

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');

        $intTotal            = 0;

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );

        $arrayParamClausula = array(
                                    'nombreProceso'     => self::NOMBRE_PROCESO,
                                    'nombreDocumento'   => self::NOMBRE_DOCUMENTO,
                                    'empresaCod'        => $strEmpresaCod,
                                    'ipCreacion'        => '127.0.0.1',
                                    'usrCreacion'       => $strUsrCreacion
                                    );
        $arrayResponse      = $serviceRestClient->postJSON($strUrlObtenerGrid,
                                                           json_encode($arrayParamClausula),
                                                           $arrayOptions);
        $arrayConsulta     = json_decode($arrayResponse['result'], true);

        if(isset($arrayConsulta['status']) && $arrayConsulta['status'] != 'OK')
        {
            throw new \Exception($arrayResultado['message']);
        }
        else
        {
            $intTotal   = count($arrayConsulta['data']['enunciado']);
            foreach($arrayConsulta['data']['enunciado'] as $arrayDatos)
            {
                $arrayRespPreg = array();
                foreach($arrayDatos['respuestas'] as $arrayResp)
                {
                    $arrayRespPreg[] = $arrayResp['valorRespuesta'].',';
                }
                $arrayClausula = array( 'strEncuesta'        => $arrayDatos['nombreenunciado'],
                                        'strDescripcion'     => $arrayDatos['descripcionenunciado'],
                                        'strRespuestas'      => trim(implode($arrayRespPreg), ','),
                                        'strProceso'         => $arrayConsulta['data']['nombreproceso'],
                                        'strDocumento'       => $arrayConsulta['data']['nombredocumento'],
                                        'arrayEvento'        => array('editar'     => $this->generateUrl('admi_plantillapregcontr_editarClausulas', 
                                                                                            array('intIdenunciado' => $arrayDatos['idenunciado'])),
                                                                      'idEnunciado'=> $arrayDatos['idenunciado']
                                                                    )
                                        );
                $arrayRegistros[]   = $arrayClausula;
            }
        }
        

        $arrayRespuesta     = array('intTotal'  => $intTotal,
                                    'data'      => $arrayRegistros);
        $objResponse        = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


    /**
     * getClausulaAction, método que obtiene un listado de clausulas en base al proceso y documento
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 26-01-2022    
     *                    
     * @return Response lista de ADMI_RESPUESTA.
     */
    public function getClausulaAction()
    {
        $objRequest          = $this->get('request');
        $intPuntoId          = $objRequest->get("puntoId");
        $objSession          = $objRequest->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');

        $arrayParamClausula = array(
                                    'nombreProceso'     => self::NOMBRE_PROCESO,
                                    'nombreDocumento'   => self::NOMBRE_DOCUMENTO,
                                    'puntoId'           => $intPuntoId,
                                    'empresaCod'        => $strEmpresaCod,
                                    'ipCreacion'        => '127.0.0.1',
                                    'usrCreacion'       => $strUsrCreacion
                                    );
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto   = $this->get('comercial.InfoPunto');
        $arrayConsulta      = $serviceInfoPunto->getConsultaDataEncuesta($arrayParamClausula);
        
        $objResponse        = new Response(json_encode($arrayConsulta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'editAction'.
     *
     * Función para editar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function editAction($intIdenunciado)
    {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas();
        $strUrlObtenerGrid   = $this->container->getParameter('ws_ms_documento_url').'obtenerEnunciado';
        $serviceRestClient   = $this->get('schema.RestClient');

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');

        $intTotal            = 0;

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );

        $arrayParamClausula = array(
                                    'nombreProceso'     => self::NOMBRE_PROCESO,
                                    'nombreDocumento'   => self::NOMBRE_DOCUMENTO,
                                    'enunciadoId'       => $intIdenunciado,
                                    'empresaCod'        => $strEmpresaCod,
                                    'ipCreacion'        => '127.0.0.1',
                                    'usrCreacion'       => $strUsrCreacion
                                    );
        $arrayResponse      = $serviceRestClient->postJSON($strUrlObtenerGrid,
                                                           json_encode($arrayParamClausula),
                                                           $arrayOptions);
        $arrayConsulta     = json_decode($arrayResponse['result'], true);
        
        return $this->render('administracionBundle:AdmiPreguntasContrato:edit.html.twig', 
                                 array('objEnunciado'              => $arrayConsulta
                                       ));
    }

    /**
     * Documentación para la función 'editAction'.
     *
     * Función para editar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function getCorreoAction()
    {
        $arrayConsulta       = array();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->get('request');
        $intIdPunto          = $objRequest->get("idPunto");
        $objSession          = $objRequest->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strNotificacionWhatsapp = "N";
        $arrayContacto= array();
        $arrayContactoNum= array();
        $arrayContacto[]="Correo Electronico";
        if(!isset($intIdPunto) || empty($intIdPunto) || $intIdPunto == "null")
        {
            $objPtoCliente  = $objSession->get('ptoCliente');
            $intIdPunto     = $objPtoCliente['id'];
        }
        $objPunto            = $emComercial->getRepository('schemaBundle:InfoPunto')
                                           ->find($intIdPunto);        
        if($objPunto)
        {
            $arrayParametrosDet     = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                      '',
                      '',
                      'NOTIFICACION_WHATSAPP',
                      '',
                      '',
                      '',
                      '',
                      '',
                      $strEmpresaCod );

                if(!empty($arrayParametrosDet))
                {
                    $strNotificacionWhatsapp= $arrayParametrosDet['valor1'];
                }

            $arrayFormaCont      = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                 ->findBy(array('descripcionFormaContacto' =>$arrayContacto,
                                                                   'estado' => 'Activo'
                                                                )
                                                            );
            $arrayAdmiFormContactEmp = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                   ->findBy(array('personaId'       => $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                                  'formaContactoId' => $arrayFormaCont,
                                                                  'estado'          => 'Activo'
                                                                  )
                                                                );
            $arrayCorreo       = array();    
            foreach($arrayAdmiFormContactEmp as $objAdmiFormaContactoEmp)
            {
                $arrayFormaContacto = array('clave' => $objAdmiFormaContactoEmp->getId(),
                                            'valor' => strtolower($objAdmiFormaContactoEmp->getValor()));
                $arrayCorreo[]=$arrayFormaContacto;
            }
            $arrayNumeroNum= array();                             
            if($strNotificacionWhatsapp=="S")  
            {
              $arrayContactoNum[]="Telefono Movil Movistar";
              $arrayContactoNum[]="Telefono Movil Claro";
              $arrayFormaContNum      = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
              ->findBy(array('descripcionFormaContacto' =>$arrayContactoNum,
                                  'estado' => 'Activo'
                              )
                            );
              $arrayAdmiFormContactEmpNum = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                            ->findBy(array('personaId'       => $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                  'formaContactoId' => $arrayFormaContNum,
                                                  'estado'          => 'Activo'
                                          )
                              );
                foreach($arrayAdmiFormContactEmpNum as $objAdmiFormaContactoEmp)
                {
                    $arrayFormaContactoNum = array('clave' => $objAdmiFormaContactoEmp->getId(),
                                                'valor' => strtolower($objAdmiFormaContactoEmp->getValor()));
                    $arrayNumeroNum[]=$arrayFormaContactoNum;
                }
            
            }
            $arrayDatosContacto []   = array('correos' => $arrayCorreo,
            'numeros' => $arrayNumeroNum);
        }
        $objResponse        = new Response(json_encode($arrayDatosContacto));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'getSolicitarInformacionAction'.
     *
     * Función para editar las claúsulas del contrato
     *
     * @return Json - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function getSolicitarInformacionAction()
    {
        $serviceTokenCas            = $this->get('seguridad.TokenCas');
        $serviceUtil                = $this->get('schema.Util');
        $serviceRestClient          = $this->get('schema.RestClient');
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $intPersonaFormaContacto    = $objRequest->get("intPersonaFormaContacto");
        $intPersonaFormaContactoNum    = $objRequest->get("intPersonaFormaContactNum");
        $intPersonaEmpRolId         = $objRequest->get("intPersonaEmpRolId");
        $intPuntoId                 = $objRequest->get("intPuntoId");
        $boolReenvio                = ($objRequest->get("boolReenvio")) ? $objRequest->get("boolReenvio") : false;
        $boolFormaPago              = ($objRequest->get("isEditarFormaPago")) ? $objRequest->get("isEditarFormaPago") : false;

        $strUsrCreacion             = $objSession->get('user');
        $strEmpresaCod              = $objSession->get('idEmpresa');
        $strUrlSolicitarInformacion = $this->container->getParameter('ws_ms_contrato_digital_url').'generarLinkClausulasBancarios';
        if(!isset($intPuntoId) || empty($intPuntoId) || $intPuntoId == "null")
        {
            $objPtoCliente  = $objSession->get('ptoCliente');
            $intPuntoId     = $objPtoCliente['id'];
        }
        try
        {
            $arrayTokenCas      = $serviceTokenCas->generarTokenCas();
            $arrayOptions       = array(
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_HTTPHEADER     => array(
                                                'Content-Type: application/json',
                                                'tokencas: ' . $arrayTokenCas['strToken']
                                            )
                                        );
            $arrayParamSolInfCliente = array(
                                             'idPersonaFormaContactNum' => $intPersonaFormaContactoNum,
                                             'idPersonaFormaContacto'   => $intPersonaFormaContacto,
                                             'puntoId'                  => $intPuntoId,
                                             'usrCreacion'              => $strUsrCreacion,
                                             'codEmpresa'               => $strEmpresaCod,
                                             'empresaCod'               => $strEmpresaCod,
                                             'personaEmpresaRolId'      => $intPersonaEmpRolId,
                                             'reenvioDatosInvalido'     => $boolReenvio,
                                             'isEditarFormaPago'        => $boolFormaPago
                                            );

            $arrayResponse      = $serviceRestClient->postJSON($strUrlSolicitarInformacion,
                                                                json_encode($arrayParamSolInfCliente),
                                                                $arrayOptions);
            $arrayConsulta     = json_decode($arrayResponse['result'], true);
        }
        catch (\Exception $e) 
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();
  
            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.getSolicitarInformacion',
                                        'Error AdmiPreguntasContrato.getSolicitarInformacion:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                     );
        }
        
        $objResponse        = new Response(json_encode($arrayConsulta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }

    /**
     * Documentación para la función 'editAction'.
     *
     * Función para editar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function getCreacionPuntoAction()
    {
        $serviceRestClient      = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();

        $intPuntoId             = $objRequest->get("intPuntoId");
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $boolFormaPago          = ($objRequest->get("isEditarFormaPago")) ? $objRequest->get("isEditarFormaPago") : false;

        if(!isset($intPuntoId) || empty($intPuntoId) || $intPuntoId == "null")
        {
            $objPtoCliente  = $objSession->get('ptoCliente');
            $intPuntoId     = $objPtoCliente['id'];
        }

        try
        {
            if(!isset($intPuntoId) || empty($intPuntoId) || $intPuntoId == "null")
            {
                $arrayRespuesta = array('status' => 'ERROR', 'message' => 'No se encontró el punto de cliente');
            }
            else
            {
                $arrayParametros    = array(
                                            'puntoId'               => $intPuntoId,
                                            'usrCreacion'           => $strUsrCreacion,
                                            'personaEmpresaRolId'   => $intIdPersonEmpresaRol,
                                            'empresaCod'            => $strEmpresaCod    ,
                                            'isEditarFormaPago'     => $boolFormaPago
                                        );
                /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                $arrayRespuesta     = $serviceInfoPunto->getDataLinksContratoCliente($arrayParametros);
            }
        }
        catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de obtener los datos bacarios. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.getCreacionPuntoAction',
                                        'Error AdmiPreguntasContrato.getCreacionPuntoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }

        $objResponse        = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'eliminarEnunciadoAction'.
     *
     * Función para eliminar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function eliminarEnunciadoAction()
    {
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUrlEliminarEnunciado= $this->container->getParameter('ws_ms_documento_url').'eliminaClausula';
        $serviceRestClient      = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');

        $intIdEnunciado         = $objRequest->get("idEnunciado");

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');

        try
        {
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();
            $arrayOptions       = array(
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_HTTPHEADER     => array(
                                                'Content-Type: application/json',
                                                'tokencas: ' . $arrayTokenCas['strToken']
                                            )
                                        );
            $arrayParametros    = array(
                                        'enunciadoId'           => $intIdEnunciado,
                                        'usrCreacion'           => $strUsrCreacion,
                                        'empresaCod'            => $strEmpresaCod    
                                       );

            $arrayResponse      = $serviceRestClient->postJSON($strUrlEliminarEnunciado,
                                                               json_encode($arrayParametros),
                                                               $arrayOptions);
            $arrayConsulta     = json_decode($arrayResponse['result'], true);
        }
        catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de eliminar los datos de enunciados. Favor Notificar a Sistemas" . 
                                         $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.eliminarEnunciadoAction',
                                        'Error AdmiPreguntasContrato.eliminarEnunciadoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayConsulta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'eliminarPreguntaAction'.
     *
     * Función para eliminar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function datosInicialesAction()
    {
        $serviceRestClient      = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        $servicePreCliente      = $this->get('comercial.PreCliente');
        $objRequest             = $this->get('request');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $objSession             = $objRequest->getSession();
        $strMostrarDatoBancario = 'N';
        $strMostrarClausula     = 'N';
        $strMostrarInfoBanco    = 'N';
        $strEsPreCliente        = 'N';

        $intIdPersonEmpresaRol  = ($objRequest->get('intPersonaEmpRolId')) ? 
                                    $objRequest->get('intPersonaEmpRolId') : 0;

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');

        try
        {
            $arrayParametrosDet     = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                                                            '',
                                                            '',
                                                            'MOSTRAR CLAUSULA',
                                                            '',
                                                            'CLAUSULA_CONTRATO',
                                                            '',
                                                            '',
                                                            '',
                                                            $strEmpresaCod);

            if(!empty($arrayParametrosDet))
            {
                $strMostrarClausula = $arrayParametrosDet['valor1'];
            }

            $arrayParametrosDet     = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                                                           '',
                                                           '',
                                                           'MOSTRAR DATOS BANCARIO',
                                                           '',
                                                           'LINK_BANCARIO',
                                                           '',
                                                           '',
                                                           '',
                                                           $strEmpresaCod);

            if(!empty($arrayParametrosDet))
            {
                $strMostrarInfoBanco = $arrayParametrosDet['valor1'];
            }
            $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($intIdPersonEmpresaRol);

            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->getPersonaEmpresaRolPorPersonaPorTipoRolNew(array('intIdPersona'  => $objPersonaEmpresaRol
                                                                                                                           ->getPersonaId()->getId(),
                                                                                                        'strDescRol'    => 'Pre-cliente',
                                                                                                        'intCodEmpresa' =>  $strEmpresaCod));
            if(is_object($objInfoPersonaEmpresaRol))
            {
                $strEsPreCliente    = 'S';
            } 

            $arrayResultado = array('formaPagoId'           => 0,
                                    'tipoCuentaId'          => 0,
                                    'bancoTipoCuentaId'     => 0,
                                    'strMostrarClausula'    => $strMostrarClausula,
                                    'strMostrarInfoBanco'   => $strMostrarInfoBanco,
                                    'esRolCliente'          => $strEsPreCliente,
                                    'status'                => 'OK',
                                    'message'               => 'OK',
                                    'esDebitoBancario'      => $strMostrarDatoBancario
                                    );
            /* @var $entityPersonaEmpFormaPago \telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago */
            $entityPersonaEmpFormaPago = $servicePreCliente->getDatosPersonaEmpFormaPago($objPersonaEmpresaRol->getPersonaId()->getId(),
                                                                                          $strEmpresaCod);
            if(is_object($entityPersonaEmpFormaPago))
            {
                if(is_object($entityPersonaEmpFormaPago->getFormaPagoId()) && 
                    $entityPersonaEmpFormaPago->getFormaPagoId()->getCodigoFormaPago() == 'DEB')
                {
                    $strMostrarDatoBancario = 'S';
                }

                $arrayResultado['formaPagoId']           = (is_object($entityPersonaEmpFormaPago->getFormaPagoId()) ?
                                                                $entityPersonaEmpFormaPago->getFormaPagoId()->getId() : 0);
                $arrayResultado['tipoCuentaId']          = ($entityPersonaEmpFormaPago->getTipoCuentaId() ?
                                                                $entityPersonaEmpFormaPago->getTipoCuentaId()->getId() : null);
                $arrayResultado['bancoTipoCuentaId']     = ($entityPersonaEmpFormaPago->getBancoTipoCuentaId() ?
                                                                $entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getId() : null);
                $arrayResultado['esDebitoBancario']      = $strMostrarDatoBancario;
                $arrayResultado['esTarjeta']             = ($entityPersonaEmpFormaPago->getBancoTipoCuentaId() ?
                                                            $entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getEsTarjeta() : null);

            }
        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso de obtener los datos bacarios. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.formaPagoAction',
                                        'Error AdmiPreguntasContrato.formaPagoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'eliminarPreguntaAction'.
     *
     * Función para eliminar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function actualizarEnunciadoAction()
    {
        $arrayResultado         = array();
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUrlActualizar       = $this->container->getParameter('ws_ms_documento_url').'actualizarAdmEnunciado';
        $serviceRestClient      = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $strVisibleEnDocumento  = 'N';
        $strValorVisible        = $objRequest->get('visibleEnDocumento');
        $arrayAtributo          = json_decode($objRequest->get("atributoEnunciado"), true);
        try
        {
            $arrayTokenCas       = $serviceTokenCas->generarTokenCas();
            $arrayValoresDefault = array($objRequest->get("esDefaultEnunciado"));
            if(isset($strValorVisible) && $strValorVisible == 'SI')
            {
                $strVisibleEnDocumento = 'S';
            }
            $arrayEnunciado = array(
                                    "enunciadoId"                       => $objRequest->get('enunciadoId'),
                                    "tituloEnunciado"                   => $objRequest->get('tituloEnunciado'),
                                    "descripcionEnunciado"              => $objRequest->get('descripcionEnunciado'),
                                    "atributoEnunciado"                 => $arrayAtributo,
                                    "tagPlantilla"                      => $objRequest->get('tagPlantilla'),
                                    "visibleEnDocumento"                => $strVisibleEnDocumento,
                                    "esDefaultEnunciado"                => $arrayValoresDefault,
                                    "requeridoParaContinuarEnunciado"   => $objRequest->get('requeridoParaContinuarEnunciado'),
                                    "requiereJustificacionEnunciado"    => "S",
                                    "tipoOpcionEnunciado"               => "check",
                                    "lstRespuestas"                     => $objRequest->get('lstRespuestas')
            );
            $arrayParamClausula = array(
                                        'enunciado'         => $arrayEnunciado,
                                        'empresaCod'        => $strEmpresaCod    ,
                                        'usrCreacion'       => $strUsrCreacion
                                        );

            $arrayOptions       = array(
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_HTTPHEADER     => array(
                                                'Content-Type: application/json',
                                                'tokencas: ' . $arrayTokenCas['strToken']
                                            )
                                        );
            $arrayResponse      = $serviceRestClient->postJSON($strUrlActualizar,
                                                                json_encode($arrayParamClausula),
                                                                $arrayOptions);
            $arrayResultado     = json_decode($arrayResponse['result'], true);
        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso de actualizar los enunciados. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.actualizarEnunciadoAction',
                                        'Error AdmiPreguntasContrato.actualizarEnunciadoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'guardarEncBancAction'.
     *
     * Función para guaradar las claúsulas y datos bancarios del contrato
     *
     * @return Json - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function guardarEncBancAction()
    {
        $arrayResultado         = array();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $serviceUtil            = $this->get('schema.Util');

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');

        try
        {

            $arrayParamClausula = array(
                                        'puntoId'           => $objRequest->get('puntoId'),
                                        'clausulas'         => $objRequest->get('clausulas'),
                                        'dataBancario'      => $objRequest->get('dataBancario'),
                                        'empresaCod'        =>  $strEmpresaCod,
                                        'usrCreacion'       => $strUsrCreacion,
                                        'ipCreacion'        => '127.0.0.1'
                                        );

            /* @var $serviceContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceContrato     = $this->get('comercial.InfoContrato');
            $arrayResultado      = $serviceContrato->guardarClausulasOrDataBancaria($arrayParamClausula);
        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso de actualizar los enunciados. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.actualizarEnunciadoAction',
                                        'Error AdmiPreguntasContrato.actualizarEnunciadoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'guardarEncBancAction'.
     *
     * Función para guaradar las claúsulas y datos bancarios del contrato
     *
     * @return Json - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function actualizarEstadoClausulaAction()
    {
        $arrayResultado         = array();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $serviceUtil            = $this->get('schema.Util');

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');

        try
        {

            $arrayParamClausula = array(
                                        'puntoId'           => $objRequest->get('puntoId'),
                                        'estado'            => 'Activo',
                                        'empresaCod'        =>  $strEmpresaCod,
                                        'usrCreacion'       => $strUsrCreacion,
                                        'ipCreacion'        => '127.0.0.1'
                                        );

            /* @var $serviceContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceContrato     = $this->get('comercial.InfoContrato');
            $arrayResultado      = $serviceContrato->actualizarEstadoClausula($arrayParamClausula);
        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso de actualizar los enunciados. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.actualizarEstadoClausulaAction',
                                        'Error AdmiPreguntasContrato.actualizarEstadoClausulaAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'editAction'.
     *
     * Función para editar las claúsulas del contrato
     *
     * @return Response - html.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 16-03-2022
     */
    public function obtenerInformacionClienteAction()
    {
        $serviceRestClient      = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();

        $intPuntoId             = $objRequest->get("intPuntoId");
        $intIdPersonEmpresaRol  = $objRequest->get("intIdPersonaEmpresaRol");

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $boolFormaPago          = ($objRequest->get("isEditarFormaPago")) ? $objRequest->get("isEditarFormaPago") : false;

        if(!isset($intPuntoId) || empty($intPuntoId) || $intPuntoId == "null" )
        {
            $objPtoCliente  = $objSession->get('ptoCliente');
            $intPuntoId     = $objPtoCliente['id'];
        }

        try
        {
            $arrayParametros    = array(
                                        'puntoId'               => $intPuntoId,
                                        'personaEmpresaRolId'   => $intIdPersonEmpresaRol,
                                        'usrCreacion'           => $strUsrCreacion,
                                        'personaEmpresaRolId'   => $intIdPersonEmpresaRol,
                                        'empresaCod'            => $strEmpresaCod,
                                        'isEditarFormaPago'     => $boolFormaPago
                                       );
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto   = $this->get('comercial.InfoPunto');
            $arrayRespuesta     = $serviceInfoPunto->getObtieneInformacionCliente($arrayParametros);
        }
        catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de obtener los datos bacarios. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiPreguntasContrato.getCreacionPuntoAction',
                                        'Error AdmiPreguntasContrato.getCreacionPuntoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }

        $objResponse        = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

}
