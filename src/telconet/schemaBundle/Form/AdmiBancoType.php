<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiBancoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesCabecera = array('N' => 'NO', 'S' => 'SI');
        
        $builder
            ->add('descripcionBanco','text',
                    array(
                        'label'=>'* Descripcion Banco:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Banco es requerido",
                            'maxlength'=>60)
                         )
                 )
                                                                
             ->add('requiereNumeroDebito', 'choice', array(
                        'label'=>'* Requiere Numero Debito:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Requiere Numero Debito es requerido"
                            )
                        )
                  )
                                                                 
             ->add('generaDebitoBancario', 'choice', array(
                        'label'=>'* Genera Debito Bancario:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Genera Debito Bancario es requerido"
                            )
                        )
                  )
                
            ->add('numeroCuentaContable','text',
                    array(
                        'label'=>'Numero Cuenta Contable',
                        'required' => false,
                        'attr' => array(
                            'maxlength'=>20)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admibancotype';
    }
}
