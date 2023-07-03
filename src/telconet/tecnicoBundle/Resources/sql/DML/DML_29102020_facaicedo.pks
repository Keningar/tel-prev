--=======================================================================
-- Ingreso los detalles de parámetros para el tipo de validación para la capacidad que va a realizar la ejecución de control de BW masivo
-- Ingreso los detalles de parámetros para el ratio de validación para la capacidad en el control del BW de la interface
-- Ingreso los detalles de parámetros para los días que no permite la ejecución del procedimiento para el control del BW de la interface
-- Ingreso los detalles de parámetros para las ejecuciones diarias del control del BW de la interface
-- Ingreso el tipo de solicitud para las ejecuciones programadas del procedimiento para el control del BW de la interface
-- Ingreso de la caracteristica para el id de la ejecución del masivo
-- Ingreso de la caracteristica para el id del documento
-- Ingreso de la caracteristica para la fecha de ejecución
-- Ingreso de la caracteristica para el total de switch de la ejecución programada
-- Ingreso de la caracteristica para el total de interfaces de la ejecución programada
-- Ingreso de la caracteristica para la fecha de inicio de la ejecución programada
-- Ingreso de la caracteristica para la fecha de fin de la ejecución programada
-- Habilitar el control del BW de la interface
-- Actualizar el detalle de parámetro de la configuración
-- Vaciar los detalles de parámetros de los filtros para el control del BW de la interface
-- Eliminar la cabecera de parámetros para los filtros de elementos e interfaces
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'TIPO_VALIDACION_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'TIPO_VALIDACION_CONTROL_BW_INTERFACE',
        'Se define el tipo de validación para la capacidad que va a realizar la ejecución de control de BW masivo',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'TIPO_VALIDACION_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'TIPO_VALIDACION_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'RATIO_PORCENTAJE_CONTROL_BW_INTERFACE',
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
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'TIPO_VALIDACION_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE',
        'Eliminado',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'RATIO_PORCENTAJE_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'RATIO_PORCENTAJE_CONTROL_BW_INTERFACE',
        'Ratio de validación para la capacidad en el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'RATIO_PORCENTAJE_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'RATIO_PORCENTAJE_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1.12',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE',
        'Días que no permite la ejecución del procedimiento para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1',
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
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '15',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'EJECUCIONES_DIARIAS_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'EJECUCIONES_DIARIAS_CONTROL_BW_INTERFACE',
        'Configuración de las ejecuciones diarias del control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'EJECUCIONES_DIARIAS_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'EJECUCIONES_DIARIAS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1',
        'Eliminado',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- INGRESO EL TIPO SOLICITUD PARA EL CONTROL BW AUTOMATICO
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD CONTROL BW AUTOMATICO',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL ID EJECUCION
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ID_EJECUCION',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL ID DOCUMENTO
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ID_DOCUMENTO',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL FECHA EJECUCION
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'FECHA_EJECUCION',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL TOTAL SWITCH
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'TOTAL_SWITCH',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL TOTAL INTERFACES
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'TOTAL_INTERFACES',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL FECHA INICIO
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'FECHA_INICIO',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL FECHA FIN
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'FECHA_FIN',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);

-- HABILITAR EL CONTROL BW MASIVO
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 'SI' WHERE ID_PARAMETRO_DET = 12599;

-- ACTUALIZAR LA CONFIGURACIÓN DEL CONTROL BW MASIVO
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR4 = '30', VALOR5 = 'telcos/web/public/uploads/TN/tecnico/controlbwmasivo/',
    VALOR6 = '300-2400', VALOR7 = 'SI' WHERE ID_PARAMETRO_DET = 11215;

-- VACIAR LOS DETALLES DE PARAMETROS DE LOS FILTROS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID IN ( 
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE ESTADO = 'Activo'
      AND NOMBRE_PARAMETRO IN (
        'CLIENTES_CONTROL_BW_INTERFACE','REGIONES_CONTROL_BW_INTERFACE',
        'PROVINCIAS_CONTROL_BW_INTERFACE','CANTONES_CONTROL_BW_INTERFACE',
        'PARROQUIAS_CONTROL_BW_INTERFACE','ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE',
        'INTERFACE_ARRAY_CONTROL_BW_INTERFACE','ELEMENTOS_CONTROL_BW_INTERFACE',
        'INTERFACE_CONTROL_BW_INTERFACE'
      )
);

-- ELIMINAR LA CABECERA DE PARAMETROS PARA LOS FILTROS ELEMENTOS Y INTERFACES
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO IN (
    'ELEMENTOS_CONTROL_BW_INTERFACE','INTERFACE_CONTROL_BW_INTERFACE'
);

COMMIT;
/
