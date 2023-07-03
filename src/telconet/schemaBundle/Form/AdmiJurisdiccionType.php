<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiJurisdiccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('nombreJurisdiccion','text',
                    array(
                        'label'=>'* Nombre Jurisdiccion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionJurisdiccion','textarea',
                    array(
                        'label'=>'* Descripcion Jurisdiccion:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion de la Jurisdiccion es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admijurisdicciontype';
    }
}
