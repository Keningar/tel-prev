<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiNumeracionType extends AbstractType
{
    public $intMaxLengthSecuencial1        = 3;
    public $intMaxLengthSecuencial2        = 3;
    public $intMaxLengthNumeroAutorizacion = 13;
    public $strMostrarSecuenciales         = 'N';
    public $strMostrarNumeroAutorizacion   = 'N';


    /**
     * __construct
     *
     * Método de configuración inicial de la clase AdmiNumeracionType                               
     *      
     * @param array $arrayParametros['intMaxLengthSecuencial1'        => 'Cantidad máxima permitida para escribir en el secuencial 1',
     *                               'intMaxLengthSecuencial2'        => 'Cantidad máxima permitida para escribir en el secuencial 2',
     *                               'intMaxLengthNumeroAutorizacion' => 'Cantidad máxima permitida para escribir en el número de autorización',
     *                               'strMostrarSecuenciales'         => 'Parámetro que indica si se deben presentar los secuenciales',
     *                               'strMostrarNumeroAutorizacion'   => 'Parámetro que indica si se debe presentar el campo número de autorización]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-06-2017
     */
    public function __construct($arrayParametros = null)
    {
        if( isset($arrayParametros['intMaxLengthSecuencial1']) && $arrayParametros['intMaxLengthSecuencial1'] > 0 )
        {
            $this->intMaxLengthSecuencial1 = $arrayParametros['intMaxLengthSecuencial1']; 
        }//( isset($arrayParametros['intMaxLengthSecuencial1']) && $arrayParametros['intMaxLengthSecuencial1'] > 0 )

        if( isset($arrayParametros['intMaxLengthSecuencial2']) && $arrayParametros['intMaxLengthSecuencial2'] > 0 )
        {
            $this->intMaxLengthSecuencial2 = $arrayParametros['intMaxLengthSecuencial2']; 
        }//( isset($arrayParametros['intMaxLengthSecuencial2']) && $arrayParametros['intMaxLengthSecuencial2'] > 0 )

        if( isset($arrayParametros['intMaxLengthNumeroAutorizacion']) && $arrayParametros['intMaxLengthNumeroAutorizacion'] > 0 )
        {
            $this->intMaxLengthNumeroAutorizacion = $arrayParametros['intMaxLengthNumeroAutorizacion']; 
        }//( isset($arrayParametros['intMaxLengthNumeroAutorizacion']) && $arrayParametros['intMaxLengthNumeroAutorizacion'] > 0 )
        
        if( isset($arrayParametros['strMostrarSecuenciales']) && !empty($arrayParametros['strMostrarSecuenciales']) )
        {
            $this->strMostrarSecuenciales = $arrayParametros['strMostrarSecuenciales']; 
        }//( isset($arrayParametros['strMostrarSecuenciales']) && !empty($arrayParametros['strMostrarSecuenciales']) )
        
        if( isset($arrayParametros['strMostrarNumeroAutorizacion']) && !empty($arrayParametros['strMostrarNumeroAutorizacion']) )
        {
            $this->strMostrarNumeroAutorizacion = $arrayParametros['strMostrarNumeroAutorizacion']; 
        }//( isset($arrayParametros['strMostrarNumeroAutorizacion']) && !empty($arrayParametros['strMostrarNumeroAutorizacion']) )
    }


    /**
     * Documentación para el método 'buildForm'
     * 
     * Función que retorna el formulario para la creación de la numeración para la oficina
     * 
     * @version 1.0 Versión Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29/12/2015 - Se modifica para que retorne sólo los campos necesarios para crear la numeración de las facturas. 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 27-06-2017 - Se agrega el campo 'numeroAutorizacion' y se valida mediante parámetros si se deben presentar o no los campos
     *                           respectivos
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder   
            ->add('descripcion', 'text',
                    array(
                            'label' =>'* Descripcion:',
                            'attr'  => array(
                                                'class'             => 'campo-obligatorio',
                                                'validationMessage' => "Nombre del Area es requerido",
                                                'maxlength'         => 40
                                            )
                         )
                 );


        if ( $this->strMostrarSecuenciales == 'S' )
        {
            $builder->add('numeracionUno', 'text', array( 'label' => '* Código Establecimiento:',
                                                          'attr'  => array( 'class'             => 'campo-obligatorio',
                                                                            'validationMessage' => "Codigo del establecimiento es requerido",
                                                                            'maxlength'         => $this->intMaxLengthSecuencial1,
                                                                            'readOnly'          => true,
                                                                            'style'             => "width: 50px;" ) ))
                    ->add('numeracionDos', 'text', array( 'label'=>'* Punto de Emisión:',
                                                          'attr' => array( 'class'             => 'campo-obligatorio',
                                                                           'validationMessage' => "El punto de emisión es requerido",
                                                                           'maxlength'         => $this->intMaxLengthSecuencial2,
                                                                           'style'             => "width: 50px;",
                                                                           'onKeyPress'        => "return verificarSoloNumeros(event);" ) ) );
        }// ( $this->strMostrarSecuenciales == 'S' )


        if ( $this->strMostrarNumeroAutorizacion == 'S' )
        {
            $builder->add('numeroAutorizacion', 'text', array( 'label'=>'* Impresión Fiscal:',
                                                               'attr' => array( 'class'             => 'campo-obligatorio',
                                                                                'validationMessage' => "El número de autorización es requerido",
                                                                                'maxlength'         => $this->intMaxLengthNumeroAutorizacion,
                                                                                'style'             => "width: 150px;" ) ) );
        }// ( $this->strMostrarNumeroAutorizacion == 'S' )
    }

    
    /**
     * Documentación para el método 'getName'
     * 
     * Función que retorna el prefijo con el cual saldran los id y names de cada uno de los items del formulario.
     * 
     * @version 1.0 Versión Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-01-2016 - Se modifica para que no retorne prefijo. 
     */
    public function getName()
    {
        return '';
    }
}
