<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoDocumentoGeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreClaseDocumento','text',
                    array(
                        'label'=>'* Nombre Clase Documento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre de la clase de documento es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionClaseDocumento','textarea',
                    array(
                        'label'=>'* Descripcion Clase Documento:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion de la clase documento es requerido",)
                         )
                 )
            ->add('visible','choice',array('label'=>'Es Visible :','choices'=>array('SI'=>'SI','NO'=>'NO')))
        ;
    }
    public function getName()
    {
        return 'telconet_schemabundle_admiclasedocumentotype';
    }
}
