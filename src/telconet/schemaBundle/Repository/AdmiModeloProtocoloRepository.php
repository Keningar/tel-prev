<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiModeloProtocoloRepository extends EntityRepository
{
    public function generarJsonModeloUsuariosAcceso($idModelo,$estado,$start,$limit,$em){
        $arr_encontrados = array();
        //$em = $this->getManager('telconet_infraestructura');
        $entidadesTotal = $this->getModeloProtocolos($idModelo,$estado,'','');
        
        $entidades = $this->getModeloProtocolos($idModelo,$estado,$start,$limit);
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $protocolo = $em->find('schemaBundle:AdmiProtocolo', $entidad->getProtocoloId());
                
                $arr_encontrados[]=array('idModeloProtocolo' =>$entidad->getId(),
                                         'esPreferenciaProtocolo' =>trim($entidad->getEsPreferido()),
                                         'idProtocolo' =>trim($protocolo->getId()),
                                         'nombreProtocolo' =>trim($protocolo->getNombreProtocolo())   
                                         );
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
   
    public function getModeloProtocolos($idModelo,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiModeloProtocolo','e');
               
            
        if($idModelo!=""){
            $qb ->where( 'e.modeloElementoId = ?1');
            $qb->setParameter(1, $idModelo);
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
