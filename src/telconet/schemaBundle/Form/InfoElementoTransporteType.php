<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoTransporteType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElementoVehiculo                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreElemento','text', array(
                                                    'label' =>'* Placa',
                                                    'attr'  => array(
                                                                        'class'             => 'campo-obligatorio',
                                                                        'validationMessage' => "Nombre del Elemento es requerido",
                                                                        'maxlength'         => 8,
                                                                        'onKeyPress'        => 'return validarCaracteresEspeciales(event);',
                                                                        'onKeyUp'           => 'convertirTextoEnMayusculas("nombreElemento");',
                                                                        'autocomplete'      => "off",
                                                                        'onpaste'           => "return false;"
                                                                    )
                                                  )
                 )
        ;
    }

    
    /**
     * getName
     *
     * Método de usado para la creación del formulario de la tabla InfoElementoVehiculo       
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function getName()
    {
        return '';
    }
}
