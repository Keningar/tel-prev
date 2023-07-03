<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoElementoContrasenaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            //combo tipo elemento
            ->add('tipoElementoId', 'entity', 
                array(
                    'class'         => 'telconet\schemaBundle\Entity\AdmiTipoElemento',
                    'label'         => '* Tipo Elemento :',
                    'required'      => false,
                    'mapped'        => false,
                    'attr'          => array('class'    => 'campo-obligatorio',
                                             'onchange' => 'presentarModelos(this, "telconet_schemabundle_infoelementocontrasenatype_modeloElementoId", '
                                                         . '"encontrados", "")'
                                            ),
                    'em'            => 'telconet_infraestructura',
                    'query_builder' => function ($repository)
                                        {
                                            return $repository  ->createQueryBuilder('admi_tipo_elemento')
                                                                ->select('admi_tipo_elemento')
                                                                ->where("admi_tipo_elemento.esDe = 'BACKBONE'")
                                                                ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                                ->andWhere("admi_tipo_elemento.claseTipoElemento ='ACTIVO'")
                                                                ->orderBy('admi_tipo_elemento.nombreTipoElemento', 'ASC');
                                        }
                )
            )
            //combo modelo elemento
            ->add('modeloElementoId', 'choice', 
                array(
                    'label'         => '* Modelo Elemento :',
                    'required'      => false,
                    'mapped'        => false,
                    'attr'          => array('class'    => 'campo-obligatorio',
                                             'onchange' => 'presentarElementoYUsuarios(this)'
                                            )
                )
            )
            //combo elemento
            ->add('elementoId', 'choice', 
                array(
                    'label'     => '* Elemento:',
                    'required'  => false,
                    'mapped'    => false,
                    'attr'      => array('class' => 'campo-obligatorio')
                )
            )
           //combo usuarios
            ->add('usuarioId', 'choice', 
                array(
                    'label'     => '* Usuario:',
                    'required'  => false,
                    'mapped'    => false,
                    'attr'      => array('class' => 'campo-obligatorio')
                )
            )
            //contraseña
            ->add('contrasena', 'text', 
                array(
                    'label'     => '* Contrasena:',
                    'attr'      => array(
                                        'class'     => 'campo-obligatorio',
                                        'maxlength' => 30
                                        )
                )
            )
            ->add('elemento','hidden',
                array(
                        'label'=>'* Elemento:',
                        'mapped' => false
                        )
                 )
            ->add('usuario','hidden',
                array(
                        'label'=>'* Usuario:',
                        'mapped' => false
                        )
                 )
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementocontrasenatype';
    }

}
