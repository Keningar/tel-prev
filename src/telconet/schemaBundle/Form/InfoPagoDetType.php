<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoPagoDetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenciaId')
            ->add('valorPago')
            ->add('numeroReferencia')
            ->add('feAplicacion')
            ->add('estado')
            ->add('comentario')
            ->add('depositado')
            ->add('depositoPagoId')
            ->add('numeroCuentaBanco')
            ->add('feCreacion')
            ->add('feUltMod')
            ->add('usrCreacion')
            ->add('usrUltMod')
            ->add('pagoId')
            ->add('formaPagoId')
            ->add('bancoTipoCuentaId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoPagoDet'
        ));
    }

    public function getName()
    {
        return 'infopagodettype';
    }
}
