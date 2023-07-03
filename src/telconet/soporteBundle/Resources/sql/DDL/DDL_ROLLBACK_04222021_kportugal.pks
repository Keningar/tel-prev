/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se elimina index y parametros creados para generacion de Nc por indisponibilidad
 * @author Katherine Portugal <kportugal@telconet.ec>
 * @version 1.0 22-04-2021 - Versión Inicial.
 */

DROP INDEX INFO_CASO_INDEX1;
DROP INDEX INFO_CASO_INDEX2;

DELETE
FROM DB_GENERAL.admi_parametro_det PARDET
WHERE PARDET.PARAMETRO_ID IN
  (SELECT PARCAB.ID_PARAMETRO
  FROM DB_GENERAL.admi_parametro_cab PARCAB
  WHERE PARCAB.Nombre_parametro = 'PARAMETROS DE INDISPONIBILIDAD PARA NC'
  OR PARCAB.Nombre_parametro    = 'PARAMETROS_REPORTE_NC_INDISPONIBILIDAD'
  AND PARCAB.ESTADO             = 'Activo'
  )
AND PARDET.ESTADO = 'Activo';
          
DELETE
FROM DB_GENERAL.admi_parametro_cab PARCAB
WHERE PARCAB.Nombre_parametro = 'PARAMETROS DE INDISPONIBILIDAD PARA NC'
OR PARCAB.Nombre_parametro    = 'PARAMETROS_REPORTE_NC_INDISPONIBILIDAD'
AND PARCAB.ESTADO             = 'Activo';

COMMIT;
/

