<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiProveedorRedRepository extends EntityRepository
{
    public function generarJsonProveedoresRedes($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $proveedoresRedTotal = $this->getProveedoresRedes($nombre,$estado,'','');
        
        $proveedoresRedes = $this->getProveedoresRedes($nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($proveedoresRedes) {
            
            $num = count($proveedoresRedTotal);
            
            foreach ($proveedoresRedes as $proveedorRed)
            {
                $arr_encontrados[]=array('idProveedorRed' =>$proveedorRed->getId(),
                                         'nombreProveedorRed' =>trim($proveedorRed->getNombreProveedorRed()),
                                         'estado' =>(trim($proveedorRed->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($proveedorRed->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($proveedorRed->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getProveedoresRedes($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiProveedorRed','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreProveedorRed like ?1');
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
