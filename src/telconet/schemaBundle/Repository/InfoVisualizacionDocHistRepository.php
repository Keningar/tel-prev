<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class InfoVisualizacionDocHistRepository extends BaseRepository
{

    /**
     *
     * Obtiene registros de historial de visualización documentos digitales.
     *
     * @author Brando Tomala <btomala@telconet.ec>
     * @version 1.0 17-05-2021
     */
    public function getHistDocumentoDigital($arrayParametros)
    {
        $objResultado = array();
        try
        {
            $strNombre         = $arrayParametros['nombre'];
            $strApellido       = $arrayParametros['apellido'];
            $strIdentificacion = $arrayParametros['identificacion'];
            $strFechaDesde     = $arrayParametros['fechaDesde'];
            $strFechaHasta     = $arrayParametros['fechaHasta'];
            $intLimit          = $arrayParametros['limit'] ? intval($arrayParametros['limit']) : 0;
            $intStart          = $arrayParametros['start'] ? intval($arrayParametros['start']) : 0;
            $strLogin          = $arrayParametros['login'];




            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSqlCount = " SELECT COUNT(*) AS TOTAL FROM INFO_VISUALIZACION_DOC_HIST IDOC, INFO_PERSONA IP ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSqlSelect = "SELECT IDOC.*, IP.NOMBRES ||' '|| IP.APELLIDOS CLIENTE, IP.IDENTIFICACION_CLIENTE  
                                FROM DB_COMERCIAL.INFO_VISUALIZACION_DOC_HIST IDOC, DB_COMERCIAL.INFO_PERSONA IP";
            $strSql = " WHERE 1=1 AND IDOC.IDENTIFICACION = IP.IDENTIFICACION_CLIENTE";

  

             if (!empty($strNombre))
             {

                 $strSql .= " AND UPPER(IP.NOMBRES) like :nombre ";
                 $objQuery->setParameter("nombre", '%' . $strNombre . '%');
                 $objQueryCount->setParameter("nombre", '%' . $strNombre . '%');
             }

             if (!empty($strApellido))
             {
                 $strSql .= " AND UPPER(IP.APELLIDOS) like :apellidos ";
                 $objQuery->setParameter("apellidos", '%' . $strApellido . '%');
                 $objQueryCount->setParameter("apellidos", '%' . $strApellido . '%');
             }

             if (!empty($strIdentificacion))
             {
                 $strSql .= " AND IP.IDENTIFICACION_CLIENTE = :identificacion ";
                 $objQuery->setParameter("identificacion", $strIdentificacion);
                 $objQueryCount->setParameter("identificacion", $strIdentificacion);
             }

             if (!empty($strFechaDesde))
             {
                 $strFechaDesde .= ' 00:00:00';
                 $strSql .= " AND IDOC.FE_CREACION >= to_date(:fechaDesde, 'YYYY-MM-DD HH24:MI:SS') ";
                 $objQuery->setParameter('fechaDesde',  $strFechaDesde);
                 $objQueryCount->setParameter('fechaDesde',  $strFechaDesde);
             }

             if (!empty($strFechaHasta))
             {
                 $strFechaHasta .= ' 23:59:59';
                 $strSql .= " AND IDOC.FE_CREACION <= to_date(:fechaHasta, 'YYYY-MM-DD HH24:MI:SS')";
                 $objQuery->setParameter('fechaHasta', $strFechaHasta);
                 $objQueryCount->setParameter('fechaHasta', $strFechaHasta);
             }

             if (!empty($strLogin))
             {

                 $strSql .= " AND UPPER(IDOC.LOGIN_CLIENTE) like :login ";
                 $objQuery->setParameter("login", '%' . $strLogin . '%');
                 $objQueryCount->setParameter("login", '%' . $strLogin . '%');
             }


            $strSqlSelect = $strSqlSelect . " " . $strSql;

            $strSqlOrder = " ORDER BY IDOC.FE_CREACION DESC";
            $strSqlSelect  = $strSqlSelect  . " " . $strSqlOrder;

            $objRsm->addScalarResult('ID_VISUALIZACION_DOC_HIST', 'id', 'integer');
            $objRsm->addScalarResult('EMPRESA_COD', 'empresaCod', 'string');
            $objRsm->addScalarResult('ACCION', 'accion', 'string');
            $objRsm->addScalarResult('OBSERVACION', 'observacion', 'text');
            $objRsm->addScalarResult('ESTADO_SERVICIO', 'estado', 'string');
            $objRsm->addScalarResult('IDENTIFICACION', 'identificacion', 'string');
            $objRsm->addScalarResult('TIPO_DOCUMENTO', 'tipoDocumento', 'string');
            $objRsm->addScalarResult('USR_CREACION', 'usrCreacion',  'string');
            $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
            $objRsm->addScalarResult('IP_CREACION', 'ipCreacion', 'string');
            $objRsm->addScalarResult('CLIENTE', 'cliente', 'string');
            $objRsm->addScalarResult('IDENTIFICACION', 'identificacion', 'string');
            $objRsm->addScalarResult('LOGIN_CLIENTE', 'login', 'string');


            $strSqlCount = $strSqlCount  . " " . $strSql;

            $objQueryCount->setSQL($strSqlCount);
            $intCountResultados = $objQueryCount->getSingleScalarResult();
            $objResultado['total'] = $intCountResultados;

            $objQuery->setSQL($strSqlSelect );
            $arrayResul = $this->setQueryLimit($objQuery, $intLimit, $intStart);
            $objResultado['registros'] = $arrayResul->getArrayResult();
        } catch (\Exception $e)
        {
            error_log('ERROR_PRUEBA'.$e->getMessage());
        }
        return $objResultado;
    }
}
