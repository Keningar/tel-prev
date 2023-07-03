<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiRegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('paisId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiPais',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')
													->where("LOWER(p.estado) not like LOWER('Eliminado') ")
													->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Pais:'                                       
                    ))
            ->add('nombreRegion','text',
                    array(
                        'label'=>'* Nombre Region:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Region es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiregiontype';
    }
}
