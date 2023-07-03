/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para rollback de registros de gestión de pendientes, regresar al estado antes del pase de NOC
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-01-2022 - Versión Inicial.
 */

DECLARE
    Lv_MensajeError    VARCHAR2(4000);
    contador   number := 0;
    CURSOR cursor_info_asig_tmp IS SELECT ia.ID_ASIGNACION_SOLICITUD as ID, ia.ESTADO FROM DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_TMP ia
                        ORDER BY ia.ID_ASIGNACION_SOLICITUD ASC;
    TYPE info_asig_tmp IS TABLE OF cursor_info_asig_tmp%ROWTYPE INDEX BY BINARY_INTEGER;
    info_asig info_asig_tmp;
BEGIN
    OPEN cursor_info_asig_tmp;
    LOOP
        contador := contador + 1;
        FETCH cursor_info_asig_tmp BULK COLLECT INTO info_asig LIMIT 300;
        DBMS_OUTPUT.PUT_LINE('Total bloque #'||contador||': '||info_asig.count);
        FORALL i IN info_asig.FIRST..info_asig.LAST SAVE EXCEPTIONS
            UPDATE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD SET ESTADO=info_asig(i).ESTADO 
                    WHERE ID_ASIGNACION_SOLICITUD = info_asig(i).ID;
        COMMIT;
        EXIT WHEN cursor_info_asig_tmp%NOTFOUND;    
    END LOOP;
    DBMS_OUTPUT.PUT_LINE('Total registros actualizados: '||TO_CHAR(cursor_info_asig_tmp%rowcount));
    CLOSE cursor_info_asig_tmp;
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
        ROLLBACK;    
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                            'MIGRACION NOC FASE 5 - ROLLBACK PENDIENTES',
                                            Lv_MensajeError,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                            '127.0.0.1')
                                            );
END;
/