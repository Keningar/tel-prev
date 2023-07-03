INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            (SELECT ID_PARAMETRO
             FROM DB_GENERAL.ADMI_PARAMETRO_CAB
             WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL'), 'ESTADOS_SERVICIO_FACTIBILIDAD','Factible,Anulado,Inactivo,Eliminado,Cancel,Rechazada', null, null, null, 'Activo', 'epin', SYSDATE, '127.0.0.1', NULL, NULL, NULL, null, NULL, null, null,null); 
commit;
/             