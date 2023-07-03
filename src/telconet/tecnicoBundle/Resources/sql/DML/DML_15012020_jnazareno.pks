DECLARE
  Ln_Aplicacion_Id NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'ec.telconet.mobile.telcos.operaciones',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_Aplicacion_Id
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'ec.telconet.mobile.telcos.operaciones';

--Configurar clase TecnicoWSController y relacionarlo con el ec.telconet.mobile.telcos.operaciones
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'TecnicoWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_Aplicacion_Id
    );

--Configurar clase SoporteWSController y relacionarlo con el ec.telconet.mobile.telcos.operaciones
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'SoporteWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_Aplicacion_Id
    );

  --Configurar Usuario/Clave MOVIL_OPERACIONES/MOVIL_OPERACIONES(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'MOVIL_OPERACIONES',
      '5c683920276f216ad1b2fca75d41bbab72aea23e6e7aaed9efc4097f1b88d1bc',
      'Activo',
      Ln_Aplicacion_Id
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;

/
