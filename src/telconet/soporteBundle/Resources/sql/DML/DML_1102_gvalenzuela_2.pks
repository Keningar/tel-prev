--PROCESO ANÓNIMO O BLOQUE ANÓNIMO PARA CONFIGURAR EL PROCESO Y USUARIO
--ENCARGADO DE SOLICITAR TOKEN PARA EL SISTEMA HAL/MPG
SET SERVEROUTPUT ON
DECLARE
    Ln_Aplicacion_NCam_Id NUMBER;
BEGIN
    --Crear Aplicación 
    INSERT INTO DB_TOKENSECURITY.APPLICATION VALUES (
        DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
        'APP.HAL9000',
        'ACTIVO',
         30
    );

    -- Obtener id de la aplicación 
    SELECT id_application INTO Ln_Aplicacion_NCam_Id
        FROM DB_TOKENSECURITY.APPLICATION
    WHERE name = 'APP.HAL9000';

  --Configurar clase SoporteWSController y relacionarlo con el APP.HAL9000
    INSERT INTO DB_TOKENSECURITY.WEB_SERVICE VALUES (
       DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'SoporteWSController',
      'procesarAction',
       1,
      'ACTIVO',
       Ln_Aplicacion_NCam_Id
    );

  --Configurar clase SoporteProcesosWSController y relacionarlo con el APP.HAL9000
    INSERT INTO DB_TOKENSECURITY.WEB_SERVICE VALUES (
       DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'SoporteProcesosWSController',
      'procesarAction',
       1,
      'ACTIVO',
       Ln_Aplicacion_NCam_Id
    );

    --Configurar Usuario/Clave HAL9000/HAL9000(sha256)
    INSERT INTO DB_TOKENSECURITY.USER_TOKEN VALUES (
       DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'HAL9000',
      '93BE1EFF5BCF47DDC756EFC99C2F02CB05800C704EF2B0AA5BE6EBD4C52B21F3',
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
