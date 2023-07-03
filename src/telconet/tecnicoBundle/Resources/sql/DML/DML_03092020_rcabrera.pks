/**
 *
 * Se crean parametros para el proyecto parametrizar distancia de cajas
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 03-09-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROYECTO PARAMETRIZAR DISTANCIA DE CAJAS',
    'PROYECTO PARAMETRIZAR DISTANCIA DE CAJAS',
    'TECNICO',
    'PROCESO DE FACTIBILIDAD',
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
  WHERE NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR DISTANCIA DE CAJAS';
     
    
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
    ln_id_param,
    'VALOR DE LA DISTANCIA USADO PARA LAS CAJAS',
    '250',
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

