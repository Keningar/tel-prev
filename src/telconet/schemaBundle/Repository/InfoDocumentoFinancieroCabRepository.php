<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoDocumentoFinancieroCabRepository extends BaseRepository
{
    /**
     * Documentación para el método 'getFechasDiasPeriodo'.
     * 
     * Método que retorna el período de facturación dependiendo de la fecha de activación o inicio a facturar de un cliente
     *
     * @param  array $arrayParametros ['strEmpresaCod'      => 'Código de la empresa a validar',
     *                                 'strFechaActivacion' => 'Fecha de activación o inicio a facturar de un cliente']
     * @return array $arrayResultados ['strFechaInicioPeriodo' => 'Texto que indica si se debe modificar la cabecera del documento',
     *                                 'strFechaFinPeriodo'    => 'Valor que debe ser sumado al subtotal de impuestos',
     *                                 'intTotalDiasMes'       => 'Cantidad de días mensuales',
     *                                 'intTotalDiasRestantes' => 'Cantidad de días restantes' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-06-2017
     */
    public function getFechasDiasPeriodo($arrayParametros)
    {
        $arrayResultados = array('strFechaInicioPeriodo' => '', 
                                 'strFechaFinPeriodo'    => '', 
                                 'intTotalDiasMes'       => 0,
                                 'intTotalDiasRestantes' => 0);
        
        try
        {
            $strEmpresaCod      = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) ) 
                                   ? $arrayParametros['strEmpresaCod'] : '';
            $strFechaActivacion = ( isset($arrayParametros['strFechaActivacion']) && !empty($arrayParametros['strFechaActivacion']) ) 
                                   ? $arrayParametros['strFechaActivacion'] : '';
            
            if( !empty($strEmpresaCod) && !empty($strFechaActivacion) )
            {
                $strFechaInicioPeriodo = '';
                $strFechaInicioPeriodo = str_pad($strFechaInicioPeriodo, 15, " ");
                $strFechaFinPeriodo    = '';
                $strFechaFinPeriodo    = str_pad($strFechaFinPeriodo, 15, " ");
                $intTotalDiasMes       = '';
                $intTotalDiasMes       = str_pad($intTotalDiasMes, 3, " ");
                $intTotalDiasRestantes = '';
                $intTotalDiasRestantes = str_pad($intTotalDiasRestantes, 3, " ");
                
                $strSql = "BEGIN DB_FINANCIERO.FNCK_CONSULTS.P_GET_FECHAS_DIAS_PERIODO( :strEmpresaCod, ".
                                                                                       ":strFechaActivacion, ".
                                                                                       ":strFechaInicioPeriodo, ".
                                                                                       ":strFechaFinPeriodo, ".
                                                                                       ":intTotalDiasMes, ".
                                                                                       ":intTotalDiasRestantes ); END;";
                $stmt   = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('strEmpresaCod',         $strEmpresaCod);
                $stmt->bindParam('strFechaActivacion',    $strFechaActivacion);
                $stmt->bindParam('strFechaInicioPeriodo', $strFechaInicioPeriodo);
                $stmt->bindParam('strFechaFinPeriodo',    $strFechaFinPeriodo);
                $stmt->bindParam('intTotalDiasMes',       $intTotalDiasMes);
                $stmt->bindParam('intTotalDiasRestantes', $intTotalDiasRestantes);
                $stmt->execute();
                
                $strFechaInicioPeriodo = trim($strFechaInicioPeriodo);
                $strFechaFinPeriodo    = trim($strFechaFinPeriodo);
                $intTotalDiasMes       = intval(trim($intTotalDiasMes));
                $intTotalDiasRestantes = intval(trim($intTotalDiasRestantes));
                
                $arrayResultados['strFechaInicioPeriodo'] = !empty($strFechaInicioPeriodo) ? $strFechaInicioPeriodo : '';
                $arrayResultados['strFechaFinPeriodo']    = !empty($strFechaFinPeriodo) ? $strFechaFinPeriodo : '';
                $arrayResultados['intTotalDiasMes']       = !empty($intTotalDiasMes) ? $intTotalDiasMes : 0;
                $arrayResultados['intTotalDiasRestantes'] = !empty($intTotalDiasRestantes) ? $intTotalDiasRestantes : 0;
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para obtener el período de fechas para la facturación. '.
                                     'EmpresaCod('.$strEmpresaCod.'), FechaActivacion('.$strFechaActivacion.')');
            }//( !empty($strEmpresaCod) && !empty($strFechaActivacion) )
        }
        catch(\Exception $e)
        {
            throw ($e);
        }
            
        return $arrayResultados;
    }


    /**
     * Documentación para el método 'getReprocesamientoContable'.
     * 
     * Método que invoca el procedimiento 'DB_FINANCIERO.FNCK_TRANSACTION.P_REPROCESAMIENTO_CONTABLE' que reprocesa la información contable
     * dependiendo de lo seleccionado por el usuario
     *
     * @param  array  $arrayParametros ['strEmpresaCod'                   => 'Código de la empresa en sessión',
                                        'strPrefijoEmpresa'               => 'Prefijo de la empresa en sessión',
                                        'strCodigoTipoDocumento'          => 'Código del tipo de documento a reprocesar',
                                        'strCodigoDiario'                 => 'Código del asiento contable',
                                        'strActualizarContabilizado'      => 'Bandera que indica si se debe actualizar el campo de CONTABILIZADO',
                                        'strFechaReprocesamientoContable' => 'Fecha de la información a reprocesar',
                                        'strUsuario'                      => 'Usuario en sessión',
                                        'strTipoProceso'                  => 'Tipo de proceso a contabilizar']
     * @return String $strMensajeProceso
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2017
     */
    public function getReprocesamientoContable($arrayParametros)
    {
        $strMensajeProceso = str_pad($strMensajeProceso, 3000, " ");
        
        try
        {
            $strEmpresaCod                   = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
                                               ? $arrayParametros['strEmpresaCod'] : '';
            $strPrefijoEmpresa               = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) ) 
                                               ? $arrayParametros['strPrefijoEmpresa'] : '';
            $strCodigoTipoDocumento          = ( isset($arrayParametros['strCodigoTipoDocumento']) 
                                                 && !empty($arrayParametros['strCodigoTipoDocumento']) )
                                               ? $arrayParametros['strCodigoTipoDocumento'] : '';
            $strCodigoDiario                 = ( isset($arrayParametros['strCodigoDiario']) && !empty($arrayParametros['strCodigoDiario']) )
                                               ? $arrayParametros['strCodigoDiario'] : '';
            $strActualizarContabilizado      = ( isset($arrayParametros['strActualizarContabilizado']) 
                                                 && !empty($arrayParametros['strActualizarContabilizado']) )
                                               ? $arrayParametros['strActualizarContabilizado'] : '';
            $strFechaReprocesamientoContable = ( isset($arrayParametros['strFechaReprocesamientoContable']) 
                                                 && !empty($arrayParametros['strFechaReprocesamientoContable']) ) 
                                               ? $arrayParametros['strFechaReprocesamientoContable'] : '';
            $strUsuario                      = ( isset($arrayParametros['strUsuario']) && !empty($arrayParametros['strUsuario']) )
                                               ? $arrayParametros['strUsuario'] : '';
            $strTipoProceso                  = ( isset($arrayParametros['strTipoProceso']) && !empty($arrayParametros['strTipoProceso']) )
                                               ? $arrayParametros['strTipoProceso'] : '';
            
            if(!empty($strEmpresaCod) && !empty($strPrefijoEmpresa) && !empty($strCodigoTipoDocumento) && !empty($strCodigoDiario)
               && !empty($strFechaReprocesamientoContable) && !empty($strUsuario) && !empty($strTipoProceso) && !empty($strActualizarContabilizado))
            {
                $strSql = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_REPROCESAMIENTO_CONTABLE( :strEmpresaCod, ".
                                                                                           ":strPrefijoEmpresa, ".
                                                                                           ":strCodigoTipoDocumento, ".
                                                                                           ":strCodigoDiario, ".
                                                                                           ":strActualizarContabilizado, ".
                                                                                           ":strFechaReprocesamientoContable, ".
                                                                                           ":strUsuario, ".
                                                                                           ":strTipoProceso, ".
                                                                                           ":strMensajeProceso ); END;";
                $stmt   = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('strEmpresaCod',                   $strEmpresaCod);
                $stmt->bindParam('strPrefijoEmpresa',               $strPrefijoEmpresa);
                $stmt->bindParam('strCodigoTipoDocumento',          $strCodigoTipoDocumento);
                $stmt->bindParam('strCodigoDiario',                 $strCodigoDiario);
                $stmt->bindParam('strActualizarContabilizado',      $strActualizarContabilizado);
                $stmt->bindParam('strFechaReprocesamientoContable', $strFechaReprocesamientoContable);
                $stmt->bindParam('strUsuario',                      $strUsuario);
                $stmt->bindParam('strTipoProceso',                  $strTipoProceso);
                $stmt->bindParam('strMensajeProceso',               $strMensajeProceso);
                $stmt->execute();
            }//(!empty($strEmpresaCod) && !empty($strPrefijoEmpresa) && !empty($strCodigoTipoDocumento) && !empty($strCodigoDiario)...
            else
            {
                throw new \Exception('Todos los parámetros son obligatorios para poder reprocesar la información contable.');
            }
        }
        catch(\Exception $e)
        {
            throw ($e);
        }
            
        return $strMensajeProceso;
    }
    
    
    /**
     * Documentación para el método 'getValidadorDocumentosFinancieros'.
     * 
     * Retorna dos variables que indican una bandera y una diferencia la cual se debe agregar al subtotal de impuestos de la cabecera del documento
     * financiero
     *
     * @param  array $arrayParametros ['intIdDocumento' => 'Id del documento a consultar la informacion']
     * @return array $arrayResultados ['strValidador'             => 'Texto que indica si se debe modificar la cabecera del documento',
     *                                 'floatDiferenciaImpuestos' => 'Valor que debe ser sumado al subtotal de impuestos' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 24-01-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-02-2017 - Se corrige que el EXCEPTION cuando no se encuentra 'idDocumento'
     */
    public function getValidadorDocumentosFinancieros($arrayParametros)
    {
        $arrayResultados = array('strValidador' => 'N', 'floatDiferenciaImpuestos' => 0);
        
        try
        {
            $intIdDocumento = ( isset($arrayParametros['intIdDocumento']) && !empty($arrayParametros['intIdDocumento']) )
                              ? $arrayParametros['intIdDocumento'] : 0;
            
            if( !empty($intIdDocumento) && $intIdDocumento > 0 )
            {
                $strValidador             = str_pad($strValidador, 5, " ");
                $floatDiferenciaImpuestos = str_pad($floatDiferenciaImpuestos, 5, " ");
                
                $strSql = "BEGIN DB_FINANCIERO.FNCK_CONSULTS.P_VALIDADOR_DOCUMENTOS( :intIdDocumento, ".
                                                                                    ":strValidador, ".
                                                                                    ":floatDiferenciaImpuestos ); END;";
                $stmt   = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('intIdDocumento',           $intIdDocumento);
                $stmt->bindParam('strValidador',             $strValidador);
                $stmt->bindParam('floatDiferenciaImpuestos', $floatDiferenciaImpuestos);
                $stmt->execute();
                
                $strValidador             = trim($strValidador);
                $floatDiferenciaImpuestos = trim($floatDiferenciaImpuestos);
                
                $arrayResultados['strValidador']             = !empty($strValidador) ? $strValidador : 'N';
                $arrayResultados['floatDiferenciaImpuestos'] = !empty($floatDiferenciaImpuestos) ? $floatDiferenciaImpuestos : 0;
            }//( !empty($intIdDocumento) && $intIdDocumento > 0 )
            else
            {
                throw new \Exception('No se encontró documento para validar la información.');
            }
        }
        catch(\Exception $e)
        {
            throw ($e);
        }
            
        return $arrayResultados;
    }
    
    /**
     * Permite listar las facturas dependiendo de los parámetros enviados por el usuario
     *
     * @param array $arrayParametros['arrayPuntos'            Id puntos a los cuales pertenece el documento 
     *                               'arrayTipoDocumento'     Código con los documentos que se desean buscar
     *                               'arrayInEstados'         Estados de los documentos que se desean buscar
     *                               'orderBy'                Sentido y campos por los cuales se ordenará la información
     *                               'strCodEmpresa'          Código de la empresa en sessión del usuario ]
     * 
     * @return array $arrayDocumentosFinancieros
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-10-2016
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 08-04-2020 - Se añade query para obtener las NDI con caracteristica de Diferido,
     *                           presentandolas como prioridad antes las otras facturas si corresponden a la misma fecha de creación.
     * Costo Query: 69
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 09-07-2020 - Se modifica query principal para eliminar sentencia TRUNC del ordenamiento por fecha y se añade
     *                           sentencia UNION ALL.
     * Costo Query: 33
     * 
     */
    public function findDocumentosFinancieros($arrayParametros)
    {
        $arrayDocumentosFinancieros = array();
                
        $objRsm       = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSelect    = "SELECT IDFC.ID_DOCUMENTO, IDFC.OFICINA_ID, IDFC.PUNTO_ID, IDFC.TIPO_DOCUMENTO_ID, IDFC.NUMERO_FACTURA_SRI, IDFC.SUBTOTAL, 
                           IDFC.SUBTOTAL_CERO_IMPUESTO, IDFC.SUBTOTAL_CON_IMPUESTO, IDFC.SUBTOTAL_DESCUENTO, IDFC.VALOR_TOTAL,
                           IDFC.ENTREGO_RETENCION_FTE, IDFC.ESTADO_IMPRESION_FACT, IDFC.ES_AUTOMATICA, IDFC.PRORRATEO, IDFC.REACTIVACION, 
                           IDFC.RECURRENTE, IDFC.COMISIONA, IDFC.FE_CREACION, IDFC.FE_EMISION, IDFC.USR_CREACION, IDFC.NUM_FACT_MIGRACION, 
                           IDFC.OBSERVACION, IDFC.REFERENCIA_DOCUMENTO_ID, IDFC.ES_ELECTRONICA, IDFC.FE_AUTORIZACION, IDFC.MES_CONSUMO, 
                           IDFC.ANIO_CONSUMO, IDFC.RANGO_CONSUMO, IDFC.DESCUENTO_COMPENSACION ";
        
        $strFrom      = "FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  IDFC,
                           DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF,
                           DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ";
        
        $strWhere     = "WHERE IDFC.NUMERO_FACTURA_SRI IS NOT NULL
                           AND IDFC.TIPO_DOCUMENTO_ID  = ATDF.ID_TIPO_DOCUMENTO
                           AND IDFC.OFICINA_ID         = IOG.ID_OFICINA
                           AND IOG.EMPRESA_ID          = :strCodEmpresa
                           AND IDFC.PUNTO_ID           IN (:arrayPuntos) ";
        
        $strSelectNdi = "SELECT IDFC.ID_DOCUMENTO, IDFC.OFICINA_ID, IDFC.PUNTO_ID, IDFC.TIPO_DOCUMENTO_ID, IDFC.NUMERO_FACTURA_SRI, IDFC.SUBTOTAL, 
                            IDFC.SUBTOTAL_CERO_IMPUESTO, IDFC.SUBTOTAL_CON_IMPUESTO, IDFC.SUBTOTAL_DESCUENTO, IDFC.VALOR_TOTAL, 
                            IDFC.ENTREGO_RETENCION_FTE, IDFC.ESTADO_IMPRESION_FACT, IDFC.ES_AUTOMATICA, IDFC.PRORRATEO, IDFC.REACTIVACION, 
                            IDFC.RECURRENTE, IDFC.COMISIONA, IDFC.FE_CREACION, IDFC.FE_EMISION, IDFC.USR_CREACION, IDFC.NUM_FACT_MIGRACION, 
                            IDFC.OBSERVACION, IDFC.REFERENCIA_DOCUMENTO_ID, IDFC.ES_ELECTRONICA, IDFC.FE_AUTORIZACION, IDFC.MES_CONSUMO, 
                            IDFC.ANIO_CONSUMO, IDFC.RANGO_CONSUMO, IDFC.DESCUENTO_COMPENSACION ";
        
        $strFromNdi   = "FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,
                           DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                           DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF,
                           DB_COMERCIAL.INFO_OFICINA_GRUPO IOG,
                           DB_COMERCIAL.ADMI_CARACTERISTICA AC ";
        
        $strWhereNdi  = "WHERE IDFC.NUMERO_FACTURA_SRI  IS NOT NULL
                           AND IDC.DOCUMENTO_ID         = IDFC.ID_DOCUMENTO
                           AND IDFC.TIPO_DOCUMENTO_ID   = ATDF.ID_TIPO_DOCUMENTO
                           AND IDFC.OFICINA_ID          = IOG.ID_OFICINA
                           AND IDC.CARACTERISTICA_ID    = AC.ID_CARACTERISTICA   
                           AND IOG.EMPRESA_ID           = :strCodEmpresa
                           AND IDFC.PUNTO_ID            IN (:arrayPuntos) ";
            
        $objQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        $objQuery->setParameter('arrayPuntos',   array_values($arrayParametros['arrayPuntos']));

        if( !empty($arrayParametros['arrayTipoDocumento']) )
        {
            $strWhere    .= "AND ATDF.CODIGO_TIPO_DOCUMENTO IN (:arrayTipoDocumento) ";
            $strWhereNdi .= "AND ATDF.CODIGO_TIPO_DOCUMENTO = :strTipoDocumentoNdi AND AC.DESCRIPCION_CARACTERISTICA = :strCaracteristicaNdi "
                           ."AND IDC.VALOR = :strValorCaracteristicaNdi ";
            $objQuery->setParameter('arrayTipoDocumento', array_values($arrayParametros['arrayTipoDocumento']));
            $objQuery->setParameter('strTipoDocumentoNdi', 'NDI');
            $objQuery->setParameter('strCaracteristicaNdi', 'PROCESO_DIFERIDO');
            $objQuery->setParameter('strValorCaracteristicaNdi', 'S');
        }

        if( !empty($arrayParametros['arrayInEstados']) )
        {
            $strWhere .= "AND IDFC.ESTADO_IMPRESION_FACT IN (:arrayInEstados) ";
            $strWhereNdi .= "AND IDFC.ESTADO_IMPRESION_FACT IN (:arrayInEstados) ";
            $objQuery->setParameter('arrayInEstados', array_values($arrayParametros['arrayInEstados']));
        }

        $strSql         = $strSelect.$strFrom.$strWhere;
        $strSqlNdi      = $strSelectNdi.$strFromNdi.$strWhereNdi;
        
        $strSelectTable = "SELECT TBL_TEMP_IDFC.ID_DOCUMENTO, TBL_TEMP_IDFC.OFICINA_ID, TBL_TEMP_IDFC.PUNTO_ID, TBL_TEMP_IDFC.TIPO_DOCUMENTO_ID,
                                  TBL_TEMP_IDFC.NUMERO_FACTURA_SRI, TBL_TEMP_IDFC.SUBTOTAL, TBL_TEMP_IDFC.SUBTOTAL_CERO_IMPUESTO,
                                  TBL_TEMP_IDFC.SUBTOTAL_CON_IMPUESTO, TBL_TEMP_IDFC.SUBTOTAL_DESCUENTO, TBL_TEMP_IDFC.VALOR_TOTAL, 
                                  TBL_TEMP_IDFC.ENTREGO_RETENCION_FTE, TBL_TEMP_IDFC.ESTADO_IMPRESION_FACT, TBL_TEMP_IDFC.ES_AUTOMATICA, 
                                  TBL_TEMP_IDFC.PRORRATEO, TBL_TEMP_IDFC.REACTIVACION, TBL_TEMP_IDFC.RECURRENTE, TBL_TEMP_IDFC.COMISIONA, 
                                  TBL_TEMP_IDFC.FE_CREACION, TBL_TEMP_IDFC.FE_EMISION, TBL_TEMP_IDFC.USR_CREACION, TBL_TEMP_IDFC.NUM_FACT_MIGRACION,
                                  TBL_TEMP_IDFC.OBSERVACION, TBL_TEMP_IDFC.REFERENCIA_DOCUMENTO_ID, TBL_TEMP_IDFC.ES_ELECTRONICA,
                                  TBL_TEMP_IDFC.FE_AUTORIZACION, TBL_TEMP_IDFC.MES_CONSUMO, TBL_TEMP_IDFC.ANIO_CONSUMO, TBL_TEMP_IDFC.RANGO_CONSUMO,
                                  TBL_TEMP_IDFC.DESCUENTO_COMPENSACION FROM ( " . $strSql . " UNION ALL " . $strSqlNdi .") TBL_TEMP_IDFC "
                                  . "ORDER BY TBL_TEMP_IDFC.FE_CREACION ASC, TBL_TEMP_IDFC.TIPO_DOCUMENTO_ID desc ";

        $objRsm->addEntityResult('telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab', 'IDFC'); 
        $objRsm->addFieldResult('IDFC', 'ID_DOCUMENTO', 'id');
        $objRsm->addFieldResult('IDFC', 'OFICINA_ID', 'oficinaId');
        $objRsm->addFieldResult('IDFC', 'PUNTO_ID', 'puntoId');
        $objRsm->addJoinedEntityResult('telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero' , 'ATDF', 'IDFC', 'tipoDocumentoId');
        $objRsm->addFieldResult('ATDF', 'TIPO_DOCUMENTO_ID', 'id');
        $objRsm->addFieldResult('IDFC', 'NUMERO_FACTURA_SRI', 'numeroFacturaSri');
        $objRsm->addFieldResult('IDFC', 'SUBTOTAL', 'subtotal');
        $objRsm->addFieldResult('IDFC', 'SUBTOTAL_CERO_IMPUESTO', 'subtotalCeroImpuesto');
        $objRsm->addFieldResult('IDFC', 'SUBTOTAL_CON_IMPUESTO', 'subtotalConImpuesto');
        $objRsm->addFieldResult('IDFC', 'SUBTOTAL_DESCUENTO', 'subtotalDescuento');
        $objRsm->addFieldResult('IDFC', 'VALOR_TOTAL', 'valorTotal');
        $objRsm->addFieldResult('IDFC', 'ENTREGO_RETENCION_FTE', 'entregoRetencionFte');
        $objRsm->addFieldResult('IDFC', 'ESTADO_IMPRESION_FACT', 'estadoImpresionFact');
        $objRsm->addFieldResult('IDFC', 'ES_AUTOMATICA', 'esAutomatica');
        $objRsm->addFieldResult('IDFC', 'PRORRATEO', 'prorrateo');
        $objRsm->addFieldResult('IDFC', 'REACTIVACION', 'reactivacion');
        $objRsm->addFieldResult('IDFC', 'RECURRENTE', 'recurrente');
        $objRsm->addFieldResult('IDFC', 'COMISIONA', 'comisiona');
        $objRsm->addFieldResult('IDFC', 'FE_CREACION', 'feCreacion');
        $objRsm->addFieldResult('IDFC', 'FE_EMISION', 'feEmision');
        $objRsm->addFieldResult('IDFC', 'USR_CREACION', 'usrCreacion');
        $objRsm->addFieldResult('IDFC', 'NUM_FACT_MIGRACION', 'numFactMigracion');
        $objRsm->addFieldResult('IDFC', 'OBSERVACION', 'observacion');
        $objRsm->addFieldResult('IDFC', 'REFERENCIA_DOCUMENTO_ID', 'referenciaDocumentoId');
        $objRsm->addFieldResult('IDFC', 'ES_ELECTRONICA', 'esElectronica');
        $objRsm->addFieldResult('IDFC', 'FE_AUTORIZACION', 'feAutorizacion');
        $objRsm->addFieldResult('IDFC', 'MES_CONSUMO', 'mesConsumo');
        $objRsm->addFieldResult('IDFC', 'ANIO_CONSUMO', 'anioConsumo');
        $objRsm->addFieldResult('IDFC', 'RANGO_CONSUMO', 'rangoConsumo');
        $objRsm->addFieldResult('IDFC', 'DESCUENTO_COMPENSACION', 'descuentoCompensacion'); 
        
        $objQuery->setSQL($strSelectTable);
        $arrayDocumentosFinancieros = $objQuery->getResult();
        
        return $arrayDocumentosFinancieros;   
       
    }
    
    
    /**
     * Documentación para reajustarImpuestos
     *
     * Llama al procedimiento FNCK_TRANSACTION.P_PROCESAR_ERROR_IVA para reajustar los valores de los impuestos.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 10-10-2017 - Versión inicial
     **/
    public function reajustarImpuestos($arrayParametros)
    {
        $strMensaje = ''; 
        $strSql     = 'BEGIN FNCK_TRANSACTION.P_PROCESAR_ERROR_IVA(:PV_EMPRESA_COD, :PN_ID_DOCUMENTO, :PV_ESTADO, :PV_USUARIO, :PV_MENSAJE); END;';
        $objStmt    = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam('PV_EMPRESA_COD', $arrayParametros["strEmpresaCod"]);
        $objStmt->bindParam('PN_ID_DOCUMENTO', $arrayParametros["intIdDocumento"]);
        $objStmt->bindParam('PV_ESTADO', $arrayParametros["strEstado"]);
        $objStmt->bindParam('PV_USUARIO', $arrayParametros["strUsuario"]);
        $objStmt->bindParam('PV_MENSAJE', $strMensaje);
        $objStmt->execute();
        return $strMensaje;
    }

    /**
     * Función que obtiene el número de facturas en un determinado estado por características de instalación.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 26-11-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 14-01-2018
     * Se modifica el query para obtener la última factura de instalación.
     */
    public function getFacturacionInstalacionPagada($arrayParametros)
    {
        $arrayRetorno = array();
        $strSql = "SELECT CASE WHEN CAB2.VALOR_TOTAL <= (SELECT NVL(SUM(VALOR_PAGO), 0)
                                                          FROM DB_FINANCIERO.INFO_PAGO_DET
                                                         WHERE REFERENCIA_ID = CAB2.ID_DOCUMENTO
                                                           AND ESTADO IN (:arrayEstadosPagos))
                               THEN 'S'
                               ELSE 'N'
                               END AS PAGADA
                      FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB2
                     WHERE ID_DOCUMENTO = (
                    SELECT MAX(CAB.ID_DOCUMENTO)
                     FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
                          DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                          DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                          DB_COMERCIAL.INFO_CONTRATO IC,
                          DB_COMERCIAL.INFO_PUNTO IP
                    WHERE IC.ID_CONTRATO = :intIdContrato
                      AND IC.PERSONA_EMPRESA_ROL_ID = IP.PERSONA_EMPRESA_ROL_ID
                      AND IP.ID_PUNTO = CAB.PUNTO_ID
                      AND IP.ESTADO = :strEstadoActivo
                      AND CAB.ID_DOCUMENTO = IDC.DOCUMENTO_ID
                      AND IDC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                      AND AC.DESCRIPCION_CARACTERISTICA IN (:arrayCaracteristicas)
                      AND AC.ESTADO = :strEstadoActivo
                      AND IDC.VALOR = :strValorS
                      AND IDC.ESTADO = :strEstadoActivo
                      AND CAB.ESTADO_IMPRESION_FACT in(:arrayEstados))";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intIdContrato", $arrayParametros["intIdContrato"]);
        $objQuery->setParameter("strEstadoActivo", "Activo");
        $objQuery->setParameter("strValorS", "S");
        $objQuery->setParameter("arrayEstados", $arrayParametros["arrayEstados"]);
        $objQuery->setParameter("arrayCaracteristicas", $arrayParametros["arrayCaracteristicas"]);
        $objQuery->setParameter("arrayEstadosPagos", array("Cerrado","Activo"));

        $objRsm->addScalarResult('PAGADA', 'pagada', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        $arrayRetorno[] = $arrayRespuesta[0]["pagada"];
        return $arrayRetorno;
    }

    /**
     * Función que obtiene el número de facturas en un determinado estado por características de instalación.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 06-01-2019
     *
     * Se modifica la forma de obtener las facturas pagadas. Se liga la consulta a los servicios proporcionados por parámetro.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 22-01-2019
     */
    public function esUltimaFactInstalacionPagada($arrayParametros)
    {
        $strSql =  "SELECT CASE WHEN CAB2.VALOR_TOTAL <= (SELECT NVL(SUM(VALOR_PAGO), 0)
                                                            FROM DB_FINANCIERO.INFO_PAGO_DET
                                                           WHERE REFERENCIA_ID = CAB2.ID_DOCUMENTO
                                                             AND ESTADO IN (:arrayEstadosPagos))
                             THEN 'S'
                             ELSE 'N'
                             END AS PAGADA
                        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB2
                        WHERE ID_DOCUMENTO = (
                          SELECT NVL(MAX (CAB.ID_DOCUMENTO), 0) AS ID_DOCUMENTO
                           FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
                                DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DET,
                                DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                                DB_COMERCIAL.ADMI_CARACTERISTICA AC
                          WHERE DET.SERVICIO_ID IN :arrayServicios
                            AND DET.DOCUMENTO_ID = CAB.ID_DOCUMENTO
                            AND CAB.ID_DOCUMENTO = IDC.DOCUMENTO_ID
                            AND IDC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                            AND AC.DESCRIPCION_CARACTERISTICA IN (SELECT DISTINCT VALOR2
                                                                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
                                                                         DB_GENERAL.ADMI_PARAMETRO_DET DET
                                                                   WHERE CAB.NOMBRE_PARAMETRO = :strNombreParametro
                                                                     AND CAB.ESTADO = :strEstadoActivo
                                                                     AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID
                                                                     AND DET.ESTADO <> :strEstadoEliminado)
                            AND AC.ESTADO = :strEstadoActivo
                            AND IDC.VALOR = :strValorS
                            AND IDC.ESTADO = :strEstadoActivo
                            AND CAB.ESTADO_IMPRESION_FACT in(:arrayEstadosFact))";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("arrayServicios", $arrayParametros["arrayServicios"]);
        $objQuery->setParameter("strEstadoActivo", "Activo");
        $objQuery->setParameter("strEstadoEliminado", "Eliminado");
        $objQuery->setParameter("strValorS", "S");
        $objQuery->setParameter("arrayEstadosFact", $arrayParametros["arrayEstadosFact"]);
        $objQuery->setParameter("strNombreParametro", $arrayParametros["strNombreParametro"]);
        $objQuery->setParameter("arrayEstadosPagos", array("Cerrado","Activo"));

        $objRsm->addScalarResult('PAGADA', 'pagada', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        //Si no tiene una factura, se asume que es del proceso de tarifarios con promoción por lo que sí permite el flujo.
        return $arrayRespuesta[0]["pagada"] ? $arrayRespuesta[0]["pagada"] : "S";
    }

    /**
     * Función que devuelve el número de facturas notas de crédito con un determinado estado aplicadas a las facturas de instalación.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 07-01-2019
     */
    public function cuentaNCInstalacionXPunto($arrayParametros)
    {
        $strSql      =   "SELECT COUNT(*) AS TOTAL
                            FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
                                 DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF,
                                 DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC2,
                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC2
                           WHERE CAB.PUNTO_ID = :intPuntoId
                             AND ATDF.ID_TIPO_DOCUMENTO = CAB.TIPO_DOCUMENTO_ID
                             AND ATDF.CODIGO_TIPO_DOCUMENTO = :strCodigoTipoDocumento
                             AND CAB.ESTADO_IMPRESION_FACT = :strEstado
                             AND CAB.REFERENCIA_DOCUMENTO_ID = IDC2.DOCUMENTO_ID
                             AND IDC2.ESTADO = :strEstadoActivo
                             AND IDC2.VALOR = :strValor
                             AND IDC2.CARACTERISTICA_ID = AC2.ID_CARACTERISTICA
                             AND AC2.DESCRIPCION_CARACTERISTICA IN (SELECT DISTINCT VALOR2
                                                                  FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
                                                                       DB_GENERAL.ADMI_PARAMETRO_DET DET
                                                                 WHERE CAB.NOMBRE_PARAMETRO = :strNombreParametro
                                                                   AND CAB.ESTADO = :strEstadoActivo
                                                                   AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID
                                                                   AND DET.ESTADO <> :strEstadoEliminado)
                             AND AC2.ESTADO = :strEstadoActivo";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);
        $objQuery->setParameter("strCodigoTipoDocumento", $arrayParametros["strCodigoTipoDocumento"]);
        $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
        $objQuery->setParameter("strEstadoActivo", "Activo");
        $objQuery->setParameter("strEstadoEliminado", "Eliminado");
        $objQuery->setParameter("strValor", "S");
        $objQuery->setParameter("strNombreParametro", "SOLICITUDES_DE_CONTRATO");

        $objRsm->addScalarResult('TOTAL', 'total', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta[0]["total"] ? $arrayRespuesta[0]["total"] : 0;
    }

    /**
     * Documentación para getSaldoXFactura
     * 
     * Función que obtiene el saldo por Factura.
     * 
     * @param array $arrayParametros['intIdDocumento'       => 'Id del documento Factura',
     *                               'strFeConsultaHasta'   => 'Fecha consulta hasta donde se desea consultar el saldo, si no se recibe calcula el 
     *                                                          saldo hasta el Sysdate'
     *                               'strTipoConsulta'      => 'Tipo de Consulta: saldo']
     * @return Valor del saldo de la Factura.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 23-12-2019
     */
    public function getSaldoXFactura($arrayParametros)
    {
        $fltSaldoXFactura = 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdDocumento     = ( isset($arrayParametros["intIdDocumento"]) ? ( !empty($arrayParametros["intIdDocumento"]) 
                                        ? $arrayParametros["intIdDocumento"] : 0 ) : 0 );
                $strFeConsultaHasta = ( isset($arrayParametros["strFeConsultaHasta"]) ? ( !empty($arrayParametros["strFeConsultaHasta"]) 
                                        ? $arrayParametros["strFeConsultaHasta"] : "" ) : "" );
                $strTipoConsulta    = ( isset($arrayParametros["strTipoConsulta"]) ? ( !empty($arrayParametros["strTipoConsulta"]) 
                                        ? $arrayParametros["strTipoConsulta"] : "saldo" ) : "saldo" );
            
                $fltSaldoXFactura = str_pad($fltSaldoXFactura, 50, " ");
                
                $strSql = "BEGIN :fltSaldoXFactura := DB_FINANCIERO.FNKG_CARTERA_CLIENTES.F_SALDO_X_FACTURA( :intIdDocumento, ".
                                                                                                            ":strFeConsultaHasta, ".
                                                                                                            ":strTipoConsulta ); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdDocumento',       $intIdDocumento);
                $objStmt->bindParam('strFeConsultaHasta',   $strFeConsultaHasta);
                $objStmt->bindParam('strTipoConsulta',      $strTipoConsulta);
                $objStmt->bindParam('fltSaldoXFactura',     $fltSaldoXFactura);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltSaldoXFactura;
    }
    /**
     * Documentación para getCantidadNcPorPunto
     * 
     * Función que Obtiene la Cantidad de Notas de Crédito o de Notas de Crédito internas que existen en proceso o Flujo de Activación asociadas
     * a las Facturas del Punto en sesión y que entran al Proceso de Diferido.
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna la cantidad de Facturas por Punto que entraran al Proceso de Diferido y que poseen NC o NCI
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 26-06-2020
     */
    public function getCantidadNcPorPunto($arrayParametros)
    {
        $intCantidadNcPorPunto = 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $intCantidadNcPorPunto = str_pad($intCantidadNcPorPunto, 50, " ");
                
                $strSql = "BEGIN :intCantidadNcPorPunto := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CANTIDAD_NC_PORPUNTO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('intCantidadNcPorPunto', $intCantidadNcPorPunto);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $intCantidadNcPorPunto;
    }

    /**
     * Documentación para getCantProcDiferidoPorPto
     * 
     * Función que obtiene la cantidad de Procesos de Diferidos de Facturas que se han generado por punto, se considera en estado
     * Pendiente y Finalizado.
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna la cantidad de Procesos de Diferidos de Facturas que se han generado por punto.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 27-06-2020
     */
    public function getCantProcDiferidoPorPto($arrayParametros)
    {
        $intCantProcDiferidoPorPto = 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $intCantProcDiferidoPorPto = str_pad($intCantProcDiferidoPorPto, 50, " ");
                
                $strSql = "BEGIN :intCantProcDiferidoPorPto := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CANTPROC_DIFERIDO_PORPTO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('intCantProcDiferidoPorPto', $intCantProcDiferidoPorPto);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $intCantProcDiferidoPorPto;
    }
    /**
     * Documentación para getTotalSaldoFactPorPto
     * 
     * Función que obtiene el total de saldo de Facturas por Punto.
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna el valor el total de saldo de Facturas por Punto.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 24-06-2020
     */
    public function getTotalSaldoFactPorPto($arrayParametros)
    {
        $fltTotalSaldoFactPorPto = 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $fltTotalSaldoFactPorPto = str_pad($fltTotalSaldoFactPorPto, 50, " ");
                
                $strSql = "BEGIN :fltTotalSaldoFactPorPto := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_TOTAL_SALDO_FACTPTO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('fltTotalSaldoFactPorPto', $fltTotalSaldoFactPorPto);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltTotalSaldoFactPorPto;
    }

    /**
     * Documentación para getTotalDiferido
     * 
     * Función que obtiene valor Total Diferido = Total de la Deuda(Los valores diferidos en cada proceso).  
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna el valor total de la Deuda Diferida por Punto.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 25-06-2020
     */
    public function getTotalDiferido($arrayParametros)
    {
        $fltTotalDiferido= 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $fltTotalDiferido = str_pad($fltTotalDiferido, 50, " ");
                
                $strSql = "BEGIN :fltTotalDiferido := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_TOTAL_DIFERIDO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('fltTotalDiferido', $fltTotalDiferido);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltTotalDiferido;
    }

    /**
     * Documentación para getDiferidoPagado
     * 
     * Función que obtiene el Total de Diferido Pagado = Pagos de las NDI que se generaron por la Opción de Emergencia. 
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna el valor Total de Diferido Pagado
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 25-06-2020
     */
    public function getDiferidoPagado($arrayParametros)
    {
        $fltDiferidoPagado= 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $fltDiferidoPagado = str_pad($fltDiferidoPagado, 50, " ");
                
                $strSql = "BEGIN :fltDiferidoPagado := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_DIFERIDO_PAGADO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('fltDiferidoPagado', $fltDiferidoPagado);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltDiferidoPagado;
    }

    /**
     * Documentación para getDiferidoPorVencer
     * 
     * Función que obtiene el valor diferido por vencer, corresponde al valor total de diferido pendientes de generarse.
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna el valor Total de Diferido por vencer
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 25-06-2020
     */
    public function getDiferidoPorVencer($arrayParametros)
    {
        $fltDiferidoPorVencer= 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $fltDiferidoPorVencer = str_pad($fltDiferidoPorVencer, 50, " ");
                
                $strSql = "BEGIN :fltDiferidoPorVencer := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_DIFERIDO_POR_VENCER(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('fltDiferidoPorVencer', $fltDiferidoPorVencer);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltDiferidoPorVencer;
    }

    /**
     * Documentación para getDiferidoVencido
     * 
     * Función que obtiene el valor total de NDI impagas a la fecha. 
     * 
     * @param array $arrayParametros['intIdPunto'  => 'Id del Punto']
     * @return Retorna el valor total de NDI impagas a la fecha. 
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 25-06-2020
     */
    public function getDiferidoVencido($arrayParametros)
    {
        $fltDiferidoVencido= 0;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPunto     = ( isset($arrayParametros["intIdPunto"]) ? ( !empty($arrayParametros["intIdPunto"]) 
                                        ? $arrayParametros["intIdPunto"] : 0 ) : 0 );
               
                $fltDiferidoVencido = str_pad($fltDiferidoVencido, 50, " ");
                
                $strSql = "BEGIN :fltDiferidoVencido := DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_DIFERIDO_VENCIDO(:intIdPunto); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('intIdPunto',       $intIdPunto);
                $objStmt->bindParam('fltDiferidoVencido', $fltDiferidoVencido);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $fltDiferidoVencido;
    }  
    /**
     * getAnticiposPendientes, obtiene la cantidad de de anticipos sin cruzar que posee un punto.
     *      
     * @param array $arrayParametros [
     *                                "arrayTiposDoc" => Tipos de  Documento 'ANT','ANTS','ANTC'
     *                                "strEstado"     => Estado del proceso 'Pendiente'
     *                                "intIdPunto"    => Id del punto. 
     *                               ]
     *
     * Costo de query : 5
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 22-07-2020 
     * 
     * @return $arrayRespuesta
     */
    public function getAnticiposPendientes($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);        
        
        $strQuery        = "SELECT COUNT(*) AS CANTIDAD 
                            FROM DB_FINANCIERO.INFO_PAGO_CAB PAG, 
                            DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOC
                            WHERE PAG.TIPO_DOCUMENTO_ID     = TDOC.ID_TIPO_DOCUMENTO
                            AND TDOC.CODIGO_TIPO_DOCUMENTO IN (:arrayTiposDoc) 
                            AND PAG.ESTADO_PAGO             = :strEstado
                            AND PAG.PUNTO_ID                = :intIdPunto";

        $objRsm->addScalarResult('CANTIDAD', 'cantidad', 'integer');
                 
        $objNtvQuery->setParameter('arrayTiposDoc' ,$arrayParametros['arrayTiposDoc']);        
        $objNtvQuery->setParameter('strEstado' ,$arrayParametros['strEstado']);
        $objNtvQuery->setParameter('intIdPunto' ,$arrayParametros['intIdPunto']);
                
        $objNtvQuery->setSQL($strQuery);
        $arrayRespuesta  = $objNtvQuery->getScalarResult();
        return $arrayRespuesta[0]["cantidad"] ;
    }

    /**
     * Documentación para getClienteCompensado
     * 
     * Función que obtiene si el cliente debe ser compensado o no 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-09-2016
     */
    public function getClienteCompensado($arrayParametros)
    {
        $strEsCompensado = '';
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPersonaEmpresaRol = ( isset($arrayParametros["intIdPersonaEmpresaRol"]) ? ( !empty($arrayParametros["intIdPersonaEmpresaRol"]) 
                                            ? $arrayParametros["intIdPersonaEmpresaRol"] : 0 ) : 0 );
                $intIdOficina           = ( isset($arrayParametros["intIdOficina"]) ? ( !empty($arrayParametros["intIdOficina"]) 
                                            ? $arrayParametros["intIdOficina"] : 0 ) : 0 );
                $strEmpresaCod          = ( isset($arrayParametros["strEmpresaCod"]) ? ( !empty($arrayParametros["strEmpresaCod"]) 
                                            ? $arrayParametros["strEmpresaCod"] : "" ) : "" );
                $intIdSectorPunto       = ( isset($arrayParametros["intIdSectorPunto"]) ? ( !empty($arrayParametros["intIdSectorPunto"]) 
                                            ? $arrayParametros["intIdSectorPunto"] : 0 ) : 0 );
                $intIdPuntoFacturacion  = ( isset($arrayParametros["intIdPuntoFacturacion"]) ? ( !empty($arrayParametros["intIdPuntoFacturacion"]) 
                                            ? $arrayParametros["intIdPuntoFacturacion"] : 0 ) : 0 );
            
                $strEsCompensado = str_pad($strEsCompensado, 10, " ");
                
                $sql = "BEGIN :strEsCompensado := DB_FINANCIERO.FNCK_CONSULTS.F_VALIDA_CLIENTE_COMPENSADO(  :intIdPersonaEmpresaRol, ".
                                                                                                           ":intIdOficina, ".
                                                                                                           ":strEmpresaCod, ".
                                                                                                           ":intIdSectorPunto, ".
                                                                                                           ":intIdPuntoFacturacion ); END;";
                $stmt = $this->_em->getConnection()->prepare($sql);
                $stmt->bindParam('intIdPersonaEmpresaRol',  $intIdPersonaEmpresaRol);
                $stmt->bindParam('intIdOficina',            $intIdOficina);
                $stmt->bindParam('strEmpresaCod',           $strEmpresaCod);
                $stmt->bindParam('intIdSectorPunto',        $intIdSectorPunto);
                $stmt->bindParam('intIdPuntoFacturacion',   $intIdPuntoFacturacion);
                $stmt->bindParam('strEsCompensado',         $strEsCompensado);
                $stmt->execute();
            }//( !empty($arrayParametros) )
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $strEsCompensado;
    }
    
    
    /* ******************************************************************************* */
    /* *********************  BUSQUEDA AVANZADA FINANCIERA **************************** */
    /* ******************************************************************************* */
    /**
     * Documentación para el método 'findBusquedaAvanzadaFinanciera'.
     *
     * Retorna el listado de informacion relacionada a los documentos financieros
     *
     * @param mixed $arrayVariables criterios de busqueda
     * @param mixed $empresaId      empresa a consultar
     * @param mixed $oficinaId      oficina a consultar
     * @param mixed $start          valor de inicio
     * @param mixed $limit          valor de limite
     *
     * @return array De informacion segun los criterios de busqueda dados
     *
     * Se agregan campos necesarios en el retorno de la infomacion segun los campos solicitados por el usuario
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 20-07-2015
     * @since   1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-08-2016 - Se agrega validación por fecha de autorización en la consulta
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 31-12-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     */
    public function findBusquedaAvanzadaFinanciera($arrayVariables, $empresaId, $oficinaId, $start, $limit){	
        $whereVar = "";
        $fromAdicional = "";
        $whereAdicional = "";
        $strTipo               = ( isset($arrayVariables['strTipoPersonal']) && !empty($arrayVariables['strTipoPersonal']) )
                                   ? $arrayVariables['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayVariables['strPrefijoEmpresa']) && !empty($arrayVariables['strPrefijoEmpresa']) )
                                   ? $arrayVariables['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayVariables['intIdPersonEmpresaRol'] ? intval($arrayVariables['intIdPersonEmpresaRol']) : 0;
        $strSubQuery           = "";
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            if( $strTipo == 'SUBGERENTE' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (SELECT ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.estado                        = 'Activo'
                                    AND ipervend.personaId                   = ipvend.id
                                    AND ipvend.estado                        = 'Activo'
                                    AND (ipervend.reportaPersonaEmpresaRolId = ".$intIdPersonEmpresaRol."
                                    OR ipervend.id                           = ".$intIdPersonEmpresaRol."))
                              ";
            }
            elseif( $strTipo == 'ASISTENTE' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (select ipvend.login
                                    from schemaBundle:InfoPersonaEmpresaRolCarac ipercvend ,
                                      schemaBundle:AdmiCaracteristica acvend ,
                                      schemaBundle:InfoPersona ipvend
                                WHERE ipercvend.personaEmpresaRolId          = ".$intIdPersonEmpresaRol."
                                        and acvend.id                        = ipercvend.caracteristicaId
                                        and ipvend.id                        = ipercvend.valor
                                        AND acvend.descripcionCaracteristica = 'ASISTENTE_POR_CARGO'
                                        AND acvend.estado                    = 'Activo'
                                        AND ipercvend.estado                 = 'Activo'
                                        AND ipvend.estado                    = 'Activo')
                              ";
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = ".$intIdPersonEmpresaRol."
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = 'Activo'
                                    AND ipvend.estado      = 'Activo')
                              ";
            }
        }
        if($arrayVariables && count($arrayVariables)>0)
        {
            if(isset($arrayVariables["login"]))
            {
                if($arrayVariables["login"] && $arrayVariables["login"]!="")
                {
                    $whereVar .= "AND UPPER(pun.login) like '%".strtoupper(trim($arrayVariables["login"]))."%' ";
                }
            }

            if(isset($arrayVariables["direccion_pto"]))
            {
                if($arrayVariables["direccion_pto"] && $arrayVariables["direccion_pto"]!="")
                {
                    $whereVar .= "AND UPPER(pun.direccion) like '%".strtoupper(trim($arrayVariables["direccion_pto"]))."%' ";
                }
            }

            if(isset($arrayVariables["descripcion_pto"]))
            {
                if($arrayVariables["descripcion_pto"] && $arrayVariables["descripcion_pto"]!="")
                {
                    $whereVar .= "AND UPPER(pun.descripcionPunto) like '%".strtoupper(trim($arrayVariables["descripcion_pto"]))."%' ";
                }
            }

            if(isset($arrayVariables["estados_pto"]))
            {
                if($arrayVariables["estados_pto"] && $arrayVariables["estados_pto"]!="" && $arrayVariables["estados_pto"]!="0")
                {
                    $whereVar .= "AND UPPER(pun.estado) = '".strtoupper(trim($arrayVariables["estados_pto"]))."' ";
                }
            }

            if(isset($arrayVariables["negocios_pto"]))
            {
                if($arrayVariables["negocios_pto"] && $arrayVariables["negocios_pto"]!="" && $arrayVariables["negocios_pto"]!="0")
                {
                    $whereVar .= "AND pun.tipoNegocioId = '".trim($arrayVariables["negocios_pto"])."' ";
                }
            }

            if(isset($arrayVariables["vendedor"]))
            {
                if($arrayVariables["vendedor"] && $arrayVariables["vendedor"]!="")
                {
					$whereVar .= "AND CONCAT(LOWER(peVend.nombres),CONCAT(' ',LOWER(peVend.apellidos))) like '%".strtolower(trim($arrayVariables["vendedor"]))."%' ";
				}
            }

            if(isset($arrayVariables["identificacion"]))
            {
                if($arrayVariables["identificacion"] && $arrayVariables["identificacion"]!="")
                {
                    $whereVar .= "AND per.identificacionCliente = '".trim($arrayVariables["identificacion"])."' ";
                }
            }

            if(isset($arrayVariables["nombre"]))
            {
                if($arrayVariables["nombre"] && $arrayVariables["nombre"]!="")
                {
                    $whereVar .= "AND UPPER(per.nombres) like '%".strtoupper(trim($arrayVariables["nombre"]))."%' ";
                }
            }

            if(isset($arrayVariables["apellido"]))
            {
                if($arrayVariables["apellido"] && $arrayVariables["apellido"]!="")
                {
                    $whereVar .= "AND UPPER(per.apellidos) like '%".strtoupper(trim($arrayVariables["apellido"]))."%' ";
                }
            }

            if(isset($arrayVariables["razon_social"]))
            {
                if($arrayVariables["razon_social"] && $arrayVariables["razon_social"]!="")
                {
                    $whereVar .= "AND UPPER(per.razonSocial) like '%".strtoupper(trim($arrayVariables["razon_social"]))."%' ";
                }
            }

            if(isset($arrayVariables["direccion_grl"]))
            {
                if($arrayVariables["direccion_grl"] && $arrayVariables["direccion_grl"]!="")
                {
                    $whereVar .= "AND UPPER(per.direccion) like '%".strtoupper(trim($arrayVariables["direccion_grl"]))."%' ";
                }
            }
			
            if(isset($arrayVariables["es_edificio"]) || isset($arrayVariables["depende_edificio"]))
            {
                $boolPDA = false;
                if($arrayVariables["es_edificio"] && $arrayVariables["es_edificio"]!="" && $arrayVariables["es_edificio"]!="0")
                {
                    $boolPDA = true;
                    $whereVar .= "AND pda.esEdificio = '".trim($arrayVariables["es_edificio"])."' ";
                }
                if($arrayVariables["depende_edificio"] && $arrayVariables["depende_edificio"]!="" && $arrayVariables["depende_edificio"]!="0")
                {
                    $boolPDA = true;
                    $whereVar .= "AND pda.dependeDeEdificio = '".trim($arrayVariables["depende_edificio"])."' ";
                }

                if($boolPDA)
                {
                    $fromAdicional .= "schemaBundle:InfoPuntoDatoAdicional pda, ";
                    $whereAdicional .= "AND pun.id = pda.puntoId ";
                }
            }
			
			$strTipoDocumento = '';
            if(isset($arrayVariables["fin_tipoDocumento"]))
            {
                if($arrayVariables["fin_tipoDocumento"] && $arrayVariables["fin_tipoDocumento"]!="" && $arrayVariables["fin_tipoDocumento"]!="0")
                {
		    $strTipoDocumento = $arrayVariables["fin_tipoDocumento"];
                    $whereVar .= "AND lower(atdf.codigoTipoDocumento) = lower('".trim($arrayVariables["fin_tipoDocumento"])."') ";
                }
            }
			
                        if($strTipoDocumento == 'FAC'  ||
                           $strTipoDocumento == 'FACP' ||
                           $strTipoDocumento == 'NC'   ||
                           $strTipoDocumento == 'ND'   ||
                           $strTipoDocumento == 'NDI'  ||
                           $strTipoDocumento == 'NCI'  ||
                           $strTipoDocumento == 'DEV')
                        {
				if(isset($arrayVariables["doc_numDocumento"]))
				{
					if($arrayVariables["doc_numDocumento"] && $arrayVariables["doc_numDocumento"]!="" && $arrayVariables["doc_numDocumento"]!="0")
					{
						$whereVar .= "AND idfc.numeroFacturaSri like '%".trim($arrayVariables["doc_numDocumento"])."%' ";
					}
				}
				if(isset($arrayVariables["doc_creador"]))
				{
					if($arrayVariables["doc_creador"] && $arrayVariables["doc_creador"]!="" && $arrayVariables["doc_creador"]!="0")
					{
						$whereVar .= "AND lower(idfc.usrCreacion) like lower('%".trim($arrayVariables["doc_creador"])."%') ";
					}
				}
				if(isset($arrayVariables["doc_estado"]))
				{
					if($arrayVariables["doc_estado"] && $arrayVariables["doc_estado"]!="" && $arrayVariables["doc_estado"]!="0")
					{
						$whereVar .= "AND lower(idfc.estadoImpresionFact) like lower('".trim($arrayVariables["doc_estado"])."') ";
					}
				}
				if(isset($arrayVariables["doc_monto"]) && isset($arrayVariables["doc_montoFiltro"]))
				{
					if($arrayVariables["doc_monto"] && $arrayVariables["doc_monto"]!="" && $arrayVariables["doc_monto"]!="0" &&
					   $arrayVariables["doc_montoFiltro"] && $arrayVariables["doc_montoFiltro"]!="" && $arrayVariables["doc_montoFiltro"]!="0")
					{
						if($arrayVariables["doc_montoFiltro"] == 'p') $whereVar .= "AND idfc.valorTotal  < ".trim($arrayVariables["doc_monto"])." ";
						if($arrayVariables["doc_montoFiltro"] == 'i') $whereVar .= "AND idfc.valorTotal  = ".trim($arrayVariables["doc_monto"])." ";
						if($arrayVariables["doc_montoFiltro"] == 'm') $whereVar .= "AND idfc.valorTotal  > ".trim($arrayVariables["doc_monto"])." ";
					}
				}
				
                $strDocFechaAutorizacionDesde = ( isset($arrayVariables["finDocFechaAutorizacionDesde"]) 
                                                  ? $arrayVariables["finDocFechaAutorizacionDesde"] : '' );
                $strDocFechaAutorizacionHasta = ( isset($arrayVariables["finDocFechaAutorizacionHasta"]) 
                                                  ? $arrayVariables["finDocFechaAutorizacionHasta"] : '' );
                
                $strDocFechaAutorizacionDesde = trim($strDocFechaAutorizacionDesde);
                
                if( !empty($strDocFechaAutorizacionDesde) )
                {
                    $arrayTmpFeAutorizacionDesde  = explode("-", $strDocFechaAutorizacionDesde);
                    $strDocFechaAutorizacionDesde = date("Y/m/d", strtotime($arrayTmpFeAutorizacionDesde[0]."-".$arrayTmpFeAutorizacionDesde[1].
                                                                            "-".$arrayTmpFeAutorizacionDesde[2]));
                    $strDocFechaAutorizacionDesde = trim($strDocFechaAutorizacionDesde);

                    if( !empty($strDocFechaAutorizacionDesde) )
                    {
                        $whereVar .= "AND idfc.feAutorizacion >= '".$strDocFechaAutorizacionDesde."' ";
                    }
                }
                
                
                $strDocFechaAutorizacionHasta = trim($strDocFechaAutorizacionHasta);
                
                if( !empty($strDocFechaAutorizacionHasta) )
                {
                    $arrayTmpFeAutorizacionHasta  = explode("-", $strDocFechaAutorizacionHasta);
                    $strTimeFeAutorizacionHasta   = strtotime(date("Y-m-d", strtotime( $arrayTmpFeAutorizacionHasta[0]."-".
                                                                                       $arrayTmpFeAutorizacionHasta[1]."-".
                                                                                       $arrayTmpFeAutorizacionHasta[2])). " +1 day");
                    $strDocFechaAutorizacionHasta = date("Y/m/d", $strTimeFeAutorizacionHasta);
                    $strDocFechaAutorizacionHasta = trim($strDocFechaAutorizacionHasta);

                    if( !empty($strDocFechaAutorizacionHasta) )
                    {
                        $whereVar .= "AND idfc.feAutorizacion < '".$strDocFechaAutorizacionHasta."' ";
                    }
                }
                
				$doc_fechaCreacionDesde = (isset($arrayVariables["doc_fechaCreacionDesde"]) ? $arrayVariables["doc_fechaCreacionDesde"] : 0);
				$doc_fechaCreacionHasta = (isset($arrayVariables["doc_fechaCreacionHasta"]) ? $arrayVariables["doc_fechaCreacionHasta"] : 0);
				$doc_fechaEmisionDesde = (isset($arrayVariables["doc_fechaEmisionDesde"]) ? $arrayVariables["doc_fechaEmisionDesde"] : 0);
				$doc_fechaEmisionHasta = (isset($arrayVariables["doc_fechaEmisionHasta"]) ? $arrayVariables["doc_fechaEmisionHasta"] : 0);
				if($doc_fechaCreacionDesde && $doc_fechaCreacionDesde!="0")
				{
					$dateF = explode("-",$doc_fechaCreacionDesde);
					$doc_fechaCreacionDesde = date("Y/m/d", strtotime($dateF[0]."-".$dateF[1]."-".$dateF[2]));
				}
				if($doc_fechaCreacionHasta && $doc_fechaCreacionHasta!="0")
				{
					$dateF = explode("-",$doc_fechaCreacionHasta);
					$fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[0]."-".$dateF[1]."-".$dateF[2])). " +1 day");
					$doc_fechaCreacionHasta = date("Y/m/d", $fechaSqlAdd);
				}
				if($doc_fechaEmisionDesde && $doc_fechaEmisionDesde!="0")
				{
					$dateF = explode("-",$doc_fechaEmisionDesde);
					$doc_fechaEmisionDesde = date("Y/m/d", strtotime($dateF[0]."-".$dateF[1]."-".$dateF[2]));
				}
				if($doc_fechaEmisionHasta && $doc_fechaEmisionHasta!="0")
				{
					$dateF = explode("-",$doc_fechaEmisionHasta);
					$fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[0]."-".$dateF[1]."-".$dateF[2])). " +1 day");
					$doc_fechaEmisionHasta = date("Y/m/d", $fechaSqlAdd);
				}
				
					
				if($doc_fechaCreacionDesde && $doc_fechaCreacionDesde!="0"){  $whereVar .= "AND idfc.feCreacion >= '".trim($doc_fechaCreacionDesde)."' "; }
				if($doc_fechaCreacionHasta && $doc_fechaCreacionHasta!="0") { $whereVar .= "AND idfc.feCreacion < '".trim($doc_fechaCreacionHasta)."' ";   }
				if($doc_fechaEmisionDesde && $doc_fechaEmisionDesde!="0"){  $whereVar .= "AND idfc.feEmision >= '".trim($doc_fechaEmisionDesde)."' "; }
				if($doc_fechaEmisionHasta && $doc_fechaEmisionHasta!="0") { $whereVar .= "AND idfc.feEmision < '".trim($doc_fechaEmisionHasta)."' ";   }
			}
			
		}     
		
		$selectedCont = " count(idfc) as cont ";
		$selectedData = "
							idfc.id as id_documento, 
                            idfc.oficinaId, 
                            idfc.numeroFacturaSri as numeroDocumento, 
                            idfc.valorTotal, 
                            idfc.estadoImpresionFact as estadoDocumentoGlobal,
							idfc.esAutomatica, 
                            idfc.feCreacion,
                            idfc.feEmision, 
                            idfc.feAutorizacion, 
                            idfc.usrCreacion, 
                            atdf.codigoTipoDocumento, 
                            atdf.nombreTipoDocumento, 
							pun.id as id_punto, 
							pun.login, 
                            pun.nombrePunto,
                            pun.direccion as direccion_pto, 
                            pun.descripcionPunto, 
                            pun.estado, 
                            pun.usrVendedor, 
							per.id, 
                            per.identificacionCliente, 
                            per.nombres, 
                            per.apellidos, 
                            per.razonSocial, 
							per.direccion as direccion_grl, 
                            per.calificacionCrediticia, 
							CONCAT(peVend.nombres,CONCAT(' ',peVend.apellidos)) as nombreVendedor,
							atn.codigoTipoNegocio,
							iog.nombreOficina,
							idfc.subtotal,
							idfc.subtotalConImpuesto,
							idfc.subtotalDescuento,
                            perol.id as idPersonaRol,
                            idfc.referenciaDocumentoId,
                            (idfc.subtotal-idfc.subtotalDescuento) as valorReal
						";
		$from = "FROM 
					schemaBundle:InfoDocumentoFinancieroCab idfc 
						JOIN schemaBundle:InfoOficinaGrupo iogi with iogi.id=idfc.oficinaId
						JOIN schemaBundle:InfoEmpresaGrupo iegi with iegi.id=iogi.empresaId and iegi.id='".$empresaId."',
					schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                    schemaBundle:InfoPersona per, 
                    schemaBundle:InfoPersonaEmpresaRol perol,
					$fromAdicional 
                    schemaBundle:InfoPunto pun LEFT JOIN schemaBundle:InfoPersona peVend WITH peVend.login = pun.usrVendedor,
                    schemaBundle:AdmiTipoNegocio atn,
                    schemaBundle:InfoOficinaGrupo iog";				
		$wher = "WHERE 
					idfc.tipoDocumentoId = atdf.id 
					AND perol.id = pun.personaEmpresaRolId 
                    AND per.id = perol.personaId 
                    AND idfc.puntoId=pun.id
                    $whereAdicional 
                    $whereVar 
                    AND atn.id=pun.tipoNegocioId
                    AND iog.id=perol.oficinaId ".$strSubQuery."
                ";
				
		$sql = "SELECT $selectedData $from $wher ";
		$sqlC = "SELECT $selectedCont $from $wher ";
				
        $queryC = $this->_em->createQuery($sqlC); 
        $query = $this->_em->createQuery($sql); 
        
		$resultTotal = $queryC->getOneOrNullResult();
		$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
	
	/**
     * Documentación para el método 'findComprobantesElectronicos'.
     *
     * Retorna todos los documentos electronicos
     *
     * @param mixed $parametros Arreglo de parametros.
     *
     * @return array Listado de comprobantes electronicos
     *
     * @author Kenneth Jimenez <kjimenez.ec>
     * @version 1.0 03-10-2014
     */
    public function findComprobantesElectronicos($parametros)
    {
        $codigosTipoDocumento = $parametros["codigosTipoDocumento"];//array
        $estados              = $parametros["estados"];//array
        $idEmpresa            = $parametros["idEmpresa"];
        $feDesde              = $parametros["feDesde"];
        $feHasta              = $parametros["feHasta"];
        $puntos               = $parametros["puntos"];//array
        $idOficina            = $parametros["idOficina"];
        
        $createQuery = $this->_em->createQuery();
        
        $query="SELECT 
                    idfc.id as idDocumento,
                    idfc.numeroFacturaSri,
                    idfc.feEmision,
                    idfc.subtotalCeroImpuesto as subtotal,
                    idfc.subtotalConImpuesto as iva,
                    idfc.subtotalDescuento as descuento,
                    idfc.valorTotal as total,
                    atd.nombreTipoDocumento as tipoDocumento,
                    idfc.estadoImpresionFact as estado
                FROM 
                    schemaBundle:InfoComprobanteElectronico ice,
                    schemaBundle:InfoDocumentoFinancieroCab idfc,
                    schemaBundle:AdmitipoDocumentoFinanciero atd,
                    schemaBundle:InfoOficinaGrupo iog
                WHERE
                    idfc.tipoDocumentoId=atd.id 
                AND idfc.id = ice.documentoId
                AND atd.codigoTipoDocumento in (:codigosTipoDocumento)
                AND iog.id=idfc.oficinaId
                "; 
            
        if($estados!="")
        {
            $query.=" AND idfc.estadoImpresionFact in (:estados) ";
            $createQuery->setParameter('estados', $estados);
        }
        
        if($idEmpresa!="")
        {
            $query.=" AND iog.empresaId=:empresa";
            $createQuery->setParameter('empresa', $idEmpresa);
        }    
        
        if($feDesde!="")
        {
            $query.=" AND idfc.feEmision>=:feDesde";
            $createQuery->setParameter('feDesde', $feDesde);
        }
        
        if($feHasta!="")
        {
            $query.=" AND idfc.feEmision<=:feHasta";
            $createQuery->setParameter('feHasta', $feHasta);
        }
            
        if($puntos)
        {
            $query.=" AND idfc.puntoId in (:puntos)";
            $createQuery->setParameter('puntos', $puntos);
        }
            
        if($idOficina)
        {
            $query.=" AND idfc.oficinaId=:oficina";
            $createQuery->setParameter('oficina', $idOficina);
        }
        
        $createQuery->setParameter('codigosTipoDocumento', $codigosTipoDocumento);
        $query.=" order by idfc.feEmision desc ";
        //Datos
        $createQuery->setDQL($query);
        $datos = $createQuery->getResult();
        
        return $datos;
    }

    /**
     * findFacturasPorPunto
     *
     * Retorna todas las facturas por punto según los filtros enviados
     *
     * @param mixed $parametros Arreglo de parametros.
     *
     * @return array Listado de facturas
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 04-03-2022
     */
    public function findFacturasPorPunto($arrayParametros)
    {
        $strCodigosTipoDocumento = $arrayParametros["strCodigosTipoDocumento"];
        $arrayEstados            = $arrayParametros["arrayEstados"];
        $strIdEmpresa            = $arrayParametros["strIdEmpresa"];
        $arrayPuntos             = $arrayParametros["arrayPuntos"];
        $strIdOficina            = $arrayParametros["strIdOficina"];
        
        $objCreateQuery = $this->_em->createQuery();
        
        $strQuery = "SELECT 
                        idfc.id as idDocumento,
                        idfc.numeroFacturaSri,
                        idfc.feEmision,
                        idfc.subtotalCeroImpuesto as subtotal,
                        idfc.subtotalConImpuesto as iva,
                        idfc.subtotalDescuento as descuento,
                        idfc.valorTotal as total,
                        atd.nombreTipoDocumento as tipoDocumento,
                        idfc.estadoImpresionFact as estado,
                        idfc.usrCreacion as usrCreacion
                    FROM 
                        schemaBundle:InfoDocumentoFinancieroCab idfc,
                        schemaBundle:AdmitipoDocumentoFinanciero atd,
                        schemaBundle:InfoOficinaGrupo iog
                    WHERE
                        idfc.tipoDocumentoId=atd.id 
                    AND atd.codigoTipoDocumento in (:codigosTipoDocumento)
                    AND iog.id=idfc.oficinaId
                    "; 
            
        if($arrayEstados!="")
        {
            $strQuery .= " AND idfc.estadoImpresionFact in (:estados) ";
            $objCreateQuery->setParameter('estados', $arrayEstados);
        }
        
        if($strIdEmpresa!="")
        {
            $strQuery .= " AND iog.empresaId=:empresa";
            $objCreateQuery->setParameter('empresa', $strIdEmpresa);
        }    
        
        if($arrayPuntos)
        {
            $strQuery .= " AND idfc.puntoId in (:puntos)";
            $objCreateQuery->setParameter('puntos', $arrayPuntos);
        }
            
        if($strIdOficina)
        {
            $strQuery .= " AND idfc.oficinaId=:oficina";
            $objCreateQuery->setParameter('oficina', $strIdOficina);
        }
        
        $objCreateQuery->setParameter('codigosTipoDocumento', $strCodigosTipoDocumento);
        $strQuery .= " order by idfc.feEmision desc ";
        //Datos
        $objCreateQuery->setDQL($strQuery);
        $arrayDatos = $objCreateQuery->getResult();
        
        return $arrayDatos;
    }
    
	/**
     * find30FacturasPorEmpresaPorEstado, obtiene facturas enviando el id empresa como parametro
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-12-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 11-03-2014
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 - Se modifica para que busque todos los documentos financieros si no se envía el parámetro 'arrayTipoDoc', adicional se agregan
     *                filtros de 'strFeEmisionDesde', 'strFeEmisionHasta' y 'strNumeroDocumento'
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 30-12-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo a la caracteristica de la persona en sesion 
     *                         por medio de las siguiente descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' Estos cambios solo aplican para Telconet
     * @author : Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 07-08-2020 Se agrega una bandera con el fin de mejorar la consulta a base cuando se esta consultando la informacion
     *                          sobre una factura especifica.
     * 
     * @since 17-06-2016
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     */
	public function find30FacturasPorEmpresaPorEstado($arrayParametros){	  
        //Query que obtiene los Datos
        $query          = $this->_em->createQuery();
        $dqlSelect      = "SELECT idfc ";
        //Query que obtiene el conteo de resultado de datos
        $queryCount     = $this->_em->createQuery();
        $dqlSelectCount = "SELECT count(idfc.id) ";

        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        $strFrom               ='';
        $strWhere              ='';

        $boolBusqueda = (isset($arrayParametros['boolBusqueda']) && !empty($arrayParametros['boolBusqueda'])) ? 
                                                                        $arrayParametros['boolBusqueda'] : 0;
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            $strFrom =" ,schemaBundle:InfoPunto ipuVend ";
            if( $strTipo == 'SUBGERENTE' )
            {
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
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
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
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
                $query->setParameter('strDescripcion', $strDescripcion);
                $queryCount->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = :intIdPersonEmpresaRol
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = :strEstadoActivo
                                    AND ipvend.estado      = :strEstadoActivo)
                              ";
            }
            $query->setParameter('strEstadoActivo', $strEstadoActivo);
            $query->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);

            $queryCount->setParameter('strEstadoActivo', $strEstadoActivo);
            $queryCount->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }

        //Cuerpo del Query
        $dqlBody        = " FROM 
                            schemaBundle:InfoDocumentoFinancieroCab idfc,
                            schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                            schemaBundle:InfoOficinaGrupo iog ".$strFrom."
                    WHERE idfc.tipoDocumentoId=atdf.id
                            AND iog.id                      =   idfc.oficinaId
                            AND iog.empresaId               =   :intIdEmpresa
                            ".$strWhere."
                            AND idfc.estadoImpresionFact    !=  :strEstadoEliminado ";
        
        //Query que obtiene los Datos
        $query->setParameter('strEstadoEliminado', 'Eliminado');
        //Query que obtiene conteo de Datos
        $queryCount->setParameter('strEstadoEliminado', 'Eliminado');         
        
        if(!empty($arrayParametros['arrayTipoDoc']))
        {
            $dqlBody .= " AND atdf.codigoTipoDocumento    in  (:arrayTipoDoc) ";
            //Query que obtiene los Datos
            $query->setParameter('arrayTipoDoc', $arrayParametros['arrayTipoDoc']);
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('arrayTipoDoc', $arrayParametros['arrayTipoDoc']);
        }
        if(!empty($arrayParametros['strFeCreacionDesde']))
        {
            $dqlBody .= " AND idfc.feCreacion             >=  :strFeCreacionDesde ";
            //Query que obtiene los Datos
            $query->setParameter('strFeCreacionDesde', $arrayParametros['strFeCreacionDesde']);
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('strFeCreacionDesde', $arrayParametros['strFeCreacionDesde']);
        }
        if(!empty($arrayParametros['strFeCreacionHasta']))
        {
            $dqlBody .= " AND idfc.feCreacion             <=  :strFeCreacionHasta ";
            //Query que obtiene los Datos
            $query->setParameter('strFeCreacionHasta', $arrayParametros['strFeCreacionHasta'].' 23:59:59');
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('strFeCreacionHasta', $arrayParametros['strFeCreacionHasta'].' 23:59:59');
        }
        if(!empty($arrayParametros['strFeEmisionDesde']))
        {
            $dqlBody .= " AND idfc.feEmision             >=  :strFeEmisionDesde ";
            //Query que obtiene los Datos
            $query->setParameter('strFeEmisionDesde', $arrayParametros['strFeEmisionDesde']);
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('strFeEmisionDesde', $arrayParametros['strFeEmisionDesde']);
        }
        if(!empty($arrayParametros['strFeEmisionHasta']))
        {
            $dqlBody .= " AND idfc.feEmision             <=  :strFeEmisionHasta ";
            //Query que obtiene los Datos
            $query->setParameter('strFeEmisionHasta', $arrayParametros['strFeEmisionHasta'].' 23:59:59');
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('strFeEmisionHasta', $arrayParametros['strFeEmisionHasta'].' 23:59:59');
        }
        
        if($boolBusqueda)
        {
            if(!empty($arrayParametros['strNumeroDocumento']))
            {
                $dqlBody .= " AND idfc.numeroFacturaSri = :strNumeroDocumento ";
                //Query que obtiene los Datos
                $query->setParameter('strNumeroDocumento', $arrayParametros['strNumeroDocumento']);
                //Query que obtiene conteo de Datos
                $queryCount->setParameter('strNumeroDocumento', $arrayParametros['strNumeroDocumento']);
            }
        }
        else
        {
            if(!empty($arrayParametros['strNumeroDocumento']))
            {
                $dqlBody .= " AND idfc.numeroFacturaSri LIKE :strNumeroDocumento ";
                //Query que obtiene los Datos
                $query->setParameter('strNumeroDocumento', '%'.$arrayParametros['strNumeroDocumento'].'%');
                //Query que obtiene conteo de Datos
                $queryCount->setParameter('strNumeroDocumento', '%'.$arrayParametros['strNumeroDocumento'].'%');
            }
        }

        if(!empty($arrayParametros['strEstado']))
        {
            $dqlBody .= " AND idfc.estadoImpresionFact = :strEstado ";
            //Query que obtiene los Datos
            $query->setParameter('strEstado', $arrayParametros['strEstado']);
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        if(!empty($arrayParametros['intIdPunto']))
        {
            $dqlBody .= " AND idfc.puntoId = :intIdPunto ";
            //Query que obtiene los Datos
            $query->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
            //Query que obtiene conteo de Datos
            $queryCount->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        }
        //Query que obtiene los Datos
        $dqlCompleto = $dqlSelect.$dqlBody." order by idfc.feCreacion DESC";
        $query->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $query->setDQL($dqlCompleto);
        $arrayDatos = $query->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        //Query que obtiene conteo de Datos
        $queryCount->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        if(!empty($arrayDatos)){
            //query de conteo de datos
            $dqlCompleto    = $dqlSelectCount.$dqlBody;
            $queryCount->setDQL($dqlCompleto);
            $intTotal       = $queryCount->getSingleScalarResult();
        }
        
        $arrayResultado['registros']    = $arrayDatos;
        $arrayResultado['total']        = $intTotal;
        return $arrayResultado;
    }
    
        /**
     * findFacturasPorCriterios, obtiene facturas enviando criterios de busqueda
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-12-2014
     * @since 1.0
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     */
    public function findFacturasPorCriterios($arrayParametros){	
        //Query que obtiene los Datos
        $query          = $this->_em->createQuery();
        $dqlSelect      = "SELECT idfc ";
        //Query que obtiene el conteo de resultado de datos
        $queryCount     = $this->_em->createQuery();
        $dqlSelectCount = "SELECT count(idfc.id) ";
        //Cuerpo del Query
        $dqlBody        = " FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                                schemaBundle:InfoOficinaGrupo iog
                            WHERE 
                                idfc.tipoDocumentoId            =   atdf.id 
                                AND atdf.codigoTipoDocumento    in  (:arrayTipoDoc)
                                AND iog.id                      =   idfc.oficinaId 
                                AND iog.empresaId               =   :intIdEmpresa 
                                AND idfc.feCreacion             >=  :strFechaDesde  
                                AND idfc.feCreacion             <=  :strFechaHasta ";
        $query->setParameter('arrayTipoDoc', $arrayParametros['arrayTipoDoc']);
        $queryCount->setParameter('arrayTipoDoc', $arrayParametros['arrayTipoDoc']);
        if (!empty($arrayParametros['strEstado'])){
            //cuerpo del query
            $dqlBody .= " AND idfc.estadoImpresionFact = :strEstado ";
            //query de datos
            $query->setParameter('strEstado', $arrayParametros['strEstado']);
            //query de conteo de datos
            $queryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        if (!empty($arrayParametros['intIdPunto'])){
            $dqlBody .= " AND idfc.puntoId = :intIdPunto ";
            $query->setParameter('intIdPunto',      $arrayParametros['intIdPunto']);
            $queryCount->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        }
        //query de datos
        $dqlCompleto = $dqlSelect.$dqlBody." order by idfc.feCreacion desc";
        $query->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $query->setParameter('strFechaDesde', $arrayParametros['strFechaDesde'][0]);
        $query->setParameter('strFechaHasta', $arrayParametros['strFechaHasta'][0]);
        //resultado de query de datos
        $query->setDQL($dqlCompleto);
        $arrayDatos = $query->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        //query de conteo de datos
        $queryCount->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $queryCount->setParameter('strFechaDesde', $arrayParametros['strFechaDesde'][0]);
        $queryCount->setParameter('strFechaHasta', $arrayParametros['strFechaHasta'][0]);
        
        if(!empty($arrayDatos)){
            //query de conteo de datos
            $dqlCompleto    = $dqlSelectCount.$dqlBody;
            $queryCount->setDQL($dqlCompleto);
            $intTotal       = $queryCount->getSingleScalarResult();
        }
        $arrayResultado['registros']    = $arrayDatos;
        $arrayResultado['total']        = $intTotal;
        return $arrayResultado;
    }
    
	public function find30FacturasProporcionalesPorEmpresaPorEstado($idOficina,$limit, $page, $start,$punto){	
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";
		
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmiTipoDocumentoFinanciero atdf
				WHERE 
						idfc.tipoDocumentoId=atdf.id AND
						atdf.codigoTipoDocumento='FACP' AND
						idfc.oficinaId=".$idOficina.$subquery)->setMaxResults(30);
                //echo $query->getSQL();
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }
    
    public function findFacturasProporcionalesPorCriterios($idOficina,$fechaDesde,$fechaHasta,$limit, $page, $start,$punto){	
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";
			
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmiTipoDocumentoFinanciero atdf
				WHERE 
						idfc.tipoDocumentoId=atdf.id AND
						atdf.codigoTipoDocumento='FACP' AND
						idfc.oficinaId=".$idOficina." AND
						idfc.feCreacion >= '".$fechaDesde."' AND 
						idfc.feCreacion <= '".$fechaHasta."'".$subquery);
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }
    
        
        public function findFacturasAbiertasPorFecha($fechaIni, $fechaFin, $idOficina){
 		$query = $this->_em->createQuery("SELECT sum(idfc.valorTotal) AS total
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf
				WHERE 
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento='FAC' AND  
                                idfc.estadoImpresionFact='Activo' AND
                                idfc.oficinaId=".$idOficina." AND
                                idfc.feCreacion >= '".$fechaIni."' AND 
                                idfc.feCreacion <= '".$fechaFin."'");
		$resultado=$query->getResult();
                //echo $query->getSQL();die;
		return $resultado;           
        }
        
        public function findEstadoDeCuentaOG($idOficina,$fechaDesde,$fechaHasta,$puntos)
        {
			//echo $fechaDesde;
		
			$sub_parte="";
			
			if($puntos!="")
			{	
			
				if($fechaDesde!="")
					$sub_parte.="idfc.feCreacion >= '".date('Y/m/d', strtotime($fechaDesde))."' AND ";
						
				if($fechaHasta!="")
					$sub_parte.="idfc.feCreacion <= '".date('Y/m/d', strtotime($fechaHasta))."' AND ";
					
				
				$query = $this->_em->createQuery("SELECT idfc.id,
						idfc.numeroFacturaSri,
						idfc.tipoDocumentoId,
						idfc.valorTotal,
						idfc.feCreacion,
						idfc.puntoId,
						idfc.oficinaId,
						idfc.referencia,
						idfc.codigoFormaPago,
						idfc.numeroReferencia,
						idfc.numeroCuentaBanco,
						idfc.referenciaId
						FROM schemaBundle:EstadoCuenta idfc,
						schemaBundle:AdmiTipoDocumentoFinanciero atdf
						WHERE 
						".$sub_parte." 
						idfc.tipoDocumentoId=atdf.id
						and idfc.puntoId in (".$puntos.")");
				
				$total=count($query->getResult());
				$datos = $query->getResult();
				
				$resultado['registros']=$datos;
				$resultado['total']=$total;
			}else { 
				$resultado= '{"registros":"[]","total":0}';
			}
		
			return $resultado;
		}
	
        
    /**
     * Documentación para el método 'findEstadoDeCuenta'.
     *
     * Me devuelve los documentos tales como FAC, FACP, ND, NDI; los cuales generaran un arbol interno de opciones
     *
     * @param mixed $idOficina Oficina en session.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     * @param mixed $puntos Pto o listado de ptos clientes.
     *
     * @return resultado Listado de documentos y total de documentos.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 15-05-2014
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 11-09-2016 - Se agrega a que el método retorne las NDI (Nota de debito interna), documento que será visible en el estado de 
     *                           cuenta
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 02-03-2018 - Se modifica Vista para que se obtenga la Fe_Creacion, Fe_Emision y Fe_Autorizacion sin condiciones adicionales. 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 08-10-2019 Se agrega condición para ordenamiento de datos en la consulta de información del estado de cuenta.
     */
    public function findEstadoDeCuenta($idOficina,$fechaDesde,$fechaHasta,$puntos)
    {
        
        if($puntos!=""){    
            
            $query = $this->_em->createQuery();
            
            $dql_cc="SELECT count(idfc.id) ";
            
            $dql="SELECT idfc.id,
                    idfc.numeroFacturaSri,
                    idfc.tipoDocumentoId,
                    idfc.valorTotal,
                    idfc.feCreacion,
                    idfc.fecCreacion,
                    idfc.fecEmision,
                    idfc.fecAutorizacion,
                    idfc.puntoId,
                    idfc.oficinaId,
                    idfc.referencia,
                    idfc.codigoFormaPago,
                    idfc.numeroReferencia,
                    idfc.numeroCuentaBanco,
                    idfc.referenciaId,
                    atdf.codigoTipoDocumento ";
            
            
            $cuerpo="
                    FROM schemaBundle:EstadoCuentaCliente idfc,
                    schemaBundle:AdmiTipoDocumentoFinanciero atdf
                    WHERE 
                    idfc.tipoDocumentoId=atdf.id
                    and atdf.codigoTipoDocumento in (:codigos)
                    and idfc.migracion is null
                    and idfc.puntoId in (:puntos)";
                    
            $dql_cc.=$cuerpo;
            $dql.=$cuerpo;
            
            if($fechaDesde!="")
            {
                $dql.=" and idfc.feCreacion >= :fe_desde";
                $dql_cc.=" and idfc.feCreacion >= :fe_desde";
                $query->setParameter('fe_desde',date('Y/m/d', strtotime($fechaDesde)));
            }    
                
            if($fechaHasta!="")
            {
                $dql.=" and idfc.feCreacion <= :fe_hasta";
                $dql_cc.=" and idfc.feCreacion <= :fe_hasta";
                $query->setParameter('fe_hasta',date('Y/m/d', strtotime($fechaHasta)));
            }
            $dql.=" order by idfc.feCreacion ";
            $codigos=array('FAC' , 'FACP', 'NDI');
            $query->setParameter('codigos',$codigos);
            $query->setParameter('puntos',$puntos);
            
            $query->setDQL($dql);
            $datos= $query->getResult();
            
            if($datos)
            {
                $query->setDQL($dql_cc);
                $total= $query->getSingleScalarResult();
            }
            else
                $total=0;
            
            $resultado['registros']=$datos;
            $resultado['total']=$total;
            
        }
        else 
        { 
            $resultado= '{"registros":"[]","total":0}';
        }
        
        return $resultado;
    }

    /**
     * Documentación para el método 'findAnticiposEstadoDeCuenta'.
     *
     * Me devuelve los anticipos en estado pendientes
     *
     * @param mixed $idOficina Oficina en session.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     * @param mixed $puntos Pto o listado de ptos clientes.
     *
     * @return resultado Listado de documentos pendientes
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 15-05-2014
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 03-06-2016 Se modifico a NativeQuery para poder hacer el uso del EXISTS para los estados de la INFO_DOCUMENTO_FINANCIERO_CAB
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.2 07-06-2016 Se modifico el query existente con las tablas correspondientes a los detalles de pago
     * @since 1.0
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.3 02-03-2018 - Se modifica Vista para que se obtenga la Fe_Creacion, Fe_Emision y Fe_Autorizacion sin condiciones adicionales.      
     */
    public function findAnticiposEstadoDeCuenta($idOficina, $fechaDesde, $fechaHasta, $puntos, $estado)
    {

        if($puntos != "")
        {
            $rsmBuilder = new ResultSetMappingBuilder($this->_em);
            $query      = $this->_em->createNativeQuery(null, $rsmBuilder);

            $dql_cc = "SELECT count(idfc.ID_DOCUMENTO) TOTAL ";
            $rsmBuilder->addScalarResult('TOTAL', 'total', 'integer');

            $dql = "SELECT idfc.ID_DOCUMENTO ,
                                idfc.NUMERO_FACTURA_SRI ,
                                idfc.TIPO_DOCUMENTO_ID ,
                                idfc.VALOR_TOTAL ,
                                TO_CHAR(idfc.FE_CREACION, 'DD/MM/YYYY') FE_CREACION,
                                idfc.FEC_CREACION,
                                idfc.FEC_EMISION,
                                idfc.FEC_AUTORIZACION,
                                idfc.PUNTO_ID ,
                                idfc.OFICINA_ID,
                                idfc.REFERENCIA ,
                                idfc.CODIGO_FORMA_PAGO ,
                                idfc.NUMERO_REFERENCIA ,
                                idfc.NUMERO_CUENTA_BANCO ,
                                idfc.REFERENCIA_ID ,
                                ipd.COMENTARIO ";
            $rsmBuilder->addScalarResult('ID_DOCUMENTO'         , 'id'                  , 'integer');
            $rsmBuilder->addScalarResult('NUMERO_FACTURA_SRI'   , 'numeroFacturaSri'    , 'string');
            $rsmBuilder->addScalarResult('TIPO_DOCUMENTO_ID'    , 'tipoDocumentoId'     , 'integer');
            $rsmBuilder->addScalarResult('VALOR_TOTAL'          , 'valorTotal'          , 'float');
            $rsmBuilder->addScalarResult('FE_CREACION'          , 'feCreacion'          , 'string');
            $rsmBuilder->addScalarResult('FEC_CREACION'         , 'fecCreacion'         , 'string');
            $rsmBuilder->addScalarResult('FEC_EMISION'          , 'fecEmision'          , 'string');
            $rsmBuilder->addScalarResult('FEC_AUTORIZACION'     , 'fecAutorizacion'     , 'string');
            $rsmBuilder->addScalarResult('PUNTO_ID'             , 'puntoId'             , 'integer');
            $rsmBuilder->addScalarResult('OFICINA_ID'           , 'oficinaId'           , 'integer');
            $rsmBuilder->addScalarResult('REFERENCIA'           , 'referencia'          , 'string');
            $rsmBuilder->addScalarResult('CODIGO_FORMA_PAGO'    , 'codigoFormaPago'     , 'string');
            $rsmBuilder->addScalarResult('NUMERO_REFERENCIA'    , 'numeroReferencia'    , 'string');
            $rsmBuilder->addScalarResult('NUMERO_CUENTA_BANCO'  , 'numeroCuentaBanco'   , 'string');
            $rsmBuilder->addScalarResult('REFERENCIA_ID'        , 'referenciaId'        , 'integer');
            $rsmBuilder->addScalarResult('COMENTARIO'           , 'comentario'          , 'string');
           
            $cuerpo = "       FROM 
                                ESTADO_CUENTA_CLIENTE idfc,
                                ADMI_TIPO_DOCUMENTO_FINANCIERO atdf,
                                INFO_PAGO_DET ipd
                            WHERE 
                                idfc.TIPO_DOCUMENTO_ID = atdf.ID_TIPO_DOCUMENTO
                                and atdf.CODIGO_TIPO_DOCUMENTO in (:codigoTipoDocumento)
                                and idfc.MIGRACION IS NULL
                                and EXISTS (SELECT ipd.ID_PAGO_DET FROM INFO_PAGO_DET ipd
                                            WHERE ipd.ID_PAGO_DET = idfc.ID_DOCUMENTO and ipd.ESTADO in (:estado))
                                and ipd.REFERENCIA_ID IS NULL
                                and ipd.ID_PAGO_DET = idfc.ID_DOCUMENTO
                                and idfc.PUNTO_ID in (:puntos) ";

            $dql_cc.=$cuerpo;
            $dql.=$cuerpo;
            
            if($fechaDesde != "")
            {
                $dql.=" and idfc.FE_CREACION >= :fe_desde";
                $dql_cc.=" and idfc.FE_CREACION >= :fe_desde";
                $query->setParameter('fe_desde', date('Y/m/d', strtotime($fechaDesde)));
            }

            if($fechaHasta != "")
            {
                $dql.=" and idfc.FE_CREACION <= :fe_hasta";
                $dql_cc.=" and idfc.FE_CREACION <= :fe_hasta";
                $query->setParameter('fe_hasta', date('Y/m/d', strtotime($fechaHasta)));
            }

            if($estado == 'Pendiente')
            {
                $estado = array('Pendiente', 'Cerrado');
            }

            $codigoTipoDocumento = array('PAG', 'PAGC', 'ANT', 'ANTS', 'ANTC');

            $query->setParameter('codigoTipoDocumento', $codigoTipoDocumento);
            $query->setParameter('estado', $estado);
            $query->setParameter('puntos', $puntos);


            $query->setSQL($dql);
            $datos = $query->getResult();

            if($datos)
            {
                $query->setSQL($dql_cc);
                $total = $query->getSingleScalarResult();
                
            }
            else
            {
                $total = 0;
            }
            $resultado['registros'] = $datos;
            $resultado['total'] = $total;
        }
        else
        {
            $resultado = '{"registros":"[]","total":0}';
        }
        
        return $resultado;
    }

    public function findResumenEstadoCuenta($puntos)
    {
		
		//echo $fechaDesde;
		
		$sub_parte="";
		
		if($puntos!="")
		{	
			
			$rsm = new ResultSetMapping;
			$rsm->addEntityResult('telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero', 'atdf');	      
			$rsm->addFieldResult('atdf', 'ID_TIPO_DOCUMENTO', 'id');
			$rsm->addFieldResult('atdf', 'CODIGO_TIPO_DOCUMENTO', 'codigoTipoDocumento');
			$rsm->addFieldResult('atdf', 'NOMBRE_TIPO_DOCUMENTO', 'nombreTipoDocumento');
			$rsm->addFieldResult('atdf', 'MOVIMIENTO', 'movimiento');
			$rsm->addFieldResult('atdf','SUMATORIA', 'sumatoria');
        
			$query = $this->_em->createNativeQuery("
				SELECT 
					atdf.id_tipo_documento,
					atdf.codigo_Tipo_Documento,
					atdf.nombre_Tipo_Documento,
					atdf.movimiento,
					sum(ecc.valor_Total) as sumatoria
				FROM 
				ESTADO_CUENTA_CLIENTE ecc
				LEFT JOIN INFO_PAGO_CAB ipc ON ipc.id_pago=ecc.id_documento,
				ADMI_TIPO_DOCUMENTO_FINANCIERO atdf
				WHERE
				ecc.tipo_Documento_Id=atdf.id_tipo_documento
				and (SEARCH_LONG_PAGO_CAB(ipc.rowid) NOT LIKE '%Generado por N/C%' or SEARCH_LONG_PAGO_CAB(ipc.rowid) is NULL)
				and ecc.punto_Id in (".$puntos.")
				group by atdf.id_tipo_documento,atdf.codigo_Tipo_Documento,atdf.movimiento,atdf.nombre_Tipo_Documento
				order by atdf.id_tipo_documento",$rsm);
				
			
			//sum(ecc.valor_Total) as sumatoria
			
			
			//Se debe habilitar la oficina para el estado de cta
			$total=count($query->getResult());
			$datos = $query->getResult();
			
			//echo $query->getSQL();
			//var_dump($datos);
			
			//echo "<br/>";
			
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		}else { 
			$resultado= '{"registros":"[]","total":0}';
		}
		
		return $resultado;
	}
	
	public function findPagosParaEstadoDeCuenta($idDocumento,$fechaDesde,$fechaHasta)
    {
		
		
		if($fechaDesde!="" && $fechaHasta!="")
			$sub_parte="idfc.feCreacion >= '".$fechaDesde."' AND 
				idfc.feCreacion <= '".$fechaHasta."' AND";
		else
			$sub_parte="";
		
		$query = $this->_em->createQuery("SELECT idfc.id,
				idfc.numeroFacturaSri,
				idfc.tipoDocumentoId,
				idfc.valorTotal,
				idfc.feCreacion,
				idfc.puntoId,
				idfc.oficinaId,
				idfc.referencia,
				idfc.codigoFormaPago,
				idfc.numeroReferencia,
				idfc.numeroCuentaBanco,
				idfc.referenciaId
				FROM schemaBundle:EstadoCuentaCliente idfc
				WHERE 
				".$sub_parte." 
				idfc.tipoDocumentoId=2
				or idfc.tipoDocumentoId=3
				or idfc.tipoDocumentoId=4");
		
		/*
		 * idfc.tipoDocumentoId=atdf.id 
				and atdf.codigoTipoDocumento='FAC' or atdf.codigoTipoDocumento='FACP' 
				and idfc.puntoId in (".$puntos.")");
		 * */
		
		//Se debe habilitar la oficina para el estado de cta
		//idfc.oficinaId=".$idOficina." AND
		$total=count($query->getResult());
		//echo $total;
		//ie();
		$datos = $query->getResult();
		
		//echo $query->getSQL();
		//die();
		
		$resultado['registros']=$datos;
		$resultado['total']=$total;
              
			
		if(empty($resultado['registros']))
			$resultado= '{"registros":"[]","total":0}';		
		
		return $resultado;
	}

     /**
     * obtienePrimeraFeEmision - Funcion que obtiene la fecha de emision de la primera factura pendiente por cliente
     * Costo=3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-06-2018
     *
     * @param array $arrayParametros [ intIdPunto           => id del punto,
     *                                 arrayTipoDocumentoId => tipos de documentos,
     *                                 strEstadoFactura     => estado de la factura ]
     *
     * @return srting $strFechaEmision
     */
    public function obtienePrimeraFeEmision($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsm);
        $strFechaEmision = "";

        $strSql = " SELECT TO_CHAR(IDF2.FE_EMISION,'DD-MM-YYYY') FE_EMISION
                        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDF2
                        WHERE IDF2.ID_DOCUMENTO = (
                              SELECT MIN(IDF.ID_DOCUMENTO) MINIMO_DOC
                                  FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDF,DB_COMERCIAL.INFO_PUNTO INFOPUNTO
                                  WHERE IDF.PUNTO_ID = INFOPUNTO.ID_PUNTO
                                  AND IDF.TIPO_DOCUMENTO_ID IN ( :paramTipoDocumentoId )
                                  AND IDF.ESTADO_IMPRESION_FACT = :paramEstadoImpresion
                                  AND INFOPUNTO.PERSONA_EMPRESA_ROL_ID =
                                  (SELECT PUNTO1.PERSONA_EMPRESA_ROL_ID FROM INFO_PUNTO PUNTO1 WHERE PUNTO1.ID_punto = :paramIdPunto )) ";

        $objRsm->addScalarResult('FE_EMISION','feEmision','string');

        $objQuery->setParameter("paramIdPunto",$arrayParametros["intIdPunto"]);
        $objQuery->setParameter("paramTipoDocumentoId",$arrayParametros["arrayTipoDocumentoId"]);
        $objQuery->setParameter("paramEstadoImpresion",$arrayParametros["strEstadoFactura"]);

        $objQuery->setSQL($strSql);
        $strFechaEmision = $objQuery->getResult();

        return $strFechaEmision[0];
    }

     /**
     * obtieneTiempoEsperaMeses - Funcion que obtiene el tiempo que tiene un cliente para pagar sus facturas
     * Costo=4
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 10-07-2018
     *
     * @param array $arrayParametros [ intIdPunto => id del punto ]
     *
     * @return srting $strTiempoEsperaMeses
     */
    public function obtieneTiempoEsperaMeses($arrayParametros)
    {
        $objRsm               = new ResultSetMappingBuilder($this->_em);
        $objQuery             = $this->_em->createNativeQuery(null, $objRsm);
        $strTiempoEsperaMeses = "";
        $strEstado            = "Activo";

        $strSql = " SELECT INFOCONTRATODA.TIEMPO_ESPERA_MESES_CORTE FROM DB_COMERCIAL.INFO_CONTRATO_DATO_ADICIONAL INFOCONTRATODA
                        WHERE INFOCONTRATODA.CONTRATO_ID = ( SELECT INFOCON.ID_CONTRATO FROM  INFO_CONTRATO INFOCON
                        WHERE INFOCON.ESTADO = :paramEstado
                        AND INFOCON.PERSONA_EMPRESA_ROL_ID =
                            (SELECT INFOPU.PERSONA_EMPRESA_ROL_ID FROM INFO_PUNTO INFOPU WHERE INFOPU.ID_PUNTO = :paramIdPunto)) ";

        $objRsm->addScalarResult('TIEMPO_ESPERA_MESES_CORTE','feEsperaMeses','integer');

        $objQuery->setParameter("paramIdPunto",$arrayParametros["intIdPunto"]);
        $objQuery->setParameter("paramEstado",$strEstado);

        $objQuery->setSQL($strSql);

        $strTiempoEsperaMeses = $objQuery->getResult();

        return $strTiempoEsperaMeses[0];
    }


     /**
     * obtieneDeudaPorCliente - Funcion que obtiene el saldo total de un cliente
     * Costo=127
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-07-2018
     *
     * @param array $arrayParametros [ intIdPunto => id del punto ]
     *
     * @return array $arraySaldoTotal
     */
    public function obtieneDeudaPorCliente($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsm);
        $arraySaldoTotal = "";

        $strSql = " SELECT NVL(SUM(VISTA.SALDO),'0') SALDO_TOTAL
                        FROM DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO VISTA,DB_COMERCIAL.INFO_PUNTO INFOPUNTO
                        WHERE VISTA.PUNTO_ID = INFOPUNTO.ID_PUNTO
                        AND INFOPUNTO.PERSONA_EMPRESA_ROL_ID =
                        (SELECT PUNTO1.PERSONA_EMPRESA_ROL_ID FROM INFO_PUNTO PUNTO1 WHERE PUNTO1.ID_PUNTO = :paramPuntoId ) ";

        $objRsm->addScalarResult('SALDO_TOTAL','saldoTotal','integer');

        $objQuery->setParameter("paramPuntoId",$arrayParametros["intIdPunto"]);

        $objQuery->setSQL($strSql);

        $arraySaldoTotal = $objQuery->getResult();

        return $arraySaldoTotal[0];
    }


     /**
     * obtieneDiferenciaFechas - Funcion que retorna cuantos dias tiene recorrido una factura
     * Costo=2
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-06-2018
     *
     * @param array $arrayParametros [ strFechaEmision => strFechaEmision,
     *                                 strFechaActual  => tipos de documentos ]
     *
     * @return srting $strDiasRecorridos
     */
    public function obtieneDiferenciaFechas($arrayParametros)
    {
        $objRsm            = new ResultSetMappingBuilder($this->_em);
        $objQuery          = $this->_em->createNativeQuery(null, $objRsm);
        $strDiasRecorridos = "";

        $strSql = " SELECT DB_FINANCIERO.FNCK_CONSULTS.F_GET_DIFERENCIAS_FECHAS(:paramFechaEmision,:paramFechaActual) as DIASFACTURA FROM DUAL ";

        $objRsm->addScalarResult('DIASFACTURA','diasFactura','string');

        $objQuery->setParameter("paramFechaEmision",$arrayParametros["strFechaEmision"]);
        $objQuery->setParameter("paramFechaActual",$arrayParametros["strFechaActual"]);

        $objQuery->setSQL($strSql);
        $strDiasRecorridos = $objQuery->getSingleScalarResult();

        return $strDiasRecorridos;
    }


     /**
     * obtieneFacturasPendientes - Funcion que retorna la cantidad de facturas pendientes por punto
     * Costo=17
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 21-06-2018
     *
     * @param array $arrayParametros [ intIdPunto           => id del punto,
     *                                 arrayTipoDocumentoId => tipos de documentos,
     *                                 strEstadoFactura     => estado de la factura ]
     *
     * @return integer $intCantidadFacturas
     */
    public function obtieneFacturasPendientes($arrayParametros)
    {
        $objRsm            = new ResultSetMappingBuilder($this->_em);
        $objQuery          = $this->_em->createNativeQuery(null, $objRsm);
        $intCantidadFacturas = "";

        $strSql = " SELECT COUNT(IDF.ID_DOCUMENTO) FACTURAS
                        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDF
                        WHERE IDF.TIPO_DOCUMENTO_ID IN (:paramTipoDocumentoId)
                        AND IDF.ESTADO_IMPRESION_FACT = :paramEstadoImpresion
                        AND IDF.PUNTO_ID = :paramIdPunto ";

        $objRsm->addScalarResult('FACTURAS','facturas','integer');

        $objQuery->setParameter("paramIdPunto",$arrayParametros["intIdPunto"]);
        $objQuery->setParameter("paramTipoDocumentoId",$arrayParametros["arrayTipoDocumentoId"]);
        $objQuery->setParameter("paramEstadoImpresion",$arrayParametros["strEstadoFactura"]);

        $objQuery->setSQL($strSql);
        $intCantidadFacturas = $objQuery->getSingleScalarResult();

        return $intCantidadFacturas;
    }


	/**
     * findNotasCredito, obtiene notas de credito enviando el id empresa como parametro
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-12-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 04-03-2015
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 23-05-2016 - Se cambia a que ordene por feCreacion en orden DESC
     * @since 1.2
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 30-12-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     */
    public function findNotasCredito($arrayParametros)
    {
        //Query que obtiene los Datos
        $objQuery          = $this->_em->createQuery();
        $strSelect      = "SELECT idfc ";
        //Query que obtiene el conteo de resultado de datos
        $objQueryCount     = $this->_em->createQuery();
        $strSelectCount = "SELECT count(idfc.id) ";

        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        //Cuerpo del Query
        $strBody        = " FROM 
                            schemaBundle:InfoDocumentoFinancieroCab idfc,
                            schemaBundle:InfoPunto ip,
                            schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                            schemaBundle:InfoOficinaGrupo iog
                    WHERE idfc.tipoDocumentoId=atdf.id
                            AND idfc.puntoId                =   ip.id
                            AND atdf.codigoTipoDocumento    IN  (:arrayTipoDocumento)
                            AND iog.id                      =   idfc.oficinaId
                            AND iog.empresaId               =   :intIdEmpresa ";
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            if( $strTipo == 'SUBGERENTE' )
            {
                $strBody .= " AND ip.usrVendedor IN
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
                $strBody .= " AND ip.usrVendedor IN
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
                $objQueryCount->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strBody .= " AND ip.usrVendedor IN
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

            $objQueryCount->setParameter('strEstadoActivo', $strEstadoActivo);
            $objQueryCount->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }
        if(!empty($arrayParametros['strFeCreacionDesde']))
        {
            $strBody .= " AND idfc.feCreacion             >=  :strFeCreacionDesde ";
            //Query que obtiene los Datos
            $objQuery->setParameter('strFeCreacionDesde', $arrayParametros['strFeCreacionDesde']);
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('strFeCreacionDesde', $arrayParametros['strFeCreacionDesde']);
        }
        if(!empty($arrayParametros['strFeCreacionHasta']))
        {
            $strBody .= " AND idfc.feCreacion             <=  :strFeCreacionHasta ";
            //Query que obtiene los Datos
            $objQuery->setParameter('strFeCreacionHasta', $arrayParametros['strFeCreacionHasta'].' 23:59:59');
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('strFeCreacionHasta', $arrayParametros['strFeCreacionHasta'].' 23:59:59');
        }
        if(!empty($arrayParametros['arrayEstado']))
        {
            $strBody .= " AND idfc.estadoImpresionFact IN (:arrayEstado) ";
            //Query que obtiene los Datos
            $objQuery->setParameter('arrayEstado', $arrayParametros['arrayEstado']);
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('arrayEstado', $arrayParametros['arrayEstado']);
        }
        if(!empty($arrayParametros['intIdPunto']))
        {
            $strBody .= " AND idfc.puntoId = :intIdPunto ";
            //Query que obtiene los Datos
            $objQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        }
        if(!empty($arrayParametros['intMontoInicio']))
        {
            $strBody .= " AND idfc.valorTotal >= :intMontoInicio ";
            //Query que obtiene los Datos
            $objQuery->setParameter('intMontoInicio', $arrayParametros['intMontoInicio']);
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('intMontoInicio', $arrayParametros['intMontoInicio']);
        }
        if(!empty($arrayParametros['intMontoFin']))
        {
            $strBody .= " AND idfc.valorTotal <= :intMontoFin ";
            //Query que obtiene los Datos
            $objQuery->setParameter('intMontoFin', $arrayParametros['intMontoFin']);
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('intMontoFin', $arrayParametros['intMontoFin']);
        }
        if(!empty($arrayParametros['strLogin']))
        {
            $strBody .= " AND LOWER(ip.login) LIKE LOWER(:strLogin) ";
            //Query que obtiene los Datos
            $objQuery->setParameter('strLogin', '%'. $arrayParametros['strLogin']. '%');
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('strLogin', '%'. $arrayParametros['strLogin']. '%');
        }
        if(!empty($arrayParametros['strUsuario']))
        {
            $strBody .= " AND LOWER(idfc.usrCreacion) LIKE LOWER(:strUsuario) ";
            //Query que obtiene los Datos strUsuario
            $objQuery->setParameter('strUsuario', '%'. $arrayParametros['strUsuario']. '%');
            //Query que obtiene conteo de Datos
            $objQueryCount->setParameter('strUsuario', '%'. $arrayParametros['strUsuario']. '%');
        }
        //Query que obtiene los Datos
        $strQuery = $strSelect.$strBody." order by idfc.feCreacion DESC,  idfc.valorTotal ASC";
        $objQuery->setParameter('arrayTipoDocumento',  $arrayParametros['arrayTipoDocumento']);
        $objQuery->setParameter('intIdEmpresa',        $arrayParametros['intIdEmpresa']);
        $objQuery->setDQL($strQuery);
        $arrayDatos = $objQuery->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        //Query que obtiene conteo de Datos
        $objQueryCount->setParameter('arrayTipoDocumento', $arrayParametros['arrayTipoDocumento']);
        $objQueryCount->setParameter('intIdEmpresa',       $arrayParametros['intIdEmpresa']);
        
        if(!empty($arrayDatos)){
            //query de conteo de datos
            $strQuery    = $strSelectCount.$strBody;
            $objQueryCount->setDQL($strQuery);
            $intTotal       = $objQueryCount->getSingleScalarResult();
        }
        
        $arrayResultado['registros']    = $arrayDatos;
        $arrayResultado['total']        = $intTotal;
        return $arrayResultado;
    }
    
	public function findNCParaAprobar($arrayParametros)
	{
//        'intIdOficina' => $intIdOficina, 
//                                    'strEstado' => 'Pendiente',
//                                    'intLimite' => $intLimite,
//                                    'intPagina' => $intPagina,
//                                    'intInicio' => $intInicio,
//                                    'strUsuario' => $strUsuario,
//                                    'strLogin' => $strLogin,
//                                    'intMontoInicio' => $intMontoInicio,
//                                    'intMontoFin' => $intMontoFin,
//                                    'intIdEmpresa' => $intIdEmpresa,
//                                    'strFechaDesde' => $arrayFeDesde[0],
//                                    'strFechaHasta' => $arrayFeHasta[0]
        
        
//		if($punto)
//			$subquery=" AND idfc.puntoId=".$punto;
//		else
//			$subquery="";
//			
//		if($estado=="Inactivo")
//			$subquery_est="idfc.estadoImpresionFact in ('Inactivo','Anulado') AND ";
//		else
//			$subquery_est="idfc.estadoImpresionFact='".$estado."' AND ";
//			
//				
//		$query = $this->_em->createQuery("SELECT idfc
//				FROM 
//						schemaBundle:InfoDocumentoFinancieroCab idfc,
//						schemaBundle:AdmiTipoDocumentoFinanciero atdf,
//						schemaBundle:InfoOficinaGrupo iog
//				WHERE 
//						".$subquery_est."
//						idfc.tipoDocumentoId=atdf.id AND
//						(atdf.codigoTipoDocumento='NC' or atdf.codigoTipoDocumento='NCI')
//						".$subquery." AND
//						iog.id=idfc.oficinaId AND
//						iog.empresaId='".$idEmpresa."'
//						order by idfc.feCreacion desc");
//        
//        //echo($query->getSQL()); die();
//		$total=count($query->getResult());
//		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
//		$resultado['registros']=$datos;
//		$resultado['total']=$total;
//		return $resultado;
        
        $arrayResultado = array();
        try
        {
            $objQb = $this->_em->createQueryBuilder();
            $objQb->select('idfc')
                ->from('schemaBundle:InfoDocumentoFinancieroCab', 'idfc')
                ->from('schemaBundle:AdmiTipoDocumentoFinanciero', 'atdf')
                ->from('schemaBundle:InfoOficinaGrupo', 'iog')
                ->where('idfc.tipoDocumentoId = atdf.id')
                ->andWhere('atdf.estado                 IN (:arrayEstadoTipoDocumentos)')
                ->andWhere('atdf.codigoTipoDocumento    IN (:arrayCodigoTipoDocumento)')
                ->andWhere('idfc.oficinaId              = iog.id')
                ->andWhere('iog.empresaId               =  :intIdEmpresa')
                ->setParameter(':arrayEstadoTipoDocumentos' , $arrayParametros['arrayEstadoTipoDocumentos'])
                ->setParameter(':arrayCodigoTipoDocumento'  , $arrayParametros['arrayCodigoTipoDocumento'])
                ->setParameter(':intIdEmpresa'              , $arrayParametros['intIdEmpresa']);
            if(!empty($arrayParametros['arrayEstadoDocumento']))
            {
                $objQb->andWhere('idfc.estadoImpresionFact IN (:arrayEstadoDocumento)')
                      ->setParameter(':arrayEstadoDocumento', $arrayParametros['arrayEstadoDocumento']);
            }
            if(!empty($arrayParametros['strFechaDesde']))
            {
                $objQb->andWhere('idfc.feCreacion >= :strFechaDesde')
                      ->setParameter(':strFechaDesde', $arrayParametros['strFechaDesde']);
            }
            if(!empty($arrayParametros['strFechaHasta']))
            {
                $objQb->andWhere('idfc.feCreacion <= :strFechaHasta')
                      ->setParameter(':strFechaHasta', $arrayParametros['strFechaHasta']);
            }
            $objQb->groupBy('idfc.numeroFacturaSri')
                  ->orderBy('idfc.numeroFacturaSri');
            $objQb->setFirstResult($arrayParametros['intInicio'])->setMaxResults($arrayParametros['intLimite'])->getResult();
            $objQuery                         = $objQb->getQuery();
            $arrayResultado['arrayResultado'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $arrayResultado['strMensajeError'] = 'Existion un error en getEstadosByTipoDocumentos ' . $ex->getMessage();
        }
        return $arrayResultado;
        
    }
    
	public function find30NCIPorEmpresaPorEstado($idOficina,$estado,$limit, $page, $start,$punto,$idEmpresa){
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";
			
		if($estado=="Inactivo")
			$subquery_est="idfc.estadoImpresionFact in ('Inactivo','Anulado') AND ";
		else
			$subquery_est="idfc.estadoImpresionFact='".$estado."' AND ";
			
				
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmiTipoDocumentoFinanciero atdf,
						schemaBundle:InfoOficinaGrupo iog
				WHERE 
						".$subquery_est."
						idfc.tipoDocumentoId=atdf.id AND
						atdf.codigoTipoDocumento='NCI' ".$subquery." AND
						iog.id=idfc.oficinaId AND
						iog.empresaId='".$idEmpresa."'
						order by idfc.feCreacion desc");
        
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }
    
    /**
     * findNCPorCriterios, obtiene notas de criterio segun criterio de busqueda
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-12-2014
     * @since 1.0
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     */
    public function findNCPorCriterios($arrayParametros)
    {
        //Query que obtiene los Datos
        $query          = $this->_em->createQuery();
        $dqlSelect      = "SELECT idfc ";
        //Query que obtiene el conteo de resultado de datos
        $queryCount     = $this->_em->createQuery();
        $dqlSelectCount = "SELECT count(idfc.id) ";
        //Cuerpo del Query
        $strSqlBody     = " FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                                schemaBundle:InfoOficinaGrupo iog
                            WHERE 
                                idfc.tipoDocumentoId        =   atdf.id 
                                AND atdf.codigoTipoDocumento    =   :strTipoDoc 
                                AND iog.id                      =   idfc.oficinaId 
                                AND iog.empresaId               =   :intIdEmpresa 
                                AND idfc.feCreacion             >=  :strFechaDesde  
                                AND idfc.feCreacion             <=  :strFechaHasta ";
        if (!empty($arrayParametros['strEstado'])){
            //cuerpo del query
            $strSqlBody .= " AND idfc.estadoImpresionFact = :strEstado ";
            //query de datos
            $query->setParameter('strEstado', $arrayParametros['strEstado']);
            //query de conteo de datos
            $queryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        if (!empty($arrayParametros['intIdPunto'])){
            $strSqlBody .= " AND idfc.puntoId = :intIdPunto ";
            $query->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
            $queryCount->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        }
        //query de datos
        $dqlCompleto = $dqlSelect.$strSqlBody." order by idfc.feCreacion desc";
        $query->setParameter('strTipoDoc', $arrayParametros['strTipoDoc']);
        $query->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $query->setParameter('strFechaDesde', $arrayParametros['strFechaDesde'][0]);
        $query->setParameter('strFechaHasta', $arrayParametros['strFechaHasta'][0]);
        //resultado de query de datos
        $query->setDQL($dqlCompleto);
        $arrayDatos = $query->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        //query de conteo de datos
        $queryCount->setParameter('strTipoDoc', $arrayParametros['strTipoDoc']);
        $queryCount->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $queryCount->setParameter('strFechaDesde', $arrayParametros['strFechaDesde'][0]);
        $queryCount->setParameter('strFechaHasta', $arrayParametros['strFechaHasta'][0]);
        
        if(!empty($arrayDatos)){
            //query de conteo de datos
            $dqlCompleto    = $dqlSelectCount.$strSqlBody;
            $queryCount->setDQL($dqlCompleto);
            $intTotal       = $queryCount->getSingleScalarResult();
        }
        $arrayResultado['registros']    = $arrayDatos;
        $arrayResultado['total']        = $intTotal;
        return $arrayResultado;
    }
    
    public function findNCIPorCriterios($idOficina,$fechaDesde,$fechaHasta,$estado,$limit, $page, $start,$punto,$idEmpresa){
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";

		if($estado=="Inactivo")
			$subquery_est="idfc.estadoImpresionFact in ('Inactivo','Anulado') AND ";
		else
			$subquery_est="idfc.estadoImpresionFact='".$estado."' AND ";
			
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmiTipoDocumentoFinanciero atdf,
						schemaBundle:InfoOficinaGrupo iog
				WHERE 
						".$subquery_est."
						idfc.tipoDocumentoId=atdf.id AND
						atdf.codigoTipoDocumento='NCI' AND
						
						iog.id=idfc.oficinaId AND
						iog.empresaId=".$idEmpresa." AND
						idfc.feCreacion >= '".$fechaDesde."' AND 
						idfc.feCreacion <= '".$fechaHasta."'
						$subquery
						order by idfc.feCreacion desc");

		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    } 
    /**
     * Documentación para el método 'getNotasDebito'.
     *
     * Función que retorna las notas de debito de acuerdo a los criterios recibidos
     * Adicional:
     * Se agrega logica para retornar información de acuerdo
     * a la caracteristica de la persona en sesion por medio de las siguiente 
     * descripciones de caracteristica:
     * 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     * Estos cambios solo aplican para Telconet
     * 
     * @param mixed $arrayParametros[
     *                               'intIdOficina'          => id oficina en sesion
     *                               'strEstado'             => estado
     *                               'intIdPunto'            => id del punto en sesion
     *                               'intIdEmpresa'          => id empresa en sesion
     *                               'strFechaDesde'         => fecha inicio de creación
     *                               'strFechaHasta'         => fecha fin de creación
     *                               'intStart'              => numero de inicio para el grid
     *                               'intLimit'              => numero de limite para el grid
     *                               'strPrefijoEmpresa'     => prefijo de la empresa en sesion
     *                               'strTipoPersonal'       => tipo de la persona en sesion
     *                               'intIdPersonEmpresaRol' => id de la persona en sesion
     *                               ]
     * 
     * @return response
     *
     * @author: Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 26-12-2018
     *
     */
    public function getNotasDebito($arrayParametros)
    {
        $objQuery              = $this->_em->createQuery();
        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        $strQueryIn            = " ";
        if( isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']))
        {
            $strSubquery = " AND idfc.puntoId= :intIdPunto ";
            $objQuery->setParameter('intIdPunto'   , $arrayParametros['intIdPunto']);
        }
        else
        {
            $strSubquery = "";
        }
        $strSelect  = "SELECT idfc ";
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroCab idfc,
                            schemaBundle:AdmitipoDocumentoFinanciero atd,
                            schemaBundle:InfoOficinaGrupo iog ";
        $strWhere   = "WHERE idfc.estadoImpresionFact= :strEstado 
                             AND idfc.tipoDocumentoId=atd.id 
                             AND atd.codigoTipoDocumento='ND' 
                             AND iog.id=idfc.oficinaId 
                             AND iog.empresaId= :intIdEmpresa ";
        $strOrderBy = "order by idfc.feCreacion desc ";
        if( !empty($arrayParametros['strFechaDesde']) && !empty($arrayParametros['strFechaHasta']) )
        {
            $strWhere .= " AND idfc.feCreacion >= :strFechaDesde
                           AND idfc.feCreacion <= :strFechaHasta ";
            $objQuery->setParameter('strFechaDesde', $arrayParametros['strFechaDesde']);
            $objQuery->setParameter('strFechaHasta', $arrayParametros['strFechaHasta']);
        }
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            $strFrom     .= ",schemaBundle:InfoPunto ipu ";
            $strWhere   .=" AND ipu.id = idfc.puntoId ";
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
        $strSql  = $strSelect . $strFrom . $strWhere . $strSubquery . $strQueryIn . $strOrderBy;
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
	public function find30NDPorEmpresaPorEstado($idOficina,$estado,$limit, $page, $start,$punto,$idEmpresa){
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";
				
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmitipoDocumentoFinanciero atd,
						schemaBundle:InfoOficinaGrupo iog
				WHERE 
						idfc.estadoImpresionFact='".$estado."' AND
						idfc.tipoDocumentoId=atd.id AND
						atd.codigoTipoDocumento='ND' AND
						iog.id=idfc.oficinaId 
						$subquery
                                               order by idfc.feCreacion desc ");
						
		//->setMaxResults(30)
		//echo $query->getSQL();
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }
    
    public function findNDPorCriterios($idOficina,$fechaDesde,$fechaHasta,$estado,$limit, $page, $start,$punto,$idEmpresa){	
		
		if($punto)
			$subquery=" AND idfc.puntoId=".$punto;
		else
			$subquery="";
			
		$query = $this->_em->createQuery("SELECT idfc
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab idfc,
						schemaBundle:AdmitipoDocumentoFinanciero atd,
						schemaBundle:InfoOficinaGrupo iog
				WHERE 
						idfc.estadoImpresionFact='".$estado."' AND
						idfc.tipoDocumentoId=atd.id AND
						atd.codigoTipoDocumento='ND' AND

						iog.id=idfc.oficinaId AND
						iog.empresaId=".$idEmpresa." AND
						idfc.feCreacion >= '".$fechaDesde."' AND 
						idfc.feCreacion <= '".$fechaHasta."'
                                                   $subquery 
                                              order by idfc.feCreacion desc ");
               //echo($query->getSQL()); die();
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }

        public function findFacturasAbiertasPorPersonaPorOficina($idPersona, $idOficina){
 		$query = $this->_em->createQuery("SELECT idfc
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPunto pto
				WHERE 
								per.personaId=$idPersona AND
								per.id=pto.personaEmpresaRolId AND
								pto.id=idfc.puntoId AND
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento='FAC' AND  
                                idfc.estadoImpresionFact='Activo' AND
                                idfc.oficinaId=$idOficina");
		$resultado=$query->getResult();
                //echo $query->getSQL();die;
		return $resultado;           
        }		

        public function findValorTotalDocumentoPorPersonaPorOfiPorTipoDocPorEmp($idPersonaEmpresaRol,$tipoDoc){
 		$query = $this->_em->createQuery("SELECT sum(idfc.valorTotal) as valorTotal
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
								schemaBundle:InfoPunto pto
				WHERE 
								pto.personaEmpresaRolId=$idPersonaEmpresaRol AND
								pto.id=idfc.puntoId AND
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento='$tipoDoc' AND  
                                idfc.estadoImpresionFact not in ('Anulado','Inactivo')"
								//."AND idfc.oficinaId=$idOficina"
								);
		$resultado=$query->getResult();
                //echo $query->getSQL();die;
		return $resultado;           
        }
        
    /**
    * Obtiene el valor total del documento por punto y tipo de documento.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 2019-01-07 - Se modifica la función para obtener los estados por medio de una consulta a la Base de Datos.
    *
    **/

    public function findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp($idPunto,$tipoDoc){
        $strQuery = $this->_em->createQuery("SELECT sum(idfc.valorTotal) as valorTotal
        FROM 
        schemaBundle:InfoDocumentoFinancieroCab idfc,
        schemaBundle:AdmiTipoDocumentoFinanciero atdf,
        schemaBundle:InfoPunto pto
        WHERE 
        pto.id=:puntoId AND
        pto.id=idfc.puntoId AND
        idfc.tipoDocumentoId=atdf.id AND
        atdf.codigoTipoDocumento=:tipoDocumentoId AND  
        idfc.estadoImpresionFact not in (:estados)"
        );
        $arrayEstados = $this->getParametrosEstados('ESTADOS_DOCUMENTO_FINANCIERO');
        $strQuery->setParameter('estados',$arrayEstados);
        $strQuery->setParameter('tipoDocumentoId',$tipoDoc);
        $strQuery->setParameter('puntoId',$idPunto);             
        $arrayResultado=$strQuery->getResult();
        return $arrayResultado;           
    }
        
    /**
    * Obtiene los parametros de los estados necesarios para excluir documentos financieros, en recibo por caja
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 2019-01-07 - Obtiene los parametros de los estados por nombre de parametro de la tabla Admi_Parametro_Cab
    * 
    * @param string $nombreParametro
    * 
    * @return $query->getResult()
    **/
        
    public function getParametrosEstados($strNombreParametro)
    {
        $strQuery = $this->_em->createQuery("SELECT PD.valor1
                  FROM schemaBundle:AdmiParametroDet PD,
                       schemaBundle:AdmiParametroCab PC
                  WHERE PC.id = PD.parametroId
                  AND PC.nombreParametro = :nombreParametro
                  AND PC.estado = :estado
                  AND PD.estado = :estado");

        $strQuery->setParameter("nombreParametro", $strNombreParametro);
        $strQuery->setParameter("estado", 'Activo');
        return $strQuery->getResult();
    }
 
 
    /**
     * Obtiene Detalle de facturas a contabilizar
     * 
     * @author Desarrollo Inicial
     * @version 1.0 
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-04-25 - Eliminación de vinculo con AdmiProducto, 
     *                           por campos removidos: ctaContableProd y ctaContableProdNc
     * 
     * @param integer $documento
     * 
     * @return resultSet $resultado
     **/
        public function getDetalleContabilizar($documento)
        {
			 $query = $this->_em->createQuery("SELECT
				idfd.id,
				idfd.precioVentaFacproDetalle,
				idfd.porcetanjeDescuentoFacpro,
				ipc.descuentoPlan,
				ipd.productoId,
				ipd.precioItem
				FROM schemaBundle:InfoDocumentoFinancieroCab idfc,
				schemaBundle:InfoDocumentoFinancieroDet idfd,
				schemaBundle:InfoPlanCab ipc, 
				schemaBundle:InfoPlanDet ipd
				WHERE idfc.id=".$documento." AND idfd.documentoId=idfc.id "
                 . "AND ipc.id=idfd.planId and ipd.planId=ipc.id");
			 $resultado=$query->getResult();
			 return $resultado;  
		}		

		public function getValorCtaCliente($documento,$empresa_id)
		{
			/*select idfc.id_documento,
			idfc.numero_factura_sri,
			idfc.subtotal,
			idfc.subtotal_con_impuesto,
			idfc.valor_total,
			iog.cta_contable_clientes 
			from info_documento_financiero_cab idfc, info_oficina_grupo iog
			where idfc.id_documento=123 and iog.empresa_id='09' and iog.nombre_oficina='TRANSTELCO - Guayaquil';*/
			
			$query = $this->_em->createQuery("SELECT idfc.id,
			idfc.numeroFacturaSri,
			idfc.subtotal,
			idfc.subtotalConImpuesto,
			idfc.valorTotal,
			SUBSTRING(idfc.feCreacion,1,10) as feCreacion,
			SUBSTRING(idfc.feEmision,1,10) as feEmision,
			idfc.usrCreacion,
			iog.ctaContableClientes,
			iog.nombreOficina,
			ip.login 
			FROM schemaBundle:InfoDocumentoFinancieroCab idfc, schemaBundle:InfoOficinaGrupo iog, schemaBundle:InfoPunto ip 
			WHERE idfc.id=".$documento." and ip.id=idfc.puntoId and iog.empresaId='".$empresa_id."' and iog.id=idfc.oficinaId");
			
			$resultado=$query->getSingleResult();
			//echo $query->getSQL();
			//print_r($resultado);
			//die;
			return $resultado;    
		}
		
		public function getValorCtaND($documento,$empresa_id)
		{
			/*select idfc.id_documento,
			idfc.numero_factura_sri,
			idfc.subtotal,
			idfc.subtotal_con_impuesto,
			idfc.valor_total,
			iog.cta_contable_clientes 
			from info_documento_financiero_cab idfc, info_oficina_grupo iog
			where idfc.id_documento=123 and iog.empresa_id='09' and iog.nombre_oficina='TRANSTELCO - Guayaquil';*/
			
			$query = $this->_em->createQuery("SELECT idfd.id,
			idfc.numeroFacturaSri,
			idfc.subtotal,
			idfc.subtotalConImpuesto,
			idfd.precioVentaFacproDetalle as valorTotal,
			SUBSTRING(idfc.feCreacion,1,10) as feCreacion,
			SUBSTRING(idfc.feEmision,1,10) as feEmision,
			idfc.usrCreacion,
			iog.nombreOficina,
			ip.login,
			am.ctaContable 
			FROM schemaBundle:InfoDocumentoFinancieroCab idfc, schemaBundle:InfoDocumentoFinancieroDet idfd, schemaBundle:InfoOficinaGrupo iog, schemaBundle:InfoPunto ip , schemaBundle:AdmiMotivo am
			WHERE idfc.id=".$documento." and ip.id=idfc.puntoId and iog.empresaId='".$empresa_id."' and iog.id=idfc.oficinaId and idfc.id=idfd.documentoId and am.id=idfd.motivoId");
			
			//echo $query->getSQL();
			//die();
			
			//$resultado=$query->getSingleResult();
			$resultado=$query->getResult();
			//print_r($resultado);
			//die;
			return $resultado;    
		}
		
		public function getValorIva($documento)
		{
			/*
			 * select 
                idfd.id_doc_detalle,
                idfi.id_doc_imp,
                idfi.impuesto_id,
                idfi.valor_impuesto,
                idfi.porcentaje
                from info_documento_financiero_cab idfc
                left join info_documento_financiero_det idfd on idfd.documento_id=idfc.id_documento
                left join info_documento_financiero_imp idfi on IDFI.DETALLE_DOC_ID=IDFD.ID_DOC_DETALLE
                left join admi_impuesto ai on ai.id_impuesto=idfi.impuesto_id
                where idfc.id_documento=123 and ai.tipo_impuesto='IVA';
                * 
                * 
                select 
                sum(idfi.valor_impuesto) as total_impuesto,
                ai.cta_contable
                from info_documento_financiero_cab idfc
                left join info_documento_financiero_det idfd on idfd.documento_id=idfc.id_documento
                left join info_documento_financiero_imp idfi on IDFI.DETALLE_DOC_ID=IDFD.ID_DOC_DETALLE
                left join admi_impuesto ai on ai.id_impuesto=idfi.impuesto_id
                where idfc.id_documento=123 and ai.tipo_impuesto='IVA'
                group by ai.cta_contable;
			 * */
			$query = $this->_em->createQuery("SELECT 
                sum(idfi.valorImpuesto) as totalImpuesto,
                ai.ctaContable
                FROM schemaBundle:InfoDocumentoFinancieroCab idfc, 
                schemaBundle:InfoDocumentoFinancieroDet idfd, 
                schemaBundle:InfoDocumentoFinancieroImp idfi,
                schemaBundle:AdmiImpuesto ai
                WHERE idfc.id=".$documento." and ai.tipoImpuesto='IVA' and idfd.documentoId=idfc.id and idfi.detalleDocId=idfd.id and ai.id=idfi.impuestoId
                group by ai.ctaContable"); 
			$resultado=$query->getOneOrNullResult();
			//echo $query->getSQL();
			return $resultado;    
		}


        public function findPrimeraFacturaAbiertaPorPersonaEmpresaRolPorOficinaPorValor($idPersonaEmpresaRol, $idOficina,$valor){
			$criterioValor="";
			if($valor){
				$criterioValor=" idfc.valorTotal=$valor AND ";
			}
			$query = $this->_em->createQuery("SELECT idfc
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPunto pto
				WHERE 
								per.id=$idPersonaEmpresaRol AND
								per.id=pto.personaEmpresaRolId AND
								pto.id=idfc.puntoId AND
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento in('FAC','FACP') AND  
                                idfc.estadoImpresionFact in ('Activo','Activa','Courier') AND
								$criterioValor
                                idfc.oficinaId=$idOficina ORDER BY idfc.feCreacion ASC");
			$resultado=$query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();
					//echo $query->getSQL()."\n";
					//die;
			return $resultado;           
        }	

        public function findFacturasAbiertasPorPersonaEmpresaRolPorOficinaPorValor($idPersonaEmpresaRol, $idOficina,$valor){
			$criterioValor="";
			if($valor){
				$criterioValor=" idfc.valorTotal=$valor AND ";
			}
			$query = $this->_em->createQuery("SELECT idfc
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPunto pto
				WHERE 
								per.id=$idPersonaEmpresaRol AND
								per.id=pto.personaEmpresaRolId AND
								pto.id=idfc.puntoId AND
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento in('FAC','FACP') AND  
								$criterioValor
                                 idfc.estadoImpresionFact in ('Activo','Activa','Courier') ORDER BY idfc.feCreacion ASC");
			$resultado=$query->getResult();
					//echo $query->getSQL();die;
			return $resultado;           
        }
        
        public function findFacturasAbiertasPorPuntoPorOficinaPorValor($idpunto,$valor){

			$criterioValor="";
			if($valor){
				$criterioValor=" idfc.valorTotal=$valor AND ";
			}
			$query = $this->_em->createQuery("SELECT idfc
				FROM 
                                    schemaBundle:InfoDocumentoFinancieroCab idfc,
                                    schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                                    schemaBundle:InfoPunto pto
				WHERE 
                                    pto.id =$idpunto AND
                                    pto.id=idfc.puntoId AND
                                    idfc.tipoDocumentoId=atdf.id AND
                                    atdf.codigoTipoDocumento in('FAC','FACP') AND  
                                    $criterioValor
                                    idfc.estadoImpresionFact in ('Activo','Activa','Courier') 
                                ORDER BY 
                                    idfc.feCreacion ASC");
			$resultado=$query->getResult();
					//echo $query->getSQL();die;
			return $resultado;           
        }        
     
    /**
     * Permite listar las facturas en estado pendiente
     *
     * @param array $arrayParametros['intIdOficina'        Id de la oficina a consultar
     *                               'strfechaDesde'       Fecha de inicio
     *                               'strfechaHasta'       Fecha de fin
     *                               'intPtoCliente'       Id del punto cliente
     *                               'intEmpresaId'        Id de empresa en sesión
     *                               'intLimit'            Rango inicial de consulta
     *                               'intStart'            Rango final de consulta
     *                               'strTipoDoc'          Tipo de Documento 
     *                               'strUsrCreacion'      Usuario de creación del documento  
     *                               'objContainer'        Objeto contenedor]
     * 
     * @return Array $arrayResultado['registros'] Listado de facturas pendientes.
     *               $arrayResultado['total']     Total de registros.
     * 
     * Costo-Query: Count : 24
     *              Select: 31
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 24-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se cambia el método para que consulte por $intIdOficina en caso de que sea diferente de NULL
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.2 16-09-2016
     * Se cambia el método de acceso a los datos, se implementan funciones SQL en el Query
     * Se obtiene el nombre del Vendedor y la Descripción de la Factura.
     * Se cambia el código SQL para el seteo de la :fecha_hasta para que sea inclusive en la consulta.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 14-09-2017 - Se agrega envío de parámetros mediante un arreglo, adicional se agrega envío de parámetro usrCreacion. 
     */
    public function findListadoFacturasPendientes($arrayParametros)
    { 
        $arrayResultado['registros'] = null;
        $rsmBuilder                  = new ResultSetMappingBuilder($this->_em);
        $ntvQuery                    = $this->_em->createNativeQuery(null, $rsmBuilder);
        
        $intIdOficina  = $arrayParametros['intIdOficina'];        
        $strFechaDesde = $arrayParametros['strfechaDesde'];
        $strFechaHasta = $arrayParametros['strfechaHasta'];
        $intIdCliente  = $arrayParametros['intIdCliente']; 
        $intPtoCliente = $arrayParametros['intPtoCliente'];    
        $intEmpresaId  = $arrayParametros['intEmpresaId'];  
        $intLimit      = $arrayParametros['intLimit'];  
        $intStart      = $arrayParametros['intStart'];  
        $strTipoDoc    = $arrayParametros['strTipoDoc'];  
        $strUsrCreacion= $arrayParametros['strUsrCreacion'];  
        
        $arrayParametros['objContainer']    = $this->container;          

        $strSqlCount = " SELECT COUNT(DFC.ID_DOCUMENTO) AS TOTAL ";
        $strSqlBody  = " FROM       DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  DFC
                         LEFT OUTER JOIN (SELECT * FROM (SELECT IDFD.*, 
                                                                ROW_NUMBER() OVER 
                                                                (PARTITION BY DOCUMENTO_ID ORDER BY PRECIO_VENTA_FACPRO_DETALLE DESC) RN 
                                                         FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFD) WHERE RN = 1 ) 
                                                                                 DFD ON DFD.DOCUMENTO_ID      = DFC.ID_DOCUMENTO        
                         LEFT JOIN DB_COMERCIAL.INFO_PUNTO                      PO  ON PO.ID_PUNTO           = DFC.PUNTO_ID
                         LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL        PER ON PER.ID_PERSONA_ROL    = PO.PERSONA_EMPRESA_ROL_ID
                         LEFT JOIN DB_COMERCIAL.INFO_PERSONA                    PE  ON PE.ID_PERSONA         = PER.PERSONA_ID
                         LEFT JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDF ON TDF.ID_TIPO_DOCUMENTO = DFC.TIPO_DOCUMENTO_ID
                         LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO              OG  ON OG.ID_OFICINA         = DFC.OFICINA_ID
                         LEFT JOIN DB_COMERCIAL.INFO_PERSONA                    PV  ON PV.LOGIN              = PO.USR_VENDEDOR";

        $strSqlWhere = "  WHERE DFC.NUMERO_FACTURA_SRI    IS NULL
                           AND  DFC.ESTADO_IMPRESION_FACT =   :estado
                           AND  TDF.CODIGO_TIPO_DOCUMENTO IN (:codigoTipoDocumento) 
                           AND  OG.EMPRESA_ID             =   :empresaId ";
        
        if($strFechaDesde != "" && $strFechaDesde != null)
        {
            $strSqlWhere .= " AND DFC.FE_CREACION >= :fe_desde";
            $ntvQuery->setParameter('fe_desde', date('Y/m/d', strtotime($strFechaDesde)));
        }

        if($strFechaHasta != "" && $strFechaHasta != null)
        {
            $strSqlWhere .= " AND DFC.FE_CREACION < TO_DATE(:fe_hasta, 'yyyy/mm/dd') + 1";
            $ntvQuery->setParameter('fe_hasta', $strFechaHasta);
        }

        if($strUsrCreacion != "" && $strUsrCreacion != null)
        {
            $strSqlWhere .= " AND DFC.USR_CREACION= :strUsrCreacion";
            $ntvQuery->setParameter('strUsrCreacion', $strUsrCreacion);
        }
        
        if($intIdCliente != "" && is_numeric($intIdCliente))
        {
            $strSqlWhere .= " AND PE.ID_PERSONA= :id_cliente";
            $ntvQuery->setParameter('id_cliente', $intIdCliente);
        }

        if($intPtoCliente != "" && is_numeric($intPtoCliente))
        {
            $strSqlWhere .= " AND PO.ID_PUNTO= :ptocliente";
            $ntvQuery->setParameter('ptocliente', $intPtoCliente);
        }

        if(!empty($intIdOficina))
        {
            $strSqlWhere .= " AND DFC.OFICINA_ID = :intIdOficina";
            $ntvQuery->setParameter('intIdOficina', $intIdOficina);
        }

        $rsmBuilder->addScalarResult('TOTAL',                  'total',               'integer');
        $rsmBuilder->addScalarResult('ID',                     'id',                  'integer');
        $rsmBuilder->addScalarResult('CODIGO_TIPO_DOCUMENTO',  'codigoTipoDocumento', 'string');
        $rsmBuilder->addScalarResult('FE_CREACION',            'feCreacion',          'string');
        $rsmBuilder->addScalarResult('SUB_TOTAL',              'subtotal',            'float');
        $rsmBuilder->addScalarResult('SUB_TOTAL_CON_IMPUESTO', 'subtotalConImpuesto', 'float');
        $rsmBuilder->addScalarResult('SUB_TOTAL_DESCUENTO',    'subtotalDescuento',   'float');
        $rsmBuilder->addScalarResult('VALOR_TOTAL',            'valorTotal',          'float');
        $rsmBuilder->addScalarResult('DESCRIPCION_PUNTO',      'descripcionPunto',    'string');
        $rsmBuilder->addScalarResult('LOGIN',                  'login',               'string');
        $rsmBuilder->addScalarResult('NOMBRES',                'nombres',             'string');
        $rsmBuilder->addScalarResult('APELLIDOS',              'apellidos',           'string');
        $rsmBuilder->addScalarResult('RAZON_SOCIAL',           'razonSocial',         'string');
        $rsmBuilder->addScalarResult('NOMBRE_OFICINA',         'nombreOficina',       'string');
        $rsmBuilder->addScalarResult('VENDEDOR',               'vendedor',            'string');
        $rsmBuilder->addScalarResult('OBSERVACION',            'observacion',         'string');
        $rsmBuilder->addScalarResult('USR_CREACION',           'usrCreacion',         'string');
        
        $ntvQuery->setParameter('codigoTipoDocumento', $strTipoDoc);
        $ntvQuery->setParameter('estado',              'Pendiente');
        $ntvQuery->setParameter('empresaId',           $intEmpresaId);

        $ntvQuery->setSQL($strSqlCount . $strSqlBody . $strSqlWhere);
        
        $arrayResultado['total'] = intval($ntvQuery->getSingleScalarResult());

        if($arrayResultado['total'] > 0)
        {
            $strSqlSelect = "   SELECT DFC.ID_DOCUMENTO         ID,
                                TDF.CODIGO_TIPO_DOCUMENTO       CODIGO_TIPO_DOCUMENTO,
                                SUBSTR (DFC.FE_CREACION, 1, 10) FE_CREACION,
                                DFC.SUBTOTAL                    SUB_TOTAL,
                                DFC.SUBTOTAL_CON_IMPUESTO       SUB_TOTAL_CON_IMPUESTO ,
                                DFC.SUBTOTAL_DESCUENTO          SUB_TOTAL_DESCUENTO,
                                DFC.VALOR_TOTAL                 VALOR_TOTAL,
                                PO.DESCRIPCION_PUNTO            DESCRIPCION_PUNTO,
                                PO.LOGIN                        LOGIN,
                                TRIM(PE.NOMBRES)                NOMBRES,
                                TRIM(PE.APELLIDOS)              APELLIDOS ,
                                TRIM(PE.RAZON_SOCIAL)           RAZON_SOCIAL,
                                OG.NOMBRE_OFICINA               NOMBRE_OFICINA,
                                CONCAT(TRIM(PV.NOMBRES), CONCAT(' ', TRIM(PV.APELLIDOS))) VENDEDOR ,
                                REPLACE(DFD.OBSERVACIONES_FACTURA_DETALLE, Chr(10), ' ') OBSERVACION,
                                DFC.USR_CREACION                USR_CREACION "
                                ;
            $ntvQuery->setSQL("$strSqlSelect $strSqlBody $strSqlWhere ORDER BY DFC.ID_DOCUMENTO ASC");

            $arrayResultado['registros'] = $this->setQueryLimit($ntvQuery, $intLimit, $intStart)->getResult();
        }
        
        return $arrayResultado;
    }

    /**
     * Permite calcular la suma total de las facturas obtenidas según los parámetros enviados por el usuario
     *
     * @param array $arrayParametros['intIdOficina'        Id de la oficina a consultar
     *                               'strfechaDesde'       Fecha de inicio
     *                               'strfechaHasta'       Fecha de fin
     *                               'intPtoCliente'       Id del punto cliente
     *                               'intEmpresaId'        Id de empresa en sesión
     *                               'intLimit'            Rango inicial de consulta
     *                               'intStart'            Rango final de consulta
     *                               'strTipoDoc'          Tipo de Documento 
     *                               'strUsrCreacion'      Usuario de creación del documento  
     *                               'objContainer'        Objeto contenedor]
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 24-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se cambia el método para que consulte por $intIdOficina en caso de que sea diferente de NULL
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 14-09-2017 - Se agrega envío de parámetros mediante un arreglo, adicional se agrega envío de parámetro usrCreacion.
     */
    public function findTotalesFacturasPendientes($arrayParametros)
    {
        $intIdOficina  = $arrayParametros['intIdOficina'];        
        $strFechaDesde = $arrayParametros['strfechaDesde'];
        $strFechaHasta = $arrayParametros['strfechaHasta'];
        $intIdCliente  = $arrayParametros['intIdCliente']; 
        $intPtoCliente = $arrayParametros['intPtoCliente'];    
        $intEmpresaId  = $arrayParametros['intEmpresaId'];  
        $strTipoDoc    = $arrayParametros['strTipoDoc'];  
        $strUsrCreacion= $arrayParametros['strUsrCreacion'];         
        $query = $this->_em->createQuery();

        $dql    ="SELECT 
                    sum(idfc.subtotal) as subtotal,
                    sum(idfc.subtotalConImpuesto) as subtotalConImpuesto,
                    sum(idfc.subtotalDescuento) as subtotalDescuento, 
                    sum(idfc.valorTotal) as valorTotal";

        $cuerpo=" FROM 
                    schemaBundle:InfoDocumentoFinancieroCab idfc,
                    schemaBundle:InfoPunto pto,
                    schemaBundle:InfoPersonaEmpresaRol per,
                    schemaBundle:InfoPersona ip,
                    schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                    schemaBundle:InfoOficinaGrupo iog
                 WHERE 
                    idfc.estadoImpresionFact=:estado
                    and idfc.tipoDocumentoId=atdf.id
                    and atdf.codigoTipoDocumento in (:codigoTipoDocumento)
                    and iog.id=idfc.oficinaId
                    and pto.id=idfc.puntoId
                    and pto.personaEmpresaRolId=per.id
                    and per.personaId=ip.id
                    and iog.empresaId=:empresaId 
                    and idfc.numeroFacturaSri is null ";

        $dql    .=$cuerpo;

        if($strFechaDesde!="" && $strFechaDesde!=null)
        {
            $dql.=" and idfc.feCreacion >= :fe_desde";
            $query->setParameter('fe_desde',date('Y/m/d', strtotime($strFechaDesde)));
        }

        if($strFechaHasta!="" && $strFechaHasta!=null)
        {
            $dql.=" and idfc.feCreacion <= :fe_hasta";
            $query->setParameter('fe_hasta',date('Y/m/d', strtotime($strFechaHasta)));
        }
        
       if($strUsrCreacion!="" && $strUsrCreacion!=null)
        {
            $dql.=" and idfc.usrCreacion = :usrCreacion";
            $query->setParameter('usrCreacion', $strUsrCreacion);
        }        

        if($intIdCliente!="" &&  is_numeric($intIdCliente))
        {
            $dql.=" and ip.id= :id_cliente";
            $query->setParameter('id_cliente',$intIdCliente);
        }

        if($intPtoCliente!="" &&  is_numeric($intPtoCliente))
        {
            $dql.=" and pto.id= :ptocliente";
            $query->setParameter('ptocliente',$intPtoCliente);
        }
        
        if( !empty($intIdOficina) )
        {
            $dql .= " and idfc.oficinaId = :intIdOficina";
            $query->setParameter('intIdOficina',  $intIdOficina);
        }

        $query->setParameter('codigoTipoDocumento', $strTipoDoc);
        $query->setParameter('estado','Pendiente');
        $query->setParameter('empresaId',$intEmpresaId);

        $dql.=" ORDER BY
                idfc.id ";        
       
        $query->setDQL($dql);
        $datos= $query->getResult();

        $resultado['registros'] =$datos;
        return $resultado;
    }
		
        public function findListadoFacturasProporcionalesPendientes($idOficina,$fechaDesde,$fechaHasta,$idcliente)
        {
			$subquery="";
			
			if($fechaDesde!=""){
				$subquery="idfc.feCreacion >= '".$fechaDesde."' AND ";
			}
			
			if($fechaDesde!=""){
				$subquery.="idfc.feCreacion <= '".$fechaHasta."' AND ";
			}
			
			if($idcliente!="")
				$subquery.="ip.id=".$idcliente." AND ";
			
			$query = $this->_em->createQuery("SELECT idfc.id,
								substring(idfc.feCreacion,1,10) as feCreacion,
								idfc.subtotal,
								idfc.subtotalConImpuesto,
								idfc.subtotalDescuento, 
								idfc.valorTotal, 
								pto.descripcionPunto,
								pto.login,
								ip.nombres,
								ip.apellidos,
								ip.razonSocial,
								iog.nombreOficina
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
								schemaBundle:InfoPunto pto,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPersona ip,
								schemaBundle:InfoOficinaGrupo iog,
								schemaBundle:AdmiTipoDocumentoFinanciero atdf
				WHERE 
                                idfc.estadoImpresionFact='Pendiente' AND
                                idfc.tipoDocumentoId=atdf.id AND
                                atdf.codigoTipoDocumento='FACP' AND
                                iog.id=idfc.oficinaId AND
								".$subquery."
								pto.id=idfc.puntoId AND
								pto.personaEmpresaRolId=per.id AND
								per.personaId=ip.id 
                                ORDER BY idfc.id");
			$resultado=$query->getResult();
			return $resultado;
		}
		
		public function findListadoFacturasProcesadas($idOficina,$fechaDesde,$fechaHasta,$idestado)
        {
			$subquery="";
			
			
			if($idestado=="Rechazada")
			{
				if($fechaDesde!=""){
					$subquery="idfc.feCreacion >= '".$fechaDesde."' AND ";
				}
				
				if($fechaDesde!=""){
					$subquery.="idfc.feCreacion <= '".$fechaHasta."' AND ";
				}
			}
			else
			{
				if($fechaDesde!=""){
					$subquery="idfc.feEmision >= '".$fechaDesde."' AND ";
				}
				
				if($fechaDesde!=""){
					$subquery.="idfc.feEmision <= '".$fechaHasta."' AND ";
				}
			}
			
			$query = $this->_em->createQuery("SELECT idfc.id,
								idfc.numeroFacturaSri,
								substring(idfc.feEmision,1,10) as feEmision,
								substring(idfc.feCreacion,1,10) as feCreacion,
								idfc.subtotal,
								idfc.subtotalConImpuesto,
								idfc.subtotalDescuento, 
								idfc.valorTotal, 
								pto.descripcionPunto,
								pto.login,
								ip.nombres,
								ip.apellidos,
								ip.razonSocial,
								iog.nombreOficina
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
								schemaBundle:InfoPunto pto,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPersona ip,
								schemaBundle:InfoOficinaGrupo iog
				WHERE 
                                idfc.esAutomatica='S' AND
                                idfc.usrCreacion='telcos' AND
                                idfc.estadoImpresionFact='".$idestado."' AND
                                iog.id=idfc.oficinaId AND
								".$subquery."
								pto.id=idfc.puntoId AND
								pto.personaEmpresaRolId=per.id AND
								per.personaId=ip.id 
                                ORDER BY idfc.id");
			$resultado=$query->getResult();
			//echo $query->getSQL();
			return $resultado;
		}			
		public function findListadoFacturasProporcionalesProcesadas($idOficina,$fechaDesde,$fechaHasta,$idestado)
        {
			$subquery="";
			
			if($idestado=="Rechazada")
			{
				if($fechaDesde!=""){
					$subquery="idfc.feCreacion >= '".$fechaDesde."' AND ";
				}
				
				if($fechaDesde!=""){
					$subquery.="idfc.feCreacion <= '".$fechaHasta."' AND ";
				}
			}
			else
			{
				if($fechaDesde!=""){
					$subquery="idfc.feEmision >= '".$fechaDesde."' AND ";
				}
				
				if($fechaDesde!=""){
					$subquery.="idfc.feEmision <= '".$fechaHasta."' AND ";
				}
			}
			
			$query = $this->_em->createQuery("SELECT idfc.id,
								idfc.numeroFacturaSri,
								substring(idfc.feEmision,1,10) as feEmision,
								substring(idfc.feEmision,1,10) as feCreacion,
								idfc.subtotal,
								idfc.subtotalConImpuesto,
								idfc.subtotalDescuento, 
								idfc.valorTotal, 
								pto.descripcionPunto,
								pto.login,
								ip.nombres,
								ip.apellidos,
								ip.razonSocial,
								iog.nombreOficina
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
								schemaBundle:InfoPunto pto,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPersona ip,
								schemaBundle:InfoOficinaGrupo iog
				WHERE 
                                idfc.esAutomatica='S' AND
                                idfc.usrCreacion='telcos_proporcional' AND
                                idfc.estadoImpresionFact='".$idestado."' AND
                                iog.id=idfc.oficinaId AND
								".$subquery."
								pto.id=idfc.puntoId AND
								pto.personaEmpresaRolId=per.id AND
								per.personaId=ip.id 
                                ORDER BY idfc.id");
			$resultado=$query->getResult();
			//echo $query->getSQL();
			return $resultado;
		}			
		public function findListadoFacturasProcesadasAts($idEmpresa,$fechaDesde,$fechaHasta)
        {
			$subquery="";
			
			if($fechaDesde!=""){
				$subqueryFechas="idfc.feEmision >= '".$fechaDesde."' AND ";
			}
			
			if($fechaDesde!=""){
				$subqueryFechas.="idfc.feEmision <= '".$fechaHasta."' AND ";
			}
			
			$query = $this->_em->createQuery("SELECT 
								per.id as idPersonaEmpresaRol,			
								ip.identificacionCliente,
								tdf.codigoTipoDocumento,
								ip.tipoIdentificacion,
								count(idfc.id) as totalRegistros,
								sum(idfc.subtotal) as subtotal,
								sum(idfc.subtotalConImpuesto) as subtotalConImpuesto,
								sum(idfc.subtotalDescuento) as subtotalDescuento,
								sum(idfc.valorTotal) as valorTotal			
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
								schemaBundle:InfoPunto pto,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPersona ip,
								schemaBundle:InfoOficinaGrupo iog,
								schemaBundle:AdmiTipoDocumentoFinanciero tdf
				WHERE 
								idfc.tipoDocumentoId=tdf.id AND
								tdf.codigoTipoDocumento in ('FACP','FAC','NC') AND	
                                idfc.estadoImpresionFact in ('Activo','Activa','Cerrado','Cerrada','Courier') AND
                                iog.empresaId=".$idEmpresa." AND
                                iog.id=idfc.oficinaId AND
								".$subqueryFechas."
								pto.id=idfc.puntoId AND
								pto.personaEmpresaRolId=per.id AND
								per.personaId=ip.id 
                                GROUP BY 	
								per.id,
								ip.identificacionCliente,
								tdf.codigoTipoDocumento,
								ip.tipoIdentificacion
								ORDER BY 
								per.id,
								ip.identificacionCliente,
								tdf.codigoTipoDocumento,
								ip.tipoIdentificacion");
			//$resultado=$query->setMaxResults(1000)->getResult();
			$resultado=$query->getResult();
			//echo $query->getSQL();die;
			return $resultado;
		}


		public function findListadoFacturasAnuladasAts($idEmpresa,$fechaDesde,$fechaHasta)
        {
			$subquery="";		
			if($fechaDesde!="")
				$subqueryFechas="idfc.feEmision >= '".$fechaDesde."' AND ";		
			if($fechaDesde!="")
				$subqueryFechas.="idfc.feEmision <= '".$fechaHasta."' AND ";
			$query = $this->_em->createQuery("SELECT 
								idfc.numeroFacturaSri,
								tdf.codigoTipoDocumento,
								iog.id as oficinaId
								
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
								schemaBundle:InfoOficinaGrupo iog,
								schemaBundle:AdmiTipoDocumentoFinanciero tdf
				WHERE 
								idfc.tipoDocumentoId=tdf.id AND
								tdf.codigoTipoDocumento in ('FACP','FAC','NC') AND	
                                idfc.estadoImpresionFact in ('Anulado','Anulada') AND
                                iog.id=idfc.oficinaId AND
								".$subqueryFechas."
                                iog.empresaId=".$idEmpresa." 								
								ORDER BY 
								idfc.numeroFacturaSri");
			//$resultado=$query->setMaxResults(1000)->getResult();
			$resultado=$query->getResult();
			//echo $query->getSQL();die;
			return $resultado;
		}		
		
		
		public function obtenerFacturasParaMigra()
		{
			$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a 
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.feEmision >= '".date('Y/m/d', strtotime('2013-05-01'))."'
				and a.feEmision < '".date('Y/m/d', strtotime('2013-06-01'))."' 
				and a.id=159450
				and a.tipoDocumentoId in (1,5)
				order by a.feEmision,a.id");
			
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();
			//die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
		return $resultado;
		}

		public function obtenerFacturasAnuladasParaMigra()
		{
			$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a 
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.feEmision >= '".date('Y/m/d', strtotime('2013-05-01'))."'
				and a.feEmision < '".date('Y/m/d', strtotime('2013-06-01'))."' 
				and a.tipoDocumentoId in (1,5)
				and a.valorTotal>0
				and a.id=159446
				and a.estadoImpresionFact in ('Anulado','Inactivo')
				order by a.feEmision,a.id");
				
			//Abril: 1/30 
			
			/*$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a 
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.tipoDocumentoId in (1,5)
				order by a.feEmision,a.id");*/
			
			$total=count($query->getResult());
			$datos = $query->getResult();
			echo $query->getSQL();
			//die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
		return $resultado;
		}
		
		public function obtenerNCParaMigra()
		{
			$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a 
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.feEmision >= '".date('Y/m/d', strtotime('2013-08-01'))."'
				and a.feEmision < '".date('Y/m/d', strtotime('2013-09-01'))."' 
				and a.tipoDocumentoId in (6)
				order by a.feEmision,a.id");
			//abril 1/30
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();
			//die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
			return $resultado;
		}
		
		public function obtenerNDParaMigra()
		{
			$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.feEmision >= '".date('Y/m/d', strtotime('2013-06-01'))."'
				and a.feEmision < '".date('Y/m/d', strtotime('2013-07-01'))."' 
				and a.tipoDocumentoId in (7)
				order by a.feEmision,a.id");
			//abril 1/30
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();
			//die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
			return $resultado;
		}
		
		public function obtenerNCAnuladasParaMigra()
		{
			$query= $this->_em->createQuery("
				select a
				from schemaBundle:InfoDocumentoFinancieroCab a 
				where 
				a.numFactMigracion is null 
				and a.numeroFacturaSri is not null 
				and a.feEmision >= '".date('Y/m/d', strtotime('2013-08-01'))."'
				and a.feEmision < '".date('Y/m/d', strtotime('2013-09-01'))."' 
				and a.tipoDocumentoId in (6)
				and a.estadoImpresionFact not in ('Pendiente','Rechazada','Courier','Activo','Cerrado')
				order by a.feEmision,a.id");
			
			//Abril 1/30
			//Mayo 1/31
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();
			//die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
			return $resultado;
		}

    /**
    * Documentación para el método 'findFacturasPorCliente'.
    *
    * consulta las facturas por id_persona y por empresa
    * @param idPersona
    * @param idEmpresa
    * @return json.
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 17-12-2014
    */
    public function findFacturasPorCliente($idPersona,$idEmpresa)
    {
        $query = $this->_em->createQuery("
            select 
                a 
            from 
                schemaBundle:InfoDocumentoFinancieroCab a,
                schemaBundle:InfoPunto b,
                schemaBundle:InfoPersonaEmpresaRol c,
                schemaBundle:InfoPersona d,
                schemaBundle:InfoEmpresaRol e,
                schemaBundle:AdmiTipoDocumentoFinanciero f
            where 
                a.puntoId=b.id
                AND b.personaEmpresaRolId=c.id
                AND c.personaId=d.id 
                AND c.empresaRolId=e.id
                AND d.id = :idPersona  
                AND e.empresaCod= :idEmpresa
                AND a.estadoImpresionFact in (:estados)
                AND a.tipoDocumentoId=f.id
                AND f.codigoTipoDocumento in (:codigos)
            order by 
                a.feCreacion");
        $estados=array('Courier','Activo','Activa');
        $codigos=array('FACP','FAC');
        $query->setParameter('idPersona',$idPersona);
        $query->setParameter('idEmpresa',$idEmpresa);
        $query->setParameter('codigos',$codigos);
        $query->setParameter('estados',$estados);
        $datos = $query->getResult();
        return $datos;		
    }
    /**
     * Documentación para la función 'getUltimaFacturaPorPersonaEmpresaRol'
     * 
     * Función que retorna la fecha de emision de la última factura por medio del IdPersonaEmpresaRol del cliente
     * 
     * @param  int  "intIdPersonaEmpresaRol" => "IdPersonaEmpresaRol del cliente a buscar"
     * 
     * @return array $arrayFechaEmision
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 10-01-2019
     * Costo del query: 49
     */
    public function getUltimaFacturaPorPersonaEmpresaRol($intIdPersonaEmpresaRol)
    {
        $arrayFechaEmision = array();
        try
        {
            $objRsmBuilder = new ResultSetMappingBuilder($this->_em);
            $objQuery      = $this->_em->createNativeQuery(null, $objRsmBuilder);
            $strSelect     = " SELECT T1.FECHAEMISION
                                FROM
                                (SELECT
                                    CASE
                                    WHEN IDFC.FE_EMISION IS NULL
                                    THEN ' '
                                    ELSE TO_CHAR(IDFC.FE_EMISION,'dd/mm/yyyy hh24:mi')
                                    END AS FECHAEMISION ";

            $strFrom       = " FROM DB_COMERCIAL.INFO_PUNTO IPCLT,
                                    DB_COMERCIAL.INFO_SERVICIO ISER,
                                    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC ";

            $strWhere      = " WHERE IPCLT.ID_PUNTO             =ISER.PUNTO_ID
                                    AND IDFC.PUNTO_ID                =ISER.PUNTO_FACTURACION_ID
                                    AND IDFC.ESTADO_IMPRESION_FACT  IN ('Activo','Cerrado')
                                    AND IPCLT.PERSONA_EMPRESA_ROL_ID = :intIdPersonaEmpresaRol
                                    ORDER BY IDFC.FE_EMISION DESC
                                    )T1
                            WHERE ROWNUM =1 ";
            $objRsmBuilder->addScalarResult('FECHAEMISION', 'fechaEmision', 'string');
            $objQuery     ->setParameter('intIdPersonaEmpresaRol', $intIdPersonaEmpresaRol);
            $strSql      = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);

            $arrayFechaEmision = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayFechaEmision;
    }

		public function findPrimeraFacturaValidaPorPersonaEmpresaRol($idPersonaEmpresaRol){
			$query = $this->_em->createQuery("select a.id,
                                a.estadoImpresionFact,a.numeroFacturaSri,a.valorTotal,a.feEmision 
				from 
				schemaBundle:InfoDocumentoFinancieroCab a,
				schemaBundle:InfoPunto b,
				schemaBundle:InfoPersonaEmpresaRol c,
				schemaBundle:AdmiTipoDocumentoFinanciero t
				where 
				a.puntoId=b.id AND
				b.personaEmpresaRolId=c.id AND
				c.id = $idPersonaEmpresaRol AND
				a.tipoDocumentoId=t.id AND
				t.codigoTipoDocumento in ('FAC') AND
				a.esAutomatica='S' AND
				a.recurrente='S' AND
				a.estadoImpresionFact in ('Courier' , 'Activo', 'Activa','Cerrado','Cerrada')
				order by a.feCreacion asc");
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();			
			//echo $query->getSQL();
			//die;
		
		return $datos;		
		}
                
    public function findPrimeraFacturaValidaPorPunto($idPunto){
        $query = $this->_em->createQuery("select a.id,
                    a.estadoImpresionFact,a.numeroFacturaSri,
                    a.valorTotal,a.feEmision 
                    from 
                    schemaBundle:InfoDocumentoFinancieroCab a,
                    schemaBundle:InfoPunto b,
                    schemaBundle:AdmiTipoDocumentoFinanciero t
                    where 
                    a.puntoId=b.id AND
                    b.id = :puntoId AND
                    a.tipoDocumentoId=t.id AND
                    t.codigoTipoDocumento in (:tiposDocumento) AND
                    a.estadoImpresionFact not in (:estados)
                    and a.estadoImpresionFact is not null order by a.feCreacion asc");
        $estados=array('Pendiente' , 'Anulado', 'Anulada','Inactivo','Inactiva','Rechazada','Rechazado','null','PendienteError','PendienteSri');
        $tiposDocumentos=array('FAC','FACP','ND');        
        $query->setParameter('estados',$estados);   
        $query->setParameter('tiposDocumento',$tiposDocumentos);
        $query->setParameter('puntoId',$idPunto);
       $datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();			
       return $datos;		
    }
    
    public function getSaldoTotalByIdenficacion($arrayParametros)
    {
        $intEmpresaMd = $arrayParametros['codEmpresa'];  
        $strEstado = 'Eliminado'; 
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery  = "select sum(idps.saldo) TOTAL, count(*) CUENTA from (select distinct IPT.id_punto punto_id,vtc.saldo SALDO
                        from DB_COMERCIAL.INFO_PERSONA IP,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                        DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                        DB_COMERCIAL.INFO_PUNTO IPT,
                        DB_COMERCIAL.vista_estado_cuenta_resumido VTC
                        where IP.ID_PERSONA = IPER.PERSONA_ID
                        and vtc.punto_id = ipt.id_punto
                        and IPER.ID_PERSONA_ROL = IPT.PERSONA_EMPRESA_ROL_ID
                        and IER.id_empresa_rol = IPER.empresa_rol_id
                        and ip.identificacion_cliente = :ident
                        and IER.EMPRESA_COD = :empresaMD
                        and VTC.saldo != 0
                        and IPT.estado != :estado) IDPS";
        

        
        $objQuery->setParameter("ident", $arrayParametros["identificacion"]);


        $objQuery->setParameter('empresaMD', $intEmpresaMd);
        $objQuery->setParameter("estado", $strEstado);
        $objRsm->addScalarResult(strtoupper('TOTAL'), 'total', 'float');
        $objRsm->addScalarResult(strtoupper('CUENTA'), 'cuenta', 'integer');
        $objQuery->setSQL($strQuery);
        return $objQuery->getResult();		
    } 
    
    public function findPuntosIdByIdentificacion($arrayParametros)
    {
        $intEmpresaMD = trim($arrayParametros['codEmpresa']);
        $strEstado = 'Eliminado';
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery  = "select distinct IPT.id_punto ID,ipt.fe_creacion fecha
                        from DB_COMERCIAL.INFO_PERSONA IP,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                        DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                        DB_COMERCIAL.INFO_PUNTO IPT,
                        DB_COMERCIAL.vista_estado_cuenta_resumido VTC
                        where IP.ID_PERSONA = IPER.PERSONA_ID
                        and vtc.punto_id = ipt.id_punto
                        and IPER.ID_PERSONA_ROL = IPT.PERSONA_EMPRESA_ROL_ID
                        and ip.identificacion_cliente = :ident
                        and IPT.id_punto != :puntoDiscriminado
                        and IER.id_empresa_rol = IPER.empresa_rol_id
                        and IER.EMPRESA_COD = :empresaMD
                        and vtc.saldo != 0
                        and IPT.estado != :estado
                        order by ipt.fe_creacion ASC";
        


        $objQuery->setParameter("ident", $arrayParametros["identificacion"]);
        $objQuery->setParameter("puntoDiscriminado", $arrayParametros["intIdPunto"]);
        $objQuery->setParameter("empresaMD", $intEmpresaMD);
        $objQuery->setParameter("estado", $strEstado);
        
        
        $objRsm->addScalarResult(strtoupper('ID'), 'id', 'integer');
        $objQuery->setSQL($strQuery);
        return $objQuery->getResult();		
    }

    public function findParametrosbyNombre($arrayParametro)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery  = "select PD.valor1 VALOR 
        from DB_GENERAL.admi_parametro_det PD,
        DB_GENERAL.admi_parametro_cab PC 
        where pc.id_parametro = pd.parametro_id
        and PC.NOMBRE_PARAMETRO = :parametro
        and PC.estado = 'Activo'
        and PD.estado = 'Activo'";
       
        $objQuery->setParameter("parametro", $arrayParametro["parametro"]);                
        $objRsm->addScalarResult(strtoupper('VALOR'), 'valor', 'integer');
        $objQuery->setSQL($strQuery);
        return $objQuery->getResult();		
    }
		
    
        /**
         * Documentación para el método 'getPuntosFacturacionAndFacturasAbiertasByIdPunto'.
         *
         * Método que retorna un array con la información del 'saldoCliente', 'puntosFacturacion', 'login' y 'numFacturasAbiertas' que se muestra en 
         * la barra de sesión del cliente
         *
         * @param int        $idPunto     Id del punto a verificar
         * @param connection $em          Conexión con la base de datos
         * @param int        $codEmpresa  Código de la empresa a verificar
         *
         * @return array $result ['saldoCliente', 'puntosFacturacion', 'login', 'numFacturasAbiertas']
         *
         * @version 1.0 Version Inicial
         * @author Edson Franco <efranco@telconet.ec>
         * @version 1.1 03-09-2016 - Se corrige que el método sólo tome en cuenta las facturas en estado 'Activo' para mostrar la cantidad de
         *                           facturas abiertas en la barra de información del cliente en sesión
         * @author Edgar Holguín <eholguin@telconet.ec>
         * @version 1.2 12-04-2018 - Se agrega envío del id del punto de facturación en el array resultante.
         * 
         * @author Edgar Pin Villavicencio <epin@telconet.ec>
         * @version 1.3 18-11-2019 - Se modifica par que devuelva la fecha más reciente de la deuda, para tm-comercial
         * Costo: 5
         * 
         * @author José Candelario <jcandelario@telconet.ec>
         * @version 1.4 28-06-2020 - Se agrega valores de NDI diferidas a los puntos de facturación.
         */
        public function getPuntosFacturacionAndFacturasAbiertasByIdPunto($idPunto, $em, $codEmpresa)
        {
            $result                      = array();
            $result['saldoCliente']      = 0;
            $result['puntosFacturacion'] = array();
            $arrayCodigosFacturas        = array('FAC', 'FACP');
            $strEstadoActivo             = 'Activo';
            $strValor                    = 'N';
			
            $arrayPadresFacturacion = array();
       
            $arrayFacturas = array();

            $sqlNumFacturas = "SELECT COUNT(idfc.id) ".
                                      "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                      "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                      "WHERE idfc.tipoDocumentoId = atdf.id ".
                                      "AND idfc.puntoId = :intIdPadreFacturacion ".
                                      "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                      "AND idfc.estadoImpresionFact = :strEstadoActivo ";

            $strSqlMaxFacturas = "SELECT MAX(idfc.feCreacion) ".
                                    "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                    "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                    "WHERE idfc.tipoDocumentoId   = atdf.id             ".
                                    "AND idfc.puntoId             = :intIdPadreFacturacion    ".
                                    "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                    "AND idfc.estadoImpresionFact = :strEstadoActivo          ";
       
           
            
			$sqlPadresFacturacion = "select distinct p.id
						 from schemaBundle:InfoServicio s, schemaBundle:InfoPunto p
						  where s.puntoId = $idPunto
						  and p.id = s.puntoFacturacionId
						  and lower(s.estado) != 'eliminado' ";
							  
			$queryPadresFacturacion = $em->createQuery($sqlPadresFacturacion);
			$padresFacturacion = $queryPadresFacturacion->getResult();
			
            if($padresFacturacion)
            {
                foreach($padresFacturacion as $arrayIdPadreFacturacion)
                {
                    $arrayPadreFacturacion = array();
                    $objInfoPunto          = $em->getRepository('schemaBundle:InfoPunto')->find($arrayIdPadreFacturacion['id']);
                    $arraySaldoPunto       = $em->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($arrayIdPadreFacturacion['id']);
                    $arrayTmpSaldoPunto    = ( !empty($arraySaldoPunto) ) ? $arraySaldoPunto[0] : array();
                    $floatSaldoPunto       = ( !empty($arrayTmpSaldoPunto) ) ? ( isset($arrayTmpSaldoPunto['saldo']) ? $arrayTmpSaldoPunto['saldo'] 
                                             : 0 ) : 0;
                    $arrayParametrosNDI    = array('intIdPunto' => $arrayIdPadreFacturacion['id']);
                    $fltTotalDiferido      = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getTotalDiferido($arrayParametrosNDI);
                    $fltDiferidoPagado     = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getDiferidoPagado($arrayParametrosNDI);
        
                    $fltDiferidoPorVencer  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getDiferidoPorVencer($arrayParametrosNDI);
        
                    $fltDiferidoVencido    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getDiferidoVencido($arrayParametrosNDI);
                    $arrayParametrosPto    = array('intPuntoId' => $arrayIdPadreFacturacion['id']);
                    $fltSaldoPorDiferir    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->getSaldoPorVencerNDI($arrayParametrosPto);
                    if ($fltTotalDiferido > 0 || $fltDiferidoPagado > 0 || 
                        $fltDiferidoPorVencer > 0 || $fltDiferidoVencido > 0 || $fltSaldoPorDiferir > 0)
                    {
                        $strValor = 'S';
                    }
                    

                    $queryNumFacturas = $this->_em->createQuery($sqlNumFacturas);
                    $queryNumFacturas->setParameter("arrayCodigosFacturas",  array_values($arrayCodigosFacturas));
                    $queryNumFacturas->setParameter("strEstadoActivo",       $strEstadoActivo);
                    $queryNumFacturas->setParameter("intIdPadreFacturacion", $arrayIdPadreFacturacion['id']);
  
                    $objQueryMaxFacturas = $this->_em->createQuery($strSqlMaxFacturas);
                    $objQueryMaxFacturas->setParameter("arrayCodigosFacturas",  array_values($arrayCodigosFacturas));
                    $objQueryMaxFacturas->setParameter("strEstadoActivo",       $strEstadoActivo);
                    $objQueryMaxFacturas->setParameter("intIdPadreFacturacion", $arrayIdPadreFacturacion['id']);

                    $arrayPadreFacturacion['id']                  = $objInfoPunto ? $objInfoPunto->getId() : 0;
                    $arrayPadreFacturacion['login']               = $objInfoPunto ? $objInfoPunto->getLogin() : '';
                    $arrayPadreFacturacion['saldo']               = (!empty($floatSaldoPunto)) ? round($floatSaldoPunto,2) : 0;
                    $arrayPadreFacturacion['numFacturasAbiertas'] = $queryNumFacturas->getSingleScalarResult();
                    $arrayPadreFacturacion['maxFacturasAbiertas'] = $objQueryMaxFacturas->getSingleScalarResult();

                    $arrayPadreFacturacion['fltTotalDiferido']     = $fltTotalDiferido;
                    $arrayPadreFacturacion['fltDiferidoPagado']    = $fltDiferidoPagado;
                    $arrayPadreFacturacion['fltDiferidoPorVencer'] = $fltDiferidoPorVencer;
                    $arrayPadreFacturacion['fltDiferidoVencido']   = $fltDiferidoVencido;
                    $arrayPadreFacturacion['fltSaldoPorDiferir']   = $fltSaldoPorDiferir;
                    $arrayPadreFacturacion['strValor']             = $strValor;
                    $arrayPadresFacturacion[] = $arrayPadreFacturacion ;
                    $result['saldoCliente'] = $result['saldoCliente'] + $arrayPadreFacturacion['saldo'];

                    $result['fechaCliente'] = $arrayPadreFacturacion['maxFacturasAbiertas'];
                    


                
                }//foreach($padresFacturacion as $arrayIdPadreFacturacion)
            }//($padresFacturacion)
            else
            {
                $arrayPadreFacturacion = array();
			    
                //Caso contrario verificamos con el punto enviado
                $objInfoPunto       = $em->getRepository('schemaBundle:InfoPunto')->find($idPunto);
                $arraySaldoPunto    = $em->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($idPunto);
                $arrayTmpSaldoPunto = ( !empty($arraySaldoPunto) ) ? $arraySaldoPunto[0] : array();
                $floatSaldoPunto    = ( !empty($arrayTmpSaldoPunto) ) ? ( isset($arrayTmpSaldoPunto['saldo']) ? $arrayTmpSaldoPunto['saldo'] 
                                      : 0 ) : 0;
                
                $sqlNumFacturas = "SELECT COUNT(idfc.id)".
                                  "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                  "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                  "WHERE idfc.tipoDocumentoId = atdf.id ".
                                  "AND idfc.puntoId = :intIdPunto ".
                                  "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                  "AND idfc.estadoImpresionFact = :strEstadoActivo ";

                $strSqlMaxFacturas = "SELECT MAX(idfc.feCreacion)".
                                  "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                  "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                  "WHERE idfc.tipoDocumentoId = atdf.id ".
                                  "AND idfc.puntoId = :intIdPunto ".
                                  "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                  "AND idfc.estadoImpresionFact = :strEstadoActivo ";

                

                $queryNumFacturas = $this->_em->createQuery($sqlNumFacturas);
                $queryNumFacturas->setParameter("arrayCodigosFacturas", array_values($arrayCodigosFacturas));
                $queryNumFacturas->setParameter("strEstadoActivo",      $strEstadoActivo);
                $queryNumFacturas->setParameter("intIdPunto",           $idPunto);
				
                $objQueryMaxFacturas = $this->_em->createQuery($strSqlMaxFacturas);
                $objQueryMaxFacturas->setParameter("arrayCodigosFacturas", array_values($arrayCodigosFacturas));
                $objQueryMaxFacturas->setParameter("strEstadoActivo",      $strEstadoActivo);
                $objQueryMaxFacturas->setParameter("intIdPunto",           $idPunto);

                $arrayPadreFacturacion['login']               = $objInfoPunto ? $objInfoPunto->getLogin() : '';
                $arrayPadreFacturacion['saldo']               = (!empty($floatSaldoPunto)) ? round($floatSaldoPunto, 2) : 0;
                $arrayPadreFacturacion['numFacturasAbiertas'] = $queryNumFacturas->getSingleScalarResult();
                $arrayPadreFacturacion['maxFacturasAbiertas'] = $objQueryMaxFacturas->getSingleScalarResult();
                $arrayParametrosNDI    = array('intIdPunto' => $idPunto);
                $fltTotalDiferido      = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getTotalDiferido($arrayParametrosNDI);
                $fltDiferidoPagado     = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getDiferidoPagado($arrayParametrosNDI);

                $fltDiferidoPorVencer  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getDiferidoPorVencer($arrayParametrosNDI);

                $fltDiferidoVencido    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getDiferidoVencido($arrayParametrosNDI);
                $arrayParametrosPto    = array('intPuntoId' => $idPunto);
                $fltSaldoPorDiferir    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getSaldoPorVencerNDI($arrayParametrosPto);
                if ($fltTotalDiferido > 0 || $fltDiferidoPagado > 0 || 
                    $fltDiferidoPorVencer > 0 || $fltDiferidoVencido > 0 || $fltSaldoPorDiferir > 0)
                {
                    $strValor = 'S';
                }
                $arrayPadreFacturacion['fltTotalDiferido']     = $fltTotalDiferido;
                $arrayPadreFacturacion['fltDiferidoPagado']    = $fltDiferidoPagado;
                $arrayPadreFacturacion['fltDiferidoPorVencer'] = $fltDiferidoPorVencer;
                $arrayPadreFacturacion['fltDiferidoVencido']   = $fltDiferidoVencido;
                $arrayPadreFacturacion['fltSaldoPorDiferir']   = $fltSaldoPorDiferir;
                $arrayPadreFacturacion['strValor']             = $strValor;
                $arrayPadresFacturacion[] = $arrayPadreFacturacion ;
                
                $result['saldoCliente'] = $result['saldoCliente'] + $arrayPadreFacturacion['saldo'];
                $result['fechaCliente'] = $arrayPadreFacturacion['maxFacturasAbiertas'];
            }//(!$padresFacturacion)
			
			
			$result['puntosFacturacion'] = $arrayPadresFacturacion;
			$result['saldoCliente'] = round($result['saldoCliente'],2);

            

            
			
			return $result;
				
		}


        /**
         * Documentación para el método 'getPuntosAndFacturasAbiertasByIdentificacion'.
         *
         * Método que retorna un array con la información del 'saldoCliente', 'puntosFacturacion', 'login' y 'numFacturasAbiertas' que se muestra en 
         * la barra de sesión del cliente por identificacion para MEGA DATOS
         *
         * @param int        $idPunto     Id del punto a verificar
         * @param connection $em          Conexión con la base de datos
         * @param int        $codEmpresa  Código de la empresa a verificar
         * @param string        $identificacion  Identificacion del cliente actualmente logeado
         *
         * @return array $result ['saldoCliente', 'puntosFacturacion', 'login', 'numFacturasAbiertas']
         *
         * @version 1.0 Version Inicial
         * @author Luis Ardila <lardila@telconet.ec>
         
         */
        public function getPuntosAndFacturasAbiertasByIdentificacion($arrayParametros)
        {
            $arrayResult                      = array();
            $intRangoLista = 5;
            $arrayResult['saldoCliente']      = 0.00;
            $arrayResult['puntosFacturacion'] = array();
            $arrayCodigosFacturas        = array('FAC', 'FACP');
            $strEstadoActivo             = 'Activo';
            $strValor                    = 'N';
			
            $arrayPadresFacturacion = array();
       
            $arrayFacturas = array();

            $strNumFacturas = "SELECT COUNT(idfc.id) ".
                                      "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                      "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                      "WHERE idfc.tipoDocumentoId = atdf.id ".
                                      "AND idfc.puntoId = :intIdPadreFacturacion ".
                                      "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                      "AND idfc.estadoImpresionFact = :strEstadoActivo ";

            $strSqlMaxFacturas = "SELECT MAX(idfc.feCreacion) ".
                                    "FROM schemaBundle:InfoDocumentoFinancieroCab idfc, ".
                                    "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                                    "WHERE idfc.tipoDocumentoId   = atdf.id             ".
                                    "AND idfc.puntoId             = :intIdPadreFacturacion    ".
                                    "AND atdf.codigoTipoDocumento IN (:arrayCodigosFacturas)  ".
                                    "AND idfc.estadoImpresionFact = :strEstadoActivo          ";
       
            
            
            $arrayParametros["parametro"] = 'RANGO_VISUALIZACION_SALDO';
            $arrayPuntosSaldos = $this->findPuntosIdByIdentificacion($arrayParametros);
            $arrayVisualizacionRango = $this->findParametrosbyNombre($arrayParametros);
			
            
            if ($arrayVisualizacionRango)
            {
                $intRangoLista = $arrayVisualizacionRango[0]['valor'];
                $arrayPuntosSaldos = array_slice($arrayPuntosSaldos,0,$intRangoLista-1);
                
            }
           

         
            $objArrayLogeado = array('id' => $arrayParametros["intIdPunto"]);
			array_unshift($arrayPuntosSaldos,$objArrayLogeado);

            if($arrayPuntosSaldos)
            {
                foreach($arrayPuntosSaldos as $idx => $puntoSaldo ) 
                {
                    $arraySaldoPunto = $arrayParametros["em"]->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($puntoSaldo['id']);
                    $arrayTmpSaldoPunto = (!empty($arraySaldoPunto) ) ? $arraySaldoPunto[0] : array();
                    $floatSaldoPunto = (!empty($arrayTmpSaldoPunto) ) ? (isset($arrayTmpSaldoPunto['saldo']) ? $arrayTmpSaldoPunto['saldo'] : 0 ):0;

                    
                        $arrayPadreFacturacion = array();
                        $objInfoPunto          =  $arrayParametros["em"]->getRepository('schemaBundle:InfoPunto')->find($puntoSaldo['id']);
                        
                        $arrayParametrosNDI    = array('intIdPunto' => $puntoSaldo['id']);
                        $fltTotalDiferido      = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getTotalDiferido($arrayParametrosNDI);
                        $fltDiferidoPagado     = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getDiferidoPagado($arrayParametrosNDI);
            
                        $fltDiferidoPorVencer  = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getDiferidoPorVencer($arrayParametrosNDI);
            
                        $fltDiferidoVencido    = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getDiferidoVencido($arrayParametrosNDI);
                        $arrayParametrosPto    = array('intPuntoId' => $puntoSaldo['id']);
                        $fltSaldoPorDiferir    = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getSaldoPorVencerNDI($arrayParametrosPto);
                        if ($fltTotalDiferido > 0 || $fltDiferidoPagado > 0 || 
                            $fltDiferidoPorVencer > 0 || $fltDiferidoVencido > 0 || $fltSaldoPorDiferir > 0)
                        {
                            $strValor = 'S';
                        }
                        
    
                        $objQueryNumFacturas = $this->_em->createQuery($strNumFacturas);
                        $objQueryNumFacturas->setParameter("arrayCodigosFacturas",  array_values($arrayCodigosFacturas));
                        $objQueryNumFacturas->setParameter("strEstadoActivo",       $strEstadoActivo);
                        $objQueryNumFacturas->setParameter("intIdPadreFacturacion", $puntoSaldo['id']);
      
                        $objQueryMaxFacturas = $this->_em->createQuery($strSqlMaxFacturas);
                        $objQueryMaxFacturas->setParameter("arrayCodigosFacturas",  array_values($arrayCodigosFacturas));
                        $objQueryMaxFacturas->setParameter("strEstadoActivo",       $strEstadoActivo);
                        $objQueryMaxFacturas->setParameter("intIdPadreFacturacion", $puntoSaldo['id']);
    
                        $arrayPadreFacturacion['id']                  = $objInfoPunto ? $objInfoPunto->getId() : 0;
                        $arrayPadreFacturacion['login']               = $objInfoPunto ? $objInfoPunto->getLogin() : '';
                        $arrayPadreFacturacion['saldo']               = (!empty($floatSaldoPunto)) ? round($floatSaldoPunto,2) : 0;
                        $arrayPadreFacturacion['estado']              = $objInfoPunto ? $objInfoPunto->getEstado() : '';
                        $arrayPadreFacturacion['numFacturasAbiertas'] = $objQueryNumFacturas->getSingleScalarResult();
                        $arrayPadreFacturacion['maxFacturasAbiertas'] = $objQueryMaxFacturas->getSingleScalarResult();
    
                        $arrayPadreFacturacion['fltTotalDiferido']     = $fltTotalDiferido;
                        $arrayPadreFacturacion['fltDiferidoPagado']    = $fltDiferidoPagado;
                        $arrayPadreFacturacion['fltDiferidoPorVencer'] = $fltDiferidoPorVencer;
                        $arrayPadreFacturacion['fltDiferidoVencido']   = $fltDiferidoVencido;
                        $arrayPadreFacturacion['fltSaldoPorDiferir']   = $fltSaldoPorDiferir;
                        $arrayPadreFacturacion['strValor']             = $strValor;
                        
                        if ($floatSaldoPunto != 0 || $puntoSaldo['id']  == $arrayParametros["intIdPunto"] )
                        {
                            
                            $arrayPadresFacturacion[] = $arrayPadreFacturacion;   
                            $arrayResult['fechaCliente'] = $arrayPadreFacturacion['maxFacturasAbiertas'];
                        
                        }
                        
                }
            }
            
            $arraySaldo = $arrayParametros["em"]->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
            ->getSaldoTotalByIdenficacion($arrayParametros);

            if (!empty($arraySaldo))
            {
                $arrayResult['saldoCliente'] =  $arraySaldo[0]['total'];
                $arrayResult['totalPuntos'] = $arraySaldo[0]['cuenta'] - count($arrayPadresFacturacion);
            }

           
			$arrayResult['puntosFacturacion'] = $arrayPadresFacturacion;
			$arrayResult['saldoCliente'] = round($arrayResult['saldoCliente'],2);

			
			return $arrayResult;
				
		}

		
		public function getCantidadNC($referencia)
		{
			$query = $this->_em->createQuery("SELECT idfc
					FROM 
							schemaBundle:InfoDocumentoFinancieroCab idfc,
							schemaBundle:AdmiTipoDocumentoFinanciero atdf
					WHERE 
							idfc.tipoDocumentoId=atdf.id AND 
							atdf.codigoTipoDocumento='NC' AND 
							idfc.referenciaDocumentoId=".$referencia." AND 
							idfc.estadoImpresionFact='Activo'");
			
			$total=count($query->getResult());
			
			return $total;
		}
        /**
         * Documentación para el método 'getTieneFacturas'.
         *
         * Método que retorna el número de pagos asociados a la factura enviada como parámetro
         *
         * @param int  $referencia     Id del documento
         *
         * @return int $total          Número de pagos asociados al documento
         *
         * @version 1.0 Version Inicial
         * 
         * @author  Edgar Holguín <eholguín@telconet.ec>
         * @version 1.1 22-06-2017 - Se agrega en la consulta de pagos asociados a una factura los estados 
         *                           que no se deben considerar (Anulado y Eliminado)
         */		
		public function getTieneFacturas($referencia)
		{
			$query = $this->_em->createQuery("SELECT ipc
					FROM 
							schemaBundle:InfoPagoCab ipc,
							schemaBundle:InfoPagoDet ipd
					WHERE 
							ipc.id=ipd.pagoId AND
                            ipc.estadoPago not in (:strEstado) AND
							ipd.referenciaId=".$referencia);
            
            $query->setParameter('strEstado', array('Anulado', 'Eliminado'));
			
			$total=count($query->getResult());
			
			return $total;
		}
	/**Determinae el total del NC aplicadas a las facturas
	* @param string $idFactura   
	* @return resultado del query generado
	* @author gvillalba
	*/	
		public function getTotalNcAplicadas($idFactura)
		{
			$query = $this->_em->createQuery("SELECT sum(idfc.valorTotal) as sumatoria
					FROM 
							schemaBundle:InfoDocumentoFinancieroCab idfc
					WHERE 
							idfc.estadoImpresionFact='Activo'
							and idfc.referenciaDocumentoId=".$idFactura);
			
			$datos = $query->getResult();
			
			return $datos;
		}
			
        /**Determinae si los rangos de las facturas enviados por parametro existen en la base de datos
	* @param string $numero   
	* @param integer $rango1
	* @param integer $rango2
	* @return resultado del query generado
	* @author arsuarez
	*/
       public function existenFacturas($numero , $rango1, $rango2, $tipo, $empresa){
       
	  if($tipo == 'FACT') $in = "('FAC','FACP')"; else $in = "('NC')";
       
	   $rsm = new ResultSetMapping;
	      $rsm->addEntityResult('telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab', 'a');	      
	      $rsm->addFieldResult('a', 'ID_DOCUMENTO', 'id');
	      $rsm->addFieldResult('a', 'PUNTO_ID', 'puntoId');
	      $rsm->addFieldResult('a', 'OFICINA_ID', 'oficinaId');
	      $rsm->addFieldResult('a', 'NUMERO_FACTURA_SRI', 'numeroFacturaSri');
	      $rsm->addFieldResult('a', 'FE_CREACION', 'feCreacion');
	      $rsm->addFieldResult('a', 'SUBTOTAL', 'subtotal');
	      $rsm->addFieldResult('a', 'SUBTOTAL_CON_IMPUESTO', 'subtotalConImpuesto');
	      $rsm->addFieldResult('a', 'VALOR_TOTAL', 'valorTotal');
	      
	      $sql = $this->_em->createNativeQuery("
			    SELECT a.id_documento,a.punto_id,a.oficina_id,a.numero_factura_sri , a.fe_creacion, a.subtotal,
				   a.subtotal_con_impuesto, a.valor_total
				FROM 
				info_documento_financiero_cab a,
				admi_tipo_documento_financiero b,
				db_comercial.info_punto c,
				db_comercial.admi_tipo_negocio d,
				db_comercial.info_oficina_grupo e
				WHERE
				a.tipo_documento_id = b.id_tipo_documento and 
				a.punto_id = c.id_punto and 
				c.tipo_negocio_id = d.id_tipo_negocio and 
				a.oficina_id = e.id_oficina and 
	                        e.empresa_id = $empresa and 
				d.codigo_tipo_negocio not in ('ISP','CHK','PHK') and 				
				b.codigo_tipo_documento in $in AND
            regexp_like(a.numero_factura_sri,'^[[:digit:]][[:digit:]][[:digit:]]-[[:digit:]][[:digit:]][[:digit:]]-[[:digit:]]+$') and                                     
            to_number(substr(a.numero_factura_sri,9)) in ('$rango1','$rango2')
            and a.numero_factura_sri like '".$numero."-%'",$rsm);                                                     
		
	    return $sql->getResult();	
       
       }
    
    
     /**
     * Documentación para el método 'existenNC'.
     *
     * Retorna el listado de NC a procesar
     *
     * @param mixed $empresaCod Empresa a procesar
     *
     * @return array $datos Listado de documentos a procesar
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 24-10-2014
     * @since 1.0 
     */
    public function existenNC($empresaCod)
    {

        $query = $this->_em->createQuery("select count(a) as num 
				FROM 
						schemaBundle:InfoDocumentoFinancieroCab a,
						schemaBundle:InfoOficinaGrupo b,
                        schemaBundle:AdmiTipoDocumentoFinanciero c
				WHERE 
                        a.tipoDocumentoId=c.id and 
                        c.codigoTipoDocumento=:srtCodigo and 
                        c.estado=:srtEstado and 
						a.estadoImpresionFact=:srtEstadoFactura and
						a.oficinaId = b.id and
						b.empresaId =:strEmpresa ");

        $query->setParameter('srtCodigo', "NC");
        $query->setParameter('srtEstado', "Activo");
        $query->setParameter('srtEstadoFactura', "Activo");
        $query->setParameter('strEmpresa', $empresaCod);

        $datos = $query->getResult();

        return $datos;
    }

    /**
     * Documentación para el método 'findDevolucionPorCriterios'.
     *
     * Retorna todos los documentos de tipo DEV para el punto cliente en session
     *
     * @param mixed $param1 El primer parámetro.
     * @param mixed $param2 El segundo parámetro.
     *
     * @return array Listado devoluciones encontradas
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     * 
     * Se elimina la oficina dentros de la presentacion de la informacion para que los usuarios puedan visualizar
     * las notas de debito creadas.
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 03-08-2016
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.2 02-01-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     */
    public function findDevolucionPorCriterios($arrayParametros)
    {
        $strEstado                 = $arrayParametros["estado"];
        $intIdEmpresa              = $arrayParametros["idEmpresa"];
        $strFeDesde                = $arrayParametros["feDesde"];
        $strFeHasta                = $arrayParametros["feHasta"];
        $intPunto                  = $arrayParametros["punto"];
        $intStart                  = $arrayParametros["start"];
        $intLimit                  = $arrayParametros["limit"];
        $intCodigoTipoDocumento    = $arrayParametros["codigoTipoDocumento"];
        $query                  = $this->_em->createQuery();
        $strTipo                = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa      = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol  = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo        = 'Activo';
        $strDescripcion         = 'ASISTENTE_POR_CARGO';
        $strFrom                ='';
        $strWhere               ='';
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            $strFrom =" ,schemaBundle:InfoPunto ipuVend ";
            if( $strTipo == 'SUBGERENTE' )
            {
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
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
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
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
                $query->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strWhere = " AND ipuVend.id = idfc.puntoId
                                AND ipuVend.usrVendedor IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = :intIdPersonEmpresaRol
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = :strEstadoActivo
                                    AND ipvend.estado      = :strEstadoActivo)
                              ";
            }
            $query->setParameter('strEstadoActivo', $strEstadoActivo);
            $query->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }
        $squery="SELECT idfc";
        
        $cuerpo="
            FROM 
                schemaBundle:InfoDocumentoFinancieroCab idfc,
                schemaBundle:AdmitipoDocumentoFinanciero atd,
                schemaBundle:InfoOficinaGrupo iog ".$strFrom."
            WHERE
                idfc.tipoDocumentoId=atd.id 
                AND atd.codigoTipoDocumento=:codigoTipoDocumento 
                ".$strWhere."
                AND iog.id=idfc.oficinaId "; 

        $squery.=$cuerpo;

        if($strEstado!="")
        {
            $squery.=" AND idfc.estadoImpresionFact!=:estado ";
            $query->setParameter('estado', $strEstado);
        }
        
        if($intIdEmpresa!="")
        {
            $squery.=" AND iog.empresaId=:empresa";
            $query->setParameter('empresa', $intIdEmpresa);
        }    
        
        if($strFeDesde!="")
        {
            $squery.=" AND idfc.feCreacion>=:feDesde";
            $query->setParameter('feDesde', $strFeDesde);
        }
        
        if($strFeHasta!="")
        {
            $squery.=" AND idfc.feCreacion<=:feHasta";
            $query->setParameter('feHasta', $strFeHasta);
        }
            
        if($intPunto)
        {
            $squery.=" AND idfc.puntoId=:punto";
            $query->setParameter('punto', $intPunto);
        }
            
        $query->setParameter('codigoTipoDocumento', $intCodigoTipoDocumento);
        $squery.=" order by idfc.feCreacion desc ";
        
        $query->setDQL($squery);
        $intTotal = count($query->getResult());
        $objDatos = $query->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        $arrayResultado['registros'] = $objDatos;
        $arrayResultado['total']     = $intTotal;
        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'findErroresEstadoDeCuenta'.
     *
     * Permite listar los errores presentes en el estado de cuenta
     * - Listado de pagos asociados a facturas anuladas
     * 
     * @return listado_errores Listado de errores.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 09-10-2014
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 08-08-2017 - Se cambia de nombre al array que retora los errores presentes en el estado de cuenta y
     *                           se elimina seteo a vacio en caso de que no devuelva valor el procedimiento.
     */
    public function findErroresEstadoDeCuenta($db, $user_financiero, $passwd_financiero, $idptocliente)
    {
        $arrayListadoPagos =  array();
        $oci_con = oci_connect(
            $user_financiero, $passwd_financiero, $db
        );

        if($oci_con)
        {
            $curs_r = oci_new_cursor($oci_con);
            $s = oci_parse($oci_con, "
                BEGIN 
                    DOCUMENTOS_ERROR.PAGOS_FACTURA_ANULADA(:id_punto,:listado ); 
                END;
            ");
            oci_bind_by_name($s, ":id_punto", $idptocliente);
            oci_bind_by_name($s, ":listado", $curs_r, -1, OCI_B_CURSOR);
            oci_execute($s);
            oci_execute($curs_r);
            oci_commit($oci_con);
            
            while(($row = oci_fetch_array($curs_r, OCI_ASSOC + OCI_RETURN_NULLS)) != false)
            {
                $arrayListadoPagos[] = array(
                    'comentario_error' => $row['COMENTARIO_ERROR'],
                    'login' => $row['LOGIN'],
                    'origen_documento' => $row['ORIGEN_DOCUMENTO'],
                );
            }

            oci_close($oci_con);

        }
        
        return $arrayListadoPagos;
    }
    
    /**
    * Documentación para el método 'getSaldosPorFactura'.
    * Obtiene las facturas con saldo
    *
    * @param  String $strNumeroFactura  Recibe numero de la factura
    * @param  String $strPrefijoEmpresa Recibe el prefijo de la empresa
    * @return String $strResultado      Retorna la factura y el saldo
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 22-10-2014
    */
    public function getSaldosPorFactura($strNumeroFactura, $strPrefijoEmpresa)
    {
        $strResultado = str_pad($strResultado, 1000, " ");
        $sql = "BEGIN SALDOS_POR_FACTURAS(:strNumeroFactura, :strPrefijoEmpresa, :strResultado); END;";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('strNumeroFactura' , $strNumeroFactura);
        $stmt->bindParam('strPrefijoEmpresa', $strPrefijoEmpresa);
        $stmt->bindParam('strResultado'     , $strResultado);
        $stmt->execute();
        return $strResultado;
    }

    /**
     * Documentación para el método 'getDocumentosRelacionados'.
     * Obtiene los documentos relacionados de la factura
     *
     * @param  array  $arrayParametros Recibe los parametros para la conexion con la base
     * @return cursor $cursorResult    false cuando existe algun error, por verdadero retorna un cursor con la informacion
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-12-2014
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 15-09-2016 - Se modifica el método para usar la función 'P_DOCUMENTOS_RELACIONADOS' que está dentro del package 
     *                          'DB_FINANCIERO.FNCK_CONSULTS'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 21-10-2016 - Se modifica el método para actualizarlo a que reciba el parámetro de 'Pv_FeConsultaHasta' que permitirá consultar
     *                           los documentos relacionados a una fecha de consulta enviada por el usuario
     */
    public function getDocumentosRelacionados($arrayParametros)
    {
        try
        {
            $strFeConsultaHasta = ( (isset($arrayParametros['strFeConsultaHasta']) && !empty($arrayParametros['strFeConsultaHasta']))
                                    ? $arrayParametros['strFeConsultaHasta'] : null );
                
            $objOciConn     = oci_connect($arrayParametros['user_financiero'], 
                                          $arrayParametros['passwd_financiero'], 
                                          $arrayParametros['database_dsn']);
            $cursorResult   = oci_new_cursor($objOciConn);
            $strSQL         = "BEGIN DB_FINANCIERO.FNCK_CONSULTS.P_DOCUMENTOS_RELACIONADOS(:id_documento, :strFeConsultaHasta, :refCursor ); END;";
            $stmt           = oci_parse($objOciConn, $strSQL);

            oci_bind_by_name($stmt, ":id_documento",       $arrayParametros['intIdDocumento']);
            oci_bind_by_name($stmt, ":strFeConsultaHasta", $strFeConsultaHasta);
            oci_bind_by_name($stmt, ":refCursor",          $cursorResult, -1, OCI_B_CURSOR);
            oci_execute($stmt);
            oci_execute($cursorResult);
            oci_commit($objOciConn);
        }catch(\Exception $ex){
            $cursorResult = false;
        }
        return $cursorResult;
    }//getDocumentosRelacionados
    
    /**
    * Documentación para el método 'getNotaDebitoAntNoAplicados'.
    * Obtiene las notas de debito a anticipos no aplicados
    *
    * @param  array  $arrayParametros Recibe los parametros para la conexion con la base
    * @return cursor $cursorResult    false cuando existe algun error, por verdadero retorna un cursor con la informacion
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 17-12-2014
    */
    public function getNotaDebitoAntNoAplicados($arrayParametros)
    {
        try{
            $objOciConn     = oci_connect($arrayParametros['user_financiero'], 
                                          $arrayParametros['passwd_financiero'], 
                                          $arrayParametros['database_dsn']);
            $cursorResult   = oci_new_cursor($objOciConn);
            $strSQL         = "DECLARE pv_mensajeerror VARCHAR2(32767); begin P_DOC_ANT_NO_APLICADOS(:id_documento, :refCursor ); end;";
            $stmt           = oci_parse($objOciConn, $strSQL);
            oci_bind_by_name($stmt, ":id_documento", $arrayParametros['intIdDocumento']);
            oci_bind_by_name($stmt, ":refCursor", $cursorResult, -1, OCI_B_CURSOR);
            oci_execute($stmt);
            oci_execute($cursorResult);
            oci_commit($objOciConn);
        }catch(\Exception $ex){
            $cursorResult = false;
        }
        return $cursorResult;
    }//getNotaDebitoAntNoAplicados
    
    /**
    * Documentación para el método 'getAnticipoGenerados'.
    * Obtiene los anticipos generados
    *
    * @param  array  $arrayParametros Recibe los parametros para la conexion con la base
    * @return cursor $cursorResult    false cuando existe algun error, por verdadero retorna un cursor con la informacion
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 17-12-2014
    */
    public function getAnticipoGenerados($arrayParametros)
    {
        try{
            $objOciConn     = oci_connect($arrayParametros['user_financiero'], 
                                          $arrayParametros['passwd_financiero'], 
                                          $arrayParametros['database_dsn']);
            $cursorResult   = oci_new_cursor($objOciConn);
            $strSQL         = "DECLARE begin ANTICIPOS_GENERADOS(:id_pago, :anticipos ); end;";
            $stmt           = oci_parse($objOciConn, $strSQL);
            oci_bind_by_name($stmt, ":id_pago", $arrayParametros['intIdPago']);
            oci_bind_by_name($stmt, ":anticipos", $cursorResult, -1, OCI_B_CURSOR);
            oci_execute($stmt);
            oci_execute($cursorResult);
            oci_commit($objOciConn);
        }catch(\Exception $ex){
            $cursorResult = false;
        }
        return $cursorResult;
    }//getAnticipoGenerados
    
    /**
     * Documentación para el método 'getSaldosByFactura'.
     * Obtiene el saldo por factura, se debe enviar como parametro el ID de la factura o IdReferencia
     *
     * @param  String $arrayParametros   Recibe como parametro el ID de la factura
     * @return String $arrayResultado    Retorna el saldo o un mensaje de error en caso de existir uno
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-02-2015
     */
    public function getSaldosXFactura($arrayParametros)
    {
        try
        {

            $arrayResultado['strMessageError']  = str_pad($arrayResultado['strMessageError'], 4000, " ");
            $arrayResultado['intSaldo']         = str_pad($arrayResultado['intSaldo'], 100, " ");
            $strSql = "BEGIN FNCK_CONSULTS.P_SALDO_X_FACTURA(:intIdDocumento, :intReferenciaId, :intSaldo, :strMessageError); END;";
            $stmt = $this->_em->getConnection()->prepare($strSql);
            $stmt->bindParam('intIdDocumento', $arrayParametros['intIdDocumento']);
            $stmt->bindParam('intReferenciaId', $arrayParametros['intReferenciaId']);
            $stmt->bindParam('intSaldo', $arrayResultado['intSaldo']);
            $stmt->bindParam('strMessageError', $arrayResultado['strMessageError']);
            $stmt->execute();
        }
        catch(\Exception $ex)
        {
            $arrayResultado['strMessageError'] = 'Error en getSaldosXFactura ' . $ex->getMessage();
        }
        return $arrayResultado;
    }//getSaldosXFactura

    /**
     * Documentación para el método 'getValorTotalNcByFactura'.
     * Obtiene el saldo por factura solo considerando las notas de creditos
     *
     * @param  String $arrayParametros   intIdDocumento => Recibe le id documento, arrayEstadoTipoDocumentos => Recibe el array de estados de tipo de
     *                                   documentos, arrayCodigoTipoDocumento >= Recibe el array de codigos de tipo de documentos,
                                         arrayEstadoNC => Recibe los estado de la nota de credito a considerar
     * @return String $arrayResultado    Retorna el saldo o un mensaje de error en caso de existir uno
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-02-2015
     */
    function getValorTotalNcByFactura($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objQb = $this->_em->createQueryBuilder();
            $objQb->select('idfc.numeroFacturaSri, COALESCE(idfc.valorTotal, 0) valorTotalFac, sum(COALESCE(idfc_nc.valorTotal, 0)) valorTotalNc')
                ->from('schemaBundle:InfoDocumentoFinancieroCab', 'idfc')
                ->leftJoin('schemaBundle:InfoDocumentoFinancieroCab', 'idfc_nc', 'WITH', 
                           "idfc_nc.referenciaDocumentoId = idfc.id AND idfc_nc.estadoImpresionFact IN (:arrayEstadoNC)")
                ->leftJoin('schemaBundle:AdmiTipoDocumentoFinanciero', 'atdf', 'WITH', 
                           'atdf.id = idfc_nc.tipoDocumentoId '
                         . 'AND atdf.estado IN (:arrayEstadoTipoDocumentos) '
                         . 'AND atdf.codigoTipoDocumento IN (:arrayCodigoTipoDocumento)')
                ->where('idfc.id = :intIdDocumento')
                ->setParameter(':intIdDocumento', $arrayParametros['intIdDocumento'])
                ->setParameter(':arrayEstadoTipoDocumentos', $arrayParametros['arrayEstadoTipoDocumentos'])
                ->setParameter(':arrayCodigoTipoDocumento', $arrayParametros['arrayCodigoTipoDocumento'])
                ->setParameter(':arrayEstadoNC', $arrayParametros['arrayEstadoNC'])
                ->groupBy('idfc.valorTotal, idfc.numeroFacturaSri');
            $objQuery = $objQb->getQuery();
            $arrayResultado['arrayResultado'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $arrayResultado['strMensajeError'] = 'Existion un error en getValorTotalNcByFactura ' . $ex->getMessage();
        }
        return $arrayResultado;
    }//getValorTotalNcByFactura

    /**
     * EL metodo getEstadosByTipoDocumentos obtiene los estados de los documentos
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 25-11-2014
     * @param type $arrayParametros     Recibe los parametros para la consulta, array de tipo de documentos y rango de fechas
     * @return array                    Retorna un array con la lista de estados de documentos
     */
    public function getEstadosByTipoDocumentos($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objQb = $this->_em->createQueryBuilder();
            $objQb->select('idfc.estadoImpresionFact estadoDocumento')
                ->from('schemaBundle:InfoDocumentoFinancieroCab', 'idfc')
                ->from('schemaBundle:AdmiTipoDocumentoFinanciero', 'atdf')
                ->from('schemaBundle:InfoOficinaGrupo', 'iog')
                ->where('idfc.tipoDocumentoId = atdf.id')
                ->andWhere('atdf.estado                 IN (:arrayEstadoTipoDocumentos)')
                ->andWhere('atdf.codigoTipoDocumento    IN (:arrayCodigoTipoDocumento)')
                ->andWhere('idfc.oficinaId              = iog.id')
                ->andWhere('iog.estado                  IN (:arrayEstadoOficina)')
                ->andWhere('iog.empresaId               =  :intIdEmpresa')
                ->setParameter(':arrayEstadoTipoDocumentos' , $arrayParametros['arrayEstadoTipoDocumentos'])
                ->setParameter(':arrayCodigoTipoDocumento'  , $arrayParametros['arrayCodigoTipoDocumento'])
                ->setParameter(':arrayEstadoOficina'        , $arrayParametros['arrayEstadoOficina'])
                ->setParameter(':intIdEmpresa'              , $arrayParametros['intIdEmpresa']);
            if(!empty($arrayParametros['arrayEstados']))
            {
                $objQb->andWhere('idfc.estadoImpresionFact IN (:arrayEstados)')
                      ->setParameter(':arrayEstados', $arrayParametros['arrayEstados']);
            }
            if(!empty($arrayParametros['strFechaInicio']))
            {
                $objQb->andWhere('idfc.feCreacion >= :strFechaInicio')
                      ->setParameter(':strFechaInicio', $arrayParametros['strFechaInicio']);
            }
            if(!empty($arrayParametros['strFechaFin']))
            {
                $objQb->andWhere('idfc.feCreacion <= :strFechaFin')
                      ->setParameter(':strFechaFin', $arrayParametros['strFechaFin']);
            }
            $objQb->groupBy('idfc.estadoImpresionFact')
                  ->orderBy('idfc.estadoImpresionFact');
            $objQuery                         = $objQb->getQuery();
            $arrayResultado['arrayResultado'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $arrayResultado['strMensajeError'] = 'Existion un error en getEstadosByTipoDocumentos ' . $ex->getMessage();
        }
        return $arrayResultado;
    }

    /**
     * EL metodo getDocumentosFinancieroNativeQuery genera el SQL para obtener los documentos financieros
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @param type $arrayParametrosIn   Recibe los parametros para la consulta, array de tipo de documentos y rango de fechas
     * @return array                    Retorna el sql formado para obtener los documentos
     */
    public function getDocumentosFinancieroNativeQuery($arrayParametrosIn)
    {
        $arrayResult['strMensajeError'] = '';
        try
        {
            $rsmBuilder = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsmBuilder);
            //Case que define el select del query, count o con los campos
            switch($arrayParametrosIn['strTipoQuery'])
            {
                case 'count' : $strDocumentos = 'SELECT COUNT(*) AS TOTAL';
                    $rsmBuilder->addScalarResult('TOTAL', 'total', 'integer');
                    break;
                case 'query' : $strDocumentos = "SELECT 
                                        IDFC.NUMERO_FACTURA_SRI, 
                                        TO_CHAR(IDFC.FE_CREACION, 'DD-MM-YYYY') FE_CREACION, 
                                        IDFC.FE_EMISION, 
                                        ATDF.NOMBRE_TIPO_DOCUMENTO, 
                                        IDFC.ESTADO_IMPRESION_FACT, 
                                        IDFC.FE_AUTORIZACION, 
                                        IP.LOGIN, 
                                        IDFC.OFICINA_ID, 
                                        IDFC.PUNTO_ID, 
                                        FNCK_CONSULTS.F_GET_NOMBRE_COMPLETO_CLIENTE(IDFC.PUNTO_ID) NOMBRE_CLIENTE, 
                                        DECODE(UPPER(IDFC.ES_AUTOMATICA), 'S', 'Si', 'No') ES_AUTOMATICA, 
                                        DECODE(UPPER(IDFC.ES_ELECTRONICA), 'S', 'Si', 'No') ES_ELECTRONICA,
                                        ROUND(IDFC.VALOR_TOTAL, 2) VALOR_TOTAL, 
                                        FNCK_CONSULTS.F_GET_SALDO_DISPONIBLE_BY_NC(IDFC.ID_DOCUMENTO) SALDO_DISPONIBLE, 
                                        IDFC.ID_DOCUMENTO ";
                    $rsmBuilder->addScalarResult('NUMERO_FACTURA_SRI', 'numeroFacturaSri', 'string');
                    $rsmBuilder->addScalarResult('FE_CREACION', 'feCreacion', 'string');
                    $rsmBuilder->addScalarResult('FE_EMISION', 'feEmision', 'string');
                    $rsmBuilder->addScalarResult('NOMBRE_TIPO_DOCUMENTO', 'nombreTipoDocumento', 'string');
                    $rsmBuilder->addScalarResult('ESTADO_IMPRESION_FACT', 'estadoImpresionFact', 'string');
                    $rsmBuilder->addScalarResult('FE_AUTORIZACION', 'feAutorizacion', 'string');
                    $rsmBuilder->addScalarResult('LOGIN', 'login', 'string');
                    $rsmBuilder->addScalarResult('OFICINA_ID', 'oficinaId', 'string');
                    $rsmBuilder->addScalarResult('PUNTO_ID', 'puntoId', 'string');
                    $rsmBuilder->addScalarResult('NOMBRE_CLIENTE', 'nombreCliente', 'string');
                    $rsmBuilder->addScalarResult('ES_AUTOMATICA', 'esAutomatica', 'string');
                    $rsmBuilder->addScalarResult('ES_ELECTRONICA', 'esElectronica', 'string');
                    $rsmBuilder->addScalarResult('VALOR_TOTAL', 'valorTotal', 'string');
                    $rsmBuilder->addScalarResult('SALDO_DISPONIBLE', 'saldoDisponible', 'string');
                    $rsmBuilder->addScalarResult('ID_DOCUMENTO', 'id', 'string');
                    break;
            } //arrayParametrosIn['strTipoQuery']
            $strDocumentos .= "  FROM 
                                        INFO_DOCUMENTO_FINANCIERO_CAB IDFC, 
                                        INFO_PUNTO IP, 
                                        ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF, 
                                        INFO_OFICINA_GRUPO IOG 
                                      WHERE 
                                        ATDF.ID_TIPO_DOCUMENTO        = IDFC.TIPO_DOCUMENTO_ID 
                                      AND IP.ID_PUNTO                 = IDFC.PUNTO_ID 
                                      AND ATDF.ESTADO                 = 'Activo' 
                                      AND IOG.ID_OFICINA              = IDFC.OFICINA_ID 
                                      AND IOG.ESTADO                  = 'Activo' 
                                      AND IOG.EMPRESA_ID              = :intIdEmpresa ";
            $ntvQuery->setParameter('intIdEmpresa', $arrayParametrosIn['intIdEmpresa']);
            /* Permite filtrar que el documento financiero,
             * no tenga otro relacionado, en algun estado especifico
             */
            if(!empty($arrayParametrosIn['notExistsDocumento']))
            {
                $strDocumentos .= "AND NOT EXISTS (SELECT 
                                                        NULL 
                                                      FROM 
                                                        INFO_DOCUMENTO_FINANCIERO_CAB IDFC_NC, 
                                                        ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF_NC 
                                                      WHERE 
                                                        IDFC_NC.TIPO_DOCUMENTO_ID        = ATDF_NC.ID_TIPO_DOCUMENTO 
                                                      AND ATDF_NC.CODIGO_TIPO_DOCUMENTO IN (:arrayTipoDocNotExists) 
                                                      AND ATDF_NC.ESTADO                 = 'Activo' 
                                                      AND IDFC_NC.ESTADO_IMPRESION_FACT IN (:arrayEstadoDocNotExists) 
                                                      AND IDFC.ID_DOCUMENTO              =  IDFC_NC.REFERENCIA_DOCUMENTO_ID) ";
                $ntvQuery->setParameter('arrayTipoDocNotExists',    $arrayParametrosIn['arrayTipoDocNotExists']);
                $ntvQuery->setParameter('arrayEstadoDocNotExists',  $arrayParametrosIn['arrayEstadoDocNotExists']);
            }
            /**Permite filtrar que el documento tenga saldo disponible
             * en relacion a otro documento y en cierto estado
             */
            if(!empty($arrayParametrosIn['strSaldoDisponible']))
            {
                $strDocumentos .= "AND (
                                          NVL((ROUND(IDFC.VALOR_TOTAL, 2) - NVL(
                                          (
                                            SELECT
                                              ROUND(SUM(NVL(IDFC_SD.VALOR_TOTAL, 0)), 2)
                                            FROM
                                              INFO_DOCUMENTO_FINANCIERO_CAB IDFC_SD,
                                              ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF_SD
                                            WHERE
                                              IDFC_SD.TIPO_DOCUMENTO_ID         = ATDF_SD.ID_TIPO_DOCUMENTO
                                            AND ATDF_SD.ESTADO                  = 'Activo'
                                            AND ATDF_SD.CODIGO_TIPO_DOCUMENTO  IN (:arrayTipoDocumentoSaldoDis)
                                            AND IDFC_SD.ESTADO_IMPRESION_FACT  IN (:arrayEstadoDocSalDisp)
                                            AND IDFC_SD.REFERENCIA_DOCUMENTO_ID = IDFC.ID_DOCUMENTO
                                          )
                                          , 0)), 0)
                                        ) > 0 ";
                $ntvQuery->setParameter('arrayTipoDocumentoSaldoDis',   $arrayParametrosIn['arrayTipoDocumentoSaldoDis']);
                $ntvQuery->setParameter('arrayEstadoDocSalDisp',        $arrayParametrosIn['arrayEstadoDocSalDisp']);
            }
            //Permite la filtrar por el rango de factura >= al numero enviado como parametro
            if(!empty($arrayParametrosIn['strRangoFacturaDesde']))
            {
                $strDocumentos .= "AND SUBSTR( IDFC.NUMERO_FACTURA_SRI , 9, INSTR(IDFC.NUMERO_FACTURA_SRI, '-') +9) >= TO_NUMBER(:strRangoFacturaDesde) ";
                $ntvQuery->setParameter('strRangoFacturaDesde', $arrayParametrosIn['strRangoFacturaDesde']);
            }
            //Permite la filtrar por el rango de factura <= al numero enviado como parametro
            if(!empty($arrayParametrosIn['strRangoFacturaHasta']))
            {
                $strDocumentos .= "AND SUBSTR( IDFC.NUMERO_FACTURA_SRI , 9, INSTR(IDFC.NUMERO_FACTURA_SRI, '-') +9) <= TO_NUMBER(:strRangoFacturaHasta) ";
                $ntvQuery->setParameter('strRangoFacturaHasta', $arrayParametrosIn['strRangoFacturaHasta']);
            }
            //Permite la filtrar por tipo de documento
            if(!empty($arrayParametrosIn['arrayTipoDocumentoFinanciero']))
            {
                $strDocumentos .= "AND ATDF.CODIGO_TIPO_DOCUMENTO IN (:arrayTipoDocumentoFinanciero) ";
                $ntvQuery->setParameter('arrayTipoDocumentoFinanciero', $arrayParametrosIn['arrayTipoDocumentoFinanciero']);
            }
            //Permite la filtrar por el estado del documento
            if(!empty($arrayParametrosIn['arrayEstadoImpresionDocumento']))
            {
                $strDocumentos .= "AND IDFC.ESTADO_IMPRESION_FACT IN (:arrayEstadoImpresionDocumento) ";
                $ntvQuery->setParameter('arrayEstadoImpresionDocumento', $arrayParametrosIn['arrayEstadoImpresionDocumento']);
            }
            //Permite filtrar por una fecha de inicio
            if(!empty($arrayParametrosIn['dateFechaDesde']))
            {
                $strDocumentos .= "AND IDFC.FE_EMISION >= TO_DATE(:dateFechaDesde , 'DD-MM-YYYY') ";
                $ntvQuery->setParameter('dateFechaDesde', $arrayParametrosIn['dateFechaDesde']);
            }
            //Permite filtrar por una fecha de fin
            if(!empty($arrayParametrosIn['dateFechaHasta']))
            {
                $strDocumentos .= "AND IDFC.FE_EMISION <= TO_DATE(:dateFechaHasta , 'DD-MM-YYYY') ";
                $ntvQuery->setParameter('dateFechaHasta', $arrayParametrosIn['dateFechaHasta']);
            }
            //Permite filtrar por un plan o producto buscando en el detalle del documento
            if(!empty($arrayParametrosIn['intIdPlan']) || !empty($arrayParametrosIn['intIdProducto']))
            {
                //Verifica si no tiene plan y lo setea vacio
                if(empty($arrayParametrosIn['intIdPlan']))
                {
                    $arrayParametrosIn['intIdPlan'] = '';
                }
                //Verifica si no tiene prodcuto y lo setea vacio
                if(empty($arrayParametrosIn['intIdProducto']))
                {
                    $arrayParametrosIn['intIdProducto'] = '';
                }
                $strDocumentos .= "AND EXISTS
                                        (
                                          SELECT
                                            NULL 
                                          FROM 
                                            INFO_DOCUMENTO_FINANCIERO_DET IDFD 
                                          WHERE 
                                            IDFC.ID_DOCUMENTO = IDFD.DOCUMENTO_ID 
                                          AND (IDFD.PLAN_ID  = NVL(:intIdPlan, -1) OR IDFD.PRODUCTO_ID = NVL(:intIdProducto, -1))
                                        ) ";
                $ntvQuery->setParameter('intIdPlan',        $arrayParametrosIn['intIdPlan']);
                $ntvQuery->setParameter('intIdProducto',    $arrayParametrosIn['intIdProducto']);
            }
            //Permite filtrar por login
            if(!empty($arrayParametrosIn['strLogin']))
            {
                $strDocumentos .= "AND LOWER(IP.LOGIN) LIKE LOWER(:strLogin) ";
                $ntvQuery->setParameter('strLogin', '%' . $arrayParametrosIn['strLogin'] . '%');
            }
            //Permite filtrar por forma de pago o tipo de cuenta
            if(!empty($arrayParametrosIn['intIdFormaPago']) || !empty($arrayParametrosIn['intIdTipoCuenta']))
            {
                $strDocumentos .= "AND EXISTS ( 
                                    SELECT 
                                        NULL 
                                      FROM 
                                        ADMI_TIPO_CUENTA ATC, 
                                        ADMI_BANCO_TIPO_CUENTA ABTC, 
                                        INFO_CONTRATO_FORMA_PAGO ICFP, 
                                        INFO_CONTRATO IC, 
                                        INFO_PERSONA_EMPRESA_ROL IPER, 
                                        INFO_PUNTO IPC, 
                                        INFO_EMPRESA_ROL IER, 
                                        INFO_PUNTO_DATO_ADICIONAL IPDA 
                                      WHERE 
                                        ATC.ID_TIPO_CUENTA          = ABTC.TIPO_CUENTA_ID 
                                      AND ABTC.ID_BANCO_TIPO_CUENTA = ICFP.BANCO_TIPO_CUENTA_ID 
                                      AND ICFP.CONTRATO_ID          = IC.ID_CONTRATO 
                                      AND IC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL 
                                      AND IPER.ID_PERSONA_ROL       = IPC.PERSONA_EMPRESA_ROL_ID 
                                      AND IPER.EMPRESA_ROL_ID       = IER.ID_EMPRESA_ROL 
                                      AND IPDA.PUNTO_ID             = IPC.ID_PUNTO 
                                      AND IPDA.ES_PADRE_FACTURACION = 'S' 
                                      AND TRIM(IPER.ESTADO)        IN ('Activo', 'Cancelado', 'Modificado') 
                                      AND ICFP.ESTADO NOT          IN ('Inactivo', 'Pendiente') 
                                      AND ATC.ESTADO                = 'Activo' 
                                      AND ABTC.ESTADO              IN ('Activo', 'Activo-debitos') 
                                      AND IC.ESTADO                IN ('Activo', 'Cancelado') 
                                      AND IPC.ID_PUNTO              = IP.ID_PUNTO 
                                      AND IER.EMPRESA_COD           = :intIdEmpresa ";
                $ntvQuery->setParameter('intIdEmpresa', $arrayParametrosIn['intIdEmpresa']);
                //Permite filtrar por forma de pago
                if(!empty($arrayParametrosIn['intIdFormaPago']))
                {
                    $strDocumentos .= "AND IC.FORMA_PAGO_ID = :intIdFormaPago ";
                    $ntvQuery->setParameter('intIdFormaPago', $arrayParametrosIn['intIdFormaPago']);
                }
                //Permite filtrar por tipo de cuenta
                if(!empty($arrayParametrosIn['intIdTipoCuenta']))
                {
                    $strDocumentos .= "AND ATC.ID_TIPO_CUENTA = :intIdTipoCuenta ";
                    $ntvQuery->setParameter('intIdTipoCuenta', $arrayParametrosIn['intIdTipoCuenta']);
                }
                $strDocumentos .= " ) ";
            } //if !empty($arrayParametrosIn['intIdFormaPago']) || !empty($arrayParametrosIn['intIdTipoCuenta'])
            //permite validar por tipo de solicitud o estado de solicitud
            if(!empty($arrayParametrosIn['intIdTipoSolicitud']) || !empty($arrayParametrosIn['strIdEstTipoSolicitud']))
            {
                $strDocumentos .= "AND EXISTS (
                                        SELECT
                                            NULL
                                          FROM
                                            DB_COMERCIAL.ADMI_TIPO_SOLICITUD ATS,
                                            DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDS,
                                            DB_COMERCIAL.INFO_SERVICIO ISR,
                                            INFO_PUNTO IPSS
                                          WHERE
                                            IDS.TIPO_SOLICITUD_ID   = ATS.ID_TIPO_SOLICITUD
                                          AND ISR.ID_SERVICIO       = IDS.SERVICIO_ID
                                          AND IPSS.ID_PUNTO          = ISR.PUNTO_FACTURACION_ID
                                          AND ATS.ESTADO            = 'Activo'
                                          AND IPSS.ID_PUNTO          = IP.ID_PUNTO ";
                //Permite la validacion por el tipo de solicitud
                if(!empty($arrayParametrosIn['intIdTipoSolicitud']))
                {
                    $strDocumentos .= "AND ATS.ID_TIPO_SOLICITUD = :intIdTipoSolicitud ";
                    $ntvQuery->setParameter('intIdTipoSolicitud', $arrayParametrosIn['intIdTipoSolicitud']);
                }
                //Permite la validacion por el estado del tipo de solicitud
                if(!empty($arrayParametrosIn['strIdEstTipoSolicitud']))
                {
                    $strDocumentos .= "AND IDS.ESTADO            = :strIdEstTipoSolicitud ";
                    $ntvQuery->setParameter('strIdEstTipoSolicitud', $arrayParametrosIn['strIdEstTipoSolicitud']);
                }
                $strDocumentos .= " ) ";
            } //if(!empty($arrayParametrosIn['intIdTipoSolicitud']) || !empty($arrayParametrosIn['strIdEstTipoSolicitud']))
            //Permite validar por Elemento
            if(!empty($arrayParametrosIn['intIdElemento']))
            {
                //Obtiene los id servicios
                $arrayIdServicio = $this->getServByElementoInterface($arrayParametrosIn);
                //Verifica que no haya existido un error para formar el query
                if(empty($arrayIdServicio['strMensajeError']))
                {
                    $strDocumentos .= "AND EXISTS ( SELECT
                                                        NULL
                                                      FROM
                                                        DB_COMERCIAL.INFO_SERVICIO ISR,
                                                        DB_COMERCIAL.INFO_SERVICIO_TECNICO IST,
                                                        INFO_PUNTO IPS
                                                      WHERE
                                                        ISR.ID_SERVICIO    = IST.SERVICIO_ID
                                                      AND IPS.ID_PUNTO      = ISR.PUNTO_ID
                                                      AND IPS.ID_PUNTO     = IDFC.PUNTO_ID
                                                      AND ISR.ID_SERVICIO IN (:arrayIdServicios)
                                                      GROUP BY
                                                        IPS.ID_PUNTO )";
                    $ntvQuery->setParameter('arrayIdServicios', $arrayIdServicio);
                }
                else
                {
                    $arrayResult['strMensajeError'] = $arrayIdServicio['strMensajeError'];
                }
            } //if(!empty($arrayParametrosIn['intIdElemento']))
            $strDocumentos          .= "ORDER BY IDFC.ID_DOCUMENTO DESC";
            $arrayResult['objQuery'] = $ntvQuery->setSQL($strDocumentos);
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en getDocumentosFinancieroNativeQuery ' . $ex->getMessage();
        }
        return $arrayResult;
    }//getDocumentosFinancieroNativeQuery

    /**
     * getDocumentosFinanciero, obtiene el resultado del query formado para la obtencion de los documentos financieros
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-01-2015
     * @param  array $arrayParametrosIn Obtiene los filtros para la creacion del query
     * @return array $arrayResult       Retorna un array con los datos y el count de regitros   
     */
    public function getDocumentosFinanciero($arrayParametrosIn)
    {
        try
        {
            $arrayParametrosIn['strTipoQuery']  = 'count';
            //Obtiene el count del query formado para obtener los documentos financieros
            $objDocumentoCountFinanNQuery       = $this->getDocumentosFinancieroNativeQuery($arrayParametrosIn);
            //Verifica que no haya existido un error y obtiene el resultado
            if(empty($objDocumentoCountFinanNQuery['strMensajeError']))
            {
                $intTotalRegistros                  = $objDocumentoCountFinanNQuery['objQuery']->getSingleScalarResult();
                $arrayParametrosIn['strTipoQuery']  = 'query';
                //Obtiene la data del query formado para obtener los documentos financieros
                $objDocumentoFinanNQuery            = $this->getDocumentosFinancieroNativeQuery($arrayParametrosIn);
                //Verifica que no haya existido un error y obtiene el resultado
                if(empty($objDocumentoFinanNQuery['strMensajeError']))
                {
                    //Pregunta si el limite es > 0
                    if($arrayParametrosIn['intLimit'] > 0)
                    {
                        $objDocumentoFinanNQuery['objQuery']->setSQL('SELECT a.*, rownum AS intDoctrineRowNum FROM (' 
                                                                    . $objDocumentoFinanNQuery['objQuery']->getSQL() 
                                                                    . ') a WHERE rownum <= :intDoctrineLimit');
                        $objDocumentoFinanNQuery['objQuery']->setParameter('intDoctrineLimit', 
                                                                           $arrayParametrosIn['intLimit'] + $arrayParametrosIn['intStart']);
                        //Pregunta si el intStart > 0
                        if($arrayParametrosIn['intStart'] > 0)
                        {
                            $objDocumentoFinanNQuery['objQuery']->setSQL('SELECT * FROM (' 
                                                                        . $objDocumentoFinanNQuery['objQuery']->getSQL() 
                                                                        . ') WHERE intDoctrineRowNum >= :intDoctrineStart');
                            $objDocumentoFinanNQuery['objQuery']->setParameter('intDoctrineStart', $arrayParametrosIn['intStart'] + 1);
                        }
                    }
                    $arrayResult['arrayRegistros'] = $objDocumentoFinanNQuery['objQuery']->getResult();
                    //Si no hay error porcede a crear el array de datos
                    if(empty($arrayResult['strMensajeError']))
                    {
                        foreach($arrayResult['arrayRegistros'] as $arrayInfoDocumentoFinancieroCab):
                            $arrayStoreDocumentos[] = array('intIdDocumento'    => $arrayInfoDocumentoFinancieroCab['id'],
                                                            'strTipoDocumento'  => $arrayInfoDocumentoFinancieroCab['nombreTipoDocumento'],
                                                            'strNumFactura'     => $arrayInfoDocumentoFinancieroCab['numeroFacturaSri'],
                                                            'strLogin'          => $arrayInfoDocumentoFinancieroCab['login'],
                                                            'strCliente'        => $arrayInfoDocumentoFinancieroCab['nombreCliente'],
                                                            'strEsAutomatica'   => $arrayInfoDocumentoFinancieroCab['esAutomatica'],
                                                            'strElectronica'    => $arrayInfoDocumentoFinancieroCab['esElectronica'],
                                                            'strEstado'         => $arrayInfoDocumentoFinancieroCab['estadoImpresionFact'],
                                                            'strFeEmision'      => $arrayInfoDocumentoFinancieroCab['feEmision'],
                                                            'strFeCreacion'     => $arrayInfoDocumentoFinancieroCab['feCreacion'],
                                                            'intSaldoDisponible'=> $arrayInfoDocumentoFinancieroCab['saldoDisponible'],
                                                            'intTotal'          => $arrayInfoDocumentoFinancieroCab['valorTotal']);
                        endforeach;
                        $arrayResult['arrayStoreDocumentos']    = $arrayStoreDocumentos;
                        $arrayResult['intTotalRegistros']       = $intTotalRegistros;
                    }
                }
                else //Setea el error en una variable
                {
                    $arrayResult['strMensajeError'] = $objDocumentoCountFinanNQuery['strMensajeError'];
                }
            }
            else //Setea el error en una variable
            {
                $arrayResult['strMensajeError'] = $objDocumentoCountFinanNQuery['strMensajeError'];
            }//if(empty($objDocumentoCountFinanNQuery['strMensajeError']))
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en getDocumentosFinanciero ' . $ex->getMessage();
        }
        return $arrayResult;
    } //getDocumentosFinanciero

    /**
     * getServByElementoInterface, obtiene los servicios por elemento
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-01-2015
     * @param  array $arrayParametrosIn Recibe el id elemento y el id interface
     * @return array $arrayIdServicios  Retorna un array con los id servicios
     */
    public function getServByElementoInterface($arrayParametrosIn)
    {
        $arrayIdServicios['strMensajeError'] = '';
        try
        {
            $strIdServicios = str_pad($strIdServicios, 2000, " ");
            $sql = "BEGIN :arrayIdServicios := DB_COMERCIAL.TECNK_SERVICIOS.FNC_GET_SERV_ELE_PTO(:intIdElemento, :intIdInterface); END;";
            $stmt = $arrayParametrosIn['emComercial']->getConnection()->prepare($sql);
            $stmt->bindParam('intIdElemento',    $arrayParametrosIn['intIdElemento']);
            $stmt->bindParam('intIdInterface',   $arrayParametrosIn['intIdInterface']);
            $stmt->bindParam('arrayIdServicios', $strIdServicios);
            $stmt->execute();
            $arrayIdServicios = explode(',', $strIdServicios);
        }
        catch(\Exception $ex)
        {
            $arrayIdServicios['strMensajeError'] = 'Existio un error en getServByElementoInterface ' . $ex->getMessage();
        }
        return $arrayIdServicios;
    } //getServByElementoInterface

    /**
     * creaNotaCreditoMasiva, Hace el llamado al procedimiento para hacer las notas de credito masivas, enviando los
     * Id Facturas y el tipo de nota de credito a crear
     * @param  array  $arrayParametrosIn    Recibe los id facturas, y el tipo de nota de credito con sus respectivos valores de creacion
     * @return array  $arrayParametrosOut   Retorna un mensaje de error o de exito
     */
    public function creaNotaCreditoMasiva($arrayParametrosIn)
    {
        try
        {
            $arrayParametrosOut['strMsnResultado']  = str_pad($arrayParametrosOut['strMsnResultado'], 1000, " ");
            $arrayParametrosOut['strMsnError']      = str_pad($arrayParametrosOut['strMsnError'], 1000, " ");
            $strSQLCreaNotaCreditoMasiva = "BEGIN FNCK_CONSULTS.P_CREA_NOTA_CREDITO_MASIVA(:clbDocumentos, "
                                                                                        . ":strDelimitador, "
                                                                                        . ":strObservacion, "
                                                                                        . ":intIdMotivo, "
                                                                                        . ":strUsrCreacion, "
                                                                                        . ":strEstadoNc, "
                                                                                        . ":strTipoNotaCredito, "
                                                                                        . ":intIdOficina, "
                                                                                        . ":intIdEmpresa, "
                                                                                        . ":intPorcentaje, "
                                                                                        . ":strFechaInicio, "
                                                                                        . ":strFechaFin, "
                                                                                        . ":strMsnResultado, "
                                                                                        . ":strMsnError); END;";
            $stmt = $this->_em->getConnection()->prepare($strSQLCreaNotaCreditoMasiva);
            $stmt->bindParam('clbDocumentos',       $arrayParametrosIn['clbDocumentos']);
            $stmt->bindParam('strDelimitador',      $arrayParametrosIn['strDelimitador']);
            $stmt->bindParam('strObservacion',      $arrayParametrosIn['strObservacion']);
            $stmt->bindParam('intIdMotivo',         $arrayParametrosIn['intIdMotivo']);
            $stmt->bindParam('strUsrCreacion',      $arrayParametrosIn['strUsrCreacion']);
            $stmt->bindParam('strEstadoNc',         $arrayParametrosIn['strEstadoNc']);
            $stmt->bindParam('strTipoNotaCredito',  $arrayParametrosIn['strTipoNotaCredito']);
            $stmt->bindParam('intIdOficina',        $arrayParametrosIn['intIdOficina']);
            $stmt->bindParam('intIdEmpresa',        $arrayParametrosIn['intIdEmpresa']);
            $stmt->bindParam('intPorcentaje',       $arrayParametrosIn['intPorcentaje']);
            $stmt->bindParam('strFechaInicio',      $arrayParametrosIn['strFechaInicio']);
            $stmt->bindParam('strFechaFin',         $arrayParametrosIn['strFechaFin']);
            $stmt->bindParam('strMsnResultado',     $arrayParametrosOut['strMsnResultado']);
            $stmt->bindParam('strMsnError',         $arrayParametrosOut['strMsnError']);
            $stmt->execute();
        } 
        catch (\Exception $ex) 
        {
            $arrayParametrosOut['strMsnError'] = 'Existio un error en: '.$ex->getMessage();
        }
        return $arrayParametrosOut;
    }//creaNotaCreditoMasiva
    
    /**
     * Documentación para el método 'findEstadoDeCuentaPorFechas'.
     *
     * Me devuelve los documentos tales como FAC, FACP, ND, NDI; los cuales generaran un arbol interno de opciones
     *
     * @param mixed $idOficina Oficina en session.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     * @param mixed $puntos Pto o listado de ptos clientes.
     *
     * @return resultado Listado de documentos y total de documentos.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 15-05-2014
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 25-05-2016 - Se modifica el query para enviar todos los puntos pertenecientes a un cliente
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>            
     * @version 1.2 23-09-2016 - Se presenta informacion de campo "REF_ANTICIPO_ID", Id de anticipo que Origino un Anticipo por cruce
     *
     */
    public function findEstadoDeCuentaPorFechas($idOficina,$fechaDesde,$fechaHasta, $intIdEmpresa, $intIdCliente)
    {
        if($intIdCliente!="")
        {    
            
            $query = $this->_em->createQuery();
            
            $dql_cc="SELECT count(idfc.id) ";
            
            $dql="SELECT idfc.id,
                    idfc.numeroFacturaSri,
                    idfc.tipoDocumentoId,
                    idfc.valorTotal,
                    idfc.feCreacion,
                    idfc.puntoId,
                    idfc.oficinaId,
                    idfc.referencia,
                    idfc.codigoFormaPago,
                    idfc.numeroReferencia,
                    idfc.numeroCuentaBanco,
                    idfc.referenciaId,
                    atdf.codigoTipoDocumento,
                    atdf.movimiento,
                    idfc.estadoImpresionFact,
                    idfc.refAnticipoId ";
            
            
            $cuerpo="
                    FROM schemaBundle:EstadoCuentaCliente idfc,
                    schemaBundle:AdmiTipoDocumentoFinanciero atdf
                    WHERE 
                    idfc.tipoDocumentoId=atdf.id
                    and idfc.puntoId in (
                                            SELECT p
                                            FROM schemaBundle:InfoPunto p,
                                                 schemaBundle:InfoPersonaEmpresaRol iper,
                                                 schemaBundle:InfoEmpresaRol er
                                            WHERE er.id=iper.empresaRolId
                                              AND p.personaEmpresaRolId=iper.id
                                              AND er.empresaCod= :idEmpresa 
                                              AND iper.personaId = :idcliente
                                        )
                    and atdf.estado= :estado
                    ";
                    
            $dql_cc.=$cuerpo;
            $dql.=$cuerpo;
            
            if($fechaDesde!="")
            {
                $dql.=" and idfc.feCreacion >= :fe_desde";
                $dql_cc.=" and idfc.feCreacion >= :fe_desde";
                $query->setParameter('fe_desde',date('Y/m/d', strtotime($fechaDesde)));
            }    
                
            if($fechaHasta!="")
            {
                $dql.=" and idfc.feCreacion <= :fe_hasta";
                $dql_cc.=" and idfc.feCreacion <= :fe_hasta";
                $query->setParameter('fe_hasta',date('Y/m/d', strtotime($fechaHasta)));
            }
            
            $dql.=" ORDER BY
                    idfc.feCreacion ";
                
            $query->setParameter('estado'   ,'Activo');
            $query->setParameter('idEmpresa', $intIdEmpresa);
            $query->setParameter('idcliente', $intIdCliente);
            
            $query->setDQL($dql);
            $datos= $query->getResult();
            
            if($datos)
            {
                $query->setDQL($dql_cc);
                $total= $query->getSingleScalarResult();
            }
            else
                $total=0;
            
            $resultado['registros']=$datos;
            $resultado['total']=$total;
            
        }
        else 
        { 
            $resultado= '{"registros":"[]","total":0}';
        }
        
        return $resultado;
    }
    
    /**
     * Documentación para el método 'findSumatoriaPorFechas'.
     *
     * Me devuelve la sumatoria de los documentos tales como FAC, FACP, ND, NDI
     *
     * @param mixed $idOficina Oficina en session.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $puntos Pto o listado de ptos clientes.
     *
     * @return resultado Listado de documentos y total de documentos.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 25-09-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 25-05-2016 - Se modifica el query para enviar todos los puntos pertenecientes a un cliente
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 22-09-2016 
     * Se modifica query para verificar si un Anticipo por cruce se origino de otro Anticipo
     * y si se encuentra Cerrado no sumarizara al Saldo Total
     * 
     */
    public function findSumatoriaPorFechas($idOficina, $fechaDesde, $intIdEmpresa, $intIdCliente)
    {
        if($puntos != "")
        {

            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $dql = "SELECT
                    atdf.movimiento,
                    idfc.estado_Impresion_Fact,
                    SUM(ROUND(idfc.VALOR_TOTAL,2)) as  valorTotal
                    ";

            $cuerpo = "
                    FROM ESTADO_CUENTA_CLIENTE idfc,
                    ADMI_TIPO_DOCUMENTO_FINANCIERO atdf
                    WHERE 
                    idfc.TIPO_DOCUMENTO_ID=atdf.ID_TIPO_DOCUMENTO
                    and idfc.PUNTO_ID IN (
                                            SELECT p.ID_PUNTO
                                            FROM INFO_PUNTO p,
                                                 INFO_PERSONA_EMPRESA_ROL iper,
                                                 INFO_EMPRESA_ROL er
                                            WHERE er.ID_EMPRESA_ROL = iper.EMPRESA_ROL_ID
                                              AND p.PERSONA_EMPRESA_ROL_ID = iper.ID_PERSONA_ROL
                                              AND er.EMPRESA_COD = :idEmpresa 
                                              AND iper.PERSONA_ID = :idcliente
                                          )
                     AND NOT EXISTS( SELECT anto.ID_PAGO 
                                     FROM INFO_PAGO_CAB anto, INFO_PAGO_CAB antcc, INFO_PAGO_DET antcd
                                     WHERE 
                                       antcd.ID_PAGO_DET = idfc.ID_DOCUMENTO
                                       AND antcd.PAGO_ID = antcc.ID_PAGO
                                       AND antcc.ANTICIPO_ID = anto.ID_PAGO 
                                       AND anto.ESTADO_PAGO= :estadoPago
                                    )
                    and atdf.ESTADO= :estado
                    ";

            $dql.=$cuerpo;

            if($fechaDesde != "")
            {
                $dql.=" and idfc.FE_CREACION < :fe_desde";
                $query->setParameter('fe_desde', date('Y/m/d', strtotime($fechaDesde)));
            }

            $query->setParameter('estado', 'Activo');            
            $query->setParameter('idEmpresa', $intIdEmpresa);
            $query->setParameter('idcliente', $intIdCliente);
            $query->setParameter('estadoPago', 'Cerrado');

            $dql.=" GROUP BY 
                    atdf.movimiento,
                    idfc.estado_Impresion_Fact ";

            $rsm->addScalarResult('MOVIMIENTO', 'movimiento', 'string');
            $rsm->addScalarResult('ESTADOIMPRESIONFACT', 'estado_Impresion_Fact', 'string');
            $rsm->addScalarResult('VALORTOTAL', 'valorTotal', 'float');
            $query->setSQL($dql);

            $resultado = $query->getScalarResult();
            return $resultado;
        }
        else
        {
            $resultado = '{"registros":"[]"}';
        }

        return $resultado;
    }
    
    /**
    * Permite generar el Json de las facturas en estado pendiente
    *
    * @param array $arrayParametros['intIdOficina'        Id de la oficina a consultar
    *                               'strfechaDesde'       Fecha de inicio
    *                               'strfechaHasta'       Fecha de fin
    *                               'intPtoCliente'       Id del punto cliente
    *                               'intEmpresaId'        Id de empresa en sesión  $intIdOficina
    *                               'intLimit'            Rango inicial de consulta
    *                               'intStart'            Rango final de consulta
    *                               'strTipoDoc'          Tipo de Documento 
    *                               'strUsrCreacion'      Usuario de creación del documento  
    *                               'objContainer'        Objeto contenedor]
    *  
    * @author Gina Villalba <gvillalba@telconet.ec>
    * @version 1.0 24-12-2015
    * 
    * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
    * @version 1.1 16-09-2016 Se obtiene el nombre del Vendedor y la Descripción de la Factura.
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.2 14-09-2017 - Se agrega envío de parámetros mediante un arreglo, adicional se agrega envío de parámetro usrCreacion. 
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.3 09-02-2021  - Se agrega parametro url para poder clonar una prefactura
    * 
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.4 22-04-2022  - Se modifica para poder mostrar si se necesita mostrar el boton de clonacion en las diferentes empresas.
    */
    public function getJsonFacturasPendientes($arrayParametros)
    {
        $arrayEncontrados       = array();
        $strCliente             ="";
        
        $arrayResultado         = $this->findListadoFacturasPendientes($arrayParametros);
        
        $arrayFacturas          = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        
        foreach($arrayFacturas as $objFactura):
            $strCliente    = "";
            if($objFactura["razonSocial"] != "")
            {
                $strCliente    = $objFactura["razonSocial"];
            }
            else
            {
                if($objFactura["nombres"]!= "")
                {
                    $strCliente = $objFactura["nombres"];
                }

                if($objFactura["apellidos"]!= "")
                {
                    $strCliente .=" " . $objFactura["apellidos"];
                }
            }

            //Bloque para generar link para clonar prefacturas
            $strLinkClone = null;
            if($objFactura["codigoTipoDocumento"] == "FAC" && $arrayParametros["strPintarBoton"]=="S")
            {
                $strLinkClone = $arrayParametros['objContainer']->get('router')->generate('infodocumentofinancierocab_clonar', 
                                                                                        array('intId' => $objFactura["id"]));
            }
            else if($objFactura["codigoTipoDocumento"] == "FACP" && $arrayParametros["strPintarBoton"]=="S")
            {
                $strLinkClone = $arrayParametros['objContainer']->get('router')->generate('facturasproporcionales_clonar', 
                                                                                        array('intId' => $objFactura["id"]));
            }

            $arrayEncontrados[] = array(
                'id'                    => $objFactura["id"],
                'codigoTipoDocumento'   => $objFactura["codigoTipoDocumento"],
                'documento'             => $objFactura["id"],
                'feCreacion'            => $objFactura["feCreacion"],
                'oficina'               => $objFactura["nombreOficina"],
                'subtotal'              => $objFactura["subtotal"],
                'impuestos'             => $objFactura["subtotalConImpuesto"],
                'descuento'             => $objFactura["subtotalDescuento"],
                'total'                 => $objFactura["valorTotal"],
                'punto'                 => $objFactura["login"],
                'cliente'               => $strCliente,
                'vendedor'              => $objFactura["vendedor"],
                'observacion'           => $objFactura["observacion"],
                'usrCreacion'           => $objFactura["usrCreacion"],
                'strLinkShow'           => $arrayParametros['objContainer']->get('router')
                                                                           ->generate('infodocumentofinancierocab_show', 
                                                                                       array('id' => $objFactura["id"])),
                'strLinkClone'           => $strLinkClone,
            
                
            );
        endforeach;

        if(empty($arrayEncontrados))
        {
            $arrayEncontrados[] = array(
                'id'                    => "",
                'codigoTipoDocumento'   => "",
                'documento'             => "",
                'feCreacion'            => "",
                'oficina'               => "",
                'subtotal'              => "",
                'impuestos'             => "",
                'descuento'             => "",
                'total'                 => "",
                'punto'                 => "",
                'cliente'               => "",
                'strLinkShow'           => "",
            );
        }

        $arrayResultadoTotales  = $this->findTotalesFacturasPendientes($arrayParametros);

        
        $arrayFacturasTotales   = $arrayResultadoTotales['registros'];

        foreach($arrayFacturasTotales as $objFactura):
            $arrayEncontradosTotales[] = array(
                'subtotal'              => $objFactura["subtotal"],
                'subtotalConImpuesto'   => $objFactura["subtotalConImpuesto"],
                'subtotalDescuento'     => $objFactura["subtotalDescuento"],
                'valorTotal'            => $objFactura["valorTotal"],
            );
        endforeach;

        if(empty($arrayEncontradosTotales))
        {
            $arrayEncontradosTotales[] = array(
                'subtotal'              => "",
                'subtotalConImpuesto'   => "",
                'subtotalDescuento'     => "",
                'valorTotal'            => "",
            );
        }
        
        $arrayRespuesta = array('total' => $intTotal, 'documentos' => $arrayEncontrados, 'totalizados' => $arrayEncontradosTotales);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }

    /**
     * Documentación para el método 'getResultadoValidacionDocumento'.
     *
     * Me devuelve un numero mayo a cero si el documento pertence al login a verificar
     *
     * @param mixed $intIdFactura Documento a verificar.
     * @param mixed $intIdPunto   Login en sesion a verificar.
     * @param mixed $strCodigoTipoDocumento Tipos de documentos a verificar.
     *
     * @return $intTotal Documento asociado al punto
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 22-02-2016
     */
    public function getResultadoValidacionDocumento($intIdFactura,$intIdPunto,$strCodigoTipoDocumento)
    {
        $intTotal = 0;

        try
        {
            $query = $this->_em->createQuery();

            $dql_cc="SELECT count(idfc.id) ";
            $cuerpo="
                    FROM schemaBundle:InfoDocumentoFinancieroCab idfc,
                    schemaBundle:AdmiTipoDocumentoFinanciero atdf
                    WHERE 
                    idfc.tipoDocumentoId=atdf.id
                    and idfc.puntoId= :intIdPunto
                    and idfc.id= :intIdFactura
                    and atdf.codigoTipoDocumento in (:strCodigoTipoDocumento)
                    ";
            
            $dql_cc.=$cuerpo;

            $query->setParameter('intIdPunto',$intIdPunto);
            $query->setParameter('intIdFactura',$intIdFactura);
            $query->setParameter('strCodigoTipoDocumento',$strCodigoTipoDocumento);
            
            $query->setDQL($dql_cc);
            $intTotal= $query->getSingleScalarResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $intTotal;
    }
    
    /**
     * Documentación para contabilizarDocumentosNDI
     * 
     * Función para contabilizar las NDI realizadas en el TELCOS
     * 
     * Se agrega el parametro de fechaProceso para permitir realizar re-procesos a fecha
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 03-08-2016
     * @since 1.0
     * 
     * Se recibe parámetro $objParametros para poder reutilizar la función insertError() en el repositorio.
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.2 07-11-2019
     * @since 1.1
     */
    public function contabilizarDocumentosNDI($arrayContabilidad, $objParametros)
    {
        $serviceUtil = $objParametros['serviceUtil'];

        $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDocumentoFinancieroCabRepository/contabilizarDocumentosNDI '.
                        '- FNKG_CONTABILIZAR_NDI.P_CONTABILIZAR con los sgtes parametros... '.
                        'Codigo de empresa: ' . $arrayContabilidad['empresaCod'] . 
                        ', prefijo: '. $arrayContabilidad['prefijo'] . 
                        ', codigoTipoDocumento: ' . $arrayContabilidad['codigoTipoDocumento'] . 
                        ', tipoProceso: ' . $arrayContabilidad['tipoProceso'] . 
                        ', idDocumento: ' . $arrayContabilidad['idDocumento'] . 
                        ', fechaProceso: ' . $arrayContabilidad['fechaProceso'], 
                        'telcos', 
                        '127.0.0.1' );  

        //Proceso para crear las NDI
        if($arrayContabilidad["idDocumento"] != null)
        {
            $sql = "BEGIN
                        FNKG_CONTABILIZAR_NDI.P_CONTABILIZAR
                        (
                            :empresaCod,
                            :prefijo,
                            :codigoTipoDocumento,
                            :tipoProceso,
                            :idDocumento,
                            :fechaProceso
                        );
                    END;";
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('empresaCod'           , $arrayContabilidad["empresaCod"]);
            $stmt->bindParam('prefijo'              , $arrayContabilidad["prefijo"]);
            $stmt->bindParam('codigoTipoDocumento'  , $arrayContabilidad["codigoTipoDocumento"]);
            $stmt->bindParam('tipoProceso'          , $arrayContabilidad["tipoProceso"]);
            $stmt->bindParam('idDocumento'          , $arrayContabilidad["idDocumento"]);
            $stmt->bindParam('fechaProceso'         , $arrayContabilidad["fechaProceso"]);
            $stmt->execute();

            $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDocumentoFinancieroCabRepository/contabilizarDocumentosNDI '.
                        '- DESPUES DE EJECUTAR: FNKG_CONTABILIZAR_NDI.P_CONTABILIZAR con los sgtes parametros... '.
                        'Codigo de empresa: ' . $arrayContabilidad['empresaCod'] . 
                        ', prefijo: '. $arrayContabilidad['prefijo'] . 
                        ', codigoTipoDocumento: ' . $arrayContabilidad['codigoTipoDocumento'] . 
                        ', tipoProceso: ' . $arrayContabilidad['tipoProceso'] . 
                        ', idDocumento: ' . $arrayContabilidad['idDocumento'] . 
                        ', fechaProceso: ' . $arrayContabilidad['fechaProceso'], 
                        'telcos', 
                        '127.0.0.1' );  
        }
    }
    
    
    /**
     * Documentación para getFVarcharClean
     * 
     * Función que limpia los caracteres especiales de la descripción de los servicios en las facturas 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-06-2016
     */
    public function getFVarcharClean($strDescripcionServicio)
    {
        try
        {
            $strDescripcionServicioCleaned = "";
            $strDescripcionServicioCleaned = str_pad($strDescripcionServicioCleaned, 2000, " ");
            $sql = "BEGIN :strDescripcionServicioCleaned := DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_VARCHAR_CLEAN(:strDescripcionServicio); END;";
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('strDescripcionServicio',        $strDescripcionServicio);
            $stmt->bindParam('strDescripcionServicioCleaned', $strDescripcionServicioCleaned);
            $stmt->execute();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            
            $strDescripcionServicioCleaned = "";
        }
        
        return $strDescripcionServicioCleaned;
    }
    
    /**
     * Documentación para el método 'getValorImpuesto'.
     *
     * Retorna el valor total calculado correspondiente al impuesto que se consulta
     *
     * @param mixed $intIdDocumento  documento a verificar
     * @param mixed $srtTipoImpuesto impuesto a verificar
     *
     * @return float Correspondiente al total del impuesto calculado
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 20-07-2015
     */
    public function getValorImpuesto($intIdDocumento,$srtTipoImpuesto)
    {
        $query = $this->_em->createQuery();

        $dql="SELECT sum(idfi.valorImpuesto) as totalImpuesto ";
        $cuerpo="
                FROM 
                    schemaBundle:InfoDocumentoFinancieroCab idfc, 
                    schemaBundle:InfoDocumentoFinancieroDet idfd, 
                    schemaBundle:InfoDocumentoFinancieroImp idfi,
                    schemaBundle:AdmiImpuesto ai
                WHERE
                    idfc.id= :intIdDocumento
                    and ai.tipoImpuesto= :srtTipoImpuesto
                    and idfd.documentoId=idfc.id 
                    and idfi.detalleDocId=idfd.id 
                    and ai.id=idfi.impuestoId
                ";

        $dql.=$cuerpo;

        $query->setParameter('intIdDocumento',$intIdDocumento);
        $query->setParameter('srtTipoImpuesto',$srtTipoImpuesto);

        $query->setDQL($dql);
        $floatTotalImpuesto= $query->getResult();
        return $floatTotalImpuesto;    
    }
    
    /**
     * Documentación para el método 'getHistorialDocumento'.
     *
     * Retorna la observacion ligada al historial del documento
     *
     * @param mixed $intIdDocumento documento a verificar
     *
     * @return string de observacion del historial
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 20-07-2015
     */
    public function getHistorialDocumento($intIdDocumento)
    {
        $query = $this->_em->createQuery();

        $dql="SELECT idh.observacion as informacion";
        $cuerpo="
                FROM 
                    schemaBundle:InfoDocumentoFinancieroCab idfc, 
                    schemaBundle:InfoDocumentoHistorial idh
                WHERE
                    idh.documentoId = idfc.id
                    and idfc.id = :intIdDocumento
                    and idfc.esAutomatica = :strEsAutomatica
                ORDER BY idh.feCreacion";

        $dql.=$cuerpo;

        $query->setParameter('intIdDocumento',$intIdDocumento);
        $query->setParameter('strEsAutomatica',"N");
        $query->setMaxResults(1);
        $query->setDQL($dql);
        $strObservacion= $query->getResult();
        return $strObservacion;    
    }
    
    /**
     * Documentación para el método 'getInformacionCaracteristica'.
     *
     * Retorna el valor de la caracteristica asociada al documento
     *
     * @param mixed $intIdDocumento     documento a verificar
     * @param mixed $strCaracteristica  caracteristica a consultar
     *
     * @return string de valor de la caracteristica
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 20-07-2015
     */
    public function getInformacionCaracteristica($intIdDocumento,$strCaracteristica)
    {
        $query = $this->_em->createQuery();

        $dql="SELECT idc.valor as informacion";
        $cuerpo="
                FROM 
                    schemaBundle:InfoDocumentoFinancieroCab idfc, 
                    schemaBundle:InfoDocumentoCaracteristica idc,
                    schemaBundle:AdmiCaracteristica ac
                WHERE
                    idc.documentoId = idfc.id
                    and idfc.id = :intIdDocumento
                    and ac.id=idc.caracteristicaId
                    and ac.estado= :strEstado
                    and ac.descripcionCaracteristica= :strCaracteristica";

        $dql.=$cuerpo;

        $query->setParameter('intIdDocumento',$intIdDocumento);
        $query->setParameter('strEstado',"Activo");
        $query->setParameter('strCaracteristica',$strCaracteristica);
        $query->setDQL($dql);
        $strInformacionCaracteristica= $query->getResult();
        return $strInformacionCaracteristica;    
    }
    
    /**
     * Documentación para el método 'getMotivoDocumento'.
     *
     * Retorna los motivos ligados a un documento
     *
     * @param mixed $intIdDocumento documento a verificar
     *
     * @return string de motivo relacionado
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 20-07-2015
     */
    public function getMotivoDocumento($intIdDocumento)
    {
        $query = $this->_em->createQuery();

        $dql="SELECT am.nombreMotivo";
        $cuerpo="
                FROM 
                    schemaBundle:InfoDocumentoFinancieroCab idfc, 
                    schemaBundle:InfoDocumentoFinancieroDet idfd,
                    schemaBundle:AdmiMotivo am
                WHERE
                    idfd.documentoId = idfc.id
                    and idfc.id = :intIdDocumento
                    and am.id=idfd.motivoId";

        $dql.=$cuerpo;

        $query->setParameter('intIdDocumento',$intIdDocumento);
        $query->setDQL($dql);
        $query->setMaxResults(1);
        $strMotivo= $query->getResult();
        return $strMotivo;    
    }
    
    /**
     * Documentación para el método 'ejecutarEnvioReporteFacturacion'.
     *
     * Ejecuta la generación y envío de reporte de facturación según los parámetros indicados.
     *
     * @param mixed $arrayParametros[
     *                               'usrSesion'                    => usuario en sesion
     *                               'prefijoEmpresa'               => prefijo de la empresa en sesion
     *                               'emailUsrSesion'               => email usuario en sesion
     *                               'intEmpresaId'                 => id empresa en sesion
     *                               'fin_tipoDocumento'            => codigo de tipo de documento
     *                               'doc_numDocumento'             => numero de documento
     *                               'doc_creador'                  => usuario en sesion
     *                               'doc_estado'                   => estado del documento
     *                               'doc_monto'                    => valor o monto del documento
     *                               'doc_montoFiltro'              => operador para filtrar por monto
     *                               'finDocFechaAutorizacionDesde' => rango inicial para fecha de autorizacion
     *                               'finDocFechaAutorizacionHasta' => rango final para fecha de autorizacion
     *                               'doc_fechaCreacionDesde'       => rango inicial para fecha de creacion
     *                               'doc_fechaCreacionHasta'       => rango final para fecha de creacion
     *                               'doc_fechaEmisionDesde'        => rango inicial para fecha de emision
     *                               'doc_fechaEmisionHasta'        => rango final para fecha de emision
     *                               'start'                        => rango de inicio de consulta
     *                               'limit'                        => rango maximo de consulta
     *                               ]
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 26-08-2016
     */
    public function ejecutarEnvioReporteFacturacion($arrayParametros)
    {

        $strTipoDocumento           = null;
        $strNumeroDocumento         = null;
        $strUsrCreacion             = null;
        $strEstadoDocumento         = null;
        $strMonto                   = null;
        $strFiltroMonto             = null;
        $dateFechaCreacionDesde     = null;
        $dateFechaCreacionHasta     = null;
        $dateFechaEmisionDesde      = null;
        $dateFechaEmisionHasta      = null;
        $dateFechaAutorizacionDesde = null;
        $dateFechaAutorizacionHasta = null;
        $strEmpresaId               = null;
        $strUsrSesion               = null;
        $strPrefijoEmpresa          = null;
        $strEmailUsrSesion          = null;
        $strStart                   = null;
        $strLimit                   = null;

        if($arrayParametros && count($arrayParametros) > 0)
        {
            $strUsrSesion       = $arrayParametros['usrSesion'];
            $strPrefijoEmpresa  = $arrayParametros['prefijoEmpresa'];
            $strEmailUsrSesion  = $arrayParametros['emailUsrSesion'];

            if(isset($arrayParametros["intEmpresaId"]))
            {
                if($arrayParametros["intEmpresaId"] != "" && $arrayParametros["intEmpresaId"] != "0")
                {
                    $strEmpresaId = trim($arrayParametros["intEmpresaId"]);
                }
            }

            if(isset($arrayParametros["start"]))
            {
                if($arrayParametros["start"] != "")
                {
                    $strStart = trim($arrayParametros["start"]);
                }
            }

            if(isset($arrayParametros["limit"]))
            {
                if($arrayParametros["limit"] != "")
                {
                    $strStart = trim($arrayParametros["limit"]);
                }
            }

            if(isset($arrayParametros["fin_tipoDocumento"]))
            {
                if($arrayParametros["fin_tipoDocumento"] != "" && $arrayParametros["fin_tipoDocumento"] != "0")
                {
                    $strTipoDocumento = trim($arrayParametros["fin_tipoDocumento"]);
                }
            }

            if(isset($arrayParametros["doc_numDocumento"]))
            {
                if($arrayParametros["doc_numDocumento"] != "" && $arrayParametros["doc_numDocumento"] != "0")
                {
                    $strNumeroDocumento = trim($arrayParametros["doc_numDocumento"]);
                }
            }

            if(isset($arrayParametros["doc_creador"]))
            {
                if($arrayParametros["doc_creador"] != "" && $arrayParametros["doc_creador"] != "0")
                {
                    $strUsrCreacion = trim($arrayParametros["doc_creador"]);
                }
            }

            if(isset($arrayParametros["doc_estado"]))
            {
                if($arrayParametros["doc_estado"] != "" && $arrayParametros["doc_estado"] != "0")
                {
                    $strEstadoDocumento = trim($arrayParametros["doc_estado"]);
                }
            }

            if(isset($arrayParametros["doc_monto"]) && isset($arrayParametros["doc_montoFiltro"]))
            {
                if($arrayParametros["doc_monto"] != "" && $arrayParametros["doc_monto"] != "0" &&
                   $arrayParametros["doc_montoFiltro"] != "" && $arrayParametros["doc_montoFiltro"] != "0")
                {
                    $strMonto = trim($arrayParametros["doc_monto"]);
                    if($arrayParametros["doc_montoFiltro"] == 'p')
                    {
                        $strFiltroMonto = "<";
                    }
                    else if($arrayParametros["doc_montoFiltro"] == 'i')
                    {
                        $strFiltroMonto = "=";
                    }
                    else if($arrayParametros["doc_montoFiltro"] == 'm')
                    {
                        $strFiltroMonto = ">";
                    }
                }
            }


            $strFechaCreacionDesde     = (isset($arrayParametros["doc_fechaCreacionDesde"]) ? $arrayParametros["doc_fechaCreacionDesde"] : 0);
            $strFechaCreacionHasta     = (isset($arrayParametros["doc_fechaCreacionHasta"]) ? $arrayParametros["doc_fechaCreacionHasta"] : 0);
            $strFechaEmisionDesde      = (isset($arrayParametros["doc_fechaEmisionDesde"]) ? $arrayParametros["doc_fechaEmisionDesde"] : 0);
            $strFechaEmisionHasta      = (isset($arrayParametros["doc_fechaEmisionHasta"]) ? $arrayParametros["doc_fechaEmisionHasta"] : 0);
            $strFechaAutorizacionDesde = (isset($arrayParametros["finDocFechaAutorizacionDesde"]) ? $arrayParametros["finDocFechaAutorizacionDesde"] : 0);
            $strFechaAutorizacionHasta = (isset($arrayParametros["finDocFechaAutorizacionHasta"]) ? $arrayParametros["finDocFechaAutorizacionHasta"] : 0);
            

            if($strFechaCreacionDesde && $strFechaCreacionDesde != "0")
            {
                $dateF                  = explode("-", $strFechaCreacionDesde);
                $dateFechaCreacionDesde = $dateF[0] . "/" . $dateF[1] . "/" . $dateF[2];
            }

            if($strFechaCreacionHasta && $strFechaCreacionHasta != "0")
            {
                $dateF                  = explode("-", $strFechaCreacionHasta);
                $fechaSqlAdd            = strtotime(date("Y-m-d", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                $dateFechaCreacionHasta = date("d/m/Y", $fechaSqlAdd);
            }

            if($strFechaEmisionDesde && $strFechaEmisionDesde != "0")
            {
                $dateF                  = explode("-", $strFechaEmisionDesde);
                $dateFechaEmisionDesde  = $dateF[0] . "/" . $dateF[1] . "/" . $dateF[2];
            }

            if($strFechaEmisionHasta && $strFechaEmisionHasta != "0")
            {
                $dateF                  = explode("-", $strFechaEmisionHasta);
                $fechaSqlAdd            = strtotime(date("Y-m-d", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                $dateFechaEmisionHasta  = date("d/m/Y", $fechaSqlAdd);
            }
            
            if($strFechaAutorizacionDesde && $strFechaAutorizacionDesde != "0")
            {
                $dateF                      = explode("-", $strFechaAutorizacionDesde);
                $dateFechaAutorizacionDesde = $dateF[0] . "/" . $dateF[1] . "/" . $dateF[2];
            }

            if($strFechaAutorizacionHasta && $strFechaAutorizacionHasta != "0")
            {
                $dateF                      = explode("-", $strFechaAutorizacionHasta);
                $fechaSqlAdd                = strtotime(date("Y-m-d", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                $dateFechaAutorizacionHasta = date("d/m/Y", $fechaSqlAdd);
            }            
        }

        $strSql = "BEGIN
                    DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_REPORTE_FACTURACION
                    (
                        :Pv_TipoDocumento,
                        :Pv_NumeroDocumento,
                        :Pv_UsrCreacion,
                        :Pv_EstadoDocumento,
                        :Pf_Monto,
                        :Pv_FiltroMonto,
                        :Pv_FechaCreacionDesde,
                        :Pv_FechaCreacionHasta,
                        :Pv_FechaEmisionDesde,
                        :Pv_FechaEmisionHasta,                        
                        :Pv_FechaAutorizacionDesde,
                        :Pv_FechaAutorizacionHasta,
                        :Pv_EmpresaCod,                        
                        :Pv_UsrSesion,
                        :Pv_PrefijoEmpresa,
                        :Pv_EmailUsrSesion,
                        :Pv_Start,
                        :Pv_Limit
                    );
                END;";

        try
        {
            $stmt = $this->_em->getConnection()->prepare($strSql);

            $stmt->bindParam('Pv_TipoDocumento', $strTipoDocumento);
            $stmt->bindParam('Pv_NumeroDocumento', $strNumeroDocumento);
            $stmt->bindParam('Pv_UsrCreacion', $strUsrCreacion);
            $stmt->bindParam('Pv_EstadoDocumento', $strEstadoDocumento);
            $stmt->bindParam('Pf_Monto', $strMonto);
            $stmt->bindParam('Pv_FiltroMonto', $strFiltroMonto);
            $stmt->bindParam('Pv_FechaCreacionDesde', trim($dateFechaCreacionDesde));
            $stmt->bindParam('Pv_FechaCreacionHasta', trim($dateFechaCreacionHasta));
            $stmt->bindParam('Pv_FechaEmisionDesde', trim($dateFechaEmisionDesde));
            $stmt->bindParam('Pv_FechaEmisionHasta', trim($dateFechaEmisionHasta));
            $stmt->bindParam('Pv_FechaAutorizacionDesde', trim($dateFechaAutorizacionDesde));
            $stmt->bindParam('Pv_FechaAutorizacionHasta', trim($dateFechaAutorizacionHasta));
            $stmt->bindParam('Pv_EmpresaCod', $strEmpresaId);
            $stmt->bindParam('Pv_UsrSesion', $strUsrSesion);
            $stmt->bindParam('Pv_PrefijoEmpresa', $strPrefijoEmpresa);
            $stmt->bindParam('Pv_EmailUsrSesion', $strEmailUsrSesion);
            $stmt->bindParam('Pv_Start', $strStart);
            $stmt->bindParam('Pv_Limit', $strLimit);

            $stmt->execute();
        }
        catch(\Exception $e)
        {
            throw($e);
        }
    }

    /**
     * Documentación para el método 'activaFactura'.
     * Este metodo activa la factura vinculada a la nota de credito.
     *
     * @param  Integer $IdDocumento   Obtiene el Id del pago
     * @param  String  $usuario       Obtiene el usuario quien actualiza
     * @param  String  $strIpCreacion Obtiene la ip quien actualiza
     * @return String  $out_Resultado Retorna un ok si la actualizacion fue correcta y un Error en caso contrario.
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 18-10-2016
     */
    public function activaFactura($arrayParametros)
    {
        try
        {
            if(!empty($arrayParametros["strfacturaId"]))
            {
                $strError = null;
                $strError = str_pad($strError, 1000, " ");
                $strSql = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_ACTIVA_FAC_POR_ANULA_NC(:Pn_IdDocumento, :Pv_User, :Pv_MsnError); END;";
                $stmt = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('Pn_IdDocumento', $arrayParametros["strfacturaId"]);
                $stmt->bindParam('Pv_User', $arrayParametros["strUser"]);
                $stmt->bindParam('Pv_MsnError', $strError);
                $stmt->execute();
            }
        }
        catch(\Exception $ex)
        {
            $strError = 'Ocurrió un error al tratar de anular la Nota de Credito' . $ex->getMessage();
        }

        $strError = trim($strError);

        return $strError;
    }

    /**
     * Documentación para el método 'getFormaPagoCliente'.
     * 
     * Obtiene la forma de pago del cliente
     *
     * @param  array $arrayParametros[ 'intIdPersonaRol' => 'Id del cliente',
     *                                 'intIdPunto'      => 'Id del punto de facturacion del cliente' ]
     *
     * @return array $arrayResultado[ 'intIdFormaPago'          => 'Id con la forma de pago del cliente',
     *                                'strCodigoFormaPago'      => 'Codigo de la forma de pago del cliente',
     *                                'strDescripcionFormaPago' => 'Descripción de la forma de pago del cliente',
     *                                'strCodigoSri'            => 'Código del SRI de la forma de pago del cliente',
     *                                'strTipoFormaPago'        => 'Tipo de la forma de pago del cliente',
     *                                'strFormaPagoObtenidaPor' => 'Texto que indica de donde fue obtenida la forma de pago, la cual puede ser por
     *                                                              PUNTO, CONTRATO o PERSONA' ]
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-10-2016
     */
    public function getFormaPagoCliente($arrayParametros)
    {
        $arrayResultado          = array();
        $intIdFormaPago          = str_pad($intIdFormaPago, 10, " ");
        $strCodigoFormaPago      = str_pad($strCodigoFormaPago, 4, " ");
        $strDescripcionFormaPago = str_pad($strDescripcionFormaPago, 60, " ");
        $strCodigoSri            = str_pad($strCodigoSri, 2, " ");
        $strTipoFormaPago        = str_pad($strTipoFormaPago, 20, " ");
        $strFormaPagoObtenidaPor = str_pad($strFormaPagoObtenidaPor, 10, " ");
        $intIdPersonaRol         = ( isset($arrayParametros['intIdPersonaRol']) && !empty($arrayParametros['intIdPersonaRol']) )
                                     ? $arrayParametros['intIdPersonaRol'] : 0;
        $intIdPunto              = ( isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']) )
                                     ? $arrayParametros['intIdPunto'] : 0;
        
        if( intval($intIdPersonaRol) > 0 || intval($intIdPunto) > 0 )
        {
            $strSql = "BEGIN DB_FINANCIERO.FNCK_CONSULTS.P_GET_FORMA_PAGO_CLIENTE(:intIdPersonaRol, ".
                                                                                 ":intIdPunto, ".
                                                                                 ":intIdFormaPago, ".
                                                                                 ":strCodigoFormaPago, ".
                                                                                 ":strDescripcionFormaPago, ".
                                                                                 ":strCodigoSri, ".
                                                                                 ":strTipoFormaPago, ".
                                                                                 ":strFormaPagoObtenidaPor); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('intIdPersonaRol' ,        $intIdPersonaRol);
            $objStmt->bindParam('intIdPunto',              $intIdPunto);
            $objStmt->bindParam('intIdFormaPago',          $intIdFormaPago);
            $objStmt->bindParam('strCodigoFormaPago',      $strCodigoFormaPago);
            $objStmt->bindParam('strDescripcionFormaPago', $strDescripcionFormaPago);
            $objStmt->bindParam('strCodigoSri',            $strCodigoSri);
            $objStmt->bindParam('strTipoFormaPago',        $strTipoFormaPago);
            $objStmt->bindParam('strFormaPagoObtenidaPor', $strFormaPagoObtenidaPor);
            $objStmt->execute();
        }
        
        $arrayResultado['intIdFormaPago']          = $intIdFormaPago;
        $arrayResultado['strCodigoFormaPago']      = $strCodigoFormaPago;
        $arrayResultado['strDescripcionFormaPago'] = $strDescripcionFormaPago;
        $arrayResultado['strCodigoSri']            = $strCodigoSri;
        $arrayResultado['strTipoFormaPago']        = $strTipoFormaPago;
        $arrayResultado['strFormaPagoObtenidaPor'] = $strFormaPagoObtenidaPor;
            
        return $arrayResultado;
    }

     /**
    * Documentación para el método 'aplicarNciInterna'.
    * Funcion que ejecuta el procedimiento P_APLICA_NOTA_CREDITO que genera los ANTC y NDI 
    * que se requieran al aplicar la Nota de Credito Interna
    *   
    *@param mixed $arrayNciAplica[
    *                               'intIdDocumento'             => id documento de la nota de credito
    *                               'intRefereneciaDocumentoId'  => referencia documento id de la nc(FAC ID)
    *                               'intOficinaId'               => id de la oficina de la NCI
    *                               'strMensaje'                 => mensaje de error en caso de existir 
    *                             ]
    * @return string $strMensaje      Retorna mensaje de error en caso de existir
    *
    * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
    * @version 1.0 18-10-2016
    */
    public function aplicarNciInterna($arrayNciAplica)
    {
        $strMensaje = str_pad($strMensaje, 1000, " ");
        $strSql = "BEGIN 
                     DB_FINANCIERO.FNCK_CONSULTS.P_APLICA_NOTA_CREDITO(
                                                         :intIdDocumento, 
                                                         :intRefereneciaDocumentoId, 
                                                         :intOficinaId,
                                                         :strMensaje
                                                         ); 
                   END;";
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam('intIdDocumento'           , $arrayNciAplica["intIdDocumento"]);
        $objStmt->bindParam('intRefereneciaDocumentoId', $arrayNciAplica["intRefereneciaDocumentoId"]);
        $objStmt->bindParam('intOficinaId'             , $arrayNciAplica["intOficinaId "]);
        $objStmt->bindParam('strMensaje'               , $strMensaje);
        $objStmt->execute();
        return $strMensaje;
    }

    /**
     * Documentación para contabilizarDocumentosNCI
     * 
     * Función para contabilizar las NCI realizadas en el TELCOS
     * @param array $arrayContabilidad    - strEmpresaCod
     *                                    - strPrefijo
     *                                    - strCodigoTipoDocumento
     *                                    - strTipoProceso
     *                                    - intIdDocumento
     *                                    - strFechaProceso
     * 
     * Se agrega el parametro de fechaProceso para permitir realizar re-procesos a fecha
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-10-2016
     *      
     */
    public function contabilizarDocumentosNCI($arrayContabilidad)
    {
        //Proceso para contabilizar las NCI
        if(isset($arrayContabilidad["intIdDocumento"]) && (!empty($arrayContabilidad["intIdDocumento"])))
        {
            $strSql = "BEGIN
                        FNKG_CONTABILIZAR_NCI.P_CONTABILIZAR
                        (
                            :empresaCod,
                            :prefijo,
                            :codigoTipoDocumento,
                            :tipoProceso,
                            :idDocumento                            
                        );
                    END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('empresaCod'           , $arrayContabilidad["strEmpresaCod"]);
            $objStmt->bindParam('prefijo'              , $arrayContabilidad["strPrefijo"]);
            $objStmt->bindParam('codigoTipoDocumento'  , $arrayContabilidad["strCodigoTipoDocumento"]);
            $objStmt->bindParam('tipoProceso'          , $arrayContabilidad["strTipoProceso"]);
            $objStmt->bindParam('idDocumento'          , $arrayContabilidad["intIdDocumento"]);            
            $objStmt->execute();
        }
    }
    
    /**
     * Documentación para getValidXmlValue
     * 
     * Función que limpia los caracteres no validos dentro de un tag xml
     * de la descripción de los servicios en las facturas 
     * 
     * @param String $strDescripcion Es la cadena a evaluar
     * @return String $strDescripcionCleaned Es la cadena evaluada, sin lo caracteres no valido de un tag xml.
     *
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.0, 22-12-2016
     */
    public function getValidXmlValue($strDescripcion)
    {
        try
        {
            $strDescripcionCleaned = "";
            $strDescripcionCleaned = str_pad($strDescripcionCleaned, 2000, " ");
            $strSql = "BEGIN :strDescripcionCleaned := " . 
                      "DB_FINANCIERO.FNCK_COM_ELECTRONICO.F_GET_VARCHAR_VALID_XML_VALUE(:strDescripcion); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('strDescripcion',        $strDescripcion);
            $objStmt->bindParam('strDescripcionCleaned', $strDescripcionCleaned);
            $objStmt->execute();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            $strDescripcionCleaned = "";
        }
        
        return $strDescripcionCleaned;
    }
    
     /**
     * Obtiene notas de credito Activas asociadas a la factura.
     * 
     * @param array $arrayParametros['intIdDocumento'         Id del Documento
     *                               'arrayInEstados'         Estados de los documentos que se desean buscar ]
     * 
     * @return array $arrayNotaCreditoActivas
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 20-07-2017
     */
    
    public function getNotasDeCreditoActivas( $arrayParametros )
    {
        $arrayNotasDeCreditosActivas = array();
        
        $objQuery      = $this->_em->createQuery();
         
        $strSelect  = "SELECT IDFC ";
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroCab IDFC ";
        $strWhere   = "WHERE  IDFC.referenciaDocumentoId = :intIdFactura ";
        $strOrderBy = "ORDER BY IDFC.feCreacion ";
         
        $objQuery->setParameter('intIdFactura', $arrayParametros['intIdDocumento']);
        
        if( !empty($arrayParametros['arrayInEstados']) )
        {
            $strWhere .= "AND IDFC.estadoImpresionFact IN (:arrayInEstados) ";
            $objQuery->setParameter('arrayInEstados', array_values($arrayParametros['arrayInEstados']));
        }
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $objQuery->setDQL($strSql);

        $arrayNotasDeCreditosActivas = $objQuery->getResult();
        
        return $arrayNotasDeCreditosActivas;
    }
    
    /**
     * Obtiene Facturas asociadas al Punto por estados 
     * 
     * @param array $arrayParametros['intIdPunto'          Id del Punto
     *                               'arrayInEstados'      Estados de los documentos que se desean buscar 
     *                               'arrayTipoDocumento'  Código con los documentos que se desean buscar ]
     * 
     * @return array $arrayFacturasPorPunto
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 03-05-2018
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 29-07-2022 - Se agrega validación al query para obtener facturas con las características
     *                           POR_CONTRATO_FISICO o POR_CONTRATO_DIGITAL.
     */
    
    public function getFacturasPorPuntoPorEstado( $arrayParametros )
    {
        $arrayFacturasPorPunto = array();
        
        $objQuery      = $this->_em->createQuery();
         
        $strSelect  = "SELECT IDFC ";
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroCab IDFC,"
                    . " schemaBundle:AdmiTipoDocumentoFinanciero ATDF ";
        $strWhere   = "WHERE  "
                    . " IDFC.tipoDocumentoId = ATDF.id "
                    . " AND IDFC.puntoId = :intIdPunto ";
        $strOrderBy = "ORDER BY IDFC.feCreacion ";
         
        $objQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        
        if( !empty($arrayParametros['arrayTipoDocumento']) )
        {
            $strWhere .= "AND ATDF.codigoTipoDocumento IN (:arrayTipoDocumento) ";
            $objQuery->setParameter('arrayTipoDocumento', array_values($arrayParametros['arrayTipoDocumento']));
        }
        
        if( !empty($arrayParametros['arrayInEstados']) )
        {
            $strWhere .= "AND IDFC.estadoImpresionFact IN (:arrayInEstados) ";
            $objQuery->setParameter('arrayInEstados', array_values($arrayParametros['arrayInEstados']));
        }
        //Se agrega validaciones en el query para los documentos
        if( !empty($arrayParametros['arrayCaracteristicas']) && !empty($arrayParametros['strValor']) 
                && !empty($arrayParametros['strEstadoCaracDoc']) )
        {
            $strFrom .= " ,schemaBundle:InfoDocumentoCaracteristica IDC, "
                      . " schemaBundle:AdmiCaracteristica AC ";
            
            $strWhere .= " AND IDFC.id      = IDC.documentoId "
                       . " AND AC.id        = IDC.caracteristicaId "
                       . " AND AC.descripcionCaracteristica IN (:arrayCaracteristicas) "
                       . " AND IDC.valor    = :strValor "
                       . " AND IDC.estado   = :strEstadoCaracDoc ";
            
            $objQuery->setParameter('arrayCaracteristicas', array_values($arrayParametros['arrayCaracteristicas']));
            $objQuery->setParameter('strValor', $arrayParametros['strValor']);
            $objQuery->setParameter('strEstadoCaracDoc', $arrayParametros['strEstadoCaracDoc']);
        }
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $objQuery->setDQL($strSql);

        $arrayFacturasPorPunto = $objQuery->getResult();
        
        return $arrayFacturasPorPunto;
    }
    /**
     * Obtiene Facturas asociadas al Punto y al servicio por estados y que sea generadas por el proceso de Contrato Digital
     * 
     * @param array $arrayParametros    'intIdPunto'           Id del Punto.<br>
     *                                  'intIdServicio'        Id del servicio.<br>
     *                                  'arrayInEstados'       Estados de los documentos que se desean buscar <br>
     *                                  'arrayTipoDocumento'   Código con los documentos que se desean buscar <br>
     *                                  'arrayCaracteristicas' Descripción de las características de contratos.<br>
     * 
     * @return array $arrayFacturasContrato
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 04-05-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 22-10-2018
     * Se modifica el query para permitir el filtro de la característica mediante un array. Siendo este POR_CONTRATO_DIGITAL y POR_CONTRATO_WEB.
     * Se agrega filtro de estado para la tabla AdmiTipoDocumentoFinanciero y AdmiCaracteristica
     * Se modifica el nombre de la función a getFacturasPorContratoFisicoDigital
     */
    
    public function getFacturasPorContratoFisicoDigital( $arrayParametros )
    {
        $objQuery      = $this->_em->createQuery();
         
        $strSelect  = "SELECT IDFC ";
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroCab IDFC,"
                    . " schemaBundle:InfoDocumentoFinancieroDet IDFD, "
                    . " schemaBundle:AdmiTipoDocumentoFinanciero ATDF, "
                    . " schemaBundle:InfoDocumentoCaracteristica IDCA, "
                    . " schemaBundle:AdmiCaracteristica AC ";
        $strWhere   = "WHERE  "
                    . " IDFC.id                          = IDFD.documentoId "
                    . " AND IDFC.tipoDocumentoId         = ATDF.id "
                    . " AND ATDF.estado                  = :strEstadoActivo"
                    . " AND IDFC.id                      = IDCA.documentoId "
                    . " AND AC.id                        = IDCA.caracteristicaId "
                    . " AND AC.descripcionCaracteristica IN (:arrayCaracteristicas) "
                    . " AND AC.estado                    = :strEstadoActivo"
                    . " AND IDCA.valor                   = :strValor "
                    . " AND IDCA.estado                  = :strEstadoActivo "
                    . " AND IDFC.puntoId                 = :intIdPunto "
                    . " AND IDFD.servicioId              = :intIdServicio ";
        $strOrderBy = " ORDER BY IDFC.feCreacion ";
         
        $objQuery->setParameter('intIdPunto',    $arrayParametros['intIdPunto']);
        $objQuery->setParameter('intIdServicio', $arrayParametros['intIdServicio']);
        
        if( !empty($arrayParametros['arrayTipoDocumento']) )
        {
            $strWhere .= "AND ATDF.codigoTipoDocumento IN (:arrayTipoDocumento) ";
            $objQuery->setParameter('arrayTipoDocumento', array_values($arrayParametros['arrayTipoDocumento']));
        }
        
        if( !empty($arrayParametros['arrayInEstados']) )
        {
            $strWhere .= "AND IDFC.estadoImpresionFact IN (:arrayInEstados) ";
            $objQuery->setParameter('arrayInEstados', array_values($arrayParametros['arrayInEstados']));
        }
        $objQuery->setParameter('arrayCaracteristicas', array_values($arrayParametros['arrayCaracteristicas']));
        $objQuery->setParameter('strValor',                     'S');
        $objQuery->setParameter('strEstadoActivo',              'Activo');
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $objQuery->setDQL($strSql);

        $arrayFacturasContrato = $objQuery->getResult();
        
        return $arrayFacturasContrato;
    }
  /**
     * Documentación para el método 'ejecutarReporteBuro'.
     *
     * Ejecuta la generación de reporte de buro según los parámetros indicados.
     * 
     * @param array $arrayParametros      - strHost                Host de conección para la base de datos
     *                                    - strPathFileLogger      Ruta donde se guardará el log del script
     *                                    - strNameFileLogger      Nombre del log del script
     *                                    - strPrefijoEmpresa      Empresa del usuario que manda a ejecutar el script para generar el reporte
     *                                    - strIpSession           Ip del usuario que manda a ejecutar el script para generar el reporte
     *                                    - strUsuarioSession      Nombre del usuario que manda a ejecutar el script para generar el reporte
     *                                    - strValorClientesBuenos Valor de deuda permitido para los clientes buenos
     *                                    - strValorClientesMalos  Valor de deuda permitido para los clientes malos
     *                                    - strDirectorioUpload    Directorio donde se guardará el reporte a descargar
     *                                    - strAmbiente            Si es generado por el usuario o por el job
     *                                    - emailUsrSesion         Email del usuario que genera el reporte
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 10-08-2017
     *
     */     
    public function ejecutarReporteBuro($arrayParametros)
    {
        
        $strHostScripts    = null;
        $strPathFileLogger = null;
        $strNameFileLogger = null;
        $strPrefijoEmpresa = null;
        $strIpSession      = null;
        $strUserSession    = null;
        $strClientesBuenos = null;
        $strClientesMalos  = null;
        $strUploadPath     = null;
        $strAmbiente       = null;
        $strEmailUsrSesion = '';
        
        if($arrayParametros && count($arrayParametros) > 0)
        {
            $strHostScripts    = $arrayParametros['strHost'];
            $strPathFileLogger = $arrayParametros['strPathFileLogger'];
            $strNameFileLogger = $arrayParametros['strNameFileLogger'];
            $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
            $strIpSession      = $arrayParametros['strIpSession'];
            $strUserSession    = $arrayParametros['strUsuarioSession'];
            $strClientesBuenos = $arrayParametros['strValorClientesBuenos'];
            $strClientesMalos  = $arrayParametros['strValorClientesMalos'];
            $strUploadPath     = $arrayParametros['strDirectorioUpload'];
            $strAmbiente       = $arrayParametros['strAmbiente'];
            $strEmailUsrSesion = $arrayParametros['emailUsrSesion'];
        }

        try
        {
            
            $strSql = "DECLARE
                         Ln_job_exists NUMBER;
                       BEGIN
                       
                        SELECT COUNT(*) INTO Ln_job_exists
                          FROM user_scheduler_jobs
                         WHERE job_name = 'JOB_RPTE_BURO_MD';

                        IF Ln_job_exists = 1 THEN
                            DBMS_SCHEDULER.DROP_JOB(job_name => '\"DB_FINANCIERO\".\"JOB_RPTE_BURO_MD\"',
                                                        defer => false,
                                                        force => false);
                        END IF;
                
                        DBMS_SCHEDULER.CREATE_JOB (
                                            job_name   => '\"DB_FINANCIERO\".\"JOB_RPTE_BURO_MD\"',
                                            job_type   => 'PLSQL_BLOCK',
                                            job_action => 'DECLARE
                                                                PV_HOST VARCHAR2(50);
                                                                PV_PATHFILELOGGER VARCHAR2(1000);
                                                                PV_NAMEFILELOGGER VARCHAR2(1000);
                                                                PV_PREFIJOEMPRESA VARCHAR2(50);
                                                                PV_IPSESSION VARCHAR2(100);
                                                                PV_USUARIOSESSION VARCHAR2(100);
                                                                PV_VALORCLIENTESBUENOS VARCHAR2(100);
                                                                PV_VALORCLIENTESMALOS VARCHAR2(100);
                                                                PV_DIRECTORIOUPLOAD VARCHAR2(1000);
                                                                PV_AMBIENTE VARCHAR2(50);
                                                                PV_EMAILUSRSESION VARCHAR2(250);
                                                            BEGIN
                                                              PV_HOST                 := ''$strHostScripts'';
                                                              PV_PATHFILELOGGER       := ''$strPathFileLogger'';
                                                              PV_NAMEFILELOGGER       := ''$strNameFileLogger'';
                                                              PV_PREFIJOEMPRESA       := ''$strPrefijoEmpresa'';
                                                              PV_IPSESSION            := ''$strIpSession'';
                                                              PV_USUARIOSESSION       := ''$strUserSession'';
                                                              PV_VALORCLIENTESBUENOS  := ''$strClientesBuenos'';
                                                              PV_VALORCLIENTESMALOS   := ''$strClientesMalos'';
                                                              PV_DIRECTORIOUPLOAD     := ''$strUploadPath'';
                                                              PV_AMBIENTE             := ''$strAmbiente'';
                                                              PV_EMAILUSRSESION       := ''$strEmailUsrSesion'';

                                                              DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_GEN_REPORTE_BURO(
                                                                PV_HOST                => PV_HOST,
                                                                PV_PATHFILELOGGER      => PV_PATHFILELOGGER,
                                                                PV_NAMEFILELOGGER      => PV_NAMEFILELOGGER,
                                                                PV_PREFIJOEMPRESA      => PV_PREFIJOEMPRESA,
                                                                PV_IPSESSION           => PV_IPSESSION,
                                                                PV_USUARIOSESSION      => PV_USUARIOSESSION,
                                                                PV_VALORCLIENTESBUENOS => PV_VALORCLIENTESBUENOS,
                                                                PV_VALORCLIENTESMALOS  => PV_VALORCLIENTESMALOS,
                                                                PV_DIRECTORIOUPLOAD    => PV_DIRECTORIOUPLOAD,
                                                                PV_AMBIENTE            => PV_AMBIENTE,
                                                                PV_EMAILUSRSESION      => PV_EMAILUSRSESION
                                                              );
                                                            END;',
                                                    number_of_arguments => 0,
                                                    start_date => NULL,
                                                    repeat_interval => NULL,
                                                    end_date => NULL,
                                                    enabled => FALSE,
                                                    auto_drop => FALSE,
                                                    comments => 'Proceso para generar el reporte de buro de la empresa MD  ');
                DBMS_SCHEDULER.SET_ATTRIBUTE( 
                         name => '\"DB_FINANCIERO\".\"JOB_RPTE_BURO_MD\"', 
                         attribute => 'logging_level', value => DBMS_SCHEDULER.LOGGING_OFF);



                DBMS_SCHEDULER.enable(
                         name => '\"DB_FINANCIERO\".\"JOB_RPTE_BURO_MD\"');
            END;";
            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->execute();
        }
        catch(\Exception $e)
        {
            error_log('Error en InfoDocumentoFinancieroCabRepository - ejecutarReporteBuro: '.$e);
            throw($e);
        }
    }
    
    /**
    * Documentacion para la funcion getEstadoCuentaCliente
    *
    * Función que llama a un procedimiento almacenado en la base que  retorna los movimientos financieros del cliente enviado como parámetro.
    * 
    * @param mixed $arrayParametros[
    *                               'strEmpresaCod'          => código de la empresa en sesión
    *                               'intIdCliente'           => Id del cliente en sesión
    *                               'strFechaCreacionDesde'  => rango inicial para fecha de creacion
    *                               'strFechaCreacionHasta'  => rango final para fecha de creacion
    *                               'objCursor'              => identificación del cliente
    *                               ]
    *
    * @return mixed $arrayResultado[
    *                               'total'      => número total de registros
    *                               'registros'  => array de registos resultantes de la consulta realizada.
    *                               ]
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 04-10-2017
    * 
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 23-10-2017 - Se recupera las siguientes columnas del estado de cuenta del cliente: 'PAGO_TIENE_DEPENDENCIA', 'SALDO_ACT_DOCUMENTO'
    * Donde 'PAGO_TIENE_DEPENDENCIA' comprueba si un Pago o Ant depende de un Padre (tiene dependencia) y  'SALDO_ACT_DOCUMENTO' permite obtener el 
    * saldo del documento: FAC o FACP.
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.1 01-03-2018 - Se cambia Filtro de FeCreacion a FeEmision y se Agregan al Grid FeEmision, FeAutorizacion      
    * 
    */    
    public function getEstadoCuentaCliente($arrayParametros)
    {
        $intTotal           = 0;
        $arrayMovimientos[] = array();
        $objCursor          = $arrayParametros['cursor'];
        
        if($arrayParametros && count($arrayParametros)>0)
        {
            try
            { 
                $strSql = "BEGIN
                            DB_FINANCIERO.FNCK_CONSULTS.P_ESTADO_CTA_CLIENTE
                            (
                                :Pv_EmpresaCod,
                                :Pn_PersonaId,
                                :Pv_FechaEmisionDesde,
                                :Pv_FechaEmisionHasta,
                                :Pn_TotalRegistros,
                                :Pc_Documentos
                            );
                           END;";
 
                $objStm  = oci_parse($arrayParametros['oci_con'], $strSql);

                oci_bind_by_name($objStm, ":Pv_EmpresaCod", $arrayParametros['strEmpresaCod']);
                oci_bind_by_name($objStm, ":Pn_PersonaId", $arrayParametros['intIdCliente']);
                oci_bind_by_name($objStm, ":Pv_FechaEmisionDesde", $arrayParametros['strFechaDesde']);
                oci_bind_by_name($objStm, ":Pv_FechaEmisionHasta", $arrayParametros['strFechaHasta']);
                oci_bind_by_name($objStm, ":Pn_TotalRegistros", $intTotal, 10);   
                oci_bind_by_name($objStm, ":Pc_Documentos", $objCursor, -1, OCI_B_CURSOR);

                oci_execute($objStm); 
                oci_execute($objCursor, OCI_DEFAULT); 

                while (($objRow = oci_fetch_array($objCursor)) !== false)
                { 
                    $arrayMovimientos[] = array(
                                                 'id'                  => $objRow['ID_DOCUMENTO'],
                                                 'numeroFacturaSri'    => trim($objRow['NUMERO_FACTURA_SRI']),
                                                 'tipoDocumentoId'     => $objRow['TIPO_DOCUMENTO_ID'],
                                                 'valorTotal'          => $objRow['VALOR_TOTAL'],
                                                 'feCreacion'          => trim($objRow['FEC_CREACION']),
                                                 'strFeEmision'        => trim($objRow['FEC_EMISION']),
                                                 'strFeAutorizacion'   => trim($objRow['FEC_AUTORIZACION']),
                                                 'puntoId'             => $objRow['PUNTO_ID'],
                                                 'oficinaId'           => $objRow['OFICINA_ID'],
                                                 'referencia'          => trim($objRow['REFERENCIA']),
                                                 'codigoFormaPago'     => trim($objRow['CODIGO_FORMA_PAGO']),
                                                 'numeroReferencia'    => trim($objRow['NUMERO_REFERENCIA']),
                                                 'numeroCuentaBanco'   => trim($objRow['NUMERO_CUENTA_BANCO']),
                                                 'referenciaId'        => $objRow['REFERENCIA_ID'],
                                                 'codigoTipoDocumento' => trim($objRow['CODIGO_TIPO_DOCUMENTO']),
                                                 'movimiento'          => trim($objRow['MOVIMIENTO']),
                                                 'estadoImpresionFact' => trim($objRow['ESTADO_IMPRESION_FACT']),
                                                 'refAnticipoId'       => $objRow['REF_ANTICIPO_ID'],
                                                 'pagoTieneDependencia'=> $objRow['PAGO_TIENE_DEPENDENCIA'],
                                                 'saldoActualDocumento'=> $objRow['SALDO_ACT_DOCUMENTO']
                                                 );
                }
                
                $arrayResultado = array('total' => $intTotal, 'registros' => $arrayMovimientos);

            }catch (\Exception $e) 
            {
                error_log('Error al consultar estado de cuenta: '.$e->getMessage());
                throw($e);
            }
        }
        else 
        { 
            $arrayResultado = '{"registros":"[]","total":0}';
        }
        
        return $arrayResultado;
    }

    /**
     * Documentación para el método 'getInformacionFacturacionAsesor'.
     * 
     *
     * Actualización: Se agrega parametro strEmailUsrSession
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 24-04-2018
     * 
     * Función encargada para retornar la información necesaria para la presentación de facturación de asesor
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-03-2018
     *
     * @param array $arrayParametros [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                strUsrCreacion          => Usuario en sessión
     *                                strIpCreacion           => Ip del usuario en sessión
     *                                strTipoPersonal         => El tipo del personal en sessión si es 'VENDEDOR' o 'SUBGERENTE'
     *                                strTipo                 => El tipo si es MRC o NRC
     *                                strTipoConsulta         => Si es Totalizado o Detallado
     *                                intIdPersonEmpresaRol   => Id del usuario en sessión
     *                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO'
     *                                strEmailUsrSession      => Email del usuario que realiza la consulta ]
     *
     * @return cursor $cursorInformacionComercial
     */
    public function getDetalleFacturacionAsesor($arrayParametros)
    {
        $cursorFacturacion = null;
        try
        {
            $strEmailUsrSession      = ( isset($arrayParametros['strEmailUsrSession']) && !empty($arrayParametros['strEmailUsrSession']) )
                                       ? $arrayParametros['strEmailUsrSession'] : null;
            $strPrefijoEmpresa       = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                       ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strFechaInicio          = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                       ? $arrayParametros['strFechaInicio'] : null;
            $strDatabaseDsn          = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserBiFinanciero     = ( isset($arrayParametros['strUserBiFinanciero']) && !empty($arrayParametros['strUserBiFinanciero']) )
                                       ? $arrayParametros['strUserBiFinanciero'] : null;
            $strPasswordBiFinanciero = ( isset($arrayParametros['strPasswordBiFinanciero']) && !empty($arrayParametros['strPasswordBiFinanciero']) )
                                       ? $arrayParametros['strPasswordBiFinanciero'] : null;
            $strTipo                 = ( isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']) )
                                    ? $arrayParametros['strTipo'] : null;
            $strTipoConsulta         = ( isset($arrayParametros['strTipoConsulta']) && !empty($arrayParametros['strTipoConsulta']) )
                                    ? $arrayParametros['strTipoConsulta'] : null;
            $strTipoPersonal      = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                    ? $arrayParametros['strTipoPersonal'] : null;
            $intIdPersonEmpresaRol   = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                       ? $arrayParametros['intIdPersonEmpresaRol'] : 0;

            if( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strDatabaseDsn)
                && !empty($strUserBiFinanciero) && !empty($strPasswordBiFinanciero) )
            {
                $objOciConexion             = oci_connect($strUserBiFinanciero, $strPasswordBiFinanciero, $strDatabaseDsn);
                $cursorFacturacion          = oci_new_cursor($objOciConexion);
                $strSQL                     = "BEGIN BI_FINANCIERO.BFNKG_CONSULTS.P_GET_INFO_DET_FACT_ASESOR( :strPrefijoEmpresa, ".
                                                                                                              ":strTipoConsulta, ".
                                                                                                              ":strTipoPersonal, ".
                                                                                                              ":strTipo, ".
                                                                                                              ":intIdPersonEmpresaRol, ".
                                                                                                              ":strFechaInicio, ".
                                                                                                              ":strEmailUsrSession,".
                                                                                                              ":cursorFacturacion ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":strPrefijoEmpresa",          $strPrefijoEmpresa);
                oci_bind_by_name($objStmt, ":strFechaInicio",             $strFechaInicio);
                oci_bind_by_name($objStmt, ":strTipoConsulta",            $strTipoConsulta);
                oci_bind_by_name($objStmt, ":strTipo",                    $strTipo);
                oci_bind_by_name($objStmt, ":strTipoPersonal",            $strTipoPersonal);
                oci_bind_by_name($objStmt, ":intIdPersonEmpresaRol",      $intIdPersonEmpresaRol);
                oci_bind_by_name($objStmt, ":strEmailUsrSession",         $strEmailUsrSession);
                oci_bind_by_name($objStmt, ":cursorFacturacion", $cursorFacturacion, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($cursorFacturacion);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información de facturacion de asesor. - Prefijo('.
                                     $strPrefijoEmpresa.'), FechaInicio('.$strFechaInicio.') Database('.
                                     $strDatabaseDsn.'), UsrComercial('.$strUserBiFinanciero.'), PassComercial('.$strPasswordBiFinanciero.').'); 
            }//( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strDatabaseDsn)
            //&& !empty($strUserBiFinanciero) && !empty($strPasswordBiFinanciero) )
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $cursorFacturacion;
    }
    /**
     * Determina la validez de una identificacion segun su tipo
     * @param  string $strLogin
     * @param  string $strIdentificacionCliente
     * @return string $strMensaje
     *
     * @author Sofía Fernandez <sfernandez@telconet.ec>
     * @version 1.0 07-05-2018
     */
    public function consumeApiInterfazPanama($arrayParametros)
    {
        $arrayDatos     = array();
        $intIdDocumento = $arrayParametros['intIdDocumento'];
        $strCodEmpresa  = $arrayParametros['strCodEmpresa'];
        $strMensaje     = str_repeat(' ', 32767);
        $strCodError    = str_repeat(' ', 32767);
        
        $strSql     = 'BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_API_INTERFAZ_FACTURACION_TNP(:intIdDocumento,'
                                                                                        . ':strCodEmpresa,'
                                                                                        . ':strCodError,'
                                                                                        . ':strMensaje); END;';
        try
        {
            $strStmt = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('intIdDocumento',   $intIdDocumento);
            $strStmt->bindParam('strCodEmpresa',    $strCodEmpresa);
            $strStmt->bindParam('strCodError',      $strCodError);
            $strStmt->bindParam('strMensaje',       $strMensaje);
            $strStmt->execute();
           
           
            if(!empty ($strCodError)|| $strCodError=='Error')
            {
                 error_log("InfoDocumentoFinancieroCabRepository->consumeApiInterfazPanama " . $strCodError .' '. $strMensaje);
                
            }
        } 
        catch (\Exception $ex) 
        {
            error_log("Error ->> InfoDocumentoFinancieroCabRepository->consumeApiInterfazPanama " . $ex->getMessage());
        }
        $arrayDatos['strCodError']= $strCodError;
        $arrayDatos['strMensaje'] = $strMensaje;
        
        return $arrayDatos;

    }     
    
    /**
     * Genera Reporte de Cierre Fiscal X o Cierre Fiscal Z para la empresa Telconet Panamá
     * @param array $arrayParametros [
     *                                strTipoCierre      => Tipo de Cierre Fiscal : Cierre Fiscal X o Cierre Fiscal Z
     *                                strCodEmpresa      => Codigo de la empresa que realizará la consulta.
     *                                strPrefijoEmpresa  => Empresa del usuario que manda a ejecutar el script para generar el reporte
     *                                strUsuarioSession  => Nombre del usuario que manda a ejecutar el script para generar el reporte
     *                                strEmailUsrSesion  => Email del usuario que genera el reporte
     *                               ]
     *
     * @return array $arrayDatos
     *
     * @author apenaherrera@telconet.ec
     * @version 1.0 28/01/2019
     */
    public function consumeApiInterfazPanamaCierreFiscal($arrayParametros)
    {
        $arrayDatos        = array();
        $strTipoCierre     = $arrayParametros['strTipoCierre'];
        $strCodEmpresa     = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
        $strUserSession    = $arrayParametros['strUserSession'];
        $strEmailUsrSesion = $arrayParametros['strEmailUsrSesion'];        
        $strMensaje        = str_repeat(' ', 32767);
        $strCodError       = str_repeat(' ', 32767);
        
        $strSql     = 'BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_API_CIERRE_FISCAL_TNP(:strTipoCierre,'
                                                                                        . ':strCodEmpresa,'
                                                                                        . ':strPrefijoEmpresa,'
                                                                                        . ':strUserSession,'
                                                                                        . ':strEmailUsrSesion,'
                                                                                        . ':strCodError,'
                                                                                        . ':strMensaje); END;';
        try
        {
            $strStmt = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('strTipoCierre',     $strTipoCierre);
            $strStmt->bindParam('strCodEmpresa',     $strCodEmpresa);
            $strStmt->bindParam('strPrefijoEmpresa', $strPrefijoEmpresa);
            $strStmt->bindParam('strUserSession',    $strUserSession);
            $strStmt->bindParam('strEmailUsrSesion', $strEmailUsrSesion);
            $strStmt->bindParam('strCodError',       $strCodError);
            $strStmt->bindParam('strMensaje',        $strMensaje);
            $strStmt->execute();
           
           
            if(!empty ($strCodError)|| $strCodError=='Error')
            {
                 error_log("InfoDocumentoFinancieroCabRepository->consumeApiInterfazPanamaCierreFiscal " . $strCodError .' '. $strMensaje);
                
            }
        } 
        catch (\Exception $ex) 
        {
            error_log("Error ->> InfoDocumentoFinancieroCabRepository->consumeApiInterfazPanamaCierreFiscal " . $ex->getMessage());
        }
        $arrayDatos['strCodError']= $strCodError;
        $arrayDatos['strMensaje'] = $strMensaje;
        
        return $arrayDatos;

    }     

    /**
     * Documentación para el método 'getCumplimientoAsesor'.
     * 
     *
     * Actualización: Se agrega parametro strEmailUsrSession
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 24-04-2018
     * 
     * Función encargada para retornar la información necesaria para la presentación de facturación de asesor
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-03-2018
     *
     * @param array $arrayParametros [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                strUsrCreacion          => Usuario en sessión
     *                                strIpCreacion           => Ip del usuario en sessión
     *                                strTipoPersonal         => El tipo del personal en sessión si es 'VENDEDOR' o 'SUBGERENTE'
     *                                strTipo                 => El tipo si es MRC o NRC
     *                                strTipoConsulta         => Si es Totalizado o Detallado
     *                                intIdPersonEmpresaRol   => Id del usuario en sessión
     *                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO'
     *                                strEmailUsrSession      => Email del usuario que realiza la consulta ]
     *
     * @return cursor $cursorInformacionComercial
     */
    public function getCumplimientoAsesor($arrayParametros)
    {
        $strMensajeRespuesta = '';
        try
        {
            $strEmailUsrSession      = ( isset($arrayParametros['strEmailUsrSession']) && !empty($arrayParametros['strEmailUsrSession']) )
                                       ? $arrayParametros['strEmailUsrSession'] : null;
            $strPrefijoEmpresa       = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                       ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strFechaInicio          = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                       ? $arrayParametros['strFechaInicio'] : null;
            $strDatabaseDsn          = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserBiFinanciero     = ( isset($arrayParametros['strUserBiFinanciero']) && !empty($arrayParametros['strUserBiFinanciero']) )
                                       ? $arrayParametros['strUserBiFinanciero'] : null;
            $strPasswordBiFinanciero = ( isset($arrayParametros['strPasswordBiFinanciero']) && !empty($arrayParametros['strPasswordBiFinanciero']) )
                                       ? $arrayParametros['strPasswordBiFinanciero'] : null;
            $strTipo                 = ( isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']) )
                                    ? $arrayParametros['strTipo'] : null;
            $strTipoConsulta         = ( isset($arrayParametros['strTipoConsulta']) && !empty($arrayParametros['strTipoConsulta']) )
                                    ? $arrayParametros['strTipoConsulta'] : null;
            $strTipoPersonal      = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                    ? $arrayParametros['strTipoPersonal'] : null;
            $intIdPersonEmpresaRol   = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                       ? $arrayParametros['intIdPersonEmpresaRol'] : 0;

            if( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strDatabaseDsn)
                && !empty($strUserBiFinanciero) && !empty($strPasswordBiFinanciero) )
            {

                $objOciConexion             = oci_connect($strUserBiFinanciero, $strPasswordBiFinanciero, $strDatabaseDsn);
                $strSQL                     = "BEGIN BI_FINANCIERO.BFNKG_CONSULTS.P_ENVIA_CUMPLIMIENTO_VEND( :Pv_PrefijoEmpresa, ".
                                                                                                              ":Pv_TipoConsulta, ".
                                                                                                              ":Pv_CargoPersona, ".
                                                                                                              ":Pv_Tipo, ".
                                                                                                              ":Pv_IdPersonaEmpresaRol, ".
                                                                                                              ":Pd_FechaInicio, ".
                                                                                                              ":Pv_EmailUsrSesion ); END;";
                $objStmt                    = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":Pv_PrefijoEmpresa",      $strPrefijoEmpresa);
                oci_bind_by_name($objStmt, ":Pv_TipoConsulta",        $strTipoConsulta);
                oci_bind_by_name($objStmt, ":Pv_CargoPersona",        $strTipoPersonal);
                oci_bind_by_name($objStmt, ":Pv_Tipo",                $strTipo);
                oci_bind_by_name($objStmt, ":Pv_IdPersonaEmpresaRol", $intIdPersonEmpresaRol);
                oci_bind_by_name($objStmt, ":Pd_FechaInicio",         $strFechaInicio);
                oci_bind_by_name($objStmt, ":Pv_EmailUsrSesion",      $strEmailUsrSession);
                oci_execute($objStmt);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información de facturacion de asesor. - Prefijo('.
                                     $strPrefijoEmpresa.'), FechaInicio('.$strFechaInicio.') Database('.
                                     $strDatabaseDsn.'), UsrComercial('.$strUserBiFinanciero.'), PassComercial('.$strPasswordBiFinanciero.').'); 
            }//( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strDatabaseDsn)
            //&& !empty($strUserBiFinanciero) && !empty($strPasswordBiFinanciero) )
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $strMensajeRespuesta;
    }

        
    /**
     * ejecutarFacturacionCancelacion
     *
     * Método que ejecuta la facturación por cancelación voluntaria.                             
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 10-09-2018
     * 
     */
    public function ejecutarFacturacionCancelacion($arrayParametros)
    {
        $strEstadoSolicitud   = $arrayParametros['strEstadoSolicitud'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $intMotivoId          = $arrayParametros['intMotivoId'];
        $strMsnError          = $arrayParametros['strMsnError'];
        $strEmpresaCod        = $arrayParametros['strEmpresaCod'];
        $strDescTipoSolicitud = $arrayParametros['strDescTipoSolicitud'];
        $strEstadoServicio    = 'Cancel';
        
        try
        {           
            $strSql  = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_GENERAR_FACTURAS_SOLICITUD( :strEstado,  "
                                                                                        . ":strDescTipoSolicitud,"
                                                                                        . ":strUsrCreacion, "
                                                                                        . ":intMotivoId, "
                                                                                        . ":strEmpresaCod, "
                                                                                        . ":strEstadoServicio, "
                                                                                        . ":strMsnError); END;";            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('strEstado' , $strEstadoSolicitud);
            $objStmt->bindParam('strDescTipoSolicitud' , $strDescTipoSolicitud);
            $objStmt->bindParam('strUsrCreacion' , $strUsrCreacion);
            $objStmt->bindParam('intMotivoId' , $intMotivoId);
            $objStmt->bindParam('strEmpresaCod' , $strEmpresaCod);
            $objStmt->bindParam('strEstadoServicio' , $strEstadoServicio);
            $objStmt->bindParam('strMsnError' , $strMsnError); 
            $objStmt->execute();
            
            $strRpta ='OK';
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar cancelacion voluntaria: ". $ex->getMessage());
            $strRpta = 'Error';
        }

        return $strRpta;
    }
    
    /**
      * Documentación para el método 'hasNcAprobadas'.
      *
      * Método que verifica si una factura posee una nota de crédito en estado Aprobado
      *
      * @param  int  $intReferenciaDocId   Id de la factura a la que aplica la nota de crédito.
      *
      * @return bool $boolTieneNc          Bandera que indica si la factura con id enviado como parámetro posee notas de crédito aprobadas.
      * 
      * @author  Edgar Holguín <eholguín@telconet.ec>
      * @version 1.0 17-12-2018 
      */		
     public function hasNcAprobadas($intReferenciaDocId)
     {
         $boolTieneNcAprobadas = false;
         
         $objQuery = $this->_em->createQuery("  SELECT  idfc
                                                FROM 
                                                        schemaBundle:InfoDocumentoFinancieroCab  idfc,
                                                        schemaBundle:AdmiTipoDocumentoFinanciero atdf
                                                WHERE 
                                                        idfc.tipoDocumentoId       = atdf.id             AND
                                                        idfc.referenciaDocumentoId = :intReferenciaDocId AND 
                                                        idfc.estadoImpresionFact   = :strEstadoNc        AND
                                                        atdf.codigoTipoDocumento   = :strCodigoTipoDoc ");

         $objQuery->setParameter('intReferenciaDocId', $intReferenciaDocId);
         $objQuery->setParameter('strEstadoNc', 'Aprobada');
         $objQuery->setParameter('strCodigoTipoDoc', 'NC');

         if(count($objQuery->getResult()) > 0)
         {
             $boolTieneNcAprobadas = true;
         }
         
         return $boolTieneNcAprobadas;
         
     }
     
    /**
      * Documentación para el método 'getEstadoFacturaInstalacion'.
      *
      * Método que retorna el estado de la factura de instalación según los parametros requeridos
      *
      * @param  arrayParametros{
      *                          intPunto .- Id del punto que se desea consulta el estado de la factura
      *                        } 
      * costoQuery: 25 
      * @return string Estado de la factura.
      * 
      * @author  Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 23-04-2019 
      */
     public function getEstadoFacturaInstalacion($arrayParametros)
    {
        $strSql      =   "SELECT ESTADO_IMPRESION_FACT 
                          FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB 
                          WHERE PUNTO_ID = :intPuntoId 
                          AND TIPO_DOCUMENTO_ID IN (:arrayTipo)
                          AND USR_CREACION IN ('telcos_contrato','telcos_web')";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);
        $objQuery->setParameter("arrayTipo", $arrayParametros["arrayTipo"]);

        $objRsm->addScalarResult('ESTADO_IMPRESION_FACT', 'estado', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta[0]["estado"] ? $arrayRespuesta[0]["estado"] : "";
    }

     
    /**
      * Documentación para el método 'hasNcTotalAplicada'.
      *
      * Método que verifica si una factura posee una nota de crédito en estado aplicada por su valor total.
      *
      * @param  int  $intReferenciaDocId     Id de la factura a la que aplica la nota de crédito.
      *
      * @return bool $boolTieneNc          Bandera que indica si la factura con id enviado como parámetro posee notas de crédito aprobadas.
      * 
      * @author  Edgar Holguín <eholguín@telconet.ec>
      * @version 1.0 17-12-2018 
      */		
     public function hasNcTotalAplicada($intReferenciaDocId)
     {
         $boolTieneNcTotalAplicada = false;
         
         $objQuery = $this->_em->createQuery("  SELECT  idfc
                                                FROM 
                                                        schemaBundle:InfoDocumentoFinancieroCab  idfc,
                                                        schemaBundle:AdmiTipoDocumentoFinanciero atdf
                                                WHERE 
                                                        idfc.tipoDocumentoId       = atdf.id             AND
                                                        idfc.referenciaDocumentoId = :intReferenciaDocId AND 
                                                        idfc.estadoImpresionFact   = :strEstadoNc        AND
                                                        atdf.codigoTipoDocumento   = :strCodigoTipoDoc   AND 
                                                        idfc.valorTotal = (SELECT fact.valorTotal 
                                                                           FROM    schemaBundle:InfoDocumentoFinancieroCab  fact
                                                                           WHERE   fact.estadoImpresionFact   = :strEstadoFact  
                                                                           AND     fact.id                    = :intIdFactAplica)");

         $objQuery->setParameter('intReferenciaDocId', $intReferenciaDocId);
         $objQuery->setParameter('strEstadoNc', 'Activo');
         $objQuery->setParameter('strCodigoTipoDoc', 'NC');
         $objQuery->setParameter('strEstadoFact', 'Cerrado');
         $objQuery->setParameter('intIdFactAplica', $intReferenciaDocId);

         if(count($objQuery->getResult()) > 0)
         {
             $boolTieneNcTotalAplicada =  true;
         }
         
         return $boolTieneNcTotalAplicada;
     }
     

     /**
     * Documentación para el método 'getTipoCambio'.
     * 
     * Método que retorna el tipo de cambio dependiendo de la fecha del dia de la emision.
     *
     * @param  array $arrayParametros ['intIdDocumento'   => 'Código del documento',
     *                                  'strFechaEmision'  => 'Fecha de emisión del documento',
     *                                  'strUsrSession'    => 'Ususario en sesión']
     * 
     * @return array $arrayDatos      ['strCodError'      => 'Texto que indica si hubo error en el procesamiento' 
     *                                  'strMensaje'       => 'Texto que posee mensaje del procedimiento' ]
     *
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 06-03-2019
     */
    public function getTipoCambio($arrayParametros)
    {
        $arrayDatos      = array();
        $strMensaje      = str_repeat(' ', 32767);
        $strCodError     = str_repeat(' ', 32767);
         try
        {
            $intIdDocumento      = ( isset($arrayParametros['intIdDocumento']) && !empty($arrayParametros['intIdDocumento']) ) 
                                   ? $arrayParametros['intIdDocumento'] : '';
            $strFechaEmision     = ( isset($arrayParametros['strFechaEmision']) && !empty($arrayParametros['strFechaEmision']) ) 
                                   ? $arrayParametros['strFechaEmision'] : '';
            $strUsrSession       = ( isset($arrayParametros['strUsrSession']) && !empty($arrayParametros['strUsrSession']) ) 
                                   ? $arrayParametros['strUsrSession'] : '';
             
            if( !empty($intIdDocumento) && !empty($strFechaEmision) )
            {
                $strSql = "BEGIN
                            DB_FINANCIERO.FNKG_TIPO_CAMBIO.P_WS_TIPO_CAMBIO
                            (
                                :Pv_Id_Documento,
                                :Pv_Fecha_Emision,
                                :Pv_Usuario,
                                :strCodError,
                                :strMensaje
                            );
                           END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pv_Id_Documento' , $intIdDocumento );
                $objStmt->bindParam('Pv_Fecha_Emision', $strFechaEmision);
                $objStmt->bindParam('Pv_Usuario'      , $strUsrSession  );
                $objStmt->bindParam('strCodError'     , $strCodError    );
                $objStmt->bindParam('strMensaje'      , $strMensaje     );
                $objStmt->execute();
                
                
                if($strCodError!='OK')
                {
                     throw new \Exception("InfoDocumentoFinancieroCabRepository->consumeTipoCambio " . $strCodError .' '. $strMensaje);
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para obtener el tipo de cambio de la fecha de emisión. '.
                                     'DocumentoId('.$intIdDocumento.'), FechaEmision('.$strFechaEmision.')');
            }//( !empty($intIdDocumento) && !empty($strFechaEmision) )
            $arrayDatos['strCodError']= $strCodError;
            $arrayDatos['strMensaje'] = $strMensaje;
        
            

        }
        catch(\Exception $ex)
        {
             $arrayDatos['strCodError']= '503';
             $arrayDatos['strMensaje'] = $ex->getMessage();
        }
        
        return $arrayDatos;
        
    }
    
    /**
     * Documentación para findFacturasMensualesFiltroFecha
     * 
     * Función que se encarga de obtener las facturas mensuales automáticas correspondiente al proceso del escenario 1.
     * 
     * @param array $arrayParametros['strFiltroEscenario' : Filtro del Escenario1- Filtro Fecha
     *                               'intIdPunto'         : Id Punto
     *                               'strIdEmpresa'       : Id Empresa ]
     * 
     * @return array de Facturas.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.0 27-05-2020
     */
    public function findFacturasMensualesFiltroFecha($arrayParametros)
    {
       try
       {
            $intIdPunto          = $arrayParametros["intIdPunto"];
            $strFiltroEscenario  = $arrayParametros["strFiltroEscenario"];
            $strIdEmpresa        = $arrayParametros["strIdEmpresa"];
            $arrayFecha          = explode("/", $strFiltroEscenario);
            
            $objQuery = $this->_em->createQuery();

            $strQuery = "SELECT idfc
                    FROM 
                        schemaBundle:InfoDocumentoFinancieroCab idfc,
                        schemaBundle:AdmiTipoDocumentoFinanciero atd,
                        schemaBundle:InfoOficinaGrupo iog
                    WHERE
                        idfc.tipoDocumentoId     = atd.id  
                    AND idfc.oficinaId           = iog.id  
                    AND iog.empresaId            =:strIdEmpresa  
                    AND atd.codigoTipoDocumento  IN ('FAC')       
                    AND idfc.estadoImpresionFact IN ('Courier','Activo','Activa')  
                    AND idfc.puntoId             IN (:intIdPunto)  
                    AND idfc.recurrente          = 'S'             
                    AND idfc.esAutomatica        = 'S'            
                    AND idfc.usrCreacion         = 'telcos'       
                    AND idfc.feEmision           = '".date('Y/m/d', strtotime($arrayFecha[2] . "-" . $arrayFecha[1] . "-" . $arrayFecha[0]))."'
                    ORDER BY idfc.feEmision ASC ";      

            $objQuery->setParameter('intIdPunto', $intIdPunto);
            $objQuery->setParameter('strIdEmpresa', $strIdEmpresa);
            $objQuery->setDQL($strQuery);

            $arrayFacturas = $objQuery->getResult();

            return $arrayFacturas;
       } 
       catch (Exception $ex) 
       {
            return null;
       }    
               
    }
    
    
    /**
     * Documentación para findNdiDiferidasFiltroCuotas
     * 
     * Función que se encarga de obtener las NDI's Diferidas correspondiente al proceso del escenario 3, con filtro de cuotas.
     * 
     * @param array $arrayParametros['strFiltroEscenario' : Filtro del Escenario3- Número de Cuotas
     *                               'intIdPerEmpRol'     : Id PersonaEmpresaRol
     *                               'strIdEmpresa'       : Id Empresa ]
     * 
     * @return array de NDI Diferidas.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.0 23-06-2020
     * Costo Query: 35
     */
    public function findNdiDiferidasFiltroCuotas($arrayParametros)
    {
       try
       {
            $intIdPerEmpRol   = $arrayParametros["intIdPerEmpRol"];
            $intNumCuotasNdi  = (int)$arrayParametros["strFiltroEscenario"];
            $strIdEmpresa     = $arrayParametros["strIdEmpresa"];
                            
            $objRsm       = new ResultSetMappingBuilder($this->_em);
            $objQuery     = $this->_em->createNativeQuery(null, $objRsm);

            $strSelectNdi = "SELECT IDFC.ID_DOCUMENTO, IDFC.OFICINA_ID, IDFC.PUNTO_ID, IDFC.TIPO_DOCUMENTO_ID, IDFC.NUMERO_FACTURA_SRI,
                               IDFC.SUBTOTAL, IDFC.VALOR_TOTAL, IDFC.ESTADO_IMPRESION_FACT, IDFC.FE_CREACION, IDFC.FE_EMISION, IDFC.USR_CREACION ";

            $strFromNdi   = "FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC, 
                                  DB_COMERCIAL.INFO_PUNTO IP, 
                                  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER, 
                                  DB_COMERCIAL.INFO_OFICINA_GRUPO IOG, 
                                  DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF, 
                                  DB_COMERCIAL.ADMI_CARACTERISTICA AC, 
                                  DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC "; 

            $strWhereNdi  = "WHERE IDFC.PUNTO_ID                 = IP.ID_PUNTO 
                               AND IP.PERSONA_EMPRESA_ROL_ID     = IPER.ID_PERSONA_ROL 
                               AND IPER.ID_PERSONA_ROL           = :intIdPerEmpRol
                               AND IDFC.OFICINA_ID               = IOG.ID_OFICINA
                               AND IOG.EMPRESA_ID                = :strCodEmpresa
                               AND IDFC.TIPO_DOCUMENTO_ID        = ATDF.ID_TIPO_DOCUMENTO
                               AND ATDF.CODIGO_TIPO_DOCUMENTO    = :strTipoDocumentoNdi 
                               AND IDFC.ESTADO_IMPRESION_FACT    = :strEstado 
                               AND IDFC.USR_CREACION             = :strUsrCreacion 
                               AND IDC.DOCUMENTO_ID              = IDFC.ID_DOCUMENTO
                               AND IDC.CARACTERISTICA_ID         = AC.ID_CARACTERISTICA   
                               AND AC.DESCRIPCION_CARACTERISTICA = :strCaracteristicaNdi 
                               AND IDC.VALOR                     = :strValorCaracteristicaNdi 
                               ORDER BY IDFC.FE_CREACION ASC, IDFC.ID_DOCUMENTO ASC ";

            $objQuery->setParameter('strCodEmpresa', $strIdEmpresa);
            $objQuery->setParameter('intIdPerEmpRol',   $intIdPerEmpRol);
            $objQuery->setParameter('strTipoDocumentoNdi', 'NDI');
            $objQuery->setParameter('strCaracteristicaNdi', 'PROCESO_DIFERIDO');
            $objQuery->setParameter('strValorCaracteristicaNdi', 'S');
            $objQuery->setParameter('strEstado', 'Activo');
            $objQuery->setParameter('strUsrCreacion', 'telcos_diferido');

            $strSqlNdi      = $strSelectNdi.$strFromNdi.$strWhereNdi;

            $strSelectTable = "SELECT TBL_TEMP_IDFC.ID_DOCUMENTO, TBL_TEMP_IDFC.OFICINA_ID, TBL_TEMP_IDFC.PUNTO_ID, TBL_TEMP_IDFC.TIPO_DOCUMENTO_ID,
                                      TBL_TEMP_IDFC.NUMERO_FACTURA_SRI, TBL_TEMP_IDFC.SUBTOTAL, TBL_TEMP_IDFC.VALOR_TOTAL, 
                                      TBL_TEMP_IDFC.ESTADO_IMPRESION_FACT, TBL_TEMP_IDFC.FE_CREACION, TBL_TEMP_IDFC.FE_EMISION,
                                      TBL_TEMP_IDFC.USR_CREACION FROM ( " . $strSqlNdi .") TBL_TEMP_IDFC WHERE ROWNUM <= :intNumCuotasNDI ";
               
            $objQuery->setParameter('intNumCuotasNDI', $intNumCuotasNdi);
                
            $objRsm->addEntityResult('telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab', 'IDFC'); 
            $objRsm->addFieldResult('IDFC', 'ID_DOCUMENTO', 'id');
            $objRsm->addFieldResult('IDFC', 'OFICINA_ID', 'oficinaId');
            $objRsm->addFieldResult('IDFC', 'PUNTO_ID', 'puntoId');
            $objRsm->addJoinedEntityResult('telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero' , 'ATDF', 'IDFC', 'tipoDocumentoId');
            $objRsm->addFieldResult('ATDF', 'TIPO_DOCUMENTO_ID', 'id');
            $objRsm->addFieldResult('IDFC', 'NUMERO_FACTURA_SRI', 'numeroFacturaSri');
            $objRsm->addFieldResult('IDFC', 'SUBTOTAL', 'subtotal');
            $objRsm->addFieldResult('IDFC', 'VALOR_TOTAL', 'valorTotal');
            $objRsm->addFieldResult('IDFC', 'ESTADO_IMPRESION_FACT', 'estadoImpresionFact');
            $objRsm->addFieldResult('IDFC', 'FE_CREACION', 'feCreacion');
            $objRsm->addFieldResult('IDFC', 'FE_EMISION', 'feEmision');
            $objRsm->addFieldResult('IDFC', 'USR_CREACION', 'usrCreacion');

            $objQuery->setSQL($strSelectTable);
            $arrayDocFinNdi = $objQuery->getResult();

            return $arrayDocFinNdi;   
       } 
       catch (Exception $ex) 
       {
            return null;
       }    
               
    }
    
    /**
     * Documentación para findNdiDiferidas
     * 
     * Función que se encarga de obtener las NDI's Diferidas correspondiente al proceso del escenario 3.
     * 
     * @param array $arrayParametros['intIdPerEmpRol' : Id PersonaEmpresaRol
     *                               'strIdEmpresa'   : Id Empresa ]
     * 
     * @return array de NDI Diferidas.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.0 23-06-2020
     * Costo Query: 35
     */
    public function findNdiDiferidas($arrayParametros)
    {
       try
       {
            $intIdPerEmpRol = $arrayParametros["intIdPerEmpRol"];
            $strIdEmpresa   = $arrayParametros["strIdEmpresa"];
            
            $objQuery = $this->_em->createQuery();

            $strQuery = " SELECT idfc
                            FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc, 
                                schemaBundle:InfoPunto ip, 
                                schemaBundle:InfoPersonaEmpresaRol iper, 
                                schemaBundle:InfoOficinaGrupo iog, 
                                schemaBundle:AdmiTipoDocumentoFinanciero atd, 
                                schemaBundle:AdmiCaracteristica ac, 
                                schemaBundle:InfoDocumentoCaracteristica idc    
                            WHERE 
                                   idfc.puntoId                  = ip.id  
                               AND ip.personaEmpresaRolId        = iper.id 
                               AND iper.id                       = :intIdPerEmpRol 
                               AND idfc.oficinaId                = iog.id 
                               AND iog.empresaId                 = :strIdEmpresa 
                               AND idfc.tipoDocumentoId          = atd.id 
                               AND atd.codigoTipoDocumento       = :strTipoDocumentoNdi 
                               AND idfc.estadoImpresionFact      = :strEstado   
                               AND idfc.usrCreacion              = :strUsrCreacion 
                               AND idc.documentoId               = idfc.id 
                               AND idc.caracteristicaId          = ac.id 
                               AND ac.descripcionCaracteristica  = :strCaracteristicaNdi 
                               AND idc.valor                     = :strValorCaracteristicaNdi 
                               ORDER BY idfc.feCreacion ASC, idfc.id ASC ";

            $objQuery->setParameter('intIdPerEmpRol',   $intIdPerEmpRol);
            $objQuery->setParameter('strTipoDocumentoNdi', 'NDI');
            $objQuery->setParameter('strCaracteristicaNdi', 'PROCESO_DIFERIDO');
            $objQuery->setParameter('strValorCaracteristicaNdi', 'S');
            $objQuery->setParameter('strEstado', 'Activo');
            $objQuery->setParameter('strUsrCreacion', 'telcos_diferido');
            $objQuery->setParameter('strIdEmpresa', $strIdEmpresa);
            $objQuery->setDQL($strQuery);

            $arrayDocFinNdi = $objQuery->getResult();

            return $arrayDocFinNdi;
       } 
       catch (Exception $ex) 
       {
            return null;
       }    
               
    }
        
      /*
      * Documentación para el método 'getParametroFormaPago'.
      * Costo = 0,18
      * Método que retorna el porcentaje de descuento en la factura de instalación, según la forma de pago y la última milla para poder habilitar la opción de la aprobación del contrato.
      *
      * @return int $intPorcentaje Porcentaje de descuento en la factura de instalación.
      * 
      * @author  Josselhin Moreira <kjmoreira@telconet.ec>
      * @version 1.0 22-03-2019 
      */
     public function getParametroFormaPago($arrayParametros)
     {
        $intIdContrato = $arrayParametros['intIdContrato'];
        error_log($intIdContrato);
        
        $strSql = " SELECT PD.VALOR3 AS PORCENTAJE FROM DB_GENERAL.ADMI_PARAMETRO_DET PD
                    WHERE PD.PARAMETRO_ID IN (SELECT  PC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB PC
                                              WHERE PC.NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                                              AND PC.ESTADO = 'Activo')
                    AND PD.VALOR1 IN (  SELECT ATMO.CODIGO_TIPO_MEDIO FROM DB_COMERCIAL.ADMI_TIPO_MEDIO ATMO
                                        WHERE ATMO.ID_TIPO_MEDIO IN(SELECT IST.ULTIMA_MILLA_ID FROM DB_SOPORTE.INFO_SERVICIO_TECNICO IST
                                                                    WHERE IST.SERVICIO_ID  IN(  SELECT IOS.ID_SERVICIO FROM DB_COMERCIAL.INFO_SERVICIO IOS
                                                                                                WHERE IOS.PUNTO_ID IN(  SELECT IP.ID_PUNTO FROM DB_COMERCIAL.INFO_PUNTO IP
                                                                                                                        WHERE IP.PERSONA_EMPRESA_ROL_ID IN( SELECT PERSONA_EMPRESA_ROL_ID FROM DB_COMERCIAL.INFO_CONTRATO IC
                                                                                                                                                            WHERE IC.ID_CONTRATO = :intIdContrato)
                                                                                                                        AND IP.ESTADO = 'Activo')
                                                                                                AND IOS.ESTADO = 'Factible'))
                                        AND ATMO.ESTADO = 'Activo')    
                    AND PD.VALOR2 IN(SELECT TCTA.DESCRIPCION_CUENTA FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BTC , DB_GENERAL.ADMI_BANCO BCO , DB_GENERAL.ADMI_TIPO_CUENTA TCTA
                                     WHERE BCO.ID_BANCO = BTC.BANCO_ID
                                     AND TCTA.ID_TIPO_CUENTA = BTC.TIPO_CUENTA_ID
                                     AND BTC.ID_BANCO_TIPO_CUENTA IN(SELECT CFP.BANCO_TIPO_CUENTA_ID  FROM DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO CFP
                                                                    WHERE CFP.CONTRATO_ID = :intIdContrato
                                                                    AND CFP.ESTADO = 'Activo')
                                     AND BTC.ESTADO='Activo')
                    AND PD.ESTADO = 'Activo'";
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter('intIdContrato', $arrayParametros['intIdContrato']);
        $objRsm->addScalarResult('PORCENTAJE','porcentaje','string');
        
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
     }
     
    /**
     * generarFacturacionSolicitud
     *
     * Método que ejecuta la facturación por solicitud detallada.                             
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 10-09-2018
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 04-09-2019 Se agrega validación para setear variable de estado en caso de recibir estado del servicio como parámetro.
     */
    public function generarFacturacionSolicitud($arrayParametros)
    {
        $strEstadoSolicitud   = $arrayParametros['strEstadoSolicitud'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $intMotivoId          = $arrayParametros['intMotivoId'];
        $strMsnError          = $arrayParametros['strMsnError'];
        $strEmpresaCod        = $arrayParametros['strEmpresaCod'];
        $strDescTipoSolicitud = $arrayParametros['strDescTipoSolicitud'];
        
        if( isset($arrayParametros['strEstadoServicio']) && !empty($arrayParametros['strEstadoServicio']))
        {
            $strEstadoServicio = $arrayParametros['strEstadoServicio'];
        }
        else
        {
            $strEstadoServicio = 'Cancel';
        }
        
        try
        {           
            $strSql  = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_GENERAR_FACTURAS_SOLICITUD( :strEstado,  "
                                                                                        . ":strDescTipoSolicitud,"
                                                                                        . ":strUsrCreacion, "
                                                                                        . ":intMotivoId, "
                                                                                        . ":strEmpresaCod, "
                                                                                        . ":strEstadoServicio, "
                                                                                        . ":strMsnError); END;";            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('strEstado' , $strEstadoSolicitud);
            $objStmt->bindParam('strDescTipoSolicitud' , $strDescTipoSolicitud);
            $objStmt->bindParam('strUsrCreacion' , $strUsrCreacion);
            $objStmt->bindParam('intMotivoId' , $intMotivoId);
            $objStmt->bindParam('strEmpresaCod' , $strEmpresaCod);
            $objStmt->bindParam('strEstadoServicio' , $strEstadoServicio);
            $objStmt->bindParam('strMsnError' , $strMsnError); 
            $objStmt->execute();
            
            $strRpta ='OK';
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al generar facturación por solicitud: ". $ex->getMessage());
            $strRpta = 'Error';
        }

        return $strRpta;
    }
    
    /**
     * marcarFacturasCaracteristicaPtoId
     *
     * Método que ejecuta procedimiento que agrega característica a los documentos con punto id enviado como parámetro.                             
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 08-09-2019
     */
    public function marcarFacturasCaracteristicaPtoId($arrayParametros)
    {
        $intPuntoId           = $arrayParametros['intPuntoId'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $intCaracteristicaId  = $arrayParametros['intCaracteristicaId'];
        $strMsnError          = $arrayParametros['strMsnError'];

        try
        {
                
            $strSql  = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_MARCAR_FACTURAS_PUNTO( :Pn_PuntoId,  "
                                                                                   . ":Pn_CaracteristicaId, "
                                                                                   . ":Pv_UsrCreacion, "
                                                                                   . ":Pv_Mensaje); END;";            
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_PuntoId' , $intPuntoId);
            $objStmt->bindParam('Pn_CaracteristicaId' , $intCaracteristicaId);
            $objStmt->bindParam('Pv_UsrCreacion' , $strUsrCreacion);
            $objStmt->bindParam('Pv_Mensaje' , $strMsnError); 
            $objStmt->execute();
            
            $strRpta ='OK';
        } 
        catch (\Exception $ex) 
        {
            error_log("Error en InfoDocumentoFinancieroCabRepository.marcarFacturasCaracteristicaPtoId: ". $ex->getMessage());
            $strRpta = 'Error';
        }

        return $strRpta;
    }
    
    /**
     * Documentación getSaldoPorVencerNDI, función encargada de obtener el saldo pendiente de NDI por generarse.
     * 
     * @param array $arrayParametros ['intPuntoId' punto id del servicio
     *                               ]
     * @return int $fltSaldoNDI Saldo pendiente de NDI por punto.
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0, 22-06-2020
     */
    public function getSaldoPorVencerNDI($arrayParametros)
    {
        $fltSaldoNDI = 0;
        try
        {
            $fltSaldoNDI = str_pad($fltSaldoNDI, 50, " ");
            $strSql      = "BEGIN :fltSaldoNDI := " . 
                           "DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_SALDO_X_DIFERIR_PTO(:intPuntoId); END;";
            $objStmt     = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('intPuntoId',        $arrayParametros['intPuntoId']);
            $objStmt->bindParam('fltSaldoNDI',       $fltSaldoNDI);
            $objStmt->execute();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            $fltSaldoNDI = 0;
        }
        
        return $fltSaldoNDI;
    }
    
    /**
     * ejecutarNDICancelacion, invoca al proceso de generación de NDI diferidas por cancelación voluntaria.
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0, 22-06-2020
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 18-08-2020 - Se añade parámetro Tipo de Proceso, para identificar el proceso de PreCancelación de Deuda Diferida.
     * 
     */
    public function ejecutarNDICancelacion($arrayParametros)
    {
        $intIdServicio   = $arrayParametros['intIdServicio'];
        $strEmpresaCod   = $arrayParametros['strEmpresaCod'];
        $strTipoProceso  = $arrayParametros['strTipoProceso'];
        $strMsnError     = $arrayParametros['strMsnError'];
        
        try
        {
            $strSql  = "BEGIN DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_GENERAR_NDI_CANCELACION( :intIdServicio, "
                                                                                              ." :strEmpresaCod, "
                                                                                              ." :strTipoProceso, "
                                                                                              ." :strMsnError); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('intIdServicio' , $intIdServicio);
            $objStmt->bindParam('strEmpresaCod' , $strEmpresaCod);
            $objStmt->bindParam('strTipoProceso' , $strTipoProceso);
            $objStmt->bindParam('strMsnError' , $strMsnError); 
            $objStmt->execute();
            
            $strRpta ='OK';
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar las NDI por cancelacion voluntaria: ". $ex->getMessage());
            $strRpta = 'Error';
        }

        return $strRpta;
    }
    
    /*
     * Documentación para el método 'getCumpleEstadosDiferido'.
     *
     * Método que retorna un array (un valor entero "1" si el servicio mandatorio del punto cumple con los estados
     * "0" no cumple, un string de los estados parametrizados)
     *
     * @param  arrayParametros [intPunto .- Id del punto que se desea consulta el estado de la factura
     *                         ]
     * costoQuery: 10 
     * 
     * @author  José Candelario <jcandelario@telconet.ec>
     * @version 1.0 28-06-2020 
     */
     public function getCumpleEstadosDiferido($arrayParametros)
    {
        $strSql      =   "  SELECT TABLA1.VALOR,
                            (SELECT DISTINCT LISTAGG(CODIGO, ',') 
                             WITHIN GROUP (ORDER BY CODIGO) OVER (PARTITION BY GRUPO) AS ESTADOS
                             FROM 
                            (SELECT PD.VALOR1 AS CODIGO,'1' AS GRUPO
                             FROM DB_GENERAL.ADMI_PARAMETRO_DET PD,
                               DB_GENERAL.ADMI_PARAMETRO_CAB PC
                             WHERE PC.ID_PARAMETRO   = PD.PARAMETRO_ID
                             AND PC.NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
                             AND PC.ESTADO           = 'Activo'
                             AND PD.ESTADO           = 'Activo'
                             AND PD.DESCRIPCION      = 'ESTADOS_SERVICIO')TABLA) AS VALORES
                           FROM 
                           (SELECT COUNT(DBIS.ID_SERVICIO) AS VALOR
                            FROM DB_COMERCIAL.INFO_PUNTO DBIP, 
                              DB_COMERCIAL.INFO_SERVICIO DBIS,
                              DB_COMERCIAL.ADMI_PRODUCTO DBAP,
                              DB_COMERCIAL.INFO_PLAN_DET DBIPD
                            WHERE DBIP.ID_PUNTO      = :intPuntoId
                            AND DBIS.PUNTO_ID        = DBIP.ID_PUNTO
                            AND DBIPD.PLAN_ID        = DBIS.PLAN_ID
                            AND DBAP.ID_PRODUCTO     = DBIPD.PRODUCTO_ID
                            AND DBAP.CODIGO_PRODUCTO = 'INTD'
                            AND UPPER(DBIS.ESTADO)   IN (SELECT UPPER(PD.VALOR1)
                                                         FROM DB_GENERAL.ADMI_PARAMETRO_DET PD,
                                                           DB_GENERAL.ADMI_PARAMETRO_CAB PC
                                                         WHERE PC.ID_PARAMETRO   = PD.PARAMETRO_ID
                                                         AND PC.NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
                                                         AND PC.ESTADO           = 'Activo'
                                                         AND PD.ESTADO           = 'Activo'
                                                         AND PD.DESCRIPCION      = 'ESTADOS_SERVICIO')) TABLA1 ";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);

        $objRsm->addScalarResult('VALOR', 'valor', 'integer');
        $objRsm->addScalarResult('VALORES', 'valores', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta;
    }
    
    /*
     * Documentación para el método 'getCumpleFormaPagoDiferido'.
     *
     * Método que retorna un array (un valor entero "1" si el servicio mandatorio del punto cumple con las formas de pago
     * "0" no cumple, un string de las formas de pagos parametrizadas)
     *
     * @param  arrayParametros [intPunto .- Id del punto que se desea consulta el estado de la factura
     *                         ]
     * costoQuery: 8
     * 
     * @author  José Candelario <jcandelario@telconet.ec>
     * @version 1.0 28-06-2020 
     */
     public function getCumpleFormaPagoDiferido($arrayParametros)
    {
        $strSql      =   "  SELECT TABLA1.VALOR,
                            (SELECT PD.VALOR2
                            FROM DB_GENERAL.ADMI_PARAMETRO_DET PD,
                              DB_GENERAL.ADMI_PARAMETRO_CAB PC
                            WHERE PC.ID_PARAMETRO   = PD.PARAMETRO_ID
                            AND PC.NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
                            AND PC.ESTADO           = 'Activo'
                            AND PD.ESTADO           = 'Activo'
                            AND PD.DESCRIPCION      = 'VALOR_FORMAS_DE_PAGO') AS VALORES
                            FROM 
                            (SELECT COUNT(CONT.ID_CONTRATO) AS VALOR 
                            FROM DB_COMERCIAL.INFO_CONTRATO CONT
                            WHERE CONT.ID_CONTRATO IN (SELECT MAX (DBIC.ID_CONTRATO) 
                                                       FROM DB_COMERCIAL.INFO_CONTRATO DBIC,
                                                         DB_COMERCIAL.INFO_PUNTO DBIP
                                                       WHERE DBIP.ID_PUNTO      = :intPuntoId
                                                       AND DBIC.PERSONA_EMPRESA_ROL_ID = DBIP.PERSONA_EMPRESA_ROL_ID)
                            AND CONT.FORMA_PAGO_ID IN (SELECT REGEXP_SUBSTR (TABLA.VALOR1,'[^,]+',1, LEVEL) VALOR 
                                                       FROM DUAL, 
                                                       (SELECT PD.VALOR1
                                                         FROM DB_GENERAL.ADMI_PARAMETRO_DET PD,
                                                           DB_GENERAL.ADMI_PARAMETRO_CAB PC
                                                         WHERE PC.ID_PARAMETRO   = PD.PARAMETRO_ID
                                                         AND PC.NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
                                                         AND PC.ESTADO           = 'Activo'
                                                         AND PD.ESTADO           = 'Activo'
                                                         AND PD.DESCRIPCION      = 'VALOR_FORMAS_DE_PAGO') TABLA
                                                       CONNECT BY REGEXP_SUBSTR (TABLA.VALOR1,'[^,]+',1, LEVEL) IS NOT NULL)) TABLA1 ";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);

        $objRsm->addScalarResult('VALOR', 'valor', 'integer');
        $objRsm->addScalarResult('VALORES', 'valores', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta;
    }
    
    
    /**
     * Documentación para el método 'getTotalNDI'.
     *
     * Función que retorna el valor total de notas de débito asociadas a pagos y anticipos con el idDetPagAut enviado como parámetro.
     *
     * Costo: 9
     * 
     * @param $intDetallePagoAutomaticoId Id del detalle de estado de cuenta.
     * 
     * @return $floatTotaldi
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 19-10-2020 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 10-06-2021 Se agrega condición usada en pagos por retención.
     */
    public function getTotalNDI($intDetallePagoAutomaticoId)
    {
        
        $strSql =   "SELECT SUM(IDFC.VALOR_TOTAL) AS TOTAL
                         FROM INFO_DOCUMENTO_FINANCIERO_CAB  IDFC
                         JOIN INFO_DOCUMENTO_FINANCIERO_DET  IDFD  ON IDFC.ID_DOCUMENTO = IDFD.DOCUMENTO_ID
                         JOIN INFO_PAGO_DET                  IPD   ON IPD.ID_PAGO_DET   = IDFD.PAGO_DET_ID
                         JOIN INFO_PAGO_CAB                  IPC   ON IPC.ID_PAGO  = IPD.PAGO_ID
                         JOIN ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF  ON ATDF.ID_TIPO_DOCUMENTO = IDFC.TIPO_DOCUMENTO_ID
                         WHERE ATDF.CODIGO_TIPO_DOCUMENTO    IN ('NDI') AND 
                         (IPC.DETALLE_PAGO_AUTOMATICO_ID      = :intDetallePagoAutomaticoId OR 
                          IPD.REFERENCIA_DET_PAGO_AUT_ID      = :intDetallePagoAutomaticId)
                         ORDER BY IDFC.FE_CREACION DESC ";  
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intDetallePagoAutomaticoId", $intDetallePagoAutomaticoId);
        $objQuery->setParameter("intDetallePagoAutomaticId", $intDetallePagoAutomaticoId);
        $objRsm->addScalarResult('TOTAL', 'totalNdi', 'integer');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        $floatTotaldi = (!empty($arrayRespuesta[0]['totalNdi']) ? $arrayRespuesta[0]['totalNdi'] : 0 );            
            
        return floatval($floatTotaldi);
    }

    /*
     * Documentación para el método 'creaNotaCredito'.
     *
     * Función que crea una nota de crédito por el proceso de reubicación.
     *
     * @param  arrayParametrosIn [intIdDocumento'        => Recibe el id del documento de factura, 
                                 'intTipoDocumentoId'    => Recibe el id tipo documento,
                                 'intIdMotivo'           => Recibe el id motivo de nota de crédito,
                                 'strValorOriginal'      => Recibe un Y o N para hacer la NC por valor original,
                                 'strPorcentajeServicio' => Recibe el porcentaje,
                                 'intPorcentaje'         => Recibe el valor del porcentaje,
                                 'intIdOficina'          => Recibe el id Oficina, 
                                 'intIdEmpresa'          => Recibe el id Empresa
     *                          ]
     * 
     * @author  Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 30-10-2020 
     */
    public function creaNotaCredito($arrayParametrosIn)
    {
        try
        {
            $arrayParametrosOut['intIdDocumentoNC']       = str_pad($arrayParametrosOut['intIdDocumentoNC'], 1000, " ");
            $arrayParametrosOut['strMessageError']        = str_pad($arrayParametrosOut['strMessageError'], 1000, " ");

            $strSQLCreaNotaCredito = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_CREA_NOTA_CREDITO_REUB(:intIdDocumento, "  
                                                                            . ":intTipoDocumentoId, "  
                                                                            . ":intIdMotivo, "  
                                                                            . ":strValorOriginal, "  
                                                                            . ":strPorcentajeServicio, "  
                                                                            . ":intPorcentaje, "  
                                                                            . ":intIdOficina, "  
                                                                            . ":intIdEmpresa, "  
                                                                            . ":intIdDocumentoNC, "  
                                                                            . ":strMessageError); END;";  
                      
            
            $objStmt = $this->_em->getConnection()->prepare($strSQLCreaNotaCredito);
            $objStmt->bindParam('intIdDocumento',       intval($arrayParametrosIn['intIdDocumento']));
            $objStmt->bindParam('intTipoDocumentoId',   $arrayParametrosIn['intTipoDocumentoId']);
            $objStmt->bindParam('intIdMotivo',          intval($arrayParametrosIn['intIdMotivo']));
            $objStmt->bindParam('strValorOriginal',     $arrayParametrosIn['strValorOriginal']);
            $objStmt->bindParam('strPorcentajeServicio',$arrayParametrosIn['strPorcentajeServicio']);
            $objStmt->bindParam('intPorcentaje',        intval($arrayParametrosIn['intPorcentaje']));
            $objStmt->bindParam('intIdOficina',         intval($arrayParametrosIn['intIdOficina']));
            $objStmt->bindParam('intIdEmpresa',         $arrayParametrosIn['intIdEmpresa']); 
            $objStmt->bindParam('intIdDocumentoNC',     $arrayParametrosOut['intIdDocumentoNC']);
            $objStmt->bindParam('strMessageError',      $arrayParametrosOut['strMessageError']);
            $objStmt->execute();
        } 
        catch (\Exception $ex) 
        {
            $arrayParametrosOut['strMessageError'] = 'Existio un error';
        }

        return $arrayParametrosOut;
    }
    
    /*
     * Documentación para el método 'numeraNotaCredito'.
     *
     * Función que numera una nota de crédito por el proceso de Reubicación.
     *
     * @param  arrayParametrosIn ['intIdDocumento'    => Recibe el id del documento de nota de crédito,
                                  'strPrefijoEmpresa' => Recibe el prefijo de la empresa,
                                  'strObsHistorial'   => Recibe observación para el historial de la numeración,
                                  'strUsrCreacion'    => Recibe el usuario de creación
     *                          ]
     * 
     * @author  Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 30-10-2020 
     */
    public function numeraNotaCredito($arrayParametrosIn)
    {
        try
        {           
            $arrayParametrosOut['strMsnError'] = str_pad($arrayParametrosOut['strMessageError'], 1000, " ");

            $strSQLCreaNotaCredito = "BEGIN DB_FINANCIERO.FNCK_TRANSACTION.P_NUMERA_NOTA_CREDITO(:intIdDocumento, " 
                                                                                              . ":strPrefijoEmpresa, " 
                                                                                              . ":strObsHistorial, " 
                                                                                              . ":strUsrCreacion, " 
                                                                                              . ":strMsnError); END;";
                      
            
            $objStmt = $this->_em->getConnection()->prepare($strSQLCreaNotaCredito);
            $objStmt->bindParam('intIdDocumento',    intval($arrayParametrosIn['intIdDocumento'])); 
            $objStmt->bindParam('strPrefijoEmpresa', $arrayParametrosIn['strPrefijoEmpresa']); 
            $objStmt->bindParam('strObsHistorial',   $arrayParametrosIn['strObsHistorial']); 
            $objStmt->bindParam('strUsrCreacion',    $arrayParametrosIn['strUsrCreacion']); 
            $objStmt->bindParam('strMsnError',       $arrayParametrosOut['strMsnError']); 
            $objStmt->execute();
        } 
        catch (\Exception $ex) 
        {
            $arrayParametrosOut['strMessageError'] = 'Existio un error';
        }

        return $arrayParametrosOut;
    }
    

     /*
     * Función encargada de validar que la Factura de Instalación de los servicios que se tengan en el Punto se encuentre Pagada, es decir, 
     * la factura de Instalación Cerrada con Pago asociado sin NC y sin anticipo y sin documento DEV asociado.
     *
     * costo 69
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-03-2020
     *
     * @param  Array $arrayParametros [
     *                                  intIdPunto          : Id del Punto,
     *                                  arrayEstadosPagos   : Estados de Pagos definidos Cerrado, Activo.
     *                                  strEstadoActivo     : Estado Activo
     *                                  strEstadoEliminado  : Estado Eliminado
     *                                  arrayEstadoFactura  : Estados de la Factura Pendiente, Activo, Cerrado
     *                                  arrayTipoDocumento  : FACP, FAC
     *                                  strNombreParametro  : Parámetro para definir el tipo de Factura de Instalación WEB O MOVIL
     *                                  strValor            : Define el valor de la característica para definir que es Fact de Instalación.
     *                                  strPagada           : Recibe valor N, para obtener Facturas no pagadas con Pago
     * 
     *                                ]
     * @return Array $arrayRespuesta
     */
    public function getDocumentoDevolucion($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSqlSelect =  "SELECT TABLA.* ".
                               "FROM ".
                                   "( ".
                                       "SELECT  NVL(DB_FINANCIERO.FNCK_CONSULTS.F_GET_DIFERENCIAS_FECHAS( ".
                                                   "TO_CHAR(IDFCAB.FE_EMISION,'DD-MM-RRRR'), ".
                                                   "TO_CHAR(SYSDATE,'DD-MM-RRRR')),0 ".
                                               ") AS DIAS, ".                                               
                                               "IDFCAB.ID_DOCUMENTO AS ID_DOCUMENTO, ".
                                               "IDFCAB.NUMERO_FACTURA_SRI AS NUMERO_FACTURA, ".
                                               "CASE WHEN IDFCAB.VALOR_TOTAL <= (SELECT NVL(SUM(PD.VALOR_PAGO), 0) ".
                                                       " FROM DB_FINANCIERO.INFO_PAGO_DET PD,  ".
                                                       " DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB FAC ".
                                                       " WHERE FAC.ID_DOCUMENTO = IDFCAB.ID_DOCUMENTO  ".                     
                                                       " AND PD.REFERENCIA_ID   = FAC.ID_DOCUMENTO ".
                                                       " AND PD.ESTADO          IN (:arrayEstadosPagos) ".
                                                       " AND NOT EXISTS (SELECT 1 FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB NCCAB, ".
                                                       " DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB NDCAB, ".
                                                       " DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET NDDET, ".
                                                       " DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOCNDI, ".
                                                       " DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOCDEV, ".
                                                       " DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOCANTC, ".
                                                       " DB_FINANCIERO.INFO_PAGO_CAB ANTCCAB, ".
                                                       " DB_FINANCIERO.INFO_PAGO_DET ANTCDET, ".
                                                       " DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB DEVCAB, ".
                                                       " DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DEVDET ".
                                                       " WHERE  ".
                                                       //NDI
                                                       " NDDET.PAGO_DET_ID                  = PD.ID_PAGO_DET  ".
                                                       " AND NDDET.DOCUMENTO_ID             = NDCAB.ID_DOCUMENTO   ".
                                                       " AND NDCAB.TIPO_DOCUMENTO_ID        = TDOCNDI.ID_TIPO_DOCUMENTO  ".
                                                       " AND TDOCNDI.CODIGO_TIPO_DOCUMENTO  = :strCodigoTipoDocNdi ".
                                                       " AND NDCAB.ESTADO_IMPRESION_FACT    IN (:arrayEstadosPagos)   ".             
                                                      //NC                
                                                      " AND NCCAB.REFERENCIA_DOCUMENTO_ID  = FAC.ID_DOCUMENTO  ".
                                                      " AND NCCAB.ESTADO_IMPRESION_FACT    = :strEstadoActivo  ".
                                                      //ANTC Y DEV
                                                      " AND REGEXP_LIKE(ANTCCAB.COMENTARIO_PAGO, NCCAB.NUMERO_FACTURA_SRI) ".
                                                      " AND ANTCCAB.PUNTO_ID               = NCCAB.PUNTO_ID ".
                                                      " AND ANTCCAB.TIPO_DOCUMENTO_ID      = TDOCANTC.ID_TIPO_DOCUMENTO ".
                                                      " AND TDOCANTC.CODIGO_TIPO_DOCUMENTO = :strCodigoTipoDocAntc ".
                                                      " AND ANTCCAB.ESTADO_PAGO            IN (:arrayEstadosPagos) ".
                                                      " AND ANTCCAB.ID_PAGO                = ANTCDET.PAGO_ID ".
                                                      " AND ANTCDET.ID_PAGO_DET            = DEVDET.PAGO_DET_ID ".
                                                      " AND DEVCAB.ID_DOCUMENTO            = DEVDET.DOCUMENTO_ID ".
                                                      " AND DEVCAB.TIPO_DOCUMENTO_ID       = TDOCDEV.ID_TIPO_DOCUMENTO  ".
                                                      " AND TDOCDEV.CODIGO_TIPO_DOCUMENTO  = :strCodigoTipoDocDev ".
                                                      " AND DEVCAB.ESTADO_IMPRESION_FACT   IN (:arrayEstadosPagos) ".
                                                    " )) ".
                                                " THEN 'S' ".
                                                " ELSE 'N' ".
                                               "END AS PAGADA, ".
                                               "ATDOCFINAN.NOMBRE_TIPO_DOCUMENTO AS TIPO_DOCUMENTO, ".
                                               "TO_CHAR(IDFCAB.FE_EMISION,'DD-MM-RRRR') AS FE_EMISION, ".
                                               "TO_CHAR(SYSDATE,'DD-MM-RRRR') AS FE_ACTUAL, ".
                                               "IPUNTO.LOGIN AS LOGIN ".                                           
                                           "FROM DB_COMERCIAL.INFO_PUNTO                     IPUNTO, ". 
                                                "DB_COMERCIAL.INFO_SERVICIO                  ISER, ".
                                                "DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFCAB, ".
                                                "DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFDET, ".
                                                "DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC, ".
                                                "DB_COMERCIAL.ADMI_CARACTERISTICA AC, ".
                                                "DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDOCFINAN ".
                
                                       "WHERE IPUNTO.ID_PUNTO                = :intIdPunto ".
                                        "AND IPUNTO.ID_PUNTO                 = ISER.PUNTO_ID ".
                                        "AND IDFCAB.PUNTO_ID                 = IPUNTO.ID_PUNTO ".
                                        "AND IDFCAB.ID_DOCUMENTO             = IDFDET.DOCUMENTO_ID ".
                                        "AND IDFDET.SERVICIO_ID              = ISER.ID_SERVICIO  ".                                      
                                        "AND IDFCAB.ID_DOCUMENTO             = IDC.DOCUMENTO_ID ". 
                                        "AND IDC.CARACTERISTICA_ID           = AC.ID_CARACTERISTICA ". 
                                        "AND IDFCAB.TIPO_DOCUMENTO_ID        = ATDOCFINAN.ID_TIPO_DOCUMENTO  ".
                                        "AND AC.DESCRIPCION_CARACTERISTICA   IN (SELECT DISTINCT VALOR2 ". 
                                                                               " FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,  ".
                                                                               " DB_GENERAL.ADMI_PARAMETRO_DET DET ".
                                                                               " WHERE CAB.NOMBRE_PARAMETRO = :strNombreParametro ".
                                                                               " AND CAB.ESTADO = :strEstadoActivo ".
                                                                               " AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID ".
                                                                               " AND DET.ESTADO <> :strEstadoEliminado) ". 
                                        "AND AC.ESTADO                        = :strEstadoActivo ". 
                                        "AND IDC.VALOR                        = :strValor  ". 
                                        "AND IDC.ESTADO                       = :strEstadoActivo ". 
                                        "AND IDFCAB.ESTADO_IMPRESION_FACT     IN (:arrayEstadoFactura)  ". 
                                        "AND ATDOCFINAN.CODIGO_TIPO_DOCUMENTO IN (:arrayTipoDocumento)  ". 
                
                                   ") TABLA ".
                           "WHERE TABLA.PAGADA = :strPagada ";

            $objQuery->setParameter("intIdPunto"           , $arrayParametros['intIdPunto']);
            $objQuery->setParameter("strCodigoTipoDocNdi"  , 'NDI');
            $objQuery->setParameter("strCodigoTipoDocAntc" , 'ANTC');
            $objQuery->setParameter("strCodigoTipoDocDev"  , 'DEV');
            $objQuery->setParameter("arrayEstadosPagos"    , $arrayParametros['arrayEstadosPagos']);          
            $objQuery->setParameter("strNombreParametro"   , $arrayParametros['strNombreParametro']);
            $objQuery->setParameter("strEstadoActivo"      , $arrayParametros['strEstadoActivo']);
            $objQuery->setParameter("strEstadoEliminado"   , $arrayParametros['strEstadoEliminado']);                        
            $objQuery->setParameter("strValor"             , $arrayParametros['strValor']);
            $objQuery->setParameter("arrayEstadoFactura"   , $arrayParametros['arrayEstadoFactura']);
            $objQuery->setParameter("arrayTipoDocumento"   , $arrayParametros['arrayTipoDocumento']);            
            $objQuery->setParameter("strPagada"            , $arrayParametros['strPagada']);

            $objRsm->addScalarResult('DIAS'           ,'dias'          , 'integer');            
            $objRsm->addScalarResult('ID_DOCUMENTO'   ,'idDocumento'   , 'integer');
            $objRsm->addScalarResult('NUMERO_FACTURA' ,'numeroFactura' , 'string');
            $objRsm->addScalarResult('PAGADA'         ,'pagada', 'string');       
            $objRsm->addScalarResult('TIPO_DOCUMENTO' ,'tipoDocumento' , 'string');
            $objRsm->addScalarResult('FE_EMISION'     ,'feEmision'     , 'string');
            $objRsm->addScalarResult('FE_ACTUAL'      ,'feActual'      , 'string');
            $objRsm->addScalarResult('LOGIN'          ,'login'         , 'string');
           
            $objQuery->setSQL($strSqlSelect);

            $arrayRespuesta = array('status' => true,
                                    'result' => $objQuery->getResult());
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status' => false,
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    } 
    
    /*
     * Función encargada de retornar los datos de la última factura de un login.
     *
     * costo 20
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 05-03-2020
     *
     * @param  Array $arrayParametros [
     *                                  arrayServicios       : Ids de servicios,
     *                                  strNombreParametro   : Nombre de característica.
     *                                ]
     * @return Array $arrayRespuesta
     */
    public function datosUltimaFactInstalacion($arrayParametros)
    {
        $strSql       =  " SELECT CAB2.ID_DOCUMENTO,
                             CAB2.ESTADO_IMPRESION_FACT
                           FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB2
                           WHERE ID_DOCUMENTO IN ( SELECT NVL(MAX (CAB.ID_DOCUMENTO), 0) AS ID_DOCUMENTO
                                                   FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
                                                     DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DET,
                                                     DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                                                     DB_COMERCIAL.ADMI_CARACTERISTICA AC
                                                   WHERE DET.SERVICIO_ID             IN :arrayServicios
                                                   AND DET.DOCUMENTO_ID              = CAB.ID_DOCUMENTO
                                                   AND CAB.ID_DOCUMENTO              = IDC.DOCUMENTO_ID
                                                   AND IDC.CARACTERISTICA_ID         = AC.ID_CARACTERISTICA
                                                   AND AC.DESCRIPCION_CARACTERISTICA IN (SELECT DISTINCT VALOR2
                                                                                         FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
                                                                                           DB_GENERAL.ADMI_PARAMETRO_DET DET
                                                                                         WHERE CAB.NOMBRE_PARAMETRO = :strNombreParametro
                                                                                         AND CAB.ESTADO             = :strEstadoActivo
                                                                                         AND CAB.ID_PARAMETRO       = DET.PARAMETRO_ID
                                                                                         AND DET.ESTADO             <> :strEstadoEliminado)
                                                   AND AC.ESTADO                     = :strEstadoActivo
                                                   AND IDC.VALOR                     = :strValorS
                                                   AND IDC.ESTADO                    = :strEstadoActivo )";

        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objQuery      = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("arrayServicios", $arrayParametros["arrayServicios"]);
        $objQuery->setParameter("strEstadoActivo", "Activo");
        $objQuery->setParameter("strEstadoEliminado", "Eliminado");
        $objQuery->setParameter("strValorS", "S");
        $objQuery->setParameter("strNombreParametro", $arrayParametros["strNombreParametro"]);
        
        $objRsm->addScalarResult('ID_DOCUMENTO', 'idDocumento', 'int');
        $objRsm->addScalarResult('ESTADO_IMPRESION_FACT', 'strEstado', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta;
    }

   /**
    * Documentación para el método 'getFactRechazadasPorCriterios'.
    *
    * Función que obtiene el listado de facturas rechazadas.
    *
    * @param  arrayParametrosIn ['strFeEmisionDesde'  => Recibe la fecha emisión desde,
                                 'strFeEmisionHasta'  => Recibe la fecha emisión hasta,
                                 'arrayTipoRechazo'   => Recibe un arreglo de los tipos de mensajes de rechazo,
                                 'strLogin'           => Recibe el login,
                                 'strIdentificacion'  => Recibe la identificación,
                                 'intIdEmpresa'       => Recibe el id empresa
    *                           ]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    * Costo Query:3100
    */
    public function getFactRechazadasPorCriterios($arrayParametros)
    {
        $strFeEmisionDesde = $arrayParametros['strFeEmisionDesde'] ? $arrayParametros['strFeEmisionDesde'] : '';
        $strFeEmisionHasta = $arrayParametros['strFeEmisionHasta'] ? $arrayParametros['strFeEmisionHasta'] : '';
        $arrayTipoRechazo  = $arrayParametros['arrayTipoRechazo'] ? $arrayParametros['arrayTipoRechazo'] : '';
        $strLogin          = $arrayParametros['strLogin'] ? $arrayParametros['strLogin'] : "";
        $strIdentificacion = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion'] : "";
        $intIdEmpresa      = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa'] : "";
        
        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('ID_DOCUMENTO',           'intIdDocumento',   'integer');
            $objRsm->addScalarResult('NUMERO_FACTURA_SRI',     'strNumeroFactSri', 'string');
            $objRsm->addScalarResult('LOGIN',                  'strLogin',         'string');
            $objRsm->addScalarResult('NOMBRE_CLIENTE',         'strNombreCliente', 'string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'strIdentificacion',  'string');
            $objRsm->addScalarResult('ESTADO',                 'strEstado', 'string');  
            $objRsm->addScalarResult('FE_CREACION',            'dateFeCreacion',   'datetime');
            $objRsm->addScalarResult('FE_EMISION',             'dateFeEmision',    'datetime');
            $objRsm->addScalarResult('VALOR_TOTAL',            'strValorTotal',    'string');
            $objRsm->addScalarResult('INFORMACION_ADICIONAL',  'strMensajeError',  'string');

            $strSelect  = "SELECT IDFC.ID_DOCUMENTO, 
                                  IDFC.NUMERO_FACTURA_SRI, 
                                  IP.LOGIN, 
                                  INITCAP 
                                  ( 
                                      CASE 
                                         WHEN IPE.RAZON_SOCIAL IS NOT NULL 
                                         THEN 
                                            IPE.RAZON_SOCIAL 
                                         WHEN IPE.NOMBRES IS NOT NULL AND IPE.APELLIDOS IS NOT NULL 
                                         THEN 
                                            IPE.NOMBRES || ' ' || IPE.APELLIDOS 
                                         WHEN ipe.REPRESENTANTE_LEGAL IS NOT NULL 
                                         THEN 
                                            IPE.REPRESENTANTE_LEGAL 
                                      END 
                                   ) NOMBRE_CLIENTE, 
                                   IPE.IDENTIFICACION_CLIENTE, 
                                   IDFC.ESTADO_IMPRESION_FACT ESTADO,          
                                   IDFC.FE_CREACION, 
                                   IDFC.FE_EMISION, 
                                   NVL(IDFC.VALOR_TOTAL,0) VALOR_TOTAL,                                  
                                   LISTAGG (IMEN.INFORMACION_ADICIONAL, '|') WITHIN GROUP (ORDER BY IDFC.ID_DOCUMENTO) INFORMACION_ADICIONAL"; 

            $strFrom    = " FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  IDFC".
                              ", DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF".
                              ", DB_COMERCIAL.INFO_OFICINA_GRUPO IOG".
                              ", DB_COMERCIAL.INFO_PUNTO                      IP".
                              ", DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL        IPER".
                              ", DB_COMERCIAL.INFO_PERSONA                    IPE".
                              ", DB_FINANCIERO.INFO_MENSAJE_COMP_ELEC         IMEN";

            $strWhere   = " WHERE ATDF.ID_TIPO_DOCUMENTO         = IDFC.TIPO_DOCUMENTO_ID".
                            " AND IDFC.OFICINA_ID                = IOG.ID_OFICINA".
                            " AND IP.ID_PUNTO                    = IDFC.PUNTO_ID".
                            " AND IPER.ID_PERSONA_ROL            = IP.PERSONA_EMPRESA_ROL_ID".
                            " AND IPE.ID_PERSONA                 = IPER.PERSONA_ID".
                            " AND IOG.EMPRESA_ID                 = :intIdEmpresa".
                            " AND IDFC.ID_DOCUMENTO              = IMEN.DOCUMENTO_ID".
                            " AND IDFC.ESTADO_IMPRESION_FACT     = 'Rechazado'".
                            " AND ATDF.CODIGO_TIPO_DOCUMENTO     IN (:arrayTiposDoc)".
                            " AND IMEN.TIPO                      = :strTipoError ";

            $strGroupBy = " GROUP BY IDFC.ID_DOCUMENTO, 
                            IDFC.NUMERO_FACTURA_SRI, 
                            IP.LOGIN, 
                            INITCAP 
                            ( 
                              CASE 
                                 WHEN IPE.RAZON_SOCIAL IS NOT NULL 
                                 THEN 
                                    IPE.RAZON_SOCIAL 
                                 WHEN IPE.NOMBRES IS NOT NULL AND IPE.APELLIDOS IS NOT NULL 
                                 THEN 
                                    IPE.NOMBRES || ' ' || IPE.APELLIDOS 
                                 WHEN ipe.REPRESENTANTE_LEGAL IS NOT NULL 
                                 THEN 
                                    IPE.REPRESENTANTE_LEGAL 
                              END 
                            ) , 
                            IDFC.ESTADO_IMPRESION_FACT, 
                            IPE.IDENTIFICACION_CLIENTE, 
                            IDFC.FE_CREACION, 
                            IDFC.FE_EMISION, 
                            VALOR_TOTAL "; 
            
            $objQuery->setParameter('strTipoError', 'ERROR');
            $objQuery->setParameter('arrayTiposDoc', array('FAC','FACP'));
            $objQuery->setParameter('intIdEmpresa', $intIdEmpresa);

            $objQueryCount->setParameter('strTipoError', 'ERROR');  
            $objQueryCount->setParameter('arrayTiposDoc', array('FAC','FACP'));
            $objQueryCount->setParameter('intIdEmpresa', $intIdEmpresa);
                  
            if($strFeEmisionDesde=="" && $strFeEmisionHasta=="") 
            {         
                $strWhere .= "AND TO_CHAR(IDFC.FE_EMISION,'MM-RRRR')  = TO_CHAR(SYSDATE,'MM-RRRR') ";               
            }
            
            if($arrayTipoRechazo!=="")
            {
                $strWhere .= "AND IMEN.MENSAJE IN (:arrayTipoRechazo) ";
                $objQuery->setParameter('arrayTipoRechazo', $arrayTipoRechazo);
                $objQueryCount->setParameter('arrayTipoRechazo', $arrayTipoRechazo);
            }
            if($strLogin!=="")
            {
                $strWhere .= "AND IP.LOGIN = :strLogin ";
                $objQuery->setParameter('strLogin', $strLogin);
                $objQueryCount->setParameter('strLogin', $strLogin);
            }
            if($strIdentificacion!=="")
            {
                $strWhere .= "AND IPE.IDENTIFICACION_CLIENTE = :strIdentificacion ";
                $objQuery->setParameter('strIdentificacion', $strIdentificacion);
                $objQueryCount->setParameter('strIdentificacion', $strIdentificacion);
            }            
            if($strFeEmisionDesde!=="")
            {
                $strWhere .= "AND IDFC.FE_EMISION >= TO_DATE(:dateFechaDesde , 'DD-MM-YYYY') ";
                $objQuery->setParameter('dateFechaDesde', $strFeEmisionDesde);
                $objQueryCount->setParameter('dateFechaDesde', $strFeEmisionDesde);
            }
            if($strFeEmisionHasta!=="")
            {
                $strWhere .= "AND IDFC.FE_EMISION <= TO_DATE(:dateFechaHasta , 'DD-MM-YYYY') + 1 ";
                $objQuery->setParameter('dateFechaHasta', $strFeEmisionHasta);
                $objQueryCount->setParameter('dateFechaHasta', $strFeEmisionHasta);
            }

            $strSqlSub   = "(".$strSelect     .$strFrom.$strWhere.$strGroupBy.") ORDER BY IDFC.FE_EMISION DESC ";
            
            $strSqlCount = $strSelectCount.$strFrom.$strWhere.$strGroupBy;

            $strSqlTotal = "SELECT COUNT(CANTIDAD.TOTAL) AS TOTAL FROM ($strSqlCount) CANTIDAD";
            $arrayTotalFactRechazadas = $objQueryCount->setSQL($strSqlTotal)->getSingleScalarResult();
            $arrayFactRechazadas = $objQuery->setSQL($strSqlSub)->getArrayResult();

            $objResultado['total']     = $arrayTotalFactRechazadas;
            $objResultado['registros'] = $arrayFactRechazadas;
            
        }
        catch(\Exception $e)
        {
            $objResultado = array('total' => 0, 'registros' => array());
        }

        return $objResultado;
        
    }
    
   /**
    * Documentación para el método 'getTipoErrorFactRechazadas'.
    *
    * Función que obtiene los tipos de mensajes de errores de las facturas rechazadas
    *
    * @param  $arrayParametros ['strSqlTipoError'  => Query para obtener los tipos de mensajes de error ]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    * Costo Query:3000
    */
    public function getTipoErrorFactRechazadas($arrayParametros)
    {   
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);  
            $strSql   = $arrayParametros['strSqlTipoError'];
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('MENSAJE', 'strMensaje', 'string');        
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            $arrayResult = array();
        }
        
        return $arrayResult;
    }
    
   /**
    * Documentación para el método 'getNumeroAutorizacion'.
    *
    * Función que obtiene el número de autorización de un documento financiero. 
    *
    * @param  $arrayParametros ['intIdDocumento'  => Id documento financiero ]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    * Costo Query:3
    */
    public function getNumeroAutorizacion($arrayParametros)
    {
        try
        {
            $strSql   =   "SELECT NUMERO_AUTORIZACION 
                             FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB 
                          WHERE ID_DOCUMENTO = :intIdDocumento ";

            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $objQuery->setParameter("intIdDocumento", $arrayParametros["intIdDocumento"]);

            $objRsm->addScalarResult('NUMERO_AUTORIZACION', 'numeroAutorizacion', 'string');
            $objQuery->setSQL($strSql);
            
            $arrayRespuesta = $objQuery->getScalarResult();
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array();
        }
          
        return $arrayRespuesta;
    }

   /**
    * Documentación para el método 'ejecutarReprocesoFactRechazadas'.
    *
    * Función que ejecuta el reproceso de facturas rechazadas para enviarlas al SRI. 
    *
    * @param  $arrayParametros ['strCodEmpresa'       => Código de empresa,
                                'strTipoTransaccion'  => Tipo de Transacción(UPDATE),
                                'strIdsDocumento'     => Id's documento financiero]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    */
    public function ejecutarReprocesoFactRechazadas($arrayParametros)
    {
        try
        {
            $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
            $strTipoTransaccion = $arrayParametros['strTipoTransaccion'];
            $strIdsDocumento    = $arrayParametros['strIdsDocumento'];
            
            $strSql = "DECLARE
                         Ln_job_exists NUMBER;
                       BEGIN

                            SELECT COUNT(*) INTO Ln_job_exists
                              FROM user_scheduler_jobs
                             WHERE job_name = 'JOB_MASIVO_FACT_RECHAZADAS';

                            IF Ln_job_exists = 1 THEN
                                DBMS_SCHEDULER.DROP_JOB(job_name => '\"DB_FINANCIERO\".\"JOB_MASIVO_FACT_RECHAZADAS\"',
                                                        defer    => false,
                                                        force    => false);
                            END IF;

                            DBMS_SCHEDULER.CREATE_JOB (job_name   => '\"DB_FINANCIERO\".\"JOB_MASIVO_FACT_RECHAZADAS\"',
                                                       job_type   => 'PLSQL_BLOCK',
                                                       job_action => 'DECLARE
                                                        BEGIN
                                                          DB_FINANCIERO.FNCK_COM_ELECTRONICO.P_PROCESAR_FACT_RECHAZADAS(
                                                          Pv_CodEmpresa      => ''$strCodEmpresa'', 
                                                          Pv_TipoTransaccion => ''$strTipoTransaccion'', 
                                                          Pv_IdsDocumento    => ''$strIdsDocumento'');
                                                        END;',
                                                       number_of_arguments => 0,
                                                       start_date          => NULL,
                                                       repeat_interval     => NULL,
                                                       end_date            => NULL,
                                                       enabled             => FALSE,
                                                       auto_drop           => TRUE,
                                                       comments => 'Proceso para reenviar facturas rechazadas al SRI.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_FINANCIERO\".\"JOB_MASIVO_FACT_RECHAZADAS\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_FINANCIERO\".\"JOB_MASIVO_FACT_RECHAZADAS\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->execute();
            $strRespuesta='OK';
            
            return $strRespuesta;

        }
        catch (\Exception $ex)
        {       
            return "ERROR";
        }
    }
    
   /**
    * Documentación para el método 'creaProcesoMasivoFactRechazadas'.
    *
    * Función que crea el proceso masivo de facturas rechazadas para enviarlas al SRI. 
    *
    * @param  $arrayParametros ['strUsrCreacion'    => Usuario de Creación,
                                'strCodEmpresa'     => Código de Empresa,
                                'strIpCreacion'     => Ip de Creación,
                                'strTipoPma'     => Tipo de Proceso Masivo(Individual/Masivo)
                                ]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    */
    public function creaProcesoMasivoFactRechazadas($arrayParametros)
    {     
        try
        {  
            $strSql = " BEGIN 
                         DB_FINANCIERO.FNCK_COM_ELECTRONICO.P_CREA_PM_FACT_RECHAZADAS 
                         ( :Pv_UsrCreacion,
                           :Pv_CodEmpresa,
                           :Pv_IpCreacion,
                           :Pv_TipoPma,
                           :Pv_MsjResultado ); 
                       END; ";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $strResultado = str_pad($strResultado, 5000, " ");
            $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);
            $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
            $objStmt->bindParam('Pv_IpCreacion', $arrayParametros['strIpCreacion']);
            $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);
            $objStmt->bindParam('Pv_MsjResultado', $strResultado);
            $objStmt->execute();   

        }
        catch (\Exception $e)
        {
            $strResultado= 'Ocurrió un error al guardar el Proceso Masivo '.$arrayParametros['strTipoPma'];
        }
        
        return $strResultado; 
    }
    
   /**
    * Documentación para el método 'getCountProcesoMasivo'.
    *
    * Función que obtiene el número de procesos masivos en estado pendiente. 
    *
    * @param  $arrayParametros ['strCodEmpresa'  => Código de Empresa,
                                'strTipoPma'     => Tipo de Proceso Masivo(Individual/Masivo)
                                ]
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 25-01-2021
    * Costo Query: 3 
    */
    public function getCountProcesoMasivo($arrayParametros)
    {
        try
        {
            $strSql      = "SELECT COUNT(*) CANTIDAD
                              FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB 
                            WHERE TIPO_PROCESO = :strTipoPma
                              AND EMPRESA_ID   = :strCodEmpresa
                              AND ESTADO       = :strEstado ";

        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("strTipoPma", $arrayParametros["strTipoPma"]);
        $objQuery->setParameter("strCodEmpresa", $arrayParametros["strCodEmpresa"]);
        $objQuery->setParameter("strEstado", 'Pendiente');

        $objRsm->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        
        }
        catch (\Exception $e)
        {
            $arrayRespuesta = array();
        }
        
        return $arrayRespuesta;
    }
        
    
    /**
     * Documentación para el método 'getFacturasManuales'.
     *
     * Función que retorna las facturas manuales que no tengan nota de credito asociada del mes vigente.
     *
     * Costo: 1500
     * 
     * @param $arrayBusqueda.
     * 
     * @return $arrayRespuesta
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 11-01-2021
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 07-04-2021 - Se agrega el campo valor total de la factura.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.2 21-05-2021 - Se modifica el query para que valide correctamente las facturas con notas de credito.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.3 28-01-2022 - Se modifica el query para que valide estados impresión cerrados en la facturas.
     *
     */
    public function getFacturasManuales($arrayBusqueda)
    {
        $intIdFactura      = ( isset($arrayBusqueda['intIdFactura']) && !empty($arrayBusqueda['intIdFactura']) ) 
                                   ? $arrayBusqueda['intIdFactura'] : '';
        
        $strVendedor      = ( isset($arrayBusqueda['strVendedor']) && !empty($arrayBusqueda['strVendedor']) ) 
                                   ? $arrayBusqueda['strVendedor'] : '';
        $strSql =   "SELECT DISTINCT IDFC.ID_DOCUMENTO AS ID_DOCUMENTO,IDFC.FE_EMISION AS FE_CONSUMO,IP.USR_VENDEDOR AS VENDEDOR,IP.LOGIN AS LOGIN,
                     NVL(IPE.RAZON_SOCIAL,IPE.NOMBRES ||' '|| IPE.APELLIDOS) CLIENTE, IDFC.VALOR_TOTAL
                        FROM INFO_DOCUMENTO_FINANCIERO_CAB IDFC,INFO_PUNTO IP , INFO_DOCUMENTO_FINANCIERO_DET IDFD
                        ,INFO_PERSONA_EMPRESA_ROL IPER, INFO_PERSONA IPE, DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOC
                        WHERE IDFC.ES_AUTOMATICA='N' 
                        AND IDFC.ESTADO_IMPRESION_FACT in ('Activo','Cerrado')
                        AND IDFC.FE_EMISION>=TRUNC(SYSDATE, 'MM') AND IDFC.FE_EMISION<=TRUNC(LAST_DAY(SYSDATE))
                        AND IDFC.TIPO_DOCUMENTO_ID = TDOC.ID_TIPO_DOCUMENTO
                        AND TDOC.CODIGO_TIPO_DOCUMENTO  IN ('FAC','FACP')
                        AND IDFC.PUNTO_ID=ID_PUNTO 
                        AND IDFD.DOCUMENTO_ID=IDFC.ID_DOCUMENTO AND IDFD.EMPRESA_ID=10
                        AND IDFC.ID_DOCUMENTO NOT IN (SELECT IDFC2.REFERENCIA_DOCUMENTO_ID FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC2
                        WHERE IDFC2.REFERENCIA_DOCUMENTO_ID=IDFC.ID_DOCUMENTO)
                        AND IP.PERSONA_EMPRESA_ROL_ID=IPER.ID_PERSONA_ROL
                        AND IPER.PERSONA_ID=IPE.ID_PERSONA AND IPE.ESTADO='Activo' ";  
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        if(!empty($intIdFactura))
        {
            $strSql .= " AND IDFC.ID_DOCUMENTO = :intIdDocumento ";
            $objQuery->setParameter('intIdDocumento', $intIdFactura);
        }
        if(!empty($strVendedor))
        {
            $strSql .= " AND IP.USR_VENDEDOR = :strVendedor ";
            $objQuery->setParameter('strVendedor', $strVendedor);
        }
        
        $objRsm->addScalarResult('ID_DOCUMENTO', 'idDocumento', 'string');
        $objRsm->addScalarResult('FE_CONSUMO', 'feConsumo', 'string');
        $objRsm->addScalarResult('VENDEDOR', 'vendedor', 'string');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('CLIENTE', 'cliente', 'string');
        $objRsm->addScalarResult('VALOR_TOTAL', 'valor_total', 'string');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        return $arrayRespuesta;
    }
    
   /**
    * Documentación para el método 'getInformacionDocumento'.
    *
    * Función que obtiene información del documento enviado como parámetro.
    * 
    * @return $arrayValores
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 11-02-2021
    * 
    * Costo Query:10
    */
    public function getInformacionDocumento($arrayParametros)
    {   
        try
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSql = " SELECT  FAC.ID_DOCUMENTO,FAC.PUNTO_ID ,(FAC.SUBTOTAL-FAC.SUBTOTAL_DESCUENTO) AS BASE_IMPONIBLE,
                                FAC.SUBTOTAL_CON_IMPUESTO 
                        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB FAC
                        JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATD ON ATD.ID_TIPO_DOCUMENTO = FAC.TIPO_DOCUMENTO_ID
                        WHERE ATD.ID_TIPO_DOCUMENTO IN (1,5)
                        AND   FAC.NUMERO_FACTURA_SRI    = :strNumeroFacturaSri
                        AND (SELECT IEG.COD_EMPRESA 
                             FROM DB_COMERCIAL.INFO_PUNTO IPT,
                                  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                  DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                                  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG
                            WHERE IPT.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                            AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                            AND IER.EMPRESA_COD = IEG.COD_EMPRESA
                            AND IPT.ID_PUNTO = FAC.PUNTO_ID) = :strCodEmpresa";

           $objQuery->setParameter('strNumeroFacturaSri', $arrayParametros['strNumeroFacturaSri']);
           $objQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
           $objRsm->addScalarResult('ID_DOCUMENTO', 'intIdDocumento', 'integer');  
           $objRsm->addScalarResult('PUNTO_ID', 'intIdPunto', 'integer');
           $objRsm->addScalarResult('BASE_IMPONIBLE', 'floatBaseImp', 'float');
           $objRsm->addScalarResult('SUBTOTAL_CON_IMPUESTO', 'floatBaseImpIva', 'float');
           $objQuery->setSQL($strSql);
           $arrayValores = $objQuery->getResult();
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
        
        return $arrayValores;
    } 
    
    
    /**
     * Documentación para getFVarcharClean
     * 
     * Función que limpia los caracteres especiales de la descripción enviada como parámetro. 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 13-07-2021
     */
    public function getVarcharClean($strDescripcion)
    {
        try
        {
            $strDescripcionCleaned = "";
            $strDescripcionCleaned = str_pad($strDescripcionCleaned, 10000, " ");
            $strSql = "BEGIN :strDescripcionCleaned := DB_FINANCIERO.FNKG_PAGO_AUTOMATICO.GET_VARCHAR_CLEAN(:strDescripcion); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('strDescripcion',        $strDescripcion);
            $objStmt->bindParam('strDescripcionCleaned', $strDescripcionCleaned);
            $objStmt->execute();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            
            $strDescripcionCleaned = "";
        }
        
        return $strDescripcionCleaned;
    }  
    
    
    /**
    * Documentación para el método 'existeFacturaPendiente'.
    *
    * Función que indica si el cliente tiene una factura creada.
    * 
    * @return $arrayValores
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.0 27-03-2022
    * 
    * Costo Query:10
    */
    public function existeFacturaCerradaPunto($arrayParametros)
    {   
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSql   = "   SELECT IDFC.ID_DOCUMENTO
                            FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,
                                DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                                DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                                DB_COMERCIAL.INFO_PUNTO IP,
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                DB_COMERCIAL.INFO_CONTRATO IC
                            WHERE IDFC.ID_DOCUMENTO = IDC.DOCUMENTO_ID
                            AND AC.ID_CARACTERISTICA = IDC.CARACTERISTICA_ID
                            AND IP.ID_PUNTO = IDFC.PUNTO_ID
                            AND IPER.ID_PERSONA_ROL = IP.PERSONA_EMPRESA_ROL_ID
                            AND IC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                            AND IDFC.ESTADO_IMPRESION_FACT = :estadosFactura
                            AND AC.DESCRIPCION_CARACTERISTICA IN (:strDescripcionCaracteristica)
                            AND IDC.VALOR = :strValor
                            AND IDC.ESTADO = :strEstadoActivo
                            AND IP.ID_PUNTO = :idPunto
                            AND ROWNUM = 1 ";

            $objQuery->setParameter('strDescripcionCaracteristica', array_values(array('POR_CONTRATO_DIGITAL', 'POR_CONTRATO_FISICO')));
            $objQuery->setParameter('estadosFactura','Cerrado');
            $objQuery->setParameter('strValor',       'S');
            $objQuery->setParameter('strEstadoActivo','Activo');
            $objQuery->setParameter('idPunto',        $arrayParametros['intPunto']);
            $objRsm->addScalarResult('ID_DOCUMENTO', 'intIdDocumento', 'integer');  
            $objQuery->setSQL($strSql);
            $arrayValores = $objQuery->getResult();
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
        
        return $arrayValores;
    } 

/**
    * Documentación para el método 'getTotalFacturasVencidasPorPersonaEmpresaRolPorPunto'.
    *
    * Función que el total de facturas abiertas
    * 
    * @param $arrayParametros [
    *                            'intIdPersonaEmpresaRol',
    *                            'intIdPunto',
    *                            'strFechaLimite'
    *                          ]
    *@return $intTotal
    * @author Daniel Guzman <ddguzman@telconet.ec>
    * @version 1.0 07-03-2023
    * 
    */
    public function getTotalFacturasVencidasPorPersonaEmpresaRolPorPunto($arrayParametros)
    {
        $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
        $intIdPunto             = $arrayParametros['intIdPunto'];
        $strFechaLimite         = $arrayParametros['strFechaLimite'];

        $objQuery = $this->_em->createQuery();

        $strQuery ="
                    SELECT COUNT(idfc) AS TOTAL
            FROM 
                        schemaBundle:InfoDocumentoFinancieroCab idfc,
                        schemaBundle:AdmiTipoDocumentoFinanciero atdf,
              schemaBundle:InfoPersonaEmpresaRol per,
              schemaBundle:InfoPunto pto
          WHERE
              per.id = :intIdPersonaEmpresaRol AND
            per.id = pto.personaEmpresaRolId AND
                        pto.id = idfc.puntoId AND
                        pto.id = :intIdPunto AND
                        idfc.feEmision <= :strFechaLimite AND
                        idfc.tipoDocumentoId = atdf.id AND
            atdf.codigoTipoDocumento in ('FAC','FACP') AND  
                        idfc.estadoImpresionFact in ('Activo','Activa')
                    ORDER BY 
                        idfc.feCreacion ASC";
        $objQuery->setParameter('intIdPersonaEmpresaRol', $intIdPersonaEmpresaRol);
        $objQuery->setParameter('intIdPunto', $intIdPunto);
        $objQuery->setParameter('strFechaLimite', $strFechaLimite);

        $objQuery->setDQL($strQuery);
        $intTotal = $objQuery->getSingleScalarResult();
        return $intTotal;
    }



}
