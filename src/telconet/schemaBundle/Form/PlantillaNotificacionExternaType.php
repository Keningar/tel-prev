<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlantillaNotificacionExternaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', 'entity', array(                                           
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiClaseDocumento',                                            
                                            'em' => 'telconet_comunicacion',                                            
                                            'query_builder' => function ($repositorio) {
                                                $qb = $repositorio->createQueryBuilder('entity')							    
                                                            ->where("entity.estado not like 'Eliminado'")                                                      
                                                ->andWhere('entity.nombreClaseDocumento like ?1 ')
                                                ->setParameter(1, '%'.'Notificacion Externa'.'%');                                                
                                                return $qb;
                                           },
                                           'attr'=>array('onChange'=>'verPlantillaTipo()'),                                                                                  
                                           'required' => true,
                                           'label' => '* Tipo:'  
            ))           
            ->add('nombrePlantilla', 'text', array('label' => '* Nombre:'))
            ->add('plantilla_mail','hidden',array())
            ->add('plantilla_sms','hidden',array())
            ->add('fecha_desde', 'hidden', array('label' => '* Fecha Desde:'))
            ->add('fecha_hasta', 'hidden', array('label' => '* Fecha Hasta:'));
    }

    public function getName()
    {
        return 'telconet_schemabundle_plantillaNotificacionExternatype';
    }
}
