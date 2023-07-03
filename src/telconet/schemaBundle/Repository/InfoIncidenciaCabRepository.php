<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoIncidenciaCabRepository extends EntityRepository
{
    
    /**
     * 
     * getTodosLosRegistrosPorEmpresa
     * Obtiene los registros de incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 1-03-2019
     * 
     * @param array $arrayParametros   
     *   [intIdEmpresa      - Código de empresa
     *   strFeEmisionDesde  - Fecha inicial
     *   strFeEmisionHasta  - Fecha final 
     *   strNumeroCaso      - Número de Caso
     *   strEstadoInci      - Estado de Incidencia
     *   strNoTicket        - Número de Ticket
     *   strIpAddressFil    - Ip de la incidencia
     *   strSubEstadoInci   - Sub Estado de la incidencia
     *   strPrioridadInci   - Prioridad de la Incidencia
     *   strEstadoGestion   - Estado de la Gestión
     *   strNotificaInci    - Estado de notificación
     *   strCategoria       - Categoria de la incidencia
     *   strCanton          - Cantón de la tarea
     *   strTipoEvento      - Tipo de evento de la incidencia ]   
     * 
     * @return array $arrayResultado
     *   [ipAddress          - Ip de la incidencia
     *   idIncidencia        - Id de la incidencia
     *   noTicket            - Número de Ticket
     *   feIncidencia        - Fecha de la Incidencia
     *   categoria           - Categoria de la Incidencia
     *   subCategoria        - SubCategoria de la Incidencia
     *   estadoIncidencia    - Estado de la Incidencia
     *   prioridad           - Prioridad de la incidencia
     *   subject             - Texto enviado por la ECUCERT
     *   casoId              - Id del caso creado para la incidencia de ECUCERT
     *   personaEmpresaRolId - Id Empresa Rol del cliente
     *   tipoUsuario         - Tipo de Usuario del cliente
     *   numeroCaso          - Número de Caso creado para la incidencia de ECUCERT
     *   loginCliente        - Login del Cliente
     *   estadoCaso          - Estado del Caso
     *   empresaId           - Id de la empresa de la tarea
     *   duracionDias        - Duración en días del tiempo transcurrido desde que se envió el ticket de incidencia
     *   idDetalleIncidencia - Id del detalle de la incidencia
     *   idDetalle           - Id del detalle de la tarea
     *   idComunicacion      - Número de la tarea
     *   seguimientoInterno  - Bandera para saber si el seguimiento es ingresado de forma interna
     *   nombretarea         - Nombre de la tarea
     *   fechaSol            - Fecha de la tarea
     *   horaSol             - Hora de la tarea
     *   idTarea             - Id del tipo de tarea
     *   estadoTarea         - Estado de la tarea
     *   estadoNotificacion  - Estado de notificación
     *   idPunto             - Id del Punto
     *   estadoIncEcucert    - Estado de Gestión de la Incidencia
     *   tipoEvento          - Tipo de evento de la Incidencia
     *   puerto              - Puerto de la ip enviada por ECUCERT
     *   ipDestino           - Ip destino enviada por ECUCERT
     *   subEstado           - Sub Estado de la Incidencia
     *   ipwan               - Ip Wan encontrada en el backend
     *   nombreEmpresa       - Nombre de la empresa que pertenece el cliente]  
     * 
     * costoQuery = 32  
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-08-2019 - Se agrega el login del cliente para el filtro
     * @since 1.0
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.2 08-04-2020 - Se agrega el ipController, puerto, tipo de usuario
     * y número de tarea para el filtro
     * @since 1.1
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.3 04-05-2020 - Se retorna el login ingresado manualmente por IPPCL2
     * @since 1.2
     * 
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.4 05-08-2022 - Se optimiza el query, por motivos que el consumo desde cert se corta la conexión por timeout, 
     *                           ya que el query demora más de 1 minuto.
     * @since 1.3
     * 
     */  
    public function getTodosLosRegistrosPorEmpresa($arrayParametros)
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $strFeEmisionDesde  = $arrayParametros['strFeEmisionDesde']; 
        $strFeEmisionHasta  = $arrayParametros['strFeEmisionHasta'];  
        $strNumeroCaso      = $arrayParametros['strNumeroCaso']; 
        $strEstadoInci      = $arrayParametros['strEstadoInci'];
        $strNoTicket        = $arrayParametros['strNoTicket'];
        $strIpAddressFil    = $arrayParametros['strIpAddressFil'];
        $strSubEstadoInci   = $arrayParametros['strSubEstadoInci'];
        $strPrioridadInci   = $arrayParametros['strPrioridadInci'];
        $strEstadoGestion   = $arrayParametros['strEstadoGestion'];
        $strNotificaInci    = $arrayParametros['strNotificaInci']; 
        $strCategoria       = $arrayParametros['strCategoria'];
        $strCanton          = $arrayParametros['strCanton'];
        $strTipoEvento      = $arrayParametros['strTipoEvento'];
        $strLogin           = $arrayParametros['strLogin'];
        $strIpControllerFil = $arrayParametros['strIpControllerFil'];
        $strNumeroTareaFil  = $arrayParametros['strNumeroTareaFil'];
        $strPuertoControl   = $arrayParametros['strPuertoControl'];
        $strIipoCliente     = $arrayParametros['strIipoCliente'];
        $boolEstadoCerrado  = true;               
        
        $strWhere           = "";
        $strWhereNotif      = "";
        
        try
        {
            
            $strSql = "SELECT * FROM 
                        (
                        SELECT 
                            ID_INCIDENCIA, NO_TICKET,FE_TICKET,CATEGORIA, SUBCATEGORIA,ESTADOINCIDENTE,PRIORIDAD, SUBJECT,
                            CASO_ID,PERSONA_EMPRESA_ROL_ID,TIPO_USUARIO,NUMERO_CASO,LOGIN,ESTADOCASO,EMPRESA_ID,
                            ROUND(DURACIONDIAS,0) AS DURACIONDIAS,ID_DETALLE_INCIDENCIA,DETALLE_ID,COMUNICACION_ID,
                            SEGUIMIENTOINTERNO, NOMBRE_TAREA, ID_DETALLE,FECHASOL,HORASOL,ID_TAREA,ESTADOTAREA,
                            NVL(ESTADONOTI,'No Notificado') AS ESTADONOTI, 
                            ID_PUNTO, IP, ESTADO_GESTION,TIPO_EVENTO,PUERTO,IP_DEST,SUB_ESTADO, IPWAN, 
                            NOMBRE_EMPRESA, JURISDICCION,LOGIN_ADICIONAL, FE_INCIDENCIA 
                            FROM                                   
                                (SELECT
                                IIC.ID_INCIDENCIA,IIC.NO_TICKET,IIC.FE_TICKET,IIC.CATEGORIA,IIC.SUBCATEGORIA,
                                IID.STATUS AS ESTADOINCIDENTE,IIC.PRIORIDAD,IIC.SUBJECT, IID.CASO_ID,IID.PERSONA_EMPRESA_ROL_ID,IID.TIPO_USUARIO,
                                (SELECT ic.NUMERO_CASO  FROM DB_SOPORTE.INFO_CASO ic WHERE ic.ID_CASO = IID.CASO_ID) AS NUMERO_CASO,
                                (SELECT IPU.LOGIN  FROM DB_COMERCIAL.INFO_SERVICIO ISER,DB_COMERCIAL.INFO_PUNTO IPU 
                                    WHERE IPU.PERSONA_EMPRESA_ROL_ID=IID.PERSONA_EMPRESA_ROL_ID AND IPU.ID_PUNTO=ISER.PUNTO_ID 
                                    AND ISER.ID_SERVICIO=IID.SERVICIO_ID) AS LOGIN,
                                (SELECT ICA.ESTADO FROM DB_SOPORTE.INFO_CASO_HISTORIAL ICA 
                                    WHERE ICA.ID_CASO_HISTORIAL = 
                                    (SELECT MAX(ICA1.ID_CASO_HISTORIAL)
                                        FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID1 
                                        INNER JOIN  DB_SOPORTE.INFO_CASO_HISTORIAL ICA1 
                                        ON IID1.CASO_ID = ICA1.CASO_ID
                                        WHERE IID1.CASO_ID = IID.CASO_ID
                                )) ESTADOCASO,
                                IID.EMPRESA_ID,
                                ROUND((TRUNC(IIC.FE_TICKET) -SYSDATE),2)*-1 AS DURACIONDIAS,
                                IID.ID_DETALLE_INCIDENCIA,
                                (SELECT ICO.DETALLE_ID FROM DB_COMUNICACION.INFO_COMUNICACION ICO 
                                    WHERE IID.COMUNICACION_ID=ICO.ID_COMUNICACION)  DETALLE_ID, 
                                IID.COMUNICACION_ID,'N' AS SEGUIMIENTOINTERNO,
                                (SELECT ATA.NOMBRE_TAREA FROM DB_SOPORTE.ADMI_TAREA ATA 
                                    WHERE ATA.ID_TAREA = (SELECT IDE.TAREA_ID FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                                                                WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                                                                AND IDE.ID_DETALLE=ICO1.DETALLE_ID) 
                                )NOMBRE_TAREA,
                                (SELECT IDE.ID_DETALLE FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                    WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                    AND IDE.ID_DETALLE=ICO1.DETALLE_ID) ID_DETALLE,
                                (SELECT TO_CHAR(IDE.FE_SOLICITADA,'YYYY/MM/DD')  
                                    FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                    WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                    AND IDE.ID_DETALLE=ICO1.DETALLE_ID) FECHASOL,
                                (SELECT TO_CHAR(IDE.FE_SOLICITADA,'HH:MI:SS')  FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                    WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                    AND IDE.ID_DETALLE=ICO1.DETALLE_ID) HORASOL,
                                (SELECT ATA.ID_TAREA  FROM DB_SOPORTE.ADMI_TAREA ATA 
                                    WHERE ATA.ID_TAREA = (SELECT IDE.TAREA_ID FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                                                                WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                                                                AND IDE.ID_DETALLE=ICO1.DETALLE_ID) 
                                )ID_TAREA,
                                (SELECT IDH.ESTADO FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH 
                                            WHERE IDH.ID_DETALLE_HISTORIAL = (SELECT MAX(IDH.ID_DETALLE_HISTORIAL) AS DETALLE_HIS_ID 
                                                                    FROM DB_SOPORTE.INFO_INCIDENCIA_DET IID2  
                                                                    INNER JOIN DB_SOPORTE.INFO_COMUNICACION ICO 
                                                                    ON IID2.COMUNICACION_ID=ICO.ID_COMUNICACION
                                                                    INNER JOIN DB_SOPORTE.INFO_DETALLE_HISTORIAL IDH 
                                                                    ON IDH.DETALLE_ID=ICO.DETALLE_ID
                                                                    WHERE IDH.DETALLE_ID IN (SELECT IDE.ID_DETALLE 
                                                                            FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                                                            WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                                                            AND IDE.ID_DETALLE=ICO1.DETALLE_ID)
                                                                )) ESTADOTAREA,
                                (SELECT IINO.ESTADO  FROM DB_SOPORTE.INFO_INCIDENCIA_NOTIF IINO 
                                    WHERE IINO.ID_INCIDENCIA_NOTIFICACION =
                                    (SELECT max(IINO1.ID_INCIDENCIA_NOTIFICACION) FROM DB_SOPORTE.INFO_INCIDENCIA_NOTIF IINO1 
                                        where IINO1.DETALLE_INCIDENCIA_ID = IID.ID_DETALLE_INCIDENCIA)) ESTADONOTI,
                                (SELECT IPU.ID_PUNTO  FROM DB_COMERCIAL.INFO_SERVICIO ISER,DB_COMERCIAL.INFO_PUNTO IPU 
                                    WHERE IPU.PERSONA_EMPRESA_ROL_ID=IID.PERSONA_EMPRESA_ROL_ID AND IPU.ID_PUNTO=ISER.PUNTO_ID 
                                    AND ISER.ID_SERVICIO=IID.SERVICIO_ID) AS ID_PUNTO,
                                IID.IP,IID.ESTADO_GESTION,IIC.TIPO_EVENTO,IID.PUERTO,IID.IP_DEST,IID.SUB_ESTADO,IID.IPWAN,
                                (SELECT IEG.NOMBRE_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG 
                                        WHERE IEG.COD_EMPRESA = IID.EMPRESA_ID) NOMBRE_EMPRESA,
                                (SELECT AC.JURISDICCION
                                        FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA,DB_GENERAL.ADMI_CANTON AC 
                                        WHERE (DETALLE_ID,ID_DETALLE_ASIGNACION) IN (
                                                SELECT IDA.DETALLE_ID,MAX(IDA.ID_DETALLE_ASIGNACION)
                                                FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA
                                                GROUP BY IDA.DETALLE_ID)
                                        AND IDA.DETALLE_ID IN (SELECT IDE.ID_DETALLE 
                                                    FROM DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                                    WHERE IID.COMUNICACION_ID=ICO1.ID_COMUNICACION 
                                                    AND IDE.ID_DETALLE=ICO1.DETALLE_ID)
                                        AND IDA.CANTON_ID = AC.ID_CANTON ) JURISDICCION,
                                IID.LOGIN_ADICIONAL,IID.FE_INCIDENCIA		
                            FROM DB_SOPORTE.INFO_INCIDENCIA_CAB IIC
                            INNER JOIN DB_SOPORTE.INFO_INCIDENCIA_DET IID 
                            ON IIC.ID_INCIDENCIA = IID.INCIDENCIA_ID  
                            WHERE 1= 1";

            if(isset($strNoTicket) && !empty($strNoTicket))
            {
                $strWhere .= " AND IIC.NO_TICKET = :intNoTicket ";
                $objQuery->setParameter("intNoTicket", $strNoTicket);
                $boolEstadoCerrado  = false;
            }

            if(isset($strIpAddressFil) && !empty($strIpAddressFil))
            {
                $strWhere .= " AND IID.IP = :intIp ";
                $objQuery->setParameter("intIp", $strIpAddressFil);
                $boolEstadoCerrado  = false;
            }

            if(isset($intIdEmpresa) && !empty($intIdEmpresa))
            {
                $strWhere .= " AND NVL(IID.EMPRESA_ID,:intIdEmpresa) = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }

            if(isset($strSubEstadoInci) && !empty($strSubEstadoInci))
            {
                $strWhere .= " AND IID.SUB_ESTADO = :strSubEstadoInci ";
                $objQuery->setParameter("strSubEstadoInci", $strSubEstadoInci);
            }

            if(isset($strPrioridadInci) && !empty($strPrioridadInci))
            {
                $strWhere .= " AND IIC.PRIORIDAD = :strPrioridadInci ";
                $objQuery->setParameter("strPrioridadInci", $strPrioridadInci);
            }

            if(isset($strEstadoGestion) && !empty($strEstadoGestion))
            {
                $strWhere .= " AND IID.ESTADO_GESTION = :strEstadoGestion ";
                $objQuery->setParameter("strEstadoGestion", $strEstadoGestion);
            }

            if(isset($strFeEmisionDesde) && !empty($strFeEmisionDesde) && isset($strFeEmisionHasta) && !empty($strFeEmisionHasta))
            {
                $strFeEmisionDesdeDate  = date("Y/m/d", strtotime($strFeEmisionDesde));
                $strFeEmisionDesdeDate  = $strFeEmisionDesdeDate." 00:00:00";
                $strFeEmisionHastaDate  = date("Y/m/d", strtotime($strFeEmisionHasta)); 
                $strFeEmisionHastaDate  = $strFeEmisionHastaDate." 23:59:59";
                $strWhere .= " AND IIC.FE_TICKET BETWEEN :fechaDesdeInci AND :fechaHastaInci ";
                $objQuery->setParameter("fechaDesdeInci", $strFeEmisionDesdeDate);
                $objQuery->setParameter("fechaHastaInci", $strFeEmisionHastaDate);
            }

            if(isset($strNumeroCaso) && !empty($strNumeroCaso))
            {
                $strWhere .= " AND IID.CASO_ID in (SELECT ic.ID_CASO  FROM DB_SOPORTE.INFO_CASO ic 
                                                    WHERE ic.ID_CASO = IID.CASO_ID AND ic.NUMERO_CASO = :numeroCaso)";
                $objQuery->setParameter("numeroCaso", $strNumeroCaso);
            }

            if(isset($strEstadoInci) && !empty($strEstadoInci))
            {
                $strWhere .= " AND IID.STATUS = :estadoInci ";
                $objQuery->setParameter("estadoInci", $strEstadoInci);
            }

            if(isset($strCategoria) && !empty($strCategoria))
            {
                $strWhere .= " AND IIC.CATEGORIA = :categoriaInc ";
                $objQuery->setParameter("categoriaInc", $strCategoria);
            }

            if(isset($strCanton) && !empty($strCanton))
            {
                $strWhere .= " AND IID.COMUNICACION_ID in (SELECT ICO1.ID_COMUNICACION
                                    FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA,DB_GENERAL.ADMI_CANTON AC,
                                        DB_COMUNICACION.INFO_COMUNICACION ICO1,DB_SOPORTE.INFO_DETALLE IDE
                                    WHERE (IDA.DETALLE_ID,IDA.ID_DETALLE_ASIGNACION) IN (
                                            SELECT IDA1.DETALLE_ID,MAX(IDA1.ID_DETALLE_ASIGNACION)
                                            FROM DB_SOPORTE.INFO_DETALLE_ASIGNACION IDA1
                                            GROUP BY IDA1.DETALLE_ID)
                                    AND IID.COMUNICACION_ID=ICO1.ID_COMUNICACION
                                    AND IDA.DETALLE_ID = IDE.ID_DETALLE
                                    AND IDE.ID_DETALLE=ICO1.DETALLE_ID
                                    AND IDA.CANTON_ID = AC.ID_CANTON 
                                    AND AC.JURISDICCION = :cantonInc ) ";
                $objQuery->setParameter("cantonInc", $strCanton);
            }

            if(isset($strTipoEvento) && !empty($strTipoEvento))
            {
                $strWhere .= " AND IIC.TIPO_EVENTO = :tipoEvento ";
                $objQuery->setParameter("tipoEvento", $strTipoEvento);
            }

            if(isset($strIpControllerFil) && !empty($strIpControllerFil))
            {
                $strWhere .= " AND IID.IP_DEST = :strIpDestino ";
                $objQuery->setParameter("strIpDestino", $strIpControllerFil);
            }

            if(isset($strNumeroTareaFil) && !empty($strNumeroTareaFil))
            {
                $strWhere .= " AND IID.COMUNICACION_ID = :strNumeroTarea ";
                $objQuery->setParameter("strNumeroTarea", $strNumeroTareaFil);
            }

            if(isset($strPuertoControl) && !empty($strPuertoControl))
            {
                $strWhere .= " AND IID.PUERTO = :strPuerto ";
                $objQuery->setParameter("strPuerto", $strPuertoControl);
            }

            if(isset($strIipoCliente) && !empty($strIipoCliente))
            {
                $strWhere .= " AND IID.TIPO_USUARIO = :strTipocliente ";
                $objQuery->setParameter("strTipocliente", $strIipoCliente);
            }

            if(isset($strLogin) && !empty($strLogin))
            {
                $strWhere .= " AND IID.PERSONA_EMPRESA_ROL_ID in (SELECT IPU.PERSONA_EMPRESA_ROL_ID  
                                FROM DB_COMERCIAL.INFO_SERVICIO ISER,DB_COMERCIAL.INFO_PUNTO IPU 
                                WHERE IPU.PERSONA_EMPRESA_ROL_ID=IID.PERSONA_EMPRESA_ROL_ID 
                                AND IPU.ID_PUNTO=ISER.PUNTO_ID AND ISER.ID_SERVICIO=IID.SERVICIO_ID
                                AND IPU.LOGIN = :strLogin )";
                $objQuery->setParameter("strLogin", $strLogin);
                $boolEstadoCerrado  = false;
            }

            if($boolEstadoCerrado)
            {
                $strWhere .= " ) T WHERE (NVL(ESTADOTAREA,'NN') != 'Finalizada' OR NVL(ESTADOCASO,'NN') != 'Cerrado') ";
                $objQuery->setParameter("estadoTarea", 'Finalizada');
                $objQuery->setParameter("estadoCaso" , 'Cerrado');
            }else
            {
                $strWhere .= " ) T ";
            }

            $strSql    .= $strWhere." )";

            if(isset($strNotificaInci) && !empty($strNotificaInci))
            {
                $strWhereNotif .= " WHERE ESTADONOTI = :strNotificaInci ORDER BY ID_DETALLE_INCIDENCIA ";
                $objQuery->setParameter("strNotificaInci", $strNotificaInci);
                $strSql     .= $strWhereNotif;
            }
            else
            {
                $strSql     .= " ORDER BY ID_DETALLE_INCIDENCIA ";
            }
            
            $objRsm->addScalarResult('IP',                      'ipAddress',            'string');
            $objRsm->addScalarResult('ID_INCIDENCIA',           'idIncidencia',         'integer');
            $objRsm->addScalarResult('NO_TICKET',               'noTicket',             'string');
            $objRsm->addScalarResult('FE_TICKET',               'feIncidencia',         'string');
            $objRsm->addScalarResult('CATEGORIA',               'categoria',            'string');
            $objRsm->addScalarResult('SUBCATEGORIA',            'subCategoria',         'string');
            $objRsm->addScalarResult('ESTADOINCIDENTE',         'estadoIncidencia',     'string');
            $objRsm->addScalarResult('PRIORIDAD',               'prioridad',            'string');
            $objRsm->addScalarResult('SUBJECT',                 'subject',              'string');
            $objRsm->addScalarResult('CASO_ID',                 'casoId',               'string');
            $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID',  'personaEmpresaRolId',  'string');
            $objRsm->addScalarResult('TIPO_USUARIO',            'tipoUsuario',          'string');
            $objRsm->addScalarResult('NUMERO_CASO',             'numeroCaso',           'string');
            $objRsm->addScalarResult('LOGIN',                   'loginCliente',         'string');
            $objRsm->addScalarResult('ESTADOCASO',              'estadoCaso',           'string');
            $objRsm->addScalarResult('EMPRESA_ID',              'empresaId',            'string');
            $objRsm->addScalarResult('DURACIONDIAS',            'duracionDias',         'string');
            $objRsm->addScalarResult('ID_DETALLE_INCIDENCIA',   'idDetalleIncidencia',  'string');  
            $objRsm->addScalarResult('ID_DETALLE',              'idDetalle',            'string'); 
            $objRsm->addScalarResult('COMUNICACION_ID',         'idComunicacion',       'string'); 
            $objRsm->addScalarResult('SEGUIMIENTOINTERNO',      'seguimientoInterno',   'string');
            $objRsm->addScalarResult('NOMBRE_TAREA',            'nombretarea',          'string');
            $objRsm->addScalarResult('FECHASOL',                'fechaSol',             'string');
            $objRsm->addScalarResult('HORASOL',                 'horaSol',              'string');
            $objRsm->addScalarResult('ID_TAREA',                'idTarea',              'string'); 
            $objRsm->addScalarResult('ESTADOTAREA',             'estadoTarea',          'string');
            $objRsm->addScalarResult('ESTADONOTI',              'estadoNotificacion',   'string');
            $objRsm->addScalarResult('ID_PUNTO',                'idPunto',              'string');
            $objRsm->addScalarResult('ESTADO_GESTION',          'estadoIncEcucert',     'string');
            $objRsm->addScalarResult('TIPO_EVENTO',             'tipoEvento',           'string');
            $objRsm->addScalarResult('PUERTO',                  'puerto',               'string');
            $objRsm->addScalarResult('IP_DEST',                 'ipDestino',            'string');
            $objRsm->addScalarResult('SUB_ESTADO',              'subEstado',            'string');
            $objRsm->addScalarResult('IPWAN',                   'ipwan',                'string');
            $objRsm->addScalarResult('NOMBRE_EMPRESA',          'nombreEmpresa',        'string');
            $objRsm->addScalarResult('JURISDICCION',            'jurisdiccion',         'string');
            $objRsm->addScalarResult('LOGIN_ADICIONAL',         'loginAdicional',       'string');
            $objRsm->addScalarResult('FE_INCIDENCIA',           'fechaIpReportada',     'string');

            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            $arrayResult['error'] = $e->getMessage();
        }
        
        return $arrayResult;
    }  
    
    /**
     * 
     * getTodasPrioridadesIncidencia
     * Obtiene los registros con las prioridades de las incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 29-04-2019  
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   prioridadIncidencia - Prioridad de la Incidencia
     * ]
     *
     * costoQuery = 3  
     *
     */  
    public function getTodasPrioridadesIncidencia()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IIC.PRIORIDAD
                    FROM DB_SOPORTE.INFO_INCIDENCIA_CAB IIC
                    GROUP BY IIC.PRIORIDAD ";
        
        $objRsm->addScalarResult('PRIORIDAD',   'prioridadIncidencia',         'string');        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();
    }
    
    /**
     * 
     * getCorreosClientesNotifInc
     * Obtiene los registros de correo de los cliente notificados por una incidencia ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 1-03-2019 
     *
     * @param array $arrayParametros [
     *   idDetalleIncidencia - Id del detalle de la incidencia]
     * 
     * @return array $arrayResultado[
     *   correo - Correo del cliente que se notificó,
     *   fecha  - Fecha de creación
     * ]
     *
     * costoQuery = 2   
     *
     */  
    public function getCorreosClientesNotifInc($arrayParametros)
    { 
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);
        $intIdDetIncidencia = $arrayParametros['intIncidenciaDetId'];
        
        $strSql = " SELECT IIN.CORREO,IIN.FE_CREACION
                    FROM DB_SOPORTE.INFO_INCIDENCIA_NOTIF IIN
                    WHERE IIN.DETALLE_INCIDENCIA_ID = :detalleIncidenciaId ";
        
        $objQuery->setParameter("detalleIncidenciaId", $intIdDetIncidencia);
        
        
        $objRsm->addScalarResult('CORREO',      'correo',         'string');      
        $objRsm->addScalarResult('FE_CREACION', 'fecha',         'string');  
        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();
    }  
         
    /**
     * 
     * getRegistrosDatosPorTicket
     * Obtiene el números de registros ingresados y porcentaje de avance por Ticket
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 1-03-2019
     * 
     * @param array $arrayParametros
     * [noTicket - Número de Ticket]  
     * 
     * @return array $arrayResultados
     * [numeroRegistros      - Números de totales
     *  registrosbase        - Números de Registros encontrados
     *  porcentajeRealizado  - Porcentaje de registros ingresados]
     *
     * costoQuery = 3
     */  
    public function getRegistrosDatosPorTicket($arrayParametros)
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strNoTicket  = $arrayParametros['noTicket'];   
        
        $strWhere           = "";
        
        $strSql = " SELECT IIC.NUMERO_REGISTROS, T1.REGISTROS_BASE, (T1.REGISTROS_BASE/NUMERO_REGISTROS)*100 AS PORCENTAJE_REALIZADO
                    FROM DB_SOPORTE.INFO_INCIDENCIA_CAB IIC 
                    INNER JOIN (SELECT COUNT(ID_DETALLE_INCIDENCIA) AS REGISTROS_BASE, INCIDENCIA_ID
                                FROM DB_SOPORTE.INFO_INCIDENCIA_DET 
                                GROUP BY INCIDENCIA_ID) T1 ON T1.INCIDENCIA_ID = IIC.ID_INCIDENCIA
                    WHERE IIC.NO_TICKET= :NoTicket
                    ";

        $objQuery->setParameter("NoTicket", $strNoTicket);
        
        $strSql    .= $strWhere;
 
        $objRsm->addScalarResult('NUMERO_REGISTROS',        'numeroRegistros',      'string');
        $objRsm->addScalarResult('REGISTROS_BASE',          'registrosbase',        'string');
        $objRsm->addScalarResult('PORCENTAJE_REALIZADO',    'porcentajeRealizado',  'string');
                
        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }
    
    /**
     * 
     * getTodasCategoriasIncidencia
     * Obtiene los registros con las categorías de las incidencias enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 23-05-2021 - Se cambia la tabla donde se obtiene la categoría, se agregan filtros
     *                           de empresa y de tipo de evento
     * @since 1.0
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   strNombreParam  - Nombre del parámetro
     *   strDescripParam - Descripción del parámetro
     *   strEstado       - Estado del parámetro
     * ]
     * 
     * costoQuery = 7    
     * 
     */  
    public function getTodasCategoriasIncidencia($arrayParametros)
    { 
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);
        $strNombreParam     = $arrayParametros['strNombreParam'];
        $strDescripParam    = $arrayParametros['strDescripParam'];
        $strEstado          = $arrayParametros['strEstado'];
        $strTipoEvento      = $arrayParametros['strTipoEvento'];
        $intCodEmpresa      = $arrayParametros['intCodEmpresa'];
        
        $strSql = " SELECT LOWER(APD.VALOR1) AS CATEGORIA
                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
                    WHERE APC.NOMBRE_PARAMETRO= :nombreParam
                    AND APD.DESCRIPCION = :descripcionParam AND APD.ESTADO = :estado
                   ";

        if(!empty($strTipoEvento) )
        {
            $strWhereTipo .= " AND APD.VALOR3 = :strTipoEvento ";
            $objQuery->setParameter("strTipoEvento", $strTipoEvento);
            $strSql     .= $strWhereTipo;
        }

        if(!empty($intCodEmpresa) )
        {
            $strWhereEmpresa .= " AND APD.EMPRESA_COD = :intCodEmpresa ";
            $objQuery->setParameter("intCodEmpresa", $intCodEmpresa);
            $strSql     .= $strWhereEmpresa;
        }

        $strSql .=  " GROUP BY LOWER(APD.VALOR1)
                      ORDER BY LOWER(APD.VALOR1) ASC
                    ";

        $objQuery->setParameter("nombreParam", $strNombreParam);
        $objQuery->setParameter("descripcionParam", $strDescripParam);
        $objQuery->setParameter("estado", $strEstado);

        $objRsm->addScalarResult('CATEGORIA',   'categoria',         'string');        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();
    }
    
    /**
     * 
     * getTiposdeEvento
     * Obtiene los registros con los tipos de eventos enviadas por ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 10-07-2019    
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   tipoEvento - Tipo de evento de la Incidencia
     * ]
     *
     * costoQuery = 3 
     *
     */  
    public function getTiposdeEvento()
    { 
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT IIC.TIPO_EVENTO
                    FROM DB_SOPORTE.INFO_INCIDENCIA_CAB IIC
                    GROUP BY IIC.TIPO_EVENTO ";
        
        $objRsm->addScalarResult('TIPO_EVENTO',   'tipoEvento',         'string');        
        
        $objQuery->setSQL($strSql);
   
        return $objQuery->getResult();
    }
    
    /**
     * 
     * getTodosSeguimientosECUCERT
     * Obtiene los registros con los seguimientos para el reporte ECUCERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 23-08-2019    
     * 
     * @param array $arrayParametros null    
     * 
     * @return array $arrayResultado[
     *   seguimientos - Seguimiento que tomará par el reporte de ECUCERT
     * ]
     *
     * costoQuery = 3 
     *
     */  
    public function getTodosSeguimientosECUCERT()
    { 
        $arrayRespuesta = array();
        $strEstado      = "A";
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSql = " SELECT ASE.SEGUIMIENTO
                        FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT ASE 
                        WHERE ASE.ESTADO = :estado  ";

            $objQuery->setParameter("estado", $strEstado);
            
            $objRsm->addScalarResult('SEGUIMIENTO',   'seguimientos',         'string');        
            
            $objQuery->setSQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            error_log("Ha ocurrido un error en getTodosSeguimientosECUCERT: " . $ex->getMessage());
        }
   
        return $arrayRespuesta;
    }
    
     /**
     * 
     * Método encargado de llamar al paquete SPKG_INCIDENCIA_ECUCERT y al 
     * procedimiento P_IDENTIFICA_IP
     * 
     * 
     * @author Otto Navas Collao <onavas@telconet.ec>
     * @version 1.0 19-11-2019
     * 
     * @author Néstor Naula <Nestor Naula>
     * @version 1.1 02-09-202 - Se cambia de la conexión a Oci para retorno de Json (clob)
     * @since 1.0
     * 
     * @return arreglo(json)
     */
    public function getIdentifyClient($arrayParametros)
    {
        $strMsjError      = str_repeat('a',  30*1024);
        $arrayRespuesta   = str_repeat('a',  30*1024);
        $strUser          = $arrayParametros['strUser'];
        $strIP            = $arrayParametros['strIP'];
        $strTimeStamp     = $arrayParametros['strTimeStamp'];
        $strOriginID      = $arrayParametros['strOriginID'];
        $strUserSoporte   = $arrayParametros['strUserSoporte'];
        $strPassSoporte   = $arrayParametros['strPassSoporte'];
        $strDnsSoporte    = $arrayParametros['strDnsSoporte'];

        try
        {   
            if(!empty($strIP) && !empty($strOriginID) && !empty($strUser))
            {

                $strSql  = "BEGIN DB_SOPORTE.SPKG_INCIDENCIA_ECUCERT.P_IDENTIFICA_IP(
                            :Pv_IpRequest, 
                            :Pv_originID, 
                            :Pv_TimeStamp,
                            :Pv_user,
                            :Pc_JsonResponse,
                            :Pv_ErrorMsj); END;";

                $objConn  = oci_connect($strUserSoporte,
                                        $strPassSoporte,
                                        $strDnsSoporte);

                $objStmt          = oci_parse($objConn, $strSql);
                $strJsonClobResp  = oci_new_descriptor($objConn,OCI_D_LOB);
                $strJsonClobResp->writetemporary($strRespuesta);

                $strJsonClobError = oci_new_descriptor($objConn);
                $strJsonClobError->writetemporary($strMsjError);

                oci_bind_by_name($objStmt, ':Pv_IpRequest', $strIP);
                oci_bind_by_name($objStmt, ':Pv_originID', $strOriginID);
                oci_bind_by_name($objStmt, ':Pv_TimeStamp', $strTimeStamp);
                oci_bind_by_name($objStmt, ':Pv_User', $strUser);
                oci_bind_by_name($objStmt, ':Pv_ErrorMsj', $strMsjError);
                oci_bind_by_name($objStmt, ':Pc_JsonResponse', $strJsonClobResp,-1, OCI_B_CLOB);

                oci_execute($objStmt);
                $strErrorOci = oci_error($objStmt);

                if (strpos($strMsjError, 'Error') === false && $strErrorOci==null 
                && is_object($strJsonClobResp)) 
                {
                    $arrayRespuesta = json_decode($strJsonClobResp->load(),true);
                }
                else
                {
                    if (strpos($strMsjError, str_repeat('a',4)) === false)
                    {
                        $arrayRespuesta = json_decode($strMsjError,true);
                    }
                    else
                    {
                        $arrayRespuesta = $strErrorOci['message'];
                    }                
                }
                oci_free_statement($objStmt);
                oci_close($objConn);
            } 
            else
            {
                $arrayRespuesta = json_decode("{ \"mensaje\":\"No se logró realizar la consulta debido a uno de los datos de la petición \","
                                            . "\"status\":\"ERROR\" ,\"data\":[]}",true);                
            }            
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = $objException->getMessage();
        }
       return $arrayRespuesta;
    }

    /**
     * Obtiene la secuencia de un ticket interno
     * 
     * @author Carlos Pérez <cjperez@telconet.ec>
     * @version 1.0 02-06-2021
     * 
     * @return int $idSecuenciaTicket
     **/
    public function getSecuenciaTicket() 
    {
        $intSecuenciaTicket = null;
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objRsm->addScalarResult('SECUENCIA', 'secuenciaValor', 'string');
        $objQuery           = $this->_em->createNativeQuery("SELECT SEQ_INFO_TICKETS_ECUCERT.NEXTVAL as SECUENCIA FROM DUAL", $objRsm);
        $arrayDatos         = $objQuery->getScalarResult();
        $intSecuenciaTicket = $arrayDatos[0]['secuenciaValor'];
        return $intSecuenciaTicket;
    }
}
