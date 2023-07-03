-- Parametros para promociones
-- Cab 1 - Tipos de promociones a parametrizar
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
(
  ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION
) 
VALUES (
  DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval, 'PROMOCION ANCHO BANDA',
  'Paremetros para promociones de ancho de banda', 'COMERCIAL',
  'PROMO_ANCHO_BANDA', 'Activo', 'djreyes', SYSDATE, '127.0.0.1'
);

-- DET 1 - Tipos de Promociones validas
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, VALOR5, VALOR6, VALOR7,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
  ),
  'Promociones que no generan link de clonacion en el nombre', 'PROM_BW', null, null, null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18', 'valor1 = Codigo del tipo de promocion'
);

-- Estados para detener promociones por promocion
-- DET 3 - Por activo
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, VALOR5, VALOR6, VALOR7,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
  ),
  'Estados permitidos para detener la promocion', 'PROM_BW', 'Activo', null, null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18', 'valor1 = Codigo del tipo de promocion, valor2 = Estado permitido'
);

-- DET 4 - Datos para webservices de detener promociones
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, VALOR5, VALOR6, VALOR7,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
  ),
  'Datos para webservices de detener promocion', 'PROM_BW', 'VALIDAR_PROMOCIONES', 'DETENER_PROMOCIONES', null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18', 'valor1 = Codigo del tipo de promocion, valor2 = opcion valida promocion, valor3 = Opcion detiene promocion'
);

-- DET 5 - Estado inicial e la promocion
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, VALOR5, VALOR6, VALOR7,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
  ),
  'Valores de inicio de la promocion', 'PROM_BW', 'Programado', 'Nuevo', '1|0', null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18', 'valor1 = Codigo del tipo de promocion, valor2 = Estado inicial, valor3 = Tipo de cliente, valor4 = Periodo de vigencia'
);

-- DET 6 - Datos para consulta
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, VALOR5, VALOR6, VALOR7,
  ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
  ),
  'Datos para consultas de planes para promocion', 'PROM_BW',
  'INTERNET DEDICADO', 'LINE-PROFILE-NAME', 'Activo,Programado', 'Activo,Clonado,Inactivo', null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'valor1=Codigo del tipo de promocion, valor2=Producto, valor3=Caracteristica, valor4=Estados de promocion, valor5=Estados de los planes'
);

COMMIT;
/
