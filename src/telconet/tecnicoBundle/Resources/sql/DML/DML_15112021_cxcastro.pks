/**
 *
 * Se crean parametros para el proyecto TN : INT: Telcos: Nuevo: Cambios en pantalla de activacion producto fastCloud
 *	 
 * @author Christian Castro <cxcastro@telconet.ec>
 * @version 1.0 15-11-2021
 */

DECLARE
  Ln_id_param NUMBER := 0;
  Lv_nombre_parametro VARCHAR2(100) := 'NO_VISUALIZAR_FORM_DATOS_TECNICOS';
BEGIN
             
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
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
  ) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    Lv_nombre_parametro,
    'Lista de los productos que no deben visualizar el formulario de datos tecnicos',
    'TECNICO',
    null,
    'Activo',
    'cxcastro',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
  );

  SELECT id_parametro
  INTO Ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = Lv_nombre_parametro;
       

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,  
      valor2,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
  ) VALUES (
      db_general.seq_admi_parametro_det.nextval,
      Ln_id_param,
      'PRODUCTO FastCloud NDT',
      'FastCloud',   
      null,
      'Activo',
      'cxcastro',
      SYSDATE,
      '127.0.0.1',
      '10'
  ); 




COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
