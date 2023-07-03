<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoSplitterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contenidoEn = array('' => 'Seleccione Opcion','NODO' => 'NODO', 'CAJA DISPERSION' => 'CAJA DISPERSION');
        $nivel = array('' => 'Seleccione Opcion','1' => 'NIVEL 1', '2' => 'NIVEL 2');
        $elementoContenedor = array('0' => 'Seleccione Elemento');
        $builder
            ->add('nombreElemento','text',
                    array(
                        'label'=>'* Nombre Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del elemento es requerido",
                            'onkeypress'=>'return validador(event,"")',
                            'maxlength'=>200)
                         )
                 )
            ->add('contenidoEn', 'choice', array(
                        'label'=>'* Contenido En:',   
                        'required' => false,
                        'choices' => $contenidoEn,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
                        )
                  )
            ->add('nivel', 'choice', array(
                        'label'=>'* Nivel:',   
                        'required' => true,
                        'choices' => $nivel,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
                        )
                  )
            ->add('elementoContenedorId','hidden',
                    array('label'=>'* Elemento Contenedor:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
                          )				
                )
            
            ->add('modeloElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'=>'* Modelo:',
                        'required' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                    ->select('admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'SPLITTER'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');;
                                            }
                          )				
                )
             ->add('descripcionElemento','textarea',
                    array(
                        'label'=>'Descripcion Elemento:',
                        'attr' => array("col" => "20", "row" => 10,
                            'validationMessage'=>"Descripcion del Elemento es requerido",)
                         )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementosplittertype';
    }
}
