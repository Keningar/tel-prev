<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiConectorInterfaceRepository extends EntityRepository
{
    public function generarJsonConectoresInterfaces($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $conectoresTotal = $this->getConectoresInterfaces($nombre,$estado,'','');
        
        $conectores = $this->getConectoresInterfaces($nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($conectores) {
            
            $num = count($conectoresTotal);
            
            foreach ($conectores as $conector)
            {
                $arr_encontrados[]=array('idConectorInterface' =>$conector->getId(),
                                         'nombreConectorInterface' =>trim($conector->getNombreConectorInterface()),
                                         'estado' =>(trim($conector->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($conector->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($conector->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
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
   
    public function getConectoresInterfaces($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiConectorInterface','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreConectorInterface like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
}
