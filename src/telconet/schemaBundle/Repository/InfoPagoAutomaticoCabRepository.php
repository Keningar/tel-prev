<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response; 

class InfoPagoAutomaticoCabRepository extends EntityRepository
{
    
    /**
     * getEstadosCtaPorCriterios()
     * Obtiene listado de estados de cuenta, mediante filtros por: fechaDesde, fechaHasta, bancoTipoCtaId
     *
     * costoQuery: 10
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-09-2020 
     *
     * @param  array $arrayParametros [
     *                                  "fechaDesde"  => Fecha desde,
     *                                  "fechaHasta" => Fecha hasta,
     *                                  "bancoTipoCtaId" => Banco tipo cta id
     *                                ]
     *
     * @return $objResultado - Listado de Pagos
     */
    public function getEstadosCtaPorCriterios($arrayParametros)
    {
        $strFechaDesde         = $arrayParametros['strFechaDesde'] ? $arrayParametros['strFechaDesde'] : '';
        $strFechaHasta         = $arrayParametros['strFechaHasta'] ? $arrayParametros['strFechaHasta'] : '';
        $intCtaContableId      = $arrayParametros['intCtaContableId'] ? $arrayParametros['intCtaContableId'] : "";

        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('ID_PAGO_AUTOMATICO' , 'intIdPagoAutomatico'  , 'integer');
            $objRsm->addScalarResult('BANCO'              , 'strBanco'             , 'string');            
            $objRsm->addScalarResult('ESTADO'             , 'strEstado'            , 'string');
            $objRsm->addScalarResult('FE_CREACION'        , 'dateFeCreacion'       , 'datetime');
            $objRsm->addScalarResult('USR_CREACION'       , 'strUsrCreacion'       , 'string');

            $strSelect  = "SELECT IPA.ID_PAGO_AUTOMATICO,
                                  CONCAT(ACCT.DESCRIPCION,CONCAT('-',ACCT.NO_CTA)) AS BANCO,
                                  IPA.ESTADO,
                                  IPA.FE_CREACION,
                                  IPA.USR_CREACION";

            $strFrom    = " FROM DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CAB IPA,".
                              "  DB_FINANCIERO.ADMI_CUENTA_CONTABLE  ACCT, ".
                              "  DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE ATCC ";

            $strWhere   = " WHERE IPA.CUENTA_CONTABLE_ID  = ACCT.ID_CUENTA_CONTABLE AND ATCC.ID_TIPO_CUENTA_CONTABLE = ACCT.TIPO_CUENTA_CONTABLE_ID".
                          " AND ATCC.DESCRIPCION = :strTipoCuenta AND ACCT.EMPRESA_COD = :strEmpresaCod  ";
                
                
            $strGroupBy = " ORDER BY IPA.ID_PAGO_AUTOMATICO ASC ";

            if($intCtaContableId !== "")
            {
                $strWhere .=" AND IPA.CUENTA_CONTABLE_ID = :intCtaContableId";
                $objQuery->setParameter('intCtaContableId', $intCtaContableId);
                $objQueryCount->setParameter('intCtaContableId', $intCtaContableId);
            }

            if($strFechaDesde!=="" && $strFechaHasta!=="")
            {
                $strFechaDesdeC = strtotime($strFechaDesde);
                $strFechaHastaC = strtotime($strFechaHasta);
                $strWhere .=" AND IPA.FE_CREACION >= :fechaDesde AND IPA.FE_CREACION <= :fechaHasta ";
                $objQuery->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQueryCount->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQuery->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));
                $objQueryCount->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));                
            }
            $objQuery->setParameter('strTipoCuenta', 'BANCOS');
            $objQueryCount->setParameter('strTipoCuenta', 'BANCOS'); 
            $objQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
            $objQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']); 
            
            $strSqlSub   = $strSelect     .$strFrom.$strWhere.$strGroupBy;
            $strSqlCount = $strSelectCount.$strFrom.$strWhere.$strGroupBy;

            $strSqlTotal = "SELECT COUNT(CANTIDAD.TOTAL) AS TOTAL FROM ($strSqlCount) CANTIDAD";
            
            $arrayTotalPagosCab = $objQueryCount->setSQL($strSqlTotal)->getSingleScalarResult();

            $strSql .= "SELECT PAGOS.* FROM ($strSqlSub) PAGOS ORDER BY PAGOS.FE_CREACION DESC";
            $arrayPagosCab = $objQuery->setSQL($strSql)->getArrayResult();

            $objResultado['total']     = $arrayTotalPagosCab;
            $objResultado['registros'] = $arrayPagosCab;
        }
        catch(\Exception $e)
        {
            $objResultado = array('total' => 0, 'registros' => array());
        }

        return $objResultado;       
    }    

    /**
     * Documentación para el método 'findEstadoDeCuenta'.
     *
     * Me devuelve los documentos tales como FAC, FACP, ND, NDI; los cuales generaran un arbol interno de opciones
     *
     * @param $arrayParametros
     *
     * @return resultado Listado de documentos y total de documentos.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-10-2020
     */
    public function findEstadoDeCuenta($arrayParametros)
    {
        $intTotal=0;
        if(isset($arrayParametros['intIdPtoSelect'])){    
            
            $objQuery = $this->_em->createQuery();
            
            $strDqlCc="SELECT count(idfc.id) ";
            
            $strDql="SELECT idfc.id,
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
            
            
            $strCuerpo="
                    FROM schemaBundle:EstadoCuentaCliente idfc,
                    schemaBundle:AdmiTipoDocumentoFinanciero atdf
                    WHERE 
                    idfc.tipoDocumentoId=atdf.id
                    and atdf.codigoTipoDocumento in (:arrayCodigos)
                    and idfc.migracion is null
                    and idfc.puntoId = :intIdPtoSelect  
                    and idfc.id      = :intIdDoc ";
                    
            $strDqlCc.=$strCuerpo;
            $strDql  .=$strCuerpo;
            
            if($arrayParametros['strFechaDesde']!="")
            {
                $strDql.=" and idfc.feCreacion >= :feDesde";
                $strDqlCc.=" and idfc.feCreacion >= :feDesde";
                $objQuery->setParameter('feDesde',date('Y/m/d', strtotime($arrayParametros['strFechaDesde'])));
            }    
                
            if($arrayParametros['strFechaHasta']!="")
            {
                $strDql.=" and idfc.feCreacion <= :feHasta";
                $strDqlCc.=" and idfc.feCreacion <= :feHasta";
                $objQuery->setParameter('feHasta',date('Y/m/d', strtotime($arrayParametros['strFechaHasta'])));
            }
            $strDql.=" order by idfc.feCreacion ";
            $arrayCodigos=array('FAC' , 'FACP', 'NDI');
            $objQuery->setParameter('arrayCodigos',$arrayCodigos);
            $objQuery->setParameter('intIdPtoSelect',$arrayParametros['intIdPtoSelect']);
            $objQuery->setParameter('intIdDoc',$arrayParametros['intIdFacturaSelect']);
            
            $objQuery->setDQL($strDql);
            $arrayDatos= $objQuery->getResult();
            
            if($arrayDatos)
            {
                $objQuery->setDQL($strDqlCc);
                $intTotal= $objQuery->getSingleScalarResult();
            }
            
            $arrayResultado['registros']= $arrayDatos;
            $arrayResultado['total']    = $intTotal;
            
        }
        else 
        { 
            $arrayResultado= '{"registros":"[]","total":0}';
        }
        
        return $arrayResultado;
    }

    /**
     * Documentación para el método 'findAnticiposEstadoDeCuenta'.
     *
     * Me devuelve los anticipos en estado pendientes
     *
     * @param $arrayParametros.
     *
     * @return resultado Listado de documentos pendientes
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-10-2020      
     */
    public function findAnticiposEstadoDeCuenta($arrayParametros)
    {
        $intTotal = 0;
        if(isset($arrayParametros['intIdPtoSelect']))
        {
            $objRsmBuilder = new ResultSetMappingBuilder($this->_em);
            $objQuery      = $this->_em->createNativeQuery(null, $objRsmBuilder);

            $strDqlCc = "SELECT count(idfc.ID_DOCUMENTO) TOTAL ";
            $objRsmBuilder->addScalarResult('TOTAL', 'total', 'integer');

            $strDql = "SELECT idfc.ID_DOCUMENTO ,
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
            $objRsmBuilder->addScalarResult('ID_DOCUMENTO'         , 'id'                  , 'integer');
            $objRsmBuilder->addScalarResult('NUMERO_FACTURA_SRI'   , 'numeroFacturaSri'    , 'string');
            $objRsmBuilder->addScalarResult('TIPO_DOCUMENTO_ID'    , 'tipoDocumentoId'     , 'integer');
            $objRsmBuilder->addScalarResult('VALOR_TOTAL'          , 'valorTotal'          , 'float');
            $objRsmBuilder->addScalarResult('FE_CREACION'          , 'feCreacion'          , 'string');
            $objRsmBuilder->addScalarResult('FEC_CREACION'         , 'fecCreacion'         , 'string');
            $objRsmBuilder->addScalarResult('FEC_EMISION'          , 'fecEmision'          , 'string');
            $objRsmBuilder->addScalarResult('FEC_AUTORIZACION'     , 'fecAutorizacion'     , 'string');
            $objRsmBuilder->addScalarResult('PUNTO_ID'             , 'puntoId'             , 'integer');
            $objRsmBuilder->addScalarResult('OFICINA_ID'           , 'oficinaId'           , 'integer');
            $objRsmBuilder->addScalarResult('REFERENCIA'           , 'referencia'          , 'string');
            $objRsmBuilder->addScalarResult('CODIGO_FORMA_PAGO'    , 'codigoFormaPago'     , 'string');
            $objRsmBuilder->addScalarResult('NUMERO_REFERENCIA'    , 'numeroReferencia'    , 'string');
            $objRsmBuilder->addScalarResult('NUMERO_CUENTA_BANCO'  , 'numeroCuentaBanco'   , 'string');
            $objRsmBuilder->addScalarResult('REFERENCIA_ID'        , 'referenciaId'        , 'integer');
            $objRsmBuilder->addScalarResult('COMENTARIO'           , 'comentario'          , 'string');
           
            $strCuerpo = "      FROM 
                                ESTADO_CUENTA_CLIENTE idfc,
                                ADMI_TIPO_DOCUMENTO_FINANCIERO atdf,
                                INFO_PAGO_DET ipd
                            WHERE 
                                idfc.TIPO_DOCUMENTO_ID = atdf.ID_TIPO_DOCUMENTO
                                and atdf.CODIGO_TIPO_DOCUMENTO in (:codigoTipoDocumento)
                                and idfc.MIGRACION IS NULL
                                and EXISTS (SELECT ipd.ID_PAGO_DET FROM INFO_PAGO_DET ipd
                                            WHERE ipd.ID_PAGO_DET = idfc.ID_DOCUMENTO and ipd.ESTADO in (:estado))
                                and ipd.REFERENCIA_ID = :intIdDoc  
                                and ipd.ID_PAGO_DET   = idfc.ID_DOCUMENTO 
                                and idfc.PUNTO_ID in (:puntos) ";

            $strDqlCc.=$strCuerpo;
            $strDql  .=$strCuerpo;
            
            if($arrayParametros['strFechaDesde'] != "")
            {
                $strDql.=" and idfc.FE_CREACION >= :feDesde";
                $strDqlCc.=" and idfc.FE_CREACION >= :feDesde";
                $objQuery->setParameter('feDesde', date('Y/m/d', strtotime($arrayParametros['strFechaDesde'])));
            }

            if($arrayParametros['strFechaHasta'] != "")
            {
                $strDql.=" and idfc.FE_CREACION <= :fe_hasta";
                $strDqlCc.=" and idfc.FE_CREACION <= :fe_hasta";
                $objQuery->setParameter('fe_hasta', date('Y/m/d', strtotime($arrayParametros['strFechaHasta'])));
            }

            if($arrayParametros['strEstadoAntEstCta'] == 'Pendiente')
            {
                $arrayEstados = array('Pendiente');
            }

            $arrayCodigosTipoDocumento = array('PAG', 'PAGC', 'ANT', 'ANTS', 'ANTC');

            $objQuery->setParameter('codigoTipoDocumento', $arrayCodigosTipoDocumento);
            $objQuery->setParameter('estado', $arrayEstados);
            $objQuery->setParameter('intIdDoc', $arrayParametros['intIdFacturaSelect']);
            $objQuery->setParameter('puntos', $arrayParametros['intIdPtoSelect']);


            $objQuery->setSQL($strDql);
            $arrayDatos = $objQuery->getResult();

            if($arrayDatos)
            {
                $objQuery->setSQL($strDqlCc);
                $intTotal = $objQuery->getSingleScalarResult();
                
            }

            $arrayResultado['registros'] = $arrayDatos;
            $arrayResultado['total']     = $intTotal;
        }
        else
        {
            $arrayResultado = '{"registros":"[]","total":0}';
        }
        
        return $arrayResultado;
    }
    /**
     * Documentación para el método 'findEstadoDeCuentaOG'.
     *
     * Me devuelve los anticipos en estado pendientes
     *
     * @param $arrayParametros.
     *
     * @return resultado Estado de cuenta OG
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-10-2020      
     */
    public function findEstadoDeCuentaOG($arrayParametros)
    {
        $strSubQuery="";

        if(isset($arrayParametros['intIdPtoSelect']))
        {	

            if($arrayParametros['strFechaDesde']!="")
            {
                $strSubQuery.="idfc.feCreacion >= '".date('Y/m/d', strtotime($fechaDesde))."' AND ";
            }

            if($arrayParametros['strFechaHasta']!="")
            {
                $strSubQuery.="idfc.feCreacion <= '".date('Y/m/d', strtotime($fechaHasta))."' AND ";
            }				

            $objQuery = $this->_em->createQuery("SELECT idfc.id,
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
                    ".$strSubQuery."
                    idfc.id = :intIdDoc
                    and idfc.tipoDocumentoId = atdf.id
                    and idfc.puntoId     = :intIdPtoSelect");
            $objQuery->setParameter('intIdDoc', $arrayParametros['intIdFacturaSelect']);
            $objQuery->setParameter('intIdPtoSelect', $arrayParametros['intIdPtoSelect']);
            $intTotal=count($objQuery->getResult());
            $arrayDatos = $objQuery->getResult();

            $arrayResultado['registros']= $arrayDatos;
            $arrayResultado['total']    = $intTotal;
        }
        else 
        { 
            $arrayResultado= '{"registros":"[]","total":0}';
        }

        return $arrayResultado;
    }

    /* Documentación para el método 'obtenerAnticiposAsignados'.
     *
     * Me devuelve los documentos tales como  'PAG','PAGC','ANT','ANTS','ANTC' que se encuentren en estado Asignado
     *
     * @param $arrayParametros.
     *
     * @return resultado Listado de documentos y total de documentos.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-10-2020
     */

    public function obtenerAnticiposAsignados($arrayParametros)
    {
        $intTotal    = 0;
        if(isset($arrayParametros['intIdPtoSelect']))
        {
            $objQuery = $this->_em->createQuery();

            $strDqlCc = "SELECT count(ipc.id) ";

            $strDql = "SELECT ipc.id,
					ipc.numeroPago,
					atdf.id as tipoDocumentoId,
					ipc.valorTotal,
					ipc.feCreacion,
					ipc.puntoId,
					ipc.oficinaId,
                    rec.id as recaudacionId  ";

            $strCuerpo = " 
                FROM schemaBundle:InfoPagoCab ipc 
                left join schemaBundle:InfoRecaudacion rec with rec.id     = ipc.recaudacionId   
                left join schemaBundle:InfoPagoDet     ipd with ipd.pagoId = ipc.id,  
					 schemaBundle:AdmiTipoDocumentoFinanciero atdf  
                WHERE 
                    ipc.tipoDocumentoId = atdf.id
					and atdf.codigoTipoDocumento in (:codigos)
					and ipc.estadoPago   = :estado
					and ipc.puntoId      = :intIdPtoSelect
                    and ipd.referenciaId = :intIdFactSelect ";

            $strDqlCc.=$strCuerpo;
            $strDql.=$strCuerpo;

            if($arrayParametros['strFechaDesde'] != "")
            {
                $strDql.=" and ipc.feCreacion >= :feDesde";
                $strDqlCc.=" and ipc.feCreacion >= :feDesde";
                $objQuery->setParameter('feDesde', date('Y/m/d', strtotime($arrayParametros['strFechaDesde'])));
            }

            if($arrayParametros['strFechaHasta'] != "")
            {
                $strDql.=" and ipc.feCreacion <= :feHasta";
                $strDqlCc.=" and ipc.feCreacion <= :feHasta";
                $objQuery->setParameter('feHasta', date('Y/m/d', strtotime($arrayParametros['strFechaHasta'])));
            }

            $arrayCodigos = array('PAG', 'PAGC', 'ANT', 'ANTS', 'ANTC');
            $objQuery->setParameter('estado', $arrayParametros['strEstadoAsig']);
            $objQuery->setParameter('codigos', $arrayCodigos);
            $objQuery->setParameter('intIdPtoSelect', $arrayParametros['intIdPtoSelect']);
            $objQuery->setParameter('intIdFactSelect', $arrayParametros['intIdFactSelect']);

            $objQuery->setDQL($strDql);
            $arrayDatos = $objQuery->getResult();

            if($arrayDatos)
            {
                $objQuery->setDQL($strDqlCc);
                $intTotal = $objQuery->getSingleScalarResult();
            }
            $arrayResultado['registros'] = $arrayDatos;
            $arrayResultado['total'] = $intTotal;
        }
        else
        {
            $arrayResultado = '{"registros":"[]","total":0}';
        }

        return $arrayResultado;
    }
    
    /**
     * getPagosAutomaticosPorCriterios()
     * Obtiene listado de pagos automaticos, mediante filtros enviados como parámteros.
     *
     * costoQuery: 17
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 04-02-2021 
     *
     * @param  array $arrayParametros [
     *                                  "fechaDesde"     => Fecha desde,
     *                                  "fechaHasta"     => Fecha hasta
     *                                  "strEmpresaCod"  => Código de empresa en sesión,
     *                                  "strEmpresaCod"  => Tipo de forma de pago
     *                                ]
     *
     * @return $objResultado - Listado de Retenciones
     */
    public function getPagosAutomaticosPorCriterios($arrayParametros)
    {
        $strFechaDesde         = $arrayParametros['strFechaDesde'] ? $arrayParametros['strFechaDesde'] : '';
        $strFechaHasta         = $arrayParametros['strFechaHasta'] ? $arrayParametros['strFechaHasta'] : '';
        $strCodEmpresa         = $arrayParametros['strEmpresaCod'] ? $arrayParametros['strEmpresaCod'] : '';
        $strTipoFormaPago      = $arrayParametros['strTipoFormaPago'] ? $arrayParametros['strTipoFormaPago'] : '';
        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('ID_PAGO_AUTOMATICO' , 'intIdPagoAutomatico'  , 'integer');
            $objRsm->addScalarResult('RAZON_SOCIAL'       , 'strCliente'           , 'string');
            $objRsm->addScalarResult('NUMERO_REFERENCIA'  , 'strReferencia'        , 'string');            
            $objRsm->addScalarResult('ESTADO'             , 'strEstado'            , 'string');
            $objRsm->addScalarResult('FE_CREACION'        , 'dateFeCreacion'       , 'datetime');
            $objRsm->addScalarResult('USR_CREACION'       , 'strUsrCreacion'       , 'string');

            $strSelect  = "SELECT IPA.ID_PAGO_AUTOMATICO,
                                  IPA.RAZON_SOCIAL,
                                  IPD.NUMERO_REFERENCIA,                                  
                                  IPA.ESTADO,
                                  IPA.FE_CREACION,
                                  IPA.USR_CREACION";

            $strFrom    = " FROM DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CAB IPA "
                        . " JOIN DB_FINANCIERO.INFO_PAGO_AUTOMATICO_DET IPD ON IPD.PAGO_AUTOMATICO_ID = IPA.ID_PAGO_AUTOMATICO ";
            
            $strWhere   = " WHERE IPD.EMPRESA_COD = :strEmpresaCod";

            if(isset($strTipoFormaPago) && $strTipoFormaPago!=='')
            {
                $strWhere   .= " AND IPA.TIPO_FORMA_PAGO = :strTipoFormaPago ";    
            }
           
            $objQuery->setParameter('strEmpresaCod', $strCodEmpresa);
            $objQueryCount->setParameter('strEmpresaCod', $strCodEmpresa);
            
             if(isset($strTipoFormaPago) && $strTipoFormaPago!=='')
            {
                $objQuery->setParameter('strTipoFormaPago', $strTipoFormaPago);
                $objQueryCount->setParameter('strTipoFormaPago', $strTipoFormaPago);
            }           
                
            $strGroupBy = " GROUP BY IPA.ID_PAGO_AUTOMATICO,IPA.RAZON_SOCIAL,IPD.NUMERO_REFERENCIA,IPA.ESTADO,IPA.FE_CREACION,IPA.USR_CREACION"
                        . " ORDER BY IPA.ID_PAGO_AUTOMATICO ASC ";

            if($strFechaDesde!=="" && $strFechaHasta!=="")
            {
                $strFechaDesdeC = strtotime($strFechaDesde);
                $strFechaHastaC = strtotime($strFechaHasta);
                $strWhere .=" AND IPA.FE_CREACION >= :fechaDesde AND IPA.FE_CREACION <= :fechaHasta ";
                $objQuery->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQueryCount->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQuery->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));
                $objQueryCount->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));                
            } 
            
            $strSqlSub   = $strSelect.$strFrom.$strWhere.$strGroupBy;
            $strSqlCount = $strSelectCount.$strFrom.$strWhere.$strGroupBy;

            $strSqlTotal = "SELECT COUNT(CANTIDAD.TOTAL) AS TOTAL FROM ($strSqlCount) CANTIDAD";
            
            $arrayTotalPagosCab = $objQueryCount->setSQL($strSqlTotal)->getSingleScalarResult();

            $strSql .= "SELECT PAGOS.* FROM ($strSqlSub) PAGOS ORDER BY PAGOS.FE_CREACION DESC";
            $arrayPagosCab = $objQuery->setSQL($strSql)->getArrayResult();
            $objResultado['total']     = $arrayTotalPagosCab;
            $objResultado['registros'] = $arrayPagosCab;
        }
        catch(\Exception $e)
        {
            $objResultado = array('total' => 0, 'registros' => array());
        }

        return $objResultado;       
    }
    
    /**
     * procesarRetenciones
     * Función que ejecuta el proceso de generación de pagos por retención según los datos enviados como parámetros.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 26-04-2021 
     *
     * @param  array $arrayParametros [
     *                                  "strIdsRetencionesSelect"  => Ids de retenciones seleccionadas,
     *                                  "strUsrCreacion"           => Usuario de creación,
     *                                  "strCodEmpresa"            => Código empresa,
     *                                  "strPrefijoEmpresa"        => Prefijo empresa,
     *                                  "strIpCreacion"            => Ip de creación
     *      *                         ]
     *
     * @return $objResponse
     */
    public function procesarRetenciones($arrayParametros)
    {
        $strStatus                = "OK";
        $strMensaje               = "";
        $strIdsRetencionesSelect  = $arrayParametros['strIdsRetencionesSelect'] ? $arrayParametros['strIdsRetencionesSelect'] : '';
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : 'telcos';
        $strCodEmpresa            = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa'] : "10";
        $strPrefijoEmpresa        = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa'] : "TN";
        $strIpCreacion            = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "";

        try
        {
            $strStatus  = str_pad($strStatus, 10, " ");
            $strMensaje = str_pad($strMensaje, 5000, " ");
            $strSql  = "BEGIN 
                        DB_FINANCIERO.FNKG_PAGO_AUTOMATICO.P_PROCESAR_RETENCIONES(:Pv_IdsRetSelecionadas,
                                                                                  :Pv_PrefijoEmpresa,
                                                                                  :Pv_EmpresaCod,
                                                                                  :Pv_UsrCreacion,
                                                                                  :Pv_Ip,
                                                                                  :Pv_Status,
                                                                                  :Pv_Mensaje); END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pv_IdsRetSelecionadas', $strIdsRetencionesSelect);
            $objStmt->bindParam('Pv_PrefijoEmpresa', $strPrefijoEmpresa);
            $objStmt->bindParam('Pv_EmpresaCod', $strCodEmpresa);
            $objStmt->bindParam('Pv_UsrCreacion', $strUsrCreacion);
            $objStmt->bindParam('Pv_Ip', $strIpCreacion);
            $objStmt->bindParam('Pv_Status', $strStatus);            
            $objStmt->bindParam('Pv_Mensaje', $strMensaje);
            $objStmt->execute();
            $arrayResultado  = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje);        

        }
        catch(\Exception $e)
        {
            if($this->_em->getConnection()->isTransactionActive())
            {
                $this->_em->getConnection()->rollback();
            }

            $arrayResultado = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje());
        }

        return $arrayResultado;       
    }
    
    
    /**
     * procesaRptRetencionesExistentes
     * Función que genera y envia reporte de retenciones existentes a partir de la lectura de un archivo enviado como parámetro.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 13-09-2021 
     *
     * @param  array $arrayParametros [
     *                                  "strUrlFile"               => Ruta del archivo,
     *                                  "strUsrCreacion"           => Usuario de creación,
     *                                  "strCodEmpresa"            => Código empresa,
     *                                  "strPrefijoEmpresa"        => Prefijo empresa,
     *                                  "strIpCreacion"            => Ip de creación     *      *                         ]
     *
     * @return $objResponse
     */
    public function procesaRptRetencionesExistentes($arrayParametros)
    {
        $strStatus                = "OK";
        $strMensaje               = "";
        $strUrlFile               = $arrayParametros['strUrlFile'] ? $arrayParametros['strUrlFile'] : '';
        $strCodEmpresa            = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa'] : "10";
        $strIpCreacion            = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "";
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : 'telcos';
error_log('URLLLLLLLLLLLLL '.$strUrlFile);
        try
        {
            $strStatus  = str_pad($strStatus, 10, " ");
            $strMensaje = str_pad($strMensaje, 5000, " ");
            $strSql  = "BEGIN 
                        DB_FINANCIERO.FNKG_PAGO_AUTOMATICO.P_PROCESA_RPT_TRIBUTACION(:Pv_UrlFile,
                                                                                  :Pv_EmpresaCod,
                                                                                  :Pv_UsrCreacion,
                                                                                  :Pv_Ip,
                                                                                  :Pv_Status,
                                                                                  :Pv_Mensaje); END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pv_UrlFile', $strUrlFile);
            $objStmt->bindParam('Pv_EmpresaCod', $strCodEmpresa);
            $objStmt->bindParam('Pv_UsrCreacion', $strUsrCreacion);
            $objStmt->bindParam('Pv_Ip', $strIpCreacion);
            $objStmt->bindParam('Pv_Status', $strStatus);            
            $objStmt->bindParam('Pv_Mensaje', $strMensaje);
            $objStmt->execute();
            $arrayResultado  = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje);        

        }
        catch(\Exception $e)
        {
            if($this->_em->getConnection()->isTransactionActive())
            {
                $this->_em->getConnection()->rollback();
            }

            $arrayResultado = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje());
        }

        return $arrayResultado;       
    }
    
    /**
     * getInfoPuntosFacturasCliente()
     * Obtiene listado de puntos y facturas por cliente
     *
     * costoQuery: 10
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 10-05-2022 
     *
     * @param  array $arrayParametros [
     *                                  "personaEmpresaRolId" => Id del cliente
     *                                ]
     *
     * @return $objResultado - Listado de Pagos
     */
    public function getInfoPuntosFacturasCliente($arrayParametros)
    {
        $strFechaDesde         = $arrayParametros['strFechaDesde'] ? $arrayParametros['strFechaDesde'] : '';
        $strFechaHasta         = $arrayParametros['strFechaHasta'] ? $arrayParametros['strFechaHasta'] : '';
        $intCtaContableId      = $arrayParametros['intCtaContableId'] ? $arrayParametros['intCtaContableId'] : "";

        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('ID_PAGO_AUTOMATICO' , 'intIdPagoAutomatico'  , 'integer');
            $objRsm->addScalarResult('BANCO'              , 'strBanco'             , 'string');            
            $objRsm->addScalarResult('ESTADO'             , 'strEstado'            , 'string');
            $objRsm->addScalarResult('FE_CREACION'        , 'dateFeCreacion'       , 'datetime');
            $objRsm->addScalarResult('USR_CREACION'       , 'strUsrCreacion'       , 'string');

            $strSelect  = "SELECT IPA.ID_PAGO_AUTOMATICO,
                                  CONCAT(ACCT.DESCRIPCION,CONCAT('-',ACCT.NO_CTA)) AS BANCO,
                                  IPA.ESTADO,
                                  IPA.FE_CREACION,
                                  IPA.USR_CREACION";

            $strFrom    = " FROM DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CAB IPA,".
                              "  DB_FINANCIERO.ADMI_CUENTA_CONTABLE  ACCT, ".
                              "  DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE ATCC ";

            $strWhere   = " WHERE IPA.CUENTA_CONTABLE_ID  = ACCT.ID_CUENTA_CONTABLE AND ATCC.ID_TIPO_CUENTA_CONTABLE = ACCT.TIPO_CUENTA_CONTABLE_ID".
                          " AND ATCC.DESCRIPCION = :strTipoCuenta AND ACCT.EMPRESA_COD = :strEmpresaCod  ";
                
                
            $strGroupBy = " ORDER BY IPA.ID_PAGO_AUTOMATICO ASC ";

            if($intCtaContableId !== "")
            {
                $strWhere .=" AND IPA.CUENTA_CONTABLE_ID = :intCtaContableId";
                $objQuery->setParameter('intCtaContableId', $intCtaContableId);
                $objQueryCount->setParameter('intCtaContableId', $intCtaContableId);
            }

            if($strFechaDesde!=="" && $strFechaHasta!=="")
            {
                $strFechaDesdeC = strtotime($strFechaDesde);
                $strFechaHastaC = strtotime($strFechaHasta);
                $strWhere .=" AND IPA.FE_CREACION >= :fechaDesde AND IPA.FE_CREACION <= :fechaHasta ";
                $objQuery->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQueryCount->setParameter('fechaDesde', date("Y/m/d", $strFechaDesdeC));
                $objQuery->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));
                $objQueryCount->setParameter('fechaHasta', date("Y/m/d", $strFechaHastaC));                
            }
            $objQuery->setParameter('strTipoCuenta', 'BANCOS');
            $objQueryCount->setParameter('strTipoCuenta', 'BANCOS'); 
            $objQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
            $objQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']); 
            
            $strSqlSub   = $strSelect     .$strFrom.$strWhere.$strGroupBy;
            $strSqlCount = $strSelectCount.$strFrom.$strWhere.$strGroupBy;

            $strSqlTotal = "SELECT COUNT(CANTIDAD.TOTAL) AS TOTAL FROM ($strSqlCount) CANTIDAD";
            
            $arrayTotalPagosCab = $objQueryCount->setSQL($strSqlTotal)->getSingleScalarResult();

            $strSql .= "SELECT PAGOS.* FROM ($strSqlSub) PAGOS ORDER BY PAGOS.FE_CREACION DESC";
            $arrayPagosCab = $objQuery->setSQL($strSql)->getArrayResult();

            $objResultado['total']     = $arrayTotalPagosCab;
            $objResultado['registros'] = $arrayPagosCab;
        }
        catch(\Exception $e)
        {
            $objResultado = array('total' => 0, 'registros' => array());
        }

        return $objResultado;       
    }     
    
}
