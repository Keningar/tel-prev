<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiSnmpRepository extends EntityRepository
{
    public function generarJsonSnmps($comunidad,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $snmpsTotal = $this->getSmps($comunidad,$estado,'','');
        
        $snmps = $this->getSmps($comunidad,$estado,$start,$limit);
//        error_log('entra');
        if ($snmps) {
            
            $num = count($snmpsTotal);
            
            foreach ($snmps as $snmp)
            {
                $arr_encontrados[]=array('idSnmp' =>$snmp->getId(),
                                         'snmpComunidad' =>trim($snmp->getSnmpCommunity()),
                                         'estado' =>(trim($snmp->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($snmp->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($snmp->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getSmps($comunidad,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiSnmp','e');
               
            
        if($comunidad!=""){
            $qb ->where( 'e.snmpCommunity = ?1');
            $qb->setParameter(1, $comunidad);
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
