--CREACION DE LA CATEGORIA 
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'CATEGORIA_TAREA',
    'PARAMETRO USADO PARA LA ESTRUCTURA DE CATEGORIAS DE LAS TAREAS',
    'MOVIL',
    NULL,
    'Activo',
    'wvera',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);
--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA BACKBONE - ATENUACIÓN
BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2650',
                    '3219',
                    '2630'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'BACKBONE',
            'ATENUACIÓN',
            subpadre.valor_3,
            'backbone.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA BACKBONE - CORTE FIBRA
BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '3219',
                    '2594'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'BACKBONE',
            'CORTE FIBRA',
            subpadre.valor_3,
            'backbone.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------


-- FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL PARA 
-- CLIENTE - CAJA MULTIMEDIA/ROSETA
BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2614',
                    '2615',
                    '2616',
                    '2613'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'CLIENTE',
            'CAJA MULTIMEDIA/ROSETA',
            subpadre.valor_3,
            'cliente.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL
--PARA CLIENTE - CONFIGURACIÓN EQUIPOS

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2626',
                    '2672',
                    '3235',
                    '2649'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'CLIENTE',
            'CONFIGURACIÓN EQUIPOS',
            subpadre.valor_3,
            'cliente.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA CLIENTE - EQUIPOS

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2618', 
                    '2619', 
                    '2627', 
                    '2621', 
                    '2628', 
                    '2671', 
                    '3262', 
                    '2646', 
                    '2647'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'CLIENTE',
            'EQUIPOS',
            subpadre.valor_3,
            'cliente.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA CLIENTE - PATCHCORD

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2612',
                    '2617'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'CLIENTE',
            'PATCHCORD',
            subpadre.valor_3,
            'cliente.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL
--PARA NODO - EQUIPOS

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2076',
                    '2294',
                    '2601',
                    '2602',
                    '2623'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'NODO',
            'EQUIPOS',
            subpadre.valor_3,
            'nodo.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA NODO - PATCHCORD
BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2651',
                    '2652'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'NODO',
            'PATCHCORD',
            subpadre.valor_3,
            'nodo.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA NODO - ODF

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2598',
                    '2632',
                    '2600',
                    '2655'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'NODO',
            'ODF',
            subpadre.valor_3,
            'nodo.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/
COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA RED DE DISTRIBUCIÓN - ATENUACION

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2604'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'RED DE DISTRIBUCIÓN',
            'ATENUACION',
            subpadre.valor_3,
            'redDistribucion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/

--------------------------------------------------------------------------------
--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA RED DE DISTRIBUCIÓN - CAJA BMX/FTTH

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2607',
                    '2608',
                    '4968',
                    '4991',
                    '4969',
                    '2609'
                )
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'RED DE DISTRIBUCIÓN',
            'CAJA BMX/FTTH',
            subpadre.valor_3,
            NULL,
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA RED DE DISTRIBUCIÓN - CORTE FIBRA 

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2603',
                    '2605',
                    '2660'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'RED DE DISTRIBUCIÓN',
            'CORTE FIBRA',
            subpadre.valor_3,
            'redDistribucion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA RETIRO - EQUIPOS

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2898',
                    '2897',
                    '3251'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'RETIRO',
            'EQUIPOS',
            subpadre.valor_3,
            'retiro.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA RETIRO - FIBRA

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '3247'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'RETIRO',
            'FIBRA',
            subpadre.valor_3,
            'retiro.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA ULTIMA MILLA - CORTE FIBRA

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2611'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'ULTIMA MILLA',
            'CORTE FIBRA',
            subpadre.valor_3,
            'ultimaMilla.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA ULTIMA MILLA - MINIMANGA

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2610'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'ULTIMA MILLA',
            'MINIMANGA',
            subpadre.valor_3,
            'ultimaMilla.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA INSTALACION - INSPECCIÓN 

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '2673',
                    '3893',
                    '3236',
                    '3895',
                    '3916'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'INSTALACIÓN',
            'INSPECCIÓN',
            subpadre.valor_3,
            'instalacion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA INSTALACION - INSTALACION

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '4410',
                    '3233'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'INSTALACIÓN',
            'INSTALACIÓN',
            subpadre.valor_3,
            'instalacion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/

--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA INSTALACION - MIGRACIÓN CLIENTE 

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '3513'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'INSTALACIÓN',
            'MIGRACIÓN CLIENTE',
            subpadre.valor_3,
            'instalacion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA MANTENIMIENTO - ENLACES URBANOS

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '5794',
                    '3224',
                    '3226',
                    '3228',
                    '3225',
                    '3227',
                    '4040',
                    '3923'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'MANTENIMIENTO',
            'ENLACES URBANOS',
            subpadre.valor_3,
            'mantenimiento.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA MANTENIMIENTO - CAJA BMX/FTTH

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '3220',
                    '4678'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'MANTENIMIENTO',
            'CAJA BMX/FTTH',
            subpadre.valor_3,
            'mantenimiento.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA MANTENIMIENTO - TELEFÓNICA

BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '3218'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'MANTENIMIENTO',
            'TELEFÓNICA',
            subpadre.valor_3,
            'mantenimiento.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/

--------------------------------------------------------------------------------

--FOR PARA CLIENTE PRIMER, SEGUNDO NIVEL y TERCER NIVEL 
--PARA FISCALIZACIÓN - TRABAJOS MAL REALIZADOS
BEGIN
    FOR subpadre IN 
    (
        SELECT
            column_value AS valor_3
        FROM
            TABLE 
            ( 
                sys.odcivarchar2list
                (
                    '4739',
                    '4740',
                    '4741',
                    '4746',
                    '4742',
                    '4999',
                    '5002',
                    '5001',
                    '5005',
                    '5006',
                    '5010',
                    '5058',
                    '4998',
                    '5000',
                    '5007'
                ) 
            )
    ) 
    LOOP
        INSERT INTO db_general.admi_parametro_det VALUES 
        (
            db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'CATEGORIA_TAREA'
            ),
            'CATEGORIAS DE LAS TAREAS',
            'FISCALIZACIÓN',
            'TRABAJOS MAL REALIZADOS',
            subpadre.valor_3,
            'fiscalizacion.png',
            'Activo',
            'wvera',
            SYSDATE,
            '192.168.1.1', 
            NULL,
            NULL,
            NULL,
            '128',
            NULL,
            NULL,
            NULL,
            NULL
        );

        dbms_output.put_line('INSERT EXITOSO REQUIERE_MATERIAL '
        || subpadre.valor_3);
    END LOOP;
END;
/

COMMIT;
/
