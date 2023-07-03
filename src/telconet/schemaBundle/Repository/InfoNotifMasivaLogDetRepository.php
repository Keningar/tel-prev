<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoNotifMasivaLogDetRepository extends BaseRepository
{
    /**
     * Función que sirve para obtener el detalle de los logs de los envíos masivos
     * Costo = 64
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intStart"              => inicio del rownum
     *                                  "intLimit"              => fin del rownum
     *                                  "intIdNotifMasivaLog"   => id del log de la notificación masiva
     *                                  "strLogin"              => login del cliente
     *                                  "strNombres"            => nombres del contacto del cliente,
     *                                  "strEstado"             => estado del envío
     *                              ]
     * @return array $arrayRespuesta
     */
    public function getNotificacionesMasivasLogsDets($arrayParametros)
    {
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSelectCount = " SELECT COUNT(INMLD.ID_NOTIF_MASIVA_LOG_DET) AS TOTAL ";
            $strSelect      = " SELECT INMLD.ID_NOTIF_MASIVA_LOG_DET, INMLD.LOGIN, INMLD.NOMBRES, INMLD.CORREO, INMLD.TIPO_CONTACTO, INMLD.ESTADO ";
            
            $strFrom        = " FROM DB_COMUNICACION.INFO_NOTIF_MASIVA_LOG INML
                                INNER JOIN DB_COMUNICACION.INFO_NOTIF_MASIVA_LOG_DET INMLD
                                ON INMLD.NOTIF_MASIVA_LOG_ID = INML.ID_NOTIF_MASIVA_LOG ";
            $strWhere       = "";
            
            $strOrderBy     = "ORDER BY INMLD.LOGIN ASC ";
            
            $objRsm->addScalarResult('ID_NOTIF_MASIVA_LOG_DET', 'intIdNotifMasivaLogDet', 'integer');
            $objRsm->addScalarResult('LOGIN', 'strLogin', 'string');
            $objRsm->addScalarResult('NOMBRES', 'strNombres', 'string');
            $objRsm->addScalarResult('CORREO', 'strCorreo', 'string');
            $objRsm->addScalarResult('TIPO_CONTACTO', 'strTipoContacto', 'string');
            $objRsm->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsm->addScalarResult('TOTAL', 'intTotal', 'integer');
            
            if(isset($arrayParametros["intIdNotifMasivaLog"]) && !empty($arrayParametros["intIdNotifMasivaLog"]))
            {
                $strWhere   .= "INML.ID_NOTIF_MASIVA_LOG = :intIdNotifMasivaLog AND ";
                $objNtvQuery->setParameter('intIdNotifMasivaLog', $arrayParametros['intIdNotifMasivaLog']);
            }
            
            if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
            {
                $strWhere   .= "INMLD.LOGIN LIKE :strLogin AND ";
                $objNtvQuery->setParameter('strLogin', $arrayParametros['strLogin'].'%');
            }
            
            if(isset($arrayParametros["strNombres"]) && !empty($arrayParametros["strNombres"]))
            {
                $strWhere   .= "INMLD.NOMBRES LIKE :strNombres AND ";
                $objNtvQuery->setParameter('strNombres', '%'.$arrayParametros['strNombres'].'%');
            }
                       
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   .= "INMLD.ESTADO = :strEstado AND ";
                $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }
            
            if(!empty($strWhere))
            {
                $strWhere = "WHERE ".substr($strWhere, 0, -4);
            }
            
            $strQueryFinal  =  $strSelect . $strFrom . $strWhere .$strOrderBy;
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
     * Método que obtiene el json de los detalles de los logs de las notificaciones masivas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intStart"              => inicio del rownum
     *                                  "intLimit"              => fin del rownum
     *                                  "intIdNotifMasivaLog"   => id del log de la notificación masiva
     *                                  "strLogin"              => login del cliente
     *                                  "strNombres"            => nombres del contacto del cliente,
     *                                  "strEstado"             => estado del envío
     *                              ]
     * 
     * @return string $strJsonData
     */
    public function getJSONNotificacionesMasivasLogsDets($arrayParametros)
    {
        $arrayRespuestaNotifMasivasLogsDets = $this->getNotificacionesMasivasLogsDets($arrayParametros);
        $arrayResultadoNotifMasivasLogsDets = $arrayRespuestaNotifMasivasLogsDets['arrayResultado'];
        $intTotalNotifMasivasLogsDets       = $arrayRespuestaNotifMasivasLogsDets['intTotal'];
        $strJsonData                        = json_encode(array('intTotal'          => $intTotalNotifMasivasLogsDets, 
                                                                'arrayResultado'    => $arrayResultadoNotifMasivasLogsDets));
        return $strJsonData;
    }
}

