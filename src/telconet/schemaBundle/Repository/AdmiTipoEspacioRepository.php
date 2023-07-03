<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoEspacioRepository extends EntityRepository
{
    public function generarJsonTiposEspacios($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $tiposEspaciosTotal = $this->getTiposEspacios($nombre,$estado,'','');
        
        $tiposEspacios = $this->getTiposEspacios($nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($tiposEspacios) {
            
            $num = count($tiposEspaciosTotal);
            
            foreach ($tiposEspacios as $tipoEspacio)
            {
                $arr_encontrados[]=array('idTipoEspacio' =>$tipoEspacio->getId(),
                                         'nombreTipoEspacio' =>trim($tipoEspacio->getNombreTipoEspacio()),
                                         'estado' =>(trim($tipoEspacio->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoEspacio->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoEspacio->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getTiposEspacios($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoEspacio','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreTipoEspacio like ?1');
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
