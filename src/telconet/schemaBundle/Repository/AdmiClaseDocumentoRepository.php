<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiClaseDocumentoRepository extends EntityRepository
{
    public function generarJsonEntidades($nombre,$estado,$start,$limit,$visible="Todos")
    {
    
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getEntidades($nombre, $estado, '', '',$visible);
        
        
        $entidades = $this->getEntidades($nombre, $estado, $start, $limit,$visible);
 
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('id_clase_documento' =>$entidad->getId(),
                                         'nombre_clase_documento' =>trim($entidad->getNombreClaseDocumento()),
                                         'estado' =>(trim($entidad->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'visible'=>(trim($entidad->getVisible())),
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
    public function getEntidades($nombre,$estado,$start,$limit,$visible="Todos"){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiClaseDocumento','e');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(e.nombreClaseDocumento) like LOWER(?1)');
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
        if($visible!="Todos"){
	    
	    $qb->andWhere("e.visible = ?3");
	    $qb->setParameter(3, $visible);
        
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