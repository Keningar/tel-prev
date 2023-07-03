
/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback de creacion de motivos de cierre de casos Hal Megadatos
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 28-07-2021 - Versión Inicial.
 */

 
 DELETE DB_GENERAL.ADMI_PARAMETRO_DET S
  where S.PARAMETRO_ID = (SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A 
                          WHERE UPPER(A.NOMBRE_PARAMETRO) = 'MOTIVOS_CATEGORIA_FIN_CASO');
                                                                
 DELETE DB_GENERAL.ADMI_PARAMETRO_CAB S 
 where UPPER(S.NOMBRE_PARAMETRO) = 'MOTIVOS_CATEGORIA_FIN_CASO';

 DELETE DB_GENERAL.ADMI_PARAMETRO_DET S 
 where S.PARAMETRO_ID = (SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A 
                         WHERE UPPER(A.NOMBRE_PARAMETRO) = 'PARAMETROS_GENERALES_MOVIL')
    and S.VALOR1 = 'TIEMPO_ESPERA_REINTENTO';

 commit;
 /