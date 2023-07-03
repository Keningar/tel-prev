DELETE FROM DB_GENERAL.admi_parametro_det
    where parametro_id IN (SELECT
    id_parametro FROM DB_GENERAL.admi_parametro_cab
    where nombre_parametro in ('PLANIFICACION_TIPOS','PLANIFICACION_ESTADOS'));

DELETE FROM DB_GENERAL.admi_parametro_cab
    where nombre_parametro in ('PLANIFICACION_TIPOS','PLANIFICACION_ESTADOS');

--CAB CANT_DIA_MAX_PLANIFICACION
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CANT_DIA_MAX_PLANIFICACION'
    );

--
--DET CANT_DIA_MAX_PLANIFICACION
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'CANT_DIA_MAX_PLANIFICACION';

--CAB TIEMPO_BANDEJA_PLAN_AUTOMATICA
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'TIEMPO_BANDEJA_PLAN_AUTOMATICA'
    );

--
--DET TIEMPO_BANDEJA_PLAN_AUTOMATICA
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'TIEMPO_BANDEJA_PLAN_AUTOMATICA';

-- PROGRAMAR_MOTIVO_HAL
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROGRAMAR_MOTIVO_HAL'
    );

--
--DET PROGRAMAR_MOTIVO_HAL
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'PROGRAMAR_MOTIVO_HAL';

COMMIT;