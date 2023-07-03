<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * AdmiNumeracionOficinaRepository.
 *
 * Repositorio que se encargará de administrar las funcionalidades adicionales que se relacionen con la entidad AdmiNumeracionOficina
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 02-12-2015
 */
class AdmiNumeracionOficinaRepository extends EntityRepository
{
    /**
     * getNumeracionByCriterios
     *
     * Metodo encargado de obtener la numeración correspondiente para la creación de la factura de acuerdo a los criterios ingresados
     * por el usuario.
     * 
     * Costo del query: 3
     *
     * @param array $arrayParametros  ['estadoActivo', 'oficina', 'empresa', 'codigoNumeracion', 'inicio', 'limite']
     *          
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 02-12-2015
     */  
    public function getNumeracionByCriterios( $arrayParametros )
    {
        $arrayResultados = array();
        $intLimite       = 0;
        $objRegistros    = null;

        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT anumo ";
        $strSelectCount = "SELECT COUNT(DISTINCT anumo.id) ";
        $strFrom        = "FROM schemaBundle:AdmiNumeracionOficina anumo ";
        $strJoin        = "JOIN anumo.oficinaId iog 
                           JOIN anumo.numeracionId an
                           JOIN iog.empresaId ieg ";
        $strWhere       = "WHERE anumo.estado = :estadoActivo
                             AND an.estado = :estadoActivo 
                             AND ieg.estado = :estadoActivo ";
        
        $query->setParameter("estadoActivo",      $arrayParametros['estadoActivo']);
        $queryCount->setParameter("estadoActivo", $arrayParametros['estadoActivo']);
        
        
        if( isset($arrayParametros['oficina']) )
        {
            if($arrayParametros['oficina'])
            {
                $strWhere .= "AND iog.id = :oficina ";
                
                $query->setParameter("oficina",      $arrayParametros['oficina']);
                $queryCount->setParameter("oficina", $arrayParametros['oficina']);
            }
        }
        
        
        if( isset($arrayParametros['empresa']) )
        {
            if($arrayParametros['empresa'])
            {
                $strWhere .= "AND ieg.id = :empresa ";
                
                $query->setParameter("empresa",      $arrayParametros['empresa']);
                $queryCount->setParameter("empresa", $arrayParametros['empresa']);
            }
        }
        
        
        if( isset($arrayParametros['codigoNumeracion']) )
        {
            if($arrayParametros['codigoNumeracion'])
            {
                $strWhere .= "AND an.codigo = :codigoNumeracion ";
                
                $query->setParameter("codigoNumeracion",      $arrayParametros['codigoNumeracion']);
                $queryCount->setParameter("codigoNumeracion", $arrayParametros['codigoNumeracion']);
            }
        }
        
        $strDql      = $strSelect.$strFrom.$strJoin.$strWhere;
        $strDqlCount = $strSelectCount.$strFrom.$strJoin.$strWhere;
        
        $query->setDQL($strDql);
        $queryCount->setDQL($strDqlCount);

        if( isset($arrayParametros['inicio']) )
        {
            if($arrayParametros['inicio'])
            {
                $query->setFirstResult($arrayParametros['inicio']);
            }
        }
        
        if( isset($arrayParametros['limite']) )
        {
            $intLimite = $arrayParametros['limite'];
            
            if($intLimite > 1)
            {
                $query->setMaxResults($arrayParametros['limite']);
            }
        }

        
        if( $intLimite == 1 )
        {
            $objRegistros = $query->getOneOrNullResult();
        }
        else
        {
            $objRegistros = $query->getResult();
        }
        
        $arrayResultados['registros'] = $objRegistros;
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
        
    }
}
