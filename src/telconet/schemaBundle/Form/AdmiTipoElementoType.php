<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiTipoElementoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreTipoElemento','text',
                    array(
                        'label'=>'* Nombre Tipo Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Tipo es requerido",
                            'maxlength'=>30)
                         )
                 )
            ->add('descripcionTipoElemento','textarea',
                    array(
                        'label'=>'* Descripcion Tipo Elemento:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Tipo es requerido",)
                         )
                 )
            ->add('claseTipoElemento', 'choice', array(
                        'choices' => array('ACTIVO'=>'Activo','PASIVO'=>'Pasivo'), 
                        'label'=>'* Clase Tipo Elemento',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
            ->add(  'esDe', 'entity', array(
                    'class'         => 'telconet\schemaBundle\Entity\AdmiParametroDet',
                    'label'         => '* Es De :',
                    'required'      => false,
                    'mapped'        => false,
                    'em'            => 'telconet_general',
                    'query_builder' =>  function ($repository)
                                        {
                                            return $repository->createQueryBuilder('admi_parametro_det')
                                                ->select('admi_parametro_det')
                                                ->from('telconet\schemaBundle\Entity\AdmiParametroCab','admi_parametro_cab')
                                                ->where("admi_parametro_cab.id = admi_parametro_det.parametroId")
                                                ->andWhere("admi_parametro_cab.nombreParametro ='TIPO_ELEMENTO'")
                                                ->andWhere("admi_parametro_cab.estado ='Activo'")
                                                ->andWhere("admi_parametro_det.estado ='Activo'")
                                                ->orderBy('admi_parametro_det.valor1', 'ASC');
                                        }
                )
            )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admitipoelementotype';
    }
}
