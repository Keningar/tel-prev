DELETE
FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO = 'DATOS FWA'
  AND EMPRESA_COD      = 10
  AND ESTADO           = 'Activo'
  );

/
