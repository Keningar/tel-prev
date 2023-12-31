/**
 * @author German Valenzuela <gvalenzuela@telconet.ec>
 * @version 1.0
 * @since 11-06-2021    
 * Se crea DML para insertar varias caracterisiticas para 
 * identificar los elementos que pertenecen a un nodo 
 */

--INGRESO DE LA CARACTERÍSTICA 'ELEMENTO NODO', SIRVE PARA IDENTIFICAR
--LOS ELEMENTOS QUE PERTENECEN A UN NODO.
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  TIPO
) VALUES (
  DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
 'ELEMENTO NODO',
 'N',
 'Activo',
  SYSDATE,
 'gvalenzuela',
 'TECNICA');

--INGRESO DE LA CARACTERÍSTICA 'CAMBIO ELEMENTO', SIRVE PARA IDENTIFICAR
--SI LA SOLICITUD DE RETIRO DE EQUIPO ES POR UN CAMBIO DE ELEMENTO.
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  TIPO
) VALUES (
  DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
 'CAMBIO ELEMENTO',
 'N',
 'Activo',
  SYSDATE,
 'gvalenzuela',
 'TECNICA');

--INGRESO DE LA CARACTERÍSTICA 'SOLICITUD NODO', SIRVE PARA ENLAZAR LA TAREA
--CON LAS SOLICITUDES CREADAS DE LOS ELEMENTOS QUE PERTENECEN A UN NODO.
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  TIPO
) VALUES (
  DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
 'SOLICITUD NODO',
 'N',
 'Activo',
  SYSDATE,
 'gvalenzuela',
 'TECNICA');

--INGRESO DE LOS NUEVOS PARÁMETROS, QUE SIRVE PARA REALIZAR EL FLUJO DE CAMBIO Y RETIRO DE LOS ELEMENTOS
--QUE PERTENECEN A UN NODO.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'GESTION_ELEMENTOS_NODO',
    'PARAMETROS QUE CONTIENEN LOS VALORES NECESARIOS PARA REALIZAR EL FLUJO DE CAMBIO Y RETIRO DE ELEMENTOS EN UN NODO',
    'TECNICO',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION_ELEMENTOS_NODO'
    ),
   'ID DE LA RELACION SISTEMA PARA OBTENER LOS MOTIVOS DE CAMBIO DE ELEMENTO',
   'solicitudCambioElemento',
   '12514',
   'SOLICITUD CAMBIO EQUIPO',
   'Activo',
   'gvalenzuela',
    SYSDATE,
   '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION_ELEMENTOS_NODO'
    ),
   'ID DE LA RELACION SISTEMA PARA OBTENER LOS MOTIVOS DE RETIRO DE ELEMENTO',
   'solicitudRetiroElemento',
   '12515',
   'SOLICITUD RETIRO EQUIPO',
   'Activo',
   'gvalenzuela',
    SYSDATE,
   '127.0.0.1'
);

COMMIT;
/
