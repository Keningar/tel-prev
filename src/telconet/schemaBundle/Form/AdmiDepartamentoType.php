<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiDepartamentoType extends AbstractType
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
                        ))
						
			->add('areaId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiArea',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Area:'                                       
                    ))
                                                                    
            ->add('nombreDepartamento','text',
                    array(
                        'label'=>'* Nombre Departamento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Departamento es requerido",
                            'maxlength'=>30)
                         )
                 )
				 
            ->add('emailDepartamento','text',
                    array(
                        'label'=>'Email Departamento:',
					  'required' => false,
                        'attr' => array(
                            'maxlength'=>50)
                         )
                 )					 
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admidepartamentotype';
    }
}
