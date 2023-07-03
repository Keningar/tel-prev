<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoContratoFormaPagoEditType extends AbstractType
{
    protected $intIdPais;
    protected $intAnioVencimiento;
    /*
    * Constructor para formulario de forma de pago de contrato
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.0 25-08-2017
    * 
    * Se añade parametro de rango usado en años de vencimiento de tarjetas.
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version : 1.1 17-09-2020
    */
    public function __construct($objOptions) 
    {
        $this->intIdPais = $objOptions['intIdPais'];
        if (isset($objOptions['intAnioVencimiento']))
        {
              $this->intAnioVencimiento = $objOptions['intAnioVencimiento'];
        }
        else
        {
            $this->intAnioVencimiento = 15;
        }
            
        
    }
    
    /*
    * Funcion para construir formulario para forma de pago de contrato
    * @author : telcos
    * @version 1.0 19-06-2014
    * 
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.1 25-08-2017
    * Se consulta tipo de cuenta por id de Pais
    *
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version 1.2 17-09-2020
    * Se modifica el anio de vencimiento a mostrar en el HTML
    * para las pantallas (ContratoEdit, ContratoNew).
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intPaisId = $this->intIdPais;
        $arrayAnios = range(date('Y'), date('Y') + $this->intAnioVencimiento );
        
        
        
        $builder->add('tipoCuentaId',
                      'entity',
                      array(
                                'class'         => 'telconet\schemaBundle\Entity\AdmiTipoCuenta',
                                'label'         => '* Tipo de Cuenta : ',
                                'label_attr'    => array('class' => 'campo-obligatorio'),
                                'property'      => 'descripcionCuenta',
                                'attr'          => array('class' => 'campo-obligatorio-select'),
                                'em'            => 'telconet',
                                'required'      => false, 
                                'empty_value'   => 'Seleccione',
                                'query_builder' => function (EntityRepository $objEntityRepository) use ($intPaisId)
                                {
                                    $objQueryBuilder = $objEntityRepository->createQueryBuilder("t")
                                                                           ->select("a")
                                                                           ->from('schemaBundle:AdmiTipoCuenta a','')
                                                                           ->where("a.estado = :strEstado")
                                                                           ->andWhere("a.paisId = :intIdPais");
                                    $objQueryBuilder->setParameter("strEstado",'Activo');
                                    $objQueryBuilder->setParameter("intIdPais",$intPaisId);
                                    return $objQueryBuilder;

                                },
                           )
                     )
            ->add('bancoTipoCuentaId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiBancoTipoCuenta',
                                                                                           'label'=>'* Banco : ',
											   'label_attr' => array('class' => 'campo-obligatorio'),
                                                                                           'property'=>'bancoId',
											   'attr' => array('class' => 'campo-obligatorio-select'),
											   'em'=> 'telconet',
                                                                                           'required' => false, 
                                                                                           'empty_value' => 'Seleccione',
            ))												   
            ->add('numeroCtaTarjeta','text',array('required'=>false,'label'=>'* N° Tarjeta/Cuenta:',
            'label_attr' => array('class' => 'campo-obligatorio'),
            'attr' => array('maxLength'=>16, 'class' => 'campo-obligatorio'))) 			
            ->add('titularCuenta','text',array('label'=>'* Titular:','required' => false,
			'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>90, 'class' => 'campo-obligatorio')))                                                                                                                    
            ->add('anioVencimiento', 'choice', array('required'=>false,'choices' => array_combine($arrayAnios, $arrayAnios),
                'empty_value' => 'Seleccione...',
                'empty_data' => null))                                                                                                 
            ->add('mesVencimiento', 'choice',array('required'=>false,'label'=>'Mes Vencimiento:',
            'label_attr' => array('class' => ''),
            'choices' => array(
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
                ),
                'empty_value' => 'Seleccione...',
                'empty_data' => null))                                                                                               
            ->add('codigoVerificacion','text',array('required'=>false,'label'=>'Cod. Verificacion:',
            'label_attr' => array('class' => ''),'attr' => array('maxLength'=>4, 'class' => '')))                                                                                                      
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoContratoFormaPago'
        ));
    }

    public function getName()
    {
        return 'infocontratoformapagotype';
    }
}
