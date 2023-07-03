INSERT
    INTO DB_FINANCIERO.ADMI_CICLO
      (
        ID_CICLO,
        NOMBRE_CICLO,
        FE_INICIO,
        FE_FIN,
        OBSERVACION,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION,
        EMPRESA_COD,
        ESTADO
      )
      VALUES
      (
        DB_FINANCIERO.SEQ_ADMI_CICLO.NEXTVAL,
        'Ciclo (I) - 1 al 30',
        to_date('01/01/2017','DD/MM/RRRR'),
        to_date('31/01/2017','DD/MM/RRRR'),
        'Ciclo inicial configurado',
        sysdate,
        'jguerrerop',
        '127.0.0.1',
        '18',
        'Inactivo'
      );
INSERT
    INTO DB_FINANCIERO.ADMI_CICLO
      (
        ID_CICLO,
        NOMBRE_CICLO,
        FE_INICIO,
        FE_FIN,
        OBSERVACION,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION,
        EMPRESA_COD,
        ESTADO
      )
      VALUES
      (
        DB_FINANCIERO.SEQ_ADMI_CICLO.NEXTVAL,
        'Ciclo (II) - 15 al 14',
        to_date('15/01/2017','DD/MM/RRRR'),
        to_date('14/01/2017','DD/MM/RRRR'),
        'Ciclo inicial configurado',
        sysdate,
        'jguerrerop',
        '127.0.0.1',
        '18',
        'Activo'
      );
commit;
