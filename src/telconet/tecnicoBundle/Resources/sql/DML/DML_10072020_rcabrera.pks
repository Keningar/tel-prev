/**
 *
 * Se crean parametros para el proyecto mejoras en creacion de rutas automaticas.
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 23-07-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN


--Creacion de Tarea: REVISION INCONSISTENCIA EN SUBRED
insert into db_soporte.admi_tarea values(db_soporte.seq_admi_tarea.nextval,839,null,null,null,1,0,
'REVISION INCONSISTENCIA EN SUBRED','REVISION INCONSISTENCIA EN SUBRED',1,'HORAS',1,1,'Activo','rcabrera',sysdate,'rcabrera',sysdate,null,null,null,'N','N');


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'MEJORAS PROCESO CREACION DE RUTAS AUTOMATICAS',
    'MEJORAS PROCESO CREACION DE RUTAS AUTOMATICAS',
    'INFRAESTRUCTURA',
    'CREACION DE RUTAS AUTOMATICAS',
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
  WHERE NOMBRE_PARAMETRO = 'MEJORAS PROCESO CREACION DE RUTAS AUTOMATICAS';
     
    
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
    'TAREA USADA PARA MEJORAS EN CREACION RUTAS AUTOMATICAS',
    'REVISION INCONSISTENCIA EN SUBRED',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'DEPARTAMENTO USADO PARA ASIGNAR TAREA',
    '115',
    '75',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'MENSAJE DE RESPUESTA DE NETWORKING',
    'PrimeraValidacionEnrutamientoPeSubredes',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'MENSAJE DE RESPUESTA DE NETWORKING',
    'SegundaValidacionEnrutamientoPeSubredes',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'MENSAJE DE RESPUESTA DE NETWORKING',
    'TerceraValidacionEnrutamientoPeSubredes',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'MENSAJE DE EXITO - RESPUESTA DE NETWORKING',
    'Enrutamiento listo para configurar',
    'Subred apta para crear ruta automática',
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
