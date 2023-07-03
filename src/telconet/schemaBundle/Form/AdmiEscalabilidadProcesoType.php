<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiEscalabilidadProcesoType extends AbstractType
{
    private $arrayRoles;
    public function __construct($options) 
    {
        $this->arrayRoles = $options['arrayRoles'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayRoles = array("" => "-- Escoja una persona --");
        if($this->arrayRoles && count($this->arrayRoles)>0)
        {
            foreach($this->arrayRoles as $key => $value)
            {
                $arrayRoles[$value["id"]] = $value["descripcion"];
                
            }
        }
        
        $builder
	    ->add('procesoId', 'entity', 
                    array(
                        'em'=> 'telconet_soporte',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiProceso',
                        'query_builder' => function ($repositorio) {
                                        return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Proceso',
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Proceso es requerido",)                                     
                    ))
                                                
            ->add('rolId', 
                            'choice', array('choices' => $arrayRoles,
                                            'label' => 'Rol:',
                                            'required'=>true
                                            ))
                                                
            ->add('ordenEscalabilidad','text',
                    array(
                        'label'=>'* Orden:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Orden de Escalabilidad es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
                                        
            /*
                                                
	    ->add('rolId', 'entity', 
                    array(
                        'em'=> 'telconet_general',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiRol',
                        'query_builder' => function ($repositorio) {
                                        return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Rol',
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Rol es requerido",)                                     
                    ))*/
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiescalabilidadprocesotype';
    }
}
