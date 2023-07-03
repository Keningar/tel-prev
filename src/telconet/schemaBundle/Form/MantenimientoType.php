<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MantenimientoType extends AbstractType
{
    private $arrayFrecuenciasMantenimiento ;
    private $arrayTiposFrecuenciasMantenimiento ;
	
	public function __construct($options) 
    {
        $this->arrayFrecuenciasMantenimiento = $options['arrayFrecuenciasMantenimiento'];
        $this->arrayTiposFrecuenciasMantenimiento  = $options['arrayTiposFrecuenciasMantenimiento'];

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            
        $factory = $builder->getFormFactory(); 
            	           
        $builder                    
            ->add('frecuencias','collection', array(
                'type'      => 'choice',
                'allow_add' => true,                
                'allow_delete' => true,
                'label_attr' => array('class' => '*Frecuencia'),
                'prototype' => true,
                'options'=>array('choices' => $this->arrayFrecuenciasMantenimiento,'preferred_choices' => array(1),
                'required'  => true,
                'by_reference' => false))) 
        
            ->add('tiposFrecuencia','collection', array(
                'type'      => 'choice',
                'allow_add' => true,                
                'allow_delete' => true,
                'label_attr' => array('class' => '*Unidad de Medida'),
                'prototype' => true,
                'options'=>array('choices' => $this->arrayTiposFrecuenciasMantenimiento,'preferred_choices' => array(1),
                'required'  => true,
                'by_reference' => false))) 

            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\AdmiProceso'
        ));
    }

    public function getName()
    {
        return 'mantenimientotype';
    }
}
