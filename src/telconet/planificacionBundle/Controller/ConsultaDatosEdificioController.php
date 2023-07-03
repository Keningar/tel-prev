<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class ConsultaDatosEdificioController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_484-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 31-10-2022
     * 
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_484-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_484-1'; //INDEX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_484-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_484-7'; //GRID
        }
        if(true === $this->get('security.context')->isGranted('ROLE_484-8717'))
        {
            $arrayRolesPermitidos[] = 'ROLE_484-8717'; //EDITARFACTIBILIDAD
        }
        
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("487", "1");

        return $this->render('planificacionBundle:Factibilidad:indexConsultaDatosEdificio.html.twig', array(
                             'item'            => $entityItemMenu,
                             'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_484-7")
     * 
     * Documentación para el método 'ajaxGridAction'.
     * 
     * Llena el grid de consulta.
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 31-10-2022
     * 
     */
    public function ajaxGridAction()
    {
        ini_set('max_execution_time', 3000000);

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        
        $objPeticion = $this->get('request');
        $strCodEmpresa = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
		
        $arrayFechaDesdePlanif = explode('T',$objPeticion->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif = explode('T',$objPeticion->get('fechaHastaPlanif'));
        
        $strLogin = $objPeticion->get('login2');
        $strDescripcionPunto = $objPeticion->get('descripcionPunto');
        $strVendedor = $objPeticion->get('vendedor');
        $strCiudad = $objPeticion->get('ciudad');
        
        $intStart = $objPeticion->get('start');
        $intLimit = $objPeticion->get('limit');
        
        //Consulta de parámetros
        $arrayEstadosPermitidos = array();

        $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('CONSULTA_DATOS_EDIFICIO', 
                                                'COMERCIAL', 
                                                'COMERCIAL', 
                                                '', 
                                                'ESTADOS_PERMITIDOS', 
                                                '','','','', 
                                                $strCodEmpresa);

        if( !empty($arrayParametrosDet) )
        {
            $arrayEstadosPermitidos = explode('|',$arrayParametrosDet['valor2']);
        }
        else
        {
            throw new \Exception('No se encontró el parámetro CONSULTA_DATOS_EDIFICIO');
        }

        $arrayParametros = array("emComercial"               => $emComercial,
                                "intStart"                   => $intStart,
                                "intLimit"                   => $intLimit,
                                "strSearchFechaDesdePlanif"  => $arrayFechaDesdePlanif[0],
                                "strSearchFechaHastaPlanif"  => $arrayFechaHastaPlanif[0],
                                "strSearchLogin"             => $strLogin,
                                "strSearchDescripcionPunto"  => $strDescripcionPunto,
                                "strSearchVendedor"          => $strVendedor,
                                "strSearchCiudad"            => $strCiudad,
                                "strCodEmpresa"              => $strCodEmpresa,
                                "arrayEstadosServicio"       => $arrayEstadosPermitidos);

        $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                ->generarJsonServiciosEstado($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    

    /**
     * @Secure(roles="ROLE_484-8717")
     * 
     * Documentación para el método 'ajaxProcesaNuevaFactibilidadAction'.
     * 
     * Proceso que ejecuta la verificación de factibilidad con los nuevos datos de edificio
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 31-10-2022
     * 
     */
    public function ajaxProcesaNuevaFactibilidadAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');
        $serviceUtil            = $this->get('schema.Util');
        
        $objPeticion            = $this->get('request');
        $objSession             = $objPeticion->getSession();
        $strUserSession         = $objSession->get('user');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strIpSession           = $objPeticion->getClientIp();
        $strDependeDeEdificio   = $objPeticion->get('strDependeDeEdificio');
        $intElementoEdificioId  = $objPeticion->get('intElementoEdificioId');
        $strElementoEdificio    = $objPeticion->get('strElementoEdificio');
        $strTipoEdificio        = $objPeticion->get('strTipoEdificio');
        $intIdPersonaEmpresaRol = $objPeticion->get('intIdPersonaEmpresaRol');
        $intIdServicio          = $objPeticion->get('intIdServicio');
        $intIdPunto             = $objPeticion->get('id_punto');

        $intElementoEdificioIdActual = null;
        $boolStatus             = true;
        $strMensaje             = "Se asigno nueva factibilidad por cambio de datos en Edificio";

        try
        {
            if(!$emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->beginTransaction();
            }
            if(!$emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->beginTransaction();
            }

            //Consulta datos actuales de edificacion
            $objInfoPuntoDatoAdicional  = $emComercial->getRepository("schemaBundle:InfoPuntoDatoAdicional")->findOneByPuntoId($intIdPunto);
            if(!is_object($objInfoPuntoDatoAdicional))
            {
                throw new \Exception('No se encontró información del punto. Favor notificar a sistemas');
            }

            if($objInfoPuntoDatoAdicional->getElementoId() && $objInfoPuntoDatoAdicional->getDependeDeEdificio() == "S")
            {
                $intElementoEdificioIdActual = $objInfoPuntoDatoAdicional->getElementoId()->getId();
            }

            //Verifica que los datos ingresados sean diferentes a los actuales
            if(($strDependeDeEdificio == "S" && $intElementoEdificioIdActual == $intElementoEdificioId)
                || ($strDependeDeEdificio == "N" && !$intElementoEdificioIdActual))
            {
                throw new \Exception('Los datos de Edificio son iguales. No se ejecuta proceso.');
            }
            
            //Define operacion a realizar
            if($strDependeDeEdificio == "S" && $intElementoEdificioIdActual)
            {
                $strMensaje = "Se realiza actualizacion de los datos de edificio del punto.";
            }
            else if($strDependeDeEdificio == "S" && !$intElementoEdificioIdActual)
            {
                $strMensaje = "Se agregan datos de edificio en el punto.";
            }
            else if($strDependeDeEdificio == "N" && $intElementoEdificioIdActual)
            {
                $strMensaje = "Se eliminan los datos de edificio del punto.";
            }

            //Verifica datos del edificio seleccionado y actualiza elementoid.
            if ($strDependeDeEdificio == "S")
            {

                //Obtenemos el modelo del elemento.
                $objModelo = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                            ->findModeloElementoPorCriterios(array("strNombre" => $strTipoEdificio,
                                                                                    "strEstado" => "Activo"));

                if (!is_object($objModelo) || empty($strTipoEdificio))
                {
                    throw new \Exception('No se pudo obtener el modelo del elemento.');
                }

                //los elementos en qué estados pueden estar para poderlos asignar??
                $arrayEstadosElemento = array("Activo", "Pendiente", "Factible", "PreFactibilidad");

                $objInfoElemento = $emComercial->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy(array(
                                                "nombreElemento"   => trim(strtoupper($strElementoEdificio)),
                                                "estado"           => $arrayEstadosElemento,
                                                "modeloElementoId" => $objModelo->getId()
                                            ));

                if (!is_object($objInfoElemento))
                {
                    throw new \Exception('No se encontró datos del elemento por la Edificación seleccionada.');
                }

                $objInfoPuntoDatoAdicional->setElementoId($objInfoElemento);
            }
            else if($strDependeDeEdificio == "N") //Elimina elemento id asociado al punto
            {
                $objInfoPuntoDatoAdicional->setElementoId(null);
            }

            $objInfoPuntoDatoAdicional->setDependeDeEdificio($strDependeDeEdificio);
            $objInfoPuntoDatoAdicional->setFeUltMod(new \DateTime('now'));
            $objInfoPuntoDatoAdicional->setUsrUltMod($strUserSession);
            $emComercial->persist($objInfoPuntoDatoAdicional);

            //Verifica Factibilidad.
            $arrayParamFactibilidad = array("emComercial"        => $emComercial,
                                            "emInfraestructura"  => $emInfraestructura,
                                            "intCodEmpresa"      => $strCodEmpresa,
                                            "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                            "intIdServicio"      => $intIdServicio,
                                            "strUsuarioCreacion" => $strUserSession,
                                            "strClienteIp"       => $strIpSession);

            $arrayResponseFactibilidad = $serviceInfoServicio->verificarFactibilidadServicio($arrayParamFactibilidad);

            if($arrayResponseFactibilidad["status"] != "OK")
            {
                throw new \Exception($arrayResponseFactibilidad["mensaje"]);
            }
            
            //Asigna nueva factibilidad
            $arrayNuevaFactibilidad = array("intElementoId"                  => $arrayResponseFactibilidad["intElementoId"],
                                            "intInterfaceElementoId"         => $arrayResponseFactibilidad["intInterfaceElementoId"],
                                            "intElementoContenedorId"        => $arrayResponseFactibilidad["intElementoContenedorId"],
                                            "intElementoConectorId"          => $arrayResponseFactibilidad["intElementoConectorId"],
                                            "intInterfaceElementoConectorId" => $arrayResponseFactibilidad["intInterfaceElementoConectorId"],
                                            "strObservacionFactibilidad"     => $arrayResponseFactibilidad["strObservacionFactibilidad"]);
            
            $arrayParamsAsignaFactibilidad = $arrayParamFactibilidad;
            $arrayParamsAsignaFactibilidad["arrayNuevaFactibilidad"] = $arrayNuevaFactibilidad;
            $arrayParamsAsignaFactibilidad["intIdPersonaEmpresaRol"] = $intIdPersonaEmpresaRol;
            
            $arrayResponseAsignacion = $serviceInfoServicio->asignaNuevaFactibilidadServicio($arrayParamsAsignaFactibilidad);

            if($arrayResponseAsignacion["status"] != "OK")
            {
                throw new \Exception($arrayResponseAsignacion["mensaje"]);
            }

            $strMensaje = "[SE ACTUALIZAN DATOS DE EDIFICACION]<br>";
            $strMensaje .= $arrayNuevaFactibilidad["strObservacionFactibilidad"];

            $emComercial->flush();
            $emComercial->getConnection()->commit();
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
        }
        catch( \Exception $ex )
        {
            $boolStatus = false;
            $strMensaje = $ex->getMessage();

            $serviceUtil->insertError('Telcos+',
                                      'ConsultaDatosEdificioController.ajaxProcesaNuevaFactibilidadAction',
                                      $strMensaje,
                                      $strUserSession,
                                      $strIpSession
                                     );

            $emComercial->getConnection()->rollback();
            $emInfraestructura->getConnection()->rollback();
        }

        $objResultado = json_encode(array ('status'  => $boolStatus,
                                            'message' => $strMensaje));
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
    
}
