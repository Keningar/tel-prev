<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoDetalleSolicitudType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motivoId')
            ->add('usrCreacion')
            ->add('fechaCreacion')
            ->add('servicioId')
            ->add('tipoSolicitudId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoDetalleSolicitud'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_infodetallesolicitudtype';
    }
}
