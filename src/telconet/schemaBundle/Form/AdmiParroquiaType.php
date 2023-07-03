<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiParroquiaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('cantonId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiCanton',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')
													->where("LOWER(p.estado) not like LOWER('Eliminado') ")
													->orderBy('p.nombreCanton', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Canton:'                                       
                    ))
	    ->add('tipoParroquiaId', 'entity', array(
					  'em'=> 'telconet_general',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTipoParroquia',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')
													->where("LOWER(p.estado) not like LOWER('Eliminado') ")
													->orderBy('p.nombreTipoParroquia', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Tipo Parroquia:'                                       
                    ))
            ->add('nombreParroquia','text',
                    array(
                        'label'=>'* Nombre Parroquia:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Parroquia es requerido",
                            'maxlength'=>30)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiparroquiatype';
    }
}
