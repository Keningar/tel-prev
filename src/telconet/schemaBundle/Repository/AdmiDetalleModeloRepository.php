<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiDetalleModeloRepository extends EntityRepository
{
    public function generarJsonDetallesModelo($idModelo,$estado,$start,$limit,$em){
        $arr_encontrados = array();
        //$em = $this->getManager('telconet_infraestructura');
        $entidadesTotal = $this->getDetallesModelo($idModelo,$estado,'','');
        
        $entidades = $this->getDetallesModelo($idModelo,$estado,$start,$limit);
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $detalle = $em->find('schemaBundle:AdmiDetalle', $entidad->getDetalleId());
                
                $arr_encontrados[]=array('idDetalleModelo' =>$entidad->getId(),
                                         'idDetalle' =>trim($detalle->getId()),
                                         'nombreDetalle' =>trim($detalle->getNombreDetalle()),
                                         'tipoDetalle' => $detalle->getTipo()   
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
   
    public function getDetallesModelo($idModelo,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiDetalleModelo','e');
               
            
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
    
    public function generarJsonAllDetalles($idModelo,$estado,$start,$limit,$em){
        $arr_encontrados = array();
        //$em = $this->getManager('telconet_infraestructura');
        
        $entidadesTotal = $this->getDetallesModelo($idModelo,$estado,'','');
        $entidades = $this->getDetallesModelo($idModelo,$estado,$start,$limit);
        
        $entidadesInterfaceTotal = $this->getInterfaceModelo($idModelo,$estado,'','');
        $entidadesInterface = $this->getInterfaceModelo($idModelo,$estado,$start,$limit);
        
        if ($entidades) {
            $numEntidades = count($entidadesTotal);
            $numInterface = count($entidadesInterfaceTotal);
            
            $num = $numEntidades + $numInterface;
            
            foreach ($entidades as $entidad)
            {
                $detalle = $em->find('schemaBundle:AdmiDetalle', $entidad->getDetalleId());
                
                $arr_encontrados[]=array('idDetalleModelo' =>$entidad->getId(),
                                         'valor' =>'('.trim($detalle->getNombreDetalle()).')',
                                         'nombreDetalle' =>trim($detalle->getNombreDetalle()) 
                                         );
            }
            
            foreach ($entidadesInterface as $entidadInterface)
            {
                $detalleInterface = $em->getRepository('schemaBundle:AdmiDetalleInterface')->findBy(array( "interfaceModeloId" => $entidadInterface->getId()));
                
                foreach($detalleInterface as $int){
                    
                    $detalle = $em->find('schemaBundle:AdmiDetalle', $int->getDetalleId());
                    
                    $arr_encontrados[]=array('idDetalleModelo' =>$int->getId(),
                                         'valor' =>'('.trim($detalle->getNombreDetalle()).')',
                                         'nombreDetalle' =>trim($detalle->getNombreDetalle()) 
                                         );
                }
                
                
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
   
    public function getInterfaceModelo($idModelo,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiInterfaceModelo','e');
               
            
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
