<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoFuenteType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento de tipo 'FUENTE'                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2015
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(  'modeloElementoId','entity',
                        array(
                                'class'         =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                                'label'         =>'* Modelo:',
                                'required'      => true,
                                'attr'          => array( 'class' => 'campo-obligatorio' ),
                                'em'            => 'telconet_infraestructura',
                                'empty_value'   => 'Seleccione',
                                'query_builder' => function ($repository) 
                                                   {
                                                        $qb = $repository->createQueryBuilder('ame')
                                                                         ->select('ame')
                                                                         ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','ate')
                                                                         ->where("ate = ame.tipoElementoId")
                                                                         ->andWhere("ate.nombreTipoElemento = :nombreElemento")
                                                                         ->andWhere("ate.estado = :estadoActivo")
                                                                         ->andWhere("ame.estado != :estadoEliminado")
                                                                         ->orderBy('ame.nombreModeloElemento', 'ASC');
                                                                      
                                                        $qb->setParameter("nombreElemento",  "FUENTE");
                                                        $qb->setParameter("estadoActivo",    "Activo");
                                                        $qb->setParameter("estadoEliminado", "Eliminado");
                                                        
                                                        return $qb;
                                                    }
                              )				
                    )
                ->add(  'nombreElemento','text',
                        array(
                                'label'=>'* Nombre Elemento:',
                                'attr' => array(
                                                    'class'             => 'campo-obligatorio',
                                                    'validationMessage' => "Nombre del elemento es requerido",
                                                    'maxlength'         => 30
                                                )
                             )
                     )
                ->add( 'observacion','textarea',
                       array(
                               'label'  => '* Observación:',
                               'attr'   => array(
                                                    "cols"               => "28", 
                                                    "rows"               => 5,
                                                    'validationMessage' => "Observación del Elemento es requerido"
                                                )
                            )
                    );
    }

    public function getName()
    {
        return 'infoElementoFuente';
    }
}
