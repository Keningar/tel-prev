<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiImpuestoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                
            ->add('tipoImpuesto','text',
                    array(
                        'label'=>'* Tipo Impuesto:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Tipo Impuesto es requerido",
                            'maxlength'=>3, 'size'=>3
                            )
                         )
                 )
                
            ->add('descripcionImpuesto','text',
                    array(
                        'label'=>'* Descripcion Impuesto:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Impuesto es requerido",
                            'maxlength'=>50)
                         )
                 )
                
            ->add('codigoSri','text',
                    array(
                        'label'=>'* Codigo Sri:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Codigo Sri es requerido",
                            'maxlength'=>10)
                         )
                 )
                
                
            ->add('porcentajeImpuesto','text',
                    array(
                        'label'=>'* Porcentaje Impuesto:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Porcentaje Impuesto es requerido",
                            'maxlength'=>7)
                         )
                 )
                
            ->add('cuentaContable','text',
                    array(
                        'label'=>'* Cuenta Contable:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Cuenta Contable es requerido",
                            'maxlength'=>15)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiimpuestotype';
    }
}
