<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoEmpresaPermisosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesPermisos = array('BOMBEROS' => 'CUERPO DE BOMBEROS', 'MUNICIPAL' => 'PERMISO MUNICIPAL');
        $opcionesTienePermisos = array('N' => 'NO', 'S' => 'SI');
        
        $builder
	    ->add('empresaCod', 'entity', array(
					  'em'=> 'telconet',
					  'class'         => 'telconet\\schemaBundle\\Entity\\InfoEmpresaGrupo',
					  'query_builder' => function ($repositorio) {
							    return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
							    },
					  'empty_value' => 'Escoja una opcion',
					  'required' => false,
					  'label' => 'Nombre Empresa:'                                       
                    ))
                
            ->add('tipoPermiso', 
                                'choice', array('choices' => $opcionesPermisos,
                                                'label' => '* Tipo Permiso:',
                                                'required'=>true
                                                ))
                
            ->add('tienePermiso', 
                                'choice', array('choices' => $opcionesTienePermisos,
                                                'label' => '* Tiene Permiso:',
                                                'required'=>true
                                                ))
                                                
            ->add('observacion','textarea',
                    array(
                        'label'=>'Observacion:',
                        'required'=>false,
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Observacion es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoempresapermisostype';
    }
}
