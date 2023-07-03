/**
* Se crea un nuevo motivo, para el cambio de usuario creacion en los prospectos.
* @author Kevin Baque Puya <kbaque@telconet.ec>
* @version 1.0 28-01-2019
*/
DECLARE
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_MOTIVO
    (
      ID_MOTIVO,
      RELACION_SISTEMA_ID,
      NOMBRE_MOTIVO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
      NULL,
      'Cambio de usuario creación',
      'Activo',
      'kbaque',
      sysdate,
      'kbaque',
      sysdate
    );
  COMMIT;
END;