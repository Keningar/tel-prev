insert into DB_GENERAL.ADMI_PARAMETRO_CAB (id_parametro, nombre_parametro, descripcion, modulo, proceso, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod)
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'URL_INTERFAZ_API', 'URL DE INTERFAZ CON API PARA IMPRESORA PANAMA', 'FINANCIERO', 'EMISION DE FACTURA IMPRESORA FISCAL', 'Activo', 'sfernandez', to_date('07-05-2018 15:35:57', 'dd-mm-yyyy hh24:mi:ss'), '127.0.0.1', null, null, null);
--
insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(  SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'URL_INTERFAZ_API'), 'URL_INTERFAZ_API', 'http://192.168.102.101:80/panama/comprobante', null, null, null, 'Activo', 'sfernandez', to_date('07-05-2018 18:27:51', 'dd-mm-yyyy hh24:mi:ss'), '0.0.0.0', null, null, null, null, '26');

commit;