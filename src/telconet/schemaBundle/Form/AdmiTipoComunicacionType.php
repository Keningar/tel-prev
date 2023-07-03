<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoComunicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoComunicacion','text',
                    array(
                        'label'=>'* Nombre Tipo Comunicacion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo Comunicacion es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTipoComunicacion','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Comunicacion:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Comunicacion es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipocomunicaciontype';
    }
}
