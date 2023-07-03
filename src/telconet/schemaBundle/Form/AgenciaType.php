<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AgenciaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('cargo')
            //->add('latitud')
            //->add('longitud')  			
            ->add('tipoIdentificacion', 'choice', array(
	            'attr'=>array('onChange'=>'esRuc()'),    
	            'label'=>'* Tipo Identificacion:',
	            'label_attr' => array('class' => 'campo-obligatorio'),    
	            'choices' => array(
		            'CED' => 'Cedula',
		            'RUC' => 'Ruc',
		            'PAS' => 'Pasaporte'
				),
	            'required'    => true,
	            'empty_value' => 'Seleccione...',
	            'empty_data'  => null
            ))   
			
            ->add('identificacionCliente','text',array('label'=>'* Identificacion:', 'required'    => true,    
				'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>10, 'class' => 'campo-obligatorio', 'onChange'=>'buscarPorIdentificacion(this.value)')))
   
            ->add('nombres','text',array('label'=>'* Nombres:', 'required'    => true,
				'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio','onChange'=>'')))
            
			->add('apellidos','text',array('label'=>'* Apellidos:', 'required'    => true,
				'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio','onChange'=>'')))
            
			->add('genero', 'choice', array(
	            'label'=>'* Genero:',
	            'label_attr' => array('class' => 'campo-obligatorio'),    
	            'choices' => array(
		            'M' => 'Masculino',
		            'F' => 'Femenino'
				),
	            'required'    => true,
	            'empty_value' => 'Seleccione...',
	            'empty_data'  => null
            )) 
			
            ->add('estadoCivil', 'choice', array(
	            'label'=>'Estado Civil:',   
	            'choices' => array(
		            'S' => 'Soltero(a)',
		            'C' => 'Casado(a)',
                            'D' => 'Divorciado(a)',
                            'V' => 'Viudo(a)',
                            'U' => 'Unión Libre'                         
	             ),
	            'empty_value' => 'Seleccione...',
	            'empty_data'  => null,
				'required' => false
            ))
			
            ->add('fechaNacimiento','date', array(
				'years' => range(1930,2000),
				'label'=>'Fecha Nacimiento:',
				'attr' => array('onChange'=>''),
				'required' => false			
			))                
            
            ->add('nacionalidad', 'choice', array(
	            'label'=>'* Nacionalidad:',
	            'label_attr' => array('class' => 'campo-obligatorio'),                 
	            'choices' => array(
					'NAC' => 'Nacional',
					'EXT' => 'Extranjera'
	             ),
	            'required'    => true
            ))
			
            ->add('direccion','text',array('label'=>'* Direccion:', 'required'    => true,
				'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio')))
            
			->add('tituloId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiTitulo',
											   'property'=>'codigoTitulo',
											   'label'=>'* Titulo:',
											   'label_attr' => array('class' => 'campo-obligatorio'),
											   'required' => true,
											   'em'=> 'telconet',
											   'empty_value' => 'Seleccione...',
											   'empty_data'  => null                
												))                
        ;
    }    

    public function getName()
    {
        return 'agenciatype';
    }
}