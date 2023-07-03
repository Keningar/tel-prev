--Se habilita la creación de una solicitud masiva para servicios Internet Small Business y se agrega los ; en la función precio
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET SOPORTE_MASIVO   = 'S',
  FUNCION_PRECIO     = 'if ([VELOCIDAD]==20) { PRECIO=150; } else if ([VELOCIDAD]==50) { PRECIO = 250; }'
WHERE NOMBRE_TECNICO = 'INTERNET SMALL BUSINESS'
AND ESTADO           = 'Activo';
COMMIT;
