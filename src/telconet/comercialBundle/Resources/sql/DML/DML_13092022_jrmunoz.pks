/*
 * @author Joel Muñoz <jrmunoz@telconet.ec>
 * @version 1.0
 * @since 13-09-2022
 */

 INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,
                                             DESCRIPCION_CARACTERISTICA,
                                             TIPO_INGRESO,
                                             ESTADO,
                                             FE_CREACION,
                                             USR_CREACION,
                                             FE_ULT_MOD,
                                             USR_ULT_MOD,
                                             TIPO,
                                             DETALLE_CARACTERISTICA)

VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'IP/FQDN NG FIREWALL',
        'C',
        'Activo',
         SYSDATE,
        'jrmunoz',
         SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda un valor que puede ser una ip o un DNS para productos NG FIREWALL'
        );


INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,
                                             DESCRIPCION_CARACTERISTICA,
                                             TIPO_INGRESO,
                                             ESTADO,
                                             FE_CREACION,
                                             USR_CREACION,
                                             FE_ULT_MOD,
                                             USR_ULT_MOD,
                                             TIPO,
                                             DETALLE_CARACTERISTICA)

VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'SERIAL LICENCIA NG FIREWALL',
        'C',
        'Activo',
        SYSDATE,
        'jrmunoz',
        SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el valor del campo Serial Licencia al activar el servicio para producto NGFIREWALL'
        );


INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,
                                             DESCRIPCION_CARACTERISTICA,
                                             TIPO_INGRESO,
                                             ESTADO,
                                             FE_CREACION,
                                             USR_CREACION,
                                             FE_ULT_MOD,
                                             USR_ULT_MOD,
                                             TIPO,
                                             DETALLE_CARACTERISTICA)

VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'PUERTO ADMINISTRACION WEB NG FIREWALL',
        'C',
        'Activo',
        SYSDATE,
        'jrmunoz',
        SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el valor del campo Puerto administracion web al activar el servicio para producto NGFIREWALL'
        );


INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,
                                             DESCRIPCION_CARACTERISTICA,
                                             TIPO_INGRESO,
                                             ESTADO,
                                             FE_CREACION,
                                             USR_CREACION,
                                             FE_ULT_MOD,
                                             USR_ULT_MOD,
                                             TIPO,
                                             DETALLE_CARACTERISTICA)

VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ADMINISTRACION NG FIREWALL',
        'S',
        'Activo',
        SYSDATE,
        'jrmunoz',
        SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el valor del campo Administracion al activar el servicio para producto NGFIREWALL');


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA(ID_PRODUCTO_CARACTERISITICA,
                                                      PRODUCTO_ID,
                                                      CARACTERISTICA_ID,
                                                      FE_CREACION,
                                                      FE_ULT_MOD,
                                                      USR_CREACION,
                                                      USR_ULT_MOD,
                                                      ESTADO,
                                                      VISIBLE_COMERCIAL)

VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
         FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'IP/FQDN NG FIREWALL'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA(ID_PRODUCTO_CARACTERISITICA,
                                                      PRODUCTO_ID,
                                                      CARACTERISTICA_ID,
                                                      FE_CREACION,
                                                      FE_ULT_MOD,
                                                      USR_CREACION,
                                                      USR_ULT_MOD,
                                                      ESTADO,
                                                      VISIBLE_COMERCIAL)

VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
         FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'SERIAL LICENCIA NG FIREWALL'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA(ID_PRODUCTO_CARACTERISITICA,
                                                      PRODUCTO_ID,
                                                      CARACTERISTICA_ID,
                                                      FE_CREACION,
                                                      FE_ULT_MOD,
                                                      USR_CREACION,
                                                      USR_ULT_MOD,
                                                      ESTADO,
                                                      VISIBLE_COMERCIAL)

VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
         FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'PUERTO ADMINISTRACION WEB NG FIREWALL'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA(ID_PRODUCTO_CARACTERISITICA,
                                                      PRODUCTO_ID,
                                                      CARACTERISTICA_ID,
                                                      FE_CREACION,
                                                      FE_ULT_MOD,
                                                      USR_CREACION,
                                                      USR_ULT_MOD,
                                                      ESTADO,
                                                      VISIBLE_COMERCIAL)

VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
         FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'ADMINISTRACION NG FIREWALL'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(ID_PARAMETRO,
                                          NOMBRE_PARAMETRO,
                                          DESCRIPCION,
                                          MODULO,
                                          ESTADO,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'LISTA_ADMINISTRACION_PRODUCTO_NGFIREWALL',
        'LISTA  DE OPCIONES QUE SE MUESTRAN EN EL CAMPO ADMINISTRACION AL ACTIVAR EL PRODUCTO NG FIREWALL',
        'COMERCIAL',
        'Activo',
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          ESTADO,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'LISTA_ADMINISTRACION_PRODUCTO_NGFIREWALL'),
        'LISTA DE OPCIONES EN FORMATO JSON',
        '{"items":[{"desc": "Compartida-Limitada", "value": "CL"}]}',
        'Activo',
        'jrmunoz',
        SYSDATE,
        '127.0.0.1'
        );


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          ESTADO,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION,
                                          EMPRESA_COD)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO'
           AND ESTADO = 'Activo'),
        'LISTA VALORES',
        (SELECT ID_PRODUCTO
         FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL'
           AND EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'SEC FW NUBES PUBLICAS'
           AND ESTADO = 'Activo'),
        'Activo',
        'jrmunoz',
        SYSDATE,
        '127.0.0.1',
        10);

COMMIT;
/
