<?php

/*
 * ENVIO DE SMS
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiParametroDet;

class EnvioSmsController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * 
     * Metodo Index que muestra y permite modificar si se notifica o no, por Mensajes de Texto,
     *  las credenciales de Fox, Paramount y Noggin
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @since 1.0 Version Inicial
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 01-08-2021 Se elimina parámetro que ya no será usado en la activación/desactivación de envío de SMS por producto
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     * 
     * @Secure(roles="ROLE_454-1")
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_454-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_454-1';
        }
        return $this->render('administracionBundle:EnvioSms:index.html.twig', 
                             array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * 
     * Metodo que realiza la actualizacion del estado del servicio de Mensajes de Texto
     * para los productos Fox, Paramount y Noggin
     * 
     * @author Jonathan Mazón Sánchez  <jmazon@telconet.ec>
     * @since 1.0 Version Inicial
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 01-08-2021 Se actualizan los parámetros para activación y desactivación de envio de SMS pero ahora considerándolo por producto
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     * 
     * @Secure(roles="ROLE_454-1")
     */
    public function updateAction()
    {
        $objJsonResponse            = new JsonResponse();
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $emComercial                = $this->getDoctrine()->getManager('telconet');
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $strUsrCreacion             = $objSession->get('user');
        $strIpCreacion              = $objRequest->getClientIp();
        $strNombreTecnicoProducto   = $objRequest->get('nombreTecnicoProducto') ? $objRequest->get('nombreTecnicoProducto') : "";
        $strAccionAEjecutar         = $objRequest->get('accionAEjecutar') ? $objRequest->get('accionAEjecutar') : "";
        $serviceUtil                = $this->get('schema.Util');
        $emGeneral->getConnection()->beginTransaction(); 
        try
        {
            if(empty($strNombreTecnicoProducto))
            {
                throw new \Exception("No se ha enviado el nombre técnico del producto");
            }
            $objProducto    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                          ->findOneBy(array("nombreTecnico" => $strNombreTecnicoProducto,
                                                            "empresaCod"    => $strCodEmpresa));
            if(!is_object($objProducto))
            {
                throw new \Exception("No se encontró el Producto asociado al nombre técnico ".$strNombreTecnicoProducto);
            }
            
            $arrayProductosParamsEnvioSMS   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get(  'ENVIO_SMS_POR_PRODUCTO',
                                                                '',
                                                                '',
                                                                '',
                                                                'NOMBRE_TECNICO',
                                                                $strNombreTecnicoProducto,
                                                                '',
                                                                '',
                                                                '',
                                                                $strCodEmpresa);
            if(isset($arrayProductosParamsEnvioSMS) && !empty($arrayProductosParamsEnvioSMS))
            {
                foreach($arrayProductosParamsEnvioSMS as $arrayProductoParamEnvioSMS)
                {
                    $objParamDetProductoParamEnvioSMS = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->find($arrayProductoParamEnvioSMS["id"]);
                    if(is_object($objParamDetProductoParamEnvioSMS))
                    {
                        $objParamDetProductoParamEnvioSMS->setEstado("Eliminado");
                        $objParamDetProductoParamEnvioSMS->setUsrUltMod($strUsrCreacion);
                        $objParamDetProductoParamEnvioSMS->setFeUltMod(new \DateTime('now'));
                        $emGeneral->persist($objParamDetProductoParamEnvioSMS);
                        $emGeneral->flush();
                    }
                }
            }
            
            $objParametroEnvioSMSPorProducto    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy(array(
                                                                                'nombreParametro'   => 'ENVIO_SMS_POR_PRODUCTO',
                                                                                'estado'            => 'Activo'
                                                                             )
                                                                        );
            if(is_object($objParametroEnvioSMSPorProducto))
            {
                if($strAccionAEjecutar === "activar")
                {
                    $strPermiteEnvioSMSPorProducto = "SI";
                }
                else
                {
                    $strPermiteEnvioSMSPorProducto = "NO";
                }
                $objAdmiParametroDet = new AdmiParametroDet();
                $objAdmiParametroDet->setParametroId($objParametroEnvioSMSPorProducto);
                $objAdmiParametroDet->setDescripcion("Valor1:Campo del producto,Valor2:Valor del producto,Valor3:SI/NO se permite el envío de SMS");
                $objAdmiParametroDet->setValor1("NOMBRE_TECNICO");
                $objAdmiParametroDet->setValor2($strNombreTecnicoProducto);
                $objAdmiParametroDet->setValor3($strPermiteEnvioSMSPorProducto);
                $objAdmiParametroDet->setEstado("Activo");
                $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
                $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                $objAdmiParametroDet->setEmpresaCod($strCodEmpresa);
                $emGeneral->persist($objAdmiParametroDet);
                $emGeneral->flush();
            }
            else
            {
                throw new \Exception("No se encontró el parámetro principal ENVIO_SMS_POR_PRODUCTO");
            }
            $emGeneral->getConnection()->commit();
            $strMensaje = 'OK';
        }
        catch(\Exception $e)
        {
            $strMensaje = 'Ha ocurrido un problema al '.$strAccionAEjecutar.' el envío de SMS. Por favor notificar a Sistemas!';
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
            $emGeneral->getConnection()->close();
            $serviceUtil->insertError(  'Telcos+', 
                                        'EnvioSmsController->updateAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpCreacion);
        }
        $objJsonResponse->setContent($strMensaje);
        return $objJsonResponse;
    }

    /**
     * @Secure(roles="ROLE_454-1")
     * 
     * Documentación para el método 'gridAction'.
     * 
     * Muestra el listado de productos parametrizados para la activación y desactivación del envío de SMS
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2021 
     * 
     */
    public function gridAction()
    {
        $objJsonResponse                = new JsonResponse();
        $objRequest                     = $this->get('request');
        $objSession                     = $objRequest->getSession();
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $emComercial                    = $this->getDoctrine()->getManager('telconet');
        $emGeneral                      = $this->getDoctrine()->getManager('telconet_general');
        $intTotal                       = 0;
        $arrayListadoProductosEnvioSMS  = array();
        $arrayProductosParamsEnvioSMS   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get(  'ENVIO_SMS_POR_PRODUCTO',
                                                            '',
                                                            '',
                                                            '',
                                                            'NOMBRE_TECNICO',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strCodEmpresa);
        
        if(isset($arrayProductosParamsEnvioSMS) && !empty($arrayProductosParamsEnvioSMS))
        {
            foreach( $arrayProductosParamsEnvioSMS as $arrayEnvioSMSPorProducto)
            {
                $intIdParametroDetEnvioSMS  = $arrayEnvioSMSPorProducto['id'];
                $strDescripcionProducto     = "";
                $strNombreTecnicoProducto   = $arrayEnvioSMSPorProducto['valor2'];
                $strPermiteEnvioSMS         = $arrayEnvioSMSPorProducto['valor3'];
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
                $arrayListadoProductosEnvioSMS[]    = array("idParametroDetEnvioSMS"    => $intIdParametroDetEnvioSMS,
                                                            "descripcionProducto"       => $strDescripcionProducto,
                                                            "nombreTecnicoProducto"     => $strNombreTecnicoProducto,
                                                            "permiteEnvioSMS"           => $strPermiteEnvioSMS,
                                                            "activaEnvioSMS"            => $strPermiteEnvioSMS === "SI" ? "NO" : "SI",
                                                            "desactivaEnvioSMS"         => $strPermiteEnvioSMS === "SI" ? "SI" : "NO"
                                                        );
                $intTotal++;
            }
        }
        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayListadoProductosEnvioSMS);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }
}