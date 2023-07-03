<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoPlanCaracteristicaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valor','text',array('label'=>'* Valor:','attr' => array('class' => 'campo-obligatorio')))                 
        ;
    }

    public function getName()
    {
        return 'infoplancaracteristicatype';
    }
}
