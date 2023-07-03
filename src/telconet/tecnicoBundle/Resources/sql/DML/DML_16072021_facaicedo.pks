--=======================================================================
-- Subnetear redes para L3MPLS para GUAYAQUIL y QUITO bajo la RED GPON
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    PV_MSG_ERROR VARCHAR2(200);
BEGIN
    --
    --GUAYAQUIL
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577817,
        pv_subred_ip => '10.214.200.0',
        pv_subred_mascara => 29,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577817,
        pv_subred_ip => '10.214.200.8',
        pv_subred_mascara => 29,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577817,
        pv_subred_ip => '10.214.200.16',
        pv_subred_mascara => 29,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    --QUITO
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577950,
        pv_subred_ip => '10.224.200.0',
        pv_subred_mascara => 27,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577922,
        pv_subred_ip => '10.224.200.32',
        pv_subred_mascara => 27,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => 577922,
        pv_subred_ip => '10.224.200.64',
        pv_subred_mascara => 27,
        pv_uso => 'DATOSGPON',
        pv_tipo => 'WAN',
        pv_msg_error => PV_MSG_ERROR
    );
    DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    --PREFIJOS RED PE
    --
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
            577817,
            'PREFIJO_RED',
            '214',
            'PREFIJO RED',
            'facaicedo',
            SYSDATE,
            '127.0.0.1',
            NULL,
            'Activo'
    );
    --
    --
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
            577950,
            'PREFIJO_RED',
            '224',
            'PREFIJO RED',
            'facaicedo',
            SYSDATE,
            '127.0.0.1',
            NULL,
            'Activo'
    );
    --
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
            577922,
            'PREFIJO_RED',
            '224',
            'PREFIJO RED',
            'facaicedo',
            SYSDATE,
            '127.0.0.1',
            NULL,
            'Activo'
    );
    --
    COMMIT;
    --
    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;

