/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * Script para crear cabecera y detalle de parametro que devuelve las empresas responsables, habilitadas para el nuevo campo de indisponibilidad
 * @author Jose Daniel Giler <jdgiler@telconet.ec>
 * @version 1.0 24-11-2021 - Versión Inicial.
 */

Insert into DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'INDISPONIBILIDAD_TAREAS_EMPRESAS',
    'RETORNA LAS EMPRESAS RESPONSABLES DE LAS TAREAS',
    'SOPORTE',
    'TAREAS',
    'Activo',
    'jdgiler',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
);

-- detalle 1
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'INDISPONIBILIDAD_TAREAS_EMPRESAS'),
    'RETORNA LAS EMPRESAS RESPONSABLES DE LAS TAREAS',
    '10',
    'Telconet',
    null,
    null,
    'Activo',
    'jdgiler',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

-- detalle 2
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'INDISPONIBILIDAD_TAREAS_EMPRESAS'),
    'RETORNA LAS EMPRESAS RESPONSABLES DE LAS TAREAS',
    '18',
    'Megadatos',
    null,
    null,
    'Activo',
    'jdgiler',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);


/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * Script para crear cabecera y detalle de parametro que devuelve el departamento autorizado para habilitar boton de indisponibilidad
 * @author Jose Daniel Giler <jdgiler@telconet.ec>
 * @version 1.0 24-11-2021 - Versión Inicial.
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
    'INDISPONIBILIDAD_TAREAS_ROL',
    'DEPARTAMENTOS PERMITIDOS PARA HABILITAR BOTON INDISPONIBILIDAD',
    'SOPORTE',
    'TAREAS',
    'Activo',
    'jdgiler',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
);

-- detalle 1
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'INDISPONIBILIDAD_TAREAS_ROL'),
    'RETORNA LOS DEPARTAMENTOS PERMITIDOS PARA HABILITAR BOTON INDISPONIBILIDAD',
    '914',
    'GEPON/TAP',
    null,
    null,
    'Activo',
    'jdgiler',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

COMMIT;

/
