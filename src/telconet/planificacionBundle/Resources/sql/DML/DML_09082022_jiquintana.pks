/**
 *
 * Se crean parametros para el proyecto Perfiles Factibilidad Nacional
 *	 
 * @author Jonathan Quintana <jiquintana@telconet.ec>
 * @version 1.0 09-08-2022
 */

DECLARE
  ln_id_param NUMBER := 0;
  count_cab NUMBER := 1;
  count_carac NUMBER := 1;
  
BEGIN

SELECT COUNT(*) INTO count_cab FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';

IF count_cab < 1 THEN

  INSERT INTO DB_GENERAL.admi_parametro_cab (
      ID_PARAMETRO, 
      NOMBRE_PARAMETRO, 
      DESCRIPCION, 
      MODULO,  
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION
  ) VALUES (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
      'PERFILES_FACTIBILIDAD_NACIONAL', 
      'CONFIGURACION DE LOS PERFILES DE FACTIBILIDAD', 
      'TECNICO',
      'Activo', 
      'jiquintana', 
      sysdate, 
      '192.168.221.0'
  );
  
END IF;

SELECT COUNT(*) INTO count_carac FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CONFIGURACION_PERFILES_FACTIBILIDAD';

IF count_carac < 1 THEN

  INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA, 
    DESCRIPCION_CARACTERISTICA, 
    TIPO_INGRESO, 
    ESTADO, 
    FE_CREACION,
    USR_CREACION,
    TIPO
  ) VALUES (
      DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval, 
      'CONFIGURACION_PERFILES_FACTIBILIDAD', 
      'N', 
      'Activo', 
      sysdate, 
      'jiquintana',
      'TECNICA'
  );

END IF;

SELECT COUNT(*) INTO count_cab FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';
             
IF count_cab > 0 THEN
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';

  -- GIS TELCO COD 121

  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'GIS - Telconet', 
      '121', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '10'
    );
    
  -- Técnica Sucursal TELCO COD 117
  
  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'Técnica Sucursal - Telconet', 
      '117', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '10'
    );
    
  -- Técnica Sucursal Gos COD 862
  
  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'Técnica Sucursal Gos', 
      '862', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '10'
    );
    
  -- Técnica Sucursal Mintel COD 627
  
  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'Técnica Sucursal Mintel', 
      '627', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '10'
    );
    
  -- GIS MEGADATOS COD 371
  
  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'GIS', 
      '371', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '18'
    );
    
  -- Técnica Sucursal MEGADATOS COD 363
  
  INSERT INTO db_general.ADMI_PARAMETRO_DET (
      ID_PARAMETRO_DET, 
      PARAMETRO_ID, 
      DESCRIPCION, 
      VALOR1,
      ESTADO, 
      USR_CREACION, 
      FE_CREACION, 
      IP_CREACION,
      EMPRESA_COD
    ) VALUES (
      db_general.SEQ_ADMI_PARAMETRO_DET.nextval,
      ln_id_param, 
      'Técnica Sucursal', 
      '363', 
      'Activo', 
      'jiquintana', 
      sysdate,
      '192.168.221.0', 
      '18'
    );

END IF;

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 