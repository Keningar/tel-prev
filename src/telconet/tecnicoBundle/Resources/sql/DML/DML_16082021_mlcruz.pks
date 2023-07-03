SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamUrlHttp NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'MAPEO_URLS_HTTPS_A_HTTP',
      'Directorio de Base de Datos destinado para la escritura de archivos temporales',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamUrlHttp
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MAPEO_URLS_HTTPS_A_HTTP'
  AND ESTADO            = 'Activo';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      VALOR3,
      VALOR4,
      VALOR5,
      VALOR6,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      EMPRESA_COD
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParamUrlHttp,
      'Valor1: Nombre identificador, Valor2: Url con Https, Valor3: Url con Http',
      'URL_MS_GUARDAR_ARCHIVOS',
      'https://archivos.telconet.ec',
      'http://nosites.telconet.ec/archivos',
      NULL,
      NULL,
      NULL,
      'Activo',
      'mlcruz',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el parámetro MAPEO_URLS_HTTPS_A_HTTP');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/