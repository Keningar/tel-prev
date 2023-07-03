<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class InfoSubredesType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoSubred                               
     *      
     * @param FormBuilderInterface $objBuilder
     * @param array                $arrayOptions
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    
    public function buildForm(FormBuilderInterface $objBuilder, array $arrayOptions)
    {
       

        $objBuilder
       
        ->add('subred','text',
                array(
                    'label'=>'Subred:',
                    'attr' => array(
                                    'class'             => 'campo-obligatorio',
                                    'validationMessage' => "Campo Subred es requerido",
                                    'disabled'          => "true",
                                    'maxlength'         => 30)
                     )
             )
        ->add('uso','text',
                array(
                    'label'=>'Uso:',
                    'attr' => array(
                                    'class'             => 'campo-obligatorio',
                                    'validationMessage' => "Uso de la Subred es requerido",
                                    'onkeypress'        => 'return validador(event,"")',
                                    'maxlength'         => 10)
                    )
            )
          
        ->add('tipo','text',
                array(
                    'label'=>'Tipo:',
                    'attr' => array(
                                    'class'             => 'campo-obligatorio',
                                    'validationMessage' => "Tipo de Subred es requerido",
                                    'onkeypress'        => 'return validador(event,"")',
                                    'maxlength'         => 10)
                    )
            )
        ->add('estado','text',
                    array(
                        'label'=>'Estado:',
                        'attr' => array(
                                        'class'             => 'campo-obligatorio',
                                        'validationMessage' => "Estado de Subred es requerido",
                                        'onkeypress'        => 'return validador(event,"")',
                                        'maxlength'         => 10,
                                        'name'              => 'comboEstados',
                                        'id'                => 'comboEstados'
                                        
                                        )
                        )
                )
       
       ;                                  
    
    }

    public function getName()
    {
        return 'telconet_schemabundle_infosubredtype';
    }
}
