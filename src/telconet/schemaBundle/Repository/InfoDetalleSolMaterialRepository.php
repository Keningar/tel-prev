<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleSolMaterialRepository extends EntityRepository
{    
    /* ******************************************** FACTIBILIDAD ******************************************* */
    public function generarJsonMaterialesByTarea($em, $em_naf, $start,$limit, $id_solicitud,$idTarea, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getMaterialesByTarea('', '', $idTarea);
        $registros = $this->getMaterialesByTarea($start, $limit, $idTarea);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {        
                $material_cod = $data["materialCod"];                
                $descripcionArticulo = "";
                $cantidadEstimada = 0;
                $cantidadCliente = 0;
                $idDetalleSolMaterial = 0;
                
                $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $material_cod); 
                if($vArticulo && count($vArticulo)>0)
                {
                    $descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                }
                
                $entitySolMaterial = $em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getUltimoIngresoMaterial($id_solicitud, $material_cod); 
                if($entitySolMaterial && count($entitySolMaterial)>0)
                {
                    $cantidadEstimada =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadEstimada() ? $entitySolMaterial->getCantidadEstimada() : 0) : 0); 
                    $cantidadCliente =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadCliente() ? $entitySolMaterial->getCantidadCliente() : 0) : 0); 
                    $idDetalleSolMaterial =  (isset($entitySolMaterial) ? ($entitySolMaterial->getId() ? $entitySolMaterial->getId() : 0) : 0); 
                }
                
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : "");    
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");    
                $costoMaterial =  ($data["costoMaterial"] ? "$ " .number_format($data["costoMaterial"], 2, '.', '') : 0.00);  
                $precioVentaMaterial =  ($data["precioVentaMaterial"] ? "$ " .number_format($data["precioVentaMaterial"], 2, '.', '')  : 0.00);  
                $cantidadMaterial =  ($data["cantidadMaterial"] ? $data["cantidadMaterial"]  : 0);                  
                $idDetalleSol =  (isset($data["id_detalle_solicitud"]) ? ($data["id_detalle_solicitud"] ? $data["id_detalle_solicitud"] : $id_solicitud) : $id_solicitud); 
                               
                $arr_encontrados[]=array(
                                         'id_detalle_solicitud' => $idDetalleSol,
                                         'id_detalle_sol_material' => $idDetalleSolMaterial,
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_tarea_material' =>$data["idTareaMaterial"],
                                         'cod_material' => $data["materialCod"],
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_material' =>trim($descripcionArticulo),
                                         'costo_material' => $costoMaterial,
                                         'precio_venta_material' => $precioVentaMaterial,
                                         'cantidad_empresa' => $cantidadMaterial,
                                         'cantidad_estimada' => $cantidadEstimada,
                                         'cantidad_cliente' => $cantidadCliente
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_detalle_solicitud' => 0 , 'id_detalle_sol_material' => 0, 'id_proceso' => 0, 
                                                        'id_tarea' => 0 , 'id_tarea_material' => 0, 'cod_material' => "",    
                                                        'nombre_proceso' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                                        'nombre_material' => 'Ninguno', 'costo_material' => 0.00, 
                                                        'precio_venta_material' => 0.00, 'cantidad_empresa' => 0, 
                                                        'cantidad_usada' => 0, 'cantidad_cliente' => 0, 
                                                        'estado' => 'Ninguno'));
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
    
    public function generarJsonFactibilidadLikeProceso($em, $em_naf, $start,$limit, $id_solicitud, $proceso, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosFactibilidadLikeProceso('', '', $id_solicitud, $proceso);
        $registros = $this->getRegistrosFactibilidadLikeProceso($start, $limit, $id_solicitud, $proceso);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {        
                $material_cod = $data["materialCod"];                
                $descripcionArticulo = "";
                $cantidadEstimada = 0;
                $cantidadCliente = 0;
                $idDetalleSolMaterial = 0;
                
                $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $material_cod); 
                if($vArticulo && count($vArticulo)>0)
                {
                    $descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                }
                
                $entitySolMaterial = $em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getUltimoIngresoMaterial($id_solicitud, $material_cod); 
                if($entitySolMaterial && count($entitySolMaterial)>0)
                {
                    $cantidadEstimada =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadEstimada() ? $entitySolMaterial->getCantidadEstimada() : 0) : 0); 
                    $cantidadCliente =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadCliente() ? $entitySolMaterial->getCantidadCliente() : 0) : 0); 
                    $idDetalleSolMaterial =  (isset($entitySolMaterial) ? ($entitySolMaterial->getId() ? $entitySolMaterial->getId() : 0) : 0); 
                }
                
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : "");    
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");    
                $costoMaterial =  ($data["costoMaterial"] ? "$ " .number_format($data["costoMaterial"], 2, '.', '') : 0.00);  
                $precioVentaMaterial =  ($data["precioVentaMaterial"] ? "$ " .number_format($data["precioVentaMaterial"], 2, '.', '')  : 0.00);  
                $cantidadMaterial =  ($data["cantidadMaterial"] ? $data["cantidadMaterial"]  : 0);                  
                $idDetalleSol =  (isset($data["id_detalle_solicitud"]) ? ($data["id_detalle_solicitud"] ? $data["id_detalle_solicitud"] : $id_solicitud) : $id_solicitud); 
                
                if($cantidadEstimada>0){
                $arr_encontrados[]=array(
                                         'id_detalle_solicitud' => $idDetalleSol,
                                         'id_detalle_sol_material' => $idDetalleSolMaterial,
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_tarea_material' =>$data["idTareaMaterial"],
                                         'cod_material' => $data["materialCod"],
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_material' =>trim($descripcionArticulo),
                                         'costo_material' => $costoMaterial,
                                         'precio_venta_material' => $precioVentaMaterial,
                                         'cantidad_empresa' => $cantidadMaterial,
                                         'cantidad_estimada' => $cantidadEstimada,
                                         'cantidad_cliente' => $cantidadCliente
                                        );
                }
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_detalle_solicitud' => 0 , 'id_detalle_sol_material' => 0, 'id_proceso' => 0, 
                                                        'id_tarea' => 0 , 'id_tarea_material' => 0, 'cod_material' => "",    
                                                        'nombre_proceso' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                                        'nombre_material' => 'Ninguno', 'costo_material' => 0.00, 
                                                        'precio_venta_material' => 0.00, 'cantidad_empresa' => 0, 
                                                        'cantidad_usada' => 0, 'cantidad_cliente' => 0, 
                                                        'estado' => 'Ninguno'));
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
    
    public function generarJsonFactibilidad($em, $em_naf, $start,$limit, $id_solicitud, $id_proceso, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosFactibilidad('', '', $id_solicitud, $id_proceso);
        $registros = $this->getRegistrosFactibilidad($start, $limit, $id_solicitud, $id_proceso);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {        
                $material_cod = $data["materialCod"];                
                $descripcionArticulo = "";
                $cantidadEstimada = 0;
                $cantidadCliente = 0;
                $idDetalleSolMaterial = 0;
                
                $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $material_cod); 
                if($vArticulo && count($vArticulo)>0)
                {
                    $descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                }
                
                $entitySolMaterial = $em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getUltimoIngresoMaterial($id_solicitud, $material_cod); 
                if($entitySolMaterial && count($entitySolMaterial)>0)
                {
                    $cantidadEstimada =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadEstimada() ? $entitySolMaterial->getCantidadEstimada() : 0) : 0); 
                    $cantidadCliente =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadCliente() ? $entitySolMaterial->getCantidadCliente() : 0) : 0); 
                    $idDetalleSolMaterial =  (isset($entitySolMaterial) ? ($entitySolMaterial->getId() ? $entitySolMaterial->getId() : 0) : 0); 
                }
                
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : "");    
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");    
                $costoMaterial =  ($data["costoMaterial"] ? "$ " .number_format($data["costoMaterial"], 2, '.', '') : 0.00);  
                $precioVentaMaterial =  ($data["precioVentaMaterial"] ? "$ " .number_format($data["precioVentaMaterial"], 2, '.', '')  : 0.00);  
                $cantidadMaterial =  ($data["cantidadMaterial"] ? $data["cantidadMaterial"]  : 0);                  
                $idDetalleSol =  (isset($data["id_detalle_solicitud"]) ? ($data["id_detalle_solicitud"] ? $data["id_detalle_solicitud"] : $id_solicitud) : $id_solicitud); 
                
                if($cantidadEstimada>0){
                $arr_encontrados[]=array(
                                         'id_detalle_solicitud' => $idDetalleSol,
                                         'id_detalle_sol_material' => $idDetalleSolMaterial,
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_tarea_material' =>$data["idTareaMaterial"],
                                         'cod_material' => $data["materialCod"],
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_material' =>trim($descripcionArticulo),
                                         'costo_material' => $costoMaterial,
                                         'precio_venta_material' => $precioVentaMaterial,
                                         'cantidad_empresa' => $cantidadMaterial,
                                         'cantidad_estimada' => $cantidadEstimada,
                                         'cantidad_cliente' => $cantidadCliente
                                        );
                }
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_detalle_solicitud' => 0 , 'id_detalle_sol_material' => 0, 'id_proceso' => 0, 
                                                        'id_tarea' => 0 , 'id_tarea_material' => 0, 'cod_material' => "",    
                                                        'nombre_proceso' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                                        'nombre_material' => 'Ninguno', 'costo_material' => 0.00, 
                                                        'precio_venta_material' => 0.00, 'cantidad_empresa' => 0, 
                                                        'cantidad_usada' => 0, 'cantidad_cliente' => 0, 
                                                        'estado' => 'Ninguno'));
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
    
    public function getMaterialesByTarea($start, $limit, $idTarea)
    {
        $boolBusqueda = false; 
        $where = "";  
        
        $sql = "SELECT 
                pr.id as idProceso, t.id as idTarea, 
                tm.id as idTareaMaterial, tm.materialCod, 
                pr.nombreProceso, t.nombreTarea, 
                tm.costoMaterial, tm.precioVentaMaterial,
                tm.cantidadMaterial        
        
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
    
    public function getRegistrosFactibilidadLikeProceso($start, $limit, $id_solicitud, $proceso)
    {
        $boolBusqueda = false; 
        $where = "";  
        
        $sql = "SELECT 
                pr.id as idProceso, t.id as idTarea, 
                tm.id as idTareaMaterial, tm.materialCod, 
                pr.nombreProceso, t.nombreTarea, 
                tm.costoMaterial, tm.precioVentaMaterial,
                tm.cantidadMaterial        
        
                FROM 
                schemaBundle:AdmiProceso pr, schemaBundle:AdmiTarea t,
                schemaBundle:AdmiTareaMaterial tm 
        
                WHERE pr.id = t.procesoId 
                AND t.id = tm.tareaId 
        
                AND LOWER(t.estado) not like LOWER('Eliminado') 
                AND LOWER(tm.estado) not like LOWER('Eliminado') 
                AND pr.nombreProceso like '%$proceso%'
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
    public function getRegistrosFactibilidad($start, $limit, $id_solicitud, $id_proceso)
    {
        $boolBusqueda = false; 
        $where = "";  
        
        $sql = "SELECT 
                pr.id as idProceso, t.id as idTarea, 
                tm.id as idTareaMaterial, tm.materialCod, 
                pr.nombreProceso, t.nombreTarea, 
                tm.costoMaterial, tm.precioVentaMaterial,
                tm.cantidadMaterial        
        
                FROM 
                schemaBundle:AdmiProceso pr, schemaBundle:AdmiTarea t,
                schemaBundle:AdmiTareaMaterial tm 
        
                WHERE pr.id = t.procesoId 
                AND t.id = tm.tareaId 
        
                AND LOWER(t.estado) not like LOWER('Eliminado') 
                AND LOWER(tm.estado) not like LOWER('Eliminado') 
                AND pr.id = $id_proceso
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
 
    public function getUltimoIngresoMaterial($id_detalle_solicitud, $material_cod)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('material')
           ->from('schemaBundle:InfoDetalleSolMaterial','material')
           ->where("material.detalleSolicitudId = ?1 AND material.materialCod = ?2")
           ->setParameter(1, $id_detalle_solicitud)
           ->setParameter(2, $material_cod)
           ->orderBy('material.id','DESC')
           ->setMaxResults(1);
        
        $query = $qb->getQuery();
        $results = $query->getResult();
       
        if(count($results)>0) return $results[0];
        else return false;
    }

    /**
     * Funcion que sirve para obtener los materiales que han sido ingresados mediante una solicitud
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 01-08-2015
     * @param string $idServicio
     */
    public function getMaterialesPorServicio($idServicio)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "select dsm.material_cod,
                    dsm.costo_material,
                    dsm.cantidad_usada,
                    dsm.cantidad_facturada
                from info_detalle_solicitud ds, info_detalle_sol_material dsm
                where dsm.detalle_solicitud_id = ds.id_detalle_solicitud
                    and ds.servicio_id = :idServicio";
        
        $rsm->addScalarResult('MATERIAL_COD',           'materialCod',          'string');
        $rsm->addScalarResult('COSTO_MATERIAL',         'costoMaterial',        'float');
        $rsm->addScalarResult('CANTIDAD_USADA',         'cantidadUsada',        'integer');
        $rsm->addScalarResult('CANTIDAD_FACTURADA',     'cantidadFacturada',    'integer');

        
        $query->setParameter("idServicio",  $idServicio);
        
        $query->setSQL($sql);
        $arrayResultado = $query->getResult();

        return $arrayResultado;
    }

	/**
     * getVerificaDocumentosMaterialesExcedentes
     *
     * Metodo encargado de obtener los archivos (imagenes) enrutados a la solicitud
     * mediante una consulta a la base de datos
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     */ 
    public function getVerificaDocumentosMaterialesExcedentes($arrayParametros,$emInfraestructura)
    {

        $strQuery = $emInfraestructura->createQuery();

        $strSql = " SELECT infoDocumento.id,
                  infoDocumento.ubicacionLogicaDocumento as ubicacionLogica,
                  infoDocumento.feCreacion as feCreacion,infoDocumento.usrCreacion,
                  infoDocumento.ubicacionFisicaDocumento as linkVerDocumento ";

        $strFrom = " FROM
                  schemaBundle:InfoDocumento infoDocumento,
                  schemaBundle:InfoDocumentoRelacion infoDocumentoRelacion
                  WHERE
                  infoDocumento.id = infoDocumentoRelacion.documentoId ";
        
        $strWhere= " AND infoDocumento.estado <> :strEstadoDocumento ";
        $strQuery->setParameter("strEstadoDocumento", 'Eliminado');
        $strWhere .= " AND infoDocumentoRelacion.servicioId = :varServicioId 
                        AND infoDocumentoRelacion.modulo = :modulo ";
           
        $strQuery->setParameter(":varServicioId", $arrayParametros["intIdServicio"]);
        $strQuery->setParameter(":modulo", "PLANIFICACION");
        
        
        $strOrder = " ORDER BY
                  infoDocumento.feCreacion DESC ";

        $strSql = $strSql . $strFrom . $strWhere . $strOrder;
        $strQuery->setDQL($strSql);
        $strDatos['registros'] = $strQuery->getResult();
        $strSqlCount = " SELECT COUNT(infoDocumento) ";
        $strSqlCount = $strSqlCount . $strFrom . $strWhere . $strOrder;

        return $strDatos;
    }

}
