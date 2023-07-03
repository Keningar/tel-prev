--PARÁMETRO CAB APLICA_PAQUETE_HORAS
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION)
  VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'PROD_APLICA_PAQUETE_HORAS', 'PARÁMETRO UTILIZADO PARA CONFIGURAR LOS PRODUCTOS DE HORAS DE SOPORTE, SI = APLICAN, NO = NO APLICAN', 'COMERCIAL', 'GENERACIÓN LOGIN AUX', 'Activo', 'vpena', sysdate, '127.0.0.1');
    
    

 --PARÁMETRO DET APLICA_PAQUETE_HORAS
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION, EMPRESA_COD)
  VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROD_APLICA_PAQUETE_HORAS'), 
    'PRODUCTOS QUE APLICAN Y NO APLICAN PARA PAQUETE DE HORAS DE SOPORTE', '{
 "SI": [
   1449, 1438, 1437, 1436, 1278, 1260, 1259, 1238, 1196, 1190,
   1191, 1181, 1152, 1151, 1149, 1150, 1148, 1147, 1146, 1137,
   1127, 1107, 1098, 1087, 1086, 1085, 1084, 1050, 1049, 1048, 
   1047, 1046, 1045, 1044, 1043, 1042, 1041, 1040, 1039, 1038, 
   1037, 1036, 1034, 1033, 1030, 982,  976,  975,  949,  873,
   848,  763,  316,  305,  299,  302,  273,  225,  224,  223, 
   222,  1346, 1345, 1343, 1342, 1340, 1339, 1134, 1125, 1124,
   1121, 1120, 1016, 1015, 1014, 1013, 1011, 891,  1456, 1448,
   1249, 1239, 910,  907,  1452, 1443, 1398, 1396, 1393, 1392,
   1365, 1285, 1283, 1102, 1101, 1100, 1099, 1410, 1366, 1257,
   1138, 1136, 1117, 1116, 908,  896,  862,  276,  1206, 1200,
   1081, 1364, 1069, 1430, 1428, 1420, 1399, 986,  984,  980, 
   838,  766,  765,  289,  233,  228
    ]
}', 'Activo', 'vpena', sysdate, '127.0.0.1', '10');

commit;