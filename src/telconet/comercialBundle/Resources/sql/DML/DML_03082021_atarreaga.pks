/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 03-08-2021  
 * Se crea DML de configuraciones para adulto mayor fase 3.
 */

--caracteristica
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
  DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
  'TIPO_CATEGORIA_PLAN_ADULTO_MAYOR',
  'T',
  'Activo',
  SYSDATE,
  'atarreaga',
  'COMERCIAL',
  NULL);

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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'CATEGORIA_PLAN_ADULTO_MAYOR',
    'BASICO',
    NULL,
    'PLAN_BASICO',
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'Parámetro que identifica el tipo de plan basico para asignarse como característica en el plan'
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'CATEGORIA_PLAN_ADULTO_MAYOR',
    'COMERCIAL',
    'default',
    'PLAN_COMERCIAL',
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'Parámetro que identifica el tipo de plan comercial para asignarse como característica en el plan'
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
    CTA_CONTABLE,
    REF_MOTIVO_ID
  ) 
  VALUES
 (
   DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
   438,
   '3era Edad Resolución 07-2021',
   'Activo',
   'atarreaga',
   SYSDATE,
   'atarreaga',
   SYSDATE,
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'APLICA_DESC_TIPO_PLAN_COMERCIAL',
    'S',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'VALOR1: Representa el valor de: S->Habilitado  N->Deshabilitado.'
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
    VALOR6,
    VALOR7,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_DESC_ADULTO_MAYOR',
    '3era Edad Resolución 07-2021',
    'RECALCULO',
    'CANCEL_CAMBIO_BENEFICIO',
    'LIBERACION_BENEFICIO',
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'CAMBIO_PLAN',
    'PROCESO_3ERA_EDAD_RESOLUCION_072021',
    (SELECT ID_MOTIVO FROM DB_COMERCIAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = '3era Edad Resolución 07-2021'),
    '18',
    'VALOR1:Indica el nombre motivo de 3era edad. VALOR2:Indica el proceso requerido a usarse. VALOR3:Indica el proceso requerido a usarse. VALOR4:Indica el proceso requerido a usarse. VALOR5:Indica el proceso requerido a usarse. VALOR6: Indica el tipo de flujo del motivo. VALOR7: Indica el id_motivo del beneficio.'
  );    

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2='RECALCULO', VALOR3='CANCEL_CAMBIO_BENEFICIO', VALOR4='LIBERACION_BENEFICIO', VALOR6='PROCESO_3ERA_EDAD_ADULTO_MAYOR', VALOR7=(SELECT ID_MOTIVO FROM DB_COMERCIAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = 'Beneficio 3era Edad / Adulto Mayor'),
OBSERVACION='VALOR1:Indica el nombre motivo de 3era edad. VALOR2:Indica el proceso recálculo. VALOR3:Indica el proceso cancelación y cambio beneficio. VALOR4:Indica el proceso liberación beneficio. VALOR6: Indica el tipo de flujo del motivo. VALOR7: Indica el id_motivo del beneficio.'
     WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM  DB_GENERAL.ADMI_PARAMETRO_CAB 
                          WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR')
        AND DESCRIPCION = 'MOTIVO_DESC_ADULTO_MAYOR'
        AND VALOR1      = 'Beneficio 3era Edad / Adulto Mayor'; 

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR4='LIBERACION_BENEFICIO', VALOR7=(SELECT ID_MOTIVO FROM DB_COMERCIAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = 'Cliente con Discapacidad'),
OBSERVACION='VALOR4: Indica el proceso liberación beneficio. VALOR7: Indica el id_motivo del beneficio.'    
     WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM  DB_GENERAL.ADMI_PARAMETRO_CAB 
                          WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD')
        AND DESCRIPCION = 'MOTIVO_DESC_DISCAPACIDAD'
        AND VALOR1      = 'Cliente con Discapacidad'; 
  
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'PORCENTAJE_DESC_RESOLUCION_072021_ADULTO_MAYOR',
    '50',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por plan comercial',
    'MENSAJE_VALIDACION_TIPO_CATEGORIA_PLAN',
    'Beneficio Adulto Mayor No aplica a Planes Comerciales ',
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'VALOR1: Mensaje para validación del tipo de plan.'
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por plan comercial si no existe',
    'MENSAJE_VALIDACION_NOEXISTE_TIPO_CATEGORIA_PLAN',
    'No existe Plan Básico. Imposible otorgar beneficio ',
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'VALOR1: Mensaje para validación tipo de categoría plan básico no existente.'
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_CONTRATO',
    'Pendiente',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_CONTRATO',
    'PorAutorizar',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_CONTRATO',
    'Activo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL
  ); 

UPDATE DB_GENERAL.ADMI_MOTIVO SET ESTADO = 'Eliminado' WHERE NOMBRE_MOTIVO = 'Beneficio 3era Edad / Adulto Mayor'; 

COMMIT;
/
