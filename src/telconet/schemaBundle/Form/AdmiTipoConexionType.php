<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoConexionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoConexion','text',
                    array(
                        'label'=>'* Nombre Tipo Conexion:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionTipoConexion','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Medio:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Conexion es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipoconexiontype';
    }
}
