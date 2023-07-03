/**
 * Elimina job JOB_CONTEO_FRECUENCIAS
 * Se unifican procesos y se agrega la ejecucion de los conteos de Frecuencia en el JOB FACTURACIONMASIVATN Facturacion masiva de TN
 *
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 27-11-2018 Se elimina JOB porque se unifica ejecucion al JOB FACTURACIONMASIVATN
 */
BEGIN
  SYS.DBMS_SCHEDULER.DROP_JOB
    (job_name  => '"DB_COMERCIAL"."JOB_CONTEO_FRECUENCIAS"');
END;
