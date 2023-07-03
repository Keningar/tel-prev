<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConvertirType extends AbstractType
{
	private $identificacion;
    
    public function __construct($options) 
    {
        $this->identificacion = $options['identificacion'];
        $this->razonSocial = $options['razonSocial'];
        $this->direccion = $options['direccion'];
        $this->nombres = $options['nombres'];
        $this->apellidos = $options['apellidos'];
        $this->tipoEmpresa = $options['tipoEmpresa'];
        $this->tipoIdentificacion = $options['tipoIdentificacion'];
        $this->tipoTributario = $options['tipoTributario'];
        $this->nacionalidad = $options['nacionalidad']; 
        $this->direccionTributaria = $options['direccionTributaria'];
        $this->calificacionCrediticia = $options['calificacionCrediticia'];
        $this->genero = $options['genero'];
        $this->estadoCivil = $options['estadoCivil'];
        $this->fechaNacimiento = $options['fechaNacimiento'];
        if ($options['fechaNacimiento'])
            $this->fechaNacimiento=$options['fechaNacimiento']->format('Y-m-d');
        else
            $this->fechaNacimiento='';
		//echo $this->fechaNacimiento;die;
        $this->titulo = $options['titulo'];
        $this->representanteLegal = $options['representanteLegal'];
        //echo $this->direccionTributaria;
		//echo $this->representanteLegal;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	 //echo $this->direccionTributaria;
        $builder      
            ->add('tipoIdentificacion', 'choice', array(
            'attr'=>array('onChange'=>'esRuc();validarIdentificacionTipo();'),    
            'label'=>'* Tipo Identificacion:',
            'label_attr' => array('class' => 'campo-obligatorio'),    
            'choices' => array(
            'CED' => 'Cedula',
            'RUC' => 'Ruc',
            'PAS' => 'Pasaporte'
             ),
            'data' => $this->tipoIdentificacion,
            'required'    => true,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            ))        
            ->add('identificacionCliente','text',array('label'=>'* Identificacion:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('value'=>$this->identificacion,'maxLength'=>10,
			'onChange'=>"validarIdentificacionTipo();", 'class' => 'campo-obligatorio')))
            ->add('tipoTributario', 'choice', array(
            'label'=>'* Tipo Tributario:',
            'label_attr' => array('class' => 'campo-obligatorio'),                 
            'choices' => array(
            'NAT' => 'Natural',
            'JUR' => 'Juridico'
             ),
            'data' => $this->tipoTributario,
            'required'    => true,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            ))
            ->add('nombres','text',array('label'=>'* Nombres:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('value'=>$this->nombres,'class' => 'campo-obligatorio','onChange'=>'')))
            ->add('apellidos','text',array('label'=>'* Apellidos:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('value'=>$this->apellidos,'class' => 'campo-obligatorio','onChange'=>'')))
            ->add('razonSocial','text',array('required' => false,'label'=>'* Razon Social:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('value'=>$this->razonSocial,'class' => 'campo-obligatorio','onChange'=>'')))
            ->add('genero', 'choice', array(
            'label'=>'* Genero:',
            'label_attr' => array('class' => 'campo-obligatorio'),    
            'choices' => array(
            'M' => 'Masculino',
            'F' => 'Femenino'
             ),
            'data' => $this->genero,
            'required'    => true,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            )) 
            ->add('estadoCivil', 'choice', array(
            'label'=>'* Estado Civil:',
            'label_attr' => array('class' => 'campo-obligatorio'),    
            'choices' => array(
            'S' => 'Soltero(a)',
            'C' => 'Casado(a)',
			'U' => 'Union Libre',
			'D' => 'Divorciado(a)',
			'V' => 'Viudo(a)'			
             ),
            'data' => $this->estadoCivil,
            'required'    => true,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            ))
            ->add('fechaNacimiento','date',array('years' => range(1921,2000),'label'=>'* Fecha Nacimiento:','input'=>"string",'data' => $this->fechaNacimiento,'empty_value' => array('year' => 'Anio', 'month' => 'Mes', 'day' => 'Dia'),
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('class' => 'campo-obligatorio','onChange'=>'')))                
            ->add('representanteLegal','text',array('required'    => false,'label'=>'Representante Legal:',
            'label_attr' => array('class' => ''),'attr' => array('value'=>$this->representanteLegal,'class' => 'campo-obligatorio')))
            ->add('direccionTributaria','textarea',array('required'    => true,'label'=>'* Direccion Tributaria:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('default'=>$this->direccionTributaria,'class' => 'campo-obligatorio','maxlength'=>150,'cols'=>26, 'rows'=>5)))                
            ->add('tipoEmpresa', 'choice', array('attr'=>array('onChange'=>'esEmpresa()'),   
            'label'=>'Tipo Empresa:',
            'label_attr' => array('class' => ''),                 
            'choices' => array(
            'Publica' => 'Publica',
            'Privada' => 'Privada'
             ),
            'data' => $this->tipoEmpresa,
            'required'    => false,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            ))
            ->add('nacionalidad', 'choice', array(
            'label'=>'* Nacionalidad:',
            'label_attr' => array('class' => 'campo-obligatorio'),                 
            'choices' => array(
            'NAC' => 'Nacional',
            'EXT' => 'Extranjera'
             ),
            'data' => $this->nacionalidad,
            'required'    => true,
            'empty_value' => 'Seleccione...',
            'empty_data'  => null
            ))
            ->add('direccion','textarea',array('label'=>'* Direccion:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('value'=>$this->direccion,'class' => 'campo-obligatorio','maxlength'=>50,'cols'=>26, 'rows'=>5)))
            ->add('tituloId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiTitulo',
											   'property'=>'codigoTitulo',
											   'label'=>'* Titulo:',
                                                                                           'label_attr' => array('class' => 'campo-obligatorio'),
											   'required' => true,
											   'em'=> 'telconet',
                                                                                           'data' => $this->titulo,
											   'empty_value' => 'Seleccione...',
                                                                                           'empty_data'  => null                
												))                
        ;
    }

    public function getName()
    {
        return 'convertirtype';
    }
}
