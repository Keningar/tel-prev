DELETE
FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE CARACTERISTICA_ID IN
  (SELECT ID_CARACTERISTICA
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA'
  );
DELETE
FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR1 = '236,237,238,242,1155'
WHERE PARAMETRO_ID = 906;

COMMIT;
/






