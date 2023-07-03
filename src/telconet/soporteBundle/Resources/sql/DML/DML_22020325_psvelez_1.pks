/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear nuevos parametros para los casos marcados como
 * de mantenimiento programado
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 25-03-2022 - Versión Inicial.
 */

DECLARE
 Ln_IdParametro number;
 Lv_MensajeError VARCHAR2(500);

BEGIN

   Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

   insert into DB_GENERAL.admi_parametro_cab
   (id_parametro,nombre_parametro,descripcion,modulo,estado,usr_creacion,fe_creacion,ip_creacion)
   VALUES
   (Ln_IdParametro,
   'PARAMETRO_GENERAL_CASOS',
   'Cabecera de parametros general para casos',
   'SOPORTE',
   'Activo',
   'psvelez',
   SYSDATE,
   '127.0.0.1');

 
   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, valor2,estado, usr_creacion, fe_creacion, ip_creacion)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'Tipo de afectacion para casos de manteniento programado',
   'TIPO_AFECTACION', 
   'Caída',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1');

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, valor2,estado, usr_creacion, fe_creacion, ip_creacion)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'Tipo de afectacion para casos de manteniento programado',
   'TIPO_AFECTACION', 
   'Intermitencia',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1');  
 -------------------------------------------------------------------------   
 
   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, valor2,estado, usr_creacion, fe_creacion, ip_creacion)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'Tipo de notificacion para casos de manteniento programado',
   'TIPO_NOTIFICACION', 
   'Programado',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1');

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, valor2,estado, usr_creacion, fe_creacion, ip_creacion)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'Tipo de notificacion para casos de manteniento programado',
   'TIPO_NOTIFICACION', 
   'Emergente',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1');  

   Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

   insert into DB_GENERAL.admi_parametro_cab
   (id_parametro,nombre_parametro,descripcion,modulo,estado,usr_creacion,fe_creacion,ip_creacion)
   VALUES
   (Ln_IdParametro,
   'MENSAJES_NOTIFICACION_PUSH',
   'Cabecera de parametros general para notificaciones push',
   'SOPORTE',
   'Activo',
   'psvelez',
   SYSDATE,
   '127.0.0.1');

 
   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro,     
   'CREACION_CASO_FALLA_MASIVA', 
   'Estimado cliente al momento estamos presentando una falla masiva en su sector bajo caso: <numCaso>, estamos trabajando para dar una pronta solución al problema.',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push por creacion de casos fallas masivas');

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro,    
   'NOTIFICAR_AVANCES_FALLA_MASIVA', 
   'Estimado cliente continuamos realizando la reparación de la falla bajo caso <numCaso>, te seguiremos dando avances. Lamentamos los inconvenientes presentados.',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push por notificar avances de casos fallas masiva');  
 
   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro,    
   'CERRAR_CASO_FALLA_MASIVA', 
   'Estimado cliente hemos solucionado la falla que afectaba su sector, por favor verifique que su servicio de internet esté operativo.',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push por cierre de casos fallas masiva');

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'NOTI_CORTA_MANT_PROGRA_MISMA_FECHA', 
   'El <fechaInicio> de <horaInicio> a <horaFin> se realizó un trabajo <tipoNotificacion> en su sector provocando <tipoAfectacion> en el servicio durante <tiempoAfectacion> horas',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push corta por cierre de caso mantenimiento programado misma fecha');  

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'NOTI_LARGA_MANT_PROGRA_MISMA_FECHA', 
   'Estimado/a <cliente> ,
    Nos encontramos realizando mejoras constantes dentro de nuestra red con el objetivo de seguir brindando un servicio de calidad, por tal motivo se ejecutará un trabajo <tipoNotificacion> en su sector el cual ocasionará <tipoAfectacion> del servicio de internet durante la ventana de trabajo.

    Fecha: <fechaInicio>
    Hora de Inicio: <horaInicio>
    Hora de Finalización: <horaFin>
    Tipo de afectación: <tipoAfectacion> del servicio
    Tiempo de afectación: <tiempoAfectacion> horas

    De antemano pedimos disculpas por las molestias que podamos ocasionar. Agradecemos su comprensión.',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push larga por cierre de caso mantenimiento programado misma fecha');  

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro,     
   'NOTI_CORTA_MANT_PROGRA_DISTINTAS_FECHA', 
   'El <fechaInicio> <horaInicio> hasta el <fechaFin> <horaFin> se realizó un trabajo <tipoNotificacion> en su sector provocando <tipoAfectacion> en el servicio durante <tiempoAfectacion> horas',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push corta por cierre de caso mantenimiento programado distintas fechas');  

   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion,empresa_cod,OBSERVACION)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
   Ln_IdParametro, 
   'NOTI_LARGA_MANT_PROGRA_DISTINTAS_FECHA', 
   'Estimado/a <cliente>,
    Nos encontramos realizando mejoras constantes dentro de nuestra red con el objetivo de seguir brindando un servicio de calidad, por tal motivo se ejecutará un trabajo <tipoNotificacion> en su sector el cual ocasionará <tipoAfectacion> del servicio de internet durante la ventana de trabajo

    Fecha de inicio: <fechaInicio>
    Hora de inicio: <horaInicio>
    Fecha de finalización: <fechaFin>
    Hora de finalización: <horaFin>
    Tipo de afectación: <tipoAfectacion> del servicio
    Tiempo de afectación: <tiempoAfectacion> horas

    De antemano pedimos disculpas por las molestias que podamos ocasionar. Agradecemos su comprensión',  
   'Activo', 
   'psvelez', 
   SYSDATE, 
   '127.0.0.1',
   '18',
   'Mensaje a enviar en notificacion push larga por cierre de caso mantenimiento programado distintas fecha'); 
    
    COMMIT;
    
EXCEPTION
 WHEN OTHERS THEN
    Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;    
    ROLLBACK;
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'CREACION PARAMETRO MD',
                                          Lv_MensajeError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                          '127.0.0.1')
                                        );
END;
/
