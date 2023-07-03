<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoRecaudacionType extends AbstractType
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
                    $labelFile = '* Respuesta Recaudacion:';
            }else{
                    $classFile = '';
                    $labelFile = 'Respuesta Recaudacion:';
            }

            $factory = $builder->getFormFactory(); 	
	
        $builder
            ->add('file','file',array('label'=>$labelFile,'required'=>$this->validaFile,'label_attr' => array('class' => $classFile),'attr' => array('class' => $classFile))) 			
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoRecaudacion'
        ));
    }

    public function getName()
    {
        return 'inforecaudaciontype';
    }
}
