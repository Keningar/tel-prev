<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiFormaContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcionFormaContacto','text',
                    array(
                        'label'=>'* Descripcion Forma Contacto:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion de la Forma de Contacto es requerido",
                            'maxlength'=>80)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiformacontactotype';
    }
}
