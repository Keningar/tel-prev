<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoDocumentoFinancieroCabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('oficinaId')
            //->add('puntoId')
            //->add('numeroFacturaSri','text',array('label'=>'No. Factura SRI:','required'=>true,'attr' => array('class' => 'campo-obligatorio','validationMessage'=>"Tipo es requerido")))
            //->add('subtotal')
            //->add('subtotalCeroImpuesto')
            //->add('subtotalConImpuesto')
            //->add('subtotalDescuento')
            //->add('valorTotal')
            //->add('entregoRetencionFte','choice',array('label'=>'Rte. fuente:','choices' => array('S' => 'Si', 'N' => 'No'),'empty_value' => 'Seleccione'))
            //->add('estadoImpresionFact')
            //->add('esAutomatica')
            //->add('prorrateo')
            //->add('reactivacion')
            //->add('recurrente')
            //->add('comisiona')
            //->add('feCreacion')
            //->add('feEmision')
            //->add('usrCreacion')
            //->add('tipoDocumentoId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_infodocumentofinancierocabtype';
    }
}
