<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiEscalabilidadProcesoRepository extends EntityRepository
{
    public function generarJson($em_general, $nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                $nombreRol = "";
                if($data->getRolId())
                {    
                    $objRol = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($data->getRolId());
                    $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
                }                        
                        
                $arr_encontrados[]=array('id_escalabilidad' =>$data->getId(),
                                         'nombre_proceso' => trim($data->getProcesoId() ? $data->getProcesoId()->getNombreProceso() : "-" ),
                                         'nombre_rol' => trim($nombreRol),
                                         'orden' =>($data->getOrdenEscalabilidad() ? $data->getOrdenEscalabilidad() : "1"),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_escalabilidad' => 0 , 'nombre_proceso' => 'Ninguno', 'nombre_rol' => 'Ninguno', 'orden' => '0', 'escalabilidad_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($nombreProceso,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiEscalabilidadProceso','sim');
           
        $boolBusqueda = false;  
        if($nombreProceso!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreProceso) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombreProceso.'%');
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
}
