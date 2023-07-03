<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class PreClienteType extends AbstractType
{

    private $titulo;
    private $empresaId;
    private $tieneCarnetConadis;
    private $tipoIdentificacion;
    private $identificacionCliente;
    private $nacionalidad;
    private $direccionTributaria;
    private $origenIngresos;
    private $contribuyenteEspecial;
    private $pagaIva;
    private $esPrepago;
    private $numeroConadis;
    private $oficinaFacturacion;
    private $tipoEmpresa;
    private $tipoTributario;
    private $razonSocial;
    private $representanteLegal;
    private $fechaInicioCompania; 
    private $estadoLegal; 
    private $dataRecomendacion;
    private $genero;
    private $nombres;
    private $apellidos;
    private $estadoCivil;
    private $fechaNacimiento;
    private $formas_contacto;
    private $referido;
    private $idReferido;
    private $idPerreferido;
    private $yaExiste;
    private $id;
    private $origen_web;
    private $arrayEsPrepago;
    private $strPrefijoEmpresa;
    private $holding;
    private $vendedores;
    private $strEsDistribuidor;
    private $rigthStyle;
    private $leftStyle;
    
    public function __construct($options) 
    {
        $this->empresaId          = $options['empresaId'];
        $this->oficinaFacturacion = $options['oficinaFacturacion'];
        $this->tieneNumeroConadis = $options['tieneNumeroConadis'];  
        $this->pagaIva            = $options['pagaIva'];   
        $this->strEsDistribuidor  = $options['es_distribuidor'];
        
        
        if(isset($options['empresaId']))
        {
            $this->empresaId          = $options['empresaId'];
        }
        
        if(isset($options['prefijoEmpresa']))
        {
            $this->strPrefijoEmpresa = $options['prefijoEmpresa'];
        }
        
        if(isset($options['oficinaFacturacion']))
        {
            $this->oficinaFacturacion = $options['oficinaFacturacion'];
        }
        
        if(isset($options['titulo']))
        {
            $this->titulo = $options['titulo'];
        }
        
        if(isset($options['tieneCarnetConadis']))
        {
            $this->tieneCarnetConadis = $options['tieneCarnetConadis'];
        }
        
        if(isset($options['esPrepago']))
        {
            $this->esPrepago          = $options['esPrepago'];
        }
        
        $this->arrayEsPrepago = array('S' => 'Si', 'N' => 'No');
        
        if(isset($options['prefijoEmpresa']) && $options['prefijoEmpresa'] == 'TN')
        {
            $this->arrayEsPrepago = array('S' => 'Si');
        }
        
        if(isset($options['vendedores']))
        {
            $this->vendedores          = $options['vendedores'];
        }
        
        if(isset($options['datos']))
        {
            $this->tipoIdentificacion    = $options['datos']['tipoIdentificacion'];
            $this->identificacionCliente = $options['datos']['identificacionCliente'];
            $this->nacionalidad          = $options['datos']['nacionalidad'];
            $this->direccionTributaria   = $options['datos']['direccionTributaria'];
            $this->origenIngresos        = $options['datos']['origenIngresos'];
            $this->contribuyenteEspecial = $options['datos']['contribuyenteEspecial'];
            $this->pagaIva               = $options['datos']['pagaIva'];
            $this->esPrepago             = $options['datos']['esPrepago'];
            $this->tieneCarnetConadis    = $options['datos']['tieneCarnetConadis'];
            $this->numeroConadis         = $options['datos']['numeroConadis'];
            $this->idOficinaFacturacion  = $options['datos']['idOficinaFacturacion'];
            $this->tipoEmpresa           = $options['datos']['tipoEmpresa'];
            $this->tipoTributario        = $options['datos']['tipoTributario'];
            $this->razonSocial           = $options['datos']['razonSocial'];
            $this->representanteLegal    = $options['datos']['representanteLegal'];
            $this->fechaInicioCompania   = $options['datos']['fechaInicioCompania'];
            $this->estadoLegal           = $options['datos']['estadoLegal'];
            $this->dataRecomendacion     = $options['datos']['dataRecomendacion'];
            $this->genero                = $options['datos']['genero'];
            $this->nombres               = $options['datos']['nombres'];
            $this->apellidos             = $options['datos']['apellidos'];
            $this->estadoCivil           = $options['datos']['estadoCivil'];
            $this->fecha                 = $options['datos']['fechaNacimiento'];
            $this->formas_contacto       = $options['datos']['formas_contacto'];
            $this->referido              = $options['datos']['referido'];
            $this->idReferido            = $options['datos']['idreferido'];
            $this->idPerreferido         = $options['datos']['idperreferido'];
            $this->yaExiste              = $options['datos']['yaexiste'];
            $this->id                    = $options['datos']['id'];
            $this->origen_web            = $options['datos']['origen_web'];
            $this->holding               = $options['datos']['holding'];
            $this->vendedores            = $options['datos']['vendedores'];
            $this->strEsDistribuidor     = $options['datos']['es_distribuidor'];
            
            if($this->fecha['year'])
            {
                $datFechaNacimiento = new \DateTime();
                $datFechaNacimiento->setDate($this->fecha['year'], $this->fecha['month'], $this->fecha['day']);
                $this->fechaNacimiento = $datFechaNacimiento;
            }
        }
        $this->rigthStyle = 'margin-left: 8px; width:127px;';
        $this->leftStyle  = 'margin-left: 20px; width:155px;';
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
     * @version 1.1 27-07-2016  
     * Se esta usando en el Form style => text-transform: uppercase; actualmente muestra texto que escribe el usuario en mayuscula,
     * pero existe el error ya que no esta enviando el texto realmente a la base en mayuscula, y se tiene data de clientes en minuscula 
     * Se corrige con el evento onkeyup  => javascript:this.value=this.value.toUpperCase() que se encarga de convertir realmente a mayuscula 
     * el texto.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 19-02-2019 Se agrega tipo de identificación para empresa Telconet Guatemala.  
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.3 29-10-2020 Se agrega el campo Holding para clientes Tn.  
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 25-05-2021 - Se agrega el campo 'Distribuidor' para clientes Tn.
     *
     * @author Kenth Encalada <kencalada@telconet.ec>
     * @version 1.5 23-06-2023 - Se agrego una funcion onChange para limpiar los caracteres especiales de los campos.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $strTieneNumeroConadis = $this->tieneNumeroConadis;
        $strPagaIva            = $this->pagaIva ;
        
        if( $this->strPrefijoEmpresa === 'TNG')
        {
            $arrayTiposIdentificacion = array('NIT' => 'NIT','DPI' => 'DPI','PAS' => 'Pasaporte');
        }
        else
        {
            $arrayTiposIdentificacion = array('CED' => 'Cedula','RUC' => 'Ruc','PAS' => 'Pasaporte');
        }
        
        $builder
            ->add('idOficinaFacturacion','entity',array('class'         => 'telconet\schemaBundle\Entity\InfoOficinaGrupo',
                                                        'mapped'        => false,
                                                        'property'      => 'nombreOficina',
                                                        'attr'          => array('style' => 'margin-left: 5px;'),
                                                        'label'         => '* Oficina Facturación:',
                                                        'label_attr'    => array('class' => 'campo-obligatorio'),
                                                        'required'      => true,
                                                        'em'            => 'telconet',
                                                        'empty_value'   => 'Seleccione...',
                                                        'data'          => $this->oficinaFacturacion,
                                                        'empty_data'    => null,             
                                                        'query_builder' => function (EntityRepository $er) 
                                                                          {
                                                                              return $er->createQueryBuilder('info_oficina')
                                                                                        ->select('oficina')
                                                                                        ->from('telconet\schemaBundle\Entity\InfoOficinaGrupo',
                                                                                               'oficina')
                                                                                        ->where("oficina.estado = ?1")
                                                                                        ->andWhere("oficina.esOficinaFacturacion = ?2")
                                                                                        ->andWhere("oficina.empresaId = ?3")
                                                                                        ->setParameter(1, 'Activo')
                                                                                        ->setParameter(2, 'S')
                                                                                        ->setParameter(3, $this->empresaId);
                                                                          }
                 ))            
            ->add('tipoIdentificacion', 'choice', array('attr'       => array('onChange' => 'validarIdentificacionTipo();',
                                                                              'style'    => 'width: 110px;'),
                                                        'label'      => '* Tipo Identificación:',
                                                        'label_attr' => array('style' => $this->leftStyle),
                                                        'choices'    => $arrayTiposIdentificacion,
                                                        'required' => true,
                                                        'empty_value' => 'Seleccione...',
                                                        'empty_data' => null,
                                                        'data'       => $this->tipoIdentificacion
            ))
            ->add('identificacionCliente', 'text', array('required'   => true, 
                                                         'label'      => '* Identificación:',
                                                         'label_attr' => array('class' => 'campo-obligatorio',  
                                                                               'style' => 'margin-left: 20px; width:152px;'),
                                                         'attr'       => array('maxLength' => 13, 
                                                                               'class'     => 'campo-obligatorio', 
                                                                               'style'     => 'width: 250px;  text-transform: uppercase;', 
                                                                               'onkeyup'   => 'javascript:this.value=this.value.toUpperCase();',
                                                                               'onChange'  => 'validaIdentificacion(true);',
                                                         'data'       => $this->identificacionCliente))
                )
            ->add('tipoTributario', 'choice', array('label'       => '* Tipo Tributario:',
                                                    'label_attr'  => array('style' => $this->leftStyle),
                                                    'attr'        => array('onChange' => 'esTipoNatural();',
                                                                           'style'    => '65px'),
                                                    'choices'     => array('NAT' => 'Natural',
                                                                           'JUR' => 'Juridico'),
                                                    'required'    => true,
                                                    'empty_value' => 'Seleccione...',
                                                    'empty_data'  => null,
                                                    'data'        => $this->tipoTributario)
                )
            ->add('tipoEmpresa', 'choice', array('attr'       => array('style'    => 'width: 110px;'),
                                                 'label'      => 'Tipo Empresa:',
                                                 'label_attr' => array('style' => 'margin-left: 28px; width:147px;'),
                                                 'choices'    => array('Publica' => 'Publica',
                                                                    'Privada' => 'Privada'),
                                                'required'    => false,
                                                'empty_value' => 'Seleccione...',
                                                'empty_data'  => null,
                                                'data'        => $this->tipoEmpresa)
                )
            ->add('nombres', 'text', array('label'      => '* Nombres:',
                                           'label_attr' => array('class'   => 'campo-obligatorio'), 
                                           'attr'       => array('class'   => 'campo-obligatorio',
                                                                 'style'   => ' text-transform: uppercase;',
                                                                 'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'),
                                           'data'       => $this->nombres)
                )
            ->add('apellidos', 'text', array('label'      => '* Apellidos:',
                                             'label_attr' => array('class'   => 'campo-obligatorio'),
                                             'attr'       => array('class'   => 'campo-obligatorio',
                                                                   'style'   => ' text-transform: uppercase;',
                                                                   'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'),
                                             'data'       => $this->apellidos)
                )
            ->add('razonSocial', 'text', array('required'   => false, 
                                               'label'      => '* Razón Social:',
                                               'label_attr' => array('class'   => 'campo-obligatorio'), 
                                               'attr'       => array('class'   => 'campo-obligatorio', 
                                                                     'style'   => ' text-transform: uppercase;',
                                                                     'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'),
                                               'data'       => $this->razonSocial)
                )
            ->add('genero', 'choice', array('label'       => '* Género:',
                                            'attr'        => array('style'    => 'width: 110px;'),
                                            'label_attr'  => array('class' => 'campo-obligatorio',
                                                                   'style' => 'width: 135px'),
                                            'choices'     => array('M' => 'Masculino',
                                                                   'F' => 'Femenino',
                                                                   'O'=> 'Otro'),
                                            'required'    => true,
                                            'empty_value' => 'Seleccione...',
                                            'empty_data'  => null,
                                            'data'        => $this->genero)
                )
            //cambios DINARDARP - se coloca como obligatorio el estado civil 
            ->add('estadoCivil', 'choice', array('label'       => 'Estado Civil:',
                                                 'attr'        => array('style'    => 'width: 110px;'),
                                                 'label_attr'  => array('class' => 'campo-obligatorio',
                                                                        'style' => $this->rigthStyle),
                                                 'choices'     => array('S' => 'Soltero(a)',
                                                                        'C' => 'Casado(a)',
                                                                        'U' => 'Union Libre',
                                                                        'D' => 'Divorciado(a)',
                                                                        'V' => 'Viudo(a)'),
                                                 'required'    => false,
                                                 'empty_value' => 'Seleccione...',
                                                 'empty_data'  => null,
                                                 'data'        => $this->estadoCivil)
                )
            ->add('fechaNacimiento', 'date', array('required'    => false, 
                                                   'empty_value' => array('year' => 'Año', 'month' => 'Mes', 'day' => 'Dia'),
                                                   'years'       => range(date('Y')-100, date('Y')-18), 
                                                   'label'       => 'Fecha Nacimiento:',
                                                   'label_attr'  => array('for'   => 'preclientetype_fechaNacimiento',
                                                                          'style' => $this->rigthStyle),
                                                   'data'        => $this->fechaNacimiento)
                )
            ->add('representanteLegal', 'text', array('required'   => false, 
                                                      'label'      => '* Representante Legal:',
                                                      'label_attr' => array('style'    => 'font-size:11px;','class'   => ''), 
                                                      'attr'       => array('class'   => 'campo-obligatorio',
                                                                            'style'   => ' text-transform: uppercase;',
                                                                            'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'),
                                                      'data'       => $this->representanteLegal)
                )
            ->add('fechaInicioCompania', 'text', array('label'       => 'Fecha Compania:',
            
                'attr'        => array('readonly'    => true),
                'label_attr'  => array('style'    => 'font-size:11px;','class'   => 'campo-obligatorio'),  
                'required'    => false,
                'mapped'      => false,                
                'data'        => $this->fechaInicioCompania)
                ) 
     
             ->add('estadoLegal', 'text', array('label'       => 'Estado Legal:',
                'attr'        => array('readonly'    => true),
                'label_attr'  => array('class'   => 'campo-obligatorio'),  
                'required'    => false,
                'mapped'      => false,                
                'data'        => $this->estadoLegal)
                ) 

            ->add('dataRecomendacion', 'text', array('label'       => 'Data recomendada:',
                'attr'        => array('readonly'    => true),
                'label_attr'  => array('class'   => 'campo-obligatorio'),  
                'required'    => false,
                'mapped'      => false,                
                'data'        => $this->dataRecomendacion)
                ) 
            ->add('nacionalidad', 'choice', array('label'       => '* Nacionalidad:',
                                                  'attr'        => array('style'    => 'width: 110px;'),
                                                  'label_attr'  => array('style' => $this->rigthStyle),
                                                  'choices'     => array('NAC' => 'Nacional',
                                                                         'EXT' => 'Extranjera'),
                                                  'required'    => false,
                                                  'empty_value' => 'Seleccione...',
                                                  'empty_data'  => null,
                                                  'data'        => $this->nacionalidad)
                )
            ->add('direccionTributaria', 'textarea', array( 'required'   => true, 
                                                            'label'      => '* Dirección Tributaria:',
                                                            'label_attr' => array('class'     => 'campo-obligatorio', 
                                                                                  'style'     => $this->leftStyle . 'resize:none;'),
                                                            'attr'       => array('class'     => 'campo-obligatorio', 
                                                                                  'style'     => 'resize: vertical; max-height: 75px;' .
                                                                                                 'min-height: 38px; width: 290px;'.
                                                                                                 'text-transform: uppercase;',
                                                                                  'onkeyup'   => 'javascript:this.value=this.value.toUpperCase();',
                                                                                  'maxlength' => 100, 
                                                                                  'cols'      => 26, 
                                                                                  'rows'      => 3,
                                                                                  'onChange'  => 
                                                                                  'validarCaracterEspecial(
                                                                                    "preclientetype_direccionTributaria",
                                                                                    "Dirección Tributaria");'),
                                                            'data'       => $this->direccionTributaria)
                )
            ->add('tituloId', 'entity', array('class'       => 'telconet\schemaBundle\Entity\AdmiTitulo',
                                              'attr'        => array('style'    => 'width: 110px;'),
                                              'label_attr'  => array('class' => 'campo-obligatorio',
                                                                     'style' => 'width: 135px'),
                                              'property'    => 'codigoTitulo',
                                              'label'       => '* Título:',
                                              'required'    => true,
                                              'em'          => 'telconet',
                                              'empty_value' => 'Seleccione...',
                                              'empty_data'  => null,
                                              'data'        => $this->titulo)
                )
            //cambios DINARDARP - se agrega combo de origenes de ingresos 
            ->add('origenIngresos', 'choice', array('label'       => '* Origen de Ingresos:',
                                                    'attr'        => array('style' => 'width: 250px',), 
                                                    'label_attr'  => array('class' => 'campo-obligatorio', 
                                                                           'style' => $this->leftStyle),
                                                    'choices'     => array('B' => 'Empleado Público',
                                                                           'V' => 'Empleado Privado',
                                                                           'I' => 'Independiente',
                                                                           'A' => 'Ama de casa o estudiante',
                                                                           'R' => 'Rentista',
                                                                           'J' => 'Jubilado',
                                                                           'M' => 'Remesas del exterior'),
                                                    'required'    => false,
                                                    'empty_value' => 'Seleccione...',
                                                    'empty_data'  => null,
                                                    'data'        => $this->origenIngresos)
                )
            ->add('contribuyenteEspecial', 'choice', array('label'       => '* Contribuyente Especial:',
                                                           'required'    => true,
                                                           'empty_value' => 'Seleccione...',
                                                           'empty_data'  => null,
                                                           'label_attr'  => array('style' => $this->leftStyle),
                                                           'attr'        => array('style' => '65px'),
                                                           'choices'     => array('S' => 'Si',
                                                                                  'N' => 'No'),
                                                           'data'        => $this->contribuyenteEspecial)
		         )
            ->add('pagaIva', 'choice', array('label'       => '* Paga Iva:',
                                             'label_attr'  => array('style' => $this->leftStyle),
                                             'attr'        => array('style' => '65px'),
                                             'data'        => $strPagaIva,
                                             'required'    => true,
                                             'empty_value' => 'Seleccione...',
                                             'empty_data'  => null,                                        
                                             'choices'     => array('S' => 'Si',
                                                                    'N' => 'No'),
                                             'data'        => $this->pagaIva)
                )	    
            ->add('esPrepago', 'choice', array('mapped'      => false,            
                                               'label'       => '* Es Prepago:',
                                               'label_attr'  => array('style' => $this->leftStyle),
                                               'attr'        => array('style' => '65px'),
                                               'data'        => $this->esPrepago,
                                               'required'    => true,
                                               'empty_value' => 'Seleccione...',
                                               'empty_data'  => null,                                        
                                               'choices'     => $this->arrayEsPrepago,
                                               'data'        => $this->esPrepago)
                 )
            ->add('tieneCarnetConadis', 'choice', array('label'       => '* Tiene Carnet Conadis:',
                                                        'label_attr'  => array('style' => $this->leftStyle),
                                                        'attr'        => array('onChange' => 'tieneCarnetConadis();',
                                                                               'style' => '65px'),
                                                        'required'    => true,
                                                        'mapped'      => false,
                                                        'empty_value' => 'Seleccione...',
                                                        'empty_data'  => null,
                                                        'choices'     => array('S' => 'Si',
                                                                               'N' => 'No'),
                                                        'data'        => $this->tieneCarnetConadis)
                )
           ->add('numeroConadis', 'text', array('label'      => 'Número Carnet CONADIS:',
                                                'label_attr' => array('style' => $this->leftStyle),
                                                'required'   => false,
                                                'attr'       => array('style'   => 'width: 245px; text-transform: uppercase;',
                                                                      'onkeyup' => 'javascript:this.value=this.value.toUpperCase();'),
                                                'data'       => $this->numeroConadis)
		        )
           ->add('holding','entity',array('class'      => 'telconet\schemaBundle\Entity\AdmiParametroDet',
                                                     'mapped'        => false,
                                                     'property'      => 'valor1',
                                                     'attr'          => array('style' => 'margin-left: 5px;'),
                                                     'label'         => ' Holding:',
                                                     'required'      => false,
                                                     'em'            => 'telconet',
                                                     'empty_value'   => 'Seleccione...',
                                                     'data'          => $this->holding,
                                                     'empty_data'    => null,             
                                                     'query_builder' => function (EntityRepository $objEntity) 
                                                                       {
                                                                           return $objEntity->createQueryBuilder('admi_parametro_det')
                                                                             ->select('parametroDet')
                                                                             ->from('telconet\schemaBundle\Entity\AdmiParametroCab','parametroCab')
                                                                             ->from('telconet\schemaBundle\Entity\AdmiParametroDet','parametroDet')
                                                                             ->where("parametroCab.nombreParametro = 'HOLDING DE EMPRESAS'")
                                                                             ->andWhere("parametroCab.id = parametroDet.parametroId")
                                                                             ->andWhere("parametroDet.estado ='Activo'")  
                                                                             ->andWhere("parametroDet.valor3 In (?1)") 
                                                                             ->orderBy('parametroDet.valor1', 'ASC')
                                                                             ->setParameter(1, $this->vendedores);
                                                                       }
                 )) 
           ->add('es_distribuidor', 'choice', array('label'       => '* Es Distribuidor:',
                                                    'attr'        => array('style'    => 'width: 110px;'),
                                                    'label_attr' => array('class'   => 'campo-obligatorio'), 
                                                    'required'    => false,
                                                    'mapped'      => false,
                                                    'empty_value' => 'Seleccione...',
                                                    'empty_data'  => null,
                                                    'choices'     => array('NO' => 'No',
                                                                           'SI' => 'Si'),
                                                    'data'        => $this->strEsDistribuidor)
                );
    }

    public function getName()
    {
        return 'preclientetype';
    }
}
