  /**
  * @author Kevin Villegas <kmvillegas@telconet.ec>
  * @version 1.0 17-11-2022 Se realizan updates para la pantilla de pagos Automaticos y Manuales se ingresa Numero de Telefono y Base Celular.
  */ 
      
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono PBX 04-6020650 / 04-3922000 Extensiones: 5811 - 5815 - 5817 - 5819 - 5821 - 5823 - 5825 - 5801' ,
VALOR4='Celular  0999067676',
VALOR5='Ejecutivo',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Guayaquil');


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono  073-922000  ext.  5811' ,
VALOR4='B. Celular  098 499 8718',
VALOR5='Alias',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Cuenca');


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono 02-3963100  ext  2901-2911-2913-2915-2917-2919-2921-2923' ,
VALOR4='B. Celular  098 4880 669',
VALOR5='Ejecutivo',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Quito');


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono  042-779528' ,
VALOR4='B. Celular  099 550 4312',
VALOR5='Alias',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Salinas');


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'N/A' ,
VALOR4='B. Celular  099 925 2262',
VALOR5='Alias',
FE_ULT_MOD=SYSDATE
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Quevedo');



UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono  05 2669011  ext.  2005-2017' ,
VALOR4='B. Celular  099 906 7677',
VALOR5='Alias',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Manta');

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET 
VALOR3= 'Telefono  073-952007  ext.  5801' ,
VALOR4='B. Celular  099 841 8692',
VALOR5='Alias',
FE_ULT_MOD=SYSDATE,
OBSERVACION = 'CONFIGURA PARAMETROS PARA NOTIFICACION A ALIAS DE COBRANZA POR OFICINA VALOR1= ID_OFICINA VALOR2= CORREO DE ALIZA VALOR3= TELEFONO DE OFICINA VALOR4= BASE CELULAR VALOR5= A QUIEN SE VA HA ENVIAR EL CORREO EJECUTIVO O ALIAS DE COBRANZA .'
WHERE  
PARAMETRO_ID =(SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo')
AND VALOR1=(SELECT TO_CHAR(ID_OFICINA) FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='TELCONET - Loja');


COMMIT;
/