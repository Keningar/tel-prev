--=======================================================================
-- Subnetear subredes para servicios SAFECITY para GUAYAQUIL y QUITO
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    PV_MSG_ERROR VARCHAR2(200);
BEGIN
    --GUAYAQUIL
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => NULL,
        pv_subred_ip => '10.245.0.0',
        pv_subred_mascara => 16,
        pv_uso => 'SAFECITYGPON',
        pv_tipo => 'LAN',
        pv_msg_error => PV_MSG_ERROR
    );
    SYS.DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --QUITO
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => NULL,
        pv_subred_ip => '10.246.0.0',
        pv_subred_mascara => 16,
        pv_uso => 'SAFECITYGPON',
        pv_tipo => 'LAN',
        pv_msg_error => PV_MSG_ERROR
    );
    SYS.DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    UPDATE DB_INFRAESTRUCTURA.INFO_SUBRED SET ESTADO = 'Ocupado' WHERE USO = 'SAFECITYGPON';
    COMMIT;
    --
EXCEPTION
WHEN OTHERS THEN
    SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    ROLLBACK;
END;
