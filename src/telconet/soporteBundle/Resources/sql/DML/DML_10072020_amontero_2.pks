DECLARE
  PN_ANIO_DESDE NUMBER;
  PV_POR_ESTADO VARCHAR2(200);
  PV_MENSAJE_RESPUESTA VARCHAR2(200);
BEGIN
  PN_ANIO_DESDE := 2019;
  PV_POR_ESTADO := 'TODAS';

  DB_SOPORTE.SPKG_INFO_TAREA.P_MIGRAR_TAREAS(
    PN_ANIO_DESDE => PN_ANIO_DESDE,
    PV_POR_ESTADO => PV_POR_ESTADO,
    PV_MENSAJE_RESPUESTA => PV_MENSAJE_RESPUESTA
  );
END;

/
