/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML se añade menu para proceso de anulacion
 */

INSERT INTO DB_SEGURIDAD.SIST_ITEM_MENU (
ID_ITEM_MENU,
ITEM_MENU_ID,
NOMBRE_ITEM_MENU,
URL_IMAGEN,
POSICION,
HTML,
ESTADO,
CODIGO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD,
DESCRIPCION_ITEM_MENU,
TITLE_HTML,
DESCRIPCION_HTML
) VALUES (
    DB_SEGURIDAD.SEQ_SIST_ITEM_MENU.NEXTVAL,
    (SELECT ID_ITEM_MENU FROM DB_SEGURIDAD.SIST_ITEM_MENU WHERE NOMBRE_ITEM_MENU = 'Pagos' AND DESCRIPCION_ITEM_MENU = 'Menu Secundario'),
    'Anulacion Pagos',
    null,
    null,
    null,
    'Activo',
    null,
    'wquinto',
    SYSDATE,
    null,
    null,
    'Anulacion Pagos',
    'Anulacion Pagos',
    null
);


INSERT INTO DB_SEGURIDAD.SIST_MODULO (
ID_MODULO,
NOMBRE_MODULO,
ESTADO,
CODIGO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD
) VALUES (
    DB_SEGURIDAD.SEQ_SIST_MODULO.NEXTVAL,
    'infopagocab/anulacionPagos',
    'Activo',
    null,
    'wquinto',
    SYSDATE,
    null,
    null
);

INSERT INTO DB_SEGURIDAD.SIST_ACCION (
ID_ACCION,
NOMBRE_ACCION,
URL_IMAGEN,
ESTADO,
CODIGO,
USR_CREACION,
FE_CREACION
) VALUES (
DB_SEGURIDAD.SEQ_SIST_ACCION.NEXTVAL,
    'indexAnulacionPagos',
    null,
    'Activo',
    null,
    'wquinto',
    SYSDATE
);

INSERT INTO DB_SEGURIDAD.SEGU_RELACION_SISTEMA (
ID_RELACION_SISTEMA,
MODULO_ID,
ACCION_ID,
ITEM_MENU_ID,
RELACION_SISTEMA_ID,
USR_CREACION,
FE_CREACION,
IP_CREACION,
TAREA_INTERFACE_MODELO_TRA_ID
) VALUES (
    DB_SEGURIDAD.SEQ_SEGU_RELACION_SISTEMA.NEXTVAL,
    (SELECT ID_MODULO FROM DB_SEGURIDAD.SIST_MODULO WHERE NOMBRE_MODULO = 'infopagocab/anulacionPagos' AND ESTADO = 'Activo'),
    (SELECT ID_ACCION FROM DB_SEGURIDAD.SIST_ACCION WHERE NOMBRE_ACCION =  'indexAnulacionPagos'  
    AND ESTADO = 'Activo'),
    (SELECT ID_ITEM_MENU FROM DB_SEGURIDAD.SIST_ITEM_MENU WHERE NOMBRE_ITEM_MENU ='Anulacion Pagos' AND ESTADO = 'Activo'),
    null,
    'wquinto',
    SYSDATE,
    '127.0.0.1',
    null
);


INSERT INTO DB_SEGURIDAD.SIST_PERFIL (
ID_PERFIL,
NOMBRE_PERFIL,
ESTADO,
USR_CREACION,
FE_CREACION,
USR_ULT_MOD,
FE_ULT_MOD
) VALUES (
    DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL,
    'Anulacion Pagos',
    'Activo',
    'wquinto',
    SYSDATE,
    null,
    null
);


INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
PERFIL_ID,
RELACION_SISTEMA_ID,
USR_CREACION,
FE_CREACION,
IP_CREACION
) VALUES (
    (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Anulacion Pagos'),
    (SELECT ID_RELACION_SISTEMA 
    FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = (SELECT ID_MODULO 
                                                                FROM DB_SEGURIDAD.SIST_MODULO 
                                                                WHERE NOMBRE_MODULO = 'infopagocab/anulacionPagos'
                                                                AND ESTADO = 'Activo')
    ),
    'wquinto',
    SYSDATE,
    '127.0.0.1'
);

COMMIT;
/
 