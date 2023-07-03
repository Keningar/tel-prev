--=======================================================================
-- Subnetear subredes para servicios SAFECITY para GUAYAQUIL y QUITO
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    type                typeArray IS VARRAY(12) OF VARCHAR2(60);
    Lr_ElementosPe      typeArray;
    Lr_SubredPe         typeArray;
    Ln_IdElemento       NUMBER;
    Ln_Total            NUMBER;
    PV_STATUS           VARCHAR2(200);
    PV_MSG_ERROR        VARCHAR2(200);
BEGIN
    --DELETE SUBREDES
    DELETE DB_INFRAESTRUCTURA.INFO_IP WHERE SUBRED_ID IN (SELECT ID_SUBRED FROM DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON');
    DELETE DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON';
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
    --subnetear subred por los pe
    Lr_ElementosPe  := typeArray('pe1asrgyek.telconet.net','pe1asrgyem.telconet.net','pe1asrgyec.telconet.net','pe1asrgyei.telconet.net',
                                'pe1asrgyes.telconet.net','pe1asrgyea.telconet.net','pe1asruiog.telconet.net','pe1asruiom.telconet.net',
                                'pe1asruios.telconet.net','pe1asruioa.telconet.net','pe1asruiob.telconet.net','pe1asruiod.telconet.net');
    Lr_SubredPe     := typeArray('10.245.0.0','10.245.32.0','10.245.64.0','10.245.96.0',
                                '10.245.128.0','10.245.160.0','10.246.0.0','10.246.32.0',
                                '10.246.64.0','10.246.96.0','10.246.128.0','10.246.160.0');
    --
    Ln_Total := Lr_ElementosPe.count;
    FOR i in 1 .. Ln_Total LOOP
        --obtengo el id del elemento
        SELECT ID_ELEMENTO INTO Ln_IdElemento FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO WHERE ESTADO = 'Activo' AND NOMBRE_ELEMENTO = Lr_ElementosPe(i);
        --subnetear subred hijas
        DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.SUBNETEAR_SUBRED_HIJAS(
            PN_IDELEMENTOANT => NULL,
            PN_IDELEMENTONUEVO => Ln_IdElemento,
            PV_SUBRED_IP => Lr_SubredPe(i),
            PV_SUBRED_MASCARA => '19',
            PV_USOANTERIOR => 'SAFECITYGPON',
            PV_USONUEVO => 'SAFECITYGPON',
            PV_TIPO => 'LAN',
            PV_STATUS => PV_STATUS,
            PV_MENSAJE => PV_MSG_ERROR
        );
    END LOOP;
    --
    COMMIT;
    SYS.DBMS_OUTPUT.PUT_LINE('Se guardaron los cambios');
    --
EXCEPTION
WHEN OTHERS THEN
    --DELETE SUBREDES
    DELETE DB_INFRAESTRUCTURA.INFO_IP WHERE SUBRED_ID IN (SELECT ID_SUBRED FROM DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON');
    DELETE DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON';
    --
    COMMIT;
    --
    SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
END;
