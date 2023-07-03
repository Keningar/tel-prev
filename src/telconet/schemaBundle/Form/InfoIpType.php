<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoIpType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {                
        	
        $builder     	     
            ->add('ip', 'text', 
                   array(
                        'label'=>'Ip del Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )
						
            ->add('mascara','text',
                    array(
                        'label'=>'Mascara Subred:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )				                         
             ->add('gateway','text',
                    array(
                        'label'=>'Gateway:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>30)
                         )
                 )	            
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoiptype';
    }
}
