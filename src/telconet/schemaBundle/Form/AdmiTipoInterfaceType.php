<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoInterfaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conectorInterfaceId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiConectorInterface',
                        'label'=>'* Conector:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_conector_interface')
                                                ->where("admi_conector_interface.estado != 'Eliminado'");
                                            }
                          )				
                )
            ->add('nombreTipoInterface','text',
                    array(
                        'label'=>'* Nombre Tipo Interface:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo de Interface es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTipoInterface','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Interface:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo Interface es requerido",)
                         )
                 )
             ->add('capacidadEntrada','text',
                    array(
                        'label'=>'* Capacidad Entrada:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de entrada es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaEntrada', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'* Unid. Med. Capacidad Entrada',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
              ->add('capacidadSalida','text',
                    array(
                        'label'=>'* Capacidad Salida:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de salida es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaSalida', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'* Unid. Med. Capacidad Salida',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipointerfacetype';
    }
}
