<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiCaracteristicaRepository extends EntityRepository
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
                        
                $arr_encontrados[]=array('id_caracteristica' =>$data->getId(),
                                         'descripcion_caracteristica' =>trim($data->getDescripcionCaracteristica()),
                                         'tipo' =>trim($data->getTipo()),
                                         'tipoIngreso' =>trim($data->getTipoIngreso()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_caracteristica' => 0 , 'tipo' => 'Ninguno', 'descripcion_caracteristica' => 'Ninguno', 'caracteristica_id' => 0 , 'caracteristica_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    /**
     * Función que retorna el listado de caracteristicas que se muestran en la administración de las mismas.
     *
     * @version Initial - 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - 10-12-2019 - Se reporta que el grid de la administración de caracteristicas no paginea y
     *                             no realiza la busqueda solicitada.
     * @param type $nombre
     * @param type $estado
     * @param type $start
     * @param type $limit
     * @return type
     */
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiCaracteristica','sim');
            
        if($nombre!=""){
            $qb ->where( 'LOWER(sim.descripcionCaracteristica) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='')
        {
            $qb->setFirstResult($start);
        }
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

	public function findTodasPorEstado($estado)
	{
		$query = $this->_em->createQuery("SELECT ac
				FROM 
						schemaBundle:AdmiCaracteristica ac
				WHERE 
						ac.estado='".$estado."'");
		$datos=$query->getResult();
		return $datos;
	}
            public function findCaracteristicasPorTipoYEstado($tipo_caracteristica,$estado)
    {
        if($tipo_caracteristica!="")
            $query_var=" AND ac.tipo='".$tipo_caracteristica."'";
        else
            $query_var="";
            
        $query = $this->_em->createQuery("SELECT ac
		FROM 
                schemaBundle:AdmiCaracteristica ac
		WHERE 
                ac.estado = '".$estado."' ".$query_var);
        
        $datos = $query->getResult();
        return $datos;
    }

    
    /**
      * getIdCaracteristica
      *
      * Método que retorna el id de una caractarística                                   
      *      
      * @param string $strDescripcionCaracteristica descripción de la característica
      * 
      * @return integer $intIdCaracteristica
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 18-09-2015
      */
    public function getIdCaracteristicaByDescripcionCaracteristica($strDescripcionCaracteristica)
    {
        $intIdCaracteristica = 0;
        
        $entityTmpCaracteristica = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneByDescripcionCaracteristica($strDescripcionCaracteristica);
                    
        if( $entityTmpCaracteristica )
        {
            $intIdCaracteristica = $entityTmpCaracteristica->getId();
        }
        
        return $intIdCaracteristica;
    }
        
        
    /**
     * Documentación para el método 'getCaracteristicaPorDescripcionPorEstado'.
     * 
     * Método que obtiene una característica filtrada por su descripción exacta y su estado
     * 
     * @param Request $request
     * 
     * @return Entity Objeto AdmiCaracteristica
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 14-09-2015
     */
    public function getCaracteristicaPorDescripcionPorEstado($strDescripcionCaracteristica, $strEstado)
    {
        $objQuery = $this->_em->createQuery("SELECT ac
                                             FROM   schemaBundle:AdmiCaracteristica ac
                                             WHERE  ac.estado                    = :estado AND
                                                    ac.descripcionCaracteristica = :descripcionCaracteristica ");
        $objQuery->setParameter('estado', $strEstado);
        $objQuery->setParameter('descripcionCaracteristica', $strDescripcionCaracteristica);
        return $objQuery->getOneOrNullResult();
    }
    
    
    /**
    * Documentacion para la funcion getCanalRecaudacionCaracteristica
    *
    * Función que retorna el valor de la caracteristica según los valores enviados como parámetro.
    * 
    * @param mixed $arrayParametros[
    *                               'strEmpresaCod'               => Código de la empresa en sesión
    *                               'intIdCanalRecaudacion'       => Id de referencia a la tabla ADMI_CANAL_RECAUDACION
    *                               'strDescricionCaracteristica' => Rango inicial para fecha de creacion
    *                               'strEstadoCaracteristica'     => Estado de la caracteristica
    *                               ]
    *
    * @return object AdmiCanalRecaudacionCaract acrc
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 25-10-2017
    *
    */    
    public function getCanalRecaudacionCaracteristica($arrayParametros)
    {
        $objQuery = $this->_em->createQuery("SELECT acrc
                                             FROM   schemaBundle:AdmiCaracteristica         ac
                                             JOIN   schemaBundle:AdmiCanalRecaudacionCaract acrc with ac.id = acrc.caracteristicaId
                                             WHERE  ac.estado                    = :estado AND
                                                    ac.descripcionCaracteristica = :descripcionCaracteristica AND 
                                                    acrc.canalRecaudacionId      = :canalRecaudacionId  AND 
                                                    acrc.empresaCod              = :empresaCod ");
        $objQuery->setParameter('estado', $arrayParametros['strEstadoCaracteristica']);
        $objQuery->setParameter('descripcionCaracteristica', $arrayParametros['strDescricionCaracteristica']);
        $objQuery->setParameter('canalRecaudacionId', $arrayParametros['intIdCanalRecaudacion']);
        $objQuery->setParameter('empresaCod', $arrayParametros['strEmpresaCod']);
        return $objQuery->getOneOrNullResult();    
    }    
}
