/**
 * Script que permite insertar la característica ya sea COMERCIAL o BASICO a los planes de MD. 
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

  --Costo query: 21 
  CURSOR C_PlanesComerciales(Cv_Empresa VARCHAR2, Cv_PlanBasico VARCHAR2)  is
    SELECT IPC.ID_PLAN, IPC.NOMBRE_PLAN, IPC.EMPRESA_COD
    FROM DB_COMERCIAL.INFO_PLAN_CAB IPC,
      DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG   
    WHERE IEG.COD_EMPRESA     = IPC.EMPRESA_COD
      AND IPC.EMPRESA_COD     = Cv_Empresa
      AND IPC.NOMBRE_PLAN     NOT IN (Cv_PlanBasico);
   
   --Costo query: 3 
   CURSOR C_PlanBasico(Cv_Empresa VARCHAR2, Cv_PlanBasico VARCHAR2) is  
    SELECT IPC.ID_PLAN, IPC.NOMBRE_PLAN, IPC.EMPRESA_COD
    FROM DB_COMERCIAL.INFO_PLAN_CAB IPC,
      DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG   
    WHERE IEG.COD_EMPRESA     = IPC.EMPRESA_COD
      AND IPC.EMPRESA_COD     = Cv_Empresa
      AND IPC.NOMBRE_PLAN     IN (Cv_PlanBasico);

    Ln_IdCaracteristica     NUMBER;
    Ln_contPlanComercial    NUMBER := 0;
    Ln_contPlanBasico       NUMBER := 0;
    Ln_TotalRegistros       NUMBER := 0;
   
BEGIN

  IF C_IdCaracteristica%ISOPEN THEN
    CLOSE C_IdCaracteristica;
  END IF;

  IF C_PlanesComerciales%ISOPEN THEN
    CLOSE C_PlanesComerciales;
  END IF;

  IF C_PlanBasico%ISOPEN THEN
    CLOSE C_PlanBasico;
  END IF;

  OPEN C_IdCaracteristica('TIPO_CATEGORIA_PLAN_ADULTO_MAYOR');
  FETCH C_IdCaracteristica INTO Ln_IdCaracteristica;
  CLOSE C_IdCaracteristica;

  IF Ln_IdCaracteristica IS NOT NULL THEN

    FOR Lr_PlanesComerciales IN C_PlanesComerciales('18', 'Adulto Mayor 20Mbps NA.NA') 
    LOOP
      INSERT INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
        (
          ID_PLAN_CARACTERISITCA,
          PLAN_ID,
          CARACTERISTICA_ID,
          VALOR,
          ESTADO,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION
        )
        VALUES
        (
          DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
          Lr_PlanesComerciales.ID_PLAN,
          Ln_IdCaracteristica,
          'COMERCIAL',
          'Activo',
          SYSDATE,
          'telcos_adultomayor',
          '127.0.0.1'
        );

        Ln_contPlanComercial:= Ln_contPlanComercial+1;
        COMMIT;
    END LOOP;

    FOR Lr_PlanBasico IN C_PlanBasico('18', 'Adulto Mayor 20Mbps NA.NA') 
    LOOP
      INSERT INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
        (
          ID_PLAN_CARACTERISITCA,
          PLAN_ID,
          CARACTERISTICA_ID,
          VALOR,
          ESTADO,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION
        )
        VALUES
        (
          DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
          Lr_PlanBasico.ID_PLAN,
          Ln_IdCaracteristica,
          'BASICO',
          'Activo',
          SYSDATE,
          'telcos_adultomayor',
          '127.0.0.1'
        );

        Ln_contPlanBasico:= Ln_contPlanBasico+1;
        COMMIT;
    END LOOP;

  ELSE 

   dbms_output.put_line('No existe característica.');

  END IF;    

  Ln_TotalRegistros := Ln_contPlanComercial+Ln_contPlanBasico;
  dbms_output.put_line('Proceso terminado.');
  dbms_output.put_line('Planes comerciales procesados : '||Ln_contPlanComercial);
  dbms_output.put_line('Plan basico procesados        : '||Ln_contPlanBasico);
  dbms_output.put_line('Total procesados              : '||Ln_TotalRegistros);

  exception WHEN others THEN

    IF C_IdCaracteristica%ISOPEN THEN
      CLOSE C_IdCaracteristica;
    END IF;

    IF C_PlanesComerciales%ISOPEN THEN
      CLOSE C_PlanesComerciales;
    END IF;

    IF C_PlanBasico%ISOPEN THEN
      CLOSE C_PlanBasico;
    END IF;

    dbms_output.put_line('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    dbms_output.put_line(sqlerrm);
    ROLLBACK;

END;
