/**
 *
 * Se crean parametros para el proyecto Netlife CAM
 *	 
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 20-10-2020
 */



DECLARE
  ln_id_param NUMBER := 0;
BEGIN

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROYECTO NETLIFECAM',
    'AGREGAR LA ACTIVACION PARA NETLIFECAM',
    'INFRAESTRUCTURA',
    'ACTIVACION PARA NETLIFECAM',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );               


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
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PRODUCTO CONFIGURADO PARA REGISTRAR ELEMENTO',
    '78',
    'netlifecam',
    'CAMARA',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '18'
); 




COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
