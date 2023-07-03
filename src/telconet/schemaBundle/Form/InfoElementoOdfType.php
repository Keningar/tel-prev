<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'InfoElementoOdfType'.
 *
 * Clase utilizada para manejar la información de un rack dentro de un formulario html
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 13-02-2015
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.1 04-07-2017    Se agrega nuevo campo de ODF para determinar si el elemento aprovisiona factibilidad automatica para TN
 * 
 */
class InfoElementoOdfType extends AbstractType
{

    /**
     * Documentación para el método 'buildForm'.
     * 
     * Metodo utilizado para armar la estructura del type
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 13-02-2015
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreElemento', 'text', array(
                'label' => '* Nombre Elemento:',
                'attr' => array(
                    'class' => 'campo-obligatorio',
                    'validationMessage' => "Nombre del elemento es requerido",
                    'onkeypress' => 'return validador(event,"")',
                    'maxlength' => 300)
                )
            )
            ->add('nodoElementoId', 'entity', array('class' => 'telconet\schemaBundle\Entity\InfoElemento',
                'label' => '* Nodo:',
                'required' => false,
                'mapped' => false,
                'attr' => array('class' => 'campo-obligatorio'),
                'em' => 'telconet_infraestructura',
                'query_builder' => function ($repository)
            {
                return $repository->createQueryBuilder('info_elemento')
                    ->select('info_elemento')
                    ->from('telconet\schemaBundle\Entity\AdmiModeloElemento', 'admi_modelo_elemento')
                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento', 'admi_tipo_elemento')
                    ->where("info_elemento.modeloElementoId = admi_modelo_elemento")
                    ->andWhere("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'NODO'")
                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                    ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                    ->orderBy('info_elemento.nombreElemento', 'ASC');
                
            }
                )
            )
            ->add('modeloElementoId', 'entity', array('class' => 'telconet\schemaBundle\Entity\AdmiModeloElemento',
                'label' => '* Modelo:',
                'required' => true,
                'attr' => array('class' => 'campo-obligatorio'),
                'em' => 'telconet_infraestructura',
                'query_builder' => function ($repository)
            {
                return $repository->createQueryBuilder('admi_modelo_elemento')
                    ->select('admi_modelo_elemento')
                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento', 'admi_tipo_elemento')
                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'ODF'")
                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                    ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                    ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');
                
            }
                )
            )
            ->add('descripcionElemento', 'textarea', array(
                'label' => 'Descripcion Elemento:',
                'attr' => array("col" => "20", "row" => 10,
                    'validationMessage' => "Descripcion del Elemento es requerido",)
                )
            )
            ->add('unidadRack', 'text', array(
                'label' => '* Posicion/Unidad:',
                'attr' => array(
                    'class' => 'campo-obligatorio')
                )
            )
            ->add('nodoElementoId', 'text', array(
                'label' => '* Nodo:',
                'attr' => array(
                    'class' => 'campo-obligatorio')
                )
            )
            ->add('rackElementoId', 'text', array(
                'label' => '* Rack:',
                'attr' => array(
                    'class' => 'campo-obligatorio')
                )
            )
            ->add('factibilidadAutomatica', 'choice', array(
                'choices' => array('SI'=>'SI','NO'=>'NO'), 
                'label' => '* Factibilidad Automática:',
                'attr' => array(
                    'class' => 'campo-obligatorio'),
                'required'=>true,
                )
            )
            ->add('claseTipoMedioId', 'entity', array('class' => 'telconet\schemaBundle\Entity\AdmiClaseTipoMedio',
                'label' => '* Clase Tipo Medio:',
                'required' => true,
                'attr' => array('class' => 'campo-obligatorio'),
                'em' => 'telconet_infraestructura',
                'query_builder' => function ($repository)
            {
                return $repository->createQueryBuilder('admi_clase_tipo_medio')
                    ->where("admi_clase_tipo_medio.estado = 'Activo'")
                    ->orderBy("admi_clase_tipo_medio.nombreClaseTipoMedio", "ASC");
            }
                )
            )

        ;
    }

    /**
     * Documentación para el método 'getName'.
     *
     * Metodo utilizado para obtener nombre del type
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 13-02-2015
     */
    public function getName()
    {
        return 'telconet_schemabundle_infoelementoodftype';
    }

}
