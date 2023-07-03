/*
* Se realiza la creación de características para asociar a un punto o servicio al portal 3dEYE tomando como base el plan NetCam.
* @author Marlon Plúas <mpluas@telconet.ec>
* @version 1.0 23-12-2019
*/


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
    'CAMARA 3DEYE',
    'N',
    'Activo',
    SYSDATE,
    'mpluas',
    NULL,
    NULL,
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )	
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO 
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE NOMBRE_TECNICO = 'CAMARA IP'
     AND ESTADO = 'Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA 3DEYE'
     AND ESTADO = 'Activo'
    ),
    SYSDATE,
    NULL,
    'mpluas',
    NULL,
    'Activo',
    'SI'
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'PORTAL 3DEYE',
    'DATOS DEL CUSTOMER EN EL PORTAL 3DEYE',
    'COMERCIAL',
    'NETCAM',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'PORTAL 3DEYE' AND ESTADO = 'Activo'),
    'Datos del customer en el portal 3dEYE',
    'cartieda@netlife.net.ec',
    ')6p7K3KfuB[q',
    'fnLNWdbhSx8sB64AWbytN7q0u4W6iWsz5HqJhiKk',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/token',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1 = USERNAME, VALOR2 = PASSWORD, VALOR3 = API-KEY, VALOR4 = GENERAR TOKEN'
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'ENDPOINT ROL 3DEYE',
    'RUTAS ENDPOINT MODULO ROL 3DEYE',
    'COMERCIAL',
    'NETCAM',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ENDPOINT ROL 3DEYE' AND ESTADO = 'Activo'
    ),
    'Rutas endpoint modulo roles del portal 3dEYE',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/roles',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/roles/roleId',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/roles/roleId/users',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/roles/roleId/users/userId',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/roles/roleId/cameras/cameraId',
    18,
    NULL,
    NULL,
    'VALOR1=ROL(LISTAR, CREAR), VALOR2=ROL(ELIMINAR), VALOR3=USERS BY ROL, VALOR4=USER BY ROL(ASIGNAR Y REMOVER), VALOR5=CAM BY ROL(ASIGNAR Y REMOVER)'
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'ENDPOINT CAMARA 3DEYE',
    'RUTAS ENDPOINT MODULO CAMARA 3DEYE',
    'COMERCIAL',
    'NETCAM',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ENDPOINT CAMARA 3DEYE' AND ESTADO = 'Activo'
    ),
    'Rutas endpoint modulo cámaras del portal 3dEYE',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras/cameraId',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras/cameraId/state',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras/CreateP2P',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras/CreateOnvif',
    18,
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/cameras/CreateGeneric',
    NULL,
    'VALOR1=CAM(LISTAR), VALOR2=CAM BY ID(MOSTRAR, ACTUALIZAR, ELIMINAR), VALOR3=STATUS CAM, VALOR4=CAM P2P(CREAR), VALOR5=CAM ONVIF(CREAR), VALOR6=CAM GENERIC(CREAR)'
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'ENDPOINT USER 3DEYE',
    'RUTAS ENDPOINT MODULO USER 3DEYE',
    'COMERCIAL',
    'NETCAM',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ENDPOINT USER 3DEYE' AND ESTADO = 'Activo'
    ),
    'Rutas endpoint modulo cámaras del portal 3dEYE',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/user',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/user/password',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/user/password/reset',
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/users',
    'Activo',
    'mpluas',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'http://telcos-ws-ext-lb.telconet.ec/57kbm3g9idaytm11dt0/8rfdiha97/7d895tu4/v2/users/userId',
    18,
    NULL,
    NULL,
    'VALOR1=USER(MOSTRAR Y ACTUALIZAR DATOS DEL CUSTOMER), VALOR2=CAMBIAR PASS CUSTOMER(PUT), VALOR3=RESET PASS USER(POST), VALOR4=USER DEL CUSTOMER(LISTAR, CREAR), VALOR5=USER BY ID(MOSTRAR, ELIMINAR Y ACTUALIZAR)'
  );

COMMIT;

/

