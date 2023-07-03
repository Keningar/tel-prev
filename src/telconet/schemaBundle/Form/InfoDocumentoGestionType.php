<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoDocumentoGestionType extends AbstractType
{
    const ESTADO_ELIMINADO = "Eliminado";
    
    /**
     * buildForm
     *
     * Metodo encargado de crear la estructura de un formulario de la tabla 'InfoDocumentoGestion'
     *
     * @param FormBuilderInterface  $builder
     * @param array                 $options
     * 
     * @version 1.0 Versión Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-09-2015 - Se incluye una validación para que no presente las extensiones de
     *                           documentos que sean iguales a NULL
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder     
             ->add('nombreDocumento', 'text', 
                   array(
                        'label'=>'* Nombre Documento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',                            
                            'maxlength'=>80)
                         )
                 )
						
            ->add('mensaje','textarea',
                    array(
                        'label'=>'Descripcion:',
                        'attr' => array("col" => "20", "row" => 10,"maxlength" => 200)
                         )
                 )
            ->add('modulo','choice',array('label'=>'* Modulo :',
					  'empty_value'=>'Escoja el Modulo',
					  'choices'=>array(
						    'COMERCIAL'=>'COMERCIAL',
						    'FINANCIERO'=>'FINANCIERO',
						    'TECNICO'=>'TECNICO',
						    'SOPORTE'=>'SOPORTE'
						    )
					  ))
	    ->add('tipoDocumentoId', 'entity', array(                                           
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiTipoDocumento',                                            
                                            'em' => 'telconet_comunicacion',    
                                            'empty_value'=>'Escoja Extension',
                                            'query_builder' => function ($repositorio) 
                                            {
                                                $qb = $repositorio->createQueryBuilder('entity')							    
                                                                  ->where("entity.estado not like :estadoEliminado
                                                                           AND entity.extensionTipoDocumento IS NOT NULL ");
                                                
                                                $qb->setParameter('estadoEliminado', self::ESTADO_ELIMINADO);
                                                
                                                return $qb;
                                            },                                           
                                           'required' => true,
                                           'label' => '* Extension Documento:'  ))
                                           
            ->add('tipoDocumentoGeneralId', 'entity', array(                                           
                                            'class' => 'telconet\\schemaBundle\\Entity\\AdmiTipoDocumentoGeneral',                                            
                                            'em' => 'telconet_general',    
                                            'empty_value'=>'Escoja tipo Documento',
                                            'query_builder' => function ($repositorio)
                                            {
                                                $qb = $repositorio->createQueryBuilder('entity')							    
                                                                  ->where("entity.estado not like :estadoEliminado" ); 
                                                
                                                $qb->setParameter('estadoEliminado', self::ESTADO_ELIMINADO);
                                                $qb->orderBy('entity.descripcionTipoDocumento', 'ASC');
                                                return $qb;
                                            },                                                                                                                         
                                           'required' => true,
                                           'label' => '* Tipo Documento:' ))                                               
                  
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_info_documentotype';
    }
}
