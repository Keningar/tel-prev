<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPagoLineaRepository extends EntityRepository
{
    /**
     * Devuelve la suma de los valores de los pagos en linea pendientes de un cliente
     * @param string $empresaCod
     * @param string $identificacionCliente
     * @return float
     */
	public function obtenerSumaValorPendiente($empresaCod, $identificacionCliente)
	{
	    $query = $this->_em->createQuery("
            SELECT
                SUM(pag.valorPagoLinea)
            FROM schemaBundle:InfoPagoLinea pag
                JOIN pag.persona per
            WHERE pag.empresaId = :empresaCod
                AND per.identificacionCliente = :identificacionCliente
	            AND pag.estadoPagoLinea = 'Pendiente'
        ");
	    $query->setParameters(array(
	                    'empresaCod' => $empresaCod,
	                    'identificacionCliente' => $identificacionCliente,
	    ));
	    return $query->getSingleScalarResult();
	}
    
    /**
     * getResumenPagosLinea, metodo que crea el query para la obtencion agrupada de datos de la estructura INFO_PAGO_LINEA
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @param array $arrayParametros[
     *                              strFechaInicio           => Indica la fecha de inicio del reporte
     *                              strFechaFin              => Indica la fecha de fin del reporte
     *                              strTipoQuery             => Indica la forma de agrupacion del query
     *                              strCodEmpresa            => Indica el codigo de la empresa
     *                              arrayIdsCanalPagoLinea   => Indica Id o Id's de los canales de pagos en linea
     *                              strUsrCreacion           => Indica el usuario creacion de pagos en linea
                                    ]
     * 
     * @return array $arrayResponse[
     *                              arrayDatos  => Retorna el resultado del query creado segun los parametros enviados
     *                              strStatus   => Retorna el estatus del metodo 
     *                              ['000'  => 'No se realizó la consulta', 
     *                               '001'  =>  'Está enviando parámetros en blanco', 
     *                               '100'  => Consulta realizada con éxito ]
     *                              strMensaje  => Retorna un mensaje
     *                             ]
     */
    public function getResumenPagosLinea($arrayParametros){
        $arrayResponse = array();
        $arrayResponse['strStatus']  = '000';
        $arrayResponse['strMensaje'] = 'No se realizó la consulta';
        $arrayResponse['arrayDatos'] = '';
        $rsmBuilder                  = new ResultSetMappingBuilder($this->_em);
        $ntvQuery                    = $this->_em->createNativeQuery(null, $rsmBuilder);
        try 
        {
            //Termina el metodo si la fecha de inicio o fin estan vacias
            if(empty($arrayParametros['strFechaInicio']) || empty($arrayParametros['strFechaFin']))
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'Está enviando parámetros en blanco';
                return $arrayResponse; 
            }
            if("groupPorFecha" === $arrayParametros['strTipoQuery'])
            {
                $strSUMOpen     = 'SUM(';
                $strSUMClose    = ')';
            }
            $strQueryResumenPagosLinea = "WITH REPORTE_PAGOS_LINEA AS
                                            (SELECT ";
            //Concatena al query el campo FE_CREACION_CONCILIADO
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TRUNC(IPLC.FE_CREACION) FE_CREACION_CONCILIADO, "
                                            . " IPLC.CANAL_PAGO_LINEA_ID CANAL, ";
            } //Concatena al query el campo CANAL_PAGO_LINEA_ID
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.CANAL_PAGO_LINEA_ID CANAL, ";
            }
            //Concatena al query los campos [USR_CREACION, EMPRESA_ID, CANAL_PAGO_LINEA_ID]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.USR_CREACION USR_CREACION,
                                              IPLC.EMPRESA_ID EMPRESA, ";
            }
            $strQueryResumenPagosLinea .= "IPLC.ESTADO_PAGO_LINEA ESTADO_PAGO_LINEA,
                                              NVL2(COUNT(1), COUNT(1), 0) TOTAL
                                            FROM INFO_PAGO_LINEA IPLC
                                            WHERE IPLC.FE_CREACION BETWEEN TO_TIMESTAMP(:strFechaInicio, 'DD-MM-YYYY HH24:MI:SS') 
                                            AND TO_TIMESTAMP(:strFechaFin, 'DD-MM-YYYY HH24:MI:SS')
                                            GROUP BY ";
            //Agrupa el query por el campo FE_CREACION
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TRUNC(IPLC.FE_CREACION),"
                                           . " IPLC.CANAL_PAGO_LINEA_ID, ";
            } //Agrupa el query por el campo CANAL_PAGO_LINEA_ID
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.CANAL_PAGO_LINEA_ID, ";
            }
            //Agrupa el query por los campos [USR_CREACION, EMPRESA_ID, CANAL_PAGO_LINEA_ID]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.USR_CREACION,
                                               IPLC.EMPRESA_ID, ";
            }
            $strQueryResumenPagosLinea .= "IPLC.ESTADO_PAGO_LINEA
                                            ),
                                            TOTALES_VALORES AS
                                            (SELECT ";
            //Concatena el query por el campo FE_CREACION_CONCILIADO
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TRUNC(IPLC.FE_CREACION) FE_CREACION_CONCILIADO, "
                                           . " IPLC.CANAL_PAGO_LINEA_ID CANAL, ";
            } //Concatena el query por el campo CANAL_PAGO_LINEA_ID
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.CANAL_PAGO_LINEA_ID CANAL, ";
            }
            //Concatena el query por los campos [USR_CREACION, EMPRESA_ID, CANAL_PAGO_LINEA_ID]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.USR_CREACION USR_CREACION,
                                              IPLC.EMPRESA_ID EMPRESA, ";
            }
            $strQueryResumenPagosLinea .= "IPLC.ESTADO_PAGO_LINEA ESTADO_PAGO_LINEA,
                                            SUM(NVL2(IPLC.VALOR_PAGO_LINEA, IPLC.VALOR_PAGO_LINEA, 0)) VALOR_PAGO_LINEA
                                           FROM INFO_PAGO_LINEA IPLC
                                            WHERE IPLC.FE_CREACION BETWEEN TO_TIMESTAMP(:strFechaInicio, 'DD-MM-YYYY HH24:MI:SS') 
                                            AND TO_TIMESTAMP(:strFechaFin, 'DD-MM-YYYY HH24:MI:SS')
                                           GROUP BY ";
            //Agrupa el query por el campo FE_CREACION
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TRUNC(IPLC.FE_CREACION), "
                                           . " IPLC.CANAL_PAGO_LINEA_ID,";
            } //Agrupa el query por el campo CANAL_PAGO_LINEA_ID
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.CANAL_PAGO_LINEA_ID, ";
            } //Agrupa el query por los campos [USR_CREACION, EMPRESA_ID, CANAL_PAGO_LINEA_ID]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "IPLC.USR_CREACION,
                                               IPLC.EMPRESA_ID, ";
            }
            $strQueryResumenPagosLinea .= "IPLC.ESTADO_PAGO_LINEA
                                            )
                                          SELECT ";
            //Concatena el query por el campo FE_CREACION_CONCILIADO
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TO_DATE(TO_CHAR(TE.FE_CREACION_CONCILIADO, 'DD-MM-YYYY'), 'DD-MM-YYYY') FECHA_PAGO,";
                $rsmBuilder->addScalarResult('FECHA_PAGO', 'FECHA_PAGO', 'string');
            } //Concatena el query por el campo DESCRIPCION_CANAL_PAGO_LINEA y ID_CANAL
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "ACPL.DESCRIPCION_CANAL_PAGO_LINEA CANAL, "
                                            . "TV.CANAL ID_CANAL,";
                $rsmBuilder->addScalarResult('CANAL', 'CANAL', 'string');
                $rsmBuilder->addScalarResult('ID_CANAL', 'ID_CANAL', 'string');
            }
            /*Concatena el query por los campos 
             * [TO_DATE(TO_CHAR(TE.FE_CREACION_CONCILIADO, 'DD-MM-YYYY'), 'DD-MM-YYYY') || ' ' || ACPL.DESCRIPCION_CANAL_PAGO_LINEA,
             * EMPRESA,
             * CANAL,
             * NOMBRE_EMPRESA,
             * USR_CREACION]
             */
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TO_DATE(TO_CHAR(TE.FE_CREACION_CONCILIADO, 'DD-MM-YYYY'), 'DD-MM-YYYY') || ' ' || 
                                            ACPL.DESCRIPCION_CANAL_PAGO_LINEA FECHA_NOMBRE_CANAL,
                                            TV.EMPRESA COD_EMPRESA,
                                            TV.CANAL ID_CANAL,
                                            IEG.NOMBRE_EMPRESA EMPRESA,
                                            TE.USR_CREACION USR_CREACION,
                                            ACPL.DESCRIPCION_CANAL_PAGO_LINEA CANAL,";
                $rsmBuilder->addScalarResult('COD_EMPRESA', 'COD_EMPRESA', 'string');
                $rsmBuilder->addScalarResult('ID_CANAL', 'ID_CANAL', 'string');
                $rsmBuilder->addScalarResult('FECHA_NOMBRE_CANAL', 'FECHA_NOMBRE_CANAL', 'string');
                $rsmBuilder->addScalarResult('EMPRESA', 'EMPRESA', 'string');
                $rsmBuilder->addScalarResult('USR_CREACION', 'USR_CREACION', 'string');
                $rsmBuilder->addScalarResult('CANAL', 'CANAL', 'string');
            }
            $strQueryResumenPagosLinea .=
                                   "$strSUMOpen NVL2(TE.TOTAL_PENDIENTE, TE.TOTAL_PENDIENTE, 0) $strSUMClose TOTAL_PAGOS_PENDIENTE,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_PENDIENTE, TV.TOTAL_VALOR_PENDIENTE, 0) $strSUMClose TOTAL_VALOR_PENDIENTE,
                                    $strSUMOpen NVL2(TE.TOTAL_REVERSADO, TE.TOTAL_REVERSADO, 0) $strSUMClose TOTAL_PAGOS_REVERSADO,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_REVERSADO, TV.TOTAL_VALOR_REVERSADO, 0) $strSUMClose TOTAL_VALOR_REVERSADO,
                                    $strSUMOpen NVL2(TE.TOTAL_ELIMINADO, TE.TOTAL_ELIMINADO, 0) $strSUMClose TOTAL_PAGOS_ELIMINADO,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_ELIMINADO, TV.TOTAL_VALOR_ELIMINADO, 0) $strSUMClose TOTAL_VALOR_ELIMINADO,
                                    $strSUMOpen NVL2(TE.TOTAL_REVERSADO, TE.TOTAL_REVERSADO, 0)             + 
                                    NVL2(TE.TOTAL_ELIMINADO, TE.TOTAL_ELIMINADO, 0) $strSUMClose TOTAL_PAGOS_REVER_ELIMINADO,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_REVERSADO, TV.TOTAL_VALOR_REVERSADO, 0) + 
                                    NVL2(TV.TOTAL_VALOR_ELIMINADO, TV.TOTAL_VALOR_ELIMINADO, 0) $strSUMClose TOTAL_VALOR_REVER_ELIMINADO,
                                    $strSUMOpen NVL2(TE.TOTAL_CONCILIADO, TE.TOTAL_CONCILIADO, 0) $strSUMClose TOTAL_PAGOS_CONCILIADO,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_CONCILIADO, TV.TOTAL_VALOR_CONCILIADO, 0) $strSUMClose TOTAL_VALOR_CONCILIADO,
                                    $strSUMOpen NVL2(TV.TOTAL_VALOR_CONCILIADO, TV.TOTAL_VALOR_CONCILIADO, 0) + 
                                    NVL2(TV.TOTAL_VALOR_REVERSADO, TV.TOTAL_VALOR_REVERSADO, 0) + 
                                    NVL2(TV.TOTAL_VALOR_ELIMINADO, TV.TOTAL_VALOR_ELIMINADO, 0) + 
                                    NVL2(TV.TOTAL_VALOR_PENDIENTE, TV.TOTAL_VALOR_PENDIENTE, 0) $strSUMClose VALOR_TOTAL_TRANSACCIONES
                                  FROM REPORTE_PAGOS_LINEA PIVOT (SUM(TOTAL) FOR ESTADO_PAGO_LINEA     
                                  IN ('Pendiente' AS TOTAL_PENDIENTE, 
                                      'Reversado' AS TOTAL_REVERSADO, 
                                      'Eliminado' AS TOTAL_ELIMINADO, 
                                      'Conciliado' AS TOTAL_CONCILIADO)) TE, ";
            $rsmBuilder->addScalarResult('TOTAL_PAGOS_PENDIENTE', 'TOTAL_PAGOS_PENDIENTE', 'string');
            $rsmBuilder->addScalarResult('TOTAL_VALOR_PENDIENTE', 'TOTAL_VALOR_PENDIENTE', 'string');
            $rsmBuilder->addScalarResult('TOTAL_PAGOS_REVERSADO', 'TOTAL_PAGOS_REVERSADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_VALOR_REVERSADO', 'TOTAL_VALOR_REVERSADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_PAGOS_ELIMINADO', 'TOTAL_PAGOS_ELIMINADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_VALOR_ELIMINADO', 'TOTAL_VALOR_ELIMINADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_PAGOS_REVER_ELIMINADO', 'TOTAL_PAGOS_REVER_ELIMINADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_VALOR_REVER_ELIMINADO', 'TOTAL_VALOR_REVER_ELIMINADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_PAGOS_CONCILIADO', 'TOTAL_PAGOS_CONCILIADO', 'string');
            $rsmBuilder->addScalarResult('TOTAL_VALOR_CONCILIADO', 'TOTAL_VALOR_CONCILIADO', 'string');
            $rsmBuilder->addScalarResult('VALOR_TOTAL_TRANSACCIONES', 'VALOR_TOTAL_TRANSACCIONES', 'string');
            //Concatena las tablas [ADMI_CANAL_PAGO_LINEA, INFO_EMPRESA_GRUPO]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "ADMI_CANAL_PAGO_LINEA ACPL,
                                               INFO_EMPRESA_GRUPO IEG,";
            }
            //Concatena la tabla ADMI_CANAL_PAGO_LINEA
            if("groupPorCanal" === $arrayParametros['strTipoQuery'] || "groupPorFecha" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "ADMI_CANAL_PAGO_LINEA ACPL,";
            }
            $strQueryResumenPagosLinea .= "TOTALES_VALORES PIVOT (SUM(VALOR_PAGO_LINEA) FOR ESTADO_PAGO_LINEA 
                                            IN ('Pendiente' AS TOTAL_VALOR_PENDIENTE, 
                                                'Reversado' AS TOTAL_VALOR_REVERSADO, 
                                                'Eliminado' AS TOTAL_VALOR_ELIMINADO, 
                                                'Conciliado' AS TOTAL_VALOR_CONCILIADO)) TV 
                                          WHERE ";
            //Agrega al where la condicion TV.FE_CREACION_CONCILIADO = TE.FE_CREACION_CONCILIADO
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TV.FE_CREACION_CONCILIADO = TE.FE_CREACION_CONCILIADO"
                                           . " AND TV.CANAL                  = TE.CANAL "
                                           . " AND ACPL.ID_CANAL_PAGO_LINEA  = TV.CANAL ";
            } //Agrega al where las condiciones ACPL.ID_CANAL_PAGO_LINEA  = TV.CANAL AND TV.CANAL = TE.CANAL
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= " ACPL.ID_CANAL_PAGO_LINEA  = TV.CANAL "
                                            . " AND TV.CANAL              = TE.CANAL ";
            }
            /*Agrega al where las condiciones
             * AND IEG.COD_EMPRESA           = TV.EMPRESA
               AND ACPL.ID_CANAL_PAGO_LINEA  = TV.CANAL
               AND TV.EMPRESA                = TE.EMPRESA
               AND TV.USR_CREACION           = TE.USR_CREACION
             */
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "AND IEG.COD_EMPRESA           = TV.EMPRESA 
                                               AND TV.EMPRESA                = TE.EMPRESA 
                                               AND TV.USR_CREACION           = TE.USR_CREACION  ";
            }
            //Agrega al where la condicion para la busqueda por el campo EMPRESA
            if(!empty($arrayParametros['strCodEmpresa']) && ("groupPorFecha" !== $arrayParametros['strTipoQuery'] &&
               "groupPorCanal" !== $arrayParametros['strTipoQuery']))
            {
                $strQueryResumenPagosLinea .= " AND TV.EMPRESA = :strCodEmpresa ";
                $ntvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
            }
            //Agrega al where la condicion para la busqueda por el campo CANAL
            if(!empty($arrayParametros['arrayIdsCanalPagoLinea']))
            {
                $strQueryResumenPagosLinea .= " AND TV.CANAL IN (:arrayIdsCanalPagoLinea) ";
                $ntvQuery->setParameter('arrayIdsCanalPagoLinea', $arrayParametros['arrayIdsCanalPagoLinea']);
            }
            //Agrega al where la condicion para la busqueda por el campo USR_CREACION
            if(!empty($arrayParametros['strUsrCreacion']) && ("groupPorFecha" !== $arrayParametros['strTipoQuery'] &&
               "groupPorCanal" !== $arrayParametros['strTipoQuery']))
            {
                $strQueryResumenPagosLinea .= " AND TV.USR_CREACION = :strUsrCreacion ";
                $ntvQuery->setParameter('strUsrCreacion', $arrayParametros['strUsrCreacion']);
            }
            if("groupPorFecha" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= " GROUP BY TO_DATE(TO_CHAR(TE.FE_CREACION_CONCILIADO, 'DD-MM-YYYY'), 'DD-MM-YYYY') ";
            }
            $strQueryResumenPagosLinea .= " ORDER BY ";
            //Concatena al ORDER BY por el campo FE_CREACION_CONCILIADO
            if("groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "TO_DATE(TO_CHAR(TE.FE_CREACION_CONCILIADO, 'DD-MM-YYYY'), 'DD-MM-YYYY') ";
            } //Concatena al ORDER BY por el campo DESCRIPCION_CANAL_PAGO_LINEA
            else if("groupPorCanal" === $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= "ACPL.DESCRIPCION_CANAL_PAGO_LINEA ";
            }
            //Concatena al ORDER BY los campos [NOMBRE_EMPRESA, DESCRIPCION_CANAL_PAGO_LINEA]
            if("groupPorFecha" !== $arrayParametros['strTipoQuery'] && "groupPorCanal" !== $arrayParametros['strTipoQuery'])
            {
                $strQueryResumenPagosLinea .= " ,IEG.NOMBRE_EMPRESA, "
                                            . " ACPL.DESCRIPCION_CANAL_PAGO_LINEA";
            }
            $ntvQuery->setParameter('strFechaInicio', $arrayParametros['strFechaInicio']);
            $ntvQuery->setParameter('strFechaFin', $arrayParametros['strFechaFin']);
            $ntvQuery->setSQL($strQueryResumenPagosLinea);
            $arrayResponse['arrayDatos'] = $ntvQuery->getResult();
            $arrayResponse['strStatus']  = '100';
            $arrayResponse['strMensaje'] = 'Consulta realizada con éxito';
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strStatus']  = '001';
            $arrayResponse['strMensaje'] = 'Error: '.$ex->getMessage();
        }
        return $arrayResponse;
    }//getResumenPagosLinea

    /**
     * exportResumenPagosLinea, metodo que crea el query para la obtencion de datos de la estructura INFO_PAGO_LINEA
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @param array $arrayParametros[
     *                              strFechaInicio           => Indica la fecha de inicio del reporte
     *                              strFechaFin              => Indica la fecha de fin del reporte
     *                              arrayStrCodEmpresa       => Indica el codigo o codigos de empresa
     *                              arrayIntCanalPagoLinea   => Indica Id o Id's de los canales de pagos en linea

                                    ]
     * 
     * @return array $arrayResponse[
     *                              arrayDatos  => Retorna el resultado del query creado segun los parametros enviados
     *                              strStatus   => Retorna el estatus del metodo 
     *                              ['000'  => 'No se realizó la consulta', 
     *                               '001'  =>  'Está enviando parámetros en blanco', 
     *                               '100'  => Consulta realizada con éxito ]
     *                              strMensaje  => Retorna un mensaje
     *                             ]
     */
    public function exportResumenPagosLinea($arrayParametros){
        $arrayResponse = array();
        $arrayResponse['strStatus']  = '000';
        $arrayResponse['strMensaje'] = 'No se realizó la consulta';
        $arrayResponse['arrayDatos'] = '';
        $rsmBuilder                  = new ResultSetMappingBuilder($this->_em);
        $ntvQuery                    = $this->_em->createNativeQuery(null, $rsmBuilder);
        $strQueryResumenPagos        = "WITH IDENTIFICAICON_PERSONA AS
                                            (SELECT IPN.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE,
                                              NVL2(IPN.RAZON_SOCIAL, IPN.RAZON_SOCIAL, NVL2(TRIM(IPN.NOMBRES
                                              || ' '
                                              || IPN.APELLIDOS), TRIM(IPN.NOMBRES
                                              || ' '
                                              || IPN.APELLIDOS), IPN.REPRESENTANTE_LEGAL)) NOMBRE_PERSONA,
                                              IPN.ID_PERSONA
                                            FROM INFO_PERSONA IPN
                                            )
                                          SELECT IP.IDENTIFICACION_CLIENTE,
                                            IP.NOMBRE_PERSONA,
                                            ACPL.DESCRIPCION_CANAL_PAGO_LINEA,
                                            IEG.NOMBRE_EMPRESA,
                                            IPL.ID_PAGO_LINEA,
                                            IPL.CANAL_PAGO_LINEA_ID,
                                            IPL.EMPRESA_ID,
                                            IPL.OFICINA_ID,
                                            IPL.PERSONA_ID,
                                            IPL.VALOR_PAGO_LINEA,
                                            IPL.NUMERO_REFERENCIA,
                                            REPLACE(IPL.ESTADO_PAGO_LINEA, 'Eliminado', 'Reversado') ESTADO_PAGO_LINEA,
                                            IPL.COMENTARIO_PAGO_LINEA,
                                            IPL.USR_CREACION,
                                            IPL.FE_CREACION,
                                            IPL.USR_ULT_MOD,
                                            IPL.FE_ULT_MOD,
                                            IPL.USR_ELIMINACION,
                                            IPL.FE_ELIMINACION,
                                            IPL.PROCESO_MASIVO_ID,
                                            IPL.FE_TRANSACCION
                                          FROM INFO_PAGO_LINEA IPL
                                          LEFT JOIN IDENTIFICAICON_PERSONA IP
                                          ON IP.ID_PERSONA = IPL.PERSONA_ID,
                                            ADMI_CANAL_PAGO_LINEA ACPL,
                                            INFO_EMPRESA_GRUPO IEG
                                          WHERE IPL.FE_CREACION BETWEEN TO_TIMESTAMP(:strFechaInicio , 'DD-MM-YYYY HH24:MI:SS') 
                                          AND TO_TIMESTAMP(:strFechaFin, 'DD-MM-YYYY HH24:MI:SS')
                                          AND ACPL.ID_CANAL_PAGO_LINEA = IPL.CANAL_PAGO_LINEA_ID
                                          AND IEG.COD_EMPRESA          = IPL.EMPRESA_ID ";
        
        try
        {
            //Termina el metodo si la fecha de inicio o fin estan vacias
            if(empty($arrayParametros['strFechaInicio']) || empty($arrayParametros['strFechaFin']))
            {
                $arrayResponse['strStatus']  = '001';
                $arrayResponse['strMensaje'] = 'Está enviando parámetros en blanco';
                return $arrayResponse; 
            }
            $rsmBuilder->addScalarResult('IDENTIFICACION_CLIENTE', 'IDENTIFICACION_CLIENTE', 'string');
            $rsmBuilder->addScalarResult('NOMBRE_PERSONA', 'NOMBRE_PERSONA', 'string');
            $rsmBuilder->addScalarResult('DESCRIPCION_CANAL_PAGO_LINEA', 'DESCRIPCION_CANAL_PAGO_LINEA', 'string');
            $rsmBuilder->addScalarResult('NOMBRE_EMPRESA', 'NOMBRE_EMPRESA', 'string');
            $rsmBuilder->addScalarResult('ID_PAGO_LINEA', 'ID_PAGO_LINEA', 'string');
            $rsmBuilder->addScalarResult('CANAL_PAGO_LINEA_ID', 'CANAL_PAGO_LINEA_ID', 'string');
            $rsmBuilder->addScalarResult('EMPRESA_ID', 'EMPRESA_ID', 'string');
            $rsmBuilder->addScalarResult('OFICINA_ID', 'OFICINA_ID', 'string');
            $rsmBuilder->addScalarResult('PERSONA_ID', 'PERSONA_ID', 'string');
            $rsmBuilder->addScalarResult('VALOR_PAGO_LINEA', 'VALOR_PAGO_LINEA', 'string');
            $rsmBuilder->addScalarResult('NUMERO_REFERENCIA', 'NUMERO_REFERENCIA', 'string');
            $rsmBuilder->addScalarResult('ESTADO_PAGO_LINEA', 'ESTADO_PAGO_LINEA', 'string');
            $rsmBuilder->addScalarResult('COMENTARIO_PAGO_LINEA', 'COMENTARIO_PAGO_LINEA', 'string');
            $rsmBuilder->addScalarResult('USR_CREACION', 'USR_CREACION', 'string');
            $rsmBuilder->addScalarResult('FE_CREACION', 'FE_CREACION', 'string');
            $rsmBuilder->addScalarResult('USR_ULT_MOD', 'USR_ULT_MOD', 'string');
            $rsmBuilder->addScalarResult('FE_ULT_MOD', 'FE_ULT_MOD', 'string');
            $rsmBuilder->addScalarResult('USR_ELIMINACION', 'USR_ELIMINACION', 'string');
            $rsmBuilder->addScalarResult('FE_ELIMINACION', 'FE_ELIMINACION', 'string');
            $rsmBuilder->addScalarResult('PROCESO_MASIVO_ID', 'PROCESO_MASIVO_ID', 'string');
            $rsmBuilder->addScalarResult('FE_TRANSACCION', 'FE_TRANSACCION', 'string');
            //Concatena al where AND IPL.CANAL_PAGO_LINEA_ID IN (:arrayIntCanalPagoLinea)
            if(!empty($arrayParametros['arrayIntCanalPagoLinea']))
            {
                $strQueryResumenPagos .= "AND IPL.CANAL_PAGO_LINEA_ID IN (:arrayIntCanalPagoLinea)";
                $ntvQuery->setParameter('arrayIntCanalPagoLinea', $arrayParametros['arrayIntCanalPagoLinea']);
                
            }
            //Concatena al where AND IPL.EMPRESA_ID IN (:arrayStrCodEmpresa)
            if(!empty($arrayParametros['arrayStrCodEmpresa']))
            {
                $strQueryResumenPagos .= "AND IPL.EMPRESA_ID IN (:arrayStrCodEmpresa) ";
                $ntvQuery->setParameter('arrayStrCodEmpresa', $arrayParametros['arrayStrCodEmpresa']);
            }
            $ntvQuery->setParameter('strFechaInicio', $arrayParametros['strFechaInicio']);
            $ntvQuery->setParameter('strFechaFin', $arrayParametros['strFechaFin']);
            $ntvQuery->setSQL($strQueryResumenPagos);
            $arrayResponse['arrayDatos'] = $ntvQuery->getResult();
            $arrayResponse['strStatus']  = '100';
            $arrayResponse['strMensaje'] = 'Consulta realizada con éxito';
        } 
        catch (\Exception $ex) 
        {
            $arrayResponse['strStatus']  = '001';
            $arrayResponse['strMensaje'] = 'Error: '.$ex->getMessage();
        }
        return $arrayResponse;
    }//exportResumenPagosLinea
}
