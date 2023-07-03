INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS_REINGRESO_OS_AUTOMATICA',
    'CONFIGURACIÓN DE LOS PARAMETROS REQUERIDOS PARA EL PROCESO DE REINGRESO DE OS AUTOMATICA, SE PARAMETRIZAN LOS MOTIVOS DE RECHAZO O ANULACIÓN DE ORDEN EN PyL VALIDOS PARA EL REINGRESO Y LOS DIAS DE RECHAZO DE ORDEN QUE HABILITAN EL REINGRESO DE UNA OS',
    'COMERCIAL',
    'REINGRESO AUTOMATICO',
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  (
    SELECT
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVOS_RECHAZO_ANULACION_OS_AUTOMATICA',
    MOT.ID_MOTIVO,
    MOT.NOMBRE_MOTIVO,
    SISR.ID_RELACION_SISTEMA,
    SISM.ID_MODULO, 
    SISM.NOMBRE_MODULO,
    SISA.ID_ACCION, 
    SISA.NOMBRE_ACCION,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18',
    'VALOR1: ID_MOTIVO REFERENTE A ADMI_MOTIVO
     VALOR2: NOMBRE_MOTIVO REFERENTE A ADMI_MOTIVO
     VALOR3: ID_RELACION_SISTEMA REFERENTE A SEGU_RELACION_SISTEMA
     VALOR4: ID_MODULO REFERENTE A SIST_MODULO
     VALOR5: NOMBRE_MODULO REFERENTE A SIST_MODULO
     VALOR6: ID_ACCION REFERENTE A SIST_ACCION
     VALOR7: NOMBRE_ACCION REFERENTE A SIST_ACCION' 
    FROM DB_GENERAL.ADMI_MOTIVO MOT, 
    DB_SEGURIDAD.SEGU_RELACION_SISTEMA SISR,
    DB_SEGURIDAD.SIST_MODULO SISM ,
    DB_SEGURIDAD.SIST_ACCION SISA
    WHERE MOT.RELACION_SISTEMA_ID = SISR.ID_RELACION_SISTEMA
    AND SISR.MODULO_ID            = SISM.ID_MODULO
    AND SISR.ACCION_ID            = SISA.ID_ACCION
    AND MOT.ESTADO                = 'Activo'
    AND SISM.NOMBRE_MODULO        = 'coordinar'
    AND SISA.NOMBRE_ACCION        IN ('getMotivosRechazo','getMotivosAnulacion')
    AND MOT.NOMBRE_MOTIVO NOT IN ('Venta incorrecta','Cliente Cyber','Contrato Duplicado','Contrato digital mal ingresado')
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'DIAS_RECHAZO_OS_AUTOMATICA',
    '30',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Numero de dias permitido para el Reingreso de Orden de servicio automatica a considerar desde la fecha de PrePlanificada'
  );

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'ID_SERVICIO_REINGRESO','N','Activo',SYSDATE,'apenaherrera',NULL,NULL,'COMERCIAL');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'FACTURACION','N','Activo',SYSDATE,'apenaherrera',NULL,NULL,'COMERCIAL');

UPDATE DB_COMERCIAL.ADMI_FORMA_CONTACTO SET CODIGO='TFRIPCC' WHERE ID_FORMA_CONTACTO=214;

UPDATE DB_COMERCIAL.ADMI_FORMA_CONTACTO SET CODIGO='TMRIPCC' WHERE ID_FORMA_CONTACTO=215;

UPDATE DB_COMERCIAL.ADMI_FORMA_CONTACTO SET CODIGO='TMTUENT' WHERE ID_FORMA_CONTACTO=218;

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TFIJ',
    'ECUADOR',
    'Telefono Fijo',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'MCAWP',
    'ECUADOR',
    'Telefono Movil Cable and Wireless',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'MDIGP',
    'ECUADOR',
    'Telefono Movil Digicel',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'MCLA',
    'ECUADOR',
    'Telefono Movil Claro',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'MMOV',
    'ECUADOR',
    'Telefono Movil Movistar',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'MCNT',
    'ECUADOR',
    'Telefono Movil CNT',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TTRA',
    'ECUADOR',
    'Telefono Traslado',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TINT',
    'ECUADOR',
    'Telefono Internacional',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TFRIPCC',
    'ECUADOR',
    'Telefono Fijo Referencia IPCC',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TMRIPCC',
    'ECUADOR',
    'Telefono Movil Referencia IPCC',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'COD_FORMA_CONTACTO'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO DONDE SE CONFIGURA LOS CODIGOS DE FORMAS DE CONTACTO POR EMPRESA Y PAIS',
    'TMTUENT',
    'ECUADOR',
    'Telefono Movil Tuenti',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'VALOR1: CODIGO DE LA FORMA DE PAGO,
    VALOR2: PAIS '
   );

INSERT
INTO
  DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificación de Reingreso de Orden de Servicio Automática',    
    'NROSA',
    'COMERCIAL',
    '<!DOCTYPE html><html><head><meta http-equiv=Content-Type content="text/html; charset=UTF-8"/></head><body><table align="center" width="100%" cellspacing="0" cellpadding="5"><tr><td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;"><img alt="" src="http://images.telconet.net/others/sit/notificaciones/logo.png"/></td></tr><tr><td style="border:1px solid#6699CC;"><table><tr><td><table><tr><td>Estimado usuario <b>{{usuario}}</b>,</td></tr><tr><td></td></tr><tr><td>El proceso de reingreso de OS autom&#225;tica para el punto <b>{{loginCLiente}}</b> ha finalizado.<br/><br/></td></tr><tr><td><b>Observaci&#243;n:</b> {{observacion}}.<br/><br/><br/></td></tr><td style="float:left;color: #84939f;">Las tildes han sido omitidas intencionalmente en la <b>Observaci&#243;n</b> para evitar problemas de lectura.<br/><br/></td><tr><td>Atentamente,</td></tr><tr><td></td></tr><tr><td><strong>Sistema TelcoS+</strong><br/><br/></td></tr></table></td></tr></table></td></tr></table></body></html>',
    'Activo',
    SYSDATE,
    'apenaherrera'
  );
  
INSERT
INTO
  DB_COMUNICACION.INFO_ALIAS_PLANTILLA
  (
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
      SELECT MIN(ID_ALIAS)
      FROM DB_COMUNICACION.ADMI_ALIAS
      WHERE VALOR = 'notificaciones_telcos@telconet.ec'
      AND ESTADO  IN ('Activo','Modificado')
      AND EMPRESA_COD = 18    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'NROSA'
      AND ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'apenaherrera',
    'NO'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'DIAS_PREFACTIBLE',
    '7',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'DIAS_FACTIBLE',
    '7',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETROS_WEBSERVICES',
    'http://telcos-lb.telconet.ec/rs/comercial/ws/rest/ejecutar',
    'putReingresoOrdenServicio',
    'application/json',
    'UTF-8',
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'DIAS_REPORTE',
    '1',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );

INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'NO_PASO_CONVERTIR_OT',
   'N','Activo',SYSDATE,
   'jcandelario',
   NULL,
   NULL,
  'COMERCIAL'); 

INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'PUNTO_COBERTURA_ID',
   'N','Activo',SYSDATE,
   'apenaherrera',
   NULL,
   NULL,
  'COMERCIAL'); 

INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'SECTOR_ID',
   'N','Activo',SYSDATE,
   'apenaherrera',
   NULL,
   NULL,
  'COMERCIAL'); 

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'NUM_INTENTOS_REINGRESO_OS',
    '5',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Numero de intentos permitidos para que un servicio ejecute el flujo de reingreso automatico de orden de servicio'
  );

INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'CONT_INTENTOS_REINGRESO_OS',
   'N','Activo',SYSDATE,
   'apenaherrera',
   NULL,
   NULL,
  'COMERCIAL'); 

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  ) 
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_DATOS_GEOGRAFICOS',
    'OS-No procede para Reingreso Automático: Motivo: Se realizó cambio de localidad(Sector, Parroquia, Cantón, Jurisdiccion.)',
    null,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  ) 
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_NUMERO_INTENTOS',
    'OS-No procede para Reingreso Automático: Motivo: Máximo({NumIntentosReingresoOs}) Intentos Fallidos.',
    null,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  ) 
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_PROCESO_FACT_INST',
    'OS-No procede para Reingreso Automático: Existió un error en el proceso de Facturación por instalación.',
    null,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'FLUJO_COMPLETO',
   'T','Activo',SYSDATE,
   'apenaherrera',
   NULL,
   NULL,
  'COMERCIAL'); 

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_ORDEN',
    'N',
    'Nueva',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Tipo de Orden de Servicio que ingresa al proceso de Reingreso Automatico'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO_VALIDA_REINGRESO',
    'Rechazada',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Estados validos que debe tener un punto para poder ejecutar flujo de reingreso. '
  );
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO_VALIDA_REINGRESO',
    'Anulado',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Estados validos que debe tener un punto para poder ejecutar flujo de reingreso. '
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,    
    EMPRESA_COD,    
    OBSERVACION
  ) 
  VALUES
  (      
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO_VALIDA_REINGRESO',
    'Eliminado',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',   
    '18', 
    'VALOR1: Estados validos que debe tener un punto para poder ejecutar flujo de reingreso. '
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  ) 
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_REINGRESO_EJECUTADO',
    'Error : El servicio ya cuenta con una clonación por reingreso de OS automática en proceso.',
    null,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  ) 
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_CONVERTIR_OT',
    'El servicio no pasó las validaciones previas para convertir a OT.',
    null,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    'apenaherrera',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

COMMIT;
/

