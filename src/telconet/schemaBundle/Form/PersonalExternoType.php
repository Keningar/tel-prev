<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonalExternoType extends AbstractType
{
    /**
     * Método que construye el formulario para el ingreso/actualización de personal externo
     *
     * @version Initial
     *
     * @author Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version 1.1 - 16/10/2019 - Se solicita modificar el campo año de fecha de nacimiento del formulario, debido a que solo se
     *                             muestra hasta el año 2000.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('tipoIdentificacion', 'choice', array('attr'        => array('onChange'=>'esRuc()'),    
                                                    'label'       => '* Tipo Identificación:',
                                                    'label_attr'  => array('class' => 'campo-obligatorio'),    
                                                    'choices'     => array('CED' => 'Cedula',
                                                                           'RUC' => 'Ruc',
                                                                           'PAS' => 'Pasaporte'),
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
        ->add('nombres', 'text', array('label'      => '* Primer y Segundo Nombre:', 
                                       'required'   => true,
                                       'label_attr' => array('class' => 'campo-obligatorio'),
                                       'attr'       => array('class'      => 'campo-obligatorio',
                                                             'onChange'   => 'ajustarTexto(this)',
                                                             'onKeyDown'  => 'cambiarAMayusculas(this)',
                                                             'onKeyPress' => 'return validarEspeciales(event)')
                                       )
            )
        ->add('apellidos', 'text', array('label'      => '* Primer y Segundo Apellido:',
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
        ->add('fechaNacimiento','date', array('years'    => range(1930, date("Y")),
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
        ->add('empresaExterna', 'choice', array('label' => '* Empresa Externa :',
                                                'label_attr' => array('class' => 'campo-obligatorio'),
                                                'required' => true,
                                                'empty_data' => null
                                                )
            )
        ;
    }    

    public function getName()
    {
        return 'personalexternotype';
    }
}
