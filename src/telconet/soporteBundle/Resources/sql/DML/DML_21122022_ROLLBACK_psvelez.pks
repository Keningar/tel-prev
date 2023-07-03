/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Rollback del archivo dml_21122022_psvelez.pks
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 21-12-2022 - Versión Inicial.
 */

update DB_GENERAL.ADMI_PARAMETRO_DET s
set S.VALOR1='El <fechaInicio> de <horaInicio> a <horaFin> se realizó un trabajo <tipoNotificacion> en su sector provocando <tipoAfectacion> en el servicio durante <tiempoAfectacion> horas'
where S.DESCRIPCION='NOTI_CORTA_MANT_PROGRA_MISMA_FECHA';

update DB_GENERAL.ADMI_PARAMETRO_DET s
set S.VALOR1='El <fechaInicio> <horaInicio> hasta el <fechaFin> <horaFin> se realizó un trabajo <tipoNotificacion> en su sector provocando <tipoAfectacion> en el servicio durante <tiempoAfectacion> horas'
where S.DESCRIPCION='NOTI_CORTA_MANT_PROGRA_DISTINTAS_FECHA';

commit;

/