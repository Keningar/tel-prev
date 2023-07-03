/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 31-03-2022
 * Se crea DML de configuraciones de leyendas promociones.
 */
 
-- Actualizar descripción Restricción de planes
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET DESCRIPCION = 'Planes que no pagan instalación (Planes finalizados en "FARMA","EMTN","EMPL","EMNL"): 100% de descuento'
WHERE PARAMETRO_ID IN (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'RESTRICCION_PLANES_X_INSTALACION' AND ESTADO = 'Activo')
AND VALOR2 = 'c' AND EMPRESA_COD = '18';  

COMMIT;

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAM_EVALUA_TENTATIVA',
    'Parámetros definidos para proceso de tentativa',
    'COMERCIAL',
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '127.0.0.1',
    'atarreaga',
    SYSDATE,
    NULL
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_INS',    'PROM_INS',    'NULL',    'Desct. Inst. ',    'Porcentaje: Lv_Porcentaje%',    '#Numero de Periodos: Lv_NumeroPeriodos',
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: Grupo promoción; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción'  );
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MPLA',    'NO',    'Desct. Serv. Internet: ',    'Promoción Indefinida: Lv_Indefinida',    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',
    SYSDATE,    '127.0.0.1', '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MPRO',    'NO',    'Desct. Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    SYSDATE,
    '127.0.0.1',  '18', 'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MIX',    'NO',    'Desct. Serv. Internet/Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',
    SYSDATE, '127.0.0.1',  '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );   
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_TOT',    'NO',    'Desct. Total. Serv: ',    NULL,    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    
    SYSDATE,    '127.0.0.1',  '18',  'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );   
  
--INDEFINIDA SI
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MPLA',    'SI',    'Desct. Serv. Internet: ',    'Promoción Indefinida: Lv_Indefinida',    NULL,    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    
    SYSDATE,    '127.0.0.1', '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MPRO',    'SI',    'Desct. Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    NULL,    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    
    SYSDATE,    '127.0.0.1',  '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_MIX',    'SI',    'Desct. Serv. Internet/Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    NULL,    'Descuento: Lv_Descuento%',
    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',   '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );   
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TENTATIVA_MENS',    'PROM_TOT',    'SI',    NULL,    NULL,    NULL,    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,
    '127.0.0.1',    '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida'  );       
  
--MENSAJES POR CÓDIGO PROMOCIONAL
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_INS',    'PROM_INS',    'NULL',    'Desct. Cod. Prom. Inst. ',    'Porcentaje: Lv_Porcentaje%',    '#Numero de Periodos: Lv_NumeroPeriodos',    NULL,    NULL,    'Activo',    'atarreaga',
    SYSDATE,    '127.0.0.1',   '18',    'VALOR1: Grupo promoción; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción'  );
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MPLA',    'NO',    'Desct. Cod. Prom. Serv. Internet: ',    'Promoción Indefinida: Lv_Indefinida',    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%', NULL, 'Activo', 
    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MPRO',    'NO',    'Desct. Cod. Prom. Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',
    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MIX',    'NO',    'Desct. Cod. Prom. Serv. Internet/Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',
    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1', 
    '18', 'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );   
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_TOT',    'NO',    'Desct. Cod. Prom. Total. Serv: ',    NULL,    '#Numero de Periodos: Lv_NumeroPeriodos',    'Descuento: Lv_Descuento%',
    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1', '18',
    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR4: valor descripción; VALOR5: valor descripción; VALOR6: valor descripción'  );   
  
--INDEFINIDA SI
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MPLA',    'SI',    'Desct. Cod. Prom. Serv. Internet: ',    'Promoción Indefinida: Lv_Indefinida',    NULL,    'Descuento: Lv_Descuento%',
    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',   '18',
    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MPRO',    'SI',    'Desct. Cod. Prom. Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',    NULL,    'Descuento: Lv_Descuento%',
    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',  '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_MIX',    'SI',    'Desct. Cod. Prom. Serv. Internet/Servicio/Producto Adicional: ',    'Promoción Indefinida: Lv_Indefinida',
    NULL,    'Descuento: Lv_Descuento%',    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',  '18',
    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida; VALOR3: valor descripción; VALOR4: valor descripción; VALOR6: valor descripción'  );   
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_CODIGO_TENTATIVA_MENS',    'PROM_TOT',    'SI',    NULL,    NULL,    NULL,    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',
    '18',    'VALOR1: Grupo promoción; VALOR2: valor promoción indefinida'  );         

--  

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_GRUPOS_PROM',    'La promoción solo aplica para los tipos: (PROM_INS,PROM_MENS), punto_Id: Pn_IdPunto', '0', NULL, 'N', NULL,  NULL,    
    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1', '18', 'VALOR1: códgo error; VALOR2: descripción mensaje; VALOR3: descuento'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE', 'COD_EMPRESA', 'No se encuentra definido código de Empresa para el Proceso de Promociones Pv_CodigoGrupoPromocion COD_EMPRESA: Pv_CodEmpresa, punto_Id: Pn_IdPunto',
    '0',    NULL,    'N',    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',  '18',    'VALOR1: códgo error; VALOR2: descripción mensaje; VALOR3: descuento'  );   

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE', 'COD_ROL', 'Las promociones para Pv_CodigoGrupoPromocion solo aplican para Personas con rol PreCliente ó Cliente, del punto : Pn_IdPunto',    '0',    NULL,    
    'N', NULL, NULL, 'Activo', 'atarreaga', SYSDATE, '127.0.0.1', '18', 'VALOR1: códgo error; VALOR2: descripción mensaje; VALOR3: descuento'  );   
    
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_SIN_SERVICIOS',    'No se encontraron servicios para el ID_PUNTO: Pn_IdPunto',    '0',    NULL,    'N',    NULL,    NULL,    'Activo',
    'atarreaga',    SYSDATE,    '127.0.0.1', '18',    'VALOR1: código error; VALOR2: descripción mensaje; VALOR3: descuento'  );     
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_PROM_GRUPOS',    'No se pudo obtener los Grupos de Promocionales para la evaluación de reglas.',    '0',    NULL,    NULL,    
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',  '18',    'VALOR1: código error; VALOR2: descripción mensaje; VALOR3: descuento'
  );     
         
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_SOLICITUD',    'El servicio ya cuenta con una solicitud por descuento.',    '0',    NULL,    NULL,    NULL,    NULL,    'Activo',
    'atarreaga',    SYSDATE,    '127.0.0.1', '18',    'VALOR1: código error; VALOR2: descripción mensaje; VALOR3: descuento'  );  
  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE', 'COD_EXCEPCION',    'No hay excepción definida',    '0',    NULL,    'N',    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,
    '127.0.0.1', '18', 'VALOR1: código error; VALOR2: descripción mensaje; VALOR3: descuento'  ); 


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_PROM_REGLAS_CUMPLE',    'El servicio cumplió con las reglas de los grupos promocionales, para aplicar la promoción NombreGrupo',
    NULL,    NULL,    NULL,    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',     '18',    'VALOR1: código; VALOR2: descripción mensaje'  );     

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA'
      AND ESTADO             = 'Activo'
    ),
    'CODIGO_MENSAJE',    'COD_PROM_REGLAS_NO_CUMPLE',    'El servicio evaluado no cumplió con las reglas de la promoción Lv_DescTipoPromocion',    '0',    '0',    NULL,    
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',  '18',    'VALOR1: código; VALOR2: descripción mensaje; VALOR3: descuento; VALOR3: cantidad periodo'  );   
     
COMMIT;
/
