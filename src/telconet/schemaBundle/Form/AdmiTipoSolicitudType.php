<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoSolicitudType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder             
            ->add('descripcionSolicitud','text',array('label'=>'Descripcion:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>100)))
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitiposolicitudtype';
    }
}
