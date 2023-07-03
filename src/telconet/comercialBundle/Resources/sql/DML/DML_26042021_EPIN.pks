UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2 = '1', VALOR3 = '2', VALOR4 = '27/04/2021', VALOR5 = 'certificadosEmpresa'
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CERTIFICADO_DIGITAL'); 

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
       (SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL'),
       'LEER_CERTIFICADO_NFS', 'N', null, null, null, 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, null, null, null, null,null);
COMMIT;
/