<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class InfoElementoNodoType extends AbstractType
{        
    public function buildForm(FormBuilderInterface $builder, array $options)
    {            
        $provincia = array('0' => 'Seleccione Provincia');
        $canton = array('0' => 'Seleccione Canton');
        $parroquia = array('0' => 'Seleccione Parroquia');
                
        $builder
            
            ->add('estado','text',                
                    array(
                        'label'=>'Estado:',                               
                        'read_only'=>true,
                        'data'=>"Pendiente",
                        'attr' => array(                                                        
                            'class' => 'campo-obligatorio',                      
                            'maxlength'=>10)
                         )
                 )                           
            
            ->add('nombreElemento','text',
                    array(
                        'label'=>'* Nombre Elemento:',                        
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del elemento es requerido",
                            'onkeypress' => 'return validador(event,"")',
                            'maxlength'=>30)
                         )
                 )
            //Campo ya no es obligatorio. Proyecto Nodo Fase 1.
            ->add('modeloElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'=>'Modelo:',
                        'required' => true,
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                    ->select('admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'NODO'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');;
                                            }
                          )				
                )
            ->add('regionId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiRegion',
                        'label'=>'* Región:',
                        'required' => false,
                        'mapped' => false,
                        'empty_value' => 'Seleccione',	
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarProvincias(this.value, "telconet_schemabundle_infoelementonodotype_provinciaId","")'),
                        'em'=> 'telconet_general',
                        'query_builder' => function (EntityRepository $er) {
                                                return $er->getRegiones();
                                           },         
                          )				
                )                                            
            ->add('provinciaId','choice',
                    array(
                        'label'=>'* Provincia:',
                        'required' => false,
                        'choices' => $provincia,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                       'onchange'=>'presentarCantones(this.value, "telconet_schemabundle_infoelementonodotype_cantonId","")')
                         )				
                )      

            ->add('cantonId','choice',
                    array('label'=>'* Cantón:',
                        'required' => false,
                        'choices' => $canton,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarParroquias(this.value, "telconet_schemabundle_infoelementonodotype_parroquiaId","")')
                          )				
                )
            ->add('parroquiaId','choice',
                    array('label'=>'* Parroquia:',
                        'required' => false,
                        'choices' => $parroquia,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                          )				
                )
            ->add('alturaSnm','text',
                    array(
                        'label'=>'* Altura Sobre Nivel Mar:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'onkeypress'=>'return isNumeric(event)',
                            'validationMessage'=>"Altura sobre nivel del mar es requerido",
                            'maxlength'=>5)
                         )
                 )
            ->add('longitudUbicacion','hidden',
                    array(
                        'label'=>'Coordenadas Longitud:',
                        'mapped' => false
                         )
                 )
            ->add('latitudUbicacion','hidden',
                    array(
                        'label'=>'Coordenadas Latitud:',
                        'mapped' => false
                         )
                 )
            ->add('direccionUbicacion','text',
                    array(
                        'label'=>'* Dirección:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Direccion es requerido",
                            'maxlength'=>150)
                         )
                 )
             ->add('descripcionElemento','textarea',
                    array(
                        'label'=>'Descripción:',
                        'attr' => array("col" => "40", "row" => 20,
                            'validationMessage'=>"Descripcion del Elemento es requerido",)
                         )
                 )
                                               
              ->add('observacion','textarea',
                    array(
                        'label'=>'Observación:',
                        'attr' => array("col" => "60", "row" => 20)                            
                         )
                 )     
                                               
               ->add('accesoPermanente','choice',
                    array(
                        'label'=>'Es 24x7:',
                        'empty_value' => 'Seleccione',
                        'choices'=> array('S'=>'SI','N'=>'NO'),
                        'attr' => array('class' => 'campo-obligatorio')                            
                         )
                 )                                                   
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementonodotype';
    }
}
