
/**
 * @author José Alava <jialava@telconet.ec>
 * @version 1.0
 * @since 24-06-2019
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con validaciones de licencias.
 */

--CAB.  
  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'SISTEMA OPERATIVO',
    'LICENCIAS PARA SISTEMAS OPERATIVOS',
    'COMERCIAL',
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null
  );


  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'BASE DE DATOS',
    'LICENCIAS PARA BASE DE DATOS',
    'COMERCIAL',
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null
  );

    INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'APLICACIONES',
    'LICENCIAS PARA APLICACIONES',
    'COMERCIAL',
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null
  ); 

-- DET.
--Cumplen con la primera condicion

--DB de 4
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Licencia SQL Server Standard SPLA 2008,2012,2014,2016',
    1,
    4,
    4,
    4,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016',
    1,
    4,
    4,
    4,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Lic. Sql Server WebEdit Core Licencia SQL Server Web Edition SPLA',
    1,
    4,
    4,
    4,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );
--DB de 2
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Adicional Licencia SQL Server Standard SPLA 2008,2012,2014,2016',
    1,
    4,
    4,
    2,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Adicional Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016',
    1,
    4,
    4,
    2,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS'),
    'Adicional Licencia SQL Server Web Edition SPLA 2008,2012,2014,2016',
    1,
    4,
    4,
    2,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

-- WINDOWS
    INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Licencia Windows Server STD 2008/2012/2016 Core Fisico o Virtual',
    null,
    null,
    null,
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

    INSERT
      INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
      (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
        'Lic. Windows Svr DC - Core Licencia Windows Server DataCenter por Core 9EA-0039',
        null,
        null,
        null,
        null,
        'Activo',
        'jialava',
        sysdate,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        10,NULL,NULL,NULL
      );

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Licencia Windows Server STD 2008/2012/2016 Core Fisico o Virtual',
        null,
        null,
        null,
        null,
        'Activo',
        'jialava',
        sysdate,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        10,NULL,NULL,NULL
      );

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Lic. Windows Svr DC - Proc Licencia Windows Server DataCenter por Procesador P71-01031',
    null,
    null,
    null,
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );


INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Lic. Windows Svr DC - Core Licencia Windows Server DataCenter por Core 9EA-0039',
    null,
    null,
    null,
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );



--
--RED HAT

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Suscripcion Red Hat Large Instance ( Más de 4 Cores fisicos o Virtuales) (COD: MCT2568)',
    5,
    null,
    null,
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );



INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO'),
    'Suscripcion Red Hat Small Instance ( hasta 4 Cores fisicos o Virtuales) (COD: MCT2567)',
    null,
    4,
    null,
    null,
    'Activo',
    'jialava',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );




--

COMMIT;


/