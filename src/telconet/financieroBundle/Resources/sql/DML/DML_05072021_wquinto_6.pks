/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 05-07-2021    
 * Se crea la sentencia DML para insert de parametros
 */
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
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAM_ANULACION_PAGOS' AND ESTADO      = 'Activo'),
     'PARAMETRO DE HOST PARA OBTENCION DE ARCHIVO',
     'FILE-HTTP-HOST',
	 'http://nosites.telconet.ec/archivos',
	 '',
	 '',
    'Activo',
    'wquinto',
     SYSDATE,
    '127.0.0.1'
  );
