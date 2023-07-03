BEGIN
delete from db_general.admi_parametro_det where parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APCA
      WHERE APCA.NOMBRE_PARAMETRO = 'SHOW_TAG_BY_EMPRESA')
     AND VALOR2 = 'detalle_adicional' and usr_creacion = 'gnarea';
     commit;
END;