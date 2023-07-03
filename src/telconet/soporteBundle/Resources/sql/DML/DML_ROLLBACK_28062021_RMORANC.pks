--ELIMINANDO CABECERA DE PREFIJOS_EMPRESA
DELETE FROM 
db_general.admi_parametro_det apdt
WHERE apdt.PARAMETRO_ID = (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PREFIJOS_EMPRESA'
            AND estado = 'Activo'
			AND usr_creacion = 'rmoranc');


--ELIMINANDO DETALLE DE PREFIJOS_EMPRESA
DELETE FROM 
db_general.admi_parametro_cab
WHERE 
nombre_parametro = 'PREFIJOS_EMPRESA'
AND estado = 'Activo'
AND usr_creacion = 'rmoranc';

commit;