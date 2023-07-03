/**
* ScriptS para creación de índices requeridos para consultar pagos asociados a facturas anuladas.
* @author Edgar Holguín <eholguin@telconet.ec>
* @version 1.0 23-11-2018
*/
  CREATE INDEX IDX_NOMMOT_ESTADO ON DB_GENERAL.ADMI_MOTIVO (NOMBRE_MOTIVO ASC, ESTADO ASC);

  CREATE INDEX IDX_PTOID_ESTPAG ON DB_FINANCIERO.INFO_PAGO_CAB (PUNTO_ID ASC, ESTADO_PAGO ASC);   
