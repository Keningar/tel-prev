<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\administracionBundle\Service;

use \telconet\schemaBundle\Entity\AdmiPuntoAtencion;


/**
 * Description of PuntoAtencionService
 *
 * @author imata
 */
class PuntoAtencionService {
    
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
   
    /**
     * @var \telconet\administracionBundle\Service\UtilidadesService
     */
    private $serviceUtilidades;
    
    /**
     * setDependencies
     *
     * Método que agrega las dependencias usadas en el service                                   
     *      
     * @param ContainerInterface $container
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 03-04-2021 - Se agrega la variable '$this->emGeneral' que contiene al entity manager del esquema 'DB_GENERAL'
     *
     */
    public function setDependencies( \Symfony\Component\DependencyInjection\ContainerInterface $container )
    {
        $this->emComercial       = $container->get('doctrine.orm.telconet_entity_manager');
        $this->utilServicio      = $container->get('schema.Util');
    }
    
    
    
    /**
     * Metodo encargado de guardar un nuevo punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     *
     * @param type $arrayParametros
     * @return array $arrayRespuesta
     */
    public function guardarPuntoAtencion($arrayParametros)
    {
        
        $emComercial    = $this->emComercial;
        $serviceUtil    = $this->utilServicio;
        $strNombrePA    = $arrayParametros['strPuntoAtencion'];
        $strEmpresaCod  = $arrayParametros['strEmpresaCod'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strFechaActual = new \DateTime('now');
        
        
        $emComercial->getConnection()->beginTransaction();
        
        try 
        {
            
            $objPuntoAtencion =  new AdmiPuntoAtencion();
            $objPuntoAtencion->setNombrePuntoAtencion($strNombrePA);
            $objPuntoAtencion->setEstado("Activo");
            $objPuntoAtencion->setUsrCreacion($strUsrCreacion);
            $objPuntoAtencion->setFeCreacion($strFechaActual);
            $objPuntoAtencion->setIpCreacion($strIpCreacion);
            $objPuntoAtencion->setEmpresaCod($strEmpresaCod);
            
            $emComercial->persist($objPuntoAtencion);
            $emComercial->flush();
            
            $emComercial->getConnection()->commit();
            
            $arrayRespuesta['strStatus']  = "OK";
            $arrayRespuesta['strMensaje'] = "Se realizó el registro del punto de atención correctamente";
                
        } 
        catch (\Exception $ex) 
        {
         
            $arrayRespuesta['strStatus']  = "ERROR";
            $arrayRespuesta['strMensaje'] = $ex->getMessage();
                     

            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.PuntoAtencionService.guardarPuntoAtencion',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion);

            $emComercial->getConnection()->rollback();
            
        }
        
        return $arrayRespuesta;
    }
    
    
    /**
     * Metodo encargado de eliminar un punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     *
     * @param type $arrayParametros
     * @return array $arrayRespuesta
     */
    public function eliminarPuntoAtencion($arrayParametros)
    {
     
        $emComercial    = $this->emComercial;
        $serviceUtil    = $this->utilServicio;
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strFechaActual = new \DateTime('now');
        
        $emComercial->getConnection()->beginTransaction();
        
        try 
        {
             $objPuntoAtencion  = $emComercial->getRepository('schemaBundle:AdmiPuntoAtencion')
                                         ->findOneBy(array('id'  => $arrayParametros['intIdPuntoAtencion']));
        
        
             if(is_object($objPuntoAtencion))
             {
                 $objPuntoAtencion->setEstado("Eliminado");
                 $objPuntoAtencion->setUsrModificacion($strUsrCreacion);
                 $objPuntoAtencion->setFeModificacion($strFechaActual);
                 
                 $emComercial->persist($objPuntoAtencion);
                 $emComercial->flush();
            
                 $emComercial->getConnection()->commit();
            
                 $arrayRespuesta['strStatus']  = "OK";
                 $arrayRespuesta['strMensaje'] = "Se ha eliminado correctamente el punto de atención";
             }
             else 
             {
                throw new \Exception('No existe información del punto de atención ingresado');
             }
        } 
        catch (\Exception $ex)
        {
            
            $arrayRespuesta['strStatus']  = "ERROR";
            $arrayRespuesta['strMensaje'] = $ex->getMessage();
                     

            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.PuntoAtencionService.eliminarPuntoAtencion',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion);

            $emComercial->getConnection()->rollback();
        }
       
         return $arrayRespuesta;
        
        
    }
    
    /**
     * Metodo encargado de editar un punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     *
     * @param type $arrayParametros
     * @return array $arrayRespuesta
     */
    public function editarPuntoAtencion($arrayParametros)
    {
        $emComercial    = $this->emComercial;
        $serviceUtil    = $this->utilServicio;
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strFechaActual = new \DateTime('now');
        
        
        $emComercial->getConnection()->beginTransaction();
        
        try 
        {
            $objPuntoAtencion  = $emComercial->getRepository('schemaBundle:AdmiPuntoAtencion')
                                         ->findOneBy(array('id'  => $arrayParametros['intIdPuntoAtencion']));
        
        
            if(is_object($objPuntoAtencion))
            {
                 $objPuntoAtencion->setNombrePuntoAtencion($arrayParametros['strNombrePuntoAtencion']);
                 $objPuntoAtencion->setEstado("Modificado");
                 $objPuntoAtencion->setUsrModificacion($strUsrCreacion);
                 $objPuntoAtencion->setFeModificacion($strFechaActual);
                 
                 $emComercial->persist($objPuntoAtencion);
                 $emComercial->flush();
            
                 $emComercial->getConnection()->commit();
            
                 $arrayRespuesta['strStatus']  = "OK";
                 $arrayRespuesta['strMensaje'] = "Se ha editado correctamente el punto de atención";
            }
            else 
            {
                throw new \Exception('No existe información del punto de atención ingresado');
            }
             
        } 
        catch (\Exception $ex) 
        {
            $arrayRespuesta['strStatus']  = "ERROR";
            $arrayRespuesta['strMensaje'] = $ex->getMessage();
                     

            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.PuntoAtencionService.editarPuntoAtencion',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion);

            $emComercial->getConnection()->rollback();
        }
        
        return $arrayRespuesta;
        
    }
    
    
    
}
