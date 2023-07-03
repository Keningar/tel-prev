<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoCasoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoCaso','text',
                    array(
                        'label'=>'* Nombre Tipo Caso:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo Caso es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTipoCaso','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Caso:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Caso es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipocasotype';
    }
}
