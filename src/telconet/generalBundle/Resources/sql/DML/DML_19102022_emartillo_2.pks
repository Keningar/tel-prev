/**
 *
 * Insert de parámetros .
 * Parametrizacion con el esquema DB_GENERAL
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 19-10-2022
 * 
 **/
DECLARE
  ln_id_param NUMBER := 0;
BEGIN

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROYECTO NETLIFECAM';
   

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2, 
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PRODUCTO CONFIGURADO PARA REGISTRAR ELEMENTO',
    (SELECT ID_PRODUCTO 
    FROM DB_COMERCIAL.ADMI_PRODUCTO 
    WHERE DESCRIPCION_PRODUCTO = 'NETLIFECAM OUTDOOR'),
    'netlifecam',
    'CAMARA',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18'
); 

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,   
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES(
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
    (SELECT NOMBRE_TECNICO
     FROM DB_COMERCIAL.ADMI_PRODUCTO
     WHERE CODIGO_PRODUCTO = 'VISEG'),
    'SOLICITAR NUEVO SERVICIO NETLIFECAM IN-DOOR',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18'
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,   
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES(
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
    (SELECT NOMBRE_TECNICO
     FROM DB_COMERCIAL.ADMI_PRODUCTO
     WHERE CODIGO_PRODUCTO = 'NLCO'),
    'SOLICITAR NUEVO SERVICIO NETLIFECAM OUTDOOR',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    '18'
);

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
  SET   VALOR3 = 'NETLIFECAM'
  WHERE VALOR1 = 'PERMANENCIA MINIMA NETLIFECAM';
COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;
/
/**
 *Antes de ejecutar estas sentencias primero debe ejecutar las
 *sentencias DML de la entidad DB_SOPORTE
 **/
Declare Ln_Idtarea   Number(5,0);
BEGIN

  SELECT ID_TAREA
  INTO Ln_Idtarea
  FROM DB_SOPORTE.ADMI_TAREA
  WHERE NOMBRE_TAREA='INSTALACION NETLIFECAM - Servicio Basico de Visualizacion Remota Residencial';
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
  	VALOR5,
  	EMPRESA_COD,
  	VALOR6,
  	VALOR7, 
  	OBSERVACION
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES'),
    'Listado de tareas asociadas a la solicitud',
    8,
    'SOLICITUD PLANIFICACION',
    null,
    Ln_Idtarea,
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    null,
    18,
    null, 
    null,
    'Valor1: idTipoSolicitud, Valor2: nombre de la solicitud, Valor4: idTarea asociada'
);
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
Declare Ln_Idtarea   Number(5,0);
BEGIN

  SELECT ID_TAREA
  INTO Ln_Idtarea
  FROM DB_SOPORTE.ADMI_TAREA
  WHERE NOMBRE_TAREA='INSTALACION NETLIFECAM - Outdoor';
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
  	VALOR5,
  	EMPRESA_COD,
  	VALOR6,
  	VALOR7, 
  	OBSERVACION
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES'),
    'Listado de tareas asociadas a la solicitud',
    8,
    'SOLICITUD PLANIFICACION',
    null,
    Ln_Idtarea,
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    null,
    18,
    null, 
    null,
    'Valor1: idTipoSolicitud, Valor2: nombre de la solicitud, Valor4: idTarea asociada'
);
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/



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
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CAMARA EZVIZ CS-C2C-A0-1E2WF', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        '45', --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C2C-A0-1E2WF'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'VISEG',
        'Activo',
        'emartillo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '45'
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
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CAMARA EZVIZ CS-C3N-A0-3G2WFL1', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        75, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C3N-A0-3G2WFL1'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'NLCO',
        'Activo',
        'emartillo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '75'
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
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'TARJETA MICRO SD 32 GB KINGSTON', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        6, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'TARJETA MICRO SD 32 GB KINGSTON'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'NLCO',
        'Activo',
        'emartillo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '6'
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
    'TARJETA MICRO SD',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'TARJETA MICRO SD'
      AND   PROD.EMPRESA_COD  = '18'
      AND   PROD.ESTADO       = 'Inactivo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TARJETA MICRO SD 32 GB KINGSTON'
      AND ESTADO             = 'Activo'
      AND TIPO = 'COMERCIAL'
    ),
    'NLCO',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
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
    'CAMARA EZVIZ CS-C3N-A0-3G2WFL1',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'CAMARA EZVIZ CS-C3N-A0-3G2WFL1'
      AND   PROD.EMPRESA_COD  = '18'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C3N-A0-3G2WFL1'
      AND TIPO = 'COMERCIAL'
    ),
    'NLCO',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
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
    'CAMARA EZVIZ CS-C2C-A0-1E2WF',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'CAMARA EZVIZ CS-C2C-A0-1E2WF'
      AND   PROD.EMPRESA_COD  = '18'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C2C-A0-1E2WF'
      AND TIPO = 'COMERCIAL'
    ),
    'VISEG',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    'N'
  );  


-- Se Inserta parámetro de permanencia mínima 24 Meses.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND    ESTADO           = 'Activo' ),
    'Tiempo en meses de permanencia mínima del servicio Netlifecam Outdoor',
    'PERMANENCIA MINIMA NETLIFECAM OUTDOOR',
    24,
    'NETLIFECAM OUTDOOR',
    NULL,
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );


  

  
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND    ESTADO           = 'Activo' ),
    'Mensaje de alerta para selección de equipos a facturar por cancelación servicio Netlifecam Outdoor',
    'MENSAJE NETLIFECAM OUTDOOR',
    'El cliente tiene contratado NetlifeCam Outdoor como producto adicional. Revisar entrega de equipos.',
    NULL,
    'NLCO',
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  COMMIT;
/

/**
 *
 * ACTUALIZACION DE PARAMETROS QUE SIRVEN PARA PALNIFICACION SIMULTANEA
 * Parametrizacion con el esquema DB_GENERAL
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 19-10-2022
 * 
 **/

       DECLARE
  ln_id_producto NUMBER := 0;
BEGIN

  SELECT ID_PRODUCTO
  INTO ln_id_producto
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE DESCRIPCION_PRODUCTO = 'NETLIFECAM OUTDOOR';
       
  
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos para planificacion con HAL'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR3      = ln_id_producto
  WHERE DESCRIPCION = 'Servicios manuales no activos se trasladan como nuevos'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');
         
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos manuales que cambian estado en origen'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');        
 
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos manuales que cambian estado en origen'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA');


  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos trasladados que deben inactivar tareas'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos adicionales manuales para planificar simultaneo'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR4      = ln_id_producto
  WHERE DESCRIPCION = 'Productos adicionales manuales para inactivar'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET   VALOR3      = ln_id_producto
  WHERE DESCRIPCION = 'Productos adicionales manuales para activar'
  AND   PARAMETRO_ID = (SELECT ID_PARAMETRO 
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES');

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;
/

/**
 *
 * Creación de parámetros para servicios de MD para mostrar modelos de camara Planificacion
 * Parametrizacion con el esquema DB_GENERAL
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 19-10-2022
 * 
 **/


DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN

  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de camara NetlifeCam Outdoor');
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'EZVIZ',
    'CAMARA',
    'NETLIFECAM OUTDOOR',
    'CS-C3N-A0-3G2WFL1',
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de camara NetlifeCam Outdoor');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;

/

DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN

  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'EZVIZ',
    'CAMARA',
    'NETLIFECAM',
    'CS-CV206(MINI-O)',
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de camara NetlifeCam');
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'EZVIZ',
    'CAMARA',
    'NETLIFECAM',
    'CS-C1C-D0-1D1WFR',
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de camara NetlifeCam');
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'EZVIZ',
    'CAMARA',
    'NETLIFECAM',
    'CS-C3N-A0-3G2WFL1',
    'Activo',
    'emartillo',
    sysdate,
    '127.0.0.1',
    '18'
  );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

/**
 *
 * Insert de parámetros para mostrar los check.
 * Parametrizacion con el esquema DB_GENERAL
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 19-10-2022
 * 
 **/


  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de productos adicionales automaticos','1461','NETLIFECAM OUTDOOR','NO',null,'Activo',
    'emartillo',sysdate,'127.0.0.1',null,18,null, null,
    'Valor1 es codigo del producto, valor2 es la descripcion, valor3 si es prodcuto Konibit'
);

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR6= 45
WHERE DESCRIPCION = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
AND PARAMETRO_ID = 847
AND VALOR4 = 'VISEG';


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR6= 35
WHERE DESCRIPCION = 'CAMARA EZVIZ CS-CV206(MINI-O)'
AND PARAMETRO_ID = 847
AND VALOR4 = 'VISEG';


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR6= 6
WHERE DESCRIPCION = 'MICRO SD 32GB KINGSTON'
AND PARAMETRO_ID = 847
AND VALOR4 = 'VISEG';

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de productos adicionales automaticos','78','NETLIFECAM - Servicio Básico de Visualización Remota Residencial','NO',null,'Activo',
    'emartillo',sysdate,'127.0.0.1',null,18,null, null,
    'Valor1 es codigo del producto, valor2 es la descripcion, valor3 si es prodcuto Konibit'
);
  /

SET SERVEROUTPUT ON
--Creación de parámetros con coordenadas por país
DECLARE
  Ln_IdParamServiciosFact NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamServiciosFact
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='SERVICIOS_ADICIONALES_FACTURAR';
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
    Ln_IdParamServiciosFact,
    'NOMBRE_TECNICO',
    NULL,
    'NETLIFECAM OUTDOOR',
    NULL,
    NULL,
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se agrega correctamente el producto con nombre técnico NETLIFECAM OUTDOOR');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

SET SERVEROUTPUT ON
--Creación de parámetros con coordenadas por país
DECLARE
  Ln_IdParamServiciosFact NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamServiciosFact
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='SERVICIOS_ADICIONALES_FACTURAR';
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
    Ln_IdParamServiciosFact,
    'NOMBRE_TECNICO',
    NULL,
    'NETLIFECAM OUTDOOR',
    NULL,
    NULL,
    'Activo',
    'emartillo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se agrega correctamente el producto con nombre técnico NETLIFECAM OUTDOOR');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/