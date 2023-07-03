<?php

namespace telconet\schemaBundle\Service;

/**
 * Service para envolver funcionalidades de Swift Mailer
 * @see \Swift_Mailer
 */
class MailerService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    private $templating;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
        $this->mailer = $container->get('mailer');
        $this->templating = $container->get('templating');
    }
    
    /**
     * Envia un e-mail en formato HTML
     * @param string $subject asunto del mensaje
     * @param string $from direccion del remitente
     * @param string|array $to direccion del destinatario, o array con direcciones de los destinatarios
     * @param string $html cuerpo del mensaje en formato HTML
     * @return integer numero de destinatarios aceptados para el envio
     */
    public function sendHTML($subject, $from, $to, $html)
    {
        if (is_string($to))
        {
            $to = array($to);
        }
        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($html, 'text/html');
        return $this->mailer->send($message);
    }

    /**
     * Función que sirve para enviar un e-mail en formato HTML con archivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 13-11-2020
     *
     * @param Array  $arrayParametros [
     *                                    'strSubject'  => asunto del mensaje
     *                                    'strFrom'     => dirección del remitente
     *                                    'arrayTo'     => dirección del destinatario o array con direcciones de los destinatarios
     *                                    'strHtml'     => cuerpo del mensaje en formato HTML
     *                                    'strContents' => contenido del archivo
     *                                    'strNameFile' => nombre del archivo
     *                                    'strTypeFile' => tipo de archivo
     *                                 ]
     * @return Integer - numero de destinatarios aceptados para el envió
     */
    public function sendHTMLWithFileContents($arrayParametros)
    {
        if (is_string($arrayParametros['arrayTo']))
        {
            $arrayParametros['arrayTo'] = array($arrayParametros['arrayTo']);
        }
        $objMessage = \Swift_Message::newInstance()
                ->setSubject($arrayParametros['strSubject'])
                ->setFrom($arrayParametros['strFrom'])
                ->setTo($arrayParametros['arrayTo'])
                ->setBody($arrayParametros['strHtml'], 'text/html')
                ->attach(\Swift_Attachment::newInstance($arrayParametros['strContents'],
                        $arrayParametros['strNameFile'],$arrayParametros['strTypeFile']));
        return $this->mailer->send($objMessage);
    }

    /**
     * Envia un e-mail en formato HTML en base a una plantilla TWIG
     * @param string $subject asunto del mensaje
     * @param string $from direccion del remitente
     * @param string|array $to direccion del destinatario, o array con direcciones de los destinatarios
     * @param string $twig nombre de la plantilla TWIG para generar el cuerpo del mensaje en formato HTML
     * @param array $parameters parametros para la plantilla TWIG
     * @return integer numero de destinatarios aceptados para el envio
     */
    public function sendTwig($subject, $from, $to, $twig, array $parameters)
    {
        $html = $this->templating->render($twig, $parameters);
        return $this->sendHTML($subject, $from, $to, $html);
    }

    /**
     * Función que sirve para enviar un e-mail en formato HTML en base a una plantilla TWIG con archivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 13-11-2020
     *
     * @param Array  $arrayParametros [
     *                                    'strSubject'  => asunto del mensaje
     *                                    'strFrom'     => dirección del remitente
     *                                    'arrayTo'     => dirección del destinatario o array con direcciones de los destinatarios
     *                                    'strTwig'     => nombre de la plantilla TWIG para generar el cuerpo del mensaje en formato HTML
     *                                    'arrayParams' => parámetros para la plantilla TWIG
     *                                    'strContents' => contenido del archivo
     *                                    'strNameFile' => nombre del archivo
     *                                    'strTypeFile' => tipo de archivo
     *                                 ]
     * @return Integer - numero de destinatarios aceptados para el envió
     */
    public function sendTwigWithFileContents($arrayParametros)
    {
        $arrayParametros['strHtml'] = $this->templating->render($arrayParametros['strTwig'], $arrayParametros['arrayParams']);
        return $this->sendHTMLWithFileContents($arrayParametros);
    }

    /**
     * Devuelve un array con los correos de la persona del id dado
     * @param integer $personaId
     * @return array
     */
    public function obtenerCorreosPorPersonaId($personaId)
    {
        $correos = array();
        $contactos = $this->emcom->getRepository('schemaBundle:InfoPersonaFormaContacto')->findBy(array(
                        'personaId' => $personaId, 'formaContactoId' => 5, 'estado' => 'Activo'));
        foreach ($contactos as $contacto)
        {
            $correo = trim($contacto->getValor());
            if (!empty($correo))
            {
                $correos[] = $correo;
            }
        }
        return $correos;
    }
    
    /**
     * Devuelve un array con los correos de la persona del login dado
     * @param string $login
     * @return array
     */
    public function obtenerCorreosPorLogin($login)
    {
        $empleados = $this->emcom->getRepository('schemaBundle:InfoPersona')->findBy(array(
                        'login' => $login, 'estado' => 'Activo'));
        $correos = array();
        foreach ($empleados as $empleado)
        {
            $correos = $correos + $this->obtenerCorreosPorPersonaId($empleado->getId());
        }
        return $correos;
    }
    
}
