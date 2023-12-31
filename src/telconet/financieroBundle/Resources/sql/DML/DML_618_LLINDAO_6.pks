-- Creacion configuracion nuevo producto 205 -- 4310201004
insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE
     (ID_CUENTA_CONTABLE,
      NO_CIA,
      CUENTA,
      TABLA_REFERENCIAL,
      CAMPO_REFERENCIAL,
      VALOR_CAMPO_REFERENCIAL,
      NOMBRE_OBJETO_NAF,
      TIPO_CUENTA_CONTABLE_ID,
      DESCRIPCION,
      EMPRESA_COD,
      OFICINA_ID,
      FE_CREACION,
      USR_CREACION,
      IP_CREACION,
      ESTADO)
SELECT DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL,
      ACC.NO_CIA,
      '4310201004' CUENTA,
      ACC.TABLA_REFERENCIAL,
      ACC.CAMPO_REFERENCIAL,
      205 VALOR_CAMPO_REFERENCIAL,
      ACC.NOMBRE_OBJETO_NAF,
      ACC.TIPO_CUENTA_CONTABLE_ID,
      ACC.DESCRIPCION,
      ACC.EMPRESA_COD,
      ACC.OFICINA_ID,
      SYSDATE FE_CREACION,
      'db_financiero' USR_CREACION,
      '127.0.0.1' IP_CREACION,
      ACC.ESTADO
FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 56
AND ACC.TIPO_CUENTA_CONTABLE_ID = 7
AND ACC.NO_CIA = '18'
AND EXISTS (SELECT NULL
            FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE ATCC
            WHERE ATCC.ID_TIPO_CUENTA_CONTABLE = ACC.TIPO_CUENTA_CONTABLE_ID
            AND ATCC.DESCRIPCION = 'PRODUCTOS');

-- Creacion configuracion nuevo producto 1186 -- 4610105004
INSERT INTO DB_FINANCIERO.ADMI_CUENTA_CONTABLE
     (ID_CUENTA_CONTABLE,
      NO_CIA,
      CUENTA,
      TABLA_REFERENCIAL,
      CAMPO_REFERENCIAL,
      VALOR_CAMPO_REFERENCIAL,
      NOMBRE_OBJETO_NAF,
      TIPO_CUENTA_CONTABLE_ID,
      DESCRIPCION,
      EMPRESA_COD,
      OFICINA_ID,
      FE_CREACION,
      USR_CREACION,
      IP_CREACION,
      ESTADO)
SELECT DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL,
      ACC.NO_CIA,
      '4610105004' CUENTA,
      ACC.TABLA_REFERENCIAL,
      ACC.CAMPO_REFERENCIAL,
      1186 VALOR_CAMPO_REFERENCIAL,
      ACC.NOMBRE_OBJETO_NAF,
      ACC.TIPO_CUENTA_CONTABLE_ID,
      ACC.DESCRIPCION,
      ACC.EMPRESA_COD,
      ACC.OFICINA_ID,
      SYSDATE FE_CREACION,
      'db_financiero' USR_CREACION,
      '127.0.0.1' IP_CREACION,
      ACC.ESTADO
FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 51
AND ACC.NO_CIA = '18'
AND EXISTS (SELECT NULL
            FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE ATCC
            WHERE ATCC.ID_TIPO_CUENTA_CONTABLE = ACC.TIPO_CUENTA_CONTABLE_ID
            AND ATCC.DESCRIPCION = 'PRODUCTOS');
            
            
-- Creacion condifuracion nuevo producto 1187 -- 4110101008
INSERT INTO DB_FINANCIERO.ADMI_CUENTA_CONTABLE
     (ID_CUENTA_CONTABLE,
      NO_CIA,
      CUENTA,
      TABLA_REFERENCIAL,
      CAMPO_REFERENCIAL,
      VALOR_CAMPO_REFERENCIAL,
      NOMBRE_OBJETO_NAF,
      TIPO_CUENTA_CONTABLE_ID,
      DESCRIPCION,
      EMPRESA_COD,
      OFICINA_ID,
      FE_CREACION,
      USR_CREACION,
      IP_CREACION,
      ESTADO)
SELECT DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL,
      ACC.NO_CIA,
      '4110101008' CUENTA,
      ACC.TABLA_REFERENCIAL,
      ACC.CAMPO_REFERENCIAL,
      1187 VALOR_CAMPO_REFERENCIAL,
      ACC.NOMBRE_OBJETO_NAF,
      ACC.TIPO_CUENTA_CONTABLE_ID,
      ACC.DESCRIPCION,
      ACC.EMPRESA_COD,
      ACC.OFICINA_ID,
      SYSDATE FE_CREACION,
      'db_financiero' USR_CREACION,
      '127.0.0.1' IP_CREACION,
      ACC.ESTADO
FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 51
AND ACC.NO_CIA = '18'
AND EXISTS (SELECT NULL
            FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE ATCC
            WHERE ATCC.ID_TIPO_CUENTA_CONTABLE = ACC.TIPO_CUENTA_CONTABLE_ID
            AND ATCC.DESCRIPCION = 'PRODUCTOS');

--
-- cambiar a tipo de cuenta del producto 1128 de 7-productos a 33-portador en facturas
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.TIPO_CUENTA_CONTABLE_ID = 33,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610103001'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1128
AND ACC.TIPO_CUENTA_CONTABLE_ID = 7
AND ACC.NO_CIA = '18';
--
-- cambiar a tipo de cuenta del producto 1128 de 36-procuto_nc a 46-servicios en Notas de Créditos
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.Tipo_Cuenta_Contable_Id = 46,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610103002'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1128
AND ACC.TIPO_CUENTA_CONTABLE_ID = 36
AND ACC.NO_CIA = '18';

-- cambiar a tipo de cuenta del producto 1128 de 7-productos a 33-portador en facturas
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.TIPO_CUENTA_CONTABLE_ID = 33,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610103001'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1130
AND ACC.TIPO_CUENTA_CONTABLE_ID = 7
AND ACC.NO_CIA = '18';
--
-- cambiar a tipo de cuenta del producto 1128 de 36-procuto_nc a 46-servicios en Notas de Créditos
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.Tipo_Cuenta_Contable_Id = 46,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610103002'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1130
AND ACC.TIPO_CUENTA_CONTABLE_ID = 36
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4710104001 por 4410101003 al producto 1140
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4410101003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1140
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4710104001 por 4410101003 al producto 1142
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4410101003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1142
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102001 por 4610102003 al producto 213
-- se cambia en tipo cuenta portador
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610102003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 213
AND ACC.TIPO_CUENTA_CONTABLE_ID = 33
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102001 por 4610102003 al producto 213
-- se cambia en tipo cuenta servicio
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610102004'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 213
AND ACC.TIPO_CUENTA_CONTABLE_ID = 46
AND ACC.NO_CIA = '18';

-- cambiar a tipo de cuenta del producto 1128 de 7-productos a 33-portador en facturas
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.TIPO_CUENTA_CONTABLE_ID = 33,
    ACC.CUENTA = '4610105003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1128
AND ACC.TIPO_CUENTA_CONTABLE_ID = 7
AND ACC.NO_CIA = '18';
--
-- cambiar a tipo de cuenta del producto 1128 de 36-procuto_nc a 46-servicios en Notas de Créditos
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.Tipo_Cuenta_Contable_Id = 46,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610103002'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1130
AND ACC.TIPO_CUENTA_CONTABLE_ID = 36
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610101002 por 4610105003 al producto 51
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610105003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 51
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4310101003 por 4410101003 al producto 69
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4410101003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 69
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102001 por 4610103003 al producto 93
-- se cambia en tipo cuenta portador
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610103003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 93
AND ACC.TIPO_CUENTA_CONTABLE_ID = 33
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102002 por 4610103004 al producto 93
-- se cambia en tipo cuenta servicio
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610103004'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 93
AND ACC.TIPO_CUENTA_CONTABLE_ID = 46
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610105003 por 4610105003 al producto 939
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610105003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 939
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102001 por 4610102003 al producto 94
-- se cambia en tipo cuenta portador
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610102003'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 94
AND ACC.TIPO_CUENTA_CONTABLE_ID = 33
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610102002 por 4610102004 al producto 94
-- se cambia en tipo cuenta servicio
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610102004'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 94
AND ACC.TIPO_CUENTA_CONTABLE_ID = 46
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610105003 por 4610105005 al producto 1017
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610105005'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1017
AND ACC.NO_CIA = '18';

-- cambiar cuenta contable 4610105003 por 4610105005 al producto 1143
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610105005'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 1143
AND ACC.NO_CIA = '18';

-- cambiar a tipo de cuenta del producto 201 de 71-productos a 33-portador en facturas
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.TIPO_CUENTA_CONTABLE_ID = 33,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610102001'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 201
AND ACC.TIPO_CUENTA_CONTABLE_ID = 7
AND ACC.NO_CIA = '18';
--
-- cambiar a tipo de cuenta del producto 201 de 36-procuto_nc a 46-servicios en Notas de Créditos
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.Tipo_Cuenta_Contable_Id = 46,
    ACC.CAMPO_REFERENCIAL = 'ID_PORTADOR',
    ACC.CUENTA = '4610102002'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 201
AND ACC.TIPO_CUENTA_CONTABLE_ID = 36
AND ACC.NO_CIA = '18';
--
-- se habilita contabilziacion MD
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET APD
SET APD.VALOR2 = 'S'
WHERE APD.VALOR1 = 'MD'
AND EXISTS (SELECT NULL
            FROM ADMI_PARAMETRO_CAB APC
            WHERE APC.ID_PARAMETRO = APD.PARAMETRO_ID
            AND APC.NOMBRE_PARAMETRO = 'PROCESO CONTABILIZACION EMPRESA');

-- cambiar cuenta contable 4610105003 por 4610105005 al producto 79
-- se cambia a producto  y producto_nc
UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
SET ACC.CUENTA = '4610105005'
WHERE ACC.VALOR_CAMPO_REFERENCIAL = 79
AND ACC.TIPO_CUENTA_CONTABLE_ID = 36
AND ACC.NO_CIA = '18';


-- cambio de productos en planes
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4944 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4945 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4946 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4947 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4951 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4952 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4954 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4955 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4956 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4957 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4892 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4893 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 53 WHERE ID_ITEM = 4896 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 53 WHERE ID_ITEM = 4897 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4899 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4900 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4903 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4904 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4905 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4906 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4909 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4910 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4911 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4912 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4913 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4914 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4916 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4917 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4918 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 56 WHERE ID_ITEM = 4927 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4928 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4929 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4930 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4931 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4932 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4933 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4934 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4935 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4940 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4941 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 63 WHERE ID_ITEM = 4942 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1187 WHERE ID_ITEM = 4943 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1186 WHERE ID_ITEM = 3975 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 1186 WHERE ID_ITEM = 3976 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 78 WHERE ID_ITEM = 5387 ;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 234 WHERE ID_ITEM = 4908;
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 234 WHERE ID_ITEM in (4953,4949,4938,4950,4948);
  UPDATE DB_COMERCIAL.INFO_PLAN_DET SET PRODUCTO_ID = 210 WHERE ID_ITEM in (4894,4902);

--Parametros que indican que documentos generan detalle producto, se configura sin empresa porque ambas empresas procesan los mismos documentos
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'DOCUMENTOS_DETALLE_PRODUCTOS', 'FAC', 'NULL', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', NULL);
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'DOCUMENTOS_DETALLE_PRODUCTOS', 'FACP', 'NULL', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', NULL);
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'DOCUMENTOS_DETALLE_PRODUCTOS', 'NC', 'NULL', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', NULL);
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'DOCUMENTOS_DETALLE_PRODUCTOS', 'NCI', 'NULL', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', NULL);
  --
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET APD
  SET APD.VALOR2 = DECODE(APD.ID_PARAMETRO_DET, 5670, 1, 5671, 5, 5672, 6)
  WHERE APD.ID_PARAMETRO_DET IN (5670, 5671, 5672);
  --
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET APD
  SET APD.VALOR1 = 'S'
  WHERE APD.DESCRIPCION = 'DECIMALES_CONTABILIZACION'
  AND APD.ESTADO = 'Activo'
  AND APD.EMPRESA_COD = '18'
  AND EXISTS ( SELECT NULL
               FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
               WHERE APC.ID_PARAMETRO = APD.PARAMETRO_ID
               AND APC.NOMBRE_PARAMETRO = 'VALIDACIONES_PROCESOS_CONTABLES'
               AND APC.ESTADO = 'Activo');
--
COMMIT;
--