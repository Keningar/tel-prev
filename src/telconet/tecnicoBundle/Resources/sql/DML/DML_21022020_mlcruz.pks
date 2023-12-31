--Creación de la asociación de las características CAPACIDAD1 y CAPACIDAD2 a los productos Small Business y TelcoHome
--Creación de la asociación de las características CLIENT CLASS y PACKAGE ID a los productos Small Business(restantes) y TelcoHome
SET SERVEROUTPUT ON
DECLARE
  Ln_IdCaractCapacidad1     NUMBER(5,0);
  Ln_IdCaractCapacidad2     NUMBER(5,0);
  Ln_IdCaractClientClass    NUMBER(5,0);
  Ln_IdCaractPackageId      NUMBER(5,0);
  Ln_IdProdTelcoHome        NUMBER(5,0);
  CURSOR Lc_ProdsSmallBusiness
  IS
    SELECT PROD.ID_PRODUCTO,
      PROD.DESCRIPCION_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO PROD
    WHERE PROD.NOMBRE_TECNICO = 'INTERNET SMALL BUSINESS'
    AND EMPRESA_COD           = '10';
  CURSOR Lc_ProdsSmallBusinessZte
  IS
    SELECT PROD.ID_PRODUCTO,
      PROD.DESCRIPCION_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO PROD
    WHERE PROD.NOMBRE_TECNICO = 'INTERNET SMALL BUSINESS'
    AND EMPRESA_COD           = '10'
    AND PROD.ID_PRODUCTO      <> 1155;
TYPE Lt_FetchArray
IS
  TABLE OF Lc_ProdsSmallBusiness%ROWTYPE;
TYPE Lt_FetchArrayZte
IS
  TABLE OF Lc_ProdsSmallBusinessZte%ROWTYPE;
  Lt_ProdsSmallBusiness Lt_FetchArray;
  Lt_ProdsSmallBusinessZte Lt_FetchArrayZte;
  Le_BulkErrors EXCEPTION;
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractCapacidad1
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='CAPACIDAD1';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractCapacidad2
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='CAPACIDAD2';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractClientClass
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='CLIENT CLASS';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPackageId
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='PACKAGE ID';
  SELECT ID_PRODUCTO
  INTO Ln_IdProdTelcoHome
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='TELCOHOME';
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractCapacidad1,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación Producto TELCOHOME Caracteristica CAPACIDAD1');
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractCapacidad2,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación Producto TELCOHOME Caracteristica CAPACIDAD2');
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractClientClass,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación Producto TELCOHOME Caracteristica CLIENT CLASS');
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractPackageId,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación Producto TELCOHOME Caracteristica PACKAGE ID');

  IF Lc_ProdsSmallBusinessZte%ISOPEN THEN
    CLOSE Lc_ProdsSmallBusinessZte;
  END IF;
  OPEN Lc_ProdsSmallBusinessZte;
  LOOP
    FETCH Lc_ProdsSmallBusinessZte BULK COLLECT INTO Lt_ProdsSmallBusinessZte LIMIT 100;
    FORALL Ln_Index IN 1..Lt_ProdsSmallBusinessZte.COUNT SAVE EXCEPTIONS
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
        Lt_ProdsSmallBusinessZte(Ln_Index).ID_PRODUCTO,
        Ln_IdCaractPackageId,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    FORALL Ln_Index IN 1..Lt_ProdsSmallBusinessZte.COUNT SAVE EXCEPTIONS
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
        Lt_ProdsSmallBusinessZte(Ln_Index).ID_PRODUCTO,
        Ln_IdCaractClientClass,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    EXIT
  WHEN Lc_ProdsSmallBusinessZte%NOTFOUND;
  END LOOP;
  CLOSE Lc_ProdsSmallBusinessZte;
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación para productos Small Business restantes - CLIENT CLASS y PACKAGE ID');
  IF Lc_ProdsSmallBusiness%ISOPEN THEN
    CLOSE Lc_ProdsSmallBusiness;
  END IF;
  OPEN Lc_ProdsSmallBusiness;
  LOOP
    FETCH Lc_ProdsSmallBusiness BULK COLLECT INTO Lt_ProdsSmallBusiness LIMIT 100;
    FORALL Ln_Index IN 1..Lt_ProdsSmallBusiness.COUNT SAVE EXCEPTIONS
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
        Lt_ProdsSmallBusiness(Ln_Index).ID_PRODUCTO,
        Ln_IdCaractCapacidad1,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    FORALL Ln_Index IN 1..Lt_ProdsSmallBusiness.COUNT SAVE EXCEPTIONS
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
        Lt_ProdsSmallBusiness(Ln_Index).ID_PRODUCTO,
        Ln_IdCaractCapacidad2,
        CURRENT_TIMESTAMP,
        'mlcruz',
        'Activo',
        'NO'
      );
    EXIT
  WHEN Lc_ProdsSmallBusiness%NOTFOUND;
  END LOOP;
  CLOSE Lc_ProdsSmallBusiness;
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de relación para productos Small Business - CAPACIDAD1 y CAPACIDAD2');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
|| DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se actualiza el valor4 del parámetro de velocidades de productos Small Business que serán considerados como los valores de las capacidades 1 y 2 
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR4         = COALESCE(TO_NUMBER(REGEXP_SUBSTR(VALOR1,'^\d+')),0)*1000,
USR_ULT_MOD        = 'mlcruz',
FE_ULT_MOD         = SYSDATE
WHERE PARAMETRO_ID =
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MAPEO_VELOCIDAD_PERFIL'
  );
COMMIT;
/
--Script de regularización para eliminar la características de perfil asociadas a los servicios Small Business ZTE que ya están activos y
--se crean las características CAPACIDAD1 y CAPACIDAD2 con el valor respectivo de la velocidad * 1000
DECLARE
  CURSOR Lc_ServiciosRegulaCaracts
  IS
    SELECT DISTINCT SERVICIO.ID_SERVICIO,
      PUNTO.LOGIN,
      ELEMENTO.NOMBRE_ELEMENTO,
      CARACT.DESCRIPCION_CARACTERISTICA,
      COALESCE(TO_NUMBER(REGEXP_SUBSTR(SPC_VELOCIDAD.VALOR,'^\d+')),0) * 1000 AS CAPACIDAD,
      (SELECT APC_CAPACIDAD1.ID_PRODUCTO_CARACTERISITICA
      FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_CAPACIDAD1
      INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT_CAPACIDAD1
      ON CARACT_CAPACIDAD1.ID_CARACTERISTICA           = APC_CAPACIDAD1.CARACTERISTICA_ID
      WHERE APC_CAPACIDAD1.PRODUCTO_ID                 = SERVICIO.PRODUCTO_ID
      AND CARACT_CAPACIDAD1.DESCRIPCION_CARACTERISTICA = 'CAPACIDAD1'
      AND APC_CAPACIDAD1.ESTADO                        = 'Activo'
      ) AS ID_PROD_CARACT_CAPACIDAD1,
    (SELECT APC_CAPACIDAD2.ID_PRODUCTO_CARACTERISITICA
    FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_CAPACIDAD2
    INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT_CAPACIDAD2
    ON CARACT_CAPACIDAD2.ID_CARACTERISTICA           = APC_CAPACIDAD2.CARACTERISTICA_ID
    WHERE APC_CAPACIDAD2.PRODUCTO_ID                 = SERVICIO.PRODUCTO_ID
    AND CARACT_CAPACIDAD2.DESCRIPCION_CARACTERISTICA = 'CAPACIDAD2'
    AND APC_CAPACIDAD2.ESTADO                        = 'Activo'
    ) AS ID_PROD_CARACT_CAPACIDAD2,
    (SELECT APC_PERFIL.ID_PRODUCTO_CARACTERISITICA
    FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_PERFIL
    INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT_PERFIL
    ON CARACT_PERFIL.ID_CARACTERISTICA           = APC_PERFIL.CARACTERISTICA_ID
    WHERE APC_PERFIL.PRODUCTO_ID                 = SERVICIO.PRODUCTO_ID
    AND CARACT_PERFIL.DESCRIPCION_CARACTERISTICA = 'PERFIL'
    AND APC_PERFIL.ESTADO                        = 'Activo'
    ) AS ID_PROD_CARACT_PERFIL
  FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO
  INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
  ON ST.SERVICIO_ID = SERVICIO.ID_SERVICIO
  INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
  ON PUNTO.ID_PUNTO = SERVICIO.PUNTO_ID
  INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
  ON PRODUCTO.ID_PRODUCTO = SERVICIO.PRODUCTO_ID
  INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO
  ON ELEMENTO.ID_ELEMENTO = ST.ELEMENTO_ID
  INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO
  ON MODELO.ID_MODELO_ELEMENTO = ELEMENTO.MODELO_ELEMENTO_ID
  INNER JOIN DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC_VELOCIDAD
  ON SPC_VELOCIDAD.SERVICIO_ID = SERVICIO.ID_SERVICIO
  INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_VELOCIDAD
  ON APC_VELOCIDAD.ID_PRODUCTO_CARACTERISITICA = SPC_VELOCIDAD.PRODUCTO_CARACTERISITICA_ID
  INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
  ON CARACT.ID_CARACTERISTICA           = APC_VELOCIDAD.CARACTERISTICA_ID
  WHERE (SERVICIO.ESTADO                = 'Activo' 
         OR SERVICIO.ESTADO             = 'Asignada')
  AND (PRODUCTO.NOMBRE_TECNICO          = 'INTERNET SMALL BUSINESS'
        OR PRODUCTO.NOMBRE_TECNICO      = 'TELCOHOME')
  AND MODELO.NOMBRE_MODELO_ELEMENTO     = 'C320'
  AND CARACT.DESCRIPCION_CARACTERISTICA = 'VELOCIDAD'
  AND SPC_VELOCIDAD.ESTADO              = 'Activo';
TYPE Lt_FetchArray
IS
  TABLE OF Lc_ServiciosRegulaCaracts%ROWTYPE;
  Lt_ServiciosRegulaCaracts Lt_FetchArray;
  Le_BulkErrors EXCEPTION;
  PRAGMA EXCEPTION_INIT(Le_BulkErrors, -24381);
BEGIN
  IF Lc_ServiciosRegulaCaracts%ISOPEN THEN
    CLOSE Lc_ServiciosRegulaCaracts;
  END IF;
  OPEN Lc_ServiciosRegulaCaracts;
  LOOP
    FETCH Lc_ServiciosRegulaCaracts BULK COLLECT
    INTO Lt_ServiciosRegulaCaracts LIMIT 1000;
    FORALL Ln_Index IN 1..Lt_ServiciosRegulaCaracts.COUNT SAVE EXCEPTIONS
    UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
    SET ESTADO                      = 'Eliminado',
      USR_ULT_MOD                   = 'regulaCaractZte',
      FE_ULT_MOD                    = SYSDATE
    WHERE SERVICIO_ID               = Lt_ServiciosRegulaCaracts(Ln_Index).ID_SERVICIO
    AND PRODUCTO_CARACTERISITICA_ID = Lt_ServiciosRegulaCaracts(Ln_Index).ID_PROD_CARACT_PERFIL;
    FORALL Ln_Index IN 1..Lt_ServiciosRegulaCaracts.COUNT SAVE EXCEPTIONS
    INSERT
    INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      (
        ID_SERVICIO_PROD_CARACT,
        SERVICIO_ID,
        PRODUCTO_CARACTERISITICA_ID,
        VALOR,
        FE_CREACION,
        USR_CREACION,
        ESTADO
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
        Lt_ServiciosRegulaCaracts(Ln_Index).ID_SERVICIO,
        Lt_ServiciosRegulaCaracts(Ln_Index).ID_PROD_CARACT_CAPACIDAD1,
        Lt_ServiciosRegulaCaracts(Ln_Index).CAPACIDAD,
        SYSDATE,
        'regulaCaractZte',
        'Activo'
      );
    FORALL Ln_Index IN 1..Lt_ServiciosRegulaCaracts.COUNT SAVE EXCEPTIONS
    INSERT
    INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      (
        ID_SERVICIO_PROD_CARACT,
        SERVICIO_ID,
        PRODUCTO_CARACTERISITICA_ID,
        VALOR,
        FE_CREACION,
        USR_CREACION,
        ESTADO
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
        Lt_ServiciosRegulaCaracts(Ln_Index).ID_SERVICIO,
        Lt_ServiciosRegulaCaracts(Ln_Index).ID_PROD_CARACT_CAPACIDAD2,
        Lt_ServiciosRegulaCaracts(Ln_Index).CAPACIDAD,
        SYSDATE,
        'regulaCaractZte',
        'Activo'
      );
    EXIT
  WHEN Lc_ServiciosRegulaCaracts%NOTFOUND;
  END LOOP;
  CLOSE Lc_ServiciosRegulaCaracts;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se regularizaron los servicios Small Business Zte');
EXCEPTION
WHEN Le_BulkErrors THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/