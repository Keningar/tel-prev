/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear parametro de minutos que se deben restar a la fecha actual para buscar tareas
 * por observacion, con el objetivo de evitar duplicidad de tareas
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 26-08-2021 - Versión Inicial.
 */


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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'WEB SERVICE TAREAS'),
    'RESTAR_MINUTOS',
    'S',
    '60',
    null,
    null,
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    '10',
    null,
    null,
    'El valor1 se utilizara como bandera para buscar o no la tarea por observacion.
    El valor2 se utilizara para restar minutos a la fecha actual y buscar la tarea por observacion y tiempo de creacion'
);

COMMIT;

/
