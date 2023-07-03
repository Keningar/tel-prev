<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiEmpresaExternaType extends AbstractType
{

    protected $intMaxLongitudIdentificacion;
    /*
     * 
     * 
    */
    public function __construct($arrayOptions) 
    {
        $this->intMaxLongitudIdentificacion = 0;
        
        if(!empty($arrayOptions) && isset($arrayOptions['intMaxLongitudIdentificacion']))
        {
            $this->intMaxLongitudIdentificacion = $arrayOptions['intMaxLongitudIdentificacion'];
        }
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intMaxLongitudIdentif = ($this->intMaxLongitudIdentificacion > 0) ? $this->intMaxLongitudIdentificacion : 13;
        $builder  
            ->add('identificacionCliente','text',array('label'      => '* RUC:', 
                                                       'required'   => true,    
                                                       'label_attr' => array('class' => 'campo-obligatorio'),
                                                       'attr'       => array('maxLength' => $intMaxLongitudIdentif, 
                                                                             'class'     => 'campo-obligatorio',
                                                                             'onChange'  => 'buscarPorIdentificacion(this)')
                                                       )
                )
            ->add('razonSocial', 'text', array('label'      => '* Razón Social:',
                                               'required'   => true,
                                               'label_attr' => array('class' => 'campo-obligatorio'),
                                               'attr'       => array('class'      => 'campo-obligatorio',
                                                                     'onChange'   => 'ajustarTexto(this)',
                                                                     'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                                     'onKeyPress' => 'return validarEspeciales(event)')
                                               )
                )
            ->add('nombres', 'text', array('label'      => '* Nombre Empresa:',
                                           'required'   => true,
                                           'label_attr' => array('class' => 'campo-obligatorio'),
                                           'attr'       => array('class'      => 'campo-obligatorio',
                                                                 'onChange'   => 'ajustarTexto(this)',
                                                                 'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                                 'onKeyPress' => 'return validarEspeciales(event)')
                                           )
                )
			->add('genero', 'choice', array('label'       => '* Género:',
                                            'label_attr'  => array('class' => 'campo-obligatorio'),    
                                            'choices'     => array('M' => 'Masculino',
                                                                   'F' => 'Femenino'),
                                            'required'    => true,
                                            'empty_value' => 'Seleccione...',
                                            'empty_data'  => null
                                            )
                ) 
            ->add('fechaNacimiento', 'date', array('years'    => range(1800, date('Y')),
                                                   'label'    => 'Fecha Institución:',
                                                   'required' => false
                                                   )
                )                
            ->add('nacionalidad', 'choice', array('label'      => '* Nacionalidad:',
                                                  'label_attr' => array('class' => 'campo-obligatorio'),                 
                                                  'choices'    => array('NAC'   => 'Nacional',
                                                                        'EXT'   => 'Extranjera'),
                                                  'required'   => true
                                                  )
                )
            ->add('direccion','text',array('label'      => '* Dirección:', 
                                           'required'   => true,
                                           'label_attr' => array('class' => 'campo-obligatorio'),
                                           'attr'       => array('class'      => 'width',
                                                                 'onChange'   => 'ajustarTexto(this)',
                                                                 'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                                 'onKeyPress' => 'return validarEspeciales(event)')
                                          )
                )
        ;
    }    

    public function getName()
    {
        return 'admiempresaexternatype';
    }
}
