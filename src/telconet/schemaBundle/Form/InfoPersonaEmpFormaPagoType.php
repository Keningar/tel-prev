<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoPersonaEmpFormaPagoType extends AbstractType
{
    private $formaPagoId;
    private $tipoCuentaId;
    private $intIdPais;
    private $arrayFormasPago;
    
    /*
    * Constructor para formulario de forma de pago de persona empresa rol
    * @author : telcos
    * @version 1.0 19-01-2014
    *
    * Actualización: Se asigna a la variable intIdPais el id de pais que se envia por parametro
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.2 25-11-2021 - Se agrega y asigna arrayFormasPago para la consulta de formas de pago. 
    */
    public function __construct($options) 
    {
        $this->intIdPais = $options['intIdPais'];
        $this->arrayFormasPago = $options['arrayFormasPago'];
        if(isset($options['datos']))
        {
            if(isset($options['datos']['entityFormaPago']))
            {
                $this->formaPagoId = $options['datos']['entityFormaPago'];
            }
            
            if(isset($options['datos']['entityTipoCuenta']))
            {
                $this->tipoCuentaId = $options['datos']['entityTipoCuenta'];
            }
        }
    }

    /*
    * Funcion para construir formulario para forma de pago de persona empresa rol
    * @author : telcos
    * @version 1.0 19-06-2014
    * 
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    * Se consulta tipo de cuenta por id de Pais
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.2 25-11-2021 - Se agrega arrayFormasPago para la consulta de formas de pago.
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intPaisId = $this->intIdPais;
        $arrayFormaPago = $this->arrayFormasPago;
        $builder
            ->add('formaPagoId', 'entity', array('class'         => 'telconet\schemaBundle\Entity\AdmiFormaPago',
                                                 'property'      => 'descripcionFormaPago',
                                                 'label_attr'    => array('class' => 'campo-obligatorio'),
                                                 'label'         => '* Forma de pago:',
                                                 'required'      => true,
                                                 'em'            => 'telconet',
                                                 'empty_value'   => 'Seleccione...',
                                                 'empty_data'    => null ,  
                                                 'data'          => $this->formaPagoId,
                                                 'query_builder' => function (EntityRepository $objEntityRep) use ($arrayFormaPago)
                                                                    {
                                                                        return $objEntityRep->findFormasPagoXEstado('Activo',$arrayFormaPago);
                                                                    }
                                                ))
            ->add('tipoCuentaId', 'entity', array('class'         => 'telconet\schemaBundle\Entity\AdmiTipoCuenta',
                                                  'label'         => '* Tipo de Cuenta : ',
                                                  'label_attr'    => array('class' => 'campo-obligatorio'),
                                                  'property'      => 'descripcionCuenta',
                                                  'attr'          => array('class' => 'campo-obligatorio-select'),
                                                  'data'          => $this->tipoCuentaId,
                                                  'em'            => 'telconet',
                                                  'required'      => false,
                                                  'empty_value'   => 'Seleccione',
                                                  'query_builder' => function (EntityRepository $objEntityRepository) use ($intPaisId)
                                                                     {
                                                                        $objQueryBuilder = $objEntityRepository->createQueryBuilder("t")
                                                                                                               ->select("a")
                                                                                                               ->from('schemaBundle:AdmiTipoCuenta a',
                                                                                                                      '')
                                                                                                               ->where("a.estado = :strEstado")
                                                                                                               ->andWhere("a.paisId = :intIdPais");
                                                                        $objQueryBuilder->setParameter("strEstado",'Activo');
                                                                        $objQueryBuilder->setParameter("intIdPais",$intPaisId);
                                                                        return $objQueryBuilder;
                                                                     },
                                                ))
            ->add('bancoTipoCuentaId', 'choice', array('label'       =>'Banco : ',
                                                       'label_attr'  => array('class' => ' '),             
                                                       'required'    => false,
                                                       'empty_value' => 'Seleccione...',
                                                       'empty_data'  => null
                                                        ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago'));
    }

    public function getName()
    {
        return 'infopersonaempformapagotype';
    }
}
