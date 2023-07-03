<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class InfoCotizacionCabRepository extends EntityRepository
{
    public function getCotizacionListado($arrayParametros)
    {
        $strCodEmpresa            = $arrayParametros['strCodEmpresa'];
        $intNumeroCotizacion      = $arrayParametros['intNumeroCotizacion'];
        $strIdentificacionCliente = $arrayParametros['strIdentificacionCliente'];
        $strFechaDesde            = $arrayParametros['strFechaDesde'];
        $strFechaHasta            = $arrayParametros['strFechaHasta'];
        $strLoginVendedor         = $arrayParametros['strLoginVendedor'];

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT CAB.ID_COTIZACION, CAB.NUMERO_COTIZACION, TO_CHAR(CAB.FE_CREACION, 'DD/MM/YYYY HH24:MI:SS') FE_CREACION, 
                   PER.IDENTIFICACION_CLIENTE, CAB.USR_CREACION, 
                   CASE WHEN PER.RAZON_SOCIAL IS NULL THEN PER.NOMBRES || ' ' || PER.APELLIDOS ELSE PER.RAZON_SOCIAL END NOMBRES ,
                   PER.ESTADO, CAB.PUNTO_ID, PUN.LOGIN, CAB.EMPRESA_COD, ROL.ID_PERSONA_ROL, CAB.ARCHIVO_DIGITAL 
                   FROM DB_COMERCIAL.INFO_COTIZACION_CAB CAB
                   LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL ROL
                     ON CAB.PERSONA_EMPRESA_ROL_ID = ROL.ID_PERSONA_ROL
                   LEFT JOIN DB_COMERCIAL.INFO_PERSONA PER
                     ON ROL.PERSONA_ID = PER.ID_PERSONA
                   LEFT JOIN DB_COMERCIAL.INFO_PUNTO PUN
                     ON CAB.PUNTO_ID = PUN.ID_PUNTO";

        $strSqlWhere = " WHERE CAB.EMPRESA_COD = :empresaCod AND CAB.USR_CREACION = :loginvend ";
        $objQuery->setParameter("empresaCod", $strCodEmpresa);
        $objQuery->setParameter("loginvend" , $strLoginVendedor);

        if (isset($intNumeroCotizacion) && $intNumeroCotizacion > 0)
        {
            $strSqlWhere .= "AND CAB.NUMERO_COTIZACION = :numero ";
            $objQuery->setParameter("numero", $intNumeroCotizacion);
        }
        else
        {
            if (isset($strIdentificacionCliente) && $strIdentificacionCliente !== "")
            {
                $strSqlWhere .= "AND PER.IDENTIFICACION_CLIENTE = :identificacion ";
                $objQuery->setParameter("identificacion", $strIdentificacionCliente);
            }
            if ($strFechaDesde !== " 00:00:00" && $strFechaHasta !== " 23:59:59")
            {
                $strSqlWhere .= " AND CAB.FE_CREACION >= to_date(:feDesde, 'DD/MM/YYYY HH24:MI:SS')
                               AND CAB.FE_CREACION <= to_date(:feHasta, 'DD/MM/YYYY HH24:MI:SS')"; 
                $objQuery->setParameter("feDesde", $strFechaDesde);
                $objQuery->setParameter("feHasta", $strFechaHasta);
            }
        }

        $strSql = $strSql . " " . $strSqlWhere;

        $strSqlOrder = "ORDER BY 1 DESC";
        $strSql = $strSql . " " . $strSqlOrder;

        $objRsm->addScalarResult('ID_COTIZACION',          'idCotizacion',          'integer');
        $objRsm->addScalarResult('NUMERO_COTIZACION',      'numeroCotizacion',      'integer');
        $objRsm->addScalarResult('FE_CREACION',            'feCreacion',            'string');
        $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionCliente', 'string');
        $objRsm->addScalarResult('USR_CREACION',           'usrCreacion',           'string');
        $objRsm->addScalarResult('NOMBRES',                'nombres',               'string');
        $objRsm->addScalarResult('ESTADO',                 'estado',                'string');
        $objRsm->addScalarResult('PUNTO_ID',               'puntoId',               'integer');
        $objRsm->addScalarResult('LOGIN',                  'login',                 'string');
        $objRsm->addScalarResult('EMPRESA_COD',            'empresaCod',            'string');
        $objRsm->addScalarResult('ID_PERSONA_ROL',         'idPersonaRol',          'integer');
        $objRsm->addScalarResult('ARCHIVO_DIGITAL',        'archivoDigital',        'string');

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
    }
}
