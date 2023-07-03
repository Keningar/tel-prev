<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'InfoElementoRadioType'.
 *
 * Clase utilizada para manejar la información de una radio dentro de un formulario html
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 25-05-2016
 */
class InfoElementoRadioType extends AbstractType
{
    /**
     * Documentación para el método 'buildForm'.
     * 
     * Metodo utilizado para armar la estructura del type
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 25-05-2016
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
            ->add('nodoElementoId','text',
                    array(
                        'label'=>'* Nodo:',
                        'attr' => array(
                            'class' => 'campo-obligatorio')
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
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'RADIO'")
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
             ->add('macElemento', 'text', array(
                            'label' => '* Mac:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
             ->add('switchElementoId', 'text', array(
                            'label' => '* Switch:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
             ->add('interfaceSwitchId', 'text', array(
                            'label' => '* Interface Switch:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
             ->add('sid', 'text', array(
                            'label' => '* SID:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
             ->add('tipoElementoRed', 'text', array(
                            'label' => '* Tipo Elemento Red:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
             ->add('radioInicioId', 'text', array(
                            'label' => '* RADIO INICIO:',
                            'attr' => array(
                                'class' => 'campo-obligatorio')
                            )
                  )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementoradiotype';
    }
}
