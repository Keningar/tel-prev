<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiConectorInterfaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreConectorInterface','text',
                    array(
                        'label'=>'* Nombre Conector:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Conector es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionConectorInterface','textarea',
                    array(
                        'label'=>'* Descripcion Conector:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Conector es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiconectorinterfacetype';
    }
}
