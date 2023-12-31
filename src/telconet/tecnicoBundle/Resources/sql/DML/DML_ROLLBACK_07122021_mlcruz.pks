DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'FINALIZA_PROCESOS_MASIVOS_POR_OPCION'
    AND DET.VALOR2 = 'REACTIVACION_INDIVIDUAL_INTERNET'
    AND DET.ESTADO = 'Activo'
  );
COMMIT;
/