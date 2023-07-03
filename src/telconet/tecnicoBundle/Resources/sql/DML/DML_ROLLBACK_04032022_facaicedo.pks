
--reverso de los detalles de parametros de las promociones de ancho de banda
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 30000, VALOR2 = NULL
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'MAXIMO_REGISTROS_CLIENTES';

COMMIT;
/
