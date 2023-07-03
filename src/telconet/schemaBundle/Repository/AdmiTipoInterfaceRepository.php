<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoInterfaceRepository extends EntityRepository
{
    public function generarJsonTiposInterfaces($nombre,$conectorInterface,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $tiposInterfacesTotal = $this->getTiposInterfaces($nombre,$conectorInterface,$estado,'','');
        
        $tiposInterfaces = $this->getTiposInterfaces($nombre,$conectorInterface,$estado,$start,$limit);
//        error_log('entra');
        if ($tiposInterfaces) {
            
            $num = count($tiposInterfacesTotal);
            
            foreach ($tiposInterfaces as $tipoInterface)
            {
                $arr_encontrados[]=array('idTipoInterface' =>$tipoInterface->getId(),
                                         'nombreTipoInterface' =>trim($tipoInterface->getNombreTipoInterface()),
                                         'conectorInterface' =>trim($tipoInterface->getConectorInterfaceId()->getNombreConectorInterface()),
                                         'estado' =>(trim($tipoInterface->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoInterface->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoInterface->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getTiposInterfaces($nombre,$conectorInterface,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoInterface','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreTipoInterface like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($conectorInterface!=""){
            $qb ->where( 'e.conectorInterfaceId = ?1');
            $qb->setParameter(1, $conectorInterface);
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
