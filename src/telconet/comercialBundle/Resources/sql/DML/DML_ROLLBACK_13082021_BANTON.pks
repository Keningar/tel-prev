DELETE FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
WHERE PRODUCTO_CARACTERISITICA_ID IN (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='Relacionar Proyecto'));

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='Relacionar Proyecto');  

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA='Relacionar Proyecto';


COMMIT;
/
