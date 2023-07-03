<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiInterfaceModeloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modeloElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'=>'* Modelo:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                ->where("admi_modelo_elemento.estado != 'Eliminado'");
                                            }
                          )				
                )
            ->add('tipoInterfaceId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiTipoInterface',
                        'label'=>'* Tipo Interface:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_tipo_interface')
                                                ->where("admi_tipo_interface.estado != 'Eliminado'");
                                            }
                          )				
                )
            ->add('cantidadInterface','text',
                    array(
                        'label'=>'* Cantidad Interface:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Cantidad de la Interface es requerido",
                            'maxlength'=>10)
                         )
                 )
            ->add('formatoInterface','text',
                    array(
                        'label'=>'* Formato Interface:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Formato de la Interface es requerido",
                            'maxlength'=>10)
                         )
                 )
            ->add('claseInterface', 'choice', array(
                        'choices' => array('STANDAR'=>'Standar','MODULAR'=>'Modular'),
                        'label'=>'* Clase Interface',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admiinterfacemodelotype';
    }
}
