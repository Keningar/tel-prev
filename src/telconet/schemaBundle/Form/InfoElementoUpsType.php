<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoUpsType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento de tipo 'OLT'                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2015
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                ->add(  'ipElemento','text',
                        array(
                                'label'=> '* Ip:',
                                'attr' => array(
                                                    'class'             => 'campo-obligatorio',
                                                    'validationMessage' => "Ip del elemento es requerido",
                                                    'maxlength'         => 15,
                                                    'data-inputmask'    => "'alias': 'ip'",
                                                    'data-mask'         => ""
                                                )
                             )
                     )
                ->add( 'observacion', 'textarea',
                       array(
                               'label'  => '* Observacion:',
                               'attr'   => array(
                                                    "col"               => "20", 
                                                    "row"               => 14,
                                                    'validationMessage' => "Observación del Elemento es requerido"
                                                )
                            )
                    );
    }

    public function getName()
    {
        return 'infoElementoUps';
    }
}
