<?php

namespace telconet\inicioBundle;

use TelconetSSO\TelconetSSOBundle\DependencyInjection\Security\Factory\SSOFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class inicioBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);


    }
}
