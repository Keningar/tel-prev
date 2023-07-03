<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiCantonType extends AbstractType
{
    /**
     * 
     * View que se muestra en las pantallas de Admin Cantones
     * 
     * @author Codigo Inicial
     * @version 1.0 
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - Se adiciona los siguientes campos a la pantalla
     *                de la Admin Cantón codigo inec, región, zona.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return view de edición de los cantones.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesCabecera = array('NO' => 'NO', 'SI' => 'SI');
        $opcionesRegion   = array('R1' => 'R1', 'R2' => 'R2');
        $opcionesZona     = array('Zona1' => 'Zona1', 'Zona2' => 'Zona2', 'Zona3' => 'Zona3');


        $builder
            ->add('provinciaId', 'entity', array(
                                            'em'                    => 'telconet_general',
                                            'class'                 => 'telconet\\schemaBundle\\Entity\\AdmiProvincia',
                                            'query_builder'         => function ($repositorio)
                                                                    {
                                                                        return $repositorio->createQueryBuilder('p')
                                                                            ->where("LOWER(p.estado) not like LOWER('Eliminado') ")
                                                                            ->orderBy('p.nombreProvincia', 'ASC');
                                                                    },
                                            'empty_value'           => 'Escoja una opcion',
                                            'required'              => false,
                                            'label'                 => 'Nombre Provincia:'
                                            )
            )
            ->add('nombreCanton', 'text', array(
                                            'label'                 => '* Nombre Cantón:',
                                            'attr'                  => array(
                                            'class'                 => 'campo-obligatorio',
                                            'validationMessage'     => "Nombre del Cantón es requerido",
                                            'required'              => true,
                                            'maxlength'             => 30)
                                            )
            )
            ->add('esCapital', 'choice', array(
                                            'label'                 => '* Es Capital Provincial:',
                                            'choices'               => $opcionesCabecera)
            )
            ->add('esCabecera', 'choice', array(
                                            'label'                 => '* Es Cabecera Cantonal:',
                                            'required'              => true,
                                            'choices'               => $opcionesCabecera)
            )
            ->add('sigla', 'text', array(
                                            'label'                 => '* Sigla:',
                                            'attr'                  => array(
                                                'class'             => 'campo-obligatorio',
                                                'validationMessage' => "Siglas del Canton son requerida",
                                                'required'          => true,
                                                'maxlength'         => 4)
                                            )
            )
            ->add('codigoInec', 'text', array(
                                            'label'                 => '* Código Inec:',
                                            'attr'                  => array(
                                                'class'             => 'campo-obligatorio',
                                                'validationMessage' => "Codigo INEC del Canton son requerida",
                                                'required'          => true,
                                                'maxlength'         => 5)
                                            )
            )
            ->add('region', 'choice', array(
                                            'label'                 => '* Región:',
                                            'choices'               => $opcionesRegion
                                            )
            )
            ->add('zona', 'choice', array(
                                            'label'                 => '* Zona:',
                                            'choices'               => $opcionesZona
                                            )
            );
    }

    public function getName()
    {
        return 'telconet_schemabundle_admicantontype';
    }
}
