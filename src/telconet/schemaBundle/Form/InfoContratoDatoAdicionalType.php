<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoContratoDatoAdicionalType extends AbstractType
{
     /**
     * buildForm
     *
     * Metodo encargado de crear la estructura de un formulario de la tabla 'InfoContratoDatoAdicional'
     *
     * @param FormBuilderInterface  $builder
     * @param array                 $options
     *  
     * @version 1.0 Versión Inicial
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 08-07-2016 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('esVip','checkbox',array('label'=>'Es Vip:','required'=> false))
            ->add('esTramiteLegal','checkbox',array('label'=>'Tramite Legal:','required'=> false))
            ->add('permiteCorteAutomatico','checkbox',array('label'=>'Corte automatico:','required'=> false))
            ->add('fideicomiso','checkbox',array('label'=>'Fideicomiso:','required'=> false))
            ->add('convenioPago','checkbox',array('label'=>'Convenio pago:','required'=> false))
            ->add('notificaPago','checkbox',array('label'=>'Notifica pago:','required'=> false))
            ->add('tiempoEsperaMesesCorte','text',array('label'      => 'Tiempo mes corte:',
                                                        'label_attr' => array('class' => 'campo-obligatorio'),
                                                        'required'   => false,
                                                        'attr'       => array('style' => 'margin-left: 100px; width:60px;')                                                    
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoContratoDatoAdicional'
        ));
    }

    public function getName()
    {
        return 'infocontratodatoadicionaltype';
    }
}
