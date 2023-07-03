<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class AdmiHoldingType extends AbstractType
{
    private $vendedor;
    private $vendedores;
    
    
    public function __construct($options) 
    {
        
        $this->vendedores          = $options['vendedores'];
    }
    
    public function buildForm(FormBuilderInterface $objBuilder, array $arrayOptions)
    {
        
        $objBuilder
            ->add('valor1', 'text', 
                    array('label' => '* Nombre Razón Social',
                            'label_attr' => array('class' => 'campo-obligatorio'), 
                            'attr' => array('maxLength' => 50,
                            'class' => 'campo-obligatorio', 
                            'onChange' => '')))
           
            ->add('valor2', 'text', 
                    array('label' => '* Identificación:',
                            'label_attr' => array('class' => 'campo-obligatorio'), 
                            'attr' => array('maxLength' => 25,
                            'class' => 'campo-obligatorio', 
                            'onChange' => '')))
           
            ->add('valor3', 'choice', 
                    array('attr'       => array('style'    => 'width: 300px;'),
                            'label'      => '* Vendedor:',
                            'label_attr' => array('class' => 'campo-obligatorio'),
                            'choices'    => $this->vendedores,
                            'required'    => false,
                            'empty_value' => 'Seleccione...',
                            'empty_data'  => null,
                            'data'        => $this->vendedor))
             ;  
             
        
    }

    public function getName()
    {
        return 'admiholdingtype';
    }
}
