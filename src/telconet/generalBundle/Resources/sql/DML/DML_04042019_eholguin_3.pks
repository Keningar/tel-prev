  /**
  *
  * Bloque que realiza los inserts correspondientes a los sectores asociados a cada municipio (parroquia) para Telconet Guatemala.
  * Cantidad Registros 328
  * @version 1.0 22-03-2019
  * @author Edgar Holguín <eholguin@telconet.ec>
  */
DECLARE
  CURSOR C_GetIdParroquia(Cv_NombrePais DB_GENERAL.ADMI_PAIS.NOMBRE_PAIS%TYPE,
                          Cv_Estado     DB_GENERAL.ADMI_PARAMETRO_CAB.ESTADO%TYPE)
  IS
    SELECT ID_PARROQUIA 
    FROM DB_GENERAL.ADMI_PARROQUIA 
    WHERE CANTON_ID IN (SELECT ID_CANTON 
                        FROM   DB_GENERAL.ADMI_CANTON 
                        WHERE  PROVINCIA_ID IN (SELECT ID_PROVINCIA 
                                                FROM   DB_GENERAL.ADMI_PROVINCIA 
                                                WHERE  REGION_ID IN (SELECT ID_REGION 
                                                                     FROM   DB_GENERAL.ADMI_REGION 
                                                                     WHERE  PAIS_ID IN (SELECT ID_PAIS 
                                                                                        FROM DB_GENERAL.ADMI_PAIS 
                                                                                        WHERE NOMBRE_PAIS = Cv_NombrePais 
                                                                                        AND ESTADO = Cv_Estado))));
  
  Ln_IdParroquia   DB_GENERAL.ADMI_PARROQUIA.ID_PARROQUIA%TYPE;
  Le_Exception     EXCEPTION;
BEGIN
  --
  --

  IF C_GetIdParroquia%ISOPEN THEN CLOSE C_GetIdParroquia; END IF;

  OPEN C_GetIdParroquia('GUATEMALA', 'ACTIVO');
  LOOP
    FETCH C_GetIdParroquia INTO Ln_IdParroquia;
	INSERT 
	INTO DB_GENERAL.ADMI_SECTOR (ID_SECTOR,
		                     NOMBRE_SECTOR,
		                     PARROQUIA_ID,
		                     ESTADO,
		                     USR_CREACION,
		                     FE_CREACION,
		                     USR_ULT_MOD,
		                     FE_ULT_MOD,
		                     EMPRESA_COD) 
	VALUES (DB_GENERAL.SEQ_ADMI_SECTOR.NEXTVAL,'Norte',Ln_IdParroquia,'Activo','eholguin',SYSDATE,null,SYSDATE,'27');

    EXIT
  WHEN C_GetIdParroquia%NOTFOUND;
  END LOOP;
  CLOSE C_GetIdParroquia;
  
COMMIT;
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
END;
/
