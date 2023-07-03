--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo PYL y fin de tarea  GESTION DE INSTALACION ONLINE
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'PYL',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'GESTION DE INSTALACION ONLINE' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'cralarcon') and proceso_id = 544),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);

--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo GIS y fin de tarea Caja saturada reingenieria
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GIS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Caja saturada reingenieria' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'rcabrera') and proceso_id = 578),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
				
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo GIS y fin de tarea  Inspección de Urbanización
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GIS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Inspección de Urbanización' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'rcabrera') and proceso_id = 869),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo GIS y fin de tarea  Inspección de edificio
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GIS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Inspección de edificio' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'rcabrera') and proceso_id = 701),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo GIS y fin de tarea Inspección de Centro Comercial
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GIS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Inspección de Centro Comercial' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'rcabrera') and proceso_id = 869),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
				
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo FISCALIZACION y fin de tarea Trabajo mal realizado en caja BMX por instalacion
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'FISCALIZACION',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Trabajo mal realizado en caja BMX por instalacion' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'molmedo') and proceso_id = 973),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo FISCALIZACION y fin de tarea  Trabajo mal realizado en caja BMX por soporte
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'FISCALIZACION',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Trabajo mal realizado en caja BMX por soporte' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'molmedo') and proceso_id = 973),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo FISCALIZACION y fin de tarea  Trabajo mal realizado por arreglo de caja BMX
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'FISCALIZACION',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Trabajo mal realizado por arreglo de caja BMX' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'molmedo') and proceso_id = 973),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo FISCALIZACION y fin de tarea Trabajo mal realizado en nodo por soporte
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'FISCALIZACION',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Trabajo mal realizado en nodo por soporte' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'molmedo') and proceso_id = 973),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo FISCALIZACION y fin de tarea  Trabajo mal realizado en nodo instalacion cliente
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'FISCALIZACION',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Trabajo mal realizado en nodo instalacion cliente' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'molmedo') and proceso_id = 973),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  L3MPLS - ACTIVACION
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'L3MPLS - ACTIVACION' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'kjimenez') and proceso_id = 832),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea L3MPLS - CAMBIO CPE
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'L3MPLS - CAMBIO CPE' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'kjimenez') and proceso_id = 832),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		

--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  L3MPLS - CAMBIO CPE
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'L3MPLS - CAMBIO CPE' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'kjimenez') and proceso_id = 832),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  Habilitar Credenciales
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Habilitar Credenciales' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'vrodriguez') and proceso_id = 810),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  RETIRO DE EQUIPOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'RETIRO DE EQUIPOS' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'kjimenez') and proceso_id = 833),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea ERROR CON TAREAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'ERROR CON TAREAS' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'vrodriguez') and proceso_id = 836),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		

--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  MOBIL TECNICO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'MOBIL TECNICO' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'mmoreta') and proceso_id = 832),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		
		
		
--Ingresando la nueva categoría ADMINISTRATIVO con un Hijo SISTEMAS y fin de tarea  TAREAS PEDIDOS/DEVOLUCIONES
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'TAREAS PEDIDOS/DEVOLUCIONES' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez', 'mmoreta') and proceso_id = 1148),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);
		


--Ingresando la nueva categoría con un Hijo ELIMINAR REGISTRO LINEA PON
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GPON',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'ELIMINAR REGISTRO LINEA PON' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);

--Ingresando la nueva categoría con un Hijo AGREGAR POOL DE IP´s
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GPON',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'AGREGAR POOL DE IP´s' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);


--Ingresando la nueva categoría con un Hijo CAMBIO DE LINEA PON
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'GPON',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'CAMBIO DE LINEA PON' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        128,
        null,
        null,
        null,
        null);

--Ingresando la nueva categoría con un Hijo Informe Incidencias Interurbanas y telefónica
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'NOC',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Informe Incidencias Interurbanas y telefónica' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);


--Ingresando la nueva categoría con un Hijo Informe de Mantenimiento Interurbano y Telefónica
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'NOC',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Informe de Mantenimiento Interurbano y Telefónica' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);

--Ingresando la nueva categoría con un Hijo Informe de Recorrido Interurbanos y Telefónica
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'NOC',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Informe de Recorrido Interurbanos y Telefónica' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','oramirez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);



--Ingresando la nueva categoría con un Hijo Habilitar Credenciales
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Habilitar Credenciales' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','vrodriguez') and proceso_id = 810),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);
        


--Ingresando la nueva categoría con un Hijo Mobil Técnico
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Mobil Técnico' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','vrodriguez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);

--Ingresando la nueva categoría con un Pedidos/Devoluciones
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        960,
        'CATEGORIAS DE LAS TAREAS',
        'ADMINISTRATIVO',
        'SISTEMAS',
        (SELECT ID_TAREA
        FROM DB_SOPORTE.ADMI_TAREA
        WHERE NOMBRE_TAREA = 'Pedidos/Devoluciones' AND estado= 'Activo' and usr_creacion in ( 'lzambrano','caldaz','vrodriguez') and proceso_id = 628),
        'mantenimiento.png',
        'Activo',
        'rmoranc',
        sysdate,
        '192.168.1.1',
        null,
        null,
        null,
        116,
        null,
        null,
        null,
        null);


commit;

