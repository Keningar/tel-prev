<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

class InfoTransaccionesRepository extends EntityRepository
{
    /**
     * getTransaccionesByCriterios
     *
     * Método que retorna las transacciones de la empresa dependiendo de los criterios enviados por el usuario.                                    
     *      
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'criterios' => ('tipoTransaccion', 'empresa', 'estadosTransacciones', 'nombreModulo', 
     *                                                                          'nombreAccion', 'estadosModulo', 'estadosAcciones', 'feInicial', 
     *                                                                          'feFinal') ]
     * 
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 
     * @since 18-03-2016
     */
    public function getTransaccionesByCriterios($arrayParametros)
    {
        $arrayResultados  = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = "SELECT it ";
        $strSelectCount = "SELECT COUNT ( it.id ) ";
        $strFrom        = "FROM schemaBundle:InfoTransacciones it "; 
        $strJoin        = "JOIN it.relacionSistemaId srs
                           JOIN srs.moduloId sm
                           JOIN srs.accionId sa "; 
        $strWhere       = "WHERE it.id IS NOT NULL ";
        $strOrderBy     = "ORDER BY it.feCreacion DESC ";
        
        
        $arrayCriterios = $arrayParametros['criterios'];
        
        if( !empty($arrayCriterios) )
        {
            if( !empty($arrayCriterios['tipoTransaccion']) )
            {
                $strWhere .= 'AND it.tipoTransaccion = :tipoTransaccion ';

                $query->setParameter('tipoTransaccion',      trim($arrayCriterios['tipoTransaccion']));
                $queryCount->setParameter('tipoTransaccion', trim($arrayCriterios['tipoTransaccion']));
            }
            
            
            if( !empty($arrayCriterios['empresa']) )
            {
                $strWhere .= 'AND it.empresaId = :empresa ';

                $query->setParameter('empresa',      trim($arrayCriterios['empresa']));
                $queryCount->setParameter('empresa', trim($arrayCriterios['empresa']));
            }
            
            
            if( !empty($arrayCriterios['estadosTransacciones']) )
            {
                $strWhere .= 'AND it.estado IN (:estadosTransacciones) ';

                $query->setParameter('estadosTransacciones',      array_values($arrayCriterios['estadosTransacciones']));
                $queryCount->setParameter('estadosTransacciones', array_values($arrayCriterios['estadosTransacciones']));
            }
            
            
            if( !empty($arrayCriterios['nombreModulo']) )
            {
                $strWhere .= 'AND sm.nombreModulo LIKE :nombreModulo ';

                $query->setParameter('nombreModulo',      '%'.trim($arrayCriterios['nombreModulo']).'%');
                $queryCount->setParameter('nombreModulo', '%'.trim($arrayCriterios['nombreModulo']).'%');
            }
            
            
            if( !empty($arrayCriterios['nombreAccion']) )
            {
                $strWhere .= 'AND sa.nombreAccion LIKE :nombreAccion ';

                $query->setParameter('nombreAccion',      '%'.trim($arrayCriterios['nombreAccion']).'%');
                $queryCount->setParameter('nombreAccion', '%'.trim($arrayCriterios['nombreAccion']).'%');
            }
            
            
            if( !empty($arrayCriterios['estadosModulo']) )
            {
                $strWhere .= 'AND sm.estado IN (:estadosModulo) ';

                $query->setParameter('estadosModulo',      array_values($arrayCriterios['estadosModulo']));
                $queryCount->setParameter('estadosModulo', array_values($arrayCriterios['estadosModulo']));
            }
            
            
            if( !empty($arrayCriterios['estadosAcciones']) )
            {
                $strWhere .= 'AND sa.estado IN (:estadosAcciones) ';

                $query->setParameter('estadosAcciones',      array_values($arrayCriterios['estadosAcciones']));
                $queryCount->setParameter('estadosAcciones', array_values($arrayCriterios['estadosAcciones']));
            }
            
            
            if( !empty($arrayCriterios['feInicial']) )
            {
                $strWhere .= 'AND it.feCreacion >= :feInicial ';

                $query->setParameter('feInicial',      $arrayCriterios['feInicial']);
                $queryCount->setParameter('feInicial', $arrayCriterios['feInicial']);
            }
            
            
            if( !empty($arrayCriterios['feFinal']) )
            {
                $strWhere .= 'AND it.feCreacion < :feFinal ';

                $query->setParameter('feFinal',      $arrayCriterios['feFinal']);
                $queryCount->setParameter('feFinal', $arrayCriterios['feFinal']);
            }
        }
        
        
        $strSql      = $strSelect.$strFrom.$strJoin.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strJoin.$strWhere;
        
        $query->setDQL($strSql);
        $queryCount->setDQL($strSqlCount);
        
        if( !empty($arrayParametros['intStart']) )
        {
            $query->setFirstResult($arrayParametros['intStart']);
        }
        
        if( !empty($arrayParametros['intLimit']) )
        {
            $query->setMaxResults($arrayParametros['intLimit']);
        }
        
            
        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
    }
    
    
    /**
     * getJsonTransaccionesByCriterios
     *
     * Método que retorna las transacciones de la empresa dependiendo de los criterios enviados por el usuario en formato JSON.
     *      
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'strNombreReporte', 'route', 'criterios' => ('tipoTransaccion', 'empresa', 
     *                                                                                                       'estadosTransacciones', 'nombreModulo', 
     *                                                                                                       'nombreAccion', 'estadosModulo', 
     *                                                                                                       'estadosAcciones', 'feInicial', 
     *                                                                                                       'feFinal') ]
     * 
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 
     * @since 18-03-2016
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.1 Se descargan los archivos desde el servidor nfs
     * @since 25-02-2022 
     */
    public function getJsonTransaccionesByCriterios($arrayParametros)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getTransaccionesByCriterios($arrayParametros);
        $arrayInfoTransacciones = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayInfoTransacciones )
        {
            foreach( $arrayInfoTransacciones as $objInfoTransaccion )
            {
                $intIdTransaccion   = $objInfoTransaccion->getId();
                $objRelacionSistema = $objInfoTransaccion->getRelacionSistemaId();
                
                $arrayItem                         = array();
                $arrayItem['intIdTransaccion']     = $intIdTransaccion;
                $strUrl                            = $objInfoTransaccion->getNombreTransaccion();                
                if( strpos($strUrl, "/") !== false )
                {
                    $strNombreTransaccion = basename($strUrl);
                } 
                else
                {
                    $strNombreTransaccion = $strUrl;
                }
                $arrayItem['strNombreTransaccion'] = $strNombreTransaccion;
                $arrayItem['strTipoTransaccion']   = $objInfoTransaccion->getTipoTransaccion();
                $arrayItem['strEstado']            = $objInfoTransaccion->getEstado();
                $arrayItem['strEmpresa']           = $objInfoTransaccion->getEmpresaId();
                $arrayItem['strUsuarioCreacion']   = $objInfoTransaccion->getUsrCreacion();
                $arrayItem['intIdModulo']          = $objRelacionSistema ? $objRelacionSistema->getModuloId()->getId() : '';
                $arrayItem['strNombreModulo']      = $objRelacionSistema ? $objRelacionSistema->getModuloId()->getNombreModulo() : '';
                $arrayItem['intIdAccion']          = $objRelacionSistema ? $objRelacionSistema->getAccionId()->getId() : '';
                $arrayItem['strNombreAccion']      = $objRelacionSistema ? $objRelacionSistema->getAccionId()->getNombreAccion() : '';
                $arrayItem['strFechaCreacion']     = $objInfoTransaccion->getFeCreacion() 
                                                     ? $objInfoTransaccion->getFeCreacion()->format('d M Y H:i:s') : '';
                
                if( !empty($arrayParametros['strNombreReporte']) )
                {
                    if( strpos($strUrl, "/") !== false )
                    {
                        $arrayItem['strUrlDescargar'] = $strUrl;
                    }
                    else
                    {
                    $router = $arrayParametros['route'];

                    $arrayItem['strUrlDescargar'] = $router->generate( 'reportes_descargar_reporte_buro', 
                                                                array( 'strNombreArchivo' => $arrayItem['strNombreTransaccion'] ));
                    }
                }
                
                $arrayEncontrados[] = $arrayItem;
            }//foreach( $arrayInfoTransacciones as $objInfoTransaccion )
        }//( $arrayInfoTransacciones )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData; 
    }
}


