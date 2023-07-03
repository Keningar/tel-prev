<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlanMantenimientoType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	   
            ->add('nombreProceso','text',
                    array(
                        'label'=>'* Nombre del Plan:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Plan de Mantenimiento es requerido",
                            'onKeyDown'  => 'cambiarAMayusculas(this)',
                            )
                         )
                 )
            ->add('descripcionProceso','textarea',
                    array(
                        'label'=>'* Descripción del Plan:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Plan de Mantenimiento es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_planmantenimientotype';
    }
}
