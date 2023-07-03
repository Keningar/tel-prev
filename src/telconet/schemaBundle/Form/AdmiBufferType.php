<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiBufferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('numeroBuffer','text',
                    array('label'   =>'* Numero Buffer:',
                          'attr'    => array('class'                => 'campo-obligatorio',
                                             'validationMessage'    => "Numero buffer es requerido",
                                             'maxlength'            => 30
                                            )
                         )
                 )
            ->add('colorBuffer','text',
                    array('label'=>'* Color Buffer:',
                          'attr' => array('class'               => 'campo-obligatorio',
                                          'validationMessage'   => "Numero buffer es requerido",
                                          'maxlength'           => 30
                                         )
                         )
                 )
             ->add('descripcionBuffer','textarea',
                    array('label'=>'* Descripcion Buffer:',
                          'attr' => array("col"                 => "20", 
                                          "row"                 => 10,
                                          'class'               => 'campo-obligatorio',
                                          'validationMessage'   => "Descripcion del Buffer es requerido"
                                         )
                         )
                 )
             ->add('claseTipoMedioId','entity',
                  array('class'         => 'telconet\schemaBundle\Entity\AdmiClaseTipoMedio',
                        'label'         => '* Clase Tipo Medio:',
                        'required'      => false,
                        'mapped'        => false,
                        'attr'          => array('class'    => 'campo-obligatorio',
                                                 'onchange' => 'cargarHilosPorClaseTipoMedio(this)'
                                                ),
                        'em'            => 'telconet_infraestructura',
                        'query_builder' => function ($repository) 
                                            {
                                                return $repository->createQueryBuilder('admi_clase_tipo_medio')
                                                    ->where("admi_clase_tipo_medio.estado = 'Activo'")
                                                    ->orderBy("admi_clase_tipo_medio.nombreClaseTipoMedio","ASC");
                                            }
                       )				
                )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admibuffertype';
    }
}
