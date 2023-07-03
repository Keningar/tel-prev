<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SistPerfilType extends AbstractType
{
    private $modulos;
    
    public function __construct($options) {
        $this->modulos = $options['modulos'];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opciones = array('ACTIVE' => 'Activo', 'EDITED' => 'Modificado', 'DELETED' => 'Eliminado');
        $builder
            ->add('nombrePerfil','text', array('label' => '* Nombre'))
            ->add('modulos', 'choice', array('choices' => $this->modulos,
                                            'label' => 'Modulo',
                                            'read_only' => false,
                                            "mapped" => false,
                                            'attr' => array('onChange' => 'presentarAcciones(this)'))
            )
        ;
    }

    public function getName()
    {
        return 'telconet_schemaBundle_sistperfiltype';
    }
}
