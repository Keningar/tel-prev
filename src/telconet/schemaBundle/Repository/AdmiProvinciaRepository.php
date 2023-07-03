<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiProvinciaRepository extends EntityRepository
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
                        
                $arr_encontrados[]=array('id_provincia' =>$data->getId(),
                                         'nombre_provincia' =>trim($data->getNombreProvincia()),
                                         'nombre_region' => trim($data->getRegionId() ? $data->getRegionId()->getNombreRegion() : "NA" ),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_provincia' => 0 , 'nombre_provincia' => 'Ninguno', 'nombre_region' => 'Ninguno', 'provincia_id' => 0 , 'provincia_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
			$where .= "AND LOWER(pr.nombreProvincia) like LOWER('%".$nombre."%') ";
        }
		
		if(isset($parametros["idPais"]))
		{
	        if($parametros["idPais"] && $parametros["idPais"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND pa.id = '".$parametros["idPais"]."' ";
	        }
		}
		if(isset($parametros["idRegion"]))
		{
	        if($parametros["idRegion"] && $parametros["idRegion"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND re.id = '".$parametros["idRegion"]."' ";
	        }
		}
		
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
				$where .= "AND LOWER(pr.estado) not like LOWER('Eliminado') ";
            }
			else if($estado == "Activo-Todos")
			{
				$where .= "AND LOWER(pr.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(re.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(pa.estado) not like LOWER('Eliminado') ";			
			}
            else{
				$where .= "AND LOWER(pr.estado) like LOWER('".$estado."') ";
            }
        }
        
        $sql = "SELECT pr
        
                FROM 
                schemaBundle:AdmiProvincia pr, 
				schemaBundle:AdmiRegion re, 
                schemaBundle:AdmiPais pa 
        
                WHERE 
                pa.id = re.paisId   
                AND re.id = pr.regionId 
				
				$where 
				
				ORDER BY pr.nombreProvincia
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
       
}