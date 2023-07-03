<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

    /**
     * Clase: InfoTareaTiempoParcialRepository, deberia de contener los metodos que esten realacionados a la tabla
     *                                          INFO_TAREA_TIEMPO_PARCIAL y a la logica de Iniciar,Pausar y Reanudar una tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-11-2016
     *
     */
class InfoTareaTiempoParcialRepository extends EntityRepository
{
    /**
     * Función encargada de obtener los tiempos de la tarea.
     *
     * Costo 8
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 17-14-2019
     *
     * @param  Array $arrayParametros [
     *                                  intIdDetalle  => Id detalle de la tarea.
     *                                  strEstado     => Estado de la tarea.
     *                                ]
     * @return Array $arrayRespuesta
     */
    public function getTiemposTarea($arrayParametros)
    {
        $strAnd = "";

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if (isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strAnd .= 'AND ITTP.ESTADO = :strEstado ';
                $objNativeQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }

            $strSql = "SELECT TIEMPO.*, ".
                             "TIEMPO.EMPRESA + TIEMPO.CLIENTE AS TOTAL ".
                        "FROM (SELECT MAX(ITTP.FE_CREACION)               AS FE_FINALIZA, ".
                                     "NVL(SUM(ITTP.VALOR_TIEMPO),0)       AS EMPRESA, ".
                                     "NVL(SUM(ITTP.VALOR_TIEMPO_PAUSA),0) AS CLIENTE ".
                                "FROM DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL ITTP ".
                              "WHERE ITTP.DETALLE_ID = :intIdDetalle $strAnd".
                        ") TIEMPO";

            $objNativeQuery->setParameter('intIdDetalle', $arrayParametros['intIdDetalle']);

            $objResultSetMap->addScalarResult('EMPRESA'     , 'empresa'    , 'integer');
            $objResultSetMap->addScalarResult('CLIENTE'     , 'cliente'    , 'integer');
            $objResultSetMap->addScalarResult('TOTAL'       , 'total'      , 'integer');
            $objResultSetMap->addScalarResult('FE_FINALIZA' , 'feFinaliza' , 'datetime');

            $objNativeQuery->setSQL($strSql);

            $arrayRespuesta = array("status" => 'ok',
                                    "result" => $objNativeQuery->getResult());
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * Función encargada de obtener la ultima tarea que es Solución para un caso.
     *
     * Costo 16
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 17-14-2019
     *
     * @param  Array $arrayParametros [intIdCaso  => Id del caso]
     * @return Array $arrayRespuesta
     */
    public function getUltimaTareaSolucion($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strQuerySql = "SELECT ULTIMATAREA.* FROM ( ".
                                   "SELECT IDHI.ESTADO      AS ESTADO, ".
                                          "IDHI.FE_CREACION AS FE_FINALIZA, ".
                                          "IDE.ID_DETALLE   AS ID_DETALLE, ".
                                          "IDE.ES_SOLUCION  AS ES_SOLUCION ".
                                       "FROM DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH, ".
                                            "DB_SOPORTE.INFO_DETALLE           IDE, ".
                                            "DB_SOPORTE.ADMI_TAREA             ATA, ".
                                            "DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHI ".
                                   "WHERE IDH.CASO_ID               = :intIdCaso ".
                                     "AND IDH.ID_DETALLE_HIPOTESIS  = IDE.DETALLE_HIPOTESIS_ID ".
                                     "AND IDE.TAREA_ID              = ATA.ID_TAREA ".
                                     "AND IDE.ES_SOLUCION           = :strEsSolucion ".
                                     "AND IDHI.DETALLE_ID           = IDE.ID_DETALLE ".
                                     "AND IDHI.ID_DETALLE_HISTORIAL = ".
                                           "(SELECT MAX(IDHIMAX.ID_DETALLE_HISTORIAL) ".
                                               "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHIMAX ".
                                            "WHERE IDHIMAX.DETALLE_ID = IDE.ID_DETALLE) ".
                                   "ORDER BY IDHI.FE_CREACION DESC ".
                               ") ULTIMATAREA ".
                           "WHERE ROWNUM = 1";

            $objNativeQuery->setParameter('intIdCaso'    , $arrayParametros['intIdCaso']);
            $objNativeQuery->setParameter('strEsSolucion', 'S');

            $objResultSetMap->addScalarResult('ID_DETALLE'  , 'idDetalle'  , 'integer');
            $objResultSetMap->addScalarResult('ESTADO'      , 'estado'     , 'string');
            $objResultSetMap->addScalarResult('FE_FINALIZA' , 'feFinaliza' , 'datetime');
            $objResultSetMap->addScalarResult('ES_SOLUCION' , 'esSolucion' , 'string');

            $objNativeQuery->setSQL($strQuerySql);

            $arrayRespuesta = array("status" => 'ok',
                                    "result" => $objNativeQuery->getResult());
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }

        return $arrayRespuesta;
    }
}
