SET SERVEROUTPUT ON
DECLARE
    --
    Ln_ContadorSsid     NUMBER       := 0;
    Ln_ContadorAdmin    NUMBER       := 0;
    Lb_Multiplatafroma  VARCHAR2(60) := 'MULTIPLATAFORMA';
    Lb_NombreVlanSsid   VARCHAR2(60) := 'VLAN SSID WIFI SAFECITY GPON';
    Lb_NombreVlanAdmin  VARCHAR2(60) := 'VLAN ADMIN WIFI SAFECITY GPON';
    Lb_VlanSsid         VARCHAR2(10) := '891';
    Lb_VlanAdmin        VARCHAR2(10) := '890';
    Lb_User             VARCHAR2(20) := 'migracion_tn';
    Lb_Ip               VARCHAR2(20) := '127.0.0.1';
    CURSOR C_getIdOltPorDetalleElemento(Cb_DetalleNombre VARCHAR2)
    IS
      SELECT ELE.ID_ELEMENTO
      FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO ELE, DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO MUL
      WHERE MUL.ELEMENTO_ID = ELE.ID_ELEMENTO
        AND MUL.DETALLE_NOMBRE = Lb_Multiplatafroma
        AND MUL.DETALLE_VALOR = 'SI'
        AND NOT EXISTS (
            SELECT 1
            FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DET
            WHERE ELE.ID_ELEMENTO  = DET.ELEMENTO_ID
            AND DET.DETALLE_NOMBRE = Cb_DetalleNombre
            AND DET.ESTADO         = 'Activo'
        );
    --
BEGIN
    --
    IF C_getIdOltPorDetalleElemento%ISOPEN THEN
        CLOSE C_getIdOltPorDetalleElemento;
    END IF;
    FOR I_getElemento IN C_getIdOltPorDetalleElemento(Lb_NombreVlanSsid)
    LOOP
        --Ingresamos el detalle para la vlan 'SAFE VIDEO ANALYTICS CAM'
        INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
                ID_DETALLE_ELEMENTO,
                ELEMENTO_ID,
                DETALLE_NOMBRE,
                DETALLE_VALOR,
                DETALLE_DESCRIPCION,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION,
                REF_DETALLE_ELEMENTO_ID,
                ESTADO
        )
        VALUES
        (
                DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
                I_getElemento.ID_ELEMENTO,
                Lb_NombreVlanSsid,
                Lb_VlanSsid,
                Lb_NombreVlanSsid,
                Lb_User,
                SYSDATE,
                Lb_Ip,
                NULL,
                'Activo'
        );
        Ln_ContadorSsid := Ln_ContadorSsid + 1;
    END LOOP;
    --
    IF C_getIdOltPorDetalleElemento%ISOPEN THEN
        CLOSE C_getIdOltPorDetalleElemento;
    END IF;
    FOR I_getElemento IN C_getIdOltPorDetalleElemento(Lb_NombreVlanAdmin)
    LOOP
        --Ingresamos el detalle para la vlan 'SAFE VIDEO ANALYTICS CAM'
        INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
                ID_DETALLE_ELEMENTO,
                ELEMENTO_ID,
                DETALLE_NOMBRE,
                DETALLE_VALOR,
                DETALLE_DESCRIPCION,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION,
                REF_DETALLE_ELEMENTO_ID,
                ESTADO
        )
        VALUES
        (
                DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
                I_getElemento.ID_ELEMENTO,
                Lb_NombreVlanAdmin,
                Lb_VlanAdmin,
                Lb_NombreVlanAdmin,
                Lb_User,
                SYSDATE,
                Lb_Ip,
                NULL,
                'Activo'
        );
        Ln_ContadorAdmin := Ln_ContadorAdmin + 1;
    END LOOP;
    --
    COMMIT;
    DBMS_OUTPUT.put_line('TOTAL VLAN SSID: ' || Ln_ContadorSsid);
    DBMS_OUTPUT.put_line('TOTAL VLAN ADMIN: ' || Ln_ContadorAdmin);
    DBMS_OUTPUT.put_line('OK: Se guardaron los cambios.');

    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;
