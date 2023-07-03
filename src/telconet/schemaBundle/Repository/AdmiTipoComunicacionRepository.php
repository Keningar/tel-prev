<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoComunicacionRepository extends EntityRepository
{
    public function generarJsonEntidades($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getEntidades($nombre, $estado, '', '');
        
        
        $entidades = $this->getEntidades($nombre, $estado, $start, $limit);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('id_tipo_comunicacion' =>$entidad->getId(),
                                         'nombre_tipo_comunicacion' =>trim($entidad->getNombreTipoComunicacion()),
                                         'estado' =>(trim($entidad->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($entidad->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($entidad->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_comunicacion' => 0 , 'nombre_tipo_comunicacion' => 'Ninguno','tipo_comunicacion_id' => 0 , 'tipo_comunicacion_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    public function getEntidades($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoComunicacion','e');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(e.nombreTipoComunicacion) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(e.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(e.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $qb->orderBy('e.id');
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
}