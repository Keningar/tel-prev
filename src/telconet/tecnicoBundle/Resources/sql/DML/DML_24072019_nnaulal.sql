
-- Parámetro que definen el tiempo del cliente en el caso
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
(
    SELECT
        id_parametro
    FROM
        db_general.admi_parametro_cab
    WHERE
        nombre_parametro = 'PARAMETROS_ECUCERT'
),
'PARAMETROS_CASOS_SLA',
'Asignada',
'C',
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

/