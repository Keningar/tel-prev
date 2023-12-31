--1
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CAMBIO_LINEA_PON_DATOS_NW'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/
--2 
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CAMBIO_LINEA_PON_DATOS_SERVICIOS'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/  
--3
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CAMBIO_LINEA_PON_DATOS_SERVICIOS_2'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/
--4
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CAMBIO_LINEA_PON_R1_NOTIFICACION'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/
--5
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CAMBIO_LINEA_PON_R2_NOTIFICACION'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/ 
--6  
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE descripcion = 'DATOS_CREAR_TAREA'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/
--7
DELETE FROM DB_GENERAL.admi_parametro_cab 
  WHERE  nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
/


DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
  WHERE CODIGO = 'SAFECITYCAN'
  AND USR_CREACION = 'mcarpio'; 
  
/

--8
DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'Cambio de linea pon DATOS GPON SAFE CITY'
    AND PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITUD CAMBIO DE LINEA PON');
/
--9
DELETE DB_SOPORTE.ADMI_PROCESO_EMPRESA
WHERE
    PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITUD CAMBIO DE LINEA PON');
/
--10
DELETE DB_SOPORTE.ADMI_PROCESO
WHERE NOMBRE_PROCESO = 'SOLICITUD CAMBIO DE LINEA PON';
  
COMMIT;
