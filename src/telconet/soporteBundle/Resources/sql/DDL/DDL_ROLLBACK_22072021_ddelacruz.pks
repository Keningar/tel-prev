/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para eliminar tabla y secuencia creados para registrar los puntos afectados por tareas creadas en 
 * Sistemas externos a Telcos, como Sisred
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-07-2021 - Versión Inicial.
 */

DROP TABLE DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO;
DROP SEQUENCE DB_SOPORTE.SEQ_INFO_ACTIV_PUNTO_AFECTADO;