<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;


     /**
     * Clase InfoCuadrillaTareaRepository
     * 
     * Esta clase Repository contiene todas las funciones relacionadas a la nueva tabla INFO_CUADRILLA_TAREA
     * En esta tabla se van a asociar los integrantes de una cuadrilla con las tareas que se crean
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015 
     * 
     */

class InfoCuadrillaTareaRepository extends EntityRepository
{
    
     /**
     * getIntegrantesCuadrilla
     * 
     * Esta funcion ejecuta el Query que retorna la linea base de los los integrantes que conforman una cuadrilla
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 29-06-2016 Se agrega el campo empresaRolId en el select del query
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015 
     * 
     * @param array  $cuadrilla
     * 
     * @return array $strDatos
     * 
     */
    public function getIntegrantesCuadrilla($cuadrilla) 
    {


        $strQuery       = $this->_em->createQuery();        
 
        $strSelect =      " SELECT 
                                DISTINCT(infoPersona.id) as idPersona,infoPersona.identificacionCliente as identificacionCliente,
                                infoPersona.nombres as nombres,infoPersona.apellidos as apellidos,infoPersonaEmpresaRol.id as empresaRolId
                                
                              FROM 
                                schemaBundle:InfoPersona infoPersona,schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol,
                                schemaBundle:InfoEmpresaRol InfoEmpresaRol,schemaBundle:AdmiRol AdmiRol,schemaBundle:AdmiTipoRol AdmiTipoRol
                                
                              WHERE 
                                infoPersona.id = infoPersonaEmpresaRol.personaId
                                AND infoPersonaEmpresaRol.empresaRolId = InfoEmpresaRol.id
                                AND InfoEmpresaRol.rolId = AdmiRol.id
                                AND AdmiRol.tipoRolId = AdmiTipoRol.id
                                AND infoPersona.estado NOT IN (:varEstado)
                                AND infoPersonaEmpresaRol.estado NOT IN (:paramEstado)
                                AND AdmiTipoRol.descripcionTipoRol = :varTipoRol
                                AND infoPersonaEmpresaRol.cuadrillaId = :varCuadrillaId "; 
                                
        $strQuery->setParameter("varEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $strQuery->setParameter("paramEstado", array('Cancelado','Inactivo','Anulado','Eliminado'));
        $strQuery->setParameter("varTipoRol", 'Empleado');
        $strQuery->setParameter("varCuadrillaId", $cuadrilla);        
        $strQuery-> setDQL($strSelect);  
  
        $strDatos = $strQuery->getResult();        
     
        return $strDatos;
    }  

}