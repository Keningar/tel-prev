--INICIO DE PARAMETROS PARA MENSAJES QUE SE RETORNARAN AL USUARIO DEL GRUPO TMO
INSERT INTO db_general.admi_parametro_cab 
(
ID_PARAMETRO,
NOMBRE_PARAMETRO,
DESCRIPCION,
MODULO,
ESTADO,
USR_CREACION,
FE_CREACION,
IP_CREACION
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'MENSAJES_TM_OPERACIONES',
    'MENSAJES DE USUARIO PARA GRUPO TM OPERACIONES',
    'TMO',
    'Activo',
    'jnazareno',
     SYSDATE,
    '127.0.0.0'
);

---------------------------------------------------------------

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'MENSAJES_TM_OPERACIONES'
            ),
    'MENSAJE DE EXITO PARA WS putPermisoJornadaAlimentacion',
    'MSG_EXITO_PERMISO_JORNADA_ALIMENTACION',
    'Valor del permiso seteado',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

-------------------------------------------------------------------------

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'MENSAJES_TM_OPERACIONES'
            ),
    'MENSAJE DE ERROR PARA WS putPermisoJornadaAlimentacion',
    'MSG_ERROR_PERMISO_JORNADA_ALIMENTACION',
    'No se pudo setear el valor del permiso',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--------------------------------------------------------------------------------

--FIN DE PARAMETRO PARA MENSAJES QUE SE RETORNARAN AL MOVIL

----INICIO DE PARÁMETROS PARA EL MOVIL

INSERT INTO db_general.admi_parametro_det 
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
VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'URL PARA EL CONSUMO DEL SERVICIO DE HALL.',
    'URL_HALL_SOLICITAR_PERMISO_EVENTO',
    'http://hal.telconet.ec:8181/cxf/coordinador/cuadrilla/solicitarPermiso',
    'N',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

INSERT
	INTO
	DB_GENERAL.ADMI_PARAMETRO_DET (
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
	OBSERVACION)
VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
(SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'), 
'TIEMPO DE ALIMENTACIÓN EN MINUTOS', 
'TIEMPO_ALIMENTACION', '60', NULL, NULL, 'Activo', 'wvera', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--------------------------------------------------------------------------------

--FIN DE PARÁMETROS PARA EL MOVIL

COMMIT;