<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiMecanismoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigoMecanismo','text',array('label'=>'* Codigo: ','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>4)))
            ->add('descripcionMecanismo','text',array('label'=>'* Descripcion:','required'=>true,'attr' => array('class' => 'campo-obligatorio','maxlength'=>30)))
            ->add('esCatalogoEstatico', 'choice', array(
													'choices' => array('S'=>'Si','N'=>'No'), 
													'multiple'=>false,
													'expanded'=>true,
													'label'=>'* Es Catalogo Estatico?',
													'attr' => array('class' => 'campo-obligatorio'),
													'required'=>true,
												  )
				)								  
            ->add('requierePlanificacion', 'choice', array(
													'choices' => array('S'=>'Si','N'=>'No'), 
													'multiple'=>false,
													'expanded'=>true,
													'label'=>'* Requiere Planific?',
													'attr' => array('class' => 'campo-obligatorio'),
													'required'=>true,
												  )
				  ) 
            ->add('servicioId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiTipoServicio',
											  'property'=>'descripcionServicio',
											  'label'=>'* Tipo Servicio:',
											  'required'=>true,
											  'attr' => array('class' => 'campo-obligatorio'),
											  'em'=> 'telconet',
											  'query_builder' => function ($repository) {
																   return $repository->createQueryBuilder('admi_tipo_servicio')
																					  ->where("admi_tipo_servicio.estado = 'Activo'");
																   }
												)				
				)
            ->add('unidadBwId','entity',array('class' =>'telconet\schemaBundle\Entity\AdmiUnidadMedidaBw',
											  'property'=>'nombreBw',
											  'label'=>'* Unidad BW:',
											  'required'=>true,
											  'attr' => array('class' => 'campo-obligatorio'),
											  'em'=> 'telconet',
											  'query_builder' => function ($repository) {
																   return $repository->createQueryBuilder('admi_unidad_medida_bw')
																					  ->where("admi_unidad_medida_bw.estado = 'Activo'");
																   }
												)				
				)
            ->add('caracteristics','entity',array('multiple'=>true,
											  'class' =>'telconet\schemaBundle\Entity\AdmiCaracteristica',
											  'property'=>'descripcionCaracteristica',
											  'required'=>false,'label'=>'Caracteristicas:',
											  'em'=> 'telconet',
											  'query_builder' => function ($repository) {
																   return $repository->createQueryBuilder('admi_caracteristica')
																					  ->where("admi_caracteristica.estado = 'Activo'");
																   }
												)				
				)
        ;
    }

    public function getName()
    {
        return 'telcos_adminbundle_admimecanismotype';
    }
}
