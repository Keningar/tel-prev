<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiParametroDet;

class CorreoFormularioSoporteController extends Controller
{ 
    /**
     * 
     * Metodo Index que muestra en la administración el grid de contacto para el formulario de soporte.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 10-09-2021  Version Inicial
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     * 
     * @Secure(roles="ROLE_469-1")
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_469-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_469-1';
        }
        return $this->render('administracionBundle:CorreoFormularioSoporte:index.html.twig', 
                             array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_469-1")
     * 
     * Documentación para el método 'gridAction'.
     * 
     * Muestra el listado de productos parametrizados para la administración de correo del formulario de soporte.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 10-09-2021  Version Inicial
     * 
     */
    public function gridAction()
    {
        $objJsonResponse                            = new JsonResponse();
        $objRequest                                 = $this->get('request');
        $objSession                                 = $objRequest->getSession();
        $strCodEmpresa                              = $objSession->get('idEmpresa');
        $emComercial                                = $this->getDoctrine()->getManager('telconet');
        $emGeneral                                  = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil                                = $this->get('schema.Util');
        $strIpUsuarioCreacion                       = $objRequest->getClientIp();
        $strUsrCreacion                             = $objSession->get('user');
        $intTotal                                   = 0;
        $arrayListadoProductosAdmiFormularioSoporte = array();
        try
        {
            $arrayProductosParamsAdmiFormSoporte        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get(  'ADMI_CORREO_FORMULARIO_SOPORTE',
                                                                            'ADMINISTRACION',
                                                                            'FORMULARIO_SOPORTE',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $strCodEmpresa);
            
            if(isset($arrayProductosParamsAdmiFormSoporte) && !empty($arrayProductosParamsAdmiFormSoporte))
            {
                foreach( $arrayProductosParamsAdmiFormSoporte as $arrayFormularioPorProducto)
                {
                    $intIdParametroDetcorreoSoporte  = $arrayFormularioPorProducto['id'];
                    $strNombreTecnicoProducto        = $arrayFormularioPorProducto['valor1'];
                    if(isset($strNombreTecnicoProducto) && !empty($strNombreTecnicoProducto))
                    {
                        $objProducto    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                      ->findOneBy(array("nombreTecnico" => $strNombreTecnicoProducto,
                                                                        "empresaCod"    => $strCodEmpresa));
                        if(is_object($objProducto))
                        {
                            $strDescripcionProducto = $objProducto->getDescripcionProducto();
                        }
                    }
                    $arrayListadoProductosAdmiFormularioSoporte[]    = array("idParametroDetEnvioSMS"    => $intIdParametroDetcorreoSoporte,
                                                                             "descripcionProducto"       => $strDescripcionProducto,
                                                                             "nombreTecnicoProducto"     => $strNombreTecnicoProducto
                                                                            );
                    $intTotal++;
                }
            }

            $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayListadoProductosAdmiFormularioSoporte);
            $objJsonResponse->setData($arrayRespuesta);
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => $ex, 'total' => 0, 'encontrados' => '');
            $objJsonResponse->setData($arrayRespuesta);

            $serviceUtil->insertError('Telcos+',
                                      'CorreoFormularioSoporteController.gridAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpUsuarioCreacion
                                     );
        }
        return $objJsonResponse;
    }

    /**
     * @Secure(roles="ROLE_469-1")
     * 
     * Documentación para el método 'gridCorreoAction'.
     * 
     * Muestra el listado de productos parametrizados para la administración de correo.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 10-09-2021  Version Inicial
     * 
     */
    public function gridCorreoAction()
    {
        $objJsonResponse            = new JsonResponse();
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $strNombreTecnicoProducto   = $objRequest->get('nombreProducto');
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil                = $this->get('schema.Util');
        $strIpUsuarioCreacion       = $objRequest->getClientIp();
        $strUsrCreacion             = $objSession->get('user');
        $arrayListadoCorreo         = array();
        try
        {
            //acceder a los correos guardados en la parametro det
            $arrayCorreoDestinatario                    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get(  'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE',
                                                                            'TECNICO',
                                                                            'FORMULARIO_SOPORTE',
                                                                            'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE',
                                                                            $strNombreTecnicoProducto,'','','','',
                                                                            $strCodEmpresa);
    
            if(isset($arrayCorreoDestinatario) && !empty($arrayCorreoDestinatario))
            {
                foreach( $arrayCorreoDestinatario as $arrayCorreo)
                {
                    //asignar valor
                    $arrayListadoCorreo[]   = array("idParametroDetCorreoForm"  => $arrayCorreo['id'],
                                                    "formaContacto"             => 'Correo Electronico',
                                                    "valor"                     => $arrayCorreo['valor2']
                                                    );
                }
            }
    
           
            $arrayRespuesta = array('total' => count($arrayListadoCorreo), 'encontrados' => $arrayListadoCorreo);
            $objJsonResponse->setData($arrayRespuesta);
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => $ex, 'total' => 0, 'encontrados' => '');
            $objJsonResponse->setData($arrayRespuesta);

            $serviceUtil->insertError('Telcos+',
                                      'CorreoFormularioSoporteController.gridCorreoAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpUsuarioCreacion
                                     );
        }
        return $objJsonResponse;
    }

    /**
     * @Secure(roles="ROLE_469-1")
     * 
     * Documentación para el método 'registrarCorreoAction'.
     * 
     * Guarda, actualiza y elimina el correo para el Formulario Soporte.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 10-09-2021  Version Inicial
     * 
     */
    public function registrarCorreoAction()
    {
        $objJsonResponse                = new JsonResponse();
        $objRequest                     = $this->get('request');
        $objSession                     = $objRequest->getSession();
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $strUsrCreacion                 = $objSession->get('user');
        $strIpCreacion                  = $objRequest->getClientIp();
        $strNombreTecnicoProducto       = $objRequest->get('nombreProducto');
        $arrayData                      = json_decode($objRequest->get('array_data'), true);
        $serviceUtil                    = $this->get('schema.Util');
        $emGeneral                      = $this->getDoctrine()->getManager('telconet_general');
        $objParamDetFormularioSoporte   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro'=>'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'));
        //acceder a los correos guardados en la parametro det
        $arrayCorreoDestinatario                    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(  'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE',
                                                                        'TECNICO',
                                                                        'FORMULARIO_SOPORTE',
                                                                        'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE',
                                                                        $strNombreTecnicoProducto,'','','','',
                                                                        $strCodEmpresa);
        try
        {
            //Barrido de data a guardar y eliminar
            foreach($arrayData as $intClave => $arrayCorreoDataJs) // data del js
            {
                foreach($arrayCorreoDestinatario as $intIndice => $arrayAdmiContacto)// data del parameter det
                {
                    if($arrayCorreoDataJs['valor'] == $arrayAdmiContacto['valor2'])
                    {
                        unset($arrayData[$intClave]);
                        unset($arrayCorreoDestinatario[$intIndice]);
                    }
                    if($arrayCorreoDataJs['idParametroDetCorreoForm'] == $arrayAdmiContacto['id'])
                    {
                        unset($arrayCorreoDestinatario[$intIndice]);
                    }
                }
            }
    
            //logica de guardado y actualizado
            if(is_array($arrayData) && !empty($arrayData) && is_object($objParamDetFormularioSoporte))
            {
                foreach($arrayData as $arrayValorAGuardar)
                {
                    //guardar
                    if($arrayValorAGuardar['idParametroDetCorreoForm'] === 0)
                    {
                        //guardar en la InfoServicioProdCaract el correo nuevo
                        $objAdmiParametroDet = new AdmiParametroDet;
                        $objAdmiParametroDet->setParametroId($objParamDetFormularioSoporte);
                        $objAdmiParametroDet->setDescripcion('CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE');
                        $objAdmiParametroDet->setValor1($strNombreTecnicoProducto);
                        $objAdmiParametroDet->setValor2($arrayValorAGuardar['valor']);
                        $objAdmiParametroDet->setEstado('Activo');
                        $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
                        $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                        $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                        $objAdmiParametroDet->setEmpresaCod($strCodEmpresa);
                        $emGeneral->persist($objAdmiParametroDet);
                    }
                    //actualizar
                    else
                    {
                        $objParamDetValorActualizar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->findOneById($arrayValorAGuardar["idParametroDetCorreoForm"]);
                        if(is_object($objParamDetValorActualizar))
                        {
                            $objParamDetValorActualizar->setValor2($arrayValorAGuardar["valor"]);
                            $objParamDetValorActualizar->setUsrUltMod($strUsrCreacion);
                            $objParamDetValorActualizar->setFeUltMod(new \DateTime('now'));
                            $objParamDetValorActualizar->setIpCreacion($strIpCreacion);
                            $emGeneral->persist($objParamDetValorActualizar);
                        }
                    }
                }
            }
            //logica de eliminar 
            if(is_array($arrayCorreoDestinatario) && !empty($arrayCorreoDestinatario))
            {
                foreach($arrayCorreoDestinatario as $arrayValorAEliminar)
                {
                    $objParamDetValorEliminar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->findOneById($arrayValorAEliminar["id"]);
                        if(is_object($objParamDetValorEliminar))
                        {
                            $objParamDetValorEliminar->setEstado("Eliminado");
                            $objParamDetValorEliminar->setUsrUltMod($strUsrCreacion);
                            $objParamDetValorEliminar->setFeUltMod(new \DateTime('now'));
                            $objParamDetValorEliminar->setIpCreacion($strIpCreacion);
                            $emGeneral->persist($objParamDetValorEliminar);
                        }
                }
    
            }
            //grabar
            $emGeneral->flush();

            $strMensaje = 'OK';

            
        }
        catch(\Exception $ex)
        {
            $strMensaje = "Ocurrió un error al realizar el guardado de los Correos, por favor consulte con el Administrador";

            $serviceUtil->insertError('Telcos+',
                                      'CorreoFormularioSoporteController.registrarCorreoAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                     );
        }

        return new Response($strMensaje);

    }
}
