<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiNivelCriticidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreNivelCriticidad','text',
                    array(
                        'label'=>'* Nombre Nivel Criticidad:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Nivel Criticidad es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionNivelCriticidad','textarea',
                    array(
                        'label'=>'* Descripcion Nivel Criticidad:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Nivel Criticidad es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_adminivelcriticidadtype';
    }
}
