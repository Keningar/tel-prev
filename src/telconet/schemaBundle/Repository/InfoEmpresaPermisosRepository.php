<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoEmpresaPermisosRepository extends EntityRepository
{

    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {      
                $arr_encontrados[]=array('id_empresa_permisos' =>$data->getId(),
                                         'nombre_empresa' =>trim($data->getEmpresaCod()->getNombreEmpresa()),
                                         'tipo_permiso' =>trim($data->getTipoPermiso()),
                                         'tiene_permiso' =>($data->getTienePermiso()=='S'?'SI':'NO'),
                                         'fecha_vigencia' => ($data->getFechaVigencia() ? $data->getFechaVigencia()->format('d M Y') : "-"),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_empresa_permisos' => 0 , 'nombre_empresa' => 'Ninguno', 'tipo_permiso' => 'Ninguno',  
                                                        'tiene_permiso' => 'Ninguno', 'fecha_vigencia' => 'Ninguno', 
                                                        'empresa_id' => 0 , 'empresa_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:InfoEmpresaPermisos','sim');
            
        $boolBusqueda = false;
        /*if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreEmpresa) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }*/
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

}
