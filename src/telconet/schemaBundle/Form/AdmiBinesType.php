<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdmiBinesType extends AbstractType
{
    private $intIdPais;
    
    /*
    * Constructor para formulario de forma de pago de bines
    * @author : telcos
    * @version 1.0 19-01-2014
    *
    * Actualización: Se asigna a la variable intIdPais el id de pais que se envia por parametro
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
     * 
    */
    public function __construct($objOptions) 
    {
        $this->intIdPais = $objOptions['intIdPais'];
    }
    
    /*
    * Funcion para construir formulario para forma de pago de bines
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
        $builder
        ->add('bin_nuevo', 'text', array('label'=>'* BIN Nuevo',
                                         'required'=>true,
                                         'label_attr' => array('class' => 'campo-obligatorio'), 
                                         'attr' => array('class' => 'campo-obligatorio','maxLength'=>6, 'onChange'=>"validarBin(this);") ))
            
        ->add('descripcion', 'text', array('label' => '* Descripción',
                                           'required' => true,
                                           'label_attr' => array('class' => 'campo-obligatorio'), 
                                           'attr' => array('class' => 'campo-obligatorio', 'onChange' => '') ))
            
        ->add('tarjeta', 'entity', array('class' => 'telconet\\schemaBundle\\Entity\\AdmiTipoCuenta',
                                         'query_builder' => function ($objEntityRepository) use ($intPaisId)
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
                                        'label' => '* Tipo de Cuenta',
                                        'property' => 'descripcionCuenta',
                                        'attr' => array('class' => 'campo-obligatorio-select', 
                                                                 'validationMessage' => "Proceso es requerido", 
                                                                 'onChange' => "getBancos();"),
                                        'empty_value' => 'Escoja una opción'))
                                             
            ->add('banco', 'choice', array('label'=>'* Banco : ',
                                           'label_attr' => array('class' => ' '),             
                                           'required'    => false,
                                           'empty_value' => 'Seleccione...',
                                           'empty_data'  => null))
                                             
            ->add('motivo_id', 'choice', array('label'=>'* Motivo : ',
                                               'label_attr' => array('class' => ''),         
                                               'attr' => array('class' => 'campo-obligatorio', 'onCreate'=>"getMotivos();"),
                                               'required'    => true,
                                               'empty_value' => 'Seleccione...',
                                               'empty_data'  => null))
                                             
            ->add('motivo_descripcion', 'textarea', array('label' => '* Descripción',
                                                          'required' => true,
                                                          'label_attr' => array('class' => 'campo-obligatorio'), 
                                                          'attr' => array('class' => 'campo-obligatorio', 'size'=>500 , 'maxLength' => 200 , 
                                                                          'enforceMaxLength'=>true, 'rows' => 3, 'cols'=>70) ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\AdmiBines'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_admibinestype';
    }
}