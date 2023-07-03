
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='PARAMS_PRODS_TN_GPON'),
'Parámetro con los ids de los productos y sus velocidades disponibles',
'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
(select ID_PRODUCTO from DB_COMERCIAL.admi_producto where DESCRIPCION_PRODUCTO='TelcoHome'),
'10',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='PARAMS_PRODS_TN_GPON'),
'Parámetro con los ids de los productos y sus velocidades disponibles',
'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
(select ID_PRODUCTO from DB_COMERCIAL.admi_producto where DESCRIPCION_PRODUCTO='TelcoHome'),
'20',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='PARAMS_PRODS_TN_GPON'),
'Parámetro con los ids de los productos y sus velocidades disponibles',
'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
(select ID_PRODUCTO from DB_COMERCIAL.admi_producto where DESCRIPCION_PRODUCTO='TelcoHome'),
'50',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='PARAMS_PRODS_TN_GPON'),
'Parámetro con los ids de los productos y sus velocidades disponibles',
'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
(select ID_PRODUCTO from DB_COMERCIAL.admi_producto where DESCRIPCION_PRODUCTO='TelcoHome'),
'75',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10
);

commit;
/