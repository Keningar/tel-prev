<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoDocumentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('extensionTipoDocumento','text',
                    array(
                        'label'=>'* Extension del tipo de documento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Extension del tipo de documento",
                            'maxlength'=>30)
                         )
                 )
            ->add('tipoMime','text',
                    array(
                        'label'=>'* Mime:',
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Tipo Mime es requerido",)
                         )
                 )
            ->add('descripcionTipoDocumento','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Comunicacion:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Documento es requerido",)
                         )
                 )     
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipodocumentotype';
    }
}
