<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiSectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    /*->add('parroquiaId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiParroquia',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')
													->where("LOWER(p.estado) not like LOWER('Eliminado') ")
													->orderBy('p.nombreParroquia', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => '* Nombre Parroquia:'                                       
                    ))*/
                                                                    
            ->add('nombreSector','text',
                    array(
                        'label'=>'* Nombre Sector:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Sector es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admisectortype';
    }
}
