UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Factura Diferida.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ES_SOL_FACTURA';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Número de cuotas por factura.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ES_MESES_DIFERIDO';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Id de Proceso Masivo.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ES_PROCESO_MASIVO';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Característica de diferidos por emergencia sanitaria.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'PROCESO_DIFERIDO';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Contador de cuotas generadas por el diferido.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ES_CONT_DIFERIDO';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'NCI diferida.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ID_REFERENCIA_NCI';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Numero de cuota de NDI generada.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'NUM_CUOTA_DIFERIDA';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Id de número de solicitud.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'ES_ID_SOLICITUD';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA AC
SET AC.DETALLE_CARACTERISTICA = 'Valor de la cuota diferida.'
WHERE AC.DESCRIPCION_CARACTERISTICA = 'VALOR_CUOTA_DIFERIDA';

COMMIT;
/
