--INSERT PARAMETER CAB CAMBIO_ESTADO_SOLICITUD_MIGRACION
INSERT INTO db_general.ADMI_PARAMETRO_CAB 
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
VALUES (db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'CAMBIO_ESTADO_SOLICITUD_MIGRACION',
        'CAMBIO_ESTADO_SOLICITUD_MIGRACION','SOPORTE','CAMBIO_ESTADO','Activo','jmazon',SYSDATE,'127.0.0.1',NULL,NULL,NULL);

--INSERT PARAMETER DET DEPARTAMENTOS_ASIGNADOS
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,   
    valor2,   
    valor3,
    valor4,   
    valor5,   
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'CAMBIO_ESTADO_SOLICITUD_MIGRACION'),
    'DEPARTAMENTO_ASIGNADO',
    '128',
    '',
    '',
    '',
    '',  
    '',
    '',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    '18'
); 
--INSERT PARAMETER DET DEPARTAMENTOS_ASIGNADOS
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,   
    valor2,   
    valor3,
    valor4,   
    valor5,   
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'CAMBIO_ESTADO_SOLICITUD_MIGRACION'),
    'DEPARTAMENTO_ASIGNADO',
    '117',
    '',
    '',
    '',
    '',  
    '',
    '',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    '18'
); 