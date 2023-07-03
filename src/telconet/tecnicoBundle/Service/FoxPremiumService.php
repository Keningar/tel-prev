<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoFormaContacto;
class FoxPremiumService
{

    //Se definen las constantes de la clase.
    const STR_VALOR_SPC            = 'strValorSpc';
    const STR_ESTADO_SPC           = 'strEstadoSpc';
    const EM_COMERCIAL             = 'telconet';
    const DOCTRINE                 = 'doctrine';
    const INFO_SERVICIO_REPOSITORY = 'schemaBundle:InfoServicio';
    const INFO_PERSONA_REPOSITORY  = 'schemaBundle:InfoPersona';
    const ADMI_PRODUCTO            = 'schemaBundle:AdmiProducto';
    const ESTADO_ACTIVO            = 'Activo';
    const ESTADO_INACTIVO          = 'Inactivo';
    const ESTADO_ELIMINADO         = 'Eliminado';
    const ESTADO_CANCEL            = 'Cancel';
    const ESTADO_INCORTE           = 'In-Corte';
    const OK                       = 'OK';
    const NOW                      = 'now';
    const INT_ID_SERVICIO          = 'intIdServicio';
    const STR_USR_CREACION         = 'strUsrCreacion';
    const STR_EMPRESA_COD          = 'strEmpresaCod';
    const STR_CLIENT_IP            = 'strClientIp';


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emgen;
    /**
     * @var \telconet\schemaBundle\Service\ValidatorService
     */
    private $validator;


    private $serviceEnvioPlantilla;
    private $serviceSoporte;
    private $serviceCrypt;
    private $strSmsNombreTecnicoFoxPremium;
    private $strSmsNombreTecnicoParamount;
    private $strSmsNombreTecnicoNoggin;
    private $serviceTecnico;
    private $serviceUtilidades;
    private $objContainer;
    private $serviceUtil;
    private $serviceAuthorizationFox;
    private $urlMsSecurity;
    private $msTokenSecurity;
    //Repository
    private $emComercial;
    private $emGeneral;
    private $emComunicacion;

    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;

    /**
     * Método que fija las dependencias necesarias para el service Fox
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 18-06-2018
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        //Container
        $this->objContainer                   = $objContainer;
        //Entity Manager
        $this->emcom                          = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan                        = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emgen                          = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        //Repository
        $this->emComercial                    = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral                      = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->emComunicacion                 = $objContainer->get('doctrine')->getManager('telconet_comunicacion');

        //Services
        $this->serviceEnvioPlantilla          = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceSoporte                 = $objContainer->get('soporte.SoporteService');
        $this->serviceCrypt                   = $objContainer->get('seguridad.Crypt');
        $this->serviceTecnico                 = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceUtilidades              = $objContainer->get('administracion.Utilidades');
        $this->validator                      = $objContainer->get('schema.Validator');
        $this->serviceAuthorizationFox        = $objContainer->get('tecnico.AuthorizationFox');
        //Parámetros Fox
        $this->strSmsNombreTecnicoFoxPremium  = $objContainer->getParameter('fox.producto.nombre_tecnico');

        $this->serviceUtil                    = $objContainer->get('schema.Util');
        //Parámetros Paramount
        $this->strSmsNombreTecnicoParamount             = $objContainer->getParameter('paramount.producto.nombre_tecnico');
        //Parámetros Noggin
        $this->strSmsNombreTecnicoNoggin                = $objContainer->getParameter('noggin.producto.nombre_tecnico');

        $this->restClient                     = $objContainer->get('schema.RestClient');
        // Parametros productos que no generan credenciales
        $this->urlMsSecurity                  = $objContainer->getParameter('ws_ms_generar_token_acceso');
        $this->msTokenSecurity                = $objContainer->getParameter('ms_token_security');
    }

    /**
     * Función que restablece la contraseña en base a un servicio.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 18-06-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * Se elimina el parámetro que llama a generaContraseniaFox
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * Se agrega el método (determinarProducto) para determinar que producto desea restablecer contraseña
     * se modifican valiables de contraseña y password del producto a restablecer
     * @since 15-09-2020
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3
     * Se modifica Metodo determinarProducto para validar los productos Paramount y Noggin
     * Se envia parametro idServicio y NombreTecnico para validar metodos Notifica sms y correo
     * @since 07-12-2020
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4
     * Se modifica Metodo para que permita restablecer las contraseña cuando el servicio ECDF está cancelado
     * @since 09-08-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.5
     * Se modifica Metodo para que permita restablecer las contraseña cuando el producto sea HBO-MAX y E-LEARN
     * @since 16-08-2022
     */
    public function restablecerContrasenia($arrayParametros)
    {
        $strMensaje = self::OK;
        $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $intIdServicio       = $arrayParametros[self::INT_ID_SERVICIO];
            $arrayProducto       = $this->determinarProducto(array('strNombreTecnico'=>$arrayParametros['strNombreProducto']));
            if ($arrayProducto['Status'] != 'OK')
            {
                $strMensaje      = $arrayProducto['Mensaje'];
                throw new \Exception($strMensaje);
            }
            $objInfoServicio     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                               ->findOneById($intIdServicio);
            //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA FLUJO DE CANCELACIÓN
            $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PERMITIR_RESTABLECER_PASS',//nombre parametro cab
                                                    'TECNICO', //modulo cab
                                                    'OBTENER_PROD_TV',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    $arrayParametros[self::STR_EMPRESA_COD]); //empresa
            foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
            {
            $arrayProdPermitido[]   =   $arrayNombreTecnico['valor1'];
            }
            if(is_object($objInfoServicio) && $objInfoServicio->getEstado() == self::ESTADO_CANCEL && 
               in_array($arrayProducto['strNombreTecnico'],$arrayProdPermitido))
            {
                $arrayParamsGetSsidXIdServicio      = array(
                                                            "intIdServicio"                 => $intIdServicio,
                                                            "strDescripcionCaract"          => $arrayProducto['strSsid'],
                                                            "strEstadoSpcEstaParametrizado" => "SI");
                $arrayRespuestaGetSsidXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetSsidXIdServicio);
                if($arrayRespuestaGetSsidXIdServicio['status'] !== "OK")
                {
                    throw new \Exception ("No se pudo obtener el SuscriberId del servicio");
                }
                $arrayRegistrosGetSsidXIdServicio = $arrayRespuestaGetSsidXIdServicio["arrayRegistros"];
                $intIdSpcUsuario = $arrayRegistrosGetSsidXIdServicio[0]["intIdSpc"];

                $arrayParametrosAuthorization   = array(
                                                            'country_code'      =>'EC',
                                                            'intIdSpcSuscriber' =>$intIdSpcUsuario,
                                                            'strSsid'           => $arrayProducto['strSsid']
                                                       );
                $arrayRespuesta  =   $this->serviceAuthorizationFox->autorizarServicio($arrayParametrosAuthorization);
                if ($arrayRespuesta['strCodigoSalida'] != 'OK')
                {
                    throw new \Exception($arrayRespuesta['strMensajeSalida']);
                }
            }
            else if(is_object($objInfoServicio) && $objInfoServicio->getEstado() != self::ESTADO_ACTIVO)
            {
                throw new \Exception("No es posible reiniciar la contraseña del usuario debido a que el"
                            . " servicio no se encuentra en estado Activo.");
            }

            $intProdCaractContraseniaId    = null;

            $arrayParamsGetPasswordXIdServicio      = array(
                                                        "intIdServicio"                 => $intIdServicio,
                                                        "strDescripcionCaract"          => $arrayProducto['strPass'],
                                                        "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetPasswordXIdServicio   = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                ->obtieneInfoSpcProductosTv($arrayParamsGetPasswordXIdServicio);
            if($arrayRespuestaGetPasswordXIdServicio['status'] !== "OK")
            {
                throw new \Exception ("No se pudo obtener la contrasenia del servicio");
            }
            $arrayRegistrosGetPasswordXIdServicio = $arrayRespuestaGetPasswordXIdServicio["arrayRegistros"];
            if (!isset($arrayRegistrosGetPasswordXIdServicio[0]) || empty($arrayRegistrosGetPasswordXIdServicio[0])) 
            {
                throw new \Exception ("El cliente aún no ha creado una contraseña para el servicio");
            }
            $intIdSpcPassword = $arrayRegistrosGetPasswordXIdServicio[0]["intIdSpc"];
            $objServProdCaracContrasenia = $this->emcom->getRepository("schemaBundle:InfoServicioProdCaract")->find($intIdSpcPassword);

            $arrayParamsGetUsuarioXIdServicio      = array(
                            "intIdServicio"                 => $intIdServicio,
                            "strDescripcionCaract"          => $arrayProducto['strUser'],
                            "strEstadoSpcEstaParametrizado" => "SI");

            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                      'COMERCIAL', //modulo cab
                      'OBTENER_NOMBRE_TECNICO',//proceso cab
                      'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                      $arrayProducto['strNombreTecnico'],'','','','', $arrayParametros["strEmpresaCod"]);
            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                $arrayParamsGetUsuarioXIdServicio["strDescripcionCaract"] = $arrayProducto['strCorreo'];
            }
            $arrayRespuestaGetUsuarioXIdServicio   = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                ->obtieneInfoSpcProductosTv($arrayParamsGetUsuarioXIdServicio);
            if($arrayRespuestaGetUsuarioXIdServicio['status'] !== "OK")
            {
                throw new \Exception ("No se pudo obtener el usuario del servicio");
            }

            $arrayRegistrosGetUsuarioXIdServicio = $arrayRespuestaGetUsuarioXIdServicio["arrayRegistros"];
            $intIdSpcUsuario = $arrayRegistrosGetUsuarioXIdServicio[0]["intIdSpc"];
            $objServProdCaracUsuario = $this->emcom->getRepository("schemaBundle:InfoServicioProdCaract")->find($intIdSpcUsuario);

            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                // OBTENER TOKEN PARA GENERAR URL
                $strUrlRestablecerContra = $this->objContainer->getParameter('url_restablecer_password_security');
                $strUrlToken = $strUrlRestablecerContra.$arrayParametros["token"];
                if(!isset($arrayParametros["token"]) || empty($arrayParametros["token"]))
                {
                    $arrayCaracteristicaCorreo[]  = array('caracteristica' => 'CORREO ELECTRONICO', 
                                                          'valor' => $objServProdCaracUsuario->getValor());

                    $arrayParametrosUrlToken      = array('strUsrCreacion'        => $arrayParametros["strUsrCreacion"],
                                                          'arrayCaracteristicas'   => $arrayCaracteristicaCorreo,
                                                          'strNombreTecnico'       => $arrayProducto['strNombreTecnico'],
                                                          'strCrearPassword'       => "NO",
                                                          'strEmpresaCod'          => $arrayParametros["strEmpresaCod"]);
                    $arrayUrlToken = $this->obtenerUrlActivarServicio($arrayParametrosUrlToken);
                    if($arrayUrlToken["status"] !== "OK")
                    {
                        throw new \Exception ("No existe un token para poder restablecer la contraseña. ".$arrayUrlToken["mensaje"]);
                    }
                    $strUrlToken = $arrayUrlToken["url"];
                }
                $strCorreoProd = $objServProdCaracUsuario->getValor();
                
                if (!isset($strUrlRestablecerContra) || empty($strUrlRestablecerContra)) 
                {
                    throw new \Exception ("No existe url_restablecer_password_security en el parameters.yml");
                }
                $strCliente = trim($objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getInformacionPersona());
                $arrayParametrosCorreo = array("usuario"  => $strCorreoProd, "cliente" => $strCliente, 
                                               "url" => $strUrlToken);
                //validación para permitir el envío de SMS
                $boolEnviarSms = false;
                $strObservacionHist = "Se enviaron las indicaciones para restablecer la contraseña al correo: <b>".$strCorreoProd."</b>";
            }
            else
            {
                $strNuevaContrasenia         = $this->generaContraseniaFox();
                $boolEnviarSms = true;
                //Actualizo la característica correspondiente a la contraseña
                if (is_object($objServProdCaracContrasenia))
                {
                    $objServProdCaracContrasenia->setEstado(self::ESTADO_INACTIVO);
                    $objServProdCaracContrasenia->setFeUltMod(new \DateTime(self::NOW));
                    $objServProdCaracContrasenia->setUsrUltMod($arrayParametros[self::STR_USR_CREACION]);
    
                    $this->emcom->persist($objServProdCaracContrasenia);
                    $this->emcom->flush();
                    $intProdCaractContraseniaId    = $objServProdCaracContrasenia->getProductoCaracterisiticaId();
                }
                else
                {
                    //Si no tenemos la característica la buscamos
                    $objAdmiCaracteristica      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("estado"                    => self::ESTADO_ACTIVO,
                                                                        "descripcionCaracteristica" => $arrayProducto['strPass']));
                    $intProdCaractContraseniaId = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                      ->findOneBy(array("productoId"       => $objInfoServicio->getProductoId(),
                                                                        "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                        "estado"           => self::ESTADO_ACTIVO))
                                                      ->getId();
                }
    
                //Se crea la nueva característica con la nueva contraseña
                $objInfoServicioProdCaract = new \telconet\schemaBundle\Entity\InfoServicioProdCaract();
                $objInfoServicioProdCaract->setServicioId($intIdServicio);
                $objInfoServicioProdCaract->setValor($this->serviceCrypt->encriptar($strNuevaContrasenia));
                $objInfoServicioProdCaract->setEstado(self::ESTADO_ACTIVO);
                $objInfoServicioProdCaract->setProductoCaracterisiticaId($intProdCaractContraseniaId);
                $objInfoServicioProdCaract->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
                $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                $this->emcom->persist($objInfoServicioProdCaract);
                $this->emcom->flush();

                $arrayParametrosCorreo = array("contrasenia" => $strNuevaContrasenia,
                "usuario"     => $objServProdCaracUsuario->getValor());
                //Se reemplaza la contraseña del mensaje del parámetro
                $strMensajeSMS = str_replace("{{USUARIO}}",
                                              $objServProdCaracUsuario->getValor(),
                                              str_replace("{{CONTRASENIA}}",
                                                          $strNuevaContrasenia,
                                                          $arrayProducto['strSmsRestContra']
                                                         )
                                            );
                $strObservacionHist = "Se realiza el reinicio de contraseña";
            }
            //Se actualiza el Historial del Servicio para llevar el control del reinicio de contraseña
            $objInfoServicioHistorial = new \telconet\schemaBundle\Entity\InfoServicioHistorial();
            $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
            $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
            $objInfoServicioHistorial->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
            $objInfoServicioHistorial->setObservacion($strObservacionHist);
            $objInfoServicioHistorial->setServicioId($objInfoServicio);
            $objInfoServicioHistorial->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
            $this->emcom->persist($objInfoServicioHistorial);
            $this->emcom->flush();

            //Notifico al cliente por Correo y SMS
            $this->notificaCorreoServicioFox(
                    array("strDescripcionAsunto"   => $arrayProducto['strAsuntoRestContra'],
                          "strCodigoPlantilla"     => $arrayProducto['strCodPlantRest'],
                          self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                          "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                          "intIdServicio"          => $objInfoServicio->getId(),
                          "strNombreTecnico"       => $arrayProducto['strNombreTecnico'],
                          "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                          "arrayParametros"        => $arrayParametrosCorreo,
                          "strCorreoDest"          => $strCorreoProd,
                         )
                   );
            if ($boolEnviarSms) 
            {
                $this->notificaSMSServicioFox(
                    array("strMensaje"             => $strMensajeSMS,
                          "strTipoEvento"          => "enviar_infobip",
                          self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                          "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                          "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                          "strNombreTecnico"       => $arrayProducto['strNombreTecnico']
                        )
                    );
            }
            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = $ex->getMessage();
        }
        return $strMensaje;
    }

    /**
     * Función que obtiene todas las características de la tabla InfoServicioProdCarac y las devuelve como un array.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 18-06-2018
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * Se agrega parametro $arrayParametros [strEstado].
     * @since 04-07-2018
     */
    public function obtieneArrayCaracteristicas($arrayParametros)
    {
        $intIdServicio           = $arrayParametros[self::INT_ID_SERVICIO];        
        $strEstado               = ( isset($arrayParametros['strEstado'])
                                   && !empty($arrayParametros['strEstado']) )
                                   ? $arrayParametros['strEstado'] : self::ESTADO_ACTIVO;        
        $arrayOrderBy           = ( isset($arrayParametros['arrayOrderBy'])
                                    && !empty($arrayParametros['arrayOrderBy']) )
                                    ? $arrayParametros['arrayOrderBy'] : array();
        $arrayListServProdCaract = $this->emcom->getRepository("schemaBundle:InfoServicioProdCaract")
                                       ->findBy(array("estado"     => $strEstado,
                                                      "servicioId" => $intIdServicio), $arrayOrderBy);
        $arrayCaracteristicas   = array();
        foreach($arrayListServProdCaract as $objProdSevCarac)
        {
            $objAdmiProductoCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                  ->find($objProdSevCarac->getProductoCaracterisiticaId());
            $objAdmiCaracteristica         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->find($objAdmiProductoCaracteristica->getCaracteristicaId());
            $arrayCaracteristicas[$objAdmiCaracteristica->getDescripcionCaracteristica()] = $objProdSevCarac;
        }
        return $arrayCaracteristicas;
    }

    /**
     * Función que genera la contraseña por defecto de un usuario FoxPremium
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 26-12-2018
     * Se modifica la función a llamar para generar un string aleatorio
     */
    public function generaContraseniaFox()
    {
        return $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)->creaAleatorio();
    }

    /**
     * Función que genera el usuario FoxPremium
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1  
     * @since 15-09-2020
     *  - Se modifica la entrada de datos del Metodo, se adiciona 2 parametros para determinar el producto que genera la contraseña:
     *                                                          -strCaracUsuario-> caracteristica del producto.
     *                                                          -strNombreTecnico-> nombre técnico del producto.
     *  - Se Modifica los parametros de envío para generaUsuario, dependiendo del producto (Foxpremium, Paramount o Noggin).
     */
    public function generaUsuarioFox($arrayParametros)
    {
        $intIdPersona       = $arrayParametros['intIdPersona'];
        $strCaracUsuario    = $arrayParametros['strCaracUsuario'];
        $strNombreTecnico   = $arrayParametros['strNombreTecnico'];
        return $this->emcom->getRepository(self::INFO_PERSONA_REPOSITORY)
                    ->generaUsuario(array("intIdPersona"      => $intIdPersona,
                                          "strPrefijoEmpresa" => "FP",
                                          "strInfoTabla"      => "INFO_SERVICIO_PROD_CARACT",
                                          "strCaracUsuario"   => $strCaracUsuario,
                                          "strNombreTecnico"  => $strNombreTecnico));
    }

    /**
     * Función que notifica al cliente según la plantilla deseada por el servicio FOX PREMIUM
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 09-05-2019 - Se Genera Historial con la información de los correos enviados en las Notificaciones de los Clientes.
     *                           Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.
     *    
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 09-09-2020 - Se Modifica mensajes del envio de la información de Netlifeplay, PARAMOUNT Y NOGGIN.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 07-12-2020 - Se parametriza que tome valores de la caracteristica del producto y no de la formaContactoPunto
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4 09-08-2021 - Se ajusta la validación al guardar el historial y se agrega parametro para validar con el producto ECDF
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.5 30-07-2021 - Se mejora la obtencion de los parametros para que traigas todos los productos que acceden a correo.
     * 
     */
    public function notificaCorreoServicioFox($arrayParametros)
    {
        $strCorreoDest  =   (isset($arrayParametros['strCorreoDest'])?$arrayParametros['strCorreoDest']:'');
        $objInfoPersonaEmpresaRol  = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                          ->find($arrayParametros["intPersonaEmpresaRolId"]);
        $arrayParamValidaCorreoPersona = array();
        $arrayParamValidaCorreos = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('VALIDA_NOTIFICACION_CORREO',//nombre parametro cab
                                                            'COMERCIAL', //modulo cab
                                                            'VALIDA_NOTIFICACION',//proceso cab
                                                            'VALIDA_CORREO_PERSONA_PUNTO_CORREO', //descripcion det
                                                            '','','','','',
                                                            '18'); //empresa
        foreach($arrayParamValidaCorreos as $objParamValidaCorreo)
        {
            $arrayParamValidaCorreoPersona[] = $objParamValidaCorreo['valor1'];
        }
        $arrayDestinatario = array();
        if(($arrayParametros['strNombreTecnico'] == $this->strSmsNombreTecnicoParamount ||
            $arrayParametros['strNombreTecnico'] == $this->strSmsNombreTecnicoNoggin) && $strCorreoDest == '')
        {
            //Se consulta los correos de un producto
            $arrayParametrosCaract  = array( 'strNombreProducto' => $arrayParametros['strNombreTecnico'],
                                             'intIdServicio'     => $arrayParametros['intIdServicio']);
            $arrayCaracteristicas   = $this->obtenerCaractCorreo($arrayParametrosCaract);

            foreach($arrayCaracteristicas['registros'] as $arrayDatos)
            {
                $arrayCaratCorreo[] = $arrayDatos;
            }
            foreach($arrayCaratCorreo as $arrayValorCorreo)
            {
                $arrayDestinatarios[] = $arrayValorCorreo['valor'];
            }
        }
        //Se valida que cuando sea ECDF se envie la notificación a la FormaContactoPersona, FormaContactoPunto y CaracteristicaCorreo.
        else if (in_array($arrayParametros['strNombreTecnico'],$arrayParamValidaCorreoPersona))
        {
            $arrayDestinatarios        = $this->emcom->getRepository("schemaBundle:AdmiFormaContacto")
                                              ->obtieneFormaContactoxParametros(
                                                array("intPuntoId"                  => $arrayParametros["intPuntoId"],
                                                      "intPersonaId"                => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                      "strDescripcionFormaContacto" => "Correo Electronico"));
            if($strCorreoDest != '')
            {
                $arrayDestinatarios[] = $strCorreoDest;
            }
            else
            {
                //Se consulta los correos de un producto cuando se realiza el restablecimiento de la clave
                $arrayParametrosCaract  = array( 'strNombreProducto' => $arrayParametros['strNombreTecnico'],
                                                'intIdServicio'     => $arrayParametros['intIdServicio']);
                $arrayCaracteristicas   = $this->obtenerCaractCorreo($arrayParametrosCaract);
                if(is_array($arrayCaracteristicas) && !empty($arrayCaracteristicas))
                {
                    foreach($arrayCaracteristicas['registros'] as  $arrayDatos)
                    {
                        $arrayDestinatarios[] = $arrayDatos['valor'];
                    }
                }
            }
            
        }
        else if($strCorreoDest != '')
        {
            $arrayDestinatarios[] = $strCorreoDest;
        }
        if ((isset($arrayParametros["strEsPlan"]) && $arrayParametros["strEsPlan"] == 'SI') || empty($arrayDestinatarios))
        {
            $arrayDestinatarios        = $this->emcom->getRepository("schemaBundle:AdmiFormaContacto")
                                              ->obtieneFormaContactoxParametros(
                                                array("intPuntoId"                  => $arrayParametros["intPuntoId"],
                                                      "intPersonaId"                => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                      "strDescripcionFormaContacto" => "Correo Electronico"));
            
        }

        $strCliente = trim($objInfoPersonaEmpresaRol->getPersonaId()->getInformacionPersona());
        $arrayParametros["arrayParametros"]["cliente"] = $strCliente;
        //Se realiza la notificación por plantilla y alias.
        $this->serviceEnvioPlantilla->generarEnvioPlantilla($arrayParametros["strDescripcionAsunto"],
                                                            $arrayDestinatarios,
                                                            $arrayParametros["strCodigoPlantilla"], 
                                                            $arrayParametros["arrayParametros"],
                                                            $arrayParametros[self::STR_EMPRESA_COD],
                                                            '',
                                                            '',
                                                            null,
                                                            true,
                                                            null);
        
        if (isset($arrayParametros['arrayParamHistorial']) && !empty($arrayParametros['arrayParamHistorial']))
        {
            $arrayParamHistorial = $arrayParametros['arrayParamHistorial'];
            $strDestinatarios    = implode(", ", $arrayDestinatarios);
            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                  'COMERCIAL', //modulo cab
                  'OBTENER_NOMBRE_TECNICO',//proceso cab
                  'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                  $arrayParametros['strNombreTecnico'],'','','','', $arrayParametros["strEmpresaCod"]);
            if(isset($arrayParamHistorial['strTipoAccion']) && !empty($arrayParamHistorial['strTipoAccion']) 
                && substr($arrayParamHistorial['strTipoAccion'], 0, 7) == 'Activar')
            {
                // CAMBIAR OBSERVACION SI EL PRODUCTO NO GENERA CREDENCIALES
                if(is_array($objProdGenCred) && !empty($objProdGenCred))
                {
                    $arrayParamHistorial['strObservacion'] = 'Se Confirmó el Servicio. <br>'
                                                         . 'El Usuario y el enlace para generar la contraseña de '.
                                                         $arrayParamHistorial['strMensaje']
                                                         .' fue enviado al correo: <b> '
                                                         . $strDestinatarios.'</b>';
                }
                else
                {
                    $arrayParamHistorial['strObservacion'] = 'Se Confirmó el Servicio. <br>'
                    . 'El Usuario y Contraseña de '. $arrayParamHistorial['strMensaje']
                    .' fue enviado al correo: <br> '
                    . $strDestinatarios;
                }
            }
            if( substr($arrayParamHistorial['strTipoAccion'], 0, 7) == 'Reenvio')
            {
                $arrayParamHistorial['strObservacion'] = 'El Usuario y Contraseña de '. $arrayParamHistorial['strMensaje']
                                                        .' fue reenviado al correo: '
                                                        . $strDestinatarios;
                if(is_array($objProdGenCred) && !empty($objProdGenCred))
                {
                    $arrayParamHistorial['strObservacion'] = 'El Usuario y el enlace para generar la contraseña de '.
                    $arrayParamHistorial['strMensaje'].' fue enviado al correo: <b> '. $strDestinatarios.'</b>';
                }
            }
            $this->crearHistorialServicioFox($arrayParamHistorial);
        }
    }

    /**
     * Función que notifica al cliente según el mensaje para el servicio FOX PREMIUM
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 09-05-2019 - Se Genera Historial con la información de los teléfonos movil enviados en las Notificaciones de los Clientes. 
     *                           Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.    
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2      - Se Modifica mensajes del envio de la información de Netlifeplay, PARAMOUNT Y NOGGIN.
     *                   - Se modifica la funcion stripos agregando '0' por error en el envio de sms
     * @since 09-09-2020
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3      - Se Modifica para que cuando se active o desactive  el estado, envie o no, mensajes de texto con la información 
     *                     PARAMOUNT Y NOGGIN.
     * @since 07-12-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 01-08-2021 Se modifica la consulta para verificar si está permitido el envío de SMS por nombre técnico del producto
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.5  09-08-2021  - Se Modifica el guardado del historial.
     * 
     */
    public function notificaSMSServicioFox($arrayParametros)
    {
        $arrayEnvioSMSPorProducto   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne( 'ENVIO_SMS_POR_PRODUCTO',
                                                                '',
                                                                '',
                                                                '',
                                                                'NOMBRE_TECNICO',
                                                                $arrayParametros['strNombreTecnico'],
                                                                '',
                                                                '',
                                                                '',
                                                                $arrayParametros['strEmpresaCod']);
        if((isset($arrayEnvioSMSPorProducto) && !empty($arrayEnvioSMSPorProducto) && $arrayEnvioSMSPorProducto['valor3'] === "SI")
            || $arrayParametros['strNombreTecnico'] == $this->strSmsNombreTecnicoFoxPremium)
        {
            $objInfoPersonaEmpresaRol  = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                              ->find($arrayParametros["intPersonaEmpresaRolId"]);
            $arrayDestinatarios        = $this->emcom->getRepository("schemaBundle:AdmiFormaContacto")
                                              ->obtieneFormaContactoxParametros(
                                                array("intPuntoId"                  => $arrayParametros["intPuntoId"],
                                                      "intPersonaId"                => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                      "strDescripcionFormaContacto" => "%Movil%"));
            $strNumerosDestino = "";
            foreach($arrayDestinatarios as $strDestino)
            {
                if (stripos($strDestino, '0') == 0)
                {
                    $strDestino = preg_replace('/0/', '593', $strDestino, 1);
                }
                $strNumerosDestino .= $strDestino . "-";
            }
            //Se realiza la notificación por SMS.
            $arrayParametros["strNumeros"] = $strNumerosDestino;
            //Valida si la plantilla del SMS se encuentra vacia.
            if($arrayParametros["strMensaje"] == "")
            {
                $strMensaje = "No se encontraron plantillas para el envío de sms";
                $this->serviceUtil->insertError( 'Telcos+', 
                                 'FoxPremiumService.notificaSMSServicioFox', 
                                 $strMensaje, 
                                 'telcos', 
                                 '127.0.0.1' );
                throw new \Exception("No se encontraron plantillas para el envío de sms");
            }
            else
            {
                $this->serviceSoporte->enviarSMS($arrayParametros);
        
                if (isset($arrayParametros['arrayParamHistorial']) && !empty($arrayParametros['arrayParamHistorial']))
                {
                    $arrayParamHistorial = $arrayParametros['arrayParamHistorial'];
                    $strDestinatarios    = implode(", ", $arrayDestinatarios);
                    // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
                    $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                          'COMERCIAL', //modulo cab
                          'OBTENER_NOMBRE_TECNICO',//proceso cab
                          'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                          $arrayParametros['strNombreTecnico'],'','','','', $arrayParametros["strEmpresaCod"]);
                    if(isset($arrayParamHistorial['strTipoAccion']) && !empty($arrayParamHistorial['strTipoAccion']) 
                        && substr($arrayParamHistorial['strTipoAccion'], 0, 7) == 'Activar')
                    {
                        // CAMBIAR OBSERVACION SI EL PRODUCTO NO GENERA CREDENCIALES
                        if(is_array($objProdGenCred) && !empty($objProdGenCred))
                        {
                            $arrayParamHistorial['strObservacion'] = 'Se Confirmó el Servicio. <br>'
                                                                . 'El Usuario y el enlace para generar la contraseña de '.
                                                                $arrayParamHistorial['strMensaje']
                                                                .' fue enviado al teléfono: <br> '
                                                                . $strDestinatarios;
                        }
                        else
                        {
                            $arrayParamHistorial['strObservacion'] = 'Se Confirmó el Servicio. <br>'
                            . 'El Usuario y Contraseña de '. $arrayParamHistorial['strMensaje']
                            .' fue enviado al teléfono: <br> '
                            . $strDestinatarios;
                        }
                    }
                    if(substr($arrayParamHistorial['strTipoAccion'], 0, 7) == 'Reenvio')
                    {
                        $arrayParamHistorial['strObservacion'] = 'El Usuario y Contraseña '. $arrayParamHistorial['strMensaje']
                                                                 .' fue reenviado al teléfono: '
                                                                 . $strDestinatarios;
                        if(is_array($objProdGenCred) && !empty($objProdGenCred))
                        {
                            $arrayParamHistorial['strObservacion'] = 'El Usuario y el enlace para generar la contraseña de '.
                            $arrayParamHistorial['strMensaje'].' fue enviado al teléfono: <b> '. $strDestinatarios.'</b>';
                        }
                    }
                    $this->crearHistorialServicioFox($arrayParamHistorial);
                }
            }
        }
        
    }

    /**
     * Función que realiza el flujo necesario al activar el servicio Fox Premium.
     * Inicialmente sólo se notifica al cliente.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 22-06-2018
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @since 04-05-2018-  Se debe verificar por SSID_FOX si corresponde a un ID_SERVICIO Cancel y pasar la caracteristica MIGRADO_FOX a 'S'
     *                     por tratarse de una Recontratacion o Reingreso del cliente que ha reutilizado la data USUARIO_FOX, y el SSID_FOX  
     *                     para el nuevo servicio FOX  Contratado  
     *                     Se crea Historial en el Servicio por Recontratación del Servicio FOX.        
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 15-12-2018
     * Se agrega el consumo del método clearCache cuando es un reingreso.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-05-2019 - Se habilita Flujo de Activación Automática de FOX Premium  
     *                           Se Genera Historial con la información de los correos y teléfonos enviados en las Notificaciones de los Clientes.
     *                           Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.    
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 15-09-2020
     * Se modifica Flujo de Activación Automática de FOX Premium, Paramount, Noggin.
     * Se Genera Historial con la información de los correos y teléfonos enviados en las Notificaciones de los Clientes.
     * Se moodifica mensaje de información dependiendo del producto.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4
     * Se modifica Metodo determinarProducto para validar los productos Paramount y Noggin
     * Se hace el envio de parametros inIdServicio y Nombre Tecnico para validar la notificacion por correo y sms
     * @since 07-12-2020
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.5
     * Se modifica Parametro de envio al ClearCacheToolbox para acceso al ws de toolbox cuando es Paramount o Noggin
     * @since 28-06-2021
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.6
     * Se modifica que si es producto ECDF se guarde en la infoservProdCaract las fechas de activacion y de fin de suscripcion.
     * @since 28-09-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.7
     * Se agrega un validador para crear el historial del canal del futbol
     * @since 07-12-2021
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.8
     * Se modifica que si los productos no generan credenciales, entonces no se envía la notificación por correo o sms.
     * @since 08-08-2022
     */
    public function activarServicio($arrayParametros)
    {
        //Obtiene el servicio, y notifica con su respectiva plantilla.
        $intIdServicio      = $arrayParametros[self::INT_ID_SERVICIO];
        $objInfoServicio    = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)->findOneById($intIdServicio);
        $arrayServicio      = $this->obtieneArrayCaracteristicas($arrayParametros);
        $arrayProducto      = array('');
        $boolBanderaCrearHistorialECDF = false;
        if (isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']))
        {
            $arrayProducto  = $this->determinarProducto(array('intIdProducto'=>$arrayParametros['intIdProducto']));
        }
        else
        {
            $arrayProducto  = $this->determinarProducto(array('intIdServicio'=>$intIdServicio));
        }
        if ($arrayProducto['Status'] != 'OK')
        {
            $strMensaje = $arrayProducto['Mensaje'];
            throw new \Exception($strMensaje);
        }
        $objServProdCaracContrasenia = $arrayServicio[$arrayProducto['strPass']];
        $objServProdCaracUsuario     = $arrayServicio[$arrayProducto['strUser']];
        $objServProdCaracSsid        = $arrayServicio[$arrayProducto['strSsid']];

        $arrayParamHistorial         = array('strUsrCreacion'  => $arrayParametros[self::STR_USR_CREACION], 
                                             'strClientIp'     => $arrayParametros[self::STR_CLIENT_IP], 
                                             'objInfoServicio' => $objInfoServicio,
                                             'strTipoAccion'   => $arrayProducto['strAccionActivo'],
                                             'strMensaje'      => $arrayProducto['strMensaje']);
        
        //Obtengo el suscriber_id del servicio FOX, PARAMOUNT O NOGGIN
        if(!is_object($objServProdCaracSsid))
        {
            throw new \Exception("No se pudo obtener el SuscriberId para el servicio ".$arrayProducto['strMensaje']); 
        }
        $intIdServicioOrigenMigrado    = $objServProdCaracSsid->getValor();
        $objInfoServicioOrigenMigrado  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                              ->findOneById($intIdServicioOrigenMigrado);
         /* Si el SSID_XXX corresponde a un ID_SERVICIO Cancel, debemos pasar el valor de la caracteristica MIGRADO_XX  del servicio origen en 'S'
            ya que se trata de una Recontratacion o Reingreso del cliente y se ha reutilizado la data USUARIO_XXX, y el SSID_XXX para el nuevo
            servicio FOX PREMIUM, PARAMOUNT O NOGGIN contratado */
        if (self::ESTADO_CANCEL == $objInfoServicioOrigenMigrado->getEstado() && $intIdServicio!=$intIdServicioOrigenMigrado)
        {
            $arrayServicioOrigenMigrado  = $this->obtieneArrayCaracteristicas(array(self::INT_ID_SERVICIO => $intIdServicioOrigenMigrado,
                                                                                       'strEstado'           => self::ESTADO_ELIMINADO));
            $objServProdCaracMigrado     = $arrayServicioOrigenMigrado[$arrayProducto['strMigrar']];
            if (is_object($objServProdCaracMigrado) && $objServProdCaracMigrado->getValor()=='N')
            {
                $objServProdCaracMigrado->setValor('S');   
                $objServProdCaracMigrado->setFeUltMod(new \DateTime(self::NOW));
                $objServProdCaracMigrado->setUsrUltMod($arrayParametros[self::STR_USR_CREACION]);

                $this->emcom->persist($objServProdCaracMigrado);
                $this->emcom->flush();  
                //Se crea Historial en el Servicio nuevo contratado indicando que se reutilizo USUARIO_XXX, y el SSID_XXX en base a un servicio 
                //FOXPREMIUM, PARAMOUNT O NOGGIN "Cancel" existente en el cliente por ser recontratacion o Reingreso.
                $objInfoServicioHistorial = new \telconet\schemaBundle\Entity\InfoServicioHistorial();
                $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
                $objInfoServicioHistorial->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
                $objInfoServicioHistorial->setObservacion("Recontratación: El Cliente ya posee un Servicio ". $arrayProducto['strMensaje'] .
                                           " Cancelado, se procede a Activar el Servicio con la información del <b>LOGIN</b> y <b>SUSCRIBER_ID".
                                           " existente.");
                $objInfoServicioHistorial->setServicioId($objInfoServicio);
                $objInfoServicioHistorial->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
                $this->emcom->persist($objInfoServicioHistorial);
                $this->emcom->flush();
            }
        }
        // buscamos la fecha y hora de activación del servicio
        // validamos que se busque el historial del servicio con estado Pendiente
        if($arrayProducto['strNombreTecnico'] === "ECDF"
          && isset($arrayParametros['activarCorreoECDF']) 
          && !empty($arrayParametros['activarCorreoECDF'])
          && $arrayParametros['activarCorreoECDF'] === "SI")
        {
            $boolBanderaCrearHistorialECDF = true;
        }
        if (!$boolBanderaCrearHistorialECDF && $intIdServicio != $intIdServicioOrigenMigrado)
        {
            //Se limpia la caché del usuario por cambio de estado del servicio de Pendiente -> Activo Siempre y cuando sea reingreso o traslado.
            $arrayParametrosClear['strSubscriberId']   = $objServProdCaracSsid->getValor();
            $arrayParametrosClear['intIdServicio']     = $intIdServicio;
            $arrayParametrosClear['strEstado']         = $objInfoServicio->getEstado();
            $arrayParametrosClear['strCreaProcMasivo'] = 'S';
            $arrayParametrosClear['strUsrCreacion']    = $arrayProducto['strUserCreacion'];
            $arrayParametrosClear['strIpCreacion']     = "127.0.0.1";
            //Nombre de parametro para consumo de ws toolbox
            $arrayParametrosClear['strNombreParametro']= $arrayProducto['strNombreParametro'];

            $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)->clearCacheToolbox($arrayParametrosClear);
        }
        //si es producto con fecha minima de suscripción
        $arrayParamDetProdFechaSuscripcion  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PRODUCTO_FECHA_MINIMA_SUSCRIPCION',//nombre parametro cab
                                                                'TECNICO', //modulo cab
                                                                'FECHA_SUSCRIPCION',//proceso cab
                                                                'NOMBRE_TECNICO', //descripcion det
                                                                '','','','','',
                                                                $arrayParametros[self::STR_EMPRESA_COD]); //empresa
        //meses minimos de suscripcion
        $arrayParamDetFechaMinima           = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PRODUCTO_FECHA_MINIMA_SUSCRIPCION',//nombre parametro cab
                                                                'TECNICO', //modulo cab
                                                                'FECHA_SUSCRIPCION',//proceso cab
                                                                'MESES_MINIMOS', //descripcion det
                                                                $arrayProducto['strNombreTecnico'],//valor1
                                                                '','','','',
                                                                $arrayParametros[self::STR_EMPRESA_COD]); //empresa
        foreach($arrayParamDetProdFechaSuscripcion as $arrayProductos)
        {
            $arrayProdPermitido[]     = $arrayProductos['valor1'];
        }
        if(in_array($arrayProducto['strNombreTecnico'],$arrayProdPermitido) && !$boolBanderaCrearHistorialECDF)
        {
                $objInfoServicioHistorial    = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $intIdServicio,
                                                                          'accion'     => 'confirmarServicio',
                                                                          'estado'     => self::ESTADO_ACTIVO)
                                                                        );

                if(is_object($objInfoServicioHistorial) && !empty($arrayParamDetFechaMinima))
                {
                    $objFechaActivacion = $objInfoServicioHistorial->getFeCreacion();
                    //Calculo de la fecha minima donde se termina la suscripcion.
                    $strFechaMinima     = date("Y-m-d H:i:s",
                                                strtotime($objFechaActivacion->format('Y-m-d H:i:s')." +".
                                                $arrayParamDetFechaMinima['valor2']." month")
                                            );
                    //calculo fecha de activacion del prod
                    $strFechaActivacion =   date("Y-m-d H:i:s",strtotime($objFechaActivacion->format('Y-m-d H:i:s')));
                    //guadado de fecha minima y de activacion en la caracteristica
                    $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros["intIdProducto"]);
                    if(is_object($objProducto))
                    {
                        $this->serviceTecnico
                            ->ingresarServicioProductoCaracteristica($objInfoServicio,$objProducto, $arrayProducto['strFechaFin'],
                                                                      $strFechaMinima,$arrayParametros[self::STR_USR_CREACION]);
                        $this->serviceTecnico
                            ->ingresarServicioProductoCaracteristica($objInfoServicio,$objProducto, $arrayProducto['strFechaActivacion'],
                                                                      $strFechaActivacion,$arrayParametros[self::STR_USR_CREACION]);
                        if ($arrayProducto['strNombreTecnico'] === "ECDF")
                        {
                            $this->serviceTecnico
                            ->ingresarServicioProductoCaracteristica($objInfoServicio,$objProducto, "BAJA_ECDF",
                                                                      "NO",$arrayParametros[self::STR_USR_CREACION]);
                        }
                    }
                    else
                    {
                        throw new \Exception('Ocurrió un error al tratar de obtener el producto, Favor comunicar a Sistemas.');
                    }
                }
                else
                {
                    throw new \Exception('Ocurrió un error al tratar de obtener la fecha de Activación del servicio, Favor comunicar a Sistemas.');
                }
        }
        // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
        $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
              'COMERCIAL', //modulo cab
              'OBTENER_NOMBRE_TECNICO',//proceso cab
              'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
              $arrayProducto['strNombreTecnico'],'','','','', $arrayParametros["strEmpresaCod"]);

        $strCorreoProd  =   '';
        if(isset($arrayParametros['arrayCaracteristicas']))
        {
            foreach($arrayParametros['arrayCaracteristicas'] as $arrayCaract)
            {
                if($arrayCaract['caracteristica']=='CORREO ELECTRONICO')
                {
                    $strCorreoProd  =  $arrayCaract['valor'];
                }
            }
        }
        if(is_array($objProdGenCred) && !empty($objProdGenCred))
        {
            $arrayParametrosCorreo = array("usuario"  => $strCorreoProd, "cliente" => $arrayParametros["strCliente"], 
                                           "url" => $arrayParametros["strUrlProducto"]);
            //Se reemplaza la contraseña del mensaje del parámetro
            $strMensajeSMS = str_replace("{{CORREO}}", $strCorreoProd,  $arrayProducto['strSmsNuevo']);
        }
        else  
        {
            //Desencripto la contraseña
            $strContraseniaActual        = $this->serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
            $arrayParametrosCorreo = array("contrasenia" => $strContraseniaActual,
                                          "usuario"     => $objServProdCaracUsuario->getValor());
            //Se reemplaza la contraseña del mensaje del parámetro
            $strMensajeSMS = str_replace("{{USUARIO}}", $objServProdCaracUsuario->getValor(),
                             str_replace("{{CONTRASENIA}}", $strContraseniaActual,
                            $arrayProducto['strSmsNuevo'])
                            );
        }
        
        //Flujo para notificar por la caracteristica ingresada al crear el servicio.
        //Notifico al cliente por Correo y SMS
        $this->notificaCorreoServicioFox(
                    array("strDescripcionAsunto"   => $arrayProducto['strAsuntoNuevo'],
                          "strCodigoPlantilla"     => $arrayProducto['strCodPlantNuevo'],
                          self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                          "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                          "intIdServicio"          => $objInfoServicio->getId(),
                          "strCorreoDest"          => $strCorreoProd,
                          "strNombreTecnico"       => $arrayProducto['strNombreTecnico'],
                          "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                          "arrayParametros"        => $arrayParametrosCorreo,
                          "arrayParamHistorial"    => $arrayParamHistorial
                        )
        );
        $this->notificaSMSServicioFox(
                    array("strMensaje"             => $strMensajeSMS,
                          "strTipoEvento"          => "enviar_infobip",
                          self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                          "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                          "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                          "arrayParamHistorial"    => $arrayParamHistorial,
                          "strNombreTecnico"       => $arrayProducto['strNombreTecnico']
                         )
        );
    }

    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 15-12-2018
     *             
     * Función que realiza las validaciones y limpia la caché de un servicio FOX.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 13-05-2019 - Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 16-09-2020
     * - Se modifica mensajes donde indicaba Netlifeplay por Netlifeplay, Paramount o Noggin.
     * - Se agrega método determinarProducto para seleccionar caracteristicas del producto seleccionado.
     * - se modifica el ingreso de valores Ssid determinado por el producto.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3
     * Se modifica Metodo determinarProducto para validar los productos Paramount y Noggin
     * @since 07-12-2020
     * 
     *  @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4
     * Se agrega parametros para Paramount/Noggin para acceder al ws de toolbox
     * @since 28-06-2021
     */
    public function clearCacheToolbox($arrayParametros)
    {
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $arrayProducto = $this->determinarProducto(array("strNombreTecnico" => $arrayParametros['strNombreProducto']));
            if ($arrayProducto['Status'] == 'ERROR')
            {
                throw new \Exception($arrayProducto['Mensaje']);
            }
            $objInfoServicio                    = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                                       ->findOneById($arrayParametros[self::INT_ID_SERVICIO]);
            $arrayServicioFox                   = $this->obtieneArrayCaracteristicas($arrayParametros);
            if (!is_object($arrayServicioFox[$arrayProducto['strSsid']]))
            {
                throw new \Exception("No se han encontrado características de ".$arrayProducto['strMensaje']." asociadas al presente servicio.");
            }
            $arrayParametros['strSubscriberId'] = $arrayServicioFox[$arrayProducto['strSsid']]->getValor();

            if (empty($arrayServicioFox) || !$arrayParametros['strSubscriberId'])
            {
                throw new \Exception("No se ha encontrado el usuario de ".$arrayProducto['strMensaje']." asociado al presente servicio.");
            }
            $arrayParametros['strEstado']       = $objInfoServicio->getEstado();
            //Nombre de parametro para consumo de ws toolbox
            $arrayParametros['strNombreParametro']  = $arrayProducto['strNombreParametro'];
            //Se llama al repositorio
            $strRespuesta = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)->clearCacheToolbox($arrayParametros);
            if ($strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
            $strMensaje  = self::OK;
            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = "Error al conectarse con el servidor de ".$arrayProducto['strMensaje'].": " . $ex->getMessage();
        }
        return $strMensaje;
    }

    /**
     * Función que realiza la autenticación de un servicio de fox.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 07-12-2018
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 11-09-2020
     * Se modifica para poder autenticar FOXPREMIUM, PARAMOUNT Y NOGGIN
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 09-08-2021
     * Se Parametriza el nombre del producto del WS.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 06-08-2021 Se modifican las validaciones para permitir la autenticación de servicios El Canal del Fútbol
     * 
     */
    public function autenticacionFox($arrayParametros)
    {
        $intErrorCode = 5;
        try
        {
            // La variable $arrayParametros['producto'] debe recibir unicamente los valores "fp" para FOXPREMIUM, 
            // "paramountlatam" para PARAMOUNT O "nogginlatam" para NOGGIN
            $strUsuario             = $arrayParametros["username"];
            $strPassword            = $arrayParametros["password"];
            $strProducto            = $arrayParametros['producto'];
            //Se valida el tipo de producto
            $arrayNombreProductoWs  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('NOMBRE_PRODUCTO_WS',//nombre parametro cab
                                            'COMERCIAL', //modulo cab
                                            'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                            'NOMBRE DE PRODUCTO WS', //descripcion det
                                            '','','','','',
                                            '18'); //empresa
            foreach($arrayNombreProductoWs as $arrayProducto)
            {
                //valida si el nombre del producto es el mismo que es registrado
                if($strProducto == $arrayProducto['valor1'])
                {
                    //guarda el nombre tecnico
                    $strNombreTecnico = $arrayProducto['valor2'];
                }
            }

            $arrayProducto = $this->determinarProducto(array('strNombreTecnico'=>$strNombreTecnico));

            if ($arrayProducto['Status'] == 'ERROR')
            {
                throw new \Exception($arrayProducto['Mensaje']);
            }
            $arrayMsjsErrorUsuario = $arrayProducto['arrayMsjsErrorUsuario'];
            
            $objInfoServicioRepository              = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                  'COMERCIAL', //modulo cab
                  'OBTENER_NOMBRE_TECNICO',//proceso cab
                  'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                  $arrayProducto['strNombreTecnico'],'','','','', '18');

            $arrayParamsGetIdServicioXUsuario       = array(
                                                            "strNombreTecnicoProd"          => $arrayProducto['strNombreTecnico'],
                                                            "strDescripcionCaract"          => $arrayProducto['strUser'],
                                                            "strValorCaract"                => $strUsuario,
                                                            "strEstadoSpcEstaParametrizado" => "SI");
            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                $arrayParamsGetIdServicioXUsuario["strDescripcionCaract"] = $arrayProducto['strCorreo'];
            }
            $arrayRespuestaGetIdServicioXUsuario    = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetIdServicioXUsuario);
            if($arrayRespuestaGetIdServicioXUsuario['status'] !== "OK")
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_5"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_5"]["msj"]);
            }
            $arrayRegistrosGetIdServicioXUsuario = $arrayRespuestaGetIdServicioXUsuario["arrayRegistros"];
            if(!isset($arrayRegistrosGetIdServicioXUsuario[0]) || empty($arrayRegistrosGetIdServicioXUsuario[0]))
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_2"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_2"]["msj"]);
            }
            
            $intIdServicio = $arrayRegistrosGetIdServicioXUsuario[0][self::INT_ID_SERVICIO];
            
            //Se encripta la contraseña actual.
            $strPasswordFoxPremium = $this->serviceCrypt->encriptar($strPassword);
            if(empty($strPasswordFoxPremium))                             
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_3"]["code"];
                throw new \Exception($arrayMsjsErrorUsuario["COD_ERROR_3"]["msj"] . $strUsuario);
            }
            
            $arrayParamsGetPasswordXIdServicio      = array(
                                                            "intIdServicio"                 => $intIdServicio,
                                                            "strDescripcionCaract"          => $arrayProducto['strPass'],
                                                            "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetPasswordXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetPasswordXIdServicio);
            if($arrayRespuestaGetPasswordXIdServicio['status'] !== "OK")
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_6"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_6"]["msj"]);
            }
            
            $arrayRegistrosGetPasswordXIdServicio = $arrayRespuestaGetPasswordXIdServicio["arrayRegistros"];
            if(!isset($arrayRegistrosGetPasswordXIdServicio[0]) || empty($arrayRegistrosGetPasswordXIdServicio[0])
                || $arrayRegistrosGetPasswordXIdServicio[0][self::STR_VALOR_SPC] != $strPasswordFoxPremium)
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_4"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_4"]["msj"]);
            }
            
            $arrayParamsGetSsidXIdServicio      = array(
                                                        "intIdServicio"                 => $intIdServicio,
                                                        "strDescripcionCaract"          => $arrayProducto['strSsid'],
                                                        "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetSsidXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetSsidXIdServicio);
            if($arrayRespuestaGetSsidXIdServicio['status'] !== "OK")
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_7"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_7"]["msj"]);
            }
            $arrayRegistrosGetSsidXIdServicio = $arrayRespuestaGetSsidXIdServicio["arrayRegistros"];
            if(!isset($arrayRegistrosGetSsidXIdServicio[0]) || empty($arrayRegistrosGetSsidXIdServicio[0]))
            {
                $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_8"]["code"];
                throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_8"]["msj"]);
            }
            
            $arrayRespuestaAutenticacion['access'] = true;
            if(isset($arrayProducto["strRequiereCorreo"]) && !empty($arrayProducto["strRequiereCorreo"])
                && $arrayProducto["strRequiereCorreo"] === "REQUIERE_CORREO")
            {
                if(!isset($arrayProducto['strCorreo']) || empty($arrayProducto['strCorreo']))
                {
                    $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_9"]["code"];
                    throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_9"]["msj"]);
                }
                
                $arrayParamsGetCorreoXIdServicio    = array(
                                                            "intIdServicio"                 => $intIdServicio,
                                                            "strDescripcionCaract"          => $arrayProducto['strCorreo'],
                                                            "strEstadoSpcEstaParametrizado" => "SI");
                $arrayRespuestaGetCorreoXIdServicio = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetCorreoXIdServicio);
                if($arrayRespuestaGetCorreoXIdServicio['status'] !== "OK")
                {
                    $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_9"]["code"];
                    throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_9"]["msj"]);
                }
                $arrayRegistrosGetCorreoXIdServicio = $arrayRespuestaGetCorreoXIdServicio["arrayRegistros"];
                if(!isset($arrayRegistrosGetCorreoXIdServicio[0]) || empty($arrayRegistrosGetCorreoXIdServicio[0]))
                {
                    $intErrorCode = $arrayMsjsErrorUsuario["COD_ERROR_10"]["code"];
                    throw new \Exception ($arrayMsjsErrorUsuario["COD_ERROR_10"]["msj"]);
                }
                $arrayRespuestaAutenticacion['email']         = $arrayRegistrosGetCorreoXIdServicio[0][self::STR_VALOR_SPC];
            }
            $arrayRespuestaAutenticacion['subscriber_id'] = $arrayRegistrosGetSsidXIdServicio[0][self::STR_VALOR_SPC];
            $arrayRespuestaAutenticacion['country_code']  = "EC";
        }
        catch(\Exception $ex)
        {
            $arrayError['errorCode']  = $intErrorCode;
            $arrayError['details']    = $ex->getMessage();
            $arrayRespuestaAutenticacion['access'] = false;
            $arrayRespuestaAutenticacion['error']  = $arrayError;
        }
        return json_encode($arrayRespuestaAutenticacion);
    }

    /**
     * Función que recibe los parámetros del WS e invoca a la función que tiene la lógica para el reinicio de contraseña.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 25-06-2018
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 11-09-2020
     * Se modifica para poder reiniciar contrasenia desde FOXPREMIUM, PARAMOUNT Y NOGGIN
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 09-08-2021
     * Se Parametriza el nombre del producto del WS.
     */
    public function reiniciaContraeniaDesdeFox($arrayParametros)
    {
        try
        {
            $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
            // La variable $arrayParametros['strProducto'] debe recibir unicamente los valores "fp" para FOXPREMIUM, 
            // "paramountlatam" para PARAMOUNT O "nogginlatam" para NOGGIN
            $strProducto   = $arrayParametros["strProducto"];
            //Se valida el tipo de producto
            $arrayNombreProductoWs  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('NOMBRE_PRODUCTO_WS',//nombre parametro cab
                                            'COMERCIAL', //modulo cab
                                            'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                            'NOMBRE DE PRODUCTO WS', //descripcion det
                                            '','','','','',
                                            '18'); //empresa
            foreach($arrayNombreProductoWs as $arrayProducto)
            {
                //valida si el nombre del producto es el mismo que es registrado
                if($strProducto == $arrayProducto['valor1'])
                {
                    //guarda el nombre tecnico
                    $strNombreTecnico = $arrayProducto['valor2'];
                }
            }

            $arrayProducto = $this->determinarProducto(array('strNombreTecnico'=>$strNombreTecnico));

            if ($arrayProducto['Status'] == 'ERROR')
            {
                throw new \Exception($arrayProducto['Mensaje']);
            }
            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                  'COMERCIAL', //modulo cab
                  'OBTENER_NOMBRE_TECNICO',//proceso cab
                  'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                  $arrayProducto['strNombreTecnico'],'','','','', $arrayParametros[self::STR_EMPRESA_COD]);
            $arrayParamsGetIdServicioXUsuario       = array(
                                                    "strNombreTecnicoProd"          => $arrayProducto['strNombreTecnico'],
                                                    "strDescripcionCaract"          => $arrayProducto['strUser'],
                                                    "strValorCaract"                => $arrayParametros["strUsuario"],
                                                    "strEstadoSpcEstaParametrizado" => "SI");
            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                $arrayParamsGetIdServicioXUsuario["strDescripcionCaract"] = $arrayProducto['strCorreo'];
            }
            $arrayRespuestaGetIdServicioXUsuario    = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetIdServicioXUsuario);
            if($arrayRespuestaGetIdServicioXUsuario['status'] !== "OK")
            {
                throw new \Exception ("No se ha podido consultar un servicio ligado al usuario proporcionado");
            }
            $arrayRegistrosGetIdServicioXUsuario = $arrayRespuestaGetIdServicioXUsuario["arrayRegistros"];
            if(!isset($arrayRegistrosGetIdServicioXUsuario[0]) || empty($arrayRegistrosGetIdServicioXUsuario[0]))
            {
                throw new \Exception ("No existe un servicio ligado al usuario proporcionado");
            }
            $intIdServicio = $arrayRegistrosGetIdServicioXUsuario[0][self::INT_ID_SERVICIO];

            $strRespuesta = $this->restablecerContrasenia(array(self::INT_ID_SERVICIO  => $intIdServicio,
                                                                "strNombreProducto"    => $strNombreTecnico,
                                                                "token"                => $arrayParametros["token"],
                                                                self::STR_EMPRESA_COD  => $arrayParametros[self::STR_EMPRESA_COD],
                                                                self::STR_USR_CREACION => $arrayProducto['strUserCreacionTelcos'],
                                                                self::STR_CLIENT_IP    => $arrayParametros[self::STR_CLIENT_IP]));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                return json_encode(array("status" => $strRespuesta, "message" => "Se realizó el proceso correctamente. "
              . "Hemos enviado un Correo electrónico con las indicaciones para que pueda cambiar su contraseña."));
            }
            return json_encode(array("status" => $strRespuesta, "message" => "Se ha realizado el reseteo de contraseña. "
                . "Hemos enviado un Correo electrónico y SMS con su nueva contraseña."));
        }
        catch(\Exception $ex)
        {
            return json_encode(array("status"=> "ERROR", "message" => $ex->getMessage()));
        }
    }

    /**
     * Documentación para el método 'validarServiciosFoxPremium'.
     *
     * Método utilizado para validar los siguiente:
     * - Se Valida para el ingreso de servicios FOX_PREMIUM que el punto posea al menos un servicio de internet en estado activo para dicho login
     *   y que la ultima milla del servicio de internet sea Fibra o Cobre 
     * - No debe permitirse el ingreso de mas de 1 servicio Fox Premium por Punto o Login.
     * - Se verifica si el Cliente ya posee un Servicio FoxPremium en estado Cancel y no Migrado para poder tomar la informacion 
     *   del LOGIN (USUARIO_FOX) y SUSCRIBER_ID (SSID_FOX) existente para el nuevo servicio FOX ingresado.     
     *
     * @param array $arrayParametros [ 'strEmpresaCod'               => 'Código de la empresa en session',
     *                                 'strPrefijoEmpresa'           => 'Prefijo de la empresa en session',
     *                                 'intIdPtoCliente'             => 'Id del punto cliente en sessión',
     *                                 'strNombreEstadosInternet'    => 'Nombre del detalle que contiene los estados a buscar en los servicios',
     *                                 'strParametroEstadosInternet' => 'Nombre del parámetro a buscar en los servicios' ]
     * 
     * @return array $arrayResultado[ 'strExisteServicioFoxPremium'   => 'Parámetro que indicará si existen servicios de Fox Premium en estado 
     *                                                                   'Pre-servicio','Activo','In-Corte'
     *                                'strExisteServicioInternet'     => 'Parámetro que indicará si existe algún servicio de internet contratado' 
     *                                'strExisteServCancelFoxPremium' => 'Parametro que indicara si el cliente posee un servicio FOXPREMIUM en estado Cancel
     *                                                                   y que no haya sido migrado su suscriber_id a otro servicio : MIGRADO_FOX='N'' 
     *                              ]
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 21-06-2018
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 13-05-2019 - Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 12-10-2020 - Se modifica mensajes para FOX, PARAMOUNT Y NOGGIN
     *                         - Se envía nuevos parametros: $strDescrCaracteristica  = Valores FOX_PREMIUM, PARAMOUNT O NOGGIN
     *                                                       $strNombreTecnico        = nombre técnico del producto
     *                                                       $strMigrar               = Caracteristica Migrar del producto
     *                                                       $strMensaje              = nombre del producto para mensaje
     *                         - Se modifica Nombre de variable de strExisteServicioFoxPremium   = strExisteServicio   y 
     *                                                            strExisteServCancelFoxPremium = strExisteServCancel.
     *                         - Se habilita opcion para validar el ingreso de productos adiconales Paramount o NOggin 
     *                              si el estado del internet es diferente de Activo.
     *                         - Se agrega validacion donde no permita ingresar productos Paramount o Noggin 
     *                              cuando el internet se encuentre en estado In-Corte
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 09-08-2021 - Se modifica validaciones de servicios existentes, cancelados e Incorte parametrizados.
     *                        - Se Agrega validación de estado de internet permitidos para agregar servicios ECDF.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 24-11-2021 Se modifica la función para validar correctamente la existencia de un servicio de Internet en los estados permitidos
     *                         que se encuentran parametrizados al agregar servicios El canal del Fútbol o Gol Tv
     * 
     * @author Emmanuel Fernando <emartillo@telconet.ec>
     * @version 1.5 25-08-2022 Se modifica la función para validar correctamente la existencia de un servicio 
     *                de Internet en los estados permitidos para ingresar el Producto PARAMOUNT+    
     */
    public function validarServiciosFoxPremium($arrayParametros)
	{
        $strEmpresaCod               = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) ) 
                                        ? $arrayParametros['strEmpresaCod'] : '';
        $strPrefijoEmpresa           = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) ) 
                                        ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPtoCliente             = ( isset($arrayParametros['intIdPtoCliente']) && !empty($arrayParametros['intIdPtoCliente']) )
                                        ? $arrayParametros['intIdPtoCliente'] : 0;
        $strNombreEstadosInternet    = (isset($arrayParametros['strNombreEstadosInternet']) && !empty($arrayParametros['strNombreEstadosInternet']))
                                        ? $arrayParametros['strNombreEstadosInternet'] : 'ESTADOS_INTERNET';
        $strParametroEstadosInternet = ( isset($arrayParametros['strParametroEstadosInternet']) 
                                         && !empty($arrayParametros['strParametroEstadosInternet']) )
                                        ? $arrayParametros['strParametroEstadosInternet'] : 'estadosServicios';
        $strDescrCaracteristica  = $arrayParametros['strDescCaracteristica'];
        $strNombreTecnico        = $arrayParametros['strNombreTecnico'];
        $strMigrar               = $arrayParametros['strMigrar'];
        $strMensaje              = $arrayParametros['strMensaje'];
        $arrayResultado          = array('strExisteServicioInternet'    => 'N', 
                                         'strExisteServicio'            => 'N',
                                         'strExisteServCancel'          => 'N');

        try
        {
            if( !empty($strEmpresaCod) && !empty($strPrefijoEmpresa) && !empty($intIdPtoCliente) 
            && !empty($strNombreEstadosInternet) && !empty($strParametroEstadosInternet) )
            {
                //Obtengo los estados de INTERNET válidos para la generación de la orden de trabajo de servicios Fox Premium,Paramount o Noggin
                $arrayParametrosDet = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')->get('INFO_SERVICIO', 
                                                                                                        'COMERCIAL', 
                                                                                                        'ACTIVACION_SERVICIO', 
                                                                                                        '', 
                                                                                                        $strNombreEstadosInternet, 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        $strEmpresaCod);

                if( !empty($arrayParametrosDet) )
                {
                    $arrayEstadosInternet = array();
                    foreach( $arrayParametrosDet as $arrayParametro )
                    {
                        $arrayEstadosInternet[] = ( isset($arrayParametro['valor2']) && !empty($arrayParametro['valor2']) )
                                                  ? trim($arrayParametro['valor2']) : '';
                    }
                    if( !empty($arrayEstadosInternet) )
                    {
                        $arrayNombreTecnicoParametrizable = array();
                        $arrayClasificacionParametrizable = array();
                        $arrayParametrizacionInicial      = array('strNombreParametroCab' => 'INFO_SERVICIO',
                                                                  'strModulo'             => 'COMERCIAL',
                                                                  'strProceso'            => 'ACTIVACION_SERVICIO');

                        if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "MD" )
                        {
                            $arrayNombreTecnicoParametrizable                        = $arrayParametrizacionInicial;
                            $arrayNombreTecnicoParametrizable['strValor1']           = 'NOMBRE_TECNICO';
                            $arrayNombreTecnicoParametrizable['strValorUltimaMilla'] = 'ULTIMAS_MILLAS_INTERNET_'.$strDescrCaracteristica;
                        }
                        else
                        {
                            if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" )
                            {
                                $arrayClasificacionParametrizable              = $arrayParametrizacionInicial;
                                $arrayClasificacionParametrizable['strValor1'] = 'CLASIFICACION';
                            }
                        }

                        $arrayPametrosServicios         = array($strParametroEstadosInternet       => $arrayEstadosInternet, 
                                                                'productoInternetPorLogin'         => 'S',
                                                                'estadoActivo'                     => 'Activo',
                                                                'empresaCod'                       => $strEmpresaCod,
                                                                'intIdPuntoCliente'                => $intIdPtoCliente,
                                                                'arrayNombreTecnicoParametrizable' => $arrayNombreTecnicoParametrizable,
                                                                'arrayClasificacionParametrizable' => $arrayClasificacionParametrizable);
                        
                        $arrayConsultaServiciosInternet = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                      ->getServiciosByCriterios($arrayPametrosServicios);

                        $arrayResultado['strExisteServicioInternet'] = ( isset($arrayConsultaServiciosInternet['total']) 
                                                                         && !empty($arrayConsultaServiciosInternet['total']) 
                                                                         && $arrayConsultaServiciosInternet['total'] > 0 ) ? 'S' : 'N';
                    }//( !empty($arrayEstadosInternet) )
                }//( !empty($arrayParametrosDet) )
                
                
                $objInfoPuntoCliente = $this->emcom->getRepository('schemaBundle:InfoPunto')->findOneById($intIdPtoCliente);
                
                if( !is_object($objInfoPuntoCliente) )
                {
                    throw new \Exception('No se encontró el punto para buscar si existen servicios '.$strMensaje.' en el login ');
                }//( is_object($objInfoPuntoCliente) )

                //valida si existe otro producto igual al que se va a ingresar en estado activo
                $arrayExisteProdTV = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('VALIDA_SERVICIOS_TV',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'VALIDACION',//proceso cab
                                                              'EXISTE_SERVICIO', //descripcion det
                                                              '','','','','',
                                                              '18'); //empresa
                if(in_array($strNombreTecnico,$arrayExisteProdTV))
                {
                    $arrayParamsServiciosFoxPremium   = array(
                                                            "nombreTecnicoProducto"      => $strNombreTecnico,
                                                            "estadosServicios"           => array('Pendiente','Activo','In-Corte'),
                                                            "productoInternetPorLogin"   => "S",
                                                            "estadoActivo"               => "Activo",
                                                            "empresaCod"                 => $strEmpresaCod,
                                                            "intIdPuntoCliente"          => $intIdPtoCliente
                                                            );
                    $arrayConsultaServiciosFoxPremium              = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                                ->getServiciosByCriterios($arrayParamsServiciosFoxPremium);
                    $arrayResultado['strExisteServicio'] = ( isset($arrayConsultaServiciosFoxPremium['total']) 
                                                                    && !empty($arrayConsultaServiciosFoxPremium['total']) 
                                                                    && $arrayConsultaServiciosFoxPremium['total'] > 0 ) ? 'S' : 'N';
                }
                
               // Verifico si el cliente ya posee un servicio Fox Premium,PARAMOUNT O NOGGIN en estado Cancelado que no haya sido migrado
               $arrayProdTVCancel = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('VALIDA_SERVICIOS_TV',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'VALIDACION',//proceso cab
                                                              'EXISTE_SERVICIO_CANCEL', //descripcion det
                                                              '','','','','',
                                                              '18'); //empresa
                if(in_array($strNombreTecnico,$arrayProdTVCancel))
                {
                    $arrayParamsServCancelFoxPremium   = array(
                                                            "intIdPersonaRol"            => $objInfoPuntoCliente->getPersonaEmpresaRolId()->getId(),
                                                            "strNombreTecnico"           => $strNombreTecnico,
                                                            "strEstadoServicio"          => array('Cancel'),
                                                            "strDescrCaracteristica"     => $strMigrar,
                                                            "strValorCaracteristica"     => 'N',
                                                            "strEstadoCaracServ"         => array('Eliminado','Cancelado'),
                                                            "intIdPuntoCliente"          => $intIdPtoCliente
                                                            );
                    $objInfoServicioRepository = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
                    $arrayRespuesta            = $objInfoServicioRepository->obtieneServicioIdCancelFoxPremium($arrayParamsServCancelFoxPremium);
                    $intIdServicio             = $arrayRespuesta[0][self::INT_ID_SERVICIO];
                    $arrayResultado['strExisteServCancel'] = ( isset($intIdServicio) && !empty($intIdServicio)
                                                                            && $intIdServicio>0) ? 'S' : 'N';
                }
                //validacion que permite conocer si existe Internet Activo en un punto para Productos Paramount y Noggin
                $arrayProdTVInCorte = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('VALIDA_SERVICIOS_TV',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'VALIDACION',//proceso cab
                                                              'EXISTE_SERVICIO_IN_CORTE', //descripcion det
                                                              '','','','','',
                                                              '18'); //empresa
                if(in_array($strNombreTecnico,$arrayProdTVInCorte))
                {
                    $arrayRespuestaServInternetValido   = $this->serviceTecnico->obtieneServicioInternetValido(array(
                                                                                        "intIdPunto"    => $intIdPtoCliente,
                                                                                        "strCodEmpresa" => $strEmpresaCod
                                                                                                                  ));
                    $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];

                    if($objServicioInternet == null)
                    {
                        $arrayResultado['strExisteServicioInternet']='N';
                    }
                    else if ($objServicioInternet->getEstado()!='Activo')
                    {
                        $arrayResultado['strExisteServicioInternet']='S';
                        $arrayResultado['strExisteServicioPaNo']='N';
                        $arrayResultado['strExisteServicio']='N';
                        //si el servicio se encuentra en esta In-Corte no permite ingresar Producto Paramount y Noggin
                        if ($objServicioInternet->getEstado()=='In-Corte')
                        {
                            $arrayResultado['strExisteServicioInternet']='S';
                            $arrayResultado['strExisteServicioPaNo']='S';
                        }
                    }
                    else
                    {
                        // Se valida que sea de valor N para permitir agregar mas productos Paramount y Noggin
                        $arrayResultado['strExisteServicio']='N';
                    }
                }
                //valida los estados permitidos para crear un servicio de ECDF
                $arrayProdTVyEstados = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('VALIDA_ESTADOS_INTERNET',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'OBTENER_PROD_TV_Y_ESTADOS_PERMITIDOS',//proceso cab
                                                              'PRODUCTO', //descripcion det
                                                              $strNombreTecnico,'','','','',
                                                              '18'); //empresa
                if(!empty($arrayProdTVyEstados))
                {
                    foreach($arrayProdTVyEstados as $arrayProdTv)
                    {
                        $arrayEstados[]   =   $arrayProdTv['valor2'];
                    }
                    
                    if(!empty($arrayEstados) && isset($intIdPtoCliente) && !empty($intIdPtoCliente))
                    {
                        $arrayParamsServicioInternet    = array('estadosServicios'          => $arrayEstados,
                                                                'productoInternetPorLogin'  => 'S',
                                                                'estadoActivo'              => 'Activo',
                                                                'empresaCod'                => $strEmpresaCod,
                                                                'intIdPuntoCliente'         => $intIdPtoCliente,
                                                                'nombreTecnicoProducto'     => 'INTERNET',
                                                                'omiteEstadoPunto'          => "SI");
                        $arrayConsultaServiciosInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getServiciosByCriterios($arrayParamsServicioInternet);
                        //Se verifica que exista un servicio de Internet en un estado permitido
                        if(isset($arrayConsultaServiciosInternet['total']) && !empty($arrayConsultaServiciosInternet['total'])
                            && $arrayConsultaServiciosInternet['total'] > 0 )
                        {
                            $arrayResultado['strExisteServicioInternet']        = 'S';
                            $arrayResultado['strServicioInternetEnEstadoOK']    = 'S';
                        }
                        else
                        {
                            $arrayRespuestaServInternetValido   = $this->serviceTecnico->obtieneServicioInternetValido(array(
                                                                                                "intIdPunto"    => $intIdPtoCliente,
                                                                                                "strCodEmpresa" => $strEmpresaCod
                                                                                                                          ));
                            $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];

                            if(is_object($objServicioInternet))
                            {
                                $arrayResultado['strExisteServicioInternet']        = 'S';
                                $arrayResultado['strServicioInternetEnEstadoOK']    = 'N';
                            }
                            else
                            {
                                $arrayResultado['strExisteServicioInternet'] = 'N';
                            }
                        }
                    }
                }
                
            }
            else
            {
                throw new \Exception('No se enviaron los parámetros adecuados para validar los servicios de '.$strMensaje);
            }//( !empty($strEmpresaCod) && !empty($strPrefijoEmpresa) && !empty($intIdPtoCliente) && !empty($strNombreEstadosInternet)...
        }
        catch( \Exception $e )
        {
            throw ($e);
        }

        return $arrayResultado;
    }

     /**
     * Documentación para el método 'crearHistorialServicioFox'.
     * Función que crea el Historial del servicio
     *  
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-05-2019 
     * 
     * @param arrayParametros ['objInfoServicio']     = > Objeto del servicio
     *                        ['strUsrCreacion']      => string del usuario
     *                        ['strClientIp']         => string de IP del cliente
     *                        ['strObservacion']      => string de Observación
     *                        ['strTipoAccion']       => string del Tipo de Acción    
     */
    public function crearHistorialServicioFox($arrayParametros)
    {
        $objInfoServicio          = $arrayParametros['objInfoServicio'];
        $objInfoServicioHistorial = new \telconet\schemaBundle\Entity\InfoServicioHistorial();
        $objInfoServicioHistorial->setServicioId($objInfoServicio);
        $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
        $objInfoServicioHistorial->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);        
        $objInfoServicioHistorial->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
        $objInfoServicioHistorial->setObservacion($arrayParametros['strObservacion']);
        $objInfoServicioHistorial->setAccion($arrayParametros['strTipoAccion']);
        $this->emcom->persist($objInfoServicioHistorial);
        $this->emcom->flush();        
    }
    /**
     * Documentación para el método 'guardaServProdCaracFoxPremium'.
     * Guarda Caracteristicas para el Servicio si el producto es FOX PRIMIUM
     * @param array $arrayParametros [ 'intIdPersona'      => 'Id de la persona',
     *                                 'intIdPersonaRol'   => 'Id del cliente IdPersonaRol'
     *                                 'intIdServicio'     => 'Id del servicio a Ingresarse',
     *                                 'intIdProducto'     => 'Id del Producto',
     *                                 'strUsrCreacion'    => 'Usuario en sesion']
     *      
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 21-06-2018    
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 26-12-2018
     * Se elimina el parámetro enviado a generaContraseniaFox
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 13-05-2019 - Se modifica mensajes donde indique FOX PREMIUM se modifica por Netlifeplay.
     *
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 3-09-2020 
     * -Se Modifica Nombre del Método guardaServProdCaracFoxPremium a guardaServProdCarac, ya que
     *  se utilizará para Servicios de FOXPREMIUM, PARAMOUNT y NOGGIN
     * -Se modifica funcionalidad para que guarde los ServProdCarac de los 
     *  porductos FOXPREMIUM, PARAMOUNT y NOGGIN
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4
     * Se modifica Metodo determinarProducto para validar los productos Paramount y Noggin
     * @since 07-12-2020
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.5  - Se Parametriza productos que entran al flujo de REINGRESO
     * @since 09-08-2021
     */
    public function guardaServProdCarac($arrayParametros)
    {
        $intIdServicio = $arrayParametros[self::INT_ID_SERVICIO];
        $intIdPtoCliente = $arrayParametros['intIdPuntoCliente'];
        //Trae el Producto y sus caracteristicas
        $arrayProducto = $this->determinarProducto(array('intIdProducto'=>$arrayParametros["intIdProducto"]));
        if ($arrayProducto['Status'] != 'OK')
        {
            $strMensaje = $arrayProducto['Mensaje'];
            throw new \Exception($strMensaje);
        }
        $arrayParametrosCaractFox = array( 'intIdProducto'         => $arrayParametros["intIdProducto"], 
                                           'strDescCaracteristica' => $arrayProducto['strDescCaracteristica'], 
                                           'strEstado'             => self::ESTADO_ACTIVO );
        $strEsProdCarac = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaractFox);
                
        if( !empty($strEsProdCarac) && $strEsProdCarac == "S" )
        {                              
            // Verifico si el cliente ya posee un servicio Fox Premium, Paramount o Noggin 
            // en estado Cancelado que no haya sido migrado
            
            $arrayParamsServCancel   = array(
                                                       "intIdPersonaRol"            => $arrayParametros["intIdPersonaRol"],
                                                       "strNombreTecnico"           => $arrayProducto['strNombreTecnico'],
                                                       "strEstadoServicio"          => array('Cancel'),
                                                       "strDescrCaracteristica"     => $arrayProducto['strMigrar'],
                                                       "strValorCaracteristica"     => 'N',
                                                       "strEstadoCaracServ"         => array('Eliminado','Cancelado'),
                                                       "intIdPuntoCliente"          => $intIdPtoCliente
                                                       );
            $objInfoServicioRepository     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
            $arrayRespuesta                = $objInfoServicioRepository->obtieneServicioIdCancelFoxPremium($arrayParamsServCancel);
            $intIdServicio                 = $arrayRespuesta[0][self::INT_ID_SERVICIO];
            $strExisteServCancel = ( isset($intIdServicio) && !empty($intIdServicio)
                                               && $intIdServicio>0) ? 'S' : 'N';
            //Si el cliente ya posee un Servicio FoxPremium, Paramount y Noggin en estado Cancel se procede a tomar la información 
            //del LOGIN (USUARIO_XX) y SUSCRIBER_ID (SSID_XXX) existente para el nuevo servicio ingresado, por tratarse de una
            // Recontratacion o Reingreso del cliente
            //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA EL FLUJO DE REINGRESO
            $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('FLUJO_DE_REEINGRESO',//nombre parametro cab
                                                    'COMERCIAL', //modulo cab
                                                    'NOMBRE_TECNICO_PROD_TV',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    '18'); //empresa
            foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
            {
            $arrayProdNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
            }
            if( $strExisteServCancel == 'S' && in_array($arrayProducto['strNombreTecnico'],$arrayProdNombreTecnico))             
            {                
                $arrayServicio            = $this->obtieneArrayCaracteristicas(array(self::INT_ID_SERVICIO => $intIdServicio,
                                                                                     'strEstado' => self::ESTADO_ELIMINADO));
                $objServProdCaracUsuario     = $arrayServicio[$arrayProducto['strUser']];
                $objServProdCaracSsid        = $arrayServicio[$arrayProducto['strSsid']];
                if(!is_object($objServProdCaracUsuario))                             
                {
                    throw new \Exception("No se pudo obtener el Usuario para el servicio ".$arrayProducto['strMensaje']);
                }  
                if(!is_object($objServProdCaracSsid))                             
                {
                    throw new \Exception("No se pudo obtener el SuscriberId para el servicio  ".$arrayProducto['strMensaje']);
                }
                $strUsuario = $objServProdCaracUsuario->getValor(); 
                $intSuscriberId       = $objServProdCaracSsid->getValor();                
            }
            else
            {   //Caso contrario debe generar el Usuario y debo tomar como SSID_xxx el ID_SERVICIO nuevo.

                $strUsuario  = $this->generaUsuarioFox(array('intIdPersona'     => $arrayParametros["intIdPersona"],
                                                             'strCaracUsuario'  => $arrayProducto['strUser'],
                                                             'strNombreTecnico' => $arrayProducto['strNombreTecnico']));
                if(empty($strUsuario))                             
                {
                    throw new \Exception("No se pudo obtener el Usuario para el servicio ".$arrayProducto['strMensaje']);
                }
                $intSuscriberId = $arrayParametros["intIdServicio"];
            }                
            //Genero y guardo Contrasenia encriptada
            $strPassword = $this->generaContraseniaFox();
            $strPassword = $this->serviceCrypt->encriptar($strPassword);
            if(empty($strPassword))                             
            {
                throw new \Exception("No se pudo generar Password para el servicio ".$arrayProducto['strMensaje']);
            }                                                                            
            //Ingreso Caracteristicas para FOX_PREMIUM, PARAMOUNT O NOGGIN
            $objProductoFoxPremium = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros["intIdProducto"]);
            if(!is_object($objProductoFoxPremium))
            {
                throw new \Exception("No se encontro Producto para el servicio ".$arrayProducto['strMensaje']);
            }
            $objInfoServicio= $this->emcom->getRepository('schemaBundle:InfoServicio')->find($arrayParametros["intIdServicio"]);
            if(!is_object($objInfoServicio))
            {
                throw new \Exception("No se encontro el Servicio para ".$arrayProducto['strMensaje']);
            }
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strSsid'],
                                                                       $intSuscriberId,$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strUser'],
                                                                       $strUsuario,$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strPass'],
                                                                       $strPassword,$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strMigrar'],
                                                                       'N',$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, 
                                                            $arrayProducto['strDescCaracteristica'],'S',$arrayParametros["strUsrCreacion"]);
        }
    }
    
    /**
     * Documentación para el método 'reenviarContrasenia'.
     * Función que reenvía credenciales del servicio FOX PREMIUM por notificación SMS Y CORREO.
     *  
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 08-05-2019 
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 15-09-2020
     * se modificó para que reenvíe contraseña por sms y correo 
     * a los productos FOX, PARAMOUNT y NOGGIN
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * Se modifica Metodo determinarProducto para validar los productos Paramount y Noggin
     * Se hace el envio de parametros inIdServicio y Nombre Tecnico para validar la notificacion por correo y sms
     * @since 07-12-2020
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.3
     * Se modifica el método para el producto ECDF, cuando el estado es diferente de Activo
     * Validamos que cumplan la fecha límite para poder realizar el envío de contraseña por correo y SMS
     * @since 07-12-2021
     * 
     * @param arrayParametros ['intIdServicio']       => Id del servicio.
     *                        ['strUsrCreacion']      => string del usuario.
     *                        ['strClientIp']         => string de IP del cliente.
     *                        ['strEmpresaCod']       => string de código de la empresa.
     */
    public function reenviarContrasenia($arrayParametros)
    {
        $strMensaje = self::OK;
        $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $intIdServicio       = $arrayParametros[self::INT_ID_SERVICIO];
            $objInfoServicio     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                               ->findOneById($intIdServicio);
            $strEstadoCaract    = "Activo";
            //Trae el Producto y sus caracteristicas
            $arrayProducto = $this->determinarProducto(array('strNombreTecnico'=>$arrayParametros['strNombreProducto']));
            if ($arrayProducto['Status'] != 'OK')
            {
                throw new \Exception($arrayProducto['Mensaje']);
            }

            //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA FLUJO DE CANCELACIÓN
            $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PERMITIR_RESTABLECER_PASS',//nombre parametro cab
                                                    'TECNICO', //modulo cab
                                                    'OBTENER_PROD_TV',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    $arrayParametros[self::STR_EMPRESA_COD]); //empresa
            foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
            {
                $arrayProdPermitido[]   =   $arrayNombreTecnico['valor1'];
            }

            if((self::ESTADO_CANCEL == $objInfoServicio->getEstado() || self::ESTADO_INCORTE == $objInfoServicio->getEstado())
            && in_array($arrayProducto['strNombreTecnico'],$arrayProdPermitido))
            {
                // validación para el producto ECDF
                if($arrayProducto['strNombreTecnico'] == "ECDF")
                {
                    $arrayMensajesParametrizados = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                      ->get('MENSAJES_REENVIO_CREDENCIALES_ECDF',//nombre parametro cab
                      'TECNICO', //modulo cab
                      'MENSAJES_REENVIO_CREDENCIALES_ECDF',//proceso cab
                      '', //descripcion det
                      '','','','','',
                      $arrayParametros[self::STR_EMPRESA_COD]);
                    
                    if(isset($arrayMensajesParametrizados) && !empty($arrayMensajesParametrizados)) 
                    {
                        $arrayMensajes = $arrayMensajesParametrizados[0];
                    }
                    if(self::ESTADO_INCORTE == $objInfoServicio->getEstado()) 
                    {
                        throw new \Exception ($arrayMensajes["valor1"]);
                    }
                    if(self::ESTADO_CANCEL == $objInfoServicio->getEstado()) 
                    {
                        $strEstadoCaract = "Eliminado";
                    }
                }

                $arrayParamsGetSsidXIdServicio      = array(
                    "intIdServicio"                 => $intIdServicio,
                    "strDescripcionCaract"          => $arrayProducto['strSsid'],
                    "strEstadoSpcEstaParametrizado" => "SI");
                $arrayRespuestaGetSsidXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetSsidXIdServicio);
                if($arrayRespuestaGetSsidXIdServicio['status'] !== "OK")
                {
                    throw new \Exception ("No se pudo obtener el SuscriberId del servicio");
                }
                $arrayRegistrosGetSsidXIdServicio = $arrayRespuestaGetSsidXIdServicio["arrayRegistros"];
                $intIdSpcUsuario = $arrayRegistrosGetSsidXIdServicio[0]["intIdSpc"];

                $arrayParametrosAuthorization   = array(
                                    'country_code'      =>'EC',
                                    'intIdSpcSuscriber' =>$intIdSpcUsuario,
                                    'strSsid'           => $arrayProducto['strSsid']
                            );
                $arrayRespuesta  =   $this->serviceAuthorizationFox->autorizarServicio($arrayParametrosAuthorization);
                if ($arrayRespuesta['strCodigoSalida'] != 'OK')
                {
                    throw new \Exception($arrayRespuesta['strMensajeSalida']);
                }
            }
            //Si el servicio no está Activo, no se puede reenviar la contraseña
            else if (self::ESTADO_ACTIVO != $objInfoServicio->getEstado())
            {
                throw new \Exception("No es posible reenviar la contraseña del usuario debido a que el"
                                     . " servicio no se encuentra en estado Activo.");
            }
            $arrayOrderBy                = array("feUltMod" => "ASC");
            $arrayServicioFox            = $this->obtieneArrayCaracteristicas(array(self::INT_ID_SERVICIO => $intIdServicio, 
            'strEstado' => $strEstadoCaract, 'arrayOrderBy' => $arrayOrderBy));
            $objServProdCaracContrasenia = $arrayServicioFox[$arrayProducto['strPass']];
            $objServProdCaracUsuario     = $arrayServicioFox[$arrayProducto['strUser']];
            $arrayProducto['strNombreTecnico'] == "ECDF" 
            ? $objServProdCaracCorreo = $arrayServicioFox[$arrayProducto['strCorreo']]->getValor()
            : null;
            
            $strContraseniaActual        = $this->serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
            $arrayParamHistorial         = array('strUsrCreacion'  => $arrayParametros[self::STR_USR_CREACION], 
                                                 'strClientIp'     => $arrayParametros[self::STR_CLIENT_IP], 
                                                 'objInfoServicio' => $objInfoServicio,
                                                 'strTipoAccion'   => $arrayProducto['strAccionReenvio'],
                                                 'strMensaje'      => $arrayProducto['strMensaje']);
            //Notificación al cliente por Correo y SMS
            $this->notificaCorreoServicioFox(array("strDescripcionAsunto"   => $arrayProducto['strAsuntoReenvContra'],
                                                   "strCodigoPlantilla"     => $arrayProducto['strCodPlantReenv'],
                                                   self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                                                   "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                                                   "intIdServicio"          => $objInfoServicio->getId(),
                                                   "strNombreTecnico"       => $arrayProducto['strNombreTecnico'],
                                                   "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                                                   "arrayParametros"        => array("contrasenia" => $strContraseniaActual,
                                                                                     "usuario"     => $objServProdCaracUsuario->getValor()),
                                                   "arrayParamHistorial"    => $arrayParamHistorial,
                                                   "strCorreoDest"          => $objServProdCaracCorreo));

            //Se reemplaza la contraseña del mensaje del parámetro            
            $strMensajeSMS = str_replace("{{USUARIO}}",
                                         $objServProdCaracUsuario->getValor(),
                                         str_replace("{{CONTRASENIA}}",
                                                     $strContraseniaActual,
                                                     $arrayProducto['strSmsReenvContra']));

            $this->notificaSMSServicioFox(array("strMensaje"             => $strMensajeSMS,
                                                "strTipoEvento"          => "enviar_infobip",
                                                self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                                                "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                                                "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                                                "arrayParamHistorial"    => $arrayParamHistorial,
                                                "strNombreTecnico"       => $arrayProducto['strNombreTecnico']));
            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {

            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = $ex->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'FoxPremiumService.reenviarContrasenia',
                                            'Error FoxPremiumService.reenviarContrasenia:'.$ex->getMessage(),
                                            $arrayParametros[self::STR_USR_CREACION],
                                            $arrayParametros[self::STR_CLIENT_IP]);
        }
        return $strMensaje;
    }

    /**
     * Documentación para el método 'determinarProducto'.
     * Función que determina y envía los parametros que corresponden al producto.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 15-09-2020
     * 
     * @param arrayParametros  =>   $arrayParametros['intIdServicio']     =  Id del servicio
     *                              $arrayParametros['intIdProducto']     =  Id del producto
     *                              $arrayParametros['strNombreTecnico']  =  Nombre tecnico del producto
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 7-12-2020   Se modifica que permita consultar por id servicio solo productos Adicionales.
     *                          Se agrega caracteristica de correo para productos Paramount y Noggin
     *                          Se agrega plantillas de SMS y valida si existen.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 28-06-2021   Se Agrega NombreParametro para acceder al ws de toolbox.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 09-08-2021   Se Parametriza todos los datos de los productos de tv.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4 27-08-2021   Se Agrega validación para cuando el producto no es permitido
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.5 28-09-2021   Se Modifica para obtener la fecha de activacion y fin de suscripcion de producto ECDF.
     * 
     */
    public function determinarProducto ($arrayParametros)
    {
        $arrayProducto              = array('');
        $arrayProducto['Status']    ='OK';
        $arrayProducto['Mensaje']   ='OK';
        $arrayProdNombreTecnico     = array();

        if (!empty($arrayParametros['intIdServicio']))
        {
            $objInfoServicio     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                                ->findOneById($arrayParametros['intIdServicio']);
            if (!is_object($objInfoServicio))
            {
                $arrayProducto['Status']='ERROR';
                $arrayProducto['Mensaje']='No es posible determinar el producto debido a que no se encontró el Servicio.';
            }
            else
            {
                $objProducto = $this->emcom->getRepository(self::ADMI_PRODUCTO)
                                            ->findOneById($objInfoServicio->getProductoId());
                if(!is_object($objProducto))
                {
                    $arrayProducto['Status']='ERROR';
                    $arrayProducto['Mensaje']='No es posible determinar el Producto.';
                }
            }
            
        }
       else if (!empty($arrayParametros['intIdProducto']))
       {
            $objProducto = $this->emcom->getRepository(self::ADMI_PRODUCTO)
                                        ->findOneById($arrayParametros['intIdProducto']);
            if(!is_object($objProducto))
            {
                $arrayProducto['Status']='ERROR';
                $arrayProducto['Mensaje']='No es posible determinar el Producto.';
            }
       }
       else if (!empty($arrayParametros['strNombreTecnico']))
       {
           $objProducto = $this->emcom->getRepository(self::ADMI_PRODUCTO)
                                        ->findOneBy(array("nombreTecnico" => $arrayParametros['strNombreTecnico']));
            if(!is_object($objProducto))
            {
                $arrayProducto['Status']='ERROR';
                $arrayProducto['Mensaje']='No es posible encontrar el Nombre del Producto.';
            }
       }
       else
       {
            $arrayProducto['Status']='ERROR';
            $arrayProducto['Mensaje']='No es posible encontrar el Producto.';
       }
        //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS
        $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PRODUCTOS_TV',//nombre parametro cab
                                            'COMERCIAL', //modulo cab
                                            'OBTENER_NOMBRE_TECNICO_PROD_TV',//proceso cab
                                            'PRODUCTOS_TV', //descripcion det
                                            '','','','','',
                                            '18'); //empresa
        foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
        {
            $arrayProdNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
        }
        if(empty($arrayProdNombreTecnico) )
        {
            $arrayProducto['Status']='ERROR';
            $arrayProducto['Mensaje']='No existen Nombres Tecnicos registrados';
        }

        //OBTIENE INFORMACION DEL PRODUCTO
        if(is_object($objProducto) && $arrayProducto['Status']=='OK' &&
           in_array($objProducto->getNombreTecnico(), $arrayProdNombreTecnico) )
        {
            $arrayProducto['strNombreTecnico']      = $objProducto->getNombreTecnico();
            
            //CARACTERISTICAS DEL PRODUCTO
            $arrayParametrosDetCarac = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CARAC_PRODUCTOS_TV',//nombre parametro cab
                                                          'COMERCIAL', //modulo cab
                                                          'OBTENER_CARACT_PROD_TV',//proceso cab
                                                          $arrayProducto['strNombreTecnico'], //descripcion det
                                                          '','','','','',
                                                          '18'); //empresa

            $arrayProducto['strProducto']           = $arrayParametrosDetCarac['valor1'];
            $arrayProducto['strUser']               = $arrayParametrosDetCarac['valor2'];
            $arrayProducto['strPass']               = $arrayParametrosDetCarac['valor3'];
            $arrayProducto['strSsid']               = $arrayParametrosDetCarac['valor4'];
            $arrayProducto['strMigrar']             = $arrayParametrosDetCarac['valor5'];
            $arrayProducto['strCorreo']             = $arrayParametrosDetCarac['valor6'];

            //USUARIOS
            $arrayParametrosDetUser = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('USUARIOS_PRODUCTOS_TV',//nombre parametro cab
                                                          'COMERCIAL', //modulo cab
                                                          'OBTENER_USER_PROD_TV',//proceso cab
                                                          $arrayProducto['strNombreTecnico'], //descripcion det
                                                          '','','','','',
                                                          '18'); //empresa
            $arrayProducto['strAccionReenvio']      = $arrayParametrosDetUser['valor1'];
            $arrayProducto['strAccionActivo']       = $arrayParametrosDetUser['valor2'];
            $arrayProducto['strUserCreacion']       = $arrayParametrosDetUser['valor3'];
            $arrayProducto['strUserCreacionTelcos'] = $arrayParametrosDetUser['valor4'];

            //DATA DE CONFIG
            $arrayParamData = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFI_ADICIONAL_PROD_TV',//nombre parametro cab
                                                          'COMERCIAL', //modulo cab
                                                          'OBTENER_DATOS_PROD_TV',//proceso cab
                                                          $arrayProducto['strNombreTecnico'], //descripcion det
                                                          '','','','','',
                                                          '18'); //empresa
            
            $arrayProducto['strDescCaracteristica'] = $arrayParamData['valor1'];
            $arrayProducto['strMensaje']            = $arrayParamData['valor2'];
            //TOOLBOX
            $arrayProducto['strNombreParametro']    = $arrayParamData['valor3'];
            //Fecha final de suscripción minima
            $arrayProducto['strFechaFin']           = $arrayParamData['valor5'];
            //Fecha Activacion
            $arrayProducto['strFechaActivacion']    = $arrayParamData['valor6'];

            //URN
            $arrayParametrosDetUrn = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('URN_PRODUCTOS_TV',//nombre parametro cab
                                                        'COMERCIAL', //modulo cab
                                                        'OBTENER_URN_PROD_TV',//proceso cab
                                                        $arrayProducto['strNombreTecnico'], //descripcion det
                                                        '','','','','',
                                                        '18'); //empresa

            $arrayProducto['strCodigoUrn']          = $arrayParametrosDetUrn['valor1'];

            //PLANTILLA CORREO
            $arrayParametrosDetCodPlantillaCorreo = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('COD_PLANTILLA_CORREO_PRODUCTOS_TV',//nombre parametro cab
                                                        'COMERCIAL', //modulo cab
                                                        'OBTENER_COD_PLANTILLA_PROD_TV',//proceso cab
                                                        $arrayProducto['strNombreTecnico'], //descripcion det
                                                        '','','','','',
                                                        '18'); //empresa

            $arrayProducto['strCodPlantRest']       = $arrayParametrosDetCodPlantillaCorreo['valor1'];
            $arrayProducto['strCodPlantReenv']      = $arrayParametrosDetCodPlantillaCorreo['valor2'];
            $arrayProducto['strCodPlantNuevo']      = $arrayParametrosDetCodPlantillaCorreo['valor3'];
            $arrayProducto['strAsuntoRestContra']   = $arrayParametrosDetCodPlantillaCorreo['valor4'];
            $arrayProducto['strAsuntoReenvContra']  = $arrayParametrosDetCodPlantillaCorreo['valor5'];
            $arrayProducto['strAsuntoNuevo']        = $arrayParametrosDetCodPlantillaCorreo['valor6'];

            //PLANTILLA SMS
            $arrayParametrosDetCodPlantillaSms = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('COD_PLANTILLA_SMS_PRODUCTOS_TV',//nombre parametro cab
                                                        'COMERCIAL', //modulo cab
                                                        'OBTENER_COD_PLANTILLA_SMS_PROD_TV',//proceso cab
                                                        $arrayProducto['strNombreTecnico'], //descripcion det
                                                        '','','','','',
                                                        '18'); //empresa

            $objPlantRestEcdf                  = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                        ->findOneBy(array('codigo' => $arrayParametrosDetCodPlantillaSms['valor1']));
            $objPlantReenvEcdf                 = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                        ->findOneBy(array('codigo' => $arrayParametrosDetCodPlantillaSms['valor2']));
            $objPlantNuevoEcdf                 = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                        ->findOneBy(array('codigo' => $arrayParametrosDetCodPlantillaSms['valor3']));

            if(is_object($objPlantRestEcdf) && is_object($objPlantNuevoEcdf) && is_object($objPlantReenvEcdf))
            {
                $arrayProducto['strSmsRestContra']      = $objPlantRestEcdf->getPlantilla();
                $arrayProducto['strSmsReenvContra']     = $objPlantReenvEcdf->getPlantilla();
                $arrayProducto['strSmsNuevo']           = $objPlantNuevoEcdf->getPlantilla();
            }
            else
            {
                $arrayProducto['strSmsRestContra']      = "";
                $arrayProducto['strSmsReenvContra']     = "";
                $arrayProducto['strSmsNuevo']           = "";
            }
            
            $arrayProducto['strRequiereCorreo']     = $arrayParamData['valor4'];    
            $arrayDetsMsjsErrorUsuario  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get('PARAMETROS_WS_PRODUCTOS_TV',
                                                            '',
                                                            '',
                                                            '',
                                                            'MENSAJES_ERRORES_USUARIO_AUTENTICACION',
                                                            $arrayProducto['strNombreTecnico'],
                                                            '',
                                                            '',
                                                            '',
                                                            '18');
            if(isset($arrayDetsMsjsErrorUsuario) && !empty($arrayDetsMsjsErrorUsuario))
            {
                $arrayMsjsErrorUsuario  = array();
                foreach($arrayDetsMsjsErrorUsuario as $arrayMsjErrorUsuario)
                {
                    $arrayMsjsErrorUsuario[$arrayMsjErrorUsuario["valor3"]] = array("code"  => $arrayMsjErrorUsuario["valor5"],
                                                                                    "msj"   => $arrayMsjErrorUsuario["valor4"]);
                }
                $arrayProducto['arrayMsjsErrorUsuario'] = $arrayMsjsErrorUsuario;
            }
            else
            {
                $arrayProducto['Status']    ='ERROR';
                $arrayProducto['Mensaje']   ='No se han configurado los mensajes de error';
            }
            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                      'COMERCIAL', //modulo cab
                      'OBTENER_NOMBRE_TECNICO',//proceso cab
                      'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                      $arrayProducto['strNombreTecnico'],'','','','', '18');
            if(is_array($objProdGenCred) && !empty($objProdGenCred))
            {
                $arrayProducto['strCodPlantConfActivacion']   = $objProdGenCred["valor2"];
                $arrayProducto['strCodPlantConfRest']         = $objProdGenCred["valor3"];
                $arrayProducto['strAsuntoConfActivacion']     = $objProdGenCred["valor4"];
                $arrayProducto['strAsuntoConfRest']           = $objProdGenCred["valor5"];
                $arrayProducto['strUrlProducto']              = $objProdGenCred["valor6"];
            }
        }
        else
        {
            $arrayProducto['Status']='ERROR';
            $arrayProducto['Mensaje']='El producto ingresado no coincide con los productos permitidos.';
        }
        return $arrayProducto;
    }

    /**
     * Documentación para el método 'convertirProductoAdicional'.
     * Función que convierte como Producto adicional un producto dentro de un plan.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @param arrayParametros  =>   $arrayParametros['ObjServicio']             =  Objeto del servicio
     *                              $arrayParametros['objProductoPlanDet']      =  Objeto  del producto dentro del plan
     *                              $arrayParametros['strUsrCreacion']          =  login del usuario que crea
     *                              $arrayParametros['strIpCreacion']           =  direccion IP del usuario
     */
    public function convertirAProductoAdicional ($arrayParametros)
    {
        $objServicioPlanViejo   = $arrayParametros['objServicio'];
        $objProductoPlanDet     = $arrayParametros['objProductoPlanDet'];
        $objPunto               = $objServicioPlanViejo->getPuntoId();
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];

        $arrayProducto = $this->determinarProducto(array('intIdProducto' =>  $objProductoPlanDet->getProductoId()));
        if($arrayProducto['Status']=='OK')
        {
            $objProducto = $this->emcom->getRepository(self::ADMI_PRODUCTO)
                                            ->findOneById($objProductoPlanDet->getProductoId());
            //Obtener caracteristica del servicio del plan viejo
            $arrayParam          = array('intIdServicio' =>  $objServicioPlanViejo->getId(), 'strEstado' => self::ESTADO_ACTIVO);
            $arrayCaracteristica = $this->obtieneArrayCaracteristicas($arrayParam);
            unset($arrayCaracteristica['CORREO ELECTRONICO']);
            //obtener precio del producto
            $strFuncionPrecio  =   $objProductoPlanDet->getCostoItem();
            
            //creacion del servicio Adicional
            $entityServicio = new InfoServicio();
            $entityServicio->setPuntoId($objPunto);
            $entityServicio->setProductoId($objProducto);
            $entityServicio->setEsVenta($objServicioPlanViejo->getEsVenta());
            $entityServicio->setCantidad($objServicioPlanViejo->getCantidad());
            $entityServicio->setPrecioVenta($strFuncionPrecio);
            $entityServicio->setFrecuenciaProducto($objServicioPlanViejo->getFrecuenciaProducto());
            $entityServicio->setMesesRestantes($objServicioPlanViejo->getMesesRestantes());
            $entityServicio->setEstado($objServicioPlanViejo->getEstado());
            $entityServicio->setFeCreacion(new \DateTime('now'));
            $entityServicio->setUsrCreacion($strUsrCreacion);
            $entityServicio->setIpCreacion($strIpCreacion);
            $entityServicio->setPuntoFacturacionId($objServicioPlanViejo->getPuntoFacturacionId());
            $entityServicio->setTipoOrden($objServicioPlanViejo->getTipoOrden());
            $entityServicio->setPrecioFormula($strFuncionPrecio);
            $entityServicio->setUsrVendedor($strUsrCreacion);
            $entityServicio->setOrigen($objServicioPlanViejo->getOrigen());
            
            $this->validator->validateAndThrowException($entityServicio);
            $this->emcom->persist($entityServicio);
            $this->emcom->flush();
            
            if($arrayCaracteristica != null)
            {
                //creacion de infoServicioProdCarac para producto adicional
                $arrayCaracProd    =   array();
                $arrayCaracProd[]  =   $arrayCaracteristica[$arrayProducto['strProducto']];
                $arrayCaracProd[]  =   $arrayCaracteristica[$arrayProducto['strUser']];
                $arrayCaracProd[]  =   $arrayCaracteristica[$arrayProducto['strSsid']];
                $arrayCaracProd[]  =   $arrayCaracteristica[$arrayProducto['strPass']];
                $arrayCaracProd[]  =   $arrayCaracteristica[$arrayProducto['strMigrar']];
                $objMigrar         =   $arrayCaracteristica[$arrayProducto['strMigrar']];
                
                foreach($arrayCaracProd as $objCaracteristica)
                {
                    //Se agrega servicioProdCaract al servicio adicional
                    $objInfoServicioProdCaract = new InfoServicioProdCaract();
                    $objInfoServicioProdCaract->setServicioId($entityServicio->getId());
                    $objInfoServicioProdCaract->setProductoCaracterisiticaId($objCaracteristica->getProductoCaracterisiticaId());
                    $objInfoServicioProdCaract->setValor($objCaracteristica->getValor());
                    $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                    $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioProdCaract->setEstado($objCaracteristica->getEstado());
                    //se cambia estado de Migrado a S, ya que se tomó el valor de otro SuscriberId 
                    if($objCaracteristica->getProductoCaracterisiticaId() ==  $objMigrar->getProductoCaracterisiticaId())
                    {
                        $objInfoServicioProdCaract->setValor('S');
                        $objInfoServicioProdCaract->setFeUltMod(new \DateTime(self::NOW));
                        $objInfoServicioProdCaract->setUsrUltMod($strUsrCreacion);
                    }
                    //se cambia a estado Eliminado las infoServicioProdCaract Antiguas
                    $objCaracteristica->setEstado(self::ESTADO_ELIMINADO);
                    $objCaracteristica->setFeUltMod(new \DateTime(self::NOW));
                    $objCaracteristica->setUsrUltMod($strUsrCreacion);
                    $this->emcom->persist($objCaracteristica);
                    
                    $this->emcom->persist($objInfoServicioProdCaract);
                    $this->emcom->flush();
                }
                //Se consulta los correos de ese producto adicional
                $arrayParametrosCaract  = array( 'strNombreProducto' => $objProducto->getNombreTecnico(),
                                                 'intIdServicio'     => $objServicioPlanViejo->getId());
                $arrayCaracteristicas   = $this->obtenerCaractCorreo($arrayParametrosCaract);
                
                foreach($arrayCaracteristicas['registros'] as $arrayDatos)
                {
                    $arrayCaratCorreo[] = $arrayDatos;
                }
                if(!empty($arrayCaratCorreo) && isset($arrayCaratCorreo))
                {
                    foreach($arrayCaratCorreo as $objCaracteristica)
                    {
                        $objCorreo  = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findOneById($objCaracteristica['id']);
                        //Se agrega servicioProdCaract al servicio adicional
                        $objInfoServicioProdCaract = new InfoServicioProdCaract();
                        $objInfoServicioProdCaract->setServicioId($entityServicio->getId());
                        $objInfoServicioProdCaract->setProductoCaracterisiticaId($objCorreo->getProductoCaracterisiticaId());
                        $objInfoServicioProdCaract->setValor($objCorreo->getValor());
                        $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                        $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                        $objInfoServicioProdCaract->setEstado($objCorreo->getEstado());

                        //se cambia a estado Eliminado las infoServicioProdCaract Antiguas
                        $objCorreo->setEstado(self::ESTADO_ELIMINADO);
                        $objCorreo->setFeUltMod(new \DateTime(self::NOW));
                        $objCorreo->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objCorreo);
                        
                        $this->emcom->persist($objInfoServicioProdCaract);
                        $this->emcom->flush();
                    }
                }
            }
            
            //Agregar Historial Producto adicional
            $objInfoServicioHistorial = new InfoServicioHistorial();
            $objInfoServicioHistorial->setServicioId($entityServicio);
            $objInfoServicioHistorial->setObservacion("El Producto <b>".$arrayProducto['strNombreTecnico'].
                                                      "</b> dentro del Plan pasó como un Producto Adicional");
            $objInfoServicioHistorial->setEstado(self::ESTADO_ACTIVO);
            $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
            $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
            $objInfoServicioHistorial->setAccion('confirmarServicio');
            $this->emcom->persist($objInfoServicioHistorial);
            $this->emcom->flush();
        }
        else 
        {
            throw new \Exception("No se pudo pasar el producto <b>".$arrayProducto['strNombreTecnico'].
                                    "</b> del plan como producto adicional");
        }
    }
    /**
     * Documentación para el método 'activarProductoEnPlan'.
     * Función que activa un producto dentro de un plan.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @param arrayParametros  =>   $arrayParametros['ObjServicio']             =  Objeto del servicio
     *                              $arrayParametros['objProductoPlanDet']      =  Objeto  del producto dentro del plan
     *                              $arrayParametros['strCodEmpresa']           =  Codigo de la empresa
     *                              $arrayParametros['strUsrCreacion']          =  login del usuario que crea
     *                              $arrayParametros['strIpCreacion']           =  direccion IP del usuario
     *                              $arrayParametros['objProducto']             =  Objeto del producto origen
     */
    public function activarProductoEnPlan ($arrayParametros)
    {
        $objServicioPlan        = $arrayParametros['objServicio'];
        $objProductoPlanDet     = $arrayParametros['objProductoPlanDet'];
        $objProducto            = $arrayParametros['objProducto'];
        $objPunto               = $objServicioPlan->getPuntoId();
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];

        $arrayProducto = $this->determinarProducto(array('intIdProducto' =>  $objProductoPlanDet->getProductoId()));
        if ($arrayProducto['Status'] == 'OK')
        {
            $arrayParametrosCaract = array(  'intIdProducto'         => $objProductoPlanDet->getProductoId(), 
                                             'strDescCaracteristica' => $arrayProducto['strDescCaracteristica'], 
                                             'strEstado'             => self::ESTADO_ACTIVO);
            $strEsProdCarac  = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaract);
            if( !empty($strEsProdCarac) && $strEsProdCarac == "S" )
            {                            
                $objInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                  ->find($objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId());
                if(!is_object($objInfoPersona))
                {
                    throw new \Exception("No se pudo guardar el Servicio - No se encontro Persona para generar "
                           ."Usuario para el servicio ".  $arrayProducto['strMensaje']);
                }
                $arrayParametrosServProdCarac = array('intIdPersona'   => $objInfoPersona->getId(), 
                                                   'intIdPersonaRol'   => $objPunto->getPersonaEmpresaRolId()->getId(),
                                                   'intIdServicio'     => $objServicioPlan->getId(),
                                                   'intIdProducto'     => $objProductoPlanDet->getProductoId(),
                                                   'strUsrCreacion'    => $strUsrCreacion,
                                                   'intIdPuntoCliente' => $objPunto->getId()
                                                  );
                $this->guardaServProdCarac($arrayParametrosServProdCarac);
                //Guardar correo forma contacto a la caracteristica del producto
                $objInfoPersonaEmpresaRol       = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                    ->find($objPunto->getPersonaEmpresaRolId()->getId());
                $arrayDestinatarios['valor']    = $this->emcom->getRepository("schemaBundle:AdmiFormaContacto")
                                                    ->obtieneFormaContactoxParametros(
                                                        array("intPuntoId"            => $objPunto->getId(),
                                                                "intPersonaId"        => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                        "strDescripcionFormaContacto" => "Correo Electronico"));
                $objCaract          =    $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                        ->findOneBy(array('descripcionCaracteristica'=>$arrayProducto['strCorreo']));
                $objAdmiProdCaract  =    $this->emcom->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                        ->findOneBy(array('productoId'          =>  $objProductoPlanDet->getProductoId(),
                                                          'caracteristicaId'    =>  $objCaract->getId() ));
                foreach($arrayDestinatarios['valor'] as $strCorreo)
                {
                    //Se agrega servicioProdCaract al Plan
                    $objInfoServicioProdCaract = new InfoServicioProdCaract();
                    $objInfoServicioProdCaract->setServicioId($objServicioPlan->getId());
                    $objInfoServicioProdCaract->setProductoCaracterisiticaId($objAdmiProdCaract->getId());
                    $objInfoServicioProdCaract->setValor($strCorreo);
                    $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                    $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioProdCaract->setEstado(self::ESTADO_ACTIVO);
                    
                    $this->emcom->persist($objInfoServicioProdCaract);
                    $this->emcom->flush();
                }
                
                //Historial del servicio por Activación del Servicio
                $objInfoServicioHistorial = new InfoServicioHistorial();
                $objInfoServicioHistorial->setServicioId($objServicioPlan);
                $objInfoServicioHistorial->setObservacion("Otros: Se confirmó el servicio");
                $objInfoServicioHistorial->setEstado("Activo");
                $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                $objInfoServicioHistorial->setAccion('confirmarServicio');
                $this->emcom->persist($objInfoServicioHistorial);
                
                $this->activarServicio(array(   "strUsrCreacion" => $strUsrCreacion,
                                                "strClientIp"    => $strIpCreacion,
                                                "strEmpresaCod"  => $strCodEmpresa,
                                                "intIdServicio"  => $objServicioPlan->getId()));
                $this->emcom->flush();
            }
        }
        else 
        {
            throw new \Exception("No se pudo Activar el Producto dentro del plan");
        }
    }

    /**
     * Documentación para el método 'convertirAPlan'.
     * Función que convierte el Producto adicional a un producto dentro de un plan.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @param arrayParametros  =>   $arrayParametros['objServicio']             =  Objeto del servicio del plan viejo
     *                              $arrayParametros['strUsrCreacion']          =  login del usuario que crea
     *                              $arrayParametros['strIpCreacion']           =  direccion IP
     *                              $arrayParametros['objProdAdicViejo']        =  Objeto del producto origen
     */
    public function convertirAPlan ($arrayParametros)
    {
        $objServicioPlanViejo   = $arrayParametros['objServicio'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $objServProdAdicViejo   = $arrayParametros['objProdAdicViejo'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];

        try
        {
            $arrayParam          = array('intIdServicio' =>  $objServProdAdicViejo->getId(), 'strEstado' => self::ESTADO_ACTIVO);
            $arrayCaracteristica = $this->obtieneArrayCaracteristicas($arrayParam);
            //Se eliminan las caracteristicas del correo ya que el metodo obtieneArrayCaracteristicas solo devuelve un correo
            unset($arrayCaracteristica['CORREO ELECTRONICO']);
            
            foreach($arrayCaracteristica as $objCaracteristica)
            {
                //Se agrega servicioProdCaract al Plan
                $objInfoServicioProdCaract = new InfoServicioProdCaract();
                $objInfoServicioProdCaract->setServicioId($objServicioPlanViejo->getId());
                $objInfoServicioProdCaract->setProductoCaracterisiticaId($objCaracteristica->getProductoCaracterisiticaId());
                $objInfoServicioProdCaract->setValor($objCaracteristica->getValor());
                $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                $objInfoServicioProdCaract->setEstado($objCaracteristica->getEstado());
                
                //se cambia a estado Eliminado las infoServicioProdCaract Antiguas del producto adicional
                $objCaracteristica->setEstado(self::ESTADO_ELIMINADO);
                $objCaracteristica->setFeUltMod(new \DateTime(self::NOW));
                $objCaracteristica->setUsrUltMod($strUsrCreacion);
                $this->emcom->persist($objCaracteristica);
                
                $this->emcom->persist($objInfoServicioProdCaract);
                $this->emcom->flush();
            }
            //Se consulta los correos de ese producto adicional
            $arrayParametrosCaract  = array( 'strNombreProducto' => $objServProdAdicViejo->getProductoId()->getNombreTecnico(),
                                             'intIdServicio'     => $objServProdAdicViejo->getId());
            $arrayCaracteristicas   = $this->obtenerCaractCorreo($arrayParametrosCaract);
            
            foreach($arrayCaracteristicas['registros'] as $arrayDatos)
            {
                $arrayCaratCorreo[] = $arrayDatos;
            }
            if(!empty($arrayCaratCorreo) && isset($arrayCaratCorreo))
            {
                foreach($arrayCaratCorreo as $objCaracteristica)
                {
                    $objCorreo  = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findOneById($objCaracteristica['id']);
                    //Se agrega servicioProdCaract al Plan
                    $objInfoServicioProdCaract = new InfoServicioProdCaract();
                    $objInfoServicioProdCaract->setServicioId($objServicioPlanViejo->getId());
                    $objInfoServicioProdCaract->setProductoCaracterisiticaId($objCorreo->getProductoCaracterisiticaId());
                    $objInfoServicioProdCaract->setValor($objCorreo->getValor());
                    $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
                    $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioProdCaract->setEstado($objCorreo->getEstado());
                    
                    //se cambia a estado Eliminado las infoServicioProdCaract Antiguas del producto adicional
                    $objCorreo->setEstado(self::ESTADO_ELIMINADO);
                    $objCorreo->setFeUltMod(new \DateTime(self::NOW));
                    $objCorreo->setUsrUltMod($strUsrCreacion);
                    $this->emcom->persist($objCorreo);
                    
                    $this->emcom->persist($objInfoServicioProdCaract);
                    $this->emcom->flush();
                }
            }

    
            //Poner en estado de cancel el producto adicional
            $objServProdAdicViejo->setEstado(self::ESTADO_CANCEL);
            $this->emcom->persist($objServProdAdicViejo);
            $this->emcom->flush();
    
            //Agregar Historial al Plan
            $objInfoServicioHistorialPlan = new InfoServicioHistorial();
            $objInfoServicioHistorialPlan->setServicioId($objServicioPlanViejo);
            $objInfoServicioHistorialPlan->setObservacion("El Producto Adicional <b>".$objServProdAdicViejo->getProductoId()->getNombreTecnico().
                                                          "</b> pasó como Producto dentro del Plan");
            $objInfoServicioHistorialPlan->setEstado(self::ESTADO_ACTIVO);
            $objInfoServicioHistorialPlan->setUsrCreacion($strUsrCreacion);
            $objInfoServicioHistorialPlan->setFeCreacion(new \DateTime(self::NOW));
            $objInfoServicioHistorialPlan->setIpCreacion($strIpCreacion);
            $objInfoServicioHistorialPlan->setAccion('confimarServicio');
            $this->emcom->persist($objInfoServicioHistorialPlan);
            $this->emcom->flush();

            //Agregar Historial Producto adicional
            $objInfoServicioHistorial = new InfoServicioHistorial();
            $objInfoServicioHistorial->setServicioId($objServProdAdicViejo);
            $objInfoServicioHistorial->setObservacion("El Producto Adicional <b>".$objServProdAdicViejo->getProductoId()->getNombreTecnico().
                                                            "</b> pasó como Producto dentro del Plan");
            $objInfoServicioHistorial->setEstado(self::ESTADO_CANCEL);
            $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
            $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
            $objInfoServicioHistorial->setAccion('cancelarCliente');
            $this->emcom->persist($objInfoServicioHistorial);
            $this->emcom->flush();

        }
        catch(\Exception $ex)
        {
            throw new \Exception("No se pudo llevar dentro del plan el producto adicional <b>".
                                    $objServProdAdicViejo->getProductoId()->getNombreTecnico()."</b>");
        }
    }
    /**
     * Documentación para el método 'guardarFormaContactoPunto'.
     * Función que guarda el contacto del punto.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @param arrayParametros  =>   $arrayParametros["arrayFormaContacto"]  = Datos del contacto del punto
     *                              $arrayParametros["strUsrCreacion"]      = Login del usuario
     *                              $arrayParametros["strNombreProducto"]   = Nombre del producto
     *                              $arrayParametros["strClientIp"]         = DIreccion Ip
     *                              $arrayParametros["intIdServicio"]       = Id del servicio
     */
    public function guardarFormaContactoPunto ($arrayParametros)
    {
        $arrayFormaContacto =   (array)$arrayParametros["arrayFormaContacto"];
        $strNombreProducto  =   $arrayParametros["strNombreProducto"];
        $intIdServicio      =   $arrayParametros["intIdServicio"];
        $strUsrCreacion     =   $arrayParametros["strUsrCreacion"];
        $strClientIp        =   $arrayParametros["strClientIp"];
        $strMensaje         =   self::OK;
        
        $objServicio                = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        
        $arrayParametrosCaract  = array( 'strNombreProducto' => $strNombreProducto,
                                         'intIdServicio'     => $intIdServicio);
        $arrayCaracteristicas   = $this->obtenerCaractCorreo($arrayParametrosCaract);
        if(!empty($arrayCaracteristicas['registros']))
        {
            foreach($arrayCaracteristicas['registros'] as $arrayDatos)
            {
                $arrayCaratCorreo[] = $arrayDatos;
            }
        }
        else
        {
            $arrayCaratCorreo[]['valor']= '';
        }

        if(is_array($arrayCaratCorreo) && is_array($arrayFormaContacto))
        {
            //Barrido de data a guardar y eliminar
            foreach($arrayFormaContacto as $intClave => $objContacto)
            {
                foreach($arrayCaratCorreo as $intIndice => $arrayAdmiContacto)
                {
                    if($objContacto['valor'] == $arrayAdmiContacto['valor'])
                    {
                        unset($arrayFormaContacto[$intClave]);
                        unset($arrayCaratCorreo[$intIndice]);
                    }
                }
            }
            $arrayProducto = $this->determinarProducto(array('strNombreTecnico' =>  $strNombreProducto));
            //logica de guardar
            if ($arrayFormaContacto != null && $arrayProducto['Status']=='OK')
            {
                foreach($arrayFormaContacto as $objFormaContacto)
                {
                    //Consultar el Id del producto
                    $objProducto  = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array('nombreTecnico' => $arrayProducto['strNombreTecnico'],
                                                                    'estado'          => self::ESTADO_ACTIVO));
                    //Buscamos el id de la caracteristica CORREO ELECTRONICO
                    $objAdmiCaracteristica      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "estado"                    => self::ESTADO_ACTIVO,
                                                                        "descripcionCaracteristica" => $arrayProducto['strCorreo']));
                    $intProdCaractContraseniaId = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array("productoId"     => $objProducto->getId(),
                                                                                "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                "estado"           => self::ESTADO_ACTIVO))
                                                                ->getId();
                    //guardar en la InfoServicioProdCaract el correo nuevo
                    $objInfoServicioProdCaract = new \telconet\schemaBundle\Entity\InfoServicioProdCaract();
                    $objInfoServicioProdCaract->setServicioId($intIdServicio);
                    $objInfoServicioProdCaract->setValor($objFormaContacto['valor']);
                    $objInfoServicioProdCaract->setEstado(self::ESTADO_ACTIVO);
                    $objInfoServicioProdCaract->setProductoCaracterisiticaId($intProdCaractContraseniaId);
                    $objInfoServicioProdCaract->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));

                     //Agregar Historial
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    $objInfoServicioHistorial->setObservacion("Se agrego el siguiente correo: " .$objFormaContacto['valor']);
                    $objInfoServicioHistorial->setEstado(self::ESTADO_ACTIVO);
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
                    $objInfoServicioHistorial->setIpCreacion($strClientIp);
                    $objInfoServicioHistorial->setAccion('agregarCorreo');
                    
                    $this->emcom->persist($objInfoServicioProdCaract);
                    $this->emcom->persist($objInfoServicioHistorial);
                    $this->emcom->flush();
                }
            }
            //logica de Eliminar
            if($arrayCaratCorreo != null)
            {
                foreach($arrayCaratCorreo as $objAdmiFormContac)
                {
                    if(isset($objAdmiFormContac['id']))
                    {
                        $objAdmiContac  = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findOneById($objAdmiFormContac['id']);
                        //Setea el estado como inactivo
                        $objAdmiContac->setEstado(self::ESTADO_ELIMINADO);
                        $objAdmiContac->setFeUltMod(new \DateTime(self::NOW));
                        $objAdmiContac->setUsrUltMod($strUsrCreacion);
    
                         //Agregar Historial
                         $objInfoServicioHistorial = new InfoServicioHistorial();
                         $objInfoServicioHistorial->setServicioId($objServicio);
                         $objInfoServicioHistorial->setObservacion("Se elimino el siguiente correo: " .$objAdmiFormContac['valor']);
                         $objInfoServicioHistorial->setEstado(self::ESTADO_ACTIVO);
                         $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                         $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
                         $objInfoServicioHistorial->setIpCreacion($strClientIp);
                         $objInfoServicioHistorial->setAccion('eliminarCorreo');
    
                        $this->emcom->persist($objAdmiContac);
                        $this->emcom->persist($objInfoServicioHistorial);
                        $this->emcom->flush();
                    }
                }
            }
        }
        else
        {
            $strMensaje = "ERROR";
        }
        return $strMensaje;
    }

    /**
     * Documentación para el método 'obtenerCaractCorreo'.
     * Función que obtiene el correo de la caracteristica del producto.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 30-09-2021 - Corrige escenario de que no posea la caracteristica el producto y se caiga el flujo
     * 
     * @param arrayParametros  =>   $arrayParametros["strNombreProducto"]   = Nombre del producto
     *                              $arrayParametros["intIdServicio"]       = Id del servicio
     */
    public function obtenerCaractCorreo($arrayParametros)
    {
        $arrayArreglo = array();
        $objProducto  = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array('nombreTecnico' => $arrayParametros['strNombreProducto'],
                                                        'estado'          => self::ESTADO_ACTIVO));
        //Buscamos el id de la caracteristica CORREO ELECTRONICO por producto
        if(is_object($objProducto))
        {
            $objAdmiCaracteristica      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array(  "estado"                    => self::ESTADO_ACTIVO,
                                                                    "descripcionCaracteristica" => 'CORREO ELECTRONICO'));
            $objProdCaractContrasenia = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array("productoId"     => $objProducto->getId(),
                                                                    "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                    "estado"           => self::ESTADO_ACTIVO));
            if (!empty($objProdCaractContrasenia))
            {
                $intProdCaractContraseniaId = $objProdCaractContrasenia->getId();
                
                $arrayCaratCorreo   =   $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findBy(array( 'productoCaracterisiticaId'  =>  $intProdCaractContraseniaId,
                                                                    'estado'                     =>  self::ESTADO_ACTIVO,
                                                                    'servicioId'                 =>  $arrayParametros['intIdServicio']));
                
                if(is_array($arrayCaratCorreo))
                {
                    foreach ($arrayCaratCorreo as $objValue)
                    {
                        $arrayArreglo[] = array(
                                        'id' => $objValue->getId(),
                                        'formaContacto' => 'Correo Electronico',
                                        'valor' => $objValue->getValor());
                    }
                }
                else
                {
                    $arrayArreglo[] = array(
                        'id'             => $arrayCaratCorreo->getId(),
                        'formaContacto' => 'Correo Electronico',
                        'valor'          => $arrayCaratCorreo->getValor());
                }
            }

        }
        return array('registros' => $arrayArreglo);

    }

    /**
     * Documentación para el método 'eliminarCaractCorreo'.
     * Función que Cambia de estado a eliminado el correo de la caracteristica del producto.
     *  
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 07-12-2020
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 07-12-2021
     * Se realizó una validación, en caso de algún error para eliminar el historial del correo en el canal del futbol
     * 
     * @param arrayParametros  =>   $arrayParametros["strNombreProducto"]   = Nombre del producto
     *                              $arrayParametros["intIdServicio"]       = Id del servicio
     */
    public function eliminarCaractCorreo($arrayParametros)
    {
        $objProducto  = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array('nombreTecnico' => $arrayParametros['strNombreTecnico'],
                                                        'estado'          => self::ESTADO_ACTIVO));
        //Buscamos el id de la caracteristica CORREO ELECTRONICO por producto en el Servicio
        if(is_object($objProducto) && isset($arrayParametros['intIdServicio']))
        {
            $objAdmiCaracteristica      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array(  "estado"                    => self::ESTADO_ACTIVO,
                                                                    "descripcionCaracteristica" => 'CORREO ELECTRONICO'));
            $intProdCaractContraseniaId = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array("productoId"     => $objProducto->getId(),
                                                                    "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                    "estado"           => self::ESTADO_ACTIVO))
                                                    ->getId();
            if($intProdCaractContraseniaId != null)
            {
                $arrayCaratCorreo   =   $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findBy(array( 'productoCaracterisiticaId'  =>  $intProdCaractContraseniaId,
                                                                    'estado'                     =>  self::ESTADO_ACTIVO,
                                                                    'servicioId'                 =>  $arrayParametros['intIdServicio']));
            }
        }
        //Si existen correos electronicos se cambia el estado
        if(is_array($arrayCaratCorreo))
        {
            //Recorremos el arreglo y seteamos el estado eliminado
            foreach($arrayCaratCorreo as $objCorreo)
            {
                //Setea el estado como inactivo
                $objCorreo->setEstado(self::ESTADO_ELIMINADO);
                $objCorreo->setFeUltMod(new \DateTime(self::NOW));
                $objCorreo->setUsrUltMod($arrayParametros['strUsrCreacion']);
                $this->emcom->persist($objCorreo);
                $this->emcom->flush();
            }
            if ($arrayParametros['strNombreTecnico'] === "ECDF") 
            {
              $objInfoServicioCorreoHistorial    = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $arrayParametros['intIdServicio'],
                                                                          'accion'     => 'agregarCorreo',
                                                                          'estado'     => 'Activo')
                                                                        );
              $objInfoServicioCorreoHistorial->setEstado("Eliminado");
              $this->emcom->persist($objInfoServicioCorreoHistorial);
              $this->emcom->flush();
            }
        }
    }
    
    /**
     * Documentación para el método 'activarServicioECDF'.
     * Función que activa el producto ECDF en estado Pendiente y envia las credenciales nuevas al cliente.
     *  
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 07-12-2021
     * 
     * @param arrayParametros  =>   $arrayParametros["intIdServicio"]           = Id del servicio
     *                              $arrayParametros['strUsrCreacion']          = string del usuario
     *                              $arrayParametros['strClientIp']             = string de IP del cliente
     *                              $arrayParametros['strEmpresaCod']           = Codigo de la empresa
     *                              $arrayParametros['intIdPersonaEmpresaRol']  = Id del rol de la persona
     */
    public function activarServicioECDF($arrayParametros)
    {
        $strMensaje = self::OK;
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $intIdServicio            = $arrayParametros[self::INT_ID_SERVICIO];
            $intIdPersonaEmpresaRol   = $arrayParametros["intIdPersonaEmpresaRol"];
            $objServicio              = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto              = $objServicio->getProductoId();
            $objPersonaEmpresaRol     = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRol);
            $objPersona               = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($objPersonaEmpresaRol->getPersonaId()->getId());
            $arrayParam               = array('intIdServicio' =>  $intIdServicio, 'strEstado' => self::ESTADO_ACTIVO);
            
            //servicio
            $objServicio->setEstado("Activo");
            $this->emcom->persist($objServicio);
            
            $arrayCaracteristica = $this->obtieneArrayCaracteristicas($arrayParam);
            $arrayProducto       = $this->determinarProducto(array('intIdServicio'=>$intIdServicio));
            $strNombreTecnico    = $arrayProducto["strNombreTecnico"];

            //Generación de credenciales con el nuevo correo
            $arrayParametrosGenerarUsuario["intIdPersona"]     = $objPersona->getId();
            $arrayParametrosGenerarUsuario["strCaracUsuario"]  = $arrayProducto['strUser'];
            $arrayParametrosGenerarUsuario["strNombreTecnico"] = $arrayProducto["strNombreTecnico"];

            $strUsuario  = $this->generaUsuarioFox($arrayParametrosGenerarUsuario);

            if(empty($strUsuario))
            {
                throw new \Exception("No se pudo obtener Usuario para el servicio ".$objProducto->getDescripcionProducto());
            }

            $strPassword           = $this->generaContraseniaFox();
            $strPasswordEncriptado = $this->serviceCrypt->encriptar($strPassword);
            if(empty($strPassword))
            {
                throw new \Exception("No se pudo generar Password para el servicio ".$objProducto->getDescripcionProducto());
            }

            $objCorreoElectronico         = $arrayCaracteristica["CORREO ELECTRONICO"];
            $arrayCaracteristicaCorreo[]  = array('caracteristica' => 'CORREO ELECTRONICO', 
                                                  'valor' => $objCorreoElectronico->getValor());

            $strCorreoAnterior = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                'CORREO ELECTRONICO',
                                                $objServicio->getProductoId(),
                                                array("strEstadoSpc" => "Cancel")
                                                );
            $arrayParametrosECDF["email_old"]              = $strCorreoAnterior->getValor();
            $arrayParametrosECDF["email_new"]              = $objCorreoElectronico->getValor();
            $arrayParametrosECDF["usrCreacion"]            = $arrayParametros[self::STR_USR_CREACION];
            $arrayParametrosECDF["ipCreacion"]             = $arrayParametros[self::STR_CLIENT_IP];
            $arrayParametrosECDF['boolCrearTarea']         = $arrayParametros["boolCrearTarea"];

            $arrayResultado  = $this->actualizarCorreoECDF($arrayParametrosECDF);
            if($arrayResultado['mensaje'] != 'ok')
            {
                $arrayParametros["boolEliminarCorreo"] = false;
                $strMensaje = "";
                if (!$arrayParametros["boolActualizar"])
                {
                    $strMensaje = "El correo se agregó correctamente. Sin embargo, no se activó el servicio <br />";
                }
                throw new \Exception($strMensaje."".$arrayResultado['mensaje']);
            }
            else
            {
              $arrayCaratCorreoAnt   =   $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
              ->find($strCorreoAnterior->getId());
              $arrayCaratCorreoAnt->setEstado(self::ESTADO_ELIMINADO);
              $this->emcom->persist($arrayCaratCorreoAnt);
            }

            //Insertar nuevas caracteristicas: usuario y password
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio,
                                                                    $objProducto,
                                                                    $arrayProducto['strUser'],
                                                                    $strUsuario,
                                                                    $arrayParametros[self::STR_USR_CREACION]);

            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio,
                                                                    $objProducto,
                                                                    $arrayProducto["strPass"],
                                                                    $strPasswordEncriptado,
                                                                    $arrayParametros[self::STR_USR_CREACION]);


            $objInfoServicioConfirmarHistorial    = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $intIdServicio,
                                                                          'accion'     => 'confirmarServicio',
                                                                          'estado'     => 'Pendiente')
                                                                        );
            $objInfoServicioFeOrigenHistorial    = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $intIdServicio,
                                                                          'accion'     => 'feOrigenCambioRazonSocial',
                                                                          'estado'     => 'Pendiente')
                                                                        );


            /*Buscar el valor de la caracteristica para colocar la fecha.*/
            $strFechaActivacion = $this->serviceTecnico->getCaracteristicaServicio($objServicio, 'FECHA_ACTIVACION');

            /*Validamos que existe el objeto, y registramos los elementos del historial.*/
            if (!empty($strFechaActivacion))
            {
                // Validamos si existe le objeto.
                if (!is_object($objInfoServicioConfirmarHistorial)) 
                {
                    /*Crear los nuevos registros de la fecha.*/
                    $entityServicioHistorialConf = new InfoServicioHistorial();
                    $entityServicioHistorialConf->setServicioId($objServicio);
                    $entityServicioHistorialConf->setFeCreacion(new \Datetime($strFechaActivacion));
                    $entityServicioHistorialConf->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
                    $entityServicioHistorialConf->setEstado('Activo');
                    $entityServicioHistorialConf->setAccion('confirmarServicio');
                    $entityServicioHistorialConf->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
                    $entityServicioHistorialConf->setObservacion('Se Confirmó el Servicio por cambio de razón social');
                    $this->emcom->persist($entityServicioHistorialConf);
                    $this->emcom->flush();
                }else
                {
                    $objInfoServicioConfirmarHistorial->setEstado("Activo");
                }

                // Validamos que existe el objeto.
                if (!is_object($objInfoServicioFeOrigenHistorial)) 
                {
                    /*Crear los nuevos registros de la fecha.*/
                    $entityServicioHistorialFeOri = new InfoServicioHistorial();
                    $entityServicioHistorialFeOri->setServicioId($objServicio);
                    $entityServicioHistorialFeOri->setFeCreacion(new \Datetime($strFechaActivacion));
                    $entityServicioHistorialFeOri->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
                    $entityServicioHistorialFeOri->setEstado('Activo');
                    $entityServicioHistorialFeOri->setAccion('feOrigenCambioRazonSocial');
                    $entityServicioHistorialFeOri->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
                    $entityServicioHistorialFeOri->setObservacion('Fecha inicial de servicio por Cambio de razón social.');
                    $this->emcom->persist($entityServicioHistorialFeOri);
                    $this->emcom->flush();
                }else
                {
                    $objInfoServicioFeOrigenHistorial->setEstado("Activo");
                }

            } 
            else 
            {
                throw new \Exception("No existe la caracteristica de fecha de activacion ECDF.");
            }
                                                                    
            //historial del servicio de cambio de razón social
            $entityServicioHistorial = new InfoServicioHistorial();
            $entityServicioHistorial->setServicioId($objServicio);
            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
            $entityServicioHistorial->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
            $entityServicioHistorial->setEstado($objServicio->getEstado());
            $entityServicioHistorial->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
            $entityServicioHistorial->setObservacion('Cambio de razon social');
            $this->emcom->persist($entityServicioHistorial);
            $this->emcom->flush();


            $this->activarServicio(array(   "strUsrCreacion"            => $arrayParametros[self::STR_USR_CREACION],
                                                "strClientIp"           => $arrayParametros[self::STR_CLIENT_IP],
                                                "strEmpresaCod"         => $arrayParametros[self::STR_EMPRESA_COD],
                                                "intIdServicio"         => $intIdServicio,
                                                "intIdProducto"         => $objProducto->getId(),
                                                "activarCorreoECDF"     => "SI",
                                                'arrayCaracteristicas'  => $arrayCaracteristicaCorreo));

            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = $ex->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'FoxPremiumService.activarServicioECDF',
                                            'Error FoxPremiumService.activarServicioECDF:'.$ex->getMessage(),
                                            $arrayParametros[self::STR_USR_CREACION],
                                            $arrayParametros[self::STR_CLIENT_IP]);
            if($arrayParametros["boolEliminarCorreo"])
            {
                $arrayParameter =   array(
                  "strNombreTecnico"  =>  $strNombreTecnico,
                  "strUsrCreacion"    =>  $arrayParametros[self::STR_USR_CREACION],
                  "intIdServicio"     =>  $intIdServicio
              );
                $this->eliminarCaractCorreo($arrayParameter);
            }
        }
        return $strMensaje;
    }

    /**
     * Documentación para el método 'actualizarCorreoECDF'.
     *
     * Función que sirve para consumir el WS del canal del futbol para actualizar el correo de un cliente
     * al momento de realizar un cambio de razón social
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 23-12-2021
     *
     * @param Array $arrayParametros [
     *          'email_old'             Correo antiguo
     *          'email_new'             Correo nuevo
     *          'usrCreacion'       -   Usuario creación
     *          'ipCreacion'        -   Ip cliente
     *      ]
     *
     * @return string[] $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *      ]
     */
    public function actualizarCorreoECDF($arrayParametros)
    {
        $strUsrSesion               = $arrayParametros['usrCreacion'];
        $strIpClient                = $arrayParametros['ipCreacion'];
        $strLoginOrigen             = $arrayParametros['strLoginOrigen'];
        $strLoginDestino            = $arrayParametros['strLoginDestino'];
        $intIdEmpresa               = $arrayParametros['intIdEmpresa'];
        $strPrefijoEmpresa          = $arrayParametros['strPrefijoEmpresa'];
        $strUsuarioAsigna           = $arrayParametros['strUsuarioAsigna'];
        $intIdPersonaEmpresaRol     = $arrayParametros['intIdPersonaEmpresaRol'];
        $intPuntoId                 = $arrayParametros['intPuntoId'];
        $strIdentificacionCliente   = $arrayParametros['identificacionCliente'];

        $arrayRespuesta = array(
            "status" => "ERROR",
            "mensaje" => "Ha ocurrido un error, por favor notificar a Sistemas."
        );

        $strApiKey            = $this->objContainer->getParameter('apikey_ecdf');
        $strUrlFinal          = $this->objContainer->getParameter('ws_actualizar_correo_ecdf');

        if (!empty($strApiKey) && !empty($strUrlFinal))
        {
            try
            {
                $objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
                    "apikey: ". $strApiKey);

                $arrayDataWS["email_old"]     = $arrayParametros["email_old"];
                $arrayDataWS["email_new"]     = $arrayParametros["email_new"];
                $strJsonData                  = json_encode($arrayDataWS, true);

                /*postJSON: {"result":"{\"success\": false, \"status\": \"Email no valido\"}","status":200,"error":""}*/
                $arrayRespJsonWS              = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
                /*Debemos parsear el contenido de result para poder utilizarlo en PHP.*/
                $arrayResult                  = json_decode($arrayRespJsonWS['result'], true);

                /*Validamos la respuesta del WS, que sea 200 para continuar.*/
                if (isset($arrayRespJsonWS['status']) && $arrayRespJsonWS['status'] == 200)
                {
                    /*Validamos los valores de la respuesta.*/
                    if (isset($arrayResult['success']) && $arrayResult['success'])
                    {
                        $arrayRespuesta = array(
                            "mensaje" => "ok"
                        );
                    }
                    else
                    {
                        /*En caso de error vamos a devolver la respuesta enviada por el WS de ECDF.*/
                        throw new \Exception($arrayResult['status']);
                    }
                }else
                {
                    throw new \Exception("Error de Conexión con WS ECDF. Intente más tarde");
                }
            }
            catch (\Exception $ex)
            {
                if ($arrayParametros["boolCrearTarea"])
                {
                    $arrayParamTarea     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne('TAREA_SOPORTE_ACTUALIZAR_CORREO_ECDF',
                                                               'TECNICO',
                                                               'TAREA_SOPORTE_ACTUALIZAR_CORREO_ECDF',
                                                               'PARAMETROS PARA LA CREACION DE TAREA CUANDO EXISTE ERROR AL ACTUALIZAR CORREO ECDF',
                                                               "ECDF",'','','','',
                                                               $intIdEmpresa);


                    $strObservacionTarea = "<b>CRS:</b> Pendiente de activación Servicio Adicional ECDF por CRS.";
                    $strObservacionTarea .= "<b>Login Origen:</b> $strLoginOrigen <b>Login Destino:</b> $strLoginDestino";

                    $arrayParametros = array ('intIdPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                        'intIdEmpresa'           => $intIdEmpresa,
                        'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                        'strNombreTarea'         => $arrayParamTarea["valor2"],
                        'strNombreProceso'       => $arrayParamTarea["valor3"],
                        'strObservacionTarea'    => $strObservacionTarea,
                        'strMotivoTarea'         => $strObservacionTarea,
                        'strTipoAsignacion'      => 'empleado',
                        'strIniciarTarea'        => "N",
                        'strTipoTarea'           => 'T',
                        'strTareaRapida'         => 'S',
                        'strFechaHoraSolicitada' => date("Y-m-d").' '.date("H:i:s"),
                        'boolAsignarTarea'       => true,
                        "strAplicacion"          => 'telcoSys',
                        'strUsuarioAsigna'       => $strUsuarioAsigna,
                        'strUserCreacion'        => $strUsrSesion,
                        'strIpCreacion'          => $strIpClient,
                        'intPuntoId'             => $intPuntoId,
                        'strEstadoActual'        => "Finalizada");

                    $arrayCreaTarea = $this->serviceSoporte->crearTareaCasoSoporte($arrayParametros);

                    $this->serviceSoporte->ingresarSeguimientoTarea(array(
                                                                    'idEmpresa'             => $intIdEmpresa,
                                                                    'idDetalle'             => $arrayCreaTarea['numeroDetalle'],
                                                                    'seguimiento'           => "Se finaliza tarea de forma automática.",
                                                                    'usrCreacion'           => $strUsrSesion,
                                                                    'ipCreacion'            => $strIpClient,
                                                                    'strEnviaDepartamento'  => "N"));

                    $strAsunto = $arrayParamTarea["valor4"];
                    $arrayCorreos     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('CORREOS_ACTUALIZAR_WS_ECDF',
                                                            'TECNICO',
                                                            'CORREOS_ACTUALIZAR_WS_ECDF',
                                                            'CORREOS DESTINATARIOS USADOS PARA LA ACTUALIZACION DE CORREO DEL ECDF',
                                                            "ECDF",'','','','',
                                                            $intIdEmpresa);

                    foreach($arrayCorreos as $objCorreo)
                    {
                        $arrayDestinatarios[] = $objCorreo['valor2'];
                    }
                    $arrayParametrosCorreo["empresa"]                 = $strPrefijoEmpresa;
                    $arrayParametrosCorreo["login_cliente"]           = $strLoginDestino;
                    $arrayParametrosCorreo["ws_actualizar_correo"]    = $strUrlFinal;
                    $arrayParametrosCorreo["error_tecnico"]           = $ex->getMessage();
                    $arrayParametrosCorreo["identificacion_cliente"]  = $strIdentificacionCliente;

                    //Se realiza la notificación por correo.
                    $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                        $arrayDestinatarios,
                        $arrayParamTarea["valor5"],
                        $arrayParametrosCorreo,
                        $intIdEmpresa,
                        '',
                        '',
                        null,
                        true,
                        null);
                }

                $arrayRespuesta['mensaje'] = $ex->getMessage();

                $this->serviceUtil->insertError('Telcos+',
                    'FoxPremiumService.actualizarCorreoECDF',
                    $ex->getMessage(),
                    $strUsrSesion,
                    $strIpClient);
            }
        }
        else
        {
            $this->serviceUtil->insertError('Telcos+',
            'FoxPremiumService.actualizarCorreoECDF',
            'No se encuetran configurados los parametros: apikey_ecdf o ws_actualizar_correo_ecdf en parameters.yml',
            $strUsrSesion,
            $strIpClient);
        }

        return $arrayRespuesta;
    }
    /**
     * Documentación para el método 'guardaServProdCaracSinCredenciales'.
     * Guarda Caracteristicas para los servicios que no deben generar crdenciales de acceso
     * @param array $arrayParametros [ 'intIdPersona'      => 'Id de la persona',
     *                                 'intIdPersonaRol'   => 'Id del cliente IdPersonaRol'
     *                                 'intIdServicio'     => 'Id del servicio a Ingresarse',
     *                                 'intIdProducto'     => 'Id del Producto',
     *                                 'strUsrCreacion'    => 'Usuario en sesion']
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 08-08-2022
     *
     */
    public function guardaServProdCaracSinCredenciales($arrayParametros)
    {
        $intIdServicio = $arrayParametros[self::INT_ID_SERVICIO];
        $intIdPtoCliente = $arrayParametros['intIdPuntoCliente'];
        //Trae el Producto y sus caracteristicas
        $arrayProducto = $this->determinarProducto(array('intIdProducto'=>$arrayParametros["intIdProducto"]));
        if ($arrayProducto['Status'] != 'OK')
        {
            $strMensaje = $arrayProducto['Mensaje'];
            throw new \Exception($strMensaje);
        }
        $arrayParametrosCaractFox = array( 'intIdProducto'         => $arrayParametros["intIdProducto"], 
                                           'strDescCaracteristica' => $arrayProducto['strDescCaracteristica'], 
                                           'strEstado'             => self::ESTADO_ACTIVO );
        $strEsProdCarac = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaractFox);
                
        if( !empty($strEsProdCarac) && $strEsProdCarac == "S" )
        {                              
            // Verifico si el cliente ya posee un servicio Fox Premium, Paramount o Noggin 
            // en estado Cancelado que no haya sido migrado
            
            $arrayParamsServCancel   = array(
                                                       "intIdPersonaRol"            => $arrayParametros["intIdPersonaRol"],
                                                       "strNombreTecnico"           => $arrayProducto['strNombreTecnico'],
                                                       "strEstadoServicio"          => array('Cancel'),
                                                       "strDescrCaracteristica"     => $arrayProducto['strMigrar'],
                                                       "strValorCaracteristica"     => 'N',
                                                       "strEstadoCaracServ"         => array('Eliminado','Cancelado'),
                                                       "intIdPuntoCliente"          => $intIdPtoCliente
                                                       );
            $objInfoServicioRepository     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
            $arrayRespuesta                = $objInfoServicioRepository->obtieneServicioIdCancelFoxPremium($arrayParamsServCancel);
            $intIdServicio                 = $arrayRespuesta[0][self::INT_ID_SERVICIO];
            $strExisteServCancel = ( isset($intIdServicio) && !empty($intIdServicio)
                                               && $intIdServicio>0) ? 'S' : 'N';
            //Si el cliente ya posee un Servicio FoxPremium, Paramount y Noggin en estado Cancel se procede a tomar la información 
            //del LOGIN (USUARIO_XX) y SUSCRIBER_ID (SSID_XXX) existente para el nuevo servicio ingresado, por tratarse de una
            // Recontratacion o Reingreso del cliente
            //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA EL FLUJO DE REINGRESO
            $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('FLUJO_DE_REEINGRESO',//nombre parametro cab
                                                    'COMERCIAL', //modulo cab
                                                    'NOMBRE_TECNICO_PROD_TV',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    '18'); //empresa
            foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
            {
            $arrayProdNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
            }
            if( $strExisteServCancel == 'S' && in_array($arrayProducto['strNombreTecnico'],$arrayProdNombreTecnico))             
            {                
                $arrayServicio            = $this->obtieneArrayCaracteristicas(array(self::INT_ID_SERVICIO => $intIdServicio,
                                                                                     'strEstado' => self::ESTADO_ELIMINADO));
                $objServProdCaracSsid        = $arrayServicio[$arrayProducto['strSsid']];
                if(!is_object($objServProdCaracSsid))                             
                {
                    throw new \Exception("No se pudo obtener el SuscriberId para el servicio  ".$arrayProducto['strMensaje']);
                }
                $intSuscriberId       = $objServProdCaracSsid->getValor();
            }
            else
            {
              //Caso contrario debo tomar como SSID_xxx el ID_SERVICIO nuevo.
                $intSuscriberId = $arrayParametros["intIdServicio"];
            }
            //Ingreso Caracteristicas para FOX_PREMIUM, PARAMOUNT O NOGGIN
            $objProductoFoxPremium = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros["intIdProducto"]);
            if(!is_object($objProductoFoxPremium))
            {
                throw new \Exception("No se encontro Producto para el servicio ".$arrayProducto['strMensaje']);
            }
            $objInfoServicio= $this->emcom->getRepository('schemaBundle:InfoServicio')->find($arrayParametros["intIdServicio"]);
            if(!is_object($objInfoServicio))
            {
                throw new \Exception("No se encontro el Servicio para ".$arrayProducto['strMensaje']);
            }
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strSsid'],
                                                                       $intSuscriberId,$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, $arrayProducto['strMigrar'],
                                                                       'N',$arrayParametros["strUsrCreacion"]);
            $this->serviceTecnico->ingresarServicioProductoCaracteristica($objInfoServicio,$objProductoFoxPremium, 
                                                            $arrayProducto['strDescCaracteristica'],'S',$arrayParametros["strUsrCreacion"]);
        }
    }

    /**
     * Función que recibe los parámetros del WS e invoca a la función que tiene la lógica para crear contraseña.
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0
     * @since 22-08-2022
     * 
     */
    public function crearContraseniaDesdeFox($arrayParametros)
    {
        try
        {
            $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
            // La variable $arrayParametros['strProducto'] debe recibir unicamente los valores "fp" para FOXPREMIUM, 
            // "paramountlatam" para PARAMOUNT O "nogginlatam" para NOGGIN
            $strProducto   = $arrayParametros["strProducto"];
            //Se valida el tipo de producto
            $arrayNombreProductoWs  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('NOMBRE_PRODUCTO_WS',//nombre parametro cab
                                            'COMERCIAL', //modulo cab
                                            'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                            'NOMBRE DE PRODUCTO WS', //descripcion det
                                            '','','','','',
                                            '18'); //empresa
            foreach($arrayNombreProductoWs as $arrayProducto)
            {
                //valida si el nombre del producto es el mismo que es registrado
                if($strProducto == $arrayProducto['valor1'])
                {
                    //guarda el nombre tecnico
                    $strNombreTecnico = $arrayProducto['valor2'];
                }
            }
            $arrayProducto = $this->determinarProducto(array('strNombreTecnico'=>$strNombreTecnico));

            if ($arrayProducto['Status'] == 'ERROR')
            {
                throw new \Exception($arrayProducto['Mensaje']);
            }
            $arrayParamsGetIdServicioXUsuario       = array(
                                                    "strNombreTecnicoProd"          => $arrayProducto['strNombreTecnico'],
                                                    "strDescripcionCaract"          => $arrayProducto['strCorreo'],
                                                    "strValorCaract"                => $arrayParametros["strUsuario"],
                                                    "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetIdServicioXUsuario    = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetIdServicioXUsuario);
            if($arrayRespuestaGetIdServicioXUsuario['status'] !== "OK")
            {
                throw new \Exception ("No se ha podido consultar un servicio ligado al usuario proporcionado");
            }
            $arrayRegistrosGetIdServicioXUsuario = $arrayRespuestaGetIdServicioXUsuario["arrayRegistros"];
            if(!isset($arrayRegistrosGetIdServicioXUsuario[0]) || empty($arrayRegistrosGetIdServicioXUsuario[0]))
            {
                throw new \Exception ("No existe un servicio ligado al usuario proporcionado");
            }
            $intIdServicio = $arrayRegistrosGetIdServicioXUsuario[0][self::INT_ID_SERVICIO];

            $strRespuesta = $this->crearRestablecerContrasenia(array(self::INT_ID_SERVICIO  => $intIdServicio,
                                                                "strNombreProducto"    => $strNombreTecnico,
                                                                "strPassword"          => $arrayParametros['strPassword'],
                                                                "strCrearPassword"     => $arrayParametros['strCrearPassword'],
                                                                "strUsuario"           => $arrayParametros['strUsuario'],
                                                                self::STR_EMPRESA_COD  => $arrayParametros[self::STR_EMPRESA_COD],
                                                                self::STR_USR_CREACION => $arrayProducto['strUserCreacionTelcos'],
                                                                self::STR_CLIENT_IP    => $arrayParametros[self::STR_CLIENT_IP]));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
            return json_encode(array("status" => $strRespuesta, "message" => "Transacción realizada correctamente"));
        }
        catch(\Exception $ex)
        {
            return json_encode(array("status"=> "ERROR", "message" => $ex->getMessage()));
        }
    }

    /**
     * Función que crea la contraseña en base a un servicio.
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0
     * @since 22-08-2022
     * 
     */
    public function crearRestablecerContrasenia($arrayParametros)
    {
        $strMensaje = self::OK;
        $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
        $strCorreo                  = $arrayParametros['strUsuario'];
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $intIdServicio       = $arrayParametros[self::INT_ID_SERVICIO];
            $arrayProducto       = $this->determinarProducto(array('strNombreTecnico'=>$arrayParametros['strNombreProducto']));
            if ($arrayProducto['Status'] != 'OK')
            {
                $strMensaje      = $arrayProducto['Mensaje'];
                throw new \Exception($strMensaje);
            }
            // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
            $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
            'COMERCIAL', //modulo cab
            'OBTENER_NOMBRE_TECNICO',//proceso cab
            'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
            $arrayProducto['strNombreTecnico'],'','','','', $arrayParametros[self::STR_EMPRESA_COD]);

            if(!is_array($objProdGenCred) || empty($objProdGenCred))
            {
                throw new \Exception ("El producto seleccionado, no permite realizar esta transacción.");
            }
            $objInfoServicio     = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                               ->findOneById($intIdServicio);
            //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA FLUJO DE CANCELACIÓN
            $arrayNombreTecnicoPermitido = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PERMITIR_RESTABLECER_PASS',//nombre parametro cab
                                                    'TECNICO', //modulo cab
                                                    'OBTENER_PROD_TV',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    $arrayParametros[self::STR_EMPRESA_COD]); //empresa
            foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
            {
                $arrayProdPermitido[]   =   $arrayNombreTecnico['valor1'];
            }
            if(is_object($objInfoServicio) && $objInfoServicio->getEstado() == self::ESTADO_CANCEL && 
               in_array($arrayProducto['strNombreTecnico'],$arrayProdPermitido))
            {
                $arrayParamsGetSsidXIdServicio      = array(
                                                            "intIdServicio"                 => $intIdServicio,
                                                            "strDescripcionCaract"          => $arrayProducto['strSsid'],
                                                            "strEstadoSpcEstaParametrizado" => "SI");
                $arrayRespuestaGetSsidXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetSsidXIdServicio);
                if($arrayRespuestaGetSsidXIdServicio['status'] !== "OK")
                {
                    throw new \Exception ("No se pudo obtener el SuscriberId del servicio");
                }
                $arrayRegistrosGetSsidXIdServicio = $arrayRespuestaGetSsidXIdServicio["arrayRegistros"];
                $intIdSpcUsuario = $arrayRegistrosGetSsidXIdServicio[0]["intIdSpc"];

                $arrayParametrosAuthorization   = array(
                                                            'country_code'      =>'EC',
                                                            'intIdSpcSuscriber' =>$intIdSpcUsuario,
                                                            'strSsid'           => $arrayProducto['strSsid']
                                                       );
                $arrayRespuesta  =   $this->serviceAuthorizationFox->autorizarServicio($arrayParametrosAuthorization);
                if ($arrayRespuesta['strCodigoSalida'] != 'OK')
                {
                    throw new \Exception($arrayRespuesta['strMensajeSalida']);
                }
            }
            else if(is_object($objInfoServicio) && $objInfoServicio->getEstado() != self::ESTADO_ACTIVO)
            {
                throw new \Exception("No es posible reiniciar la contraseña del usuario debido a que el"
                            . " servicio no se encuentra en estado Activo.");
            }

            $intProdCaractContraseniaId    = null;

            $arrayParamsGetPasswordXIdServicio      = array(
                                                        "intIdServicio"                 => $intIdServicio,
                                                        "strDescripcionCaract"          => $arrayProducto['strPass'],
                                                        "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetPasswordXIdServicio   = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                ->obtieneInfoSpcProductosTv($arrayParamsGetPasswordXIdServicio);
            $arrayRegistrosGetPasswordXIdServicio = $arrayRespuestaGetPasswordXIdServicio["arrayRegistros"];
            if($arrayParametros['strCrearPassword'] === "NO" && 
              ($arrayRespuestaGetPasswordXIdServicio['status'] !== "OK" || 
              !isset($arrayRegistrosGetPasswordXIdServicio[0]) || 
              empty($arrayRegistrosGetPasswordXIdServicio[0])))
            {
                throw new \Exception ("No se pudo obtener la contrasenia del servicio");
            }

            $arrayParamsGetUsuarioXIdServicio      = array(
                            "intIdServicio"                 => $intIdServicio,
                            "strDescripcionCaract"          => $arrayProducto['strCorreo'],
                            "strEstadoSpcEstaParametrizado" => "SI");

            $arrayRespuestaGetUsuarioXIdServicio   = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY)
                                ->obtieneInfoSpcProductosTv($arrayParamsGetUsuarioXIdServicio);
            if($arrayRespuestaGetUsuarioXIdServicio['status'] !== "OK")
            {
                throw new \Exception ("No se pudo obtener el usuario del servicio");
            }

            $arrayRegistrosGetUsuarioXIdServicio = $arrayRespuestaGetUsuarioXIdServicio["arrayRegistros"];
            $intIdSpcUsuario = $arrayRegistrosGetUsuarioXIdServicio[0]["intIdSpc"];
            $objServProdCaracUsuario = $this->emcom->getRepository("schemaBundle:InfoServicioProdCaract")->find($intIdSpcUsuario);

            // SE ENCRIPTA LA NUEVA CONTRASEÑA
            $strNuevaContrasenia         = $this->serviceCrypt->encriptar($arrayParametros['strPassword']);
            if(isset($arrayRegistrosGetPasswordXIdServicio[0]) && !empty($arrayRegistrosGetPasswordXIdServicio[0]))
            {
                $intIdSpcPassword = $arrayRegistrosGetPasswordXIdServicio[0]["intIdSpc"];
                $objServProdCaracContrasenia = $this->emcom->getRepository("schemaBundle:InfoServicioProdCaract")->find($intIdSpcPassword);
            }

            //Actualizo la característica correspondiente a la contraseña
            if (is_object($objServProdCaracContrasenia))
            {
                $objServProdCaracContrasenia->setEstado(self::ESTADO_ELIMINADO);
                $objServProdCaracContrasenia->setFeUltMod(new \DateTime(self::NOW));
                $objServProdCaracContrasenia->setUsrUltMod($arrayParametros[self::STR_USR_CREACION]);
    
                $this->emcom->persist($objServProdCaracContrasenia);
                $this->emcom->flush();
                $intProdCaractContraseniaId    = $objServProdCaracContrasenia->getProductoCaracterisiticaId();
            }
            else
            {
                //Si no tenemos la característica la buscamos
                $objAdmiCaracteristica      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("estado"                    => self::ESTADO_ACTIVO,
                                                                        "descripcionCaracteristica" => $arrayProducto['strPass']));
                $intProdCaractContraseniaId = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                      ->findOneBy(array("productoId"       => $objInfoServicio->getProductoId(),
                                                                        "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                        "estado"           => self::ESTADO_ACTIVO))
                                                      ->getId();
            }

            //Se crea la nueva característica con la nueva contraseña
            $objInfoServicioProdCaract = new \telconet\schemaBundle\Entity\InfoServicioProdCaract();
            $objInfoServicioProdCaract->setServicioId($intIdServicio);
            $objInfoServicioProdCaract->setValor($strNuevaContrasenia);
            $objInfoServicioProdCaract->setEstado(self::ESTADO_ACTIVO);
            $objInfoServicioProdCaract->setProductoCaracterisiticaId($intProdCaractContraseniaId);
            $objInfoServicioProdCaract->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
            $objInfoServicioProdCaract->setFeCreacion(new \DateTime(self::NOW));
            $this->emcom->persist($objInfoServicioProdCaract);
            $this->emcom->flush();
            if(!isset($arrayProducto['strUrlProducto']) || empty($arrayProducto['strUrlProducto']))
            {
                throw new \Exception ("No existe URL del producto para el botón DESCUBRE MAS");
            }
            $arrayParametrosCorreo = array("url"     => $arrayProducto['strUrlProducto']);

            //Se actualiza el Historial del Servicio para llevar el control del reinicio de contraseña
            $objInfoServicioHistorial = new \telconet\schemaBundle\Entity\InfoServicioHistorial();
            $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
            $objInfoServicioHistorial->setFeCreacion(new \DateTime(self::NOW));
            $objInfoServicioHistorial->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
            $objInfoServicioHistorial->setObservacion("Se creó nueva contraseña del producto: <b>".$arrayProducto['strNombreTecnico']."</b>");
            $objInfoServicioHistorial->setServicioId($objInfoServicio);
            $objInfoServicioHistorial->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);

            $strAsuntoCorreo        = $arrayProducto['strAsuntoConfActivacion'];
            $strCodPlantillaCorreo  = $arrayProducto['strCodPlantConfActivacion'];
            if($arrayParametros['strCrearPassword'] === "NO")
            {
                $objInfoServicioHistorial->setObservacion("Se cambió la contraseña del producto: <b>".$arrayProducto['strNombreTecnico']."</b>");
                // VALIDAR EL TIPO DE PLANTILLA Y ASUNTO
                $strAsuntoCorreo        = $arrayProducto['strAsuntoConfRest'];
                $strCodPlantillaCorreo  = $arrayProducto['strCodPlantConfRest'];
            }
            $this->emcom->persist($objInfoServicioHistorial);
            $this->emcom->flush();

            //Notifico al cliente por Correo y SMS
            $this->notificaCorreoServicioFox(
                    array("strDescripcionAsunto"   => $strAsuntoCorreo,
                          "strCodigoPlantilla"     => $strCodPlantillaCorreo,
                          self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                          "intPuntoId"             => $objInfoServicio->getPuntoId()->getId(),
                          "intIdServicio"          => $objInfoServicio->getId(),
                          "strNombreTecnico"       => $arrayProducto['strNombreTecnico'],
                          "intPersonaEmpresaRolId" => $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                          "arrayParametros"        => $arrayParametrosCorreo,
                          "strCorreoDest"          => $strCorreo,
                         )
                   );

            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = $ex->getMessage();
        }
        $this->serviceUtil->insertError('Telcos+',
                                          'FoxPremiumService.crearRestablecerContrasenia',
                                          $strMensaje,
                                          $arrayParametros[self::STR_USR_CREACION],
                                          $arrayParametros[self::STR_CLIENT_IP]);
        return $strMensaje;
    }
        /**
     * Documentación para el método 'obtenerUrlActivarServicio'.
     *
     * Función que sirve para consumir el WS de security y obtener token para generar url de activación
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 24-09-2022
     *
     * @param Array $arrayParametros [
     *          'strUsrCreacion'
     *          'arrayCaracteristicas'
     *          'strEmpresaCod'
     *          'strNombreTecnico'
     *          'strCrearPassword'
     *      ]
     *
     * @return string[] $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *          'url'                 - url para acceder al servicio
     *      ]
     */
    public function obtenerUrlActivarServicio($arrayParametros)
    {
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
        $strEmpresaCod              = $arrayParametros['strEmpresaCod'];
        $strNombreTecnico           = $arrayParametros['strNombreTecnico'];
        $strCrearPassword           = $arrayParametros['strCrearPassword'];

        $arrayRespuesta = array(
            "status" => "ERROR",
            "mensaje" => "Ha ocurrido un error, por favor notificar a Sistemas."
        );
        // OBTENER TOKEN PARA GENERAR URL
        $strUrlCrearContra = $this->objContainer->getParameter('url_crear_password_security');
        $strUrlRestablecerContra = $this->objContainer->getParameter('url_restablecer_password_security');
        if (!empty($this->msTokenSecurity) || !empty($this->urlMsSecurity) 
        || !empty($strUrlCrearContra) || !empty($strUrlRestablecerContra))
        {
            try
            {
                // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
                $objProdGenCred = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                  ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                  'COMERCIAL', //modulo cab
                  'OBTENER_NOMBRE_TECNICO',//proceso cab
                  'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                  $strNombreTecnico,'','','','', $strEmpresaCod);

                if(is_array($objProdGenCred) && !empty($objProdGenCred))
                {
                    // OBTENER NOMBRES WS
                    $arrayNombreProductoWs  = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NOMBRE_PRODUCTO_WS',//nombre parametro cab
                                                        'COMERCIAL', //modulo cab
                                                        'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                                        'NOMBRE DE PRODUCTO WS', //descripcion det
                                                        '',$strNombreTecnico,'','','',
                                                        $strEmpresaCod); //empresa
                    if(is_array($arrayNombreProductoWs) && !empty($arrayNombreProductoWs))
                    {
                        $arrayDataWS["issuer"]       = $arrayNombreProductoWs[0]["valor1"];
                    }
                    $strCorreoProd  =   '';
                    if(isset($arrayParametros['arrayCaracteristicas']))
                    {
                        foreach($arrayParametros['arrayCaracteristicas'] as $arrayCaract)
                        {
                            if($arrayCaract['caracteristica']=='CORREO ELECTRONICO')
                            {
                                $strCorreoProd  =  $arrayCaract['valor'];
                            }
                        }
                    }
                    $objHeaders[CURLOPT_HTTPHEADER]  = array("Accept: application/json", "token: ". $this->msTokenSecurity);
                    $arrayDataWS["creationUser"]     = $strUsrCreacion;
                    $arrayDataWS["subject"]          = $strCorreoProd;
                    $strJsonData                     = json_encode($arrayDataWS, true);

                    $arrayRespJsonWS                 = $this->restClient->postJSON($this->urlMsSecurity, $strJsonData, $objHeaders);
                    $arrayResponse                   = json_decode($arrayRespJsonWS['result'],true);
                    if(isset($arrayResponse['status']) && $arrayResponse['status'] == self::OK && $arrayResponse['code'] == 200)
                    {
                        $strToken     = $arrayResponse['data']["token"];
                        $strUrlFinal  = $strUrlCrearContra.$strToken;
                        if($strCrearPassword === "NO")
                        {
                            $strUrlFinal  = $strUrlRestablecerContra.$strToken;
                        }
                        $arrayRespuesta['status']     = "OK";
                        $arrayRespuesta['mensaje']    = "OK";
                        $arrayRespuesta['url']        = $strUrlFinal;
                    }
                    else 
                    {
                        throw new \Exception("Error de comunicación con el WS de generar TOKEN. ".$arrayResponse['message']);
                    }
                }
                else 
                {
                    $arrayRespuesta = array(
                      "status" => "OK"
                    );
                }
            }
            catch (\Exception $ex)
            {
                $arrayRespuesta['mensaje'] = $ex->getMessage();
            }
        }
        else
        {
            $arrayRespuesta['mensaje'] = 'No se encuetran configurados los parametros: ms_token_security, ws_ms_generar_token_acceso".
            " o url_restablecer_password_security, url_crear_password_security en parameters.yml';
        }
        return $arrayRespuesta;
    }
    /**
     * Documentación para el método 'activarServicioSinCredenciales'.
     * Función que activa los productos de streaming que no generan credenciales.
     *  
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 25-08-2022
     * 
     * @param arrayParametros  =>   $arrayParametros["intIdServicio"]           = Id del servicio
     *                              $arrayParametros['strUsrCreacion']          = string del usuario
     *                              $arrayParametros['strClientIp']             = string de IP del cliente
     *                              $arrayParametros['strEmpresaCod']           = Codigo de la empresa
     */
    public function activarServicioSinCredenciales($arrayParametros)
    {
        $strMensaje = self::OK;
        $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $intIdServicio            = $arrayParametros[self::INT_ID_SERVICIO];
            $objServicio              = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto              = $objServicio->getProductoId();
            $arrayParam               = array('intIdServicio' =>  $intIdServicio, 'strEstado' => self::ESTADO_ACTIVO);

            //servicio
            $objServicio->setEstado("Activo");
            $this->emcom->persist($objServicio);
            
            $arrayCaracteristica = $this->obtieneArrayCaracteristicas($arrayParam);
            $arrayProducto       = $this->determinarProducto(array('intIdServicio'=>$intIdServicio));
            $strNombreTecnico    = $arrayProducto["strNombreTecnico"];
            $objCorreoElectronico         = $arrayCaracteristica["CORREO ELECTRONICO"];
            $arrayCaracteristicaCorreo[]  = array('caracteristica' => 'CORREO ELECTRONICO', 
                                                  'valor' => $objCorreoElectronico->getValor());
            $arrayParamsGetIdServicioXUsuario       = array(
                                                        "strNombreTecnicoProd"          => $strNombreTecnico,
                                                        "strDescripcionCaract"          => $arrayProducto['strCorreo'],
                                                        "strValorCaract"                => $objCorreoElectronico->getValor(),
                                                        "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetIdServicioXUsuario    = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetIdServicioXUsuario);
            if($arrayRespuestaGetIdServicioXUsuario['status'] !== "OK")
            {
                throw new \Exception ("No se ha podido consultar un servicio ligado al usuario proporcionado");
            }
            $arrayRegistrosGetIdServicioXUsuario = $arrayRespuestaGetIdServicioXUsuario["arrayRegistros"];
            if(!isset($arrayRegistrosGetIdServicioXUsuario[0]) || empty($arrayRegistrosGetIdServicioXUsuario[0]))
            {
                throw new \Exception ("No existe un servicio ligado al usuario proporcionado");
            }

            // OBTENER URL DE ACTIVACION
            $arrayParametrosUrlToken = array('strUsrCreacion'          => $arrayParametros['strUsrCreacion'],
                                            'arrayCaracteristicas'   => $arrayCaracteristicaCorreo,
                                            'strNombreTecnico'       => $strNombreTecnico,
                                            'strCrearPassword'       => "SI",
                                            'strEmpresaCod'          => $arrayParametros['strEmpresaCod']);
            $arrayUrlToken = $this->obtenerUrlActivarServicio($arrayParametrosUrlToken);
            if ($arrayUrlToken["status"] !== "OK") 
            {
                throw new \Exception($arrayUrlToken["mensaje"]);
            }
            $objInfoServicioConfirmarHistorial    = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $intIdServicio,
                                                                          'accion'     => 'confirmarServicio',
                                                                          'estado'     => 'Pendiente')
                                                                        );
            /*Validamos que existe el objeto, y registramos los elementos del historial.*/
            // Validamos si existe le objeto.
            if (!is_object($objInfoServicioConfirmarHistorial)) 
            {
                /*Crear los nuevos registros de la fecha.*/
                $entityServicioHistorialConf = new InfoServicioHistorial();
                $entityServicioHistorialConf->setServicioId($objServicio);
                $entityServicioHistorialConf->setFeCreacion(new \DateTime('now'));
                $entityServicioHistorialConf->setUsrCreacion($arrayParametros[self::STR_USR_CREACION]);
                $entityServicioHistorialConf->setEstado('Activo');
                $entityServicioHistorialConf->setAccion('confirmarServicio');
                $entityServicioHistorialConf->setIpCreacion($arrayParametros[self::STR_CLIENT_IP]);
                $entityServicioHistorialConf->setObservacion('Otros: Se confirmo el servicio');
                $this->emcom->persist($entityServicioHistorialConf);
                $this->emcom->flush();
            }else
            {
                $objInfoServicioConfirmarHistorial->setEstado("Activo");
                $this->emcom->flush();
            }
            $this->activarServicio(array(   "strUsrCreacion"        => $arrayParametros[self::STR_USR_CREACION],
                                            "strClientIp"           => $arrayParametros[self::STR_CLIENT_IP],
                                            "strEmpresaCod"         => $arrayParametros[self::STR_EMPRESA_COD],
                                            "intIdServicio"         => $intIdServicio,
                                            "intIdProducto"         => $objProducto->getId(),
                                            'arrayCaracteristicas'  => $arrayCaracteristicaCorreo,
                                            'strUrlProducto'        => $arrayUrlToken["url"]));

            $this->emcom->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $strMensaje = $ex->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'FoxPremiumService.activarServicioSinCredenciales',
                                            'Error FoxPremiumService.activarServicioSinCredenciales:'.$ex->getMessage(),
                                            $arrayParametros[self::STR_USR_CREACION],
                                            $arrayParametros[self::STR_CLIENT_IP]);
        }
        return $strMensaje;
    }
    /**
     * Documentación para el método 'reenviarCorreoPassword'.
     * Función que envia un correo con las indicaciones para crear contraseña de productos de streaming.
     *  
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 10-09-2022
     * 
     * @param arrayParametros  =>   $arrayParametros["intIdServicio"]           = Id del servicio
     *                              $arrayParametros['strUsrCreacion']          = string del usuario
     *                              $arrayParametros['strClientIp']             = string de IP del cliente
     *                              $arrayParametros['strEmpresaCod']           = Codigo de la empresa
     */
    public function reenviarCorreoPassword($arrayParametros)
    {
        $strMensaje = self::OK;
        $objInfoServicioRepository  = $this->emcom->getRepository(self::INFO_SERVICIO_REPOSITORY);
        try
        {
            $intIdServicio            = $arrayParametros[self::INT_ID_SERVICIO];
            $objServicio              = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto              = $objServicio->getProductoId();
            $arrayParam               = array('intIdServicio' =>  $intIdServicio, 'strEstado' => self::ESTADO_ACTIVO);
            
            $arrayCaracteristica = $this->obtieneArrayCaracteristicas($arrayParam);
            $arrayProducto       = $this->determinarProducto(array('intIdServicio'=>$intIdServicio));
            $strNombreTecnico    = $arrayProducto["strNombreTecnico"];
            $objCorreoElectronico         = $arrayCaracteristica["CORREO ELECTRONICO"];
            $arrayCaracteristicaCorreo[]  = array('caracteristica' => 'CORREO ELECTRONICO', 
                                                  'valor' => $objCorreoElectronico->getValor());
            $arrayParamsGetIdServicioXUsuario       = array(
                                                        "strNombreTecnicoProd"          => $strNombreTecnico,
                                                        "strDescripcionCaract"          => $arrayProducto['strCorreo'],
                                                        "strValorCaract"                => $objCorreoElectronico->getValor(),
                                                        "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetIdServicioXUsuario    = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetIdServicioXUsuario);
            if($arrayRespuestaGetIdServicioXUsuario['status'] !== "OK")
            {
                throw new \Exception ("No se ha podido consultar un servicio ligado al usuario proporcionado");
            }
            $arrayRegistrosGetIdServicioXUsuario = $arrayRespuestaGetIdServicioXUsuario["arrayRegistros"];
            if(!isset($arrayRegistrosGetIdServicioXUsuario[0]) || empty($arrayRegistrosGetIdServicioXUsuario[0]))
            {
                throw new \Exception ("No existe un servicio ligado al usuario proporcionado");
            }
            $arrayParamsGetPasswordXIdServicio      = array(
                                                          "intIdServicio"                 => $intIdServicio,
                                                          "strDescripcionCaract"          => $arrayProducto['strPass'],
                                                          "strEstadoSpcEstaParametrizado" => "SI");
            $arrayRespuestaGetPasswordXIdServicio   = $objInfoServicioRepository->obtieneInfoSpcProductosTv($arrayParamsGetPasswordXIdServicio);
            $arrayRegistrosGetPasswordXIdServicio = $arrayRespuestaGetPasswordXIdServicio["arrayRegistros"];
            if($arrayRespuestaGetPasswordXIdServicio['status'] === "OK" &&
              isset($arrayRegistrosGetPasswordXIdServicio[0]) && !empty($arrayRegistrosGetPasswordXIdServicio[0]))
            {
                throw new \Exception ("El cliente ya tiene registrada una contraseña para el servicio");
            }
            // OBTENER URL DE ACTIVACION
            $arrayParametrosUrlToken = array('strUsrCreacion'          => $arrayParametros['strUsrCreacion'],
                                            'arrayCaracteristicas'   => $arrayCaracteristicaCorreo,
                                            'strNombreTecnico'       => $strNombreTecnico,
                                            'strCrearPassword'       => "SI",
                                            'strEmpresaCod'          => $arrayParametros['strEmpresaCod']);
            $arrayUrlToken = $this->obtenerUrlActivarServicio($arrayParametrosUrlToken);
            if ($arrayUrlToken["status"] !== "OK") 
            {
                throw new \Exception($arrayUrlToken["mensaje"]);
            }
            $arrayParamHistorial         = array('strUsrCreacion'  => $arrayParametros[self::STR_USR_CREACION], 
                                                'strClientIp'     => $arrayParametros[self::STR_CLIENT_IP], 
                                                'objInfoServicio' => $objServicio,
                                                'strTipoAccion'   => $arrayProducto['strAccionReenvio'],
                                                'strMensaje'      => $arrayProducto['strMensaje']);
            $strCliente = trim($objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getInformacionPersona());
            $arrayParametrosCorreo  = array("usuario"  => $objCorreoElectronico->getValor(), "cliente" => $strCliente, 
                                            "url" => $arrayUrlToken["url"]);
            $strMensajeSMS          = str_replace("{{CORREO}}", $objCorreoElectronico->getValor(),  $arrayProducto['strSmsNuevo']);
            //Notifico al cliente por Correo y SMS
            $this->notificaCorreoServicioFox(
              array("strDescripcionAsunto"   => $arrayProducto['strAsuntoNuevo'],
                    "strCodigoPlantilla"     => $arrayProducto['strCodPlantNuevo'],
                    self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                    "intPuntoId"             => $objServicio->getPuntoId()->getId(),
                    "intIdServicio"          => $objServicio->getId(),
                    "strCorreoDest"          => $objCorreoElectronico->getValor(),
                    "strNombreTecnico"       => $strNombreTecnico,
                    "intPersonaEmpresaRolId" => $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                    "arrayParametros"        => $arrayParametrosCorreo,
                    "arrayParamHistorial"    => $arrayParamHistorial
                  )
            );
            $this->notificaSMSServicioFox(
                      array("strMensaje"             => $strMensajeSMS,
                            "strTipoEvento"          => "enviar_infobip",
                            self::STR_EMPRESA_COD    => $arrayParametros[self::STR_EMPRESA_COD],
                            "intPuntoId"             => $objServicio->getPuntoId()->getId(),
                            "intPersonaEmpresaRolId" => $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                            "arrayParamHistorial"    => $arrayParamHistorial,
                            "strNombreTecnico"       => $strNombreTecnico
                          )
            );
        }
        catch (\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'FoxPremiumService.reenviarCorreoPassword',
                                            'Error FoxPremiumService.reenviarCorreoPassword:'.$ex->getMessage(),
                                            $arrayParametros[self::STR_USR_CREACION],
                                            $arrayParametros[self::STR_CLIENT_IP]);
        }
        return $strMensaje;
    }
}
