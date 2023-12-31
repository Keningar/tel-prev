/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 21-08-2019    
 * Se crea DML para configuraciones al Banco Guayaquil.
 */

--Tipo de Cuenta
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Tipo de Cuenta',
	'V',
	null,
	1,
	' ',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'D',
	'A',
	(
	SELECT ID_VARIABLE_FORMATO 
	FROM DB_FINANCIERO.ADMI_VARIABLE_FORMATO_DEBITO 
	WHERE DESCRIPCION = 'Tipo Cuenta' 
	AND ESTADO = 'Activo'
	), 
	'S',
	1,
	18,
	'N',
	null,
	'DETALLE'
  );

--Nro Cuenta
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Nro Cuenta',
	'V',
	null,
	10,
	'0',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'D',
	'N',
	(
	SELECT ID_VARIABLE_FORMATO FROM DB_FINANCIERO.ADMI_VARIABLE_FORMATO_DEBITO 
	WHERE DESCRIPCION = 'Numero Cuenta / Tarjeta' 
	AND ESTADO = 'Activo'
	),
	'N',
	2,
	18,
	'S',
	null,
	'DETALLE'
  );

--Valor
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Valor',
	'V',
	null,
	15,
	'0',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'D',
	'N',
	(
	SELECT ID_VARIABLE_FORMATO FROM DB_FINANCIERO.ADMI_VARIABLE_FORMATO_DEBITO 
	WHERE DESCRIPCION = 'Valor Total' 
	AND ESTADO        = 'Activo'
	),
	'N',
	3,
	18,
	'N',
	'quitarPuntoRedondear|2',
	'DETALLE'
  );

--Motivo
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Motivo',
	'F',
	'E0',
	2,
	' ',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'D',
	'A',
	null,
	'N',
	4,
	18,
	'N',
	null,
	'DETALLE'
  );

--Tipo nota
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Tipo nota',
	'F',
	'W',
	1,
	' ',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'D',
	'A',
	null,
	'N',
	5,
	18,
	'N',
	null,
	'DETALLE'
  );

--Agencia
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Agencia',
	'F',
	'01',
	'2',
	'0',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'I',
	'N',
	null,
	'N',
	6,
	18,
	'N',
	null,
	'DETALLE'
  );

--Referencia
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO (
ID_FORMATO_DEBITO,
DESCRIPCION,
TIPO_CAMPO,
CONTENIDO,
LONGITUD,
CARACTER_RELLENO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
ESTADO,
BANCO_TIPO_CUENTA_ID,
ORIENTACION_CARACTER_RELLENO,
TIPO_DATO,
VARIABLE_FORMATO_ID,
REQUIERE_VALIDACION,
POSICION,
EMPRESA_COD,
ES_REFERENCIA,
OPERACION_ADICIONAL,
TIPO_FORMATO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO.NEXTVAL,
	'Referencia',
	'V',
	null,
	'28',
	' ',
	'atarreaga',
	SYSDATE,
	null,
	null,
	'Activo',
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	'I',
	'A',
	(
	SELECT ID_VARIABLE_FORMATO 
	FROM DB_FINANCIERO.ADMI_VARIABLE_FORMATO_DEBITO 
	WHERE DESCRIPCION = 'Numero Identificacion' 
	AND ESTADO 		  = 'Activo'
	),
	'N',
	8,
	18,
	'N',
	null,
	'DETALLE'
  );


--SE AGREGA CUENTA AHORRO EN DB_FINANCIERO.ADMI_VALIDACION_FORMATO
INSERT INTO DB_FINANCIERO.ADMI_VALIDACION_FORMATO (
ID_VALIDACION_FORMATO,
FORMATO_DEBITO_ID,
CAMPO_TABLA_ID,
EQUIVALENCIA,
USR_CREACION,
FE_CREACION
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_VALIDACION_FORMATO.NEXTVAL,
	(
	SELECT ID_FORMATO_DEBITO 
	FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO 
	WHERE DESCRIPCION 		   = 'Tipo de Cuenta' 
	AND BANCO_TIPO_CUENTA_ID = 
	  (
	  SELECT ABTC.ID_BANCO_TIPO_CUENTA
	  FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	    DB_FINANCIERO.ADMI_BANCO AB,
	    DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	  WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	  AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	  AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	  AND ATC.ESTADO             = 'Activo'
	  AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	  AND AB.ESTADO              = 'Activo'
	  )
	  AND EMPRESA_COD = 18
	  AND POSICION    = 1
	  AND ESTADO      = 'Activo'
	),
	'1',
	'A',
	'atarreaga',
	SYSDATE
  );

--SE AGREGA CUENTA CORRIENTE EN DB_FINANCIERO.ADMI_VALIDACION_FORMATO
INSERT INTO DB_FINANCIERO.ADMI_VALIDACION_FORMATO (
ID_VALIDACION_FORMATO,
FORMATO_DEBITO_ID,
CAMPO_TABLA_ID,
EQUIVALENCIA,
USR_CREACION,
FE_CREACION
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_VALIDACION_FORMATO.NEXTVAL,
	(
    SELECT ID_FORMATO_DEBITO FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO 
    WHERE DESCRIPCION        = 'Tipo de Cuenta' 
    AND BANCO_TIPO_CUENTA_ID = 
      (
      SELECT ABTC.ID_BANCO_TIPO_CUENTA
      FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
          DB_FINANCIERO.ADMI_BANCO AB,
          DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
      WHERE ABTC.BANCO_ID        = AB.ID_BANCO
      AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
      AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
      AND ATC.ESTADO             = 'Activo'
      AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
      AND AB.ESTADO              = 'Activo'
      )
      AND EMPRESA_COD = 18
      AND POSICION    = 1
      AND ESTADO 	  = 'Activo'
	),
	'2',
	'C',
	'atarreaga',
	SYSDATE
	);

--INSERTAR FORMATO_DEBITO_RESPUESTA DE BANCO GUAYAQUIL.
INSERT INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO_RESPUESTA (
ID_FORMATO_DEBITO_RESPUESTA,
BANCO_TIPO_CUENTA_ID,
EMPRESA_COD,
BUSCA_POR,
COL_VALOR_DEBITADO,
COL_DESCRIPCION_ESTADO,
COL_ESTADO,
COL_CUENTA,
COL_IDENTIFICACION,
FILA_INICIA,
TIPO_ARCHIVO,
ESTADO,
MENSAJE_OK,
NUMERO_CUENTA_DIVIDIDA,
CARACTER_DIVISION_NUMCTA,
POSICION_DIVISION_NUMCTA,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
IP_CREACION,
COL_VALOR_ENVIADO,
COL_REFERENCIA,
CANTIDAD_FILAS,
HOJA,
TIPO_REGISTROS,
FORMATO_AGRUPADO
) VALUES (
	DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO_RESP.NEXTVAL,
	(
	SELECT ABTC.ID_BANCO_TIPO_CUENTA
	FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,
	  DB_FINANCIERO.ADMI_BANCO AB,
	  DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
	WHERE ABTC.BANCO_ID        = AB.ID_BANCO
	AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
	AND ATC.DESCRIPCION_CUENTA = 'AHORRO'
	AND ATC.ESTADO             = 'Activo'
	AND AB.DESCRIPCION_BANCO   = 'BANCO GUAYAQUIL'
	AND AB.ESTADO              = 'Activo'
	),
	18,
	'CUENTA|VALOR',
	'13-28-1',
	'0-0-0',
	'64-66-1',
	'3-13-1',
	'36-64-1',
	1,
	'TXT',
	'Activo',
	'0-0-0',
	'N',
	null,
	null,
	SYSDATE,
	'atarreaga',
	null,
	null,
	'127.0.0.1',
	'0-0-0',
	'36-64-1',
	1,
	0,
	'TODOS',
	null
  );  


COMMIT;
/
