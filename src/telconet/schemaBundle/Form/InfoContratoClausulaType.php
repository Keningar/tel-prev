<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoContratoClausulaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clausulaId')
            ->add('descripcionClausula')
            ->add('feCreacion')
            ->add('usrCreacion')
            ->add('feUltMod')
            ->add('usrUltMod')
            ->add('estado')
            ->add('contratoId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'telconet\schemaBundle\Entity\InfoContratoClausula'
        ));
    }

    public function getName()
    {
        return 'telconet_schemabundle_infocontratoclausulatype';
    }
}
