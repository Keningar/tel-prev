SET SERVEROUTPUT ON;
DECLARE
  LV_NOMBRE_PARAMETRO VARCHAR2(100) := 'PARAMETROS_LINEAS_TELEFONIA';
  LN_ID_PARAMETRO NUMBER;
  LV_RANGO_CONSUMO VARCHAR2(100) := 'PARAM_RANGO_CONSUMO';
  LV_PRODUCTO_RANGO VARCHAR2(100) := 'PARAM_PRODUCTO_RANGOFECHA';
  LV_FECHA_CONSUMO_WS VARCHAR2(100) := 'PARAM_FECHA_CONSUMO_WS';
  LV_FORMATO_FECHA_BASE VARCHAR2(100) := 'PARAM_FORMATO_FECHA_BASE';
BEGIN
  SELECT ID_PARAMETRO INTO LN_ID_PARAMETRO 
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = LV_NOMBRE_PARAMETRO;

DELETE FROM   DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = LN_ID_PARAMETRO AND DESCRIPCION = LV_RANGO_CONSUMO;

DELETE FROM   DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = LN_ID_PARAMETRO AND DESCRIPCION = LV_PRODUCTO_RANGO;

DELETE FROM   DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = LN_ID_PARAMETRO AND DESCRIPCION = LV_FECHA_CONSUMO_WS;

DELETE FROM   DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = LN_ID_PARAMETRO AND DESCRIPCION = LV_FORMATO_FECHA_BASE;
  
commit;
END;