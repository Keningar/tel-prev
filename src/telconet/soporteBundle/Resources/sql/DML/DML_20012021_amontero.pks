--
--
--
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'DEPARTAMENTOS_REPLICAR_CASOS_TAREAS',
'DEFINE LOS DEPARTAMENTOS QUE ESTAN PERMITIDOS PARA QUE CUANDO CREAN CASOS O TAREAS SE REPLIQUEN AL MODULO DE GESTION DE PENDIENTES',
       'SOPORTE',null,'Activo','amontero',sysdate,'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,OBSERVACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DEPARTAMENTOS_REPLICAR_CASOS_TAREAS'),
	   'DEPARTAMENTO NOC','133','Activo','amontero',sysdate,'127.0.0.1','VALOR1 => id del departamento',10
	  );
--
--
--
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'PROCESOS_REPLICAR_CASOS_TAREAS',
'DEFINE LOS PROCESOS QUE ESTAN PERMITIDOS PARA QUE CUANDO CREAN CASOS O TAREAS SE REPLIQUEN AL MODULO DE GESTION DE PENDIENTES',
       'SOPORTE',null,'Activo','amontero',sysdate,'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,OBSERVACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESOS_REPLICAR_CASOS_TAREAS'),
	   'NOC-GESTION MONITOREO BACKBONE','689','Activo','amontero',sysdate,'127.0.0.1','VALOR1 => id del proceso',10
	  );
--
--
--
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'TIPO_CASO_REPLICAR_CASOS',
'DEFINE TIPOS DE CASO QUE ESTAN PERMITIDOS PARA QUE CUANDO CREAN CASOS SE REPLIQUEN AL MODULO DE GESTION DE PENDIENTES',
       'SOPORTE',null,'Activo','amontero',sysdate,'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,OBSERVACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_CASO_REPLICAR_CASOS'),
	   'Backbone','124','Activo','amontero',sysdate,'127.0.0.1','VALOR1 => id de tipo caso',10
	  );

--
COMMIT;

/