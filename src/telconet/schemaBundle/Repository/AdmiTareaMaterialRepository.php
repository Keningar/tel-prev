<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTareaMaterialRepository extends EntityRepository
{
    /**
     * Funcion que sirve para obtener los materiales de un proceso definido por el usuario
     * Costo = 10
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-08-2015
     * @param string $nombreProceso
     * @param string $codEmpresa
     * @return array $materiales (idProceso, nombreProceso, idTarea, idTareaMaterial, materialCod, costoMaterial, precioVentaMaterial,
     *                            cantidadMaterial, unidadMedidaMaterial)
     */
    public function getMaterialesPorProceso($nombreProceso, $codEmpresa)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT 
                PROCESO.ID_PROCESO ID_PROCESO, 
                PROCESO.NOMBRE_PROCESO NOMBRE_PROCESO, 
                TAREA.ID_TAREA ID_TAREA, 
                TAREA_MATERIAL.ID_TAREA_MATERIAL ID_TAREA_MATERIAL, 
                TAREA_MATERIAL.MATERIAL_COD MATERIAL_COD, 
                TAREA_MATERIAL.COSTO_MATERIAL COSTO_MATERIAL,
                TAREA_MATERIAL.PRECIO_VENTA_MATERIAL PRECIO_VENTA_MATERIAL,
                TAREA_MATERIAL.CANTIDAD_MATERIAL CANTIDAD_MATERIAL,
                TAREA_MATERIAL.UNIDAD_MEDIDA_MATERIAL UNIDAD_MEDIDA_MATERIAL
                FROM ADMI_PROCESO PROCESO,
                ADMI_TAREA TAREA,
                ADMI_TAREA_MATERIAL TAREA_MATERIAL
                WHERE PROCESO.ID_PROCESO = TAREA.PROCESO_ID
                AND TAREA.ID_TAREA = TAREA_MATERIAL.TAREA_ID
                AND PROCESO.NOMBRE_PROCESO = :nombreProceso
                AND PROCESO.ESTADO = :estado
                AND TAREA.ESTADO = :estado
                AND TAREA_MATERIAL.ESTADO = :estado
                AND TAREA_MATERIAL.EMPRESA_COD = :codEmpresa";
        
        $rsm->addScalarResult('ID_PROCESO',             'idProceso',            'integer');
        $rsm->addScalarResult('NOMBRE_PROCESO',         'nombreProceso',        'string');
        $rsm->addScalarResult('ID_TAREA',               'idTarea',              'integer');
        $rsm->addScalarResult('ID_TAREA_MATERIAL',      'idTareaMaterial',      'integer');
        $rsm->addScalarResult('MATERIAL_COD',           'materialCod',          'string');
        $rsm->addScalarResult('COSTO_MATERIAL',         'costoMaterial',        'float');
        $rsm->addScalarResult('PRECIO_VENTA_MATERIAL',  'precioVentaMaterial',  'float');
        $rsm->addScalarResult('CANTIDAD_MATERIAL',      'cantidadMaterial',     'integer');
        $rsm->addScalarResult('UNIDAD_MEDIDA_MATERIAL', 'unidadMedidaMaterial', 'string');
        
        $query->setParameter("nombreProceso",   $nombreProceso);
        $query->setParameter("codEmpresa",      $codEmpresa);
        $query->setParameter("estado",          'Activo');
        
        $query->setSQL($sql);
        $materiales = $query->getResult();

        return $materiales;
    }
    
public function generarJson($em_naf, $codEmpresa, $nombre,$estado,$start,$limit,$prefijoEmpresa="")
    {
        $arr_encontrados = array();               
        
        $registrosTotal = $this->getRegistros($codEmpresa, $nombre, $estado, '', '');
        $registros = $this->getRegistros($codEmpresa, $nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {     
                $nombreMaterial = "";
                if($data->getMaterialCod())
                {    
		    if($prefijoEmpresa == 'MD') $codEmpresa = '10';		    		    
                
		    $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $data->getMaterialCod()); 
                    if($vArticulo && count($vArticulo)>0)
					{
						$nombreMaterial =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
					}
					//$objMaterial = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->findOneById($data->getMaterialCod());
					//$nombreMaterial = $objMaterial ? $objMaterial->getDescripcion() : "";
                }                              
                
                $arr_encontrados[]=array('id_tarea_material' =>$data->getId(),
                                         'nombre_tarea' => trim($data->getTareaId() ? $data->getTareaId()->getNombreTarea() : "-" ),
                                         'nombre_material' => trim($nombreMaterial),
                                         'unidad' => trim($data->getUnidadMedidaMaterial() ? $data->getUnidadMedidaMaterial() : "-" ),
                                         'costo' =>($data->getCostoMaterial() ? $data->getCostoMaterial() : 0.00),
                                         'precio' =>($data->getPrecioVentaMaterial() ? $data->getPrecioVentaMaterial() : 0.00),
                                         'cantidad' =>($data->getCantidadMaterial() ? $data->getCantidadMaterial() : 0),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea_material' => 0 , 'nombre_tarea' => 'Ninguno', 
                                        'nombre_material' => 'Ninguno', 'unidad' => 'Ninguno', 
                                        'precio' => '0', 'costo' => '0', 'cantidad' => '0', 
                                        'tarea_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
	
	public function generarJsonByTarea($em_naf, $codEmpresa, $idTarea ,$estado,$start,$limit,$prefijoEmpresa="")
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getMaterialesByTarea("", $limit, $idTarea);
        $registros = $this->getMaterialesByTarea("", $limit, $idTarea);                
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {     
                $nombreMaterial = "";
                if($data->getMaterialCod())
                {    
		     if($prefijoEmpresa == 'MD') $codEmpresa = '10';		    		    
                
		    $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $data->getMaterialCod()); 
                    if($vArticulo && count($vArticulo)>0)
					{
						$nombreMaterial =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
					}
					//$objMaterial = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->findOneById($data->getMaterialCod());
					//$nombreMaterial = $objMaterial ? $objMaterial->getDescripcion() : "";
                }
                
                
                $arr_encontrados[]=array('id_tarea_material' =>$data->getId(),
                                         'nombre_tarea' => trim($data->getTareaId() ? $data->getTareaId()->getNombreTarea() : "-" ),
                                         'nombre_material' => trim($nombreMaterial),
                                         'unidad' => trim($data->getUnidadMedidaMaterial() ? $data->getUnidadMedidaMaterial() : "-" ),
                                         'costo' =>($data->getCostoMaterial() ? $data->getCostoMaterial() : 0.00),
                                         'precio' =>($data->getPrecioVentaMaterial() ? $data->getPrecioVentaMaterial() : 0.00),
                                         'cantidad' =>($data->getCantidadMaterial() ? $data->getCantidadMaterial() : 0),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea_material' => 0 , 'nombre_tarea' => 'Ninguno', 
                                        'nombre_material' => 'Ninguno', 'unidad' => 'Ninguno', 
                                        'precio' => '0', 'costo' => '0', 'cantidad' => '0', 
                                        'tarea_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($codEmpresa, $nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
		$qb->select('sim')
		   ->from('schemaBundle:AdmiTareaMaterial','sim');
			    
        if($codEmpresa!=""){
            $qb ->where("sim.empresaCod = ?1 ");
            $qb->setParameter(1, $codEmpresa);
        }
        /*    
        if($nombre!=""){
            $qb ->where( 'LOWER(sim.nombreTarea) like LOWER(?1)');
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
        $qb->orderBy('sim.estado', 'ASC');
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
	
	public function getMaterialesByTarea($start, $limit, $idTarea)
    {
        $boolBusqueda = false; 
        $where = "";  
        
        $sql = "SELECT tm
                FROM 
                schemaBundle:AdmiProceso pr, schemaBundle:AdmiTarea t,
                schemaBundle:AdmiTareaMaterial tm 
        
                WHERE pr.id = t.procesoId 
                AND t.id = tm.tareaId 
        
                AND LOWER(t.estado) not like LOWER('Eliminado') 
                AND LOWER(tm.estado) not like LOWER('Eliminado') 
                AND t.id = $idTarea
                $where 
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
