<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoLogRepository extends BaseRepository
{
    /**
      * getLog
      *
      * Método que retornará el log según los parámetros requeridos
      *
      * @param array $arrayParametros
      * @return array $arrayResultado
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 16-11-2018
      */
    public function getLog($arrayParametros)
    {
        $strEmpresaCod  = $arrayParametros['strEmpresaCod'];
        $strTipoLog     = $arrayParametros['strTipoLog'];
        $strOrigenLog   = $arrayParametros['strOrigenLog'];
        $strAplicacion  = $arrayParametros['strAplicacion'];        
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strFechaDesde  = $arrayParametros['strFechaDesde'];
        $strFechaHasta  = $arrayParametros['strFechaHasta'];

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT * FROM DB_GENERAL.INFO_LOG";

        $strSqlWhere = " WHERE 1 = 1 ";
        if (isset($strEmpresaCod))
        {
            $strSqlWhere .= " and EMPRESA_COD = :empresaCod ";
            $objQuery->setParameter("empresaCod", $strEmpresaCod);
        }
        if (isset($strTipoLog))
        {
            $strSqlWhere .= " and TIPO_LOG = :tipoLog ";
            $objQuery->setParameter("tipoLog", $strTipoLog);
        }
        if (isset($strOrigenLog))
        {
            $strSqlWhere .= " and ORIGEN_LOG = :origenLog ";
            $objQuery->setParameter("origenLog", $strOrigenLog);
        }
        if (isset($strAplicacion))
        {
            $strSqlWhere .= " and APLICACION = :aplicacion ";
            $objQuery->setParameter("aplicacion", $strAplicacion);
        }        
        if (isset($strUsrCreacion))
        {
            $strSqlWhere .= " and USR_CREACION = :usrCreacion ";
            $objQuery->setParameter("usrCreacion", $strUsrCreacion);
        }
        if (isset($strFechaDesde) && isset($strFechaHasta))
        {
            $strFechaDesde .= ' 00:00:00';
            $strFechaHasta .= ' 23:59:59';

            $strSqlWhere .= " and FE_CREACION >= to_date(:fechaDesde, 'DD/MM/YYYY HH24:MI:SS')
                              and FE_CREACION <= to_date(:fechaHasta, 'DD/MM/YYYY HH24:MI:SS')";
            $objQuery->setParameter("fechaDesde", $strFechaDesde);
            $objQuery->setParameter("fechaHasta", $strFechaHasta);
        }

        $strSql = $strSql . " " . $strSqlWhere;

        $strSqlOrder = " ORDER BY FE_CREACION DESC";
        $strSql = $strSql . " " . $strSqlOrder;

        $objRsm->addScalarResult('ID_LOG',            'idLog',            'integer');
        $objRsm->addScalarResult('EMPRESA_COD',       'empresaCod',       'integer');
        $objRsm->addScalarResult('TIPO_LOG',          'tipoLog',          'string');
        $objRsm->addScalarResult('ORIGEN_LOG',        'origenLog',        'string');
        $objRsm->addScalarResult('LATITUD',           'latitud',          'string');
        $objRsm->addScalarResult('LONGITUD',          'longitud',         'string');
        $objRsm->addScalarResult('APLICACION',        'aplicacion',       'string');
        $objRsm->addScalarResult('CLASE',             'clase',            'string');
        $objRsm->addScalarResult('METODO',            'metodo',           'string');
        $objRsm->addScalarResult('ACCION',            'accion',           'string');
        $objRsm->addScalarResult('MENSAJE',           'mensaje',          'string');
        $objRsm->addScalarResult('ESTADO',            'estado',           'string');
        $objRsm->addScalarResult('DESCRIPCION',       'descripcion',      'text');
        $objRsm->addScalarResult('IMEI',              'imei',             'integer');
        $objRsm->addScalarResult('MODELO',            'modelo',           'string');
        $objRsm->addScalarResult('VERSION_APK',       'versionApk',       'string');
        $objRsm->addScalarResult('VERSION_SO',        'versionSo',        'string');
        $objRsm->addScalarResult('TIPO_CONEXION',     'tipoConexion',     'string');
        $objRsm->addScalarResult('INTENSIDAD_SENAL',  'intensidadSenal',  'string');
        $objRsm->addScalarResult('PARAMETRO_ENTRADA', 'parametroEntrada', 'text');
        $objRsm->addScalarResult('USR_CREACION',      'usrCreacion',      'string');
        $objRsm->addScalarResult('FE_CREACION',       'feCreacion',       'string');

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
    }

    public function getLogDocumentoDigital($arrayParametros)
    {
        $objResultado = array();
        try
        {
            $strNombre   = $arrayParametros['nombre'];
            $strApellido  = $arrayParametros['apellido'];
            $strIdentificacion = $arrayParametros['identificacion'];
            $strFechaDesde  = $arrayParametros['fechaDesde'];
            $strFechaHasta  = $arrayParametros['fechaHasta'];
            $intLimit = $arrayParametros['limit'] ? intval($arrayParametros['limit']) : 0;
            $intStart = $arrayParametros['start'] ? intval($arrayParametros['start']) : 0;

            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSqlCount = " SELECT COUNT(*) AS TOTAL FROM DB_GENERAL.INFO_LOG ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT * FROM DB_GENERAL.INFO_LOG";

            $strSqlWhere = " WHERE 1 = 1 ";
            $strSqlWhere .= " AND  (ACCION = 'LOGVIS' OR ACCION = 'LOGDES')";

            if (!empty($strNombre))
            {
                
                $strSqlWhere .= " AND UPPER(PARAMETRO_ENTRADA) like :nombre ";
                $objQuery->setParameter("nombre", '%' . $strNombre . '%');
                $objQueryCount->setParameter("nombre", '%' . $strNombre . '%');
            }

            if (!empty($strApellido))
            {
                $strSqlWhere .= " AND UPPER(PARAMETRO_ENTRADA) like :apellidos ";
                $objQuery->setParameter("apellidos", '%' . $strApellido . '%');
                $objQueryCount->setParameter("apellidos", '%' . $strApellido . '%');
            }
            if (!empty($strIdentificacion))
            {
                $strSqlWhere .= " AND UPPER(PARAMETRO_ENTRADA) like :identificacion ";
                $objQuery->setParameter("identificacion", '%' . $strIdentificacion . '%');
                $objQueryCount->setParameter("identificacion", '%' . $strIdentificacion . '%');
            }

            if (!empty($strFechaDesde))
            {
                $strFechaDesde .= ' 00:00:00';
                $strSqlWhere .= " AND FE_CREACION >= to_date(:fechaDesde, 'YYYY-MM-DD HH24:MI:SS') ";
                $objQuery->setParameter('fechaDesde',  $strFechaDesde);
                $objQueryCount->setParameter('fechaDesde',  $strFechaDesde);
            }

            if (!empty($strFechaHasta))
            {
                $strFechaHasta .= ' 23:59:59';
                $strSqlWhere .= " AND FE_CREACION <= to_date(:fechaHasta, 'YYYY-MM-DD HH24:MI:SS')";
                $objQuery->setParameter('fechaHasta', $strFechaHasta);
                $objQueryCount->setParameter('fechaHasta', $strFechaHasta);
            }


            $strSql = $strSql . " " . $strSqlWhere;

            $strSqlOrder = " ORDER BY FE_CREACION DESC";
            $strSql = $strSql . " " . $strSqlOrder;

            $objRsm->addScalarResult('ID_LOG',            'idLog',            'integer');
            $objRsm->addScalarResult('EMPRESA_COD',       'empresaCod',       'integer');
            $objRsm->addScalarResult('TIPO_LOG',          'tipoLog',          'string');
            $objRsm->addScalarResult('ORIGEN_LOG',        'origenLog',        'string');
            $objRsm->addScalarResult('LATITUD',           'latitud',          'string');
            $objRsm->addScalarResult('LONGITUD',          'longitud',         'string');
            $objRsm->addScalarResult('APLICACION',        'aplicacion',       'string');
            $objRsm->addScalarResult('CLASE',             'clase',            'string');
            $objRsm->addScalarResult('METODO',            'metodo',           'string');
            $objRsm->addScalarResult('ACCION',            'accion',           'string');
            $objRsm->addScalarResult('MENSAJE',           'mensaje',          'string');
            $objRsm->addScalarResult('ESTADO',            'estado',           'string');
            $objRsm->addScalarResult('DESCRIPCION',       'descripcion',      'text');
            $objRsm->addScalarResult('IMEI',              'imei',             'integer');
            $objRsm->addScalarResult('MODELO',            'modelo',           'string');
            $objRsm->addScalarResult('VERSION_APK',       'versionApk',       'string');
            $objRsm->addScalarResult('VERSION_SO',        'versionSo',        'string');
            $objRsm->addScalarResult('TIPO_CONEXION',     'tipoConexion',     'string');
            $objRsm->addScalarResult('INTENSIDAD_SENAL',  'intensidadSenal',  'string');
            $objRsm->addScalarResult('PARAMETRO_ENTRADA', 'parametroEntrada', 'text');
            $objRsm->addScalarResult('USR_CREACION',      'usrCreacion',      'string');
            $objRsm->addScalarResult('FE_CREACION',       'feCreacion',       'string');


            $strSqlCount= $strSqlCount  ." ". $strSqlWhere ." ". $strSqlOrder;

            $objQueryCount->setSQL($strSqlCount);
            $intCountResultados = $objQueryCount->getSingleScalarResult();
            $objResultado['total'] = $intCountResultados;

            $objQuery->setSQL($strSql);
            $arrayResul = $this->setQueryLimit($objQuery, $intLimit, $intStart);
            $objResultado['registros'] = $arrayResul->getArrayResult();
        } catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $objResultado;
    }

}
