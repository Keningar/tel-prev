/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Configuración para lectura de reporte de tributación.
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 26-01-2021 
 */

  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_PAGO_AUTOMATICO
  (
      ID_FORMATO_PAGO_AUTOMATICO,
      EMPRESA_COD,
      FILA_INICIA,
      COL_FECHA,
      COL_CONCEPTO,
      COL_TIPO,
      COL_MONTO,
      COL_REFERENCIA,
      HOJA,
      TIPO_ARCHIVO,
      COL_VALIDA_TIPO,
      COL_VALIDA_REF,
      ESTADO,
      IP_CREACION,
      USR_CREACION,
      FE_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      FORMATO_FECHA
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_PAGO_AUT.NEXTVAL,
      '10',
      '2',
      '5',      
      '3',
      '6',
      '7',
      '1',
      '0',
      'XLS',
      'RPT_RET',
      '2',
      'Activo',
      '127.0.0.1',
      'eholguin',
      SYSDATE, 
      'eholguin',
      SYSDATE,
      'dd/mm/aaaa'
  );

COMMIT;
/
