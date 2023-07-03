<?php
 
namespace telconet\seguridadBundle\EventListener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use TelconetSSO\TelconetSSOBundle\Security\Authentication\Token\UserSSOToken;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x
 
/**
* Custom login listener.
*/
class LoginListener
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;
    /** @var \Doctrine\ORM\EntityManager */
    private $em;
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
    /**
    * Constructor
    *
    * @param SecurityContext $securityContext
    * @param Doctrine $doctrine
    * @param Doctrine $session
    */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Session $session)
    {
	$this->securityContext = $securityContext;
	$this->em = $doctrine->getManager();
	$this->emSeguridad = $doctrine->getManager('telconet_seguridad');
	$this->session = $session;
    }
    /**
    * Do the magic.
    *
    * @param InteractiveLoginEvent $event
    */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
	error_log('login listener');
// 	if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
	// user has just logged in
// 	}
	
// 	if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
	// user has logged in using remember_me cookie
// 	}
	// do some other magic here
	$user = $this->securityContext->getToken()->getUser();
	// ...
	$empleado = $this->em->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($user->getUsername());
            
	if($empleado)
	{  
	    //guardo empresas del usuario
	    $arrayEmpresas = $this->em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpresasByPersona($user->getUsername(), "Empleado");
	    
            $this->session->set('arrayEmpresas',$arrayEmpresas);
	    if($arrayEmpresas && count($arrayEmpresas)>0)
	    {
	      $this->session->set('idPersonaEmpresaRol', $arrayEmpresas[0]["IdPersonaEmpresaRol"]);
	      $this->session->set('idEmpresa', $arrayEmpresas[0]["CodEmpresa"]);
	      $this->session->set('empresa', $arrayEmpresas[0]["razonSocial"]);
	      $this->session->set('idOficina', $arrayEmpresas[0]["IdOficina"]);
	      $this->session->set('oficina', $arrayEmpresas[0]["nombreOficina"]);
	      $this->session->set('idDepartamento', $arrayEmpresas[0]["IdDepartamento"]);
	      $this->session->set('departamento', $arrayEmpresas[0]["nombreDepartamento"]);
	    }
	    		    
	    $this->session->set('empleado', $empleado->getNombres().' '.$empleado->getApellidos() );
	    $this->session->set('id_empleado', $empleado->getId() );
	    $this->session->set('user', $empleado->getLogin() );
	    $this->session->set('request_login', 1);
	  
	    
        }
    }
}
