--SCRIPT PARA REGUALIZAR LOS CASOS APERTURADOS
DECLARE

    --CURSOR PARA OBTENER LOS CASOS APERTURADOR
    CURSOR C_CasosAperturados IS
        SELECT ICA.ID_CASO,
               ICA.FE_APERTURA
            FROM DB_SOPORTE.INFO_CASO ICA,
                 DB_SOPORTE.INFO_CASO_HISTORIAL ICAHIS
        WHERE ICA.ID_CASO = ICAHIS.CASO_ID
          AND ICA.EMPRESA_COD = '18'
          AND ICAHIS.ID_CASO_HISTORIAL =
                (SELECT MAX(MAXICAHIS.ID_CASO_HISTORIAL)
                    FROM DB_SOPORTE.INFO_CASO_HISTORIAL MAXICAHIS
                 WHERE MAXICAHIS.CASO_ID = ICA.ID_CASO)
          AND ICAHIS.ESTADO IN ('Creado','Asignado')
          AND TO_CHAR(ICA.FE_APERTURA,'RRRR-MM') <= '2019-06';

    -- CURSOR PARA OBTENER LAS TAREAS ABIERTAS DE UN CASO
    CURSOR C_TareasCaso(Cn_IdCaso NUMBER) IS
        SELECT IDET.ID_DETALLE
            FROM DB_SOPORTE.INFO_CASO              ICAS,
                 DB_SOPORTE.INFO_DETALLE_HIPOTESIS IDHIP,
                 DB_SOPORTE.INFO_DETALLE           IDET,
                 DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHIS
        WHERE ICAS.ID_CASO               = Cn_IdCaso
          AND ICAS.ID_CASO               = IDHIP.CASO_ID
          AND IDHIP.ID_DETALLE_HIPOTESIS = IDET.DETALLE_HIPOTESIS_ID
          AND IDET.ID_DETALLE            = IDHIS.DETALLE_ID
          AND IDHIS.ID_DETALLE_HISTORIAL = (
            SELECT MAX(IDHISMAX.ID_DETALLE_HISTORIAL)
                FROM DB_SOPORTE.INFO_DETALLE_HISTORIAL IDHISMAX
            WHERE IDHISMAX.DETALLE_ID = IDET.ID_DETALLE)
          AND IDHIS.ESTADO NOT IN ('Finalizada','Anulada','Cancelada');

    --CURSOR PARA OBTENER EL TIEMPO ASIGNACION DEL CASO
    CURSOR C_GetCasoTiempoAsignacion(Cn_IdCaso NUMBER) IS
        SELECT COUNT(ICTASI.ID_CASO_TIEMPO_ASIGNACION)
            FROM DB_SOPORTE.INFO_CASO_TIEMPO_ASIGNACION ICTASI
        WHERE ICTASI.CASO_ID = Cn_IdCaso;

    --VARIABLES LOCALES
    Ld_FechaActual             DATE;
    Ld_FechaAperturaCaso       DATE;
    Ln_TiempoTotalCaso         NUMBER;
    Ln_TiempoIncidencia        NUMBER;
    Ln_TiempoCliente           NUMBER;
    Ln_TiempoEmpresa           NUMBER;
    Ln_TiempoSolucion          NUMBER;
    Ln_Cantidad                NUMBER;
    Ln_TotalOk                 NUMBER := 0;
    Ln_TotalFail               NUMBER := 0;

BEGIN

    IF C_CasosAperturados%ISOPEN THEN
        CLOSE C_CasosAperturados;
    END IF;

    IF C_TareasCaso%ISOPEN THEN
        CLOSE C_TareasCaso;
    END IF;

    IF C_GetCasoTiempoAsignacion%ISOPEN THEN
        CLOSE C_GetCasoTiempoAsignacion;
    END IF;

    --FOR PARA RECORRER TODO LOS CASOS APERTURADOS
    FOR CASOS IN C_CasosAperturados LOOP

        BEGIN

            Ld_FechaAperturaCaso := CASOS.FE_APERTURA;
            Ld_FechaActual       := SYSDATE;

            --FOR PARA RECORRER LAS TAREAS ABIERTAS DE UN CASO.
            FOR TAREAS IN C_TareasCaso(CASOS.ID_CASO) LOOP

                INSERT INTO DB_SOPORTE.INFO_DETALLE_HISTORIAL (
                    ID_DETALLE_HISTORIAL,
                    DETALLE_ID,
                    OBSERVACION,
                    ESTADO,
                    USR_CREACION,
                    FE_CREACION,
                    IP_CREACION,
                    ACCION
                ) VALUES (
                    DB_SOPORTE.SEQ_INFO_DETALLE_HISTORIAL.NEXTVAL,
                    TAREAS.ID_DETALLE,
                    'Tarea cancelada automaticamente por Telcos.',
                    'Cancelada',
                    'telcos',
                     Ld_FechaActual,
                    '127.0.0.1',
                    'Cancelada'
                );

            END LOOP;

            --TIEMPO DEL CIERRE DEL CASO
            Ln_TiempoIncidencia := 0;
            Ln_TiempoCliente    := 0;
            Ln_TiempoEmpresa    := 0;
            Ln_TiempoSolucion   := 0;
            Ln_TiempoTotalCaso  := TRUNC( (Ld_FechaActual - Ld_FechaAperturaCaso) * 24 * 60);

            --VERIFICAMOS SI YA SE ENCUENTRA REGISTRADO EL TIEMPO DE ASIGNACION DEL CASO.
            OPEN C_GetCasoTiempoAsignacion(CASOS.ID_CASO);
                FETCH C_GetCasoTiempoAsignacion INTO Ln_Cantidad;
            CLOSE C_GetCasoTiempoAsignacion;

            IF Ln_Cantidad < 1 THEN

                Ln_Cantidad := 0;

                INSERT INTO DB_SOPORTE.INFO_CASO_TIEMPO_ASIGNACION (
                    ID_CASO_TIEMPO_ASIGNACION,
                    CASO_ID,
                    TIEMPO_TOTAL_CASO,
                    TIEMPO_CLIENTE_ASIGNADO,
                    TIEMPO_EMPRESA_ASIGNADO,
                    USR_CREACION,
                    FE_CREACION,
                    TIEMPO_TOTAL_CASO_SOLUCION,
                    TIEMPO_TOTAL
                ) VALUES (
                    DB_SOPORTE.SEQ_INFO_CASO_TIEMPO_ASIG.NEXTVAL,
                    CASOS.ID_CASO,
                    Ln_TiempoIncidencia,
                    Ln_TiempoCliente,
                    Ln_TiempoEmpresa,
                    'telcos',
                    Ld_FechaActual,
                    Ln_TiempoSolucion,
                    Ln_TiempoTotalCaso
                );

            END IF;

            --CIERRE DEL CASO
            UPDATE DB_SOPORTE.INFO_CASO
                SET TITULO_FIN      = 'Caso duplicado/mal aperturado',
                    VERSION_FIN     = 'Se procede con el cierre por regularización de datos',
                    FE_CIERRE       = Ld_FechaActual,
                    TIPO_AFECTACION = 'SINAFECTACION'
            WHERE ID_CASO = CASOS.ID_CASO;

            --INSERTAMOS EL HISTORIAL DEL CIERRE DEL CASO
            INSERT INTO DB_SOPORTE.INFO_CASO_HISTORIAL (
                ID_CASO_HISTORIAL,
                CASO_ID,
                OBSERVACION,
                ESTADO,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION
            ) VALUES (
                DB_SOPORTE.SEQ_INFO_CASO_HISTORIAL.NEXTVAL,
                CASOS.ID_CASO,
                'Cierre del caso',
                'Cerrado',
                'telcos',
                Ld_FechaActual,
                '127.0.0.1'
            );

            Ln_TotalOk := Ln_TotalOk + 1;

            COMMIT;

        EXCEPTION

            WHEN OTHERS THEN

                ROLLBACK;

                Ln_TotalFail := Ln_TotalFail + 1;

                DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('PROCESO_REGULACION',
                                                     'CIERRE_AUTOMATICO_CASO_INDIVIDUAL',
                                                     'IdCaso: '||CASOS.ID_CASO||' - Error: '||
                                                        SQLCODE || ' - ERROR_STACK: '||
                                                        DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: '||
                                                        DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                                     'telcos',
                                                      SYSDATE,
                                                     '127.0.0.1');

        END;

    END LOOP;

    UTL_MAIL.SEND(SENDER     => 'gvalenzuela@telconet.ec',
                  RECIPIENTS => 'gvalenzuela@telconet.ec',
                  SUBJECT    => 'EL PROCESO DE CIERRE AUTOMATICO DE CASOS HA FINALIZADO',
                  MESSAGE    => 'Total Procesados: '||Ln_TotalOk||' - Total Error: '||Ln_TotalFail,
                  MIME_TYPE  => 'text/html; charset=UTF-8');

EXCEPTION

    WHEN OTHERS THEN

        ROLLBACK;

        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('PROCESO_REGULACION',
                                             'CIERRE_AUTOMATICO_CASO_GENERAL',
                                             'Error: ' || SQLCODE || ' - ERROR_STACK: '||
                                                DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: '||
                                                DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                             'telcos',
                                              SYSDATE,
                                             '127.0.0.1');

        UTL_MAIL.SEND(SENDER     => 'gvalenzuela@telconet.ec',
                      RECIPIENTS => 'gvalenzuela@telconet.ec',
                      SUBJECT    => 'EL PROCESO DE CIERRE AUTOMATICO DE CASOS HA FALLADO',
                      MESSAGE    => 'REVISAR LOS LOG DE ERROR',
                      MIME_TYPE  => 'text/html; charset=UTF-8');

END;
/
