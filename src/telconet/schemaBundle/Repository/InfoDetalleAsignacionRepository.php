<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleAsignacionRepository extends EntityRepository
{
    
     /**
     * getUltimaAsignacionTarea
     * 
     * Esta funcion retorna la ultima fecha de asignacion de una detalle id
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-10-2015 
     * 
     * @param integer  $detalleId
     * 
     * @return array $strDatos
     * 
     */
    public function getUltimaAsignacionTarea($detalleId) 
    {
        $strQuery   = $this->_em->createQuery();        
        $strSelect  = " SELECT                     
                            infoDetalleAsignacion.tipoAsignado as asignado,infoDetalleAsignacion.asignadoId as asignadoId
                       FROM 
                            schemaBundle:InfoDetalleAsignacion infoDetalleAsignacion 
                       WHERE 
                            infoDetalleAsignacion.detalleId = :varDetalleId
                       AND infoDetalleAsignacion.feCreacion = (
                                SELECT max(InfoDetalleAsignacion.feCreacion)
                                FROM schemaBundle:InfoDetalleAsignacion InfoDetalleAsignacion
                                WHERE InfoDetalleAsignacion.detalleId = :varDetalleId) ";
        
                                
        $strQuery->setParameter("varDetalleId", $detalleId);      
        $strQuery->setDQL($strSelect);  
  
        $arrayDatos = $strQuery->getResult();        
        
        return $arrayDatos;
    }  
    

     /**
     * getNumeroTareasAbiertas
     *
     * Método que obtiene el numero de tareas que estan abiertas que estan asignadas a un empleado
     *
     * @param array $arrayParametros[ 'intPersonaEmpresaRolId' => persona empresa rol de la persona en session,
     *                                'strTipoConsulta'        => Tipo de Consulta: CantidadTareasAbiertas,TareasEjecutando
     *                                'arrayEstados'           => estados utilizados en el query
     *                                'strEstado'              => estado de la persona_empresa_rol
     *                              ]
     *
     * @return array $arrayRespuesta[ 'intCantidadTareas' => cantidad de tareas abiertas
     *                                'arrayResultado'    => numero y nombre de las tareas
     *                              ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-10-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 15-12-2016 - Se realiza ajustes para que no sea considerado el estado Anulada en el contador de
     *                           tareas pendientes
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-04-2017 Se realizan ajustes para reutilizar la función y poder determinar si un empleado tiene una tarea en estado Aceptada
     *
     */
    public function getNumeroTareasAbiertas($arrayParametros)
    {
        $objQuery       = $this->_em->createQuery();
        $objQuery2      = $this->_em->createQuery();
        $arrayRespuesta = array();

        $strWhereNotIn  = " AND idh.estado NOT IN (:paramEstadosNoConsiderar)";
        $strWhereIn     = " AND idh.estado IN (:paramEstadosNoConsiderar)";
        $strSelectNotIn = " SELECT COUNT(ida.detalleId) ";
        
        $strSelectIn    = " SELECT 
                                (SELECT MIN(InfoComunicacion.id) 
                                FROM schemaBundle:InfoComunicacion InfoComunicacion 
                                WHERE InfoComunicacion.detalleId = ida.detalleId) numeroTarea  ";
        
        $strFrom = " FROM
                        schemaBundle:InfoDetalleAsignacion ida,
                        schemaBundle:InfoDetalle id,
                        schemaBundle:InfoDetalleHistorial idh
                        WHERE ida.detalleId = id.id
                        AND id.id = idh.detalleId
                        AND idh.id = (SELECT MAX(idh1.id) FROM schemaBundle:InfoDetalleHistorial idh1
                                                         WHERE idh1.detalleId = ida.detalleId )
                        AND ida.id = (SELECT MAX(ida1.id) FROM schemaBundle:InfoDetalleAsignacion ida1
                                                         WHERE ida1.detalleId = ida.detalleId ) ";

        $strFromTareasAbiertas = " AND ida.personaEmpresaRolId = :paramEmpresaRolId ";

        $strWhereTareasEjecutadas = " AND ida.personaEmpresaRolId IN ( SELECT infopersonaempresarol2.id
                                        FROM schemaBundle:InfoPersonaEmpresaRol infopersonaempresarol2
                                        WHERE infopersonaempresarol2.personaId =
                                          (SELECT (infopersonaempresarol.personaId)
                                          FROM schemaBundle:InfoPersonaEmpresaRol infopersonaempresarol
                                          WHERE infopersonaempresarol.id = :paramEmpresaRolId
                                          )
                                        AND infopersonaempresarol2.estado = :paramEstado ) ";

        if($arrayParametros["strTipoConsulta"] == "CantidadTareasAbiertas")
        {
            $strSql = $strSelectNotIn . $strFrom . $strFromTareasAbiertas . $strWhereNotIn;
        }
        if($arrayParametros["strTipoConsulta"] == "TareasEjecutando")
        {
            $strSql = $strSelectNotIn. $strFrom . $strWhereTareasEjecutadas. $strWhereIn;

            $objQuery->setParameter('paramEstado',$arrayParametros["strEstado"]);
            $objQuery2->setParameter('paramEmpresaRolId',$arrayParametros["intPersonaEmpresaRolId"]);
            $objQuery2->setParameter('paramEstadosNoConsiderar',$arrayParametros["arrayEstados"]);
            $objQuery2->setParameter('paramEstado',$arrayParametros["strEstado"]);
            $strSql2 = $strSelectIn. $strFrom . $strWhereTareasEjecutadas . $strWhereIn;
        }

        $objQuery->setParameter('paramEmpresaRolId',$arrayParametros["intPersonaEmpresaRolId"]);
        $objQuery->setParameter('paramEstadosNoConsiderar',$arrayParametros["arrayEstados"]);

        $objQuery->setDQL($strSql);

        if($arrayParametros["strTipoConsulta"] == "CantidadTareasAbiertas")
        {
            $arrayRespuesta["intCantidadTareasAbiertas"] = $objQuery->getSingleScalarResult();
        }
        if($arrayParametros["strTipoConsulta"] == "TareasEjecutando")
        {
            $arrayRespuesta["intCantidadTareasEjecutando"] = $objQuery->getSingleScalarResult();
            
            $objQuery2->setDQL($strSql2);            
            $arrayRespuesta["arrayTareasEjecutando"] = $objQuery2->getResult();            
        }       

        return $arrayRespuesta;
    }

     /**
     * Costo: 1
     * getDetalleTareas
     *
     * Método que obtiene la cantidad de tareas pendientes  por criterios
     *
     * @param array $arrayParametros[ 'strTieneCredencial'   => bandera que indica si tiene la credencial para el indicador de tareas nacional
     *                                'intDepartamentoId'    => id de departamento
     *                                'intOficinaId'         => id de oficina
     *                                'intPersonaEmpresaRol' => id de la persona empresa rol
     *                                'strTipoConsulta'      => por 'departamento' , 'persona'
     *                              ]
     *
     * @return array $arrayRespuesta[ 'intCantidadTareas' => cantidad de tareas pendientes ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-01-2018
     *
     * @author Modificado - Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-05-2018 - Se realizan ajustes para visualizar las tareas a nivel nacional cuando se el usuario
     *                           tenga la credencial: indicadorTareasNacional
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 04-12-2018 -  Se agrega la fecha por defecto para obtener un mejor tiempo de respuesta.
     *
     * @author modificado Néstor Naula <nnaulal@telconet.ec>
     * @version 1.3 04-12-2018 -  Se agrega filtro para que no cuente las tareas de ECUCERT.
     * @since 1.2
     * 
     * @author modificado: Néstor Naula <nnaulal@telconet.ec>
     * @version 1.4 13-09-2019 - Se cambia la forma de filtro de las tareas ECUCERT de NOT IN a EXISTS para optimizar la consulta.
     * @since 1.3
     * 
     * @author modificado: Néstor Naula <nnaulal@telconet.ec>
     * @version 1.5 23-04-2020 - Se quita el filtro de las tareas ECUCERT con el NOT EXISTS.
     * @since 1.4
     * @author modificado: José Guamán <nnaulal@telconet.ec>
     * @version 1.5 16-06-2023 - Se cambia la forma de crear el query con la misma funcionalidad que tiene actualmente, 
     * se agrega select de INFO_TAREA para filtro de persona.
     * @since 1.4
    * 
    */
    public function getDetalleTareas($arrayParametros)
    {
        $strRsm = new ResultSetMappingBuilder($this->_em);
        $strQuery = $this->_em->createNativeQuery(null, $strRsm);

        if($arrayParametros["strTipoConsulta"] == "persona")
        {
            $strSelect = "SELECT COUNT(a.ID_INFO_TAREA) as CANTIDAD FROM DB_SOPORTE.INFO_TAREA a ";
            $strWhere = " WHERE a.PERSONA_EMPRESA_ROL_ID = ".$arrayParametros["intPersonaEmpresaRol"];            
        }else
        {
            $strSelect = "SELECT COUNT(a.DETALLE_ID) as CANTIDAD  FROM DB_SOPORTE.INFO_DETALLE_TAREAS a ";

            if($arrayParametros["strTieneCredencial"] == "S")
            {
                $strWhere = " WHERE a.DEPARTAMENTO_ID = :intDepartamentoId";
                $strQuery->setParameter("intDepartamentoId", $arrayParametros["intDepartamentoId"]);
            }
            else
            {
                $strWhere = " WHERE a.DEPARTAMENTO_ID = ".$arrayParametros["intDepartamentoId"]; 
                $strWhere .= " AND a.OFICINA_ID = :intOficinaId";
                $strQuery->setParameter("intOficinaId", $arrayParametros["intOficinaId"]);
                $strQuery->setParameter("intDepartamentoId", $arrayParametros["intDepartamentoId"]);
            }
        }

        if (isset($arrayParametros['strFechaDefecto']) && $arrayParametros['strFechaDefecto'] !== '')
        {
            $strFechaDefault = date("Y/m/d", strtotime($arrayParametros['strFechaDefecto']));
            $strWhere .= " AND a.FE_CREACION >= :strFechaDefault";
            $strQuery->setParameter("strFechaDefault", $strFechaDefault);
        }

        $strWhere .= " AND a.estado NOT IN (:paramEstadoHistorial) ";
        $strRsm->addScalarResult('CANTIDAD','intCantidadTareas','string');
        $strQuery->setParameter("paramEstadoHistorial",         array('Finalizada','Cancelada','Rechazada','Anulada'));

        $strSql = $strSelect . $strWhere;
        
        $strQuery->setSQL($strSql);
        $arrayData = $strQuery->getResult();

        if (!empty($arrayData)) 
        {
            $arrayRespuesta["intCantidadTareas"] = $arrayData[0]["intCantidadTareas"];
        }else
        {
            $arrayRespuesta["intCantidadTareas"] = 0;
        }

        return $arrayRespuesta;
    }

     /*
     * getResultadoTareaAperturada
     *
     * Esta funcion retorna la tarea aperturada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-02-2016
     *
     * @param  int    $intIdComunicacion
     * @return array  $strDatos
     *
     */
    public function getResultadoTareaAperturada($intIdComunicacion)
    {
        $strQuery       = $this->_em->createQuery();
        $strDatos       = array();
        $sql = " SELECT infoDetalleHistorial.feCreacion as feCreacion
                    FROM schemaBundle:InfoDetalleHistorial infoDetalleHistorial
                    WHERE infoDetalleHistorial.detalleId = (select infoComunicacion.detalleId from schemaBundle:InfoComunicacion infoComunicacion
                    where infoComunicacion.id = :varTarea)
                    AND infoDetalleHistorial.feCreacion  =
                      (SELECT MIN( infoDetalleHistoria.feCreacion )
                      FROM schemaBundle:InfoDetalleHistorial infoDetalleHistoria
                      WHERE infoDetalleHistoria.detalleId = (select infoComunicacio.detalleId from schemaBundle:InfoComunicacion infoComunicacio
                      where infoComunicacio.id = :varTarea)) ";

        $strQuery->setParameter("varTarea", $intIdComunicacion);

        $strQuery->setDQL($sql);
        $strDatos = $strQuery->getResult();
        return $strDatos[0];

    }

     /**
     * getTareaFueIniciada
     *
     * Esta funcion sirve para saber si la tarea ha sido iniciada
     * Costo = 6
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-01-2017
     *
     * @param  array    $arrayParametros [ 'intDetalleId'   => id detalle de la tarea,
     *                                     'strObservacion' => observacion a buscar ]
     *
     * @return string  $strTareaFueIniciada
     *
     */
    public function getTareaFueIniciada($arrayParametros)
    {
        $objQuery            = $this->_em->createQuery();
        $intTareaIniciada    = 0;
        $strTareaFueIniciada = "N";

        $strSql = " SELECT COUNT ( infoTareaSeguimiento.id )
                        FROM schemaBundle:InfoTareaSeguimiento infoTareaSeguimiento
                        WHERE infoTareaSeguimiento.detalleId = :paramDetalleId
                        AND infoTareaSeguimiento.observacion LIKE :paramObservacion ";

        $objQuery->setParameter("paramDetalleId", $arrayParametros["intDetalleId"]);
        $objQuery->setParameter("paramObservacion", '%'.$arrayParametros["strObservacion"].'%');

        $objQuery->setDQL($strSql);

        $intTareaIniciada = $objQuery->getSingleScalarResult();

        if($intTareaIniciada > 0)
        {
            $strTareaFueIniciada = "S";
        }

        return $strTareaFueIniciada;
    }

     /*
     * getResultadoTareaFinalizada
     *
     * Esta funcion retorna la tarea finalizada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-02-2016
     *
     * @param  int    $intIdComunicacion
     * @return array  $strDatos
     *
     */
    public function getResultadoTareaFinalizada($intIdComunicacion)
    {
        $strQuery       = $this->_em->createQuery();
        $strDatos       = array();
        $sql = " SELECT infoDetalleHistorial.feCreacion as feCreacion
                    FROM schemaBundle:InfoDetalleHistorial infoDetalleHistorial
                    WHERE infoDetalleHistorial.detalleId = (select infoComunicacion.detalleId from schemaBundle:InfoComunicacion infoComunicacion
                    where infoComunicacion.id = :varTarea)
                    AND ( infoDetalleHistorial.estado = :varEstado OR infoDetalleHistorial.estado = :varEstado2 )
                    AND infoDetalleHistorial.feCreacion =
                      (SELECT MAX( infoDetalleHistoria.feCreacion )
                      FROM schemaBundle:InfoDetalleHistorial infoDetalleHistoria
                      WHERE infoDetalleHistoria.detalleId = (select infoComunicacio.detalleId from schemaBundle:InfoComunicacion infoComunicacio
                      where infoComunicacio.id = :varTarea)) ";

        $strQuery->setParameter("varEstado", "Finalizada");
        $strQuery->setParameter("varEstado2", "Cancelada");
        $strQuery->setParameter("varTarea", $intIdComunicacion);

        $strQuery->setDQL($sql);
        $strDatos = $strQuery->getResult();
        return $strDatos[0];

    }

    /**
     * getServiciosPorActivador
     * Obtiene el servicio según el empleado activador.
     * Costo=656
     * 
     * @author John Vera <javera@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 09-02-2015
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.1 16-02-2016 - Se corrige el query de consulta para Ruta Georreferencial.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 14-11-2016 - Se obtiene la observación con la información del Ingeniero IPCCL2.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 18-11-2016 - Se obtiene el id_detalle de una tarea.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 17-01-2017 - Se agrega en la consulta las respectivas relaciones para obtener el número de la tarea en la consulta.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.5 22-05-2017 - Se añade en la consulta el campo tieneMaterial para consultar si se ha ingresado anteriormente
     *                           en una instación los materiales.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.6 22-09-2017 - Se solicita ordenar por fe_solicitada las instalaciones.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.7 25-05-2018 - Se valida si la consulta, switcheando cuando es operativo y cuando es tecnico, se adiciona una variable como
     *                           parametro a la función.
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.8 20-05-2018 - Se agrego estado Cencel y Cancelado para visializar el servicio en el detalle de la aplicación, se considera
     *                           las solicitudes de retiro de equipo.
     *
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.9 22-03-2021 - Se agregan estados PreFactibilidad, FactibilidadEnProceso, Factible 
     *                           para verificar el estado del servico, utilizado en el detalle de la tarea en la
     *                           aplicación móvil TM Operaciones.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.0 10-01-2022 - Se modifíca la forma de obtener las solicitudes y se agrega la "SOLICITUD MIGRACION SWITCH POE".
     *  
     * @param string $loginActivador
     * @param string $idServicio
     * 
     * @return array $servicios (datosCliente, login, idPunto, idProducto, idPlan, perfil, idServicio, estadoServicio, tipoOrden,
     *                           nombrePlan, direccion, descripcionPunto, longitud, latitud, nombreProducto, idProductoTecnico, 
     *                           nombreProductoTecnico, ultimaMilla, idElemento, nombreOlt, idInterfaceElemento, interfaceOlt, 
     *                           idCaja, caja, idSplitter, splitter, idInterfaceSplitter, interfaceSplitter, ipElemento, 
     *                           modeloElemento, tieneEncuesta, tieneActa, solicitudMigracion, tieneMaterial)
     */    
    public function getServiciosPorActivador($loginActivador, $idServicio, $boolMovilTecnico)
    {
        
        $arraySolicitudes = array('SOLICITUD PLANIFICACION', 
                                    'SOLICITUD RETIRO EQUIPO', 
                                    'SOLICITUD MIGRACION', 
                                    'SOLICITUD MIGRACION SWITCH POE');

        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "SELECT
                TO_CHAR(D.FE_SOLICITADA,'YYYY-MM-DD HH24:MI') FE_SOLICITADA,
                (SELECT PU.LOGIN||';'||NVL(PE.NOMBRES,PE.RAZON_SOCIAL)||' '||PE.APELLIDOS
                FROM INFO_PERSONA_EMPRESA_ROL PER,
                  INFO_PERSONA PE
                WHERE PER.PERSONA_ID   = PE.ID_PERSONA
                AND PER.ID_PERSONA_ROL = PU.PERSONA_EMPRESA_ROL_ID) DATOS_CLIENTE,
                (SELECT IP FROM INFO_IP WHERE ELEMENTO_ID = ST.ELEMENTO_ID AND ESTADO = :estadoIp) IP_ELEMENTO,
                (SELECT ME.NOMBRE_MODELO_ELEMENTO
                  FROM INFO_ELEMENTO E,
                    ADMI_MODELO_ELEMENTO ME
                  WHERE E.ID_ELEMENTO       = ST.ELEMENTO_ID 
                  AND ME.ID_MODELO_ELEMENTO = E.MODELO_ELEMENTO_ID ) MODELO_ELEMENTO,
                  PU.DIRECCION,
                  PU.DESCRIPCION_PUNTO,
                  D.ID_DETALLE,
                  IC.ID_COMUNICACION,
                  PU.LONGITUD,
                  PU.LATITUD,
                  P.LOGIN,  
                  PU.ID_PUNTO,
                  S.PRODUCTO_ID,
                  PROD.ID_PRODUCTO ID_PRODUCTO_TECNICO,                      
                  PROD.NOMBRE_TECNICO NOMBRE_PRODUCTO_TECNICO,
                  S.PLAN_ID,
                  (SELECT SERVICIO_PROD_CARACT.VALOR
                    FROM ADMI_CARACTERISTICA CARACTERISTICA,
                    ADMI_PRODUCTO_CARACTERISTICA PRODUCTO_CARACTERISTICA,
                    INFO_SERVICIO_PROD_CARACT SERVICIO_PROD_CARACT
                    WHERE SERVICIO_PROD_CARACT.SERVICIO_ID = S.ID_SERVICIO
                    AND SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
                    AND PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID = CARACTERISTICA.ID_CARACTERISTICA
                    AND CARACTERISTICA.DESCRIPCION_CARACTERISTICA = :nombreCaracteristica
                    and SERVICIO_PROD_CARACT.ESTADO = :estadoServProdCaract
                    and ROWNUM < 2) PERFIL,
                  S.ID_SERVICIO,
                  S.ESTADO,
                  DECODE (S.TIPO_ORDEN, 'N', 'NUEVO', 'T', 'TRASLADO', 'R', 'REUBICACION') TIPO_ORDEN,
                  (SELECT PC.NOMBRE_PLAN||';'||PC.EMPRESA_COD FROM INFO_PLAN_CAB PC WHERE PC.ID_PLAN = S.PLAN_ID ) NOMBRE_PLAN,
                  (SELECT AP.DESCRIPCION_PRODUCTO||';'||AP.EMPRESA_COD 
                  FROM ADMI_PRODUCTO AP
                  WHERE S.PRODUCTO_ID = AP.ID_PRODUCTO) NOMBRE_PRODUCTO,
                  (SELECT TM.NOMBRE_TIPO_MEDIO
                  FROM INFO_SERVICIO_TECNICO ST1,
                    ADMI_TIPO_MEDIO TM
                  WHERE TM.ID_TIPO_MEDIO = ST1.ULTIMA_MILLA_ID
                  AND ST1.SERVICIO_ID    = S.ID_SERVICIO ) ULTIMA_MILLA,
                  ST.ELEMENTO_ID,
                  (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_ID) NOMBRE_OLT,
                  ST.INTERFACE_ELEMENTO_ID,
                  (select IE.NOMBRE_INTERFACE_ELEMENTO  from INFO_INTERFACE_ELEMENTO IE where IE.ID_INTERFACE_ELEMENTO = ST.INTERFACE_ELEMENTO_ID) 
                  INTERFACE_OLT,
                  ST.ELEMENTO_CONTENEDOR_ID,
                  (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_CONTENEDOR_ID) CAJA,
                  (SELECT UBI.LATITUD_UBICACION  
                       FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                      WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                        AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) CAJA_LATITUD,
                  (SELECT UBI.LONGITUD_UBICACION 
                       FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                      WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                        AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) CAJA_LONGITUD,

                  (SELECT UBI.PARROQUIA_ID  
                       FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                      WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                        AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) ID_PARROQUIA,

                  ST.ELEMENTO_CONECTOR_ID,
                  (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_CONECTOR_ID) SPLITTER,
                  ST.INTERFACE_ELEMENTO_CONECTOR_ID,
                  (select IE.NOMBRE_INTERFACE_ELEMENTO  from INFO_INTERFACE_ELEMENTO IE 
                  where IE.ID_INTERFACE_ELEMENTO = ST.INTERFACE_ELEMENTO_CONECTOR_ID) INTERFACE_SPLITTER,
                  TECNK_SERVICIOS.GET_ACTA_ENCUESTA(:acta, :modulo, S.ID_SERVICIO,D.ID_DETALLE) TIENE_ACTA,
                  TECNK_SERVICIOS.GET_ACTA_ENCUESTA(:encuesta, :modulo, S.ID_SERVICIO,D.ID_DETALLE) TIENE_ENCUESTA,
                  TECNK_SERVICIOS.GET_OBSERVACION_DETALLE(D.ID_DETALLE) OBSERVACION_DETALLE,
                  TECNK_SERVICIOS.GET_ID_DET_SOL_PARAM_EST(S.ID_SERVICIO, :tipoSolicitudMigra, :nombreParamMigracion, :moduloParamMigracion) ID_SOLICITUD_MIGRACION,
                  TECNK_SERVICIOS.GET_ESTADO_SOLICITUD_PLANIFICA(S.ID_SERVICIO, :tipoSolicitudMigra) ESTADO_SOLICITUD_MIGRACION,
                  TECNK_SERVICIOS.GET_ESTADO_SOLICITUD_PLANIFICA(S.ID_SERVICIO, :tipoSolicitudPlan) ESTADO_SOLICITUD_PLANIFICACION,
                  (SELECT IPC.ID_PUNTO_CARACTERISTICA 
                    FROM INFO_PUNTO_CARACTERISTICA IPC, ADMI_CARACTERISTICA AC
                    WHERE IPC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA 
                      AND AC.DESCRIPCION_CARACTERISTICA = :strCaracteristicaRuta 
                      AND IPC.ESTADO = :strEstadoPuntoCarac 
                      AND IPC.PUNTO_ID = PU.ID_PUNTO) TIENE_RUTA,
                  (SELECT COUNT(*)
                    FROM DB_SOPORTE.INFO_DETALLE_MATERIAL
                    WHERE DETALLE_ID = D.ID_DETALLE) TIENE_MATERIAL
                FROM INFO_DETALLE_ASIGNACION DA,
                  INFO_DETALLE D,
                  DB_COMUNICACION.INFO_COMUNICACION IC,
                  INFO_DETALLE_SOLICITUD DS,
                  INFO_PERSONA P,
                  INFO_SERVICIO S
                    LEFT JOIN DB_COMERCIAL.INFO_PLAN_CAB PLANC
                    ON PLANC.ID_PLAN = S.PLAN_ID
                    LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET PLAND
                    ON PLAND.PLAN_ID = PLANC.ID_PLAN
                    LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD
                    ON PROD.ID_PRODUCTO     = PLAND.PRODUCTO_ID
                    AND PROD.ES_PREFERENCIA = :productoPreferencia,
                  INFO_PUNTO PU,
                  INFO_SERVICIO_TECNICO ST";
        
        //parte where del sql
        $sqlWhere = "WHERE DA.DETALLE_ID        = D.ID_DETALLE
                AND ST.SERVICIO_ID = S.ID_SERVICIO
                AND D.DETALLE_SOLICITUD_ID = DS.ID_DETALLE_SOLICITUD
                AND DA.TIPO_ASIGNADO      IN (:tipoAsignado)
                AND DA.REF_ASIGNADO_ID     = P.ID_PERSONA
                AND DS.TIPO_SOLICITUD_ID  IN (SELECT ATS.ID_TIPO_SOLICITUD 
                                                FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD ATS 
                                                WHERE ATS.DESCRIPCION_SOLICITUD IN (:tipoSolicitud))
                AND DS.ESTADO             IN (:estadoSolicitud)
                AND S.ESTADO              IN (:estadoServicio)
                AND S.ID_SERVICIO          = DS.SERVICIO_ID
                AND S.PUNTO_ID             = PU.ID_PUNTO
                AND P.LOGIN                = :login                
                AND IC.DETALLE_ID          = D.ID_DETALLE 
                AND IC.ID_COMUNICACION     = (SELECT MIN(icMin.ID_COMUNICACION) 
                                              FROM DB_COMUNICACION.INFO_COMUNICACION icMin
                                              WHERE icMin.DETALLE_ID = IC.DETALLE_ID) ";
        if($boolMovilTecnico)
        {
           $sqlWhere .= " AND TRUNC(D.FE_SOLICITADA) = TRUNC(SYSDATE) ";
        }
        $sql = $sql . " " . $sqlWhere;
        
        if($idServicio != 0)
        {
            $sql = $sql . " AND S.ID_SERVICIO = :idServicio";
        }
         
        //parte group by del sql
        $sqlGroup = "GROUP BY S.ID_SERVICIO,
              PU.ID_PUNTO,
              S.PRODUCTO_ID,
              S.PLAN_ID,
              PROD.ID_PRODUCTO,                      
              PROD.NOMBRE_TECNICO,                      
              PROD.DESCRIPCION_PRODUCTO,
              PU.LOGIN,
              P.LOGIN,
              PU.PERSONA_EMPRESA_ROL_ID,
              ST.ELEMENTO_ID,
              ST.INTERFACE_ELEMENTO_ID,
              ST.ELEMENTO_CONTENEDOR_ID,
              ST.ELEMENTO_CONECTOR_ID,
              ST.INTERFACE_ELEMENTO_CONECTOR_ID,
              PU.DIRECCION,
              PU.DESCRIPCION_PUNTO,
              PU.LONGITUD,
              PU.LATITUD,
              S.ESTADO,
              S.TIPO_ORDEN,
              D.ID_DETALLE,
              IC.ID_COMUNICACION,
              D.FE_SOLICITADA
              ORDER BY D.FE_SOLICITADA";
        $sql = $sql . " " . $sqlGroup;

        $rsm->addScalarResult('DATOS_CLIENTE',                  'datosCliente',                 'string');
        $rsm->addScalarResult('LOGIN',                          'login',                        'string');
        $rsm->addScalarResult('ID_PUNTO',                       'idPunto',                      'integer');
        $rsm->addScalarResult('PRODUCTO_ID',                    'idProducto',                   'integer');
        $rsm->addScalarResult('PLAN_ID',                        'idPlan',                       'integer');
        $rsm->addScalarResult('PERFIL',                         'perfil',                       'string');
        $rsm->addScalarResult('ID_SERVICIO',                    'idServicio',                   'integer');
        $rsm->addScalarResult('ESTADO',                         'estadoServicio',               'string');
        $rsm->addScalarResult('TIPO_ORDEN',                     'tipoOrden',                    'string');
        $rsm->addScalarResult('NOMBRE_PLAN',                    'nombrePlan',                   'string');        
        $rsm->addScalarResult('DIRECCION',                      'direccion',                    'string');
        $rsm->addScalarResult('DESCRIPCION_PUNTO',              'descripcionPunto',             'string');
        $rsm->addScalarResult('ID_DETALLE',                     'idDetalle',                    'integer');
        $rsm->addScalarResult('ID_COMUNICACION',                'idComunicacion',               'integer');
        $rsm->addScalarResult('OBSERVACION_DETALLE',            'observacionDetalle',           'string');
        $rsm->addScalarResult('LONGITUD',                       'longitud',                     'string');
        $rsm->addScalarResult('LATITUD',                        'latitud',                      'string');        
        $rsm->addScalarResult('NOMBRE_PRODUCTO',                'nombreProducto',               'string');
        $rsm->addScalarResult('ID_PRODUCTO_TECNICO',            'idProductoTecnico',            'string');
        $rsm->addScalarResult('NOMBRE_PRODUCTO_TECNICO',        'nombreProductoTecnico',        'string');
        $rsm->addScalarResult('ULTIMA_MILLA',                   'ultimaMilla',                  'string');
        $rsm->addScalarResult('ELEMENTO_ID',                    'idElemento',                   'integer');
        $rsm->addScalarResult('NOMBRE_OLT',                     'nombreOlt',                    'string');
        $rsm->addScalarResult('INTERFACE_ELEMENTO_ID',          'idInterfaceElemento',          'integer');
        $rsm->addScalarResult('INTERFACE_OLT',                  'interfaceOlt',                 'string');
        $rsm->addScalarResult('ELEMENTO_CONTENEDOR_ID',         'idCaja',                       'integer');
        $rsm->addScalarResult('CAJA',                           'caja',                         'string');
        $rsm->addScalarResult('CAJA_LONGITUD',                  'longitudCaja',                 'string');
        $rsm->addScalarResult('CAJA_LATITUD',                   'latitudCaja',                  'string'); 
        
        $rsm->addScalarResult('ID_PARROQUIA',                   'idParroquia',                  'string'); 
        
        $rsm->addScalarResult('ELEMENTO_CONECTOR_ID',           'idSplitter',                   'integer');
        $rsm->addScalarResult('SPLITTER',                       'splitter',                     'string');
        $rsm->addScalarResult('INTERFACE_ELEMENTO_CONECTOR_ID', 'idInterfaceSplitter',          'integer');
        $rsm->addScalarResult('INTERFACE_SPLITTER',             'interfaceSplitter',            'string');
        $rsm->addScalarResult('IP_ELEMENTO',                    'ipElemento',                   'string');
        $rsm->addScalarResult('MODELO_ELEMENTO',                'modeloElemento',               'string');
        $rsm->addScalarResult('TIENE_ENCUESTA',                 'tieneEncuesta',                'string');
        $rsm->addScalarResult('TIENE_ACTA',                     'tieneActa',                    'string');
        $rsm->addScalarResult('TIENE_RUTA',                     'tieneRuta',                    'string');
        $rsm->addScalarResult('ID_SOLICITUD_MIGRACION',         'idSolicitudMigracion',         'string');
        $rsm->addScalarResult('ESTADO_SOLICITUD_MIGRACION',     'estadoSolicitudMigracion',     'string');
        $rsm->addScalarResult('ESTADO_SOLICITUD_PLANIFICACION', 'estadoSolicitudPlanificacion', 'string');
        $rsm->addScalarResult('TIENE_MATERIAL',                 'tieneMaterial',                'integer');
        $rsm->addScalarResult('FE_SOLICITADA',                  'feSolicitada',                 'string');
        
        $query->setParameter("login",                   $loginActivador);
        $query->setParameter("tipoSolicitud",           $arraySolicitudes);
        $query->setParameter("tipoAsignado",            array('EMPLEADO','CUADRILLA'));
        $query->setParameter("acta",                    'ACTA');
        $query->setParameter("encuesta",                'ENC');
        $query->setParameter("modulo",                  'TECNICO');
        $query->setParameter("productoPreferencia",     'SI');
        $query->setParameter("nombreParamMigracion",    'ESTADOS_SOLICITUD_MIGRACION');
        $query->setParameter("moduloParamMigracion",    'TECNICO');
        $query->setParameter("estadoIp",                'Activo');
        $query->setParameter("nombreCaracteristica",    'PERFIL');
        $query->setParameter("estadoServProdCaract",    'Activo');
        $query->setParameter("tipoSolicitudMigra",      'SOLICITUD MIGRACION');
        $query->setParameter("tipoSolicitudPlan",       'SOLICITUD PLANIFICACION');
        $query->setParameter("estadoServicio",          array('AsignadoTarea','Asignada','EnVerificacion','EnPruebas','Pendiente',
                                                              'Activo','Cancel','Cancelado','PreFactibilidad','FactibilidadEnProceso',
                                                              'Factible')); 
        $query->setParameter("estadoSolicitud",         array('AsignadoTarea','Asignada','AsignadoTarea','Finalizada'));
        $query->setParameter("strEstadoPuntoCarac",     'Activo');
        $query->setParameter("strCaracteristicaRuta",   'Ruta Georreferencial');
        
        if($idServicio != 0)
        {
            $query->setParameter("idServicio",      $idServicio);
        }
                
        $query->setSQL($sql);
        $servicios = $query->getResult();

        return $servicios;
    }

     /**
     * getServiciosPorId
     * Obtiene el servicio según el id
     * Costo=80
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-02-2017

     * @return array $servicios (datosCliente, login, idPunto, idProducto, idPlan, perfil, idServicio, estadoServicio, tipoOrden,
     *                           nombrePlan, direccion, descripcionPunto, longitud, latitud, nombreProducto, idProductoTecnico, 
     *                           nombreProductoTecnico, ultimaMilla, idElemento, nombreOlt, idInterfaceElemento, interfaceOlt, 
     *                           idCaja, caja, idSplitter, splitter, idInterfaceSplitter, interfaceSplitter, ipElemento, 
     *                           modeloElemento, tieneEncuesta, tieneActa, solicitudMigracion, tieneMaterial)
     */    
    public function getArrayDatosServicios($arrayParametro)
    {

        $strUser        = $arrayParametro['strUser'];
        $intServicio    = $arrayParametro['intServicio'];  
                error_log('+*+++++++++++++'.$intServicio.'++++++++++++*********************'.$strUser.'++++++++++++++++++++++******************');

        $arrayServicios = array();
        
        if($intServicio > 0)
        {       

            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $sql = "SELECT
                    TO_CHAR(D.FE_SOLICITADA,'YYYY-MM-DD HH24:MI') FE_SOLICITADA,
                    (SELECT PU.LOGIN||';'||NVL(PE.NOMBRES,PE.RAZON_SOCIAL)||' '||PE.APELLIDOS
                    FROM INFO_PERSONA_EMPRESA_ROL PER,
                      INFO_PERSONA PE
                    WHERE PER.PERSONA_ID   = PE.ID_PERSONA
                    AND PER.ID_PERSONA_ROL = PU.PERSONA_EMPRESA_ROL_ID) DATOS_CLIENTE,
                    (SELECT IP FROM INFO_IP WHERE ELEMENTO_ID = ST.ELEMENTO_ID AND ESTADO = :estadoIp) IP_ELEMENTO,
                    (SELECT ME.NOMBRE_MODELO_ELEMENTO
                      FROM INFO_ELEMENTO E,
                        ADMI_MODELO_ELEMENTO ME
                      WHERE E.ID_ELEMENTO       = ST.ELEMENTO_ID 
                      AND ME.ID_MODELO_ELEMENTO = E.MODELO_ELEMENTO_ID ) MODELO_ELEMENTO,
                      PU.DIRECCION,
                      PU.DESCRIPCION_PUNTO,
                      D.ID_DETALLE,
                      (SELECT NUMERO_TAREA FROM DB_SOPORTE.INFO_DETALLE_TAREAS WHERE DETALLE_ID = D.ID_DETALLE ) ID_COMUNICACION,
                      PU.LONGITUD,
                      PU.LATITUD,
                      P.LOGIN,  
                      PU.ID_PUNTO,
                      S.PRODUCTO_ID,
                      PROD.ID_PRODUCTO ID_PRODUCTO_TECNICO,                      
                      PROD.NOMBRE_TECNICO NOMBRE_PRODUCTO_TECNICO,
                      S.PLAN_ID,
                      (SELECT SERVICIO_PROD_CARACT.VALOR
                        FROM ADMI_CARACTERISTICA CARACTERISTICA,
                        ADMI_PRODUCTO_CARACTERISTICA PRODUCTO_CARACTERISTICA,
                        INFO_SERVICIO_PROD_CARACT SERVICIO_PROD_CARACT
                        WHERE SERVICIO_PROD_CARACT.SERVICIO_ID = S.ID_SERVICIO
                        AND SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
                        AND PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID = CARACTERISTICA.ID_CARACTERISTICA
                        AND CARACTERISTICA.DESCRIPCION_CARACTERISTICA = :nombreCaracteristica
                        and SERVICIO_PROD_CARACT.ESTADO = :estadoServProdCaract
                        and ROWNUM < 2) PERFIL,
                      S.ID_SERVICIO,
                      S.ESTADO,
                      DECODE (S.TIPO_ORDEN, 'N', 'NUEVO', 'T', 'TRASLADO', 'R', 'REUBICACION') TIPO_ORDEN,
                      (SELECT PC.NOMBRE_PLAN||';'||PC.EMPRESA_COD FROM INFO_PLAN_CAB PC WHERE PC.ID_PLAN = S.PLAN_ID ) NOMBRE_PLAN,
                      (SELECT AP.DESCRIPCION_PRODUCTO||';'||AP.EMPRESA_COD 
                      FROM ADMI_PRODUCTO AP
                      WHERE S.PRODUCTO_ID = AP.ID_PRODUCTO) NOMBRE_PRODUCTO,
                      (SELECT TM.NOMBRE_TIPO_MEDIO
                      FROM INFO_SERVICIO_TECNICO ST1,
                        ADMI_TIPO_MEDIO TM
                      WHERE TM.ID_TIPO_MEDIO = ST1.ULTIMA_MILLA_ID
                      AND ST1.SERVICIO_ID    = S.ID_SERVICIO ) ULTIMA_MILLA,
                      ST.ELEMENTO_ID,
                      (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_ID) NOMBRE_OLT,
                      ST.INTERFACE_ELEMENTO_ID,
                      (select IE.NOMBRE_INTERFACE_ELEMENTO  from INFO_INTERFACE_ELEMENTO IE where IE.ID_INTERFACE_ELEMENTO = ST.INTERFACE_ELEMENTO_ID) 
                      INTERFACE_OLT,
                      ST.ELEMENTO_CONTENEDOR_ID,
                      (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_CONTENEDOR_ID) CAJA,
                      (SELECT UBI.LATITUD_UBICACION  
                           FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                          WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                            AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) CAJA_LATITUD,
                      (SELECT UBI.LONGITUD_UBICACION 
                           FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                          WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                            AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) CAJA_LONGITUD,

                    (SELECT UBI.PARROQUIA_ID  
                       FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                      WHERE UBI.ID_UBICACION = EMPELEUBI.UBICACION_ID
                        AND EMPELEUBI.ELEMENTO_ID = ST.ELEMENTO_CONTENEDOR_ID AND ROWNUM < 2) ID_PARROQUIA,

                      ST.ELEMENTO_CONECTOR_ID,
                      (SELECT EL.NOMBRE_ELEMENTO FROM INFO_ELEMENTO EL WHERE EL.ID_ELEMENTO = ST.ELEMENTO_CONECTOR_ID) SPLITTER,
                      ST.INTERFACE_ELEMENTO_CONECTOR_ID,
                      (select IE.NOMBRE_INTERFACE_ELEMENTO  from INFO_INTERFACE_ELEMENTO IE 
                      where IE.ID_INTERFACE_ELEMENTO = ST.INTERFACE_ELEMENTO_CONECTOR_ID) INTERFACE_SPLITTER,
                      TECNK_SERVICIOS.GET_ACTA_ENCUESTA(:acta, :modulo, S.ID_SERVICIO,D.ID_DETALLE) TIENE_ACTA,
                      TECNK_SERVICIOS.GET_ACTA_ENCUESTA(:encuesta, :modulo, S.ID_SERVICIO,D.ID_DETALLE) TIENE_ENCUESTA,
                      TECNK_SERVICIOS.GET_OBSERVACION_DETALLE(D.ID_DETALLE) OBSERVACION_DETALLE,
                      TECNK_SERVICIOS.GET_ID_DET_SOL_PARAM_EST(S.ID_SERVICIO, :tipoSolicitudMigra, :nombreParamMigracion, :moduloParamMigracion) ID_SOLICITUD_MIGRACION,
                      TECNK_SERVICIOS.GET_ESTADO_SOLICITUD_PLANIFICA(S.ID_SERVICIO, :tipoSolicitudMigra) ESTADO_SOLICITUD_MIGRACION,
                      TECNK_SERVICIOS.GET_ESTADO_SOLICITUD_PLANIFICA(S.ID_SERVICIO, :tipoSolicitudPlan) ESTADO_SOLICITUD_PLANIFICACION,
                      (SELECT IPC.ID_PUNTO_CARACTERISTICA 
                        FROM INFO_PUNTO_CARACTERISTICA IPC, ADMI_CARACTERISTICA AC
                        WHERE IPC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA 
                          AND AC.DESCRIPCION_CARACTERISTICA = :strCaracteristicaRuta 
                          AND IPC.ESTADO = :strEstadoPuntoCarac 
                          AND IPC.PUNTO_ID = PU.ID_PUNTO) TIENE_RUTA,
                      (SELECT COUNT(*)
                        FROM DB_SOPORTE.INFO_DETALLE_MATERIAL
                        WHERE DETALLE_ID = D.ID_DETALLE) TIENE_MATERIAL
                    FROM INFO_DETALLE_ASIGNACION DA,
                      INFO_DETALLE D,
                      INFO_DETALLE_SOLICITUD DS,
                      INFO_PERSONA P,
                      INFO_SERVICIO S
                        LEFT JOIN DB_COMERCIAL.INFO_PLAN_CAB PLANC
                        ON PLANC.ID_PLAN = S.PLAN_ID
                        LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET PLAND
                        ON PLAND.PLAN_ID = PLANC.ID_PLAN
                        LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD
                        ON PROD.ID_PRODUCTO     = PLAND.PRODUCTO_ID
                        AND PROD.ES_PREFERENCIA = :productoPreferencia,
                      INFO_PUNTO PU,
                      INFO_SERVICIO_TECNICO ST";

            //parte where del sql
            $sqlWhere = "WHERE DA.DETALLE_ID   = D.ID_DETALLE
                    AND ST.SERVICIO_ID         = S.ID_SERVICIO
                    AND D.DETALLE_SOLICITUD_ID = DS.ID_DETALLE_SOLICITUD
                    AND DA.TIPO_ASIGNADO      IN (:tipoAsignado)
                    AND DA.REF_ASIGNADO_ID     = P.ID_PERSONA
                    AND DS.TIPO_SOLICITUD_ID  IN (:tipoSolicitud)
                    AND DS.ESTADO             IN (:estadoSolicitud)
                    AND S.ID_SERVICIO          = DS.SERVICIO_ID
                    AND S.PUNTO_ID             = PU.ID_PUNTO
                    AND P.LOGIN                = :login";
            $sql = $sql . " " . $sqlWhere;

            if($intServicio != 0)
            {
                $sql = $sql . " AND S.ID_SERVICIO = :idServicio";
                $query->setParameter("idServicio",      $intServicio);

            }

            //parte group by del sql
            $sqlGroup = "GROUP BY S.ID_SERVICIO,
                  PU.ID_PUNTO,
                  S.PRODUCTO_ID,
                  S.PLAN_ID,
                  PROD.ID_PRODUCTO,                      
                  PROD.NOMBRE_TECNICO,                      
                  PROD.DESCRIPCION_PRODUCTO,
                  PU.LOGIN,
                  P.LOGIN,
                  PU.PERSONA_EMPRESA_ROL_ID,
                  ST.ELEMENTO_ID,
                  ST.INTERFACE_ELEMENTO_ID,
                  ST.ELEMENTO_CONTENEDOR_ID,
                  ST.ELEMENTO_CONECTOR_ID,
                  ST.INTERFACE_ELEMENTO_CONECTOR_ID,
                  PU.DIRECCION,
                  PU.DESCRIPCION_PUNTO,
                  PU.LONGITUD,
                  PU.LATITUD,
                  S.ESTADO,
                  S.TIPO_ORDEN,
                  D.ID_DETALLE,
                  D.FE_SOLICITADA
                  ORDER BY D.FE_SOLICITADA";
            $sql = $sql . " " . $sqlGroup;

            $rsm->addScalarResult('DATOS_CLIENTE',                  'datosCliente',                 'string');
            $rsm->addScalarResult('LOGIN',                          'login',                        'string');
            $rsm->addScalarResult('ID_PUNTO',                       'idPunto',                      'integer');
            $rsm->addScalarResult('PRODUCTO_ID',                    'idProducto',                   'integer');
            $rsm->addScalarResult('PLAN_ID',                        'idPlan',                       'integer');
            $rsm->addScalarResult('PERFIL',                         'perfil',                       'string');
            $rsm->addScalarResult('ID_SERVICIO',                    'idServicio',                   'integer');
            $rsm->addScalarResult('ESTADO',                         'estadoServicio',               'string');
            $rsm->addScalarResult('TIPO_ORDEN',                     'tipoOrden',                    'string');
            $rsm->addScalarResult('NOMBRE_PLAN',                    'nombrePlan',                   'string');        
            $rsm->addScalarResult('DIRECCION',                      'direccion',                    'string');
            $rsm->addScalarResult('DESCRIPCION_PUNTO',              'descripcionPunto',             'string');
            $rsm->addScalarResult('ID_DETALLE',                     'idDetalle',                    'integer');
            $rsm->addScalarResult('ID_COMUNICACION',                'idComunicacion',               'integer');
            $rsm->addScalarResult('OBSERVACION_DETALLE',            'observacionDetalle',           'string');
            $rsm->addScalarResult('LONGITUD',                       'longitud',                     'string');
            $rsm->addScalarResult('LATITUD',                        'latitud',                      'string');        
            $rsm->addScalarResult('NOMBRE_PRODUCTO',                'nombreProducto',               'string');
            $rsm->addScalarResult('ID_PRODUCTO_TECNICO',            'idProductoTecnico',            'string');
            $rsm->addScalarResult('NOMBRE_PRODUCTO_TECNICO',        'nombreProductoTecnico',        'string');
            $rsm->addScalarResult('ULTIMA_MILLA',                   'ultimaMilla',                  'string');
            $rsm->addScalarResult('ELEMENTO_ID',                    'idElemento',                   'integer');
            $rsm->addScalarResult('NOMBRE_OLT',                     'nombreOlt',                    'string');
            $rsm->addScalarResult('INTERFACE_ELEMENTO_ID',          'idInterfaceElemento',          'integer');
            $rsm->addScalarResult('INTERFACE_OLT',                  'interfaceOlt',                 'string');
            $rsm->addScalarResult('ELEMENTO_CONTENEDOR_ID',         'idCaja',                       'integer');
            $rsm->addScalarResult('CAJA',                           'caja',                         'string');
            $rsm->addScalarResult('CAJA_LONGITUD',                  'longitudCaja',                 'string');
            $rsm->addScalarResult('CAJA_LATITUD',                   'latitudCaja',                  'string');
             
            $rsm->addScalarResult('ID_PARROQUIA',                   'idParroquia',                  'string'); 
         
            $rsm->addScalarResult('ELEMENTO_CONECTOR_ID',           'idSplitter',                   'integer');
            $rsm->addScalarResult('SPLITTER',                       'splitter',                     'string');
            $rsm->addScalarResult('INTERFACE_ELEMENTO_CONECTOR_ID', 'idInterfaceSplitter',          'integer');
            $rsm->addScalarResult('INTERFACE_SPLITTER',             'interfaceSplitter',            'string');
            $rsm->addScalarResult('IP_ELEMENTO',                    'ipElemento',                   'string');
            $rsm->addScalarResult('MODELO_ELEMENTO',                'modeloElemento',               'string');
            $rsm->addScalarResult('TIENE_ENCUESTA',                 'tieneEncuesta',                'string');
            $rsm->addScalarResult('TIENE_ACTA',                     'tieneActa',                    'string');
            $rsm->addScalarResult('TIENE_RUTA',                     'tieneRuta',                    'string');
            $rsm->addScalarResult('ID_SOLICITUD_MIGRACION',         'idSolicitudMigracion',         'string');
            $rsm->addScalarResult('ESTADO_SOLICITUD_MIGRACION',     'estadoSolicitudMigracion',     'string');
            $rsm->addScalarResult('ESTADO_SOLICITUD_PLANIFICACION', 'estadoSolicitudPlanificacion', 'string');
            $rsm->addScalarResult('TIENE_MATERIAL',                 'tieneMaterial',                'integer');
            $rsm->addScalarResult('FE_SOLICITADA',                  'feSolicitada',                 'string');

            $query->setParameter("login",                   $strUser);
            $query->setParameter("tipoSolicitud",           array('9'));
            $query->setParameter("tipoAsignado",            array('EMPLEADO','CUADRILLA'));
            $query->setParameter("acta",                    'ACTA');
            $query->setParameter("encuesta",                'ENC');
            $query->setParameter("modulo",                  'TECNICO');
            $query->setParameter("productoPreferencia",     'SI');
            $query->setParameter("nombreParamMigracion",    'ESTADOS_SOLICITUD_MIGRACION');
            $query->setParameter("moduloParamMigracion",    'TECNICO');
            $query->setParameter("estadoIp",                'Activo');
            $query->setParameter("nombreCaracteristica",    'PERFIL');
            $query->setParameter("estadoServProdCaract",    'Activo');
            $query->setParameter("tipoSolicitudMigra",      'SOLICITUD MIGRACION');
            $query->setParameter("tipoSolicitudPlan",       'SOLICITUD PLANIFICACION');
            $query->setParameter("estadoSolicitud",         array('AsignadoTarea','Asignada','AsignadoTarea','Finalizada'));
            $query->setParameter("strEstadoPuntoCarac",     'Activo');
            $query->setParameter("strCaracteristicaRuta",   'Ruta Georreferencial');

            $query->setSQL($sql);
            $arrayServicios = $query->getResult();
        
        }

        return $arrayServicios;
    }
    

     /**
     * getServiciosPorId
     * Obtiene el nombre del Hilo por medio del elemento 
     * Costo = 80
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 09-12-2020
     */    
    public function getHiloPrincipalPorElementoId($arrayParametro)
    {

        $intIdElemento                  = $arrayParametro['intIdElemento']?$arrayParametro['intIdElemento']:0;
        $strNombreInterfaceElemento     = $arrayParametro['strNombreInterfaceElemento']?$arrayParametro['strNombreInterfaceElemento']:"";

        $arrayServicios = null;
        
        if($intIdElemento > 0)
        {       

            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "                                   
            SELECT  (HILO.NUMERO_HILO ||', '||HILO.COLOR_HILO) COLOR_HILO
            
            FROM DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO PUERTO,
                    DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE,
                    DB_INFRAESTRUCTURA.INFO_BUFFER_HILO BUFFER_HILO,
                    DB_INFRAESTRUCTURA.ADMI_HILO HILO,
                    DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE2,
                    DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO PUERTO_FIN
            WHERE ENLACE.INTERFACE_ELEMENTO_FIN_ID = PUERTO.ID_INTERFACE_ELEMENTO
                    AND ENLACE.ESTADO = 'Activo'
                    AND ENLACE.BUFFER_HILO_ID = BUFFER_HILO.ID_BUFFER_HILO
                    AND BUFFER_HILO.HILO_ID = HILO.ID_HILO
                    AND BUFFER_HILO.ESTADO = 'Activo'
                    AND PUERTO.ELEMENTO_ID = :intIdElemento 
                    AND PUERTO_FIN.NOMBRE_INTERFACE_ELEMENTO = :strNombreInterfaceElemento 
                    AND PUERTO.ESTADO = 'connected'
                    AND PUERTO.ID_INTERFACE_ELEMENTO = ENLACE2.INTERFACE_ELEMENTO_INI_ID
                    AND ENLACE2.ESTADO = 'Activo'
                    AND ENLACE2.INTERFACE_ELEMENTO_FIN_ID = PUERTO_FIN.ID_INTERFACE_ELEMENTO
                    AND PUERTO_FIN.ESTADO NOT IN ('not connect') 
                    AND ROWNUM <= 1 ";

            $objRsm->addScalarResult('COLOR_HILO',       'colorHilo',    'string');

            $objQuery->setParameter("intIdElemento",               $intIdElemento);
            $objQuery->setParameter("strNombreInterfaceElemento",  $strNombreInterfaceElemento);

            $objQuery->setSQL($strSql);
            $arrayServicios = $objQuery->getResult();
        
        }

        return $arrayServicios;
    }


    
    
    public function generarJsonTareasXUsuario($start, $limit, $estado, $startDate, $endDate, $idDepartamento, $idUsuario, $tipo, $emSoporte, $em)
    {
        $arr_encontrados = array();        
        $rs = $this->getTareasXUsuarioByCasos($start, $limit, $estado, $startDate, $endDate, $idDepartamento, $idUsuario, $tipo);
 
        if(isset($rs))
        {
            $num = count($rs);
            foreach ($rs as $entidad)
            {
                if(intval(date_format($entidad->getFeCreacion(), "G"))<10)
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "0G:i:s"));  
                else
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "G:i:s"));  
                
				$ultimoEstado = $this->getUltimoEstado($entidad->getDetalleId()->getId());
				$estado = ($ultimoEstado ? $ultimoEstado->getEstado() : "");				
				$nombreAsignado = $entidad->getRefAsignadoNombre() ? $entidad->getRefAsignadoNombre() : "";
				
				$nombresAsignadoPor = "";
				$usrAsignadoPor = $entidad->getUsrCreacion() ? $entidad->getUsrCreacion() : "";
				if($usrAsignadoPor)
				{
					$empleado = $em->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($usrAsignadoPor);
					if($empleado && count($empleado)>0)
					{
						$nombresAsignadoPor = (($empleado->getNombres() && $empleado->getApellidos()) ? $empleado->getNombres() . " " . $empleado->getApellidos() : "");
					}
				}
					
					
				$entityDetalle = $entidad->getDetalleId();
				$entityDetalleHipotesisId = ($entityDetalle ? ($entityDetalle->getDetalleHipotesisId() ? $entityDetalle->getDetalleHipotesisId() : '') : '');
                $entityDetalleHipotesis = $emSoporte->getRepository("schemaBundle:InfoDetalleHipotesis")->findOneById($entityDetalleHipotesisId);
				
				$entityCaso = ($entityDetalleHipotesis ? ($entityDetalleHipotesis->getCasoId() ? $entityDetalleHipotesis->getCasoId() : '') : '');
				
                $arr_encontrados[]=array('id'=> $entidad->getId(),
                                         'cid'=> $entidad->getId(),
                                         'id_caso'=> ($entityCaso ? ($entityCaso->getId() ? $entityCaso->getId() : '') : ''),
                                         'numero_caso'=> ($entityCaso ? ($entityCaso->getNumeroCaso() ? $entityCaso->getNumeroCaso() : '') : ''),
                                         'nombreAsignado'=> ucwords( strtolower($nombreAsignado) ),
                                         'nombreAsignadoPor'=> ucwords( strtolower($nombresAsignadoPor) ),
                                         'start' =>$fecha,
                                         'end' =>$fecha,
										 'estado' => $estado,
                                         'title' => "" . ucwords( strtolower( trim($entityDetalle->getTareaId()->getNombreTarea()) ) ),
                                         'note' => "HAVE FUN");
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"success":true,"message":"Loaded data","total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"success":false,"message":"No Loaded data","total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function generarJsonCasosYPlanificacionXUsuario($start, $limit, $origen, $startDate, $endDate, $idUsuario)
    {
        $arr_encontrados = array();  
        $rs = array();
        
        if($origen == "Casos")
        {
            $rs = $this->getTareasXUsuarioByCasos($start, $limit, "Aceptada", $startDate, $endDate, '', $idUsuario, 'ByUsuario');
        }
        else if($origen == "Planificacion")
        {
            $rs = $this->getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate, $idUsuario);
        }
        else if($origen == "Soporte")
        {
            $rs = $this->getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate, $idUsuario);
        }
        else
        {
            $rs_casos = $this->getTareasXUsuarioByCasos($start, $limit, "Aceptada", $startDate, $endDate, '', $idUsuario, 'ByUsuario');
            $rs_planifiacion = $this->getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate, $idUsuario);
            $rs_soporte = $this->getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate, $idUsuario);
            
            if($rs_casos && count($rs_casos)>0)
            {
                foreach($rs_casos as $entity_casos)
                {
                    $rs[] = $entity_casos;
                }
            }
            if($rs_planifiacion && count($rs_planifiacion)>0)
            {
                foreach($rs_planifiacion as $entity_planif)
                {
                    $rs[] = $entity_planif;
                }
            }
            if($rs_soporte && count($rs_soporte)>0)
            {
                foreach($rs_soporte as $entity_soporte)
                {
                    $rs[] = $entity_soporte;
                }
            }
        }
                
        if(isset($rs))
        {
            $num = count($rs);
            foreach ($rs as $entidad)
            {
                if(intval(date_format($entidad->getFeCreacion(), "G"))<10)
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "0G:i:s"));  
                else
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "G:i:s"));  
                
                $origen = "";
                $cid = 4;
                if($entidad->getDetalleId()->getDetalleHipotesisId() != null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                {
                    $origen = "Casos";
                    $cid = 1;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() != null)
                {
                    $origen = "Planificacion";
                    $cid = 2;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                {
                    $origen = "Soporte";
                    $cid = 3;
                }
                
                $arr_encontrados[]=array('id'=> $entidad->getId(),
                                         'cid'=> $cid,
                                         'start' =>$fecha,
                                         'end' =>$fecha,
                                         'origen' =>$origen,
                                         'observacion' =>$entidad->getDetalleId()->getObservacion(),
                                         'title' => "" . trim($entidad->getDetalleId()->getTareaId()->getNombreTarea()),
                                         'note' => "HAVE FUN");
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"success":true,"message":"Loaded data","total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"success":false,"message":"No Loaded data","total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
     /**
     * getInfoPlantillaCorreo
     * 
     * Esta funcion retorna informacion necesaria para el envio del correo cuando se rechaza una tarea
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-11-2015 
     * 
     * @param array  $arrayParametros
     * 
     * @return array $arrayDatos
     * 
     */
    public function getInfoPlantillaCorreo($arrayParametros) 
    {        
        $emSoporte      = $arrayParametros['emSoporte'];
        $emComercial    = $arrayParametros['emComercial'];
        $emComunicacion = $arrayParametros['emComunicacion'];
        $emGeneral      = $arrayParametros['emGeneral'];
        $detalleId      = $arrayParametros['detalleId'];
        $casoId         = $arrayParametros['casoId'];
        $asunto         = $arrayParametros['asunto'];
        
        $infoDetalleAsignacion  = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->findByDetalleId($detalleId);
        $persona                = null;
        $empresa                = '';
        $departamento           = '';
        $canton                 = '';
        $persona                = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->findOneByLogin($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getUsrCreacion());

        if($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getTipoAsignado() == "CUADRILLA")
        {
             $cuadrilla   = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                        ->find($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getAsignadoId());
             if($cuadrilla)
             {
                 $departamento   = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                             ->find($cuadrilla->getDepartamentoId());

                 if($departamento)
                 {
                     $empresa        = $departamento->getEmpresaCod();
                     $departamento   = $departamento->getId();
                 }
                 else
                 {
                     $departamento = '';
                 }
             }
        }
        else
        {
            $departamento   = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                        ->find($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getAsignadoId());
            if($departamento)
            {
                $empresa        = $departamento->getEmpresaCod();
                $departamento   = $departamento->getId();
            }
            else
            {
                $departamento = '';
            }
        }

        if($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getPersonaEmpresaRolId())
        {
            $infoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->find($infoDetalleAsignacion[count($infoDetalleAsignacion) - 1]->getPersonaEmpresaRolId());
        }

        if($infoPersonaEmpresaRol)
        {
            $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                   ->find($infoPersonaEmpresaRol->getOficinaId()->getId());
            $canton  = $oficina->getCantonId();
        }
        else
        {
            $canton = '';
        }

        if($persona->getId())
        {

            $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                    ->findOneBy(array('personaId'       => $persona->getId(),
                                                                      'formaContactoId' => 5,
                                                                      'estado'          => "Activo"));

            //OBTENGO EL CONTACTO DE LA PERSONA QUE CREO LA TAREA
            if($infoPersonaFormaContacto)
            {
                $destinatario = $infoPersonaFormaContacto->getValor(); //Correo Persona Creo la Tarea
            }
        }

        if($casoId)
        {
            $perteneceACaso         = 'true';
            $caso                   = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($casoId);
            $numeracion             = $caso->getNumeroCaso();

            $numeracionReferencia   = ' al Caso #' . $numeracion;
        }
        else
        {
            $perteneceACaso         = 'false';
            $actividad              = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->getNumeroActividadXDetalle($detalleId);

            if($actividad[0])
            {
                $numeracion             = $actividad[0]->getId();
                $numeracionReferencia   = ' a la Actividad #' . $numeracion;
            }
            else
            {
                $numeracionReferencia   = ' a Actividad';
            }
        }      
        
        $arrayDatos['perteneceACaso']       = $perteneceACaso;
        $arrayDatos['numeracion']           = $numeracion;
        $arrayDatos['numeracionReferencia'] = $numeracionReferencia;
        $arrayDatos['empresa']              = $empresa;
        $arrayDatos['departamento']         = $departamento;
        $arrayDatos['canton']               = $canton;        
        $arrayDatos['asunto']               = $asunto;
        $arrayDatos['destinatario']         = $destinatario;  
        
        return $arrayDatos;
    }     
    

    public function generarJsonCasosYPlanificacionXDepartamento($start, $limit, $origen, $startDate, $endDate, $idDepartamento)
    {
        $arr_encontrados = array();  
        $rs = array();
        
        if($origen == "Casos")
        {
            $rs = $this->getTareasXUsuarioByCasos($start, $limit, "Aceptada", $startDate, $endDate, $idDepartamento, '', 'ByDepartamento');
        }
        else if($origen == "Planificacion")
        {
            $rs = $this->getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate, '',$idDepartamento,'ByDepartamento');
        }
        else if($origen == "Soporte")
        {
            $rs = $this->getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate, '',$idDepartamento,'ByDepartamento');
        }
        else
        {
            $rs_casos = $this->getTareasXUsuarioByCasos($start, $limit, "Aceptada", $startDate, $endDate,$idDepartamento, '', 'ByDepartamento');
            $rs_planifiacion = $this->getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate, '',$idDepartamento,'ByDepartamento');
            $rs_soporte = $this->getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate, '',$idDepartamento,'ByDepartamento');
            
            if($rs_casos && count($rs_casos)>0)
            {
                foreach($rs_casos as $entity_casos)
                {
                    $rs[] = $entity_casos;
                }
            }
            if($rs_planifiacion && count($rs_planifiacion)>0)
            {
                foreach($rs_planifiacion as $entity_planif)
                {
                    $rs[] = $entity_planif;
                }
            }
            if($rs_soporte && count($rs_soporte)>0)
            {
                foreach($rs_soporte as $entity_soporte)
                {
                    $rs[] = $entity_soporte;
                }
            }
        }
                
        if(isset($rs))
        {
            $num = count($rs);
            foreach ($rs as $entidad)
            {
                if(intval(date_format($entidad->getFeCreacion(), "G"))<10)
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "0G:i:s"));  
                else
                    $fecha = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "G:i:s"));  
                
                $origen = "";
                $cid = 4;
                if($entidad->getDetalleId()->getDetalleHipotesisId() != null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                {
                    $origen = "Casos";
                    $cid = 1;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() != null)
                {
                    $origen = "Planificacion";
                    $cid = 2;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                {
                    $origen = "Soporte";
                    $cid = 3;
                }
                
                $arr_encontrados[]=array('id'=> $entidad->getId(),
                                         'cid'=> $cid,
                                         'start' =>$fecha,
                                         'end' =>$fecha,
                                         'origen' =>$origen,
                                         'observacion' =>$entidad->getDetalleId()->getObservacion(),
                                         'title' => "" . trim($entidad->getDetalleId()->getTareaId()->getNombreTarea()),
                                         'note' => "HAVE FUN");
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"success":true,"message":"Loaded data","total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"success":false,"message":"No Loaded data","total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function getTareasXUsuarioByCasos($start, $limit, $estado, $startDate, $endDate, $idDepartamento, $idUsuario, $tipo) 
    {        
        $boolBusqueda = false; 
        $where = "";  
        
        $qb = $this->_em->createQueryBuilder();
        if($startDate!=""){
            $dateF = explode("-",$startDate);
            $fechaSql = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion >= '".trim($fechaSql)."' ";
        }
        if($endDate!=""){
            $dateF = explode("-",$endDate);
            $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
            $fechaSql = date("Y/m/d", $fechaSqlAdd);
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion <= '".trim($fechaSql)."' ";
        }
		if($estado != "")
		{
			$where .= "AND LOWER(detalleHistorial.estado) like LOWER('".$estado."') ";
		}
		
		if($tipo == "ByDepartamento")
		{
			$where .= "AND detalleAsignacion.asignadoId = ".$idDepartamento." ";
		}
		if($tipo == "ByUsuario")
		{
			$where .= "AND detalleAsignacion.refAsignadoId = ".$idUsuario." ";
		}
		
        $sql = "SELECT detalleAsignacion        
                FROM  schemaBundle:InfoDetalleAsignacion detalleAsignacion,
                schemaBundle:InfoDetalleHistorial detalleHistorial,
                schemaBundle:InfoDetalle detalle,
                schemaBundle:InfoDetalleHipotesis detalleHipotesis
                WHERE  
                detalle = detalleHistorial.detalleId 
                AND detalleHipotesis = detalle.detalleHipotesisId 
                AND detalleHipotesis.casoId is not null 
                AND detalleAsignacion.detalleId = detalleHistorial.detalleId 
                AND detalleHistorial.id = (SELECT MAX(detalleHistorialMax.id) 
                                           FROM schemaBundle:InfoDetalleHistorial detalleHistorialMax
                                            WHERE detalleHistorialMax.detalleId = detalleHistorial.detalleId) 
                AND detalleAsignacion.id = (SELECT MAX(detalleAsignacionMax.id) 
                                           FROM schemaBundle:InfoDetalleAsignacion detalleAsignacionMax
                                            WHERE detalleAsignacionMax.detalleId = detalleAsignacion.detalleId)  
                $where
				";
		
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();       
		
		//echo $query->getSql();
        return $datos;        
    }
    
    public function getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate,$idUsuario,$idDepartamento="",$tipo="ByUsuario")
    {        
        $boolBusqueda = false; 
        $where = "";  
        
        $qb = $this->_em->createQueryBuilder();
        if($startDate!=""){
            $dateF = explode("-",$startDate);
            $fechaSql = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion >= '".trim($fechaSql)."' ";
        }
        if($endDate!=""){
            $dateF = explode("-",$endDate);
            $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
            $fechaSql = date("Y/m/d", $fechaSqlAdd);
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion <= '".trim($fechaSql)."' ";
        }
		if($tipo == "ByDepartamento")
		{
			$where .= "AND detalleAsignacion.asignadoId = ".$idDepartamento." ";
		}
		if($tipo == "ByUsuario")
		{
			$where .= "AND detalleAsignacion.refAsignadoId = ".$idUsuario." ";
		}
        $sql = "SELECT detalleAsignacion        
                FROM  schemaBundle:InfoDetalleAsignacion detalleAsignacion,
                schemaBundle:InfoDetalle detalle
                WHERE detalle = detalleAsignacion.detalleId ".
                "AND detalle.detalleSolicitudId is not null ".
                "AND detalle.detalleHipotesisId is null ".
                "AND detalleAsignacion.id = (SELECT MAX(detalleAsignacionMax.id) ".
                "                           FROM schemaBundle:InfoDetalleAsignacion detalleAsignacionMax
                                            WHERE detalleAsignacionMax.detalleId = detalleAsignacion.detalleId)  ".
                $where;
                
        //PREGUNTAR PARA BUSCAR POR USUARIO ID --- DE CUADRILLA....
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();        
        return $datos;        
    }
	
    public function getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate,$idUsuario,$idDepartamento="",$tipo="ByUsuario")
    {        
        $boolBusqueda = false; 
        $where = "";  
        
        $qb = $this->_em->createQueryBuilder();
        if($startDate!=""){
            $dateF = explode("-",$startDate);
            $fechaSql = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion >= '".trim($fechaSql)."' ";
        }
        if($endDate!=""){
            $dateF = explode("-",$endDate);
            $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
            $fechaSql = date("Y/m/d", $fechaSqlAdd);
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion <= '".trim($fechaSql)."' ";
        }
		if($tipo == "ByDepartamento")
		{
			$where .= "AND detalleAsignacion.asignadoId = ".$idDepartamento." ";
		}
		if($tipo == "ByUsuario")
		{
			$where .= "AND detalleAsignacion.refAsignadoId = ".$idUsuario." ";
		}
        $sql = "SELECT detalleAsignacion        
                FROM  schemaBundle:InfoDetalleAsignacion detalleAsignacion,
                schemaBundle:InfoDetalle detalle
                WHERE detalle = detalleAsignacion.detalleId ".
                "AND detalle.detalleSolicitudId is null ".
                "AND detalle.detalleHipotesisId is null ".
                "AND detalleAsignacion.id = (SELECT MAX(detalleAsignacionMax.id) ".
                "                           FROM schemaBundle:InfoDetalleAsignacion detalleAsignacionMax
                                            WHERE detalleAsignacionMax.detalleId = detalleAsignacion.detalleId)  ".
                $where;
                
        //PREGUNTAR PARA BUSCAR POR USUARIO ID --- DE CUADRILLA....
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();        
        return $datos;        
    }

    public function getUltimaAsignacion($id_detalle)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('asignacion')
           ->from('schemaBundle:InfoDetalleAsignacion','asignacion')
           ->where('asignacion.detalleId = ?1')
           ->setParameter(1, $id_detalle)
           ->orderBy('asignacion.id','DESC')
           ->setMaxResults(1);
        
        $query = $qb->getQuery();
        $results = $query->getResult();
       
        if(count($results)>0) return $results[0];
        else return false;
    }

    public function getUltimoEstado($id_detalle)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('dH')
           ->from('schemaBundle:InfoDetalleHistorial','dH')
           ->where('dH.detalleId = ?1')
           ->setParameter(1, $id_detalle)
           ->orderBy('dH.id','DESC')
           ->setMaxResults(1);
        
        $query = $qb->getQuery();
        $results = $query->getResult();
       
        if(count($results)>0) return $results[0];
        else return false;
    }
    
   ///////////////////inicio: taty///////
   
    public function generarJsonTareasTodas($start, $limit, $origen, $startDate, $endDate, $idUsuario,$idDepartamento,$tipo,$emComercial,$em_soporte)
    {
          
        $arr_encontrados = array();  
        $rs = array();
        
        if($origen == "Casos")
        {
            $rs = $this->getTareasXUsuarioByCasos($start, $limit, "Aceptada", $startDate, $endDate, '', $idUsuario, 'ByUsuario');
        }
        else if($origen == "Planificacion")
        {
            $rs = $this->getTareasXUsuarioByPlanificacion($start, $limit, $startDate, $endDate, $idUsuario);
        }
        else if($origen == "Soporte")
        {
            $rs = $this->getTareasXUsuarioBySoporte($start, $limit, $startDate, $endDate, $idUsuario);
        }
       else if($tipo=="ByUsuario")
        {
           
           
            $idDepartamento="";
            $rs= $this->getTareasTodas($start, $limit, $estado="", $startDate, $endDate, $idDepartamento, $idUsuario, $tipo); 
           
        }
        else if($tipo=="ByDepartamento")
        {
           
           
           // $idDepartamento="";
            $rs= $this->getTareasTodas($start, $limit, $estado="", $startDate, $endDate, $idDepartamento, $idUsuario, $tipo); 
           
        }
           
        if(isset($rs) )
        {
            $num = count($rs);
            
            foreach ($rs as $entidad)
            {
                
             
                if(intval(date_format($entidad->getDetalleId()->getFeSolicitada(), "G"))<10)
                    $fecha = strval(date_format($entidad->getDetalleId()->getFeSolicitada(), "Y-m-d")). "T" . strval(date_format($entidad->getDetalleId()->getFeSolicitada(), "0G:i:s"));  
                else
                    $fecha = strval(date_format($entidad->getDetalleId()->getFeSolicitada(), "Y-m-d")). "T" . strval(date_format($entidad->getDetalleId()->getFeSolicitada(), "G:i:s"));  
                
                if(intval(date_format($entidad->getFeCreacion(), "G"))<10)
                    $fechaAsignada = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "0G:i:s"));  
                else
                    $fechaAsignada = strval(date_format($entidad->getFeCreacion(), "Y-m-d")). "T" . strval(date_format($entidad->getFeCreacion(), "G:i:s"));  
                
                
                
                $origen = "";
                $cid = 4;
                
                
                if($entidad->getDetalleId()->getDetalleHipotesisId() != null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                 
                {
                    $origen = "Casos";
                    $cid = 1;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() != null)
                
                {
                    $origen = "Planificacion";
                    $cid = 2;
                }
                if($entidad->getDetalleId()->getDetalleHipotesisId() == null && $entidad->getDetalleId()->getDetalleSolicitudId() == null)
                 
                    
                {
                    $origen = "Soporte";
                    $cid = 3;
                }
                
                
                $usrAsignaTarea=$entidad->getUsrCreacion();
                $personaAsignaTarea= $emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$usrAsignaTarea)); 
                $nombreAsignadoPor="";
                if($personaAsignaTarea->getNombres()!=""){
                    $nombreAsignadoPor=$personaAsignaTarea->getNombres()." ".$personaAsignaTarea->getApellidos();
                }else{
                  $nombreAsignadoPor=  $personaAsignaTarea->getRazonSocial();
                }
                
                
               $infoDetalleHistorial=$em_soporte->getRepository('schemaBundle:InfoDetalleHistorial')->findOneBy(array('detalleId'=>$entidad->getDetalleId() ),array('id'=>'DESC')); 
               $estado= $infoDetalleHistorial->getEstado();
               
               ///si l epertenece a un caso:
               $idCaso="";
               $numeroCaso="";
              
              
             if(($entidad->getDetalleId()->getDetalleHipotesisId())) {
                if($entidad->getDetalleId()->getDetalleHipotesisId()){
                   $infoDetalleHipotesis=$em_soporte->getRepository('schemaBundle:InfoDetalleHipotesis')->find($entidad->getDetalleId()->getDetalleHipotesisId()); 
                   $infoCaso=$infoDetalleHipotesis->getCasoId(); 
                   $idCaso=$infoCaso->getId();
                   $numeroCaso=$infoCaso->getNumeroCaso();
                }
              }
                
               $arr_encontrados[]=array('id'=> $entidad->getId(),
                                         'cid'=> $cid,
                                         'detalleId'=>$entidad->getDetalleId()->getId(),
                                         'start' =>$fecha,
                                         'end' =>$fecha,
                                         'feAsignada' =>$fechaAsignada,
                                         'origen' =>$origen,
                                         'observacion' =>$entidad->getDetalleId()->getObservacion(),
                                         'title' => "" . trim($entidad->getDetalleId()->getTareaId()->getNombreTarea()),
                                         'note' => $entidad->getMotivo(),
                                         'idAsignado'=>$entidad->getRefAsignadoId(),
                                         'nombreAsignado'=>$entidad->getRefAsignadoNombre(),
                                         'idAsignadoPor'=>$personaAsignaTarea->getId(),
                                         'nombreAsignadoPor'=>$nombreAsignadoPor,
                                         'estado'=>$estado,
                                         'id_caso'=>$idCaso,
                                          'numero_caso'=>$numeroCaso
                                        );
                
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"success":true,"message":"Loaded data","total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"success":false,"message":"No Loaded data","total":"0","encontrados":[]}';
            return $resultado;
        }
       
    }
    
    public function getTareasTodas($start, $limit, $estado, $startDate, $endDate, $idDepartamento, $idUsuario, $tipo) 
    {        
        $boolBusqueda = false; 
        $where = "";  
       
        if($startDate!=""){
            $dateF = explode("-",$startDate);
            $fechaSql = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion >= '".trim($fechaSql)."' ";
        }
        if($endDate!=""){
            $dateF = explode("-",$endDate);
            $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
            $fechaSql = date("Y/m/d", $fechaSqlAdd);
            
            $boolBusqueda = true;
            $where .= "AND detalleAsignacion.feCreacion <= '".trim($fechaSql)."' ";
        }
		if($estado != "")
		{
			$where .= "AND LOWER(detalleHistorial.estado) like LOWER('".$estado."') ";
		}
		
		if($tipo == "ByDepartamento")
		{
			$where .= "AND detalleAsignacion.asignadoId = ".$idDepartamento." ";
			$where .= "AND detalle.feSolicitada  is not null ";
		}
		if($tipo == "ByUsuario")
		{
			$where .= "AND detalleAsignacion.refAsignadoId = ".$idUsuario." ";
		}
		
        $sql = "SELECT detalleAsignacion
                FROM  schemaBundle:InfoDetalleAsignacion detalleAsignacion,
                schemaBundle:InfoDetalleHistorial detalleHistorial,
                schemaBundle:InfoDetalle detalle
                WHERE  
                detalle = detalleHistorial.detalleId 
                AND detalle=detalleAsignacion.detalleId 
                
                AND detalleAsignacion.detalleId = detalleHistorial.detalleId 
                AND detalleHistorial.id = (SELECT MAX(dhMax.id) 
								FROM schemaBundle:InfoDetalleHistorial dhMax
								WHERE dhMax.detalleId = detalleHistorial.detalleId)  
                AND detalleAsignacion.id = (SELECT MAX(daMax.id) 
								FROM schemaBundle:InfoDetalleAsignacion daMax
								WHERE daMax.detalleId = detalleAsignacion.detalleId) 
                $where
                            ";
		
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();  
        
        return $datos;        
    
    }
   
   ///////////////fin:taty////////////
    
    
    /**
      * getResultadoUltimoDetalleAsignacionTareaRechazada
      *
      * Método que retornará el historial del detalle de acuerdo a los parámetros enviados.
      * Costo = 11                                    
      *
      * @param array $arrayParametros
      * @return array $arrayResultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 17-11-2016
      * 
      */
    public function getResultadoUltimoDetalleAsignacionTareaRechazada($arrayParametros)
    {

        $arrayResultado     = array();
        try
        {
            $objQuery       = $this->_em->createQuery();
            
            
            $strSqlSelectAsignacionMax  = "SELECT max(detalleAsignacionMax.id) ";
            $strSqlFromAsignacionMax    = "FROM schemaBundle:InfoDetalleAsignacion detalleAsignacionMax ";
            $strSqlWhereAsignacionMax   = "WHERE detalleAsignacionMax.detalleId = :intIdDetalle ";
            
            $strSqlAsignacionMax        = "(".$strSqlSelectAsignacionMax.$strSqlFromAsignacionMax.$strSqlWhereAsignacionMax.") ";
            
            
            $strSqlSelectAsignacionesPrincipal      = "SELECT detalleAsignacion ";
            $strSqlFromAsignacionesPrincipal        = "FROM schemaBundle:InfoDetalleAsignacion detalleAsignacion ";
            $strSqlWhereAsignacionesPrincipal       = "WHERE detalleAsignacion.detalleId = :intIdDetalle "
                                                    . "AND detalleAsignacion.id < ".$strSqlAsignacionMax;
            $strSqlOrderByAsignacionesPrincipal     = "ORDER BY detalleAsignacion.id DESC ";
            
            $objQuery->setParameter("intIdDetalle", $arrayParametros["intIdDetalle"]);
            
            $strSql         = $strSqlSelectAsignacionesPrincipal
                            . $strSqlFromAsignacionesPrincipal
                            . $strSqlWhereAsignacionesPrincipal
                            . $strSqlOrderByAsignacionesPrincipal;
            $objQuery->setDQL($strSql);
            $arrayResultado = $objQuery->getResult();
            
        }
        catch (\Exception $e) 
        {
            error_log($e->getMessage());

        }
        return $arrayResultado;

    }
    
    /**
      * getAsignacionesVerTareaAnulada
      *
      * Método que retornará el número de asignaciones de una determinada tarea y la información de la asignación de una tarea a una 
      * determinada persona.
      * Esta función consulta el número de asignaciones en total que se han realizado a una determinada tarea y los datos de las asignaciones
      * realizadas a la persona en sesión.
      * 
      * Costo = 9                       
      *
      * @param array $arrayParametros
      * @return array $arrayRespuesta
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 17-11-2016
      * 
      */
    public function getAsignacionesVerTareaAnulada($arrayParametros) 
    {
        $arrayRespuesta                = array();
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $objQuery       = $this->_em->createQuery();
            $objQueryCount  = $this->_em->createQuery();
            $strSelectCount = " SELECT COUNT(infoDetalleAsignacion.id) ";
            $strSelect      = " SELECT infoDetalleAsignacion.id ";
            $strFromWhere   = " FROM 
                                schemaBundle:InfoDetalleAsignacion infoDetalleAsignacion,
                                schemaBundle:InfoDetalle infoDetalle,
                                schemaBundle:InfoPersona infoPersona
                                WHERE infoDetalleAsignacion.detalleId = infoDetalle.id 
                                      AND infoPersona.id    = infoDetalleAsignacion.refAsignadoId ";

            
            $strWhereInfo   = "";
            if(isset($arrayParametros['intIdDetalle']) && !empty($arrayParametros['intIdDetalle']))
            {
                $strFromWhere .= "AND infoDetalleAsignacion.detalleId = :intIdDetalle ";
                $objQueryCount->setParameter("intIdDetalle", $arrayParametros['intIdDetalle']);
                $objQuery->setParameter("intIdDetalle", $arrayParametros['intIdDetalle']);
            }
                
            if(isset($arrayParametros['intIdRefAsignado']) && !empty($arrayParametros['intIdRefAsignado']))
            {
                $strWhereInfo   = "AND infoPersona.login = infoDetalle.usrCreacion 
                                   AND infoDetalleAsignacion.refAsignadoId = :intIdRefAsignado ";
                $objQuery->setParameter("intIdRefAsignado", $arrayParametros['intIdRefAsignado']);
            }
            

            $strSqlCount    = $strSelectCount.$strFromWhere;
            $strSql         = $strSelect.$strFromWhere.$strWhereInfo;
            
            $objQuery->setDQL($strSql);
            $objQueryCount->setDQL($strSqlCount);
            
            $arrayRespuesta['resultado']    = $objQuery->getResult();
            $arrayRespuesta['total']        = $objQueryCount->getSingleScalarResult();
            
        } 
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayRespuesta;
    }
    
    /**
      * getTareaPorIncidencias
      *
      * Método que retornará el listado de tareas de tipo Incidencias.
      *
      * Costo = 1513
      *
      * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
      * @version 1.0 20-09-2017
      *
      * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
      * @version 1.1 25-09-2017 - Se Obtiene de la consulta la longitud, latitud, observacion y usuario de creacion
      *                           de la tarea de incidencia asignada a un tecnico.
      * @param array $arrayParametros
      * @return array $arrayRespuesta
      */
    public function getTareaPorIncidencias($arrayParametros)
    {
        $arrayRespuesta                = array();
        $arrayRespuesta['resultado']   = "";
        try
        {
            $objQuery       = $this->_em->createQuery();
            $strSelect      = " SELECT
                                    infoDetalle.id                                 idTarea,
                                    admiTarea.nombreTarea                          tareaInicial,
                                    admiTarea.id                                   idTareaInicial,
                                    infoDetalle.feCreacion                         fechaInicial,
                                    infoDetalleAsignacion.refAsignadoNombre        nombreAsignado,
                                    (SELECT MIN(infCom.id)
                                     FROM schemaBundle:InfoComunicacion infCom
                                     WHERE infCom.detalleId = infoDetalle.id)      idComunicacion,
                                    infoDetalleAsignacion.cantonId                 cantonId,
                                    infoDetalle.longitud                           longitud,
                                    infoDetalle.latitud                            latitud,
                                    infoDetalle.observacion                        observacion,
                                    infoDetalle.usrCreacion                        usrCreacionDetalle,
                                    infoDetalleHistorial.estado ";
            $strFromWhere   = " FROM
                                schemaBundle:InfoDetalleAsignacion    infoDetalleAsignacion,
                                schemaBundle:InfoDetalle              infoDetalle,
                                schemaBundle:InfoDetalleHistorial     infoDetalleHistorial,
                                schemaBundle:AdmiTarea                admiTarea
                                WHERE infoDetalleAsignacion.detalleId = infoDetalle.id
                                  AND infoDetalle.id                  = infoDetalleHistorial.detalleId
                                  AND infoDetalle.tareaId             = admiTarea.id
                                  AND infoDetalleHistorial.id         =( SELECT MAX(infDetHis.id) AS detalleHist
                                                                         FROM schemaBundle:InfoDetalleHistorial infDetHis
                                                                         WHERE infDetHis.detalleId = infoDetalleAsignacion.detalleId )
                                  AND infoDetalleAsignacion.id        =( SELECT MAX(infDetAsig.id) AS detalleAsign
                                                                         FROM schemaBundle:InfoDetalleAsignacion infDetAsig
                                                                         WHERE infDetAsig.detalleId = infoDetalleAsignacion.detalleId ) ";

            $strOrderBy     =" ORDER BY infoDetalle.feSolicitada ";
            if(isset($arrayParametros['intIdPersonaEmpresaRol']) && !empty($arrayParametros['intIdPersonaEmpresaRol']))
            {
                $strFromWhere .= " AND infoDetalleAsignacion.personaEmpresaRolId = :intIdPersonaEmpresaRol ";
                $objQuery->setParameter("intIdPersonaEmpresaRol", $arrayParametros['intIdPersonaEmpresaRol']);
            }
            if(isset($arrayParametros['arrayTareaId']) && !empty($arrayParametros['arrayTareaId']))
            {
                $strFromWhere .= " AND infoDetalle.tareaId IN (:arrayTareaId) ";
                $objQuery->setParameter("arrayTareaId", array_values($arrayParametros['arrayTareaId']));
            }
            if(isset($arrayParametros['arrayEstado']) && !empty($arrayParametros['arrayEstado']))
            {
                $strFromWhere .= " AND infoDetalleHistorial.estado IN (:arrayEstado) ";
                $objQuery->setParameter("arrayEstado", array_values($arrayParametros['arrayEstado']));
            }
            $strSql         = $strSelect.$strFromWhere.$strOrderBy;
            $objQuery->setDQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch (\Exception $e)
        {
            error_log('InfoDetalleAsignacionRepository->getTareaPorIncidencias()  fffff'.$e->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
      * getTareaInterdepartamentales
      *
      * Método que retornará el listado de las tareas interdepartamentales.
      *
      * Costo = 3089
      *
      * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
      * @version 1.0 24-11-2017
      * 
      * @author John Vera Rendon <wgaibor@telconet.ec>
      * @version 1.0 11-03-2018 Se agrego para q filtre por el id de la tarea
      *
      * @param array $arrayParametros{
      *                                 intIdPersonaEmpresaRol: integer: Id persona empresa rol del operativo.
      *                                 arrayTareaId          : array  : Arreglo que contiene los idTareas que no deben ser considerados para mostrar
      *                                                                  el listado de las tareas interdepartamentales.
      *                                 arrayEstado           : array  : Arreglo que contiene los estados que debe encontrarse la tarea para poder
      *                                                                  ser listada en las tareas interdepartamentales.
      *                              }
      * @return array $arrayRespuesta : Retorna el listado de las tareas interdepartamentales.
      */
    public function getTareaInterdepartamentales($arrayParametros)
    {
        $arrayRespuesta                = array();
        $boolBusqueda                  = false;
        $arrayRespuesta['resultado']   = "";
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect      = 'WITH 
                                MIN_COMUNICACION AS
                                    (SELECT ICO.DETALLE_ID, MIN(ICO.ID_COMUNICACION) MIN_COM
                                    FROM DB_COMUNICACION.INFO_COMUNICACION ICO
                                    GROUP BY ICO.DETALLE_ID),
                                MAX_ASIGNACION AS
                                    (SELECT IDA.DETALLE_ID, MAX (IDA.ID_DETALLE_ASIGNACION) MAX_ASI
                                    FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA
                                    GROUP BY IDA.DETALLE_ID)
                                SELECT IDET.ID_DETALLE                           AS ID_DETALLE,
                                    ATAR.NOMBRE_TAREA                            AS NOMBRE_TAREA,
                                    ATAR.ID_TAREA                                AS ID_TAREA,
                                    IDET.FE_CREACION                             AS FE_CREACION,
                                    IDAS.REF_ASIGNADO_NOMBRE                     AS REF_ASIGNADO_NOMBRE,
                                    ICOM.id_comunicacion                         AS ID_COMUNICACION,
                                    IDAS.CANTON_ID                               AS CANTON_ID,
                                    IDET.LONGITUD                                AS LONGITUD,
                                    IDET.LATITUD                                 AS LATITUD,
                                    IDET.OBSERVACION                             AS OBSERVACION,
                                    IDET.USR_CREACION                            AS USR_CREACION,
                                    IDHI.ESTADO                                  AS ESTADO ';

            $objRsm->addScalarResult('ID_DETALLE', 'idTarea', 'integer');
            $objRsm->addScalarResult('NOMBRE_TAREA', 'tareaInicial', 'string');
            $objRsm->addScalarResult('ID_TAREA', 'idTareaInicial', 'integer');
            $objRsm->addScalarResult('FE_CREACION', 'fechaInicial', 'datetime');
            $objRsm->addScalarResult('REF_ASIGNADO_NOMBRE', 'nombreAsignado', 'string');
            $objRsm->addScalarResult('ID_COMUNICACION', 'idComunicacion', 'string');
            $objRsm->addScalarResult('CANTON_ID', 'cantonId', 'integer');
            $objRsm->addScalarResult('LONGITUD', 'longitud', 'string');
            $objRsm->addScalarResult('LATITUD', 'latitud', 'string');
            $objRsm->addScalarResult('OBSERVACION', 'observacion', 'string');
            $objRsm->addScalarResult('USR_CREACION', 'usrCreacionDetalle', 'string');
            $objRsm->addScalarResult('ESTADO', 'estado', 'string');

            $strFromWhere   =  'FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDAS,
                                    DB_SOPORTE.INFO_DETALLE           IDET,
                                    DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHI,
                                    Db_Soporte.ADMI_TAREA             ATAR,
                                    DB_COMUNICACION.INFO_COMUNICACION      ICOM
                                WHERE IDAS.DETALLE_ID = IDET.ID_DETALLE
                                    AND IDET.ID_DETALLE = IDHI.DETALLE_ID
                                    AND IDET.TAREA_ID = ATAR.ID_TAREA
                                    AND ICOM.DETALLE_ID = IDET.ID_DETALLE
                                    AND IDHI.ID_DETALLE_HISTORIAL =
                                        (SELECT MAX (IDH.ID_DETALLE_HISTORIAL)     AS dctrn__detalleHist
                                        FROM db_soporte.INFO_DETALLE_HISTORIAL IDH
                                        WHERE IDH.DETALLE_ID = IDAS.DETALLE_ID)
                                    AND ICOM.CASO_ID IS NULL
                                    AND IDAS.ID_DETALLE_ASIGNACION = (SELECT MAX_ASI FROM MAX_ASIGNACION MA WHERE MA.DETALLE_ID = IDAS.DETALLE_ID)
                                    AND ICOM.ID_COMUNICACION = (SELECT MIN_COM FROM MIN_COMUNICACION MC WHERE MC.DETALLE_ID = ICOM.DETALLE_ID) ';

            $strOrderBy     =   ' ORDER BY IDET.FE_SOLICITADA DESC';
            
            if(isset($arrayParametros['idDetalle']) && !empty($arrayParametros['idDetalle']))
            {
                $strFromWhere .= ' AND IDET.ID_DETALLE = :idDetalle ';
                $objNtvQuery->setParameter("idDetalle", $arrayParametros['idDetalle']);
                $boolBusqueda  = true;
            }
            if(isset($arrayParametros['intIdPersonaEmpresaRol']) && !empty($arrayParametros['intIdPersonaEmpresaRol']))
            {
                $strFromWhere .= ' AND IDAS.PERSONA_EMPRESA_ROL_ID = :intIdPersonaEmpresaRol ';
                $objNtvQuery->setParameter("intIdPersonaEmpresaRol", $arrayParametros['intIdPersonaEmpresaRol']);
                $boolBusqueda  = true;
            }
            if(isset($arrayParametros['arrayTareaId']) && !empty($arrayParametros['arrayTareaId']))
            {
                $strFromWhere .= ' AND IDET.TAREA_ID NOT IN (:arrayTareaId) ';
                $objNtvQuery->setParameter("arrayTareaId", array_values($arrayParametros['arrayTareaId']));
                $boolBusqueda  = true;
            }
            if(isset($arrayParametros['arrayEstado']) && !empty($arrayParametros['arrayEstado']))
            {
                $strFromWhere .= ' AND IDHI.ESTADO IN (:arrayEstado) ';
                $objNtvQuery->setParameter("arrayEstado", array_values($arrayParametros['arrayEstado']));
                $boolBusqueda  = true;
            }
            if($boolBusqueda)
            {
                $strSql         = $strSelect.$strFromWhere.$strOrderBy;
               
                $objNtvQuery->setSQL($strSql);
                $arrayRespuesta = $objNtvQuery->getResult();
            }
            else
            {
                error_log('InfoDetalleAsignacionRepository->getTareaInterdepartamentales()  : La consulta de las tareas Interdepartamentales no '
                         . 'cumple con los filtros necesarios para recuperar la información.');
            }
        }
        catch (\Exception $e)
        {
            error_log('InfoDetalleAsignacionRepository->getTareaInterdepartamentales()  '.$e->getMessage());
        }
        return $arrayRespuesta;
    }
    
    
    
    /**
     * Método que obtiene puerto,elemento, modelo e interfaz del splitter y olt por servicio.
     *
     * Costo: 15
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 11-07-2018
     *
     * @param  $arrayParametros[ intIdServicio , strEstado ]
     * @return $arrayResultado
     */
    public function getInfoOltySplitterPorServicio($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $intIdServicio    = $arrayParametros['data']['idServicio'];
        $strEstado        = $arrayParametros['data']['estado'];
        $arrayResultado   = array();
        try
        {
            $strSql = " SELECT 
                        IIE.ID_INTERFACE_ELEMENTO AS ID_INTERFACE_SPLITTER,IIE.NOMBRE_INTERFACE_ELEMENTO AS NOMBRE_INTERFACE_SPLITTER,
                        IE.ID_ELEMENTO AS ID_ELEMENTO_SPLITTER,IE.NOMBRE_ELEMENTO AS NOMBRE_ELEMENTO_SPLITTER,
                        IE2.ID_ELEMENTO AS ID_ELEMENTO_OLT,IE2.NOMBRE_ELEMENTO AS NOMBRE_ELEMENTO_OLT,
                        AME.NOMBRE_MODELO_ELEMENTO,IIE2.ID_INTERFACE_ELEMENTO AS ID_INTERFACE_ELEMENTO_OLT,
                        IIE2.NOMBRE_INTERFACE_ELEMENTO AS NOMBRE_INTERFACE_OLT,II.IP AS IP_OLT
                        FROM DB_COMERCIAL.INFO_SERVICIO_TECNICO IST
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IIE ON IIE.ID_INTERFACE_ELEMENTO=IST.INTERFACE_ELEMENTO_CONECTOR_ID
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO IE ON IE.ID_ELEMENTO=IST.ELEMENTO_CONECTOR_ID
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO IE2 ON IE2.ID_ELEMENTO=IST.ELEMENTO_ID
                        INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME ON AME.ID_MODELO_ELEMENTO=IE2.MODELO_ELEMENTO_ID
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IIE2 ON IIE2.ID_INTERFACE_ELEMENTO=IST.INTERFACE_ELEMENTO_ID
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_IP II ON II.ELEMENTO_ID=IE2.ID_ELEMENTO
                        WHERE IST.SERVICIO_ID=:idservicio AND II.ESTADO=:estado ";
    
            if(isset($intIdServicio) && !empty($intIdServicio))
            {
                $objNativeQuery->setParameter("idservicio",$intIdServicio);
            }
            
            if(isset($strEstado) && !empty($strEstado))
            {
                $objNativeQuery->setParameter("estado",    $strEstado);
            }
            
            $objResultSetMap->addScalarResult('ID_INTERFACE_SPLITTER',      'idInterfaceSplitter',      'string');
            $objResultSetMap->addScalarResult('NOMBRE_INTERFACE_SPLITTER',  'nombreInterfaceSplitter',  'string');
            $objResultSetMap->addScalarResult('ID_ELEMENTO_SPLITTER',       'idElementoSplitter',       'string');
            $objResultSetMap->addScalarResult('NOMBRE_ELEMENTO_SPLITTER',   'nombreElementoSplitter',   'string');
            $objResultSetMap->addScalarResult('ID_ELEMENTO_OLT',            'idElementoOtl',            'string');
            $objResultSetMap->addScalarResult('NOMBRE_ELEMENTO_OLT',        'nombreElementoOtl',        'string');
            $objResultSetMap->addScalarResult('NOMBRE_MODELO_ELEMENTO',     'nombreModeloOtl',          'string');
            $objResultSetMap->addScalarResult('ID_INTERFACE_ELEMENTO_OLT',  'idInterfaceOtl',           'string');
            $objResultSetMap->addScalarResult('NOMBRE_INTERFACE_OLT',       'nombreInterfaceOtl',       'string');
            $objResultSetMap->addScalarResult('IP_OLT',                     'ipOtl',                    'string');

            $objNativeQuery->setSQL($strSql);
            $arrayResultado = $objNativeQuery->getOneOrNullResult();    
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }
    
   /**
     *
     * Método encargado de retornar la información de una tarea abierta.
     *
     * Costo 10
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 26-07-2018
     *
     * @param Array $arrayParametros [
     *                                  intIdDetalle      : Id del asignado,
     *                                  arrayEstadosTarea : Estado de las tareas que no deben ser filtradas,
     *                               ]
     * @return Array
     */
    public function getTareaAbierta($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = '';

            if(isset($arrayParametros['intIdDetalle']) && !empty($arrayParametros['intIdDetalle']))
            {
                $strWhere .= 'AND DET.ID_DETALLE = :intIdDetalle ';
                $objNativeQuery->setParameter("intIdDetalle", $arrayParametros['intIdDetalle']);
            }

            if(isset($arrayParametros['arrayEstadosTarea']) && !empty($arrayParametros['arrayEstadosTarea']))
            {
                $strWhere .= 'AND DH.ESTADO NOT IN (:arrayEstadosTarea) ';
                $objNativeQuery->setParameter("arrayEstadosTarea", $arrayParametros['arrayEstadosTarea']);
            }

            $strSql = "SELECT "
                       . "COM.ID_COMUNICACION ID_COMUNICACION, "
                       . "DET.ID_DETALLE      ID_DETALLE, "
                       . "DH.ESTADO           ESTADOTAREA "
                     . "FROM "
                        . "DB_SOPORTE.INFO_DETALLE            DET, "
                        . "DB_SOPORTE.INFO_DETALLE_ASIGNACION DA, "
                        . "DB_SOPORTE.INFO_DETALLE_HISTORIAL  DH, "
                        . "DB_SOPORTE.INFO_COMUNICACION       COM "
                    . "WHERE DET.ID_DETALLE = DA.DETALLE_ID "
                      . "AND DET.ID_DETALLE = DH.DETALLE_ID "
                      . "AND DET.ID_DETALLE = COM.DETALLE_ID "
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

            $arrayResultado['status'] = 'ok';
            $arrayResultado['result'] = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo InfoDetalleAsignacionRepository.getTareaAbierta -> ".$objException->getMessage());
            $arrayResultado = array();
            $arrayResultado["status"]      = 'fail';
            $arrayResultado["descripcion"] = $objException->getMessage();
        }
        return $arrayResultado;
    }

    /**
     *
     * Método encargado de obtener el detalleId de las tareas asignadas a una persona.
     *
     * Costo 1752
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 04-11-2020
     *
     * @param Array $arrayParametros [personaEmpresaRolId]
     * @return Array
     */
    public function getDetalleAsignacionByPersonaEmpresaRol($arrayParametros)
    {
        try
        {
            $arrayDiasTareas    = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                        '', 
                        '', 
                        '', 
                        'MAX_DIAS_TAREAS_LIDER_CUADRILLA', 
                        '', 
                        '', 
                        ''
                    );

            if(is_array($arrayDiasTareas))
            {
                $intDiasTareas = !empty($arrayDiasTareas['valor2']) ? intval($arrayDiasTareas['valor2']) : 0;
            }

            $arrayResultado     = array();
            $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT "
                       . "DISTINCT(IDA.DETALLE_ID) "
                     . "FROM "
                        . "DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA "
                    . "WHERE IDA.PERSONA_EMPRESA_ROL_ID = :personaEmpresaRolId "
                      . "AND TO_CHAR(IDA.FE_CREACION, 'rrrr-mm-dd') >= TO_CHAR((SYSDATE - :diasTareas), 'rrrr-mm-dd') ";

            $objNativeQuery->setParameter("personaEmpresaRolId", $arrayParametros['personaEmpresaRolId']);
            $objNativeQuery->setParameter("diasTareas", $intDiasTareas);

            $objResultSetMap->addScalarResult('DETALLE_ID'           , 'detalleId',             'integer');

            $objNativeQuery->setSQL($strSql);

            $arrayResultado['status'] = 'OK';
            $arrayResultado['result'] = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            $arrayResultado["status"]      = 'ERROR';
            $arrayResultado["descripcion"] = $objException->getMessage();
        }

        return $arrayResultado;
    }

     /**
     * Función que retorna si la tarea fuea asignada a cuadrilla Hal.
     *
     * @param Array $arrayParametros [
     *                                 intDetalleId : id del detalle de tarea.
     *                               ]
     *
     * @return boolean $boolAsignadoHal
     *
     * Costo query: 6
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 16-10-2021
     */
    public function isAsignadoHal($arrayParametros)
    {
        $intAsignadoHal = 0;
        $boolAsignadoHal = false;
        try
        {
            $objRsmb  = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

            $strSql   = " SELECT COUNT(A.ID_CUADRILLA) CANTIDAD ".
                         " FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION S, ".
                         "      DB_COMERCIAL.ADMI_CUADRILLA A ".
                         " WHERE  A.ID_CUADRILLA = S.ASIGNADO_ID ".
                          " AND S.DETALLE_ID  = :idDetalle ".
                          " AND A.ES_HAL = 'S' ";

            $objQuery->setParameter('idDetalle' ,  $arrayParametros['intDetalleId']);
            $objRsmb->addScalarResult('CANTIDAD' , 'cantidad' , 'integer');
            $objQuery->setSQL($strSql);

            $intAsignadoHal = $objQuery->getSingleScalarResult();
            if ($intAsignadoHal > 0)
            {
                $boolAsignadoHal = true;
            }
        }
        catch(\Exception $ex)
        {
            $boolAsignadoHal = false;
        }
        return $boolAsignadoHal;
    }
}
