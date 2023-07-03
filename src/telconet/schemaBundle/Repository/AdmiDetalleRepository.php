<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiDetalleRepository extends EntityRepository
{
    public function generarJsonDetalles($nombreDetalle,$tipo,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getDetalles($nombreDetalle,$tipo,$estado,'','');
        
        $encontrados = $this->getDetalles($nombreDetalle,$tipo,$estado,$start,$limit);
//        error_log('entra');
        if ($encontrados) {
            
            $num = count($encontradosTotal);
            
            foreach ($encontrados as $encontrado)
            {
                $arr_encontrados[]=array('idDetalle' =>$encontrado->getId(),
                                         'nombreDetalle' =>trim($encontrado->getNombreDetalle()),
                                         'tipo' =>trim($encontrado->getTipo()),
                                         'estado' =>(trim($encontrado->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($encontrado->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($encontrado->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getDetalles($nombreDetalle,$tipo,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiDetalle','e');
               
            
        if($nombreDetalle!=""){
            $qb ->where( 'e.nombreDetalle = ?1');
            $qb->setParameter(1, $nombre);
        }
        if($tipo!=""){
            $qb ->where( 'e.tipo = ?3');
            $qb->setParameter(3, $tipo);
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
