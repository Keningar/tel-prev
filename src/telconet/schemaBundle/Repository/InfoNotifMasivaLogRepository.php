<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoNotifMasivaLogRepository extends BaseRepository
{
    /**
     * Función que sirve para obtener el listado de logs de los envíos masivos
     * Costo = 3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intStart"              => inicio del rownum
     *                                  "intLimit"              => fin del rownum
     *                                  "intIdNotifMasiva"      => id de la notificación masiva
     *                              ]
     * @return array $arrayRespuesta
     */
    public function getNotificacionesMasivasLogs($arrayParametros)
    {
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSelectCount = " SELECT COUNT(INML.ID_NOTIF_MASIVA_LOG) AS TOTAL ";
            $strSelect      = " SELECT INML.ID_NOTIF_MASIVA_LOG,INM.ID_NOTIF_MASIVA, INM.NOMBRE_JOB,
                                COALESCE(TO_CHAR(INML.FE_CREACION,'DD/MM/YYYY'),'') AS FE_CREACION,
                                INML.NUM_PROCESADOS, INML.NUM_ENVIADOS, INML.NUM_NO_ENVIADOS, INML.ESTADO ";
            
            $strFrom        = " FROM DB_COMUNICACION.INFO_NOTIF_MASIVA INM
                                INNER JOIN DB_COMUNICACION.INFO_NOTIF_MASIVA_LOG INML
                                ON INML.NOTIF_MASIVA_ID = INM.ID_NOTIF_MASIVA ";
            $strWhere       = "";
            
            $strOrderBy     = "ORDER BY INML.ID_NOTIF_MASIVA_LOG DESC ";
            
            $objRsm->addScalarResult('ID_NOTIF_MASIVA_LOG', 'intIdNotifMasivaLog', 'integer');
            $objRsm->addScalarResult('ID_NOTIF_MASIVA', 'intIdNotifMasiva', 'integer');
            $objRsm->addScalarResult('NOMBRE_JOB', 'strNombreJob', 'string');
            $objRsm->addScalarResult('FE_CREACION', 'strFechaCreacion', 'string');
            $objRsm->addScalarResult('NUM_PROCESADOS', 'intNumProcesados', 'integer');
            $objRsm->addScalarResult('NUM_ENVIADOS', 'intNumEnviados', 'integer');
            $objRsm->addScalarResult('NUM_NO_ENVIADOS', 'intNumNoEnviados', 'integer');
            $objRsm->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsm->addScalarResult('TOTAL', 'intTotal', 'integer');
            
            
            if(isset($arrayParametros["intIdNotifMasiva"]) && !empty($arrayParametros["intIdNotifMasiva"]))
            {
                $strWhere   .= "INM.ID_NOTIF_MASIVA = :intIdNotifMasiva AND ";
                $objNtvQuery->setParameter('intIdNotifMasiva', $arrayParametros['intIdNotifMasiva']);
            }
            
            if(!empty($strWhere))
            {
                $strWhere = "WHERE ".substr($strWhere, 0, -4);
            }
            
            $strQueryFinal  =  $strSelect . $strFrom . $strWhere;
            $strQueryCount  = $strSelectCount . $strFrom. $strWhere.$strOrderBy;
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
     * Método que obtiene el json de los logs de las notificaciones masivas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intStart"              => inicio del rownum
     *                                  "intLimit"              => fin del rownum
     *                                  "intIdNotifMasiva"      => id de la notificación masiva
     *                              ]
     * 
     * @return string $strJsonData
     */
    public function getJSONNotificacionesMasivasLogs($arrayParametros)
    {
        $arrayRespuestaNotifMasivasLogs = $this->getNotificacionesMasivasLogs($arrayParametros);
        $arrayResultadoNotifMasivasLogs = $arrayRespuestaNotifMasivasLogs['arrayResultado'];
        $intTotalNotifMasivasLogs       = $arrayRespuestaNotifMasivasLogs['intTotal'];
        $strJsonData                    = json_encode(array('intTotal'          => $intTotalNotifMasivasLogs, 
                                                            'arrayResultado'    => $arrayResultadoNotifMasivasLogs));
        return $strJsonData;
    }
}
