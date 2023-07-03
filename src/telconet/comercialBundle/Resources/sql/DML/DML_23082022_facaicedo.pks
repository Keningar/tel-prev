--=======================================================================
-- Subnetear subredes para servicios de Cámaras VPN Safecity GPON
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    --
    TYPE Ltl_Array      IS VARRAY(56) OF VARCHAR2(100);--NUMERO DE REGISTROS VARRAY
    Ln_IdProducto       NUMBER          := 1450;--INTERNET VPNoGPON
    Ln_IdCracateristica NUMBER          := 1110;--SUBRED_PRIVADA
    Lv_Estado           VARCHAR2(60)    := 'Activo';
    Lv_EstadoOcupado    VARCHAR2(60)    := 'Ocupado';
    Lv_EstadoEliminado  VARCHAR2(60)    := 'Eliminado';
    Lv_EstadoReservada  VARCHAR2(60)    := 'Reservada';
    Lv_Backup           VARCHAR2(60)    := '%BACKUP%';
    Lv_UsoSubred        VARCHAR2(60)    := 'SAFECITYCAMVPN';
    Lv_Mascara          VARCHAR2(60)    := '255.255.255.248';
    Lv_TipoIp           VARCHAR2(60)    := 'LAN';
    Lv_VersionIp        VARCHAR2(60)    := 'IPV4';
    Lv_User             VARCHAR2(60)    := 'facaicedo';
    Lv_Ip               VARCHAR2(60)    := '127.0.0.1';
    --
    Lv_Login            VARCHAR2(100);
    Lv_Subred           VARCHAR2(100);
    --
    Lv_SubredIp         VARCHAR2(100);
    Lv_SubredMascara    VARCHAR2(100);
    Ln_IdSubred         NUMBER;
    Lv_IpInicial        VARCHAR2(100);
    Lv_IpFinal          VARCHAR2(100);
    Ln_OctetoIpInicial  NUMBER;
    Ln_OctetoIpFinal    NUMBER;
    Lv_1octeto          VARCHAR2(10);
    Lv_2octeto          VARCHAR2(10);
    Lv_3octeto          VARCHAR2(10);
    Lv_IpGenerada       VARCHAR2(100);
    Lv_ExistIp          VARCHAR2(10);
    Lv_ExistCaract      VARCHAR2(10);
    Lv_ExistElemento    VARCHAR2(10);
    Ln_IdPersonaEmpRol  NUMBER;
    Ln_IdPunto          NUMBER;
    Ln_IdServicio       NUMBER;
    Ln_IdElemento       NUMBER;
    Lv_Status           VARCHAR2(4000);
    Lv_Mensaje          VARCHAR2(4000);
    --
    CURSOR C_GetIdSubred(Cv_Subred VARCHAR2) IS
        SELECT ID_SUBRED, IP_INICIAL, IP_FINAL FROM DB_INFRAESTRUCTURA.INFO_SUBRED
        WHERE SUBRED = Cv_Subred
          AND USO = Lv_UsoSubred
          AND MASCARA = Lv_Mascara
          AND ESTADO != Lv_EstadoEliminado;
    --
    CURSOR C_VerificarIp(Cn_IdSubred NUMBER, Cv_Ip VARCHAR2) IS
        SELECT 'SI' FROM DB_INFRAESTRUCTURA.INFO_IP
        WHERE SUBRED_ID = Cn_IdSubred
          AND IP = Cv_Ip
          AND ESTADO != Lv_EstadoEliminado;
    --
    CURSOR C_VerificarElemento(Cn_IdSubred NUMBER) IS
        SELECT 'SI'
        FROM DB_INFRAESTRUCTURA.INFO_SUBRED 
        WHERE ID_SUBRED = Cn_IdSubred
          AND ELEMENTO_ID IS NOT NULL;
    --
    CURSOR C_GetPunto(Cv_Login VARCHAR2) IS
        SELECT ID_PUNTO, PERSONA_EMPRESA_ROL_ID
        FROM DB_COMERCIAL.INFO_PUNTO WHERE LOGIN = Cv_Login
          AND ESTADO = Lv_Estado;
    --
    CURSOR C_VerificarCaract(Cn_IdSubred NUMBER) IS
        SELECT 'SI'
        FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC 
        WHERE CARACTERISTICA_ID = Ln_IdCracateristica
          AND VALOR = Cn_IdSubred
          AND ESTADO = Lv_Estado;
    --
    CURSOR C_GetIdServicio(Cn_IdPunto NUMBER) IS
        SELECT ID_SERVICIO FROM DB_COMERCIAL.INFO_SERVICIO
        WHERE PUNTO_ID = Cn_IdPunto
          AND PRODUCTO_ID = Ln_IdProducto
          AND ESTADO = Lv_Estado
          AND UPPER(DESCRIPCION_PRESENTA_FACTURA) NOT LIKE Lv_Backup
          AND ROWNUM <= 1;
    --
    CURSOR C_GetElemento(Cn_IdServicio NUMBER) IS
        SELECT ELEMENTO_ID FROM DB_COMERCIAL.INFO_SERVICIO_TECNICO
        WHERE SERVICIO_ID = Cn_IdServicio;
    --
    La_Subred       Ltl_Array;
    La_Login        Ltl_Array;
    Ln_Total 	    NUMBER;
    --
    PV_MSG_ERROR VARCHAR2(200);
BEGIN
    --
    La_Login := Ltl_Array(NULL,NULL,'cscgvidanali-puertolisa','cscgvidanalit-cl44yestpp','cscgvidanalit-mz32s8pros','cscgvidanalit-cdlalbv3er',
                            'cscgvidanalit-saucevi318','cscgvidanalit-callemz314','cscgvidanalit-cazadsy2do','cscgvidanalit-mz314sauce','cscgvidanalit-mz318sauce',
                            'cscgvidanalit-call5tapro','cscgvidanalit-mz33mapasg','cscgvidanalit-pros60mz32','cscgvidanalit-prosp29oct','cscgvidanalit-prospsec74',
                            'cscgvidanalit-mapa95mz47','cscgvidanalit-prosmz1302','cscgvidanalit-bspopbloq4','cscgvidanalit-18ycjn18no','cscgvidanalit-74mz19sl26',
                            'cscgvidanalit-mz1302pros','cscgvidanalit-bloq4mz690','cscgvidanalit-3erclj15cn','cscgvidanalit-cjn15ay5to','cscgvidanalit-vergel3eta',
                            'cscgvidanalit-saucvi14vo','cscgvidanalit-tamarmz114','cscgvidanalit-luzamerica','cscgvidanalit-mapes1ercj','cscgvidanalit-mapsecto95',
                            'cscgvidanalit-mapdecto95','cscgvidanalit-coopestrbe','cscgvidanalit-mapsecto76','cscgvidanalit-mamz34sl19','cscgvidanalit-mapa95mz17',
                            'cscgvidanalit-mapmz47sl2','Cscgvidanalit-vgerc17psj','Cscgvidanalit-valgeracad','Cscgvidanalit-sfran1y2ma','Cscgvidanalit-socvivienc',
                            'cscgvidanalit-cordcondor','cscgvidanalit-cjn15yav36','cscgvidanalit-cisn23ylar','cscgvidanalit-cisn2varad','cscgvidanalit-cisne3ery6',
                            'Cscgvidanalit-colimpavpa','cscgvidanalit-mapas24oct','cscgvidanalit-mapaluzame','cscgvidanalit-call5tapro','cscgvidanalit-mz33mapasg',
                            'cscgvidanalit-3erclj15cn','cscgvidanalit-cjn15ay5to','cscgvidanalit-pasolav57s','cscgvidanalit-victymupp2','cscgvidanalit-9naych1009');
    La_Subred := Ltl_Array('10.247.0.0/29','10.247.0.8/29','10.247.0.16/29','10.247.0.24/29','10.247.0.32/29','10.247.0.40/29','10.247.0.48/29','10.247.0.56/29',
                            '10.247.0.64/29','10.247.0.72/29','10.247.0.80/29','10.247.0.88/29','10.247.0.96/29','10.247.0.104/29','10.247.0.112/29','10.247.0.120/29',
                            '10.247.0.128/29','10.247.0.136/29','10.247.0.144/29','10.247.0.152/29','10.247.0.160/29','10.247.0.168/29','10.247.0.176/29','10.247.0.184/29',
                            '10.247.0.192/29','10.247.0.200/29','10.247.0.208/29','10.247.0.216/29','10.247.0.224/29','10.247.0.232/29','10.247.0.240/29','10.247.0.248/29',
                            '10.247.1.0/29','10.247.1.8/29','10.247.1.16/29','10.247.1.24/29','10.247.1.32/29','10.247.1.40/29','10.247.1.48/29','10.247.1.56/29',
                            '10.247.1.64/29','10.247.1.72/29','10.247.1.80/29','10.247.1.88/29','10.247.1.96/29','10.247.1.104/29','10.247.1.112/29','10.247.1.120/29',
                            '10.247.1.128/29','10.247.0.88/29','10.247.0.96/29','10.247.0.184/29','10.247.0.192/29','10.247.1.168/29','10.247.1.176/29','10.247.2.0/29');
    --
    IF C_GetIdSubred%ISOPEN THEN
        CLOSE C_GetIdSubred;
    END IF;
    --
    IF C_GetPunto%ISOPEN THEN
        CLOSE C_GetPunto;
    END IF;
    --
    IF C_GetIdServicio%ISOPEN THEN
        CLOSE C_GetIdServicio;
    END IF;
    --
    IF C_GetElemento%ISOPEN THEN
        CLOSE C_GetElemento;
    END IF;
    --
    Ln_Total := La_Subred.count;
    FOR Ln_Index in 1 .. Ln_Total LOOP
        --
        Lv_Subred := La_Subred(Ln_Index);
        Lv_Login  := La_Login(Ln_Index);
        --
        Ln_IdSubred := NULL;
        OPEN C_GetIdSubred(Lv_Subred);
        FETCH C_GetIdSubred INTO Ln_IdSubred, Lv_IpInicial, Lv_IpFinal;
        CLOSE C_GetIdSubred;
        --
        IF Ln_IdSubred IS NOT NULL THEN
            --
            DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - OK SUBRED');
            --
            UPDATE DB_INFRAESTRUCTURA.INFO_SUBRED SET ESTADO = Lv_EstadoOcupado WHERE ID_SUBRED = Ln_IdSubred;
            --
            Lv_SubredIp      := SUBSTR(Lv_Subred, 1, INSTR(Lv_Subred,'/')-1);
            Lv_SubredMascara := SUBSTR(Lv_Subred, INSTR(Lv_Subred,'/')+1);
            --
            Lv_1octeto := REGEXP_SUBSTR(Lv_SubredIp, '[^.]+', 1, 1);
            Lv_2octeto := REGEXP_SUBSTR(Lv_SubredIp, '[^.]+', 1, 2);
            Lv_3octeto := REGEXP_SUBSTR(Lv_SubredIp, '[^.]+', 1, 3);
            Ln_OctetoIpInicial := TO_NUMBER(REGEXP_SUBSTR(Lv_IpInicial, '[^.]+', 1, 4));
            Ln_OctetoIpFinal   := TO_NUMBER(REGEXP_SUBSTR(Lv_IpFinal, '[^.]+', 1, 4));
            --
            FOR Ln_4octeto IN Ln_OctetoIpInicial..Ln_OctetoIpFinal
            LOOP
                Lv_IpGenerada := Lv_1octeto || '.' || Lv_2octeto || '.' || Lv_3octeto || '.' || Ln_4octeto;
                --
                Lv_ExistIp := NULL;
                OPEN C_VerificarIp(Ln_IdSubred,Lv_IpGenerada);
                FETCH C_VerificarIp INTO Lv_ExistIp;
                CLOSE C_VerificarIp;
                IF Lv_ExistIp IS NULL THEN
                    INSERT INTO DB_INFRAESTRUCTURA.INFO_IP (ID_IP,IP,USR_CREACION,FE_CREACION,IP_CREACION,ESTADO,SUBRED_ID,MASCARA,TIPO_IP,VERSION_IP)
                    VALUES
                    (DB_INFRAESTRUCTURA.SEQ_INFO_IP.NEXTVAL,Lv_IpGenerada,Lv_User,SYSDATE,Lv_Ip,Lv_EstadoReservada,Ln_IdSubred,Lv_Mascara,Lv_TipoIp,Lv_VersionIp);
                    DBMS_OUTPUT.PUT_LINE('IP GENERADA: ' || Lv_IpGenerada);
                END IF;
            END LOOP;
            --
            IF Lv_Login IS NOT NULL THEN
                Ln_IdPunto := NULL;
                Ln_IdPersonaEmpRol := NULL;
                --
                OPEN C_GetPunto(Lv_Login);
                FETCH C_GetPunto INTO Ln_IdPunto, Ln_IdPersonaEmpRol;
                CLOSE C_GetPunto;
                --
                IF Ln_IdPunto IS NOT NULL AND Ln_IdPersonaEmpRol IS NOT NULL THEN
                    --
                    Lv_ExistCaract := NULL;
                    OPEN C_VerificarCaract(Ln_IdSubred);
                    FETCH C_VerificarCaract INTO Lv_ExistCaract;
                    CLOSE C_VerificarCaract;
                    --
                    IF Lv_ExistCaract IS NULL THEN
                        --
                        INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC 
                        (ID_PERSONA_EMPRESA_ROL_CARACT,PERSONA_EMPRESA_ROL_ID,CARACTERISTICA_ID,VALOR,FE_CREACION,USR_CREACION,IP_CREACION,ESTADO) 
                        VALUES
                        (DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.NEXTVAL,Ln_IdPersonaEmpRol,Ln_IdCracateristica,
                         Ln_IdSubred,SYSDATE,Lv_User,Lv_Ip,Lv_Estado);
                        --
                    END IF;
                    --
                    Ln_IdServicio := NULL;
                    OPEN C_GetIdServicio(Ln_IdPunto);
                    FETCH C_GetIdServicio INTO Ln_IdServicio;
                    CLOSE C_GetIdServicio;
                    --
                    Lv_ExistElemento := NULL;
                    OPEN C_VerificarElemento(Ln_IdSubred);
                    FETCH C_VerificarElemento INTO Lv_ExistElemento;
                    CLOSE C_VerificarElemento;
                    --
                    IF Lv_ExistElemento IS NULL THEN
                        IF Ln_IdServicio IS NOT NULL THEN
                            --
                            Ln_IdElemento := NULL;
                            OPEN C_GetElemento(Ln_IdServicio);
                            FETCH C_GetElemento INTO Ln_IdElemento;
                            CLOSE C_GetElemento;
                            --
                            IF Ln_IdElemento IS NOT NULL THEN
                                --
                                Lv_Status := NULL;
                                DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_SUBRED_HIJAS(NULL,Ln_IdElemento,Lv_SubredIp,Lv_SubredMascara,Lv_UsoSubred,
                                                                                              Lv_UsoSubred,NULL,Lv_Status,Lv_Mensaje);
                                --
                                IF Lv_Status = 'OK' THEN
                                    DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - OK SUBNETEAR');
                                ELSE
                                    --
                                    DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - ERROR_SUBNETEAR: No se subnetearon las subredes al id elemento ' || Ln_IdElemento || ' login ' || Lv_Login);
                                END IF;
                                --
                            ELSE
                                --
                                DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - ERROR_SUBNETEAR: No se encontro el elemento del id servicio ' || Ln_IdServicio || ' login ' || Lv_Login);
                            END IF;
                            --
                        ELSE
                            --
                            DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - ERROR_SUBNETEAR: No se encontro el servicio del punto ' || Lv_Login);
                        END IF;
                    END IF;
                    --
                ELSE
                    --
                    DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - ERROR_SUBNETEAR: No se encontro el id del punto del login ' || Lv_Login);
                    --
                END IF;
            END IF;
            --
        ELSE
            --
            DBMS_OUTPUT.PUT_LINE(Lv_Subred || ' - ERROR: No se encontro el id de la subred');
            --
        END IF;
        --
        DBMS_OUTPUT.PUT_LINE('');
    END LOOP;
    --se guardan los cambios
    DBMS_OUTPUT.PUT_LINE('Se guadaron los cambios.');
    COMMIT;
    --
EXCEPTION
WHEN OTHERS THEN
    SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    DBMS_OUTPUT.PUT_LINE('Se reversan los cambios.');
    --se reversan los cambios
    ROLLBACK;
END;
/
