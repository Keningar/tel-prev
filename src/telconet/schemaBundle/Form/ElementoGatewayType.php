<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ElementoGatewayType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {                
        	
        $builder     
// 	     ->add('modeloElementoId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiMarcaElemento',
// 						     'label'=>'Modelo Elemento',
// 					             'em'=>'telconet_infraestructura',
// 						     'query_builder' => function($repositorio){
// 							  return $repositorio->createQueryBuilder('me')
// 								->from('schemaBundle:AdmiTipoElemento','te')
// 								->from('schemaBundle:AdmiModeloElemento','moe')
// 								->where('me = moe.marcaElementoId')
// 								->andWhere('te = moe.tipoElementoId')
// 								->andWhere("te.nombreTipoElemento = 'GATEWAY' ");
// 						     },
// 						     'empty_value' => 'Escoja una opcion',						     
// 						     'required' => true,
// 						     )
// 		)   

            ->add('nombreElemento', 'text', 
                   array(
                        'label'=>'Nombre del Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )
						
            ->add('descripcionElemento','text',
                    array(
                        'label'=>'Descripcion del Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )	            
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_elementogatewaytype';
    }
}
