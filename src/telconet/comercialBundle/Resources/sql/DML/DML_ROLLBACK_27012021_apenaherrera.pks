DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'CODIGO_PRODUCTO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'ESTADO_PUNTO';

COMMIT;
/

