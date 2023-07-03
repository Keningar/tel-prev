<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoTareaCaracteristicaRepository extends EntityRepository
{
    /**
     * Método encargado de obtener las tareas con la característica ATENDER_ANTES.
     *
     * costo 4
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 31-08-2018
     *
     * @param  $arrayParametros [
     *                              arrayIdComunicacion = lista de id de la tabla INFO_COMUNICACION,
     *                              strEstado           = Estado,
     *                              strCaracteristica   = Caracteristica
     *                          ]
     * @return Array
     */
    public function getTareasCaractAtenderAntes($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT IC.ID_COMUNICACION ID_COMUNICACION,
                        NVL((SELECT ITC.VALOR
                              FROM DB_COMERCIAL.ADMI_CARACTERISTICA     AC,
                                   DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC
                             WHERE AC.ID_CARACTERISTICA          =  ITC.CARACTERISTICA_ID
                               AND AC.DESCRIPCION_CARACTERISTICA = 'ATENDER_ANTES'
                               AND IC.ID_COMUNICACION            =  ITC.TAREA_ID
                               AND AC.ESTADO                     = :strEstado
                               AND ITC.ESTADO                    = :strEstado
                           ),'N') VALOR
                       FROM DB_COMUNICACION.INFO_COMUNICACION IC
                   WHERE IC.ID_COMUNICACION IN (:arrayIdComunicacion)";

            $objQuery->setParameter('arrayIdComunicacion', array_values($arrayParametros['arrayIdComunicacion']));
            $objQuery->setParameter("strEstado"          , $arrayParametros['strEstado']);
            $objQuery->setParameter("strCaracteristica"  , $arrayParametros['strCaracteristica']);

            $objRsm->addScalarResult('ID_COMUNICACION','idComunicacion','integer');
            $objRsm->addScalarResult('VALOR'          ,'valor'         ,'string');

            $objQuery->setSQL($strSql);

            $arrayRespuesta["status"]   = 'ok';
            $arrayRespuesta["resultado"] = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoTareaCaracteristicaRepository.getTareasCaractAtenderAntes -> ".$objException->getMessage());
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtener las tareas con que se finalizan en el móvil
     *
     * costo 248
     *
     * @author Jean Nazareno <jnazareno@telconet.ec>
     *
     * @return Array
     */
    public function getTareaFinalizaMovil()
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT AT.ID_TAREA, AT.NOMBRE_TAREA, AT.REQUIERE_FIBRA , RM.REQUIERE_MATERIAL, RRF.REQUIERE_RUTA_FIBRA 
            FROM
            (
              SELECT AT1.* 
              FROM DB_SOPORTE.ADMI_TAREA AT1
                WHERE AT1.VISUALIZAR_MOVIL = 'S'
                AND AT1.ESTADO = 'Activo'
                ORDER BY AT1.NOMBRE_TAREA
            ) AT
            LEFT JOIN 
            (
              SELECT ITC1.TAREA_ID, ITC1.VALOR AS REQUIERE_MATERIAL 
              FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC1 
              WHERE ITC1.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
              AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_MATERIAL'
            ) RM ON AT.ID_TAREA                 = RM.TAREA_ID
            LEFT JOIN 
            (
              SELECT ITC2.TAREA_ID, ITC2.VALOR AS REQUIERE_RUTA_FIBRA 
              FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC2 
              WHERE ITC2.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
              AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_RUTA_FIBRA'
            ) RRF ON AT.ID_TAREA                = RRF.TAREA_ID
            ";

            $objRsm->addScalarResult('ID_TAREA',            'idTarea',          'integer');
            $objRsm->addScalarResult('NOMBRE_TAREA',        'nombreTarea',      'string');
            $objRsm->addScalarResult('REQUIERE_FIBRA',      'requiereFibra',    'string');
            $objRsm->addScalarResult('REQUIERE_MATERIAL',   'requiereMaterial', 'string');
            $objRsm->addScalarResult('REQUIERE_RUTA_FIBRA', 'requiereRutaFibra','string');

            $objQuery->setSQL($strSql);

            $arrayRespuesta["status"]   = 'ok';
            $arrayRespuesta["registros"] = $objQuery->getArrayResult();

        }
        catch (\Exception $objException)
        {
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }

        return $arrayRespuesta;
    }

    /*
     * Función encargada de obtener las características de una tarea.
     *
     * costo 30
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 30-04-2019
     *
     * @param  Array $arrayParametros [
     *                                  arrayIdComunicacion = Lista de id de la tabla INFO_COMUNICACION,
     *                                  arrayCaracteristica = Lista de nombres de Caracteristicas.
     *                                  strEstado           = Estado.
     *                                ]
     * @return Array $arrayRespuesta
     */
    public function getTareasCaracteristicas($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT ICO.ID_COMUNICACION AS ID_COMUNICACION, ".
                             "ACA.DESCRIPCION_CARACTERISTICA AS DESCRIPCION_CARACTERISTICA, ".
                             "ITCA.VALOR AS VALOR ".
                           "FROM DB_COMERCIAL.ADMI_CARACTERISTICA     ACA, ".
                                "DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITCA, ".
                                "DB_COMUNICACION.INFO_COMUNICACION    ICO ".
                      "WHERE ACA.ID_CARACTERISTICA = ITCA.CARACTERISTICA_ID ".
                        "AND ITCA.TAREA_ID         = ICO.ID_COMUNICACION ".
                        "AND ACA.estado            = :strEstado ".
                        "AND ITCA.estado           = :strEstado ".
                        "AND ICO.estado            = :strEstado ".
                        "AND ACA.DESCRIPCION_CARACTERISTICA IN (:arrayCaracteristica) ".
                        "AND ICO.ID_COMUNICACION            IN (:arrayIdComunicacion) ";

            $objQuery->setParameter('arrayIdComunicacion' , array_values($arrayParametros['arrayIdComunicacion']));
            $objQuery->setParameter("arrayCaracteristica" , array_values($arrayParametros['arrayCaracteristica']));
            $objQuery->setParameter("strEstado"           , $arrayParametros['strEstado']);

            $objRsm->addScalarResult('ID_COMUNICACION'            , 'idComunicacion'            , 'integer');
            $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA' , 'descripcionCaracteristica' , 'string');
            $objRsm->addScalarResult('VALOR'                      , 'valor'                     , 'string');

            $objQuery->setSQL($strSql);

            $arrayRespuesta = array('status' => 'ok',
                                    'result' => $objQuery->getResult());
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status' => 'fail',
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }
    
    
    
    
    /**
     * Método que obtiene la última milla de una tarea de soporte
     *
     * costo 16
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     *
     * @return Array
     */
    public function getUltimaMillaSoporte($arrayParametros)
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "
                SELECT  
                    ATM.CODIGO_TIPO_MEDIO 
                FROM DB_SOPORTE.INFO_DETALLE_HIPOTESIS DH,
                    DB_SOPORTE.INFO_DETALLE ID,
                    DB_SOPORTE.INFO_PARTE_AFECTADA IPA,
                    DB_SOPORTE.ADMI_TIPO_MEDIO ATM ,
                    DB_SOPORTE.INFO_SERVICIO_TECNICO IST
                WHERE DH.CASO_ID = :casoId
                    AND DH.ID_DETALLE_HIPOTESIS = ID.DETALLE_HIPOTESIS_ID
                    AND IPA.DETALLE_ID = ID.ID_DETALLE
                    AND IPA.TIPO_AFECTADO='Servicio'
                    AND IST.SERVICIO_ID = IPA.AFECTADO_ID
                    AND IST.ULTIMA_MILLA_ID = ATM.ID_TIPO_MEDIO 
                    AND ATM.ESTADO = 'Activo' ";
            
            $objQuery->setParameter('casoId' ,                       $arrayParametros['casoId']);
            
            $objRsm->addScalarResult('CODIGO_TIPO_MEDIO',            'ultimaMillaSoporte',          'string');

            $objQuery->setSQL($strSql);

            $arrayRespuesta["status"]   = 'ok';
            $arrayRespuesta["result"]   = $objQuery->getArrayResult();

        }
        catch (\Exception $objException)
        {
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }

        return $arrayRespuesta;
    }
    
    /**
     * Método que obtiene la última milla de una tarea de instalación
     *
     * costo 4
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     *
     * @return Array
     */
    public function getUltimaMillaTecnico($intIdServicio)
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "
                SELECT ATM.ID_TIPO_MEDIO 
                            FROM 
                            DB_COMERCIAL.INFO_SERVICIO_TECNICO IFST, 
                            DB_COMERCIAL.ADMI_TIPO_MEDIO ATM
                            WHERE
                            IFST.ULTIMA_MILLA_ID = ID_TIPO_MEDIO
                            AND SERVICIO_ID = :idServicio  ";
            
            $objQuery->setParameter('idServicio' , $intIdServicio);
            
            $objRsm->addScalarResult('ID_TIPO_MEDIO', 'tipoMedioId','integer');

            $objQuery->setSQL($strSql);

            $arrayRespuesta["status"]   = 'ok';
            $arrayRespuesta["result"]   = $objQuery->getArrayResult();

        }
        catch (\Exception $objException)
        {
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }

        return $arrayRespuesta;
    }

    /**
     * Método que obtiene la hipotesis de cierre de caso Hal
     *
     * costo 3
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     *  @version 1.0 28-07-2021
     *
     * @return Array
     */
    public function getIdHipotesisCierreCasoHal($arrayParametros)
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = " 
                       SELECT S.VALOR
                         FROM DB_SOPORTE.INFO_TAREA_CARACTERISTICA S
                        WHERE S.TAREA_ID = :taraId
                          AND S.CARACTERISTICA_ID = :AdmiCaracteristica
                          AND S.ESTADO = 'Activo'";
            
            $objQuery->setParameter('taraId',$arrayParametros['idTarea']);
            $objQuery->setParameter('AdmiCaracteristica',$arrayParametros['idAmiCaracteristica']);
            
            $objRsm->addScalarResult('VALOR','idHipotesis','integer');

            $objQuery->setSQL($strSql);

            
            $intIdHipotesis = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $objException)
        {
            $intIdHipotesis = null;
        }

        return $intIdHipotesis;
    }

    /**
     * Método que obtiene codigo de trabajo de una tarea
     *
     * costo 3
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     *  @version 1.0 01-11-2022
     *
     * @return Array
     */
    public function getCodigoTrabajoTarea($arrayParametros)
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = " select S.VALOR 
                        from DB_SOPORTE.INFO_TAREA_CARACTERISTICA s 
                where S.CARACTERISTICA_ID = (select S.Id_Caracteristica 
                                         from DB_COMERCIAL.admi_caracteristica s 
                                        where S.Descripcion_Caracteristica='CODIGO_TRABAJO')  
                and S.DETALLE_ID = :idDetalle
                and S.ESTADO = 'Activo'";

            $objQuery->setParameter('idDetalle',$arrayParametros['idDetalle']);
            
            $objRsm->addScalarResult('VALOR','codigoTrabajo','string');

            $objQuery->setSQL($strSql);
            
            $intCodigoTrabajo = $objQuery->getSingleScalarResult();

        }
        catch (\Exception $objException)
        {
            $intCodigoTrabajo = null;
        }
        return $intCodigoTrabajo;
    }
    
}
