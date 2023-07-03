DECLARE
  Ln_Aplicacion_NCam_Id NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'APP.CLOUDFORM',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_Aplicacion_NCam_Id
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'APP.CLOUDFORM';

  --Configurar clase TecnicoWSController y relacionarlo con el APP.CLOUDFORM
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'CloudFormsWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_Aplicacion_NCam_Id
    );

  --Configurar Usuario/Clave CLOUDFORM/CLOUDFORM(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'CLOUDFORM',
      'c5f6992b6f5c3c175f2b1d96b08818cb2c1cff5914b79d9db8e68e96ebf94f54',
      'Activo',
      Ln_Aplicacion_NCam_Id
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;

/