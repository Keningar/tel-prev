/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Se crea nueva caracteristica InAudit para corte a Cliente Posible Abusador.
 * Si se ha creado previamente actualiza su estado a Activo
 * @author Javier Hidalgo Fernández <jihidalgo@telconet.ec>
 * @version 1.0 25-11-2021 - Versión Inicial.
 */
DECLARE 
    CARACT VARCHAR2(10);
    MENSAJERR VARCHAR2(1500);
BEGIN
    SELECT ID_CARACTERISTICA INTO CARACT FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'InAudit';
    IF CARACT IS NOT NULL THEN
        UPDATE DB_COMERCIAL.admi_caracteristica SET estado = 'Activo' where DESCRIPCION_CARACTERISTICA = 'InAudit';
        COMMIT;
    END IF;
EXCEPTION
        WHEN NO_DATA_FOUND THEN
            -- AGREGA CARACTERISTICA INAUDIT
            INSERT INTO DB_COMERCIAL.admi_caracteristica (ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, TIPO)
            VALUES (DB_COMERCIAL.Seq_Admi_Caracteristica.NEXTVAL, 'InAudit', 'T', 'Activo', sysdate, 'jihidalgo', null, null, 'TECNICA');
            COMMIT;
        WHEN OTHERS THEN
            MENSAJERR := SQLCODE || ' -ERROR- ' || SQLERRM;
            DBMS_OUTPUT.PUT_LINE(MENSAJERR);
END;

/