<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints as Assert;

class InfoPuntoType extends AbstractType
{
    private $direccion;
    private $descripcionpunto;
    private $nombrepunto;
    private $observacion;
    private $tipoNegocioId;
    private $tipoUbicacionId;
    private $file;
    private $fileDigital;
    private $latitud;
    private $longitud;

    private $validaFile ;
    private $validaFileDigital ;
    private $empresaId;
	
	public function __construct($options) 
    {
        $this->validaFile        = $options['validaFile'];
        $this->validaFileDigital = $options['validaFileDigital'];
        $this->empresaId         = $options['empresaId'];
        
        if(isset($options['ubicacionDefault']))
        {
            $this->tipoUbicacionId = $options['ubicacionDefault'];
        }
        else
        {
            $this->tipoUbicacionId = null;
        }
        
        if(isset($options['datos']))
        {
            $this->direccion        = $options['datos']['direccion'];
            $this->descripcionpunto = $options['datos']['descripcionpunto'];
            $this->nombrepunto      = $options['datos']['nombrepunto'];
            $this->observacion      = $options['datos']['observacion'];
            $this->tipoNegocioId    = $options['datos']['tipoNegocioId'];
            $this->tipoUbicacionId  = $options['datos']['tipoUbicacionId'];
            $this->file             = $options['datos']['file'];
            $this->fileDigital      = $options['datos']['fileDigital'];
            $this->latitud          = $options['datos']['latitudFloat'];
            $this->longitud         = $options['datos']['longitudFloat'];
        }
    }
    
    /**
     * Documentación para el método 'buildForm'.
     *
     * Contruye tipo de estructura de componentes visuales que componen un formulario respecto a una entidad definida.
     *
     * @param FormBuilderInterface $builder Constructo del Formulario.
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-08-2016
     * @since   1.0
     * Se modificó el tamaño y ortografía de los componentes: direccion, descripcionpunto, observacion, tipoUbicacionId
     * 
     * @author Kenth Encalada <kencalada@telconet.ec>
     * @version 1.2 23-06-2023 - Se agrego una funcion onChange para limpiar los caracteres especiales de los campos.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $empresa             = $this->empresaId;
        $entityTipoUbicacion = $this->tipoUbicacionId;
        $this->validaFile    = false;
        
        if($this->validaFile)
        {
            $classFile = 'campo-obligatorio';
            $labelFile = '* Croquis:';
        }
        else
        {
            $classFile = '';
            $labelFile = 'Croquis:';
        }

        if($this->validaFileDigital)
        {
            $classFileDigital = 'campo-obligatorio';
            $labelFileDigital = '* Archivo:';
        }
        else
        {
            $classFileDigital = '';
            $labelFileDigital = 'Archivo:';
        }

        $builder
          
        
            ->add('direccion', 'textarea', array('label'      => '* Dirección:',
                                                 'label_attr' => array('class' => 'campo-obligatorio'),
                                                 'data'       => $this->direccion,
                                                 'attr'       => array('maxLength' => 100,
                                                                       'class'     => 'campo-obligatorio',
                                                                       'cols'      => 45,
                                                                       'rows'      => 3,
                                                                       'onChange'  => 
                                                                       'validarCaracterEspecial(
                                                                            "infopuntotype_direccion", 
                                                                            "Dirección");')))
            
            ->add('descripcionpunto', 'textarea', array('label'      => '* Referencia:',
                                                        'label_attr' => array('class' => 'campo-obligatorio'),
                                                        'data'       => $this->descripcionpunto,
                                                        'attr'       => array('maxLength' => 100,
                                                                              'class'     => 'campo-obligatorio',
                                                                              'cols'      => 45,
                                                                              'rows'      => 3,
                                                                              'onChange'  => 
                                                                              'validarCaracterEspecial(
                                                                                    "infopuntotype_descripcionpunto",
                                                                                    "Referencia");')))
            
            ->add('nombrepunto', 'text', array('required'   => false, 
                                               'label'      => 'Nombre Punto:',
                                               'label_attr' => array('class' => ''),
                                               'data'       => $this->nombrepunto,
                                               'attr'       => array('maxLength' => 50, 
                                                                     'class'     => 'campo-obligatorio')))
            
            ->add('observacion', 'textarea', array('required'   => false,
                                                   'label'      => 'Observación:',
                                                   'label_attr' => array('class' => ''),
                                                   'data'       => $this->observacion,
                                                   'attr'       => array('maxLength' => 150,
                                                                         'cols' => 45,
                                                                         'rows' => 3,
                                                                         'onChange'  => 
                                                                         'validarCaracterEspecial(
                                                                            "infopuntotype_observacion", 
                                                                            "Observación");')))
            
            ->add('tipoNegocioId', 'entity', array('class'         => 'telconet\schemaBundle\Entity\AdmiTipoNegocio',
                                                   'property'      => 'nombreTipoNegocio',
                                                   'label'         => '* Tipo Negocio:',
                                                   'label_attr'    => array('class' => 'campo-obligatorio'),
                                                   'required'      => true,
                                                   'em'            => 'telconet',
                                                   'empty_value'   => 'Seleccione...',
                                                   'data'          => $this->tipoNegocioId,
                                                   'query_builder' => function (EntityRepository $er)use ($empresa)
                                                                      {
                                                                          return $er->findTiposNegocioActivosPorEmpresa($empresa);
                                                                      },
                                                   'empty_data' => null))
                                                                          
            ->add('tipoUbicacionId', 'entity', array('class'         => 'telconet\schemaBundle\Entity\AdmiTipoUbicacion',
                                                     'property'      => 'descripcionTipoUbicacion',
                                                     'label'         => '* Tipo Ubicación:',
                                                     'label_attr'    => array('class' => 'campo-obligatorio'),
                                                     'required'      => true,
                                                     'em'            => 'telconet',
                                                     'empty_value'   => 'Seleccione...',
                                                     'data'          => $this->tipoUbicacionId,
                                                     'query_builder' => function (EntityRepository $er)
                                                                        {
                                                                            return $er->findTiposUbicacionActivos();
                                                                        },
                                                     'data'          => $entityTipoUbicacion,
                                                     'empty_data'    => null))
                                                        
            ->add('file', 'file', array('label'    => $labelFile, 
                                        'required' => $this->validaFile,
                                        'attr'     => array('class'  => $classFile,
                                                            'accept' => 'image/jpeg, image/png')))
                                                                            
            ->add('fileDigital', 'file', array('label'    => $labelFileDigital, 
                                               'required' => $this->validaFileDigital,
                                               'attr'     => array('class' => $classFileDigital)))
                                                                            
            ->add('latitud', 'text', array('required'   => false, 
                                           'label'      => 'Latitud:',
                                           'label_attr' => array('class' => ''),
                                           'data'       => $this->latitud,
                                           'attr'       => array('maxLength' => 10,
                                                                 'class'     => 'campo-obligatorio')))
                                                                            
            ->add('longitud', 'text', array('required'   => false, 
                                            'label'      => 'Longitud:',
                                            'label_attr' => array('class' => ''), 
                                            'data'       => $this->longitud,
                                            'attr'       => array('maxLength' => 10, 
                                                                  'class'     => 'campo-obligatorio')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'telconet\schemaBundle\Entity\InfoPunto'));
    }

    public function getName()
    {
        return 'infopuntotype';
    }
}
