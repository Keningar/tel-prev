<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoPersonaEmpFormaPagoEditType extends AbstractType
{
    private $intIdPais;
    
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
    * @version 1.2 22-12-2022 - Se agrega arrayFormasPago para la consulta de formas de pago. 
    */
    public function __construct($objOptions) 
    {
        $this->intIdPais = $objOptions['intIdPais'];
        $this->arrayFormasPago = $objOptions['arrayFormasPago']; 
    }

    /*
    * Funcion para construir formulario para forma de pago de persona empresa rol
    * @author : telcos
    * @version 1.0 19-06-2014
    *
    * Actualización: se consulta tipo de cuenta por id de Pais 
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    *
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intPaisId = $this->intIdPais;
        $arrayFormaPago = $this->arrayFormasPago;
        $builder
            ->add('formaPagoId','entity',array('class'         => 'telconet\schemaBundle\Entity\AdmiFormaPago',
                                               'property'      => 'descripcionFormaPago',
                                               'label_attr'    => array('class' => 'campo-obligatorio'),
                                               'label'         => ' Forma de pago:',
                                               'attr'          => array('class'    => 'campo-obligatorio-select',
                                                                        'onChange' => "validaServiciosXFormaPago()"
                                                                       ),
                                               'required'      => true,
                                               'em'            => 'telconet',
                                               'empty_value'   => 'Seleccione',
                                               'query_builder' => function (EntityRepository $objEntityRepository) use ($arrayFormaPago)
                                                                  {
                                                                      return $objEntityRepository->findFormasPagoXEstado('Activo',$arrayFormaPago);
                                                                  }
                                                 ))
            ->add('tipoCuentaId','entity',array('class'         => 'telconet\schemaBundle\Entity\AdmiTipoCuenta',
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
                                                                                                              ->from('schemaBundle:AdmiTipoCuenta a',
                                                                                                                     '')
                                                                                                              ->where("a.estado = :strEstado")
                                                                                                              ->andWhere("a.paisId = :intIdPais");
                                                                       $objQueryBuilder->setParameter("strEstado",'Activo');
                                                                       $objQueryBuilder->setParameter("intIdPais",$intPaisId);
                                                                       return $objQueryBuilder;
                                                                   },
                                               )
                 )
           ->add('bancoTipoCuentaId','entity',array('class'       =>'telconet\schemaBundle\Entity\AdmiBancoTipoCuenta',
                                                    'label'       =>'* Banco : ',
                                                    'label_attr'  => array('class' => 'campo-obligatorio'),
                                                    'property'    =>'bancoId',
                                                    'attr'        => array('class'    => 'campo-obligatorio-select',
                                                                           'onChange' => "validaServiciosXFormaPago()"
                                                                          ),
                                                    'em'          => 'telconet',
                                                    'required'    => false,
                                                    'empty_value' => 'Seleccione',
                                                   )
                );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago'
        ));
    }

    public function getName()
    {
        return 'infopersonaempformapagotype';
    }
   
}
