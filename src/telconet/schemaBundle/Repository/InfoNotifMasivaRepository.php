<?php

namespace telconet\schemaBundle\Repository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoNotifMasivaRepository extends BaseRepository
{
    /**
     * Función que sirve para obtener el listado principal de los envíos masivos
     * Costo = 3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intIdNotifMasiva"          => id de la notificación masiva
     *                                  "intStart"                  => inicio del rownum
     *                                  "intLimit"                  => fin del rownum
     *                                  "intIdPlantilla"            => id de la plantilla
     *                                  "strTipoEnvio"              => tipo de envío de la notificación masiva                      
     *                                  "strEstado"                 => estado de la notificación masiva
     *                                  "strSoloValidarEnvio"       => 'S' si sólo se requiere conocer si existe otro envío del mismo tipo
     *                                                                  con la misma plantilla  
     *                              ]
     * @return array $arrayRespuesta
     */
    public function getNotificacionesMasivas($arrayParametros)
    {
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSelectCount = " SELECT COUNT(INM.ID_NOTIF_MASIVA) AS TOTAL ";
            $strSelect      = " SELECT INM.ID_NOTIF_MASIVA, AP.NOMBRE_PLANTILLA, INM.TIPO, ";
            if(empty($arrayParametros["strSoloValidarEnvio"]))
            {
                $strQueryContactos  = " SELECT LISTAGG (TRIM(AR.DESCRIPCION_ROL), '<br>') WITHIN GROUP (
                                        ORDER BY AR.DESCRIPCION_ROL) TIPOS_CONTACTOS
                                        FROM DB_COMERCIAL.INFO_EMPRESA_ROL IER
                                        INNER JOIN DB_GENERAL.ADMI_ROL AR
                                        ON AR.ID_ROL = IER.ROL_ID
                                        WHERE IER.ID_EMPRESA_ROL IN
                                        (SELECT TRIM(REGEXP_SUBSTR
                                                        (DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(  INM.ID_NOTIF_MASIVA, 
                                                                                                                        :strParamTiposContactos , 
                                                                                                                        :strEstadoActivo), 
                                                        '[^,]+', 1, LEVEL)) nombreVariable
                                        FROM dual
                                        CONNECT BY LEVEL <= 
                                        LENGTH(DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA, 
                                                                                                           :strParamTiposContactos,
                                                                                                           :strEstadoActivo)) 
                                        - LENGTH(REPLACE(DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA, 
                                                                                                                     :strParamTiposContactos , 
                                                                                                                     :strEstadoActivo), ',', '')) +1 
                                      ) ";
                $strSelect .= "(". $strQueryContactos .") AS TIPOS_CONTACTOS,
                                CONCAT(
                                NVL(
                                CASE 
                                  WHEN INM.TIPO = 'PROGRAMADO' THEN 'FECHA Y HORA PROGRAMADA: ' 
                                                        || DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA,
                                                                                                                        :strParamFeHoraProg,
                                                                                                                        :strEstadoActivo)
                                  WHEN INM.TIPO = 'RECURRENTE' THEN 
                                                        'PERIODICIDAD: '
                                                        || DB_GENERAL.GNRLPCK_UTIL.F_GET_PARAM_VALOR2(:strParamCabPeriod,
                                                           NVL(DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA,
                                                                                                                        :strParamPeriodicidad,
                                                                                                                        :strEstadoActivo),'')) ||
                                                        '<br/>EJECUTAR DESDE: '
                                                        || DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA,
                                                                                                                        :strParamFeEjecucionDesde,
                                                                                                                        :strEstadoActivo) ||
                                                        '<br/>HORA EJECUCIÓN: '
                                                        || DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA,
                                                                                                                        :strParamHoraEjecucion,
                                                                                                                        :strEstadoActivo)  
                                                        
                                  ELSE 'FECHA Y HORA DE EJECUCIÓN: ' || INM.FE_CREACION 
                                END , '') , 
                                NVL(
                                CASE 
                                  WHEN (INM.TIPO = 'RECURRENTE' AND 
                                  (DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA(INM.ID_NOTIF_MASIVA,
                                                                                                :strParamDia,
                                                                                                :strEstadoActivo) IS NOT NULL)) 
                                  THEN '<br/>DIA: ' || DB_COMUNICACION.CUKG_CONSULTS.F_GET_VALOR_PARAM_NOTIF_MASIVA( INM.ID_NOTIF_MASIVA,
                                                                                                                :strParamDia,
                                                                                                                :strEstadoActivo)  
                                  ELSE ''

                                END , '') ) AS INFO_GENERAL,";
            }
            
            $strSelect      .= " INM.USR_CREACION, INM.ESTADO ";
            $strFrom        = " FROM DB_COMUNICACION.INFO_NOTIF_MASIVA INM
                                INNER JOIN DB_COMUNICACION.ADMI_PLANTILLA AP
                                ON AP.ID_PLANTILLA = INM.PLANTILLA_ID ";
            $strWhere       = "";
            
            $strOrderBy     = "ORDER BY INM.ID_NOTIF_MASIVA DESC ";
            
            $objNtvQuery->setParameter('strParamCabPeriod', 'PERIODICIDAD_ENVIO_MASIVO_RECURRENTE');
            $objNtvQuery->setParameter('strParamTiposContactos', 'idsTipoContacto');
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            $objNtvQuery->setParameter('strParamPeriodicidad', 'periodicidad');
            $objNtvQuery->setParameter('strParamFeHoraProg', 'fechaHoraProgramada');
            $objNtvQuery->setParameter('strParamFeEjecucionDesde', 'fechaEjecucionDesde');
            $objNtvQuery->setParameter('strParamHoraEjecucion', 'horaEjecucion');
            $objNtvQuery->setParameter('strParamDia', 'numeroDia');

            $objRsm->addScalarResult('ID_NOTIF_MASIVA', 'intIdNotifMasiva', 'integer');
            $objRsm->addScalarResult('NOMBRE_PLANTILLA', 'strNombrePlantilla', 'string');
            $objRsm->addScalarResult('TIPO', 'strTipoEnvio', 'string');
            $objRsm->addScalarResult('TIPOS_CONTACTOS', 'strTiposContactos', 'string');
            $objRsm->addScalarResult('INFO_GENERAL', 'strInfoGeneral', 'string');
            $objRsm->addScalarResult('USR_CREACION', 'strUsrCreacion', 'string');
            $objRsm->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsm->addScalarResult('TOTAL', 'intTotal', 'integer');
            
            
            if(isset($arrayParametros["intIdNotifMasiva"]) && !empty($arrayParametros["intIdNotifMasiva"]))
            {
                $strWhere   .= "INM.ID_NOTIF_MASIVA = :intIdNotifMasiva AND ";
                $objNtvQuery->setParameter('intIdNotifMasiva', $arrayParametros['intIdNotifMasiva']);
            }
            
            if(isset($arrayParametros["intIdPlantilla"]) && !empty($arrayParametros["intIdPlantilla"]))
            {
                $strWhere   .= "AP.ID_PLANTILLA = :intIdPlantilla AND ";
                $objNtvQuery->setParameter('intIdPlantilla', $arrayParametros['intIdPlantilla']);
            }
            
            if(isset($arrayParametros["strTipoEnvio"]) && !empty($arrayParametros["strTipoEnvio"]))
            {
                $strWhere   .= "INM.TIPO = :strTipoEnvio AND ";
                $objNtvQuery->setParameter('strTipoEnvio', $arrayParametros['strTipoEnvio']);
            }
            
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   .= " INM.ESTADO = :strEstado AND ";
                $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }
            
            if(!empty($strWhere))
            {
                $strWhere = "WHERE ".substr($strWhere, 0, -4);
            }
            
            $strQueryFinal  =  $strSelect . $strFrom . $strWhere. $strOrderBy;
            $strQueryCount  = $strSelectCount . $strFrom. $strWhere;
            $objNtvQuery->setSQL($strQueryCount);
            
            
            $intTotal       = $objNtvQuery->getSingleScalarResult();
            
            if(intval($intTotal) > 0)
            {
                $objNtvQuery->setSQL($strQueryFinal);
                $arrayResultado = $this->setQueryLimit($objNtvQuery, $arrayParametros['intLimit'], $arrayParametros['intStart'])->getResult();
            }
            
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
     * Método que obtiene el json de las notificaciones masivas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intIdNotifMasiva"          => id de la notificación masiva
     *                                  "intStart"                  => inicio del rownum
     *                                  "intLimit"                  => fin del rownum
     *                                  "intIdPlantilla"            => id de la plantilla
     *                                  "strTipoEnvio"              => tipo de envío de la notificación masiva                      
     *                                  "strEstado"                 => estado de la notificación masiva 
     *                                  "strSoloValidarEnvio"       => 'S' si sólo se requiere conocer si existe otro envío del mismo tipo
     *                                                                  con la misma plantilla  
     *                              ]
     * 
     * @return string $strJsonData
     */
    public function getJSONNotificacionesMasivas($arrayParametros)
    {
        $arrayRespuestaNotifMasivas = $this->getNotificacionesMasivas($arrayParametros);
        $arrayResultadoNotifMasivas = $arrayRespuestaNotifMasivas['arrayResultado'];
        $intTotalNotifMasivas       = $arrayRespuestaNotifMasivas['intTotal'];
        
        if($arrayResultadoNotifMasivas)
        {
            foreach($arrayResultadoNotifMasivas as $arrayNotifMasiva)
            {
                $arrayNotifMasivas[] = array(
                                            "intIdNotifMasiva"      => $arrayNotifMasiva['intIdNotifMasiva'],
                                            "strNombrePlantilla"    => $arrayNotifMasiva['strNombrePlantilla'],
                                            "strTipoEnvio"          => $arrayNotifMasiva['strTipoEnvio'],
                                            "strTiposContactos"     => $arrayNotifMasiva['strTiposContactos'],
                                            "strInfoGeneral"        => $arrayNotifMasiva['strInfoGeneral'],
                                            "strUsrCreacion"        => $arrayNotifMasiva['strUsrCreacion'],
                                            "strEstado"             => $arrayNotifMasiva['strEstado'],
                                            "strAccionEliminar"     => ($arrayNotifMasiva['strEstado'] === 'Eliminado') ? 'icon-invisible'
                                                                        : "btn-acciones button-grid-delete"
                                      );
            }
        }
        $strJsonData                = json_encode(array('intTotal'   => $intTotalNotifMasivas, 'arrayResultado' => $arrayNotifMasivas));
        return $strJsonData;
    }
}
