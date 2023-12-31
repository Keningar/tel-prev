--AGREGAMOS CODIGOS DE PLANTILLAS TEMPORALES.------
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (
ID_PLANTILLA,
NOMBRE_PLANTILLA,
CODIGO,
MODULO,
PLANTILLA,
ESTADO,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
EMPRESA_COD) VALUES 
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
'ENCUESTA VISITA TECNICA TN PILOTO',
'ENC-VIS-TN-PLT',
'SOPORTE',
'',
'Activo',
SYSDATE,
'wvera',
'',
'',
'10');
-----------------------------------------------------------
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (
ID_PLANTILLA,
NOMBRE_PLANTILLA,
CODIGO,
MODULO,
PLANTILLA,
ESTADO,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
EMPRESA_COD) VALUES 
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
'ENCUESTA INSTALACION TN PILOTO',
'ENC-INST-TN-PLT',
'SOPORTE',
'',
'Activo',
SYSDATE,
'wvera',
'',
'',
'10');
-----------------------------------------------------------
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (
ID_PLANTILLA,
NOMBRE_PLANTILLA,
CODIGO,
MODULO,
PLANTILLA,
ESTADO,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
EMPRESA_COD) VALUES 
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
'ENCUESTA VISITA TECNICA TN PILOTO',
'ACT-ENT-TN-INSP',
'TECNICO',
'',
'Activo',
SYSDATE,
'wvera',
'',
'',
'10');
-----------------------------------------------------------
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (
ID_PLANTILLA,
NOMBRE_PLANTILLA,
CODIGO,
MODULO,
PLANTILLA,
ESTADO,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
EMPRESA_COD) VALUES 
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
'Acta de Entrega Visita de Servicio TN PLT',
'ACT-ENT-TN-VISP',
'TECNICO',
'',
'Activo',
SYSDATE,
'wvera',
'',
'',
'10');
-----------------------------------------------------------
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    EMPRESA_COD) 
    VALUES 
    (
        DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
        'Acta de Retiro de Equipo TN PILOTO',
        'ACT-RET-TN-PLT',
        'PLANIFICACION',
        '',
        '',
        SYSDATE
        ,'wvera','','',
        NULL
    );

COMMIT;