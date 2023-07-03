<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProtocoloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreProtocolo','text',array('label'=>'* Nombre:',
                'attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Nombre de Protocolo es requerido",'maxlength'=>10)))
            ->add('descripcionProtocolo','text',array('label'=>'* Descripcion:',
                'attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Descripcion es requerido",'maxlength'=>30)))
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiprotocolotype';
    }
}