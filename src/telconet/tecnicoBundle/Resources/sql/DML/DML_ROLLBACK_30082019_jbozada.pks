DELETE
FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE CARACTERISTICA_ID IN
  (SELECT ID_CARACTERISTICA
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA IN 
  ('PERFIL-PROMO',
   'TRAFFIC-TABLE-PROMO', 
   'GEM-PORT-PROMO', 
   'LINE-PROFILE-NAME-PROMO',
   'CAPACIDAD1-PROMO', 
   'CAPACIDAD2-PROMO', 
   'AB-PROMO', 
   'REINTENTO-PROMO', 
   'PROCESO-PROMO', 
   'CONFIGURA-PROMO',
   'VALOR-PROMO')
  );
DELETE
FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA IN 
  ('PERFIL-PROMO',
   'TRAFFIC-TABLE-PROMO', 
   'GEM-PORT-PROMO', 
   'LINE-PROFILE-NAME-PROMO',
   'CAPACIDAD1-PROMO', 
   'CAPACIDAD2-PROMO', 
   'AB-PROMO', 
   'REINTENTO-PROMO', 
   'PROCESO-PROMO', 
   'CONFIGURA-PROMO',
   'VALOR-PROMO') ;


DELETE
FROM DB_SOPORTE.ADMI_PROCESO_EMPRESA
WHERE  PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE IP CONTACT CENTER - Reintento Promo BW');


DELETE
FROM DB_SOPORTE.ADMI_TAREA
WHERE  PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE IP CONTACT CENTER - Reintento Promo BW');

DELETE
FROM DB_SOPORTE.ADMI_PROCESO
WHERE NOMBRE_PROCESO='TAREAS DE IP CONTACT CENTER - Reintento Promo BW';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='NOTIF_PROMO_BW'
  AND ESTADO = 'Activo');


DELETE
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='NOTIF_PROMO_BW'
  AND ESTADO = 'Activo';


DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='ASIGNAR_TAREA_JEFES_IPCC_MD'
  AND ESTADO = 'Activo');


DELETE
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='ASIGNAR_TAREA_JEFES_IPCC_MD'
  AND ESTADO = 'Activo';

COMMIT;
/





