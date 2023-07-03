<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDocumentoFinancieroDetRepository extends EntityRepository
{
    /**
     * Devuelve los detalles de notas de debito de un detalle de pago
     * @param int $pagoDetId
     * @param array $estados
     * @return Object
     */    
    public function findNotasDeDebitoPorPagoDetIdPorEstados($pagoDetId,$estados)
    {
        $query = $this->_em->createQuery(
        "SELECT nddet
        FROM 
        schemaBundle:InfoPagoDet pd, 
        schemaBundle:InfoDocumentoFinancieroDet nddet, 
        schemaBundle:InfoDocumentoFinancieroCab ndcab
        WHERE pd.id=nddet.pagoDetId AND 
        nddet.documentoId=ndcab.id AND 
        nddet.pagoDetId=:pagoDetId AND
        ndcab.estadoImpresionFact in(:estados)");     
        $query->setParameter(':estados', $estados);
        $query->setParameter('pagoDetId', $pagoDetId);
        $datos = $query->getResult();
        return $datos;        
    } 
    
     /**
     * Devuelve la informacion referente al pago relacionado al documento
     * ND| NDI | DEV      
     * @param int $idDocumento
     * @return Object
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 25-09-2015
     */   
    public function findPagoRelacionado($idDocumento)
    {
        $query = $this->_em->createQuery(
        "SELECT ipc.numeroPago
        FROM 
            schemaBundle:InfoDocumentoFinancieroDet idfd,
            schemaBundle:InfoPagoDet ipd, 
            schemaBundle:InfoPagoCab ipc
        WHERE 
            idfd.documentoId=:idDocumento 
            AND ipd.id=idfd.pagoDetId 
            AND ipc.id=ipd.pagoId
        ");     
        $query->setParameter('idDocumento', $idDocumento);
        $datos = $query->getResult();
        return $datos;        
    }
    
    /**
     * Obtiene los detalles de la factura o Nota de credito ordenadas de forma descendente por el PuntoId, ProductoId y PrecioVenta
     * 
     * @param array $arrayParametros['intIdDocumento'         Id del Documento]
     * 
     * @return array $arrayDetallesFactura
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 20-07-2017
     */
    public function getDetallesDelDocumento( $arrayParametros )
    {   
        $arrayDetallesFactura = array();
        
        $objQuery      = $this->_em->createQuery();
         
        $strSelect  = "SELECT IDFD ";
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroDet IDFD ";
        $strWhere   = "WHERE  IDFD.documentoId = :intIdDocumento ";
        $strOrderBy = " ORDER BY IDFD.precioVentaFacproDetalle DESC, ".
                      " IDFD.puntoId    DESC, ".
                      " IDFD.productoId DESC ";
        
        
        $objQuery->setParameter('intIdDocumento', $arrayParametros['intIdDocumento']);
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $objQuery->setDQL($strSql);
       
        
        $arrayDetallesFactura = $objQuery->getResult();
        
        return $arrayDetallesFactura;
    }

    /**
     * Obtiene los detalles financieros de las facturas
     * @param array $arrayFacturas  IdDocumentos a buscar
     * 
     * @return array $arrayDetallesFinancierosFacturas Detalles de Facturas
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 22-01-2021
     * Costo Query: 19
     * 
     * @author Gustavo Narea <gnarea@telconet.ec> Se toma en cuenta aquellos puntos que no tengan vendedores.
     * @version 1.1 15-03-2021
     * Costo Query: 34
     */
    public function getDetallesFinancierosFacturas($arrayParametros)
    {
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
        $objBuilder                  = new ResultSetMappingBuilder($this->_em);
        $objQuery                    = $this->_em->createNativeQuery(null, $objBuilder);
        $strSqlSelect = "SELECT SUBSTR (IDFC.FE_CREACION, 1, 10) FE_CREACION, IOG.NOMBRE_OFICINA, ATDF.CODIGO_TIPO_DOCUMENTO, 
                        IDFC.ID_DOCUMENTO, SUBSTR(TO_CHAR(IDFD.OBSERVACIONES_FACTURA_DETALLE),0,1000) OBSERVACIONES_FACTURA_DETALLE, 
                        IDFC.USR_CREACION, IPER.RAZON_SOCIAL, IPER.NOMBRES, 
                        IPER.APELLIDOS, IPTO.LOGIN, PEVE.NOMBRES ||' '|| PEVE.APELLIDOS VENDEDOR, IDFD.DESCUENTO_FACPRO_DETALLE, 
                        IDFD.PRECIO_VENTA_FACPRO_DETALLE*IDFD.CANTIDAD SUBTOTAL ";
        $strSqlSelImp = "DECODE  ( (SELECT TIPO_IMPUESTO 
        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP A 
        INNER JOIN ADMI_IMPUESTO B ON A.IMPUESTO_ID = B.iD_IMPUESTO 
        WHERE DETALLE_DOC_ID = idfd.ID_DOC_DETALLE
        AND TIPO_IMPUESTO = 'IVA' ),'IVA', (SELECT VALOR_IMPUESTO 
                                             FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP A 
                                             INNER JOIN ADMI_IMPUESTO B ON A.IMPUESTO_ID = B.iD_IMPUESTO 
                                             WHERE DETALLE_DOC_ID = idfd.ID_DOC_DETALLE
                                             AND TIPO_IMPUESTO = 'IVA'), 0) IVA,
        DECODE  ( (SELECT TIPO_IMPUESTO 
        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP A 
        INNER JOIN ADMI_IMPUESTO B ON A.IMPUESTO_ID = B.iD_IMPUESTO 
        WHERE DETALLE_DOC_ID = idfd.ID_DOC_DETALLE
        AND TIPO_IMPUESTO = 'ICE'),'ICE', (SELECT VALOR_IMPUESTO 
                                             FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP A 
                                             INNER JOIN ADMI_IMPUESTO B ON A.IMPUESTO_ID = B.iD_IMPUESTO 
                                             WHERE DETALLE_DOC_ID = idfd.ID_DOC_DETALLE
                                             AND TIPO_IMPUESTO = 'ICE'), 0) ICE";
        
        $strSqlBody = "FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC
                        LEFT OUTER JOIN (SELECT * FROM (SELECT IDFD.*, 
                                                                ROW_NUMBER() OVER 
                                                                (PARTITION BY DOCUMENTO_ID ORDER BY PRECIO_VENTA_FACPRO_DETALLE DESC) RN 
                                                         FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFD) WHERE RN = 1 ) 
                                                                                 DFD ON DFD.DOCUMENTO_ID      = IDFC.ID_DOCUMENTO
                        INNER JOIN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFD ON IDFC.ID_DOCUMENTO = IDFD.DOCUMENTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ON IOG.ID_OFICINA=IDFC.OFICINA_ID
                        INNER JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF ON ATDF.ID_TIPO_DOCUMENTO = IDFC.TIPO_DOCUMENTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_PUNTO IPTO  ON IPTO.ID_PUNTO = IDFC.PUNTO_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERL ON PERL.ID_PERSONA_ROL = IPTO.PERSONA_EMPRESA_ROL_ID
                        INNER JOIN DB_COMERCIAL.INFO_PERSONA IPER ON IPER.ID_PERSONA = PERL.PERSONA_ID
                        LEFT JOIN DB_COMERCIAL.INFO_PERSONA PEVE  ON PEVE.LOGIN = IPTO.USR_VENDEDOR";

        $strSqlWhere = "WHERE IDFC.NUMERO_FACTURA_SRI    IS NULL
                        AND IDFC.ESTADO_IMPRESION_FACT =   :estado
                        AND  ATDF.CODIGO_TIPO_DOCUMENTO IN (:codigoTipoDocumento) 
                        AND  IOG.EMPRESA_ID             =   :empresaId";

        if($strFechaDesde != "" && $strFechaDesde != null)
        {
            $strSqlWhere .= " AND IDFC.FE_CREACION >= :fe_desde";
            $objQuery->setParameter('fe_desde', date('Y/m/d', strtotime($strFechaDesde)));
        }

        if($strFechaHasta != "" && $strFechaHasta != null)
        {
            $strSqlWhere .= " AND IDFC.FE_CREACION < TO_DATE(:fe_hasta, 'yyyy/mm/dd') + 1";
            $objQuery->setParameter('fe_hasta', $strFechaHasta);
        }

        if($strUsrCreacion != "" && $strUsrCreacion != null)
        {
            $strSqlWhere .= " AND IDFC.USR_CREACION= :strUsrCreacion";
            $objQuery->setParameter('strUsrCreacion', $strUsrCreacion);
        }
        
        if($intIdCliente != "" && is_numeric($intIdCliente))
        {
            $strSqlWhere .= " AND IPER.ID_PERSONA= :id_cliente";
            $objQuery->setParameter('id_cliente', $intIdCliente);
        }

        if($intPtoCliente != "" && is_numeric($intPtoCliente))
        {
            $strSqlWhere .= " AND IPTO.ID_PUNTO= :ptocliente";
            $objQuery->setParameter('ptocliente', $intPtoCliente);
        }

        if(!empty($intIdOficina))
        {
            $strSqlWhere .= " AND IDFC.OFICINA_ID = :intIdOficina";
            $objQuery->setParameter('intIdOficina', $intIdOficina);
        }
                
    
        $objBuilder->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objBuilder->addScalarResult('NOMBRE_OFICINA', 'nombreOficina', 'string');
        $objBuilder->addScalarResult('CODIGO_TIPO_DOCUMENTO', 'codigoTipoDocumento', 'string');
        $objBuilder->addScalarResult('ID_DOCUMENTO', 'id', 'integer');
        $objBuilder->addScalarResult('OBSERVACIONES_FACTURA_DETALLE', 'detalleFactura', 'string');
        $objBuilder->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objBuilder->addScalarResult('RAZON_SOCIAL', 'razonSocial', 'string');
        $objBuilder->addScalarResult('NOMBRES', 'nombres', 'string');
        $objBuilder->addScalarResult('APELLIDOS', 'apellidos', 'string');
        $objBuilder->addScalarResult('CODIGO_TIPO_DOCUMENTO', 'codigoTipoDocumento', 'string');
        $objBuilder->addScalarResult('LOGIN', 'login', 'string');
        $objBuilder->addScalarResult('VENDEDOR', 'vendedor', 'string');
        $objBuilder->addScalarResult('DESCUENTO_FACPRO_DETALLE', 'descuento', 'float');
        $objBuilder->addScalarResult('SUBTOTAL', 'subtotal', 'float');
        $objBuilder->addScalarResult('IVA', 'iva', 'float');
        $objBuilder->addScalarResult('ICE', 'ice', 'float');

        $objQuery->setParameter(':estado', 'Pendiente');
        $objQuery->setParameter(':codigoTipoDocumento', $strTipoDoc);
        $objQuery->setParameter(':empresaId', $intEmpresaId);
        
        $objQuery->setSQL("$strSqlSelect, $strSqlSelImp  $strSqlBody $strSqlWhere");
        return $objQuery->getResult();
    }


    /**
     * @author Arcángel Farro <lfarro@telconet.ec> Creación de método de consulta del detalle de facturas por DocumentoId.
     * @version 1.1 12-05-2023
     * Costo Query: 34
     */
    public function obtieneDatosDocumento($intIdDocumento)
    {    
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        // Query
        $strSql   = "SELECT * FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET idfd
                    WHERE DOCUMENTO_ID = :intIdDocumento 
                    ORDER BY FE_CREACION ASC";
        $objQuery->setParameter("intIdDocumento", $intIdDocumento);

        // Mapeo de registros
        $objRsm->addScalarResult('EMPRESA_ID', 'empresaId', 'integer');
        $objRsm->addScalarResult('PLAN_ID', 'planId', 'integer');
        $objRsm->addScalarResult('ID_DOC_DETALLE', 'id', 'integer');
        $objRsm->addScalarResult('DOCUMENTO_ID', 'documentoId', 'integer');
        $objRsm->addScalarResult('PRODUCTO_ID', 'productoId', 'integer');
        $objRsm->addScalarResult('PUNTO_ID', 'puntoId', 'integer');
        $objRsm->addScalarResult('CANTIDAD', 'cantidad', 'integer');
        $objRsm->addScalarResult('OFICINA_ID', 'oficinaId', 'integer');
        $objRsm->addScalarResult('PRECIO_VENTA_FACPRO_DETALLE', 'precioVentaFacproDetalle', 'float');
        $objRsm->addScalarResult('PORCETANJE_DESCUENTO_FACPRO', 'porcetanjeDescuentoFacpro', 'float');
        $objRsm->addScalarResult('DESCUENTO_FACPRO_DETALLE', 'descuentoFacproDetalle', 'float');
        $objRsm->addScalarResult('VALOR_FACPRO_DETALLE', 'valorFacproDetalle', 'float');
        $objRsm->addScalarResult('COSTO_FACPRO_DETALLE', 'costoFacproDetalle', 'float');
        $objRsm->addScalarResult('OBSERVACIONES_FACTURA_DETALLE', 'observacionesFacturaDetalle', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('FE_ULT_MOD', 'feUltMod', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('USR_ULT_MOD', 'usrUltMod', 'string');
        $objRsm->addScalarResult('MOTIVO_ID', 'motivoId', 'integer');
        $objRsm->addScalarResult('PAGO_DET_ID', 'pagoDetId', 'integer');
        $objRsm->addScalarResult('SERVICIO_ID', 'servicioId', 'integer');
        
        // Ejecuto sentencia y asigno valores a mi array
        $objQuery->setSQL($strSql);
        
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }
}
