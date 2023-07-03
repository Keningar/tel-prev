/**
 *
 * Se crean parametros para el proyecto Paramount Fase 2: Formulario de Soporte L1
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 06-12-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROYECTO INTEGRACION PARAMOUNT';  

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
    'TAREA USADA PARA FORMULARIO DE SOPORTE L1',
    'Paramount+ y Noggin',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
);  

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'DATOS USADOS PARA ENVIO DE CORREO DEL FORMULARIO DE SOPORTE L1',
    'adrian.martini@vimn.com',
    'soporte@netlife.net.ec',
    'Formulario de Soporte L1',
    '75',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    'GRAVEDAD-PROBLEMA',
    'bajo',
    'Bajo',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    'GRAVEDAD-PROBLEMA',
    'medio',
    'Medio',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    'GRAVEDAD-PROBLEMA',
    'alto',
    'Alto',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    'CATEGORIA-FORMULARIO',
    '2',
    'Quejas de Clientes',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    'CATEGORIA-FORMULARIO',
    '1',
    'Registro Interno',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
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
    ln_id_param,
    'EXTENSIONES_PERMITIDAS',
    'png,jpg,jpeg',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    18
); 



COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
