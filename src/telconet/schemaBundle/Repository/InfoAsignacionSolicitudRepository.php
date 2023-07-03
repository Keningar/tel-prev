<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
//use \DateTime;

class InfoAsignacionSolicitudRepository extends EntityRepository
{
    /**
     * Actualización: Se realiza validaciones para consultar información adicional de las tareas solo para usuarios que tengan el
     *                perfil verNuevosCamposTareas
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.10 24-01-2022
     * 
     * Actualización: Se añade información para funcionalidad de finalizar tarea
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.9 19-11-2021
     * 
     * Actualización: Se agrega programacion para consultas de parámetros para gestion de tareas.
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.8 14-09-2021
     * 
     * Actualización: Se agrega programación para consultar detalles de asignaciones por tabvisible y por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 28-05-2020
     * 
     * Actualización: Se agrega que retorne el campo id_asignado donde se muestra el id del departamento asignado a la tarea.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.7 23-03-2020
     *  
     * Actualización: Se agrega variables $asignacionConsultaHijas, $intPadreId para recibir el id de la asignación que sera padre
     *                y el listado de las que seran hijas.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.6 02-07-2019
     * 
     * Actualización: Se agrega programación para obtener el campo infoTareas el cual contiene las tareas de un caso.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 21-02-2019
     * 
     * Actualización: Se agrega que retorne el campo asignado en donde se muestra el nombre del departamento asignado a la tarea.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 22-01-2019
     *
     * Actualización: Se agrega que se pueda filtre asignaciones por id_canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 15-01-2019
     *
     * Actualización: Se obtiene afectado y asignado directamente desde la función getDetalleAsignacionesPorDefecto y getDetalleAsignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 28-11-2018
     *
     * Actualización: Se obtiene el login afectado desde una función aparte
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Construye el Json de los detalles de asignaciones.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     codEmpresa              => id de la empresa
     *     intIdDepartamento       => id del departamento
     *     intIdCanton             => id del canton
     *     usrAsignado             => Login del usuario asignado
     *     strTabVisible           => tab visible de la asignación
     *     strEstado               => estado de la asignación
     *     esOrderByUsrAsignacion  => Bandera que indica si la consulta sera ordenada por usrAsignacion (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByFeCreacionDesc => Bandera que indica si la consulta sera ordenada por feCreacion en forma descendente (S o N)
     *     esOrderByTipoAtencion   => Bandera que indica si la consulta sera ordenada por tipo de atencion (S o N)
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function generarJsonDetalleAsignaciones($arrayParametros){
        
        $objJsonResponse          = new JsonResponse();
        $strUrlAfectado           = "";
        //
        $codEmpresa               = $arrayParametros["codEmpresa"];
        $strConsultaPorDefecto    = $arrayParametros["consultaPorDefecto"];
        $strDepartamento          = $arrayParametros["strDepartamento"];
        $intIdDepartamento        = $arrayParametros["intIdDepartamento"];
        $intIdCanton              = $arrayParametros["intIdCanton"];
        $container                = $arrayParametros["container"];
        $asignacionProactiva      = $arrayParametros["asignacionProactiva"];
        $asignacionConsultaHijas  = $arrayParametros['asignacionConsultaHijas'];
        $intPadreId               = $arrayParametros['intPadreId'];
        $strTabVisible            = $arrayParametros['strTabVisible'];
        $strEstado                = $arrayParametros['strEstado'];

        $objEmSoporte            = $arrayParametros['objEmSoporte'];
        $objEmComunicacion       = $arrayParametros['objEmComunicacion'];
        $emComercial             = $arrayParametros['emComercial'];
        $objEmGeneral            = $arrayParametros['objEmGeneral'];
        $booleanRegistroActivos  = $arrayParametros['permiteRegistroActivos'];
        $intPersonaEmpresaRol    = $arrayParametros['idPersonaEmpresaRol'];
        $strUsuarioSession       = $arrayParametros["strUsrSession"];

        $booleanPermiteVerNuevosCamposTareas = $arrayParametros['permiteVerNuevosCamposTareas'];

        // Verificar si es interdepartamental
        $strIdsTareasNoReqActivos   = "";
        $arrayIdTareasNoReqActivo 	= $objEmSoporte->getRepository('schemaBundle:AdmiParametroDet') 
                                    ->getOne('IDS_TAREAS_NO_REG_ACTIVOS','','','','','','','');

        if (is_array($arrayIdTareasNoReqActivo))
        {
            $strIdsTareasNoReqActivos = !empty($arrayIdTareasNoReqActivo['valor1']) ? $arrayIdTareasNoReqActivo['valor1'] : "";
        }

        if (!empty($strConsultaPorDefecto) && $strConsultaPorDefecto==="S")
        {
            $parametrosDetallesPorDefecto                            = array();
            $parametrosDetallesPorDefecto['codEmpresa']              = $codEmpresa;
            $parametrosDetallesPorDefecto['intIdDepartamento']       = $intIdDepartamento;
            $parametrosDetallesPorDefecto['intIdCanton']             = $intIdCanton;
            $parametrosDetallesPorDefecto['asignacionProactiva']     = $asignacionProactiva;
            $parametrosDetallesPorDefecto['intPadreId']              = $intPadreId;
            $parametrosDetallesPorDefecto['asignacionConsultaHijas'] = $asignacionConsultaHijas;
            $parametrosDetallesPorDefecto['strTabVisible']           = $strTabVisible;
            $parametrosDetallesPorDefecto['strEstado']               = $strEstado;
            $parametrosDetallesPorDefecto['permiteVerNuevosCamposTareas'] = $booleanPermiteVerNuevosCamposTareas;
            $registros                                               = $this->getDetalleAsignacionesPorDefecto($parametrosDetallesPorDefecto);
        }
        else
        {
            $resultado = array();
            $registros = $this->getDetalleAsignaciones($arrayParametros);
        }

        //
        if(!empty($registros))
        {
            $intSecuencial  = 1;
            foreach($registros as $data)
            {
                $strRowClass = "";
                $strUrlAfectado ="";
                //Verificamos si el caso cerrado y esta en el departamento en sesion, de ser asi se lo pone como Abierto
                $strEstadoCaso = $data["estadoCaso"];
                //
                if ($data["tipoAtencion"] === 'CASO' && $data["estadoCaso"]!=='Cerrado' && $data["estadoCaso"]!== null)
                {
                    $strDepartamentoAsignado = (isset($data['asignado']) ? $data['asignado']  : "");
                    $strEstadoCaso = (strtoupper($strDepartamentoAsignado)=== strtoupper($strDepartamento) ? "Abierto" : "Escalado");
                }
                //Se crea el link para ir al show del caso o la tarea
                $strUrlVer="";
                if($data["tipoAtencion"] === 'CASO' && $data["estado"] === 'EnGestion' && isset($data["referenciaId"]))
                {
                    $strUrlVer = $container->get('router')->generate('infocaso_show', array('id' =>$data["referenciaId"]));
                }
                elseif ($data["tipoAtencion"] === 'TAREA' && $data["estado"] === 'EnGestion' && isset($data["numero"]))
                {
                    $strUrlVer = $container->get('router')->generate('tareas')."?numTarea=".$data["numero"];
                }
                if (isset($data["id_afectado"]))
                {
                    
                    $strUrlAfectado = $container->get('router')->generate('infopunto_show', array('id'  => $data["id_afectado"],
                                                                                                  'rol' => "Cliente"));
                }
                if ($data["asignado"] === $strDepartamento &&  $data["estadoTarea"] === "Asignada" )
                {
                    
                    $strRowClass = "detalleAsignacionRowRed";
                }
                if ( $data["estadoTarea"] === "Reprogramada" )
                {
                    $objFechaNow        = new \DateTime();
                    $objFechaSolicitada = null;

                    if ($data["tipoAtencion"] === 'CASO' )
                    {
                        $arrayInfoTarea  = explode(',',$data["infoTareas"]);

                        if(count($arrayInfoTarea)>0)
                        {
                            $arrayDetalle = explode(':',$arrayInfoTarea[0]);
                            if(count($arrayDetalle)>0)
                            {
                                $intIdDetalle       = intval(str_replace('"','',$arrayDetalle[1]));
                                $objInfoDetalle     = $this->_em->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalle);
                                $objFechaSolicitada = (is_object($objInfoDetalle))?$objInfoDetalle->getFeSolicitada():null;
                            }
                        }
                    }
                    elseif ($data["tipoAtencion"] === 'TAREA' )
                    {
                        $objInfoComunicacion = $this->_em->getRepository('schemaBundle:InfoComunicacion')->find($data["referenciaId"]);
                        if (is_object($objInfoComunicacion))
                        {
                            $objInfoDetalle     = $this->_em->getRepository('schemaBundle:InfoDetalle')->find($objInfoComunicacion->getDetalleId());
                            $objFechaSolicitada = (is_object($objInfoDetalle))?$objInfoDetalle->getFeSolicitada():null;
                        }
                    }
                    if(is_object($objFechaSolicitada))
                    {
                        $objInterval = $objFechaNow->diff($objFechaSolicitada);

                        if ( ( ($objInterval->format('%R')==='+') && 
                        (intval($objInterval->format('%a')) === 0) &&
                        (intval($objInterval->format('%h')) === 0) &&
                        (intval($objInterval->format('%i')) <= 30) ) ||
                        ($objInterval->format('%R') === '-') )
                        {
                            $strRowClass = "detalleAsignacionRowBlue";
                        }
                    }
                }

                $intIdDetalleHist = "";
                $strNombreUsrAsignado = "";
                $strEstadoHist = "";
                $intIdDetalle = "";
                $strNombreTarea = '';
                $intMinutos = "";
                $strMinutos = "";
                $objFechaEjecucion = "";
                $strHoraEjecucion = "";
                $intCasoId =0;
                $booleanEsInterdep = true;
                $intIdTarea='';
                $strPersonaEmpresaRol = array();
                $intAsignadoId='';
                $strTipoAsignado = '';
                $strIniciadaDesdeMobil = "S";
                $strPerteneceACaso = false;
                $strCasoPerteneceTn = false;
                $strNombreProceso = "";
                $strBanderaFinalizarInformeEjecutivo = "S";
                $strCerrarTarea = "S";
                $strDepAsignado = "";
                $strObservacionTarea = "";
                $booleanMostrarInfoAdicional = false;

                //Validación para extraer información de tarae solo para usuarios que tengan el perfil verNuevosCamposTareas
                if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas
                    && $data["numero"] !== null && $data["tipoAtencion"] === 'TAREA' && isset($data['idDetalle'])
                    && $data['idDetalle'] !== null)
                {
                    $strFecha = $data["feCreacion"];
                    $strEstado = $data["estadoTarea"];
                    $strDepAsignado = $data["asignado"];

                    $strFeCreacionTareaAceptada = isset($data['fecha_tarea_creacion'])?$data['fecha_tarea_creacion']:'';
                    $intValorTiempoPausa        = $data['valor_tiempo_pausa'];
                    $strFeCreacionReanuda       = $data['fecha_creacion_reanuda'];
                    $intCasoEmpresaCod          = $data['caso_empresa_cod'];
                    $strTareaInfoAdicional      = isset($data['tarea_info_adicional'])?$data['tarea_info_adicional']:'';
                    $strObservacionTarea        = isset($data['observacion'])?$data['observacion']:'';
                    $intNumeroTareaPadre        = isset($data['numero_tarea_padre'])?$data['numero_tarea_padre']:'';
                    $strPermiteFinalizar        = $data['permite_finalizar_informe'];
                    $intIdDetalle               = (int)$data['idDetalle'];
                    $intIdDetalleHist           = isset($data['idDetalleHist'])?(int)$data['idDetalleHist']:'';
                    $strEstadoHist              = isset($data['estadoHist'])?$data['estadoHist']:'';
                    $strFechaCreaHist           = isset($data['fechaCreaHist'])?$data['fechaCreaHist']:'';
                    $strNombreUsrAsignado       = isset($data['ref_asignado_nombre'])?$data['ref_asignado_nombre']:'';
                    $strNombreTarea             = isset($data['nombre_tarea'])?$data['nombre_tarea']:'';
                    $intIdTarea                 = isset($data['idtarea'])?(int)$data['idtarea']:'';
                    $strNombreProceso           = isset($data['nombreProceso'])?$data['nombreProceso']:'';
                    $intCasoId                  = isset($data['idCaso'])?(int)$data['idCaso']:0;
                    $strAfectado                = isset($data['afectado'])?$data['afectado']:'';
                    $intIdTareaAnterior         = isset($data['id_tarea_anterior'])?(int)$data['id_tarea_anterior']:'';
                    $strNombreTareaAnterior     = isset($data['nombre_tarea_anterior'])?$data['nombre_tarea_anterior']:'';

                    // Validar si la persona en session puede finalizar la tarea de generacion de informe ejecutivo
                    $strBanderaFinalizarInformeEjecutivo = ($strPermiteFinalizar === 'N')?'N':'S';
                    $strCerrarTarea                      = $intNumeroTareaPadre==='N'?'N':'S';
                    // obtener valor para la consulta de archivo
                    $booleanMostrarInfoAdicional = ($strTareaInfoAdicional !== '')?true:false; 

                    $strPerteneceACaso = ($intCasoId !== 0)?true:false;

                    $strCasoPerteneceTn = ($intCasoEmpresaCod == "10")?true:false;

                    $strFechaCreacionTarea  = "";
                    if($strEstado == 'Asignada')
                    {
                        $strFechaCreacionTarea = new \DateTime($strFecha);
                    }
                    else
                    { 
                        if($strFeCreacionTareaAceptada != "")
                        {
                            $strFechaCreacionTarea      = new \DateTime($strFeCreacionTareaAceptada);
                        }
                    }

                    if( ($strEstado == 'Cancelada' || $strEstado == 'Finalizada' || $strEstado == 'Rechazada' || $strEstado == 'Anulada') 
                            && isset($strFechaCreaHist) && $strFechaCreaHist !=='')
                    {
                        $objDatetimeFinal = new \DateTime($strFechaCreaHist);
                    }
                    else
                    {
                        $objDatetimeFinal = new \DateTime();
                    }

                    if(is_object($strFechaCreacionTarea))
                    {
                        $objDatetimeDiferenciaFechas = $objDatetimeFinal->diff($strFechaCreacionTarea);
                        $intMinutos  = $objDatetimeDiferenciaFechas->days * 24 * 60;
                        $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                        $intMinutos += $objDatetimeDiferenciaFechas->i;
                    }
                    $strMinutos  = $intMinutos.' minutos';

                    if($strEstado == "Pausada")
                    {
                        if(isset($intValorTiempoPausa) && $intValorTiempoPausa !== '')
                        {
                            $strMinutos = $intValorTiempoPausa . ' minutos';
                        }
                    }
                    else if($strEstado <> 'Cancelada' && $strEstado <> 'Finalizada' && $strEstado <> 'Rechazada'
                            && isset($strFeCreacionReanuda) && $strFeCreacionReanuda !== '')
                    {
                            $objDateFechaReanudada       = new \DateTime($strFeCreacionReanuda);
                            $objDateFechaActual          = new \DateTime();
                            $objDatetimeDiferenciaFechas = $objDateFechaActual->diff($objDateFechaReanudada);

                            $intMinutos = $objDatetimeDiferenciaFechas->days * 24 * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->i;

                            if(isset($intValorTiempoPausa) && $intValorTiempoPausa !== '')
                            {
                                $intTiempoTareaPausada = $intValorTiempoPausa;
                            }

                            $strMinutos = $intMinutos + $intTiempoTareaPausada;
                            $strMinutos = $strMinutos.' minutos';
                    }

                    $intMinutos = substr($strMinutos,0,-8);

                    $objInfoAsignaciones = $objEmSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                        ->getUltimaAsignacion($intIdDetalle);
                    if($objInfoAsignaciones)
                    {
                        $intAsignadoId = $objInfoAsignaciones->getAsignadoId();
                        $strDepAsignado = $objInfoAsignaciones->getAsignadoNombre();
                        $intRefAsignadoid = $objInfoAsignaciones->getRefAsignadoId();
                        $strTipoAsignado = $objInfoAsignaciones->getTipoAsignado();

                        //SE DETERMINA EN ESTE CASO EL TIEMPO DE INICIO DE LA TAREA

                        $objFechaAsignacion = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')
                                                        ->getUltimaFechaAsignacion($intIdDetalle,$intRefAsignadoid,$intAsignadoId);

                        if($objFechaAsignacion[0]['fecha'] != "")
                        {
                            $objFechaAsignacion = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')
                                                ->getUltimaFechaAsignacion($intIdDetalle,$intRefAsignadoid,$intAsignadoId);
                            if($objFechaAsignacion[0]['fecha'] != "")
                            {
                                $objFechaEjecucion = $objFechaAsignacion[0]['fecha'];
                            }
                        }
                        if($objFechaEjecucion != "")
                        {
                            $arrayFecha        = explode(" ", $objFechaEjecucion);
                            $arrayFech         = explode("-", $arrayFecha[0]);
                            $arrayHora         = explode(":", $arrayFecha[1]);
                            $objFechaEjecucion = $arrayFech[2] . "-" . $arrayFech[1] . "-" . $arrayFech[0];
                            $strHoraEjecucion  = $arrayHora[0] . ":" . $arrayHora[1];
                        }
                        // consulta del id departamento
                        $strPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->getIdDepartCoordinador($intRefAsignadoid); 

                    }                    

                    $arrayIdsTareasNoReqActivo = explode (",", $strIdsTareasNoReqActivos);  

                    if(in_array($intIdTarea,$arrayIdsTareasNoReqActivo) || $intCasoId != 0)
                    {
                        $booleanEsInterdep = false;
                    }
                }

                $arr_encontrados[]    = array(
                                                 'id'                => $data["id"],
                                                 'numero'            => $intSecuencial,
                                                 'referenciaCliente' => $data["referenciaCliente"],
                                                 'tipoAtencion'      => $data["tipoAtencion"],
                                                 'tipoProblema'      => $data["tipoProblema"],
                                                 'usrAsignado'       => $data["usrAsignado"],
                                                 'detalle'           => $data["detalle"],
                                                 'feCreacion'        => $data["feCreacion"],
                                                 'criticidad'        => $data["criticidad"],
                                                 'casoTarea'         => $data["numero"] == null ? "" : $data["numero"],
                                                 'urlVerCasoTarea'   => $strUrlVer,
                                                 'estadoCaso'        => $strEstadoCaso,
                                                 'estadoTarea'       => $data["estadoTarea"],
                                                 'infoTareas'        => $data["infoTareas"],
                                                 'estado'            => $data["estado"],
                                                 'cambioTurno'       => $data["cambioTurno"],
                                                 'referenciaId'      => $data["referenciaId"],
                                                 'urlLoginAfectado'  => $strUrlAfectado,
                                                 'loginAfectado'     => $data["afectado"] == null ? "" : $data["afectado"],
                                                 'asignado'          => $data["asignado"],
                                                 'origen'            => $data["origen"],
                                                 'padre'             => $data["padre"],
                                                 'ciudad'            => $data["ciudad"],
                                                 'tabVisible'        => $data["tabVisible"],
                                                 'colorRegistro'     => $strRowClass,
                                                 'nombreTarea'          => $strNombreTarea,
                                                 'idDetalle'            => $intIdDetalle,
                                                 'idPersonaEmpresaRol'  => $intPersonaEmpresaRol,
                                                 'idDetalleHist'        => $intIdDetalleHist,
                                                 'nombreUsrAsignado'    => $strNombreUsrAsignado,
                                                 'EstadoHist'           => $strEstadoHist,
                                                 'acciones'          => "",
                                                 "observacionTarea" =>  str_replace('*fff','"',$strObservacionTarea),
                                                 "strBanderaFinalizarInformeEjecutivo" => $strBanderaFinalizarInformeEjecutivo,
                                                 "fechaEjecucion"         => $objFechaEjecucion,
                                                 "horaEjecucion"          => $strHoraEjecucion,
                                                 "duracionMinutos"        => $intMinutos,
                                                 "permiteRegistroActivos" => $booleanRegistroActivos,
                                                 "id_caso"                => $intCasoId,
                                                 "esInterdepartamental"   => $booleanEsInterdep,
                                                 "departamentoId"     => (count($strPersonaEmpresaRol)>0 && $strPersonaEmpresaRol['idDepartamento'])?
                                                                         $strPersonaEmpresaRol['idDepartamento']:'',
                                                 "asignado_id"            => $intAsignadoId,
                                                 "tipoAsignado"           => $strTipoAsignado,
                                                 "cerrarTarea"            => $strCerrarTarea,
                                                 "iniciadaDesdeMobil"     => $strIniciadaDesdeMobil,
                                                 "perteneceCaso"          => $strPerteneceACaso,
                                                 "casoPerteneceTN"        => $strCasoPerteneceTn,
                                                 "nombreTareaAnterior"   => ($strNombreTareaAnterior != '')?$strNombreTareaAnterior:
                                                                            ($strNombreTarea? $strNombreTarea:"N/A"),
                                                 "idTareaAnterior"        => ($intIdTareaAnterior != '')?$intIdTareaAnterior:$intIdTarea,
                                                 "asignado_nombre"        => $strDepAsignado,
                                                 "ref_asignado_nombre"    => $strNombreUsrAsignado,
                                                 "nombre_proceso"         => $strNombreProceso,
                                                 "clientes"               => isset($strAfectado)?$strAfectado:'',
                                                 "duracionTarea"          => $strMinutos,
                                                 "id_tarea"               => $intIdTarea,
                                                 "intIdDetalleHist"       => $intIdDetalleHist,
                                                 "numeroTarea"           => $data["numero"] == null ? "" : $data["numero"],
                                                 'strTareaIncAudMant'  => $booleanMostrarInfoAdicional ? 'S' : 'N',
                                                 'strUsuarioSession'   => $strUsuarioSession
                                             );
                $intSecuencial ++;
            }
            $objJsonResponse->setData(['data'=>$arr_encontrados]);
            return $objJsonResponse;

        }
        else
        {
            $objJsonResponse->setData(['data'=>array()]);
            return $objJsonResponse;
        }
    }


    /**
     * Actualizacón: Se quita conversión de texto a minusculas de los detalles del seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 14-02-2019
     * 
     * Construye el Json de los detalles de seguimientos.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     codEmpresa              => id de la empresa
     *     usrAsignado             => Login del usuario asignado
     *     esOrderByUsrAsignacion  => Bandera que indica si la consulta sera ordenada por usrAsignacion (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByFeCreacionDesc => Bandera que indica si la consulta sera ordenada por feCreacion en forma descendente (S o N)
     *     esOrderByTipoAtencion   => Bandera que indica si la consulta sera ordenada por tipo de atencion (S o N)
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function generarJsonDetalleSeguimientos($arrayParametros){
        $objJsonResponse          = new JsonResponse();
        $start                    = $arrayParametros["start"];
        $limit                    = $arrayParametros["limit"];
        $arrayParametros["start"] = "";
        $arrayParametros["limit"] = "";
        $intRegistrosTotal        = 0;
        $arrayParametros["start"] = $start;
        $arrayParametros["limit"] = $limit;
        $cursorSeguimientos = $this->getSeguimientos($arrayParametros);
        if( !empty($cursorSeguimientos) )
        {
            $arrResultado   = array();
            $i                = 0;
            while( ($arrayResultadoCursor = oci_fetch_array($cursorSeguimientos, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
            {
                $arrResultado[$i]['numero']            = $i+1;
                $arrResultado[$i]['idSeguimientoAsig'] = ( isset($arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION'])
                                                              && !empty($arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION']) )
                                                              ? $arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION'] : 0;
                $arrResultado[$i]['feCreacion']        = ( isset($arrayResultadoCursor['FE_CREACION'])
                                                              && !empty($arrayResultadoCursor['FE_CREACION']) )
                                                              ? $arrayResultadoCursor['FE_CREACION'] : '';
                $arrResultado[$i]['usrCreacion']       = ( isset($arrayResultadoCursor['USR_CREACION'])
                                                              && !empty($arrayResultadoCursor['USR_CREACION']) )
                                                              ? strtolower($arrayResultadoCursor['USR_CREACION']) : '';
                $arrResultado[$i]['detalle']           = ( isset($arrayResultadoCursor['DETALLE'])
                                                              && !empty($arrayResultadoCursor['DETALLE']) )
                                                              ? $arrayResultadoCursor['DETALLE'] : '';
                $arrResultado[$i]['procedencia']       = ( isset($arrayResultadoCursor['TIPO'])
                                                              && !empty($arrayResultadoCursor['TIPO']) )
                                                              ? strtolower($arrayResultadoCursor['TIPO']) : '';
                $i++;
            }
            $intRegistrosTotal = $i;
            $objJsonResponse->setData(['data'=>$arrResultado]);
            return $objJsonResponse;
        }
        else
        {
            $objJsonResponse->setData(['data'=>array()]);
            return $objJsonResponse;
        }
    }

    /**
     * Construye el Json de los detalles de seguimientos pendientes.
     * @param $arrayParametros
     * [
     *     strUserDbSoporte        => usuario de bd soporte
     *     strPasswordDbSoporte    => password de bd soporte
     *     strDatabaseDsn          => dsn de base de datos
     *     strUsrGestion           => usuario al que pertenecen los seguimientos pendientes
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function generarJsonDetalleSeguimientosPendUsr($arrayParametros){
        $objJsonResponse    = new JsonResponse();
        $cursorSeguimientos = $this->getSeguimientosPendientesPorUsr($arrayParametros);
        if( !empty($cursorSeguimientos) )
        {
            $arrResultado   = array();
            $i                = 0;
            while( ($arrayResultadoCursor = oci_fetch_array($cursorSeguimientos, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
            {
                $arrResultado[$i]['numero']            = $i+1;
                $arrResultado[$i]['idSeguimientoAsig'] = ( isset($arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION'])
                                                              && !empty($arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION']) )
                                                              ? $arrayResultadoCursor['ID_SEGUIMIENTO_ASIGNACION'] : 0;
                $arrResultado[$i]['feCreacion']        = ( isset($arrayResultadoCursor['FE_CREACION'])
                                                              && !empty($arrayResultadoCursor['FE_CREACION']) )
                                                              ? $arrayResultadoCursor['FE_CREACION'] : '';
                $arrResultado[$i]['usrCreacion']       = ( isset($arrayResultadoCursor['USR_CREACION'])
                                                              && !empty($arrayResultadoCursor['USR_CREACION']) )
                                                              ? strtolower($arrayResultadoCursor['USR_CREACION']) : '';
                $arrResultado[$i]['detalle']           = ( isset($arrayResultadoCursor['DETALLE'])
                                                              && !empty($arrayResultadoCursor['DETALLE']) )
                                                              ? strtolower($arrayResultadoCursor['DETALLE']) : '';
                $arrResultado[$i]['procedencia']       = ( isset($arrayResultadoCursor['TIPO'])
                                                              && !empty($arrayResultadoCursor['TIPO']) )
                                                              ? strtolower($arrayResultadoCursor['TIPO']) : '';
                $arrResultado[$i]['tipoAtencion']      = ( isset($arrayResultadoCursor['TIPO_ATENCION'])
                                                              && !empty($arrayResultadoCursor['TIPO_ATENCION']) )
                                                              ? strtolower($arrayResultadoCursor['TIPO_ATENCION']) : '';
                $arrResultado[$i]['referenciaCliente'] = ( isset($arrayResultadoCursor['REFERENCIA_CLIENTE'])
                                                              && !empty($arrayResultadoCursor['REFERENCIA_CLIENTE']) )
                                                              ? strtolower($arrayResultadoCursor['REFERENCIA_CLIENTE']) : '';
                $arrResultado[$i]['numero']            = ( isset($arrayResultadoCursor['NUMERO'])
                                                              && !empty($arrayResultadoCursor['NUMERO']) )
                                                              ? strtolower($arrayResultadoCursor['NUMERO']) : '';
                $i++;
            }
            $intRegistrosTotal = $i;
            $objJsonResponse->setData(['data'=>$arrResultado]);
            return $objJsonResponse;
        }
        else
        {
            $objJsonResponse->setData(['data'=>array()]);
            return $objJsonResponse;
        }
    }

    /**
     * Construye el Json de los detalles de asignaciones.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     codEmpresa              => id de la empresa
     *     usrAsignado             => Login del usuario asignado
     *     esOrderByUsrAsignacion  => Bandera que indica si la consulta sera ordenada por usrAsignacion (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByFeCreacionDesc => Bandera que indica si la consulta sera ordenada por feCreacion en forma descendente (S o N)
     *     esOrderByTipoAtencion   => Bandera que indica si la consulta sera ordenada por tipo de atencion (S o N)
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function generarJsonDetalleHistorialAsignacion($arrParametros){
        $objJsonResponse = new JsonResponse();
        $resultado       = array();
        $registros       = $this->getDetalleAHistorialAsignacion($arrParametros);

        //
        if(!empty($registros))
        {
            $intSecuencial  = 1;
            foreach($registros as $data)
            {
                $arr_encontrados[] = array(
                                              'id'                => $data["id"],
                                              'numero'            => $intSecuencial,
                                              'usrAsignado'       => $data["usrAsignado"],
                                              'feCreacion'        => $data["feCreacion"],
                                              'tipo'              => $data["tipo"]
                                          );
                $intSecuencial ++;
            }
            $objJsonResponse->setData(['data'=>$arr_encontrados]);
            return $objJsonResponse;
        }
        else
        {
            $objJsonResponse->setData(['data'=>$resultado]);
            return $objJsonResponse;
        }
    }

    /**
     * Actualización: Se agrga validación para no considerar asignaciones con dependencia.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.3 26-06-2019
     * 
     * Actualización: Se agrega en el query que consulte el afectado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 04-12-2018
     *
     * Actualización: Se agrega en el query que consulte si es primera asignación o reasignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Realiza la consulta de asignaciones segun los criterios recibidos por parametros.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     codEmpresa              => id de la empresa
     *     usrAsignado             => Login del usuario asignado
     *     esGroupBy               => Bandera que indica si la consulta sera agrupada (S o N)
     *     esGroupByUsrAsignacion  => Bandera que indica si la consulta sera agrupada por usrAsignacion (S o N)
     *     esGroupByEstado         => Bandera que indica si la consulta sera agrupada por estado (S o N)
     *     esGroupByTipoAtencion   => Bandera que indica si la consulta sera agrupada por tipo de atencion (S o N)
     *     esGroupByOrigen         => Bandera que indica si la consulta sera agrupada por origen (S o N)
     *     esOrderByUsrAsignacion  => Bandera que indica si la consulta sera ordenada por usrAsignacion (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByFeCreacionDesc => Bandera que indica si la consulta sera ordenada por feCreacion en forma descendente (S o N)
     *     esOrderByTipoAtencion   => Bandera que indica si la consulta sera ordenada por tipo de atencion (S o N)
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getRegistrosAsignaciones($arrayParametros){
        $intStart                   = $arrayParametros["start"];
        $intLimit                   = $arrayParametros["limit"];
        $strCodEmpresa              = $arrayParametros["codEmpresa"];
        $strUsrAsignado             = $arrayParametros["usrAsignado"];
        $objFeCreacion              = $arrayParametros["feCreacion"];
        $strEsGroupBy               = $arrayParametros["esGroupBy"];
        $strEsGroupByUsrAsignacion  = $arrayParametros["esGroupByUsrAsignacion"];
        $strEsGroupByEstado         = $arrayParametros["esGroupByEstado"];
        $strEsGroupByTipoAtencion   = $arrayParametros["esGroupByTipoAtencion"];
        $strEsGroupByOrigen         = $arrayParametros["esGroupByOrigen"];
        $strEsOrderByUsrAsignacion  = $arrayParametros["esOrderByUsrAsignacion"];
        $strEsOrderByTipoAtencion   = $arrayParametros["esOrderByTipoAtencion"];
        $strEsOrderByEstado         = $arrayParametros["esOrderByEstado"];
        $strEsOrderByFeCreacionDesc = $arrayParametros["esOrderByFeCreacionDesc"];
        $strEsOrderByTodosEstados   = $arrayParametros["esOrderByTodosEstados"];
        $rsm                        = new ResultSetMappingBuilder($this->_em);
        $query                      = $this->_em->createNativeQuery(null,$rsm);
        $where                      = "";
        $select                     = "SELECT asigh.USR_ASIGNADO, asigh.TIPO, ".
                                             "CASE WHEN asigh.TIPO = 'ASIGNACION' AND ".
                                             "(SELECT COUNT(*) FROM INFO_ASIGNACION_SOLICITUD_HIST asigh1 ".
                                             "    WHERE asigh1.asignacion_solicitud_id = asig.ID_ASIGNACION_SOLICITUD) > 1 THEN".
                                             "'PIN1'".
                                             "WHEN asigh.TIPO = 'REASIGNACION' AND ".
                                             "(SELECT MIN(asigh1.ID_ASIGNACION_SOLICITUD_HIST) FROM INFO_ASIGNACION_SOLICITUD_HIST asigh1 ".
                                             "    WHERE asigh1.asignacion_solicitud_id = asig.ID_ASIGNACION_SOLICITUD ".
                                             "AND asigh1.TIPO = 'REASIGNACION' ) = asigh.ID_ASIGNACION_SOLICITUD_HIST AND ".
                                             "(SELECT COUNT(*) FROM INFO_ASIGNACION_SOLICITUD_HIST asigh1 ".
                                             "    WHERE asigh1.asignacion_solicitud_id = asig.ID_ASIGNACION_SOLICITUD AND asigh1.TIPO = 'REASIGNACION' ) > 1".
                                             " THEN ".
                                             " 'PIN2' ".
                                             " ELSE ".
                                             "'NO_PIN' ".
                                             "END PIN,".
                                             "asig.TIPO_ATENCION, asig.ESTADO, asig.REFERENCIA_ID, ".
                                             "asig.REFERENCIA_CLIENTE, TO_CHAR(asig.FE_CREACION,'YYYY/MM/DD HH24:MI:SS') FE_CREACION, ".
                                             "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                                             " WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                                             " THEN (SELECT DISTINCT caso.NUMERO_CASO FROM DB_COMUNICACION.INFO_CASO caso ".
                                             " WHERE caso.ID_CASO = asig.REFERENCIA_ID) ELSE NULL END NUMERO, ".
                                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(asig.REFERENCIA_ID) ".
                                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_CASO(asig.REFERENCIA_ID) ".
                                             " END ESTADO_TAREA,".
                                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_TAREA(asig.REFERENCIA_ID) ".
                                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(asig.REFERENCIA_ID) ".
                                             " END ESTADO_CASO, ".
                                             " CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID)".
                                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID)".
                                             " END AFECTADO ";
        $arrGroupBy                 = array();
        $arrOrderBy                 = array();
        $strGroupBy                 = "";
        $strOrderBy                 = "";
        if($strUsrAsignado != "")
        {
            $where .= " AND asigh.USR_ASIGNADO = :usrAsignado ";
            $query->setParameter('usrAsignado', $strUsrAsignado);
        }
        if($objFeCreacion != "")
        {
            $where .= " AND TO_CHAR(asigh.FE_CREACION,'YYYY/MM/DD') = :feCreacion ";
            $query->setParameter('feCreacion', $objFeCreacion);
        }

        $sql   = "FROM
                    DB_SOPORTE.INFO_ASIGNACION_SOLICITUD asig
                    JOIN DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_HIST asigh ON asigh.ASIGNACION_SOLICITUD_ID = asig.ID_ASIGNACION_SOLICITUD
                  WHERE
                    asig.EMPRESA_COD = :empresaCod
                    AND asig.ESTADO <> :estadoEliminado
                    AND asigh.ESTADO <> :estadoEliminado
                    AND asig.ASIGNACION_PADRE_ID IS NULL
                  ";

        if($strEsGroupBy == "S")
        {
            if ($strEsGroupByUsrAsignacion == "S")
            {
                $arrGroupBy[]="asigh.USR_ASIGNADO";
            }
            if ($strEsGroupByEstado == "S")
            {
                $arrGroupBy[]="asig.ESTADO";
            }
            if ($strEsGroupByTipoAtencion == "S")
            {
                $arrGroupBy[]="asig.TIPO_ATENCION";
            }
            if ($strEsGroupByOrigen == "S")
            {
                $arrGroupBy[]="asig.ORIGEN";
            }
            $strGroupBy = implode(",",$arrGroupBy);

            $select      = "SELECT ".$strGroupBy.",count(asig) ord_cantidad ";
            $strGroupBy  = " GROUP BY ".$strGroupBy;
            $strOrderBy  = " ORDER BY ord_cantidad DESC";
            $sql = $select.$sql.$where.$strGroupBy.$strOrderBy;
        }

        //
        else
        {
            if ($strEsOrderByFeCreacionDesc == "S")
            {
                $arrOrderBy[]="asig.FE_CREACION DESC";
            }
            if ($strEsOrderByEstado == "S")
            {
                $arrOrderBy[]="asig.ESTADO";
            }
            if($strEsOrderByUsrAsignacion=="S")
            {
                $arrOrderBy[]="asig.USR_ASIGNADO";
            }
            if ($strEsOrderByTipoAtencion == "S")
            {
                $arrOrderBy[]="asig.TIPO_ATENCION";
            }
            if(count($arrOrderBy)>0 )
            {
                $strOrderBy = implode(",",$arrOrderBy);
                $strOrderBy = " ORDER BY ".$strOrderBy." DESC";
            }
            $sql = $select.$sql.$where.$strOrderBy;

            if ($strEsOrderByTodosEstados === "S")
            {
                $sql ="SELECT USR_ASIGNADO, TIPO, PIN, TIPO_ATENCION, ESTADO, ".
                        "FE_CREACION, NUMERO, ".
                        "CASE WHEN ESTADO = 'Pendiente' THEN ".
                        "'A' ".
                        "WHEN ESTADO = 'EnGestion' AND TIPO_ATENCION = 'TAREA' AND ESTADO_TAREA <> 'Finalizada' AND TIPO = 'REASIGNACION'  THEN ".
                        "'B' ".
                        "WHEN ESTADO = 'EnGestion' AND TIPO_ATENCION = 'CASO' AND ESTADO_CASO <> 'Cerrado' AND TIPO = 'REASIGNACION' THEN ".
                        "'B' ".
                        "WHEN ESTADO = 'EnGestion' AND TIPO_ATENCION = 'TAREA' AND ESTADO_TAREA <> 'Finalizada' AND TIPO = 'ASIGNACION' THEN ".
                        "'C' ".
                        "WHEN ESTADO = 'EnGestion' AND TIPO_ATENCION = 'CASO' AND ESTADO_CASO <> 'Cerrado' AND TIPO = 'ASIGNACION' THEN ".
                        "'C' ".
                        "ELSE ".
                        "'D' ".
                        "END ORDEN, ".
                      " REFERENCIA_ID, REFERENCIA_CLIENTE, ESTADO_TAREA, ESTADO_CASO, AFECTADO  FROM (".$sql.") ORDER BY ORDEN ASC";
            }
            if ($intLimit>0)
            {
                $sql = "SELECT * FROM (".$sql.") WHERE ROWNUM <= ".$intLimit;
            }
        }

        $query->setParameter('empresaCod', $strCodEmpresa);
        $query->setParameter('estadoEliminado', 'Eliminado');

        $query->setSQL($sql);
        if($strEsGroupBy == "S")
        {
            for($i=0;$i<count($arrGroupBy);$i++)
            {
                $strCampo = str_replace('asig.','',$arrGroupBy[$i]);
                if(strpos($strCampo,'_'))
                {
                    $arrCampo         = split($strCampo,'_');
                    $strCampoPresenta = strtolower($arrCampo[0]).ucwords($arrCampo[1]);
                }
                $rsm->addScalarResult( $strCampo, $strCampoPresenta,'string');
            }
        }
        else
        {
            $rsm->addScalarResult('USR_ASIGNADO', 'usrAsignado','string');
            $rsm->addScalarResult('TIPO', 'tipo','string');
            $rsm->addScalarResult('TIPO_ATENCION', 'tipoAtencion','string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');
            $rsm->addScalarResult('REFERENCIA_ID', 'referenciaId','string');
            $rsm->addScalarResult('REFERENCIA_CLIENTE', 'referenciaCliente','string');
            $rsm->addScalarResult('ESTADO_TAREA', 'estadoTarea','string');
            $rsm->addScalarResult('ESTADO_CASO', 'estadoCaso','string');
            $rsm->addScalarResult('FE_CREACION', 'feCreacion','string');
            $rsm->addScalarResult('NUMERO', 'numero','string');
            $rsm->addScalarResult('PIN', 'pin','string');
            $rsm->addScalarResult('AFECTADO', 'afectado','string');
        }
        $datos = $query->getScalarResult();
        //}
        return $datos;

    }

    /**
     * Actualización: Se agrega filtro para listar solo las asiganciones que tengan el mismo departamento de la tarea,
     *                Se añade campos para el nuevo perfil de verNuevosCampos en agente
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.10 24-01-2022
     *      
     * Actualización: Se agrega programación para consultar detalles de asignaciones por tabvisible y por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.9 28-05-2020
     * 
     * Actualización: Se agrega que retorne el campo id_asignado donde se muestra el id del departamento asignado a la tarea.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 23-03-2020
     * 
     * Actualización: Se agrega que retorne el canton de donde se registro la asignación.
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.7 10-01-2020
     * 
     * Actualización: Se agrega campo padre para saber si se presenta la opcion de ver asignaciones hija.
     *                Se agrega en la información devuelta por el Query el campo Origen para asi 
     *                realizar la validación de su procedencia.
     * Costo Query 74
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.6 01-07-2019
     * 
     * Actualización: Se agrega en la recepcion de los para metros que considere feCreacionFin
     *                para de esta manera realizar la busqueda por rango de fecha.
     * Costo Query 76
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.5 20-05-2019
     * 
     * Actualización: Se agrega programación para obtener el campo infoTareas el cual contiene 
     *                las tareas de un caso y se quita que consulte el número de tarea por caso.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 21-02-2019
     * 
     * Actualización: Se agrega que obtenga el asignado para las tareas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 22-01-2019
     *
     * Actualización: Se agrega que se pueda filtre asignaciones por id_canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 15-01-2019
     *
     * Actualización: Se obtiene afectado y asignado directamente desde el query
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 28-11-2018
     *
     * Realiza la consulta de detalles de asignaciones segun los criterios recibidos por parametros.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     codEmpresa              => id de la empresa
     *     usrAsignado             => Login del usuario asignado
     *     intIdDepartamento       => id del departamento
     *     intIdCanton             => id del canton
     *     strTabVisible           => tab visible de la asignación
     *     strEstado               => estado de la asignación
     *     esOrderByUsrAsignacion  => Bandera que indica si la consulta sera ordenada por usrAsignacion (S o N)
     *     esOrderByEstado         => Bandera que indica si la consulta sera ordenada por estado (S o N)
     *     esOrderByFeCreacionDesc => Bandera que indica si la consulta sera ordenada por feCreacion en forma descendente (S o N)
     *     esOrderByTipoAtencion   => Bandera que indica si la consulta sera ordenada por tipo de atencion (S o N)
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getDetalleAsignaciones($arrayParametros){
        
        $intStart                   = $arrayParametros["start"];
        $intLimit                   = $arrayParametros["limit"];
        $strCodEmpresa              = $arrayParametros["codEmpresa"];
        $strUsrAsignado             = $arrayParametros["usrAsignado"];
        $strFeCreacion              = $arrayParametros["feCreacion"];
        $strFeCreacionFin           = $arrayParametros["feCreacionFin"];
        $strCambioTurno             = $arrayParametros["strCambioTurno"];
        $strCambioTurnoAuto         = $arrayParametros["strCambioTurnoAuto"];
        $intDepartamentoId          = $arrayParametros["intIdDepartamento"];
        $intCantonId                = $arrayParametros["intIdCanton"];        
        $strEsOrderByUsrAsignacion  = $arrayParametros["esOrderByUsrAsignacion"];
        $strEsOrderByTipoAtencion   = $arrayParametros["esOrderByTipoAtencion"];
        $strEsOrderByEstado         = $arrayParametros["esOrderByEstado"];
        $strEsOrderByFeCreacionDesc = $arrayParametros["esOrderByFeCreacionDesc"];
        $strBuscaPendientes         = $arrayParametros["buscaPendientes"];
        $strTabVisible              = $arrayParametros['strTabVisible'];
        $strEstado                  = $arrayParametros['strEstado'];
        $booleanPermiteVerNuevosCamposTareas = isset($arrayParametros['permiteVerNuevosCamposTareas'])?
                                                $arrayParametros['permiteVerNuevosCamposTareas']:'';
        $strFechaConsultaQuery      = "FE_CREACION";
        $where                      = "";
        $booleanNewValue = false;

        try
        {
            $rsm                        = new ResultSetMappingBuilder($this->_em);
            $query                      = $this->_em->createNativeQuery(null,$rsm);

            $strQuery1 = '';
            $strQuery2 = '';
            $strQuery3 ='';
            if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas)
            {

                $strQuery1 ="SELECT ASIGNACION.*, ".
                                    "SPKG_ASIGNACION_SOLICITUD.F_GET_FECHA_CREACION_TAREA(NVL(ASIGNACION.ID_DETALLE,0), ".
                                    " ASIGNACION.VECES_TAREA_INICIADA) AS FECHA_TAREA_CREACION, ".
                                    " (SELECT tp.VALOR_TIEMPO FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp ".
                                    "    WHERE tp.ID_TIEMPO_PARCIAL = (SELECT max(tp1.ID_TIEMPO_PARCIAL) FROM ".
                                    " DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp1 WHERE tp1.DETALLE_ID = ASIGNACION.ID_DETALLE ".
                                    "    AND tp1.ESTADO = 'Pausada')) AS VALOR_TIEMPO_PAUSA, ".
                            "(SELECT TO_CHAR(tp.FE_CREACION, 'dd-mm-yyyy hh24:mi') AS fe_creacion FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp ".
                                " WHERE tp.ID_TIEMPO_PARCIAL = (SELECT max(tp1.ID_TIEMPO_PARCIAL) FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp1 ".
                                " WHERE tp1.DETALLE_ID = ASIGNACION.ID_DETALLE AND tp1.ESTADO = 'Reanudada')) AS FECHA_CREACION_REANUDA, ".
                            "CASE WHEN ASIGNACION.IDCASO <> 0 THEN (SELECT ic.EMPRESA_COD FROM DB_SOPORTE.INFO_CASO ic
                                        WHERE ic.ID_CASO = ASIGNACION.IDCASO) ELSE '' END CASO_EMPRESA_COD, ".
                            " (SELECT PD.valor1 FROM DB_GENERAL.ADMI_PARAMETRO_DET PD, DB_GENERAL.ADMI_PARAMETRO_CAB PC WHERE PC.ID_PARAMETRO = ". 
                                    " PD.PARAMETRO_ID AND PC.NOMBRE_PARAMETRO = 'TAREAS_MOSTRAR_BTN_INFO_ADICIONAL' AND PC.estado = 'Activo' ".
                                    " AND PD.estado = 'Activo' AND PD.VALOR1 = ASIGNACION.NOMBRE_TAREA AND rownum = 1) AS TAREA_INFO_ADICIONAL, ".
                            "SPKG_UTILIDADES.GET_VARCHAR_CLEAN(CAST(ASIGNACION.OBSERVACION_TAREA AS VARCHAR2(3999))) AS OBSERVACION, ".
                            "DB_SOPORTE.SPKG_INFO_TAREA.F_GET_TAREA_PADRE(ASIGNACION.DETALLE_ID_RELACIONADO) AS NUMERO_TAREA_PADRE,  ".
                            "DB_SOPORTE.SPKG_INFO_TAREA.F_GET_PERMITE_FINALIZAR_INFORM(ASIGNACION.ID_DETALLE, ASIGNACION.NOMBRE_TAREA, ".
                            ":departamentoId) AS PERMITE_FINALIZAR_INFORME, ".
                            "(SELECT idh.TAREA_ID FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh, DB_SOPORTE.ADMI_TAREA ata WHERE ".
                                    "idh.ID_DETALLE_HISTORIAL = (SELECT min(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1 ".
                                    "WHERE idh1.DETALLE_ID = ASIGNACION.ID_DETALLE  AND idh1.ACCION = 'Reasignada' AND idh1.TAREA_ID IS NOT NULL) ".
                                        "AND ata.ID_TAREA = idh.TAREA_ID) AS ID_TAREA_ANTERIOR, ".

                            "(SELECT ata.NOMBRE_TAREA FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh, DB_SOPORTE.ADMI_TAREA ata  WHERE ".
                                    "idh.ID_DETALLE_HISTORIAL = (SELECT min(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1 ".
                                     "WHERE idh1.DETALLE_ID = ASIGNACION.ID_DETALLE AND idh1.ACCION = 'Reasignada' AND idh1.TAREA_ID IS NOT NULL) ".
                                        "AND ata.ID_TAREA = idh.TAREA_ID) AS NOMBRE_TAREA_ANTERIOR ".
                            " FROM (";
                              
                $strQuery2 =  ", CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT a.ASIGNADO_ID FROM INFO_TAREA a ". 
                                    "JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper ON iper.PERSONA_ID = a.REF_ASIGNADO_ID ". 
                                    "JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = iper.OFICINA_ID ".
                                        "WHERE a.NUMERO_TAREA =  asig.REFERENCIA_ID ". 
                                        "AND iper.ESTADO = 'Activo' ".
                                        "AND iper.DEPARTAMENTO_ID = :departamentoId ".
                                        "AND ofi.EMPRESA_ID = :empresaCod) ".
                                    "ELSE asig.DEPARTAMENTO_ID END TAREA_DEPARTAMENTO_ID, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com ".
                                        "WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID) ELSE NULL END ID_DETALLE,".
                                "CASE WHEN asig.TIPO_ATENCION ='TAREA' THEN (SELECT idh.ID_DETALLE_HISTORIAL ".
                                        "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh WHERE idh.ID_DETALLE_HISTORIAL = ".
                                        "(SELECT max(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1, ".
                                            "DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = asig.REFERENCIA_ID ".
                                           " AND idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END ID_DETALLEHIST, ". 
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT idh.ESTADO FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh ".
                                    "WHERE idh.ID_DETALLE_HISTORIAL = (SELECT max(idh1.ID_DETALLE_HISTORIAL) FROM ".
                                     "DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1,DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = ".
                                     " asig.REFERENCIA_ID AND idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END ESTADOHIST, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT TO_CHAR(idh.FE_CREACION, 'yyyy/mm/dd hh24:mi:ss') FECHA ".
                                        "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh WHERE idh.ID_DETALLE_HISTORIAL = (SELECT ".
                                        " max(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1,".
                                        " DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = asig.REFERENCIA_ID AND ".
                                        " idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END FECHACREAHIST, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT COUNT(its.ID_SEGUIMIENTO) n_tarea_ini FROM ".
                                " DB_SOPORTE.INFO_TAREA_SEGUIMIENTO its WHERE its.DETALLE_ID = (SELECT com.DETALLE_ID FROM ".
                                " DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1) ".
                                "  AND its.OBSERVACION like '%Iniciada%' ) ELSE NULL END VECES_TAREA_INICIADA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT ip.NOMBRES||' '||ip.APELLIDOS  FROM ".
                                  "DB_COMERCIAL.INFO_PERSONA ip WHERE ip.LOGIN = asig.USR_ASIGNADO) ELSE NULL END REF_ASIGNADO_NOMBRE, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.NOMBRE_TAREA FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                    "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = ".
                                    " asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END NOMBRE_TAREA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.TAREA_ID FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                    "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                    "com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END IDTAREA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.NOMBRE_PROCESO FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                   "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = ".
                                   " asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END NOMBRE_PROCESO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT NVL(MAX(dh.CASO_ID), 0) AS ID_CASO FROM ".
                                        " DB_SOPORTE.INFO_DETALLE_HIPOTESIS dh,DB_SOPORTE.INFO_DETALLE id WHERE dh.ID_DETALLE_HIPOTESIS = ".
                                        " id.DETALLE_HIPOTESIS_ID AND id.ID_DETALLE = (SELECT com.DETALLE_ID FROM ".
                                        " DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ".
                                        " ELSE NULL END IDCASO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT id.DETALLE_ID_RELACIONADO FROM DB_SOPORTE.INFO_DETALLE id ".
                                        "WHERE id.ID_DETALLE = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                        " com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END DETALLE_ID_RELACIONADO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT id.OBSERVACION FROM DB_SOPORTE.INFO_DETALLE id WHERE ".
                                        "id.ID_DETALLE = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                        "com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END OBSERVACION_TAREA ";
                $strQuery3 = ")ASIGNACION ".
                             " WHERE ASIGNACION.TAREA_DEPARTAMENTO_ID = :departamentoId";   
            }

            $select                     = "SELECT asig.ID_ASIGNACION_SOLICITUD, TO_CHAR(asig.FE_CREACION,'YYYY/MM/DD HH24:MI:SS') FE_CREACION, ".
                                                 "asig.REFERENCIA_CLIENTE, asig.TIPO_ATENCION, asig.ORIGEN, ".
                                                 "DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_ES_PADRE(asig.ID_ASIGNACION_SOLICITUD) PADRE,".
                                                 "asig.TIPO_PROBLEMA, asig.REFERENCIA_ID, asig.CRITICIDAD, ".
                                                 "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                                                 "     WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                                                 "     THEN (SELECT DISTINCT caso.NUMERO_CASO FROM DB_COMUNICACION.INFO_CASO caso ".
                                                 "          WHERE caso.ID_CASO = asig.REFERENCIA_ID) ELSE NULL END NUMERO,".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " asig.REFERENCIA_ID ".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " NULL ".
                                                 " END NUMERO_TAREA,".
                                                 "asig.USR_ASIGNADO,". 
                                                 "DB_SOPORTE.SPKG_UTILIDADES.GET_VARCHAR_CLEAN(CAST(asig.DETALLE AS VARCHAR2(3999))) as DETALLE, ".
                                                 "asig.CAMBIO_TURNO, asig.ESTADO, ".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(asig.REFERENCIA_ID) ".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_CASO(asig.REFERENCIA_ID) ".
                                                 " END ESTADO_TAREA,".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " NULL ".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_INFO_TAREAS_POR_CASO(asig.REFERENCIA_ID) ".
                                                 " END INFO_TAREAS,".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_TAREA(asig.REFERENCIA_ID) ".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(asig.REFERENCIA_ID) ".
                                                 " END ESTADO_CASO, ".
                                                 "asig.DEPARTAMENTO_ID, ".
                                                 "asig.TAB_VISIBLE, ".
                                                 "ofi.CANTON_ID, ".
                                                 " can.NOMBRE_CANTON,".
                                                 " can.REGION,".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(asig.REFERENCIA_ID,'asignado') ".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(".
                                                 "     TO_CHAR(SPKG_ASIGNACION_SOLICITUD.F_NUMERO_TAREA_POR_CASO(asig.REFERENCIA_ID)),".
                                                 "     'asignado') ".
                                                 " END ASIGNADO, ".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' ".
                                                 "THEN (SELECT PTO1.ID_PUNTO ".
                                                 " FROM DB_COMERCIAL.INFO_PUNTO PTO1 ".
                                                 " JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER1 ".
                                                 " ON PER1.ID_PERSONA_ROL = PTO1.PERSONA_EMPRESA_ROL_ID ".
                                                 " JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EROL1 ".
                                                 " ON EROL1.ID_EMPRESA_ROL = PER1.EMPRESA_ROL_ID ".
                                                 " WHERE LOGIN = DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID) ".
                                                 " AND EROL1.EMPRESA_COD = :empresaCod ".
                                                 " AND PTO1.ESTADO NOT IN ('Cancelado','Anulado') ".
                                                 ") ".
                                                 "WHEN asig.TIPO_ATENCION = 'CASO' ".
                                                 "THEN (SELECT PTO1.ID_PUNTO ".
                                                 " FROM DB_COMERCIAL.INFO_PUNTO PTO1 ".
                                                 " JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER1 ".
                                                 " ON PER1.ID_PERSONA_ROL = PTO1.PERSONA_EMPRESA_ROL_ID ".
                                                 " JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EROL1 ".
                                                 " ON EROL1.ID_EMPRESA_ROL = PER1.EMPRESA_ROL_ID ".
                                                 " WHERE LOGIN = DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID) ".
                                                 " AND EROL1.EMPRESA_COD = :empresaCod ".
                                                 " AND PTO1.ESTADO NOT IN ('Cancelado','Anulado') ".
                                                 ") ".
                                                " END ID_AFECTADO,".
                                                 "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID)".
                                                 " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                                                 " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID)".
                                                 " END AFECTADO ";

            $arrOrderBy                 = array();
            $strOrderBy                 = "";
            
            if($strUsrAsignado != "")
            {
                $where .= " AND asig.USR_ASIGNADO = :usrAsignado ";
                $query->setParameter('usrAsignado', $strUsrAsignado);
            }

            if($intDepartamentoId != 0)
            {
                $where .= " AND DEPARTAMENTO_ID = :departamentoId ";
                $query->setParameter('departamentoId', $intDepartamentoId);
            }

            if($intCantonId != null && $intCantonId != 0)
            {
                $where .= " AND CANTON_ID = :cantonId "; 
                $query->setParameter('cantonId', $intCantonId);
            }      

            if ($strEsOrderByFeCreacionDesc == "S")
            {
                $arrOrderBy[]="asig.FE_CREACION DESC";
            }   

            if($strCambioTurno != "")
            {
                $where .= " AND CAMBIO_TURNO = :cambioTurno ";
                $query->setParameter('cambioTurno', $strCambioTurno);
            }
            if ($strTabVisible !== 'todos')
            {
                if($strTabVisible !== '' && $strTabVisible !== null)
                {
                    $where .= " AND asig.TAB_VISIBLE = :tabVisible ";
                    $query->setParameter('tabVisible', $strTabVisible);
                }
                else
                {
                    $where .= " AND (asig.TAB_VISIBLE IS NULL OR TAB_VISIBLE ='' ) ";
                }
            }

            if($strEstado !== '' && $strEstado !== null)
            {
                if ($strEstado === 'Abierto')
                {
                    $arrayEstados = array('Pendiente','EnGestion','Standby');
                    $where .= " AND asig.ESTADO in (:estado) ";
                    $query->setParameter('estado', $arrayEstados);
                }
                elseif ($strEstado === 'Standby')
                {
                    $where .= " AND asig.ESTADO  = :estado ";
                    $query->setParameter('estado', trim($strEstado));
                }
                else
                {
                    $where .= " AND asig.ESTADO = :estado ";
                    $query->setParameter('estado', trim($strEstado));
                    $strFechaConsultaQuery  = "FE_ULT_MOD";
                }
            }
            
            
            if(!empty($strFeCreacionFin) && !empty($strFeCreacion))
            {
                $arrFechaCreacion    = explode("-",$strFeCreacion);
                $arrFechaCreacionFin = explode("-",$strFeCreacionFin);
                $objFeCreacion       =    date("Y/m/d", strtotime($arrFechaCreacion[2]."-".$arrFechaCreacion[1]."-".$arrFechaCreacion[0]));
                $objFeCreacionFin    =    date("Y/m/d", strtotime($arrFechaCreacionFin[2]."-".$arrFechaCreacionFin[1]."-".$arrFechaCreacionFin[0]));         
                                $where .= " AND TO_CHAR(asig.".$strFechaConsultaQuery.",'YYYY/MM/DD') >= :feCreacion ".
                                          " AND TO_CHAR(asig.".$strFechaConsultaQuery.",'YYYY/MM/DD') <= :feCreacionFin ";

                $query->setParameter('feCreacion',    $objFeCreacion);
                $query->setParameter('feCreacionFin', $objFeCreacionFin);
            }

            if( !empty($strFeCreacion) && empty($strFeCreacionFin) )
            {
                $arrFechaCreacion = explode("-",$strFeCreacion);
                $objFeCreacion    = date("Y/m/d", strtotime($arrFechaCreacion[2]."-".$arrFechaCreacion[1]."-".$arrFechaCreacion[0]));
                $where           .= " AND TO_CHAR(asig.".$strFechaConsultaQuery.",'YYYY/MM/DD') = :feCreacion ";
                $query->setParameter('feCreacion', $objFeCreacion);
            }

            if($strEsOrderByUsrAsignacion == "S")
            {
                $arrOrderBy[]="asig.USR_ASIGNADO";
            }

            if ($strEsOrderByTipoAtencion == "S")
            {
                $arrOrderBy[]="asig.TIPO_ATENCION";
            }

            if ($strEsOrderByEstado == "S")
            {
                $arrOrderBy[]="asig.ESTADO";
            }

            $strOrderBy = implode(",",$arrOrderBy);
            $strOrderBy = " ORDER BY ".$strOrderBy;

            $sql   = " FROM INFO_ASIGNACION_SOLICITUD asig
                       JOIN INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = asig.OFICINA_ID
                       JOIN ADMI_CANTON can ON can.ID_CANTON = ofi.CANTON_ID 
                      WHERE asig.EMPRESA_COD =  :empresaCod
                        AND asig.ESTADO  <> :estadoAsignacionElim ";

            if($strBuscaPendientes==='S' && $strCambioTurnoAuto =="")
            {
                $sql   = "SELECT * FROM (".$select.$sql.$where.$strOrderBy.") ASIGN ".
                         " WHERE ".
                         "  ASIGN.ESTADO = :estadoAsignacionPend ";
                $sql  .= " OR (".
                         "      ASIGN.ESTADO IN (:estadoAsignacionGest) AND ".
                         "      (".
                         "        ASIGN.ESTADO_TAREA <> :estadoTarea OR (ASIGN.ESTADO_CASO <> :estadoCaso AND ASIGN.ESTADO_CASO IS NOT NULL)".
                         "      )".
                         "    )";
                $query->setParameter('estadoAsignacionGest', array('EnGestion','Standby'));
                $query->setParameter('estadoTarea',          'Anulada');
                $query->setParameter('estadoCaso',           'Cerrado');
                $query->setParameter('estadoAsignacionPend', 'Pendiente');

            }
            else if($strBuscaPendientes=='S' && $strCambioTurnoAuto =='S')
            {
                $where .= " AND asig.ESTADO <> :estadoAsignaCerrado ".
                          " AND asig.CAMBIO_TURNO = :cambiTurno ";
                $query->setParameter('estadoAsignaCerrado', 'Cerrado');
                $query->setParameter('cambiTurno', 'N');

                $sql   = $select.$sql.$where.$strOrderBy;
            }
            else
            {
                if($intDepartamentoId != 0 && isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas)
                {
                    $booleanNewValue = true;
                    $sql   = $strQuery1.$select.$strQuery2.$sql.$where.$strOrderBy.$strQuery3;
                }else
                {
                    $sql   = $select.$sql.$where.$strOrderBy;
                }                
            }
            
            $query->setParameter('empresaCod', $strCodEmpresa);
            $query->setParameter('estadoAsignacionElim', 'Eliminado');
            
            $query->setSQL($sql);   

            $rsm->addScalarResult('ID_ASIGNACION_SOLICITUD', 'id'                ,'integer');
            $rsm->addScalarResult('FE_CREACION'            , 'feCreacion'        ,'string');
            $rsm->addScalarResult('REFERENCIA_CLIENTE'     , 'referenciaCliente' ,'string');
            $rsm->addScalarResult('TIPO_ATENCION'          , 'tipoAtencion'      ,'string');
            $rsm->addScalarResult('TIPO_PROBLEMA'          , 'tipoProblema'      ,'string');
            $rsm->addScalarResult('REFERENCIA_ID'          , 'referenciaId'      ,'integer');
            $rsm->addScalarResult('CRITICIDAD'             , 'criticidad'        ,'string');
            $rsm->addScalarResult('NUMERO'                 , 'numero'            ,'string');
            $rsm->addScalarResult('USR_ASIGNADO'           , 'usrAsignado'       ,'integer');
            $rsm->addScalarResult('DETALLE'                , 'detalle'           ,'string');
            $rsm->addScalarResult('CAMBIO_TURNO'           , 'cambioTurno'       ,'float');
            $rsm->addScalarResult('ESTADO'                 , 'estado'            ,'string');
            $rsm->addScalarResult('ESTADO_TAREA'           , 'estadoTarea'       ,'string');
            $rsm->addScalarResult('INFO_TAREAS'            , 'infoTareas'        ,'string');
            $rsm->addScalarResult('ESTADO_CASO'            , 'estadoCaso'        ,'string');
            $rsm->addScalarResult('NUMERO_TAREA'           , 'numeroTarea'       ,'string');
            $rsm->addScalarResult('DEPARTAMENTO_ID'        , 'departamentoId'    ,'string');
            $rsm->addScalarResult('NOMBRE_CANTON'          , 'ciudad'            ,'string');
            $rsm->addScalarResult('ASIGNADO'               , 'asignado'          ,'string');
            $rsm->addScalarResult('AFECTADO'               , 'afectado'          ,'string');
            $rsm->addScalarResult('ID_AFECTADO'            , 'id_afectado'       ,'integer');
            $rsm->addScalarResult('ORIGEN'                 , 'origen'            ,'string');
            $rsm->addScalarResult('PADRE'                  , 'padre'             ,'string');
            $rsm->addScalarResult('TAB_VISIBLE'            , 'tabVisible'        ,'string');
            
            if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas && $booleanNewValue)
            {
                $rsm->addScalarResult('FECHA_TAREA_CREACION'               , 'fecha_tarea_creacion'        ,'string');
                $rsm->addScalarResult('VALOR_TIEMPO_PAUSA'                 , 'valor_tiempo_pausa'          ,'string');
                $rsm->addScalarResult('FECHA_CREACION_REANUDA'             , 'fecha_creacion_reanuda'      ,'string');
                $rsm->addScalarResult('CASO_EMPRESA_COD'                   , 'caso_empresa_cod'            ,'integer');
                $rsm->addScalarResult('TAREA_INFO_ADICIONAL'               , 'tarea_info_adicional'        ,'string');
                $rsm->addScalarResult('OBSERVACION'                        , 'observacion'                 ,'string');
                $rsm->addScalarResult('NUMERO_TAREA_PADRE'                 , 'numero_tarea_padre'          ,'integer');
                $rsm->addScalarResult('PERMITE_FINALIZAR_INFORME'          , 'permite_finalizar_informe'   ,'string');
                $rsm->addScalarResult('ID_DETALLE'                         , 'idDetalle'                   ,'integer');
                $rsm->addScalarResult('ID_DETALLEHIST'                     , 'idDetalleHist'               ,'integer');
                $rsm->addScalarResult('ESTADOHIST'                         , 'estadoHist'                  ,'string');
                $rsm->addScalarResult('FECHACREAHIST'                      , 'fechaCreaHist'               ,'string');
                $rsm->addScalarResult('REF_ASIGNADO_NOMBRE'                , 'ref_asignado_nombre'         ,'string');
                $rsm->addScalarResult('NOMBRE_TAREA'                       , 'nombre_tarea'                ,'string');
                $rsm->addScalarResult('IDTAREA'                            , 'idtarea'                     ,'integer');
                $rsm->addScalarResult('NOMBRE_PROCESO'                     , 'nombreProceso'               ,'string');
                $rsm->addScalarResult('IDCASO'                             , 'idCaso'                      ,'integer');
                $rsm->addScalarResult('ID_TAREA_ANTERIOR'                  , 'id_tarea_anterior'           ,'integer');
                $rsm->addScalarResult('NOMBRE_TAREA_ANTERIOR'              , 'nombre_tarea_anterior'       ,'string');
            }

            $datos = $query->getScalarResult();        

        }
        catch(\Exception $e)
        {
            error_log('Se produjo un error en InfoAsignacionSolicitudRepository.getDetalleAsignaciones -> '.$e);
            $datos = array();
        }
        
        return $datos;
    }

    /**
     * Actualización: Se agrega filtro para listar solo las asiganciones que tengan el mismo departamento de la tarea,
     *                Se añade campos para el nuevo perfil de verNuevosCampos en agente
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.9 24-01-2022
     * 
     * Actualización: Se agrega programación para consultar detalles de asignaciones por tabvisible y por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 28-05-2020
     * 
     * Actualización: Se agrega que retorne el canton de donde se registro la asignación.
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.7 10-01-2020
     * 
     * Actualización: Se agrega registro "Origen" para validacion en consulta detalle.
     *                Se agrega validación para realizar la consulta de las asignaciones proactivas.
     * Costo query 76
     * @author Miguel Angulo Sánchez<jmangulos@telconet.ec>
     * @version 1.6 14-06-2019
     * 
     * Actualización: Se retira parte del query para evitar que se presenten las asignaciones eliminadas
     *                mientras tenían asociado un caso activo.
     * Costo query 76
     * @author Miguel Angulo Sánchez<jmangulos@telconet.ec>
     * @version 1.5 09-06-2019
     * 
     * Costo query 81
     * 
     * Actualización: Se agrega programación para obtener el campo infoTareas el cual contiene 
     *                las tareas de un caso y se quita que consulte el número de tarea por caso.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 21-02-2019
     * 
     * Actualización: Se agrega que obtenga el asignado para las tareas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 22-01-2019
     *
     * Actualización: Se agrega que se pueda filtre asignaciones por id_canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 15-01-2019
     *
     * Actualización: Se modifica para obtener campo afectado y asignado directamente desde el query
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 28-11-2018
     *
     * Realiza la consulta de detalles de asignaciones que estan pendientes y todas las del dia.
     * @param $arrayParametros
     * [
     *     codEmpresa        => id de la empresa
     *     intIdDepartamento => id del departamento
     *     intIdCanton       => id del canton
     *     strTabVisible     => tab visible de la asignación
     *     strEstado         => estado de la asignación
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 26-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getDetalleAsignacionesPorDefecto($arrayParametros){
        $strCodEmpresa            = $arrayParametros["codEmpresa"];
        $intDepartamentoId        = $arrayParametros["intIdDepartamento"];
        $intCantonId              = $arrayParametros["intIdCanton"];
        $strConsulCambioTurnoAuto = $arrayParametros["consultaCambioTurno"];
        $strUsrCambioTurno        = $arrayParametros["strUsrCambioTurno"];
        $asignacionProactiva      = $arrayParametros['asignacionProactiva'];
        $asignacionConsultaHijas  = $arrayParametros['asignacionConsultaHijas'];
        $intPadreId               = $arrayParametros['intPadreId'];      
        $strTabVisible            = $arrayParametros['strTabVisible'];
        $strEstado                = $arrayParametros['strEstado'];
        $booleanPermiteVerNuevosCamposTareas = $arrayParametros['permiteVerNuevosCamposTareas'];
        $booleanNewValue = true;

        try
        {
            $rsm           = new ResultSetMappingBuilder($this->_em);
            $query         = $this->_em->createNativeQuery(null,$rsm);

            $strQuery1 = '';
            $strQuery2 = '';
            if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas
                && $intDepartamentoId != 0)
            {

                $strQuery1 =", SPKG_ASIGNACION_SOLICITUD.F_GET_FECHA_CREACION_TAREA(NVL(ASIGNACION.ID_DETALLE,0), ASIGNACION.VECES_TAREA_INICIADA) ". 
                                    " AS FECHA_TAREA_CREACION, (SELECT tp.VALOR_TIEMPO FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp ".
                                    "    WHERE tp.ID_TIEMPO_PARCIAL = (SELECT max(tp1.ID_TIEMPO_PARCIAL) FROM ".
                                    " DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp1 WHERE tp1.DETALLE_ID = ASIGNACION.ID_DETALLE ".
                                    "    AND tp1.ESTADO = 'Pausada')) AS VALOR_TIEMPO_PAUSA, ".
                            "(SELECT TO_CHAR(tp.FE_CREACION, 'dd-mm-yyyy hh24:mi') AS fe_creacion FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp ".
                                " WHERE tp.ID_TIEMPO_PARCIAL = (SELECT max(tp1.ID_TIEMPO_PARCIAL) FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL tp1 ".
                                " WHERE tp1.DETALLE_ID = ASIGNACION.ID_DETALLE AND tp1.ESTADO = 'Reanudada')) AS FECHA_CREACION_REANUDA, ".
                            "CASE WHEN ASIGNACION.IDCASO <> 0 THEN (SELECT ic.EMPRESA_COD FROM DB_SOPORTE.INFO_CASO ic
                                        WHERE ic.ID_CASO = ASIGNACION.IDCASO) ELSE '' END CASO_EMPRESA_COD, ".
                            " (SELECT PD.valor1 FROM DB_GENERAL.ADMI_PARAMETRO_DET PD, DB_GENERAL.ADMI_PARAMETRO_CAB PC WHERE PC.ID_PARAMETRO = ". 
                                    " PD.PARAMETRO_ID AND PC.NOMBRE_PARAMETRO = 'TAREAS_MOSTRAR_BTN_INFO_ADICIONAL' AND PC.estado = 'Activo' ".
                                    " AND PD.estado = 'Activo' AND PD.VALOR1 = ASIGNACION.NOMBRE_TAREA AND rownum = 1) AS TAREA_INFO_ADICIONAL, ".
                            "SPKG_UTILIDADES.GET_VARCHAR_CLEAN(CAST(ASIGNACION.OBSERVACION_TAREA AS VARCHAR2(3999))) AS OBSERVACION, ".
                            "DB_SOPORTE.SPKG_INFO_TAREA.F_GET_TAREA_PADRE(ASIGNACION.DETALLE_ID_RELACIONADO) AS NUMERO_TAREA_PADRE,  ".
                            "DB_SOPORTE.SPKG_INFO_TAREA.F_GET_PERMITE_FINALIZAR_INFORM(ASIGNACION.ID_DETALLE, ASIGNACION.NOMBRE_TAREA, ".
                            ":departamentoId) AS PERMITE_FINALIZAR_INFORME, ".
                            "(SELECT idh.TAREA_ID FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh, DB_SOPORTE.ADMI_TAREA ata WHERE ".
                                    "idh.ID_DETALLE_HISTORIAL = (SELECT min(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1 ".
                                     "WHERE idh1.DETALLE_ID = ASIGNACION.ID_DETALLE  AND idh1.ACCION = 'Reasignada' AND idh1.TAREA_ID IS NOT NULL) ".
                                     "AND ata.ID_TAREA = idh.TAREA_ID) AS ID_TAREA_ANTERIOR, ".

                            "(SELECT ata.NOMBRE_TAREA FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh, DB_SOPORTE.ADMI_TAREA ata  WHERE ".
                                    "idh.ID_DETALLE_HISTORIAL = (SELECT min(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1 ".
                                     "WHERE idh1.DETALLE_ID = ASIGNACION.ID_DETALLE AND idh1.ACCION = 'Reasignada' AND idh1.TAREA_ID IS NOT NULL) ".
                                        "AND ata.ID_TAREA = idh.TAREA_ID) AS NOMBRE_TAREA_ANTERIOR ";
                              
                $strQuery2 =  ", CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT a.ASIGNADO_ID FROM INFO_TAREA a ". 
                                    "JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper ON iper.PERSONA_ID = a.REF_ASIGNADO_ID ". 
                                    "JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = iper.OFICINA_ID ".
                                        "WHERE a.NUMERO_TAREA =  asig.REFERENCIA_ID ". 
                                        "AND iper.ESTADO = 'Activo' ".
                                        "AND iper.DEPARTAMENTO_ID = :departamentoId ".
                                        "AND ofi.EMPRESA_ID = :empresaCod) ".
                                    "ELSE asig.DEPARTAMENTO_ID END TAREA_DEPARTAMENTO_ID, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com ".
                                        "WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID) ELSE NULL END ID_DETALLE,".
                                "CASE WHEN asig.TIPO_ATENCION ='TAREA' THEN (SELECT idh.ID_DETALLE_HISTORIAL ".
                                        "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh WHERE idh.ID_DETALLE_HISTORIAL = ".
                                        "(SELECT max(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1, ".
                                            "DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = asig.REFERENCIA_ID ".
                                           " AND idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END ID_DETALLEHIST, ". 
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT idh.ESTADO FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh ".
                                    "WHERE idh.ID_DETALLE_HISTORIAL = (SELECT max(idh1.ID_DETALLE_HISTORIAL) FROM ".
                                     "DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1,DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = ".
                                     " asig.REFERENCIA_ID AND idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END ESTADOHIST, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT TO_CHAR(idh.FE_CREACION, 'yyyy/mm/dd hh24:mi:ss') FECHA ".
                                        "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh WHERE idh.ID_DETALLE_HISTORIAL = (SELECT ".
                                        " max(idh1.ID_DETALLE_HISTORIAL) FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL idh1,".
                                        " DB_COMUNICACION.INFO_COMUNICACION con WHERE con.ID_COMUNICACION = asig.REFERENCIA_ID AND ".
                                        " idh1.DETALLE_ID = con.DETALLE_ID)) ELSE NULL END FECHACREAHIST, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT COUNT(its.ID_SEGUIMIENTO) n_tarea_ini FROM ".
                                " DB_SOPORTE.INFO_TAREA_SEGUIMIENTO its WHERE its.DETALLE_ID = (SELECT com.DETALLE_ID FROM ".
                                " DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1) ".
                                "  AND its.OBSERVACION like '%Iniciada%' ) ELSE NULL END VECES_TAREA_INICIADA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT ip.NOMBRES||' '||ip.APELLIDOS  FROM ".
                                  "DB_COMERCIAL.INFO_PERSONA ip WHERE ip.LOGIN = asig.USR_ASIGNADO) ELSE NULL END REF_ASIGNADO_NOMBRE, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.NOMBRE_TAREA FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                    "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = ".
                                    " asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END NOMBRE_TAREA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.TAREA_ID FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                    "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                    "com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END IDTAREA, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT it.NOMBRE_PROCESO FROM DB_SOPORTE.INFO_TAREA it WHERE ".
                                   "it.DETALLE_ID = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = ".
                                   " asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END NOMBRE_PROCESO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT NVL(MAX(dh.CASO_ID), 0) AS ID_CASO FROM ".
                                        " DB_SOPORTE.INFO_DETALLE_HIPOTESIS dh,DB_SOPORTE.INFO_DETALLE id WHERE dh.ID_DETALLE_HIPOTESIS = ".
                                        " id.DETALLE_HIPOTESIS_ID AND id.ID_DETALLE = (SELECT com.DETALLE_ID FROM ".
                                        " DB_COMUNICACION.INFO_COMUNICACION com WHERE com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ".
                                        " ELSE NULL END IDCASO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT id.DETALLE_ID_RELACIONADO FROM DB_SOPORTE.INFO_DETALLE id ".
                                        "WHERE id.ID_DETALLE = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                        " com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END DETALLE_ID_RELACIONADO, ".
                                "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN (SELECT id.OBSERVACION FROM DB_SOPORTE.INFO_DETALLE id WHERE ".
                                        "id.ID_DETALLE = (SELECT com.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION com WHERE ".
                                        "com.ID_COMUNICACION = asig.REFERENCIA_ID AND rownum = 1)) ELSE NULL END OBSERVACION_TAREA ";    
            }


            $strSqlViejos  = "(SELECT ASIGNACION.* ";
            $strSqlViejos = $strSqlViejos.$strQuery1;
            $strSqlViejos.= "FROM (SELECT asig.ID_ASIGNACION_SOLICITUD, TO_CHAR(asig.FE_CREACION,'YYYY/MM/DD HH24:MI:SS') FE_CREACION, ".
                             "asig.REFERENCIA_CLIENTE, asig.TIPO_ATENCION, asig.ORIGEN,".
                             "DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_ES_PADRE(asig.ID_ASIGNACION_SOLICITUD) PADRE,".
                             "asig.TIPO_PROBLEMA, asig.REFERENCIA_ID, asig.CRITICIDAD, ".
                             "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                             "     WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                             "     THEN (SELECT DISTINCT caso.NUMERO_CASO FROM DB_COMUNICACION.INFO_CASO caso ".
                             "          WHERE caso.ID_CASO = asig.REFERENCIA_ID) ELSE NULL END NUMERO,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " asig.REFERENCIA_ID ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " NULL ".
                             " END NUMERO_TAREA,".
                             "asig.USR_ASIGNADO,". 
                             "DB_SOPORTE.SPKG_UTILIDADES.GET_VARCHAR_CLEAN(CAST(asig.DETALLE AS VARCHAR2(3999))) as DETALLE, ".
                             "asig.CAMBIO_TURNO, asig.ESTADO, ".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(asig.REFERENCIA_ID) ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_CASO(asig.REFERENCIA_ID) ".
                             " END ESTADO_TAREA,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " NULL ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_INFO_TAREAS_POR_CASO(asig.REFERENCIA_ID) ".
                             " END INFO_TAREAS,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_TAREA(asig.REFERENCIA_ID) ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(asig.REFERENCIA_ID) ".
                             " END ESTADO_CASO, ".
                             " asig.DEPARTAMENTO_ID, ".
                             " asig.FE_ULT_MOD, ".
                             "asig.TAB_VISIBLE, ".
                             " ofi.CANTON_ID, ".
                             " can.NOMBRE_CANTON,".
                             " can.REGION,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(asig.REFERENCIA_ID,'asignado') ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(".
                             "     TO_CHAR(SPKG_ASIGNACION_SOLICITUD.F_NUMERO_TAREA_POR_CASO(asig.REFERENCIA_ID)),".
                             "     'asignado') ".
                             " END ASIGNADO, ".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' ".
                             "THEN (SELECT PTO1.ID_PUNTO ".
                             " FROM DB_COMERCIAL.INFO_PUNTO PTO1 ".
                             " JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER1 ON PER1.ID_PERSONA_ROL = PTO1.PERSONA_EMPRESA_ROL_ID ".
                             " JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EROL1 ON EROL1.ID_EMPRESA_ROL = PER1.EMPRESA_ROL_ID ".
                             " WHERE LOGIN = DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID) ".
                             " AND EROL1.EMPRESA_COD = :empresaCod ".
                             " AND PTO1.ESTADO NOT IN ('Cancelado','Anulado') ".
                             ") ".
                             "WHEN asig.TIPO_ATENCION = 'CASO' ".
                             "THEN (SELECT PTO1.ID_PUNTO ".
                             " FROM DB_COMERCIAL.INFO_PUNTO PTO1 ".
                             " JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER1 ON PER1.ID_PERSONA_ROL = PTO1.PERSONA_EMPRESA_ROL_ID ".
                             " JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EROL1 ON EROL1.ID_EMPRESA_ROL = PER1.EMPRESA_ROL_ID ".
                             " WHERE LOGIN = DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID) ".
                             " AND EROL1.EMPRESA_COD = :empresaCod ".
                             " AND PTO1.ESTADO NOT IN ('Cancelado','Anulado') ".
                             ") ".
                            " END ID_AFECTADO,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID)".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID)".
                             " END AFECTADO ";
            $strSqlViejos = $strSqlViejos.$strQuery2;
            
            $strSqlViejos.= "FROM ".
                             "INFO_ASIGNACION_SOLICITUD asig ".
                             "JOIN INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = asig.OFICINA_ID ".
                             "JOIN ADMI_CANTON can ON can.ID_CANTON = ofi.CANTON_ID ".
                             "WHERE ".
                             "asig.EMPRESA_COD = :empresaCod ".
                             "AND asig.CAMBIO_TURNO <> 'S' ";
            
            $strSqlViejos.= ") ASIGNACION ".
                              "WHERE ".
                              "ASIGNACION.ESTADO NOT IN (:estadoAsignacion) ) ";

            $strWhereParams = "WHERE 1=1 ";

            if($intDepartamentoId != 0)
            {
                $strWhereParams .= " AND DEPARTAMENTO_ID = :departamentoId ";
                if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas)
                {
                    $strWhereParams .= " AND TAREA_DEPARTAMENTO_ID = :departamentoId ";
                }
                $query->setParameter('departamentoId', $intDepartamentoId);
            }
            if($intCantonId != null && $intCantonId != 0)
            {
                $strWhereParams .= " AND CANTON_ID = :cantonId ";
                $query->setParameter('cantonId', $intCantonId);
            }
            
            if($strConsulCambioTurnoAuto === 'S')
            {
                $strWhereParams .= " AND USR_ASIGNADO = :cambioTurnoUsr ";
                $query->setParameter('cambioTurnoUsr', $strUsrCambioTurno);
            }

            if ($strTabVisible !== 'todos')
            {
                if($strTabVisible !== '' && $strTabVisible !== null)
                {
                    $strWhereParams .= " AND TAB_VISIBLE = :tabVisible ";
                    $query->setParameter('tabVisible', $strTabVisible);
                }
                else
                {
                    $strWhereParams .= " AND (TAB_VISIBLE IS NULL OR TAB_VISIBLE ='' ) ";
                }
            }

            if($strEstado !== '' && $strEstado !== null)
            {
                if ($strEstado === 'Abierto')
                {
                    $arrayEstados = array('Pendiente','EnGestion','Standby');
                    $strWhereParams .= " AND ESTADO in (:estado) ";
                    $query->setParameter('estado', $arrayEstados);
                }
                elseif ($strEstado === 'Standby')
                {
                    $strWhereParams .= " AND ESTADO in (:estado) ";
                    $query->setParameter('estado', trim($strEstado));
                }
                else
                {
                    $strWhereParams .= " AND ESTADO = :estado ";
                    $query->setParameter('estado', trim($strEstado));
                    $strWhereParams .= " AND FE_ULT_MOD >= TO_TIMESTAMP(TO_CHAR(SYSDATE,'YYYY-MM-DD'),'YYYY-MM_DD') "; 
                }
            }
            
            $strSql = "SELECT * FROM (".$strSqlViejos.") ".$strWhereParams." ORDER BY FE_CREACION ASC";
            
            
            if( ($asignacionConsultaHijas === 'S') || ($asignacionProactiva === 'S'))
            {
                $booleanNewValue = false;
                $strSql =   "SELECT ".
                            "asig.ID_ASIGNACION_SOLICITUD,".
                             " TO_CHAR(asig.FE_CREACION,'YYYY/MM/DD HH24:MI:SS') FE_CREACION, ".
                             "asig.TIPO_ATENCION, asig.ORIGEN, ".
                             "DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_GET_ES_PADRE(asig.ID_ASIGNACION_SOLICITUD) PADRE,".
                             "asig.TIPO_PROBLEMA, asig.REFERENCIA_ID, asig.CRITICIDAD, ".
                             "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                             "     WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                             "     THEN (SELECT DISTINCT caso.NUMERO_CASO FROM DB_COMUNICACION.INFO_CASO caso ".
                             "          WHERE caso.ID_CASO = asig.REFERENCIA_ID) ELSE NULL END NUMERO,".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(asig.REFERENCIA_ID) ". 
                             "     WHEN asig.TIPO_ATENCION = 'CASO'  THEN SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_CASO(asig.REFERENCIA_ID) ". 
                             "END ESTADO_TAREA, ".
                             "asig.TIPO_ATENCION,".
                             "asig.ESTADO, ".
                             "asig.DEPARTAMENTO_ID, ".
                             "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(asig.REFERENCIA_ID,'asignado') ".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_DATOS_TAREA(".
                             "     TO_CHAR(SPKG_ASIGNACION_SOLICITUD.F_NUMERO_TAREA_POR_CASO(asig.REFERENCIA_ID)),".
                             "     'asignado') ".
                             " END ASIGNADO, ".
                             " CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(asig.REFERENCIA_ID)".
                             " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                             " SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(asig.REFERENCIA_ID)".
                             " END AFECTADO ".
                             " FROM ".
                             "INFO_ASIGNACION_SOLICITUD asig ".
                             "JOIN INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = asig.OFICINA_ID ".
                             "WHERE asig.EMPRESA_COD = :empresaCod ".
                             "AND asig.CAMBIO_TURNO <> 'S' ".
                             "AND asig.ESTADO NOT IN (:estadoAsignacion) ".
                             "AND asig.DEPARTAMENTO_ID = :departamentoId ".
                             "AND asig.TIPO_ATENCION <> 'CASO' "
                             ;
                
                            if($asignacionProactiva === 'S')
                            {
                                $strSql.= "AND asig.ORIGEN = 'PROACTIVOS' ".
                                          "AND asig.ASIGNACION_PADRE_ID IS NULL ";
                                $query->setParameter('estadoAsignacion', array('Eliminado','Cerrado'));
                            }
                            else
                            {
                                $strSql.= "AND asig.ASIGNACION_PADRE_ID = :padreID ";
                                $query->setParameter('estadoAsignacion', array('Eliminado'));
                            }

                            $strSql.= "ORDER BY FE_CREACION DESC ";

                $query->setParameter('padreID', $intPadreId);
                $query->setParameter('departamentoId', $intDepartamentoId);
                
            }
            else
            {
                if($strEstado !== '' && $strEstado !== null)
                {
                    $query->setParameter('estadoAsignacion', array('Eliminado'));
                }
                else
                {
                    $query->setParameter('estadoAsignacion', array('Eliminado','Cerrado'));
                }
            }

            
            $query->setParameter('empresaCod', $strCodEmpresa);

            $query->setSQL($strSql);

            $rsm->addScalarResult('ID_ASIGNACION_SOLICITUD' , 'id'               ,'integer');
            $rsm->addScalarResult('FE_CREACION'             , 'feCreacion'       ,'string');
            $rsm->addScalarResult('REFERENCIA_CLIENTE'      , 'referenciaCliente','string');
            $rsm->addScalarResult('TIPO_ATENCION'           , 'tipoAtencion'     ,'string');
            $rsm->addScalarResult('TIPO_PROBLEMA'           , 'tipoProblema'     ,'string');
            $rsm->addScalarResult('REFERENCIA_ID'           , 'referenciaId'     ,'integer');
            $rsm->addScalarResult('CRITICIDAD'              , 'criticidad'       ,'string');
            $rsm->addScalarResult('NUMERO'                  , 'numero'           ,'string');
            $rsm->addScalarResult('USR_ASIGNADO'            , 'usrAsignado'      ,'integer');
            $rsm->addScalarResult('DETALLE'                 , 'detalle'          ,'string');
            $rsm->addScalarResult('CAMBIO_TURNO'            , 'cambioTurno'      ,'float');
            $rsm->addScalarResult('ESTADO'                  , 'estado'           ,'string');
            $rsm->addScalarResult('ESTADO_TAREA'            , 'estadoTarea'      ,'string');
            $rsm->addScalarResult('INFO_TAREAS'             , 'infoTareas'       ,'string');
            $rsm->addScalarResult('ESTADO_CASO'             , 'estadoCaso'       ,'string');
            $rsm->addScalarResult('NUMERO_TAREA'            , 'numeroTarea'      ,'string');
            $rsm->addScalarResult('DEPARTAMENTO_ID'         , 'departamentoId'   ,'string');
            $rsm->addScalarResult('NOMBRE_CANTON'           , 'ciudad'           ,'string');
            $rsm->addScalarResult('ASIGNADO'                , 'asignado'         ,'string');
            $rsm->addScalarResult('AFECTADO'                , 'afectado'         ,'string');
            $rsm->addScalarResult('ID_AFECTADO'             , 'id_afectado'      ,'integer');
            $rsm->addScalarResult('ORIGEN'                  , 'origen'           ,'string');
            $rsm->addScalarResult('PADRE'                   , 'padre'            ,'string');
            $rsm->addScalarResult('TAB_VISIBLE'             , 'tabVisible'       ,'string');

            if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas && $booleanNewValue
             && $intDepartamentoId != 0)
            {
                $rsm->addScalarResult('FECHA_TAREA_CREACION'               , 'fecha_tarea_creacion'        ,'string');
                $rsm->addScalarResult('VALOR_TIEMPO_PAUSA'                 , 'valor_tiempo_pausa'          ,'string');
                $rsm->addScalarResult('FECHA_CREACION_REANUDA'             , 'fecha_creacion_reanuda'      ,'string');
                $rsm->addScalarResult('CASO_EMPRESA_COD'                   , 'caso_empresa_cod'            ,'integer');
                $rsm->addScalarResult('TAREA_INFO_ADICIONAL'               , 'tarea_info_adicional'        ,'string');
                $rsm->addScalarResult('OBSERVACION'                        , 'observacion'                 ,'string');
                $rsm->addScalarResult('NUMERO_TAREA_PADRE'                 , 'numero_tarea_padre'          ,'integer');
                $rsm->addScalarResult('PERMITE_FINALIZAR_INFORME'          , 'permite_finalizar_informe'   ,'string');
                $rsm->addScalarResult('ID_DETALLE'                         , 'idDetalle'                   ,'integer');
                $rsm->addScalarResult('ID_DETALLEHIST'                     , 'idDetalleHist'               ,'integer');
                $rsm->addScalarResult('ESTADOHIST'                         , 'estadoHist'                  ,'string');
                $rsm->addScalarResult('FECHACREAHIST'                      , 'fechaCreaHist'               ,'string');
                $rsm->addScalarResult('REF_ASIGNADO_NOMBRE'                , 'ref_asignado_nombre'         ,'string');
                $rsm->addScalarResult('NOMBRE_TAREA'                       , 'nombre_tarea'                ,'string');
                $rsm->addScalarResult('IDTAREA'                            , 'idtarea'                     ,'integer');
                $rsm->addScalarResult('NOMBRE_PROCESO'                     , 'nombreProceso'               ,'string');
                $rsm->addScalarResult('IDCASO'                             , 'idCaso'                      ,'integer');
                $rsm->addScalarResult('ID_TAREA_ANTERIOR'                  , 'id_tarea_anterior'           ,'integer');
                $rsm->addScalarResult('NOMBRE_TAREA_ANTERIOR'              , 'nombre_tarea_anterior'       ,'string');
            }

            $datos = $query->getScalarResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $datos;
    }


    /* Realiza la consulta de los seguimientos por asignación.
     * @param $idAsignacion
     * @author Miguel Angulo Sanchéz<jmangulos@telconet.ec>
     * @version 1.0 09-07-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getSeguimientoAsignacion($idAsignacion)
    {
        
        $objrsm   = new ResultSetMappingBuilder($this->_em);
        $objquery = $this->_em->createNativeQuery(null,$objrsm);        
        
        try
        {
            $strSql = "SELECT DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_SEGUIMIENTOSJSON_POR_IDASIG(:id) AS SEGUIMIENTOS FROM DUAL";
            
            $objquery->setParameter('id', $idAsignacion);
            $objquery->setSQL($strSql);
            
            $objrsm->addScalarResult('SEGUIMIENTOS', 'seguimientos', 'string');
            
            $strDatos = $objquery->getScalarResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $strDatos;
    }
    
    /**
     *
     * Actualización: Se envia nuevo parametros orden para procedimiento SPKG_ASIGNACION_SOLICITUD.P_GET_INFO_EMPLE_ASIGNACION
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 08-01-2019
     *
     * Realiza la consulta de detalles de asignaciones por empleado.
     * @param $arrayParametros
     * [
     *     strCodEmpresa        => id de la empresa
     *     strIdDepartamento    => id del departamento
     *     strIdCanton          => id del canton
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getEmpleadosConAsignacionesPorDep($arrayParametros)
    {
        $cursorEmpleados = null;
        try
        {
            $strOrden             = ( isset($arrayParametros['strOrden']) && !empty($arrayParametros['strOrden']) )
                                       ? $arrayParametros['strOrden'] : null;
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : null;
            $strIdDepartamento    = ( isset($arrayParametros['strIdDepartamento']) && !empty($arrayParametros['strIdDepartamento']) )
                                       ? $arrayParametros['strIdDepartamento'] : null;
            $strIdCanton          = ( isset($arrayParametros['strIdCanton']) && !empty($arrayParametros['strIdCanton']) )
                                       ? $arrayParametros['strIdCanton'] : null;
            $strFeCreacion        = ( isset($arrayParametros['strFeCreacion']) && !empty($arrayParametros['strFeCreacion']) )
                                       ? $arrayParametros['strFeCreacion'] : null;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;

            if(    !empty($strCodEmpresa)  && !empty($strDatabaseDsn)   && !empty($strIdDepartamento)
                                           && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) ){
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $cursorEmpleados = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_INFO_EMPLE_ASIGNACION( :strCodEmpresa, ".
                                                                                                           ":strIdDepartamento, ".
                                                                                                           ":strIdCanton, ".
                                                                                                           ":strFeCreacion, ".
                                                                                                           ":strOrden, ".
                                                                                                           ":cursorEmpleados ); END;";
                $objStmt         = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":strCodEmpresa",     $strCodEmpresa);
                oci_bind_by_name($objStmt, ":strIdDepartamento", $strIdDepartamento);
                oci_bind_by_name($objStmt, ":strIdCanton",       $strIdCanton);
                oci_bind_by_name($objStmt, ":strFeCreacion",     $strFeCreacion);
                oci_bind_by_name($objStmt, ":strOrden",          $strOrden);
                oci_bind_by_name($objStmt, ":cursorEmpleados",   $cursorEmpleados, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($cursorEmpleados);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - codEmpresa('.
                                     $strCodEmpresa.'),  idDepartamento('.$strIdDepartamento.') Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $cursorEmpleados;
    }

    /**
     * Realiza la consulta de una asignación por id.
     * @param $arrayParametros
     * [
     *     intIdAsignacion        => id de la asignación
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getAsignacionPorId($arrayParametros)
    {
        $cursorAsignaciones = null;
        try
        {
            $intIdAsignacion      = ( isset($arrayParametros['intIdAsignacion']) && !empty($arrayParametros['intIdAsignacion']) )
                                       ? $arrayParametros['intIdAsignacion'] : 0;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;

            if(    !empty($intIdAsignacion)  && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion             = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $cursorAsignaciones         = oci_new_cursor($objOciConexion);

                $strSQL                     = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_INFO_ASIGNACION_POR_ID( :intIdAsignacion, ".
                                                                                                              ":cursorAsignaciones ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":intIdAsignacion",          $intIdAsignacion);
                oci_bind_by_name($objStmt, ":cursorAsignaciones", $cursorAsignaciones, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($cursorAsignaciones);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - idAsignacion('.
                                     $intIdAsignacion.'),  Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $cursorAsignaciones;
    }


    /**
     * Actualización: Se agrega programación para enviar como parámetro el id de la tarea 
     *                y consultar los seguimientos por id tarea.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 21-02-2019
     * 
     * Realiza la consulta de los seguimientos según los parametros.
     * @param $arrayParametros
     * [
     *     intIdAsignación      => id de asignación
     *     strUsrCreacion       => usr de creación de la asignación
     *     strFeCreacion        => fecha de creación de la asignación
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 15-08-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getSeguimientos($arrayParametros)
    {
        $cursorSeguimientos = null;
        try
        {
            $intIdAsignacion      = ( isset($arrayParametros['intIdAsignacion']) && !empty($arrayParametros['intIdAsignacion']) )
                                       ? $arrayParametros['intIdAsignacion'] : 0;
            $intIdTarea           = ( isset($arrayParametros['intIdTarea']) && !empty($arrayParametros['intIdTarea']) )
                                       ? $arrayParametros['intIdTarea'] : 0;
            $strUsrCreacion       = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                       ? $arrayParametros['strUsrCreacion'] : null;
            $strFeCreacion        = ( isset($arrayParametros['strFeCreacion']) && !empty($arrayParametros['strFeCreacion']) )
                                       ? $arrayParametros['strFeCreacion'] : null;
            $strTipo              = ( isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']) )
                                       ? $arrayParametros['strTipo'] : null;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;

            if( !empty($strTipo) && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion     = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $cursorSeguimientos = oci_new_cursor($objOciConexion);

                $strSQL             = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_SEGUIMIENTOS( :idAsignacion, ".
                                                                                                     ":idTarea,".
                                                                                                     ":usrCreacion,".
                                                                                                     ":feCreacion,".
                                                                                                     ":tipo,".
                                                                                                     ":cursorSeguimientos ); END;";
                $objStmt            = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":idAsignacion", $intIdAsignacion);
                oci_bind_by_name($objStmt, ":idTarea", $intIdTarea);
                oci_bind_by_name($objStmt, ":usrCreacion", $strUsrCreacion);
                oci_bind_by_name($objStmt, ":feCreacion", $strFeCreacion);
                oci_bind_by_name($objStmt, ":tipo", $strTipo);
                oci_bind_by_name($objStmt, ":cursorSeguimientos", $cursorSeguimientos, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($cursorSeguimientos);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - idAsignacion('.
                                     $intIdAsignacion.'), usrCreacion('.$strUsrCreacion.'), tipo('.$strTipo.'), Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $cursorSeguimientos;
    }

    /**
     * Realiza la consulta de los seguimientos que fueron asignados para gestion.
     * @param $arrayParametros
     * [
     *     strUsrGestion        => usuario asignado para gestion de seguimiento
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 17-08-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getSeguimientosPendientesPorUsr($arrayParametros)
    {
        $cursorSeguimientos = null;
        try
        {
            $strUsrGestion        = ( isset($arrayParametros['strUsrGestion']) && !empty($arrayParametros['strUsrGestion']) )
                                       ? $arrayParametros['strUsrGestion'] : "";
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if(    !empty($strUsrGestion)  && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion     = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $cursorSeguimientos = oci_new_cursor($objOciConexion);

                $strSQL             = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_SEGUIMIENTOS_PEND_USR( :usrGestion, ".
                                                                                                              ":cursorSeguimientos ); END;";
                $objStmt            = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":usrGestion", $strUsrGestion);
                oci_bind_by_name($objStmt, ":cursorSeguimientos", $cursorSeguimientos, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($cursorSeguimientos);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - idAsignacion('.
                                     $strUsrGestion.'),  Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $cursorSeguimientos;
    }

    /**
     * Realiza la consulta de los seguimientos por id de asignación.
     * @param $arrayParametros
     * [
     *     intIdAsignacion      => id de la asignación
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getEstadosPorTarea($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $intIdTarea           = ( isset($arrayParametros['intIdTarea']) && !empty($arrayParametros['intIdTarea']) )
                                       ? $arrayParametros['intIdTarea'] : 0;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if(    !empty($intIdTarea)  && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion             = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $strEstadoTarea             = "";
                $strEstadoCaso              = "";
                $strSQL                     = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_ESTADOS_POR_TAREA( :intIdTarea, ".
                                                                                                              ":strEstadoTarea, ".
                                                                                                              ":strEstadoCaso  ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":intIdTarea",     $intIdTarea);
                oci_bind_by_name($objStmt, ":strEstadoTarea", $strEstadoTarea, 16, SQLT_CHR);
                oci_bind_by_name($objStmt, ":strEstadoCaso",  $strEstadoCaso, 16, SQLT_CHR);
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                //
                $arrayResultado['strEstadoTarea'] = $strEstadoTarea;
                $arrayResultado['strEstadoCaso'] = $strEstadoCaso;

                // Free resources.
                oci_free_statement($objStmt);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - idAsignacion('.
                                     $intIdTarea.'),  Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayResultado;
    }


    /**
     * Realiza la consulta de los seguimientos por id de asignación.
     * @param $arrayParametros
     * [
     *     intIdAsignacion      => id de la asignación
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getEstadosPorCaso($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $intIdCaso            = ( isset($arrayParametros['intIdCaso']) && !empty($arrayParametros['intIdCaso']) )
                                       ? $arrayParametros['intIdCaso'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if(    !empty($intIdCaso)  && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion             = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $strEstadoTarea             = "";
                $strEstadoCaso              = "";
                $strSQL                     = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_ESTADOS_POR_CASO( :intIdCaso, ".
                                                                                                                 ":strEstadoTarea, ".
                                                                                                                 ":strEstadoCaso  ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":intIdCaso",     $intIdCaso);
                oci_bind_by_name($objStmt, ":strEstadoTarea", $strEstadoTarea, 16, SQLT_CHR);
                oci_bind_by_name($objStmt, ":strEstadoCaso",  $strEstadoCaso, 16, SQLT_CHR);
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                //
                $arrayResultado['strEstadoTarea'] = $strEstadoTarea;
                $arrayResultado['strEstadoCaso'] = $strEstadoCaso;
                // Free resources.
                oci_free_statement($objStmt);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - idCaso('.
                                     $intIdCaso.'),  Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayResultado;
    }


    /**
     * Realiza la consulta de detalles de historial de asignaciones segun los criterios recibidos por parametros.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     intIdAsignacion         => id de la asignación
     *     strTipo                 => tipo de historial de asignación
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 31-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getNumeroPorIdAsignacion($arrParametros){

        $intIdAsignacion = $arrParametros["intIdAsignacion"];
        $rsm  = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null,$rsm);

        $select = "SELECT asig.REFERENCIA_ID, ".
                  "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                  " WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                  " THEN TO_CHAR(SPKG_ASIGNACION_SOLICITUD.F_NUMERO_TAREA_POR_CASO(asig.REFERENCIA_ID)) ELSE NULL END NUMERO_TAREA, ".
                  "CASE WHEN (asig.TIPO_ATENCION = 'TAREA' AND asig.REFERENCIA_ID IS NOT NULL) THEN asig.REFERENCIA_ID ".
                  " WHEN (asig.TIPO_ATENCION = 'CASO' AND asig.REFERENCIA_ID IS NOT NULL) ".
                  " THEN (SELECT DISTINCT caso.NUMERO_CASO FROM DB_COMUNICACION.INFO_CASO caso ".
                  " WHERE caso.ID_CASO = asig.REFERENCIA_ID) ELSE NULL END NUMERO, ".
                  "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                  " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(asig.REFERENCIA_ID) ".
                  " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                  " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_CASO(asig.REFERENCIA_ID) ".
                  " END ESTADO_TAREA,".
                  "CASE WHEN asig.TIPO_ATENCION = 'TAREA' THEN ".
                  " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_TAREA(asig.REFERENCIA_ID) ".
                  " WHEN asig.TIPO_ATENCION = 'CASO' THEN ".
                  " SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(asig.REFERENCIA_ID) ".
                  " END ESTADO_CASO ";
        $sql    = "FROM
                    INFO_ASIGNACION_SOLICITUD asig ";
       $where   = "WHERE
                    asig.ID_ASIGNACION_SOLICITUD = :idAsignacion
                    AND asig.ESTADO <> :estado
                  ";
        $sql    = $select.$sql.$where;

        $query->setParameter('estado', 'Eliminado');
        $query->setParameter('idAsignacion', $intIdAsignacion);

        $query->setSQL($sql);

        $rsm->addScalarResult('REFERENCIA_ID', 'referenciaId','integer');
        $rsm->addScalarResult('NUMERO', 'numero','integer');
        $rsm->addScalarResult('NUMERO_TAREA', 'numeroTarea','integer');
        $rsm->addScalarResult('ESTADO_CASO', 'estadoCaso','integer');
        $rsm->addScalarResult('ESTADO_TAREA', 'estadoTarea','integer');

        $datos = $query->getScalarResult();
        return $datos;
    }

    /**
     * Realiza la consulta de detalles de historial de asignaciones segun los criterios recibidos por parametros.
     * @param $arrayParametros
     * [
     *     start                   => Valor de inicio de cantidad de registros
     *     limit                   => Valor limite de cantidad de registros
     *     intIdAsignacion         => id de la asignación
     *     strTipo                 => tipo de historial de asignación
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 31-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getDetalleAHistorialAsignacion($arrParametros){

        $intStart        = $arrParametros["start"];
        $intLimit        = $arrParametros["limit"];
        $intIdAsignacion = $arrParametros["intIdAsignacion"];
        $strTipo         = $arrParametros["strTipo"];
        $rsm  = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null,$rsm);
        $where           = "";
        $select          = "SELECT asigh.ID_ASIGNACION_SOLICITUD_HIST, ".
                           "TO_CHAR(asigh.FE_CREACION,'YYYY/MM/DD HH24:MI:SS') FE_CREACION, ".
                           "asigh.USR_ASIGNADO, asigh.TIPO ";
        $strOrderBy      = "ORDER BY asigh.FE_CREACION DESC";
        if($strTipo !== "" && $strTipo !== "TODOS")
        {
            $where .= " AND asig.TIPO = :tipo ";
            $query->setParameter('tipo', $strTipo);
        }

        $sql   = "FROM
                    INFO_ASIGNACION_SOLICITUD_HIST asigh
                  WHERE
                    asigh.ASIGNACION_SOLICITUD_ID = :idAsignacion
                    AND asigh.ESTADO <> :estado
                  ";
        $sql   = $select.$sql.$where.$strOrderBy;

        $query->setParameter('estado', 'Eliminado');
        $query->setParameter('idAsignacion', $intIdAsignacion);

        $query->setSQL($sql);

        $rsm->addScalarResult('ID_ASIGNACION_SOLICITUD_HIST', 'id','integer');
        $rsm->addScalarResult('FE_CREACION', 'feCreacion','string');
        $rsm->addScalarResult('USR_ASIGNADO', 'usrAsignado', 'string');
        $rsm->addScalarResult('TIPO', 'tipo','string');

        $datos = $query->getScalarResult();
        return $datos;
    }


    /**
     * Actualización: Se agrega programación para consultar asignaciones totalizado por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 28-05-2020
     * 
     * Actualización: Se agrega que se pueda filtre asignaciones por id_canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 15-01-2019
     *
     * Actualización: Ahora se usa consulta con procedmiento P_GET_ASIGNACIONES_TOTALIZADAS
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 05-12-2018
     *
     * Actualización: Se agrega que se pueda consultar por rango de fecha de creación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 26-09-2018
     *
     * Realiza la consulta de asignaciones totalizado por usuario
     * @param $arrParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     intIdCanton             => id del canton
     *     strFeCreacionIni        => fecha de creación inicio
     *     strFeCreacionFin        => fecha de creación fin
     *     strCodEmpresa           => id de la empresa
     *     strEstado               => estado de la asignación
     *     strTotalizadoPor        => criterio de totalizado
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbSoporte        => usuario de esquema DbSoporte
     *     strPasswordDbSoporte    => password de esquema DbSoporte
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-09-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getAsignacionesTotalizado($arrayParametros)
    {
        $objCursorTotalizado = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : '';
            $strEstado            = ( isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']) )
                                       ? $arrayParametros['strEstado'] : '';
            $strFeCreacionIni     = ( isset($arrayParametros['strFeCreacionIni']) && !empty($arrayParametros['strFeCreacionIni']) )
                                       ? $arrayParametros['strFeCreacionIni'] : '';
            $strFeCreacionFin     = ( isset($arrayParametros['strFeCreacionFin']) && !empty($arrayParametros['strFeCreacionFin']) )
                                       ? $arrayParametros['strFeCreacionFin'] : '';
            $strTotalizadoPor     = ( isset($arrayParametros['strTotalizadoPor']) && !empty($arrayParametros['strTotalizadoPor']) )
                                       ? $arrayParametros['strTotalizadoPor'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if( !empty($strCodEmpresa)     && !empty($intIdDepartamento) &&
                !empty($strFeCreacionIni)  && !empty($strFeCreacionFin)  &&
                !empty($strTotalizadoPor)  && !empty($strUserDbSoporte)  && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion      = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $objCursorTotalizado = oci_new_cursor($objOciConexion);

                $strSQL           = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_ASIGNACIONES_TOTALIZADAS( :strCodEmpresa, ".
                                                                                                               ":intIdDepartamento, ".
                                                                                                               ":intIdCanton, ".
                                                                                                               ":strEstado, ".
                                                                                                               ":strFeCreacionIni, ".
                                                                                                               ":strFeCreacionFin, ".
                                                                                                               ":strTotalizadoPor, ".
                                                                                                               ":cursorTotalizado  ); END;";
                $objStmt          = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":strCodEmpresa"    ,  $strCodEmpresa);
                oci_bind_by_name($objStmt, ":intIdDepartamento",  $intIdDepartamento);
                oci_bind_by_name($objStmt, ":intIdCanton"      ,  $intIdCanton);
                oci_bind_by_name($objStmt, ":strEstado"        ,  $strEstado);
                oci_bind_by_name($objStmt, ":strFeCreacionIni" ,  $strFeCreacionIni);
                oci_bind_by_name($objStmt, ":strFeCreacionFin" ,  $strFeCreacionFin);
                oci_bind_by_name($objStmt, ":strTotalizadoPor" ,  $strTotalizadoPor);
                oci_bind_by_name($objStmt, ":cursorTotalizado" ,  $objCursorTotalizado, -1, OCI_B_CURSOR);
                //
                oci_execute($objStmt);
                oci_execute($objCursorTotalizado);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.
                                     $strCodEmpresa.'), intIdCanton('.$intIdCanton.'), strFeCreacionIni('.$strFeCreacionIni.'), '.
                                     ', intIdDepartamento('.$intIdDepartamento.') strFeCreacionFin('.$strFeCreacionFin.'), '.
                                     ' strTotalizadoPor('.$strTotalizadoPor.')'.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursorTotalizado;
    }

    /**
     *
     * Realiza la consulta de asignaciones totalizado por estado
     * @param $arrayParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     intIdCanton             => id del canton
     *     strCodEmpresa           => id de la empresa
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbSoporte        => usuario de esquema DbSoporte
     *     strPasswordDbSoporte    => password de esquema DbSoporte
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 07-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getAsignacionesTotalizadoPorEstado($arrayParametros)
    {
        $objCursorTotalizado = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if( !empty($strCodEmpresa)     && !empty($intIdDepartamento) &&
                !empty($strUserDbSoporte)  && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion      = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $objCursorTotalizado = oci_new_cursor($objOciConexion);

                $strSQL           = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_ASIGNACIONES_TOT_ESTADO(  :strCodEmpresa, ".
                                                                                                               ":intIdDepartamento, ".
                                                                                                               ":intIdCanton, ".
                                                                                                               ":cursorTotalizado  ); END;";
                $objStmt          = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":strCodEmpresa"    ,  $strCodEmpresa);
                oci_bind_by_name($objStmt, ":intIdDepartamento",  $intIdDepartamento);
                oci_bind_by_name($objStmt, ":intIdCanton"      ,  $intIdCanton);
                oci_bind_by_name($objStmt, ":cursorTotalizado" ,  $objCursorTotalizado, -1, OCI_B_CURSOR);
                //
                oci_execute($objStmt);
                oci_execute($objCursorTotalizado);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.
                                     $strCodEmpresa.'), intIdCanton('.$intIdCanton.'), '.
                                     ', intIdDepartamento('.$intIdDepartamento.'), '.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursorTotalizado;
    }

    /**
     * Actualización: Se agrega programación para consultar seguimientos de asignaciones por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 28-05-2020
     * 
     * Actualización: Se agrega que se pueda filtre seguimientos por id_canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 15-01-2019
     *
     * Actualización: Se agrega que se pueda consultar por rango de fecha de creación de seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 26-09-2018
     *
     * Realiza la consulta de seguimientos totalizado por usuario
     * @param $arrParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     intIdCanton             => id del canton
     *     objFeCreacion           => fecha de creación
     *     strCodEmpresa           => código de la empresa
     *     strTotalizadoPor        => criterio de totalizado
     *     strUsrCreacion          => usuario creación del seguimiento
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-09-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getSeguimientosTotalizado($arrParametros){

        $intIdDepartamento = $arrParametros["intIdDepartamento"];
        $intIdCanton       = $arrParametros['intIdCanton'];
        $strEstado         = $arrParametros['strEstado'];
        $strFeCreacionIni  = $arrParametros["strFeCreacionIni"];
        $strFeCreacionFin  = $arrParametros["strFeCreacionFin"];
        $strCodEmpresa     = $arrParametros["strCodEmpresa"];
        $strTotalizadoPor  = $arrParametros["strTotalizadoPor"];
        $strUsrCreacion    = $arrParametros["strUsrCreacion"];
        $rsm               = new ResultSetMappingBuilder($this->_em);
        $query             = $this->_em->createNativeQuery(null,$rsm);
        $where             = "";
        $strWhereUsr       = "";
        $select            = "SELECT seg.USR_CREACION ";
        if($strFeCreacionIni != "")
        {
            $where .= " AND (TO_CHAR(seg.FE_CREACION,'YYYY/MM/DD') >= :feCreacionIni ".
                      "AND TO_CHAR(seg.FE_CREACION,'YYYY/MM/DD') <= :feCreacionFin ) ";
            $query->setParameter('feCreacionIni', $strFeCreacionIni);
            $query->setParameter('feCreacionFin', $strFeCreacionFin);
        }
        if($intIdDepartamento != 0)
        {
            $where       .= " AND asig.DEPARTAMENTO_ID = :departamentoId ";
            $strWhereUsr .= " AND per.DEPARTAMENTO_ID  = :departamentoUsrId ";
            $query->setParameter('departamentoId'   , $intIdDepartamento);
            $query->setParameter('departamentoUsrId', $intIdDepartamento);
        }
        if($intIdCanton != null && $intIdCanton != 0)
        {
            $where       .= " AND ofi.CANTON_ID  = :cantonId ";
            $strWhereUsr .= " AND ofi1.CANTON_ID = :cantonUsrId ";
            $query->setParameter('cantonId'   , $intIdCanton);
            $query->setParameter('cantonUsrId', $intIdCanton);
        }
        if($strEstado != null && strtoupper($strEstado) != 'TODOS')
        {
            if ($strEstado == 'Abierto')
            {
                $where .= " AND asig.ESTADO in (:estado) ";
                $query->setParameter('estado', array ('Pendiente','EnGestion','Standby'));
            }
            else
            {
                $where .= " AND asig.ESTADO = :estado ";
                $query->setParameter('estado', $strEstado);    
            }
        }
        else
        {
            $where .= " AND asig.ESTADO <> :estado ";
            $query->setParameter('estado', 'Eliminado');
        }
        
        if($strUsrCreacion != "")
        {
            $where .= " AND seg.USR_CREACION = :usrAsignado ";
            $query->setParameter('usrCreacion', $strUsrCreacion);
        }

        $sql   = "FROM
                    INFO_ASIGNACION_SOLICITUD asig
                    JOIN INFO_SEGUIMIENTO_ASIGNACION seg ON seg.ASIGNACION_SOLICITUD_ID = asig.ID_ASIGNACION_SOLICITUD
                    JOIN INFO_OFICINA_GRUPO ofi ON ofi.ID_OFICINA = asig.OFICINA_ID
                  WHERE
                    asig.EMPRESA_COD = :empresaCod
                    AND seg.ESTADO = :estadoSeg
                  ";
        if ($strTotalizadoPor === "USUARIO")
        {
            $sql   = "SELECT USR_CREACION, COUNT(*) CANTIDAD FROM (".$select.$sql.$where.")SEGUIMIENTO ";
            $sql  .= " WHERE".
                     " (".
                     "  SELECT COUNT(*) FROM INFO_PERSONA p ".
                     "    JOIN INFO_PERSONA_EMPRESA_ROL per ON per.PERSONA_ID = p.ID_PERSONA ".
                     "    JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO ofi1 ON ofi1.ID_OFICINA = per.OFICINA_ID ".
                     "  WHERE  ".
                     "    p.LOGIN                 = SEGUIMIENTO.USR_CREACION".
                     $strWhereUsr.
                     "    AND ofi1.EMPRESA_ID     = :empresaUsrCod ".
                     " ) > 0 "
                     ;
            $sql  .= " GROUP BY USR_CREACION ORDER BY USR_CREACION ASC";
        }
        $query->setParameter('estadoSeg', 'Activo');
        $query->setParameter('empresaCod', $strCodEmpresa);
        $query->setParameter('empresaUsrCod', $strCodEmpresa);
        $query->setSQL($sql);
        if ($strTotalizadoPor === "USUARIO")
        {
            $rsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        }
        $rsm->addScalarResult('CANTIDAD', 'cantidad','string');
        $datos = $query->getScalarResult();
        return $datos;
    }

    /**
     * Realiza la consulta de login del cliente afectado
     * @param $arrParametros
     * [
     *     objContainer        => container del controller
     *     strTipoAtencion     => tipo de atención
     *     strEstado           => estado de la asignación
     *     intReferenciaId     => id de refencia del caso o tarea
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 26-09-2018
     * @since 1.0
     * @return Array arrRespuesta
     */
    public function obtenerAfectado($arrParametros)
    {
        $strLoginAfectado = "";
        $urlVer           = "";
        $container        = $arrParametros["container"];
        $strTipoAtencion  = $arrParametros["tipoAtencion"];
        $strEstado        = $arrParametros["estado"];
        $strReferenciaId  = $arrParametros["referenciaId"];

        $arrRespuesta     = array();

        if($strTipoAtencion === 'CASO' && $strEstado === 'EnGestion')
        {
            $urlVer = $container->get('router')->generate('infocaso_show', array('id' =>$strReferenciaId));

            //consulta el login del afectado del caso
            $objJsonAfectadosCaso = $this->_em->getRepository('schemaBundle:InfoCaso')
                                              ->generarJsonAfectadosTotalXCaso($strReferenciaId,0,999999);
            $objRespAfectadosCaso = json_decode($objJsonAfectadosCaso);
            $arrAfectadosCaso     = $objRespAfectadosCaso->encontrados;

            for ($i = 0; $i<count($arrAfectadosCaso);$i++)
            {
                $objAfectado = $arrAfectadosCaso[$i];
                if ($objAfectado->tipo_afectado === "Servicio")
                {
                    $objServicio = $this->_em->getRepository("schemaBundle:InfoServicio")->findOneById($objAfectado->id_afectado);
                    $strLoginAfectado = $objServicio->getPuntoId()->getLogin();
                }
                elseif($objAfectado->tipo_afectado === "Cliente")
                {
                    $strLoginAfectado = $objAfectado->nombre_afectado;
                }
                if (!empty($strLoginAfectado))
                {
                  break;
                }
            }

        }
        elseif($strTipoAtencion === 'TAREA' && $strEstado === 'EnGestion')
        {
            $urlVer = $container->get('router')->generate('callactivity_show', array('id' =>$strReferenciaId));
            //busca el detalle de la tarea
            $objInfoComunicacion = $this->_em->getRepository("schemaBundle:InfoComunicacion")->findOneById($strReferenciaId);
            if (is_object($objInfoComunicacion))
            {
                $ClientesAfectados = $this->_em->getRepository("schemaBundle:InfoDetalle")
                                               ->getRegistrosAfectadosTotal($objInfoComunicacion->getDetalleId(), 'Cliente', 'Data');
                $string_clientes_1 = "";
                if($ClientesAfectados && count($ClientesAfectados) > 0)
                {
                    $arrayClientes = false;
                    foreach($ClientesAfectados as $clientAfect)
                    {
                        $arrayClientes[] = $clientAfect["afectadoNombre"];
                    }
                    $string_clientes_1 = implode(",", $arrayClientes);
                    $strLoginAfectado   = "" . $string_clientes_1 . "";
                }
                $arrayParametroCliente = array(
                                                'intIdDetalle'   =>  $objInfoComunicacion->getDetalleId()
                                              );
                $objClienteAfectado = $this->_em->getRepository("schemaBundle:InfoDetalle")
                                                ->getClienteAfectadoTarea($arrayParametroCliente);
                if(is_object($objClienteAfectado))
                {
                    $strLoginAfectado = $objClienteAfectado->getLogin();
                }
            }
        }
        $arrRespuesta['strLoginAfectado'] = $strLoginAfectado;
        $arrRespuesta['strUrlVer']        = $urlVer;
        return $arrRespuesta;
    }


    /**
     *
     * Actualización: Se envia parametros 'VALOR' para función SPKG_ASIGNACION_SOLICITUD.F_GET_CARACTERISTICA_EMPLEADO
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 08-01-2019
     *
     * Realiza la consulta de estado de conexión y de extensión del empleado
     * @param $arrayParametros
     * [
     *     intPersonaRolId => id de la persona empresa rol
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-12-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function getEstadoConexionyExtension($arrayParametros)
    {
        $intPersonaRolId            = $arrayParametros["intPersonaRolId"];
        $rsm                        = new ResultSetMappingBuilder($this->_em);
        $query                      = $this->_em->createNativeQuery(null,$rsm);
        $strSql                     = "SELECT ".
                                      "    SPKG_ASIGNACION_SOLICITUD.F_GET_CARACTERISTICA_EMPLEADO(".
                                      "    :idPersonaRol,'ESTADO CONEXION MODULO ASIGNACIONES','VALOR') AS ESTADO_CONEXION, ".
                                      "    SPKG_ASIGNACION_SOLICITUD.F_GET_CARACTERISTICA_EMPLEADO(".
                                      "    :idPersonaRol,'EXTENSION USUARIO','VALOR') AS EXTENSION_USUARIO ".
                                      " FROM DUAL";

        $query->setParameter('idPersonaRol', $intPersonaRolId);
        //Costo Query 2
        $query->setSQL($strSql);
        $rsm->addScalarResult('ESTADO_CONEXION', 'estadoConexion','string');
        $rsm->addScalarResult('EXTENSION_USUARIO', 'extensionUsuario','string');
        $datos = $query->getScalarResult();
        return $datos;
    }


    /**
     * Realiza la consulta de los seguimientos por id de asignación.
     * @param $arrayParametros
     * [
     *     intIdAsignacion      => id de la asignación
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getReporteAsignacionesPendientes($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intDepartamentoId    = ( isset($arrayParametros['intDepartamentoId']) && !empty($arrayParametros['intDepartamentoId']) )
                                        ? $arrayParametros['intDepartamentoId'] : '';
            $intCantonId          = ( isset($arrayParametros['intCantonId']) && !empty($arrayParametros['intCantonId']) )
                                        ? $arrayParametros['intCantonId'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if(    !empty($strCodEmpresa)       && !empty($intDepartamentoId) 
                   && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion             = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $strRespuesta               = "";
                $strSQL                     = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_REPORTE_ASIGNACIONES_PEND( :strCodEmpresa, ".
                                                                                                                      ":intDepartamentoId, ".
                                                                                                                      ":intCantonId, ".
                                                                                                                      ":strRespuesta  ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":strCodEmpresa",     $strCodEmpresa);
                oci_bind_by_name($objStmt, ":intDepartamentoId", $intDepartamentoId);
                oci_bind_by_name($objStmt, ":intCantonId",       $intCantonId);
                oci_bind_by_name($objStmt, ":strRespuesta",      $strRespuesta, 16, SQLT_CHR);
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                //
                $arrayResultado['strRespuesta'] = $strRespuesta;
                // Free resources.
                oci_free_statement($objStmt);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información.'.
                                     ' strCodEmpresa('.$strCodEmpresa.'), '.' intDepartamentoId('.$intDepartamentoId.'), '.
                                     '  Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayResultado;
    }

    /**
     * Realiza la consulta del último agente asignado
     * @param $arrayParametros
     * [
     *     intIdDepartamento => id del departamento a consultar
     *     intIdCanton       => id del cantón a consultar
     *     strCodEmpresa     => código de la empresa a consultar
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20-02-2019
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getUltimoAgenteAsignado($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : '';
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if(    !empty($intIdDepartamento)  && !empty($strCodEmpresa) && 
                   !empty($strUserDbSoporte)   && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion             = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $strUltimoAsignado          = "";
                $strSQL                     = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_ULTIMO_AGENTE_ASIGNADO( :strCodEmpresa, ".
                                                                                                                       ":intDepartamentoId, ".
                                                                                                                       ":intCantonId, ".
                                                                                                                       ":strUltimoAsignado ".
                                                                                                                       "); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":strCodEmpresa"     , $strCodEmpresa);
                oci_bind_by_name($objStmt, ":intDepartamentoId" , $intIdDepartamento);
                oci_bind_by_name($objStmt, ":intCantonId"       , $intIdCanton);
                oci_bind_by_name($objStmt, ":strUltimoAsignado" , $strUltimoAsignado, 16, SQLT_CHR);
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                $arrayResultado['strRespuesta'] = $strUltimoAsignado;
                oci_free_statement($objStmt);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. -'.
                                     ' idCaso('.$strCodEmpresa.'), '.' idCaso('.$strCodEmpresa.'), '.' idCaso('.$strCodEmpresa.'), '.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayResultado;
    }

    /**
     * * Actualización: Se añade parámentros para el nuevo perfil de verNuevosCamposTareas en agente
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.1 24-01-2022
     * 
     * Realiza la consulta de tareas pendientes por departamento
     * @param $arrayParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     intIdOficina             => id de la oficina
     *     strCodEmpresa           => id de la empresa
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbSoporte        => usuario de esquema DbSoporte
     *     strPasswordDbSoporte    => password de esquema DbSoporte
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getTareasPendientesDepartamento($arrayParametros)
    {
        $objCursorTareas = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdOficina         = ( isset($arrayParametros['intIdOficina']) && !empty($arrayParametros['intIdOficina']) )
                                       ? $arrayParametros['intIdOficina'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            $strPermiteVerNuevosCamposTareas = ( isset($arrayParametros['permiteVerNuevosCamposTareas']) && 
                                                !empty($arrayParametros['permiteVerNuevosCamposTareas']))? 'S' : 'N';
            $strFechaInicio        = ( isset($arrayParametros['fechaInicio']) && !empty($arrayParametros['fechaInicio']) )
                                                ? $arrayParametros['fechaInicio'] : '';
            $strFechaFin        = ( isset($arrayParametros['fechaFin']) && !empty($arrayParametros['fechaFin']) )
                                                ? $arrayParametros['fechaFin'] : '';
            if( !empty($strCodEmpresa)     && !empty($intIdDepartamento) && !empty($intIdOficina) &&
                !empty($strUserDbSoporte)  && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $objCursorTareas = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_TAREAS_DEPARTAMENTO( ".
                                                                                                            ":intIdDepartamento, ".
                                                                                                            ":strCodEmpresa, ".
                                                                                                            ":cursorTareas, ".
                                                                                                            ":strPermiteVerCamposTareas, ".
                                                                                                            ":strFechaInicio, ".
                                                                                                            ":strFechaFin  ); END;";
                $objStmt          = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":intIdDepartamento",  $intIdDepartamento);
                oci_bind_by_name($objStmt, ":strCodEmpresa"    ,  $strCodEmpresa);                
                oci_bind_by_name($objStmt, ":cursorTareas"     ,  $objCursorTareas, -1, OCI_B_CURSOR);
                oci_bind_by_name($objStmt, ":strPermiteVerCamposTareas"    ,  $strPermiteVerNuevosCamposTareas);
                oci_bind_by_name($objStmt, ":strFechaInicio"    ,  $strFechaInicio);
                oci_bind_by_name($objStmt, ":strFechaFin"    ,  $strFechaFin);
                oci_execute($objStmt);
                oci_execute($objCursorTareas);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.
                                     $strCodEmpresa.'), intIdOficina('.$intIdOficina.'), '.', intIdDepartamento('.$intIdDepartamento.'), '.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursorTareas;
    }

    /**
     * Actualización: Se agrega programación para consultar asignaciones totalizado por estado.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 28-05-2020
     * 
     * Realiza la consulta de tareas pendientes por departamento
     * @param $arrayParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     intIdOficina            => id de la oficina
     *     strCodEmpresa           => id de la empresa
     *     strEstado               => estado de la asignación
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbSoporte        => usuario de esquema DbSoporte
     *     strPasswordDbSoporte    => password de esquema DbSoporte
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getTopLogins($arrayParametros)
    {
        $objCursorLogins = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : '';
            $strEstado            = ( isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']) )
                                       ? $arrayParametros['strEstado'] : '';
            $strFechaIni          = ( isset($arrayParametros['strFechaIni']) && !empty($arrayParametros['strFechaIni']) )
                                       ? $arrayParametros['strFechaIni'] : '';
            $strFechaFin          = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                       ? $arrayParametros['strFechaFin'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if( !empty($strCodEmpresa)     && !empty($intIdDepartamento) && !empty($strUserDbSoporte)  && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $objCursorLogins = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_TOP_LOGINS( ".
                                                                                                    ":strCodEmpresa, ".
                                                                                                    ":intIdDepartamento, ".
                                                                                                    ":intIdCanton, ".
                                                                                                    ":strEstado, ".
                                                                                                    ":strFechaIni, ".
                                                                                                    ":strFechaFin, ".
                                                                                                    ":cursorLogins  ); END;";
                $objStmt          = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":intIdDepartamento",  $intIdDepartamento);
                oci_bind_by_name($objStmt, ":intIdCanton"      ,  $intIdCanton);
                oci_bind_by_name($objStmt, ":strEstado"        ,  $strEstado);
                oci_bind_by_name($objStmt, ":strCodEmpresa"    ,  $strCodEmpresa);
                oci_bind_by_name($objStmt, ":strFechaIni"      ,  $strFechaIni);
                oci_bind_by_name($objStmt, ":strFechaFin"      ,  $strFechaFin);
                oci_bind_by_name($objStmt, ":cursorLogins"     ,  $objCursorLogins, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($objCursorLogins);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.$strCodEmpresa.'),'.
                                     ' intIdDepartamento('.$intIdDepartamento.'), '.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursorLogins;
    }
    /**
     *
     * Realiza la consulta de datos de la tarea o del caso.
     * @param $arrayParametros
     * [
     *     intNumeroTarea  => número de la tarea.
     *     intIdCaso       => id del caso.
     *     strTipoAtencion => tipo de atención.
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 02-04-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getDatosTareaCaso($arrayParametros)
    {
        $intNumeroTarea  = $arrayParametros["intNumeroTarea"];
        $intIdCaso       = $arrayParametros["intIdCaso"];
        $strTipoAtencion = $arrayParametros["strTipoAtencion"];
        try
        {
            $objRsm          = new ResultSetMappingBuilder($this->_em);
            $objQuery        = $this->_em->createNativeQuery(null,$objRsm);
            $strSql          = "SELECT NULL ESTADO, NULL AFECTADO, '[]' AS ASIGNACIONES FROM DUAL";
            if ($strTipoAtencion === 'TAREA')
            {
                $strSql = "SELECT ESTADO, AFECTADO, '['||ASIGNACIONES||']' AS ASIGNACIONES ".
                          "FROM ".
                          "( ".
                          " SELECT ". 
                          "   SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(:numTarea) AS ESTADO,". 
                          "   SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_TAREA(:numTarea) AS AFECTADO,".
                          "   (SELECT LISTAGG('{'||".
                          "                     'IDASIG:'||asig.ID_ASIGNACION_SOLICITUD||','||".
                          "                     'TPROB:'||asig.TIPO_PROBLEMA||".
                          "                   '}', ', ') WITHIN GROUP (ORDER BY FE_CREACION DESC) ".
                          "    FROM DB_SOPORTE.INFO_ASIGNACION_SOLICITUD asig ".
                          "    WHERE ".
                          "      asig.REFERENCIA_ID = :numTarea ".
                          "      AND asig.ESTADO    = :estado ".
                          "   ) AS ASIGNACIONES ".
                          " FROM DUAL".
                          ")";
                $objQuery->setParameter('numTarea', $intNumeroTarea);
                $objQuery->setParameter('estado', 'EnGestion');
            }
            elseif($strTipoAtencion === 'CASO')
            {
                $strSql = "SELECT ESTADO, AFECTADO, '['||ASIGNACIONES||']' AS ASIGNACIONES ".
                          "FROM ".
                          "( ".
                          " SELECT ". 
                          "   SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(:idCaso) AS ESTADO,". 
                          "   SPKG_ASIGNACION_SOLICITUD.F_GET_AFECTADOS_POR_CASO(:idCaso) AS AFECTADO,".
                          "   (SELECT LISTAGG('{'||".
                          "                     'IDASIG:'||asig.ID_ASIGNACION_SOLICITUD||','||".
                          "                     'TPROB:'||asig.TIPO_PROBLEMA||".
                          "                   '}', ', ') WITHIN GROUP (ORDER BY FE_CREACION DESC) ".
                          "    FROM DB_SOPORTE.INFO_ASIGNACION_SOLICITUD asig ".
                          "    WHERE ".
                          "      asig.REFERENCIA_ID = :idCaso ".
                          "      AND asig.ESTADO    = :estado ".
                          "   ) AS ASIGNACIONES ".
                          " FROM DUAL".
                          ")";
                $objQuery->setParameter('idCaso', $intIdCaso);
                $objQuery->setParameter('estado', 'EnGestion');
            }
            //Costo Query 2
            $objQuery->setSQL($strSql);
            $objRsm->addScalarResult('ESTADO', 'estado','string');
            $objRsm->addScalarResult('AFECTADO', 'afectado','string');
            $objRsm->addScalarResult('ASIGNACIONES', 'asignaciones','string');
            $arrayDatos = $objQuery->getScalarResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayDatos;
    }

    /**
     * Realiza la consulta de tareas pendientes por departamento
     * @param $arrayParametros
     * [
     *     intIdDepartamento       => id del departamento
     *     strUsrAsignado          => usuario asignado
     *     intIdOficina            => id de la oficina
     *     strCodEmpresa           => id de la empresa
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbSoporte        => usuario de esquema DbSoporte
     *     strPasswordDbSoporte    => password de esquema DbSoporte
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-03-2020
     * @since 1.0
     * @return $intTotal
     */
    public function getTotalAsignacionesSinNumero($arrayParametros)
    {
        $intTotal = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $intIdDepartamento    = ( isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']) )
                                       ? $arrayParametros['intIdDepartamento'] : '';
            $intIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : '';
            $strUsrAsignado       = ( isset($arrayParametros['strUsrAsignado']) && !empty($arrayParametros['strUsrAsignado']) )
                                       ? $arrayParametros['strUsrAsignado'] : '';
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
            if( !empty($strCodEmpresa) && !empty($strUserDbSoporte)  && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_TOT_ASIGNACIONES_SIN_NUM( ".
                                                                                                            ":intIdDepartamento, ".
                                                                                                            ":strCodEmpresa, ".
                                                                                                            ":intIdCanton, ".
                                                                                                            ":strUsrAsignado, ".
                                                                                                            ":intTotal  ); END;";
                $objStmt = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":intIdDepartamento",  $intIdDepartamento);
                oci_bind_by_name($objStmt, ":strCodEmpresa"    ,  $strCodEmpresa);
                oci_bind_by_name($objStmt, ":intIdCanton"      ,  $intIdCanton);
                oci_bind_by_name($objStmt, ":strUsrAsignado"   ,  $strUsrAsignado);
                oci_bind_by_name($objStmt, ":intTotal", $intTotal, 12, SQLT_INT);
                oci_execute($objStmt);
                oci_commit($objOciConexion);
                // Free resources.
                oci_free_statement($objStmt);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.
                                     $strCodEmpresa.'), Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.
                                     '), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $intTotal;
    }

    /**
     * Realiza la consulta del historial de conexión de un usuario según los parametros.
     * @param $arrayParametros
     * [
     *     intIdPersonaEmpresaRol => idPersonaEmpresaRol del usuario
     *     intLimiteMeses         => limite de registros para la consulta
     *     strDatabaseDsn         => string de la conexion a la Base de datos
     *     strUserDbSoporte       => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte   => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-03-2020
     * @since 1.0
     * @return OCI_B_CURSOR
     */
    public function getRegistrosConexionUsr($arrayParametros)
    {
        $objCursor = null;
        try
        {
            $intIdPersonaEmpresaRol = ( isset($arrayParametros['intIdPersonaEmpresaRol']) && !empty($arrayParametros['intIdPersonaEmpresaRol']) )
                                          ? $arrayParametros['intIdPersonaEmpresaRol'] : 0;
            $intMes                 = ( isset($arrayParametros['intMes']) && !empty($arrayParametros['intMes']) )
                                          ? $arrayParametros['intMes'] : 0;
            $intAnio                = ( isset($arrayParametros['intAnio']) && !empty($arrayParametros['intAnio']) )
                                          ? $arrayParametros['intAnio'] : 0;
            $strDatabaseDsn         = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                          ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte       = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                          ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte   = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                          ? $arrayParametros['strPasswordDbSoporte'] : null;

            if(!empty($intIdPersonaEmpresaRol) && !empty($intMes) && !empty($intAnio) && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte))
            {
                $objOciConexion     = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn);
                $objCursor          = oci_new_cursor($objOciConexion);

                $strSQL             = "BEGIN DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.P_GET_REGISTROS_CONEXION( :idPersonaEmpresaRol, ".
                                                                                                           ":mes,".
                                                                                                           ":anio,".
                                                                                                           ":cursorHistorial ); END;";
                $objStmt            = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":idPersonaEmpresaRol", $intIdPersonaEmpresaRol);
                oci_bind_by_name($objStmt, ":mes", $intMes);
                oci_bind_by_name($objStmt, ":anio", $intAnio);
                oci_bind_by_name($objStmt, ":cursorHistorial", $objCursor, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($objCursor);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información de '.
                                     ' InfoAsignacionSolicitudRepository->getHistorialUsrConexion.'.
                                     ' idPersonaEmpresaRol('.$intIdPersonaEmpresaRol.'), Mes('.$intMes.'), Año('.$intAnio.'), Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursor;
    }



    /**
     * Actualización: Se agrega que reciba por parámetro tambien strTabVisible
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-05-2021
     * 
     * Realiza la consulta de detalles tareas o casos por departamento y que se encuentren registrados en el 
     * módulo de gestión de pendientes.
     * 
     * @param $arrayParametros
     * [
     *     strCodEmpresa        => id de la empresa
     *     strIdDepartamento    => id del departamento
     *     intIdOficina         => id de la oficina
     *     strEstado            => estado del pendiente
     *     strTipo              => tipo del pendiente, tarea o caso
     *     strFechaIni          => fecha inicio del pendiente
     *     strFechaFin          => fecha fin del pendiente
     *     strTabVisible        => permite indicar en que tab sera visible las asignaciones
     *     strDatabaseDsn       => string de la conexion a la Base de datos
     *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
     *     strPasswordDbSoporte => password del esquema DB_SOPORTE
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @return OCI_B_CURSOR
     */
    public function getTareasCasosPendientesPorDep($arrayParametros)
    {
        $objCursor = null;
        try
        {
            $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : null;
            $strIdDepartamento    = ( isset($arrayParametros['strIdDepartamento']) && !empty($arrayParametros['strIdDepartamento']) )
                                       ? $arrayParametros['strIdDepartamento'] : null;
            $strIdCanton          = ( isset($arrayParametros['intIdCanton']) && !empty($arrayParametros['intIdCanton']) )
                                       ? $arrayParametros['intIdCanton'] : null;
            $strEstado            = ( isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']) )
                                       ? $arrayParametros['strEstado'] : null;
            $strTipo              = ( isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']) )
                                       ? $arrayParametros['strTipo'] : null;
            $strTabVisible        = ( isset($arrayParametros['strTabVisible']) && !empty($arrayParametros['strTabVisible']) )
                                       ? $arrayParametros['strTabVisible'] : null;
            $strFechaIni          = ( isset($arrayParametros['strFechaIni']) && !empty($arrayParametros['strFechaIni']) )
                                       ? $arrayParametros['strFechaIni'] : null;
            $strFechaFin          = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                       ? $arrayParametros['strFechaFin'] : null;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;

            if(    !empty($strCodEmpresa)  && !empty($strDatabaseDsn) && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn,'AL32UTF8');
                $objCursor       = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_GESTION_PENDIENTES.P_GET_PENDIENTES( :strCodEmpresa, ".
                                                                                              ":strIdDepartamento, ".
                                                                                              ":strIdCanton, ".
                                                                                              ":strEstado, ".
                                                                                              ":strTipo, ".
                                                                                              ":strTabVisible, ".
                                                                                              ":strFechaIni, ".
                                                                                              ":strFechaFin, ".
                                                                                              ":cursor ); END;";
                $objStmt         = oci_parse($objOciConexion, $strSQL);

                oci_bind_by_name($objStmt, ":strCodEmpresa",     $strCodEmpresa);
                oci_bind_by_name($objStmt, ":strIdDepartamento", $strIdDepartamento);
                oci_bind_by_name($objStmt, ":strIdCanton",      $strIdCanton);
                oci_bind_by_name($objStmt, ":strEstado",         $strEstado);
                oci_bind_by_name($objStmt, ":strTipo",           $strTipo);
                oci_bind_by_name($objStmt, ":strTabVisible",     $strTabVisible);
                oci_bind_by_name($objStmt, ":strFechaIni",       $strFechaIni);
                oci_bind_by_name($objStmt, ":strFechaFin",       $strFechaFin);
                oci_bind_by_name($objStmt, ":cursor",   $objCursor, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($objCursor);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - codEmpresa('.
                                     $strCodEmpresa.'),  idDepartamento('.$strIdDepartamento.') Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursor;
    }

    /**
    *
    * Realiza la consulta de seguimientos de las tareas y casos del módulo de gestión de pendientes.
    * @param $arrayParametros
    * [
    *     strCodEmpresa        => id de la empresa
    *     strIdDepartamento    => id del departamento
    *     intIdTarea           => id de la tarea
    *     intReferenciaId      => id de referencia de la tarea o caso
    *     strProcedencia       => procedencia del pendiente INTERNO-GESTION o EXTERNO
    *     strDatabaseDsn       => string de la conexion a la Base de datos
    *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
    *     strPasswordDbSoporte => password del esquema DB_SOPORTE
    * ]
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 10-02-2020
    * @return OCI_B_CURSOR
    */
   public function getSeguimientosGestPendiente($arrayParametros)
   {
       $objCursor = null;
       try
       {
           $strCodEmpresa        = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                      ? $arrayParametros['strCodEmpresa'] : null;
           $strIdDepartamento    = ( isset($arrayParametros['strIdDepartamento']) && !empty($arrayParametros['strIdDepartamento']) )
                                      ? $arrayParametros['strIdDepartamento'] : null;
           $intTareaId           = ( isset($arrayParametros['intIdTarea']) && !empty($arrayParametros['intIdTarea']) )
                                      ? $arrayParametros['intIdTarea'] : null;
           $intReferenciaId      = ( isset($arrayParametros['intReferenciaId']) && !empty($arrayParametros['intReferenciaId']) )
                                      ? $arrayParametros['intReferenciaId'] : null;
           $strProcedencia       = ( isset($arrayParametros['strProcedencia']) && !empty($arrayParametros['strProcedencia']) )
                                      ? $arrayParametros['strProcedencia'] : null;
           $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                      ? $arrayParametros['strDatabaseDsn'] : null;
           $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                      ? $arrayParametros['strUserDbSoporte'] : null;
           $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                      ? $arrayParametros['strPasswordDbSoporte'] : null;

           if(    !empty($strCodEmpresa)  && !empty($strDatabaseDsn)   && !empty($strIdDepartamento)
                                          && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
           {
               $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn,'AL32UTF8');
               $objCursor       = oci_new_cursor($objOciConexion);
               $strSQL          = "BEGIN DB_SOPORTE.SPKG_GESTION_PENDIENTES.P_GET_SEGUIMIENTOS( :intTareaId, ".
                                                                                               ":intDepartamentoId, ".
                                                                                               ":strEmpresaCod, ".
                                                                                               ":intReferenciaId, ".
                                                                                               ":strProcedencia, ".
                                                                                               ":cursor ); END;";
               $objStmt         = oci_parse($objOciConexion, $strSQL);

               oci_bind_by_name($objStmt, ":intTareaId",        $intTareaId);
               oci_bind_by_name($objStmt, ":intDepartamentoId", $strIdDepartamento);
               oci_bind_by_name($objStmt, ":strEmpresaCod",     $strCodEmpresa);
               oci_bind_by_name($objStmt, ":intReferenciaId",   $intReferenciaId);
               oci_bind_by_name($objStmt, ":strProcedencia",    $strProcedencia);
               oci_bind_by_name($objStmt, ":cursor",   $objCursor, -1, OCI_B_CURSOR);
               oci_execute($objStmt);
               oci_execute($objCursor);
               oci_commit($objOciConexion);
           }
           else
           {
               throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - codEmpresa('.
                                    $strCodEmpresa.'),  idDepartamento('.$strIdDepartamento.') Database('.
                                    $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
           }
       }
       catch(\Exception $e)
       {
           throw($e);
       }
       return $objCursor;
   }

    /**
    *
    * Realiza la consulta de la información de observación y nombre de tarea de una tarea
    * @param $arrayParametros
    * [
    *     intIdComunicacion    => id de la tarea
    *     strDatabaseDsn       => string de la conexion a la Base de datos
    *     strUserDbSoporte     => usuario del esquema DB_SOPORTE
    *     strPasswordDbSoporte => password del esquema DB_SOPORTE
    * ]
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 23-03-2021
    * @return OCI_B_CURSOR
    */
    public function getDatosTarea($arrayParametros)
    {
        $objCursor = null;
        try
        {
            $intIdComunicacion    = ( isset($arrayParametros['intIdComunicacion']) && !empty($arrayParametros['intIdComunicacion']) )
                                       ? $arrayParametros['intIdComunicacion'] : null;
            $strDatabaseDsn       = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbSoporte     = ( isset($arrayParametros['strUserDbSoporte']) && !empty($arrayParametros['strUserDbSoporte']) )
                                       ? $arrayParametros['strUserDbSoporte'] : null;
            $strPasswordDbSoporte = ( isset($arrayParametros['strPasswordDbSoporte']) && !empty($arrayParametros['strPasswordDbSoporte']) )
                                       ? $arrayParametros['strPasswordDbSoporte'] : null;
 
            if( !empty($strDatabaseDsn)   && !empty($intIdComunicacion) && !empty($strUserDbSoporte) && !empty($strPasswordDbSoporte) )
            {
                $objOciConexion  = oci_connect($strUserDbSoporte, $strPasswordDbSoporte, $strDatabaseDsn,'AL32UTF8');
                $objCursor       = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_SOPORTE.SPKG_GESTION_PENDIENTES.P_GET_DATOS_TAREA( :intIdComunicacion, ".
                                                                                                ":cursor ); END;";
                $objStmt         = oci_parse($objOciConexion, $strSQL);
 
                oci_bind_by_name($objStmt, ":intIdComunicacion",   $intIdComunicacion);
                oci_bind_by_name($objStmt, ":cursor",   $objCursor, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($objCursor);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - codEmpresa('.
                                     $strCodEmpresa.'),  idComunicacion('.$intIdComunicacion.') Database('.
                                     $strDatabaseDsn.'), UsrSoporte('.$strUserDbSoporte.'), PassSoporte('.$strPasswordDbSoporte.').');
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursor;
    }

    /**
     * Realiza la consulta de información necesaria para finalizar tarea.
     * @param $arrayParametros
     * [
     *     detalle_Id_Relacionado     => id detalle relacionado de la tarea
     *     id_detalle                 => id detalle de la tarea
     *     nombreTarea                => nombre tarea
     *     departamentoId             => id de departamento
     *     observacionTarea           => observación de la tarea 
     * ]
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.0 18-11-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function getInfoFinalizarTarea($arrayParametros)
    {

        $intDetalleIdRelacionado = !empty($arrayParametros["detalle_Id_Relacionado"])?$arrayParametros["detalle_Id_Relacionado"]:0;
        $intIdDetalle = $arrayParametros["id_detalle"];
        $strNombreTarea = $arrayParametros["nombreTarea"];
        $strDepartamentoId = $arrayParametros["departamentoId"];
        $strObservacionTtarea = $arrayParametros['observacionTarea'];

        $objRsm  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null,$objRsm);

        $strSql = "SELECT                        
                DB_SOPORTE.SPKG_INFO_TAREA.F_GET_TAREA_PADRE( :detalle_Id_Relacionado) AS NUMERO_TAREA_PADRE, 
                DB_SOPORTE.SPKG_INFO_TAREA.F_GET_PERMITE_FINALIZAR_INFORM(:id_detalle,:nombre_Tarea,:departamentoId) AS PERMITE_FINALIZAR_INFORME,
                DB_SOPORTE.SPKG_UTILIDADES.GET_VARCHAR_CLEAN(CAST(:observacion AS VARCHAR2(3999))) AS OBSERVACION 
                FROM dual";

        $objQuery->setParameter('detalle_Id_Relacionado', $intDetalleIdRelacionado);
        $objQuery->setParameter('id_detalle', $intIdDetalle);
        $objQuery->setParameter('nombre_Tarea', $strNombreTarea);
        $objQuery->setParameter('departamentoId', $strDepartamentoId);
        $objQuery->setParameter('observacion', $strObservacionTtarea);

        $objQuery->setSQL($strSql);

        $objRsm->addScalarResult('NUMERO_TAREA_PADRE', 'numero_tarea_padre','integer');
        $objRsm->addScalarResult('PERMITE_FINALIZAR_INFORME', 'permite_finalizar_informe','string');
        $objRsm->addScalarResult('OBSERVACION', 'observacion_tarea','string');

        $arrayDatos = $objQuery->getScalarResult();
        return $arrayDatos;
    }
    
}
