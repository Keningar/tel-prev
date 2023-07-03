<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoDocumentoType extends AbstractType
{

	private $validaFile ;
    private $arrayTipoDocumentos ;
    private $arrayTagDocumentos ;
    private $validaFechaPublicacionHasta ;
    private $fechaPublicacionHasta ;
	
	public function __construct($options) 
    {

        $this->validaFile = $options['validaFile'];
        $this->validaFechaPublicacionHasta = $options['validaFechaPublicacionHasta'];
        $this->arrayTipoDocumentos = $options['arrayTipoDocumentos'];
        $this->arrayTagDocumentos  = $options['arrayTagDocumentos'];

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            if($this->validaFile){
                    $classFile = 'campo-obligatorio';
                    $labelFile = '* Archivo Digital:';
            }else{
                    $classFile = '';
                    $labelFile = 'Archivo Digital:';
            }
            
            
            if($this->validaFechaPublicacionHasta){
                    $classFecha = 'campo-obligatorio';
                    $labelFecha = '* Fecha de Caducidad:';
            }else{
                    $classFecha = '';
                    $labelFecha = 'Fecha de Caducidad:';
            }

            $factory = $builder->getFormFactory(); 
            	           
        $builder                    
           ->add('imagenes', 'collection', array(
                'type'      => 'file',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'options'=>array(
                'required'  => false,
                'by_reference' => false,
                'attr'  => array('class' => $classFile),
                )))
         
            ->add('tipos','collection', array(
                'type'      => 'choice',
                'allow_add' => true,                
                'allow_delete' => true,
                'prototype' => true,
                'options'=>array('choices' => $this->arrayTipoDocumentos,'preferred_choices' => array(2),
                'required'  => true,
                'by_reference' => false))) 
            
            ->add('tags','collection', array(
                'type'      => 'choice',
                'allow_add' => true,                
                'allow_delete' => true,
                'prototype' => true,
                'options'=>array('choices' => $this->arrayTagDocumentos,'preferred_choices' => array(2),
                'required'  => true,
                'by_reference' => false)))
            ->add('fechasPublicacionHasta', 'collection', array(
                'type'      => 'date',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'options'=>array(
                                'required'  => true,
                                'years' => range(date('Y'), date('Y')+20), 
                                'input' => "string", 'data' => $this->fechaPublicacionHasta, 
                                'empty_value' => array('year' => 'Año', 'month' => 'Mes', 'day' => 'Dia'),
                                'label_attr' => array('class' => $labelFecha), 
                                'attr' => array('class' => $classFecha, 'onChange' => '')
                )))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoDocumento'
        ));
    }

    public function getName()
    {
        return 'infodocumentotype';
    }
}
