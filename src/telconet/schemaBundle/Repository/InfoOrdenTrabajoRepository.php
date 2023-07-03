<?php

namespace telconet\schemaBundle\Repository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoOrdenTrabajoRepository extends EntityRepository
{
    /**
     * Documentación para el método 'getOrdenes'.
     *
     * Función que retorna las ordenes de trabajo por empresa y por estado
     * Adicional:
     * Se agrega logica para retornar información de acuerdo
     * a la caracteristica de la persona en sesion por medio de las siguiente 
     * descripciones de caracteristica:
     * 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     * Estos cambios solo aplican para Telconet
     * 
     * @param mixed $arrayParametros[
     *                               'strPrefijoEmpresa'     => prefijo de la empresa en sesion
     *                               'strTipoPersonal'       => tipo de la persona en sesion
     *                               'intIdPersonEmpresaRol' => id de la persona en sesion
     *                               'intIdOficina'          => id oficina en sesion
     *                               'intIdEmpresa'          => id empresa en sesion
     *                               'strEstado'             => estado
     *                               'strFechaInicio'        => fecha inicio de creación
     *                               'strFechaFin'           => fecha fin de creación
     *                               'intLimit'              => numero de limite para el grid
     *                               'intStart'              => numero de inicio para el grid
     *                               ]
     * 
     * @return response
     *
     * @author: Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 26-12-2018
     *
     */
    public function getOrdenes($arrayParametros)
    {
        $objQuery              = $this->_em->createQuery();
        $strFechaInicio        = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                   ? $arrayParametros['strFechaInicio'] : null;
        $strFechaFin           = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                   ? $arrayParametros['strFechaFin'] : null;
        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';

        $strSelect  = "SELECT iot ";
        $strFrom    = "FROM schemaBundle:InfoOrdenTrabajo iot,schemaBundle:InfoOficinaGrupo iog ";
        $strWhere   = "WHERE iot.oficinaId    = iog.id
                            AND iot.estado    = :strEstado
                            AND iog.empresaId = :intIdEmpresa
                            AND iot.oficinaId = :intIdOficina ";
        $strOrderBy = "ORDER by iot.feCreacion DESC ";
        if( !empty($strFechaInicio) && !empty($strFechaFin) )
        {
            $strWhere .= " AND iot.feCreacion >= '".$strFechaInicio."' 
                          AND iot.feCreacion <= '".$strFechaFin."' ";
        }
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            $strFrom     .= ",schemaBundle:InfoPunto ipu ";
            $strWhere   .=" AND ipu.id = iot.puntoId ";
            $strQueryIn   = " ";
            if( $strTipo == 'SUBGERENTE' )
            {
                $strQueryIn = " AND ipu.usrVendedor IN
                                (SELECT ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.estado                        = :strEstadoActivo
                                    AND ipervend.personaId                   = ipvend.id
                                    AND ipvend.estado                        = :strEstadoActivo
                                    AND (ipervend.reportaPersonaEmpresaRolId = :intIdPersonEmpresaRol
                                    OR ipervend.id                           = :intIdPersonEmpresaRol))
                              ";
            }
            elseif( $strTipo == 'ASISTENTE' )
            {
                $strQueryIn = " AND ipu.usrVendedor IN
                                (select ipvend.login
                                    from schemaBundle:InfoPersonaEmpresaRolCarac ipercvend ,
                                      schemaBundle:AdmiCaracteristica acvend ,
                                      schemaBundle:InfoPersona ipvend
                                WHERE ipercvend.personaEmpresaRolId          = :intIdPersonEmpresaRol
                                        and acvend.id                        = ipercvend.caracteristicaId
                                        and ipvend.id                        = ipercvend.valor
                                        AND acvend.descripcionCaracteristica = :strDescripcion
                                        AND acvend.estado                    = :strEstadoActivo
                                        AND ipercvend.estado                 = :strEstadoActivo
                                        AND ipvend.estado                    = :strEstadoActivo )
                              ";
                $objQuery->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strQueryIn = " AND ipu.usrVendedor IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = :intIdPersonEmpresaRol
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = :strEstadoActivo
                                    AND ipvend.estado      = :strEstadoActivo)
                              ";
            }
            $objQuery->setParameter('strEstadoActivo', $strEstadoActivo);
            $objQuery->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }

        $objQuery->setParameter('strEstado'   , $arrayParametros['strEstado']);
        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('intIdOficina', $arrayParametros['intIdOficina']);
        $strSql  = $strSelect . $strFrom . $strWhere . $strQueryIn . $strOrderBy;
        $objQuery->setDQL($strSql);

        $intTotal = count($objQuery->getResult());

        if( $arrayParametros['intStart']!='' )
        {
            $objQuery->setFirstResult($arrayParametros['intStart']);
        }
        if( $arrayParametros['intLimit']!='' )
        {
            $objQuery->setMaxResults($arrayParametros['intLimit']);
        }
        $objDatos = $objQuery->getResult();
        $arrayResultado['registros'] = $objDatos;
        $arrayResultado['total']     = $intTotal;

        return $arrayResultado;
    }
    public function find30OrdenesPorEmpresaPorEstado($idOficina,$idEmpresa,$estado,$limit, $page, $start){	
                $query = $this->_em->createQuery("SELECT iot
		FROM 
                schemaBundle:InfoOrdenTrabajo iot,schemaBundle:InfoOficinaGrupo iog
		WHERE 
                iot.oficinaId=iog.id AND
                iot.estado='".$estado."' AND
                iog.empresaId='".$idEmpresa."' AND
                iot.oficinaId=".$idOficina." ORDER by iot.feCreacion DESC")->setMaxResults(30);
		$datos = $query->getResult();
                //echo $query->getSQL();
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }
    
    public function findOrdenesPorCriterios($idOficina,$idEmpresa,$fechaDesde,$fechaHasta,$estado,$limit, $page, $start){	
                $query = $this->_em->createQuery("SELECT iot
		FROM 
                schemaBundle:InfoOrdenTrabajo iot,schemaBundle:InfoOficinaGrupo iog
		WHERE 
                iot.oficinaId=iog.id AND
                iog.empresaId='".$idEmpresa."' AND
                iot.oficinaId=".$idOficina." AND
                iot.estado='".$estado."' AND
                iot.feCreacion >= '".$fechaDesde."' AND 
                iot.feCreacion <= '".$fechaHasta."' ORDER by iot.feCreacion DESC");
                $datos = $query->getResult();
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }    
    
    /**
     * getResultadoOrdenesTrabajoVehiculo
     * 
     * Obtiene las órdenes de trabajo de un vehículo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-08-2016
     * 
     * @param  array $arrayParametros[
     *                                  "idElemento"                : id del vehículo
     *                                  "tipoDocumentoGeneralId"    : id del tipo de documento general
     *                                  "idCaractTipoMantenimiento" : id de la característica del tipo de mantenimiento
     *                                  "idCaractKmActual"          : id de la característica del km actual
     *                                  "idCaractNumeracion"        : id de la característica de la numeración
     *                                  "intStart"                  : inicio del rownum
     *                                  "intLimit"                  : fin del rownum
     *                               ]    
     * 
     * @return json $arrayRespuesta
     */
    public function getResultadoOrdenesTrabajoVehiculo($arrayParametros)
    {

        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";

        try
        {
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);
            
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);

            $strSelect = " SELECT distinct iot.ID_ORDEN_TRABAJO,iot.NUMERO_ORDEN_TRABAJO,elemento.ID_ELEMENTO,idr.ID_DOCUMENTO_RELACION, "
                        . " idc.ID_DOCUMENTO,idc.UBICACION_FISICA_DOCUMENTO, idc.UBICACION_LOGICA_DOCUMENTO,idc.USR_CREACION, "
                        . " caractTipoMant.VALOR as TIPO_MANTENIMIENTO, caractKm.VALOR as KM_ACTUAL,caractNumeracion.VALOR as VER_NUMERACION_OT,"
                        . " iot.FE_CREACION, iot.FE_INICIO, iot.FE_FIN ";
            

            $strFrom =" FROM 
                        DB_INFRAESTRUCTURA.INFO_ELEMENTO elemento  
                        INNER JOIN DB_COMERCIAL.INFO_ORDEN_TRABAJO iot ON elemento.ID_ELEMENTO = iot.ELEMENTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_ORDEN_TRABAJO_CARACT caractTipoMant ON iot.ID_ORDEN_TRABAJO = caractTipoMant.ORDEN_TRABAJO_ID
                        INNER JOIN DB_COMERCIAL.INFO_ORDEN_TRABAJO_CARACT caractKm ON iot.ID_ORDEN_TRABAJO = caractKm.ORDEN_TRABAJO_ID
                        INNER JOIN DB_COMERCIAL.INFO_ORDEN_TRABAJO_CARACT caractNumeracion 
                            ON iot.ID_ORDEN_TRABAJO = caractNumeracion.ORDEN_TRABAJO_ID
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION idr ON iot.ID_ORDEN_TRABAJO = idr.ORDEN_TRABAJO_ID
                        INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO idc ON idc.ID_DOCUMENTO = idr.DOCUMENTO_ID 
                        INNER JOIN DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL atdg ON atdg.ID_TIPO_DOCUMENTO = idc.TIPO_DOCUMENTO_GENERAL_ID
                        WHERE elemento.ESTADO= :estadoActivo 
                        AND caractTipoMant.ESTADO= :estadoActivo
                        AND caractKm.ESTADO = :estadoActivo
                        AND caractTipoMant.CARACTERISTICA_ID = :idCaractTipoMantenimiento 
                        AND caractKm.CARACTERISTICA_ID = :idCaractKmActual 
                        AND caractNumeracion.CARACTERISTICA_ID = :idCaractNumeracion ";

            $rsm->addScalarResult('ID_ORDEN_TRABAJO', 'idOrdenTrabajo','integer');
            $rsm->addScalarResult('ID_DOCUMENTO_RELACION','idDocumentoRelacion','integer');
            $rsm->addScalarResult('ID_ELEMENTO','idElemento','integer');
            $rsm->addScalarResult('ID_DOCUMENTO','idDocumento','integer');
            $rsm->addScalarResult('NUMERO_ORDEN_TRABAJO','numeroOrdenTrabajo','string');
            $rsm->addScalarResult('VER_NUMERACION_OT','verNumeracionOT','string');
            $rsm->addScalarResult('UBICACION_FISICA_DOCUMENTO', 'ubicacionFisicaDocumento','string');
            $rsm->addScalarResult('UBICACION_LOGICA_DOCUMENTO', 'ubicacionLogicaDocumento','string');
            $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $rsm->addScalarResult('TIPO_MANTENIMIENTO', 'tipoMantenimiento','string');
            $rsm->addScalarResult('KM_ACTUAL', 'kmActual','string');
            $rsm->addScalarResult('FE_CREACION', 'feCreacion','datetime');
            $rsm->addScalarResult('FE_INICIO', 'feInicio','datetime');
            $rsm->addScalarResult('FE_FIN', 'feFin','datetime');
            
            $rsmCount->addScalarResult('TOTAL','total','integer');
            
            $strWhere = "";
            
            
            if(isset($arrayParametros['idElemento']) )
            {
                if($arrayParametros['idElemento'])
                {
                    $strWhere .= 'AND idr.ELEMENTO_ID = :idElemento ';        
                    $ntvQuery->setParameter('idElemento', $arrayParametros['idElemento']);
                    $ntvQueryCount->setParameter('idElemento', $arrayParametros['idElemento']);
                }
            }
            
            if(isset($arrayParametros["tipoDocumentoGeneralId"]))
            {
                if($arrayParametros['tipoDocumentoGeneralId'])
                {
                    $strWhere  .=    " AND idc.TIPO_DOCUMENTO_GENERAL_ID = :tipoDocumentoGeneralId ";
                    $ntvQuery->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                    $ntvQueryCount->setParameter('tipoDocumentoGeneralId', $arrayParametros["tipoDocumentoGeneralId"]);
                }
            }
            
            $ntvQuery->setParameter('estadoActivo', 'Activo');
            $ntvQueryCount->setParameter('estadoActivo', 'Activo');
            
            $ntvQuery->setParameter('idCaractTipoMantenimiento', $arrayParametros["idCaractTipoMantenimiento"]);
            $ntvQueryCount->setParameter('idCaractTipoMantenimiento', $arrayParametros["idCaractTipoMantenimiento"]);
            
            $ntvQuery->setParameter('idCaractKmActual', $arrayParametros["idCaractKmActual"]);
            $ntvQueryCount->setParameter('idCaractKmActual', $arrayParametros["idCaractKmActual"]);
            
            $ntvQuery->setParameter('idCaractKmActual', $arrayParametros["idCaractKmActual"]);
            $ntvQueryCount->setParameter('idCaractKmActual', $arrayParametros["idCaractKmActual"]);
            
            $ntvQuery->setParameter('idCaractNumeracion', $arrayParametros["idCaractNumeracion"]);
            $ntvQueryCount->setParameter('idCaractNumeracion', $arrayParametros["idCaractNumeracion"]);
            
            $strOrderBy=" ORDER BY iot.ID_ORDEN_TRABAJO ASC ";
            
            $strSqlPrincipal = $strSelect.$strFrom.$strWhere.$strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();
            
            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);
            
            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
     
    
    
    /**
     * getJSONOrdenesTrabajoVehiculo, Obtiene las órdenes de trabajo de un vehículo 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-08-2016
     * 
     * @param  array $arrayParametros[
     *                                  "idElemento"                : id del vehículo
     *                                  "tipoDocumentoGeneralId"    : id del tipo de documento general
     *                                  "idCaractTipoMantenimiento" : id de la característica del tipo de mantenimiento
     *                                  "idCaractKmActual"          : id de la característica del km actual
     *                                  "idCaractNumeracion"        : id de la característica de la numeración
     *                                  "intStart"                  : inicio del rownum
     *                                  "intLimit"                  : fin del rownum
     *                               ]
     * 
     * @return json $jsonData
     */
    public function getJSONOrdenesTrabajoVehiculo($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoOrdenesTrabajoVehiculo($arrayParametros);
        $resultado = $arrayResultado['resultado'];
        $intTotal = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[] = array(
                                            "idOrdenTrabajo"            => $data['idOrdenTrabajo'],
                                            "verNumeracionOT"           => $data['verNumeracionOT'],
                                            "numeroOrdenTrabajo"        => $data['verNumeracionOT']=="SI" ? $data['numeroOrdenTrabajo']:"",
                                            "kilometraje"               => number_format( $data['kmActual'] ,  0 , "," , "." ),
                                            "tipoMantenimiento"         => $data['tipoMantenimiento'],
                                            "idDocumentoRelacion"       => $data['idDocumentoRelacion'],
                                            "idElemento"                => $data['idElemento'],
                                            "idDocumento"               => $data['idDocumento'],
                                            "ubicacionLogicaDocumento"  => $data['ubicacionLogicaDocumento'],
                                            "ubicacionFisicaDocumento"  => $data['ubicacionFisicaDocumento'],
                                            "usrCreacion"               => $data['usrCreacion'],
                                            "feCreacion"                => $data["feCreacion"] ? 
                                                                           strval(date_format($data["feCreacion"],"d-m-Y H:i")):"",
                                            "feInicio"                  => $data["feInicio"] ? strval(date_format($data["feInicio"],"d-m-Y")):"",
                                            "feFin"                     => $data["feFin"] ? strval(date_format($data["feFin"],"d-m-Y")):""
                );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    
}
