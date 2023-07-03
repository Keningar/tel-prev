<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoServicioHistorialRepository extends EntityRepository
{
    	public function findFechaActivacionPorServicioId($idServicio)
	{
		$query = $this->_em->createQuery("SELECT e
		FROM 
                schemaBundle:InfoServicioHistorial e
		WHERE 
                e.servicioId=$idServicio AND
                e.estado='Activo'    
                order by e.feCreacion ASC");
                //echo $query->getSQL();die;
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		return $datos;
	}
    
    
    /**
     * findMaxHistorialPorServicio
     *
     * Método que retorna el maximo historial de un servicio                                   
     *      
     * @param integer $intIdServicio
     * 
     * @return object $objHistorial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 01-12-2015
     */
    public function findMaxHistorialPorServicio( $intIdServicio )
    {
        $strSelect = "SELECT ish ";
        $strFrom   = "FROM schemaBundle:InfoServicioHistorial ish ";
        $strWhere  = "WHERE ish.id = (
                                        SELECT MAX(ish2.id)
                                        FROM schemaBundle:InfoServicioHistorial ish2
                                        JOIN ish2.servicioId iser
                                        WHERE iser.id = :intIdServicio
                                     ) ";

        $strSql = $strSelect.$strFrom.$strWhere;
        $query  = $this->_em->createQuery($strSql);

        $query->setParameter("intIdServicio", $intIdServicio);

        $objHistorial = $query->getOneOrNullResult();

        return $objHistorial;
    }
    
    
     /**
     * Costo: 9
     * getServicioHistorial
     * 
     * Método que retorna si ya existe en el historial del servicio la acción de 'confirmarServicio' o la observación
     * 'Se confirmo el servicio'
     * 
     * @param array $arrayParametros[ 'intServicioId'  => id del servicio 
     *                                'strEstado'      => estado del historial del servicio
     *                                'strAccion'      => accion 'confirmarServicio'
     *                                'strObservacion' => observacion 'Se confirmo el servicio' ]
     *
     * @return string $strExisteHistorial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-04-2017
     */
    public function getServicioHistorial($arrayParametros)
    {
        $intHistorialServicio = 0;
        $strExisteHistorial   = "N";

        $strSql = " SELECT COUNT(infoserviciohistorial.id_servicio_historial) as TOTAL
                        FROM info_servicio_historial infoserviciohistorial
                        WHERE infoserviciohistorial.servicio_id = :servicioId
                        AND infoserviciohistorial.estado = :estado
                        AND ( infoserviciohistorial.accion = :accion
                        OR dbms_lob.compare(nvl(infoserviciohistorial.observacion,null),:observacion) = 0) ";

        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue('servicioId',$arrayParametros["intServicioId"]);
        $objStmt->bindValue('estado',$arrayParametros["strEstado"]);
        $objStmt->bindValue('accion',$arrayParametros["strAccion"]);
        $objStmt->bindValue('observacion',$arrayParametros["strObservacion"]);
        $objStmt->execute();

        $intHistorialServicio = $objStmt->fetchColumn();

        if($intHistorialServicio > 0)
        {
            $strExisteHistorial = "S";
        }

        return $strExisteHistorial;
    }

     /**
     * Costo: 3
     *
     * Encargado de verificar si la asignación de recursos de red son recursos nuevos o existentes
     *
     * @param array $arrayParametros[ 'intIdServicio'   => id del servicio
     *                                'strTipoRecursos' => tipo de recursos
     *                                'strHistorial'    => trama a buscar en la observacion del historial
     *                                'strEstado'       => estado del historial del servicio a buscar ]
     *
     * @return string $strExisteHistorial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 09-01-2020
     */
    public function validaTipoRecurso($arrayParametros)
    {
        $intHistorialServicio = 0;
        $strExisteHistorial   = "N";

        $strSql = " SELECT COUNT(ISH.ID_SERVICIO_HISTORIAL) FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
                    WHERE ISH.OBSERVACION LIKE :paramTipoRecursos AND ISH.ID_SERVICIO_HISTORIAL = (
                    SELECT MAX(ISH2.ID_SERVICIO_HISTORIAL) FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH2 WHERE ISH2.SERVICIO_ID = :paramServicio AND
                    ISH2.OBSERVACION LIKE :paramHistorial AND ISH2.ESTADO = :paramEstado)";

        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue('paramServicio',$arrayParametros["intIdServicio"]);
        $objStmt->bindValue('paramTipoRecursos','%'.$arrayParametros["strTipoRecursos"].'%');
        $objStmt->bindValue('paramHistorial','%'.$arrayParametros["strHistorial"].'%');
        $objStmt->bindValue('paramEstado',$arrayParametros["strEstado"]);
        $objStmt->execute();

        $intHistorialServicio = $objStmt->fetchColumn();

        if($intHistorialServicio > 0)
        {
            $strExisteHistorial = "S";
        }

        return $strExisteHistorial;
    }


     /**
     * 
     * Método que retorna las ordenes nuevas agrupadas por vendedor
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * Costo query: 519
     * @param array $arrayParametrosServiceComercial [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                                strFechaFin             => Fecha final de la búsqueda
     *                                                strUsrCreacion          => Usuario en sessión
     *                                                strIpCreacion           => Ip del usuario en sessión
     *                                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                                strTipo                 => Tipo
     *                                                strTipoConsulta         => Tipo de consulta
     *                                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *                                                strTipoPersonal         => Tipo del personal
     *                                                intIdPersonEmpresaRol   => Id de la persona
     *
     * @return array $arrayDatosOrdenesNuevas ['vendedor'     => Todos los vendedores de acuerdo al intIdPersonEmpresaRol recibido por parametro
     *                                         'TOTAL'       => cantidad de las órdenes nuevas
     *                                         'SUMATOTAL'   => suma total
     *                                        ] 
     *
     */    
    public function getOrdenesNuevas($arrayParametrosServiceComercial)
    {
                
        $arrayDatosOrdenesNuevas=array();
        
        $strSelect = "select iser.usr_vendedor as vendedor,
                        count(*) as TOTAL, 
                        sum(iser.PRECIO_VENTA) as SUMATOTAL ";
        
        $strFrom   = "  FROM DB_COMERCIAL.info_servicio iser
                        JOIN DB_COMERCIAL.info_servicio_historial           ish  ON ish.SERVICIO_ID=iser.ID_SERVICIO
                        JOIN DB_COMERCIAL.info_persona                      ip   ON ip.login=iser.usr_vendedor
                        JOIN DB_COMERCIAL.info_persona_empresa_rol          iper ON iper.persona_id=ip.id_persona          
                        JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO                IOG  ON IOG.ID_OFICINA=IPER.OFICINA_ID 
                        JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO                IEG  ON IOG.EMPRESA_ID=IEG.COD_EMPRESA  ";
        if($arrayParametrosServiceComercial['strTipoPersonal']==='SUBGERENTE' || $arrayParametrosServiceComercial['strTipoPersonal']==='GERENTE_VENTAS')        
        {
            $strWhere  = "WHERE LOWER(ish.accion)   = LOWER('confirmarServicio')
                            AND LOWER(ish.ESTADO)   = LOWER('Activo')
                            AND LOWER(IOG.ESTADO)   = LOWER('Activo')
                            AND LOWER(IEG.PREFIJO)  = LOWER(:Pv_PrefijoEmpresa)
                            and ish.FE_CREACION BETWEEN TO_DATE(:FechaInicio,'dd-MON-yy') AND TO_DATE(:FechaFin,'dd-MON-yy')
                            and iper.reporta_persona_empresa_rol_id=:IdPersonaEmpresaRol
                            GROUP BY iser.usr_vendedor 
                            ORDER BY iser.USR_VENDEDOR ";        
        }        
        else
        {
            $strWhere  = "WHERE LOWER(ish.accion)   = LOWER('confirmarServicio')
                            AND LOWER(ish.ESTADO)   = LOWER('Activo')
                            AND LOWER(IOG.ESTADO)   = LOWER('Activo')
                            AND LOWER(IEG.PREFIJO)  = LOWER(:Pv_PrefijoEmpresa)
                            and ish.FE_CREACION BETWEEN TO_DATE(:FechaInicio,'dd-MON-yy') AND TO_DATE(:FechaFin,'dd-MON-yy')
                            and iper.ID_PERSONA_ROL=:IdPersonaEmpresaRol
                            GROUP BY iser.usr_vendedor 
                            ORDER BY iser.USR_VENDEDOR ";            
        }

        
        $strSql = $strSelect.$strFrom.$strWhere;
               
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue("Pv_PrefijoEmpresa",    $arrayParametrosServiceComercial['strPrefijoEmpresa']);        
        $objStmt->bindValue('FechaInicio'         , $arrayParametrosServiceComercial['strFechaInicio']);
        $objStmt->bindValue('FechaFin'            , $arrayParametrosServiceComercial['strFechaFin']);
        $objStmt->bindValue("IdPersonaEmpresaRol" , $arrayParametrosServiceComercial['intIdPersonEmpresaRol']);
        
        $objStmt->execute();
        
        $arrayDatosOrdenesNuevas = $objStmt->fetchAll();

        return $arrayDatosOrdenesNuevas;
    }
    
    /**
     *
     * Función encargada para retornar todas las ordenes upgrade o Dowgrade agrupadas por vendedor
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * Costo query: 7
     * @param mixed strFechaInicio => Fecha de inicio de la búsqueda
     * @param mixed strFechaFin    => Fecha final de la búsqueda
     * @param mixed $intIdServicio    => Id del servicio
     * @return array $arrayListaOrdenesUpgrade ['USR_VENDEDOR'     => Todos los vendedores de acuerdo a los parametros
     *                                          'observacion'       => observacion donde se detallará las ordenes up y dow
     *                                         ]
     * 
     */     
    public function getDatosOrdenes($strFechaInicio,$strFechaFin,$intIdServicio)
    {
        $arrayListaOrdenesUpgrade = array();
        
        try
        {
            $strSelect ="select ish.observacion,iser.USR_VENDEDOR ";
            $strFrom   = "from INFO_SERVICIO_HISTORIAL ish
                            join info_servicio iser on iser.id_servicio=ish.SERVICIO_ID ";
            $strWhere  ="where ish.SERVICIO_ID=:idServicio
                            and ish.FE_CREACION BETWEEN TO_DATE(:FechaInicio,'dd-MON-yy') AND TO_DATE(:FechaFin,'dd-MON-yy')
                            and (ish.OBSERVACION like '%Cambio de Plan%')
                            order by ish.FE_CREACION";

            $strSql = $strSelect.$strFrom.$strWhere;

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindValue('idServicio',$intIdServicio);
            $objStmt->bindValue('FechaInicio',$strFechaInicio);
            $objStmt->bindValue('FechaFin',$strFechaFin);        
            $objStmt->execute();

            $arrayListaOrdenesUpgrade = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('getDatosOrdenes -> '.$e->getMessage());
            throw($e);
        }         
        return $arrayListaOrdenesUpgrade;
    }

    /**
     *
     * getRowsHistorialServicio, obtiene filas del historial de un servicio, dependiendo los parametros enviados
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 26/02/2019
     * Costo query: 15 , Cardinalidad: 2
     * @param mixed arrayRequest => ['intRow'        => Numero de filas a retornar
     *                               'strOrder'      => Orden en que se recuperaran las filas
     *                               'intField'      => Columna que debe ordenar
     *                               'intIdServicio' => Id del servicio]
     * @return array $arraResponse => Array con el resultado del query
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 08/01/2021 - Se modifica query para obtener los registros de historial de un servicio por día.
     * 
     */     
    public function getRowsHistorialServicio($arrayRequest)
    {
        $arrayResponse = array();
        try
        {
            $strQuery  = "WITH LAST_2_STATES AS ( ";
            $strQuery .= "      SELECT ";
            $strQuery .= "           * ";
            $strQuery .= "       FROM ";
            $strQuery .= "           DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH ";
            $strQuery .= "       WHERE ";
            $strQuery .= "           ISH.SERVICIO_ID = :intIdServicio ORDER BY ";
            $strQuery .= "  " . (!empty($arrayRequest['intField'])) ? ' ' . $arrayRequest['intField'] . ' ' : ' 1 ';
            $strQuery .= "  " . (!empty($arrayRequest['strOrder'])) ? ' ' . $arrayRequest['strOrder'] . ' ' : ' DESC ' ;
            $strQuery .= "   ) ";
            $strQuery .= "   SELECT TBL_SERV_HIST.FE_CREACION, TBL_SERV_HIST.ID_SERVICIO, TBL_SERV_HIST.ESTADO FROM ( ";
            $strQuery .= "     SELECT TO_CHAR(L2S.FE_CREACION, 'DD-MM-YYYY HH24:MI:SS') FE_CREACION, ";
            $strQuery .= "       L2S.SERVICIO_ID ID_SERVICIO, ";
            $strQuery .= "       L2S.ESTADO ESTADO, ";
            $strQuery .= "       L2S.FE_CREACION AS FECHA ";
            $strQuery .= "     FROM LAST_2_STATES L2S ";
            $strQuery .= "     WHERE ROWNUM < :intRow";
            $strQuery .= "   UNION ";
            $strQuery .= "     SELECT TO_CHAR(L2S.FE_CREACION, 'DD-MM-YYYY HH24:MI:SS') FE_CREACION, ";
            $strQuery .= "       L2S.SERVICIO_ID ID_SERVICIO, ";
            $strQuery .= "       L2S.ESTADO ESTADO, ";
            $strQuery .= "       L2S.FE_CREACION AS FECHA ";
            $strQuery .= "     FROM LAST_2_STATES L2S WHERE ";
            $strQuery .= "       FE_CREACION < (SELECT TRUNC(MAX(FE_CREACION)) FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH ";
            $strQuery .= "             WHERE ISH.SERVICIO_ID = :intIdServicio) ";
            $strQuery .= "   AND ROWNUM < :intRow ) TBL_SERV_HIST ORDER BY TBL_SERV_HIST.FECHA DESC";

            $objStmt = $this->_em->getConnection()->prepare($strQuery);
            $objStmt->bindValue('intRow', $arrayRequest['intRow']); 
            $objStmt->bindValue('intIdServicio', $arrayRequest['intIdServicio']);
            $objStmt->execute();

            $arrayResponse = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('InfoServicioHistorialRepository -> getRowsHistorialServicio : '. $e->getMessage());
            throw($e);
        }         
        return $arrayResponse;
    }
    
    /**
     *
     * Función encargada para retornar los clientes cancelados segun el tipo de personal.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * Costo query: 467
     * @param array $arrayParametrosServiceComercial [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                                strFechaFin             => Fecha final de la búsqueda
     *                                                strUsrCreacion          => Usuario en sessión
     *                                                strIpCreacion           => Ip del usuario en sessión
     *                                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                                strTipo                 => Tipo
     *                                                strTipoConsulta         => Tipo de consulta
     *                                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *                                                strTipoPersonal         => Tipo del personal
     *                                                intIdPersonEmpresaRol   => Id de la persona
     * @return array $arrayClientesCancel   ['VENDEDOR'     => Todos los vendedores de acuerdo al intIdPersonEmpresaRol recibido por parametro
     *                                       'CANTIDAD'     => cantidad de las órdenes a facturar
     *                                       'TOTAL'        => cantidad
     *                                      ]    
     * 
     */      
    public function getClientesCancelados($arrayParametrosServiceComercial)
    {
        $arrayClientesCancel=array();   
        try
        {
            $strSelect ="
                            SELECT IDS.USR_VENDEDOR AS VENDEDOR,
                            CASE
                            WHEN IP.RAZON_SOCIAL IS NOT NULL
                            THEN IP.RAZON_SOCIAL
                            ELSE IP.NOMBRES
                                || ' '
                                || IP.APELLIDOS
                            END AS CLIENTE,
                            IPU.LOGIN,
                            AP.DESCRIPCION_PRODUCTO, 
                            CASE
                            WHEN IDS.DESCUENTO_UNITARIO IS NOT NULL
                            THEN ROUND((IDS.CANTIDAD*IDS.PRECIO_VENTA)-(IDS.DESCUENTO_UNITARIO),2)
                            ELSE ROUND((IDS.CANTIDAD*IDS.PRECIO_VENTA)-(IDS.DESCUENTO_TOTALIZADO),2)
                            END AS TOTAL,
                            CASE
                            WHEN IDS.MOTIVO_CANCELACION IS NOT NULL
                            THEN IDS.MOTIVO_CANCELACION
                            ELSE 'NINGUNO'
                            END AS MOTIVO
                        ";
            $strFrom   = "
                            FROM DB_COMERCIAL.INFO_DASHBOARD_SERVICIO  IDS
                            JOIN DB_COMERCIAL.INFO_PUNTO               IPU   ON IPU.ID_PUNTO        = IDS.PUNTO_ID
                            JOIN DB_COMERCIAL.ADMI_PRODUCTO            AP    ON AP.ID_PRODUCTO      = IDS.PRODUCTO_ID
                            --JOIN INFO_PUNTO_DATO_ADICIONAL           IPUD  ON IPUD.PUNTO_ID=IPU.ID_PUNTO
                            JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER  ON IPER.ID_PERSONA_ROL = IPU.PERSONA_EMPRESA_ROL_ID
                            JOIN DB_COMERCIAL.INFO_PERSONA             IP    ON IP.ID_PERSONA       = IPER.PERSONA_ID
                            JOIN DB_COMERCIAL.INFO_SERVICIO_HISTORIAL  ISERH ON ISERH.SERVICIO_ID   = IDS.SERVICIO_ID
                            JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO       IOG   ON IOG.ID_OFICINA      = IPER.OFICINA_ID
                            JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO       IEG   ON IOG.EMPRESA_ID      = IEG.COD_EMPRESA
                        ";

            if($arrayParametrosServiceComercial['strTipoPersonal']==='SUBGERENTE' || $arrayParametrosServiceComercial['strTipoPersonal']==='GERENTE_VENTAS')        
            {
                $strWhere  ="
                                WHERE IDS.MOTIVO_PADRE_CANCELACION ='Cancelacion'
                                AND IDS.FRECUENCIA_PRODUCTO        =1
                                AND IDS.ES_VENTA                   ='S'
                                AND ISERH.ESTADO                   ='Cancel'
                                AND IDS.FECHA_TRANSACCION BETWEEN TO_DATE(:Pd_FechaInicio,'dd-mm-yy') AND TO_DATE(:Pd_FechaFin,'dd-mm-yy')-1
                                AND ISERH.FE_CREACION BETWEEN TO_DATE(:Pd_FechaInicio,'dd-mm-yy') AND TO_DATE(:Pd_FechaFin,'dd-mm-yy')    -1
                                AND IDS.ACCION        ='Nueva'
                                AND IDS.USR_VENDEDOR IN
                                (SELECT IPE_S.LOGIN
                                FROM DB_COMERCIAL.INFO_PERSONA IPE_S
                                JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER_S
                                ON IPER_S.PERSONA_ID                        = IPE_S.ID_PERSONA
                                WHERE IPER_S.REPORTA_PERSONA_EMPRESA_ROL_ID = :intIdPersonEmpresaRol
                                )
                                AND LOWER(IEG.PREFIJO) = LOWER(:PrefijoEmpresa) 
                                --AND IPUD.ES_PADRE_FACTURACION='S'
                                ORDER BY VENDEDOR
                            ";            
            }
            else
            {
                $strWhere  ="
                                WHERE IDS.MOTIVO_PADRE_CANCELACION ='Cancelacion'
                                AND IDS.FRECUENCIA_PRODUCTO        =1
                                AND IDS.ES_VENTA                   ='S'
                                AND ISERH.ESTADO                   ='Cancel'
                                AND IDS.FECHA_TRANSACCION BETWEEN TO_DATE(:Pd_FechaInicio,'dd-mm-yy') AND TO_DATE(:Pd_FechaFin,'dd-mm-yy')-1
                                AND ISERH.FE_CREACION BETWEEN TO_DATE(:Pd_FechaInicio,'dd-mm-yy') AND TO_DATE(:Pd_FechaFin,'dd-mm-yy')    -1
                                AND IDS.ACCION        ='Nueva'
                                AND IDS.USR_VENDEDOR IN
                                (SELECT IPE_S.LOGIN
                                FROM DB_COMERCIAL.INFO_PERSONA IPE_S
                                JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER_S
                                ON IPER_S.PERSONA_ID                        = IPE_S.ID_PERSONA
                                WHERE IPER_S.ID_PERSONA_ROL = :intIdPersonEmpresaRol
                                )
                                AND LOWER(IEG.PREFIJO) = LOWER(:PrefijoEmpresa)
                                --AND IPUD.ES_PADRE_FACTURACION='S'
                                ORDER BY VENDEDOR
                            ";
            }

            $strSql  = $strSelect.$strFrom.$strWhere;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('Pd_FechaInicio'       , date("d-M-Y", strtotime("-1 month", strtotime($arrayParametrosServiceComercial['strFechaInicio']))));
            $objStmt->bindValue('Pd_FechaFin'          , date("d-M-Y", strtotime("-1 month", strtotime($arrayParametrosServiceComercial['strFechaFin']))));
            $objStmt->bindValue('PrefijoEmpresa'       , $arrayParametrosServiceComercial['strPrefijoEmpresa']);
            $objStmt->bindValue('intIdPersonEmpresaRol', $arrayParametrosServiceComercial['intIdPersonEmpresaRol']);
            $objStmt->execute();

            $arrayClientesCancel = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('getClientesCancelados -> '.$e->getMessage());
            throw($e);
        }  
        return $arrayClientesCancel;
    }    

    /**
     * findHistorialPorObservacion
     *
     * Costo: 14
     *
     * Método que retorna el historial de un servicio por observacion
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 07-05-2020
     *
     * @param array $arrayParametros[
     *                                  intServicioId:    integer:   Servicio id
     *                                  strObservacion:   string:    Observación a buscar
     *                              ]
     * @return object $objHistorial
     */
    public function findHistorialPorObservacion( $arrayParametros )
    {
        $strSelect = "SELECT ish ";
        $strFrom   = "FROM schemaBundle:InfoServicioHistorial ish ";
        $strWhere  = "WHERE ish.servicioId  = :intServicioId
                        AND ish.observacion like :strObservacion";

        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery  = $this->_em->createQuery($strSql);

        $objQuery->setParameter("intServicioId", $arrayParametros['intServicioId']);
        $objQuery->setParameter("strObservacion", $arrayParametros['strObservacion']. "%");

        $objHistorial = $objQuery->getOneOrNullResult();

         return $objHistorial;
    }

    /**
     * findHistorialServicioIdEstado
     * Método que retorna el ultimo historial de un servicio por su estado
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 14-12-2021
     *
     * @param array $arrayParametros[ intIdServicio: Id del Servicio Servicio id
     *                                strEstado:     Estado por el que buscara el historial]
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 06-10-2022
     * 
     * Se modifica la función para devolver un array de registros
     * 
     * @return array $objHistorial
    */
    public function findHistServicioIdEstado($arrayParametros)
	{
        $intIdServicio = $arrayParametros['intIdServicio'];
        $strEstado = $arrayParametros['strEstado'];
		$strSql = "SELECT h FROM schemaBundle:InfoServicioHistorial h 
                   WHERE h.servicioId  = :intIdServicio
                   AND h.estado = :strEstado
                   ORDER BY h.feCreacion desc";
        $objQuery  = $this->_em->createQuery($strSql);
        $objQuery->setParameter("intIdServicio", $intIdServicio);
        $objQuery->setParameter("strEstado", $strEstado);
        $arrayHistoriales = $objQuery->getResult();

        return $arrayHistoriales;
	}

     /**
     * findHistorialPorObservacion
     *
     * Costo: 14
     *
     * Método que retorna el historial de un servicio por observacion
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 07-05-2020
     *
     * @param array $arrayParametros[
     *                                  intServicioId:    integer:   Servicio id
     *                                  strObservacion:   string:    Observación a buscar
     *                              ]
     * @return object $objHistorial
     */
    public function findListaHistorialPorObservacion( $arrayParametros )
    {
        $strSelect = "SELECT ish ";
        $strFrom   = "FROM schemaBundle:InfoServicioHistorial ish ";
        $strWhere  = "WHERE ish.servicioId  = :intServicioId
                        AND ish.observacion like :strObservacion";

        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery  = $this->_em->createQuery($strSql);

        $objQuery->setParameter("intServicioId", $arrayParametros['intServicioId']);
        $objQuery->setParameter("strObservacion", $arrayParametros['strObservacion']. "%");

        $objHistorial = $objQuery->getResult();

         return $objHistorial;
    }

}