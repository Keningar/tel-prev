<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SistModuloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opciones = array('ACTIVE' => 'Activo', 'EDITED' => 'Modificado', 'DELETED' => 'Eliminado');
        $builder
            ->add('nombreModulo','text', array('label' => '* Nombre','attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Nombre del modulo es requerido",'maxlength'=>50)))
            ->add('codigo','text', array('label' => 'codigo','required'=>false))
        ;
    }

    public function getName()
    {
        return 'telconet_seguridadBundle_sistmodulotype';
    }
}
