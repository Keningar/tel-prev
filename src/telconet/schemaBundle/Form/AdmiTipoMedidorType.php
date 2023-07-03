<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoMedidorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoMedidor','text',
                    array(
                        'label'=>'* Nombre Tipo Medidor:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Tipo Medidor es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionTipoMedidor','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Medidor:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Medidor es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipomedidortype';
    }
}
