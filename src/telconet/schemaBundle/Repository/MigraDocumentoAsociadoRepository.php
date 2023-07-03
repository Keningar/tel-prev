<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MigraDocumentoAsociadoRepository extends EntityRepository
{

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
     */     
    public function getSecuenceMigraDocAso($arrayRequest)
    {
        $arrayResponse = array();
        try
        {
            $strQuery  = "SELECT NAF47_TNET.TRANSA_ID.MIGRA_CK (:strCodEmpresa) ID_DOC_ASO FROM DUAL";

            $objStmt = $this->_em->getConnection()->prepare($strQuery);
            $objStmt->bindValue('strCodEmpresa', $arrayRequest['strCodEmpresa']);
            $objStmt->execute();

            $arrayResponse = $objStmt->fetchAll();
            error_log('MigraDocumentoAsociadoRepository -> getSecuenceMigraDocAso : ' . json_encode($arrayResponse));
        }
        catch(\Exception $e)
        {
            error_log('MigraDocumentoAsociadoRepository -> getSecuenceMigraDocAso : '. $e->getMessage());
        }         
        return $arrayResponse;
    }

    /**
    * reversaContPAL, reversa la contabilidad de un pago en linea
    *
    * @param mixed $arrayRequest    [
    *                               'strCodEmpresa'   => Codigo de empresa,
    *                               'strPagosDet'     => Id detalle de pagos separados por ,
    *                               'strUserCreacion' => Usuario creacion
    *                               ]
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 31-05-2019
    */
    public function reversaContPAL($arrayRequest)
    {
        $arrayResponse = array();
        $strRespuesta = str_pad($strRespuesta, 50, " ");
        try
        {
            if (!empty($arrayRequest))
            {
                $strSql = "BEGIN NAF47_TNET.ARCK_PAL.P_REVERSA_CONT_PAL(:strIdPagoDet, :strCodEmpresa, :strUsrCreacion, :strCode); END;";
                $stmt = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('strIdPagoDet', $arrayRequest['strIdPagoDet']);
                $stmt->bindParam('strCodEmpresa', $arrayRequest['strCodEmpresa']);
                $stmt->bindParam('strUsrCreacion', $arrayRequest['strUsrCreacion']);
                $stmt->bindParam('strCode', $strRespuesta);
                $stmt->execute();
                $strRespuesta  = trim($strRespuesta);
                $arrayResponse = array('strCode' => $strRespuesta);
            }
        }
        catch(\Exception $ex)
        {
            error_log('Error en MigraDocumentoAsociadoRepository - reversaContPAL: '. $ex->getMessage());
            $arrayResponse = array('strCode' => '001');
        }

        return $arrayResponse;
    }
    
}
