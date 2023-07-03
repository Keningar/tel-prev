<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TelconetSSO\TelconetSSOBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class TelconetSSOFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('server_url');
        $this->addOption('login_url');
        $this->addOption('JOSSO_SESSIONID');

        $this->addOption('gateway_login_url');
        $this->addOption('gateway_login_url_canales');
        $this->addOption('gateway_logout_url');
        $this->addOption('gateway_logout_url_canales');
        $this->addOption('service_endpoint_url');
        $this->addOption('service_endpoint_url_canales');
        $this->addOption('proxy_host');
        $this->addOption('proxy_port');
        $this->addOption('proxy_username');
        $this->addOption('proxy_password');
        $this->addOption('success_signin_url');
        $this->addOption('success_signout_url');
        $this->addOption('security_check_module');
        $this->addOption('sso_ips_canales', array());

/*
gateway_login_url: https://192.168.240.66:8443/josso/signon/login.do    
    gateway_logout_url: https://192.168.240.66:8443/josso/signon/logout.do
    service_endpoint_url: https://192.168.240.66:8443
        proxy_host: 
    proxy_port:
    proxy_username:
    proxy_password:
        success_signin_url: @homepage
    success_signout_url: @homepage

    security_check_module: /check
*/
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'fos_telconet';
    }

    protected function getListenerId()
    {
        return 'fos_telconet.security.authentication.listener';
    }

    /**
     * Se modifica metodo addConfiguration para que maneje correctamente los array definidos en el archivo YML
     *
     * @see \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory::addConfiguration()
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.0 Version Inicial
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $builder = $node->children();

        $builder
            ->scalarNode('provider')->end()
            ->booleanNode('remember_me')->defaultTrue()->end()
            ->scalarNode('success_handler')->end()
            ->scalarNode('failure_handler')->end()
        ;

        foreach (array_merge($this->options, $this->defaultSuccessHandlerOptions, $this->defaultFailureHandlerOptions) as $name => $default) {
            if (is_bool($default)) {
                $builder->booleanNode($name)->defaultValue($default);
            } else if (is_array($default)) {
                $builder->arrayNode($name)->prototype('scalar');
            } else {
                $builder->scalarNode($name)->defaultValue($default);
            }
        }
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        // with user provider
        if (isset($config['provider'])) {
            $authProviderId = 'fos_telconet.auth.'.$id;

            $container
                ->setDefinition($authProviderId, new DefinitionDecorator('fos_telconet.auth'))
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.user_checker'))
            ;

            return $authProviderId;
        }

        // without user provider
        return 'fos_telconet.auth';
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPointId)
    {
        $entryPointId = 'fos_telconet.security.authentication.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('fos_telconet.security.authentication.entry_point'))
            //->replaceArgument(1, $config)
        ;

        // set options to container for use by other classes
        $container->setParameter('fos_telconet.options.'.$id, $config);

        return $entryPointId;
    }

//     protected function createListener($container, $id, $config, $userProvider)
//     {
//         $listenerId = $this->getListenerId();
//         $listener = new DefinitionDecorator($listenerId);
//         $listener->replaceArgument(4, $id);
//         $listener->replaceArgument(7, array_intersect_key($config, $this->options));
//
//         // success handler
//         if (isset($config['success_handler'])) {
//             $listener->replaceArgument(5, new Reference($config['success_handler']));
//         }
//
//         // failure handler
//         if (isset($config['failure_handler'])) {
//             $listener->replaceArgument(6, new Reference($config['failure_handler']));
//         }
//
//         $listenerId .= '.'.$id;
//         $container->setDefinition($listenerId, $listener);
//
//         return $listenerId;
//     }

}
