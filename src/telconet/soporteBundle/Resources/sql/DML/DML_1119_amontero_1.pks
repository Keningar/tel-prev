--UPDATE TIPOS DE PROBLEMAS ACTUALES QUE SEAN PARA IPCCL1
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR3=132 WHERE EMPRESA_COD=10 AND DESCRIPCION= 'TIPO DE PROBLEMA DE TAREA' AND VALOR1='TAREA';
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR3=132 WHERE EMPRESA_COD=10 AND DESCRIPCION= 'TIPO DE PROBLEMA DE CASO' AND VALOR1='CASO';

--TIPOS DE PROBLEMA PARA SISTEMAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','TECNICO','131','Activo','amontero',sysdate,'127.0.0.1',10);
     
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','TECNICO','131','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','FINANCIERO','131','Activo','amontero',sysdate,'127.0.0.1',10);
     
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','FINANCIERO','131','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','COMERCIAL','131','Activo','amontero',sysdate,'127.0.0.1',10);
     
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','COMERCIAL','131','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','MOVIL','131','Activo','amontero',sysdate,'127.0.0.1',10);
     
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','MOVIL','131','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','OTROS','131','Activo','amontero',sysdate,'127.0.0.1',10);
     
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','OTROS','131','Activo','amontero',sysdate,'127.0.0.1',10);

COMMIT;