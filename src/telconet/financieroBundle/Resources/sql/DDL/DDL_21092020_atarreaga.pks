/**
 * Se crea índices por mejora en la generación de archivos de débitos. 
 *
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 21-09-2020
 */

CREATE UNIQUE INDEX DB_FINANCIERO.INFO_PAGO_CAB_INDEX3 
ON DB_FINANCIERO.INFO_PAGO_CAB (ESTADO_PAGO ASC,ID_PAGO ASC);

CREATE UNIQUE INDEX DB_FINANCIERO.INFO_PAGO_CAB_INDEX4 
ON DB_FINANCIERO.INFO_PAGO_CAB (TIPO_DOCUMENTO_ID ASC,ANTICIPO_ID ASC,punto_id ASC,estado_pago ASC,id_pago ASC);

CREATE INDEX DB_FINANCIERO.INFO_PAGO_DET_INDEX1 
ON DB_FINANCIERO.INFO_PAGO_DET (PAGO_ID ASC, VALOR_PAGO ASC);

CREATE UNIQUE INDEX DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB 
ON DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB (PUNTO_ID ASC, VALOR_TOTAL ASC, ESTADO_IMPRESION_FACT ASC, TIPO_DOCUMENTO_ID ASC, ID_DOCUMENTO ASC);

COMMIT;
/