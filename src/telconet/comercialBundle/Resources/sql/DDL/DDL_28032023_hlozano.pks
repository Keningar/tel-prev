/**
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0
 * @since 28-03-2023  
 * Se crea índice para mejorar costo de query en proceso de factura de instalación.
 */

--ÍNDICE
CREATE INDEX DB_COMERCIAL.INFO_CONTRATO_IDX20 ON DB_COMERCIAL.INFO_CONTRATO (ORIGEN, ESTADO, FE_APROBACION);

COMMIT;
/
