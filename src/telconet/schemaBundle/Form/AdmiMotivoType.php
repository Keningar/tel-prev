<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiMotivoType extends AbstractType
{    
    protected $motivos;
    public function __construct($options) {    
        $this->motivos   = $options['motivos'];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreMotivo','choice',
                    array('label'=>'* Motivos:',
                        'required' => true,
                        'empty_value' => 'Seleccione',	                        
                        'choices' =>  $this->motivos,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
                          )				
                )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admimotivotype';
    }
}
