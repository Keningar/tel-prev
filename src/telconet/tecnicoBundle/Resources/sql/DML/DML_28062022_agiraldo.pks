
-- script para crea un parametro para asociar una  vlan a  un servicio de clear Channel
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
 VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'REGULARIZACION IP OTN','Asociar las vlan en el proceso de regularizacion','TECNICO','REGULARIZACION','Activo','agiraldo',sysdate,'127.0.0.1');

-- script para crea un parametro con la  mascara de red /31
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
 VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MASCARA_SUBREDES'),'MASCARAS DE RED PARA SUBREDES POR PE',
        '/31','255.255.255.254','1','Activo','agiraldo',sysdate,'127.0.0.1','10');


-- script para crear un parametro con un Nuevo Uso de Subredes para el proceso de regualrizacion 
-- de CLEAR CHANNEL
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3,valor4,estado, usr_creacion,
                                     fe_creacion,ip_creacion,empresa_cod  )
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_USO'),
    'TIPOS DE USO EN SUBREDES POR PE','CLEAR CHANNEL','0','0', '0','Activo','agiraldo', SYSDATE, '127.0.0.1','10' );


COMMIT;
/
  