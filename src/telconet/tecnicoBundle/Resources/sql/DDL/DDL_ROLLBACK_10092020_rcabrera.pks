/**
 * Vista que obtiene información de puertos de cliente
 *
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 11/06/2019
 *
 */
CREATE OR REPLACE FORCE VIEW DB_INFRAESTRUCTURA.V_PUERTO_CLIENTES
AS SELECT

IE.NOMBRE_ELEMENTO,
IIE.NOMBRE_INTERFACE_ELEMENTO,
IPU.LOGIN,
ISE.LOGIN_AUX,
IPE.RAZON_SOCIAL,
    
APO.DESCRIPCION_PRODUCTO PRODUCTO,
ISE.DESCRIPCION_PRESENTA_FACTURA AS SERVICIO,
ISE.ESTADO,

(SELECT ISEH2.FE_CREACION FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISEH2 WHERE ISEH2.ID_SERVICIO_HISTORIAL = (
  SELECT MAX(ISEH.ID_SERVICIO_HISTORIAL) FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISEH WHERE ISEH.SERVICIO_ID = ISE.ID_SERVICIO AND ISEH.ESTADO = 'Cancel')) AS FECHA_CANCELACION,

ISE.USR_VENDEDOR,

(SELECT ISPC2.VALOR FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC2 WHERE ISPC2.ID_SERVICIO_PROD_CARACT = (
SELECT
      MAX(ISPC.ID_SERVICIO_PROD_CARACT)
    FROM
      DB_COMERCIAL.INFO_SERVICIO ISERV,
      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
      DB_COMERCIAL.ADMI_PRODUCTO AP,
      DB_COMERCIAL.ADMI_CARACTERISTICA AC
    WHERE
      ISPC.SERVICIO_ID                   = ISERV.ID_SERVICIO
    AND ISPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
    AND APC.PRODUCTO_ID                  = AP.ID_PRODUCTO
    AND APC.CARACTERISTICA_ID            = AC.ID_CARACTERISTICA
    AND AC.ESTADO                        = 'Activo'
    AND ISERV.ID_SERVICIO                = ISE.ID_SERVICIO
    AND AC.DESCRIPCION_CARACTERISTICA    = 'CAPACIDAD1')) AS BW_SUBIDA,

(SELECT ISPC2.VALOR FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC2 WHERE ISPC2.ID_SERVICIO_PROD_CARACT = (
SELECT
      MAX(ISPC.ID_SERVICIO_PROD_CARACT)
    FROM
      DB_COMERCIAL.INFO_SERVICIO ISERV,
      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
      DB_COMERCIAL.ADMI_PRODUCTO AP,
      DB_COMERCIAL.ADMI_CARACTERISTICA AC
    WHERE
      ISPC.SERVICIO_ID                   = ISERV.ID_SERVICIO
    AND ISPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
    AND APC.PRODUCTO_ID                  = AP.ID_PRODUCTO
    AND APC.CARACTERISTICA_ID            = AC.ID_CARACTERISTICA
    AND AC.ESTADO                        = 'Activo'
    AND ISERV.ID_SERVICIO                = ISE.ID_SERVICIO
    AND AC.DESCRIPCION_CARACTERISTICA    = 'CAPACIDAD2')) AS BW_BAJADA,

IIP.IP

FROM 

DB_INFRAESTRUCTURA.INFO_SERVICIO_TECNICO IST,
DB_COMERCIAL.INFO_SERVICIO ISE,
DB_COMERCIAL.INFO_PUNTO IPU,
DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
DB_COMERCIAL.INFO_PERSONA IPE,
DB_COMERCIAL.ADMI_PRODUCTO APO,
DB_INFRAESTRUCTURA.INFO_ELEMENTO IE,
DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IIE,
DB_INFRAESTRUCTURA.INFO_IP IIP

WHERE IST.SERVICIO_ID = ISE.ID_SERVICIO
AND IST.ELEMENTO_ID = IE.ID_ELEMENTO
AND IST.INTERFACE_ELEMENTO_ID = IIE.ID_INTERFACE_ELEMENTO
AND IPU.ID_PUNTO = ISE.PUNTO_ID
AND IPU.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
AND IPER.PERSONA_ID = IPE.ID_PERSONA
AND APO.ID_PRODUCTO = ISE.PRODUCTO_ID
AND IIP.SERVICIO_ID = ISE.ID_SERVICIO
AND IIP.ID_IP = (SELECT MAX(ID_IP) FROM DB_INFRAESTRUCTURA.INFO_IP WHERE SERVICIO_ID = ISE.ID_SERVICIO)
AND IST.ELEMENTO_ID IS NOT NULL
AND IST.INTERFACE_ELEMENTO_ID IS NOT NULL
AND ISE.PRODUCTO_ID IS NOT NULL
AND ISE.ESTADO IN ('Activo','EnPruebas','In-Corte','Cancel');

/
