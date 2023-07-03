<?php

namespace telconet\schemaBundle\Repository;

use Symfony\Component\Validator\Constraints\Length;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use \Datetime;
use telconet\schemaBundle\Entity\InfoLog;

class InfoPuntoRepository extends BaseRepository
{
    /**
     * Documentación para la función 'validarProvinciaPuntoVentaExterna'
     * 
     * Función que retorna el punto si la provincia del punto está incluída en los parámetros 'PRODUCTOS_VALIDO_CIERTAS_PROVINCIAS'.
     * 
     * @param  array $arrayParametros [ "arrayNombreProvincias" => "Array con los nombres de las provincias que se desea buscar"
     *                                  "intIdPunto"            => "Id del punto que se desea buscar" ]     
     * 
     * @return object $objInfoPunto
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-02-2017
     * Costo del query: 7
     */
    public function validarProvinciaPuntoVentaExterna($arrayParametros)
    {
        $objInfoPunto = null;
        
        try
        {
            if( isset($arrayParametros['arrayNombreProvincias']) && !empty($arrayParametros['arrayNombreProvincias']) 
                && isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']) )
            {
                $objQuery  = $this->_em->createQuery(); 
                $strSelect = "SELECT IP ";
                $strFrom   = "FROM schemaBundle:InfoPunto IP, ".
                             " schemaBundle:AdmiSector ASE, ".
                             " schemaBundle:AdmiParroquia AP, ".
                             " schemaBundle:AdmiCanton AC, ".
                             " schemaBundle:AdmiProvincia APR ";
                $strWhere  = "WHERE IP.sectorId = ASE.id ".
                             "AND ASE.parroquiaId = AP.id ".
                             "AND AP.cantonId = AC.id ".
                             "AND AC.provinciaId = APR.id ".
                             "AND APR.nombreProvincia IN ( :arrayNombreProvincias ) ".
                             "AND IP.id = :intIdPunto ";

                $objQuery->setParameter('arrayNombreProvincias', array_values($arrayParametros['arrayNombreProvincias']));
                $objQuery->setParameter('intIdPunto',            $arrayParametros['intIdPunto']);

                $strSql = $strSelect.$strFrom.$strWhere;
                $objQuery->setDQL($strSql);

                $objInfoPunto = $objQuery->getOneOrNullResult();
            }
            else
            {
                throw new \Exception('No se enviaron todos los parámetros adecuados a la función InfoRepository:validarProvinciaPuntoVentaExterna');
            }//( isset($arrayParametros['arrayNombreProvincias']) && !empty($arrayParametros['arrayNombreProvincias'])...
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        
        return $objInfoPunto;
    }


     /**
     * getResultadoServiciosVtaExterna
     * 
     * Obtiene Servicios de Tipo Venta Externa (ES_VENTA = 'E'), 
     * solo servicios que no posean asociado un documento digital de Tipo 'VTAEX' -> VENTA EXTERNA
     * y que se encuentren en los estados Pre-servicio, Factible, PrePlanificada, Planificada, AsignadoTarea
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 17-02-2017
     * 
     * @param  array $arrayParametros [
     *                                  "intIdPunto"        : Id del Punto
     *                                  "intStart"          : inicio el rownum,
     *                                  "intLimit"          : fin del rownum,
     *                                  "arrayEstado"       : Array de Estados de servicios
     *                                ]     
     * 
     * @return json $arrayResultado
     */
    public function getResultadoServiciosVtaExterna($arrayParametros)
    {     
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        $strSqlCantidad   = ' SELECT COUNT(*)  AS TOTAL '; 
                        
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $ntvQuery         = $this->_em->createNativeQuery(null, $objRsm);              
	    $strSqlDatos      = ' SELECT SERV.ID_SERVICIO, PROD.DESCRIPCION_PRODUCTO, SERV.ESTADO, SERV.PRECIO_VENTA';                
                 
        $strSqlFrom       = ' FROM DB_COMERCIAL.INFO_SERVICIO SERV,                                  
                                   DB_COMERCIAL.INFO_PUNTO PTO,
                                   DB_COMERCIAL.ADMI_PRODUCTO PROD
                              WHERE 
                              SERV.PUNTO_ID        = PTO.ID_PUNTO
                              AND SERV.ES_VENTA    = :strEsVenta
                              AND PTO.ID_PUNTO     = :intIdPunto    
                              AND SERV.ESTADO      in (:arrayEstados) 
                              AND SERV.PRODUCTO_ID = PROD.ID_PRODUCTO
                              AND NOT EXISTS (
                                  SELECT 1
                                  FROM 
                                   DB_COMUNICACION.INFO_DOCUMENTO_RELACION IDR,
                                   DB_COMUNICACION.INFO_DOCUMENTO IDC,
                                   DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL ATDG 
                                   WHERE
                                   IDR.ESTADO = :strEstadoActivo 
                                   AND IDC.ESTADO = :strEstadoActivo  
                                   AND ATDG.CODIGO_TIPO_DOCUMENTO = :strCodigoTipoDoc
                                   AND IDR.SERVICIO_ID = SERV.ID_SERVICIO
                                   AND IDR.DOCUMENTO_ID = IDC.ID_DOCUMENTO
                                   AND IDC.TIPO_DOCUMENTO_GENERAL_ID = ATDG.ID_TIPO_DOCUMENTO ) ';
       
        $strSqlOrderBy    = " ORDER BY SERV.ID_SERVICIO DESC ";
        
        $objRsm->addScalarResult('ID_SERVICIO','id','integer');               
        $objRsm->addScalarResult('DESCRIPCION_PRODUCTO', 'descripcionProducto','string');
        $objRsm->addScalarResult('ESTADO', 'estado','string');
        $objRsm->addScalarResult('PRECIO_VENTA','precioVenta','float');  
       
        $objRsmCount->addScalarResult('TOTAL','total','integer');
        
        $ntvQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);                        
        $ntvQuery->setParameter('arrayEstados', $arrayParametros['arrayEstados']);            
        $ntvQuery->setParameter('strEsVenta', 'E');            
        $ntvQuery->setParameter('strEstadoActivo', 'Activo');            
        $ntvQuery->setParameter('strCodigoTipoDoc', 'VTAEX');                
        
        $strSqlDatos    .= $strSqlFrom;        
        $strSqlDatos    .= $strSqlOrderBy;
        
        $ntvQuery->setSQL($strSqlDatos);
        $objDatos = $ntvQuery->getResult();
        
        $objNtvQueryCount->setParameter('intIdPunto', $arrayParametros['intIdPunto']);                        
        $objNtvQueryCount->setParameter('arrayEstados', $arrayParametros['arrayEstados']);            
        $objNtvQueryCount->setParameter('strEsVenta', 'E');         
        $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
        $objNtvQueryCount->setParameter('strCodigoTipoDoc', 'VTAEX');        
                
        $strSqlCantidad .= $strSqlFrom;           
        $objNtvQueryCount->setSQL($strSqlCantidad);        
        $intTotal        = $objNtvQueryCount->getSingleScalarResult();
        
        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;
       
        return $arrayResultado;               
    }
   
    /**
     * getServiciosVtaExterna
     * 
     * Obtiene Servicios de Tipo Venta Externa (ES_VENTA = 'E'), 
     * solo servicios que no posean asociado un documento digital de Tipo 'VTAEX' -> VENTA EXTERNA
     * y que se encuentren en los estados Pre-servicio, Factible, PrePlanificada, Planificada, AsignadoTarea
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 17-02-2017
     * 
     * @param  array $arrayParametros [
     *                                  "intIdPunto"        : Id del Punto
     *                                  "intStart"          : inicio el rownum,
     *                                  "intLimit"          : fin del rownum,
     *                                  "arrayEstado"       : Array de Estados de servicios
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getServiciosVtaExterna($arrayParametros)
    {       
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoServiciosVtaExterna($arrayParametros);                        
        $arrayRegistros   = $arrayResultado['objRegistros'];
        $intTotal         = $arrayResultado['intTotal'];                
        
        if(($arrayRegistros))
        {         
            foreach($arrayRegistros as $arrayServicios)
            {                
                $arrayEncontrados[] = array(  
                    'id'           => $arrayServicios['id'],                    
                    'descripcion'  => $arrayServicios['descripcionProducto'],                    
                    'estado'       => $arrayServicios['estado'],
                    'precio'       => $arrayServicios['precioVenta']
                    
                );                
            }
        }
        
        $arrayRespuesta = array('total' => $intTotal, 'listado' => $arrayEncontrados);        
        return $arrayRespuesta;
        
    }

    /**
     * Costo: 25
     *
     * getPuntosPorElementoYPuerto
     *
     * Esta función que retorna los puntos por elemento SWITCH y por puerto
     *
     * @param array $arrayParametros[ 'strNombreElemento'       => nombre del elemento,
     *                                'strDescripcionInterface' => nombre del puerto del elemento,
     *                                'arrayEstadosServicio'    => arreglo de estados de los servicios,
     *                                'arrayRazonSocial'        => arreglo de razones sociales a considerar ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-10-2018
     *
     * @return array $arrayPuntos
     */
    public function getPuntosPorElementoYPuerto($arrayParametros)
    {
        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT ISE.PUNTO_ID
                        FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO IE,DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IIE,
                        DB_INFRAESTRUCTURA.INFO_SERVICIO_TECNICO IST,DB_INFRAESTRUCTURA.INFO_SERVICIO ISE
                        WHERE  IE.ID_ELEMENTO = IIE.ELEMENTO_ID
                        AND IST.INTERFACE_ELEMENTO_ID = IIE.ID_INTERFACE_ELEMENTO
                        AND IST.SERVICIO_ID = ISE.ID_SERVICIO
                        AND IE.NOMBRE_ELEMENTO = :paramNombreElemento
                        AND IIE.DESCRIPCION_INTERFACE_ELEMENTO = :paramNombreInterface
                        AND IST.SERVICIO_ID IN (
                                                  SELECT ISE.ID_SERVICIO FROM DB_INFRAESTRUCTURA.INFO_SERVICIO ISE WHERE ISE.PUNTO_ID IN (
                                                  SELECT IPU.ID_PUNTO FROM DB_INFRAESTRUCTURA.INFO_PUNTO IPU WHERE IPU.PERSONA_EMPRESA_ROL_ID IN (
                                                  SELECT IPER.ID_PERSONA_ROL FROM DB_INFRAESTRUCTURA.INFO_PERSONA_EMPRESA_ROL IPER
                                                  WHERE IPER.PERSONA_ID IN (
                                                  SELECT IPE.ID_PERSONA FROM DB_INFRAESTRUCTURA.INFO_PERSONA IPE WHERE IPE.RAZON_SOCIAL IN
                                                  ( :paramRazonSocial ))))
                                                  AND ISE.ESTADO IN ( :paramEstadoServicio )) ";

        $objRsmb->addScalarResult('PUNTO_ID', 'idPunto', 'integer');

        $objQuery->setParameter("paramNombreElemento", $arrayParametros["strNombreElemento"]);
        $objQuery->setParameter("paramNombreInterface", $arrayParametros["strDescripcionInterface"]);
        $objQuery->setParameter("paramRazonSocial", $arrayParametros["arrayRazonSocial"]);
        $objQuery->setParameter("paramEstadoServicio", $arrayParametros["arrayEstadosServicio"]);

        $objQuery->setSQL($strSql);

        $arrayClientes = $objQuery->getResult();

        return $arrayClientes;
    }

    /**
     * Costo: 8
     *
     * getPuntosPorLogin
     *
     * Esta función que retorna los puntos relacionados a una razón social
     *
     * @param array $arrayParametros[ 'strLogin'          => nombre del login,
     *                                'arrayRazonSocial'  => arreglo de razones sociales a considerar ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-10-2018
     *
     * @return array $arrayPuntos
     */
    public function getPuntosPorLogin($arrayParametros)
    {
        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT IPU2.ID_PUNTO FROM INFO_PUNTO IPU2
                        WHERE IPU2.LOGIN = :paramNombreLogin
                        AND IPU2.ID_PUNTO IN (
                        SELECT IPU.ID_PUNTO FROM INFO_PUNTO IPU WHERE IPU.PERSONA_EMPRESA_ROL_ID IN (
                        SELECT IPER.ID_PERSONA_ROL FROM INFO_PERSONA_EMPRESA_ROL IPER WHERE IPER.PERSONA_ID IN (
                        SELECT IPE.ID_PERSONA FROM INFO_PERSONA IPE WHERE IPE.RAZON_SOCIAL IN ( :paramRazonSocial )))) ";

        $objRsmb->addScalarResult('ID_PUNTO', 'idPunto', 'integer');

        $objQuery->setParameter("paramNombreLogin", $arrayParametros["strLogin"]);
        $objQuery->setParameter("paramRazonSocial", $arrayParametros["arrayRazonSocial"]);

        $objQuery->setSQL($strSql);

        $arrayClientes = $objQuery->getResult();

        return $arrayClientes;
    }
    /** 
     * getPuntoByLogin
     *
     * Esta función que retorna los puntos por login de clientes, consume WS Megadatos
     *
     * @param $strLogin => nombre del login
     *
     * @author Roberth Cobeña Conforme <rcobena@telconet.ec>
     * @version 1.0 14-04-2021
     *
     * @return array $arrayPuntosWs
     */
    public function getPuntoByLogin($strLogin)
	{
		$strQuery = $this->_em->createQuery(
							"	SELECT p.id
								FROM schemaBundle:InfoPunto p
								WHERE p.login = :LoginCliente 
							")
						->setParameter('LoginCliente',$strLogin);			
		$arrayEntities =  $strQuery->getResult();
                $intTotal=count($strQuery->getResult());
		
		$arrayPuntosWs = array();
		$arrayPuntosWs['total'] = $intTotal;
		if($arrayEntities && count($arrayEntities)>0)
		{
			foreach($arrayEntities as $entity)
			{
					$arrayPuntoFormaContactoOne['PUNTOID'] = $entity["id"];
					$arrayPuntosWs[] = $arrayPuntoFormaContactoOne;
			}
		}
		return $arrayPuntosWs;
	}


    /**
     * Costo: 4
     *
     * getNotifacionCasosBack
     *
     * Esta función que retorna los correos y logines afectados de un caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-10-2018
     *
     * @param array $arrayParametros[ 'intCasoId' => id del caso ]
     *
     * @return array $arrayResultado
     */
    public function getNotifacionCasosBack($arrayParametros)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = " SELECT LISTAGG(TNB.CADENA_LOGIN , ', ') WITHIN GROUP (ORDER BY TNB.CADENA_CORREO) LISTADO_LOGINES,TNB.CADENA_CORREO
                    FROM DB_SOPORTE.TEMP_NOTIF_BACKBONE TNB
                    WHERE TNB.CASO_ID = :paramIdCaso
                    GROUP BY TNB.CADENA_CORREO ";

        $rsm->addScalarResult('LISTADO_LOGINES', 'listadoLogines', 'string');
        $rsm->addScalarResult('CADENA_CORREO', 'correo', 'string');

        $query->setParameter("paramIdCaso", $arrayParametros["intCasoId"]);

        $query->setSQL($sql);

        $arrayResultado = $query->getResult();

        return $arrayResultado;
    }

    /**
     * eliminaRegistrosTemporales
     *
     * Esta función se encarga de eliminar los registros temporales de la tabla TEMP_NOTIF_BACKBONE, que son usados para notificar
     * a afectados Backbone
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-10-2018
     *
     * @param array  $arrayParametros["intCasoId" => id del caso]
     *
     * @return string $strRespuesta
     */
    public function eliminaRegistrosTemporales($arrayParametros)
    {
        $strRespuesta = str_pad($strRespuesta, 50, " ");

        $strSql = "BEGIN :strRespuesta := DB_SOPORTE.SPKG_UTILIDADES.F_ELIMINAR_REGISTROS_TEMPORAL(:paramCasoId); END;";

        $stmt = $this->_em->getConnection()->prepare($strSql);
        $stmt->bindParam('paramCasoId', $arrayParametros["intCasoId"]);
        $stmt->bindParam('strRespuesta', $strRespuesta);
        $stmt->execute();
    }

    /**
     * getPuntoPorDetalleHipotesis
     *
     * Obtiene la información del punto en base a un id_detalle_hipotesis de un caso.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 22-05-2017
     *
     * costoQuery: 3
     * @param  array $arrayParametros [
     *                                  "intDetalleHipotesisId"        : Id del Punto
     *                                ]
     *
     * @return $arrayInfoPunto
     */
    public function getPuntoPorDetalleHipotesis($arrayParametros)
    {
        $arrayInfoPunto = null;
        try
        {
            if( isset($arrayParametros['intDetalleHipotesisId']) )
            {
                $objQuery  = $this->_em->createQuery();
                $strSelect = "SELECT PUNTO ";
                $strFrom   = "FROM schemaBundle:InfoPunto PUNTO ";
                $strWhere  = "WHERE PUNTO.id IN (SELECT AFECTADA.afectadoId ".
                                                   "FROM schemaBundle:InfoParteAfectada AFECTADA ".
                                                   "WHERE AFECTADA.detalleId = ".
                                                     "(SELECT MIN(DETALLE.id) ".
                                                     "FROM schemaBundle:InfoDetalleHipotesis HIPOTESIS, ".
                                                       "schemaBundle:InfoDetalle DETALLE ".
                                                      "WHERE HIPOTESIS.id = DETALLE.detalleHipotesisId ".
                                                      "AND DETALLE.detalleHipotesisId     = :intDetalleHipotesisId ".
                                                      ") ". 
                                                    "AND AFECTADA.tipoAfectado = :strTipoAfectado".
                                                    ")";

                $objQuery->setParameter('intDetalleHipotesisId',  $arrayParametros['intDetalleHipotesisId']);
                $objQuery->setParameter('strTipoAfectado',        $arrayParametros['strTipoAfectado']);

                $strSql = $strSelect.$strFrom.$strWhere;
                $objQuery->setDQL($strSql);

                $arrayInfoPunto = $objQuery->getResult();
            }
        }
        catch(\Exception $e)
        {
            error_log("Problemas al recuperar la información de la función InfoPuntoRepository:getPuntoPorDetalleHipotesis ".$e->getMessage());
        }
        return $arrayInfoPunto;
    }

    /**
     * getDatosLdapPorId
     * obtiene los datos del ldap segun el id servicio que viene en el array
     * 
     * @author John Vera
     * @version 1.0 22/01/2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2018 Se agrega el prefijo empresa para la consulta en el árbol ldap
     * 
     * @param $arrayParametros
     * @return $resultadoArray
     */
    
    public function getDatosLdapPorId($arrayParametros)
    {
        
        $start = $arrayParametros['start'];
        $limit = $arrayParametros['limit'];
        $tecnicoService = $arrayParametros["tecnicoService"];
     
        $registros = $this->getServiciosPorOltLogin($arrayParametros);
        
        $totalSolicitudes = count($registros);

        $encontrados = array_slice($registros, $start, $limit);

        $solicitudesArray = array();
        if($encontrados)
        {
            foreach($encontrados as $registro)
            {
                
                $resultadoJsonLdap = $tecnicoService->ejecutarComandoLdap("C", $registro['idServicio'], $arrayParametros["strPrefijoEmpresa"]);
                
                $ldap       = substr($resultadoJsonLdap->mensaje, 11);
                $ldap       = str_replace("}"," ",$ldap);
                $arrayCampos= explode('=',$ldap);
                
                if ($arrayCampos)
                {
                    $description    = explode(',', $arrayCampos[1]);
                    $cn             = str_replace(", sn"," ",$arrayCampos[2]);
                    $sn             = explode(',',$arrayCampos[3]);
                    $tnEmpresa      = explode(',',$arrayCampos[5]);
                    $tnClientId     = explode(',',$arrayCampos[6]);
                    $tnClientClass  = explode(',',$arrayCampos[7]);
                    $tnStatus       = explode(',',$arrayCampos[8]);
                    $tnPolicy       = explode(',',$arrayCampos[10]);
                    $macAddress     = explode(',',$arrayCampos[11]);
                    $packageID      = explode(',',$arrayCampos[12]);
                }
                $solicitudesArray[] = array('login'         =>$registro['login'],
                                            'idServicio'    => $registro['idServicio'],
                                            'nombreElemento'=> $registro['nombreElemento'],
                                            'estadoServicio'=> $registro['estado'],
                                            'description'   =>trim($description[0]),
                                            'cn'            =>trim($cn),
                                            'sn'            =>trim($sn[0]),
                                            'tnEmpresa'     =>trim($tnEmpresa[0]),
                                            'tnClientId'    =>trim($tnClientId[0]),
                                            'tnClientClass' =>trim($tnClientClass[0]),
                                            'tnStatus'      =>trim($tnStatus[0]),
                                            'tnPolicy'      =>trim($tnPolicy[0]),
                                            'macAddress'    =>trim($macAddress[0]),
                                            'packageID'     =>trim($packageID[0])
                                            );
            }
        }
        
        $resultadoArray['registros'] = $solicitudesArray;
        $resultadoArray['total'] = $totalSolicitudes;
        return $resultadoArray;
        
    }

    
    /**
     * getServiciosPorOltLogin
     * obtiene los id servicio por login o por equipo olt
     * 
     * @author John Vera
     * @version 1.0 22/01/2016
     * 
     * @version 1.1 05/02/2016 John Vera cambio en query
     * 
     * @author Lizbeth Cruz
     * @version 1.2 12/04/2018 Se agrega la consulta para obtener los servicios Internet Small Business 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 05-05-2020 Se modifica la consulta para obtener todos los servicios Small Business y TelcoHome 
     * 
     * @param $arrayParametros
     * @return $registros
     * 
     */   
    public function getServiciosPorOltLogin($arrayParametros)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        $login = $arrayParametros["login"];
        $idElemento = $arrayParametros["idElemento"];
        $estado = $arrayParametros["estado"];
        $objProdInternet = $arrayParametros['objProdInternet'];
        
        $sql = "SELECT S.ID_SERVICIO, P.LOGIN, E.NOMBRE_ELEMENTO, S.ESTADO
                FROM INFO_PUNTO P
                INNER JOIN DB_COMERCIAL.INFO_SERVICIO S
                ON P.ID_PUNTO = S.PUNTO_ID
                INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
                ON ST.SERVICIO_ID = S.ID_SERVICIO
                INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO E
                ON E.ID_ELEMENTO = ST.ELEMENTO_ID
                LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET DT
                ON DT.PLAN_ID = S.PLAN_ID
                LEFT JOIN DB_COMERCIAL.INFO_PLAN_CAB PC
                ON (DT.PLAN_ID = PC.ID_PLAN AND DT.ESTADO = PC.ESTADO)
                LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD_ISB
                ON S.PRODUCTO_ID = PROD_ISB.ID_PRODUCTO
                WHERE (
                    PROD_ISB.ID_PRODUCTO IN (SELECT ID_PRODUCTO
                                            FROM DB_COMERCIAL.ADMI_PRODUCTO
                                            WHERE NOMBRE_TECNICO = :nombreProductoIsb
                                            AND EMPRESA_COD      = :idEmpresaTn
                                            AND ESTADO           = :estadoIsb )
                    OR
                    PROD_ISB.ID_PRODUCTO IN (SELECT ID_PRODUCTO
                                            FROM DB_COMERCIAL.ADMI_PRODUCTO
                                            WHERE NOMBRE_TECNICO = :nombreProductoTelcoHome
                                            AND EMPRESA_COD      = :idEmpresaTn
                                            AND ESTADO           = :estadoIsb )
                    OR DT.PRODUCTO_ID = :idProducto )
                    AND S.ESTADO NOT  IN (:estados) ";
        
        $query->setParameter("idProducto", $objProdInternet->getId());
        $query->setParameter("estados", array('Cancel', 'Eliminado'));
        $query->setParameter("nombreProductoIsb", "INTERNET SMALL BUSINESS");
        $query->setParameter("nombreProductoTelcoHome", "TELCOHOME");
        $query->setParameter("idEmpresaTn", "10");
        $query->setParameter("estadoIsb", "Activo");


        if($estado)
        {
            $sql.= " AND S.ESTADO in (:estado) ";
            $query->setParameter("estado", $estado);
        }
        if($idElemento)
        {
            $sql.= " AND ST.ELEMENTO_ID = :idElemento ";
            $query->setParameter("idElemento", $idElemento);
        }
        if($login)
        {
            $sql.= " AND P.LOGIN like :login ";
            $query->setParameter("login", '%'.$login.'%');
        }
       
        $rsm->addScalarResult(strtoupper('ID_SERVICIO'), 'idServicio', 'integer');
        $rsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');
        $rsm->addScalarResult(strtoupper('NOMBRE_ELEMENTO'), 'nombreElemento', 'string');
        $rsm->addScalarResult(strtoupper('ESTADO'), 'estado', 'string');

        $query->setSQL($sql);

        $registros = $query->getResult();
        
        return $registros;
    }

    /**
     * Obtiene el plan (tipo de negocio asociado al punto)
     * 
     * @param type $id_punto
     * @return string
     */
    public function getTipoNegocioByPuntoId($id_punto){
        $em = $this->_em;
        $infoPunto = $em->getRepository('schemaBundle:InfoPunto')->find($id_punto);
        if($infoPunto){
            $plan = $infoPunto->getTipoNegocioId()->getNombreTipoNegocio();
            return $plan;
         }
         return 'Plan No encontrado';
    }
    /**
     * Obtiene el grupo de Negocio Asociado a un Punto
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.0 15-05-2016     
     * @param type $intIdPunto
     * @return string
     */
     public function getGrupoNegocioByPuntoId($intIdPunto)
    {
        $query = $this->_em->createQuery("select t from		
                 schemaBundle:InfoPunto p,
                 schemaBundle:AdmiTipoNegocio t
                 where p.id = :intIdPunto
                  and p.tipoNegocioId= t.id");
                
        $query->setParameter('intIdPunto', $intIdPunto);        
        $datos = $query->getOneOrNullResult();		
        return $datos;
   }
   /**
     * Obtiene la Zona Asociada a un Punto
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.0 15-05-2016     
     * @param type $intIdPunto
     * @return string
     */
     public function getZonaByPuntoId($intIdPunto)
    {
        $query = $this->_em->createQuery("select cant from		
                 schemaBundle:InfoPunto pto,
                 schemaBundle:AdmiSector sec,
                 schemaBundle:AdmiParroquia parr,
                 schemaBundle:AdmiCanton cant
                 where pto.id = :intIdPunto
                  and pto.sectorId= sec.id
                  and sec.parroquiaId = parr.id
                  and parr.cantonId = cant.id
                  ");
                
        $query->setParameter('intIdPunto', $intIdPunto);        
        $datos = $query->getOneOrNullResult();		
        return $datos;
   }
    /**
     * Documentación para el método 'search'.
     *
     * Función que retorna la información consultada en la búsqueda avanzada
     * 
     * @param object  $datos          parámetros ingresados por el usuario
     * @param integer $codEmpresa     código de la empresa
     * @param string  $prefijoEmpresa prefijo de la empresa
     * @param integer $start          registro inicial
     * @param integer $limit          cantidad de registros que se deben retornar
     * 
     * @return response
     *
     * @author Modificado: Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 11-01-2018 - Se modifica consulta por login y estado.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-09-2015 - Se ordena la información por Apellidos, Nombres y Razon Social.
     *
     * @version 1.0 Version Inicial
     */
    public function search($datos,$codEmpresa,$prefijoEmpresa,$start,$limit)
    {
        $from = "";
        $where = "";
        $query = $this->_em->createQuery();
        /* DATOS CLIENTE */
        if($datos->identificacion_cliente_avanzada){
            $where.= " AND ip.identificacionCliente = '".trim($datos->identificacion_cliente_avanzada)."'";
        }

        if($datos->nombres_cliente_avanzada){
            $where.= " AND lower(ip.nombres) = lower('".trim($datos->nombres_cliente_avanzada)."')";
        }

        if($datos->apellidos_cliente_avanzada){
            $where.= " AND lower(ip.apellidos) = lower('".trim($datos->apellidos_cliente_avanzada)."')";
        }

        if($datos->razon_social_cliente_avanzada){
            $where.= " AND lower(ip.razonSocial) = lower('".trim($datos->razon_social_cliente_avanzada)."')";
        }

        if($datos->direccion_cliente_avanzada){
            $where.= " AND lower(ip.direccion) like lower('%".trim($datos->direccion_cliente_avanzada)."%')";
        }
        
        if(isset($datos->id_oficina_cliente_avanzada)){
            $where.= " AND iper.oficinaId = ".$datos->id_oficina_cliente_avanzada;
        }

        if($datos->representante_legal_cliente_avanzada){
            $where.= " AND lower(ip.representanteLegal) like lower('%".trim($datos->representante_legal_cliente_avanzada)."%')";
        }
        
        if($datos->valor_forma_contacto_cliente_avanzada){
            $from.= ",schemaBundle:InfoPersonaFormaContacto ipfc
                     ,schemaBundle:AdmiFormaContacto afc";
            $where.= " AND ip.id = ipfc.personaId
                       AND ipfc.formaContactoId = afc.id
                       AND ipfc.estado = 'Activo'
                       AND afc.id = ".$datos->forma_contacto_cliente_avanzada."
                       AND lower(ipfc.valor) like lower('%".trim($datos->valor_forma_contacto_cliente_avanzada)."%')";
        }
        /* FIN DATOS CLIENTE */
        
        /* DATOS PUNTO */
        if($datos->login_punto_avanzada){
            $where.= " AND lower(p.login) like lower('".trim($datos->login_punto_avanzada)."%')";
        }
        if($datos->descripcion_punto_avanzada){
            $where.= " AND lower(p.descripcionPunto) like lower('%".trim($datos->descripcion_punto_avanzada)."%')";
        }
        if($datos->direccion_punto_avanzada){
            $where.= " AND lower(p.direccion) like lower('%".trim($datos->direccion_punto_avanzada)."%')";
        }
         if($datos->nombre_punto_avanzada){
            //echo( $datos->nombre_punto_avanzada); die();
            $where.= " AND lower(p.nombrePunto) like lower('%".trim($datos->nombre_punto_avanzada)."%')";
        }
        if($datos->ciudad_punto_avanzada){
            $from.= ",schemaBundle:AdmiSector ase";
            $from.= ",schemaBundle:AdmiParroquia ap";
            $from.= ",schemaBundle:AdmiCanton ac";

            $where.= " AND p.sectorId = ase.id";
            $where.= " AND ase.parroquiaId = ap.id";
            $where.= " AND ap.cantonId = ac.id";
            $where.= " AND lower(ac.nombreCanton) like lower('%".trim($datos->ciudad_punto_avanzada)."%')";
        }
        if(isset($datos->tipo_negocio_punto_avanzada)){
            $from.= ",schemaBundle:AdmiTipoNegocio atn";

            $where.= " AND p.tipoNegocioId = atn.id";
            $where.= " AND atn.id = ".$datos->tipo_negocio_punto_avanzada;
        }
        if(isset($datos->tipo_ubicacion_punto_avanzada)){
            $from.= ",schemaBundle:AdmiTipoUbicacion atu";

            $where.= " AND p.tipoUbicacionId = atu.id";
            $where.= " AND atu.id = ".$datos->tipo_ubicacion_punto_avanzada;
        }
        if(isset($datos->vendedor_punto_avanzada)){
            $where.= " AND lower(p.usrVendedor) = '".trim($datos->vendedor_punto_avanzada)."'";
        }
        if(isset($datos->estado_punto_avanzada)){
            $where.= " AND lower(p.estado) like lower('".$datos->estado_punto_avanzada."%')";
        }
        /* FIN DATOS PUNTO */
        
        /* DATOS COMERCIALES */
        
        // DOCUMENTOS
        $ingresoContrato = false;
        
        if($datos->numero_documento_comercial_avanzada){
	    if(isset($datos->tipo_documento_comercial_avanzada)){
		if(strtolower($datos->tipo_documento_comercial_avanzada)=="contrato"){
		  $ingresoContrato = true;
		  $from.= ",schemaBundle:InfoContrato ic";

		  $where.= " AND iper.id = ic.personaEmpresaRolId";
		  $where.= " AND ic.numeroContrato like '%".trim($datos->numero_documento_comercial_avanzada)."%'";
		}
		if(strtolower($datos->tipo_documento_comercial_avanzada)=="orden trabajo"){
		  $from.= ",schemaBundle:InfoOrdenTrabajo iot";

		  $where.= " AND p.id = iot.puntoId";
		  $where.= " AND iot.numeroOrdenTrabajo like '%".trim($datos->numero_documento_comercial_avanzada)."%'";
		}
	    }
        }
        if(isset($datos->forma_pago_comercial_avanzada)){
	    if($ingresoContrato){
		$where.= " AND ic.formaPagoId = ".$datos->forma_pago_comercial_avanzada;
	    }
	    else{
	      $ingresoContrato = true;
	      $from.= ",schemaBundle:InfoContrato ic";

	      $where.= " AND iper.id = ic.personaEmpresaRolId";
	      $where.= " AND ic.formaPagoId = ".$datos->forma_pago_comercial_avanzada;
	    }
        }
        if(isset($datos->estado_tipo_documento_comercial_avanzada)){
	    if($ingresoContrato){
		$where.= " AND lower(ic.estado) = lower('".$datos->estado_tipo_documento_comercial_avanzada."')";
	    }
	    else{
	      if(isset($datos->tipo_documento_comercial_avanzada)){
		  if(strtolower($datos->tipo_documento_comercial_avanzada)=="contrato"){
		    $ingresoContrato = true;
		    $from.= ",schemaBundle:InfoContrato ic";

		    $where.= " AND iper.id = ic.personaEmpresaRolId";
		    $where.= " AND lower(ic.estado) = lower('".$datos->estado_tipo_documento_comercial_avanzada."')";
		  }
		  if(strtolower($datos->tipo_documento_comercial_avanzada)=="orden trabajo"){
		    $from.= ",schemaBundle:InfoOrdenTrabajo iot";

		    $where.= " AND p.id = iot.puntoId";
		    $where.= " AND lower(iot.estado) = lower('".$datos->estado_tipo_documento_comercial_avanzada."')";
		  }
	      }
	    }
        }
        if($datos->fecha_aut_documento_comercial_avanzada){
	    $fechaAutDocumento = explode('T',$datos->fecha_aut_documento_comercial_avanzada);
	    $dateF = explode("-",$fechaAutDocumento[0]);
	    $fechaAutDocumento = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));        
	    
	    if($ingresoContrato){
		$where.= " AND ic.feAprobacion = '".$fechaAutDocumento."'";
	    }
	    else{
	      $ingresoContrato = true;
	      $from.= ",schemaBundle:InfoContrato ic";

	      $where.= " AND iper.id = ic.personaEmpresaRolId";
	      $where.= " AND ic.feAprobacion = '".$fechaAutDocumento."'";
	    }
        }
        if($datos->fecha_creacion_documento_comercial_avanzada){
	    $fechaCreacionDocumento = explode('T',$datos->fecha_creacion_documento_comercial_avanzada);
	    $dateF = explode("-",$fechaCreacionDocumento[0]);
	    $fechaCreacionDocumento = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));        
	    
	    if($ingresoContrato){
		$where.= " AND ic.feCreacion = '".$fechaCreacionDocumento."'";
	    }
	    else{
	      $ingresoContrato = true;
	      $from.= ",schemaBundle:InfoContrato ic";

	      $where.= " AND iper.id = ic.personaEmpresaRolId";
	      $where.= " AND ic.feCreacion = '".$fechaCreacionDocumento."'";
	    }
        }
        if($datos->usuario_documento_comercial_avanzada){
	    if($ingresoContrato){
		$where.= " AND lower(ic.usrCreacion) like lower('%".trim($datos->usuario_documento_comercial_avanzada)."%')";
	    }
	    else{
	      $ingresoContrato = true;
	      $from.= ",schemaBundle:InfoContrato ic";

	      $where.= " AND iper.id = ic.personaEmpresaRolId";
	      $where.= " AND lower(ic.usrCreacion) like lower('%".trim($datos->usuario_documento_comercial_avanzada)."%')";
	    }
        }
        // FIN DOCUMENTOS
        
        // SERVICIOS
        $ingresoServicio = false;
        $ingresoServicioHistorial = false;
        
        if(isset($datos->producto_plan_avanzada)){
	    $ingresoServicio = true;
            $from.= ",schemaBundle:InfoServicio s";

            $where.= " AND p.id = s.puntoId";
            
            if(strtolower($datos->servicios_por_comercial_avanzada)=="catalogo")
	      $where.= " AND s.productoId = ".$datos->producto_plan_avanzada;
	    if(strtolower($datos->servicios_por_comercial_avanzada)=="portafolio")
	      $where.= " AND s.planId = ".$datos->producto_plan_avanzada;
	      
        }
        if(isset($datos->estado_servicio_comercial_avanzada)){
	    if($ingresoServicio)
	      $where.= " AND lower(s.estado) like lower('".$datos->estado_servicio_comercial_avanzada."%')";
	    else{
	       $ingresoServicio = true;
	       $from.= ",schemaBundle:InfoServicio s";

	       $where.= " AND p.id = s.puntoId";
	       $where.= " AND lower(s.estado) like lower('".$datos->estado_servicio_comercial_avanzada."%')";
	    }  
        }
        if($datos->fecha_cancelacion_desde_avanzada){
	    $dateF = explode("-",$datos->fecha_cancelacion_desde_avanzada);
	    $fechaCancelacionDesdeServicio = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
            
	    if($ingresoServicio){
		$ingresoServicioHistorial = true;
		$from.= ",schemaBundle:InfoServicioHistorial ish";

	        $where.= " AND s.id = ish.servicioId";
	        $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
	        $where.= " AND lower(ish.estado) in ('cancel','cancelado','cancel-sineje')";
		$where.= " AND ish.feCreacion >= '".$fechaCancelacionDesdeServicio."'";
	    }
	    else{
		$ingresoServicio = true;
		$ingresoServicioHistorial = true;
		$from.= ",schemaBundle:InfoServicio s";
		$from.= ",schemaBundle:InfoServicioHistorial ish";

	        $where.= " AND p.id = s.puntoId";
	        $where.= " AND s.id = ish.servicioId";
	        $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
	        $where.= " AND lower(ish.estado) in ('cancel','cancelado','cancel-sineje')";
		$where.= " AND ish.feCreacion >= '".$fechaCancelacionDesdeServicio."'";
	    }
        }
        if($datos->fecha_cancelacion_hasta_avanzada){
	    $dateF = explode("-",$datos->fecha_cancelacion_hasta_avanzada);
	    $fechaCancelacionHastaServicio = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
	    
	    if($ingresoServicio){
		if($ingresoServicioHistorial){
		  $where.= " AND ish.feCreacion <= '".$fechaCancelacionHastaServicio."'";
		}else{
		  $from.= ",schemaBundle:InfoServicioHistorial ish";

		  $where.= " AND s.id = ish.servicioId";
		  $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
		  $where.= " AND lower(ish.estado) in ('cancel','cancelado','cancel-sineje')";
		  $where.= " AND ish.feCreacion <= '".$fechaCancelacionHastaServicio."'";
		}
	    }
	    else{
		$ingresoServicio = true;
		$ingresoServicioHistorial = true;
		$from.= ",schemaBundle:InfoServicio s";
		$from.= ",schemaBundle:InfoServicioHistorial ish";

	        $where.= " AND p.id = s.puntoId";
	        $where.= " AND s.id = ish.servicioId";
	        $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
	        $where.= " AND lower(ish.estado) in ('cancel','cancelado','cancel-sineje')";
		$where.= " AND ish.feCreacion <= '".$fechaCancelacionHastaServicio."'";
	    }
        }
        if($datos->fecha_corte_desde_avanzada){
	    $dateF = explode("-",$datos->fecha_corte_desde_avanzada);
	    $fechaCorteDesdeServicio = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
	    
	    if($ingresoServicio){
	      if($ingresoServicioHistorial){
// 		   $where.= " AND ish.id = (select max(ish.id) 
// 	                                         from schemaBundle:InfoServicioHistorial ishMax
// 	                                         where ishMax.servicioId = ish.servicioId)";
		   $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		   $where.= " AND ish.feCreacion >= '".$fechaCorteDesdeServicio."'";
	      }else{
		   $ingresoServicioHistorial = true;
		   $from.= ",schemaBundle:InfoServicioHistorial ish";

		   $where.= " AND s.id = ish.servicioId";
		   $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
		   $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		   $where.= " AND ish.feCreacion >= '".$fechaCorteDesdeServicio."'";
	      }
	    }
	    else{
		$ingresoServicio = true;
		$ingresoServicioHistorial = true;
		$from.= ",schemaBundle:InfoServicio s";
		$from.= ",schemaBundle:InfoServicioHistorial ish";

	        $where.= " AND p.id = s.puntoId";
	        $where.= " AND s.id = ish.servicioId";
	        $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
	        $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		$where.= " AND ish.feCreacion >= '".$fechaCorteDesdeServicio."'";
	    }
        }
        if($datos->fecha_corte_hasta_avanzada){
	    $dateF = explode("-",$datos->fecha_corte_hasta_avanzada);
	    $fechaCorteHastaServicio = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
	    
	    if($ingresoServicio){
	      if($ingresoServicioHistorial){
// 	           $where.= " AND ish.id = (select max(ish.id) 
// 	                                         from schemaBundle:InfoServicioHistorial ishMax
// 	                                         where ishMax.servicioId = ish.servicioId)";
		   $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		   $where.= " AND ish.feCreacion <= '".$fechaCorteHastaServicio."'";
	      }else{
		   $ingresoServicioHistorial = true;
		   $from.= ",schemaBundle:InfoServicioHistorial ish";

		   $where.= " AND s.id = ish.servicioId";
		   $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
		   $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		   $where.= " AND ish.feCreacion <= '".$fechaCorteHastaServicio."'";
	      }
	    }
	    else{
		$ingresoServicio = true;
		$ingresoServicioHistorial = true;
		$from.= ",schemaBundle:InfoServicio s";
		$from.= ",schemaBundle:InfoServicioHistorial ish";

	        $where.= " AND p.id = s.puntoId";
	        $where.= " AND s.id = ish.servicioId";
	        $where.= " AND ish.id = (select max(ish.id) 
	                                         from schemaBundle:InfoServicioHistorial ishMax
	                                         where ishMax.servicioId = ish.servicioId)";
	        $where.= " AND lower(ish.estado) in ('in-corte','in-corte-sineje')";
		$where.= " AND ish.feCreacion <= '".$fechaCorteHastaServicio."'";
	    }
        }
        // FIN SERVICIOS
        
        /* FIN DATOS COMERCIALES */
        
        /* DATOS TECNICOS */
        
        // BACKBONE
        $ingresoServicioTecnico = false;
        $ingresoInfoElementoBackbone = false;
        $ingresoInfoElementoCliente = false;
        $ingresoServicioProdCaract = false;
        
        if(isset($datos->interface_elemento_tecnico_avanzada)){
            $ingresoServicioTecnico = true;

            if($ingresoServicio){
            $from.= ",schemaBundle:InfoServicioTecnico st";
            }else{
            $ingresoServicio = true;
            $from.= ",schemaBundle:InfoServicio s";
            $from.= ",schemaBundle:InfoServicioTecnico st";

            $where.= " AND p.id = s.puntoId";
            }

            $where.= " AND s.id = st.servicioId";

            $servicios = '';
            $servicios = $this->getServiciosByElementoAndInterface($datos->elemento_tecnico_avanzada, $datos->interface_elemento_tecnico_avanzada);
            if ($servicios)
            {
                $where .= " AND s.id in (:servicios)";
                $serviciosArray = explode(',',$servicios);
                $query->setParameter("servicios", $serviciosArray);
            }
            else
            {
                return $resultado;        
            }
        }
        
        if(isset($datos->elemento_tecnico_avanzada) && !isset($datos->interface_elemento_tecnico_avanzada))
        {
            if(!$ingresoServicioTecnico)
            {
                $ingresoServicioTecnico = true;
                if($ingresoServicio)
                {
                    $from.= ",schemaBundle:InfoServicioTecnico st";
                }
                else
                {
                    $ingresoServicio = true;
                    $from.= ",schemaBundle:InfoServicio s";
                    $from.= ",schemaBundle:InfoServicioTecnico st";

                    $where.= " AND p.id = s.puntoId";
                }

                $where.= " AND s.id = st.servicioId";
            }

            $servicios = '';
            $servicios = $this->getServiciosByElementoAndInterface($datos->elemento_tecnico_avanzada, '');
            if ($servicios)
            {
                $where .= " AND s.id in (:servicios)";
                $serviciosArray = explode(',',$servicios);
                $query->setParameter("servicios", $serviciosArray);
            }
            else
            {
                return $resultado;        
            }
        }

        if(isset($datos->modelo_elemento_tecnico_avanzada) && !isset($datos->interface_elemento_tecnico_avanzada) && !isset($datos->elemento_tecnico_avanzada)){
            $ingresoInfoElementoBackbone = true;
            if(!$ingresoServicioTecnico){
		$ingresoServicioTecnico = true;
		if($ingresoServicio){
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		}else{
		    $ingresoServicio = true;
		    $from.= ",schemaBundle:InfoServicio s";
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		    
		    $where.= " AND p.id = s.puntoId";
		}
		
		$where.= " AND s.id = st.servicioId";
	    }
	    
	    $from.= ",schemaBundle:InfoElemento ie";
	    
	    $where.= " AND st.elementoId = ie.id";
	    $where.= " AND ie.modeloElementoId = ".$datos->modelo_elemento_tecnico_avanzada;
        }
        
        if(isset($datos->tipo_elemento_tecnico_avanzada) && !isset($datos->interface_elemento_tecnico_avanzada) && !isset($datos->elemento_tecnico_avanzada) && !isset($datos->modelo_elemento_tecnico_avanzada)){
	    if(!$ingresoInfoElementoBackbone){  
	      $ingresoInfoElementoBackbone = true;
	      if(!$ingresoServicioTecnico){
		  $ingresoServicioTecnico = true;
		  if($ingresoServicio){
		      $from.= ",schemaBundle:InfoServicioTecnico st";
		  }else{
		      $ingresoServicio = true;
		      $from.= ",schemaBundle:InfoServicio s";
		      $from.= ",schemaBundle:InfoServicioTecnico st";
		      
		      $where.= " AND p.id = s.puntoId";
		  }
		  
		  $where.= " AND s.id = st.servicioId";
	      }
	      
	      $from.= ",schemaBundle:InfoElemento ie";
	      
	      $where.= " AND st.elementoId = ie.id";
	      
	    }
	    
	    $from.= ",schemaBundle:AdmiModeloElemento ame";
	    $from.= ",schemaBundle:AdmiTipoElemento ate";
	    
	    $where.= " AND ie.modeloElementoId = ame.id";
	    $where.= " AND ame.tipoElementoId = ate.id";
	    $where.= " AND ate.id = ".$datos->tipo_elemento_tecnico_avanzada;	    
        }
        // FIN BACKBONE
        
        // CLIENTE
        if(isset($datos->tipo_medio_tecnico_avanzada)){
            
            if(!$ingresoServicioTecnico){
		$ingresoServicioTecnico = true;
		if($ingresoServicio){
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		}else{
		    $ingresoServicio = true;
		    $from.= ",schemaBundle:InfoServicio s";
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		    
		    $where.= " AND p.id = s.puntoId";
		}
		
		$where.= " AND s.id = st.servicioId";
	    }
	    
	    $where.= " AND st.ultimaMillaId = ".$datos->tipo_medio_tecnico_avanzada;
        }
        
        if($datos->host_cpe_tecnico_avanzada){
	    $ingresoInfoElementoCliente = true;
            if(!$ingresoServicioTecnico){
		$ingresoServicioTecnico = true;
		if($ingresoServicio){
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		}else{
		    $ingresoServicio = true;
		    $from.= ",schemaBundle:InfoServicio s";
		    $from.= ",schemaBundle:InfoServicioTecnico st";
		    
		    $where.= " AND p.id = s.puntoId";
		}
		
		$where.= " AND s.id = st.servicioId";
	    }
	    
	    $from.= ",schemaBundle:InfoElemento iec";
	    
	    $where.= " AND st.elementoClienteId = iec.id";
	    $where.= " AND lower(iec.nombreElemento) like lower('%".trim($datos->host_cpe_tecnico_avanzada)."%')";
        }
        
        if($datos->serie_cpe_tecnico_avanzada){
	    if(!$ingresoInfoElementoCliente){	
		$ingresoInfoElementoCliente = true;
		if(!$ingresoServicioTecnico){
		    $ingresoServicioTecnico = true;
		    if($ingresoServicio){
			$from.= ",schemaBundle:InfoServicioTecnico st";
		    }else{
			$ingresoServicio = true;
			$from.= ",schemaBundle:InfoServicio s";
			$from.= ",schemaBundle:InfoServicioTecnico st";
			
			$where.= " AND p.id = s.puntoId";
		    }
		    
		    $where.= " AND s.id = st.servicioId";
		}
		
		$from.= ",schemaBundle:InfoElemento iec";
		
		$where.= " AND st.elementoClienteId = iec.id";
	    }
	    
	    $where.= " AND lower(iec.serieFisica) like lower('%".trim($datos->serie_cpe_tecnico_avanzada)."%')";
        }
        
        if($datos->mac_cpe_tecnico_avanzada)
        {
            $caracteristicaMac = "";
            if($prefijoEmpresa == "TTCO")
                $caracteristicaMac = "MAC";
            if($prefijoEmpresa == "MD")
                $caracteristicaMac = "MAC ONT";

            $productoInternetDedicado = $this->_em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array("empresaCod" => $codEmpresa, "descripcionProducto" => "INTERNET DEDICADO", "estado" => "Activo"));
            $admiCaractMac = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array("descripcionCaracteristica" => $caracteristicaMac, "estado" => "Activo"));
            $prodCaractMac = $this->_em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array("productoId" => $productoInternetDedicado->getId(), "caracteristicaId" => $admiCaractMac->getId(), "estado" => "Activo"));

            if($prodCaractMac)
            {
                $ingresoServicioProdCaract = true;
                if(!$ingresoServicio)
                {
                    $ingresoServicio = true;
                    $from.= ",schemaBundle:InfoServicio s";

                    $where.= " AND p.id = s.puntoId";
                }

                $from.= ",schemaBundle:InfoServicioProdCaract spc";

                $where.= " AND s.id = spc.servicioId";
                $where.= " AND spc.estado not in (:estadoSpc)";
                $where.= " AND spc.productoCaracterisiticaId = :prodCaractMac";
                $where.= " AND lower(spc.valor) like lower(:macCpe)";
                
                $query->setParameter('estadoSpc', array('Anulado', 'Eliminado'));
                $query->setParameter('prodCaractMac', $prodCaractMac->getId());
                $query->setParameter('macCpe', '%'. trim($datos->mac_cpe_tecnico_avanzada) .'%');
            }
        }

        if($datos->ip_tecnico_avanzada){

	    if(!$ingresoServicio){
		$ingresoServicio = true;
		$from.= ",schemaBundle:InfoServicio s";
		
		$where.= " AND p.id = s.puntoId";
	    }
	    
	    $from.= ",schemaBundle:InfoIp ii";
	      
	    $where.= " AND s.id = ii.servicioId";
	    $where.= " AND lower(ii.estado) = 'activo'";
	    $where.= " AND lower(ii.ip) like lower('%".trim($datos->ip_tecnico_avanzada)."%')";
	    
        }
        // FIN CLIENTE
        
        /* FIN DATOS TECNICOS */

        $sql = "
                            SELECT 
                                ip.id as id_cliente,
                                ip.razonSocial as razon_social_cliente,
                                ip.nombres as nombres_cliente,
                                ip.apellidos as apellidos_cliente,
                                p.id as id_punto,p.nombrePunto as nombrePunto,
                                p.login as login,
                                p.descripcionPunto as descripcion_punto,
                                p.direccion as direccion_punto,
                                p.estado as estado_punto,
                                iog.id as id_oficina,
                                p.usrVendedor as usrVendedor
                            FROM 
                                schemaBundle:InfoPersona ip, 
                                schemaBundle:InfoPersonaEmpresaRol iper,
                                schemaBundle:InfoEmpresaRol er,
                                schemaBundle:AdmiRol ar,
                                schemaBundle:AdmiTipoRol atr,
                                schemaBundle:InfoOficinaGrupo iog,
                                schemaBundle:InfoPunto p
                                $from
                            WHERE 
                                ip.id = iper.personaId 
                            AND iper.empresaRolId = er.id 
                            AND er.rolId = ar.id 
                            AND p.personaEmpresaRolId = iper.id
                            AND ar.tipoRolId = atr.id
                            AND iper.oficinaId = iog.id
                            AND lower(atr.descripcionTipoRol) in ('cliente','pre-cliente')
                            AND er.empresaCod ='$codEmpresa'
                                $where   
                            GROUP BY p.id ,
                                    ip.id ,
									ip.razonSocial,
									ip.nombres,
									ip.apellidos,
                                    p.login,
                                    p.nombrePunto,
									p.descripcionPunto,
									p.direccion,
									p.estado,
                                    iog.id,
                                    p.usrVendedor
                            ORDER BY ip.apellidos, ip.nombres, ip.razonSocial ";
        
        $query->setDQL($sql);        
   
        $resultado['total'] = count($query->getResult());
        $resultado['datos'] = $query->setFirstResult($start)->setMaxResults($limit)->getResult();

        return $resultado;
    }
    
    /**
     * Documentación para el método 'setSessionByIdPunto'.
     *
     * Método que establece la sesión en base al Punto de Cliente.
     *
     * @return Response Resultado de la Operación.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 04-04-2016
     * Verificación del Cliente VIP para agregarlo a la sesión.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 08-06-2016
     * Se inicializan los valores de sesión del cliente
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 18-04-2018 Se agrega seteo de variable de sesión 'cicloFacturacionCliente'
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 11-05-2018 Se agrega seteo de variable de sesión para visualización de contactos a nivel de cliente y punto.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.5 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.6 19-03-2021 - Se agrega seteo de variables 'fechaNacimiento', 'edad' a la sesión de cliente. 
     * 
     * @author Joel Ontuña <jontuna@telconet.ec>
     * @version 1.7 24-11-2022 - Se agrega la consulta del modelo predictivo con sus respectivas validaciones.
     * 
     * @param int       $intIdPunto
     * @param object    $objSession
     * @param array     $arrayParametrosAdicionales
     */
    public function setSessionByIdPunto($intIdPunto,$objSession, $arrayParametrosAdicionales = array())
    {
        $intCodEmpresa     = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');

        // Se inicializan los valores de sesión
        $objSession->set('ptoCliente', '');
        $objSession->set('cliente', '');
        $objSession->set('clienteContactos', '');
        $objSession->set('puntoContactos', '');
        $objSession->set('contactosCliente', '');
        $objSession->set('contactosPunto', '');
        $objSession->set('esVip', '');
        //defino la sessión para la marca del Vip Técnico del cliente
        $objSession->set('esVipTecnico', '');
        //defino la sessión para los datos de los ingenieros Vip del cliente
        $objSession->set('ingenierosVip', '');
        $objSession->set('cicloFacturacionCliente', '');
        //defino la sesión para los datos del modelo predictivo
        $objSession->set('modeloPredictivo', '');
        
        $objInfoPunto           = $this->_em->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
        $intCobertura           = $objInfoPunto->getPuntoCoberturaId();
        $intTipoNegocio         = $objInfoPunto->getTipoNegocioId();
        $intTipoUbicacion       = $objInfoPunto->getTipoUbicacionId();
        $objPersonaEmpresaRol   = $objInfoPunto->getPersonaEmpresaRolId();
        $objInfoPersona         = $objPersonaEmpresaRol->getPersonaId();
        $objInfoOficinaGrupo    = $objPersonaEmpresaRol->getOficinaId();
        
        $intIdPersonaRol = $objPersonaEmpresaRol->getId();
        
        if($strPrefijoEmpresa === 'TN' && is_object($objInfoPunto)) 
        {
            $arrayFormasContactoPto               = array();
            $arrayFormasContactoClt               = array();
            $arrayParametros                      = array();
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa; 
            $arrayParametros['strEstado']         = 'Activo'; 
            $arrayParametros['intIdPunto']        = $objInfoPunto->getId();
            $arrayParametros['intIdPersonaRol']   = $objInfoPunto->getPersonaEmpresaRolId()->getId();
            $arrayParametros['intMaxContactos']   = 1;

            $arrayParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne("MAX_NUMERO_CONTACTOS", "COMERCIAL", "", "", "", "", "", "","",$intCodEmpresa);

            if ($arrayParametroDet["valor2"])
            {
                $arrayParametros['intMaxContactos'] = $arrayParametroDet["valor2"];
            }

            $arrayContactosPunto   = $this->_em->getRepository('schemaBundle:InfoPersonaContacto')->getContactosPuntoRol($arrayParametros);

            $arrayContactosCliente = $this->_em->getRepository('schemaBundle:InfoPersonaContacto')->getContactosPersonaRol($arrayParametros);
            
            if(count($arrayContactosCliente)>0)
            {
                $intIdPersonaContacto = 0;
                
                foreach($arrayContactosCliente as $contactoCliente)
                {
                    if($intIdPersonaContacto !== $contactoCliente["idPersona"])
                    {
                        $arrayFormasContactoCliente = $this->_em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getFormasContactoPersona($contactoCliente["idPersona"]);
                        $arrayFormasContactoClt     = array_merge($arrayFormasContactoClt,$arrayFormasContactoCliente);

                        $intIdPersonaContacto       = $contactoCliente["idPersona"];
                    }
                }            
                $objSession->set('contactosCliente', $arrayContactosCliente);
                $objSession->set('formasContactoCliente', $arrayFormasContactoClt);
                
            }
         
            if(count($arrayContactosPunto)>0)
            {
                $intIdPersonaContactoPto = 0;
                
                foreach($arrayContactosPunto as $contactoPunto)
                {
                    if($intIdPersonaContactoPto !== $contactoPunto["idPersona"])
                    {                    
                        $arrayFormasContactoPunto = $this->_em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                            ->getFormasContactoPersona($contactoPunto["idPersona"]);

                        $arrayFormasContactoPto   = array_merge($arrayFormasContactoPto,$arrayFormasContactoPunto);

                        $intIdPersonaContactoPto  = $contactoPunto["idPersona"];
                    }
                }
                $objSession->set('contactosPunto', $arrayContactosPunto);
                $objSession->set('formasContactoPunto', $arrayFormasContactoPto);
               
            }
        }
        
        if($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN')
        {
            $arrayParamCiclo                    = array();
            $arrayParamCiclo['intIdPersonaRol'] = $intIdPersonaRol;
            //Obtengo Ciclo de Facturacion asignado en el Cliente
            $arrayPersEmpRolCaracCicloCliente = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                          ->getCaractCicloFacturacion($arrayParamCiclo);
            if( isset($arrayPersEmpRolCaracCicloCliente['intIdPersonaEmpresaRolCaract'])
                    && !empty($arrayPersEmpRolCaracCicloCliente['intIdPersonaEmpresaRolCaract']) )
            {
                $objSession->set('cicloFacturacionCliente', $arrayPersEmpRolCaracCicloCliente['strNombreCiclo']);
            }           

            //MODELO PREDICTIVO
            if(is_array($arrayParametrosAdicionales) && !empty($arrayParametrosAdicionales))
            {

                $strUser = $objSession->get('user');

                //obtengo la cabecera de los parámetros del modelo predictivo            
                $objAdmiParametroCab  = $this->_em->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                    array('nombreParametro' => 'PARAMETROS_MODELO_PREDICTIVO',
                    'estado'          => 'Activo'));

                $arrayParamVisibleParaUsuario = array();
                $arrayParamVisibleParaUsuario['strUser']             = $strUser;
                $arrayParamVisibleParaUsuario['objAdmiParametroCab'] = $objAdmiParametroCab;
                $arrayParamVisibleParaUsuario['codEmpresa']          = $intCodEmpresa;
                $arrayParamVisibleParaUsuario['serviceUtil']         = $arrayParametrosAdicionales['serviceUtil'];
                $arrayParamVisibleParaUsuario['strIpSession']        = $arrayParametrosAdicionales['strIpSession'];

                  //Verificar si el modelo predictivo es visible para este usuario según su perfil
                 // $boolModeloPredictivoVisible = $this->isVisibleParaUsuario($arrayParamVisibleParaUsuario);
                 $boolModeloPredictivoVisible = $this->isVisibleParaUsuario($arrayParamVisibleParaUsuario);



                if ($boolModeloPredictivoVisible) 
                {
                    

                    $arrayParamRetencionMinima = array();

                    $arrayParamRetencionMinima['strIdentificacion']   = $objInfoPersona->getIdentificacionCliente();
                    $arrayParamRetencionMinima['strUser']             = $strUser;
                    $arrayParamRetencionMinima['objAdmiParametroCab'] = $objAdmiParametroCab;
                    $arrayParamRetencionMinima['codEmpresa']          = $intCodEmpresa;
                    $arrayParamRetencionMinima['serviceUtil']         = $arrayParametrosAdicionales['serviceUtil'];
                    $arrayParamRetencionMinima['strIpSession']        = $arrayParametrosAdicionales['strIpSession'];

                     ///Verificar que el cliente no tenga tareas de retención mayor o igual a n meses
                    //n es un valor parametrizable que está registrado en la base de datos                    
                    //$boolNoTieneRetencion = $this->isLibreDeRetencionMinima($arrayParamRetencionMinima);
                    $boolNoTieneRetencion = $this->isLibreDeRetencionMinima($arrayParamRetencionMinima);


                    if ($boolNoTieneRetencion) 
                    {
                        
                        $arrayParamModeloPredictivo = array();
                        $arrayParamModeloPredictivo['strIdentificacion']   = $objInfoPersona->getIdentificacionCliente();
                        $arrayParamModeloPredictivo['strUser']             = $strUser;
                        $arrayParamModeloPredictivo['strLogin']            = $objInfoPunto->getLogin();
                        $arrayParamModeloPredictivo['serviceUtil']         = $arrayParametrosAdicionales['serviceUtil'];
                        $arrayParamModeloPredictivo['strIpSession']        = $arrayParametrosAdicionales['strIpSession'];
                        $arrayParamModeloPredictivo['serviceRDA']          = $arrayParametrosAdicionales['serviceRDA'];

                        //Obtengo la información del modelo predictivo
                        $arrayModeloPredictivo = $this->getArrayModeloPredictivo($arrayParamModeloPredictivo);

                        $objSession->set('modeloPredictivo', $arrayModeloPredictivo);
                    }
                }  
            }
                     
            
        }

        $sql = "SELECT 
                                    d.id as IdRol, d.descripcionRol, e.id as IdTipoRol, e.descripcionTipoRol 
                         FROM 
                                 schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                                 schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e,
                                 schemaBundle:InfoOficinaGrupo og, schemaBundle:InfoEmpresaGrupo eg
                         WHERE 
                                 a.id=b.personaId
                                 AND b.empresaRolId=c.id
                                 AND c.rolId=d.id 
                                 AND d.tipoRolId=e.id 
                                 AND b.oficinaId=og.id
                                 AND og.empresaId=eg.id
                                 AND b.id = '$intIdPersonaRol'  
                                 AND c.empresaCod='$intCodEmpresa' 
                                 AND b.estado in ('Activo','Modificado','Pend-convertir','Cancel','Cancelado') ".
                                 " AND c.estado in ('Activo','Modificado','Pend-convertir') ORDER BY b.feCreacion DESC";
        $query = $this->_em->createQuery($sql)->setMaxResults(1);
                //echo $query->getSQL();die;
        $entity =  $query->getSingleResult();
        
        //variables para session del punto cliente
        $arrayPtoCliente = array();
        $arrayPtoCliente['id']                     = $objInfoPunto->getId();
        $arrayPtoCliente['login']                  = $objInfoPunto->getLogin();
        $arrayPtoCliente['descripcion']            = substr($objInfoPunto->getDescripcionPunto(),0,50);
        $arrayPtoCliente['direccion']              = substr($objInfoPunto->getDireccion(),0,50);
        $arrayPtoCliente['cobertura']              = sprintf("%s",$intCobertura); 
        $arrayPtoCliente['tipo_negocio']           = sprintf("%s",$intTipoNegocio); 
        $arrayPtoCliente['tipo_ubicacion']         = sprintf("%s",$intTipoUbicacion); 
        $arrayPtoCliente['id_sector']              = $objInfoPunto->getSectorId()->getId();
        $arrayPtoCliente['id_cobertura']           = $intCobertura->getId();
        $arrayPtoCliente['id_tipo_negocio']        = $intTipoNegocio->getId();
        $arrayPtoCliente['id_tipo_ubicacion']      = $intTipoUbicacion->getId();
        $arrayPtoCliente['id_persona_empresa_rol'] = $objPersonaEmpresaRol->getId();
        $arrayPtoCliente['id_persona']             = $objInfoPersona->getId();
        $arrayPtoCliente['estado']                 = $objInfoPunto->getEstado();
        
        $objSession->set('ptoCliente', $arrayPtoCliente);
        
        //variables para session del cliente
        $arrayCliente['id_persona_empresa_rol'] = $objPersonaEmpresaRol->getId();
        
        $arrayCliente['id']              = $objInfoPersona->getId();
        $arrayCliente['id_persona']      = $objInfoPersona->getId();
        $arrayCliente['razon_social']    = $objInfoPersona->getRazonSocial();
        $arrayCliente['nombres']         = $objInfoPersona->getNombres();
        $arrayCliente['apellidos']       = $objInfoPersona->getApellidos();
        $arrayCliente['identificacion']  = $objInfoPersona->getIdentificacionCliente();
        $arrayCliente['direccion']       = $objInfoPersona->getDireccion();
        $arrayCliente['id_oficina']      = $objInfoOficinaGrupo->getId();
        $arrayCliente['nombre_oficina']  = $objInfoOficinaGrupo->getNombreOficina();
        $arrayCliente['estado']          = $objInfoPersona->getEstado();
        
        $arrayCliente['id_rol']          = $entity["IdRol"];
        $arrayCliente['nombre_rol']      = $entity["descripcionRol"];
        $arrayCliente['id_tipo_rol']     = $entity["IdTipoRol"];
        $arrayCliente['nombre_tipo_rol'] = $entity["descripcionTipoRol"];

        //es recontratacion
        $boolEsRecontratacion = 
            $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->esRecontratacion($objInfoPersona->getId(),$intCodEmpresa);	
        $arrayCliente['esRecontratacion'] = $boolEsRecontratacion; 
        
        //Se obtiene la fecha de nacimiento de la persona.
        $arrayCliente['fechaNacimiento'] = 
            $objInfoPersona->getFechaNacimiento() != null ? $objInfoPersona->getFechaNacimiento()->format('d-M-Y') : "";
        //Se invoca función para obtener la edad de la persona.
        $intEdad = $this->_em->getRepository('schemaBundle:InfoPersona')
                                         ->getEdadPersona(array('intIdPersona' => $objInfoPersona->getId()));
        $arrayCliente['edad'] = $intEdad > 0 ? $intEdad : "";
        
        $objSession->set('cliente', $arrayCliente);
        
        $strEsVip        = '';
        //defino la variable para la marca del Vip Técnico del cliente
        $strEsVipTecnico = '';
            
        if($strPrefijoEmpresa == 'TN')
        {
            // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
            $arrayParams       = array('ID_PER'  => $objPersonaEmpresaRol->getId(),
                                       'EMPRESA' => $intCodEmpresa,
                                       'ESTADO'  => 'Activo');
            $entityContratoDat = $this->_em->getRepository('schemaBundle:InfoContratoDatoAdicional')->getResultadoDatoAdicionalContrato($arrayParams);
            $strEsVip          = $entityContratoDat && $entityContratoDat->getEsVip() ? 'Sí' : 'No';

            // Buscamos en InfoPersonaEmpresaRolCarac para verificar que sea VIP Técnico
            $objAdmiCaractVipTecnico  = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('ID_VIP_TECNICO');
            if( is_object($objAdmiCaractVipTecnico) )
            {
                $objPerEmpCaracVipTec = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(array('personaEmpresaRolId' => $objPersonaEmpresaRol->getId(),
                                                                      'caracteristicaId'    => $objAdmiCaractVipTecnico->getId(),
                                                                      'estado'              => 'Activo'));
                if( is_object($objPerEmpCaracVipTec) )
                {
                    $strEsVipTecnico  = $objPerEmpCaracVipTec->getValor();
                }
            }
        }

        $objSession->set('esVIP', $strEsVip);
        //seteo la sessión de la marca Vip Técnico del cliente
        $objSession->set('esVipTecnico', $strEsVipTecnico);
        
        // Si es VIP Técnico obtengo los datos de los ingenieros VIP
        $arrayDatosIngenierosVip    = array();
        if( $strEsVipTecnico == 'Sí' )
        {
            $arrayParametrosVip     = array();
            $objAdmiCaractCiudad    = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica('ID_VIP_CIUDAD');
            if(is_object($objAdmiCaractCiudad))
            {
                $arrayParametrosVip['strCaractCiudad'] = $objAdmiCaractCiudad->getID();
            }
            $objAdmiCaractExtension = $this->_em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('EXTENSION USUARIO');
            if(is_object($objAdmiCaractExtension))
            {
                $arrayParametrosVip['strCaractExt']    = $objAdmiCaractExtension->getID();
            }

            $arrayParametrosVip['intIdPerEmp']         = $objPersonaEmpresaRol->getId();
            $arrayParametrosVip['strCaracteristica']   = 'ID_VIP';
            $arrayParametrosVip['strEstado']           = 'Activo';
            $arrayParametrosVip['strEmpresa']          = $intCodEmpresa;

            $arrayResultadosIngenierosVip           = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->getIngenierosVipTecnicoCliente($arrayParametrosVip);
            if( $arrayResultadosIngenierosVip['status'] == 'OK' )
            {
                $arrayDatosIngenierosVip            = $arrayResultadosIngenierosVip['result'];
            }
        }
        //seteo la sessión de los datos de los ingenieros Vip del cliente
        $objSession->set('ingenierosVip', $arrayDatosIngenierosVip);

        //formas contacto cliente para toolbar
        $arrayClienteContactos = 
            $this->_em->getRepository('schemaBundle:InfoPersonaFormaContacto')->getFormasContactoParaSession($objInfoPersona->getId());
        if($arrayClienteContactos)
        {
            $objSession->set('clienteContactos', $arrayClienteContactos);
        }
        
        $arrayParametros['PUNTOID'] = intval($intIdPunto);
        $arrayParametros['FORMA1']  = '%telefono%';
        $arrayParametros['FORMA2']  = '%correo%';
        $arrayParametros['ESTADO']  = 'Activo';
        $puntoContactos = $this->_em->getRepository('schemaBundle:InfoPuntoFormaContacto')->getFormasContactoParaSession($arrayParametros);
        if($puntoContactos)
        {
            $objSession->set('puntoContactos', $puntoContactos);
        }
    }
    
    public function findPtosEdificiosActivosPorEmpresa($codEmpresa, $nombre, $limit, $page, $start, $order = NULL)
    {
        $query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial, b.nombres, b.apellidos, a.direccion, e.nombreEdificio
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b,
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e
                WHERE
                a.personaEmpresaRolId = c.id AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                a.id = e.puntoId AND "
                . ($nombre ? " UPPER(a.descripcionPunto) like :nombre AND " : "") .
                " d.empresaCod = :codEmpresa AND
                e.esEdificio='S' "
                . ($order == 'nombreEdificio' ? 'ORDER BY e.nombreEdificio' : ''));
        $query->setParameter('codEmpresa', $codEmpresa);
        if ($nombre)
        {
            $query->setParameter('nombre', '%'.strtoupper($nombre).'%');
        }
        $total = count($query->getResult()); // FIXME usar count SQL
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }
    /**
     * findPtosPorEmpresaPorClientePorRol    
     * Se Elimina variable  $esPadre = "S" quemada en el codigo de la funcion ya que esta generando BUG en el proceso de 
     * Cambio de Razón Social, debido a que no esta pasando todos los logines en el CRS y solo migra los puntos que son padres de Facturacion.
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 Modificada 04-02-2019
     * @since   1.0
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 1.2 Modificada 19-08-2022 - Se excluye puntos con estado Eliminado, ya que estos son reversos de CRS CD
     *
     * @param $codEmpresa Codigo de la empresa en sesion
     * @param $idCliente  Id de la Persona cliente en sesion                       
     * @param $nombre     Login o Punto del cliente
     * @param $rol        identificacion del Rol 
     * @param $limit      Limite maximo de registros
     * @param $page       Numero de Paginas
     * @param $start      Registro de Inicio
     * @param $esPadre    Es padre de Facturacion S/N
     * @return $arrayResultado
     */    
	public function findPtosPorEmpresaPorClientePorRol($codEmpresa, $idCliente, $nombre, $rol, $limit, $page, $start, $esPadre )
	{                
		$query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial, b.nombres, b.apellidos, a.direccion, a.estado
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, "
		        . ($esPadre ? " schemaBundle:InfoPuntoDatoAdicional ptoAd, " : "") .
                " schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d,
				schemaBundle:AdmiRol f, schemaBundle:AdmiTipoRol g
                WHERE "
		        . ($esPadre ? " a.id=ptoAd.puntoId AND ptoAd.esPadreFacturacion = :esPadre AND " : "") .
                " a.personaEmpresaRolId = c.id AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND "
                . ($nombre ? " UPPER(a.login) like :nombre AND " : "") .
                " d.empresaCod = :codEmpresa AND
                b.id = :idCliente AND
				d.rolId = f.id AND
				f.tipoRolId = g.id AND
				g.descripcionTipoRol = :rol AND
                a.estado <> :estadoDif");
		$query->setParameter('codEmpresa', $codEmpresa);
		$query->setParameter('idCliente', $idCliente);
		$query->setParameter('rol', $rol);
        $query->setParameter('estadoDif', 'Eliminado');
		if ($esPadre)
		{
		    $query->setParameter('esPadre', $esPadre);
		}
		if ($nombre)
		{
		    $query->setParameter('nombre', '%'.strtoupper($nombre).'%');
		}
        $total = count($query->getResult()); // FIXME usar count SQL
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }

    /**
     * Función que obtiene el párametro que contiene el arreglo con los 
     * id de los productos que aplican para paquete de horas de soporte.
     * @author Victor Peña <vpena@telconet.ec>
     * @version 1.0
     * @since 24-05-2023
     */
    public function findParametroHorasSoporte($strNombreParametro, $strUsrCreacion, $strCodEmpresa)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQueryData       = $this->_em->createNativeQuery(null,$objRsm);
        
        $strSql = " SELECT VALOR1
                        FROM DB_GENERAL.ADMI_PARAMETRO_DET 
                        WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                              WHERE NOMBRE_PARAMETRO = :strNombreParametro AND USR_CREACION = :strUsrCreacion 
                                              AND ESTADO = 'Activo') 
                        AND USR_CREACION = :strUsrCreacion
                        AND ESTADO = 'Activo' 
                        AND EMPRESA_COD = :strCodEmpresa";

              
        $objRsm->addScalarResult('VALOR1', 'valor1', 'string');

        $objQueryData->setParameter('strUsrCreacion', $strUsrCreacion);
        $objQueryData->setParameter('strNombreParametro', $strNombreParametro);
        $objQueryData->setParameter('strCodEmpresa', $strCodEmpresa);

        $objQueryData->setSQL($strSql);

        

        $arrayDatos = $objQueryData->getResult();

        $arrayProductos = json_decode($arrayDatos[0]['valor1'])->SI;

        return $arrayProductos;
    }

    /**
     * Función que obtiene los puntos por empresa y por grupo.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     * 
     * 
     * Se modificò para que no aparecieran los logines del propio producto
     * Y se parametrizò los estados a excluir en la busqueda..
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1
     * @since 06-02-2023
     * 
     * Se modifica query de búsqueda con el listado de productos 
     * parametrizados que aplican para paquete de horas
     * @author Victor Peña <vpena@telconet.ec>
     * @version 1.2
     * @since 24-05-2023
     * 
     */
    public function findPtosPorEmpresaPorGrupo($intCodEmpresa, $strLogin)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQueryData   = $this->_em->createNativeQuery(null,$objRsm);
        // se consulta los paràmetros para validar los estados del servicio que no aparezcan
        $objParametroDetEstados =   $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne("ESTADOS_EXCLUIDOS_PARA_ASOCIAR", //nombre parametro cab
                            "SOPORTE", "", 
                            "ESTADOS PARA CONDICIONAR LOS SERVICIOS QUE NO DEBEN MORSTRASE EN LA PANTALLA DE ASOCIAR", //descripcion det
                            "", "", "", "", "", $intCodEmpresa
                        );

        $arrayProductos = $this->findParametroHorasSoporte('PROD_APLICA_PAQUETE_HORAS', 'vpena', $intCodEmpresa);

        if($objParametroDetEstados)
        {
            $strValorUno    = $objParametroDetEstados['valor1'];
            $strValorDos    = $objParametroDetEstados['valor2'];
            $strValorTres   = $objParametroDetEstados['valor3'];
            $strValorCuat   = $objParametroDetEstados['valor4'];

        $strSql = " SELECT DISTINCT(B.ID_PUNTO), B.LOGIN, B.DESCRIPCION_PUNTO, B.DIRECCION, B.ESTADO ". 
                  " FROM DB_COMERCIAL.INFO_SERVICIO A, DB_COMERCIAL.INFO_PUNTO B,  DB_COMERCIAL.ADMI_PRODUCTO P
                    WHERE A.PUNTO_ID = B.ID_PUNTO 
                    AND B.PERSONA_EMPRESA_ROL_ID = (SELECT PERSONA_EMPRESA_ROL_ID 
                    FROM DB_COMERCIAL.INFO_PUNTO WHERE LOGIN = :strLogin) 
                    AND A.ESTADO NOT IN ('".$strValorUno."', '".$strValorDos."', '".$strValorTres."', '".$strValorCuat."')
                    AND B.ESTADO NOT IN ('Anulado', 'Cancelado')
                    AND A.PRODUCTO_ID = P.ID_PRODUCTO
                    AND A.PRODUCTO_ID IN (:arrayProductos) ";

        $objRsm->addScalarResult('ID_PUNTO', 'id_punto', 'integer');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('DESCRIPCION_PUNTO', 'descripcion_punto', 'string');
        $objRsm->addScalarResult('DIRECCION', 'direccion', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('DESCRIPCION_PRODUCTO', 'Descripcion_Producto', 'string');

        $objQueryData->setParameter('strLogin', $strLogin);
        $objQueryData->setParameter('strCodEmpresa', $intCodEmpresa);
        $objQueryData->setParameter('arrayProductos', $arrayProductos);

        $objQueryData->setSQL($strSql);

        $intTotal = count($objQueryData->getArrayResult());

        $arrayDatos = $objQueryData->getArrayResult();

        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total'] = $intTotal;
        return $arrayResultado;   
    }


    /**
     * getJsonFindLoginesPorPersonaEmpresaRol
     *
     * Método que genera un JSON el cual contiene los puntos de un cliente por criterios de búsqueda
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 26-02-2016     
     
     * @param array $arrayParametros [idper, rol, strEstadoPunto, strDireccion, strFechaDesde, strFechaHasta, strLogin,strNombrePunto, 
     *                                strCodEmpresa, strEsPadre, start, limit, serviceInfoPunto  ]
     * @return Json $arrayResultadoJson[total, ptos]
     *
     */
    public function getJsonFindLoginesPorPersonaEmpresaRol($arrayParametros)
    {      
        $arrayPuntosEncontrados = array();        
        $arrayResultado         = $this->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);
        $objPuntos              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        if($objPuntos)
        {
            foreach($objPuntos as $objPuntos)
            {
                 $arrayPuntosEncontrados[] = array('idPtoCliente'   => $objPuntos['id'],
                                                   'descripcionPto' => $objPuntos['login'],
                                                  );
            }
        }
       
        if(!empty($arrayPuntosEncontrados))
        {            
            $arrayPuntosJson    = json_encode($arrayPuntosEncontrados);
            $arrayResultadoJson = '{"total":"' . $intTotal . '","listado":' . $arrayPuntosJson . '}';
        }
        else
        {
            $arrayPuntosEncontrados[] = array('idPtoCliente'   => "0",
                                              'descripcionPto' => "Not Found",
                                             );
            $arrayPuntosJson          = json_encode($arrayPuntosEncontrados);
            $arrayResultadoJson       = '{"total":"' . $intTotal . '","listado":' . $arrayPuntosJson . '}';            
        }
        return $arrayResultadoJson;        
    }
     /**
     * getJsonFindPtosPorPersonaEmpresaRol
     *     
     * Método que genera un JSON el cual contiene los puntos de un cliente por criterios de búsqueda
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-02-2016     
     
     * @param array $arrayParametros [idper, rol, strEstadoPunto, strDireccion, strFechaDesde, strFechaHasta, strLogin,strNombrePunto, 
     *                                strCodEmpresa, strEsPadre, start, limit, serviceInfoPunto  ]
     * @return Json $arrayResultadoJson[total, ptos]
     *
     */
    public function getJsonFindPtosPorPersonaEmpresaRol($arrayParametros)
    {        
        $arrayPuntosEncontrados = array();        
        $arrayResultado         = $this->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);
        $objPuntos              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        $strPermiteAnularPunto  = '';
                
        foreach($objPuntos as $objPuntos)
        {
            $strEsPadre                = 'No';
            $strCiudad                 = '';
            $strParroquia              = '';
            $strSector                 = '';
            $intCiudadId               = '';
            $intParroquiaId            = '';
            $intSectorId               = '';
            $srtDatosEnvio             = '';
            $strDireccionEnvio         = '';
            $strTelefonoEnvio          = '';
            $strEmailEnvio             = '';
            $strNombreEnvio            = '';
            $objInfoPuntoDatoAdicional = $this->_em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($objPuntos['id']);
            
            if($objInfoPuntoDatoAdicional)
            {
                if($objInfoPuntoDatoAdicional->getEsPadreFacturacion() == 'S')
                {
                    $strEsPadre = 'Si';
                }
                else
                {
                    $strEsPadre = 'No';
                }
                if($objInfoPuntoDatoAdicional->getSectorId() != null)
                {
                    $objSector      = $objInfoPuntoDatoAdicional->getSectorId();
                    $objParroquia   = $objSector->getParroquiaId();
                    $objCanton      = $objParroquia->getCantonId();
                    $strCiudad      = $objCanton->getNombreCanton();
                    $intCiudadId    = $objCanton->getId();
                    $strParroquia   = $objParroquia->getNombreParroquia();
                    $intParroquiaId = $objParroquia->getId();
                    $strSector      = $objSector->getNombreSector();
                    $intSectorId    = $objSector->getId();
                }                
                if($objInfoPuntoDatoAdicional->getDatosEnvio() == 'S')
                {
                    $srtDatosEnvio = 'Si';
                }
                else
                {
                    $srtDatosEnvio = 'No';
                }
                $strDireccionEnvio = $objInfoPuntoDatoAdicional->getDireccionEnvio();
                $strTelefonoEnvio  = $objInfoPuntoDatoAdicional->getTelefonoEnvio();
                $strEmailEnvio     = $objInfoPuntoDatoAdicional->getEmailEnvio();
                $strNombreEnvio    = $objInfoPuntoDatoAdicional->getNombreEnvio();
            }                        
            $linkVer      = "../../../punto/".$objPuntos['id']."/".$arrayParametros['rol']."/show";
            $linkEditar   = "#";
            $linkEliminar = "#";
            if($objPuntos['razonSocial'])
            {
                $strCliente = $objPuntos['razonSocial'];
            }
            else
            {
                $strCliente = $objPuntos['nombres'] . ' ' . $objPuntos['apellidos'];
            }
                        
            $strPermiteAnularPunto = $arrayParametros['serviceInfoPunto']->permiteAnularPtoCliente($objPuntos['id']);

            if($objPuntos['estado'] != 'Anulado')
            {
                $arrayPuntosEncontrados[] = array(
                    'idPto'              => $objPuntos['id'],
                    'cliente'            => $strCliente,
                    'login'              => $objPuntos['login'],
                    'descripcionPunto'   => $objPuntos['descripcionPunto'],
                    'direccion'          => $objPuntos['direccion'],
                    'estado'             => $objPuntos['estado'],
                    'nombrePunto'        => $objPuntos['nombrePunto'],
                    'esPadre'            => $strEsPadre,
                    'datosEnvio'         => $srtDatosEnvio,
                    'nombreEnvio'        => $strNombreEnvio,
                    'ciudadEnvio'        => $strCiudad,
                    'parroquiaEnvio'     => $strParroquia,
                    'sectorEnvio'        => $strSector,
                    'id_ciudadEnvio'     => $intCiudadId,
                    'id_parroquiaEnvio'  => $intParroquiaId,
                    'id_sectorEnvio'     => $intSectorId,
                    'direccionEnvio'     => $strDireccionEnvio,
                    'telefonoEnvio'      => $strTelefonoEnvio,
                    'emailEnvio'         => $strEmailEnvio,
                    'linkVer'            => $linkVer,
                    'linkEditar'         => $linkEditar,
                    'linkEliminar'       => $linkEliminar,
                    'permiteAnularPunto' => $strPermiteAnularPunto,
                    'rol'                => $arrayParametros['rol']
                );
            }
        }
        if($intTotal == 0)
        {
            $arrayResultadoJson = '{"total":"0","ptos":[]}';
        }
        else
        {
            $arrayPuntosJson    = json_encode($arrayPuntosEncontrados);
            $arrayResultadoJson = '{"total":"' . $intTotal . '","ptos":' . $arrayPuntosJson . '}';
        }
        return $arrayResultadoJson;
       
    }
    
    /**
     * getResultadoFindPtosPorPersonaEmpresaRol
     *
     * Metodo obtiene los puntos clientes (Logines) por Cliente (Persona_Empresa_Rol) y por Criterios de Busqueda
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-02-2016
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 23-10-2019 Se agrega implementación para excluir por distintos estados.
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.2 09-05-2022 Se agrega latitud y longitud a extraer de la consulta.
     *
     * @param array $arrayParametros [idper, rol, strEstadoPunto, strDireccion, strFechaDesde, strFechaHasta, strLogin,strNombrePunto, 
     *                                strCodEmpresa, strEsPadre, start, limit, serviceInfoPunto  ]
     * @return array $arrayResultadoPuntos[total, registros]
     *
     */
    public function getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros)
    {                     
        $strSqlDatos      ='SELECT pto.id, pto.login, pto.descripcionPunto, pto.nombrePunto, '
                           . 'per.razonSocial,per.nombres,per.apellidos,pto.direccion,pto.estado,pto.latitud,pto.longitud '; 
        $strSqlCantidad   = 'SELECT count(pto) ';
        $strSqlFrom       = 'FROM schemaBundle:InfoPunto pto '                                
                           . ($arrayParametros['strEsPadre'] ? ' INNER JOIN schemaBundle:InfoPuntoDatoAdicional pda WITH pto.id = pda.puntoId '
                           . ' AND pda.esPadreFacturacion = (?4)' : '') .
                           ' INNER JOIN schemaBundle:InfoPersonaEmpresaRol pemprol WITH pto.personaEmpresaRolId = pemprol.id 
                             INNER JOIN schemaBundle:InfoPersona per WITH pemprol.personaId = per.id
                             WHERE pto.personaEmpresaRolId = (?5) '                
                           .($arrayParametros['strLogin'] ? ' AND (LOWER(pto.login) like LOWER(?1)) ' : '')
                           .($arrayParametros['strNombrePunto'] ? ' AND (LOWER(pto.nombrePunto) like LOWER(?2)) ': '')
                           .($arrayParametros['strDireccion'] ? ' AND (LOWER(pto.direccion) like LOWER(?3)) ': '')                
                           .($arrayParametros['strFechaDesde'] ? ' AND pto.feCreacion>=(?6) ' : '')                  
		                   .($arrayParametros['strFechaHasta'] ? ' AND pto.feCreacion<=(?7) ' : '');

        if(isset($arrayParametros['strNotInEstados']) && !empty($arrayParametros['strNotInEstados']))
        {
            $strSqlFrom .= ' AND pto.estado NOT IN (?8) ';
        } else if(isset($arrayParametros['strEstadoPunto']) && !empty($arrayParametros['strEstadoPunto']))
        {
            $strSqlFrom .= ' AND pto.estado = (?8) ';
        }
        
        $strSqlOrderBy   = " ORDER BY pto.id DESC";
        
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        if($arrayParametros['strLogin'] != "")
        { 
            $strQueryDatos->setParameter(1, '%' . $arrayParametros['strLogin'] . '%');  
        }
        if($arrayParametros['strNombrePunto'] != "")
        { 
            $strQueryDatos->setParameter(2, '%' . $arrayParametros['strNombrePunto'] . '%');
        }
        if($arrayParametros['strDireccion'] != "")
        { 
            $strQueryDatos->setParameter(3, '%' . $arrayParametros['strDireccion'] . '%');
        }
        if($arrayParametros['strEsPadre'] !="")
        {
            $strQueryDatos->setParameter(4, $arrayParametros['strEsPadre']);
        }        
        $strQueryDatos->setParameter(5, $arrayParametros['idper']);
        if($arrayParametros['strFechaDesde'] !="" && $arrayParametros['strFechaHasta']!="")
        {
           $strQueryDatos->setParameter(6, $arrayParametros['strFechaDesde']); 
           $strQueryDatos->setParameter(7, $arrayParametros['strFechaHasta']); 
        }

        if(isset($arrayParametros['strNotInEstados']) && !empty($arrayParametros['strNotInEstados']))
        {
            $strQueryDatos->setParameter(8, $arrayParametros['strNotInEstados']);
        }
        else if($arrayParametros['strEstadoPunto'] != "")
        {
            $strQueryDatos->setParameter(8, $arrayParametros['strEstadoPunto']);
        }
        
        $strSqlDatos    .= $strSqlFrom;
        $strSqlDatos    .= $strSqlOrderBy;                     
        
        $strQueryDatos->setDQL($strSqlDatos);       
        $objDatos        = $strQueryDatos->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();                 
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        if($arrayParametros['strLogin'] != "")
        { 
            $strQueryCantidad->setParameter(1, '%' . $arrayParametros['strLogin'] . '%');  
        }
        if($arrayParametros['strNombrePunto'] != "")
        { 
            $strQueryCantidad->setParameter(2, '%' . $arrayParametros['strNombrePunto'] . '%');
        }
        if($arrayParametros['strDireccion'] != "")
        { 
            $strQueryCantidad->setParameter(3, '%' . $arrayParametros['strDireccion'] . '%');
        }
        if($arrayParametros['strEsPadre'] !="")
        {
            $strQueryCantidad->setParameter(4, $arrayParametros['strEsPadre']);
        }
        $strQueryCantidad->setParameter(5, $arrayParametros['idper']);
        if($arrayParametros['strFechaDesde'] !="" && $arrayParametros['strFechaHasta']!="")
        {
           $strQueryCantidad->setParameter(6, $arrayParametros['strFechaDesde']); 
           $strQueryCantidad->setParameter(7, $arrayParametros['strFechaHasta']); 
        }
        if(isset($arrayParametros['strNotInEstados']) && !empty($arrayParametros['strNotInEstados']))
        {
            $strQueryCantidad->setParameter(8, $arrayParametros['strNotInEstados']);
        }
        else if($arrayParametros['strEstadoPunto'] != "")
        {
            $strQueryCantidad->setParameter(8, $arrayParametros['strEstadoPunto']);
        }
        
        $strSqlCantidad .= $strSqlFrom;   
        $strQueryCantidad->setDQL($strSqlCantidad);        
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultadoPuntos['registros'] = $objDatos;
        $arrayResultadoPuntos['total']     = $intTotal;
        
        return $arrayResultadoPuntos;
    }
    
	public function findPtosPorPersonaEmpresaRol($idEmpresa,$idper,$nombre,$limit,$start,$esPadre){
                $nombre=  strtoupper($nombre);               
                $criterio_nombre='';   
                $criterio_es_padre="";
                $criterio_tabla_es_padre="";
                if ($nombre){       
                    $criterio_nombre=" UPPER(a.login) like '%$nombre%' AND ";
                } 
                if ($esPadre){    
                    $criterio_tabla_es_padre=" schemaBundle:InfoPuntoDatoAdicional ptoAd, ";
                    $criterio_es_padre=" a.id=ptoAd.puntoId AND ptoAd.esPadreFacturacion = '$esPadre' AND ";
                }                 
                
		$query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial,b.nombres,b.apellidos,a.direccion,a.estado
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, $criterio_tabla_es_padre
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d
                WHERE
                $criterio_es_padre
                a.personaEmpresaRolId = c.id AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                $criterio_nombre
                d.empresaCod='$idEmpresa' AND
                a.personaEmpresaRolId=$idper ");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	} 

	public function findPtosPorEmpresaPorClientePorCanton($idEmpresa,$idCli,$idCanton,$limit,$page,$start){              
		$query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial,b.nombres,b.apellidos,a.direccion,a.estado
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e,
				schemaBundle:AdmiSector g, schemaBundle:AdmiParroquia h, schemaBundle:AdmiCanton i
                WHERE
				a.sectorId=g.id AND
				g.parroquiaId=h.id AND
				h.cantonId=i.id AND	
                a.personaEmpresaRolId = c.id AND
				i.id=$idCanton AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                a.id = e.puntoId AND
                d.empresaCod='$idEmpresa' AND
                c.personaId=$idCli");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
				//echo $total;die;
		return $resultado;
	}  
    
    public function findPtosPorEmpresaPorCanton($codEmpresa, $login, $idCanton, $limit, $page, $start)
	{              
        $query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial, b.nombres, b.apellidos, a.direccion, a.estado
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e,
                schemaBundle:AdmiSector g, schemaBundle:AdmiParroquia h, schemaBundle:AdmiCanton i
                WHERE
                a.sectorId = g.id AND
                g.parroquiaId = h.id AND
                h.cantonId=i.id AND	
                a.personaEmpresaRolId = c.id AND
                i.id = :idCanton AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                a.id = e.puntoId AND
                a.login like :login AND
                d.empresaCod = :codEmpresa");
        $query->setParameter('idCanton', $idCanton);
        $query->setParameter('login', '%'.$login.'%');
        $query->setParameter('codEmpresa', $codEmpresa);
        $total = count($query->getResult()); // FIXME usar count SQL
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    } 
    
    
    /*
     * Documentación para el método 'findPtosPadrePorEmpresaPorCliente'.
     *
     * Retorna un listado de puntos padre facturación por empresa y cliente.
     *
     * @param $idEmpresa    Integer: Id de la empresa.
     * @param $idCli        Integer: Id Persona Empresa Rol.
     * @param $arrayEstados Array:   Listado de estados
     * 
     * @return Array Listado de puntos padre de facturación.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 29-07-2016
     * @since   1.0
     * Se agrega el filtro de exclusión por estados del punto.
     * Se realizan los setParameters respectivos.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 18-08-2016
     * Se corrige el escenario en que no se filtra por estados que se seten sólo los parámetros requeridos.
     */
    public function findPtosPadrePorEmpresaPorCliente($idEmpresa, $idCli, $arrayEstados = null)
    {
        try
        {
            $objQuery = $this->_em->createQuery();

            $strDQL   = "SELECT a.id, a.login, a.descripcionPunto, b.razonSocial,b.nombres,b.apellidos,a.direccion,a.estado
                         FROM
                         schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                         schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e
                         WHERE a.personaEmpresaRolId = c.id AND
                               b.id                  = c.personaId AND
                               c.empresaRolId        = d.id AND
                               a.id                  = e.puntoId AND
                               d.empresaCod          = :EMPRESA AND
                               c.personaId           = :CLIENTE AND 
                               e.esPadreFacturacion  = :ES_PADRE_FACT ";

            if($arrayEstados)
            {
                $strDQL .= 'AND a.estado not in (:ESTADOS) ';
                $objQuery->setParameter("ESTADOS", $arrayEstados);
            }
            
            $objQuery->setParameter("EMPRESA",       $idEmpresa);
            $objQuery->setParameter("CLIENTE",       $idCli);
            $objQuery->setParameter("ES_PADRE_FACT", 'S');

            return $objQuery->setDQL($strDQL)->getResult();
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Documentación para el método 'getJsonEjecutivosCobranza'.
     *
     * Retorna la cadena Json de los Ejecutivos de Cobranza
     *
     * @param Array $arrayParametros['EMPRESA']      String: Código de la empresa
     *                              ['DEPARTAMENTO'] String: Nombre del departamento
     *                              ['ESTADOS']      String: Estado de info_persona_empresa_rol
     *                              ['ROLES']        String: Roles que debe tener el ejecutivo (RECAUDADOR|COBRANZA)
     *                              ['EJECUTIVO']    String: Nombre del Ejecutivo
     *                              ['START']        Int   : Inicio de la paginación
     *                              ['LIMIT']        Int   : Rango máximo de la paginación
     * 
     * @return Array Listado de Ejecutivos de Cobranza
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function getJsonEjecutivosCobranza($arrayParametros)
    {
        $objResultado = $this->getResultadoEjecutivosCobranza($arrayParametros);
       
        if(empty($objResultado['ERROR']))
        {
            $strJsonResultado = '{"total":"' . $objResultado['TOTAL'] . '","registros":' . json_encode($objResultado['REGISTROS']) . '}';
        }
        else
        {
            $strJsonResultado = '{"total":"0", "registros":[], "error":[' . $objResultado['ERROR'] . ']}';
        }

        return $strJsonResultado;
    }
    
    /**
     * Documentación para el método 'getResultadoEjecutivosCobranza'.
     *
     * Retorna el listado paginado de los ejecutivos de cobranza
     *
     * @param Array $arrayParametros['EMPRESA']      String: Código de la empresa
     *                              ['DEPARTAMENTO'] String: Nombre del departamento
     *                              ['ESTADOS']      String: Estado de info_persona_empresa_rol
     *                              ['ROLES']        String: Roles que debe tener el ejecutivo(RECAUDADOR|COBRANZA)
     *                              ['EJECUTIVO']    String: Nombre del Ejecutivo
     *                              ['START']        Int   : Inicio de la paginación
     *                              ['LIMIT']        Int   : Rango máximo de la paginación
     * 
     * @return Array Listado de Ejecutivos de Cobranza
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function getResultadoEjecutivosCobranza($arrayParametros)
    {
        $arrayResult     = array();
        $arrayEjecutivos = array();
        
        $intStart = 0;
        $intLimit = 0;
        
        try
        {
            // Se obtiene el Objeto Navite Query para reformular el SQL y obtener el total de registros.
            $objEjecutivosCobranzaNativeQuery = $this->getEjecutivosCobranzaNativeQuery($arrayParametros);
            
            if(empty($objEjecutivosCobranzaNativeQuery['ERROR']))
            {
                $objNativeQuery = $objEjecutivosCobranzaNativeQuery['OBJ_QUERY'];
                $strQuery       = $objNativeQuery->getSQL();
                
                $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
                
                $intTotalRegistros   = $objNativeQuery->getSingleScalarResult(); // Se obtiene el total de registros
                
                if($intTotalRegistros > 0)
                {
                    $objNativeQuery->setSQL($strQuery);

                    // Se define el Inicio y el Límite de la paginación.
                    if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                    {
                        $intStart  = $arrayParametros['LIMIT'];
                        $intLimit = $arrayParametros['START'];
                    }
                    
                    $arrayEjecutivos = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                }
                
                $arrayResult['REGISTROS'] = $arrayEjecutivos;
                $arrayResult['TOTAL']     = $intTotalRegistros;
                $arrayResult['ERROR']     = '';
            }
            else
            {
                $arrayResult['ERROR'] = $objEjecutivosCobranzaNativeQuery['ERROR'];
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'Error: ' . $ex->getMessage(); // Se almacena el error por excepción
        }

        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'getEjecutivosCobranzaNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención de los ejecutivos de cobranza
     *
     * @param Array $arrayParametros['EMPRESA']      String: Código de la empresa
     *                              ['DEPARTAMENTO'] String: Nombre del departamento
     *                              ['ESTADOS']      String: Estado de info_persona_empresa_rol
     *                              ['ROLES']        String: Roles que debe tener el ejecutivo(RECAUDADOR|COBRANZA)
     *                              ['EJECUTIVO']    String: Nombre del Ejecutivo
     * costoQuery: 86   
     * 
     * @return Array Listado de Ejecutivos de Cobranza
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    private function getEjecutivosCobranzaNativeQuery($arrayParametros)
    {
        try
        {
            $objMappingBuilder = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objMappingBuilder);
            $strEjecutivo      = '';

            if(isset($arrayParametros['EJECUTIVO']) && $arrayParametros['EJECUTIVO'] != '')
            {
                $strEjecutivo = "AND CONCAT(UPPER(P.NOMBRES),CONCAT(' ',UPPER(P.APELLIDOS))) LIKE UPPER(:EJECUTIVO)";
                $objNativeQuery->setParameter('EJECUTIVO', '%' . $arrayParametros['EJECUTIVO'] . '%');
            }

            $strSQL = " SELECT P.LOGIN, P.NOMBRES || ' ' || P.APELLIDOS EJECUTIVO_COBRANZA
                        FROM INFO_PERSONA P
                        INNER JOIN INFO_PERSONA_EMPRESA_ROL PER ON PER.PERSONA_ID    = P.ID_PERSONA
                        INNER JOIN INFO_EMPRESA_ROL         ER  ON ER.ID_EMPRESA_ROL = PER.EMPRESA_ROL_ID
                        INNER JOIN ADMI_DEPARTAMENTO        D   ON D.ID_DEPARTAMENTO = PER.DEPARTAMENTO_ID
                        INNER JOIN INFO_EMPRESA_GRUPO       EG  ON EG.COD_EMPRESA    = ER.EMPRESA_COD
                                                               AND EG.COD_EMPRESA    = D.EMPRESA_COD
                        INNER JOIN ADMI_ROL                 R   ON R.ID_ROL          = ER.ROL_ID
                        WHERE D.NOMBRE_DEPARTAMENTO = :DEPARTAMENTO
                        AND   D.EMPRESA_COD         = :EMPRESA
                        AND   PER.ESTADO          IN (:ESTADOS)
                        AND   P.LOGIN IS NOT NULL
                        AND   REGEXP_LIKE (UPPER(R.DESCRIPCION_ROL), :ROLES)
                        $strEjecutivo
                        GROUP BY P.LOGIN, P.NOMBRES,  P.APELLIDOS
                        ORDER BY EJECUTIVO_COBRANZA";

            $objNativeQuery->setParameter('DEPARTAMENTO', $arrayParametros['DEPARTAMENTO']);
            $objNativeQuery->setParameter('EMPRESA', $arrayParametros['EMPRESA']);
            $objNativeQuery->setParameter('ESTADOS', $arrayParametros['ESTADOS']);
            $objNativeQuery->setParameter('ROLES', $arrayParametros['ROLES']);

            $objMappingBuilder->addScalarResult('LOGIN', 'login', 'string');
            $objMappingBuilder->addScalarResult('EJECUTIVO_COBRANZA', 'ejecutivoCobranza', 'string');
            $objMappingBuilder->addScalarResult('TOTAL', 'total', 'integer');

            $objNativeQuery->setSQL($strSQL);

            $arrayResult['OBJ_QUERY'] = $objNativeQuery->setSQL($strSQL);
            $arrayResult['ERROR']     = '';
        }
        catch(\Exception $ex)
        {
            $arrayResult['ERROR'] = 'ERROR: ' . $ex->getMessage();
        }
        return $arrayResult;
    }

    /**
     * Documentación para el método 'getJsonPuntosCliente'.
     *
     * Retorna la cadena Json de Todos los Puntos del Cliente por empresa
     *
     * @param Array $arrayParametros['EMPRESA']    String : Código de la empresa
     *                              ['PERSONA']    Integer: Id_Persona_Empresa_Rol
     *                              ['START']      Integer: Inicio de la paginación
     *                              ['LIMIT']      Integer: Rango máximo de la paginación
     * 
     * @return Array Listado de Puntos del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function getJsonPuntosCliente($arrayParametros)
    {
        $arrayPuntosCliente = array();
        
        $intStart = 0;
        $intLimit = 0;
        
        try
        {
            $objNativeQuery = $this->getPuntosClienteNativeQuery($arrayParametros);
            $strQuery       = $objNativeQuery->getSQL();
            
            $objNativeQuery->setSQL("SELECT COUNT(*) AS TOTAL FROM ($strQuery)");
            
            $intTotalRegistros = $objNativeQuery->getSingleScalarResult();
            
            if($intTotalRegistros > 0)
            {
                $objNativeQuery->setSQL($strQuery);

                // Se define el Inicio y el Límite de la paginación.
                if(isset($arrayParametros['LIMIT']) &&  isset($arrayParametros['START']))
                {
                    $intStart  = $arrayParametros['LIMIT'];
                    $intLimit = $arrayParametros['START'];
                }

                $arrayPuntosCliente = $this->setQueryLimit($objNativeQuery, $intLimit, $intStart)->getResult();
                
                return '{"total":' . $intTotalRegistros . ',"registros":' . json_encode($arrayPuntosCliente) . '}';
            }
            else
            {
                return '{"total":0,"registros":[]}';
            }
        }
        catch(\Exception $ex)
        {
            return '{"total":"0","error":"' . $ex->getMessage() . '"}';
        }
    }    
    
    /**
     * Documentación para el método 'getPuntosClienteNativeQuery'.
     *
     * Retorna el objeto NativeQuery para la obtención de los puntos del Cliente
     *
     * @param Array $arrayParametros['EMPRESA']    String : Código de la empresa
     *                              ['PERSONA']    Integer: Id_Persona_Empresa_Rol
     *                              ['LOGIN']      String:  Login del Punto
     *                              ['ESTADO']     String:  Estado del Punto
     * 
     * @return Array Listado de Puntos del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    private function getPuntosClienteNativeQuery($arrayParametros)
    {
        $rsmBuilder = new ResultSetMappingBuilder($this->_em);
        $ntvQuery   = $this->_em->createNativeQuery(null, $rsmBuilder);
        
        $strAndWhere = '';
        
        // validaciones para agregar filtros del query
        if(isset($arrayParametros['LOGIN']) && $arrayParametros['LOGIN'] != '')
        {
            $strAndWhere .= 'AND UPPER(P.LOGIN) LIKE UPPER(:LOGIN) ';
            $ntvQuery->setParameter('LOGIN', '%'.$arrayParametros['LOGIN'].'%');
        }
        
        if(isset($arrayParametros['ASIGNADO']) && $arrayParametros['ASIGNADO'] != 'Todos')
        {
            $strYesOrNot = '';
            if($arrayParametros['ASIGNADO'] == 'SI')
            {
                $strYesOrNot = 'NOT';
            }
            $strAndWhere .= "AND   P.USR_COBRANZAS IS $strYesOrNot NULL";
        }
        
        $sqlQuery   = " SELECT P.ID_PUNTO, P.LOGIN, P.ESTADO, P.DIRECCION, P.NOMBRE_PUNTO NOMBRE,
                        CASE WHEN PRS.APELLIDOS IS NULL THEN 'No-Asignado' ELSE PRS.NOMBRES || ' ' || PRS.APELLIDOS END EJECUTIVO  
                        FROM           INFO_PUNTO               P
                            INNER JOIN INFO_PERSONA_EMPRESA_ROL PER ON PER.ID_PERSONA_ROL = P.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN INFO_EMPRESA_ROL         ER  ON ER.ID_EMPRESA_ROL  = PER.EMPRESA_ROL_ID
                            LEFT  JOIN INFO_PERSONA             PRS ON PRS.LOGIN          = P.USR_COBRANZAS
                        WHERE ER.EMPRESA_COD =  :EMPRESA
                        AND   PER.PERSONA_ID =  :PERSONA
                        AND   P.ESTADO      IN (:ESTADOS)
                        $strAndWhere
                        ORDER BY P.LOGIN, PRS.NOMBRES, PRS.APELLIDOS, P.ESTADO";
        
        $ntvQuery->setParameter('EMPRESA', $arrayParametros['EMPRESA']);
        $ntvQuery->setParameter('PERSONA', $arrayParametros['PERSONA']);
        $ntvQuery->setParameter('ESTADOS', $arrayParametros['ESTADOS']);
        
        $rsmBuilder->addScalarResult('ID_PUNTO',  'idPunto',      'integer');
        $rsmBuilder->addScalarResult('LOGIN',     'login',        'string');
        $rsmBuilder->addScalarResult('EJECUTIVO', 'usrCobranzas', 'string');
        $rsmBuilder->addScalarResult('ESTADO',    'estado',       'string');
        $rsmBuilder->addScalarResult('DIRECCION', 'direccion',    'string');
        $rsmBuilder->addScalarResult('NOMBRE',    'nombre',       'string');
        
        $rsmBuilder->addScalarResult('TOTAL', 'total', 'integer');

        return $ntvQuery->setSQL($sqlQuery);
    }
    
    /**
     * Método que obtiene la razón social o el nombre de la persona por Login
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 18-04-2018
     *
     */
    public function obtieneTitularPorLogin($arrayParametros)
    {
        $strSql =   "SELECT CASE
                            WHEN P.RAZON_SOCIAL IS NOT NULL
                                THEN P.RAZON_SOCIAL
                                ELSE NVL(P.APELLIDOS,'')
                                || ' '
                                || NVL(P.NOMBRES,'')
                            END AS TITULAR
                    FROM
                        DB_COMERCIAL.INFO_PUNTO PTO,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER,
                        DB_COMERCIAL.INFO_PERSONA P
                    WHERE
                        PTO.ID_PUNTO = :intPuntoId
                        AND   PTO.PERSONA_EMPRESA_ROL_ID = PER.ID_PERSONA_ROL
                        AND   PER.PERSONA_ID = P.ID_PERSONA";
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam("intPuntoId", $arrayParametros["intPuntoId"]);
        $objStmt->execute();
        $arrayResult = $objStmt->fetchAll();
        return $arrayResult;
    }

       /**
        * Método encargado de devolver la información del punto.
        *
        * @since 1.0
        *
        * @author Germán Valenzuela <gvalenzuela@telconet.ec>
        * @version 1.1 01-10-2018 - Se agrega la validación para detectar si es objeto el punto a buscar.
        */
	public function getPuntoParaSession($idPunto)
	{
		$entity = $this->_em->getRepository('schemaBundle:InfoPunto')->findOneById($idPunto);

                if (!is_object($entity))
                {
                    return null;
                }

		$ptoCliente = array();
		$ptoCliente['id'] = $entity->getId();
		$ptoCliente['login'] = $entity->getLogin();
		$ptoCliente['descripcion'] = $entity->getDescripcionPunto();
		$ptoCliente['direccion'] = $entity->getDireccion();
		$ptoCliente['cobertura'] = sprintf("%s",$entity->getPuntoCoberturaId()); 
		$ptoCliente['tipo_negocio'] = sprintf("%s",$entity->getTipoNegocioId()); 
		$ptoCliente['tipo_ubicacion'] = sprintf("%s",$entity->getTipoUbicacionId()); 
		$ptoCliente['id_sector'] = sprintf("%s",$entity->getSectorId()->getId());
		$ptoCliente['id_cobertura'] = sprintf("%s",$entity->getPuntoCoberturaId()->getId());
		$ptoCliente['id_tipo_negocio'] = sprintf("%s",$entity->getTipoNegocioId()->getId());
		$ptoCliente['id_tipo_ubicacion'] = sprintf("%s",$entity->getTipoUbicacionId()->getId());
		$ptoCliente['id_persona_empresa_rol'] = sprintf("%s",$entity->getPersonaEmpresaRolId()->getId());
		$ptoCliente['id_persona'] = sprintf("%s",$entity->getPersonaEmpresaRolId()->getPersonaId()->getId());
		$ptoCliente['estado'] = $entity->getEstado();
		
		return $ptoCliente;
	}
	
	public function findPtosPorEmpresaParaOrden($idEmpresa,$nombre,$limit,$page,$start){
                $nombre=  strtoupper($nombre);               
                $criterio_nombre='';                    
                if ($nombre){       
                    $criterio_nombre=" UPPER(a.descripcionPunto) like '%$nombre%' AND ";
                }                 
		$query = $this->_em->createQuery("
                SELECT a.id, a.login, a.descripcionPunto, b.razonSocial,b.nombres,b.apellidos,a.direccion
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e
                WHERE
                a.personaId = b.id AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                a.id = e.puntoId AND
                $criterio_nombre
                d.empresaCod='$idEmpresa' AND
                (a.estado<>'Eliminado' OR a.estado<>'Inactivo')");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	} 
        
        public function findPtosAgrupadosPorPuntosCobertura($idEmpresa,$feIni,$feFin){
            
            	$query =$this->_em->createQuery(
			"SELECT b.nombreJurisdiccion as puntoCobertura, count(a.id) AS total
			FROM schemaBundle:InfoPunto a,schemaBundle:InfoPersonaEmpresaRol c,
                        schemaBundle:InfoEmpresaRol d, schemaBundle:AdmiJurisdiccion b 
			WHERE a.puntoCoberturaId=b.id and 
                        a.personaEmpresaRolId=c.id AND
                        c.empresaRolId=d.id AND
                        d.empresaCod='$idEmpresa' AND
                        a.feCreacion >= :fechaIni AND 
                        a.feCreacion <= :fechaFin 
                        GROUP BY b.nombreJurisdiccion"
			);
		$query->setParameter('fechaIni',new \DateTime($feIni));
		$query->setParameter('fechaFin',new \DateTime($feFin));			

		return $query->getResult();
        }
        
        public function findPtosClienteAgrupadosPorTipoNegocio($idEmpresa,$feIni,$feFin){
			$query = $this->_em->createQuery(
			"SELECT b.nombreTipoNegocio as tipoNegocio, count(a.id) AS total
			FROM schemaBundle:InfoPunto a,schemaBundle:InfoPersonaEmpresaRol c,
                        schemaBundle:InfoEmpresaRol d, schemaBundle:AdmiTipoNegocio b 
			WHERE 
                        a.personaEmpresaRolId=c.id AND
                        c.empresaRolId=d.id AND 
                        d.empresaCod='$idEmpresa' AND                        
                        a.tipoNegocioId=b.id and 
                        a.feCreacion >= :fechaIni AND a.feCreacion <= :fechaFin 
                        GROUP BY b.nombreTipoNegocio"
			)
			->setParameter('fechaIni',new \DateTime($feIni))
			->setParameter('fechaFin',new \DateTime($feFin))
			;			
			return $query->getResult();            
        }
        
    /**
     * generarJsonClientes
     * 
     * Documentación para el método 'generarJsonClientes' que realiza la recuperación de logines Activos y Cancelados 
     * segun el valor del parametro $nombre
     *
     * @param  String  $nombre       Cadena de caracteres que contiene el nombre del login a consultar  
     * 
     * @return String  $resultado    Cadena de caracteres que contiene JSON para retorno de información consultada
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 16-02-2017
     * @since 1.0
     * 
     * @version 1.0
     */
    public function generarJsonClientes($nombre)
    {
        $arr_encontrados = array();
        $qb              = $this->_em->createQueryBuilder();
        $qb->select('punto')
           ->from('schemaBundle:InfoPunto','punto')
           ->andWhere("punto.estado in ('Activo','Cancelado')")
           ->andWhere("punto.login like ?1")
           ->setParameter(1, '%'.$nombre.'%');
           
        $query   = $qb->getQuery();
        $results = $query->getResult();
        
        if ($results)
        {
            $num = count($results);
            foreach ($results as $entidad)
            {
                $arr_encontrados[] = array('id_cliente' => $entidad->getId(),
                                           'cliente'    => $entidad->getLogin());
            }
            $data      = json_encode($arr_encontrados);
            $resultado = '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function findListarPtosClientes($idcliente)
	{
		$query = $this->_em->createQuery(
				    'SELECT p.id
				     FROM schemaBundle:InfoPunto p,
				     schemaBundle:InfoPersonaEmpresaRol iper
				     WHERE iper.personaId = :idcliente AND
				     p.personaEmpresaRolId=iper.id')
			      ->setParameter('idcliente',$idcliente);
		//echo $query->getSQL();
		//die();
		$total=count($query->getResult());
		//echo $total;
		//die();
		$datos =  $query->getResult();
		//print_r($datos);
		//die();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	} 
	
    /**
     * findListarTodosPtosClientes
     * Metodo que devuelve los puntos segun idPersona y idEmpresa
     * @author Andres Montero <amontero@telconet.ec>
     * @since 18/11/2014
     * @param string $punto
     * @param string $idEmpresa
     * @return array
     */    
	public function findListarTodosPtosClientes($idPersona,$idEmpresa)
	{
		$query = $this->_em->createQuery(
				    'SELECT p
				     FROM 
                     schemaBundle:InfoPunto p,
				     schemaBundle:InfoPersonaEmpresaRol iper,
                     schemaBundle:InfoEmpresaRol er
				     WHERE 
                     er.id=iper.empresaRolId
				     AND p.personaEmpresaRolId=iper.id
                     AND er.empresaCod= :idEmpresa 
                     AND iper.personaId = :idcliente');
        $query->setParameter('idcliente',$idPersona);
        $query->setParameter('idEmpresa',$idEmpresa);
		$total=count($query->getResult());
		$datos =  $query->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	}
    
    /**
    * Función que retorna listado de puntos cliente
    *
    * @param mixed  $arrayEstados estados que no deben tomarse en cuenta.
    * @param string $strCodEmpresa codigo de la empresa en sesión.
    * @param string $strPunto login del punto que se desea buscar.
    *
    * @return mixed $resultados Retorna listado de puntos cliente.
    *       
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 02-12-2014
    */ 	
    public function findListarPtosClientesCruce($arrayEstados,$strCodEmpresa,$strPunto)
    {
        $sql = 'SELECT 
		  p 
                FROM   
		  schemaBundle:InfoPunto p,
		  schemaBundle:InfoEmpresaRol c,
		  schemaBundle:InfoPersonaEmpresaRol iper
		WHERE  
		  p.estado not in (:estado) AND 
		  c.empresaCod=:codEmpresa AND 
		  p.personaEmpresaRolId=iper.id AND 
		  iper.empresaRolId=c.id';
	if($strPunto!="")
	    $sql.=" AND LOWER(p.login) like LOWER(:punto)";
	else
	    $sql.="";	  
	    
	$query = $this->_em->createQuery($sql);
        $query->setParameter('estado',$arrayEstados);
        $query->setParameter('codEmpresa',$strCodEmpresa);
        if($strPunto!="")
            $query->setParameter('punto','%'.$strPunto.'%');   
	      
	$total=count($query->getResult());
	$datos =  $query->getResult();
	$resultado['registros']=$datos;
	$resultado['total']=$total;
	return $resultado;
    }
    public function findListadoClientesPorEmpresaPorEstado($arrayEstado,$strCodEmpresa,$strCliente,$arrayRolesPersona){
        $sql="SELECT a
        FROM 
        schemaBundle:InfoPersona a, 
        schemaBundle:InfoPersonaEmpresaRol b, 
        schemaBundle:InfoEmpresaRol c, 
        schemaBundle:AdmiRol d, 
        schemaBundle:AdmiTipoRol e
        WHERE 
        a.id=b.personaId AND
        b.empresaRolId=c.id AND
        c.rolId=d.id AND
        d.tipoRolId=e.id AND
        b.estado not in (:estado) AND 
        c.empresaCod=:codEmpresa AND 
        UPPER(e.descripcionTipoRol)  in(:rolesPersona) ";
        if($strCliente!=""){
            $sql.=" AND (CONCAT(UPPER(a.nombres),CONCAT(
            ' ',UPPER(a.apellidos))) like UPPER(:cliente) OR 
            UPPER(a.razonSocial) like UPPER(:cliente))";
        }else
            $sql.="";
        
        $query = $this->_em->createQuery($sql);        
        $query->setParameter('estado',$arrayEstado);
        $query->setParameter('codEmpresa',$strCodEmpresa);
        $query->setParameter('rolesPersona',$arrayRolesPersona);
        if($strCliente!=""){
            $query->setParameter('cliente','%'.$strCliente.'%');
        }
        $datos = $query->getResult();
        return $datos;
    }  	
	
	public function findListarTodosPtosClientesPorPto($idcliente,$idpunto,$strEmpresaCod)
	{
		if($idcliente!="")
		{
                      
			$query = $this->_em->createQuery(
						'SELECT p
						 FROM schemaBundle:InfoPunto p,
						 schemaBundle:InfoPersonaEmpresaRol iper
                         schemaBundle:InfoEmpresaRol ier
						 WHERE p.personaEmpresaRolId=iper.id
                         AND   iper.empresaRolId=ier.id 
                         AND   ier.empresaCod = :empresaId
                         iper.personaId = :idcliente')
                      ->setParameter('empresaId',$strEmpresaCod)
					  ->setParameter('idcliente',$idcliente);
		}
		
		if($idpunto!="")
		{           
            $query = $this->_em->createQuery(
            "SELECT p
             FROM schemaBundle:InfoPunto p,
                  schemaBundle:InfoPersonaEmpresaRol iper,
                  schemaBundle:InfoEmpresaRol ier
             WHERE p.personaEmpresaRolId = iper.id
                AND   iper.empresaRolId=ier.id 
                AND   ier.empresaCod = :empresaId
                AND   p.login like '%".trim($idpunto)."%'")
             ->setParameter('empresaId',$strEmpresaCod);
		}
		$total =  count($query->getResult()); 
		$datos =  $query->getResult(); 
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	}
        
    /**
     * Documentación para el método 'findTotalPtosCliente'.
     *
     * Obtiene la cantidad total de puntos del cliente.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
     * @version 1.1 22-06-2016
     * @since 1.0
     * Se agrega el filtro de ESTADOS para obtener los punto que NO estén dentro del listado de estados.
     */
    public function findTotalPtosCliente($idcliente,$idEmpresa, $arrayEstados = null)
	{
        $strEstados = '';
        
        if($arrayEstados != null)
        {
            $strEstados = 'a.estado NOT IN (:ESTADOS) AND';
        }
        
		$query = $this->_em->createQuery("
                SELECT count(a) as total
                FROM
                schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e
                WHERE
                a.personaEmpresaRolId = c.id AND
                b.id = c.personaId AND
                c.empresaRolId = d.id AND
                a.id = e.puntoId AND
                $strEstados
                d.empresaCod='$idEmpresa' AND
                c.personaId=$idcliente");                
        
        if($arrayEstados != null)
        {
            $query->setParameter("ESTADOS", $arrayEstados);
        }
        
		$datos =  $query->getSingleResult();
		return $datos;
	}

	public function findPrimerPtoClientePorPersonaEmpresaRolId($personaEmpresaRolId)
	{
		$query = $this->_em->createQuery("
                SELECT a
                FROM
                schemaBundle:InfoPunto a,
                schemaBundle:InfoPuntoDatoAdicional e
                WHERE
                a.id = e.puntoId AND
				a.personaEmpresaRolId=$personaEmpresaRolId
                order by a.id ASC")->setFirstResult(0)->setMaxResults(1);
                //echo $query->getSQL();die;
		$datos =  $query->getOneOrNullResult();
		return $datos;
	}	

    /**
     * Documentación para findPtoClienteActivoPadreFacturacion
     * 
     * Función que se encarga de obtener el punto padre de facturación de un cliente,
     * (Proceso de creación de anticipos cuando se realizan los pagos).
     * 
     * @param array $arrayParametros['intIdPerEmpRol' : Id PersonaEmpresaRol
     *                               'strEstado'      : Estado del Punto ]
     * 
     * @return int Id Punto(Padre de Facturación).
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.0 23-06-2020
     */
    public function findPtoClienteActivoPadreFacturacion($arrayParametros)
    {
        try
        {
            $intIdPunto = str_pad($intIdPunto, 100, " ");
            
            $strSql     = "BEGIN DB_FINANCIERO.FNKG_PROCESO_MASIVO_DEB.P_GET_PTO_CLIENTE_ACTIVO(:Pn_IdPersonaRol, :Pv_Estado, :Pn_IdPunto); END;";
            
            $objStmt    = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdPersonaRol', $arrayParametros["intIdPerEmpRol"]);
            $objStmt->bindParam('Pv_Estado', $arrayParametros["strEstado"]);
            $objStmt->bindParam('Pn_IdPunto', $intIdPunto);
          
            $objStmt->execute();
                      
        }catch(\Exception $ex)
        {
            $intIdPunto = 0;
        }
        
        return $intIdPunto;
    }
    
	public function findPrimerPtoClientePadreActivoPorPersonaEmpresaRolId($personaEmpresaRolId)
	{
		$query = $this->_em->createQuery("
                SELECT a
                FROM
                schemaBundle:InfoPunto a,
                schemaBundle:InfoPuntoDatoAdicional e
                WHERE
                a.id = e.puntoId AND
				a.personaEmpresaRolId=$personaEmpresaRolId AND
				e.esPadreFacturacion='S' 
                order by a.id ASC")->setFirstResult(0)->setMaxResults(1);
                //echo $query->getSQL();die;
		$datos =  $query->getOneOrNullResult();
		return $datos;
	}
	
	public function findPuntosByClienteAndEstado($idCliente,$estado)
	{
		$query = $this->_em->createQuery(
				    'SELECT p
				     FROM schemaBundle:InfoPunto p,
				     schemaBundle:InfoPersonaEmpresaRol iper
				     WHERE iper.personaId = :idCliente 
					 AND p.personaEmpresaRolId=iper.id ')
// 					 AND lower(p.estado) = lower( :estado )')
			      ->setParameter('idCliente',$idCliente);
// 			      ->setParameter('estado',$estado);
		//echo $query->getSQL();
		//die();
		$total=count($query->getResult());
		//echo $total;
		//die();
		$datos =  $query->getResult();
		//print_r($datos);
		//die();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	}
	
	public function getEstados()
	{
	      $qb = $this->_em->createQueryBuilder('p')
		  ->select('DISTINCT p.estado')
		  ->from('schemaBundle:InfoPunto','p')
                  ->orderBy('p.estado','ASC');
	      
	      $estados = $qb->getQuery()->getResult();
	      return $estados;
	}
	
	 public function getSaldoByIdPunto($idPunto,$codEmpresa){
	    
	    $qb = $this->_em->createQueryBuilder();
	    
	    $qb->select('vista')

	      ->from('schemaBundle:VistaSaldoClientePunto','vista')

	      ->where('vista.empresaCod = ?1')
	      ->setParameter(1, $codEmpresa)
	      ->andWhere('vista.puntoId = ?2')
	      ->setParameter(2, $idPunto);
	      
	    $query = $qb->getQuery();
	    
	    $resultado = $query->getOneOrNullResult();
	    
	    return $resultado;
	}
	
/////Traer solo nombres clientes que tienen login login
        public function nombreClientesConLogins($idPunto,$codEmpresa,$nombre){
            $whereAdicional="";
            
            if(isset($nombre)){
                if($nombre!=''){
                    
                  $nombreTemp=strtolower($nombre);
                  
                    $whereAdicional=" and (lower(per.nombres) like '$nombreTemp%' or lower(per.apellidos) like '$nombreTemp%' or lower(per.razonSocial) like '$nombreTemp%' )" ;
                }
            }
            $query = $this->_em->createQuery(
				    "SELECT distinct per.id,per.nombres, per.apellidos,per.razonSocial,per.identificacionCliente
				     FROM schemaBundle:InfoPersona per
				     , schemaBundle:InfoPersonaEmpresaRol infoPer 
                                      , schemaBundle:InfoEmpresaRol emp 
                                     , schemaBundle:AdmiRol rol 
                                     , schemaBundle:InfoPunto pto 
				    where 
                                      per.id=infoPer.personaId 
                                    and   emp.id =infoPer.empresaRolId 
                                    and rol.id=emp.rolId   
                                    and pto.personaEmpresaRolId= infoPer.id 
                                    and  lower(rol.descripcionRol)='cliente' 
                                    and emp.empresaCod=$codEmpresa
				    and lower(pto.estado)='activo'
                                    and lower(per.estado)='activo'
                                     $whereAdicional
                                      ");
                    //->setFirstResult(0)->setMaxResults(7);
            
            //echo $query->getSQL(); die;
            $datos = $query->getResult();// traigo el redultado
              
             $total=count( $datos); //cuento el resultado
                //echo($total);
                //die();
                $resultado['registros']=$datos; //coloco los resultados en un array llamado registros
                $resultado['total']=$total; // coloco el total en un array llamado total
               
	   
                return $resultado;				
			      
        }
        
  ////////// fin taty:Busca los login de los clinetes con el respectivo nombre del cliente
  
  
    /*
     *
     * Método que devuelve el login segun su ID_Persona
     *
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.1 - 25/04/2019
     *
     * @since 1.0
     *
     * Costo 14
     *
     * @param Array $arrayParametros [
                                      'strRol',         => Rol de las personas a buscar
                                      'intIdPersona',   => Identificación de la persona
                                      'strLogin',       => Login de persona a buscar
                                      'intIdEmpresa',   => Identificación de la empresa
                                      'strEstadoPer',   => Estado de la persona
                                      'strEstadoPto'    => Estado del punto
                                     ]
     * @return Array $arrayResultado
     */
    public function loginClientes($arrayParametros)
    {
        $strSql   = ''; 
        $strWhere = '';
        $strOrder = '';
        
        $arrayResultado = array();
       
        try 
        { 
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
            $strSql         = "SELECT PTO.ID_PUNTO,
                                      PER.ID_PERSONA,
                                      PER.NOMBRES, 
                                      PER.APELLIDOS,
                                      PER.RAZON_SOCIAL,
                                      PER.IDENTIFICACION_CLIENTE,
                                      PTO.LOGIN
                                 FROM DB_COMERCIAL.INFO_PERSONA PER,
                                      DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFOPER, 
                                      DB_COMERCIAL.INFO_EMPRESA_ROL EMP,
                                      DB_GENERAL.ADMI_ROL ROL, 
                                      DB_COMERCIAL.INFO_PUNTO PTO "; 

            $strWhere        = " WHERE PER.ID_PERSONA = INFOPER.PERSONA_ID 
                                  AND EMP.ID_EMPRESA_ROL = INFOPER.EMPRESA_ROL_ID 
                                  AND ROL.ID_ROL=EMP.ROL_ID   
                                  AND PTO.PERSONA_EMPRESA_ROL_ID= INFOPER.ID_PERSONA_ROL 
                                  AND ROL.DESCRIPCION_ROL=:strRol 
                                  AND EMP.EMPRESA_COD = :intIdEmpresa
                                  AND PER.ID_PERSONA = :intIdPersona ";

            $strOrder       = " ORDER BY PER.ID_PERSONA ASC, 
                                         PTO.ID_PUNTO ASC";

            if(isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= " AND PTO.LOGIN LIKE :strLogin ";
                $objQuery->setParameter("strLogin",'%'.$arrayParametros["strLogin"].'%');
            }

            if(isset($arrayParametros['strEstadoPer']) && !empty($arrayParametros['strEstadoPer']))
            {
                $strWhere .= " AND PER.ESTADO=:strEstadoPer ";
                $objQuery->setParameter("strEstadoPer",$arrayParametros["strEstadoPer"]);
            }

            if(isset($arrayParametros['strEstadoPto']) && !empty($arrayParametros['strEstadoPto']))
            {
                $strWhere .= " AND PTO.ESTADO=:strEstadoPto "; 
                $objQuery->setParameter("strEstadoPto",$arrayParametros["strEstadoPto"]);
            }

            $strSql .= $strWhere . $strOrder;

            $objQuery->setParameter("strRol",      $arrayParametros["strRol"]);
            $objQuery->setParameter("intIdEmpresa",$arrayParametros["intIdEmpresa"]);
            $objQuery->setParameter("intIdPersona",$arrayParametros["intIdPersona"]);

            $objRsm->addScalarResult('LOGIN', 'login', 'string');
            $objRsm->addScalarResult('ID_PUNTO', 'id', 'integer');

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();

        }
        catch(\Exception $objException)
        {
            error_log('Error InfoPuntoRepository.loginClientes => '.$objException->getMessage());
            $arrayResultado['status']  = 'fail';
            $arrayResultado['message'] = 'Error al obtener los logins de clientes';
        }

        return $arrayResultado; 
    }	

        public function findIdsPtosPorIdsServicios($idsServicios, $limit, $page, $start)
        {
            $query = $this->_em->createQuery("
                SELECT distinct p.id
                FROM
                schemaBundle:InfoServicio s,
                schemaBundle:InfoPunto p
                WHERE
                s.puntoId = p.id
                and s.id in (:idsServicios) ");
            $query->setParameter('idsServicios', $idsServicios);
           
            $datos = $query->getResult('ScalarValueHydrator');// traigo el redultado

            return $datos;
        }
            
        
    /**
     * getUltimaMillaPorPunto
     *
     * Metodo que devuelve la ultima milla según el punto cliente
     * @author John Vera            <javera@telconet.ec>
     * @param string $punto
     * @return string
     */
 public function getUltimaMillaPorPunto($punto)
    {
        $ultimaMilla = '';   
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = "select TECNK_SERVICIOS.FNC_GET_MEDIO_POR_PUNTO(:punto,:producto) as ULTIMA_MILLA from dual";

        $rsm->addScalarResult('ULTIMA_MILLA', 'ultimaMilla', 'string');

        $query->setParameter('punto', $punto);
        $query->setParameter('producto', 'INTERNET');

        $query->setSQL($sql);

        $datos = $query->getScalarResult();
        if ($datos)
        {
            $ultimaMilla = $datos[0]['ultimaMilla']; 
        }
        
        return $ultimaMilla;
        
    }
      
    /**
     * getRolClientePorPunto
     *
     * Metodo encargado de obtener el rol y la razon social dado un punto cliente
     *     
     * @param integer $intPuntoId        
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 14-11-2014 - Version Inicial
     */    
    public function getRolClientePorPunto($intPuntoId)
    {
        $query = $this->_em->createQuery("
                SELECT 
                a.id,
                e.razonSocial,
                d.descripcionRol
                FROM
                schemaBundle:InfoPunto a,
                schemaBundle:InfoPersonaEmpresaRol b,
                schemaBundle:InfoEmpresaRol c,
                schemaBundle:AdmiRol d,
                schemaBundle:InfoPersona e
                WHERE
                a.personaEmpresaRolId   =   b.id and
                b.empresaRolId          =   c.id and
                b.personaId             =   e.id and
                c.rolId                 =   d.id and
                a.id                    =   :punto");
                
        $query->setParameter('punto', $intPuntoId);

        $datos = $query->getResult();

        return $datos;

    }
    
    
     /**
     * getServiciosByElementoAndInterface
     *
     * Metodo que devuelve el id de los servicios segun el elemento y las interfaces.
     *
     * @author John Vera            <javera@telconet.ec>
     * @param string $elemento
     * @param string $interfaceElemento
     * @return string
     */
 public function getServiciosByElementoAndInterface($elemento, $interfaceElemento)
    {
        
        $servicios = '';
        
        if ($elemento || $interfaceElemento)
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $sql = "select TECNK_SERVICIOS.FNC_GET_SERV_ELE_PTO(:elemento,:interfaceElemento) as SERVICIOS from dual";

            $rsm->addScalarResult('SERVICIOS', 'servicios', 'string');

            $query->setParameter('elemento', $elemento);
            $query->setParameter('interfaceElemento', $interfaceElemento);

            $query->setSQL($sql);

            $datos = $query->getScalarResult();

            if ($datos)
            {
                $servicios = $datos[0]['servicios']; 
            }
        }
        return $servicios;
        
    }
    
     /**
     * getJsonCambioRazonSocialPorLogin
     *
     * Metodo JSON obtiene los puntos clientes que pueden ser cambiados de razon social
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 07-08-2015
     *
     * @param string $strLogin
     * @param string $strNombrePunto
     * @param string $strDireccion
     * @param integer $start
     * @param integer $limit
     * @return JSON
     */
    public function getJsonCambioRazonSocialPorLogin($idper, $strLogin, $strNombrePunto, $strDireccion, $start, $limit)
    {
        $arrayPuntosEncontrados = array();        
        $arrayResultado         = $this->getPuntosCambioRazonSocialPorLogin($idper, $strLogin, $strNombrePunto, $strDireccion, $start, $limit);
        $objPuntos              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        if($objPuntos)
        {            
            foreach($objPuntos as $objPuntos)
            {                
                $arrayPuntosEncontrados[] = array('idPto' => $objPuntos['id'],
                                         'login'          => trim($objPuntos['login']),
                                         'nombrePunto'    => trim($objPuntos['nombrePunto']),
                                         'direccionPunto' => trim($objPuntos['direccion']));                
            }

            if($intTotal == 0)
            {
                $arrayResultadoJson = array('total'       => 1,
                                            'encontrados' => array('idPto' => 0, 'login' => 'Ninguno',
                                            'nombrePunto' => 'Ninguno', 'direccionPunto' => 'Ninguno'));
                $arrayResultadoJson = json_encode($arrayResultadoJson);               
            }
            else
            {
                $arrayPuntosJson    = json_encode($arrayPuntosEncontrados);
                $arrayResultadoJson = '{"total":"' . $intTotal . '","encontrados":' . $arrayPuntosJson . '}';                
            }
        }
        else
        {
            $arrayResultadoJson = '{"total":"0","encontrados":[]}';            
        }
        return $arrayResultadoJson;
    }
    
    /**
     * getPuntosCambioRazonSocialPorLogin
     *
     * Metodo para obtener los puntos clientes Login que pueden ser cambiados de razon social
     * Consideracion: 
     * 1)No se permite Cambio de Razon Social Por Login, si el Login es Punto Padre 
     * de Facturacion de otros Logines.
     * 2)No se permite Cambio de Razon Social Por Login si el Login no posee servicio en estado Activo
     * 3)Se Cargara el Grid solo con los Logines que cumplen las condiciones. 
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 07-08-2015
     *
     * @param integer $idper
     * @param string  $strLogin
     * @param string  $strNombrePunto
     * @param string  $strDireccion
     * @param integer $start
     * @param integer $limit
     * @return array
     */
    public function getPuntosCambioRazonSocialPorLogin($idper, $strLogin, $strNombrePunto, $strDireccion, $start, $limit)
    {             
        $strSqlDatos      = 'SELECT pto.id,pto.login,pto.nombrePunto,pto.direccion ';
        $strSqlCantidad   = 'SELECT count(pto) ';
        $strSqlFrom       = 'FROM schemaBundle:InfoPunto pto                
                INNER JOIN schemaBundle:InfoPuntoDatoAdicional pda WITH pto.id = pda.puntoId
                INNER JOIN schemaBundle:InfoPersonaEmpresaRol pers WITH pto.personaEmpresaRolId = pers.id
                INNER JOIN schemaBundle:InfoServicio serv WITH pto.id = serv.puntoId
                WHERE pto.personaEmpresaRolId = (?5)
                AND serv.estado = (?4) '
                .($strLogin ? ' AND (LOWER(pto.login) like LOWER(?1)) ' : '')
                .($strNombrePunto ? ' AND (LOWER(pto.nombrePunto) like LOWER(?2)) ': '')
                .($strDireccion ? ' AND (LOWER(pto.direccion) like LOWER(?3)) ': '')            
                . ' AND (
                          (
                           pda.esPadreFacturacion= (?6) 
                           AND NOT EXISTS (
                           SELECT 1
                           FROM schemaBundle:InfoServicio servicio
                           WHERE pto.id = servicio.puntoFacturacionId
                           AND servicio.puntoId!=servicio.puntoFacturacionId
                           AND servicio.estado in (?7))
                           ) OR pda.esPadreFacturacion= (?8)
                        ) ';
        
        $strSqlGroupBy   = " GROUP BY pto.id,pto.login,pto.nombrePunto,pto.direccion";
        
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        if($strLogin != "")
        { 
            $strQueryDatos->setParameter(1, '%' . $strLogin . '%');  
        }
        if($strNombrePunto != "")
        { 
            $strQueryDatos->setParameter(2, '%' . $strNombrePunto . '%');
        }
        if($strDireccion != "")
        { 
            $strQueryDatos->setParameter(3, '%' . $strDireccion . '%');
        }
        $strQueryDatos->setParameter(4, 'Activo');
        $strQueryDatos->setParameter(5, $idper);
        $strQueryDatos->setParameter(6, 'S');
        $strQueryDatos->setParameter(7, array('Activo','In-Corte'));
        $strQueryDatos->setParameter(8, 'N');
        
        $strSqlDatos    .= $strSqlFrom;
        $strSqlDatos    .= $strSqlGroupBy;                     
        
        $strQueryDatos->setDQL($strSqlDatos);                
        $objDatos        = $strQueryDatos->setFirstResult($start)->setMaxResults($limit)->getResult();                 
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        if($strLogin != "")
        { 
            $strQueryCantidad->setParameter(1, '%' . $strLogin . '%');  
        }
        if($strNombrePunto != "")
        { 
            $strQueryCantidad->setParameter(2, '%' . $strNombrePunto . '%');
        }
        if($strDireccion != "")
        { 
            $strQueryCantidad->setParameter(3, '%' . $strDireccion . '%');
        }
        $strQueryCantidad->setParameter(4, 'Activo');
        $strQueryCantidad->setParameter(5, $idper);
        $strQueryCantidad->setParameter(6, 'S');
        $strQueryCantidad->setParameter(7, array('Activo','In-Corte'));
        $strQueryCantidad->setParameter(8, 'N');
        
        $strSqlCantidad .= $strSqlFrom;   
        $strQueryCantidad->setDQL($strSqlCantidad);        
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultadoPuntos['registros'] = $objDatos;
        $arrayResultadoPuntos['total']     = $intTotal;
        
        return $arrayResultadoPuntos;
    }
    /**
     * getTotalPuntosConServicioActivoCortado
     *
     * Metodo para obtener total de Puntos (Logines) que poseen servicio Activo o In-Corte por Persona Empresa Rol
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 21-09-2015
     *
     * @param integer $idper
     * @return integer
     */   
    public function getTotalPuntosConServicioActivoCortado($idper)
    {         
        $query = $this->_em->createQuery('SELECT count(pto) 
                FROM schemaBundle:InfoPunto pto                                
                INNER JOIN schemaBundle:InfoPersonaEmpresaRol pers WITH pto.personaEmpresaRolId = pers.id                
                WHERE pto.personaEmpresaRolId = :intIdPersonaRol'                
                . ' AND EXISTS (
                        SELECT 1
                        FROM schemaBundle:InfoServicio servicio
                        WHERE pto.id = servicio.puntoId
                        AND servicio.estado in (:strEstadoServicio))
                ');        
                
        $query->setParameter('intIdPersonaRol', $idper);        
        $query->setParameter('strEstadoServicio',  array("Activo", "In-Corte"));
       
        $intTotalPuntos = $query->getSingleScalarResult();
       if(!$intTotalPuntos)
        {
            $intTotalPuntos = 0;
        }

        return $intTotalPuntos;        
    }
    
    
    /**
     * 
     * Metodo que obtiene el Json de los clientes segun el filtro para calcular el SLA
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 12-12-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 08-09-2016 Se retorna el numero de registro desde el cual debe de tomarse en cuenta para el calculo del SLA
     *
     * @param type $arrayParametros
     * @return string
     */
    public function getJsonPuntosCalculoSla($arrayParametros)
    {
        $arrayResultado = $this->getPuntosCalculoSla($arrayParametros);
        $total     = $arrayResultado['total'];
        $resultado = $arrayResultado['resultado'];                
        
        if($resultado)
        {
            foreach($resultado as $data)
            {
                $arrayEncontrados[] = array( "idPunto"      => $data['idPunto'],
                                             "idServicio"   => $data['idServicio'],
                                             "login"        => $data['login'],
                                             "nombres"      => $data['nombres'],
                                             "nombreOficina"=> $data['nombreOficina'],
                                             "estado"       => $data['estado'],
                                             "calculoIni"   => $arrayParametros['start']
                );
            }

            $arrayRespuesta = array('total'=> $total , 'encontrados' => $arrayEncontrados);                                            
        }
        else
        {
            $arrayRespuesta = array('total'=> 0 , 'encontrados' => []);
        }
        
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * 
     * Metodo que obtiene el resultado de la consulta de los clientes segun el filtro para calcular el SLA
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 12-12-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 07-09-2016 Se realizan ajustes en el query, se quita la referencia a la tabla INFO_EMPRESA_GRUPO y
     *                         se retorna el id_servicio como null, cuando no se busca por producto. Esto se realiza para
     *                         evitar PHP Notice:  Undefined index: idServicio.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 20-01-2021 - Se agrega el filtro por identificación del cliente.
     *
     * @param type $arrayParametros
     * @return type
     */        
    public function getPuntosCalculoSla($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $select = "";
            $from   = "";
            $where  = "";
            $strWhere = "";
            $strAnd   = "";
            $strSql1  = "";
            $strSql2  = "";
            $strSql3  = "";
            $strSql4  = "";
            $strSql5  = "";
            $strUnion = "";
            

            if($arrayParametros['producto']!="" && $arrayParametros['producto']!="null")
            {
                $select .= "SERVICIO.ID_SERVICIO,";
                $from   .= "INFO_SERVICIO SERVICIO,";
                $where  .= " AND SERVICIO.PUNTO_ID     = PUNTO.ID_PUNTO 
                             AND SERVICIO.ID_SERVICIO IN ((SELECT 
                                ISR.id_SERVICIO
                              FROM INFO_SERVICIO ISR,
                                INFO_PLAN_CAB IPC,
                                INFO_PLAN_DET IPD                            
                              WHERE ISR.PLAN_ID   = IPC.ID_PLAN
                              AND IPC.ID_PLAN     = IPD.PLAN_ID
                              AND IPD.PRODUCTO_ID = :producto
                              AND ISR.PUNTO_ID    = PUNTO.ID_PUNTO                          
                              UNION
                              SELECT 
                                ISR.id_SERVICIO
                              FROM INFO_SERVICIO ISR                            
                              WHERE ISR.PRODUCTO_ID = :producto
                              AND ISR.PUNTO_ID      = PUNTO.ID_PUNTO                           
                              )) ";

                $query->setParameter('producto', $arrayParametros['producto']);  
                $rsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');
            }
            else
            {
                $select .= "null ID_SERVICIO,";
                $rsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');              
            }

            if($arrayParametros['oficina']!="" && $arrayParametros['oficina']!="null")
            {
                $where .= " AND OFICINA.ID_OFICINA = :oficina ";
                $query->setParameter('oficina',   $arrayParametros['oficina']);
            }

            if (isset($arrayParametros['identificacion']) && $arrayParametros['identificacion'] != "")
            {
                $where .= " AND PERSONA.IDENTIFICACION_CLIENTE = :identificacion ";
                $query->setParameter('identificacion', $arrayParametros['identificacion']);
            }

            if($arrayParametros['razonSocial']!="")
            {
                if($arrayParametros['nombres']=="" && $arrayParametros['apellidos']=="")
                {
                    $where .= " AND UPPER(PERSONA.RAZON_SOCIAL)       LIKE  UPPER(:razonSocial) ";
                    $query->setParameter('razonSocial',  '%'. $arrayParametros['razonSocial'] .'%');            
                }
                else if($arrayParametros['nombres']!="" && $arrayParametros['apellidos']=="")
                {
                    $where .= " AND UPPER(PERSONA.RAZON_SOCIAL) LIKE UPPER(:razonSocial) AND UPPER(PERSONA.NOMBRES) LIKE UPPER(:nombres) ";
                    $query->setParameter('razonSocial', '%'.$arrayParametros['razonSocial'].'%');            
                    $query->setParameter('nombres', '%'.$arrayParametros['nombres'].'%');
                }
                else if($arrayParametros['nombres']=="" && $arrayParametros['apellidos']!="")
                {
                    $where .= " AND UPPER(PERSONA.RAZON_SOCIAL) LIKE UPPER(:razonSocial) AND UPPER(PERSONA.APELLIDOS) LIKE UPPER(:apellidos) ";
                    $query->setParameter('razonSocial', '%'.$arrayParametros['razonSocial'].'%');            
                    $query->setParameter('apellidos', '%'.$arrayParametros['apellidos'].'%');
                }
                else if($arrayParametros['nombres']!="" && $arrayParametros['apellidos']!="")
                {
                    $where .= " AND UPPER(PERSONA.RAZON_SOCIAL) LIKE UPPER(:razonSocial) "
                             . "AND UPPER(PERSONA.NOMBRES) LIKE UPPER(:nombres) "
                             . "AND UPPER(PERSONA.APELLIDOS) LIKE UPPER(:apellidos) ";
                    $query->setParameter('razonSocial', '%'.$arrayParametros['razonSocial'].'%');            
                    $query->setParameter('nombres', '%'.$arrayParametros['nombres'].'%');
                    $query->setParameter('apellidos', '%'.$arrayParametros['apellidos'].'%');
                }
            }
            else
            {
                if($arrayParametros['nombres']!="" && $arrayParametros['apellidos']=="")
                {
                    $where .= " AND UPPER(PERSONA.NOMBRES) LIKE UPPER(:nombres) ";                        
                    $query->setParameter('nombres', '%'.$arrayParametros['nombres'].'%');
                }
                else if($arrayParametros['nombres']=="" && $arrayParametros['apellidos']!="")
                {
                    $where .= " AND UPPER(PERSONA.APELLIDOS) LIKE UPPER(:apellidos) ";                          
                    $query->setParameter('apellidos', '%'.$arrayParametros['apellidos'].'%');
                }
                else if($arrayParametros['nombres']!="" && $arrayParametros['apellidos']!="")
                {
                    $where .= " AND UPPER(PERSONA.APELLIDOS) LIKE UPPER(:apellidos) AND UPPER(PERSONA.NOMBRES) LIKE UPPER(:nombres) ";
                    $query->setParameter('nombres', '%'.$arrayParametros['nombres'].'%');            
                    $query->setParameter('apellidos', '%'.$arrayParametros['apellidos'].'%');
                }
            }       

            if($arrayParametros['estado'] != 'Todos')
            {            
                $query->setParameter('estado', $arrayParametros['estado']);
            }
            else
            {
                $arrayEstado = array('Cancelado','In-Corte');
                $query->setParameter('estado', array_values($arrayEstado));
                
            }
            
            if($arrayParametros['estado'] == 'Activo')
            {
                if($from == "")
                {
                    $from .= "INFO_SERVICIO SERVICIO, ";
                }

                $strAnd .= "AND SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO  ";
                $strWhere .= "AND SERVICIO.ESTADO  IN ('Activo')  ";
            }
            

            $strSelectCont = "SELECT COUNT(DISTINCT PUNTO.ID_PUNTO) CONT ";

            $strSelectData = "SELECT 
                                DISTINCT(PUNTO.ID_PUNTO),
                                $select
                                PUNTO.LOGIN,
                                NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES
                                ||' '
                                ||PERSONA.APELLIDOS) NOMBRES,
                                OFICINA.NOMBRE_OFICINA,
                                PUNTO.ESTADO";

            $strSql = "
                  FROM INFO_PERSONA PERSONA,
                    INFO_PUNTO PUNTO,
                    $from
                    INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                    INFO_EMPRESA_ROL EMPRESA_ROL,
                    INFO_OFICINA_GRUPO OFICINA,
                    ADMI_ROL ROL,
                    ADMI_TIPO_ROL TIPO_ROL
                  WHERE PERSONA.ID_PERSONA       = PERSONA_ROL.PERSONA_ID              
                  AND EMPRESA_ROL.EMPRESA_COD    = :empresa
                  AND PERSONA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                  AND EMPRESA_ROL.ID_EMPRESA_ROL = PERSONA_ROL.EMPRESA_ROL_ID
                  AND PERSONA_ROL.OFICINA_ID     = OFICINA.ID_OFICINA
                  AND EMPRESA_ROL.ROL_ID         = ROL.ID_ROL
                  AND ROL.TIPO_ROL_ID            = TIPO_ROL.ID_TIPO_ROL
                  $strAnd
                  AND TIPO_ROL.DESCRIPCION_TIPO_ROL = :cliente
                  AND PUNTO.ESTADO               IN (:estado)
                  $strWhere
                  $where              

                  ";
            
            
            if(($arrayParametros['producto']=="" || $arrayParametros['producto']=="null" || empty($arrayParametros['producto'])) 
                    && $arrayParametros['estado'] == 'Todos')
            {
                
                $strSql2 = " select sum(tabla.cont)CONT from ( ";
                
                $strSql5 = " select tabla.ID_PUNTO,tabla.ID_SERVICIO,tabla.LOGIN,tabla.NOMBRES,tabla.NOMBRE_OFICINA,tabla.ESTADO from ( ";
                
                $strUnion = " union ";
                
            
                $strSql1 = "
                  FROM INFO_PERSONA PERSONA,
                    INFO_PUNTO PUNTO,
                    INFO_SERVICIO SERVICIO,
                    INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                    INFO_EMPRESA_ROL EMPRESA_ROL,
                    INFO_OFICINA_GRUPO OFICINA,
                    ADMI_ROL ROL,
                    ADMI_TIPO_ROL TIPO_ROL
                  WHERE PERSONA.ID_PERSONA       = PERSONA_ROL.PERSONA_ID              
                  AND EMPRESA_ROL.EMPRESA_COD    = :empresa
                  AND PERSONA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                  AND EMPRESA_ROL.ID_EMPRESA_ROL = PERSONA_ROL.EMPRESA_ROL_ID
                  AND PERSONA_ROL.OFICINA_ID     = OFICINA.ID_OFICINA
                  AND EMPRESA_ROL.ROL_ID         = ROL.ID_ROL
                  AND ROL.TIPO_ROL_ID            = TIPO_ROL.ID_TIPO_ROL
                  AND SERVICIO.PUNTO_ID          = PUNTO.ID_PUNTO
                  AND TIPO_ROL.DESCRIPCION_TIPO_ROL = :cliente
                  AND PUNTO.ESTADO               IN ('Activo')
                  AND SERVICIO.ESTADO            IN ('Activo')
                  $where              

                  ";
                
                $strSql1 .= " ) tabla  "; 
                
                
                $strSql3 .= $strSql2.$strSelectCont.$strSql.$strUnion.$strSelectCont.$strSql1;
                
                $strSql4 .= $strSql5.$strSelectData.$strSql.$strUnion.$strSelectData.$strSql1;
                
            } 
            

            $rsm->addScalarResult('ID_PUNTO', 'idPunto', 'integer');
            $rsm->addScalarResult('LOGIN', 'login', 'string');
            $rsm->addScalarResult('NOMBRES', 'nombres', 'string');        
            $rsm->addScalarResult('NOMBRE_OFICINA', 'nombreOficina', 'string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string'); 
            $rsm->addScalarResult('CONT','cont','integer');

            $query->setParameter('cliente',  "Cliente");        
            $query->setParameter('empresa',   $arrayParametros['empresa']);

            
            if($strSql3 == "" && empty($strSql3))
            {
                $query->setSQL($strSelectCont.$strSql);	
            }
            else
            {
                $query->setSQL($strSql3);
            }
                          
            $arrayResultado['total'] = $query->getSingleScalarResult();        

            if($strSql3 == "" && empty($strSql3))
            {
                $query->setSQL($strSelectData.$strSql);
            }
            else
            {
                $query->setSQL($strSql4);
            }                        	   
            
            $objQuery = $this->setQueryLimit($query, $arrayParametros['limit'], $arrayParametros['start']);                        

            $arrayResultado['resultado'] = $objQuery->getArrayResult();
                          
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
    
    /**
     * getVentasByCriterios
     *
     * Método que retorna información de las ventas obtenidas dependiendo de los criterios ingresados por el usuario                                    
     *      
     * @param array $arrayParametros  [ 'numeroVentas', 'detalleVentas', 'puntoCliente', 'idPlan', 'nombreCliente', 'apellidoCliente', 'empresa',
     *                                   'jurisdiccion', 'sector', 'identificacionCliente', 'nombreVendedor', 'apellidoVendedor', 'usuarioVendedor', 
     *                                   'usrCreacion', 'feAprobacionInicio', 'feAprobacionFinal', 'feCreacionPuntoInicio', 'feCreacionPuntoFinal', 
     *                                   'feActivacionInicio', 'feActivacionFinal', 'estadosServiciosNoIncluidos', 'estadosServiciosIncluidos', 
     *                                   'tipoVenta', 'inicio', 'limite' ]
     * 
     * @return array $arrayResultados [ 'resultados', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-09-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-11-2015 - Se cambia la opción para retornar las ventas que tengan estado 'Rechazadas' o 'Canceladas'
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 08-02-2016 - Se corrige que cuando se busca por nombre del cliente y si es una persona jurídica busque además por razón social.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 20-05-2016 - Se modifica para que retorne solo las ventas que tengan en los productos nombre técnico 'INTERNET'.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 07-12-2018 - Se modifica el proceso de la funcion a query nativo para mejoras del tiempo en los reportes
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 08-05-2023 - Se modifica query para obtener el distinct de la caracteristica por punto PUNTO_DE_VENTA_CANAL
     *  
     */
    public function getVentasByCriterios($arrayParametros)
    {
        $objRsmBuilder = new ResultSetMappingBuilder($this->_em);
        $objRsmCount   = new ResultSetMappingBuilder($this->_em);
        
        $objQuery      = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $boolDetalleVentas = false;
        $strSelect         = '';
        $strOrderBy        = '';
        $strSelectCount    = 'SELECT COUNT(DISTINCT ins.ID_SERVICIO) AS TOTAL ';
        
        if( isset($arrayParametros['numeroVentas']) )
        {
            if( $arrayParametros['numeroVentas'] )
            {
                $strSelect = 'SELECT DISTINCT ins.ID_SERVICIO AS TOTALSERVICIO ';
                $objRsmBuilder->addScalarResult('TOTALSERVICIO', 'id', 'integer');
            }
        }
        
        $strFrom  = 'FROM DB_COMERCIAL.INFO_PUNTO ip,
                          DB_COMERCIAL.INFO_SERVICIO ins,
                          DB_COMERCIAL.INFO_CONTRATO ic,
                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,
                          DB_COMERCIAL.INFO_EMPRESA_ROL ier,
                          DB_GENERAL.ADMI_ROL ar,
                          DB_GENERAL.ADMI_TIPO_ROL atr '; 
        
        $strWhere = "WHERE ip.ID_PUNTO = ins.PUNTO_ID
                       AND ic.PERSONA_EMPRESA_ROL_ID = ip.PERSONA_EMPRESA_ROL_ID
                       AND iper.ID_PERSONA_ROL = ip.PERSONA_EMPRESA_ROL_ID
                       AND ier.ID_EMPRESA_ROL = iper.EMPRESA_ROL_ID 
                       AND iper.EMPRESA_ROL_ID = ier.ID_EMPRESA_ROL
                       AND ar.ID_ROL = ier.ROL_ID
                       AND ar.TIPO_ROL_ID = atr.ID_TIPO_ROL
                       AND atr.DESCRIPCION_TIPO_ROL = 'Cliente'
                       AND ins.ES_VENTA = 'S'
                       AND (
                              ins.PLAN_ID IN (
                                                SELECT ipc.id_plan
                                                FROM DB_COMERCIAL.INFO_PLAN_DET ipd,
                                                     DB_COMERCIAL.INFO_PLAN_CAB ipc,
                                                     DB_COMERCIAL.ADMI_PRODUCTO ap
                                                WHERE ipc.id_plan = ipd.plan_id
                                                  AND ipc.id_plan = ins.plan_id
                                                  AND ap.id_producto = ipd.producto_id
                                                  AND ap.nombre_tecnico = 'INTERNET'
                                            )
                                OR ins.PRODUCTO_ID IN (
                                                         SELECT ap2.ID_PRODUCTO
                                                         FROM DB_COMERCIAL.ADMI_PRODUCTO ap2
                                                         WHERE ap2.ID_PRODUCTO = ins.PRODUCTO_ID
                                                           AND ap2.nombre_tecnico = 'INTERNET'
                                                     )
                            ) ";        
        if( isset($arrayParametros['detalleVentas']) )
        {
            if( $arrayParametros['detalleVentas'] )
            {
                $boolDetalleVentas = true;
                
                $strSelect = "SELECT DISTINCT IP.LOGIN                         AS LOGINPUNTO,
                                INS.ESTADO                                     AS ESTADOSERVICIO,
                                CONCAT(IPS.NOMBRES, CONCAT(' ',IPS.APELLIDOS)) AS NOMBRECLIENTE,
                                IPS.DIRECCION,
                                IPS.RAZON_SOCIAL,
                                AJ.NOMBRE_JURISDICCION,
                                ASEC.NOMBRE_SECTOR,
                                IEG.NOMBRE_EMPRESA,
                                IP.USR_VENDEDOR     AS LOGINVENDEDOR,
                                IP.FE_CREACION      AS FECHACREACIONPUNTO,
                                IPER.ID_PERSONA_ROL AS IDPERSONAEMPRESAROL,
                                INS.PRECIO_VENTA,
                                CONCAT(IP.LATITUD,CONCAT(', ',IP.LONGITUD)) AS COORDENADASPUNTO,
                                INS.ID_SERVICIO                             AS IDSERVICIO,
                                IPS.IDENTIFICACION_CLIENTE ";
                
                $objRsmBuilder->addScalarResult('LOGINPUNTO',            'loginPunto',            'string');
                $objRsmBuilder->addScalarResult('ESTADOSERVICIO',        'estadoServicio',        'string');
                $objRsmBuilder->addScalarResult('NOMBRECLIENTE',         'nombreCliente',         'string');
                $objRsmBuilder->addScalarResult('DIRECCION',             'direccion',             'string');
                $objRsmBuilder->addScalarResult('RAZON_SOCIAL',          'razonSocial',          'string');
                $objRsmBuilder->addScalarResult('NOMBRE_JURISDICCION',   'nombreJurisdiccion',   'string');
                $objRsmBuilder->addScalarResult('NOMBRE_SECTOR',         'nombreSector',         'string');
                $objRsmBuilder->addScalarResult('NOMBRE_EMPRESA',        'nombreEmpresa',        'string');
                $objRsmBuilder->addScalarResult('LOGINVENDEDOR',         'loginVendedor',         'string');
                $objRsmBuilder->addScalarResult('FECHACREACIONPUNTO',    'fechaCreacionPunto',    'datetime');
                $objRsmBuilder->addScalarResult('IDPERSONAEMPRESAROL',   'idPersonaEmpresaRol',   'integer');
                $objRsmBuilder->addScalarResult('PRECIO_VENTA',          'precioVenta',          'float');
                $objRsmBuilder->addScalarResult('COORDENADASPUNTO',      'coordenadasPunto',      'string');
                $objRsmBuilder->addScalarResult('IDSERVICIO',            'idServicio',            'integer');
                $objRsmBuilder->addScalarResult('IDENTIFICACION_CLIENTE','identificacionCliente','string');
                
                $strFrom   .= ', DB_COMERCIAL.INFO_PERSONA ips,
                               DB_INFRAESTRUCTURA.ADMI_JURISDICCION aj,
                               DB_GENERAL.ADMI_SECTOR asec,
                               DB_COMERCIAL.INFO_EMPRESA_GRUPO ieg ';
                
                $strWhere  .= 'AND ips.ID_PERSONA = iper.PERSONA_ID
                               AND aj.ID_JURISDICCION = ip.PUNTO_COBERTURA_ID
                               AND asec.ID_SECTOR = ip.SECTOR_ID
                               AND ieg.COD_EMPRESA = ier.EMPRESA_COD ';
                
                $strOrderBy .= 'ORDER BY ip.login ';                                
            }
        }
        
        if( isset($arrayParametros['banderaReporte']) && !empty($arrayParametros['banderaReporte']) )
        {
            $strSelect.=" ,CONCAT(Initcap(IP_PTO.nombres), CONCAT(' ', Initcap(IP_PTO.apellidos))) as STRVENDEDOR 
                          ,(
                            SELECT DISTINCT(CONCAT(APD.VALOR2, CONCAT('|',APD.VALOR4)))

                            FROM INFO_PUNTO_CARACTERISTICA IPUC
                            LEFT JOIN ADMI_CARACTERISTICA ac
                            ON AC.ID_CARACTERISTICA          =IPUC.CARACTERISTICA_ID
                            AND AC.DESCRIPCION_CARACTERISTICA='PUNTO_DE_VENTA_CANAL'
                            AND AC.ESTADO                    ='Activo'
                            LEFT JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD
                            ON APD.VALOR1 = DBMS_LOB.SUBSTR(IPUC.VALOR, 4000,1)
                            JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC
                            ON APC.ID_PARAMETRO     =APD.PARAMETRO_ID
                            WHERE APD.ESTADO        ='Activo'
                            AND APD.VALOR1         IS NOT NULL
                            AND APC.NOMBRE_PARAMETRO='CANALES_PUNTO_VENTA'
                            AND APC.MODULO          ='COMERCIAL'
                            AND APC.PROCESO         ='CANALES_PUNTO_VENTA'
                            AND APC.ESTADO          ='Activo'
                            AND IP.ID_PUNTO         =IPUC.PUNTO_ID
                            AND IPUC.ESTADO         ='Activo'
                            ) AS STRPUNTOVENTA " ;

            $strFrom  .=" ,DB_COMERCIAL.INFO_PERSONA IP_PTO ";
            $strWhere .=" AND IP_PTO.LOGIN=IP.USR_VENDEDOR ";
            $objRsmBuilder->addScalarResult('STRVENDEDOR', 'strVendedor', 'string');
            $objRsmBuilder->addScalarResult('STRPUNTOVENTA', 'strPuntoVenta', 'string');
        }
        
        if( isset($arrayParametros['puntoCliente']) )
        {
            if( $arrayParametros['puntoCliente'] )
            {
                $strWhere .= 'AND ip.ID_PUNTO = :puntoCliente ';
                
                $objQuery->setParameter('puntoCliente', $arrayParametros['puntoCliente']);
                
                $objQueryCount->setParameter('puntoCliente', $arrayParametros['puntoCliente']);
            }
        }
        
                
        if( isset($arrayParametros['idPlan']) )
        {
            if( $arrayParametros['idPlan'] )
            {
                $strWhere .= "AND ins.plan_id = :idPlan ";

                $objQuery->setParameter('idPlan', $arrayParametros['idPlan']);
                
                $objQueryCount->setParameter('idPlan', $arrayParametros['idPlan']);
            }
        }
        
                
        if( isset($arrayParametros['nombreCliente']) )
        {
            if( $arrayParametros['nombreCliente'] )
            {
                $strWhere .= "AND ( ips.NOMBRES LIKE :nombreCliente OR ips.RAZON_SOCIAL LIKE :nombreCliente ) ";

                $objQuery->setParameter('nombreCliente', '%'.trim(strtoupper($arrayParametros['nombreCliente'])).'%');

                $objQueryCount->setParameter('nombreCliente', '%'.trim(strtoupper($arrayParametros['nombreCliente'])).'%');
            }
        }
        
                
        if( isset($arrayParametros['apellidoCliente']) )
        {
            if( $arrayParametros['apellidoCliente'] )
            {
                $strWhere .= "AND ips.APELLIDOS LIKE :apellidoCliente ";

                $objQuery->setParameter('apellidoCliente', '%'.trim(strtoupper($arrayParametros['apellidoCliente'])).'%');

                $objQueryCount->setParameter('apellidoCliente', '%'.trim(strtoupper($arrayParametros['apellidoCliente'])).'%');
            }
        }
        
                
        if( isset($arrayParametros['empresa']) )
        {
            if( $arrayParametros['empresa'] )
            {
                if( !$boolDetalleVentas )
                {
                    $strFrom  .= ", DB_COMERCIAL.INFO_EMPRESA_GRUPO ieg ";
                    $strWhere .= "AND ieg.COD_EMPRESA = ier.EMPRESA_COD ";
                }
                
                $strWhere .= "AND ieg.COD_EMPRESA = :empresa ";

                $objQuery->setParameter('empresa', $arrayParametros['empresa']);

                $objQueryCount->setParameter('empresa', $arrayParametros['empresa']);
            }
        }
        
                
        if( isset($arrayParametros['jurisdiccion']) )
        {
            if( $arrayParametros['jurisdiccion'] )
            {
                $strWhere .= "AND aj.ID_JURISDICCION = :jurisdiccion ";

                $objQuery->setParameter('jurisdiccion', $arrayParametros['jurisdiccion']);

                $objQueryCount->setParameter('jurisdiccion', $arrayParametros['jurisdiccion']);
            }
        }
        
                
        if( isset($arrayParametros['sector']) )
        {
            if( $arrayParametros['sector'] )
            {
                $strWhere .= "AND asec.ID_SECTOR = :sector ";

                $objQuery->setParameter('sector', $arrayParametros['sector']);

                $objQueryCount->setParameter('sector', $arrayParametros['sector']);
            }
        }
        
                
        if( isset($arrayParametros['identificacionCliente']) )
        {
            if( $arrayParametros['identificacionCliente'] )
            {
                $strWhere .= "AND ips.IDENTIFICACION_CLIENTE = :identificacionCliente ";

                $objQuery->setParameter('identificacionCliente', $arrayParametros['identificacionCliente']);

                $objQueryCount->setParameter('identificacionCliente', $arrayParametros['identificacionCliente']);
            }
        }
        
                
        if( isset($arrayParametros['nombreVendedor']) )
        {
            if( $arrayParametros['nombreVendedor'] )
            {
                $strWhere .= "AND ip.USR_VENDEDOR IN (
                                                        SELECT ip2.USR_VENDEDOR
                                                        FROM DB_COMERCIAL.INFO_PUNTO ip2,
                                                             DB_COMERCIAL.INFO_PERSONA ips2
                                                        WHERE ip2.USR_VENDEDOR = ips2.LOGIN
                                                          AND ips2.NOMBRES LIKE :nombreVendedor
                                                    ) ";

                $objQuery->setParameter('nombreVendedor', '%'.trim(strtoupper($arrayParametros['nombreVendedor'])).'%');
                
                $objQueryCount->setParameter('nombreVendedor', '%'.trim(strtoupper($arrayParametros['nombreVendedor'])).'%');
            }
        }
        
                
        if( isset($arrayParametros['apellidoVendedor']) )
        {
            if( $arrayParametros['apellidoVendedor'] )
            {
                $strWhere .= "AND ip.USR_VENDEDOR IN (
                                                        SELECT ip3.USR_VENDEDOR
                                                        FROM DB_COMERCIAL.INFO_PUNTO ip3,
                                                             DB_COMERCIAL.INFO_PERSONA ips3
                                                        WHERE ip3.USR_VENDEDOR = ips3.LOGIN
                                                          AND ips3.APELLIDOS LIKE :apellidoVendedor
                                                    ) ";

                $objQuery->setParameter('apellidoVendedor', '%'.trim(strtoupper($arrayParametros['apellidoVendedor'])).'%');
                
                $objQueryCount->setParameter('apellidoVendedor', '%'.trim(strtoupper($arrayParametros['apellidoVendedor'])).'%');
            }
        }
                
        
        if( isset($arrayParametros['usuarioVendedor']) )
        {
            if( $arrayParametros['usuarioVendedor'] )
            {
                if( is_array ( $arrayParametros['usuarioVendedor'] ) )
                {
                    $strWhere .= 'AND ip.USR_VENDEDOR IN (:usuarioVendedor) ';

                    $objQuery->setParameter('usuarioVendedor' , array_values($arrayParametros['usuarioVendedor']));

                    $objQueryCount->setParameter('usuarioVendedor' , array_values($arrayParametros['usuarioVendedor']));
                }
                else
                {
                    $strWhere .= 'AND ip.USR_VENDEDOR = :usuarioVendedor ';

                    $objQuery->setParameter('usuarioVendedor' , $arrayParametros['usuarioVendedor']);

                    $objQueryCount->setParameter('usuarioVendedor' , $arrayParametros['usuarioVendedor']);
                }
            }
        }
        
        
        if( isset($arrayParametros['feAprobacionInicio']) )
        {
            if( $arrayParametros['feAprobacionInicio'] )
            {
                $strWhere .= 'AND ic.FE_APROBACION >= :feAprobacionInicio ';
                
                $objQuery->setParameter('feAprobacionInicio', $arrayParametros['feAprobacionInicio']);
                
                $objQueryCount->setParameter('feAprobacionInicio', $arrayParametros['feAprobacionInicio']);
            }
        }
        
        
        if( isset($arrayParametros['feAprobacionFinal']) )
        {
            if( $arrayParametros['feAprobacionFinal'] )
            {
                $strWhere .= 'AND ic.FE_APROBACION < :feAprobacionFinal ';
                
                $objQuery->setParameter('feAprobacionFinal', $arrayParametros['feAprobacionFinal']);
                
                $objQueryCount->setParameter('feAprobacionFinal', $arrayParametros['feAprobacionFinal']);
            }
        }
        
        
        if( isset($arrayParametros['feCreacionPuntoInicio']) )
        {
            if( $arrayParametros['feCreacionPuntoInicio'] )
            {
                $strWhere .= 'AND ip.FE_CREACION >= :feCreacionPuntoInicio ';
                
                $objQuery->setParameter('feCreacionPuntoInicio', $arrayParametros['feCreacionPuntoInicio']);
                
                $objQueryCount->setParameter('feCreacionPuntoInicio', $arrayParametros['feCreacionPuntoInicio']);
            }
        }
        
        
        if( isset($arrayParametros['feCreacionPuntoFinal']) )
        {
            if( $arrayParametros['feCreacionPuntoFinal'] )
            {
                $strWhere .= 'AND ip.FE_CREACION < :feCreacionPuntoFinal ';
                
                $objQuery->setParameter('feCreacionPuntoFinal', $arrayParametros['feCreacionPuntoFinal']);
                
                $objQueryCount->setParameter('feCreacionPuntoFinal', $arrayParametros['feCreacionPuntoFinal']);
            }
        }
        
        
        if( isset($arrayParametros['fePlanificacionInicio']) || isset($arrayParametros['fePlanificacionFinal']) )
        {
            if( $arrayParametros['fePlanificacionInicio'] || $arrayParametros['fePlanificacionFinal'] )
            {
                $arrayParametros['tipoVenta']                   = 'brutas';
                $arrayParametros['feInicio']                    =  $arrayParametros['fePlanificacionInicio'];
                $arrayParametros['feFinal']                     =  $arrayParametros['fePlanificacionFinal'];
                $arrayParametros['estadosServiciosNoIncluidos'] = array( 'Inactivo', 'Rechazada', 'Cancel', 'Eliminado', 'In-Corte', 'Anulado', 
                                                                         'In-Temp' );
            }//( $arrayParametros['fePlanificacionInicio'] || $arrayParametros['fePlanificacionFinal'] )
        }//( isset($arrayParametros['fePlanificacionInicio']) || isset($arrayParametros['fePlanificacionFinal']) )
        
        
        if( isset($arrayParametros['feActivacionInicio']) || isset($arrayParametros['feActivacionFinal']) )
        {
            if( $arrayParametros['feActivacionInicio'] || $arrayParametros['feActivacionFinal'] )
            {
                if( $arrayParametros['feActivacionInicio'] )
                {
                    $strWhere .= 'AND ins.ID_SERVICIO IN (
                                                    SELECT ins2.ID_SERVICIO
                                                    FROM DB_COMERCIAL.INFO_SERVICIO ins2,
                                                         DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh2
                                                    WHERE ins2.ID_SERVICIO = insh2.SERVICIO_ID
                                                      AND insh2.OBSERVACION LIKE :observacion
                                                      AND ( insh2.ACCION = :accion OR insh2.ACCION IS NULL ) 
                                                      AND insh2.fe_Creacion >= :feActivacionInicio 
                                                ) ';

                    $objQuery->setParameter('feActivacionInicio', $arrayParametros['feActivacionInicio']);

                    $objQueryCount->setParameter('feActivacionInicio', $arrayParametros['feActivacionInicio']);
                } 
                
            
                if( $arrayParametros['feActivacionFinal'] )
                {
                    $strWhere .= 'AND ins.ID_SERVICIO IN (
                                                    SELECT ins3.ID_SERVICIO
                                                    FROM DB_COMERCIAL.INFO_SERVICIO ins3,
                                                         DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh3
                                                    WHERE ins3.ID_SERVICIO = insh3.SERVICIO_ID
                                                      AND insh3.OBSERVACION LIKE :observacion
                                                      AND ( insh3.ACCION = :accion OR insh3.ACCION IS NULL ) 
                                                      AND insh3.fe_Creacion < :feActivacionFinal 
                                                ) ';

                    $objQuery->setParameter('feActivacionFinal', $arrayParametros['feActivacionFinal']);

                    $objQueryCount->setParameter('feActivacionFinal', $arrayParametros['feActivacionFinal']);
                }
                
                $objQuery->setParameter('accion',      'confirmarServicio');
                $objQuery->setParameter('observacion', '%Se confirmo el servicio%');
                
                $objQueryCount->setParameter('accion',      'confirmarServicio');
                $objQueryCount->setParameter('observacion', '%Se confirmo el servicio%');
            }//( $arrayParametros['feActivacionInicio'] || $arrayParametros['feActivacionFinal'] )
        }//( isset($arrayParametros['feActivacionInicio']) || isset($arrayParametros['feActivacionFinal']) )
        
        
        if( isset($arrayParametros['estadosServiciosNoIncluidos']) )
        {
            if( $arrayParametros['estadosServiciosNoIncluidos'] )
            {
                $strWhere .= "AND ins.estado NOT IN (:estadosServiciosNoIncluidos) ";

                $objQuery->setParameter('estadosServiciosNoIncluidos', array_values($arrayParametros['estadosServiciosNoIncluidos']));

                $objQueryCount->setParameter('estadosServiciosNoIncluidos', array_values($arrayParametros['estadosServiciosNoIncluidos']));
            }
        }
        
        
        if( isset($arrayParametros['estadosServiciosIncluidos']) )
        {
            if( $arrayParametros['estadosServiciosIncluidos'] )
            {
                if( is_array($arrayParametros['estadosServiciosIncluidos']) )
                {
                    $strWhere .= "AND ins.estado IN (:estadosServiciosIncluidos) ";

                    $objQuery->setParameter('estadosServiciosIncluidos', array_values($arrayParametros['estadosServiciosIncluidos']));

                    $objQueryCount->setParameter('estadosServiciosIncluidos', array_values($arrayParametros['estadosServiciosIncluidos']));
                }
                else
                {
                    $strWhere .= "AND ins.estado = :estadoServicio ";

                    $objQuery->setParameter('estadoServicio', $arrayParametros['estadosServiciosIncluidos']);

                    $objQueryCount->setParameter('estadoServicio', $arrayParametros['estadosServiciosIncluidos']);
                    
                }//( is_array($arrayParametros['estadosServiciosIncluidos']) )
            }//( $arrayParametros['estadosServiciosIncluidos'] )
        }//( isset($arrayParametros['estadosServiciosIncluidos']) )
        
        
        if( isset($arrayParametros['tipoVenta']) )
        {
            if( $arrayParametros['tipoVenta'] == 'brutas' )
            {
                if( isset($arrayParametros['feInicio']) && isset($arrayParametros['feFinal']) )
                {
                    if( $arrayParametros['feInicio'] && $arrayParametros['feFinal'] )
                    {
                        $strWhere .= 'AND ins.ID_SERVICIO IN (
                                                        SELECT ins4.ID_SERVICIO
                                                        FROM DB_COMERCIAL.INFO_SERVICIO ins4,
                                                             DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh5
                                                        WHERE ins4.ID_SERVICIO = insh5.SERVICIO_ID
                                                          AND insh5.OBSERVACION LIKE :observacion
                                                          AND insh5.fe_Creacion >= :feInicio 
                                                     )
                                       AND ins.ID_SERVICIO IN (
                                                        SELECT ins6.ID_SERVICIO
                                                        FROM DB_COMERCIAL.INFO_SERVICIO ins6,
                                                             DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh7
                                                        WHERE ins6.ID_SERVICIO = insh7.SERVICIO_ID
                                                          AND insh7.OBSERVACION LIKE :observacion
                                                          AND insh7.fe_Creacion < :feFinal 
                                                      ) ';
                
                        $objQuery->setParameter('observacion' , '%Se solicito planificacion%');
                        $objQuery->setParameter('feInicio'    , $arrayParametros['feInicio']);
                        $objQuery->setParameter('feFinal'     , $arrayParametros['feFinal']);
                        
                        $objQueryCount->setParameter('observacion' , '%Se solicito planificacion%');
                        $objQueryCount->setParameter('feInicio'    , $arrayParametros['feInicio']);
                        $objQueryCount->setParameter('feFinal'     , $arrayParametros['feFinal']);
                        
                    }//( $arrayParametros['feInicio'] && $arrayParametros['feFinal'] )
                }//( isset($arrayParametros['feInicio']) && isset($arrayParametros['feFinal']) )
            }//( $arrayParametros['tipoVenta'] == 'brutas' )
            elseif( $arrayParametros['tipoVenta'] != 'activas' )
            {
                if( isset($arrayParametros['feInicio']) && isset($arrayParametros['feFinal']) )
                {
                    if( $arrayParametros['feInicio'] && $arrayParametros['feFinal'] )
                    {
                        $strWhere .= 'AND ip.FE_CREACION >= :feInicio AND ip.FE_CREACION < :feFinal
                                      AND ins.ID_SERVICIO IN (
                                                        SELECT ins_1.ID_SERVICIO
                                                        FROM DB_COMERCIAL.INFO_SERVICIO ins_1,
                                                             DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh_1
                                                        WHERE ins_1.ID_SERVICIO = insh_1.SERVICIO_ID
                                                          AND insh_1.fe_Creacion >= :feInicio 
                                                          AND insh_1.estado IN (:estadoServiciosTipoVenta)
                                                    )
                                      AND ins.ID_SERVICIO IN (
                                                      SELECT ins_2.ID_SERVICIO
                                                      FROM DB_COMERCIAL.INFO_SERVICIO ins_2,
                                                           DB_COMERCIAL.INFO_SERVICIO_HISTORIAL insh_2
                                                      WHERE ins_2.ID_SERVICIO = insh_2.SERVICIO_ID
                                                        AND insh_2.fe_Creacion < :feFinal 
                                                        AND insh_2.estado IN (:estadoServiciosTipoVenta)
                                                    ) ';
                
                        $objQuery->setParameter('estadoServiciosTipoVenta' , array_values($arrayParametros['estadoServiciosTipoVenta']));
                        $objQuery->setParameter('feInicio'                 , $arrayParametros['feInicio']);
                        $objQuery->setParameter('feFinal'                  , $arrayParametros['feFinal']);
                        
                        $objQueryCount->setParameter('estadoServiciosTipoVenta' , array_values($arrayParametros['estadoServiciosTipoVenta']));
                        $objQueryCount->setParameter('feInicio'                 , $arrayParametros['feInicio']);
                        $objQueryCount->setParameter('feFinal'                  , $arrayParametros['feFinal']);
                        
                    }//( $arrayParametros['feInicio'] && $arrayParametros['feFinal'] )
                }//( isset($arrayParametros['feInicio']) && isset($arrayParametros['feFinal']) )
            }//( $arrayParametros['tipoVenta'] != 'activas' )
        }//( isset($arrayParametros['tipoVenta']) )
        
        $strSql      = $strSelect.$strFrom.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;

        $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

        $objQueryCount->setSQL($strSqlCount);
        $intTmpTotal = $objQueryCount->getSingleScalarResult();

        $arrayResultados['resultados'] = null;
        $arrayResultados['total']      = $intTmpTotal;

        if(intval($arrayResultados['total']) > 0)
        {
            $objQuery->setSQL($strSql);

            if( isset($arrayParametros['limite']) && isset($arrayParametros['inicio']))
            {
                $arrayResultados['resultados'] = $this->setQueryLimit($objQuery, $arrayParametros['limite'], $arrayParametros['inicio'])->getResult();
            }
            else
            {
                $arrayResultados['resultados'] = $objQuery->getResult();
            }
        }
        return $arrayResultados;
    }
    
    /**
    * Permite generar el Json de los puntos clientes (logins) segun criterios de busqueda
    *
    * @param $intIdCliente      Parametro correspondiente al cliente a verificar
    * @param $strPtoCliente     Parametro correspondiente al punto cliente a verificar
     *  
    * @author Gina Villalba <gvillalba@telconet.ec>
    * @version 1.0 24-12-2015
    */
    public function getJsonPuntosClientes($intIdCliente, $strPtoCliente,$strEmpresaCod)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->findListarTodosPtosClientesPorPto($intIdCliente, $strPtoCliente,$strEmpresaCod);
        $arrayPtosClientes      = $arrayResultado['registros'];
        
        foreach($arrayPtosClientes as $objPtoCliente):

            $arrayEncontrados[] = array(
                'idPtoCliente'     => $objPtoCliente->getId(),
                'descripcionPto'   => $objPtoCliente->getLogin(),
            );
        endforeach;

        $arrayRespuesta = array('listado' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }

     /**
     * Documentación para el método 'getPuntosParaEnlazarDatos'.
     *
     * Método utilizado para obtener los puntos para crear un enlace de datos
     *
     * @param string login login o parte del login de un punto a buscar por login
     * @param string razonSocial Para buscar login por razon social
     * @param string direccion Para buscar login por direccion del punto
     * @param string idPunto id del punto en session a no incluir en el query
     * @param string codEmpresa codigo de la empresa al cual deben pertenecer los logins
     *
     * @return array puntos
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 15-12-2015
     * @version 1.1 21-03-2015 eliminacion de rol cliente y aumento de estados de servicios para soportar opcion de definir concentrador
    */   
    public function getPuntosParaEnlazarDatos($login,$razonSocial,$direccion,$idPunto,$codEmpresa)
    {
        $puntos = array();
        if($idPunto>0 && (!empty($login) || !empty($razonSocial) || !empty($direccion)))
        {
            $sqlQuery = "
                    SELECT 
                        ip.id,
                        ip.login
                    FROM
                        schemaBundle:InfoPunto ip,
                        schemaBundle:InfoPersonaEmpresaRol iper,
                        schemaBundle:InfoEmpresaRol er,
                        schemaBundle:InfoPersona ipe
                    WHERE
                        ip.personaEmpresaRolId = iper.id
                    and iper.empresaRolId      = er.id
                    and iper.personaId         = ipe.id
                    and er.empresaCod          = :empresaCod
                    and ip.id                 != :punto " .
                    (empty($login)? "" : "and ip.login like :login ") .
                    (empty($razonSocial)? "" : "and ipe.razonSocial like :razonSocial ") .
                    (empty($direccion)? "" : "and ip.direccion like :direccion ") .
                    " and ip.estado not in (:estados) ";
                    
            $query = $this->_em->createQuery($sqlQuery);
            
            if(!empty($login))
            {
                $query->setParameter('login', '%'.$login.'%');
            }
            if(!empty($razonSocial))
            {
                $query->setParameter('razonSocial', '%'.$razonSocial.'%');
            }
            if(!empty($direccion))
            {
                $query->setParameter('direccion', '%'.$direccion.'%');
            }

            $query->setParameter('punto', $idPunto);
            $query->setParameter('empresaCod', $codEmpresa);
            $query->setParameter('estados', array('Anulado','Cancel','Cancelado','Eliminado','Reubicado','Trasladado'));
            
            $puntos = $query->getResult();
        }    
        return $puntos;

    }
    
    /**
     * 
     * Metodo que permite obtener todos a agregar como afectados de un CASO dado un login o una razon social
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-05-2016
     * 
     * @param string $tipoParam
     * @param string $strFiltro
     * @return Array $arrayResultado [ idPunto , login , estado , nombres ]
     */
    public function getResultadoPuntosParaAfectacionCasos($tipoParam, $strFiltro , $start , $limit)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm   = new ResultSetMappingBuilder($this->_em);	      
            $query = $this->_em->createNativeQuery(null, $rsm);	           

            $strWhere = "";
            
            if($tipoParam == 'razonSocial')
            {
                $strWhere .= " AND PERSONA.ID_PERSONA  IN (SELECT ID_PERSONA FROM INFO_PERSONA WHERE UPPER(RAZON_SOCIAL) LIKE UPPER(:razonSocial)) ";
                $query->setParameter('razonSocial', '%'.$strFiltro.'%');             
            }
            else if($tipoParam == 'login')
            {
                $strWhere .= " AND PUNTO.LOGIN like (:login) ";
                $query->setParameter('login', '%'.$strFiltro.'%');         
            }
            
            $selectCont   = "SELECT COUNT(*) CONT ";

            $strSelectData = "SELECT 
                                PUNTO.ID_PUNTO,
                                PUNTO.LOGIN,
                                PUNTO.ESTADO,                                
                                NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES||' '||PERSONA.APELLIDOS) NOMBRES ";                                          
            
            $strSql = "          FROM 
                                  INFO_PUNTO PUNTO,
                                  INFO_PERSONA PERSONA,
                                  INFO_PERSONA_EMPRESA_ROL PERSONA_ROL  
                                WHERE 
                                      PUNTO.PERSONA_EMPRESA_ROL_ID = PERSONA_ROL.ID_PERSONA_ROL
                                AND PERSONA.ID_PERSONA             = PERSONA_ROL.PERSONA_ID
                                AND PUNTO.ESTADO                   = :estado
                                AND PERSONA_ROL.ESTADO             = :estado 
                                $strWhere";
            
            $rsm->addScalarResult('ID_PUNTO','idPunto','integer');
            $rsm->addScalarResult('LOGIN','login','string');
            $rsm->addScalarResult('NOMBRES','nombres','string');
            $rsm->addScalarResult('ESTADO','estado','string');         
            $rsm->addScalarResult('CONT','cont','integer');

            $query->setParameter('estado', 'Activo');  
            
            $query->setSQL($selectCont.$strSql);	   

            $arrayResultado['total'] = $query->getSingleScalarResult();        

            $query->setSQL($strSelectData.$strSql);	   
            
            $objQuery = $this->setQueryLimit($query,$limit,$start);

            $arrayResultado['resultado'] = $objQuery->getArrayResult();                        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
     /**
     * getResultadoPuntosTraslados
     *
     * Metodo para obtener los puntos clientes Login que pueden ser trasladados.
     * Consideracion:  
     * 1)Solo se permite trasladar Puntos Clientes en estado Activo.
     * 2)Solo se permite trasladar si el Login posee al menos 1 servicio en estado Activo
     * 3)Se Cargara el Grid solo con los Logines que cumplen las condiciones. 
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2017
     * Costo del Query: 7
     * 
     * @param  array $arrayParametros [
     *                                 'intIdPersonaRol'    : Id de la Persona Empresa Rol Cliente
     *                                 'intIdPto'           : Id del Punto que realiza el traslado
     *                                 'strEstado'          : Estado
     *                                ]     
     * @return array
     */
    public function getResultadoPuntosTraslados($arrayParametros)
    {      
       $strSqlDatos      = 'SELECT pto.id,pto.login ';
       $strSqlCantidad   = 'SELECT count(pto) ';
       $strSqlFrom       = 'FROM schemaBundle:InfoPunto pto
                            INNER JOIN schemaBundle:InfoPersonaEmpresaRol pers WITH pto.personaEmpresaRolId = pers.id
                            WHERE pto.personaEmpresaRolId = :intIdPersonaRol                              
                            AND pto.estado= :strEstado
                            AND EXISTS (
                                        SELECT 1
                                        FROM schemaBundle:InfoServicio servicio
                                        WHERE pto.id = servicio.puntoId
                                        AND servicio.estado= :strEstado) ';
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        $strQueryDatos->setParameter('intIdPersonaRol',$arrayParametros['intIdPersonaRol'] );
        $strQueryDatos->setParameter('strEstado',$arrayParametros['strEstado'] );
        $strSqlDatos    .= $strSqlFrom;
        
        $strQueryDatos->setDQL($strSqlDatos);
        $objDatos        = $strQueryDatos->getResult();
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        $strQueryCantidad->setParameter('intIdPersonaRol',$arrayParametros['intIdPersonaRol'] );
        $strQueryCantidad->setParameter('strEstado',$arrayParametros['strEstado'] ); 
        $strSqlCantidad .= $strSqlFrom;   
        
        $strQueryCantidad->setDQL($strSqlCantidad);
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultadoPuntos['objRegistros'] = $objDatos;
        $arrayResultadoPuntos['intTotal']     = $intTotal;
        
        return $arrayResultadoPuntos;
    }

     /**
     * getPuntosTraslados
     *
     * Metodo para obtener los puntos clientes Login que pueden ser trasladados.
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2017
     *
     * @param  array $arrayParametros [
     *                                 'intIdPersonaRol'    : Id de la Persona Empresa Rol Cliente  
     *                                 'intIdPto'           : Id del Punto que realiza el traslado
     *                                 'strEstado'          : Estado                                                                     
     *                                ]     
     * @return JSON
     */
    public function getPuntosTraslados($arrayParametros)
    {
        $arrayPuntosEncontrados = array();
        $arrayResultado = $this->getResultadoPuntosTraslados($arrayParametros);
        $objPuntos = $arrayResultado['objRegistros'];
        $intTotal = $arrayResultado['intTotal'];
        foreach($objPuntos as $objPuntos)
        {
            if(isset($arrayParametros['intIdPto']) && !empty($arrayParametros['intIdPto']) && $arrayParametros['intIdPto']!=$objPuntos['id'])
            {
                $arrayPuntosEncontrados[] = array('idPunto' => $objPuntos['id'],
                                                  'login'   => trim($objPuntos['login'])
                                                 );
            }
        }
        $arrayRespuesta = array('total' => $intTotal, 'listado' => $arrayPuntosEncontrados);
        return $arrayRespuesta;
    }
        
    /**
     * 
     * Metodo encargado de consultar todos los puntos dado una persona empresa rol para poder seleccionar sus servicios a ser clonados
     * 
     * Costo : 14
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 23-01-2018
     * 
     * @param  Array $arrayParametros [ intPersonaRolId , intIdOficina , strLogin ]
     * @return Array $arrayResultado  [ idPunto , login , nombrePunto , nombreOficina ]
     */
    public function getArrayPuntosMigracionFactibilidad($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);	  
            $strWhere = "";
            
            $objQuery->setParameter('personaRol', $arrayParametros['intPersonaRolId']); 
            $objQuery->setParameter('productos',  $arrayParametros['arrayProductosAdmitidos']); 
            $objQuery->setParameter('estado',     'Activo'); 
            $objQuery->setParameter('ultimaMilla', $arrayParametros['intUltimaMilla']); 
           
            if(isset($arrayParametros['intIdOficina']) && !empty($arrayParametros['intIdOficina']))
            {
                $strWhere .= ' AND OFICINA.ID_OFICINA = :oficina ';
                $objQuery->setParameter('oficina',    $arrayParametros['intIdOficina']);
            }
            
            if(isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= ' AND PUNTO.LOGIN LIKE :login ';
                $objQuery->setParameter('login',    '%'.$arrayParametros['strLogin'].'%');
            }
            
            $strSql = "SELECT 
                        PUNTO.ID_PUNTO,
                        PUNTO.LOGIN,
                        PUNTO.NOMBRE_PUNTO,
                        OFICINA.NOMBRE_OFICINA
                      FROM 
                        DB_COMERCIAL.info_punto         PUNTO,
                        DB_COMERCIAL.ADMI_JURISDICCION  JURISDICCION,
                        DB_COMERCIAL.INFO_OFICINA_GRUPO OFICINA
                      WHERE 
                            PUNTO.PUNTO_COBERTURA_ID   = JURISDICCION.ID_JURISDICCION
                      AND JURISDICCION.OFICINA_ID      = OFICINA.ID_OFICINA
                      AND PUNTO.PERSONA_EMPRESA_ROL_ID = :personaRol
                      AND PUNTO.ESTADO                 = :estado
                      AND ((SELECT COUNT(*)
                                FROM 
                                  DB_COMERCIAL.INFO_SERVICIO SERV,
                                  DB_COMERCIAL.ADMI_PRODUCTO PROD,
                                  DB_COMERCIAL.INFO_SERVICIO_TECNICO TEC
                                WHERE
                                      SERV.PUNTO_ID  = PUNTO.ID_PUNTO
                                AND SERV.ESTADO      = :estado 
                                AND SERV.PRODUCTO_ID = PROD.ID_PRODUCTO 
                                AND PROD.NOMBRE_TECNICO IN (:productos)
                                AND SERV.ID_SERVICIO = TEC.SERVICIO_ID
                                AND TEC.ULTIMA_MILLA_ID = :ultimaMilla) > 0)
                      $strWhere ";
                        
            $objRsm->addScalarResult('ID_PUNTO',      'idPunto',      'integer');
            $objRsm->addScalarResult('LOGIN',         'login',        'string');
            $objRsm->addScalarResult('NOMBRE_PUNTO',  'nombrePunto',  'string');
            $objRsm->addScalarResult('NOMBRE_OFICINA','nombreOficina','string');        
            
            $objQuery->setSQL($strSql);
                       
            $arrayResultado = $objQuery->getArrayResult();                        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
    
    /**
     * 
     * Metodo encargado de consultar todos los servicios dado un punto , ultima milla y tipo de producto enviado como parametro ( metodo usado para
     * migracion y clonacion de datos de un servicios a otro recien creado )
     * 
     * Costo : 8
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 23-01-2018
     * 
     * @param  Array $arrayParametros [ intIdPunto , intUltimaMilla , arrayProductosAdmitidos ]
     * @return Array $arrayResultado  [ idServicios , loginAux , nombreProducto , capacidad1 , capacidad2 ]
     */
    public function getArrayServiciosMigracionFactibilidad($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
                      
            $strSql = "SELECT 
                        SERVICIO.ID_SERVICIO,
                        SERVICIO.LOGIN_AUX,
                        PRODUCTO.DESCRIPCION_PRODUCTO,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SERVICIO.ID_SERVICIO,'CAPACIDAD1') CAPACIDAD1,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SERVICIO.ID_SERVICIO,'CAPACIDAD2') CAPACIDAD2
                      FROM 
                        DB_COMERCIAL.INFO_PUNTO            PUNTO,
                        DB_COMERCIAL.ADMI_PRODUCTO         PRODUCTO,
                        DB_COMERCIAL.INFO_SERVICIO         SERVICIO,
                        DB_COMERCIAL.INFO_SERVICIO_TECNICO SERVICIO_TECNICO
                      WHERE 
                            PRODUCTO.ID_PRODUCTO           = SERVICIO.PRODUCTO_ID
                      AND SERVICIO.ID_SERVICIO             = SERVICIO_TECNICO.SERVICIO_ID
                      AND SERVICIO.PUNTO_ID                = PUNTO.ID_PUNTO
                      AND SERVICIO_TECNICO.ULTIMA_MILLA_ID = :ultimaMilla
                      AND PRODUCTO.NOMBRE_TECNICO          IN (:productos)
                      AND SERVICIO.ESTADO                  = :estado
                      AND PUNTO.ID_PUNTO                   = :punto";
                        
            $objQuery->setParameter('punto',       $arrayParametros['intIdPunto']); 
            $objQuery->setParameter('estado',      'Activo'); 
            $objQuery->setParameter('ultimaMilla', $arrayParametros['intUltimaMilla']); 
            $objQuery->setParameter('productos',   $arrayParametros['arrayProductosAdmitidos']); 
            
            $objRsm->addScalarResult('ID_SERVICIO',          'idServicio',      'integer');
            $objRsm->addScalarResult('LOGIN_AUX',            'loginAux',        'string');
            $objRsm->addScalarResult('DESCRIPCION_PRODUCTO', 'nombreProducto',  'string');
            $objRsm->addScalarResult('CAPACIDAD1',           'capacidad1',      'integer');        
            $objRsm->addScalarResult('CAPACIDAD2',           'capacidad2',      'integer');    
            
            $objQuery->setSQL($strSql);
                       
            $arrayResultado = $objQuery->getArrayResult();                        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
    
    /*
     * Documentación para el método 'getLoginesPuntosFacturacion'.
     *
     * Retorna un listado de logines que son puntos de facturación asociados a los servicios del punto enviado como parámetro.
     *
     * @param $intIdPunto    Integer: Id del punto a consultar.
     * 
     * @return Array Listado de puntos padre de facturación.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 14-03-2017
     * 
     */
    public function getLoginesPuntosFacturacion($intIdPunto)
    {
        try
        {
            $objQuery = $this->_em->createQuery();

            $strDQL   = "SELECT DISTINCT pto.login
                         FROM   schemaBundle:InfoPunto pto 
                         JOIN   schemaBundle:InfoServicio ise WITH pto.id = ise.puntoFacturacionId
                         JOIN   schemaBundle:InfoPuntoDatoAdicional pda WITH pto.id = pda.puntoId 
                         WHERE  pto.estado              = :strEstato
                         AND    pda.esPadreFacturacion  = :strEsPtoFacturacion 
                         AND    ise.puntoId             = :intIdPunto";
                       
            $objQuery->setParameter("strEstato",'Activo');
            $objQuery->setParameter("strEsPtoFacturacion", 'S');
            $objQuery->setParameter("intIdPunto",$intIdPunto);
             
            $arrayResultado= $objQuery->setDQL($strDQL)->getResult();            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());   
        }
        
        return $arrayResultado;
    }     
    
    /**
     * 
     * Metodo encargado de consultar todas las soluciones que fueron creados para un determinado punto, devuelve el numero
     * de solución, descripción y precio total de venta referente
     * 
     * Costo : 21
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 12-03-2018
     * 
     * @param type $intIdPunto
     * @return Array
     */
    public function getArrayResumenSolucionesPorPunto($intIdPunto)
    {
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
                      
            $strSql = "SELECT                                                 
                        DISTINCT(SERVICIO_PROD_CARACT.VALOR) SECUENCIAL,
                        SERVICIO_PROD_CARACT_NG.VALOR NOMBRE_SOLUCION,
                        (SELECT SUM(S.PRECIO_VENTA)
                        FROM INFO_SERVICIO S,
                          INFO_SERVICIO_PROD_CARACT SPC,
                          ADMI_PRODUCTO_CARACTERISTICA APC,
                          ADMI_CARACTERISTICA C
                        WHERE S.ID_SERVICIO                 = SPC.SERVICIO_ID
                        AND SPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
                        AND APC.CARACTERISTICA_ID           = C.ID_CARACTERISTICA
                        AND C.DESCRIPCION_CARACTERISTICA    = :secuencial
                        AND SPC.ESTADO                      = :estado
                        AND SPC.VALOR                       = SERVICIO_PROD_CARACT.VALOR
                        ) TOTAL_SOLUCION
                      FROM 
                        INFO_SERVICIO SERVICIO,
                        INFO_SERVICIO_PROD_CARACT SERVICIO_PROD_CARACT,
                        ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT,
                        ADMI_CARACTERISTICA CARACT,
                        INFO_SERVICIO_PROD_CARACT SERVICIO_PROD_CARACT_NG,
                        ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT_NG,
                        ADMI_CARACTERISTICA CARACT_NG
                      WHERE SERVICIO.PUNTO_ID                                 = :punto
                      AND SERVICIO.ID_SERVICIO                                = SERVICIO_PROD_CARACT.SERVICIO_ID
                      AND SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID    = PROD_CARACT.ID_PRODUCTO_CARACTERISITICA
                      AND PROD_CARACT.CARACTERISTICA_ID                       = CARACT.ID_CARACTERISTICA
                      AND CARACT.DESCRIPCION_CARACTERISTICA                   = :secuencial
                      AND SERVICIO_PROD_CARACT.ESTADO                         = :estado
                      AND SERVICIO_PROD_CARACT_NG.SERVICIO_ID                 = SERVICIO.ID_SERVICIO
                      AND SERVICIO_PROD_CARACT_NG.PRODUCTO_CARACTERISITICA_ID = PROD_CARACT_NG.ID_PRODUCTO_CARACTERISITICA
                      AND PROD_CARACT_NG.CARACTERISTICA_ID                    = CARACT_NG.ID_CARACTERISTICA
                      AND CARACT_NG.DESCRIPCION_CARACTERISTICA                = :nombreSolucion
                      AND SERVICIO_PROD_CARACT_NG.ESTADO                      = :estado
                      ORDER BY SERVICIO_PROD_CARACT.VALOR ASC";
                        
            $objQuery->setParameter('punto',         $intIdPunto); 
            $objQuery->setParameter('estado',        'Activo'); 
            $objQuery->setParameter('secuencial',    'SECUENCIAL_GRUPO'); 
            $objQuery->setParameter('nombreSolucion','NOMBRE_GRUPO_PRODUCTOS'); 
            
            $objRsm->addScalarResult('SECUENCIAL',           'numeroSolucion',      'integer');
            $objRsm->addScalarResult('NOMBRE_SOLUCION',      'nombreSolucion',      'string');     
            $objRsm->addScalarResult('TOTAL_SOLUCION',       'totalSolucion',       'string');   
            
            $objQuery->setSQL($strSql);
                       
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }

     /**
     * 
     * Metodo encargado de validar si un punto tiene autorizada la solicitud por contrato cargado referente a producto CloudForm
     * 
     * Costo : 20
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2018
     * 
     * @param String $strLogin
     * @return boolean
     */
    public function isSolicitudCloudFormAprobadaPorPunto($strLogin)
    {        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
                      
            $strSql = "   SELECT 
                            COUNT(*) CONT
                          FROM 
                            DB_COMUNICACION.INFO_DOCUMENTO          DOCUMENTO,
                            DB_COMUNICACION.INFO_DOCUMENTO_RELACION RELACION,
                            DB_COMERCIAL.INFO_PUNTO                 PUNTO,
                            DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL  DOC_GENERAL,
                            DB_COMERCIAL.INFO_DETALLE_SOLICITUD     SOLICITUD,
                            DB_COMERCIAL.INFO_DETALLE_SOL_CARACT    SOL_CARACT,
                            DB_COMERCIAL.ADMI_CARACTERISTICA        CARACT,
                            DB_COMERCIAL.ADMI_TIPO_SOLICITUD        TIPO_SOL
                          WHERE 
                                DOCUMENTO.ID_DOCUMENTO            = RELACION.DOCUMENTO_ID
                          AND RELACION.PUNTO_ID                   = PUNTO.ID_PUNTO
                          AND DOCUMENTO.TIPO_DOCUMENTO_GENERAL_ID = DOC_GENERAL.ID_TIPO_DOCUMENTO
                          AND DOC_GENERAL.CODIGO_TIPO_DOCUMENTO   = :codTipoDoc
                          AND SOL_CARACT.VALOR                    = TO_CHAR(PUNTO.ID_PUNTO)
                          AND SOL_CARACT.CARACTERISTICA_ID        = CARACT.ID_CARACTERISTICA
                          AND CARACT.DESCRIPCION_CARACTERISTICA   = :caracteristica
                          AND SOLICITUD.ID_DETALLE_SOLICITUD      = SOL_CARACT.DETALLE_SOLICITUD_ID
                          AND SOLICITUD.TIPO_SOLICITUD_ID         = TIPO_SOL.ID_TIPO_SOLICITUD
                          AND TIPO_SOL.DESCRIPCION_SOLICITUD      = :tipoSolicitud
                          AND SOLICITUD.ESTADO                    = :estadoSol
                          AND DOCUMENTO.ESTADO                    = :estado
                          AND PUNTO.LOGIN                         = :login ";
                        
            $objQuery->setParameter('login',         $strLogin); 
            $objQuery->setParameter('estado',        'Activo'); 
            $objQuery->setParameter('estadoSol',     'Aprobada'); 
            $objQuery->setParameter('codTipoDoc',    'CLOUD'); 
            $objQuery->setParameter('caracteristica','ID_PUNTO'); 
            $objQuery->setParameter('tipoSolicitud', 'SOLICITUD APROBACION CLOUDFORM'); 
            
            $objRsm->addScalarResult('CONT',           'cont',      'integer');
            
            $objQuery->setSQL($strSql);
                       
            $arrayAprobadas = $objQuery->getOneOrNullResult();
            
            return $arrayAprobadas['cont'] != 0;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }        
    }
    
    /**
     * 
     * Metodo encargado de devolver los documento de cloudpublic relacionado a un Punto
     * 
     * Costo : 15
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2018
     * 
     * @param String $strLogin
     * @return Array
     */
    public function getArrayDocumentosPorPunto($strLogin)
    {        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
                      
            $strSql = "   SELECT 
                            DOCUMENTO.ID_DOCUMENTO,
                            RELACION.ID_DOCUMENTO_RELACION
                          FROM 
                            DB_COMUNICACION.INFO_DOCUMENTO          DOCUMENTO,
                            DB_COMUNICACION.INFO_DOCUMENTO_RELACION RELACION,
                            DB_COMERCIAL.INFO_PUNTO                 PUNTO,
                            DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL  DOC_GENERAL                            
                          WHERE 
                                DOCUMENTO.ID_DOCUMENTO            = RELACION.DOCUMENTO_ID
                          AND RELACION.PUNTO_ID                   = PUNTO.ID_PUNTO
                          AND DOCUMENTO.TIPO_DOCUMENTO_GENERAL_ID = DOC_GENERAL.ID_TIPO_DOCUMENTO
                          AND DOC_GENERAL.CODIGO_TIPO_DOCUMENTO   = :codTipoDoc                          
                          AND DOCUMENTO.ESTADO                    = :estado
                          AND PUNTO.LOGIN                         = :login 
                          AND ROWNUM                              = 1";
                        
            $objQuery->setParameter('login',         $strLogin); 
            $objQuery->setParameter('estado',        'Activo');             
            $objQuery->setParameter('codTipoDoc',    'CLOUD');            
            
            $objRsm->addScalarResult('ID_DOCUMENTO',           'idDocumento',      'integer');
            $objRsm->addScalarResult('ID_DOCUMENTO_RELACION',  'idDocumentoRelacion',      'integer');
            
            $objQuery->setSQL($strSql);
                       
            $arrayAprobadas = $objQuery->getResult();
            
            return $arrayAprobadas;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            return array();
        }        
    }
    
    /**
     * 
     * Metodo encargado de devolver la información necesaria que se requiere del punto para generación de credenciales vía WS de Cloudform
     * 
     * Costo : 7
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 26-07-2018
     * 
     * @param String $intIdPunto
     * @return Array
     */
    public function getArrayDatosPuntoCloudPublic($intIdPunto)
    {
        $arrayRespuesta = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
                      
            $strSql = "   SELECT
                            PUNTO.LOGIN,                            
                            PERSONA.RAZON_SOCIAL,
                            PERSONA.DIRECCION,
                            (SELECT PERSONA.NOMBRES
                            FROM   
                              DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                              DB_COMERCIAL.INFO_PERSONA_CONTACTO CONTACTO,
                              DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL,
                              DB_COMERCIAL.ADMI_ROL ROL,
                              DB_COMERCIAL.ADMI_TIPO_ROL TIPO_ROL,
                              DB_COMERCIAL.INFO_PERSONA PERSONA                             
                            WHERE 
                                 CONTACTO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
                            AND CONTACTO.PERSONA_ROL_ID          = PERSONA_ROL.ID_PERSONA_ROL
                            AND PERSONA_ROL.EMPRESA_ROL_ID       = EMPRESA_ROL.ID_EMPRESA_ROL
                            AND EMPRESA_ROL.ROL_ID               = ROL.ID_ROL
                            AND ROL.TIPO_ROL_ID                  = TIPO_ROL.ID_TIPO_ROL
                            AND PERSONA_ROL.PERSONA_ID           = PERSONA.ID_PERSONA                          
                            AND ROL.DESCRIPCION_ROL              = :tipoContacto
                            AND TIPO_ROL.DESCRIPCION_TIPO_ROL    = :contacto
                            AND CONTACTO.ESTADO                  = :estado
                            AND PERSONA_ROL.ESTADO               = :estado
                            AND ROWNUM                           = 1) NOMBRES,
                             (SELECT PERSONA.APELLIDOS
                            FROM   
                              DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                              DB_COMERCIAL.INFO_PERSONA_CONTACTO CONTACTO,
                              DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL,
                              DB_COMERCIAL.ADMI_ROL ROL,
                              DB_COMERCIAL.ADMI_TIPO_ROL TIPO_ROL,
                              DB_COMERCIAL.INFO_PERSONA PERSONA                             
                            WHERE 
                                 CONTACTO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
                            AND CONTACTO.PERSONA_ROL_ID          = PERSONA_ROL.ID_PERSONA_ROL
                            AND PERSONA_ROL.EMPRESA_ROL_ID       = EMPRESA_ROL.ID_EMPRESA_ROL
                            AND EMPRESA_ROL.ROL_ID               = ROL.ID_ROL
                            AND ROL.TIPO_ROL_ID                  = TIPO_ROL.ID_TIPO_ROL
                            AND PERSONA_ROL.PERSONA_ID           = PERSONA.ID_PERSONA                          
                            AND ROL.DESCRIPCION_ROL              = :tipoContacto
                            AND TIPO_ROL.DESCRIPCION_TIPO_ROL    = :contacto
                            AND CONTACTO.ESTADO                  = :estado
                            AND PERSONA_ROL.ESTADO               = :estado
                            AND ROWNUM                           = 1) APELLIDOS,
                            (SELECT FORMA_CONTACTO.VALOR
                            FROM   
                              DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                              DB_COMERCIAL.INFO_PERSONA_CONTACTO CONTACTO,
                              DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL,
                              DB_COMERCIAL.ADMI_ROL ROL,
                              DB_COMERCIAL.ADMI_TIPO_ROL TIPO_ROL,
                              DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FORMA_CONTACTO,
                              DB_COMERCIAL.ADMI_FORMA_CONTACTO FORMA
                            WHERE 
                                 CONTACTO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
                            AND CONTACTO.PERSONA_ROL_ID          = PERSONA_ROL.ID_PERSONA_ROL
                            AND PERSONA_ROL.EMPRESA_ROL_ID       = EMPRESA_ROL.ID_EMPRESA_ROL
                            AND EMPRESA_ROL.ROL_ID               = ROL.ID_ROL
                            AND ROL.TIPO_ROL_ID                  = TIPO_ROL.ID_TIPO_ROL
                            AND PERSONA_ROL.PERSONA_ID           = FORMA_CONTACTO.PERSONA_ID
                            AND FORMA_CONTACTO.FORMA_CONTACTO_ID = FORMA.ID_FORMA_CONTACTO
                            AND FORMA.DESCRIPCION_FORMA_CONTACTO = :correo
                            AND FORMA.ESTADO                     = :estado
                            AND ROL.DESCRIPCION_ROL              = :tipoContacto
                            AND TIPO_ROL.DESCRIPCION_TIPO_ROL    = :contacto
                            AND CONTACTO.ESTADO                  = :estado
                            AND PERSONA_ROL.ESTADO               = :estado
                            AND ROWNUM                           = 1
                            ) CORREO,
                            (SELECT FORMA_CONTACTO.VALOR
                            FROM   
                              DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                              DB_COMERCIAL.INFO_PERSONA_CONTACTO CONTACTO,
                              DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL,
                              DB_COMERCIAL.ADMI_ROL ROL,
                              DB_COMERCIAL.ADMI_TIPO_ROL TIPO_ROL,
                              DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FORMA_CONTACTO,
                              DB_COMERCIAL.ADMI_FORMA_CONTACTO FORMA
                            WHERE 
                                 CONTACTO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
                            AND CONTACTO.PERSONA_ROL_ID          = PERSONA_ROL.ID_PERSONA_ROL
                            AND PERSONA_ROL.EMPRESA_ROL_ID       = EMPRESA_ROL.ID_EMPRESA_ROL
                            AND EMPRESA_ROL.ROL_ID               = ROL.ID_ROL
                            AND ROL.TIPO_ROL_ID                  = TIPO_ROL.ID_TIPO_ROL
                            AND PERSONA_ROL.PERSONA_ID           = FORMA_CONTACTO.PERSONA_ID
                            AND FORMA_CONTACTO.FORMA_CONTACTO_ID = FORMA.ID_FORMA_CONTACTO
                            AND FORMA.DESCRIPCION_FORMA_CONTACTO LIKE :telefono
                            AND FORMA.ESTADO                     = :estado
                            AND ROL.DESCRIPCION_ROL              = :tipoContacto
                            AND TIPO_ROL.DESCRIPCION_TIPO_ROL    = :contacto
                            AND CONTACTO.ESTADO                  = :estado
                            AND PERSONA_ROL.ESTADO               = :estado
                            AND ROWNUM                           = 1
                            ) TELEFONO
                          FROM
                            DB_COMERCIAL.INFO_PUNTO               PUNTO,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_EMPRESA_ROL,
                            DB_COMERCIAL.INFO_PERSONA             PERSONA
                          WHERE
                            PUNTO.PERSONA_EMPRESA_ROL_ID   = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL AND
                            PERSONA_EMPRESA_ROL.PERSONA_ID = PERSONA.ID_PERSONA AND
                            PUNTO.ID_PUNTO                 = :punto";
                        
            $objQuery->setParameter('punto',         $intIdPunto); 
            $objQuery->setParameter('estado',        'Activo');             
            $objQuery->setParameter('contacto',      'Contacto');            
            $objQuery->setParameter('tipoContacto',  'Contacto Tecnico');            
            $objQuery->setParameter('correo',        'Correo Electronico');     
            $objQuery->setParameter('telefono',      'Telefono%');     
            
            $objRsm->addScalarResult('LOGIN',         'login',       'string');
            $objRsm->addScalarResult('NOMBRES',       'nombres',     'string');
            $objRsm->addScalarResult('APELLIDOS',     'apellidos',   'string');
            $objRsm->addScalarResult('RAZON_SOCIAL',  'razonSocial', 'string');
            $objRsm->addScalarResult('DIRECCION',     'direccion',   'string');
            $objRsm->addScalarResult('CORREO',        'correo',      'string');
            $objRsm->addScalarResult('TELEFONO',      'telefono',    'string');
            
            $objQuery->setSQL($strSql);
                       
            $arrayRespuesta = $objQuery->getOneOrNullResult();
        }
        catch(\Exception $e)
        {
            error_log('getArrayDatosPuntoCloudPublic => '.$e->getMessage());
        }
        
        return $arrayRespuesta;
    }

    /**
     *
     * Método que devuelve los puntos según los parámetros asignados
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0
     * @since 10-04-2018
     *
     * @author Modificado: Ronny Moran <rmoranc@telconet.ec>
     * @version 1.1 14-05-2018 - Se agrega en la respuesta el numero de casos que tiene el punto.
     *
     * Costo 4
     *
     * @param Array $arrayParametros [ 
     *                                intIdElemento,
     *                                strTipoElemento,
     *                                strEstado
     *                               ]
     * @return Array $arrayResultado
     */
    public function getPuntoCliente($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strWhere = "";
        $strSql   = "SELECT IP.ID_PUNTO,
                        IP.LOGIN,
                        IP.LATITUD,
                        IP.LONGITUD,
                        IP.PUNTO_COBERTURA_ID,
                        AJ.NOMBRE_JURISDICCION,
                        AC.NOMBRE_CANTON,
                        APROV.NOMBRE_PROVINCIA,
                        IP.DIRECCION,
                        IP.DESCRIPCION_PUNTO,
                        IPER.RAZON_SOCIAL,
                        IPER.NOMBRES,
                        IPER.APELLIDOS,
                        IP.DIRECCION,
                        IP.ESTADO,
                        NVL((SELECT COUNT(INCA.ID_CASO)
                     FROM DB_SOPORTE.INFO_PARTE_AFECTADA IPAF,
                      DB_SOPORTE.INFO_DETALLE IDET,
                      DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHI,
                      DB_SOPORTE.INFO_CASO INCA,
                      DB_SOPORTE.INFO_CASO_HISTORIAL INHI
                     WHERE IPAF.DETALLE_ID         = IDET.ID_DETALLE
                     AND IDET.DETALLE_HIPOTESIS_ID = IDHI.ID_DETALLE_HIPOTESIS
                     AND IDHI.CASO_ID              = INCA.ID_CASO
                     AND INHI.FE_CREACION >= TO_DATE (add_months( sysdate, -12 ))
                     AND INHI.ID_CASO_HISTORIAL =
                      (SELECT MAX(ICHIST.ID_CASO_HISTORIAL)
                      FROM db_soporte.INFO_CASO_HISTORIAL ICHIST
                      WHERE ICHIST.CASO_ID = INCA.ID_CASO
                      )
                    AND IPAF.TIPO_AFECTADO = 'Cliente'
                    AND IPAF.AFECTADO_ID   = IP.ID_PUNTO
                    GROUP BY IPAF.AFECTADO_ID
                    ),0) Casos
                    FROM DB_COMERCIAL.INFO_PUNTO IP
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPEROL
                    ON IP.PERSONA_EMPRESA_ROL_ID = IPEROL.ID_PERSONA_ROL
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA IPER
                    ON IPER.ID_PERSONA = IPEROL.PERSONA_ID
                    LEFT JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER
                    ON IPEROL.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                    LEFT JOIN DB_GENERAL.ADMI_ROL AR
                    ON IER.ROL_ID = AR.ID_ROL
                    LEFT JOIN DB_GENERAL.ADMI_TIPO_ROL ATR
                    ON AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL
                    LEFT JOIN DB_COMERCIAL.ADMI_JURISDICCION AJ
                    ON AJ.ID_JURISDICCION = IP.PUNTO_COBERTURA_ID
                    LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG
                    ON IOG.ID_OFICINA = AJ.OFICINA_ID
                    LEFT JOIN DB_COMERCIAL.ADMI_CANTON AC
                    ON AC.ID_CANTON = IOG.CANTON_ID
                    LEFT JOIN DB_COMERCIAL.ADMI_PROVINCIA APROV
                    ON APROV.ID_PROVINCIA = AC.PROVINCIA_ID
                    WHERE  1=1";

        if(isset($arrayParametros["strCodEmpresa"]) && !empty($arrayParametros["strCodEmpresa"]))
        {
            $strWhere .= " AND IER.EMPRESA_COD = :strCodEmpresa ";
            $objQuery->setParameter("strCodEmpresa", $arrayParametros["strCodEmpresa"]); 
        }

        if(isset($arrayParametros["intIdPersona"]) && $arrayParametros["intIdPersona"] > 0)
        {
            $strWhere .= " AND IPER.ID_PERSONA = :intIdPersona ";
            $objQuery->setParameter("intIdPersona", $arrayParametros["intIdPersona"]); 
        }

        if(isset($arrayParametros["intIdCanton"]) && $arrayParametros["intIdCanton"] > 0)
        {
            $strWhere .= " AND AC.ID_CANTON = :intIdCanton ";
            $objQuery->setParameter("intIdCanton", $arrayParametros["intIdCanton"]); 
        }

        if(isset($arrayParametros["strDireccion"]) && !empty($arrayParametros["strDireccion"]))
        {
            $strWhere .= " AND IP.DIRECCION = :strDireccion ";
            $objQuery->setParameter("strDireccion", $arrayParametros["strDireccion"]); 
        }

        if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
        {
            $strWhere .= " AND IP.ESTADO = :strEstado ";
            $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]); 
        }

        if(isset($arrayParametros["strLogin"]) && !empty($arrayParametros["strLogin"]))
        {
            $strWhere .= " AND IP.LOGIN = :strLogin ";
            $objQuery->setParameter("strLogin", $arrayParametros["strLogin"]); 
        }

        if(isset($arrayParametros["strEstadoNotIn"]) && !empty($arrayParametros["strEstadoNotIn"]))
        {
            $strWhere .= " AND IP.ESTADO NOT IN (:strEstadoNotIn) ";
            $objQuery->setParameter("strEstadoNotIn", $arrayParametros["strEstadoNotIn"]); 
        }

        $strSql .= $strWhere;

        $objRsm->addScalarResult('ID_PUNTO', 'idPunto', 'integer');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('LATITUD', 'latitud', 'string');
        $objRsm->addScalarResult('LONGITUD', 'longitud', 'string');
        $objRsm->addScalarResult('PUNTO_COBERTURA_ID', 'puntoCoberturaId', 'integer');
        $objRsm->addScalarResult('NOMBRE_JURISDICCION', 'puntoCobertura', 'string');
        $objRsm->addScalarResult('NOMBRE_CANTON', 'canton', 'string');
        $objRsm->addScalarResult('NOMBRE_PROVINCIA', 'provincia', 'string');
        $objRsm->addScalarResult('DIRECCION', 'direccion', 'string');
        $objRsm->addScalarResult('DESCRIPCION_PUNTO', 'descripcionPunto', 'string');
        $objRsm->addScalarResult('RAZON_SOCIAL', 'razonSocial', 'string');
        $objRsm->addScalarResult('NOMBRES', 'nombres', 'string');
        $objRsm->addScalarResult('APELLIDOS', 'apellidos', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('CASOS', 'numeroCasos', 'string');

        $objQuery->setSQL($strSql);

        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;
    }

    /**
     *
     * Método que devuelve las coberturas segun los parámetros asignados
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0
     * @since 18-04-2018
     *
     * Costo 17
     *
     * @param Array $arrayParametros [ 
     *                                intIdElemento,
     *                                strTipoElemento,
     *                                strEstado
     *                               ]
     * @return Array $arrayResultado
     */
    public function getCoberturaCliente($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strWhere = "";
        $strSql   = " SELECT DISTINCT
                        IP.PUNTO_COBERTURA_ID,
                        AJ.NOMBRE_JURISDICCION,
                        AC.ID_CANTON,
                        AC.NOMBRE_CANTON,
                        APROV.ID_PROVINCIA,
                        APROV.NOMBRE_PROVINCIA
                        FROM
                        DB_COMERCIAL.INFO_PUNTO IP 
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPEROL ON IP.PERSONA_EMPRESA_ROL_ID = IPEROL.ID_PERSONA_ROL 
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA IPER ON IPER.ID_PERSONA = IPEROL.PERSONA_ID
                        LEFT JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPEROL.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL 
                        LEFT JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL
                        LEFT JOIN DB_GENERAL.ADMI_TIPO_ROL ATR ON AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL
                        LEFT JOIN DB_COMERCIAL.ADMI_JURISDICCION AJ ON AJ.ID_JURISDICCION = IP.PUNTO_COBERTURA_ID
                        LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ON IOG.ID_OFICINA = AJ.OFICINA_ID
                        LEFT JOIN DB_COMERCIAL.ADMI_CANTON AC ON AC.ID_CANTON = IOG.CANTON_ID
                        LEFT JOIN DB_COMERCIAL.ADMI_PROVINCIA APROV ON APROV.ID_PROVINCIA = AC.PROVINCIA_ID
                        WHERE 
                        1=1 ";

        if(isset($arrayParametros["strCodEmpresa"]) && !empty($arrayParametros["strCodEmpresa"]))
        {
            $strWhere .= " AND IER.EMPRESA_COD = :strCodEmpresa ";
            $objQuery->setParameter("strCodEmpresa", $arrayParametros["strCodEmpresa"]);
        }

        if(isset($arrayParametros["intIdPersona"]) && $arrayParametros["intIdPersona"] > 0)
        {
            $strWhere .= " AND IPER.ID_PERSONA = :intIdPersona ";
            $objQuery->setParameter("intIdPersona", $arrayParametros["intIdPersona"]);
        }

        if(isset($arrayParametros["strRol"]) && !empty($arrayParametros["strRol"]))
        {
            $strWhere .= " AND ATR.DESCRIPCION_TIPO_ROL = :strRol ";
            $objQuery->setParameter("strRol", $arrayParametros["strRol"]);
        }

        if(isset($arrayParametros["strEstadoNotIn"]) && !empty($arrayParametros["strEstadoNotIn"]))
        {
            $strWhere .= " AND IP.ESTADO NOT IN (:strEstadoNotIn) ";
            $objQuery->setParameter("strEstadoNotIn", $arrayParametros["strEstadoNotIn"]);
        }

        $strSql .= $strWhere . ' ORDER BY IP.PUNTO_COBERTURA_ID ';

        $objRsm->addScalarResult('PUNTO_COBERTURA_ID', 'puntoCoberturaId', 'integer');
        $objRsm->addScalarResult('NOMBRE_JURISDICCION', 'puntoCobertura', 'string');
        $objRsm->addScalarResult('ID_CANTON', 'idCanton', 'integer');
        $objRsm->addScalarResult('NOMBRE_CANTON', 'canton', 'string');
        $objRsm->addScalarResult('ID_PROVINCIA', 'idProvincia', 'integer');
        $objRsm->addScalarResult('NOMBRE_PROVINCIA', 'provincia', 'string');

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;
    }


    /**
     *
     * Método que devuelve los puntos por empresa y cliente segun los parámetros recibidos
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0
     * @since 18-10-2018
     *
     *
     * @param Array $arrayParametros
     * @return Array $arrayResultado
     */
    public function findPtosPorEmpresaPorClientePorRolFilter($arrayParametros)
    {
        $strCampo = "a." . $arrayParametros['strCriteriaFilterPoint'];
        $objQuery = $this->_em->createQuery("
        SELECT a.id, a.login, a.descripcionPunto, b.razonSocial, b.nombres, b.apellidos, a.direccion, a.estado
        FROM
        schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, "
                . ($arrayParametros['esPadre'] ? " schemaBundle:InfoPuntoDatoAdicional ptoAd, " : "") .
        " schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d,
                        schemaBundle:AdmiRol f, schemaBundle:AdmiTipoRol g
        WHERE a.estado = 'Activo' AND "
                . ($arrayParametros['esPadre'] ? " a.id=ptoAd.puntoId AND ptoAd.esPadreFacturacion = :esPadre AND " : "") .
        " a.personaEmpresaRolId = c.id AND
        b.id = c.personaId AND
        c.empresaRolId = d.id AND "
         . " UPPER(" . $strCampo . ") like :nombre AND "   .
        " d.empresaCod = :codEmpresa AND
        b.id = :idCliente AND
                        d.rolId = f.id AND
                        f.tipoRolId = g.id AND
                        g.descripcionTipoRol = :rol");
        $objQuery->setParameter('codEmpresa', $arrayParametros['strCodEmpresa']);
        $objQuery->setParameter('idCliente', $arrayParametros['intIdCliente']);
        $objQuery->setParameter('rol', $arrayParametros['strRol']);
        if ($arrayParametros['esPadre'])
        {
            $objQuery->setParameter('esPadre', $arrayParametros['esPadre']);
        }
        $objQuery->setParameter('nombre', '%'.strtoupper($arrayParametros['strTextFilterPoint']).'%');
        $intTotal = count($objQuery->getResult());
        $arrayDatos = $objQuery->setFirstResult(0)->setMaxResults(9999999)->getResult();
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total'] = $intTotal;
        return $arrayResultado;
    }
    
    /**
     * Función que obtiene todos los correos usados como datos de envío para MD
     * Costo = 25
     * 
     * @param type $arrayParametros ["intIdPunto" => id del punto]
     * @return string
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 01-04-2019 Se agregan nuevo filtro para validar que un correo no sea nulo, adicional se agregan campos 
     *                         en retorno de respuesta (TIENESEPARADOR) para validar de mejor manera los correos del cliente según nuevas 
     *                         definiciones comerciales y poder contemplar todo tipo de información registrada como contacto del cliente
     *                         COSTO: 25
     * @since 1.0
     * 
     */
    public function getCorreosDatosEnvioMd($arrayParametros)
    {
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            $strSql             = "
                                    SELECT CONTACTO ,
                                     (instr(CONTACTO,';') +
                                      instr(CONTACTO,',' ))  TIENESEPARADOR
                                    FROM (
                                      (SELECT DISTINCT EMAIL_ENVIO AS CONTACTO,
                                        3                          AS ORDEN
                                      FROM
                                        (SELECT REGEXP_REPLACE(EMAIL_ENVIO,'(^[[:space:]]*|[[:space:]]*$)') AS EMAIL_ENVIO,
                                          ROW_NUMBER() OVER (ORDER BY EMAIL_ENVIO) rno
                                        FROM DB_COMERCIAL.INFO_PUNTO_DATO_ADICIONAL
                                        WHERE PUNTO_ID   = :intIdPunto
                                        AND EMAIL_ENVIO IS NOT NULL
                                        ORDER BY EMAIL_ENVIO
                                        )
                                      )
                                    UNION ALL
                                      (SELECT DISTINCT REGEXP_REPLACE(IPFC.VALOR,'(^[[:space:]]*|[[:space:]]*$)') AS CONTACTO,
                                        2                                                                         AS ORDEN
                                      FROM DB_COMERCIAL.INFO_PUNTO IP,
                                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                        DB_COMERCIAL.INFO_PERSONA IPR,
                                        DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC,
                                        DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC
                                      WHERE IP.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                                      AND IPER.PERSONA_ID             = IPR.ID_PERSONA
                                      AND IPR.ID_PERSONA              = IPFC.PERSONA_ID
                                      AND IPFC.FORMA_CONTACTO_ID      = AFC.ID_FORMA_CONTACTO
                                      AND IPFC.ESTADO                 = :strEstadoActivo
                                      AND AFC.ESTADO                  = :strEstadoActivo
                                      AND AFC.CODIGO                 IN
                                        (SELECT CODIGO
                                        FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO
                                        WHERE DESCRIPCION_FORMA_CONTACTO LIKE 'Correo%'
                                        )
                                      AND IP.ID_PUNTO = :intIdPunto
                                      )
                                    UNION ALL
                                      (SELECT DISTINCT REGEXP_REPLACE(IPFC.VALOR,'(^[[:space:]]*|[[:space:]]*$)') AS CONTACTO,
                                        1                                                                         AS ORDEN
                                      FROM DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPFC,
                                        DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC
                                      WHERE IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO
                                      AND AFC.CODIGO              IN
                                        (SELECT CODIGO
                                        FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO
                                        WHERE DESCRIPCION_FORMA_CONTACTO LIKE 'Correo%'
                                        )
                                      AND IPFC.ESTADO   = :strEstadoActivo
                                      AND AFC.ESTADO    = :strEstadoActivo
                                      AND IPFC.PUNTO_ID = :intIdPunto
                                      ) )
                                    WHERE CONTACTO IS NOT NULL
                                    GROUP BY CONTACTO
                                    ORDER BY MIN(ORDEN) ";
            $objRsm->addScalarResult('CONTACTO', 'strCorreo', 'string');
            $objRsm->addScalarResult('TIENESEPARADOR', 'intSeparador', 'integer');
            $objNtvQuery->setParameter('intIdPunto', $arrayParametros["intIdPunto"]);
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            $objNtvQuery->setSQL($strSql);
            $arrayResultadoCorreos = $objNtvQuery->getResult();
        }
        catch (\Exception $e) 
        {
            error_log("error al obtener correo de envío ".$e->getMessage());
            $arrayResultadoCorreos = array();
        }
        return $arrayResultadoCorreos;
    }

    /**
     * Método encargado de devolver todos los puntos que tengan contratados un grupo de producto específico.
     *
     * Costo 15
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 07-11-2018
     *
     * Costo 20
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 19-03-2019 - Se parámetriza el estado del servicio y el estado del punto, para poder obtener todo los clientes
     *                           indistinto de los parámetros mencionados.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 01-01-2019 - Se modifica el filtro por razón social, por motivos que se estaba filtrando por punto.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 28-06-2019 - Se filtra el parámetro intIdEmpresa en caso de no ser nulo.
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.4 26-01-2022 -se elimina filtro de estado en la tabla Info_persona.
     *
     * @param Array $arrayParametros [
     *                                  strLogin            : Login del cliente.
     *                                  strRazonSocial      : Razón Social del cliente.
     *                                  strPuntoCobertura   : Punto de cobertura.
     *                                  strProducto         : Descripción del producto.
     *                                  strOficina          : Oficina.
     *                                  arrayGrupoProducto  : Array de grupos de producto.
     *                                  arrayNombreTecnico  : Array de nombres tecnicos del producto.
     *                                  arrayEstadoServicio : Array de estados de los servicios.
     *                                  arrayEstadoPunto    : Array de estados de los puntos.
     *                               ]
     * @return Array $arrayResultado
     */
    public function getArrayPuntosClientesPorGrupoProducto($arrayParametros)
    {
        $arrayResultado = array();
        $strWhere       = '';

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            if (isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= 'AND PUNTO.LOGIN like (:strLogin) ';
                $objQuery->setParameter("strLogin", '%'.$arrayParametros['strLogin'].'%');
            }

            if (isset($arrayParametros['strRazonSocial']) && !empty($arrayParametros['strRazonSocial']))
            {
                $strWhere .= "AND ("
                                    . "UPPER(PERSONA.NOMBRES ||' '||PERSONA.APELLIDOS) LIKE (:strRazonSocial) "
                                    . "OR "
                                    . "UPPER(PERSONA.RAZON_SOCIAL) LIKE (:strRazonSocial)"
                               . ") ";

                $objQuery->setParameter("strRazonSocial", '%'.$arrayParametros['strRazonSocial'].'%');
            }

            if (isset($arrayParametros['strPuntoCobertura']) && !empty($arrayParametros['strPuntoCobertura']))
            {
                $strWhere .= 'AND JURISDICCION.NOMBRE_JURISDICCION like (:strPuntoCobertura) ';
                $objQuery->setParameter("strPuntoCobertura", '%'.$arrayParametros['strPuntoCobertura'].'%');
            }

            if (isset($arrayParametros['strProducto']) && !empty($arrayParametros['strProducto']))
            {
                $strWhere .= 'AND PRODUCTO.DESCRIPCION_PRODUCTO like (:strProducto) ';
                $objQuery->setParameter("strProducto", '%'.$arrayParametros['strProducto'].'%');
            }

            if (isset($arrayParametros['strOficina']) && !empty($arrayParametros['strOficina']))
            {
                $strWhere .= 'AND OFICINA.NOMBRE_OFICINA like (:strOficina) ';
                $objQuery->setParameter("strOficina", '%'.$arrayParametros['strOficina'].'%');
            }

            if (isset($arrayParametros['arrayGrupoProducto']) && !empty($arrayParametros['arrayGrupoProducto']))
            {
                $strWhere .= 'AND PRODUCTO.GRUPO in (:arrayGrupoProducto) ';
                $objQuery->setParameter('arrayGrupoProducto' , $arrayParametros['arrayGrupoProducto']);
            }

            if (isset($arrayParametros['arrayNombreTecnico']) && !empty($arrayParametros['arrayNombreTecnico']))
            {
                $strWhere .= 'AND PRODUCTO.NOMBRE_TECNICO in (:arrayNombreTecnico) ';
                $objQuery->setParameter('arrayNombreTecnico' , $arrayParametros['arrayNombreTecnico']);
            }

            if (isset($arrayParametros['arrayEstadoServicio']) && !empty($arrayParametros['arrayEstadoServicio']))
            {
                $strWhere .= 'AND SERVICIO.ESTADO IN (:arrayEstadoServicio) ';
                $objQuery->setParameter('arrayEstadoServicio' , $arrayParametros['arrayEstadoServicio']);
            }

            if (isset($arrayParametros['arrayEstadoPunto']) && !empty($arrayParametros['arrayEstadoPunto']))
            {
                $strWhere .= 'AND PUNTO.ESTADO IN (:arrayEstadoPunto) ';
                $objQuery->setParameter('arrayEstadoPunto' , $arrayParametros['arrayEstadoPunto']);
            }

            if (isset($arrayParametros['intIdEmpresa']) && !empty($arrayParametros['intIdEmpresa']))
            {
                $strWhere .= 'AND OFICINA.EMPRESA_ID = :intIdEmpresa ';
                $objQuery->setParameter('intIdEmpresa' , $arrayParametros['intIdEmpresa']);
            }

            $strSql = "SELECT PUNTO.LOGIN LOGIN,
                              SERVICIO.LOGIN_AUX LOGINAUXILIAR,
                              NVL(PERSONA.RAZON_SOCIAL,PERSONA.NOMBRES||' '||PERSONA.APELLIDOS) RAZON_SOCIAL,
                              JURISDICCION.NOMBRE_JURISDICCION PUNTO_COBERTURA,
                              PRODUCTO.DESCRIPCION_PRODUCTO PRODUCTO,
                              SERVICIO.DESCRIPCION_PRESENTA_FACTURA DESCRIPCION,
                              OFICINA.NOMBRE_OFICINA NOMBRE_OFICINA,
                              PERSONA.ESTADO ESTADOCLIENTE,
                              SERVICIO.ESTADO ESTADOSERVICIO,
                              PUNTO.ESTADO ESTADOPUNTO
                        FROM
                          DB_COMERCIAL.INFO_PUNTO               PUNTO,
                          DB_COMERCIAL.INFO_SERVICIO            SERVICIO,
                          DB_COMERCIAL.ADMI_PRODUCTO            PRODUCTO,
                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                          DB_COMERCIAL.INFO_EMPRESA_ROL         EMPRESA_ROL,
                          DB_COMERCIAL.ADMI_ROL                 ROL,
                          DB_COMERCIAL.INFO_PERSONA             PERSONA,
                          DB_COMERCIAL.INFO_OFICINA_GRUPO       OFICINA,
                          DB_COMERCIAL.ADMI_JURISDICCION        JURISDICCION
                        WHERE SERVICIO.PUNTO_ID              = PUNTO.ID_PUNTO
                          AND PRODUCTO.ID_PRODUCTO           = SERVICIO.PRODUCTO_ID
                          AND PERSONA_ROL.ID_PERSONA_ROL     = PUNTO.PERSONA_EMPRESA_ROL_ID
                          AND EMPRESA_ROL.ID_EMPRESA_ROL     = PERSONA_ROL.EMPRESA_ROL_ID
                          AND ROL.ID_ROL                     = EMPRESA_ROL.ROL_ID
                          AND PERSONA.ID_PERSONA             = PERSONA_ROL.PERSONA_ID
                          AND OFICINA.ID_OFICINA             = PERSONA_ROL.OFICINA_ID
                          AND JURISDICCION.ID_JURISDICCION   = PUNTO.PUNTO_COBERTURA_ID
                          AND ROL.DESCRIPCION_ROL            = :strDescripcionRol
                          AND PERSONA_ROL.ESTADO            IN (:strEstado)
                          AND EMPRESA_ROL.ESTADO            IN (:strEstado)
                          AND ROL.ESTADO                    IN (:strEstado)                    
                          AND OFICINA.ESTADO                IN (:strEstado)
                          AND JURISDICCION.ESTADO           IN (:strEstado)
                          $strWhere
                        ORDER BY PUNTO.LOGIN";

            $objQuery->setParameter('strEstado'         , $arrayParametros['strEstado']);
            $objQuery->setParameter('strDescripcionRol' , $arrayParametros['strDescripcionRol']);

            $objRsm->addScalarResult('LOGIN'           , 'login'          , 'string');
            $objRsm->addScalarResult('LOGINAUXILIAR'   , 'loginAuxiliar'  , 'string');
            $objRsm->addScalarResult('RAZON_SOCIAL'    , 'razonSocial'    , 'string');
            $objRsm->addScalarResult('NOMBRE_OFICINA'  , 'oficina'        , 'string');
            $objRsm->addScalarResult('PUNTO_COBERTURA' , 'puntoCobertura' , 'string');
            $objRsm->addScalarResult('PRODUCTO'        , 'producto'       , 'string');
            $objRsm->addScalarResult('DESCRIPCION'     , 'descripcion'    , 'string');
            $objRsm->addScalarResult('ESTADOCLIENTE'   , 'estadoCliente'  , 'string');
            $objRsm->addScalarResult('ESTADOSERVICIO'  , 'estadoServicio' , 'string');
            $objRsm->addScalarResult('ESTADOPUNTO'     , 'estadoPunto'    , 'string');

            $objQuery->setSQL($strSql);

            $arrayResult = $objQuery->getArrayResult();

            if (empty($arrayResult) || count($arrayResult) < 1)
            {
                $arrayResultado['status']  = 'fail';
                $arrayResultado['message'] = 'La consulta no retornó valores';
            }
            else
            {
                $arrayResultado['status'] = 'ok';
                $arrayResultado['total']  = count($arrayResult);
                $arrayResultado['result'] = $arrayResult;
            }
        }
        catch(\Exception $objException)
        {
            error_log('Error InfoPuntoRepository.getArrayPuntosClientesPorGrupoProducto => '.$objException->getMessage());
            $arrayResultado['status']  = 'fail';
            $arrayResultado['message'] = 'Error al obtener los puntos clientes';
        }
        return $arrayResultado;
    }

    /**
     * Función encargada de devolver todas las tareas creadas de un cliente.
     *
     * Costo 60
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 16-07-2019
     *
     * @param Array $arrayParametros [
     *                                  strLogin    : Login del cliente.
     *                                  strEstado   : Estado de la tarea.
     *                                  strFechaIni : Fecha de creación inicio de la tarea en formato (Año-Mes-Día).
     *                                  strFechaFin : Fecha de creación fin de la tarea en formato (Año-Mes-Día).
     *                               ]
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.1 18-03-2020 - Se agrega el campo departamento y campo empleado.
     *
     * @return Array $arrayResultado
     */
    public function getTareasClientes($arrayParametros)
    {
        $strWhere             = '';
        $strSelect            = '';
        $boolMostrarAsignados = false;
        $boolMostrarTareasAbiertas = false;

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            

            if (isset($arrayParametros['strVerTareasValidas']) && 
                !empty($arrayParametros['strVerTareasValidas']) && 
                $arrayParametros['strVerTareasValidas'] == 'S')
                {
                    $boolMostrarTareasAbiertas = true;
                }

            if (isset($arrayParametros['strMostrarAsignado']) && 
                !empty($arrayParametros['strMostrarAsignado']) && 
                $arrayParametros['strMostrarAsignado'] == 'S')
                {
                    $boolMostrarAsignados = true;
                }

            if (isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= "AND LOWER(IPUN.LOGIN) = LOWER(:strLogin) "; 
                $objQuery->setParameter("strLogin", $arrayParametros['strLogin']);
            }

            if (isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strWhere .= "AND LOWER(IDHI.ESTADO) = LOWER(:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros['strEstado']);
            }
            else
            {
                if($boolMostrarTareasAbiertas)
                {
                    $strWhere .= "AND IDHI.ESTADO NOT IN (:strEstadoFinalizado,:strEstadoCancelada,:strEstadoRechazada) ";
                    $objQuery->setParameter("strEstadoFinalizado", 'Finalizada');
                    $objQuery->setParameter("strEstadoCancelada",  'Cancelada');
                    $objQuery->setParameter("strEstadoRechazada",  'Rechazada');
                }
            }

            if (isset($arrayParametros['strFechaIni']) && !empty($arrayParametros['strFechaIni']))
            {
                $strWhere .= "AND TO_CHAR(IDET.FE_CREACION,'RRRR-MM-DD') >= :strFechaIni ";
                $objQuery->setParameter("strFechaIni", $arrayParametros['strFechaIni']);
            }

            if (isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']))
            {
                $strWhere .= "AND TO_CHAR(IDET.FE_CREACION,'RRRR-MM-DD') <= :strFechaFin ";
                $objQuery->setParameter("strFechaFin", $arrayParametros['strFechaFin']);
            }

            if($boolMostrarAsignados)
            {
                $strSelect = ",(SELECT ASIGNADO_NOMBRE FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION WHERE DETALLE_ID = IDET.ID_DETALLE
                                and id_detalle_asignacion = (SELECT max(id_detalle_asignacion) 
                                FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION WHERE DETALLE_ID = IDET.ID_DETALLE)) DEPARTAMENTO,
                                (SELECT REF_ASIGNADO_NOMBRE FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION WHERE DETALLE_ID = IDET.ID_DETALLE
                                and id_detalle_asignacion = (SELECT max(id_detalle_asignacion) 
                                FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION WHERE DETALLE_ID = IDET.ID_DETALLE)) EMPLEADO,
                                IDET.OBSERVACION ";
            }


                $strSql = "SELECT NVL(IPER.RAZON_SOCIAL,IPER.APELLIDOS||' '||IPER.NOMBRES) AS RAZONSOCIAL, ".
                             "IPUN.LOGIN            AS LOGIN, ".
                             "ICOM.ID_COMUNICACION  AS NUMEROTAREA, ".
                             "ATAR.NOMBRE_TAREA     AS NOMBRETAREA, ".
                             "APRO.NOMBRE_PROCESO   AS NOMBREPROCESO, ".
                             "TO_CHAR(IDET.FE_CREACION,'RRRR-MM-DD HH24:MI') AS FECHACREACION, ".
                             "TO_CHAR(IDHI.FE_CREACION,'RRRR-MM-DD HH24:MI') AS FECHAESTADO, ".
                             "IDHI.ESTADO AS ESTADO, ".
                             "CAST((CAST(IDHI.FE_CREACION AS DATE) - ".
                                   "CAST(IDET.FE_CREACION AS DATE))*24*60 ".
                             "AS INTEGER) AS TIEMPOMINUTOS, ".
                             "NVL(UPPER(IDET.ES_SOLUCION),'N') AS ESSOLUCION ".
                             $strSelect.
                       "FROM DB_SOPORTE.INFO_DETALLE               IDET, ".
                            "DB_SOPORTE.INFO_DETALLE_HISTORIAL     IDHI, ".
                            "DB_COMUNICACION.INFO_COMUNICACION     ICOM, ".
                            "DB_SOPORTE.INFO_PARTE_AFECTADA        IPAF, ".
                            "DB_SOPORTE.ADMI_TAREA                 ATAR, ".
                            "DB_SOPORTE.ADMI_PROCESO               APRO, ".
                            "DB_COMERCIAL.INFO_PUNTO               IPUN, ".
                            "DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPERO, ".
                            "DB_COMERCIAL.INFO_PERSONA             IPER ".
                       "WHERE IDET.ID_DETALLE           = IDHI.DETALLE_ID ".
                         "AND IDET.ID_DETALLE           = ICOM.DETALLE_ID ".
                         "AND IDET.ID_DETALLE           = IPAF.DETALLE_ID ".
                         "AND IDET.TAREA_ID             = ATAR.ID_TAREA ".
                         "AND ATAR.PROCESO_ID           = APRO.ID_PROCESO ".
                         "AND IPUN.ID_PUNTO             = IPAF.AFECTADO_ID ".
                         "AND IPERO.ID_PERSONA_ROL      = IPUN.PERSONA_EMPRESA_ROL_ID ".
                         "AND IPERO.PERSONA_ID          = IPER.ID_PERSONA ".
                         "AND ICOM.ID_COMUNICACION      = ".
                               "(SELECT MIN(ICOMMIN.ID_COMUNICACION) ".
                                   "FROM DB_COMUNICACION.INFO_COMUNICACION ICOMMIN ".
                                "WHERE ICOMMIN.DETALLE_ID = IDET.ID_DETALLE) ".
                         "AND IDHI.ID_DETALLE_HISTORIAL = ".
                               "(SELECT MAX(MAXIDHIS.ID_DETALLE_HISTORIAL) ".
                                   "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL MAXIDHIS ".
                                "WHERE MAXIDHIS.DETALLE_ID = IDET.ID_DETALLE) ".
                         "AND LOWER(IPAF.TIPO_AFECTADO) = LOWER(:strTipoAfectado) ".
                         "$strWhere".
                       "ORDER BY IDET.FE_CREACION ASC";

            $objQuery->setParameter('strTipoAfectado' , 'cliente');

            $objRsm->addScalarResult('RAZONSOCIAL'   , 'razonSocial'   , 'string');
            $objRsm->addScalarResult('LOGIN'         , 'login'         , 'string');
            $objRsm->addScalarResult('NUMEROTAREA'   , 'numeroTarea'   , 'integer');
            $objRsm->addScalarResult('NOMBRETAREA'   , 'nombreTarea'   , 'string');
            $objRsm->addScalarResult('NOMBREPROCESO' , 'nombreProceso' , 'string');
            $objRsm->addScalarResult('FECHACREACION' , 'fechaCreacion' , 'string');
            $objRsm->addScalarResult('FECHAESTADO'   , 'fechaEstado'   , 'string');
            $objRsm->addScalarResult('ESTADO'        , 'estado'        , 'string');
            $objRsm->addScalarResult('TIEMPOMINUTOS' , 'tiempoMinutos' , 'integer');
            $objRsm->addScalarResult('ESSOLUCION'    , 'esSolucion'    , 'string');
            $objRsm->addScalarResult('ESSOLUCION'    , 'esSolucion'    , 'string');

            if($boolMostrarAsignados)
            {
                $objRsm->addScalarResult('DEPARTAMENTO'    , 'nombreDepartamento' , 'string');
                $objRsm->addScalarResult('EMPLEADO'        , 'empleado'           , 'string');
                $objRsm->addScalarResult('OBSERVACION'     , 'observacion'        , 'string');
            }
            
            $objQuery->setSQL($strSql);

            $arrayResult = $objQuery->getArrayResult();
            $intTotal    = count($arrayResult);

            if (empty($arrayResult) || $intTotal < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            $arrayResultado = array('status' => 'ok',
                                    'total'  => $intTotal,
                                    'result' => $arrayResult);
        }
        catch(\Exception $objException)
        {
            $arrayResultado = array('status'  => 'fail',
                                    'message' => $objException->getMessage());
        }
        return $arrayResultado;
    }
    
    /**
     * Método encargado de devolver todos los puntos de un cliente según los parámetros enviados
     * Costo: 18
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-03-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 22-04-2019 Se agrega columnas en el grid de servicios TelcoHome con información del proceso masivo que está ejecutándose, 
     *                          para que el equipo de soporte pueda revisar rápidamente dicha ejecución en caso de que existiera algún error.
     *                          Costo: 415
     * 
     * @param Array $arrayParametros [
     *                                  'strCodEmpresa'         => id de la empresa
     *                                  'strLogin'              => login de un punto,
     *                                  'strLoginFact'          => padre de facturación,
     *                                  'intIdPersona'          => id de la persona del cliente
     *                                  'strDescripcionRol'     => rol del cliente
     *                                  'strNombreTecnico'      => nombre técnico de un producto,
     *                                  'arrayEstadoServicios'  => arreglo de estados de servicios,
     *                                  'arrayEstadosPunto'     => arreglo de estados de puntos,
     *                               ]
     * 
     * @return Array $arrayRespuesta['total', 'registros']
     */
    public function getPuntosClienteByCriterios($arrayParametros)
    {
        $arrayRespuesta['total']        = 0;
        $arrayRespuesta['registros']    = array();
        $strWhere                       = '';

        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);

            if(isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= 'AND PUNTO.LOGIN like (:strLogin) ';
                $objNtvQuery->setParameter("strLogin", '%'.$arrayParametros['strLogin'].'%');
            }
            
            if(isset($arrayParametros['strLoginFact']) && !empty($arrayParametros['strLoginFact']))
            {
                $strWhere .= 'AND PUNTO_FACTURACION.LOGIN like (:strLoginFact) ';
                $objNtvQuery->setParameter("strLoginFact", '%'.$arrayParametros['strLoginFact'].'%');
            }
            
            if(isset($arrayParametros['strNombreTecnico']) && !empty($arrayParametros['strNombreTecnico']))
            {
                $strWhere .= 'AND PRODUCTO.NOMBRE_TECNICO = :strNombreTecnico ';
                $objNtvQuery->setParameter('strNombreTecnico' , $arrayParametros['strNombreTecnico']);
            }
            
            if(isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona']))
            {
                $strWhere .= 'AND PERSONA.ID_PERSONA = :intIdPersona ';
                $objNtvQuery->setParameter('intIdPersona' , $arrayParametros['intIdPersona']);
            }
            
            if(isset($arrayParametros['arrayEstadosPunto']) && !empty($arrayParametros['arrayEstadosPunto']))
            {
                $strWhere .= 'AND PUNTO.ESTADO IN (:arrayEstadosPunto) ';
                $objNtvQuery->setParameter('arrayEstadosPunto' , $arrayParametros['arrayEstadosPunto']);
            }
            
            if(isset($arrayParametros['arrayEstadoServicios']) && !empty($arrayParametros['arrayEstadoServicios']))
            {
                $strWhere .= 'AND SERVICIO.ESTADO IN (:arrayEstadoServicios) ';
                $objNtvQuery->setParameter('arrayEstadoServicios' , $arrayParametros['arrayEstadoServicios']);
            }
            
            if(isset($arrayParametros['strEstadoServicio']) && !empty($arrayParametros['strEstadoServicio']))
            {
                $strWhere .= 'AND SERVICIO.ESTADO = :strEstadoServicio ';
                $objNtvQuery->setParameter('strEstadoServicio' , $arrayParametros['strEstadoServicio']);
            }

            $strSelect      = " SELECT PUNTO.ID_PUNTO, PUNTO.LOGIN, SERVICIO.ID_SERVICIO, SERVICIO.ESTADO, PUNTO.DIRECCION, PUNTO.NOMBRE_PUNTO NOMBRE,
                                CASE WHEN PUNTO_FACTURACION.LOGIN IS NULL 
                                THEN 'NA' ELSE PUNTO_FACTURACION.LOGIN END LOGIN_FACT,
                                (SELECT PMD.ID_PROCESO_MASIVO_DET
                                    FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET PMD
                                    WHERE PMD.SERVICIO_ID = SERVICIO.ID_SERVICIO
                                    AND PMD.ESTADO     IN (:arrayEstadosProcesosDet)
                                    AND ROWNUM         = 1) AS ID_PROCESO_MASIVO_DET,
                                NVL((SELECT PMD.ESTADO
                                    FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET PMD
                                    WHERE PMD.SERVICIO_ID = SERVICIO.ID_SERVICIO
                                    AND PMD.ESTADO     IN (:arrayEstadosProcesosDet)
                                    AND ROWNUM         = 1),'') AS ESTADO_PROCESO_MASIVO_DET ";
            $strSelectCount = " SELECT COUNT(DISTINCT SERVICIO.ID_SERVICIO) AS TOTAL ";
            $strFromWhere   = " FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_ROL
                                INNER JOIN DB_COMERCIAL.INFO_PERSONA PERSONA
                                ON PERSONA.ID_PERSONA = PERSONA_ROL.PERSONA_ID
                                INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
                                ON PERSONA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                                INNER JOIN DB_COMERCIAL.INFO_SERVICIO SERVICIO
                                ON SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO
                                INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
                                ON PRODUCTO.ID_PRODUCTO = SERVICIO.PRODUCTO_ID
                                INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO_FACTURACION
                                ON PUNTO_FACTURACION.ID_PUNTO = SERVICIO.PUNTO_FACTURACION_ID
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL
                                ON EMPRESA_ROL.ID_EMPRESA_ROL = PERSONA_ROL.EMPRESA_ROL_ID
                                INNER JOIN DB_GENERAL.ADMI_ROL ROL
                                ON ROL.ID_ROL = EMPRESA_ROL.ROL_ID
                                LEFT JOIN DB_COMERCIAL.INFO_PERSONA PERSONA_COBRANZAS
                                ON PERSONA_COBRANZAS.LOGIN = PUNTO.USR_COBRANZAS
                                WHERE EMPRESA_ROL.EMPRESA_COD = :strCodEmpresa
                                AND ROL.DESCRIPCION_ROL       = :strDescripcionRol
                                AND PERSONA_ROL.ESTADO        IN (:arrayEstadoPer)
                                AND EMPRESA_ROL.ESTADO        IN (:arrayEstadoEr)
                                AND ROL.ESTADO                IN (:arrayEstadoEr) ";
            
            $strSql         = $strSelect.$strFromWhere.$strWhere;
            
            $objNtvQuery->setParameter('arrayEstadosProcesosDet', array('Pendiente','Fallo'));
            $objNtvQuery->setParameter('arrayEstadoPer'         , array('Activo', 'Pendiente'));
            $objNtvQuery->setParameter('arrayEstadoEr'          , array('Activo', 'Modificado'));
            $objNtvQuery->setParameter('strDescripcionRol'      , $arrayParametros['strDescripcionRol']);
            $objNtvQuery->setParameter('strCodEmpresa'          , $arrayParametros['strCodEmpresa']);
            
            $objRsm->addScalarResult('ID_PUNTO',                    'idPunto',                  'integer');
            $objRsm->addScalarResult('ID_SERVICIO',                 'idServicio',               'integer');
            $objRsm->addScalarResult('ID_PROCESO_MASIVO_DET',       'idProcesoMasivoDet',       'integer');
            $objRsm->addScalarResult('ESTADO_PROCESO_MASIVO_DET',   'estadoProcesoMasivoDet',   'string');
            $objRsm->addScalarResult('LOGIN',                       'login',                    'string');
            $objRsm->addScalarResult('LOGIN_FACT',                  'loginFact',                'string');
            $objRsm->addScalarResult('ESTADO',                      'estado',                   'string');
            $objRsm->addScalarResult('DIRECCION',                   'direccion',                'string');
            $objRsm->addScalarResult('NOMBRE',                      'nombre',                   'string');
            $objRsm->addScalarResult('TOTAL',                       'total',                    'integer');

            $objNtvQuery->setSQL($strSql);
            
            
            $arrayResultado = $objNtvQuery->getResult();
            
            $strSqlCount    = $strSelectCount.$strFromWhere.$strWhere;
            $objNtvQuery->setSQL($strSqlCount);
            
            $intTotal       = $objNtvQuery->getSingleScalarResult();

            $arrayRespuesta['registros']   = $arrayResultado;
            $arrayRespuesta['total']        = $intTotal;
        }
        catch(\Exception $e)
        {
             print_r($e->getMessage());
        }
        return $arrayRespuesta;
    }
    /**
     * Función encargada de devolver todos los servicios del punto que tenga la característica de konibit.
     *
     * Costo: 15
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 02-12-2019
     *
     * @param Array $arrayParametros [
     *                                 arrayEstadosServicio : Lista de estados del servicio.
     *                                 intIdPunto           : Id del punto.
     *                                 strEstadoProdCaract  : Estado del producto característica.
     *                                 strDescripcionCaract : Descripción de la característica.
     *                                 strUsuario           : Usuario quien realiza la petición.
     *                                 strIp                : Ip del usuario quien realiza la petición.
     *                                 objUtilService       : Objeto del service Util.
     *                               ]
     *
     * @return Array $arrayRespuesta['result']
     */
    public function getServiciosProductoKonibit($arrayParametros)
    {
        $objUtilService = $arrayParametros['objUtilService'];
        $strUsuario     = $arrayParametros['strUsuario'] ? $arrayParametros['strUsuario'] : 'Telcos+';
        $strIp          = $arrayParametros['strIp']      ? $arrayParametros['strIp']      : '127.0.0.1';

        try
        {
            $objRsm      = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT INPU.ID_PUNTO    AS ID_PUNTO, ".
                             "INPU.LOGIN       AS LOGIN, ".
                             "INSE.ID_SERVICIO AS ID_SERVICIO, ".
                             "INSE.ESTADO      AS ESTADO_SERVICIO, ".
                             "ADPR.DESCRIPCION_PRODUCTO       AS DESCRIPCION_PRODUCTO, ".
                             "ADCA.DESCRIPCION_CARACTERISTICA AS DESCRIPCION_CARACTERISTICA ".
                        "FROM DB_COMERCIAL.INFO_PUNTO                   INPU, ".
                             "DB_COMERCIAL.INFO_SERVICIO                INSE, ".
                             "DB_COMERCIAL.ADMI_PRODUCTO                ADPR, ".
                             "DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADPRCA, ".
                             "DB_COMERCIAL.ADMI_CARACTERISTICA          ADCA ".
                      "WHERE INPU.ID_PUNTO             = INSE.PUNTO_ID ".
                        "AND INSE.PRODUCTO_ID          = ADPR.ID_PRODUCTO ".
                        "AND ADPR.ID_PRODUCTO          = ADPRCA.PRODUCTO_ID ".
                        "AND ADPRCA.CARACTERISTICA_ID  = ADCA.ID_CARACTERISTICA ".
                        "AND INPU.ID_PUNTO             = :intIdPunto ".
                        "AND UPPER(INSE.ESTADO)       IN (:arrayEstadosServicio) ".
                        "AND UPPER(ADPRCA.ESTADO)      = :strEstadoProdCaract ".
                        "AND UPPER(ADCA.DESCRIPCION_CARACTERISTICA) = :strDescripcionCaract";

            $objNtvQuery->setParameter('intIdPunto'           , $arrayParametros['intIdPunto']);
            $objNtvQuery->setParameter('arrayEstadosServicio' , array_map('strtoupper', $arrayParametros['arrayEstadosServicio']));
            $objNtvQuery->setParameter('strEstadoProdCaract'  , strtoupper($arrayParametros['strEstadoProdCaract']));
            $objNtvQuery->setParameter('strDescripcionCaract' , strtoupper($arrayParametros['strDescripcionCaract']));

            $objRsm->addScalarResult('ID_PUNTO'                   , 'idPunto'                   , 'integer');
            $objRsm->addScalarResult('LOGIN'                      , 'login'                     , 'string');
            $objRsm->addScalarResult('ID_SERVICIO'                , 'idServicio'                , 'integer');
            $objRsm->addScalarResult('ESTADO_SERVICIO'            , 'estadoServicio'            , 'string');
            $objRsm->addScalarResult('DESCRIPCION_PRODUCTO'       , 'descripcionProducto'       , 'string');
            $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA' , 'descripcionCaracteristica' , 'string');

            $objNtvQuery->setSQL($strSql);

            $arrayRespuesta = array ('result' => $objNtvQuery->getResult());
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta = array ('result' => null);

            if (is_object($objUtilService))
            {
                $objUtilService->insertError('Telcos+',
                                             'InfoPuntoRepository->getServiciosProductoKonibit',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);
            }
        }
        return $arrayRespuesta;
    }

    /*
     * Función que crea el Job para la ejecución del proceso automático de
     * reingreso de orden de servicio.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 26-08-2019
     *
     * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
     * @return Array $arrayRespuesta
     */
    public function jobReingresoAutomatico($arrayParametros)
    {
        $intIdServicio  = $arrayParametros['intIdServicio'];
        $serviceUtil    = $arrayParametros['serviceUtil'];
        $strUserSession = $arrayParametros['strUsuario'];
        $strIpSession   = $arrayParametros['strIp'];

        try
        {
            $arrayParametros['serviceUtil'] = null;
            $strJson = json_encode($arrayParametros);
          
            $strSqlJ = "DECLARE
                            Lv_idServicio VARCHAR(50) := '$intIdServicio';
                        BEGIN
                            DBMS_SCHEDULER.CREATE_JOB(job_name   => '\"DB_COMERCIAL\".\"JOB_REINGRESO_OS_'||Lv_idServicio||'\"',
                                                      job_type   => 'PLSQL_BLOCK',
                                                      job_action => '
                                                        DECLARE
                                                            Lv_Mensaje VARCHAR2(3000);
                                                        BEGIN
                                                            DB_COMERCIAL.CMKG_REINGRESO.P_REINGRESO_ORDEN_SERVICIO(
                                                                Pcl_Json   => ''$strJson'',
                                                                Pv_Mensaje => Lv_Mensaje);
                                                        END;',
                                                      number_of_arguments => 0,
                                                      start_date          => NULL,
                                                      repeat_interval     => NULL,
                                                      end_date            => NULL,
                                                      enabled             => FALSE,
                                                      auto_drop           => TRUE,
                                                      comments            => 'Proceso para ejecutar el reingreso de orden de servicio automática.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_COMERCIAL\".\"JOB_REINGRESO_OS_'||Lv_idServicio||'\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_COMERCIAL\".\"JOB_REINGRESO_OS_'||Lv_idServicio||'\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSqlJ);
            $objStmt->execute();

            $arrayRespuesta = array ('status'  => true,
                                     'message' => 'El proceso se encuentra ejecutándose, será notificado cuando concluya.');
        }
        catch (\Exception $objException)
        {
            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('Telcos+',
                                          'InfoPuntoRepository->jobReingresoAutomatico',
                                           substr($objException->getMessage(), 0, 4000),
                                           $strUserSession,
                                           $strIpSession);
            }

            $arrayRespuesta = array ('status'  => false,
                                     'message' => 'Error al crear el proceso. <br/>'.
                                                  'Si el problema persiste, por favor comunicar a Sistemas.');
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de devolver la cantidad total de puntos y servicios de n clientes
     * Costo: 12
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 29-11-2019
     *
     * @param array $arrayParametros [
     *                                  'arrayIdClientes' => Id de clientes
     *                               ]
     *
     * @throws $objException
     * @return array $arrayResultado['intCantidadClientes', 'intCantidadPuntos', 'intCantidadServicios']
     */
    public function getTotalPuntosPorCliente($arrayParametros)
    {
        $strSelect  = '';
        $strFrom    = '';
        $strWhere   = '';
        $strOrderBy = '';
        $arrayResultado = array();

        try
        {

            $objRsmb = new ResultSetMappingBuilder($this->getEntityManager());
            $objQuery = $this->getEntityManager()->createNativeQuery(null, $objRsmb);

            $strSelect = ' SELECT iper.intCantidadClientes, ip.intCantidadPuntos, iser.intCantidadServicios ';
            $strFrom = ' FROM 
                       (
                         SELECT COUNT(iper.id_persona_rol) as intCantidadClientes
                             FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper
                             WHERE iper.id_persona_rol IN (:arrayIdClientes)
                       ) iper, 
                       (
                         SELECT COUNT(ip.id_punto) as intCantidadPuntos
                             FROM DB_COMERCIAL.INFO_PUNTO ip
                             WHERE ip.persona_empresa_rol_id IN (:arrayIdClientes)
                       ) ip, 
                       (
                         SELECT COUNT(iser.id_servicio) as intCantidadServicios
                             FROM DB_COMERCIAL.INFO_PUNTO ip 
                             INNER JOIN DB_COMERCIAL.INFO_SERVICIO iser
                                 ON ip.id_punto = iser.punto_id 
                             WHERE ip.persona_empresa_rol_id IN (:arrayIdClientes)
                       ) iser ';

            $strSql = $strSelect . $strFrom . $strWhere . $strOrderBy;

            $objRsmb->addScalarResult('INTCANTIDADCLIENTES', 'intCantidadClientes', 'integer');
            $objRsmb->addScalarResult('INTCANTIDADPUNTOS', 'intCantidadPuntos', 'integer');
            $objRsmb->addScalarResult('INTCANTIDADSERVICIOS', 'intCantidadServicios', 'integer');

            $objQuery->setParameter('arrayIdClientes', $arrayParametros['arrayIdClientes']);
            $objQuery->setSQL($strSql);

            $arrayResultado = $objQuery->getSingleResult();
        }
        catch(\Exception $objException)
        {
           throw $objException;
        }

        return $arrayResultado;
    }
    
     /**
     * Método que obtiene el Contrato del cliente. 
     *  
     * @author: Josselhin Moreira Q. <kjmoreira@telconet.ec>
     * @version 1.0 11-06-2019 
     * costoQuery = 14
     *
     * @author: Gustavo Narea <gnarea<@telconet.ec>
     * @version 1.1 17-02-2021 - Se agrega filtro para busqueda del contrato por punto
     * costoQuery = 12
     *  
     * @param  $arrayParametros[ $arrayParametros]
     * @return $arrayResultado
     */
    public function obtenerInformacionContrato($arrayParametros)
    {
        $intIdPersona       =  $arrayParametros["id_cliente"];
        $intIdPunto         = $arrayParametros["id_punto"];

        $strSql        =   "SELECT IC.ID_CONTRATO AS CONTRATO FROM DB_COMERCIAL.INFO_CONTRATO IC 
                               INNER JOIN DB_COMERCIAL.INFO_PUNTO IPT ON IPT.PERSONA_EMPRESA_ROL_ID = IC.PERSONA_EMPRESA_ROL_ID 
                               WHERE IC.PERSONA_EMPRESA_ROL_ID IN (
                               SELECT IPR.ID_PERSONA_ROL FROM DB_COMERCIAL.INFO_PERSONA  IP, 
                             DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPR ,
                             DB_COMERCIAL.INFO_EMPRESA_ROL IER, DB_COMERCIAL.ADMI_ROL AR, DB_COMERCIAL.ADMI_TIPO_ROL ATR
                             WHERE IPR.PERSONA_ID =  :intIdPersona 
                             AND IP.ID_PERSONA = IPR.PERSONA_ID
                             AND IPR.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                             AND IER.ROL_ID = AR.ID_ROL
                             AND AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL
                             AND LOWER(ATR.DESCRIPCION_TIPO_ROL) = LOWER('cliente') )
                            AND IPT.ID_PUNTO = :intIdPunto"; 
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter('intIdPersona', $intIdPersona);
        $objQuery->setParameter('intIdPunto', $intIdPunto);
        $objRsm->addScalarResult('CONTRATO','CONTRATO','string');
  
        
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'getVerificarEstadoTareaInternaPorInterface'.
     *
     * Obtiene las tareas internas que no se encuentren en los estados finalizados por la interface del elemento
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 13-07-2020
     *
     * @param Array $arrayParametros [
     *                                  strEstado           => estado
     *                                  strIdEmpresa        => id de la empresa
     *                                  intIdTarea          => id de la tarea
     *                                  strNombreElemento   => nombre del elemento
     *                                  strNombreInterface  => nombre de la interface
     *                                  strNombreParametro  => nombre del parametro para los estados
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'result'    => resultado de la información de la tarea
     *                               ]
     *
     * costoQuery: 635
     */
    public function getVerificarEstadoTareaInternaPorInterface($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strSql          = "SELECT COM.ID_COMUNICACION, DET.ID_DETALLE, DET.TAREA_ID, DET.OBSERVACION
                                FROM
                                    DB_COMUNICACION.INFO_COMUNICACION COM
                                INNER JOIN DB_SOPORTE.INFO_DETALLE    DET ON DET.ID_DETALLE = COM.DETALLE_ID
                                WHERE COM.ESTADO        = :ESTADO
                                    AND COM.EMPRESA_COD = :ID_EMPRESA
                                    AND DET.TAREA_ID    = :TAREA_ID
                                    AND NOT EXISTS (
                                        SELECT 1 FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL HIST
                                        WHERE HIST.DETALLE_ID = DET.ID_DETALLE
                                            AND EXISTS
                                            (
                                                SELECT 1 FROM DB_GENERAL.ADMI_PARAMETRO_DET PAR_EST
                                                WHERE PAR_EST.PARAMETRO_ID = (
                                                    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                    WHERE NOMBRE_PARAMETRO = :NOMBRE_PARAMETRO AND ESTADO = :ESTADO
                                                    AND ROWNUM = 1 )
                                                AND PAR_EST.ESTADO = :ESTADO AND PAR_EST.VALOR1 = HIST.ESTADO
                                            )
                                    )
                                    AND DET.OBSERVACION LIKE :NOMBRE_ELEMENTO
                                    AND DET.OBSERVACION LIKE :NOMBRE_INTERFACE
                                ORDER BY COM.ID_COMUNICACION DESC";

            $objNativeQuery->setParameter("ESTADO",           $arrayParametros['strEstado']);
            $objNativeQuery->setParameter("ID_EMPRESA",       $arrayParametros['strIdEmpresa']);
            $objNativeQuery->setParameter("TAREA_ID",         $arrayParametros['intIdTarea']);
            $objNativeQuery->setParameter("NOMBRE_ELEMENTO",  '%'.$arrayParametros['strNombreElemento'].'%');
            $objNativeQuery->setParameter("NOMBRE_INTERFACE", '%'.$arrayParametros['strNombreInterface'].'%');
            $objNativeQuery->setParameter("NOMBRE_PARAMETRO", $arrayParametros['strNombreParametro']);

            $objResultSetMap->addScalarResult('ID_COMUNICACION', 'idComunicacion', 'integer');
            $objResultSetMap->addScalarResult('ID_DETALLE',      'idDetalle',      'integer');
            $objResultSetMap->addScalarResult('TAREA_ID',        'idTarea',        'integer');
            $objResultSetMap->addScalarResult('OBSERVACION',     'strObservacion', 'string');

            $objNativeQuery->setSQL($strSql);
            $arrayData = $objNativeQuery->getOneOrNullResult();

            $arrayResultado = array(
                'status' => 'OK',
                'result' => $arrayData
            );
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }

        return $arrayResultado;
    }


    /**
     * Documentación para el método 'findPuntosPorClientePaginado'.
     *
     * Obtiene los puntos del clientes de acuerdo a la paginación indicada
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 31-08-2020
     *
     * @throws \Doctrine\ORM\Query\QueryException
     * costoQuery: 199
     *
     */
    public function findPuntosPorClientePaginado($arrayParametros)
    {
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'];
        $intIdPersona            = $arrayParametros['intIdPersona'];
        $strLogin                = $arrayParametros['strLogin'];
        $strDireccion            = $arrayParametros['strDireccion'];
        $strCiudad               = $arrayParametros['strCiudad'];
        $strRol                  = $arrayParametros['strRol'];
        $intPagina               = $arrayParametros['intPagina'];
        $intLimite               = $arrayParametros['intLimite'];
        $boolPaginado            = $arrayParametros['activaPaginacionPuntos'];
        $arrayEstadosPuntosTotal = $arrayParametros['arrayEstadosPuntosTotal'];

        if($boolPaginado)
        {
            if(is_null($intLimite) || $intLimite <= 0)
            {
                $intLimite = 10;
            }

            if(!is_null($intPagina) && !empty($intPagina))
            {
                $intInicio = (($intPagina - 1) * $intLimite);
            }
            else
            {
                $intInicio = 0;
            }
        }
        else
        {
            $intLimite = null;
            $intInicio = null;
        }

        $objQuery = $this->getEntityManager()->createQuery();
        $objQueryCount = $this->getEntityManager()->createQuery();

        $strSelectCount = "SELECT count(a.id) ";

        $strSelect = "SELECT a.id, 
                             a.login, 
                             a.descripcionPunto, 
                             b.razonSocial, 
                             b.nombres, 
                             b.apellidos, 
                             a.direccion, 
                             a.estado ";

        $strFrom = " FROM schemaBundle:InfoPunto a, 
                          schemaBundle:InfoPersona b,  
                          schemaBundle:InfoPersonaEmpresaRol c, 
                          schemaBundle:InfoEmpresaRol d,
                          schemaBundle:AdmiRol f, 
                          schemaBundle:AdmiTipoRol g ";
        $strWhere = " WHERE a.personaEmpresaRolId = c.id 
                            AND b.id = c.personaId 
                            AND c.empresaRolId = d.id 
                            AND b.id = :intIdPersona 
                            AND d.rolId = f.id 
                            AND f.tipoRolId = g.id 
                            AND d.empresaCod = :strCodEmpresa 
                            AND g.descripcionTipoRol = :strRol  ";

        $strOrderBy = " ORDER BY a.feCreacion DESC ";

        if (!is_null($strLogin) && !empty($strLogin))
        {
            $strWhere .= " AND UPPER(a.login) like :strLogin ";
            $objQuery->setParameter('strLogin', '%' . strtoupper($strLogin) . '%');
            $objQueryCount->setParameter('strLogin', '%' . strtoupper($strLogin) . '%');
        }

        if (!is_null($strDireccion) && !empty($strDireccion))
        {
            $strWhere .= " AND UPPER(a.direccion) like :strDireccion ";
            $objQuery->setParameter('strDireccion', '%' . strtoupper($strDireccion) . '%');
            $objQueryCount->setParameter('strDireccion', '%' . strtoupper($strDireccion) . '%');
        }

        if (!is_null($strCiudad) && !empty($strCiudad))
        {
            $strFrom .=  " , schemaBundle:AdmiSector h, schemaBundle:AdmiParroquia i, schemaBundle:AdmiCanton j ";
            $strWhere .= " AND a.sectorId = h.id 
                               AND h.parroquiaId = i.id 
                               AND i.cantonId = j.id 
                               AND h.estado = 'Activo' 
                               AND i.estado = 'Activo' 
                               AND j.estado = 'Activo' 
                               AND UPPER(j.nombreCanton) like :strCiudad ";

            $objQuery->setParameter('strCiudad', '%' . strtoupper($strCiudad) . '%');
            $objQueryCount->setParameter('strCiudad', '%' . strtoupper($strCiudad) . '%');
        }

        $strWhereCount = $strWhere . " AND a.estado IN (:arrayEstadosPuntosTotal) ";

        $objQuery->setParameter('strCodEmpresa', $strCodEmpresa);
        $objQuery->setParameter('intIdPersona', $intIdPersona);
        $objQuery->setParameter('strRol', $strRol);

        $objQueryCount->setParameter('strCodEmpresa', $strCodEmpresa);
        $objQueryCount->setParameter('intIdPersona', $intIdPersona);
        $objQueryCount->setParameter('strRol', $strRol);
        $objQueryCount->setParameter('arrayEstadosPuntosTotal', $arrayEstadosPuntosTotal);

        $strDql = $strSelect . $strFrom . $strWhere . $strOrderBy;
        $strDqlCount = $strSelectCount . $strFrom . $strWhereCount;

        $objQuery->setDQL($strDql);
        $objQueryCount->setDQL($strDqlCount);

        if(!empty($intInicio) && !empty($intLimite))
        {
            $arrayDatos = $objQuery->setFirstResult($intInicio)->setMaxResults($intLimite)->getResult();
        }
        else if(!empty($intInicio) && empty($intLimite))
        {
            $arrayDatos = $objQuery->setFirstResult($intInicio)->getResult();
        }
        else if(empty($intInicio) && !empty($intLimite))
        {
            $arrayDatos = $objQuery->setMaxResults($intLimite)->getResult();
        }
        else
        {
            $arrayDatos = $objQuery->getResult();
        }

        $intTotal = $objQueryCount->getSingleScalarResult();

        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total'] = $intTotal;

        return $arrayResultado;
    }

    /*
     * Documentación para el método 'findPtosPadreByEmpresaPorCliente'.
     *
     * Retorna un listado de puntos padre facturación por empresa y cliente.
     * Costo: 13
     * @param $idEmpresa    Integer: Id de la empresa.
     * @param $idCli        Integer: Id Persona Empresa Rol.
     * @param $arrayEstados Array:   Listado de estados
     *
     * @return Array Listado de puntos padre de facturación.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-10-2020
     * Variante de función original que obtiene logines por cliente (adominguez) para que trabaje con el idPersonaRol.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 19-05-2022 Se elimina restriccinó de búsqueda de logines que son puntos de facturación. 
     */
    public function findPtosPadreByEmpresaPorCliente($intIdEmpresa, $intIdPersonaRol)
    {
        try
        {
            $objQuery = $this->_em->createQuery();

            $strDQL   = "SELECT a.id, a.login, a.descripcionPunto, b.razonSocial,b.nombres,b.apellidos,a.direccion,a.estado
                         FROM
                         schemaBundle:InfoPunto a, schemaBundle:InfoPersona b, 
                         schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d, schemaBundle:InfoPuntoDatoAdicional e
                         WHERE a.personaEmpresaRolId = c.id AND
                               b.id                  = c.personaId AND
                               c.empresaRolId        = d.id AND
                               a.id                  = e.puntoId AND
                               d.empresaCod          = :EMPRESA AND
                               c.id                  = :CLIENTE  ";


            $objQuery->setParameter("EMPRESA",       $intIdEmpresa);
            $objQuery->setParameter("CLIENTE",       $intIdPersonaRol);

            return $objQuery->setDQL($strDQL)->getResult();
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }    

    /*
     * Método encargado de notificar la culminación del proceso de Reingreso Automático de OS.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 04-09-2019
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta
     */
    public function notificarProcesoReingresoOS($arrayParametros)
    {
        $strString       = '';
        $strMensajeError = str_pad($strString, 4000, " ");
        $booleanStatus   = true;
        $strSql = "BEGIN DB_COMERCIAL.CMKG_REINGRESO.P_SET_NOTIFICA_USUARIO(:Pn_IdServicio,".
                                                                           ":Pv_Mensaje,".
                                                                           ":Pv_Usuario,".
                                                                           ":Pv_Ip,".
                                                                           ":Pv_Error); END;";

        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdServicio' , $arrayParametros['intIdServicio']);
            $objStmt->bindParam('Pv_Mensaje'    , $arrayParametros['strMensaje']);
            $objStmt->bindParam('Pv_Usuario'    , $arrayParametros['strUsuario']);
            $objStmt->bindParam('Pv_Ip'         , $arrayParametros['strIp']);
            $objStmt->bindParam('Pv_Error'      , $strMensajeError);
            $objStmt->execute();

            $arrayRespuesta = array('status'  => ($strMensajeError === 'OK' ? $booleanStatus : !$booleanStatus),
                                    'message' => $strMensajeError);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status'  => false,
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }
    
    /*
     * Método encargado de ejecutar el proceso de Facturación por Instalación.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 - 28-10-2020
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta
     */
    public function getFacturacionInstalReingresoOs($arrayParametros)
    {
        $strString        = '';
        $strMensajeError  = str_pad($strString, 4000, " ");
        $strAplicaProceso = str_pad($strString, 2, " ");
        $booleanStatus    = true;
        $strSql = "BEGIN DB_COMERCIAL.CMKG_REINGRESO.P_FACTURACION_INSTAL_REINGRESO(:Pn_IdServicio,".
                                                                                   ":Pn_PuntoId,".
                                                                                   ":Pv_EmpresaCod,".
                                                                                   ":Pv_AplicaProceso,".
                                                                                   ":Pv_Mensaje); END;";

        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdServicio'     , $arrayParametros['intIdServicio']);
            $objStmt->bindParam('Pn_PuntoId'        , $arrayParametros['intPuntoId']);
            $objStmt->bindParam('Pv_EmpresaCod'     , $arrayParametros['strEmpresaCod']);
            $objStmt->bindParam('Pv_AplicaProceso'  , $strAplicaProceso);
            $objStmt->bindParam('Pv_Mensaje'        , $strMensajeError);
            $objStmt->execute();

            $arrayRespuesta = array('status'  => ($strAplicaProceso === 'S' ? $booleanStatus : !$booleanStatus),
                                    'message' => $strMensajeError);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status'  => false,
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /*
     * Método que me devuelve la cantidad de solicitudes de instalacion anuladas o rechazadas en un punto.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 - 21-08-2022
     *
     * @param  int $intIdPunto
     * @return int $intTotal
     */
    public function getSolicitudRechazada($intIdPunto)
    {     
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        $strSqlCantidad   = ' SELECT COUNT(PUN.ID_PUNTO)  AS TOTAL '; 
                        
        $strSqlFrom       = ' FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL
                                   INNER JOIN DB_COMERCIAL.INFO_SERVICIO SER
                                   ON SOL.SERVICIO_ID = SER.ID_SERVICIO
                                   INNER JOIN DB_COMERCIAL.INFO_PUNTO PUN
                                   ON SER.PUNTO_ID = PUN.ID_PUNTO
                               WHERE PUN.ID_PUNTO = :intIdPunto
                                 AND SOL.TIPO_SOLICITUD_ID = 8           
                              AND SOL.ESTADO      in (:arrayEstados) ';
       
        
       
        $objRsmCount->addScalarResult('TOTAL','total','integer');
        
        
        $objNtvQueryCount->setParameter('intIdPunto', $intIdPunto);                        
        $objNtvQueryCount->setParameter('arrayEstados', array('Anulado','Rechazada'));                        

        $strSqlCantidad .= $strSqlFrom;           
        $objNtvQueryCount->setSQL($strSqlCantidad);        
        $intTotal        = $objNtvQueryCount->getSingleScalarResult();
               
        return $intTotal;               
    }

    /**
     * 
     * Método que verifica si el cliente tiene una tarea de retención mayor o igual a 6 meses
     * 
     * @author Joel Ontuña <jontuna@telconet.ec>
     * @version 1.0 - 21-11-2022
     * 
     * @param array $arrayParamVisibleParaUsuario ['strUser'                => 'Login del Usuario en sesión',
     *                                             'objAdmiParametroCab'    => 'Objeto cabecera de los parametros del Modelo Predictivo',
     *                                             'codEmpresa'             => 'Código de la empresa en sessión',
     *                                             'serviceUtil'            => 'Servicio para ingresar errores',
     *                                             'strIpSession'           => 'Dirección Ip']
     * 
     * @return Boolean $boolIsVisibleParaUsuario
     */
    private function isVisibleParaUsuario($arrayParamVisibleParaUsuario)
    {

        $strUser             = $arrayParamVisibleParaUsuario['strUser'];
        $objAdmiParametroCab = $arrayParamVisibleParaUsuario['objAdmiParametroCab'];
        $intCodEmpresa       = $arrayParamVisibleParaUsuario['codEmpresa'];
        $serviceUtil         = $arrayParamVisibleParaUsuario['serviceUtil'];
        $strIpSession        = $arrayParamVisibleParaUsuario['strIpSession'];
        
        $boolIsVisibleParaUsuario = false;             

        try
        {

            if(is_object($objAdmiParametroCab))
            {
                $intTienePerfil   = 0;
                $objRsm        = new ResultSetMappingBuilder($this->_em);
                $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);                             
                $strQuery      = " SELECT COUNT(*) AS CANTIDAD 
                                FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_COMERCIAL.INFO_PERSONA ip
                                WHERE ip.LOGIN in(:strUser)
                                AND EXISTS (
                                    SELECT 1 from DB_SEGURIDAD.SEGU_PERFIL_PERSONA spp
                                    WHERE spp.PERFIL_ID = sp.ID_PERFIL
                                    AND spp.PERSONA_ID  = ip.ID_PERSONA)
                                AND sp.NOMBRE_PERFIL IN (SELECT VALOR1 FROM ADMI_PARAMETRO_DET 
                                                        WHERE PARAMETRO_ID = :intParametroId 
                                                        AND DESCRIPCION = 'PERFILES' 
                                                        AND ESTADO = 'Activo' 
                                                        AND EMPRESA_COD = :intCodEmpresa) ";

                $objRsm->addScalarResult('CANTIDAD', 'Cantidad', 'integer');

                $objNtvQuery->setParameter('strUser'            , $strUser);
                $objNtvQuery->setParameter('intParametroId'     , $objAdmiParametroCab->getId());
                $objNtvQuery->setParameter('intCodEmpresa'      , $intCodEmpresa);
                
                $intTienePerfil = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();

                $boolIsVisibleParaUsuario = intval($intTienePerfil) > 0 ;
            }

        }catch(\Exception $objException)
        {

            $serviceUtil->insertError(  'Telcos+', 
                                        'InfoPuntoRepository->isVisibleParaUsuario', 
                                        $objException->getMessage(), 
                                        $strUser, 
                                        $strIpSession );
        }
        
        return $boolIsVisibleParaUsuario;
    }

    /**
     * 
     * Método que verifica si el cliente tiene una tarea de retención mayor o igual a 6 meses
     * 
     * @author Joel Ontuña <jontuna@telconet.ec>
     * @version 1.0 - 21-11-2022
     * 
     * @param array $arrayParamRetencionMinima ['strIdentificacion'      => 'Identificación del cliente al que se va a consultar sus tareas',
     *                                          'strUser'                => 'Login del Usuario en sesión',
     *                                          'objAdmiParametroCab'    => 'Objeto cabecera de los parametros del Modelo Predictivo',
     *                                          'codEmpresa'             => 'Código de la empresa en sessión',
     *                                          'serviceUtil'            => 'Servicio para ingresar errores',
     *                                          'strIpSession'           => 'Dirección Ip']
     * 
     * @return bool $boolIsLibreDeRetencion
     * 
     */
    private function isLibreDeRetencionMinima($arrayParamRetencionMinima)
    {

        $strIdentificacion   = $arrayParamRetencionMinima['strIdentificacion'];
        $strUser             = $arrayParamRetencionMinima['strUser'];
        $objAdmiParametroCab = $arrayParamRetencionMinima['objAdmiParametroCab'];
        $intCodEmpresa       = $arrayParamRetencionMinima['codEmpresa'];
        $serviceUtil         = $arrayParamRetencionMinima['serviceUtil'];
        $strIpSession        = $arrayParamRetencionMinima['strIpSession'];

        $boolIsLibreDeRetencion = true;
        $intContadorRetenciones = 0;

        try 
        {
            $objParamMesesRetencion = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array( 'estado'      => 'Activo',
                                                                        'parametroId' => $objAdmiParametroCab->getId(),
                                                                        'descripcion' => 'TIEMPO_RETENCION_MESES',
                                                                        'empresaCod'  => $intCodEmpresa
                                                                    )
                                                                );

            if (is_object($objParamMesesRetencion))
            {
                $intMinimoMesesRetencion = intval($objParamMesesRetencion->getValor1());
            }
            
            $objRsm           = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery         = $this->_em->createNativeQuery(null, $objRsm);              
            $strSqlDatos      = ' SELECT COUNT(*) TOTAL ';

            $strSqlFrom = " FROM DB_COMERCIAL.INFO_PERSONA ip 
                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL ipe ON ipe.PERSONA_ID = ip.ID_PERSONA 
                            JOIN DB_COMERCIAL.INFO_PUNTO ipu ON ipu.PERSONA_EMPRESA_ROL_ID = ipe.ID_PERSONA_ROL 
                            JOIN DB_SOPORTE.INFO_PARTE_AFECTADA ipa ON ipu.LOGIN = ipa.AFECTADO_NOMBRE 
                            JOIN DB_SOPORTE.INFO_TAREA ita on ita.DETALLE_ID = ipa.DETALLE_ID 
                            JOIN DB_SOPORTE.ADMI_TAREA ata on ita.TAREA_ID = ata.ID_TAREA 
                            JOIN DB_SOPORTE.ADMI_PROCESO pro on ata.PROCESO_ID = pro.ID_PROCESO 
                            WHERE ip.IDENTIFICACION_CLIENTE = :strIdentificacion 
                            AND pro.NOMBRE_PROCESO = 'PROCESO TAREAS RETENCIÓN' 
                            AND ita.ESTADO in (SELECT VALOR1 FROM ADMI_PARAMETRO_DET 
                                                WHERE PARAMETRO_ID = :intParametroId 
                                                AND DESCRIPCION = 'ESTADOS_TAREAS' 
                                                AND ESTADO = 'Activo' 
                                                AND EMPRESA_COD = :intCodEmpresa) 
                            AND MONTHS_BETWEEN(CURRENT_DATE,ita.FE_SOLICITADA) <= :intMinimoMesesRetencion ";
            
            $objRsm->addScalarResult('TOTAL','total','integer');
            
            $objNtvQuery->setParameter('strIdentificacion'          , $strIdentificacion);
            $objNtvQuery->setParameter('intParametroId'             , $objAdmiParametroCab->getId());
            $objNtvQuery->setParameter('intCodEmpresa'              , $intCodEmpresa);
            $objNtvQuery->setParameter('intMinimoMesesRetencion'    , $intMinimoMesesRetencion);
            
            $strSqlDatos    .= $strSqlFrom;
            
            $objNtvQuery->setSQL($strSqlDatos);
            $intContadorRetenciones = intval($objNtvQuery->getSingleScalarResult());

            $boolIsLibreDeRetencion = $intContadorRetenciones == 0;
            
        } catch (\Exception $objException)
        {

            $boolIsLibreDeRetencion = false;
            
            $serviceUtil->insertError(  'Telcos+', 
                                        'InfoPuntoRepository->isLibreDeRetencionMinima', 
                                        $objException->getMessage(), 
                                        $strUser, 
                                        $strIpSession );
        }

        return $boolIsLibreDeRetencion;
    }

    /**
     * 
     * Método que realizar la consulta al WS para obtener la información de BI
     * 
     * @author Joel Ontuña <jontuna@telconet.ec>
     * @version 1.0 - 21-11-2022
     * 
     * @author Byron Pibaque <bpibaque@telconet.ec>
     * @version 1.1 - 02-28-2023
     * @param array $arrayParamModeloPredictivo['strIdentificacion'      => 'Identificación del cliente al que se va a consultar sus tareas',
     *                                          'strUser'                => 'Login del Usuario en sesión',
     *                                          'strLogin'               => 'Login del cliente',
     *                                          'serviceUtil'            => 'Servicio para ingresar errores',
     *                                          'strIpSession'           => 'Dirección Ip']
     * 
     * @return array $arrayModeloPredictivoResponse
     * 
     */
    private function getArrayModeloPredictivo($arrayParamModeloPredictivo)
    {      
        
        $strIdentificacion   = $arrayParamModeloPredictivo['strIdentificacion'];
        $strUser             = $arrayParamModeloPredictivo['strUser'];
        $strLogin            = $arrayParamModeloPredictivo['strLogin'];
        $serviceUtil         = $arrayParamModeloPredictivo['serviceUtil'];
        $strIpSession        = $arrayParamModeloPredictivo['strIpSession'];
        $serviceRDA          = $arrayParamModeloPredictivo['serviceRDA'];
       

        $arrayModeloPredictivoRequest  = array(
            'login'                 =>  $strLogin
        );

        $arrayModeloPredictivoResponse = array();
       

        try 
        {   
            $objRsm           = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery         = $this->_em->createNativeQuery(null, $objRsm);                                
                $strQuery      = "SELECT VALOR1 
                                    FROM DB_GENERAL.ADMI_PARAMETRO_DET DET 
                                    WHERE DET.DESCRIPCION='SEMAFORO_PARAMETRO' 
                                    AND DET.ESTADO  = 'Activo' 
                                    and DET.EMPRESA_COD=18";
               
                $objRsm->addScalarResult('VALOR1', 'Valor1', 'string');

               
                
                $strParam = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();

            //Consumo de MS MODELO PREDICTIVO
            $arrayResponse = $serviceRDA->microservicio(json_encode($arrayModeloPredictivoRequest));
            if ($arrayResponse['status'] == 'OK') 
            {  
                    $arrayModeloPredictivoResponse = $arrayResponse['datos'];
                    $arrayModeloPredictivoResponse['param'] = $strParam;

            } elseif ($arrayResponse['status'] == 'ERROR') 
            {
                $arrayModeloPredictivoResponse['Nota'] = $arrayResponse['mensaje'];
                $arrayModeloPredictivoResponse['param'] = $strParam;

                $serviceUtil->insertError(
                    'Telcos+', 
                                            'InfoPuntoRepository->getArrayModeloPredictivo', 
                                            $arrayResponse['mensaje'], 
                                            $strUser, 
                                            $strIpSession );
            }

        } catch (\Exception $objException) 
        {
            
            $serviceUtil->insertError(  'Telcos+', 
                                        'InfoPuntoRepository->getArrayModeloPredictivo', 
                                        $objException->getMessage(), 
                                        $strUser, 
                                        $strIpSession );
                                        error_log($objException->getMessage());

        }

            
        return $arrayModeloPredictivoResponse;
    }
    
    /* Función encargada de devolver todos los servicios adicionales del punto que tengan la característica de konibit.
     *
     * Costo: 15
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-11-2022
     *
     * @param Array $arrayParametros [
     *                                 strValor             : Valor de la caracteristica.
     *                                 strAccion            : Crs ó Traslado.
     *                                 intIdPunto           : Id del punto.
     *                                 strEstadoProdCaract  : Estado del producto característica.
     *                                 strDescripcionCaract : Descripción de la característica.
     *                                 strUsuario           : Usuario quien realiza la petición.
     *                                 strIp                : Ip del usuario quien realiza la petición.
     *                                 objUtilService       : Objeto del service Util.
     *                               ]
     *
     * @return Array $arrayRespuesta['result']
     */

    public function getServProdCartKonibit($arrayParametros)
    {
        $objUtilService = $arrayParametros['objUtilService'];
        $strUsuario     = $arrayParametros['strUsuario'] ? $arrayParametros['strUsuario'] : 'Telcos+';
        $strIp          = $arrayParametros['strIp']      ? $arrayParametros['strIp']      : '127.0.0.1';

        try
        {
            $objRsm      = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT INPU.ID_PUNTO    AS ID_PUNTO, ".
                             "INPU.LOGIN       AS LOGIN, ".
                             "INSE.ID_SERVICIO AS ID_SERVICIO, ".
                             "INSE.ESTADO      AS ESTADO_SERVICIO, ".
                             "ADPR.DESCRIPCION_PRODUCTO       AS DESCRIPCION_PRODUCTO, ".
                             "ADCA.DESCRIPCION_CARACTERISTICA AS DESCRIPCION_CARACTERISTICA ".
                        "FROM DB_COMERCIAL.INFO_PUNTO                   INPU, ".
                             "DB_COMERCIAL.INFO_SERVICIO                INSE, ".
                             "DB_COMERCIAL.INFO_SERVICIO_HISTORIAL      ISH, ".
                             "DB_COMERCIAL.ADMI_PRODUCTO                ADPR, ".
                             "DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADPRCA, ".
                             "DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT    ISPC, ".
                             "DB_COMERCIAL.ADMI_CARACTERISTICA          ADCA ".
                      "WHERE INPU.ID_PUNTO                          = INSE.PUNTO_ID ".
                        "AND INSE.PRODUCTO_ID                       = ADPR.ID_PRODUCTO ".
                        "AND ISH.SERVICIO_ID                        = INSE.ID_SERVICIO ".
                        "AND ADPR.ID_PRODUCTO                       = ADPRCA.PRODUCTO_ID ".
                        "AND ADPRCA.CARACTERISTICA_ID               = ADCA.ID_CARACTERISTICA ".
                        "AND ISPC.PRODUCTO_CARACTERISITICA_ID       = ADPRCA.ID_PRODUCTO_CARACTERISITICA ".
                        "AND ISPC.SERVICIO_ID                       = INSE.ID_SERVICIO ".
                        "AND ISH.ACCION                             = :strAccion ".
                        "AND ISPC.VALOR                             = :strValor ".
                        "AND INPU.ID_PUNTO                          = :intIdPunto ".
                        "AND UPPER(ADPRCA.ESTADO)                   = :strEstadoProdCaract ".
                        "AND UPPER(ADCA.DESCRIPCION_CARACTERISTICA) = :strDescripcionCaract";

            $objNtvQuery->setParameter('intIdPunto'           , $arrayParametros['intIdPunto']);
            $objNtvQuery->setParameter('strValor'             , $arrayParametros['strValor']);
            $objNtvQuery->setParameter('strAccion'            , $arrayParametros['strAccion']);
            $objNtvQuery->setParameter('strEstadoProdCaract'  , strtoupper($arrayParametros['strEstadoProdCaract']));
            $objNtvQuery->setParameter('strDescripcionCaract' , strtoupper($arrayParametros['strDescripcionCaract']));

            $objRsm->addScalarResult('ID_PUNTO'                   , 'idPunto'                   , 'integer');
            $objRsm->addScalarResult('LOGIN'                      , 'login'                     , 'string');
            $objRsm->addScalarResult('ID_SERVICIO'                , 'idServicio'                , 'integer');
            $objRsm->addScalarResult('ESTADO_SERVICIO'            , 'estadoServicio'            , 'string');
            $objRsm->addScalarResult('DESCRIPCION_PRODUCTO'       , 'descripcionProducto'       , 'string');
            $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA' , 'descripcionCaracteristica' , 'string');

            $objNtvQuery->setSQL($strSql);

            $arrayRespuesta = array ('result' => $objNtvQuery->getResult());
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta = array ('result' => null);

            if (is_object($objUtilService))
            {
                $objUtilService->insertError('Telcos+',
                                             'InfoPuntoRepository->getServiciosProductoKonibit',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);
            }
        }
        return $arrayRespuesta;
    }

}
