Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD) values 
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,116,'CNR','ZTE','CNR',null,null,'Activo','jbozada',sysdate,'127.0.0.1',null,null,null,null,null);

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO) 
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'CLIENT CLASS','N','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO) 
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'PACKAGE ID','N','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,FE_ULT_MOD,
USR_CREACION,USR_ULT_MOD,ESTADO,VISIBLE_COMERCIAL) 
values (DB_COMERCIAL.seq_ADMI_PRODUCTO_CARAC.nextval,63,
(select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA = 'CLIENT CLASS'),SYSDATE,null,'jbozada',null,'Activo','NO');

Insert into DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,FE_ULT_MOD,
USR_CREACION,USR_ULT_MOD,ESTADO,VISIBLE_COMERCIAL) 
values (DB_COMERCIAL.seq_ADMI_PRODUCTO_CARAC.nextval,63,
(select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA = 'PACKAGE ID'),SYSDATE,null,'jbozada',null,'Activo','NO');


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'ISB_TECNOLOGIAS_NO_PERMITIDAS',
    'ISB_TECNOLOGIAS_NO_PERMITIDAS',
    'COMERCIAL',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ISB_TECNOLOGIAS_NO_PERMITIDAS'),
    'ISB_TECNOLOGIAS_NO_PERMITIDAS',
    'TECNOLOGIAS',
    'ZTE',
    '',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );



COMMIT;

