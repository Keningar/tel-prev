<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoSwitchPerimetralType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento de tipo 'SWITCH'                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 8-12-2105
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 12-10-2106  Se aumentó validación de los elementos con el naf
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreElemento','text',
                    array(
                        'label'=>'* Nombre Elemento:',
                        'attr' => array(
                                        'class'             => 'campo-obligatorio',
                                        'validationMessage' => "Nombre del elemento es requerido",
                                        'onkeypress'        => 'return validador(event,"")',
                                        'maxlength'         => 60)
                         )
                 )
            ->add('ipElemento','text',
                    array(
                        'label'=>'* Ip:',
                        'attr' => array(
                                        'class'             => 'campo-obligatorio',
                                        'validationMessage' => "Ip del elemento es requerido",
                                        'onkeypress'        => 'return validador(event,"ip")',
                                        'maxlength'         => 15
                                        )
                         )
                 )
            ->add('modeloElementoId','entity',
                    array('class'       => 'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'         => '* Modelo:',
                        'required'      => false,
                        'attr'          => array('class' => 'campo-obligatorio',
                                                 'onChange' => 'return validaNaf()'),
                        'em'            => 'telconet_infraestructura',
                        'query_builder' => function ($repository) 
                                            {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                    ->select('admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = ?1")
                                                    ->andWhere("admi_tipo_elemento.estado = ?2")
                                                    ->andWhere("admi_modelo_elemento.estado != ?3")
                                                    ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC')
                                                    ->setParameter(1, 'SWITCH')
                                                    ->setParameter(2, 'Activo')
                                                    ->setParameter(3, 'Eliminado');
                                            
                                            }
                          )				
                )
             ->add('descripcionElemento','textarea',
                    array(
                            'label'=>'* Descripcion Elemento:',
                            'attr' => array(
                                            "col"               => "20", 
                                            "row"               => 10,
                                            'validationMessage' => "Descripcion del Elemento es requerido"
                                           )
                         )
                 )
              ->add('nodoElementoId','text',
                    array(
                        'label'=>'* Nodo:',
                        'attr' => array('class' => 'campo-obligatorio')
                         )
                 )
              ->add('serieFisica','text',
                    array(
                        'label'=>'* Serie Fisica:',
                        'attr' => array('class'      => 'campo-obligatorio',
                                        'onkeypress' => 'return limpiaModeloElemento()')
                         )
                 )
              ->add('versionOs','text',
                    array(
                        'label'=>'* Version OS:',
                        'attr' => array('class' => 'campo-obligatorio')
                         )
                 )                              
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementoswitchperimetraltype';
    }
}
