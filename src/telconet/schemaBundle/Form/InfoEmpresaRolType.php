<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoEmpresaRolType extends AbstractType
{
    private $arrayRoles;
    public function __construct($options) 
    {
        $this->arrayRoles = $options['arrayRoles'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayRoles = array("" => "-- Escoja un rol --");
        if($this->arrayRoles && count($this->arrayRoles)>0)
        {
            foreach($this->arrayRoles as $key => $value)
            {
                $arrayRoles[$value["id"]] = $value["nombre"];
                
            }
        }
        
        
              /*                                                      
	    ->add('rolId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiRol',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Descripcion Rol:'                                       
                    ))*/
        
        $builder
	    ->add('empresaCod', 'entity', array(
					  'em'=> 'telconet',
					  'class'         => 'telconet\\schemaBundle\\Entity\\InfoEmpresaGrupo',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Empresa:'                                       
                    ))
                                         
            ->add('rolId', 'choice', 
                    array('choices' => $arrayRoles,
                        'label' => '* Rol:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Rol es requerido",) 
                        ))
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoempresaroltype';
    }
}
