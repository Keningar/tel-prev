<?php

namespace telconet\seguridadBundle\Service;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

class SeguridadService 
{
    // ==========================================
    // #        VARIABLES NECESARIAS PARA
    //          EL CONSUMO DEL SERVICIO LDAP    #
    // ==========================================
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    private $urlWebServiceAuth;

    private $urlWebServiceAuthNueva;
    private $banderaWebServiceAuth;
    /**
     * Código de respuesta: Respuesta valida
     */
    private static $strStatusOK = 200;
    private static $strStatusERROR = 500;

    const ID_CLIENTE       = 1;
    const ID_ADMINISTRADOR = 2;
    // ===========================================


     // ==========================================
    // #        INJECCION DE SERVICES           #
    // ==========================================
    /* @var $serviceSms SMSService */
    private $serviceSms;
    
    /* @var $serviceExtranet ExtranetService */
    private $serviceExtranet;
    
    /* @var $utilService UtilService */
    private $utilService;
    
    /* @var $serviceEnvioPlantilla EnvioPlantillaService */
    private $serviceEnvioPlantilla;
    /* ==========================================*/
    //             INYECCIÓN DE TEMPLATES
    private $templating;       
    // ==========================================
    // #       INJECCION DE ENTITY MANAGER      # 
    // ==========================================
    /* @var \Doctrine\ORM\EntityManager */
    private $emComercial;

    
    /* @var \Doctrine\ORM\EntityManager */
    private $emSeguridad;

    /* @var \Doctrine\ORM\EntityManager */
    private $emSoporte;
    // ==========================================
    // #          Variables Globales            # 
    // ==========================================
    
    /**
     * Tiempo maximo de validez de un pin
     */
    private $intTiempoMaxValidezPin;

    /**
     *
     * @var string
     */
    private $strUrlWebLDAP;

    /**
     *
     * @var string
     */
    private $strUrlLDAPTN;

    /**
     * Funcion que sirve para setear las dependenecias del service
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 22-12-2015
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 09-09-2018 - Se adiciona el string que contiene la url del gateway LDAP.
     * 
     * Se elimina el parámetro "strEnvioInfobip", dicho parámetro se envía a Base.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.1 04-12-2019
     * 
     * Se agregan nuevos parametros para consumo de LDAP Movil.
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 28-09-2022
     *
     * @param ContainerInterface $container -- Contenedor de interface
     * 
     */    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->emComercial            = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emSeguridad            = $container->get('doctrine.orm.telconet_seguridad_entity_manager');
        $this->emSoporte              = $container->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->serviceSms             = $container->get('comunicaciones.SMS');
        $this->serviceExtranet        = $container->get('seguridad.Extranet');
        $this->intTiempoMaxValidezPin = $container->getParameter('ws_contrato_digital_max_time');
        $this->restClient             = $container->get('schema.RestClient');
        $this->urlWebServiceAuth      = $container->getParameter('url_web_service_auth');
        $this->strUrlWebLDAP          = $container->getParameter('tecnico.ws_ldap_url');
        $this->strUrlLDAPTN           = $container->getParameter('seguridad.ws_tn_ldap');
        $this->utilService            = $container->get('schema.Util');
        $this->serviceEnvioPlantilla  = $container->get('soporte.EnvioPlantilla');
        $this->templating             = $container->get('templating');
        $this->urlWebServiceAuthNueva = $container->getParameter('url_web_service_auth_nueva');
        $this->banderaWebServiceAuth  = $container->getParameter('bandera_web_service_auth');
    }

    /**
     * Funcion que sirve para obtener InfoPersonaEmpresaRolCaractiristica de un usuario con tipo LOGIN
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 22-12-2015
     * 
     * @param string $strUsuario         -- User Name del Cliente Netlife para ello debera tener un Carateristica LOGIN en InfoPersonaEmpresaRol
     * @param string strCodEmpresa      -- codigo de la empresa
     * 
     * @return object $objData          -- Valores que se retornan con la informacion del usuario de tipo Login
     */
    public function obtenerIPERCaracLogin($strUsuario, $strCodEmpresa) 
    {

        $objData = null;
        
        // Obtenemos la admi_caracteristica [LOGIN]
        $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $objAdmiCaractLogin    = $objAdmiCaracteristica->findOneBy(array('descripcionCaracteristica' => 'LOGIN',
                                                                         'tipo'                      => 'SEGURIDAD',
                                                                         'estado'                    => 'Activo'));

        if (is_object($objAdmiCaractLogin)) 
        {
            $objInfoPersonaEmpresaRolCaracRepo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
            $objInfoLoginCaracteristica        = $objInfoPersonaEmpresaRolCaracRepo
                                                 ->findCaracteristicaPorCriterios(array('empresaCod'       => $strCodEmpresa,
                                                                                        'caracteristicaId' => $objAdmiCaractLogin->getId(),
                                                                                        'valor'            => $strUsuario,
                                                                                        'estado'           => 'Activo',
                                                                                        'intStart'         => 0,
                                                                                        'intLimit'         => 1));
            $objData = $objInfoLoginCaracteristica;
        }
        return $objData;
    }

    /**
     * Valida credenciales ingresadas para el acceso de los usuarios externos (clientes de telcos)
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 05-01-2015
     * 
     * @param string $strUsuario
     * @param string $strContrasena
     * @param string $strEmpresa
     * @param string $arrayDatosTlf
     * 
     * @return array con datos del cliente si se encuentra, null caso contrario
     */
    public function loginUsuarioExterno($strUsuario, $srtContrasena, $strEmpresa, $arrayDatosTlf) 
    {
        
        $arrayEntity               = null;
        $arrayEntity["status"]     = "ERROR_SERVICE";
        $arrayPerfilesAsignados    = array();
        // Obtenemos la admi_caracteristica [PASS]
        $objAdmiCaracteristicaRepo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $objAdmiCaractPass         = $objAdmiCaracteristicaRepo->findOneBy(array('descripcionCaracteristica'  => 'PASS',
                                                                                 'tipo'                       => 'SEGURIDAD',
                                                                                 'estado'                     => 'Activo'));

        // verificamos que exista un login para la empresa indicada
        $strLoginExtranet             = $this->obtenerIPERCaracLogin($strUsuario, $strEmpresa);
        if ($strLoginExtranet && is_object($objAdmiCaractPass)) 
        {
            
            //sabiendo el $rolCarac padre, verificamos el password recibido
            $objPersonaEmpresaRolCaracRepo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
            $objRolCaracPass               = $objPersonaEmpresaRolCaracRepo
                                             ->findOneBy(array('valor'                      => $srtContrasena,
                                                               'caracteristicaId'           => $objAdmiCaractPass->getId(),
                                                               'personaEmpresaRolCaracId'   => $strLoginExtranet->getId(),
                                                               'estado'                     => 'Activo'));

            if (is_object($objRolCaracPass)) 
            {
                // Existe el password
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol');
                $objUsuarioLogin          = $objInfoPersonaEmpresaRol->findOneBy(array('id' => $strLoginExtranet->getPersonaEmpresaRolId()));

                if (is_object($objUsuarioLogin)) 
                {
                    //Verificamos que el telefono esta registrado para el usuario
                    $arrayRespuestaVerificarDispositivo = $this->serviceExtranet->verificarDispositivo($strUsuario, $strEmpresa, $arrayDatosTlf);
                    if ($arrayRespuestaVerificarDispositivo['status'] == "OK") 
                    {
                        $arrayEntity["status"]                 = "OK";
                        $arrayEntity["mensaje"]                = "CONSULTA EXITOSA";
                        $arrayEntity["id_persona_empresa_rol"] = $objUsuarioLogin->getId();
                        $arrayEntity["cambioClave"]            = $objUsuarioLogin->getCambioContrasena();
                       
                        $arrayPerfiles                         = $this->emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                                                      ->findByPersonaId($objUsuarioLogin->getPersonaId()->getId());
                                      
                        foreach ($arrayPerfiles as $objPerfil) 
                        {
                            $arrayPerfilesAsignados[] = $objPerfil->getPerfilId()->getNombrePerfil();
                        }
                        $arrayEntity["perfiles"] = $arrayPerfilesAsignados;
                    } 
                    else 
                    {
                        $arrayEntity["mensaje"] = "Dispositivo no asociado";
                    }
                }
            } 
            else 
            {
                $arrayEntity["mensaje"]     = "Login Incorrecto.";
                $arrayEntity["cambioClave"] = "";
                $arrayEntity["perfiles"]    = $arrayPerfilesAsignados;
            }
        } 
        else 
        {
            $arrayEntity["mensaje"]     = "ERROR al obtener el login";
            $arrayEntity["cambioClave"] = "";
            $arrayEntity["perfiles"]    = "";
        }

        return $arrayEntity;
    }

    //##############################################################
    //###   METODOS DE GENERACION Y VALIDACION DE PIN SECURITY   ###
    //##############################################################

    /**
     * 
     * Actualización: Se inicializa arrayData con valores por defecto
     * Luego de enviar SMS en el switch por el caso de default se obtiene mensaje desde $arrayResponseSMS['detail']
     * Tambien se modifica smsBox por smsbox para corregir envio de sms
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 07-03-2017
     * 
     * Funcion que sirve para Generar Pin Security,
     * Esta funcionalidad genera un numero de 6 caracteres y lo envia por SMS al numero telefonico
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 03-12-2015
     * 
     * Se añadio la asociacion del numero de telefono al pin en la tabla InfoPersonaEmpresaRolCarac
     * @author Robinson Salgado <rsalgado85@telconet.ec>
     * @version 1.2 16-08-2017
     * 
     * Se modifico para que el envio de sms se haga por el consumo de WS de API SMS
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 08-07-2018
     * 
     * Se quita el envio de sms para que solo se envie el pin por mail
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 13/09/2018
     * 
     * Se modifica para que según parámetro el sms se envíe por INFOBIP o MASSEND
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 30/05/2019
     * 
     * Se modifica la lógica para que solo se proceda con la generación del pin, el envío se realiza desde otra función.
     *  También se ajusta los parámetros de entrada.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.6 03/12/2019
     * 
     * @param string $arrayData   -- Valores que se retornan
                                    * $arrayData['strUsername']        Usuario que realiza la transacción.
                                    * $arrayData['strCodEmpresa']      Código de la empresa a la cual pertenece el usuario.
     * 
     * @return array $arrayData  -- Valores que se retornan
     *                              * $arrayResponse['pin']      ->  Pin Security Generado para el cliente
     *                              * $arrayResponse['status']   ->  Estado de la peticion.
     *                              * $arrayResponse['mensaje']  ->  Mensaje del proceso de la peticion.
     */
    public function generarPinSecurity($arrayData) 
    {

        $arrayResponse            = array();
        $arrayResponse['status']  = 'ERROR_SERVICE';
        $arrayResponse['mensaje'] = 'OCURRIÓ UN PROBLEMA INESPERADO AL GENERAR EL PIN';
        //======================================================================
        //Generacion de PIN
        //______________________________________________________________________
        $strPin = '';
        for ($intI = 0; $intI < 6; $intI++) 
        {
            $strPin .= mt_rand(0, 9);
        }
        $strPin          .= '';
        if (!empty($strPin))
        {
            $arrayResponse['pin']     = $strPin;
            $arrayResponse['status']  = self::$strStatusOK;
            $arrayResponse['mensaje'] = 'PIN GENERADO DE FORMA EXITOSA';
            //Inserto el pin en la base
            try
            {
                $arrayParametrosLog['enterpriseCode']   = isset($arrayData['strCodEmpresa']) ? $arrayData['strCodEmpresa'] : '18';
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "";
                $arrayParametrosLog['status']           = "Exitoso";
                $arrayParametrosLog['descriptionError'] = "Pin generado: ".$strPin;
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "TELCOS";
                $this->utilService->insertLog($arrayParametrosLog);
            } catch (\Exception $ex) {/*No hago nada por el catch*/}
        }
        //========================================================================
        return $arrayResponse;
    }
    
    /**
     * Función que permite el envío del pin de seguridad al cliente, a través de los medios configurados
     * 
     * @author    Juan Romero Aguilar <jromero@telconet.ec>
     * @version   1.0.0 26-11-2019
     * 
     * @author   Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version  1.1 12-02-2020    Adición del número telefónico del cliente al mensaje de usuario luego de enviar pin,
     *                             corrección al enviar el código de la empresa como parámetro a la función getCaracteristicasByParametros 
     * 
     * @param string $arrayData        -- Valores que se retornan
                                          * $arrayData['strPin']             Pin de instalación.
                                          * $arrayData['strNumeroTlf']       Número al cual se debe enviar el sms.
                                          * $arrayData['strIdentificacion']  Número al cual se debe enviar el sms.
                                          * $arrayData['strPersonaId']       Id del cliente.
                                          * $arrayData['strUsername']        Usuario que realiza la transacción.
                                          * $arrayData['strCodEmpresa']      Código de la empresa a la cual pertenece el usuario.
     * @return array $arrayResponse    -- Valores que se retornan
                                          * $arrayResponseSMS['status']      Estatus de la transacción.
                                          * $arrayResponseSMS['mensaje']     Mensaje de resultado de la transacción.
     */
    public function enviarPinSecurity($arrayData)
    {
        $intMediosEnvioSucess = 0;
        $strMensajeError      = "";
        $arrayResponse        = array();
        try
        {
            if (isset($arrayData) && count($arrayData) == 6)
            {
                // Obtenemos la admi_caracteristica [USUARIO]
                $objAdmiCaracteristicaRepo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
                $objAdmiCaractLogin        = $objAdmiCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'USUARIO',
                                                                                         'tipo' => 'TECNICA',
                                                                                         'estado' => 'Activo'));
                if (is_object($objAdmiCaractLogin))
                {
                    // Obtenemos la infoPersonaEmpresaRolCaracteristica [USUARIO], asignada para el ususario y para la empresa indicada
                    $objPersonaEmpresaRolCaracRepo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
                    $objRolCarac                   = $objPersonaEmpresaRolCaracRepo->findCaracteristicaPorCriterios
                                                     (
                                                      array(
                                                            'empresaCod'       => $arrayData['strCodEmpresa'],
                                                            'caracteristicaId' => $objAdmiCaractLogin->getId(),
                                                            'valor'            => $arrayData['strIdentificacion'],
                                                            'estado'           => 'Activo',
                                                            'intStart'         => 0,
                                                            'intLimit'         => 1
                                                           )
                                                     );
                }
                if(isset($objRolCarac) && is_object($objRolCarac))
                {
                    $arrayCanalesEnvio = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->get('CANALES_ENVIO_PIN',
                                                                       'COMERCIAL',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       $arrayData['strCodEmpresa']);
                    if(isset($arrayCanalesEnvio) && count($arrayCanalesEnvio) > 0)
                    {
                        foreach($arrayCanalesEnvio as $arrayCanalEnvio)
                        {
                            if (isset($arrayCanalEnvio) && isset($arrayCanalEnvio['valor1']))
                            {
                                switch($arrayCanalEnvio['valor1'])
                                {
                                    case 'SMS':
                                        $arrayParametrosSMS                       = array();
                                        $arrayParametrosSMS ['strPin']            = $arrayData['strPin'];
                                        $arrayParametrosSMS ['strCanalSms']       = $arrayCanalEnvio['valor2'];
                                        $arrayParametrosSMS ['strNumeroTlf']      = $arrayData['strNumeroTlf'];
                                        $arrayParametrosSMS ['strIdentificacion'] = $arrayData['strIdentificacion'];   
                                        $arrayParametrosSMS ['strUsername']       = $arrayData['strUsername'];
                                        $arrayParametrosSMS ['strCodEmpresa']     = $arrayData['strCodEmpresa'];                            
                                        if ($this->isEnvioPinViaSMS($arrayParametrosSMS))
                                        {
                                            $intMediosEnvioSucess++;
                                        }
                                        else
                                        {
                                            $strMensajeError = $strMensajeError."No se envió el sms, ";
                                        }
                                        break;
                                    case 'MAIL':
                                        $arrayParametrosMail                       = array();
                                        $arrayParametrosMail ['strPin']            = $arrayData['strPin'];
                                        $arrayParametrosMail ['strPersonaId']      = $arrayData['strPersonaId'];
                                        $arrayParametrosMail ['strIdentificacion'] = $arrayData['strIdentificacion'];
                                        $arrayParametrosMail ['strUsername']       = $arrayData['strUsername'];
                                        $arrayParametrosMail ['strCodEmpresa']     = $arrayData['strCodEmpresa'];
                                        if ($this->isEnvioPinViaMail($arrayParametrosMail))
                                        {
                                            $intMediosEnvioSucess++;
                                        }
                                        else
                                        {
                                            $strMensajeError = $strMensajeError."No se envió el correo, ";
                                        }
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                        if ($intMediosEnvioSucess > 0) 
                        {

                            // Iniciamos la transaccion
                            $this->emComercial->getConnection()->beginTransaction();

                            // Obtenemos la admi_caracteristica [TELEFONO]
                            $objCaractTel    = $objAdmiCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'TELEFONO',
                                                                                            'tipo'                      => 'TECNICA',
                                                                                            'estado'                    => 'Activo'));

                            // Obtenemos la admi_caracteristica [PIN]
                            $objCaractPin    = $objAdmiCaracteristicaRepo
                                               ->findOneBy(array('descripcionCaracteristica' => 'PIN',
                                                                 'tipo'                      => 'TECNICA',
                                                                 'estado'                    => 'Activo'));   
                            $arrayPinActivos = $objPersonaEmpresaRolCaracRepo
                                                ->getCaracteristicasByParametros(
                                                           array('empresaCod'          => $arrayData['strCodEmpresa'],
                                                                 'personaEmpresaRolId' => $objRolCarac->getPersonaEmpresaRolId(),
                                                                 'caracteristicaId'    => $objCaractPin->getId(),
                                                                 'estado'              => 'Activo'));                       
                            // Cancelar InfoPersonaEmpresaRolCarac de tipo PIN si existen mas registros Activos.       
                            if ($arrayPinActivos) 
                            {
                                foreach ($arrayPinActivos as $objRowPin):
                                    if ($objRowPin->getEstado('Activo')) 
                                    {
                                        $objRowPin->setEstado('Cancelado');
                                        $this->emComercial->persist($objRowPin);
                                        $this->emComercial->flush();
                                    }
                                endforeach;
                            }

                            //Se Guarda la caracteristica pin.
                            $objPersonaEmpresaRolCarac  = new InfoPersonaEmpresaRolCarac();
                            $objPersonaEmpresaRolCarac->setValor($arrayData['strPin']);
                            $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaractPin);
                            $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objRolCarac->getPersonaEmpresaRolId());
                            $objPersonaEmpresaRolCarac->setPersonaEmpresaRolCaracId($objRolCarac->getId());
                            $objPersonaEmpresaRolCarac->setUsrCreacion('MobileNetlife');
                            $objPersonaEmpresaRolCarac->setFeCreacion(new \Datetime('now'));
                            $objPersonaEmpresaRolCarac->setIpCreacion('127.0.0.1');
                            $objPersonaEmpresaRolCarac->setEstado('Activo');
                            $this->emComercial->persist($objPersonaEmpresaRolCarac);
                            $this->emComercial->flush();

                            //Se Guarda la caracteristica Telefono asociado al pin al que fue enviado.
                            $objPersonaEmpresaRolCaracTelf  = new InfoPersonaEmpresaRolCarac();
                            $objPersonaEmpresaRolCaracTelf->setValor($arrayData['strNumeroTlf']);
                            $objPersonaEmpresaRolCaracTelf->setCaracteristicaId($objCaractTel);
                            $objPersonaEmpresaRolCaracTelf->setPersonaEmpresaRolId($objRolCarac->getPersonaEmpresaRolId());
                            $objPersonaEmpresaRolCaracTelf->setPersonaEmpresaRolCaracId($objPersonaEmpresaRolCarac->getId());
                            $objPersonaEmpresaRolCaracTelf->setUsrCreacion('MobileNetlife');
                            $objPersonaEmpresaRolCaracTelf->setFeCreacion(new \Datetime('now'));
                            $objPersonaEmpresaRolCaracTelf->setIpCreacion('127.0.0.1');
                            $objPersonaEmpresaRolCaracTelf->setEstado('Activo');
                            $this->emComercial->persist($objPersonaEmpresaRolCaracTelf);
                            $this->emComercial->flush();

                            $arrayResponse['status']  = self::$strStatusOK;
                            $arrayResponse['mensaje'] = "Pin enviado al número {$arrayData['strNumeroTlf']}";
                            $this->emComercial->getConnection()->commit();   
                        }
                        else
                        {
                            $arrayResponse['status']  = self::$strStatusERROR;
                            $arrayResponse['mensaje'] = "Pin NO enviado al número {$arrayData['strNumeroTlf']}";

                        }
                        if (!empty($strMensajeError))
                        {
                            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                            $arrayParametrosLog['logType']          = "1";
                            $arrayParametrosLog['logOrigin']        = "TELCOS";
                            $arrayParametrosLog['application']      = basename(__FILE__);
                            $arrayParametrosLog['appClass']         = basename(__CLASS__);
                            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                            $arrayParametrosLog['messageUser']      = "";
                            $arrayParametrosLog['status']           = "Fallido";
                            $arrayParametrosLog['descriptionError'] = $strMensajeError;
                            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                            $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "TELCOS";
                            $this->utilService->insertLog($arrayParametrosLog);        
                        }
                    }
                    else
                    {
                        $strMensajeError = "No se obtuvo ningún medio configurado para el envío del pin";
                        $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "TELCOS";
                        $arrayParametrosLog['application']      = basename(__FILE__);
                        $arrayParametrosLog['appClass']         = basename(__CLASS__);
                        $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                        $arrayParametrosLog['appAction']        = "ObtenerMediosEnvío";
                        $arrayParametrosLog['messageUser']      = "";
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['descriptionError'] = $strMensajeError;
                        $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                        $arrayParametrosLog['creationUser']     = $arrayData['strUsername'];
                        $this->utilService->insertLog($arrayParametrosLog);
                        $arrayResponse['status']  = self::$strStatusERROR;
                        $arrayResponse['mensaje'] = "Pin No enviado";
                    }
                }
                else
                {
                    $arrayResponse['status']  = self::$strStatusERROR;
                    $arrayResponse['mensaje'] = "Inconvenientes con sus credenciales";
                    
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = "validarCaracterísticas";
                    $arrayParametrosLog['messageUser']      = $arrayResponse['mensaje'];
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = "No se encontró la característica de USUARIO para la persona.";
                    $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                    $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                    $this->utilService->insertLog($arrayParametrosLog);
                }
            }
            else
            {
                $arrayResponse['status']  = self::$strStatusERROR;
                $arrayResponse['mensaje'] = "No se recibió datos para el envío del pin"; 
                                
                $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = "validarData";
                $arrayParametrosLog['messageUser']      = $arrayResponse['mensaje'];
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = "No se recibió datos para el envío del pin";
                $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
                $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                $this->utilService->insertLog($arrayParametrosLog); 
            }
        }
        catch(\Exception $e)
        {
            $arrayResponse['status']  = self::$strStatusERROR;
            $arrayResponse['mensaje'] = "Pin No enviado";
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }                 
                        
            $strMensajeError = $e->getMessage();
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $strMensajeError;
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['strUsername'];
            $this->utilService->insertLog($arrayParametrosLog);
        }
        return $arrayResponse;
    }
    
    /**
     * Función que permite el envío de un pin por  SMS vía InfoBip o Massend, según el parámetro "arrayData['strCanalSms']"
     * 
     * @author    Juan Romero Aguilar <jromero@telconet.ec>
     * @version   1.0.0 26-11-2019
     * 
     * @param string $arrayData           -- Valores que se reciben
                                               * $arrayData['strPin']             Pin de instalación.
                                               * $arrayData['strCanalSms']        Canal para el envío del sms.
                                               * $arrayData['strNumeroTlf']       Número al cual se debe enviar el sms.
                                               * $arrayData['strIdentificacion']  Número de identificación del cliente.
                                               * $arrayData['strUsername']        Usuario que realiza la transacción.
                                               * $arrayData['strCodEmpresa']      Código de la empresa a la cual pertenece el usuario.
     * 
     * @return boolean $bolResponseSMS    -- Indica si el sms se envió de forma exitosa o no.
     */
    public function isEnvioPinViaSMS($arrayData)
    {
        $boolResponseSMS    = false;
        $arrayParametrosLog = array();
        $arrayResponseSMS   = array();
        try
        {
            if (isset($arrayData) && count($arrayData) == 6)
            {                
                //Obtengo el mensaje configurado.
                $arrayMensaje = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAMETROS_ENVIO_PIN',
                                                                   'COMERCIAL',
                                                                   '',
                                                                   'MENSAJE_ENVIO_PIN',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   $arrayData['strCodEmpresa']);
                if (!isset($arrayMensaje['valor1']) || empty($arrayMensaje['valor1']))
                {
                    $strMensaje = 'Gracias por Elegir a Netlife como su proveedor de Internet. '.
                                        'El codigo Netlife para validar su firma digital es: ';
                }
                else
                {
                   $strMensaje =  $arrayMensaje['valor1'];
                }
                $strMensaje = $strMensaje.$arrayData['strPin'];
                $arrayParametros                = array();
                $arrayParametros['mensaje']     = $strMensaje;
                $arrayParametros['numero']      = $arrayData['strNumeroTlf'];
                $arrayParametros['user']        = $arrayData['strIdentificacion'];
                $arrayParametros['codEmpresa']  = $arrayData['strCodEmpresa'];
                              
                if (isset($arrayData['strCanalSms']) && $arrayData['strCanalSms'] == 'INFOBIP')
                {
                    $arrayResponseSMS  = (array) $this->serviceSms->sendAPISMS($arrayParametros);                   
                }
                else if (isset($arrayData['strCanalSms']) && $arrayData['strCanalSms'] == 'MASSEND')
                {
                    $arrayResponseSMS  = (array) $this->serviceSms->sendAPISMSMassend($arrayParametros);
                }
                else
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                    $arrayParametrosLog['appClass']         = "SeguridadService";
                    $arrayParametrosLog['appMethod']        = "enviarPinViaSMS";
                    $arrayParametrosLog['appAction']        = "validar canal de envío";
                    $arrayParametrosLog['messageUser']      = "No se encontró un canal válido configurado para el envío del sms";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = "No se encontró un canal válido configurado para el envío del sms";
                    $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                    $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                    $this->utilService->insertLog($arrayParametrosLog); 
                }
                if (isset($arrayResponseSMS['salida']) && $arrayResponseSMS['salida'] == 200)
                {
                    $boolResponseSMS = true;
                }
                if ($boolResponseSMS)
                {
                    $strMensajeConfirm = "Sms enviado de forma correcta: Empresa: ". $arrayData['strCodEmpresa'] . 
                                       " Telefono: ". $arrayData['strNumeroTlf'] .
                                       $strMensaje . 
                                       " Pin# ". $arrayData['strPin'] .
                                       " identificación: ". $arrayData['strIdentificacion'];
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                    $arrayParametrosLog['appClass']         = "SeguridadService";
                    $arrayParametrosLog['appMethod']        = "enviarPinViaSMS";
                    $arrayParametrosLog['appAction']        = "enviarSMS";
                    $arrayParametrosLog['messageUser']      = "";
                    $arrayParametrosLog['status']           = "Info";
                    $arrayParametrosLog['descriptionError'] = $strMensajeConfirm;
                    $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                    $arrayParametrosLog['creationUser']     = $arrayData['strUsername'];
                    $this->utilService->insertLog($arrayParametrosLog);
                }
                else
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                    $arrayParametrosLog['appClass']         = "SeguridadService";
                    $arrayParametrosLog['appMethod']        = "enviarPinViaSMS";
                    $arrayParametrosLog['appAction']        = "EnvíoSMS";
                    $arrayParametrosLog['messageUser']      = "";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = $arrayResponseSMS['detail'];
                    $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                    $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                    $this->utilService->insertLog($arrayParametrosLog); 
                }
            }
            else
            {                       
                $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appClass']         = "SeguridadService";
                $arrayParametrosLog['appMethod']        = "enviarPinViaSMS";
                $arrayParametrosLog['appAction']        = "validarData";
                $arrayParametrosLog['messageUser']      = "No se recibió datos para el envío del sms";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = "No se recibió datos para el envío del sms";
                $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                $this->utilService->insertLog($arrayParametrosLog); 
            }
        }
        catch(\Exception $e)
        {                  
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "SeguridadService";
            $arrayParametrosLog['appMethod']        = "enviarPinViaSMS";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error al tratar de enviar el SMS";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
            $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
            $this->utilService->insertLog($arrayParametrosLog);
        }
        return $boolResponseSMS;
    }
    
     /**
     * Función que permite el envío de un pin vía correo.
     * 
     * @author    Juan Romero Aguilar <jromero@telconet.ec>
     * @version   1.0.0 26-11-2019
     * 
     * @param string $arrayData           -- Valores que se retornan
                                               * $arrayData['strPin']            Pin de instalación.
                                               * $arrayData['strPersonaId']      Id del cliente.
                                               * $arrayData['strIdentificacion'] Identificación del cliente.
                                               * $arrayData['strUsername']       Usuario que realiza la transacción.
                                               * $arrayData['strCodEmpresa']     Código de la empresa a la cual pertenece el usuario.
     * 
     * @return boolean $bolResponseMail   -- Indica si el correo se envió de forma exitosa o no.
     */
    public function isEnvioPinViaMail($arrayData)
    {
        $boolResponseMail = false;
        try
        {
            if (isset($arrayData) && count($arrayData) == 5)
            {                
                //Defino el asunto del correo
                $arrayAsunto = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAMETROS_ENVIO_PIN',
                                                                   'COMERCIAL',
                                                                   '',
                                                                   'ASUNTO_ENVIO_PIN',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   $arrayData['strCodEmpresa']);                
                if (!isset($arrayAsunto['valor1']) || empty($arrayAsunto['valor1']))
                {    
                    $strAsunto = "Pin de Instalación";
                }
                else 
                {
                    $strAsunto = $arrayAsunto['valor1'];
                }
                //Defino los destinatarios
                $arrayTo = array();

                $arrayParametros = array("intIdPersona"     => $arrayData['strPersonaId'],
                                         "strFormaContacto" => 'Correo Electronico');
                $arrayFormasContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                         ->getContactosByIdPersonaAndFormaContacto($arrayParametros);
                if($arrayFormasContactoCliente)
                {
                    foreach($arrayFormasContactoCliente as $arrayFormaContacto)
                    {
                         $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }

           

                //Defino el cuerpo del mensaje
                $strMensaje = $this->templating->render('comercialBundle:infocontrato:notificacionPin.html.twig', 
                                                      array('strCedula' => $arrayData['strIdentificacion'], 
                                                      'strPin' => $arrayData['strPin']));
                                              
                $boolResponseMail = $this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje); 
                if ($boolResponseMail)
                {
                    $strMensajeConfirm = "Mail enviado de forma correcta: Empresa: ". $arrayData['strCodEmpresa'] . 
                                       " PersonaId: ". $arrayData['strPersonaId'] .
                                       $strMensaje . 
                                       " Pin# ". $arrayData['strPin'] .
                                       " identificación: ". $arrayData['strIdentificacion'];
                    $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                    $arrayParametrosLog['logType']          = "0";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                    $arrayParametrosLog['appClass']         = "SeguridadService";
                    $arrayParametrosLog['appMethod']        = "enviarPinViaMail";
                    $arrayParametrosLog['appAction']        = "enviarMail";
                    $arrayParametrosLog['messageUser']      = "";
                    $arrayParametrosLog['status']           = "Info";
                    $arrayParametrosLog['descriptionError'] = $strMensajeConfirm;
                    $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                    $arrayParametrosLog['creationUser']     = $arrayData['strUsername'];
                    $this->utilService->insertLog($arrayParametrosLog);
                }
            }
            else
            {                       
                $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appClass']         = "SeguridadService";
                $arrayParametrosLog['appMethod']        = "enviarPinViaMail";
                $arrayParametrosLog['appAction']        = "validarData";
                $arrayParametrosLog['messageUser']      = "No se recibió datos para el envío del correo";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = "No se recibió datos para el envío del correo";
                $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
                $this->utilService->insertLog($arrayParametrosLog); 
            }
        }
        catch(\Exception $e)
        {    
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "SeguridadService";
            $arrayParametrosLog['appMethod']        = "enviarPinViaMail";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error al tratar de enviar el correo";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
            $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "Telcos";
            $this->utilService->insertLog($arrayParametrosLog);
        }
        return $boolResponseMail;
    }
    
    /**
     * 
     * Actualización: 
     * - Se inicializa arrayData con valores por defecto
     * - Se elimina else de if principal porque ya se inicializo los valores por defecto
     * - cuando es pin valido se asigna $arrayData['status']  = 'OK'
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 14-03-2017
     * 
     * Se genera el mensaje de validacion del PIN a ser guardado en los servicios
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 24-08-2017
     * 
     * 
     * Funcion que sirve para Validar Pin Security
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 04-01-2016
     * 
     * @param string $strPin            -- Pin que se validara en el proceso mencionado.
     * @param string $strUsername       -- User Name del Cliente Netlife para ello debera tener un Carateristica LOGIN en InfoPersonaEmpresaRol
     * @param string $srtCodEmpresa     -- Codigo de la Empresa para verificar que el usuario LOGIN pertenezca a dicha empresa
     * 
     * @return array $arrayData         -- Valores que se retornan
     *                                       * arrayData['idIPERCaracPin']  ->  Id de la InfoPerosonaEmpresaRolCaracteristica del PIN
     *                                       * arrayData['status']          ->  Estado de la peticion.
     *                                       * arrayData['mensaje']         ->  Mensaje del proceso de la peticion.
     */
    public function validarPinSecurity($strPin, $strUsername, $srtCodEmpresa) 
    {
        $arrayData            = array();
        $arrayData['status']  = 'ERROR_SERVICE';
        $arrayData['mensaje'] = 'PIN Invalido';
        // Obtenemos la admi_caracteristica [LOGIN]
        $objCaracteristicaRepo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $objCaractLogin        = $objCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'USUARIO',
                                                                         'tipo'                      => 'TECNICA',
                                                                         'estado'                    => 'Activo'));
    
        // Obtenemos la admi_caracteristica [PIN]
        $objAdmiCaractPin = $objCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'PIN',
                                                                    'tipo'                      => 'TECNICA',
                                                                    'estado'                    => 'Activo'));
        
         // Obtenemos la admi_caracteristica [PIN]
        $objAdmiCaractTel = $objCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'TELEFONO',
                                                                    'tipo'                      => 'TECNICA',
                                                                    'estado'                    => 'Activo'));
        
        // Obtenemos la infoPersonaEmpresaRolCaracteristica [LOGIN], asignada para el ususario y para la empresa indicada
        $objPersonaEmpresaRolCaracRepo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
        $arrayIPERCaracteristicaLogin  = $objPersonaEmpresaRolCaracRepo
                                         ->getCaracteristicasByParametros(array('empresaCod'      => $srtCodEmpresa,
                                                                               'caracteristicaId' => $objCaractLogin->getId(),
                                                                               'valor'            => $strUsername,
                                                                               'estado'           => 'Activo'));
        
        // Obtenemos la infoPersonaEmpresaRolCaracteristica [PIN], generada para el ususario
        $arrayIPERCaracteristicaPin = $objPersonaEmpresaRolCaracRepo
                                      ->getCaracteristicasByParametros(array('empresaCod'          => $srtCodEmpresa,
                                                                             'personaEmpresaRolId' => $arrayIPERCaracteristicaLogin[0]
                                                                                                      ->getPersonaEmpresaRolId(),
                                                                             'caracteristicaId'    => $objAdmiCaractPin->getId(),
                                                                             'valor'               => $strPin,
                                                                             'estado'              => 'Activo'));

        if ($arrayIPERCaracteristicaPin) 
        {
            // Obtenemos la infoPersonaEmpresaRolCaracteristica [TELEFONO], generada para el ususario
            $arrayIPERCaracteristicaTel = $objPersonaEmpresaRolCaracRepo
                                      ->getCaracteristicasByParametros(array('empresaCod'          => $srtCodEmpresa,
                                                                             'personaEmpresaRolId' => $arrayIPERCaracteristicaPin[0]
                                                                                                      ->getPersonaEmpresaRolId(),
                                                                             'caracteristicaId'    => $objAdmiCaractTel->getId(),
                                                                             'estado'              => 'Activo'));
            
            
            // Iniciamos la transaccion
            $this->emComercial->getConnection()->beginTransaction();
            
            try
            {
                // Calculo del tiempo de creacion del Pin Security 
                $objDateStart = $arrayIPERCaracteristicaPin[0]->getFeCreacion();
                $objDateEnd   = new \DateTime();
                $objDateDiff  = $objDateStart->diff($objDateEnd);
                $intMinutes   = $objDateDiff->days * 24 * 60;
                $intMinutes  += $objDateDiff->h * 60;
                $intMinutes  += $objDateDiff->i;

                // Validacion de Tiempo del Pin Security (MAX_TIME => 10 Minutos)
                if ($intMinutes > $this->intTiempoMaxValidezPin) 
                {
                    $arrayData['mensaje'] = 'PIN Expirado';
                    $arrayIPERCaracteristicaPin[0]->setEstado('Expirado');
                    $this->emComercial->persist($arrayIPERCaracteristicaPin[0]);
                    $this->emComercial->flush();
                } 
                else 
                {
                    $arrayData['status']  = 'OK';
                    $arrayData['mensaje'] = 'PIN Valido';
                    $arrayIPERCaracteristicaPin[0]->setEstado('Validado');
                    $this->emComercial->persist($arrayIPERCaracteristicaPin[0]);
                    $this->emComercial->flush();
                                        
                    /******************** HISTORIAL DEL SERVICIO ************************/
                    
                    $strPin         = $arrayIPERCaracteristicaPin[0]->getValor();
                    $strPinOculto   = '';
                    for($i=0; $i<strlen($strPin); $i++)
                    {
                        if($i==0 || $i == (strlen($strPin)-1))
                        {
                            $strPinOculto .= $strPin[$i];
                        }
                        else
                        {
                            $strPinOculto .= 'X';
                        }
                    }
                    
                    $strObservacion = 'se validó con el PIN: '. $strPinOculto;
                    $strObservacion.= ' enviado al número: '. $arrayIPERCaracteristicaTel[0]->getValor();
                    
                    $arrayData['strObservacionHistorial'] = $strObservacion;
                }

                $arrayData['idIPERCaracPin'] = $arrayIPERCaracteristicaPin[0]->getId();

                $this->emComercial->getConnection()->commit();
            }
            catch(\Exception $e)
            {
                error_log("Error: ".$e->getMessage());
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->rollback();
                }
                $this->emComercial->getConnection()->close();                
            }
        }
        return $arrayData;
    }
    
     /**
     * Funcion que sirve para Procesar el Pin Security
     * 
     * @author Washington Sanchez <wsanchez@telconet.ec>
     * @version 1.0 15-12-2015
     * 
     * @param string $intIdIPERCaracPin     -- Id del Pin que se validara en el proceso mencionado.
     */
    public function procesarPinSecurity($intIdIPERCaracPin) 
    {
        
        // Obtenemos la admi_caracteristica [PIN]
        $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $objAdmiCaractPin      = $objAdmiCaracteristica->findOneBy(array('descripcionCaracteristica' => 'PIN',
                                                                         'tipo'                      => 'SEGURIDAD',
                                                                         'estado'                    => 'Activo'));
        
        // Obtenemos la caractiristica del PIN que queremos procesar
        $objPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
        $objIPERcarac              = $objPersonaEmpresaRolCarac->findOneBy(array("id" => $intIdIPERCaracPin));

        if (is_object($objIPERcarac)) 
        {
            $this->emComercial->getConnection()->beginTransaction();            
            try
            {
                $objIPERcarac->setEstado('Procesado');
                $this->emComercial->persist($objIPERcarac);
                $this->emComercial->flush();

                // Obtenemos la infoPersonaEmpresaRolCaracteristica [PIN], generadas para el ususario con estado Validado
                $arrayIPERCaracPin = $objPersonaEmpresaRolCarac
                                     ->getCaracteristicasByParametros(array('empresaCod'          => '18',
                                                                            'personaEmpresaRolId' => $objIPERcarac
                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                     ->getId(),
                                                                            'caracteristicaId'    => $objAdmiCaractPin->getId(),
                                                                            'estado'              => 'Validado'));

                // Cancelar los PIN SECURITY que se encuentren en estado Validado
                if ($arrayIPERCaracPin) 
                {
                    foreach ($arrayIPERCaracPin as $objRowPin):
                        if ($objRowPin->getEstado('Validado')) 
                        {
                            $objRowPin->setEstado('Cancelado');
                            $this->emComercial->persist($objRowPin);
                            $this->emComercial->flush();
                        }
                    endforeach;
                }

                $this->emComercial->getConnection()->commit();

            }
            catch(\Exception $e)
            {
                error_log("Error: ".$e->getMessage());
                if ($this->emComercial->getConnection()->isTransactionActive()) 
                {
                    $this->emComercial->getConnection()->rollback();
                }
                $this->emComercial->getConnection()->close();                
            }
        }
    }


    /**
     * Funcion que consume elservicio web del LDAP login !
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 21-12-2017
     * @param array datosUsuario[user, pass]. Contiene el usuario y password de la persona que intenta loguearse.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 28-09-2022 Se realiza cambio de autenticacion para LDAP Movil.
     * 
     * @return bool confiesa si tiene o no acceso el usuario.
     */
    public function authLdapUsuarios($datosUsuario){
        $boolAuth  = false;
        if ($this->banderaWebServiceAuth == 'S') 
        {
            $arrayLdap = array();
            $arrayLdap['strUrl'] = $this->urlWebServiceAuthNueva;
            $arrayLdap['data'] = json_encode(array( 'action'    => '0',
                                        'baseDn' => '',
                                        'searchControl' => '2',
                                        'findBy' => 
                                                array('uid'=> $datosUsuario['user'])
                                        ));
            $arrayResponseJsonWS = $this->restClient->getJsonCurl($arrayLdap);
            
            error_log(json_encode($arrayResponseJsonWS));
            if ($arrayResponseJsonWS['status'] == 0) 
            {
                $arrayResponseJsonWS = json_decode($arrayResponseJsonWS['result'],true);
                if($arrayResponseJsonWS['status'] == 'FOUND')
                {
                    $strShaObtenido = str_replace("{SHA256}","",$arrayResponseJsonWS['object']['userPassword']);
                    $strClaveSha256 = base64_encode(hash('sha256',$datosUsuario['password'],true));
                    if ($strClaveSha256 == $strShaObtenido) 
                    {
                        $boolAuth = true;
                    }
                }
                else
                {
                    $boolAuth  = false;
                }
            }
        }
        else
        {
            //Se convierte la data array a tipo JsonCode
            $jsonBodyWs = json_encode($datosUsuario);
            //Configuración para desactivar el "SSL verify".
            $arrayOptionsRest = array(CURLOPT_SSL_VERIFYPEER => false);
            $arrayResponseJsonWS = $this->restClient->postJSON($this->urlWebServiceAuth, $jsonBodyWs, $arrayOptionsRest);
            if($arrayResponseJsonWS['status'] == self::$strStatusOK && $arrayResponseJsonWS['result'])
            {
                if($arrayResponseJsonWS['result'] == "access_granted")
                {
                    $boolAuth = true;
                }
            }
            else
            {
                $boolAuth  = false;
            }
        }

        return $boolAuth;
    }

    /**
     * Función que realiza el llamado a cualquier función del web service para gestionar el Ldap
     *
     * @param type array $arrayParametrosWs array con diferentes estructuras dependiendo de la opción que se invoca en la petición al web service
     *
     * @return string $arrayResultado["status", "msj", "token"]
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 17-06-2018
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 21/12/2018 - Si no es cliente se consulta en el arbol del LDAP de usuarios TN, si existe es un administrador de la app
     */
    public function callWsLdapAppClientes($arrayParametrosWs)

    {

        $arrayResultado     = array();
        $arrayData          = array();
        $strStatus          = "";
        $strMensaje         = "";
        $strMsjEx           = "";
        $strTokenResultado  = "";
        //Se genera el json a enviar al ws por tipo de proceso a ejecutar

        $strDataWs          = json_encode($arrayParametrosWs['ldap']);

        //Se obtiene el resultado de la ejecucion via rest hacia el ws

        $arrayOptionsRest   = array(CURLOPT_SSL_VERIFYPEER => false);

        $arrayResponseJsonWS= $this->restClient->postJSON($this->strUrlWebLDAP, $strDataWs, $arrayOptionsRest);
        $arrayConsultaLdap = json_decode($arrayResponseJsonWS['result'], 1);
        if($arrayConsultaLdap['status'] == self::$strStatusOK && $arrayResponseJsonWS['result'])
        {
            $objPersonaMail     = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneByValor($arrayParametrosWs['ldap']['data']['uid']);

            if(is_object($objPersonaMail))
            {
                //Verificar si el dispositivo se encuentra ya registrado previamente
                $arrayParametroDisp = array('personaId'           => $objPersonaMail->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                            'codigoDispositivo'   => $arrayParametrosWs['ldap']['source']['originID']);
                $objDispositivo     = $this->emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                           ->findOneBy($arrayParametroDisp);
                $arrayData = array(
                                    'idPersona'             => $objPersonaMail->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                    'idPersonaEmpresaRol'   => $objPersonaMail->getPersonaEmpresaRolId()->getId(),
                                    'razonSocial'           => $objPersonaMail->getPersonaEmpresaRolId()->getPersonaId()->__toString(),
                                    'identificacion'        => $objPersonaMail->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente(),
                                    'dispositivoVinculado'  => (is_object($objDispositivo) ? 1 : 0),
                                    'tipoCliente'           => self::ID_CLIENTE
                                   );
            }

            $arrayResponseWs    = json_decode($arrayResponseJsonWS['result'], true);
            $strMensaje         = $arrayResponseWs['msj'];
            $strTokenResultado  = $arrayResponseWs['token'];
            $strMsjEx           = $arrayResponseWs['msjEx'];
            $strStatus = "OK";

            if($arrayResponseWs['status'] == self::$strStatusOK)
            {
                $strStatus = self::$strStatusOK;
            }
            else
            {
                $arrayData = null;
                $strStatus = self::$strStatusERROR;
            }
        }
        else
        {
            $objPersona     = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->findOneByLogin($arrayParametrosWs['ldap']['data']['uid']);
            if(!is_object($objPersona))
            {
                return array('status'       => self::$strStatusERROR,
                             'msj'          => "Credenciales incorrectas");
            }
            $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->getPersonaOficina(array('intIdPersona'    => $objPersona,
                                                                                'strEstado'       => 'Activo',
                                                                                'intCodEmpresa'   => 10));
            if(!is_object($objPersonaEmpresaRol))
            {
                return array('status'       => self::$strStatusERROR,
                             'msj'          => "Credenciales incorrectas");
            }
            //Lógica para consultar el LDAP del arbol de usuarios TN
            $arrayParametros= array('strNombrePerfil'   => 'AdministracionTnCliente',
                                    'intIdPersonaRol'   => $objPersonaEmpresaRol->getId());
            $strTienePerfil = $this->emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                ->getPerfilPorPersona($arrayParametros);
            if($strTienePerfil === 'S')
            {
                $arrayParametroLDAP = array('user'          => $arrayParametrosWs['ldap']['data']['uid'],
                                            'pass'          => $arrayParametrosWs['ldap']['data']['pass'],
                                            'application'   => $arrayParametrosWs['ldap']['source']);
                $strDataWs          = json_encode($arrayParametroLDAP);
                $arrayResponseJsonWS= $this->restClient->postJSON($this->strUrlLDAPTN, $strDataWs, $arrayOptionsRest);
                $arrayConsultaLdap = json_decode($arrayResponseJsonWS['result'], 1);
                if (isset($arrayConsultaLdap['token']) && !empty($arrayConsultaLdap['token']))
                {
                    $arrayData = array(
                                        'idPersona'             => 0,
                                        'idPersonaEmpresaRol'   => 0,
                                        'razonSocial'           => 'Administrador',
                                        'identificacion'        => 'N/A',
                                        'dispositivoVinculado'  => 1,
                                        'tipoCliente'           => self::ID_ADMINISTRADOR
                                       );
                    $strStatus = self::$strStatusOK;
                }
                else
                {
                    return array('status'       => self::$strStatusERROR,
                                 'msj'          => "Credenciales incorrectas");
                }
            }
            else
            {
                return array('status'       => self::$strStatusERROR,
                             'msj'          => "Ud. aún no posee credenciales, por favor comunicarse con el área de soporte de sistemas");
            }

        }

        $arrayResultado['data']     = $arrayData;
        $arrayResultado['status']   = $strStatus;
        $arrayResultado['msj']      = $strMensaje;
        $arrayResultado['msjEx']    = $strMsjEx;
        $arrayResultado['token']    = $strTokenResultado;

        return $arrayResultado;
    }

 

    /**
     * Funcion que sirve para verificar si el usuario tiene el perfil de acceso .
     * 
     * @author Jefferson Carrillo<jacarrillo@telconet.ec>
     * @version 1.0 08-05-2023
     * @param array $$objSession
     * @param string $strPerfil
     * @return boolean $boolAcceso  confiesa si tiene o no acceso el usuario.
     *  
     * 
     */
    public function isAccesoLoginPerfil($objSession, $strPerfil)
    {
        $boolAcceso = false;      
        $strUsuario    = $objSession->get('user');
        $strEmpresaCod = $objSession->get('idEmpresa');  

       //obtener los datos y departamento de la persona por empresa
        $objUsuario   = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                            ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strEmpresaCod);
  
        //verificar si tiene el perfil asignado
      
        $arrayRespuesta =  $this->emComercial->getRepository('schemaBundle:SeguPerfilPersona')
                                        ->getAccesoPorPerfilPersona( $strPerfil,  $objUsuario['ID_PERSONA']);

 
        if(count($arrayRespuesta)>0)
        {
            $boolAcceso= true; 
        }
       
        return    $boolAcceso; 
    }

}
