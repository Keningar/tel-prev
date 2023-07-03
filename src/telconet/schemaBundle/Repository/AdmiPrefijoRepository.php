<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiPrefijoRepository extends EntityRepository
{
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se modifica la consulta del nombre del proveedor
    **/
    
    public function generarJsonPrefijos($proveedorRed,$nombreIpv4,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $prefijosTotal = $this->getPrefijos($proveedorRed,$nombreIpv4,$estado,'','');
        
        $prefijos = $this->getPrefijos($proveedorRed,$nombreIpv4,$estado,$start,$limit);
//        error_log('entra');
        if ($prefijos) {
            
            $num = count($prefijosTotal);
            
            foreach ($prefijos as $prefijo)
            {
                $strProveedorRed = '';
                if($prefijo->getProveedorRedId())
                {
                    $objProveedorRed = $this->_em->getRepository('schemaBundle:AdmiProveedorRed')->find($prefijo->getProveedorRedId());
                    if(is_object($objProveedorRed))
                    {
                        $strProveedorRed = $objProveedorRed->getNombreProveedorRed();
                    }
                }
                
                $arr_encontrados[]=array('idPrefijo' =>$prefijo->getId(),
                                         'proveedorRed' =>trim($strProveedorRed),
                                         'nombreIpv4' =>trim($prefijo->getNombreIpv4()),
                                         'estado' =>(trim($prefijo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($prefijo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($prefijo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getPrefijos($proveedorRed,$nombreIpv4,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiPrefijo','e');
               
            
        if($proveedorRed!=""){
            $qb ->where( 'e.proveedorRedId = ?1');
            $qb->setParameter(1, $proveedorRed);
        }
        if($nombreIpv4!=""){
            $qb ->where( 'e.nombreIpv4 like ?3');
            $qb->setParameter(3, '%'.$nombreIpv4.'%');
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
