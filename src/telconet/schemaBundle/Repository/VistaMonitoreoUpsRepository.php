<?php

namespace telconet\schemaBundle\Repository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class VistaMonitoreoUpsRepository extends BaseRepository
{
    /**
     * getElementosMonitoreoUpsByCriterios
     *
     * Método que retorna los elementos ups con la información correspondiente para el monitoreo.                                    
     *
     * @param array $arrayParametros ['intInicio', 'intLimite', 'criterios' => array( 'strNombreNodo' , 'strIpsUps', 'strMarca', 'strRegion', 
     *                                                                                'strProvincia', 'strCiudad', 'arrayEstado', 'arraySeveridad' )]
     * 
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 24-02-2016
     */
    public function getElementosMonitoreoUpsByCriterios($arrayParametros)
    {
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT vmu ";
        $strSelectCount = "SELECT COUNT ( vmu.id ) ";
        $strFrom        = "FROM schemaBundle:VistaMonitoreoUps vmu "; 
        $strWhere       = "WHERE vmu.tipo IS NOT NULL ";
        
        $arrayCriterios = $arrayParametros['criterios'];
        
        if( !empty($arrayCriterios) )
        {
            if( !empty($arrayCriterios['strNombreNodo']) )
            {
                $strWhere .= 'AND vmu.nombreNodo LIKE :strNombreNodo ';

                $query->setParameter('strNombreNodo',      '%'.trim($arrayCriterios['strNombreNodo']).'%');
                $queryCount->setParameter('strNombreNodo', '%'.trim($arrayCriterios['strNombreNodo']).'%');
            }
            
            
            if( !empty($arrayCriterios['strIpsUps']) )
            {
                $strWhere .= 'AND vmu.ipUps = :strIpsUps ';

                $query->setParameter('strIpsUps',      trim($arrayCriterios['strIpsUps']));
                $queryCount->setParameter('strIpsUps', trim($arrayCriterios['strIpsUps']));
            }
            
            
            if( !empty($arrayCriterios['strMarca']) )
            {
                $strWhere .= 'AND vmu.tipo = :strMarca ';

                $query->setParameter('strMarca',      trim($arrayCriterios['strMarca']));
                $queryCount->setParameter('strMarca', trim($arrayCriterios['strMarca']));
            }
            
            
            if( !empty($arrayCriterios['strRegion']) )
            {
                $strWhere .= 'AND vmu.region = :strRegion ';

                $query->setParameter('strRegion',      trim($arrayCriterios['strRegion']));
                $queryCount->setParameter('strRegion', trim($arrayCriterios['strRegion']));
            }
            
            
            if( !empty($arrayCriterios['strProvincia']) )
            {
                $strWhere .= 'AND vmu.provincia = :strProvincia ';

                $query->setParameter('strProvincia',      trim($arrayCriterios['strProvincia']));
                $queryCount->setParameter('strProvincia', trim($arrayCriterios['strProvincia']));
            }
            
            
            if( !empty($arrayCriterios['strCiudad']) )
            {
                $strWhere .= 'AND vmu.ciudad = :strCiudad ';

                $query->setParameter('strCiudad',      trim($arrayCriterios['strCiudad']));
                $queryCount->setParameter('strCiudad', trim($arrayCriterios['strCiudad']));
            }
            
            
            if( !empty($arrayCriterios['arrayEstado']) )
            {
                $strWhere .= 'AND vmu.estadoAlerta IN (:arrayEstado) ';

                $query->setParameter('arrayEstado',      array_values($arrayCriterios['arrayEstado']));
                $queryCount->setParameter('arrayEstado', array_values($arrayCriterios['arrayEstado']));
            }
            
            
            if( !empty($arrayCriterios['arraySeveridad']) )
            {
                $strWhere .= 'AND vmu.severidad IN (:arraySeveridad) ';

                $query->setParameter('arraySeveridad',      array_values($arrayCriterios['arraySeveridad']));
                $queryCount->setParameter('arraySeveridad', array_values($arrayCriterios['arraySeveridad']));
            }
        }
        
        
        $strSql      = $strSelect.$strFrom.$strWhere;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;
        
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
    