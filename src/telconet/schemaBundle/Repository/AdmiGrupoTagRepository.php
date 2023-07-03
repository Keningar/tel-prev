<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdmiGrupoTagRepository extends EntityRepository
{


    /**
     * getTags
     *
     * metodo que consulta los tags de cada grupo para el grid de show principal
     * @param mixed $arrayParametros 
     * @return json
     *
     * @author Roberth Cobena <rcobena@telconet.ec>
     * @author Francisco Gonzalez <fgonzalezh@telconet.ec>
     * @version 1.0 03-06-2021
     *
     */
    public function findTagsporScope($arrayParametros)
    {
       
        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT
                        tag.id_tag AS idtag,
                        tag.descripcion,
                        tag.observacion,
                        grupo.fe_creacion,
                        grupo.usr_creacion
                    FROM
                        db_infraestructura.admi_grupo_tag    grupo,
                        db_infraestructura.admi_tag          tag,
                        db_general.admi_parametro_cab        cab,
                        db_general.admi_parametro_det        det
                    WHERE
                            grupo.tag_id = tag.id_tag
                        AND grupo.estado = :paramEstado
                        AND grupo.empresa_cod = :paramEmpresa
                        AND tag.estado = :paramEstado
                        AND cab.nombre_parametro = :paramDet
                        AND det.parametro_id = cab.id_parametro
                        AND det.estado = :paramEstado
                        AND det.valor2 = :paramScope
                        AND det.valor3 = grupo.scope";

        $objQuery->setParameter('paramScope', $arrayParametros["strTipoScope"]);
        $objQuery->setParameter('paramEmpresa', $arrayParametros["strEmpresa"]);
        $objQuery->setParameter('paramEstado', 'Activo');
        $objQuery->setParameter('paramDet', 'CONFIGURACION_SCOPES');

        $objRsmb->addScalarResult('IDTAG', 'idtag', 'integer');
        $objRsmb->addScalarResult('DESCRIPCION', 'descripcion', 'string');
        $objRsmb->addScalarResult('OBSERVACION', 'observacion', 'string');
        $objRsmb->addScalarResult('FE_CREACION', 'fe_creacion', 'string');
        $objRsmb->addScalarResult('USR_CREACION', 'usr_creacion', 'string');
        $objQuery->setSQL($strSql);

        $arrayDatos = $objQuery->getResult();

        return $arrayDatos;
    }


    /**
     * findScopeByTags
     *
     * metodo que consulta los tipos scope activos con tags agregados a ese scope
     * @param mixed $arrayParametros 
     * @return json
     *
     * @author Francisco Gonzalez <fgonzalezh@telconet.ec>
     * @version 1.0 07-06-2021
     *
     */
    public function findScopeByTags($arrayParametros)
    {

        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT DISTINCT
                        grupo.scope,
                        det.valor2 AS prefixscope,
                        grupo.estado
                    FROM
                        db_infraestructura.admi_grupo_tag    grupo,
                        db_general.admi_parametro_det        det
                    WHERE
                        det.valor3 = grupo.scope
                        AND grupo.estado = 'Activo'";

        $objRsmb->addScalarResult('SCOPE', 'scope', 'string');
        $objRsmb->addScalarResult('PREFIXSCOPE', 'prefixscope', 'string');
        $objRsmb->addScalarResult('ESTADO', 'estado', 'string');
        $objRsmb->addScalarResult('COUNTTAGS', 'counttags', 'string');

        $objQuery->setSQL($strSql);

        $arrayDatos = $objQuery->getResult();
        return $arrayDatos;
    }



    public function getTagsByScope($arrayParametros)
    {

        $objRsmb = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objRsmb);

        $strSql = " SELECT
                        tag.id_tag AS intIdTagBlanck,
                        tag.descripcion AS strDescripcionTagBlanck
                    FROM
                        db_infraestructura.admi_grupo_tag    grupo,
                        db_infraestructura.admi_tag          tag,
                        db_general.admi_parametro_cab        cab,
                        db_general.admi_parametro_det        det
                    WHERE
                            grupo.tag_id = tag.id_tag
                        AND grupo.estado = :paramEstado
                        AND grupo.empresa_cod = :paramEmpresa
                        AND tag.estado = :paramEstado
                        AND cab.nombre_parametro = :paramDet
                        AND det.parametro_id = cab.id_parametro
                        AND det.estado = :paramEstado
                        AND det.valor2 = :paramScope
                        AND det.valor3 = grupo.scope";

        $objQuery->setParameter('paramScope', $arrayParametros["strTipoScope"]);
        $objQuery->setParameter('paramEmpresa', $arrayParametros["strEmpresa"]);
        $objQuery->setParameter('paramEstado', 'Activo');
        $objQuery->setParameter('paramDet', 'CONFIGURACION_SCOPES');

        $objRsmb->addScalarResult('INTIDTAGBLANCK', 'intIdTagBlanck', 'integer');
        $objRsmb->addScalarResult('STRDESCRIPCIONTAGBLANCK', 'strDescripcionTagBlanck', 'string');
        $objQuery->setSQL($strSql);

        $arrayDatos = $objQuery->getResult();

        return $arrayDatos;
    }
}
