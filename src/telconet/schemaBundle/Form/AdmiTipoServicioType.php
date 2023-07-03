<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoServicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigoServicio','text',array('label'=>'* Codigo:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>4)))
            ->add('descripcionServicio','text',array('label'=>'* Descripcion:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>30)))
        ;
    }

    public function getName()
    {
        return 'telcos_adminbundle_admitiposerviciotype';
    }
}
