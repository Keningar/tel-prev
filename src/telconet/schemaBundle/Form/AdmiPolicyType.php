<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiPolicyType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
                      
            ->add('nombrePolicy','text',
                    array(
                        'label'=>'* Nombre Policy :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre es requerido",
                            'maxlength'=>50)
                         )
                 )
            ->add('leaseTime','text',
                    array(
                        'label'=>'* Lease Time :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Lease Time es requerido",
                            'onkeypress'=>'return isNumeric(event)',
                            'maxlength'=>50)
                         )
                 )
            ->add('mascara','text',
                    array(
                        'label'=>'* Mascara :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Mascara es requerida",
                            'onkeypress'=>'return isNumeric(event)',
                            'maxlength'=>50)
                         )
                 )
            ->add('gateway','text',
                    array(
                        'label'=>'* Gateway :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Gateway es requerida",
                            'onkeypress'=>'return isNumeric(event)',
                            'maxlength'=>50)
                         )
                 )
            ->add('dnsName','text',
                    array(
                        'label'=>'* Dns Name :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Dns Name es requerido",
                            'maxlength'=>50)
                         )
                 )
            ->add('dnsServers','text',
                    array(
                        'label'=>'* Dns Servers :',
                        'required'=>true,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Dns Servers es requerido",
                            'maxlength'=>50)
                         )
                 )
            
            ->add('elementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\InfoElemento',
                        'label'=>'* Elemento:',
                        'required' => false,
                        'empty_value' => 'Seleccione',		
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('info_elemento')
                                                    ->select('info_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiModeloElemento','admi_modelo_elemento')                                                    
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
        return 'telconet_schemabundle_admipolicytype';
    }
}
