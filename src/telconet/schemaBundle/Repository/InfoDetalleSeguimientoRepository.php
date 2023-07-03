<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleSeguimientoRepository extends EntityRepository
{
    /**
     * Función que sirve para obtener la tarea en la que el empleado está trabajando actualmente.
     * Costo = 5226
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-01-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-08-2017 Se mejora el costo de la consulta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 14-08-2017 Se modifica la consulta para agregar parámetros referentes al caso.
     *                         Costo = 88 para consulta de seguimientos de tareas que pertenecen a un caso.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 15-09-2017 Se requiere obtener la tarea que se encuentra ejecutando el tecnico.
     * @param array $arrayParametros[
     *                                  intIdDetalle                        => id del detalle de la tarea
     *                                  strObservacionDetalleSeguimiento    => observación al iniciar ejecución de tarea                             
     *                                  strTipoAsignado                     => tipo de asignación: EMPLEADO, CUADRILLA, EMPRESAEXTERNA
     *                                  intAsignadoId                       => id del asignado de la tarea
     *                                  intRefAsignadoId                    => id de la referencia del asignado de la tarea,
     *                                  intPersonaEmpresaRolId              => id de la persona empresa rol id
     *                                  arrayUltimosEstadoTarea             => array con los estados de la tarea,
     *                                  intIdCaso                           => id del caso
     * 
     *                              ]
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 21-12-2017 - Si el tipo asignado es nulo se debe filtrar por el idPersonaEmpresaRol.
     * @return array $arrayRespuesta
     */
    public function getTareasSeguimientosPorCriterios($arrayParametros)
    {
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            $objNtvQueryCount   = $this->_em->createNativeQuery(null, $objRsm);
            $strSqlWithMinMax   = "WITH MAX_INFDETHIS
                                   AS (  SELECT dhMax.DETALLE_ID, MAX (dhMax.ID_DETALLE_HISTORIAL) AS ID_DETALLE_HISTORIAL 
                                         FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL dhMax
                                         GROUP BY dhMax.DETALLE_ID),
                                    MAX_INFDETASIG
                                    AS (  SELECT daMax.DETALLE_ID, MAX (daMax.ID_DETALLE_ASIGNACION) AS ID_DETALLE_ASIGNACION
                                          FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION daMax
                                          GROUP BY daMax.DETALLE_ID),
                                    MIN_INFCOM
                                    AS (  SELECT icMin.DETALLE_ID, MIN (icMin.ID_COMUNICACION) AS ID_COMUNICACION 
                                          FROM DB_COMUNICACION.INFO_COMUNICACION icMin
                                          GROUP BY icMin.DETALLE_ID) ";
            
            $strSelectCount = " SELECT COUNT(IDE.ID_DETALLE) AS TOTAL ";
            $strSelect      = " SELECT IDE.ID_DETALLE, IC.ID_COMUNICACION, AT.NOMBRE_TAREA, IDE.OBSERVACION, 
                                TO_CHAR(IDS.FE_CREACION,'YYYY-MM-DD HH24:MI') AS FE_IDS, IDH.FE_CREACION AS FE_IDH ";
            
            $strFrom        = " FROM DB_SOPORTE.INFO_DETALLE IDE
                                INNER JOIN DB_COMUNICACION.INFO_COMUNICACION IC
                                ON IC.DETALLE_ID = IDE.ID_DETALLE 
                                INNER JOIN MIN_INFCOM MIN_IC
                                ON IC.DETALLE_ID = MIN_IC.DETALLE_ID
                                INNER JOIN DB_SOPORTE.INFO_TAREA_SEGUIMIENTO IDS 
                                ON IDS.DETALLE_ID            = IDE.ID_DETALLE  
                                INNER JOIN DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH
                                ON IDH.DETALLE_ID = IDE.ID_DETALLE 
                                INNER JOIN MAX_INFDETHIS MAX_IDH
                                ON IDH.DETALLE_ID = MAX_IDH.DETALLE_ID
                                INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA
                                ON IDA.DETALLE_ID            = IDE.ID_DETALLE  
                                INNER JOIN MAX_INFDETASIG MAX_IDA
                                ON IDA.DETALLE_ID = MAX_IDA.DETALLE_ID
                                INNER JOIN DB_SOPORTE.ADMI_TAREA AT
                                ON AT.ID_TAREA               = IDE.TAREA_ID  ";
            
            $strWhere       = " WHERE IDH.ID_DETALLE_HISTORIAL =  MAX_IDH.ID_DETALLE_HISTORIAL 
                                AND IDA.ID_DETALLE_ASIGNACION = MAX_IDA.ID_DETALLE_ASIGNACION
                                AND IC.ID_COMUNICACION = MIN_IC.ID_COMUNICACION ";
            
            $objRsm->addScalarResult('ID_DETALLE', 'intIdDetalle', 'integer');
            $objRsm->addScalarResult('ID_COMUNICACION', 'intIdComunicacion', 'integer');
            $objRsm->addScalarResult('NOMBRE_TAREA', 'strNombreTarea', 'string');
            $objRsm->addScalarResult('OBSERVACION', 'strObservacionTarea', 'string');
            $objRsm->addScalarResult('FE_IDS', 'strFechaTareaSeguimiento', 'string');
            $objRsm->addScalarResult('FE_IDH', 'strFechaTareaHistorial', 'string');
            $objRsm->addScalarResult('TOTAL', 'total', 'integer');
            
            if(isset($arrayParametros["intIdCaso"]) && !empty($arrayParametros["intIdCaso"]))
            {
                $strQueryDepartamento = "SELECT AD.NOMBRE_DEPARTAMENTO
                                         FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                                         INNER JOIN DB_COMERCIAL.INFO_PERSONA IPERSONA
                                         ON IPER.PERSONA_ID = IPERSONA.ID_PERSONA
                                         INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG
                                         ON IOG.ID_OFICINA = IPER.OFICINA_ID
                                         INNER JOIN DB_GENERAL.ADMI_DEPARTAMENTO AD
                                         ON AD.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID 
                                         WHERE IPERSONA.LOGIN = IDS.USR_CREACION
                                         AND IOG.EMPRESA_ID  = IDS.EMPRESA_COD 
                                         AND IPER.DEPARTAMENTO_ID IS NOT NULL AND IPER.DEPARTAMENTO_ID <> 0 
                                         AND IPER.ESTADO NOT IN (:strEstadosNotInPer) 
                                         AND ROWNUM = 1";
                
                $strSelect  .= ", IDH.ID_DETALLE_HISTORIAL, AT.NOMBRE_TAREA, IDS.OBSERVACION AS OBS_SEGUIM, IDS.USR_CREACION, 
                                IDS.EMPRESA_COD, IDS.ESTADO_TAREA, IDA.PERSONA_EMPRESA_ROL_ID,
                                NVL((".$strQueryDepartamento."),'Empresa') AS DEPARTAMENTO ";

                $objRsm->addScalarResult('ID_DETALLE_HISTORIAL', 'intIdDetalleHistorial', 'integer');
                $objRsm->addScalarResult('NOMBRE_TAREA', 'strNombreTarea', 'string');
                $objRsm->addScalarResult('OBS_SEGUIM', 'strObsSeguim', 'string');
                $objRsm->addScalarResult('USR_CREACION', 'strUsrCreacionSeguim', 'string');
                $objRsm->addScalarResult('EMPRESA_COD', 'strEmpresaCodSeguim', 'string');
                $objRsm->addScalarResult('ESTADO_TAREA', 'strEstadoTareaSeguim', 'string');
                $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'intIdPerAsignacion', 'integer');
                $objRsm->addScalarResult('DEPARTAMENTO', 'strDepartamento', 'string');
                
                $strFrom    .= "INNER JOIN DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHIP
                                 ON IDHIP.ID_DETALLE_HIPOTESIS = IDE.DETALLE_HIPOTESIS_ID ";
                
                $strWhere   .= "AND IDHIP.CASO_ID = :intIdCaso ";
                $objNtvQuery->setParameter('intIdCaso', $arrayParametros['intIdCaso']);
                $objNtvQueryCount->setParameter('intIdCaso', $arrayParametros['intIdCaso']);
                
                $objNtvQuery->setParameter('strEstadosNotInPer', array('Inactivo', 'Cancelado', 'Anulado', 'Eliminado'));
                $objNtvQueryCount->setParameter('strEstadosNotInPer', array('Inactivo', 'Cancelado', 'Anulado', 'Eliminado'));
            }
            
            if(isset($arrayParametros["intIdDetalle"]) && !empty($arrayParametros["intIdDetalle"]))
            {
                $strWhere   .= "AND IDE.ID_DETALLE         = :intIdDetalle ";
                $objNtvQuery->setParameter('intIdDetalle', $arrayParametros['intIdDetalle']);
                $objNtvQueryCount->setParameter('intIdDetalle', $arrayParametros['intIdDetalle']);
            }
            
            if(isset($arrayParametros["strObservacionDetalleSeguimiento"]) && !empty($arrayParametros["strObservacionDetalleSeguimiento"]))
            {
                $strWhere   .= "AND DBMS_LOB.COMPARE(IDS.OBSERVACION, :strObservacionDetalleSeguimiento) = 0 ";
                $objNtvQuery->setParameter('strObservacionDetalleSeguimiento', $arrayParametros['strObservacionDetalleSeguimiento']);
                $objNtvQueryCount->setParameter('strObservacionDetalleSeguimiento', $arrayParametros['strObservacionDetalleSeguimiento']);
            }
            
            if(isset($arrayParametros["strTipoAsignado"]) && !empty($arrayParametros["strTipoAsignado"]))
            {
                $strWhere .= "AND IDA.TIPO_ASIGNADO = :strTipoAsignado ";
                $objNtvQuery->setParameter('strTipoAsignado', $arrayParametros['strTipoAsignado']);
                $objNtvQueryCount->setParameter('strTipoAsignado', $arrayParametros['strTipoAsignado']);
                        
                if($arrayParametros["strTipoAsignado"]=="CUADRILLA")
                {
                    if(isset($arrayParametros["intAsignadoId"]) && !empty($arrayParametros["intAsignadoId"]))
                    {
                        $strWhere .= "AND IDA.ASIGNADO_ID = :intAsignadoId ";
                        $objNtvQuery->setParameter('intAsignadoId', $arrayParametros['intAsignadoId']);
                        $objNtvQueryCount->setParameter('intAsignadoId', $arrayParametros['intAsignadoId']);
                        
                    }
                }
                else
                {
                    if(isset($arrayParametros["intRefAsignadoId"]) && !empty($arrayParametros["intRefAsignadoId"]))
                    {
                        $strWhere .= "AND IDA.REF_ASIGNADO_ID = :intRefAsignadoId ";
                        $objNtvQuery->setParameter('intRefAsignadoId', $arrayParametros['intRefAsignadoId']);
                        $objNtvQueryCount->setParameter('intRefAsignadoId', $arrayParametros['intRefAsignadoId']);
                    }
                    
                    if(isset($arrayParametros["arrayPersonaEmpresaRolId"]) && !empty($arrayParametros["arrayPersonaEmpresaRolId"]))
                    {
                        $strWhere .= "AND IDA.PERSONA_EMPRESA_ROL_ID IN (:arrayPersonaEmpresaRolId) ";
                        $objNtvQuery->setParameter('arrayPersonaEmpresaRolId', array_values($arrayParametros['arrayPersonaEmpresaRolId']));
                        $objNtvQueryCount->setParameter('arrayPersonaEmpresaRolId', array_values($arrayParametros['arrayPersonaEmpresaRolId']));
                    }
                }
            }
            else
            {
                if(isset($arrayParametros["intRefAsignadoId"]) && !empty($arrayParametros["intRefAsignadoId"]))
                {
                    $strWhere .= "AND IDA.REF_ASIGNADO_ID = :intRefAsignadoId ";
                    $objNtvQuery->setParameter('intRefAsignadoId', $arrayParametros['intRefAsignadoId']);
                    $objNtvQueryCount->setParameter('intRefAsignadoId', $arrayParametros['intRefAsignadoId']);
                }

                if(isset($arrayParametros["arrayPersonaEmpresaRolId"]) && !empty($arrayParametros["arrayPersonaEmpresaRolId"]))
                {
                    $strWhere .= "AND IDA.PERSONA_EMPRESA_ROL_ID IN (:arrayPersonaEmpresaRolId) ";
                    $objNtvQuery->setParameter('arrayPersonaEmpresaRolId', array_values($arrayParametros['arrayPersonaEmpresaRolId']));
                    $objNtvQueryCount->setParameter('arrayPersonaEmpresaRolId', array_values($arrayParametros['arrayPersonaEmpresaRolId']));
                }
            }
            
            if(isset($arrayParametros["arrayUltimosEstadoTarea"]) && !empty($arrayParametros["arrayUltimosEstadoTarea"]))
            {
                $strWhere .= "AND IDH.ESTADO IN (:arrayUltimosEstadoTarea) ";
                $objNtvQuery->setParameter('arrayUltimosEstadoTarea', array_values($arrayParametros['arrayUltimosEstadoTarea']));
                $objNtvQueryCount->setParameter('arrayUltimosEstadoTarea', array_values($arrayParametros['arrayUltimosEstadoTarea']));
            }
            
            $strQueryFinal  = $strSqlWithMinMax . $strSelect . $strFrom . $strWhere;
            $objNtvQuery->setSQL($strQueryFinal);
            $arrayResultado = $objNtvQuery->getResult();
            
            $strQueryCount      = $strSqlWithMinMax . $strSelectCount . $strFrom. $strWhere;
            $objNtvQueryCount->setSQL($strQueryCount);
            $intTotal           = $objNtvQueryCount->getSingleScalarResult();
            
            $arrayRespuesta['arrayResultado']   = $arrayResultado;
            $arrayRespuesta['intTotal']         = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    
   /**
     * Método que obtiene el json de todos los seguimientos perteneciente a las tareas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-08-2017 Se modifica la  ajustes en la consulta de los seguimientos 
     * 
     * @param array $arrayParametros["intIdCaso" => id del caso ]
     * 
     * @return string $strJsonData
     */
    public function getJSONTareasSeguimientosPorCriterios($arrayParametros)
    {
        $arraySeguimientos          = array();
        $arrayRespuestaSeguimientos = $this->getTareasSeguimientosPorCriterios($arrayParametros);
        $arrayResultadoSeguimientos = $arrayRespuestaSeguimientos['arrayResultado'];
        $intTotalSeguimientos       = $arrayRespuestaSeguimientos['intTotal'];
        if($arrayResultadoSeguimientos)
        {
            foreach($arrayResultadoSeguimientos as $arraySeguimiento)
            {
                $arraySeguimientos[] = array(
                                                 'tarea'        => $arraySeguimiento["strNombreTarea"],
                                                 'estado'       => $arraySeguimiento["strEstadoTareaSeguim"],
                                                 'observacion'  => $arraySeguimiento["strObsSeguim"],
                                                 'departamento' => strtoupper($arraySeguimiento["strDepartamento"]),
                                                 'empleado'     => $arraySeguimiento["strUsrCreacionSeguim"],
                                                 'fecha'        => $arraySeguimiento["strFechaTareaSeguimiento"]
                );
            }
        }

        $arrayRespuesta = array('total' => $intTotalSeguimientos, 'encontrados' => $arraySeguimientos);
        $strJsonData = json_encode($arrayRespuesta);
        return $strJsonData;
    }

}
