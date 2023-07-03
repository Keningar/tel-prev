<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Documentación para la clase 'VpnType'.
 *
 * Clase que contiene la funcionalidad de creación de formularios para la Administración de Vpns
 *
 * @author Kenneth Jimenez <kjimenez@telconet.ec>
 * @version 1.0 08-12-2015
*/
class VpnType extends AbstractType
{
    private $arrayListaFormatos;
    private $booleanCamara;
    public function __construct($arrayOptions)
    {
        $this->arrayListaFormatos = isset($arrayOptions['arrayListaFormatos']) ? $arrayOptions['arrayListaFormatos'] : null;
        $this->booleanCamara = isset($arrayOptions['booleanCamara']) ? $arrayOptions['booleanCamara'] : false;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tiene_camara','hidden',
                    array(
                          'data' => $this->booleanCamara ? 'SI' : 'NO',
                         )
                );
        if($this->booleanCamara)
        {
            $builder->add('es_camara','choice',
                    array(
                          'label'   => '* Servicio Cámara:',
                          'choices' => array(0=>"NO",1=>"SI"),
                          'data'    => "NO",
                          'attr'    => array(
                                            'onchange'          => 'cambiarVpnCamara();',
                                            'class'             => 'campo-obligatorio',
                                            'validationMessage' => 'Es servicio cámara es requerido'
                                       )
                        )
                 );
        }
        if($this->booleanCamara && !empty($this->arrayListaFormatos) && isset($this->arrayListaFormatos['choices']))
        {
            $builder->add('formato_camara','choice',
                    array(
                          'label'   => 'Formato Vrf Cámaras:',
                          'label_attr' => array('id' => 'vpn_form_formato_camara_lbl'),
                          'choices' => $this->arrayListaFormatos['choices'],
                          'data'    => $this->arrayListaFormatos['select'],
                          'attr'    => array(
                                        'onchange'          => 'cambiarVpnFormat();',
                                        'class'             => 'campo-obligatorio',
                                        'validationMessage' => 'Formato Vpn Cámaras es requerido'
                                       )
                         )
                 );
        }
        $builder->add('nombre','text',
                    array(
                          'label' => '* Nombre Vpn:',
                          'attr'  => array(
                                        'class'             => 'campo-obligatorio',
                                        'validationMessage' => 'Nombre de la VPN es requerido',
                                        'maxlength'         => 34
                                       )
                         )
                 )
        ;
    }

    public function getName()
    {
        return 'vpn_form';
    }
}
