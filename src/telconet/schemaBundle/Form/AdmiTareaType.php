<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTareaType extends AbstractType
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
			/*
			->add('procesoId', 'entity', 
                    array(
                        'em'=> 'telconet_soporte',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiProceso',
                        'query_builder' => function ($repositorio) {
                                        return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Proceso:',
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Proceso es requerido",)                                     
                    )) */
                                                
            ->add('rolAutorizaId', 'choice', 
                    array('choices' => $arrayRoles,
                        'label' => 'Rol Autoriza:',
                        'required'=>false,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Rol Autoriza es requerido",) 
                        ))
                                                
	   /* ->add('tareaAnteriorId', 'entity', 
                    array(
                        'em'=> 'telconet_soporte',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTarea',
                        'query_builder' => function ($repositorio) {
                                        return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Tarea Anterior:',
                        'required'=>false,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Tara Anterior es requerido",)                                     
                    ))	
                                                
	    ->add('tareaSiguienteId', 'entity', 
                    array(
                        'em'=> 'telconet_soporte',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTarea',
                        'query_builder' => function ($repositorio) {
                                        return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Tarea Siguiente:',
                        'required'=>false,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Tara Siguiente es requerido",)                                     
                    ))	*/
                                                
            ->add('peso','text',
                    array(
                        'label'=>'* Peso %:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Peso es requerido",
                            'maxlength'=>3)
                         )
                 )
            ->add('esAprobada', 'choice', array(
                        'choices' => array('1'=>'Si','0'=>'No'), 
                        'label'=>'* Es Aprobada:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Aprobacion es requerida")
                         )
                  )          
                                                
            ->add('nombreTarea','text',
                    array(
                        'label'=>'* Nombre Tarea:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Tarea es requerido",
                            'maxlength'=>100)
                         )
                 )
                                                
            ->add('descripcionTarea','textarea',
                    array(
                        'label'=>'* Descripcion Tarea:',
                        'required'=>false,
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion Tarea es requerido",)
                         )
                 )
                                                
            ->add('tiempoMax','text',
                    array(
                        'label'=>'* Tiempo Max:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Tiempo Max es requerido",
                            'maxlength'=>8)
                         )
                 )
            ->add('unidadMedidaTiempo', 'choice', array(
                        'choices' => array('DIAS'=>'Dias','HORAS'=>'Horas','MINUTOS'=>'Minutos'), 
                        'label'=>'* Unidad Medida Tiempo',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Unidad Medida Tiempo es requerido",
                            'maxlength'=>30)
                         )
                  )
                 
                                                
            ->add('costo','text',
                    array(
                        'label'=>'* Costo:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Costo es requerido",
                            'maxlength'=>8)
                         )
                 )
                                                
            ->add('precioPromedio','text',
                    array(
                        'label'=>'* Precio Promedio:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Precio Promedio es requerido",
                            'maxlength'=>8)
                         )
                 )
            ->add('tareasInterfacesModelosTramos','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitareatype';
    }
}
