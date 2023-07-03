<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'InfoBitacoraAccesoNodoType'.
 *
 * Clase utilizada para manejar la información de una bitácora de acceso nodo
 * dentro de un formulario html
 *
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 01-04-2021
 */
class InfoBitacoraAccesoNodoType extends AbstractType
{
    /**
     * Documentación para el método 'buildForm'.
     * 
     * Metodo utilizado para armar la estructura del type
     *
     * @param FormBuilderInterface $objBuilder
     * @param array                $arrayOptions
     */
    public function buildForm(FormBuilderInterface $objBuilder, array $arrayOptions)
    {
        $objBuilder
            ->add('tareaId', 'text',
                array(
                    'label'=>'* Tarea:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Tarea es requerido",
                    )
                )
            )
            ->add('canton', 'text',
                array(
                    'label'=>'* Ciudad:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Canton es requerido",
                    )
                )
            )
            ->add('departamento', 'text',
                array(
                    'label'=>'* Departamento:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Departamento es requerido",
                    )
                )
            )
            ->add('tecnicoAsignado', 'text',
                array(
                    'label'=>'* Técnico:',
                    'attr' => array(
                        'class' => 'campo-obligatorio',
                        'validationMessage'=>"Tecnico es requerido",
                    )
                )
            )
            ->add('elementoNodoNombre', 'text',
                array(
                    'label'=>'* Nodo:'
                )
            )
            ->add('elemento', 'text',
                array(
                    'label'=>'Elemento Relacionado:'
                )
            )
            ->add('telefono', 'text',
                array(
                    'label'=>' Teléfono:' ,
                    'attr' => array(
                        'maxlength' => 12,
                        'onKeyPress'=> "return verificarSoloNumeros(event);"
                    )

                )
            )
            ->add('codigos', 'textarea',
                array(
                    'label'=>' Llave Acsys:',
                    'attr' => array(
                        'col' => '60',
                        'row' => 20,
                        'width' => 120,
                        'maxlength' => 150,
                        'style' => 'width: 250px',
                    )
                )
            )
            ->add('observacion', 'textarea',
                array(
                    'label' => ' Observación:',
                    'attr' => array(
                        'col' => '60',
                        'row' => 20,
                        'width' => 200,
                        'maxlength' => 500,
                        'style' => 'width: 900px; height: 100px',
                    )
                )
            );
    }

    public function getName()
    {
        return 'telconet_schemabundle_infobitacoraaccesonodotype';
    }
}
