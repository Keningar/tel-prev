<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoNotificacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoNotificacion','text',
                    array(
                        'label'=>'* Nombre Tipo Notificacion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo Notificacion es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTipoNotificacion','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Notificacion:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Notificacion es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitiponotificaciontype';
    }
}
