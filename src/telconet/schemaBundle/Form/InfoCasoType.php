<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoCasoType extends AbstractType
{   
    private $arraytipoCaso;

    public function __construct($arraytipoCaso)
    {
        $this->arraytipoCaso = $arraytipoCaso['arraytipoCaso'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipoCasoId', 'entity', array(
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiTipoCaso',
                                            'em' => 'telconet_soporte',
                                            'query_builder' => function ($repositorio) {
                                                return $repositorio->createQueryBuilder('entity')
                                                                   ->where("entity.estado = 'Activo' and entity.nombreTipoCaso
                                                                                              in ('".$this->arraytipoCaso."')");
                                            },
                                            'empty_value'       => 'Escoja una opcion',
                                            'required'          => true,
                                            'preferred_choices' => array('1'),                                         
                                            'label'             => '* Tipo Caso:',
                                            'attr'              => array('onChange'=>"setearTipoCasoId(this);")

            ))
                                                 
            ->add('tipoNotificacionId', 'entity', array(
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiFormaContacto',
                                            'em' => 'telconet',
                                            'query_builder' => function ($repositorio) {
                                                return $repositorio->createQueryBuilder('entity')
                                                        ->where("entity.estado = 'Activo'");
                                            },
                                            'empty_value' => 'Escoja una opcion',
                                            'required' => false,
                                            'label' => '* Forma de Contacto:'  
            ))
                                                    
            ->add('nivelCriticidadId', 'entity', array(
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiNivelCriticidad',
                                            'em' => 'telconet_soporte',
                                            'query_builder' => function ($repositorio) {
                                                return $repositorio->createQueryBuilder('entity')
                                                        ->where("entity.estado = 'Activo'");
                                            },
                                            'empty_value' => 'Escoja una opcion',
                                            'required' => true,
                                            'label' => '* Nivel de Criticidad:'  
            ))
            ->add('tituloIni','text',array('label'=>'* Titulo Inicial:',
					  // 'attr'=>array('onkeypress'=>'return validador(event,"")','maxlength'=> '50')))
                        'attr'=>array('maxlength'=> '50')))					   
            ->add('versionIni','textarea',array('label'=>'* Version Inicial:',                                                
                                                'attr' => array('cols'=>'69','rows'=>'4'
								,'maxlength'=> '1000'
								)))      
            ->add('tipoAfectacion', 'choice', array(                           
                           'label' => '* Tipo Afectacion:',                           
                           'choices' => array(
                               'CAIDA' => 'Caida',
                               'INTERMITENCIA' => 'Intermitencia',
                               'SINAFECTACION' => 'Sin Afectacion'
                           ),                           
                           'empty_value' => 'Escoja una opcion',
                           'attr'=>array('onchange'=>'return mostrarAccionesPorTipoCaso(this.value)')
                       ))                                               
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infocasotype';
    }
}
