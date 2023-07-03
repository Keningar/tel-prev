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
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  (
    SELECT
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'MAPEO DE PROMOCIONES MENSUAL'
      AND ESTADO             = 'Activo'
    ) AS PARAMETRO_ID,    
   (CASE 
       WHEN VALOR5 = '5'
       THEN REPLACE(DET.DESCRIPCION, 'CICLO1', 'CICLO3')
       WHEN VALOR5 = '6'  
       THEN REPLACE(DET.DESCRIPCION, 'CICLO2', 'CICLO4')
    END) AS DESCRIPCION,
     DET.VALOR1,
     DET.VALOR2,
     DET.VALOR3,
     DET.VALOR4,     
     (CASE 
       WHEN VALOR5 = '5'
       THEN
       (SELECT TO_CHAR(ID_CICLO) FROM DB_FINANCIERO.ADMI_CICLO 
       WHERE EMPRESA_COD='18' AND CODIGO='CICLO3' AND ROWNUM=1)
       WHEN VALOR5 = '6' 
       THEN 
       (SELECT TO_CHAR(ID_CICLO) FROM DB_FINANCIERO.ADMI_CICLO 
       WHERE EMPRESA_COD='18' AND CODIGO='CICLO4' AND ROWNUM=1)
    END) AS VALOR5,
     DET.VALOR6,
     DET.VALOR7,     
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18',
    DET.OBSERVACION
   FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
   DB_GENERAL.ADMI_PARAMETRO_DET DET
   WHERE NOMBRE_PARAMETRO='MAPEO DE PROMOCIONES MENSUAL'
   AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID   
  );

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
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  (
   SELECT
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO DE DIFERIDO DE FACTURAS'
      AND ESTADO             = 'Activo'
    ) AS PARAMETRO_ID,    
   (CASE 
       WHEN VALOR5 = '5'
       THEN REPLACE(DET.DESCRIPCION, 'CICLO1', 'CICLO3')
       WHEN VALOR5 = '6'  
       THEN REPLACE(DET.DESCRIPCION, 'CICLO2', 'CICLO4')
    END) AS DESCRIPCION,
     DET.VALOR1,
     DET.VALOR2,
     DET.VALOR3,
     DET.VALOR4,     
     (CASE 
       WHEN VALOR5 = '5'
       THEN
       (SELECT TO_CHAR(ID_CICLO) FROM DB_FINANCIERO.ADMI_CICLO 
       WHERE EMPRESA_COD='18' AND CODIGO='CICLO3' AND ROWNUM=1)
       WHEN VALOR5 = '6' 
       THEN 
       (SELECT TO_CHAR(ID_CICLO) FROM DB_FINANCIERO.ADMI_CICLO 
       WHERE EMPRESA_COD='18' AND CODIGO='CICLO4' AND ROWNUM=1)
    END) AS VALOR5,
     DET.VALOR6,
     DET.VALOR7,     
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18',
    DET.OBSERVACION
   FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
   DB_GENERAL.ADMI_PARAMETRO_DET DET
   WHERE NOMBRE_PARAMETRO='PROCESO DE DIFERIDO DE FACTURAS'
   AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID  
  );

COMMIT;
/

