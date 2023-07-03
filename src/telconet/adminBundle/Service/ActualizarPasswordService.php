<?php

namespace telconet\adminBundle\Service;

use telconet\schemaBundle\Entity\InfoTelconetUsersAAAA;
use telconet\schemaBundle\Entity\InfoTelconetTmpUserAAAA;
use telconet\schemaBundle\Entity\InfoTelconetUserHistoryPassAAAA;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use telconet\seguridadBundle\Service\TokenValidatorService;

/**
 * Documentación para la clase 'ActualizarPasswordService'.
 *
 * La clase ActualizarPasswordService contiene los métodos para la actualización de la contraseña, 
 * en los aplicativos administrados por Networking AAAA, Tacacs y NmsBackbone
 *
 * @author Fabricio Bermeo <fbermeo@telconet.ec>
 * @version 1.0 30-06-2016 Version Inicial
 *
 * @author Luis Tama <ltama@telconet.ec>
 * @version 1.1 20-07-2016 Se quita TacacsCpe y NmsCorp
 */
class ActualizarPasswordService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emAAAA;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emTacacs;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    
    private $emNmsBackbone;
    
    private $emSeguridad;

    private $emSoporte;
    
    private $utilService;
    
    private $strEncodeKey;

    private $strPathTelcos;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;
    /**
     *
     * @var type Tiempo de Espera para peticiones hacia el WS Forti
     */
    private $intWsFortiTimeOut;
    /**
     *
     * @var type URL del WS de Forti para actualizar la clave del cliente
     */
    private $strWsFortiUpdateUrl;
    /**
     *
     * @var Nombre de la app a consumir
     */
    private $strNombreAppWsToken;
    /**
     *
     * @var Nombre de la app que consume el Web Service
     */
    private $strUserWsToken;
    /**
     *
     * @var Nombre del archivo que contiene el web service para las consultas para Forti
     */
    private $strPasswordWsToken;
    /**
     * service $tokenGenerateURL
     */
    private $strTokenGenerateURL;
    /**
     *
     * @var boolean
     */
    private $boolTokenSslVerify;
    /**
     *
     * @var boolean
     */
    private $boolCertSslVerify;
    /**
     * service $strIpClient
     */
    private $strIpClient;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emAAAA                  = $container->get('doctrine.orm.telconet_aaaa_entity_manager');
        $this->emTacacs                = $container->get('doctrine.orm.telconet_tacacs_entity_manager');
        $this->emNmsBackbone           = $container->get('doctrine.orm.telconet_nmsbackbone_entity_manager');
        $this->strEncodeKey            = $container->getParameter('encode_key_aaaa');
        $this->strPathTelcos           = $container->getParameter('path_telcos');
        $this->intWsFortiTimeOut       = $container->getParameter('ws_forti_timeout');
        $this->strWsFortiUpdateUrl     = $container->getParameter('ws_forti_url_update');
        $this->strNombreAppWsToken     = $container->getParameter('ws_nombre_app_token');
        $this->strUserWsToken          = $container->getParameter('ws_user_app_token');
        $this->strPasswordWsToken      = $container->getParameter('ws_password_app_token');
        $this->strTokenGenerateURL     = $container->getParameter('ws_token_generate_url');
        $this->boolTokenSslVerify      = ($container->hasParameter('ws_token_ssl_verify') ?
                                          $container->getParameter('ws_token_ssl_verify') : true);
        $this->boolCertSslVerify       = ($container->hasParameter('ws_cert_ssl_verify') ?
                                          $container->getParameter('ws_cert_ssl_verify') : true);
        $this->strIpClient             = $container->getParameter('ws_token_ip_url');
        $this->serviceRestClient       = $container->get('schema.RestClient');
        $this->emComercial             = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emSeguridad             = $container->get('doctrine.orm.telconet_seguridad_entity_manager');
        $this->emSoporte               = $container->get('soporte.EnvioPlantilla'); 
        $this->utilService             = $container->get('schema.Util');
    }

    /**
     * actualizaPassword, método actualiza el campo password los aplicativos Ldap, AAAA, Tacacs y NmsBackbone
     *
     * @param array $arrParametros, $arrParametros['strLogin'] => nombre de usuario para actualizar password
     *                              $arrParametros['strClave'] => clave que se va actualizar
     *                              $arrParametros['strConfirmarClave'] => confirmación de la clave ingresada
     * @return arrRespuesta retorna un arreglo con la siguiente información:
     *         $arrRespuesta['salida'] = 0 => Error en la actualización Ldap, no continua
     *                                       con las actualizaciones de los aplicativos
     *         $arrRespuesta['salida'] = 1 => Las actualizaciones se realizaron.
     *         $arrRespuesta['salida'] = 2 => Error, la clave y su confirmación no coinciden, debe
     *                                          regresar al formulario.
     *         $arrRespuesta['mensaje'] => Un mensaje de éxito o error
     *
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 13-07-2016 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 20-07-2016 Se quita TacacsCpe y NmsCorp, se valida salida de LDAP con igual en vez de identico
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-09-29 Se incluyen validación si coindiden con algun password anterior
     *                              y envio de error por LDAP
     * 
     * @author Jorge Guerrero P. <jguerrerop@telconet.ec>
     * @version 1.3 2018-04-24 Se agrega un procedimiento para la actualización de la contraseña en Forti de CERT
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 08-02-2021 Se elimina la actualizacion de contraseña del usuario en el servidor tacacs
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.5 18-03-2023 Se elimina la actualizacion de password en el nmsbackbone, por baja del server de lado de NW
     */
    public function actualizarPassword($arrParametros)
    {
        $arrRespuesta = array();
        //Si las claves son distintas no realiza ninguna acciony debe renderizar al 
        //formulario
        if($arrParametros['strClave'] === $arrParametros['strConfirmarClave'])
        {
            if($this->coincideConPaswordAnterior(array('strLogin'   => $arrParametros['strLogin'],
                                                       'strNewPass' => $arrParametros['strClave'])))
            {
                $arrRespuesta['salida'] = 2;
                $arrRespuesta['mensaje'] = "La clave coincide con una anterior";
            }
            else
            {
                //Primero se actualiza en el LDAP y según su respuesta efectúa los cambios
        $arrLdap = $this->actualizaPasswordLdap($arrParametros);
                if(1 == $arrLdap['salida'])
                {
                    $arrRespuesta['salida'] = 1;
                    $arrRespuesta['mensaje'] = $arrLdap['mensaje'];
                    $arrRespuesta['mensaje'] .= $this->actualizaPasswordAAAA($arrParametros)."<br>";
                    //Se elimina el llamado a la funcion actualizaPasswordNmsBackbone 18/3/2023
                    $arrRespuesta['mensaje'] .= $this->actualizaRegistrosHistoricosAAAA($arrParametros)."<br>";
                    $arrRespuesta['mensaje'] .= $this->actualizaForti($arrParametros)."<br>";
                }
                else
                {
                    $arrRespuesta['salida'] = 2;
                    $arrRespuesta['mensaje'] = $arrLdap['mensaje'];
                }
            }
        }
        else
        {
            $arrRespuesta['salida'] = 2;
            $arrRespuesta['mensaje'] = "Las claves no coinciden";
        }
        return $arrRespuesta;
    }


     /*** actualizaPassword, método actualiza el campo password los aplicativos Tacacs
      * @param array $arrParametros, $arrParametros['strLogin'] => nombre de usuario para actualizar password
      *                              $arrParametros['strClave'] => clave que se va actualizar
      *                              $arrParametros['strConfirmarClave'] => confirmación de la clave ingresada
      * @author William Sanchez <wdsanchez@telconet.ec>
      * @version 1.0 04-14-2022 Version Inicial
      */
    public function actualizarPasswordForti($arrayParametros)
    {
        $arrayRespuesta = array();
        //Si las claves son distintas no realiza ninguna acciony debe renderizar al 
        //formulario
        if($arrayParametros['strClave'] === $arrayParametros['strConfirmarClave'])
        {
            if($this->coincideConPaswordAnterior(array('strLogin'   => $arrayParametros['strLogin'],
                                                       'strNewPass' => $arrayParametros['strClave'])))
          
            {
                $arrayRespuesta['salida'] = 2;
                $arrayRespuesta['mensaje'] = "La clave coincide con una anterior";
            }
            else
            {
                    $arrayRespuesta['salida'] = 1;
                    $arrayRespuesta['mensaje'] .= $this->actualizaForti($arrayParametros)."<br>";
            }
        }
        else
        {
            $arrayRespuesta['salida'] = 2;
            $arrayRespuesta['mensaje'] = "Las claves no coinciden";
        }
        return $arrayRespuesta;
    }


    
    
    /**
     * actualizaPasswordLdap, método actualiza el campo password en el LDAP
     *
     * @param array $arrParametros, $arrParametros['strLogin'] => nombre de usuario para actualizar password
     *                              $arrParametros['strClave'] => clave que se va actualizar
     * @return arrRespuesta retorna un arreglo con la siguiente información:
     *         $arrRespuesta['salida'] = 0 => Error en la actualización de Ldap
     *         $arrRespuesta['salida'] = 1 => Actualización correcta en Ldap
     *         $arrRespuesta['mensaje'] => Un mensaje de éxito o error
     *
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 13-07-2016 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 20-07-2016 Se valida salida de LDAP con igual en vez de identico
     *
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.2 08-03-2018 Se cambia path de busqueda de usuario en LDAP.
     */
    private function actualizaPasswordLdap($arrParametros)
    {
        $arrResultado = array();
        $arrResultado['salida'] = 0;
        $strMensaje = "";
        $strComando = "java -jar ".$this->strPathTelcos."telcos/src/telconet/adminBundle/batch/SSO_LDAP.jar" .
            " 'modifyPassword' 'PROD;dc=telconet,dc=net;" . $arrParametros['strLogin'] .
            ";" . $arrParametros['strClave'] . "'";

        //Ejecuta el comando
        $intSalida = shell_exec($strComando);
        if(1 == $intSalida)
        {
            $strMensaje = "<b>La contrase&ntilde;a se actualiz&oacute; correctamente</b><br>";
            $strMensaje .= "1. Su contrase&ntilde;a se actualiz&oacute; en el correo<br>";
            $strMensaje .= "2. Su contrase&ntilde;a se actualiz&oacute; en el Aplicativo Telcos+<br>";
        }
        else
        {
            $strMensaje = "<b>Su contrase&ntilde;a no se actualiz&oacute; correctamente.</b><br>Error: ".$intSalida;
        }
        $arrResultado['mensaje'] = $strMensaje;
        $arrResultado['salida'] = $intSalida;
        return $arrResultado;
    }

    /**
     * actualizaPasswordAAAA, método actualiza el campo password(si el registro existe) o  crea(el registro no existe)
     * en el aplicativo AAAA
     * @param array $arrParametros, $arrParametros['strLogin'] => nombre de usuario para actualizar password
     *                              $arrParametros['strClave'] => clave que se va actualizar
     * @return strMensaje cadena con un mensaje de éxito o error
     *
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 13-07-2016
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 20-07-2016 Se corrige catch. Se almacena el password encriptado.
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-10-19 Cuando campo shell sea NULL se envia vacio, para enviar excepción en INSERT
     */
    private function actualizaPasswordAAAA($arrParametros)
    {
        $strMensaje = "";
        $entity_tbusers = $this->emAAAA->getRepository('schemaBundle:InfoTelconetTbUsersAAAA')
                                       ->findOneByLogin($arrParametros['strLogin']);
        if($entity_tbusers)
        {
            $this->emAAAA->getConnection()->beginTransaction();
            try
            {
                $entity_tbusers->setPassword(crypt($arrParametros['strClave']));
                $this->emAAAA->persist($entity_tbusers);
                $this->emAAAA->flush();
                $entity_users = $this->emAAAA->getRepository('schemaBundle:InfoTelconetUsersAAAA')
                                             ->findOneByLogin($arrParametros['strLogin']);
                // encode password
                $strEncQuery    = "SELECT ENCODE(:strPassword, :strEncodeKey)";
                $arrEncParams   = array('strPassword' => $arrParametros['strClave'], 'strEncodeKey' => $this->strEncodeKey);
                $strEncPassword = $this->emAAAA->getConnection()->fetchColumn($strEncQuery, $arrEncParams, 0);
                if($entity_users)
                {
                    $entity_users->setPassword($strEncPassword);
                    $this->emAAAA->persist($entity_users);
                    $this->emAAAA->flush();
                }
                else
                {
                    $entity_users = new InfoTelconetUsersAAAA();
                    $entity_users->setNombre($entity_tbusers->getNombre());
                    $entity_users->setDepartamento($entity_tbusers->getDepartamento());
                    $entity_users->setCiudad("NN");
                    $entity_users->setPerfil(0);
                    $entity_users->setPerfilRouters($entity_tbusers->getPerfilRo());
                    $entity_users->setLogin($entity_tbusers->getLogin());
                    if($entity_tbusers->getShell())
                    {
                        $entity_users->setShell($entity_tbusers->getShell());
                    }
                    else
                    {
                        $entity_users->setShell("");
                    }
                    $entity_users->setTipo($entity_tbusers->getTipo());
                    $entity_users->setPassword($strEncPassword);
                    $this->emAAAA->persist($entity_users);
                    $this->emAAAA->flush();
                }
                $this->emAAAA->getConnection()->commit();
                $strMensaje = "3. Su contrase&ntilde;a se actualiz&oacute; en el Aplicativo AAAA";
            }
            catch(\Exception $e)
            {
                $strMensaje = "3. No se actualiz&oacute; su contrase&ntilde;a en el Aplicativo AAAA";
                if($this->emAAAA->getConnection()->isTransactionActive())
                {
                    $this->emAAAA->getConnection()->rollback();
                }
                $this->emAAAA->getConnection()->close();
            }
        }
        else
        {
            $strMensaje = "3. No se actualiz&oacute; su contrase&ntilde;a en el Aplicativo AAAA (Usuario no encontrado)";
        }
        return $strMensaje;
    }

    /**
     * actualizaPasswordNmsBackbone, método actualiza el campo password en el aplicativo NmsBackone
     *
     * @param array $arrParametros, $arrParametros['strLogin'] => nombre de usuario para actualizar password
     *                              $arrParametros['strClave'] => clave que se va actualizar
     * @return strMensaje cadena con un mensaje de éxito o error
     *
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 13-07-2016 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 20-07-2016 Se corrige mensajes y catch. Se almacena el password encriptado.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 22-03-2021 Se cambia la numeracion en los mensajes
     */
    private function actualizaPasswordNmsBackbone($arrParametros)
    {
        $strMensaje = "";
        $entity = $this->emNmsBackbone->getRepository('schemaBundle:InfoTelconetNmsBackboneUserAuth')
                                      ->findOneByUsername($arrParametros['strLogin']);
        if($entity)
        {
            $this->emNmsBackbone->getConnection()->beginTransaction();
            try
            {
                $entity->setPassword(hash('md5', $arrParametros['strClave']));
                $this->emNmsBackbone->persist($entity);
                $this->emNmsBackbone->flush();
                $strMensaje = "4. Su contrase&ntilde;a se actualiz&oacute; en el Aplicativo NMS-BACKBONE";
                $this->emNmsBackbone->getConnection()->commit();
            }
            catch(\Exception $e)
            {
                $strMensaje = "4. No se actualiz&oacute; su contrase&ntilde;a en el Aplicativo NMS-BACKBONE";
                if($this->emNmsBackbone->getConnection()->isTransactionActive())
                {
                    $this->emNmsBackbone->getConnection()->rollback();
                }
                $this->emNmsBackbone->getConnection()->close();
            }
        }
        else
        {
            $strMensaje = "4. No se actualiz&oacute; su contrase&ntilde;a en el Aplicativo NMS-BACKBONE (Usuario no encontrado)";
        }
        return $strMensaje;
    }
    
    /**
     * requiereCambioPassword, método que verifica si el usurio requiere cambio de Clave por expiración
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-09-28
     *
     * @param array $arrParametros ['strLogin'] => usuario para verificar
     *
     * @return boolean true cuando nunca a cambiado la clave o a expirado
     */
    public function requiereCambioPassword($arrParametros)
    {           
        return false;
        if($arrParametros['strLogin'])
        {
            $objTmpUser = $this->emAAAA->getRepository('schemaBundle:InfoTelconetTmpUserAAAA')
                                       ->findOneByLogin($arrParametros['strLogin']);
            if($objTmpUser)
            {
                $dateDiff = date_diff(new \DateTime('now'), $objTmpUser->getFeUltMod());
                if($dateDiff->format("%a") <  90)
                {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * coincideConPaswordAnterior
     * 
     * Método que verifica si la clave ingresada ya fue utilizada por el usuario
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-09-28
     *
     * @param array $arrParametros ['strLogin'   => usuario para verificar
     *                              'strNewPass' => nueva clave
     *                             ]
     *
     * @return boolean
     */
    private function coincideConPaswordAnterior($arrParametros)
    {
        if($arrParametros['strLogin'] && $arrParametros['strNewPass'])
        {
            $strNewSha256Passwd = base64_encode(hash('sha256', $arrParametros['strNewPass'], true));
            
            $arrayUserHistoryPass = $this->emAAAA->getRepository('schemaBundle:InfoTelconetUserHistoryPassAAAA')
                                                 ->findBy(array('login' => $arrParametros['strLogin']));
            
            foreach ($arrayUserHistoryPass as $objUserHistoryPass) 
            {
                if($objUserHistoryPass->getEncrypt()== "SHA256")
                {
                    if($objUserHistoryPass->getPasswd() == $strNewSha256Passwd)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * actualizaRegistrosHistoricosAAAA
     * 
     * Método que actualiza los registro historicos de los cambios de clave
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-09-29
     *
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-10-19 Ajuste de mensaje en caso de excepción, enviando sólo mensaje de no actualización
     * 
     * @param array $arrParametros ['strLogin'           => usuario para verificar
     *                              'strClave'           => nueva clave
     *                              'boolRequiereCambio' => fue cambio por caducidad
     *                             ]
     *
     * @return string
     */
    private function actualizaRegistrosHistoricosAAAA($arrParametros)
    {
        $strMensaje = "HIST. ";
        $this->emAAAA->beginTransaction();
        try
        {
            if($arrParametros['strLogin'] || $arrParametros['strClave'] )
            {
                $objTmpUser = $this->emAAAA->getRepository('schemaBundle:InfoTelconetTmpUserAAAA')
                                           ->findOneByLogin($arrParametros['strLogin']);
                if(!$objTmpUser)
                {
                    $objTmpUser = new InfoTelconetTmpUserAAAA();
                    $objTmpUser->setLogin($arrParametros['strLogin']);
                    $strMensaje .= " Se creo registro de verificación.";
                }
                $objTmpUser->setFeUltMod(new \DateTime('now'));
                $this->emAAAA->persist($objTmpUser);

                $objUserHistoryPass = new InfoTelconetUserHistoryPassAAAA();
                $objUserHistoryPass->setLogin($arrParametros['strLogin']);
                $objUserHistoryPass->setPasswd(base64_encode(hash('sha256', $arrParametros['strClave'], true)));
                $objUserHistoryPass->setEncrypt("SHA256");
                $objUserHistoryPass->setFeCambio(date("Y-m-d H:i:s"));
                if($arrParametros['boolRequiereCambio'])
                {
                    $objUserHistoryPass->setObservacion("Cambio de Clave por caducidad");
                }
                else
                {
                    $objUserHistoryPass->setObservacion("Cambio de Clave por solicitud Usuario");
                }
                $this->emAAAA->persist($objUserHistoryPass);
                $this->emAAAA->flush();
                $this->emAAAA->commit();
                $strMensaje .= " Se actualizaron los registros históricos";
            }
            else
            {
                $strMensaje .= " Sin parámetros para los registros históricos";
            }
        }
        catch(\Exception $e)
        {
            $strMensaje = "HIST.  No se actualizaron los registros históricos";
            if($this->emAAAA->getConnection()->isTransactionActive())
            {
                $this->emAAAA->rollback();
            }
            $this->emAAAA->close();
        }
        return $strMensaje;
    }
    
    /**
     * actualizaForti
     * 
     * Método que actualiza las contraseñas en el Forti
     * 
     * @author Jorge Guerrero. <jguerrerop@telconet.ec>
     * @version 1.0 2018-04-19
     *
     * @param array $arrayParametros ['strLogin'           => usuario para verificar
     *                              'strClave'           => nueva clave
     *                             ]
     *
     * @return string
     */
    private function actualizaForti($arrayParametros)
    {
        $strMensaje = "";
        try
        {
            $arrayClient=array('strIpClient' => $this->strIpClient);
            $arrayRespToken=$this->generateTokenWsForti($arrayClient);
            if ($arrayRespToken['strStatus'] == 'OK')
            {
                $arrayDataUpdate = json_encode(array('token'      => $arrayRespToken['strToken'],
                                                     'userTelco'  => $arrayParametros['strLogin'],
                                                     'claveTelco' => $arrayParametros['strClave']
                                                    )
                                              );
                $arrayRestUpdate[CURLOPT_TIMEOUT]        = $this->intWsFortiTimeOut;
                $arrayRestUpdate[CURLOPT_SSL_VERIFYPEER] = $this->boolCertSslVerify;    
                $arrayResponseWSUpdate                   = $this->serviceRestClient->postJSON($this->strWsFortiUpdateUrl,
                                                                                              $arrayDataUpdate,
                                                                                              $arrayRestUpdate);
                $arrayResult= json_decode($arrayResponseWSUpdate['result'], true);
                if ($arrayResult['success'])
                {
                    $strMensaje .=$arrayResult['msg'];
                }
                else
                {
                    $strMensaje .='Error en la respuesta al cambiar la clave';
                    error_log(print_R($arrayResponseWSUpdate,true));
                }
            }
            else
            {
                $strMensaje .='Error al obtener token security';
            }
        }
        catch(\Exception $e)
        {
            $strMensaje = "No se pudo actualizar la clave en la Base de Forti";
            error_log("No se pudo actualizar la clave en la Base de Forti ".$e);
        }
        return $strMensaje;
    }
    
    /**
     * Documentación para el método 'generateTokenWsForti'.
     *
     * Función que genera el token que será enviado como parámetro al web service de Forti
     * 
     * @param type array $arrayParametros [
     *                                      "strIpClient"           => IP origen de donde proviene la peticion
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 20-04-2018
     */
    private function generateTokenWsForti($arrayParametros)
    {
        $arrayRespuesta = array();
        $strToken       = "";
        $strStatus      = "";
        $arraySource    = array("name"          => $this->strNombreAppWsToken,
                                "originID"      => $arrayParametros["strIpClient"],
                                "tipoOriginID"  => "IP");
        
        $objDataString = json_encode(array('username' => $this->strUserWsToken,
                                           'password' => $this->strPasswordWsToken,
                                           'source' => $arraySource));
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => $this->boolTokenSslVerify);
        $arrayResponse = $this->serviceRestClient->postJSON($this->strTokenGenerateURL, $objDataString, $arrayOptions);
        if ($arrayResponse['status'] == 200)
        {
            $strStatus  = "OK";
            $arrayResult = json_decode($arrayResponse['result'],true);
            $strToken   = $arrayResult['token'];
        }
        else
        {
            $strStatus = "ERROR";
        }
        $arrayRespuesta["strStatus"]    = $strStatus;
        $arrayRespuesta["strToken"]     = $strToken;
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'resetearClave'.
     *
     * Función que resetea clave y envía mail de notifiación
     * 
     * @param type array $arrayParametros [
     *                                      "strLogin"  => Login de persona,
     *                                      "strPrefijo"  => Prefijo de empresa
     *                                    ]
     * 
     * @return array    $arrayResult[
     *                                      "strClave"  => Nueva Clave
     *                                      "strStatus" => Status
     *                                      "strMail"   => Correo del empleado
     *                               ]
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-02-2020
     * 
     * @author Byron Anton. <banton@telconet.ec>
     * @version 1.1 06-01-2022 Si se envia strMailPersonal no se obtiene correo
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.2 09-08-2022 correccion generacion clave aleatoria aumento complejidad 
     * 
     */
    public function resetearClave($arrayParametros)

    {
        $strLogin           = $arrayParametros['strLogin'];
        $arrayRespuesta     = array();
        $strClave           = "";
        $objEmpresa         = null;
        $strPrefijo         = $arrayParametros['strPrefijo'];
        
        try
        {
            $objPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                             ->findOneByLogin($strLogin);

            
            $strClave =  $this->generarCadenaAleatoria();                 

            $arrayParametros['strLogin'] = $strLogin;
            $arrayParametros['strClave'] = $strClave;
            $arrayParametros['strConfirmarClave'] = $strClave;
            $arrayRespuestaPass = $this->actualizarPassword($arrayParametros);
            $intSalida = $arrayRespuestaPass['salida'];

            //Error en la actualización, no continua
            if(!$intSalida)
            {
               $arrayRespuesta["strStatus"]    = 'false';
            }else
            {
                if(is_object($objPersona))
                {
                    $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijo);
                    $arrayParametroMail                     = array();
                    $arrayParametroMail['strLogin']         = $objPersona->getLogin();
                    $arrayParametroMail['intIdEmp']         = $objEmpresa->getId();
                    $arrayParametroMail['objUtilService']   = $this->serviceUtil;
                    if(empty($arrayParametros['strMailPersonal']))
                    {
                        $strMail                                = $this->emComercial
                                                            ->getRepository('schemaBundle:InfoPersona')
                                                            ->getMailNaf($arrayParametroMail);
                    }
                    else
                    {
                        $strMail =  $arrayParametros['strMailPersonal'];
                    }
                    if( !empty($strMail) )
                    {
                        $arrayTo                        = array();
                        $arrayTo[]                      = $strMail; 
                        $arrayNotificacion              = array();
                        $arrayNotificacion['login']     = $strLogin;
                        $arrayNotificacion['clave']     = $strClave;
                        $arrayNotificacion['fecha']     = new \DateTime('now');
                        $this->emSoporte->generarEnvioPlantilla('Restablecer contraseña',
                                                                     $arrayTo,
                                                                     'RESETEO_CLAVE',
                                                                     $arrayNotificacion,
                                                                     '10',
                                                                     0,
                                                                     0,
                                                                     null,
                                                                     null,
                                                                     'notificaciones_telcos@telconet.ec'
                                                                    );
                        $arrayRespuesta['strClave']     = $strClave;
                        $arrayRespuesta['strStatus']    = 'true';
                        $arrayRespuesta['strMail']      = $strMail;
                    }
                }
            return $arrayRespuesta;
            }
        }
         catch(\Exception $e)
        {
            $arrayRespuesta['strMensaje']   = 'Hubo un problema al resetear la clave.'.$e;
            $arrayRespuesta['strStatus']    = 'false';
            return $arrayRespuesta;
        }
    }


     /**
     * Documentación para el método 'resetearClaveWithoutMail'.
     *
     * Función que resetea clave y envía mail de notifiación
     * 
     * @param type array $arrayParametros [
     *                                      "strLogin"  => Login de persona,
     *                                      "strPrefijo"  => Prefijo de empresa
     *                                    ]
     * 
     * @return array    $arrayResult[
     *                                      "strClave"  => Nueva Clave
     *                                      "strStatus" => Status
     *                               ]
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.0 04-14-2022
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.1 09-08-2022 correccion generacion aleatoria aumento complejidad 
     * 
     */
    public function resetearClaveWithoutMail($arrayParametros)

    {
        $strLogin           = $arrayParametros['strLogin'];
        $arrayRespuesta     = array();
        $strClave           = "";
        $objEmpresa         = null;
        $strPrefijo         = $arrayParametros['strPrefijo'];
        
        try
        {
          
            

            $strClave =  $this->generarCadenaAleatoria();
            
            $arrayParametros['strLogin'] = $strLogin;
            $arrayParametros['strClave'] = $strClave;
            $arrayParametros['strConfirmarClave'] = $strClave;
            $arrayRespuestaPass = $this->actualizarPassword($arrayParametros);
            $intSalida = $arrayRespuestaPass['salida'];

            //Error en la actualización, no continua
            if(!$intSalida)
            {
               $arrayRespuesta["strStatus"]    = 'false';
            }else
            {
                $arrayRespuesta['strClave']     = $strClave;
                $arrayRespuesta['strStatus']    = 'true';
            }

            return $arrayRespuesta;
        }
         catch(\Exception $e)
        {
            $arrayRespuesta['strMensaje']   = 'Hubo un problema al resetear la clave.'.$e;
            $arrayRespuesta['strStatus']    = 'false';
            return $arrayRespuesta;
        }
    }



    /**
     * Documentación para el método 'generarCadenaAleatoria'.
     *
     * Función que genera clave aleatoria complejida acorde a parametros cert
     * 
     * 
     * @return array    $strClave
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.0 04-14-2022
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.1 09-08-2022 correccion generacion aleatoria aumento complejidad 
     * 
     */
    public function generarCadenaAleatoria()
    {

        $strClave = "";
        $strClave1 = "";
        $strClave2 = ""; 
        $strClave3 = "";
        $strClave4 = "";
        $strClaveTemp = "";


        $strRand1 = "ABCDEFGHIJKLMNPQRSTUVWXYZ"; 
        $strRand2 = "abcdefghijkmnopqrstuvwxyz";
        $strRand3 = "0123456789";
        $strRand4 = "!~#@%&*_-+={[}]|;:,.?";


        for($intA=0;$intA<3;$intA++)
            {
                $strClave1 .= substr($strRand1,rand(0,25),1);
            }

            for($intB=0;$intB<3;$intB++)
            {
                $strClave2 .= substr($strRand2,rand(0,25),1);
            }

            for($intC=0;$intC<3;$intC++)
            {
                $strClave3 .= substr($strRand3,rand(0,9),1);
            }

            for($intD=0;$intD<1;$intD++)
            {
                $strClave4 .= substr($strRand4,rand(0,3),1);
            }

            $strClaveTemp = $strClave1. $strClave2. $strClave3. $strClave4; 

            $strClave = str_shuffle($strClaveTemp);

            return $strClave;

    }


    /**
     * Documentación para el método 'resetearClavePhishing'.
     *
     * Función que resetea clave y envía mail de notifiación
     * 
     * @param type array $arrayParametros [
     *                                      "strLogin"  => Login de persona,
     *                                    ]
     * 
     * @return array    $arrayResult[
     *                                      "strClave"  => Nueva Clave
     *                                      "strStatus" => Status
     *                               ]
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.0 04-14-2022
     * 
     * @author William Sanchez C. <wdsanchez@telconet.ec>
     * @version 1.1 09-08-2022 correccion generacion aleatoria aumento complejidad 
     * 
     */
    public function resetearClaveForti($arrayParametros)

    {
        $strLogin           = $arrayParametros['strLogin'];
        $arrayRespuesta     = array();
        $strClave           = "";
        $objEmpresa         = null;
        $strPrefijo         = $arrayParametros['strPrefijo'];
        
        try
        {

            $strClave =  $this->generarCadenaAleatoria();   

            $arrayParametros['strLogin'] = $strLogin;
            $arrayParametros['strClave'] = $strClave;
            $arrayParametros['strConfirmarClave'] = $strClave;
            $arrayRespuestaPass = $this->actualizarPasswordForti($arrayParametros);
            $intSalida = $arrayRespuestaPass['salida'];

            //Error en la actualización, no continua
            if(!$intSalida)
            {
               $arrayRespuesta["strStatus"]    = 'false';
            }else
            {
                $arrayRespuesta['strClave']     = $strClave;
                $arrayRespuesta['strStatus']    = 'true';
            }

            return $arrayRespuesta;
        }
         catch(\Exception $e)
        {
            $arrayRespuesta['strMensaje']   = 'Hubo un problema al resetear la clave.'.$e;
            $arrayRespuesta['strStatus']    = 'false';
            return $arrayRespuesta;
        }
    }
    
}


