<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CallActivityType extends AbstractType
{
    /**
     * Documentacion para 'buildForm'
     * 
     * Método que crea el formulario
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09/12/2015 - Se agrega atributo 'onchange' para mostrar la opción de facturación de la actividad.        
     */   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', 'entity', array(
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiFormaContacto',
                                            'em' => 'telconet',
                                            'query_builder' => function ($repositorio) {
                                                return $repositorio->createQueryBuilder('entity')
                                                        ->where("entity.estado not like 'Eliminado'");
                                            },
                                            'empty_value' => 'Escoja una opcion',
                                            'required' => true,
                                            'label' => '* Tipo:',
                                            'attr' => array('class'=>'form_new_select')
            ))
            ->add('claseDocumento', 'entity', array(
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiClaseDocumento',
                                            'em' => 'telconet_comunicacion',
                                            'query_builder' => function ($repositorio) {
                                                return $repositorio->createQueryBuilder('entity')
                                                        ->where("entity.estado not like 'Eliminado' and entity.visible = 'SI'");
                                            },
                                            'empty_value' => 'Escoja una opcion',
                                            'required' => true,
                                            'label' => '* Tipo:',
                                            'attr' => array('onChange'=>"mostrarEmpresa(this); mostrarEsFacturable();",
                                                            'class'   =>'form_new_select')
            ))
            ->add('observacion','textarea',array('label'=>'* Version Inicial:','attr' => array(
                  'cols'=>'69','rows'=>'4', 'class' => 'form_new_textarea')))
        ;
    }
    public function getName()
    {
        return 'telconet_schemabundle_callactivitytype';
    }
}
