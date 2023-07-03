declare
lv_id_parametro_cab int;
lv_id_parametro_det int;

begin
  select db_general.seq_admi_parametro_cab.nextval into lv_id_parametro_cab from dual;
  insert into db_general.admi_parametro_cab values(lv_id_parametro_cab, 'FACTURACION UNICA', 'PARAMETRO UTLIZADO PARA EL PROCESO DE FACTURACION UNICA', 'FINANCIERO', 'FACTURACION_UNICA', 'Activo', 'gnarea', sysdate, '0.0.0.0', null, null, null);
  dbms_output.put_line('Insercion cabecera exitosa '||lv_id_parametro_cab);

  select db_general.seq_admi_parametro_det.nextval into lv_id_parametro_det from dual;
  insert into db_general.admi_parametro_det values (lv_id_parametro_det,lv_id_parametro_cab, 'BANDERA_FECHA_ACTIVACION','S', null, null, null, 'Activo', 'gnarea', sysdate, '0.0.0.0', null,null, null,null,18,null,null,'S: INSERTA FECHA DE ACTIVACION. N: NO INSERTA FECHA DE ACTIVACION EN OBSERVACION DE DETALLE-FACTURA');
  dbms_output.put_line('Insercion detalle exitoso MD'||lv_id_parametro_det);
  
  select db_general.seq_admi_parametro_det.nextval into lv_id_parametro_det from dual;
  insert into db_general.admi_parametro_det values (lv_id_parametro_det,lv_id_parametro_cab, 'BANDERA_FECHA_ACTIVACION','N', null, null, null, 'Activo', 'gnarea', sysdate, '0.0.0.0', null,null, null,null,10,null,null,'S: INSERTA FECHA DE ACTIVACION. N: NO INSERTA FECHA DE ACTIVACION EN OBSERVACION DE DETALLE-FACTURA');
  dbms_output.put_line('Insercion detalle exitoso TN'||lv_id_parametro_det);
  
  commit;
end;
