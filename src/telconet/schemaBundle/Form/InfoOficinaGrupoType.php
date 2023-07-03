<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoOficinaGrupoType extends AbstractType
{
    private $arrayCantones;
    private $intIdEmpresa = 0;
    private $boolEsOfiFact = false;
    public function __construct($options) 
    {
        $this->arrayCantones = $options['arrayCantones'];
        $this->intIdEmpresa = $options['idEmpresa'];
        if($options['esOfiFact'] == 'S')
        {
            $this->boolEsOfiFact = true;
        }
        else
        {
            $this->boolEsOfiFact = false;
        }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayCantones = array("" => "-- Escoja un canton --");
        if($this->arrayCantones && count($this->arrayCantones)>0)
        {
            foreach($this->arrayCantones as $key => $value)
            {
                $arrayCantones[$value["id"]] = $value["nombre"];
                
            }
        }
        
        $opciones = array('N' => 'NO', 'S' => 'SI');
        $opciones2 = array('' => 'Seleccion una opcion', 'N' => 'NO', 'S' => 'SI');
        
        $builder
            
	    ->add('empresaId', 'entity', array(
                        'em'=> 'telconet',
                        'class'         => 'telconet\\schemaBundle\\Entity\\InfoEmpresaGrupo',
                        'query_builder' => function ($repositorio) 
                                           {
                                             return $repositorio->createQueryBuilder('p')
                                                                ->where('p.id = :idEmpresa')
                                                                ->setParameter('idEmpresa', $this->getIdEmpresa())
                                                                ->orderBy('p.id', 'ASC');
                                           },
                        'required' => true,
                        'label' => '* Nombre Empresa:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Empresa es requerido",
                                'onChange' => "ajaxCargaDatosEmpresa(this.value);"
                            )                                 
                    ))
                                                                 
             ->add('esMatriz', 'choice', array(
                        'label'=>'* Es Matriz:',    
                        'choices' => $opciones,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Matriz es requerido",
                                'onChange' => "ajaxEsMatriz(this.value);"
                            )
                        )
                  )
                                     
            ->add('cantonId', 'choice', 
                    array('choices' => $arrayCantones,
                        'label' => '* Canton:',
                        'required'=>true,
                        'attr' => array('class' => 'campo-obligatorio',
                            'validationMessage'=>"Canton es requerido",) 
                        ))
                                                                    
            ->add('nombreOficina','text',
                    array(
                        'label'=>'* Nombre Oficina:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre de la Oficina es requerido",
                            'maxlength'=>30)
                         )
                 )	
                                                                    
            ->add('direccionOficina','text',
                    array(
                        'label'=>'* Direccion Oficina:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Direccion de la Oficina es requerido",
                            'maxlength'=>100)
                         )
                 )
                                                                    
            ->add('telefonoFijoOficina','text',
                    array(
                        'label'=>'* Telefono Fijo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El Telefono Fijo de la Oficina es requerido",
                            'maxlength'=>10)
                         )
                 )
                                                                    
            ->add('extensionOficina','text',
                    array(
                        'label'=>'* Extension:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La Extension de la Oficina es requerido",
                            'maxlength'=>5)
                         )
                 )
                                                                    
            ->add('faxOficina','text',
                    array(
                        'label'=>'Fax:',
                        'required' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El Fax de la Oficina es requerido",
                            'maxlength'=>10)
                         )
                 )
                                                                    
            ->add('codigoPostalOfi','text',
                    array(
                        'label'=>'* Codigo Postal:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"La Extension de la Oficina es requerido",
                            'maxlength'=>30)
                         )
                 )
                                                                 
             ->add('esOficinaFacturacion', 'choice', array(
                        'label'=>'* Es Oficina Facturacion:',    
                        'choices' => $opciones,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Oficina Facturacion es requerido", 
                            'onChange'=>"validarFacturacion(this.value);"                            
                            )
                        )
                  )
                                                                   
             ->add('numEstabSri', 'text', array('label'    => ($this->esOficinaFact() ? '* ' : '') . 'Número Estab. SRI:',
                                                'required' => $this->esOficinaFact(),
                                                'attr'     => array('maxlength' => 3)
                                                )
                 )
                                                                 
             ->add('esVirtual', 'choice', array(
                        'label'=>'Es Virtual:', 
                        'required' => false,   
                        'choices' => $opciones2,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Es Virtual es requerido"
                            )
                        )
                  )
                                                                    
            ->add('territorio','text',
                    array(
                        'label'=>'Territorio:',
                        'required' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"El Territorio de la Oficina es requerido",
                            'maxlength'=>20)
                         )
                 )
        ;
    }

    private function getIdEmpresa()
    {
        return $this->intIdEmpresa; 
    }
    
    private function esOficinaFact()
    {
        return $this->boolEsOfiFact; 
    }
    
    public function getName()
    {
        return 'telconet_schemabundle_infooficinagrupotype';
    }
}
