
--INSERTS PARA AGREGAR DIRECTORIO A LA TABLA ADMI_GESTION_DIRECTORIOS

SET SERVEROUTPUT ON;

DECLARE 
    Ln_NextVal      NUMBER;
    Lv_Aplicacion   VARCHAR2(15)   := 'TelcosWeb';
    
    CURSOR Lc_GetUltDir
    IS
    SELECT gd.codigo_app,
    (SELECT max(agd.codigo_path)  
        FROM DB_GENERAL.admi_gestion_directorios agd
        WHERE agd.codigo_app = gd.codigo_app) as max_codigo_path
    FROM DB_GENERAL.admi_gestion_directorios gd
    WHERE gd.aplicacion = Lv_Aplicacion
    group by gd.codigo_app;
    
    Lr_DatosDir Lc_GetUltDir%ROWTYPE;
    
BEGIN 
     
  OPEN Lc_GetUltDir;
  FETCH Lc_GetUltDir INTO Lr_DatosDir;
  CLOSE Lc_GetUltDir;
  
  dbms_output.put_line(Lr_DatosDir.codigo_app);
  dbms_output.put_line(Lr_DatosDir.max_codigo_path);
  
 Ln_NextVal := Lr_DatosDir.max_codigo_path +1;
 
  dbms_output.put_line(Ln_NextVal);
  
  INSERT INTO
	DB_GENERAL.ADMI_GESTION_DIRECTORIOS
	(ID_GESTION_DIRECTORIO,
	CODIGO_APP,
	CODIGO_PATH,
	APLICACION,
	PAIS,
	EMPRESA,
	MODULO,
	SUBMODULO,
	ESTADO,
	FE_CREACION,
	FE_ULT_MOD,
	USR_CREACION,
	USR_ULT_MOD
	)VALUES(
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    Lr_DatosDir.codigo_app,
    Ln_NextVal,
    Lv_Aplicacion,
    '593',
    'MD',
    'Administracion',
    'Notificaciones',
    'Activo',
    SYSDATE,
    NULL,
    'adorellana',
    NULL);
    
    COMMIT;
END;



 
   
