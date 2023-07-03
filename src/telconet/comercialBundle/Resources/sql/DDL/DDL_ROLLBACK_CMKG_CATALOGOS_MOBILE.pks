create or replace PACKAGE DB_COMERCIAL.CMKG_CATALOGOS_MOBILE
AS
    /**
      * Documentación para el procedimiento P_GENERA_JSON_CATALOGOS
      *
      * Método que se encarga de generar el JSON de cada uno de los catálogos y cargalos en la tabla
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 07-08-2018
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.1 16-16-2019 - Se modifica el procedimiento para que solo puedan enviar los productos que utiliza el TM-COMERCIAL
      *
      */
    PROCEDURE P_GENERA_JSON_CATALOGOS(Pv_Empresa     IN  VARCHAR2,
                                      Pv_Descripcion IN  VARCHAR2,
                                      Pv_Error       OUT VARCHAR2);

    /**
      * Función que se encarga de generar el JSON de catalogo de productos
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 07-08-2018
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.1 16-10-2019 - Se modifica el procedimiento par aque solo se puedan enviar los productos
      *                           que se encuentren especificados en admi_parametros
      */
    FUNCTION F_GENERA_JSON_PRODUCTOS(Pv_Empresa     IN  VARCHAR2,
                                     Pv_Descripcion IN  VARCHAR2,
                                     Pv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de catalogo de puntos de cobertura
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 03-10-2018
      */
    FUNCTION F_GENERA_JSON_COBERTURA(Fv_Empresa     IN  VARCHAR2,
                                     Fv_Descripcion IN  VARCHAR2,
                                     Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de los canales de venta
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 14-02-2019
      *
      * author Edgar Holguin <eholguin@telconet.ec>
      * @version 1.1 24-07-2019 Se unifican cursores por medio del uso de sentencia JOIN. Se agregan validaciones para obtener el json original.
      */
    FUNCTION F_GENERA_JSON_CANALES(Fv_Empresa     IN  VARCHAR2,
                                   Fv_Descripcion IN  VARCHAR2,
                                   Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de los TIPO DE CUENTA/BANCOS
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 18-02-2019
      *
      * author Edgar Holguin <eholguin@telconet.ec>
      * @version 1.1 24-07-2019 Se unifican cursores por medio del uso de sentencia JOIN.Se agregan validaciones para obtener el json original.
      */
    FUNCTION F_GENERA_JSON_TIPO_CUENTA(Fv_Empresa     IN  VARCHAR2,
                                       Fv_Descripcion IN  VARCHAR2,
                                       Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de los TIPO DE NEGOCIO
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 19-02-2019
      */
    FUNCTION F_GENERA_JSON_TIPO_NEGOCIO(Fv_Empresa     IN  VARCHAR2,
                                        Fv_Descripcion IN  VARCHAR2,
                                        Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de los TIPO DE CONTRATO
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 19-02-2019
      */
    FUNCTION F_GENERA_JSON_TIPO_CONTRATO(Fv_Empresa     IN  VARCHAR2,
                                         Fv_Descripcion IN  VARCHAR2,
                                         Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de los DOCUMENTOS OBLIGATORIOS
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 24-04-2019
      */
    FUNCTION F_GENERA_JSON_DOC_OBLIGATORIOS(Fv_Empresa     IN  VARCHAR2,
                                            Fv_Descripcion IN  VARCHAR2,
                                            Fv_Error       IN  VARCHAR2)
    RETURN CLOB;

    /**
      * Función que se encarga de generar el JSON de elemento por empresa y tipo.
      *
      * @param Pv_Error  OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * author Edgar Holguin <eholguin@telconet.ec>
      * @version 1.0 19-07-2019
      */

    FUNCTION F_GENERA_JSON_ELEMENTOS (Fv_Empresa  IN  VARCHAR2)
    RETURN CLOB;

    /**
   * Documentacion para la funcion F_GET_VARCHAR_CLEAN
   * Funcion que limpia ciertos caracteres especiales de lña cadena enviada cono parámetro.
   * @param Fv_Cadena IN VARCHAR2   Recibe la cadena a limpiar
   * @return             VARCHAR2   Retorna cadena sin caracteres especiales
   *
   * @author Edgar Holguin <eholguin@telconet.ec>
   * @version 1.0 31-07-2019
   */
  FUNCTION F_GET_VARCHAR_CLEAN(
      Fv_Cadena IN VARCHAR2)
    RETURN VARCHAR2;  
    
    /**
      * Función que se encarga de generar el JSON de catálogo de productos que se presentarán por empresa
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 16-10-2019
      */
    FUNCTION F_GENERA_JSON_PRODUCTOS_DISP(Pv_Empresa     IN  VARCHAR2,
                                          Pv_Descripcion IN  VARCHAR2,
                                          Pv_Error       IN  VARCHAR2)
    RETURN CLOB;
    
    /**
      * Función que se encarga de generar el JSON de catalogo de productos que se presentaran por empresa
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 16-10-2019
      */
    FUNCTION F_GENERA_JSON_PARAMETROS(Pv_Empresa     IN  VARCHAR2,
                                      Pv_Descripcion IN  VARCHAR2,
                                      Pv_Error       IN  VARCHAR2)
    RETURN CLOB;
END CMKG_CATALOGOS_MOBILE;
/
create or replace PACKAGE BODY                           DB_COMERCIAL.CMKG_CATALOGOS_MOBILE AS
  PROCEDURE P_GENERA_JSON_CATALOGOS(Pv_Empresa     IN  VARCHAR2,
                                    Pv_Descripcion IN  VARCHAR2,
                                    Pv_Error       OUT VARCHAR2) AS

    Lcl_Json CLOB ;
  BEGIN
  Lcl_Json :=  Lcl_Json || '{"response":{';

  --Lleno el Json object productos
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_PRODUCTOS (Pv_Empresa,
                                                    Pv_Descripcion,
                                                    Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');

  --Lleno el Json object productos disponibles
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_PRODUCTOS_DISP (Pv_Empresa,
                                                          Pv_Descripcion,
                                                          Pv_Error));


  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object puntoCobertura
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_COBERTURA (Pv_Empresa,
                                                    Pv_Descripcion,
                                                    Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Canales
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_CANALES (Pv_Empresa,
                                                  Pv_Descripcion,
                                                  Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Tipo Negocio
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_TIPO_NEGOCIO (Pv_Empresa,
                                                       Pv_Descripcion,
                                                       Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Tipo Negocio
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_TIPO_CONTRATO (Pv_Empresa,
                                                        Pv_Descripcion,
                                                        Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Tipo Negocio
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_ELEMENTOS (Pv_Empresa));

  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Tipo Negocio
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_DOC_OBLIGATORIOS (Pv_Empresa,
                                                           Pv_Descripcion,
                                                           Pv_Error));
  DBMS_LOB.APPEND(Lcl_Json, ',');
  --Lleno el Json object Tipo Negocio
  DBMS_LOB.APPEND(Lcl_Json, F_GENERA_JSON_PARAMETROS (Pv_Empresa,
                                                      Pv_Descripcion,
                                                      Pv_Error));


  DBMS_LOB.APPEND(Lcl_Json, '},');
  DBMS_LOB.APPEND(Lcl_Json, '"status": "200",');
  DBMS_LOB.APPEND(Lcl_Json, '"message": "OK",');
  DBMS_LOB.APPEND(Lcl_Json, '"success": true,');
  DBMS_LOB.APPEND(Lcl_Json, '"token": false');
  DBMS_LOB.APPEND(Lcl_Json, '}');


  UPDATE DB_COMERCIAL.ADMI_CATALOGOS
     SET JSON_CATALOGO = Lcl_Json
  WHERE COD_EMPRESA = Pv_Empresa
    AND TIPO = 'CATALOGOEMPRESA';
  COMMIT;

    END P_GENERA_JSON_CATALOGOS;

  FUNCTION F_GENERA_JSON_PRODUCTOS(Pv_Empresa     IN  VARCHAR2,
                                   Pv_Descripcion IN  VARCHAR2,
                                   Pv_Error       IN  VARCHAR2)
  RETURN CLOB
    IS Lcl_Json CLOB;
    CURSOR C_Productos(Cv_Estado VARCHAR2, Cv_NombreTecnico VARCHAR2, Cv_EmpresaCod VARCHAR2) IS
        SELECT PRO.*, CASE  WHEN NVL(IMP.PORCENTAJE_IMPUESTO, 0) >0 THEN 'S' ELSE 'N' END AS PORCENTAJE_IMPUESTO
        FROM DB_COMERCIAL.ADMI_PRODUCTO PRO
        LEFT JOIN DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO IMP
          ON PRO.ID_PRODUCTO = IMP.PRODUCTO_ID
         AND IMP.IMPUESTO_ID = 1
         AND IMP.ESTADO = 'Activo'
        WHERE PRO.ESTADO = Cv_Estado
        AND PRO.nombre_Tecnico <> Cv_NombreTecnico
        AND PRO.es_Concentrador <> 'SI'
        AND PRO.EMPRESA_COD = Cv_EmpresaCod
        ORDER BY PRO.DESCRIPCION_PRODUCTO ASC;

   CURSOR C_Caracteristica(Cn_IdProducto NUMBER, Cv_Estado VARCHAR2) IS
       SELECT PCA.ID_PRODUCTO_CARACTERISITICA, CAR.DESCRIPCION_CARACTERISTICA
       FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PCA
       LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CAR
         ON PCA.CARACTERISTICA_ID = CAR.ID_CARACTERISTICA
       WHERE PCA.PRODUCTO_ID = Cn_IdProducto
       AND PCA.ESTADO = Cv_Estado
       AND PCA.VISIBLE_COMERCIAL = 'SI';

  BEGIN
      Lcl_Json := '"productos": [ ';
      FOR I IN C_Productos('Activo', 'FINANCIERO', Pv_Empresa) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I.ID_PRODUCTO || ',');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_PRODUCTO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"f": "' || REPLACE(I.FUNCION_PRECIO,'"','\"') || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"t": "' || I.NOMBRE_TECNICO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"i": "' || 'S' || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"g": "' || I.GRUPO || '",');
          --Inserto las caracteristicas
          DBMS_LOB.APPEND(Lcl_Json, '"c": [ ');
          FOR I1 IN C_Caracteristica(I.ID_PRODUCTO, I.ESTADO) LOOP
              DBMS_LOB.APPEND(Lcl_Json, '{');
              DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I1.ID_PRODUCTO_CARACTERISITICA || ',');
              DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I1.DESCRIPCION_CARACTERISTICA || '"');
              DBMS_LOB.APPEND(Lcl_Json, '},');
          END LOOP;
          Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
          DBMS_LOB.APPEND(Lcl_Json, ']');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);
  END F_GENERA_JSON_PRODUCTOS;

  FUNCTION F_GENERA_JSON_COBERTURA(Fv_Empresa     IN  VARCHAR2,
                                   Fv_Descripcion IN  VARCHAR2,
                                   Fv_Error       IN  VARCHAR2)
  RETURN CLOB
    IS Lcl_Json CLOB;

    CURSOR C_Jurisdiccion(Cv_EmpresaCod VARCHAR2) IS
        SELECT jur.ID_JURISDICCION, jur.NOMBRE_JURISDICCION
        FROM DB_COMERCIAL.ADMI_JURISDICCION jur
        LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO ofi
          on jur.OFICINA_ID = ofi.ID_OFICINA
        WHERE ofi.EMPRESA_ID = Cv_EmpresaCod
        AND jur.estado in ('Activo','Modificado');

    CURSOR C_Cantones(Cn_Jurisdiccion NUMBER) IS
        SELECT can.ID_CANTON, can.NOMBRE_CANTON
        FROM DB_COMERCIAL.ADMI_CANTON can
        LEFT JOIN DB_COMERCIAL.ADMI_CANTON_JURISDICCION jur
          on can.id_canton = jur.canton_id
        WHERE jur.jurisdiccion_id = Cn_Jurisdiccion
          AND jur.ESTADO = 'Activo';

    CURSOR C_Parroquias(Cn_Canton NUMBER) IS
        SELECT ID_PARROQUIA, NOMBRE_PARROQUIA
        FROM DB_COMERCIAL.ADMI_PARROQUIA
        WHERE CANTON_ID = Cn_Canton
          AND ESTADO = 'Activo'
        ORDER BY NOMBRE_PARROQUIA ASC;

    CURSOR C_Sectores(Cn_Parroquia NUMBER, Cv_EmpresaCod VARCHAR2) IS
        SELECT ID_SECTOR, NOMBRE_SECTOR
        FROM  DB_GENERAL.ADMI_SECTOR
        WHERE PARROQUIA_ID = Cn_Parroquia
          AND ESTADO = 'Activo'
          and EMPRESA_COD = Cv_EmpresaCod;


  BEGIN
      Lcl_Json := '"puntoCobertura": [ ';
      FOR I IN C_Jurisdiccion(Fv_Empresa) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I.ID_JURISDICCION || ',');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.NOMBRE_JURISDICCION || '",');
          --Inserto los cantones
          DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
          FOR I1 IN C_Cantones(I.ID_JURISDICCION) LOOP
              DBMS_LOB.APPEND(Lcl_Json, '{');
              DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I1.ID_CANTON || ',');
              DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I1.NOMBRE_CANTON || '",');
              DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
              FOR I2 IN C_Parroquias(I1.ID_CANTON) LOOP
                  DBMS_LOB.APPEND(Lcl_Json, '{');
                  DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I2.ID_PARROQUIA || ',');
                  DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I2.NOMBRE_PARROQUIA || '",');
                  DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
                  FOR I3 IN C_Sectores(I2.ID_PARROQUIA, Fv_Empresa) LOOP
                      DBMS_LOB.APPEND(Lcl_Json, '{');
                      DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I3.ID_SECTOR || ',');
                      DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I3.NOMBRE_SECTOR || '"');
                      DBMS_LOB.APPEND(Lcl_Json, '},');
                  END LOOP;
                  Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
                  DBMS_LOB.APPEND(Lcl_Json, ']');
                  DBMS_LOB.APPEND(Lcl_Json, '},');
              END LOOP;
              Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
              DBMS_LOB.APPEND(Lcl_Json, ']');
              DBMS_LOB.APPEND(Lcl_Json, '},');
          END LOOP;
          Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
          DBMS_LOB.APPEND(Lcl_Json, ']');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);
  END F_GENERA_JSON_COBERTURA;

  FUNCTION F_GENERA_JSON_CANALES(Fv_Empresa     IN  VARCHAR2,
                                 Fv_Descripcion IN  VARCHAR2,
                                 Fv_Error       IN  VARCHAR2)
  RETURN CLOB
      IS Lcl_Json CLOB;

    CURSOR C_Canales(Cv_Empresa VARCHAR2, Cv_Nombre VARCHAR2, Cv_Modulo VARCHAR2, Cv_Valor VARCHAR2)
    IS
      SELECT    DET.VALOR1 AS VALOR1, DET.VALOR2 AS VALOR2, DETH.VALOR1 AS VALOR3, DETH.VALOR2 AS VALOR4
      FROM      DB_GENERAL.ADMI_PARAMETRO_CAB CAB
      LEFT JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET  ON  CAB.ID_PARAMETRO = DET.PARAMETRO_ID
      LEFT JOIN DB_GENERAL.ADMI_PARAMETRO_DET DETH ON  DETH.VALOR3      = DET.VALOR1
      WHERE CAB.NOMBRE_PARAMETRO = Cv_Nombre
      AND CAB.MODULO             = Cv_Modulo
      AND DET.EMPRESA_COD        = Cv_Empresa
      AND DET.VALOR3             = Cv_Valor
      AND CAB.ESTADO             = 'Activo'
      AND DET.ESTADO             = 'Activo'
      ORDER BY DET.VALOR1 ASC ,DET.VALOR2 ASC ;

  Lv_Canal VARCHAR2(25) := ' ';

  BEGIN
      Lcl_Json := '"canales": [ ';
      FOR Lr_Canal IN C_Canales(Fv_Empresa, 'CANALES_PUNTO_VENTA', 'COMERCIAL', 'CANAL') LOOP

          IF Lv_Canal <> Lr_Canal.VALOR1 THEN
             IF Lv_Canal <> ' ' THEN
                Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
                DBMS_LOB.APPEND(Lcl_Json, ']');
                DBMS_LOB.APPEND(Lcl_Json, '},');
             END IF;
            --
            DBMS_LOB.APPEND(Lcl_Json, '{');
            DBMS_LOB.APPEND(Lcl_Json, '"k": "' || Lr_Canal.VALOR1 || '",');
            DBMS_LOB.APPEND(Lcl_Json, '"v": "' || Lr_Canal.VALOR2 || '",');
            --Inserto los cantones
            DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');

            DBMS_LOB.APPEND(Lcl_Json, '{');
            DBMS_LOB.APPEND(Lcl_Json, '"k": "' || Lr_Canal.VALOR3 || '",');
            DBMS_LOB.APPEND(Lcl_Json, '"v": "' || NVL(F_GET_VARCHAR_CLEAN(TRIM(
                                                  REPLACE(
                                                  REPLACE(
                                                  REPLACE(
                                                    Lr_Canal.VALOR4, Chr(9), ' '), Chr(10), ' '),
                                                    Chr(13), ' '))), '')|| '"');            
            DBMS_LOB.APPEND(Lcl_Json, '},');

            Lv_Canal := Lr_Canal.VALOR1;
            --
          ELSE
            --
            DBMS_LOB.APPEND(Lcl_Json, '{');
            DBMS_LOB.APPEND(Lcl_Json, '"k": "' || Lr_Canal.VALOR3 || '",');
            DBMS_LOB.APPEND(Lcl_Json, '"v": "' || NVL(F_GET_VARCHAR_CLEAN(TRIM(
                                                  REPLACE(
                                                  REPLACE(
                                                  REPLACE(
                                                    Lr_Canal.VALOR4, Chr(9), ' '), Chr(10), ' '),
                                                    Chr(13), ' '))), '')|| '"');        
            DBMS_LOB.APPEND(Lcl_Json, '},');
            --
          END IF;
      END LOOP;

      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      DBMS_LOB.APPEND(Lcl_Json, '},');

      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');

      RETURN(Lcl_Json);

 END F_GENERA_JSON_CANALES;

  FUNCTION F_GENERA_JSON_TIPO_CUENTA(Fv_Empresa     IN  VARCHAR2,
                                     Fv_Descripcion IN  VARCHAR2,
                                     Fv_Error       IN  VARCHAR2)
  RETURN CLOB
      IS Lcl_Json CLOB;

    Ln_Banco NUMBER := 0;

    CURSOR C_TipoCuenta
    IS
      SELECT TIPO.ID_TIPO_CUENTA       AS ID_TIPO_CUENTA, 
             TIPO.DESCRIPCION_CUENTA   AS DESCRIPCION_CUENTA,
             ABTC.ID_BANCO_TIPO_CUENTA AS ID_BANCO_TIPO_CUENTA,
             BAN.ID_BANCO              AS ID_BANCO,
             BAN.DESCRIPCION_BANCO     AS DESCRIPCION_BANCO
      FROM  DB_GENERAL.ADMI_TIPO_CUENTA  TIPO 
      LEFT JOIN DB_GENERAL.ADMI_BANCO_TIPO_CUENTA ABTC ON ABTC.TIPO_CUENTA_ID = TIPO.ID_TIPO_CUENTA
      LEFT JOIN DB_GENERAL.ADMI_BANCO             BAN  ON ABTC.BANCO_ID       = BAN.ID_BANCO     
      WHERE (   (ABTC.ES_TARJETA <> 'S' AND BAN.GENERA_DEBITO_BANCARIO = 'S')
             OR  ABTC.ES_TARJETA =  'S' AND ABTC.ESTADO IN ('Activo', 'Activo-debitos'))
     ORDER BY TIPO.ID_TIPO_CUENTA ASC, BAN.DESCRIPCION_BANCO  ASC;
  BEGIN
      Lcl_Json := '"tiposCuenta": [ ';
      FOR I IN C_TipoCuenta() LOOP
        IF Ln_Banco <> I.ID_TIPO_CUENTA THEN
             IF Ln_Banco <> 0 THEN
                Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
                DBMS_LOB.APPEND(Lcl_Json, ']');
                DBMS_LOB.APPEND(Lcl_Json, '},');
             END IF;

          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I.ID_TIPO_CUENTA || ',');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_CUENTA || '",');
          --Inserto los cantones
          DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_BANCO_TIPO_CUENTA || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_BANCO || '"');
          DBMS_LOB.APPEND(Lcl_Json, '},');

          Ln_Banco := I.ID_TIPO_CUENTA;

          ELSE
            --
            DBMS_LOB.APPEND(Lcl_Json, '{');
            DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_BANCO_TIPO_CUENTA || '",');
            DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_BANCO || '"');
            DBMS_LOB.APPEND(Lcl_Json, '},');
            --
          END IF;
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      DBMS_LOB.APPEND(Lcl_Json, '},');

      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);

 END F_GENERA_JSON_TIPO_CUENTA;

 FUNCTION F_GENERA_JSON_TIPO_NEGOCIO(Fv_Empresa     IN  VARCHAR2,
                                     Fv_Descripcion IN  VARCHAR2,
                                     Fv_Error       IN  VARCHAR2)
 RETURN CLOB
      IS Lcl_Json CLOB;

    CURSOR C_TIPO_NEGOCIO(Cv_Empresa VARCHAR2) 
      IS
        SELECT ID_TIPO_NEGOCIO, NOMBRE_TIPO_NEGOCIO
        FROM  ADMI_TIPO_NEGOCIO
        WHERE EMPRESA_COD = Cv_Empresa
        AND   ESTADO      = 'Activo';

  BEGIN
      Lcl_Json := '"tipoNegocio": [ ';
      FOR I IN C_TIPO_NEGOCIO(Fv_Empresa) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_TIPO_NEGOCIO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.NOMBRE_TIPO_NEGOCIO || '"');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);

 END F_GENERA_JSON_TIPO_NEGOCIO;

 FUNCTION F_GENERA_JSON_TIPO_CONTRATO(Fv_Empresa     IN  VARCHAR2,
                                      Fv_Descripcion IN  VARCHAR2,
                                      Fv_Error       IN  VARCHAR2)
 RETURN CLOB
      IS Lcl_Json CLOB;

    CURSOR C_TipoContrato(Cv_Empresa VARCHAR2)
    IS
      SELECT ID_TIPO_CONTRATO, DESCRIPCION_TIPO_CONTRATO
      FROM ADMI_TIPO_CONTRATO
      WHERE EMPRESA_COD = Cv_Empresa
      AND   ESTADO      = 'Activo';

  BEGIN
      Lcl_Json := '"tipoContrato": [ ';
      FOR I IN C_TipoContrato(Fv_Empresa) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_TIPO_CONTRATO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_TIPO_CONTRATO || '"');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);

 END F_GENERA_JSON_TIPO_CONTRATO;

 FUNCTION F_GENERA_JSON_DOC_OBLIGATORIOS(Fv_Empresa     IN  VARCHAR2,
                                         Fv_Descripcion IN  VARCHAR2,
                                         Fv_Error       IN  VARCHAR2)
 RETURN CLOB
      IS Lcl_Json CLOB;

    CURSOR C_DOC_OBLIGATORIO(Cv_Empresa VARCHAR2, Cv_Tipo VARCHAR2) 
      IS
        SELECT DET.DESCRIPCION, DET.VALOR3
        FROM   DB_GENERAL.ADMI_PARAMETRO_DET DET
        INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
        WHERE CAB.NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO'
          AND DET.VALOR1           = Cv_Tipo
          AND DET.VALOR2           = Cv_Empresa;
 BEGIN
      Lcl_Json := '"documentosObligatorio": [ ';


      DBMS_LOB.APPEND(Lcl_Json, '{');
      DBMS_LOB.APPEND(Lcl_Json, '"k": "NAT",');
      DBMS_LOB.APPEND(Lcl_Json, '"v": "PERSONA NATURAL",');
      --Inserto los cantones
      DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
     FOR I IN C_DOC_OBLIGATORIO(Fv_Empresa, 'NAT') LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.VALOR3 || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION || '"');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      DBMS_LOB.APPEND(Lcl_Json, '}');
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);

 END F_GENERA_JSON_DOC_OBLIGATORIOS;


 FUNCTION F_GENERA_JSON_ELEMENTOS (Fv_Empresa  IN  VARCHAR2)
 RETURN CLOB
      IS Lcl_Json CLOB;

    CURSOR C_GetElementos(Cv_Empresa VARCHAR2, Cv_TipoElemento VARCHAR2)
    IS
      SELECT ID_ELEMENTO, NOMBRE_ELEMENTO, ID_CANTON, NOMBRE_CANTON
      FROM   DB_INFRAESTRUCTURA.VISTA_ELEMENTOS
      WHERE  EMPRESA_COD          = Cv_Empresa
      AND    NOMBRE_TIPO_ELEMENTO = Cv_TipoElemento;

    Lv_TipoElemento  VARCHAR2(20) := 'EDIFICACION';
  BEGIN
      Lcl_Json := '"puntoEdificio": [ ';
      FOR I IN C_GetElementos(Fv_Empresa,Lv_TipoElemento) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_ELEMENTO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || NVL(F_GET_VARCHAR_CLEAN(TRIM(
                                                REPLACE(
                                                REPLACE(
                                                REPLACE(
                                                  I.NOMBRE_ELEMENTO, Chr(9), ' '), Chr(10), ' '),
                                                  Chr(13), ' '))), '')|| '",');
          DBMS_LOB.APPEND(Lcl_Json, '"items": [ ');
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": "' || I.ID_CANTON || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || NVL(F_GET_VARCHAR_CLEAN(TRIM(
                                                REPLACE(
                                                REPLACE(
                                                REPLACE(
                                                  I.NOMBRE_CANTON, Chr(9), ' '), Chr(10), ' '),
                                                  Chr(13), ' '))), '')|| '"');
          DBMS_LOB.APPEND(Lcl_Json, '}');
          DBMS_LOB.APPEND(Lcl_Json, ']');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);
  EXCEPTION
  WHEN OTHERS THEN

    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'TelcosMobile',
                                          'CMKG_CATALOGOS_MOBILE.F_GENERA_JSON_ELEMENTOS',
                                          'Error al obtener listado de elemntos' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_INFRAESTRUCTURA'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
    RETURN NULL;

 END F_GENERA_JSON_ELEMENTOS;

 FUNCTION F_GET_VARCHAR_CLEAN(
         Fv_Cadena IN VARCHAR2)
     RETURN VARCHAR2
  IS
  BEGIN
      RETURN TRIM(
              REPLACE(
              REPLACE(
              REPLACE(
              REPLACE(
              TRANSLATE(
              REGEXP_REPLACE(
              REGEXP_REPLACE(Fv_Cadena,'^[^A-Z|^a-z|^0-9]|[?|¿|<|>|/|;|,|.|%|"]|[)]+$', ' ')
              ,'[^A-Za-z0-9ÁÉÍÓÚáéíóúÑñ&()-_ ]' ,' ')
              ,'ÁÉÍÓÚÑ,áéíóúñ', 'AEIOUN aeioun')
              , Chr(9), ' ')
              , Chr(10), ' ')
              , Chr(13), ' ')
              , Chr(59), ' '));
      --

  END F_GET_VARCHAR_CLEAN;


  FUNCTION F_GENERA_JSON_PRODUCTOS_DISP(Pv_Empresa     IN  VARCHAR2,
                                        Pv_Descripcion IN  VARCHAR2,
                                        Pv_Error       IN  VARCHAR2)
  RETURN CLOB
    IS Lcl_Json CLOB;
    CURSOR C_Productos(Cv_Estado VARCHAR2, Cv_NombreTecnico VARCHAR2, Cv_EmpresaCod VARCHAR2) IS
      SELECT PRO.*,
        CASE
          WHEN NVL(IMP.PORCENTAJE_IMPUESTO, 0) >0
          THEN 'S'
          ELSE 'N'
        END AS PORCENTAJE_IMPUESTO
      FROM DB_COMERCIAL.ADMI_PRODUCTO PRO
      LEFT JOIN DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO IMP
      ON PRO.ID_PRODUCTO  = IMP.PRODUCTO_ID
      AND IMP.IMPUESTO_ID = 1
      AND IMP.ESTADO      = 'Activo'
      WHERE EXISTS
        (SELECT CAB.NOMBRE_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_DET DET
        LEFT JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB
        ON DET.PARAMETRO_ID        = CAB.ID_PARAMETRO
        WHERE CAB.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
        AND DET.DESCRIPCION        = PRO.DESCRIPCION_PRODUCTO
        AND DET.ESTADO             = 'Activo'
        AND DET.VALOR1             = Cv_EmpresaCod
        )
      AND PRO.ESTADO           = Cv_Estado
      AND PRO.nombre_Tecnico  <> Cv_NombreTecnico
      AND PRO.es_Concentrador <> 'SI'
      AND PRO.EMPRESA_COD      = Cv_EmpresaCod
      ORDER BY PRO.DESCRIPCION_PRODUCTO ASC;

   CURSOR C_Caracteristica(Cn_IdProducto NUMBER, Cv_Estado VARCHAR2) IS
       SELECT PCA.ID_PRODUCTO_CARACTERISITICA, CAR.DESCRIPCION_CARACTERISTICA
       FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PCA
       LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CAR
         ON PCA.CARACTERISTICA_ID = CAR.ID_CARACTERISTICA
       WHERE PCA.PRODUCTO_ID = Cn_IdProducto
       AND PCA.ESTADO = Cv_Estado
       AND PCA.VISIBLE_COMERCIAL = 'SI';

  BEGIN
      Lcl_Json := '"productosDisponibles": [ ';
      FOR I IN C_Productos('Activo', 'FINANCIERO', Pv_Empresa) LOOP
          DBMS_LOB.APPEND(Lcl_Json, '{');
          DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I.ID_PRODUCTO || ',');
          DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I.DESCRIPCION_PRODUCTO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"f": "' || REPLACE(I.FUNCION_PRECIO,'"','\"') || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"t": "' || I.NOMBRE_TECNICO || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"i": "' || 'S' || '",');
          DBMS_LOB.APPEND(Lcl_Json, '"g": "' || I.GRUPO || '",');
          --Inserto las caracteristicas
          DBMS_LOB.APPEND(Lcl_Json, '"c": [ ');
          FOR I1 IN C_Caracteristica(I.ID_PRODUCTO, I.ESTADO) LOOP
              IF (I1.DESCRIPCION_CARACTERISTICA != 'ANTIVIRUS' AND I1.DESCRIPCION_CARACTERISTICA != 'TIEMPO CONEXION')   THEN
                DBMS_LOB.APPEND(Lcl_Json, '{');
                DBMS_LOB.APPEND(Lcl_Json, '"k": ' || I1.ID_PRODUCTO_CARACTERISITICA || ',');
                DBMS_LOB.APPEND(Lcl_Json, '"v": "' || I1.DESCRIPCION_CARACTERISTICA || '"');
                DBMS_LOB.APPEND(Lcl_Json, '},');
              END IF;  
          END LOOP;
          Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
          DBMS_LOB.APPEND(Lcl_Json, ']');
          DBMS_LOB.APPEND(Lcl_Json, '},');
      END LOOP;
      Lcl_Json := SUBSTR(Lcl_Json, 0, LENGTH(Lcl_Json) - 1);
      DBMS_LOB.APPEND(Lcl_Json, ']');
      RETURN(Lcl_Json);
  END F_GENERA_JSON_PRODUCTOS_DISP;

  FUNCTION F_GENERA_JSON_PARAMETROS(Pv_Empresa     IN  VARCHAR2,
                                    Pv_Descripcion IN  VARCHAR2,
                                    Pv_Error       IN  VARCHAR2)
  RETURN CLOB
    IS Lcl_Json CLOB;

  BEGIN
      Lcl_Json := '"parametrosEmpresa": [{"k":"fechaLimitePuntoWeb", "v" : null}, {"k":"procesarPuntoWeb", "v": "N"}]';
      RETURN(Lcl_Json);
  END F_GENERA_JSON_PARAMETROS;


END CMKG_CATALOGOS_MOBILE;
/