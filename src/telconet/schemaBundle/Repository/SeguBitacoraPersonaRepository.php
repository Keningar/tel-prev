<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Documentación para la clase 'SeguBitacoraPersonaRepository'.
 *
 * Clase que contiene todas las consultas a la Entidad SeguBitacoraPersona
 *
 * @author Duval Medina C. <dmedina@telconet.ec>
 * @version 1.0 2016-10-21
*/
class SeguBitacoraPersonaRepository extends EntityRepository
{
    /**
     * Funcion que sirve para obtener las últimas 3 actividades realizadas por el usuario
     * 
     *  Costo del query 34
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-10-06
     * 
     * @param integer $intPersonaId
     * 
     * @return array scalar results
     */
    public function getUltimasActividades($intPersonaId)
    {
        $objRSM         = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery = $this->_em->createNativeQuery(null, $objRSM);
        
        $strSql = "SELECT * FROM
                    (SELECT actividad.* FROM
                      (SELECT ip.NOMBRES||' '||ip.APELLIDOS AS EMPLEADO,
                              sbp.BITACORA_DETALLE,
                              sm.NOMBRE_MODULO,
                              sa.NOMBRE_ACCION,
                              TO_CHAR(sbp.FE_CREACION, 'YYYY-MM-DD HH24:MI:SS') AS FECHA
                       FROM SEGU_BITACORA_PERSONA sbp
                            JOIN INFO_PERSONA ip ON ip.ID_PERSONA=sbp.PERSONA_ID
                            JOIN SEGU_RELACION_SISTEMA srs ON srs.ID_RELACION_SISTEMA=sbp.RELACION_SISTEMA_ID
                            JOIN SIST_MODULO sm ON sm.ID_MODULO=srs.MODULO_ID
                            JOIN SIST_ACCION sa ON sa.ID_ACCION=srs.ACCION_ID
                       WHERE sbp.PERSONA_ID = :personaId
                       ORDER BY sbp.FE_CREACION DESC) actividad)
                    WHERE ROWNUM < 4";

        $objNativeQuery->setParameter("personaId", $intPersonaId);        
        
        $objRSM->addScalarResult('EMPLEADO',         'empleado',        'string');
        $objRSM->addScalarResult('BITACORA_DETALLE', 'bitacoraDetalle', 'string');
        $objRSM->addScalarResult('NOMBRE_MODULO',    'nombreModulo',    'string');
        $objRSM->addScalarResult('NOMBRE_ACCION',    'nombreAccion',    'string');
        $objRSM->addScalarResult('FECHA',            'fecha',           'string');
        
        $objNativeQuery->setSQL($strSql);
        $arrayDatos = $objNativeQuery->getScalarResult();

        return $arrayDatos;
    }
}
