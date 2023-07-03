<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoPlanProdPromocionRepository extends EntityRepository
{
    /**
     * getPromoValidasOlts()
     * Obtiene la promocion activa para las nuevas Olts.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 08-06-2022
     * 
     * @param Array $arrayParametros["intIdPromocion" => Codigo de promocion,
     *                               "intIdEmpresa"   => Codigo de la empresa,
     *                               "strEstado       => Estado permitido para los registros"]
     * 
     * @return $objQuery - Lista de Planes y line profiels de la promocion
     */
    public function getLinesProfilePromociones($arrayParametros)
    {
        $intPromocion = $arrayParametros['intIdPromocion'];
        $intIdEmpresa = $arrayParametros['intIdEmpresa'];
        $strEstado    = $arrayParametros['strEstado'];

        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('PLAN_ANTERIOR', 'planAnterior', 'string');
        $objResultSet->addScalarResult('LINE_ANTERIOR', 'lineAnterior', 'string');
        $objResultSet->addScalarResult('PLAN_NUEVO', 'planNuevo', 'string');
        $objResultSet->addScalarResult('LINE_NUEVO', 'lineNuevo', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSql = "SELECT ATPP.PLAN_ID AS PLAN_ANTERIOR,
                        (SELECT IPPC.VALOR 
                        FROM DB_COMERCIAL.INFO_PLAN_CAB IPC,
                             DB_COMERCIAL.INFO_PLAN_DET IPD,
                             DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT IPPC 
                        WHERE IPC.ID_PLAN = IPD.PLAN_ID
                        AND IPD.ID_ITEM = IPPC.PLAN_DET_ID
                        AND IPC.EMPRESA_COD = :intIdEmpresa
                        AND IPPC.PRODUCTO_CARACTERISITICA_ID = (
                                SELECT ID_PRODUCTO_CARACTERISITICA
                                FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                                WHERE PRODUCTO_ID = (
                                    SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                                    WHERE DESCRIPCION_PRODUCTO = 'INTERNET DEDICADO'
                                    AND EMPRESA_COD = :intIdEmpresa
                                    AND ESTADO = :strEstado)
                                AND CARACTERISTICA_ID = (
                                    SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                                    WHERE DESCRIPCION_CARACTERISTICA = 'LINE-PROFILE-NAME'
                                    AND ESTADO = :strEstado))
                        AND IPPC.ESTADO = :strEstado
                        AND IPC.ID_PLAN = ATPP.PLAN_ID) LINE_ANTERIOR,
                        ATPP.PLAN_ID_SUPERIOR AS PLAN_NUEVO,
                        (SELECT IPPC.VALOR 
                        FROM DB_COMERCIAL.INFO_PLAN_CAB IPC,
                             DB_COMERCIAL.INFO_PLAN_DET IPD,
                             DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT IPPC 
                        WHERE IPC.ID_PLAN = IPD.PLAN_ID
                        AND IPD.ID_ITEM = IPPC.PLAN_DET_ID
                        AND IPC.EMPRESA_COD = :intIdEmpresa
                        AND IPPC.PRODUCTO_CARACTERISITICA_ID = (
                            SELECT ID_PRODUCTO_CARACTERISITICA
                            FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                            WHERE PRODUCTO_ID = (
                                SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                                WHERE DESCRIPCION_PRODUCTO = 'INTERNET DEDICADO'
                                AND EMPRESA_COD = :intIdEmpresa
                                AND ESTADO = :strEstado)
                            AND CARACTERISTICA_ID = (
                                SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                                WHERE DESCRIPCION_CARACTERISTICA = 'LINE-PROFILE-NAME'
                                AND ESTADO = :strEstado))
                        AND IPPC.ESTADO = :strEstado
                        AND IPC.ID_PLAN = ATPP.PLAN_ID_SUPERIOR) LINE_NUEVO
                    FROM ADMI_TIPO_PLAN_PROD_PROMOCION ATPP
                    WHERE ATPP.TIPO_PROMOCION_ID IN (
                        SELECT ATP.ID_TIPO_PROMOCION
                        FROM ADMI_GRUPO_PROMOCION AGP, ADMI_TIPO_PROMOCION ATP
                        WHERE AGP.ID_GRUPO_PROMOCION = ATP.GRUPO_PROMOCION_ID
                        AND AGP.ID_GRUPO_PROMOCION = :intPromocion)";
        $objQuery->setParameter('intPromocion', $intPromocion);
        $objQuery->setParameter('intIdEmpresa', $intIdEmpresa);
        $objQuery->setParameter('strEstado', $strEstado);
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
}
