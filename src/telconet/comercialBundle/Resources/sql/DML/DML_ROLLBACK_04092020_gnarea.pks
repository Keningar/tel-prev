--PERFLES GRUPALES QUE DEBEN TENER ACCESO A RELACION_ID 2581
begin
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (7962, 2581,'jlgarciar', TO_DATE('21-11-2017 12:15:37', 'DD-MM-YYYY HH:MI:SS'), '127.0.0.1');
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (9131, 2581,'mmoreta', TO_DATE('13-06-2019 11:25:36', 'DD-MM-YYYY HH:MI:SS'), '172.24.15.76');
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (9133, 2581,'mmoreta', TO_DATE('06-06-2019 01:10:15', 'DD-MM-YYYY HH:MI:SS'), '172.24.15.71');
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (9137, 2581,'mmoreta', TO_DATE('14-07-2019 09:15:25', 'DD-MM-YYYY HH:MI:SS'), '172.24.15.71');
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (9327, 2581,'SYS', TO_DATE('24-08-2019 11:46:58', 'DD-MM-YYYY HH:MI:SS'), '127.0.0.1');
  INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION VALUES (9328, 2581,'SYS', TO_DATE('24-08-2019 11:47:17', 'DD-MM-YYYY HH:MI:SS'), '127.0.0.1');
commit;
end;