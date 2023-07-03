<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiClausulaContratoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('tipoContratoId', 'entity', array(
					  'em'=> 'telconet',
					  'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTipoContrato',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => true,
					  'label' => 'Tipo contrato:'                                       
                    ))
                                                                    
            ->add('nombreClausula','text',array('label'=>'Nombre:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>40)))
               
                                                
            ->add('descripcionClausula','textarea',
                    array(
                        'label'=>'* Descripcion:',
                        'required'=>false,
                        'attr' => array("col" => 40, "row" => 30,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion Clausula es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiclausulacontratotype';
    }
}
