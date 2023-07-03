/**
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 29-03-2019 
  * Se insertan los parámetros de configuracion de facturación para telconet Guatemala
  */
  
    /*configuracion de la numeracion de la factura*/
    Insert INTO DB_COMERCIAL.ADMI_NUMERACION(
        ID_NUMERACION,
        EMPRESA_ID,
        OFICINA_ID,
        Descripcion,
        Codigo,
        NUMERACION_UNO,
        NUMERACION_DOS,
        SECUENCIA,
        FE_CREACION,
        Usr_Creacion,
        TABLA,
        ESTADO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
       (SELECT COD_EMPRESA 
         From DB_COMERCIAL.INFO_EMPRESA_GRUPO  
         WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'),
       (SELECT ID_OFICINA 
         FROM DB_COMERCIAL.INFO_OFICINA_GRUPO  
         WHERE ESTADO='Activo' 
         AND ES_OFICINA_FACTURACION='S' 
         and EMPRESA_ID=(SELECT COD_EMPRESA
                          FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
                          WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA')),
        'Num de Fac. para TELCONET - Guatemala',
        'FAC',
        '001',
        '001',
        '1',
        sys_extract_utc(systimestamp),
        'kyager',
        'info_documento_financiero_cab',
        'Activo'
      );

    /*configuracion de la numeracion de la NC*/
    INSERT INTO DB_COMERCIAL.ADMI_NUMERACION(
        ID_NUMERACION,
        EMPRESA_ID,
        OFICINA_ID,
        DESCRIPCION,
        CODIGO,
        NUMERACION_UNO,
        NUMERACION_DOS,
        SECUENCIA,
        FE_CREACION,
        USR_CREACION,
        TABLA,
        ESTADO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'),
        (SELECT ID_OFICINA FROM DB_COMERCIAL.INFO_OFICINA_GRUPO 
           WHERE ESTADO='Activo' 
           AND ES_OFICINA_FACTURACION='S' 
           AND EMPRESA_ID=(SELECT COD_EMPRESA 
                            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
                            WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA')),
          'Numeración de nota de crédito TELCONET - Panama',
          'NC',
          '001',
          '001',
          '1',
           sys_extract_utc(systimestamp),
          'kyager',
          'info_documento_financiero_cab',
          'Activo');

    /*Actualiza a la empresa para que aplique la facturacion electronica*/
    UPDATE DB_COMERCIAL.INFO_EMPRESA_GRUPO 
        SET FACTURA_ELECTRONICO='N'
         WHERE COD_EMPRESA=(SELECT COD_EMPRESA  
                               FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
                                WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA');

    /*agregar caracteristicas para moneda quetlzal y fecha de conversion*/
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
       'VALOR DOC QUETZALES',
       'N',
       'Activo',
        sys_extract_utc(systimestamp),
       'kyager',
       'FINANCIERO');


    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
       'FECHA CONVERSION QUETZALES',
       'C',
       'Activo',
       sys_extract_utc(systimestamp),
       'kyager',
       'FINANCIERO');


    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(
       ID_CARACTERISTICA,
       DESCRIPCION_CARACTERISTICA,
       TIPO_INGRESO,
       ESTADO,
       FE_CREACION,
       USR_CREACION,
       TIPO 
    )VALUES(
       DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
      'TIPO CAMBIO QUETZALES',
      'N',
      'Activo',
      sys_extract_utc(systimestamp),
      'kyager',
      'FINANCIERO');

    /*Num de Pag. TELCONET - Guatemala - para opcionde pagos*/
    INSERT INTO DB_COMERCIAL.ADMI_NUMERACION(
       ID_NUMERACION,
       EMPRESA_ID,
       OFICINA_ID,
       DESCRIPCION,
       CODIGO,
       NUMERACION_UNO,
       NUMERACION_DOS,
       SECUENCIA,
       FE_CREACION,
       USR_CREACION,
       TABLA,
       ESTADO
    )VALUES(
       DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
       (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'),
       (SELECT ID_OFICINA 
           FROM DB_COMERCIAL.INFO_OFICINA_GRUPO 
           WHERE Estado='Activo' 
           AND ES_OFICINA_FACTURACION='S' 
           AND EMPRESA_ID=(SELECT COD_EMPRESA 
                           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
                           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA')),
        'Num de Pag. TELCONET - Guatemala',
        'PAG',
        '001',
        '232',
        '1',
        sys_extract_utc(systimestamp),
        'kyager',
        'info_pago_cab',
        'Activo');

    /*Num de Pag. TELCONET - Guatemala - para opcionde anticipos*/
    INSERT INTO DB_COMERCIAL.ADMI_NUMERACION(
        ID_NUMERACION,
        EMPRESA_ID,
        OFICINA_ID,
        DESCRIPCION,
        CODIGO,
        NUMERACION_UNO,
        NUMERACION_DOS,
        SECUENCIA,
        FE_CREACION,
        USR_CREACION,
        TABLA,
        ESTADO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'),
        (SELECT ID_OFICINA 
           FROM DB_COMERCIAL.INFO_OFICINA_GRUPO  
           WHERE ESTADO='Activo' 
           AND ES_OFICINA_FACTURACION='S'
           AND EMPRESA_ID=(SELECT COD_EMPRESA 
                              FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
                              WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA')),
        'Num de Ant. TELCONET - Guatemala',
        'ANT',
        '001',
        '232',
        '1',
        sys_extract_utc(systimestamp),
        'kyager',
        'info_pago_cab',
        'Activo');


    /*Num de Pag. TELCONET - Guatemala - para opcion de curce de anticipos*/
    INSERT INTO DB_COMERCIAL.ADMI_NUMERACION(
        ID_NUMERACION,
        EMPRESA_ID,
        OFICINA_ID,
        DESCRIPCION,
        CODIGO,
        NUMERACION_UNO,
        NUMERACION_DOS,
        SECUENCIA,
        FE_CREACION,
        USR_CREACION,
        TABLA,
        ESTADO
    )VALUES(
        DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'),
        (SELECT ID_OFICINA 
           FROM DB_COMERCIAL.INFO_OFICINA_GRUPO  
           WHERE ESTADO='Activo' 
           AND ES_OFICINA_FACTURACION='S' 
           AND EMPRESA_ID=(SELECT COD_EMPRESA 
                              FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
                              WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA')),
        'Num de Pagc. TELCONET - Guatemala',
        'PAGC',
        '001',
        '232',
        '1',
        sys_extract_utc(systimestamp),
        'kyager',
        'info_pago_cab',
        'Activo');

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO, 
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('CANTONES_OFICINAS_COMPENSADAS')),
        'OFICINA COMPENSADA TN',
        'TELCONET - GUATEMALA',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO IN ('OPCIONES_HABILITADAS_FINANCIERO')),
        'FACP',
        'PRECARGADA_SIN_FRECUENCIA',
        'S',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )values(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('OPCIONES_HABILITADAS_FINANCIERO')),
        'FAC',
        'OPCIONES_FECHA_CONSUMO',
        'S',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       VALOR2,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('OPCIONES_HABILITADAS_FINANCIERO')),
      'FAC',
      'PRECARGADA_SIN_FRECUENCIA',
      'S',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1',
      (SELECT COD_EMPRESA 
        FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
        WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('SECUENCIALES_POR_EMPRESA')),
       'FAC',
       '9',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
       (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    -- actualizar IVA
    UPDATE DB_GENERAL.ADMI_IMPUESTO 
      SET TIPO_IMPUESTO='IVA_GT' 
      WHERE PAIS_ID=(SELECT ID_PAIS 
                        FROM DB_GENERAL.ADMI_PAIS 
                        WHERE NOMBRE_PAIS='GUATEMALA');


    /*NUEVO PARAMETRO CAB PARA EL ENVIO DE PARAMETROS EN WS y para configuracion de metodo*/
    -- parametros
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
       ID_PARAMETRO,
       NOMBRE_PARAMETRO,
       DESCRIPCION, 
       MODULO,
       ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'WEB_SERVICE_PARAMETROS',
      'PARAMETROS RELACIONADOS AL WS PARA GENERAR XML',
      'FINANCIERO',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1');

    -- metodo
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION) 
    VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'WEB_SERVICE_METODO',
        'METODO RELACIONADOS AL WS PARA GENERAR XML',
        'FINANCIERO',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1');

    -- metodo
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
       ID_PARAMETRO,
       NOMBRE_PARAMETRO,
       DESCRIPCION, 
       MODULO,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION
    )values(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'WEB_SERVICE_METODO_URL',
      'METODO COMPLEMENTARIO AL WS PARA GENERAR XML',
      'FINANCIERO',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1');

    /****CONFIGURACION DE PARAMETROS PARA ENVIO ED WS*******/
    -- NAMESPACE
    INSERT INTO DB_GENERAL.Admi_Parametro_Det(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    ) values(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')),
       'NAMESPACE',
       'xmlns="http://www.banguat.gob.gt/variables/ws/"',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
       (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- PATH_CERT
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    ) VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')),
       'PATH_CERT',
       'www.banguat.gob.gt',
       'Activo',
       'kyager',
       CURRENT_DATE,
      '127.0.0.1',
       (SELECT COD_EMPRESA 
          FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
          WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    -- PWS_CERT
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')),
       'PWS_CERT',
       'www.banguat.gob.gt',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
       (SELECT COD_EMPRESA 
          FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
          WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- URL
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       ESTADO, 
       USR_CREACION,
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')),
       'URL',
       'http://www.banguat.gob.gt/variables/ws',
       'Activo',
       'kyager',
       CURRENT_DATE,
      '127.0.0.1',
       (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE Nombre_Empresa='TELCONET GUATEMALA'));

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1, 
      ESTADO, 
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD
    )VALUES(
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')),
      'FECHA',
      'CURRENT_DATE',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1',
      (SELECT COD_EMPRESA 
         FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
         WHERE Nombre_Empresa='TELCONET GUATEMALA'));

    --metodo ws

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_METODO')),
        'METODO',
        'TipoCambioRango',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    --complemeto url metodo ws
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_METODO_URL')),
       'METODO_URL',
       'TipoCambio.asmx?op=',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    /*CONFIGURACIÓN DE VARIABLES PARA ARMAR ARRAY DE WS FACE*/
    -- variables consulta ws soap
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
       ID_PARAMETRO,
       NOMBRE_PARAMETRO,
       DESCRIPCION,
       MODULO,
       ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION
    )VALUES( 
       DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'WEB_SERVICE_VARIABLES',
      'VARIABLES PARA EL ENVIO DE ARRAY XML',
      'FINANCIERO',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1');

    -- clave 
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO in ('WEB_SERVICE_VARIABLES')),
      'CLAVE',
      'B2FDC80789AFAF22C372965901B16DF533A4FCB19FD9F2FD5CBDA554032983B0',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1',
      (SELECT COD_EMPRESA 
         FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
         WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- codigoEstablecimiento 
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
       'codigoEstablecimiento',
       '1',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
       (SELECT COD_EMPRESA 
          FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
          WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- codigoMoneda

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'codigoMoneda',
        'GTQ',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --descripcionOtroImpuesto

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION, 
        VALOR1, 
        ESTADO, 
        USR_CREACION,
        FE_CREACION, 
        IP_CREACION,
        EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
             WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'descripcionOtroImpuesto',
        'N/A',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- idDispositivo

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'idDispositivo',
        '1',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- serieDocumento FACTURA
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       VALOR2,
       ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'serieDocumento',
        '63',
        'FAC',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- serieDocumento NC

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1,
       VALOR2, 
       ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
          'serieDocumento',
          '64',
          'NC',
          'Activo',
          'kyager',
          CURRENT_DATE,
          '127.0.0.1',
          (SELECT COD_EMPRESA 
             FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
             WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --tipoDocumento FACTURA
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1,
       VALOR2, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'tipoDocumento',
        'FACE',
        'FAC',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
            WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    --tipoDocumento NC
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       VALOR2, 
       ESTADO, 
       USR_CREACION,
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'tipoDocumento',
        'NCE',
        'NC',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- tipoCambio
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1, 
        ESTADO, 
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'tipoCambio',
        '1.00',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- serieAutorizada
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION, 
        VALOR1, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'serieAutorizada',
        'ABCD',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    -- fechaResolucion

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, VALOR1, ESTADO, 
       USR_CREACION,
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
       DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
       'fechaResolucion',
       '2013-02-15 10:22:02',
       'Activo',
       'kyager',
       CURRENT_DATE,
       '127.0.0.1',
       (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));


    --numeroResolucion
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'numeroResolucion',
        '123456789',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --usuario

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO  
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'usuario',
        'DEMO',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --nitGFACE
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1, 
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD
    )VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('WEB_SERVICE_VARIABLES')),
        'nitGFACE',
        '12521337',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO  
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --  ****PERIODO_FACTURACION*******
    -- NAMESPACE
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION, 
       VALOR1,
       VALOR2,
       ESTADO, 
       USR_CREACION, 
       FE_CREACION,
       IP_CREACION,
       EMPRESA_COD) 
    VALUES(
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO IN ('PERIODO_FACTURACION')),
      'PERIODO DE FACTURACION TNG',
      '28',
      '27',
      'Activo',
      'kyager',
      CURRENT_DATE,
      '127.0.0.1',
      (SELECT COD_EMPRESA 
          FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
          WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    --  ****PROCESO CONTABILIZACION EMPRESA*******
    -- NAMESPACE
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
       ID_PARAMETRO_DET,
       PARAMETRO_ID,
       DESCRIPCION,
       VALOR1, 
       VALOR2,
       ESTADO, 
       USR_CREACION,
       FE_CREACION, 
       IP_CREACION,
       EMPRESA_COD) 
    VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO 
           FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
           WHERE NOMBRE_PARAMETRO IN ('PROCESO CONTABILIZACION EMPRESA')),
        'DEFINE SI SE HABILITA CONTABILIZACION DE TNG',
        'TNG',
        'N',
        'Activo',
        'kyager',
        CURRENT_DATE,
        '127.0.0.1',
        (SELECT COD_EMPRESA 
           FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
           WHERE NOMBRE_EMPRESA='TELCONET GUATEMALA'));

    COMMIT;
/