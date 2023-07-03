<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiMarcaElementoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreMarcaElemento','text',
                    array(
                        'label'=>'* Nombre Marca:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre de la Marca es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionMarcaElemento','textarea',
                    array(
                        'label'=>'* Descripcion Marca:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion de la marca es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_marcaelementotype';
    }
}
