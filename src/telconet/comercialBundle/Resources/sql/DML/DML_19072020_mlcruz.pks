--Actualización de producto TELCOHOME MG
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET NOMBRE_TECNICO = 'OTROS',
ESTADO_INICIAL = 'Activo'
WHERE ID_PRODUCTO = 1290;

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR1 = '[{"PRODUCTO_ID":1117,"DESCRIPCION_PRODUCTO":"Networking LAN","ID_CARACTERISTICA":1493,'
            || '"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,'
            || '"HELPER":"Instalación simultánea Networking LAN","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":null},'
            || '{"PRODUCTO_ID":1281,"DESCRIPCION_PRODUCTO":"SAFE CAM","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA",'
            || '"OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea SAFE CAM","REQUIERE_REGISTRO":true,'
            || '"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":11945,"DESCRIPCION_CARACTERISTICA":"IP WAN","LABEL":"IP WAN"},'
            || '{"ID_PRODUCTO_CARACTERISITICA":11941,"DESCRIPCION_CARACTERISTICA":"FIRMWARE","LABEL":"FIRMWARE"}]}]'
WHERE ID_PARAMETRO_DET = 11129;

UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SET ESTADO = 'Eliminado',
FE_ULT_MOD = SYSDATE,
USR_ULT_MOD= 'mlcruz'
WHERE ID_PRODUCTO_CARACTERISITICA IN (12409, 12410, 12424, 12425);

/
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProductoNuevo            NUMBER(5,0) := 1290;
  Ln_IdCaractPuntoMdAsociado    NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PUNTO MD ASOCIADO',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
    'COMERCIAL'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de característica PUNTO MD ASOCIADO');
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPuntoMdAsociado
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='PUNTO MD ASOCIADO';
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
      Ln_IdProductoNuevo,
      Ln_IdCaractPuntoMdAsociado,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'SI'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto con Id ' || Ln_IdProductoNuevo || ' Caracteristica PUNTO MD ASOCIADO');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para servicios de TN
DECLARE
  Ln_IdParamsServiciosTn    NUMBER;
  Ln_IdProductoNuevo        NUMBER(5,0) := 1290;
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
      'PARAMETROS_ASOCIADOS_A_SERVICIOS_TN',
      'Parámetros para diversas validaciones de servicios TN',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosTn
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_TN';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosTn,
    'Estados de los servicios parametrizados para la búsqueda de logines asociados de MD',
    'PUNTO_MD_ASOCIADO',
    'ESTADOS_SERVICIOS_PUNTOS_ASOCIADOS_MD',
    'Activo',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados de servicios permitidos para consultar los logines asociados MD');

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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosTn,
    'Ids de los productos que permiten editar el precio de negociación',
    'PUNTO_MD_ASOCIADO',
    'IDS_PRODUCTOS_EDITA_PRECIO_NEGOCIACION',
    TO_CHAR(Ln_IdProductoNuevo),
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los ids de los productos que permiten editar el precio de negociación');

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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosTn,
    'Ids de los productos que permiten editar el precio de negociación',
    'PUNTO_MD_ASOCIADO',
    'TIPO_NEGOCIO_PERMITIDO',
    'HOME',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los tipos de negocio permitidos');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/