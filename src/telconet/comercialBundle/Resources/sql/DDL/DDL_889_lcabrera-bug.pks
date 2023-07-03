/**
 * Script de regularización de la tabla INFO_PERSONA_EMPRESA_ROL para los Clientes y Pre-Clientes.- Se inserta el historial con el estado
 * de la INFO_PERSONA_EMPRESA_ROL
 * Se regularizan los pre-clientes que tienen dos ciclos activos.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 07-05-2018
 */
DECLARE
    --Cursor que devuelve todos las persona_empresa_rol que tienen 2 ciclos activos.
    CURSOR Lc_PreclienteCiclos IS
        SELECT PER.ID_PERSONA_ROL, PER.ESTADO
          FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER,
               (
                SELECT PERC.PERSONA_EMPRESA_ROL_ID,
                       COUNT(*)
                  FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                 WHERE PERC.CARACTERISTICA_ID = 1144
                   AND PERC.ESTADO = 'Activo'
                 GROUP BY PERC.PERSONA_EMPRESA_ROL_ID
                HAVING COUNT(*) > 1
               ) TABLA
         WHERE PER.ID_PERSONA_ROL = TABLA.PERSONA_EMPRESA_ROL_ID;

    CURSOR Lc_ObtieneEmpresaRol (Cv_EmpresaCod DB_COMERCIAL.INFO_EMPRESA_ROL.EMPRESA_COD%TYPE) IS
        SELECT ER.ID_EMPRESA_ROL, R.DESCRIPCION_ROL
          FROM DB_COMERCIAL.INFO_EMPRESA_ROL ER, DB_GENERAL.ADMI_ROL R
         WHERE ER.EMPRESA_COD = Cv_EmpresaCod
           AND ER.ESTADO IN ('Activo','Modificado')
           AND ER.ROL_ID = R.ID_ROL 
           AND R.DESCRIPCION_ROL IN ('Pre-cliente', 'Cliente');

    CURSOR Lc_PersonaEmpRol (Cn_EmpresaRolId DB_COMERCIAL.INFO_EMPRESA_ROL.ID_EMPRESA_ROL%TYPE) IS
        SELECT ESTADO, ID_PERSONA_ROL
          FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER
         WHERE PER.EMPRESA_ROL_ID = Cn_EmpresaRolId;

    Lv_Usuario        VARCHAR2(15) := 'regulaCiclo3_0';
    Lv_Usuario1       VARCHAR2(15) := 'regulaCiclo3_1';
    Lv_Estado         VARCHAR2(10) := 'Inactivo';
    Lv_Estado2        VARCHAR2(10);
    Ld_FePaseProd     DATE         := TO_DATE('15/04/2018','DD/MM/YYYY');
    Ln_Numero         NUMBER       := 0;
    Ln_Clientes       NUMBER       := 0;

BEGIN
    /**
     * Crear un historial de regularización para clientes y pre-clientes de MD con el estado de la Persona_empresa_rol
     */
    FOR Lr_ObtieneEmpresaRol IN Lc_ObtieneEmpresaRol('18')
    LOOP
        FOR Lr_Cancelados IN Lc_PersonaEmpRol(Lr_ObtieneEmpresaRol.ID_EMPRESA_ROL)
        LOOP
            Lv_Estado2 := Lr_Cancelados.ESTADO;
            IF Lr_ObtieneEmpresaRol.DESCRIPCION_ROL = 'Pre-cliente' AND Lr_Cancelados.ESTADO = 'Inactivo' THEN
                Lv_Estado2 := 'Convertido';
            END IF;
            INSERT
                INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO
                  (
                    ID_PERSONA_EMPRESA_ROL_HISTO,
                    USR_CREACION,
                    FE_CREACION,
                    IP_CREACION,
                    ESTADO,
                    PERSONA_EMPRESA_ROL_ID,
                    OBSERVACION,
                    MOTIVO_ID,
                    EMPRESA_ROL_ID,
                    OFICINA_ID,
                    DEPARTAMENTO_ID,
                    CUADRILLA_ID,
                    REPORTA_PERSONA_EMPRESA_ROL_ID,
                    ES_PREPAGO
                  )
                  VALUES
                  (
                    DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL_H.NEXTVAL,
                    Lv_Usuario1,
                    SYSDATE,
                    '127.0.0.1',
                    Lv_Estado2,
                    Lr_Cancelados.ID_PERSONA_ROL,
                    'Regularización de ' || Lr_ObtieneEmpresaRol.DESCRIPCION_ROL ||' con estado ' || Lr_Cancelados.ESTADO,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                  );
            Ln_Clientes := Ln_Clientes + 1;
        END LOOP;
    END LOOP;
    COMMIT;
    DBMS_OUTPUT.PUT_LINE('INFO_PERSONA_EMPRESA_ROL regularizado: ' || Ln_Clientes || ' registros afectados.');

    /**
     * Actualizar el estado de la característica de los preclientes con dos ciclos activos.
     */
    FOR Lr_PreClienteCiclos IN Lc_PreclienteCiclos
    LOOP
        UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
           SET ESTADO = Lv_Estado,
               USR_ULT_MOD = Lv_Usuario,
               FE_ULT_MOD  = SYSDATE
         WHERE ID_PERSONA_EMPRESA_ROL_CARACT = (SELECT MIN (ID_PERSONA_EMPRESA_ROL_CARACT)
                                                 FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
                                                WHERE PERSONA_EMPRESA_ROL_ID = Lr_PreClienteCiclos.ID_PERSONA_ROL
                                                  AND CARACTERISTICA_ID = 1144
                                                  AND TRUNC(FE_CREACION) = Ld_FePaseProd);

        INSERT
            INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO
              (
                ID_PERSONA_EMPRESA_ROL_HISTO,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION,
                ESTADO,
                PERSONA_EMPRESA_ROL_ID,
                OBSERVACION,
                MOTIVO_ID,
                EMPRESA_ROL_ID,
                OFICINA_ID,
                DEPARTAMENTO_ID,
                CUADRILLA_ID,
                REPORTA_PERSONA_EMPRESA_ROL_ID,
                ES_PREPAGO
              )
              VALUES
              (
                DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL_H.NEXTVAL,
                Lv_Usuario,
                SYSDATE,
                '127.0.0.1',
                Lr_PreClienteCiclos.ESTADO,
                Lr_PreClienteCiclos.ID_PERSONA_ROL,
                'Regularización de Preclientes con más de un ciclo activo',
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL
              );
        Ln_Numero := Lc_PreclienteCiclos%ROWCOUNT;
    END LOOP;
    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Se realizó el proceso de regularización correctamente: ' || Ln_Numero || ' registros afectados.');
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.PUT_LINE('ERROR al ejecutar el script: ' || SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK
                                                             || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
END;