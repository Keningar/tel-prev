<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoPagoCabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('empresaId')
            //->add('puntoId')
            //->add('oficinaId')
            //->add('personaId')
            ->add('numeroPago','text',array('required'=>true,'label'=>'* Numero:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('maxLength'=>10, 'class' => 'campo-obligatorio')))
            ->add('valorTotal','text',array('required'=>true,'label'=>'* Total:',
            'label_attr' => array('class' => 'campo-obligatorio'),'attr' => array('readonly'=>true,'maxLength'=>10, 'class' => 'campo-obligatorio')))                
            //->add('feEliminacion')
            //->add('estadoPago')
            ->add('comentarioPago','textarea',array('required'=>false,'label'=>'Comentarios:',
            'label_attr' => array('class' => ''),'attr' => array( 'maxlength'=>150,'cols'=>26, 'rows'=>3))) 
            //->add('feCreacion')
            //->add('feUltMod')
            //->add('usrCreacion')
            //->add('usrUltMod')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoPagoCab'
        ));
    }

    public function getName()
    {
        return 'infopagocabtype';
    }
}
