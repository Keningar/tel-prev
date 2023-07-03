insert into db_general.ADMI_PARAMETRO_CAB VALUES(
    db_general.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'ANIO_VIGENCIA_TARJETA',
    'INTERVALO MAXIMO DE ANIOS USADOS EN LA CREACION/MODIFICACION DE CONTRATO',
    'COMERCIAL',
    null,
    'Activo',
    'gnarea',
    sysdate,
    '0.0.0.0',
    null,
    null,
    null
  );
  
  insert into db_general.admi_parametro_det values(
    db_general.seq_ADMI_PARAMETRO_DET.NEXTVAL,
    (select b.id_parametro from db_general.ADMI_PARAMETRO_CAB b where b.nombre_parametro = 'ANIO_VIGENCIA_TARJETA'),--12592
     (select b.descripcion from db_general.ADMI_PARAMETRO_CAB b where b.nombre_parametro = 'ANIO_VIGENCIA_TARJETA'),
    10, --valor1
    null,
    null,
    null,
    'Activo',
    'gnarea',
    sysdate,
    '0.0.0.0',
    null,
    null,
    null,--ip_ult_mod
    null,--valor5
    18,--empresa_cod
    null,
    null,
    null
  );
  commit;
/