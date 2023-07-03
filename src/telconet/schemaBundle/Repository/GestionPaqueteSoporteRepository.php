<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

class GestionPaqueteSoporteRepository extends EntityRepository
{

    /**
     * Función que llama al procedimiento P_REGISTRAR_PAQUETE_SOPORTE para registrar un
     * paquete de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function crearPaqueteSoporte($arrayParametros)
    {
        $strUuidPaquete = isset($arrayParametros['uuidPaquete'])?$arrayParametros['uuidPaquete']:'';
        $strUuidDetalle = isset($arrayParametros['uuidDetalle'])?$arrayParametros['uuidDetalle']:'';
        $strTipo = isset($arrayParametros['tipo'])?$arrayParametros['tipo']:'';
        $intPersonaEmpresaRolId = isset($arrayParametros['personaEmpresaRolId'])?$arrayParametros['personaEmpresaRolId']:0;
        $intPuntoPaqueteId = isset($arrayParametros['puntoPaqueteId'])?$arrayParametros['puntoPaqueteId']:0;
        $intServicioPaqueteId = isset($arrayParametros['servicioPaqueteId'])?$arrayParametros['servicioPaqueteId']:0;
        $intMinutosContratados = isset($arrayParametros['minutosContratados'])?$arrayParametros['minutosContratados']:0;
        $objFechaInicio = isset($arrayParametros['fechaInicio'])?$arrayParametros['fechaInicio']:'';
        $objFechaFin = isset($arrayParametros['fechaFin'])?$arrayParametros['fechaFin']:'';
        $strObservacion = isset($arrayParametros['observacion'])?$arrayParametros['observacion']:'';
        $strUsuario = isset($arrayParametros['usuario'])?$arrayParametros['usuario']:'';
        $arrayServicios = isset($arrayParametros['servicios'])?$arrayParametros['servicios']:array();

        $arrayExtraParams = array('uuid_paquete' => $strUuidPaquete,
                                  'uuid_detalle' => $strUuidDetalle,
                                  'tipo' => $strTipo,
                                  'persona_empresa_rol_id' => $intPersonaEmpresaRolId,
                                  'punto_paquete_id' => $intPuntoPaqueteId,
                                  'servicio_paquete_id' => $intServicioPaqueteId,
                                  'minutos_contratados' => $intMinutosContratados,
                                  'fecha_inicio' => $objFechaInicio->format('d/m/Y'),
                                  'fecha_fin' => $objFechaFin->format('d/m/Y'),
                                  'observacion' => $strObservacion,
                                  'usuario' => $strUsuario,
                                  'servicios' => $arrayServicios);

        $strMensaje          = '';
        $strStatus           = '';

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.
            P_REGISTRAR_PAQUETE_SOPORTE                                      (:PCL_REQUEST,
                                                                              :PV_STATUS,
                                                                              :PV_MENSAJE);       
                                                                               END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                                   $arrayConnParams['password'],
                                   $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt, ':PCL_REQUEST', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMensaje);

                if(empty($strMensaje))
                {
                    $strMensaje = $strOCIError['message'];
                }
            }
            else
            {
                $strMensaje = trim($strMensaje);
            }

            if (empty($strMensaje))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMensaje);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }


    /**
     * Función que llama al procedimiento P_CONSULTAR_SOPORTES_PAQUETE para obtener
     * todos los soportes de un paquete.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function getSoportesPaqueteSoporte($arrayParametros)
    {
        $strUuidPaquete = isset($arrayParametros['uuidPaquete'])?$arrayParametros['uuidPaquete']:'';
        $intPersonaEmpresaRolId = isset($arrayParametros['personaEmpresaRolId'])?$arrayParametros['personaEmpresaRolId']:0;
        $intServicioPaqueteId = isset($arrayParametros['servicioPaqueteId'])?$arrayParametros['servicioPaqueteId']:0;

        $arrayExtraParams = array(array('uuid_paquete' => $strUuidPaquete,
                                        'persona_empresa_rol_id' => intval($intPersonaEmpresaRolId),
                                        'servicio_paquete_id' => intval($intServicioPaqueteId)));

        $strMensaje          = '';
        $strStatus           = '';
        $strResponse         = '';

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.                   P_CONSULTAR_SOPORTES_PAQUETE (:PCL_REQUEST,
                                          :PV_STATUS,
                                          :PV_MENSAJE,                                                          :PCL_RESPONSE);      
                                                     END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                                   $arrayConnParams['password'],
                                   $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            $strRequest = json_encode($arrayExtraParams);

            oci_bind_by_name($objStmt, ':PCL_REQUEST', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PCL_RESPONSE', $strResponse, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMensaje);

                if(empty($strMensaje))
                {
                    $strMensaje = $strOCIError['message'];
                }
            }
            else
            {
                $strMensaje = trim($strMensaje);
            }

            if (empty($strMensaje))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMensaje);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return array('respuesta' => $arrayRespuesta, 'informacion' => json_decode($strResponse));
    }


    /**
     * Función que llama al procedimiento P_SOLICITAR_AJUSTE_TIEMPO_SOP para generar una solicitud
     * de ajuste de tiempo de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function putSolAjusteTiempoSoporte($arrayParametros)
    {
        $strUuidPaquete = isset($arrayParametros['uuidPaquete'])?$arrayParametros['uuidPaquete']:'';
        $intTareaId = isset($arrayParametros['tareaId'])?$arrayParametros['tareaId']:0;
        $intMotivoId = isset($arrayParametros['motivoId'])?$arrayParametros['motivoId']:0;
        $intMinutosSoporte = isset($arrayParametros['minutosSoporte'])?$arrayParametros['minutosSoporte']:0;
        $strObservacion = isset($arrayParametros['observacion'])?$arrayParametros['observacion']:'';
        $strUsuarioSolicita = isset($arrayParametros['usuarioSolicita'])?$arrayParametros['usuarioSolicita']:'';
       
        $arrayExtraParams = array('uuid_paquete_soporte' => $strUuidPaquete,
                                  'tarea_id' => intval($intTareaId),
                                  'motivo_id' => intval($intMotivoId),
                                  'minutos_soporte' => intval($intMinutosSoporte),
                                  'observacion' => $strObservacion,
                                  'usuario_solicita' => $strUsuarioSolicita);

        $strMensaje          = '';
        $strStatus           = '';
        $strResponse         = '';


        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.                   P_SOLICITAR_AJUSTE_TIEMPO_SOP(:PCL_REQUEST,
                                          :PV_STATUS,
                                          :PV_MENSAJE,                                                  :PCL_RESPONSE);    
                                                                               END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                                   $arrayConnParams['password'],
                                   $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            $strPrueba = json_encode($arrayExtraParams);

            oci_bind_by_name($objStmt, ':PCL_REQUEST', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PCL_RESPONSE', $strResponse, 32*1024, SQLT_CHR);


            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMensaje);

                if(empty($strMensaje))
                {
                    $strMensaje = $strOCIError['message'];
                }
            }
            else
            {
                $strMensaje = trim($strMensaje);
            }

            if (empty($strMensaje))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMensaje);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return array('respuesta' => $arrayRespuesta, 'informacion' => json_decode($strResponse));
    }


    /**
     * Función que llama al procedimiento P_GESTIONAR_AJUSTE_TIEMPO_SOP para la aprobación o 
     * rechazo de una solicitud de ajuste de tiempo a un soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function putAprobarSolAjstTiempoSoporte($arrayParametros)
    {
        $intIdDetalleSolicitud = isset($arrayParametros['idDetalleSolicitud'])?$arrayParametros['idDetalleSolicitud']:0;
        $intServicioId = isset($arrayParametros['servicioId'])?$arrayParametros['servicioId']:0;
        $intTipoSolicitudId = isset($arrayParametros['tipoSolicitudId'])?$arrayParametros['tipoSolicitudId']:0;
        $intMotivoId = isset($arrayParametros['motivoId'])?$arrayParametros['motivoId']:0;
        $strEstado = isset($arrayParametros['estado'])?$arrayParametros['estado']:'';
        $strObservacion = isset($arrayParametros['observacion'])?$arrayParametros['observacion']:'';
        $strUserGestion = isset($arrayParametros['userGestion'])?$arrayParametros['userGestion']:'';
        $objFechaRechazo = isset($arrayParametros['feRechazo'])?$arrayParametros['feRechazo']:'';
        $intDetalleProcesoId = isset($arrayParametros['detalleProcesoId'])?$arrayParametros['detalleProcesoId']:0;
        $objFechaEjecucion = isset($arrayParametros['feEjecucion'])?$arrayParametros['feEjecucion']:'';

        $arrayExtraParams = array('Ln_IdDetalleSolicitud' => intval($intIdDetalleSolicitud),
                                  'pn_servicioId' => intval($intServicioId),
                                  'pn_tipoSolicitudId' => intval($intTipoSolicitudId),
                                  'pn_motivoId' => intval($intMotivoId),
                                  'pv_estado' => $strEstado,
                                  'pv_observacion' => $strObservacion,
                                  'pv_userGestion' => $strUserGestion,
                                  'pv_feRechazo' => date("d/m/y", strtotime($objFechaRechazo)),
                                  //'pv_feRechazo' => $objFechaRechazo->format('d/m/y'),
                                  'pv_detalleProcesoId' => $intDetalleProcesoId,
                                  'pv_feEjecucion' => date("d/m/y", strtotime($objFechaEjecucion)));
                                  //'pv_feEjecucion' => $objFechaEjecucion->format('d/m/y'));

        $strMensaje          = '';
        $strStatus           = '';

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.P_GESTIONAR_AJUSTE_TIEMPO_SOP(:PCL_REQUEST,
                                                                              :PV_STATUS,
                                                                              :PV_MENSAJE);
                                                                              END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                                   $arrayConnParams['password'],
                                   $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            $strPrueba = json_encode($arrayExtraParams);

            oci_bind_by_name($objStmt, ':PCL_REQUEST', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMensaje);

                if(empty($strMensaje))
                {
                    $strMensaje = $strOCIError['message'];
                }
            }
            else
            {
                $strMensaje = trim($strMensaje);
            }

            if (empty($strMensaje))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMensaje);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * Función que obtiene el uuid del paquete de horas de soporte con el id del servicio.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function obtenerUuidPaquete($intIdServicio)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQueryData       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = " SELECT UUID_PAQUETE_SOPORTE_CAB  ";
        $strFrom = " FROM DB_SOPORTE.INFO_PAQUETE_SOPORTE_CAB ";
        $strWhere = " WHERE SERVICIO_ID = :idServicio ";

        $objRsm->addScalarResult('UUID_PAQUETE_SOPORTE_CAB', 'strUuidPaquete', 'string');

        $objQueryData->setParameter('idServicio', $intIdServicio);

        $strSql = $strSelect.$strFrom.$strWhere;

        $objQueryData->setSQL($strSql);

        $objDatos = $objQueryData->getResult(); 
        
        return $objDatos;
    }

    /**
     * Función que obtiene el id de servicio mediante el número de
     * tarea.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function findIdServicioByNumeroTarea($intTareaId)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQueryData       = $this->_em->createNativeQuery(null,$objRsm);
        $strSelect = " SELECT AFECTADO_ID ";
        $strFrom = " FROM DB_SOPORTE.INFO_PARTE_AFECTADA ";
        $strWhere = " WHERE TIPO_AFECTADO = 'Servicio' 
                      AND DETALLE_ID = (SELECT DETALLE_ID FROM DB_SOPORTE.INFO_TAREA WHERE NUMERO_TAREA = :tareaId) ";

        $objRsm->addScalarResult('AFECTADO_ID', 'servicioId', 'integer');

        $objQueryData->setParameter('tareaId', $intTareaId);

        $strSql = $strSelect.$strFrom.$strWhere;

        $objQueryData->setSQL($strSql);

        $objDatos = $objQueryData->getResult(); 
        
        return $objDatos;
    }

    /**
     * Función que obtiene el tipo de tarea - (Caso a actividad)
     * tarea.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0
     * @since 06-02-2023
     */
    public function findTipoTareaByNumeroTarea($intTareaId)
    {
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objRsmCaso         = new ResultSetMappingBuilder($this->_em);
        $objQueryData       = $this->_em->createNativeQuery(null,$objRsm);
        $objQueryDataCaso   = $this->_em->createNativeQuery(null,$objRsmCaso);

        $strSelectCaso  = " SELECT IC.NUMERO_CASO, TA.NOMBRE_TAREA  ";
        $strFromCaso    = " FROM  DB_SOPORTE.ADMI_TAREA TA, DB_SOPORTE.INFO_CASO IC, DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH ";
        $strWhereCaso   = " WHERE IDH.id_detalle_hipotesis = (
                                    select detalle_hipotesis_id from db_soporte.info_detalle where id_detalle =   
                                    ( SELECT DETALLE_ID FROM DB_SOPORTE.INFO_TAREA WHERE NUMERO_TAREA = :tareaId ) 
                                )
                                AND IC.ID_CASO = IDH.CASO_ID
                                AND TA.ID_TAREA = (  SELECT TAREA_ID FROM DB_SOPORTE.INFO_TAREA WHERE NUMERO_TAREA = :tareaId ) ";

        $objRsmCaso->addScalarResult('NUMERO_CASO', 'caso_id', 'string');
        $objRsmCaso->addScalarResult('NOMBRE_TAREA', 'nombre_tarea', 'string');
        $objQueryDataCaso->setParameter('tareaId', $intTareaId);
        $strSqlCaso      = $strSelectCaso.$strFromCaso.$strWhereCaso;
        $objQueryDataCaso->setSQL($strSqlCaso);
        $objDatosCaso    = $objQueryDataCaso->getResult();
        
        if($objDatosCaso)
        {
            $strTipoTarea   = $objDatosCaso[0]['caso_id'];
            $strNombreTarea = $objDatosCaso[0]['nombre_tarea'];
            $strDatoFinal   = $objDatosCaso;
        }
        else
        {
            $strSelect  = " SELECT AFECTADO_ID, TA.NOMBRE_TAREA ";
            $strFrom    = " FROM DB_SOPORTE.INFO_PARTE_AFECTADA PA, DB_SOPORTE.ADMI_TAREA TA ";
            $strWhere   = " WHERE PA.TIPO_AFECTADO = 'Servicio' 
                            AND PA.DETALLE_ID = (SELECT DETALLE_ID FROM DB_SOPORTE.INFO_TAREA WHERE NUMERO_TAREA = :tareaId )
                            AND TA.ID_TAREA = (  ( SELECT  tarea_id FROM DB_SOPORTE.Info_Detalle where id_detalle 
                                            = ( SELECT detalle_id FROM DB_SOPORTE.info_Tarea Where numero_tarea = :tareaId) )
                            )";

            $objRsm->addScalarResult('AFECTADO_ID', 'servicioId', 'integer');
            $objRsm->addScalarResult('NOMBRE_TAREA', 'nombre_tarea', 'string');

            $objQueryData->setParameter('tareaId', $intTareaId);
            $strSql      = $strSelect.$strFrom.$strWhere;
            $objQueryData->setSQL($strSql);
            $objDatos    = $objQueryData->getResult();

            $strTipoTarea   = '';
            $strNombreTarea = $objDatos[0]['nombre_tarea'];
            $strDatoFinal   = $objDatos;
        }
        return $strDatoFinal;
    }
    /**
     * Función que obtiene el campo valor de una caracteristica mediante el id de servicio
     * y la descripción de la característica.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function obtenerValorCaracteristica($intIdServicio, $strCaract)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQueryData       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = " SELECT VALOR  ";
        $strFrom = " FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ";
        $strWhere = " WHERE SERVICIO_ID IN :idServicio 
                      AND PRODUCTO_CARACTERISITICA_ID IN ( SELECT ID_PRODUCTO_CARACTERISITICA 
                      FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                      WHERE PRODUCTO_ID IN
                        (SELECT ID_PRODUCTO
                        FROM DB_COMERCIAL.ADMI_PRODUCTO
                        WHERE DESCRIPCION_PRODUCTO = 'PAQUETE HORAS SOPORTE' OR DESCRIPCION_PRODUCTO = 'PAQUETE HORAS SOPORTE RECARGA'
                        )
                      AND CARACTERISTICA_ID IN
                        (SELECT ID_CARACTERISTICA
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                        WHERE DESCRIPCION_CARACTERISTICA IN :strCaract
                        ))";

        $objRsm->addScalarResult('VALOR', 'valor', 'string');

        $objQueryData->setParameter('idServicio', $intIdServicio);

        $objQueryData->setParameter('strCaract', $strCaract);

        $strSql = $strSelect.$strFrom.$strWhere;

        $objQueryData->setSQL($strSql);

        $objDatos = $objQueryData->getResult(); 
        
        return $objDatos;
    }


    /**
     *
     * Documentación para el método 'ConsultaPaqueteHorasSoporte' y los paràmetros vienen de ajaxGetHorasSoporteAction.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1 01-09-2022 Se llama al procedimiento P_CONSULTAR_TODO_INFO_PAQUETE que realiza la consulta
     *                         de todo un pquete de horas de soporte.
    */
    public function ConsultaPaqueteHorasSoporte($arrayRequestDatos)
    {
        $arrayResponse = new JsonResponse();
        $strStatus     = 'ERROR';
        $strMensaje    = '';
      
        $strSql        = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.P_CONSULTAR_TODO_INFO_PAQUETE( :Pcl_request,
                                                                                                       :Pv_status,
                                                                                                       :Pv_mensaje,
                                                                                                       :Pcl_response);  
                                                                                                    END;";
        $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();
        $objConn = oci_connect($arrayConnParams['user'], $arrayConnParams['password'], $arrayConnParams['dbname']);
                                                                                        
        $objStmt        = oci_parse($objConn, $strSql);

        $objDatosClob   = oci_new_descriptor($objConn);
        $objDatosClob->writetemporary(json_encode($arrayRequestDatos));

        $objResponse    = oci_new_descriptor($objConn, OCI_D_LOB);

        oci_bind_by_name($objStmt, ':Pcl_request'   , $objDatosClob, -1, SQLT_CLOB); 
        oci_bind_by_name($objStmt, ':Pv_status'     , $strStatus,50);
        oci_bind_by_name($objStmt, ':Pv_mensaje'    , $strMensaje,4000);  
        oci_bind_by_name($objStmt, ':Pcl_Response'  , $objResponse, -1, SQLT_CLOB);
        oci_execute($objStmt);
        oci_commit($objConn);

        if ($strStatus != 'OK')
        {
            $strStatus   = '500';   
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $strMensaje  = $strMensaje . " Por favor notificar a Sistemas."; 
            $arrayDatosSoporteExtraer[] = array(  'mensaje'   =>  $strMensaje,
                                                    'status'  =>  $strStatus ); 
        }
        else
        {
            $strDatosSoporte    = html_entity_decode($objResponse->load());
            
            // Convertimos la CADENA $strDatosSoporte a JSON y guardamos el resultado en una variable
            $arrayDatosSoporteExtraer  =json_decode($strDatosSoporte);
        }
              
        $arrayResponse->setContent(json_encode($arrayDatosSoporteExtraer[0]));
        return $arrayResponse;
    }


    /**
     *
     * Documentación para el método 'ingresaSoportePaqueteSoporte'.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1 10-11-2022 Se llama al procedimiento P_REGISTRAR_SOPORTES que realiza el registro
     *                         de el soporte como tal, asì mismo realiza lo de recalcular los tiempos del paquete de horas
     *                         y envio de notificacion al cliente con el reporte del paquete de horas.
    */
    public function ingresaSoportePaqueteSoporte($arrayParametros)
    {
        $intTareaId   = $arrayParametros['tarea_id'];
        $intDetalleId = $arrayParametros['detalle_id'];

        $arrayRequestDatos          = array(
            'tarea_id'   => intval($intTareaId),
            'detalle_id'  => intval($intDetalleId)
            )
        ;
        $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.P_REGISTRAR_SOPORTES(:PCL_REQUEST,
        :PV_STATUS,
        :PV_MENSAJE);  END;";
        
        $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();
        $objConn         = oci_connect($arrayConnParams['user'], $arrayConnParams['password'], $arrayConnParams['dbname']);
                                                                                        
        $objStmt         = oci_parse($objConn, $strSql);

        $objDatosClob   = oci_new_descriptor($objConn);
        $objDatosClob->writetemporary(json_encode($arrayRequestDatos));

        oci_bind_by_name($objStmt, ':Pcl_request'   , $objDatosClob, -1, SQLT_CLOB); 
        oci_bind_by_name($objStmt, ':Pv_status'     , $strStatus,50);
        oci_bind_by_name($objStmt, ':Pv_mensaje'    , $strMensaje,4000);  
        oci_bind_by_name($objStmt, ':Pcl_Response'  , $objResponse, -1, SQLT_CLOB);
        oci_execute($objStmt);
        oci_commit($objConn);

    }
}