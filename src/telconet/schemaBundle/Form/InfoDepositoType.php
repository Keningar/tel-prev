<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoDepositoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bancoNafId')
            ->add('noCuentaBancoNaf')
            ->add('noCuentaContableNaf')
            ->add('noComprobanteDeposito')
            ->add('valor')
            ->add('feDeposito')
            ->add('feAnulado')
            ->add('feProcesado')
            ->add('feCreacion')
            ->add('feUltMod')
            ->add('usrCreacion')
            ->add('usrProcesa')
            ->add('usrAnula')
            ->add('usrUltMod')
            ->add('estado')
            ->add('ipCreacion')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoDeposito'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_infodepositotype';
    }
}
