-- Parametros de semaforo
INSERT INTO DB_MONITOREO.GPS_PARAMETROS (ID_PARAMETRO,DESCRIPCION,VALOR,ESTADO,USR_CREACION, FE_CREACION , IP_CREACION )
	VALUES (1,'TIEMPO_SEMAFORO','{"tiempo_semaforo_rojo":"5","tiempo_semaforo_amarillo":"5","tiempo_semaforo_verde":"5"}','Activo','wvera', SYSDATE, '192.168.1');


COMMIT
/