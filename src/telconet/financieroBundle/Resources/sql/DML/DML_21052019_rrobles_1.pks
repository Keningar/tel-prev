/**
 * @author Ricardo Robles <rrobles@telconet.ec>
 * @version 1.0
 * @since 21-05-2019    
 * Se crean la sentencia DML para insertar un nuevo motivo de anulación en la tabla
 * DB_GENERAL.ADMI_MOTIVO.
 */
    --SE CREA MOTIVO 'Regularizacion de Motivo no seleccionado en Telcos'
    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
      ID_MOTIVO,RELACION_SISTEMA_ID,
      NOMBRE_MOTIVO,ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )VALUES(
         DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
         473,
         'Regularizacion de Motivo no seleccionado en Telcos',
         'Inactivo',
         'rrobles',
         'rrobles',
         SYSDATE,
         SYSDATE
      );

    COMMIT;
/
