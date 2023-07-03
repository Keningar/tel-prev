<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Clase que crea los componentes necesarios para
 * el formulario de Hilo
 * 
 * @author creado Francisco Adum <fadum@telconet.ec>
 * @version 1.0 24-02-2015
 */
class AdmiHiloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroHilo','text',
                    array(
                        'label'=>'* Numero Hilo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Numero Hilo es requerido",
                            'onkeypress'=>'return validador(event,"numeros")',
                            'maxlength'=>5)
                         )
                 )
            ->add('colorHilo','text',
                    array(
                        'label'=>'* Color Hilo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Colo Hilo es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('claseTipoMedioId','entity',
                  array('class'       => 'telconet\schemaBundle\Entity\AdmiClaseTipoMedio',
                        'label'         => '* Clase Tipo Medio:',
                        'required'      => true,
                        'attr'          => array('class' => 'campo-obligatorio'),
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
        return 'telconet_schemabundle_admihilotype';
    }
}
