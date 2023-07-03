<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiOidRepository extends EntityRepository
{
    /**
     * cargarInformacionUps
     *
     * Metodo encargado de obtener toda la información de los OID existentes
     *
     * @param array   $arrayParametros[ 'nombreOid', 'idMarca', 'estado', 'inicio', 'limite' ]
     * @return string $strResultado
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para retorne la informacion adecuada de los OID
     */
    public function generarJsonOids( $arrayParametros )
    {
        $arrayEncontrados = array();
        $jsonData         = null;
        $strResultado     = "";
        
        $arrayOids = $this->getOids( $arrayParametros );

        if($arrayOids['registros']) 
        {
            foreach ($arrayOids['registros'] as $objOid)
            {
                $arrayEncontrados[] = array('idOid'         => $objOid->getId(),
                                            'marcaElemento' => trim($objOid->getMarcaElementoId()->getNombreMarcaElemento()),
                                            'nombreOid'     => trim($objOid->getNombreOid()),
                                            'oid'           => trim($objOid->getOid()),
                                            'feCreacion'    => trim($objOid->getFeCreacion()),
                                            'estado'        => (trim($objOid->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                            'action1'       => 'button-grid-show',
                                            'action2'       => (trim($objOid->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                            'action3'       => (trim($objOid->getEstado())=='Eliminado' ? 'button-grid-invisible'
                                                                                                        :'button-grid-delete'));
            }

            $jsonData = json_encode($arrayEncontrados);
        }//($arrayOids['registros']) 
        
        $strResultado = '{"total":"'.$arrayOids['total'].'","encontrados":'.$jsonData.'}';

        return $strResultado;
    }
   
    
    /**
     * getOids
     *
     * Metodo encargado de hacer la consulta con la base de datos para retornar toda la información de los OID existentes
     *
     * @param array  $arrayParametros[ 'nombreOid', 'idMarca', 'estado', 'inicio', 'limite' ]
     * @return array $arrayResultados[ 'registros', 'total' ]
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco  <efranco@telconet.ec>
     * @version 1.1 18-02-2016 - Se modifica para retorne la informacion adecuada de los OID
     */
    public function getOids( $arrayParametros )
    {
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT e ";
        $strSelectCount = "SELECT COUNT ( e.id ) ";
        $strFrom        = "FROM schemaBundle:AdmiOid e ";
        $strWhere       = "WHERE e.id IS NOT NULL ";
        $strOrderBy     = "ORDER BY e.id ";
        
        
        if( isset($arrayParametros['estado']) )
        {
            if($arrayParametros['estado'])
            {
                if($arrayParametros['estado'] == 'Todos')
                {
                    $strWhere .= 'AND e.estado <> :estado ';

                    $query->setParameter('estado',      'Eliminado');
                    $queryCount->setParameter('estado', 'Eliminado');
                }
                else
                {
                    $strWhere .= 'AND e.estado = :estado ';
                    
                    $query->setParameter('estado',      $arrayParametros['estado']);
                    $queryCount->setParameter('estado', $arrayParametros['estado']);
                }
            }
        }

        if( isset($arrayParametros['idMarca']) )
        {
            if($arrayParametros['idMarca'])
            {
                $strWhere .= 'AND e.marcaElementoId = :marca ';

                $query->setParameter('marca',      $arrayParametros['idMarca']);
                $queryCount->setParameter('marca', $arrayParametros['idMarca']);
            }  
        }
        
        
        if( isset($arrayParametros['nombreOid']) )
        {
            if($arrayParametros['nombreOid'])
            {
                $strWhere .= 'AND e.nombreOid like :nombre ';

                $query->setParameter('nombre',      '%'.$arrayParametros['nombreOid'].'%');
                $queryCount->setParameter('nombre', '%'.$arrayParametros['nombreOid'].'%');
            }  
        }
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $query->setDQL($strSql);  

        if( isset($arrayParametros['inicio']) )
        {
            if($arrayParametros['inicio'])
            {
                $query->setFirstResult($arrayParametros['inicio']);
            }  
        }
        
        if( isset($arrayParametros['limite']) )
        {
            if($arrayParametros['limite'])
            {
                $query->setMaxResults($arrayParametros['limite']);
            }
        }
        
        $arrayTmpDatos = $query->getResult();
        
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $queryCount->setDQL($strSqlCount);  
        
        $intTotal = $queryCount->getSingleScalarResult();
            
        $arrayResultados['registros'] = $arrayTmpDatos;
        $arrayResultados['total']     = $intTotal;
        
        return $arrayResultados;
    }
}
