-- script para Eliminar el parametro con la  mascara de red /31
   DELETE
   FROM
   DB_GENERAL.ADMI_PARAMETRO_DET where DESCRIPCION = 'MASCARAS DE RED PARA SUBREDES POR PE'
   AND USR_CREACION ='regularizacion OTN'
   AND VALOR1 ='/31';

-- script para Eliminar el parametro con un Nuevo Uso de Subredes para el proceso de regualrizacion 
-- de CLEAR CHANNEL
  DELETE
   FROM
   DB_GENERAL.ADMI_PARAMETRO_DET where DESCRIPCION = 'TIPOS DE USO EN SUBREDES POR PE'
   AND USR_CREACION ='regularizacion OTN'
   AND VALOR1 ='CLEAR CHANNEL';

COMMIT;
/