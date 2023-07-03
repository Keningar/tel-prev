INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES(SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'CANTONES PARA MODULO ASIGNACIONES TN','DEFINE LOS CANTONES QUE SE PUEDEN FILTRAR EN EL DETALLE DE EL MODULO AGENTE','SOPORTE',null,'Activo','amontero',sysdate,'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CANTONES PARA MODULO ASIGNACIONES TN'),
	   'CANTON QUITO TN','R2','QUITO','178','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CANTONES PARA MODULO ASIGNACIONES TN'),
	   'CANTON GUAYAQUIL TN','R1','GUAYAQUIL','75','Activo','amontero',sysdate,'127.0.0.1',10);

COMMIT;

/