SET SERVEROUTPUT ON
DECLARE
    --
    Ln_IdCaractVrf              NUMBER        := 666;
    Lv_FormatoVrf               VARCHAR2(30)  := 'vrf_video%';
    Lv_Caracteristica           VARCHAR2(100) := 'VRF_VIDEO_SAFECITY';
    --
    Ln_IdCaracteristica         NUMBER;
    Lv_Estado                   VARCHAR2(30) := 'Activo';
    Lv_User                     VARCHAR2(20) := 'telcos';
    Lv_Ip                       VARCHAR2(20) := '127.0.0.1';
    --
    CURSOR C_IdCaracteristica IS
        SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = Lv_Caracteristica;
    --
    TYPE R_Registros IS RECORD (
        ID_PERSONA_EMPRESA_ROL_CARACT   NUMBER,
        PERSONA_EMPRESA_ROL_ID          NUMBER,
        PERSONA_EMPRESA_ROL_CARAC_ID    NUMBER
    );
    Lr_Registros                R_Registros;
    --
    CURSOR C_ClienteVrf IS
        SELECT ID_PERSONA_EMPRESA_ROL_CARACT, PERSONA_EMPRESA_ROL_ID, PERSONA_EMPRESA_ROL_CARAC_ID
        FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
        WHERE CARACTERISTICA_ID = Ln_IdCaractVrf AND VALOR LIKE Lv_FormatoVrf AND ESTADO = Lv_Estado;
BEGIN
    --
    IF C_IdCaracteristica%ISOPEN THEN
        CLOSE C_IdCaracteristica;
    END IF;
    --
    OPEN C_IdCaracteristica;
    FETCH C_IdCaracteristica INTO Ln_IdCaracteristica;
    CLOSE C_IdCaracteristica;
    --
    IF C_ClienteVrf%ISOPEN THEN
        CLOSE C_ClienteVrf;
    END IF;
    --
    IF Ln_IdCaracteristica IS NOT NULL THEN
        FOR Lr_Registros IN C_ClienteVrf
        LOOP
            --
            INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
            VALUES (
                DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.NEXTVAL, 
                Lr_Registros.PERSONA_EMPRESA_ROL_ID,
                Ln_IdCaracteristica,
                Lr_Registros.ID_PERSONA_EMPRESA_ROL_CARACT,
                SYSDATE,
                NULL,
                Lv_User,
                NULL,
                Lv_Ip,
                Lv_Estado,
                Lr_Registros.PERSONA_EMPRESA_ROL_CARAC_ID
            );
            --
        END LOOP;
        --
        IF C_ClienteVrf%ISOPEN THEN
            CLOSE C_ClienteVrf;
        END IF;
        --
        COMMIT;
        --
        dbms_output.put_line('Se guardan los cambios.');
    ELSE
        dbms_output.put_line('No existe la característica "VRF_VIDEO_SAFECITY".');
    END IF;
    --
EXCEPTION
    WHEN OTHERS THEN
        --se reservan los cambios
        dbms_output.put_line('Se reversan los cambios.');
        ROLLBACK;
        dbms_output.put_line(SUBSTR(SQLCODE || ' -ERROR- ' || SQLERRM,0,4000));
END;
/
