<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Clase que crea los componentes necesarios para
 * el formulario de Clase Tipo Medio
 * 
 * @author creado Francisco Adum <fadum@telconet.ec>
 * @version 1.0 24-02-2015
 */
class AdmiClaseTipoMedioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreClaseTipoMedio','text',
                    array(
                        'label'=>'* Nombre Clase Tipo Medio:',
                        'attr' => array('class' => 'campo-obligatorio',
                                        'validationMessage'=>"Nombre Clase Tipo Medio es requerido",
                                        'maxlength'=>20
                                       )
                         )
                 )
            ->add('descripcionClaseTipoMedio','textarea',
                  array('label'=>'* Descripcion Clase Tipo Medio:',
                        'attr' => array("col" => "20", 
                                        "row" => 10,
                                        'class' => 'campo-obligatorio',
                                        'validationMessage'=>"Descripcion del clase de tipo de medio es requerido",)
                       )
                )
            ->add('tipoMedioId', 
                  'entity', 
                  array('class'         => 'telconet\schemaBundle\Entity\AdmiTipoMedio',
                        'label'         => '* Tipo Medio:',
                        'required'      => true,
                        'attr'          => array('class' => 'campo-obligatorio'),
                        'em'            => 'telconet_infraestructura',
                        'query_builder' => function ($repository)
                        {
                            return $repository->createQueryBuilder('admi_tipo_medio')
                                              ->where("admi_tipo_medio.estado = 'Activo'");
                        }
                )
            )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiclasetipomediotype';
    }
}
