SET SERVEROUTPUT ON
--Se marcan los productos tradicionales con la característica ES_PRODUCTO_TRADICIONAL para evitar que la validación se realice por nombre de grupo,
--ya que al modificar el nombre del grupo en el caso de los Small Business a BUSINESS SOLUTIONS, ha producido un error en el flujo de traslado,
--La validación considerada anteriormente era: GRUPO = 'INTERNET Y DATOS' OR (GRUPO='WIFI' AND ES_ENLACE = 'SI')
DECLARE
  Ln_IdCaractEsProdTradicional  NUMBER(5,0);
  Ln_IdCaractEsProdRestringDemo NUMBER(5,0);
  CURSOR Lc_GetProductosTradicionales
  IS
    SELECT DISTINCT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO 
    WHERE SOPORTE_MASIVO  = 'S'
    AND NOMBRE_TECNICO <> 'FINANCIERO'
    AND (   (GRUPO = 'INTERNET Y DATOS' AND NOMBRE_TECNICO <> 'IPSB')
            OR (GRUPO ='WIFI' AND ES_ENLACE = 'SI')
            OR (GRUPO = 'BUSINESS SOLUTIONS' AND LINEA_NEGOCIO = 'CONNECTIVITY' AND ID_PRODUCTO IN (1236,1246,1258,1271,1272,1273,1275,1276)
                AND NOMBRE_TECNICO <> 'IPSB')
    )
    ORDER BY ID_PRODUCTO ASC;
BEGIN
  Ln_IdCaractEsProdTradicional := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractEsProdTradicional,
      'ES_PRODUCTO_TRADICIONAL',
      'T',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  FOR I_GetProductosTradicionales IN Lc_GetProductosTradicionales
  LOOP
    INSERT
    INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      (
        ID_PRODUCTO_CARACTERISITICA,
        PRODUCTO_ID,
        CARACTERISTICA_ID,
        FE_CREACION,
        FE_ULT_MOD,
        USR_CREACION,
        USR_ULT_MOD,
        ESTADO,
        VISIBLE_COMERCIAL
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        I_GetProductosTradicionales.ID_PRODUCTO,
        Ln_IdCaractEsProdTradicional,
        SYSDATE,
        NULL,
        'mlcruz',
        NULL,
        'Activo',
        'NO'
      );
  END LOOP;
  SYS.DBMS_OUTPUT.PUT_LINE('Se han marcado correctamente los productos tradicionales');

  Ln_IdCaractEsProdRestringDemo := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractEsProdRestringDemo,
      'ES_PRODUCTO_RESTRINGIDO_DEMO',
      'T',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      FE_ULT_MOD,
      USR_CREACION,
      USR_ULT_MOD,
      ESTADO,
      VISIBLE_COMERCIAL
    )
  VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      261,
      Ln_IdCaractEsProdRestringDemo,
      SYSDATE,
      NULL,
      'mlcruz',
      NULL,
      'Activo',
      'NO'
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/