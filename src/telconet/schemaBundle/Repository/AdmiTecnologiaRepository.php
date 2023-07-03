<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTecnologiaRepository extends EntityRepository
{
    public function generarJsonTecnologias($estado,$start,$limit){
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getTecnologias($estado,'','');
        
        $encontrados = $this->getTecnologias($estado,$start,$limit);
//        error_log('entra');
        if ($encontrados) {
            
            $num = count($encontradosTotal);
            
            foreach ($encontrados as $entidad)
            {
                $arr_encontrados[]=array('idTecnologia' =>$entidad->getId(),
                                         'nombreTecnologia' =>trim($entidad->getNombreTecnologia()));
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
   
    public function getTecnologias($estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTecnologia','e');
               
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?1');
            $qb->setParameter(1, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
}
