<?php 
namespace telconet\soporteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificacionAsignacionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('Notificacion:Asignacion')
            ->setDescription('Envio de notificaciones de asignaciones de tareas')
            ->addArgument('id_comunicacion', InputArgument::OPTIONAL, 'Comunicacion')
            ->addArgument('id_documento', InputArgument::OPTIONAL, 'Documento o correo a enviar')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contenedor = $this->getContainer(); 
        
        $id_comunicacion = $input->getArgument('id_comunicacion');
        $id_documento = $input->getArgument('id_documento');
        
        $emComunicacion = $contenedor->get('doctrine')->getManager('telconet_comunicacion');
        $emComercial = $contenedor->get('doctrine')->getManager('telconet');
        
        $destinatarios = $emComunicacion->getRepository('schemaBundle:InfoDestinatario')->findBy(array('comunicacionId'=>$id_comunicacion));
        
        $to = array();
        foreach($destinatarios as $destinatario){
            $personaFormaContactoId = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->find($destinatario->getPersonaFormaContactoId());
            
            if($personaFormaContactoId){
                if($personaFormaContactoId->getFormaContactoId()->getId()==5)
                    $to[]=$personaFormaContactoId->getValor();
            }
        }
        
        
        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id_documento);
        
        $estado = $this->envioCorreo($contenedor,$documento->getNombreDocumento(),$to,$documento->getMensaje());

	$output->writeln($estado);
        
    } 
    
    public function envioCorreo($contenedor,$asunto,$to,$render){
        $message = \Swift_Message::newInstance()
                    ->setSubject($asunto)
                    ->setFrom('prueba@telconet.ec')
                    ->setTo($to)
                    ->setBody($render,'text/html');

        return $contenedor->get('mailer')->send($message);
    }
    
}