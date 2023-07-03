<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiDetalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreDetalle','text',
                    array(
                        'label'=>'* Nombre Detalle:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"nombre es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('tipo','text',
                    array(
                        'label'=>'* Tipo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Tipo es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionDetalle','textarea',
                    array(
                        'label'=>'* Descripcion Detalle:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Detalle es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admidetalletype';
    }
}
