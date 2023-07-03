<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiFormaPagoType extends AbstractType
{
    private $arrayTipoFormaPago = array();
    
    /**
     * __construct
     *
     * Método de configuración inicial de la clase AdmiFormaPagoType                               
     *      
     * @param array $arrayParametros
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-11-2016
     */
    public function __construct($arrayParametros = null)
    {
        if( !empty($arrayParametros) )
        {
            if( isset($arrayParametros['arrayTipoFormaPago']) && !empty($arrayParametros['arrayTipoFormaPago']) )
            {
                $this->arrayTipoFormaPago = $arrayParametros['arrayTipoFormaPago'];
            }
        }
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcionesCabecera = array('N' => 'NO', 'S' => 'SI');
        
        $builder
                
            ->add('codigoFormaPago','text',
                    array(
                        'label'=>'* Codigo Forma Pago',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Codigo de Forma de Pago es requerido",
                            'maxlength'=>4)
                         )
                 )
                
            ->add('descripcionFormaPago','text',
                    array(
                        'label'=>'* Descripcion Forma Pago:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion de Forma de Pago es requerido",
                            'maxlength'=>60)
                         )
                 )	
                                                                
             ->add('esDepositable', 'choice', array(
                        'label'=>'* Es Depositable:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Depositable es requerido"
                            )
                        )
                  )
                                                                 
             ->add('esMonetario', 'choice', array(
                        'label'=>'* Es Monetario:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"GEs Monetario es requerido"
                            )
                        )
                  )
                                                                
             ->add('esPagoParaContrato', 'choice', array(
                        'label'=>'* Es Pago Para Contrato:',    
                        'choices' => $opcionesCabecera,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Pago Para Contrato es requerido"
                            )
                        )
                  )                                             
             ->add('tipoFormaPago', 'choice', array( 'label'   => '* Tipo Forma de Pago:',    
                                                     'choices' => $this->arrayTipoFormaPago,
                                                     'attr'    => array( 'class'             => 'campo-obligatorio',
                                                                         'validationMessage' => 'El tipo de forma de pago es requerido' ) ) )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiformapagotype';
    }
}
