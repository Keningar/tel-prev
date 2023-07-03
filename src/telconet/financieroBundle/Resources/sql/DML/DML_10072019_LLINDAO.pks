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
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_FUENTE','8','RF2','RETENCION FUENTE 2%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_FUENTE'
               AND VALOR1 = '9'
               AND VALOR2 = 'RF8'
               AND VALOR3 = 'RETENCION FUENTE 8%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_FUENTE','9','RF8','RETENCION FUENTE 8%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_FUENTE'
               AND VALOR1 = '14'
               AND VALOR2 = 'RTF'
               AND VALOR3 = 'RETENCION'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_FUENTE','14','RTF','RETENCION',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_EXTERIOR'
               AND VALOR1 = '182'
               AND VALOR2 = 'REXT'
               AND VALOR3 = 'RETENCIONES DEL EXTERIOR'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_EXTERIOR','182','REXT','RETENCIONES DEL EXTERIOR',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '21'
               AND VALOR2 = 'RI70'
               AND VALOR3 = 'RETENCION IVA 70%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','21','RI70','RETENCION IVA 70%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '22'
               AND VALOR2 = 'RI10'
               AND VALOR3 = 'RETENCION IVA 100%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','22','RI10','RETENCION IVA 100%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '23'
               AND VALOR2 = 'RI1'
               AND VALOR3 = 'RETENCION IVA 10%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','23','RI1','RETENCION IVA 10%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '24'
               AND VALOR2 = 'RI20'
               AND VALOR3 = 'RETENCION IVA 20%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','24','RI20','RETENCION IVA 20%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'RETENCION_IVA'
               AND VALOR1 = '25'
               AND VALOR2 = 'RTIV'
               AND VALOR3 = 'RETENCION IVA 30%'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'RETENCION_IVA','25','RTIV','RETENCION IVA 30%',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;
  --
  --
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO
               FROM DB_GENERAL.ADMI_PARAMETRO_CAB
               WHERE NOMBRE_PARAMETRO = 'PAIS_ORIGEN_ATS'), 0)
  INTO Ln_IdParametro
  FROM DUAL;
  --
  IF Ln_IdParametro = 0 THEN
    --
    Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
    --
    Insert into DB_GENERAL.ADMI_PARAMETRO_CAB ( ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION) 
    values ( Ln_IdParametro, 'PAIS_ORIGEN_ATS', 'PERMITIRA VINCULAR EL PAIS EN DONDE SE GENERA EL ATS', 'FINANCIERO', 'ATS','Activo', 'llindao', sysdate, '127.0.0.1');
  END IF;
  --
  --
  SELECT NVL( (SELECT ID_PARAMETRO_DET
               FROM DB_GENERAL.ADMI_PARAMETRO_DET
               WHERE DESCRIPCION = 'PARAMETRO PAIS DE ORIGEN DEL ATS'
               AND VALOR1 = 'PAIS_ATS'
               AND VALOR2 = 'COLOMBIA'
               AND VALOR3 = 'NULL'
               AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdParametroDet
  FROM DUAL;
  --
  if Ln_IdParametroDet = 0 THEN
    Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,VALOR7,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION) 
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParametro,'PARAMETRO PAIS DE ORIGEN DEL ATS','PAIS_ATS','COLOMBIA','NULL',NULL,NULL,NULL,NULL,'Activo','llindao',sysdate,'127.0.0.1','10',null);
  END IF;

  --
  COMMIT;
  --
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE(SQLERRM);
    ROLLBACK;
end;
/
