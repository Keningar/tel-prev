-- Agregando el progreso de materiales a tareas de instalacion TN 849
Insert into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE (
	ID_PROGRESO_PORCENTAJE,
	PORCENTAJE,
	TIPO_PROGRESO_ID,
	TAREA_ID,
	ESTADO,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION,
	USR_ULT_MOD,
	FE_ULT_MOD,
	ORDEN,
	EMPRESA_ID
) values (
	'59',
	'10',
	'5',
	'849',
	'Activo',
	'admin',
	to_date('26/11/2018','DD/MM/RRRR'),
	'127.0.0.1',
	null,
	to_date('26/11/2018','DD/MM/RRRR'),
	'11',
	'10'
);

-- Actualizando el valor del  porcentaje de tarea de instalacion TN 849
UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" 
SET PORCENTAJE = '10' 
WHERE ID_PROGRESO_PORCENTAJE = 27;

-- Actualizando el valor del  porcentaje de tarea de instalacion TN 849
UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" 
SET PORCENTAJE = '10' 
WHERE ID_PROGRESO_PORCENTAJE = 28;


-- Agregando el progreso de materiales a tareas de instalacion TN 850
Insert into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE (
	ID_PROGRESO_PORCENTAJE,
	PORCENTAJE,
	TIPO_PROGRESO_ID,
	TAREA_ID,
	ESTADO,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION,
	USR_ULT_MOD,
	FE_ULT_MOD,
	ORDEN,
	EMPRESA_ID
) values (
	'60',
	'10',
	'5',
	'850',
	'Activo',
	'admin',
	to_date('10/12/2018','DD/MM/RRRR'),
	'127.0.0.1',
	null,
	to_date('10/12/2018','DD/MM/RRRR'),
	'7',
	'10'
);

-- Actualizando el valor del porcentaje de tarea de instalacion TN 850
UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" 
SET PORCENTAJE = '20' 
WHERE ID_PROGRESO_PORCENTAJE = 49;

commit;
/