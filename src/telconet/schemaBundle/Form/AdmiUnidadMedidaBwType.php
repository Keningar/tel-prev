<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiUnidadMedidaBwType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreBw')
            ->add('conversion')
            ->add('feCreacion')
            ->add('feUltMod')
            ->add('usrCreacion')
            ->add('usrUltMod')
            ->add('codigoUnidad')
            ->add('estado')
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiunidadmedidabwtype';
    }
}
