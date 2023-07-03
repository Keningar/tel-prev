/**
 *
 * Se parametriza el valor del decode del campo estado_naf para la opcion: consulta de trazabilidad.
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 14-12-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'HERRAMIENTA CONSULTA DE TRAZABILIDAD',
    'HERRAMIENTA CONSULTA DE TRAZABILIDAD',
    'INFRAESTRUCTURA',
    'OPCION CONSULTA DE TRAZABILIDAD',
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
  WHERE NOMBRE_PARAMETRO = 'HERRAMIENTA CONSULTA DE TRAZABILIDAD';  

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
    'VALOR DECODE CAMPO ESTADO_NAF',
    'PendienteInstalar',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL
);  

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
