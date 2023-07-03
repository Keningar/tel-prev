<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ipCreacion')
            ->add('codigoProducto')
            ->add('nombreProducto')
            ->add('descripcionProducto')
            ->add('frecuenciaProducto')
            ->add('feCreacion')
            ->add('feUltMod')
            ->add('usrCreacion')
            ->add('usrUltMod')
            ->add('tipoItem')
            ->add('aplicaPromocion')
            ->add('descuentoProducto')
            ->add('permiteVenta')
            ->add('multiplesPrecios')
            ->add('estado')
            ->add('tiempoPermitido')
            ->add('visiblePromocion')
            ->add('mecanismoId')
            ->add('empresaId')
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoproductotype';
    }
}
