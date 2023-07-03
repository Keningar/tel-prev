<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoEspacioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoEspacio','text',
                    array(
                        'label'=>'* Nombre Tipo Espacio:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Tipo Espacio es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionTipoEspacio','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Espacio:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Espacio es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipoespaciotype';
    }
}
