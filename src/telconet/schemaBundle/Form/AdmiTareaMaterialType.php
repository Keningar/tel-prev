<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTareaMaterialType extends AbstractType
{
    private $codEmpresa;
    
    public function __construct($option){

	  $this->codEmpresa = $option['codEmpresa'];
	  	  
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesUnidades = array(  
                                    '' => 'Escoja una opcion', 
                                    'KT' => 'Kit', 
                                    'UD' => 'Unidad',
                                    'CM' => 'Centimetros', 
                                    'MT' => 'Metros', 
                                    'KM' => 'Kilometros', 
                                    'FT' => 'Pies',  
                                    'MI' => 'Millas',  
                                    'YD' => 'Yardas', 
                                    'KPPS' => 'Kilobit/S',  
                                    'MBPS' => 'Megabit/S',  
                                    'GBPS' => 'Gigabit/S', 
                                 );
        
        $builder
	    ->add('tareaId', 'entity', 
                    array(
                        'em'=> 'telconet_soporte',
                        'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTarea',
                        'query_builder' => function ($repositorio) {
                                      //  return $repositorio->createQueryBuilder('p')->orderBy('p.id', 'ASC');
					    return $repositorio->createQueryBuilder('p')
							       ->from('schemaBundle:AdmiProceso','ap')
							       ->from('schemaBundle:AdmiProcesoEmpresa','ape')
							       ->where('ap.id = ape.procesoId')
							       ->andWhere('p.procesoId = ap.id')
							       ->andWhere('ape.empresaCod = ?1')
							       ->setParameter(1, $this->codEmpresa)
							   //    ->andWhere("ap.visible = 'SI' ")
							       ->andWhere("p.estado = 'Activo' ")
							       ->orderBy('p.id', 'ASC');
                                        },
                        'empty_value' => 'Escoja una opcion',
                        'label' => '* Tarea:',
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Tarea es requerido",)                                     
                    ))
                                                
                                         
            ->add('unidadMedidaMaterial', 'choice', 
                    array('choices' => $opcionesUnidades,
                        'label' => '* Unidad Medida:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Unidad Medida es requerido",) 
                        ))
                                                
            ->add('costoMaterial','text',
                    array(
                        'label'=>'* Costo:',
                        'required'=>false,
                        'attr' => array(
                            'readonly' => 'readonly',
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Costo es requerido",
                            'maxlength'=>8)
                         )
                 )    
                                                
            ->add('precioVentaMaterial','text',
                    array(
                        'label'=>'* Precio Venta:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Precio Venta es requerido",
                            'maxlength'=>8)
                         )
                 )    
                                                
            ->add('cantidadMaterial','text',
                    array(
                        'label'=>'* Cantidad:',
                        'required'=>false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Cantidad es requerido",
                            'maxlength'=>8)
                         )
                 )    
                                               
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitareamaterialtype';
    }
}
