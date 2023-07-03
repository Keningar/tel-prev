
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
where nombre_parametro = 'VALIDA_VALOR_PROYECTO_NAF' and estado='Activo');

Delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where NOMBRE_PARAMETRO='VALIDA_VALOR_PROYECTO_NAF' ;

Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
where nombre_parametro = 'DATOS_TAREA_CONTADOR' and estado='Activo');

Delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where NOMBRE_PARAMETRO='DATOS_TAREA_CONTADOR' ;

commit;

/