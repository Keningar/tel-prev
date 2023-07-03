/**
 *
 * Se crea parametro para registrar la lista de productos permitidos  en la Herramienta: Reversar Orden de Trabajo
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 11-06-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO',
    'PARAMETROS PROYECTO PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO',
    'INFRAESTRUCTURA',
    'REVERSAR ORDEN DE TRABAJO',
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
  WHERE NOMBRE_PARAMETRO = 'PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO';
     
    
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
    'LISTADO PRODUCTOS PERMITIDOS',
    '276|',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
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
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'MAPEO DE PRODUCTOS Y ESTADOS',
    '276',
    'PrePlanificada',
    'Finalizada',
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
