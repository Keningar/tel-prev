<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoContratoType extends AbstractType
{

	private $validaFile ;
	
	public function __construct($options) 
    {

        $this->validaFile = $options['validaFile'];

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $this->validaFile = true;
            if($this->validaFile){
                    $classFile = 'campo-obligatorio';
                    $labelFile = '* Archivo Digital:';
            }else{
                    $classFile = '';
                    $labelFile = 'Archivo Digital:';
            }

            $factory = $builder->getFormFactory(); 	
	
        $builder
            ->add('formaPagoId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiFormaPago',
											   'property'=>'descripcionFormaPago',
											   'label_attr' => array('class' => 'campo-obligatorio'),
											   'label'=>'* Forma de pago:',
											   'required' => true,
											   'em'=> 'telconet',
											   'empty_value' => 'Seleccione',
											   'query_builder' => function (EntityRepository $er) {
														return $er->findFormasPagoXEstado('Activo');
												   }
												))
            ->add('numeroContratoEmpPub','text',array('label'=>'N°. contrato emp. publica:','required' => false))
            //->add('valorContrato','text',array('label'=>'* Valor:', 'label_attr' => array('class' => 'campo-obligatorio')))
            ->add('valorAnticipo','text',array('label'=>'Anticipo:','required' => false))
           //->add('valorGarantia','text',array('label'=>'Garantía:','required' => false))           
           ->add('file','file',array('label'=>$labelFile,'required'=>$this->validaFile,'attr' => array('class' => $classFile)))                                                                                                                    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoContrato'
        ));
    }

    public function getName()
    {
        return 'infocontratotype';
    }
}
