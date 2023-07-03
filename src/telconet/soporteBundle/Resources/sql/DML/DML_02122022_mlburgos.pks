/** 
 * @author Leonela Burgos <mlburgos@telconet.ec>
 * @version 1.0 
 * @since 17-11-2022
 * Se crea DML de configuraciones del Proyecto Tarjetas ABU
 */


   
INSERT   
    INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      (select ID_PROCESO from  DB_SOPORTE.ADMI_PROCESO where nombre_proceso='TARJETA ABU'),
      'Cambiar forma de pago',
      'Nombre de la tarea',
      'Activo',
      'mlburgos',
      'mlburgos',
      SYSDATE,
      SYSDATE
    );
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA
    (
      ID_PROCESO_EMPRESA,
      PROCESO_ID,
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,
      (select ID_PROCESO from  DB_SOPORTE.ADMI_PROCESO where nombre_proceso='TARJETA ABU'),
      '18',
      'Activo',
      'mlburgos',
      SYSDATE
    );
    
    COMMIT;
