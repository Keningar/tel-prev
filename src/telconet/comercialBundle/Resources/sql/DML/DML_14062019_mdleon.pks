--SENTENCIAS DE REVERSO DE LA PARAMETRIZACION DE LINEA DE NEGOCIO Y SUBGRUPO, REFERENCIA "DML_13062019_mdleon"

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID=(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE DESCRIPCION='LINEA_NEGOCIO' AND MODULO='COMERCIAL');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO='LINEA_NEGOCIO' AND DESCRIPCION='LINEA_NEGOCIO' AND MODULO='COMERCIAL';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID=(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE DESCRIPCION='SUBGRUPO_PRODUCTO' AND MODULO='COMERCIAL');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO='SUBGRUPO_PRODUCTO' AND DESCRIPCION='SUBGRUPO_PRODUCTO' AND MODULO='COMERCIAL';

COMMIT;
/