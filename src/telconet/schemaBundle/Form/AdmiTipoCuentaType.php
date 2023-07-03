<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoCuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcionCuenta','text',
                    array(
                        'label'=>'* Descripcion Tipo Cuenta:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Cuenta es requerido",
                            'maxlength'=>30)
                         )
                 )
                ->add('esTarjeta', 'choice', array(
                    'choices'   => array('' => 'Seleccione','S' => 'Si', 'N' => 'No'),
                    'required'  => true,
                    'label'=>'* Es Tarjeta:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Este campo es requerido",
                        'maxlength'=>30)                    
                    ))                
                ->add('visibleFormato', 'choice', array(
                    'choices'   => array('' => 'Seleccione','S' => 'Si', 'N' => 'No'),
                    'required'  => true,
                    'label'=>'* Visible Formato Debito:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Este campo es requerido",
                        'maxlength'=>30)                    
                    ))                
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipocuentatype';
    }
}
