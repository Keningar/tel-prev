<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoOltType extends AbstractType
{
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento de tipo 'OLT'                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 11-11-2015 - Se agrega la función onchange al combo 'modeloElementoId' para que traiga los tipos de aprovisionamiento
     *                           dependiendo de la marca del modelo del elemento.
     *
     * @version 1.0 Version Inicial
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $interfaceElemento = array('0' => 'Seleccione Puerto');
        $canton = array('0' => 'Seleccione Canton');
        $parroquia = array('0' => 'Seleccione Parroquia');
        $builder
            ->add('nombreElemento','text',
                    array(
                        'label'=>'* Nombre Elemento:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Nombre del elemento es requerido",
                            'onkeypress'=>'return validador(event,"")',
                            'maxlength'=>30)
                         )
                 )
            ->add('ipElemento','text',
                    array(
                        'label'=>'* Ip:',
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Ip del elemento es requerido",
                            'onkeypress'=>'return validador(event,"ip")',
                            'maxlength'=>15)
                         )
                 )
            ->add('modeloElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'=>'* Modelo:',
                        'required' => false,
                        'attr' => array('class' => 'campo-obligatorio', 'onChange' => 'getAprovisionamientoIp();'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                    ->select('admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'OLT'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');;
                                            }
                          )				
                )
             ->add('descripcionElemento','textarea',
                    array(
                        'label'=>'Descripcion Elemento:',
                        'attr' => array("col" => "20", "row" => 10,
                            'validationMessage'=>"Descripcion del Elemento es requerido",)
                         )
                 )
             ->add('unidadRack','text',
                    array(
                        'label'=>'* Posicion/Unidad:',
                        'attr' => array(
                            'class' => 'campo-obligatorio')
                         )
                 )
              ->add('nodoElementoId','text',
                    array(
                        'label'=>'* Nodo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio')
                         )
                 )
              ->add('rackElementoId','text',
                    array(
                        'label'=>'* Rack:',
                        'attr' => array(
                            'class' => 'campo-obligatorio')
                         )
                 )
                              
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementoolttype';
    }
}
