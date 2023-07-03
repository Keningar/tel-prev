--ELIMINAR CARACTERISTICA AL PRODUCTO
DELETE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE CARACTERISTICA_ID IN (select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA IN 
('senalOptica','HEC','FEC','BIP','apONT-nombreDipositivo','apONT-IPv4','apONT-macAddress','apONT-RSSI','apONT-banda','neighbors-SSID','neighbors-canal','neighbors-RSSI','neighbors-banda',
'hosts-nombreDipositivo','hosts-IPv4','hosts-macAddress','hosts-RSSI','hosts-banda','speedTest-upload','speedTest-download','speedTest-ping'));

--ELIMINAR LA CARACTERISTICA
DELETE DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA IN 
('senalOptica','HEC','FEC','BIP','apONT-nombreDipositivo','apONT-IPv4','apONT-macAddress','apONT-RSSI','apONT-banda','neighbors-SSID','neighbors-canal','neighbors-RSSI','neighbors-banda',
'hosts-nombreDipositivo','hosts-IPv4','hosts-macAddress','hosts-RSSI','hosts-banda','speedTest-upload','speedTest-download','speedTest-ping')


--ELIMINAR PARAMETROS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'RANGOS CALIDAD DE INSTALACION'
    );

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB CABECERA
WHERE
    NOMBRE_PARAMETRO = 'RANGOS CALIDAD DE INSTALACION';

DROP PACKAGE DB_COMERCIAL.SPKG_CALIDAD_INSTALACION;

commit;
