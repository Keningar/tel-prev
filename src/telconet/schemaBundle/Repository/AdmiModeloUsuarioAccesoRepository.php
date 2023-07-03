<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiModeloUsuarioAccesoRepository extends EntityRepository
{
    public function generarJsonModeloUsuariosAcceso($idModelo,$estado,$start,$limit,$em){
        $arr_encontrados = array();
        //$em = $this->getManager('telconet_infraestructura');
        $entidadesTotal = $this->getModeloUsuariosAcceso($idModelo,$estado,'','');
        
        $entidades = $this->getModeloUsuariosAcceso($idModelo,$estado,$start,$limit);
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $usuarioAcceso = $em->find('schemaBundle:AdmiUsuarioAcceso', $entidad->getUsuarioAccesoId());
                
                $arr_encontrados[]=array('idModeloUsuarioAcceso' =>$entidad->getId(),
                                         'esPreferenciaUsuario' =>trim($entidad->getEsPreferencia()),
                                         'idUsuarioAcceso' =>trim($usuarioAcceso->getId()),
                                         'nombreUsuarioAcceso' =>trim($usuarioAcceso->getNombreUsuarioAcceso())   
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
   
    public function getModeloUsuariosAcceso($idModelo,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiModeloUsuarioAcceso','e');
               
            
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
