<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoRolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcionTipoRol','text',
                    array(
                        'label'=>'* Descripcion Tipo Rol:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Rol es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitiporoltype';
    }
}
