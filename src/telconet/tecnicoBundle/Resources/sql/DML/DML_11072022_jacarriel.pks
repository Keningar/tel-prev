/**
 * Se crean parametros para tabla ADMI_PARAMETRO_DET y ADMI_PARAMETRO_CAB de productos sin data tecnica
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0
 * @since 11/07/2022
 */

--INGRESO DE CABECERA
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PRODUCTOS SIN DATA TECNICA',
    'Productos que no tienen data tecnica para mostrar la encuesta desde app movil',
    'TECNICO',
    'Activo',
    'jacarriel',
     SYSDATE,
    '127.0.0.1'
  );
 
--INGRESO DE DETALLE
 INSERT INTO db_general.admi_parametro_det 
 (
   ID_PARAMETRO_DET,
   PARAMETRO_ID,
   DESCRIPCION,
   VALOR1,
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
     db_general.seq_admi_parametro_det.NEXTVAL,
     (
         SELECT
             id_parametro
         FROM
             db_general.admi_parametro_cab
         WHERE
             nombre_parametro = 'PRODUCTOS SIN DATA TECNICA'
     ),
     'PRODUCTOS MOVIL',
     '1200',
     'Activo',
     'jacarriel',
     SYSDATE,
     '127.0.0.0', 
     NULL,
     NULL,
     NULL,
     NULL,
     NULL,
     NULL,
     NULL,
     NULL
 );

COMMIT;