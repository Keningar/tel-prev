 delete from db_general.admi_parametro_det a 
  where  a.parametro_id=
  (select b.id_parametro from ADMI_PARAMETRO_CAB b where b.nombre_parametro = 'ANIO_VIGENCIA_TARJETA');
  
delete from db_general.admi_parametro_cab b where b.nombre_parametro = 'ANIO_VIGENCIA_TARJETA';
commit;
/