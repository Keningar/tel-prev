/**
 *
 * Se parametriza valores para la asignación de subredes por Pe
 * Se parametriza tarea para IPCCL2
 * Se parametriza mensaje para el correo dirigido a IPCCL2
 * Se parametriza caracteristica para el servico de Internet Dedicado
 * @author Jonathan Montecé <jmontece@telconet.ec>
 * @version 1.0 03-09-2021
 */

-------------------TIPOS DE ELEMENTO PARA SUBREDES POR PE
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'TIPOS_ELEMENTO_SUBREDES',
    'PARAMETRIZACION PARA BUSQUEDA DE PE EN SUBREDES',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );
  
  
  
  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS_ELEMENTO_SUBREDES'),
    'QUERY LIKE PE EN SUBREDES',
    'pe%',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS_ELEMENTO_SUBREDES'),
    'QUERY LIKE RO EN SUBREDES',
    'ro%',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS_ELEMENTO_SUBREDES'),
    'QUERY TIPO ELEMENTO PE EN SUBREDES',
    'ROUTER',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
-------------------ESTADOS PARA SUBREDES POR PE
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ESTADOS_SUBRED_PE',
    'PARAMETRIZACION DE ESTADOS PARA SUBREDES POR PE',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );
  
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_SUBRED_PE'),
    'ESTADOS SUBREDES POR PE',
    'Activo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_SUBRED_PE'),
    'ESTADOS SUBREDES POR PE',
    'Ocupado',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_SUBRED_PE'),
    'ESTADOS SUBREDES POR PE',
    'Reservada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

-------------------TIPO DE RED WAN Y LAN
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'TIPO_RED',
    'PARAMETRO PARA EL TIPO DE RED WAN O LAN EN SUBREDES POR PE',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_RED'),
    'TIPOS DE RED SUBREDES POR PE',
    'LAN',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_RED'),
    'TIPOS DE RED SUBREDES POR PE',
    'WAN',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

------------------USO INTMPLS
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'TIPO_USO',
    'PARAMETRO PARA EL USO DE RED EN SUBREDES POR PE',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_USO'),
    'TIPOS DE USO EN SUBREDES POR PE',
    'INTMPLS',
    '0',
    '0',
    '0',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

------------------MASCARA
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'MASCARA_SUBREDES',
    'PARAMETRO PARA LA MASCARA DE RED EN SUBREDES POR PE',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/24',
    '255.255.255.0',
    '254',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/25',
    '255.255.255.128',
    '126',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/26',
    '255.255.255.192',
    '62',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/27',
    '255.255.255.224',
    '30',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/28',
    '255.255.255.240',
    '14',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/29',
    '255.255.255.248',
    '6',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),
    'MASCARAS DE RED PARA SUBREDES POR PE',
    '/30',
    '255.255.255.252',
    '2',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

------------------TRUE DEL WS
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'CONFIGURACION_WS_PROVINCIAS',
    'CONFIGURACION DE WS PARA APROVISIONAMIENTO EN PROVINCIAS',
    'TECNICO',
    'SUBREDES_PE',
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'RESPUESTA NETWORKING PROVINCIAS',
    'true',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

-------- DETALLES PARAMETRIZACION EN ASIGNACION DE RECURSOS DE RED
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'LLAMAR_WS_PROVINCIAS',
    'S',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'LLAMAR_WS_GYE_UIO',
    'N',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

--------- DETALLES  PARA CONFIGURACION DE NOMBRE DE LA TAREA A L2 
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'NOMBRE_TAREA_L2',
    'REVISION INCONSISTENCIA EN IP ASIGNACION DE RECURSOS DE RED',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'DEPARTAMENTO_TAREA_RECURSOS_DE_RED',
    '115',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

--------- DETALLES PARA CONFIGURACION DE MENSAJE A ENVIAR PARA CONOCIMIENTO DE L2
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'MENSAJE_IP_OCUPADA_UNO',
    'La ip:',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
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
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_PROVINCIAS'),
    'MENSAJE_IP_OCUPADA_DOS',
    'ya se encuentra ocupada, favor de regularizar en el telcos',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmontece',
    SYSDATE,
    '127.0.0.1',
    '10'
  );

-------------------CARACTERISTICA QUE DEBE TENER EL SERVICO PARA QUE SE PROCESE Y PRESENTE LA PANTALLA ANTIGUA O NUEVA EN LA ASIGNACIÓN DE RECURSOS DE RED PROVINCIAS
-------------------PRIMERO SE REALIZA EL INSERT PARA ADMI_CARACTERISTICA 
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    tipo
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'VENTANA_RECURSOS_RED_PROVINCIAS',
    'S',
    'Activo',
    SYSDATE,
    'jmontece',
    'TECNICA'
  );

-------------------SEGUNDO SE REALIZA EL INSERT PARA ADMI_PRODUCTO_CARACTERISTICA
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    usr_creacion,
    estado,
    visible_comercial
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    '242',
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'VENTANA_RECURSOS_RED_PROVINCIAS'),
    SYSDATE,
    'jmontece',
    'Activo',
    'NO'
  );

  
------INSERT PARA LA TAREA A L2, EN LLAMADO DE WS EN LA ASIGNACION DE RECURSOS DE RED
INSERT
INTO DB_soporte.admi_tarea VALUES
  (
    DB_soporte.SEQ_ADMI_TAREA.nextval,
    '1234',
    NULL,
    NULL,
    NULL,
    '1',
    '1',
    'REVISION INCONSISTENCIA EN IP ASIGNACION DE RECURSOS DE RED',
    'REVISION INCONSISTENCIA EN IP ASIGNACION DE RECURSOS DE RED',
    '1',
    'HORAS',
    '1',
    '1',
    'Activo',
    'jmontece',
    sysdate,
    'jmontece',
    sysdate,
    NULL,
    NULL,
    NULL,
    NULL,
    'N'
  );

COMMIT;

/
