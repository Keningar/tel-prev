insert into DB_COMERCIAL.ADMI_CARACTERISTICA (id_caracteristica, descripcion_caracteristica, tipo_ingreso, estado, fe_creacion, usr_creacion, tipo)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 'CATEGORIA EMPRESA EXTERNA', 'N', 'Activo', sysdate, 'sfernandez',  'COMERCIAL');

INSERT INTO DB_TOKENSECURITY.APPLICATION
  (ID_APPLICATION, NAME, STATUS, EXPIRED_TIME
  ) VALUES
  (DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL, 'TELCOS', 'ACTIVO', '5'
  );

INSERT INTO DB_TOKENSECURITY.USER_TOKEN
  (ID_USER_TOKEN, USERNAME, PASSWORD, ESTADO, APPLICATION_ID
  ) VALUES
  (DB_TOKENSECURITY.SEQ_USER_TOKEN.NEXTVAL, 'TELCOS', '38083C7EE9121E17401883566A148AA5C2E2D55DC53BC4A94A026517DBFF3C6B', 
  'Activo', (SELECT ID_APPLICATION FROM DB_TOKENSECURITY.APPLICATION WHERE NAME='TELCOS')
  );

   Insert into DB_TOKENSECURITY.WEB_SERVICE (ID_WEB_SERVICE,SERVICE,METHOD,GENERATOR,STATUS,ID_APPLICATION) 
   values (DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,'PersonalExternoController','createAction','1',
           'ACTIVO',(SELECT ID_APPLICATION FROM DB_TOKENSECURITY.APPLICATION WHERE NAME = 'TELCOS'));

commit;

DECLARE
  
  CURSOR C_CARACTERISTICA(Cv_DescripcionCaracteristica Varchar2) IS 
    SELECT ID_CARACTERISTICA 
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA = Cv_DescripcionCaracteristica;

  CURSOR C_INFOPERSONAEMPRESAROLCARAC(Cn_CaracteristicaId Varchar2)IS
   SELECT * 
     FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC 
    WHERE CARACTERISTICA_ID = Cn_CaracteristicaId;


  Ln_IdCaracteristicaEmpExt    DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE:=0;
  Ln_IdCaracteristicaCatEmpExt DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE:=0;
  Ln_IdPersonaEmpresaRolCaract DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC.ID_PERSONA_EMPRESA_ROL_CARACT%TYPE:=0;
  Lv_EmpresaExterna            DB_COMERCIAL.ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA%TYPE:='EMPRESA EXTERNA';
  Lv_CategoriaEmpExt           DB_COMERCIAL.ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA%TYPE:='CATEGORIA EMPRESA EXTERNA';
  Ln_Contador                  NUMBER:=0; 
  Ln_Commit                    NUMBER:=0; 

BEGIN 
 
  IF C_CARACTERISTICA%ISOPEN THEN CLOSE C_CARACTERISTICA; END IF;
  OPEN C_CARACTERISTICA(Lv_EmpresaExterna);
  FETCH C_CARACTERISTICA INTO Ln_IdCaracteristicaEmpExt; 
  CLOSE C_CARACTERISTICA;
  
  IF C_CARACTERISTICA%ISOPEN THEN CLOSE C_CARACTERISTICA; END IF;
  OPEN C_CARACTERISTICA(Lv_CategoriaEmpExt);
  FETCH C_CARACTERISTICA INTO Ln_IdCaracteristicaCatEmpExt; 
  CLOSE C_CARACTERISTICA;
  
  FOR I IN C_INFOPERSONAEMPRESAROLCARAC(Ln_IdCaracteristicaEmpExt) LOOP
    
    Ln_IdPersonaEmpresaRolCaract:= DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.NEXTVAL;
    INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC (ID_PERSONA_EMPRESA_ROL_CARACT, 
                                                             PERSONA_EMPRESA_ROL_ID,
                                                             CARACTERISTICA_ID, 
                                                             VALOR, 
                                                             FE_CREACION, 
                                                             USR_CREACION, 
                                                             IP_CREACION, 
                                                             ESTADO)
     VALUES (Ln_IdPersonaEmpresaRolCaract, 
             I.VALOR,
             Ln_IdCaracteristicaCatEmpExt, 
             'DISTRIBUIDOR', 
             SYSDATE,
             'SFERNANDEZ',
             '192.168.56.1', 
             'Activo');
      DBMS_OUTPUT.PUT_LINE('Se creó: '|| Ln_IdPersonaEmpresaRolCaract);     
        
      UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
         SET PERSONA_EMPRESA_ROL_CARAC_ID = Ln_IdPersonaEmpresaRolCaract
         WHERE ID_PERSONA_EMPRESA_ROL_CARACT = I.ID_PERSONA_EMPRESA_ROL_CARACT;
         
      DBMS_OUTPUT.PUT_LINE('Se actualizó: '|| I.ID_PERSONA_EMPRESA_ROL_CARACT);
    Ln_Contador:= Ln_Contador+1;
    Ln_Commit  := Ln_Commit+1;
    IF Ln_Commit = 30 THEN
      COMMIT;
      DBMS_OUTPUT.PUT_LINE('COMMIT: '|| Ln_Commit);
      Ln_Commit:=0;
    END IF;
  END LOOP;
      COMMIT;
      DBMS_OUTPUT.PUT_LINE('COMMIT FINAL: '|| Ln_Commit);
      DBMS_OUTPUT.PUT_LINE('Total Registros: '|| Ln_Contador);
  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('ERROR NO CONTORLADO. '||SQLERRM);
      ROLLBACK;
END;
      

