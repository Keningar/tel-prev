/**
 * V_SCANNING_FIREWALL
 *
 * Vista que genera información necesaria para que cert pueda contactar a los contactos.
 * 
 * @costo anterior 13921
 * @costo nuevo    17073 
 * @author David Leon <mdleon@telconet.ec>
 * @version 1.1
 * @since 17-04-2019
 */

  CREATE OR REPLACE FORCE VIEW "DB_COMERCIAL"."V_SCANNING_FIREWALL" ("ID_SERVICIO", "NOMBRE_OFICINA", "CLIENTE", "TIPO_IDENTIFICACION", "IDENTIFICACION_CLIENTE", "COBERTURA", "NOMBRE_SECTOR", "NOMBRE_CANTON", "NOMBRE_PROVINCIA", "NOMBRE_PARROQUIA", "LOGIN", "ESTADO_SERVICIO", "DESCRIPCION_FACTURA", "FECHA_ACTIVACION", "DESCRIPCION_PRODUCTO", "CONTACTO_CLIENTE", "VALOR_CONTACTO_CLIENTE", "TIPO_CONTACTO", "FORMA_CONTACTO", "ESCALABILIDAD", "HORARIO", "OBSERVACION") AS 
  SELECT SERVICIO.ID_SERVICIO,
  OFICINA.NOMBRE_OFICINA,
  NVL( PERSONA.RAZON_SOCIAL, CONCAT( PERSONA.NOMBRES, CONCAT(' ', PERSONA.APELLIDOS) ) ) AS CLIENTE,
  PERSONA.TIPO_IDENTIFICACION,
  PERSONA.IDENTIFICACION_CLIENTE,
  JURISDICCION.NOMBRE_JURISDICCION AS COBERTURA,
  SECTOR.NOMBRE_SECTOR,
  CANTON.NOMBRE_CANTON,
  PROVINCIA.NOMBRE_PROVINCIA,
  PARROQUIA.NOMBRE_PARROQUIA,
  PUNTO.LOGIN,
  SERVICIO.ESTADO AS ESTADO_SERVICIO,
  SERVICIO.DESCRIPCION_PRESENTA_FACTURA AS DESCRIPCION_FACTURA,
  (SELECT FE_CREACION
  FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
  WHERE ID_SERVICIO_HISTORIAL =
    (SELECT MIN(ID_SERVICIO_HISTORIAL)
    FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
    WHERE ESTADO    ='Activo'
    AND SERVICIO_ID = SERVICIO.ID_SERVICIO
    )
  ) AS FECHA_ACTIVACION,
  PRODUCTO.DESCRIPCION_PRODUCTO,
  INFO_CONTACTO.CONTACTO_CLIENTE,
  INFO_CONTACTO.VALOR_CONTACTO_CLIENTE,
  INFO_CONTACTO.TIPO_CONTACTO,
  INFO_CONTACTO.FORMA_CONTACTO,
  INFOROLCARACESCALA.VALOR AS ESCALABILIDAD,
  INFOROLCARACHORA.VALOR AS HORARIO,
  (SELECT OBSERVACION
  FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
  WHERE ID_SERVICIO_HISTORIAL =
    (SELECT MIN(ID_SERVICIO_HISTORIAL)
    FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
    WHERE ESTADO    ='Activo'
    AND ACCION      ='confirmarServicio'
    AND SERVICIO_ID = SERVICIO.ID_SERVICIO
    )
  ) AS OBSERVACION
FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO
INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
ON PUNTO.ID_PUNTO = SERVICIO.PUNTO_ID
INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER
ON PER.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO OFICINA
ON OFICINA.ID_OFICINA = PER.OFICINA_ID
INNER JOIN DB_INFRAESTRUCTURA.ADMI_JURISDICCION JURISDICCION
ON JURISDICCION.ID_JURISDICCION = PUNTO.PUNTO_COBERTURA_ID
INNER JOIN DB_GENERAL.ADMI_SECTOR SECTOR
ON SECTOR.ID_SECTOR = PUNTO.SECTOR_ID
INNER JOIN DB_GENERAL.ADMI_PARROQUIA PARROQUIA
ON PARROQUIA.ID_PARROQUIA = SECTOR.PARROQUIA_ID
INNER JOIN DB_GENERAL.ADMI_CANTON CANTON
ON CANTON.ID_CANTON = PARROQUIA.CANTON_ID
INNER JOIN DB_GENERAL.ADMI_PROVINCIA PROVINCIA
ON PROVINCIA.ID_PROVINCIA = CANTON.PROVINCIA_ID
INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
ON PRODUCTO.ID_PRODUCTO = SERVICIO.PRODUCTO_ID
INNER JOIN DB_COMERCIAL.INFO_PERSONA PERSONA
ON PERSONA.ID_PERSONA = PER.PERSONA_ID
LEFT JOIN
  (SELECT PUNTO_CONTACTO.PUNTO_ID,
    PUNTO_CONTACTO.ID_PUNTO_CONTACTO,
    NVL( PERSONA_CONTACTO.RAZON_SOCIAL, CONCAT( PERSONA_CONTACTO.NOMBRES, CONCAT(' ', PERSONA_CONTACTO.APELLIDOS) ) ) AS CONTACTO_CLIENTE,
    PERSONA_F_CONTACTO.VALOR                                                                                          AS VALOR_CONTACTO_CLIENTE,
    ROL_CONTACTO.DESCRIPCION_ROL                                                                                      AS TIPO_CONTACTO,
    FORMA_CONTACTO.DESCRIPCION_FORMA_CONTACTO                                                                         AS FORMA_CONTACTO,
    PER_CONTACTO.ID_PERSONA_ROL                                                                                       AS ID_PERSONA_ROL  
  FROM DB_COMERCIAL.INFO_PUNTO_CONTACTO PUNTO_CONTACTO
  INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO PERSONA_F_CONTACTO
  ON PERSONA_F_CONTACTO.PERSONA_ID = PUNTO_CONTACTO.CONTACTO_ID
  INNER JOIN DB_COMERCIAL.INFO_PERSONA PERSONA_CONTACTO
  ON PERSONA_CONTACTO.ID_PERSONA = PERSONA_F_CONTACTO.PERSONA_ID
  INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER_CONTACTO
  ON PER_CONTACTO.ID_PERSONA_ROL = PUNTO_CONTACTO.PERSONA_EMPRESA_ROL_ID
  INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER
  ON IER.ID_EMPRESA_ROL = PER_CONTACTO.EMPRESA_ROL_ID
  INNER JOIN DB_GENERAL.ADMI_ROL ROL_CONTACTO
  ON ROL_CONTACTO.ID_ROL = IER.ROL_ID
  INNER JOIN DB_GENERAL.ADMI_TIPO_ROL TIPO_ROL_CONTACTO
  ON TIPO_ROL_CONTACTO.ID_TIPO_ROL = ROL_CONTACTO.TIPO_ROL_ID
  INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO FORMA_CONTACTO
  ON FORMA_CONTACTO.ID_FORMA_CONTACTO            = PERSONA_F_CONTACTO.FORMA_CONTACTO_ID
  WHERE PUNTO_CONTACTO.ESTADO                    = 'Activo'
  AND PERSONA_F_CONTACTO.ESTADO                  = 'Activo'
  AND ROL_CONTACTO.DESCRIPCION_ROL              in ('Contacto Tecnico','Contacto Seguridad Escalable')
  AND TIPO_ROL_CONTACTO.DESCRIPCION_TIPO_ROL     = 'Contacto'
  AND FORMA_CONTACTO.DESCRIPCION_FORMA_CONTACTO IN ('Correo Electronico', 'Telefono Fijo', 'Telefono Movil', 
                                                    'Telefono Movil Claro', 'Telefono Movil Movistar', 'Telefono Movil CNT',
                                                    'Telegram')
  AND FORMA_CONTACTO.ESTADO                      = 'Activo'
  ) INFO_CONTACTO ON INFO_CONTACTO.PUNTO_ID      = PUNTO.ID_PUNTO
  LEFT JOIN 
  (SELECT VALOR ,PERSONA_EMPRESA_ROL_ID
  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
  DB_COMERCIAL.ADMI_CARACTERISTICA AC
  WHERE IPERC.CARACTERISTICA_ID=AC.ID_CARACTERISTICA
  AND AC.DESCRIPCION_CARACTERISTICA='NIVEL ESCALABILIDAD'
  AND IPERC.ESTADO='Activo')INFOROLCARACESCALA
  ON INFO_CONTACTO.ID_PERSONA_ROL= INFOROLCARACESCALA.PERSONA_EMPRESA_ROL_ID
  LEFT JOIN 
  (SELECT VALOR ,PERSONA_EMPRESA_ROL_ID
  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
  DB_COMERCIAL.ADMI_CARACTERISTICA AC
  WHERE IPERC.CARACTERISTICA_ID=AC.ID_CARACTERISTICA
  AND AC.DESCRIPCION_CARACTERISTICA='HORARIO ESCALABILIDAD'
  AND IPERC.ESTADO='Activo')INFOROLCARACHORA
  ON INFO_CONTACTO.ID_PERSONA_ROL= INFOROLCARACHORA.PERSONA_EMPRESA_ROL_ID
WHERE PRODUCTO.DESCRIPCION_PRODUCTO IN (
SELECT DET.VALOR1 
FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
WHERE CAB.NOMBRE_PARAMETRO = 'DESCRIPCION_PRODUCTO_V_SCANNING_FIREWALL'
AND CAB.ESTADO = 'Activo'
AND DET.ESTADO = 'Activo');

/