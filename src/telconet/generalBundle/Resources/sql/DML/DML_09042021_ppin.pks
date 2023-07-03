/*
 * Registro de nuevas características para flujo ZeroTouch:

 * “IPV6_TEMP” ➜ Para guardar IPV6 temporal enviada por WS de NW.
 * “VLAN_TEMP” ➜ Para guardar VLAN temporal enviada por WS de NW.
 * "CPE_TEMP" ➜ Para guardar CPE registrado de forma temporal.
 * "TRANSCEIVER_TEMP" ➜ Para guardar TRANSCEIVER registrado de forma temporal.
 * “RCA_TEST” ➜ Para guardar true o false de exíto en pruebas RCA.
 * “NW_TEST” ➜ Para guardar true o false de exíto en pruebas NW.
 * "FLUJO_ZEROTOUCH” ➜ Para controlar que L2 no pueda asignar recursos de red antes de tiempo.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 29-03-2021 - Versión Inicial.
 */

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'CARACTERISTICAS_ZERO_TOUCH',
    'PARAMETRO QUE CONTIENE LAS CARACTERISTICAS Y SU VALOR INICIAL DE ZEROTOUCH.',
    'TECNICO',
    null,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CARACTERISTICAS_ZERO_TOUCH'
            AND estado = 'Activo'
    ),
    'PARAMETROS_CARACTERISTICAS_VALORES_ZERO_TOUCH',
    '[{"nombre":"IPV6_TEMP","valorInicial":""},{"nombre":"VLAN_TEMP","valorInicial":""},{"nombre":"CPE_TEMP","valorInicial":""},{"nombre":"TRANSCEIVER_TEMP","valorInicial":""},{"nombre":"NW_TEST","valorInicial":""},{"nombre":"FLUJO_ZEROTOUCH","valorInicial":"S"}]',
    NULL,
    NULL,
    NULL,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1',
    NULL,
    10,
    null,
    null,
    null
);

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PRODUCTOS_ZERO_TOUCH',
    'PARAMETRO QUE CONTIENE LOS PRODUCTOS HABILITADOS PARA ZEROTOUCH.',
    'TECNICO',
    null,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PRODUCTOS_ZERO_TOUCH'
            AND estado = 'Activo'
    ),
    'PRODUCTOS_ZERO_TOUCH',
    '[236, 237, 242, 1360]',
    NULL,
    NULL,
    NULL,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1',
    NULL,
    10,
    null,
    null,
    null
);

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'UM_ZERO_TOUCH',
    'PARAMETRO QUE CONTIENE LAS UM PERMITIDAS PARA EL FLUJO ZEROTOUCH.',
    'TECNICO',
    null,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'UM_ZERO_TOUCH'
            AND estado = 'Activo'
    ),
    'UM_ZERO_TOUCH',
    '[1]',
    '',
    NULL,
    NULL,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1',
    NULL,
    10,
    null,
    null,
    null
);

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'CIUDADES_ZERO_TOUCH',
    'PARAMETRO QUE CONTIENE LAS CIUDADES PERMITIDAS PARA EL FLUJO ZEROTOUCH.',
    'TECNICO',
    null,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CIUDADES_ZERO_TOUCH'
            AND estado = 'Activo'
    ),
    'CIUDADES_ZERO_TOUCH',
    '[75,178]',
    '["GUAYAQUIL", "QUITO"]',
    '["tn-ztp-gye", "tn-ztp-uio"]',
    NULL,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1',
    NULL,
    10,
    null,
    null,
    null
);

UPDATE db_general.ADMI_PARAMETRO_DET SET VALOR1 = 'Los valores no cumplen con los umbrales. <br>No se pudo validar enlace."
                                       ."<br>Por favor, luego del tercer reintento comunicarse con IPCCL1 para la revisión.' WHERE id_parametro_det = 12760 AND DESCRIPCION = 'VALIDACION_ENLACE_ERROR_UMBRALES' ;

UPDATE db_general.ADMI_PARAMETRO_DET SET VALOR1 = 99.90 WHERE PARAMETRO_ID = 1346 AND DESCRIPCION = 'MIN_PORCENTAJE_PAQUETES_RECIBIDO' ;

--INSERT PARA NUEVO PARAMETRO MÓVIL
INSERT INTO db_general.admi_parametro_det 
(
  ID_PARAMETRO_DET,
  PARAMETRO_ID,
  DESCRIPCION,
  VALOR1,
  VALOR2,
  VALOR3,
  VALOR4,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD,
  VALOR5,
  EMPRESA_COD,
  VALOR6,
  VALOR7,
  OBSERVACION
)
VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'Productos TN que estarán en el nuevo flujo de instalacion.',
    'PRODUCTOS_INSTALACION_TN',
    '{"FLUJO_ACTIVACION_TN":[{"id_producto": "1360", "nombre" : "DIRECTLINK MPLS"},{"id_producto": "236", "nombre" : "Internet MPLS"},{"id_producto": "237", "nombre" : "L3MPLS"},{"id_producto": "242", "nombre" : "Internet Dedicado"}]}',
    NULL,
    NULL,
    'Activo',
    'wvera',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--------------------------------------------------------------------------------

--FIN DE PARÁMETROS PARA EL MOVIL


COMMIT;
/