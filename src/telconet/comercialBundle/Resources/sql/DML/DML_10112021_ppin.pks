/*
* Creamos un nuevo tipo de solicitud correspondiente al cambio de puerto para servicios GPON.
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0 10-11-2021
*
*/

INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD 
(
	ID_TIPO_SOLICITUD,
	DESCRIPCION_SOLICITUD,
	FE_CREACION,
	USR_CREACION,
	FE_ULT_MOD,
	USR_ULT_MOD,
	ESTADO,
	TAREA_ID,
	ITEM_MENU_ID,
	PROCESO_ID
)
VALUES
(
	db_comercial.SEQ_ADMI_TIPO_SOLICITUD.nextval,
	'SOLICITUD CAMBIO PUERTO',
	sysdate,
	'ppin',
	NULL,
	NULL,
	'Activo',
	NULL,
	NULL,
	NULL
);

COMMIT;
/
