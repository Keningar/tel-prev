<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InfoPuntoArchivoDigitalType extends AbstractType
{
    
	private $validaFile ;
        private $validaFileDigital ;
	
	public function __construct($options) 
    {

        $this->validaFile = $options['validaFile'];
        $this->validaFileDigital = $options['validaFileDigital'];

    }
	    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

            $this->validaFileDigital = false;
            if($this->validaFileDigital){
                    $classFileDigital = 'campo-obligatorio';
                    $labelFileDigital = '* Archivo:';
            }else{
                    $classFileDigital = '';
                    $labelFileDigital = 'Archivo:';
            }            
            
            $factory = $builder->getFormFactory(); 
            
        $builder        
            ->add('fileDigital','file',array('label'=>$labelFileDigital,'required'=>'required','attr' => array('class' => 'campo-obligatorio'))) 
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoPunto'
        ));
    }

    public function getName()
    {
        return 'infopuntoarchivodigitaltype';
    }
}
