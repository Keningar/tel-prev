/**
 *
 * Se parametriza el nombre de las tareas de facturacion
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 18-06-2021
 */

DECLARE

BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS_PROYECTO_CAMBIO_PRECIO_TN',
    'PARAMETROS PARA LAS TAREAS DE CAMBIO DE PRECIO',
    'COMERCIAL',
    'AUTORIZACION_EXCEDENTE',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
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
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROYECTO_CAMBIO_PRECIO_TN'),
    'NOMBRE_TAREA_FACTURACION_MATERIALES_EXCEDENTES',
    'FACTURACION: FACTURAS',     
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
); 

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 