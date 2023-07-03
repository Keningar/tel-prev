<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdmiFormatoDebitoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion','text',array('required'=>false,'label'=>'* Descripcion:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))        
            ->add('tipoCampo','text',array('required'=>false,'label'=>'* Tipo Campo:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))
            ->add('contenido','text',array('required'=>false,'label'=>'* Contenido:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))
            ->add('longitud','text',array('required'=>false,'label'=>'* Longitud:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))
            ->add('caracterRelleno','text',array('required'=>false,'label'=>'* Caracter de Relleno:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))
            ->add('orientacionCaracterRelleno','text',array('required'=>false,'label'=>'* Orientacion Caracter:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))                
            ->add('variableFormatoId','text',array('required'=>false,'label'=>'* Variable:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => '','onChange'=>'')))			
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\AdmiFormatoDebito'
        ));
    }

    public function getName()
    {
        return 'admiformatodebitotype';
    }
}
