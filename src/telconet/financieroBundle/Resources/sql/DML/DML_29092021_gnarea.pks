insert into db_general.admi_parametro_det values (
db_general.seq_admi_parametro_det.nextval,
(select id_parametro from db_general.admi_parametro_cab  where nombre_parametro = 'AUTOMATIZACION PAGOS' and rownum=1),
'FORMATO_FECHA', 'd-m-Y', '-', null, null,'Activo', 'gnarea', sysdate, '0.0.0.0',null, null, null, null, '10', null, null, 'Valor1: Formato de Fecha Y(anio) m(mes) d(dia); Valor2:Separador de fecha');