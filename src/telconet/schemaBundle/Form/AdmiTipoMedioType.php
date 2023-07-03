<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoMedioType extends AbstractType
{
    
    /*
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se corrige los campos para que se muestren correctamente en el twig
    */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigoTipoMedio','text',
                    array(
                        'label'=>'* Codigo Tipo Medio:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Codigo es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('nombreTipoMedio','text',
                    array(
                        'label'=>'* Nombre Tipo Medio:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionTipoMedio','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Medio:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Medio es requerido",)
                         )
                 )
            
            ->add('nombreTipoMedio','text',
                    array(
                        'label'=>'* Nombre Tipo Medio:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo Medio es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipomediotype';
    }
}
