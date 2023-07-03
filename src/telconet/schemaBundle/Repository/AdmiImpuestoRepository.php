<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiImpuestoRepository extends EntityRepository
{
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {                
                $arr_encontrados[]=array('id_impuesto' =>$data->getId(),
                                         'descripcion_impuesto' =>trim($data->getDescripcionImpuesto()),
                                         'codigo_sri' =>trim($data->getCodigoSri()),
                                         'porcentaje_impuesto' => ($data->getPorcentajeImpuesto() ? $data->getPorcentajeImpuesto() : 0) . " %",
                                         'fecha_vigencia_impuesto' => ($data->getFechaVigenciaImpuesto() ? $data->getFechaVigenciaImpuesto()->format('d M Y') : "-"),
                                         'tipo_impuesto' =>trim($data->getTipoImpuesto()),
                                         'cuenta_contable' =>trim($data->getCuentaContable()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_impuesto' => 0 , 'descripcion_impuesto' => 'Ninguno', 'codigo_sri' => 'Ninguno',
                                                        'cuenta_contable' => 'Ninguno', 'porcentaje_impuesto' => 'Ninguno', 
                                                        'tipo_impuesto' => 'Ninguno', 'fecha_vigencia_impuesto' => 'Ninguno',
                                                        'impuesto_id' => 0 , 'impuesto_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiImpuesto','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionImpuesto) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function findTodosImpuestosPorEstado($estado)
	{
		$query = $this->_em->createQuery("SELECT ai
				FROM 
						schemaBundle:AdmiImpuesto ai
				WHERE 
						ai.estado='".$estado."'");
		$datos=$query->getResult();
		return $datos;
	}


    /**
     * getImpuestosByCriterios
     *
     * Método que retorna los impuestos de la empresa dependiendo de los criterios enviados por el usuario.                                    
     *      
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'strTipoImpuesto', 'boolDocumentoFinancieroImp', 'intDetalleDocId', 'intIdPais' ]
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-06-2016 - Se añaden los parámetros para encontrar los impuestos relacionados al detalle de una factura
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 21-11-2016 - Se añade validación para que retorne información de la tabla 'DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP' cuando
     *                           se busca los impuestos de una factura para crear una Nota de Crédito con Valor original.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 28-06-2017 - Se agrega el parametros en $arrayParametros: 
     *     intPaisId => pais al que pertenecen los impuestos
     *     strEstado => estado del impuesto
     */
    public function getImpuestosByCriterios($arrayParametros)
    {
        $arrayResultados = array();

        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();

        $strSelect      = "SELECT ai ";
        $strSelectCount = "SELECT COUNT ( ai.id ) ";
        $strFrom        = "FROM schemaBundle:AdmiImpuesto ai ";
        $strWhere       = "WHERE ai.id IS NOT NULL ";
        $strOrderBy     = "ORDER BY ai.id ";

        if ( !isset($arrayParametros['intIdPais']) && $arrayParametros['intIdPais'] > 0 )
        {
            $strWhere .= 'AND ai.paisId = :intIdPais ';

            $query->setParameter('intIdPais',      $arrayParametros['intIdPais']);
            $queryCount->setParameter('intIdPais', $arrayParametros['intIdPais']);
        }// ( !isset($arrayParametros['intIdPais']) && $arrayParametros['intIdPais'] > 0 )

        if( !empty($arrayParametros['strTipoImpuesto']) )
        {
            $strWhere .= 'AND ai.tipoImpuesto = :strTipoImpuesto ';

            $query->setParameter('strTipoImpuesto',      trim($arrayParametros['strTipoImpuesto']));
            $queryCount->setParameter('strTipoImpuesto', trim($arrayParametros['strTipoImpuesto']));
        }

        if( !empty($arrayParametros['intPrioridad']) )
        {
            $strWhere .= 'AND ai.prioridad = :intPrioridad ';

            $query->setParameter('intPrioridad',      trim($arrayParametros['intPrioridad']));
            $queryCount->setParameter('intPrioridad', trim($arrayParametros['intPrioridad']));
        }

        if( !empty($arrayParametros['intPaisId']) )
        {
            $strWhere .= 'AND ai.paisId = :intPaisId ';

            $query->setParameter('intPaisId',      trim($arrayParametros['intPaisId']));
            $queryCount->setParameter('intPaisId', trim($arrayParametros['intPaisId']));
        }

        if( !empty($arrayParametros['strEstado']) )
        {
            $strWhere .= 'AND ai.estado = :strEstado ';

            $query->setParameter('strEstado',      trim($arrayParametros['strEstado']));
            $queryCount->setParameter('strEstado', trim($arrayParametros['strEstado']));
        }
        
        if( !empty($arrayParametros['boolDocumentoFinancieroImp']) )
        {
            if( !empty($arrayParametros['intDetalleDocId']) )
            {
                if( isset($arrayParametros['booleanValorOriginal']) && !empty($arrayParametros['booleanValorOriginal']) 
                    && $arrayParametros['booleanValorOriginal'] )
                {
                    $strSelect      = "SELECT idfi ";
                    $strSelectCount = "SELECT COUNT ( idfi.id ) ";
                }
                
                $strFrom  .= ", schemaBundle:InfoDocumentoFinancieroImp idfi ";
                $strWhere .= "AND idfi.impuestoId = ai.id
                              AND idfi.detalleDocId = :intDetalleDocId ";
                
                $query->setParameter('intDetalleDocId',      trim($arrayParametros['intDetalleDocId']));
                $queryCount->setParameter('intDetalleDocId', trim($arrayParametros['intDetalleDocId']));
            }
        }
        

        $strSql      = $strSelect.$strFrom.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;

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
     * getJSONImpuestosByCriterios
     *
     * Método que retornará los impuestos dependiendo de los criterios ingresados por el usuario en formato JSON                               
     *
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'strTipoImpuesto' ]
     * @return array $arrayResultados [ 'registros', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-06-2016
     */
    public function getJSONImpuestosByCriterios( $arrayParametros )
    {
        $arrayEncontrados   = array();
        $arrayResultado     = $this->getImpuestosByCriterios($arrayParametros);
        $arrayRegistros     = $arrayResultado['registros'];
        $intTotal           = $arrayResultado['total'];

        if( $arrayRegistros )
        {
            foreach( $arrayRegistros as $objRegistro )
            {
                $arrayItem                              = array();
                $arrayItem['intIdImpuesto']             = $objRegistro->getId();
                $arrayItem['strCodigoSri']              = $objRegistro->getCodigoSri();
                $arrayItem['strDescripcionImpuesto']    = $objRegistro->getDescripcionImpuesto();
                $arrayItem['strTipoImpuesto']           = $objRegistro->getTipoImpuesto();
                $arrayItem['strEstado']                 = $objRegistro->getEstado();
                $arrayItem['strPorcentajeImpuesto']     = $objRegistro->getPorcentajeImpuesto();
                
                $arrayEncontrados[] = $arrayItem;
            }//foreach( $arrayInfoElementos as $objParametroDet )
        }//( $arrayInfoElementos )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
}
