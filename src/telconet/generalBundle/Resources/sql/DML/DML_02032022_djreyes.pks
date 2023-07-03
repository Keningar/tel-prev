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
  'Bandera para presentar datos de lineprofile', 'PROM_BW', 'SI', null, null, null, null, null,
  'Activo', 'djreyes', SYSDATE, '127.0.0.1', '18',
  'valor1=Codigo del tipo de promocion, valor2=ValorBandera'
);

COMMIT;
/
