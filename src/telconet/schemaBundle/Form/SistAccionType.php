<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SistAccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreAccion','text',array('label'=>'* Nombre:','attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Nombre de la accion es requerido",'maxlength'=>50)))
            ->add('urlImagen','text',array('required'=>false,'label'=>'Url Imagen:','attr' => array()))
            ->add('codigo','integer',array('required'=>false,'label'=>'Codigo:','attr' => array()))
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_sistacciontype';
    }
}
