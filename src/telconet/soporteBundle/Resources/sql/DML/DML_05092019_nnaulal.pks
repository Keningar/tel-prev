--Ingreso de los seguimientos en el combo de ECUCERT
INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Cliente solicita que le expliquen la notificación','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Cliente indica que no posee la vulnerabilidad','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Vulnerabilidad se encuentra en el CPE del cliente','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'La vulnerabilidad sí está en los activos del cliente','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Cliente indica que toma acción','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Cliente indica que la información proporcionada no es suficiente','nnaulal',SYSDATE,'127.0.01');

INSERT INTO DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT(
  ID_SEGUIMIENTO_ECUCERT 
, SEGUIMIENTO  
, USR_CREACION 
, FE_CREACION 
, IP_CREACION) 
VALUES (DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT.NEXTVAL,
'Cliente solicita que se le vuelva a notificar','nnaulal',SYSDATE,'127.0.01');

COMMIT;

/


