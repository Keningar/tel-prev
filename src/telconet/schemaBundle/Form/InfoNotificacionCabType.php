<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoNotificacionCabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('estado')
            ->add('titulo')
            ->add('contenido','textarea',array("attr" => array("col" => "10", "row" => 3)))
            //->add('feCreacion')
            //->add('usrCreacion')
            //->add('feUltMod')
            //->add('usrUltMod')
            //->add('ipCreacion')
            //->add('empresaId')
        ;
    }

    public function getName()
    {
        return 'infonotificacioncabtype';
    }
}
