
--Detalle de parametro para el id del producto Mobile Bus
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'PRODUCTO_ID',
        'MOBILE BUS',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO WHERE CODIGO = 'FOTO_DESPUES'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'CASO_ACTA'), 
    'Activo', 
    'jacarriel', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '7', 
    '10'
);

INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO WHERE CODIGO = 'FOTO_DESPUES'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'RETIRAR EQUIPO'), 
    'Activo', 
    'jacarriel', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '6', 
    '10'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'ELEMENTO_CLIENTE_ID'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'ELEMENTO_CLIENTE_ID' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

COMMIT;
/
