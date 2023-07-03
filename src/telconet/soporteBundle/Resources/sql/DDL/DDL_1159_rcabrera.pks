/* Esta es una tabla temporal que sirve para el envió de notificaciones de casos tipo Backbone, la cual contiene información de logines y correos a notificar */

CREATE TABLE DB_SOPORTE.TEMP_NOTIF_BACKBONE
(ID_TEMP NUMBER,CASO_ID NUMBER,NUMERO_CASO VARCHAR2(50),CADENA_LOGIN VARCHAR2(4000),CADENA_CORREO VARCHAR2(4000));
    
CREATE SEQUENCE DB_SOPORTE.SEQ_TEMP_NOTIF_BACKBONE 
INCREMENT BY 1 
MAXVALUE 9999999999999999999999999999 
MINVALUE 1 
NOCACHE;