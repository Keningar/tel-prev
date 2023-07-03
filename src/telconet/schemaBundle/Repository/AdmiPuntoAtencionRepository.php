<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiPuntoAtencionRepository extends BaseRepository
{
    
    
    /**
     * Método encargado obtener el listado de puntos de atencion
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 - 26-04-2021
     *
     * @param  String $strEmpresaCod
     * @return Array $arrayResultado
     */
    public function getPuntosAtencion($strEmpresaCod)
    {
        
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT ID_PUNTO_ATENCION,NOMBRE_PUNTO_ATENCION,ESTADO,
                                   USR_CREACION,EMPRESA_COD
                              FROM DB_COMERCIAL.ADMI_PUNTO_ATENCION 
                              WHERE ESTADO IN ('Activo','Modificado')
                              AND EMPRESA_COD = :empresaCod ";
        
        
        $objQuery->setParameter("empresaCod",       $strEmpresaCod);
        $objRsm->addScalarResult('ID_PUNTO_ATENCION', 'idPuntoAtencion', 'string');
        $objRsm->addScalarResult('NOMBRE_PUNTO_ATENCION', 'nombrePuntoAtencion', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('EMPRESA_COD', 'empresaCod', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;

        
    }
    
    /**
     * Método encargado de generar el json de punto de atención
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 - 26-04-2021
     *
     * @param  array $arrayParametros
     * @return Array $objResultado
     */
    public function generarJsonPuntosAtencion($arrayParametros)
    {
        
        $objResultado = "";   
        
        $arrayPuntosAtencion    = $this->getListadoPuntosAtencion($arrayParametros);
        $arrayParametros['intStart'] = "";
        $arrayParametros['intLimit'] = "";
        $arrayPuntosTotal = $this->getListadoPuntosAtencion($arrayParametros);
        
        if($arrayPuntosAtencion)
        {
            $intCantidad = count($arrayPuntosTotal);
            
            foreach ($arrayPuntosAtencion as $objPuntoAtencion)
            {
                $arrayEncontrados[] = array('idPuntoAtencion'     => $objPuntoAtencion->getId(),
                                            'nombrePuntoAtencion' => $objPuntoAtencion->getNombrePuntoAtencion(),
                                            'estado'              => $objPuntoAtencion->getEstado(),
                                            'usrCreacion'         => $objPuntoAtencion->getUsrCreacion());
            }
            
            
            $arrayDatos = json_encode($arrayEncontrados);
            $objResultado  = '{"total":"'.$intCantidad.'","encontrados":'.$arrayDatos.'}';
            
        }
        
        
        return $objResultado;
    }
    
    
    /**
     * Método encargado de consultar los puntos de atención
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 - 26-04-2021
     *
     * @param  Array $arrayParametros
     * @return Array $objQuery
     */
    public function getListadoPuntosAtencion($arrayParametros)
    {
        $intStart      = $arrayParametros["intStart"];
        $intLimit      = $arrayParametros["intLimit"];
        $strEstado     = $arrayParametros["strEstado"];
        $strNombres    = $arrayParametros["strNombres"];
        $strEmpresaCod = $arrayParametros["strEmpresaCod"];
        
        $strSql = $this->_em->createQueryBuilder();
            $strSql->select('sim')
               ->from('schemaBundle:AdmiPuntoAtencion','sim');
        
            
        if($strNombres!="")
        {
            $strSql ->andWhere("LOWER(sim.nombrePuntoAtencion) like LOWER(:strNombres) ");
            $strSql->setParameter('strNombres', '%'.$strNombres.'%');
        }
        
        if($strEstado=="Todos")
        {
            $strSql ->andWhere("sim.estado IN (:estado) ");
            $strSql->setParameter("estado", array("Activo","Modificado"));
        }
        else
        {
            $strSql ->andWhere('sim.estado = :estado');
            $strSql->setParameter("estado", $strEstado);
        }
        
        $strSql ->andWhere('sim.empresaCod = :empresaCod');
        $strSql->setParameter("empresaCod", $strEmpresaCod);
        
        if($intStart!='')
        {
            $strSql->setFirstResult($intStart);
        }
               
        if($intLimit!='')
        {
            $strSql->setMaxResults($intLimit);
        }
            
        
        
        $objQuery = $strSql->getQuery();
        
        return $objQuery->getResult();
    }
    
}
