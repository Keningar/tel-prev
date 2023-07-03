<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoPlanCabType extends AbstractType
{
    private $empresaId;
    
    public function __construct($options) 
    {
        $this->empresaId = $options['empresaId'];
    }
	 
    
    /**
     * buildForm
     * 
     * Función que construye Formulario a pintar en pantalla de creación de planes MD
     * 
     * @param FormBuilderInterface  $builder 
     * @param array                 $options
     * @return FormBuilderInterface $builder
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 07-05-2019   Se agrega propiedad de tamaño máximo a campo código
     * @since 1.0
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $empresa = $this->empresaId;
        
        $builder            
            ->add('codigoPlan','text',array('label'=>' Codigo:',
                                            'attr'             => 
                                            array('maxlength'  => 8)
                                           ))         
            ->add('nombrePlan','text',array('label'=>'* Nombre:','attr' => array('class' => 'campo-obligatorio')))
            ->add('descripcionPlan','text',array('label'=>'* Descripción:','attr' => array('class' => 'campo-obligatorio')))
            ->add('descuentoPlan','text',array('label'=>'Descuento:'))          
        ;
    }

    public function getName()
    {
        return 'infoplancabtype';
    }
}
