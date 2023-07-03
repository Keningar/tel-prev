<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiPlantillaType extends AbstractType
{ 
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {                
        $builder                                                                                
            ->add('nombrePlantilla','text',
                    array(
                        'label'=>'* Nombre Plantilla:',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre es requerido",
                            'maxlength'=>100,
                            'width'=>500)
                         )
                 )
             ->add('codigo','text',
                    array(
                        'label'=>'* Codigo Plantilla:',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Codigo es requerido",
                            'maxlength'=>15)
                         )
                 )  
              ->add('modulo', 'choice', array(
			    'choices' => array(
					       'COMERCIAL' => 'COMERCIAL',
					       'PLANIFICACION' => 'PLANIFICACION',
					       'TECNICO' => 'TECNICO',
					       'FINANCIERO' => 'FINANCIERO',
					       'SOPORTE' => 'SOPORTE'					       
					       ),
			     'required'    => false,
			      'empty_value' => 'Modulo Plantilla',
			      'empty_data'  => null
					       )
		    )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiplantillatype';
    }
}
