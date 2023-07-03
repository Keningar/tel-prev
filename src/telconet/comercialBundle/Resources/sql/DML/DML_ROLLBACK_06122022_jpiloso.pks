/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para ELIMINAR los parametros del proyecto de Derechos del titular
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0
 * @since 04-01-2023
 */


--Reverso el detalle para el proceso y tarea de derechos del titular
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA_AUTOMATICA_ACTUALIZACION_Y_RECTIFICACION'
    AND VALOR2 = 'SOLICITUD DE ACTUALIZACION Y RECTIFICACION'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );
      
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA_AUTOMATICA_PORTABILIDAD'
    AND VALOR2 = 'SOLICITUD DE PORTABILIDAD'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );

DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA_AUTOMATICA_OPOSICION'
    AND VALOR2 = 'SOLICITUD DE OPOSICION'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );

DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA_AUTOMATICA_SUSPENSION_TRATAMIENTO'
    AND VALOR2 = 'SOLICITUD DE SUSPENSION DE TRATAMIENTO'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );
      
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA_AUTOMATICA_DETENCION_SUSPENSION_TRATAMIENTO'
    AND VALOR2 = 'SOLICITUD DE DETENCION DE SUSPENSION DE TRATAMIENTO'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );
 
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TIPO PERSONA PERMITIDO PARA REGISTRAR BITACORA Y TAREA AUTOMATICA'
    AND VALOR2 = 'Cliente'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo' );

DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO'
    AND VALOR2 = 'PROCESOS_DERECHOS_DEL_TITULAR'
    AND PARAMETRO_ID = ( SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
      AND estado            ='Activo' );
      
DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'SOLICITUD DE ACTUALIZACION Y RECTIFICACION'
    AND PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');
  
DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'SOLICITUD DE PORTABILIDAD'
    AND PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');

DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'SOLICITUD DE OPOSICION'
    AND PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');

DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'SOLICITUD DE SUSPENSION DE TRATAMIENTO'
    AND PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');
  
DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'SOLICITUD DE DETENCION DE SUSPENSION DE TRATAMIENTO'
    AND PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');

DELETE DB_SOPORTE.ADMI_PROCESO_EMPRESA
WHERE
    PROCESO_ID = (SELECT ID_PROCESO
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR');

DELETE DB_SOPORTE.ADMI_PROCESO
WHERE NOMBRE_PROCESO = 'PROCESOS DERECHOS DEL TITULAR';

DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
  AND estado            ='Activo';
  
DELETE DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'PORTABILIDAD';

DELETE DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'DERECHO_TITULAR';

DELETE DB_DOCUMENTO.ADMI_DOCUMENTO
WHERE NOMBRE = 'Derechos del titular';

    COMMIT;