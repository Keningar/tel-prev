<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiUsuarioAccesoType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options)
    {                
        	
        $builder     	     
            ->add('nombreUsuarioAcceso', 'text', 
                   array(
                        'label'=>'Usuario Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )
						
            ->add('descripcionUsuarioAcceso','text',
                    array(
                        'label'=>'Descripcion Usuario Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )				                                              
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiusuarioaccesotype';
    }
}