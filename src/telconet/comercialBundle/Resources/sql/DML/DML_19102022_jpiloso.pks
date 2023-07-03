SET SERVEROUTPUT ON
/**
 *
 *Creación de parámetros para servicios NetlifeCamOutDoor que deben seguir flujo diferente en el cambio de razón social
 *	 
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0 19-10-2022
 */

DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
  Lv_Valor1Opcion3          VARCHAR2(19) := 'CAMBIO_RAZON_SOCIAL';
  Lv_Valor2EstadosXProdCrs  VARCHAR2(50) := 'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO';
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  --CAMBIO DE RAZÓN SOCIAL
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos de productos que no deben comparar estados de servicios en el CRS',
    Lv_Valor1Opcion3,
    'NOMBRES_TECNICOS_PRODS_PERMITIDOS_SIN_ACTIVAR',
    'NETLIFECAM OUTDOOR',
    NULL,
    NULL,
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'NETLIFECAM OUTDOOR',
    'Asignada',
    'PrePlanificada',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'NETLIFECAM OUTDOOR',
    'AsignadoTarea',
    'PrePlanificada',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'NETLIFECAM OUTDOOR',
    'PrePlanificada',
    'PrePlanificada',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'NETLIFECAM OUTDOOR',
    'Planificada',
    'PrePlanificada',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'NETLIFECAM OUTDOOR',
    'Replanificada',
    'PrePlanificada',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros con los estados cambio de razón social');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/


DECLARE
  ln_id_param NUMBER := 0;
BEGIN

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROYECTO NETLIFECAM';
       

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2, 
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETROS NETLIFECAM OUTDOOR',
    'CAMBIO RAZON SOCIAL',
    'feOrigServicioNetlifeCam',
    'Fecha inicial de servicio NetlifeCam',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Strings de consulta para realizar búsqueda de fecha de activación de origen de un servicio al aplicar cambio de razón social del producto NetlifeCamOutdoor'
); 

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2, 
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETROS NETLIFECAM OUTDOOR',
    'RENOVACION',
    'OBSERVACION RENOVACION',
    'Renovación de cámara por cumplimiento de vigencia',
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Strings de consulta para realizar búsqueda de la observacio de un servicio al aplicar cambio de razón social del producto NetlifeCamOutdoor'
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2, 
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETROS NETLIFECAM OUTDOOR',
    'RENOVACION',
    'DIAS ANTES PARA GENERAR ORDEN DE RENOVACION',
     4,
    'Activo',
    'jpiloso',
    SYSDATE,
    '127.0.0.1',
    '18',
    'Parametro de días antes de finalización deL ciclo de facturación para generar orden de renovacion'
);


COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
