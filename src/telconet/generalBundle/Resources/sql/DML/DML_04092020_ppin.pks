/*
 * ➜ Ingresa una cabecera CARACTERISTICAS_SERVICIOS_SIMULTANEOS.
 *
 * ➜ Ingresa el detalle sobre el nuevo parámetro.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 29-05-2020 - Versión Inicial.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
        'ESTABLECER_ESTADO_SOLICITUD_PRODUCTO',
        'PARAMETRO PARA ESTABLECER DETERMINADO ESTADO A DETERMINADO PRODUCTO',
        'TECNICO',
        'ESTADO_SOLICITUD_PRODUCTO',
        'Activo',
        'ppin',
        SYSDATE,
        '127.0.0.1',
        null,
        null,
        null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval, (SELECT ID_PARAMETRO
                                                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                    WHERE NOMBRE_PARAMETRO = 'ESTABLECER_ESTADO_SOLICITUD_PRODUCTO'),
        'ESTABLECER_ESTADO_SOLICITUD_PRODUCTO',
        '[{"PRODUCTO_ID":261,"DESCRIPCION_PRODUCTO":"INTERNET WIFI","OPCIONES":{"128_Fact_Fin":{"ESTADO_BUSQUEDA":"Factible","ESTADO_NUEVO":"Finalizada","SOLICITUD_ID":128,"DESCRIPCION_SOLICITUD":"SOLICITUD NODO WIFI"}}}]',
        null, null, null, 'Activo', 'ppin',
        SYSDATE, '127.0.0.1', null, null, null, null, '10', null, null,
        null);


COMMIT;

/