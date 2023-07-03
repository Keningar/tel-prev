SET SERVEROUTPUT ON
--Creación de la asociación de características al producto Internet Small Business
DECLARE
  Lv_NombreTecSmallBusiness     VARCHAR2(23) := 'INTERNET SMALL BUSINESS';
  Lv_NombreTecTelcoHome         VARCHAR2(9) := 'TELCOHOME';
  Lv_NombreTecIpSmallBusiness   VARCHAR2(23) := 'IPSB';
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
  Ln_IdCaractInterfaceElementoT NUMBER(5,0);
  Ln_IdCaractMigrado            NUMBER(5,0);
  CURSOR Lc_GetProdsInternetTnMigracion
  IS
    SELECT DISTINCT ID_PRODUCTO, DESCRIPCION_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE (NOMBRE_TECNICO = Lv_NombreTecSmallBusiness OR NOMBRE_TECNICO = Lv_NombreTecTelcoHome)
    AND ESTADO = Lv_EstadoActivo
    AND EMPRESA_COD = '10';
  CURSOR Lc_GetProdsIpsTnMigracion
  IS
    SELECT ID_PRODUCTO, DESCRIPCION_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE NOMBRE_TECNICO = Lv_NombreTecIpSmallBusiness
    AND ESTADO = Lv_EstadoActivo
    AND EMPRESA_COD = '10';
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractInterfaceElementoT
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='INTERFACE ELEMENTO TELLION';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMigrado
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MIGRADO';

  FOR I_GetProdsInternetTnMigracion IN Lc_GetProdsInternetTnMigracion
  LOOP
    INSERT
    INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      (
        ID_PRODUCTO_CARACTERISITICA,
        PRODUCTO_ID,
        CARACTERISTICA_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO,
        VISIBLE_COMERCIAL
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        I_GetProdsInternetTnMigracion.ID_PRODUCTO,
        Ln_IdCaractInterfaceElementoT,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto: ' 
                             || I_GetProdsInternetTnMigracion.DESCRIPCION_PRODUCTO || ' Característica: INTERFACE ELEMENTO TELLION');
    COMMIT;
  END LOOP;

  FOR I_GetProdsIpsTnMigracion IN Lc_GetProdsIpsTnMigracion
  LOOP
    INSERT
    INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      (
        ID_PRODUCTO_CARACTERISITICA,
        PRODUCTO_ID,
        CARACTERISTICA_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO,
        VISIBLE_COMERCIAL
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        I_GetProdsIpsTnMigracion.ID_PRODUCTO,
        Ln_IdCaractInterfaceElementoT,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto: ' 
                             || I_GetProdsIpsTnMigracion.DESCRIPCION_PRODUCTO || ' Característica: INTERFACE ELEMENTO TELLION');
    INSERT
    INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      (
        ID_PRODUCTO_CARACTERISITICA,
        PRODUCTO_ID,
        CARACTERISTICA_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO,
        VISIBLE_COMERCIAL
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        I_GetProdsIpsTnMigracion.ID_PRODUCTO,
        Ln_IdCaractMigrado,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto: ' 
                             || I_GetProdsIpsTnMigracion.DESCRIPCION_PRODUCTO || ' Característica: MIGRADO');
    COMMIT;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/