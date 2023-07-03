<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTagType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', 'text', array(
                'label' => '* Descripción:',
                'attr' => array(
                    'class' => 'campo-obligatorio',
                    'validationMessage' => "Nombre es requerido",
                    'maxlength' => 30)
                )
            )
            ->add('observacion', 'textarea', array(
                'label' => ' Observación:',
                'attr' => array("col" => "20", "row" => 10)
                )
            )
            ->add('elementoId', 'entity', array('class' => 'telconet\schemaBundle\Entity\InfoElemento',
                'label' => '* Elemento:',
                'required' => true,
                'empty_value' => 'Seleccione',
                'attr' => array('class' => 'campo-obligatorio'),
                'em' => 'telconet_infraestructura',
                'query_builder' => function ($repository)
                                    {
                                        return $repository->createQueryBuilder('info_elemento')
                                            ->select('info_elemento')
                                            ->from('telconet\schemaBundle\Entity\AdmiTipoElemento', 'admi_tipo_elemento')
                                            ->from('telconet\schemaBundle\Entity\AdmiModeloElemento', 'admi_modelo_elemento')
                                            ->where("info_elemento.modeloElementoId = admi_modelo_elemento")
                                            ->andWhere("admi_modelo_elemento.tipoElementoId = admi_tipo_elemento")
                                            ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'SERVIDOR'")
                                            ->andWhere("info_elemento.estado ='Activo'")
                                            ->orderBy('info_elemento.nombreElemento', 'ASC');
                                    }
                )
            )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitagtype';
    }

}
