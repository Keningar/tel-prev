/**
 * @author Javier Hidalgo <jihidalgo@telconet.ec>
 * @version 1.0
 * @since 05/11/2022    
 * Bloque anonimo que permite regularizar pagos en linea con anticipos  
 * y facturas abiertas (activaecuador).
 */
ALTER session set NLS_NUMERIC_CHARACTERS ='.,'; 
DECLARE 
    
    TYPE Lr_InfoPagosError
    IS
      RECORD
      (
        ID_PAGO_LINEA                        DB_FINANCIERO.INFO_PAGO_LINEA.ID_PAGO_LINEA%TYPE, 
        VALOR_PAGO_LINEA                     DB_FINANCIERO.INFO_PAGO_LINEA.VALOR_PAGO_LINEA%TYPE,
        NUMERO_REFERENCIA                    DB_FINANCIERO.INFO_PAGO_LINEA.NUMERO_REFERENCIA%TYPE,  
        FE_CREACION                          DB_FINANCIERO.INFO_PAGO_LINEA.FE_CREACION%TYPE,
        IDENTIFICACION_CLIENTE               DB_FINANCIERO.INFO_PERSONA.IDENTIFICACION_CLIENTE%TYPE
        );

    TYPE T_InfoPagosErrores IS TABLE OF Lr_InfoPagosError INDEX BY PLS_INTEGER;
    
    CURSOR C_GetPagoCab(Cv_IdPagoLinea VARCHAR2) IS
        SELECT A.* FROM DB_FINANCIERO.INFO_PAGO_CAB A WHERE A.PAGO_LINEA_ID = Cv_IdPagoLinea;

    CURSOR C_GetPagosError IS
        select ipl.id_pago_linea,
        ipl.valor_pago_linea,
        ipl.numero_referencia,
        ipl.fe_creacion,
        ip.identificacion_cliente
        from DB_FINANCIERO.info_pago_linea ipl, DB_COMERCIAL.info_persona ip
        where ipl.persona_id = ip.id_persona
        and ipl.estado_pago_linea = 'Conciliado'
        and trunc(ipl.fe_creacion) >= '01-NOV-2022'
        and ipl.canal_pago_linea_id = 109
        -- con anticipos
        and exists (
            select 1 from DB_FINANCIERO.info_pago_cab ipc
            where ipc.pago_linea_id = ipl.id_pago_linea
            and ipc.estado_pago = 'Pendiente'
            and ipc.tipo_documento_id in (3,4) -- anticipo
        )
        -- con facturas aun abiertas
        and exists (
            select 1 from 
            DB_COMERCIAL.info_persona_empresa_rol iper,
            DB_COMERCIAL.info_punto ipt,
            DB_FINANCIERO.info_documento_financiero_cab idfc
            where iper.persona_id = ip.id_persona
            and ipt.persona_empresa_rol_id = iper.id_persona_rol
            and idfc.punto_id = ipt.id_punto
            and iper.estado in ('Activo','In-Corte')
            and ipt.estado in ('Activo','In-Corte')
            --and idfc.estado_impresion_fact not in ('Cerrado','Rechazado','Eliminado')
            and idfc.estado_impresion_fact in ('Activo')
            and idfc.tipo_documento_id in (1,5)
        )
        --AND ROWNUM <=100
        ;
    Lv_IdPagoCab      VARCHAR2(1000);
    Li_Limit            CONSTANT PLS_INTEGER DEFAULT 4000;
    Li_Cont             PLS_INTEGER;
    Le_InfoPagosErrores   T_InfoPagosErrores;
    Lcl_ReqConciliacion     CLOB;
    Pv_Status          VARCHAR2(1000);
    Pv_Mensaje         VARCHAR2(1000);
    Pcl_Response            CLOB;
    Lr_InfoPagoCab          DB_FINANCIERO.INFO_PAGO_CAB%ROWTYPE;
    
BEGIN 
    IF C_GetPagosError%ISOPEN THEN
        CLOSE C_GetPagosError;
    END IF;
    OPEN C_GetPagosError;
    FETCH C_GetPagosError BULK COLLECT INTO Le_InfoPagosErrores LIMIT Li_Limit;
    
    Li_Cont := Le_InfoPagosErrores.FIRST;
    
    WHILE (Li_Cont IS NOT NULL)  
    LOOP
        UPDATE DB_FINANCIERO.INFO_PAGO_LINEA IPL
        SET IPL.ESTADO_PAGO_LINEA = 'Pendiente'
        WHERE IPL.ID_PAGO_LINEA   = Le_InfoPagosErrores(Li_Cont).ID_PAGO_LINEA;
        
        UPDATE DB_FINANCIERO.INFO_PAGO_CAB ICAB
        SET ICAB.ESTADO_PAGO = 'Anulado'
        WHERE ICAB.PAGO_LINEA_ID = Le_InfoPagosErrores(Li_Cont).ID_PAGO_LINEA;
        
        IF C_GetPagoCab%ISOPEN THEN
            CLOSE C_GetPagoCab;
        END IF;
        
        FOR Lr_InfoPagoCab IN C_GetPagoCab(Le_InfoPagosErrores(Li_Cont).ID_PAGO_LINEA)
        LOOP
            Lv_IdPagoCab := Lr_InfoPagoCab.ID_PAGO;
        END LOOP;
        
        IF C_GetPagoCab%ISOPEN THEN
            CLOSE C_GetPagoCab;
        END IF;
        
        UPDATE DB_FINANCIERO.INFO_PAGO_DET IDET
        SET IDET.ESTADO = 'Anulado'
        WHERE IDET.PAGO_ID = Lv_IdPagoCab;
        
        COMMIT;
        
        Lcl_ReqConciliacion := '{' ||
                                    '"codigoExternoEmpresa":"18",' ||
                                    '"fechaTransaccion":"' || TO_CHAR(Le_InfoPagosErrores(Li_Cont).FE_CREACION, 'YYYY-MM-DD HH:MI:SS') || '",' ||
                                    '"secuencialRecaudador":"' || Le_InfoPagosErrores(Li_Cont).NUMERO_REFERENCIA || '",' ||
                                    '"secuencialPagoInterno":"' || Le_InfoPagosErrores(Li_Cont).ID_PAGO_LINEA || '",' ||
                                    '"identificacionCliente":"' || Le_InfoPagosErrores(Li_Cont).IDENTIFICACION_CLIENTE  || '",' ||
                                    '"valorPago":"' || Le_InfoPagosErrores(Li_Cont).VALOR_PAGO_LINEA || '",' ||
                                    '"tipoTransaccion":"400",' ||
                                    '"canal":"activaecuador",' ||
                                    '"usuario":"activaecuador",' ||
                                    '"clave":"@C0lL3\u0026TCH4NN3L"'
                                || '}';
        
        BEGIN
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'BUSPAGOS',
                                            'REGULAR_P_CONCILIA',
                                            SUBSTR('REQUEST:'||Lcl_ReqConciliacion,1,4000),
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'telcos'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
            DB_FINANCIERO.FNCK_PAGOS_LINEA.P_CONCILIAR_PAGO_LINEA(Lcl_ReqConciliacion, Pv_Status, Pv_Mensaje, Pcl_Response);
            
        EXCEPTION
            WHEN OTHERS THEN
            BEGIN
                DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'BUSPAGOS',
                                            'REGULAR_P_CONCILIA_ERROR',
                                            SUBSTR('Error:'|| SQLCODE || ' -ERROR- ' || SQLERRM,1,4000),
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'telcos'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
            END;
            
        END;
        
        IF C_GetPagoCab%ISOPEN THEN
            CLOSE C_GetPagoCab;
        END IF;

        FOR Lr_InfoPagoCab IN C_GetPagoCab(Le_InfoPagosErrores(Li_Cont).ID_PAGO_LINEA)
        LOOP
            INSERT INTO DB_FINANCIERO.INFO_PAGO_HISTORIAL VALUES (
                DB_FINANCIERO.SEQ_INFO_PAGO_HISTORIAL.NEXTVAL,
                Lr_InfoPagoCab.ID_PAGO,
                null,
                SYSDATE,
                'telcos_pal',
                Lr_InfoPagoCab.ESTADO_PAGO,
                'Regularización de pagos con anticipos y facturas afiertas - tarea #70897094'
            ); 
        END LOOP;
        
        IF C_GetPagoCab%ISOPEN THEN
            CLOSE C_GetPagoCab;
        END IF;
        
        COMMIT;

        Li_Cont:= Le_InfoPagosErrores.NEXT(Li_Cont);
    END LOOP;
    CLOSE C_GetPagosError;
    
EXCEPTION
    WHEN OTHERS THEN
    BEGIN
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'BUSPAGOS',
                                            'REGULAR_PAGO_GENERAL_ERROR',
                                            SUBSTR('Error:'|| SQLCODE || ' -ERROR- ' || SQLERRM,1,4000),
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'telcos'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
    END;    
END;