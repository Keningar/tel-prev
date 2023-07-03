<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProductoType extends AbstractType
{

    var $arrayNombreTecnico = array();
    
    /**
     * __construct
     *
     * Método de configuración inicial de la clase AdmiProductoType                               
     *      
     * @param array $arrayParametros
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-04-2016
     */
    public function __construct($arrayParametros = null)
    {
        if( $arrayParametros )
        {
            if( !empty($arrayParametros['arrayNombreTecnico']) )
            {
                $this->arrayNombreTecnico = $arrayParametros['arrayNombreTecnico'];
            }
        }
    }
    
    
    /**
     * buildForm
     *
     * Método que construye el formulario para el catálogo de productos                        
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @param array                $arrayParametros
     *
     * @author Programacion Inicial
     * @version 1.0 
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-04-16 - Se remueven 2 campos (ctaContablePro y ctaContableProNc),
     *                           se agregan 1 (esConcentrador) y
     *                           se remueve NombreTécnico porque se obtendrá información desde la BD
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 21-04-2016 - Se modifica que el nombre técnico obtenga el array enviado
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.3 2016-05-10 - Se solucionan conflictos y remuevo el Nombre Tecnico
     *                            porqe se carga automáticamente estando en el formulario
     * 
     * @author Modificado: Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.4 2016-10-05 - Se agrega campos comisionVenta, comisionMantenimiento
     * 
     * @author Modificado: Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.5 2017-03-13 - Se agrega campo : requiere comisionar (SI/NO)
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $comboPref = array( 'SI'=>'SI' , 'NO'=>'NO');
	
        $builder
        ->add('codigoProducto', 'text', array( 'label' => '* Codigo:',
                                               'attr'  => array( 'class'             => 'campo-obligatorio',
                                                                 'validationMessage' => "Codigo de Producto es requerido",
                                                                 'maxlength'         => 4 ) ) )
	    ->add('descripcionProducto', 'text', array( 'label' => '* Descripcion:',
                                                    'attr'  => array( 'class'             => 'campo-obligatorio',
                                                                      'validationMessage' => "Descripcion es requerido",
                                                                      'maxlength'         => 30 ) ) )
        ->add('tipo', 'choice', array('label'       => '* Tipo:',
                                      'label_attr'  => array('class' => 'campo-obligatorio'),
                                      'attr'        => array('class' => 'campo-obligatorio', 
                                                             'width' => '300px'),
                                      'choices'     => array('B' => 'Bien',
                                                             'S' => 'Servicio'),
                                      'required'    => true,
                                      'empty_value' => 'Seleccione...',
                                     )
              )
        ->add('frecuencia', 'choice', array('label'       => '* Frecuencia:',
                                      'label_attr'  => array('class' => 'campo-obligatorio'),
                                      'attr'        => array('class' => 'campo-obligatorio',
                                                             'width' => '300px'),
                                      'choices'     => array('Mensual' => 'Mensual',
                                                             'Unica' => 'Unica'),
                                      'required'    => true,
                                      'empty_value' => 'Seleccione...',
                                     )
              )
	    ->add('instalacion','text',array('label'=>'* Instalacion:',
                                         'attr' => array('class'            => '',
                                                         'validationMessage'=>"Instalacion es requerido",
                                                         'min'              =>"1",
                                                         'max'              =>"10",
                                                         'data-max-msg'     =>"Enter value between 1 and 10")))			
	    ->add('esPreferencia', 'choice', array(
                        'label'   =>'* Es Preferencia:',    
                        'choices' => $comboPref)
                  )            
             ->add('requierePlanificacion', 'choice', array(
                        'label'   =>'* Requiere Planificacion:',    
                        'choices' => $comboPref)
                  )
             ->add('requiereInfoTecnica', 'choice', array(
                        'label'   =>'* Requiere Info Tecnica:',    
                        'choices' => $comboPref)
                  )
             ->add('esEnlace', 'choice', array(
                        'label'   =>'* Es Enlace:',    
                        'choices' => $comboPref)
                  )
             ->add('esConcentrador', 'choice', array(
                        'label'   =>'* Es Concentrador:',    
                        'choices' => $comboPref)
                  )
             ->add('comisionVenta','text',array(
                        'label'    => '* Comisión Venta:',
                        'required' => false)
                  )
             ->add('comisionMantenimiento','text',array(
                        'label'    => '* Comisión Mantenimiento:',
                        'required' => false)
                  )
             ->add('requiereComisionar', 'choice', array(
                        'label'   =>'* Requiere Comisionar:',    
                        'choices' => $comboPref)
                  )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiproductotype';
    }
}
