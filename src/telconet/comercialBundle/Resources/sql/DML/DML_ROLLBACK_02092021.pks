----DELETE. 
DELETE
FROM
    DB_COMUNICACION.INFO_PLANTILLA_PREGUNTA
WHERE
    PLANTILLA_ID = 318 AND 
    PREGUNTA_ID = 
    (SELECT ID_PREGUNTA FROM DB_COMERCIAL.ADMI_PREGUNTa WHERE PREGUNTA = '¿El personal técnico le informó con que área se debe contactar para realizar las pruebas del servicio instalado?');
    
DELETE
FROM
    DB_COMUNICACION.INFO_PLANTILLA_PREGUNTA
WHERE
    PLANTILLA_ID = 318 AND 
    PREGUNTA_ID = 
    (SELECT ID_PREGUNTA FROM DB_COMERCIAL.ADMI_PREGUNTa WHERE PREGUNTA = 'NOMBRE Y APELLIDO DEL CONTACTO EN SITIO');   
           
DELETE
FROM
    DB_COMUNICACION.INFO_PLANTILLA_PREGUNTA
WHERE
    PLANTILLA_ID = 320 AND 
    PREGUNTA_ID = 
    (SELECT ID_PREGUNTA FROM DB_COMERCIAL.ADMI_PREGUNTa WHERE PREGUNTA = 'NOMBRE Y APELLIDO DEL CONTACTO EN SITIO');

DELETE FROM DB_COMUNICACION.ADMI_PREGUNTA WHERE PREGUNTA = 'NOMBRE Y APELLIDO DEL CONTACTO EN SITIO';

DELETE FROM DB_COMUNICACION.ADMI_PREGUNTA WHERE PREGUNTA = '¿El personal técnico le informó con que área se debe contactar para realizar las pruebas del servicio instalado?';

COMMIT;