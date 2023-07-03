<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPuntoFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;

class InfoPersonaFormaContactoService {
    
    private $emcom;
    private $serviceEnvioPlantilla;
    private $serviceUtil;

    /**
     * Este metodo permite setear las dependencias del Service
     * @param \Doctrine\ORM\EntityManager $emcom
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * 
     * Se agrega parametro $container para obtener services
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.1 31-01-2017
     */
    public function setDependencies(EntityManager $emcom,
                                    \Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->emcom                 = $emcom;
        $this->serviceEnvioPlantilla = $container->get('soporte.EnvioPlantilla');
        $this->serviceUtil           = $container->get('schema.Util');
    }

    /**
     * Pone estado 'Inactivo' a todas las formas de contacto de la persona dada, que tengan estado 'Activo'.
     * Se hace persist y flush al inactivar cada registro. No se hace commit, debe hacerse fuera.
     * @param integer $idPersona id de la persona
     * @param string $user usuario que realiza la modificacion
     */
    public function inactivarPersonaFormaContactoActivasPorPersona($idPersona, $user)
    {
        $arrayPersonaFormasContacto = $this->emcom->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($idPersona, 'Activo', null, null, null);
        $objPersonasFormasContacto = $arrayPersonaFormasContacto['registros'];
        if ($objPersonasFormasContacto)
        {
            foreach($objPersonasFormasContacto as $emp)
            {
                $emp->setEstado('Inactivo');
                $emp->setFeUltMod(new \DateTime('now'));
                $emp->setUsrUltMod($user);
                $this->emcom->persist($emp);
                $this->emcom->flush();
            }
        }
    }
    /** 
     * Descripcion: Metodo encargado de validar las formas de contactos ingresadas
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     *
     * @param array  $arrayParamFormasContac   ->  array('strOpcionPermitida','strPrefijoEmpresa','arrayFormasContacto')
     * donde arrayFormasContacto  ->  array('idFormaContacto','valor','formaContacto')
     * 
     * @throws Exception
     * @version 1.0 28-08-2015 
     * @return array
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 08-11-2016
     * Se recibe array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar 
     * que para empresa MD no se obligue el ingreso de al menos 1 correo

     * 
     * Descripcion: Se agrega la validacion para telefonos internacionales
     * 
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.2 28-11-2016
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3 03-07-2017
     * Se agrega validación para teléfonos fijo y móviles de Panamá
     * 
     * @author Edgar Holguín <lcabrera@telconet.ec>
     * @version 1.4 09-02-2018
     * Se cambia expresión regular para validar teléfono fijo de Panamá.
     * 
     * @author Edgar Holguín <lcabrera@telconet.ec>
     * @version 1.5 26-03-2019
     * Se agrega parametrización de formatos de teléfono para Tenconet Panamá y Guatemala.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 12-11-2020 Se elimina uso de transacciones ya que la función es sólo de consulta y en estos casos nunca se usa,
     *                         ya que al colocar transacciones y hacer el close se cierra la misma conexión desde la función que la invoca y por ende
     *                         no se guarda nada de lo anterior que se haya realizado ya que no se hizo commit y así mismo lo que esté programado
     *                         después hará commit automático(sin necesidad de escribirlo) ya que ya no estará abierta la transacción.
     *                         Este error ha sido detectado desde la opción de aprobación de contrato por cambio de razón social por punto.
     *
     */
    public function validarFormasContactos($arrayParamFormasContac)
    {
        $arrayValidaciones        = array();
        $boolBanderaCorreo        = false;
        $arrayFormasContacto      = $arrayParamFormasContac['arrayFormasContacto'];
        $strNombrePais            = $arrayParamFormasContac['strNombrePais'];
        //AÑADE VALIDACION PANAMA
        $intTelefonoFijoDigitos   = 9;
        $intTelefonoMovilDigitos  = 10;
        $strExpresionRegularFijo  = '/^(0[2-8]{1}[0-9]{7})$/';
        $strExpresionRegularMovil = '/^(09[0-9]{8})$/';
        $strMensajeTelefonoFijo1  = 'Telefono Fijo Incorrecto debe poseer ' .$intTelefonoFijoDigitos . ' digitos incluyendo '
                                . '                codigo de area, No cumple el formato permitido : ';
        $strMensajeTelefonoFijo2  = 'Telefono Fijo Incorrecto debe poseer codigo de area '
                                    . '                valido, No cumple el formato permitido : ';
        $strMensajeTelefonoMovil1 = 'Telefono Movil Incorrecto debe poseer ' . $intTelefonoMovilDigitos . ' digitos,'
                                . ' No cumple el formato permitido : ';
        $strMensajeTelefonoMovil2 = 'Telefono Movil Incorrecto, No cumple el formato permitido : ';
        
        if(isset($strNombrePais) && ($strNombrePais == 'PANAMA' || $strNombrePais == 'GUATEMALA'))
        {
            $objEmpresaGrupo  = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')
                                            ->findOneBy( array('prefijo'     => $arrayParamFormasContac['strPrefijoEmpresa'],
                                                               'estado'      => 'Activo'));
            if(is_object($objEmpresaGrupo))
            {
                $objParamTlFijo  = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy( array('descripcion' => 'Telefono fijo',
                                                                   'valor1'      => 'numMinDigitos',
                                                                   'empresaCod'  => $objEmpresaGrupo->getId(),
                                                                   'estado'      => 'Activo') );
                if(is_object($objParamTlFijo))
                {
                     $intMinFonoFijoDigitos    = intval($objParamTlFijo->getValor2());
                }
                
                $objParamTlFijo2  = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy( array('descripcion' => 'Telefono fijo',
                                                                   'valor1'      => 'numMaxDigitos',
                                                                   'empresaCod'  => $objEmpresaGrupo->getId(),
                                                                   'estado'      => 'Activo') );
                if(is_object($objParamTlFijo2))
                {
                     $intTelefonoFijoDigitos    = intval($objParamTlFijo2->getValor2());
                }

                $objParamTlFijo3  = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy( array('descripcion' => 'Telefono fijo',
                                                                   'valor1'      => 'expRegularFormato',
                                                                   'empresaCod'  => $objEmpresaGrupo->getId(),
                                                                   'estado'      => 'Activo') );
                if(is_object($objParamTlFijo3))
                {
                     $strExpresionRegularFijo    = $objParamTlFijo3->getValor2();
                }
                
                if($strNombrePais == 'PANAMA')
                {
                    $objParamTlMovil  = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy( array('descripcion' => 'Telefono movil',
                                                                       'valor1'      => 'numMaxDigitos',
                                                                       'empresaCod'  => $objEmpresaGrupo->getId(),
                                                                       'estado'      => 'Activo') );
                    if(is_object($objParamTlMovil))
                    {
                         $intTelefonoMovilDigitos    = intval($objParamTlMovil->getValor2());
                    }
                    
                    $objParamTlMovil1  = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy( array('descripcion' => 'Telefono movil',
                                                                       'valor1'      => 'expRegularFormato',
                                                                       'empresaCod'  => $objEmpresaGrupo->getId(),
                                                                       'estado'      => 'Activo') );
                    if(is_object($objParamTlMovil1))
                    {
                         $strExpresionRegularMovil = $objParamTlMovil1->getValor2();
                    }                    
                }   
            }
            
            $strMensajeTelefonoFijo1  = 'Teléfono Fijo Incorrecto, debe poseer mínimo ' . $intMinFonoFijoDigitos.' dígitos y máximo '
                                        . $intTelefonoFijoDigitos .' . No cumple el formato permitido : ';         
        
        }        
        if(isset($strNombrePais) && $strNombrePais == 'PANAMA')
        {
            $strMensajeTelefonoFijo2  = 'Teléfono Fijo Incorrecto. No cumple el formato permitido [0-9]: ';
            $strMensajeTelefonoMovil1 = 'Teléfono Móvil Incorrecto debe poseer ' . $intTelefonoMovilDigitos . ' dígitos.'
                                . ' No cumple el formato permitido : ';
            $strMensajeTelefonoMovil2 = 'Teléfono Móvil Incorrecto. No cumple el formato permitido : ';
        }
        try
        {
            for($i = 0; $i < count($arrayFormasContacto); $i ++)
            {
                if(isset($arrayFormasContacto[$i]['idFormaContacto']))
                {
                    $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                        ->find($arrayFormasContacto[$i]['idFormaContacto']);
                }
                else
                {
                    $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                        ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]['formaContacto']);
                }
                if($objAdmiFormaContacto != null)
                {                    
                    if(strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'TELEFONO FIJO') !== false 
                        || strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'FAX') !== false 
                        || strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'TELEFONO TRASLADO') !== false)
                    {
                        if((isset($strNombrePais) && ($strNombrePais == 'PANAMA' || $strNombrePais == 'GUATEMALA')
                           && ((strlen($arrayFormasContacto  [$i]['valor'])  <  $intMinFonoFijoDigitos)    ||
                               (strlen($arrayFormasContacto  [$i]['valor'])  >  $intTelefonoFijoDigitos))) ||
                               ((strlen($arrayFormasContacto [$i]['valor']) !=  $intTelefonoFijoDigitos) && $strNombrePais != 'PANAMA' 
                                                                                                         && $strNombrePais != 'GUATEMALA'))
                        {
                            $arrayValidaciones[] = array('mensaje_validaciones' => $strMensajeTelefonoFijo1 . $arrayFormasContacto[$i]['valor']);
                        }
                        else
                        {
                            if(!preg_match($strExpresionRegularFijo, $arrayFormasContacto[$i]['valor']))
                            {
                                $arrayValidaciones[] = array('mensaje_validaciones' => $strMensajeTelefonoFijo2 . $arrayFormasContacto[$i]['valor']);
                            }
                        }
                    }
                    if(strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false
                        || strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'FACEBOOK') !== false)
                    {
                        $strEmail = $arrayFormasContacto[$i]['valor'];
                        if($this->esEmailValido($arrayFormasContacto[$i]['valor']))
                        {
                            //Verificando el dominio de mail				
                            $arrayMailTemp = explode("@", $arrayFormasContacto[$i]['valor']);
                            $strDominioTemp = $arrayMailTemp[1];

                            if(!$this->verificarMailDNS($strDominioTemp))
                            {
                                $arrayValidaciones[] = array('mensaje_validaciones' => 'Dominio del Correo Electronico Incorrecto,'
                                    . ' No cumple el formato permitido : ' . $strEmail );
                            }
                            else
                            {
                                if(strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false)
                                {
                                    $boolBanderaCorreo = true; // Bandera para validar que tenemos 1 forma de contacto de tipo Correo Valida
                                }
                            }
                        }
                        else
                        {
                            $arrayValidaciones[] = array('mensaje_validaciones' => 'Correo Electronico Incorrecto,'
                                . ' No cumple el formato permitido : ' . $arrayFormasContacto[$i]['valor']);
                        }
                    }
                    if(strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'TELEFONO MOVIL') !== false)
                    {
                        if(strlen($arrayFormasContacto [$i]['valor']) != $intTelefonoMovilDigitos)
                        {
                            $arrayValidaciones[] = array('mensaje_validaciones' => $strMensajeTelefonoMovil1 . $arrayFormasContacto[$i]['valor']);
                        }
                        else
                        {
                            if(!preg_match($strExpresionRegularMovil, $arrayFormasContacto [$i]['valor']))
                            {
                                $arrayValidaciones[] = array('mensaje_validaciones' => $strMensajeTelefonoMovil2 . $arrayFormasContacto[$i]['valor']);
                            }
                        }
                    }
                   
                    if(strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'TELEFONO INTERNACIONAL') !== false)
                    {
                            if(!preg_match('/^([0-9]{7,15})$/', $arrayFormasContacto [$i]['valor']))
                            {
                                $arrayValidaciones[] = array('mensaje_validaciones' => 'Telefono Internacional Incorrecto '
                                    . ' Solo debe ingresar entre 7 y 15 digitos,'
                                    . ' No cumple el formato permitido : ' . $arrayFormasContacto[$i]['valor']);
                            }
                    } 
                }
            }
            $boolNoValida = false;
            if(isset($arrayParamFormasContac['strPrefijoEmpresa']) && $arrayParamFormasContac['strPrefijoEmpresa'] == 'MD')
            {
                if(isset($arrayParamFormasContac['strOpcionPermitida']) && $arrayParamFormasContac['strOpcionPermitida'] == 'SI')
                {
                    $boolNoValida = true;
                }
            }
           
            if(!$boolBanderaCorreo && !$boolNoValida)
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Debe Ingresar al menos 1 Correo Electronico valido');
            }
            return $arrayValidaciones;
        }
        catch(\Exception $e)
        {
            $arrayValidaciones[] = array('mensaje_validaciones' => $e->getMessage());
            return $arrayValidaciones;
        }
    }
    
    /**
     * Documentación para el método 'verificarMailDNS'.
     *
     * Ejecuta comando DOS -dig- para la verificación de existencia del dominio del servidor de correo.
     * 
     * @param String $strHostName Nombre del Dominio
     * 
     * @return Bool Servidor de Correos válido o no.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.0 29-04-2016
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 31-05-2016
     * Se agrega una capa de validación en caso de que el servidor consultado no de respuesta el correo será rechazado.
     */
    public function verificarMailDNS($strHostName)
    {
        $arrayResult = array();

        // Indica el formato de la respuesta; 
        // MX = Mail Exchange, para validar el servidor de correos.
        $strTypeFormat = "MX";
        
        if(!empty($strHostName))
        {
            // Ejecuto el comando DOS para verificación del Dominios
            // +short indica la forma abreviada, y solo si es correcto el servidor devolverá datos
            // 8.8.8.8 Es el DNS público de Google y garantiza la confirmación del servidor de correos. 
            exec("dig @8.8.8.8 $strHostName $strTypeFormat +short", $arrayResult);
            
            if(count($arrayResult) == 0)
            {
                return false;
            }
            else
            {
                // Se procede a evaluar la respuesta
                foreach($arrayResult as $strValue)
                {
                    // si el resultado fue un time-out el servidor de correo NO es válido
                    if(strpos($strValue, 'timed out') !== false)
                    {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /** 
     * Descripcion: Metodo encargado de validar un correo ingresado como forma de contacto
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     *
     * @param string $email
     * 
     * @throws Exception
     * @version 1.0 31-08-2015 
     * @return boolean
     */
    
    public function esEmailValido($email)
    {
        // Primero, checamos que solo haya un simbolo @, y que los largos sean correctos
        if(!ereg("^[^@]{1,64}@[^@]{1,255}$", $email))
        {
            // correo invalido por numero incorrecto de caracteres en una parte, o numero incorrecto de simbolos @
            return false;
        }
        // se divide en partes para hacerlo mas sencillo
        $arrayEmailArray = explode("@", $email);
        $arrayLocalArray = explode(".", $arrayEmailArray[0]);
        for($i = 0; $i < sizeof($arrayLocalArray); $i++)
        {
            if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $arrayLocalArray[$i]))
            {
                return false;
            }
        }
        // se revisa si el dominio es una IP. Si no, debe ser un nombre de dominio valido
        if(!ereg("^\[?[0-9\.]+\]?$", $arrayEmailArray[1]))
        {
            $arrayDomainArray = explode(".", $arrayEmailArray[1]);
            if(sizeof($arrayDomainArray) < 2)
            {
                return false; // No son suficientes partes o secciones para ser un dominio
            }
            for($i = 0; $i < sizeof($arrayDomainArray); $i++)
            {
                if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $arrayDomainArray[$i]))
                {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Funcion que permite editar las formas de contacto
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 
     * @since 24/03/2016
     * 
     * @param array $arrayParametros
     * @param array $arrayData
     *
     * Actualización: 
     * - Se corrige en el catch reemplazando Exception por \Exception
     * - Se corrige en el catch borrar error_log el error y grabar error en BD
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 14-03-2017
     *
     */
    public function actualizarFormasContacto($arrayParametros)
    {
        try
        {
            $arrayData = array();
            
            if(!array_key_exists('objPersona', $arrayParametros))
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'Es requerido la información de la Persona';
                return $arrayData;
            }
            if(!array_key_exists('strUsuario', $arrayParametros))
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'Es requerido el usuario de modificación';
                return $arrayData;
            }
            
            if(empty($arrayParametros['arrayFormasContacto']))
            {
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'Es requerido la información de las Formas de Contacto';
                return $arrayData; 
            }
            
            $this->inactivarPersonaFormaContactoActivasPorPersona($arrayParametros['objPersona']->getId(), $arrayParametros['strUsuario']);
            
            $arrayFormasContacto = $arrayParametros['arrayFormasContacto'];
            
            //Registra las formas de contacto del cliente  
            for($i = 0; $i < count($arrayFormasContacto); $i++)
            {
                $objFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                         ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]["formaContacto"]);
                
                $objPersonaFormaContacto = new InfoPersonaFormaContacto();
                $objPersonaFormaContacto->setValor($arrayFormasContacto[$i]["valor"]);
                $objPersonaFormaContacto->setFormaContactoId($objFormaContacto);
                $objPersonaFormaContacto->setIpCreacion($arrayParametros['strIpCreacion']);
                $objPersonaFormaContacto->setPersonaId($arrayParametros['objPersona']);
                $objPersonaFormaContacto->setUsrCreacion($arrayParametros['strUsuario']);
                $objPersonaFormaContacto->setEstado("Activo");
                $objPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                
                $this->emcom->persist($objPersonaFormaContacto);
                $this->emcom->flush();
            }
        } catch(\Exception $ex)
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Inconvenientes con la actualizacion de la Información de Formas Contacto';
            $this->serviceUtil->insertError(
                                            "Telcos+",
                                            "InfoPersonaFormaContactoService->actualizarFormasContacto", 
                                            $e->getMessage(), 
                                            $arrayParametros['strUsuario'], 
                                            $arrayParametros['strIpCreacion']
                                           );
            return $arrayData;
        }
    }
    
    /**
     * 
     * Metodo que se encarga de actualizar, crear o eliminar las formas de contacto de una persona segun como sea la gestión de administración
     * en las pantallas de formas de contacto ( js )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 20-07-2016
     * 
     * @param  Array $arrayParametros [ idPersona , jsonFormasContacto , usrCreacion, ipCreacion ]
     * @return Array 
     */
    public function agregarActualizarEliminarFormasContacto($arrayParametros)
    {
        try
        {
            $intIdPersona       = $arrayParametros['idPersona'];
            $jsonFormasContacto = $arrayParametros['jsonFormasContacto'];
            $usrCreacion        = $arrayParametros['usrCreacion'];
            $ipCreacion         = $arrayParametros['ipCreacion'];
            
            $arrayNuevosContactos = $jsonFormasContacto!=""?json_decode($jsonFormasContacto):null;            
            $arrayFormaContacto   = $this->emcom->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                ->findBy(array('personaId' => $intIdPersona,
                                                               'estado'    => 'Activo')
                                                              );                        
            if($arrayNuevosContactos)
            {
                $arrayIdsFormasContactoExistente = array();
                $arrayIdsFormasContactoNuevos    = array();               
                
                $objPersona = $this->emcom->getRepository("schemaBundle:InfoPersona")->find($intIdPersona);
                
                foreach($arrayFormaContacto as $objFormaContacto)
                {
                    $arrayIdsFormasContactoExistente[] = $objFormaContacto->getId();
                }
                
                foreach($arrayNuevosContactos->data as $nuevoContacto)                
                {                    
                    $intIdFormaContacto = $nuevoContacto->idPersonaFormaContacto;
                    $arrayIdsFormasContactoNuevos[] = $intIdFormaContacto;    
                    
                    //Obtengo la forma de contacto de cada item para verificar si ha cambiado o no o si es un registro nuevo poder enlazar 
                    $objAdmiFormaContacto = $this->emcom->getRepository("schemaBundle:AdmiFormaContacto")
                                                        ->findOneByDescripcionFormaContacto($nuevoContacto->formaContacto);
                    
                    //Si el id es 0 significa que es forma de contacto nueva
                    if($intIdFormaContacto == 0)
                    {                        
                        $objFormaContacto = new InfoPersonaFormaContacto();
                        $objFormaContacto->setPersonaId($objPersona);
                        $objFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                        $objFormaContacto->setValor($nuevoContacto->valor);
                        $objFormaContacto->setEstado("Activo");
                        $objFormaContacto->setFeCreacion(new \DateTime('now'));
                        $objFormaContacto->setUsrCreacion($usrCreacion);
                        $objFormaContacto->setIpCreacion($ipCreacion);
                        $this->emcom->persist($objFormaContacto);
                        $this->emcom->flush();
                    }
                    else
                    {
                        //Obtengo la forma de contacto existente para verificar que le valor haya cambiado
                        $objFormaContacto = $this->emcom->getRepository("schemaBundle:InfoPersonaFormaContacto")->find($intIdFormaContacto);
                        
                        //Si cambió el valor o el tipo de forma de contacto se edita el registro caso contrario no se lo toca
                        if($objFormaContacto->getValor() != $nuevoContacto->valor ||
                           $objAdmiFormaContacto->getId() !=  $objFormaContacto->getFormaContactoId()->getId())
                        {
                            $objFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                            $objFormaContacto->setValor($nuevoContacto->valor);
                            $objFormaContacto->setFeUltMod(new \DateTime('now'));
                            $objFormaContacto->setUsrUltMod($usrCreacion);
                            $this->emcom->persist($objFormaContacto);
                            $this->emcom->flush();
                        }
                    }
                }
                
                //Busqueda de correos viejos para editar estado a Eliminado ( en caso de que sean borrados por pantalla )
                foreach($arrayIdsFormasContactoExistente as $id)
                {
                    if(!in_array($id, $arrayIdsFormasContactoNuevos))
                    {
                        $idsViejos[] = $id;
                    }
                }                                                                                                                      
                
                //Se cambia a estado Eliminado los alias que son borrados de la plantilla
                if($idsViejos && count($idsViejos)>0)
                {
                    foreach($idsViejos as $id)
                    {
                        $objFormaContacto = $this->emcom->getRepository("schemaBundle:InfoPersonaFormaContacto")->find($id);
                        $objFormaContacto->setEstado("Eliminado");
                        $objFormaContacto->setFeUltMod(new \DateTime('now'));
                        $objFormaContacto->setUsrUltMod($usrCreacion);
                        $this->emcom->persist($objFormaContacto);
                        $this->emcom->flush();
                    }
                }                
            }
            else //Si no existe ninguna forma de contacto, significa que fueron borrados todos por pantalla
            {                
                if($arrayFormaContacto)
                {
                    foreach($arrayFormaContacto as $objFormaContacto)
                    {
                        $objFormaContacto->setEstado("Eliminado");
                        $objFormaContacto->setFeUltMod(new \DateTime('now'));
                        $objFormaContacto->setUsrUltMod($usrCreacion);
                        $this->emcom->persist($objFormaContacto);
                        $this->emcom->flush();
                    }
                }
            }
            
            return array('success'=>true,'mensaje'=>'Formas de Contacto Actualizadas Correctamente');
        } 
        catch (\Exception $ex) 
        {
            return array('success'=>false,'mensaje'=>'Error al actualizar contacto : '.$ex->getMessage());
        }
    }
    
    
    /**
     * Funcion que permite validar y enviar una noticación  por Edició de contactos
     * 
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.0 26-01-2017
     * 
     * @param type $arrayParametros
     * $arrayParametros['strCodigoEmpresa'] Es el codigo de la empresa en donde se esta modicando el contacto
     * $arrayParametros['strIpCliente'] Ip del cliente que realiza la modificacion del contacto 
     * $arrayParametros['arrayDatosEnvioPlantilla'] Array con los datos para envir la notificacion de edicion de contacto
     * dentro de este array estan los siguientes parametros:
     *     $arrayDatosEnvioPlantilla['strRazonSocialCliente'] Es la razon del cliente al que se le modifica el contacto
     *     $arrayDatosEnvioPlantilla['strIdentificacionCliente'] Es la identificacion del cliente al que se le modifica el contacto
     *     $arrayDatosEnvioPlantilla['strLogin'] Login del cliente al que se le modifica el contacto
     *     $arrayDatosEnvioPlantilla['strNombresContacto'] Nombres del contacto que se esta modificando
     *     $arrayDatosEnvioPlantilla['strApellidosContacto'] Apellidos del contacto que se esta modificando
     *     $arrayDatosEnvioPlantilla['strIdentificacionContacto'] Identificacion del Contacto que se esta modificando
     *     $arrayDatosEnvioPlantilla['strFechaModificacionContacto'] Fecha en la que se modifica el contacto
     *     $arrayDatosEnvioPlantilla['strUsuarioModificacionContacto'] = Usuario que modifca el contacto
     *     $arrayDatosEnvioPlantilla['strFormasContactoEditadas'] Cadena con las formas de contacto editadas
     *     $arrayDatosEnvioPlantilla['strFormasContactoEliminadas'] Cadena con las formas de contacto eliminadas
     *     $arrayDatosEnvioPlantilla['strFormasContactoNuevas'] Cadena con las formas de contacto eliminadas
     *     $arrayDatosEnvioPlantilla['strNombresActuales'] Nombres Actuales del contacto que se esta editando
     *     $arrayDatosEnvioPlantilla['strApellidosActuales'] Apellido Actuales del contacto que se esta editanto
     *     $arrayDatosEnvioPlantilla['strTituloActual'] Titulo actual del contacto que se esta editando
     *     $arrayDatosEnvioPlantilla['strIdentificacionActual'] Identificacion actual del contacto que se esta editando
     *     $arrayDatosEnvioPlantilla['strFormasContactoActuales'] Formas de contacto actuales que tiene el contacto que se esta editando
     * 
     */
    public function enviarNotificacionEdicionContacto($arrayParametros)
    {
        try
        {
            if(!empty($arrayParametros) && isset($arrayParametros["strCodigoEmpresa"]))
            {
                $strCodigoEmpresa = $arrayParametros['strCodigoEmpresa'];

                //Consulta en la tabla detalle parametros validar si para la empresa se debe enviar
                //la notificacion por edicion de contacto
                $arrayValidacionNotificacion = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne("PARAMETROS_NOTIFICACION_EDICION_CONTACTOS", 
                                                                    "COMERCIAL", "", "", "NOTIFICAR_EDICION_CONTACTOS", 
                                                                    "", "", "", "", $strCodigoEmpresa);

                if(!empty($arrayValidacionNotificacion) &&
                    isset($arrayValidacionNotificacion["valor2"]) &&
                    $arrayValidacionNotificacion["valor2"] === 'S' &&
                    isset($arrayValidacionNotificacion["valor3"]) &&
                    isset($arrayValidacionNotificacion["valor4"]) &&
                    isset($arrayValidacionNotificacion["valor5"]))
                {
                    $strAsunto                = $arrayValidacionNotificacion["valor3"];
                    $strRemitente             = $arrayValidacionNotificacion["valor4"];
                    $strCodigoPlantilla       = $arrayValidacionNotificacion["valor5"];
                    $arrayDatosEnvioPlantilla = $arrayParametros["arrayDatosEnvioPlantilla"];

                    $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto, null, $strCodigoPlantilla, 
                                                                        $arrayDatosEnvioPlantilla, $strCodigoEmpresa, 
                                                                        null, null, null, false, $strRemitente);
                }
            }
        }
        catch(\Exception $ex)
        {
            error_log($ex);
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoPersonaFormaContactoService->enviarNotificacionEdicionContacto', 
                                            $ex->getMessage(),
                                            $arrayParametros['arrayDatosEnvioPlantilla']['strUsuarioModificacionContacto'], 
                                            $arrayParametros['strIpCliente']
                                           );
        }
    }
    
    
    /**
     * Método que crea,actualiza y elimina una forma de contacto, para un punto y una persona.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 14/04/2021
     * @param type $arrayParametros
     *
     * Actualización: 
     * - Se añaden validaciones de pertenencia para formas de contacto para personas y punto
     * - Se añade validacion de existencia de formas de contacto
     * @author Roberth Cobeña <rcobena@telconet.ec>
     * @version 1.1 28-04-2021
     */
    public function procesoFormasContacto($arrayParametros)
    {
        
        $emComercial     = $this->emcom;
        $serviceUtils    = $this->serviceUtil;
        $arrayData       = $arrayParametros;
        $strIpCreacion   = "127.0.0.1";
        
        $emComercial->getConnection()->beginTransaction();
        
        try 
        {
            
            if ($arrayData['origen']=="PERSONA")
            {
                if($arrayData['proceso'] == "CREAR")
                {
                    
                    //Valida si existe el cliente ingresado
                    $entityPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $arrayData['identificacion']));
                    if(empty($entityPersona))
                    {
                       throw new \Exception("No se encontró información de la persona con el número de indentificación ingresado");
                    }
                    
                    //valida si se recibio idformacontacto
                    if(empty($arrayData['idFormaContacto']))
                    {
                        throw new \Exception("Falta la variable idFormaContacto en el request");
                    }
                    
                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }

                    //valida formatos de correo y numero telefonico
                    $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($arrayData['idFormaContacto']);

                    //INICIO validacion correo y telefonos
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false
                        || strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'FACEBOOK') !== false)
                    {
                        $strEmail = $arrayData['valor'];
                        if($this->esEmailValido($arrayData['valor']))
                        {
                            //Verificando el dominio de mail				
                            $arrayMailTemp = explode("@", $arrayData['valor']);
                            $strDominioTemp = $arrayMailTemp[1];

                            if(!$this->verificarMailDNS($strDominioTemp))
                            {
                                throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . " No cumple el formato permitido : " . $strEmail);
                            }
                        }
                        else
                        {
                            throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . " No cumple el formato permitido : " . $arrayData['valor']);
                        }
                    }
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'TELEFONO MOVIL') !== false)
                    {
                        if(strlen($arrayData['valor']) != 10)
                        {
                            throw new \Exception("Teléfono Móvil no cumple el formato permitido, debe poseer 10 dígitos:  " . $arrayData['valor']);
                        }
                        else
                        {
                            if(!preg_match('/^(09[0-9]{8})$/', $arrayData['valor']))
                            {
                                throw new \Exception("Teléfono Móvil Incorrecto, no cumple el formato permitido : " . $arrayData['valor']);
                            }
                        }
                    }
                    //FIN validacion correo y telefonos
                    if(is_object($entityFormaContacto))
                    {
                       $entityInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                       $entityInfoPersonaFormaContacto->setPersonaId($entityPersona);
                       $entityInfoPersonaFormaContacto->setFormaContactoId($entityFormaContacto);
                       $entityInfoPersonaFormaContacto->setValor($arrayData['valor']);
                       $entityInfoPersonaFormaContacto->setEstado("Activo");
                       $entityInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                       $entityInfoPersonaFormaContacto->setUsrCreacion($arrayData['usuario']);
                       $entityInfoPersonaFormaContacto->setIpCreacion($strIpCreacion);
                       
                       $emComercial->persist($entityInfoPersonaFormaContacto);
                       $emComercial->flush();
                       $emComercial->getConnection()->commit();
                   
                       $arrayRespuesta['status']  = "OK";
                       $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                       
                    }
                    else
                    {
                        throw new \Exception("No se proceso el registro de forma contacto, por favor, reintente");
                        
                    }
                    
                }
                elseif($arrayData['proceso'] == "ACTUALIZAR")
                {
                    //valida que recibio idcontacto
                    if (empty($arrayData['idContacto']))
                    {
                       throw new \Exception("Falta la variable idContacto en el request");
                    }
                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }

                    //validar pertenencia
                    $entityPersona1  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $arrayData['identificacion']));

                    if(is_object($entityPersona1))
                    {
                        $arrayContactoPersona    =  $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getFormasContacto($entityPersona1);
                    }
                    else 
                    {
                        throw new \Exception("La identificación ".$arrayData['identificacion']." ingresada no existe");  
                    }
                    
                    $strCount=0;                                   
                    foreach ($arrayContactoPersona as $array) 
                    {
                        $arrayResponse = $array['idPersonaFormaContacto'];
                        $arrayResponse2 = $array['idPersona'];
                        
                        if (($arrayData['idContacto']==$arrayResponse) && ($entityPersona1->getId()==$arrayResponse2)) 
                        {
                            $strCount+=1;                    
                        }
                        else
                        {
                            $strCoun=0;
                        }
                    }
                    if ($strCount!==1) 
                    {
                        throw new \Exception('No existen idContacto: '.$arrayData['idContacto'].' para identificación '.$arrayData['identificacion']);
                    }
                
                    $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($arrayData['idFormaContacto']);

                    //INICIO validacion correo y telefonos
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false
                        || strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'FACEBOOK') !== false)
                    {
                        $strEmail = $arrayData['valor'];
                        if($this->esEmailValido($arrayData['valor']))
                        {
                            //Verificando el dominio de mail				
                            $arrayMailTemp = explode("@", $arrayData['valor']);
                            $strDominioTemp = $arrayMailTemp[1];

                            if(!$this->verificarMailDNS($strDominioTemp))
                            {
                                throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $strEmail);
                            }
                        }
                        else
                        {
                            throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $arrayData['valor']);
                        }
                    }
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'TELEFONO MOVIL') !== false)
                    {
                        if(strlen($arrayData['valor']) != 10)
                        {
                            throw new \Exception("Teléfono Móvil no cumple el formato permitido, debe poseer 10 dígitos:  " . $arrayData['valor']);
                        }
                        else
                        {
                            if(!preg_match('/^(09[0-9]{8})$/', $arrayData['valor']))
                            {
                                throw new \Exception("Teléfono Móvil Incorrecto, no cumple el formato permitido : " . $arrayData['valor']);
                            }
                        }
                    }
                    //FIN validacion correo y telefonos
                                       
                    $objPersonaFormaContacto  = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                            ->findOneBy(array('id'  => $arrayData['idContacto']));
         
                    if(is_object($objPersonaFormaContacto))
                    {
                       //actualizo forma de contacto
                       $objPersonaFormaContacto->setEstado("Inactivo");
                       $objPersonaFormaContacto->setFeUltMod(new \DateTime('now'));
                       $objPersonaFormaContacto->setUsrUltMod($arrayData['usuario']);
                       
                       $emComercial->persist($objPersonaFormaContacto);
                       $emComercial->flush();
                       
                       //insertar nueva forma de contacto
                       $entityPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $arrayData['identificacion']));
          
                       if(empty($entityPersona))
                       {
                        throw new \Exception("No se encontró información de la persona con el número de indentificación ingresado");
                       }
                       
                       if(empty($arrayData['idFormaContacto']))
                       {
                          throw new \Exception("Falta la variable idFormaContacto en el request");
                       }
                       
                       if(empty($arrayData['valor']))
                       {
                          throw new \Exception("Falta la variable valor en el request");
                       }
                       
                       if(is_object($entityFormaContacto))
                       {
                          $entityInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                          $entityInfoPersonaFormaContacto->setPersonaId($entityPersona);
                          $entityInfoPersonaFormaContacto->setFormaContactoId($entityFormaContacto);
                          $entityInfoPersonaFormaContacto->setValor($arrayData['valor']);
                          $entityInfoPersonaFormaContacto->setEstado("Activo");
                          $entityInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                          $entityInfoPersonaFormaContacto->setUsrCreacion($arrayData['usuario']);
                          $entityInfoPersonaFormaContacto->setIpCreacion($strIpCreacion);
                       
                          $emComercial->persist($entityInfoPersonaFormaContacto);
                          $emComercial->flush();
                          $emComercial->getConnection()->commit();
                   
                          $arrayRespuesta['status']  = "OK";
                          $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                       
                       }
                       else
                       {
                          throw new \Exception("No se encontró forma de contanto con el idFormaContacto ingresado");
                          
                          
                       }
                       
                       
                    }
                    else
                    {
                        throw new \Exception("No se encontró una forma de contacto con el id contacto ingresado");
                       
                    }
                        
                    
                }
                elseif($arrayData['proceso'] == "ELIMINAR") 
                {
                
                    if (empty($arrayData['idContacto']))
                    {
                       throw new \Exception("Falta la variable idContacto en el request");
                    }
                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }

                    //validar pertenencia
                    $entityPersona1  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $arrayData['identificacion']));

                    if(is_object($entityPersona1))
                    {
                        $arrayContactoPersona    =  $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getFormasContacto($entityPersona1);
                    }
                    else 
                    {
                        throw new \Exception("La identificación ".$arrayData['identificacion']." ingresada no existe");  
                    }
                    
                    $strCount=0;                                   
                    foreach ($arrayContactoPersona as $array) 
                    {
                        $arrayResponse = $array['idPersonaFormaContacto'];
                        $arrayResponse2 = $array['idPersona'];
                        
                        if (($arrayData['idContacto']==$arrayResponse) && ($entityPersona1->getId()==$arrayResponse2)) 
                        {
                            $strCount+=1;                    
                        }
                        else
                        {
                            $strCoun=0;
                        }
                    }
                    if ($strCount!==1) 
                    {
                        throw new \Exception('No existen idContacto: '.$arrayData['idContacto'].' para identificación '.$arrayData['identificacion']);
                    }

                    $objPersonaFormaContacto  = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                            ->findOneBy(array('id'  => $arrayData['idContacto']));
                    
                    if(is_object($objPersonaFormaContacto))
                    {
                        
                        //actualizo forma de contacto
                       $objPersonaFormaContacto->setEstado("Eliminado");
                       $objPersonaFormaContacto->setFeUltMod(new \DateTime('now'));
                       $objPersonaFormaContacto->setUsrUltMod($arrayData['usuario']);
                       
                       $emComercial->persist($objPersonaFormaContacto);
                       $emComercial->flush();
                       $emComercial->getConnection()->commit();
                       
                       $arrayRespuesta['status']  = "OK";
                       $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                        
                    }
                    else
                    {
                        throw new \Exception("No se encontró una forma de contacto con el id contacto ingresado");
                       
                    }
                       
                    
                }
                
                
            }
            elseif($arrayData['origen']=="PUNTO")
            {
                
                if($arrayData['proceso'] == "CREAR")
                {
                    $entityPunto  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                  ->findOneBy(array('login'  => $arrayData['login']));
          
                    if(empty($entityPunto))
                    {
                        throw new \Exception("No se encontró información del punto con el login ingresado");
                    }
                    
                    if(empty($arrayData['idFormaContacto']))
                    {
                        throw new \Exception("Falta la variable idFormaContacto en el request");
                    }
                    
                    if(empty($arrayData['valor']))
                    {
                        throw new \Exception("Falta la variable valor en el request");
                    }
                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }                   

                    $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($arrayData['idFormaContacto']);

                    //INICIO validacion correo y telefonos
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false
                        || strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'FACEBOOK') !== false)
                    {
                        $strEmail = $arrayData['valor'];
                        if($this->esEmailValido($arrayData['valor']))
                        {
                            //Verificando el dominio de mail				
                            $arrayMailTemp = explode("@", $arrayData['valor']);
                            $strDominioTemp = $arrayMailTemp[1];

                            if(!$this->verificarMailDNS($strDominioTemp))
                            {
                                throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $strEmail);
                            }
                        }
                        else
                        {
                            throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $arrayData['valor']);
                        }
                    }
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'TELEFONO MOVIL') !== false)
                    {
                        if(strlen($arrayData['valor']) != 10)
                        {
                            throw new \Exception("Teléfono Móvil no cumple el formato permitido, debe poseer 10 dígitos:  " . $arrayData['valor']);
                        }
                        else
                        {
                            if(!preg_match('/^(09[0-9]{8})$/', $arrayData['valor']))
                            {
                                throw new \Exception("Teléfono Móvil Incorrecto, no cumple el formato permitido : " . $arrayData['valor']);
                            }
                        }
                    }
                    //FIN validacion correo y telefonos
                    
                    if(is_object($entityFormaContacto))
                    {
                        $entityInfoPuntoFormaContacto = new InfoPuntoFormaContacto();
                        $entityInfoPuntoFormaContacto->setPuntoId($entityPunto);
                        $entityInfoPuntoFormaContacto->setFormaContactoId($entityFormaContacto);
                        $entityInfoPuntoFormaContacto->setValor($arrayData['valor']);
                        $entityInfoPuntoFormaContacto->setEstado("Activo");
                        $entityInfoPuntoFormaContacto->setFeCreacion(new \DateTime('now'));
                        $entityInfoPuntoFormaContacto->setUsrCreacion($arrayData['usuario']);
                        $entityInfoPuntoFormaContacto->setIpCreacion($strIpCreacion);
                       
                        $emComercial->persist($entityInfoPuntoFormaContacto);
                        $emComercial->flush();
                        $emComercial->getConnection()->commit();
                    
                        $arrayRespuesta['status']  = "OK";
                        $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                        
                        
                    }
                    else
                    {
                         throw new \Exception("No se encontró formas de contacto para el idFormaContacto ingresado");
                        
                    }
                 
                    
                    
                }
                elseif($arrayData['proceso'] == "ACTUALIZAR")
                {
                    
                    if (empty($arrayData['idContacto']))
                    {
                       throw new \Exception("Falta la variable idContacto en el request");
                    }
                    
                    $objPunto  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findOneBy(array('login'  => $arrayData['login']));
                    
                    if(empty($objPunto))
                    {
                        throw new \Exception("No se encontró información del punto con el login ingresado");
                    }

                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }

                    //validar pertenencia
                    $objPunto1  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findOneBy(array('login'  => $arrayData['login']));

                    if(is_object($objPunto1))
                    {
                        $arrayContactoPunto    =  $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->getFormasContactoPunto($objPunto1);
                    }
                    else 
                    {
                        throw new \Exception("El login ".$arrayData['login']." ingresado no existe");  
                    }
                    
                    $strCount=0;                                   
                    foreach ($arrayContactoPunto as $array) 
                    {
                        $arrayResponse = $array['idPuntoFormaContacto'];
                        $arrayResponse2 = $array['idPunto'];
                        if (($arrayData['idContacto']==$arrayResponse) && ($objPunto1->getId()==$arrayResponse2)) 
                        {
                            $strCount+=1;                    
                        }
                        else 
                        {
                            $strCoun=0;
                        }
                    }
                    if ($strCount!==1) 
                    {
                        throw new \Exception('No existen idContacto: '.$arrayData['idContacto'].' para el login '.$arrayData['login']);
                    }

                    $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($arrayData['idFormaContacto']);

                    //INICIO validacion correo y telefonos
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false
                        || strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'FACEBOOK') !== false)
                    {
                        $strEmail = $arrayData['valor'];
                        if($this->esEmailValido($arrayData['valor']))
                        {
                            //Verificando el dominio de mail				
                            $arrayMailTemp = explode("@", $arrayData['valor']);
                            $strDominioTemp = $arrayMailTemp[1];

                            if(!$this->verificarMailDNS($strDominioTemp))
                            {
                                throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $strEmail);
                            }
                        }
                        else
                        {
                            throw new \Exception("Dominio del Correo Electronico Incorrecto,"
                                . "No cumple el formato permitido : " . $arrayData['valor']);
                        }
                    }
                    if(strpos(strtoupper($entityFormaContacto->getDescripcionFormaContacto()), 'TELEFONO MOVIL') !== false)
                    {
                        if(strlen($arrayData['valor']) != 10)
                        {
                            throw new \Exception("Teléfono Móvil no cumple el formato permitido, debe poseer 10 dígitos:  " . $arrayData['valor']);
                        }
                        else
                        {
                            if(!preg_match('/^(09[0-9]{8})$/', $arrayData['valor']))
                            {
                                throw new \Exception("Teléfono Móvil Incorrecto, no cumple el formato permitido : " . $arrayData['valor']);
                            }
                        }
                    }
                    //FIN validacion correo y telefonos
                    
                    $objPuntoFormaContacto  = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                          ->findOneBy(array('id'  => $arrayData['idContacto']));
                    
         
                    if(is_object($objPuntoFormaContacto))
                    {
                        //actualizó forma de contacto
                       $objPuntoFormaContacto->setEstado("Inactivo");
                       
                       $emComercial->persist($objPuntoFormaContacto);
                       $emComercial->flush();
                       
                       //insertar nueva forma de contacto
                       $entityPunto  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                  ->findOneBy(array('login'  => $arrayData['login']));
                       if(empty($entityPunto))
                       {
                          throw new \Exception("No se encontró información del punto con el login ingresado");
                       }
                       
                       if(empty($arrayData['idFormaContacto']))
                       {
                          throw new \Exception("Falta la variable idFormaContacto en el request");
                       }
                       
                       if(empty($arrayData['valor']))
                       {
                         throw new \Exception("Falta la variable valor en el request");
                       }
          
                       $entityFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->find($arrayData['idFormaContacto']);
                       
                       if(is_object($entityFormaContacto))
                       {
                          $entityInfoPuntoFormaContacto = new InfoPuntoFormaContacto();
                          $entityInfoPuntoFormaContacto->setPuntoId($entityPunto);
                          $entityInfoPuntoFormaContacto->setFormaContactoId($entityFormaContacto);
                          $entityInfoPuntoFormaContacto->setValor($arrayData['valor']);
                          $entityInfoPuntoFormaContacto->setEstado("Activo");
                          $entityInfoPuntoFormaContacto->setFeCreacion(new \DateTime('now'));
                          $entityInfoPuntoFormaContacto->setUsrCreacion($arrayData['usuario']);
                          $entityInfoPuntoFormaContacto->setIpCreacion($strIpCreacion);
                       
                          $emComercial->persist($entityInfoPuntoFormaContacto);
                          $emComercial->flush();
                          $emComercial->getConnection()->commit();
                    
                          $arrayRespuesta['status']  = "OK";
                          $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                        
                        
                       }
                       else
                       {
                          throw new \Exception("No se encontró formas de contacto para el idFormaContacto ingresado");
                          
                       }
                       
                       
                    }
                    else
                    {
                        
                        throw new \Exception("No se encontró formas de contacto para el idContacto ingresado");
                        
                    }
                       
                    
                }
                elseif($arrayData['proceso'] == "ELIMINAR") 
                {
                
                    if (empty($arrayData['idContacto']))
                    {
                       throw new \Exception("Falta la variable idContacto en el request");
                    }
                    
                    $objPunto  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findOneBy(array('login'  => $arrayData['login']));
                    
                    if(empty($objPunto))
                    {
                        throw new \Exception("No se encontró información del punto con el login ingresado");
                    }
                    //valida si existe el idformacontacto
                    $entityIdFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findOneBy(array('id'  => $arrayData['idFormaContacto']));
                    if(empty($entityIdFormaContacto))
                    {
                        throw new \Exception("IdFormaContacto: ".$arrayData['idFormaContacto']." Incorrecta: ");
                    }

                    //validar pertenencia
                    $objPunto1  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findOneBy(array('login'  => $arrayData['login']));

                    if(is_object($objPunto1))
                    {
                        $arrayContactoPunto    =  $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->getFormasContactoPunto($objPunto1);
                    }
                    else 
                    {
                        throw new \Exception("El login ".$arrayData['login']." ingresado no existe");  
                    }
                    
                    $strCount=0;                                   
                    foreach ($arrayContactoPunto as $array) 
                    {
                        $arrayResponse = $array['idPuntoFormaContacto'];
                        $arrayResponse2 = $array['idPunto'];
                        if (($arrayData['idContacto']==$arrayResponse) && ($objPunto1->getId()==$arrayResponse2)) 
                        {
                            $strCount+=1;                    
                        }
                        else 
                        {
                            $strCoun=0;
                        }
                    }
                    if ($strCount!==1) 
                    {
                        throw new \Exception('No existen idContacto: '.$arrayData['idContacto'].' para el login '.$arrayData['login']);
                    }
                    
                    $objPuntoFormaContacto  = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                          ->findOneBy(array('id'  => $arrayData['idContacto']));
                    
                    if(is_object($objPuntoFormaContacto))
                    {
                     
                        //actualizó forma de contacto
                       $objPuntoFormaContacto->setEstado("Eliminado");
                       
                       $emComercial->persist($objPuntoFormaContacto);
                       $emComercial->flush();
                       $emComercial->getConnection()->commit();
                       
                       $arrayRespuesta['status']  = "OK";
                       $arrayRespuesta['mensaje'] = "Proceso realizado correctamente";
                        
                    }
                    else
                    {
                        throw new \Exception("No se encontró formas de contacto para el idContacto ingresado");
                    }
                       
                       
                    
                }
                
                
            }
            
        } 
        catch (\Exception $ex) 
        {
            
            $serviceUtils->insertError('Telcos+',
                                      'InfoPersonaFormaContactoService->procesoFormasContacto',
                                      $ex->getMessage(),
                                      $arrayData['usuario'],
                                      $strIpCreacion);
            
            if($arrayData['origen']=="PUNTO")
            {
                $strAsuntoCorreo = "Error al actualizar los datos “Contacto punto” del cliente";
                $strWS = "Cambios en la forma de contacto Punto , op: actualizarFormaContacto";
                $strDescripcion = 'WS que actualiza en Telcos los datos “Contacto persona" del cliente';
            }
            elseif($arrayData['origen']=="PERSONA")
            {
                $strAsuntoCorreo = "Error al actualizar los datos “Contacto persona” del cliente";
                $strWS = "Cambios en la forma de contacto Persona , op: actualizarFormaContacto";
                $strDescripcion = 'WS que actualiza en Telcos los datos “Contacto Punto" del cliente';
            }
            
            $arrayParametros = array('strCanal'          => $arrayData['canal'],
                                     'strWS'             => $strWS,
                                     'strDescripcion'    => $strDescripcion,
                                     'strIdentificacion' => $arrayData['identificacion'],
                                     'strOrigen'         => $arrayData['origen'],
                                     'strLogin'          => $arrayData['login'],
                                     'strError'          => $ex->getMessage(),
                                     'strTipoCorreo'     => 'CONTACTO');
            
            $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                                array(), 
                                                                "NOTIF_EXT_CONT", 
                                                                $arrayParametros, 
                                                                $arrayData['codEmpresa'], 
                                                                null, 
                                                                '', 
                                                                null,
                                                                false,
                                                                null);
    
            $arrayRespuesta['mensaje'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            
            $emComercial->getConnection()->rollback();
            
        }
        
        
        return $arrayRespuesta;
    }
    
    
}
