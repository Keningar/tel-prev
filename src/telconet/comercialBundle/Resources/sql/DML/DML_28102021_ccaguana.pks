INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL'),
'ESTADOS ADENDUM VALIDOS MOVIL', 'Pendiente,PorAutorizar,Activo,Migrado,Traslado,Factible', NULL, NULL, NULL, 'Activo', 'ccaguana', sysdate, '127.0.0.1', NULL, NULL,NULL,NULL,'18',NULL,NULL, 'Estados de ademdums que filtrara para visualizarse en el movil');

COMMIT;
   