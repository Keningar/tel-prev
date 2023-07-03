<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiRolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesCabecera = array('N' => 'NO', 'S' => 'SI');
		
        $builder
	    ->add('tipoRolId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTipoRol',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => true,
					  'label' => 'Descripcion Tipo Rol:'                                       
                    ))
                                                                    
            ->add('descripcionRol','text',
                    array(
                        'label'=>'* Descripcion Rol:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Rol es requerido",
                            'maxlength'=>30)
                         )
                 )	
				                                           
             ->add('esJefe', 'choice', array(
                        'label'=>'* Es Jefe:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Jefe es requerido"
                            )
                        )
                  )
                     
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiroltype';
    }
}
