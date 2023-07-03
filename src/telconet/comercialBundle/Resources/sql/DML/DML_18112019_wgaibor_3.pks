--Creación de las redes y subredes de la ip 10.21.9.0/24
DECLARE
  PV_PREFIJORED  VARCHAR2(200);
  PV_INICIORED   NUMBER;
  PV_FINRED      NUMBER;
  PV_PRIMEROCT   VARCHAR2(200);
  PV_TERCEROCT   VARCHAR2(200);
  PV_TIPOUSO     VARCHAR2(200);
  LV_MENSAERROR  VARCHAR2(200);
  PV_TIPOPREFIJO VARCHAR2(200);
BEGIN
  PV_PREFIJORED  := '21';
  PV_INICIORED   := 0;
  PV_FINRED      := 255;
  PV_PRIMEROCT   := '10';
  PV_TERCEROCT   := '9';
  PV_TIPOUSO     := 'DATOS-FWA';
  DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.INFRP_CREAR_REDES_Y_SUBREDES( PV_PREFIJORED  => PV_PREFIJORED,
                                                                       PV_INICIORED   => PV_INICIORED,
                                                                       PV_FINRED      => PV_FINRED,
                                                                       PV_PRIMEROCT   => PV_PRIMEROCT,
                                                                       PV_TERCEROCT   => PV_TERCEROCT,
                                                                       PV_TIPOUSO     => PV_TIPOUSO,
                                                                       PV_TIPOPREFIJO => PV_TIPOPREFIJO,
                                                                       LV_MENSAERROR  => LV_MENSAERROR );
  DBMS_OUTPUT.PUT_LINE('LV_MENSAERROR = ' || LV_MENSAERROR);
  UPDATE DB_INFRAESTRUCTURA.INFO_SUBRED INSU
  SET INSU.ESTADO = 'Activo',
  INSU.USO = 'DATOS-FWA'
  WHERE INSU.SUBRED LIKE '10.21.9.%';
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.PUT_LINE(SQLERRM);
END; 
/