delete from db_general.admi_parametro_det where id_parametro_det = (
  select id_parametro_det from db_general.admi_parametro_det
    where parametro_id = 
      (select id_parametro from db_general.admi_parametro_cab  where nombre_parametro = 'AUTOMATIZACION PAGOS' and rownum=1)
  and descripcion = 'FORMATO_FECHA');
