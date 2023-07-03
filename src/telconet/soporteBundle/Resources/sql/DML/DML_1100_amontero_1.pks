INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES(SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN','DEFINE TIPOS DE PROBLEMA PARA EL TIPO DE ATENCION DE LA ASIGNACION','SOPORTE',null,'Activo','amontero',sysdate,'127.0.0.1');

--TIPOS DE PROBLEMA PARA CASOS

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','MONITOREO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','CACTI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','CORREOS','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','WEBHOSTING','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','FORTI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','DNS','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','SLA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','INFORME TECNICO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','INFORMACION CLIENTE','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','ENRUTAMIENTO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','MONITOREO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','WIFI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','CAIDA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','INTERMITENCIA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','SIN AFECTACION','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE CASO','CASO','OTROS','Activo','amontero',sysdate,'127.0.0.1',10);





--TIPOS DE PROBLEMA PARA TAREAS

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','MONITOREO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','CACTI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','CORREOS','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','WEBHOSTING','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','FORTI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','DNS','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','SLA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','INFORME TECNICO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','INFORMACION CLIENTE','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','ENRUTAMIENTO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','MONITOREO','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','WIFI','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','CAIDA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','INTERMITENCIA','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','SIN AFECTACION','Activo','amontero',sysdate,'127.0.0.1',10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN'),
	   'TIPO DE PROBLEMA DE TAREA','TAREA','OTROS','Activo','amontero',sysdate,'127.0.0.1',10);


COMMIT;