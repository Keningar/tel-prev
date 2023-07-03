<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SistItemMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder
            ->add('nombreItemMenu','text', array('label' => '* Nombre','attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Nombre del item menu es requerido",'maxlength'=>50)))
            ->add('titleHtml','text', array('label' => '* Nombre Html','attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Nombre HTML del item menu es requerido",'maxlength'=>50)))
            ->add('descripcionHtml','text', array('required'=>false,'label' => 'Desc. HTML del Item Menu'))
			->add('Html','text', array('required'=>false,'label' => 'Html'))
            ->add('urlImagen','text', array('required'=>false,'label' => 'URL imagen'))
            ->add('descripcionItemMenu','text', array('required'=>false,'label' => 'Desc. del Item Menu'))
            ->add('posicion','integer', array('required'=>false,'label' => 'Posicion'))
            ->add('itemMenuId', 'entity', array(
            'em'            => 'telconet_seguridad',
            'class'         => 'telconet\\schemaBundle\\Entity\\SistItemMenu',
            'query_builder' => function ($repositorio) {
                               return $repositorio->createQueryBuilder('p')->where("p.estado not like 'ELIMINADO'")->orderBy('p.id', 'ASC');
                               },
            'empty_value' => 'Escoja una opcion',
            'required' => false,
            'label' => 'Submenu de'                                       
            ));
    }

    public function getName()
    {
        return 'telconet_schemaBundle_sistitemmenutype';
    }
}
