<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiPaisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombrePais','text',
                    array(
                        'label'=>'* Nombre Pais:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Pais es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admipaistype';
    }
}
