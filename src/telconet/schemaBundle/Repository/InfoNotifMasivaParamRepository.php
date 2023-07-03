<?php

namespace telconet\schemaBundle\Repository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoNotifMasivaParamRepository extends BaseRepository
{
    /**
     * Función que sirve para obtener los parámetros de los envíos masivos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "strDatabaseDsn"            => DSN de la base
     *                                  "strUserComunicacion"       => usuario de comunicación
     *                                  "strPasswordComunicacion"   => password del usuario de comunicación
     *                                  "intIdNotifMasiva"          => id de la notificación masiva
     *                                  "strTipo"                   => tipo de envío de la notificación masiva
     *                              ]
     * @return array $arrayRespuesta
     */
    public function getParamsNotificacionesMasivas($arrayParametros)
    {
        $arrayRespuesta = array();
        try
        {
            $intIdNotifMasiva           = ( isset($arrayParametros['intIdNotifMasiva']) && !empty($arrayParametros['intIdNotifMasiva']) )  
                                            ? $arrayParametros['intIdNotifMasiva'] : 0;
            
            $strTipo                    = ( isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']) )  
                                            ? $arrayParametros['strTipo'] : "";
            
            $strDatabaseDsn             = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                            ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserComunicacion        = ( isset($arrayParametros['strUserComunicacion']) && !empty($arrayParametros['strUserComunicacion']) )
                                            ? $arrayParametros['strUserComunicacion'] : null;
            $strPasswordComunicacion    = ( isset($arrayParametros['strPasswordComunicacion']) 
                                            && !empty($arrayParametros['strPasswordComunicacion']) )
                                            ? $arrayParametros['strPasswordComunicacion'] : null;
            $strEstadoActivo            = "Activo";
            if( !empty($strDatabaseDsn) && !empty($strUserComunicacion) && !empty($strPasswordComunicacion) )
            {
                $objOciConexion                 = oci_connect($strUserComunicacion, $strPasswordComunicacion, $strDatabaseDsn);
                $arrayCursorParamsNotifMasiva   = oci_new_cursor($objOciConexion);
                $strSQL                         = "BEGIN 
                                                     :cursorParamsNotifMasiva := DB_COMUNICACION.CUKG_CONSULTS.F_GET_PARAMS_NOTIF_MASIVA(
                                                                                                                :intIdNotifMasiva,
                                                                                                                :strTipo,
                                                                                                                :strEstado); 
                                                   END;";
                $objStmt                    = oci_parse($objOciConexion,    $strSQL);
                oci_bind_by_name($objStmt, ":intIdNotifMasiva",             $intIdNotifMasiva);
                oci_bind_by_name($objStmt, ":strTipo",                      $strTipo);
                oci_bind_by_name($objStmt, ":strEstado",                    $strEstadoActivo);
                oci_bind_by_name($objStmt, ":cursorParamsNotifMasiva",  $arrayCursorParamsNotifMasiva, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($arrayCursorParamsNotifMasiva);

                if(!empty($arrayCursorParamsNotifMasiva))
                {
                    $arrayRegistrosParamsNotifMasiva    = array();
                    while(($arrayResultadoCursor        = oci_fetch_array($arrayCursorParamsNotifMasiva, OCI_ASSOC + OCI_RETURN_NULLS)))
                    {
                        $arrayRegistrosParamsNotifMasiva[$arrayResultadoCursor["NOMBRE"]]  = $arrayResultadoCursor["VALOR"];
                    }
                    $arrayRespuesta   = $arrayRegistrosParamsNotifMasiva;
                }
                oci_free_statement($objStmt);
                oci_free_statement($arrayCursorParamsNotifMasiva);
                oci_close($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información del envío masivo. Database('.
                                     $strDatabaseDsn.'), UsrComunicacion('.$strUserComunicacion.'), PassComunicacion('.
                                     $strPasswordComunicacion.').'); 
            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    
    /**
     * getJSONParamsNotificacionesMasivas
     * 
     * Función que sirve para obtener los parámetros de los envíos masivos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "strDatabaseDsn"            => DSN de la base
     *                                  "strUserComunicacion"       => usuario de comunicación
     *                                  "strPasswordComunicacion"   => password del usuario de comunicación
     *                                  "intIdNotifMasiva"      => id de la notificación masiva
     *                                  "strTipo"               => tipo de envío de la notificación masiva
     *                              ]
     * @return string $strJsonData    
     */  
    public function getJSONParamsNotificacionesMasivas($arrayParametros)
    {  
        $arrayRespuesta             = $this->getParamsNotificacionesMasivas($arrayParametros);
        $strJsonData                = json_encode(array('arrayRespuesta' => $arrayRespuesta));
        return $strJsonData;
    }
}

