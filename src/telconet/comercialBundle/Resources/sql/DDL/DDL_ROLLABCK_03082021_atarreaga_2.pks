/**
 * Script que permite reversar la característica insertada ya sea COMERCIAL o BASICO a los planes de MD. 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 03-08-2021
 */

DECLARE

  --Costo query: 2 
  CURSOR C_IdCaracteristica(Cv_DescripcionCaract VARCHAR2)  is
    SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
    WHERE DESCRIPCION_CARACTERISTICA = Cv_DescripcionCaract
      AND ESTADO                     = 'Activo'; 

  --Costo query: 42
  CURSOR C_TipoCaractPlanes(Cv_Empresa VARCHAR2, Cv_DescripcionCaractPlan VARCHAR2)  is
    SELECT IP_CARACT.ID_PLAN_CARACTERISITCA
    FROM DB_COMERCIAL.INFO_PLAN_CAB IPC,
         DB_COMERCIAL.INFO_PLAN_CARACTERISTICA IP_CARACT,
         DB_COMERCIAL.ADMI_CARACTERISTICA AC,
      DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG   
    WHERE IPC.ID_PLAN                   = IP_CARACT.PLAN_ID
      AND AC.ID_CARACTERISTICA          = IP_CARACT.CARACTERISTICA_ID
      AND AC.DESCRIPCION_CARACTERISTICA = Cv_DescripcionCaractPlan  
      AND  IEG.COD_EMPRESA              = IPC.EMPRESA_COD
      AND IPC.EMPRESA_COD               = Cv_Empresa;
   
    Ln_IdCaracteristica     NUMBER;
    Ln_contCaractEliminados NUMBER := 0;                
   
BEGIN

  IF C_IdCaracteristica%ISOPEN THEN
    CLOSE C_IdCaracteristica;
  END IF;

  IF C_TipoCaractPlanes%ISOPEN THEN
    CLOSE C_TipoCaractPlanes;
  END IF;

  OPEN C_IdCaracteristica('TIPO_CATEGORIA_PLAN_ADULTO_MAYOR');
  FETCH C_IdCaracteristica INTO Ln_IdCaracteristica;
  CLOSE C_IdCaracteristica;

  IF Ln_IdCaracteristica IS NOT NULL THEN

    FOR Lr_TipoCaractPlanes IN C_TipoCaractPlanes('18', 'TIPO_CATEGORIA_PLAN_ADULTO_MAYOR') 
    LOOP
      
      DELETE DB_COMERCIAL.INFO_PLAN_CARACTERISTICA WHERE 
            ID_PLAN_CARACTERISITCA = Lr_TipoCaractPlanes.ID_PLAN_CARACTERISITCA 
        AND CARACTERISTICA_ID      = Ln_IdCaracteristica;

        Ln_contCaractEliminados:= Ln_contCaractEliminados+1;
        COMMIT;
    END LOOP;

  ELSE 

   dbms_output.put_line('No existe característica.');

  END IF;


  dbms_output.put_line('Proceso terminado.');
  dbms_output.put_line('Registros eliminados procesados : '||Ln_contCaractEliminados);

  exception WHEN others THEN

    IF C_TipoCaractPlanes%ISOPEN THEN
    CLOSE C_TipoCaractPlanes;
    END IF;

    IF C_IdCaracteristica%ISOPEN THEN
    CLOSE C_IdCaracteristica;
    END IF;

    dbms_output.put_line('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    dbms_output.put_line(sqlerrm);
    ROLLBACK;
END;
