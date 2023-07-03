<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlantillaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('tipo', 'entity', array(
//                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiFormaContacto',
//                                            'em' => 'telconet',
//                                            'query_builder' => function ($repositorio) {
//                                                $qb = $repositorio->createQueryBuilder('entity')
//                                                            ->where("entity.estado not like 'Eliminado'");
//                                                $qb->andWhere(
//                                                        $qb->expr()->orX(
//                                                                        $qb->expr()->like('entity.descripcionFormaContacto',$qb->expr()->literal("Movil")),
//                                                                        $qb->expr()->like('entity.descripcionFormaContacto',$qb->expr()->literal("Mail"))
//                                                                        )
//                                                );
//                                                return $qb;
//                                            },
//                                            'empty_value' => 'Escoja una opcion',
//                                            'required' => true,
//                                            'label' => '* Tipo:'  
//            ))
            ->add('nombrePlantilla', 'text', array('label' => '* Nombre:'))
            ->add('plantilla','hidden',array())
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_plantillatype';
    }
}