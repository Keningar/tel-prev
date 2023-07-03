<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdmiBancoTipoCuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('totalCaracteres','text',array('required'=>true,'label'=>'* Total Caracteres:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>2, 'class' => 'campo-obligatorio')))         
            ->add('totalCodseguridad','text',array('required'=>true,'label'=>'* Total Cod. Seguridad:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>1, 'class' => 'campo-obligatorio')))         
            ->add('caracterEmpieza','text',array('required'=>true,'label'=>'* Caracter Empieza:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>1 ,'class' => 'campo-obligatorio')))         
            ->add('esTarjeta')
            ->add('bancoId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiBanco',
											   'property'=>'descripcionBanco',
											   'label'=>'* Banco:',
                                                                                           'label_attr' => array('class' => 'campo-obligatorio'),
											   'required' => true,
											   'em'=> 'telconet_general',
											   'empty_value' => 'Seleccione...',
                                                                                           'empty_data'  => null
												))                
            ->add('tipoCuentaId')  
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\AdmiBancoTipoCuenta'
        ));
    }

    public function getName()
    {
        return 'admibancotipocuentatype';
    }
}
