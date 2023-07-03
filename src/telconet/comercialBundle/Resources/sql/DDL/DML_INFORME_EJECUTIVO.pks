grant select on NAF47_TNET.V_EMPLEADOS_EMPRESAS to db_soporte;
grant select on NAF47_TNET.V_EMPLEADOS_EMPRESAS to db_comercial;
grant execute on db_comercial.comek_consultas to db_soporte;



  -------inserto las preguntas
  
  
INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'CLIENTE AFECTADO', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'SERVICIOS AFECTADOS', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  ); 

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'PROBLEMA REPORTADO', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  ); 

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'FECHA Y  HORA DE INICIO', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  ); 

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'FECHA Y  HORA DE FINALIZACIÓN', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'NO. DE CASO/TAREAS', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'DEPARTAMENTO ASIGNADO', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'ANÁLISIS DE PROBLEMA', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'ACCIONES CORRECTIVAS', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'PLAN DE MEJORAMIENTO', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );

INSERT
  INTO DB_COMUNICACION.admi_pregunta  (    id_pregunta,    pregunta,    tipo_respuesta,    descripcion,    estado,    fe_creacion,    usr_creacion )
VALUES (db_comunicacion.seq_admi_pregunta.nextval, 'STATUS ACTUAL', 'TEXTO', 'INFORME EJECUTIVO TECNICO', 'Activo', sysdate,  'javera'  );


