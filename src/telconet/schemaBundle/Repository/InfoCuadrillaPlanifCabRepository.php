<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoCuadrillaPlanifCabRepository extends EntityRepository
{
    /**
     * 
     * Metodo encargado de generar las cabeceras y detalles de las planificaciones de Cuadrillas ( HAL )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 04-04-2018
     * 
     * @param Array $arrayParametros
     * @return string $strMensajeError;
     */
    public function crearPlanificacionHAL($arrayParametros)
    {             
        $strString       = '';
        $strMensajeError = str_pad($strString, 3000, " ");

        try
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_PLANIFICACION_CUADRILLAS.P_MAIN( "
                 . "                                           :Pn_CuadrillaId, "
                 . "                                           :Pn_IntervaloId,"
                 . "                                           :Pt_FechaDesde,"
                 . "                                           :Pt_FechaHasta,"
                 . "                                           :Pv_EmpresaCod,"
                 . "                                           :Pv_AsignadoMobile,"
                 . "                                           :Pv_UsrCreacion,"
                 . "                                           :Pv_IpCreacion,"
                 . "                                           :Pn_PersonaRolId,"
                 . "                                           :Pb_Automatico,"
                 . "                                           :Pn_ZonaId,"
                 . "                                           :Pv_Actividad,"
                 . "                                           :Lv_MensaError"
                 . "                                           ); "
                 . "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_CuadrillaId',        $arrayParametros['intIdCuadrilla']);
            $objStmt->bindParam('Pn_IntervaloId',        $arrayParametros['intIdIntervalo']);
            $objStmt->bindParam('Pt_FechaDesde',         $arrayParametros['objFechaDesde']);
            $objStmt->bindParam('Pt_FechaHasta',         $arrayParametros['objFechaHasta']);
            $objStmt->bindParam('Pv_EmpresaCod',         $arrayParametros['strEmpresaCod']);
            $objStmt->bindParam('Pv_AsignadoMobile',     $arrayParametros['strAsignado']);
            $objStmt->bindParam('Pv_UsrCreacion',        $arrayParametros['strUsrCreacion']);
            $objStmt->bindParam('Pv_IpCreacion',         $arrayParametros['strIpCreacion']);
            $objStmt->bindParam('Pn_PersonaRolId',       $arrayParametros['intIdPersonaRol']);
            $objStmt->bindParam('Pb_Automatico',         $arrayParametros['strEsAutomatico']);
            $objStmt->bindParam('Pn_ZonaId',             $arrayParametros['intIdZona']);
            $objStmt->bindParam('Pv_Actividad',          $arrayParametros['strCodActividad']);
            $objStmt->bindParam('Lv_MensaError',         $strMensajeError);
            $objStmt->execute();

            return trim($strMensajeError);
        }
        catch (\Exception $objException)
        {
            error_log("Error - InfoCuadrillaPlanifCabRepository.getJornadaDeTrabajo -> ".$objException->getMessage());
            $strMensajeError = '{"estado":"fail","mensaje":"'.$objException->getMessage().'"}';
            return trim($strMensajeError);
        }
    }       

    /**
     * 
     * Metodo encargado de generar las horas dentro de una cabecera de planificacion por actualizacion requerida por el usuario
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 14-04-2018
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 28-06-2018 - Se agrega el control de excepciones y se agrega el parametro strTipoProceso
     *                             para identificar si es Manual o Automatico
     *
     * @param Array $arrayParametros
     * @return string $strMensajeError;
     */
    public function actualizarHorasTrabajoHAL($arrayParametros)
    {
        $strString       = '';
        $strMensajeError = str_pad($strString, 3000, " ");

        $strSql = "BEGIN DB_SOPORTE.SPKG_PLANIFICACION_CUADRILLAS.P_GENERA_INTERVALO_POR_ACT( "
             . "                                           :Pn_PlanifCuadrillaId, "
             . "                                           :Pn_HoraInicio,"
             . "                                           :Pn_HoraFin,"
             . "                                           :Pn_FechaRegistro,"
             . "                                           :Pn_PersonaRolId,"
             . "                                           :Pv_UsrCreacion,"
             . "                                           :Pv_IpCreacion,"
             . "                                           :Pv_TipoProceso,"
             . "                                           :Pv_Error"
             . "                                           ); "
             . "END;";

        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_PlanifCuadrillaId', $arrayParametros['intIdCabecera']);
            $objStmt->bindParam('Pn_HoraInicio',        $arrayParametros['objHoraInicio']);
            $objStmt->bindParam('Pn_HoraFin',           $arrayParametros['objHoraFin']);
            $objStmt->bindParam('Pn_FechaRegistro',     $arrayParametros['objFechaRegistro']);
            $objStmt->bindParam('Pn_PersonaRolId',      $arrayParametros['intIdPersonaRol']);
            $objStmt->bindParam('Pv_UsrCreacion',       $arrayParametros['strUsrCreacion']);
            $objStmt->bindParam('Pv_IpCreacion',        $arrayParametros['strIpCreacion']);
            $objStmt->bindParam('Pv_TipoProceso',       $arrayParametros['strTipoProceso']);
            $objStmt->bindParam('Pv_Error',             $strMensajeError);
            $objStmt->execute();
        }
        catch (\Exception $objException)
        {
            $strMensajeError = 'InfoCuadrillaPlanifCabRepository.actualizarHorasTrabajoHAL: '.$objException->getMessage();
        }
        return $strMensajeError;
    }

    /**
     *
     * Método encargado de realizar la consulta de las planificaciones HAL generadas.
     *
     * Costo 8
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 05-04-2018
     *
     * Costo 18
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 20-06-2018 - Se agrega el filtro por zona.
     *
     * Costo 15
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 13-08-2018 - Se agrega filtros por fecha de trabajo.
     *                         - Se modifica el query para que devuelva la cantidad de tareas abiertas.
     *
     * Costo 15
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 24-08-2018 - Se agrega los paréntesis respectivos al momento de traer la fecha inicio y fecha fin, por motivos
     *                           que no se devuelve las fechas correctas cuando la planificación está en estado Liberado.
     *
     * Costo 14
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 12-09-2018 - Se modifica el query para devolver la zona prestada.
     *                         - Se agrega el control de Excepciones.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 26-09-2018 - Se parametriza el strCodEmpresa.
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.5 - 19-17-2021 - Se agregan en los filtros: el estado "Prestado" y el "CoordinadorPrestadoId", con el objetivo
     * de que  se muestre la planificacion en la Agenda, tanto para el Coordinador principal, como para el Coordinador termporal
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.6 - 16-01-2023 - Se agrega nuevos filtros para la consulta de la planificación Hal cuadrillas en base 
     *                              al Departamento u Oficina y Departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.7 - 05-06-2023 - Se Quita validación de cuadrillas pertenecientes al id del propietario.
     * 
     * 
     * @param Array $arrayParametros [
     *                                  intIdPersonaRol => Id de la persona empresa rol,
     *                                  strCodEmpresa   => Id de la empresa,
     *                                  intIdCuadrilla  => Id de la cuadrilla,
     *                                  intIdZona       => Id de la zona,
     *                                  strFechaIni     => Fecha de trabajo inicio,
     *                                  strFechaFin     => Fecha de trabajo fin
     *                               ]
     * @return Array
     * 
     */
    public function getArrayPlanificacionAgendaHAL($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = '';
            $strWhereEstado  = '';
            $strSelectParam  = '';
            $strFrom         = '';
            if (isset($arrayParametros['strNombreDepartamento']) && $arrayParametros['strNombreDepartamento'] == 'Operaciones Urbanas')
            {
                if (isset($arrayParametros['strBuscarPor']) &&
                    $arrayParametros['strBuscarPor'] == 'oficina' &&
                    isset($arrayParametros['intOficinaId']) &&
                    isset($arrayParametros['intDepartamentoId']))
                {
                    $strFrom  .= ', DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFOPERSONA ';
                    $strWhere .= " AND (CUADRILLA.COORDINADOR_PRINCIPAL_ID = INFOPERSONA.ID_PERSONA_ROL 
                                    OR CUADRILLA.COORDINADOR_PRESTADO_ID = INFOPERSONA.ID_PERSONA_ROL) 
                                    AND INFOPERSONA.OFICINA_ID = :intOficinaId 
                                    AND DEPARTAMENTO.ID_DEPARTAMENTO = :intDepartamentoId ";

                    $objNativeQuery->setParameter("intDepartamentoId" , $arrayParametros['intDepartamentoId']);
                    $objNativeQuery->setParameter("intOficinaId", $arrayParametros['intOficinaId']);
                }
                
                if( isset($arrayParametros['strBuscarPor']) &&
                    $arrayParametros['strBuscarPor'] == 'departamento' &&
                    isset($arrayParametros['intDepartamentoId']))
                {
                    $strWhere .= ' AND DEPARTAMENTO.ID_DEPARTAMENTO = :intDepartamentoId '; 
                    $objNativeQuery->setParameter("intDepartamentoId", $arrayParametros['intDepartamentoId']);
                }
            }

            if (isset($arrayParametros['intIdCuadrilla']) && !empty($arrayParametros['intIdCuadrilla']))
            {
                $strWhere .= ' AND CUADRILLA.ID_CUADRILLA = :cuadrilla ';
                $objNativeQuery->setParameter("cuadrilla", $arrayParametros['intIdCuadrilla']);
            }

            if (isset($arrayParametros['intIdZona']) && !empty($arrayParametros['intIdZona']))
            {
                $strWhere .= ' AND ZONA.ID_ZONA = :intIdZona ';
                $objNativeQuery->setParameter("intIdZona", $arrayParametros['intIdZona']);
            }

            if (isset($arrayParametros['strFechaIni']) && !empty($arrayParametros['strFechaIni']))
            {
                $strWhere .= " AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') >= :strFechaIni ";
                $objNativeQuery->setParameter("strFechaIni", $arrayParametros['strFechaIni']);
            }

            if (isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']))
            {
                $strWhere .= " AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') <= :strFechaFin ";
                $objNativeQuery->setParameter("strFechaFin", $arrayParametros['strFechaFin']);
            }

            if (isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
            {
                $strWhere       .= " AND CAB.EMPRESA_COD = :empresaCod ";
                $strSelectParam .= " AND PDET.EMPRESA_COD = :empresaCod ";
                $objNativeQuery->setParameter("empresaCod" , $arrayParametros['strCodEmpresa']);
            }

            $strSql = "SELECT
                            CAB.ID_CUADRILLA_PLANIF_CAB ID_CAB,
                            CUADRILLA.ID_CUADRILLA,
                            CUADRILLA.NOMBRE_CUADRILLA,
                            ZONA.ID_ZONA,
                            ZONA.NOMBRE_ZONA,
                            CAB.ZONA_PRESTADA_ID,
                            TRUNC(CAB.FE_TRABAJO) FE_TRABAJO,
                            INTERVALO.ID_INTERVALO,
                            (SELECT MAX(PDET.VALOR2) FROM DB_GENERAL.ADMI_PARAMETRO_CAB PCAB 
                             JOIN DB_GENERAL.ADMI_PARAMETRO_DET PDET ON PDET.PARAMETRO_ID = PCAB.ID_PARAMETRO
                             WHERE PCAB.NOMBRE_PARAMETRO = :nombreParametroActividad
                             ".$strSelectParam." AND UPPER(PDET.VALOR1) = UPPER(CAB.ACTIVIDAD) ) ACTIVIDAD,
                            TO_CHAR(INTERVALO.HORA_INI,'HH24:MI')
                            ||' - '
                            ||TO_CHAR(INTERVALO.HORA_FIN,'HH24:MI') INTERVALO_EFECTIVO,
                            (SELECT TO_CHAR(MIN(FE_INICIO),'HH24:MI')
                            FROM DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET
                            WHERE CUADRILLA_PLANIF_CAB_ID = CAB.ID_CUADRILLA_PLANIF_CAB AND
                            ((ESTADO = :estado AND CAB.ESTADO = :estado) or (estado = :estadoLiberado and cab.estado = :estadoLiberado))
                            ) HORA_INICIO,
                            (SELECT TO_CHAR(MAX(FE_FIN),'HH24:MI')
                            FROM DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET
                            WHERE CUADRILLA_PLANIF_CAB_ID = CAB.ID_CUADRILLA_PLANIF_CAB AND
                            ((ESTADO = :estado AND CAB.ESTADO = :estado) or (estado = :estadoLiberado and cab.estado = :estadoLiberado))
                            ) HORA_FIN,
                            CAB.ESTADO,
                            (SELECT
                                COUNT(COUNT(DET.COMUNICACION_ID))
                              FROM
                                DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET
                              WHERE DET.CUADRILLA_PLANIF_CAB_ID = CAB.ID_CUADRILLA_PLANIF_CAB
                                AND DET.COMUNICACION_ID IS NOT NULL
                                AND (SELECT
                                        IDH.ESTADO
                                    FROM
                                        DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH
                                    WHERE IDH.ID_DETALLE_HISTORIAL =
                                        (SELECT
                                            MAX(HIST.ID_DETALLE_HISTORIAL)
                                         FROM
                                            DB_COMUNICACION.INFO_COMUNICACION COM,
                                            DB_SOPORTE.INFO_DETALLE           DETALLE,
                                            DB_SOPORTE.ADMI_TAREA             TAREA,
                                            DB_SOPORTE.INFO_DETALLE_HISTORIAL HIST
                                         WHERE COM.ID_COMUNICACION = DET.COMUNICACION_ID
                                           AND COM.DETALLE_ID      = DETALLE.ID_DETALLE
                                           AND DETALLE.TAREA_ID    = TAREA.ID_TAREA
                                           AND DETALLE.ID_DETALLE  = HIST.DETALLE_ID)
                                    ) NOT IN (:estadosTareas)
                                GROUP BY DET.COMUNICACION_ID
                            ) TAREAS_ABIERTAS,
                            (SELECT ZONANUEVA.NOMBRE_ZONA
                                FROM DB_GENERAL.ADMI_ZONA ZONANUEVA
                             WHERE ZONANUEVA.ID_ZONA = CAB.ZONA_PRESTADA_ID
                            ) ZONA_PRESTADA
                          FROM
                             DB_COMERCIAL.ADMI_CUADRILLA          CUADRILLA,
                             DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB,
                             DB_GENERAL.ADMI_ZONA                 ZONA,
                             DB_SOPORTE.ADMI_INTERVALO            INTERVALO,
                             DB_COMERCIAL.ADMI_DEPARTAMENTO       DEPARTAMENTO ".
                             $strFrom.
                          "WHERE CUADRILLA.ES_HAL       = 'S'
                          AND CUADRILLA.DEPARTAMENTO_ID = DEPARTAMENTO.ID_DEPARTAMENTO ".
                            $strWhereEstado.
                            "AND CUADRILLA.ID_CUADRILLA = CAB.CUADRILLA_ID
                            AND CAB.INTERVALO_ID       = INTERVALO.ID_INTERVALO
                            AND CAB.ESTADO             <> :estadoEliminado
                            AND CAB.ZONA_ID            = ZONA.ID_ZONA".
                          $strWhere;

            $objNativeQuery->setParameter("estado"          , 'Activo');
            $objNativeQuery->setParameter("estadoLiberado"  , 'Liberado');
            $objNativeQuery->setParameter("estadoEliminado" , 'Eliminado');
            $objNativeQuery->setParameter("estadosTareas"   , array('Finalizada','Cancelada','Anulada','Rechazada'));
            $objNativeQuery->setParameter("nombreParametroActividad" , 'PREFERENCIAS_CUADRILLAS_HAL');
 
            $objResultSetMap->addScalarResult('ID_CAB'             , 'idCab'          , 'integer');
            $objResultSetMap->addScalarResult('ID_CUADRILLA'       , 'idCuadrilla'    , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_CUADRILLA'   , 'cuadrilla'      , 'string');
            $objResultSetMap->addScalarResult('ID_ZONA'            , 'idZona'         , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_ZONA'        , 'zona'           , 'string');
            $objResultSetMap->addScalarResult('ID_INTERVALO'       , 'idIntervalo'    , 'integer');
            $objResultSetMap->addScalarResult('INTERVALO_EFECTIVO' , 'intervalo'      , 'string');
            $objResultSetMap->addScalarResult('FE_TRABAJO'         , 'feTrabajo'      , 'string');
            $objResultSetMap->addScalarResult('HORA_INICIO'        , 'horaInicio'     , 'string');
            $objResultSetMap->addScalarResult('HORA_FIN'           , 'horaFin'        , 'string');
            $objResultSetMap->addScalarResult('ESTADO'             , 'estado'         , 'string');
            $objResultSetMap->addScalarResult('TAREAS_ABIERTAS'    , 'tareasAbiertas' , 'integer');
            $objResultSetMap->addScalarResult('ZONA_PRESTADA'      , 'zonaPrestada'   , 'string');
            $objResultSetMap->addScalarResult('ZONA_PRESTADA_ID'   , 'idzonaPrestada' , 'integer');
            $objResultSetMap->addScalarResult('ACTIVIDAD'          , 'actividad'      , 'string');
            
            $objNativeQuery->setSQL($strSql);

            $arrayResultado = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.getArrayPlanificacionAgendaHAL -> ".$objException->getMessage());
        }

        return $arrayResultado;
    }

    /**
     *
     * Metodo encargado de realizar la consulta general de las planificaciones generadas HAL
     *
     * Costo 8
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 13-04-2018
     *
     * Costo 20
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 20-06-2018 - Se modifica el query para obtener la correcta fecha de inicio y fecha fin de trabajo de las cuadrillas y
     *                           mostrar las planificaciones separadas, por motivos que se mostraba una sola cuando pertenecian a la misma zona.
     *                         - Se agrega el filtro por zona.
     *
     * Costo 11
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 07-08-2018 - Se agrega filtros por fecha de trabajo
     *                         - Se modifica el query para que retorne la cantidad correcta de tareas abiertas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 26-09-2018 - Se parametriza el strCodEmpresa.
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.6 - 19-17-2021 - Se agregan en los filtros: el estado "Prestado" y el "CoordinadorPrestadoId", con el objetivo
     * de que  se muestre la planificacion en la Agenda, tanto para el Coordinador principal, como para el Coordinador termporal
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.7 - 16-01-2023 - Se agrega nuevos filtros para la consulta de la planificación Hal cuadrillas en base 
     *                              al Departamento u Oficina y Departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.8 - 05-06-2023 - Se Quita validación de cuadrillas pertenecientes al id del propietario.
     * 
     * @param Array $arrayParametros [
     *                                  intIdCuadrilla  => Id de la cuadrilla,
     *                                  intIdZona       => Id de la zona,
     *                                  strFechaIni     => Fecha de trabajo inicio,
     *                                  strFechaFin     => Fecha de trabajo fin,
     *                                  intIdPersonaRol => Id de la persona empresa rol
     *                               ]
     * @return Array
     */
    public function getArrayPlanificacionGeneralHAL($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strWhere = '';
        $strWhereEstado = '';
        $strSelectParam  = '';
        $strFrom = '';

        if (isset($arrayParametros['strNombreDepartamento']) && $arrayParametros['strNombreDepartamento'] == 'Operaciones Urbanas')
        {
            if (isset($arrayParametros['strBuscarPor']) &&
                $arrayParametros['strBuscarPor'] == 'oficina' &&
                isset($arrayParametros['intOficinaId']) &&
                isset($arrayParametros['intDepartamentoId']))
            {
                $strFrom  .= ', DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFOPERSONA ';
                $strWhere .= " AND (CUADRILLA.COORDINADOR_PRINCIPAL_ID = INFOPERSONA.ID_PERSONA_ROL 
                                OR CUADRILLA.COORDINADOR_PRESTADO_ID = INFOPERSONA.ID_PERSONA_ROL) 
                                AND INFOPERSONA.OFICINA_ID = :intOficinaId 
                                AND DEPARTAMENTO.ID_DEPARTAMENTO = :intDepartamentoId ";

                $objNativeQuery->setParameter("intDepartamentoId" , $arrayParametros['intDepartamentoId']);
                $objNativeQuery->setParameter("intOficinaId", $arrayParametros['intOficinaId']);
            }
            
            if( isset($arrayParametros['strBuscarPor']) &&
                $arrayParametros['strBuscarPor'] == 'departamento' &&
                isset($arrayParametros['intDepartamentoId']))
            {
                $strWhere .= ' AND DEPARTAMENTO.ID_DEPARTAMENTO = :intDepartamentoId '; 
                $objNativeQuery->setParameter("intDepartamentoId", $arrayParametros['intDepartamentoId']);
            }
        }
        
        if(isset($arrayParametros['intIdCuadrilla']) && !empty($arrayParametros['intIdCuadrilla']))
        {
            $strWhere .= ' AND CUADRILLA.ID_CUADRILLA = :cuadrilla ';
            $objNativeQuery->setParameter("cuadrilla",      $arrayParametros['intIdCuadrilla']);
        }

        if(isset($arrayParametros['intIdZona']) && !empty($arrayParametros['intIdZona']))
        {
            $strWhere .= ' AND ZONA.ID_ZONA = :intIdZona ';
            $objNativeQuery->setParameter("intIdZona",      $arrayParametros['intIdZona']);
        }

        if(isset($arrayParametros['strFechaIni']) && !empty($arrayParametros['strFechaIni']))
        {
            $strWhere .= " AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') >= :strFechaIni ";
            $objNativeQuery->setParameter("strFechaIni", $arrayParametros['strFechaIni']);
        }

        if(isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']))
        {
            $strWhere .= " AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') <= :strFechaFin ";
            $objNativeQuery->setParameter("strFechaFin", $arrayParametros['strFechaFin']);
        }

        if (isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
        {
            $strWhere .= " AND CAB.EMPRESA_COD = :empresaCod ";
            $strSelectParam .= " AND PDET.EMPRESA_COD = :empresaCod ";
            $objNativeQuery->setParameter("empresaCod" , $arrayParametros['strCodEmpresa']);
        }

        $strSql =  "SELECT T.*
                        FROM
                            (SELECT
                                PLANIFICACION.ID_CUADRILLA,
                                PLANIFICACION.NOMBRE_CUADRILLA,
                                PLANIFICACION.ID_ZONA,
                                PLANIFICACION.NOMBRE_ZONA,
                                PLANIFICACION.ID_INTERVALO,
                                PLANIFICACION.INTERVALO,
                                MIN(PLANIFICACION.FE_INICIO) FE_INICIO,
                                MAX(PLANIFICACION.FE_FIN) FE_FIN,
                                PLANIFICACION.FE_CREACION,
                                PLANIFICACION.ACTIVIDAD,
                                SUM(PLANIFICACION.TAREAS_ABIERTAS) TAREAS_ABIERTAS
                            FROM
                                (SELECT
                                    CUADRILLA.ID_CUADRILLA,
                                    CUADRILLA.NOMBRE_CUADRILLA,
                                    ZONA.ID_ZONA,
                                    ZONA.NOMBRE_ZONA,
                                    INTERVALO.ID_INTERVALO,
                                    TO_CHAR(INTERVALO.HORA_INI,'HH24:MI') ||' - ' ||TO_CHAR(INTERVALO.HORA_FIN,'HH24:MI') INTERVALO,
                                    (SELECT MAX(PDET.VALOR2) FROM DB_GENERAL.ADMI_PARAMETRO_CAB PCAB 
                                    JOIN DB_GENERAL.ADMI_PARAMETRO_DET PDET ON PDET.PARAMETRO_ID = PCAB.ID_PARAMETRO
                                    WHERE PCAB.NOMBRE_PARAMETRO = :nombreParametroActividad
                                    ".$strSelectParam." AND UPPER(PDET.VALOR1) = UPPER(CAB.ACTIVIDAD) ) ACTIVIDAD,
                                    TRUNC(TRUNC(CAB.FE_TRABAJO))  FE_INICIO,
                                    TRUNC(TRUNC(CAB.FE_TRABAJO))  FE_FIN,
                                    TRUNC(TRUNC(CAB.FE_CREACION)) FE_CREACION,
                                    (SELECT
                                        COUNT(COUNT(DET.COMUNICACION_ID))
                                      FROM
                                        DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET
                                      WHERE DET.CUADRILLA_PLANIF_CAB_ID = CAB.ID_CUADRILLA_PLANIF_CAB
                                        AND DET.COMUNICACION_ID IS NOT NULL
                                        AND (SELECT
                                                IDH.ESTADO
                                            FROM
                                                DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH
                                            WHERE IDH.ID_DETALLE_HISTORIAL =
                                                (SELECT
                                                    MAX(HIST.ID_DETALLE_HISTORIAL)
                                                 FROM
                                                    DB_COMUNICACION.INFO_COMUNICACION COM,
                                                    DB_SOPORTE.INFO_DETALLE           DETALLE,
                                                    DB_SOPORTE.ADMI_TAREA             TAREA,
                                                    DB_SOPORTE.INFO_DETALLE_HISTORIAL HIST
                                                 WHERE COM.ID_COMUNICACION = DET.COMUNICACION_ID
                                                   AND COM.DETALLE_ID      = DETALLE.ID_DETALLE
                                                   AND DETALLE.TAREA_ID    = TAREA.ID_TAREA
                                                   AND DETALLE.ID_DETALLE  = HIST.DETALLE_ID)
                                            ) NOT IN (:estadosTareas)
                                        GROUP BY DET.COMUNICACION_ID
                                    ) TAREAS_ABIERTAS
                                  FROM
                                    DB_COMERCIAL.ADMI_CUADRILLA          CUADRILLA,
                                    DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB,
                                    DB_GENERAL.ADMI_ZONA                 ZONA,
                                    DB_SOPORTE.ADMI_INTERVALO            INTERVALO,
                                    DB_COMERCIAL.ADMI_DEPARTAMENTO       DEPARTAMENTO ".
                                    $strFrom.
                                  "WHERE CUADRILLA.ES_HAL       = 'S'
                                    AND CUADRILLA.DEPARTAMENTO_ID = DEPARTAMENTO.ID_DEPARTAMENTO ".
                                    $strWhereEstado.
                                    "AND CUADRILLA.ID_CUADRILLA = CAB.CUADRILLA_ID
                                    AND CAB.ESTADO             = :estado
                                    AND CAB.ZONA_ID            = ZONA.ID_ZONA
                                    AND CAB.INTERVALO_ID       = INTERVALO.ID_INTERVALO".
                                  $strWhere.
                                " ) PLANIFICACION
                                GROUP BY PLANIFICACION.ID_CUADRILLA,
                                    PLANIFICACION.NOMBRE_CUADRILLA,
                                    PLANIFICACION.ID_ZONA,
                                    PLANIFICACION.NOMBRE_ZONA,
                                    PLANIFICACION.ID_INTERVALO,
                                    PLANIFICACION.INTERVALO,
                                    PLANIFICACION.FE_CREACION,
                                    PLANIFICACION.ACTIVIDAD
                            ) T ORDER BY T.FE_CREACION DESC, T.FE_INICIO";

        $objNativeQuery->setParameter("estadosTareas" , array('Finalizada','Cancelada','Anulada','Rechazada'));
        $objNativeQuery->setParameter("estado"        , 'Activo');
        $objNativeQuery->setParameter("nombreParametroActividad" , 'PREFERENCIAS_CUADRILLAS_HAL');

        $objResultSetMap->addScalarResult('ID_CUADRILLA',    'idCuadrilla', 'integer');
        $objResultSetMap->addScalarResult('NOMBRE_CUADRILLA','cuadrilla',   'string');
        $objResultSetMap->addScalarResult('ID_ZONA',         'idZona',      'integer');
        $objResultSetMap->addScalarResult('NOMBRE_ZONA',     'zona',        'string');
        $objResultSetMap->addScalarResult('ID_INTERVALO',    'idIntervalo', 'integer');
        $objResultSetMap->addScalarResult('INTERVALO',       'intervalo',    'string');
        $objResultSetMap->addScalarResult('FE_INICIO',       'feInicio',     'string');
        $objResultSetMap->addScalarResult('FE_FIN',          'feFin',        'string');
        $objResultSetMap->addScalarResult('TAREAS_ABIERTAS', 'tareasAbiertas','integer');
        $objResultSetMap->addScalarResult('ACTIVIDAD',       'actividad',     'string');
        $objResultSetMap->addScalarResult('FE_CREACION',     'feCreacion','string');

        $objNativeQuery->setSQL($strSql);

        $arrayResultado = $objNativeQuery->getResult();

        return $arrayResultado;
    }

    /**
     *
     * Metodo encargado de realizar la consulta del detalle de planificacion diaria generada por HAL
     *
     * Costo 5
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 12-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 26-06-2018 - Se modifica el query para que devuelva el tipo de proceso.
     *
     * Costo 6
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 09-08-2018 - Se modifica el query para que devuelva el estado correcto de la tarea.
     *
     * @param $intIdCabecera
     * @return Array
     */
    public function getArrayDetallePlanificacionDiaria($intIdCabecera)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery = $this->_em->createNativeQuery(null, $objResultSetMap);

        $strSql = "SELECT ".
                    "DET.ID_CUADRILLA_PLANIF_DET      ID_DET, ".
                    "TO_CHAR(DET.FE_INICIO,'HH24:MI') HORA_INICIO, ".
                    "TO_CHAR(DET.FE_FIN,'HH24:MI')    HORA_FIN, ".
                    "DET.ESTADO, ".
                    "DET.COMUNICACION_ID, ".
                    "DET.TIPO_PROCESO TIPO_PROCESO, ".
                    "(CASE DET.COMUNICACION_ID WHEN NULL THEN '' ELSE ( ".
                        "SELECT ".
                            "TAREA.NOMBRE_TAREA ".
                        "FROM ".
                            "DB_COMUNICACION.INFO_COMUNICACION COM, ".
                            "DB_SOPORTE.INFO_DETALLE           DETALLE, ".
                            "DB_SOPORTE.ADMI_TAREA             TAREA ".
                        "WHERE COM.DETALLE_ID      = DETALLE.ID_DETALLE ".
                          "AND DETALLE.TAREA_ID    = TAREA.ID_TAREA ".
                          "AND COM.ID_COMUNICACION = DET.COMUNICACION_ID ) ".
                    "END) NOMBRE_TAREA, ".
                    "(CASE DET.COMUNICACION_ID WHEN NULL THEN '' ELSE ( ".
                        "SELECT ".
                            "IDH.ESTADO  ".
                        "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH  ".
                          "WHERE IDH.ID_DETALLE_HISTORIAL = (SELECT  ".
                            "MAX(HIST.ID_DETALLE_HISTORIAL) ".
                          "FROM ".
                            "DB_COMUNICACION.INFO_COMUNICACION COM, ".
                            "DB_SOPORTE.INFO_DETALLE           DETALLE, ".
                            "DB_SOPORTE.INFO_DETALLE_HISTORIAL HIST, ".
                            "DB_SOPORTE.ADMI_TAREA             TAREA ".
                          "WHERE COM.DETALLE_ID    = DETALLE.ID_DETALLE ".
                            "AND DETALLE.TAREA_ID    = TAREA.ID_TAREA ".
                            "AND COM.ID_COMUNICACION = DET.COMUNICACION_ID ".
                            "AND HIST.DETALLE_ID     = DETALLE.ID_DETALLE)) ".
                    "END) ESTADO_TAREA ".
                  "FROM ".
                    "DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET ".
                  "WHERE DET.CUADRILLA_PLANIF_CAB_ID = :cabecera ".
                    "AND DET.ESTADO                 <> :estadoEliminado ".
                  "ORDER BY DET.FE_INICIO ASC";

        $objNativeQuery->setParameter("cabecera",        $intIdCabecera);
        $objNativeQuery->setParameter("estadoEliminado", 'Eliminado');

        $objResultSetMap->addScalarResult('ID_DET',          'idDet',      'integer');
        $objResultSetMap->addScalarResult('HORA_INICIO',     'horaInicio', 'string');
        $objResultSetMap->addScalarResult('HORA_FIN',        'horaFin',    'string');
        $objResultSetMap->addScalarResult('COMUNICACION_ID', 'idTarea',    'integer');
        $objResultSetMap->addScalarResult('NOMBRE_TAREA',    'nombreTarea','string');
        $objResultSetMap->addScalarResult('ESTADO_TAREA',    'estadoTarea','string');
        $objResultSetMap->addScalarResult('ESTADO',          'estado',     'string');
        $objResultSetMap->addScalarResult('TIPO_PROCESO',    'tipoProceso','string');

        $objNativeQuery->setSQL($strSql);

        $arrayResultado = $objNativeQuery->getResult();

        return $arrayResultado;
    }

   /**
     * Función que se encarga de verificar si la tarea existe en Hal
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 11-04-2018
     *
     * @param  $arrayParametros [
     *                              intNumeroTarea    = Numero de la tarea,
     *                              strEstadoCab      = Estado de la tabla INFO_CUADRILLA_PLANIF_CAB,
     *                              strEstadoDet      = Estado de la tabla INFO_CUADRILLA_PLANIF_DET
     *                          ]
     * @return Array
     */
    public function tareaExisteEnHal($arrayParametros)
    {
        $boolExiteTareaHal = false;

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSql =  "SELECT COUNT(DET.COMUNICACION_ID) CANTIDAD "
                       ."FROM "
                           ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB, "
                           ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET "
                       ."WHERE CAB.ID_CUADRILLA_PLANIF_CAB = DET.CUADRILLA_PLANIF_CAB_ID "
                        ."AND DET.COMUNICACION_ID = :intNumeroTarea ";

            // Parametros principales
            $objQuery->setParameter("intNumeroTarea", $arrayParametros['intNumeroTarea']);

            if ($arrayParametros['strEstadoCab'])
            {
                $strSql .= "AND CAB.ESTADO = :strEstadoCab ";
                $objQuery->setParameter("strEstadoCab", $arrayParametros['strEstadoCab']);
            }

            if ($arrayParametros['strEstadoDet'])
            {
                $strSql .= "AND DET.ESTADO = :strEstadoDet ";
                $objQuery->setParameter("strEstadoDet", $arrayParametros['strEstadoDet']);
            }

            $objRsm->addScalarResult('CANTIDAD','cantidad','integer');

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getOneOrNullResult();

            if (!empty($arrayResultado) && count($arrayResultado) > 0 && $arrayResultado['cantidad'] > 0)
            {
                $boolExiteTareaHal = true;
            }

            $arrayRespuesta["resultado"]   = 'ok';
            $arrayRespuesta["existeTarea"] = $boolExiteTareaHal;
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.tareaExisteEnHal -> ".$objException->getMessage());
            $arrayRespuesta["resultado"] = 'fail';
            $arrayRespuesta["mensaje"]   = $objException->getMessage();
        }

        return $arrayRespuesta;
    }

    /**
     * Método que procesa el procedure de la base de datos para asignar las solicitudes y tareas a una cudrilla
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0
     *
     * @param  $arrayParametros [
     *                              dateFechaInicio   : Fecha de inicio,
     *                              dateFechaFin      : Fecha fin,
     *                              dateFeIniOrigen   : Fecha de inicio a limpiar u/o liberar,
     *                              dateFeFinOrigen   : Fecha fin a limpiar u/o liberar,
     *                              intIdCuadrilla    : id de la cuadrilla,
     *                              intIdSolicitud    : id de la solicitud,
     *                              intIdComunicacion : numero de tarea,
     *                              strOpcion         : Opcion,
     *                              strUser           : Usuario,
     *                          ]
     * @return $arrayRespuesta
     */
    public function setAsignacionPlanifCuadrilla($arrayParametros)
    {
        try
        {
            /* Convert Fecha Inicio */
            $arrayFeInicio    = explode(" ", $arrayParametros['dateFechaInicio']);
            $arrayDateIni     = explode("-", $arrayFeInicio[0]);
            $arrayHoraIni     = explode(":", $arrayFeInicio[1]);

            if (count($arrayDateIni) != 3 || count($arrayHoraIni) != 3)
            {
                $arrayRespuesta["mensaje"]     = 'fail';
                $arrayRespuesta["descripcion"] = "Formato de fecha inicio incorrecto";

                return $arrayRespuesta;
            }

            $arrayFechaInicio = date("Y/m/d H:i:s",
                strtotime($arrayDateIni[0]."-".$arrayDateIni[1]."-".$arrayDateIni[2] . " " . $arrayFeInicio[1]));

            /* Convert Fecha Fin */
            $arrayFeFin     = explode(" ", $arrayParametros['dateFechaFin']);
            $arrayDateFin   = explode("-", $arrayFeFin[0]);
            $arrayHoraFin   = explode(":", $arrayFeFin[1]);

            if (count($arrayDateFin) != 3 || count($arrayHoraFin) != 3)
            {
                $arrayRespuesta["mensaje"]     = 'fail';
                $arrayRespuesta["descripcion"] = "Formato de fecha fin incorrecto";

                return $arrayRespuesta;
            }

            $arrayFechaFin  = date("Y/m/d H:i:s",
                strtotime($arrayDateFin[0]."-".$arrayDateFin[1]."-".$arrayDateFin[2] . " " . $arrayFeFin[1]));

            /* Convert Fecha Origen Inicio */
            if ($arrayParametros['dateFeIniOrigen'])
            {
                $arrayFeOrigenInicio1 = explode(" ", $arrayParametros['dateFeIniOrigen']);
                $arrayFeOrigenInicio2 = explode("-", $arrayFeOrigenInicio1[0]);
                $strFeOrigenInicio    = strtotime($arrayFeOrigenInicio2[0]."-".
                                                  $arrayFeOrigenInicio2[1]."-".
                                                  $arrayFeOrigenInicio2[2]." ".
                                                  $arrayFeOrigenInicio1[1]);
                $objFeOrigenInicio    = date("Y/m/d H:i:s",$strFeOrigenInicio);
            }

            /* Convert Fecha Origen Fin */
            if ($arrayParametros['dateFeFinOrigen'])
            {
                $arrayFeOrigenFin1 = explode(" ", $arrayParametros['dateFeFinOrigen']);
                $arrayFeOrigenFin2 = explode("-", $arrayFeOrigenFin1[0]);
                $strFeOrigenFin    = strtotime($arrayFeOrigenFin2[0]."-".
                                               $arrayFeOrigenFin2[1]."-".
                                               $arrayFeOrigenFin2[2]." ".
                                               $arrayFeOrigenFin1[1]);
                $objFeOrigenFin    = date("Y/m/d H:i:s",$strFeOrigenFin);
            }

            $strSql =  "BEGIN DB_SOPORTE.SPKG_PLANIFICACION_CUADRILLAS.P_ASIGNAR_SOLICITUD_TAREA(:dateFechaInicio, "
                                                                                               .":dateFechaFin, "
                                                                                               .":dateFeOrigenIni, "
                                                                                               .":dateFeOrigenFin, "
                                                                                               .":intIdCuadrilla, "
                                                                                               .":intIdSolicitud, "
                                                                                               .":intIdComunicacion, "
                                                                                               .":strOpcion, "
                                                                                               .":strUser, "
                                                                                               .":strMensajeError); "
                     . "END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $strMensajeError = str_repeat(' ', 5000);
            $objStmt->bindParam('dateFechaInicio'   , $arrayFechaInicio);
            $objStmt->bindParam('dateFechaFin'      , $arrayFechaFin);
            $objStmt->bindParam('dateFeOrigenIni'   , $objFeOrigenInicio);
            $objStmt->bindParam('dateFeOrigenFin'   , $objFeOrigenFin);
            $objStmt->bindParam('intIdCuadrilla'    , $arrayParametros['intIdCuadrilla']);
            $objStmt->bindParam('intIdSolicitud'    , $arrayParametros['intIdSolicitud']);
            $objStmt->bindParam('intIdComunicacion' , $arrayParametros['intIdComunicacion']);
            $objStmt->bindParam('strOpcion'         , $arrayParametros['strOpcion']);
            $objStmt->bindParam('strUser'           , $arrayParametros['strUser']);
            $objStmt->bindParam('strMensajeError'   , $strMensajeError);
            $objStmt->execute();

            if (strtoupper($strMensajeError) === 'OK')
            {
                $arrayRespuesta["mensaje"]     = $strMensajeError;
                $arrayRespuesta["descripcion"] = "La informacion se registro correctamente.";
            }
            else
            {
                $arrayRespuesta["mensaje"]     = 'fail';
                $arrayRespuesta["descripcion"] = $strMensajeError;
            }
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.setAsignacionPlanifCuadrilla -> ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Función que se encarga de obtener el detalle de planificación de las cuadrillas
     *
     * Costo 20
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 05-07-2018 - Se agrega en el query el TIPO_PROCESO para que sea retornado como pOrigen.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 18-07-2018 - Se agrega en el query la columna VISUALIZAR_MOVIL para que sea retornado como mostrarMovil.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 12-09-2018 - Se agrega en el query las siguiente columnas para que sean retornadas
     *                           FE_CREACION,USR_CREACION,FE_MODIFICACION,USR_MODIFICACION,ZONA_PRESTADA_ID
     *
     * @param  $arrayParametros [
     *                              arrayIdCab        = lista de id de la tabla INFO_CUADRILLA_PLANIF_CAB,
     *                              arrayIdDet        = lista de id de la tabla INFO_CUADRILLA_PLANIF_DET,
     *                              intIdZona         = Id de la zona,
     *                              intIdCuadrilla    = Id de la cuadrilla,
     *                              intIdComunicacion = Id de comunicacion,
     *                              strFechaIni       = Fecha de trabajo Inicio,
     *                              strFechaFin       = Fecha de trabajo Fin,
     *                              strEstadoCab      = Estado de la tabla INFO_CUADRILLA_PLANIF_CAB,
     *                              strEstadoDet      = Estado de la tabla INFO_CUADRILLA_PLANIF_DET
     *                          ]
     * @return Array
     */
    public function getSolicitarDetallePlanificacion($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT CAB.ID_CUADRILLA_PLANIF_CAB ID_CUADRILLA_PLANIF_CAB, "
                           . "DET.ID_CUADRILLA_PLANIF_DET ID_CUADRILLA_PLANIF_DET, "
                           . "TO_CHAR(DET.FE_INICIO,'YYYY-MM-DD HH24:MI:SS') FE_INICIO, "
                           . "TO_CHAR(DET.FE_FIN,'YYYY-MM-DD HH24:MI:SS') FE_FIN, "
                           . "DET.COMUNICACION_ID COMUNICACION_ID, "
                           . "CAB.CUADRILLA_ID CUADRILLA_ID, "
                           . "CAB.ZONA_ID ZONA_ID, "
                           . "CAB.ZONA_PRESTADA_ID ZONA_PRESTADA_ID, "
                           . "TO_CHAR(DET.FE_CREACION,'RRRR-MM-DD HH24:MI:SS') FE_CREACION, "
                           . "DET.USR_CREACION USR_CREACION, "
                           . "TO_CHAR(DET.FE_MODIFICACION,'RRRR-MM-DD HH24:MI:SS') FE_MODIFICACION, "
                           . "DET.USR_MODIFICACION USR_MODIFICACION, "
                           . "DET.TIPO_PROCESO TIPO_PROCESO, "
                           . "DET.VISUALIZAR_MOVIL VISUALIZAR_MOVIL, "
                           . "CAB.ESTADO ESTADOCAB, "
                           . "DET.ESTADO ESTADODET "
                          ."FROM "
                              ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB, "
                              ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET "
                            ."WHERE CAB.ID_CUADRILLA_PLANIF_CAB = DET.CUADRILLA_PLANIF_CAB_ID ";

            $objRsm->addScalarResult('ID_CUADRILLA_PLANIF_CAB' , 'idCuadrillaPlanifCab' , 'integer');
            $objRsm->addScalarResult('ID_CUADRILLA_PLANIF_DET' , 'idCuadrillaPlanifDet' , 'integer');
            $objRsm->addScalarResult('FE_INICIO'               , 'feInicio'             , 'string');
            $objRsm->addScalarResult('FE_FIN'                  , 'feFin'                , 'string');
            $objRsm->addScalarResult('COMUNICACION_ID'         , 'idComunicacion'       , 'integer');
            $objRsm->addScalarResult('CUADRILLA_ID'            , 'idCuadrilla'          , 'integer');
            $objRsm->addScalarResult('ZONA_ID'                 , 'idZona'               , 'integer');
            $objRsm->addScalarResult('TIPO_PROCESO'            , 'pOrigen'              , 'string');
            $objRsm->addScalarResult('ESTADOCAB'               , 'estadoCab'            , 'string');
            $objRsm->addScalarResult('ESTADODET'               , 'estadoDet'            , 'string');
            $objRsm->addScalarResult('VISUALIZAR_MOVIL'        , 'mostrarMovil'         , 'string');
            $objRsm->addScalarResult('FE_CREACION'             , 'feCreacion'           , 'string');
            $objRsm->addScalarResult('USR_CREACION'            , 'usrCreacion'          , 'string');
            $objRsm->addScalarResult('FE_MODIFICACION'         , 'feModificacion'       , 'string');
            $objRsm->addScalarResult('USR_MODIFICACION'        , 'usrModificacion'      , 'string');
            $objRsm->addScalarResult('ZONA_PRESTADA_ID'        , 'idZonaPrestada'       , 'integer');

            if (!empty($arrayParametros['arrayIdCab']) && count($arrayParametros['arrayIdCab']) > 0)
            {
                $strSql .= "AND CAB.ID_CUADRILLA_PLANIF_CAB IN (:arrayIdCab) ";
                $objQuery->setParameter('arrayIdCab', array_values($arrayParametros['arrayIdCab']));
            }

            if (!empty($arrayParametros['arrayIdDet']) && count($arrayParametros['arrayIdDet']) > 0)
            {
                $strSql .= "AND DET.ID_CUADRILLA_PLANIF_DET IN (:arrayIdDet) ";
                $objQuery->setParameter("arrayIdDet", array_values($arrayParametros['arrayIdDet']));
            }

            if ($arrayParametros['intIdZona'])
            {
                $strSql .= "AND CAB.ZONA_ID = :intIdZona ";
                $objQuery->setParameter("intIdZona", $arrayParametros['intIdZona']);
            }

            if ($arrayParametros['intIdCuadrilla'])
            {
                $strSql .= "AND CAB.CUADRILLA_ID = :intIdcuadrilla ";
                $objQuery->setParameter("intIdcuadrilla", $arrayParametros['intIdCuadrilla']);
            }

            if ($arrayParametros['intIdComunicacion'])
            {
                $strSql .= "AND DET.COMUNICACION_ID = :intIdComunicacion ";
                $objQuery->setParameter("intIdComunicacion", $arrayParametros['intIdComunicacion']);
            }

            if ($arrayParametros['strFechaIni'])
            {
                $strFechaIni  = date("Y-m-d H:i", strtotime($arrayParametros['strFechaIni']));
                $strSql .= "AND TO_CHAR(DET.FE_INICIO,'RRRR-MM-DD HH24:MI') >= :strFechaIni ";
                $objQuery->setParameter("strFechaIni", $strFechaIni);
            }

            if ($arrayParametros['strFechaFin'])
            {
                $strFechaFin  = date("Y-m-d H:i", strtotime($arrayParametros['strFechaFin']));
                $strSql .= "AND TO_CHAR(DET.FE_INICIO,'RRRR-MM-DD HH24:MI') <= :strFechaFin ";
                $objQuery->setParameter("strFechaFin", $strFechaFin);
            }

            if ($arrayParametros['strEstadoCab'])
            {
                $strSql .= "AND CAB.ESTADO = :strEstadoCab ";
                $objQuery->setParameter("strEstadoCab", $arrayParametros['strEstadoCab']);
            }

            if ($arrayParametros['strEstadoDet'])
            {
                $strSql .= "AND DET.ESTADO = :strEstadoDet ";
                $objQuery->setParameter("strEstadoDet", $arrayParametros['strEstadoDet']);
            }

            $strSql .= "ORDER BY CAB.CUADRILLA_ID, CAB.FE_TRABAJO, DET.FE_INICIO";

            $objQuery->setSQL($strSql);

            $arrayRespuesta["mensaje"]       = 'ok';
            $arrayRespuesta["planificacion"] = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.getSolicitarDetallePlanificacion -> ".$objException->getMessage());
            $arrayRespuesta["mensaje"]       = 'fail';
            $arrayRespuesta["descripcion"]   = $objException->getMessage();
        }

        return $arrayRespuesta;
    }

    /**
     * Función que se encarga de obtener el detalle de trabajo de las cuadrillas
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 12-09-2018 - Se agrega en el query las siguiente columnas para que sean retornadas
     *                           FE_CREACION,USR_CREACION,FE_MODIFICACION,USR_MODIFICACION,ZONA_PRESTADA_ID
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.2 15-12-2020 - Se agrega en el query las siguiente columnas para que sean retornadas
     *                           ACTIVIDAD, DESCRIPCION_ACTIVIDAD
     *
     * @param  $arrayParametros [
     *                              arrayIdCab         = Lista de id de la tabla INFO_CUADRILLA_PLANIF_CAB,
     *                              idIntervalo        = Id del intervalo,
     *                              idZona             = Id de la zona,
     *                              intIdCuadrilla     = Id de la cuadrilla,
     *                              strFechaIni        = Fecha de trabajo Inicio,
     *                              strFechaFin        = Fecha de trabajo Fin
     *                              strEstadoIntervalo = Estado de la tabla ADMI_INTERVALO,
     *                              strEstadoPlanifCab = Estado de la tabla INFO_CUADRILLA_PLANIF_CAB
     *                          ]
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 14/10/2020
     * Se agregan campos "AUTORIZA_FINALIZAR" y "AUTORIZA_ALIMENTACION" en la consulta.
     * 
     * @return Array
     */
    public function getSolicitarTrabajoCuadrilla($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT CAB.ID_CUADRILLA_PLANIF_CAB ID_CUADRILLA_PLANIF_CAB, "
                           . "CAB.INTERVALO_ID ID_INTERVALO, "
                           . "CAB.AUTORIZA_FINALIZAR, "
                           . "CAB.AUTORIZA_ALIMENTACION, "
                           . "CAB.CUADRILLA_ID CUADRILLA_ID, "
                           . "TO_CHAR(INTERVALO.HORA_INI,'HH24:MI:SS') HORA_INI, "
                           . "TO_CHAR(INTERVALO.HORA_FIN,'HH24:MI:SS') HORA_FIN, "
                           . "TO_CHAR(CAB.FE_TRABAJO, 'RRRR-MM-DD') FE_TRABAJO, "
                           . "TO_CHAR(CAB.FE_CREACION,'RRRR-MM-DD HH24:MI:SS') FE_CREACION, "
                           . "CAB.USR_CREACION USR_CREACION, "
                           . "TO_CHAR(CAB.FE_MODIFICACION,'RRRR-MM-DD HH24:MI:SS') FE_MODIFICACION, "
                           . "CAB.USR_MODIFICACION USR_MODIFICACION, "
                           . "CAB.ZONA_ID ZONA_ID, "
                           . "CAB.ZONA_PRESTADA_ID ZONA_PRESTADA_ID, "
                           . "INTERVALO.ESTADO ESTADOINTERVALO, "
                           . "CAB.ESTADO ESTADOCAB, "
                           . "CAB.ACTIVIDAD ACTIVIDAD, "
                           . "(SELECT UNIQUE VALOR2 FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE " 
                           . " DESCRIPCION = 'PREFERENCIA CUADRILLA SOPORTE' AND VALOR1 = CAB.ACTIVIDAD) DESCRIPCION_ACTIVIDAD "
                          ."FROM "
                              ."DB_SOPORTE.ADMI_INTERVALO INTERVALO, "
                              ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB "
                          ."WHERE INTERVALO.ID_INTERVALO = CAB.INTERVALO_ID ";

            $objRsm->addScalarResult('ID_CUADRILLA_PLANIF_CAB' ,'idCuadrillaPlanifCab' , 'integer');
            $objRsm->addScalarResult('ID_INTERVALO'            ,'idIntervalo'          , 'integer');
            $objRsm->addScalarResult('CUADRILLA_ID'            ,'idCuadrilla'          , 'integer');
            $objRsm->addScalarResult('HORA_INI'                ,'horaInicio'           , 'string');
            $objRsm->addScalarResult('HORA_FIN'                ,'horaFin'              , 'string');
            $objRsm->addScalarResult('FE_TRABAJO'              ,'feTrabajo'            , 'string');
            $objRsm->addScalarResult('ZONA_ID'                 ,'idZona'               , 'integer');
            $objRsm->addScalarResult('ESTADOINTERVALO'         ,'estadoIntervalo'      , 'string');
            $objRsm->addScalarResult('ESTADOCAB'               ,'estadoPlanifCab'      , 'string');
            $objRsm->addScalarResult('FE_CREACION'             ,'feCreacion'           , 'string');
            $objRsm->addScalarResult('USR_CREACION'            ,'usrCreacion'          , 'string');
            $objRsm->addScalarResult('FE_MODIFICACION'         ,'feModificacion'       , 'string');
            $objRsm->addScalarResult('USR_MODIFICACION'        ,'usrModificacion'      , 'string');
            $objRsm->addScalarResult('ZONA_PRESTADA_ID'        ,'idZonaPrestada'       , 'integer');
            $objRsm->addScalarResult('ACTIVIDAD'               ,'actividad'            , 'string');
            $objRsm->addScalarResult('DESCRIPCION_ACTIVIDAD'   ,'descripcionActividad' , 'string');
            $objRsm->addScalarResult('AUTORIZA_FINALIZAR'      ,'autorizaFinalizar'    , 'string');
            $objRsm->addScalarResult('AUTORIZA_ALIMENTACION'   ,'autorizaAlimentacion' , 'string');

            if (!empty($arrayParametros['arrayIdCab']) && count($arrayParametros['arrayIdCab']) > 0)
            {
                $strSql .= "AND CAB.ID_CUADRILLA_PLANIF_CAB IN (:arrayIdCab) ";
                $objQuery->setParameter('arrayIdCab', array_values($arrayParametros['arrayIdCab']));
            }

            if ($arrayParametros['intIdIntervalo'])
            {
                $strSql .= "AND CAB.INTERVALO_ID = :intIdIntervalo ";
                $objQuery->setParameter("intIdIntervalo", $arrayParametros['intIdIntervalo']);
            }

            if ($arrayParametros['intIdCuadrilla'])
            {
                $strSql .= "AND CAB.CUADRILLA_ID = :intIdCuadrilla ";
                $objQuery->setParameter("intIdCuadrilla", $arrayParametros['intIdCuadrilla']);
            }

            if ($arrayParametros['intIdZona'])
            {
                $strSql .= "AND CAB.ZONA_ID = :intIdZona ";
                $objQuery->setParameter("intIdZona", $arrayParametros['intIdZona']);
            }

            if ($arrayParametros['strFechaIni'])
            {
                $strFechaIni  = date("Y-m-d", strtotime($arrayParametros['strFechaIni']));
                $strSql .= "AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') >= :strFechaIni ";
                $objQuery->setParameter("strFechaIni", $strFechaIni);
            }

            if ($arrayParametros['strFechaFin'])
            {
                $strFechaFin  = date("Y-m-d", strtotime($arrayParametros['strFechaFin']));
                $strSql .= "AND TO_CHAR(CAB.FE_TRABAJO,'RRRR-MM-DD') <= :strFechaFin ";
                $objQuery->setParameter("strFechaFin", $strFechaFin);
            }

            if ($arrayParametros['strEstadoIntervalo'])
            {
                $strSql .= "AND INTERVALO.ESTADO = :strEstadoIntervalo ";
                $objQuery->setParameter("strEstadoIntervalo", $arrayParametros['strEstadoIntervalo']);
            }

            if ($arrayParametros['strEstadoPlanifCab'])
            {
                $strSql .= "AND CAB.ESTADO = :strEstadoPlanifCab ";
                $objQuery->setParameter("strEstadoPlanifCab", $arrayParametros['strEstadoPlanifCab']);
            }

            $strSql .= "ORDER BY CAB.CUADRILLA_ID, CAB.FE_TRABAJO ";

            $objQuery->setSQL($strSql);

            $arrayRespuesta["mensaje"]       = 'ok';
            $arrayRespuesta["planificacion"] = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.getSolicitarTrabajoCuadrilla -> ".$objException->getMessage());
            $arrayRespuesta["mensaje"]       = 'fail';
            $arrayRespuesta["descripcion"]   = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Función encargada de la llamda al método que devuelve las horas de trabajo o jornadas de trabajo
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 02-05-2018
     *
     * @param  $arrayParametros [
     *                              strHoraIni = Hora Inicio,
     *                              strHoraFin = Hora fin,
     *                              strEstado  = Estado
     *                          ]
     * @return Array
     */
    public function getSolicitarIntervalosTrabajo($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT INTERVALO.ID_INTERVALO ID_INTERVALO, "
                           . "TO_CHAR(INTERVALO.HORA_INI,'HH24:MI:SS') HORA_INI, "
                           . "TO_CHAR(INTERVALO.HORA_FIN,'HH24:MI:SS') HORA_FIN, "
                           . "INTERVALO.ESTADO ESTADO "
                          ."FROM "
                              ."DB_SOPORTE.ADMI_INTERVALO INTERVALO "
                          ."WHERE INTERVALO.ID_INTERVALO = INTERVALO.ID_INTERVALO ";

            $objRsm->addScalarResult('ID_INTERVALO','idIntervalo','integer');
            $objRsm->addScalarResult('HORA_INI'    ,'horaIni','string');
            $objRsm->addScalarResult('HORA_FIN'    ,'horaFin','string');
            $objRsm->addScalarResult('ESTADO'      ,'estado','string');

            if ($arrayParametros['intIdIntervalo'])
            {
                $strSql .= "AND INTERVALO.ID_INTERVALO = :intIdIntervalo ";
                $objQuery->setParameter("intIdIntervalo", $arrayParametros['intIdIntervalo']);
            }

            if ($arrayParametros['strHoraIni'])
            {
                $strHoraIni = date("H:i", strtotime($arrayParametros['strHoraIni']));
                $strSql    .= "AND TO_CHAR(INTERVALO.HORA_INI,'HH24:MI') >= :strHoraIni ";
                $objQuery->setParameter("strHoraIni", $strHoraIni);
            }

            if ($arrayParametros['strHoraFin'])
            {
                $strHoraFin = date("H:i", strtotime($arrayParametros['strHoraFin']));
                $strSql    .= "AND TO_CHAR(INTERVALO.HORA_INI,'HH24:MI') <= :strHoraFin ";
                $objQuery->setParameter("strHoraFin", $strHoraFin);
            }

            if ($arrayParametros['strEstado'])
            {
                $strSql .= "AND INTERVALO.ESTADO = :strEstado ";
                $objQuery->setParameter("strEstado", $arrayParametros['strEstado']);
            }

            $strSql .= "ORDER BY INTERVALO.HORA_INI ";

            $objQuery->setSQL($strSql);

            $arrayRespuesta["mensaje"]       = 'ok';
            $arrayRespuesta["planificacion"] = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.getSolicitarIntervalosTrabajo -> ".$objException->getMessage());
            $arrayRespuesta["mensaje"]       = 'fail';
            $arrayRespuesta["descripcion"]   = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Función que se encarga de obtener la jornada de trabajo de una cuadrilla.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @param  $arrayParametros [
     *                              strEstadoCab            = Estado de la tabla INFO_CUADRILLA_PLANIF_CAB
     *                              strEstadoDet            = Estado de la tabla INFO_CUADRILLA_PLANIF_DET
     *                              intIdCuadrillaPlanifCab = Id de la tabla INFO_CUADRILLA_PLANIF_CAB
     *                          ]
     * @return Array
     */
    public function getJornadaDeTrabajo($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT (TO_CHAR(MIN(DET.FE_INICIO),'HH24:MI') || ' - ' || TO_CHAR(MAX(DET.FE_FIN),'HH24:MI')) JORNADA_LABORAL "
                      ."FROM DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CAB, "
                           ."DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DET "
                      ."WHERE CAB.ID_CUADRILLA_PLANIF_CAB = DET.CUADRILLA_PLANIF_CAB_ID "
                        ."AND CAB.ESTADO = :strEstadoCab "
                        ."AND DET.ESTADO = :strEstadoDet "
                        ."AND CAB.ID_CUADRILLA_PLANIF_CAB = :intIdCuadrillaPlanifCab ";

            $objRsm->addScalarResult('JORNADA_LABORAL','jornadaLaboral','string');

            $objQuery->setParameter("strEstadoCab", $arrayParametros['strEstadoCab']);
            $objQuery->setParameter("strEstadoDet", $arrayParametros['strEstadoDet']);
            $objQuery->setParameter("intIdCuadrillaPlanifCab", $arrayParametros['intIdCuadrillaPlanifCab']);

            $objQuery->setSQL($strSql);

            $arrayIntervalo = $objQuery->getResult();

            if (!empty($arrayIntervalo))
            {
                $arrayRespuesta = $arrayIntervalo[0];
            }
        }
        catch (\Exception $objException)
        {
            error_log("Error - InfoCuadrillaPlanifCabRepository.getJornadaDeTrabajo -> ".$objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     *
     * Método encargado de realizar la consulta de las tareas asignadas en la planificación HAL de las cuadrillas.
     *
     * Costo 697
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 13-07-2018
     *
     * @param Array $arrayParametros [
     *                                  intAsignadoId       : Id del asignado,
     *                                  strTipoAsignado     : Tipo Asignado,
     *                                  arrayEstadosTarea   : Estado de las tareas que no deben ser filtradas,
     *                                  strEstadoCab        : Estado de la planificacion cab,
     *                                  strEstadoDet        : Estado de la planificacion Det
     *                               ]
     * @return Array
     */
    public function getPlanificacionTareasHal($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = '';

            if(isset($arrayParametros['intAsignadoId']) && !empty($arrayParametros['intAsignadoId']))
            {
                $strWhere .= 'AND DA.ASIGNADO_ID = :intAsignadoId ';
                $objNativeQuery->setParameter("intAsignadoId", $arrayParametros['intAsignadoId']);
            }

            if(isset($arrayParametros['strTipoAsignado']) && !empty($arrayParametros['strTipoAsignado']))
            {
                $strWhere .= 'AND DA.TIPO_ASIGNADO = :strTipoAsignado ';
                $objNativeQuery->setParameter("strTipoAsignado", $arrayParametros['strTipoAsignado']);
            }

            if(isset($arrayParametros['arrayEstadosTarea']) && !empty($arrayParametros['arrayEstadosTarea']))
            {
                $strWhere .= 'AND DH.ESTADO NOT IN (:arrayEstadosTarea) ';
                $objNativeQuery->setParameter("arrayEstadosTarea", $arrayParametros['arrayEstadosTarea']);
            }

            if(isset($arrayParametros['strEstadoCab']) && !empty($arrayParametros['strEstadoCab']))
            {
                $strWhere .= 'AND CABC.ESTADO = :strEstadoCab ';
                $objNativeQuery->setParameter("strEstadoCab", $arrayParametros['strEstadoCab']);
            }

            if(isset($arrayParametros['strEstadoDet']) && !empty($arrayParametros['strEstadoDet']))
            {
                $strWhere .= 'AND DETC.ESTADO = :strEstadoDet ';
                $objNativeQuery->setParameter("strEstadoDet", $arrayParametros['strEstadoDet']);
            }

            $strSql = "SELECT "
                       . "COM.ID_COMUNICACION ID_COMUNICACION, "
                       . "DET.ID_DETALLE      ID_DETALLE, "
                       . "DH.ESTADO           ESTADOTAREA "
                     . "FROM "
                        . "DB_SOPORTE.INFO_DETALLE              DET, "
                        . "DB_SOPORTE.INFO_DETALLE_ASIGNACION   DA, "
                        . "DB_SOPORTE.INFO_DETALLE_HISTORIAL    DH, "
                        . "DB_SOPORTE.INFO_COMUNICACION         COM, "
                        . "DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB CABC, "
                        . "DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET DETC "
                    . "WHERE DET.ID_DETALLE = DA.DETALLE_ID "
                      . "AND DET.ID_DETALLE = DH.DETALLE_ID "
                      . "AND DET.ID_DETALLE = COM.DETALLE_ID "
                      . "AND CABC.ID_CUADRILLA_PLANIF_CAB = DETC.CUADRILLA_PLANIF_CAB_ID "
                      . "AND COM.ID_COMUNICACION = DETC.COMUNICACION_ID "
                      . "AND CABC.CUADRILLA_ID = DA.ASIGNADO_ID "
                      . $strWhere
                      . "AND DA.ID_DETALLE_ASIGNACION = "
                      . "(SELECT MAX(DAMAX.ID_DETALLE_ASIGNACION) "
                          . "FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION DAMAX WHERE DAMAX.DETALLE_ID = DA.DETALLE_ID) "
                      . "AND DH.ID_DETALLE_HISTORIAL = "
                      . "(SELECT MAX(DHMAX.ID_DETALLE_HISTORIAL) "
                          . "FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL DHMAX WHERE DHMAX.DETALLE_ID = DH.DETALLE_ID) ";

            $objResultSetMap->addScalarResult('ID_COMUNICACION', 'idComunicacion', 'integer');
            $objResultSetMap->addScalarResult('ID_DETALLE'     , 'idDetalle'     , 'integer');
            $objResultSetMap->addScalarResult('ESTADOTAREA'    , 'estadoTarea'   , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayResultado['status']        = 'ok';
            $arrayResultado['planificacion'] = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoCuadrillaPlanifCabRepository.getPlanificacionTareasHal -> ".$objException->getMessage());
            $arrayResultado = array();
            $arrayResultado["status"]      = 'fail';
            $arrayResultado["descripcion"] = $objException->getMessage();
        }
        return $arrayResultado;
    }

    /**
     * Método encargado de actualizar el campo VISUALIZAR_MOVIL para la visualización de las tareas en el telcos móvil
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 17-07-2018
     *
     * @param Array $arrayParametros [
     *                                 intIdComunicacion  = Id de comunicación o conocido como número de tarea,
     *                                 strVisualizarMovil = Valor para visualizar en el telcos móvil (S o N),
     *                                 strUsuario         = Usuario quien realiza la modificación,
     *                                 strIp              = Ip del usuario quien realiza la mofidicación
     *                               ]
     *
     * @return string $strMensajeError;
     */
    public function setVisualizarMovil($arrayParametros)
    {
        $strString       = '';
        $strMensajeError = str_pad($strString, 3000, " ");

        $strSql = "BEGIN DB_SOPORTE.SPKG_PLANIFICACION_CUADRILLAS.P_UPDATE_VISUALIZAR_MOVIL(:Pn_IdComunicacion, "
                                                                                          .":Pv_VisualizarMovil, "
                                                                                          .":Pv_Usuario, "
                                                                                          .":Pv_Ip, "
                                                                                          .":Pv_Error); "
                 ."END;";

        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_IdComunicacion'  , $arrayParametros['intIdComunicacion']);
            $objStmt->bindParam('Pv_VisualizarMovil' , $arrayParametros['strVisualizarMovil']);
            $objStmt->bindParam('Pv_Usuario'         , $arrayParametros['strUsuario']);
            $objStmt->bindParam('Pv_Ip'              , $arrayParametros['strIp']);
            $objStmt->bindParam('Pv_Error'           , $strMensajeError);
            $objStmt->execute();
        }
        catch (\Exception $objException)
        {
            $strMensajeError = "Error InfoCuadrillaPlanifCabRepository.setVisualizarMovil -> Error: ".$objException->getMessage();
            error_log($strMensajeError);
        }
        return $strMensajeError;
    }
}
