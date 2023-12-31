
--Eliminando parametros.
--Eliminamos el detalle de parámetros

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN
  ( 
  SELECT ID_PARAMETRO 
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETRO_PRODUCTO_FACTIBILIDAD'
    AND CAB.ESTADO = 'Activo';
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'PARAMETRO_PRODUCTO_FACTIBILIDAD'
    AND CAB.ESTADO = 'Activo';


COMMIT;
