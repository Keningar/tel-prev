SET SERVEROUTPUT ON
UPDATE DB_FINANCIERO.INFO_CONSUMO_CLOUD_CAB
SET ESTADO = 'Validado'
WHERE ESTADO = 'Revision';
COMMIT;
/
DECLARE
  PN_EMPRESACOD      VARCHAR2(2);
  PV_NOMBRE_PRODUCTO VARCHAR2(300);
  PV_MENSAJE         VARCHAR2(1000);
BEGIN
  PN_EMPRESACOD := '10';
  PV_NOMBRE_PRODUCTO := 'COU LINEAS TELEFONIA FIJA';
  DB_FINANCIERO.FNCK_FACTURACION_CLOUD_TN.P_FACTURACION_CLOUD( PV_NOMBRE_PRODUCTO, PN_EMPRESACOD );
  commit;  
END;
/