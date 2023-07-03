<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTareaRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Esta funcion retorna un JSON con la lista de las tareas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 18-12-2015 Se realizan ajustes para presentar las tareas en base al tipo de caso seleccionado en la
    *                         creacion de los casos
    *
    * @version 1.0
    *
    * @param array   $parametros
    *
    * @return array $resultado
    *
    */
    public function generarJson($parametros)
    {
        $arr_encontrados = array();
        $datos           = array();

        $em_general      = $parametros["em_general"];
        $datos           = $this->getRegistros($parametros);
        $registros       = $datos["registros"];
        $registrosTotal  = $datos["total"];

        if ($registros)
        {
            $num = $registrosTotal;
            foreach ($registros as $data)
            {        
                $nombreRol = "";
                if($data->getRolAutorizaId())
                {    
                    $objRol    = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($data->getRolAutorizaId());
                    $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
                }   
                
                $arr_encontrados[]=array('id_tarea'           => $data->getId(),
                                         'nombre_proceso'     => trim($data->getProcesoId() ? $data->getProcesoId()->getNombreProceso() : "-" ),
                                         'nombre_rol_autoria' => trim($nombreRol),
                                         'nombre_tarea_ant'   => trim($data->getTareaAnteriorId() ? $data->getTareaAnteriorId()
                                                                                                         ->getNombreTarea() : "-" ),
                                         'nombre_tarea_sig'   => trim($data->getTareaSiguienteId() ? $data->getTareaSiguienteId()
                                                                                                          ->getNombreTarea() : "-" ),
                                         'nombre_tarea'       => trim($data->getNombreTarea()),
                                         'peso'               => ($data->getPeso() ? $data->getPeso() : 0.00),
                                         'costo'              => ($data->getCosto() ? $data->getCosto() : 0.00),
                                         'precio_promedio'    => ($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
                                         'estado'             => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                 'Eliminado':'Activo'),
                                         'action1'            => 'button-grid-show',
                                         'action2'            => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                 'icon-invisible':'button-grid-edit'),
                                         'action3'            => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,'encontrados' => array('id_tarea'           => 0        ,'nombre_proceso'   => 'Ninguno',
                                                                       'nombre_rol_autoria' => 'Ninguno','nombre_tarea_ant' => 'Ninguno',
                                                                       'nombre_tarea_sig'   => 'Ninguno','nombre_tarea'     => 'Ninguno',
                                                                       'peso'               => '0'      ,'costo'            => '0',
                                                                       'precio_promedio'    => '0'      ,'tarea_id'         =>  0 ,
                                                                       'proceso_nombre'     => 'Ninguno','estado'           => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF     = json_encode($arr_encontrados);
                $resultado = '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }


    /**
    * getRegistros
    *
    * Esta funcion retorna la lista de las tareas
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.6 01-07-2020 Se realizan ajustes para obtener las tareas y procesos activos.
    * @since 1.5
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.5 03-01-2019 Se modifica el filtro para obtener las tareas por empresa, cuando el requerimiento viene del móvil
    * @since 1.4 
    * 
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.4 04-09-2018 Se filtra los motivos de fin de tarea que se desea visualizar en el móvil operaciones
    * @since 1.3 
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.3 25-01-2016 Se realizan ajustes para que se presenten las tareas de acuerdo al parámetro esPlanMantenimiento
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.2 30-12-2015 Se realizan ajustes para presentar todas las tareas cuando no se envie ninguna empresa por session
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 18-12-2015 Se realizan ajustes para presentar las tareas en base al tipo de caso seleccionado en la
    *                         creacion de los casos
    *
    * @version 1.0
    *
    * @param array   $parametros
    *
    * @return array $resultado
    *
    */
    public function getRegistros($parametros)
	{
        $boolBusqueda       = false; 
        $where              = "";
        $query              = $this->_em->createQuery();
        $queryCount         = $this->_em->createQuery();

        $nombre             = $parametros["nombre"];
        $estado             = $parametros["estado"];
        $codEmpresa         = $parametros["codEmpresa"];
        $start              = $parametros["start"];
        $limit              = $parametros["limit"];
        $visible            = $parametros["visible"]?$parametros["visible"]:"Todos";
        $tipoCaso           = $parametros["tipoCaso"]?$parametros["tipoCaso"]:"";
        $strVisualizaMovil  = $parametros["visualizaMovil"];
       
        //Se consulta el tipo de caso Movilizacion y se extrae el proceso que tiene realacionado para segun esto,presentar las tareas realacionadas
        //a este proceso
        if($tipoCaso && $tipoCaso != "")
        {
            $existeTipoCaso = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get("TIPO CASO POR PROCESOS", "", "", "","", $tipoCaso, "", "");

            $parametroProceso = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                          ->get("PROCESOS TIPO CASO MOVILIZACION", "", "", "","", "", "", "");

            $proceso          = $parametroProceso[0]['valor1'];

        }

        if($estado!="Todos")
        {
            $boolBusqueda = true;
            if($estado=="Activo")
            {
				$where .= "WHERE t.estado not in (:arrayEstado) ";
                $query->setParameter("arrayEstado", array('Eliminado','Inactivo'));
                $queryCount->setParameter("arrayEstado", array('Eliminado','Inactivo'));
            }
            else
            {
				$where .= "WHERE LOWER(t.estado) like LOWER(:varEstado) ";
                $query->setParameter("varEstado", $estado);
                $queryCount->setParameter("varEstado", $estado);
            }
        }
        else
        {
            $where .= "WHERE t.estado is not null ";
        }
        //Si el tipo de caso fue encontrado se filtra por el proceso que tenga configurado
        if($tipoCaso)
        {
            if($existeTipoCaso)
            {
                $where .= " AND p.id = :varProceso ";
                $query->setParameter("varProceso", $proceso);
                $queryCount->setParameter("varProceso", $proceso);
            }
            else
            {
                $where .= " AND p.id <> :varProceso ";
                $query->setParameter("varProceso", $proceso);
                $queryCount->setParameter("varProceso", $proceso);
            }
        }
		
        if($nombre!="")
        {
            $boolBusqueda = true;
			$where .= "AND LOWER(t.nombreTarea) like LOWER(:varNombreTarea) ";
            $query->setParameter("varNombreTarea", '%'.$nombre.'%');
            $queryCount->setParameter("varNombreTarea", '%'.$nombre.'%');

        }
		if($strVisualizaMovil!="")
        {
			$where .= " AND t.visualizarMovil = :varVisualizarMovil  ";
            $query->setParameter("varVisualizarMovil", $strVisualizaMovil);
            $queryCount->setParameter("varVisualizarMovil", $strVisualizaMovil);

        }
        if(isset($parametros["idTareaActual"]))
        {
            if($parametros["idTareaActual"] && $parametros["idTareaActual"]!="")
            {
                $boolBusqueda = true;
                    $where .= "AND t.id NOT IN (:varTareaActual) ";
                    $query->setParameter("varTareaActual", $parametros["idTareaActual"]);
                    $queryCount->setParameter("varTareaActual", $parametros["idTareaActual"]);
            }
        }

        if(isset($parametros["idProceso"]))
        {
            if($parametros["idProceso"] && $parametros["idProceso"]!="")
            {
                $boolBusqueda = true;
                $where .= "AND t.procesoId = :varIdProceso ";
                $query->setParameter("varIdProceso", $parametros["idProceso"]);
                $queryCount->setParameter("varIdProceso", $parametros["idProceso"]);
            }
        }

        if(isset($parametros["proceso"]))
        {
              if($parametros["proceso"] && $parametros["proceso"]!="")
              {
                  $boolBusqueda = true;
                  $where .= " AND p.nombreProceso like :varNombreProceso ";
                  $query->setParameter("varNombreProceso", '%'.$parametros["proceso"].'%');
                  $queryCount->setParameter("varNombreProceso", '%'.$parametros["proceso"].'%');
              }
        }
        
        
        if(isset($parametros["esPlanMantenimiento"]))
        {
            if($parametros["esPlanMantenimiento"] && $parametros["esPlanMantenimiento"]!="")
            {
                $where .= " AND p.esPlanMantenimiento like :varEsPlanMantenimiento ";
                $query->setParameter("varEsPlanMantenimiento", $parametros["esPlanMantenimiento"]);
                $queryCount->setParameter("varEsPlanMantenimiento", $parametros["esPlanMantenimiento"]);
            }
        }
        
        if($codEmpresa && $strVisualizaMovil=="")
        {
            $where .= " AND pe.empresaCod = :varCodEmpresa ";
            $query->setParameter("varCodEmpresa", $codEmpresa);
            $queryCount->setParameter("varCodEmpresa", $codEmpresa);
        }

        if($visible && $visible!="Todos")
        {
            $where .= " AND p.visible = :varVisible ";
            $query->setParameter("varVisible", $visible);
            $queryCount->setParameter("varVisible", $visible);
        }
		
        $sqlCount = "SELECT COUNT(t) ";

        $from = " FROM
		schemaBundle:AdmiTarea t,
		schemaBundle:AdmiProceso p,
		schemaBundle:AdmiProcesoEmpresa pe		
		$where 				
		AND p.id = pe.procesoId 
        AND t.procesoId = p.id
        AND p.estado <> 'Eliminado'
        AND t.estado <> 'Eliminado'
		ORDER BY t.nombreTarea 
               ";  

        //Calculo la cantidad total de los registros a retornar
        $querySqlCount = $sqlCount . $from;
        $queryCount->setDQL($querySqlCount);
        $datos["total"] = $queryCount->getSingleScalarResult();


        $sql = "SELECT t ";
        $querySql = $sql . $from;
        $query->setDQL($querySql);
        
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos["registros"] = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos["registros"] = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos["registros"] = $query->setMaxResults($limit)->getResult();
        else
            $datos["registros"] = $query->getResult();
		return $datos;
    }
    
     public function findTareasActivasByProceso($idProceso){
        $sql = "SELECT t 
                FROM schemaBundle:AdmiTarea t
                WHERE t.procesoId = '$idProceso' AND t.estado not like 'Eliminado' ";
        
        $query = $this->_em->createQuery($sql);
        //echo $query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    }
     public function findTareasActivasLikeProceso($proceso){
        $sql = "SELECT t 
                FROM schemaBundle:AdmiTarea t,
					 schemaBundle:AdmiProceso p
				
                WHERE p.nombreProceso like '%$proceso%' 
				AND p.id = t.procesoId
				AND t.estado not like 'Eliminado' ";
        
        $query = $this->_em->createQuery($sql);
        // echo $query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    }
    
    /**
    * generarJsonTareasLikeProcesoAndTarea
    *
    * Esta funcion retorna la lista de las tareas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes por modificacion de la funcion getRegistros
    *
    * @version 1.0
    *
    * @param String   $emGeneral
    * @param int      $start
    * @param int      $limit
    * @param String   $nombreTarea
    * @param String   $estado
    * @param String   $proceso
    * @param String   $codEmpresa
    *
    * @return array $resultado
    *
    */
    public function generarJsonTareasLikeProcesoAndTarea($emGeneral, $start, $limit, $nombreTarea, $estado , $proceso, $codEmpresa){
        $arr_encontrados = array();
        $datos           = array();
        $em = $this->_em;
        $parametros = array();
        $parametros['proceso'] = $proceso;

        $parametros["nombre"]     = $nombreTarea;
        $parametros["estado"]     = $estado;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["codEmpresa"] = $codEmpresa;

        $datos          = $this->getRegistros($parametros);
        $registros      = $datos["registros"];
        $registrosTotal = $datos["total"];
 
        if ($registros) {
            $num = $registrosTotal;
            foreach ($registros as $data)
            {       
                
                $tareaAntId = $data->getTareaAnteriorId();
                $tareaSigId = $data->getTareaSiguienteId();
                
                if($tareaAntId!=null){
                    $tareaAnt = $em->find('schemaBundle:AdmiTarea', $tareaAntId);
                    $nombreTareaAnterior = $tareaAnt->getNombreTarea();
                }
                else{
                    $nombreTareaAnterior = "N/A";
                }
                
                if($tareaSigId!=null){
                    $tareaSig = $em->find('schemaBundle:AdmiTarea', $tareaSigId);
                    $nombreTareaSiguiente = $tareaSig->getNombreTarea();
                }
                else{
                    $nombreTareaSiguiente = "N/A";
                }
                
                $arr_encontrados[]=array('idTarea' =>$data->getId(),
                                         'nombreTarea' =>trim($data->getNombreTarea()),
                                         'nombreTareaSiguiente' => ($nombreTareaSiguiente),
                                         'nombreTareaAnterior' => ($nombreTareaAnterior),
                                         'tiempoMax' => ($data->getTiempoMax()),
                                         'peso' =>($data->getPeso() ? $data->getPeso() : 0.00),
                                         'costo' =>($data->getCosto() ? $data->getCosto() : 0.00),
                                         'precioPromedio' =>($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                        'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                        'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                        'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
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

    /**
    * generarJsonTareasByProcesoAndTarea
    *
    * Esta funcion retorna la lista de las tareas por el proceso y tareas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes por modificacion de la funcion getRegistros
    *
    * @version 1.0
    *
    * @param String   $emGeneral
    * @param int      $start
    * @param int      $limit
    * @param String   $nombreTarea
    * @param String   $estado
    * @param String   $procesoId
    * @param String   $codEmpresa
    *
    * @return array $resultado
    *
    */
    public function generarJsonTareasByProcesoAndTarea($emGeneral, $start, $limit, $nombreTarea, $estado , $procesoId, $codEmpresa){
        $arr_encontrados = array();
        $datos           = array();
        $em = $this->_em;
        $parametros = array();
        $parametros['idProceso'] = $procesoId;
        
        $parametros["nombre"]     = $nombreTarea;
        $parametros["estado"]     = $estado;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["codEmpresa"] = $codEmpresa;

        $datos          = $this->getRegistros($parametros);
        $registros      = $datos["registros"];
        $registrosTotal = $datos["total"];
 
        if ($registros) {
            $num = $registrosTotal;
            foreach ($registros as $data)
            {       
                
                $tareaAntId = $data->getTareaAnteriorId();
                $tareaSigId = $data->getTareaSiguienteId();
                
                if($tareaAntId!=null){
                    $tareaAnt = $em->find('schemaBundle:AdmiTarea', $tareaAntId);
                    $nombreTareaAnterior = $tareaAnt->getNombreTarea();
                }
                else{
                    $nombreTareaAnterior = "N/A";
                }
                
                if($tareaSigId!=null){
                    $tareaSig = $em->find('schemaBundle:AdmiTarea', $tareaSigId);
                    $nombreTareaSiguiente = $tareaSig->getNombreTarea();
                }
                else{
                    $nombreTareaSiguiente = "N/A";
                }
                
                $arr_encontrados[]=array('idTarea' =>$data->getId(),
                                         'nombreTarea' =>trim($data->getNombreTarea()),
                                         'nombreTareaSiguiente' => ($nombreTareaSiguiente),
                                         'nombreTareaAnterior' => ($nombreTareaAnterior),
                                         'tiempoMax' => ($data->getTiempoMax()),
                                         'peso' =>($data->getPeso() ? $data->getPeso() : 0.00),
                                         'costo' =>($data->getCosto() ? $data->getCosto() : 0.00),
                                         'precioPromedio' =>($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                        'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                        'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                        'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
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
	
    /**
    * generarJsonTareasConMaterialesByProcesoSinModem
    *
    * Esta funcion retorna la lista de las tareas con materiales por procesos sin moden
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes por modificacion de la funcion getRegistros
    *
    * @version 1.0
    *
    * @param String   $emGeneral
    * @param int      $start
    * @param int      $limit
    * @param String   $estado
    * @param String   $procesoId
    * @param String   $codEmpresa
    *
    * @return array $resultado
    *
    */
    public function generarJsonTareasConMaterialesByProcesoSinModem($emGeneral, $start, $limit, $estado , $procesoId, $codEmpresa){
        $arr_encontrados = array();
        $datos           = array();
        $em = $this->_em;
        $parametros = array();
        $parametros['idProceso'] = $procesoId;
        
        $parametros["nombre"]     = "";
        $parametros["estado"]     = $estado;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["codEmpresa"] = $codEmpresa;

        $datos          = $this->getRegistros($parametros);
        $registros      = $datos["registros"];
        $registrosTotal = $datos["total"];
 
        if ($registros) {
            $num = $registrosTotal;
            foreach ($registros as $data)
            {
				if(strpos(strtoupper($data->getNombreTarea()),"MODEM")==FALSE){	
					if($em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getMaterialesByTarea('', '', $data->getId())){
						$tareaAntId = $data->getTareaAnteriorId();
						$tareaSigId = $data->getTareaSiguienteId();
						
						if($tareaAntId!=null){
							$tareaAnt = $em->find('schemaBundle:AdmiTarea', $tareaAntId);
							$nombreTareaAnterior = $tareaAnt->getNombreTarea();
						}
						else{
							$nombreTareaAnterior = "N/A";
						}
						
						if($tareaSigId!=null){
							$tareaSig = $em->find('schemaBundle:AdmiTarea', $tareaSigId);
							$nombreTareaSiguiente = $tareaSig->getNombreTarea();
						}
						else{
							$nombreTareaSiguiente = "N/A";
						}
						
						$arr_encontrados[]=array('idTarea' =>$data->getId(),
												 'nombreTarea' =>trim($data->getNombreTarea()),
												 'nombreTareaSiguiente' => ($nombreTareaSiguiente),
												 'nombreTareaAnterior' => ($nombreTareaAnterior),
												 'tiempoMax' => ($data->getTiempoMax()),
												 'peso' =>($data->getPeso() ? $data->getPeso() : 0.00),
												 'costo' =>($data->getCosto() ? $data->getCosto() : 0.00),
												 'precioPromedio' =>($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
												 'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
					}
				}
			}

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                        'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                        'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                        'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
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

    /**
    * generarJsonTareasConMaterialesByProceso
    *
    * Esta funcion retorna la lista de las tareas con materiales por procesos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 29-12-2015 Se realizan ajustes por modificacion de la funcion getRegistros
    *
    * @version 1.0
    *
    * @param String   $emGeneral
    * @param int      $start
    * @param int      $limit
    * @param String   $estado
    * @param String   $procesoId
    * @param String   $codEmpresa
    *
    * @return array $resultado
    *
    */
    public function generarJsonTareasConMaterialesByProceso($emGeneral, $start, $limit, $estado , $procesoId, $codEmpresa){
        $arr_encontrados = array();
        $datos           = array();
        $em = $this->_em;
        $parametros = array();
        $parametros['idProceso'] = $procesoId;
        
        $parametros["nombre"]     = "";
        $parametros["estado"]     = $estado;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["codEmpresa"] = $codEmpresa;

        $datos          = $this->getRegistros($parametros);
        $registros      = $datos["registros"];
        $registrosTotal = $datos["total"];
 
        if ($registros) {
            $num = $registrosTotal;
            foreach ($registros as $data)
            {
// 					if($em->getRepository('schemaBundle:InfoDetalleSolMaterial')->getMaterialesByTarea('', '', $data->getId())){
						$tareaAntId = $data->getTareaAnteriorId();
						$tareaSigId = $data->getTareaSiguienteId();
						
						if($tareaAntId!=null){
							$tareaAnt = $em->find('schemaBundle:AdmiTarea', $tareaAntId);
							$nombreTareaAnterior = $tareaAnt->getNombreTarea();
						}
						else{
							$nombreTareaAnterior = "N/A";
						}
						
						if($tareaSigId!=null){
							$tareaSig = $em->find('schemaBundle:AdmiTarea', $tareaSigId);
							$nombreTareaSiguiente = $tareaSig->getNombreTarea();
						}
						else{
							$nombreTareaSiguiente = "N/A";
						}
						
						$arr_encontrados[]=array('idTarea' =>$data->getId(),
												 'nombreTarea' =>trim($data->getNombreTarea()),
												 'nombreTareaSiguiente' => ($nombreTareaSiguiente),
												 'nombreTareaAnterior' => ($nombreTareaAnterior),
												 'tiempoMax' => ($data->getTiempoMax()),
												 'peso' =>($data->getPeso() ? $data->getPeso() : 0.00),
												 'costo' =>($data->getCosto() ? $data->getCosto() : 0.00),
												 'precioPromedio' =>($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
												 'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
// 					}
				
			}

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                        'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                        'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                        'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
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

    public function generarJsonTareasPorProceso($procesoId,$start,$limit,$estado=''){
        $arr_encontrados = array();
        $em = $this->_em;
        $registrosTotal = $this->getTareasPorProceso($procesoId, '', '',$estado);
        $registros = $this->getTareasPorProceso($procesoId, $start, $limit,$estado);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {       
                
                $tareaAntId = $data->getTareaAnteriorId();
                $tareaSigId = $data->getTareaSiguienteId();
                
                if($tareaAntId!=null){
                    $tareaAnt = $em->find('schemaBundle:AdmiTarea', $tareaAntId);
                    $nombreTareaAnterior = $tareaAnt->getNombreTarea();
                }
                else{
                    $nombreTareaAnterior = "N/A";
                }
                
                if($tareaSigId!=null){
                    $tareaSig = $em->find('schemaBundle:AdmiTarea', $tareaSigId);
                    $nombreTareaSiguiente = $tareaSig->getNombreTarea();
                }
                else{
                    $nombreTareaSiguiente = "N/A";
                }
                
                $arr_encontrados[]=array('idTarea' =>$data->getId(),
                                         'nombreTarea' =>trim($data->getNombreTarea()),
                                         'descripcionTarea'=>trim($data->getDescripcionTarea()),
                                         'nombreTareaSiguiente' => ($nombreTareaSiguiente),
                                         'nombreTareaAnterior' => ($nombreTareaAnterior),
                                         'tiempoMax' => ($data->getTiempoMax()),
                                         'peso' =>($data->getPeso() ? $data->getPeso() : 0.00),
                                         'costo' =>($data->getCosto() ? $data->getCosto() : 0.00),
                                         'precioPromedio' =>($data->getPrecioPromedio() ? $data->getPrecioPromedio() : 0.00),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
										 'action1' => 'button-grid-show'
										 );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array(
                                        'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                        'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                        'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno','descripcion_tarea' => 'Ninguno', 
                                        'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
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
    
    public function getTareasPorProceso($procesoId,$start,$limit,$estado=''){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTarea','sim');
            
        if($procesoId!=""){ 
            $qb ->where( 'sim.procesoId = ?1');
            $qb->setParameter(1, $procesoId);
        }
        if($estado!=""){ 
            $qb ->andWhere( 'sim.estado like ?2');
            $qb->setParameter(2, $estado);
        }
		$qb->orderBy('sim.nombreTarea', 'ASC');
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function getTareasXNombre($nombre){
	     $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTarea','sim');
            
        if($nombre!=""){ 
            $qb ->where( 'sim.nombreTarea like ?1');
            $qb->setParameter(1, $nombre.'%');
        }		               
        
        $query = $qb->getQuery();
               
        
        return $query->getResult();
    
    
    
    }
    
    
     /**
     * getEmpresaTarea
     *
     * Metodo encargado de obtener informacion de empresa vinculada a una tarea
     *
     * @param integer $idDetalle
     *
     * @return resultado del detalle de informacion de la tarea
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 23-07-2014
     */
    public function getEmpresaTarea($idDetalle)
    {
	  $sql = "SELECT 
		  a.id,
		  b.id,			  
		  c.empresaCod
		  FROM
		  schemaBundle:InfoDetalle a,
		  schemaBundle:AdmiTarea b,
		  schemaBundle:AdmiProcesoEmpresa c
		  WHERE
		  a.tareaId   = b.id and 
		  b.procesoId = c.procesoId and
		  a.id        = :detalle";
		  
          $query = $this->_em->createQuery($sql);
          
          $query->setParameter('detalle',$idDetalle);
          
          return $query->getResult();           
    
    }
    
    /**
     * getJsonTareasPorElementoNodo
     * 
     * Metodo qye devuelve un json con las tareas vinculadas a un Elemento NODO
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 25-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se agrega observación a mostrar en la consulta de tareas por nodo
     * @since 21-07-2016
     * 
     * @param integer $intIdNodo
     * @return $json
     */
    function getJsonTareasPorElementoNodo($intIdNodo)
    {
        $arrayResultado = $this->getResultadoTareasPorElementoNodo($intIdNodo);
        
        if($arrayResultado)
        {            
            foreach($arrayResultado['resultado'] as $data)
            {
                $arrayEncontrados[] = array(
                                            'idDetalle'         => $data['id'],
                                            'idComunicacion'    => $data['idComunicacion'],
                                            'feSolicitada'      => strval(date_format($data['feSolicitada'], "d-m-Y H:i")),
                                            'nombreTarea'       => $data['nombreTarea'],
                                            'tipoAsignado'      => $data['tipoAsignado'],
                                            'asignadoNombre'    => $data['asignadoNombre'],
                                            'refAsignadoNombre' => $data['refAsignadoNombre'],
                                            'estado'            => $data['estado'],
                                            'observacion'       => $data['observacion']
                                            );
            }
            
            $arrayRespuesta = array('encontrados' => $arrayEncontrados); 
        }
        else
        {
            $arrayRespuesta = array('encontrados' => '[]');
        }
        
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData; 
    }
    
    /**
     * getResultadoTareasPorElementoNodo
     * 
     * Metodo qye devuelve un array con las tareas vinculadas a un Elemento NODO
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 
     * @since 25-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se agrega observación a mostrar en la consulta de tareas por nodo
     * @since 21-07-2016
     * 
     * @param integer $intIdNodo
     * @return $arrayResultado
     */
    function getResultadoTareasPorElementoNodo($intIdNodo)
    {
        $arrayResultado = array();
        
        try
        {
            $query = $this->_em->createQuery(null);

            $dql = "SELECT 
                        detalle.id,
                        com.id idComunicacion,
                        detalle.feSolicitada,
                        detalle.observacion,
                        tarea.nombreTarea,
                        asig.tipoAsignado,
                        asig.asignadoNombre,
                        asig.refAsignadoNombre,
                        hist.estado                 
                    FROM 
                        schemaBundle:InfoDetalle detalle,
                        schemaBundle:InfoDetalleTareaElemento tareaElemento,
                        schemaBundle:AdmiTarea tarea,
                        schemaBundle:InfoDetalleAsignacion asig,
                        schemaBundle:InfoDetalleHistorial hist,
                        schemaBundle:InfoComunicacion com
                    where 
                        detalle.id       =  tareaElemento.detalleId and
                        tarea.id         =  detalle.tareaId and
                        detalle.id       =  asig.detalleId and
                        detalle.id       =  hist.detalleId and
                        detalle.id       =  com.detalleId and                                       
                        hist.id          =  (select max(h.id) from schemaBundle:InfoDetalleHistorial h where h.detalleId = detalle.id) and
                        com.id           =  (select min(c.id) from schemaBundle:InfoComunicacion c where c.detalleId = detalle.id) and 
                        tareaElemento.elementoId  =  :nodo";
            
            $query->setParameter('nodo', $intIdNodo);              
            
            $query->setDQL($dql);                              

            $arrayResultado['resultado'] = $query->getResult();                        
                           
        } 
        catch (Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * getTareaIncidencia
     *
     * Método que retorna los idTarea de una tarea de incidencia creada desde el movil
     *
     * @author Walther Joao Gaibor <mailto:wgaibor@telconet.ec>
     * @version 1.0
     * @since 20/09/2017
     *
     * @param array $arrayParametro['arrayNombreTarea' => Array - Tareas de Incidencias creada desde el movil,
     *                              'strEstado'        => String - Estado de las tareas a buscar.]
     * @return array $arrayResultado
     */
    public function getTareaIncidencia($arrayParametro)
    {
        $arrayRespuesta = array();
        try
        {
            $objQuery  = $this->_em->createQuery();
            $strSql    = "SELECT admiTarea.id tareaId
                            FROM schemaBundle:AdmiTarea admiTarea
                            WHERE admiTarea.nombreTarea IN (:arrayNombreTarea)
                            AND   admiTarea.estado = (:strEstado)";
            $objQuery->setParameter("arrayNombreTarea", array_values($arrayParametro['arrayNombreTarea']));
            $objQuery->setParameter("strEstado", $arrayParametro['strEstado']);
            $objQuery->setDQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch (\Exception $e)
        {
            error_log('InfoDetalleAsignacionRepository->getTareaPorIncidencias()  '.$e->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de realizar la consulta de las tareas con sus respectivos procesos.
     *
     * Costo 8
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 17-09-2018
     * 
     * 
     * Se agrega campo adicional NIVEL1, para indicar el macroproceso
     * @author José Bedón Sánchez <jobedon@telconet.ec>
     * @version 2.0 29-04-2021
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 2.1 26-08-2021 - Se actualiza para incluir filtro por CodEmpresa 
     * 
     * @param $arrayParametros [
     *                              arrayIdTarea     => Lista de id de tareas,
     *                              arrayIdProceso   => Lista de id de procesos,
     *                              strNombreTarea   => Nombre de la tarea,
     *                              strNombreProceso => Nombre del proceso,
     *                              strEstadoTarea   => Estado de la tarea,
     *                              strEstadoProceso => Estado del proceso,
     *                              strCodEmpresa    => Codigo de Empresa,
     *                              boolFiltraEmpresa => Indica si filtra por Empresa
     *                         ]
     * @return $arrayResultado
     */
    public function getTareasProcesos($arrayParametros)
    {
        $arrayResultado = array();
        $strWhere       = '';


        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if(isset($arrayParametros['arrayIdTarea']) && !empty($arrayParametros['arrayIdTarea']))
            {
                $strWhere .= 'AND TAREA.ID_TAREA IN (:arrayIdTarea) ';
                $objNativeQuery->setParameter('arrayIdTarea', array_values($arrayParametros['arrayIdTarea']));
            }

            if(isset($arrayParametros['arrayIdProceso']) && !empty($arrayParametros['arrayIdProceso']))
            {
                $strWhere .= 'AND PROCESO.ID_PROCESO IN (:arrayIdProceso) ';
                $objNativeQuery->setParameter('arrayIdProceso', array_values($arrayParametros['arrayIdProceso']));
            }

            if(isset($arrayParametros['strNombreTarea']) && !empty($arrayParametros['strNombreTarea']))
            {
                $strWhere .= 'AND TAREA.NOMBRE_TAREA LIKE (:strNombreTarea) ';
                $objNativeQuery->setParameter('strNombreTarea', '%'.$arrayParametros['strNombreTarea'].'%');
            }

            if(isset($arrayParametros['strNombreProceso']) && !empty($arrayParametros['strNombreProceso']))
            {
                $strWhere .= 'AND PROCESO.NOMBRE_PROCESO LIKE (:strNombreProceso) ';
                $objNativeQuery->setParameter('strNombreProceso', '%'.$arrayParametros['strNombreProceso'].'%');
            }

            if(isset($arrayParametros['strEstadoTarea']) && !empty($arrayParametros['strEstadoTarea']))
            {
                $strWhere .= 'AND TAREA.ESTADO = :strEstadoTarea ';
                $objNativeQuery->setParameter("strEstadoTarea", $arrayParametros['strEstadoTarea']);
            }

            if(isset($arrayParametros['strEstadoProceso']) && !empty($arrayParametros['strEstadoProceso']))
            {
                $strWhere .= 'AND PROCESO.ESTADO = :strEstadoProceso ';
                $objNativeQuery->setParameter("strEstadoProceso", $arrayParametros['strEstadoProceso']);
            }

            $strSql = "SELECT TAREA.ID_TAREA ID_TAREA, "
                           . "TAREA.NOMBRE_TAREA NOMBRE_TAREA, "
                           . "TAREA.DESCRIPCION_TAREA DESCRIPCION_TAREA, "
                           . "TAREA.ESTADO ESTADO_TAREA, "
                           . "PROCESO.ID_PROCESO ID_PROCESO, "
                           . "PROCESO.NOMBRE_PROCESO NOMBRE_PROCESO, "
                           . "PROCESO.DESCRIPCION_PROCESO DESCRIPCION_PROCESO, "
                           . "PROCESO.ESTADO ESTADO_PROCESO, "
                           . "(SELECT MAX(APD.VALOR1) "
                           . " FROM DB_GENERAL.ADMI_PARAMETRO_DET APD, "
                           . "     DB_GENERAL.ADMI_PARAMETRO_CAB APC "
                           . " WHERE APD.PARAMETRO_ID    = APC.ID_PARAMETRO "
                           . " AND APC.NOMBRE_PARAMETRO IN ('CATEGORIA_TAREA') "
                           . " AND APD.VALOR3            = TAREA.ID_TAREA "
                           . " AND (UPPER(APD.VALOR2)    LIKE '%'||UPPER(PROCESO.NOMBRE_PROCESO)||'%' "
                           . " OR PROCESO.NOMBRE_PROCESO LIKE '%'||UPPER(APD.VALOR2)||'%') "
                           . "     ) AS NIVEL1 "
                    . "FROM DB_SOPORTE.ADMI_TAREA   TAREA, ";
            
            if(isset($arrayParametros['boolFiltraEmpresa']) && $arrayParametros['boolFiltraEmpresa'])
            {
                $strSql = $strSql. "DB_SOPORTE.ADMI_PROCESO PROCESO, "
                                . "DB_SOPORTE.ADMI_PROCESO_EMPRESA APE "
                                . "WHERE TAREA.PROCESO_ID = PROCESO.ID_PROCESO  "
                                . "AND PROCESO.ID_PROCESO = APE.PROCESO_ID "
                                . "AND APE.EMPRESA_COD = :codEmpresa ";
                
                $objNativeQuery->setParameter("codEmpresa", $arrayParametros['strCodEmpresa']);
            }
            else
            {
                $strSql = $strSql. "DB_SOPORTE.ADMI_PROCESO PROCESO " 
                                 ."WHERE TAREA.PROCESO_ID = PROCESO.ID_PROCESO ";
            }
            
            $strSql = $strSql.$strWhere;
                    
            $objResultSetMap->addScalarResult('ID_TAREA'            , 'idTarea'            , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_TAREA'        , 'nombreTarea'        , 'string');
            $objResultSetMap->addScalarResult('DESCRIPCION_TAREA'   , 'descripcionTarea'   , 'string');
            $objResultSetMap->addScalarResult('ESTADO_TAREA'        , 'estadoTarea'        , 'string');
            $objResultSetMap->addScalarResult('ID_PROCESO'          , 'idProceso'          , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_PROCESO'      , 'nombreProceso'      , 'string');
            $objResultSetMap->addScalarResult('DESCRIPCION_PROCESO' , 'descripcionProceso' , 'string');
            $objResultSetMap->addScalarResult('ESTADO_PROCESO'      , 'estadoProceso'      , 'string');
            $objResultSetMap->addScalarResult('NIVEL1'              , 'nombreNivel1'       , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayResult              = $objNativeQuery->getResult();
            $arrayRespuesta["status"] = 'ok';
            $arrayRespuesta["total"]  = count($arrayResult);
            $arrayRespuesta["result"] = $arrayResult;
        }
        catch (\Exception $objException)
        {
            $arrayResultado["status"]  = 'fail';
            $arrayResultado["message"] = $objException->getMessage();
        }

        return $arrayRespuesta;
    }
    
    /**
    * generarJsonTareasConMaterialesByProcesoRequiereTrabajo
    *
    * Esta funcion retorna la lista de las tareas con materiales por procesos
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 
    *
    * @param String   $emGeneral
    * @param $arrayParametros [
    *                              procesoId        => Id del proceso,
    *                              codEmpresa       => Codigo de la empresa,
    *                              estado           => Estado del proceso,
    *                              start            => Comienzo del pagineo,
    *                              limit            => Total de registros a consultar,
    *                              servicioId       => Id del servicio
    *                              idSolicitud      => Id de la solictud
    *                              tarea            => Nombre de la tarea
    *                              caracteristica   => Característica de Requiere Trabajo  
    *                              producto         => Nombre del Producto 
    *                         ]
    *
    * @return array $resultado
    *
    */
    public function generarJsonTareasConMaterialesByProcesoRequiereTrabajo($emGeneral, $arrayParametros)
    {
        $arrayEncontrados           = array();
        $arrayDatos                 = array();
        $objEm                      = $this->_em;
        $arrayParametrosDatos       = array();
        $intNum                     = 0;
        $arrayParametrosPrimario    = array();
        
        if ($arrayParametros['boolRequiereFlujoSimultanea'] == 'SI')
        {
            $arrayParametrosPrimario['idProceso']    = $arrayParametros['procesoId'];
            $arrayParametrosPrimario["nombre"]       = "";
            $arrayParametrosPrimario["estado"]       = $arrayParametros['estado'];
            $arrayParametrosPrimario["start"]        = $arrayParametros['start'];
            $arrayParametrosPrimario["limit"]        = $arrayParametros['limit'];
            $arrayParametrosPrimario["codEmpresa"]   = $arrayParametros['codEmpresa'];

            $objDatos           = $this->getRegistros($arrayParametrosPrimario);
            $objRegistros       = $objDatos["registros"];
            $intRegistrosTotal  = $objDatos["total"];
 
            if ($objRegistros) 
            {
                $intNum = $intRegistrosTotal;
                foreach ($objRegistros as $objDataSim)
                {
                    $strTareaAntId = $objDataSim->getTareaAnteriorId();
					$strTareaSigId = $objDataSim->getTareaSiguienteId();
						
					if($strTareaAntId != null)
                    {
						$strTareaAnt = $objEm->find('schemaBundle:AdmiTarea', $strTareaAntId);
						$strNombreTareaAnterior = $strTareaAnt->getNombreTarea();
					}
					else
                    {
						$strNombreTareaAnterior = "N/A";
					}
						
					if($strTareaSigId != null)
                    {
						$strTareaSig = $objEm->find('schemaBundle:AdmiTarea', $strTareaSigId);
						$strNombreTareaSiguiente = $strTareaSig->getNombreTarea();
					}
					else
                    {
						$strNombreTareaSiguiente = "N/A";
					}
						
					$arrayEncontrados[]=array('idTarea' =>$objDataSim->getId(),
											 'nombreTarea' =>trim($objDataSim->getNombreTarea()),
											 'nombreTareaSiguiente' => ($strNombreTareaSiguiente),
											 'nombreTareaAnterior' => ($strNombreTareaAnterior),
                        					 'tiempoMax' => ($objDataSim->getTiempoMax()),
											 'peso' =>($objDataSim->getPeso() ? $objDataSim->getPeso() : 0.00),
											 'costo' =>($objDataSim->getCosto() ? $objDataSim->getCosto() : 0.00),
											 'precioPromedio' =>($objDataSim->getPrecioPromedio() ? $objDataSim->getPrecioPromedio() : 0.00),
											 'estado' =>(strtolower(trim($objDataSim->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
                }
            }
        }
        
        if ($arrayParametros['boolRequiereFlujoSimultanea'] == 'SI')
        {
            $arrayParametrosDatos['idProceso']  = $arrayParametros["procesoIdSimultaneo"];
        }
        else
        {
            $arrayParametrosDatos['idProceso']  = $arrayParametros["procesoId"];
        }
        
        $arrayParametrosDatos["estado"]     = $arrayParametros["estado"];
        $arrayParametrosDatos["start"]      = $arrayParametros["start"];
        $arrayParametrosDatos["limit"]      = $arrayParametros["limit"];
        $arrayParametrosDatos["codEmpresa"] = $arrayParametros["codEmpresa"];
        
        $arrayResultado   = $this->getProductoCaracteristica($arrayParametros);
        if($arrayResultado)
        {
            foreach($arrayResultado as $arrayRegistro)
            {
                $arrayParametrosDatos["nombre"] = $arrayRegistro["nombreDepartamento"];
                $intIdDepartamento              = $arrayRegistro["valor"];
                $arrayDatos                     = $this->getRegistrosTareas($arrayParametrosDatos);
                $strRegistros                   = $arrayDatos["registros"];
                                
                if ($strRegistros) 
                {
                    foreach ($strRegistros as $objData)
                    {
                        $strTareaAntId = $objData->getTareaAnteriorId();
                        $strTareaSigId = $objData->getTareaSiguienteId();
                        
                        if($strTareaAntId != null)
                        {
                            $strTareaAnt            = $objEm->find('schemaBundle:AdmiTarea', $strTareaAntId);
                            $strNombreTareaAnterior = $strTareaAnt->getNombreTarea();
                        }
                        else
                        {
                            $strNombreTareaAnterior = "N/A";
                        }
						
                        if($strTareaSigId != null)
                        {
                            $strTareaSig                = $objEm->find('schemaBundle:AdmiTarea', $strTareaSigId);
                            $strNombreTareaSiguiente    = $strTareaSig->getNombreTarea();
                        }
                        else
                        {
                            $strNombreTareaSiguiente    = "N/A";
                        }
						
                        $arrayEncontrados[] = array('idTarea'  =>$objData->getId(),
        						'nombreTarea'          =>trim($objData->getNombreTarea()),
        						'nombreTareaSiguiente' => ($strNombreTareaSiguiente),
        						'nombreTareaAnterior'  => ($strNombreTareaAnterior),
        						'tiempoMax'            => ($objData->getTiempoMax()),
        						'peso'                 =>($objData->getPeso() ? $objData->getPeso() : 0.00),
        						'costo'                =>($objData->getCosto() ? $objData->getCosto() : 0.00),
        						'precioPromedio'       =>($objData->getPrecioPromedio() ? $objData->getPrecioPromedio() : 0.00),
                                'idDepartamento'       => ($intIdDepartamento),
                                'estado'               =>(strtolower(trim($objData->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
                        $intNum = $intNum + 1;
                    }
                }
            }
            
            if($intNum == 0)
            {
                $objResulta     = array('total' => 1 ,
                                                'encontrados' => array(
                                                'id_tarea' => 0 , 'nombre_proceso' => 'Ninguno', 
                                                'nombre_rol_autoria' => 'Ninguno', 'nombre_tarea_ant' => 'Ninguno', 
                                                'nombre_tarea_sig' => 'Ninguno', 'nombre_tarea' => 'Ninguno', 
                                                'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
                                                'tarea_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                
                $objResultado   = json_encode( $objResulta);
            }
            else
            {
                $objDataF       = json_encode($arrayEncontrados);
                $objResultado   = '{"total":"'.$intNum.'","encontrados":'.$objDataF.'}';
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
        }
        return $objResultado;
    }
    
    /**
    * Costo: 12
    * 
    * getProductoCaracteristica
    *
    * Esta funcion retorna la lista de las características que Requieren Trabajo
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0
    *
    * @param array   $arrayParametros
    *
    * @return $objDatos
    *
    */
    public function getProductoCaracteristica($arrayParametros)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm); 
        
        $intIdServicio      = $arrayParametros["servicioId"];
        $strProducto        = $arrayParametros["producto"];
        $strCaracteristica  = $arrayParametros["caracteristica"];
        $strCodEmpresa      = $arrayParametros["codEmpresa"];
                        
        $objSql = "SELECT ID_SERVICIO_PROD_CARACT ID_SERVICIO_PROD_CARACT,
                        SERVICIO_ID SERVICIO_ID,
                        PRODUCTO_CARACTERISITICA_ID PRODUCTO_CARACTERISITICA_ID,
                        PROD.VALOR VALOR, 
                        PROD.ESTADO ESTADO,
                        DEP.NOMBRE_DEPARTAMENTO NOMBRE_DEPARTAMENTO
              FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT PROD, DB_GENERAL.ADMI_DEPARTAMENTO DEP
              WHERE SERVICIO_ID = :servicioId AND PRODUCTO_CARACTERISITICA_ID = (
                        SELECT ID_PRODUCTO_CARACTERISITICA 
                        FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
                        WHERE PRODUCTO_ID=(
                                    SELECT ID_PRODUCTO 
                                    FROM DB_COMERCIAL.ADMI_PRODUCTO 
                                    WHERE DESCRIPCION_PRODUCTO = :producto AND ESTADO= :estado AND EMPRESA_COD = :empresa)
                        AND CARACTERISTICA_ID=(
                                    SELECT ID_CARACTERISTICA 
                                    FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                                    WHERE DESCRIPCION_CARACTERISTICA = :caracteristica))
                        AND PROD.VALOR = DEP.ID_DEPARTAMENTO
                        AND DEP.ESTADO = :estado
                        AND DEP.EMPRESA_COD = :empresa";
        
        
        $objQuery->setParameter("servicioId"       , $intIdServicio);
        $objQuery->setParameter("estado"           , 'Activo');
        $objQuery->setParameter("producto"         , $strProducto);
        $objQuery->setParameter("empresa"          , $strCodEmpresa);
        $objQuery->setParameter("caracteristica"   , $strCaracteristica);
      
        
        $objRsm->addScalarResult('ID_SERVICIO_PROD_CARACT'     ,'idServicioProdCarac'  , 'integer');
        $objRsm->addScalarResult('SERVICIO_ID'                 ,'idServicio'           , 'integer');
        $objRsm->addScalarResult('PRODUCTO_CARACTERISITICA_ID' ,'idProdCaract'         , 'integer');
        $objRsm->addScalarResult('VALOR'                       ,'valor'                , 'string');
        $objRsm->addScalarResult('ESTADO'                      ,'estado'               , 'string');
        $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO'         ,'nombreDepartamento'   , 'string');
        
        return $objQuery->setSQL($objSql)->getResult();
    }
    
    /**
    * Costo: 10 
    * 
    * getRegistrosTareas
    *
    * Esta funcion retorna la lista de las tareas por productos que Requieren Trabajo
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0
    *
    * @param array   $arrayParametros
    *
    * @return array $resultado
    *
    */
    public function getRegistrosTareas($arrayParametros)
	{
        $boolBusqueda       = false; 
        $strWhere           = "";
        $objQuery           = $this->_em->createQuery();
        $objQueryCount      = $this->_em->createQuery();

        $strNombre          = $arrayParametros["nombre"];
        $strEstado          = $arrayParametros["estado"];
        $strCodEmpresa      = $arrayParametros["codEmpresa"];
        $strStart           = $arrayParametros["start"];
        $strLimit           = $arrayParametros["limit"];
        $strVisible         = $arrayParametros["visible"]?$arrayParametros["visible"]:"Todos";
        $strTipoCaso        = $arrayParametros["tipoCaso"]?$arrayParametros["tipoCaso"]:"";
        $strVisualizaMovil  = $arrayParametros["visualizaMovil"];
       
        //Se consulta el tipo de caso Movilizacion y se extrae el proceso que tiene realacionado para segun esto,presentar las tareas realacionadas
        //a este proceso
        if($strTipoCaso && $strTipoCaso != "")
        {
            $objExisteTipoCaso      = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get("TIPO CASO POR PROCESOS", "", "", "","", $strTipoCaso, "", "");

            $objParametroProceso    = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get("PROCESOS TIPO CASO MOVILIZACION", "", "", "","", "", "", "");

            $strProceso             = $objParametroProceso[0]['valor1'];

        }

        if($strEstado!="Todos")
        {
            $boolBusqueda = true;
            if($strEstado=="Activo")
            {
				$strWhere .= "WHERE LOWER(t.estado) not like LOWER(:varEstado) ";
                $objQuery->setParameter("varEstado", 'Eliminado');
                $objQueryCount->setParameter("varEstado", 'Eliminado');
            }
            else
            {
				$strWhere .= "WHERE LOWER(t.estado) like LOWER(:varEstado) ";
                $objQuery->setParameter("varEstado", $strEstado);
                $objQueryCount->setParameter("varEstado", $strEstado);
            }
        }
        else
        {
            $strWhere .= "WHERE t.estado is not null ";
        }
        //Si el tipo de caso fue encontrado se filtra por el proceso que tenga configurado
        if($strTipoCaso)
        {
            if($objExisteTipoCaso)
            {
                $strWhere .= " AND p.id = :varProceso ";
                $objQuery->setParameter("varProceso", $strProceso);
                $objQueryCount->setParameter("varProceso", $strProceso);
            }
            else
            {
                $strWhere .= " AND p.id <> :varProceso ";
                $objQuery->setParameter("varProceso", $strProceso);
                $objQueryCount->setParameter("varProceso", $strProceso);
            }
        }
		
        if($strNombre!="")
        {
            $boolBusqueda = true;
			$strWhere .= "AND LOWER(t.descripcionTarea) like LOWER(:varNombreTarea) ";
            $objQuery->setParameter("varNombreTarea", '%'.$strNombre.'%');
            $objQueryCount->setParameter("varNombreTarea", '%'.$strNombre.'%');

        }
		if($strVisualizaMovil!="")
        {
			$strWhere .= " AND t.visualizarMovil = :varVisualizarMovil  ";
            $objQuery->setParameter("varVisualizarMovil", $strVisualizaMovil);
            $objQueryCount->setParameter("varVisualizarMovil", $strVisualizaMovil);

        }
        if(isset($arrayParametros["idTareaActual"]) && !empty($arrayParametros["idTareaActual"]))
        {
            $boolBusqueda = true;
            $strWhere .= "AND t.id NOT IN (:varTareaActual) ";
            $objQuery->setParameter("varTareaActual", $arrayParametros["idTareaActual"]);
            $objQueryCount->setParameter("varTareaActual", $arrayParametros["idTareaActual"]);
        }

        if(isset($arrayParametros["idProceso"]) && !empty($arrayParametros["idProceso"]))
        {
            $boolBusqueda = true;
            $strWhere .= "AND t.procesoId = :varIdProceso ";
            $objQuery->setParameter("varIdProceso", $arrayParametros["idProceso"]);
            $objQueryCount->setParameter("varIdProceso", $arrayParametros["idProceso"]);
        }

        if(isset($arrayParametros["proceso"]) && !empty($arrayParametros["proceso"]))
        {
            $boolBusqueda = true;
            $strWhere .= " AND p.nombreProceso like :varNombreProceso ";
            $objQuery->setParameter("varNombreProceso", '%'.$arrayParametros["proceso"].'%');
            $objQueryCount->setParameter("varNombreProceso", '%'.$arrayParametros["proceso"].'%');
        }
        
        
        if(isset($arrayParametros["esPlanMantenimiento"]) && !empty($arrayParametros["esPlanMantenimiento"]))
        {
            $strWhere .= " AND p.esPlanMantenimiento like :varEsPlanMantenimiento ";
            $objQuery->setParameter("varEsPlanMantenimiento", $arrayParametros["esPlanMantenimiento"]);
            $objQueryCount->setParameter("varEsPlanMantenimiento", $arrayParametros["esPlanMantenimiento"]);
        }
        
        if($strCodEmpresa && $strVisualizaMovil=="")
        {
            $strWhere .= " AND pe.empresaCod = :varCodEmpresa ";
            $objQuery->setParameter("varCodEmpresa", $strCodEmpresa);
            $objQueryCount->setParameter("varCodEmpresa", $strCodEmpresa);
        }

        if($strVisible && $strVisible!="Todos")
        {
            $strWhere .= " AND p.visible = :varVisible ";
            $objQuery->setParameter("varVisible", $strVisible);
            $objQueryCount->setParameter("varVisible", $strVisible);
        }
		
        $objSqlCount = "SELECT COUNT(t) ";

        $strFrom = " FROM
		schemaBundle:AdmiTarea t,
		schemaBundle:AdmiProceso p,
		schemaBundle:AdmiProcesoEmpresa pe		
		$strWhere 				
		AND p.id = pe.procesoId 
		AND t.procesoId = p.id
		ORDER BY t.nombreTarea 
               ";  

        //Calculo la cantidad total de los registros a retornar
        $objQuerySqlCount = $objSqlCount . $strFrom;
        $objQueryCount->setDQL($objQuerySqlCount);
        $objDatos["total"] = $objQueryCount->getSingleScalarResult();


        $objSql = "SELECT t ";
        $objQuerySql = $objSql . $strFrom;
        $objQuery->setDQL($objQuerySql);
        
        if ($strStart!='' && !$boolBusqueda && $strLimit!='')
        {
            $objDatos["registros"] = $objQuery->setFirstResult($strStart)->setMaxResults($strLimit)->getResult();
        }
        elseif($strStart!='' && !$boolBusqueda && $strLimit=='')
        {
            $objDatos["registros"] = $objQuery->setFirstResult($strStart)->getResult();
        }
        elseif(($strStart=='' || $boolBusqueda) && $strLimit!='')
        {
            $objDatos["registros"] = $objQuery->setMaxResults($strLimit)->getResult();
        }
        else
        {
            $objDatos["registros"] = $objQuery->getResult();
        }
        
        return $objDatos;
    }
    
    /**
     * Costo: 11
     * getProductosRequiereTrabajo
     *
     * Método encargado de obtener los productos que requieren trabajo
     *
     * @param array  $arrayParametros [ servicioId       => id del servicio
     *                                  producto         => nombre del producto
     *                                  estado           => estado del producto
     *                                  empresa          => codigo de la empresa
     *                                  caracteristica   => caracteristica Requiere Trabajo
     *                                  idDepartamento   => departamento asignado a la tarea ]
     *
     * @return array $arrayProductos
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function getProductosRequiereTrabajo($arrayParametros)
    {
        $objRsmb            = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null,$objRsmb);

        try
        {
            $strSql = " SELECT ID_SERVICIO_PROD_CARACT ID_SERVICIO_PROD_CARACT,
                        SERVICIO_ID SERVICIO_ID,
                        PRODUCTO_CARACTERISITICA_ID PRODUCTO_CARACTERISITICA_ID,
                        PROD.VALOR VALOR, 
                        PROD.ESTADO ESTADO
                        
              FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT PROD
              WHERE SERVICIO_ID = :servicioId AND PRODUCTO_CARACTERISITICA_ID = (
                        SELECT ID_PRODUCTO_CARACTERISITICA 
                        FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
                        WHERE PRODUCTO_ID=(
                                    SELECT ID_PRODUCTO 
                                    FROM DB_COMERCIAL.ADMI_PRODUCTO 
                                    WHERE DESCRIPCION_PRODUCTO = :producto AND ESTADO= :estado AND EMPRESA_COD = :empresa)
                        AND CARACTERISTICA_ID=(
                                    SELECT ID_CARACTERISTICA 
                                    FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                                    WHERE DESCRIPCION_CARACTERISTICA = :caracteristica))
                        AND PROD.VALOR = :idDepartamento ";
        
            $objQuery->setParameter('servicioId',$arrayParametros["servicioId"]);
            $objQuery->setParameter('producto',$arrayParametros["producto"]);
            $objQuery->setParameter('estado',$arrayParametros["estado"]);
            $objQuery->setParameter('empresa',$arrayParametros["empresa"]);
            $objQuery->setParameter('caracteristica',$arrayParametros["caracteristica"]);
            $objQuery->setParameter('idDepartamento',$arrayParametros["idDepartamento"]);
        
            $objRsmb->addScalarResult('SERVICIO_ID', 'idServicio','integer');
            $objRsmb->addScalarResult('PRODUCTO_CARACTERISITICA_ID', 'idProductoCaracteristica','integer');
            $objRsmb->addScalarResult('VALOR', 'valor','string');
            $objRsmb->addScalarResult('ESTADO', 'estado','string');
        
            return $objQuery->setSQL($strSql)->getResult();
        } 
        catch (\Exception $objEx) 
        {
            throw($objEx);
        }
    }


    /**
     * getEmpresaIndisponibilidad
     *
     * Metodo encargado de obtener empresas responsables de una tarea
     *
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     * 
     * @return resultado del detalle de informacion de la tarea
     * 
     */
    public function getEmpresaIndisponibilidad()
    {
        
        try
        {
            
            $arrayRespuesta = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get("INDISPONIBILIDAD_TAREAS_EMPRESAS", 
                                                "SOPORTE", 
                                                "TAREAS", 
                                                "",
                                                "", 
                                                "", 
                                                "", 
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "");

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getEmpresaIndisponibilidad()  '.$e->getMessage());
        }

        return json_encode($arrayRespuesta);
    
    }

    public function getTiempoAfectacionIndisponibilidad($intDetalleId)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objRespuesta ;

        try
        {

            $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT abs(extract(day from (sysdate - a.fecha_inicial))) *24*60 + "
                        . "abs(extract(hour from (sysdate - a.fecha_inicial))) *60 + "
                        . "abs(extract(minute from (sysdate - a.fecha_inicial))) tiempoafectacion "
                        . "FROM "
                        . " (select s.id_detalle_historial , s.fe_creacion fecha_inicial, estado, s.detalle_id, rownum "
                        . " from db_soporte.info_detalle_historial s "
                        . " where detalle_id = :intDetalleId "
                        . " and rownum = 1 "
                        . " order by s.id_detalle_historial) a";

            $objQuery->setParameter("intDetalleId", $intDetalleId);
            $objRsm->addScalarResult('TIEMPOAFECTACION','tiempoafectacion','integer');
        
            $objQuery->setSQL($strSql);

            $objRespuesta = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getTiempoAfectacionIndisponibilidad()  '.$e->getMessage());
        }
        
        return $objRespuesta;
    
    }


    public function verificarRolTap($strUser)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $objRespuesta;
        $intIdDepartamentoPermiso = null;

        try
        {

            // obtiene el departamento parametrizado para habilitar el boton indisponibilidad
            $arrayRespuesta = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get("INDISPONIBILIDAD_TAREAS_ROL", 
                                                "SOPORTE", 
                                                "TAREAS", 
                                                "",
                                                "", 
                                                "", 
                                                "", 
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "");

            $intIdDepartamentoPermiso = $arrayRespuesta[0]['valor1'];
            
            // verifica si el usuario pertenece al departamento permitido
            $strSql = "select decode(nvl(count(d.id_departamento), 0) , 0, 'N', 'S') rolTap "
                        . "from db_comercial.info_persona a "
                        . "inner join db_comercial.info_persona_empresa_rol b on a.id_persona = b.persona_id "
                        . "inner join db_comercial.info_empresa_rol c on c.id_empresa_rol = b.empresa_rol_id "
                        . "left join db_general.admi_departamento d on d.id_departamento = b.departamento_id "
                        . "inner join db_compras.admi_empresa e on e.codigo = c.empresa_cod "
                        . "inner join db_general.admi_rol f on f.id_rol = c.rol_id "
                        . "where a.login = :strUser " 
                        . "and d.id_departamento = :intIdDepartamentoPermiso "
                        . "and a.estado = 'Activo'";

            $objQuery->setParameter("strUser", $strUser);
            $objQuery->setParameter("intIdDepartamentoPermiso", $intIdDepartamentoPermiso);
            $objRsm->addScalarResult('ROLTAP','rolTap','string');
        
            $objQuery->setSQL($strSql);

            $objRespuesta = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->verificarRolTap()  '.$e->getMessage());
        }
        
        return $objRespuesta;
    
    }


    
    public function getClientesAfectados($strNombreOlt, $strIdPuerto, $strIdCaja, $strIdSplitter)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $objRespuesta = 0;

        try
        {

            // calculo clientes afectados
            $strSql = "select count(*) clientesAfectados "
                    . "from DB_INFRAESTRUCTURA.info_elemento a "
                    . "inner join DB_INFRAESTRUCTURA.info_interface_elemento b on a.id_elemento = b.elemento_id "
                    . "inner join DB_COMERCIAL.info_servicio_tecnico c on c.elemento_id = a.id_elemento and "
                    . "c.interface_elemento_id = b.id_interface_elemento "
                    . "inner join DB_INFRAESTRUCTURA.info_elemento d on d.id_elemento = c.elemento_contenedor_id "
                    . "inner join DB_INFRAESTRUCTURA.info_elemento e on e.id_elemento = c.elemento_conector_id "
                    . "inner join DB_SOPORTE.INFO_SERVICIO f on c.servicio_id = f.id_servicio "
                    . "inner join DB_SOPORTE.INFO_PUNTO g on f.punto_id = g.id_punto "
                    . "where a.nombre_elemento in ('" . $strNombreOlt . "') ";
                    

            if($strIdPuerto != '')
            {
                $strSql = $strSql . "and b.id_interface_elemento in (" . $strIdPuerto . ") ";
            }
            if($strIdCaja != '')
            {
                $strSql = $strSql . "and c.elemento_contenedor_id in (" . $strIdCaja . ") ";
            }
            if($strIdSplitter != '')
            {
                $strSql = $strSql . "and c.elemento_conector_id in (" . $strIdSplitter . ") ";
            }
            
            $strSql = $strSql . "and f.estado = 'Activo' ";

            $objRsm->addScalarResult('CLIENTESAFECTADOS','clientesAfectados','string');
        
            $objQuery->setSQL($strSql);

            $objRespuesta = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getClientesAfectados()  '.$e->getMessage());
        }
        
        return $objRespuesta;
    
    }



    public function getIdOltPorNombre($strNombreOlt)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $objRespuesta;

        try
        {

            $strSql = "select a.id_elemento as idElemento from DB_INFRAESTRUCTURA.info_elemento a where  a.nombre_elemento = '" . $strNombreOlt . "'";

            $objRsm->addScalarResult('IDELEMENTO','idElemento','integer');
        
            $objQuery->setSQL($strSql);

            $objRespuesta = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getIdOltPorNombre()  '.$e->getMessage());
        }
        
        return $objRespuesta;
    
    }



    public function getCajas($intIdOlt, $strIdPuerto)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        try
        {

            $strSql = "select distinct c.elemento_contenedor_id idCaja, concat(concat(b.nombre_interface_elemento, ' - '), "
                    . "d.nombre_elemento) nombreCaja "
                    . "from DB_INFRAESTRUCTURA.info_elemento a "
                    . "inner join DB_INFRAESTRUCTURA.info_interface_elemento b on a.id_elemento = b.elemento_id "
                    . "inner join DB_COMERCIAL.info_servicio_tecnico c on c.elemento_id = a.id_elemento "
                    . "and c.interface_elemento_id = b.id_interface_elemento "
                    . "inner join DB_INFRAESTRUCTURA.info_elemento d on d.id_elemento = c.elemento_contenedor_id "
                    . "where a.id_elemento = :intIdOlt "
                    . "and b.id_interface_elemento in (" . $strIdPuerto . ")";

            $objQuery->setParameter("intIdOlt", $intIdOlt);

            $objRsm->addScalarResult('IDCAJA','idCaja','integer');
            $objRsm->addScalarResult('NOMBRECAJA','nombreCaja','string');

            return json_encode($objQuery->setSQL($strSql)->getResult());

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getClientesAfectados()  '.$e->getMessage());
        }
    
    }


    public function getSplitter($intIdOlt, $strIdPuerto, $strIdCaja)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        try
        {

            $strSql = "select distinct c.elemento_conector_id idSplitter, concat(concat(b.nombre_interface_elemento, ' - '), "
                    . "e.nombre_elemento) nombreSplitter "
                    . "from DB_INFRAESTRUCTURA.info_elemento a "
                    . "inner join DB_INFRAESTRUCTURA.info_interface_elemento b on a.id_elemento = b.elemento_id "
                    . "inner join DB_COMERCIAL.info_servicio_tecnico c on c.elemento_id = a.id_elemento "
                    . "and c.interface_elemento_id = b.id_interface_elemento "
                    . "inner join DB_INFRAESTRUCTURA.info_elemento d on d.id_elemento = c.elemento_contenedor_id "
                    . "inner join DB_INFRAESTRUCTURA.info_elemento e on e.id_elemento = c.elemento_conector_id "
                    . "where a.id_elemento = :intIdOlt "
                    . "and b.id_interface_elemento in (" . $strIdPuerto . ") "
                    . "and c.elemento_contenedor_id in (" . $strIdCaja . ")";

            $objQuery->setParameter("intIdOlt", $intIdOlt);

            $objRsm->addScalarResult('IDSPLITTER','idSplitter','integer');
            $objRsm->addScalarResult('NOMBRESPLITTER','nombreSplitter','string');

            return json_encode($objQuery->setSQL($strSql)->getResult());

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getSplitter()  '.$e->getMessage());
        }
    
    }



    public function getTiempoAfectacionIndisponibilidadCaso($intCasoId)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objRespuesta ;

        try
        {

            $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT abs(extract(day from (sysdate - a.fecha_inicial))) *24*60 + "
                ."abs(extract(hour from (sysdate - a.fecha_inicial))) *60 +  "
                ."abs(extract(minute from (sysdate - a.fecha_inicial))) tiempoafectacion  "
                ."FROM  "
                ."(select s.id_caso_historial , s.fe_creacion fecha_inicial, estado, s.caso_id, rownum  "
                ."from db_soporte.INFO_CASO_HISTORIAL s  "
                ."where caso_id = :intCasoId "
                ."and rownum = 1  "
                ."order by s.id_caso_historial) a";

            $objQuery->setParameter("intCasoId", $intCasoId);
            $objRsm->addScalarResult('TIEMPOAFECTACION','tiempoafectacion','integer');
        
            $objQuery->setSQL($strSql);

            $objRespuesta = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getTiempoAfectacionIndisponibilidadCaso()  '.$e->getMessage());
        }
        
        return $objRespuesta;
    
    }

    

    public function getArbolHipotesis($intCasoId)
    {
       
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objRespuesta ;

        try
        {

            $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "select a.ID_HIPOTESIS id_1, nvl(a.NOMBRE_HIPOTESIS, 'SIN HIPOTESIS INICIAL') nombre_1, "
            ."b.ID_HIPOTESIS id_2, nvl(b.NOMBRE_HIPOTESIS, 'SIN HIPOTESIS INICIAL') nombre_2, "
            ."c.ID_HIPOTESIS id_3, nvl(c.NOMBRE_HIPOTESIS, 'SIN HIPOTESIS INICIAL') nombre_3 "
            ."from DB_SOPORTE.INFO_DETALLE_HIPOTESIS d "
            ."left join ADMI_HIPOTESIS c on d.HIPOTESIS_ID = c.ID_HIPOTESIS "
            ."left join ADMI_HIPOTESIS b on b.ID_HIPOTESIS = c.HIPOTESIS_ID "
            ."left join ADMI_HIPOTESIS a on a.ID_HIPOTESIS = b.HIPOTESIS_ID  "
            ."and a.estado = 'Activo'  "
            ."and a.HIPOTESIS_ID = 0  "
            ."and a.EMPRESA_COD in (10, 18) "
            ."where d.caso_id = :intCasoId "
            ."order by 2, 4, 6";

            $objQuery->setParameter("intCasoId", $intCasoId);
            $objRsm->addScalarResult('ID_1','id_1','integer');
            $objRsm->addScalarResult('NOMBRE_1','nombre_1','string');
            $objRsm->addScalarResult('ID_2','id_2','integer');
            $objRsm->addScalarResult('NOMBRE_2','nombre_2','string');
            $objRsm->addScalarResult('ID_3','id_3','integer');
            $objRsm->addScalarResult('NOMBRE_3','nombre_3','string');

            $objRespuesta = $objQuery->setSQL($strSql)->getResult();

        }
        catch (\Exception $e)
        {
            error_log('AdmiTareaRepository->getTiempoAfectacionIndisponibilidadCaso()  '.$e->getMessage());
        }
        
        return json_encode($objRespuesta);
    
    }


    /**
     * Documentación para el método 'ejecutarCreacionHETareaFinalizada'.
     * 
     * Función que invoca al proceso de creacion solicitud horas extras al finalizar una tarea.
     *
     * @author Katherine Portugal <kportugal@telconet.ec>
     * @version 1.0, 13-09-2021
     * 
     */
    public function ejecutarCreacionHETareaFinalizada($arrayParametros)
    {
        $intIdEmpresa    = $arrayParametros['intIdEmpresa'];
        $intIdDetalle     = $arrayParametros['intIdDetalle'];
        $strIdTarea       = $arrayParametros['strIdTarea'];
        $intNumeroTarea   = $arrayParametros['intNumeroTarea'];
        $strUsrCreacion  = $arrayParametros['strUsrCreacion'];
        $strMensaje       = str_pad(' ', 200);
        try
        {
            $strSql  = "BEGIN DB_SOPORTE.SPKG_SOPORTE_TAREA.P_GENERA_HE_TAREA_FINALIZADA( :intIdEmpresa,   "
                                                                                       ." :intIdDetalle,    "
                                                                                       ." :strIdTarea,      "
                                                                                       ." :intNumeroTarea,  "
                                                                                       ." :strUsrCreacion, "
                                                                                       ." :strMensaje); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdEmpresa' , $intIdEmpresa);
            $objStmt->bindParam('intIdDetalle' , $intIdDetalle);
            $objStmt->bindParam('strIdTarea' , $strIdTarea);
            $objStmt->bindParam('intNumeroTarea' , $intNumeroTarea);
            $objStmt->bindParam('strUsrCreacion' , $strUsrCreacion); 
            $objStmt->bindParam('strMensaje' , $strMensaje); 
            $objStmt->execute();
        }
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar el proceso de creacion solicitud horas extras al finalizar una tarea: ". $ex->getMessage());
            $strMensaje = 'Error' + $ex->getMessage();
        }

        return $strMensaje;
    }

     /**
     * Documentación para el método 'getTareasBy'.
     * 
     * Función modulable que permite obtener listado de tareas según cristerios de búsqueda
     * 
     * @param array $parametros
     * 
     * @return array
     *
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.0, 08-02-2023
     * 
     */
    public function getTareasBy($arrayParametros)
	{
        $boolBusqueda       = false; 
        $strWhere           = "";
        $entityQuery        = $this->_em->createQuery();

        try
        {
            if(isset($arrayParametros['nombreTarea']) && strlen(trim($arrayParametros['nombreTarea']))>0)
            {
                $strWhere .= " AND t.nombreTarea = :nombreTarea ";
                $entityQuery->setParameter(":nombreTarea", $arrayParametros['nombreTarea']);
                $boolBusqueda =  true;
            }

            if(isset($arrayParametros['puntoId']) && intval($arrayParametros['puntoId'])>0)
            {
                $strWhere .= " AND c.puntoId = :puntoId ";
                $entityQuery->setParameter(":puntoId", $arrayParametros['puntoId']);
                $boolBusqueda =  true;
            }
    
            $strFrom = " FROM
            schemaBundle:InfoComunicacion c,
            schemaBundle:InfoDetalle d,
            schemaBundle:InfoTarea t
            WHERE		 				
            c.detalleId = d.id AND
            d.id        = t.detalleId AND
            lower(t.estado) not in(
                'finalizada',
                'rechazada',
                'anulada',
                'eliminado',
                'cancelada',
                'anulado'
            )
            {$strWhere}
            ";  
    
            if(!$boolBusqueda)
            {
                return array(
                    "registros" => array()
                );
            }
            else
            {
                $strSql = "SELECT c.id, d.observacion ";
                $strQuerySql = $strSql . $strFrom;
                $entityQuery->setDQL($strQuerySql);
        
                return array(
                    "registros" => $entityQuery->getResult()
                );
            }
        }
        catch (\Exception $ex) 
        {
            error_log('AdmiTareaRepository->getTareasBy()  '.$ex->getMessage());
             
            return array(
                "registros" => array()
            );
        }
    }

     /**
     * Documentación para el método 'findTareasActivasByProcesoTarea'.
     * 
     * Función que retorna las tareas en base al proceso y tarea.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0, 03-03-2023
     * 
     */
    public function findTareasActivasByProcesoTarea($arrayDatos)
    {
        $strProceso = $arrayDatos["procesoId"];
        $strTarea   = $arrayDatos["idTarea"];
        $strSql = "SELECT t 
                FROM schemaBundle:AdmiTarea t
                WHERE t.procesoId = '$strProceso' AND t.id = '$strTarea' AND t.estado not like 'Eliminado' ";
        
        $objQuery = $this->_em->createQuery($strSql);
        $arrayDatos = $objQuery->getResult();
        return $arrayDatos;
    }
}
