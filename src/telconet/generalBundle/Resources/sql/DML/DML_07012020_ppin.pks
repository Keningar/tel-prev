SET DEFINE OFF;

--PARAMETROS NECESARIOS PARA ASIGNACION DE TAREAS A LOS RESPECTIVOS COORDINADORES DE RADIO DE ACUERDO A SU REGION ASIGNADA.

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'COORDINADORES_RADIO',
    'PARAMETRO QUE CONTIENE UN ARREGLO DE OBJETOS CON LOS DATOS PRINCIPALES DE LOS COORDINADORES DE RADIO',
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
            nombre_parametro = 'COORDINADORES_RADIO'
            AND estado = 'Activo'
    ),
    'PARAMETROS_COORDINADORES_RADIO',
    '{"R1":{"nombres":"KATHERINE TATIANA","apellidos":"ARAUJO WILSON","nombreCompleto":"KATHERINE TATIANA ARAUJO WILSON","login":"karaujo","region":"R1","departamento":"RADIO","empresaCod":"10","idPersona":237553,"idPersonaEmpresaRol":616659},"R2":{"nombres":"JOSE DARIO","apellidos":"VALLE CORONEL","nombreCompleto":"JOSE DARIO VALLE CORONEL","login":"dvalle","region":"R2","departamento":"RADIO","empresaCod":"10","idPersona":237719,"idPersonaEmpresaRol":1778150}}',
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

--PARAMETROS PARA ENVIAR NOTIFICACIONES A RADIO

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PRODUCTOS_NOTIFICACION_RADIO',
    'PARAMETRO QUE CONTIENE UN ARREGLO OBJETOS CON PRODUCTOS PARA ENVIO DE NOTIFICACIONES A RADIO',
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
            nombre_parametro = 'PRODUCTOS_NOTIFICACION_RADIO'
            AND estado = 'Activo'
    ),
    'PARAMETROS_PRODUCTOS_RADIO_NOTIFICACION',
    '{"arrayProductos":[261,276],"productosObject":[{"ID_PRODUCTO":261,"EMPRESA_COD":"10","CODIGO_PRODUCTO":"SISCTN","DESCRIPCION_PRODUCTO":"INTERNET WIFI","ESTADO":"Activo","NOMBRE_TECNICO":"INTERNET WIFI","SUBGRUPO":"INTERNET WIFI","LINEA_NEGOCIO":"CONNECTIVITY"},{"ID_PRODUCTO":276,"EMPRESA_COD":"10","CODIGO_PRODUCTO":"WF01","DESCRIPCION_PRODUCTO":"WIFI Alquiler Equipos","ESTADO":"Activo","NOMBRE_TECNICO":"WIFI","SUBGRUPO":"WIFI ALQUILER EQUIPOS","LINEA_NEGOCIO":"CONNECTIVITY"}]}',
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

COMMIT;
/