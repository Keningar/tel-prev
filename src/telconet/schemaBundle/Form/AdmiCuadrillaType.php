<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\DBAL\Types\DateType;

class AdmiCuadrillaType extends AbstractType
{
    var $intEmpresaCod      = 0;
    var $strEstadoEliminado = 'Eliminado';
    var $strEstadoInactivo  = 'Inactivo';
    var $strNombreArea      = 'Tecnica.';
    var $zonaId             = null;
    var $departamentoId     = null;
    var $tareaId            = null;
    var $strNombreProceso   = 'ACTIVIDADES CUADRILLA';
    
    /**
     * __construct
     *
     * Método de configuración inicial de la clase AdmiCuadrillaType                               
     *      
     * @param array $arrayParametros
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015
     */
    public function __construct($arrayParametros = null)
    {
        if( $arrayParametros )
        {
            if( isset($arrayParametros['intEmpresaCod']) )
            {
                if( $arrayParametros['intEmpresaCod'] )
                {
                    $this->intEmpresaCod = $arrayParametros['intEmpresaCod']; 
                }
            }
            
            if( isset($arrayParametros['strNombreArea']) )
            {
                if( $arrayParametros['strNombreArea'] )
                {
                    $this->strNombreArea = $arrayParametros['strNombreArea']; 
                }
            }
            
            if( isset($arrayParametros['zonaId']) )
            {
                if( $arrayParametros['zonaId'] )
                {
                    $this->zonaId = $arrayParametros['zonaId']; 
                }
            }
            
            if( isset($arrayParametros['tareaId']) )
            {
                if( $arrayParametros['tareaId'] )
                {
                    $this->tareaId = $arrayParametros['tareaId']; 
                }
            }
            
            if( isset($arrayParametros['departamentoId']) )
            {
                if( $arrayParametros['departamentoId'] )
                {
                    $this->departamentoId = $arrayParametros['departamentoId']; 
                }
            }
        }
    }
    
    
    /**
     * buildForm
     *
     * Método de usado para la creación del formulario de la tabla AdmiCuadrilla                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se añade los campos cuadrilla y zona
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 18-11-2015 - Se cambia para retorne los departamentos con estado diferente de 'Eliminado' o 'Inactivo'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 21-12-2016 Se modifica la consulta para obtener las zonas con estado diferente de 'Eliminado' o 'Inactivo'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 03-04-2017 Se modifica la consulta para obtener los departamentos filtrándolos por empresa.
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreCuadrilla','text', array(
                                                    'label' =>'* Nombre Cuadrilla:',
                                                    'attr'  => array(
                                                                        'class'             => 'campo-obligatorio',
                                                                        'validationMessage' => "Nombre de la Cuadrilla es requerido",
                                                                        'maxlength'         => 300,
                                                                        'onKeyPress'        => 'return validarCaracteresEspeciales(event);',
                                                                        'autocomplete'      => "off",
                                                                        'onpaste'           => "return false;"
                                                                    )
                                                  )
                 )                            
            ->add('zonaId', 'entity', array(                                           
                                              'class'         => 'telconet\\schemaBundle\\Entity\\AdmiZona',                                            
                                              'em'            => 'telconet_general',    
                                              'empty_value'   => 'Seleccione Zona',
                                              'query_builder' => function ($repositorio) 
                                                                 {
                                                                      $qb = $repositorio->createQueryBuilder('entity')							    
                                                                                    ->where("entity.estado <> :estado 
                                                                                             AND entity.estado <> :estadoInactivo")
                                                                                    ->orderBy("entity.nombreZona");
                                                                      
                                                                      $qb->setParameter("estado", $this->strEstadoEliminado);
                                                                      $qb->setParameter("estadoInactivo", $this->strEstadoInactivo);
                                                                      
                                                                      return $qb;
                                                                 },    
                                              'data'          => $this->zonaId,                                                                                                                       
                                              'required'      => ( $this->zonaId ) ? true : ( ($this->zonaId == null && $this->tareaId == null) 
                                                                                               ? true : false ),
                                              'label'         => '* Zona:' ))                            
            ->add('tareaId', 'entity', array(                                           
                                              'class'         => 'telconet\\schemaBundle\\Entity\\AdmiTarea',                                            
                                              'em'            => 'telconet_soporte',    
                                              'empty_value'   => 'Seleccione Tarea',
                                              'query_builder' => function ($repositorio) 
                                                                 {
                                                                      $qb = $repositorio->createQueryBuilder('entity')
                                                                                    ->join("entity.procesoId", "ap")
                                                                                    ->where("entity.estado not like :estadoEliminado
                                                                                             AND ap.nombreProceso = :nombreProceso
                                                                                             AND ap.estado not like :estadoEliminado")
                                                                                    ->orderBy("entity.nombreTarea");
                                                                      
                                                                      $qb->setParameter("estadoEliminado", $this->strEstadoEliminado);
                                                                      $qb->setParameter("nombreProceso",   $this->strNombreProceso);
                                                                      
                                                                      return $qb;
                                                                 },    
                                              'data'          => $this->tareaId,                                                                                                                       
                                              'required'      => ( $this->tareaId ) ? true : false,
                                              'label'         => '* Tarea:' ))                                
            ->add('departamentoId', 'entity', array(                                           
                                                      'class'         => 'telconet\\schemaBundle\\Entity\\AdmiDepartamento',                                            
                                                      'em'            => 'telconet_general',    
                                                      'empty_value'   => 'Seleccione Departamento',
                                                      'query_builder' => function ($repositorio) 
                                                                         {
                                                                             $qb = $repositorio->createQueryBuilder("entity")
                                                                                      ->join("entity.areaId", "a")
                                                                                      ->where("entity.estado not like :estadoEliminado
                                                                                               AND entity.estado not like :estadoInactivo 
                                                                                               AND a.empresaCod = :empresaCod
                                                                                               AND a.nombreArea = :nombreArea
                                                                                               AND entity.empresaCod = :empresaCod")
                                                                                      ->orderBy("entity.nombreDepartamento"); 
                                                                      
                                                                              $qb->setParameter("estadoEliminado", $this->strEstadoEliminado);
                                                                              $qb->setParameter("estadoInactivo",  $this->strEstadoInactivo);
                                                                              $qb->setParameter("empresaCod", $this->intEmpresaCod);
                                                                              $qb->setParameter("nombreArea", $this->strNombreArea);
                                                                              
                                                                              return $qb;
                                                                         }, 
                                                      'data'          => $this->departamentoId,                          
                                                      'required'      => true,
                                                      'label'         => '* Departamento:' ))                                                      
        ;
    }

    
    /**
     * getName
     *
     * Método de usado para la creación del formulario de la tabla AdmiCuadrilla                               
     *      
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se quita el nombre para que los parametros de este formulario sean leidos con un nombre mas corto
     *                           el cual solo incluye el nombre del campo configurado en la función 'buildForm'
     * 
     * @version 1.0 Version Inicial
     */
    public function getName()
    {
        return '';
    }
}
