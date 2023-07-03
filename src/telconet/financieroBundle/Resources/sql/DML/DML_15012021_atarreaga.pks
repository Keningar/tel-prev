/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para actualizacion en el formato Banco Produbanco - TN
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 15-01-2021 - Version Inicial.
 */

UPDATE DB_FINANCIERO.ADMI_FORMATO_DEBITO SET OPERACION_ADICIONAL = 'verificacodigoretencion|Bien_Base|NA|NA'
WHERE ID_FORMATO_DEBITO = 1405;
COMMIT;

/
