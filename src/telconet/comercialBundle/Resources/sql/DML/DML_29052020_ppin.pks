/*
 * ➜ Ingresa una nueva característica para a cualquier producto la posibilidad
 *    de ser instalado en simultaneo con un tradicional.
 *
 * ➜ Ingresa la relación entre el producto y la nueva característica INSTALACION_SIMULTANEA.
 *
 * ➜ Ingresa la relación entre el producto y la caracteristica MAC.
 *
 * ➜ Ingresa la relación entre el producto y la caracteristica CPE SERIAL NUMBER.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 29-05-2020 - Versión Inicial.
 */

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
VALUES  (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval, 
        'INSTALACION_SIMULTANEA', 'N', 'Activo',
        SYSDATE, 'ppin', null, null, 'TECNICA', null);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 1117,
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION_SIMULTANEA'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'SI');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 1117,
        6,
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 1117,
        1298,
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

---------------------------------------------------------------------------

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
VALUES  (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval, 'FIRMWARE', 'N', 'Activo',
        SYSDATE, 'ppin', null, null, 'TECNICA', null);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'SAFE CAM' 
        AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'FIRMWARE'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'SAFE CAM' 
        AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION_SIMULTANEA'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'SI');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'SAFE CAM' 
        AND ESTADO = 'Activo'),
        6,
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'SAFE CAM' 
        AND ESTADO = 'Activo'),
        1298,
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'SAFE CAM' 
        AND ESTADO = 'Activo'),
        11,
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

-- TELCOHOME MG1
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'TIPO ELEMENTO',
    'T',
    'Activo',
    SYSDATE,
    'ppin',
    SYSDATE,
    'ppin',
    'TECNICA'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'MARCA ELEMENTO',
    'T',
    'Activo',
    SYSDATE,
    'ppin',
    SYSDATE,
    'ppin',
    'TECNICA'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE 
        DESCRIPCION_PRODUCTO = 'TELCOHOME MG1' 
        AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'INSTALACION_SIMULTANEA'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
        (SELECT id_producto
         FROM DB_COMERCIAL.ADMI_producto
         WHERE DESCRIPCION_PRODUCTO='TELCOHOME MG1'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'MAC'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
        (SELECT id_producto
         FROM DB_COMERCIAL.ADMI_producto
         WHERE DESCRIPCION_PRODUCTO='TELCOHOME MG1'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'CPE SERIAL NUMBER'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
        (SELECT id_producto
         FROM DB_COMERCIAL.ADMI_producto
         WHERE DESCRIPCION_PRODUCTO='TELCOHOME MG1'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'TIPO ELEMENTO'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');
        
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval,
        (SELECT id_producto
         FROM DB_COMERCIAL.ADMI_producto
         WHERE DESCRIPCION_PRODUCTO='TELCOHOME MG1'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'MARCA ELEMENTO'),
        SYSDATE, null, 'ppin', null,
        'Activo', 'NO');

COMMIT;

/