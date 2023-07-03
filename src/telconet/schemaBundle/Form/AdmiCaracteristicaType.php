<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiCaracteristicaType extends AbstractType
{
    /**
     * Documentación para el método 'buildForm'.
     *
     * Crea el formulario para el registro o edición de una característica.
     *
     * @return form 
     *
     * @version 1.0 Versión Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se agrega la opción 'Seleccionable' al Tipo de Ingreso de la característica.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcionCaracteristica','text',array( 'label'=> '* Descripcion:',
                                                            'attr' => array( 'class'             => 'campo-obligatorio',
                                                                             'validationMessage' => "Descripcion de Caracteristica es requerido",
                                                                             'maxlength'         => 60)))
            ->add('tipoIngreso','choice',array( 'label'       => '* Tipo de Ingreso:',
                                                'choices'     => array( 'N' => 'Numero', 
                                                                        'T' => 'Texto', 
                                                                        'S' => 'Seleccionable', 
                                                                        'O' => 'Opcion (Si/No)'),
                                                'empty_value' => 'Seleccione',
                                                'required'    => true,
                                                'attr'        => array( 'class'             => 'campo-obligatorio',
                                                                        'validationMessage' => "Tipo de ingreso es requerido",
                                                                        'onChange'          => 'cambioTipoIngreso();' ) ) )
            ->add('tipo', 'choice', array(  'label'       => '* Tipo:',
                                            'choices'     => array( 'COMERCIAL'  => 'COMERCIAL', 
                                                                    'TECNICA'    => 'TECNICA', 
                                                                    'FINANCIERA' => 'FINANCIERA',
                                                                    'PROMOCION'  => 'PROMOCION' ),
                                            'empty_value' => 'Seleccione',
                                            'required'    => true,
                                            'attr'        => array( 'class'             => 'campo-obligatorio',
                                                                    'validationMessage' => "Tipo es requerido" ) ) )
        ;
    }

    public function getName()
    {
        return 'admicaracteristica';
    }
}
