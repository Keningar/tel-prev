/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para crear de registro para web service de consulta 
 * de motivo de cierre de casos Hal
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 28-07-2021 - Versión Inicial.
 */

DELETE DB_COMERCIAL.admi_caracteristica s where s.descripcion_caracteristica = 'HIPOTESIS_CIERRE_CASO_HAL';

COMMIT;
/