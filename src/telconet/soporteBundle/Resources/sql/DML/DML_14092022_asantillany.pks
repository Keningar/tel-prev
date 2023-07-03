/**
 *
 * Se debe ejecutar en DB_GENERAL
 * Se crean parametros para la validacion de productos Ng Firewall
 *	 
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 15-09-2022
 */

/* PARAMETROS */
/* DB_GENERAL.ADMI_PARAMETRO_CAB */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
	ID_PARAMETRO, 
	NOMBRE_PARAMETRO, 
	DESCRIPCION, 
	MODULO, 
	PROCESO, 
	ESTADO, 
	USR_CREACION, 
	FE_CREACION, 
	IP_CREACION, 
	USR_ULT_MOD, 
	FE_ULT_MOD, 
	IP_ULT_MOD
)
VALUES
(
	DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
	'VALIDACION DE PRODUCTOS NG FIREWALL', 
	'PARAMETRO PARA VALIDACION', 
	'SOPORTE', 
	'FINALIZAR TAREA', 
	'Activo', 
	'asantillany', 
	SYSDATE, 
	'127.0.0.1', 
	'', 
	'', 
	''
);

/* DB_GENERAL.ADMI_PARAMETRO_DET */
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) 
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VALIDACION DE PRODUCTOS NG FIREWALL'),
    'NOMBRE DE LA CARACTERISTICA EN LA CUAL SE ASOCIAN LOS SERVICIOS A LAS TAREAS DE NG-FIREWALL',
    'ORQUESTADOR_SERVICIO_ID',   
    'Activo',
    'asantillany',
    SYSDATE,
    '127.0.0.1',
    'TN'
); 

COMMIT;
/