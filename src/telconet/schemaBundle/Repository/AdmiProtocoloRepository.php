<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiProtocoloRepository extends EntityRepository
{
    public function generarJsonProtocolos($estado,$start,$limit){
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getProtocolos($estado,'','');
        
        $encontrados = $this->getProtocolos($estado,$start,$limit);
//        error_log('entra');
        if ($encontrados) {
            
            $num = count($encontradosTotal);
            
            foreach ($encontrados as $entidad)
            {
                $arr_encontrados[]=array('idProtocolo' =>$entidad->getId(),
                                         'nombreProtocolo' =>trim($entidad->getNombreProtocolo()));
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
   
    public function getProtocolos($estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiProtocolo','e');
               
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

    public function generarJsonProtocolosEncontrados($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $protocolosTotal = $this->getProtocolosEncontrados($nombre,$estado,'','');
        
        $protocolos = $this->getProtocolosEncontrados($nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($protocolos) {
            
            $num = count($protocolosTotal);
            
            foreach ($protocolos as $protocolo)
            {
                $arr_encontrados[]=array('idProtocolo' =>$protocolo->getId(),
                                         'nombreProtocolo' =>trim($protocolo->getNombreProtocolo()),
                                         'estado' =>(trim($protocolo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($protocolo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($protocolo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getProtocolosEncontrados($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiProtocolo','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreProtocolo like ?1');
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
