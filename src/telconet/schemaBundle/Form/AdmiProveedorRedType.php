<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiProveedorRedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreProveedorRed','text',
                    array(
                        'label'=>'* Nombre Proveedor Red:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Proveedor Red es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('routeMapIpv4','text',
                    array(
                        'label'=>'* Route Map Ipv4:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Route Map Ipv4 es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('ipNeighborIpv4','text',
                    array(
                        'label'=>'* Ip Neighbor Ipv4:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Ip Neighbor Ipv4 es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('routeMapIpv6','text',
                    array(
                        'label'=>'Route Map Ipv6:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('ipNeighborIpv6','text',
                    array(
                        'label'=>'Ip Neighbor Ipv6:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionProveedorRed','textarea',
                    array(
                        'label'=>'* Descripcion Proveedor Red:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Proveedor Red es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiproveedorredtype';
    }
}
