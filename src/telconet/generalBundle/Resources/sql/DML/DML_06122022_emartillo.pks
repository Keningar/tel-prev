/**
 *
 * Parametros referentes a ciclo de Facturacion.
 * Obtener fecha de corte para el cliente.
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 05-12-2022
 * 
 **/
DECLARE
  ln_id_param NUMBER := 0;
BEGIN

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    PROCESO,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CICLO_FACTURACION',
    'OBTENER LOS CICLOS DE FACTURACION',
    'GENERAR FECHA DE CORTE',
    'FINANCIERO',
    'Activo',
    'emartillo',
     SYSDATE,
    '127.0.0.1'
  );
  
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CICLO_FACTURACION';
   

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION PARA LOS CICLOS DE FACTURACION',
    'Ciclo (I) - 1 al 30',
    5,
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Se agrega unicamente el parametro VALOR2 
     para el ciclo de facturacion I.'
); 

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1, 
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES(
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION PARA LOS CICLOS DE FACTURACION',
    'Ciclo (II) - 15 al 14',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Se agrega unicamente el parametro VALOR2 
     para el ciclo de facturacion I.'
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES(
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION PARA LOS CICLOS DE FACTURACION',
    'Ciclo (III) - 8 al 7',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Se agrega unicamente el parametro VALOR2 
     para el ciclo de facturacion I.'
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1, 
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES(
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION PARA LOS CICLOS DE FACTURACION',
    'Ciclo (IV) - 22 al 21',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Se agrega unicamente el parametro VALOR2 
     para el ciclo de facturacion I.'    
);
COMMIT;
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/
