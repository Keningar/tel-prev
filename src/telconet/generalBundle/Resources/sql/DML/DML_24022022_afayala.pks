/*
 * Creación de parámetro PARAM_CANCELACION_CONFIGSW:
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 03-03-2022 - Versión Inicial.
 */
 
SET SERVEROUTPUT ON
--Creación de parámetro para el producto secure cpe
DECLARE
  Ln_IdParamCab    NUMBER;
BEGIN
  Ln_IdParamCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

  --INSERT ADMI_PARAMETRO_CAB
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
			(Ln_IdParamCab,
			'PARAM_CANCELACION_CONFIGSW',
			'PARAM_CANCELACION_CONFIGSW',
			'TECNICO',
			NULL,
			'Activo',
			'afayala',
			SYSDATE,
			'127.0.0.1',
			NULL,
			NULL,
			NULL
			);
 
  -- INSERT ADMI_PARAMETRO_DET
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_IdParamCab,
        'Parametrizacion para cancelaciones con opcion configSW',
	'SI',
        NULL,
        NULL,
	NULL,
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

  
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó parametro de ajuste de cancelaciones');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
