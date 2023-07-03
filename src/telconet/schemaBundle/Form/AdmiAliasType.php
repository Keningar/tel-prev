<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiAliasType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
                      
            ->add('valor','text',
                    array(
                        'label'=>'* Alias :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Alias es requerido",
                            'maxlength'=>50)
                         )
                 );                  
    }

    public function getName()
    {
        return 'telconet_schemabundle_admialiastype';
    }
}
