<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Clase InfoTareaSeguimientoRepository para repositorio, donde
 * se pondran todas las funciones que ejecuten sql
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class InfoTareaSeguimientoRepository extends EntityRepository
{
     /**
     * getFechaTareaAceptada Funcion que sirve para obtener la fecha de la tarea cuando inicio
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 10-08-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 08-11-2016 Se incluyen los botones de iniciar,pausar y reanudar tareas.
     *
     * @param integer $detalleId
     *
     * @return array $arrayResultado
     *
     */
    public function getFechaInicioTarea($detalleId)
    {
        $objQuery = $this->_em->createQuery();

        $strSql = " SELECT
                        infoTareaSeguimiento.feCreacion as FechaInicioTarea
                 FROM
                    schemaBundle:InfoTareaSeguimiento infoTareaSeguimiento

                 WHERE infoTareaSeguimiento.detalleId = :paramDetalelId
                   AND ( infoTareaSeguimiento.observacion LIKE :paramObservacionAceptada
                   OR infoTareaSeguimiento.observacion LIKE :paramObservacionAsignada
                   OR infoTareaSeguimiento.observacion LIKE :paramObservacionAsig
                   OR infoTareaSeguimiento.observacion LIKE :paramObservacionIni
                   OR infoTareaSeguimiento.observacion LIKE :paramObservacionIntalacion)

                 ORDER BY infoTareaSeguimiento.feCreacion DESC";

        $objQuery->setParameter('paramDetalelId', $detalleId);
        $objQuery->setParameter('paramObservacionAceptada', '%'."Tarea fue Aceptada".'%');
        $objQuery->setParameter('paramObservacionAsignada', '%'."Tarea fue asignada".'%');
        $objQuery->setParameter('paramObservacionAsig', '%'."Tarea fue Asignada".'%');
        $objQuery->setParameter('paramObservacionIni', '%'."Tarea fue Iniciada".'%');
        $objQuery->setParameter('paramObservacionIntalacion', '%'."Tarea Asignada".'%');

        $objQuery->setDQL($strSql);

        $arrayDatos = $objQuery->getResult();

        return $arrayDatos;
    }

    /**
     * Costo: 6
     * getSeguimiento
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 - 29/08/2017 - Obtener los seguimientos que ha ingresado la cuadrilla desde la tablet
     *                             discriminando el seguimiento que contiene la leyenda "Tarea fue Reanudada"
     *
     *
     * @param array $arrayParametros[
     *                                'intDetalleId'             => int Detalle id de la tarea.
     *                                'arrayEstadoTarea'         => array Estado de la tarea.
     *                                'strUsrCreacion'           => string Usuario de la tarea.
     *                                'strObservacionReanudar'   => string Observación Reanudar seguimiento de la tarea.
     *                                'strObservacionPausa'      => string Observación Pausa seguimiento de la tarea.
     *                                'strOrigen'                => string Origen de la consulta movil, web.
     *                              ]
     *
     * @return array $arrayResultado
     *
     */
    public function getSeguimiento($arrayParametros)
    {
        $arrayResultado = array();
        $objQuery = $this->_em->createQuery();
        $strSql = " SELECT its
                    FROM schemaBundle:InfoTareaSeguimiento its
                    WHERE its.usrCreacion = :strUsrCreacion
                    AND its.detalleId     = :intDetalleId
                    AND its.estadoTarea  IN (:arrayEstadoTarea) ";
        if(isset($arrayParametros['strOrigen']) && 'MOVIL' === $arrayParametros['strOrigen'])
        {
            $strSql .= "AND its.observacion NOT LIKE :strObservacionReanudar "
                       . "AND its.observacion NOT LIKE :strObservacionPausa ";
            $objQuery->setParameter('strObservacionReanudar', $arrayParametros['strObservacionReanudar']);
            $objQuery->setParameter('strObservacionPausa', $arrayParametros['strObservacionPausa']);
        }
        $objQuery->setParameter('strUsrCreacion',   $arrayParametros['strUsrCreacion']);
        $objQuery->setParameter('intDetalleId',     $arrayParametros['intDetalleId']);
        $objQuery->setParameter('arrayEstadoTarea', $arrayParametros['arrayEstadoTarea']);

        try
        {
            $objQuery->setDQL($strSql);
            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log('InfoTareaSeguimientoRepository->getSeguimiento() '.$e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Costo: 
     * getRegistrosDatosClienteDesdeSeguimientos
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.0 - 31/03/2023 - Obtener Informacion de Contacto del cliente desde seguimientos
     *
     *
     * @param array $arrayParametros[
     *                                'intDetalleId'             => int Detalle id de la tarea.
     *                                'strLikeObservacion'       => string Inicio del texto de Datos de Contacto.
     *                              ]
     *
     * @return array $arrayResultado
     *
     */
    public function getRegistrosDatosClienteDesdeSeguimientos($arrayParametros)
    {
        $arrayResultado = array();
        $objQuery = $this->_em->createQuery();
        $strSql = " SELECT its
                    FROM schemaBundle:InfoTareaSeguimiento its
                    WHERE its.detalleId     = :intDetalleId
                    AND its.observacion LIKE :strTituloDatosContacto
                    ORDER BY its.id desc";

        $objQuery->setParameter('intDetalleId'          , $arrayParametros['intDetalleId']);
        $objQuery->setParameter('strTituloDatosContacto', $arrayParametros['strTituloDatosContacto'].'%');
        try
        {
            $objQuery->setDQL($strSql);
            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log('InfoTareaSeguimientoRepository->getRegistrosDatosClienteDesdeSeguimientos() '.$e->getMessage());
        }
        return $arrayResultado;

        
    }
}