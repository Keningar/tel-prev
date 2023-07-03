<?php

namespace telconet\soporteBundle\Service;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class EnvioPlantillaService {
  
    private $emComunicacion;
    private $emGeneral;
    private $templating;
    private $mailer;
    private $adjunto;
    private $path;
    private $remitente;
    private $mailerSend;
    /* ==========================================
               INJECCION DE SERVICES           
     ==========================================*/
    /* @var $utilService UtilService */
    private $utilService;
    /* ========================================*/
    
    public function __construct(Container $container)
    {             
        $this->emComunicacion    = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral         = $container->get('doctrine')->getManager('telconet_general');
        $this->emSoporte         = $container->get('doctrine')->getManager('telconet_soporte');
        $this->templating        = $container->get('templating');
        $this->mailer            = $container->get('mailer');
        $this->path              = $container->getParameter('path_telcos');
        $this->remitente         = 'notificaciones_telcos@telconet.ec';
        $this->mailerSend        = $container->getParameter('mailer_send');
        $this->utilService       = $container->get('schema.Util');
    }

    /**
     * generarEnvioPlantilla
     *
     * Metodo encargado enviar las notificaciones via correo con información según el proceso que la invoque.
     * Buscará la plantilla en la ADMI_PLANTILLA según un codigo ( proceso ) y buscará los alias segun la empresa, 
     * la ciudad, el departamento hacia donde se que desea despachar
     *     
     * @param string $asunto     
     * @param array $to
     * @param string $codigoPlantilla
     * @param array $parametros
     * @param string $codEmpresa
     * @param integer $idCiuda
     * @param integer $idDepartamento
     * @param string $adjunto        
     * @param boolean $anadirCC     
     * @param string $remitente
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.0 16-09-2014 - Actualizacion y mejora ( Creacion de hash identificador para nombrar a cada 
     *                                                    plantilla temporal que es creada al momento de generar un correo)
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.1 20-06-2016 - Actualizacion y mejora ( Se quita el código de empresa de las plantillas porque son genéricas )
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.5 14-11-2014 - Actualizacion y mejora ( Se añade variable anadirCC que determina si ciertos correos requieren enviar
     *                                                    copias adicionales a alias ingresadas en las plantillas para este fin de 
     *                                                    acuderdo a determinado evento)
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 3.0 08-04-2015 - Actualizacion y mejora ( Se añade variable remitente que determina quien despacha el correo de acuerdo
     *                                                    al proceso que se este realizando )
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.1 14-04-2016 - Se quita la cuenta notificaciones_telcos@telconet.ec como destinatario
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.2 25-05-2016 - Se realizan ajustes para enviar un correo a todos involucrados de una tarea que pertenece a un caso
     * 
     * @author Jose Vinueza <jdvinueza@telconet.ec>
     * @version 3.3 29-03-2018 - Se realizan ajustes para la parametrizacion del remitente para el envio del correo
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.4 19-12-2018 - Se modifica validación para poder obtener los correos destinatarios de manera correcta
     * @since 3.3
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 3.5 18-01-2019 - Se valida el envío como remitente de correo a Netvoice.
     * @since 3.3
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 3.6 12-08-2019 - Se valida el envío como remitente de correo a Soporte ECUCERT.
     * @since 3.5
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 3.7 07-10-2019 - Se valida el envío como remitente de correo a Netlife ECUCERT.
     * @since 3.6
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 3.8 11-11-2019 - Se valida el envío como remitente de correo a Netlife NETCAM.
     * @since 3.7
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 3.9 30-11-2020 - Se valida que el tipoAsignado no se EMPRESAEXTERNA para obtener alias
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.0 06-12-2019 - Se valida el envío como remitente de correo a Netlife SOPORTE
     * @since 3.9
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 4.1 28-09-2021 - Se agrega lógica para plantillas que contengan caracteres 
     * especiales, la lógica solo aplica a códigos de plantillas parametrizadas.
     * @since 4.0
     *
     */        
    public function generarEnvioPlantilla($asunto, 
                                          $to = null, 
                                          $codigoPlantilla = '', 
                                          $parametros = '', 
                                          $codEmpresa = '', 
                                          $idCiudad = '', 
                                          $idDepartamento = '', 
                                          $adjunto = null,
                                          $anadirCC = false,
                                          $strEmpresaRemitente = 'notificaciones_telcos@telconet.ec',
                                          $strTipoAsignado  = '' )
    {

        $this->adjunto   = $adjunto;
        $arrayParametros =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('remitente',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      $codEmpresa);
	
	    if($strEmpresaRemitente == 'notificaciones@netvoice.ec' || $strEmpresaRemitente == 'soporte@telconet.ec'
	       || $strEmpresaRemitente == 'notificaciones_seguridad@netlife.net.ec'
	       || $strEmpresaRemitente == 'netcam@netlife.info.ec' || $strEmpresaRemitente == 'soporte@netlife.net.ec')
        {
            $this->remitente = $strEmpresaRemitente;
        }
        else
        {
            $this->remitente = ( isset($arrayParametros['valor1']) && !empty($arrayParametros['valor1']) ) ?
                                $arrayParametros['valor1'] : $strEmpresaRemitente;
        }
        
        /*
         * Ruta raiz donde la plantilla temporal será creada para el despacho del correo
         */
        
        $strPathPlantilla = $this->path."telcos/src/telconet/soporteBundle/Resources/views/Default/";
        //Se valida si la plantilla no tiene permitido que se le agregue como destinatario el notificaciones_telcos@telconet.ec
        $parametroPlantilla = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('PLANTILLAS DE NOTIFICACIONES',
                                                       'SOPORTE',
                                                       'NOTIFICACIONES',
                                                       'CODIGO DE PLANTILLA',
                                                       $codigoPlantilla,
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       '');
        if(!isset($parametroPlantilla))
        {
            if (isset($parametros["empresa"]) && $parametros["empresa"] == "TN" )
            {
             	$to[] = "notificaciones_telcos@telconet.ec";
            }
        }

        if($codigoPlantilla && $codigoPlantilla != '')
        {

            $AdmiPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->getPlantillaXCodigoYEmpresa($codigoPlantilla);

            
            /*
             * Cadena que identifica de manera unica a cada plantilla en un instante de tiempo en que se
             * requiera enviar algun correo evitando que se use una misma plantilla a la vez
             */
            $strTokenIdentificador = date("G") . '' . date('i') . '' . date('s') . substr(md5(uniqid(rand())),0,6);    
            
            $file = $strPathPlantilla."plantillaCorreo" . $strTokenIdentificador . ".html.twig";
            
            if($AdmiPlantilla)
            {
                $html = $AdmiPlantilla[0]->getPlantilla();

                /*
                  ESCRIBE UN ARCHIVO DIFERENTE CADA  VEZ Y LUEGO ES BORRADO
                 */

                $archivo = fopen($file, "w"); // or die("Problemas en la creacion");
                if($archivo)
                {
                    try
                    {

                        chmod($file, 777);

                        fwrite($archivo, $html);

                        fclose($archivo);

                        $band = "1";
                        //Si es plantilla de Finalizar Tarea y es de TN, se debe enviar la notificacion a todos los involucrados
                        if($codigoPlantilla == "TAREAFINALIZA" && $parametros["empresa"] == "TN" && $parametros["idCaso"])
                        {
                            $involucrados  = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                             ->getInvolucradosTarea($parametros["idCaso"]);

                            foreach($involucrados as $involucrado)
                            {
                                if($involucrado["asignadoId"])
                                {
                                    //Se busca los alias establecidos bajo proceso normal
                                    $alias = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                  ->getAliasXPlantilla($AdmiPlantilla[0]->getId(),$codEmpresa,$idCiudad,
                                                                       $involucrado["asignadoId"],"NO");

                                    if($alias)
                                    {
                                        $band = "0";
                                        $to = array_merge($to, $alias);
                                    }
                                    $to = array_unique($to);
                                }
                            }
                        }
                        else
                        {
                            if(empty($strTipoAsignado) || ($strTipoAsignado !== 'EMPRESAEXTERNA' &&  $idDepartamento  !== ''))
                            {
                                //Se busca los alias establecidos bajo proceso normal
                                $alias = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                              ->getAliasXPlantilla($AdmiPlantilla[0]->getId(), $codEmpresa, $idCiudad, $idDepartamento,"NO");
                            }
                        }

                        //Se busca adicional los correos determinados como esCopia para la plantilla requerida
                        $aliasEsCopia = null;

                        if($anadirCC==true)
                        {
                            $aliasEsCopia = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                 ->getAliasXPlantilla($AdmiPlantilla[0]->getId(), 
                                                                      $codEmpresa, 
                                                                      '', 
                                                                      '',
                                                                      "SI"
                                                                      );
                        }
                        if($alias && $band == "1")
                        {
                            if (empty($to))
                            {
                                $to = $alias;
                            }
                            else
                            {
                                $to = array_merge($to, $alias);
                            }
                        }
                        
                        if($aliasEsCopia)
                        {
                            $to = array_merge($to, $aliasEsCopia);
                        }

                        $strPlantillaHtml = "";

                        $arrayCodigosPlantillasHtmlSpecialChars = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->get(  'PLANTILLAS_CON_CARACTERES_ESPECIALES', 
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $empresaId);

                        if(isset($arrayCodigosPlantillasHtmlSpecialChars) && !empty($arrayCodigosPlantillasHtmlSpecialChars))
                        {
                            foreach($arrayCodigosPlantillasHtmlSpecialChars as $arrayCodigoPlantillaHtmlSpecialChars)
                            {
                                $arrayCodigosPermitidosHtmlSpecialChars[] = $arrayCodigoPlantillaHtmlSpecialChars["valor1"];
                            }
                        }

                        if(isset($arrayCodigosPermitidosHtmlSpecialChars) && 
                            !empty($arrayCodigosPermitidosHtmlSpecialChars) && 
                            in_array($codigoPlantilla, $arrayCodigosPermitidosHtmlSpecialChars))
                        {
                            $strPlantillaHtml =    $parametros!=''? 
                                                    htmlspecialchars_decode($this->templating
                                                        ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig',
                                                         $parametros), 
                                                        ENT_NOQUOTES):
                                                    $this->templating
                                                        ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig');
                        }
                        else
                        {
                            $strPlantillaHtml =    $parametros!=''? 
                                                    $this->templating
                                                        ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig',
                                                         $parametros):
                                                    $this->templating
                                                        ->render('soporteBundle:Default:plantillaCorreo' . $strTokenIdentificador . '.html.twig');
                        }

                        $this->enviarCorreo(
                            $asunto, 
                            $to, 
                            $strPlantillaHtml
                        );

                        unlink($file);
                    }
                    catch(Exception $e)
                    {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * enviarCorreo
     *
     * Metodo encargado enviar los correos usando el switfMailer, recibe el asunto los alias y el html
     *     
     * @param string $asunto     
     * @param array $to
     * @param string $mensaje    
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.0 01-09-2014 - Actualizacion (Se añade documentos adjunto en caso de ser requerido)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 2.1 21-05-2016 - Se agrega parametro para que detecte si debe o no enviar correo
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 2.2 13-04-2018 - Se agraga validacion para destinatario de correo
     * 
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 2.3 26-11-2019 - Se agrega valor de retorno, tryCatch y logueo.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 07-12-2020 - Se agrega logica para ajuntar multiples archivos
     */
    public function enviarCorreo($asunto, $to, $mensaje, $strAdjunto = null)
    {
        $boolResponseMail    = false;
        $arrayParametrosLog  = array();
        try
        {
            if($this->mailerSend == "true")
            {
                if($to)
                {
                    foreach($to as $correo)
                    {
                        if($correo != null && $correo != "")
                        {
                            if(strlen($correo) > 5)
                            {
                                $correos[] = trim($correo);
                            }
                        }
                    }
                 }
                if (count($correos) > 0)
                {               
                    $message = \Swift_Message::newInstance()
                                                ->setSubject($asunto)
                                                ->setFrom($this->remitente)
                                                ->setTo($correos)
                                                ->setBody($mensaje, 'text/html');

                    /* Si el mensaje debe contener archivo adjunto este es enviado junto con el correo */
                    if($this->adjunto && count($this->adjunto) > 1)
                    {
                        for($intI = 0; $intI < count($this->adjunto); $intI++)
                        {
                            $message->attach(\Swift_Attachment::fromPath($this->adjunto[$intI]));
                        }
                    }
                    else
                    {
                        if($this->adjunto)
                        {
                            $message->attach(\Swift_Attachment::fromPath($this->adjunto));
                        }
                    }
                    if(!is_null($strAdjunto))
                    {
                        $message->attach(\Swift_Attachment::fromPath($strAdjunto));
                    }


                    $boolResponseMail = $this->mailer->send($message);
                
                }
                else
                {
                    $boolResponseMail                       = false;
                    $arrayParametrosLog['enterpriseCode']   = "18";
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "EnvioPlantillaService";
                    $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
                    $arrayParametrosLog['appMethod']        = "enviarCorreo";
                    $arrayParametrosLog['appAction']        = "validar destinatario";
                    $arrayParametrosLog['messageUser']      = "";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = "No se recibió ningún destinatario";
                    $arrayParametrosLog['inParameters']     = $asunto.";".$to.";".$mensaje.";".$strAdjunto;
                    $arrayParametrosLog['creationUser']     = "TELCOS";
                    $this->utilService->insertLog($arrayParametrosLog); 
                }
            }
            else
            {
                $boolResponseMail                       = false;                
                $arrayParametrosLog['enterpriseCode']   = "18";
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "EnvioPlantillaService";
                $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
                $arrayParametrosLog['appMethod']        = "enviarCorreo";
                $arrayParametrosLog['appAction']        = "noDefinido";
                $arrayParametrosLog['messageUser']      = "No está habilitado el envío de correo";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = "No está habilitado el envío de correo";
                $arrayParametrosLog['inParameters']     = $asunto.";".$to.";".$mensaje.";".$strAdjunto;
                $arrayParametrosLog['creationUser']     = "TELCOS";
                $this->utilService->insertLog($arrayParametrosLog);
            }
        }
        catch(\Exception $e)
        {
            $boolResponseMail                       = false;         
            $arrayParametrosLog['enterpriseCode']   = "18";
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "EnvioPlantillaService";
            $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
            $arrayParametrosLog['appMethod']        = "enviarCorreo";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error al tratar de enviar el correo";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = $asunto.";".$to.";".$mensaje.";".$strAdjunto;
            $arrayParametrosLog['creationUser']     = "TELCOS";
            $this->utilService->insertLog($arrayParametrosLog);
        }
        return $boolResponseMail;
    }

    /**
     * enviarCorreoFrom
     *
     * Metodo encargado enviar los correos usando el switfMailer, recibe el asunto, recibe destinatario
     * recibe remitente, los alias y el html
     *
     * @param array  $arrayParams
     * @param string $strAdjunto
     *
     * @author Emmanuel Martillo Siavichay  <emartillo@telconet.ec>
     * @version 1.0 24-05-2023
     */
    public function enviarCorreoFrom($arrayParams, $strAdjunto = null)
    {
        $strAsunto  = $arrayParams['strAsunto'];
        $strFrom    = $arrayParams['strFrom'];
        $arrayTo    = $arrayParams['arrayTo'];
        $strMensaje = $arrayParams['strMensaje'];
        $boolResponseMail    = false;
        $arrayParametrosLog  = array();
        try
        {
            if($this->mailerSend == "true")
            {
                if($arrayTo)
                {
                    foreach($arrayTo as $strCorreo)
                    {
                        if(($strCorreo != null && $strCorreo != "") && strlen($strCorreo) > 5)
                        {
                                $arrayCorreos[] = trim($strCorreo);
                        }
                    }
                }
                if (count($arrayCorreos) > 0)
                {
                    $objMessage = \Swift_Message::newInstance()
                        ->setSubject($strAsunto)
                        ->setFrom($strFrom)
                        ->setTo($arrayCorreos)
                        ->setBody($strMensaje, 'text/html');

                    /* Si el mensaje debe contener archivo adjunto este es enviado junto con el correo */
                    if($this->adjunto && count($this->adjunto) > 1)
                    {
                        for($intI = 0; $intI < count($this->adjunto); $intI++)
                        {
                            $objMessage->attach(\Swift_Attachment::fromPath($this->adjunto[$intI]));
                        }
                    }
                    else
                    {
                        if($this->adjunto)
                        {
                            $objMessage->attach(\Swift_Attachment::fromPath($this->adjunto));
                        }
                    }
                    if(!is_null($strAdjunto))
                    {
                        $objMessage->attach(\Swift_Attachment::fromPath($strAdjunto));
                    }


                    $boolResponseMail = $this->mailer->send($objMessage);

                }
                else
                {
                    $boolResponseMail                       = false;
                    $arrayParametrosLog['enterpriseCode']   = "18";
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = "EnvioPlantillaService";
                    $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
                    $arrayParametrosLog['appMethod']        = "enviarCorreo";
                    $arrayParametrosLog['appAction']        = "validar destinatario";
                    $arrayParametrosLog['messageUser']      = "";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['descriptionError'] = "No se recibió ningún destinatario";
                    $arrayParametrosLog['inParameters']     = $strAsunto.";".$arrayTo.";".$strMensaje.";".$strAdjunto;
                    $arrayParametrosLog['creationUser']     = "TELCOS";
                    $this->utilService->insertLog($arrayParametrosLog);
                }
            }
            else
            {
                $boolResponseMail                       = false;
                $arrayParametrosLog['enterpriseCode']   = "18";
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "EnvioPlantillaService";
                $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
                $arrayParametrosLog['appMethod']        = "enviarCorreo";
                $arrayParametrosLog['appAction']        = "noDefinido";
                $arrayParametrosLog['messageUser']      = "No está habilitado el envío de correo";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = "No está habilitado el envío de correo";
                $arrayParametrosLog['inParameters']     = $strAsunto.";".$arrayTo.";".$strMensaje.";".$strAdjunto;
                $arrayParametrosLog['creationUser']     = "TELCOS";
                $this->utilService->insertLog($arrayParametrosLog);
            }
        }
        catch(\Exception $e)
        {
            $boolResponseMail                       = false;
            $arrayParametrosLog['enterpriseCode']   = "18";
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "EnvioPlantillaService";
            $arrayParametrosLog['appClass']         = "EnvioPlantillaService";
            $arrayParametrosLog['appMethod']        = "enviarCorreo";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error al tratar de enviar el correo";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = $strAsunto.";".$arrayTo.";".$strMensaje.";".$strAdjunto;
            $arrayParametrosLog['creationUser']     = "TELCOS";
            error_log("Errores ".print_r($arrayParametrosLog));
        }
        return $boolResponseMail;
    }

}