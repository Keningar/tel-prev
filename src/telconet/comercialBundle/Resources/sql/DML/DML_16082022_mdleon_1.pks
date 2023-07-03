
/**
 * Documentación INSERT DE PARÁMETROS PARA FLUJO DE ORQUESTADOR
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET asi como las ADMI_CARACTERISTICA.
 *
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 16-08-2022
 */
  

insert into db_general.admi_parametro_cab
(id_parametro,nombre_parametro,descripcion,modulo,proceso,estado,usr_creacion,fe_creacion,ip_creacion)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'PARAMETROS_PRODUCTOS_ORQUESTADOR',
'PARAMETROS DE LOS PRODUCTOS QUE PERMITEN FLUJO DENTRO DEL ORQUESTADOR',
'COMERCIAL',
'CREACION_SERVICIO',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='PARAMETROS_PRODUCTOS_ORQUESTADOR'),
'PRODUCTOS_PERMITIDOS',
'SECURITY NG FIREWALL',
'1074',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10,
'Valor1: Producto, Valor2:idProducto'
);



 
insert into db_general.admi_parametro_cab
(id_parametro,nombre_parametro,descripcion,modulo,proceso,estado,usr_creacion,fe_creacion,ip_creacion)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'PARAMETROS_PARA_GENERAR_TAREA_L2',
'PARAMETROS PARA CREAR TAREA PARA ACTIVACION DE PRODUCTOS SIN FLUJO',
'COMERCIAL',
'TAREA_PARA_ACTIVACION',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='PARAMETROS_PARA_GENERAR_TAREA_L2'),
'TAREA_PROCESO',
'SECURITY NG FIREWALL',
'R1',
'IPCCL2 - ACTIVACION  SECURITY',
'SECURITY EQUIPMENT',
'jcastillo',
'1597971',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10,
'Valor1: Producto, Valor2: Region, Valor3: nombre del proceso, Valor4: nombre de la tarea, Valor5: usuario asignar, Valor6: idPersonaRol'
);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,VALOR5,VALOR6,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='PARAMETROS_PARA_GENERAR_TAREA_L2'),
'TAREA_PROCESO',
'SECURITY NG FIREWALL',
'R2',
'IPCCL2 - ACTIVACION  SECURITY',
'SECURITY EQUIPMENT',
'rrubio',
'238125',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10,
'Valor1: Producto, Valor2: Region, Valor3: nombre del proceso, Valor4: nombre de la tarea, Valor5: usuario asignar, Valor6: idPersonaRol'
);

insert into db_general.admi_parametro_cab
(id_parametro,nombre_parametro,descripcion,modulo,proceso,estado,usr_creacion,fe_creacion,ip_creacion)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'OBSERVACION_SEGUIMIENTO_L2',
'PARAMETROS PARA CREAR UN SEGUIMIENTO A UNA TAREA AUTOMATICA L2',
'COMERCIAL',
'TAREA_SEGUIMIENTO',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='OBSERVACION_SEGUIMIENTO_L2'),
'PROCESO_SEGUIMIENTO',
'Se registro información del equipo, servicio cambia de estado Pendiente a Activo',
'CREAR_SEGUIMIENTO',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10,
'Valor1: observacion de seguimiento'
);

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'INSTANCIA_ID_ORQ',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );
  
  
 INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    VALUES(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='SECURITY NG FIREWALL' AND EMPRESA_COD=10 AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='INSTANCIA_ID_ORQ'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    );  

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'DOC_ANEXO_TECNICO',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );
  
  
 INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    VALUES(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='SECURITY NG FIREWALL' AND EMPRESA_COD=10 AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='DOC_ANEXO_TECNICO'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    );  

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TAREA_ACTIVACION_SECURITY',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );
  
  
 INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    VALUES(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='SECURITY NG FIREWALL' AND EMPRESA_COD=10 AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='TAREA_ACTIVACION_SECURITY'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    );  


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'OBSERVACION_TAREA_SECURITY',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );
  
  
 INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    VALUES(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='SECURITY NG FIREWALL' AND EMPRESA_COD=10 AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='OBSERVACION_TAREA_SECURITY'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    );  

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ORQUESTADOR_SERVICIO_ID',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );

insert into db_general.admi_parametro_cab
(id_parametro,nombre_parametro,descripcion,modulo,proceso,estado,usr_creacion,fe_creacion,ip_creacion)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'DATOS_ORQUESTADOR_APIKEY',
'APIKEY PARA CONECTAR CON ORQUESTADOR',
'COMERCIAL',
'APIKEY',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD,OBSERVACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='DATOS_ORQUESTADOR_APIKEY'),
'APIKEY',
'BXwTiN9cK5fehAKPwpYXH0IARtLpNuGB%2Bdn7N3nLuTwl3wFx3u%2F4quDG8GNR9pRySnKis1iONg%2BeVqggw2M%2F0TFol%2F7hjUl9x4SlGfUzIxG5YfC2q5SnJkDlwyJky%2Bq2Xv62Fs7EW40HeUlu3qGrlJMAd0vAhy0QF%2FtcZ8NrgR2c2rR9qmfpinrvVgp3JwtXKILHKaOyr4luGOldIBxRRpwKowGKbJ61CnhXYeCaVRX%2BirLVm%2BRGqrOlUZr%2B0CpIiCj8099wjczShftMRZ7B%2BVvoj3rCPmIYsQkgxdz6KIf5VyIPyFoThwMXb3HZr3hiuvmX%2Fm1Tn5llssyLoAXKwdGQoZt%2B9f2mOcXspbD9cbdXxlU%2FGReX6lx3FjCRd97sFqslMPbgTbPHtj%2Bp1g%3D%3D',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10,
'Valor1: ApiKey'
);

COMMIT;
/