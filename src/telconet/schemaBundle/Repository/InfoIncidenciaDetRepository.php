<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoIncidenciaDetRepository extends EntityRepository
{
    /**
     * 
     * getTodosEstadosIncidencia
     * Obtiene los registros de los estados de las incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 1-03-2019  
     * 
     * @param array $arrayParametros null
     * 
     * @return array $arrayResultado[
     *   estadoIncidencia - Estado de la Incidencia
     * ]
     *
     * costoQuery = 3 
     *
     */  
    public function getTodosEstadosIncidencia()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IID.STATUS
                    FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID
                    GROUP BY IID.STATUS ";
        
        $objRsm->addScalarResult('STATUS',   'estadoIncidencia',         'string');        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();

    }   
    
    /**
     * 
     * getTodosSubEstadosIncidencia
     * Obtiene los registros con los subEstados de las incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 29-04-2019
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   subEstadoIncidencia - Sub Estado de la Incidencia
     * ]
     *
     * costoQuery = 3
     *
     *
     */  
    public function getTodosSubEstadosIncidencia()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IID.SUB_ESTADO
                    FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID
                    WHERE IID.SUB_ESTADO IS NOT NULL
                    GROUP BY IID.SUB_ESTADO ";
        
        $objRsm->addScalarResult('SUB_ESTADO',   'subEstadoIncidencia',         'string');        
        
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;

    }

    /**
     * 
     * getTodosTipoCliente
     * Obtiene los registros con los tipos de clientes enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 08-04-2020
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   subEstadoIncidencia - Sub Estado de la Incidencia
     * ]
     *
     * costoQuery = 3
     *
     *
     */  
    public function getTodosTipoCliente()
    { 
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSql = " SELECT IID.TIPO_USUARIO
                        FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID
                        WHERE IID.TIPO_USUARIO IS NOT NULL
                        GROUP BY IID.TIPO_USUARIO ";
            
            $objRsm->addScalarResult('TIPO_USUARIO',   'tipoCliente',         'string');        
            
            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResultado = $e->getMessage();
        }

        return $arrayResultado;

    }
    
    /**
     * 
     * getTodosEstadoGestionIncidencia
     * Obtiene los registros con los estados de gestión de las incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 29-04-2019    
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   estadoGestionIncidencia - Estado de gestión de la Incidencia
     * ]
     *
     * costoQuery = 4
     *
     */  
    public function getTodosEstadoGestionIncidencia()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IID.ESTADO_GESTION
                    FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID
                    GROUP BY IID.ESTADO_GESTION ";
        
        $objRsm->addScalarResult('ESTADO_GESTION',   'estadoGestionIncidencia',         'string');        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();

    }
    
    /**
     * 
     * getTodosEstadosNotifIncidencia
     * Obtiene los registros con los estados de notificación de las incidencias enviadas por ECUCERT
     *
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 29-04-2019  
     *  
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   estadoNotificacionIncidencia - Estado de notificación de la Incidencia
     * ]
     *
     * costoQuery = 3
     *
     */  
    public function getTodosEstadosNotifIncidencia()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IIN.ESTADO
                    FROM DB_SOPORTE.INFO_INCIDENCIA_NOTIF IIN
                    GROUP BY IIN.ESTADO ";
        
        $objRsm->addScalarResult('ESTADO',   'estadoNotificacionIncidencia',         'string');        
        
        $objQuery->setSQL($strSql);
        
        $arrayResultado = $objQuery->getResult();
   
        return $arrayResultado;

    }
}
