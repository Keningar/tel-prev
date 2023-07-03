<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoComunicacionRepository extends EntityRepository
{
     /**
     * Funcion que sirve para obtener todas las llamadas/actividades generadas en cada comunicacion con la informacion completa
     * relacionada de donde proviene Caso/Tarea
     *
     * @author Modificado: Andrés Montero H. <amontero@telconet.ec>
     * @version 1.5 11-01-2021 Se agrega validación con perfil para el botón de finalizar tarea.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 19-11-2016 Se agrega validación para que el botón de finalizar tarea no se cuando el estado de la tarea sea Asignada
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 22-07-2016 - Se realizan ajustes para poder reutilizar esta funcion desde el modulo de Tareas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 31-03-2016 Se realizan ajustes porque se esta agregando una herramienta de finalizar las tareas desde consulta de Actividades
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 24-08-2015 ( Se actualiza enviando a la funcion de consulta de manera independiente que se desea
     *                          traer el total de registros y la data dado una variable ( cont o data )
     * 
     * @version 1.0 Version Inicial
     * 
     * @param array $parametros
     * @param int $start
     * @param int $limit
     * @param enityManager $emSoporte
     * @return array resultado
     */
    public function generarJsonComunicaciones($parametros, $start, $limit, $emSoporte)
    {
        $arr_encontrados = array();
        $cerrarTarea     = "S";
        $total     = $this->getComunicaciones($parametros, $start, $limit, 'cont');
        $resultado = $this->getComunicaciones($parametros, $start, $limit, 'data');

        $boolTienePerfilFinaliza = $parametros['boolPerfilFinalizarTarea'];

        if(isset($resultado))
        {
            $num = $total;
            $rs = $resultado;

            if($rs && count($rs) > 0)
			{
				foreach ($rs as $data)
				{																		
					$origenGenera = ($data["casoId"] != '' ? "Caso" : "");
					$origenGenera = ($data["detalleId"] != '' && $data["casoId"] == '' ? "Tarea" : $origenGenera);
					$origenGenera = ($data["detalleId"] == '' && $data["casoId"] == '' ? "Ninguno" : $origenGenera);										
					
					$numCaso = ""; 	$nombreTarea = ""; 
					
					$estado = ""; $estadoCaso = "";
					
					$nombreAsignada = ""; $departamentoAsignado=""; 
					
					$esTarea = true;
					
					if($origenGenera == "Caso")
					{
						$caso = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneById($data["casoId"]);
						$numCaso = ($caso ? $caso->getNumeroCaso() : "");
						
						$esTarea=false;
						
						$estadoCaso = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstadoCaso($data["casoId"]);
												
					}
					if($origenGenera == "Tarea")
					{
						$objDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($data["detalleId"]);

                        $strNombreTarea = ($objDetalle ? 
                            ($objDetalle->getTareaId() ? 
                                ($objDetalle->getTareaId()->getNombreTarea() ? 
                                    $objDetalle->getTareaId()->getNombreTarea() : "") : "") : "");

                        if (is_object($objDetalle)) 
                        {
                                                    
                            $arrayDetalleAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                            ->findByDetalleId($objDetalle->getId());
                                                    
                            $nombreAsignada = $arrayDetalleAsignacion[count($arrayDetalleAsignacion)-1]->getRefAsignadoNombre();
                            $departamentoAsignado = $arrayDetalleAsignacion[count($arrayDetalleAsignacion)-1]->getAsignadoNombre();
                                                    
                            $esTarea = true;
                            
                            $estado = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($data["detalleId"]);


                                        //Se obtiene la fecha en que se asigno la tarea
                                        $arrayFechaAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                                ->getUltimaFechaAsignacion($data["detalleId"],
                                                                    $arrayDetalleAsignacion[count($arrayDetalleAsignacion)-1]->getRefAsignadoId(),
                                                                    $arrayDetalleAsignacion[count($arrayDetalleAsignacion)-1]->getAsignadoId());

                                        if($arrayFechaAsignacion[0]['fecha'] != "")
                                        {
                                            $strFechaEjecucion = $arrayFechaAsignacion[0]['fecha'];

                                            $arrayFecha = explode(" ", $strFechaEjecucion);
                                            $arrayFech = explode("-", $arrayFecha[0]);
                                            $arrayHora = explode(":", $arrayFecha[1]);
                                            $strFechaEjecucion = $arrayFech[2] . "-" . $arrayFech[1] . "-" . $arrayFech[0];
                                            $strHoraEjecucion = $arrayHora[0] . ":" . $arrayHora[1];
                                        }
                                        else
                                        {
                                            $strFechaEjecucion = "";
                                            $strHoraEjecucion = "";
                                        }

                                        //Se obtiene la fecha y hora actual para el cierre de la tarea
                                        $objFechaActual =  new \DateTime('now');
                                        $strFecha       =  $objFechaActual->format('Y-m-d');
                                        $strHora        =  $objFechaActual->format('H:i');

                        }
					}

                    //Se obtiene las tareas en base al id_detalle y se verifica si tiene tareas abiertas
                    $entityDetallesRelacionados = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                            ->findByDetalleIdRelacionado($data["detalleId"]);

                    foreach($entityDetallesRelacionados as $entity)
                    {
                        $entityUltimoEstado = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                        ->getUltimoEstado($entity->getId());
                        if($entityUltimoEstado)
                        {
                            if($entityUltimoEstado->getEstado() === "Aceptada" || $entityUltimoEstado->getEstado() === "Asignada" ||
                                $entityUltimoEstado->getEstado() === "Reprogramada")
                            {
                                $cerrarTarea = "N";
                            }
                        }
                    }

                    $arr_encontrados[] = array('id_comunicacion'      => $data['idComunicacion'],
                                               'cerrarTarea'          => $cerrarTarea,
                                               'cliente'              => ($data['remitenteNombre'] ? $data['remitenteNombre'] : "N/A"),
                                               'claseDocumento'       => ($data['nombreClaseDocumento'] ? $data['nombreClaseDocumento'] : "N/A"),
                                               'origenGenera'         => ($origenGenera ? $origenGenera : ""),
                                               'departamentoAsignado' => $departamentoAsignado ? $departamentoAsignado : "",
                                               'nombreAsignada'       => $nombreAsignada ? $nombreAsignada : "",
                                               'idCaso'               => ($data["casoId"] ? $data["casoId"] : ""),
                                               'numCaso'              => ($numCaso ? $numCaso : ""),
                                               'idDetalle'            => ($data["detalleId"] ? $data["detalleId"] : ""),
                                               'idTarea'              => $objDetalle->getTareaId()->getId(),
                                               'nombreTarea'          => ($strNombreTarea ? $strNombreTarea : ""),
                                               'fechaActual'          => $strFecha,
                                               'horaActual'           => $strHora,
                                               'fechaEjecucion'       => $strFechaEjecucion,
                                               'horaEjecucion'        => $strHoraEjecucion,
                                               'descripcion'          => ($data['mensaje'] ? $data['mensaje'] : "N/A"),
                                               'fecha'                => ($data['fechaComunicacion'] ?
                                                                          date_format($data['fechaComunicacion'], "d-m-Y") : "N/A"),
                                               'hora'                 => ($data['fechaComunicacion'] ?
                                                                          date_format($data['fechaComunicacion'], "G:i") : ""),
                                               'clase'                => 'Recibido',
                                               'estado'               => $data['estado'],
                                               'estadoCaso'           => $estadoCaso ? $estadoCaso : "",
                                               'estadoTarea'          => $estado ? $estado : "",
                                               'action1'              => 'button-grid-show',
                                               'action2'              => 'button-grid-invisible',
                                               'action3'              => (trim($data['estado']) == 'Eliminado' ?
                                                                         'button-grid-invisible' : 'button-grid-delete'),
                                               'action4'              => $esTarea ? ($estado != "Finalizada" ? ($estado != "Cancelada" ?
                                                                                     'button-grid-agregarSeguimiento' : 'button-grid-invisible') :
                                                                                     'button-grid-invisible') : 'button-grid-invisible',
                                               'action5'              => $esTarea ? 'button-grid-show' : 'button-grid-invisible',
                                               'action6'              => $esTarea ? 
                                               (($estado != "Asignada" && $estado != "Finalizada") ? 
                                                                                    ($estado != "Cancelada" ? (( $boolTienePerfilFinaliza )?
                                                                                                               'button-grid-finalizarTarea': 
                                                                                                               'button-grid-invisible') : 
                                                                                                               'button-grid-invisible') :
                                                                                    'button-grid-invisible') : 'button-grid-invisible');
                }
				$data=json_encode($arr_encontrados);
				$resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

				return $resultado;
			}
        }
        else
        {
            $resultado= '{"total":"0","acciones":[]}';

            return $resultado;
        }
    }

    /**
     * getMinimaComunicacionPorDetalleId
     *
     * Método que obtiene el minimo id_comunicacion segun el detalle_id que se envia
     *
     * @param $detalleId
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 01-07-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 15-09-2016 Se usa la funcion getSingleScalarResult
     */
    public function getMinimaComunicacionPorDetalleId($detalleId)
    {
        $query = $this->_em->createQuery();

        $sql   = " SELECT MIN( infoComunicacion.id ) as comunicacionInicial
                    FROM  schemaBundle:InfoComunicacion infoComunicacion
                    WHERE infoComunicacion.detalleId = :detalleId ";

        $query->setParameter('detalleId',$detalleId);

        $query->setDQL($sql);

        $arrayDatos = $query->getSingleScalarResult();

        return $arrayDatos;
    }

    /**
     * getDetalleIdCoordenadaPunto
     * Método para obtener el idDetalle de una tarea de Actualizacion de coordenada
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 - 26-02-2018.
     * @param array $arrayParametro
     * @return array
     */
    public function getDetalleIdCoordenadaPunto($arrayParametro)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        $strSQL = "SELECT MAX(infCom.detalle_id) DETALLE_ID
                    FROM DB_SOPORTE.INFO_COMUNICACION infCom,
                         DB_SOPORTE.INFO_DETALLE infDet,
                         DB_SOPORTE.ADMI_TAREA admTa
                    WHERE infCom.DETALLE_ID = infDet.ID_DETALLE
                    AND infDet.TAREA_ID     = admTa.ID_TAREA
                    AND infCom.PUNTO_ID     = :intPuntoId
                    AND admTa.NOMBRE_TAREA  = :strNombreTarea";
        $rsm->addScalarResult('DETALLE_ID', 'detalleId', 'integer');
        $query->setParameter('intPuntoId',$arrayParametro['intPuntoId']);
        $query->setParameter('strNombreTarea',$arrayParametro['strNombreTarea']);
        $query->setSQL($strSQL);
        $arrayResultado = $query->getResult();
        return $arrayResultado;
    }

    /**
     * Funcion que sirve para obtener todas las llamadas/actividades generadas en cada comunicacion, devuelve la data o el total
     * de registros en general
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.5 27-05-2022 Se agrega validacion para mejora de armado de query de consulta de documentos de actividades
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 18-01-2021 Se cambia validaciones para agregar filtro por estado en el query: se elimina el lower y el not like
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 07-10-2019 Se agrega en el query principal criterio para consultar también por nombre_documento='Registro de tarea'
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-03-2016 Se realiza ajustes por requerimiento que permite cargar las llamadas del dia y del area
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 22-08-2015
     * 
     * @version 1.0 Version Inicial
     * 
     * @param array $parametros
     * @param int $start
     * @param int $limit
     * @param string $tipo
     * @return array resultado ( total o data )
     */
    public function getComunicaciones($parametros, $start, $limit , $tipo)
    {
        $whereAdicional = "";
        $whereVar = "";
        $fromAdicional = "";
        $intValidar = 0;

        $query      = $this->_em->createQuery();        

        if($parametros && count($parametros) > 0)
        {
            if(isset($parametros["idPunto"]))
            {
                if($parametros["idPunto"] && $parametros["idPunto"] != "")
                {
                    $whereVar .= "AND c.remitenteId =  :idPunto ";
                    $query->setParameter('idPunto', $parametros["idPunto"]);
                    $intValidar = 1;
                }
            }
            if(isset($parametros["login"]))
            {
                if($parametros["login"] && $parametros["login"] != "")
                {
                    $whereVar .= "AND c.remitenteId in "
                        . "(select p.id from schemaBundle:InfoPunto p where p.login = :login ) ";
                    $query->setParameter('login', $parametros["login"]);
                    $intValidar = 1;
                }
            }

            if(isset($parametros["idClaseDocumento"]))
            {
                if($parametros["idClaseDocumento"] && $parametros["idClaseDocumento"] != "")
                {
                    $whereVar .= "AND acd.id =  :claseDocumento ";
                    $query->setParameter('claseDocumento', $parametros["idClaseDocumento"]);
                    $intValidar = 1;
                }
            }           

            if(isset($parametros["tipo_genera"]))
            {
                if($parametros["tipo_genera"] && $parametros["tipo_genera"] != "")
                {
                    if($parametros["tipo_genera"] == "T")
                    {
                        $whereVar .= "AND c.detalleId is not null AND c.casoId is null ";
                        $intValidar = 1;
                    }
                    else if($parametros["tipo_genera"] == "C")
                    {
                        $whereVar .= "AND c.casoId is not null ";
                        $intValidar = 1;
                    }                    
                }
            }

            if(isset($parametros["estado"]) && !empty($parametros["estado"]) && $parametros["estado"] != "Todos")
            {
                $whereVar .= " AND c.estado = :estado ";
                $query->setParameter('estado', $parametros["estado"]);
            }

            if(isset($parametros["feDesde"]))
            {
                if($parametros["feDesde"] != "")
                {
                    $dateF = explode("-", $parametros["feDesde"]);
                    $fechaSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));                   
                    $whereVar .= "AND c.fechaComunicacion >= :feDesde ";
                    $query->setParameter('feDesde', $fechaSql);
                    $intValidar = 1;
                }
            }

            if(isset($parametros["feHasta"]))
            {
                if($parametros["feHasta"] != "")
                {
                    $dateF = explode("-", $parametros["feHasta"]);
                    $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0])) . " +1 day");
                    $fechaSql = date("Y/m/d", $fechaSqlAdd);                   
                    $whereVar .= "AND c.fechaComunicacion <= :feHasta ";
                    $query->setParameter('feHasta', $fechaSql);
                    $intValidar = 1;
                }
            }

            if(isset($parametros['empresa']))
            {
                if($parametros['empresa'] != "")
                {                    
                    $whereVar .= "AND c.empresaCod = :empresa ";
                    $query->setParameter('empresa', $parametros['empresa']);
                }
            }

            if(isset($parametros['asignado']) || $parametros["departamento"])
            {
                if($parametros['asignado'] != "" || $parametros["departamento"])
                {                    
                    $fromAdicional .= " , schemaBundle:InfoDetalleAsignacion ida , 
                                          schemaBundle:InfoDetalle id ";
                    $whereVar .= "  AND c.detalleId = id.id
                                    AND id.id = ida.detalleId ";
                    $intValidar = 1;
                    if($parametros["asignado"] != "")
                    {
                       $whereVar .= " AND ida.refAsignadoId = :asignado ";

                       $query->setParameter('asignado', $parametros['asignado']);
                    }
                    else if($parametros["departamento"] != "")
                    {
                       $whereVar .= " AND ida.asignadoId = :departamento ";
                       $query->setParameter('departamento', $parametros['departamento']);

                       $fechaSqlAdd = strtotime(date("Y-m-d", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))));
                       $fechaD = date("Y/m/d",$fechaSqlAdd);
                       $whereVar .= "AND c.fechaComunicacion >= :fDesde ";

                       $query->setParameter('fDesde', $fechaD);

                       $fechaSqlAdd = strtotime(date("Y-m-d", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))) . " +1 day");
                       $fechaH = date("Y/m/d",$fechaSqlAdd);
                       $whereVar .= "AND c.fechaComunicacion < :fHasta ";

                       $query->setParameter('fHasta', $fechaH);
                    }


                }
            }
            if(isset($parametros['actividad']))
            {
                if($parametros['actividad'] != "")
                {                    
                    $whereVar .= "AND c.id = :actividad ";
                    $query->setParameter('actividad', $parametros['actividad']);
                    $intValidar = 1;
                }
            }
        }

        if ( $intValidar == 0) 
        {
            return null;
        }

        $selectedCont = " count(c) as cont ";
        $selectedData = "
					c.id as idComunicacion, d.id as idDocumento, dc.id as idDocumentoComunicacion, acd.id as idClaseDocumento, 
					c.formaContactoId, c.casoId, c.detalleId, c.remitenteId, c.remitenteNombre, c.estado, c.fechaComunicacion, 
					d.nombreDocumento, d.estado as estadoDocumento, d.mensaje,
					acd.nombreClaseDocumento, acd.estado as estadoClaseDocumento 
							
						";
        $from = "FROM 
					schemaBundle:InfoComunicacion c,
					schemaBundle:InfoDocumento d,
					schemaBundle:InfoDocumentoComunicacion dc,
					schemaBundle:AdmiClaseDocumento acd 
				" . $fromAdicional;
        $wher = "WHERE 
					dc.comunicacionId = c.id 
					AND dc.documentoId = d.id 
					AND d.claseDocumentoId = acd.id 
					AND (d.nombreDocumento = :RegistroDeLlamada OR d.nombreDocumento = :RegistroDeTarea) 
                    $whereAdicional 
                    $whereVar 
                ";
        $query->setParameter('RegistroDeLlamada', 'Registro de llamada.'); 
        $query->setParameter('RegistroDeTarea', 'Registro de tarea');      
       
        
        if($tipo == 'data')
        {
            //Costo del Query 19
            $sql  = "SELECT $selectedData $from $wher ORDER BY c.id DESC";
            $query->setDQL($sql);
            if($start!='' && $limit!='')
            {
                $resultado = $query->setFirstResult($start)->setMaxResults($limit)->getResult();            
            }
            else
            {
                $resultado = $query->getResult();
            }
        }
        else
        {
            $sql = "SELECT $selectedCont $from $wher ";
            $query->setDQL($sql);
            $totalResult = $query->getOneOrNullResult();      
            $resultado = ($totalResult ? ($totalResult["cont"] ? $totalResult["cont"] : 0) : 0);
        }       

        return $resultado;
    }

    public function getNumeroActividadXDetalle($idDetalle){
    
	  $sql = "select a
		  from schemaBundle:InfoComunicacion a 
		  where
		  a.detalleId = $idDetalle and a.remitenteNombre is not null";
	  
	  $query = $this->_em->createQuery($sql);  
	  
	  return $query->getResult();
        
    
    
    }



    /**
     * Funcion que sirve para obtener la información de la fibra de una tarea sea por su id_comunicacion o su detalle_id
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * 
     * @version 1.0 28-11-2020
     * 
     * @param array $arrayParametros
     * @return array resultado ( data )
     */
    public function getFibraPorTarea($arrayParametros)
    {
        try
        {
            $intIdComunicacion     = ( isset($arrayParametros['data']['intIdComunicacion']) && !empty($arrayParametros['data']['intIdComunicacion']) )
                                     ? $arrayParametros['data']['intIdComunicacion'] : null;
            $intIdDetalle          = ( isset($arrayParametros['data']['intIdDetalle']) && !empty($arrayParametros['data']['intIdDetalle']) )
                                     ? $arrayParametros['data']['intIdDetalle'] : null;
            $strUserComercial     = $arrayParametros['userSoporte'];
            $strPasswordComercial = $arrayParametros['pwdSoporte'];
            $strDatabaseDsn       = $arrayParametros['dsnSoporte'];      
                           
            
            $strError   = "";
            if( !empty($intIdComunicacion) || !empty($intIdDetalle))
            {

                $objOciConexion = oci_connect($strUserComercial, $strPasswordComercial, $strDatabaseDsn);
                $strRetorno = oci_new_descriptor($objOciConexion, OCI_D_LOB);
                $objFibra       = oci_new_cursor($objOciConexion);
                $strSQL = "BEGIN DB_SOPORTE.SPKG_INFO_TAREA.P_GET_FIBRA_TAREA( :Pn_IdComunicacion, ".
                                                                            ":Pn_IdDetalle, ".
                                                                            ":Pv_Retorno, ".
                                                                            ":Pv_Error ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);                            
                oci_bind_by_name($objStmt, ":Pn_IdComunicacion", $intIdComunicacion);                
                oci_bind_by_name($objStmt, ":Pn_IdDetalle",      $intIdDetalle);                
                oci_bind_by_name($objStmt, ":Pv_Retorno",        $strRetorno, -1, OCI_B_CLOB);                
                oci_bind_by_name($objStmt, ":Pv_Error",          $strError, 4000);                
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                error_log("output " . html_entity_decode($strRetorno->load()));
            }
            else
            {
                throw new \Exception( 'No se han enviado los parámetros adecuados para consultar la información' ); 
            }
        }
        catch(\Exception $e)
        {
            throw ($e);
        }        
        return html_entity_decode($strRetorno->load());

    }
    
    /**
     * getInfoComunicacionTarea
     *
     * Método que obtiene responsable de la tarea Instalación.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 27-09-2021
     *
     * @param array $arrayParametros
     * @return type
     */
    public function getInfoComunicacionTarea($arrayParametros)
    {
        $arrayInfoTarea   = array();
        $intIdPunto       = $arrayParametros['intIdPunto'];
        $strEmpresaCod    = $arrayParametros['empresaCod'];
        $strNombreTarea   = 'INSTALACION SECURE CPE';
        $strNombreProceso = 'TAREAS DE IPCCL2 - INSTALACION DE SERVICIO SECURE CPE';
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT TAR.PERSONA_EMPRESA_ROL_ID, TAR.TIPO_ASIGNADO, TAR.ASIGNADO_ID
                    FROM DB_SOPORTE.INFO_TAREA TAR, DB_SOPORTE.INFO_DETALLE DET
                         ,DB_COMUNICACION.INFO_COMUNICACION COM
                    WHERE TAR.NOMBRE_TAREA = :nombreTarea
                        AND TAR.NOMBRE_PROCESO = :nombreProceso
                        AND DET.ID_DETALLE = TAR.DETALLE_ID
                        AND COM.DETALLE_ID = DET.ID_DETALLE
                        AND COM.PUNTO_ID = :puntoId
                        AND COM.EMPRESA_COD = :empresaCod";

        $objRsm->addScalarResult(strtoupper('PERSONA_EMPRESA_ROL_ID'), 'idPersonaEmpresaRol', 'integer');
        $objRsm->addScalarResult(strtoupper('TIPO_ASIGNADO'), 'strTipoAsignado', 'string');
        $objRsm->addScalarResult(strtoupper('ASIGNADO_ID'), 'asignadoId', 'string');
        
        $objQuery->setParameter("nombreTarea", $strNombreTarea);
        $objQuery->setParameter("nombreProceso", $strNombreProceso);
        $objQuery->setParameter("puntoId", $intIdPunto);
        $objQuery->setParameter("empresaCod", $strEmpresaCod);
                   
        $objQuery->setSQL($strSql);

        $objTareas = $objQuery->getResult();
                       
        if($objTareas)
        {
            foreach($objTareas as $objTarea)
            {
                    $arrayInfoTarea[] = array(  'idPersonaEmpresaRol' => $objTarea['idPersonaEmpresaRol'],
                                                'strTipoAsignado'     => $objTarea['strTipoAsignado'],
                                                'asignadoId'          => $objTarea['asignadoId']);
            }
        }
        return $arrayInfoTarea;
    }

    /**
     * obtenerDatosTareaBitacora
     * Método para obtener los datos de la tarea seleccionada.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 - 30-11-2022.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 - 03-02-2023. Se mejora query de tareas
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 - 08-02-2023. Se obtiene canton del empleado
     * 
     * @param array $arrayParametro
     * @return array
     */
    public function obtenerDatosTareaBitacora($intIdTarea)
    {
        $objRSM = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRSM);

        $strSQL = "SELECT DISTINCT A.ID_COMUNICACION,D.REF_ASIGNADO_ID,D.REF_ASIGNADO_NOMBRE, k.NOMBRE_DEPARTAMENTO,
                        (SELECT NOMBRE_CANTON FROM DB_GENERAL.ADMI_CANTON WHERE ID_CANTON = (
                            SELECT CANTON_ID FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE ID_OFICINA = (                
                                SELECT oficina_id FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL 
                                WHERE id_persona_rol = D.PERSONA_EMPRESA_ROL_ID)))NOMBRE_CANTON,
                        h.ELEMENTO_ID,I.NOMBRE_ELEMENTO,E.LOGIN,L.DEPARTAMENTO_ID,A.DETALLE_ID,
                        J.NOMBRE_TIPO_ELEMENTO, B.FE_SOLICITADA
                    FROM DB_SOPORTE.INFO_COMUNICACION A, DB_SOPORTE.INFO_DETALLE B,
                            DB_SOPORTE.INFO_DETALLE_ASIGNACION D,DB_COMERCIAL.INFO_PERSONA E,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL L,DB_SOPORTE.INFO_DETALLE_TAREA_ELEMENTO H,
                            DB_INFRAESTRUCTURA.INFO_ELEMENTO I, DB_GENERAL.ADMI_DEPARTAMENTO K,
                             DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO O,DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO J
                    WHERE A.DETALLE_ID = B.ID_DETALLE AND D.id_detalle_asignacion = 
                    (SELECT max(g.id_detalle_asignacion) FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION g 
                    WHERE g.detalle_id = B.id_detalle) AND E.ID_PERSONA=D.REF_ASIGNADO_ID
                    AND D.DETALLE_ID = B.ID_DETALLE AND D.REF_ASIGNADO_ID = E.ID_PERSONA
                    AND I.ID_ELEMENTO = h.ELEMENTO_ID AND H.DETALLE_ID = d.DETALLE_ID 
                    AND k.ID_DEPARTAMENTO = L.DEPARTAMENTO_ID  AND L.PERSONA_ID = E.ID_PERSONA
                    AND L.ESTADO = 'Activo' AND k.ESTADO = 'Activo' AND L.ID_PERSONA_ROL = D.PERSONA_EMPRESA_ROL_ID
                    AND I.MODELO_ELEMENTO_ID = O.ID_MODELO_ELEMENTO AND O.TIPO_ELEMENTO_ID = J.ID_TIPO_ELEMENTO
                    AND H.DETALLE_ID = d.DETALLE_ID AND A.ID_COMUNICACION = :comunicacionId";


        $objQuery->setParameter('comunicacionId', $intIdTarea);

        $objRSM->addScalarResult('ID_COMUNICACION', 'id_comunicacion', 'integer');
        $objRSM->addScalarResult('DETALLE_ID', 'detalle_id', 'integer');
        $objRSM->addScalarResult('REF_ASIGNADO_ID', 'id_persona', 'integer');
        $objRSM->addScalarResult('REF_ASIGNADO_NOMBRE', 'nombres', 'string');
        $objRSM->addScalarResult('NOMBRE_CANTON', 'canton', 'string');
        $objRSM->addScalarResult('ELEMENTO_ID', 'id_elemento', 'integer');
        $objRSM->addScalarResult('NOMBRE_ELEMENTO', 'nombre_elemento', 'string');
        $objRSM->addScalarResult('LOGIN', 'login', 'string');
        $objRSM->addScalarResult('DEPARTAMENTO_ID', 'id_departamento', 'integer');
        $objRSM->addScalarResult('NOMBRE_DEPARTAMENTO', 'departamento', 'string');
        $objRSM->addScalarResult('NOMBRE_TIPO_ELEMENTO', 'nombre_tipo_elemento', 'string');
        $objRSM->addScalarResult('FE_SOLICITADA', 'fecha_solicitada', 'string');
        $objQuery->setSQL($strSQL);


        $objTareas = $objQuery->getResult();

        if($objTareas)
        {
            foreach($objTareas as $objTarea)
            {
                    $arrayInfoTarea['result'] = array(  'id_comunicacion' => $objTarea['id_comunicacion'],
                                                'detalle_id'      => $objTarea['detalle_id'],
                                                'id_persona'      => $objTarea['id_persona'],
                                                'nombres'         => $objTarea['nombres'],
                                                'canton'          => $objTarea['canton'],
                                                'id_elemento'     => $objTarea['id_elemento'],
                                                'nombre_elemento' => $objTarea['nombre_elemento'],
                                                'login'           => $objTarea['login'],
                                                'id_departamento' => $objTarea['id_departamento'],
                                                'departamento'    => $objTarea['departamento'],
                                                'nombre_tipo_elemento' => $objTarea['nombre_tipo_elemento'],
                                                'fecha_solicitada' => $objTarea['fecha_solicitada']);
            }
        }
        return $arrayInfoTarea;
    }

    /**
     * obtenerDatosTareaBitacora
     * Método para obtener los datos de la tarea seleccionada sin elemento.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 - 30-11-2022.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 - 06-02-2023. Se mejora query de tareas
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 - 08-02-2023. Se obtiene canton del empleado
     * 
     * @param array $arrayParametro
     * @return array
     */
    public function obtenerDatosTareaBitacoraSinElemento($intIdTarea)
    {
        $objRSM = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRSM);

        $strSQL = "SELECT DISTINCT A.ID_COMUNICACION,
                    D.REF_ASIGNADO_ID,D.REF_ASIGNADO_NOMBRE, k.NOMBRE_DEPARTAMENTO,
                        (SELECT NOMBRE_CANTON FROM DB_GENERAL.ADMI_CANTON WHERE ID_CANTON = (
                            SELECT CANTON_ID from DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE ID_OFICINA = (                
                                SELECT oficina_id from DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL 
                                WHERE id_persona_rol = D.PERSONA_EMPRESA_ROL_ID))) NOMBRE_CANTON,
                        '' ELEMENTO_ID,
                        '' NOMBRE_ELEMENTO,
                        J.LOGIN,L.DEPARTAMENTO_ID,A.DETALLE_ID,
                        '' NOMBRE_TIPO_ELEMENTO,
                        B.FE_SOLICITADA
                    FROM DB_SOPORTE.INFO_COMUNICACION A, 
                            DB_SOPORTE.INFO_DETALLE B,
                            DB_SOPORTE.INFO_DETALLE_ASIGNACION D,DB_COMERCIAL.INFO_PERSONA E,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL L ,
                            DB_COMERCIAL.INFO_PERSONA J,DB_GENERAL.ADMI_DEPARTAMENTO K
                    WHERE A.DETALLE_ID = B.ID_DETALLE
                    AND D.id_detalle_asignacion = (SELECT max(g.id_detalle_asignacion) 
                    FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION g WHERE g.detalle_id = B.id_detalle)
                    AND J.id_persona=D.REF_ASIGNADO_ID
                    AND D.DETALLE_ID = B.ID_DETALLE AND D.REF_ASIGNADO_ID = E.ID_PERSONA
                    AND k.ID_DEPARTAMENTO = L.DEPARTAMENTO_ID  
                    AND L.PERSONA_ID = E.ID_PERSONA AND L.ESTADO = 'Activo' 
                    AND k.ESTADO = 'Activo' AND L.ID_PERSONA_ROL = D.PERSONA_EMPRESA_ROL_ID 
                    AND A.ID_COMUNICACION = :comunicacionId";


        $objQuery->setParameter('comunicacionId', $intIdTarea);
        
        $objRSM->addScalarResult('ID_COMUNICACION', 'id_comunicacion', 'integer');
        $objRSM->addScalarResult('DETALLE_ID', 'detalle_id', 'integer');
        $objRSM->addScalarResult('REF_ASIGNADO_ID', 'id_persona', 'integer');
        $objRSM->addScalarResult('REF_ASIGNADO_NOMBRE', 'nombres', 'string');
        $objRSM->addScalarResult('NOMBRE_CANTON', 'canton', 'string');
        $objRSM->addScalarResult('ELEMENTO_ID', 'id_elemento', 'integer');
        $objRSM->addScalarResult('NOMBRE_ELEMENTO', 'nombre_elemento', 'string');
        $objRSM->addScalarResult('LOGIN', 'login', 'string');
        $objRSM->addScalarResult('DEPARTAMENTO_ID', 'id_departamento', 'integer');
        $objRSM->addScalarResult('NOMBRE_DEPARTAMENTO', 'departamento', 'string');
        $objRSM->addScalarResult('NOMBRE_TIPO_ELEMENTO', 'nombre_tipo_elemento', 'string');
        $objRSM->addScalarResult('FE_SOLICITADA', 'fecha_solicitada', 'string');
        $objQuery->setSQL($strSQL);

        $objTareas = $objQuery->getResult();

        if($objTareas)
        {
            foreach($objTareas as $objTarea)
            {
                    $arrayInfoTarea['result'] = array(  'id_comunicacion' => $objTarea['id_comunicacion'],
                                                'detalle_id'      => $objTarea['detalle_id'],
                                                'id_persona'      => $objTarea['id_persona'],
                                                'nombres'         => $objTarea['nombres'],
                                                'canton'          => $objTarea['canton'],
                                                'id_elemento'     => $objTarea['id_elemento'],
                                                'nombre_elemento' => $objTarea['nombre_elemento'],
                                                'login'           => $objTarea['login'],
                                                'id_departamento' => $objTarea['id_departamento'],
                                                'departamento'    => $objTarea['departamento'],
                                                'nombre_tipo_elemento' => $objTarea['nombre_tipo_elemento'],
                                                'fecha_solicitada' => $objTarea['fecha_solicitada']);
            }
        }
        return $arrayInfoTarea;
    }

    /**
     * queryByParams
     * Método para obtener el query de los tareas filtradas según los parámetros enviados.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 - 30-11-2022.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 - 06-02-2023 - Se optima el query que obtiene las tareas
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 - 27-03-2023 - Se optimiza el query que obtiene las tareas
     * 
     * @param array $arrayParametro
     * @return array
     */
    public function queryByParams($arrayParams)
    {
        $objRSM = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRSM);

        $strSQL = "SELECT DISTINCT (SELECT min(ic.id_comunicacion) FROM DB_SOPORTE.INFO_COMUNICACION ic
                    WHERE ic.detalle_id = id.id_detalle)  ID_COMUNICACION
                    FROM DB_SOPORTE.INFO_DETALLE id,DB_SOPORTE.INFO_DETALLE_HISTORIAL idh
                    WHERE  id.id_detalle = idh.detalle_id
                    AND idh.ESTADO NOT IN ('Finalizada', 'Cancelada', 'Rechazada', 'Anulada')
                    AND  idh.id_detalle_historial = (select max(id_detalle_historial)
                    FROM db_soporte.info_detalle_historial WHERE detalle_id = id.id_detalle)
                    AND  id.id_detalle = (SELECT detalle_id FROM DB_SOPORTE.INFO_COMUNICACION ic
                    WHERE ic.id_comunicacion = :comunicacionId)";

        $objQuery->setParameter('comunicacionId', $arrayParams['comunicacionId']);

        $objRSM->addScalarResult('ID_COMUNICACION', 'id', 'integer');
        $objQuery->setSQL($strSQL);

        return $objQuery;
    }


    /**
     * findByParams
     *
     * Método encargado de obtener todas las tareas.
     * 
     * @param $arrayParams => filter params
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 30/11/2022
     * 
     */
    public function findByParams($arrayParams)
    {
        $objQuery = array();

        if (!empty($arrayParams))
        {
            $objQuery = $this->queryByParams($arrayParams)->getResult();
        }

        return $objQuery;
        
    }
}
