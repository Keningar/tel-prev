<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiSintomaType extends AbstractType
{
    private $arrayCasos;
    public function __construct($options)
    {
        $this->arrayCasos = $options['arrayTiposCasos'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayTiposCasos = array("" => " Escoja un Tipo de Caso ");
        if($this->arrayCasos && count($this->arrayCasos)>0)
        {
            foreach($this->arrayCasos as $key => $value)
            {
                $arrayTiposCasos[$value["id"]] = $value["nombre"];

            }
        }

        $builder
            ->add('tipoCasoId', 'choice',
                    array('choices'  => $arrayTiposCasos,
                          'required' => false,
                          'label'    => '* Tipo de Caso:')
				)
            ->add('nombreSintoma','text',
                    array(
                        'label'=>'* Nombre Sintoma:',
                        'attr' => array('class'             => 'campo-obligatorio',
                                        'validationMessage' => "Nombre del Sintoma es requerido",
                                        'maxlength'         => 30,
                                        'onKeyPress'        => 'return validarCaracteresEspeciales(event);')
                         )
                 )
            ->add('descripcionSintoma','textarea',
                    array('label'=>'* Descripcion Sintoma:',
                          'attr' => array("col"              => "20",
                                          "row"              => 10,
                                          'class'            => 'campo-obligatorio',
                                          'validationMessage'=> "Descripcion del Sintoma es requerido",
                                          'onKeyPress'       => 'return validarCaracteresEspeciales(event);')
                         )
                 )	
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admisintomatype';
    }
}
