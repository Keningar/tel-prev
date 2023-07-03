DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'DATA_CLIENTE_MASIVO'
    AND DET.ESTADO = 'Activo'
  );
DELETE
FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA
WHERE PLANTILLA_ID = 
(SELECT ID_PLANTILLA
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='DCM_MD'
  AND ESTADO = 'Activo');
DELETE
FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'DCM_MD';
COMMIT;
/