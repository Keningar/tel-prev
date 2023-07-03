/**
 * Documentación ACTUALIZACIÓN E INSERT DE PARÁMETROS DE PROMOCIONES INSTALACIÓN
 *
 * Se actualizan parámetros de descuento para que la promoción tome el valor de la instalación,
 * y se pueda aplicar el % de la promoción respectiva, se insertan parámetros para saber que servicios ya no
 * estan funcionales.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 14-09-2019
 */
-- actualización parámetros valores de instalación
    UPDATE ADMI_PARAMETRO_DET APD set valor5='50' ,
      APD.OBSERVACION='V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO,V5= PRECIO DE INSTALACION PARA VALIDACION DE PROMOCIONES'
    WHERE apd.ESTADO = 'Activo'
      AND apd.VALOR2 = 'EFECTIVO'
      AND apd.VALOR1 = 'CO'
      AND APD.EMPRESA_COD = '18'; 
               
               
    UPDATE ADMI_PARAMETRO_DET APD set valor5='100',
      APD.OBSERVACION='V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO,V5= PRECIO DE INSTALACION PARA VALIDACION DE PROMOCIONES'
    WHERE apd.ESTADO = 'Activo'
      AND apd.VALOR2 = 'EFECTIVO'
      AND apd.VALOR1 = 'FO'
      AND APD.EMPRESA_COD = '18'; 

--configuración parámetros

--SERVICIO 
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
    'PROM_ESTADOS_BAJA_SERV',
    'Define los estados considerados para que un servicio no este en funcionamiento.',
    'COMERCIAL',
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1'
  );
  
--1
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Anulado',
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
    '18'
  );

--2  
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Cancelado',
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
    '18'
  );
  
--3  
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Inactivo',
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
    '18'
  );
  
--4  
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Eliminado',
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
    '18'
  );
  
--5 
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Reubicado',
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
    '18'
  );
  
--6 
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Trasladado',
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
    '18'
  );
  
--7 
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'MigradoOnnet',
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
    '18'
  );
  
--8
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Rechazada',
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
    '18'
  );
  
--9
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Rechazado',
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
    '18'
  );
  
--10
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'root',
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
    '18'
  );
  
--11
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'migracion_ttco',
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
    '18'
  );
  
--12
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Cancel',
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
    '18'
  );
  
--13
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'AnuladoMigra',
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
    '18'
  );
  
--14
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADOS_BAJA_SERV',
    'Migrado',
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
    '18'
  );

--Define los tipos de solicitudes que se dan por un cambio de OLT, el mismo que no ha sido solicitado por el cliente.
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
    'PROM_SOL_CAMBIOS_TEC',
    'Define los tipos de solicitudes que se dan por un cambio de OLT, el mismo que no ha sido solicitado por el cliente.',
    'COMERCIAL',
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1'
  );
  
--1
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
      WHERE NOMBRE_PARAMETRO = 'PROM_SOL_CAMBIOS_TEC'
      AND ESTADO             = 'Activo'
    ),
    'PROM_SOL_CAMBIOS_TEC',
    'SOLICITUD CAMBIO LINEA PON',
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
    '18'
  );

--2  
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
      WHERE NOMBRE_PARAMETRO = 'PROM_SOL_CAMBIOS_TEC'
      AND ESTADO             = 'Activo'
    ),
    'PROM_SOL_CAMBIOS_TEC',
    'SOLICITUD MIGRACION',
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
    '18'
  );
commit;
/
