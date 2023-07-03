<?php
namespace telconet\seguridadBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use TelconetSSO\TelconetSSOBundle\Security\Authentication\Token\UserSSOToken;
use TelconetSSO\TelconetSSOBundle\Entity\User;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class TokenListener
{
    protected function obtenerRoles($id,$controller)
    {
        $roles = $controller->getDoctrine()
            ->getManager()
            ->getRepository('schemaBundle:SeguAsignacion')
            ->getRolesXEmpleado($id);

        return $roles;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            $session = $controller[0]->get( 'session' );

			//var_dump($session);
// 	    echo "modulo activo " . $session->get('modulo_activo');
// 	    echo " -- menu modulo activo " . $session->get('menu_modulo_activo');
			
            $emComercial = $controller[0]->getDoctrine()->getManager("telconet");
            $user = $controller[0]->get('security.context')->getToken()->getUser();
			/*
			if($user->getUsername() == "rsaenz")
			{
				$user->setUsername("eguerra");
				//echo $user->getUsername();		
			}*/
				
            $empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($user->getUsername());
            
            if($empleado)
            {   
		$arrayEmpresas = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpresasByPersona($user->getUsername(), "Empleado");
                if($session->get('idEmpresa') && $session->get('empresa'))
		{
			//NADA
		}
		else
		{
			if($arrayEmpresas && count($arrayEmpresas)>0)
			{
			  $session->set('idPersonaEmpresaRol', $arrayEmpresas[0]["IdPersonaEmpresaRol"]);
			  $session->set('idEmpresa', $arrayEmpresas[0]["CodEmpresa"]);
			  $session->set('empresa', $arrayEmpresas[0]["razonSocial"]);
			  $session->set('idOficina', $arrayEmpresas[0]["IdOficina"]);
			  $session->set('oficina', $arrayEmpresas[0]["nombreOficina"]);
			  $session->set('idDepartamento', $arrayEmpresas[0]["IdDepartamento"]);
			  $session->set('departamento', $arrayEmpresas[0]["nombreDepartamento"]);
			}
		}
		$i=0;
                $roles = $this->obtenerRoles($empleado->getId(),$controller[0]);
		foreach($roles as $rol):				
				$nombreRol = 'ROLE_'.$rol["modulo_id"].'-'.$rol["accion_id"];
				$user->addRol($nombreRol);
				$user->setRole($nombreRol);
				$i++;
		endforeach;
// 		echo "entre ".$i." veces";	
				
                $session->set('empleado', $empleado->getNombres().' '.$empleado->getApellidos() );
                $session->set('id_empleado', $empleado->getId() );
                $session->set('user', $empleado->getLogin() );
                $user->setNombres($empleado->getNombres().' '.$empleado->getApellidos());

                $tokensen= new UserSSOToken($user->getUsername(),$user->getRoles());
                
                $controller[0]->get( 'security.context' )->setToken($tokensen);
                $controller[0]->get( 'security.context' )->getToken()->setUser($user);
                $controller[0]->get( 'security.context' )->getToken()->setAuthenticated(true);
				
		//******************** ROLES OTORGADOS A ESTE USUARIO *********************
		$rolesPermitidos = $controller[0]->get( 'security.context' )->getToken()->getUser()->getRoles();
		sort($rolesPermitidos);
		$rolesPermitidos = array_unique($rolesPermitidos);
                $session->set('rolesPermitidos',$rolesPermitidos);
            }
            
        }
    }
}
