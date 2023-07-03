<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiSnmpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('snmpCommunity','text',
                    array(
                        'label'=>'* Comunidad SNMP:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Snmp Comunidad es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('snmpVersion','text',
                    array(
                        'label'=>'* Version SNMP:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Version Snmp es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionSnmp','textarea',
                    array(
                        'label'=>'* Descripcion Snmp:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Snmp es requerido",)
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admisnmptype';
    }
}
