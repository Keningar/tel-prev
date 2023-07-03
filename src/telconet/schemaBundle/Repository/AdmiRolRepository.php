<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiRolRepository extends EntityRepository
{
    public function getRolesByTipoRol($idTipoRol="1")
    {
        $query_string = "SELECT r
                         FROM schemaBundle:AdmiRol r
                         JOIN r.tipoRolId tr
                         WHERE tr.id = '$idTipoRol' 
                         ORDER BY r.descripcionRol ASC
                        ";
	return $this->_em->createQuery($query_string)->getResult();
    }
    
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_rol' =>$data->getId(),
                                         'descripcion_rol' =>trim($data->getDescripcionRol()),
                                         'descripcion_tipo_rol' =>trim($data->getTipoRolId()->getDescripcionTipoRol()),
                                         'es_jefe' =>(strtolower($data->getEsJefe())== strtolower('S') ? 'SI':'NO'),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_rol' => 0 , 'descripcion_rol' => 'Ninguno', 'descripcion_tipo_rol' => 'Ninguno',  'rol_id' => 0 , 'rol_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiRol','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionRol) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function getRolesByDescripcionTipoRol($descTipoRol)
    {
        $query_string = "SELECT r
                         FROM schemaBundle:AdmiRol r
                         JOIN r.tipoRolId tr
                         WHERE tr.descripcionTipoRol = '$descTipoRol' 
                         ORDER BY r.descripcionRol ASC
                        ";
	return $this->_em->createQuery($query_string)->getResult();
    }   
    
    
    /**
     * getRegistrosRolesEmpleadosXEmpresa
     *
     * Método que devuelve los roles de empleados por empresa y en estado Activo
     *
     * @param string $strCodEmpresa         
     * @param string $strNombre        
     *
     * @return array $arrayResultados
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 30-05-2014
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 16-11-2015 - Se modifica la validación de estado para que retorne los roles de los empleados que tengan estado 
     *                           diferente de 'Eliminado', o 'Inactivo'
     *
     * @author Modificado: Sofia Fernandez <sfernadnez@telconet.ec>
     * @version 1.2 03-03-2018 - Se agrega tipo de rol para personal externo
     */
    public function getRegistrosRolesEmpleadosXEmpresa( $strCodEmpresa, $strNombre = '' )
    {
        $arrayResultados = array();
        
	    if($strNombre && $strNombre!='')
        {
            $strWhere = " AND UPPER(b.descripcionRol) like UPPER(:nombre)";
        }
    
	    $strSql = "SELECT a.id, b.descripcionRol, b.esJefe
                   FROM schemaBundle:InfoEmpresaRol a,
                        schemaBundle:AdmiRol b,
                        schemaBundle:AdmiTipoRol c
                   WHERE a.rolId = b.id
                     AND b.tipoRolId = c.id
                     AND a.empresaCod = :empresa
                     AND c.descripcionTipoRol IN (:tipo)
                     AND a.estado != :estadoEliminado
                     AND a.estado != :estadoInactivo
                     AND b.estado != :estadoEliminado
                     AND b.estado != :estadoInactivo
                     AND c.estado != :estadoEliminado
                     AND c.estado != :estadoInactivo
                     $strWhere
                   GROUP BY b.descripcionRol, a.id , b.esJefe";
		   
	    $query = $this->_em->createQuery($strSql);
	    
	    $query->setParameter('empresa',         $strCodEmpresa);	    
	    $query->setParameter('estadoEliminado', 'Eliminado');	    
	    $query->setParameter('estadoInactivo',  'Inactivo');
	    $query->setParameter('tipo', ['Empleado', 'Personal Externo']);
    
	    if($strNombre && $strNombre!='')
        {
            $query->setParameter('nombre',  '%'.$strNombre.'%');
        }
        
        $arrayResultados = $query->getResult();
    
	    return $arrayResultados;
    }
    
    
    /**
      * generarJsonRolesEmpleadosXEmpresa
      *
      * Método que devuelve el json con los roles obtenidos con la ejecución del query
      *
      * @param string $codEmpresa         
      * @param string $nombre        
      *
      * @return JSON con valores a mostrar en el combobox
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 30-05-2014
      */
    public function generarJsonRolesEmpleadosXEmpresa($codEmpresa,$nombre)
    {
	    $registros = $this->getRegistrosRolesEmpleadosXEmpresa($codEmpresa,$nombre);
    
	    if ($registros) {
	  
		    $num = count($registros);            
		    foreach ($registros as $data)
		    {
			  $arr_encontrados[]=array('id_empresa_rol' =>$data['id'],
						  'descripcion_rol' =>ucwords(strtolower(trim($data['descripcionRol']))),
						  'es_jefe'=>$data['esJefe']);						  						  
		    }
		    $dataF =json_encode($arr_encontrados);
		    $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
		    return $resultado;
	    }
	    else
	    {
		    $resultado= '{"total":"0","encontrados":[]}';
		    return $resultado;
	    }
    }
    
    /**
      * getRolEmpleadoEmpresa
      *
      * Método que retorna la descripción del rol del empleado dentro de una empresa
      *
      * @param array $arrayParametros         
      *
      * @return string $strDescripcionRol
      *
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 13-10-2015
      */
    public function getRolEmpleadoEmpresa($arrayParametros)
    {
        $strDescripcionRol = '';
        
        $query = $this->_em->createQuery();
        
        $strSelect = 'SELECT ar.descripcionRol ';
        $strFrom   = 'FROM schemaBundle:InfoPersonaEmpresaRol iper,
                           schemaBundle:InfoEmpresaRol ier,
                           schemaBundle:AdmiRol ar,
                           schemaBundle:AdmiTipoRol atr '; 
        $strWhere  = 'WHERE iper.empresaRolId = ier.id 
                        AND ier.rolId = ar.id
                        AND ar.tipoRolId = atr.id
                        AND iper.estado not like :estadoEliminado 
                        AND iper.estado not like :estadoInactivo
                        AND iper.estado not like :estadoCancelado
                        AND ier.estado not like :estadoEliminado 
                        AND ar.estado not like :estadoEliminado 
                        AND atr.estado not like :estadoEliminado
                        AND iper.id = :usuarioRolId ';
        
        $query->setParameter('estadoEliminado'  , '%Eliminado%');
        $query->setParameter('estadoInactivo'   , '%Inactivo%');
        $query->setParameter('estadoCancelado'  , '%Cancelado%');
        $query->setParameter('usuarioRolId'     , $arrayParametros['usuario']);
        
        $strSql = $strSelect.$strFrom.$strWhere;
        
        $query->setDQL($strSql);
        
        $arrayRol = $query->getSingleResult();
        
        if( $arrayRol )
        {
            $strDescripcionRol = $arrayRol['descripcionRol'];
        }
        
        return $strDescripcionRol;
    }
    

    /**
     * getResultadoRolesPersona, obtiene los roles de una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayEmpresaRol'           => ['arrayEstado'] Recibe el estado de la empresa rol
     *                              'arrayRol'                  => ['arrayEstado'] Recible el estado del rol
     *                              'arrayPersonaEmpresaRol'    => ['arrayEstado', 'arrayPersona'] Recibe el id de la persona 
     *                                                              y el estado de la persona empresa rol
     *                              'intStart'                  => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                  => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getResultadoRolesPersona($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(ar.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT ar.id intIdRol, "
                             . "ar.descripcionRol strDescripcionRol, "
                             . "ar.usrCreacion strUsrCreacion, "
                             . "ar.usrUltMod strUsrUltMod, "
                             . "ar.feCreacion dateFeCreacion, "
                             . "ar.feUltMod dateFeUtlMod, "
                             . "iper.id intIdPersonaEmpresaRol, "
                             . "iper.usrCreacion strUsrCreacionIPER, "
                             . "iper.feCreacion dateFeCreacionIPER, "
                             . "iper.estado strEstadoIPER, "
                             . "ieg.id intIdEmpresa, "
                             . "ieg.prefijo strPrefijo ";

            $strFromQuery = "FROM schemaBundle:InfoPersonaEmpresaRol iper, "
                                . " schemaBundle:InfoEmpresaRol ier, "
                                . " schemaBundle:InfoEmpresaGrupo ieg, "
                                . " schemaBundle:AdmiRol ar "
                                . " WHERE ier.id       = iper.empresaRolId "
                                . " AND ier.empresaCod = ieg.id "
                                . " AND ar.id          = ier.rolId ";

            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIER', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaGrupo']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaGrupo']['arrayEmpresaGrupo']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.prefijo ';
                $arrayParams['strBindParam']    = ':arrayEmpresaGrupo';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaGrupo']['arrayEmpresaGrupo'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEmpresaGrupo', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEmpresaGrupo', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaEmpresaRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iper.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaEmpresaRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaEmpresaRol']['arrayPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iper.personaId ';
                $arrayParams['strBindParam']    = ':arrayPersonaIPER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaEmpresaRol']['arrayPersona'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersonaIPER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersonaIPER', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            $objQuery->setDQL($strQuery . $strFromQuery);
            //Pregunta si $arrayParametros['intStart'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            //Pregunta si $arrayParametros['intLimit'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            if($objReturnResponse->getRegistros())
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoRolesPersona - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getResultadoRolesPersona

    
    /**
     * getRolesPersonalCuadrillas
     *
     * Método que retorna la descripción de los roles asignados a los empleados que pertenecen a una cuadrilla o área técnica
     * 
     * @param array $arrayParametros
     *
     * @return array $arrayResultados 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-10-2015
     */
    public function getRolesPersonalCuadrillas($arrayParametros =  array())
    {
        $arrayResultados = array();
        
        $query      = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        
        $strSelect      = 'SELECT DISTINCT ar.id, ar.descripcionRol ';
        $strSelectCount = 'SELECT COUNT( DISTINCT ar.descripcionRol ) ';
        $strFrom        = 'FROM schemaBundle:InfoPersonaEmpresaRol iper,
                                schemaBundle:InfoPersona ip,
                                schemaBundle:InfoEmpresaRol ier,
                                schemaBundle:AdmiRol ar,
                                schemaBundle:AdmiTipoRol atr '; 
        $strWhere       = 'WHERE iper.empresaRolId = ier.id 
                             AND iper.personaId = ip.id
                             AND ier.rolId = ar.id
                             AND ar.tipoRolId = atr.id
                             AND ip.estado not like :estadoEliminado
                             AND ip.estado not like :estadoCancelado 
                             AND ip.estado not like :estadoInactivo
                             AND iper.estado not like :estadoCancelado 
                             AND iper.estado not like :estadoEliminado 
                             AND iper.estado not like :estadoInactivo
                             AND iper.estado not like :estadoCancelado
                             AND ier.estado not like :estadoEliminado 
                             AND ar.estado not like :estadoEliminado 
                             AND atr.estado not like :estadoEliminado
                             AND iper.cuadrillaId IS NOT NULL ';
        $strOrderBy      = 'ORDER BY ar.descripcionRol ';
        
        
        if( isset($arrayParametros['soloJefes']) )
        {
            if($arrayParametros['soloJefes'])
            {
                $strWhere .= "AND ar.esJefe = :soloJefes ";
                
                $query->setParameter('soloJefes', 'S');
        
                $queryCount->setParameter('soloJefes', 'S');
            }
        }
        
        
        $query->setParameter('estadoEliminado'  , '%Eliminado%');
        $query->setParameter('estadoInactivo'   , '%Inactivo%');
        $query->setParameter('estadoCancelado'  , '%Cancelado%');
        
        $queryCount->setParameter('estadoEliminado'  , '%Eliminado%');
        $queryCount->setParameter('estadoInactivo'   , '%Inactivo%');
        $queryCount->setParameter('estadoCancelado'  , '%Cancelado%');
        
        $strSql      = $strSelect.$strFrom.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;
        
        $query->setDQL($strSql);
        $queryCount->setDQL($strSqlCount);
        
        $arrayResultados['resultados'] = $query->getResult();
        $arrayResultados['total']      = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
    }
    
    /**
     * getResultadoRolesParametroDet
     * 
     * Obtiene los roles de acuerdo a los parámetros enviados en la consulta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  
     *                                  'strCodEmpresa'                     => id de la empresa
     *                                  'strDescripcionTipoRol'             => descripción del tipo rol
     *                                  'strDescripcionRol'                 => descripción del rol
     *                                  'arrayDescripCargosEnCuadrillas'    => array con los cargos que ya forman parte de los que se consideran 
     *                                                                         para la asignación de empleados en cuadrillas
     *                               ]
     * 
     * @return array $arrayRespuesta
     */
    public function getResultadoRolesParametroDet($arrayParametros)
    {
        $arrayRespuesta                     = array();
        $arrayRespuesta['arrayResultado']   = array();
        $arrayRespuesta['intTotal']         = 0;
            
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

            
            $strSelectCount     = " SELECT DISTINCT COUNT(AR.ID_ROL) AS TOTAL ";
            $strSelect          = " SELECT AR.ID_ROL, AR.DESCRIPCION_ROL, 
                                    (CASE AR.ES_JEFE WHEN  'S' THEN 'SI'
                                     WHEN 'N' THEN 'NO'
                                     ELSE '' END) AS ES_JEFE ";

            $strFromJoin        = " FROM DB_COMERCIAL.INFO_EMPRESA_ROL IER
                                    INNER JOIN DB_GENERAL.ADMI_ROL AR 
                                    ON IER.ROL_ID = AR.ID_ROL
                                    INNER JOIN DB_GENERAL.ADMI_TIPO_ROL ATR 
                                    ON ATR.ID_TIPO_ROL = AR.TIPO_ROL_ID ";
            
            $strWhere           = " WHERE IER.ESTADO <> :strEstadoEliminado 
                                    AND AR.ESTADO <> :strEstadoEliminado 
                                    AND ATR.ESTADO not like :strEstadoEliminado ";
                             
            $strOrderBy         = " ORDER BY AR.DESCRIPCION_ROL ";
            
            $objRsm->addScalarResult('ID_ROL', 'intIdRol', 'integer');
            $objRsm->addScalarResult('DESCRIPCION_ROL', 'strDescripcionRol', 'string');
            $objRsm->addScalarResult('ES_JEFE', 'strEsJefe', 'string');
            
            $objRsm->addScalarResult('TOTAL', 'total', 'integer');
            
            $objNtvQuery->setParameter('strEstadoEliminado'  , 'Eliminado');
            
            
            if(isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
            {
                $strWhere .= "AND IER.EMPRESA_COD = :strCodEmpresa ";
                $objNtvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
            }
            
            if(isset($arrayParametros['strDescripcionTipoRol']) && !empty($arrayParametros['strDescripcionTipoRol']))
            {
                $strWhere .= "AND ATR.DESCRIPCION_TIPO_ROL = :strDescripcionTipoRol ";
                $objNtvQuery->setParameter('strDescripcionTipoRol', $arrayParametros['strDescripcionTipoRol']);
            }
            
            if(isset($arrayParametros['strDescripcionRol']) && !empty($arrayParametros['strDescripcionRol']))
            {
                $strWhere .= "AND UPPER(AR.DESCRIPCION_ROL) LIKE :strDescripcionRol ";
                $objNtvQuery->setParameter('strDescripcionRol', '%'.strtoupper($arrayParametros['strDescripcionRol']).'%');
            }
            
            if(isset($arrayParametros['arrayDescCargosYaEnCuadrillas']) && !empty($arrayParametros['arrayDescCargosYaEnCuadrillas']))
            {
                $strWhere .= "AND AR.DESCRIPCION_ROL NOT IN (:arrayDescCargosYaEnCuadrillas) ";
                $objNtvQuery->setParameter('arrayDescCargosYaEnCuadrillas', array_values($arrayParametros['arrayDescCargosYaEnCuadrillas']));
            }
            
            
            if(isset($arrayParametros['arrayCriteriosInfoRolesCuadrillas']) && !empty($arrayParametros['arrayCriteriosInfoRolesCuadrillas']))
            {
                $arrayCriteriosInfoRolesCuadrillas = $arrayParametros['arrayCriteriosInfoRolesCuadrillas'];
                if(isset($arrayCriteriosInfoRolesCuadrillas['strGetInfoRolesEnCuadrillas']) 
                    && !empty($arrayCriteriosInfoRolesCuadrillas['strGetInfoRolesEnCuadrillas'])
                    && $arrayCriteriosInfoRolesCuadrillas['strGetInfoRolesEnCuadrillas']=="SI")
                {
                    $strSelect  .= ", APD.ID_PARAMETRO_DET, APD.VALOR1, APD.VALOR2 ";
                    $strFromJoin.= "INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                    ON APD.DESCRIPCION = AR.DESCRIPCION_ROL 
                                    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC 
                                    ON APC.ID_PARAMETRO = APD.PARAMETRO_ID ";
                    
                    $strWhere   .= "AND APD.ESTADO = :strEstadoActivo ";
                    
                    $objRsm->addScalarResult('ID_PARAMETRO_DET', 'intIdParametroDet', 'string');
                    $objRsm->addScalarResult('VALOR1', 'strFuncionRol', 'string');
                    $objRsm->addScalarResult('VALOR2', 'strFuncionaComoJefe', 'string');
                    
                    $objNtvQuery->setParameter('strEstadoActivo', 'Activo');

                    if(isset($arrayCriteriosInfoRolesCuadrillas['strNombreParametroCargos']) 
                        && !empty($arrayCriteriosInfoRolesCuadrillas['strNombreParametroCargos']))
                    {
                        $strWhere   .= "AND APC.NOMBRE_PARAMETRO = :strNombreParametroCargos ";
                        $objNtvQuery->setParameter('strNombreParametroCargos', $arrayCriteriosInfoRolesCuadrillas['strNombreParametroCargos']);
                    }
                }
            }
            
            
            
            $strQuery       = $strSelect . $strFromJoin . $strWhere . $strOrderBy;
            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getResult();

            $strQueryCount  = $strSelectCount . $strFromJoin . $strWhere;
            $objNtvQuery->setSQL($strQueryCount);
            $intTotal       = $objNtvQuery->getSingleScalarResult();

            $arrayRespuesta['arrayResultado']   = $arrayResultado;
            $arrayRespuesta['intTotal']         = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }
    
}
