<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiClaseMedidorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreClaseMedidor','text',
                    array(
                        'label'=>'* Nombre Clase Medidor:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Clase Medidor es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionClaseMedidor','textarea',
                    array(
                        'label'=>'* Descripcion Clase Medidor:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Clase Medidor es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiclasemedidortype';
    }
}
