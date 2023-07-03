/**
 * Revocación de permisos para consulta de tablas y secuencias TM COMERCIAL FASE 4.1
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 17-02-2020
 */
 
REVOKE SELECT, INSERT, DELETE ON DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA FROM DB_COMERCIAL;
REVOKE SELECT, INSERT, DELETE ON DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA FROM DB_COMUNICACION;
REVOKE SELECT ON DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA FROM DB_COMERCIAL;
REVOKE SELECT ON DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA FROM DB_COMUNICACION;

REVOKE SELECT, INSERT, DELETE ON DB_COMUNICACION.ADMI_PLANTILLA FROM DB_COMERCIAL;
REVOKE SELECT, INSERT, DELETE ON DB_COMUNICACION.ADMI_PLANTILLA FROM DB_FIRMAELECT;
REVOKE SELECT ON DB_COMUNICACION.SEQ_ADMI_PLANTILLA FROM DB_COMERCIAL;
REVOKE SELECT ON DB_COMUNICACION.SEQ_ADMI_PLANTILLA FROM DB_FIRMAELECT;
 /