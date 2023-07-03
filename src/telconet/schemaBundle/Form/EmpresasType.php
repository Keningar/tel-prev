<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EmpresasType extends AbstractType
{
        
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder     
            ->add('nombreEmpresa','entity',
                    array('class' =>'telconet\schemaBundle\Entity\InfoEmpresaGrupo',
                        'label'=>'* Empresa a Asignar:',
                        'required' => true,                        
                        'empty_value' => 'Escoja la Empresa',
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('info_empresa_grupo')
                                                    ->select('info_empresa_grupo')
                                                    ->from('telconet\schemaBundle\Entity\InfoEmpresaGrupo','ieg')                                                    
                                                    ->where("ieg.estado = 'Activo'")                                                    
						    ->orderBy('ieg.nombreEmpresa', 'ASC');;
                                            }
                          )				
                )		            
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_empresastype';
    }
}
