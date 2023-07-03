--Insert a la ADMI_PARAMETRO_CAB para declarar la variable que define el limite máximo de fibra

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO,
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
IP_ULT_MOD) 
VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'LIMITE_ERROR_FIBRA',
'VALOR QUE DESCRIBE EL LIMITE DE FIBRA EXCEDENTE',
'TECNICO',
NULL,
'Activo',
'nnaulal',
SYSDATE,
'127.0.0.1',
NULL,
NULL,
NULL);
COMMIT;


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
EMPRESA_COD) 
VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.CURRVAL,
'LIMITE_ERROR_FIBRA',
'5',
NULL,
NULL,
NULL,
'Activo',
'nnaulal',
SYSDATE,
'127.0.0.1',
'nnaulal',
SYSDATE,
'127.0.0.1',
NULL,
NULL
);
COMMIT;