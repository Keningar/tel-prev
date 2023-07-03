<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleSolCaractRepository extends EntityRepository
{
	public function findDetalleSolCaractPorIdDetalleSolicitud($idDetalleSolicitud){
		$query = $this->_em->createQuery("SELECT a
		FROM 
			schemaBundle:InfoDetalleSolCaract a, schemaBundle:AdmiTipoSolicitud b
		WHERE 
			a.detalleSolicitudId = ".$idDetalleSolicitud." ");
		//echo $query->getSQL();die;
		$datos = $query->getResult();
		return $datos;
	}
    
    /**
      * getSolicitudCaractPorTipoCaracteristica
      *
      * Método que obtiene la caracteristica de la solicitud por tipo caracteristica
      * 
      * @param $idDetalleSolicitud    
      * @param $tipoCaracteristica 
      *                                                                             
      * @return array con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 24-02-2016 - Agregar condicional de estado a query
      */     
    public function getSolicitudCaractPorTipoCaracteristica($idDetalleSolicitud, $tipoCaracteristica)
    {                
        $query = $this->_em->createQuery();
        $dql ="
                        SELECT a                                                
                        FROM                                
                        schemaBundle:InfoDetalleSolCaract a,
                        schemaBundle:AdmiCaracteristica b                        
                        WHERE               
                        a.caracteristicaId          =  b.id and                        
                        b.descripcionCaracteristica =  :descripcion and
                        a.detalleSolicitudId        =  :solicitud and
                        a.estado                    =  :estado
                        ";
        
        $query->setParameter('descripcion', $tipoCaracteristica);
        $query->setParameter('solicitud', $idDetalleSolicitud);
        $query->setParameter('estado', "Activo");

        $query->setDQL($dql);      

        $datos = $query->getResult();

        return $datos;
    }
    
    /**
     * getJSONSolicitudesPorDetSolCaracts
     * 
     * Obtiene las solicitudes de acuerdo a los parámetros enviados en formato json
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-10-2018
     * 
     * @param array $arrayParametros [
     *                                  "strEstadoSolicitud"            => estado de la solicitud y sus detalles características
     *                                  "arrayEstadosSolicitudes"       => arreglo con el estado de la solicitud y sus detalles características
     *                                  "intValorDetSolCaract"          => valor del detalle solicitud característica,
     *                                  "strDescripcionSolicitud"       => descripción del tipo de solicitud,
     *                                  "strDescripcionCaracteristica"  => descripción de la característica,
     *                                  "strConServicio"                => si la solicitud está asociado un servicio,
     *                                  "intIdDetalleSolicitud"         => id de la solicitud,
     *                                  "strBuscarServiciosAsociados"   => si se desea buscar todos los servicios vinculados a la solicitud,
     *                                  "intStart"                      => inicio del rownum,
     *                                  "intLimit"                      => límite del rownum
     *                               ]
     * 
     * @return return json $strJsonData
     */
    public function getJSONSolicitudesPorDetSolCaracts($arrayParametros)
    {  
        $arrayRespuesta             = $this->getSolicitudesPorDetSolCaracts($arrayParametros);
        $arrayResultado             = $arrayRespuesta['arrayResultado'];
        $intTotal                   = $arrayRespuesta['intTotal'];
        $strJsonData                = json_encode(array('intTotal'   => $intTotal, 'arrayResultado' => $arrayResultado));
        return $strJsonData;
    }
    
    /**
     * 
     * getSolicitudesPorDetSolCaracts
     * 
     * Función que obtiene las solicitudes de acuerdo a los parámetros enviados
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-10-2018
     * 
     * @param array $arrayParametros [
     *                                  "strEstadoSolicitud"            => estado de la solicitud y sus detalles características
     *                                  "arrayEstadosSolicitudes"       => arreglo con los estados de la solicitud y sus detalles características
     *                                  "intValorDetSolCaract"          => valor del detalle solicitud característica,
     *                                  "strDescripcionSolicitud"       => descripción del tipo de solicitud,
     *                                  "strDescripcionCaracteristica"  => descripción de la característica,
     *                                  "strConServicio"                => si la solicitud está asociado un servicio,
     *                                  "intIdDetalleSolicitud"         => id de la solicitud,
     *                                  "strBuscarServiciosAsociados"   => si se desea buscar todos los servicios vinculados a la solicitud,
     *                                  "intStart"                      => inicio del rownum,
     *                                  "intLimit"                      => límite del rownum
     *                               ]
     * @return array $arrayRespuesta [  
     *                                  "intTotal"          => número total de registros,
     *                                  "arrayResultado"    => registros obtenidos de la consulta
     *                               ]
     * 
     */
    public function getSolicitudesPorDetSolCaracts($arrayParametros)
    {
        $strMensaje = "";
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
            $strSelect      = " SELECT SOLICITUD.ID_DETALLE_SOLICITUD, TIPO_SOLICITUD.DESCRIPCION_SOLICITUD,
                                DET_SOL_CARACT.ID_SOLICITUD_CARACTERISTICA, CARACT.DESCRIPCION_CARACTERISTICA, 
                                DET_SOL_CARACT.VALOR, SOLICITUD.ESTADO, COALESCE(TO_CHAR(SOLICITUD.FE_CREACION,'DD-MM-YYYY'),'') AS FE_CREACION_SOL ";
            $strSelectCount = " SELECT COUNT(ID_DETALLE_SOLICITUD) AS TOTAL ";
            
            
            
           
            
            if(isset($arrayParametros["strDescripcionSolicitud"]) && $arrayParametros["strDescripcionSolicitud"] == "SOLICITUD INSPECCION")
            {
                $strFrom = " FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOLICITUD
                INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT_DET DET_SOL_CARACT
                ON DET_SOL_CARACT.DETALLE_SOLICITUD_ID = SOLICITUD.ID_DETALLE_SOLICITUD
                INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
                ON CARACT.ID_CARACTERISTICA = DET_SOL_CARACT.CARACTERISTICA_ID
                INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOLICITUD
                ON TIPO_SOLICITUD.ID_TIPO_SOLICITUD = SOLICITUD.TIPO_SOLICITUD_ID
                ";
            }else
            {
                $strFrom = " FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOLICITUD
                                INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT DET_SOL_CARACT
                                ON DET_SOL_CARACT.DETALLE_SOLICITUD_ID = SOLICITUD.ID_DETALLE_SOLICITUD
                                INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
                                ON CARACT.ID_CARACTERISTICA = DET_SOL_CARACT.CARACTERISTICA_ID
                                INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOLICITUD
                                ON TIPO_SOLICITUD.ID_TIPO_SOLICITUD = SOLICITUD.TIPO_SOLICITUD_ID
                                ";
            }
            
            $strWhere       = " WHERE TIPO_SOLICITUD.ESTADO = :strEstadoActivo
                                AND CARACT.ESTADO = :strEstadoActivo ";
            $objNtvQuery->setParameter('strEstadoActivo', "Activo");
            
            if(isset($arrayParametros["strEstadoSolicitud"]) && !empty($arrayParametros["strEstadoSolicitud"]))
            {
                $strWhere .= " AND SOLICITUD.ESTADO = :strEstadoSolicitud ";
                $strWhere .= " AND DET_SOL_CARACT.ESTADO = :strEstadoSolicitud ";
                $objNtvQuery->setParameter('strEstadoSolicitud', $arrayParametros['strEstadoSolicitud']);
            }
            if(isset($arrayParametros["arrayEstadosSolicitudes"]) && !empty($arrayParametros["arrayEstadosSolicitudes"]))
            {
                $strWhere .= " AND SOLICITUD.ESTADO IN (:arrayEstadosSolicitudes) ";
                $strWhere .= " AND DET_SOL_CARACT.ESTADO IN (:arrayEstadosSolicitudes) ";
                $objNtvQuery->setParameter('arrayEstadosSolicitudes', array_values($arrayParametros['arrayEstadosSolicitudes']));
            }
            if(isset($arrayParametros["intValorDetSolCaract"]) && !empty($arrayParametros["intValorDetSolCaract"]))
            {
                $strWhere .= " AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(DET_SOL_CARACT.VALOR,'^\d+')),0) = :intValorDetSolCaract ";
                $objNtvQuery->setParameter('intValorDetSolCaract', $arrayParametros['intValorDetSolCaract']);
            }
            if(isset($arrayParametros["strDescripcionSolicitud"]) && !empty($arrayParametros["strDescripcionSolicitud"]))
            {
                $strWhere .= " AND TIPO_SOLICITUD.DESCRIPCION_SOLICITUD = :strDescripcionSolicitud ";
                $objNtvQuery->setParameter('strDescripcionSolicitud', $arrayParametros['strDescripcionSolicitud']);
            }
            if(isset($arrayParametros["strDescripcionCaracteristica"]) && !empty($arrayParametros["strDescripcionCaracteristica"]))
            {
                $strWhere .= " AND CARACT.DESCRIPCION_CARACTERISTICA = :strDescripcionCaracteristica ";
                $objNtvQuery->setParameter('strDescripcionCaracteristica', $arrayParametros['strDescripcionCaracteristica']);
            }
            if(isset($arrayParametros["intIdDetalleSolicitud"]) && !empty($arrayParametros["intIdDetalleSolicitud"]))
            {
                $strWhere .= " AND SOLICITUD.ID_DETALLE_SOLICITUD = :intIdDetalleSolicitud ";
                $objNtvQuery->setParameter('intIdDetalleSolicitud', $arrayParametros['intIdDetalleSolicitud']);
            }
            if(isset($arrayParametros["strConServicio"]) && !empty($arrayParametros["strConServicio"]) && $arrayParametros["strConServicio"] === "SI")
            {
                $strSelect      .= ", SERVICIO.ID_SERVICIO, PUNTO.ID_PUNTO, PUNTO.LOGIN ";
                $strFrom        .= "INNER JOIN DB_COMERCIAL.INFO_SERVICIO SERVICIO
                                    ON SERVICIO.ID_SERVICIO = SOLICITUD.SERVICIO_ID 
                                    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO 
                                    ON PUNTO.ID_PUNTO = SERVICIO.PUNTO_ID ";
                $strWhere       .= " AND SOLICITUD.SERVICIO_ID IS NOT NULL ";
                $objRsm->addScalarResult('ID_SERVICIO', 'idServicioSolicitud', 'integer');
                $objRsm->addScalarResult('ID_PUNTO', 'idPuntoSolicitud', 'integer');
                $objRsm->addScalarResult('LOGIN', 'loginPuntoSolicitud', 'string');
                
                if(isset($arrayParametros["strBuscarServiciosAsociados"]) && !empty($arrayParametros["strBuscarServiciosAsociados"])
                    && $arrayParametros["strBuscarServiciosAsociados"] === "SI")
                {
                    $strSelect  .= ", PUNTOS_ASOCIADOS.LOGIN AS LOGIN_PUNTO_ASOCIADO, SERVICIOS_ASOCIADOS.ID_SERVICIO AS ID_SERVICIO_ASOCIADO,
                                    SERVICIOS_ASOCIADOS.ESTADO AS ESTADO_SERVICIO_ASOCIADO ";
                    $strFrom    .= "INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER
                                    ON PER.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID 
                                    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTOS_ASOCIADOS
                                    ON PUNTOS_ASOCIADOS.PERSONA_EMPRESA_ROL_ID = PER.ID_PERSONA_ROL 
                                    INNER JOIN DB_COMERCIAL.INFO_SERVICIO SERVICIOS_ASOCIADOS 
                                    ON SERVICIOS_ASOCIADOS.PUNTO_ID = PUNTOS_ASOCIADOS.ID_PUNTO ";
                    $strWhere   .= " AND SERVICIO.PRODUCTO_ID = SERVICIOS_ASOCIADOS.PRODUCTO_ID ";
                    $objRsm->addScalarResult('LOGIN_PUNTO_ASOCIADO', 'loginPuntoAsociado', 'string');
                    $objRsm->addScalarResult('ID_SERVICIO_ASOCIADO', 'idServicioAsociado', 'integer');
                    $objRsm->addScalarResult('ESTADO_SERVICIO_ASOCIADO', 'estadoServicioAsociado', 'string');
                    
                    if(isset($arrayParametros["strTieneEstadoServicioSol"]) && !empty($arrayParametros["strTieneEstadoServicioSol"])
                        && $arrayParametros["strTieneEstadoServicioSol"] === "SI")
                    {
                        $strWhere   .= " AND SERVICIO.ESTADO = SERVICIOS_ASOCIADOS.ESTADO ";
                    }
                    
                    if(isset($arrayParametros["arrayEstadosServiciosNotIn"]) && !empty($arrayParametros["arrayEstadosServiciosNotIn"]))
                    {
                        $strWhere   .= " AND SERVICIOS_ASOCIADOS.ESTADO NOT IN (:arrayEstadosServiciosNotIn) ";
                        $objNtvQuery->setParameter('arrayEstadosServiciosNotIn', array_values($arrayParametros["arrayEstadosServiciosNotIn"]));
                    }
                }
            }
            $objRsm->addScalarResult('ID_DETALLE_SOLICITUD', 'idSolicitud', 'integer');
            $objRsm->addScalarResult('DESCRIPCION_SOLICITUD', 'descripcionSolicitud', 'string');
            $objRsm->addScalarResult('ID_SOLICITUD_CARACTERISTICA', 'idDetSolCaract', 'integer');
            $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA', 'descripcionCaract', 'string');
            $objRsm->addScalarResult('FE_CREACION_SOL', 'fechaCreacionSolicitud', 'string');
            $objRsm->addScalarResult('VALOR', 'valorDetSolCaract', 'string');
            $objRsm->addScalarResult('ESTADO', 'estadoSolicitud', 'string');
            $objRsm->addScalarResult('TOTAL', 'total', 'integer');
            
            $strSqlPrincipal = $strSelect.$strFrom.$strWhere;

            $intStart = $arrayParametros['intStart'] ? $arrayParametros['intStart'] : 0;
            $intLimit = $arrayParametros['intLimit'] ? $arrayParametros['intLimit'] : 0;
            if($intLimit > 0)
            {
                $strSqlPrincipal = 'SELECT a.*, rownum AS doctrine_rownum FROM (' . $strSqlPrincipal . ') a WHERE rownum <= :doctrine_limit';
                $objNtvQuery->setParameter('doctrine_limit', $intLimit + $intStart);
                if($intStart > 0)
                {
                    $strSqlPrincipal = 'SELECT * FROM (' . $strSqlPrincipal . ') WHERE doctrine_rownum >= :doctrine_start';
                    $objNtvQuery->setParameter('doctrine_start', $intStart + 1);
                }
            }
            $objNtvQuery->setSQL($strSqlPrincipal);
            $arrayResultado     = $objNtvQuery->getResult();
            $strSqlCount        = $strSelectCount . " FROM (" . $strSelect.$strFrom.$strWhere . ")";
            $objNtvQuery->setSQL($strSqlCount);
            $intTotal       = $objNtvQuery->getSingleScalarResult();

            $arrayRespuesta['arrayResultado']   = $arrayResultado;
            $arrayRespuesta['intTotal']         = $intTotal;
            $strStatus                          = "OK";
            
        } 
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            $strMensaje     = $e->getMessage();
            $intTotal       = 0;
            $arrayResultado = array();
            $strStatus      = "ERROR";
            
        }
        $arrayRespuesta = array(
                                'strStatus'         => $strStatus,
                                'strMensaje'        => $strMensaje,
                                'intTotal'          => $intTotal,
                                'arrayResultado'    => $arrayResultado);
        return $arrayRespuesta;
    }
}
