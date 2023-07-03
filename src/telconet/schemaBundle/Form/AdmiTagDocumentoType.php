<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTagDocumentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tagDocumento','text',
                    array(
                        'label'=>'* Tag Documento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Tag Documento es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTag','textarea',
                    array(
                        'label'=>'* Descripcion Tag Documento:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del tag Documento es requerido",)
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitagdocumentotype';
    }
}
