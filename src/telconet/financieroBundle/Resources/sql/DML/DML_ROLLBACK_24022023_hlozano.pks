/**
 * Se crea DML para eliminar la parametrización de la empresa ECUANET utilizada en la facturación Mensual.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 24-02-2023
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'CARGO REPROCESO DEBITO')
AND EMPRESA_COD    IN (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET');



/**
 * Se crea DML para eliminar la parametrización de la empresa ECUANET utilizada en la facturación de Alcance de Cambio de Razón Social.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 24-02-2023
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'CICLO_FACTURACION_EMPRESA')
AND DESCRIPCION    = 'ECUANET';


COMMIT;
/
