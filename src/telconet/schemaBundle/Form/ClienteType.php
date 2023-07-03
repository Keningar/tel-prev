<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ClienteType extends AbstractType
{

    private $empresaId;
    
    public function __construct($options) 
    {
        $this->empresaId          = $options['empresaId'];
        $this->oficinaFacturacion = $options['oficinaFacturacion'];
        $this->tieneNumeroConadis = $options['tieneNumeroConadis'];  
        $this->esPrepago          = $options['esPrepago'];   
        $this->pagaIva            = $options['pagaIva'];   
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $empresa               = $this->empresaId;
        $intOficinaFacturacion = $this->oficinaFacturacion;		
        $strTieneNumeroConadis = $this->tieneNumeroConadis;
        $strEsPrepago          = $this->esPrepago ;
        $strPagaIva            = $this->pagaIva ;

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
                  'data'          => $intOficinaFacturacion,
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
                    'PAS' => 'Pasaporte'
                ),
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('identificacionCliente', 'text', array('label' => '* Identificacion:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('maxLength' => 10,
                    'class' => 'campo-obligatorio', 'onChange' => 'validaIdentificacion(true);')))
            ->add('tipoTributario', 'choice', array(
                'label' => '* Tipo Tributario:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'NAT' => 'Natural',
                    'JUR' => 'Juridico'
                ),
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('nombres', 'text', array('label' => '* Nombres:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('apellidos', 'text', array('label' => '* Apellidos:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('razonSocial', 'text', array('required' => false, 'label' => '* Razon Social:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio', 'onChange' => '')))
            ->add('genero', 'choice', array(
                'label' => '* Genero:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'choices' => array(
                    'M' => 'Masculino',
                    'F' => 'Femenino'
                ),
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
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('fechaNacimiento', 'date', array('empty_value' => array('year' => 'Anio', 'month' => 'Mes', 'day' => 'Dia'),
                'years' => range(date('Y')-100, date('Y')-18), 'label' => '* Fecha Nacimiento:',
                'label_attr' => array('class' => ''), 'attr' => array('class' => 'campo-obligatorio', 'onChange' => ''))) 
            ->add('representanteLegal', 'text', array('required' => false, 'label' => 'Representante Legal:',
                'label_attr' => array('class' => ''), 'attr' => array('class' => 'campo-obligatorio')))
            ->add('tipoEmpresa', 'choice', array('attr' => array('onChange' => 'esEmpresa()'),
                'label' => 'Tipo Empresa:',
                'label_attr' => array('class' => ''),
                'choices' => array(
                    'Publica' => 'Publica',
                    'Privada' => 'Privada'
                ),
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
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('direccion', 'text', array('label' => '* Direccion:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio')))
            ->add('direccionTributaria', 'textarea', array('required' => true, 'label' => 'Direccion Tributaria:',
                'label_attr' => array('class' => 'campo-obligatorio'), 'attr' => array('class' => 'campo-obligatorio', 'maxlength' => 100, 'cols' => 26, 'rows' => 3)))
            ->add('tituloId', 'entity', array('class' => 'telconet\schemaBundle\Entity\AdmiTitulo',
                'property' => 'codigoTitulo',
                'label' => '* Titulo:',
                'label_attr' => array('class' => 'campo-obligatorio'),
                'required' => true,
                'em' => 'telconet',
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
                'required' => true,
                'empty_value' => 'Seleccione...',
                'empty_data' => null
            ))
            ->add('contribuyenteEspecial', 'choice', array(
                                                           'label'       => '* Contribuyente Especial:',
                                                           'required'    => true,
                                                           'empty_value' => 'Seleccione...',
                                                           'empty_data'  => null,
                                                           'label_attr'  => array('class' => ''),
                                                           'choices'     => array('S' => 'Si','N' => 'No')  
							                              )
		         )            
            ->add('pagaIva', 'choice', array(
                                             'mapped'      => false, 
                                             'label'       => '* Paga Iva:',
                                             'label_attr'  => array('class' => ''),
                                             'data'       => $strPagaIva,
                                             'required'    => true,
                                             'empty_value' => 'Seleccione...',
                                             'empty_data'  => null,                                        
                                             'choices'     => array('S' => 'Si','N' => 'No')
                                            )
                )	    

            ->add('esPrepago', 'choice', array(
                                               'mapped'      => false,            
                                               'label'       => '* Es Prepago:',
                                               'label_attr'  => array('class' => ''),
                                               'data'        => $strEsPrepago,
                                               'required'    => true,
                                               'empty_value' => 'Seleccione...',
                                               'empty_data'  => null,                                        
                                               'choices'     => array('S' => 'Si','N' => 'No')
                                              )
                 )	 
		
            ->add('tieneCarnetConadis', 'choice', array('attr'        => array('onChange' => 'tieneCarnetConadis()'),
                                                        'label'       => '* Tiene Carnet Conadis:',
                                                        'label_attr'  => array('class' => ''),
                                                        'data'        => $strTieneNumeroConadis,
                                                        'required'    => true,
                                                        'mapped'      => false,
                                                        'empty_value' => 'Seleccione...',
                                                        'empty_data'  => null,
                                                        'choices'     => array('S' => 'Si',
                                                                               'N' => 'No'
                                                                              )                 
                                                       )
                )              
           ->add('numeroConadis', 'text', array('label'      => 'Numero Carnet CONADIS:',
                                                'label_attr' => array('class' => ''),
                                                'required'   => false,
						                        'attr'       => array('class' => '', 'onChange' => '')
						                       )
		        );
    }

    public function getName()
    {
        return 'clientetype';
    }
}
