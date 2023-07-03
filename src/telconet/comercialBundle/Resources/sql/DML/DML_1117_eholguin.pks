/**
  * @author Edgar Holguín <eholguin@telconet.ec>
  * @version 1.0 13-09-2018 Se realizan inserts necesarios para proceso de facturación por cancelación voluntaria.
  */

INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    ESTADO
  ) 
VALUES 
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD CANCELACION VOLUNTARIA',
    sysdate,
    'eholguin',
    'Activo'
  );

INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    ESTADO
  ) 
VALUES 
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD NOTA CREDITO',
    sysdate,
    'eholguin',
    'Activo'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FACTURACION DETALLADA',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EQUIPOS',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'NETLIFEASSISTANCE',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'INSTALACION',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'DESCUENTOS',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'DESCUENTO ADICIONAL',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );  

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PORCENTAJE INSTALACION NC',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PORCENTAJE DESCUENTO NC',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SOLICITUD NOTA CREDITO',
    'N',
    'Activo',
    sysdate,
    'eholguin',
    sysdate,
    'eholguin',
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    FUNCION_PRECIO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '18',
    'CANCV',
    'CANCELACION VOLUNTARIA',
    NULL,
    '0',
    'Inactivo',
    SYSDATE,
    'eholguin',
    '127.0.0.1',
    '0',
    '0',
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROSSERVICIOS',
    NULL,
    'S',
    'NO',
    'N',
    NULL,
    'OTROS',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'PRECIO=0'
  );
COMMIT;

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    null,
    'Cancelacion Voluntaria',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Problemas de instalacion',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Problemas en el servicio',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Clientes migrados',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Por Cambio de Razon Social',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Por Traslado',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
  );

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
    FE_ULT_MOD,
    CTA_CONTABLE
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    (SELECT SRS.ID_RELACION_SISTEMA 
     FROM   DB_SEGURIDAD.SEGU_RELACION_SISTEMA  SRS
     JOIN   DB_SEGURIDAD.SIST_MODULO  SM ON SM.ID_MODULO = SRS.MODULO_ID
     WHERE  SM.NOMBRE_MODULO = 'cancelacionadministrativa'
     AND SM.ESTADO = 'Activo'),
    'Traslado sin Equipo',
    'Activo',
    'eholguin',
    sysdate,
    'eholguin',
    sysdate,
    NULL
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
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
          SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
          AND MODULO  = 'FINANCIERO'
          AND PROCESO = 'FACTURACION_SOLICITUDES'
          AND ESTADO  = 'Activo'
        ),
        'Cancelacion voluntaria',
        'SOLICITUD CANCELACION VOLUNTARIA',
        NULL,
        (SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                EMPRESA_COD = '18'
                AND CODIGO_PRODUCTO = 'CANCV'
                AND DESCRIPCION_PRODUCTO = 'CANCELACION VOLUNTARIA'
                AND NOMBRE_TECNICO = 'OTROSSERVICIOS'),
        'CANCELACION VOLUNTARIA', 
        'Activo',
        'eholguin',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_cancel_volun',
        '18',
        'N'
    );

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
    'PROM_PRECIO_INSTALACION',
    'Define tabla con los precios de Instalación por Ultima Milla y empresa',
    'FINANCIERO',
    'FACTURACION',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
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
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_PRECIO_INSTALACION'
      AND ESTADO             = 'Activo'
    ),
    'Valor de la instalación dependiendo de la ultima milla del servicio',
    'FO',
    'INSTALACION HOME',
    100,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_PRECIO_INSTALACION'
      AND ESTADO             = 'Activo'
    ),
    'Valor de la instalación dependiendo de la ultima milla del servicio',
    'CO',
    'INSTALACION HOME',
    50,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );


-- CANCELACION VOLUNTARIA
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
    'CANCELACION VOLUNTARIA',
    'Define los parámetro necesarios para la facturación por cancelacion voluntaria',
    'FINANCIERO',
    'FACTURACION',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1'
  );

COMMIT;

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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND ESTADO             = 'Activo'
    ),
    'Valor a cobrar por visitas técnicas con motivo NetlifeAssistance ',
    'NETLIFFEASSISTANCE',
    30,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND ESTADO             = 'Activo'
    ),
    'Tiempo en meses de permanencia mimima del servicio mandatorio Internet ',
    'PERMANENCIA MINIMA',
    24,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND ESTADO             = 'Activo'
    ),
    'Tiempo en meses de permanencia mimima del servicio mandatorio Internet ',
    'PERMANENCIA SERVICIOS ADICIONALES',
    12,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND ESTADO             = 'Activo'
    ),
    'Contiene los destinatarios a los que se debe enviar reporte de cancelaciones no facturadas.',
    'DESTINATARIOS RPT CANCELACION NO FACTURADA',
    'dbravo@netlife.net.ec,slopez@netlife.net.ec,ssalazar@netlife.net.ec,',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );

--  FACTURACION SOLICITUD DETALLADA

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
    'FACTURACION SOLICITUD DETALLADA',
    'PARAMETROS PARA FACTURACION POR SOLICITUD DETALLADA. DESCRIPCIÓN: ITEM, V1=PRODUCTO_ID FACT, V2=PLAN_ID FACT, V3=CARACTERISTICA_ID,V4=PRODUCTO_ID NC, V5=PLAN_ID NC',
    'FINANCIERO',
    'FACTURACION',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1'
  );

COMMIT;


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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'Dcto. Adicional',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'PROMO SUSCRIPCIONX3M'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO ADICIONAL'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'S'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'Descuentos',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'PROMO SUSCRIPCIONX3M'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTOS'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'PROMO SUSCRIPCIONX3M'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Activo'),
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'Instalacion',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'INSTALACION'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Inactivo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'INSTALACION'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Inactivo'),
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'NetlifeAssistance',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'NetlifeAssistance' AND ESTADO = 'Activo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'NETLIFEASSISTANCE'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'NetlifeCloud',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'NetlifeCloud' AND ESTADO = 'Activo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'NETLIFECLOUD'
      AND ESTADO             = 'Activo'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'Equipos',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'EQUIPOS VARIOS' AND ESTADO = 'Activo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'EQUIPOS'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CPE ONT TELLION',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'ONT'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'HAR'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT TELLION'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6    
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CPE ONT HUAWEI',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'CPE Huawei'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'CPEH'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT HUAWEI'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CPE WIFI TELLION',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'WIFI'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'HAR'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CPE WIFI TELLION'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'ROSETA',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'ROSETA (Huawei)'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'RSTA'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'ROSETA'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CPE ADSL',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'CPE ADSL'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'EQV02'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'EQUIPO ADSL'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'EQUIPO AP CISCO',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'Renta Smart WiFi'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'EW03'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'EQUIPO AP CISCO'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'FUENTE DE PODER AP CISCO',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'FUENTE DE PODER'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'EQV01'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER AP CISCO'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'FUENTE DE PODER',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'FUENTE DE PODER'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.CODIGO_PLAN  = 'EQV01'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'lcabrera'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'N'
  );

COMMIT;
/