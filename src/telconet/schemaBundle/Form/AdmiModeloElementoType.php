<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiModeloElementoType extends AbstractType
{
    /**
     * Metodo que se encarga de generar el formulario para el ingreso y actualización de los modelos de los elementos
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     * 
     * @version 1.0 - Version inicial
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1 - 31/01/2023 - Se extiende las cantidad maxima de caracteres del 
     *                             input del Nombre de modelo
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marcaElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiMarcaElemento',
                        'label'=>'* Marca:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_marca_elemento')
                                                ->where("admi_marca_elemento.estado = 'Activo'")
                                                ->orderBy("admi_marca_elemento.nombreMarcaElemento","ASC");
                                            }
                          )				
                )
            ->add('tipoElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiTipoElemento',
                        'label'=>'* Tipo Elemento:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_tipo_elemento')
                                                ->where("admi_tipo_elemento.estado = 'Activo'")
                                                    ->orderBy("admi_tipo_elemento.nombreTipoElemento","ASC");
                                            }
                          )				
                )
            ->add('nombreModeloElemento','text',
                    array(
                        'label'=>'* Nombre Modelo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del Modelo es requerido",
                            'maxlength'=>50)
                         )
                 )
            ->add('descripcionModeloElemento','textarea',
                    array(
                        'label'=>'* Descripcion Modelo:',
                        'attr' => array("col" => "20", "row" => 10,'class' => 'campo-obligatorio',
                            'validationMessage'=>"Descripcion del Modelo es requerido",)
                         )
                 )
             ->add('mttr','text',
                    array(
                        'label'=>'* MTTR:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El MTTR es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaMttr', 'choice', array(
                        'choices' => array('DIAS'=>'Dias','HORAS'=>'Horas','MINUTOS'=>'Minutos'), 
                        'label'=>'* Unid. Med. MTTR',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
              ->add('mtbf','text',
                    array(
                        'label'=>'* MTBF:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El MTBF es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaMtbf', 'choice', array(
                        'choices' => array('DIAS'=>'Dias','HORAS'=>'Horas','MINUTOS'=>'Minutos'), 
                        'label'=>'* Unid. Med. MTBF',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('anchoModelo','text',
                    array(
                        'label'=>'* Ancho:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El ancho es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaAncho', 'choice', array(
                        'choices' => array('MM'=>'mm','CM'=>'cm','M'=>'m','FT'=>'ft'), 
                        'label'=>'* Unid. Med. Ancho',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('largoModelo','text',
                    array(
                        'label'=>'* Largo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El largo es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaLargo', 'choice', array(
                        'choices' => array('MM'=>'mm','CM'=>'cm','M'=>'m','FT'=>'ft'), 
                        'label'=>'* Unid. Med. Largo',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
              ->add('altoModelo','text',
                    array(
                        'label'=>'* Alto:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El alto es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaAlto', 'choice', array(
                        'choices' => array('MM'=>'mm','CM'=>'cm','M'=>'m','FT'=>'ft'), 
                        'label'=>'* Unid. Med. Alto',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('pesoModelo','text',
                    array(
                        'label'=>'* Peso:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El peso es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaPeso', 'choice', array(
                        'choices' => array('GR'=>'gr','KG'=>'kg','LB'=>'lb'), 
                        'label'=>'* Unid. Med. Peso',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('uRack', 'choice', array(
                        'choices' => array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10'), 
                        'label'=>'* Unidades Rack',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                ) 
             ->add('capacidadEntrada','text',
                    array(
                        'label'=>'* Capacidad:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de entrada es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaEntrada', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps','GB'=>'GB'), 
                        'label'=>'* Unid. Med. Capacidad Entrada',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
              ->add('capacidadSalida','text',
                    array(
                        'label'=>'* Capacidad:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de salida es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadMedidaSalida', 'choice', array(
                        'choices' => array('BPS'=>'Bps','KBPS'=>'Kbps','MBPS'=>'Mbps','GB'=>'GB'), 
                        'label'=>'* Unid. Med. Capacidad Salida',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('capacidadVaFabrica','text',
                    array(
                        'label'=>'* Capacidad VA Fabrica:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de VA de fabrica es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadVaFabrica', 'choice', array(
                        'choices' => array('W'=>'Vatio','kW'=>'KiloVatio','MW'=>'MegaVatio'), 
                        'label'=>'* Unid. Med. Capacidad Fabrica',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('capacidadVaPromedio','text',
                    array(
                        'label'=>'* Capacidad VA Promedio:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La capacidad de VA de promedio es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('unidadVaPromedio', 'choice', array(
                        'choices' => array('W'=>'Vatio','kW'=>'KiloVatio','MW'=>'MegaVatio'), 
                        'label'=>'* Unid. Med. Capacidad Promedio',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'required'=>true,
                  )
                )
             ->add('precioPromedio','text',
                    array(
                        'label'=>'* Precio Promedio:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El precio promedio es requerido",
                            'maxlength'=>30)
                         )
                 )
             ->add('interfacesModelos','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        )
                 )
             ->add('usuariosAcceso','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        ) 
                 )
             ->add('protocolos','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        )
                 )
             ->add('tecnologias','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        )
                 )
             ->add('detallesModelo','hidden',
                    array(
                        'required'=>false,
                        'mapped'=>false  
                        )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admimodeloelementotype';
    }
}
