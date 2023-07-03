<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiOidType extends AbstractType
{
    /**
     * buildForm
     *
     * Metodo encargado de retornar el formulario para la creación o edición de los OID
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para retorne las marcas pertenecientes a los UPS
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marcaElementoId', 'entity', array( 'class'         =>'telconet\schemaBundle\Entity\AdmiMarcaElemento',
                                                      'label'         =>'* Marca Elemento:',
                                                      'required'      => true,
                                                      'attr'          => array('class' => 'campo-obligatorio'),
                                                      'em'            => 'telconet_infraestructura',
                                                      'query_builder' => function ($repository) 
                                                                         {
                                                                            $qb = $repository->createQueryBuilder('ame')
                                                                                             ->select('ame')
                                                                                             ->from('schemaBundle:AdmiModeloElemento', 'amde')
                                                                                             ->from('schemaBundle:AdmiTipoElemento'  , 'ate')
                                                                                             ->where('ate.nombreTipoElemento = :nombreTipo')
                                                                                             ->andWhere('amde.tipoElementoId = ate.id')
                                                                                             ->andWhere('amde.marcaElementoId = ame.id')
                                                                                             ->andWhere('ame.estado = :estado')
                                                                                             ->andWhere('amde.estado = :estado')
                                                                                             ->andWhere('ate.estado = :estado');
                                                                            
                                                                            $qb->setParameter("estado",     "Activo");
                                                                            $qb->setParameter("nombreTipo", "UPS");
                                                                            
                                                                            return $qb;
                                                                         }
                                                    )				
                )
            ->add('nombreOid','text',
                                        array(
                                                'label'    =>'* Nombre Oid:',
                                                'required' => true,
                                                'attr'     => array(
                                                                        'class'             => 'campo-obligatorio',
                                                                        'validationMessage' => "Nombre Oid es requerido",
                                                                        'maxlength'         => 30
                                                                   )
                                             )
                 )
            ->add('Oid','text',
                                array(
                                        'label'     => '* Oid:',
                                        'required'  => true,
                                        'attr'      => array(
                                                                'class'             => 'campo-obligatorio',
                                                                'validationMessage' => "Oid es requerido",
                                                                'maxlength'         => 30,
                                                                'onKeyPress'        => 'return validarSoloNumerosYPuntos(event);'
                                                            )
                                     )
                 )
             ->add('descripcionOid','textarea',
                                                array(
                                                        'label'     =>'* Descripcion Oid:',
                                                        'required'  => true,
                                                        'attr'      => array(
                                                                                "col"               => "20", 
                                                                                "row"               => 10,
                                                                                'class'             => 'campo-obligatorio',
                                                                                'validationMessage' => "Descripcion del Oid es requerido"
                                                                            )
                                                     )
                 )
             
        ;
    }

    public function getName()
    {
        return 'telconet_schemabundle_admioidtype';
    }
}
