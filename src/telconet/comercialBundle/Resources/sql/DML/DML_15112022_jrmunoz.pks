/*
 * TN: INT: Telcos: Nuevo: SDWAN FASE II
 * @author Joel Muñoz <jrmunoz@telconet.ec>
 * @version 1.0
 * @since 15-11-2022
 */


-- REGISTRO DE CARACTERÍSTICA <<CANTIDAD USUARIOS SDWAN>>

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
        'CANTIDAD USUARIOS SDWAN',
        'N',
        'Activo',
         SYSDATE,
        'jrmunoz',
         SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el valor en formato numérico de la cantidad de usuarios para migracion SDWAN'
        );



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
         WHERE (NOMBRE_TECNICO) = 'INTERNET SDWAN'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'CANTIDAD USUARIOS SDWAN'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'SI');

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
         WHERE (NOMBRE_TECNICO) = 'L3MPLS SDWAN'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'CANTIDAD USUARIOS SDWAN'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'SI');


-- REGISTRO DE CARACTERÍSTICA <<SERVICIO_MIGRADO_SDWAN>>

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
        'SERVICIO_MIGRADO_SDWAN',
        'N',
        'Activo',
         SYSDATE,
        'jrmunoz',
         SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el ID del producto principal que fue migrado a SDWAN'
        );



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
         WHERE (NOMBRE_TECNICO) = 'INTERNET SDWAN'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_MIGRADO_SDWAN'
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
         WHERE (NOMBRE_TECNICO) = 'L3MPLS SDWAN'
           and EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_MIGRADO_SDWAN'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');


-- REGISTRO DE PARÁMETROS(CAB)
-- EQUIPOS MIGRACION SDWAN

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(ID_PARAMETRO,
                                          NOMBRE_PARAMETRO,
                                          DESCRIPCION,
                                          MODULO,
                                          ESTADO,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN',
        'LISTA DE EQUIPOS CON ANCHO DE BANDA Y CANTIDAD USUARIOS SOPORTADOS PARA MIGRACION SDWAN',
        'COMERCIAL',
        'Activo',
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


-- REGISTRO DE PARÁMETROS(DET)
-- EQUIPOS MIGRACION SDWAN FORTIGATE


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-40F',
        '102400',
        '1',
        '30',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');



INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-60F',
        '153600',
        '31',
        '60',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-80F',
        '204800',
        '61',
        '80',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-100F',
        '512000',
        '81',
        '100',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-200F',
        '1048576',
        '101',
        '200',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-400E',
        '1048576',
        '201',
        '500',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'FG-600E',
        '1048576',
        '501',
        '1000',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-64',
        '204800',
        '1',
        '50',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-67',
        '307200',
        '1',
        '50',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-68',
        '307200',
        '1',
        '50',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-75',
        '768000',
        '51',
        '200',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-85',
        '768000',
        '201',
        '250',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-95',
        '1048576',
        '251',
        '500',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                        PARAMETRO_ID,
                                        DESCRIPCION,
                                        VALOR1,
                                        VALOR2,
                                        VALOR3,
                                        VALOR4,
                                        ESTADO,
                                        EMPRESA_COD,
                                        USR_CREACION,
                                        FE_CREACION,
                                        IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN'),
        'REGISTRO CON MODELO|ANCHO DE BANDA(EN KILOBYTES)| CANTIDAD DE USUARIOS(MIN)|CANTIDAD DE USUARIOS(MAX)',
        'MX-105',
        '1572864',
        '501',
        '750',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


-- REGISTRO DE PARÁMETROS TAREA CAMBIO EQUIPO(CAB)
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(ID_PARAMETRO,
                                          NOMBRE_PARAMETRO,
                                          DESCRIPCION,
                                          MODULO,
                                          ESTADO,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'TAREA_CAMBIO_EQUIPO_MIGRACION_SDWAN',
        'Almacena datos para generar tarea de cambio de equipo durante proceso de migracion SDWAN',
        'COMERCIAL',
        'Activo',
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');


-- REGISTRO DE PARÁMETROS TAREA CAMBIO EQUIPO(DET)
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
                                          PARAMETRO_ID,
                                          DESCRIPCION,
                                          VALOR1,
                                          VALOR2,
                                          VALOR3,
                                          VALOR4,
                                          VALOR5,
                                          VALOR6,
                                          VALOR7,
                                          ESTADO,
                                          EMPRESA_COD,
                                          USR_CREACION,
                                          FE_CREACION,
                                          IP_CREACION)

VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'TAREA_CAMBIO_EQUIPO_MIGRACION_SDWAN'),
        'Detalle migración SDWAN almacena: DEPARTAMENTO(V1)|NOMBRE DE TAREA(V2)|OBSERVACION TAREA(V3)
        |JEFE PYL ID_PERSONA_ROL(V4)|ID_DEPARTAMENTO(V5)|ESTADO REQUERIDO SERVICIO PRINCIPAL(V6)|DETALLE OBSERVACION TAREA(V7)',
        'PLANIFICACION Y LOGISTICA',
        'CAMBIO EQUIPO',
        'Se requiere cambio de equipo para continuar con proceso de migración SDWAN<br>para servicio: %loginAux%.
        <br>',
        '568132',
        '149',
        'Activo,In-Corte',
        'Modelo de CPE %modeloEquipo% no es soportado para servicios SDWAN con
        <br>
        Cantidad de usuarios: %usuariosMigracionSDWAN%
        <br>
        Capacidad: %capacidad% Kbps',
        'Activo',
        10,
        'jrmunoz',
        SYSDATE,
        '127.0.0.1');

-- REGISTRO DE CARACTERISTICA
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
        'DETALLE_TAREA_INTERNA_SDWAN',
        'C',
        'Activo',
         SYSDATE,
        'jrmunoz',
         SYSDATE,
        'jrmunoz',
        'COMERCIAL',
        'Guarda el detalle de las tareas internas que se generen por cambio de equipo'
        );

-- REGISTRO DE CARACTERÍSTICA POR PRODUCTO
-- DETALLE_TAREA_INTERNA_SDWAN
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
         WHERE (NOMBRE_TECNICO) = 'INTERNET'
           AND UPPER(DESCRIPCION_PRODUCTO) = 'INTERNET DEDICADO'
           AND EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'DETALLE_TAREA_INTERNA_SDWAN'
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
         WHERE (NOMBRE_TECNICO) = 'INTMPLS'
           AND EMPRESA_COD = '10'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'DETALLE_TAREA_INTERNA_SDWAN'
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
         WHERE UPPER(NOMBRE_TECNICO) = 'L3MPLS'
           AND UPPER(DESCRIPCION_PRODUCTO) = 'L3MPLS'
           AND EMPRESA_COD = '10'
           AND ESTADO = 'Activo'),
        (SELECT ID_CARACTERISTICA
         FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'DETALLE_TAREA_INTERNA_SDWAN'
           AND ESTADO = 'Activo'
           AND ROWNUM = 1),
        SYSDATE,
        SYSDATE,
        'jrmunoz',
        'jrmunoz',
        'Activo',
        'NO');

COMMIT;
/
