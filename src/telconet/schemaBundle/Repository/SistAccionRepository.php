<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SistAccionRepository extends EntityRepository
{
    public function generarJsonAccion($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $accionesTotal = $this->getAcciones($nombre, $estado, '', '');
        
        
        $acciones = $this->getAcciones($nombre, $estado, $start, $limit);
 
        if ($acciones) {
            
            $num = count($accionesTotal);
            
            foreach ($acciones as $accion)
            {
                $arr_encontrados[]=array('id_accion' =>$accion->getId(),
                                         'nombre_accion' =>trim($accion->getNombreAccion()),
                                         'estado' =>(trim($accion->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($accion->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($accion->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_accion' => 0 , 'nombre_accion' => 'Ninguno','accion_id' => 0 , 'accion_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    public function getAcciones($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:SistAccion','sim');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(sim.nombreAccion) like LOWER(?1)');
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
        
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $qb->orderBy('sim.id');
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
}