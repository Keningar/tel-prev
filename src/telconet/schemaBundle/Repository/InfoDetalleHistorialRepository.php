<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleHistorialRepository extends EntityRepository
{
    /**
     * Funcion que sirve para crear y ejecutar sql para obtener el historial
     * de un detalle (tarea)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 17-07-2015
     * @since 1.0
     * @param int $idDetalle
     * @param int $start
     * @param int $limit
     * @param string $order
     * @return array $resultado (registros, total)
     */
    public function getHistorialDetalle($idDetalle, $start, $limit, $order='ASC')
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        $qb ->select('e')
            ->from('schemaBundle:InfoDetalleHistorial','e');
        $qbC->select('count(e.id)')
            ->from('schemaBundle:InfoDetalleHistorial','e');
         
        if($idDetalle!="")
        {
            $qb ->where('e.detalleId = ?1');
            $qb ->setParameter(1, $idDetalle);
            $qb ->orderBy('e.feCreacion',$order);
            
            $qbC->where('e.detalleId = ?1');
            $qbC->setParameter(1, $idDetalle);
        }
        
        if($start!='')
        {
            $qb->setFirstResult($start);   
        }
            
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        
        return $resultado;
    }
    

    /**
     * Obtiene el ultimo estado de la tarea
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 11/11/2016
     * 
     * @param array arrayParametros["intIdDetalle"] int : idTarea
     * @return String $stringEstadoTarea : Estado de una tarea
     */
    public function getUltimoDetHist($arrayParametros)
    {
        $rsm   = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql   = "SELECT HISTORIAL.ESTADO ESTADO_TAREA
                  FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL HISTORIAL
                  WHERE HISTORIAL.ID_DETALLE_HISTORIAL = (SELECT MAX(DETALLE_HISTORIAL.ID_DETALLE_HISTORIAL)
                                                          FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL DETALLE_HISTORIAL
                                                          WHERE DETALLE_HISTORIAL.DETALLE_ID = :intIdDetalle)";

        $rsm->addScalarResult('ESTADO_TAREA','estadoTarea','string');
        $query->setParameter("intIdDetalle",$arrayParametros["intIdTarea"]);

        $query->setSQL($sql);
        $stringEstadoTarea = $query->getOneOrNullResult();
        return $stringEstadoTarea;
    }



     /**
     * Obtiene el ultimo usuario que creo el Estado de la Tarea
     * 
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.0 26/11/2020
     * 
     * @param array arrayParametros["intIdDetalle"] int : idTarea
     * @return String $stringUsuarioTarea : Usuario de la Tarea
     */
    public function getUltimoDetUsuario($arrayParametros)
    {
        $strRsm   = new ResultSetMappingBuilder($this->_em);
        $strQuery = $this->_em->createNativeQuery(null, $strRsm);

        $strSql   = "SELECT HISTORIAL.USR_CREACION USUARIO
                  FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL HISTORIAL
                  WHERE HISTORIAL.ID_DETALLE_HISTORIAL = (SELECT MAX(DETALLE_HISTORIAL.ID_DETALLE_HISTORIAL)
                                                          FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL DETALLE_HISTORIAL
                                                          WHERE DETALLE_HISTORIAL.DETALLE_ID = :intIdDetalle)";
        $strRsm->addScalarResult('USUARIO','usuario','string');
        $strQuery->setParameter("intIdDetalle",$arrayParametros["intIdTarea"]);
        $strQuery->setSQL($strSql);
        $strUsuarioTarea = $strQuery->getOneOrNullResult();
        return $strUsuarioTarea;
    }
    
    
    
     /**
     * Obtiene el Primer Asignado a la Tarea
     * @param string $strUsrCreacion 
     * @param int $intIdEmpresa
     * @param int $intFormaContacto
     * @return array de la persona a ser informada mediante correo
     *  
     * Costo  12
     *
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version   1.0 12/03/2019
     * 
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * Se realizo la modificación del query agregando un ROWNUM y modificacmos el tipo de dato ScalarResul de int a integer
     * @version   1.1 20/03/2019 
     * 
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version   1.2 21/03/2019 - Modificación del query para obtener la información de departamento y cantón del usuario que genero el evento
     *                             obteniendo de la tabla INFO_DETALLE del dato USR_CREACION
     */    
    public function getPrimerAsignado($strUsrCreacion, $intIdEmpresa, $intFormaContacto)
    {  
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSql   = " SELECT 
                            PERSONA.ID_PERSONA,
                            OFICINA.CANTON_ID,
                            IPER.DEPARTAMENTO_ID,
                            FC.VALOR AS CORREO
                          FROM 
                            DB_COMERCIAL.INFO_PERSONA             PERSONA,
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                            DB_COMERCIAL.INFO_EMPRESA_ROL         EMPRESA_ROL,
                            DB_COMERCIAL.ADMI_ROL                 ROL,
                            DB_COMERCIAL.ADMI_TIPO_ROL            TIPO_ROL,
                            DB_COMERCIAL.INFO_OFICINA_GRUPO       OFICINA,
                            DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FC
                          WHERE PERSONA.LOGIN               = :EMPLEADO
                          AND PERSONA.ID_PERSONA            = IPER.PERSONA_ID
                          AND IPER.ESTADO                   = :ESTADO
                          AND IPER.EMPRESA_ROL_ID           = EMPRESA_ROL.ID_EMPRESA_ROL
                          AND EMPRESA_ROL.ROL_ID            = ROL.ID_ROL
                          AND ROL.TIPO_ROL_ID               = TIPO_ROL.ID_TIPO_ROL
                          AND TIPO_ROL.DESCRIPCION_TIPO_ROL = :TIPO_ROL
                          AND EMPRESA_ROL.EMPRESA_COD       = :EMPRESA_CASO
                          AND PERSONA.ID_PERSONA            = FC.PERSONA_ID
                          AND FC.ESTADO                     = :ESTADO
                          AND FC.FORMA_CONTACTO_ID          = :IDFORMACONT
                          AND OFICINA.ID_OFICINA            = IPER.OFICINA_ID
                          AND ROWNUM                        = 1 ";

            if(!empty($strUsrCreacion) && $strUsrCreacion!=null)
            {
                $objQuery->setParameter("EMPLEADO",$strUsrCreacion);
            }
            
            if(!empty($intIdEmpresa) && $intIdEmpresa != null)
            {
                $objQuery->setParameter("EMPRESA_CASO",$intIdEmpresa);
            }
            
            if(!empty($intFormaContacto) && $intFormaContacto != null)
            {
                $objQuery->setParameter("IDFORMACONT",$intFormaContacto);
            }
            
            $objRsm->addScalarResult('PERSONA_ID'     ,'personaId'     ,'integer');
            $objRsm->addScalarResult('DEPARTAMENTO_ID','departamentoId','integer');
            $objRsm->addScalarResult('CANTON_ID'      ,'cantonId'      ,'integer');            
            $objRsm->addScalarResult('CORREO'         ,'correo'        ,'string');

            $objQuery->setParameter("ESTADO"  ,'Activo');
            $objQuery->setParameter("TIPO_ROL",'Empleado');
                  
            $objQuery->setSQL($strSql);
            
            $arrayData = $objQuery->getOneOrNullResult();
            
            if(!empty($arrayData) && $arrayData != null  )
            {
                return $arrayData; 
            }
            else
            {
                throw new \Exception("No se han enviado los parámetros adecuados para realizar la consulta");
            }
        }
        catch(\Exception $e)
        {
            error_log('getPrimerAsignado -> '.$e->getMessage());
        }
    }

    /**
     * Obtiene el ultimo registro del historial de la tarea
     * 
     * Costo 7
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 05/11/2020
     * 
     * @param array arrayParametros["detalleId"]
     * @return array arrayResultado
     */
    public function obtenerTareaActiva($arrayParametros)
    {
        try
        {
            $arrayResultado     = array();
            $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql   = "SELECT IDH1.PERSONA_EMPRESA_ROL_ID, (IP.NOMBRES || ' '||IP.APELLIDOS) AS NOMBRE_COMPLETO, IP.LOGIN
            FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH1, 
            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
            DB_COMERCIAL.INFO_PERSONA IP
            WHERE IDH1.ID_DETALLE_HISTORIAL = (
                SELECT MAX(IDH.ID_DETALLE_HISTORIAL) AS ID_DETALLE_HISTORIAL
                FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH
                WHERE IDH.DETALLE_ID = :detalleId
                AND IDH.ESTADO NOT IN ('Cancelada','Rechazada','Anulada','Finalizada','Replanificada')
                )
                AND IDH1.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                AND IPER.PERSONA_ID = IP.ID_PERSONA ";

            $objResultSetMap->addScalarResult('PERSONA_EMPRESA_ROL_ID','personaEmpresaRolId', 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_COMPLETO','nombreCompleto', 'string');
            $objResultSetMap->addScalarResult('LOGIN','login', 'string');

            $objNativeQuery->setParameter("detalleId", $arrayParametros["detalleId"]);

            $objNativeQuery->setSQL($strSql);

            $arrayResultado['status'] = 'OK';
            $arrayResultado['result'] = $objNativeQuery->getArrayResult();
        }
        catch (\Exception $objException)
        {
            $arrayResultado["status"]      = 'ERROR';
            $arrayResultado["descripcion"] = $objException->getMessage();
        }

        return $arrayResultado;
    }

    /**
     * Obtiene el historial de las tareas, por los diferentes departamentos, para json
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 15-01-2021
     * @param array $arrayParametros idDetalle
     * @return array $arrayResultado
     * 
     */
    public function generarDetalleHistoriaTarea($arrayParametros)
    {
        $arrayResultado = array();
        $arrayHistorial = $this->getHistorialTarea($arrayParametros);
        foreach ($arrayHistorial as $data)
        {
            $strEsSolucion =  ($data["esSolucion"] ? ($data["esSolucion"]=='S' ? "SI" : "NO") : "NO") ;
            $arrayResultado[] = array(
                'idDetalleHist'      => $data['idDetalleHist'],
                'motivoFinTarea'     => $data['motivoFinTarea'],
                'accion'             => $data['accion'],
                'nombreTarea'        => $data['nombreTarea'],
                'nombreDepartamento' => $data['nombreDepartamento'],
                'esSolucion'         => $strEsSolucion,
                'es_solucion_TN_det' => ($strEsSolucion =="SI" ? "1" : "0"),
                'detalleId'          => $data['detalleId']
            );
        }
        return $arrayResultado;
    }

    /**
     * Obtiene el historial de las tareas, por los diferentes departamentos
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 15-01-2021
     * @param array $arrayParametros idDetalle
     * @return array $arrayResultado
     * 
     */
    public function getHistorialTarea($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        try
        {
            $strSql = "SELECT IDH.ID_DETALLE_HISTORIAL,
                            IDH.MOTIVO_FIN_TAREA,
                            IDH.ACCION,
                            AT.NOMBRE_TAREA,
                            AD.NOMBRE_DEPARTAMENTO,
                            IDH.ES_SOLUCION,
                            IDH.DETALLE_ID
                        FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH,
                            DB_SOPORTE.ADMI_TAREA AT,
                            DB_GENERAL.ADMI_DEPARTAMENTO AD
                        WHERE IDH.TAREA_ID              = AT.ID_TAREA
                        AND IDH.DEPARTAMENTO_ORIGEN_ID = AD.ID_DEPARTAMENTO
                        AND IDH.DETALLE_ID              = :idDetalle
                        ORDER BY IDH.ID_DETALLE_HISTORIAL ASC";
            
            $objRsm->addScalarResult('ID_DETALLE_HISTORIAL','idDetalleHist','string');
            $objRsm->addScalarResult('MOTIVO_FIN_TAREA','motivoFinTarea','string');
            $objRsm->addScalarResult('ACCION','accion','string');
            $objRsm->addScalarResult('NOMBRE_TAREA','nombreTarea','string');
            $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO','nombreDepartamento','string');
            $objRsm->addScalarResult('ES_SOLUCION','esSolucion','string');
            $objRsm->addScalarResult('DETALLE_ID','detalleId','string');

            $objQuery->setParameter('idDetalle', $arrayParametros['idDetalle']);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $arrayResultado;

    }

    /**
     * Obtiene el historial de las tareas por casos. se agregaga id_detalle_historial
     * para validaciones 
     * 
     * @author Modificado por Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 12-10-2021
     * @param array $arrayParametros idCaso
     * @return array $arrayResultado
     * 
     */
    public function getHistorialPorCaso($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        try
        {
            $strSql = "SELECT IDE.ID_DETALLE,
                            ATA.NOMBRE_TAREA,
                            NVL(IDHIS.ES_SOLUCION,'N') ES_SOLUCION,
                            IDHIS.ID_DETALLE_HISTORIAL
                        FROM DB_SOPORTE.INFO_CASO IC,
                            DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDH,
                            DB_SOPORTE.INFO_DETALLE IDE,
                            DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHIS,
                            DB_SOPORTE.ADMI_TAREA ATA
                        WHERE IDH.CASO_ID           = IC.ID_CASO
                        AND IDH.ID_DETALLE_HIPOTESIS= IDE.DETALLE_HIPOTESIS_ID
                        AND IDHIS.DETALLE_ID        = IDE.ID_DETALLE
                        AND ATA.ID_TAREA            = IDHIS.TAREA_ID
                        AND IDE.TAREA_ID           IS NOT NULL
                        AND IDHIS.TAREA_ID         IS NOT NULL
                        AND IC.ID_CASO              = :idCaso
                        ORDER BY IDHIS.ID_DETALLE_HISTORIAL ASC";
            
            $objRsm->addScalarResult('ID_DETALLE','idDetalle','string');
            $objRsm->addScalarResult('NOMBRE_TAREA','nombreTarea','string');
            $objRsm->addScalarResult('ES_SOLUCION','esSolucion','string');
            $objRsm->addScalarResult('ID_DETALLE_HISTORIAL','idDetalleHist','string');

            $objQuery->setParameter('idCaso', $arrayParametros['idCaso']);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $arrayResultado;
    }

    /**
     * Obtiene los motivos de reasignacion de las tareas 
     * se obtiene id tarea y nombre de tarea con la cual es reasignada
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 01-10-2021
     * @param array $arrayParametros idDetalle
     * @return array $arrayResultado
     * 
     */
    public function getMotivoPorTarea($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        try
        {
            $strSql = "SELECT S.TAREA_ID,ATA.NOMBRE_TAREA,S.MOTIVO_ID,S.MOTIVO_FIN_TAREA 
                         FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL S,
                              DB_SOPORTE.ADMI_TAREA ata
                        WHERE ATA.ID_TAREA = S.TAREA_ID 
                          AND S.DETALLE_ID = :idDetalle 
                          AND S.ACCION = 'Reasignada'
                          AND ROWNUM = 1
                        ORDER BY S.Id_Detalle_Historial DESC";
            
            $objRsm->addScalarResult('TAREA_ID','idTarea','integer');
            $objRsm->addScalarResult('NOMBRE_TAREA','nombreTarea','string');
            $objRsm->addScalarResult('MOTIVO_ID','motivoId','integer');
            $objRsm->addScalarResult('MOTIVO_FIN_TAREA','nombreMotivo','string');

            $objQuery->setParameter('idDetalle', $arrayParametros['idDetalle']);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            throw($e);
        }

        return $arrayResultado;
    }    

}
