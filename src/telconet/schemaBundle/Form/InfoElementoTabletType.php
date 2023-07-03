<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Clase InfoElementoTabletType
 *
 * Clase que maneja el formulario para la creación y edición de Tablets de la Entidad de InfoElemento
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 12-11-2015
 * 
 */  
class InfoElementoTabletType extends AbstractType
{
    var $strEstadoEliminado = 'Eliminado';
    var $strEstadoActivo    = 'Activo';
    var $strTipoElemento    = 'TABLET';
    
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-12-2016 Se agrega el campo que guardará el id del responsable de la tablet y se permite copiar el imei y la serie en la caja
     *                         de texto respectiva.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.2 22-09-2020 Se agrega el campo que guardará el id unico por dispositivo llamado Publish Id, dicho campo será obligatorio al momento
     * de ingresar las tablets.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modeloElementoId','entity',
                    array('class'         =>'telconet\schemaBundle\Entity\AdmiModeloElemento',
                          'label'         =>'* MODELO:',
                          'required'      => true,
                          'attr'          => array('class' => 'campo-obligatorio'),
                          'em'            => 'telconet_infraestructura',
                          'empty_data'    => 'Seleccione',
                          'query_builder' => function ($repository) 
                                             {
                                                $qb = $repository->createQueryBuilder('ame')							    
                                                                 ->select('ame')
                                                                 ->from('telconet\schemaBundle\Entity\AdmiTipoElemento', 'ate')
                                                                 ->where("ate = ame.tipoElementoId")
                                                                 ->andWhere("ate.nombreTipoElemento = :tipoElemento")
                                                                 ->andWhere("ate.estado = :estadoActivo")
                                                                 ->andWhere("ame.estado != :estadoEliminado")
                                                                 ->orderBy('ame.nombreModeloElemento', 'ASC');
                                                                      
                                                $qb->setParameter("estadoEliminado", $this->strEstadoEliminado);
                                                $qb->setParameter("estadoActivo",    $this->strEstadoActivo);
                                                $qb->setParameter("tipoElemento",    $this->strTipoElemento);

                                                return $qb;
                                             }
                          )				
                )
            ->add('nombreElemento','text', array(
                                                    'label'    =>'* IMEI',
                                                    'required' => true,
                                                    'attr'     => array(
                                                                            'class'             => 'campo-obligatorio',
                                                                            'validationMessage' => "El IMEI es requerido",
                                                                            'maxlength'         => 20,
                                                                            'onKeyPress'        => 'return validarSoloNumeros(event);',
                                                                            'autocomplete'      => "off"
                                                                        )
                                                  )
                 )
            ->add('serieLogica','text', array(
                                                    'label'    =>'* PUBLISH ID',
                                                    'required' => true,
                                                    'attr'     => array(
                                                                            'class'             => 'campo-obligatorio',
                                                                            'validationMessage' => "El Publish Id es requerido",
                                                                            'maxlength'         =>  40,
                                                                            'onKeyPress'        => 'return validarLetrasYNumeros(event);',
                                                                            'autocomplete'      => "off"
                                                                        )
                                                  )
                 )
            ->add('serieFisica','text', array(
                                                    'label'    => '* SERIE FÍSICA',
                                                    'required' => true,
                                                    'attr'     => array(
                                                                            'class'             => 'campo-obligatorio',
                                                                            'validationMessage' => "La Serie Física es requerida",
                                                                            'maxlength'         => 20,
                                                                            'onKeyPress'        => 'return validarSoloNumeros(event);',
                                                                            'autocomplete'      => "off"
                                                                        )
                                                  )
                 )
            ->add("intIdPerResponsable", "hidden", array("mapped" => false))
        ;
    }

    
    /**
     * getName
     *
     * Método de usado para la creación del formulario de la tabla InfoElemento      
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-11-2015
     */
    public function getName()
    {
        return '';
    }
}