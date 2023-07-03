
/**
 * Script que regulariza los detalles de parámetros del producto NETHOME.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0
 * @since 19-11-2018
 */
DECLARE
  
  -- Cursor que obtiene las características
  CURSOR C_GetCaracteristicas(Cv_UsrCreacion DB_COMERCIAL.ADMI_CARACTERISTICA.USR_CREACION%TYPE)
  IS
   
    SELECT
      AC.ID_CARACTERISTICA,
      AC.DESCRIPCION_CARACTERISTICA
    FROM
      DB_COMERCIAL.ADMI_CARACTERISTICA AC 
    WHERE
      AC.USR_CREACION = Cv_UsrCreacion;
  --
  
  Lv_UsrCreacion DB_COMERCIAL.ADMI_CARACTERISTICA.USR_CREACION%TYPE := 'telcos_nethome';

  --
BEGIN
  --
  IF C_GetCaracteristicas%ISOPEN THEN
    --
    CLOSE C_GetCaracteristicas;
    --
  END IF;
  --
  --Obtiene, itera las características y almacena en la tabla ADMI_PARAMETRO_DET
  FOR I_GetCaracteristica IN C_GetCaracteristicas(Lv_UsrCreacion)
  
  LOOP
    
    Insert 
    INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
	    OBSERVACION,
	    VALOR6,
	    VALOR7
      )
      values 
      (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO ='FACTURACION SOLICITUD DETALLADA'),
        I_GetCaracteristica.DESCRIPCION_CARACTERISTICA,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE CODIGO_PRODUCTO= I_GetCaracteristica.DESCRIPCION_CARACTERISTICA),
        NULL,
        I_GetCaracteristica.ID_CARACTERISTICA,
        NULL,
        'Activo',
        'telcos_nethome',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL,
        NULL,
        NULL
      );
    --
  END LOOP;
  --
  COMMIT;

  --
EXCEPTION
WHEN OTHERS THEN
  --
  ROLLBACK;
  --
  DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                        'SCRIPT_REGULARIZACION_DETALLE_PARAMETROS_PRODUCTOS_NETHOME',
                                        'No se pudo regularizar la información de las caracteristicas de productos MD - ' || SQLCODE || ' -ERROR- '
                                        || SQLERRM,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_COMERCIAL'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  --
END;
