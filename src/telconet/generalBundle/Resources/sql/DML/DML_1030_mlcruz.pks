SET DEFINE OFF;
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR4         = 'Tarea autom&aacute;tica por aprovisionamiento de servicio IP'
WHERE PARAMETRO_ID =
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'INFO_NOTIF_IPSB'
  AND ESTADO             = 'Activo'
  )
AND ESTADO = 'Activo';
COMMIT;


