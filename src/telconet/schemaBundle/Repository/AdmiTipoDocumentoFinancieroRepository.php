<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\QueryBuilder;

class AdmiTipoDocumentoFinancieroRepository extends EntityRepository
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
                        
                $arr_encontrados[]=array('id_tipo_documento' =>$data->getId(),
                                         'codigo_tipo_documento' =>trim($data->getCodigoTipoDocumento()),
                                         'nombre_tipo_documento' =>trim($data->getNombreTipoDocumento()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_documento' => 0 , 'nombre_tipo_documento' => 'Ninguno', 'descripcion_tipo_documento' => 'Ninguno', 'hipotesis_id' => 0 , 'hipotesis_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiTipoDocumentoFinanciero','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreTipoDocumento) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
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