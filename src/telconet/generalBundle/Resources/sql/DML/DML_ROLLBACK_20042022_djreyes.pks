----   ROLLBACK    ----
-- Estados permitidos para anular
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
		    where nombre_parametro = 'PROMOCION ANCHO BANDA')
and DESCRIPCION = 'Estados permitidos para anular la promocion'
and VALOR1 = 'PROM_BW'
and USR_CREACION = 'djreyes';


-- Todos los estados en planes
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR5 = ('Acivo,Clonado,Inactivo')
WHERE PARAMETRO_ID = (
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROMOCION ANCHO BANDA'
    AND ESTADO = 'Activo')
AND DESCRIPCION = 'Datos para consultas de planes para promocion'
AND VALOR1 = 'PROM_BW'
AND ESTADO = 'Activo'
AND USR_CREACION = 'djreyes';

-- Promociones que no realizan masivo
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
		    where nombre_parametro = 'PROM_TIPO_PROMOCIONES')
and DESCRIPCION = 'Promociones que no se ejecutaran en masivo'
and VALOR1 = 'PROM_BW'
and USR_CREACION = 'djreyes';

// Los estados al momento de editar
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
		    where nombre_parametro = 'PROMOCION ANCHO BANDA')
and DESCRIPCION = 'Estado inicial para editar promociones'
and VALOR1 = 'PROM_BW'
and USR_CREACION = 'djreyes';

COMMIT;
/
