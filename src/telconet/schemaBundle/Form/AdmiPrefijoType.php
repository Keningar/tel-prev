<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiPrefijoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('proveedorRedId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiProveedorRed',
                        'label'=>'* Proveedor Red:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_proveedor_red')
                                                ->where("admi_proveedor_red.estado = 'Activo'");
                                            }
                          )				
                )
            ->add('cliente','text',
                    array(
                        'label'=>'Cliente:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
            ->add('nombreIpv4','text',
                    array(
                        'label'=>'* Nombre Ipv4:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre Ipv4 es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('nombreIpv6','text',
                    array(
                        'label'=>'Nombre Ipv6:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('descripcionPrefijo','textarea',
                    array(
                        'label'=>'* Descripcion Prefijo:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Prefijo es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiprefijotype';
    }
}
