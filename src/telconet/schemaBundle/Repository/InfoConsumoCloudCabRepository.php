<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoConsumoCloudCabRepository extends EntityRepository
{

    /**
     * Obtiene el los valores a presentar en el grid
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 29-03-2018
     */
    public function obtieneGridConsumo($arrayParametros)
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objRsm2 = new ResultSetMappingBuilder($this->_em);
        $strSqlCountSum = "SELECT COUNT (*) AS NUMERO, ROUND(NVL(SUM(VALOR),0),2) AS VALOR_TOTAL FROM ( ";
        //Costo de 36
        $strSql = "SELECT CAB.FE_CONSUMO, CAB.NOMBRE, PTO.LOGIN,  PTO_2.LOGIN AS LOGIN_FACTURACION,
                     TRIM(CHR(34) FROM PDET.DESCRIPCION) AS DESCRIPCION, 
                      ROUND(DET.VALOR,2) AS VALOR, CAB.ESTADO, CAB.OBSERVACION
                    FROM DB_FINANCIERO.INFO_CONSUMO_CLOUD_CAB CAB 
                    LEFT JOIN DB_COMERCIAL.INFO_PUNTO PTO  ON CAB.PUNTO_ID = PTO.ID_PUNTO 
                    LEFT JOIN DB_COMERCIAL.INFO_PUNTO PTO_2 ON CAB.PUNTO_FACTURACION_ID = PTO_2.ID_PUNTO 
                    LEFT JOIN DB_FINANCIERO.INFO_CONSUMO_CLOUD_DET DET ON  CAB.ID_CONSUMO_CLOUD_CAB = DET.CONSUMO_CLOUD_CAB_ID,
                    DB_GENERAL.ADMI_PARAMETRO_DET PDET,
                    DB_GENERAL.ADMI_PARAMETRO_CAB PCAB
                WHERE 
                    PCAB.NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS'
                    AND PCAB.ESTADO = 'Activo'
                    AND PCAB.MODULO = 'FINANCIERO'
                    AND PCAB.PROCESO = 'FACTURACION_CONSUMO'
                    AND PDET.ESTADO = 'Activo'
                    AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(PDET.VALOR1,'^\d+')),0) = DET.CARACTERISTICA_ID 
                    AND CAB.ESTADO <> 'Eliminado'";

        $strAnd = " AND ";
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery2 = $this->_em->createNativeQuery(null, $objRsm2);
        //Se agregan los filtros adicionales
        if($arrayParametros["puntoId"] && $arrayParametros["puntoId"] != 0)
        {
            $strSql .= $strAnd . " PTO.ID_PUNTO = :puntoId \n";
            $objQuery->setParameter('puntoId', $arrayParametros["puntoId"]);
            $objQuery2->setParameter('puntoId', $arrayParametros["puntoId"]);
        }
        if($arrayParametros["estado"] && $arrayParametros["estado"] != 'Todos')
        {
            $strSql .= $strAnd . " CAB.ESTADO = :estado \n";
            $objQuery->setParameter('estado', $arrayParametros["estado"]);
            $objQuery2->setParameter('estado', $arrayParametros["estado"]);
        }
        if($arrayParametros["mesConsumo"] && $arrayParametros["mesConsumo"] != 'Todos')
        {
            $objDateFeConsumo = new \DateTime($arrayParametros["mesConsumo"] . "/01");
            $strSql .= $strAnd . " FE_CONSUMO >= :mesConsumo \n";
            $strSql .= $strAnd . " FE_CONSUMO <= LAST_DAY(:mesConsumo2) \n";
            $objQuery->setParameter('mesConsumo', $objDateFeConsumo);
            $objQuery->setParameter('mesConsumo2', $objDateFeConsumo);
            $objQuery2->setParameter('mesConsumo', $objDateFeConsumo);
            $objQuery2->setParameter('mesConsumo2', $objDateFeConsumo);
        }

        //Se forma el arreglo del grid
        $objRsm->addScalarResult('FE_CONSUMO', 'feConsumo', 'string');
        $objRsm->addScalarResult('NOMBRE', 'nombre', 'string');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('LOGIN_FACTURACION', 'loginFacturacion', 'string');
        $objRsm->addScalarResult('DESCRIPCION', 'descripcion', 'integer');
        $objRsm->addScalarResult('VALOR', 'valor', 'integer');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('OBSERVACION', 'observacion', 'string');

        //Se forma el arreglo del count y sum
        $objRsm2->addScalarResult('NUMERO', 'numero', 'integer');
        $objRsm2->addScalarResult('VALOR_TOTAL', 'valorTotal', 'integer');

        $objQuery2->setSQL($strSqlCountSum . $strSql . ')');
        $arrayDatos2 = $objQuery2->getScalarResult();

        $arrayDatos["total"] = $arrayDatos2[0]["numero"];
        $arrayDatos["valorTotal"] = $arrayDatos2[0]["valorTotal"];

        $strSql .= " ORDER BY DET.ID_CONSUMO_CLOUD_DET  DESC";

        $objQuery->setParameter('start', $arrayParametros["start"] + 1);
        $objQuery->setParameter('limit', ($arrayParametros["start"] + $arrayParametros["limit"]));
        $strSql = "SELECT a.*, rownum as intDoctrineRowNum FROM (" . $strSql . ") a WHERE ROWNUM <= :limit";
        if($arrayParametros["start"] > 0)
        {
            $strSql = "SELECT * FROM (" . $strSql . ") WHERE intDoctrineRowNum >= :start";
        }
        $objQuery->setSQL($strSql);
        $arrayDatos["documentos"] = $objQuery->getScalarResult();
        return $arrayDatos;
    }

    public function obtieneEstados()
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $strSql = "SELECT DISTINCT ESTADO AS ESTADO FROM DB_FINANCIERO.INFO_CONSUMO_CLOUD_CAB WHERE ESTADO <> 'Eliminado'";
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        return $arrayDatos;
    }

    /**
     * Obtiene cada uno de los login para el filtro de consumo
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 29-03-2018
     */
    public function obtieneLogins()
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $strSql = "SELECT DISTINCT PTO.ID_PUNTO,  PTO.LOGIN
                    FROM DB_FINANCIERO.INFO_CONSUMO_CLOUD_CAB CAB
                    JOIN DB_COMERCIAL.INFO_PUNTO PTO  ON CAB.PUNTO_ID = PTO.ID_PUNTO 
                    WHERE CAB.ESTADO <> 'Eliminado'";
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objRsm->addScalarResult('ID_PUNTO', 'puntoId', 'integer');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        return $arrayDatos;
    }

    /**
     * Obtiene cada uno de los meses para el filtro de consumo
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 29-03-2018
     */
    public function obtieneMeses()
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $strSql = "SELECT DISTINCT EXTRACT(YEAR FROM FE_CONSUMO) || '/' || TRIM(TO_CHAR(EXTRACT(MONTH FROM FE_CONSUMO),'00'))  AS MES_CONSUMO"
                . " FROM INFO_CONSUMO_CLOUD_CAB CAB "
                . " WHERE CAB.ESTADO <> 'Eliminado' "
                . " ORDER BY 1 DESC";
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objRsm->addScalarResult('MES_CONSUMO', 'mes', 'integer');
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        return $arrayDatos;
    }

    /**
     * Anula los consumos
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 29-03-2018
     */
    public function anulaConsumos($arrayParametros)
    {

        //Obtengo los registros a procesar.
        $objQueryBuilder = $this->_em->createQueryBuilder();
        $objQueryBuilder->select('cab')
                ->from('schemaBundle:InfoConsumoCloudCab', 'cab');
        if($arrayParametros["estado"] && $arrayParametros["estado"] != 'Todos')
        {
            $objQueryBuilder->andWhere("cab.estado = :estado");
            $objQueryBuilder->setParameter("estado", $arrayParametros["estado"]);
        }
        if($arrayParametros["puntoId"] && $arrayParametros["puntoId"] != 0)
        {
            $objQueryBuilder->andWhere("cab.puntoId = :puntoId");
            $objQueryBuilder->setParameter("puntoId", $arrayParametros["puntoId"]);
        }
        if($arrayParametros["mesConsumo"] && $arrayParametros["mesConsumo"] != 'Todos')
        {
            $objQueryBuilder->andWhere("cab.feConsumo BETWEEN :firstDay AND :lastDay");
            $strFechaInicial = strval($arrayParametros["mesConsumo"] . '/01');
            $objQueryBuilder->setParameter("firstDay", new \DateTime($strFechaInicial));
            $objQueryBuilder->setParameter("lastDay", date("Y/m/t", strtotime($strFechaInicial)));
        }
        $objQuery = $objQueryBuilder->getQuery();
        $arrayListInfoConsumoCloudCab = $objQuery->getResult();

        //Cambio el estado de los registros
        $this->_em->getConnection()->beginTransaction();
        try
        {
            foreach($arrayListInfoConsumoCloudCab as $objInfoConsumoCloudCab)
            {
                if($objInfoConsumoCloudCab->getEstado() == 'Procesado' ||
                   $objInfoConsumoCloudCab->getEstado() == 'Eliminado' ||
                   $objInfoConsumoCloudCab->getEstado() == 'Anulado')
                {
                    throw new \Exception("No es posible anular uno o más registros ya procesados");
                }
                else
                {
                    $objInfoConsumoCloudCab->setEstado("Anulado");
                    $objInfoConsumoCloudCab->setUsrUltMod($arrayParametros["usuario"]);
                    $objInfoConsumoCloudCab->setFeUltMod(new \DateTime('now'));
                    $objInfoConsumoCloudCab->setIpUltMod($arrayParametros["ip"]);
                    $this->_em->persist($objInfoConsumoCloudCab);
                    $this->_em->flush();
                }
            }
            $this->_em->getConnection()->commit();
            $arrayRespuesta["mensaje"] = "Se anularon los consumos satisfactoriamente.";
            $arrayRespuesta["estado"] = 1;
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta["mensaje"] = $ex->getMessage();
            $arrayRespuesta["estado"] = 0;
            $this->_em->getConnection()->rollback();
        }
        return $arrayRespuesta;
    }

}
