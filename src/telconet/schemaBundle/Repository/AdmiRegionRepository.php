<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiRegionRepository extends EntityRepository
{
    public function generarJson($parametros, $nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($parametros, $nombre, $estado, '', '');
        $registros = $this->getRegistros($parametros, $nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_region' =>$data->getId(),
                                         'nombre_region' =>trim($data->getNombreRegion()),
                                         'nombre_pais' => trim($data->getPaisId() ? $data->getPaisId()->getNombrePais() : "NA" ),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_region' => 0 , 'nombre_region' => 'Ninguno', 'nombre_pais' => 'Ninguno', 'region_id' => 0 , 'region_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($parametros, $nombre,$estado,$start,$limit)
	{
        $boolBusqueda = false; 
        $where = "";  
		
        if($nombre!="")
        {
            $boolBusqueda = true;
			$where .= "AND LOWER(re.nombreRegion) like LOWER('%".$nombre."%') ";
        }
		
		if(isset($parametros["idPais"]))
		{
	        if($parametros["idPais"] && $parametros["idPais"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND pa.id = '".$parametros["idPais"]."' ";
	        }
		}
		
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
				$where .= "AND LOWER(re.estado) not like LOWER('Eliminado') ";
            }
			else if($estado == "Activo-Todos")
			{
				$where .= "AND LOWER(re.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(pa.estado) not like LOWER('Eliminado') ";		
			}
            else{
				$where .= "AND LOWER(re.estado) like LOWER('".$estado."') ";
            }
        }
        
        $sql = "SELECT re
        
                FROM 
				schemaBundle:AdmiRegion re, 
                schemaBundle:AdmiPais pa 
        
                WHERE 
                pa.id = re.paisId   
				
				$where 
				
				ORDER BY re.nombreRegion
               ";  
			   
        $query = $this->_em->createQuery($sql);
        
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();
			
		return $datos;
    }
    
    /**
     * getRegiones
     *
     * Metodo encargado de obtener el query builder que trae los regiones para ser usado a nivel de Type         
     *
     * @return queryBuilder
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 13-02-2015
     */       
    public function getRegiones()
    {
        $qb = $this->createQueryBuilder("admi_region")
            ->select('admi_region')
            ->from('schemaBundle:AdmiRegion', 'region')
            ->where("region.estado != ?1")
            ->orderBy('region.nombreRegion', 'ASC');
        $qb->setParameter(1, 'Eliminado');
        
        return $qb;
    }

}