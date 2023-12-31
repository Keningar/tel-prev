--reverso de la info alias plantilla
DELETE DB_COMUNICACION.INFO_ALIAS_PLANTILLA
      WHERE PLANTILLA_ID IN ( SELECT ID_PLANTILLA FROM DB_COMUNICACION.ADMI_PLANTILLA
          WHERE CODIGO IN ('PROMBW_PROC','PROMBW_DET','PROM_BW_REP') AND ESTADO = 'Activo');

--reverso de la admi plantilla
DELETE DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO IN ('PROMBW_PROC','PROMBW_DET','PROM_BW_REP') AND ESTADO = 'Activo';

--reverso de los detalles de parametros de las promociones de ancho de banda
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
      WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo');

--reverso de la cabecera de parametros de las promociones de ancho de banda
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo';

COMMIT;
/
