/*
 * @author Byron Pibaque <bpibaque@telconet.ec>
 * @version 1.0
 * @since 28-02-2023
 * Se crean las sentencias DML para insertar parámetros  relacionados con el modelo predictivo.
 *
 */



DECLARE
  --
  Ln_ParametroId NUMBER := 0;
  --
BEGIN
  ------------------------------------------------------------------------------------------------------------------
  -- CONFIGURACION DE PARAMETROS PARA PERFILES Y TIEMPO DE ANTIGUEDAD DE TAREA DE RETENCION PARA MODELO PREDICTIVO--
  ------------------------------------------------------------------------------------------------------------------
  SELECT
    NVL(
      (
        SELECT
          ID_PARAMETRO
        FROM
          DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
          NOMBRE_PARAMETRO = 'PARAMETROS_MODELO_PREDICTIVO'
      ),
      0
    ) INTO Ln_ParametroId
  FROM
    DUAL;

  --
  --
  IF Ln_ParametroId = 0 THEN 

    Ln_ParametroId := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD)
    VALUES (Ln_ParametroId, 'PARAMETROS_MODELO_PREDICTIVO', 'CONFIGURA PARAMETROS PARA EL MODELO PREDICTIVO', 'COMERCIAL/FINANCIERO', 'MODELO_PREDICTIVO', 'Activo', 'jontuna', SYSDATE, '127.0.0.1', NULL, NULL, NULL);

  END IF;

  -- Parametrización de Perfiles para el modelo predictivo
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Asistente_Cobranzas_Bancario',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Asistente_Cobranzas_Jr',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Asistente_Servicio_Cliente',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Coordinador_Calidad',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Coordinador_Cobranzas',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Coordinador_Facturacion',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Coordinador_IPCC',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Coordinador_Servicio_Cliente',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Distribuidor_AtencionCliente',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Gerente_SAI',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Ip_Contact_Center',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Jefe_BigData',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Jefe_Cobranzas',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Jefe_IPCC',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'PERFILES','Md_Tecnico_Mantenimiento_IPCC',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  
  -- Parametrización de Tiempo de antigüedad de una tarea de retención (6 meses)
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'TIEMPO_RETENCION_MESES','6',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');

  -- Parametrización de los estados de las tareas
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'ESTADOS_TAREAS','Finalizada',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, VALOR5, EMPRESA_COD, VALOR6, VALOR7) VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'ESTADOS_TAREAS','Asignada',NULL, NULL, NULL, 'Activo', 'jontuna', SYSDATE, '127.0.0.1', 'NULL', '18', 'NULL', 'NULL');

--Parametrización de semaforo
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1, VALOR2,VALOR3,VALOR4, ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_ParametroId,'SEMAFORO_PARAMETRO','probabilidad_Cancel_Voluntaria',NULL,NULL,NULL,'Activo','bpibaque',SYSDATE,'127.0.0.1','NULL',  '18','NULL','NULL');
  
  COMMIT;

  EXCEPTION
    WHEN OTHERS THEN DBMS_OUTPUT.PUT_LINE (SQLERRM);

END;

/
