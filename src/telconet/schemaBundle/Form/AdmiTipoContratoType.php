<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoContratoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('empresaCod', 'entity', array(
					  'em'=> 'telconet',
					  'class'         => 'telconet\\schemaBundle\\Entity\\InfoEmpresaGrupo',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => true,
					  'label' => 'Empresa Grupo'                                       
                    ))
                                                                    
            ->add('descripcionTipoContrato','text',array('label'=>'Descripcion:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>100)))
                                       
            ->add('tiempoFinalizacion','text',array('label'=>'Tiempo Finalizacion (meses):','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>6)))
                                   
            ->add('tiempoAlertaFinalizacion','text',array('label'=>'Tiempo Alerta Finalizacion  (semanas):','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>6)))
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipocontratotype';
    }
}
