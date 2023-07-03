--Se ejecutará este bloque anónimo para ejecutar el proceso para cargar toda la información de Octubre con el SLA de los clientes

DECLARE
BEGIN
  DB_SOPORTE.SPKG_GENERACION_SLA.P_GENERACION_RESUMEN_SLA(ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1),ADD_MONTHS(LAST_DAY(TRUNC(SYSDATE)),-1));
END;

/

