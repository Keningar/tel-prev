/**
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0
 * @since 02-12-2021    
 * Se crea DML para registrar los mensajes parametrizados al momento de realizar un reenvío de credenciales para el canal del futbol
 */


/* PARAMETROS */
/* CREACIÓN DEL PARÁMETRO CAB  - MENSAJES_REENVIO_CREDENCIALES_ECDF*/
INSERT INTO db_general.ADMI_PARAMETRO_CAB 
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
VALUES (db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'MENSAJES_REENVIO_CREDENCIALES_ECDF',
        'MENSAJES QUE SE MUESTRAN CUANDO SE REALICE UN REENVIO DE CREDENCIALES PARA EL CANAL DEL FUTBOL',
        'TECNICO','MENSAJES_REENVIO_CREDENCIALES_ECDF','Activo','farias',SYSDATE,'127.0.0.1',NULL,NULL,NULL);
        
COMMIT;

/* DB_GENERAL.ADMI_PARAMETRO_DET */
/* INSERT DE LOS MENSAJES PARAMETRIZADOS*/

--1
--INSERT
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'MENSAJES_REENVIO_CREDENCIALES_ECDF'),
    'MENSAJES_REENVIO_CREDENCIALES_ECDF','Servicio In-Corte','Registra valores pendientes de pago',
    'El servicio se encuentra cancelado. Comuníquese con su proveedor.',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

COMMIT;
/


