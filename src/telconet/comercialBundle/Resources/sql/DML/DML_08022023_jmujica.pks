--=======================================================================
-- Ingreso de nuevas capacidades para servicios TN GPON-MPLS con OLT ZTE
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    --
    type                typeArray IS VARRAY(40) OF VARCHAR2(10);
    Ln_Index            NUMBER;
    
    CURSOR C_GetIdParametroDetalle IS
        SELECT ID_PARAMETRO_DET, VALOR1, VALOR3 FROM DB_GENERAL.ADMI_PARAMETRO_DET
        WHERE PARAMETRO_ID  = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                WHERE NOMBRE_PARAMETRO   = 'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON') 
        AND DESCRIPCION = 'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON';
    --
    TYPE Ltl_IdParamDet    IS TABLE OF C_GetIdParametroDetalle%ROWTYPE;
    Lr_IdParamDet          Ltl_IdParamDet;        
    
        
BEGIN
    --
    IF C_GetIdParametroDetalle%ISOPEN THEN
      CLOSE C_GetIdParametroDetalle;
    END IF;
    --
    OPEN C_GetIdParametroDetalle;
    --
    LOOP
        FETCH C_GetIdParametroDetalle BULK COLLECT INTO Lr_IdParamDet LIMIT 40;
        Ln_Index := 1;
        WHILE Ln_Index <= Lr_IdParamDet.count LOOP
            UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
            SET VALOR3 = (Lr_IdParamDet(Ln_Index).VALOR1 * 1000)
            WHERE ID_PARAMETRO_DET = Lr_IdParamDet(Ln_Index).ID_PARAMETRO_DET;
            
            Ln_Index := Ln_Index + 1;
        END LOOP;
        EXIT WHEN C_GetIdParametroDetalle%NOTFOUND;
    END LOOP;
    CLOSE C_GetIdParametroDetalle;
    COMMIT;
    DBMS_OUTPUT.put_line('OK: Se guardaron los cambios.');

    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;