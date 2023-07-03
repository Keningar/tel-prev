<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MarcaElementoType extends AbstractType
{

    private $marca;
    public function __construct($marca="") 
    {
        if($marca=="")$this->marca="Escoja una opcion";
        else $this->marca = $marca;                
                
    }
     public function buildForm(FormBuilderInterface $builder, array $options)
    {                
         	
        $builder     
	     ->add('nombreMarcaElemento','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiMarcaElemento',
						     'label'=>'Modelo Elemento',
					             'em'=>'telconet_infraestructura',
						     'query_builder' => function($repositorio){
							  return $repositorio->createQueryBuilder('me')
								->from('schemaBundle:AdmiTipoElemento','te')
								->from('schemaBundle:AdmiModeloElemento','moe')
								->where('me = moe.marcaElementoId')
								->andWhere('te = moe.tipoElementoId')
								->andWhere("te.nombreTipoElemento = 'GATEWAY' ");
						     },							   
						     'empty_value' => 'Escoja una opcion',						     
						     'preferred_choices' => array($this->marca),
						     'required' => true,
						     )
		)                                    
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_elementogatewaytype';
    }
}