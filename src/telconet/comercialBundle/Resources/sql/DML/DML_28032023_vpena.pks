/**
 * Script para crear los logines auxiliares de los servicios cuyos productos que aplican para paquete de horas de soporte.
 * @author Victor Peña <vpena@telconet.ec>
 * @version 1.0
 * @since 11-04-2023
 */
DECLARE   
    LN_LASTLOGINAUX    NUMBER;
    LN_TOTALREGISTROS             INTEGER      := 0;
    LV_USRCREACION       VARCHAR2(50)  := 'vpena';
    LV_ESTADO            VARCHAR2(50)  := 'Activo';
    LV_LOGINAUXILIAR    VARCHAR2(200);
    LN_CONTADORCOMMIT   INTEGER      := 0;
    LN_LIMITEREGISTROS  INTEGER      := 100;
    LN_IDXP              NUMBER;
    LV_MENSAJE        VARCHAR2(3000);
    LV_STATUS         VARCHAR2(50);
    LRF_GETPARAMSDETS SYS_REFCURSOR;
    type BULK_PARAMSDET
    IS
      TABLE OF DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE INDEX BY BINARY_INTEGER;
      LR_PARAMSDETS BULK_PARAMSDET;
      
    LN_TOTALPRODUCTOS NUMBER;
    LN_IDPRODUCTO NUMBER;

BEGIN
  
    LRF_GETPARAMSDETS := NULL;
    LRF_GETPARAMSDETS := DB_GENERAL.GNRLPCK_UTIL.F_GET_PARAMS_DETS('PROD_APLICA_PAQUETE_HORAS'); -- Poner nombre del parámetro
    
    FETCH LRF_GETPARAMSDETS bulk collect INTO LR_PARAMSDETS;
    LN_IDXP       := LR_PARAMSDETS.first();
   
    APEX_JSON.PARSE(LR_PARAMSDETS(LN_IDXP).VALOR1);
    LN_TOTALPRODUCTOS := APEX_JSON.GET_COUNT(P_PATH => 'SI');
    
    FOR prodId IN 1 .. LN_TOTALPRODUCTOS
    LOOP
      LN_IDPRODUCTO :=  APEX_JSON.GET_NUMBER(P_PATH => 'SI[%d]', p0 => prodId);
      DBMS_OUTPUT.PUT_LINE('Producto: ' || LN_IDPRODUCTO);
      
      SELECT count(*)
      INTO LN_TOTALREGISTROS
          FROM DB_COMERCIAL.INFO_PERSONA persona
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL er ON er.PERSONA_ID = persona.ID_PERSONA
                    LEFT JOIN DB_COMERCIAL.INFO_PUNTO pt ON pt.PERSONA_EMPRESA_ROL_ID = er.ID_PERSONA_ROL
                    LEFT JOIN DB_COMERCIAL.INFO_SERVICIO serv ON serv.PUNTO_ID = pt.ID_PUNTO
                    LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO prod ON prod.ID_PRODUCTO = serv.PRODUCTO_ID
          WHERE pt.ESTADO = 'Activo'
            AND serv.ESTADO = 'Activo'
            AND serv.PRODUCTO_ID = LN_IDPRODUCTO
            AND prod.EMPRESA_COD = '10'
            AND serv.LOGIN_AUX IS NULL;
      
      IF LN_TOTALREGISTROS != 0 THEN
      
      FOR registro IN (
          SELECT serv.ID_SERVICIO,
                 serv.PUNTO_ID,
                 serv.PRODUCTO_ID,
                 serv.LOGIN_AUX,
                 serv.ESTADO,
                 pt.LOGIN
          FROM DB_COMERCIAL.INFO_PERSONA persona
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL er ON er.PERSONA_ID = persona.ID_PERSONA
                    LEFT JOIN DB_COMERCIAL.INFO_PUNTO pt ON pt.PERSONA_EMPRESA_ROL_ID = er.ID_PERSONA_ROL
                    LEFT JOIN DB_COMERCIAL.INFO_SERVICIO serv ON serv.PUNTO_ID = pt.ID_PUNTO
                    LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO prod ON prod.ID_PRODUCTO = serv.PRODUCTO_ID
          WHERE pt.ESTADO = 'Activo'
            AND serv.ESTADO = 'Activo'
            AND serv.PRODUCTO_ID = LN_IDPRODUCTO
            AND prod.EMPRESA_COD = '10'
            AND serv.LOGIN_AUX IS NULL
            )
      
      LOOP 

          SELECT NVL(MAX(SERV.login_split), 1)
              INTO LN_LASTLOGINAUX
              FROM (
                       --En este subquery buscamos traer todos los servicios que tengan login auxiliar en el punto para luego
                       -- obtener el de mayor numero.
                       SELECT SUBSTR(ser2.LOGIN_AUX, INSTR(ser2.LOGIN_AUX, '_' ,-1,1) + 1) as login_split
                       FROM DB_COMERCIAL.INFO_PERSONA persona2
                      LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL er2 ON er2.PERSONA_ID = persona2.ID_PERSONA
                      LEFT JOIN DB_COMERCIAL.INFO_PUNTO pt2 ON pt2.PERSONA_EMPRESA_ROL_ID = er2.ID_PERSONA_ROL
                      LEFT JOIN DB_COMERCIAL.INFO_SERVICIO ser2 ON ser2.PUNTO_ID = pt2.ID_PUNTO
                      LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO pro on pro.ID_PRODUCTO = ser2.PRODUCTO_ID
                       WHERE pt2.ESTADO = 'Activo'
                         AND ser2.ESTADO = 'Activo'
                         AND ser2.LOGIN_AUX IS NOT NULL
                         AND pro.EMPRESA_COD = '10'
                         AND ser2.PUNTO_ID = registro.PUNTO_ID) SERV;
              --Como ya tenemos el ultimo login auxiliar, procedemos a aumentarle 1.

              LN_LASTLOGINAUX := LN_LASTLOGINAUX + 1;
  
              --Hacemos el update correspondiente al servicio con el login conseguido.
              UPDATE DB_COMERCIAL.INFO_SERVICIO
              SET LOGIN_AUX = registro.LOGIN || '_' || LN_LASTLOGINAUX
              WHERE ID_SERVICIO = registro.ID_SERVICIO;
              
              --Almaceno el último login auxiliar en una variable
              LV_LOGINAUXILIAR := registro.LOGIN || '_' || LN_LASTLOGINAUX;
              
              DBMS_OUTPUT.PUT_LINE('Servicio ' || registro.ID_SERVICIO || ' para el LN_IDPRODUCTO ' || LN_IDPRODUCTO || ' con login_aux ' || LV_LOGINAUXILIAR);
              
              --Genero registro en INFO_SERVICIO_HISTORIAL  
              INSERT INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
                (ID_SERVICIO_HISTORIAL, SERVICIO_ID, USR_CREACION, FE_CREACION, IP_CREACION, ESTADO, OBSERVACION, ACCION)
              VALUES
                (DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL, registro.ID_SERVICIO, LV_USRCREACION, SYSDATE, '172.17.0.1', LV_ESTADO, 'Se genera login auxiliar: ' || LV_LOGINAUXILIAR, 'genLoginAux');
              
              LN_CONTADORCOMMIT := LN_CONTADORCOMMIT + 1;
              
              IF LN_CONTADORCOMMIT = LN_LIMITEREGISTROS THEN
                DBMS_OUTPUT.PUT_LINE('Se actualizaron e insertaron ' || LN_CONTADORCOMMIT || ' registros, se realiza commit.');
                COMMIT;
                LN_CONTADORCOMMIT := 0;
              END IF;
              
          END LOOP;
          ELSE
              DBMS_OUTPUT.PUT_LINE('0 registros sin login auxiliar, LN_IDPRODUCTO: ' || LN_IDPRODUCTO); 
          END IF;

    END LOOP;
    EXCEPTION
    WHEN OTHERS THEN
      ROLLBACK;
      LV_STATUS  := 'ERROR';
      LV_MENSAJE := SUBSTR(SQLCODE ||' - ERROR_BACKTRACE: '||SQLERRM,0,3000);
      DBMS_OUTPUT.PUT_LINE(LV_STATUS);
      DBMS_OUTPUT.PUT_LINE(LV_MENSAJE);
END;


/

