<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class InfoElementoCajaType extends AbstractType
{
    protected $empresaId;
    public function __construct($options) {
        $this->empresaId = $options['empresaId'];
    }

    /*
     * Documentación para el método 'buildForm'
     *
     * Método encargado de crear el formulario para una caja de dispersion
     *
     * @version 1.0 Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 22-11-2018 - Se actualiza el campo id por el idEdificacion con el objetivo de solucionar un error que se esta presentando en la
     *                           creación de cajas
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $empresa = $this->empresaId;
        $ubicadoEn = array( ''              => 'Seleccione Opcion',
                            'EDIFICIO'      => 'EDIFICIO', 
                            'PEDESTAL'      => 'PEDESTAL', 
                            'POZO'          => 'POZO',
                            'POSTE'         => 'POSTE',
                            'URBANIZACION'  => 'URBANIZACION',
                            'CONJUNTO'      => 'CONJUNTO');
        $nivel = array('' => 'Seleccione Opcion','1' => 'NIVEL 1', '2' => 'NIVEL 2');
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
                            'maxlength'=>200)
                         )
                 )
            ->add('ubicadoEn', 'choice', array(
                        'label'=>'* Ubicado En:',   
                        'required' => true,
                        'choices' => $ubicadoEn,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
                        )
                  )
            ->add('nivel', 'choice', array(
                        'label'=>'* Nivel:',   
                        'required' => true,
                        'choices' => $nivel,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio')
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
                                                    ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'CAJA DISPERSION'")
                                                    ->andWhere("admi_tipo_elemento.estado ='Activo'")
                                                ->andWhere("admi_modelo_elemento.estado != 'Eliminado'")
                                                ->orderBy('admi_modelo_elemento.nombreModeloElemento', 'ASC');;
                                            }
                          )				
                )
                ->add('idEdificacion','entity',
                    array('class' =>'telconet\schemaBundle\Entity\InfoElemento',
                        'label'=>'Edificación:',
                        'required' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function ($repository) {
                                            return $repository->createQueryBuilder('info_elemento')
                                                    ->select('info_elemento')
                                                    ->from('telconet\schemaBundle\Entity\AdmiModeloElemento','admi_modelo_elemento')
                                                ->from('telconet\schemaBundle\Entity\AdmiTipoElemento','admi_tipo_elemento')
                                                    ->where("admi_modelo_elemento = info_elemento.modeloElementoId")
                                                    ->andWhere("admi_tipo_elemento = admi_modelo_elemento.tipoElementoId")
                                                ->andWhere("admi_tipo_elemento.nombreTipoElemento = 'EDIFICACION'")
                                                    ->andWhere("info_elemento.estado = 'Activo'")
                                                    ->orderBy('info_elemento.nombreElemento', 'ASC');;
                                            }
                          )				
                )
            ->add('jurisdiccionId','entity',
                    array('class' =>'telconet\schemaBundle\Entity\AdmiJurisdiccion',
                        'label'=>'* Jurisdiccion:',
                        'required' => false,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarCantones(this, "telconet_schemabundle_infoelementocajatype_cantonId", "buscarCantones", "encontrados", "")'),
                        'em'=> 'telconet_infraestructura',
                        'query_builder' => function (EntityRepository $er) use ($empresa) {
                                                return $er->getJurisdiccionesPorEmpresa($empresa);
                                           }
                          )				
                )
            ->add('cantonId','choice',
                    array('label'=>'* Canton:',
                        'required' => false,
                        'choices' => $canton,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio',
                                        'onchange'=>'presentarParroquias(this, "telconet_schemabundle_infoelementocajatype_parroquiaId", "buscarParroquias", "encontrados", "")')
                          )				
                )
            ->add('parroquiaId','choice',
                    array('label'=>'* Parroquia:',
                        'required' => false,
                        'choices' => $parroquia,
                        'mapped' => false,
                        'attr' => array('class' => 'campo-obligatorio'),
                          )				
                )
            ->add('alturaSnm','text',
                    array(
                        'label'=>'* Altura Sobre Nivel Mar:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Altura sobre nivel del mar es requerido",
                            'maxlength'=>5)
                         )
                 )
            ->add('longitudUbicacion','hidden',
                    array(
                        'label'=>'* Coordenadas Longitud:',
                        'mapped' => false
                        )
                 )
            ->add('latitudUbicacion','hidden',
                    array(
                        'label'=>'* Coordenadas Latitud:',
                        'mapped' => false
                         )
                 )
            ->add('direccionUbicacion','text',
                    array(
                        'label'=>'* Direccion:',
                        'mapped' => false,
                        'attr' => array(
                            'class' => 'campo-obligatorio',
                            'validationMessage'=>"Direccion es requerido",
                            'maxlength'=>150)
                         )
                 )
             ->add('descripcionElemento','textarea',
                    array(
                        'label'=>'Descripcion Elemento:',
                        'attr' => array("col" => "20", "row" => 10,
                            'validationMessage'=>"Descripcion del Elemento es requerido",)
                         )
                 )
             ->add('observacion','textarea',
                 array(
                     'label'=>'Observación :',
                     'attr' => array("col" => "20", "row" => 10,
                         'validationMessage'=>"Observación del elemento es requerido",)
                      )
              )    
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_infoelementocajatype';
    }
}
