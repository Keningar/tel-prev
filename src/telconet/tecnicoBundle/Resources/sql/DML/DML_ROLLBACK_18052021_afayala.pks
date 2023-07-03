SET DEFINE OFF;

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS');

-- Eliminar en la tabla ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS';

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ELEMENTOS_PASIVO_RUTA')
AND VALOR1 = 'RESERVA';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ELEMENTOS_PASIVO_RUTA')
AND VALOR1 = 'CDP';

-- Eliminar parámetro de almacenamiento NFS
DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  WHERE  CODIGO_APP = 4
    AND  APLICACION = 'TelcosWeb'
    AND  PAIS='593'
    AND  EMPRESA='TN'
    AND  MODULO='Tecnico'
    AND  SUBMODULO='SubidaRutas'
    AND  ESTADO='Activo';

COMMIT;

/