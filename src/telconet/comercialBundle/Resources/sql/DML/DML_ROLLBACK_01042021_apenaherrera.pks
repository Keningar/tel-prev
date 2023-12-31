DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'TIPO_PERSONA';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'TIPO_PLAN';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'TIPO_DOCUMENTO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'CANTIDAD_PERMITIDA_DOCUMENTOS';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_CAMBIO_BENEFICIO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_VALIDACION_ADULTO_MAYOR';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_VALIDACION_PLANES_PERMITIDOS';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_VALIDACION_TIPO_TRIBUTARIO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_ACTUALIZACION_FECHA_NACIMIENTO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_CONFIRMACION_FECHA_NACIMIENTO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_CANCELACION_BENEFICIO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
        AND ESTADO             = 'Activo'
    ) AND VALOR1 = 'MENSAJE_VALIDACION_DOC_REQUERIDOS';

DELETE FROM DB_GENERAL.ADMI_MOTIVO
 WHERE NOMBRE_MOTIVO IN ('Disposición de la Empresa','Decisión del Cliente','Ente Regulador');

DELETE FROM DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL 
 WHERE DESCRIPCION_TIPO_DOCUMENTO='PLANILLA';

COMMIT;
/

