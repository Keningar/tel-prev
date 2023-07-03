<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonaEmpleadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('tipoIdentificacion', 'choice', array('attr'        => array('onChange'=>'esRuc()'),    
                                                    'label'       => '* Tipo Identificación:',
                                                    'label_attr'  => array('class' => 'campo-obligatorio'),    
                                                    'choices'     => array('CED' => 'Cedula',
                                                                           'RUC' => 'Ruc',
                                                                          ' PAS' => 'Pasaporte'),
                                                    'required'    => true,
                                                    'empty_value' => 'Seleccione...',
                                                    'empty_data'  => null
                                                     )
            )
        ->add('identificacionCliente', 'text', array('label'      => '* Identificación:', 
                                                     'required'   => true, 
                                                     'label_attr' => array('class' => 'campo-obligatorio'),
                                                     'attr'       => array('maxLength' => 10, 
                                                                           'class'     => 'campo-obligatorio', 
                                                                           'onChange'  => 'buscarPorIdentificacion(this)')
                                                     )
            )
        ->add('nombres', 'text', array('label'      => '* Nombres:', 
                                       'required'   => true,
                                       'label_attr' => array('class' => 'campo-obligatorio'),
                                       'attr'       => array('class'      => 'campo-obligatorio',
                                                             'onChange'   => 'ajustarTexto(this)',
                                                             'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                             'onKeyPress' => 'return validarEspeciales(event)')
                                       )
            )
        ->add('apellidos', 'text', array('label'      => '* Apellidos:',
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
        ->add('estadoCivil', 'choice', array('label'      =>'Estado Civil:',
                                             'choices'    => array('S' => 'Soltero(a)',
                                                                   'C' => 'Casado(a)',
                                                                   'D' => 'Divorciado(a)',
                                                                   'V' => 'Viudo(a)',
                                                                   'U' => 'Unión Libre'
                                                 ),
                                            'empty_value' => 'Seleccione...',
                                            'empty_data'  => null,
                                            'required'    => false
                                             )
            )
        ->add('fechaNacimiento','date', array('years'    => range(1930,2000),
                                              'label'    =>'Fecha Nacimiento:',
                                              'attr'     => array('onChange'=>''),
                                              'required' => false			
                                              )
            )                
        ->add('nacionalidad', 'choice', array('label'      => '* Nacionalidad:',
                                              'label_attr' => array('class' => 'campo-obligatorio'),                 
                                              'choices'    => array('NAC' => 'Nacional',
                                                                    'EXT' => 'Extranjera'),
                                              'required'   => true
                                              )
            )
        ->add('direccion', 'text', array('label'      => '* Dirección:',
                                         'required'   => true,
                                         'label_attr' => array('class' => 'campo-obligatorio'),
                                         'attr'       => array('class'      => 'campo-obligatorio',
                                                               'onChange'   => 'ajustarTexto(this)',
                                                               'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                               'onKeyPress' => 'return validarEspeciales(event)')
                                         )
            )
            ->add('tituloId', 'entity', array('class'       => 'telconet\schemaBundle\Entity\AdmiTitulo',
                                          'property'    => 'codigoTitulo',
                                          'label'       => '* Título:',
                                          'label_attr'  => array('class' => 'campo-obligatorio'),
                                          'required'    => true,
                                          'em'          => 'telconet',
                                          'empty_value' => 'Seleccione...',
                                          'empty_data'  => null                
                                           )
            )
        ;
    }    

    public function getName()
    {
        return 'personaempleadotype';
    }
}