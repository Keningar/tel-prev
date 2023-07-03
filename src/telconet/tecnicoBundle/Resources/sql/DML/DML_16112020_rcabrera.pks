/**
 *
 * Se crean parametros para el proyecto Netlife CAM - Corte/Reactivacion
 *	 
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 16-11-2020
 */

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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PRODUCTO INTERNET DEDICADO',
    '63',   
    'In-Corte',
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
