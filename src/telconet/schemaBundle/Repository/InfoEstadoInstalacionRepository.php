<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoEstadoInstalacionRepository extends BaseRepository
{
    
    /**
      * ObtenerEstadoProceso
      * Funcion que regresa el ultimo estado del proceso de activacion
      * @param array $arrayParametros[
      *                              objServicio   => objeto del servicio del cliente
      *                              strCodEmpresa => codigo de la empresa
      *                             ]
      *
      * @return json con resultado
      * @author Nestor Naula Lopez <nnaulal@telconet.ec>
      * @version 1.1 22/06/2018
     */
    public function ObtenerEstadoProceso($intIdServicio){
        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
        $where="";
        
        $strSql = " SELECT ESTADO
                    FROM(SELECT MAX(ID_ESTADO_INSTALACION) AS ESTADO_INSTALACION_ID
                    FROM DB_SOPORTE.INFO_ESTADO_INSTALACION 
                    ";
        if (isset($intIdServicio) && !empty($intIdServicio)) 
        {
              $where .= " WHERE SERVICIO_ID = :servicioId ";
              $objQuery->setParameter("servicioId", $intIdServicio);
        }
        $strSql .= $where;
        $strSql .= " )T1 INNER JOIN  DB_SOPORTE.INFO_ESTADO_INSTALACION IEI ON T1.ESTADO_INSTALACION_ID=IEI.ID_ESTADO_INSTALACION";
        $objRsmb->addScalarResult('ESTADO', 'estado', 'string');

        $objQuery->setSQL($strSql);
        $objServicio = $objQuery->getOneOrNullResult();
        $array       = array(
                       "estado"  => $objServicio[estado]
                      );
        return $array;
    }
    
}

 