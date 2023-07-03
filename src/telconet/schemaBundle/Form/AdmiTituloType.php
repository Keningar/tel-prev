<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTituloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opciones = array('' => 'Seleccione un Genero', 'M' => 'Masculino', 'F' => 'Femenino');
        
        $builder
            ->add('codigoTitulo','text',
                    array(
                        'label'=>'* Codigo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Codigo del Titulo es requerido",
                            'maxlength'=>4)
                         )
                 )
                
            ->add('descripcionTitulo','text',
                    array(
                        'label'=>'* Descripcion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Titulo es requerido",
                            'maxlength'=>80)
                         )
                 )
                
            ->add('genero', 
                                'choice', array('choices' => $opciones,
                                                'label' => 'Genero:',
                                                'required'=>false
                                                ))
                ;
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admititulotype';
    }
}
