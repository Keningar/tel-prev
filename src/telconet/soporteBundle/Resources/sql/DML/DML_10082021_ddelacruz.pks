/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear cabecera y detalle de parametro con numero de Horas que se restan a la fecha actual, 
 * para buscar actividades de Sisred, casos y tareas de Telcos, que hayan afectado al login
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 10-08-2021 - Versión Inicial.
 */

Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
(
    ID_PARAMETRO, 
    NOMBRE_PARAMETRO, 
    DESCRIPCION,
    MODULO, 
    PROCESO, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    USR_ULT_MOD, 
    FE_ULT_MOD, 
    IP_ULT_MOD
)
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'TIEMPO_AFECTACIONES_LOGIN',
    'Tiempo que se resta a la fecha actual, para buscar actividades que hayan afectado a un Login',
    'SOPORTE',
    null,
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET, 
    PARAMETRO_ID, 
    DESCRIPCION, 
    VALOR1, 
    VALOR2, 
    VALOR3, 
    VALOR4, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    USR_ULT_MOD, 
    FE_ULT_MOD, 
    IP_ULT_MOD, 
    VALOR5, 
    EMPRESA_COD, 
    VALOR6, 
    VALOR7, 
    OBSERVACION
)
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIEMPO_AFECTACIONES_LOGIN'),
    'CANTIDAD_EN_HORAS',
    '48',
    '24',
    '24',
    '24',
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    '24',
    '10',
    '24',
    '24',
    'El valor1 se utilizara cuando la consulta se realice un dia lunes, para incluir el fin de semana en la busqueda.
    Del valor2 al valor7 se utilizara cuando la consulta se realice cualquier dia de la semana con excepcion del Lunes.
    Esta configuracion se encuentra disponible para cambiar el valor del dia de la semana segun se requiera'
);

COMMIT;

/