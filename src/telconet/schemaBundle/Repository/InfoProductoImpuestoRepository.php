<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoProductoImpuestoRepository extends EntityRepository
{
    public function findPorEstado($productoid,$estado)
    {
        $query = $this->_em->createQuery("SELECT ipi
            FROM 
            schemaBundle:InfoProductoImpuesto ipi
            WHERE 
            ipi.estado='".$estado."' AND ipi.productoId=".$productoid);
            //echo $query->getSQL(); die;
            $datos = $query->getResult();

            return $datos;
    }
    
    
    /**
     * getInfoImpuestoByCriterios.
     *
     * Función que retorna los impuestos asociados a un producto dependiendo de los criterios enviados por los usuarios.
     * 
     * @param $arrayParametros ['intInicio', 'intLimite', 'strEstado', 'intIdProducto', 'intPrioridad', 'intIdPais']
     * @return $arrayResultados ['registros', 'total']
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-06-2016
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 28-06-2017 - Se agrega el parámetro 'intIdPais' para que retorne los impuestos por país asociados a los productos
     * Costo Query: 4
     */
    public function getInfoImpuestoByCriterios($arrayParametros)
    {
        $arrayResultados  = array();
        $query            = $this->_em->createQuery();
        $queryCount       = $this->_em->createQuery();
        
        $strSelect      = "SELECT ipi ";
        $strSelectCount = "SELECT COUNT(ipi.id) ";
        $strFrom        = "FROM schemaBundle:InfoProductoImpuesto ipi ";
        $strJoin        = "JOIN ipi.productoId ap
                           JOIN ipi.impuestoId ai ";
        $strWhere       = "WHERE ipi.id IS NOT NULL ";
        $strOrderBy     = "ORDER BY ipi.id ";

        if ( !isset($arrayParametros['intIdPais']) && $arrayParametros['intIdPais'] > 0 )
        {
            $strWhere .= 'AND ai.paisId = :intIdPais ';

            $query->setParameter('intIdPais',      $arrayParametros['intIdPais']);
            $queryCount->setParameter('intIdPais', $arrayParametros['intIdPais']);
        }
        
        if( !empty($arrayParametros['strEstado']) )
        {
            $strWhere .= "AND ipi.estado = :strEstado ";
            $query->setParameter('strEstado',       $arrayParametros['strEstado']);
            $queryCount->setParameter('strEstado',  $arrayParametros['strEstado']);
        }
        
        if( !empty($arrayParametros['intIdProducto']) )
        {
            $strWhere .= "AND ap.id = :intIdProducto ";
            $query->setParameter('intIdProducto',       $arrayParametros['intIdProducto']);
            $queryCount->setParameter('intIdProducto',  $arrayParametros['intIdProducto']);
        }
        
        if( !empty($arrayParametros['intPrioridad']) )
        {
            $strWhere .= "AND ai.prioridad = :intPrioridad ";
            $query->setParameter('intPrioridad',        $arrayParametros['intPrioridad']);
            $queryCount->setParameter('intPrioridad',   $arrayParametros['intPrioridad']);
        }
        
        $strSql      = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strJoin.$strWhere;
        
        $query->setDQL($strSql);
        $queryCount->setDQL($strSqlCount);

        if( !empty($arrayParametros['intInicio']) )
        {
            $query->setFirstResult($arrayParametros['intInicio']);
        }
        
        if( !empty($arrayParametros['intLimite']) )
        {
            $query->setMaxResults($arrayParametros['intLimite']);
        }
        
        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();

        return $arrayResultados;
    }
}
