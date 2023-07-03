<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoHorarioEmpleadosRepository extends EntityRepository
{
    /*
     * Método encargado obtener la planificacion del empleado aplicacion Horas Extras
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * 
     * @return Array $arrayResultado
     */
    public function getHorarioEmpleado($arrayParametros)
    {
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        try
        {

        $strSql         = " SELECT ID_HORARIO_EMPLEADO,FECHA_INICIO, FECHA_FIN, HORA_INICIO, 
                                   HORA_FIN, CUADRILLA_ID, NO_EMPLE, TIPO_HORARIO_ID, ESTADO 
                            FROM DB_HORAS_EXTRAS.INFO_HORARIO_EMPLEADOS 
                            WHERE NO_EMPLE = :noEmple  
                            AND CUADRILLA_ID = :idCuadrilla
                            AND TIPO_HORARIO_ID = :idTipoHorario
                            AND TO_CHAR(FECHA_INICIO, 'DD-MM-RRRR') = :feInicio
                            AND ESTADO = 'Activo'
                            ORDER BY FECHA_INICIO ASC";

        $objQuery->setParameter('idTipoHorario', $arrayParametros['idTipoHorario']); 
        $objQuery->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']); 
        $objQuery->setParameter('noEmple', $arrayParametros['noEmple']); 
        $objQuery->setParameter('feInicio', $arrayParametros['feInicio']); 
        $objRsm->addScalarResult('ID_HORARIO_EMPLEADO', 'idHorarioEmpleado', 'string');
        $objRsm->addScalarResult('FECHA_INICIO', 'fechaInicio', 'string');
        $objRsm->addScalarResult('FECHA_FIN', 'fechaFin', 'string');
        $objRsm->addScalarResult('HORA_INICIO', 'horaInicio', 'string');
        $objRsm->addScalarResult('HORA_FIN', 'horaFin', 'string');
        $objRsm->addScalarResult('CUADRILLA_ID', 'idCuadrilla', 'string');
        $objRsm->addScalarResult('NO_EMPLE', 'noEmple', 'string');
        $objRsm->addScalarResult('TIPO_HORARIO_ID', 'idTipoHorario', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();
    } 
    catch (\Exception $ex) 
    {
        error_log("Error al consultar planificacion para aplicativo horas extras: ". $ex->getMessage());
        
    }
        return $arrayResultado;
        
    }

    /**
     * Documentación para el método 'ejecutarCrearPlaniCuadrillaHE'.
     * 
     * Función que invoca al proceso de creacion de planificacion de empleados.
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0, 13-09-2021
     * @return JsonResponse
     * 
     */
    public function ejecutarCrearPlaniCuadrillaHE($arrayParametros)
    {

        $strMensaje       = str_repeat(' ', 10000);
        $strStatus   =  str_pad(' ', 1000);
        $objData = json_encode($arrayParametros);

        try
        {
            
            $strSql  = "BEGIN DB_HORAS_EXTRAS.HEKG_HORASEXTRAS_TRANSACCION.P_PLANIFICAR_HORARIO(:jsonData,"
                                                                                                ." :strStatus,"
                                                                                                ." :strMensaje); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('jsonData' , $objData);
            $objStmt->bindParam('strStatus' , $strStatus); 
            $objStmt->bindParam('strMensaje' , $strMensaje);
            $objStmt->execute();
			
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar el proceso de creacion de planificacion para aplicativo horas extras: ". $ex->getMessage());
            $strMensaje = 'Error' + $ex->getMessage();
        }
        
        return array('status' => $strStatus, 'mensaje'=> $strMensaje);
    }


    /**
     * Documentación para el método 'ejecutarEditarPlaniCuadrillaHE'.
     * 
     * Función que invoca al proceso de actualiza planificacion del empleado.
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0, 13-09-2021
     * @return JsonResponse
     * 
     */
    public function ejecutarEditarPlaniCuadrillaHE($arrayParametros)
    {
        
        $strMensaje        = str_repeat(' ', 10000);
        $strStatus         =  str_pad(' ', 1000);

        $objData = json_encode($arrayParametros);
        
        try
        {
            
            $strSql  = "BEGIN DB_HORAS_EXTRAS.HEKG_HORASEXTRAS_TRANSACCION.P_EDITAR_PLANIFICACION(:jsonData,"
                                                                                                ." :strStatus,"
                                                                                                ." :strMensaje); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('jsonData' , $objData);
            $objStmt->bindParam('strStatus' , $strStatus); 
            $objStmt->bindParam('strMensaje' , $strMensaje);
            $objStmt->execute();
			
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar el proceso de creacion de planificacion para aplicativo horas extras: ". $ex->getMessage());
            $strMensaje = 'Error' + $ex->getMessage();
        }

        return array('status' => $strStatus, 'mensaje'=> $strMensaje);
    }

    /**
     * Documentación para el método 'ejecutarEliminarPlaniCuadrillaHE'.
     * 
     * Función que invoca al proceso de elimina planificacion del empleado.
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0, 13-09-2021
     * @return JsonResponse
     * 
     */
    public function ejecutarEliminarPlaniCuadrillaHE($arrayParametros)
    {
        $strMensaje       = str_repeat(' ', 10000);
        $strStatus        =  str_pad(' ', 1000);

        $objData = json_encode($arrayParametros);

        try
        {
            
            $strSql  = "BEGIN DB_HORAS_EXTRAS.HEKG_HORASEXTRAS_TRANSACCION.P_ELIMINAR_PLANIFICACION(:jsonData,"
                                                                                                ." :strStatus,"
                                                                                                ." :strMensaje); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('jsonData' , $objData);
            $objStmt->bindParam('strStatus' , $strStatus); 
            $objStmt->bindParam('strMensaje' , $strMensaje);
            $objStmt->execute();
			
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar el proceso de creacion de planificacion para aplicativo horas extras: ". $ex->getMessage());
            $strMensaje = 'Error' + $ex->getMessage();
        }

        return array('status' => $strStatus, 'mensaje'=> $strMensaje);
    }

    /*
     * Método encargado obtener la planificacion del empleado aplicacion Horas Extras
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * 
     * @return Array $arrayResultado
     */
    public function getHorariosEmpleado($arrayParametros)
    {
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        try
        {

        $strSql         = "             SELECT  AHE.ID_HORARIO_EMPLEADO,
                                                AHE.NO_EMPLE,
                                                TO_CHAR(AHE.FECHA_INICIO,'DD-MM-YYYY') FECHA_INICIO,
                                                TO_CHAR(AHE.FECHA_FIN,'DD-MM-YYYY') FECHA_FIN,
                                                AHE.HORA_INICIO,
                                                AHE.HORA_FIN
                                        FROM DB_HORAS_EXTRAS.INFO_HORARIO_EMPLEADOS AHE
                                        JOIN DB_HORAS_EXTRAS.ADMI_TIPO_HORARIOS ATH
                                            ON ATH.ID_TIPO_HORARIO = AHE.TIPO_HORARIO_ID
                                            AND ATH.ID_TIPO_HORARIO = :idTipoHorario
                                        WHERE AHE.EMPRESA_COD = 10
                                            AND AHE.ESTADO = 'Activo'
                                            AND AHE.NO_EMPLE = :noEmple
                                            AND AHE.CUADRILLA_ID = :idCuadrilla
                                            AND ((AHE.FECHA_INICIO >=
                                                TO_DATE(:feInicio, 'DD-MM-YYYY') AND
                                                AHE.FECHA_INICIO <=
                                                TO_DATE(:feFin, 'DD-MM-YYYY')))";

        $objQuery->setParameter('idTipoHorario', $arrayParametros['idTipoHorario']); 
        $objQuery->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']); 
        $objQuery->setParameter('noEmple', $arrayParametros['noEmple']); 
        $objQuery->setParameter('feInicio', $arrayParametros['feInicio']); 
        $objRsm->addScalarResult('ID_HORARIO_EMPLEADO', 'idHorarioEmpleado', 'string');
        $objRsm->addScalarResult('FECHA_INICIO', 'fechaInicio', 'string');
        $objRsm->addScalarResult('FECHA_FIN', 'fechaFin', 'string');
        $objRsm->addScalarResult('HORA_INICIO', 'horaInicio', 'string');
        $objRsm->addScalarResult('HORA_FIN', 'horaFin', 'string');
        $objRsm->addScalarResult('NO_EMPLE', 'noEmple', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();
    } 
    catch (\Exception $ex) 
    {
        error_log("Error al consultar planificacion: ". $ex->getMessage());
        
    }

        return $arrayResultado;
        
    }
}
