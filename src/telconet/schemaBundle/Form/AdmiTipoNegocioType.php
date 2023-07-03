<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoNegocioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigoTipoNegocio','text',array('label'=>'Codigo:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>4)))
            ->add('nombreTipoNegocio','text',array('label'=>'Nombre:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>30)))
            //->add('feCreacion')
            //->add('usrCreacion')
            //->add('feUltMod')
            //->add('usrUltMod')
            //->add('estado')
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitiponegociotype';
    }
}
