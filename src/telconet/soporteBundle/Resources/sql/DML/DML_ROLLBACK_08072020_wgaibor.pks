UPDATE DB_GENERAL.admi_parametro_det 
set estado = 'Activo',
usr_ult_mod = 'wgaibor',
fe_ult_mod = sysdate
WHERE parametro_id = (SELECT
    id_parametro
FROM db_general.admi_parametro_cab
WHERE nombre_parametro = 'CONTACTOS_L1');
--
DELETE FROM DB_GENERAL.admi_parametro_det 
WHERE parametro_id = (SELECT
    id_parametro
FROM db_general.admi_parametro_cab
WHERE nombre_parametro = 'CONTACTOS_L1')
and valor1 = 'IPCCL1';

--Número del dpto L1 Nacional
DELETE FROM db_general.admi_parametro_det
WHERE parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUMERO_CONTACTO_L1'
    );
--
DELETE FROM db_general.admi_parametro_cab
WHERE id_parametro = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUMERO_CONTACTO_L1'
    );
--SINTOMAS DE CASOS QUE NO SE DEBEN MOSTRAR EN LA APP TELCO MANAGER
DELETE FROM db_general.admi_parametro_det
WHERE parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'SINTOMAS_CASO_TELCO_MANAGER'
    );
--
DELETE FROM db_general.admi_parametro_cab
WHERE id_parametro = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'SINTOMAS_CASO_TELCO_MANAGER'
    );

/*MOSTRAR SOLO LOS CASOS DEL DÍA ACTUAL EN LA APP TELCO MANAGER*/
DELETE FROM db_general.admi_parametro_det
WHERE parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'VISUALIZAR_CASOS_DEL_DIA_TM'
    );
--
DELETE FROM db_general.admi_parametro_cab
WHERE id_parametro = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'VISUALIZAR_CASOS_DEL_DIA_TM'
    );
--
COMMIT;

/
