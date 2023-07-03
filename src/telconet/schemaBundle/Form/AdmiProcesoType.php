<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProcesoType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    /*->add('procesoPadreId', 'entity', array(
					  'em'=> 'telconet_soporte',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiProceso',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Proceso Padre'                                       
                    ))*/
            ->add('nombreProceso','text',
                    array(
                        'label'=>'* Nombre Proceso:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Proceso es requerido",
                            'maxlength'=>80)
                         )
                 )
            ->add('descripcionProceso','textarea',
                    array(
                        'label'=>'* Descripcion Proceso:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Proceso es requerido",)
                         )
                 )
            ->add('aplicaEstado','text',
                    array(
                        'label'=>'Aplica Estado:',
                        'required' => false,
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )	
             ->add('visible','choice',
                    array(			
			'choices'=>array(''=>'','SI'=>'SI','NO'=>'NO'),			
                        'label'=>'Proceso Visible:',
                        'required' => false,
                        'attr' => array(
                            'width'=>10                            
                            )
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiprocesotype';
    }
}
