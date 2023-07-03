<?php

namespace telconet\seguridadBundle;

use TelconetSSO\TelconetSSOBundle\DependencyInjection\Security\Factory\SSOFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class seguridadBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
}
