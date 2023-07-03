<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('nombres','text',array('label'=>'* Nombres:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio','onChange'=>'')))
            ->add('apellidos','text',array('label'=>'* Apellidos:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio','onChange'=>'')))
            ->add('tituloId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiTitulo',
											   'property'=>'codigoTitulo',
											   'label'=>'* Titulo:',
                                                                                           'label_attr' => array('class' => 'campo-obligatorio'),
											   'required' => true,
											   'em'=> 'telconet',
											   'empty_value' => 'Seleccione...',
                                                                                           'empty_data'  => null                
												));
    }

    public function getName()
    {
        return 'contactotype';
    }
}
