<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiFuncionType extends AbstractType
{
    private $arrayEmpresaRoles;
    public function __construct($options) 
    {
        $this->arrayEmpresaRoles = $options['arrayEmpresaRoles'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayEmpresaRoles = array("" => "-- Escoja una Empresa Rol --");
        if($this->arrayEmpresaRoles && count($this->arrayEmpresaRoles)>0)
        {
            foreach($this->arrayEmpresaRoles as $key => $value)
            {
                $arrayEmpresaRoles[$value["id"]] = $value["empresa"] . " - " . $value["rol"];
                
            }
        }              
        
        /*
	    ->add('empresaRolId', 'entity', array(
					  'em'=> 'telconet',
					  'class'         => 'telconet\\schemaBundle\\Entity\\InfoEmpresaRol',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Descripcion Empresa-Rol:'                                       
                    ))
                         
         */
        
        $builder
            ->add('empresaRolId', 'choice', 
                    array('choices' => $arrayEmpresaRoles,
                        'label' => '* Descripcion Empresa-Rol:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Empresa-Rol es requerido",) 
                        )) 
                
            ->add('descripcion','text',
                    array(
                        'label'=>'* Descripcion Funcion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion Funcion es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admifunciontype';
    }
}
