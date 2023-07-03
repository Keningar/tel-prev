
delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
where nombre_parametro = 'VALIDA_PROD_ADICIONAL' and estado='Activo');

delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
where nombre_parametro = 'BLOQUEO_TRASLADO_ADICIONAL' and estado='Activo');

delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where nombre_parametro='VALIDA_PROD_ADICIONAL';

delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where nombre_parametro='BLOQUEO_TRASLADO_ADICIONAL';

delete from DB_COMERCIAL.ADMI_CARACTERISTICA
where DESCRIPCION_CARACTERISTICA='PRODUCTO_ADICIONAL' and USR_CREACION='mdleon';

COMMIT;

/