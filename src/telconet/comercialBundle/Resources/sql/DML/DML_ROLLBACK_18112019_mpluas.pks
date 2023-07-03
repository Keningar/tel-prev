
/*
* Se crean script de reverso de las caracterisitcas User3dEYE y Rol3dEYE.
* @author Marlon Plúas <mpluas@telconet.ec>
* @version 1.0 18-11-2019
*/

Delete From DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
Where PRODUCTO_ID=(SELECT ID_PRODUCTO 
     			FROM DB_COMERCIAL.ADMI_PRODUCTO 
     			WHERE NOMBRE_TECNICO = 'CAMARA IP'
     			AND ESTADO = 'Activo')
And CARACTERISTICA_ID=(SELECT ID_CARACTERISTICA 
     			FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     			WHERE DESCRIPCION_CARACTERISTICA = 'USER 3DEYE'
     			AND ESTADO = 'Activo')
And ESTADO = 'Activo';


Delete From DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
Where PRODUCTO_ID=(SELECT ID_PRODUCTO 
     			FROM DB_COMERCIAL.ADMI_PRODUCTO 
     			WHERE NOMBRE_TECNICO = 'CAMARA IP'
     			AND ESTADO = 'Activo')
And CARACTERISTICA_ID=(SELECT ID_CARACTERISTICA 
     			FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     			WHERE DESCRIPCION_CARACTERISTICA = 'ROL 3DEYE'
     			AND ESTADO = 'Activo')
And ESTADO = 'Activo';


Delete From DB_COMERCIAL.ADMI_CARACTERISTICA
Where DESCRIPCION_CARACTERISTICA='USER 3DEYE' And ESTADO = 'Activo';

Delete From DB_COMERCIAL.ADMI_CARACTERISTICA
Where DESCRIPCION_CARACTERISTICA='ROL 3DEYE' And ESTADO = 'Activo';

UPDATE
	DB_COMERCIAL.ADMI_PRODUCTO
SET
	ESTADO = 'Inactivo'
	WHERE NOMBRE_TECNICO = 'CAMARA IP';

UPDATE
	DB_COMERCIAL.INFO_PLAN_DET
SET
	ESTADO = 'Inactivo'
WHERE
	PRODUCTO_ID = (
	SELECT
		ID_PRODUCTO
	FROM
		DB_COMERCIAL.ADMI_PRODUCTO
	WHERE
		NOMBRE_TECNICO = 'CAMARA IP');

UPDATE
	DB_COMERCIAL.INFO_PLAN_DET
SET
	ESTADO = 'Activo'
WHERE
	PRODUCTO_ID != (
	SELECT
		ID_PRODUCTO
	FROM
		DB_COMERCIAL.ADMI_PRODUCTO
	WHERE
		NOMBRE_TECNICO = 'CAMARA IP')
	AND PLAN_ID = (
	SELECT
		ID_PLAN
	FROM
		DB_COMERCIAL.INFO_PLAN_CAB
	WHERE
		NOMBRE_PLAN = 'Netlifecam ST'
		AND ESTADO = 'Activo')
	AND ESTADO = 'Activo';

COMMIT;

/

