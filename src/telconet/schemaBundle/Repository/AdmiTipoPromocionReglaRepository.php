<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoPromocionReglaRepository extends EntityRepository
{
    /**
     * getPromoValidasOlts()
     * Obtiene la promocion activa para las nuevas Olts.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 08-06-2022
     * 
     * @param Array $arrayParametros["strTipoPromocion" => Tipo de promocion,
     *                               "strIdParroquia"   => La parroquia del olt creado,
     *                               "strEstado         => Estado permitido para los registros",
     *                               "strTipoBusqueda   => Tipo de detalle de jurisdiccion"]
     * 
     * @return $objQuery - Lista de Promociones activas.
     */
    public function getValidaPromosActivasOlt($arrayParametros)
    {
        $strTipoPromocion = $arrayParametros['strTipoPromocion'];
        $strIdParroquia   = $arrayParametros['strIdParroquia'];
        $strEstado        = $arrayParametros['strEstado'];
        $strTipoBusqueda  = $arrayParametros['strTipoBusqueda'];

        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_PROMOCION', 'idPromocion', 'integer');
        $objResultSet->addScalarResult('NOMBRE', 'nombre', 'string');
        $objResultSet->addScalarResult('FEC_INICIO', 'fecIni', 'string');
        $objResultSet->addScalarResult('HOR_INICIO', 'fecFin', 'string');
        $objResultSet->addScalarResult('FEC_FIN', 'horIni', 'string');
        $objResultSet->addScalarResult('HOR_FIN', 'horFin', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT AGP.ID_GRUPO_PROMOCION ID_PROMOCION, AGP.NOMBRE_GRUPO NOMBRE,
                          TO_CHAR(AGP.FE_INICIO_VIGENCIA,'dd-mm-yyyy') FEC_INICIO,
                          TO_CHAR(AGP.FE_INICIO_VIGENCIA,'HH24:MI') HOR_INICIO,
                          TO_CHAR(AGP.FE_FIN_VIGENCIA,'dd-mm-yyyy') FEC_FIN,
                          TO_CHAR(AGP.FE_FIN_VIGENCIA,'HH24:MI') HOR_FIN ";
        
        $strFrom = "FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION AGP, DB_COMERCIAL.ADMI_TIPO_PROMOCION ATP,
                         DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA ATPR, DB_COMERCIAL.ADMI_CARACTERISTICA AC ";

        $strWhere = "WHERE AGP.ID_GRUPO_PROMOCION = ATP.GRUPO_PROMOCION_ID
                    AND ATP.ID_TIPO_PROMOCION = ATPR.TIPO_PROMOCION_ID
                    AND AC.ID_CARACTERISTICA = ATPR.CARACTERISTICA_ID
                    AND AC.ESTADO = :strEstado
                    AND ATPR.ESTADO = :strEstado
                    AND ATP.ESTADO = :strEstado
                    AND AGP.ESTADO = :strEstado
                    AND ATP.CODIGO_TIPO_PROMOCION = :strTipoPromocion ";

        if ($strTipoBusqueda == 'PARROQUIA')
        {
            $strWhere = $strWhere." AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_PARROQUIA'
                                    AND ATPR.VALOR = :strIdParroquia ";
            $objQuery->setParameter('strIdParroquia', $strIdParroquia);
        }
        if ($strTipoBusqueda == 'CANTON')
        {
            $strWhere = $strWhere." AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_CANTON'
                                    AND ATPR.VALOR IN (
                                        SELECT TO_CHAR(AC.ID_CANTON)
                                        FROM DB_GENERAL.ADMI_PARROQUIA AP, DB_GENERAL.ADMI_CANTON AC
                                        WHERE AC.ID_CANTON = AP.CANTON_ID
                                        AND AC.ESTADO = :strEstado
                                        AND AP.ESTADO = :strEstado
                                        AND AP.ID_PARROQUIA = :strIdParroquia) ";
            $objQuery->setParameter('strIdParroquia', $strIdParroquia);
        }
        if ($strTipoBusqueda == 'JURISDICCION')
        {
            $strWhere = $strWhere." AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_JURISDICCION'
                                    AND ATPR.VALOR IN (
                                        SELECT AJ.ID_JURISDICCION
                                        FROM DB_INFRAESTRUCTURA.ADMI_JURISDICCION AJ,
                                            DB_INFRAESTRUCTURA.ADMI_CANTON_JURISDICCION ACJ
                                        WHERE AJ.ID_JURISDICCION = ACJ.JURISDICCION_ID
                                        AND AJ.ESTADO = :strEstado
                                        AND ACJ.ESTADO = :strEstado
                                        AND ACJ.CANTON_ID IN (
                                        SELECT AC.ID_CANTON
                                        FROM DB_GENERAL.ADMI_PARROQUIA AP, DB_GENERAL.ADMI_CANTON AC
                                        WHERE AC.ID_CANTON = AP.CANTON_ID
                                        AND AC.ESTADO = :strEstado
                                        AND AP.ESTADO = :strEstado
                                        AND AP.ID_PARROQUIA = :strIdParroquia)) ";
            $objQuery->setParameter('strIdParroquia', $strIdParroquia);
        }

        $objQuery->setParameter('strTipoPromocion', $strTipoPromocion);
        $objQuery->setParameter('strEstado', $strEstado);
        $strSql = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

}
