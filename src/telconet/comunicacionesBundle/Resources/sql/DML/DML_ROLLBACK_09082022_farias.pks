--PLANTILLAS CORREO 
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO IN ('HBO-MAX-NUEVO', 'HBO-MAX-REST', 'HBO-MAX-CF-ACT', 'HBO-MAX-CF-REST');

COMMIT;
/