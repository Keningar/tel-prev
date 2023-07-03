<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoEmpresaGrupoType extends AbstractType
{
    private $arrayEmpresas;
    private $boolEdit;
    public function __construct($options) 
    {
        $this->arrayEmpresas = $options['arrayEmpresas'];
        $this->boolEdit = $options['boolEdit'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayEmpresas = array("" => "-- Escoja una empresa --");
        if($this->arrayEmpresas && count($this->arrayEmpresas)>0)
        {
            foreach($this->arrayEmpresas as $key => $value)
            {
                $arrayEmpresas[$value["id"]] = $value["nombre"];
                
            }
        }
        
        $boolEdit = $this->boolEdit;        
        if($boolEdit)
        {
           $builder 
            ->add('id','text',
                    array(
                        'label'=>'* Cod Empresa:',
                        'required'=>true,
                        'attr' => array(
                            'readonly' => 'readonly',
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Empresa es requerido",
                            'maxlength'=>10)
                         )
                 );
        }
        else
        {
            $builder         
                ->add('id', 'choice', 
                        array('choices' => $arrayEmpresas,
                            'label' => '* Cod Empresa:',
                            'required'=>true,
                            'attr' => array('class' => 'campo-obligatorio',
                                'validationMessage'=>"Empresa es requerido",
                                'onChange' => "ajaxCargaDatosEmpresa(this.value);"
                                ) 
                            ));
        }
        
        $builder    
            ->add('nombreEmpresa','text',
                    array(
                        'label'=>'* Nombre Empresa:',
                        'attr' => array(
                            'readonly' => 'readonly',
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre de la Empresa es requerido",
                            'maxlength'=>50)
                         )
                 )	
                
            ->add('razonSocial','text',
                    array(
                        'label'=>'* Razon Social:',
                        'attr' => array(
                            'readonly' => 'readonly',
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Razon Social es requerido",
                            'maxlength'=>50)
                         )
                 )	
                
            ->add('ruc','text',
                    array(
                        'label'=>'* Ruc:',
                        'attr' => array(
                            'readonly' => 'readonly',
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Ruc es requerido",
                            'maxlength'=>13)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infogrupoempresatype';
    }
}
