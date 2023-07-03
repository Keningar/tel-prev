
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET NOMBRE_TECNICO = 'HOSTING' WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS LICENCIAMIENTO SE' AND ESTADO = 'Activo';

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_MULTIPLE_CARACTERISTICAS',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_HOUSING',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'HYPERVIEW',
    'S',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'TECNICO'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TIPO ALQUILER SERVIDOR_VALUE',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'TECNICO'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'DESCUENTO_POR_CARACTERISTICA',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

-----------------------------------

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'HOUSING Alquiler de Espacio Físico' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOUSING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ES_HOUSING'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS POOL RECURSOS LOC' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'HYPERVIEW'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS POOL RECURSOS LOC' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ES_MULTIPLE_CARACTERISTICAS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS ALQUILER SERVIDOR' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ES_MULTIPLE_CARACTERISTICAS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS LICENCIAMIENTO SE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ES_MULTIPLE_CARACTERISTICAS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS ALQUILER SERVIDOR' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO ALQUILER SERVIDOR_VALUE'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS ALQUILER SERVIDOR' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO LICENCIAMIENTO SERVICE'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

-- dsctos

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS ALQUILER SERVIDOR' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO_POR_CARACTERISTICA'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS LICENCIAMIENTO SE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO_POR_CARACTERISTICA'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS POOL RECURSOS LOC' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'HOSTING'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO_POR_CARACTERISTICA'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

-----------------------------------------------------------------

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA SET DESCRIPCION_CARACTERISTICA = 'DISCO_VALUE' WHERE DESCRIPCION_CARACTERISTICA = 'STORAGE_VALUE';

UPDATE DB_COMERCIAL.ADMI_CARACTERISTICA SET DESCRIPCION_CARACTERISTICA = 'MEMORIA RAM_VALUE' WHERE DESCRIPCION_CARACTERISTICA = 'MEMORIA_VALUE';

--- CARACTERISTICAS DE SUB-AGRUPACION

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SUBTIPOS_CORE_CONFIGURADOS',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

--

--INTERNET DC
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET DC' AND EMPRESA_COD = 10 AND ESTADO = 'Activo'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPOS_CORE_CONFIGURADOS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

--CROSSCONECCION
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CROSSCONEXION DC COMUNICACION' AND EMPRESA_COD = 10 AND ESTADO = 'Activo'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPOS_CORE_CONFIGURADOS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

--DATOS DC
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'DATOS DC' AND EMPRESA_COD = 10),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPOS_CORE_CONFIGURADOS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');

--CONCENTRADOR CLIENTES DC
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'Concentrador Clientes DC' AND EMPRESA_COD = 10),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPOS_CORE_CONFIGURADOS'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SUBTIPO_SOLUCION',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

/

DECLARE
idCaracteristica number := null;
BEGIN

   FOR PRODUCTO IN
  (
      SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE EMPRESA_COD = 10 AND ESTADO = 'Activo' AND GRUPO = 'DATACENTER' AND
        NOMBRE_TECNICO <> 'FINANCIERO'
  )
  LOOP      
  
      INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,PRODUCTO.ID_PRODUCTO,
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPO_SOLUCION'),
      SYSDATE,NULL,'arsuarez',NULL,'Activo','NO');     
  
  END LOOP;
  
  COMMIT;

EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  raise_application_error(-20001,'UN ERROR A OCURRIDO - '||SQLERRM || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' -ERROR- '||SQLERRM);
END;

/

-- TIPO DE SUBSOLUCIONES POR GRUPO DE PRODUCTO CONFIGURADO

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'GRUPO PRODUCTOS CON SUB TIPO SOLUCION',
    'GRUPO PRODUCTOS CON SUB TIPO SOLUCION',
    'COMERCIAL',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GRUPO PRODUCTOS CON SUB TIPO SOLUCION'),
    'DATACENTER',
    'HOUSING',
    NULL,
    '#DF7401',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GRUPO PRODUCTOS CON SUB TIPO SOLUCION'),
    'DATACENTER',
    'CLOUD IAAS - DEDICADO',
    NULL,
    '#0431B4',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GRUPO PRODUCTOS CON SUB TIPO SOLUCION'),
    'DATACENTER',
    'CLOUD IAAS - COMPARTIDO',
    NULL,
    '#0489B1',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GRUPO PRODUCTOS CON SUB TIPO SOLUCION'),
    'DATACENTER',
    'COMUNICACIONES',
    'P',
    '#298A08',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GRUPO PRODUCTOS CON SUB TIPO SOLUCION'),
    'DATACENTER',
    'OTROS',
    NULL,
    '#D7DF01',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

-- AGREGAR LOS NUEVOS TIPO DE ELEMENTOS ( HYPERVIEW )

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_TIPO_ELEMENTO.NEXTVAL,
    'HYPERVIEW',
    'HYPERVIEW',
    'ACTIVO',
    'Activo',
    'arsuarez',
    sysdate,
    NULL,
    NULL,
    'BACKBONE'
  );

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_MODELO_ELEMENTO.NEXTVAL,
    '21',
    (SELECT ID_TIPO_ELEMENTO FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO WHERE NOMBRE_TIPO_ELEMENTO = 'HYPERVIEW'),
    'MODELO HYPERVIEW',
    'ESTO ES UN MODELO DE HYPERVIEW DATA CENTER',
    NULL,
    'DIAS',
    NULL,
    'DIAS',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'GR',
    '1',
    NULL,
    'BPS',
    NULL,
    'BPS',
    NULL,
    'W',
    NULL,
    'W',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    'arsuarez',
    sysdate,
    'SI'
  );

--EDITAR PARAMETROS DE ASIGNACION DE TAREA PARA ASIGNACION DE RECURSOS DE RED
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2 = 'ipcc_l2_gye@telconet.ec', VALOR4 = 'IPCCL2' WHERE DESCRIPCION = 'ASIGNAR RECURSOS RED ALQUILER SERVIDORES' AND ESTADO = 'Activo' 
AND VALOR1 = 'GUAYAQUIL';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2 = 'ipcc_l2_uio@telconet.ec', VALOR4 = 'IPCCL2' WHERE DESCRIPCION = 'ASIGNAR RECURSOS RED ALQUILER SERVIDORES' AND ESTADO = 'Activo' 
AND VALOR1 = 'QUITO';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 'Data Center Ti', ESTADO = 'Eliminado' WHERE DESCRIPCION = 'RECURSOS DE RED' AND ESTADO = 'Activo' AND VALOR2 = 'INTERNET DC';

-- parametros para segmentar los productos especiales para la asignacion de recursos de red

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO',
    'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO',
    'COMERCIAL',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO'),
    'RECURSOS DE RED',
    'INTERNET DC',
    'ES_POOL_RECURSOS',
    'IPCCL2',
    'TODO',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO'),
    'RECURSOS DE RED',
    'INTERNET DC',
    'ES_ALQUILER_SERVIDORES',
    'IPCCL2',
    'TODO',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

--adicional
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO'),
    'RECURSOS DE RED',
    'INTERNET DC',
    'ES_HOUSING',
    'IPCCL2',
    'TODO',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );
----------

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO'),
    'RECURSOS DE RED',
    'DATOS DC',
    'ES_POOL_RECURSOS',
    'IPCCL2',
    'TODO',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO'),
    'RECURSOS DE RED',
    'DATOS DC',
    'ES_ALQUILER_SERVIDORES',
    'IPCCL2',
    'TODO',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );


-- excepcion de productos que no son flujos normales

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES'),
    'RECURSOS RED',
    'INTERNET DC',
    NULL,
    NULL,
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES'),
    'RECURSOS RED',
    'DATOS DC',
    NULL,
    NULL,
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );
