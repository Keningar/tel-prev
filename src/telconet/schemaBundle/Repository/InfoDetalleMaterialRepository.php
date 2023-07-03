<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleMaterialRepository extends EntityRepository
{
    public function generarJsonMaterialesUtilizados($em, $em_naf, $start,$limit, $id_solicitud,$codEmpresa)
    {
        $arr_encontrados = array();
 
	$registros = $detalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolMaterial')->findByDetalleSolicitudId($id_solicitud);
        
        if ($registros) {
            $num = count($registros); 
	    $detalleSolicitud = $em->find('schemaBundle:InfoDetalleSolicitud', $id_solicitud);			
            
            foreach ($registros as $data)
            {        
                $descripcionArticulo = "";
                $subgrupoArticulo = "";
                $cantidadEstimada = 0;
                $cantidadCliente = 0;
                $cantidadUsada = 0;
                $cantidadFacturada = 0;
                
                $material_cod = $data->getMaterialCod();
                $cantidadEstimada = $data->getCantidadEstimada();                
                $cantidadCliente = $data->getCantidadCliente();                
                $cantidadUsada = $data->getCantidadUsada();                
                $cantidadFacturada = $data->getCantidadFacturada();                
                //Consulto el valor de la empresa equivalente cuando se realiza la consulta de los materiales por SOLICITUD DE PLANIFICACION
                $parametros = $em->getRepository('schemaBundle:AdmiParametroDet')->getOne("EMPRESA_EQUIVALENTE","","","MATERIALES_INSTALACION",
                                                  $codEmpresa,"","","");

                if($parametros)
                {
                    $empresaEquivalente = $parametros['valor2'];
                }
                else
                {
                    $empresaEquivalente = $codEmpresa;
                }

                $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($empresaEquivalente,$material_cod);
                if($vArticulo && count($vArticulo)>0)
                {
                    $descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                    $subgrupoArticulo =  (isset($vArticulo) ? ($vArticulo->getSubgrupo() ? $vArticulo->getSubgrupo() : "") : ""); 
                }

                $arr_encontrados[]=array(
                                         'nombre_material' =>trim($descripcionArticulo),
                                         'cod_material' =>trim($material_cod),
                                         'subgrupo_material' =>trim($subgrupoArticulo),
                                         'cantidad_usada' => $cantidadUsada,
                                         'cantidad_estimada' => $cantidadEstimada,
                                         'cantidad_cliente' => $cantidadCliente,
                                         'cantidad_facturada' => $cantidadFacturada
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                         'nombre_material' =>"No tiene materiales registrados",
                                         'cod_material' =>"",
                                         'subgrupo_material' =>"",
                                         'cantidad_usada' => "",
                                         'cantidad_estimada' => "",
                                         'cantidad_cliente' => "",
                                         'cantidad_facturada' => ""
                                        ));
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

    /* ******************************************** FINALIZAR ******************************************* */
    public function generarJsonIngresarMateriales($em, $em_naf, $start,$limit, $id_solicitud, $id_proceso, $codEmpresa)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistrosIngresarMateriales('', '', $id_solicitud, $id_proceso,$codEmpresa);
        $registros = $this->getRegistrosIngresarMateriales($start, $limit, $id_solicitud, $id_proceso,$codEmpresa);
        $empresaSession = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($codEmpresa);
        $prefijoEmpresa = $empresaSession->getPrefijo();
        $codEmpresaNaf = $codEmpresa;
        
        if($prefijoEmpresa=="MD" || $prefijoEmpresa=="EN")
        {
	    $empresaTN = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo("TN");
	    $codEmpresaNaf = $empresaTN->getId();
        }
 
        if ($registros) {
            $num = count($registrosTotal); 
			$detalleSolicitud = $em->find('schemaBundle:InfoDetalleSolicitud', $id_solicitud);			
            foreach ($registros as $data)
            {        
                $material_cod = $data["materialCod"];                
                $descripcionArticulo = "";
                $cantidadEstimada = 0;
                $cantidadCliente = 0;
                $idDetalleSolMaterial = 0;
                
                $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresaNaf, $material_cod); 
                if($vArticulo && count($vArticulo)>0)
                {
                    $descripcionArticulo =  (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                    $subgrupoArticulo =  (isset($vArticulo) ? ($vArticulo->getSubgrupo() ? $vArticulo->getSubgrupo() : "") : ""); 
                }
                
                $entitySolicitudMateriales = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->getDetalleSolicitudMaterialByServicioAndEstado($em,$detalleSolicitud->getServicioId()->getId(),'Aprobada'); 
                if($entitySolicitudMateriales){
					$entitySolMaterial = $em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getUltimoIngresoMaterial($entitySolicitudMateriales->getId(), $material_cod); 
                if($entitySolMaterial && count($entitySolMaterial)>0)
                {
                    $cantidadEstimada =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadEstimada() ? $entitySolMaterial->getCantidadEstimada() : 0) : 0); 
                    $cantidadCliente =  (isset($entitySolMaterial) ? ($entitySolMaterial->getCantidadCliente() ? $entitySolMaterial->getCantidadCliente() : 0) : 0); 
                    $idDetalleSolMaterial =  (isset($entitySolMaterial) ? ($entitySolMaterial->getId() ? $entitySolMaterial->getId() : 0) : 0); 
                }
					$observacionExcedente = $entitySolicitudMateriales->getObservacion();
                }else{
					$observacionExcedente = "Sin Informacion";
				}
                $nombreProceso =  ($data["nombreProceso"] ? $data["nombreProceso"]  : "");    
                $nombreTarea =  ($data["nombreTarea"] ? $data["nombreTarea"]  : "");    
                $costoMaterial =  ($data["costoMaterial"] ? "$ " .number_format($data["costoMaterial"], 2, '.', '') : 0.00);  
                $precioVentaMaterial =  ($data["precioVentaMaterial"] ? "$ " .number_format($data["precioVentaMaterial"], 2, '.', '')  : 0.00);  
                $cantidadMaterial =  ($data["cantidadMaterial"] ? $data["cantidadMaterial"]  : 0);                  
                $idDetalleSol =  (isset($data["id_detalle_solicitud"]) ? ($data["id_detalle_solicitud"] ? $data["id_detalle_solicitud"] : $id_solicitud) : $id_solicitud); 
                $idDetalle =  (isset($data["idDetalle"]) ? ($data["idDetalle"] ? $data["idDetalle"] : 0) : 0); 
                               
                $arr_encontrados[]=array(
                                         'id_detalle_solicitud' => $idDetalleSol,
                                         'id_detalle_sol_material' => $idDetalleSolMaterial,
                                         'id_detalle' => $idDetalle,
                                         'id_proceso' =>$data["idProceso"],
                                         'id_tarea' =>$data["idTarea"],
                                         'id_tarea_material' =>$data["idTareaMaterial"],
                                         'cod_material' => $data["materialCod"],
                                         'nombre_proceso' =>trim($nombreProceso),
                                         'nombre_tarea' =>trim($nombreTarea),
                                         'nombre_material' =>trim($descripcionArticulo),
                                         'subgrupo_material' =>trim($subgrupoArticulo),
                                         'costo_material' => $costoMaterial,
                                         'precio_venta_material' => $precioVentaMaterial,
                                         'cantidad_empresa' => $cantidadMaterial,
                                         'cantidad_estimada' => $cantidadEstimada,
                                         'cantidad_cliente' => $cantidadCliente,
                                         'cantidad_usada' => 0,
                                         'cantidad_excedente' => 0,
                                         'facturar' => ($data["facturable"]==0) ? false : true
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_detalle_solicitud' => 0 , 'id_detalle_sol_material' => 0, 
                                                        'id_detalle' => 0, 'id_proceso' => 0,
                                                        'id_tarea' => 0 , 'id_tarea_material' => 0, 'cod_material' => "",    
                                                        'nombre_proceso' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                                        'nombre_material' => 'Ninguno', 'costo_material' => 0.00, 
                                                        'precio_venta_material' => 0.00, 'cantidad_empresa' => 0, 
                                                        'cantidad_estimada' => 0, 'cantidad_cliente' => 0, 
                                                        'cantidad_usada' => 0, 'cantidad_excedente' => 0, 
                                                        'facturar' => false, 'estado' => 'Ninguno'));
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
    
    public function getRegistrosIngresarMateriales($start, $limit, $id_solicitud, $id_proceso,$codEmpresa)
    {
        $boolBusqueda = false; 
        $where = "";  
        
        $sql = "SELECT 
                d.id as idDetalle, tm.id as idTareaMaterial,
                pr.id as idProceso, t.id as idTarea, 
                tm.materialCod, tm.cantidadMaterial,  
                pr.nombreProceso, t.nombreTarea, 
                tm.costoMaterial, tm.precioVentaMaterial,
                tm.facturable
                       
                FROM 
                schemaBundle:InfoDetalle d, schemaBundle:AdmiTareaMaterial tm, 
                schemaBundle:AdmiProceso pr, schemaBundle:AdmiProcesoEmpresa ape,
                schemaBundle:AdmiTarea t 
        
                WHERE 
                t.id = d.tareaId
                AND d.detalleSolicitudId = $id_solicitud 
                AND pr.id = t.procesoId
                AND pr.id = ape.procesoId 
                AND ape.empresaCod = $codEmpresa
                AND t.id = tm.tareaId 
                AND (SELECT idh2.estado FROM schemaBundle:InfoDetalleHistorial idh2
                     WHERE idh2.id = (SELECT MAX(idh.id) FROM schemaBundle:InfoDetalleHistorial idh 
                                      WHERE idh.detalleId = d.id)
                                      ) = 'Aceptada'
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
    
    
    public function getUltimoIngresoMaterial($id_detalle, $material_cod)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('material')
           ->from('schemaBundle:InfoDetalleMaterial','material')
           ->where("material.detalleId = ?1 AND material.materialCod = ?2")
           ->setParameter(1, $id_detalle)
           ->setParameter(2, $material_cod)
           ->orderBy('material.id','DESC')
           ->setMaxResults(1);
        
        $query = $qb->getQuery();
        $results = $query->getResult();
       
        if(count($results)>0) return $results[0];
        else return false;
    }
    
     /**
      * Método que obtiene las características de materiales(Tipo de Transacción,Empresa,Login,Usuario)
      * asignados a un usuario en el repositorio NAF
      *
      * Costo: 10
      * 
      * @author Néstor Naula <nnaulal@telconet.ec>
      * @version 1.0 07-08-2018
      *
      * 
      * @author: Ronny Morán <rmoranc@telconet.ec>
      * @version 1.1 19-10-2018 Obteniendo materiales por tipoArticulo (Fibra) y por login
      *
      * 
      * @author: Ronny Morán <rmoranc@telconet.ec>
      * @version 1.2 15-11-2019 Devuelve si la bobina está bloqueada o desbloqueada según 
      *                         la fecha de despacho y cantidad disponible. 
      * 
      *
      * @author: Ronny Morán <rmoranc@telconet.ec>
      * @version 1.3 15-01-2020 Se obtiene la fecha de despacho de la bobina 
      * Costo: 10 
      *                         
      * @author: Wilmer Vera <wvera@telconet.ec>
      * @version 1.4 29-01-2020 Se obtienen valores desde parametros PARAMETROS_GENERALES_MOVIL
      *                         Se elimina variable que no esta siendo usada. 
      *                         Se editan variables del json haciendo uso de "camellCase".
      *                         Se agrega variable diasDiferencia para uso del móvil.  
      * 
      * @author: Jean Pierre Nazareno <jnazareno@telconet.ec>
      * @version 1.5 23-01-2020 Se agrega ORDER BY DESC para mejorar visualización
      * de bobinas en el móvil
      * Costo: 10 
      *    
      *
      * @author: Jeampier Carriel <jacarriel@telconet.ec>
      * @version 1.6 22-11-2021 Se obtienen nuevos valores de dias desde para las cuadrillas Satelite.
      *                         Se agrega validacion de cuadrilla Satelite para setear nuevo parametro de dias.
      *
      *
      * @author Jeampier Carriel <jacarriel@telconet.ec>
      * @version 1.7 24-01-2022 Se realizan modificaciones para agregar las cuadrillas con estado 'Prestado' y setear variables bind
      *
      * @param  $arrayParametros[ intIdPersona , strCodMaterial ]
      * @return $arrayResultado
      */
    public function obtenerCaracteristicasFibraPorUsuarioNaf($arrayParametros)
    {
        $arrayListaMaterialesNaf            = array();
        $arrayResultadoListaMaterialesNaf   = array();
        $intIdPersonaEmpresaRol             = $arrayParametros['intIdPersonaEmpresaRol'];
        $strLogin                           = $arrayParametros['login'];
        $strTipoArticulo                    = $arrayParametros['tipoArticulo'];
        $strTipoCustodio                    = $arrayParametros['strTipoCustodio'];
        $strTipoTransaccion                 = "Caso";
        
        $objRsm                             = new ResultSetMappingBuilder($this->_em);
        $objQuery                           = $this->_em->createNativeQuery(null, $objRsm);
        $strMateriales                      = "Fibra";
        $strAcceso                          = "ACCESO";
        $strBloqueada                       = "Bloqueada";
        $strDesbloqueada                    = "Desbloqueada";
        $strWhereInfo                       = "";
        $intDiasBloqueoBobina               = 0;
        $intCantBobinaBloqueo               = 0;
        $objDateActual                      = new \DateTime('now');
        $intDiasBloqueoBobinaSatelite       = 0;
                
        $arrayParamBobinaBloqueo            = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                                                 '', 
                                                                 '', 
                                                                 '', 
                                                                 'DIAS_BLOQUEO_BOBINA_DESPACHO', 
                                                                 '', 
                                                                 '', 
                                                                 ''
                                                                );

        if(is_array($arrayParamBobinaBloqueo))
        {
            $intDiasBloqueoBobina = !empty($arrayParamBobinaBloqueo['valor2']) ? $arrayParamBobinaBloqueo['valor2'] : "";
        }

          $arrayParamCantBobinaBloqueo            = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                                                 '', 
                                                                 '', 
                                                                 '', 
                                                                 'CANTIDAD_BLOQUEO_BOBINA', 
                                                                 '', 
                                                                 '', 
                                                                 ''
                                                                );

        if(is_array($arrayParamCantBobinaBloqueo))
        {
            $intCantBobinaBloqueo = !empty($arrayParamCantBobinaBloqueo['valor2']) ? $arrayParamCantBobinaBloqueo['valor2'] : "";
        }
        
        $arrayParamBobinaBloqueoSatelite = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS_GENERALES_MOVIL',
                                                                '',
                                                                '',
                                                                '',
                                                                'DIAS_BLOQUEO_BOBINA_DESPACHO_SATELITE',
                                                                '',
                                                                '',
                                                                ''
                                                            );

        if (is_array($arrayParamBobinaBloqueoSatelite)) 
        {
            $intDiasBloqueoBobinaSatelite = !empty($arrayParamBobinaBloqueoSatelite['valor2']) ? $arrayParamBobinaBloqueoSatelite['valor2'] : "";
        }

        
        if(isset($intIdPersonaEmpresaRol) && !empty($intIdPersonaEmpresaRol))
        {
            
            if(isset($strTipoArticulo) && !empty($strTipoArticulo))
            {
                $strWhereInfo .= " AND  CCO.TIPO_ARTICULO = :tipoarticulo";
                $objQuery->setParameter("tipoarticulo"       ,$strTipoArticulo); 
            }   
            if(isset($strLogin) && !empty($strLogin) && isset($strTipoCustodio) && !empty($strTipoCustodio) && $strTipoCustodio=='Cliente')
            {
                $strWhereInfo .= " AND CCO.LOGIN = :login";
                $objQuery->setParameter("login"              ,$strLogin);
            }else
            {
                $strWhereInfo .= " AND CCO.TIPO_TRANSACCION_ID != :TipoTransaccion";
                $objQuery->setParameter("TipoTransaccion"   ,$strTipoTransaccion);
                
                $strWhereInfoExternal .= "  AND ACC.CUSTODIO_ID = SRLT.CUSTODIO_ID ";
            }
            
            $strWhereInfo .= " AND CCO.CUSTODIO_ID = :idPersonaEmpresaRol 
                               AND CCO.FECHA_FIN >= TRUNC(SYSDATE) 
                               AND CCO.CANTIDAD>0  
                               ORDER BY CCO.FE_ASIGNACION DESC, CCO.CANTIDAD DESC";
            
            $objQuery->setParameter("idPersonaEmpresaRol"    ,$intIdPersonaEmpresaRol);
        
            $strWhereInfoExternal .= " AND ACC.TIPO_ARTICULO = :tipoarticuloDes 
                                       AND (ACC.TIPO_ACTIVIDAD = :despachoBodega
                                            OR  ACC.TIPO_ACTIVIDAD = :soporte)
                                       AND ACC.ID_CONTROL_ORIGEN IS NULL
                                       GROUP BY 
                                            SRLT.ID_CONTROL, 
                                            SRLT.ARTICULO_ID, 
                                            SRLT.ID_CARACTERISTICA,
                                            SRLT.EMPRESA_ID, 
                                            SRLT.CUSTODIO_ID, 
                                            SRLT.TIPO_TRANSACCION_ID, 
                                            SRLT.TRANSACCION_ID,
                                            SRLT.TIPO_ACTIVIDAD, 
                                            SRLT.LOGIN, 
                                            SRLT.DESCRIPCION, 
                                            SRLT.SUBGRUPO,
                                            SRLT.CANTIDAD,
                                            SRLT.TIPO_ARTICULO, 
                                            SRLT.NO_ARTICULO,
                                            SRLT.OBSERVACION 
                                        ORDER BY FE_CREACION DESC ";
            
            $objQuery->setParameter("tipoarticuloDes"       , $strMateriales);
            $objQuery->setParameter("despachoBodega"        , 'DespachoBodega');
            $objQuery->setParameter("soporte"               , 'Soporte');
            
            $strSql                            =   "SELECT 
                                                                SRLT.ID_CONTROL, 
                                                                SRLT.ARTICULO_ID, 
                                                                SRLT.ID_CARACTERISTICA,
                                                                SRLT.EMPRESA_ID, 
                                                                SRLT.CUSTODIO_ID, 
                                                                SRLT.TIPO_TRANSACCION_ID, 
                                                                SRLT.TRANSACCION_ID,
                                                                SRLT.TIPO_ACTIVIDAD, 
                                                                SRLT.LOGIN, 
                                                                SRLT.DESCRIPCION, 
                                                                SRLT.SUBGRUPO,
                                                                SRLT.CANTIDAD,
                                                                SRLT.TIPO_ARTICULO, 
                                                                SRLT.NO_ARTICULO,
                                                                SRLT.OBSERVACION,
                                                                MAX(ACC.FE_CREACION) AS FE_CREACION
                                                          FROM 
                                                          NAF47_TNET.ARAF_CONTROL_CUSTODIO ACC,
                                                          (
                                                                SELECT 
                                                                    CCO.ID_CONTROL, 
                                                                    CCO.ARTICULO_ID, 
                                                                    CCO.CARACTERISTICA_ID AS ID_CARACTERISTICA,
                                                                    CCO.EMPRESA_ID, 
                                                                    CCO.CUSTODIO_ID, 
                                                                    CCO.TIPO_TRANSACCION_ID, 
                                                                    CCO.TRANSACCION_ID,
                                                                    CCO.TIPO_ACTIVIDAD, 
                                                                    CCO.LOGIN, 
                                                                    COALESCE(DA.DESCRIPCION, 'FIBRA AUTOGENERADA') AS DESCRIPCION, 
                                                                    '' AS SUBGRUPO,
                                                                    CCO.CANTIDAD,
                                                                    CCO.TIPO_ARTICULO, 
                                                                    CCO.NO_ARTICULO,
                                                                    CCO.OBSERVACION
                                                                FROM
                                                                    NAF47_TNET.ARAF_CONTROL_CUSTODIO CCO
                                                                    LEFT JOIN NAF47_TNET.ARINDA DA ON 
                                                                    DA.NO_ARTI = CCO.NO_ARTICULO AND DA.NO_CIA = CCO.EMPRESA_ID 
                                                                WHERE
                                                                    CCO.TIPO_ACTIVIDAD != 'Retiro'
                                                                    {$strWhereInfo}  
                                                          ) SRLT 
                                                          WHERE 
                                                          ACC.ARTICULO_ID = SRLT.ARTICULO_ID
                                                          AND ACC.NO_ARTICULO = SRLT.NO_ARTICULO 
                                                          {$strWhereInfoExternal} ";  

            $objRsm->addScalarResult('ID_CONTROL',            'idControl',              'string');
            $objRsm->addScalarResult('ARTICULO_ID',           'articuloId',             'string');
            $objRsm->addScalarResult('ID_CARACTERISTICA',     'idCaracteristica',       'string');
            $objRsm->addScalarResult('EMPRESA_ID',            'empresaId',              'string');
            $objRsm->addScalarResult('CUSTODIO_ID',           'custodioId',             'string');
            $objRsm->addScalarResult('TIPO_TRANSACCION_ID',   'tipoTransaccionId',      'string');
            $objRsm->addScalarResult('TRANSACCION_ID',        'transaccionId',          'string');
            $objRsm->addScalarResult('TIPO_ACTIVIDAD',        'tipoActividad',          'string');
            $objRsm->addScalarResult('LOGIN',                 'login',                  'string');
            $objRsm->addScalarResult('DESCRIPCION',           'nombreMaterial',         'string');
            $objRsm->addScalarResult('SUBGRUPO',              'subgrupoMaterial',      'string');
            $objRsm->addScalarResult('CANTIDAD',              'cantidad',               'string');
            $objRsm->addScalarResult('TIPO_ARTICULO',         'tipoArticulo',          'string');
            $objRsm->addScalarResult('NO_ARTICULO',           'codMaterial',           'string');
            $objRsm->addScalarResult('OBSERVACION',           'observacion',            'string');
            $objRsm->addScalarResult('FE_CREACION',           'fechaAsignacion',       'string');
            
            $objQuery->setSQL($strSql);
            $arrayListaMaterialesNaf = $objQuery->getArrayResult();                  
            
        }
      
        if (!empty($arrayListaMaterialesNaf)) 
        {
            foreach ($arrayListaMaterialesNaf as &$arrayMateriales) 
            {
                if($arrayMateriales['tipoArticulo'] == $strMateriales) 
                {
                    $arrayMateriales['subgrupoMaterial'] = $strAcceso;
                }
             
                $objDateDiff = date_diff($objDateActual, new \DateTime($arrayMateriales['fechaAsignacion']));

                $arrayMateriales['strEstadoActivo'] = 'Activo';
                $arrayMateriales['strEsSatelite'] = 'S';
                $arrayMateriales['strEstadoPrestado'] = 'Prestado'; 
                
                $objAdmiCuadrilla = $this->_em->getRepository('schemaBundle:AdmiCuadrilla')->getCuadrillaSatelitePersonEmpresRol($arrayMateriales);  

                if (count($objAdmiCuadrilla['resultado']) > 0) 
                {
                    if (($objDateDiff->format("%a") <= $intDiasBloqueoBobinaSatelite) && ($arrayMateriales['cantidad'] >= $intCantBobinaBloqueo)) 
                    {
                        $arrayMateriales['bobinaAsignada'] = $strDesbloqueada;
                        $arrayMateriales['diasDiferencia'] = $objDateDiff->format("%a");
                    } else 
                    {
                        $arrayMateriales['bobinaAsignada'] = $strBloqueada;
                        $arrayMateriales['diasDiferencia'] = $objDateDiff->format("%a");
                    }
                } else 
                {
                    if (($objDateDiff->format("%a") <= $intDiasBloqueoBobina) && ($arrayMateriales['cantidad'] >= $intCantBobinaBloqueo)) 
                    {
                        $arrayMateriales['bobinaAsignada'] = $strDesbloqueada;
                        $arrayMateriales['diasDiferencia'] = $objDateDiff->format("%a");
                    } else 
                    {
                        $arrayMateriales['bobinaAsignada'] = $strBloqueada;
                        $arrayMateriales['diasDiferencia'] = $objDateDiff->format("%a");
                    }
                }
                
                $arrayResultadoListaMaterialesNaf[] = $arrayMateriales;
            }
        }
      
        return $arrayResultadoListaMaterialesNaf;
    }
    
    
     /**
     * Método que obtiene las características del material,  enviados desde el móvil para ser completados.
     *  
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 19-11-2018 
     *
     *  
     * @param  $arrayParametros[ $arrayParametros]
     * @return $arrayResultado
     */
    public function obtenerCaracteristicasFibraSeleccionada($arrayParametros)
    {
        $arrayListaMaterialesNaf            = array();
        $arrayResultadoListaMaterialesNaf   = array();
        
        $intCodEmpresa                      = $arrayParametros['codEmpresa'];
        $strUtilizado                       = $arrayParametros['tipoActividad'];
        $strCodMaterial                     = $arrayParametros['cod_material'];
        
        $objRsm                             = new ResultSetMappingBuilder($this->_em);
        $objQuery                           = $this->_em->createNativeQuery(null, $objRsm);
     
        $strWhereInfo  =   "";
        $strSql        =   "SELECT 
                            COSTO_MATERIAL,
                            PRECIO_VENTA_MATERIAL,
                            FACTURABLE
                            FROM 
                            DB_SOPORTE.ADMI_TAREA_MATERIAL 
                            WHERE 
                            ESTADO = 'Activo' ";
        
            if(isset($intCodEmpresa) && !empty($intCodEmpresa))
            {
                $strWhereInfo .= " AND empresa_cod = :codEmpresa";
                $objQuery->setParameter("codEmpresa"       ,$intCodEmpresa); 
              
            }       
            if(isset($strUtilizado) && !empty($strUtilizado))
            {
                $strWhereInfo .= " AND utilizado_en = :tipoActividad";
                $objQuery->setParameter("tipoActividad"       ,$strUtilizado); 
              
            }      
            if(isset($strCodMaterial) && !empty($strCodMaterial))
            {
                $strWhereInfo .= " AND material_cod = :cod_material";
                $objQuery->setParameter("cod_material"       ,$strCodMaterial); 
                
            }
            $strSql .= $strWhereInfo;
            
            $objRsm->addScalarResult('COSTO_MATERIAL',              'costo_material',          'string');			
            $objRsm->addScalarResult('PRECIO_VENTA_MATERIAL',       'precio_venta_material',   'string');
            $objRsm->addScalarResult('FACTURABLE',                  'facturar',                'boolean');

            $objQuery->setSQL($strSql);
            $arrayListaMaterialesNaf = $objQuery->getArrayResult();                  

        if(!empty($arrayListaMaterialesNaf))
        {
            foreach ($arrayListaMaterialesNaf as &$arrayMateriales) 
            {
                $arrayResultadoListaMaterialesNaf[] = $arrayMateriales;
            }
        }
        return $arrayResultadoListaMaterialesNaf;
    }
    
    
     /**
     * Método que obtiene el listado de materiales de su custodio 
     * asignados a un usuario en el repositorio NAF
     *  
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 31-10-2018 
     *
     * @author: Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.1 13-03-2019 Se optimiza método para estandarizar la obtención de materiales en NAF
     * 
     * Costo: 11
     *
     * 
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 20-05-2020 Se realiza la agrupación de materiales del técnico.
     * 
     * @author: Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.3 27-02-2020 Se agrega validacion por codigo de empresa para obtener el listado 
     *                         de materiales NAFT en Ecuanet. 
     * 
     * @param  $arrayParametros[ $arrayParametros]
     * @return $arrayResultado
     */
    public function obtenerCaracteristicasMaterialesPorUsuarioNaf($arrayParametros)
    {
        $arrayListaMaterialesNaf            = array();
        $arrayResultadoListaMaterialesNaf   = array();
        $intIdPersonaEmpresaRol             = $arrayParametros['intIdPersonaEmpresaRol'];
        $strTipoArticulo                    = $arrayParametros['tipoArticulo'];
        $intCodEmpresa                      = $arrayParametros['idEmpresa'];
        $strUtilizado                       = $arrayParametros['strUtilizado'];
        
        
        $objRsm                             = new ResultSetMappingBuilder($this->_em);
        $objQuery                           = $this->_em->createNativeQuery(null, $objRsm);
        $strMateriales = "Materiales";
        $strGeneral    = "GENERAL";
        $intCodEmpresaEN = 33;
        $intCodEmpresaMD = 18;
        $strWhereInfo  =   "";
        if($intCodEmpresa ==$intCodEmpresaEN)
        {
            $intCodEmpresa = $intCodEmpresaMD;
        }
        if(isset($intIdPersonaEmpresaRol) && !empty($intIdPersonaEmpresaRol))
        {
        $strSql        =   "SELECT
                            ATM.COSTO_MATERIAL,
                            ATM.PRECIO_VENTA_MATERIAL,
                            ATM.ID_TAREA_MATERIAL,
                            ATM.FACTURABLE,
                            0 ID_DETALLE_SOL_MATERIAL,
                            0 AS CANTIDAD_ESTIMADA,
                            0 AS CANTIDAD_CLIENTE,
                            0 AS CANTIDAD_USADA,
                            0 AS CANTIDAD_EXCEDENTE,
                            CCO.ARTICULO_ID,
                            CCO.ID_CONTROL,
                            ATM.CANTIDAD_MATERIAL AS CANTIDAD,--cantidad_empresa
                            CCO.CARACTERISTICA_ID AS ID_CARACTERISTICA,
                            CCO.EMPRESA_ID, --ver
                            CCO.CUSTODIO_ID,
                            CCO.TIPO_TRANSACCION_ID,
                            CCO.TRANSACCION_ID,
                            CCO.TIPO_ACTIVIDAD,
                            CCO.LOGIN,
                            CCO.NO_ARTICULO,
                            CCO.TIPO_ARTICULO,
                            DA.DESCRIPCION,
                            '' AS SUBGRUPO,
                            GROUPMAT.CANTIDAD_DIPONIBLE
                          FROM  
                            NAF47_TNET.ARAF_CONTROL_CUSTODIO    CCO, DB_SOPORTE.ADMI_TAREA_MATERIAL         ATM,
                            NAF47_TNET.ARINDA                    DA, (SELECT  
                               CCO.NO_ARTICULO, MAX(ID_CONTROL) AS ID_CONTROL, SUM(CANTIDAD) AS CANTIDAD_DIPONIBLE
                               from NAF47_TNET.ARAF_CONTROL_CUSTODIO CCO 
                               WHERE CCO.CUSTODIO_ID = :idPersonaEmpresaRol
                               AND CCO.ESTADO = :strEstadoCustodio
                               AND CCO.CANTIDAD != 0  
                               AND TIPO_ARTICULO = :tipoarticulo
                               GROUP BY 
                               CCO.NO_ARTICULO
                               ) GROUPMAT
                          WHERE
                            GROUPMAT.NO_ARTICULO            = ATM.MATERIAL_COD
                            AND GROUPMAT.ID_CONTROL         = CCO.ID_CONTROL
                            AND GROUPMAT.CANTIDAD_DIPONIBLE > 0
                            AND DA.NO_ARTI                  = CCO.NO_ARTICULO 
                            AND DA.NO_CIA                   = CCO.EMPRESA_ID
                            AND ATM.EMPRESA_COD             = :intCodEmpresa 
                            AND ATM.UTILIZADO_EN            = :strUtilizado 
                            AND ATM.ESTADO                  = :strEstadoTareaMaterial
                            ORDER BY CCO.FE_ASIGNACION DESC";
        
        $objQuery->setParameter("intCodEmpresa",$intCodEmpresa);
        $objQuery->setParameter("strEstadoTareaMaterial","Activo");
        $objQuery->setParameter("strUtilizado", $strUtilizado);
        $objQuery->setParameter("tipoarticulo"       ,$strTipoArticulo); 
        $objQuery->setParameter("idPersonaEmpresaRol"    ,$intIdPersonaEmpresaRol);
        $objQuery->setParameter("strEstadoCustodio"    ,"Asignado");
            
            $strSql .= $strWhereInfo;
            
            $objRsm->addScalarResult('COSTO_MATERIAL',              'costo_material',          'string');			
            $objRsm->addScalarResult('PRECIO_VENTA_MATERIAL',       'precio_venta_material',   'string');
            $objRsm->addScalarResult('ID_TAREA_MATERIAL',           'id_tarea_material',       'integer');
            $objRsm->addScalarResult('FACTURABLE',                  'facturar',                'boolean');
            $objRsm->addScalarResult('ID_DETALLE_SOL_MATERIAL',     'id_detalle_sol_material', 'integer');
            $objRsm->addScalarResult('CANTIDAD_ESTIMADA',           'cantidad_estimada',       'integer');
            $objRsm->addScalarResult('CANTIDAD_CLIENTE',            'cantidad_cliente',        'integer');
            $objRsm->addScalarResult('CANTIDAD_USADA',              'cantidad_usada',          'integer');
            $objRsm->addScalarResult('CANTIDAD_EXCEDENTE',          'cantidad_excedente',      'integer');
            $objRsm->addScalarResult('ARTICULO_ID',                 'cod_material',            'string');
            $objRsm->addScalarResult('ID_CONTROL',                  'id_control',              'integer');
            $objRsm->addScalarResult('CANTIDAD',                    'cantidad_empresa',        'integer');
            $objRsm->addScalarResult('ID_CARACTERISTICA',           'idCaracteristica',        'string');
            $objRsm->addScalarResult('EMPRESA_ID',                  'empresaId',               'string');
            $objRsm->addScalarResult('CUSTODIO_ID',                 'custodioId',              'string');
            $objRsm->addScalarResult('TIPO_TRANSACCION_ID',         'tipoTransaccionId',       'string');
            $objRsm->addScalarResult('TRANSACCION_ID',              'transaccionId',           'string');
            $objRsm->addScalarResult('TIPO_ACTIVIDAD',              'tipoActividad',           'string');
            $objRsm->addScalarResult('LOGIN',                       'login',                   'string');
            $objRsm->addScalarResult('NO_ARTICULO',                 'no_articulo',             'string');
            $objRsm->addScalarResult('TIPO_ARTICULO',               'tipo_articulo',           'string');
            $objRsm->addScalarResult('DESCRIPCION',                 'nombre_material',         'string');
            $objRsm->addScalarResult('SUBGRUPO',                    'subgrupo_material',       'string');
            $objRsm->addScalarResult('ID_DETALLE_SOLICITUD',        'id_detalle_solicitud',    'integer');
            $objRsm->addScalarResult('ID_DETALLE',                  'id_detalle',              'integer');
            $objRsm->addScalarResult('ID_TAREA',                    'id_tarea',                'integer');
            $objRsm->addScalarResult('NOMBRE_TAREA',                'nombre_tarea',            'string');
            $objRsm->addScalarResult('NOMBRE_PROCESO',              'nombre_proceso',          'string');
            $objRsm->addScalarResult('ID_PROCESO',                  'id_proceso',              'integer');

            $objQuery->setSQL($strSql);
            $arrayListaMaterialesNaf = $objQuery->getArrayResult();                  
           
        }

        if(!empty($arrayListaMaterialesNaf))
        {
            foreach ($arrayListaMaterialesNaf as &$arrayMateriales) 
            {   
                if($arrayMateriales['tipo_articulo']==$strMateriales)
                {
                    $arrayMateriales['subgrupo_material'] = $strGeneral;
                }
                $arrayResultadoListaMaterialesNaf[] = $arrayMateriales;
            }
        }
        return $arrayResultadoListaMaterialesNaf;
    }

    
    
    /**
     * Función que guarda los registros de materiales en el NAF (ARAF_CONTROL_CUSTODIO) para la fibra óptica.
     * 
     * @author: Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-08-2018  
     * 
     * 
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 19-10-2018 Agregando campos de Observación y Tipo de artículo, se modifica el Tipo de actividad
     *                         y se elimina el parámetro Pn_IdCaso
     * 
     * @author: Andrés Astudillo <aastudillo@telconet.ec>
     * @version 1.3 09-12-2019 Agregando parámetro en el procedimiento P_TRANSFIERE_CUSTODIO para no realizar commit
     *                         dentro del procedimiento si es llamado desde el sistema NAF
     * @since 1.2
     * 
     * 
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4 20-05-2020 Se asigna id_control cero para transferencias de materiales.
     *
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 05-06-2020 Se asigna correctamente id_control cero para transferencias de materiales.
     *  
     * @param array $arrayParametros
     * @return string $strResultado
     */    
    
    public function putguardarRegistroNaf($arrayParametros)
    {   
        $strResultado   = NULL;
        $objCon         = oci_connect(  $arrayParametros['strUser'],
                                    $arrayParametros['strPass'], 
                                    $arrayParametros['objDb']) or $this->throw_exceptionOci(oci_error());
        $strSql         =   "BEGIN
                                NAF47_TNET.AFK_CONTROL_CUSTODIO.P_TRANSFIERE_CUSTODIO
                                (
                                    :Pv_NumeroSerie,
                                    :Pn_IdCaracteristica,
                                    :Pv_IdEmpresa,
                                    :Pn_IdCustodioEnt,
                                    :Pn_CantidadEnt,
                                    :Pn_IdCustodioRec,
                                    :Pn_CantidadRec,
                                    :Pv_TipoTransaccion,
                                    :Pv_IdTransaccion,
                                    :Pv_TipoActividad,
                                    :Pn_IdTarea,
                                    :Pv_Login,
                                    :Pv_LoginProcesa,
                                    :Pv_MensajeError,
                                    :Pn_IdControl,
                                    :Pv_Observacion,
                                    :Pv_TipoArticulo,
                                    TRUE
                                );
                            END;";
   
        $objStmt    = oci_parse($objCon, $strSql);
        foreach ($arrayParametros['arrayControlCusto'] as &$objCustodio) 
        {
            if($objCustodio['tipoArticulo'] == "Materiales")
            {
                $objCustodio['idControl'] = "0";
            }
            oci_bind_by_name($objStmt,'Pv_NumeroSerie', $objCustodio['numeroSerie']);
            oci_bind_by_name($objStmt,'Pn_IdCaracteristica', $objCustodio['caracteristicaId']);
            oci_bind_by_name($objStmt,'Pv_IdEmpresa', $objCustodio['empresaId']);
            oci_bind_by_name($objStmt,'Pn_IdCustodioEnt', $arrayParametros['intidPersonaEntrega']);
            oci_bind_by_name($objStmt,'Pn_CantidadEnt', $objCustodio['cantidadEnt']);
            oci_bind_by_name($objStmt,'Pn_IdCustodioRec', $arrayParametros['intidPersonaRecibe']);
            oci_bind_by_name($objStmt,'Pn_CantidadRec', $objCustodio['cantidadRec']);
            oci_bind_by_name($objStmt,'Pv_TipoTransaccion', 
                $objCustodio['tipoTransaccion']!=null?$objCustodio['tipoTransaccion']:$arrayParametros['tipoActividad']);
            oci_bind_by_name($objStmt,'Pv_IdTransaccion',$objCustodio['transaccionId']);
            oci_bind_by_name($objStmt,'Pv_TipoActividad',$arrayParametros['tipoActividad']);
            oci_bind_by_name($objStmt,'Pn_IdTarea',$objCustodio['tareaId']);
            oci_bind_by_name($objStmt,'Pv_Login', $objCustodio['login']);
            oci_bind_by_name($objStmt,'Pv_LoginProcesa', $objCustodio['loginEmpleado']);
            oci_bind_by_name($objStmt,'Pv_MensajeError', $strResultado); 
            oci_bind_by_name($objStmt,'Pn_IdControl', $objCustodio['idControl']);
            oci_bind_by_name($objStmt,'Pv_Observacion', $arrayParametros['observacion']);
            oci_bind_by_name($objStmt,'Pv_TipoArticulo', $objCustodio['tipoArticulo']);
            oci_execute($objStmt);
        }
        
        oci_close($objCon);
        $strResultado = oci_error($objStmt);
        if($strResultado==NULL){
            $strResultado = "Realizado";
        }
        
        return $strResultado;
    }
    
	/**
     * Función que obtiene los activos de un custodio
     * 
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 05-12-2018
	 *
	 * Se agregó el Articulo Id a la respuesta de la función
	 * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 17-01-2019
     * 
     * Se cambia la forma de obtener los resultados, ahora apuntamos 
     * a una vista creada en el NAF : "NAF47_TNET.V_ARTICULOS_CUSTODIO"
     * la cual tiene un filtro que nos permite sumarizar los materiales.
     * @author: Wilmer Vera <wvera@telconet.ec>
     * @version 1.2 19-01-2019
	 *
	 * Se reemplaza la vista V_ARTICULOS_CUSTODIO por un query para mejorar la respuesta
	 * @author: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 09-03-2023
     *
     * @param array $arrayParametros
     * @return string $strResultado
     */   
	public function getActivos($arrayRequest)
    {        
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
		$objReturnResponse['registros'] = [];
		$objReturnResponse['total'] 	= 0;

        $objRsmb->addScalarResult('ID_CONTROL', 'intIdControl','integer');
		$objRsmb->addScalarResult('CUSTODIO_ID', 'intCustodioId','integer');
		$objRsmb->addScalarResult('NO_ARTICULO', 'strNoArticulo','string');
		$objRsmb->addScalarResult('ARTICULO_ID', 'strArticuloId','string');
        $objRsmb->addScalarResult('TIPO_CUSTODIO', 'strTipoCustodio','string');
		$objRsmb->addScalarResult('TIPO_ARTICULO', 'strTipoArticulo','string');
        $objRsmb->addScalarResult('DESCRIPCION', 'strDescripcion','string');
		$objRsmb->addScalarResult('LOGIN', 'strLogin','string');
        $objRsmb->addScalarResult('CANTIDAD', 'intCantidad','integer');
        $objRsmb->addScalarResult('FECHA', 'strFechaCreacion','string');
			
        $strSQL = "SELECT "
                        . " * "
                        . " FROM "
                        . " (SELECT ACC.CUSTODIO_ID,
                                    ACC.EMPRESA_CUSTODIO_ID,
                                    (SELECT  CASE
                                             when P.TIPO_IDENTIFICACION = 'RUC'
                                             then P.RAZON_SOCIAL
                                             else P.APELLIDOS||' '||P.NOMBRES
                                         END
                                     FROM DB_COMERCIAL.INFO_PERSONA P,
                                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                          DB_GENERAL.INFO_EMPRESA_ROL IER,
                                          DB_GENERAL.ADMI_ROL AR,
                                          DB_GENERAL.ADMI_TIPO_ROL ATR
                                     WHERE IPER.ID_PERSONA_ROL = ACC.CUSTODIO_ID
                                     AND ATR.DESCRIPCION_TIPO_ROL = ACC.TIPO_CUSTODIO
                                     AND P.ID_PERSONA = IPER.PERSONA_ID
                                     AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                                     AND IER.ROL_ID = AR.ID_ROL
                                     AND AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL) NOMBRE_CUSTODIO,
                                     ACC.TIPO_CUSTODIO,
                                     ACC.ARTICULO_ID,
                                     ACC.EMPRESA_ID,
                                     ACC.LOGIN,
                                     ACC.TIPO_ARTICULO,
                                     ACC.NO_ARTICULO,
                                     NVL(( SELECT DA.DESCRIPCION
                                           FROM NAF47_TNET.ARINDA DA
                                           WHERE DA.NO_ARTI = ACC.NO_ARTICULO
                                           AND DA.NO_CIA = ACC.EMPRESA_ID), 'FIBRA GENERADA') AS DESCRIPCION,
                                     SUM(ACC.VALOR_BASE) AS VALOR_BASE,
                                     SUM(ACC.CANTIDAD) As CANTIDAD,
                                     --
                                     CASE
                                       WHEN ACC.TIPO_CUSTODIO = 'Empleado' THEN
                                         (SELECT MAX(FE_ASIGNACION)
                                          FROM NAF47_TNET.ARAF_CONTROL_CUSTODIO
                                          WHERE ARTICULO_ID = ACC.ARTICULO_ID
                                          AND CUSTODIO_ID = ACC.CUSTODIO_ID
                                          AND ID_CONTROL_ORIGEN is Null
                                          AND TIPO_ACTIVIDAD = 'DespachoBodega'
                                          AND FECHA_INICIO <= NAF47_TNET.GEK_VAR.F_GET_FECHA_DESDE)
                                       ELSE
                                         (SELECT MAX(FECHA_INICIO)
                                          FROM NAF47_TNET.ARAF_CONTROL_CUSTODIO
                                          WHERE ARTICULO_ID = ACC.ARTICULO_ID
                                          AND CUSTODIO_ID = ACC.CUSTODIO_ID
                                          AND FECHA_INICIO <= NAF47_TNET.GEK_VAR.F_GET_FECHA_DESDE
                                          AND FECHA_FIN >= NAF47_TNET.GEK_VAR.F_GET_FECHA_HASTA)
                                       END FECHA_ASIGNACION,
                                     --
                                     (SELECT MAX(FE_CREACION) from NAF47_TNET.ARAF_CONTROL_CUSTODIO
                                         WHERE ARTICULO_ID = ACC.ARTICULO_ID
                                         AND ID_CONTROL_ORIGEN is Null
                                         AND TIPO_ACTIVIDAD = 'DespachoBodega'
                                         AND ACC.CUSTODIO_ID = :intCustodioId
                                         AND ACC.TIPO_ARTICULO = :strTipo) FECHA
                             FROM NAF47_TNET.ARAF_CONTROL_CUSTODIO ACC
                             WHERE ACC.FECHA_INICIO <= NAF47_TNET.GEK_VAR.F_GET_FECHA_DESDE
                             AND ACC.FECHA_FIN >= NAF47_TNET.GEK_VAR.F_GET_FECHA_HASTA
                             AND ACC.TIPO_CUSTODIO = :strTipoCustodio
                             AND ACC.CUSTODIO_ID = :intCustodioId
                             AND ACC.TIPO_ARTICULO = :strTipo
                             GROUP BY
                                 ACC.CUSTODIO_ID,
                                 ACC.EMPRESA_CUSTODIO_ID,
                                 ACC.TIPO_CUSTODIO,
                                 ACC.ARTICULO_ID,
                                 ACC.EMPRESA_ID,
                                 ACC.LOGIN,
                                 Acc.Tipo_Articulo,
                                 ACC.NO_ARTICULO
                             having (SUM(ACC.CANTIDAD) != 0)) ACC "
                        . " WHERE "
                        . " ACC.CANTIDAD > :intCantidad ";

		$objQuery->setParameter('intCantidad', 0);
		$objQuery->setParameter('strFibra', 'Fibra');
		$objQuery->setParameter('strCodFibra', '00-00-00-000');
		
		if(isset($arrayRequest['strTipoCustodio']) && !empty($arrayRequest['strTipoCustodio']))
		{
			$objQuery->setParameter('strTipoCustodio', $arrayRequest['strTipoCustodio']);
		}
        else
        {
            $objQuery->setParameter('strTipoCustodio', '');
        }
		
		if(isset($arrayRequest['intCustodioId']) && !empty($arrayRequest['intCustodioId']))
		{
			$objQuery->setParameter('intCustodioId', $arrayRequest['intCustodioId']);
		}
        else
        {
            $objQuery->setParameter('intCustodioId', 1);
        }
		
		if(isset($arrayRequest['strTipo']) && !empty($arrayRequest['strTipo']))
		{
			$objQuery->setParameter('strTipo', $arrayRequest['strTipo']);
		}
        else
        {
            $objQuery->setParameter('strTipo', '');
        }

		$objQuery->setSQL($strSQL);
		$intTotal   = count($objQuery->getResult());
		if(isset($arrayRequest['intStart']) && isset($arrayRequest['intLimit']) && $arrayRequest['intLimit'] > 0)
		{
			$objQuery->setParameter('intStart', intval($arrayRequest['intStart']));
			$objQuery->setParameter('intLimit', intval($arrayRequest['intLimit']));
			$strSQL .= ' LIMIT :intStart, :intLimit ';
		}
		$objQuery->setSQL($strSQL);
		$objReturnResponse['registros'] = $objQuery->getResult();
		$objReturnResponse['total'] 	= $intTotal;

        return $objReturnResponse;
    }

    /**
     * Método encargado de obtener los equipos asignado a un técnico.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-05-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 10-02-2022 - Verifica si el tipo de red del servicio es GPON_MPLS y no posee id persona,
     *                           no se realiza la busqueda por el id persona.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 29-09-2022 - Se agrega parámetro para obtener los elementos por varios tipos de elementos.
     * 
     * @author Jubert Goya <jgoya@telconet.ec>
     * @version 1.3 06-02-2023 - Se agrega parámetro booleanNoValidarPersona para hacer opcional la busqueda
     *                           de persona encargada, no realiza la busqueda por id persona.          
     * 
     * @param Array $arrayParametros [
     *                                  serviceUtil     : Objeto de la clase UtilService.
     *                                  strUsuario      : Usuario quien realiza la petición.
     *                                  strIpUsuario    : Ip del usuario quien realiza la petición.
     *                                  strIdEmpresa    : Id de la empresa.
     *                                  intIdPersona    : Id de la persona.
     *                                  strNumeroSerie  : Numero de serie del elemento.
     *                                  strModelo       : Modelo del elemento.
     *                                  strTipoElemento : Tipo de dispositivo.
     *                                  arrayTiposElementos : Array de Tipos de dispositivos.
     *                                  strDescripcion  : Descripción del elemento.
     *                                  booleanRedGponMpls : Tipo de red GPON MPLS
     *                              ]
     * @return Array $arrayResultado
     */
    public function obtenerEquiposAsignados($arrayParametros)
    {
        $serviceUtil     =  $arrayParametros['serviceUtil'];
        $strUsuario      =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario    =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $booleanNoValidarPersona = false;
        $strEstadoEquipo = "PI";
        
        if($arrayParametros['strIdEmpresa'] == 33)
        {
            $arrayParametros['strIdEmpresa'] = 18;
        }

        if( ( isset($arrayParametros["booleanRedGponMpls"]) && $arrayParametros["booleanRedGponMpls"] )
            || (isset($arrayParametros["booleanNoValidarPersona"]) && $arrayParametros["booleanNoValidarPersona"] ) )
        {
            $booleanNoValidarPersona = true;
        }

        try
        {
            if (isset($arrayParametros['strEstadoEquipo']) && !empty($arrayParametros['strEstadoEquipo']))
            {
                $strEstadoEquipo = $arrayParametros['strEstadoEquipo'];
            }

            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSelect = "SELECT IPE.ID_PERSONA,      ".
                                "IPER.ID_PERSONA_ROL, ".
                                "ACC.ID_CONTROL,      ".
                                "ACC.NO_ARTICULO,     ".
                                "ACC.EMPRESA_ID,      ".
                                "IAI.NUMERO_SERIE,    ".
                                "IAI.MODELO,          ".
                                "ATE.NOMBRE_TIPO_ELEMENTO, ".
                                "AR.DESCRIPCION, ".
                                "IAI.MAC ".
                         "FROM NAF47_TNET.IN_ARTICULOS_INSTALACION     IAI,  ".
                              "NAF47_TNET.ARAF_CONTROL_CUSTODIO        ACC,  ".
                              "NAF47_TNET.ARINDA                       AR,   ".
                              "DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME,  ".
                              "DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO   ATE,  ".
                              "DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL   IPER, ".
                              "DB_COMERCIAL.INFO_PERSONA               IPE   ".
                         "WHERE IAI.ID_ARTICULO      =  ACC.NO_ARTICULO ".
                           "AND IAI.NUMERO_SERIE     =  ACC.ARTICULO_ID ".
                           "AND AR.NO_ARTI           =  IAI.ID_ARTICULO ".
                           "AND IAI.MODELO           =  AME.NOMBRE_MODELO_ELEMENTO ".
                           "AND ATE.ID_TIPO_ELEMENTO =  AME.TIPO_ELEMENTO_ID ".
                           "AND ACC.CUSTODIO_ID      =  IPER.ID_PERSONA_ROL  ".
                           "AND IPER.PERSONA_ID      =  IPE.ID_PERSONA       ".
                           "AND IAI.ESTADO           = :strEstadoEquipo      ".
                           "AND ACC.CANTIDAD         > :intCantidad          ".
                           "AND ACC.ESTADO           = :strEstado            ".
                           "AND AME.ESTADO           = :strEstadoModelo      ".
                           "AND IAI.TIPO_ARTICULO    = :strTipoArticuloIns   ".
                           "AND ACC.TIPO_ARTICULO    = :strTipoArticulo      ";

            //condiccion para el id persona
            $strWherePersona = "AND IPE.ID_PERSONA   = :intIdPersona ";

            //verificar si posee id persona y el tipo de red no sea gpon mpls
            if((isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona'])) || !$booleanNoValidarPersona)
            {
                $strSelect = $strSelect.$strWherePersona;
            }

            // Condicion para obtener lo equipos permitidos sin MAC para realizar una instalacion o cambio de equipo
            $strWhere = "AND (IAI.MAC IS NOT NULL OR ATE.NOMBRE_TIPO_ELEMENTO IN ( SELECT D.VALOR1  ".
                                                                                    "FROM DB_GENERAL.ADMI_PARAMETRO_CAB C,    ".
                                                                                         "DB_GENERAL.ADMI_PARAMETRO_DET D     ".
                                                                                   "WHERE C.ID_PARAMETRO     = D.PARAMETRO_ID ".
                                                                                     "AND C.ESTADO           = D.ESTADO       ".
                                                                                     "AND C.NOMBRE_PARAMETRO = 'EQUIPOS_PERMITIDOS_SIN_MAC'".
                                                                                     "AND C.ESTADO           = 'Activo'))     ";

            if (isset($arrayParametros['boolPerteneceElementoNodo']) && is_bool($arrayParametros['boolPerteneceElementoNodo']) &&
                    $arrayParametros['boolPerteneceElementoNodo'])
            {
                $strWhere = "";
            }

            if (isset($arrayParametros['strIdEmpresa']) && !empty($arrayParametros['strIdEmpresa']))
            {
                $strWhere .= "AND ACC.EMPRESA_ID   = :strIdEmpresa ";
                $objNativeQuery->setParameter('strIdEmpresa', $arrayParametros['strIdEmpresa']);
            }

            if (isset($arrayParametros['strNumeroSerie']) && !empty($arrayParametros['strNumeroSerie']))
            {
                $strWhere .= 'AND UPPER(IAI.NUMERO_SERIE) LIKE UPPER(:strNumeroSerie) ';
                $objNativeQuery->setParameter('strNumeroSerie', '%'.$arrayParametros['strNumeroSerie'].'%');
            }

            if (isset($arrayParametros['strModelo']) && !empty($arrayParametros['strModelo']))
            {
                $strWhere .= 'AND UPPER(IAI.MODELO) LIKE UPPER(:strModelo) ';
                $objNativeQuery->setParameter('strModelo', '%'.$arrayParametros['strModelo'].'%');
            }

            if (isset($arrayParametros['strTipoElemento']) && !empty($arrayParametros['strTipoElemento']))
            {
                $strWhere .= 'AND UPPER(ATE.NOMBRE_TIPO_ELEMENTO) = UPPER(:strTipoElemento) ';
                $objNativeQuery->setParameter('strTipoElemento', $arrayParametros['strTipoElemento']);
            }
            elseif (isset($arrayParametros['arrayTiposElementos']) && !empty($arrayParametros['arrayTiposElementos']))
            {
                $arrayTiposElementos = array();
                foreach($arrayParametros['arrayTiposElementos'] as $strTipoElemento)
                {
                    $arrayTiposElementos[] = strtoupper($strTipoElemento);
                }
                $strWhere .= 'AND UPPER(ATE.NOMBRE_TIPO_ELEMENTO) IN (:strTiposElementos) ';
                $objNativeQuery->setParameter('strTiposElementos', $arrayTiposElementos);
            }

            if (isset($arrayParametros['strDescripcion']) && !empty($arrayParametros['strDescripcion']))
            {
                $strWhere .= 'AND UPPER(IAI.DESCRIPCION) LIKE UPPER(:strDescripcion) ';
                $objNativeQuery->setParameter('strDescripcion', '%'.$arrayParametros['strDescripcion'].'%');
            }

            $strOrderBy = 'ORDER BY ATE.NOMBRE_TIPO_ELEMENTO ASC, IAI.MODELO ASC';

            $objNativeQuery->setParameter('strEstado'         , 'Asignado');
            $objNativeQuery->setParameter('strEstadoModelo'   , 'Activo');
            $objNativeQuery->setParameter('strTipoArticuloIns', 'AF');
            $objNativeQuery->setParameter('strTipoArticulo'   , 'Equipos');
            $objNativeQuery->setParameter('intCantidad'       ,  0);
            $objNativeQuery->setParameter('strEstadoEquipo'   ,  $strEstadoEquipo);
            $objNativeQuery->setParameter('intIdPersona'      ,  $arrayParametros['intIdPersona']);

            $objResultSetMap->addScalarResult('ID_PERSONA'           , 'idPersona'           , 'integer');
            $objResultSetMap->addScalarResult('ID_PERSONA_ROL'       , 'idPersonaRol'        , 'string');
            $objResultSetMap->addScalarResult('ID_CONTROL'           , 'idControl'           , 'integer');
            $objResultSetMap->addScalarResult('NO_ARTICULO'          , 'noArticulo'          , 'string');
            $objResultSetMap->addScalarResult('EMPRESA_ID'           , 'idEmpresa'           , 'integer');
            $objResultSetMap->addScalarResult('NUMERO_SERIE'         , 'serieElemento'       , 'string');
            $objResultSetMap->addScalarResult('MODELO'               , 'modeloElemento'      , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_TIPO_ELEMENTO' , 'tipoElemento'        , 'string');
            $objResultSetMap->addScalarResult('DESCRIPCION'          , 'descripcionElemento' , 'string');
            $objResultSetMap->addScalarResult('MAC'                  , 'macElemento'         , 'string');

            $objNativeQuery->setSQL($strSelect.$strWhere.$strOrderBy);
            $arrayDatos = $objNativeQuery->getResult();

            if (empty($arrayDatos) || count($arrayDatos) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            $arrayRespuesta = array("status" => true, "result" => $arrayDatos);
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al obtener los datos';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.obtenerEquiposAsignados: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.obtenerEquiposAsignados: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'obtenerEquiposAsignados',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'obtenerEquiposAsignados',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtener los elemento adicionales 
     * agregar al cliente o al nodo asignado a un técnico.
     *
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.0 10-1-2023
     *
     *
     * @param Array $arrayParametros [
     *                                  serviceUtil     : Objeto de la clase UtilService.
     *                                  strUsuario      : Usuario quien realiza la petición.
     *                                  strIpUsuario    : Ip del usuario quien realiza la petición.
     *                                  strIdEmpresa    : Id de la empresa.
     *                                  intIdPersona    : Id de la persona.
     *                                  strNumeroSerie  : Numero de serie del elemento.
     *                                  strModelo       : Modelo del elemento.
     *                                  boolPerteneceElemento: Pertenece al cliente o al nodo
     *                                  strDescripcion  : Descripción del elemento.
     *                              ]
     * @return Array $arrayResultado
     */
    public function getElementosAdicionalesClienteNodo($arrayParametros)
    {
        $serviceUtil      =  $arrayParametros['serviceUtil'];
        $strUsuario       =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario     =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $strEstadoEquipo  = "PI";
        $strFechaDesde    = $arrayParametros["strFechaDesde"];
        $strFechaHasta    = $arrayParametros["strFechaHasta"];
        $strDateFechaDesde= "";
        $strDateFechaHasta= "";
        $strPertenece     = "";
        $strGroupBy       = '';

        try
        {
            if (isset($arrayParametros['strEstadoEquipo']) && !empty($arrayParametros['strEstadoEquipo']))
            {
                $strEstadoEquipo = $arrayParametros['strEstadoEquipo'];
            }

            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSelect = "SELECT IPE.ID_PERSONA,      ".
                                "IPER.ID_PERSONA_ROL, ".
                                "ACC.ID_CONTROL,      ".
                                "ACC.NO_ARTICULO,     ".
                                "ACC.EMPRESA_ID,      ".
                                "IAI.NUMERO_SERIE,    ".
                                "IAI.MODELO,          ".
                                "ATE.NOMBRE_TIPO_ELEMENTO, ".
                                "AR.DESCRIPCION, ".
                                "IAI.MAC, ".
                                "TO_CHAR(acc.FE_ASIGNACION, 'YYYY/MM/DD') AS FE_ASIGNACION ".
                         "FROM NAF47_TNET.IN_ARTICULOS_INSTALACION     IAI  ".
                    "JOIN NAF47_TNET.ARAF_CONTROL_CUSTODIO  ACC ON IAI.ID_ARTICULO =  ACC.NO_ARTICULO AND IAI.NUMERO_SERIE =  ACC.ARTICULO_ID  ".
                              "JOIN NAF47_TNET.ARINDA AR ON AR.NO_ARTI  =  IAI.ID_ARTICULO   ".
                              "JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME ON IAI.MODELO  =  AME.NOMBRE_MODELO_ELEMENTO  ".
                              "JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO   ATE ON ATE.ID_TIPO_ELEMENTO =  AME.TIPO_ELEMENTO_ID  ".
                              "JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL   IPER ON ACC.CUSTODIO_ID      =  IPER.ID_PERSONA_ROL ".
                              "JOIN DB_COMERCIAL.INFO_PERSONA IPE ON IPER.PERSONA_ID      =  IPE.ID_PERSONA   ".
                         "WHERE  IAI.ESTADO          = :strEstadoEquipo ".
                           "AND ACC.EMPRESA_ID       = :strIdEmpresa         ".
                           "AND ACC.CANTIDAD         > :intCantidad          ".
                           "AND ACC.ESTADO           = :strEstado            ".
                           "AND AME.ESTADO           = :strEstadoModelo      ".
                           "AND IAI.TIPO_ARTICULO    = :strTipoArticuloIns   ".
                           "AND ACC.TIPO_ARTICULO    = :strTipoArticulo      ";

            //condiccion para el id persona
            $strWherePersona = "AND IPE.ID_PERSONA   = :intIdPersona ";

            //verificar si posee id persona y el tipo de red no sea gpon mpls
            if((isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona'])))
            {
                $strSelect = $strSelect.$strWherePersona;
            }

            if($strFechaDesde != "")
            {
                $arrayFechaDesde = explode("-", $strFechaDesde);
                $strDateFechaDesde = date("Y/m/d", strtotime($arrayFechaDesde[2] . "-" . $arrayFechaDesde[1] . "-" . $arrayFechaDesde[0]));
    
                $strWhereDesde .= " AND TO_CHAR(ACC.FE_ASIGNACION, 'YYYY/MM/DD') >= :paramFechaDesde ";
                $strSelect = $strSelect.$strWhereDesde;
                    
            }
    
            if($strFechaHasta != "")
            {
                $arrayFechaHasta = explode("-", $strFechaHasta);
                $strDateFechaHasta = date ( 'Y/m/d' , strtotime ($arrayFechaHasta[2] . "-" . $arrayFechaHasta[1] . "-" . $arrayFechaHasta[0]));
    
                $strWhereHasta .= " AND TO_CHAR(ACC.FE_ASIGNACION, 'YYYY/MM/DD') <= :paramFechaHasta ";
                $strSelect = $strSelect.$strWhereHasta;
            }

            if (isset($arrayParametros['boolPerteneceElemento']) && is_bool($arrayParametros['boolPerteneceElemento']) &&
            $arrayParametros['boolPerteneceElemento'])
            {
                $strPertenece = "ELEMENTO_ADICIONAL_CLIENTE";
            }
            else
            {
                $strPertenece = "ELEMENTO_ADICIONAL_NODO";
            }

            // Condicion para obtener lo equipos parametrizados que pertenecen al cliente o al nodo
            $strWhere = " AND ATE.NOMBRE_TIPO_ELEMENTO IN (SELECT D.VALOR1  
                                FROM DB_GENERAL.ADMI_PARAMETRO_CAB C,    
                                    DB_GENERAL.ADMI_PARAMETRO_DET D     
                                WHERE C.ID_PARAMETRO      = D.PARAMETRO_ID 
                                    AND C.ESTADO           = D.ESTADO       
                                    AND C.NOMBRE_PARAMETRO = :strNombreParametro
                                    AND D.DESCRIPCION      = :strPertenece 
                                    AND C.ESTADO           = :strEstadoModelo)  ";

            if (isset($arrayParametros['strNumeroSerie']) && !empty($arrayParametros['strNumeroSerie']))
            {
                $strWhere .= 'AND UPPER(IAI.NUMERO_SERIE) LIKE UPPER(:strNumeroSerie) ';
                $objNativeQuery->setParameter('strNumeroSerie', '%'.$arrayParametros['strNumeroSerie'].'%');
            }

            if (isset($arrayParametros['strModelo']) && !empty($arrayParametros['strModelo']))
            {
                $strWhere .= 'AND UPPER(IAI.MODELO) LIKE UPPER(:strModelo) ';
                $objNativeQuery->setParameter('strModelo', '%'.$arrayParametros['strModelo'].'%');
            }

            if (isset($arrayParametros['strDescripcion']) && !empty($arrayParametros['strDescripcion']))
            {
                $strWhere .= 'AND UPPER(IAI.DESCRIPCION) LIKE UPPER(:strDescripcion) ';
                $objNativeQuery->setParameter('strDescripcion', '%'.$arrayParametros['strDescripcion'].'%');
            }

            $strOrderBy = 'ORDER BY ATE.NOMBRE_TIPO_ELEMENTO ASC, IAI.MODELO ASC';
            $strGroupBy = ' GROUP BY IPE.ID_PERSONA, IPER.ID_PERSONA_ROL, ACC.ID_CONTROL, ACC.NO_ARTICULO, ACC.EMPRESA_ID, IAI.NUMERO_SERIE,    
            IAI.MODELO, ATE.NOMBRE_TIPO_ELEMENTO, AR.DESCRIPCION, IAI.MAC, ACC.FE_ASIGNACION ';

            $objNativeQuery->setParameter('strEstado'         , 'Asignado');
            $objNativeQuery->setParameter('strEstadoModelo'   , 'Activo');
            $objNativeQuery->setParameter('strTipoArticuloIns', 'AF');
            $objNativeQuery->setParameter('strTipoArticulo'   , 'Equipos');
            $objNativeQuery->setParameter('strPertenece'      ,  $strPertenece);
            $objNativeQuery->setParameter('strNombreParametro',  'ELEMENTOS_ADICIONALES_CLIENTE_NODO');
            $objNativeQuery->setParameter('intCantidad'       ,  0);
            $objNativeQuery->setParameter('strEstadoEquipo'   ,  $strEstadoEquipo);
            $objNativeQuery->setParameter('strIdEmpresa'      ,  $arrayParametros['strIdEmpresa']);
            $objNativeQuery->setParameter('intIdPersona'      ,  $arrayParametros['intIdPersona']);
            $objNativeQuery->setParameter('paramFechaDesde'   ,  trim($strDateFechaDesde) );
            $objNativeQuery->setParameter('paramFechaHasta'   ,  trim($strDateFechaHasta) );

            $objResultSetMap->addScalarResult('ID_PERSONA'           , 'idPersona'           , 'integer');
            $objResultSetMap->addScalarResult('ID_PERSONA_ROL'       , 'idPersonaRol'        , 'string');
            $objResultSetMap->addScalarResult('ID_CONTROL'           , 'idControl'           , 'integer');
            $objResultSetMap->addScalarResult('NO_ARTICULO'          , 'noArticulo'          , 'string');
            $objResultSetMap->addScalarResult('EMPRESA_ID'           , 'idEmpresa'           , 'integer');
            $objResultSetMap->addScalarResult('NUMERO_SERIE'         , 'serieElemento'       , 'string');
            $objResultSetMap->addScalarResult('MODELO'               , 'modeloElemento'      , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_TIPO_ELEMENTO' , 'tipoElemento'        , 'string');
            $objResultSetMap->addScalarResult('DESCRIPCION'          , 'descripcionElemento' , 'string');
            $objResultSetMap->addScalarResult('MAC'                  , 'macElemento'         , 'string');
            $objResultSetMap->addScalarResult('FE_ASIGNACION'        , 'feAsignacion'        , 'string');

            $objNativeQuery->setSQL($strSelect.$strWhere.$strGroupBy.$strOrderBy);
            $arrayDatos = $objNativeQuery->getResult();

            if (empty($arrayDatos) || count($arrayDatos) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            $arrayRespuesta = array("status" => true, "result" => $arrayDatos);
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al obtener los datos';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.getElementosAdicionalesClienteNodo: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.getElementosAdicionalesClienteNodo: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'getElementosAdicionalesClienteNodo',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'getElementosAdicionalesClienteNodo',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado el registro del control custodio.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 19-07-2021
     *
     * @param Array $arrayParametros [
     *                                  serviceUtil     : Objeto de la clase UtilService.
     *                                  strUsuario      : Usuario quien realiza la petición.
     *                                  strIpUsuario    : Ip del usuario quien realiza la petición.
     *                                  intIdCustodio   : Id del custodio del elemento.
     *                                  strNumeroSerie  : Número de serie del elemento.
     *                              ]
     * @return Array $arrayResultado
     */
    public function obtenerControlCustodio($arrayParametros)
    {
        $serviceUtil     =  $arrayParametros['serviceUtil'];
        $strUsuario      =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario    =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $strTipoParametro     =  $arrayParametros['strTipoParametro'];
        $strEstadoEquipo = 'PI';

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if (isset($arrayParametros['strEstadoEquipo']) && !empty($arrayParametros['strEstadoEquipo']))
            {
                $strEstadoEquipo = $arrayParametros['strEstadoEquipo'];
            }

            // si viene por cambio de elemento no se toma en cuenta la IN_ARTICULOS_INSTALACION
            if($strTipoParametro === 'CambioElemento')
            {
                
                $strSelect = "SELECT               ".
                           "ACC.ID_CONTROL,    ".
                           "ACC.CUSTODIO_ID,   ".
                           "ACC.TIPO_CUSTODIO, ".
                           "ACC.ARTICULO_ID  ".
                         "FROM ".
                           "NAF47_TNET.ARAF_CONTROL_CUSTODIO    ACC      ".
                         "WHERE ACC.CUSTODIO_ID    = :intIdCustodio      ".
                           "AND ACC.CANTIDAD       > :intCantidad        ".
                           "AND ACC.ESTADO         = :strEstado          ".
                           "AND ACC.TIPO_ARTICULO  = :strTipoArticulo    ".
                           "AND ACC.ARTICULO_ID   = :strNumeroSerie     ";

                $objNativeQuery->setParameter('intIdCustodio'     ,  $arrayParametros['intIdCustodio']);
                $objNativeQuery->setParameter('intCantidad'       ,  0);
                $objNativeQuery->setParameter('strEstado'         , 'Asignado');
                $objNativeQuery->setParameter('strTipoArticulo'   , 'Equipos');
                $objNativeQuery->setParameter('strNumeroSerie'    ,  $arrayParametros['strNumeroSerie']);

                $objResultSetMap->addScalarResult('ID_CONTROL'    , 'idControl'           , 'integer');
                $objResultSetMap->addScalarResult('CUSTODIO_ID'   , 'idCustodio'          , 'integer');
                $objResultSetMap->addScalarResult('TIPO_CUSTODIO' , 'tipoCustodio'        , 'string');
                $objResultSetMap->addScalarResult('ARTICULO_ID'  , 'articuloId'          , 'string');

            }else
            {
                
                $strSelect = "SELECT               ".
                           "ACC.ID_CONTROL,    ".
                           "ACC.CUSTODIO_ID,   ".
                           "ACC.TIPO_CUSTODIO, ".
                           "IAI.NUMERO_SERIE,  ".
                           "IAI.MODELO,        ".
                           "IAI.MAC,           ".
                           "IAI.DESCRIPCION    ".
                         "FROM ".
                           "NAF47_TNET.IN_ARTICULOS_INSTALACION IAI,     ".
                           "NAF47_TNET.ARAF_CONTROL_CUSTODIO    ACC      ".
                         "WHERE IAI.ID_ARTICULO    =  ACC.NO_ARTICULO    ".
                           "AND IAI.NUMERO_SERIE   =  ACC.ARTICULO_ID    ".
                           "AND ACC.CUSTODIO_ID    = :intIdCustodio      ".
                           "AND ACC.CANTIDAD       > :intCantidad        ".
                           "AND ACC.ESTADO         = :strEstado          ".
                           "AND ACC.TIPO_ARTICULO  = :strTipoArticulo    ".
                           "AND IAI.NUMERO_SERIE   = :strNumeroSerie     ".
                           "AND IAI.ESTADO         = :strEstadoEquipo    ".
                           "AND IAI.TIPO_ARTICULO  = :strTipoArticuloIns ";

                $objNativeQuery->setParameter('intIdCustodio'     ,  $arrayParametros['intIdCustodio']);
                $objNativeQuery->setParameter('intCantidad'       ,  0);
                $objNativeQuery->setParameter('strEstado'         , 'Asignado');
                $objNativeQuery->setParameter('strTipoArticulo'   , 'Equipos');
                $objNativeQuery->setParameter('strNumeroSerie'    ,  $arrayParametros['strNumeroSerie']);
                $objNativeQuery->setParameter('strEstadoEquipo'   ,  $strEstadoEquipo);
                $objNativeQuery->setParameter('strTipoArticuloIns', 'AF');

                $objResultSetMap->addScalarResult('ID_CONTROL'    , 'idControl'           , 'integer');
                $objResultSetMap->addScalarResult('CUSTODIO_ID'   , 'idCustodio'          , 'integer');
                $objResultSetMap->addScalarResult('TIPO_CUSTODIO' , 'tipoCustodio'        , 'string');
                $objResultSetMap->addScalarResult('NUMERO_SERIE'  , 'serieElemento'       , 'string');
                $objResultSetMap->addScalarResult('MODELO'        , 'modeloElemento'      , 'string');
                $objResultSetMap->addScalarResult('MAC'           , 'macElemento'         , 'string');
                $objResultSetMap->addScalarResult('DESCRIPCION'   , 'descripcionElemento' , 'string');

            }  

            $objNativeQuery->setSQL($strSelect);
            $arrayDatos = $objNativeQuery->getResult();

            if (empty($arrayDatos) || count($arrayDatos) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            $arrayRespuesta = array("status" => true, "data" => $arrayDatos);
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al obtener los datos';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.obtenerControlCustosdio: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.obtenerControlCustosdio: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'obtenerControlCustosdio',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'obtenerControlCustosdio',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Función que realiza la carga y descarga de los activos en el naf.
     *
     * @author: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 27-05-2021
     *
     * @param Array $arrayParametros
     * @return Array $arrayResultado
     */
    public function registrarCargaDescargaActivos($arrayParametros)
    {
        $strMensaje = str_pad($strMensaje, 3000, " ");
        $strSql     = "BEGIN ".
                        "NAF47_TNET.AFK_CONTROL_CUSTODIO.P_TRANSFIERE_CUSTODIO(:Pv_NumeroSerie,"     .
                                                                              ":Pn_IdCaracteristica,".
                                                                              ":Pv_IdEmpresa,"       .
                                                                              ":Pn_IdCustodioEnt,"   .
                                                                              ":Pn_CantidadEnt,"     .
                                                                              ":Pn_IdCustodioRec,"   .
                                                                              ":Pn_CantidadRec,"     .
                                                                              ":Pv_TipoTransaccion," .
                                                                              ":Pv_IdTransaccion,"   .
                                                                              ":Pv_TipoActividad,"   .
                                                                              ":Pn_IdTarea,"         .
                                                                              ":Pv_Login,"           .
                                                                              ":Pv_LoginProcesa,"    .
                                                                              ":Pv_MensajeError,"    .
                                                                              ":Pn_IdControl,"       .
                                                                              ":Pv_Observacion,"     .
                                                                              ":Pv_TipoArticulo,"    .
                                                                              " FALSE);".
                      "END;";

        $objStmt = $this->_em->getConnection()->prepare($strSql);

        foreach($arrayParametros['arrayControlCusto'] as $objCustodio)
        {
            $objStmt->bindParam('Pv_NumeroSerie'     , $objCustodio['numeroSerie']);
            $objStmt->bindParam('Pn_IdCaracteristica', $objCustodio['caracteristicaId']);
            $objStmt->bindParam('Pv_IdEmpresa'       , $objCustodio['empresaId']);
            $objStmt->bindParam('Pn_IdCustodioEnt'   , $arrayParametros['intidPersonaEntrega']);
            $objStmt->bindParam('Pn_CantidadEnt'     , $objCustodio['cantidadEnt']);
            $objStmt->bindParam('Pn_IdCustodioRec'   , $arrayParametros['intidPersonaRecibe']);
            $objStmt->bindParam('Pn_CantidadRec'     , $objCustodio['cantidadRec']);
            $objStmt->bindParam('Pv_TipoTransaccion' , $objCustodio['tipoTransaccion']);
            $objStmt->bindParam('Pv_IdTransaccion'   , $objCustodio['transaccionId']);
            $objStmt->bindParam('Pv_TipoActividad'   , $arrayParametros['tipoActividad']);
            $objStmt->bindParam('Pn_IdTarea'         , $objCustodio['tareaId']);
            $objStmt->bindParam('Pv_Login'           , $objCustodio['login']);
            $objStmt->bindParam('Pv_LoginProcesa'    , $objCustodio['loginEmpleado']);
            $objStmt->bindParam('Pv_MensajeError'    , $strMensaje);
            $objStmt->bindParam('Pn_IdControl'       , $objCustodio['idControl']);
            $objStmt->bindParam('Pv_Observacion'     , $arrayParametros['observacion']);
            $objStmt->bindParam('Pv_TipoArticulo'    , $objCustodio['tipoArticulo']);
            $objStmt->execute();
        }

        if (empty($strMensaje) || $strMensaje === null)
        {
            $strMensaje = "Realizado";
        }

        return $strMensaje;
    }


    /**
     * Función que realiza la carga y descarga de los activos en el naf.
     *
     * @author: Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 10-02-2022
     * 
     */
    public function putguardarRegistroNaf2($arrayParametros)
    {   
        $strResultado   = null;
        $objCon         = oci_connect(  $arrayParametros['strUser'],
                                    $arrayParametros['strPass'], 
                                    $arrayParametros['objDb']);
                                    
        if($objCon == null)
        {                      
            $this->throw_exceptionOci(oci_error());
        }

        $strSql         =   "BEGIN ".
                            "NAF47_TNET.AFK_CONTROL_CUSTODIO.P_TRANSFIERE_CUSTODIO ".
                            "( ".
                            ":Pv_NumeroSerie, ".
                            ":Pn_IdCaracteristica, ".
                            ":Pv_IdEmpresa, ".
                            ":Pn_IdCustodioEnt, ".
                            ":Pn_CantidadEnt, ".
                            ":Pn_IdCustodioRec, ".
                            ":Pn_CantidadRec, ".
                            ":Pv_TipoTransaccion, ".
                            ":Pv_IdTransaccion, ".
                            ":Pv_TipoActividad, ".
                            ":Pn_IdTarea, ".
                            ":Pv_Login, ".
                            ":Pv_LoginProcesa, ".
                            ":Pv_MensajeError, ".
                            ":Pn_IdControl, ".
                            ":Pv_Observacion, ".
                            ":Pv_TipoArticulo, ".
                            "TRUE ".
                            "); ".
                            "END;";
   
        $objStmt    = oci_parse($objCon, $strSql);
        
        if($arrayParametros['tipoArticulo'] == "Materiales")
        {
            $arrayParametros['idControl'] = "0";
        }
        oci_bind_by_name($objStmt,'Pv_NumeroSerie', $arrayParametros['numeroSerie']);
        oci_bind_by_name($objStmt,'Pn_IdCaracteristica', $arrayParametros['caracteristicaId']);
        oci_bind_by_name($objStmt,'Pv_IdEmpresa', $arrayParametros['empresaId']);
        oci_bind_by_name($objStmt,'Pn_IdCustodioEnt', $arrayParametros['intidPersonaEntrega']);
        oci_bind_by_name($objStmt,'Pn_CantidadEnt', $arrayParametros['cantidadEnt']);
        oci_bind_by_name($objStmt,'Pn_IdCustodioRec', $arrayParametros['intidPersonaRecibe']);
        oci_bind_by_name($objStmt,'Pn_CantidadRec', $arrayParametros['cantidadRec']);
        oci_bind_by_name($objStmt,'Pv_TipoTransaccion', 
            $arrayParametros['tipoTransaccion']!=null?$arrayParametros['tipoTransaccion']:$arrayParametros['tipoActividad']);
        oci_bind_by_name($objStmt,'Pv_IdTransaccion',$arrayParametros['transaccionId']);
        oci_bind_by_name($objStmt,'Pv_TipoActividad',$arrayParametros['tipoActividad']);
        oci_bind_by_name($objStmt,'Pn_IdTarea',$arrayParametros['tareaId']);
        oci_bind_by_name($objStmt,'Pv_Login', $arrayParametros['login']);
        oci_bind_by_name($objStmt,'Pv_LoginProcesa', $arrayParametros['loginEmpleado']);
        oci_bind_by_name($objStmt,'Pv_MensajeError', $strResultado); 
        oci_bind_by_name($objStmt,'Pn_IdControl', $arrayParametros['idControl']);
        oci_bind_by_name($objStmt,'Pv_Observacion', $arrayParametros['observacion']);
        oci_bind_by_name($objStmt,'Pv_TipoArticulo', $arrayParametros['tipoArticulo']);
        oci_execute($objStmt);
        
        
        oci_close($objCon);
        $strResultado = oci_error($objStmt);
        if($strResultado==null)
        {
            $strResultado = "Realizado";
        }
        
        return $strResultado;
    }

    /**
     * Función que actualiza el estado en Naf de la serie del cpe.
     *
     * @author: Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 13-10-2022
     * @param Array $arrayParametros
     * @return Array $arrayResultado
     * 
     */
    public function updateEstadoNafSerieFisica($arrayParametros)
    {
        $serviceUtil          =  $arrayParametros['serviceUtil'];
        $strUsuario           =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario         =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $strSerie             =  $arrayParametros['strNumeroSerie'];
        $strIdInstalacion     =  $arrayParametros['strIdInstalacion'];
        $strEstadoNaf         =  $arrayParametros['strEstadoNaf'];
        $strUsuarioMod        =  $arrayParametros['strUsrModif'];
        $intSaldo             =  0;

        try 
        {
            $objResultSetMap  = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $arrayRespuesta = array();

            if($strEstadoNaf == 'IN')
            {
                $strSql  = " UPDATE NAF47_TNET.IN_ARTICULOS_INSTALACION "
                . " SET ESTADO = '" . $strEstadoNaf . "', "
                . " SALDO = $intSaldo , "
                . " USR_ULT_MOD = '" . $strUsuarioMod . "', "
                . " FE_ULT_MOD = SYSDATE "
                . " WHERE NUMERO_SERIE = '" . $strSerie. "' "
                . " AND ID_INSTALACION = '" . $strIdInstalacion. "' "; 
            }
            else
            {
                $strSql  = " UPDATE NAF47_TNET.IN_ARTICULOS_INSTALACION "
                . " SET ESTADO = '" . $strEstadoNaf . "', "
                . " USR_ULT_MOD = '" . $strUsuarioMod . "', "
                . " FE_ULT_MOD = SYSDATE "
                . " WHERE NUMERO_SERIE = '" . $strSerie. "' "
                . " AND ID_INSTALACION = '" . $strIdInstalacion. "' ";      
            }    
           
            $objNativeQuery->setSQL($strSql);
            
            $objRespuesta = $objNativeQuery->getResult();

            $arrayRespuesta = array("status" => true, "data" => $objRespuesta);

        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al actualizar Registro';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.updateEstadoNafSerieFisica: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.updateEstadoNafSerieFisica: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'updateEstadoNafSerieFisica',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'updateEstadoNafSerieFisica',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
            
        return $arrayRespuesta;
    }

    /**
     * Función que actualiza MAC de la serie del cpe.
     *
     * @author: Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 13-10-2022
     * @param Array $arrayParametros
     * @return Array $arrayResultado
     * 
     */
    public function updateMacSerieFisica($arrayParametros)
    {
        $serviceUtil          =  $arrayParametros['serviceUtil'];
        $strUsuario           =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario         =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $strSerie             =  $arrayParametros['strNumeroSerie'];
        $strMacNueva          =  $arrayParametros['strMacNueva'];
        $strUsuarioMod        =  $arrayParametros['strUsrModif'];
        $objRespuesta = 0;

        try 
        {
            $objResultSetMap  = new ResultSetMappingBuilder($this->_em);
            
            $arrayRespuesta = array();
   
            $strSql  = " UPDATE NAF47_TNET.IN_ARTICULOS_INSTALACION "
                     . " SET MAC = '" . $strMacNueva . "', "
                     . " USR_ULT_MOD = '" . $strUsuarioMod . "',"
                     . " FE_ULT_MOD =  SYSDATE "
                     . " WHERE NUMERO_SERIE = '" . $strSerie. "' ";        

            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $objNativeQuery->setSQL($strSql);

            $arrayRespuesta = array("status" => true, "data" => $objNativeQuery->getResult());

        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al actualizar Registro';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.updateMacSerieFisica: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.updateMacSerieFisica: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'updateMacSerieFisica',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'updateMacSerieFisica',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
            
        return $arrayRespuesta;
    }

    /**
     * Obtener informacion de serie en articulos instalacion o control_custodio
     *
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 18-10-2022
     * @param Array $arrayParametros [strNumeroSerie  : Numero de serie del elemento.]
     * @return Array $arrayResultado
     *
     */
    public function getResponsableCpe($arrayParametros)
    {
        $serviceUtil          =  $arrayParametros['serviceUtil'];
        $strUsuario           =  $arrayParametros["strUsuario"]   ? $arrayParametros["strUsuario"]   : "Telcos+";
        $strIpUsuario         =  $arrayParametros["strIpUsuario"] ? $arrayParametros["strIpUsuario"] : "127.0.0.1";
        $strTipoParametro     =  $arrayParametros['strTipoParametro'];
        $strBuscar            = 'buscarArControlCust';

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $arrayRespuesta = array();
            $strSerie       = $arrayParametros['strSerie'];

            if (isset($arrayParametros['strTipoParametro']) && !empty($arrayParametros['strTipoParametro']))
            {
                $strBuscar = $arrayParametros['strTipoParametro'];
            }

            // si viene por cambio de elemento no se toma en cuenta la IN_ARTICULOS_INSTALACION
            if($strBuscar === 'buscarArControlCust')
            {
                
                $strSelect = "SELECT               ".
                           "ACC.ID_CONTROL,    ".
                           "ACC.CUSTODIO_ID,   ".
                           "ACC.TIPO_CUSTODIO, ".
                           "ACC.ARTICULO_ID  ".
                         "FROM ".
                           "NAF47_TNET.ARAF_CONTROL_CUSTODIO    ACC      ".
                         "WHERE ".
                           "ACC.ESTADO             = :strEstado          ".
                           "AND ACC.TIPO_ARTICULO  = :strTipoArticulo    ".
                           "AND ACC.CANTIDAD       > :intCantidad        ".
                           "AND ACC.TIPO_CUSTODIO  = :strEmpleado         ".
                           "AND ACC.ARTICULO_ID    = :strNumeroSerie     ";

                $objNativeQuery->setParameter('strEstado'         , 'Asignado');
                $objNativeQuery->setParameter('strEmpleado'       , 'Empleado');
                $objNativeQuery->setParameter('strTipoArticulo'   , 'Equipos');
                $objNativeQuery->setParameter('strNumeroSerie'    ,  $strSerie);
                $objNativeQuery->setParameter('intCantidad'       ,  0);

                $objResultSetMap->addScalarResult('ID_CONTROL'    , 'idControl'           , 'integer');
                $objResultSetMap->addScalarResult('CUSTODIO_ID'   , 'idCustodioACC'          , 'integer');
                $objResultSetMap->addScalarResult('TIPO_CUSTODIO' , 'tipoCustodio'        , 'string');
                $objResultSetMap->addScalarResult('ARTICULO_ID'  , 'articuloId'          , 'string');

            }
            else
            {
                
                $strSelect = "SELECT           ".
                           "IAI.ID_CUSTODIO,   ".
                           "IAI.NUMERO_SERIE,  ".
                           "IAI.MODELO,        ".
                           "IAI.MAC,           ".
                           "IAI.ESTADO,        ".
                           "IAI.TIPO_PROCESO   ".
                         "FROM ".
                           "NAF47_TNET.IN_ARTICULOS_INSTALACION IAI      ".
                         "WHERE ".
                           "IAI.CANTIDAD           > :intCantidad        ".
                           "AND IAI.ESTADO         = :strEstado          ".
                           "AND IAI.NUMERO_SERIE   = :strNumeroSerie     ".
                           "AND IAI.TIPO_ARTICULO  = :strTipoArticuloIns ";

                $objNativeQuery->setParameter('intCantidad'       ,  0);
                $objNativeQuery->setParameter('strEstado'         , 'PI');
                $objNativeQuery->setParameter('strNumeroSerie'    ,  $strSerie);
                $objNativeQuery->setParameter('strTipoArticuloIns', 'AF');

                
                $objResultSetMap->addScalarResult('ID_CUSTODIO'   , 'idCustodioIAI'      , 'integer');
                $objResultSetMap->addScalarResult('TIPO_PROCESO'  , 'tipoProceso'        , 'string');
                $objResultSetMap->addScalarResult('NUMERO_SERIE'  , 'serieElemento'       , 'string');
                $objResultSetMap->addScalarResult('MODELO'        , 'modeloElemento'      , 'string');
                $objResultSetMap->addScalarResult('MAC'           , 'macElemento'         , 'string');
                $objResultSetMap->addScalarResult('ESTADO'        , 'estado'              , 'string');

            }  

            $objNativeQuery->setSQL($strSelect);
            $arrayDatos = $objNativeQuery->getResult();

            $arrayRespuesta = array("status" => true, "data" => $arrayDatos);
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al obtener los datos';
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            error_log("InfoDetalleMaterialRepository.getResponsableCpe: ".$strCodigo.'- 1 -'.$objException->getMessage());
            error_log("InfoDetalleMaterialRepository.getResponsableCpe: ".$strCodigo.'- 2 -'.json_encode($arrayParametros));

            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'getResponsableCpe',
                                           $strCodigo.'- 1 -'.$objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $serviceUtil->insertError('InfoDetalleMaterialRepository',
                                          'getResponsableCpe',
                                           $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false, 'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Función que actualiza custodio en el naf.
     *
     * @author: Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0 24-10-2022
     *
     * @param Array $arrayParametros
     * @return Array $arrayResultado
     */
    public function cambioCustodio($arrayParametros)
    {
        $strStatus  = str_pad($strStatus, 3000, " ");
        $strMensaje = str_pad($strMensaje, 3000, " ");

        $strSql     = "BEGIN ".
                        "NAF47_TNET.AFK_REGULARIZA_CUSTODIO.P_CAMBIO_CUSTODIO(:Pv_NumeroSerie,"     .
                                                                              ":Pn_IdCustodioEnt,"   .
                                                                              ":Pn_CantidadEnt,"     .
                                                                              ":Pn_IdCustodioRec,"   .
                                                                              ":Pn_CantidadRec,"     .
                                                                              ":Pv_LoginProcesa,"    .
                                                                              ":Pv_MensajeError,"    .
                                                                              ":Pv_Status,"    .
                                                                              ":Pn_IdControl,"       .
                                                                              " FALSE);".
                      "END;";

        $objStmt = $this->_em->getConnection()->prepare($strSql);

        $objStmt->bindParam('Pv_NumeroSerie'     , $arrayParametros['numeroSerie']);
        $objStmt->bindParam('Pn_IdCustodioEnt'   , $arrayParametros['intidPersonaEntrega']);
        $objStmt->bindParam('Pn_CantidadEnt'     , $arrayParametros['cantidadEnt']);
        $objStmt->bindParam('Pn_IdCustodioRec'   , $arrayParametros['intidPersonaRecibe']);
        $objStmt->bindParam('Pn_CantidadRec'     , $arrayParametros['cantidadRec']);
        $objStmt->bindParam('Pv_LoginProcesa'    , $arrayParametros['loginEmpleado']);
        $objStmt->bindParam('Pv_MensajeError'    , $strMensaje);
        $objStmt->bindParam('Pv_Status'          , $strStatus);
        $objStmt->bindParam('Pn_IdControl'       , $arrayParametros['idControl']);
        $objStmt->execute();

        $arrayResponse            = array();
        $arrayResponse['status']  = $strStatus;
        $arrayResponse['message'] = $strMensaje;

        return $arrayResponse;
    }
}
