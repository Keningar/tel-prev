-- Ingreso de campo medidor electrico
ALTER TABLE DB_INFRAESTRUCTURA.INFO_MEDIDOR ADD MEDIDOR_ELECTRICO VARCHAR2(200);
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_MEDIDOR.MEDIDOR_ELECTRICO
IS
  'Medidor Eléctrico CUENTA CONTRATO,CUEN,SUMINISTROS,CUENTAS';
-- Vista donde genera consulta de un nodo específico
CREATE OR REPLACE FORCE VIEW "DB_INFRAESTRUCTURA"."VISTA_INFO_NODOS" ("ID_ELEMENTO", "NOMBRE_ELEMENTO", "NOMBRE_MODELO_ELEMENTO", "ACCESO_PERMANENTE", "OBSERVACION", "ESTADO_ELEMENTO", "USR_CREACION", "FE_CREACION", "CLASE", "TORRE", "COBERTURA", "TIPO_MEDIO", "ID_SOLICITUD", "ESTADO_SOLICITUD", "DESCRIPCION", "FE_EJECUCION", "FE_RECHAZO", "ID_MOTIVO", "NOMBRE_MOTIVO", "ID_MEDIDOR", "NUMERO_MEDIDOR", "ID_TIPO_MEDIDOR", "NOMBRE_TIPO_MEDIDOR", "ID_CLASE_MEDIDOR", "NOMBRE_CLASE_MEDIDOR", "NOMBRE_REGION", "NOMBRE_PROVINCIA", "ID_PROVINCIA", "NOMBRE_CANTON", "ID_CANTON", "NOMBRE_PARROQUIA", "DIRECCION_UBICACION", "LONGITUD_UBICACION", "LATITUD_UBICACION", "ALTURA_SNM", "EMPRESA_COD", "VALOR", "CANT_FINALIZADAS", "MEDIDOR_ELECTRICO")
AS
  SELECT elemento.ID_ELEMENTO,
    elemento.NOMBRE_ELEMENTO ,
    modelo_elemento.NOMBRE_MODELO_ELEMENTO,
    NVL(elemento.ACCESO_PERMANENTE,'N/A') AS ACCESO_PERMANENTE,
    elemento.OBSERVACION                  AS OBSERVACION,
    elemento.estado                       AS ESTADO_ELEMENTO,
    elemento.USR_CREACION,
    elemento.FE_CREACION,
    NVL(GET_DETALLE_ELEMENTO_NOMBRE('CLASE',elemento.ID_ELEMENTO),'N/A')      AS CLASE,
    NVL(GET_DETALLE_ELEMENTO_NOMBRE('TORRE',elemento.ID_ELEMENTO),'N/A')      AS TORRE,
    NVL(GET_DETALLE_ELEMENTO_NOMBRE('COBERTURA',elemento.ID_ELEMENTO),'N/A')  AS COBERTURA,
    NVL(GET_DETALLE_ELEMENTO_NOMBRE('TIPO MEDIO',elemento.ID_ELEMENTO),'N/A') AS TIPO_MEDIO,
    solicitud.ID_DETALLE_SOLICITUD                                            AS ID_SOLICITUD,
    solicitud.estado                                                          AS ESTADO_SOLICITUD,
    solicitud.OBSERVACION                                                     AS DESCRIPCION,
    solicitud.FE_EJECUCION,
    solicitud.FE_RECHAZO,
    motivo.ID_MOTIVO,
    motivo.NOMBRE_MOTIVO,
    medidor.ID_MEDIDOR,
    medidor.NUMERO_MEDIDOR,
    tipo_medidor.ID_TIPO_MEDIDOR,
    tipo_medidor.NOMBRE_TIPO_MEDIDOR,
    clase_medidor.ID_CLASE_MEDIDOR,
    clase_medidor.NOMBRE_CLASE_MEDIDOR,
    region.NOMBRE_REGION,
    provincia.NOMBRE_PROVINCIA,
    provincia.ID_PROVINCIA,
    canton.NOMBRE_CANTON,
    canton.ID_CANTON,
    parroquia.NOMBRE_PARROQUIA,
    ubicacion.DIRECCION_UBICACION,
    ubicacion.LONGITUD_UBICACION,
    ubicacion.LATITUD_UBICACION,
    ubicacion.ALTURA_SNM,
    elemento_ubicacion.EMPRESA_COD,
    sol_caract.VALOR,
    (SELECT COUNT(*)
    FROM INFO_DETALLE_SOL_HIST
    WHERE DETALLE_SOLICITUD_ID = solicitud.id_detalle_solicitud
    AND ESTADO                 = 'Finalizada'
    ) CANT_FINALIZADAS,
    medidor.MEDIDOR_ELECTRICO
  FROM info_elemento elemento,
    ADMI_MODELO_ELEMENTO modelo_elemento,
    ADMI_TIPO_ELEMENTO tipo_elemento,
    info_medidor medidor,
    ADMI_CLASE_MEDIDOR clase_medidor,
    ADMI_TIPO_MEDIDOR tipo_medidor,
    INFO_EMPRESA_ELEMENTO_UBICA elemento_ubicacion,
    INFO_UBICACION ubicacion,
    admi_parroquia parroquia,
    admi_canton canton,
    admi_provincia provincia,
    admi_region region,
    info_detalle_solicitud solicitud,
    INFO_DETALLE_SOL_CARACT sol_caract,
    admi_caracteristica caract,
    admi_motivo motivo
  WHERE elemento.MODELO_ELEMENTO_ID      = modelo_elemento.ID_MODELO_ELEMENTO
  AND modelo_elemento.TIPO_ELEMENTO_ID   = tipo_elemento.ID_TIPO_ELEMENTO
  AND tipo_elemento.NOMBRE_TIPO_ELEMENTO = 'NODO'
  AND elemento.ID_ELEMENTO               = medidor.NODO_ID
  AND medidor.CLASE_MEDIDOR_ID           = clase_medidor.ID_CLASE_MEDIDOR
  AND medidor.TIPO_MEDIDOR_ID            = tipo_medidor.ID_TIPO_MEDIDOR
  AND elemento_ubicacion.ELEMENTO_ID     = elemento.ID_ELEMENTO
  AND elemento_ubicacion.UBICACION_ID    = ubicacion.ID_UBICACION
  AND parroquia.ID_PARROQUIA             = ubicacion.PARROQUIA_ID
  AND parroquia.CANTON_ID                = canton.ID_CANTON
  AND canton.PROVINCIA_ID                = provincia.ID_PROVINCIA
  AND provincia.REGION_ID                = region.ID_REGION
  AND elemento.ID_ELEMENTO               = solicitud.elemento_id
  AND solicitud.motivo_id                = motivo.id_motivo
  AND solicitud.id_detalle_solicitud     = sol_caract.DETALLE_SOLICITUD_ID
  AND sol_caract.CARACTERISTICA_ID       = caract.ID_CARACTERISTICA
  AND caract.DESCRIPCION_CARACTERISTICA  = 'VALOR_NODO';
/
