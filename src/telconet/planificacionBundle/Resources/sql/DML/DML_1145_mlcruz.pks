--Se actualiza la oficina_id en la jurisdicción de Telconet - Galapagos
--El cambio realizado es de la oficina 2 ->  TELCONET - Guayaquil a la oficina 7 -> TELCONET - Quito
UPDATE DB_INFRAESTRUCTURA.ADMI_JURISDICCION
SET OFICINA_ID = 7
WHERE ID_JURISDICCION = 264;

--Se actualiza la oficina de referencia a TELCONET - Quito
--Las oficinas actualizadas serán 16 -> TELCONET - Huaquillas, 26 -> TELCONET - Isabela, 28 -> TELCONET - Babahoyo, 29 -> TELCONET - San Cristobal
UPDATE INFO_OFICINA_GRUPO
SET REF_OFICINA_ID = 7
WHERE ID_OFICINA IN (16, 26, 28, 29);

--Se actualiza la oficina referencia de TELCONET - CTO de null a TELCONET - Guayaquil
UPDATE INFO_OFICINA_GRUPO
SET REF_OFICINA_ID = 2
WHERE ID_OFICINA = 225;

--Se actualiza la región en todas las ciudades de Ecuador excepto Guayaquil
UPDATE DB_GENERAL.ADMI_CANTON
SET REGION = 'R2'
WHERE ID_CANTON IN(
        SELECT CA.ID_CANTON
        FROM DB_GENERAL.ADMI_CANTON CA
        LEFT JOIN DB_GENERAL.ADMI_PROVINCIA PR ON PR.ID_PROVINCIA = CA.PROVINCIA_ID
        LEFT JOIN DB_GENERAL.ADMI_REGION RE ON RE.ID_REGION = PR.REGION_ID
        LEFT JOIN DB_GENERAL.ADMI_PAIS PA ON PA.ID_PAIS = RE.PAIS_ID
        WHERE CA.REGION = 'R1'
        AND CA.ESTADO = 'Activo'
        AND CA.NOMBRE_CANTON <> 'GUAYAQUIL'
        AND PA.ID_PAIS = '1'
);
COMMIT;
