<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class VistaClientesSupertelRepository extends EntityRepository
{	
    public function generarJsonReporteSupertel($parametros,$start,$limit, $em, $emInfraestructura)
    {
        $arr_encontrados = array();
		
        //$rsTotal= $this->getCasos($parametros,'','',$session);
        $resultado= $this->getReporteSupertel($parametros,$start,$limit);
        $num = $resultado['total'];
		$rs = $resultado['registros'];
		
        if(isset($rs))
        {		
            //$num = count($rsTotal);
            foreach ($rs as $entidad)
            {
				$master_account = "";	$coordenadas = "";				
				$telefono1 = "";	$telefonos2 = "";
				$tipo_enlace = "";
				$numeroPC = "";			$velocidad_contratada = "";
				$usuarioActivacion = "";	$usuarioUltimoEstado = "";
				$id_elemento = ""; 			$nombre_elemento = "";
				$id_elemento_padre = "";	$nombre_elemento_padre = "";
				
				$telefono1=  $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getStringFormasContactoParaSession($entidad["idPersona"], "Telefono Fijo");
				$telefonos2=  $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getStringFormasContactoParaSession($entidad["idPersona"], "Telefono Movil");
				/*
				$arrayTelefonosFijos = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getFormasContactoParaSession($entidad["idPersona"], "Telefono Fijo");
				$arrayTelefonosMoviles = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getFormasContactoParaSession($entidad["idPersona"], "Telefono Movil");
				if($arrayTelefonosFijos && count($arrayTelefonosFijos)>0)
				{
					$array_telefonos1 = '';
					foreach($arrayTelefonosFijos as $telefono)
					{
						if($telefono["valor"] != "" && $telefono["valor"])
						{
							$array_telefonos1[] = $telefono["valor"];
						}
					}
					
					$string_telefono1 = implode(", ", $array_telefonos1);
					$telefono1= "".$string_telefono1."";
				}
				if($arrayTelefonosMoviles && count($arrayTelefonosMoviles)>0)
				{
					$array_telefonos2 = '';
					foreach($arrayTelefonosMoviles as $telefono)
					{
						if($telefono["valor"] != "" && $telefono["valor"])
						{
							$array_telefonos2[] = $telefono["valor"];
						}
					}
					
					$string_telefonos2 = implode(", ", $array_telefonos2);
					$telefonos2= "".$string_telefonos2."";
				}
				*/
				if($entidad["ultimaMillaId"])
				{
					$infoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')->getTipoMedioByUltimaMilla($entidad["ultimaMillaId"]);
					if($infoEnlace && count($infoEnlace)>0)
					{
						$tipo_enlace = $infoEnlace[0]["nombreTipoMedio"];
					}
					/*$infoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')->findOneById($entidad["ultimaMillaId"]);					
					$tipoMedioEntity = (($infoEnlace && count($infoEnlace)>0) ? ($infoEnlace->getTipoMedioId() ? $infoEnlace->getTipoMedioId() : "") : "");
					
					if($tipoMedioEntity && count($tipoMedioEntity)>0)
					{
						$id_tipo_enlace = $tipoMedioEntity->getId() ? $tipoMedioEntity->getId() : "";
						$tipo_enlace = $tipoMedioEntity->getNombreTipoMedio() ? $tipoMedioEntity->getNombreTipoMedio() : "";
					}//fin if tipomedio 
					*/
				}// fin if ultima milla id
				
				if($entidad["idProducto"] && $entidad["idPlanDet"])
				{
					$registro_Velocidad = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaracteristicaByParametros($entidad["idPlanDet"], $entidad["idProducto"], 'CAPACIDAD1');
					$registro_NumPC = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaracteristicaByParametros($entidad["idPlanDet"], $entidad["idProducto"], 'NUMERO PC');
					
					if($registro_Velocidad && count($registro_Velocidad)>0)
					{
						$velocidad_contratada = $registro_Velocidad[0]["valor"];
					}
					if($registro_NumPC && count($registro_NumPC)>0)
					{
						$numeroPC = $registro_NumPC[0]["valor"];
					}
					//CARACTERSISTICAS NUMEROPC 10  CAPACIDAD1 1  INFO SERVICIO PROD CARAC
					/*$entityCaracteristica_Velocidad = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("CAPACIDAD1");
					$entityCaracteristica_NumPC = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("NUMERO PC");
					$caracteristica_Velocidad = ($entityCaracteristica_Velocidad ? ($entityCaracteristica_Velocidad->getId() ? $entityCaracteristica_Velocidad->getId() : '') : ''); 
					$caracteristica_NumPC = ($entityCaracteristica_NumPC ? ($entityCaracteristica_NumPC->getId() ? $entityCaracteristica_NumPC->getId() : '') : '');   
					
					if($caracteristica_Velocidad)
					{
						$productoCaracteristica_Velocidad = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array("productoId"=>$entidad["idProducto"], "caracteristicaId" => $caracteristica_Velocidad));
						$prodcarac_Velocidad = ($productoCaracteristica_Velocidad ? ($productoCaracteristica_Velocidad->getId() ? $productoCaracteristica_Velocidad->getId() : '') : '');  
						
						if($prodcarac_Velocidad)
						{
							$registro_Velocidad = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array("planDetId"=>$entidad["idPlanDet"], "productoCaracterisiticaId" => $prodcarac_Velocidad));
							$velocidad_contratada = ($registro_Velocidad ? ($registro_Velocidad->getId() ? $registro_Velocidad->getId() : '') : '');  
						}
					}
					if($caracteristica_NumPC)
					{
						$productoCaracteristica_NumPC = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array("productoId"=>$entidad["idProducto"], "caracteristicaId" => $caracteristica_NumPC));
						$prodcarac_NumPC = ($productoCaracteristica_NumPC ? ($productoCaracteristica_NumPC->getId() ? $productoCaracteristica_NumPC->getId() : '') : '');  
						
						if($prodcarac_NumPC)
						{
							$registro_NumPC = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array("planDetId"=>$entidad["idPlanDet"], "productoCaracterisiticaId" => $prodcarac_NumPC));
							$numeroPC = ($registro_NumPC ? ($registro_NumPC->getId() ? $registro_NumPC->getId() : '') : '');  
						}
					}*/
				}
				
				if($entidad["usuarioActivacion"])
				{
					$empleadoActivacion = $em->getRepository('schemaBundle:InfoPersona')->getMiniPersonaPorLogin($entidad["usuarioActivacion"]);
					if($empleadoActivacion && count($empleadoActivacion)>0)
					{
						$usuarioActivacion = $empleadoActivacion[0]["nombres"] . " " . $empleadoActivacion[0]["apellidos"];
						//(($empleadoActivacion->getNombres() && $empleadoActivacion->getApellidos()) ? $empleadoActivacion->getNombres() . " " . $empleadoActivacion->getApellidos() : "");
					}
				}
				if($entidad["usuarioUltimoEstado"])
				{
					$empleadoUltimoEstado = $em->getRepository('schemaBundle:InfoPersona')->getMiniPersonaPorLogin($entidad["usuarioUltimoEstado"]);
					if($empleadoUltimoEstado && count($empleadoUltimoEstado)>0)
					{
						$usuarioUltimoEstado = $empleadoUltimoEstado[0]["nombres"] . " " . $empleadoUltimoEstado[0]["apellidos"];
						//$usuarioUltimoEstado = (($empleadoUltimoEstado->getNombres() && $empleadoUltimoEstado->getApellidos()) ? $empleadoUltimoEstado->getNombres() . " " . $empleadoUltimoEstado->getApellidos() : "");
					}
				}
							
				if($entidad["interfaceElementoId"])
				{
					$infoElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->getElementosByInterfaceElemento($entidad["interfaceElementoId"]);
					if($infoElemento && count($infoElemento)>0)
					{
						$id_elemento = $infoElemento[0]["idElemento"];
						$nombre_elemento = $infoElemento[0]["nombreElemento"];
						$id_elemento_padre = $infoElemento[0]["idElementoPadre"];
						$nombre_elemento_padre = $infoElemento[0]["nombreElementoPadre"];
					}
					/*$interfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->findOneById($data["interfaceElementoId"]);					
					$dslamElemento = (($interfaceElemento && count($interfaceElemento)>0) ? ($interfaceElemento->getElementoId() ? $interfaceElemento->getElementoId() : "") : "");
					
					if($dslamElemento && count($dslamElemento)>0)
					{
						$id_elemento = $dslamElemento->getId() ? $dslamElemento->getId() : "";
						$nombre_elemento = $dslamElemento->getNombreElemento() ? $dslamElemento->getNombreElemento() : "";
						
						$relacionPop = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$dslamElemento->getId()));
						if($relacionPop && count($relacionPop)>0)
						{
							$popElementoId = $relacionPop[0]->getElementoIdA();
							$popElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($popElementoId);
							
							if($popElemento && count($popElemento)>0)
							{
								$id_elemento_padre = $popElemento->getId() ? $popElemento->getId() : "";
								$nombre_elemento_padre = $popElemento->getNombreElemento() ? $popElemento->getNombreElemento() : "";	
							}//fin if pop elemento
						}	//fin if relacion pop
					}//fin if dslam elemento*/
				}// fin if interface elemento id
				
					
				$ultimo_estado = ($entidad["ultimoEstado"] ? $entidad["ultimoEstado"] : "");
				$fecha_ultimo_estado = ( $ultimo_estado != "Activo" ? ($entidad["fechaUltimoEstado"] ? date_format($entidad["fechaUltimoEstado"],"d-m-Y G:i") : "") : "");
						
				$arr_encontrados[]=array(
										 'id_servicio_historial'=> $entidad["id"] ? $entidad["id"] : "",
										 'id_servicio'=> $entidad["idServicio"] ? $entidad["idServicio"] : "",
										 'id_sh_activacion'=> $entidad["idActivacion"] ? $entidad["idActivacion"] : "",
										 'id_punto'=> $entidad["idPunto"] ? $entidad["idPunto"]: "",
										 'id_persona'=> $entidad["idPersona"] ? $entidad["idPersona"] : "",
										 'id_persona_empresa_rol'=> $entidad["idPersonaRol"] ? $entidad["idPersonaRol"] : "",
										 'id_empresa_rol'=> $entidad["idEmpresaRol"] ? $entidad["idEmpresaRol"] : "",
										 'id_empresa'=> $entidad["empresaCod"] ? $entidad["empresaCod"] : "",
										 'id_tipo_negocio'=> $entidad["idTipoNegocio"] ? $entidad["idTipoNegocio"] : "",
										 'id_sector'=> $entidad["idSector"] ? $entidad["idSector"] : "",
										 'id_parroquia'=> $entidad["idParroquia"] ? $entidad["idParroquia"] : "",
										 'id_canton'=> $entidad["idCanton"] ? $entidad["idCanton"] : "",
										 'id_contrato'=> $entidad["idContrato"] ? $entidad["idContrato"] : "",
										 'id_tipo_contrato'=> $entidad["idTipoContrato"] ? $entidad["idTipoContrato"] : "",
										 'id_plan'=> $entidad["idPlan"] ? $entidad["idPlan"] : "",
										 'id_interface_elemento'=> $entidad["interfaceElementoId"] ? $entidad["interfaceElementoId"] : "",
										 'id_elemento'=> $id_elemento ? $id_elemento : "",
										 'id_elemento_padre'=> $id_elemento_padre ? $id_elemento_padre : "",
										
										 'login2' => $entidad["login"] ? $entidad["login"] : "",
										 'ciudad' => $entidad["nombreCanton"] ? $entidad["nombreCanton"] : "",
										 'nombreParroquia' => $entidad["nombreParroquia"] ? $entidad["nombreParroquia"] : "",
										 'nombreSector' => $entidad["nombreSector"] ? $entidad["nombreSector"] : "",
										 'cliente_nombres' => $entidad["nombres"] ? $entidad["nombres"] : "",
										 'cliente_apellidos' => $entidad["apellidos"] ? $entidad["apellidos"] : "",
										 'cliente' => ($entidad["nombres"] || $entidad["apellidos"] ? $entidad["nombres"] . " " . $entidad["apellidos"] : ""),
										 'razonSocial' => $entidad["razonSocial"] ? $entidad["razonSocial"] : "",
										 'vendedor' => $entidad["usrVendedor"] ? $entidad["usrVendedor"] : "",
										 'direccionPto' =>($entidad["direccionPunto"] ? $entidad["direccionPunto"] : ""),
										 'telefono1' =>($telefono1 ? $telefono1 : ""),
										 'telefonos2' =>($telefonos2 ? $telefonos2 : ""),
										 'numeroPC' =>($numeroPC ? $numeroPC : ""),
										 'tipo_enlace' =>($tipo_enlace ? $tipo_enlace : ""),
										 'velocidad_contratada' =>($velocidad_contratada ? $velocidad_contratada : ""),
										 'tipo_contrato' =>($entidad["tipoContrato"] ? $entidad["tipoContrato"] : ""),
										 'ultimo_estado' => $ultimo_estado,
										 'master_account' =>($master_account ? $master_account : ""),
										 'tipo_cuenta' =>($entidad["tipoCuenta"] ? $entidad["tipoCuenta"] : ""),
										 'ServicioPlan' =>($entidad["servicio"] ? $entidad["servicio"] : ""),
										 'descripcionProducto' =>($entidad["descripcionProducto"] ? $entidad["descripcionProducto"] : ""),										 
										 'dslam' =>($nombre_elemento ? $nombre_elemento : ""),
										 'pop' =>($nombre_elemento_padre ? $nombre_elemento_padre : ""),
										 'fechaUltimoEstado' => $fecha_ultimo_estado,
										 'fechaActivacion' =>($entidad["fechaActivacion"] ? date_format($entidad["fechaActivacion"],"d-m-Y G:i") : ""),
										 'usuarioActivacion' => ($usuarioActivacion ? ucwords(strtolower($usuarioActivacion)) : ""),
										 'usrUltimoEstado' => ($usuarioUltimoEstado ? ucwords(strtolower($usuarioUltimoEstado)) : ""),
										 'rutaCroquis' =>"",
										 'coordenadas' =>($coordenadas ? $coordenadas : ""),
										 'latitud' =>($entidad["latitud"] ? $entidad["latitud"] : ""),
										 'longitud' =>($entidad["longitud"] ? $entidad["longitud"] : ""),
										 
										 'action1' => 'button-grid-show',
								 );
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }
	
    public function getReporteSupertel($parametros, $start,$limit)
    {
        $whereVar = "";
        $fromAdicional = "";
        $whereAdicional = "";
		
        if($parametros && count($parametros)>0)
        {
			if(isset($parametros["codEmpresa"]))
            {
                if($parametros["codEmpresa"] && $parametros["codEmpresa"]!="")
                {
                    $whereVar .= "AND v.empresaCod = '".trim($parametros["codEmpresa"])."' ";
                }
            }	
			
            if(isset($parametros["estado"]))
            {
                if($parametros["estado"] && $parametros["estado"]!="")
                {
					if($parametros["estado"]=="Todos")
					{
						$whereVar .= "AND (
											lower(v.ultimoEstado) like lower('Activo') OR
											lower(v.ultimoEstado) like lower('In-Corte') OR
											lower(v.ultimoEstado) like lower('In-Corte-SinEje')
										   ) ";
					}
					else if($parametros["estado"]=="Activo")
					{
						$whereVar .= "AND lower(v.ultimoEstado) like lower('".$parametros["estado"]."') ";
					}
					else if($parametros["estado"]=="Inactivo")
					{
						$whereVar .= "AND (
											lower(v.ultimoEstado) like lower('In-Corte') OR
											lower(v.ultimoEstado) like lower('In-Corte-SinEje')
										   ) ";
					}
					else
					{
						$whereVar .= "AND (
											lower(v.ultimoEstado) like lower('Activo') OR
											lower(v.ultimoEstado) like lower('In-Corte') OR
											lower(v.ultimoEstado) like lower('In-Corte-SinEje')
										   ) ";
					}		
					//$whereVar .= "AND lower(v.ultimoEstado) like lower('".$parametros["estado"]."') ";
                }
            }
			
			$feDesde = (isset($parametros["fechaDesde"]) ? $parametros["fechaDesde"] : 0);
			$feHasta = (isset($parametros["fechaHasta"]) ? $parametros["fechaHasta"] : 0);
			if($feDesde && $feDesde!="0")
			{
				$dateF = explode("-",$feDesde);
				$feDesde = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
			}
			if($feHasta && $feHasta!="0")
			{
				$dateF = explode("-",$feHasta);
				$fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
				$feHasta = date("Y/m/d", $fechaSqlAdd);
			}			
			if($feDesde && $feDesde!="0") { $whereVar .= "AND v.fechaActivacion >= '".trim($feDesde)."' ";   }
			if($feHasta && $feHasta!="0") { $whereVar .= "AND v.fechaActivacion < '".trim($feHasta)."' ";   }
			
			
			$feUEDesde = (isset($parametros["fechaUEDesde"]) ? $parametros["fechaUEDesde"] : 0);
			$feUEHasta = (isset($parametros["fechaUEHasta"]) ? $parametros["fechaUEHasta"] : 0);
			if($feUEDesde && $feUEDesde!="0")
			{
				$dateF = explode("-",$feUEDesde);
				$feUEDesde = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
			}
			if($feUEHasta && $feUEHasta!="0")
			{
				$dateF = explode("-",$feUEHasta);
				$fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
				$feUEHasta = date("Y/m/d", $fechaSqlAdd);
			}			
			if($feUEDesde && $feUEDesde!="0") { $whereVar .= "AND v.fechaUltimoEstado >= '".trim($feUEDesde)."' ";   }
			if($feUEHasta && $feUEHasta!="0") { $whereVar .= "AND v.fechaUltimoEstado < '".trim($feUEHasta)."' ";   }
        }            
		
		$selectedCont = "count(v) as cont ";
		//$selectedData = "DISTINCT v, p ";
		$selectedData = "
							v.id, v.idServicio, v.idActivacion, v.idPunto, v.idPersona, v.idPersonaRol, v.idEmpresaRol, v.empresaCod, 
							v.idTipoNegocio, v.idSector, v.idParroquia, v.idCanton, v.idContrato, v.idTipoContrato, v.idPlan, 
							v.login, v.nombreCanton, v.nombreParroquia, v.nombreSector, v.nombres, v.apellidos, v.razonSocial, v.direccionPunto,
							v.tipoContrato, v.ultimoEstado, v.tipoCuenta, v.interfaceElementoId, v.servicio, v.fechaUltimoEstado, v.ultimaMillaId, 
							v.usuarioUltimoEstado, v.usrVendedor, v.latitud, v.longitud, v.fechaActivacion, v.usuarioActivacion,
							v.observacionActivacion,  v.observacion, pd.id as idPlanDet, p.id as idProducto, p.codigoProducto, p.descripcionProducto 							
						";
						
		$from = "FROM schemaBundle:VistaClientesSupertel v 
				 LEFT JOIN schemaBundle:InfoPlanDet pd WITH pd.planId = v.idPlan 
				 LEFT JOIN schemaBundle:AdmiProducto p WITH pd.productoId = p.id  ";
		
		$wher = " WHERE 
					v.idServicio is not null 
					AND lower(pd.estado) like lower('Activo') 
					AND lower(p.estado) like lower('Activo') 
                    $whereVar 
                ";

		$sql = "SELECT $selectedData $from $wher ";
		$sqlC = "SELECT $selectedCont $from $wher ";
				
        $queryC = $this->_em->createQuery($sqlC); 
        $query = $this->_em->createQuery($sql); 

		$resultTotal = $queryC->getOneOrNullResult();
		$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
	
}