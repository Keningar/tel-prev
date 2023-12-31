 * DEBE EJECUTARSE EN DB_GENERAL
 * Rollback para parametros de conexion con orquestador y para producto NG_FIREWAL
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 15-09-2022.
 */

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET B
WHERE B.PARAMETRO_ID =
  (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A
  WHERE A.NOMBRE_PARAMETRO   = 'CARACTERISTICA_DE_LA_INSTANCIA'
  AND A.MODULO               = 'SOPORTE'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB A
 WHERE A.NOMBRE_PARAMETRO   = 'CARACTERISTICA_DE_LA_INSTANCIA'
 AND A.MODULO               = 'SOPORTE'

COMMIT;
/
