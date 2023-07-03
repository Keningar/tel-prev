<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoEnlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $interfaceElemento = array('0' => 'Seleccione Puerto');
        $canton = array('0' => 'Seleccione Canton');
        $parroquia = array('0' => 'Seleccione Parroquia');
        $builder
            ->add('tipoElementoIdA','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiTipoElemento',
                        'label'=>'* Tipo Elemento A:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarElementoA(this, "telconet_schemabundle_infoenlacetype_elementoIdA", "buscarElementoPorTipoElemento", "encontrados", "")'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_tipo_elemento')
                                                    ->select('admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento.esDe = 'BACKBONE'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                    ->andWhere("admi_tipo_elemento.claseTipoElemento ='ACTIVO'")
                                                ->orderBy('admi_tipo_elemento.nombreTipoElemento', 'ASC');
                                            }
                          )				
                )
            ->add('tipoElementoIdB','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiTipoElemento',
                        'label'=>'* Tipo Elemento B:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarElementoB(this, "telconet_schemabundle_infoenlacetype_elementoIdB", "buscarElementoPorTipoElemento", "encontrados", "")'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_tipo_elemento')
                                                    ->select('admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento.esDe = 'BACKBONE'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                    ->andWhere("admi_tipo_elemento.claseTipoElemento ='ACTIVO'")
                                                ->orderBy('admi_tipo_elemento.nombreTipoElemento', 'ASC');
                                            }
                          )				
                )
            ->add('elementoIdA','choice',
                    array('label'=>'* Elemento A:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarInterfaces(this, "telconet_schemabundle_infoenlacetype_interfaceElementoIdA", "buscarInterfacesPorElemento", "encontrados", "")'),
                        
                          )				
                )
            ->add('interfaceElementoIdAchoice','choice',
                    array('label'=>'* Interface A:',
                        'required' => false,
                        'choices' => $interfaceElemento,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                          )				
                )
            ->add('elementoIdB','choice',
                    array('label'=>'* Elemento B:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarInterfaces(this, "telconet_schemabundle_infoenlacetype_interfaceElementoIdB", "buscarInterfacesPorElemento", "encontrados", "")'),
                        
                          )				
                )
            ->add('interfaceElementoIdBchoice','choice',
                    array('label'=>'* Interface B:',
                        'required' => false,
                        'choices' => $interfaceElemento,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                          )				
                )
            ->add('interfaceElementoIdA','hidden',
                    array(
                        'label'=>'* Interface A:',
                        'mapped' => false
                        )
                 )
            ->add('interfaceElementoIdB','hidden',
                    array(
                        'label'=>'* Interface B:',
                        'mapped' => false
                        )
                 )
                                  
           //---- CAPACIDAD IN / OUT            
            ->add('capacidadInput','text',
                    array(
                        'label'=>'Capacidad Entrada:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaInput', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'Unid. Med. Capacidad Entrada'
                  )
                )
             ->add('capacidadOutput','text',
                    array(
                        'label'=>'Capacidad Salida:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaOutput', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'Unid. Med. Capacidad Salida'
                  )
                )
            //---- CAPACIDAD INICIO - FIN                    
            ->add('capacidadIniFin','text',
                    array(
                        'label'=>'Capacidad Inicio - Fin:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaUp', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'Unid. Med. Capacidad Up'
                  )
                )
             ->add('capacidadFinIni','text',
                    array(
                        'label'=>'Capacidad Fin - Inicio:',
                        'attr' => array(
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaDown', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps'), 
                        'label'=>'Unid. Med. Capacidad Down'
                  )
                )
                                  
                                  
            ->add('tipoEnlace', 'choice', array(
                        'choices' => array('PRINCIPAL'=>'Principal','BACKUP'=>'BackUp'), 
                        'label'=>'* Tipo Enlace',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
                                  
                                  
            ->add('tipoMedioId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiTipoMedio',
                        'label'=>'* Tipo Medio:',
                        'empty_value' => 'Seleccione',	
                        'required' => true,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarClaseTipoMedio(this.value, "telconet_schemabundle_infoenlacetype_claseTipoMedioId","")'
                                        ),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_tipo_medio')
                                                ->where("admi_tipo_medio.estado != 'Eliminado'")
                                                ->orderBy('admi_tipo_medio.nombreTipoMedio', 'ASC');;
                                            }
                          )				
                )
                                            
             ->add('claseTipoMedioId','choice',
                    array(
                        'label'=>'Clase Tipo Medio:',
                        'empty_value' => 'Seleccione',	
                        'required' => true,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarBuffer(this.value, "telconet_schemabundle_infoenlacetype_bufferId","")'
                                        )                                              
                          )				
                )  
                                                                        
              ->add('bufferId','choice',
                    array(
                        'label'=>'Buffer:',
                        'empty_value' => 'Seleccione',	
                        'required' => true,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarHilo(this.value, "telconet_schemabundle_infoenlacetype_hiloId","")'
                                        )                                                                                               
                          )				
                ) 
                              
              ->add('hiloId','choice',
                    array(
                        'label'=>'Hilos:',
                        'empty_value' => 'Seleccione',	
                        'required' => true,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')                                                                                                                                                 
                          )				
                )  
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoenlacetype';
    }
}
