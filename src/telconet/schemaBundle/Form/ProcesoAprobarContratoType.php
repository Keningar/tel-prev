<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProcesoAprobarContratoType extends AbstractType
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
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $this->origenIngresos = $options['origenIngresos'];
        $this->fechaNacimiento = $options['fechaNacimiento'];
        if($options['fechaNacimiento'])
            $this->fechaNacimiento = $options['fechaNacimiento']->format('Y-m-d');
        else
            $this->fechaNacimiento = '';
        //echo $this->fechaNacimiento;die;
        $this->titulo = $options['titulo'];
        $this->representanteLegal = $options['representanteLegal'];
        // Campos Nuevos CONTRIBUYENTE_ESPECIAL,PAGA_IVA, NUMERO_CONADIS
        $this->empresaId             = $options['empresaId'];
        $this->contribuyenteEspecial = $options['contribuyenteEspecial'];
        $this->oficinaFacturacion    = $options['oficinaFacturacion'];
        $this->numeroConadis         = $options['numeroConadis'];  
        $this->tieneNumeroConadis    = $options['tieneNumeroConadis'];  
        $this->esPrepago             = $options['esPrepago'];   
        $this->pagaIva               = $options['pagaIva'];   
        $this->leftStyle             = 'margin-left: 20px; width:155px;';
    }
    /**
     * buildForm
     *
     * Metodo encargado de crear la estructura de un formulario de la tabla 'InfoPersona'
     *
     * @param FormBuilderInterface  $builder
     * @param array                 $options
     * 
     * @version 1.0 Versión Inicial
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 01-07-2016 - Se incluye una validación para el caso de que el Cliente sea de Tipo Natural
     *                           Se agregue obligatoriedad en el campo Origen Ingresos.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {     
        $builder
             ->add('idOficinaFacturacion','entity',array( 
                  'class'         => 'telconet\schemaBundle\Entity\InfoOficinaGrupo',
                  'mapped'        => false,
                  'property'      => 'nombreOficina',
                  'label'         => '* Oficina Facturacion:',
                  'label_attr'    => array('class' => 'campo-obligatorio'),
                  'required'      => true,
                  'em'            => 'telconet',
                  'empty_value'   => 'Seleccione...',
                  'data'          => $this->oficinaFacturacion,
                  'empty_data'    => null ,               
		          'query_builder' => function (EntityRepository $er) 
                                    {
				        return $er->createQueryBuilder('info_oficina')
					       ->select('oficina')
					       ->from('telconet\schemaBundle\Entity\InfoOficinaGrupo','oficina')
					       ->where("oficina.estado = ?1")
					       ->andWhere("oficina.esOficinaFacturacion = ?2")
					       ->andWhere("oficina.empresaId = ?3")
                                               ->setParameter(1, 'Activo')
                                               ->setParameter(2, 'S')
                                               ->setParameter(3, $this->empresaId);

				    }                                             
                                              
                 ))                         
            ->add('tipoIdentificacion', 'choice', array(
                'attr' => array('onChange' => 'esRuc();validarIdentificacionTipo();'),
                'label' => '* Tipo Identificacion:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'CED' => 'Cedula',
                    'RUC' => 'Ruc',
                    'NIT' => 'Nit',
                    'DPI' => 'Dpi',
                    'PAS' => 'Pasaporte'
                ),
                'data' => $this->tipoIdentificacion,
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('identificacionCliente', 'text', array('label' => '* Identificacion:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('value' => $this->identificacion, 'maxLength' => 10,
                    'onChange' => "validarIdentificacionTipo();", 'class' => 'campo-obligatorio')))
            ->add('tipoTributario', 'choice', array('label'       => ' Tipo Tributario:',
                                                    'label_attr'  => array('style'    => $this->leftStyle),
                                                    'attr'        => array('onChange' => 'esTipoNatural();',
                                                                           'style'    => '65px'),
                'choices' => array(
                    'NAT' => 'Natural',
                    'JUR' => 'Juridico'
                ),
                'data' => $this->tipoTributario,
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('nombres', 'text', array('label' => '* Nombres:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('value' => $this->nombres, 'class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('apellidos', 'text', array('label' => '* Apellidos:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('value' => $this->apellidos, 'class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('razonSocial', 'text', array('required' => false, 'label' => '* Razon Social:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('value' => $this->razonSocial, 'class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('genero', 'choice', array(
                'label' => '* Genero:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'M' => 'Masculino',
                    'F' => 'Femenino'
                ),
                'data' => $this->genero,
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('estadoCivil', 'choice', array(
                'label' => '* Estado Civil:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'S' => 'Soltero(a)',
                    'C' => 'Casado(a)',
                    'U' => 'Union Libre',
                    'D' => 'Divorciado(a)',
                    'V' => 'Viudo(a)'
                ),
                'data' => $this->estadoCivil,
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('fechaNacimiento', 'date', array('years' => range(date('Y')-100, date('Y')-18), 'label' => '* Fecha Nacimiento:', 'input' => "string", 'data' => $this->fechaNacimiento, 'empty_value' => array('year' => 'Anio', 'month' => 'Mes', 'day' => 'Dia'),
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('representanteLegal', 'text', array('required' => false, 'label' => 'Representante Legal:',
                'label_attr' => array('class' => ''), 'attr' => array('value' => $this->representanteLegal, 'class' => 'campo-obligatorio')))
            ->add('direccionTributaria', 'textarea', array('required' => true, 'label' => '* Direccion Tributaria:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('default' => $this->direccionTributaria, 'class' => 'campo-obligatorio', 'maxlength' => 150, 'cols' => 26, 'rows' => 5)))
            ->add('tipoEmpresa', 'choice', array('attr' => array('onChange' => 'esEmpresa()'),
                'label' => 'Tipo Empresa:',
                'label_attr' => array('class' => ''),
                'choices' => array(
                    'Publica' => 'Publica',
                    'Privada' => 'Privada'
                ),
                'data' => $this->tipoEmpresa,
                'required' => false,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('nacionalidad', 'choice', array(
                'label' => '* Nacionalidad:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'NAC' => 'Nacional',
                    'EXT' => 'Extranjera'
                ),
                'data' => $this->nacionalidad,
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('direccion', 'textarea', array('label' => '* Direccion:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('value' => $this->direccion, 'class' => 'campo-obligatorio', 'maxlength' => 50, 'cols' => 26, 'rows' => 5)))
            ->add('tituloId', 'entity', array('class' => 'telconet\schemaBundle\Entity\AdmiTitulo',
                'property' => 'codigoTitulo',
                'label' => '* Titulo:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'required' => true,
                'em' => 'telconet',
                'data' => $this->titulo,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            //cambios DINARDARP - se agrega campo origenes de ingresos
            ->add('origenIngresos', 'choice', array(
                'label' => '* Origines de Ingresos:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'B' => 'Empleado Público',
                    'V' => 'Empleado Privado',
                    'I' => 'Independiente',
                    'A' => 'Ama de casa o estudiante',
                    'R' => 'Rentista',
                    'J' => 'Jubilado',
                    'M' => 'Remesas del exterior'
                ),
                'data' => $this->origenIngresos,
                'required' => false,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
             ->add('contribuyenteEspecial', 'choice', array(
                                                           'label'       => '* Contribuyente Especial:',
                                                           'required'    => true,                                                           
                                                           'empty_data'  => null,
                                                           'label_attr'  => array('class' => ''),
                                                           'choices'     => array('S' => 'Si','N' => 'No'), 
                                                           'data'        => $this->contribuyenteEspecial,
                                                           'empty_value' => 'Seleccione...',
                                                           'empty_data' => null
            ))            
            ->add('pagaIva', 'choice', array(
                                             'label'       => '* Paga Iva:',
                                             'label_attr'  => array('class' => ''),
                                             'required'    => true,                                            
                                             'empty_data'  => null,                                        
                                             'choices'     => array('S' => 'Si','N' => 'No'),
                                             'data'        => $this->pagaIva,   
                                             'empty_value' => 'Seleccione...'
            ))	    

            ->add('esPrepago', 'choice', array(
                                               'mapped'      => false,            
                                               'label'       => '* Es Prepago:',
                                               'label_attr'  => array('class' => ''),                                               
                                               'required'    => true,                                               
                                               'empty_data'  => null,                                        
                                               'choices'     => array('S' => 'Si','N' => 'No'),
                                               'data'        => $this->esPrepago,
                                               'empty_value' => 'Seleccione...'
             ))	 
		
            ->add('tieneCarnetConadis', 'choice', array('attr'        => array('onChange' => 'tieneCarnetConadis()'),
                                                        'label'       => ' Tiene Carnet Conadis:',
                                                        'label_attr'  => array('class' => ''),                                                        
                                                        'required'    => true,
                                                        'mapped'      => false,                                                        
                                                        'empty_data'  => null,
                                                        'choices'     => array('S' => 'Si',
                                                                               'N' => 'No'
                                                                              ),
                                                        'data'        => $this->tieneNumeroConadis,
                                                        'empty_value' => 'Seleccione...'
             ))   
            ->add('numeroConadis', 'text', array('label'      => 'Numero Carnet CONADIS:',
                                                'label_attr' => array('class' => ''),
                                                'required'   => false,
                                                'data'        => $this->numeroConadis,
						                        'attr'       => array('class' => '', 'onChange' => '')
                                                
             ))          
        ;
    }

    public function getName()
    {
        return 'procesoaprobarcontratotype';
    }
}
