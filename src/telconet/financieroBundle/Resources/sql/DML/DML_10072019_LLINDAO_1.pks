declare
  --
  Ln_IdParametro    NUMBER := 0;
  Ln_IdParametroDet NUMBER := 0;
  --
Begin
  --
  SELECT NVL( (SELECT ID_PARAMETRO
               FROM DB_GENERAL.ADMI_PARAMETRO_CAB
               WHERE NOMBRE_PARAMETRO = 'CODIGOS_RETENCION'), 0)
  INTO Ln_IdParametro
  FROM DUAL;
  --
  IF Ln_IdParametro = 0 THEN
    --
    Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
    --
    Insert into DB_GENERAL.ADMI_PARAMETRO_CAB ( ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION) 
    values ( Ln_IdParametro, 'CODIGOS_RETENCION', 'PARAMETRIZACION CODIGOS RETENCION GENERACION ATS', 'FINANCIERO', 'ATS','Activo', 'llindao', sysdate, '127.0.0.1');
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_FUENTE'
               AND VALOR1 = '8'
               AND VALOR2 = 'RF2'
               AND VALOR3 = 'RETENCION FUENTE 2%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_FUENTE','8','RF2','RETENCION FUENTE 2%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_FUENTE'
               AND VALOR1 = '20'
               AND VALOR2 = 'RF1'
               AND VALOR3 = 'RETENCION FUENTE 1%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_FUENTE','20','RF1','RETENCION FUENTE 1%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '22'
               AND VALOR2 = 'RI10'
               AND VALOR3 = 'RETENCION IVA 100%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','22','RI10','RETENCION IVA 100%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '23'
               AND VALOR2 = 'RI1'
               AND VALOR3 = 'RETENCION IVA 10%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','23','RI1','RETENCION IVA 10%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '24'
               AND VALOR2 = 'RI20'
               AND VALOR3 = 'RETENCION IVA 20%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','24','RI20','RETENCION IVA 20%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '82'
               AND VALOR2 = 'RI50'
               AND VALOR3 = 'RETENCION IVA 50%'
               AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','82','RI50','RETENCION IVA 50%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','18',null);
  END IF;
  --
  COMMIT;
  --
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE(SQLERRM);
    ROLLBACK;
end;

