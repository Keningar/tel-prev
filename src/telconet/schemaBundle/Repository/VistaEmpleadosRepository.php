<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class VistaEmpleadosRepository extends EntityRepository
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
            
                $arr_encontrados[]=array('login_empleado' =>$entidad->getLogin(),
                                         'nombre_empleado' =>trim($entidad->getNombreCompleto()),
                                         'id_empleado' => $entidad->getId()
                                         );
            }
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
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
               ->from('schemaBundle:VistaEmpleados','e');
               
        if($nombre!=""){
            $qb ->where( "lower(e.nombreCompleto) like lower('%". $nombre ."%') ");
           // $qb->setParameter(1, "'%". $nombre . "%'");
        }
		
        if($estado!="Todos"){
            if($estado=="Activo"){
                $qb ->andWhere("lower(e.estado) not like lower('Eliminado')");
            }
            else{
                $qb ->andWhere('lower(e.estado) = lower(?2)');
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