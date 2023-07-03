<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Documentación para la clase 'InfoComprobanteElectronicoRepository'.
 *
 * La clase InfoComprobanteElectronicoRepository contiene metodos de consulta para la entidad InfoComprobanteElectronico
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 01-10-2014
 */
class InfoComprobanteElectronicoRepository extends EntityRepository
{

    /**
     * Documentación para el método 'getClaveAccesobyId'.
     * Obtiene la clave de acceso y el ruc del comprobante
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function getClaveAccesobyId($intIdDocumento)
    {
        $dqlObtieneMensajes = $this->_em->createQuery("
                                           SELECT ice.ruc, ice.claveAcceso, ice.estado
                                           FROM schemaBundle:InfoComprobanteElectronico ice
                                           WHERE ice.documentoId = :intIdDocumento
                                         ");
        $dqlObtieneMensajes->setParameter('intIdDocumento', $intIdDocumento);
        $arrayDatos = $dqlObtieneMensajes->getResult();
        return $arrayDatos;
    }//getClaveAccesobyId
    
    /**
     * Documentación para el método 'getCompruebaEstadoComprobante'.
     * Obtiene 1 si el comprobante puede ser actualizado, solo podra actualizar el comprobante
     * siempre y cuando este en estado 0
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function getCompruebaEstadoComprobante($intIdDocumento, $intEstado)
    {
        $dqlPuedeActualizar = $this->_em->createQuery("
                                           SELECT ice.ruc
                                           FROM schemaBundle:InfoComprobanteElectronico ice
                                           WHERE ice.documentoId = :intIdDocumento
                                            AND  ice.estado      = :intEstado
                                         ");
        $dqlPuedeActualizar->setParameter('intIdDocumento', $intIdDocumento);
        $dqlPuedeActualizar->setParameter('intEstado', $intEstado);
        $arrayDatos = $dqlPuedeActualizar->getResult();
        return $arrayDatos;
    }//getCompruebaEstadoComprobante

    /**
     * Documentación para el método 'getCompElectronicosPdfXml'.
     * Este metodo retorna los documentos PDF y XML de los comprobantes electronicos
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     */
    public function getCompElectronicosPdfXml($intIdDocumento)
    {
        try
        {
            $dqlComprobantesElectronicos = $this->_em->createQuery("
                                           SELECT ice.comprobanteElectDevuelto, ice.comprobanteElectronicoPdf
                                           FROM schemaBundle:InfoComprobanteElectronico ice
                                           WHERE ice.documentoId = :intIdDocumento
                                         ");
            $dqlComprobantesElectronicos->setParameter('intIdDocumento', $intIdDocumento);
            $objDocumentosElectronicos = $dqlComprobantesElectronicos->getResult();
            $arrayDocumento = array('xml' => html_entity_decode($objDocumentosElectronicos[0]['comprobanteElectDevuelto']),
                                    'pdf' => html_entity_decode($objDocumentosElectronicos[0]['comprobanteElectronicoPdf']));
        }
        catch(\Exception $ex)
        {
            $arrayDocumento['txt'] = 'Error en el metodo getCompElectronicosPdfXml - ' . $ex->getMessage();
        }
        return $arrayDocumento;
    }//getCompElectronicosPdfXml

    /**
     * EL metodo getResumenCompElectronicos obtiene el total de las facturas enviadas, actualizadas, creadas, autorizadas
     * anuladas que se generan en la info_comprobante_electronico
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 06-11-2014
     * @param type $arrayParametros     Recibe los parametros para la consulta
     * @return array                    Retorna un array con el resultado de la consulta
     */
    public function getResumenCompElectronicos($arrayParametros)
    {
        $strResumenComprobantesElectronicos = "SELECT
                                                 SUM(PENDIENTES_ENVIO_SRI) AS \"PENDIENTES ENVIO SRI\",
        SUM(PROCESANDO) AS \"PROCESANDO\",
        SUM(RECHAZADAS) AS RECHAZADAS,
        SUM(AUTORIZADAS) AS AUTORIZADAS,
        SUM(ACTUALIZADAS) AS ACTUALIZADAS,
        SUM(CON_ERRORES) AS \"CON ERRORES\",
        SUM(TOTAL) AS TOTAL,
        MES,
        FECHA
      FROM (
        SELECT
          CASE WHEN ICE.ESTADO = 9 THEN COUNT(1) ELSE 0 END AS PENDIENTES_ENVIO_SRI,
          CASE WHEN ICE.ESTADO = 1 THEN COUNT(1) ELSE 0 END AS PROCESANDO,
          CASE WHEN ICE.ESTADO = 4 THEN COUNT(1) ELSE 0 END AS RECHAZADAS,
          CASE WHEN ICE.ESTADO = 5 THEN COUNT(1) ELSE 0 END AS AUTORIZADAS,
          CASE WHEN ICE.ESTADO = 10 THEN COUNT(1) ELSE 0 END AS ACTUALIZADAS,
          CASE WHEN ICE.ESTADO = 0 THEN COUNT(1) ELSE 0  END AS CON_ERRORES,
          COUNT(1) AS TOTAL,
          TO_CHAR(ICE.FE_CREACION, 'MONTH', 'NLS_DATE_LANGUAGE=SPANISH') AS MES,
          TO_CHAR(ICE.FE_CREACION, 'MM-YYYY') AS FECHA
        FROM
          DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC
          INNER JOIN DB_FINANCIERO.INFO_COMPROBANTE_ELECTRONICO ICE
            ON IDFC.ID_DOCUMENTO = ICE.DOCUMENTO_ID
          INNER JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATD
            ON IDFC.TIPO_DOCUMENTO_ID = ATD.ID_TIPO_DOCUMENTO
            INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IFG ON
    IDFC.OFICINA_ID = IFG.ID_OFICINA
        WHERE
          ATD.ESTADO = 'Activo'
          AND ATD.CODIGO_TIPO_DOCUMENTO = :strTipoDocumento
          AND IDFC.OFICINA_ID = NVL2(:intIdOficina, :intIdOficina, IDFC.OFICINA_ID)
          AND ICE.FE_CREACION >= TO_DATE(:strFeCreacionInicio, 'DD-MM-YYYY HH24:MI:SS')
          AND ICE.FE_CREACION <= TO_DATE(:strFeCreacionFin, 'DD-MM-YYYY HH24:MI:SS')
          AND IFG.EMPRESA_ID=:strCodEmpresa
        GROUP BY
          ICE.ESTADO,
          TO_CHAR(ICE.FE_CREACION, 'MONTH', 'NLS_DATE_LANGUAGE=SPANISH'),
          TO_CHAR(ICE.FE_CREACION, 'MM-YYYY')
      )
      GROUP BY
        MES,
        FECHA
      ORDER BY
        FECHA DESC";
        $stmt = $this->_em->getConnection()->prepare($strResumenComprobantesElectronicos);
        $stmt->bindValue('intEstado',           $arrayParametros['intEstado']);
        $stmt->bindValue('strTipoDocumento',    $arrayParametros['strTipoDocumento']);
        $stmt->bindValue('strFeCreacionInicio', $arrayParametros['strFeCreacionInicio']);
        $stmt->bindValue('strFeCreacionFin',    $arrayParametros['strFeCreacionFin']);
        $stmt->bindValue('intIdOficina',        $arrayParametros['intOficinaId']);
        $stmt->bindValue('strCodEmpresa',        $arrayParametros['strCodEmpresa']);
        $stmt->execute();
        $arraResult = $stmt->fetchAll();
        return $arraResult;
    }
    
    /**
     * EL metodo getDocumentosNoCreados obtiene los documentos que no fueron creados
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-12-2014
     * @return array Retorna el array de los documentos no creados
     */
    public function getDocumentosNoCreados()
    {
        $strResumenComprobantesElectronicos = "WITH
                                                COMPOBANTE_ERROR AS
                                                (
                                                  SELECT DISTINCT
                                                    TRIM(REPLACE(IE.DETALLE_ERROR,
                                                    'Los valores de la factura no cuadran ID_DOCUMENTO: ', '')) DETALLE_ERROR
                                                  FROM
                                                    INFO_ERROR IE
                                                  WHERE
                                                    IE.PROCESO = 'VALOR <> 0'
                                                )
                                              SELECT
                                                IP.LOGIN,
                                                IDFC.NUMERO_FACTURA_SRI,
                                                IDFC.VALOR_TOTAL,
                                                IDFC.ESTADO_IMPRESION_FACT,
                                                TO_CHAR(IDFC.FE_CREACION, 'DD-MM-YYY') FE_CREACION,
                                                TO_CHAR(IDFC.FE_EMISION, 'DD-MM-YYY') FE_EMISION,
                                                IDFC.USR_CREACION,
                                                IDFC.RECURRENTE,
                                                ATDF.NOMBRE_TIPO_DOCUMENTO
                                              FROM
                                                COMPOBANTE_ERROR CE,
                                                INFO_DOCUMENTO_FINANCIERO_CAB IDFC,
                                                INFO_PUNTO IP,
                                                ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF
                                              WHERE
                                                IDFC.ID_DOCUMENTO        = CE.DETALLE_ERROR
                                              AND ATDF.ID_TIPO_DOCUMENTO = IDFC.TIPO_DOCUMENTO_ID
                                              AND IDFC.PUNTO_ID          = IP.ID_PUNTO
                                              AND IDFC.ESTADO_IMPRESION_FACT <> 'Eliminado'
                                              AND NOT EXISTS
                                                (
                                                  SELECT
                                                    NULL
                                                  FROM
                                                    INFO_COMPROBANTE_ELECTRONICO ICE
                                                  WHERE
                                                    ICE.DOCUMENTO_ID = CE.DETALLE_ERROR
                                                )";
        $stmt = $this->_em->getConnection()->prepare($strResumenComprobantesElectronicos);
        $stmt->execute();
        $arraResult = $stmt->fetchAll();
        return $arraResult;
    }//getDocumentosNoCreados

    /**
     * getErrorMensajesFacturas, Obtiene los mensajes de error de un documento financiero
     * @param type $arrayParametros     Recibe el Id Documento
     * @return array    Retorna un array con los mensajes de error del documento financiero
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 06-05-015
     */
    public function getErrorMensajesFacturas($arrayParametros)
    {   
        $arrayDatos = array();
        try
        {
            if(isset($arrayParametros['intIdDocumento']))
            {
                if(is_numeric($arrayParametros['intIdDocumento']))
                {
                    if(isset($arrayParametros['intStart']))
                    {
                        if(is_numeric($arrayParametros['intStart']))
                        {
                            if(isset($arrayParametros['intLimit']))
                            {
                                if(is_numeric($arrayParametros['intLimit']))
                                {
                                    $arrayDatos['strMensajeError'] = '';
                                    $strSelect                     = "SELECT ie ";
                                    $strCount                      = "SELECT count(ie.id) intTotalMensajes ";
                                    $strQueryMensajes              = "FROM schemaBundle:InfoError ie
                                                                       WHERE ie.detalleError LIKE :intIdDocumento
                                                                       ORDER BY ie.feCreacion DESC";
                                    $strObtieneMensajes            = $strSelect.$strQueryMensajes;
                                    $dqlObtieneMensajes            = $this->_em->createQuery($strObtieneMensajes);
                                    $dqlObtieneMensajes->setParameter('intIdDocumento', '%'.$arrayParametros['intIdDocumento'].'%');
                                    $dqlObtieneMensajes->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit']);
                                    $arrayDatos['arrayMensajes'] = $dqlObtieneMensajes->getResult();

                                    $strObtieneCountMensajes = $strCount.$strQueryMensajes;
                                    $dqlObtieneCountMensajes = $this->_em->createQuery($strObtieneCountMensajes);
                                    $dqlObtieneCountMensajes->setParameter('intIdDocumento', '%'.$arrayParametros['intIdDocumento'].'%');
                                    $arrayDatos['intTotalMensajes'] = $dqlObtieneCountMensajes->getSingleResult();
                                }
                                else
                                {
                                    $arrayDatos['strMensajeError'] = 'El limite del pagineo debe ser de tipo numerico.';
                                }
                            }
                            else
                            {
                                $arrayDatos['strMensajeError'] = 'No se esta definiendo el limite del pagineo.';
                            }
                        }
                        else
                        {
                            $arrayDatos['strMensajeError'] = 'El inicio del pagineo debe ser de tipo numerico.';
                        }
                    }
                    else
                    {
                        $arrayDatos['strMensajeError'] = 'No se esta definiendo el inicio del pagineo.';
                    }
                }
                else
                {
                    $arrayDatos['strMensajeError'] = 'El idDocumento debe ser de tipo numerico.';
                }
            }
            else
            {
                $arrayDatos['strMensajeError'] = 'No se esta definiendo un idDocumento.';
            }
        }
        catch(\Exception $ex)
        {
            $arrayDatos['strMensajeError'] = $ex->getMessage().' '.$ex->getTrace();
        }
        return $arrayDatos;
    }//getErrorMensajesFacturas
    
    /**
     * Documentación para el método 'getVerificaComprobanteByEstado'.
     * Obtiene 1 si el comprobante rechazado puede ser actualizado, solo podra actualizar el comprobante
     * para la TN si el estado 4 y para MD si el estado es 4 o 11
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     * @param  Array   $arrayEstados   Recibe estados del Documento
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 29-06-2017
     */
    public function getVerificaComprobanteByEstado($intIdDocumento, $arrayEstados)
    {
        $objQuery = $this->_em->createQuery("
                                           SELECT ice.ruc
                                           FROM schemaBundle:InfoComprobanteElectronico ice
                                           WHERE ice.documentoId = :intIdDocumento
                                            AND  ice.estado      IN (:arrayEstados)
                                         ");
        $objQuery->setParameter('intIdDocumento',   $intIdDocumento);
        $objQuery->setParameter('arrayEstados',     $arrayEstados);
        
        $arrayDatos = $objQuery->getResult();
        return $arrayDatos;
    }//getVerificaComprobanteByEstado
    
    /**
     * Método que obtiene el remitente de DB_COMPROBANTES por empresa.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 18-04-2018
     *
     */
    public function obtieneDatosEmpresaComprobantes($arrayParametros)
    {
        $strSql = "SELECT
                    N.CORREO_NOTIFICACION,
                    EMP.NOMBRE AS NOMBRE_EMPRESA
                   FROM
                        DB_COMPROBANTES.ADMI_EMPRESA EMP,
                        DB_COMPROBANTES.INFO_NOTIFICACION N
                    WHERE
                        EMP.RUC = :strRuc
                        AND   EMP.ID_EMPRESA = N.EMPRESA_ID";
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam("strRuc", $arrayParametros["strRuc"]);
        $objStmt->execute();
        $arrayResult = $objStmt->fetchAll();
        return $arrayResult;
    }

}
