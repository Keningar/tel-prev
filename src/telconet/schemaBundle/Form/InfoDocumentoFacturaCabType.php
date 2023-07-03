<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoDocumentoFacturaCabType extends AbstractType
{
    var $empresa;
    
    public function __construct($empresa){
    
	$this->empresa = $empresa;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//             ->add('numeracion', 'entity', array(
//                                             'class' => 'telconet\\schemaBundle\\Entity\\AdmiNumeracion',
//                                             'em' => 'telconet',
//                                             'query_builder' => function ($repositorio) {
//                                                 $qb = $repositorio->createQueryBuilder('entity')
// 						      ->where("entity.empresaId = '".$this->empresa."' ")
// 						      ->andWhere("entity.codigo = 'FAC' OR entity.codigo = 'FACR'");                                             
//                                                 return $qb;
//                                             },
//                                             'empty_value' => '000-000',
//                                             'required' => true,
//                                             'label' => 'Numeracion SRI :'                                            
//             ))
            ->add('descripcion','choice',array('label'  => 'Descripcion',
					       'empty_value'=>'Escoja tipo de Descripcion',
					       'choices'=> array('nombrePlan'     =>'Nombre del Plan',
					                         'descripcionFact'=>'Descripcion Plan')
					     ))
            ->add('inicio', 'text', array(
			  'label' => 'Numero de Inicio :',
			  'attr'=>array('onkeypress'=>'return validador(event,"numeros")')))			
            ->add('fin', 'text', array('label' => 'Numero de Fin :',
			  'attr'=>array('onkeypress'=>'return validador(event,"numeros")')))            
        ;
    }

    public function getName()
    {
        return 'infodocumentofacturacabtype';
    }
}