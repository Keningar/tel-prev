<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiAreaType extends AbstractType
{
    private $arrayEmpresas;
    public function __construct($options) 
    {
        $this->arrayEmpresas = $options['arrayEmpresas'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayEmpresas = array("" => "-- Escoja una empresa --");
        if($this->arrayEmpresas && count($this->arrayEmpresas)>0)
        {
            foreach($this->arrayEmpresas as $key => $value)
            {
                $arrayEmpresas[$value["id"]] = $value["nombre"];
                
            }
        }
        	
        $builder     
            ->add('empresaCod', 'choice', 
                    array('choices' => $arrayEmpresas,
					  'required' => false,
                        'label' => 'Empresa:'
						)
				)
						
            ->add('nombreArea','text',
                    array(
                        'label'=>'* Nombre Area:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Area es requerido",
                            'maxlength'=>30)
                         )
                 )
				             
            ->add('emailArea','text',
                    array(
                        'label'=>'Email Area:',
						'required' => false,
                        'attr' => array(
                            'maxlength'=>50)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiareatype';
    }
}
