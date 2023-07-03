<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProveedorMedidorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreProveedorMedidor','text',
                    array(
                        'label'=>'* Nombre Proveedor Medidor:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Proveedor Medidor es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionProveedorMedidor','textarea',
                    array(
                        'label'=>'* Descripcion Proveedor Medidor:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Proveedor Medidor es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiproveedormedidortype';
    }
}
