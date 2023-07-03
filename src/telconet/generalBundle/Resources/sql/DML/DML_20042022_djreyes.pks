-- Estados permitidos para anular
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
    AND ESTADO = 'Activo'
  ),
  'Estados permitidos para anular la promocion', 'PROM_BW', 'Programado', 'Anulado', null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'Valor1 = Codigo del tipo de promocion, Valor2 = Estado permitido, Valor3 = Estado que tomara'
);

-- Actualizar a todos los estados en planes
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR5 = NULL
WHERE PARAMETRO_ID = (
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
    AND ESTADO = 'Activo')
AND DESCRIPCION = 'Datos para consultas de planes para promocion'
AND VALOR1 = 'PROM_BW'
AND ESTADO = 'Activo'
AND USR_CREACION = 'djreyes';

-- Inserta parametro para promociones no ejecutan masivo
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
	  WHERE NOMBRE_PARAMETRO = 'PROM_TIPO_PROMOCIONES'
    AND ESTADO = 'Activo'
  ),
  'Promociones que no se ejecutaran en masivo', 'PROM_BW', null, null, null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'Se detallan los tipos de promociones que no aplicaran en masivo'
);

// Los estados al momento de editar
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
    AND ESTADO = 'Activo'
  ),
  'Estado inicial para editar promociones', 'PROM_BW', 'EDITAR', 'Pendiente', 'Programado', null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'Valor1=Codigo del tipo de promocion, Valor2=La Accion, Valor3=Estado actual, valor4=Estado final'
);

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
    AND ESTADO = 'Activo'
  ),
  'Estado inicial para editar promociones', 'PROM_BW', 'EDITAR', 'Programado', 'Programado', null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'Valor1=Codigo del tipo de promocion, Valor2=La Accion, Valor3=Estado actual, valor4=Estado final'
);

COMMIT;
/
