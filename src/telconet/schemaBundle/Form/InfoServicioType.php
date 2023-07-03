<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoServicioType extends AbstractType
{
     private $strPrefijoEmpresa;
     
     public function __construct($options) 
     {
         $this->strPrefijoEmpresa = $options['strPrefijoEmpresa'];
     }
    /**
     * Documentación para el método 'buildForm'.
     *
     * Contruye tipo de estructura de componentes visuales que componen un formulario respecto a una entidad definida.
     *
     * @param FormBuilderInterface $builder Constructo del Formulario.
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 14-09-2016
     * @since   1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 31-07-2018 - Se realizan ajustes en el codigo para quitar las opciones de Traslado y Reubicación
     * @since   1.1
     *
     * Se modificó contenido del combo Tipo de Orden: 
     * Para TN:
     *             N: Nueva
     *             T: Traslado
     *             R: Reubicacion
     * Para MD:
     *             N: Nueva
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $strPrefijoEmpresa  = $this->strPrefijoEmpresa;
        if($strPrefijoEmpresa == 'MD')
        {
           $arrayTipoOrden = array('N' => 'Nueva');
        }
        else
        {
           $arrayTipoOrden = array('N' => 'Nueva');
        }
        $builder            
            ->add('tipoOrden', 'choice', array('choices'=> $arrayTipoOrden,
                                               'required'  => true, 
                                               'label'=>'* Tipo de Servicio: ',
                                               'preferred_choices' => array('N')))           
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoServicio'
        ));
    }

    public function getName()
    {
        return 'infoserviciotype';
    }
}
