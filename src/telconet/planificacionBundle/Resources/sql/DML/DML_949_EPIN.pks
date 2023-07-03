delete from db_comercial.info_cupo_planificacion
 where fe_inicio>=to_date(to_char(sysdate,'dd/mm/rrrr')||' 00:00:00','dd/mm/rrrr hh24:mi:ss')
 and solicitud_Id is  null;

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 'PRODUCTO CONTROLA CUPO', 'T', 'Activo', SYSDATE, 'epin', null, null, 'TECNICA');

INSERT INTO "DB_COMERCIAL"."ADMI_PRODUCTO_CARACTERISTICA"
VALUES("DB_COMERCIAL"."SEQ_ADMI_PRODUCTO_CARAC".NEXTVAL,
       (SELECT ID_PRODUCTO
       FROM ADMI_PRODUCTO
       WHERE DESCRIPCION_PRODUCTO = 'NETVOICE'),
       (SELECT ID_CARACTERISTICA
       FROM ADMI_CARACTERISTICA
       WHERE DESCRIPCION_CARACTERISTICA = 'PRODUCTO CONTROLA CUPO'),
       SYSDATE, NULL, 'epin', null, 'Activo', 'NO');

INSERT INTO "DB_COMERCIAL"."ADMI_PRODUCTO_CARACTERISTICA"
VALUES("DB_COMERCIAL"."SEQ_ADMI_PRODUCTO_CARAC".NEXTVAL,
       (SELECT ID_PRODUCTO
       FROM ADMI_PRODUCTO
       WHERE DESCRIPCION_PRODUCTO = 'Renta SmartWiFi (Aironet1602)'),
       (SELECT ID_CARACTERISTICA
       FROM ADMI_CARACTERISTICA
       WHERE DESCRIPCION_CARACTERISTICA = 'PRODUCTO CONTROLA CUPO'),
       SYSDATE, NULL, 'epin', null, 'Activo', 'NO');

UPDATE DB_INFRAESTRUCTURA.ADMI_JURISDICCION
   SET CUPO = 0,
       CUPO_MOBILE = 0;
UPDATE DB_INFRAESTRUCTURA.ADMI_JURISDICCION
   SET CUPO = 1
WHERE NOMBRE_JURISDICCION IN ('MEGADATOS GUAYAQUIL','MEGADATOS QUITO');
COMMIT;

