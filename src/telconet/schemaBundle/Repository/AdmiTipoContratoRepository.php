<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoContratoRepository extends EntityRepository
{

    private $currentCodEmpresa ;

    public function setCurrentIdEmpresa($codEmpresa){
            $this->currentCodEmpresa = $codEmpresa;
    }

    public function getCurrentIdEmpresa(){
            return $this->currentCodEmpresa;
    }
	
	

    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_tipo_contrato' =>$data->getId(),
                                         'nombre_empresa' =>trim($data->getEmpresaCod()->getNombreEmpresa()),
                                         'descripcion_tipo_contrato' =>trim($data->getDescripcionTipoContrato()),
                                         'tiempo_finalizacion' => ($data->getTiempoFinalizacion() ? $data->getTiempoFinalizacion() : 0),
                                         'tiempo_alerta_finalizacion' => ($data->getTiempoAlertaFinalizacion() ? $data->getTiempoAlertaFinalizacion() : 0),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_contrato' => 0 , 'nombre_empresa' => 'Ninguno', 'descripcion_tipo_contrato' => 'Ninguno', 'tipo_contrato_id' => 0 , 'tipo_contrato_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiTipoContrato','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionTipoContrato) like LOWER(?1)');
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
    
    public function findTipoContratoXEstado($estado,$empresa)
	{
		return $qb =$this->createQueryBuilder("t")
		->select("a")
		->from('schemaBundle:AdmiTipoContrato a','')
		->where("a.estado='".$estado."' and a.empresaCod='".$empresa."'");
	}

    public function findTipoContratoPorEstadoPorEmpresa($estado, $codEmpresa)
    {
        $query = $this->_em->createQuery("SELECT a
            FROM schemaBundle:AdmiTipoContrato a
            WHERE a.estado = :estado AND
            a.empresaCod = :codEmpresa");
        $query->setParameter('estado', $estado);
        $query->setParameter('codEmpresa', $codEmpresa);
        $datos = $query->getResult();
        return $datos;	
    }
    
}
