<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new telconet\seguridadBundle\seguridadBundle(),
            new telconet\schemaBundle\schemaBundle(),
            new telconet\adminBundle\adminBundle(),
            new telconet\catalogoBundle\catalogoBundle(),
            new telconet\comercialBundle\comercialBundle(),
            new telconet\administracionBundle\administracionBundle(),
            new telconet\soporteBundle\soporteBundle(),
            new telconet\financieroBundle\financieroBundle(),
            new telconet\tecnicoBundle\tecnicoBundle(),
            new telconet\planificacionBundle\planificacionBundle(),
            new telconet\contabilizacionesBundle\contabilizacionesBundle(),
            new TelconetSSO\TelconetSSOBundle\TelconetSSOTelconetSSOBundle(),
//             new LDAP\LDAPAuthBundle\LDAPAuthBundle(),	    
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new telconet\inicioBundle\inicioBundle(),
            new telconet\searchBundle\searchBundle(),
            // Web Services
            new BeSimple\SoapBundle\BeSimpleSoapBundle(),
            new telconet\ayudaBundle\ayudaBundle(),
            new telconet\comunicacionesBundle\comunicacionesBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new telconet\generalBundle\generalBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
