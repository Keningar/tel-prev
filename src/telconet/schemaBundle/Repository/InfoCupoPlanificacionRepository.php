<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * InfoCupoPlanificacionRepository.
 *
 * Repositorio que se encargará de administrar las funcionalidades adicionales que se relacionen con la entidad InfoCupoPlanificacion
 *
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 13-03-2018
 */
class InfoCupoPlanificacionRepository extends EntityRepository 
{
    /**
     *  Metodo utilizado para retornar el listado de cupos por un rango de fecha determinado
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-02-2018
     *
     */
    public function getRangoFecha($arrayParametros)
    {
        $strFeInicio = $arrayParametros['strFeInicio'];
        $strFeFin = $arrayParametros['strFeFin'];
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('ID_CUPO_PLANIFICACION', 'ID_CUPO_PLANIFICACION');
        $objRsm->addScalarResult('FE_INICIO', 'FE_INICIO');
        $objRsm->addScalarResult('FE_FIN', 'FE_FIN');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT ID_CUPO_PLANIFICACION, FE_INICIO, FE_FIN
                FROM DB_COMERCIAL.INFO_CUPO_PLANIFICACION
                WHERE FE_INICIO >= TO_DATE('$strFeInicio', 'YYYY/MM/DD HH24:MI')
                AND FE_FIN <= TO_DATE('$strFeFin', 'YYYY/MM/DD HH24:MI')
                AND SOLICITUD_ID IS NULL
                AND CUADRILLA_ID IS NULL
                AND JURISDICCION_ID = $intJurisdiccionId
                ORDER BY FE_INICIO";

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();
        $arrayResultado = $this->getUniqueRangoFecha($arrayResultado);
        return $arrayResultado;
    }

    /**
     * Metodo utilizado para retornar un unico registro por cada intervalo de tiempo
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-02-2018
     *
     */
    public function getUniqueRangoFecha($arrayFecha)
    {
        $intCont = count($arrayFecha);
        $arrayRespuesta = array();
        $i = 0;
        while ($i < $intCont) 
        {
            $strFecha = $arrayFecha[$i]['FE_INICIO'];
            $arrayRespuesta[] = array("id" => $arrayFecha[$i]['ID_CUPO_PLANIFICACION']);
            while ($i < $intCont && $strFecha == $arrayFecha[$i]['FE_INICIO']) 
            {
                $i++;
            }
        }
        return $arrayRespuesta;
    }

    /**
     * Metodo utilizado para retornar la cantidad de cupos disponibles que quedan en el intervalo de tiempo especificado
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-02-2018
     *
     */
    public function getCountDisponibles($arrayParametros)
    {
        $strFecha        = $arrayParametros['strFecha'];
        $intJurisdiccion = $arrayParametros['intJurisdiccion'];
        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('CUANTOS', 'CUANTOS');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT COUNT(*) AS CUANTOS
                FROM DB_COMERCIAL.INFO_CUPO_PLANIFICACION
                WHERE FE_INICIO = TO_DATE('$strFecha', 'YYYY/MM/DD HH24:MI')
                AND SOLICITUD_ID IS NULL 
                AND JURISDICCION_ID = $intJurisdiccion";

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();
        return $arrayResultado;
    }

    /**
     * Metodo utilizado para retornar la cantidad de cupos disponibles que quedan en el intervalo de tiempo especificado
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-02-2018
     *
     */
    public function getCountOcupados($arrayParametros)
    {
        $strFecha        = $arrayParametros['strFecha'];
        $intJurisdiccion = $arrayParametros['intJurisdiccion'];
        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('SOLICITUD', 'SOLICITUD');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT SOLICITUD_ID AS SOLICITUD
                FROM DB_COMERCIAL.INFO_CUPO_PLANIFICACION
                WHERE FE_INICIO = TO_DATE('$strFecha', 'YYYY/MM/DD HH24:MI')
                AND SOLICITUD_ID IS NOT NULL 
                AND JURISDICCION_ID = $intJurisdiccion";

        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();
        $intCuposOcupados = 0;
        if (count($arrayResultado) > 0)
        {
            foreach($arrayResultado as $intSolicitudId)
            {
                $entityMobile = $this->_em->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->getSolicitudCaractPorTipoCaracteristica($intSolicitudId, 'Planificacion desde Mobile');
                if (count($entityMobile) > 0) 
                {
                    $intCuposOcupados++;
                }
            }
        }
        $arrayCuantos = array("CUANTOS"=>$intCuposOcupados);
        return $arrayCuantos;
    }

    /**
     * Metodo utilizado para retornar la cantidad de cupos disponibles que quedan en el intervalo de tiempo especificado para telcos
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 23-05-2018
     *
     */
    public function getCountDisponiblesWeb($arrayParametros)
    {
        $strFecha        = $arrayParametros['strFecha'];
        $intJurisdiccion = $arrayParametros['intJurisdiccion'];
        $strFechaAgenda  = $arrayParametros['strFechaAgenda'];
        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('CUPOSMOBILE', 'CUPOSMOBILE');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT CUPOS_MOVIL AS CUPOSMOBILE
                FROM DB_COMERCIAL.INFO_AGENDA_CUPO_DET DET
                JOIN DB_COMERCIAL.INFO_AGENDA_CUPO_CAB CAB
                  ON CAB.ID_AGENDA_CUPOS = DET.AGENDA_CUPO_ID
                WHERE DET.HORA_DESDE = TO_DATE('$strFechaAgenda', 'YYYY/MM/DD HH24:MI')
                  AND CAB.JURISDICCION_ID = '$intJurisdiccion'";


        $objQuery->setSQL($strSql);
        $arrayResultadoCupoMobile = $objQuery->getResult();

        $intCuposMobile = $arrayResultadoCupoMobile[0]['CUPOSMOBILE'] ? $arrayResultadoCupoMobile[0]['CUPOSMOBILE'] : 0;
        $intCupos       = 0;


        $intHora = date("H");
        $intHoraCierre = $arrayParametros['intHoraCierre'];

        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('CUANTOS', 'CUANTOS');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT COUNT(*) AS CUANTOS
                FROM DB_COMERCIAL.INFO_CUPO_PLANIFICACION CUPO
                WHERE CUPO.FE_INICIO = TO_DATE('$strFecha', 'YYYY/MM/DD HH24:MI')
                  AND CUPO.JURISDICCION_ID = '$intJurisdiccion'
                  AND EXISTS(SELECT *
                        FROM INFO_DETALLE_SOL_CARACT CARAC
                        WHERE CUPO.SOLICITUD_ID = CARAC.DETALLE_SOLICITUD_ID)";


        $objQuery->setSQL($strSql);
        $arrayResultadoMobile = $objQuery->getResult();

        $objRsm = new ResultSetMapping();
        $objRsm->addScalarResult('CUANTOS', 'CUANTOS');

        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = "SELECT COUNT(*) AS CUANTOS
                FROM DB_COMERCIAL.INFO_CUPO_PLANIFICACION CUPO
                WHERE CUPO.FE_INICIO = TO_DATE('$strFecha', 'YYYY/MM/DD HH24:MI')
                  AND CUPO.JURISDICCION_ID = '$intJurisdiccion'
                  AND SOLICITUD_ID IS NULL";

        $objQuery->setSQL($strSql);
        $arrayResultadoWeb = $objQuery->getResult();
        $intCupos = $arrayResultadoWeb[0]['CUANTOS'];

        if ($intHora > $intHoraCierre)
        {
            $intCupos += ($intCuposMobile - $arrayResultadoMobile[0]['CUANTOS']);
        }
        return $intCupos;
    }
}
