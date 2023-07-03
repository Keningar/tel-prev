--=======================================================================
-- Subnetear subredes para servicios de Cámaras VPN Safecity GPON
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    PV_MSG_ERROR VARCHAR2(200);
BEGIN
    --SUBNETING
    DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_CLASE_B(
        pn_idElemento => NULL,
        pv_subred_ip => '10.247.0.0',
        pv_subred_mascara => 16,
        pv_uso => 'SAFECITYCAMVPN',
        pv_tipo => 'LAN',
        pv_msg_error => PV_MSG_ERROR
    );
    SYS.DBMS_OUTPUT.PUT_LINE(PV_MSG_ERROR);
    --
    COMMIT;
    --
EXCEPTION
WHEN OTHERS THEN
    SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    ROLLBACK;
END;
/
