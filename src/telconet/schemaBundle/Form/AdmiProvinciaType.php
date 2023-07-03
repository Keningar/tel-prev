<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProvinciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('regionId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiRegion',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')
													->where("LOWER(p.estado) not like LOWER('Eliminado') ")
													->orderBy('p.nombreRegion', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Region:'                                       
                    ))
            ->add('nombreProvincia','text',
                    array(
                        'label'=>'* Nombre Provincia:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Provincia es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiprovinciatype';
    }
}
