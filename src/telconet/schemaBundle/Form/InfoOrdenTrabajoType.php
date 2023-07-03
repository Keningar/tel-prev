<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoOrdenTrabajoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('tipoOrden')
            ->add('tipoOrden', 'choice', array('attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarOcultarServicios(this)'),'choices'=> array('N' => 'Nueva','R' => 'Reubicacion'),'empty_value' => 'Seleccione','required'  => true, 'label'=>'* Tipo de Orden: '))
            //->add('oficinaId')
            //->add('numeroOrdenTrabajo')
            //->add('feCreacion')
            //->add('feUltMod')
            //->add('usrCreacion')
            //->add('usrUltMod')
            //->add('ipCreacion')
            /*->add('puntoId','entity',array('class' =>'telconet\schemaBundle\Entity\InfoPunto',
											   'label'=>'* Punto cliente : ',
											   'attr' => array('class' => 'campo-obligatorio-select'),
											   'em'=> 'telconet',
                                                                                           'required' => true,
            ))*/
            ->add('ultimaMillaId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiTipoMedio',
											   'label'=>'* Ultima milla : ',
											   'attr' => array('class' => 'campo-obligatorio-select'),
											   'em'=> 'telconet_infraestructura',
											   'required' => false,
											   'empty_value' => 'Seleccione',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoOrdenTrabajo'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoordentrabajotype';
    }
}
