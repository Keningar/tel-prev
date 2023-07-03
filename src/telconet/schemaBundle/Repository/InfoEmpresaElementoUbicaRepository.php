<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class InfoEmpresaElementoUbicaRepository extends EntityRepository
{

    /**
    * getBeneficiariosOlt, la cantida de beneficiarios de un canton, provincio o sector,
    *       incluidos por los planes que necesite.
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.0 13-05-2022
    *
    * @param array $arrayParametros["intJurisdiccion"  => Id de jurisdiccion,
    *                               "intIdCanton"      => Id del canton,
    *                               "intIdParroquia"   => Id de la parroquia,
    *                               "arrayIdSector"    => Arreglo de id sectores,
    *                               "arrayLineProfile" => Arreglo de id planes,
    *                               "intIdEmpresa"     => Codigo de la empresa,
    *                               "strProducto       => Producto del plan
    *                               "strCaracteristica => Caracteristica del servicio
    *                               "strEstadoActivo   => Estado de los productos]
    *
    * @return Response Cantidad de beneficiarios dependiendo de los parametros.
    */
    public function getBeneficiariosOlt($arrayParametros)
    {
        $intJurisdiccion   = $arrayParametros['intJurisdiccion'];
        $intIdCanton       = $arrayParametros['intIdCanton'];
        $intIdParroquia    = $arrayParametros['intIdParroquia'];
        $arrayIdSectores   = $arrayParametros['arrayIdSectores'];
        $arrayLineProfile  = $arrayParametros['arrayLineProfile'];
        $intIdEmpresa      = $arrayParametros['intIdEmpresa'];
        $strProducto       = $arrayParametros['strProducto'];
        $strCaracteristica = $arrayParametros['strCaracteristica'];
        $strEstadoActivo   = $arrayParametros['strEstadoActivo'];

        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSelect   = "SELECT COUNT(*) CANTIDAD ";
        $strFrom     = "FROM DB_COMERCIAL.INFO_SERVICIO_TECNICO IST, DB_COMERCIAL.INFO_PLAN_CAB IPC,
                            DB_COMERCIAL.INFO_SERVICIO CIS, DB_INFRAESTRUCTURA.INFO_ELEMENTO IIE,
                            DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA IEEU, DB_INFRAESTRUCTURA.INFO_UBICACION IU ";
        $strWhere    = "WHERE CIS.ID_SERVICIO = IST.SERVICIO_ID
                        AND IST.ELEMENTO_ID = IIE.ID_ELEMENTO
                        AND CIS.PLAN_ID = IPC.ID_PLAN
                        AND IIE.ID_ELEMENTO = IEEU.ELEMENTO_ID
                        AND IEEU.UBICACION_ID = IU.ID_UBICACION
                        AND IIE.MODELO_ELEMENTO_ID IN (
                            SELECT AME.ID_MODELO_ELEMENTO
                            FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO ATE,DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME
                            WHERE ATE.ID_TIPO_ELEMENTO = AME.TIPO_ELEMENTO_ID
                            AND ATE.NOMBRE_TIPO_ELEMENTO = 'OLT'
                            AND ATE.ESTADO = 'Activo'
                            AND AME.ESTADO = 'Activo')
                        AND IIE.ESTADO IN ('Activo', 'Modificado')
                        AND CIS.PLAN_ID IS NOT NULL
                        AND CIS.ESTADO = 'Activo' ";
        if ($arrayIdSectores <> 0)
        {
            $strWhere = $strWhere. "AND IU.PARROQUIA_ID IN (
                        SELECT PA.ID_PARROQUIA FROM DB_GENERAL.ADMI_PARROQUIA PA, DB_GENERAL.ADMI_SECTOR ASE
                        WHERE PA.ID_PARROQUIA = ASE.PARROQUIA_ID
                        AND ASE.ID_SECTOR in (:arraySectores)) ";
            $objNtvQuery->setParameter('arraySectores', $arrayIdSectores);
        }
        else if ($intIdParroquia > 0)
        {
            $strWhere = $strWhere. "AND IU.PARROQUIA_ID IN (
                        SELECT PA.ID_PARROQUIA FROM DB_GENERAL.ADMI_PARROQUIA PA
                        WHERE PA.ID_PARROQUIA = :intParroquia) ";
            $objNtvQuery->setParameter('intParroquia', $intIdParroquia);
        }
        else if ($intIdCanton > 0)
        {
            $strWhere = $strWhere. "AND IU.PARROQUIA_ID IN (
                        SELECT AP.ID_PARROQUIA FROM DB_GENERAL.ADMI_CANTON AC, DB_GENERAL.ADMI_PARROQUIA AP
                        WHERE AC.ID_CANTON = AP.CANTON_ID
                        AND AC.ID_CANTON = :intCanton) ";
            $objNtvQuery->setParameter('intCanton', $intIdCanton);
        }
        else
        {
            $strWhere = $strWhere. "AND IU.PARROQUIA_ID IN (
                        SELECT AP.ID_PARROQUIA
                        FROM DB_GENERAL.ADMI_PARROQUIA AP
                        WHERE AP.CANTON_ID IN (
                            SELECT AC.ID_CANTON
                            FROM DB_INFRAESTRUCTURA.ADMI_JURISDICCION AJ,
                                DB_INFRAESTRUCTURA.ADMI_CANTON_JURISDICCION ACJ,
                                DB_GENERAL.ADMI_CANTON AC
                            WHERE AJ.ID_JURISDICCION = ACJ.JURISDICCION_ID
                            AND AC.ID_CANTON = ACJ.CANTON_ID
                            AND AJ.ID_JURISDICCION = :intJurisdiccion
                            AND AJ.ESTADO IN ('Activo','Modificado')
                            AND ACJ.ESTADO = 'Activo'
                            AND AC.ESTADO = 'Activo'))";
            $objNtvQuery->setParameter('intJurisdiccion', $intJurisdiccion);
        }

        if (!empty($arrayLineProfile))
        {
            $strWhere = $strWhere. "AND IPC.ID_PLAN IN (
                SELECT IPC.ID_PLAN
                FROM DB_COMERCIAL.INFO_PLAN_CAB IPC, DB_COMERCIAL.INFO_PLAN_DET IPD,
                    DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT IPPC
                WHERE IPC.ID_PLAN = IPD.PLAN_ID
                AND IPD.ID_ITEM = IPPC.PLAN_DET_ID
                AND IPC.EMPRESA_COD = :intCodEmpresa
                AND IPPC.PRODUCTO_CARACTERISITICA_ID = (
                    SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                    WHERE PRODUCTO_ID = (
                        SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                        WHERE DESCRIPCION_PRODUCTO = 'INTERNET DEDICADO'
                        AND EMPRESA_COD = :intCodEmpresa
                        AND ESTADO = :strEstadoActivo)
                    AND CARACTERISTICA_ID = (
                        SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                        WHERE DESCRIPCION_CARACTERISTICA = :strCaracteristica))
                AND IPPC.VALOR in (:arrayPlanes)) ";
            $objNtvQuery->setParameter('arrayPlanes', $arrayLineProfile);
            $objNtvQuery->setParameter('intCodEmpresa', $intIdEmpresa);
            $objNtvQuery->setParameter('strEstadoActivo', $strEstadoActivo);
            $objNtvQuery->setParameter('strProducto', $strProducto);
            $objNtvQuery->setParameter('strCaracteristica', $strCaracteristica);
        }

        $objRsm->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $strQuery = $strSelect . $strFrom . $strWhere;
        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();
        return $intCantidad;
    }

}
