/**
 *
 * Se crean parametros para el acceso al ws de toolbox de los productos Paramount y Noggin
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 28-06-2021
 */


--PARAMOUNT 

INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'CONFIGURACION_WS_CLEAR_CACHE_PARAMOUNT',
    'Valores para la configuración del WS a consumir en PARAMOUNT',
    'COMERCIAL',
    'PARAMOUNT',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_PARAMOUNT'),
    'CONFIGURACION DE AMBIENTE DE PRODUCCION PARAMOUNT',
    'https://idp-cache.tbxapis.com/v1/cache/clear/ec/{subscriber_id}',
    'LPtfY59xLCWQTf25Ha5u44x8QaRc141g',
    'DELETE',
    'cloudpass.status',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'cloudpass.data.status=true',
    '18',
    'cloudpass.error.errorCode|cloudpass.error.message',
    NULL,
    NULL
);

--NOGGIN

INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'CONFIGURACION_WS_CLEAR_CACHE_NOGGIN',
    'Valores para la configuración del WS a consumir en NOGGIN',
    'COMERCIAL',
    'NOGGIN',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_NOGGIN'),
    'CONFIGURACION DE AMBIENTE DE PRODUCCION NOGGIN',
    'https://idp-cache.tbxapis.com/v1/cache/clear/ec/{subscriber_id}',
    'LPtfY59xLCWQTf25Ha5u44x8QaRc141g',
    'DELETE',
    'cloudpass.status',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'cloudpass.data.status=true',
    '18',
    'cloudpass.error.errorCode|cloudpass.error.message',
    NULL,
    NULL
);


COMMIT;

/
