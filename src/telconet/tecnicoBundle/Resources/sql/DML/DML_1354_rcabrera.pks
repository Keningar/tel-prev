/* Se habilitan los alias que son usados en las notificaciones de seguimientos de caso */

UPDATE db_general.ADMI_PARAMETRO_DET
SET estado         = 'Activo'
WHERE parametro_id =
  (SELECT id_parametro
  FROM db_general.ADMI_PARAMETRO_CAB
  WHERE usr_creacion   = 'rcabrera'
  AND NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO NOTIFICACIONES CASOS CLIENTE'
  )
AND DESCRIPCION = 'ALIAS_SEGUI_CASO_CLIENTE';


/* Se registran los roles para Jefes Departamentales */
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      269,
      'ROLES_CONSIDERAR',
      '102',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      '10',
      NULL,
      NULL,
      NULL
    );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      269,
      'ROLES_CONSIDERAR',
      '125',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      '10',
      NULL,
      NULL,
      NULL
    );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      269,
      'ROLES_CONSIDERAR',
      '140',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      '10',
      NULL,
      NULL,
      NULL
    );


commit;

/
