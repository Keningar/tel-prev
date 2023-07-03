<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoDslamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $interfaceElemento = array('0' => 'Seleccione Puerto');
        $canton = array('0' => 'Seleccione Canton');
        $parroquia = array('0' => 'Seleccione Parroquia');
        $builder
            ->add('nombreElemento','text',
                    array(
                        'label'=>'* Nombre Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del elemento es requerido",
                            'onkeypress'=>'return validador(event,"")',
                            'maxlength'=>30)
                         )
                 )
            ->add('ipElemento','text',
                    array(
                        'label'=>'* Ip:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Ip del elemento es requerido",
                            'onkeypress'=>'return validador(event,"ip")',
                            'maxlength'=>15)
                         )
                 )
            ->add('popElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\InfoElemento',
                        'label'=>'* Pop:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('info_elemento')
                                                    ->select('info_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiModeloElemento','admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("info_elemento.modeloElementoId = admi_modelo_elemento")
                                                    ->andWhere("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'POP'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('info_elemento.nombreElemento', 'ASC');;
                                            }
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
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'DSLAM'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');;
                                            }
                          )				
                )
//            ->add('elementoId','entity',
//                    array('class' =>'telconet\schemaBundle\Entity\InfoElemento',
//                        'label'=>'* Switch Telconet:',
//                        'required' => false,
//                        'mapped' => false, 
//                        'attr' => array('class' => 'campo-obligatorio',
//                                        'onchange'=>'presentarInterfaces(this, "telconet_schemabundle_infoelementodslamtype_interfaceElementoId", "buscarInterfaces", "encontrados", "")',
//                                        ),
//                        'em'=> 'telconet_infraestructura',
//                        'query_builder' => function ($repository) {
//                                            return $repository->createQueryBuilder('info_elemento')
//                                                ->select('info_elemento')
//                                                ->from('telconet\schemaBundle\Entity\AdmiModeloElemento','admi_modelo_elemento')
//                                                ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
//                                                ->where("admi_modelo_elemento = info_elemento.modeloElementoId")
//                                                ->andWhere("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
//                                                ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'Switch'")
//                                                ->andWhere("admi_tipo_elemento.estado = 'Activo'")
//                                                ->orderBy('info_elemento.nombreElemento', 'ASC');
//                                            
//                                            }
//                                            
//                          )				
//                )
//            ->add('interfaceElementoId','choice',
//                    array('label'=>'* Puerto:',
//                        'choices' => $interfaceElemento,
//                        'required' => false,
//                        'read_only' => false,
//                        'mapped' => false,
//                        'attr' => array('class' => 'campo-obligatorio')
//                          )				
//                )
//            ->add('jurisdiccionId','entity',
//                    array('class' =>'telconet\schemaBundle\Entity\AdmiJurisdiccion',
//                        'label'=>'* Jurisdiccion:',
//                        'required' => false,
//                        'mapped' => false,
//                        'attr' => array('class' => 'campo-obligatorio',
//                                        'onchange'=>'presentarCantones(this, "telconet_schemabundle_infoelementodslamtype_cantonId", "buscarCantones", "encontrados", "")'),
//                        'em'=> 'telconet_infraestructura',
//                        'query_builder' => function ($repository) {
//                                            return $repository->createQueryBuilder('admi_jurisdiccion')
//                                                ->where("admi_jurisdiccion.estado != 'Eliminado'")
//                                                ->orderBy('admi_jurisdiccion.nombreJurisdiccion', 'ASC');;
//                                            }
//                          )				
//                )
//            ->add('cantonId','choice',
//                    array('label'=>'* Canton:',
//                        'required' => false,
//                        'choices' => $canton,
//                        'mapped' => false,
//                        'attr' => array('class' => 'campo-obligatorio',
//                                        'onchange'=>'presentarParroquias(this, "telconet_schemabundle_infoelementodslamtype_parroquiaId", "buscarParroquias", "encontrados", "")')
//                          )				
//                )
//            ->add('parroquiaId','choice',
//                    array('label'=>'* Parroquia:',
//                        'required' => false,
//                        'choices' => $parroquia,
//                        'mapped' => false,
//                        'attr' => array('class' => 'campo-obligatorio'),
//                          )				
//                )
//            ->add('alturaSnm','text',
//                    array(
//                        'label'=>'* Altura Sobre Nivel Mar:',
//                        'mapped' => false,
//                        'attr' => array(
//                            'class' => 'campo-obligatorio',
//                            'validationMessage'=>"Altura sobre nivel del mar es requerido",
//                            'maxlength'=>5)
//                         )
//                 )
//            ->add('longitudUbicacion','hidden',
//                    array(
//                        'label'=>'* Coordenadas Longitud:',
//                        'mapped' => false
//                         )
//                 )
//            ->add('latitudUbicacion','hidden',
//                    array(
//                        'label'=>'* Coordenadas Latitud:',
//                        'mapped' => false
//                         )
//                 )
//            ->add('direccionUbicacion','text',
//                    array(
//                        'label'=>'* Direccion:',
//                        'mapped' => false,
//                        'attr' => array(
//                            'class' => 'campo-obligatorio',
//                            'validationMessage'=>"Direccion es requerido",
//                            'maxlength'=>150)
//                         )
//                 )
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
        return 'telconet_schemabundle_infoelementodslamtype';
    }
}
