<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoNegocioRepository extends EntityRepository
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
                        
                $arr_encontrados[]=array('id_tipo_negocio' =>$data->getId(),
                                         'codigo_tipo_negocio' =>trim($data->getCodigoTipoNegocio()),
                                         'nombre_tipo_negocio' =>trim($data->getNombreTipoNegocio()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_negocio' => 0 , 'codigo_tipo_negocio' => 'Ninguno', 'nombre_tipo_negocio' => 'Ninguno', 'tipo_negocio_id' => 0 , 'tipo_negocio_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiTipoNegocio','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreTipoNegocio) like LOWER(?1)');
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
    
    public function findTiposNegocioActivos()
	{
		return $qb =$this->createQueryBuilder("t")
		->select("a")
		->from('schemaBundle:AdmiTipoNegocio a','')->where("a.estado='Activo'");
	}    
    
	/**
	 * Devuelve un query builder para obtener los tipos de negocio activos de la empresa dada
	 * @param string $codEmpresa
	 * @return \Doctrine\ORM\QueryBuilder
	 */
    public function findTiposNegocioActivosPorEmpresa($codEmpresa)
    {
        return $qb =$this->createQueryBuilder("t")
                ->select("a")
                ->from('schemaBundle:AdmiTipoNegocio a','')
                ->where("a.estado = 'Activo'")
                ->andWhere("a.empresaCod = :codEmpresa")
                ->setParameter('codEmpresa', $codEmpresa);
    }
   
   /**
    * Funcion que devuelve los tipos de negocio por empresa
    * Consideraciones: Se toma solo los tipos de negocio Activos
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param string $codEmpresa     
    * @version 1.0 23-05-2014
    * @return object
    */
     public function findTiposNegocioPorEmpresa($codEmpresa)
    {
        $query = $this->_em->createQuery("select a from		
                 schemaBundle:AdmiTipoNegocio a
                 where a.estado = :estado
                 and a.empresaCod = :codEmpresa");
                
        $query->setParameter('estado', 'Activo');
        $query->setParameter('codEmpresa', $codEmpresa);
        $datos = $query->getResult();		
        return $datos;
   }
}
