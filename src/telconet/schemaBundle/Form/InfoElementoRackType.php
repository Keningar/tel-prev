<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'InfoElementoRackType'.
 *
 * Clase utilizada para manejar la información de un rack dentro de un formulario html
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 13-02-2015
 */
class InfoElementoRackType extends AbstractType
{
    /**
     * Documentación para el método 'buildForm'.
     * 
     * Metodo utilizado para armar la estructura del type
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 13-02-2015
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 2.0 20-03-2019
     * La modificación realizada obtiene marcas de modelo de elementos tipo
     * rack
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.1 01-07-2019
     * Se agregó validación para que no consulte elementos con estado 'Eliminado' en nodoElementoId
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ->add('nodoElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\InfoElemento',
                        'label'=>'* Nodo:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('info_elemento')
                                                    ->select('info_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiModeloElemento','admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("info_elemento.modeloElementoId = admi_modelo_elemento")
                                                    ->andWhere("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'NODO'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->andWhere("info_elemento.estado != 'Eliminado'")
                                                ->orderBy('info_elemento.nombreElemento', 'ASC');
                                            }
                          )				
                )
            ->add('modeloElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                        'label'=>'* Modelo:',
                        'required' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('admi_modelo_elemento')
                                                    ->select('admi_modelo_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'RACK'")
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
            ->add('marcaElementoId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiMarcaElemento',
                        'label'=>'* Marca:',
                        'required' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($objRepository) 
                        {
                                            return $objRepository->createQueryBuilder('admi_marca_elemento')
                                                                                             ->select('admi_marca_elemento')
                                                                                             ->from('schemaBundle:AdmiModeloElemento', 'admi_modelo_elemento')
                                                                                             ->from('schemaBundle:AdmiTipoElemento'  , 'admi_tipo_elemento')
                                                                                             ->where("admi_tipo_elemento.nombreTipoElemento = 'RACK'")
                                                                                             ->andWhere("admi_modelo_elemento.tipoElementoId = admi_tipo_elemento")
                                                                                             ->andWhere('admi_modelo_elemento.marcaElementoId = admi_marca_elemento.id')
                                                                                             ->andWhere("admi_marca_elemento.estado = 'Activo'")
                                                                                             ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                                                             ->andWhere("admi_tipo_elemento.estado = 'Activo'")
                                                                                             ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');
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
        return 'telconet_schemabundle_infoelementoracktype';
    }
}
