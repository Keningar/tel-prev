<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use telconet\schemaBundle\Entity\SeguPerfilPersona;

class SeguPerfilPersonaType extends AbstractType
{
    private $idsPersonas;
    private $si_edita;
    public function __construct($options) 
    {
        $this->idsPersonas = $options['idsPersonas'];
        $this->si_edita = $options['si_edita'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opciones = array('ACTIVE' => 'Activo', 'EDITED' => 'Modificado', 'DELETED' => 'Eliminado');
        
        $arrayPersonas = array("" => "-- Escoja una persona --");
        if($this->idsPersonas && count($this->idsPersonas)>0)
        {
            foreach($this->idsPersonas as $key => $value)
            {
                $arrayPersonas[$value["id"]] = $value["nombres"]." ".$value["apellidos"];
                
            }
        }
        
        if($this->si_edita)
        {
            /*$builder
                ->add('personaId', 
                                'choice', array('choices' => $arrayPersonas,
                                                'label' => 'Persona:',
                                                'required'=>true
                                                ))
                ;*/
        }
        else
        {
            $builder
                ->add('personaId', 'hidden')
                ;
        }
            
    }

    public function getName()
    {
        return 'telconet_schemaBundle_seguperfilpersonatype';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'telconet\schemaBundle\Entity\SeguPerfilPersona',
        );
    }
}
