<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoEncuestaType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {          
        $builder
            ->add('firma','text',
                    array(
                        'label'=>'* Firma:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Firma es requerido")
                         )
                 )
        ;
    }
    

    public function getName()
    {
        return 'telconet_schemabundle_infoencuestatype';
    }
}
