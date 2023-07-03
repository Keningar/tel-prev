<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'InfoElementoCassetteType'.
 *
 * Clase utilizada para manejar la información de un cassette dentro de un formulario html
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 17-12-2015
 */
class InfoElementoCassetteType extends AbstractType
{
    
    /**
     * Documentación para el método 'buildForm'.
     * 
     * Metodo utilizado para armar la estructura del type
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-12-2015
     * 
     * @author Antonio Ayala <jbozada@telconet.ec>
     * @version 1.1 08-12-2021 Se inicializa combo contenidoEn como hidden 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreElemento','text',
                    array(
                          'label'             => '* Nombre Elemento:',
                          'attr'              => array(
                          'class'             => 'campo-obligatorio',
                          'validationMessage' => "Nombre del elemento es requerido",
                          'onkeypress'        => 'return validador(event,"")',
                          'maxlength'         => 200)
                         )
                 )
            ->add('contenidoEn', 'hidden', 
                    array(
                          'label'    => '* Contenido En:',   
                          'required' => false,
                          'mapped'   => false,
                          'attr'     => array('class' => 'campo-obligatorio')
                         )
                  )
            ->add('elementoContenedorId','hidden',
                    array(
                          'label'    => '* Elemento Contenedor:',
                          'required' => false,
                          'mapped'   => false,
                          'attr'     => array('class' => 'campo-obligatorio')
                         )				
                )
            
            ->add('modeloElementoId','entity',
                    array(
                          'class'         => 'telconet\schemaBundle\Entity\AdmiModeloElemento',
                          'label'         => '* Modelo:',
                          'required'      => false,
                          'attr'          => array('class' => 'campo-obligatorio'),
                          'em'            => 'telconet_infraestructura',
                          'empty_value'   => 'Seleccione Modelo',
                          'query_builder' => function ($repository)
                                              {
                                               return $repository->createQueryBuilder('admi_modelo_elemento')
                                                                 ->select('admi_modelo_elemento')
                                                                 ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                                 ->where("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                                 ->andWhere("admi_tipo_elemento.nombreTipoElemento = ?1")
                                                                 ->andWhere("admi_tipo_elemento.estado =?2")
                                                                 ->andWhere("admi_modelo_elemento.estado != ?3")
                                                                 ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC')
                                                                 ->setParameter(1, 'CASSETTE')
                                                                 ->setParameter(2, 'Activo')
                                                                 ->setParameter(3, 'Eliminado');
                                              }
                         )				
                )
             ->add('descripcionElemento','textarea',
                    array(
                          'label'             => '* Descripcion Elemento:',
                          'attr'              => array("col" => "20", "row" => 10,
                          'validationMessage' => "Descripcion del Elemento es requerido",)
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
     * @version 1.0 17-12-2015
     */
    public function getName()
    {
        return 'telconet_schemabundle_infoelementocassettetype';
    }
}
