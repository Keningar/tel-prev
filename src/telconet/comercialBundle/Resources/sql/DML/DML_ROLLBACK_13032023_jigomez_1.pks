
/**
* DELETE DE PARAMETROS PARA TIPOS DE POSTE
* 
* @author Jorge Gomez <jigomez@telconet.ec>
* @version 1.0 13-03-2023
* 
*/


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE DESCRIPCION = 'TIPO DE POSTE';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPOS DE POSTE';

COMMIT;

/